<?php
/**
 * @global array $data
 * @global array $fields
 * @global int $certificate_template_id
 */

?>

<div class="container-fluid m-0 mt-3">
    <div class="row">
        <div class="col-md-4">
            <h2>Редактирование шаблона</h2>
            <div style="height: 100%;">
                <div style="position:sticky;top: 30px;">
                    <div class="alert alert-danger" role="alert" style="display: none">
                    </div>
                    <form id="templateOptions" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="certificateName">Название шаблона:</label>
                                    <input id="certificateName" class="form-control" type="text" name="name"
                                           value="<?= $data['title']; ?>" required minlength="3">
                                    <div class="invalid-feedback">
                                        Имя должно содержать не менее 3 символов и быть уникальным.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary" data-target="media-open" type="button">Сменить картинку</button>
                        <?php foreach ($fields as $key => $field) {
                            getFieldControl($field);
                        } ?>
                        <input type="hidden" name="action" value="ml_update_certificate_template">
                        <input type="hidden" name="attachment_id" value="<?= $data['attachment_id']; ?>">
                        <input type="hidden" name="certificate_id" value="<?= $certificate_template_id; ?>">
                        <div class="text-right">
                            <input class="mt-3 btn btn-primary" type="submit" value="Сохранить">
                        </div>
                    </form>
                    <a class="link" target="_blank" href="<?= $data['download'] ?>">Скачать пример в pdf</a>
                </div>
            </div>
        </div>
        <div class="col-md-8 d-flex justify-content-center">
            <div class="parent-wrap">
                <div class="parent a">
                    <img src="<?= $data['image']; ?>" alt=""
                         style="position:absolute;width: 210mm; height: 297mm;top: 0;left: 0;"
                    >
                    <?php foreach ($fields as $key => $field) {
                        getFieldView($field);
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
