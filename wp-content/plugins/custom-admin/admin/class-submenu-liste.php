<?php
/**
 * Creates the submenu page for the plugin.
 *
 * @package Custom_List_Annonce_Settings
 */
 
/**
 * Créer une sous menu pour la page du plugin.
 *
 * Fourni les fonctionnalité nécessaire pour le rendu de la page.
 *
 * @package Custom_List_Annonce_Settings
 */
class Submenu_Page_List {
 
 /**
 * Cette fonction renvoi un contenu associé à un menu qui assure le rendu.
 */
 	public function render() {
 		echo 'This is the basic submenu page list.';
 		include_once( 'views/settings-list.php' );
	}
}