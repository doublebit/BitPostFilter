<?php

/**
 * Class BPFUtils
 * Utilities for all the other classes
 */
class BPFUtils
{

    /**
     * Returns the slug of the string provided
     * @param $field_name
     * @return string
     */
    static function slugify($field_name)
    {
        return strtolower(preg_replace("/\W+/", '-', $field_name));
    }

}