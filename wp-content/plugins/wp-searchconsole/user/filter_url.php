<?php
/**
 *
 * @package: wpsearchconsole/admin/user/lib/metabox/
 * on: 24.06.2016
 * @since 0.1
 *
 * Display Url Filtering.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_filter_url')) {
    // parse url to remove specific actions
    class wpsearchconsole_filter_url
    {

        public $filtered_url;
        public $filtered_query;
        public $parsed_url;
        private $keys2beremoved = array(
            'google-keyword-api-call' => true,
            'metabox' => true,
            'mitambo-api-call' => true,
            'reload-list-data' => true,
            'delete_todo_ID' => true,
            'clear-mitambo-cache' => true,
            'clear-google-cache' => true,
        );

        public function __construct()
        {
            $this->set_default(true);
        }

        //BUG on WP when managing list
        public function filter_url($url)
        {
            $this->parsed_url = parse_url($url);
            if (!array_key_exists('query', $this->parsed_url)) {
                $this->parsed_url['query'] = '';
            }
            parse_str($this->parsed_url['query'], $parsed_query);
            $parsed_query = array_diff_key($parsed_query, $this->keys2beremoved);

            $this->filtered_query = self::map2query($parsed_query);

        }

        // the add2query must not begin with &
        public function render_url_with_newquery($add2query)
        {
            return $this->parsed_url['path'] . $this->filtered_query . (!empty($this->filtered_query) ? '&' : '') . $add2query;
        }

        // return the filtered url
        public function render_url()
        {
            return $this->parsed_url['path'] . $this->filtered_query .
                (array_key_exists('anchor', $this->parsed_url) ? '#' . $this->parsed_url['anchor'] : '');
        }

        //trigger and store current_url
        public function set_default($force = false)
        {
            if (empty($this->filtered_query) or $force) {
                $this->filter_url($_SERVER['REQUEST_URI']);
            }
        }

        // restore the querystring based on the filtered associative array
        private static function map2query($array)
        {
            $str = '';
            foreach ($array as $key => $value) {
                $str .= (strlen($str) < 1) ? "?" : "&";
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $str .= '&' . $key . '=' . rawurlencode($item) ;
                    }
                } else {
                    $str .= $key . '=' . rawurlencode($value);
                }

            }
            return $str;
        }
    }
}
?>