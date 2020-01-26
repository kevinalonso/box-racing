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
if (!class_exists('wpsearchconsole_explore_top_display')) {

    class wpsearchconsole_explore_top_display
    {

        private $exploration;
        public $tab;

        public function __construct($tab)
        {

            if (!isset($_GET['page']) || sanitize_text_field($_GET['page']) != 'wpsearchconsole-explore') return;
            $this->page = 'wpsearchconsole-explore';
            $this->tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'web');
            $this->display_notice();
            $this->exploration = new wpsearchconsole_exploration_categories($this->tab);

            $this->tabs($this->tab);
            $this->open_wpsc_container();?>
            <br class="clear">
            <?php $this->display($this->tab);
            $this->close_wpsc_container();
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
        //Display types of errors
        public function display($tab)
        {

            $output = '<ul class="subsubsub">';
            $i = 0;
            foreach ($this->exploration->assembly as $key => $val) {
                $output .= '<li>';
                $output .= ($val > 0 ? '<a href="?page=' . $this->page . '&tab=' . $this->tab . '&type=' . $key . '">' . $key . '</a>' : $key) . '<sup>' . __($val, 'wpsearchconsole') . '</sup>';
                $output .= ($i < 8 ? ' | ' : '');
                $output .= '</li>';
                $i = $i + 1;
            }
            $output .= '</ul>';
            echo $output;
        }

        //the all console data page
        public function tabs($current)
        {

            $tabs = array(
                'web' => __('Desktop', 'wpsearchconsole'),
                'smartphoneOnly' => __('SmartPhone', 'wpsearchconsole'),
                'mobile' => __( 'Mobile', 'wpsearchconsole' ),
            );
            $links = array(); ?>
            <div id="icon-themes" class="icon32"><br></div>
            <h2 class="nav-tab-wrapper wpsc">
                <?php foreach ($tabs as $tab => $name) :
                    $class = ($tab == $current) ? ' nav-tab-active' : '';
                    echo "<a class='nav-tab$class' href='?page=wpsearchconsole-explore&tab=$tab'>$name</a>";
                endforeach; ?>
            </h2>
            <?php
        }
        private function display_notice()
        {
            echo render_persistent_notices('mitambo');
        }
    }
} ?>