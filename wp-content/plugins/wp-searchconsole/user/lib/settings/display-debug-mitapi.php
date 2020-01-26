<?php
/**
 *
 * @package: wpsearchconsole/user/lib/settings/
 * on: 30.09.2016
 * @since 0.8.18
 *
 * Display debug information to user
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to display all debug info
 */
if (!class_exists('wpsearchconsole_display_debug_mitapi')) {
    class wpsearchconsole_display_debug_mitapi
    {
        private $api_list;
        private $mitambo_json_api;
        private $access_token;
        private $apicall;
        private $token;
        private $url;
        private $start;
        private $limit;

        function __construct($apicall = null, $token = null, $url = null, $start = null, $limit = null)
        {
            $this->apicall = $apicall;

            $this->url = $url;
            $this->start = $start;
            $this->limit = $limit;

            $this->mitambo_json_api = new wpsearchconsole_prepare_mitambo_api(true);
            $this->lastCollectDateArray = get_option('wpsearchconsole_mitambo_last_crawled_date');
            $this->access_token = get_option('wpsearchconsole_mitambo');
            $this->token = isset($token) ? $token : $this->access_token;
            $this->errors = array();
            $this->api_version = array("/", "/v2");
            $this->api_list = array("/global_internal_competition", "/global_duplicate_perception", "/global_duplicate_title", "/global_duplicate_desc", "/global_duplicate_content", "/crawler/lastCollectDate", "/crawler/status", "/subscription/status", "/page/status", "/main_statistics", "/server_header", "/simple_keywords", "/double_keywords", "/triple_keywords", "/meta_tags", "/main_html_tags", "/charset", "/link_analysis", "/link_analysis/summary", "/link_analysis/inbounds", "/link_analysis/outbounds", "/characteristic_keywords", "/perceptions", "/post_duplicate_title", "/post_duplicate_desc", "/post_duplicate_content", "/post_duplicate_perception", "/top_keywords/1", "/top_keywords/2", "/top_keywords/3", "/top_keywords/4", "/top_keywords/5", "/internal_by_status/301", "/internal_by_status/302", "/internal_by_status/307", "/internal_by_status/404", "/internal_by_status/500", "/resources_by_status/404", "/resources_by_status/500", "/resources_by_status/301", "/resources_by_status/302", "/resources_by_status/307", "/outgoing_by_status/301", "/outgoing_by_status/302", "/outgoing_by_status/307", "/outgoing_by_status/404", "/outgoing_by_status/500");
            $this->open_wpsc_container();
            $this->display_debug_form();
            if (isset($apicall)) {
                $this->mitambo_json_api->set_api_args($token, $url, $start, $limit);
                if (isset($this->lastCollectDateArray)){
                    list($md5, $called_url) = $this->mitambo_json_api->generate_md5( $this->lastCollectDateArray,$apicall,  $url);
                } else {
                     list($md5, $called_url) = $this->mitambo_json_api->generate_md5($this->mitambo_json_api->last_collect_date, $apicall,  $url);
                }

                $result = $this->mitambo_json_api->debug_request($called_url, $md5, true);
                $this->errors = $result['message'];
                $this->display_debug_request($result);
            }
            $this->close_wpsc_container();
        }

        function __destruct()
        {
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
        function display_debug_form()
        {
            ?>
            <div class="inside">
                <form action="<?php echo esc_url(admin_url('admin.php?page=wpsearchconsole&tab=debugmitapi')); ?>"
                      method="POST">
                    <p><label>Mitambo API Call</label><select name="api-call">
                            <?php
                            foreach ($this->api_list as $value) {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }; ?>
                        </select>
                    </p>
                    <p><label>Bearer Key</label><input type="text" name="BearerToken" size="120"
                                                       value="<?php echo $this->token ?>"></p>
                    <p><label>X-Mitambo-Url</label><input type="text" name="X-Mitambo-Url" size="120"
                                                          value="<?php echo $this->url ?>"></p>
                    <p><label>X-Mitambo-Start</label><input type="text" name="X-Mitambo-Start" size="10"
                                                            value="<?php echo $this->start ?>"></p>
                    <p><label>X-Mitambo-Limit</label><input type="text" name="X-Mitambo-Limit" size="10"
                                                            value="<?php echo $this->limit ?>"></p>
                    <?php submit_button(__('FetchData', 'wpsearchconsole'), 'primary', 'wpsc_mitambo_test_api', false); ?>
                </form>
            </div>
        <?php }


        function display_debug_request($result)
        {
            var_dump($_POST);
            ?>    <h4>Result</h4>
            <div class="inside">
                <?php echo var_dump($result) ?>
            </div>
        <?php }


    }

}
