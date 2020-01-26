<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/widget/
 * on: 24.06.2016
 * @since 0.1
 *
 * Todo widget using db _wpsearchconsole_todo.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_analysis_widget')) {

    class wpsearchconsole_analysis_widget
    {

        private $table_name;
        private $type;
        private $dimension;

        public function __construct($type, $dimension)
        {

            global $wpdb;
            //Google searchconsole
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_json';
            $this->type = $type;
            $this->dimension = $dimension;
        }

        //html output
        public function html()
        {

            $output = $this->tabs($this->type, $this->dimension);
            $output .= $this->panel($this->type, $this->dimension);

            return $output;
        }

        //convert json to array
        public function prepare_table_data($json)
        {

            $list = ($json && is_array($json) && array_key_exists('rows', $json) ? $json['rows'] : false);
            return $list;
        }

        //call the API
        public function call_data($json_ID)
        {

            global $wpdb;

            $value = $wpdb->get_var("SELECT value FROM $this->table_name WHERE json_key = '$json_ID'");

            /*
             return value of json_last_error()
                0 JSON_ERROR_NONE
                1 JSON_ERROR_DEPTH
                2 JSON_ERROR_STATE_MISMATCH
                3 JSON_ERROR_CTRL_CHAR
                4 JSON_ERROR_SYNTAX
                5 JSON_ERROR_UTF8
                6 JSON_ERROR_RECURSION
                7 JSON_ERROR_INF_OR_NAN
                8 JSON_ERROR_UNSUPPORTED_TYPE

             */

            $result = ($value ? json_decode(stripcslashes($value), true) : false);

            return $result;
        }

        //define the tabs
        public function tabs($type, $dimension)
        {

            $data = $this->tab_list($dimension);

            $output = '<ul class="category-tabs">';
            foreach ($data as $val) {
                $output .= '<li id="' . $type . '-' . $val . '" class="wpsearchconsole-tabs ' . ($val == 'mobile' || $val == 'web' ? 'tabs' : 'hide-if-no-js') . '"><a href="#">' . $val . '</a></li>';
            }
            $output .= '</ul>';

            return $output;
        }

        //Filter the data
        public function filter()
        {


        }

        //Display the panel
        public function panel($type, $dimension)
        {

            $data = $this->tab_list($dimension);
            $output = '';

            foreach ($data as $val) {
                $output .= '<div id="' . $type . '-' . $val . '-box" class="wpsearchconsole-tabs-panel tabs-panel">';
                $output .= $this->table($type, $val);
                $output .= '</div>';
            }

            return $output;
        }

        //define the columns
        public function headers($type)
        {

            $data = $this->columns($type);
            $output = '<tr>';

            foreach ($data as $val) :
                $output .= '<th>' . $val . '</th>';
            endforeach;
            $output .= '</tr>';

            return $output;
        }

        //table body
        public function body($data)
        {

            $output = '';

            foreach ($data as $info) :

                $keys = (array_key_exists('keys', $info) ? $info['keys'] : false);

                $output .= '<tr>';
                $output .= '<td>' . (array_key_exists(0, $keys) ? $keys[0] : false) . '</td>';
                $output .= '<td>' . (array_key_exists('clicks', $info) ? $info['clicks'] : false) . '</td>';
                $output .= '<td>' . (array_key_exists('impressions', $info) ? $info['impressions'] : false) . '</td>';
                $output .= '<td>' . (array_key_exists('ctr', $info) ? round($info['ctr'], 2) : false) . '</td>';
                $output .= '<td>' . (array_key_exists('position', $info) ? round($info['position'], 2) : false) . '</td>';
                $output .= '</tr>';

            endforeach;

            return $output;
        }

        //display table
        public function table($type, $dimension)
        {

            $id_string = 'wpsearchconsole_analysis_widget_' . $type . '_' . $dimension . '_ID';
            $ID =md5($id_string);

            $json = $this->call_data($ID);
            $data = $this->prepare_table_data($json);

            if (!$data || !is_array($data)) return __('No Data Available Yet.', 'wpsearchconsole');

            $output = '<table class="widefat striped">';
            $output .= '<thead>' . $this->headers($type) . '</thead>';
            $output .= '<tbody>' . $this->body($data) . '</tbody>';
            $output .= '<tfoot>' . $this->headers($type) . '</tfoot>';
            $output .= '</table>';

            return $output;
        }

        //headers for device dimensions
        public function columns($type)
        {

            return array(
                'requests' => ($type == 'query' ? __('Requests', 'wpsearchconsole') : __('Pages', 'wpsearchconsole')),
                'clicks' => __('Clicks', 'wpsearchconsole'),
                'impression' => __('Impression', 'wpsearchconsole'),
                'ctr' => __('CTR', 'wpsearchconsole'),
                'position' => __('Position', 'wpsearchconsole'),
            );
        }

        //headers for device medium
        public function tab_list($dimension)
        {
            switch ($dimension) {
                case 'device' :
                    return array('mobile', 'desktop', 'tablet');
                case 'medium' :
                    return array('web', 'image', 'video');
            }
        }

        public function jsonDecode($json, $assoc = false)
        {
            $ret = json_decode($json, $assoc);
            if ($error = json_last_error())
            {
                $errorReference = [
                    JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded.',
                    JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON.',
                    JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded.',
                    JSON_ERROR_SYNTAX => 'Syntax error.',
                    JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
                    JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded.',
                    JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded.',
                    JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.',
                ];
                $errStr = isset($errorReference[$error]) ? $errorReference[$error] : "Unknown error ($error)";
                throw new \Exception("JSON decode error ($error): $errStr");
            }
            return $ret;
        }
    }
}
?>