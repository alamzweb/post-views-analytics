<?php
/**
 * Post Views Analytics Uninstaller
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete structural settings data
delete_option( 'pva_settings' );

// Clean out custom database schema metrics safely using core constructs
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'pva_views_%'" );
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_total_views'" );
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_last_viewed'" );

wp_cache_flush();