<?php
/**
 *
 * @package: wpsearchconsole/core/lib/jwt/
 * on: 19.05.2015
 * @since 0.1
 * @modified: 1
 *
 * API call with JWT authentication object to Mitambo server.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_prepare_mitambo_api')) {

    class wpsearchconsole_prepare_mitambo_api
    {

        private $cacheTable;
        private $api_base;
        private $token;
        private $api_args;
        private $wpdb;
        public $last_collect_date;
        public $debug;


        public function __construct($debug = false)
        {
            global $wpdb;
            $this->debug = WPSEARCHCONSOLE_DEBUG;
            $this->wpdb = $wpdb;
            $this->cacheTable = $wpdb->prefix . "wpsearchconsole_cache";
            $this->cacheDataTable = $wpdb->prefix . "wpsearchconsole_data";
            $this->api_base = WPSEARCHCONSOLE_PLUGIN_API;
            $this->api_jwt_base = WPSEARCHCONSOLE_PLUGIN_JWT_API;
            $this->token = get_option('wpsearchconsole_mitambo');
            $this->api_collectdate = $this->api_jwt_base . '/crawler/lastCollectDate';
            $this->api_deactivate = $this->api_jwt_base . '/crawler/deactivate';
            $this->api_args = array('Authorization' => 'Bearer' . ' ' . $this->token);

            if ($collect_date = $this->lastCollectDate()) {

                if (array_key_exists('lastCollectDate', $collect_date)) {
                    $this->last_collect_date = $collect_date->lastCollectDate;
                } else {
                    $this->last_collect_date = false;
                }

            } else {
                $this->last_collect_date = false;
            }

        }

        //make the api call globally
        public function api_call_global($type, $subtype = null)
        {
            list($md5, $called_url) = $this->generate_md5($this->last_collect_date,$type,null, $subtype);

            return $this->request($called_url, $md5);
        }

        // api call with start and limit
        public function api_call_data_list($type, $start = 0, $limit = 25)
        {
            $api_args = array();
            $api_args['X-Mitambo-Start'] = $start ? $start : 0;
            $api_args['X-Mitambo-Limit'] = $limit ? $limit : 25;
            list($md5, $called_url) = $this->generate_md5($this->last_collect_date,$type, null, null, $start, $limit);

            return $this->request($called_url, $md5, $api_args);
        }

        //make the api call for an URL
        public function api_call($permalink, $type)
        {
            $permalink = wpsc_repair_protocol($permalink);
            $api_args = array();
            $api_args['X-Mitambo-Url'] = $permalink;
            list($md5, $called_url) = $this->generate_md5($this->last_collect_date,$type, $permalink);

            return $this->request($called_url, $md5, $api_args);
        }

        //retreive last Collect Date
        public function lastCollectDate()
        {
            if (!$this->token) {
                return false;
            }
            $call_args = array(
                'headers' => $this->api_args,
                'sslverify' => false,
                'timeout' => 10,
            );
            $result = wp_remote_get($this->api_collectdate, $call_args);
            if (!is_wp_error($result) && wp_remote_retrieve_response_code($result) == 200) {
                $responseJson = wp_remote_retrieve_body($result);
                $responseArray = !empty($responseJson) ? json_decode($responseJson) : array();
                if (!is_wp_error($responseArray)) {
                    return $responseArray;
                }

            }
            return false;
        }

        //get Page Status for an url
        public function api_status()
        {
            $type = 'page/status';
            $posts = get_posts(array('numberposts' => 1));
            if (!$posts) {
                return true;
            } else {
                $permalink = get_permalink($posts[0]->ID);
                $permalink = wpsc_repair_protocol($permalink);
                $api_args = array();
                $api_args['X-Mitambo-Url'] = $permalink;
                list($md5, $called_url) = $this->generate_md5($this->last_collect_date,$type, $permalink);

                $response = $this->request($called_url, $md5, $api_args, false);
                if (!$response['error']) {
                    return true;
                } else {
                    return $response['message'];
                }
            }
            return true;
        }

        public function set_api_args($specific_token = null, $permalink = null, $start = null, $limit = null)
        {
            $this->api_args = array('Authorization' => 'Bearer' . ' ' . (isset($specific_token) ? $specific_token : $this->token));

            if (isset($permalink) && !empty($permalink)) {
                $this->api_args['X-Mitambo-Url'] = $permalink;
            }
            if (isset($start) && !empty($start)) {
                $this->api_args['X-Mitambo-Start'] = $start;
            }
            if (isset($limit) && !empty($limit)) {
                $this->api_args['X-Mitambo-Limit'] = $limit;
            }

        }

        public function generate_md5($last_collect_date,$type,$permalink = null,$subtype = null, $start = null, $limit = null)
        {
            $main = $type;
            switch ($type) {
                case 'resume':
                    $main = 'characteristic_keywords';
                    break;
                case 'simple-keyword':
                    $main = 'simple_keywords';
                    break;
                case 'double-keyword':
                    $main = 'double_keywords';
                    break;
                case 'triple-keyword':
                    $main = 'triple_keywords';
                    break;
            };
            if ($main[0] != '/') {
                $main = '/' . $main;
            }
            $chaineMd5 = '/data' . $main;
            if ($subtype == 'duplication') {
                $subtype = 'DuplicateGroups';
            }
            if ($type == 'top_keywords' || $type == 'internal_by_status') {
                $chaineMd5 .= '/' . $subtype;
            }
            $url = $this->api_base . $chaineMd5;

            if (isset($permalink) && !empty($permalink)) {
                $chaineMd5 .= 'X-Mitambo-Url:' . $permalink;
            }
            if (isset($start) && !empty($start)) {
                $chaineMd5 .= 'X-Mitambo-Start:' . $start;
            }
            if (isset($limit) && !empty($limit)) {
                $chaineMd5 .= 'X-Mitambo-Limit:' . $limit;
            }
            if (!isset($last_collect_date)){
                $last_collect_date=$this->last_collect_date;
            }
            if ($last_collect_date){
                $chaineMd5 .= 'Last-Collect-Date:' . $last_collect_date;
            }
            $md5 = md5($chaineMd5);

            return array($md5, $url);
        }


        public function debug_request($called_url, $md5, $cache = true)
        {
            $api_args = $this->api_args;

            $call_args = array(
                'headers' => $api_args,
                'sslverify' => false,
                'timeout' => 10,
            );

            if ($cache && $this->hasCache($md5)) {

                wpsc_myLogs("cache $md5 used for $called_url ");
                $result = array('error' => false, 'message' => '', 'datas' => $this->getCache($md5), 'md5' => $md5, 'cache' => true, 'last_collect_date' => $this->last_collect_date);
            } else {
                wpsc_myLogs("cache $md5 not exist for $called_url ");
                $response = wp_remote_get($called_url, $call_args);
                if ($this->debug) {
                    wpsc_var2log($response, "mitambo_json_api request('$called_url'):  ");
                }

                $responseCode = wp_remote_retrieve_response_code($response);

                if (is_wp_error($response)) {
                    return array('error' => true, 'message' => $response->get_error_message(), 'datas' => array(), 'md5' => $md5, 'cache' => false, 'last_collect_date' => $this->last_collect_date, 'responseCode' => $responseCode);
                }
                $result = $this->parse_response($response, $called_url);
                if (!$result['error'] && $cache) {
                    $this->setCache($md5, $result['datas']);
                    if ($this->last_collect_date) {
                        update_option('wpsearchconsole_mitambo_last_crawled_date', $this->last_collect_date);
                    }
                }

            }

            return $result;

        }

        //generic request, if $cache is true data is saved
        public function request($called_url, $md5, $additionalArgs = array(), $cache = true)
        {
            if (!$this->token) {
                return;
            }
            $api_args = $this->api_args;
            if ($additionalArgs) {
                $api_args = array_merge($api_args, $additionalArgs);
            }
            $call_args = array(
                'headers' => $api_args,
                'sslverify' => false,
                'timeout' => 10,
            );
            if ($cache && $this->hasCache($md5)) {
                $result = array('error' => false, 'message' => '', 'datas' => $this->getCache($md5));
            } else {
                $response = wp_remote_get($called_url, $call_args);
                $result = $this->parse_response($response, $called_url);
                if (!$result['error'] && $cache) {
                    $this->setCache($md5, $result['datas']);
                    if ($this->last_collect_date) {
                        update_option('wpsearchconsole_mitambo_last_crawled_date', $this->last_collect_date);
                    }
                }
            }
            return $result;
        }

        private function parse_response($response, $called_url)
        {

            if (is_wp_error($response)) {
                if ($this->debug) {
                    wpsc_myLogs(' 1 for the following request: ' . $called_url . ' collect_date:' . $this->last_collect_date . ' error_code: not given , message: ' . $response->get_error_message());
                }
                return array('error' => true, 'message' => $response->get_error_message(), 'datas' => array(),'cache' => false);
            }

            $json = json_decode(wp_remote_retrieve_body($response), true);
            $responseCode = wp_remote_retrieve_response_code($response);

            $error_code = (isset($json['error_code']) ? $json['error_code'] : '');
            $status = (isset($json['status']) ? $json['status'] : 'not_given');

            $data = (isset($json['data']) ? $json['data'] : array());
            $params = (isset($json['params']) ? $json['params'] : array());
            $message_format = __($error_code, 'wpsearchconsole');

            $message = (isset($params) ? wpsc_vnsprintf($message_format, $params ) : (isset($json['message']) ? $json['message'] : ''));


            if ($status == 'not_given') {
                $errorMessage = $message;
                $errorMessage .= __('for the following request: ', 'wpsearchconsole') . $called_url;
                if ($this->debug) {
                    wpsc_myLogs('2 for the following request: ' . $called_url . ' collect_date:' . $this->last_collect_date . '  error_code:' . $error_code . ' message: ' . '[' . $responseCode . '] ' . $errorMessage);
                }
                return array('error' => true, 'message' => '[' . $responseCode . '] ' . $errorMessage, 'datas' => $data,'error_code' => $error_code,'cache' => false);
            }

            if ($status == 'error') {
                if ($this->debug) {
                    wpsc_myLogs('3 for the following request: ' . $called_url . ' collect_date:' . $this->last_collect_date . ' error_code:' . $error_code);
                }
                return array('error' => true, 'message' => $message, 'datas' => $data,'error_code' => $error_code,'cache' => false);
            } else {
                if ($this->debug) {
                    wpsc_myLogs('4 for the following request: ' . $called_url . ' collect_date:' . $this->last_collect_date . ' error_code:' . $error_code);
                }
                return array('error' => false, 'message' => $message, 'datas' => $data,'cache' => false);
            }
        }


        public function setCache($md5, $result)
        {
            $this->wpdb->delete($this->cacheTable, array('md5' => $md5));
            $json = json_encode($result);

            return $this->wpdb->insert($this->cacheTable, array(
                'md5' => $md5,
                'value' => $json,
                'last_collect_date' => $this->last_collect_date,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));
        }

        public function hasCache($md5)
        {
             $count = $this->wpdb->get_var("SELECT COUNT(*) FROM $this->cacheTable WHERE md5 = '" . $md5 . "'  ");
             if ($count) {
                 return true;
             }

            return false;
        }

        public function getCache($md5)
        {
            $cache = $this->wpdb->get_row("SELECT * FROM $this->cacheTable WHERE md5 = '" . $md5 . "'  ");

            if ($cache) {
                return json_decode($cache->value, true);
            } else {
                return false;
            }

        }

        public function cleanCache($days = 10)
        {
            $this->last_collect_date = false;
            delete_option('wpsearchconsole_mitambo_last_crawled_date');
            return $this->wpdb->query("DELETE FROM $this->cacheTable WHERE last_collect_date != '$this->last_collect_date' AND DATEDIFF(NOW(), created_at) > $days");
        }

        public function resetCache()
        {
            $this->last_collect_date = false;
            delete_option('wpsearchconsole_mitambo_last_crawled_date');
            $cache = $this->wpdb->query("TRUNCATE TABLE $this->cacheTable");
            $cache = $this->wpdb->query("TRUNCATE TABLE $this->cacheDataTable");
            return $cache;
        }

        public function GetStatusSubscriptionData()
        {
            $ok = true;
            $response = $this->api_call_global('crawler/status');
            if ($response['error']) {
                //$this->errors[] = $response['message'] . __('No Crawler status available', 'wpsearchconsole');
                $ok = false;
            }
            $response = $this->api_call_global('subscription/status');
            if ($response['error']) {
                //$this->errors[] = $response['message'] . __('No Subscription status available', 'wpsearchconsole');
                $ok = false;
            }
            return $ok;
        }

        public function pluginDeactivationApiCall()
        {
            $user = wp_get_current_user();

            $api_args = array('Authorization' => 'Bearer' . ' ' . $this->token,
                'X-User' => $user->data->user_email,
            );

            $call_args = array(
                'headers' => $api_args,
                'sslverify' => false,
                'body' => json_encode(array('url' => site_url())),
            );

            $result = wp_remote_post($this->api_deactivate, $call_args);

            return !is_wp_error($result) ? wp_remote_retrieve_body($result) : false;
        }

        // function only used to be parsed poedit
        private function translate_dynamic_messages()
        {
            $tmp = __('ERR_INTERNAL_SERVER_ERROR', 'wpsearchconsole');
            $tmp = __('ERR_PROJECT_NOT_CRAWLED', 'wpsearchconsole');
            $tmp = __('ERR_PAGE_NOT_CRAWLED', 'wpsearchconsole');
            $tmp = __('ERR_X_MITAMBO_URL_HEADER_REQUIRED', 'wpsearchconsole');
            $tmp = __('ERR_WRONG_PERCEPTION', 'wpsearchconsole');
            $tmp = __('ERR_PROJECT_NOT_FOUND', 'wpsearchconsole');
            $tmp = __('ERR_PAGE_ROBOT_TXT_DISALLOW', 'wpsearchconsole');
            $tmp = __('ERR_PAGE_NOINDEX', 'wpsearchconsole');
        }


    }

}
?>