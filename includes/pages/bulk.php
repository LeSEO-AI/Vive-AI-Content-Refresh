<?php
/**
 * Bulk Generation page (coming soon).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vive_bulk_page() {
	?>
	<div class="container-fluid p-4 vive-coming-soon">

		<!-- Back -->
		<a href="?page=vive-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; <?php esc_html_e( 'Back to Dashboard', 'vive-ai' ); ?></a>

		<div class="vive-coming-soon-overlay">
			<div class="text-center">
				<i class="bi bi-files fs-1 text-body-secondary d-block mb-3"></i>
				<h2 class="h4 text-body-secondary mb-2"><?php esc_html_e( 'Bulk Generation', 'vive-ai' ); ?></h2>
				<p class="text-body-secondary mb-0"><?php esc_html_e( 'Coming soon. Paste a list of topics and generate posts in one click.', 'vive-ai' ); ?></p>
			</div>
		</div>

	</div>
	<?php
}
