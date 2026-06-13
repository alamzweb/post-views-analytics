<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {
	public function init() {
		add_action( 'admin_init', [ $this, 'register_plugin_settings' ] );
	}

	public function register_plugin_settings() {
		register_setting( 'pva_settings_group', 'pva_settings', [
			'type'              => 'array',
			'sanitize_callback' => [ $this, 'sanitize_settings_payload' ],
			'default'           => $this->get_defaults(),
		] );
	}

	public function get_defaults() {
		return [
			'enable_tracking'  => 1,
			'enable_frontend'  => 0,
			'post_types'       => [ 'post', 'page' ],
			'count_logged_in'  => 1,
			'count_guests'     => 1,
			'ignore_admin'     => 0,
			'ignore_editor'    => 0,
			'prevent_refresh'  => 1,
			'display_format'   => '{icon} {count} Views',
			'icon_on'          => 1,
			'text_label'       => 'Views',
			'number_format'    => 1,
			'retention_period' => '30',
		];
	}

	public function sanitize_settings_payload( $input ) {
		$output = $this->get_defaults();

		$output['enable_tracking'] = isset( $input['enable_tracking'] ) ? 1 : 0;
		$output['enable_frontend'] = isset( $input['enable_frontend'] ) ? 1 : 0;
		
		if ( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) {
			$output['post_types'] = array_map( 'sanitize_text_field', $input['post_types'] );
		} else {
			$output['post_types'] = [];
		}

		$output['count_logged_in'] = isset( $input['count_logged_in'] ) ? 1 : 0;
		$output['count_guests']    = isset( $input['count_guests'] ) ? 1 : 0;
		$output['ignore_admin']    = isset( $input['ignore_admin'] ) ? 1 : 0;
		$output['ignore_editor']   = isset( $input['ignore_editor'] ) ? 1 : 0;
		$output['prevent_refresh'] = isset( $input['prevent_refresh'] ) ? 1 : 0;
		
		$output['display_format']  = isset( $input['display_format'] ) ? sanitize_text_field( $input['display_format'] ) : $output['display_format'];
		$output['icon_on']         = isset( $input['icon_on'] ) ? 1 : 0;
		$output['text_label']      = isset( $input['text_label'] ) ? sanitize_text_field( $input['text_label'] ) : '';
		$output['number_format']   = isset( $input['number_format'] ) ? 1 : 0;
		$output['retention_period'] = isset( $input['retention_period'] ) ? sanitize_text_field( $input['retention_period'] ) : '30';

		return $output;
	}
}