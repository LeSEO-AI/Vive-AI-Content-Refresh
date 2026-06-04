<?php
/**
 * Enqueue scripts and styles.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leseo_enqueue_scripts( $hook ) {
	if ( strpos( $hook, 'leseo' ) === false ) {
		return;
	}

	// Bootstrap 5.3.8 + Icons (bundled locally)
	wp_enqueue_style( 'bootstrap', LESEO_URL . 'vendor/bootstrap.min.css', array(), '5.3.8' );
	wp_enqueue_style( 'bootstrap-icons', LESEO_URL . 'vendor/bootstrap-icons.min.css', array( 'bootstrap' ), '1.11.3' );
	wp_enqueue_script( 'bootstrap', LESEO_URL . 'vendor/bootstrap.bundle.min.js', array(), '5.3.8', true );

	// Our minimal overrides
	wp_enqueue_style( 'leseo-design', LESEO_URL . 'assets/index.css', array(), LESEO_VERSION );

	// App JS — load on all Leseo pages
	wp_enqueue_script( 'leseo-app', LESEO_URL . 'assets/app.js', array(), LESEO_VERSION, true );

	// Always provide REST API config to all pages that load app.js
	$post_id = 0;
	if ( $hook === 'leseo-ai_page_leseo-ai-revive' && isset( $_GET['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = intval( wp_unslash( $_GET['post_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	wp_localize_script( 'leseo-app', 'leseoApi', array(
		'restUrl' => rest_url( 'leseo-ai/v1' ),
		'nonce'   => wp_create_nonce( 'wp_rest' ),
		'postId'  => $post_id,
	) );

	// Diff.js — always load alongside app.js
	wp_enqueue_script( 'jsdiff', LESEO_URL . 'vendor/diff.min.js', array(), '7.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'leseo_enqueue_scripts' );
