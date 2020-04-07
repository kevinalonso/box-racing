<?php
require_once( str_replace('//','/',dirname(__FILE__).'/') .'../../../../wp-config.php');

if (!empty($_POST)){

	//Permet de récupérer les données du formulaire pour les ajouter en base de données
	global $wpdb;

	$table = $wpdb->prefix . 'occasion';
	$permis;

	if($_POST['permis'] == false)
	{
		$permis = 0;
	} else {
		$permis = 1;
	}

	
	$data = array(
		'id' => $_POST['id'],
		'titre' => $_POST['titre'],
		'marque' => $_POST['select_marque'],
		'permis' => $_POST['permis'],
		'annee' => $_POST['annee'],
		'cylindre' => $_POST['cylindre'],
		'description' => $_POST['description'],
		'prix' => $_POST['prix'],
		'kilometre' => $_POST['kilometre']
	);
	
	
	$wpdb->update($table, $data, array('id' => $_POST['id']));


}