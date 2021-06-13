<?php
/**
 * @global array $data
 */
?>
<div class="container-fluid">
    <div class="card shadow mb-4 p-0" style="max-width: 100%">
        <span class="table-loading-icon fas fa-spin fa-spinner"></span>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Шаблоны сертификатов
                <a href="<?= admin_url() . 'admin.php?page=ml_certificate_templates&add=certificate' ?>"
                   class="btn btn-outline-primary">
                    Добавить новый
                </a>
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
                                    <th rowspan="1" colspan="1">
                                        №
                                    </th>
                                    <th rowspan="1" colspan="1">
                                        Название
                                    </th>
                                    <th rowspan="1" colspan="1">
                                        Предпросмотр
                                    </th>
                                    <th rowspan="1" colspan="1">
                                        Курсы
                                    </th>
                                    <th rowspan="1" colspan="1">
                                        Выданные сертификаты
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($data['certificates'] as $key => $certificate): ?>
                                    <tr id="certificate_template_<?= $certificate['template_id'] ?>">
                                        <td><?= $key + 1; ?></td>
                                        <td><a href="<?= $certificate['edit_url']; ?>"><?= $certificate['name']; ?>
                                                (Редактировать)</a></td>
                                        <td><a target="_blank" href="<?= $certificate['view_pdf']; ?>">Смотреть в
                                                pdf</a></td>
                                        <td>
                                            <?php if ($certificate['product_count']): ?>
                                            <a href="<?= $certificate['product_link'] ?>" target="_blank">
                                                Смотреть курсы (<?= $certificate['product_count'] ?>)
                                            </a>
                                            <?php else: ?>
                                                Нет
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($certificate['certificate_count']): ?>
                                                <a href="<?= $certificate['certificate_link'] ?>" target="_blank">
                                                    Смотреть сертификаты (<?= $certificate['certificate_count'] ?>)
                                                </a>
                                            <?php else: ?>
                                                Нет
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger" data-action="delete"
                                                    data-id="<?= $certificate['template_id'] ?>" type="button"><span
                                                        class="fa fa-trash"></span></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let buttonsDelete = document.querySelectorAll("[data-action=\"delete\"]");
    Array.from(buttonsDelete).forEach(button => button.addEventListener("click", certificateTemplateDelete));
    function certificateTemplateDelete(event) {
        let data = new FormData();
        data.append("action", "ml_delete_certificate_template");
        data.append("id", this.dataset.id);
        fetch(ajaxurl, {
            method: "POST",
            body: data
        }).then(response => response.json())
        .then(json => {
            if (json.status === "error") {
                alert(json.message);
            } else if (json.status === "success") {
               alert("Успешно удален");
                let tr = document.getElementById("certificate_template_" + json.id);
                tr.parentNode.removeChild(tr);
            }
        })
        .catch(error => alert(error))
    }
</script>
