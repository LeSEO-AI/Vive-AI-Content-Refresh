<?php
/**
 * Revive Post page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vive_revive_page() {
	$selected_id = isset( $_GET['post_id'] ) ? absint( wp_unslash( $_GET['post_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- UI selector, no state change.
	$selected    = $selected_id ? get_post( $selected_id ) : null;
	?>
	<div class="container-fluid p-4">

		<!-- Back -->
		<a href="?page=vive-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; Back to Dashboard</a>

		<h1 class="h3 mb-3">Revive a Post</h1>

		<!-- Why revive -->
		<div class="border rounded-3 p-3 mb-4 bg-light bg-gradient">
			<div class="d-flex align-items-center gap-3">
				<i class="bi bi-graph-up-arrow fs-2 text-primary"></i>
				<div>
					<div class="fw-medium">Substantive updates revive old authority</div>
					<div class="text-body-secondary small">
						Old posts with backlinks already have ranking power.
						Real improvements — new data, deeper analysis, better answers — signal to Google the content improved.
						Existing links amplify the update. No new outreach needed.
					</div>
				</div>
			</div>
		</div>

		<!-- Disclaimer -->
		<div class="d-inline-block mb-4">
			<div class="alert alert-info py-2 px-3 mb-0" role="alert">
				<i class="bi bi-info-circle me-1"></i>
				Set your writing rules in <a href="?page=vive-ai-settings" class="alert-link">Settings &amp; Persona</a> first.
				AI content is for reference. For best results, review and edit before publishing.
			</div>
		</div>

		<!-- Real-time research badge -->
		<div class="d-block mb-4">
			<div class="alert alert-success py-2 px-3 mb-0 d-inline-flex align-items-center gap-2" role="alert" style="max-width: fit-content;">
				<i class="bi bi-globe-americas"></i>
				<span class="fw-medium">AI finds relevant info from the web</span>
				<span class="badge bg-success-subtle text-success border border-success-subtle ms-2">Web Fetch</span>
			</div>
		</div>

		<?php if ( $selected ) : ?>

		<form id="vive-revive-form">
			<input type="hidden" name="original_content" value="<?php echo esc_attr( $selected->post_content ); ?>" />

			<!-- Selector + Options row -->
			<div class="row g-3 mb-3">
				<div class="col-md-5">
					<label for="vive-post-select" class="form-label fw-medium">Select a post to refresh</label>
					<select id="vive-post-select" class="form-select"
							onchange="window.location='?page=vive-ai-revive&post_id='+this.value">
						<option value="">— Select a post —</option>
						<?php
						$posts = get_posts( array( 'numberposts' => 20, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ) );
						foreach ( $posts as $p ) {
							$sel = $selected_id === $p->ID ? 'selected' : '';
							echo '<option value="' . esc_attr( $p->ID ) . '" ' . esc_attr( $sel ) . '>' . esc_html( $p->post_title ) . '</option>';
						}
						?>
					</select>
				</div>
				<div class="col-md-7">
					<div class="row g-2">
						<div class="col-md-4">
							<label for="vive-revive-topic" class="form-label small fw-medium">Topic focus</label>
							<input type="text" id="vive-revive-topic" name="topic" class="form-control"
								   placeholder="e.g. <?php echo esc_attr( $selected->post_title ); ?>" />
						</div>
						<div class="col-md-4">
							<label for="vive-revive-keywords" class="form-label small fw-medium">Keywords</label>
							<input type="text" id="vive-revive-keywords" name="keywords" class="form-control"
								   placeholder="target keywords" />
						</div>
						<div class="col-md-4">
							<label for="vive-revive-tone" class="form-label small fw-medium">Tone</label>
							<select id="vive-revive-tone" name="tone" class="form-select">
								<option value="professional">Professional</option>
								<option value="casual">Casual</option>
								<option value="technical">Technical</option>
								<option value="persuasive">Persuasive</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<!-- Buttons -->
			<div class="d-flex gap-2 mb-3 justify-content-end">
				<a href="?page=vive-ai-settings" class="btn btn-outline-secondary">Settings &amp; Persona</a>
				<button type="submit" class="btn btn-primary">Analyze &amp; Revive</button>
			</div>

			<!-- Two scrollable boxes -->
			<div class="row g-3">
				<div class="col-lg-6">
					<h5 class="mb-2">Current Content</h5>
					<div class="border rounded-3 p-3 bg-light" style="height:450px;overflow-y:auto;">
						<?php echo wp_kses_post( $selected->post_content ); ?>
					</div>
				</div>
				<div class="col-lg-6">
					<h5 class="mb-2">Refreshed Content</h5>
					<div class="border rounded-3 bg-light d-flex flex-column" style="height:450px;">
						<div id="vive-revive-output" class="flex-grow-1 p-3" style="overflow-y:auto;">
							<div class="text-center text-body-secondary py-5">
								<i class="bi bi-arrow-repeat fs-1 d-block mb-3"></i>
								<p class="mb-0">Click <strong>Analyze &amp; Revive</strong> to generate refreshed content.<br>
								Diff view will appear here.</p>
							</div>
						</div>
						<div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
							<button type="button" id="vive-revive-toggle-diff" class="btn btn-sm btn-outline-secondary" disabled>View Diff</button>
							<div class="d-flex gap-2">
								<button type="button" id="vive-revive-save-draft" class="btn btn-outline-secondary" disabled>Save Draft</button>
								<button type="button" id="vive-revive-publish" class="btn btn-primary" disabled>Publish</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>

		<?php else : ?>

		<!-- Post selector (no post selected) -->
		<div class="mb-4" style="max-width:400px;">
			<label for="vive-post-select" class="form-label fw-medium">Select a post to refresh</label>
			<select id="vive-post-select" class="form-select form-select-lg"
					onchange="window.location='?page=vive-ai-revive&post_id='+this.value">
				<option value="">— Select a post —</option>
				<?php
				$posts = get_posts( array( 'numberposts' => 20, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ) );
				foreach ( $posts as $p ) {
					$sel = $selected_id === $p->ID ? 'selected' : '';
					echo '<option value="' . esc_attr( $p->ID ) . '" ' . esc_attr( $sel ) . '>' . esc_html( $p->post_title ) . '</option>';
				}
				?>
			</select>
		</div>

		<!-- No post selected placeholder -->
		<div class="text-center text-body-secondary py-5 border rounded-3">
			<i class="bi bi-file-earmark-text fs-1 d-block mb-3"></i>
			<p class="mb-0">Select a post above. Its content will show here, ready to revive.</p>
		</div>

		<?php endif; ?>

	</div>
	<?php
}
