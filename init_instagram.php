<?php
/*
  Plugin Name: Maui Marketing Instagram Feed
  Plugin URI: http://mauimarketing.com
  Description: This is a plugin to help you display your Instagram photos in your website.  Placement of the feed occurs using the widget or ShortCode.  One hashtag enabled feed per site.
  Version: 1.0.1
  Author: Maui Marketing
  Author URI: http://mauimarketing.com/
  License: GPLv2 or later
 */


include dirname(__FILE__) . '/admin_instagram.php';
include dirname(__FILE__) . '/widget_instagram.php';

add_action('wp_enqueue_scripts', 'plugin_enqueue_styles');

function plugin_enqueue_styles() {
    wp_enqueue_style('instagram-style', plugin_dir_url(__FILE__) . 'css/instagram_style.css');
    wp_enqueue_style('fancybox-style', plugin_dir_url(__FILE__) . 'css/jquery.fancybox-1.3.4.css');
    wp_enqueue_script('fancybox-script', plugin_dir_url(__FILE__) . 'js/jquery.fancybox-1.3.4.pack.js',array('jquery'));
    wp_register_script('instagram-script', plugin_dir_url(__FILE__) . 'js/instagram_script.js');
    wp_localize_script( 'instagram-script', 'mmAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( 'instagram-script' );
}

function shortcode_get_data_instagram_widget($atts) {
    
    $options = wp_parse_args(get_option('mm_instagram_settings'));
    $mm_instagram_widget_access_token = $options['mm_instagram_access_token'];
    $mm_instagram_widget_user_id = $options['mm_instagram_user_id'];
    $mm_instagram_widget_hashtag = $options['mm_instagram_hashtag'];
    $number_display = $options['number_display'];
    
    ob_start();
    ?>
    <div class="wrap-all-instagram-widget">
        <div class="wrap-instagram-widget">
            <input type="hidden" value="<?php echo $mm_instagram_widget_access_token; ?>" id="mm_instagram_widget_access_token" />
            <input type="hidden" value="<?php echo $mm_instagram_widget_user_id; ?>" id="mm_instagram_widget_user_id" />
            <input type="hidden" value="<?php echo $mm_instagram_widget_hashtag; ?>" id="mm_instagram_widget_hashtag" />
            <input type="hidden" value="<?php echo $number_display; ?>" id="number_display" />
            <div class="wrap-photo-instagram-widget">
                <div class="loading-instagram-widget" ><img  src="<?php echo plugin_dir_url(__FILE__) . 'images/'; ?>balls.gif" /></div>
                <div class="content-photo-widget">

                </div>
            </div>
        </div>
    </div>

    <?php
    $html = ob_get_clean();
    return $html;
}

add_shortcode('mm_data_instagram_widget', 'shortcode_get_data_instagram_widget');


add_action("wp_ajax_get_data_instagram_widget", "get_data_instagram_widget");
add_action("wp_ajax_nopriv_get_data_instagram_widget", "get_data_instagram_widget");

function get_data_instagram_widget() {

    require plugin_dir_path(__FILE__) . "helper/src/Instagram.php";

    $mm_instagram_widget_access_token = $_POST['mm_instagram_widget_access_token'];
    $mm_instagram_widget_user_id = $_POST['mm_instagram_widget_user_id'];
    $mm_instagram_widget_hashtag = $_POST['mm_instagram_widget_hashtag'];
    $number_display = $_POST['number_display'];

    $instagram = new Instagram($mm_instagram_widget_access_token);
    $call = "https://api.instagram.com/v1/users/" . $mm_instagram_widget_user_id . "/media/recent?&access_token=" . $mm_instagram_widget_access_token;
    $media = json_decode(@file_get_contents($call));
    
    ob_start();
 
    $i = 1;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function() {
	jQuery("a.popup_images").fancybox();
    });
    </script>
    <h5 class="tile-photo-instagram">Maui Marketing Instagram</h5>
    <?php
    foreach ($media->data as $data) :
        if ($mm_instagram_widget_hashtag == '') {
            if ($i <= $number_display) { 
                ?>
                <div class="item-photo-widget item-photo-widget-<?php echo $i; ?>">  
                    <a class="popup_images" href="<?php echo $data->images->standard_resolution->url; ?>">
                        <img src="<?php echo $data->images->thumbnail->url; ?>">
                    </a>
                </div>
                <?php
                $i++;
            }
        } else {
            if(in_array($mm_instagram_widget_hashtag, $data->tags)){
                if ($i <= $number_display) {
                    ?>
                    <div class="item-photo-widget item-photo-widget-<?php echo $i; ?>">    
                        <a class="popup_images" href="<?php echo $data->images->standard_resolution->url; ?>">
                            <img src="<?php echo $data->images->thumbnail->url; ?>">
                        </a>
                    </div>
                    <?php
                    $i++;
                }
            }
        }
    endforeach;

    $html = ob_get_clean();
    echo $html;
    die();
}


