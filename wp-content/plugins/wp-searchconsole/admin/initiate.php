<?php
/**
 *
 * @package: wpsearchconsole/admin/
 * on: 25.05.2015
 * @since 0.1
 * @modified: 1
 *
 * Initiate plugin.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * All initiation classes
 */
if (!class_exists('wpsearchconsole_initiate')) {

    class wpsearchconsole_initiate
    {

        public function __construct()
        {

            add_action('plugins_loaded', array($this, 'wpsearchconsole_textdomain_cb'));
            add_action('admin_notices', array($this, 'wpsearchconsole_php_too_low'));
            add_filter('plugin_action_links', array($this, 'wpsearchconsole_scan_page_link'), 10, 2);
        }

        //Load plugin textdomain
        public function wpsearchconsole_textdomain_cb()
        {

            load_plugin_textdomain('wpsearchconsole', false, WPSEARCHCONSOLE_LN_PATH);
        }

        //Define low php verson errors
        function wpsearchconsole_php_too_low()
        {

            if (version_compare(phpversion(), '5.3', '<')) :

                $text = __('Wp Search Console Plugin can\'t be activated because your PHP version', 'wpsearchconsole');
                $text_last = __('is less than required 5.3. See more information', 'wpsearchconsole');
                $text_link = 'php.net/eol.php'; ?>

                <div id="message" class="updated notice notice-success is-dismissible">
                    <p><?php echo $text . ' ' . phpversion() . ' ' . $text_last . ': '; ?><a
                                href="http://php.net/eol.php/" target="_blank"><?php echo $text_link; ?></a></p></div>
            <?php endif;
            return;
        }

        //Add settings link to plugin page
        function wpsearchconsole_scan_page_link($links, $file)
        {

            static $this_plugin;
            //create array shift links
            if (!$this_plugin) {
                $this_plugin = WPSEARCHCONSOLE_FILE;
            }

            if ($file == $this_plugin) {
                $shift_link = array('<a href="' . admin_url() . 'admin.php?page=wpsearchconsole">' . __('Settings', 'wpsearchconsole') . '</a>',);
                foreach ($shift_link as $val) {
                    array_unshift($links, $val);
                }
            }
            return $links;
        }
    }
}
?>