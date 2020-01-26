<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/widget/
 * on: 24.06.2016
 * @since 0.1
 *
 * SVG widget for large widgets.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_duplicated_widget')) {

    class wpsearchconsole_duplicated_widget
    {

        public function __construct($title)
        {

            $this->tabs = $this->tabs();
            $this->tab_box = $this->tab_box();
        }

        public function html()
        {

            $this->tab_html();
            $this->tab_panel();
        }

        public function tab_panel()
        {

            foreach ($this->tab_box as $key => $val) : ?>
                <div id="<?php echo $val; ?>-box" class="wpsearchconsole-tabs-panel tabs-panel">
                    <?php
                    $this->display_data_table = new Widget_Data_Table('global_duplicate_' . $key);
                    $this->display_data_table->display($key);
                    ?>
                </div>
            <?php endforeach;
        }

        public function tab_html()
        { ?>

            <ul class="category-tabs">
                <?php foreach ($this->tabs as $key => $val) : ?>
                    <li id="<?php echo $key; ?>"
                        class="wpsearchconsole-tabs <?php echo($key == 'duplicate-titles' ? 'tabs' : 'hide-if-no-js'); ?>">
                        <a href="#"><?php echo $val; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        public function tab_box()
        {

            return array('title' => 'duplicate-titles', 'desc' => 'duplicate-desc', 'content' => 'duplicate-content');
        }

        //define tabs for display purpose
        public function tabs()
        {

            return array(
                'duplicate-titles' => __('Titles', 'wpsearchconsole'),
                'duplicate-desc' => __('Description', 'wpsearchconsole'),
                'duplicate-content' => __('Content', 'wpsearchconsole')
            );
        }
    }
}
?>