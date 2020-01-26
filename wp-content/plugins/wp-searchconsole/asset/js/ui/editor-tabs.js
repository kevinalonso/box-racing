function getUrlVars(){
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }

function getUrlVar(name){
        return getUrlVars()[name];
}


var mitamboTabs0 = ['wpsearchconsole-metabox-todo', 'wpsearchconsole-metabox-mitambo-keywords', '#wpsearchconsole-metabox-google-keywords', 'wpsearchconsole-metabox-mitambo-main-html-tags', 'wpsearchconsole-metabox-mitambo-link-analysis', 'wpsearchconsole-metabox-mitambo-meta-tags', 'wpsearchconsole-metabox-mitambo-post-duplicate-perception', 'wpsearchconsole-metabox-mitambo-post-duplicate-titles', 'wpsearchconsole-metabox-mitambo-post-duplicate-desc', 'wpsearchconsole-metabox-mitambo-post-duplicate-content'];

var mitamboTabs1 = ['wpsearchconsole-metabox-todo', 'wpsearchconsole-metabox-mitambo-keywords', 'wpsearchconsole-metabox-google-keywords', 'wpsearchconsole-metabox-mitambo-main-html-tags', 'wpsearchconsole-metabox-mitambo-meta-tags'];

var mitamboTabs2 = ['wpsearchconsole-metabox-todo', 'wpsearchconsole-metabox-mitambo-link-analysis'];

var mitamboTabs3 = ['wpsearchconsole-metabox-todo', 'wpsearchconsole-metabox-mitambo-post-duplicate-perception', 'wpsearchconsole-metabox-mitambo-post-duplicate-titles', 'wpsearchconsole-metabox-mitambo-post-duplicate-desc', 'wpsearchconsole-metabox-mitambo-post-duplicate-content'];


function mitambo_activate_tabs(TabIndex) {
    activatedTab = TabIndex || 0;
    elem = jQuery("#wpsearchconsole-"+activatedTab);
    elem2 = jQuery("#wpsearchconsole-tab-"+activatedTab);
    jQuery('[name=wpsearchconsole_todo_focus]').val(activatedTab);
    pageContext = elem.parent().attr("name");
    for (i = 0; i < 4; i++) {
        if (activatedTab == i) {
            elem.addClass("nav-tab-active");
            elem2.addClass("nav-tab-active");
            mitambo_display_content(activatedTab,pageContext);
        } else {
            jQuery("#wpsearchconsole-" + i).removeClass("nav-tab-active");
            jQuery("#wpsearchconsole-tab-" + i).removeClass("nav-tab-active");
        }
    }
}

function mitambo_display_content(tabIndex,pageContext) {

    //select the wpsc metabox to display
    selectedTabs = eval("mitamboTabs" + tabIndex);
    if (tabIndex == 0) {

        // reactivate the editor
        if (window.editorExpand){
            window.editorExpand.off();
            //window.editorExpand.on();
        }

        switch (pageContext) {
            case 'post':
                jQuery("#postdivrich").show();// display='none'
                jQuery("#avia-builder-button").show();
                jQuery( "#postbox-container-2" ).addClass('wpsearchconsole-editor');

                //tab index : 0=default,1=keywords,2=links,3=duplication
                activatedTab = jQuery(".nav-tab-active").first().attr('data')|| 0;
                jQuery('input[name=wpsearchconsole-focus-category]').val(activatedTab);

                // hide/show element relative to their tab position
                jQuery("#normal-sortables,#extended-sortable").children().each(function (index,elem) {
                    elem = jQuery(elem);
                    if (elem.hasClass('wpsearchconsole_metabox')) {
                        elem.hide();
                    } else {
                        elem.show();
                    }
                });
                break;
            case 'taxonomy':
                jQuery( "#edittag .form-table,.wrap h1,#poststuff, #edittag #submit" ).show();

                // hide/show element relative to their tab position
                jQuery("#edittag").children().each(function (index,elem) {
                    elem = jQuery(elem);
                    if (elem.hasClass('wpsearchconsole_metabox')) {
                        elem.hide();
                    } else {
                        elem.show();
                    }
                });
                break;

        }

    } else {

        switch (pageContext) {
            case 'post':
                jQuery("#postdivrich").hide();
                jQuery("#avia-builder-button").hide();
                jQuery("#postbox-container-2" ).removeClass('wpsearchconsole-editor');
                jQuery("#normal-sortables,#extended-sortable").children().each(function (index,elem) {
                    elem = jQuery(elem);
                    // display/hide all wpsc metabox present in selectedTabs
                    if (elem.hasClass('wpsearchconsole_metabox')) {
                        if (selectedTabs.indexOf(elem.attr('id')) >= 0) {
                            elem.show();
                        } else {
                            elem.hide();
                        }
                    } else {
                        elem.hide();
                    }
                });
                break;
            case 'taxonomy':
                jQuery( "#edittag .form-table,.wrap h1,#poststuff, #edittag #submit" ).hide();

                jQuery("#edittag").children().each(function (index,elem) {
                    elem = jQuery(elem);
                    // display/hide all wpsc metabox presetn in selectedTabs
                    if (elem.hasClass('wpsearchconsole_metabox')) {
                        if (selectedTabs.indexOf(elem.attr('id')) >= 0) {
                            elem.show();
                        } else {
                            elem.hide();
                        }
                    } else {
                        elem.hide();
                    }
                });
                break;
        }
    }
}

jQuery(document).ready(function () {
    for (var i = 0; i < 4; i++) {
        jQuery("#wpsearchconsole-" + i).click(function (event) {
            id = parseInt(jQuery(this).attr("data"));

            event.preventDefault();
            mitambo_activate_tabs(id)
        });
        jQuery("#wpsearchconsole-tab-" + i).click(function (event) {
            id = parseInt(jQuery(this).attr("data"));

            event.preventDefault();
            mitambo_activate_tabs(id)
        });
    }

    // we pickup either the focus_tab query string parameter or the hash(anchor #tabs0,#tabs1,#tabs2,#tabs3
    var focus_tab = getUrlVar('focus_tab');
    var current_tab = window.location.hash.substr(1);
    var current_tab_ID = current_tab ? parseInt(current_tab.substring(3)) : ( focus_tab ? focus_tab : 0) ;

    mitambo_activate_tabs(current_tab_ID);


});
