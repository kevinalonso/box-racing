<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/widget/
 * on: 24.06.2016
 * @since 0.1
 *
 * Todo widget using db _wpsearchconsole_widget.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Define the base class for menu and settings
 */
if (!class_exists('wpsearchconsole_svg_widget')) {

    class wpsearchconsole_svg_widget
    {

        private $table_name;
        private $name;

        public function __construct($name)
        {

            global $wpdb;

            $this->name = $name;
            //$this->table_name = $wpdb->prefix . 'wpsearchconsole_json';
            $this->token = get_option('wpsearchconsole_mitambo');
        }

        //html output
        public function html()
        {

            //$output = $this->call_data( get_option( 'wpsearchconsole_' . $this->name . '_ID' ) );

            if ($this->name == 'svg-demo1') {

                $output = $this->svg_html('BarGraph', 2258);

            } elseif ($this->name == 'svg-demo2') {

                $output = $this->svg_html('StarGraph', 9987);

            } elseif ($this->name == 'svg-demo3') {

                $output = $this->svg_html('LineGraph', 5687);

            } elseif ($this->name == 'svg-demo4') {

                $output = $this->svg_html('BarGraph', 2258);

            } elseif ($this->name == 'svg-demo5') {

                $output = $this->svg_html('HBarGraph', 3985);

            } else {

                $output = 'chart';

            }

            return $output;
        }

        //output html generator function
        public function svg_html($name, $number)
        {

            $output = '<div id="' . $name . $number . '" style="min-width: 250px; min-height: 285px;"></div>' . "\r\n";
            $output .= '<script>' . "\r\n";
            $output .= 'jQuery(document).ready(function () {' . "\r\n";
            $output .= 'CallDisplaySimple' . $name . '("' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-depth-fetchtime","' . $this->token . '","' . $name . $number . '")' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '// TODO add an onresize on the div' . "\r\n";
            $output .= 'var resizeTimer' . $number . ';' . "\r\n";
            $output .= 'jQuery(window).on("resize",function () {' . "\r\n";
            $output .= 'clearTimeout(resizeTimer' . $number . ');' . "\r\n";
            $output .= 'resizeTimer' . $number . ' = setTimeout(CallDisplaySimple' . $name . '.bind(null, "' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-depth-fetchtime","' . $name . $number . '"), 100);' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '</script>' . "\r\n";

            return $output;
        }

        //call the API
        public function call_data($json_ID)
        {

            global $wpdb;

            $value = $wpdb->get_var("SELECT value FROM $this->table_name WHERE json_key = '$json_ID'");

            return $value;
        }
    }
}
?>