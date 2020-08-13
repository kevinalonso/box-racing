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
			<h2 class="box-racing-sale-moto-title">Nos occasions</h2>

			<div class="box-racing-filter-area">
				<label class="box-racing-annonce-filter">Moto permis A2 :</label>
				<input type="checkbox" id="A2"></input>
				<label> Marque :</label>
				<?php
	                global $wpdb;
	                $resultats = $wpdb->get_results("SELECT * FROM wp_moto_marque");
	                echo"<select id='box-racing-marque-filter' name='select_marque'>";
	                	echo"<option value='0'>Toutes les motos</option>";
	                    foreach($resultats as $item){
	                       echo"<option value=".$item->marque.">".$item->marque."</option>";
	                    }
	                echo"</select>";
				?>
				<input type="button" class="box-racing-filter-btn" value="Filtrer" onclick="filterMoto()"></input>
			</div>
			
			<?php

				$query = "SELECT wp_occasion.id,imagePrincipale,image1,image2,image3,marque,titre,cylindre,annee,permis,prix,description FROM wp_occasion INNER JOIN wp_occasion_images ON wp_occasion.id = wp_occasion_images.id_annonce";

				global $wpdb;
				$annonces = $wpdb->get_results($query);
				//var_dump($annonces);

				foreach ($annonces as $annonce) {
					echo "
						<div class='".($annonce->permis == "1" ? "box-racing-background-occasion-moto-a2 ".$annonce->marque : "box-racing-background-occasion-moto ".$annonce->marque)."'>
							
							<div>
								<img class='box-racing-moto-occasion-img' src='".$annonce->imagePrincipale."'/>
							</div>
							<a href='#' class='text-click-box-racing' onclick='openPopup(".$annonce->id.")' style='display: inline-block; width:100%;'>
								<span class='box-racing-occasion-titre'>".$annonce->marque."</span>
								<span class='box-racing-occasion-titre'>".$annonce->titre."</span>
								<span class='box-racing-occasion-titre'>".$annonce->cylindre."</span>
								</br>
								<span class='box-racing-occasion-permis'>".($annonce->permis == "1" ? "Conforme Permis A2":"Non conforme Permis A2")."</span>
								</br>
								<span style='margin-left: 40%;font-size: 140%;font-weight: bold; color: white;' class='box-racing-occasion-prix'>".$annonce->prix."</span>
							</a>
						</div>

						<div id='box-racing-popup-annonce-".$annonce->id."' style='display: none;'>
							<div class='form-popup' id='box-racing-popup-".$annonce->id."'>
								<div class='box-racing-occasion-head'>
									<span class='box-racing-occasion-titre-popup'>".$annonce->marque." ".$annonce->titre." ".$annonce->cylindre."</span>
								</div>
								<div class='box-racing-occasion-head-button'>
									<input id='box-racing-popup-close-".$annonce->id."' type='button' onclick='closePopup(".$annonce->id.")' style='display: none;' value='Fermer'/>
								</div>
								
								
								<div class='box-racing-moto-occasion-img1'>
									<img src='".$annonce->imagePrincipale."'/>
								</div>

								<div id='box-racing-carrousel-annonce-img-".$annonce->id."'>
									<ul style='list-style: none;'>
										<li><img class='box-racing-carrousel-img' src='".$annonce->imagePrincipale."'/></li>
										<li><img class='box-racing-carrousel-img' src='".$annonce->image1."'/></li>
										<li><img class='box-racing-carrousel-img' src='".$annonce->image2."'/></li>
										<li><img class='box-racing-carrousel-img' src='".$annonce->image3."'/></li>
									<ul>
								</div>

								<div class='box-racing-popup-desc'>
									<textarea readonly class='box-racing-desc-txt'>".$annonce->description."</textarea>
								</div>
								<div class='box-racing-moto-occasion-img2'>
									<img src='".$annonce->image1."'/>
								</div>
								<div class='box-racing-moto-occasion-img3'>
									<img src='".$annonce->image2."'/>
								</div>
								<div class='box-racing-moto-occasion-img4'>
									<img src='".$annonce->image3."'/>
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
