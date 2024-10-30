<?php

class MM_Instagram_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'mm_instagram_widget', // Base ID
                __('MM Instagram', 'text_domain'), // Name
                array('description' => __('MM Instagram Widget', 'text_domain'),) // Args
        );
    }

    public function widget($args, $instance) {
        echo do_shortcode('[mm_data_instagram_widget]');
    }

    public function form($instance) {
        $number_display = !empty($instance['number_display']) ? $instance['number_display'] : __('MM Instagram', 'text_domain');

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('number_display'); ?>"><?php _e('Number display:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number_display'); ?>" name="<?php echo $this->get_field_name('number_display'); ?>" type="number" value="<?php echo ($number_display != 'MM Instagram') ? $number_display : 2; ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['number_display'] = (!empty($new_instance['number_display']) ) ? strip_tags($new_instance['number_display']) : '';

        $options = wp_parse_args(get_option('mm_instagram_settings'));
        $mm_instagram_access_token = $options['mm_instagram_access_token'];
        $mm_instagram_user_id = $options['mm_instagram_user_id'];
        $mm_instagram_hashtag = $options['mm_instagram_hashtag'];
        $number_display = $options['number_display'];

        $options[ 'mm_instagram_access_token' ] = $mm_instagram_access_token;
        $options[ 'mm_instagram_user_id' ] = $mm_instagram_user_id;
        $options[ 'mm_instagram_hashtag' ] = $mm_instagram_hashtag;
        $options[ 'number_display' ] = $instance['number_display'];
        update_option( 'mm_instagram_settings', $options );

        return $instance;
    }

}

function register_mm_instagram_widget() {
    register_widget('MM_Instagram_Widget');
}

add_action('widgets_init', 'register_mm_instagram_widget');