function get_data_instagram($atts) {
    $options = wp_parse_args(get_option('mm_instagram_settings'));
    $mm_instagram_access_token = $options['mm_instagram_access_token'];
    $mm_instagram_user_id = $options['mm_instagram_user_id'];
    $mm_instagram_hashtag = $options['mm_instagram_hashtag'];
    
    require plugin_dir_path(__FILE__) . "helper/src/Instagram.php";

    $instagram = new Instagram($mm_instagram_access_token);
    $get_user = $instagram->getUser($mm_instagram_user_id);
    
    ob_start();
    ?>
    <div class="wrap-all-instagram">
        <input type="hidden" value="<?php echo $mm_instagram_access_token; ?>" id="mm_instagram_access_token" />
        <input type="hidden" value="<?php echo $mm_instagram_user_id; ?>" id="mm_instagram_user_id" />
        <input type="hidden" value="<?php echo $mm_instagram_hashtag; ?>" id="mm_instagram_hashtag" />
        <div class="wrap-section-photo-instagram">
            <div class="loading-insta" ><img  src="<?php echo plugin_dir_url(__FILE__) . 'images/'; ?>balls.gif" /></div>
            <div class="content-image-photo">

            </div>
            <!--popup-->
            <div class="wrap-popup">
                <div   class="close-xpopup" >
                    <h2 onclick="click_fade_instagram();" style="cursor:pointer;" class="modal-title">X</h2>
                </div>
                <div id="light" class="white_content">
                    <div style="margin-bottom:20px;"></div>
                    <div class="image-head-content" style="text-align: center;"></div>
                    <div class="content-light-box">

                    </div> 

                </div>
                <div id="fade" class="black_overlay" onclick="click_fade_instagram()"></div>
            </div>
            <!--end popup-->
        </div>
        <div class="updown_instagram">
            <a class="up" href="javascript:void(0);" onclick="up_content_insta();"><img alt="" src="<?php echo plugin_dir_url(__FILE__) . 'images/'; ?>arr-up.png"></a>
            <a target="_blank" class="inst" href="https://instagram.com/<?php echo $get_user->data->username; ?>/"><img alt="" src="<?php echo plugin_dir_url(__FILE__) . 'images/'; ?>inst.png"></a>
            <a class="down" href="javascript:void(0);" onclick="down_content_insta();"><img alt="" src="<?php echo plugin_dir_url(__FILE__) . 'images/'; ?>arr-down.png"></a>
        </div>
        <input value="0" class="current_top" type="hidden" />
        <input value="no" class="stop_load" type="hidden" />
    </div>


    <?php
    $html = ob_get_clean();
    return $html;
}

add_shortcode('mm_data_instagram', 'get_data_instagram');


add_action("wp_ajax_get_more_data_instagram_first", "get_more_data_instagram_first");
add_action("wp_ajax_nopriv_get_more_data_instagram_first", "get_more_data_instagram_first");

