<?php
/**
 * Admin menu registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leseo_register_menu() {
	add_menu_page(
		'LeSEO',
		'LeSEO',
		'manage_options',
		'leseo-ai',
		'leseo_dashboard_page',
		'dashicons-update',
		99
	);

	// Dashboard
	add_submenu_page(
		'leseo-ai',
		'Dashboard',
		'Dashboard',
		'manage_options',
		'leseo-ai',
		'leseo_dashboard_page'
	);

	// MVP pages
	add_submenu_page(
		'leseo-ai',
		'Create Post',
		'Create Post',
		'manage_options',
		'leseo-ai-create',
		'leseo_create_page'
	);

	add_submenu_page(
		'leseo-ai',
		'Revive Post',
		'Revive Post',
		'manage_options',
		'leseo-ai-revive',
		'leseo_revive_page'
	);


	// Settings
	add_submenu_page(
		'leseo-ai',
		'Settings',
		'Settings',
		'manage_options',
		'leseo-ai-settings',
		'leseo_settings_page'
	);
	
	// Coming soon
	add_submenu_page(
		'leseo-ai',
		'Bulk Generation',
		'Bulk Gen <span class="update-plugins" style="background:#6c757d;font-size:9px;padding:1px 5px;border-radius:9px;margin-left:4px;vertical-align:middle;">Soon</span>',
		'manage_options',
		'leseo-ai-bulk',
		'leseo_bulk_page'
	);

	add_submenu_page(
		'leseo-ai',
		'Competitor Analysis',
		'Competitors <span class="update-plugins" style="background:#6c757d;font-size:9px;padding:1px 5px;border-radius:9px;margin-left:4px;vertical-align:middle;">Soon</span>',
		'manage_options',
		'leseo-ai-competitor',
		'leseo_competitor_page'
	);

	add_submenu_page(
		'leseo-ai',
		'Content Calendar',
		'Calendar <span class="update-plugins" style="background:#6c757d;font-size:9px;padding:1px 5px;border-radius:9px;margin-left:4px;vertical-align:middle;">Soon</span>',
		'manage_options',
		'leseo-ai-calendar',
		'leseo_calendar_page'
	);

	add_submenu_page(
		'leseo-ai',
		'Analytics',
		'Analytics <span class="update-plugins" style="background:#6c757d;font-size:9px;padding:1px 5px;border-radius:9px;margin-left:4px;vertical-align:middle;">Soon</span>',
		'manage_options',
		'leseo-ai-analytics',
		'leseo_analytics_page'
	);

}
add_action( 'admin_menu', 'leseo_register_menu' );
