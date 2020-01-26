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
 */
if (!class_exists('wpsearchconsole_api_call_svg')) {

    class wpsearchconsole_api_call_svg
    {

        private $type;
        private $datetime;
        public $errors;

        public function __construct()
        {

            global $wpdb;

            $this->type = $this->type();

            $json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->errors = array();

            foreach ($this->type as $type_val) {

                $response = $json_api->api_call_global($type_val);

                if ($response['error']) {
                    $this->errors[] = $response['message'];
                    break;
                }
            }

//            if ($this->errors) {
//                wpsearchconsole::getInstance()->setFlash('error', __($this->errors[0], 'wpsearchconsole'));
//            }
        }

        public function type()
        {
            return array('svg-demo1' /*, 'svg-demo2', 'svg-demo3', 'svg-demo4', 'svg-demo5', 'svg-demo6', 'svg-demo7'*/);
        }
    }
}
?>