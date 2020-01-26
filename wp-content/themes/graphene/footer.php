<?php

/**

 * The template for displaying the footer.

 *

 * Closes the <div> for #content, #content-main and #container, <body> and <html> tags.

 *

 * @package Graphene

 * @since Graphene 1.0

 */

global $graphene_settings;

?>  

<?php do_action( 'graphene_bottom_content' ); ?>

    </div><!-- #content-main -->

    

    <?php

    

        /* Sidebar 2 on the right side? */

        if ( graphene_column_mode() == 'three_col_left' ){

            get_sidebar( 'two' );

        }

		

		/* Sidebar 1 on the right side? */

        if ( in_array( graphene_column_mode(), array( 'two_col_left', 'three_col_left', 'three_col_center' ) ) ){

            get_sidebar();

        }

    

    ?>

    

    <?php do_action( 'graphene_after_content' ); ?>



</div><!-- #content -->



<?php /* Get the footer widget area */ ?>

<?php get_template_part('sidebar', 'footer'); ?>



<?php do_action('graphene_before_footer'); ?>
<style type="text/css">
#footer .vcard .fn {
	color: #000;
}
#footer .vcard .title {
	color: #000;
}
#footer .vcard .org {
	color: #000;
}
#footer .vcard .street-address {
	color: #000;
}
#footer .vcard .locality {
	color: #000;
}
#footer .vcard .region {
	color: #000;
}
#footer .vcard .postal-code {
	color: #000;
}
</style>




<div id="footer" class="clearfix">

    

    <?php if ( ! $graphene_settings['hide_copyright'] ) : ?>

    <div id="copyright">

    	<h3><?php _e('Copyright', 'graphene'); ?></h3>

		<?php if ( $graphene_settings['copy_text'] == '' && ! $graphene_settings['show_cc'] ) : ?>

            <p>

            <?php printf( '&copy; %1$s %2$s.', date( 'Y' ), get_bloginfo( 'name' ) ); ?>

            </p>

        <?php elseif ( ! $graphene_settings['show_cc'] ) : ?>

        	<?php 

				if ( ! stristr( $graphene_settings['copy_text'], '</p>' ) ) { $graphene_settings['copy_text'] = wpautop( $graphene_settings['copy_text'] ); }

				echo $graphene_settings['copy_text']; 

			?>

 	    <?php endif; ?>

        

        <?php if ( $graphene_settings['show_cc'] ) : ?>

        	<?php /* translators: %s will replaced by a link to the Creative Commons licence page, with "Creative Commons Licence" as the link text. */?>

        	<p>

				<?php printf( __( 'Except where otherwise noted, content on this site is licensed under a %s.', 'graphene' ), '<a href="http://creativecommons.org/licenses/by-nc-nd/3.0/">' . __( 'Creative Commons Licence', 'graphene' ) . '</a>' ); ?>

            </p>

        	<p class="cc-logo"><span><?php _e( 'Creative Commons Licence BY-NC-ND', 'graphene' ); ?></span></p>

        <?php endif; ?>



    	<?php do_action('graphene_copyright'); ?>

    </div>
    

<?php endif; ?>



	<?php if ( has_nav_menu( 'footer-menu' ) || ! $graphene_settings['hide_return_top'] ) : ?>

	<div class="footer-menu-wrap">

    	<ul id="footer-menu" class="clearfix">

			<?php /* Footer menu */

            $args = array(

                'container' => '',

                'fallback_cb' => 'none',

                'depth' => 2,

                'theme_location' => 'footer-menu',

                'items_wrap' => '%3$s'

            );

            wp_nav_menu(apply_filters('graphene_footer_menu_args', $args));

            ?>

            <?php if ( ! $graphene_settings['hide_return_top'] ) : ?>

        	<li class="menu-item return-top"><a href="#"><?php _e('Return to top', 'graphene'); ?></a></li>

            <?php endif; ?>

        </ul>

    </div>

    <?php endif; ?>

	

    <?php if ( ! $graphene_settings['disable_credit'] ) : ?>

    <div id="developer" class="grid_7">

        <p>

        <?php /* translators: %1$s is the link to WordPress.org, %2$s is the theme's name */ ?>

