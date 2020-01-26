<?php
/**
 * Creates the submenu item for the plugin.
 *
 * @package Custom_Admin_Settings
 */
 
/**
 * Créer un sous menu pour le menu.
 *
 * Enregistre un menu sous le menu "Outils".
 *
 * @package Custom_Admin_Settings
 */
class Submenu {
 
 /**
 * A reference the class responsible for rendering the submenu page.
 *
 * @var Submenu_Page
 * @access private
 */
 private $submenu_page;
 
 /**
 * Initializer toutes les classes.
 *
 * @param Submenu_Page $submenu_page A reference to the class that renders the
 * page for the plugin.
 */
 public function __construct( $submenu_page ) {
 $this->submenu_page = $submenu_page;
 }
 
 /**
 * Ajoute un sous menu dans le menu outils.
 */
 public function init() {
 add_action( 'admin_menu', array( $this, 'add_options_page' ) );
 }
 
 /**
 * Creates the submenu item and calls on the Submenu Page object to render
 * the actual contents of the page.
 */
 public function add_options_page() {
 
 add_options_page(
 'Page d\'ajout des annonces',
 'Ajouter des annonces',
 'manage_options',
 'custom-admin-page',
 array( $this->submenu_page, 'render' )
 );
 }
}