<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/
 * on: 24.05.2015
 * @since 0.1
 * @modified: 1
 *
 * An API call to fetch errors data.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_api_call_exploration_error')) {

    class wpsearchconsole_api_call_exploration_error
    {

        private $types;
        private $table_name;

        public $errors;

        public function __construct()
        {

            global $wpdb;

            $this->errors = array();
            $this->types = $this->types();
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_console';

            // category:"authPermissions","flashContent","manyToOneRedirect","notFollowed","notFound","other","roboted","serverError","soft404"
            // platform:"mobile","smartphoneOnly","web"

            // web or smartphoneOnly
            $platform = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'web');

            $this->delete_data($this->table_name, $platform);

            $json_api = wpsearchconsole::getInstance()->google_json_api;

            foreach ($this->types as $type) {
                $json = $json_api->api_call($type, $platform, false);
                if ($json['error']) {
                    $this->errors[] = $json['message'] . "platform $platform type $type";
                } else {
                    $error_list = $this->prepare_data($json['datas']);
                    if ($error_list) {
                        $this->save_data($error_list, $this->table_name, $platform, $type);
                        update_option("wpsearchconsole_last_crawled_{$platform}_errors", time());
                    }
                }
            }


        }

        //save data
        public function save_data($error_list, $table_name, $platform, $type)
        {

            global $wpdb;
            foreach ($error_list as $error) {
                if (array_key_exists('pageUrl', $error) && array_key_exists('last_crawled', $error) && array_key_exists('first_detected', $error) && array_key_exists('responseCode', $error)) {
                    $save_error = array(
                        'URL' => $error['pageUrl'],
                        'last_crawled' => $error['last_crawled'],
                        'first_detected' => $error['first_detected'],
                        'responseCode' => $error['responseCode'],
                        'platform' => $platform,
                        'type' => $type,
                    );
                    $insert = $wpdb->insert($table_name, $save_error, array('%s', '%s', '%s', '%s', '%s', '%s'));
                }
            }
        }

        //prepare the data
        public function prepare_data($json)
        {

            $data = ($json ? json_decode($json, true) : false);
            $error_list = ($data && is_array($data) && array_key_exists('urlCrawlErrorSample', $data) ? $data['urlCrawlErrorSample'] : false);

            return $error_list;
        }

        //delete the data first
        public function delete_data($table_name, $platform)
        {
            global $wpdb;
            $platform =esc_sql(sanitize_text_field($platform));
            $wpdb->delete($table_name, array('platform' => $platform), array('%s'));
        }

        //Define error types
        public function types()
        {
            //'flashContent',
            return array('authPermissions',  'manyToOneRedirect', 'notFollowed', 'notFound', 'other', 'roboted', 'serverError', 'soft404');
        }
    }
} ?>