<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/
 * on: 24.05.2016
 * @since 0.1
 *
 * An API call to fetch analysis data.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_api_call_analysis')) {

    class wpsearchconsole_api_call_analysis
    {

        private $table_name;
        private $args;

        public $errors;

        public function __construct()
        {

            global $wpdb;

            $this->table_name = $wpdb->prefix . 'wpsearchconsole_visitors';
            $this->args = $this->get_data();
            $this->delete_data($this->table_name);
            $this->errors = array();

            $json_api = wpsearchconsole::getInstance()->google_json_api;

            //call for table data
            $json = $json_api->analysis_api_call($this->args);

            if ($json['error']) {
                $this->errors[] = $json['message'];
            } else {
                $analysis_table = $this->prepare_table_data($json['datas']);
                if ($analysis_table) {
                    $this->save_table_data($analysis_table);
                }
            }

            if ($this->errors) {
                wpsearchconsole::getInstance()->setFlash('error', __($this->errors[0], 'wpsearchconsole'),true);
            }

        }

        //Saved from wpsearchconsole_analysis_top_display object
        public function get_data()
        {

            $output = array();

            $param_type = get_option('wpsearchconsole_analysis_datatype');
            $operator = get_option('wpsearchconsole_analysis_operator');
            $expression = get_option('wpsearchconsole_analysis_expression');
            $param = get_option('wpsearchconsole_analysis_param');
            $value = get_option('wpsearchconsole_analysis_value');

            //this are mandetory fields
            $ldate = date('Y-m-d');
            $sdate = date('Y-m-d', strtotime('-28 days'));
            $dimensions = array('query');
            $dimensionFilterGroups = false;
            $dimensionFilterGroups_alt = false;

            switch ($param_type) {
                case 'request':
                    $dimensions = array('query');

                    $dimensionFilterGroups_alt = ($operator && $expression ? true : false);
                    $dimensionFilterGroups_alt_dimension = 'query'; //notice the change of value here. It's intentaional
                    $dimensionFilterGroups_operator = $operator;
                    $dimensionFilterGroups_expression = $expression;
                    break;
                case 'page':
                    $dimensions = array('page');

                    $dimensionFilterGroups_alt = ($operator && $expression ? true : false);
                    $dimensionFilterGroups_alt_dimension = 'page';
                    $dimensionFilterGroups_operator = $operator;
                    $dimensionFilterGroups_expression = $expression;
                    break;
            }

            switch ($param) {
                case 'date':
                    switch ($value) {
                        case '7':
                            $sdate = date('Y-m-d', strtotime('-7 days'));
                            break;
                        case '90':
                            $sdate = date('Y-m-d', strtotime('-90 days'));
                            break;
                    }
                    break;
                case 'type':
                    $output['searchType'] = $value;
                    break;
                case 'device':
                    $dimensionFilterGroups = true;
                    $dimensionFilterGroups_dimension = 'device';
                    $dimensionFilterGroups_value = $value;
                    break;
                case 'country':
                    $dimensionFilterGroups = true;
                    $dimensionFilterGroups_dimension = 'country';
                    $dimensionFilterGroups_value = $value;
                    break;
            }

            $output['startDate'] = $sdate;
            $output['endDate'] = $ldate;
            $output['dimensions'] = $dimensions;

            $group = false;
            $group_alt = false;

            if ($dimensionFilterGroups) {
                $group = array(
                    'dimension' => $dimensionFilterGroups_dimension,
                    'expression' => $dimensionFilterGroups_value,
                );
            }

            if ($dimensionFilterGroups_alt) {
                $group_alt = array(
                    'dimension' => $dimensionFilterGroups_alt_dimension,
                    'operator' => $dimensionFilterGroups_operator,
                    'expression' => $dimensionFilterGroups_expression,
                );
            }
            if ($group || $group_alt) {
                $output['dimensionFilterGroups'] = array(array('filters' => array($group, $group_alt)));
            }

            return $output;
        }

        //save data for display in table
        public function save_table_data($analysis_table)
        {

            global $wpdb;
            $clicks = 0;
            $impressions = 0;
            $ctr = 0;
            $position = 0;
            foreach ($analysis_table as $analysis) {
                if (array_key_exists('keys', $analysis) && array_key_exists('clicks', $analysis) && array_key_exists('impressions', $analysis) && array_key_exists('ctr', $analysis) && array_key_exists('position', $analysis)) {

                    //fetch the keywords
                    $keys = $analysis['keys'];
                    if (is_array($keys) && array_key_exists('0', $keys)) {
                        $keys = $keys[0];
                    }
                    $save_error = array(
                        'requests' => $keys,
                        'clicks' => $analysis['clicks'],
                        'impressions' => $analysis['impressions'],
                        'ctr' => $analysis['ctr'],
                        'position' => $analysis['position'],
                    );
                    $insert = $wpdb->insert($this->table_name, $save_error, array('%s', '%s', '%s', '%s', '%s'));

                    $clicks = $clicks + $analysis['clicks'];
                    $impressions = $impressions + $analysis['impressions'];
                    $ctr = $ctr + $analysis['ctr'];
                    $position = $position + $analysis['position'];
                }
            }

            //Save total number of data returned
            $total = count($analysis_table);
            update_option('wpsearchconsole_analysis_requests', $total);
            update_option('wpsearchconsole_analysis_clicks', $clicks);
            update_option('wpsearchconsole_analysis_impressions', $impressions);
            update_option('wpsearchconsole_analysis_ctr', round(($ctr / $total), 3));
            update_option('wpsearchconsole_analysis_position', round(($position / $total), 0));
        }

        //prepare the data
        public function prepare_table_data($json)
        {

            $data = ($json ? json_decode($json, true) : false);

            $list = ($data && is_array($data) && array_key_exists('rows', $data) ? $data['rows'] : false);
            return $list;
        }

        //prepare the data
        public function prepare_data($json)
        {

            $data = ($json ? json_decode($json, true) : false);

            $list = ($data && is_array($data) && array_key_exists('rows', $data) ? $data['rows'] : false);
            $output = ($list && is_array($list) && array_key_exists('0', $list) ? $list[0] : false);

            return $output;
        }

        //delete the data first
        public function delete_data($table_name)
        {

            global $wpdb;
            $wpdb->query("TRUNCATE TABLE $table_name");
        }
    }
}
?>