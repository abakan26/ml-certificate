<?php


class Course
{

    public static function getCourses($category = 0)
    {
        $taxQuery = $category === 0
            ? []
            : [
                'tax_query' => [
                    [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $category
                    ]
                ]
            ];
        $params = array_merge([
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
        ], $taxQuery);
        return get_posts($params);
    }

    public static function getCourseOptions($category = 0)
    {
        return array_map(function ($product) {
            return [
                'product_id' => $product->ID,
                'product_name' => $product->post_title
            ];
        }, self::getCourses($category));
    }

}