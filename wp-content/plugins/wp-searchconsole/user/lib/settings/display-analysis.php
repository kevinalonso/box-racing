<?php
/**
 *
 * @package: /wpsearchconsole/user/lib/settings/
 * on: 26.05.2016
 *
 * Display of Search Analysis settings page.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the google auth display class
 */
if (!class_exists('wpsearchconsole_analysis_top_display')) {

    class wpsearchconsole_analysis_top_display
    {

        private $table_name;

        private $note;

        private $dates_data;
        private $type_data;
        private $device_data;
        private $country_data;
        private $pre_url_data;
        private $pre_query_data;

        public function __construct($displayForm = true)
        {

            global $wpdb;

            $this->note = __('NOTE: Metrics are calculated with your current combination of settings', 'wpsearchconsole');

            $this->table_name = $wpdb->prefix . 'wpsearchconsole_visitors';
            $this->dates_data = $this->dates();
            $this->type_data = $this->types();
            $this->device_data = $this->devices();
            $this->country_data = $this->countries();
            $this->pre_url_data = $this->url_pre();
            $this->pre_query_data = $this->query_pre();

            $this->open_wpsc_container();
            if ($displayForm) {
                $this->start_form();
            }
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
        //delete the data first
        public function delete_data($table_name)
        {

            global $wpdb;
            $wpdb->query("TRUNCATE TABLE $table_name");
        }

        //reset data
        public function reset($name)
        {

            if (isset($_POST[$name])) {

                delete_option('wpsearchconsole_analysis_datatype');
                delete_option('wpsearchconsole_analysis_operator');
                delete_option('wpsearchconsole_analysis_expression');
                delete_option('wpsearchconsole_analysis_param');
                delete_option('wpsearchconsole_analysis_value');

                delete_option('wpsearchconsole_analysis_requests');
                delete_option('wpsearchconsole_analysis_clicks');
                delete_option('wpsearchconsole_analysis_impressions');
                delete_option('wpsearchconsole_analysis_ctr');
                delete_option('wpsearchconsole_analysis_position');

                $this->delete_data($this->table_name);
            }
        }

        //process the data and send it to db
        public function process($name)
        {

            if (isset($_POST[$name])) {
                $ana = 'wpsearchconsole_analysis_';

                $param_type = sanitize_text_field($_POST[$ana . 'datatype']);
                switch ($param_type) {
                    case 'page':
                        $operator = (isset($_POST[$ana . 'page_select']) ? sanitize_text_field($_POST[$ana . 'page_select']) : false);
                        $expression = (isset($_POST[$ana . 'page_field']) ? sanitize_text_field($_POST[$ana . 'page_field']) : false);
                        break;
                    case 'request':
                        $operator = (isset($_POST[$ana . 'request_select']) ? sanitize_text_field($_POST[$ana . 'request_select']) : false);
                        $expression = (isset($_POST[$ana . 'request_field']) ? sanitize_text_field($_POST[$ana . 'request_field']) : false);
                        break;
                }

                $param = (isset($_POST[$ana . 'filter']) ? sanitize_text_field($_POST[$ana . 'filter']) : false);
                $value = (isset($_POST[$ana . $param]) ? sanitize_text_field($_POST[$ana . $param]) : false);

                $this->save_data($param_type, $operator, $expression, $param, $value);

                //call the data after saving the parameter on wpsearchconsole_analysis_top_display class
                $wpaca = new wpsearchconsole_api_call_analysis();
                if (!$wpaca->errors) {
                    return true;
                }
            }
            return false;
        }

        //Initiate the form here
        public function start_form()
        {

            $this->reset('wpsearchconsole_apply_analysis_reset');
            //$this->process( 'wpsearchconsole_apply_analysis_filter' );
            do_settings_sections('analysis_section');
            $this->get_data_inputs(); ?>
            <span class="alignright description"><?php echo $this->note; ?></span>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="wp-filter" style="padding: 20px;">
                    <div class="alignleft">
                        <?php $this->filter_check(); ?>
                    </div>
                    <div class="alignright">
                        <?php $field_value = (get_option('wpsearchconsole_analysis_point') ? get_option('wpsearchconsole_analysis_point') : false); ?>
                        <span id="wpsearchconsole_analysis_page" class="wpsearchconsole_analysis_datatype_hide">
							<?php
                            $this->dropdown('page_select', $this->pre_url_data);
                            $this->filter_field('wpsearchconsole_analysis_page_field', $field_value, 'http://example.com'); ?>
						</span>
                        <span id="wpsearchconsole_analysis_request" class="wpsearchconsole_analysis_datatype_hide">
							<?php
                            $this->dropdown('request_select', $this->pre_query_data);
                            $this->filter_field('wpsearchconsole_analysis_request_field', $field_value, __('keyword', 'wpsearchconsole')); ?>
						</span>
                        <?php
                        $this->dropdown('date', $this->dates_data);
                        $this->dropdown('type', $this->type_data);
                        $this->dropdown('device', $this->device_data);
                        $this->dropdown('country', $this->country_data);
                        submit_button(__('Refresh Data', 'wpsearchconsole'), 'primary', 'wpsearchconsole_apply_analysis_filter', false);
                        echo '&nbsp;&nbsp;&nbsp;';
                        submit_button(__('Reset', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_apply_analysis_reset', false); ?>
                        </fieldset>
                    </div>
                </div>
            </form>

            <!--For sending the value of chosen parameter to javascript-->
            <input type="hidden" id="wpsearchconsole_analysis_param"
                   value="<?php echo get_option('wpsearchconsole_analysis_param'); ?>"/>
            <fieldset>
                <?php $this->quick_view(); ?>
            </fieldset>
            <?php
        }

        //get filter data for javascript usage
        public function get_data_inputs()
        {

            $data = array(
                'param_type' => get_option('wpsearchconsole_analysis_datatype'),
                'operator' => get_option('wpsearchconsole_analysis_operator'),
                'expression' => get_option('wpsearchconsole_analysis_expression'),
                'param' => get_option('wpsearchconsole_analysis_param'),
                'value' => get_option('wpsearchconsole_analysis_value'),
            );

            foreach ($data as $key => $val) {
                echo '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
            }
        }

        //save form data
        public function save_data($param_type, $operator, $expression, $param, $value)
        {

            if ($param_type) {
                update_option('wpsearchconsole_analysis_datatype', $param_type);
            }

            if ($operator) {
                update_option('wpsearchconsole_analysis_operator', $operator);
            }

            if ($expression) {
                update_option('wpsearchconsole_analysis_expression', $expression);
            }

            if ($param) {
                update_option('wpsearchconsole_analysis_param', $param);
            }

            if ($value) {
                update_option('wpsearchconsole_analysis_value', $value);
            }

            if ($operator == 'all') {
                delete_option('wpsearchconsole_analysis_operator');
                delete_option('wpsearchconsole_analysis_expression');
            }
        }

        //define dropdown filter
        public function dropdown($name, $data)
        {

            if (!$data) {
                return;
            }
            ?>

            <select<?php echo($name != 'page_select' && $name != 'request_select' ? ' class="wpsearchconsole_analysis"' : false); ?>
                    id="wpsearchconsole_analysis_<?php echo $name; ?>"
                    name="wpsearchconsole_analysis_<?php echo $name; ?>">
                <?php foreach ($data as $key => $val): ?>
                    <option value="<?php echo $key; ?>" <?php echo selected($key, get_option('wpsearchconsole_analysis_value'), false); ?>><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }

        //Data quick view
        public function quick_view()
        {

            $parameters = array(
                array('clicks', __('Total Number of Clicks', 'wpsearchconsole'), get_option('wpsearchconsole_analysis_clicks')),
                array('impressions', __('Total Number of Impressions', 'wpsearchconsole'), get_option('wpsearchconsole_analysis_impressions')),
                array('ctr', __('CTR Average', 'wpsearchconsole'), get_option('wpsearchconsole_analysis_ctr')),
                array('avg_pos', __('Average Position', 'wpsearchconsole'), get_option('wpsearchconsole_analysis_position')),
                array('req', __('Total Requests', 'wpsearchconsole'), get_option('wpsearchconsole_analysis_requests')),
            ); ?>

            <?php foreach ($parameters as $params) {
            ?>
            <?php echo $params[1];
            $this->number_field($params[0], $params[2]); ?>
        <?php } ?>
            <?php
        }

        //Filters of analysis
        public function filter_check()
        {

            $parameters = array(
                array('wpsearchconsole_analysis_datatype', __('Requests', 'wpsearchconsole'), 'request', 'ff644d'),
                array('wpsearchconsole_analysis_datatype', __('Pages', 'wpsearchconsole'), 'page', 'ff644d'),
            );

            $filters = array(
                array('wpsearchconsole_analysis_filter', __('Countries', 'wpsearchconsole'), 'country', '8bba30'),
                array('wpsearchconsole_analysis_filter', __('Devices', 'wpsearchconsole'), 'device', '8bba30'),
                array('wpsearchconsole_analysis_filter', __('Type of Searches', 'wpsearchconsole'), 'type', '8bba30'),
                array('wpsearchconsole_analysis_filter', __('Date', 'wpsearchconsole'), 'date', '8bba30'),
            ); ?>
            <fieldset>
                <?php
                foreach ($parameters as $params) {
                    $checked = 1;
                    $disabled = 0;
                    $this->filter_radio_field($params[0], $checked, $disabled, $params[1], $params[2], $params[3]);
                } ?>
                <?php
                $count = 0;
                foreach ($filters as $params) {
                    if ($count == 0) {
                        $checked = 1;
                    } else {
                        $checked = 0;
                    }
                    $count == 1;
                    $disabled = 0;
                    $this->filter_radio_field($params[0], $checked, $disabled, $params[1], $params[2], $params[3]);
                } ?></fieldset> <?php
        }

        //checkbox fields
        public function filter_radio_field($name, $checked, $disabled, $label, $value, $color)
        {

            $param = get_option('wpsearchconsole_analysis_param');
            $type = get_option('wpsearchconsole_analysis_datatype', 'request');
            if ($value == 'request' || $value == 'page') {
                $checker = $type;
            } else {
                $checker = $param;
            } ?>

            <label><input type="radio" value="<?php echo $value; ?>"
                          name="<?php echo $name; ?>" <?php echo checked($value, $checker, false) . $disabled; ?> />
                <span style="border-bottom: 1px dotted #<?php echo $color; ?>;"><?php echo $label; ?></span></label>&nbsp;&nbsp;&nbsp;
            <?php
        }

        //quick view field
        public function number_field($name, $value)
        { ?>

            <input type="text" class="small-text" size="4" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
            <?php
        }

        //filter text field
        public function filter_field($name, $value, $placeholder)
        { ?>

            <input type="text" id="<?php echo $name; ?>" class="medium-text" placeholder="<?php echo $placeholder; ?>"
                   name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
            <?php
        }

        //date options
        public function dates()
        {

            return array(
                '7' => __('Last 7 days', 'wpsearchconsole'),
                '28' => __('Last 28 days', 'wpsearchconsole'),
                '90' => __('Last 90 days', 'wpsearchconsole'),
            );
        }

        //search type options
        public function types()
        {

            return array(
                'web' => __('Web', 'wpsearchconsole'),
                'image' => __('Image', 'wpsearchconsole'),
                'video' => __('Video', 'wpsearchconsole'),
            );
        }

        //device options
        public function devices()
        {

            return array(
                'desktop' => __('Desktop', 'wpsearchconsole'),
                'mobile' => __('Mobile', 'wpsearchconsole'),
                'tablet' => __('Tablet', 'wpsearchconsole'),
            );
        }

        //pages options
        public function url_pre()
        {

            return array(
                'all' => __('All URLs', 'wpsearchconsole'),
                'contains' => __('URLs containing', 'wpsearchconsole'),
                'notContains' => __('URLs not containing', 'wpsearchconsole'),
                'equals' => __('URL is exactly', 'wpsearchconsole'),
            );
        }

        //Queries options
        public function query_pre()
        {

            return array(
                'all' => __('All Queries', 'wpsearchconsole'),
                'contains' => __('Queries containing', 'wpsearchconsole'),
                'notContains' => __('Queries not containing', 'wpsearchconsole'),
                'equals' => __('Query is exactly', 'wpsearchconsole'),
            );
        }

        //websocket button
        public function websocket($title, $name, $page, $tab)
        {

            $show_tab = ($tab ? '&tab=' . $tab : false);
            echo '<a href="?page=' . $page . $show_tab . '&' . $name . '=true" class="page-title-action">' . $title . '</a>';
        }

        //device options
        public function countries()
        {

            return array(
                'ABW' => __('Aruba', 'wpsearchconsole'),
                'AFG' => __('Afghanistan', 'wpsearchconsole'),
                'AGO' => __('Angola', 'wpsearchconsole'),
                'AIA' => __('Anguilla', 'wpsearchconsole'),
                'ALA' => __('Åland Islands', 'wpsearchconsole'),
                'ALB' => __('Albania', 'wpsearchconsole'),
                'AND' => __('Andorra', 'wpsearchconsole'),
                'ARE' => __('United Arab Emirates', 'wpsearchconsole'),
                'ARG' => __('Argentina', 'wpsearchconsole'),
                'ARM' => __('Armenia', 'wpsearchconsole'),
                'ASM' => __('American Samoa', 'wpsearchconsole'),
                'ATA' => __('Antarctica', 'wpsearchconsole'),
                'ATF' => __('French Southern Territories', 'wpsearchconsole'),
                'ATG' => __('Antigua and Barbuda', 'wpsearchconsole'),
                'AUS' => __('Australia', 'wpsearchconsole'),
                'AUT' => __('Austria', 'wpsearchconsole'),
                'AZE' => __('Azerbaijan', 'wpsearchconsole'),
                'BDI' => __('Burundi', 'wpsearchconsole'),
                'BEL' => __('Belgium', 'wpsearchconsole'),
                'BEN' => __('Benin', 'wpsearchconsole'),
                'BES' => __('Bonaire, Sint Eustatius and Saba', 'wpsearchconsole'),
                'BFA' => __('Burkina Faso', 'wpsearchconsole'),
                'BGD' => __('Bangladesh', 'wpsearchconsole'),
                'BGR' => __('Bulgaria', 'wpsearchconsole'),
                'BHR' => __('Bahrain', 'wpsearchconsole'),
                'BHS' => __('Bahamas', 'wpsearchconsole'),
                'BIH' => __('Bosnia and Herzegovina', 'wpsearchconsole'),
                'BLM' => __('Saint Barthélemy', 'wpsearchconsole'),
                'BLR' => __('Belarus', 'wpsearchconsole'),
                'BLZ' => __('Belize', 'wpsearchconsole'),
                'BMU' => __('Bermuda', 'wpsearchconsole'),
                'BOL' => __('Bolivia, Plurinational State of', 'wpsearchconsole'),
                'BRA' => __('Brazil', 'wpsearchconsole'),
                'BRB' => __('Barbados', 'wpsearchconsole'),
                'BRN' => __('Brunei Darussalam', 'wpsearchconsole'),
                'BTN' => __('Bhutan', 'wpsearchconsole'),
                'BVT' => __('Bouvet Island', 'wpsearchconsole'),
                'BWA' => __('Botswana', 'wpsearchconsole'),
                'CAF' => __('Central African Republic', 'wpsearchconsole'),
                'CAN' => __('Canada', 'wpsearchconsole'),
                'CCK' => __('Cocos (Keeling) Islands', 'wpsearchconsole'),
                'CHE' => __('Switzerland', 'wpsearchconsole'),
                'CHL' => __('Chile', 'wpsearchconsole'),
                'CHN' => __('China', 'wpsearchconsole'),
                'CIV' => __('Côte d\'Ivoire', 'wpsearchconsole'),
                'CMR' => __('Cameroon', 'wpsearchconsole'),
//						'COD' => __( 'Congo, the Democratic Republic of the', 'wpsearchconsole' ),
                'COG' => __('Congo', 'wpsearchconsole'),
                'COK' => __('Cook Islands', 'wpsearchconsole'),
                'COL' => __('Colombia', 'wpsearchconsole'),
                'COM' => __('Comoros', 'wpsearchconsole'),
                'CPV' => __('Cabo Verde', 'wpsearchconsole'),
                'CRI' => __('Costa Rica', 'wpsearchconsole'),
                'CUB' => __('Cuba', 'wpsearchconsole'),
                'CUW' => __('Curaçao', 'wpsearchconsole'),
                'CXR' => __('Christmas Island', 'wpsearchconsole'),
                'CYM' => __('Cayman Islands', 'wpsearchconsole'),
                'CYP' => __('Cyprus', 'wpsearchconsole'),
                'CZE' => __('Czech Republic', 'wpsearchconsole'),
                'DEU' => __('Germany', 'wpsearchconsole'),
                'DJI' => __('Djibouti', 'wpsearchconsole'),
                'DMA' => __('Dominica', 'wpsearchconsole'),
                'DNK' => __('Denmark', 'wpsearchconsole'),
                'DOM' => __('Dominican Republic', 'wpsearchconsole'),
                'DZA' => __('Algeria', 'wpsearchconsole'),
                'ECU' => __('Ecuador', 'wpsearchconsole'),
                'EGY' => __('Egypt', 'wpsearchconsole'),
                'ERI' => __('Eritrea', 'wpsearchconsole'),
                'ESH' => __('Western Sahara', 'wpsearchconsole'),
                'ESP' => __('Spain', 'wpsearchconsole'),
                'EST' => __('Estonia', 'wpsearchconsole'),
                'ETH' => __('Ethiopia', 'wpsearchconsole'),
                'FIN' => __('Finland', 'wpsearchconsole'),
                'FJI' => __('Fiji', 'wpsearchconsole'),
                'FLK' => __('Falkland Islands (Malvinas)', 'wpsearchconsole'),
                'FRA' => __('France', 'wpsearchconsole'),
                'FRO' => __('Faroe Islands', 'wpsearchconsole'),
                'FSM' => __('Micronesia, Federated States of', 'wpsearchconsole'),
                'GAB' => __('Gabon', 'wpsearchconsole'),
                'GBR' => __('United Kingdom', 'wpsearchconsole'),
                'GEO' => __('Georgia', 'wpsearchconsole'),
                'GGY' => __('Guernsey', 'wpsearchconsole'),
                'GHA' => __('Ghana', 'wpsearchconsole'),
                'GIB' => __('Gibraltar', 'wpsearchconsole'),
                'GIN' => __('Guinea', 'wpsearchconsole'),
                'GLP' => __('Guadeloupe', 'wpsearchconsole'),
                'GMB' => __('Gambia', 'wpsearchconsole'),
                'GNB' => __('Guinea-Bissau', 'wpsearchconsole'),
                'GNQ' => __('Equatorial Guinea', 'wpsearchconsole'),
                'GRC' => __('Greece', 'wpsearchconsole'),
                'GRD' => __('Grenada', 'wpsearchconsole'),
                'GRL' => __('Greenland', 'wpsearchconsole'),
                'GTM' => __('Guatemala', 'wpsearchconsole'),
                'GUF' => __('French Guiana', 'wpsearchconsole'),
                'GUM' => __('Guam', 'wpsearchconsole'),
                'GUY' => __('Guyana', 'wpsearchconsole'),
                'HKG' => __('Hong Kong', 'wpsearchconsole'),
//						'HMD' => __( 'Heard Island and McDonald Islands', 'wpsearchconsole' ),
                'HND' => __('Honduras', 'wpsearchconsole'),
                'HRV' => __('Croatia', 'wpsearchconsole'),
                'HTI' => __('Haiti', 'wpsearchconsole'),
                'HUN' => __('Hungary', 'wpsearchconsole'),
                'IDN' => __('Indonesia', 'wpsearchconsole'),
                'IMN' => __('Isle of Man', 'wpsearchconsole'),
                'IND' => __('India', 'wpsearchconsole'),
                'IOT' => __('British Indian Ocean Territory', 'wpsearchconsole'),
                'IRL' => __('Ireland', 'wpsearchconsole'),
                'IRN' => __('Iran, Islamic Republic of', 'wpsearchconsole'),
                'IRQ' => __('Iraq', 'wpsearchconsole'),
                'ISL' => __('Iceland', 'wpsearchconsole'),
                'ISR' => __('Israel', 'wpsearchconsole'),
                'ITA' => __('Italy', 'wpsearchconsole'),
                'JAM' => __('Jamaica', 'wpsearchconsole'),
                'JEY' => __('Jersey', 'wpsearchconsole'),
                'JOR' => __('Jordan', 'wpsearchconsole'),
                'JPN' => __('Japan', 'wpsearchconsole'),
                'KAZ' => __('Kazakhstan', 'wpsearchconsole'),
                'KEN' => __('Kenya', 'wpsearchconsole'),
                'KGZ' => __('Kyrgyzstan', 'wpsearchconsole'),
                'KHM' => __('Cambodia', 'wpsearchconsole'),
                'KIR' => __('Kiribati', 'wpsearchconsole'),
                'KNA' => __('Saint Kitts and Nevis', 'wpsearchconsole'),
                'KOR' => __('Korea, Republic of', 'wpsearchconsole'),
                'KWT' => __('Kuwait', 'wpsearchconsole'),
//						'LAO' => __( 'Lao People\'s Democratic Republic', 'wpsearchconsole' ),
                'LBN' => __('Lebanon', 'wpsearchconsole'),
                'LBR' => __('Liberia', 'wpsearchconsole'),
                'LBY' => __('Libya', 'wpsearchconsole'),
                'LCA' => __('Saint Lucia', 'wpsearchconsole'),
                'LIE' => __('Liechtenstein', 'wpsearchconsole'),
                'LKA' => __('Sri Lanka', 'wpsearchconsole'),
                'LSO' => __('Lesotho', 'wpsearchconsole'),
                'LTU' => __('Lithuania', 'wpsearchconsole'),
                'LUX' => __('Luxembourg', 'wpsearchconsole'),
                'LVA' => __('Latvia', 'wpsearchconsole'),
                'MAC' => __('Macao', 'wpsearchconsole'),
                'MAF' => __('Saint Martin (French part)', 'wpsearchconsole'),
                'MAR' => __('Morocco', 'wpsearchconsole'),
                'MCO' => __('Monaco', 'wpsearchconsole'),
                'MDA' => __('Moldova, Republic of', 'wpsearchconsole'),
                'MDG' => __('Madagascar', 'wpsearchconsole'),
                'MDV' => __('Maldives', 'wpsearchconsole'),
                'MEX' => __('Mexico', 'wpsearchconsole'),
                'MHL' => __('Marshall Islands', 'wpsearchconsole'),
//						'MKD' => __( 'Macedonia, the former Yugoslav Republic of', 'wpsearchconsole' ),
                'MLI' => __('Mali', 'wpsearchconsole'),
                'MLT' => __('Malta', 'wpsearchconsole'),
                'MMR' => __('Myanmar', 'wpsearchconsole'),
                'MNE' => __('Montenegro', 'wpsearchconsole'),
                'MNG' => __('Mongolia', 'wpsearchconsole'),
                'MNP' => __('Northern Mariana Islands', 'wpsearchconsole'),
                'MOZ' => __('Mozambique', 'wpsearchconsole'),
                'MRT' => __('Mauritania', 'wpsearchconsole'),
                'MSR' => __('Montserrat', 'wpsearchconsole'),
                'MTQ' => __('Martinique', 'wpsearchconsole'),
                'MUS' => __('Mauritius', 'wpsearchconsole'),
                'MWI' => __('Malawi', 'wpsearchconsole'),
                'MYS' => __('Malaysia', 'wpsearchconsole'),
                'MYT' => __('Mayotte', 'wpsearchconsole'),
                'NAM' => __('Namibia', 'wpsearchconsole'),
                'NCL' => __('New Caledonia', 'wpsearchconsole'),
                'NER' => __('Niger', 'wpsearchconsole'),
                'NFK' => __('Norfolk Island', 'wpsearchconsole'),
                'NGA' => __('Nigeria', 'wpsearchconsole'),
                'NIC' => __('Nicaragua', 'wpsearchconsole'),
                'NIU' => __('Niue', 'wpsearchconsole'),
                'NLD' => __('Netherlands', 'wpsearchconsole'),
                'NOR' => __('Norway', 'wpsearchconsole'),
                'NPL' => __('Nepal', 'wpsearchconsole'),
                'NRU' => __('Nauru', 'wpsearchconsole'),
                'NZL' => __('New Zealand', 'wpsearchconsole'),
                'OMN' => __('Oman', 'wpsearchconsole'),
                'PAK' => __('Pakistan', 'wpsearchconsole'),
                'PAN' => __('Panama', 'wpsearchconsole'),
                'PCN' => __('Pitcairn', 'wpsearchconsole'),
                'PER' => __('Peru', 'wpsearchconsole'),
                'PHL' => __('Philippines', 'wpsearchconsole'),
                'PLW' => __('Palau', 'wpsearchconsole'),
                'PNG' => __('Papua New Guinea', 'wpsearchconsole'),
                'POL' => __('Poland', 'wpsearchconsole'),
                'PRI' => __('Puerto Rico', 'wpsearchconsole'),
//						'PRK' => __( 'Korea, Democratic People\'s Republic of', 'wpsearchconsole' ),
                'PRT' => __('Portugal', 'wpsearchconsole'),
                'PRY' => __('Paraguay', 'wpsearchconsole'),
                'PSE' => __('Palestine, State of', 'wpsearchconsole'),
                'PYF' => __('French Polynesia', 'wpsearchconsole'),
                'QAT' => __('Qatar', 'wpsearchconsole'),
                'REU' => __('Réunion', 'wpsearchconsole'),
                'ROU' => __('Romania', 'wpsearchconsole'),
                'RUS' => __('Russian Federation', 'wpsearchconsole'),
                'RWA' => __('Rwanda', 'wpsearchconsole'),
                'SAU' => __('Saudi Arabia', 'wpsearchconsole'),
                'SDN' => __('Sudan', 'wpsearchconsole'),
                'SEN' => __('Senegal', 'wpsearchconsole'),
                'SGP' => __('Singapore', 'wpsearchconsole'),
//						'SGS' => __( 'South Georgia and South Sandwich Islands', 'wpsearchconsole' ),
                //						'SHN' => __( 'Saint Helena, Ascension and Tristan da Cunha', 'wpsearchconsole' ),
                'SJM' => __('Svalbard and Jan Mayen', 'wpsearchconsole'),
                'SLB' => __('Solomon Islands', 'wpsearchconsole'),
                'SLE' => __('Sierra Leone', 'wpsearchconsole'),
                'SLV' => __('El Salvador', 'wpsearchconsole'),
                'SMR' => __('San Marino', 'wpsearchconsole'),
                'SOM' => __('Somalia', 'wpsearchconsole'),
                'SPM' => __('Saint Pierre and Miquelon', 'wpsearchconsole'),
                'SRB' => __('Serbia', 'wpsearchconsole'),
                'SSD' => __('South Sudan', 'wpsearchconsole'),
                'STP' => __('Sao Tome and Principe', 'wpsearchconsole'),
                'SUR' => __('Suriname', 'wpsearchconsole'),
                'SVK' => __('Slovakia', 'wpsearchconsole'),
                'SVN' => __('Slovenia', 'wpsearchconsole'),
                'SWE' => __('Sweden', 'wpsearchconsole'),
                'SWZ' => __('Swaziland', 'wpsearchconsole'),
                'SXM' => __('Sint Maarten (Dutch part)', 'wpsearchconsole'),
                'SYC' => __('Seychelles', 'wpsearchconsole'),
                'SYR' => __('Syrian Arab Republic', 'wpsearchconsole'),
                'TCA' => __('Turks and Caicos Islands', 'wpsearchconsole'),
                'TCD' => __('Chad', 'wpsearchconsole'),
                'TGO' => __('Togo', 'wpsearchconsole'),
                'THA' => __('Thailand', 'wpsearchconsole'),
                'TJK' => __('Tajikistan', 'wpsearchconsole'),
                'TKL' => __('Tokelau', 'wpsearchconsole'),
                'TKM' => __('Turkmenistan', 'wpsearchconsole'),
                'TLS' => __('Timor-Leste', 'wpsearchconsole'),
                'TON' => __('Tonga', 'wpsearchconsole'),
                'TTO' => __('Trinidad and Tobago', 'wpsearchconsole'),
                'TUN' => __('Tunisia', 'wpsearchconsole'),
                'TUR' => __('Turkey', 'wpsearchconsole'),
                'TUV' => __('Tuvalu', 'wpsearchconsole'),
                'TWN' => __('Taiwan, Province of China', 'wpsearchconsole'),
                'TZA' => __('Tanzania, United Republic of', 'wpsearchconsole'),
                'UGA' => __('Uganda', 'wpsearchconsole'),
                'UKR' => __('Ukraine', 'wpsearchconsole'),
//						'UMI' => __( 'United States Minor Outlying Islands', 'wpsearchconsole' ),
                'URY' => __('Uruguay', 'wpsearchconsole'),
                'USA' => __('United States of America', 'wpsearchconsole'),
                'UZB' => __('Uzbekistan', 'wpsearchconsole'),
                'VAT' => __('Holy See (Vatican City State)', 'wpsearchconsole'),
                'VCT' => __('Saint Vincent and the Grenadines', 'wpsearchconsole'),
                'VEN' => __('Venezuela, Bolivarian Republic of', 'wpsearchconsole'),
                'VGB' => __('Virgin Islands, British', 'wpsearchconsole'),
                'VIR' => __('Virgin Islands, U.S.', 'wpsearchconsole'),
                'VNM' => __('Viet Nam', 'wpsearchconsole'),
                'VUT' => __('Vanuatu', 'wpsearchconsole'),
                'WLF' => __('Wallis and Futuna', 'wpsearchconsole'),
                'WSM' => __('Samoa', 'wpsearchconsole'),
                'YEM' => __('Yemen', 'wpsearchconsole'),
                'ZAF' => __('South Africa', 'wpsearchconsole'),
                'ZMB' => __('Zambia', 'wpsearchconsole'),
                'ZWE' => __('Zimbabwe', 'wpsearchconsole'),
            );
        }

    }
} ?>