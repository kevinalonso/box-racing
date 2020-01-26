<?php
/**
 *
 * @package: wpsearchconsole/admin/user/lib/metabox/
 * on: 24.06.2016
 * @since 0.1
 *
 * Display mitambo keywords API call.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_display_mitambo_keyword_metabox')) {

    class wpsearchconsole_display_mitambo_keyword_metabox
    {

        private $tabs;
        private $tab_box;

        public function __construct()
        {

            $this->tabs = $this->tabs();
            $this->tab_box = $this->tab_box(); ?>

            <a href="<?php echo wpsearchconsole::getInstance()->filtering_url->render_url_with_newquery("mitambo-api-call=true&focus_tab=1&metabox=mitambo-keywords#tab1"); ?>"
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
                    <?php
                    $this->display_data_table = new Keywords_List($key);
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
                        class="wpsearchconsole-tabs <?php echo($key == 'resume' ? 'tabs' : 'hide-if-no-js'); ?>">
                        <a href="#"><?php echo $val; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        //define tabs for API call
        public function tab_box()
        {
            /* Added tesing variable by DEEPAK KUMAR */
            return array('resume', 'simple-keyword', 'double-keyword', 'triple-keyword');
        }

        //define tabs for display purpose
        public function tabs()
        {

            return array(
                'resume' => __('Resume', 'wpsearchconsole'),
                'simple-keyword' => __('Keywords Simple', 'wpsearchconsole'),
                'double-keyword' => __('Double', 'wpsearchconsole'),
                'triple-keyword' => __('Triple', 'wpsearchconsole'),
            );
        }
    }
}
?>