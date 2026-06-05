<?php
/**
 * API utility for Vive Worker communication.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function vive_get_api_key() {
    return get_option('vive_api_key', '');
}

function vive_get_worker_url() {
	return defined( 'VIVE_WORKER_URL' ) ? rtrim( VIVE_WORKER_URL, '/' ) : '';
}

function vive_call_worker($endpoint, $body) {
    $url = vive_get_worker_url() . $endpoint;
    $key = vive_get_api_key();

    $response = wp_remote_post($url, array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'X-API-Key' => $key,
        ),
        'body' => json_encode($body),
        'timeout' => 120,
    ));

    if (is_wp_error($response)) {
        return array('error' => $response->get_error_message());
    }

    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($code !== 200) {
        return array('error' => $data['error'] ?? "HTTP $code");
    }

    return $data;
}

/**
 * Analyze posts → build voice persona.
 */
function vive_analyze_posts($posts) {
    return vive_call_worker('/analyze', array('posts' => $posts));
}

/**
 * Create new post from scratch.
 */
function vive_create($persona, $topic, $keywords = '', $tone = '', $rules = '', $language = '') {
    return vive_call_worker('/create', array(
        'persona'  => $persona,
        'topic'    => $topic,
        'keywords' => $keywords,
        'tone'     => $tone,
        'rules'    => $rules,
        'language' => $language,
    ));
}

/**
 * Revive existing post.
 */
function vive_revive($persona, $topic, $original_content, $keywords = '', $tone = '', $rules = '', $language = '') {
    return vive_call_worker('/revive', array(
        'persona'        => $persona,
        'topic'          => $topic,
        'originalContent'=> $original_content,
        'keywords'       => $keywords,
        'tone'           => $tone,
        'rules'          => $rules,
        'language'       => $language,
    ));
}

/**
 * Test connection to Worker.
 */
function vive_test_connection() {
    $url = vive_get_worker_url() . '/health';
    $key = vive_get_api_key();

    $response = wp_remote_get($url, array(
        'headers' => array('X-API-Key' => $key),
        'timeout' => 10,
    ));

    if (is_wp_error($response)) return false;

    $code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    return $code === 200 && ($body['status'] ?? '') === 'ok';
}
