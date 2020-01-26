<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/
 * on: 24.05.2016
 * @since 0.1
 *
 * Add core functionality files.
 *
 */
if (!defined('ABSPATH')) exit;


// Set up Authentication objects
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/oauth/oauth.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/oauth/prepare-api.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/jwt/jwt.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/jwt/prepare-api.php');

// Set up API calls
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/google/exploration_error.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/google/analysis.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/google/widget.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/google/keyword-metabox.php');

require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/mitambo/keywords.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/mitambo/svg.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/mitambo/dashboard-widgets.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'api-call/mitambo/list-of-data.php');

// Set up AJAX for SVG
require_once(WPSEARCHCONSOLE_CORE_PATH . 'ajax/ajax.php');

// Set up Accessories
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/explore-cat-tabs.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/data-cat-tabs.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/csv.php');
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/get-authors.php');


// Set up Update class
require_once(WPSEARCHCONSOLE_CORE_PATH . 'lib/wp_autoupdate.php');
?>