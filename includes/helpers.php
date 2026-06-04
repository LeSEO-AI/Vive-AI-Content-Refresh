<?php
/**
 * Shared helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Back link to dashboard.
 */
function leseo_back_link() {
	return '<a href="?page=leseo-ai" class="btn btn-sm btn-outline-secondary px-3 mb-3">&larr; Back to Dashboard</a>';
}

/**
 * Fetch usage from Worker /usage endpoint.
 * Cached via WP transient for 60s. Cache busted on publish/draft.
 * Returns [used, limit, remaining] or null on failure.
 */
function leseo_fetch_usage() {
	$cache = get_transient( 'leseo_usage_cache' );

	if ( $cache !== false ) {
		return $cache;
	}

	$api_key = get_option( 'leseo_api_key', '' );
	if ( ! $api_key ) {
		return null;
	}

	$url = leseo_get_worker_url() . '/usage';

	$response = wp_remote_get( $url, array(
		'headers' => array( 'X-API-Key' => $api_key ),
		'timeout' => 10,
	) );

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! $data || isset( $data['error'] ) ) {
		return null;
	}

	$cache = array(
		'used'        => intval( $data['used'] ?? 0 ),
		'limit'       => intval( $data['limit'] ?? 5 ),
		'remaining'   => intval( $data['remaining'] ?? 5 ),
		'cycle_start' => $data['cycle_start'] ?? null,
	);

	set_transient( 'leseo_usage_cache', $cache, 60 );

	return $cache;
}

/**
 * Bust usage cache after publish/draft.
 */
function leseo_bust_usage_cache() {
	delete_transient( 'leseo_usage_cache' );
}

/**
 * Get posts remaining this month.
 */
function leseo_remaining_posts() {
	$usage = leseo_fetch_usage();
	return $usage ? $usage['remaining'] : null;
}

/**
 * Get posts used this month.
 */
function leseo_used_posts() {
	$usage = leseo_fetch_usage();
	return $usage ? $usage['used'] : null;
}

/**
 * Monthly post limit for current plan.
 */
function leseo_monthly_limit() {
	$usage = leseo_fetch_usage();
	return $usage ? $usage['limit'] : null;
}

/**
 * Get current plan from D1 or fallback.
 */
function leseo_get_plan() {
	$limit = leseo_monthly_limit();
	if ( $limit === null ) return 'unknown';
	return $limit > 100 ? 'premium' : 'free';
}

/**
 * Render usage footer (posts remaining + upgrade link).
 */
function leseo_usage_footer() {
	$remaining = leseo_remaining_posts();
	$limit     = leseo_monthly_limit();
	?>
	<div class="leseo-card" style="text-align:center;">
		<p class="leseo-text-muted" style="margin:0;">
			Posts remaining this month:
			<?php if ( $remaining === null ) : ?>
				<strong>&mdash;</strong>
			<?php else : ?>
				<strong><?php echo esc_html( $remaining ); ?> / <?php echo esc_html( $limit ); ?></strong>
				&mdash; <span class="leseo-text-muted">Upgrade for unlimited</span>
			<?php endif; ?>
		</p>
	</div>
	<?php
}

/**
 * Get days left in the current 3-week cycle.
 * Returns null if no cycle started (no usage yet).
 */
function leseo_cycle_days_left() {
	$usage = leseo_fetch_usage();
	if ( ! $usage || empty( $usage['cycle_start'] ) ) {
		return null;
	}

	$cycle_start = new DateTime( $usage['cycle_start'] );
	$now         = new DateTime();
	$days_since  = (int) $now->diff( $cycle_start )->days;
	$days_left   = max( 0, 21 - $days_since );

	return $days_left;
}

/**
 * Validate an API key by calling /usage endpoint.
 * Returns: 'valid', 'invalid', or 'unknown' (network error).
 */
function leseo_validate_api_key( $api_key ) {
	if ( empty( $api_key ) ) {
		return 'invalid';
	}

	$url = leseo_get_worker_url() . '/usage';

	$response = wp_remote_get( $url, array(
		'headers' => array( 'X-API-Key' => $api_key ),
		'timeout' => 10,
	) );

	if ( is_wp_error( $response ) ) {
		return 'unknown';
	}

	$code = wp_remote_retrieve_response_code( $response );

	if ( $code === 200 ) {
		return 'valid';
	} elseif ( $code === 401 ) {
		return 'invalid';
	} else {
		return 'unknown';
	}
}

/**
 * Get cached API key validation status.
 * Returns: 'valid', 'invalid', 'unknown', or null (not checked).
 */
function leseo_get_api_key_status() {
	$api_key = get_option( 'leseo_api_key', '' );
	if ( empty( $api_key ) ) {
		return null;
	}

	$status = get_option( 'leseo_api_key_status', null );

	// If no status stored, validate now
	if ( $status === null ) {
		$status = leseo_validate_api_key( $api_key );
		update_option( 'leseo_api_key_status', $status );
	}

	return $status;
}
