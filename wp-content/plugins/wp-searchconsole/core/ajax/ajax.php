<?php
/**
 *
 * @package: wpsearchconsole/core/ajax/
 * on: 28.06.2016
 * @since 0.1
 *
 * Add ajax to the plugin.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * oAuth object functions and features
 */
if (!class_exists('wpsearchconsole_ajax')) {

    class wpsearchconsole_ajax
    {

        private $base;
        private $table_todo;

        public function __construct()
        {

            global $wpdb;
            $this->table_todo = $wpdb->prefix . 'wpsearchconsole_todo';

            add_action('admin_footer', array($this, 'wpsearchconsole_ajax_javascript'));
            //add_action('wp_ajax_nopriv_todo_done', array($this, 'wpsearchconsole_ajax_callback'));
            //add_action('wp_ajax_todo_done', array($this, 'wpsearchconsole_ajax_callback'));

            wp_localize_script(
                'wpsearchconsole-base-script',
                'wpsearchconsole_ajax_object',
                array('ajax_url' => admin_url('admin-ajax.php')));
        }

        //base js of the calls
        public function wpsearchconsole_ajax_javascript()
        { ?>

            <?php
        }

        //Callback to be returned on request
        public function wpsearchconsole_ajax_callback()
        {

            $ID = asbint($_POST['ID']);
            $status = sanitize_text_field($_POST['status']);
            //toggle status
            if ($status == '0') {
                $status = '1';
            } else {
                $status = '0';
            }

            global $wpdb;
            $wpdb->update(
                $this->table_todo,
                array('status' => $status, 'creation_date' => current_time('mysql')),
                array('ID' => $ID),
                array('%s', '%s'),
                array('%s')
            );

            echo $status;

            wp_die();
        }
    }
}
?>