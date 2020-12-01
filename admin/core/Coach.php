<?php


class Coach
{
    public $id = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getRelatedCourses(): array
    {
        return array_filter(
            array_map([$this, 'getCourseByWmpLevel'], $this->getWpmLevels()),
            'is_object'
        );
    }

    /**
     * @return array
     */

    public function getWpmLevels()
    {
        $accessWpmCategoryIds = array_keys(get_user_meta( $this->id, '_mbla_coach_access', true));
        return array_map([$this, 'getWPMLevelByWPMCategoryId'], $accessWpmCategoryIds);
    }

    public function getWPMLevelByWPMCategoryId($wpmCategoryId)
    {
        $CourseFirstMaterial = get_posts([
            'post_type' => 'wpm-page',
            'suppress_filters' => true,
            'tax_query' => [
                [
                    'taxonomy' => 'wpm-category',
                    'field' => 'term_id',
                    'terms' => $wpmCategoryId
                ]
            ]
        ])[1];
        return get_terms([
            'object_ids' => $CourseFirstMaterial->ID,
            'taxonomy' => 'wpm-levels',
            'fields' => 'ids',
            'suppress_filter' => true
        ])[0];
    }

    /**
     * Finds a WP_Post type product by meta field _mbl_key_pin_code_level_id
     * @param int $CourseWpmLevel
     * @return WP_Post
     */

    //TODO DRY
    public function getCourseByWmpLevel(int $CourseWpmLevel)
    {
         $courses = get_posts([
            'post_type' => 'product',
            'suppress_filters' => true,
            'meta_query' => [
                [
                    'key' => '_mbl_key_pin_code_level_id',
                    'value' => $CourseWpmLevel
                ]
            ]
        ]);
        return count($courses) ? $courses[0] : null;
    }
}