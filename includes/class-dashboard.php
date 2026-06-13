<?php
namespace PostViewsAnalytics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard {
	public static function init() {}

	public static function render() {
		$metrics = self::get_dashboard_cards_metrics();
		?>
		<div class="wrap pva-wrap">
			<div class="pva-header-dashboard">
				<div class="pva-header-title">
					<h1><?php esc_html_e( 'Analytics Dashboard', 'post-views-analytics' ); ?></h1>
					<p class="description"><?php esc_html_e( 'Real-time metrics for internal organic system assets.', 'post-views-analytics' ); ?></p>
				</div>
			</div>

			<div class="pva-metric-grid">
				<div class="pva-metric-card">
					<div class="pva-card-icon text-primary"><span class="dashicons dashicons-chart-bar"></span></div>
					<div class="pva-card-data">
						<h3><?php echo esc_html( number_format( $metrics['total'] ) ); ?></h3>
						<p><?php esc_html_e( 'Total Views', 'post-views-analytics' ); ?></p>
					</div>
				</div>
				<div class="pva-metric-card">
					<div class="pva-card-icon text-success"><span class="dashicons dashicons-visibility"></span></div>
					<div class="pva-card-data">
						<h3><?php echo esc_html( number_format( $metrics['today'] ) ); ?></h3>
						<p><?php esc_html_e( 'Views Today', 'post-views-analytics' ); ?></p>
					</div>
				</div>
				<div class="pva-metric-card">
					<div class="pva-card-icon text-warning"><span class="dashicons dashicons-calendar"></span></div>
					<div class="pva-card-data">
						<h3><?php echo esc_html( number_format( $metrics['week'] ) ); ?></h3>
						<p><?php esc_html_e( 'Views This Week', 'post-views-analytics' ); ?></p>
					</div>
				</div>
				<div class="pva-metric-card">
					<div class="pva-card-icon text-info"><span class="dashicons dashicons-calendar3"></span></div>
					<div class="pva-card-data">
						<h3><?php echo esc_html( number_format( $metrics['month'] ) ); ?></h3>
						<p><?php esc_html_e( 'Views This Month', 'post-views-analytics' ); ?></p>
					</div>
				</div>
			</div>

			<div class="pva-chart-container pva-card">
				<div class="pva-chart-header">
					<h2><?php esc_html_e( 'Content View Performance Trends', 'post-views-analytics' ); ?></h2>
					<div class="pva-chart-actions">
						<button class="pva-chart-btn active" data-range="7"><?php esc_html_e( 'Last 7 Days', 'post-views-analytics' ); ?></button>
						<button class="pva-chart-btn" data-range="30"><?php esc_html_e( 'Last 30 Days', 'post-views-analytics' ); ?></button>
					</div>
				</div>
				<div class="pva-chart-wrapper">
					<canvas id="pvaTrendChart"></canvas>
				</div>
			</div>

			<div class="pva-tables-grid">
				<div class="pva-card pva-table-box">
					<h2><?php esc_html_e( 'Most Viewed Content Assets', 'post-views-analytics' ); ?></h2>
					<?php self::render_metrics_table( self::get_filtered_posts_dataset( 'total', 5 ) ); ?>
				</div>

				<div class="pva-card pva-table-box">
					<h2><?php esc_html_e( 'Recently Viewed Content', 'post-views-analytics' ); ?></h2>
					<?php self::render_metrics_table( self::get_filtered_posts_dataset( 'recent', 5 ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	private static function get_dashboard_cards_metrics() {
		global $wpdb;
		$settings = get_option( 'pva_settings', [ 'post_types' => [ 'post', 'page' ] ] );
		$allowed_types = ! empty( $settings['post_types'] ) ? $settings['post_types'] : [ 'post', 'page' ];
		
		// Securely bind dynamic in expressions using prepared markers
		$format = implode( ',', array_fill( 0, count( $allowed_types ), '%s' ) );

		$total_query = "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = 'pva_total_views' AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($format) AND post_status = 'publish')";
		$total_views = $wpdb->get_var( $wpdb->prepare( $total_query, $allowed_types ) );

		$today = current_time( 'Y-m-d' );
		$today_query = "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($format) AND post_status = 'publish')";
		$today_args = array_merge( [ 'pva_views_' . $today ], $allowed_types );
		$today_views = $wpdb->get_var( $wpdb->prepare( $today_query, $today_args ) );

		$week_views = 0;
		for ( $i = 0; $i < 7; $i++ ) {
			$day_string = date( 'Y-m-d', strtotime( "-$i days", current_time( 'timestamp' ) ) );
			$loop_query = "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($format) AND post_status = 'publish')";
			$loop_args  = array_merge( [ 'pva_views_' . $day_string ], $allowed_types );
			$week_views += (int) $wpdb->get_var( $wpdb->prepare( $loop_query, $loop_args ) );
		}

		$month_views = 0;
		for ( $i = 0; $i < 30; $i++ ) {
			$day_string = date( 'Y-m-d', strtotime( "-$i days", current_time( 'timestamp' ) ) );
			$loop_query = "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($format) AND post_status = 'publish')";
			$loop_args  = array_merge( [ 'pva_views_' . $day_string ], $allowed_types );
			$month_views += (int) $wpdb->get_var( $wpdb->prepare( $loop_query, $loop_args ) );
		}

		return [
			'total' => $total_views ? $total_views : 0,
			'today' => $today_views ? $today_views : 0,
			'week'  => $week_views,
			'month' => $month_views,
		];
	}

	public static function get_aggregated_chart_metrics() {
		global $wpdb;
		$settings = get_option( 'pva_settings', [ 'post_types' => [ 'post', 'page' ] ] );
		$allowed_types = ! empty( $settings['post_types'] ) ? $settings['post_types'] : [ 'post', 'page' ];
		$format = implode( ',', array_fill( 0, count( $allowed_types ), '%s' ) );

		$labels_7  = [];
		$dataset_7 = [];
		for ( $i = 6; $i >= 0; $i-- ) {
			$day = date( 'Y-m-d', strtotime( "-$i days", current_time( 'timestamp' ) ) );
			$labels_7[]  = date( 'M d', strtotime( $day ) );
			
			$query = "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($format) AND post_status = 'publish')";
			$args  = array_merge( [ 'pva_views_' . $day ], $allowed_types );
			$val   = $wpdb->get_var( $wpdb->prepare( $query, $args ) );
			
			$dataset_7[] = $val ? (int) $val : 0;
		}

		$labels_30  = [];
		$dataset_30 = [];
		for ( $i = 29; $i >= 0; $i-- ) {
			$day = date( 'Y-m-d', strtotime( "-$i days", current_time( 'timestamp' ) ) );
			$labels_30[]  = date( 'M d', strtotime( $day ) );
			
			$query = "SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($format) AND post_status = 'publish')";
			$args  = array_merge( [ 'pva_views_' . $day ], $allowed_types );
			$val   = $wpdb->get_var( $wpdb->prepare( $query, $args ) );
			
			$dataset_30[] = $val ? (int) $val : 0;
		}

		return [
			'range7'  => [ 'labels' => $labels_7, 'data' => $dataset_7 ],
			'range30' => [ 'labels' => $labels_30, 'data' => $dataset_30 ],
		];
	}

	private static function get_filtered_posts_dataset( $strategy, $limit = 5 ) {
		$settings = get_option( 'pva_settings', [ 'post_types' => [ 'post', 'page' ] ] );
		$allowed_types = ! empty( $settings['post_types'] ) ? $settings['post_types'] : [ 'post', 'page' ];

		$args = [
			'post_type'      => $allowed_types,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'no_found_rows'  => true,
		];

		if ( 'total' === $strategy ) {
			$args['meta_key'] = 'pva_total_views';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
		} else {
			$args['meta_key'] = 'pva_last_viewed';
			$args['orderby']  = 'meta_value';
			$args['order']    = 'DESC';
		}

		return new \WP_Query( $args );
	}

	private static function render_metrics_table( $query ) {
		if ( ! $query->have_posts() ) {
			echo '<div class="pva-empty-state"><p>' . esc_html__( 'No structural tracking views recorded yet.', 'post-views-analytics' ) . '</p></div>';
			return;
		}
		?>
		<table class="pva-data-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Title', 'post-views-analytics' ); ?></th>
					<th><?php esc_html_e( 'Post Type', 'post-views-analytics' ); ?></th>
					<th><?php esc_html_e( 'Views', 'post-views-analytics' ); ?></th>
					<th><?php esc_html_e( 'Last Viewed', 'post-views-analytics' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php while ( $query->have_posts() ) : $query->the_post(); 
					$pid   = get_the_ID();
					$views = get_post_meta( $pid, 'pva_total_views', true );
					$last  = get_post_meta( $pid, 'pva_last_viewed', true );
					?>
					<tr>
						<td class="pva-title-cell"><strong><?php the_title(); ?></strong></td>
						<td><span class="pva-badge"><?php echo esc_html( get_post_type( $pid ) ); ?></span></td>
						<td><strong><?php echo esc_html( number_format( $views ? $views : 0 ) ); ?></strong></td>
						<td class="text-muted"><?php echo esc_html( $last ? human_time_diff( strtotime( $last ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'post-views-analytics' ) : __( 'Never', 'post-views-analytics' ) ); ?></td>
					</tr>
				<?php endwhile; wp_reset_postdata(); ?>
			</tbody>
		</table>
		<?php
	}
}