<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {
	private $settings_engine;

	public function __construct( $settings_engine ) {
		$this->settings_engine = $settings_engine;
	}

	public function init() {
		add_action( 'admin_menu', [ $this, 'create_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_init', [ $this, 'process_csv_export' ] );
		add_action( 'admin_init', [ $this, 'process_data_reset' ] );
	}

	public function create_admin_menu() {
		add_menu_page(
			__( 'Post Views Analytics', 'post-views-analytics' ),
			__( 'Post Views Analytics', 'post-views-analytics' ),
			'manage_options',
			'post-views-analytics',
			[ $this, 'render_dashboard' ],
			'dashicons-chart-bar',
			30
		);

		add_submenu_page(
			'post-views-analytics',
			__( 'Dashboard', 'post-views-analytics' ),
			__( 'Dashboard', 'post-views-analytics' ),
			'manage_options',
			'post-views-analytics',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'post-views-analytics',
			__( 'Settings', 'post-views-analytics' ),
			__( 'Settings', 'post-views-analytics' ),
			'manage_options',
			'pva-settings',
			[ $this, 'render_settings' ]
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'post-views-analytics' ) === false && strpos( $hook, 'pva-settings' ) === false ) {
			return;
		}
		
		wp_enqueue_style( 'pva-admin-styles', PVA_PLUGIN_URL . 'assets/css/admin.css', [], PVA_VERSION );
		
		// 1. Enqueue the local bundled ChartJS library asset first
		wp_enqueue_script( 'pva-chartjs', PVA_PLUGIN_URL . 'assets/js/chart.min.js', [], '3.9.1', true );
		
		// 2. Enqueue admin.js with an explicit dependency on pva-chartjs and jquery
		wp_enqueue_script( 'pva-admin-js', PVA_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery', 'pva-chartjs' ], PVA_VERSION, true );

		$chart_data = Dashboard::get_aggregated_chart_metrics();
		wp_localize_script( 'pva-admin-js', 'pvaChartPayload', $chart_data );
	}

	public function render_dashboard() {
		Dashboard::render();
	}

	public function render_settings() {
		$options = get_option( 'pva_settings', $this->settings_engine->get_defaults() );
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		?>
		<div class="wrap pva-wrap">
			<div class="pva-header">
				<h1><?php esc_html_e( 'Analytics Settings', 'post-views-analytics' ); ?></h1>
				<p class="description"><?php esc_html_e( 'Configure rules, visual display elements, parameters, and structural data points.', 'post-views-analytics' ); ?></p>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php" class="pva-settings-form">
				<?php settings_fields( 'pva_settings_group' ); ?>
				
				<div class="pva-card">
					<h2><?php esc_html_e( 'General Settings', 'post-views-analytics' ); ?></h2>
					<div class="pva-form-group">
						<label class="pva-switch">
							<input type="checkbox" name="pva_settings[enable_tracking]" value="1" <?php checked( 1, $options['enable_tracking'] ); ?>>
							<span class="pva-slider"></span>
						</label>
						<span class="pva-label-text"><?php esc_html_e( 'Enable Tracking', 'post-views-analytics' ); ?></span>
					</div>

					<div class="pva-form-group">
						<label class="pva-switch">
							<input type="checkbox" name="pva_settings[enable_frontend]" value="1" <?php checked( 1, $options['enable_frontend'] ); ?>>
							<span class="pva-slider"></span>
						</label>
						<span class="pva-label-text"><?php esc_html_e( 'Enable Global Frontend Display', 'post-views-analytics' ); ?></span>
					</div>

					<div class="pva-form-group">
						<label class="pva-main-label"><?php esc_html_e( 'Track Content Post Types', 'post-views-analytics' ); ?></label>
						<div class="pva-checkbox-list">
							<?php foreach ( $post_types as $type ) : ?>
								<label>
									<input type="checkbox" name="pva_settings[post_types][]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( in_array( $type->name, $options['post_types'], true ) ); ?>>
									<?php echo esc_html( $type->label ); ?>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="pva-card">
					<h2><?php esc_html_e( 'Counting Rules', 'post-views-analytics' ); ?></h2>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[count_logged_in]" value="1" <?php checked( 1, $options['count_logged_in'] ); ?>> <?php esc_html_e( 'Count Logged-in Users', 'post-views-analytics' ); ?></label>
					</div>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[count_guests]" value="1" <?php checked( 1, $options['count_guests'] ); ?>> <?php esc_html_e( 'Count Guest Users', 'post-views-analytics' ); ?></label>
					</div>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[ignore_admin]" value="1" <?php checked( 1, $options['ignore_admin'] ); ?>> <?php esc_html_e( 'Ignore Administrators', 'post-views-analytics' ); ?></label>
					</div>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[ignore_editor]" value="1" <?php checked( 1, $options['ignore_editor'] ); ?>> <?php esc_html_e( 'Ignore Editors', 'post-views-analytics' ); ?></label>
					</div>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[prevent_refresh]" value="1" <?php checked( 1, $options['prevent_refresh'] ); ?>> <?php esc_html_e( 'Prevent Rapid Refresh Counting (5 min cookie limit)', 'post-views-analytics' ); ?></label>
					</div>
				</div>

				<div class="pva-card">
					<h2><?php esc_html_e( 'Display Settings', 'post-views-analytics' ); ?></h2>
					<div class="pva-form-group">
						<label class="pva-main-label"><?php esc_html_e( 'Display Format String', 'post-views-analytics' ); ?></label>
						<input type="text" class="regular-text" name="pva_settings[display_format]" value="<?php echo esc_attr( $options['display_format'] ); ?>">
						<p class="description"><?php esc_html_e( 'Dynamic tags available: {icon}, {count}, {text}', 'post-views-analytics' ); ?></p>
					</div>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[icon_on]" value="1" <?php checked( 1, $options['icon_on'] ); ?>> <?php esc_html_e( 'Icon On', 'post-views-analytics' ); ?></label>
					</div>
					<div class="pva-form-group">
						<label class="pva-main-label"><?php esc_html_e( 'Text Label', 'post-views-analytics' ); ?></label>
						<input type="text" class="regular-text" name="pva_settings[text_label]" value="<?php echo esc_attr( $options['text_label'] ); ?>">
					</div>
					<div class="pva-form-group">
						<label><input type="checkbox" name="pva_settings[number_format]" value="1" <?php checked( 1, $options['number_format'] ); ?>> <?php esc_html_e( 'Apply Pretty Comma Formatting (e.g., 1,245)', 'post-views-analytics' ); ?></label>
					</div>
				</div>

				<div class="pva-card">
					<h2><?php esc_html_e( 'Analytics & Data Management', 'post-views-analytics' ); ?></h2>
					<div class="pva-form-group">
						<label class="pva-main-label"><?php esc_html_e( 'Data Retention Threshold (Days)', 'post-views-analytics' ); ?></label>
						<select name="pva_settings[retention_period]">
							<option value="30" <?php selected( $options['retention_period'], '30' ); ?>><?php esc_html_e( 'Last 30 Days', 'post-views-analytics' ); ?></option>
							<option value="90" <?php selected( $options['retention_period'], '90' ); ?>><?php esc_html_e( 'Last 90 Days', 'post-views-analytics' ); ?></option>
							<option value="unlimited" <?php selected( $options['retention_period'], 'unlimited' ); ?>><?php esc_html_e( 'Keep All Logs', 'post-views-analytics' ); ?></option>
						</select>
					</div>
				</div>

				<div class="pva-actions">
					<?php submit_button( __( 'Save Changes', 'post-views-analytics' ), 'primary', 'submit', false ); ?>
				</div>
			</form>

			<div class="pva-card pva-danger-zone">
				<h2><?php esc_html_e( 'System Export & Reset Options', 'post-views-analytics' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Perform bulk system actions. These processes run instantaneously.', 'post-views-analytics' ); ?></p>
				
				<div class="pva-action-row">
					<form method="post" action="">
						<?php wp_nonce_field( 'pva_export_nonce_action', 'pva_export_nonce' ); ?>
						<select name="pva_export_target">
							<option value="all"><?php esc_html_e( 'All Content Records', 'post-views-analytics' ); ?></option>
							<option value="post"><?php esc_html_e( 'Posts Only', 'post-views-analytics' ); ?></option>
							<option value="page"><?php esc_html_e( 'Pages Only', 'post-views-analytics' ); ?></option>
						</select>
						<input type="submit" name="pva_execute_export" class="button button-secondary" value="<?php esc_attr_e( 'Export Metrics to CSV', 'post-views-analytics' ); ?>">
					</form>

					<form method="post" action="" onsubmit="return confirm('Are you completely sure you want to delete all views database records? This cannot be reversed.');">
						<?php wp_nonce_field( 'pva_reset_nonce_action', 'pva_reset_nonce' ); ?>
						<input type="submit" name="pva_execute_reset" class="button button-link-delete" value="<?php esc_attr_e( 'Reset Analytics Data', 'post-views-analytics' ); ?>">
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	public function process_csv_export() {
		if ( ! isset( $_POST['pva_execute_export'] ) || ! isset( $_POST['pva_export_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_POST['pva_export_nonce'] ), 'pva_export_nonce_action' ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Security validation failure.', 'post-views-analytics' ) );
		}

		$target_type = isset( $_POST['pva_export_target'] ) ? sanitize_text_field( wp_unslash( $_POST['pva_export_target'] ) ) : 'all';

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=post-views-analytics-' . current_time( 'Y-m-d' ) . '.csv' );

		$output = fopen( 'php://output', 'w' );
		if ( ! $output ) {
			wp_die( esc_html__( 'File output stream acquisition failed.', 'post-views-analytics' ) );
		}

		fputcsv( $output, [ 'ID', 'Title', 'Content Type', 'Total Views', 'Last Viewed Timestamp' ] );

		$args = [
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => 'pva_total_views',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		];

		if ( 'all' === $target_type ) {
			$settings = get_option( 'pva_settings', [] );
			$args['post_type'] = ! empty( $settings['post_types'] ) ? $settings['post_types'] : [ 'post', 'page' ];
		} else {
			$args['post_type'] = $target_type;
		}

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$pid   = get_the_ID();
				$views = get_post_meta( $pid, 'pva_total_views', true );
				$last  = get_post_meta( $pid, 'pva_last_viewed', true );

				fputcsv( $output, [
					$pid,
					get_the_title(),
					get_post_type( $pid ),
					$views ? $views : 0,
					$last ? $last : 'Never',
				] );
			}
			wp_reset_postdata();
		}
		fclose( $output );
		exit;
	}

	public function process_data_reset() {
		if ( ! isset( $_POST['pva_execute_reset'] ) || ! isset( $_POST['pva_reset_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_POST['pva_reset_nonce'] ), 'pva_reset_nonce_action' ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Security validation failure.', 'post-views-analytics' ) );
		}

		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'pva_views_%'" );
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_total_views'" );
		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'pva_last_viewed'" );

		wp_cache_flush();

		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'All global metrics profiles reset successfully.', 'post-views-analytics' ) . '</p></div>';
		});
	}
}