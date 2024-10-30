jQuery(document).ready(function ($) {
    
    jQuery('#mm_login_instagram_btn').click(function(){
        var input_client_id = jQuery('#input_client_id').val();
        if (input_client_id === '') {
            alert('Please enter Client ID...');
        } else {
            var redirect_url = jQuery('#input_redirect_url').val();
            
            jQuery.ajax({
                type: 'post',
                data: {
                'action': 'save_mm_instagram_client_id',
                'input_client_id': input_client_id,
                },
                url: mmAjax.ajaxurl,
                success: function (msg) {
                    window.location.href = "https://instagram.com/oauth/authorize/?client_id="+input_client_id+"&redirect_uri="+redirect_url+"&response_type=token";
                }
             });
        }
    });

    //Autofill the token and id
    var hash = window.location.hash,
            token = hash.substring(14),
            id = token.split('.')[0];

    //If there's a hash then autofill the token and id
    if (hash) {
        $('#mm_instagram_access_token').val(token);
        $('#mm_instagram_user_id').val(id);
        $('#submit').click();
        /*$('#mm_config').append('<div id="mm_config_info"><p><b>Access Token: </b><input type="text" size=58 readonly value="'+token+'" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)."></p><p><b>User ID: </b><input type="text" size=12 readonly value="'+id+'" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)."></p><p>Copy and paste these into the fields below, or use a different Access Token and User ID if you wish.</p></div>');*/
    }

});
