<?php
/**
 * Plugin Name: WP Search Console
 * Plugin URI: http://www.wpsearchconsole.com/
 * Description: Discover the real keywords used by Google to rank your pages using Google Search Console and a crawler. And get real insights on how to write better content for your users and for search engines.
 * Version: 0.8.51
 * Author: François Lamotte
 * Author URI: https://www.wpsearchconsole.com/blog/author/flamotte/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

defined('WPSEARCHCONSOLE_PLUGIN_VERSION') or define('WPSEARCHCONSOLE_PLUGIN_VERSION', '0.8.51');
defined('WPSEARCHCONSOLE_DEBUG') or define('WPSEARCHCONSOLE_DEBUG', false);
defined('WPSEARCHCONSOLE_PATH') or define('WPSEARCHCONSOLE_PATH', plugin_dir_path(__FILE__));
defined('WPSEARCHCONSOLE_LN_PATH') or define('WPSEARCHCONSOLE_LN_PATH', plugin_basename(plugin_dir_path(__FILE__) . 'asset/ln/'));
defined('WPSEARCHCONSOLE_ADMIN_PATH') or define('WPSEARCHCONSOLE_ADMIN_PATH', plugin_dir_path(__FILE__) . 'admin/');
defined('WPSEARCHCONSOLE_DB_PATH') or define('WPSEARCHCONSOLE_DB_PATH', plugin_dir_path(__FILE__) . 'db/');
defined('WPSEARCHCONSOLE_USER_PATH') or define('WPSEARCHCONSOLE_USER_PATH', plugin_dir_path(__FILE__) . 'user/');
defined('WPSEARCHCONSOLE_CORE_PATH') or define('WPSEARCHCONSOLE_CORE_PATH', plugin_dir_path(__FILE__) . 'core/');
defined('WPSEARCHCONSOLE_FILE') or define('WPSEARCHCONSOLE_FILE', plugin_basename(__FILE__));

defined('WPSEARCHCONSOLE_JS_PATH') or define('WPSEARCHCONSOLE_JS_PATH', wpsc_abs_plugins_url() . '/wp-searchconsole/asset/js/');
defined('WPSEARCHCONSOLE_CSS_PATH') or define('WPSEARCHCONSOLE_CSS_PATH', wpsc_abs_plugins_url() . '/wp-searchconsole/asset/css/');

if (WPSEARCHCONSOLE_DEBUG) {
    defined('WPSEARCHCONSOLE_PLUGIN_JWT_API') or define('WPSEARCHCONSOLE_PLUGIN_JWT_API', 'https://api.dev.mitambo.com');
    defined('WPSEARCHCONSOLE_PLUGIN_API') or define('WPSEARCHCONSOLE_PLUGIN_API', 'https://api.dev.mitambo.com/v2');
    defined('WPSEARCHCONSOLE_PLUGIN_APP') or define('WPSEARCHCONSOLE_PLUGIN_APP', 'https://app.dev.mitambo.com');
    defined('WPSEARCHCONSOLE_PLUGIN_UPDATE_PATH') or define('WPSEARCHCONSOLE_PLUGIN_UPDATE_PATH', 'http://update.mitambo.com/update.php');
} else {
    defined('WPSEARCHCONSOLE_PLUGIN_JWT_API') or define('WPSEARCHCONSOLE_PLUGIN_JWT_API', 'https://api.mitambo.com');
    defined('WPSEARCHCONSOLE_PLUGIN_API') or define('WPSEARCHCONSOLE_PLUGIN_API', 'https://api.mitambo.com/v2');
    defined('WPSEARCHCONSOLE_PLUGIN_APP') or define('WPSEARCHCONSOLE_PLUGIN_APP', 'https://app.mitambo.com');
    defined('WPSEARCHCONSOLE_PLUGIN_UPDATE_PATH') or define('WPSEARCHCONSOLE_PLUGIN_UPDATE_PATH', 'http://update.mitambo.com/update.php');
}


function wpsc_repair_protocol($link)
{
    $link_protocol = strtolower(substr($link, 0, strpos($link, '/'))) . '//';
    if (is_ssl() && ('https://' != $link_protocol)) {
        $clean_protocol = 'https://' . substr($link, strlen($link_protocol) );
        return $clean_protocol;
    }
    if (!is_ssl() && ('http://' != $link_protocol)) {
        $clean_protocol =  'http://' . substr($link, strlen($link_protocol) );
        return $clean_protocol;
    }
    return $link;
}

function wpsc_remove_trailingslash($link)
{
    return rtrim($link, '/');
}


function wpsc_abs_plugins_url( $path = '', $plugin = '' ) {
    $url = plugins_url( $path = '', $plugin = '' );
    if ( substr( $url, 0, 2 ) === '//' ) {
        return $url;
 	}
    if ( substr( $url, 5, 2 ) === '//' ) {
        return  substr( $url, 5, strlen($url) );
 	}
    if ( substr( $url, 6, 2 ) === '//' ) {
        return  substr( $url, 6, strlen($url) );
 	}
 	return $url;
}


function wpsc_myLogs($message)
{
    file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . ' - ' . $message . "<br/>\n", FILE_APPEND);
}

function wpsc_var2log($object = null, $msg = '')
{
    ob_start(); // start buffer capture
    var_export($object); // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean(); // end capture
    wpsc_myLogs($msg . $contents); // log contents of the result of var_dump( $object )
}


if (!function_exists("wpsc_array_column")) {
    function wpsc_array_column($array, $column_name)
    {
        return array_map(function ($element) use ($column_name) {
            return $element[$column_name];
        }, $array);
    }

}

 function add_wpsearchconsole_metabox( $classes=array() ) {

    if( !in_array( 'wpsearchconsole_metabox', $classes ) )
        $classes[] = 'wpsearchconsole_metabox';

    return $classes;
}

//print the $array in the format like sprintf for each
function wpsc_vnsprintf($format, array $data)
{
    preg_match_all('/(?<!∵) ∵((?: [^\$]*))\$/x', $format, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $offset = 0;
    foreach ($match as &$value) {
            $key =  $value[1][0];
        if ($key){
            $len = strlen($value[0][0]);
            $valuelen = strlen($data[$key]);
            $format = substr_replace($format, $data[$key], $offset + $value[0][1], $len );
            $offset -=  $len - $valuelen    ;
        }

    }
    return vsprintf($format, $data);
}
//Load the plugin WPSC
require_once 'main.php';


if (class_exists('wpsearchconsole')) {
    wpsearchconsole::getInstance();
}
?>
