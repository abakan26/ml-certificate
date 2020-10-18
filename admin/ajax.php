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
        if (customerHasCertificate($userObj->ID, $productId)) continue;
        $user = $userObj->data;
        $userId = intval($user->ID);
        ?>
        <tr id="user-<?= $userId ?>">
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="user_1">Выбрать <?= $user->user_login; ?></label>
                <input type="checkbox" name="users[]" id="user_<?= $userId ?>" value="<?= $userId ?>">
            </th>
            <td class="username column-username has-row-actions column-primary">
                <a href='<?= get_edit_user_link($userId); ?>'><?= $user->user_login; ?></a>
            </td>
            <td class="name column-name">
                <strong>
                    <?= get_user_meta($userId, 'last_name', true); ?>
                </strong>
            </td>
            <td class="name column-name">
                <strong>
                    <?= get_user_meta($userId, 'first_name', true); ?>
                </strong>
            </td>
            <td class="name column-name">
                <strong>
                    <?= get_user_meta($userId, 'surname', true); ?>
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
    $product = get_post($productId);
    foreach ($users as $userId) {
        Certificate::create(
            intval($userId),
            $product->post_excerpt,
            (int)get_post_meta($productId, 'template_id', true),
            $productId,
            get_user_meta($userId, 'first_name', true),
            get_user_meta($userId, 'last_name', true),
            get_user_meta($userId, 'surname', true),
            $dateIssue,
            get_post_meta($productId, 'certificate_series', true),
            get_current_user_id(),
            date('Y-m-d'),
            get_post_meta($productId, 'course_name', true)
        );
    }

    die(json_encode(['success' => true]));
});

add_action('wp_ajax_ml_get_products_by_category', function () {
    $productCourses = get_posts([
        'post_type' => 'product',
        'posts_per_page' => -1,
        'suppress_filters' => true,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => intval($_POST['category_id'])
            ]
        ],
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

    $courseOptions = array_map(function ($product) {
        return [
            'product_id' => $product->ID,
            'product_name' => $product->post_title
        ];
    }, $productCourses);
    ob_start();
    ?>
    <option value="">Выбрать товар</option>
    <?php foreach ($courseOptions as $course): ?>
        <option value="<?= $course['product_id']; ?>">
            <?= $course['product_name']; ?>
        </option>
    <?php endforeach; ?>
    <?php
    die(json_encode(['html' => ob_get_clean()]));
});
?>
