<?php
/**
 *
 * @package: wpsearchconsole/core/lib/oauth/
 * on: 19.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add settings pages oAuth authentication object.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_oauth')) {

    class wpsearchconsole_oauth
    {

        private $base;
        private $token_base;
        private $console_base;
        private $console_field_sites;
        private $scope;
        private $scope_alt;
        private $client_id;
        private $client_secret;
        private $redirect_uri;
        private $api_redirect_uri;
        private $response_type;
        private $approval_prompt;
        private $access_type;
        private $grant_type;

        public function __construct()
        {

            $this->base = 'https://accounts.google.com/o/oauth2/v2/auth';
            $this->token_base = 'https://accounts.google.com/o/oauth2/token';
            $this->console_base = 'https://www.googleapis.com/webmasters/v3/sites';
            $this->client_id = get_option('wpsearchconsole_client_ID');
            $this->client_secret = get_option('wpsearchconsole_client_secret');
            $this->console_field_sites = 'siteEntry';
            $this->scope = 'https://www.googleapis.com/auth/webmasters';
            $this->scope_alt = 'https://www.googleapis.com/auth/webmasters.readonly';
            $this->redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
            $this->response_type = 'code';
            $this->approval_prompt = 'force';
            $this->access_type = 'offline';
            $this->grant_type = 'authorization_code';
        }

        //get your url data
        public function get_url_errors($url, $token)
        {

            $data = false;
            if ($url && $token) {
                $api_url = $this->console_base . '/' . urlencode($url) . '/urlCrawlErrorsCounts/query?key=' . $token;
                //$data = file_get_contents($api_url, FILE_USE_INCLUDE_PATH);
                $data = wp_remote_retrieve_body(wp_remote_get($api_url, array('timeout' => 120, 'httpversion' => '1.1')));
            }

            return $data;
        }

        //make the API call to fetch site list
        public function api_call_process($siteentry)
        {

            $sites = array();
            $sitelist = false;

            if ($siteentry) {
                $json = json_decode($siteentry);
                if ($json && is_object($json) && property_exists($json, 'siteEntry')) {
                    $sitelist = $json->siteEntry;
                }
            }

            if ($sitelist && is_array($sitelist)) {
                foreach ($sitelist as $sitearr) {
                    $permission = $sitearr->permissionLevel;
                    if ($permission == 'siteUnverifiedUser' || $permission == 'siteOwner' || $permission == 'siteFullUser') {
                        $sites[] = array(
                            'url' => rtrim($sitearr->siteUrl, '/'),
                            'host' => parse_url($sitearr->siteUrl, PHP_URL_HOST),
                        );
                    }
                }
            }

            return $sites;
        }

        //make the API call to fetch site list
        public function api_call($token)
        {

            $api_url = $this->console_base . '?fields=' . $this->console_field_sites . '&access_token=' . urlencode($token);
            //wpsc_myLogs($api_url);
            $response = wp_remote_get($api_url, array('timeout' => 120, 'httpversion' => '1.1'));
            if (is_wp_error($response) || 200 != wp_remote_retrieve_response_code($response)) {
                return false;
            }

            return wp_remote_retrieve_body($response);

        }

        //Extract the token from json request
        public function token_process($token_string)
        {
            $json = ($token_string ? json_decode($token_string) : false);

            $access_token = ($json ? $json->access_token : false);
            $expires_in = ($json ? $json->expires_in : false);
            $refresh_token = ($json ? $json->refresh_token : false);

            if ($access_token && $expires_in && $refresh_token) {
                return array(
                    'access_token' => $access_token,
                    'expires_in' => $expires_in,
                    'refresh_token' => $refresh_token,
                );
            }
        }

        //make the api call
        public function token_request($code)
        {

            $api_args = array(
                'code' => $code,
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $this->redirect_uri,
                'grant_type' => 'authorization_code',
            );

            $call_args = array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'body' => $api_args,
                'sslverify' => false,
            );

            $result = wp_remote_post($this->token_base, $call_args);

            return (wp_remote_retrieve_response_code($result) == 200) ? wp_remote_retrieve_body($result) : false;
        }

        //google call url
        public function url()
        {
            return $this->base . '?scope=' . urlencode($this->scope) . '&client_id=' . $this->client_id . '&redirect_uri=' . $this->redirect_uri . '&response_type=' . $this->response_type . '&approval_prompt=' . $this->approval_prompt . '&access_type=' . $this->access_type;
        }
    }
}
?>