function get_more_data_instagram_first() {
    $mm_instagram_access_token = $_POST['mm_instagram_access_token'];
    $mm_instagram_user_id = $_POST['mm_instagram_user_id'];
    $mm_instagram_hashtag = $_POST['mm_instagram_hashtag'];

    $call = "https://api.instagram.com/v1/users/" . $mm_instagram_user_id . "/media/recent?&access_token=" . $mm_instagram_access_token;

    $media = json_decode(@file_get_contents($call));

    ob_start();
    ?>

    <?php
    $i = 0;
    foreach ($media->data as $data) :
        if ($mm_instagram_hashtag == '') {
            ?>
            <div class="item-photo item-photo-<?php echo $i; ?>">
                <a href="javascript:void(0);" data-img="<?php echo $data->images->low_resolution->url; ?>" onclick="click_light_instagram('<?php echo $data->id; ?>');">
                    <img class="large-page" src="<?php echo ($i == 0) ? $data->images->low_resolution->url : $data->images->thumbnail->url; ?>">
                    <img class="small-page" src="<?php echo $data->images->low_resolution->url; ?>">
                </a>
            </div>
            <input type="hidden" class="item-image-large-<?php echo $data->id; ?>" value="<?php echo $data->images->low_resolution->url; ?>" />
            <div class="wrap-content-comment item-comment-<?php echo $data->id; ?>" style="display:none;">
                <div class="item-caption">
                    <div class="left-cm">
                        <a target="_blank" href="https://instagram.com/<?php echo $data->caption->from->username; ?>"><img src="<?php echo $data->caption->from->profile_picture; ?>" /></a>
                    </div>
                    <div class="right-cm">
                        <p><?php echo $data->caption->from->username; ?></p>
                        <?php echo $data->caption->text; ?>
                    </div>
                </div>
                <?php foreach ($data->comments->data as $cmdata) { ?>
                    <div class="item-comment-detail">
                        <div class="left-cm">
                            <a target="_blank" href="https://instagram.com/<?php echo $cmdata->from->username; ?>">
                                <img src="<?php echo $cmdata->from->profile_picture; ?>" />
                            </a>
                        </div>
                        <div class="right-cm">
                            <p><?php echo $cmdata->from->username; ?></p>
                            <?php echo $cmdata->text; ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="wrap-like-cm" style="clear:both;text-align:center;">
                    <a href="<?php echo $data->link; ?>">Like or Comment on Instagram</a>
                </div>
            </div>

            <?php
            $i++;
        } else {
            if(in_array($mm_instagram_hashtag, $data->tags)){
            ?>
            <div class="item-photo item-photo-<?php echo $i; ?>">
                <a href="javascript:void(0);" data-img="<?php echo $data->images->low_resolution->url; ?>" onclick="click_light_instagram('<?php echo $data->id; ?>');">
                    <img class="large-page" src="<?php echo ($i == 0) ? $data->images->low_resolution->url : $data->images->thumbnail->url; ?>">
                    <img class="small-page" src="<?php echo $data->images->low_resolution->url; ?>">
                </a>
            </div>
            <input type="hidden" class="item-image-large-<?php echo $data->id; ?>" value="<?php echo $data->images->low_resolution->url; ?>" />
            <div class="wrap-content-comment item-comment-<?php echo $data->id; ?>" style="display:none;">
                <div class="item-caption">
                    <div class="left-cm">
                        <a target="_blank" href="https://instagram.com/<?php echo $data->caption->from->username; ?>"><img src="<?php echo $data->caption->from->profile_picture; ?>" /></a>
                    </div>
                    <div class="right-cm">
                        <p><?php echo $data->caption->from->username; ?></p>
                        <?php echo $data->caption->text; ?>
                    </div>
                </div>
                <?php foreach ($data->comments->data as $cmdata) { ?>
                    <div class="item-comment-detail">
                        <div class="left-cm">
                            <a target="_blank" href="https://instagram.com/<?php echo $cmdata->from->username; ?>">
                                <img src="<?php echo $cmdata->from->profile_picture; ?>" />
                            </a>
                        </div>
                        <div class="right-cm">
                            <p><?php echo $cmdata->from->username; ?></p>
                            <?php echo $cmdata->text; ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="wrap-like-cm" style="clear:both;text-align:center;">
                    <a href="<?php echo $data->link; ?>">Like or Comment on Instagram</a>
                </div>
            </div>

            <?php
            $i++;
            }
        }
    endforeach;
    ?>
    <input class="numcur" type="hidden" value="20" />
    <button style="display:none;" id="instagrammore" onclick="load_more_instagram(this);" data-maxid="<?php echo $media->pagination->next_max_id; ?>" data-tag="<?php echo $tag; ?>" >more</button>

    <?php
    $html = ob_get_clean();
    echo $html;
    die();
}

add_action("wp_ajax_get_more_data_instagram", "get_more_data_instagram");
add_action("wp_ajax_nopriv_get_more_data_instagram", "get_more_data_instagram");

function get_more_data_instagram() {
    $mm_instagram_access_token = $_POST['mm_instagram_access_token'];
    $mm_instagram_user_id = $_POST['mm_instagram_user_id'];
    $mm_instagram_hashtag = $_POST['mm_instagram_hashtag'];

    require plugin_dir_path(__FILE__) . "helper/src/Instagram.php";

    // Initialize class for public requests
    $instagram = new Instagram($mm_instagram_access_token);
    // Receive AJAX request and create call object
    // $tag = $_POST['tag'];
    $maxID = $_POST['maxid'];
    $clientID = $instagram->getApiKey();

    $call = new stdClass;


    $call->pagination->next_max_id = $maxID;
    //$call->pagination->next_url = "https://api.instagram.com/v1/tags/{$tag}/media/recent?client_id={$clientID}&max_tag_id={$maxID}";
    $call->pagination->next_url = "https://api.instagram.com/v1/users/" . $mm_instagram_user_id . "/media/recent?max_id=" . $maxId . "&client_id={$clientID}";
    // Receive new data
    $media = $instagram->pagination($call);

    $images = array();
    $htmlarr = array();

    $numcur = $_POST['numcur'];
    $num = $numcur;
    foreach ($media->data as $data) {
        if ($mm_instagram_hashtag == '') {
            if ($num == 21) {
                $img_html = '<img class="large-page" onload="stop_load();" src="' . $data->images->thumbnail->url . '">
                                                    <img class="small-page" onload="stop_load();" src="' . $data->images->low_resolution->url . '">';
            } else {
                $img_html = '<img class="large-page"  src="' . $data->images->thumbnail->url . '">
                                                    <img class="small-page"  src="' . $data->images->low_resolution->url . '">';
            }


            $images[$data->id] = $img_html; //$data->images->thumbnail->url;
            $html = '';
            $html .= '<input type="hidden" class="item-image-large-' . $data->id . '" value="' . $data->images->low_resolution->url . '" />';
            $html .= '<div class="wrap-content-comment item-comment-' . $data->id . '" style="display:none;">';
            $html .= '<div class="item-caption">
                                                    <div class="left-cm">
                                                    <a target="_blank" href="https://instagram.com/' . $data->caption->from->username . '"><img src="' . $data->caption->from->profile_picture . '" /></a>
                                                    </div>
                                                    <div class="right-cm">
                                                    <p>' . $data->caption->from->username . '</p>
                                                    ' . $data->caption->text . '
                                                    </div>
                                            </div>';

            foreach ($data->comments->data as $cmdata) {
                $html .= '<div class="item-comment-detail">
                                                    <div class="left-cm">
                                                            <a target="_blank" href="https://instagram.com/' . $cmdata->from->username . '">
                                                                    <img src="' . $cmdata->from->profile_picture . '" />
                                                            </a>
                                                    </div>
                                                    <div class="right-cm">
                                                    <p>' . $cmdata->from->username . '</p>
                                                    ' . $cmdata->text . '
                                                    </div>
                                            </div>';
            }
            $html .= '<div class="wrap-like-cm" style="clear:both;text-align:center;">
                                                            <a href="' . $data->link . '">Like or Comment on Instagram</a>
                                              </div>';
            $html .= '</div>';
            $htmlarr[] = $html;
            $num ++;
        } else {
            if(in_array($mm_instagram_hashtag, $data->tags)){
                if ($num == 21) {
                    $img_html = '<img class="large-page" onload="stop_load();" src="' . $data->images->thumbnail->url . '">
                                                        <img class="small-page" onload="stop_load();" src="' . $data->images->low_resolution->url . '">';
                } else {
                    $img_html = '<img class="large-page"  src="' . $data->images->thumbnail->url . '">
                                                        <img class="small-page"  src="' . $data->images->low_resolution->url . '">';
                }


                $images[$data->id] = $img_html; //$data->images->thumbnail->url;
                $html = '';
                $html .= '<input type="hidden" class="item-image-large-' . $data->id . '" value="' . $data->images->low_resolution->url . '" />';
                $html .= '<div class="wrap-content-comment item-comment-' . $data->id . '" style="display:none;">';
                $html .= '<div class="item-caption">
                                                        <div class="left-cm">
                                                        <a target="_blank" href="https://instagram.com/' . $data->caption->from->username . '"><img src="' . $data->caption->from->profile_picture . '" /></a>
                                                        </div>
                                                        <div class="right-cm">
                                                        <p>' . $data->caption->from->username . '</p>
                                                        ' . $data->caption->text . '
                                                        </div>
                                                </div>';

                foreach ($data->comments->data as $cmdata) {
                    $html .= '<div class="item-comment-detail">
                                                        <div class="left-cm">
                                                                <a target="_blank" href="https://instagram.com/' . $cmdata->from->username . '">
                                                                        <img src="' . $cmdata->from->profile_picture . '" />
                                                                </a>
                                                        </div>
                                                        <div class="right-cm">
                                                        <p>' . $cmdata->from->username . '</p>
                                                        ' . $cmdata->text . '
                                                        </div>
                                                </div>';
                }
                $html .= '<div class="wrap-like-cm" style="clear:both;text-align:center;">
                                                                <a href="' . $data->link . '">Like or Comment on Instagram</a>
                                                  </div>';
                $html .= '</div>';
                $htmlarr[] = $html;
                $num ++;
            }
        }
    }



    echo json_encode(array(
        'next_id' => $media->pagination->next_max_id,
        'images' => $images,
        'htmlcomment' => $htmlarr
    ));
    die();
}
