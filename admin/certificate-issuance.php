<?php
$user = get_user_by('ID', get_current_user_id());
$isCoach = in_array('coach', $user ->roles);
$productCategories = get_terms([
    'hide_empty' => false,
    'taxonomy' => 'product_cat'
]);
$categoryOptions = array_map(function ($term){
    return [
        'id' => $term->term_id,
        'name' => $term->name
    ];
}, $productCategories);
if ($isCoach) {
    $categories = get_user_meta( $user->ID, '_mbla_coach_access', true);
    $products = [];
    foreach (array_keys($categories) as $cat) {
        $post = get_posts([
            'post_type' => 'wpm-page',
            'suppress_filters' => true,
            'tax_query' => [
                [
                    'taxonomy' => 'wpm-category',
                    'field' => 'term_id',
                    'terms' => $cat
                ]
            ]
        ])[1];
        $wpmLevel = get_terms([
            'object_ids' => $post->ID,
            'taxonomy' => 'wpm-levels',
            'fields' => 'ids',
            'suppress_filter' => true
        ])[0];
        $product = get_posts([
            'post_type' => 'product',
            'suppress_filters' => true,
            'meta_query' => [
                [
                    'key' => '_mbl_key_pin_code_level_id',
                    'value' => $wpmLevel
                ]
            ]
        ]);
        echo '<pre>';
        var_dump($wpmLevel);
        echo '</pre>';
    }


}

include 'templates/certificate-issuance.php';