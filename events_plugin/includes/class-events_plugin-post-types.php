<?php

class Event_Plugin_Post_Types {

    private function register_single_post_type( $fields ) {

        $labels = array(
            'name'                  => $fields['plural'],
            'singular_name'         => $fields['singular'],
            'menu_name'             => $fields['menu_name'],
            'new_item'              => sprintf( __( 'Nouveau %s', 'events_plugin' ), strtolower( $fields['singular'] ) ),
            'add_new_item'          => sprintf( __( 'Ajouter un %s', 'events_plugin' ), strtolower( $fields['singular'] ) ),
            'edit_item'             => sprintf( __( 'Éditer l\'%s', 'events_plugin' ), strtolower( $fields['singular'] ) ),
            'view_item'             => sprintf( __( 'Voir l\'%s', 'events_plugin' ), strtolower( $fields['singular'] ) ),
            'view_items'            => sprintf( __( 'Voir les %s', 'events_plugin' ), strtolower( $fields['plural'] ) ),
            'search_items'          => sprintf( __( 'Rechercher des %s', 'events_plugin' ), strtolower( $fields['plural'] ) ),
            'not_found'             => sprintf( __( 'Pas de %s trouvé', 'events_plugin' ), strtolower( $fields['single'] ) ),
            'not_found_in_trash'    => sprintf( __( 'Aucun %s dans le corbeille', 'events_plugin' ), strtolower( $fields['single'] ) ),
            'all_items'             => sprintf( __( 'Tous les %s', 'events_plugin' ), strtolower( $fields['plural'] ) ),

            /* Custom archive label.  Must filter 'post_type_archive_title' to use. */
            'archive_title'        => $fields['plural'],
        );

        $args = array(
            'labels'             => $labels,
            'description'        => ( isset( $fields['description'] ) ) ? $fields['description'] : '',
            'public'             => ( isset( $fields['public'] ) ) ? $fields['public'] : true,
            'publicly_queryable' => ( isset( $fields['publicly_queryable'] ) ) ? $fields['publicly_queryable'] : true,
            'exclude_from_search'=> ( isset( $fields['exclude_from_search'] ) ) ? $fields['exclude_from_search'] : false,
            'show_ui'            => ( isset( $fields['show_ui'] ) ) ? $fields['show_ui'] : true,
            'show_in_menu'       => ( isset( $fields['show_in_menu'] ) ) ? $fields['show_in_menu'] : true,
            'query_var'          => ( isset( $fields['query_var'] ) ) ? $fields['query_var'] : true,
            'show_in_admin_bar'  => ( isset( $fields['show_in_admin_bar'] ) ) ? $fields['show_in_admin_bar'] : true,
            'capability_type'    => ( isset( $fields['capability_type'] ) ) ? $fields['capability_type'] : 'post',
            'has_archive'        => ( isset( $fields['has_archive'] ) ) ? $fields['has_archive'] : true,
            'hierarchical'       => ( isset( $fields['hierarchical'] ) ) ? $fields['hierarchical'] : true,
            'supports'           => ( isset( $fields['supports'] ) ) ? $fields['supports'] : array(
                    'title',
                    'thumbnail',
                    'custom-fields',
                    'revisions',
            ),
            'menu_position'      => ( isset( $fields['menu_position'] ) ) ? $fields['menu_position'] : 21,
            'menu_icon'          => ( isset( $fields['menu_icon'] ) ) ? $fields['menu_icon']: 'dashicons-admin-generic',
            'show_in_nav_menus'  => ( isset( $fields['show_in_nav_menus'] ) ) ? $fields['show_in_nav_menus'] : true,
        );

        register_post_type( $fields['slug'], $args );

        if ( isset( $fields['taxonomies'] ) && is_array( $fields['taxonomies'] ) ) {

            foreach ( $fields['taxonomies'] as $taxonomy ) {

                $this->register_single_post_type_taxnonomy( $taxonomy );

            }

        }

    }

