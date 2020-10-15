<?php
$productCourses = get_posts( [
    'post_type' => 'product',
    'posts_per_page' => -1,
    'suppress_filters' => true,
    'meta_query' => [
        'relation' => 'AND',
        [
            'key' => 'has_certificate',
            'value' => 'yes'
        ],
        [
            'key' => 'how_to_issue',
            'value' => 'employee'
        ]
    ]
]);

/* Убираем Бесплатные материалы из списка*/
//unset($levels_id[array_search(158, $levels_id)]);

$result['course_options'] = array_map(function ($product){
    return [
        'product_id' => $product->ID,
        'product_name' => $product->post_title
    ];
}, $productCourses);

include 'templates/certificate-issuance.php';