<?php
/**
 * @global array $result
 */
?>
<?php


?>
<style>
    th.sortable > span,  th.sorted > span{
        display: block;
        overflow: hidden;
        padding: 8px;
    }
    th.sortable .sorting-indicator, th.sorted .sorting-indicator{
        float: left;
        cursor: pointer;
    }
    th.sorted .sorting-indicator {
        visibility: visible;
    }
</style>
<h1>Выдача сертификатов</h1>
<p class="text">
    Выберите в выпадающем списке онлайн-курс. Нажмите кнопку "выбрать" и в таблице появиться список обучающихся.
</p>
<div class="tablenav top">
    <form id="usersByWmpLevel">
        <select required name="product_id" id="product_id" style="display:inline-block; float:none;">
            <option value="">Выбрать онлайн-курс</option>
            <?php foreach ($result['course_options'] as $course): ?>
                <option value="<?= $course['product_id']; ?>">
                    <?= $course['product_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="action" value="ml_select_user">
        <input type="hidden" name="orderby" value="last_name">
        <input type="hidden" name="order" value="asc">
        <input type="submit" id="ml_select_user" class="button" value="Выбрать">
    </form>
</div>
<div id="alertError" class="alert alert-danger mt-2" role="alert" style="display: none">
</div>
<div id="alertSuccess" class="alert alert-success mt-2" role="alert" style="display: none">
    Сертификат успешно присвоен
</div>
<form id="usersSetCertificate" class="needs-validation mt-2" novalidate>
    <div class="tablenav top" style="height: auto">
        <label for="date">Дата присвоения сертификата</label>
        <input id="date" type="date" name="date_issue" required class="form-control" style="display: inline-block;
    width: 200px;">
        <input type="hidden" id="product_id_users" name="product_id" value="">
        <input type="hidden" name="action" value="ml_certificate_delivery">
        <input type="submit" value="Присвоить сертификат" class="button">
    </div>
    <table class="wp-list-table widefat fixed striped table-view-list users">
        <thead>
        <tr>
            <td id="cb" class="manage-column column-cb check-column">
                <label class="screen-reader-text" for="cb-select-all-1">
                    Выделить все
                </label>
                <input id="cb-select-all-1" type="checkbox">
            </td>
            <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                <span>Имя пользователя</span>
            </th>
            <th scope="col" id="last_name" class="manage-column column-primary sortable asc">
                <span data-orderby="last_name">
                    <span>Фамилия</span><span class="sorting-indicator"></span>
                </span>
            </th>
            <th scope="col" id="first_name" class="manage-column column-primary sortable asc">
                <span data-orderby="first_name">
                    <span>Имя</span><span class="sorting-indicator"></span>
                </span>
            </th>
            <th scope="col" id="surname" class="manage-column column-primary sortable asc">
                <span data-orderby="surname">
                    <span>Отчество</span><span class="sorting-indicator"></span>
                </span>
            </th>
        </tr>
        </thead>
        <tbody id="the-list">

        </tbody>
<!--        <tfoot>-->
<!--        <tr>-->
<!--            <td class="manage-column column-cb check-column">-->
<!--                <label class="screen-reader-text" for="cb-select-all-2">Выделить-->
<!--                    все</label><input id="cb-select-all-2" type="checkbox">-->
<!--            </td>-->
<!--            <th scope="col" class="manage-column column-username column-primary sortable desc">-->
<!--                <span>Имя пользователя</span>-->
<!--            </th>-->
<!--            <th scope="col" class="manage-column column-name">Имя</th>-->
<!--        </tr>-->
<!--        </tfoot>-->

    </table>
</form>
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

        $('[data-orderby]').on('click', doOrder)

        function doOrder(event) {
            resetOrder();
            let parent = $(this).parent();
            let orderby = $(this).attr('data-orderby');
            let oldOrder = parent.hasClass('asc') ? 'asc' : 'desc';
            let newOrder = parent.hasClass('asc') ? 'desc' : 'asc';
            $(parent).removeClass(`${oldOrder} sortable`);
            $(parent).addClass(`${newOrder} sorted`);
            $('[name="orderby"]').val(orderby);
            $('[name="order"]').val(newOrder);
            getUserByWmpLevel(form, table);
        }
        
        function resetOrder() {
            jQuery('[data-orderby]').parent().addClass('sortable');
            jQuery('[data-orderby]').parent().removeClass('sorted');
        }

        function getUserByWmpLevel(form, table){
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