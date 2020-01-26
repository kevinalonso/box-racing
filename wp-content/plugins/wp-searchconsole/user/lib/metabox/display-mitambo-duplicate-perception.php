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
if (!class_exists('wpsearchconsole_display_post_duplicate_perception_metabox')) {

    class wpsearchconsole_display_post_duplicate_perception_metabox
    {

        private $tabs;
        private $tab_box;

        public function __construct()
        {

            $type = isset($_GET['taxonomy']) ? 'taxonomy' : 'post'; ?>

            <a href="<?php echo wpsearchconsole::getInstance()->filtering_url->render_url_with_newquery("mitambo-api-call=true&focus_tab=3&metabox=mitambo-$type-duplicate-perception#tab3"); ?>"
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
            $type = 'post_duplicate_perception';
            $this->display_data_table = new Duplication_Table($type);
            $this->display_data_table->display();
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
    }
} ?>