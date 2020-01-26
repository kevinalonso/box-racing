<?php
/**
 *
 * @package: wpsearchconsole/user/
 * on: 27.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add help documentation here.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_help')) {

    class wpsearchconsole_help
    {

        private $page;
        private $screen;
        private $input;

        //build the settings
        public function __construct($screen)
        {

            $this->screen = $screen;
        }

        //build helps
        public function build($help_data, $help_links)
        {

            foreach ($help_data as $key) {
                $this->screen->add_help_tab($key);
            }
            $this->screen->set_help_sidebar($help_links);
        }

        //prepare the data
        public function prepare($input, $page)
        {

            if ($input == 'settings') {

                switch ($page) {
                    case 'main' :
                        $output = $this->settings_main();
                        break;
                    case 'explore' :
                        $output = $this->settings_explore();
                        break;
                    case 'analysis' :
                        $output = $this->settings_analysis();
                        break;
                    case 'todo' :
                        $output = $this->settings_todo();
                        break;
                    case 'data' :
                        $output = $this->settings_data();
                        break;
                    default:
                        $output = false;
                }

            } elseif ($input == 'metabox') {

                $output = $this->metabox();

            } elseif ($input == 'dashboard') {

                $output = $this->dashboard();

            }

            if ($output) {
                $this->build($output['data'], $output['links']);
            }
        }

        //settings page help
        public function settings_main()
        {

            $data = array(
                array(
                    'id' => 'help_general',
                    'title' => __('General Info', 'wpsearchconsole'),
                    'content' => '<p>' . __('General Help content.', 'wpsearchconsole') . '</p>',
                ),
                array(
                    'id' => 'google_help',
                    'title' => __('Google Settings', 'wpsearchconsole'),
                    'content' => '<p>' . __('Google Help content.', 'wpsearchconsole') . '</p>',
                ),
                array(
                    'id' => 'mitambo_help',
                    'title' => __('Mitambo Settings', 'wpsearchconsole'),
                    'content' => '<p>' . __('Mitambo Help content.', 'wpsearchconsole') . '</p>',
                ),
                array(
                    'id' => 'roles_help',
                    'title' => __('Roles', 'wpsearchconsole'),
                    'content' => '<p>' . __('Roles Help content.', 'wpsearchconsole') . '</p>',
                ),
            );

            $links = '<h3>' . __('More Information', 'wpsearchconsole') . '</h3><p><a href="#">' . __('Documentation on how to manage your settings', 'wpsearchconsole') . '</a></p><p><a href="#">' . __('Official blog', 'wpsearchconsole') . '</a></p>';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }

        //settings page help
        public function settings_explore()
        {

            $data = array(
                array(
                    'id' => 'help_start',
                    'title' => __('Introduction', 'cgss'),
                    'content' => 'help content',
                ),
            );

            $links = 'side';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }

        //settings page help
        public function settings_analysis()
        {

            $data = array(
                array(
                    'id' => 'help_start',
                    'title' => __('Introduction', 'cgss'),
                    'content' => 'help content',
                ),
            );

            $links = 'side';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }

        //settings page help
        public function settings_todo()
        {

            $data = array(
                array(
                    'id' => 'help_start',
                    'title' => __('Introduction', 'cgss'),
                    'content' => 'help content',
                ),
            );

            $links = 'side';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }

        //settings page help
        public function settings_data()
        {

            $data = array(
                array(
                    'id' => 'help_start',
                    'title' => __('Introduction', 'cgss'),
                    'content' => 'help content',
                ),
            );

            $links = 'side';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }

        //metabox page help
        public function metabox()
        {

            $data = array(
                array(
                    'id' => 'help_start',
                    'title' => __('Introduction', 'cgss'),
                    'content' => 'help content',
                ),
            );

            $links = 'side';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }

        //dashboard page help
        public function dashboard()
        {

            $data = array(
                array(
                    'id' => 'help_start',
                    'title' => __('Introduction', 'cgss'),
                    'content' => 'help content',
                ),
            );

            $links = 'side';

            return array(
                'data' => $data,
                'links' => $links,
            );
        }
    }
}
?>