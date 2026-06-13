<?php
/**
 * Post Views Analytics - Activation Wipe Handling Interface
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

wp_cache_delete( 'pva_dashboard_metrics', 'post-views-analytics' );
wp_cache_delete( 'pva_chart_metrics', 'post-views-analytics' );

delete_option( 'pva_settings' );

$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'pva_views_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_total_views'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_last_viewed'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery

wp_cache_flush();