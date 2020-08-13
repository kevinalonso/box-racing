var container;
var theThing;

var tabImg;
var index;
var imgTabIndex;
var displayImage;
var alreadyExist = false;
var identifiant;
var nomClass;


window.onload = function(e){
	container = document.querySelector("#site-content");
	theThing = document.querySelector("#box-racing-popup-annonce");
	document.cookie = 'cross-site-cookie=bar; SameSite=None; Secure';
	
	//positionAnnonceDetail(false);
}

/*(function($){
	$(function(){
		var $carrousel = $('#box-racing-carrousel-annonce-img'), // on cible le bloc du carrousel
	    $img = $('#box-racing-carrousel-annonce-img img'), // on cible les images contenues dans le carrousel
	    $indexImg = $img.length - 1, // on définit l'index du dernier élément
	    $i = 0, // on initialise un compteur
	    $currentImg = $img.eq($i); // enfin, on cible l'image courante, qui possède l'index i (0 pour l'instant)
	    $img.css('display', 'none'); // on cache les images
		$currentImg.css('display', 'block'); // on affiche seulement l'image courante
		$carrousel.append('<div class="controls"> <span class="prev">Precedent</span> <span class="next">Suivant</span> </div>');
	});
	
})(jquery);*/

function openPopup(id){

	//if (window.screen.width < 400) {

		//Screen mobile
		document.getElementById("box-racing-popup-"+id).style.backgroundColor = "rgba(16,21,22,1)";
		document.getElementById("box-racing-popup-"+id).style.width = "auto";
		document.getElementById("box-racing-popup-"+id).style.height = "550px";/*500px*/
		document.getElementById("box-racing-popup-close-"+id).style.display = "block";
		document.getElementById("box-racing-popup-annonce-"+id).style.display = "block";
		document.getElementById("box-racing-popup-annonce-"+id).style.position = "absolute";

		document.getElementById("box-racing-carrousel-annonce-img-"+id).style.display = "block";
		document.getElementById("box-racing-popup-"+id).style.zIndex = "1";

		if (window.screen.width > 400) {
			document.getElementById("box-racing-popup-"+id).style.position = "relative";
			document.getElementById("box-racing-popup-"+id).style.left = "23%";
			document.getElementById("box-racing-popup-"+id).style.marginTop = "-3%";
		}

	/*} else {
		//document.getElementById("box-racing-popup").style.display = "block";
		document.getElementById("box-racing-popup-"+id).style.backgroundColor = "rgba(16,21,22,1)";
		document.getElementById("box-racing-popup-"+id).style.width = "43%";
		document.getElementById("box-racing-popup-"+id).style.height = "auto";//500px
		document.getElementById("box-racing-popup-close-"+id).style.display = "block";
		document.getElementById("box-racing-popup-annonce-"+id).style.display = "block";
		document.getElementById("box-racing-popup-annonce-"+id).style.position = "absolute";

		document.getElementById("box-racing-carrousel-annonce-img-"+id).style.display = "none";
	}*/
	if (window.screen.width < 400) {
		positionAnnonceDetail(false,id)
	}
	carrouselAnnonce(id);
	//window.scrollTo()
	
}

function closePopup(id){
	document.getElementById("box-racing-popup-annonce-"+id).style.display = "none";
	positionAnnonceDetail(true,id);
}

var mouseX,mouseY,windowWidth,windowHeight;
var  popupLeft,popupTop;

function positionAnnonceDetail(fermer,id){
	jQuery(document).ready(function(){

	   jQuery('main').mousemove(function(e){
	       mouseX = e.pageX;
	       mouseY = e.pageY;
	       //To Get the relative position
	       if( this.offsetLeft !=undefined)
	         mouseX = e.pageX - this.offsetLeft;
	       if( this.offsetTop != undefined)
	         mouseY = e.pageY; - this.offsetTop;

	       if(mouseX < 0)
	            mouseX =0;
	       if(mouseY < 0)
	           mouseY = 0;

	       windowWidth  = jQuery(window).width()+jQuery(window).scrollLeft();
	       windowHeight = jQuery(window).height()+jQuery(window).scrollTop();
	   });
	});

	if (!fermer) {

		jQuery('main').click(function(){
	       	jQuery(document.getElementById('box-racing-popup-'+id)).show();
			var popupWidth  = jQuery(document.getElementById('box-racing-popup-'+id)).outerWidth();
			var popupHeight =  jQuery(document.getElementById('box-racing-popup-'+id)).outerHeight();

			if(mouseX+popupWidth > windowWidth)
				popupLeft = mouseX-popupWidth;
			else
				popupLeft = mouseX;

			if(mouseY+popupHeight > windowHeight)
				popupTop = mouseY-popupHeight;
			else
				popupTop = mouseY; 

			if( popupLeft < jQuery(window).scrollLeft()){
				popupLeft = jQuery(window).scrollLeft();
			}

			if( popupTop < jQuery(window).scrollTop()){
				popupTop = jQuery(window).scrollTop();
			}

			if(popupLeft < 0 || popupLeft == undefined)
			   popupLeft = 0;
			if(popupTop < 0 || popupTop == undefined)
			   popupTop = 0;

			
			jQuery('main').animate({scrollTop: jQuery("#div-content-box-racing").offset().top}, popupTop);

			if (window.scrollY == 0 || window.scrollY > 0 && window.scrollY < 130) {
				if (window.screen.width < 400) {
					jQuery(document.getElementById('box-racing-popup-'+id)).offset({top:popupTop,left:popupLeft});
					jQuery(document.getElementById('box-racing-popup-'+id)).offset({top:popupTop+440});
				} /*else {
					jQuery(document.getElementById('box-racing-popup-'+id)).offset({top:+200,left:243});
				}*/
			}
			
    	});
	}
}

