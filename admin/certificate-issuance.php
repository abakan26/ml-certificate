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
    $coach = new Coach($user->ID);
    $courseOptions = $coach->getRelatedCourses();
}

include 'templates/certificate-issuance.php';