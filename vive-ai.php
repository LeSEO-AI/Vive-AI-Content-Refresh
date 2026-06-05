<?php
/**
 * Plugin Name: Vive - AI Content Reviver
 * Description: AI-powered content reviver — keep old posts ranking with AI.
 * Version: 1.0.0
 * Author: dav1lex
 * Author URI: https://leseo.app
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vive-ai
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VIVE_VERSION', '1.0.0' );
define( 'VIVE_PATH', plugin_dir_path( __FILE__ ) );
define( 'VIVE_URL', plugin_dir_url( __FILE__ ) );
define( 'VIVE_INCLUDES', VIVE_PATH . 'includes/' );
define( 'VIVE_PAGES', VIVE_INCLUDES . 'pages/' );
if ( ! defined( 'VIVE_WORKER_URL' ) ) {
	define( 'VIVE_WORKER_URL', 'https://seo-reviver-worker.zedxurl.workers.dev' );
}
// Local dev override: define( 'VIVE_WORKER_URL', 'http://localhost:8787' );

// Core includes
require_once VIVE_INCLUDES . 'api.php';
require_once VIVE_INCLUDES . 'helpers.php';
require_once VIVE_INCLUDES . 'menu.php';
require_once VIVE_INCLUDES . 'rest.php';
require_once VIVE_INCLUDES . 'enqueue.php';

// Page includes
require_once VIVE_PAGES . 'dashboard.php';
require_once VIVE_PAGES . 'create.php';
require_once VIVE_PAGES . 'revive.php';
require_once VIVE_PAGES . 'settings.php';
require_once VIVE_PAGES . 'bulk.php';
require_once VIVE_PAGES . 'competitor.php';
require_once VIVE_PAGES . 'calendar.php';
require_once VIVE_PAGES . 'analytics.php';
