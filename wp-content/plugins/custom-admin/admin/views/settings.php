<html>
<head>
    <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script class="jsbin" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js"></script>
    <script src="http://localhost/dev/wp-content/plugins/custom-admin/admin/views/js/image.js"></script>
</head>

<div class="wrap">
 
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 
    <form method="post" action="../wp-content/plugins/custom-admin/admin/add-data.php" enctype="multipart/form-data">
 
        <div id="universal-message-container">
            <h2>Ajouter une annonce</h2>
 
            <div class="options">
                <p>
                    <label>Titre de l'annonce</label>
                    <br/>
                    <input type="text" name="titre" value="<?=($_GET['titre'] == NULL ? "" : $_GET['titre']) ?>" />
                    <br/>

                    <label>Marque</label>
                    <br/>
                    <?php
                        global $wpdb;
                        $resultats = $wpdb->get_results("SELECT * FROM wp_moto_marque");
                        echo"<select name='select_marque'>";
                            foreach($resultats as $item){
                               echo"<option value=".$item->marque.">".$item->marque."</option>";
                            }
                        echo"</select>";
                    ?>
                    <br/>

                    <input type="radio" name="permis" value="1">Permis A2</input>
                    <input type="radio" name="permis" value="0">Non-A2</input>
                    <br/>

                	<label>Année</label>
                	<br/>
                    <input type="text" name="annee" value="<?=($_GET['annee'] == NULL ? "" : $_GET['annee']) ?>"/>
                    <br/>
                    <label>Cylindrée</label>
                    <br/>
                    <input type="text" name="cylindre" value="<?=($_GET['cylindre'] == NULL ? "" : $_GET['cylindre']) ?>"/>
                  
                    <br/>
                    <label>Description de l'annonce</label>
                    <br/>
                    <textarea name="description" value="<?=($_GET['description'] == NULL ? "" : $_GET['description']) ?>"></textarea>
                    <br/>
                    <label>Prix</label>
                    <br/>
                    <input type="text" name="prix" value="<?=($_GET['prix'] == NULL ? "" : $_GET['prix']) ?>"/>
                    <br/>

                    <input type='file' name="img_princ" onchange="readURLPrincipale(this);"/>
                    <input type="image" id="blah" src="#" alt="Image Princpale"/>
                    <br/>

                    <input type='file' name="img_prem" onchange="readURLPremiere(this);"/>
                    <input type="image" id="blah1" src="#" alt="1"/>

                    <input type='file' name="img_sec" onchange="readURLSecondaire(this);"/>
                    <input type="image" id="blah2" src="#" alt="2"/>

                    <input type='file' name="img_trois" onchange="readURLTroisieme(this);"/>
                    <input type="image" id="blah3" src="#" alt="3"/>

                </p>
        </div><!-- #universal-message-container -->
 
        <?php
            wp_nonce_field( 'acme-settings-save', 'acme-custom-message' );
            submit_button();
        ?>
    </form>

    <a href="<?php echo esc_html( admin_url( 'options-general.php?page=custom-admin-list'));?>">Liste des annonces moto</a>
 
</div><!-- .wrap -->
</html>