<?php
/**
 * Plugin Name:       Post Views Analytics
 * Plugin URI:        https://sitecroftdev.vercel.app/plugins/post-views-analytics
 * Description:       Track post views, analyze content performance, and display detailed view statistics with a modern analytics dashboard.
 * Version:           1.0.0
 * Author:            Saad
 * Author URI:        https://sitecroftdev.vercel.app/
 * Requires at least: 6.0
 * Tested up to:      7.0
 * Requires PHP:      8.0
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-views-analytics
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PVA_VERSION', '1.0.0' );
define( 'PVA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PVA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PVA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

function post_views_analytics_require_module( $path ) {
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-plugin.php' );
post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-tracker.php' );
post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-settings.php' );
post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-admin.php' );
post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-dashboard.php' );
post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-shortcode.php' );
post_views_analytics_require_module( PVA_PLUGIN_DIR . 'includes/class-gutenberg-block.php' );

function post_views_analytics_run_plugin() {
	$plugin = new \PostViewsAnalytics\Plugin();
	$plugin->run();
}
add_action( 'plugins_loaded', 'post_views_analytics_run_plugin' );