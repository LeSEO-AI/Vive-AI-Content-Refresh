<?php
/**
 * Create Post page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vive_create_page() {
	?>
	<div class="container-fluid p-4">

		<!-- Back -->
		<a href="?page=vive-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; Back to Dashboard</a>

		<!-- Why create -->
		<div class="border rounded-3 p-3 mb-4 bg-light bg-gradient">
			<div class="d-flex align-items-center gap-3">
				<i class="bi bi-pencil-square fs-2 text-primary"></i>
				<div>
					<div class="fw-medium">People-first content drives rankings</div>
					<div class="text-body-secondary small">
						Google rewards thorough, helpful content. AI writes in your voice, matches your persona,
						and targets your keywords. Publish more without burning out.
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

		<form id="vive-create-form">

			<!-- Topic hero -->
			<div class="mb-4">
				<label for="vive-topic" class="form-label h5">What do you want to write about?</label>
				<textarea id="vive-topic" name="topic" class="form-control form-control-lg" rows="2"
						  placeholder="e.g. How to optimize WordPress for SEO in 2025" required></textarea>
			</div>

			<!-- Options row -->
			<div class="row g-3 align-items-end mb-4">
				<div class="col-md-4">
					<label for="vive-keywords" class="form-label small fw-medium">Target Keywords</label>
					<input type="text" id="vive-keywords" name="keywords" class="form-control"
						   placeholder="wordpress seo, site speed, core web vitals" />
				</div>
				<div class="col-md-3">
					<label for="vive-tone" class="form-label small fw-medium">Tone</label>
					<select id="vive-tone" name="tone" class="form-select">
						<option value="professional">Professional</option>
						<option value="casual">Casual</option>
						<option value="technical">Technical</option>
						<option value="persuasive">Persuasive</option>
					</select>
				</div>
				<div class="col-md-5 d-flex gap-2">
					<a href="?page=vive-ai-settings" class="btn btn-outline-secondary btn-lg">
						Settings &amp; Persona
					</a>
					<button type="submit" class="btn btn-primary btn-lg flex-grow-1">
						Generate with AI
					</button>
				</div>
			</div>

		</form>

		<!-- Output -->
		<div id="vive-create-output" class="border rounded-3 p-4 bg-light">
			<div class="text-center text-body-secondary py-5">
				<i class="bi bi-robot fs-1 d-block mb-3"></i>
				<p class="mb-0">Enter a topic above and click <strong>Generate with AI</strong>.<br>
				The result will appear here.</p>
			</div>
		</div>

	</div>
	<?php
}
