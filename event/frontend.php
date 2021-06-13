<?php
require 'events-shortcode.php';
add_shortcode( 'events_page', 'events_shortcode_callback' );

add_filter( 'template_include', 'events_page_template', 99 );
function events_page_template( $template ) {
    if ( is_page( 'events' )  ) {
        return $template = __DIR__ . '/page.php';
    }
    return $template;
}

add_action('wp_ajax_nopriv_ml_get_events', function () {
    echo events_shortcode_callback([]);
    die();
});
