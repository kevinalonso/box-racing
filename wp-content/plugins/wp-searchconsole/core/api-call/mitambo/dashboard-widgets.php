<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/
 * on: 30.06.2016
 * @since 0.1
 *
 * An API call to fetch svg data.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 *
 */

/*
top_keywords

internal_by_status

global_duplicate_title

global_duplicate_desc

global_duplicate_content

global_duplicate_perception

 */
if (!class_exists('wpsearchconsole_api_call_dashboard_widget')) {

    class wpsearchconsole_api_call_dashboard_widget
    {

        private $type;
        public $errors;

        public function __construct()
        {

            $this->errors = array();
            $this->type = $this->type();

            $json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $statusApi = $json_api->api_status();

            if ($statusApi !== true) {
                $this->errors[] = $statusApi;
            } else {

                $response = $json_api->api_call_global('crawler/status');
                if ($response['error']) {
                    $this->errors[] = $response['message'] . __('No Crawler status available', 'wpsearchconsole');
                }
                $response = $json_api->api_call_global('subscription/status');
                if ($response['error']) {
                    $this->errors[] = $response['message'] . __('No Subscription status available', 'wpsearchconsole');;
                }

                foreach ($this->type as $type_val) {
                    if ($type_val == 'top_keywords' || $type_val == 'internal_by_status') {
                        $subtype = $this->get_subtype($type_val);
                        foreach ($subtype as $val) {
                            $response = $json_api->api_call_global($type_val, $val);
                            if ($response['error']) {
                                $this->errors[] = $response['message'];
                                break 2;
                            }
                        }
                    } else {
                        $response = $json_api->api_call_global($type_val);
                        if ($response['error']) {
                            $this->errors[] = $response['message'];
                            break 1;
                        }
                    }
                }
            }


        }

        public function type()
        {

            return array('top_keywords', 'internal_by_status', 'global_duplicate_title', 'global_duplicate_desc', 'global_duplicate_content', 'global_duplicate_perception');
        }

        public function get_subtype($type)
        {

            switch ($type) {

                case 'top_keywords':
                    $array = array(1, 2, 3, 4, 5, 6, 7);
                    break;
                case 'internal_by_status':
                    $array = array(301, 302, 307, 404, 500);
                    break;
            }
            return $array;
        }
    }
}
?>