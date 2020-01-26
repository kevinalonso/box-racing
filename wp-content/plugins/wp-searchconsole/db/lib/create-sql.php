<?php
/**
 *
 * @package: wpsearchconsole/db/lib/
 * on: 25.05.2015
 * @since 0.1
 *
 * Store all sql for database creation.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the class with sql store
 */
if (!class_exists('wpsearchconsole_create_sql')) {

    class wpsearchconsole_create_sql
    {

        public function cache($table_name, $collate)
        {

            return "CREATE TABLE $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					md5 varchar(64) NOT NULL,
					last_collect_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					value longtext NOT NULL,
					created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					UNIQUE KEY ID (ID),
					INDEX md5_idx (md5)
					) $collate;";
        }

        public function console($table_name, $collate)
        {

            return "CREATE TABLE $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					URL varchar(128) NOT NULL,
					last_crawled datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					first_detected datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
					responseCode mediumint(3) NOT NULL,
					platform varchar(16) NOT NULL,
					type varchar(16) NOT NULL,
					UNIQUE KEY ID (ID) ) $collate;";
        }

        public function visitors($table_name, $collate)
        {

            return "CREATE TABLE $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					requests varchar(128) NOT NULL,
					clicks mediumint(3) NOT NULL,
					impressions mediumint(3) NOT NULL,
					ctr decimal(5, 3) NOT NULL,
					position decimal(4, 1) NOT NULL,
					UNIQUE KEY ID (ID) ) $collate;";
        }

        public function json($table_name, $collate)
        {

            return "CREATE TABLE $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					datetime date NOT NULL,
					json_key varchar(64) NOT NULL,
					value mediumtext NOT NULL,
					UNIQUE KEY ID (ID) ) $collate;";
        }

        public function todo($table_name, $collate)
        {

            return "CREATE TABLE $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					post_ID mediumint(5) NOT NULL,
					type varchar(25) NOT NULL,
					post_type varchar(25) NOT NULL,
					taxonomy varchar(25) NOT NULL,
					action varchar(1024) NOT NULL,
					created_by mediumint(4) NOT NULL,
					assigned_to mediumint(4) NOT NULL,
					category varchar(64) NOT NULL,
					priority smallint(1) NOT NULL,
					status smallint(1) NOT NULL,
                    archived date NULL,
					creation_date date NOT NULL,
					due_date date NOT NULL,
					UNIQUE KEY ID (ID) ) $collate;";
        }
        public function migrate_todo($table_name){

            $exists = $this->check_table_column_exists($table_name,'archived');

            if (! $exists) {
                return "ALTER TABLE $table_name ADD COLUMN archived date NULL";
            }
           return "";

        }

        public function data($table_name, $collate)
        {

            return "CREATE TABLE IF NOT EXISTS $table_name (
					ID mediumint(9) NOT NULL AUTO_INCREMENT,
					api_key varchar(40) NOT NULL,
					api_subkey varchar(40) NOT NULL,
					json_value text NOT NULL,
					record_start int(11) NOT NULL,
					record_end int(11) NOT NULL,
					datetime date NOT NULL,
					UNIQUE KEY ID (ID) ) $collate;";
        }


        function check_table_column_exists( $table_name, $column_name ) {
            global $wpdb;
            $sql = $wpdb->prepare(
                "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
                DB_NAME, $table_name, $column_name
            );

            $column = $wpdb->get_results( $sql );
            if ( ! empty( $column ) ) {
                return true;
            }
            return false;
        }
    }
} ?>