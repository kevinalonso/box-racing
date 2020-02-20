var container;
var theThing;

window.onload = function(e){
	container = document.querySelector("#site-content");
	theThing = document.querySelector("#box-racing-popup-annonce");
	document.cookie = 'cross-site-cookie=bar; SameSite=None; Secure';
	
	positionAnnonceDetail(false);
}

function openPopup(){

	//document.getElementById("box-racing-popup").style.display = "block";
	document.getElementById("box-racing-popup").style.backgroundColor = "rgba(16,21,22,1)";
	document.getElementById("box-racing-popup").style.width = "65%";
	document.getElementById("box-racing-popup").style.height = "500px";
	document.getElementById("box-racing-popup-close").style.display = "block";
	document.getElementById("box-racing-popup-annonce").style.display = "block";
	document.getElementById("box-racing-popup-annonce").style.position = "absolute";
	
	
	//window.scrollTo()
	
}

function closePopup(){
	document.getElementById("box-racing-popup-annonce").style.display = "none";
	positionAnnonceDetail(true);
}

var mouseX,mouseY,windowWidth,windowHeight;
var  popupLeft,popupTop;

function positionAnnonceDetail(fermer){
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
	       	jQuery(document.getElementById('box-racing-popup')).show();
			var popupWidth  = jQuery(document.getElementById('box-racing-popup')).outerWidth();
			var popupHeight =  jQuery(document.getElementById('box-racing-popup')).outerHeight();

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

			jQuery(document.getElementById('box-racing-popup')).offset({top:popupTop,left:popupLeft});
			jQuery('main').animate({scrollTop: jQuery("#div-content-box-racing").offset().top}, popupTop);

			if (window.scrollY == 0 || window.scrollY > 0 && window.scrollY < 130) {
				jQuery(document.getElementById('box-racing-popup')).offset({top:popupTop+150});
			}
			
    	});
	}
}


/*function printMousePos(event) {
	var xPercent = event.clientX / jQuery( document ).width() * 100;
	var yPercent = event.clientY / jQuery( document ).height() * 100;
	X = xPercent;
	Y = yPercent;
}

document.addEventListener("click", printMousePos);*/