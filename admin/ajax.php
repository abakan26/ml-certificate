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
    $user = get_user_by('ID', get_current_user_id());
    $isCoach = in_array('coach', $user ->roles);
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
                <?php if( $isCoach ): ?>
                    <?= $user->user_login; ?>
                 <?php else: ?>
                    <a href='<?= get_edit_user_link($userId); ?>'>
                        <?= $user->user_login; ?>
                    </a>
                <?php endif ?>
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
    ob_start();
    ?>
    <option value="">Выбрать товар</option>
    <?php foreach (Course::getCourseOptions(intval($_POST['category_id'])) as $course): ?>
        <option value="<?= $course['product_id']; ?>">
            <?= $course['product_name']; ?>
        </option>
    <?php endforeach; ?>
    <?php
    die(json_encode(['html' => ob_get_clean()]));
});

add_action('wp_ajax_ml_certificate_filtered', function () {
    include PLUGIN_ADMIN_PATH . '/templates/parts/member-certificate-row.php';
    $data = json_decode(stripslashes($_POST['data']));
    $params = [
        'page_num' => $data->page_num,
        'per_page' => $data->per_page,
        'order_by' => $data->orderby,
        'order' => $data->order,
        'email' => $data->search_by_email,
        'filter' => isset($data->filter) ? $data->filter : null
    ];
    $query = Certificate::query($params);
    $pageNum = $query['page_num'];
    $perPage = $query['per_page'];
    $totalPages = $query['total_pages'];
    $total = $query['total'];
    die(json_encode([
        'html' => [
            'tbody' => empty($query['result']) ? '<tr><td colspan="10">Не надено ни одного результата</td></tr>' : renderTbody($query['result'], true),
            'result_count' => renderResultCount($pageNum, $perPage, $totalPages, $total),
            'pagination' => getPagination([
                'link' => "?page_num=",
                'page' => $pageNum,
                'total' => $totalPages
            ]),
            'data' => ['page' => $pageNum, 'per_page' => $perPage, 'total_pages' => $totalPages, 'total' => $total],
            'res' => $query['result']
        ],
        'sql' => $query['sql']
    ]));
});

add_action('wp_ajax_ml_delete_certificate', function () {
    $data = json_decode(stripslashes($_POST['data']), true);
    foreach ($data as $certificate_id) {
        Certificate::delete(intval($certificate_id));
    }
    die(json_encode([
        'data' => $data,
        'status' => 'success'
    ]));
});

add_action('wp_ajax_ml_update_certificates_template_id', function () {
    $data = json_decode(stripslashes($_POST['data']), true);
    $certificate_template_id = intval($data['template_id']);
    foreach ($data['certificates'] as $certificate_id) {
        Certificate::update(intval($certificate_id), [
                'certificate_template_id' => $certificate_template_id
        ]);
    }
    die(json_encode([
        'status' => 'success'
    ]));
});

add_action('wp_ajax_ml_delete_certificate_template', function () {
    $certificate_template_id = intval($_POST['id']);
    if (!CertificateTemplate::isAccessToDelete($certificate_template_id)) {
        die(json_encode([
            'status' => 'error',
            'message' => 'Нет разрешения на удаление'
        ]));
    }
    CertificateTemplate::delete($certificate_template_id);
    die(json_encode([
        'status' => 'success',
        'id' => $certificate_template_id
    ]));
});
?>
