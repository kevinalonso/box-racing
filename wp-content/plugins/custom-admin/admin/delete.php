<?php
require_once( str_replace('//','/',dirname(__FILE__).'/') .'../../../../wp-config.php');

function deleteOne(){
	$id = $_GET['deleteOne'];
	//Get database connection
	global $wpdb;

	$table = $wpdb->prefix . 'occasion';
	$tableImage = $wpdb->prefix . 'occasion_images';

	//Delete item by id
	$wpdb->delete($table, array( 'id' => $id ));
	$wpdb->delete($tableImage, array( 'id_annonce' => $id ));


	header('Location: http://localhost/dev/wp-admin/options-general.php?page=custom-admin-list');
}

if (isset($_GET['deleteOne'])) {
    deleteOne();
  }