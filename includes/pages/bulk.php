<?php
/**
 * Bulk Generation page (coming soon).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leseo_bulk_page() {
	?>
	<div class="container-fluid p-4 leseo-coming-soon">

		<!-- Back -->
		<a href="?page=leseo-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; <?php esc_html_e( 'Back to Dashboard', 'leseo-ai' ); ?></a>

		<div class="leseo-coming-soon-overlay">
			<div class="text-center">
				<i class="bi bi-files fs-1 text-body-secondary d-block mb-3"></i>
				<h2 class="h4 text-body-secondary mb-2"><?php esc_html_e( 'Bulk Generation', 'leseo-ai' ); ?></h2>
				<p class="text-body-secondary mb-0"><?php esc_html_e( 'Coming soon. Paste a list of topics and generate posts in one click.', 'leseo-ai' ); ?></p>
			</div>
		</div>

	</div>
	<?php
}
