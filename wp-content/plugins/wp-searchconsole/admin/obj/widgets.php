<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/
 * on: 25.05.2016
 * @since 0.1
 *
 * Add widgets. This is a buffer widget.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_dashboard_widget')) {

    class wpsearchconsole_dashboard_widget
    {

        private $todo_data;
        private $keywords_data;

        public function __construct()
        {

            $this->todo_data = new wpsearchconsole_todo_widget();
            $this->keywords_data = false;
        }

        //Create the widget
        public function widgetify($title, $data)
        { ?>

            <div class="postbox wpsearchconsole_metabox">
                <div class="hndle ui-sortable-handle" style="padding: 15px 10px 15px 10px;">
                    <strong>&nbsp;&nbsp;<?php echo $title; ?></strong><?php $this->toggle(); ?></div>
                <div class="inside">
                    <?php echo $data->html(); ?>
                </div>
            </div>
            <?php
        }

        //toggle icon on right hand side
        public function toggle()
        { ?>

            <div class="ui-toggle alignright">
                <span class="dashicons dashicons-arrow-up"></span>
                <span class="dashicons dashicons-arrow-down up-toggle"></span>
            </div>
            <?php
        }

        //Todo List metabox decalration
        public function todo($title)
        {

            $this->widgetify($title, $this->todo_data);
        }

        //keywords metabox
        public function analysis($title, $type, $dimension)
        {

            $analysis_data = new wpsearchconsole_analysis_widget($type, $dimension);
            return $this->widgetify($title, $analysis_data);
        }

        //keywords metabox
        public function keywords($title)
        {

            $keywords_data = new wpsearchconsole_keywords_widget($title);
            return $this->widgetify($title, $keywords_data);
        }

        // INTERNAL by STATUS metabox
        public function internal_status($title)
        {

            $internal_status_data = new wpsearchconsole_internal_status_widget($title);
            return $this->widgetify($title, $internal_status_data);
        }

        public function duplicated($title)
        {

            $duplicated_data = new wpsearchconsole_duplicated_widget($title);
            return $this->widgetify($title, $duplicated_data);
        }

        public function duplicate_perception($title)
        {

            $duplicate_perception_data = new wpsearchconsole_duplicate_perception_widget($title);
            return $this->widgetify($title, $duplicate_perception_data);
        }

        //keywords metabox
        public function svg($title, $name)
        {

            $svg_data = new wpsearchconsole_svg_widget($name);
            return $this->widgetify($title, $svg_data);
        }

        //keywords metabox
        public function svg_large($title, $name)
        {

            $svg_data = new wpsearchconsole_svg_large_widget($name);
            return $this->widgetify($title, $svg_data);
        }
    }
}
?>