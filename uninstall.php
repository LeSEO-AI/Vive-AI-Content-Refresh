<?php
/**
 * Uninstall Vive.
 *
 * Runs when plugin is deleted via WP admin.
 * Removes all plugin options from the database.
 *
 * @package SEO_Reviver
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'vive_api_key' );
delete_option( 'vive_persona' );
delete_option( 'vive_rules' );
delete_option( 'vive_language' );
delete_option( 'vive_refreshed_count' );
delete_transient( 'vive_usage_cache' );
