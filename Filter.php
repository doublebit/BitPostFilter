<?php

class BPFFilter
{

    protected $fields = array();

    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    public function render()
    {
        $o = new BPFOptions($this->fields);
        echo '<form class="bpf_form" method="get" action="' . site_url() . '">';
        foreach ($this->fields as $field => $values) {
            if (!$o->is_enabled(BPFUtils::slugify($field))) {
                continue;
            }
            echo '<select name="' . BPFUtils::slugify($field) . '">';
            echo '<option value="">' . $field . '</option>';
            foreach ($values as $val) {
                echo '<option value="' . $val . '" ' .
                    (( isset($_GET[BPFUtils::slugify($field)]) && $_GET[BPFUtils::slugify($field)] == $val ) ? 'selected' : '') .
                    '>' . $val . '</option>';
            }
            echo '</select>';
        }
        echo '<button type="submit">' . __('Search', 'wordpress') . '</button>';
        echo '</form>';
    }

}