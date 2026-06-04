<?php
/**
 * REST API endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extract first h1/h2 from AI-generated HTML for post title.
 */
function leseo_extract_title( $html ) {
	if ( preg_match( '/<h[12][^>]*>(.*?)<\/h[12]>/i', $html, $m ) ) {
		return trim( wp_strip_all_tags( $m[1] ) );
	}
	return '';
}

/**
 * Save or update a post.
 */
function leseo_save_post( $post_id, $content, $title, $status ) {
	if ( $post_id > 0 ) {
		$post_title = leseo_extract_title( $content ) ?: $title;
		$result = wp_update_post( array(
			'ID'           => $post_id,
			'post_title'   => $post_title,
			'post_content' => wp_slash( $content ),
			'post_status'  => $status,
		), true );
	} else {
		$post_title = leseo_extract_title( $content ) ?: ( $title ?: 'Untitled' );
		$result = wp_insert_post( array(
			'post_title'   => $post_title,
			'post_content' => wp_slash( $content ),
			'post_status'  => $status,
			'post_type'    => 'post',
		), true );
	}

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	leseo_bust_usage_cache();
	return $result;
}

add_action( 'rest_api_init', function() {
	// Save persona
	register_rest_route( 'leseo-ai/v1', '/persona', array(
		'methods' => 'POST',
		'callback' => function( $request ) {
			$persona = sanitize_textarea_field( $request->get_param( 'persona' ) );
			update_option( 'leseo_persona', $persona );
			return rest_ensure_response( array( 'success' => true ) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );

	// Delete persona
	register_rest_route( 'leseo-ai/v1', '/persona', array(
		'methods' => 'DELETE',
		'callback' => function() {
			delete_option( 'leseo_persona' );
			return rest_ensure_response( array( 'success' => true ) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );

	// Publish post
	register_rest_route( 'leseo-ai/v1', '/publish', array(
		'methods' => 'POST',
		'callback' => function( $request ) {
			$post_id = intval( $request->get_param( 'post_id' ) );
			$content = wp_kses_post( $request->get_param( 'content' ) );
			$title   = sanitize_text_field( $request->get_param( 'title' ) );

			if ( ! $content ) {
				return rest_ensure_response( array( 'error' => 'content required' ) );
			}

			$result = leseo_save_post( $post_id, $content, $title, 'publish' );

			if ( is_wp_error( $result ) ) {
				return rest_ensure_response( array( 'error' => $result->get_error_message() ) );
			}

			return rest_ensure_response( array( 'success' => true, 'post_id' => $result ) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );

	// Save draft
	register_rest_route( 'leseo-ai/v1', '/save-draft', array(
		'methods' => 'POST',
		'callback' => function( $request ) {
			$post_id = intval( $request->get_param( 'post_id' ) );
			$content = wp_kses_post( $request->get_param( 'content' ) );
			$title   = sanitize_text_field( $request->get_param( 'title' ) );

			if ( ! $content ) {
				return rest_ensure_response( array( 'error' => 'content required' ) );
			}

			$result = leseo_save_post( $post_id, $content, $title, 'draft' );

			if ( is_wp_error( $result ) ) {
				return rest_ensure_response( array( 'error' => $result->get_error_message() ) );
			}

			return rest_ensure_response( array( 'success' => true, 'post_id' => $result ) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );

	// Create new post with AI
	register_rest_route( 'leseo-ai/v1', '/create', array(
		'methods' => 'POST',
		'callback' => function( $request ) {
			$topic    = sanitize_text_field( $request->get_param( 'topic' ) );
			$keywords = sanitize_text_field( $request->get_param( 'keywords' ) );
			$tone     = sanitize_text_field( $request->get_param( 'tone' ) );
			$persona  = sanitize_textarea_field( get_option( 'leseo_persona', '' ) );
			$rules    = sanitize_textarea_field( get_option( 'leseo_rules', '' ) );
			$language = sanitize_text_field( get_option( 'leseo_language', 'en' ) );

			if ( ! $topic ) {
				return rest_ensure_response( array( 'error' => 'topic required' ) );
			}

			$result = leseo_create( $persona, $topic, $keywords, $tone, $rules, $language );

			if ( isset( $result['error'] ) ) {
				return rest_ensure_response( array( 'error' => $result['error'] ) );
			}

			return rest_ensure_response( array( 'success' => true, 'content' => $result['content'] ) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );

	// Revive existing post with AI
	register_rest_route( 'leseo-ai/v1', '/revive', array(
		'methods' => 'POST',
		'callback' => function( $request ) {
			$topic           = sanitize_text_field( $request->get_param( 'topic' ) );
			$keywords        = sanitize_text_field( $request->get_param( 'keywords' ) );
			$tone            = sanitize_text_field( $request->get_param( 'tone' ) );
			$original_content = wp_kses_post( $request->get_param( 'original_content' ) );
			$persona         = sanitize_textarea_field( get_option( 'leseo_persona', '' ) );
			$rules           = sanitize_textarea_field( get_option( 'leseo_rules', '' ) );
			$language        = sanitize_text_field( get_option( 'leseo_language', 'en' ) );

			if ( ! $original_content ) {
				return rest_ensure_response( array( 'error' => 'original_content required' ) );
			}

			$result = leseo_revive( $persona, $topic, $original_content, $keywords, $tone, $rules, $language );

			if ( isset( $result['error'] ) ) {
				return rest_ensure_response( array( 'error' => $result['error'] ) );
			}

			return rest_ensure_response( array( 'success' => true, 'content' => $result['content'] ) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );

	// Auto-discover persona + rules from existing posts
	register_rest_route( 'leseo-ai/v1', '/auto-discover', array(
		'methods' => 'POST',
		'callback' => function( $request ) {
			$posts = get_posts( array( 'numberposts' => 10, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ) );

			if ( empty( $posts ) ) {
				return rest_ensure_response( array( 'error' => 'No published posts to analyze.' ) );
			}

			$post_data = array_map( function( $p ) {
				return array(
					'title'   => $p->post_title,
					'content' => wp_trim_words( $p->post_content, 500 ),
				);
			}, $posts );

			$result = leseo_analyze_posts( $post_data );

			if ( isset( $result['error'] ) ) {
				return rest_ensure_response( array( 'error' => $result['error'] ) );
			}

			return rest_ensure_response( array(
				'success' => true,
				'persona' => $result['persona'] ?? '',
				'rules'   => $result['rules'] ?? '',
			) );
		},
		'permission_callback' => function() {
			return current_user_can( 'manage_options' );
		},
	) );
} );
