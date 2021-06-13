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
<h1>Выданные сертификаты</h1>

<div id="alertError" class="alert alert-danger mt-2" role="alert" style="display: none">
</div>
<div id="alertSuccess" class="alert alert-success mt-2" role="alert" style="display: none">

</div>
<form id="issuedСertificates" class="needs-validation mt-2" novalidate>
    <div class="tablenav top" style="height: auto">
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
            <th scope="col" id="user_login" class="manage-column column-username column-primary sortable asc">
                <span data-orderby="user_login">
                    <span>Имя пользователя</span><span class="sorting-indicator"></span>
                </span>
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
    </table>
</form>
<script>
    (function ($) {
        let form = document.getElementById('issuedСertificates');

    })(jQuery);

</script>
