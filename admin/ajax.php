<?php

function getCategoryByWPMLevel($wpmLevelId)
{
    $lesson = get_posts([
        'post_type' => 'wpm-page',
        'numberposts' => 1,
        'suppress_filters' => true,
        'tax_query' => [
            [
                'taxonomy' => 'wpm-levels',
                'field' => 'id',
                'terms' => $wpmLevelId
            ]
        ]
    ]);
    return wp_get_object_terms($lesson[0]->ID, 'wpm-category')[0];
}

function customerHasCertificate($userId, $productId)
{
    global $wpdb;
    $sql = "SELECT certificate_id FROM `{$wpdb->prefix}memberlux_certificate` WHERE `product_id` = {$productId} AND user_id = {$userId}";
    return !empty($wpdb->get_var($sql));

}

add_action('wp_ajax_ml_select_user', function () {
    $productId = intval($_POST['product_id']);
    $levelId = (int)get_post_meta($productId, '_mbl_key_pin_code_level_id', true);
    $users = getUsersByWPMLevelId($levelId);

    ob_start();
    foreach ($users as $userObj):
        if(customerHasCertificate($userObj->ID, $productId)) continue;
        $user = $userObj->data;
        ?>
        <tr id="user-<?= $user->ID ?>">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="user_1">Выбрать <?= $user->user_login; ?></label>
                <input type="checkbox" name="users[]" id="user_<?= $user->ID ?>" value="<?= $user->ID ?>">
            </th>
            <td class="username column-username has-row-actions column-primary">
                <a href='<?= get_edit_user_link($user->ID); ?>'><?= $user->user_login; ?></a>
            </td>
            <td class="name column-name">
                <strong>
                    <?= getFIO(intval($user->ID)); ?>
                </strong>
            </td>
        </tr>
    <?php
    endforeach;
    echo json_encode(['html' => ob_get_clean()]);
    wp_die();
});


add_action('wp_ajax_ml_new_certificate_template', function () {
    CertificateTemplate::saveTemplates('create');
});

add_action('wp_ajax_ml_update_certificate_template', function () {
    CertificateTemplate::saveTemplates('save');
});

add_action('wp_ajax_ml_certificate_delivery', function () {
    if (!isset($_POST['users'])) {
        die(json_encode(['error' => 'Пожалуйста выберите хотя бы одного пользователя!']));
    }
    $dateIssue = $_POST['date_issue'];
    $productId = intval($_POST['product_id']);
    $users = $_POST['users'];

    foreach ($users as $userId) {
        Certificate::create(
            intval($userId),
            (int)get_post_meta($productId, 'template_id', true),
            $productId,
            get_user_meta($userId, 'first_name', true),
            get_user_meta($userId, 'last_name', true),
            get_user_meta($userId, 'surname', true),
            $dateIssue,
            get_post_meta($productId, 'certificate_series', true),
            get_current_user_id(),
            date('m.d.Y'),
            get_post_meta($productId, 'course_name', true)
        );
    }

    die(json_encode(['success' => true]));
});

?>
