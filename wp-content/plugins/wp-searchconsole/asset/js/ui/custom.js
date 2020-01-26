function mitambo_popup(url) {
    var left = (  Math.min(jQuery(window).width() - 600,600)  / 2 );
    var top   = ( Math.min(jQuery(window).height() - 500,400) / 2 );
    var newWindow = window.open( url, 'name', 'width=600, height=500,left='+left+',top='+top );
    if (window.focus) {
        newWindow.focus();
    }
    return false;
}

(function ($){

 function mitambo_auto_togle(){
    /**
     * Toggle tabs of widgets
     */
    $(".up-toggle").hide();
    $(".postbox .hndle,.postbox .handlediv").click(function(){
        $(this).children(".ui-toggle").children(".dashicons-arrow-down").toggle();
        $(this).children(".ui-toggle").children(".dashicons-arrow-up").toggle();
        $(this).parent(".postbox").children(".inside").toggle();
    });
 }


    function mitambo_filter_svg(){
    /**
     * Filter words for svg comparison
     */
    $('.quality-filter').click(function(e){
        e.preventDefault();
        var quality = $(this).data('quality');
        $(this).parent().siblings().removeClass('active');
        if(!$(this).parent().hasClass('active')){
            $(this).parent().addClass('active');
        }
        if(quality === 'all'){
            $('#MitamboSVG #Everything g').fadeIn();
            return false;
        }
        $('#MitamboSVG #Everything g[data-quality]').fadeOut();
        $('#MitamboSVG #Everything g[data-quality="'+quality+'"]').fadeIn();

    });
    }

    setTimeout(function(){ mitambo_auto_togle()},300);
    setTimeout(function(){ mitambo_filter_svg()},300);

})(jQuery);
