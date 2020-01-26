 jQuery(document).ready(function(){

	jQuery(".wpsearchconsole_analysis, .wpsearchconsole_analysis_datatype_hide").hide();

	/**
	 * Show active fields
	 */
	if ( jQuery('input:radio[name="wpsearchconsole_analysis_datatype"]:checked').val() == 'page' ) {
		jQuery("#wpsearchconsole_analysis_page").show();
	} else {
		jQuery("#wpsearchconsole_analysis_request").show();
	}
	jQuery("#wpsearchconsole_analysis_" + jQuery('input:radio[name="wpsearchconsole_analysis_filter"]:checked').val()).show();


	//On changing the filter, analysis field changes
	jQuery('input:radio[name="wpsearchconsole_analysis_filter"]').change(function() {
		var wpsearchconsole_analysis_filter = jQuery(this).val();
		jQuery(".wpsearchconsole_analysis").hide();
		jQuery("#wpsearchconsole_analysis_" + wpsearchconsole_analysis_filter).show();
	});

	//On changing the filter, analysis field changes
	jQuery('input:radio[name="wpsearchconsole_analysis_datatype"]').change(function() {
		var wpsearchconsole_analysis_filter = jQuery(this).val();
		jQuery(".wpsearchconsole_analysis_datatype_hide").hide();
		jQuery("#wpsearchconsole_analysis_" + wpsearchconsole_analysis_filter).show();
	});

	var request = jQuery("#wpsearchconsole_analysis_request_select").val();
	var page = jQuery("#wpsearchconsole_analysis_page_select").val();
	if ( request == 'all' ) {
		jQuery("#wpsearchconsole_analysis_request_field").hide();
	} else {
		jQuery("#wpsearchconsole_analysis_request_field").show();
	}

	if ( page == 'all' ) {
		jQuery("#wpsearchconsole_analysis_page_field").hide();
	} else {
		jQuery("#wpsearchconsole_analysis_page_field").show();
	}

	jQuery("#wpsearchconsole_analysis_request_select").change(function(){
		var new_request = jQuery("#wpsearchconsole_analysis_request_select").val();
		if ( new_request == 'all' ) {
			jQuery("#wpsearchconsole_analysis_request_field").hide();
		} else {
			jQuery("#wpsearchconsole_analysis_request_field").show();
		}
	});

	jQuery("#wpsearchconsole_analysis_page_select").change(function(){
		var new_page = jQuery("#wpsearchconsole_analysis_page_select").val();
		if ( new_page == 'all' ) {
			jQuery("#wpsearchconsole_analysis_page_field").hide();
		} else {
			jQuery("#wpsearchconsole_analysis_page_field").show();
		}
	});

});
