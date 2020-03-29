var container;
var theThing;

window.onload = function(e){
	container = document.querySelector("#site-content");
	theThing = document.querySelector("#box-racing-popup-annonce");
	document.cookie = 'cross-site-cookie=bar; SameSite=None; Secure';
	
	//positionAnnonceDetail(false);
}

function openPopup(id){

	//document.getElementById("box-racing-popup").style.display = "block";
	document.getElementById("box-racing-popup-"+id).style.backgroundColor = "rgba(16,21,22,1)";
	document.getElementById("box-racing-popup-"+id).style.width = "43%";
	document.getElementById("box-racing-popup-"+id).style.height = "auto";/*500px*/
	document.getElementById("box-racing-popup-close-"+id).style.display = "block";
	document.getElementById("box-racing-popup-annonce-"+id).style.display = "block";
	document.getElementById("box-racing-popup-annonce-"+id).style.position = "absolute";
	
	positionAnnonceDetail(false,id)
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

			jQuery(document.getElementById('box-racing-popup-'+id)).offset({top:popupTop,left:popupLeft});
			jQuery('main').animate({scrollTop: jQuery("#div-content-box-racing").offset().top}, popupTop);

			if (window.scrollY == 0 || window.scrollY > 0 && window.scrollY < 130) {
				jQuery(document.getElementById('box-racing-popup-'+id)).offset({top:popupTop+213});
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


/*function printMousePos(event) {
	var xPercent = event.clientX / jQuery( document ).width() * 100;
	var yPercent = event.clientY / jQuery( document ).height() * 100;
	X = xPercent;
	Y = yPercent;
}

document.addEventListener("click", printMousePos);*/