<?php printf( __('création %1$s ', ''), '<a href="https://creatitude360.fr/box-racing-moto-competition/" target="_blank" title="agence communication paris">agence web</a>', '<a ">' . __('', '') . '</a>'); ?>

        </p>



	<?php do_action('graphene_developer'); ?>

    </div>

    <?php endif; ?>

    

    <?php do_action('graphene_footer'); ?>

<div class="vcard" align="center"><strong class="fn">magasin de moto neuf et occasion</strong> <span class="title"> preparation moto de competition</span> <span class="org">box racing</span> <span class="street-address">brest 29 finistere</span> <span class="locality"> finistere brest 29</span> <span class="region">finistere</span> <span class="postal-code">29</span></div><!-- #footer -->
<div align="center">
  <table width="962" border="0">
     <tr>
       <th width="476" scope="col"><p align="justify"><strong>Box Racing est un magasin de vente neuf et d'occasion des motos toutes marques ainsi que de la préparation de moto de compétition. Box Racing vend des pièces racing neuves et d'occasions, des pneus de route et compétition pour le circuit neufs et d'oaccasion à Ploudaniel proche de Brest 29 Finistère en Bretagne.</strong></p>
        <hr/>
<p align="center"><strong><em>BOX RACING - 4, rue Hélène Boucher – ZI Mescoden – 29260 PLOUDANIEL – Bretagne</em></strong><br />
  <u>Tél.</u> : 02 98 80 21 46 - <u>Mail :</u> <a href="mailto:contact@box-racing.fr">contact@box-racing.fr</a> - Web : <a href="https://box-racing.fr">www.box-racing.fr</a></p>
<hr/>
<p align="justify"><strong>magasin moto brest 29</strong><strong> |</strong> <strong>magasin moto ploudaniel 29</strong><strong> |</strong> <strong>préparation moto brest 29</strong><strong> |</strong> <strong>préparation moto compétition</strong><strong> |</strong> <strong>préparation moto de course</strong><strong> |</strong> <strong>préparateur moto brest 29</strong><strong> |</strong>  <strong>vente moto brest 29 | <a href="https://moto-piece-competition-occasion.com/annonce/tapis-de-sol-imprime-et-personnalise/">tapis de sol</a>  | vente moto neuve brest 29 | <a href="https://www.creatitude.fr/creation-press-book-sportif/">press-book</a> | vente moto occasion brest 29 |</strong> <strong> vente pièces neuves brest 29</strong><strong> |</strong> <strong> vente occasions neuves brest 29</strong><strong> |</strong> <strong> vente pneus neufs brest 29</strong><strong> |</strong> <strong> vente pneus occasions brest 29</strong><strong> |</strong> <strong> préparateur moto |</strong> <strong> </strong><strong><a href="https://creatitude360.fr/creation-press-book-moto/" title="press-book pilote moto" target="_blank">press-book moto</a> |</strong> <strong> pièces compétition</strong><strong> |</strong> <strong> concessionnaire moto brest 29</strong><strong> |</strong> ploudaniel 29<strong> |</strong> finistère<strong> |</strong> bretagne<strong> |</strong> <a href="https://le-bon-plan-du-motard.com" target="_blank" title="moto scooter occasion">moto scooter d'occasion</a></p>
         
         <hr/>
        </th>
     </tr>
   </table>
</div></div>

<?php do_action('graphene_after_footer'); ?>



</div><!-- #container -->



<?php if (!get_theme_mod('background_image', false) && !get_theme_mod('background_color', false)) : ?>

    </div><!-- .bg-gradient -->

<?php endif; ?>



<?php wp_footer(); ?></body>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62572185-1', 'auto');
  ga('send', 'pageview');

</script>

</html>