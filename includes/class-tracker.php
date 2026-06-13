<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tracker {
	public function init() {
		add_action( 'wp_head', [ $this, 'process_view_tracking' ] );
	}

	public function process_view_tracking() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id  = get_the_ID();
		$settings = get_option( 'pva_settings', [] );

		// General Tracking Activation Check
		if ( empty( $settings['enable_tracking'] ) ) {
			return;
		}

		// Post Type Allowed Check
		$post_type = get_post_type( $post_id );
		$allowed_types = ! empty( $settings['post_types'] ) ? $settings['post_types'] : [];
		if ( ! in_array( $post_type, $allowed_types, true ) ) {
			return;
		}

		// User Role Exclusion Filter Management
		if ( is_user_logged_in() ) {
			if ( empty( $settings['count_logged_in'] ) ) {
				return;
			}
			$current_user = wp_get_current_user();
			if ( ! empty( $settings['ignore_admin'] ) && in_array( 'administrator', $current_user->roles, true ) ) {
				return;
			}
			if ( ! empty( $settings['ignore_editor'] ) && in_array( 'editor', $current_user->roles, true ) ) {
				return;
			}
		} else {
			if ( empty( $settings['count_guests'] ) ) {
				return;
			}
		}

		// Anti-Flood Prevention Mechanics via Cookies
		if ( ! empty( $settings['prevent_refresh'] ) ) {
			$cookie_name = 'pva_tracked_' . $post_id;
			if ( isset( $_COOKIE[ $cookie_name ] ) ) {
				return;
			}
			setcookie( $cookie_name, '1', time() + 300, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		}

		$this->increment_views( $post_id );
	}

	private function increment_views( $post_id ) {
		$total_views = (int) get_post_meta( $post_id, 'pva_total_views', true );
		$total_views++;
		update_post_meta( $post_id, 'pva_total_views', $total_views );

		$today     = current_time( 'Y-m-d' );
		$day_views = (int) get_post_meta( $post_id, 'pva_views_' . $today, true );
		$day_views++;
		update_post_meta( $post_id, 'pva_views_' . $today, $day_views );

		update_post_meta( $post_id, 'pva_last_viewed', current_time( 'mysql' ) );
	}
}