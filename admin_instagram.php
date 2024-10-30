<?php

function load_instagram_admin_style() {
    wp_register_style('instagram_admin_css', plugin_dir_url(__FILE__) . 'css/admin_style.css', false, '1.0.0');
    wp_enqueue_style('instagram_admin_css');

    wp_enqueue_script('instagram_admin_js', plugin_dir_url(__FILE__) . 'js/instagram_admin_script.js',array('jquery'));
}

add_action('admin_enqueue_scripts', 'load_instagram_admin_style');


add_action('admin_menu', 'register_instagrams_menu');

function register_instagrams_menu() {
    add_menu_page('Maui Marketing Instagram Feed', 'MM Instagram', 'manage_options', 'mm_instagram', 'instagram_menu_page', plugins_url('instagram-maui-marketing/images/instagram-icon.jpg'), 100);
}

function instagram_menu_page() {
    //Declare defaults
    $options = wp_parse_args(get_option('mm_instagram_settings'));
    $mm_instagram_client_id = wp_parse_args(get_option('mm_instagram_client_id'));


    $mm_instagram_access_token = $options['mm_instagram_access_token'];
    $mm_instagram_user_id = $options['mm_instagram_user_id'];
    $mm_instagram_hashtag = $options['mm_instagram_hashtag'];
    $number_display = $options['number_display'];

    if( isset($_POST[ 'submit' ])){
        $mm_instagram_access_token = $_POST[ 'mm_instagram_access_token' ];
        $mm_instagram_user_id = $_POST[ 'mm_instagram_user_id' ];
        $mm_instagram_hashtag = $_POST[ 'mm_instagram_hashtag' ];
        $mm_instagram_client_id = $_POST[ 'input_client_id' ];

        $options[ 'mm_instagram_access_token' ] = $mm_instagram_access_token;
        $options[ 'mm_instagram_user_id' ] = $mm_instagram_user_id;
        $options[ 'mm_instagram_hashtag' ] = $mm_instagram_hashtag;
        $options[ 'number_display' ] = $number_display;
        update_option( 'mm_instagram_settings', $options );

        $options_client_id[ 'mm_instagram_client_id' ] = $mm_instagram_client_id;
        update_option( 'mm_instagram_client_id', $options_client_id );

        ?>
        <script>window.location.href='admin.php?page=mm_instagram';</script>
        <?php
    }
    ?>

    <div id="sbi_admin" class="wrap">
        <div class="maui_logo center">
                <a href="http://mauimarketing.com/"><img height="100" width="300" src="<?php echo plugin_dir_url(__FILE__); ?>/images/logo.png" alt="Maui Marketing" title="Maui Marketing"></a>
        </div>
        <hr>
        <h3><?php _e('Maui Marketing Instagram Feed'); ?></h3>


        <form id="mm_instagram_form" name="mm_instagram_form" method="post" action="">
            <div id="mm_config">
                <table class="form-table">
                    <tbody>

                    <tr valign="top">
                        <th scope="row"><label><?php _e('Client ID'); ?></label></th>
                        <td>
                            <input type="text" name="input_client_id"  id="input_client_id" size="60" value="<?php echo $mm_instagram_client_id['mm_instagram_client_id']; ?>"/>
                            <input type="hidden" name="input_redirect_url"  id="input_redirect_url" size="60" value="<?php echo admin_url('admin.php?page=mm_instagram'); ?>"/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label></label></th>
                        <td>
                            <span>
                                <a href="javascript:void(0);" id="mm_login_instagram_btn" class="mm_login_instagram_btn"><?php _e('Log in and get your Access Token and User ID'); ?></a>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <hr>

            <table class="form-table">
                <tbody>

                <tr valign="top">
                    <th scope="row"><label><?php _e('Access Token'); ?></label></th>
                    <td>
                        <input name="mm_instagram_access_token" id="mm_instagram_access_token" type="text" value="<?php esc_attr_e($mm_instagram_access_token); ?>" size="60" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('User ID(s):'); ?></label></th>
                    <td>
                        <span>
                            <input name="mm_instagram_user_id" id="mm_instagram_user_id" type="text" value="<?php esc_attr_e($mm_instagram_user_id); ?>" size="25" />
                            <br />
                        </span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Hashtag:'); ?></label></th>
                    <td>
                        <span>
                            <input name="mm_instagram_hashtag" id="mm_instagram_hashtag" type="text" value="<?php esc_attr_e($mm_instagram_hashtag); ?>" size="25" />
                            <br />
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>
        <hr />
        <p><?php _e('Instagram: <a href="https://instagram.com/" target="_blank">Go to your Instagram</a>'); ?></p>

        <p><?php _e('Shortcode: <input type="text" readonly="readonly" value="[mm_data_instagram]" onclick="this.select()"/>'); ?></p>
    </div>

    <?php

}



add_action("wp_ajax_save_mm_instagram_client_id", "save_mm_instagram_client_id");
add_action("wp_ajax_nopriv_save_mm_instagram_client_id", "save_mm_instagram_client_id");

function save_mm_instagram_client_id() {
    $options[ 'mm_instagram_client_id' ] = $_POST['input_client_id'];
    update_option( 'mm_instagram_client_id', $options );
}



