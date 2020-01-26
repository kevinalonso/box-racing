<?php
/**
 *
 * @package: wpsearchconsole/user/lib/settings/
 * on: 30.09.2016
 * @since 0.8.18
 *
 * Display debug information to user
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to display all debug info
 */
if (!class_exists('wpsearchconsole_display_debug_info')) {
    class wpsearchconsole_display_debug_info
    {
        function __construct()
        {
            $mitambo_json_api = new wpsearchconsole_prepare_mitambo_api(true);
            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
            list($md5, $called_url) = $mitambo_json_api->generate_md5($this->lastCollectDateArray ,'crawler/status');
            $discard = $mitambo_json_api->request($called_url, $md5, array(), false);
            //$json_api->wpsearchconsole_update_mitambo_crawled_date();

            $this->access_token = get_option('wpsearchconsole_mitambo');
            $this->open_wpsc_container();
            $this->display_debug_info();
            $this->close_wpsc_container();
        }

        function __destruct()
        {
        }
        public function open_wpsc_container(){
            ?>
            <div id="wpsc_container" class="wpsc">
            <?php
        }
        public function close_wpsc_container(){
            ?>
            </div>
            <?php
        }
        function display_debug_info()
        {
            $debug_data = $this->get_debug_info();
            ?>
            <div class="inside">
                <p><?php _e('This information allows our support team to see the versions of WordPress, plugins and theme on your site. Provide this information if requested in our support forum. No passwords or other confidential information is included.', 'wpsearchconsole') ?></p>
                <br/>
                <textarea style="width:100%;line-height: 22px; font-size: 12px;" rows="26"
                          readonly="readonly"><?php echo esc_html($this->do_json_encode($debug_data)); ?></textarea>
            </div>
            <h5>Debug Log</h5>
            <div class="inside">
                <?php $log_filename = __DIR__ . '/../../../debug.log';
                if (file_exists($log_filename)) {
                    echo file_get_contents(__DIR__ . '/../../../debug.log');
                }
                ?>
            </div>
            <?php
        }

        function get_debug_info($info = array())
        {
            if (!is_array($info)) {
                $info = explode(',', $info);
            }
            if (empty($info)) {
                $info = array('core', 'plugins', 'theme', 'extra-debug');
            }

            $output = array();
            foreach ($info as $type) {
                switch ($type) {
                    case 'core':
                        $output['core'] = $this->get_core_info();
                        break;
                    case 'plugins':
                        $output['plugins'] = $this->get_plugins_info();
                        break;
                    case 'theme':
                        $output['theme'] = $this->get_theme_info();
                        break;
                    case 'extra-debug':
                        $output['extra-debug'] = apply_filters('icl_get_extra_debug_info', array());
                        break;
                }
            }
            return $output;
        }

        /**
         *
         * @global object $wpdb
         *
         */
        function get_core_info()
        {

            global $wpdb;

            $jquery_ver = __('n/a', 'wpv-views');
            if (wp_script_is('jquery', 'registered')) {
                $reg_scripts = $GLOBALS['wp_scripts']->registered;
                $jquery_ver = $reg_scripts['jquery']->ver;
            }

            $core = array(
                'WPSC' => array(
                    'Jwt' => WPSEARCHCONSOLE_PLUGIN_JWT_API,
                    'Api' => WPSEARCHCONSOLE_PLUGIN_API,
                    'App' => WPSEARCHCONSOLE_PLUGIN_APP,
                    'Update' => WPSEARCHCONSOLE_PLUGIN_UPDATE_PATH,
                    'Version' => WPSEARCHCONSOLE_PLUGIN_VERSION,
                    'Debug' => WPSEARCHCONSOLE_DEBUG,
                    'Path' => WPSEARCHCONSOLE_PATH,
                    'CollectDate' => $this->lastCollectDateArray,
                    'Mitambo_token' => $this->access_token,
                ),
                'Wordpress' => array(
                    'Multisite' => is_multisite() ? 'Yes' : 'No',
                    'SiteURL' => site_url(),
                    'HomeURL' => home_url(),
                    'Version' => get_bloginfo('version'),
                    'PermalinkStructure' => get_option('permalink_structure'),
                    'PostTypes' => implode(', ', get_post_types('', 'names')),
                    'PostSatus' => implode(', ', get_post_stati()),
                ),
                'Server' => array(
                    'jQueryVersion' => $jquery_ver,
                    'PHPVersion' => phpversion(),
                    'MySQLVersion' => $wpdb->db_version(),
                    'ServerSoftware' => getenv('SERVER_SOFTWARE'),
                ),
                'PHP' => array(
                    'MemoryLimit' => ini_get('memory_limit'),
                    'UploadMax' => ini_get('upload_max_filesize'),
                    'PostMax' => ini_get('post_max_size'),
                    'TimeLimit' => ini_get('max_execution_time'),
                    'MaxInputVars' => ini_get('max_input_vars'),
                ),
            );

            return $core;
        }

        function get_plugins_info()
        {

            if (!function_exists('get_plugins')) {
                $admin_includes_path = str_replace(site_url('/', 'admin'), ABSPATH, admin_url('includes/', 'admin'));
                require_once $admin_includes_path . 'plugin.php';
            }

            $plugins = get_plugins();
            $active_plugins = get_option('active_plugins');
            $active_plugins_info = array();
            foreach ($active_plugins as $plugin) {
                if (isset($plugins[$plugin])) {
                    unset($plugins[$plugin]['Description']);
                    $active_plugins_info[$plugin] = $plugins[$plugin];
                }
            }

            $mu_plugins = get_mu_plugins();

            $dropins = get_dropins();

            $output = array(
                'active_plugins' => $active_plugins_info,
                'mu_plugins' => $mu_plugins,
                'dropins' => $dropins,
            );

            return $output;
        }

        function get_theme_info()
        {

            if (get_bloginfo('version') < '3.4') {
                $current_theme = get_theme_data(get_stylesheet_directory() . '/style.css');
                $theme = $current_theme;
                unset($theme['Description']);
                unset($theme['Status']);
                unset($theme['Tags']);
            } else {
                $current_theme = wp_get_theme();
                $theme = array(
                    'Name' => $current_theme->Name,
                    'ThemeURI' => $current_theme->ThemeURI,
                    'Author' => $current_theme->Author,
                    'AuthorURI' => $current_theme->AuthorURI,
                    'Template' => $current_theme->Template,
                    'Version' => $current_theme->Version,
                    'TextDomain' => $current_theme->TextDomain,
                    'DomainPath' => $current_theme->DomainPath,
                );
            }

            return $theme;
        }

        function do_json_encode($data)
        {
            if (version_compare(phpversion(), '5.3.0', '<')) {
                return json_encode($data);
            }
            $json_options = 0;
            if (defined('JSON_HEX_TAG')) {
                $json_options += JSON_HEX_TAG;
            }
            if (defined('JSON_HEX_APOS')) {
                $json_options += JSON_HEX_APOS;
            }
            if (defined('JSON_HEX_QUOT')) {
                $json_options += JSON_HEX_QUOT;
            }
            if (defined('JSON_HEX_AMP')) {
                $json_options += JSON_HEX_AMP;
            }
            if (defined('JSON_UNESCAPED_UNICODE')) {
                $json_options += JSON_UNESCAPED_UNICODE;
            }
            if (defined('JSON_PRETTY_PRINT')) {
                $json_options += JSON_PRETTY_PRINT;
            }
            return json_encode($data, $json_options);
        }


    }

}
