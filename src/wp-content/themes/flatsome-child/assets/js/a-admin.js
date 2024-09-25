(function($) {
    $(document).ready(function() {
        console.log("Admin JS is loaded");

        var currentUrl = window.location.href;
        if (currentUrl.includes("post-new.php?post_type=page")) {
            var pageTemplateSelect = $('#page_template');
            if (pageTemplateSelect.length) {
                pageTemplateSelect.val('page-blank.php');
            }
        }
    });
})(jQuery);
