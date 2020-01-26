<?php
/**
 *
 * @package: wpsearchconsole/core/lib/
 * on: 19.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add settings pages oAuth authentication object.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_data_categories')) {

    class wpsearchconsole_data_categories
    {

        public $assembly;
        public $tab;

        public function __construct($tab_name)
        {

            $this->tab_name = $tab_name;
            $mitambo_json_api = wpsearchconsole::getInstance()->mitambo_json_api;
            $this->datetime = $mitambo_json_api->last_collect_date;

            $type = $this->types($this->tab_name);
            $assembly = array();

            if (!is_array($type['key'])) {
                if (!empty($type['statuses']) && is_array($type['statuses'])) {
                    foreach ($type['statuses'] as $key => $val) {
                        $rows = $this->query($this->tab_name, $key);
                        $assembly[$key] = $this->count($rows);
                    }
                }
            }

            $this->assembly = $assembly;
            $this->statuscodes = $type['statuses'] ? $type['statuses'] : false;
        }

        public function count($rows)
        {

            if ($rows && is_array($rows)) {
                $row_count = $rows[0];
                $count = $row_count['COUNT(*)'];
            }
            return ($count ? $count : false);
        }

        public function query($tab, $type)
        {

            global $wpdb;
            $get_rows = 0;
            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wpsearchconsole_data WHERE api_key = '$tab'";
            $sql .= ($type != 'all') ? " AND api_subkey = '$type'" : "";
            $sql .= " AND datetime = '" . substr($this->datetime, 0, 10) . "'";

            $get_rows = $wpdb->get_results($sql, ARRAY_A);

            return $get_rows ? $get_rows : 0;
        }

        public function types($tab_name)
        {

            switch ($tab_name) {

                case 'keywords':
                    $types['key'] = 'top_keywords';
                    $types['statuses'] = array('0' => 'None', 1 => '1 Word', 2 => '2 Words', 3 => '3 Words', 4 => '4 Words', 5 => '5 Words',6 => '6 Words', 7 => '7 Words');
                    break;

                case 'status':
                    $types['key'] = 'internal_by_status';
                    $types['statuses'] = array('all' => 'All', 301 => '301', 302 => '302', 307 => '307', 404 => '404', 500 => '500');
                    break;

                case 'duplication':
                    $types['key'] = 'global_duplicate_';
                    $types['statuses'] = array('title' => 'Titles', 'desc' => 'Description', 'content' => 'Similar content', 'perception' => 'Internal Competition ( Perception )');
                    break;

                case 'resources':
                    $types['key'] = 'resources_by_status';
                    $types['statuses'] = array('all' => 'All', 301 => '301', 302 => '302', 307 => '307', 404 => '404', 500 => '500');
                    break;

                case 'outgoinglinks':
                    $types['key'] = 'outgoing_by_status';
                    $types['statuses'] = array('all' => 'All', 301 => '301', 302 => '302', 307 => '307', 404 => '404', 500 => '500');
                    break;
            }
            return $types;
        }

        // function only used to be parsed poedit
        private function translate_dynamic_mesages()
        {

            $tmp = __('None', 'wpsearchconsole');
            $tmp = __("1 Word", 'wpsearchconsole');
            $tmp = __("2 Words", 'wpsearchconsole');
            $tmp = __("3 Words", 'wpsearchconsole');
            $tmp = __("4 Words", 'wpsearchconsole');
            $tmp = __("5 Words", 'wpsearchconsole');
            $tmp = __("6 Words", 'wpsearchconsole');
            $tmp = __("7 Words", 'wpsearchconsole');
            $tmp = __('All', 'wpsearchconsole');
            $tmp = __("Titles", 'wpsearchconsole');
            $tmp = __("Similar content", 'wpsearchconsole');
            $tmp = __("Internal Competition ( Perception )", 'wpsearchconsole');

        }

    }
}