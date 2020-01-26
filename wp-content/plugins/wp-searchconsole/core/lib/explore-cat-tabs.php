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
if (!class_exists('wpsearchconsole_exploration_categories')) {

    class wpsearchconsole_exploration_categories
    {

        public $assembly;
        public $tab;

        public function __construct($tab)
        {

            $this->tab = $tab;

            $this->types();

            $types = $this->types();
            $assembly = array();
            foreach ($types as $type) {
                $rows = $this->query($type, $this->tab);
                $assembly[$type] = $this->count($rows);

            }

            arsort($assembly);

            $this->assembly = $assembly;
        }

        //count the numbers
        public function count($rows)
        {

            if ($rows && is_array($rows)) {
                $row_count = $rows[0];
                $count = $row_count['COUNT(*)'];
            }
            return ($count ? $count : false);
        }

        //search the db table
        public function query($type, $platform)
        {

            global $wpdb;

            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wpsearchconsole_console WHERE type = '$type' AND platform = '$platform'";
            $get_rows = $wpdb->get_results($sql, ARRAY_A);

            return $get_rows;
        }

        //Declare the types of errors
        public function types()
        {
            //'flashContent',
            $types = array('authPermissions', 'manyToOneRedirect', 'notFollowed', 'notFound', 'other', 'roboted', 'serverError', 'soft404');
            return $types;
        }
    }
}
?>