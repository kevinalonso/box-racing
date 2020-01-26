jQuery(document).ready(function() {

	/**
	 *
	 * Trigger analysis metabox tabs
	 */
	jQuery(".wpsearchconsole-tabs-panel").hide();
    jQuery("#resume-box").show();
    jQuery("#summary-box").show();
    jQuery("#query-mobile-box,#query-web-box,#page-mobile-box,#page-web-box,#one-words-box,#status-302-box,#duplicate-titles-box").show();


    jQuery( document ).on( "click", ".wpsearchconsole-todo-table :checkbox",function(e) {
        //remove or add class barre or parent tr barre
        elem = jQuery(this);
        parent=elem.parent().parent();
        parent.toggleClass( "barre" );
        var todoID = elem.attr("ID");
        deactivate = false;
        if ( elem.is(':checked') ){
            //deactivate the checkbox
            deactivate = true;
        }
        // ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'process_todo_checkbox',
                wpsearchconsole_todo_id : todoID,
                wpsearchconsole_todo_status : deactivate,

            },
            success : function(result){
                jQuery('#wpsearchconsole-metabox-todo .wpsc_warning').html(result.message);
            }
        });
    });


    jQuery('#wpsearchconsole_taxonomy_todo_action_submit').click(function(e){
        e.preventDefault();

        var $action = jQuery('[name=wpsearchconsole_todo_action]').val();
        var $priority = jQuery('[name=wpsearchconsole_todo_priority]').val();
        var $responsible = jQuery('[name=wpsearchconsole_todo_responsible]').val();
        var $date = jQuery('[name=wpsearchconsole_todo_date]').val();
        var $tag_id = jQuery('[name=wpsearchconsole_todo_tag_ID]').val();
        var $taxonomy = jQuery('[name=wpsearchconsole_todo_taxonomy]').val();
        var $post_type = jQuery('[name=wpsearchconsole_todo_post_type]').val();
        var $user_id = jQuery('[name=wpsearchconsole_todo_user_ID]').val();
        var $focusTab = jQuery('[name=wpsearchconsole_todo_focus]').val() ? jQuery('[name=wpsearchconsole_todo_focus]').val() : 1;

        jQuery(".wpsc_spinner").show();
        // ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'process_todo_taxonomy',
                wpsearchconsole_todo_action : $action,
                wpsearchconsole_todo_priority : $priority,
                wpsearchconsole_todo_responsible : $responsible,
                wpsearchconsole_todo_category : $focusTab,
                wpsearchconsole_todo_date : $date,
                wpsearchconsole_todo_tag_ID : $tag_id,
                wpsearchconsole_todo_taxonomy : $taxonomy,
                wpsearchconsole_todo_post_type : $post_type,
                wpsearchconsole_todo_user_ID : $user_id,
                wpsearchconsole_todo_focustab : $focusTab
            },
            success : function(result){
                jQuery(".wpsc_spinner").hide();
                jQuery('#wpsearchconsole-metabox-todo .wpsc_warning').html(result.message);
                if(result.status){
                    //window.location.href = window.location.href+'&focus_tab='+$focusTab;
                    jQuery('#todo_content').html(result.todo_content);
                }
            }

        })
    });

    jQuery('#wpsearchconsole_todo_action_submit').click(function(e){
        e.preventDefault();

        var $action = jQuery('[name=wpsearchconsole_todo_action]').val();
        var $priority = jQuery('[name=wpsearchconsole_todo_priority]').val();
        var $responsible = jQuery('[name=wpsearchconsole_todo_responsible]').val();
        var $date = jQuery('[name=wpsearchconsole_todo_date]').val();
        var $post_id = jQuery('[name=wpsearchconsole_todo_post_ID]').val();
        var $post_type = jQuery('[name=wpsearchconsole_todo_post_type]').val();
        var $user_id = jQuery('[name=wpsearchconsole_todo_user_ID]').val();
        var $focusTab = jQuery('[name=wpsearchconsole_todo_focus]').val() ? jQuery('[name=wpsearchconsole_todo_focus]').val() : 1;

        jQuery(".wpsc_spinner").show();
        // ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.ajax({
            url: ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'process_todo_post',
                wpsearchconsole_todo_action : $action,
                wpsearchconsole_todo_priority : $priority,
                wpsearchconsole_todo_responsible : $responsible,
                wpsearchconsole_todo_category : $focusTab,
                wpsearchconsole_todo_date : $date,
                wpsearchconsole_todo_post_ID : $post_id,
                wpsearchconsole_todo_post_type : $post_type,
                wpsearchconsole_todo_user_ID : $user_id,
                wpsearchconsole_todo_focustab : $focusTab
            },
            success : function(result){
                jQuery(".wpsc_spinner").hide();
                jQuery('#wpsearchconsole-metabox-todo .wpsc_warning').html(result.message);
                if(result.status){
                    //window.location.href = window.location.href+'&focus_tab='+$focusTab;
                    jQuery('#todo_content').html(result.todo_content);
                }

            }
        })
    });


    function wpsearchconsole_tab2( name ) {
        jQuery("#" + name).click(function(event) {

            event.preventDefault();
            jQuery(this).parent().children().removeClass( "tabs" ).addClass( "hide-if-no-js" );
            jQuery(this).removeClass( "hide-if-no-js" ).addClass( "tabs" );

            jQuery(this).parent().parent().children(".wpsearchconsole-tabs-panel").hide();
            jQuery("#" + name + "-box").show();
        });
    }

    wpsearchconsole_tab2( "resume" );
    wpsearchconsole_tab2( "simple-keyword" );
    wpsearchconsole_tab2( "double-keyword" );
    wpsearchconsole_tab2( "triple-keyword" );

    //====== Link Analysis =========//
    wpsearchconsole_tab2( "details" );
    wpsearchconsole_tab2( "summary" );
    wpsearchconsole_tab2( "inbounds" );
    wpsearchconsole_tab2( "outbounds" );

	function wpsearchconsole_tab( pre, name ) {
		jQuery("#" + pre + "-" + name).click(function(event) {
			event.preventDefault();

			jQuery(this).parent().children().removeClass( "tabs" ).addClass( "hide-if-no-js" );
			jQuery(this).removeClass( "hide-if-no-js" ).addClass( "tabs" );

			jQuery(this).parent().parent().children(".wpsearchconsole-tabs-panel").hide();
			jQuery("#" + pre + "-" + name + "-box").show();
		});
	}


	//=== Top Queries and pages per device and medium ===//
	wpsearchconsole_tab( "query", "desktop" );
	wpsearchconsole_tab( "query", "mobile" );
	wpsearchconsole_tab( "query", "tablet" );
	wpsearchconsole_tab( "page", "desktop" );
	wpsearchconsole_tab( "page", "mobile" );
	wpsearchconsole_tab( "page", "tablet" );
	wpsearchconsole_tab( "query", "web" );
	wpsearchconsole_tab( "query", "image" );
	wpsearchconsole_tab( "query", "video" );
	wpsearchconsole_tab( "page", "web" );
	wpsearchconsole_tab( "page", "image" );
	wpsearchconsole_tab( "page", "video" );

	//============= Dashboard top keywords ===============//
	wpsearchconsole_tab( "one", "words" );
	wpsearchconsole_tab( "two", "words" );
	wpsearchconsole_tab( "three", "words" );
	wpsearchconsole_tab( "four", "words" );
	wpsearchconsole_tab( "five", "words" );
    wpsearchconsole_tab( "six", "words" );
    wpsearchconsole_tab( "seven", "words" );

	//============= Dashboard Internal Status ===============//
	wpsearchconsole_tab( "status", "301" );
	wpsearchconsole_tab( "status", "302" );
	wpsearchconsole_tab( "status", "307" );
	wpsearchconsole_tab( "status", "404" );
	wpsearchconsole_tab( "status", "500" );

	//============= Dashboard Duplicated text ===============//
	wpsearchconsole_tab( "duplicate", "titles" );
	wpsearchconsole_tab( "duplicate", "desc" );
	wpsearchconsole_tab( "duplicate", "content" );



});
