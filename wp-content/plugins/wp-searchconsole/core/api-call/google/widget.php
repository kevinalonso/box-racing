<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/
 * on: 25.06.2016
 * @since 0.1
 *
 * An API call to fetch analysis data for widgets.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_api_call_analysis_widget')) {

    class wpsearchconsole_api_call_analysis_widget
    {

        private $table_name;

        public $errors;

        public function __construct()
        {

            global $wpdb;
            $this->errors = array();
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_json';

            $json_api = wpsearchconsole::getInstance()->google_json_api;
            // do we need it here ?
            //update_option( 'wpsearchconsole_last_crawled_widget', time() );

            $this->args = $this->build_query();

            foreach ($this->args as $ID => $uni_query):

                $datetime = current_time('mysql');
                $id_string = 'wpsearchconsole_analysis_widget_' . $ID . '_ID';
                $ID_store = md5($id_string);

                //call for table data
                $json = $json_api->widget_api_call($uni_query);

                if ($json['error']) {
                    $this->errors[] = $json['message'] . "ID: $ID  Query: $uni_query ";
                } else {
                    $this->save_table_data($ID, $ID_store, $json['datas'], $datetime);
                }

            endforeach;
        }

        //Build query to exicute
        public function build_query()
        {

            $store_output = array();

            $data_type = $this->types();
            foreach ($data_type as $key => $val):

                $output = array();

                $output['startDate'] = date('Y-m-d', strtotime('-28 days'));
                $output['endDate'] = date('Y-m-d');
                $output['dimensions'] = array($val[0]);
                $output['dimensionFilterGroups'] = array(array('filters' => array(array(
                    'dimension' => 'device',
                    'expression' => $val[1]))));
                $output['startRow'] = 0;
                $output['rowLimit'] = 10;

                $store_output[$key] = $output;

            endforeach;

            $data_dimension = $this->dimension();
            foreach ($data_dimension as $key => $val):

                $output = array();

                $output['startDate'] = date('Y-m-d', strtotime('-28 days'));
                $output['endDate'] = date('Y-m-d');
                $output['dimensions'] = array($val[0]);
                $output['searchType'] = $val[1];
                $output['startRow'] = 0;
                $output['rowLimit'] = 10;

                $store_output[$key] = $output;

            endforeach;

            return $store_output;
        }

        //Define types  ([type,platform])
        public function types()
        {

            return array(
                'query_mobile' => array('query', 'mobile'),
                'query_desktop' => array('query', 'desktop'),
                'query_tablet' => array('query', 'tablet'),
                'page_mobile' => array('page', 'mobile'),
                'page_desktop' => array('page', 'desktop'),
                'page_tablet' => array('page', 'tablet'),
            );
        }

        //Define dimensions ([type,platform])
        public function dimension()
        {

            return array(
                'query_web' => array('query', 'web'),
                'query_image' => array('query', 'image'),
                'query_video' => array('query', 'video'),
                'page_web' => array('page', 'web'),
                'page_image' => array('page', 'image'),
                'page_video' => array('page', 'video'),
            );
        }

        public function save_table_data($ID, $ID_store, $data, $datetime)
        {

            global $wpdb;

            $pre = 'wpsearchconsole_analysis_widget';

            $ID = absint($ID);
            $ID_store = esc_sql($ID_store);
            $data = esc_sql($data);

            $save_error = array(
                'datetime' => $datetime,
                'json_key' => $ID_store,
                'value' => $data,
            );
            $insert = $wpdb->insert($this->table_name, $save_error, array('%s', '%s', '%s'));

            //update_option($pre . '_' . $ID . '_ID', $ID_store);
        }
    }
}
?>