<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GutenbergBlock {
	private $settings_engine;

	public function __construct( $settings_engine ) {
		$this->settings_engine = $settings_engine;
	}

	public function init() {
		add_action( 'init', [ $this, 'register_analytics_gutenberg_block' ] );
	}

	public function register_analytics_gutenberg_block() {
		wp_register_script(
			'pva-block-script',
			PVA_PLUGIN_URL . 'assets/js/block.js',
			[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor' ],
			PVA_VERSION
		);

		register_block_type( 'post-views-analytics/display-block', [
			'editor_script'   => 'pva-block-script',
			'render_callback' => [ $this, 'render_dynamic_block_output' ],
		] );
	}

	public function render_dynamic_block_output( $attributes ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		$shortcode_engine = new Shortcode( $this->settings_engine );
		return $shortcode_engine->generate_html_display( $post_id );
	}
}