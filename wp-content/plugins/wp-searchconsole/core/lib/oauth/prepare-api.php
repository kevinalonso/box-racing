<?php
/**
 *
 * @package: wpsearchconsole/core/lib/oauth/
 * on: 19.05.2016
 * @since 0.1
 *
 * API call with oAuth authentication object.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_prepare_google_api')) {

    class wpsearchconsole_prepare_google_api
    {

        private $client_id;
        private $client_secret;
        private $access_token;
        private $refresh_token;
        private $token_time;
        private $expiry_time;
        private $webmaster;
        private $site;
        private $token_base;
        private $notice;

        public function __construct()
        {

            $this->notice = new wpsearchconsole_notices();
            $this->errors = array();
            $this->client_id = get_option('wpsearchconsole_client_ID');
            $this->client_secret = get_option('wpsearchconsole_client_secret');
            $this->access_token = get_option('wpsearchconsole_google_token');
            $this->refresh_token = get_option('wpsearchconsole_google_refresh_token');
            $this->token_time = get_option('wpsearchconsole_google_time');
            $this->expiry_time = get_option('wpsearchconsole_google_expiry');
            $this->webmaster = 'https://www.googleapis.com/webmasters/v3/sites/';
            $this->token_base = 'https://accounts.google.com/o/oauth2/token';
            $this->site = wpsc_remove_trailingslash(get_option('wpsearchconsole_selected_site'));

        }

        // TODO Mark the page as Fixed
        // DELETE https://www.googleapis.com/webmasters/v3/sites/siteUrl/urlCrawlErrorsSamples/url

        //Call API data for search console widget
        public function widget_api_call($build_query)
        {
            //see if the token has expired, if yes renew it.
            $this->refresh_process();
            $api_url = $this->webmaster . urlencode($this->site) . '/searchAnalytics/query?access_token=' . $this->access_token;
            $postdata = json_encode($build_query);
            $call_args = array(
                'headers' => array('Content-type' => 'application/json'),
                'body' => $postdata,
                'sslverify' => false,
            );
            return $this->request($api_url, 'POST', $call_args);
        }

        //call the webmaster API for analysis
        public function analysis_api_call($args)
        {
            //see if the token has expired, if yes renew it.
            $this->refresh_process();
            $api_url = 'https://www.googleapis.com/webmasters/v3/sites/' . urlencode($this->site) . '/searchAnalytics/query?access_token=' . $this->access_token;
            $postdata = json_encode($args);
            //wpsc_myLogs($postdata);
            $call_args = array(
                'headers' => array('Content-type' => 'application/json'),
                'body' => $postdata,
                'sslverify' => false,
            );

            return $this->request($api_url, 'POST', $call_args);
        }

        //Call the webmaster API for exploration error
        public function api_call($type, $platform, $count)
        {

            //see if the token has expired, if yes renew it.
            $this->refresh_process();
            //get url errors or get error count
            $api_url = $this->webmaster . urlencode($this->site) . (!$count ? '/urlCrawlErrorsSamples' : '/urlCrawlErrorsCounts') . '?category=' . $type . '&platform=' . $platform . '&access_token=' . $this->access_token;


            return $this->request($api_url, 'GET', array(), true);
        }

        public function delete_api_call($url,$type,$platform)
        {
            //see if the token has expired, if yes renew it.
            $this->refresh_process();
            //get url errors or get error count
            $delete_api_url = $this->webmaster . urlencode($this->site) .  '/urlCrawlErrorsSamples/'  . urlencode($url ) . '?category=' . $type . '&platform=' . $platform . '&access_token=' . $this->access_token;
            
            return $this->request($delete_api_url, 'DELETE', array(), true);
        }



        public function request($url, $method, $args = array(), $apiCallError = false)
        {
            if (!$this->site && !$this->access_token) {
                if ($apiCallError) {
                    $this->notice->api_call_error();
                }
                return array('error' => true, 'message' => 'Config google error', 'datas' => array());
            }

            if ($method == 'POST') {
                $result = wp_remote_post($url, $args);
            } else if ($method == 'DELETE') {
                $result = wp_remote_request( $url, array('method'    => 'DELETE'));

            } else {
                $result = wp_remote_get($url);
            }

            if (is_wp_error($result)) {
                return array('error' => true, 'message' => $result->get_error_message(), 'datas' => array());
            }

            $responseCode = wp_remote_retrieve_response_code($result);

            if ($responseCode != 200) {
                $response = json_decode(wp_remote_retrieve_body($result), true);
                if (isset($response['error']) && isset($response['error']['message'])) {
                    return array('error' => true, 'message' => '[' . $responseCode . ']' . __($response['error']['message'], 'wpsearchconsole'), 'datas' => array());
                } else {
                    return array('error' => true, 'message' => '[' . $responseCode . ']' . __(' Error google', 'wpsearchconsole'), 'datas' => array());
                }
            } else {
                if ($method == 'DELETE') {
                    return array('error' => false, 'message' => __('Url MarkedAsFixed', 'wpsearchconsole'), 'datas' => array());
                } else {
                    return array('error' => false, 'message' => '', 'datas' => wp_remote_retrieve_body($result));
                }


            }
        }

        //refreash the token process. Run this process before making an api call
        public function refresh_process()
        {

            $token_request = ($this->is_expired() ? $this->refresh_token_request($this->refresh_token, true) : false);
            $token = ($token_request ? $this->refresh_token_process($token_request) : false);
            if ($token) {
                $this->access_token = $token['access_token'];
                $this->expiry_time = $token['expires_in'];
                $this->token_time = time();

                //Access token to be used when it's not expired
                update_option('wpsearchconsole_google_token', $this->access_token);

                //Save these for future offline access to webmaster tools
                update_option('wpsearchconsole_google_expiry', $this->expiry_time);
                update_option('wpsearchconsole_google_time', $this->token_time);
            }
        }

        //has the access token expired
        public function is_expired()
        {
            //Reducing 3 seconds to compansate on process execution time and the api call time
            $allowed = $this->token_time + $this->expiry_time - 5;
            if (time() >= $allowed) {
                return true;
            } else {
                return false;
            }
        }

        //Extract the token from json request
        public function refresh_token_process($token_string)
        {

            $json = ($token_string ? json_decode($token_string) : false);

            $access_token = ($json ? $json->access_token : false);
            $token_type = ($json ? $json->token_type : false);
            $expires_in = ($json ? $json->expires_in : false);

            if ($access_token && $expires_in) {
                return array(
                    'access_token' => $access_token,
                    'expires_in' => $expires_in,
                );
            }
        }

        // function only used to be parsed poedit
        private function translate_dynamic_mesages()
        {

            $tmp = __('invalid_grant', 'wpsearchconsole');
            $tmp = __('Invalid Category and Platform combination', 'wpsearchconsole');

        }

        //make the api call
        public function refresh_token_request($code)
        {

            $api_args = array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $code,
                'grant_type' => 'refresh_token',
            );
            $call_args = array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'body' => $api_args,
                'sslverify' => false,
            );
            $result = wp_remote_post($this->token_base, $call_args);
            $responseCode = wp_remote_retrieve_response_code($result);

            if ($responseCode != 200) {
                $res = json_decode(wp_remote_retrieve_body($result), true);
                $error = (isset($res['error'])) ? $res['error'] : __('Error refresh token for google Search Console - Check the Configuration', 'wpsearchconsole');
                $error = '[' . $responseCode . '] ' . __('Refresh Token', 'wpsearchconsole') . ' : ' . $error;
                wpsearchconsole::getInstance()->setFlash('error', $error);
            }

            return (wp_remote_retrieve_response_code($result) == 200) ? wp_remote_retrieve_body($result) : false;
        }
    }
} ?>