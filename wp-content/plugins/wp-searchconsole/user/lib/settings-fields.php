<?php
/**
 *
 * @package: /wpsearchconsole/user/lib/settings/
 * on: 26.05.2015
 * @since 0.1
 * @called-in: class wpsearchconsole_general_setting();
 * @included in: /wpsearchconsole/admin/obj/settings.php
 *
 * Settings fields and settings sections that are displayed registered here.
 * But, only static fileds are included here.
 * i.e. fields involving variables are included in corrosponding objects.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 *
 * Define the google auth display class
 * This class is called directly into original class wpsearchconsole()
 * As, it's added to admin_init so it can't be called inside class, added to init hook.
 *
 */
if (!class_exists('settings_fields')) {

    class wpsearchconsole_settings_fields
    {

        public function __construct()
        {

            //add_action( 'admin_init', array( $this, 'mitambo_settings_fields' ) );
            add_action('admin_init', array($this, 'google_settings_fields'));
            //add_action( 'admin_init', array( $this, 'roles_settings_fields' ) );
        }

        //display settings
        public function google_settings_fields()
        {

            add_settings_section('mitambo_pre_field', false, array($this, 'mitambo_pre_section_cb'), 'mitambo_pre_section');
            register_setting('mitambo_pre_field', 'mitambo_pre');
            add_settings_section('mitambo_field', false, array($this, 'mitambo_section_cb'), 'mitambo_section');
            register_setting('mitambo_field', 'mitambo');

            add_settings_section('mitambo_after_field', false, array($this, 'no_section_cb'), 'mitambo_after_section');
            add_settings_field('after_mitambo_field', __('Token', 'wpsearchconsole'), array($this, 'mitambo_after_section_form_element'), 'mitambo_after_section', 'mitambo_after_field');
            register_setting('mitambo_after_field', 'after_mitambo');
            add_settings_field('after_drop_mitambo_field', __('Website', 'wpsearchconsole'), array($this, 'mitambo_after_drop_section_form_element'), 'mitambo_after_section', 'mitambo_after_field');
            register_setting('mitambo_after_field', 'after_mitambo_drop');

            add_settings_section('google_pre_field', false, array($this, 'google_pre_section_cb'), 'google_pre_section');
            register_setting('google_pre_field', 'google_pre');
            add_settings_section('google_field', false, array($this, 'google_section_cb'), 'google_section');
            register_setting('google_field', 'google');

            add_settings_section('analysis_field', false, array($this, 'analysis_section_cb'), 'analysis_section');
            register_setting('analysis_field', 'analysis');
        }

        public function no_section_cb()
        {
            return false;
        }

        public function mitambo_pre_section_cb()
        {
            echo '<p>' . __('To allow WP Search Console to fetch your Mitambo Search Console data we need to receive your authorization', 'wpsearchconsole') . '</p>';
        }

        public function mitambo_section_cb()
        {
            echo '<p>' . __('Please enter the Mitambo Authorization Code in the field below', 'wpsearchconsole') . '</p>';
        }

        public function mitambo_after_section_form_element()
        {
            echo '<input type="text" class="regular-text" id="wpsearchconsole_mitambo" name="wpsearchconsole_mitambo" value="' . get_option('wpsearchconsole_mitambo') . '" disabled="disabled" />';
        }

        public function mitambo_after_drop_section_form_element()
        {
            echo '<input type="text" class="regular-text" id="wpsearchconsole_mitambo_website" name="wpsearchconsole_mitambo_website" value="' . site_url() . '" disabled="disabled" />';
        }

        public function google_pre_section_cb()
        {
            echo '<p>' . __('To allow WP Search Console to fetch your Google Search Console data we need to receive your authorization', 'wpsearchconsole') . '</p>';
        }

        public function google_section_cb()
        {
            echo '<p>' . __('Please enter the Google Authorization Code in the field below', 'wpsearchconsole') . '</p>';
        }

        public function analysis_section_cb()
        {
            echo '<p>' . __('Analyse your search performance in the Google Search. Filter and Compare your results to better understand your vistors.', 'wpsearchconsole') . '<a href="#" target="_blank">' . __('Read more', 'wpsearchconsole') . '</a></p>';
        }
    }
} ?>