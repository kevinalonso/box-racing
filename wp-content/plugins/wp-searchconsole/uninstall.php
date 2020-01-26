<?php
/**
 *
 * @package: wpsearchconsole/
 * on: 26.05.2016
 * @since 0.1
 *
 * Uninstall function.
 *
 */
if (!defined('ABSPATH')) exit;

// SECURITY if uninstall.php is not called by WordPress, die (DO NOT REMOVE)
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/**
 *
 * The mother object which defines WP search console plugin.
 *
 */
global $wpdb;

$console = $wpdb->prefix . 'wpsearchconsole_console';
$visitors = $wpdb->prefix . 'wpsearchconsole_visitors';
$json = $wpdb->prefix . 'wpsearchconsole_json';
$cache = $wpdb->prefix . 'wpsearchconsole_cache';
$todo = $wpdb->prefix . 'wpsearchconsole_todo';

$wpdb->query("DROP TABLE IF EXISTS $cache");
$wpdb->query("DROP TABLE IF EXISTS $console");
$wpdb->query("DROP TABLE IF EXISTS $visitors");
$wpdb->query("DROP TABLE IF EXISTS $json");
$wpdb->query("DROP TABLE IF EXISTS $todo");


$pre = 'wpsearchconsole_';
$arr = array(
    'selected_site',
    'google',
    'google_token',
    'google_expiry',
    'google_time',
    'google_refresh_token',
    'selected_site',
    'mitambo',
    'last_crawled_widget',
    'last_crawled_errors',
    'last_crawled_analysis',
    'analysis_datatype',
    'analysis_operator',
    'analysis_expression',
    'analysis_param',
    'analysis_value',
    'analysis_requests',
    'analysis_clicks',
    'analysis_impressions',
    'analysis_ctr',
    'analysis_position',
    'google_keywords_ID',
    'svg-demo1_ID',
    'svg-demo2_ID',
    'svg-demo3_ID',
    'svg-demo4_ID',
    'svg-demo5_ID',
    'svg-demo6_ID',
    'svg-demo7_ID',
    'analysis_widget_query_mobile_ID',
    'analysis_widget_query_desktop_ID',
    'analysis_widget_query_tablet_ID',
    'analysis_widget_page_mobile_ID',
    'analysis_widget_page_desktop_ID',
    'analysis_widget_page_tablet_ID',
    'analysis_widget_query_web_ID',
    'analysis_widget_query_image_ID',
    'analysis_widget_query_video_ID',
    'analysis_widget_page_web_ID',
    'analysis_widget_page_image_ID',
    'analysis_widget_page_video_ID',

    'wpsearchconsole_todo_filter_priority',
    'wpsearchconsole_todo_filter_category',
    'wpsearchconsole_todo_filter_responsible',
    'wpsearchconsole_todo_filter_dates',
    'wpsearchconsole_todo_categories',
    'wpsearchconsole_todo_priority',
    'wpsearchconsole_todo_widget_filter_key',
    'wpsearchconsole_mitambo',
    'wpsearchconsole_mitambo_auth_user',
    'wpsearchconsole_mitambo_last_crawled_date',
    'wpsearchconsole_google',
    'wpsearchconsole_google_token',
    'wpsearchconsole_google_refresh_token',
    'wpsearchconsole_google_time',
    'wpsearchconsole_google_expiry',
    'wpsearchconsole_selected_site',
    'wpsearchconsole_capability',
    'wpsearchconsole_client_secret',
    'wpsearchconsole_client_ID',
    'wpsearchconsole_version',

    'wpsearchconsole_analysis_point',
    'wpsearchconsole_analysis_datatype',
    'wpsearchconsole_analysis_operator',
    'wpsearchconsole_analysis_expression',
    'wpsearchconsole_analysis_param',
    'wpsearchconsole_analysis_value',
    'wpsearchconsole_analysis_clicks',
    'wpsearchconsole_analysis_impressions',
    'wpsearchconsole_analysis_ctr',
    'wpsearchconsole_analysis_position',
    'wpsearchconsole_analysis_requests',


);
foreach ($arr as $key) :
    delete_option($pre . $key);
    delete_site_option($pre . $key);
endforeach;
?>