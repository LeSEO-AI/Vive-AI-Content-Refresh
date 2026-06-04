<?php
/**
 * Plugin Name: LeSEO - AI Content Refresh
 * Description: AI-powered content refresh — keep old posts ranking with AI.
 * Version: 1.0.0
 * Author: dav1lex
 * Author URI: https://leseo.app
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: leseo-ai
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LESEO_VERSION', '1.0.0' );
define( 'LESEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'LESEO_URL', plugin_dir_url( __FILE__ ) );
define( 'LESEO_INCLUDES', LESEO_PATH . 'includes/' );
define( 'LESEO_PAGES', LESEO_INCLUDES . 'pages/' );
if ( ! defined( 'LESEO_WORKER_URL' ) ) {
	define( 'LESEO_WORKER_URL', 'https://seo-reviver-worker.zedxurl.workers.dev' );
}
// Local dev override: define( 'LESEO_WORKER_URL', 'http://localhost:8787' );

// Core includes
require_once LESEO_INCLUDES . 'api.php';
require_once LESEO_INCLUDES . 'helpers.php';
require_once LESEO_INCLUDES . 'menu.php';
require_once LESEO_INCLUDES . 'rest.php';
require_once LESEO_INCLUDES . 'enqueue.php';

// Page includes
require_once LESEO_PAGES . 'dashboard.php';
require_once LESEO_PAGES . 'create.php';
require_once LESEO_PAGES . 'revive.php';
require_once LESEO_PAGES . 'settings.php';
require_once LESEO_PAGES . 'bulk.php';
require_once LESEO_PAGES . 'competitor.php';
require_once LESEO_PAGES . 'calendar.php';
require_once LESEO_PAGES . 'analytics.php';
