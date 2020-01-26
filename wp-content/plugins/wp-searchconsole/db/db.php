<?php
/**
 *
 * @package: advanced-wordpress-plugin/db/
 * on: 24.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add database manipulation objects.
 *
 */
if (!defined('ABSPATH')) exit;


//call necessary database objects
require_once(WPSEARCHCONSOLE_DB_PATH . 'lib/create-sql.php');
require_once(WPSEARCHCONSOLE_DB_PATH . 'lib/query-sql.php');
require_once(WPSEARCHCONSOLE_DB_PATH . 'lib/do.php');
require_once(WPSEARCHCONSOLE_DB_PATH . 'lib/csv-build.php');
?>