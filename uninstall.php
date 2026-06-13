<?php
/**
 * Post Views Analytics - Activation Wipe Handling Interface
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Clean up all plugin settings options data profiles
delete_option( 'pva_settings' );

// Clean up custom post metadata metrics logs securely
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'pva_views_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_total_views'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_last_viewed'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

wp_cache_flush();