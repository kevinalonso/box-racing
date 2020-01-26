<?php
/**
 * Fichier de démarrage
 *
 * Ceci est le fichier principal qui sera lu par WordPress.
 *
 * @link https://blogpascher.com
 * @since 1.0.0
 * @package Custom_Admin_Settings
 *
 * @wordpress-plugin
 * Plugin Name: Administration Personnalisé
 * Plugin URI: https://blogpascher.com
 * Description: Apprendre à créer un plugin WordPress.
 * Version: 1.0.0
 * Author: ALONSO Kévin
 * Author URI: https://exemple.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Inclusions de toute les dépendance.
foreach ( glob( plugin_dir_path( __FILE__ ) . 'admin/*.php' ) as $file ) {
	include_once $file;
}

//Formulaire d'ajout des annonces
add_action( 'plugins_loaded', 'tutsplus_custom_admin_settings' );

//Page de consultation et de gestion des annonces
add_action( 'plugins_loaded', 'tutsplus_custom_list_settings' );

/**
 * Démarrer le plugin.
 *
 * @since 1.0.0
 */
function tutsplus_custom_admin_settings() {

	//Création des table en base de données pour le plugin
	my_plugin_create_table();
	
	$serializer = new Serializer();
    $serializer->init();
 
	$plugin = new Submenu(new Submenu_Page());
 	$plugin->init();
	
	
	$plugin = new Submenu( new Submenu_Page( $serializer ) );
    // $plugin->init();
}

function tutsplus_custom_list_settings() {
	$list_annonce = new ListSubmenu(new Submenu_Page_List());
 	$list_annonce->init();
}

function my_plugin_create_table() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'occasion';
	$query = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like($table_name));

	if($wpdb->get_var($query) != $table_name){
		//Créer une table pour l'annonce
		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			titre varchar(50) NOT NULL,
			description longtext,
			marque varchar(20),
			cylindre varchar(10),
			permis bool,
			annee varchar(20),
			prix varchar(20),
			kilometre varchar(20),
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	
	//Créer une table pour les marques
	$table_name3 = $wpdb->prefix . 'moto_marque';
	$query = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like($table_name3));

	if($wpdb->get_var($query) != $table_name3){
			$charset_collate_table3 = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name3 (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			marque varchar(50) NOT NULL,
			UNIQUE KEY id (id)
			) $charset_collate_table3";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'APRILIA' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'BENELLI' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'BMW' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'DUCATI' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'HARLEY-DAVIDSON' 
			) 
		);$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'HUSQVARNA' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'HONDA' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'KAWASAKI' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'KTM' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'MOTO GUZZI' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'MV AGUSTA' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'SUZUKI' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'TRIUMPH' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'YAMAHA' 
			) 
		);
		$wpdb->insert( 
			$table_name3, 
			array( 
				'marque' => 'URAL' 
			) 
		);
	}

	//Créer une table pour les images
	$table_name2 = $wpdb->prefix . 'occasion_images';
	$query = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like($table_name2));
	if($wpdb->get_var($query) != $table_name2){
		$sql = "CREATE TABLE $table_name2 (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			id_annonce bigint(20) unsigned NOT NULL,
			imagePrincipale longtext NOT NULL,
			image1 longtext,
			image2 longtext,
			image3 longtext,
			UNIQUE KEY id (id)
			FOREIGN KEY  (id_annonce) REFERENCES $table_name(id)
		) $charset_collate;";
		

		//TODO alter table to add foreignkey GET

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
	
}
