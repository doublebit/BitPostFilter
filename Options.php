<?php

/**
 * Class BPFOptions
 * Plugin options
 */
class BPFOptions
{

    /**
     * @var array Fields that can be searched
     */
    protected $custom_fields = array();

    /**
     * @var array Current options (only those related to the plugin)
     */
    public $options = array();

    /**
     * @param $fields
     */
    public function __construct($fields)
    {
        $this->custom_fields = $fields;
        $this->options = get_option('bpf_settings');

    }

    /**
     * Add the options page to the wp-admin menu
     */
    function bpf_add_admin_menu()
    {

        add_menu_page('Bit Post Filter', 'Bit Post Filter', 'manage_options', 'bit_post_filter', array($this, 'bpf_options_page'));

    }

    /**
     * Initialize the options page
     */
    function bpf_settings_init()
    {

        register_setting('pluginPage', 'bpf_settings');

        /**
         * Add searchable fields section
         */
        add_settings_section(
            'bpf_pluginPage_section',
            __('Searchable fields', 'wordpress'),
            array($this, 'bpf_settings_section_callback'),
            'pluginPage'
        );

        /**
         * Loop through all custom fields and assign them as checkboxes
         */
        foreach ($this->custom_fields as $field => $values) {
            add_settings_field(
                BPFUtils::slugify($field),
                $field,
                array($this, 'bpf_checkbox_field_render'),
                'pluginPage',
                'bpf_pluginPage_section',
                array(BPFUtils::slugify($field), $values)
            );
        }

        /**
         * Assign the pages for the result page selection
         */
        $pages = get_pages();
        $opts = array('plugin-page' => 'Bit Post Filter Custom Page (default)');
        $opts['category-page'] = 'Theme category page';
        foreach ($pages as $page) {
            $opts[$page->ID] = $page->post_title;
        }

        add_settings_field(
            'results-page',
            'Results page',
            array($this, 'bpf_select_field_render'),
            'pluginPage',
            'bpf_pluginPage_section',
            array('name' => 'results-page', 'values' => $opts)
        );

    }

    /**
     * Displays the description of the options section
     */
    function bpf_settings_section_callback()
    {

        echo __('Select the custom fields that you want to be searchable. After adding a new custom field to a post,
    you need to activate it as searchable here before it showing in the filter widget', 'wordpress');

    }

    /**
     * Render checkboxes
     * @param array $options
     */
    function bpf_checkbox_field_render($options = array())
    {

        ?>
        <input type='checkbox'
               name='bpf_settings[<?php echo $options[0]; ?>]' <?php checked($this->options[$options[0]], 1); ?> value='1'>
        Values: <b><?php echo implode(', ', $options[1]); ?></b>
        <?php

    }

    /**
     * Render select boxes
     * @param array $options
     */
    function bpf_select_field_render($options = array())
    {
        ?>
        <select name="bpf_settings[<?php echo $options['name']; ?>]">
            <?php
            foreach ($options['values'] as $val => $display) {
                echo '<option value="' . $val . '" ' . ($this->options[$options['name']] == $val ? 'selected' : '') . '>' . $display . '</option>';
            }
            ?>
        </select>
        If you chose a custom page to display the result, you need to create a custom template in your theme or use the
        shortcode <code>[bit_post_filter_results]</code> to be able to display the results
        <?php

    }

    /**
     * Render the options page
     */
    function bpf_options_page()
    {

        ?>
        <form action='options.php' method='post'>

            <h2>Bit Post Filter</h2>

            <?php
            settings_fields('pluginPage');
            do_settings_sections('pluginPage');
            submit_button();
            ?>

        </form>
        <?php

    }

    /**
     * Get a list of all enabled custom fields
     * @return array
     */
    public function get_enabled()
    {
        return $this->options;
    }

    /**
     * Returns true if the specified $slug is enabled
     * @param $slug
     * @return bool
     */
    public function is_enabled($slug)
    {
        return ($this->options[$slug] == 1);
    }


}

