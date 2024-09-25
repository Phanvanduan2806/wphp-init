( function( $ ) {
    "use strict";
    $(window).on('load', function() {
        $(window).off('beforeunload');//Disable “Changes you made may not be saved” pop-up window.
        if(post_cat_uxb_data.is_publish === 'no') {
            $(document).on('click', 'h2#uxbuilder-enable-disable a.nav-tab:last-child', function(event) {
                event.preventDefault();
                alert(post_cat_uxb_data.alert_publish);
            });
        }
        if( post_cat_uxb_data.is_page === 'edit' ) {
            $('a.page-title-action').attr('href',post_cat_uxb_data.url_add_new);
        }
    });
   
} )( jQuery );