<?php
/**
 *
 * @package: advanced-wordpress-plugin/core/api-call/mitambo
 * on: 04.09.2016
 * @since 0.8
 *
 * An API call to fetch list of data.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_mitambo_list_of_data_api_call')) {

    class wpsearchconsole_mitambo_list_of_data_api_call
    {

        private $type;
        private $table_name;
        private $datetime;
        public $errors;

        public function __construct()
        {

            global $wpdb;

            $this->type = $this->type();
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_data';
            $mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->datetime = $mitambo_json_api->last_collect_date;

            $keyWithStatuses = array('keywords', 'status', 'duplication', 'resources', 'outgoinglinks');
            $start = 0;
            $end = $limit = 10;
            $totalRecords = false;
            $this->errors = array();
            $statusApi = $mitambo_json_api->api_status();
            if ($statusApi !== true) {
                $this->errors[] = $statusApi;
            } else {
                foreach ($this->type as $type_val) {

                    if (in_array($type_val, $keyWithStatuses)) {

                        $json_ar = array();
                        $endpoints_details = $this->get_subtype($type_val);
                        $endpoints = $endpoints_details['endpoints'];
                        $subtypes = $endpoints_details['subtypes'];
                        foreach ($endpoints as $key => $val) {
                            $start = 0;
                            $response = $mitambo_json_api->api_call_data_list($val);

                            if (!$response['error']) {
                                if ($response['datas'] && array_key_exists('total', $response['datas'])) {
                                    $totalRecords = $response['datas']['total'];
                                }
                                if ($totalRecords && $totalRecords > $limit) {

                                    while ($start < $totalRecords) {

                                        $response = $mitambo_json_api->api_call_data_list($val, $start, $end);
                                        if (!$response['error']) {
                                            $this->save_data($this->datetime, $type_val, $subtypes[$key], json_encode($response['datas']), $start, $end);
                                            $start = $start + $limit;
                                            $end = $limit;
                                        } else {
                                            // break
                                            break 3;
                                        }

                                    }
                                } else {
                                    $this->save_data($this->datetime, $type_val, $subtypes[$key], json_encode($response['datas']), $start, $end);
                                }

                            } else {
                                $this->errors[] = $response['message'];
                                break 2;
                            }
                        }
                    } else {
                        $response = $mitambo_json_api->api_call_data_list($type_val);
                        if (!$response['error']) {
                            $totalRecords = $response['datas']['total'];
                            if ($totalRecords && $totalRecords > $limit) {

                                $response = $mitambo_json_api->api_call_data_list($val, $start, $end);
                                if (!$response['error']) {
                                    $this->save_data($this->datetime, $type_val, '', json_encode($response['datas']), $start, $end);
                                } else {
                                    break 1;
                                }

                            } else {
                                $this->save_data($this->datetime, $type_val, '', json_encode($response['datas']), $start, $end);
                            }
                        } else {
                            $this->errors[] = $response['message'];
                            break 1;
                        }
                    }
                }
            }

        }

        //insert the array in db
        public function save_data($datetime, $type, $subtype, $data, $start, $end)
        {

            global $wpdb;

            $type=esc_sql($type);
            $subtype=esc_sql($subtype);
            $start=esc_sql($start);
            $end=esc_sql($end);

            if ($data) {

                $save_error = array(
                    'api_key' => $type,
                    'api_subkey' => $subtype,
                    'json_value' => $data,
                    'record_start' => $start ? $start : 0,
                    'record_end' => $end ? $end : 10,
                    'datetime' => $datetime,
                );
                $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpsearchconsole_data WHERE api_key = '$type' AND api_subkey = '$subtype' AND record_start = $start AND datetime ='$datetime'");
                if ($count > 0) {
                    $where = array(
                        'api_key' => $type,
                        'api_subkey' => $subtype,
                        'record_start' => $start ? $start : 0,
                        'datetime' => $datetime,
                    );
                    $update = $wpdb->update($this->table_name, $save_error, $where);
                } else {

                    $insert = $wpdb->insert($this->table_name, $save_error, array('%s', '%s', '%s', '%s', '%s', '%s'));
                }
            }
        }

        public function type()
        {

            return array('keywords', 'status', 'duplication', 'resources', 'outgoinglinks');
        }

        public function get_subtype($type)
        {
            $api = '';
            switch ($type) {

                case 'keywords':
                    $api = 'top_keywords';
                    $array['endpoints'] = array($api . '/0', $api . '/1', $api . '/2', $api . '/3', $api . '/4', $api . '/5', $api . '/6', $api . '/7');
                    $array['subtypes'] = array(0, 1, 2, 3, 4, 5,6,7);
                    break;
                case 'status':
                    $api = 'internal_by_status';
                    $array['endpoints'] = array($api . '/301', $api . '/302', $api . '/307', $api . '/404', $api . '/500');
                    $array['subtypes'] = array(301, 302, 307, 404, 500);
                    break;
                case 'duplication':
                    $api = '';
                    $array['endpoints'] = array('global_duplicate_title', 'global_duplicate_desc', 'global_duplicate_content', 'global_duplicate_perception');
                    $array['subtypes'] = array('title', 'desc', 'content', 'perception');
                    break;
                case 'resources':
                    $api = 'resources_by_status';
                    $array['endpoints'] = array($api . '/301', $api . '/302', $api . '/307', $api . '/404', $api . '/500');
                    $array['subtypes'] = array(301, 302, 307, 404, 500);
                    break;
                case 'outgoinglinks':
                    $api = 'outgoing_by_status';
                    $array['endpoints'] = array($api . '/301', $api . '/302', $api . '/307', $api . '/404', $api . '/500');
                    $array['subtypes'] = array(301, 302, 307, 404, 500);
                    break;
            }
            return $array;
        }


    }
}
?>