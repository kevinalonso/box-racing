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
		'titre' => $_POST['titre'],
		'marque' => $_POST['select_marque'],
		'permis' => $_POST['permis'],
		'annee' => $_POST['annee'],
		'cylindre' => $_POST['cylindre'],
		'description' => $_POST['description'],
		'prix' => $_POST['prix'],
		'kilometre' => $_POST	['kilometre']
	);
	
	
	$wpdb->insert($table, $data);
	

	//Permet de récupérer le dernière id de l'annonce inséré
	$id_annonce = $wpdb->insert_id;
	
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	
	$uploadedfile = $_FILES['img_princ'];
	$upload_overrides = array( 'test_form' => false );
	
	add_filter('upload_dir', 'my_upload_dir');
	$movefilePrinc = wp_handle_upload( $uploadedfile, $upload_overrides );
	remove_filter('upload_dir', 'my_upload_dir');

	$uploadedfile = $_FILES['img_prem'];
	$upload_overrides = array( 'test_form' => false );
	
	add_filter('upload_dir', 'my_upload_dir');
	$movefilePrem = wp_handle_upload( $uploadedfile, $upload_overrides );
	remove_filter('upload_dir', 'my_upload_dir');

	$uploadedfile = $_FILES['img_sec'];
	$upload_overrides = array( 'test_form' => false );
	
	add_filter('upload_dir', 'my_upload_dir');
	$movefileSec = wp_handle_upload( $uploadedfile, $upload_overrides );
	remove_filter('upload_dir', 'my_upload_dir');

	$uploadedfile = $_FILES['img_trois'];
	$upload_overrides = array( 'test_form' => false );
	
	add_filter('upload_dir', 'my_upload_dir');
	$movefileTrois = wp_handle_upload( $uploadedfile, $upload_overrides );
	remove_filter('upload_dir', 'my_upload_dir');

	
	$tableImage = $wpdb->prefix . 'occasion_images';

	//Copier le fichier photo dans le dossier uploads/annonce
	$dataImage = array(
		'id_annonce' => $id_annonce,
		'imagePrincipale' => $movefilePrinc['url'],
		'image1' => $movefilePrem['url'],
		'image2' => $movefileSec['url'],
		'image3' => $movefileTrois['url']
	);

	
	$wpdb->insert($tableImage, $dataImage);

	//Permet de faire la redirection
	header('Location: https://box-racing.fr/wp-admin/options-general.php?page=custom-admin-list');

}

function my_upload_dir($upload) {

  $upload['subdir'] = '/annonce-moto' . $upload['subdir'];

  $upload['path']   = $upload['basedir'] . $upload['subdir'];

  $upload['url']    = $upload['baseurl'] . $upload['subdir'];

  return $upload;

}

?>