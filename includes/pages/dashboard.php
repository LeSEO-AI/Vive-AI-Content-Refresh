<?php
/**
 * Dashboard page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leseo_dashboard_page() {
	$plan       = leseo_get_plan();
	$remaining  = leseo_remaining_posts();
	$limit      = max( 1, leseo_monthly_limit() );
	$used       = $remaining !== null ? max( 0, $limit - $remaining ) : 0;
	$usage_pct  = $limit > 0 ? min( 100, ( $used / $limit ) * 100 ) : 0;
	$is_premium   = $plan === 'premium';
	$total        = wp_count_posts()->publish;
	$refreshed    = get_option( 'leseo_refreshed_count', 0 );
	$cycle_days   = leseo_cycle_days_left();
	?>
	<div class="container-fluid p-4">

		<!-- Welcome -->
		<div class="border rounded-3 p-4 mb-3 bg-light bg-gradient">
			<h1 class="h3 mb-1"><?php esc_html_e( 'Welcome back', 'leseo-ai' ); ?></h1>
			<p class="text-body-secondary mb-0">
				<?php echo esc_html( $total ); ?> published posts &middot;
				<?php echo esc_html( $refreshed ); ?> refreshed this month
			</p>
		</div>

		<!-- Value prop -->
		<div class="border rounded-3 p-3 mb-4 bg-light bg-gradient">
			<div class="d-flex align-items-center gap-3">
				<i class="bi bi-rocket-takeoff fs-2 text-primary"></i>
				<div>
					<div class="fw-medium">Your old posts are leaving money on the table</div>
					<div class="text-body-secondary small">
						Most site owners post new content and forget about old posts. But old posts already have backlinks,
						authority, and Google trust. AI improves them in your voice. You keep the links, build on existing
						rankings, and open the door to more traffic. No other plugin does this.
					</div>
				</div>
			</div>
		</div>

		<!-- Disclaimer -->
		<div class="alert alert-info py-2 px-3 mb-4" role="alert">
			<i class="bi bi-info-circle me-1"></i>
			Set your writing rules in <a href="?page=leseo-ai-settings" class="alert-link">Settings &amp; Persona</a> first.
			AI content is for reference. For best results, review and edit before publishing.
		</div>

		<div class="row g-4">

			<!-- Quick Actions -->
			<div class="col-lg-8">
				<h5 class="mb-3">Quick Actions</h5>
				<div class="row g-3">
					<div class="col-sm-6">
						<a href="?page=leseo-ai-create" class="btn btn-outline-secondary w-100 text-start p-3 border-2 d-flex align-items-center gap-3">
							<i class="bi bi-pencil-square fs-4"></i>
							<span class="fw-medium">Create Post</span>
						</a>
					</div>
					<div class="col-sm-6">
						<a href="?page=leseo-ai-revive" class="btn btn-outline-secondary w-100 text-start p-3 border-2 d-flex align-items-center gap-3">
							<i class="bi bi-arrow-repeat fs-4"></i>
							<span class="fw-medium">Revive Post</span>
						</a>
					</div>
					<div class="col-sm-6">
						<button class="btn btn-outline-secondary w-100 text-start p-3 border-2 d-flex align-items-center gap-3" disabled>
							<i class="bi bi-files fs-4"></i>
							<span class="fw-medium">Bulk Generate</span>
							<span class="ms-auto badge text-bg-secondary">Soon</span>
						</button>
					</div>
					<div class="col-sm-6">
						<button class="btn btn-outline-secondary w-100 text-start p-3 border-2 d-flex align-items-center gap-3" disabled>
							<i class="bi bi-calendar-week fs-4"></i>
							<span class="fw-medium">Content Calendar</span>
							<span class="ms-auto badge text-bg-secondary">Soon</span>
						</button>
					</div>
					<div class="col-sm-6">
						<button class="btn btn-outline-secondary w-100 text-start p-3 border-2 d-flex align-items-center gap-3" disabled>
							<i class="bi bi-graph-up fs-4"></i>
							<span class="fw-medium">Analytics</span>
							<span class="ms-auto badge text-bg-secondary">Soon</span>
						</button>
					</div>
					<div class="col-sm-6">
						<button class="btn btn-outline-secondary w-100 text-start p-3 border-2 d-flex align-items-center gap-3" disabled>
							<i class="bi bi-people fs-4"></i>
							<span class="fw-medium">Competitors</span>
							<span class="ms-auto badge text-bg-secondary">Soon</span>
						</button>
					</div>
				</div>
			</div>

			<!-- Account -->
			<div class="col-lg-4">
				<h5 class="mb-3">Account</h5>
				<div class="border rounded-3 p-4">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="fw-medium">Plan</span>
					<span class="badge <?php echo esc_attr( $is_premium ? 'text-bg-success' : 'text-bg-secondary' ); ?>">
						<?php echo esc_html( $is_premium ? 'Premium' : 'Free' ); ?>
						</span>
					</div>
					<div class="small text-body-secondary mb-2">
						<?php if ( ! $is_premium ) : ?>
							<strong><?php echo $remaining === null ? '?' : esc_html( $remaining ); ?></strong> of <strong><?php echo esc_html( $limit ); ?></strong> refreshes left
						<?php else : ?>
							Unlimited refreshes
						<?php endif; ?>
					</div>
					<div class="progress mb-2" style="height:6px;">
						<div class="progress-bar <?php echo esc_attr( $usage_pct > 80 ? 'bg-warning' : '' ); ?>" style="width:<?php echo esc_attr( $usage_pct ); ?>%"></div>
					</div>
					<div class="small text-body-secondary mb-3">
						<?php if ( $cycle_days !== null ) : ?>
							<i class="bi bi-clock"></i> Resets in <?php echo esc_html( $cycle_days ); ?> day<?php echo $cycle_days !== 1 ? 's' : ''; ?>
						<?php else : ?>
							<i class="bi bi-clock"></i> 3-week cycle &mdash; starts on first use
						<?php endif; ?>
					</div>
					<?php if ( ! $is_premium ) : ?>
						<a href="https://leseo.app" target="_blank" rel="noopener" class="btn btn-primary btn-sm w-100"><?php esc_html_e( 'Upgrade', 'leseo-ai' ); ?> &mdash; $20/mo</a>
					<?php endif; ?>
				</div>
			</div>

		</div>

		<!-- Recent Posts -->
		<h5 class="mt-4 mb-3">Recent Posts</h5>
		<p class="text-body-secondary small mb-3">Older posts may benefit from a refresh. Use your judgment.</p>
		<div class="table-responsive border rounded-3">
			<table class="table table-hover mb-0">
				<thead class="table-light">
					<tr>
						<th>Title</th>
						<th>Last Updated</th>
						<th>Date</th>
						<th class="text-end">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$posts = get_posts( array(
						'numberposts' => 10,
						'post_status' => 'publish',
						'orderby'     => 'date',
						'order'       => 'ASC',
					) );
					foreach ( $posts as $post ) :
						$published  = strtotime( $post->post_date );
						$now        = time();
						$diff_months = ( ( $now - $published ) / ( 30 * 24 * 60 * 60 ) );
						$months     = max( 0, floor( $diff_months ) );

						if ( $months <= 5 ) {
							$badge_class = 'text-bg-success';
							$label = $months . ' mo ago';
						} elseif ( $months <= 6 ) {
							$badge_class = 'text-bg-warning';
							$label = $months . ' mo ago';
						} elseif ( $months <= 12 ) {
							$badge_class = 'text-bg-danger';
							$label = $months . ' mo ago';
						} else {
							$badge_class = 'text-bg-danger';
							$label = '12+ mo ago';
						}
						?>
						<tr>
							<td class="fw-medium"><?php echo esc_html( $post->post_title ); ?></td>
							<td><span class="badge <?php echo esc_attr( $badge_class ); ?>" title="Published <?php echo esc_attr( $months ); ?> months ago"><?php echo esc_html( $label ); ?></span></td>
							<td class="text-body-secondary small"><?php echo get_the_date( 'M j, Y', $post ); ?></td>
							<td class="text-end">
								<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'leseo-ai-revive', 'post_id' => $post->ID ), admin_url( 'admin.php' ) ) ); ?>" class="btn btn-outline-secondary btn-sm">Revive</a>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $posts ) ) : ?>
						<tr>
							<td colspan="4" class="text-center text-body-secondary py-4">No published posts. Create one first.</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

	</div>
	<?php
}
