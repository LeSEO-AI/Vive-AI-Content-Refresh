<?php
/**
 * Enqueue scripts and styles.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vive_enqueue_scripts( $hook ) {
	if ( strpos( $hook, 'vive' ) === false ) {
		return;
	}

	// Bootstrap 5.3.8 + Icons (bundled locally)
	wp_enqueue_style( 'bootstrap', VIVE_URL . 'vendor/bootstrap.min.css', array(), '5.3.8' );
	wp_enqueue_style( 'bootstrap-icons', VIVE_URL . 'vendor/bootstrap-icons.min.css', array( 'bootstrap' ), '1.11.3' );
	wp_enqueue_script( 'bootstrap', VIVE_URL . 'vendor/bootstrap.bundle.min.js', array(), '5.3.8', true );

	// Our minimal overrides
	wp_enqueue_style( 'vive-design', VIVE_URL . 'assets/index.css', array(), VIVE_VERSION );

	// App JS — load on all Vive pages
	wp_enqueue_script( 'vive-app', VIVE_URL . 'assets/app.js', array(), VIVE_VERSION, true );

	// Always provide REST API config to all pages that load app.js
	$post_id = 0;
	if ( isset( $_GET['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = intval( wp_unslash( $_GET['post_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	wp_localize_script( 'vive-app', 'viveApi', array(
		'restUrl' => rest_url( 'vive-ai/v1' ),
		'nonce'   => wp_create_nonce( 'wp_rest' ),
		'postId'  => $post_id,
	) );

	// Diff.js — always load alongside app.js
	wp_enqueue_script( 'jsdiff', VIVE_URL . 'vendor/diff.min.js', array(), '7.0.0', true );

	// Settings page — dirty tracking + auto-discover (safe on all pages — JS early-returns if elements missing)
	wp_enqueue_script( 'vive-settings', VIVE_URL . 'assets/settings.js', array( 'vive-app' ), VIVE_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'vive_enqueue_scripts' );
