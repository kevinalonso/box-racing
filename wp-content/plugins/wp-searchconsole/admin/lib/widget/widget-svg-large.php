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
if (!class_exists('wpsearchconsole_svg_large_widget')) {

    class wpsearchconsole_svg_large_widget
    {

        private $table_name;
        private $name;

        public function __construct($name)
        {

            global $wpdb;

            $this->name = $name;
            $this->table_name = $wpdb->prefix . 'wpsearchconsole_json';
            $this->token = get_option('wpsearchconsole_mitambo');
            $this->qualityArray = array(
                'all' => __('All', 'wpsearchconsole'),
                'very-good' => __('Very Good', 'wpsearchconsole'),
                'good' => __('Good', 'wpsearchconsole'),
                'fair' => __('Fair', 'wpsearchconsole'),
                'bad' => __('Bad', 'wpsearchconsole')
            );
        }

        //html output
        public function html()
        {

            //	$output = $this->call_data( get_option( 'wpsearchconsole_' . $this->name . '_ID' ) );

            if ($this->name == 'svg-exposition') {

                $output = $this->svg_large('semtree');

            } elseif ($this->name == 'svg-intention') {

                $output = $this->svg_large('vistree');

            } elseif ($this->name == 'svg-comparison') {

                $output = $this->svg_comparison();

            } else {

                $output = 'chart';

            }

            return $output;
        }

        //output html generator function
        public function svg_large($link)
        {

            $output = '<div id="Viewport" class="SemTree_Viewport"></div>' . "\r\n";
            $output .= '<div id="AdjustButtons" class="SemTree_Buttons"></div>' . "\r\n";
            $output .= '<div id="ViewportComment" class="SemTree_CommentWindow"></div>' . "\r\n";
            $output .= '<div id="TextViewport" class="SemTree_TextViewport"></div>' . "\r\n";

            $output .= '<script>' . "\r\n";
            $output .= 'var weblink = function(){' . "\r\n";
            $output .= 'links=["' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-' . $link . '"];' . "\r\n";
            $output .= 'return links[Math.floor(Math.random() * links.length)];' . "\r\n";
            $output .= '};' . "\r\n";
            $output .= 'jQuery(document).ready(function () {' . "\r\n";
            $output .= 'CallDisplaySimpleSemTree(weblink(),"' . $this->token . '","Viewport","TextViewport","AdjustButtons","ViewportComment")' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '// TODO add an onresize on the div' . "\r\n";
            $output .= 'var resizeTimer;' . "\r\n";
            $output .= 'jQuery(window).on("resize",function () {' . "\r\n";
            $output .= 'clearTimeout(resizeTimer);' . "\r\n";
            $output .= 'resizeTimer = setTimeout(CallDisplaySimpleSemTree.bind(null, weblink(),"' . $this->token . '", "Viewport", "TextViewport", "AdjustButtons", "ViewportComment"), 100);' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '</script>' . "\r\n";

            return $output;
        }

        //comparison svg
        public function svg_comparison()
        {
            $output = '<script>' . "\r\n";
            $output .= 'var svg_translation={ "t1" :' . json_encode(__("Underexposed", "wpsearchconsole")) . ',';
            $output .= '"t2" :' . json_encode(__("Search engines should pay", "wpsearchconsole")) . ',';
            $output .= '"t3" :' . json_encode(__("more attention to those words !", "wpsearchconsole")) . ',';
            $output .= '"t4" :' . json_encode(__("Overexposed", "wpsearchconsole")) . ',';
            $output .= '"t5" :' . json_encode(__("Search engine see words that are", "wpsearchconsole")) . ',';
            $output .= '"t6" :' . json_encode(__("not important for you !", "wpsearchconsole")) . ',';

            $output .= "}\r\n";
            $output .= '</script>' . "\r\n";
            $output .= '<div class="quality-filter-wrapper">';
            $output .= '<ul class="wpsearchconsole-subtab">';
            foreach ($this->qualityArray as $key => $val) {
                $class = ($key == 'all') ? 'active' : '';
                $output .= '<li class="' . $class . '">';
                $output .= '<a href="javascript:void(0)" data-quality="' . $key . '" class="quality-filter">';
                $output .= $val;
                $output .= '</a>';
                $output .= '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '<div style="background: #7e8c80;" class="DefaultText">' . "\r\n";
            $output .= '<div id="Viewport" class="SemTree_Viewport"></div>' . "\r\n";
            $output .= '<div id="Result"></div>' . "\r\n";
            $output .= '<div id="ViewportComment" class="SemTree_CommentWindow"></div>' . "\r\n";
            $output .= '<div id="TextViewport" class="SemTree_TextViewport"></div>' . "\r\n";

            $output .= '<script>' . "\r\n";
            $output .= 'var links=["' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-exposition","' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-intention"];' . "\r\n";
            $output .= 'var weblink = function(){' . "\r\n";
            $output .= 'return links[Math.floor(Math.random() * links.length)];' . "\r\n";
            $output .= '};' . "\r\n";
            $output .= 'var Exposition= [];' . "\r\n";
            $output .= 'var Intention= [];' . "\r\n";
            $output .= 'function getExposition(){' . "\r\n";
            $output .= 'return jQuery.ajax({' . "\r\n";
            $output .= 'type: "GET",' . "\r\n";
            $output .= 'beforeSend: function(xhr) { xhr.setRequestHeader( "Authorization", "Bearer ' . $this->token . '" ); },' . "\r\n";
            $output .= 'url: "' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-exposition",' . "\r\n";
            $output .= 'dataType:   "json",' . "\r\n";
            $output .= 'contentType: "application/json;",' . "\r\n";
            $output .= 'success:    function(data){ ' . "\r\n";
            $output .= 'Exposition= (typeof data == "string") ?  JSON.parse(data) : data;' . "\r\n";
            $output .= '}' . "\r\n";
            $output .= '}).done(function(data){
					Exposition= (typeof data == "string") ?  JSON.parse(data) : data;
			});' . "\r\n";
            $output .= '}' . "\r\n";
            $output .= 'function getIntention(){' . "\r\n";
            $output .= 'return jQuery.ajax({' . "\r\n";
            $output .= 'type: "GET",' . "\r\n";
            $output .= 'url: "' . WPSEARCHCONSOLE_PLUGIN_API . '/data/svg-intention",' . "\r\n";
            $output .= 'beforeSend: function(xhr){xhr.setRequestHeader("Authorization", "Bearer ' . $this->token . '");},' . "\r\n";
            $output .= 'dataType:   "json",' . "\r\n";
            $output .= 'contentType: "application/json;",' . "\r\n";
            $output .= 'success:    function(data){' . "\r\n";
            $output .= 'Intention= (typeof data == "string") ?  JSON.parse(data) : data;' . "\r\n";
            $output .= '}' . "\r\n";
            $output .= '}).done(function(data){
					Intention= (typeof data == "string") ?  JSON.parse(data) : data;
			});' . "\r\n";
            $output .= '}' . "\r\n";
            $output .= 'function CallDisplaySimpleComparison(targetID,ResultID,Max){' . "\r\n";
            $output .= 'DisplayGraph(targetID,Exposition,Intention,svg_translation);' . "\r\n";
            $output .= 'GlobalQuality(ResultID,Max);' . "\r\n";
            $output .= 'window.zoomTiger = svgPanZoom("#MitamboSVG", {zoomEnabled: true,controlIconsEnabled: true,fit: true,center: true});' . "\r\n";
            $output .= '}' . "\r\n";
            $output .= 'jQuery.when(getExposition(), getIntention()).done(function(res1, res2 ){' . "\r\n";
            $output .= 'CallDisplaySimpleComparison("Viewport","Result",100)' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '// TODO add an onresize on the div' . "\r\n";
            $output .= 'var resizeTimer;' . "\r\n";
            $output .= 'jQuery(window).on("resize",function () {' . "\r\n";
            $output .= 'clearTimeout(resizeTimer);' . "\r\n";
            $output .= 'resizeTimer = setTimeout(CallDisplaySimpleComparison.bind(null, "Viewport","Result",100), 100);' . "\r\n";
            $output .= '});' . "\r\n";
            $output .= '</script>' . "\r\n";
            $output .= '</div>' . "\r\n";

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
} ?>