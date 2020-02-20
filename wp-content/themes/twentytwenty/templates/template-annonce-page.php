<?php
/**
 * Template Name: Moto occasion
 * Template Post Type: post, page
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0
 */

get_header();
?>

<main id="site-content" role="main">
	<div id="div-content-box-racing">
		<div>
			<h2 class="box-racing-sale-moto-title">Moto d'occassions chez Box Racing en ce moment</h2>
			
			<?php

				$query = "SELECT imagePrincipale,marque,titre,cylindre,annee,permis,prix,description FROM wp_occasion INNER JOIN wp_occasion_images ON wp_occasion.id = wp_occasion_images.id_annonce";

				global $wpdb;
				$annonces = $wpdb->get_results($query);
				//var_dump($annonces);

				foreach ($annonces as $annonce) {
					echo "
						<!--<a href='#' onclick='openPopup()'>-->
							<div class='box-racing-background-occasion-moto' style='float: left;display: inline; margin-left: 10%;width:20% !important;color: white;background-color: rgba(16,21,22,0.8); margin-top: 1%;'>
								<div>
									<img class='box-racing-moto-occasion-img' src='".$annonce->imagePrincipale."'/>
								</div>
								<a href='#' class='text-click-box-racing' onclick='openPopup()'>
									<span class='box-racing-occasion-titre'>".$annonce->marque."</span>
									<span class='box-racing-occasion-titre'>".$annonce->titre."</span>
									<span class='box-racing-occasion-titre'>".$annonce->cylindre."</span>
									</br>
									<span class='box-racing-occasion-permis'>".($annonce->cylindre == "1" ? "Conforme Permis A2":"Non conforme Permis A2")."</span>
									</br>
									<span style='margin-left: 40%;font-size: 140%;font-weight: bold; color: white;' class='box-racing-occasion-prix'>".$annonce->prix."</span>
								</a>
							</div>
						<!--</a>-->

						<div id='box-racing-popup-annonce' style='display: none;'>
							<div class='form-popup' id='box-racing-popup'>
								<div class='box-racing-occasion-head'>
									<span class='box-racing-occasion-titre-popup'>".$annonce->marque." ".$annonce->titre." ".$annonce->cylindre."</span>
								</div>
								<div class='box-racing-occasion-head'>
									<input id='box-racing-popup-close' type='button' onclick='closePopup()' style='display: none;' value='Fermer'/>
								</div>
								
								
								<div class='box-racing-moto-occasion-img1'>
									<img src='".$annonce->imagePrincipale."'/>
								</div>
								<div class='box-racing-popup-desc'>
									<span>".$annonce->description."</span>
								</div>
								<div class='box-racing-moto-occasion-img2'>
									<img src='".$annonce->imagePrincipale."'/>
								</div>
								<div class='box-racing-moto-occasion-img3'>
									<img src='".$annonce->imagePrincipale."'/>
								</div>
								<div class='box-racing-moto-occasion-img4'>
									<img src='".$annonce->imagePrincipale."'/>
								</div>
							</div>
						</div>
					";
				}
			?>
		</div>
	</div>
</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
