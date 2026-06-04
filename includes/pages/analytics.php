<?php
/**
 * Analytics page (coming soon).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leseo_analytics_page() {
	?>
	<div class="container-fluid p-4 leseo-coming-soon">

		<!-- Back -->
		<a href="?page=leseo-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; <?php esc_html_e( 'Back to Dashboard', 'leseo-ai' ); ?></a>

		<div class="leseo-coming-soon-overlay">
			<div class="text-center">
				<i class="bi bi-graph-up fs-1 text-body-secondary d-block mb-3"></i>
				<h2 class="h4 text-body-secondary mb-2"><?php esc_html_e( 'Analytics', 'leseo-ai' ); ?></h2>
				<p class="text-body-secondary mb-0"><?php esc_html_e( 'Coming soon. Track traffic changes, refresh history, and AI costs.', 'leseo-ai' ); ?></p>
			</div>
		</div>

	</div>
	<?php
}
