<?php
/**
 *
 * @package: /wpsearchconsole/user/lib/
 * on: 24.05.2016
 * @since 0.1
 *
 * Add user display parts, in admin side.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

//For text displayed on screen mainly
require_once WPSEARCHCONSOLE_USER_PATH . 'help.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'notice.php';

//include supporting classes
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings-fields.php';

//Add metabox display - These are parts
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-todo.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-google-keywords.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-keywords.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-main-html-tags.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-link-analysis.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-meta-tags.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-duplicate-content.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-duplicate-description.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-duplicate-titles.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/metabox/display-mitambo-duplicate-perception.php';

//Add settings display - These are full, using the parts
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-google.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-mitambo.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-role.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-analysis.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-explore.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-svg-reports.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-data.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-todo.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-debug-info.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-debug-mitapi.php';
require_once WPSEARCHCONSOLE_USER_PATH . 'lib/settings/display-support.php';
?>