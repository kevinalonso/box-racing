<?php
/**
 *
 * @package: wpsearchconsole/db/lib/
 * on: 24.05.2015
 * @since 0.1
 *
 * Initiate database.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!function_exists('dbDelta')) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
}

/**
 *
 * Define the base class for menu and settings
 *
 */
if (!class_exists('wpsearchconsole_initiate_db')) {

    class wpsearchconsole_initiate_db
    {

        private $sql;
        private $capability;

        public function __construct()
        {

            $this->capability = 'wpsearchconsole_user';
            $this->sql = new wpsearchconsole_create_sql();
        }

        //remove user $capability
        public function user_capability_uninstall()
        {

            $roles = get_editable_roles();
            foreach ($roles as $key => $val) {

                $role = get_role($key);
                $role->remove_cap($this->capability);

            }
            delete_option('wpsearchconsole_capability');
        }

        //add user $capability
        public function user_capability_install()
        {

            update_option('wpsearchconsole_capability', $this->capability);

            $role = get_role('administrator');
            $role->add_cap($this->capability);
        }

        //client details saving
        public function client_details_uninstall()
        {

            delete_option('wpsearchconsole_client_ID');
            delete_option('wpsearchconsole_client_secret');
        }

        //Detele usage data
        public function user_data_uninstall()
        {

            delete_option('wpsearchconsole_google');

            delete_option('wpsearchconsole_google_token');
            delete_option('wpsearchconsole_google_expiry');
            delete_option('wpsearchconsole_google_refresh_token');

            delete_option('wpsearchconsole_selected_site');

            delete_option('wpsearchconsole_analysis_param');
            delete_option('wpsearchconsole_analysis_value');
            delete_option('wpsearchconsole_analysis_point');

            delete_option('wpsearchconsole_analysis_clicks');
            delete_option('wpsearchconsole_analysis_impressions');
            delete_option('wpsearchconsole_analysis_ctr');
            delete_option('wpsearchconsole_analysis_position');

            delete_option('wpsearchconsole_mitambo');
            delete_option('wpsearchconsole_selected_site');

            delete_option('wpsearchconsole_todo_categories');
            delete_option('wpsearchconsole_todo_priority');
        }

        //client details saving
        public function client_details()
        {

            $client_ID = '169750596647-sremenn0o2sknvg1sgue4kc48laa8v4v.apps.googleusercontent.com';
            $client_secret = 'orpfslUfKK6Dz_GdhD2JGVkX';

            update_option('wpsearchconsole_client_ID', $client_ID);
            update_option('wpsearchconsole_client_secret', $client_secret);
        }

        //todo list details
        public function todo_details()
        {

            $cats = array(
                0 => __('All Categories', 'wpsearchconsole'),
                1 => __('Keywords', 'wpsearchconsole'),
                2 => __('Links', 'wpsearchconsole'),
                3 => __('Duplication', 'wpsearchconsole'),
            );
            update_option('wpsearchconsole_todo_categories', $cats);

            $priority = array(
                0 => __('All Priorities', 'wpsearchconsole'),
                1 => __('Low', 'wpsearchconsole'),
                2 => __('Medium', 'wpsearchconsole'),
                3 => __('High', 'wpsearchconsole'),
            );
            update_option('wpsearchconsole_todo_priority', $priority);

            $archived = array(
                0 => __('Not Archived', 'wpsearchconsole'),
                1 => __('Archived', 'wpsearchconsole'),

            );
            update_option('wpsearchconsole_todo_archived', $archived);
        }

        //update version information
        public function version()
        {

            $new_version = WPSEARCHCONSOLE_PLUGIN_VERSION;
            if (get_option('wpsearchconsole_version') != $new_version) {
                update_option('wpsearchconsole_version', $new_version);
            }
        }

        //Define the variables
        public function collate()
        {

            global $wpdb;

            $collate = '';
            if ($wpdb->has_cap('collation')) {
                if (!empty($wpdb->charset)) {
                    $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
                }

                if (!empty($wpdb->collate)) {
                    $collate .= " COLLATE $wpdb->collate";
                }

            }

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            return $collate;
        }

        /**
         *
         * Define the necessary database tables
         *
         */

        public function cache()
        {

            global $wpdb;

            $wpdb->hide_errors();
            $collate = $this->collate();
            $table_name = $wpdb->prefix . "wpsearchconsole_cache";

            $sql_get_index = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE table_schema = "
                . DB_NAME . " AND table_name = $table_name AND index_name = md5_idx;";

            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                if ($wpdb->get_var($sql_get_index) > 0) {
                    $sql = "ALTER TABLE " . $table_name . " DROP INDEX md5_idx";
                    $wpdb->query($sql);
                }
                $sql = $this->sql->cache($table_name, $collate);
                dbDelta($sql);
            }
        }

        public function console()
        {

            global $wpdb;

            $wpdb->hide_errors();
            $collate = $this->collate();

            $table_name = $wpdb->prefix . "wpsearchconsole_console";
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $sql = $this->sql->console($table_name, $collate);
                dbDelta($sql);
            }
        }

        public function visitors()
        {

            global $wpdb;

            $wpdb->hide_errors();
            $collate = $this->collate();

            $table_name = $wpdb->prefix . "wpsearchconsole_visitors";
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $sql = $this->sql->visitors($table_name, $collate);
                dbDelta($sql);
            }
        }

        public function json()
        {

            global $wpdb;

            $wpdb->hide_errors();
            $collate = $this->collate();

            $table_name = $wpdb->prefix . "wpsearchconsole_json";
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $sql = $this->sql->json($table_name, $collate);
                dbDelta($sql);
            } // This else part needs to be remove in next release (current : 0.8.20)
            else {
                // Change datatype of column value text to mediumtext
                $wpdb->query("ALTER TABLE $table_name modify value MEDIUMTEXT not null");
            }
        }

        public function todo()
        {

            global $wpdb;

            $wpdb->hide_errors();
            $collate = $this->collate();

            $table_name = $wpdb->prefix . "wpsearchconsole_todo";

            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name' ") != $table_name) {
                $sql = $this->sql->todo($table_name, $collate);
                dbDelta($sql);
            }
        }

        public function migration()
        {
            global $wpdb;

            $wpdb->hide_errors();
            $table_name = $wpdb->prefix . "wpsearchconsole_todo";
            if (version_compare(get_option('wpsearchconsole_version'), '0.8.41', '<')) {
                $sql = $this->sql->migrate_todo($table_name);
                if (strlen($sql) > 0){
                    $wpdb->query($sql);
                }
                //update version
                $this->version();
            }

        }

        public function data()
        {

            global $wpdb;

            $wpdb->hide_errors();
            $collate = $this->collate();

            $table_name = $wpdb->prefix . "wpsearchconsole_data";
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $sql = $this->sql->data($table_name, $collate);
                dbDelta($sql);
            }
        }

    }
}
?>