jQuery(document).ready(function () { 
    var mm_instagram_widget_access_token = jQuery("#mm_instagram_widget_access_token").val();
    var mm_instagram_widget_user_id = jQuery("#mm_instagram_widget_user_id").val();
    var mm_instagram_widget_hashtag = jQuery("#mm_instagram_widget_hashtag").val();
    var number_display = jQuery("#number_display").val();


    jQuery.ajax({
        type: 'post',
        data: {
            'action': 'get_data_instagram_widget',
            'mm_instagram_widget_access_token': mm_instagram_widget_access_token,
            'mm_instagram_widget_user_id': mm_instagram_widget_user_id,
            'mm_instagram_widget_hashtag': mm_instagram_widget_hashtag,
            'number_display': number_display
        },
        url: mmAjax.ajaxurl,
        beforeSend: function () {
            jQuery('.loading-instagram-widget').show();
        },
        success: function (msg) {
            //var data = JSON.parse(msg);
            jQuery('.loading-instagram-widget').hide();
            jQuery('.content-photo-widget').append(msg);
        }
    });
    
    var mm_instagram_access_token = jQuery("#mm_instagram_access_token").val();
    var mm_instagram_user_id = jQuery("#mm_instagram_user_id").val();
    var mm_instagram_hashtag = jQuery("#mm_instagram_hashtag").val();

    jQuery.ajax({
        type: 'post',
        data: {
            'action': 'get_more_data_instagram_first',
            'mm_instagram_access_token': mm_instagram_access_token,
            'mm_instagram_user_id': mm_instagram_user_id,
            'mm_instagram_hashtag': mm_instagram_hashtag
        },
        url: mmAjax.ajaxurl,
        beforeSend: function () {
            jQuery('.loading-insta').show();
        },
        success: function (msg) {
            //var data = JSON.parse(msg);
            jQuery('.loading-insta').hide();
            jQuery('.content-image-photo').append(msg);
        }
    });
    
   
});

function load_more_instagram(obj) {
    var tag = jQuery(obj).data('tag'),
            maxid = jQuery(obj).data('maxid');
    var numcur = jQuery('.numcur').val();

    var mm_instagram_access_token = jQuery("#mm_instagram_access_token").val();
    var mm_instagram_user_id = jQuery("#mm_instagram_user_id").val();
    var mm_instagram_hashtag = jQuery("#mm_instagram_hashtag").val();

    if (maxid) {
        jQuery.ajax({
            type: 'post',
            data: {
                'action': 'get_more_data_instagram',
                'tag': tag,
                'maxid': maxid,
                'numcur': numcur,
                'mm_instagram_access_token': mm_instagram_access_token,
                'mm_instagram_user_id': mm_instagram_user_id,
                'mm_instagram_hashtag': mm_instagram_hashtag
            },
            url: mmAjax.ajaxurl,
            beforeSend: function () {
                //jQuery('#save-setting-ecna').text('Saving...');
            },
            success: function (msg) {
                var data = JSON.parse(msg);

                jQuery.each(data.images, function (i, img) {
                    jQuery('.content-image-photo').append('<div class="item-photo item-photo-extra"><a href="javascript:void(0);" onclick="click_light_instagram(\'' + i + '\');">' + img + '<span class="image-overlay overlay-type-extern" style="left: -5px; top: -112.2px; overflow: hidden; display: block; height: 128px; width: 138px;"><span class="image-overlay-inside"></span></span></a></div>');
                });
                jQuery.each(data.htmlcomment, function (i, cm) {
                    jQuery('.content-image-photo').append(cm);
                });
                // Store new maxid
                jQuery('#instagrammore').data('maxid', data.next_id);
            }
        });
    }
}

jQuery(window).resize(function () {
    jQuery('.content-image-photo').css('top', '0px');
    jQuery('.current_top').val(0);
}).trigger('resize');

function up_content_insta() {
    var current_top = jQuery('.current_top').val();
    var minus = jQuery('.item-photo-7').outerHeight(true);
    var next_top = parseInt(current_top) + parseInt(minus);

    if (parseInt(current_top) < 0) {
        jQuery('.current_top').val(next_top);
        jQuery('.content-image-photo').css('top', next_top + 'px');
    }
}

function down_content_insta() {
    var current_top = jQuery('.current_top').val();
    var minus = jQuery('.item-photo-7').outerHeight(true);
    var stopload = jQuery('.stop_load').val();
    var next_top = parseInt(current_top) - parseInt(minus);
    jQuery('.current_top').val(next_top);
    jQuery('.content-image-photo').css('top', next_top + 'px');
    var countimg = jQuery('.large-page').length;
    jQuery('#instagrammore').trigger('click');

}
function click_light_instagram(id) {
    jQuery('#light').show();
    jQuery('#fade').show();
    jQuery('.wrap-popup').show();
    jQuery('.updown_instagram').hide();
    var html = jQuery('.item-comment-' + id).html();
    var img_large = jQuery('.item-image-large-' + id).val();
    jQuery('.image-head-content').html("<img src='" + img_large + "' alt='' />");
    jQuery('.content-light-box').html(html);
}
function click_fade_instagram() {
    jQuery('#light').hide();
    jQuery('#fade').hide();
    jQuery('.wrap-popup').hide();
    jQuery('.updown_instagram').show();
}
function stop_load() {
    jQuery('.stop_load').val('yes');
}