<?php
/**
 *
 * @package: wpsearchconsole/admin/obj/
 * on: 25.05.2016
 * @since 0.1
 *
 * Add metabox of publishable content.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_dashboard')) {

    class wpsearchconsole_dashboard
    {

        private $widget;

        public function __construct()
        {
            $widgetColClass = '';
            $svgChart = false;

            $this->widget = new wpsearchconsole_dashboard_widget();

            $tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'keywords');
            if ($tab == 'exposition' || $tab == 'intention' || $tab == 'comparison') {
                $widgetColClass = 'svg-large';
                $svgChart = true;
            }
            ?>

            <div class="wpsearchconsole-parent-col <?php echo $widgetColClass; ?>">
                <div class="wpsearchconsole-child-left-col">
                    <?php $tab ? $this->col($tab, 'left') : false; ?>
                </div>
                <?php if (!$svgChart) { ?>
                    <div class="wpsearchconsole-child-right-col">
                        <?php $tab ? $this->col($tab, 'right') : false; ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }

        //Define the columns
        public function col($tab, $hand)
        {

            switch ($tab) {
                case 'keywords' :
                    switch ($hand) {
                        case 'left' :
                            $title = __('Latest 10 Actions', 'wpsearchconsole');
                            $this->widget->todo($title);


                            $title = __('Top RANKED page by PageRank (internal)', 'wpsearchconsole');
                            $this->widget->keywords($title);


                            $title = __('TOP 10 Queries per device', 'wpsearchconsole');
                            $type = 'query';
                            $dimension = 'device';
                            $this->widget->analysis($title, $type, $dimension);


                            $title = __('TOP 10 Queries per medium', 'wpsearchconsole');
                            $type = 'query';
                            $dimension = 'medium';
                            $this->widget->analysis($title, $type, $dimension);


                            $title = __('TOP 10 Pages per device', 'wpsearchconsole');
                            $type = 'page';
                            $dimension = 'device';
                            $this->widget->analysis($title, $type, $dimension);


                            $title = __('TOP 10 Pages per medium', 'wpsearchconsole');
                            $type = 'page';
                            $dimension = 'medium';
                            $this->widget->analysis($title, $type, $dimension);

                            break;
                        case 'right' :

                            $title = __('SVG Demo1', 'wpsearchconsole');
                            $name = 'svg-demo1';
                            //$this->widget->svg( $title, $name );

                            $title = __('SVG Demo2', 'wpsearchconsole');
                            $name = 'svg-demo2';
                            //	$this->widget->svg( $title, $name );

                            $title = __('SVG Demo3', 'wpsearchconsole');
                            $name = 'svg-demo3';
                            //$this->widget->svg( $title, $name );

                            $title = __('SVG Demo4', 'wpsearchconsole');
                            $name = 'svg-demo4';
                            //	$this->widget->svg( $title, $name );

                            $title = __('SVG Demo5', 'wpsearchconsole');
                            $name = 'svg-demo5';
                            //	$this->widget->svg( $title, $name );

                            break;
                    }
                    break;
                case 'links' :
                    switch ($hand) {
                        case 'left' :
                            $title = __('Internal By Status', 'wpsearchconsole');
                            $this->widget->internal_status($title);

                            break;
                        case 'right' :

                            break;
                    }
                    break;
                case 'duplication' :
                    switch ($hand) {
                        case 'left' :
                            $title = __('Duplicated', 'wpsearchconsole');
                            $this->widget->duplicated($title);
                            $title = __('Internal Competition (URL with the same keywords perception) ', 'wpsearchconsole');
                            $this->widget->duplicate_perception($title);

                            break;
                        case 'right' :

                            break;
                    }
                    break;
                case 'exposition' :
                    switch ($hand) {
                        case 'left' :

                            $title = __('SVG Exposition', 'wpsearchconsole');
                            $name = 'svg-exposition';
                            $this->widget->svg_large($title, $name);
                            break;

                        case 'right' :

                            break;
                    }
                    break;
                case 'intention' :
                    switch ($hand) {
                        case 'left' :

                            $title = __('SVG Intention', 'wpsearchconsole');
                            $name = 'svg-intention';
                            $this->widget->svg_large($title, $name);
                            break;

                        case 'right' :

                            break;
                    }
                    break;
                case 'comparison' :
                    switch ($hand) {
                        case 'left' :

                            $title = __('SVG Comparison', 'wpsearchconsole');
                            $name = 'svg-comparison';
                            $this->widget->svg_large($title, $name);
                            break;

                        case 'right' :

                            break;
                    }
                    break;
            }
        }
    }
}
?>