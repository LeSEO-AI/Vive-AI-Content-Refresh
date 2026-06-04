<?php
/**
 * API utility for Leseo Worker communication.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function leseo_get_api_key() {
    return get_option('leseo_api_key', '');
}

function leseo_get_worker_url() {
	return defined( 'LESEO_WORKER_URL' ) ? rtrim( LESEO_WORKER_URL, '/' ) : '';
}

function leseo_call_worker($endpoint, $body) {
    $url = leseo_get_worker_url() . $endpoint;
    $key = leseo_get_api_key();

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
function leseo_analyze_posts($posts) {
    return leseo_call_worker('/analyze', array('posts' => $posts));
}

/**
 * Create new post from scratch.
 */
function leseo_create($persona, $topic, $keywords = '', $tone = '', $rules = '', $language = '') {
    return leseo_call_worker('/create', array(
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
function leseo_revive($persona, $topic, $original_content, $keywords = '', $tone = '', $rules = '', $language = '') {
    return leseo_call_worker('/revive', array(
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
function leseo_test_connection() {
    $url = leseo_get_worker_url() . '/health';
    $key = leseo_get_api_key();

    $response = wp_remote_get($url, array(
        'headers' => array('X-API-Key' => $key),
        'timeout' => 10,
    ));

    if (is_wp_error($response)) return false;

    $code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    return $code === 200 && ($body['status'] ?? '') === 'ok';
}
