<?php
/**
 * Global template for displaying post views frontend components.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="pva-frontend-display-wrapper">
	<div class="pva-metrics-badge">
		<?php echo wp_kses_post( $output ); ?>
	</div>
</div>