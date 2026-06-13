<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcode {
	private $settings_engine;

	public function __construct( $settings_engine ) {
		$this->settings_engine = $settings_engine;
	}

	public function init() {
		add_shortcode( 'post_views', [ $this, 'render_shortcode_output' ] );
		add_filter( 'the_content', [ $this, 'append_to_content_automatically' ] );
	}

	public function render_shortcode_output( $atts ) {
		$args = shortcode_atts( [
			'post_id' => '',
		], $atts );

		$post_id = ! empty( $args['post_id'] ) ? intval( $args['post_id'] ) : get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		return $this->generate_html_display( $post_id );
	}

	public function append_to_content_automatically( $content ) {
		if ( ! is_singular() || ! is_main_query() ) {
			return $content;
		}

		$options = get_option( 'pva_settings', $this->settings_engine->get_defaults() );
		if ( empty( $options['enable_frontend'] ) ) {
			return $content;
		}

		$html_metrics = $this->generate_html_display( get_the_ID() );
		return $content . $html_metrics;
	}

	public function generate_html_display( $post_id ) {
		$options = get_option( 'pva_settings', $this->settings_engine->get_defaults() );
		$raw_count = (int) get_post_meta( $post_id, 'pva_total_views', true );
		
		$count_rendered = $options['number_format'] ? number_format( $raw_count ) : $raw_count;
		$icon_html = $options['icon_on'] ? '<span class="pva-front-icon">👁</span>' : '';
		$text_label = esc_html( $options['text_label'] );

		$format = $options['display_format'];
		$output = str_replace( [ '{icon}', '{count}', '{text}' ], [ $icon_html, $count_rendered, $text_label ], $format );

		ob_start();
		include PVA_PLUGIN_DIR . 'templates/views-display.php';
		return ob_get_clean();
	}
}