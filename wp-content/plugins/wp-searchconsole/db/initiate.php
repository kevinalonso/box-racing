<?php
/**
 *
 * @package: wpsearchconsole/db/
 * on: 26.05.2015
 * @since 0.1
 *
 * Initiate database.
 *
 */
if (!defined('ABSPATH')) exit;


/**
 * Install the databses
 */
if (!class_exists('wpsearchconsole_install_db')) {

    class wpsearchconsole_install_db
    {

        private $db;

        public function __construct($json_api)
        {

            $this->db = new wpsearchconsole_initiate_db();
            // if plugin already activate
            $this->db->migration();

            $archived = get_option('wpsearchconsole_todo_archived');

            if (!$archived) {
                $this->db->todo_details();
            }

            $this->json_api = $json_api;

            register_activation_hook(WPSEARCHCONSOLE_FILE, array($this, 'wpsearchconsole_db_install'));
            register_deactivation_hook(WPSEARCHCONSOLE_FILE, array($this, 'wpsearchconsole_db_deactivation'));
        }

        // Initiate database upon activation
        public function wpsearchconsole_db_install()
        {

            $this->db->console();
            $this->db->cache();
            $this->db->visitors();
            $this->db->json();
            $this->db->todo();
            $this->db->migration();
            $this->db->data();
            //after migration
            $this->db->version();
            $this->db->client_details();
            $this->db->todo_details();
            $this->db->user_capability_install();

        }

        // Remove data upon deactivation
        public function wpsearchconsole_db_deactivation()
        {
            $this->json_api->pluginDeactivationApiCall();
        }


    }
} ?>