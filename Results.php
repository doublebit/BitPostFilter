<?php

/**
 * Class BPFResults
 * Search engine
 */
class BPFResults
{

    /**
     * @var array Searchable fields
     */
    private $fields = array();

    /**
     * @var null|object The $wp_query object
     */
    private $query = null;

    /**
     * @param array $fields
     */
    public function __construct($fields = array())
    {
        $this->fields = $fields;
    }

    /**
     * Performs the search and stores the results in the main loop
     */
    public function search()
    {
        $args = array(
            'suppress_filters' => true
        );
        /**
         * Prepare the meta query
         */
        $meta_query = array();
        foreach ($this->fields as $field => $values) {
            if (isset($_GET[BPFUtils::slugify($field)]) && in_array($_GET[BPFUtils::slugify($field)], $values)) {
                $meta_query[] = array(
                    'key'     => $field,
                    'value'   => $_GET[BPFUtils::slugify($field)],
                    'compare' => '=',
                );
            }
        }
        /**
         * Not a valid search
         */
        if (count($meta_query) == 0) {
            return;
        }
        $args['meta_query'] = $meta_query;
        /**
         * Performs the actual search and stores the results in the main loop
         */
        query_posts($args);
        global $wp_query;
        $this->query = $wp_query;
    }

    /**
     * Returns the $wp_query object
     * @return null|object
     */
    public function get_query()
    {
        return $this->query;
    }

}