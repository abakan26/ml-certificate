<?php
/**
 * @global array $categoryOptions
 * @global array $courseOptions
 * @global bool $isCoach
 */
?>
<div class="container-fluid">
    <h1 class="mt-4">Выдача сертификатов</h1>
    <p class="text">
        Выберите в выпадающем списке онлайн-курс. Нажмите кнопку "выбрать" и в таблице появиться список обучающихся.
    </p>
    <div class="tablenav top">
        <form id="usersByWmpLevel">
        <?php if($isCoach): ?>
            <select class="form-control d-inline-block" required name="product_id" id="product_id">
                <option value="" selected="selected">Выбрать курс</option>
                <?php foreach ($courseOptions as $course): ?>
                    <option value="<?= $course->ID; ?>">
                        <?= $course->post_title; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
                <select class="form-control d-inline-block" name="category_id" id="category_id">
                    <option value="" selected="selected">Выбрать категорию товара</option>
                    <?php foreach ($categoryOptions as $category): ?>
                        <option value="<?= $category['id']; ?>">
                            <?= $category['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="form-control d-inline-block" required name="product_id" id="product_id"></select>
        <?php endif ?>
            <input type="hidden" name="action" value="ml_select_user">
            <input type="hidden" name="orderby" value="user_login">
            <input type="hidden" name="order" value="asc">
            <input type="submit" id="ml_select_user" class="btn btn-success" value="Выбрать">
        </form>
    </div>
    <div id="alertError" class="alert alert-danger mt-4" role="alert" style="display: none">
    </div>
    <div id="alertSuccess" class="alert alert-success mt-4" role="alert" style="display: none">
        Сертификат успешно присвоен
    </div>
    <form id="usersSetCertificate" class="needs-validation mt-2" novalidate>

        <div class="card shadow mb-4 p-0" style="max-width: 100%">
            <span class="table-loading-icon fas fa-spin fa-spinner"></span>
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <div class="h-auto ">
                        <label for="date">Дата присвоения сертификата</label>
                        <input id="date" type="date" name="date_issue" required class="form-control"
                               style="display: inline-block;width: 200px;">
                        <input type="hidden" id="product_id_users" name="product_id" value="">
                        <input type="hidden" name="action" value="ml_certificate_delivery">
                        <input type="submit" value="Присвоить сертификат" class="btn btn-primary">
                    </div>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">

                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-hover table-bordered dataTable" id="dataTable" width="100%"
                                       cellspacing="0" role="grid" aria-describedby="dataTable_info"
                                       style="width: 100%;">
                                    <thead>
                                    <tr role="row">
                                        <th id="cb" class="manage-column column-cb check-column">
                                            <input id="cb-select-all-1" type="checkbox">
                                        </th>
                                        <th scope="col" id="user_login" data-orderby="user_login"
                                            class="manage-column column-username column-primary sorted asc">
                                            Имя пользователя
                                        </th>
                                        <th scope="col" id="last_name" class="manage-column column-primary sortable desc"
                                            data-orderby="last_name">
                                            Фамилия
                                        </th>
                                        <th scope="col" id="first_name"
                                            class="manage-column column-primary sortable desc" data-orderby="first_name">
                                            Имя
                                        </th>
                                        <th scope="col" id="surname" class="manage-column column-primary sortable desc"
                                            data-orderby="surname">
                                            Отчество
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody id="the-list">

                                    </tbody>
                                </table>
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
<script>
    (function ($) {
        let form = document.getElementById('usersByWmpLevel');
        let certificateForm = document.getElementById('usersSetCertificate');
        let table = document.getElementById('the-list');
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            getUserByWmpLevel(form, table);
        })

        certificateForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (certificateForm.checkValidity() === false) {
                event.stopPropagation();
            }
            $(certificateForm).addClass("was-validated");
            if (certificateForm.checkValidity() === true) {
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    processData: false,
                    data: $(certificateForm).serialize(),
                    success: function (response) {
                        let data = JSON.parse(response);
                        if (data.error) {
                            $('#alertError').text(data.error);
                            $('#alertError').show();
                        } else if (data.success) {
                            table.innerHTML = "";
                            $('#alertSuccess').show();
                        }

                    }
                })
            }
        })

        $(certificateForm).on('change', function (event) {
            $('#alertError').hide();
            $('#alertSuccess').hide();
        })
        $('[data-orderby]').on('click', doOrder);

        $('#category_id').on('change', function (event) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    'action': 'ml_get_products_by_category',
                    'category_id': $('#category_id option:selected').val()
                },
                success: function (response) {
                    $('#product_id').html(JSON.parse(response).html);
                }
            })
        })

        function doOrder(event) {
            resetOrder();
            let orderby = $(this).attr('data-orderby');
            let oldOrder = $(this).hasClass('asc') ? 'asc' : 'desc';
            let newOrder = $(this).hasClass('asc') ? 'desc' : 'asc';
            $(this).removeClass(`${oldOrder} sortable`);
            $(this).addClass(`${newOrder} sorted`);
            $('[name="orderby"]').val(orderby);
            $('[name="order"]').val(newOrder);
            getUserByWmpLevel(form, table);
        }

        function resetOrder() {
            jQuery('[data-orderby]').addClass('sortable');
            jQuery('[data-orderby]').removeClass('sorted');
        }

        function getUserByWmpLevel(form, table) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                processData: false,
                data: $(form).serialize(),
                success: function (response) {
                    let productId = jQuery('#product_id option:selected').val();
                    $('#product_id_users').val(productId);
                    table.innerHTML = JSON.parse(response).html;
                }
            })
        }
    })(jQuery);

</script>