<?php


class Course
{
    /**
     * @param int $category
     * @param false $autoIssue
     * @return int[]|WP_Post[]
     */

    public static function getCourses($category = 0, $autoIssue = false): array
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
        $metaQuery = [
            'relation' => 'AND',
            [
                'key' => 'has_certificate',
                'value' => 'yes'
            ]
        ];
        if (!$autoIssue) {
            $metaQuery[] = [
                'key' => 'how_to_issue',
                'value' => 'employee'
            ];
        }
        $params = array_merge([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'suppress_filters' => true,
            'meta_query' => $metaQuery,
            'orderby' => 'title',
            'order' => 'ASC'
        ], $taxQuery);
        return get_posts($params);
    }

    public static function getCourseOptions($category = 0, $autoIssue = false):array
    {
        return array_map(function ($product) {
            return [
                'product_id' => $product->ID,
                'product_name' => $product->post_title
            ];
        }, self::getCourses($category, $autoIssue));
    }

    public static function existCertificateSeries(int $productId, string $series): bool
    {
        if (empty($series)) {
            return false;
        }
        $params = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'suppress_filters' => true,
            'exclude' => $productId,
            'meta_query' => [
                [
                    'key' => 'certificate_series',
                    'value' => $series
                ]
            ]
        ];
        $products = get_posts($params);
        return count($products) > 0;
    }

    /**
     * @param bool $hasSeries
     * @return WP_Post[]
     */

    public static function getProductHasCertificate($hasSeries = true): array
    {
        $params = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'suppress_filters' => true,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'has_certificate',
                    'value' => 'yes'
                ]
            ]
        ];
        if ($hasSeries) {
            $params['meta_query'][] = [
                'key' => 'certificate_series',
                'value' => '',
                'compare' => '!='
            ];
        }
        return get_posts($params);
    }

    public static function getAllSeries(): array
    {
        $series = [];
        $products = self::getProductHasCertificate();
        foreach ($products as $product) {
            $series[] = get_post_meta($product->ID, 'certificate_series', true);
        }
        sort($series);
        return $series;
    }

    /**
     * Возвращает все категории товаров, у товаров которых есть привязка к сертификатам
     * @return array
     */

    public static function getProductCategory(): array
    {
        function filterByCertificateAvailability($term): bool {
            return count(Course::getCourses($term->term_id, true));
        }
        function getArray($carry , $term)
        {
            $carry[] = [
                'id' => $term->term_id,
                'name' => $term->name
            ];
            return $carry;
        }
        $allProductCategories = get_terms([
            'hide_empty' => false,
            'taxonomy' => 'product_cat'
        ]);
        $productCategories = array_filter($allProductCategories, 'filterByCertificateAvailability');
        return array_reduce($productCategories, 'getArray', []);
    }

    public static function isCourse($catId): bool
    {
        // TODO online course slug
        $courseSlug = 'onlajn-kursy';
        $term = get_term($catId, 'product_cat');
        return $term->slug === $courseSlug;
    }

    public static function getWPMLevel($id)
    {
        return get_post_meta($id, '_mbl_key_pin_code_level_id', true);
    }
}
