<?php
/**
 *
 * @package: wpsearchconsole/admin/lib/
 * on: 19.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Add notices for whole admin area.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Define the base class for notices
 */
if (!class_exists('wpsearchconsole_notices')) {
    /**
     * Define the notice class
     */
    class wpsearchconsole_notices
    {

        //dashboard widget error
        public function todo_call_success()
        {
            $msg = __('Successfully added to do action.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('success', $msg);
        }

        //dashboard widget error
        public function todo_call_error()
        {
            $msg = __('All fields must be filled up to add WpSearchconsole To Do action.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //dashboard widget error
        public function todo_add_error()
        {
            $msg = __('Can not add WpSearchconsole To Do action.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //dashboard widget error
        public function dashboard_call_success()
        {
            $msg = __('You received data update from the DD/MM/YYYY', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('success', $msg);
        }

        //dashboard widget error
        public function dashboard_call_error()
        {
            $msg = __('Connection not possible, try latter.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //Call to Google Search console API
        public function api_call_error()
        {
            $msg = __('Call to the API failed, as there is no website authenticated yet.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //Installation error
        public function auth_success($site)
        {
            $msg = __('Successfully authenticated for', 'wpsearchconsole') . ' ' . $site;
            wpsearchconsole::getInstance()->setFlash('success', $msg);
        }

        //Installation error
        public function no_sites_found()
        {
            $msg = __('No verified website was found under your Google account. Please use other account.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //Installation error
        public function mitambo_not_crawled()
        {
            $msg = __('Project not crawled yet!', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //Installation error
        public function mitambo_disabled()
        {
            $msg = __('Mitambo services are disabled for free user.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //Installation error
        public function csv_error()
        {
            $msg = __('CSV headings and data fields must be entered as an array.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //Installation error
        public function installation_error()
        {
            $msg = __('Sorry. Could not install WP Search Console plugin, because necessary databases could not be created. Please contact the plugin author.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //success notice
        public function settings_success()
        {
            $msg = __('Greetings. Successfully updated the settings', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('success', $msg);
        }

        public function auth_mitambo_notify()
        {
            $msg = '<a href="' . admin_url('admin.php?page=wpsearchconsole&tab=mitambo') . '" >' . __('Please follow this link to complete Mitambo authentication ', 'wpsearchconsole') . '</a>';
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        public function auth_google_notify()
        {
            $msg = '<a href="' . admin_url('admin.php?page=wpsearchconsole&tab=google') . '" >' . __('Please follow this link to complete Google authentication ', 'wpsearchconsole') . '</a>';
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        //error notice
        public function settings_error()
        {
            $msg = __('Sorry. Could not update settings.', 'wpsearchconsole');
            wpsearchconsole::getInstance()->setFlash('error', $msg);
        }

        public function activation_notice($msg, $status)
        {
            $msg = $msg ? __($msg, 'wpsearchconsole') : __('Mitambo crawler activated successfully.', 'wpsearchconsole');

            if ($status == 'ok') {
                wpsearchconsole::getInstance()->setFlash('success', $msg);
            } else {
                wpsearchconsole::getInstance()->setFlash('error', $msg);
            }
        }
    }
}
?>