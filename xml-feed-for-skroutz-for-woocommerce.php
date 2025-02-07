<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       XML Feed for Skroutz & BestPrice for WooCommerce
 * Plugin URI:        https://github.com/Digital-Challenge/xml-feed-for-skroutz-for-woocommerce
 * Description:       This plugin helps you create an XML feed for Skroutz and BestPrice marketplaces.
 * Author:            Digital Challenge
 * Author URI:        https://www.dicha.gr
 * Version:           1.0.3
 * Text Domain:       xml-feed-for-skroutz-for-woocommerce
 * Domain Path:       /languages
 * Requires at least: 5.6
 * Tested up to:      6.7.2
 * WC requires at least: 6.2.0
 * WC tested up to:   9.6.1
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin constants.
 */
define( 'DICHA_SKROUTZ_FEED_VERSION', '1.0.3' );
define( 'DICHA_SKROUTZ_FEED_SLUG', 'xml-feed-for-skroutz-for-woocommerce' );
define( 'DICHA_SKROUTZ_FEED_FILE', __FILE__ );
define( 'DICHA_SKROUTZ_FEED_BASE_FILE', DICHA_SKROUTZ_FEED_SLUG . '/' . DICHA_SKROUTZ_FEED_SLUG . '.php' );
define( 'DICHA_SKROUTZ_FEED_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 */
function dicha_skroutz_feed_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dc-skroutz-feed-activator.php';
	Dicha_Skroutz_Feed_Activator::activate();
}

register_activation_hook( __FILE__, 'dicha_skroutz_feed_activate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dc-skroutz-feed.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function dicha_skroutz_feed_run() {

	$plugin = new Dicha_Skroutz_Feed();
	$plugin->run();
}

/**
 * If WooCommerce is inactive, this plugin is not executed.
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// load after WooCommerce
	add_action( 'woocommerce_loaded', 'dicha_skroutz_feed_run' );
}