<?php
/**
 *
 * @package: /wpsearchconsole/user/
 * on: 24.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add user display parts, in admin side.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Call all display objects
 */
require_once WPSEARCHCONSOLE_USER_PATH . '/lib/display-settings.php';
require_once WPSEARCHCONSOLE_USER_PATH . '/filter_url.php';
//Call in all the notices
//require_once( WPSEARCHCONSOLE_USER_PATH . 'help.php' );
//require_once( WPSEARCHCONSOLE_USER_PATH . 'notice.php' );
?>