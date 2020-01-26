<?php
/**
 *
 * @package: wpsearchconsole/core/lib/jwt/
 * on: 19.05.2015
 * @since 0.1
 *
 * Add settings pages JWT authentication object.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_jwt')) {

    class wpsearchconsole_jwt
    {

        private $base;
        private $btmpl;
        private $scc;
        private $oauth;
        private $state;

        private $token_base;

        public function __construct()
        {


            $this->base = 'https://accounts.google.com/AccountChooser';

            $this->continue_url = 'https://accounts.google.com/o/oauth2/auth?scope%3Dopenid%2Bprofile%2Bemail%26display%3Dpopup%26response_type%3Dcode%26redirect_uri%3D' . WPSEARCHCONSOLE_PLUGIN_APP . '/auth/google/token%26client_id%3D931898197561-fh2jthsojk4952gnmpv4qms0u4gp583a.apps.googleusercontent.com%26from_login%3D1%26as%3D29b8366843642796';
            $this->btmpl = 'authsub';
            $this->scc = 1;
            $this->oauth = 1;
            $this->state = $this->base64UrlEncode(array('url' => home_url(), 'locale' => get_locale()));

            $this->api_jwt_base = WPSEARCHCONSOLE_PLUGIN_JWT_API;
            $this->app_base = WPSEARCHCONSOLE_PLUGIN_APP;
            $this->token_base = $this->api_jwt_base . '/auth/auth';
            $this->token_activate_base = $this->app_base . '/crawler/activate';
        }

        // encode the state url
        public function base64UrlEncode($inputArr)
        {
            return base64_encode(json_encode($inputArr));

        }

        //
        public function url()
        {
            return $this->base . '?continue=' . $this->continue_url . urlencode('&state=' . $this->state) . '&btmpl=' . $this->btmpl . '&scc=' . $this->scc . '&oauth=' . $this->oauth;
        }

        //Extract the token from json request
        public function token_process($json)
        {
            $auth = ($json ? $json->status : false);
            $token = ($json ? $json->token : false);

            if ($auth && $token) {
                return array(
                    'status' => $auth,
                    'token' => $token,
                );
            }
        }

        //make the api call //https://api.mitambo.com/auth/auth - set/reset timestamp jwt session
        public function token_request($code)
        {
            $api_args = array('Authorization' => 'Bearer' . ' ' . $code);
            $call_args = array(
                'headers' => $api_args,
                'sslverify' => false
            );

            $result = wp_remote_post($this->token_base, $call_args);
            //$result = !is_wp_error($result) ? wp_remote_retrieve_body($result) : false;

            return $result;
        }

        //make the api call to activation   //https://app.mitambo.com/crawler/activate
        public function token_check($code)
        {
            $api_args = array('Authorization' => 'Bearer' . ' ' . $code);

            $call_args = array(
                'headers' => $api_args,
                'sslverify' => false,
                'body' => json_encode(array('url' =>  home_url()))
            );

            $result = wp_remote_post($this->token_activate_base, $call_args);
           // $result = !is_wp_error($result) ? wp_remote_retrieve_body($result) : false;

            return $result;
        }
    }
}
?>