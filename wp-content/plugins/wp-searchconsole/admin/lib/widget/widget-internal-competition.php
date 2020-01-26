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
if (!class_exists('wpsearchconsole_duplicate_perception_widget')) {

    class wpsearchconsole_duplicate_perception_widget
    {

        public function html()
        {

            $this->tab_panel();
        }

        public function tab_panel()
        {

            $this->display_data_table = new Widget_Data_Table('global_duplicate_perception');
            $this->display_data_table->display_perception();

        }


    }
}
?>