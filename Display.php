<?php

class BPFWidget extends WP_Widget
{

    function BPFWidget()
    {
        parent::WP_Widget(false, $name = 'Bit Post Filter' );
    }

    function form($instance)
    {

        if( $instance) {
            $title = esc_attr($instance['title']);
            $class = esc_attr($instance['class']);
            $unique_id = esc_textarea($instance['unique_id']);
        } else {
            $title = '';
            $class = '';
            $unique_id = '';
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wordpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('CSS Class:', 'wordpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo $class; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('unique_id'); ?>"><?php _e('CSS ID:', 'wordpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('unique_id'); ?>" name="<?php echo $this->get_field_name('unique_id'); ?>" type="text" value="<?php echo $unique_id; ?>" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['text'] = strip_tags($new_instance['text']);
        $instance['textarea'] = strip_tags($new_instance['textarea']);
        return $instance;
    }

    function widget($args, $instance)
    {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $class = $instance['class'];
        $unique_id = $instance['unique_id'];
        echo $before_widget;
        echo '<div class="widget-text bit_post_filter_box ' . $class . '" ' . ( $unique_id ? 'id="' . $unique_id . '"' : '' ) . '>';
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }
        global $bpf;
        $fields = $bpf->get_fields();
        $filter = new BPFFilter($fields);
        $filter->render();
        echo '</div>';
        echo $after_widget;
    }

}

class BPFShortcodeFilter
{

    private $fields = array();

    public function __construct()
    {
        global $bpf;
        $this->fields = $bpf->get_fields();
    }

    public function render($attrs)
    {
        echo '<div class="bit_post_filter_box ' . $attrs['class'] . '" ' . ( isset($attrs['id']) ? 'id="' . $attrs['id'] . '"' : '' ) . '>';
        if ( $attrs['title'] ) {
            echo '<h3 class="bpf_sc_title">' . $attrs['title'] . '</h3>';
        }
        $filter = new BPFFilter($this->fields);
        $filter->render();
        echo '</div>';
    }

}

class BPFShortcodeResults
{

    private $query = null;

    public function __construct()
    {
        global $bpf;
        $this->query = $bpf->get_query();
    }

    public function render($attrs)
    {
        global $wp_query;
        $wp_query = $this->query;
        $ob = true;
        $output = include '_results-loop.php';
        wp_reset_query();
        return $output;
    }
}