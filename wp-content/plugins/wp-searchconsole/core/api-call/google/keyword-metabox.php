<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/
 * on: 30.06.2016
 * @since 0.1
 *
 * An API call to fetch keywords data per page for widgets.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_api_call_keywords_per_page')) {

    class wpsearchconsole_api_call_keywords_per_page
    {

        private $table_name;
        private $page;
        private $meta_key;
        private $ID;
        private $type;

        public $errors;

        public function __construct($page, $ID, $type)
        {

            global $wpdb;

            $this->page = $page;
            $this->meta_key = 'wpsearchconsole_google_keywords_ID';
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_json';
            $this->ID = $ID;
            $this->type = $type;

            $json_api = wpsearchconsole::getInstance()->google_json_api;

            //do we need it here?
            //update_option( 'wpsearchconsole_last_crawled_widget', time() );

            $this->args = $this->build_query();

            $datetime = current_time('mysql');
            $ID_store = md5('searchconsole/google-keywords/' . $datetime);

            //call for table data
            $json = $json_api->widget_api_call($this->args, true);

            if ($json['error']) {
                $this->errors[] = $json['message'];
            } else {
                $this->save_table_data($ID_store, $json['datas'], $datetime);
            }

        }

        //Build query to exicute
        public function build_query()
        {

            $output = array();

            $output['startDate'] = date('Y-m-d', strtotime('-28 days'));
            $output['endDate'] = date('Y-m-d');
            $output['dimensions'] = array('query');
            $output['dimensionFilterGroups'] = array(array('filters' => array(array(
                'dimension' => 'page',
                'operator' => 'contains',
                'expression' => $this->page))));
            return $output;
        }

        public function save_table_data($ID_store, $data, $datetime)
        {

            global $wpdb;

            $save_error = array(
                'datetime' => $datetime,
                'json_key' => $ID_store,
                'value' => $data,
            );
            $insert = $wpdb->insert($this->table_name, $save_error, array('%s', '%s', '%s'));
            if ($this->type == 'term') {
                update_term_meta($this->ID, $this->meta_key, $ID_store);
            } else {
                update_post_meta($this->ID, $this->meta_key, $ID_store);
            }

        }
    }
}
?>