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
if (!class_exists('wpsearchconsole_internal_status_widget')) {

    class wpsearchconsole_internal_status_widget
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
                    $this->display_data_table = new Widget_Data_Table('internal_by_status');
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
                        class="wpsearchconsole-tabs <?php echo($key == 'status-302' ? 'tabs' : 'hide-if-no-js'); ?>">
                        <a href="#"><?php echo $val; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        public function tab_box()
        {

            return array(302 => 'status-302', 301 => 'status-301', 307 => 'status-307', 404 => 'status-404', 500 => 'status-500', /*, 'rejected' => 'status-rejected' */);
        }

        //define tabs for display purpose
        public function tabs()
        {

            return array(
                'status-301' => __('Status 301', 'wpsearchconsole'),
                'status-302' => __('Status 302', 'wpsearchconsole'),
                'status-307' => __('Status 307', 'wpsearchconsole'),
                'status-404' => __('Status 404', 'wpsearchconsole'),
                'status-500' => __('Status 500', 'wpsearchconsole')
                //'status-rejected' => __( 'Status Rejected', 'wpsearchconsole' )
            );
        }
    }
}
?>