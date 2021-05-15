<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/jrmpich
 * @since      1.0.0
 *
 * @package    Events_plugin
 * @subpackage Events_plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Events_plugin
 * @subpackage Events_plugin/public
 * @author     Jérémy Pich <pich.jeremy@gmail.com>
 */
class Events_plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( is_post_type_archive( 'evenements' ) ) {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/Events_plugin-public.css', array(), $this->version, 'all' );

		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_post_type_archive( 'evenements' ) ) {

			global $wp_query;

			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/babel/events_plugin-public.ba.js', array( 'jquery' ), $this->version, false );
 
			wp_localize_script( $this->plugin_name, 'ajax_params', array(
				'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
				'posts' => json_encode( $wp_query->query_vars ),
				'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
				'max_page' => $wp_query->max_num_pages
			) );
	
			wp_enqueue_script( $this->plugin_name );

		}

	}

	public function get_custom_post_type_archive_template() {

		global $post;
		$custom_post_type = 'evenements';
		$templates_dir = 'templates';
	
		if ( is_post_type_archive( $custom_post_type ) ) {
	
			$theme_files = array('archive-' . $custom_post_type . '.php', $this->plugin_name . '/archive-' . $custom_post_type . '.php');
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme != '' ) {
				return $archive_template;
			} else {
				if ( file_exists( WP_PLUGIN_DIR . '/' . $this->plugin_name . '/public/' . $templates_dir . '/archive-' . $custom_post_type . '.php' ) ) {
					return WP_PLUGIN_DIR . '/' . $this->plugin_name . '/public/' . $templates_dir . '/archive-' . $custom_post_type . '.php';
				}
				else {
					return null;
				}
	
			}
	
		}
	
		return $archive_template;
	}

	public function events_posts( $query ) {

		if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'evenements' ) ) {
			$query->set( 'posts_per_page', '3' );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'date_start' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_query', array(
				array(
					'key' => 'date_end',
					'value' => wp_date('Y-m-d H:i:s'),
					'compare' => '>=',
					'type' => 'DATETIME'
				), 
			), );
		}

		return $query;
	}

	public function loadmore_events() {
 
		global $wp_query;

		$args = json_decode( stripslashes( $_POST['query'] ), true );
		$args['paged'] = $_POST['page'] + 1;
		$args['post_status'] = 'publish';
	 
		query_posts( $args );
	 
		if( have_posts() ) {

			while( have_posts() ) {
				the_post();
				require( plugin_dir_path( __FILE__ ) . 'templates/template-parts/content-events.php');
			}

		}

		die;
	}

	function filter_events() {
		
		global $wp_query;

		$args = array(
			'post_type' => 'evenements',
			'posts_per_page' => 3,
			'orderby' => 'meta_value', 
			'meta_key' => 'date_start',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'date_end',
					'value' => wp_date('Y-m-d H:i:s'),
					'compare' => '>=',
					'type' => 'DATETIME'
				), 
			),
		);

		$term_slug = $_POST['type_evenement'];

		if ( !empty( $term_slug ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'type_evenement',
					'field'    => 'slug',
					'terms'    => $term_slug,
				),
			);
		}
	  
		$wp_query = new WP_Query( $args );

		if($wp_query->have_posts()) {
			while($wp_query->have_posts()) {
				$wp_query->the_post();
				require( plugin_dir_path( __FILE__ ) . 'templates/template-parts/content-events.php');
			}
			if ( $wp_query->query_vars['paged'] + 1 < $wp_query->max_num_pages ) { ?>
				<button class="events__loadmore">Voir plus</button>
				<?php
			}
		}
		
		die;
	  }

}
