<?php
/**
 *
 * @package: wpsearchconsole/admin/obj/
 * on: 25.05.2016
 * @since 0.1
 * @modified: 2
 *
 * Add settings pages for JWT and oAuth authentication and display of data.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_general_setting')) {

    class wpsearchconsole_general_setting
    {

        private $capability;
        private $help;

        //Add basic actions for menu and settings
        public function __construct()
        {

            $this->capability = get_option('wpsearchconsole_capability');

            //Initiate the settings fields to be called in.
            new wpsearchconsole_settings_fields();

            add_filter('set-screen-option', array($this, 'set_screen'), 10, 3);

            add_action('admin_menu', array($this, 'wpsearchconsole_menu_page'));

            add_action('admin_menu', array($this, 'wpsearchconsole_submenu_page_settings'));
            add_action('admin_menu', array($this, 'wpsearchconsole_dashboard_submenu_page'));
            add_action('admin_menu', array($this, 'wpsearchconsole_submenu_page_data'));
            add_action('admin_menu', array($this, 'wpsearchconsole_submenu_page_analysis'));
            add_action('admin_menu', array($this, 'wpsearchconsole_submenu_page_explore'));
            add_action('admin_menu', array($this, 'wpsearchconsole_submenu_page_todos'));
            add_action('admin_menu', array($this, 'wpsearchconsole_dashboard_page'));

            add_action('network_admin_menu', array($this, 'wpsearchconsole_menu_page'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_submenu_page_settings'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_dashboard_submenu_page'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_submenu_page_data'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_submenu_page_analysis'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_submenu_page_explore'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_submenu_page_todos'));
            add_action('network_admin_menu', array($this, 'wpsearchconsole_dashboard_page'));
            //
        }

        //submenu callback
        public function wpsearchconsole_menu_page()
        {

            $hook = add_menu_page(
                __('Settings WP Search Console', 'wpsearchconsole'),
                __('Searchconsole', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole',
                array($this, 'wpsearchconsole_submenu_page_auth_callback'));
            add_action('load-' . $hook, array($this, 'wpsearchconsole_help_tabs'));
        }

        //submenu callback
        public function wpsearchconsole_submenu_page_settings()
        {

            $hook = add_submenu_page(
                'wpsearchconsole',
                __('Settings WP Search Console', 'wpsearchconsole'),
                __('Settings', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole',
                array($this, 'wpsearchconsole_submenu_page_auth_callback'));
            add_action('load-' . $hook, array($this, 'wpsearchconsole_help_tabs'));
        }

        //submenu callback
        public function wpsearchconsole_submenu_page_data()
        {

            $hook = add_submenu_page(
                'wpsearchconsole',
                __('List of Data', 'wpsearchconsole'),
                __('List of Data', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-data',
                array($this, 'wpsearchconsole_submenu_page_data_callback'));

            add_action("load-$hook", array($this, 'wpsearchconsole_data_page_screen_option'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }

        //submenu callback
        public function wpsearchconsole_submenu_page_analysis()
        {

            $hook = add_submenu_page(
                'wpsearchconsole',
                __('Analysis Search', 'wpsearchconsole'),
                __('Analysis Search', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-analysis',
                array($this, 'wpsearchconsole_submenu_page_analysis_callback'));
            add_action("load-$hook", array($this, 'wpsearchconsole_visitors_screen_option'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }

        //submenu callback
        public function wpsearchconsole_submenu_page_explore()
        {

            $hook = add_submenu_page(
                'wpsearchconsole',
                __('Exploration', 'wpsearchconsole'),
                __('Exploration', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-explore',
                array($this, 'wpsearchconsole_submenu_page_explore_callback'));
            add_action("load-$hook", array($this, 'wpsearchconsole_console_screen_option'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }

        public function wpsearchconsole_submenu_page_svg_charts()
        {
            $hook = add_submenu_page(
                'wpsearchconsole',
                __('Reports', 'wpsearchconsole'),
                __('Reports', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-svg-charts',
                array($this, 'wpsearchconsole_submenu_page_svg_charts_callback'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }

        //submenu callback
        public function wpsearchconsole_submenu_page_todos()
        {
            $hook = add_submenu_page(
                'wpsearchconsole',
                __('Todo Lists', 'wpsearchconsole'),
                __('Todo Lists', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-todo',
                array($this, 'wpsearchconsole_submenu_page_todos_callback'));
            add_action("load-$hook", array($this, 'wpsearchconsole_todos_screen_option'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }

        public function wpsearchconsole_dashboard_submenu_page()
        {

            $hook = add_submenu_page(
                'wpsearchconsole',
                __('Dashboard', 'wpsearchconsole'),
                __('Dashboard', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-dash',
                array($this, 'wpsearchconsole_dashboard_page_callback'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }

        //
        public function wpsearchconsole_dashboard_page()
        {

            $hook = add_submenu_page(
                'index.php',
                __('SearchConsole', 'wpsearchconsole'),
                __('SearchConsole', 'wpsearchconsole'),
                $this->capability,
                'wpsearchconsole-dash',
                array($this, 'wpsearchconsole_dashboard_page_callback'));
            add_action("load-$hook", array($this, 'wpsearchconsole_help_tabs'));
        }


        //Get the screen data
        public static function set_screen($status, $option, $value)
        {

            return $value;
        }

        //set screen options to all url pages
        public function wpsearchconsole_console_screen_option()
        {

            $option = 'per_page';
            $args = array(
                'label' => __('Show Search Console Errors per page', 'wpsearchconsole'),
                'default' => 10,
                'option' => 'console_logs_per_page',
            );
            $this->console_obj = new Console_List();
            add_screen_option($option, $args);
        }

        //set screen options to all url pages
        public function wpsearchconsole_visitors_screen_option()
        {

            $option = 'per_page';
            $args = array(
                'label' => __('Show Vistors Logs per page', 'wpsearchconsole'),
                'default' => 10,
                'option' => 'vistors_logs_per_page',
            );
            add_screen_option($option, $args);
            $this->visitors_obj = new Vistors_List();
        }

        //Todo screen options
        public function wpsearchconsole_todos_screen_option()
        {

            $option = 'per_page';
            $args = array(
                'label' => __('Show Todo data per page', 'wpsearchconsole'),
                'default' => 10,
                'option' => 'todo_logs_per_page',
            );
            add_screen_option($option, $args);
            $this->todos_obj = new Actions_List();
        }

        //Data screen options
        public function wpsearchconsole_data_page_screen_option()
        {

            $option = 'per_page';
            $args = array(
                'label' => __('Show Data data per page', 'wpsearchconsole'),
                'default' => 10,
                'option' => 'data_logs_per_page',
            );
            add_screen_option($option, $args);
            $this->listofdata_obj = new List_of_Data();
        }

        //the add authenticate page
        public function wpsearchconsole_submenu_page_auth_callback()
        {
            ?>

            <div class="wrap">
                <h2><?php _e('Plugin\'s Settings', 'wpsearchconsole'); ?></h2>
                <?php settings_errors(); ?>
                <?php $tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'mitambo');
                $this->wpsearchconsole_settings_auth_tabs($tab);
                if ($tab == 'mitambo'):
                    $mitamboo = new wpsearchconsole_mitambo_auth_display();
                elseif ($tab == 'google'):
                    $google = new wpsearchconsole_google_auth_display();
                elseif ($tab == 'roles'):
                    $roles = new wpsearchconsole_role_display();
                elseif ($tab == 'support'):
                    $roles = new wpsearchconsole_display_support();
                elseif ($tab == 'debug'):
                    $roles = new wpsearchconsole_display_debug_info();
                elseif ($tab == 'debugmitapi'):
                    $token = isset($_POST['BearerToken']) ? sanitize_text_field($_POST['BearerToken']) : null;
                    $apicall = isset($_POST['api-call']) ? sanitize_text_field(urldecode($_POST['api-call'])) : null;
                    $url = isset($_POST['X-Mitambo-Url']) ? sanitize_text_field($_POST['X-Mitambo-Url']) : null;
                    $start = isset($_POST['X-Mitambo-Start']) ? sanitize_text_field(urldecode($_POST['X-Mitambo-Start'])) : null;
                    $limit = isset($_POST['X-Mitambo-Limit']) ? sanitize_text_field(urldecode($_POST['X-Mitambo-Limit'])) : null;
                    $roles = new wpsearchconsole_display_debug_mitapi($apicall, $token, $url, $start, $limit);
                endif; ?>
                <br class="clear">
            </div>
            <?php
        }

        //the all console data page
        public function wpsearchconsole_submenu_page_data_callback()
        {
            ?>

            <div class="wrap">
                <?php $tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 1); ?>
                <h2><?php _e('List of Data to display', 'wpsearchconsole'); ?>
                    &nbsp;<?php $this->websocket(__('Reload Data', 'wpsearchconsole'), 'reload-list-data', $_GET['page'], $tab); ?>
                    &nbsp;<?php echo $this->get_LastCollectDate() . ' - ' . $this->time_gap('wpsearchconsole_last_crawled_list_of_data'); ?></h2>
                </h2>
                <?php settings_errors(); ?>
                <?php $tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '1');
                new wpsearchconsole_data_top_display($tab); ?>

                <form action="" method="post">
                    <?php $this->listofdata_obj->prepare_items();
                    $this->listofdata_obj->display();

                    ?>
                </form>
                <br class="clear">
            </div>
            <?php
        }

        //the all analysis data page
        public function wpsearchconsole_submenu_page_analysis_callback()
        {
            ?>

            <div class="wrap">
                <h2><?php _e('Analysis of Searches', 'wpsearchconsole'); ?></h2>
                <?php settings_errors();
                new wpsearchconsole_analysis_top_display(); ?>

                <form action="" method="post">
                    <?php
                    //Show table of visits
                    $this->visitors_obj->prepare_items();
                    $this->visitors_obj->display(); ?>
                </form>
                <?php $this->download('analysis', 'wpsearchconsole-csv'); ?>
            </div>
            <?php
        }

        //the all console data page
        public function wpsearchconsole_submenu_page_explore_callback()
        {
            ?>

            <div class="wrap">

                <?php $tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'web'); ?>
                <h2><?php _e('Exploration from Google bots', 'wpsearchconsole'); ?>
                    &nbsp;<?php $this->websocket(__('Reload Crawl Data', 'wpsearchconsole'), 'reload-crawl-error', $_GET['page'], $tab); ?>
                    <span class="description">
				<?php foreach (array('web', 'smartphoneOnly') as $mytab): ?>
                    &nbsp;<?php echo $mytab . ": " . $this->time_gap("wpsearchconsole_last_crawled_{$mytab}_errors"); ?>
                <?php endforeach; ?>
				</span>
                </h2>
                <?php settings_errors();
                new wpsearchconsole_explore_top_display($tab); ?>
                <br class="clear">
                <form action="" method="post">
                    <?php $this->console_obj->prepare_items();
                    $this->console_obj->display(); ?>
                </form>
                <?php $this->download('explore', 'wpsearchconsole-csv'); ?>
            </div>
            <?php
        }

        // Reports page of SVG charts
        public function wpsearchconsole_submenu_page_svg_charts_callback()
        {
            $find = array('http://', 'https://');
            $siteUrl = site_url();
            $replace = '';
            $siteDomain = str_replace($find, $replace, $siteUrl);

            ?>
            <div class="wrap">
                <h1><?php echo __('Mitambo global report for ' . $siteDomain, 'wpsearchconsole'); ?></h1>

                <?php new wpsearchconsole_svg_charts_reports(); ?>
            </div>
            <?php

        }

        //the all console data page
        public function wpsearchconsole_submenu_page_todos_callback()
        {
            ?>

            <div class="wrap">
                <h2><?php _e('ToDo', 'wpsearchconsole'); ?></h2>
                <?php settings_errors();
                new wpsearchconsole_todo_top_display(); ?>
                <br class="clear">

                <!--The class is very important-->
                <form class="wpsearchconsole-todo-table" action="" method="post">
                    <div class="wpsc_warning"></div>
                    <?php
                    $this->todos_obj->prepare_items();
                    $this->todos_obj->display(); ?>
                </form>
                <?php $this->download('todo', 'wpsearchconsole-csv'); ?>
            </div>
            <?php
        }

        //
        public function wpsearchconsole_dashboard_page_callback()
        {

            $tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'keywords');

            ?>

            <div class="wrap">
                <h2><?php _e('Plugin Core', 'wpsearchconsole'); ?>
                    &nbsp;<?php $this->websocket(__('Refresh Data', 'wpsearchconsole'), 'reload-widget-data', $_GET['page'], $tab); ?>
                    &nbsp;<?php echo $this->get_LastCollectDate() . ' - ' . $this->time_gap('wpsearchconsole_last_crawled_widget'); ?></h2>
                <?php settings_errors();
                $this->wpsearchconsole_settings_dash_tabs($tab); ?>
                <div class="clear"></div>
                <br/><br/>
                <?php new wpsearchconsole_dashboard(); ?>
            </div>
            <?php
        }

        //the all console authentication page
        public function wpsearchconsole_settings_auth_tabs($current)
        {

            $checkbox = '<div class="dashicons dashicons-yes wpsearchconsole-green"><br /></div>';

            $tabs = array(
                'google' => __('Google Settings' . (get_option('wpsearchconsole_google_token') ? $checkbox : false), 'wpsearchconsole'),
                'mitambo' => __('Mitambo Settings' . (get_option('wpsearchconsole_mitambo') ? $checkbox : false), 'wpsearchconsole'),
                'roles' => __('Roles management', 'wpsearchconsole'),
                'support' => __('Plugin support', 'wpsearchconsole'),
                'debug' => __('Debug Information', 'wpsearchconsole'),
            ); ?>
            <h2 class="nav-tab-wrapper wpsc">
                <?php foreach ($tabs as $tab => $name):
                    $class = ($tab == $current) ? ' nav-tab-active' : '';
                    echo "<a class='nav-tab$class' href='?page=wpsearchconsole&tab=$tab'>$name</a>";
                endforeach; ?>
            </h2>
            <?php
        }

        //the all console data page
        public function wpsearchconsole_settings_dash_tabs($current)
        {

            $tabs = array(
                'keywords' => __('Keywords', 'wpsearchconsole'),
                'links' => __('Links', 'wpsearchconsole'),
                'duplication' => __('Duplication', 'wpsearchconsole'),
                'exposition' => __('Exposition', 'wpsearchconsole'),
                'intention' => __('Intention', 'wpsearchconsole'),
                'comparison' => __('Comparison', 'wpsearchconsole'),
            ); ?>
            <h2 class="nav-tab-wrapper wpsc">
                <?php foreach ($tabs as $tab => $name):
                    $class = ($tab == $current) ? ' nav-tab-active' : '';
                    echo "<a class='nav-tab$class' href='?page=wpsearchconsole-dash&tab=$tab'>$name</a>";
                endforeach; ?>
            </h2>
            <?php
        }

        //the all console data page
        public function wpsearchconsole_settings_data_tabs($current)
        {

            $tabs = array(
                '1' => __('Keywords', 'wpsearchconsole'),
                '2' => __('Status', 'wpsearchconsole'),
                '3' => __('Duplication', 'wpsearchconsole'),
                '4' => __('Resources', 'wpsearchconsole'),
                '5' => __('Outgoing', 'wpsearchconsole'),
            ); ?>
            <h2 class="nav-tab-wrapper wpsc">
                <?php foreach ($tabs as $tab => $name):
                    $class = ($tab == $current) ? ' nav-tab-active' : '';
                    echo "<a class='nav-tab$class' href='?page=wpsearchconsole-data&tab=$tab'>$name</a>";
                endforeach; ?>
            </h2>
            <?php
        }

        //add help tabs
        public function wpsearchconsole_help_tabs()
        {

            $page = (isset($_GET['page'])) ? sanitize_text_field($_GET['page']) : false;

            //Instantiate the class here because it requires a property, which needs to be called
            //inside a callback function
            $this->help = new wpsearchconsole_help(get_current_screen());

            switch ($page) {
                case 'wpsearchconsole':
                    $this->help->prepare('settings', 'main');
                    break;
                case 'wpsearchconsole-analysis':
                    $this->help->prepare('settings', 'analysis');
                    break;
                case 'wpsearchconsole-data':
                    $this->help->prepare('settings', 'data');
                    break;
                case 'wpsearchconsole-explore':
                    $this->help->prepare('settings', 'explore');
                    break;
                case 'wpsearchconsole-todo':
                    $this->help->prepare('settings', 'todo');
                    break;
                case 'wpsearchconsole-dash':
                    $this->help->prepare('dashboard', 'dash');
                    break;
                default:
                    false;
                    break;
            }
        }

        //websocket button
        public function websocket($title, $name, $page, $tab)
        {

            $show_tab = ($tab ? '&tab=' . $tab : false);
            echo '<a href="?page=' . $page . $show_tab . '&' . $name . '=true" class="page-title-action">' . $title . '</a>';
        }

        //time gap
        public function time_gap($name)
        {

            $time_data = get_option($name);
            $last_crawled = __('Last refreshed', 'wpsearchconsole') . ' ';
            $last_crawled .= ($time_data ? human_time_diff($time_data, time()) : false);
            $last_crawled .= ' ' . __('ago', 'wpsearchconsole');

            return '<span class="description">' . ($time_data ? $last_crawled : __('Never Refreshed', 'wpsearchconsole')) . '</span>';
        }

        public function get_LastCollectDate()
        {
            $mitamboDate = get_option('wpsearchconsole_mitambo_last_crawled_date');

            $mitamboDate = (is_object($mitamboDate) && array_key_exists('lastCollectDate', $mitamboDate)) ? $mitamboDate->lastCollectDate : $mitamboDate;
            $lastCollectDate = __('Last crawled at ', 'wpsearchconsole');
            return '<span class="description">' . ((!empty($mitamboDate)) ? $mitamboDate : __('Never Crawled', 'wpsearchconsole')) . '</span>';
        }

        //download csv link
        public function download($page, $download)
        { ?>

            <br class="clear">
            <a href="?page=wpsearchconsole-<?php echo $page; ?>&<?php echo $download; ?>=true"><?php _e('Download CSV', 'wpsearchconsole'); ?></a>
            <br class="clear">
            <?php
        }
    }
}
?>