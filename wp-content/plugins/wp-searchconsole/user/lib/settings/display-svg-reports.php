<?php
/**
 *
 * @package: /wpsearchconsole/user/lib/settings/
 * on: 26.05.2016
 *
 * Display of Reports with SVG Chart.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the Reports of SVG charts page
 */

if (!class_exists('wpsearchconsole_svg_charts_reports')) {

    class wpsearchconsole_svg_charts_reports
    {

        public function __construct()
        {

            global $wpdb;
            $this->types = $this->svg_chart_types();
            //$this->table_name = $wpdb->prefix . 'wpsearchconsole_json';
            $this->token = get_option('wpsearchconsole_mitambo');
            $this->notice = new wpsearchconsole_notices();
            $this->open_wpsc_container();
            ?>
            <div class="wpsearchconsole-parent-col">
                <?php if (!$this->token) {
                    $this->notice->api_call_error();
                    return;
                } ?>
                <div class="wpsearchconsole-child-left-col">
                    <div class="page-subtitle">
                        <h2><?php _e('SVG Bargraphs'); ?></h2>
                    </div>
                    <?php $this->col('BarGraph'); ?>
                </div>
                <div class="wpsearchconsole-child-right-col">
                    <div class="page-subtitle">
                        <h2><?php _e('SVG HBargraphs'); ?></h2>
                    </div>
                    <?php $this->col('HBarGraph'); ?>
                </div>
            </div>

            <?php
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
        public function col($type)
        {
            if (isset($this->token)) {
                foreach ($this->types as $name => $title) {

                    $this->widgetify($title, $this->html($type, $name));
                }
            }
        }

        public function widgetify($title, $data)
        { ?>

            <div class="postbox wpsearchconsole_metabox">
                <div class="hndle ui-sortable-handle" style="padding: 15px 10px 15px 10px;">
                    <strong>&nbsp;&nbsp;<?php echo $title; ?></strong><?php $this->toggle(); ?></div>
                <div class="inside">
                    <?php echo $data; ?>
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

        public function svgchart($title, $name, $type)
        {
            $this->widgetify($title, $this->html($type, $name));
        }

        public function html($type, $name)
        {

            $randNum = rand(1111, 9999);
            if ($type == 'BarGraph') {

                $output = $this->svg_html('BarGraph', $randNum, $name);
                //$output = $this->svg_html( 'PieChart', 123456 );

            } elseif ($type == 'HBarGraph') {

                $output = $this->svg_html('HBarGraph', $randNum, $name);

            } /*elseif ( $name == 'svg-demo3' ) {

				$output = $this->svg_html( 'LineGraph', 5687 );

			} elseif ( $name == 'svg-demo4' ) {

				$output = $this->svg_html( 'BarGraph', 2258 );

			} elseif ( $name == 'svg-demo5' ) {

				$output = $this->svg_html( 'HBarGraph', 3985 );

			}*/
            else {

                $output = 'chart';

            }

            return $output;
        }

        //output html generator function
        public function svg_html($type, $number, $name)
        {
            $token = $this->token;
            $output = '<div id="' . $type . $number . '" style="min-width: 200px; min-height: 425px;"></div>' . "\r\n";
            $output .= '<script>' . "\r\n";
            $output .= 'jQuery(document).ready(function () {' . "\r\n";
            $output .= 'CallDisplaySimple' . $type . '("' . WPSEARCHCONSOLE_PLUGIN_API . '/data/' . $name . '","' . $token . '", "' . $type . $number . '")' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '// TODO add an onresize on the div' . "\r\n";
            $output .= 'var resizeTimer' . $number . ';' . "\r\n";
            $output .= 'jQuery(window).on("resize",function () {' . "\r\n";
            $output .= 'clearTimeout(resizeTimer' . $number . ');' . "\r\n";
            $output .= 'resizeTimer' . $number . ' = setTimeout(CallDisplaySimple' . $type . '.bind(null, "' . WPSEARCHCONSOLE_PLUGIN_API . '/data/' . $name . '","' . $token . '", "' . $type . $number . '"), 100);' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '</script>' . "\r\n";

            return $output;
        }

        public function svg_chart_types()
        {
            // Define all svg api endpoints in array
            return array('svg-depth-fetchtime' => __('SVG Depth Fetchtime', 'wpsearchconsole'),
                'svg-depth-weight' => __('SVG Depth Weight', 'wpsearchconsole'),
                'svg-depth-status' => __('SVG Depth Status', 'wpsearchconsole'),
                'svg-depth-linkratio-gt1' => __('SVG Depth Linkratio GT1', 'wpsearchconsole'),
                'svg-depth-linkratio' => __('SVG Depth Linkratio', 'wpsearchconsole'),
                'svg-depth-perception-gt1' => __('SVG Depth Perception GT1', 'wpsearchconsole'),
                'svg-depth-perception' => __('SVG Depth Perception', 'wpsearchconsole'),
            );
        }

    }
}