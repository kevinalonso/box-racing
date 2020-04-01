<html>
	<div class="wrap">
 
	    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	    
	   
	    <!--<form action="../wp-content/plugins/custom-admin/admin/delete.php" method='get' enctype="multipart/form-data">-->
		    <table border="1" border-color="black" style="width: 100%;">
				<?php

					echo "<tr>";
				    		echo "<td>Image</td>";
							echo "<td>Marque</td>";
				    		echo "<td>Modéle</td>";
				    		echo "<td>Cylindre</td>";
							echo "<td>Kilomètre</td>";
				    		echo "<td>Annee</td>";
				    		echo "<td>Permis</td>";
							echo "<td>Description</td>";
							echo "<td>Prix</td>";
			    		echo "</tr>";
					//Requete sql à utiliser
			    	$sql = "SELECT wp_occasion.id,imagePrincipale,titre,cylindre,annee,permis,marque,kilometre,prix,description FROM wp_occasion INNER JOIN wp_occasion_images ON wp_occasion.id = wp_occasion_images.id_annonce";
					
					global $wpdb;
					$annonces = $wpdb->get_results($sql);
			    	
			    	foreach ($annonces as $item) {
			    		$a2;
			    		echo "<tr>";
				    		echo "<td style='width: 5%;'>".'<img style="height:140px;" src="'.$item->imagePrincipale.'"/>'."</td>";
							echo "<td>".$item->marque."</td>";
				    		echo "<td>".$item->titre."</td>";
				    		echo "<td>".$item->cylindre."</td>";
				    		echo "<td>".$item->kilometre."</td>";
				    		if($item->permis == 1)
				    		{
				    			$a2 = "A2";
				    		} else {
				    			$a2 = "Non-A2";
				    		}
							echo "<td>".$item->annee."</td>";
							echo "<td>".$a2."</td>";
							echo "<td>".$item->description."</td>";
							echo "<td>".$item->prix."</td>";
				    		echo "<td><a href='../wp-content/plugins/custom-admin/admin/delete.php?deleteOne=".$item->id."'>Supprimer</a></br><a href='../wp-content/plugins/custom-admin/admin/update.php?updateOne=".$item->id."'>Modifier</a></td>";
			    		echo "</tr>";
			    	}
			    ?>
			</table>
		<!--</form>-->
	</div>
</html>
<!--<form action='../wp-content/plugins/custom-admin/admin/delete.php?id=1' method='get'><input type='hidden' name='annonce_delete_".$item->id."' value='".$item->id."'/><input type='submit' value='Supprimer'/><input type='button' value='Modifier'/></form>-->

<!--../wp-content/plugins/custom-admin/admin/delete.php?id=".$item->id."-->