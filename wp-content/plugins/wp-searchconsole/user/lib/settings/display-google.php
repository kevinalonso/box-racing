<?php
/**
 *
 * @package: advanced-wordpress-plugin/user/lib/settings/
 * on: 24.05.2015
 * @since 0.1
 * @modified: 2
 *
 * Display of Google Tab in settings page.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the google auth display class
 */
if (!class_exists('wpsearchconsole_google_auth_display')) {

    class wpsearchconsole_google_auth_display
    {

        private $oauth;
        private $notice;
        private $table_name_console;
        private $table_name_visitors;

        public function __construct()
        {

            global $wpdb;
            $this->table_name_console = $wpdb->prefix . 'wpsearchconsole_console';
            $this->table_name_visitors = $wpdb->prefix . 'wpsearchconsole_visitors';

            $this->oauth = new wpsearchconsole_oauth();
            $this->notice = new wpsearchconsole_notices();

            $selected_site = get_option('wpsearchconsole_selected_site');

            $this->open_wpsc_container();
            $this->open_rightcol();

            //On submission of authentication code
            if (isset($_POST['wpsearchconsole_google_auth']) && strlen(trim($_POST['wpsearchconsole_google'])) >= 45):

                $sites = $this->process('wpsearchconsole_google');

                if ($sites && is_array($sites)):

                    $this->end_form($sites);

                else:

                    $this->notice->no_sites_found();
                    $this->start_form();

                endif;

            //On selecting enlisted sites from webmaster tools
            elseif (isset($_POST['wpsearchconsole_save_profile_submit']) && isset($_POST['wpsearchconsole_site'])):

                //Save the site
                $site = sanitize_text_field($_POST['wpsearchconsole_site']);
                update_option('wpsearchconsole_selected_site', $site);

                //Call the crawler data
                $this->notice->auth_success(get_option('wpsearchconsole_selected_site'));
                $this->profile_form();

            //If re authentication is called, delete all the oauth data
            elseif (isset($_POST['wpsearchconsole_re_auth_submit'])):

                $this->revoke();
                $this->start_form();

            //When site has been selected and stored on options table
            elseif ($selected_site):

                $this->notice->auth_success(get_option('wpsearchconsole_selected_site'));
                $this->profile_form();

            //in otherwise situation
            else:

                $this->start_form();

            endif;
            $this->display_faq();
            $this->close_container();
            $this->close_container();
        }


        public function open_wpsc_container()
        {
            ?>
            <div id="wpsc_container" class="wpsc">
            <?php
        }
        public function open_form_container()
        {
            ?>
            <div class="wpsc_standard">
            <?php
        }
    public function open_rightcol()
    {
        ?>
        <div class="rightcol">
        <?php
    }
    public function open_leftcol()
    {
        ?>
        <div class="leftcol">
        <?php
    }

    public function close_container()
    {
        ?>
        </div>
        <?php
    }

        //delete the data first
        public function delete_db_table()
        {

            global $wpdb;
            $wpdb->query("TRUNCATE TABLE $this->table_name_visitors");
            $wpdb->query("TRUNCATE TABLE $this->table_name_console");
        }

        //delete the authorization data, so authorization will be revoked
        public function revoke()
        {

            delete_option('wpsearchconsole_selected_site');
            delete_option('wpsearchconsole_google');
            delete_option('wpsearchconsole_google_token');
            delete_option('wpsearchconsole_google_expiry');
            delete_option('wpsearchconsole_google_time');
            delete_option('wpsearchconsole_google_refresh_token');
            delete_option('wpsearchconsole_last_crawled_errors');
            delete_option('wpsearchconsole_last_crawled_analysis');

            delete_option('wpsearchconsole_analysis_param');
            delete_option('wpsearchconsole_analysis_value');
            delete_option('wpsearchconsole_analysis_point');

            delete_option('wpsearchconsole_analysis_clicks');
            delete_option('wpsearchconsole_analysis_impressions');
            delete_option('wpsearchconsole_analysis_ctr');
            delete_option('wpsearchconsole_analysis_position');

            $this->delete_db_table();
        }

        //process the data, save the authentication key
        public function process($name)
        {
            $code = sanitize_text_field($_POST[$name]);
            update_option('wpsearchconsole_google', $code);

            //Get access token on initial access, using authentication code
            $token_request = $this->oauth->token_request($code);
            $token = ($token_request ? $this->oauth->token_process($token_request) : false);

            if ($token) {
                //Access token to be used when it's not expired
                update_option('wpsearchconsole_google_token', $token['access_token']);

                //Save these for future offline access to webmaster tools
                update_option('wpsearchconsole_google_expiry', $token['expires_in']);
                update_option('wpsearchconsole_google_time', time());
                update_option('wpsearchconsole_google_refresh_token', $token['refresh_token']);
            }

            //Call to webmaster api using that access token
            $webmaster_info = ($token && array_key_exists('access_token', $token) ? $this->oauth->api_call($token['access_token']) : false);
            $webmaster_sites = ($webmaster_info ? $this->oauth->api_call_process($webmaster_info) : false);
            return $webmaster_sites;
        }

        //Initiate the form here
        public function start_form()
        {

            do_settings_sections('google_pre_section'); ?>
            <button onclick="mitambo_popup('<?php echo $this->oauth->url(); ?>');"
                    class="button button-secondary"><?php _e('Get Google Authorization Code', 'wpsearchconsole'); ?></button>
            <?php do_settings_sections('google_section'); ?>
            <form method="post" action="" enctype="multipart/form-data">
                <fieldset>
                    <?php $this->auth_field();
                    submit_button(__('Authenticate', 'wpsearchconsole'), 'primary', 'wpsearchconsole_google_auth', false); ?>
                </fieldset>
            </form>
            <?php
        }

        //Modfified form
        public function end_form($args)
        {
            ?>

            <form method="post" action="" enctype="multipart/form-data">
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <td scope="row"><?php _e('Profile', 'wpsearchconsole'); ?></td>
                        <td scope="row">
                            <?php $this->auth_site($args); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row"></td>
                        <td scope="row">
                            <fieldset>
                                <?php submit_button(__('Save Profile', 'wpsearchconsole'), 'primary', 'wpsearchconsole_save_profile_submit', false);
                                echo '&nbsp;&nbsp;';
                                submit_button(__('Re-Authenticate', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_re_auth_submit', false); ?>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <?php
        }

        //
        public function profile_form()
        { ?>
            <form method="post" action="" enctype="multipart/form-data">
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <td scope="row"><?php _e('Profile', 'wpsearchconsole'); ?></td>
                        <td scope="row">
                            <?php $this->profile_field(); ?>
                            <input type="hidden" value="<?php echo get_option('wpsearchconsole_google_token'); ?>"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(__('Re-Authenticate', 'wpsearchconsole'), 'secondary', 'wpsearchconsole_re_auth_submit', false); ?>
            </form>
            <?php
        }

        //field to enter authentication
        public function profile_field()
        { ?>
            <input type="text" class="regular-text" id="wpsearchconsole_selected_site"
                   name="wpsearchconsole_selected_site"
                   value="<?php echo get_option('wpsearchconsole_selected_site'); ?>" disabled="disabled"/>
            <?php
        }

        //field to show sites
        public function auth_site($args)
        { ?>

            <select id="wpsearchconsole_site" name="wpsearchconsole_site">
                <?php foreach ($args as $val): ?>
                    <option value="<?php echo $val['url']; ?>"><?php echo $val['url']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php
        }

        //field to enter authentication
        public function auth_field()
        { ?>
            <input type="text" class="regular-text" id="wpsearchconsole_google" name="wpsearchconsole_google"
                   value="<?php echo get_option('wpsearchconsole_google'); ?>"/>
            <?php
        }

        private function display_faq() {
            ?>
            <div class="wpsc_warning">
                <h2><?php _e("Frequent Questions (FAQ)",'wpsearchconsole')?></h2>
                <h3><?php _e("Why I do not see any data?",'wpsearchconsole')?></h3>
                <p><?php echo __("To be able to receive google search console data within the plugin, you need to activate it for your blog on ", 'wpsearchconsole') . '<a href ="https://www.google.com/webmasters/tools/home">Google Search Console</a>' ?></p>
                <p><?php _e("Use the <strong>same email</strong> within Google Settings tab to retrieve those data.", 'wpsearchconsole')?></p>
                <p><?php _e("Hit 'Refresh Google data' within dashboard,post,pages,categories and tag to see data from google search console.",'wpsearchconsole')?></p>

            </div>
            <?php
        }

    }
}
?>