function filterMoto(){

	//All class checked to condition
	var hiddenClass = document.getElementsByClassName('box-racing-background-occasion-moto');
	var selectedBrand = document.getElementById("box-racing-marque-filter");
	var getClassA2 = document.getElementsByClassName('box-racing-background-occasion-moto-a2');

	//Filter motocycle by type of "Marque"
	if (selectedBrand.options[selectedBrand.selectedIndex].value != '0') {
		for (var j = 0; j < selectedBrand.length; j++) {
			if (selectedBrand.options[j].value != '0') {

				var permisCheck = false;
				if (document.getElementById("A2").checked == true) {
					permisCheck = true;
				} else {
					permisCheck = false;
				}

				if (permisCheck) {

					//Hidden all moto not A2
					for(var n=0;n<hiddenClass.length;n++) {
		    			hiddenClass[n].style.display='none';
					}

					for(var a=0;a<getClassA2.length;a++) {
				    	var str = getClassA2[a].className.split(' ');
				    	if (str[0] == 'box-racing-background-occasion-moto-a2' && str[1] == selectedBrand.options[selectedBrand.selectedIndex].value) {
				    		getClassA2[a].style.display='inline';
				    	} else {
				    		getClassA2[a].style.display='none';
				    	}
					}
				} else {
					if (selectedBrand.options[j].value != selectedBrand.options[selectedBrand.selectedIndex].value) {
	        			hiddenClass = document.getElementsByClassName(selectedBrand.options[j].value);
		        		for(var k=0;k<hiddenClass.length;k++) {
				    		hiddenClass[k].style.display='none';
						}
        			} else {
        				hiddenClass = document.getElementsByClassName(selectedBrand.options[j].value);
        				for(var y=0;y<hiddenClass.length;y++) {
				    		hiddenClass[y].style.display='inline';
						}
        			}
				}
			} 	
    	}
	} else {
		//Filter motocycle by type of "Permis" when brand is not selected
		if (document.getElementById("A2").checked == true) {

			for(var x=0;x<getClassA2.length;x++) {
				getClassA2[x].style.display='inline';
			}

			for(var i=0;i<hiddenClass.length;i++) {
		    	hiddenClass[i].style.display='none';
			}
		} else {
			for (var l = 0; l < selectedBrand.length; l++) {
				if (selectedBrand.options[l].value != '0') {
	    			hiddenClass = document.getElementsByClassName(selectedBrand.options[l].value);
	        		for(var m=0;m<hiddenClass.length;m++) {
			    		hiddenClass[m].style.display='inline';
					}
				}
    		}
		}	
	}
}

function carrouselAnnonce(id){
	jQuery(document).ready(function(){
		var $carrousel = $('#box-racing-carrousel-annonce-img-'+id), // on cible le bloc du carrousel
	    $img = $('#box-racing-carrousel-annonce-img-'+id+' img'), // on cible les images contenues dans le carrousel
	    indexImg = $img.length - 1, // on définit l'index du dernier élément
	    i = 0, // on initialise un compteur
	    $currentImg = $img.eq(i); // enfin, on cible l'image courante, qui possède l'index i (0 pour l'instant)
	    $img.css('display', 'none'); // on cache les images
		$currentImg.css('display', 'block'); // on affiche seulement l'image courante

		nomClass = "controls-"+id;
		if ($(".controls-"+id).length == 0) {
			$carrousel.append('<div class="'+nomClass+'"> <span class="prev"><</span> <span class="next">></span> </div>');
		}

		$('.'+nomClass).css('margin-left','5%');
		$('.'+nomClass).css('margin-top','-3%');
		$('.'+nomClass).css('width','45%');
		$('.prev').css('visibility','hidden');

		tabImg = $img;
		index = 0;
		imgTabIndex = indexImg;
		displayImage = $currentImg

		alreadyExist = true;
		identifiant = id;

	});

	jQuery('.next').click(function(){ // image suivante

		$(".prev").css('visibility','visible');
	    index++; // on incrémente le compteur

	    if( index <= imgTabIndex ){
	        //$img.css('display', 'none'); // on cache les images
	        tabImg.css('display', 'none');
	        displayImage = $('#box-racing-carrousel-annonce-img-'+identifiant+' img').eq(index); // on définit la nouvelle image

	        displayImage.css('display', 'block'); // puis on l'affiche

	        if (index == imgTabIndex) {
	        	$(".next").css('visibility','hidden');
	        }
	    }
	    else{
	        i = imgTabIndex;
	    }

	});

	jQuery('.prev').click(function(){ // image précédente

		$(".next").css('visibility','visible');
	    index--; // on décrémente le compteur, puis on réalise la même chose que pour la fonction "suivante"

	    if( index >= 0 ){
	        //$img.css('display', 'none');
	        tabImg.css('display', 'none');
	        displayImage = $('#box-racing-carrousel-annonce-img-'+identifiant+' img').eq(index);
	        displayImage.css('display', 'block');

	        if (index == 0) {
	        	$(".prev").css('visibility','hidden');
	        }
	    }
	    else{
	        index = 0;
	    }

	});
}