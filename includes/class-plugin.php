<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	public function run() {
		// Initialize structural layers
		$tracker   = new Tracker();
		$settings  = new Settings();
		$admin     = new Admin( $settings );
		$dashboard = new Dashboard();
		$shortcode = new Shortcode( $settings );
		$block     = new GutenbergBlock( $settings );

		$tracker->init();
		$settings->init();
		$admin->init();
		$dashboard->init();
		$shortcode->init();
		$block->init();

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_style( 'pva-frontend-styles', PVA_PLUGIN_URL . 'assets/css/frontend.css', [], PVA_VERSION );
	}
}