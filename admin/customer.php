<?php
function getUsersByWPMLevelId($wpmLevelID, $active = true)
{
    global $wpdb;
    $sql = '';
    $dayAfterEndCourse = get_option('ml_day_after_course_end') ? get_option('ml_day_after_course_end') : 0;
    $baseSql = "SELECT *
        FROM `{$wpdb->prefix}memberlux_term_keys`
        WHERE term_id = $wpmLevelID AND user_id != 'NULL'
    ";
    $sql = $baseSql;
    if ($active) {
        $sql .=  " AND date_end >= CURDATE() - INTERVAL $dayAfterEndCourse DAY";
    }
    $memberLuxTermKeys = $wpdb->get_results($sql);
    $userIds = array_map(function ($memberLuxTermKey) {
        return intval($memberLuxTermKey->user_id);
    }, $memberLuxTermKeys);

    foreach ($userIds as $id){
        if($user = get_user_by('ID', $id)){
            $users[] = $user;
        }
    }
    $params = [
        'include' => $userIds,
    ];

    if (isset($_POST['orderby'])){
        $params = array_merge($params, getOrderBy());
    }

    return get_users($params);
}

function getOrderBy(): array
{
    if ($_POST['orderby'] === 'user_login'){
        return [
            'orderby' => $_POST['orderby'],
            'order' => $_POST['order']
        ];
    }
    return [
        'meta_query' => [
            'meta_field' => [
                'key' => $_POST['orderby']
            ]
        ],
        'orderby' => 'meta_field',
        'order' => $_POST['order']
    ];
}

function getCustomerKeys(int $customerId)
{
    global $wpdb;
    $sql = "SELECT * FROM `{$wpdb->prefix}_memberlux_term_keys` WHERE `user_id` = $customerId";
    return $wpdb->get_results($sql);
}

function getCustomerWPMLevels(int $customerId)
{
    global $wpdb;
    $sql = "SELECT `term_id` FROM `{$wpdb->prefix}memberlux_term_keys` WHERE `user_id` = $customerId";
    return array_unique($wpdb->get_col($sql));
}

function  getCustomerAutoCourses(int $customerId)
{
    $courseOnBuy = [];
    foreach (getCustomerWPMLevels($customerId) as $CourseWpmLevelId){
        //TODO DRY
        $courses = get_posts([
            'post_type' => 'product',
            'suppress_filters' => true,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_mbl_key_pin_code_level_id',
                    'value' => $CourseWpmLevelId
                ],
                [
                    'key' => 'how_to_issue',
                    'value' => 'onbuy'
                ]
            ]
        ]);
        if (count($courses)) $courseOnBuy[] = $courses[0];

    }

    return array_map(function ($item){
        return $item->ID;
    }, $courseOnBuy);
}