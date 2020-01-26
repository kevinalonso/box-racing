<?php
/**
 *
 * @package: wpsearchconsole/admin/user/lib/metabox/
 * on: 24.06.2016
 * @since 0.1
 *
 * Display Link Analysis API call.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_display_link_analysis_metabox')) {

    class wpsearchconsole_display_link_analysis_metabox
    {

        private $tabs;
        private $tab_box;

        public function __construct()
        {

            $this->tabs = array(
                'summary' => __('Summary', 'wpsearchconsole'), 'inbounds' => __('Inbounds', 'wpsearchconsole'), 'outbounds' => __('Outbounds', 'wpsearchconsole'), 'details' => __('Details', 'wpsearchconsole'),
            );
            $this->tab_box = array('summary', 'inbounds', 'outbounds', 'details'); ?>

            <a href="<?php echo wpsearchconsole::getInstance()->filtering_url->render_url_with_newquery("mitambo-api-call=true&focus_tab=2&metabox=mitambo-link-analysis#tab2"); ?>"
               class="button button-primary alignright">
                <?php _e('Refresh Data', 'wpsearchconsole'); ?>
            </a>
            <div class="clear"></div>

            <?php
            $this->tab_html();
            $this->tab_panel();
        }

        //show tabs panels
        public function tab_panel()
        {

            foreach ($this->tab_box as $key): ?>
                <div id="<?php echo $key; ?>-box" class="wpsearchconsole-tabs-panel tabs-panel">
                    <?php $this->display_data_table = new Link_Analysis_Details($key);
                    $this->display_data_table->display(); ?>
                </div>
            <?php endforeach;
        }

        //show tabs
        public function tab_html()
        { ?>

            <ul class="category-tabs">
                <?php foreach ($this->tabs as $key => $val): ?>
                    <li id="<?php echo $key; ?>"
                        class="wpsearchconsole-tabs <?php echo($key == 'summary' ? 'tabs' : 'hide-if-no-js'); ?>">
                        <a href="#"><?php echo $val; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        //define tabs for API call
//        public function tab_box()
//        {
//
//            return ;
//        }
//
//        //define tabs for display purpose
//        public function tabs()
//        {
//
//            return ;
//        }
    }
}
?>