<?php
/**
 * @global $perPage
 * @global $pageNum
 * @global $certificates
 * @global $totalPages
 * @global $total
 */
?>

<div class="container-fluid">
    <form id="filterForm">
        <div class="row">
            <div class="col-lg-12 col-xl-9">
                <div class="card p-0" style="max-width: unset;">
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
                                            <?php
                                            $selected = '';
                                            if (isset($filters['certificate_template_id']) &&
                                                $filters['certificate_template_id'] === $template->id) {
                                                $selected = ' selected';
                                            } ?>
                                            <option value="<?= $template->id ?>"<?= $selected ?>><?= $template->name ?></option>
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
                                    <select class="form-control" id="responsiblePersonId"
                                            name="filter[responsible_person]">
                                        <option value="">Выберите</option>
                                        <?php foreach (ResponsiblePerson::getResponsiblePersons() as $person): ?>
                                            <option value="<?= $person->ID ?>"><?= $person->data->user_login ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col form-group" style="padding-top: 24px;">
                                <div style="margin-top: .5rem; text-align: right">
                                    <button class="btn btn-secondary mr-3" type="button" data-action="reset_filters">
                                        Сбросить
                                    </button>
                                    <button class="btn btn-primary" type="button" data-action="apply_filters">
                                        Применить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-3">
                <div class="card p-0" style="max-width: unset;">
                    <div class="card-header">
                        <h6 class="m-0">
                            Поиск по email
                            <a data-toggle="collapse" href="#collapse-search" aria-expanded="true"
                               aria-controls="collapse-search"
                               class="d-block float-right" id="heading-search">
                                <i class="fa fa-chevron-down pull-right"></i>
                            </a>
                        </h6>
                    </div>
                    <div class="card-body p-3 collapse show" id="collapse-search" aria-labelledby="heading-search">
                        <div class="form-group">
                            <label for="userEmail">Введите email пользователя</label>
                            <input class="form-control" type="text" id="userEmail" name="search_by_email">
                            <div class="text-right mt-2">
                                <button class="btn btn-primary" type="button" data-action="search_by_email">Поиск</button>
                            </div>
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
                                <table class="table table-hover table-bordered dataTable" id="dataTable" width="100%"
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
                                        <th rowspan="1" colspan="1">Название</th>
                                        <th rowspan="1" colspan="1">Дата выдачи</th>
                                        <th rowspan="1" colspan="1">Кем выдан</th>
                                        <th rowspan="1" colspan="1">Шаблон</th>
                                        <th rowspan="1" colspan="1">Просмотр</th>
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
                                        <th rowspan="1" colspan="1">Название</th>
                                        <th rowspan="1" colspan="1">Дата выдачи</th>
                                        <th rowspan="1" colspan="1">Кем выдан</th>
                                        <th rowspan="1" colspan="1">Шаблон</th>
                                        <th rowspan="1" colspan="1">Просмотр</th>
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

    input[type=date].form-control {
        height: calc(1.5em + .75rem + 0px) !important;
    }
</style>
