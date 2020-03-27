<?php
require_once( str_replace('//','/',dirname(__FILE__).'/') .'../../../../wp-config.php');

if (!empty($_POST)){

	//Permet de récupérer les données du formulaire pour les ajouter en base de données
	global $wpdb;
	$table = $wpdb->prefix . 'occasion';

	var_dump($_POST['annonce_delete_2']);
	

	//Permet de faire la redirection
	//header('Location: http://localhost/dev/wp-admin/options-general.php?page=custom-admin-list');
}