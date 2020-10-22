<?php
function getUsersByWPMLevelId($wpmLevelID)
{
    global $wpdb;


    $memberLuxTermKeys = $wpdb->get_results("
        SELECT *
        FROM `{$wpdb->prefix}memberlux_term_keys`
        WHERE term_id = $wpmLevelID AND user_id != 'NULL' AND date_end >= CURDATE()
    ");
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
//    $params['meta_query'] = [
//        'fio' => [
//            'key' => $_POST['orderby']
//        ]
//    ];
//    $params['orderby'] = 'fio';
//    $params['order'] = $_POST['order'];
}