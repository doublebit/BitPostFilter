<?php

/*
Plugin Name: Bit Post Filter
Plugin URI: https://github.com/doublebit/BitPostFilter
Description: Filter posts based on custom fields
Version: 0.1
Author: Vasile Goian
Author URI: https://github.com/doublebit
License: GPLv3
*/


/**
 * Include the dependencies
 */
include_once 'Utils.php';
include_once 'Options.php';
include_once 'Filter.php';
include_once 'Results.php';
include_once 'Display.php';

/**
 * Class BitPostFilter
 */
class BitPostFilter
{

    /**
     * @var array Searchable fields
     */
    protected $fields = array();

    /**
     * @var null|object The $wp_query object
     */
    protected $query = null;

    /**
     *
     */
    public function __construct()
    {
        global $wpdb;

        $r = $wpdb->get_results( $wpdb->prepare( "
        SELECT pm.meta_key as mk, pm.meta_value as mv FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_status = '%s'
        AND p.post_type = '%s'
    ", 'publish', 'post' ) );
        $f = array();
        foreach ($r as $v) {
            if ($v->mk[0] != '_') {
                $f[$v->mk][] = $v->mv;
                $f[$v->mk] = array_unique($f[$v->mk]);
            }
        }
        $this->fields = $f;
    }

    /**
     * Plugin initialization function
     */
    public function init()
    {
        $o = new BPFOptions($this->fields);

        add_action( 'admin_menu', array ($o, 'bpf_add_admin_menu' ));
        add_action( 'admin_init', array ($o, 'bpf_settings_init' ));


        $sc = new BPFShortcodeFilter();
        add_shortcode('bit_post_filter', array($sc, 'render'));

        $scr = new BPFShortcodeResults();
        add_shortcode('bit_post_filter_results', array($scr, 'render'));

        $is_search = false;
        foreach ($this->fields as $field => $values) {
            if (isset($_GET[BPFUtils::slugify($field)])) {
                $is_search = true;
            }
        }
        if ($is_search) {
            $results = new BPFResults($this->fields);
            $results->search();
            $this->query = $results->get_query();
            add_action("template_redirect", array ($this, 'results_redirect'));
        }
    }

    /**
     * Get the searchable fields
     * @return array
     */
    public function get_fields()
    {
        return $this->fields;
    }

    /**
     * Get the $wp_query object from the search
     * @return null|object
     */
    public function get_query()
    {
        /**
         * If the search was not performed, do it now
         */
        if ($this->query == null) {
            $results = new BPFResults($this->fields);
            $results->search();
            $this->query = $results->get_query();
        }
        return $this->query;
    }

    /**
     * Redirect or load another file based on the page selected as the result page in the options
     */
    function results_redirect() {
        $options = get_option( 'bpf_settings' );
        $redirect_to = isset($options['results-page']) ? $options['results-page'] : 'plugin-page';
        if ($redirect_to == 'plugin-page') {
            $plugindir = dirname( __FILE__ );

            if (file_exists(TEMPLATEPATH . '/bpf_results.php')) {
                $return_template = TEMPLATEPATH . '/bpf_results.php';
            } else {
                $return_template = $plugindir . '/results-page.php';
            }
            global $bpf;
            require_once $return_template;
        } elseif ($redirect_to == 'category-page') {
            if (file_exists(TEMPLATEPATH . '/category.php')) {
                $return_template = TEMPLATEPATH . '/category.php';
            } elseif (file_exists(TEMPLATEPATH . '/archive.php')) {
                $return_template = TEMPLATEPATH . '/archive.php';
            } else {
                // 404
            }
            require_once $return_template;
            exit;
        } else {
            wp_reset_query();
            if (get_permalink($redirect_to) != get_permalink(get_the_ID())) {
                wp_redirect( add_query_arg(str_ireplace(' ', '%20', $_GET), get_permalink($redirect_to))); exit;
            }
        }
    }


}

// initialize the plugin

$bpf = new BitPostFilter();

add_action('widgets_init', create_function('', 'return register_widget("BPFWidget");'));
add_action('init', array($bpf, 'init'));
