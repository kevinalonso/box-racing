<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/
 * on: 24.05.2016
 * @since 0.1
 *
 * An API call to fetch mitambo keywords.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_mitambo_api_call')) {

    class wpsearchconsole_mitambo_api_call
    {
        private $type;
        private $permalink;
        private $post_ID;
        private $mitambo_json_api;
        public $errors;

        public function __construct($permalink, $post_ID)
        {
            global $wpdb;

            $this->errors = array();
            $this->type = $this->type();
            $this->permalink = $permalink;
            $this->post_ID = $post_ID;
            $this->mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
        }

        public function save_response_data($type)
        {

            foreach ($this->type as $type_val) {
                $response = $this->mitambo_json_api->api_call($this->permalink, $type_val);

                if ($response['error'] == true) {
                    $this->errors[] = $response['message'];
                    break;
                }
            }
        }

        public function type()
        {
            return array('simple_keywords', 'double_keywords', 'triple_keywords', 'characteristic_keywords', 'main_html_tags', 'link_analysis/summary', 'link_analysis/inbounds', 'link_analysis/outbounds', 'meta_tags', 'post_duplicate_perception', 'post_duplicate_title', 'post_duplicate_desc', 'post_duplicate_content');
        }
    }
}
?>