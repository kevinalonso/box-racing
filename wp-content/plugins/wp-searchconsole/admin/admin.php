<?php
/**
 *
 * @package: wpsearchconsole/admin/
 * on: 19.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add settings pages for JWT and oAuth authentication and display of data.
 *
 */
if (!defined('ABSPATH')) exit;


//Check that 'class-wp-list-table.php' is available. Otherwise it may cause fatal error
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


//include supporting table classes
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-console.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-list-of-data.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-visitors.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-keywords.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-main-html-tags.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-link-analysis.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-todo.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-todo-metabox.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-meta-tags.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/table/table-duplication.php');


//include supporting widget classes
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-data-table.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-keywords.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-analysis.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-todo.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-svg.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-svg-large.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-internal-status.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-duplicated.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'lib/widget/widget-internal-competition.php');


//call main structures
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'obj/widgets.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'obj/scripts.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'obj/settings.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'obj/dashboard.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'obj/metabox.php');
require_once(WPSEARCHCONSOLE_ADMIN_PATH . 'obj/tabs.php'); ?>