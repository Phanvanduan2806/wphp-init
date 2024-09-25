( function( $ ) {
    "use strict";
    var postCategoryUXB= function(){
        var self = this;
        self.classDeleteTemplate = '.post-category-uxb-delete-template';
        self.classActiveTemplate = '.post-category-uxb-active-template';
        self.classDuplicateTemplate = '.post-category-uxb-duplicate-template';
        // INIT
        self.init = function() {   
            $(document).on('click', self.classDeleteTemplate, function(event) {
                event.preventDefault();
                if( confirm(post_cat_uxb_data.sure_delete_this) ) {
                    var _this           = $(this),
                        _parent         = _this.closest('.post-archive-templates'),
                        _id             = _this.data('template-id'),
                        language        = _this.data('language');
                   
                    _this.addClass('updating-message');
                    self.templateAction(_parent,'delete');
                    $.ajax({
                        url  : ajaxurl,
                        type : 'POST',
                        data : {
                            'action'        : 'post_cat_uxb_delete',
                            'nonce'         : post_cat_uxb_data.nonce,
                            'id'            : _id,
                            'language'      : language
                        }
                    })
                    .done(function (res) {
                        _this.removeClass('updating-message');
                        if(res.success){                  
                            $('html, body').animate({ scrollTop: 0 }, '200');
                            location.reload(); // refresh page
                        } 
                    })
                    .fail(function (res) {
                        _this.removeClass('updating-message');                     
                        console.log(res);
                    });
                }
            });
            
            $(document).on('click', self.classActiveTemplate, function(event) {
                event.preventDefault();
                var _this       = $(this),
                    _parent     = _this.closest('.post-archive-templates'),
                    _id         = _this.data('template-id'),
                    language    = _this.data('language');
                _this.addClass('updating-message');
                self.templateAction(_parent,'active');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'post_cat_uxb_active',
                        'nonce' : post_cat_uxb_data.nonce,
                        'id'    : _id,
                        'language': language,
                        'type'  : post_cat_uxb_data.type
                    }
                })
                .done(function (res) {
                    if(res.success) {
                        _this.removeClass('updating-message');
                        $('html, body').animate({ scrollTop: 0 }, '200');
                        location.reload(); // refresh page
                    } else {
                        console.log(res);
                    }
                })
                .fail(function (res) {
                    _this.removeClass('updating-message');
                    console.log(res);
                });
            });

            $(document).on('click', self.classDuplicateTemplate, function(event) {
                event.preventDefault();
                var _this       = $(this),
                    _parent     = _this.closest('.post-archive-templates'),
                    _id         = _this.data('template-id');
                _this.addClass('updating-message');
                self.templateAction(_parent,'duplicate');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'post_cat_uxb_duplicate',
                        'nonce' : post_cat_uxb_data.nonce,
                        'id'    : _id
                    }
                })
                .done(function (res) {
                    if(res.success) {
                        _this.removeClass('updating-message');
                        $('html, body').animate({ scrollTop: 0 }, '200');
                        location.reload(); // refresh page
                    } else {
                        console.log(res);
                    }
                })
                .fail(function (res) {
                    _this.removeClass('updating-message');
                    console.log(res);
                });
            });
        };

        self.templateAction = function(_parent, action = '') {
            var action_element = _parent.find('.archive-template-action');
            action_element.show();
            switch (action) {
                case 'active':
                    action_element.html('<p>'+post_cat_uxb_data.active+'</p>');
                    break;
                case 'duplicate':
                    action_element.html('<p>'+post_cat_uxb_data.duplicate+'</p>');
                    break;
                default: // delete
                    action_element.html('<p>'+post_cat_uxb_data.delete+'</p>');
                    break;
            }
            
        };

    };
      
    jQuery(document).ready(function($){
        var post_cat_uxb = new postCategoryUXB();
        post_cat_uxb.init();
    });
} )( jQuery );