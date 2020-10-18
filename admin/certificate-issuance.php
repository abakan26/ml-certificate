<?php
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

include 'templates/certificate-issuance.php';