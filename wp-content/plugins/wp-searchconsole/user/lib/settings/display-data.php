<?php
/**
 *
 * @package: advanced-wordpress-plugin/user/lib/settings/
 * on: 24.05.2015
 * @since 0.1
 * @modified: 2
 *
 * Display of Exploration settings page top area.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the google auth display class
 */
if (!class_exists('wpsearchconsole_data_top_display')) {

    class wpsearchconsole_data_top_display
    {

        private $data;
        public $tab;

        public function __construct($tab)
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-data') return;
            $this->page = 'wpsearchconsole-data';
            $this->tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 1);
            $this->tab_name = $this->wpsearchconsole_data_cat_tab($this->tab);
            $this->data = new wpsearchconsole_data_categories($this->tab_name);

            $this->tabs($this->tab);
            $this->open_wpsc_container();?>
            <br class="clear">
            <?php $this->display($this->tab);
            $this->close_wpsc_container();
        }

        //display sub tab categories so data_top_display
        public function display($tab)
        {

            $tabName = List_of_Data::getCurrentTabName(isset($tab) ? $tab : 1);
            $type = List_of_Data::getDefaultType($tabName);
            $type = (isset($_GET['type']) ? sanitize_key($_GET['type']) : $type);
            $output = '<ul class="wpsearchconsole-subtab">';
            $i = 0;
            foreach ($this->data->assembly as $key => $val) {
                $output .= '<li>';
                $output .= (($val > 0 || !empty($val)) && $key != $type) ? '<a href="?page=' . $this->page . '&tab=' . $this->tab . '&type=' . $key . '">' . __($this->data->statuscodes[$key], 'wpsearchconsole') . '</a>' : __($this->data->statuscodes[$key], 'wpsearchconsole');
                $output .= '</li>';
                $i = $i + 1;
            }
            $output .= '</ul>';
            echo $output;
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
        public function wpsearchconsole_data_cat_tab($tab)
        {

            switch ($tab) {

                case 1:
                    $tab_name = 'keywords';
                    break;

                case 2:
                    $tab_name = 'status';
                    break;

                case 3:
                    $tab_name = 'duplication';
                    break;

                case 4:
                    $tab_name = 'resources';
                    break;

                case 5:
                    $tab_name = 'outgoinglinks';
                    break;
            }
            return $tab_name;

        }

        public function tabs($current)
        {

            $tabs = array(
                1 => __('Main Keywords', 'wpsearchconsole'),
                2 => __('Status', 'wpsearchconsole'),
                3 => __('Duplication', 'wpsearchconsole'),
                4 => __('Resources', 'wpsearchconsole'),
                5 => __('Outgoing Links', 'wpsearchconsole'),
            );
            $links = array(); ?>
            <div id="icon-themes" class="icon32"><br></div>
            <h2 class="nav-tab-wrapper wpsc">
                <?php foreach ($tabs as $tab => $name) :
                    $class = ($tab == $current) ? ' nav-tab-active' : '';
                    echo "<a class='nav-tab$class' href='?page=wpsearchconsole-data&tab=$tab'>$name</a>";
                endforeach; ?>
            </h2>
            <?php
        }

    }
}