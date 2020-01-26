function mitambo_popup(url) {
	var left = (  Math.min(jQuery(window).width() - 600,600)  / 2 );
	var top   = ( Math.min(jQuery(window).height() - 500,400) / 2 );
	var newWindow = window.open( url, 'name', 'width=600, height=500,left='+left+',top='+top );
	if (window.focus) {
		newWindow.focus();
	}
	return false;
}
