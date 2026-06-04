<?php
/**
 * Uninstall Leseo.
 *
 * Runs when plugin is deleted via WP admin.
 * Removes all plugin options from the database.
 *
 * @package SEO_Reviver
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'leseo_api_key' );
delete_option( 'leseo_persona' );
delete_option( 'leseo_rules' );
delete_option( 'leseo_language' );
delete_option( 'leseo_refreshed_count' );
delete_transient( 'leseo_usage_cache' );
