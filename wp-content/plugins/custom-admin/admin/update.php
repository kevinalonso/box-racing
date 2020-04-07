<?php
require_once( str_replace('//','/',dirname(__FILE__).'/') .'../../../../wp-config.php');

function updateOne(){

	$sql = "SELECT wp_occasion.id,imagePrincipale,image1,image2,image3,titre,cylindre,annee,permis,marque,kilometre,prix,description FROM wp_occasion INNER JOIN wp_occasion_images ON wp_occasion.id = wp_occasion_images.id_annonce WHERE wp_occasion.id =".$_GET['updateOne'];
					
	global $wpdb;
	$annonce = $wpdb->get_results($sql);
	$tab = array('desc' => $annonce[0]->description);

	header('Location: http://localhost/dev/wp-admin/options-general.php?page=custom-admin-page&titre='.$annonce[0]->titre.'&cylindre='.$annonce[0]->cylindre.'&annee='.$annonce[0]->annee.'&marque='.$annonce[0]->marque.'&kilometre='.$annonce[0]->kilometre.'&prix='.$annonce[0]->prix.'&description='.http_build_query($tab).'&imgPrincipale='.$annonce[0]->imagePrincipale.'&img1='.$annonce[0]->image1.'&img2='.$annonce[0]->image2.'&img3='.$annonce[0]->image3.'&maj=true'.'&id='.$annonce[0]->id);
}

if (isset($_GET['updateOne'])) {
	updateOne();
}