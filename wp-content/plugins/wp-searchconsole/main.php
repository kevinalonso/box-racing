<?php
/**
 *
 * @package: wpsearchconsole/
 * on: 26.10.2018
 * @since 0.1
 *
 * Main plugin.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * The mother object which defines WP search console plugin.
 *
 */
if (!class_exists('wpsearchconsole')) {

    class wpsearchconsole
    {

        public static $sessionFlashKey = 'wpsearchconsole-flash';
        private static $_instance = null;

        public $filtering_url;
        public $filtered_url;
        public $mid_link = array();
        public $mid_id = array();
        public $parent_mid_link = array();

        private function __construct()
        {

            add_action('import_end', array($this, 'parse_mitambo_link'), 100);

            if (is_admin()) {
                //gather every class here
                $this->objects();

                $this->mitambo_json_api = new wpsearchconsole_prepare_mitambo_api();
                $this->google_json_api = new wpsearchconsole_prepare_google_api();
                $this->notices = new wpsearchconsole_notices();
                $this->filtering_url = new wpsearchconsole_filter_url();

                //This is database dependent class, and must be initiated beyond and before init hook.
                $install_db = new wpsearchconsole_install_db($this->mitambo_json_api);
                $this->token = get_option('wpsearchconsole_mitambo');


                /**
                 * If database class is not executed properly, rest of plugin should not be called in.
                 * Because that won't be accessable to user.
                 */
                if (!$install_db) {
                    $this->error();
                } else {
                    add_action('wp_loaded', array($this, 'wpsearchconsole_functionality'));
                    add_action('admin_menu', array($this, 'wpsearchconsole_install'));
                    add_action('admin_menu', array($this, 'wpsearchconsole_update'));
                    add_action('network_admin_menu', array($this, 'wpsearchconsole_install'));
                    add_action('network_admin_menu', array($this, 'wpsearchconsole_update'));
                    add_action('admin_init', array($this, 'wpsc_get_mitambo_call'), 100);
                    add_action('admin_init', array($this, 'wpsc_get_google_call'), 100);
                    add_action('admin_notices', array($this, 'wpsc_display_flash'), 100);
                    add_action('network_admin_notices', array($this, 'wpsc_display_flash'), 100);
                    add_action('wp_ajax_process_todo_taxonomy', array('wpsearchconsole_todo_add_display', 'wpsc_process_taxonomy'));
                    add_action('wp_ajax_process_todo_post', array('wpsearchconsole_todo_add_display', 'process'));
                    add_action('wp_ajax_process_todo_checkbox', array('wpsearchconsole_todo_add_display', 'process_checkbox'));
                    //add_shortcode( 'mitambo_link', 'mitambo_link_func' );
                }
            }
        }

        // return the same singleton instance
        public static function getInstance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new wpsearchconsole();
            }
            return self::$_instance;
        }

        //update the plugin
        public function wpsearchconsole_update()
        {

            $mit_plugin_current_version = WPSEARCHCONSOLE_PLUGIN_VERSION;
            $mit_plugin_remote_path = WPSEARCHCONSOLE_PLUGIN_UPDATE_PATH;
            new wpsearchconsole_auto_update($mit_plugin_current_version, $mit_plugin_remote_path, WPSEARCHCONSOLE_FILE);

            // --- Load I18n ---
            load_plugin_textdomain('wpsearchconsole', false, WPSEARCHCONSOLE_LN_PATH);
        }

        // Initiate required classes
        public function wpsearchconsole_functionality()
        {

            new wpsearchconsole_scripts();
            new wpsearchconsole_general_setting();
            new wpsearchconsole_metabox();
            new wpsearchconsole_tabs();
            new wpsearchconsole_ajax();
        }

        // Get the classes and add
        public function wpsearchconsole_install()
        {
            new wpsearchconsole_initiate();
        }

        //show notice for uninstalled db
        public function error()
        {
            $this->notices->installation_error();
        }

        // WXR import post-processing
        public function parse_mitambo_link()
        {
            global $wpdb;

            $querystr = "SELECT po.id as post_id,po.post_content as post_content,pm1.meta_value as mitambo_id,pm2.meta_value as parent_mitambo_id
			 FROM $wpdb->posts po  
			 left join $wpdb->postmeta pm1 ON po.ID = pm1.post_id
			 left join $wpdb->postmeta pm2 ON po.ID = pm2.post_id
			 WHERE pm1.meta_key = 'mitambo_id' and pm2.meta_key = 'parent_mitambo_id'";

            $results = $wpdb->get_results($querystr, OBJECT);

            $pattern = '\[(\[?)mitambo_link(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
            // loop through all posts and parse MITAMBO Meta Data
            foreach ($results as $key => $row) {
                $this->mid_link[$row->mitambo_id] = get_permalink($row->post_id);
                $this->mid_id[$row->mitambo_id] = $row->post_id;

            }
            // loop through all posts and assign parents- separated from the first one because order in xml is not garantee
            foreach ($results as $key => $row) {
                $parent = null;
                if (isset($this->mid_link[$row->parent_mitambo_id])) {
                    $parent = $this->mid_link[$row->parent_mitambo_id];
                }
                $this->parent_mid_link[$row->parent_mitambo_id] = $parent;
            }


            foreach ($results as $key => $post) {
                $content = preg_replace_callback('/' . $pattern . '/is', array($this, 'autoembed_mitambo_link'), $post->post_content);
                $parent = null;
                if (isset($this->mid_id[$post->parent_mitambo_id])) {
                    $parent = $this->mid_id[$post->parent_mitambo_id];
                }
                $wpdb->update($wpdb->posts, array('post_content' => $content, 'post_parent' => $parent), array('id' => $post->post_id));
            }

        }

        function autoembed_mitambo_link($match)
        {
            return $this->mitambo_link_func(shortcode_parse_atts($match[2]));
        }

        // [mitambo_link id="ID_413030659" label="Cliquez ici"]
        function mitambo_link_func($atts)
        {
            return '<a href="' . $this->mid_link[$atts['id']] . '">' . $atts['label'] . '</a>';
        }


        public function wpsc_get_google_call()
        {
            if (isset($_GET['page']) && sanitize_text_field($_GET['page']) == 'wpsearchconsole-analysis') {

                if (isset($_POST['wpsearchconsole_apply_analysis_filter'])) {

                    $wpatd = new wpsearchconsole_analysis_top_display(false);
                    if ($wpatd->process('wpsearchconsole_apply_analysis_filter')) {
                        wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Google data', 'wpsearchconsole'));
                    } else {
                        wpsearchconsole::getInstance()->setFlash('error', __('Check Google configuration', 'wpsearchconsole'));
                    }
                }

            }
        }

        public function wpsc_get_mitambo_call()
        {

            $page = isset($_REQUEST['page']) ? sanitize_text_field(urldecode($_REQUEST['page'])) : null;

            if (isset($_GET['clear-mitambo-cache']) && sanitize_text_field($_GET['clear-mitambo-cache']) == 'true') {

                if ($page != 'wpsearchconsole') {
                    return;
                }

                $this->mitambo_json_api->resetCache();

                wpsearchconsole::getInstance()->setFlash('success', __('Successfully clean Mitambo data', 'wpsearchconsole'), true);
                //TODO keep the anchor or fragment in querystring "HASH IS: #".explode( "#", $url )[1]
                if (wp_redirect(remove_query_arg('clear-mitambo-cache'))) {
                    exit;
                }

            }


            if (isset($_GET['reload-crawl-error']) && sanitize_text_field($_GET['reload-crawl-error']) == 'true') {

                if (!$this->token) {
                    $this->notices->auth_google_notify();
                    return;
                }

                if ($page != 'wpsearchconsole-explore') {
                    return;
                }

                $wpace = new wpsearchconsole_api_call_exploration_error();

                if (!$wpace->errors) {
                    wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Google data', 'wpsearchconsole'), true);
                } else {
                    wpsearchconsole::getInstance()->setFlash('error', __($wpace->errors[0], 'wpsearchconsole'), true);
                }

                if (wp_redirect(remove_query_arg('reload-crawl-error'))) {
                    exit;
                }
            }

            if (isset($_GET['reload-list-data']) && sanitize_text_field($_GET['reload-list-data']) == 'true') {

                if (!$this->token) {
                    $this->notices->auth_mitambo_notify();
                    return;
                }

                if ($page != 'wpsearchconsole-data') {
                    return;
                }

                $wpmlodac = new wpsearchconsole_mitambo_list_of_data_api_call();

                if (!$wpmlodac->errors) {
                    wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Mitambo data', 'wpsearchconsole'), true);
                } else {
                    wpsearchconsole::getInstance()->setFlash('error', __($wpmlodac->errors[0], 'wpsearchconsole'), true);
                }
                // Update last refreshing date
                update_option('wpsearchconsole_last_crawled_list_of_data', time());

                if (wp_redirect(remove_query_arg('reload-list-data'))) {
                    exit;
                }
            }

            if (isset($_GET['mitambo-api-call']) && sanitize_text_field($_GET['mitambo-api-call']) == 'true') {

                if (!$this->token) {
                    $this->notices->auth_mitambo_notify();
                    return;
                }

                if (isset($_GET['post'])) {
                    $postid = absint($_GET['post']);
                    //return;
                    $permalink = get_permalink($postid);
                    //$permalink = $this->wpsc_repair_protocol($permalink);
                    $permalink = wpsc_repair_protocol($permalink);

                    $mitambo_api = new wpsearchconsole_mitambo_api_call($permalink, $postid);
                    $mitambo_api->save_response_data('post');

                    if (!$mitambo_api->errors) {
                        wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Mitambo data', 'wpsearchconsole'), true);
                    } else {
                        wpsearchconsole::getInstance()->setFlash('error', __($mitambo_api->errors[0], 'wpsearchconsole'), true);
                    }
                }

                if (isset($_GET['taxonomy']) && isset($_GET['tag_ID'])) {
                    $tagid = absint($_GET['tag_ID']);
                    $permalink = get_term_link($tagid);
                    //$permalink = $this->wpsc_repair_protocol($permalink);
                    $permalink = wpsc_repair_protocol($permalink);
                    $mitambo_api = new wpsearchconsole_mitambo_api_call($permalink, $tagid);
                    $mitambo_api->save_response_data('taxonomy');

                    if (!$mitambo_api->errors) {
                        wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Mitambo data', 'wpsearchconsole'), true);
                    } else {
                        wpsearchconsole::getInstance()->setFlash('error', __($mitambo_api->errors[0], 'wpsearchconsole'), true);
                    }
                }

                if (wp_redirect(remove_query_arg('mitambo-api-call'))) {
                    exit;
                }
            }

            if (isset($_GET['google-keyword-api-call']) && sanitize_text_field($_GET['google-keyword-api-call']) == 'true') {

                //if( !$this->token ) { $this->notices->api_call_error(); return; }

                if (!isset($_GET['post']) && !isset($_GET['taxonomy'])) {
                    return;
                }

                if (isset($_GET['taxonomy']) && isset($_GET['tag_ID'])) {
                    $tagid = absint($_GET['tag_ID']);
                    $permalink = get_term_link($tagid);
                    $type = 'term';
                    $ID = $tagid;
                } else {
                    $postid = absint($_GET['post']);
                    $permalink = get_permalink($postid);
                    $type = 'post';
                    $ID = $postid;
                }
                //$permalink = $this->wpsc_repair_protocol($permalink);
                $permalink = wpsc_repair_protocol($permalink);
                if ($permalink) {
                    $parsed = parse_url($permalink);
                    //See how the post ID is taken here, because get_the_ID() will return false as called in init hook
                    $wpackpp = new wpsearchconsole_api_call_keywords_per_page($parsed['path'], $ID, $type);

                    if (!$wpackpp->errors) {
                        wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Google data', 'wpsearchconsole'), true);
                    } else {
                        wpsearchconsole::getInstance()->setFlash('error', __($wpackpp->errors[0], 'wpsearchconsole'), true);
                    }
                }

                if (wp_redirect(remove_query_arg('google-keyword-api-call'))) {
                    exit;
                }
            }

            if (isset($_GET['reload-widget-data']) && sanitize_text_field($_GET['reload-widget-data']) == 'true') {

                if (!$this->token) {
                    $this->notices->auth_mitambo_notify();
                    return;
                }

                if ($page != 'wpsearchconsole-dash') {
                    return;
                }

                $wpacaw = new wpsearchconsole_api_call_analysis_widget();
                $wpacdw = new wpsearchconsole_api_call_dashboard_widget();

                if (!$wpacdw->errors) {
                    wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Mitambo data', 'wpsearchconsole'), true);
                    update_option('wpsearchconsole_last_crawled_widget', time());
                } else {
                    wpsearchconsole::getInstance()->setFlash('error', __($wpacdw->errors[0], 'wpsearchconsole'), true);
                }
                if (!$wpacaw->errors) {
                    wpsearchconsole::getInstance()->setFlash('success', __('Successfully refreshed Google data', 'wpsearchconsole'), true);
                } else {
                    wpsearchconsole::getInstance()->setFlash('error', __($wpacaw->errors[0], 'wpsearchconsole'), true);
                }

                if (wp_redirect(remove_query_arg('reload-widget-data'))) {
                    exit;
                }

            }

            if (isset($_GET['wpsearchconsole-csv']) && sanitize_text_field($_GET['wpsearchconsole-csv']) == 'true') {

                $csv = array('name' => false, 'headings' => false, 'data' => false);
                $csv = new wpsearchconsole_csv_handler();

                if ($page == 'wpsearchconsole-explore') {
                    $csv->vars('explore');
                }

                if ($page == 'wpsearchconsole-analysis') {
                    $csv->vars('analysis');
                }

                if ($page == 'wpsearchconsole-todo') {
                    $csv->vars('todo');
                }
                if (wp_redirect(remove_query_arg('wpsearchconsole-csv'))) {
                    exit;
                }
            }

        }

        //add the objects
        public function objects()
        {

            // Add the functionality
            require_once WPSEARCHCONSOLE_DB_PATH . 'db.php';
            require_once WPSEARCHCONSOLE_CORE_PATH . 'core.php';
            require_once WPSEARCHCONSOLE_USER_PATH . 'user.php';
            require_once WPSEARCHCONSOLE_ADMIN_PATH . 'admin.php';
            require_once WPSEARCHCONSOLE_PATH . 'lib/WP_Persistent_Notices.php';
            /**
             * VERY IMPORTANT: Database creation requires db.php So sequence matters here.
             */

            //Install the plugin
            require_once WPSEARCHCONSOLE_ADMIN_PATH . 'initiate.php';
            require_once WPSEARCHCONSOLE_DB_PATH . 'initiate.php';

        }

        public function wpsc_display_flash()
        {

            if ($messages = wpsearchconsole::getFlash()) {
                $new = array();
                foreach ($messages as $message) {
                    $new[serialize($message)] = $message;
                }
                $messages = array_values($new);
                add_action('admin_notices', function () use ($messages) {
                    foreach ($messages as $message) {
                        $className = 'notice-' . $message['type'];
                        ?>
                        <div class="notice <?php echo $className ?> is-dismissible">
                            <p>WP Search Console: <?php echo $message['message'] ?></p>
                        </div>
                        <?php
                    }
                });

            }
        }

        public static function cleanFlash()
        {
            $_SESSION[wpsearchconsole::$sessionFlashKey] = array();
        }

        public function getFlash()
        {
            if (isset($_SESSION[wpsearchconsole::$sessionFlashKey])) {
                $messages = $_SESSION[wpsearchconsole::$sessionFlashKey];
                wpsearchconsole::cleanFlash();
                return $messages;
            }
            return array();
        }

        public function hasPersistentError()
        {

        }

        /**
         * @type error, warning, success, info
         *
         **/
        public function setFlash($type, $message, $redirect = false, $location = 'default')
        {
            if ($redirect) {
                add_persistent_notice(array('type' => $type, 'message' => $message), $location);
            } else {
                if (!isset($_SESSION[wpsearchconsole::$sessionFlashKey])) {
                    $_SESSION[wpsearchconsole::$sessionFlashKey] = array();
                }
                $_SESSION[wpsearchconsole::$sessionFlashKey][] = array('type' => $type, 'message' => $message);
            }

        }

    }
}
?>