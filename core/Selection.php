<?php


class Selection
{
    public function __construct()
    {
    }

    public function init()
    {
        add_action('wp_ajax_ml_test', [$this, 'get_wpmLevels']);
    }

    public function get_wpmLevels(){
        $terms = get_terms([
            'taxonomy' => 'wpm-levels'
        ]);
        echo json_encode(array_values($terms));
        die();
    }
}
