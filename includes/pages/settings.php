<?php
/**
 * Settings page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vive_settings_page() {
	$message      = '';
	$message_type = 'success';

	// --- Handle API Key Save ---
	if ( isset( $_POST['vive_save_api'] ) ) {
		check_admin_referer( 'vive_api' );
		$api_key = sanitize_text_field( wp_unslash( $_POST['vive_api_key'] ?? '' ) );
		update_option( 'vive_api_key', $api_key );
		vive_bust_usage_cache();

		// Validate the key
		if ( empty( $api_key ) ) {
			delete_option( 'vive_api_key_status' );
			$message = 'API key cleared.';
		} else {
			$status = vive_validate_api_key( $api_key );
			update_option( 'vive_api_key_status', $status );
			if ( $status === 'valid' ) {
				$message = 'API key saved and verified.';
			} elseif ( $status === 'invalid' ) {
				$message = 'Invalid API key. Check your key and try again.';
				$message_type = 'error';
			} else {
				$message = 'API key saved. Could not verify (network error).';
				$message_type = 'warning';
			}
		}
	}

	// --- Handle Persona & Rules Save ---
	if ( isset( $_POST['vive_save_persona_rules'] ) ) {
		check_admin_referer( 'vive_persona_rules' );
		$persona  = sanitize_textarea_field( wp_unslash( $_POST['vive_persona'] ?? '' ) );
		$rules    = sanitize_textarea_field( wp_unslash( $_POST['vive_rules'] ?? '' ) );
		$language = sanitize_text_field( wp_unslash( $_POST['vive_language'] ?? '' ) );
		update_option( 'vive_persona', $persona );
		update_option( 'vive_rules', $rules );
		update_option( 'vive_language', $language );
		$message = 'Persona & rules saved.';
	}

	$api_key  = get_option( 'vive_api_key', '' );
	$persona  = get_option( 'vive_persona', '' );
	$rules    = get_option( 'vive_rules', '' );
	$language = get_option( 'vive_language', 'en' );
	$plan     = vive_get_plan();
	$left     = vive_remaining_posts();
	$limit    = max( 1, vive_monthly_limit() );
	$used     = $left !== null ? max( 0, $limit - $left ) : 0;
	$usage_pct  = $limit > 0 ? min( 100, ( $used / $limit ) * 100 ) : 0;
	$is_premium = $plan === 'premium';
	$cycle_days = $is_premium ? null : vive_cycle_days_left();
	$renewal_date = vive_get_renewal_date();
	$key_status = vive_get_api_key_status();

	$languages = array(
		'en' => 'English', 'de' => 'German',    'pl' => 'Polish',
		'fr' => 'French',  'es' => 'Spanish',   'it' => 'Italian',
		'pt' => 'Portuguese', 'nl' => 'Dutch',  'ru' => 'Russian',
		'ja' => 'Japanese',   'zh' => 'Chinese', 'ar' => 'Arabic',
		'tr' => 'Turkish',    'sv' => 'Swedish', 'no' => 'Norwegian',
		'da' => 'Danish',     'fi' => 'Finnish', 'cs' => 'Czech',
		'ro' => 'Romanian',   'hu' => 'Hungarian',
	);
	?>
	<div class="container-fluid p-4">

		<!-- Back -->
		<a href="?page=vive-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; Back to Dashboard</a>

		<h1 class="h3 mb-4">Settings</h1>

		<div class="row g-4 mb-4">

			<!-- API Key -->
			<div class="col-lg-6 d-flex">
				<div class="border rounded-3 p-4 w-100">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<h5 class="mb-0">API Key</h5>
						<?php if ( $key_status === 'valid' ) : ?>
							<span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Valid</span>
						<?php elseif ( $key_status === 'invalid' ) : ?>
							<span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i>Invalid</span>
						<?php elseif ( $key_status === 'unknown' ) : ?>
							<span class="badge text-bg-warning"><i class="bi bi-question-circle me-1"></i>Unverified</span>
						<?php endif; ?>
					</div>
					<form method="post" id="vive-api-form">
						<?php wp_nonce_field( 'vive_api' ); ?>
						<div class="mb-3">
							<label for="vive_api_key" class="form-label">License Key</label>
							<input type="password" id="vive_api_key" name="vive_api_key"
								   value="<?php echo esc_attr( $api_key ); ?>" class="form-control"
								   placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" />
						</div>
						<div class="d-flex gap-2">
							<button type="submit" name="vive_save_api" class="btn btn-primary" disabled>Save</button>
							<a href="https://leseo.app/login" target="_blank" rel="noopener" class="btn btn-outline-secondary">Get API Key</a>
						</div>
					</form>
					<div class="alert alert-primary py-2 px-3 mt-3 mb-0 small">
						<i class="bi bi-info-circle me-1"></i>
						Free to use. <a href="https://leseo.app/login" target="_blank" rel="noopener" class="alert-link">Register and get your key</a>.
					</div>
				</div>
			</div>

			<!-- Account -->
			<div class="col-lg-6 d-flex">
				<div class="border rounded-3 p-4 w-100">
					<h5 class="mb-3">Account</h5>
					<div class="d-flex align-items-center gap-2 mb-3">
					<span class="badge <?php echo esc_attr( $plan === 'premium' ? 'text-bg-success' : 'text-bg-secondary' ); ?>">
						<?php echo esc_html( $plan === 'premium' ? 'Premium' : 'Free' ); ?>
						</span>
						<?php if ( $plan !== 'premium' ) : ?>
							<small class="text-body-secondary"><?php esc_html_e( 'Free for use', 'vive-ai' ); ?></small>
						<?php endif; ?>
					</div>
					<div class="small text-body-secondary mb-1">
						AI calls this month: <strong><?php echo $left === null ? '&mdash;' : esc_html( "$left / $limit" ); ?></strong>
					</div>
					<div class="progress mb-2" style="height:6px;">
						<div class="progress-bar <?php echo esc_attr( $usage_pct > 80 ? 'bg-warning' : '' ); ?>" style="width:<?php echo esc_attr( $usage_pct ); ?>%"></div>
					</div>
					<div class="small text-body-secondary mb-3">
						<?php if ( $is_premium ) : ?>
							<?php if ( $renewal_date ) : ?>
								<i class="bi bi-clock"></i> Renews <?php echo esc_html( $renewal_date ); ?>
							<?php else : ?>
								<i class="bi bi-infinity"></i> Unlimited cycle
							<?php endif; ?>
						<?php elseif ( $cycle_days !== null ) : ?>
							<i class="bi bi-clock"></i> Resets in <?php echo esc_html( $cycle_days ); ?> day<?php echo $cycle_days !== 1 ? 's' : ''; ?>
						<?php else : ?>
							<i class="bi bi-clock"></i> 3-week cycle &mdash; starts on first use
						<?php endif; ?>
					</div>
					<?php if ( $plan === 'free' ) : ?>
						<a href="https://leseo.app" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm w-100"><?php esc_html_e( 'Upgrade to Premium', 'vive-ai' ); ?> &mdash; $20/mo</a>
					<?php endif; ?>
				</div>
			</div>

		</div>

		<!-- Persona & Writing Rules -->
		<div class="border rounded-3 p-4">
			<h5 class="mb-1">Persona &amp; Writing Rules</h5>
			<p class="text-body-secondary small mb-3">Define the author voice and writing style. Analyze your existing posts to auto-discover both.</p>

			<form method="post" id="vive-persona-rules-form">
				<?php wp_nonce_field( 'vive_persona_rules' ); ?>

				<div class="mb-3">
					<label for="vive_language" class="form-label">Output Language</label>
					<select id="vive_language" name="vive_language" class="form-select" style="max-width:300px;">
						<?php foreach ( $languages as $code => $name ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $language, $code ); ?>>
								<?php echo esc_html( $name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="row g-3 mb-3">
					<div class="col-md-6">
						<label for="vive_persona" class="form-label">Persona</label>
						<textarea id="vive_persona" name="vive_persona" class="form-control" rows="10"
								  placeholder="e.g. Write like a friendly teacher. Use analogies. Short paragraphs. No jargon."><?php echo esc_textarea( $persona ); ?></textarea>
					</div>
					<div class="col-md-6">
						<label for="vive_rules" class="form-label">Style Rules</label>
						<textarea id="vive_rules" name="vive_rules" class="form-control" rows="10"
								  placeholder="e.g. Use short sentences. No jargon. Always open with a question."><?php echo esc_textarea( $rules ); ?></textarea>
					</div>
				</div>

				<div class="d-flex gap-2">
					<button type="submit" name="vive_save_persona_rules" class="btn btn-primary" disabled>Save</button>
					<button type="button" id="vive-auto-discover" class="btn btn-outline-secondary">Analyze from Posts</button>
				</div>
			</form>
		</div>

	</div>
	<?php
	// Toast trigger via enqueue system
	if ( $message ) {
		wp_add_inline_script( 'vive-app', 'document.addEventListener("DOMContentLoaded",function(){showToast("' . esc_js( $message ) . '","' . esc_js( $message_type ) . '");});' );
	}
}
