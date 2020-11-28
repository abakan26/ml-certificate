<?php
require 'templates/parts/member-certificate-row.php';
$pageNum = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$filters = isset($_POST['filter']) ? $_POST['filter'] : [];
$perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$query = Certificate::query([
    'page' => $pageNum,
    'per_page' => $perPage,
//    'filter' => [
//        'certificate_template_id' => 11,
//        'responsible_person' => 1
//    ]
]);
$total = $query['total'];
$totalPages = $query['total_pages'];
$certificates = $query['result'];

?>
<style>
    .table-loading {
        position: relative;
    }

    .table-loading:after {
        content: "";
        position: absolute;
        z-index: 5;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: #000;
        opacity: 0.3;
    }

    .table-loading-icon {
        display: none;
    }

    .table-loading .table-loading-icon {
        display: inline;
        font-size: 50px;
        color: #fff;
        position: absolute;
        z-index: 10;
        width: 50px;
        height: 50px;
        top: calc(50% - 25px);
        left: calc(50% - 25px);
    }
</style>
<div class="container-fluid">
    <form id="filterForm">
        <div class="card p-0" style="max-width: 1200px">
            <div class="card-header">
                <h6 class="m-0">
                    Фильтры
                    <a data-toggle="collapse" href="#collapse-filter" aria-expanded="true"
                       aria-controls="collapse-filter"
                       class="d-block float-right" id="heading-filter">
                        <i class="fa fa-chevron-down pull-right"></i>
                    </a>
                </h6>
            </div>
            <div class="card-body p-3 collapse show" id="collapse-filter" aria-labelledby="heading-filter">
                <div class="form-row">
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label for="certificateTemplateId">Шаблон сертификата</label>
                            <select class="form-control" id="certificateTemplateId"
                                    name="filter[certificate_template_id]">
                                <option value="">Не выбран</option>
                                <?php foreach (CertificateTemplate::getCertificateTemplates() as $template): ?>
                                    <option value="<?= $template->id ?>"><?= $template->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label for="productId">Товар</label>
                            <select class="form-control" id="productId" name="filter[product_id]">
                                <option value="">Не выбран</option>
                                <?php foreach (Course::getCourseOptions() as $course): ?>
                                    <option value="<?= $course['product_id']; ?>">
                                        <?= $course['product_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <label>Дата создания</label>
                        <div class="form-row">
                            <div class="col-xl-6 form-group">
                                <input type="date" class="form-control" name="filter[create_date][from]">
                            </div>
                            <div class="col-xl-6 form-group">
                                <input type="date" class="form-control" name="filter[create_date][to]">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <label>Дата выдачи</label>
                        <div class="form-row">
                            <div class="col-xl-6 form-group">
                                <input type="date" class="form-control" name="filter[date_issue][from]">
                            </div>
                            <div class="col-xl-6 form-group">
                                <input type="date" class="form-control" name="filter[date_issue][to]">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="form-group">
                            <label for="">Кем выдан</label>
                            <select class="form-control" id="responsiblePersonId" name="filter[responsible_person]">
                                <option value="">Выберите</option>
                                <?php foreach (ResponsiblePerson::getResponsiblePersons() as $person): ?>
                                    <option value="<?= $person->ID ?>"><?= $person->data->user_login ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col form-group" style="padding-top: 24px;">
                        <div style="margin-top: .5rem; text-align: right">
                            <button class="btn btn-secondary mr-3" type="button" data-action="reset_filters">Сбросить
                            </button>
                            <button class="btn btn-primary" type="button" data-action="apply_filters">Применить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4 p-0" style="max-width: 100%">
            <span class="table-loading-icon fas fa-spin fa-spinner"></span>
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Выданные сертификаты
                    <div class="d-inline-flex ml-5">
                        <div>
                            <label for="" class="text-secondary" style="font-size: 14px;">Удалить выбранные</label>
                            <button class="btn btn-danger" data-action="delete" type="button"><span
                                        class="fa fa-trash"></span></button>
                        </div>
                        <div class="ml-3">
                            <label for="" class="text-success" style="font-size: 14px;">Сменить шаблон</label>
                            <select name="new_template" id="set_new_template" style="height: 38px;">
                                <option value="">Не выбран</option>
                                <?php foreach (CertificateTemplate::getCertificateTemplates() as $template): ?>
                                    <option value="<?= $template->id ?>"><?= $template->name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-pimary" data-action="set_new_template" type="button"><span
                                        class="fa fa-save"></span></button>
                        </div>
                    </div>

                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Показывать по <select
                                                name="per_page" aria-controls="dataTable"
                                                class="custom-select custom-select-sm form-control form-control-sm">
                                            <option<?= $perPage === 10 ? ' selected' : ''; ?> value="10">10</option>
                                            <option<?= $perPage === 25 ? ' selected' : ''; ?> value="25">25</option>
                                            <option<?= $perPage === 50 ? ' selected' : ''; ?> value="50">50</option>
                                            <option<?= $perPage === 100 ? ' selected' : ''; ?> value="100">100</option>
                                        </select> на странице</label>
                                    <input type="hidden" name="page_num" value="<?= $pageNum ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered dataTable" id="dataTable" width="100%"
                                       cellspacing="0" role="grid" aria-describedby="dataTable_info"
                                       style="width: 100%;">
                                    <thead>
                                    <tr role="row">
                                        <th class="check-column">
                                            <input type="checkbox" class="form-check">
                                        </th>
                                        <th class="sorted asc" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="user_login">
                                            Имя пользователя
                                        </th>
                                        <th class="sortable asc" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="graduate_last_name">
                                            Фамилия
                                        </th>
                                        <th class="sortable asc" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="graduate_first_name">
                                            Имя
                                        </th>
                                        <th class="sortable asc" rowspan="1" colspan="1"
                                            data-action="sortable" data-order-by="graduate_surname">
                                            Отчество
                                        </th>
                                        <th rowspan="1" colspan="1">Название сертификата</th>
                                        <th rowspan="1" colspan="1">Дата выдачи</th>
                                        <th rowspan="1" colspan="1">Кем выдан</th>
                                        <th rowspan="1" colspan="1">Шаблон сертификата</th>
                                        <th rowspan="1" colspan="1">Дата создания</th>
                                        <input type="hidden" name="orderby" value="user_login">
                                        <input type="hidden" name="order" value="asc">
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php renderTbody($certificates) ?>
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th class="check-column">
                                            <input type="checkbox" class="form-check">
                                        </th>
                                        <th rowspan="1" colspan="1">Имя пользователя</th>
                                        <th rowspan="1" colspan="1">Фамилия</th>
                                        <th rowspan="1" colspan="1">Имя</th>
                                        <th rowspan="1" colspan="1">Отчество</th>
                                        <th rowspan="1" colspan="1">Название сертификата</th>
                                        <th rowspan="1" colspan="1">Дата выдачи</th>
                                        <th rowspan="1" colspan="1">Кем выдан</th>
                                        <th rowspan="1" colspan="1">Шаблон сертификата</th>
                                        <th rowspan="1" colspan="1">Дата создания</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                                    <?= renderResultCount($pageNum, $perPage, $totalPages, $total); ?>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                                    <?php
                                    echo getPagination([
                                        'link' => "?page_num=",
                                        'page' => $pageNum,
                                        'total' => $totalPages
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .sortable, .sorted {
        cursor: pointer;
        font-size: 14px;
    }

    .sortable:after, .sorted:after {
        font-family: "Font Awesome 5 Free";
        float: right;
    }

    .sortable:after {
        display: none;
    }

    .sorted:after {
        display: inline-block;
    }

    .sortable:hover:after {
        display: inline-block;
    }

    .sortable.asc:hover:after {
        content: "\f0d8";
    }

    .sortable.desc:hover:after {
        content: "\f0d7";
    }

    .sorted.asc:after {
        content: "\f0d8";
    }

    .sorted.desc:after {
        content: "\f0d7";
    }
    .sorted.asc:hover:after {
        content: "\f0d7";
    }

    .sorted.desc:hover:after {
        content: "\f0d8";
    }


    table.dataTable {
        clear: both;
        margin-top: 6px !important;
        margin-bottom: 6px !important;
        max-width: none !important;
        border-spacing: 0;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0.85em;
        white-space: nowrap;
    }

    div.dataTables_wrapper div.dataTables_paginate {
        margin: 0;
        white-space: nowrap;
        text-align: right;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin: 2px 0;
        white-space: nowrap;
        justify-content: flex-end;
    }

    .card-body {
        flex: 1 1 auto;
        min-height: 1px;
        padding: 1.25rem;
    }

    div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:last-child {
        padding-right: 0;
    }

    div.table-responsive > div.dataTables_wrapper > div.row > div[class^="col-"]:first-child {
        padding-left: 0;
    }

    div.table-responsive > div.dataTables_wrapper > div.row {
        margin: 0;
    }

    div.dataTables_wrapper div.dataTables_length label {
        font-weight: normal;
        text-align: left;
        white-space: nowrap;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: auto;
        display: inline-block;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0.5em;
        display: inline-block;
        width: auto;
    }

    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
    }

    .dataTable.table td, .dataTable.table th {
        padding: 0.45rem;
    }

    input[type=date].form-control {
        height: calc(1.5em + .75rem + 0px) !important;
    }
</style>