<?php

/**
 * Plugin Name:       Events Plugin
 * Plugin URI:        https://github.com/jrmpich/eventsPlugin
 * Version:           1.0.0
 * Author:            Jérémy Pich
 * Author URI:        https://github.com/jrmpich
 * Text Domain:       events_plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EVENTS_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-events_plugin-activator.php
 */
function activate_events_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-events_plugin-activator.php';
	Events_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-events_plugin-deactivator.php
 */
function deactivate_events_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-events_plugin-deactivator.php';
	Events_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_events_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_events_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-events_plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_events_plugin() {

	$plugin = new Events_Plugin();
	$plugin->run();

}
run_events_plugin();
