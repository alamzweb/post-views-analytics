<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tracker {
	public function init() {
		// Asynchronous JS dispatch for maximum cache engine compatibility
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_tracking_bridge' ] );
		add_action( 'rest_api_init', [ $this, 'register_tracking_rest_route' ] );
	}

	public function enqueue_tracking_bridge() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id  = get_queried_object_id();
		$settings = get_option( 'pva_settings', $this->get_fallback_defaults() );

		if ( empty( $settings['enable_tracking'] ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );
		$allowed_types = ! empty( $settings['post_types'] ) ? $settings['post_types'] : [];
		if ( ! in_array( $post_type, $allowed_types, true ) ) {
			return;
		}

		// Inject lightweight execution payload for async fetch routing
		wp_enqueue_script( 'pva-tracker-core', PVA_PLUGIN_URL . 'assets/js/tracker-bridge.js', [], PVA_VERSION, true );
		wp_localize_script( 'pva-tracker-core', 'pvaTrackerEndpoint', [
			'root'    => esc_url_raw( get_rest_url() ),
			'post_id' => intval( $post_id ),
			'nonce'   => wp_create_nonce( 'wp_rest' )
		] );
	}

	public function register_tracking_rest_route() {
		register_rest_route( 'post-views-analytics/v1', '/track', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'process_rest_view_event' ],
			'permission_callback' => '__return_true',
		] );
	}

	public function process_rest_view_event( $request ) {
		$post_id  = intval( $request->get_param( 'post_id' ) );
		$settings = get_option( 'pva_settings', $this->get_fallback_defaults() );

		if ( ! $post_id || empty( $settings['enable_tracking'] ) ) {
			return new \WP_REST_Response( [ 'status' => 'ignored' ], 200 );
		}

		// User context evaluation
		if ( is_user_logged_in() ) {
			if ( empty( $settings['count_logged_in'] ) ) {
				return new \WP_REST_Response( [ 'status' => 'ignored_logged_in' ], 200 );
			}
			if ( ! empty( $settings['ignore_admin'] ) && current_user_can( 'manage_options' ) ) {
				return new \WP_REST_Response( [ 'status' => 'ignored_admin' ], 200 );
			}
			if ( ! empty( $settings['ignore_editor'] ) && current_user_can( 'edit_others_posts' ) && ! current_user_can( 'manage_options' ) ) {
				return new \WP_REST_Response( [ 'status' => 'ignored_editor' ], 200 );
			}
		} else {
			if ( empty( $settings['count_guests'] ) ) {
				return new \WP_REST_Response( [ 'status' => 'ignored_guest' ], 200 );
			}
		}

		// Check anti-flood prevention cookie status
		if ( ! empty( $settings['prevent_refresh'] ) ) {
			$cookie_name = 'pva_tracked_' . $post_id;
			if ( isset( $_COOKIE[ $cookie_name ] ) ) {
				return new \WP_REST_Response( [ 'status' => 'throttled' ], 200 );
			}
			// Set expiration window safely within REST context before layout data emission
			setcookie( $cookie_name, '1', time() + 300, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		}

		$this->increment_views( $post_id );

		return new \WP_REST_Response( [ 'status' => 'success' ], 200 );
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

	private function get_fallback_defaults() {
		return [
			'enable_tracking'  => 1,
			'enable_frontend'  => 0,
			'post_types'       => [ 'post', 'page' ],
			'count_logged_in'  => 1,
			'count_guests'     => 1,
			'ignore_admin'     => 0,
			'ignore_editor'    => 0,
			'prevent_refresh'  => 1,
		];
	}
}