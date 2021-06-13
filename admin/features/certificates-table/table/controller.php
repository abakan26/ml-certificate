<?php
require_once 'table-row.php';
$pageNum = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$filters = [];
if (isset($_GET['certificate_template_id']) && !empty($_GET['certificate_template_id'])) {
    $filters['certificate_template_id'] = intval($_GET['certificate_template_id']);
}
$perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$query = Certificate::query([
    'page' => $pageNum,
    'per_page' => $perPage,
    'filter' => $filters
]);
$total = $query['total'];
$totalPages = $query['total_pages'];
$certificates = $query['result'];
include 'view.php';
