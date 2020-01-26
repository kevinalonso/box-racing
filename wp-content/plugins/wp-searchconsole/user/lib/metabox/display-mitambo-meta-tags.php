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
if (!class_exists('wpsearchconsole_display_meta_tags_metabox')) {

    class wpsearchconsole_display_meta_tags_metabox
    {

        public function __construct()
        {
            ?>

            <a href="<?php echo wpsearchconsole::getInstance()->filtering_url->render_url_with_newquery("mitambo-api-call=true&focus_tab=1&metabox=mitambo-meta-tags#tab1"); ?>"
               class="button button-primary alignright">
                <?php _e('Refresh Data', 'wpsearchconsole'); ?>
            </a>
            <div class="clear"></div>
            <br/>
            <?php
            $this->tab_panel();
        }

        //show tabs panels
        public function tab_panel()
        {

            $this->display_data_table = new Meta_Tags_List();
            $this->display_data_table->display();
        }
    }
} ?>