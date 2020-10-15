<?php
function getUsersByWPMLevelId($wpmLevelID)
{
    global $wpdb;
    $users = [];
    $memberLuxTermKeys = $wpdb->get_results("
        SELECT *
        FROM `{$wpdb->prefix}memberlux_term_keys`
        WHERE term_id = $wpmLevelID AND user_id != 'NULL' AND date_end >= CURDATE()");
    $userIds = array_map(function ($memberLuxTermKey) {
        return intval($memberLuxTermKey->user_id);
    }, $memberLuxTermKeys);

    foreach ($userIds as $id){
        if($user = get_user_by('ID', $id)){
            $users[] = $user;
        }
    }
    return $users;
}