    private function register_single_post_type_taxnonomy( $tax_fields ) {

        $labels = array(
            'name'                       => $tax_fields['plural'],
            'singular_name'              => $tax_fields['single'],
            'menu_name'                  => $tax_fields['plural'],
            'all_items'                  => sprintf( __( 'Tous les %s' , 'events_plugin' ), strtolower( $tax_fields['plural'] ) ),
            'edit_item'                  => sprintf( __( 'Éditer le %s' , 'events_plugin' ), strtolower( $tax_fields['single'] ) ),
            'view_item'                  => sprintf( __( 'Voir le %s' , 'events_plugin' ), strtolower( $tax_fields['single'] ) ),
            'update_item'                => sprintf( __( 'Mettre à jour le %s' , 'events_plugin' ), strtolower( $tax_fields['single'] ) ),
            'add_new_item'               => sprintf( __( 'Ajouter un %s' , 'events_plugin' ), strtolower( $tax_fields['single'] ) ),
            'new_item_name'              => sprintf( __( 'Nouveau %s' , 'events_plugin' ), strtolower( $tax_fields['single'] ) ),
            'search_items'               => sprintf( __( 'Rechercher des %s' , 'events_plugin' ), strtolower( $tax_fields['plural'] ) ),
            'not_found'                  => sprintf( __( 'Pas de %s trouvé' , 'events_plugin' ),strtolower(  $tax_fields['single'] ) ),
        );

        $args = array(
            'label'                 => $tax_fields['plural'],
            'labels'                => $labels,
            'hierarchical'          => ( isset( $tax_fields['hierarchical'] ) )          ? $tax_fields['hierarchical']          : true,
            'public'                => ( isset( $tax_fields['public'] ) )                ? $tax_fields['public']                : true,
            'show_ui'               => ( isset( $tax_fields['show_ui'] ) )               ? $tax_fields['show_ui']               : true,
            'show_in_nav_menus'     => ( isset( $tax_fields['show_in_nav_menus'] ) )     ? $tax_fields['show_in_nav_menus']     : true,
            'show_tagcloud'         => ( isset( $tax_fields['show_tagcloud'] ) )         ? $tax_fields['show_tagcloud']         : true,
            'meta_box_cb'           => ( isset( $tax_fields['meta_box_cb'] ) )           ? $tax_fields['meta_box_cb']           : null,
            'show_admin_column'     => ( isset( $tax_fields['show_admin_column'] ) )     ? $tax_fields['show_admin_column']     : true,
            'show_in_quick_edit'    => ( isset( $tax_fields['show_in_quick_edit'] ) )    ? $tax_fields['show_in_quick_edit']    : true,
            'update_count_callback' => ( isset( $tax_fields['update_count_callback'] ) ) ? $tax_fields['update_count_callback'] : '',
            'show_in_rest'          => ( isset( $tax_fields['show_in_rest'] ) )          ? $tax_fields['show_in_rest']          : true,
            'rest_base'             => $tax_fields['taxonomy'],
            'rest_controller_class' => ( isset( $tax_fields['rest_controller_class'] ) ) ? $tax_fields['rest_controller_class'] : 'WP_REST_Terms_Controller',
            'query_var'             => $tax_fields['taxonomy'],
            'rewrite'               => ( isset( $tax_fields['rewrite'] ) )               ? $tax_fields['rewrite']               : true,
            'sort'                  => ( isset( $tax_fields['sort'] ) )                  ? $tax_fields['sort']                  : '',
        );

        $args = apply_filters( $tax_fields['taxonomy'] . '_args', $args );

        register_taxonomy( $tax_fields['taxonomy'], $tax_fields['post_types'], $args );

    }

    public function create_custom_post_type() {

        $post_types_fields = array(
            array(
                'slug'                  => 'evenements',
                'singular'              => 'Événement',
                'plural'                => 'Événements',
                'menu_name'             => 'Événements',
                'description'           => 'Événements',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_icon'             => 'dashicons-calendar-alt',
                'menu_position'         => 21,
                'taxonomies'            => array(
                    array(
                        'taxonomy'          => 'type_evenement',
                        'plural'            => 'Types d\'événement',
                        'single'            => 'Type d\'événement',
                        'post_types'        => array( 'evenements' ),
                    ),
                ),
            ),
        );

        foreach ( $post_types_fields as $fields ) {

            $this->register_single_post_type( $fields );

        }
    }
}