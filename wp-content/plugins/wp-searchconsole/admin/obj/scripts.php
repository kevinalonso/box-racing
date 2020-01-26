<?php
/**
 *
 * @package: wpsearchconsole/admin/obj/
 * on: 16.06.2015
 * @since 0.1
 *
 * Add scripts for plugin functionality.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_scripts')) {

    class wpsearchconsole_scripts
    {

        public function __construct()
        {

            add_action('admin_head', array($this, 'wpsearchconsole_admin_scripts_settings'));
            add_action('admin_head', array($this, 'wpsearchconsole_post_todo_table_css'));

            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_admin_scripts_posttype'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_oauth_js'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_svg_js'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_dash_layout_css'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_list_of_data_css'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_settings_css'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_svg_chart_reports_css'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_dash_layout_js'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_colors_css'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_tabs_js'));
            add_action('admin_enqueue_scripts', array($this, 'wpsearchconsole_custom_js'));
        }

        //add admin header style for table width
        public function wpsearchconsole_admin_scripts_settings()
        {

            $page = (isset($_GET['page'])) ? sanitize_text_field($_GET['page']) : false;

            if ($page == 'wpsearchconsole-data' || $page == 'wpsearchconsole-explore') { ?>
                <style type="text/css">
                    .wp-list-table .column-URL {
                        width: 45%;
                    }

                    .wp-list-table .column-last_crawl {
                        width: 22.5%;
                    }

                    .wp-list-table .column-first_crawl {
                        width: 22.5%;
                    }

                    .wp-list-table .column-error_code {
                        width: 10%;
                    }
                </style>
            <?php } elseif ($page == 'wpsearchconsole-analysis') { ?>
                <style type="text/css">
                    .wp-list-table .column-requests {
                        width: 40%;
                    }

                    .wp-list-table .column-clicks {
                        width: 15%;
                    }

                    .wp-list-table .column-impressions {
                        width: 15%;
                    }

                    .wp-list-table .column-ctr {
                        width: 15%;
                    }

                    .wp-list-table .column-posiiton {
                        width: 15%;
                    }
                </style>
                <?php
            } elseif ($page == 'wpsearchconsole-todo') { ?>
                <style type="text/css">
                    .wp-list-table .column-post {
                        width: 20%;
                    }

                    .wp-list-table .column-priority {
                        width: 7.5%;
                    }

                    .wp-list-table .column-actions {
                        width: 35%;
                    }

                    .wp-list-table .column-assigned_to {
                        width: 12%;
                    }

                    .wp-list-table .column-category {
                        width: 9%;
                    }

                    .wp-list-table .column-due_date {
                        width: 9%;
                    }

                    .wp-list-table .column-delete {
                        width: 9%;
                    }
                </style>
                <?php
            }
        }

        public function wpsearchconsole_post_todo_table_css()
        {

            if (!isset($_GET['post']) && !isset($_GET['post_type'])) {
                return;
            }
            ?>

            <style type="text/css">
                .wpsearchconsole-done-checkbox {
                    width: 5%;
                }

                .wpsearchconsole-todo-priority {
                    width: 10%;
                }

                .wpsearchconsole-todo-action {
                    width: 50%;
                }

                .wpsearchconsole-todo-responsible {
                    width: 10%;
                }

                .wpsearchconsole-todo-category {
                    width: 5%;
                }

                .wpsearchconsole-todo-due_date {
                    width: 12.5%;
                }

                .wpsearchconsole-todo-delete {
                    width: 7.5%;
                }
            </style>
            <?php
        }

        //add admin header style for table width
        public function wpsearchconsole_admin_scripts_posttype()
        {

            if (!isset($_GET['post']) && !isset($_GET['post_type'])) {
                return;
            }

            wp_enqueue_script('dashboard_layout_js', WPSEARCHCONSOLE_JS_PATH . 'ui/widget.js', array('jquery'));
            wp_enqueue_script('datetimepicker_js', WPSEARCHCONSOLE_JS_PATH . 'ui/datetimepicker.js', array('jquery'));
            wp_enqueue_script('datetimepicker_trigger_js', WPSEARCHCONSOLE_JS_PATH . 'ui/datetimepicker-trigger.js', array('datetimepicker_js'));

            wp_enqueue_style('datetimepicker_css', WPSEARCHCONSOLE_CSS_PATH . 'datetimepicker.css');
        }

        //add admin header style for table width
        public function wpsearchconsole_dash_layout_css()
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-dash') {
                return;
            }

            wp_enqueue_style('dashboard_layout_css', WPSEARCHCONSOLE_CSS_PATH . 'dashboard-layout.css');
        }

        public function wpsearchconsole_custom_js()
        {

            wp_enqueue_script('dashboard_layout_js', WPSEARCHCONSOLE_JS_PATH . 'ui/widget.js', array('jquery'));
            wp_enqueue_script('custom_js', WPSEARCHCONSOLE_JS_PATH . 'ui/custom.js');
            wp_enqueue_style('dashboard_colors_css', WPSEARCHCONSOLE_CSS_PATH . 'colors.css');
        }

        //add admin header style for table width
        public function wpsearchconsole_dash_layout_js()
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-dash') {
                return;
            }

            wp_enqueue_script('dashboard_layout_js', WPSEARCHCONSOLE_JS_PATH . 'ui/widget.js');
        }

        public function wpsearchconsole_list_of_data_css()
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-data') {
                return;
            }

            wp_enqueue_style('setting_data_css', WPSEARCHCONSOLE_CSS_PATH . 'list-of-data.css');
        }

        public function wpsearchconsole_settings_css()
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole') {
                return;
            }

            wp_enqueue_style('setting_css', WPSEARCHCONSOLE_CSS_PATH . 'settings.css');
        }

        public function wpsearchconsole_svg_chart_reports_css()
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-svg-charts') {
                return;
            }

            wp_enqueue_style('svg_chart_css', WPSEARCHCONSOLE_CSS_PATH . 'svg_reports.css');
        }

        //admin javascript for displaying plots
        public function wpsearchconsole_svg_js()
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-dash' && sanitize_text_field($_GET['page']) != 'wpsearchconsole-svg-charts') {
                return;
            }

            wp_enqueue_script('svg_base_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_base.js', array('jquery'));
            wp_enqueue_script('svg_piechart_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_piechart.js', array('jquery'));
            wp_enqueue_script('svg_stargraph_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_stargraph.js', array('jquery'));
            wp_enqueue_script('svg_linegraph_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_linegraph.js', array('jquery'));
            wp_enqueue_script('svg_bargraph_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_bargraph.js', array('jquery'));
            wp_enqueue_script('svg_hbargraph_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_hbargraph.js', array('jquery'));
            wp_enqueue_script('svg_layer_js', WPSEARCHCONSOLE_JS_PATH . 'chart/layer.js', array('jquery'));
            wp_enqueue_script('svg_main_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg.js', array('jquery'));

            if (isset($_GET['tab'])) {
                if (sanitize_text_field($_GET['tab']) != 'comparison') {
                    wp_enqueue_script('svg_semtree_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_semtree.js', array('jquery'));
                } elseif (sanitize_text_field($_GET['tab']) == 'comparison') {
                    wp_enqueue_script('svg_compare_js', WPSEARCHCONSOLE_JS_PATH . 'chart/svg_compare.js', array('jquery'));
                    wp_enqueue_script('svg_pan_zoom_js', WPSEARCHCONSOLE_JS_PATH . '/svg-pan-zoom.js', array('jquery'));
                }

                if (sanitize_text_field($_GET['tab']) == 'exposition' || sanitize_text_field($_GET['tab']) == 'intention' || sanitize_text_field($_GET['tab']) == 'comparison') {
                    wp_enqueue_style('dashboard_semtree_css', WPSEARCHCONSOLE_CSS_PATH . 'semtree.css');
                    wp_enqueue_style('dashboard_semtree_global_css', WPSEARCHCONSOLE_CSS_PATH . 'global_semtree.css');
                    wp_enqueue_style('dashboard_compare_global_css', WPSEARCHCONSOLE_CSS_PATH . 'global_compare.css');
                }
            }
        }

        //admin javascript for oAuth popup and analysis
        public function wpsearchconsole_oauth_js()
        {

            if (!isset($_GET['page'])) {
                return;
            }

            if (sanitize_text_field($_GET['page']) == 'wpsearchconsole') {

                wp_enqueue_script('google_auth_js', WPSEARCHCONSOLE_JS_PATH . 'ui/gauth.js', array('jquery'));

            } elseif (sanitize_text_field($_GET['page']) == 'wpsearchconsole-analysis') {

                wp_enqueue_script('analysis_auth_js', WPSEARCHCONSOLE_JS_PATH . 'ui/analysis-filter.js', array('jquery'));

            }
        }

        //add colors for all wpsearchconsole useage
        public function wpsearchconsole_colors_css()
        {

            wp_enqueue_style('dashboard_colors_css', WPSEARCHCONSOLE_CSS_PATH . 'colors.css');
        }

        //
        public function wpsearchconsole_tabs_js()
        {

            if (!isset($_GET['post']) && !isset($_GET['post_type']) && !isset($_GET['category']) && !isset($_GET['post_tag'])) {
                return;
            }

            wp_enqueue_script('tabs_group_js', WPSEARCHCONSOLE_JS_PATH . 'ui/editor-tabs.js', array('jquery'));
        }
    }
}
?>