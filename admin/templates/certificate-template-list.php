<?php
/**
 * @global array $data
 */
?>
<div class="row mt-2">
    <div class="col-12 d-flex align-items-center">
        <h2 style="display: inline-block!important;margin: 0 5px 0 0;">Шаблоны сертификатов</h2>
        <a href="<?= admin_url() . 'admin.php?page=ml_certificate_templates&add=certificate' ?>"
           class="btn btn-outline-primary">
            Добавить новый
        </a>
    </div>
    <div class="col-6">
        <table class="table table-hover mt-5">
            <thead>
            <tr>
                <th>№</th>
                <th>Название</th>
                <th>Предпросмотр</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data['certificates'] as $key => $certificate): ?>
                <tr>
                    <td><?= $key + 1; ?></td>
                    <td><a href="<?= $certificate['edit_url']; ?>"><?= $certificate['name']; ?> (Редактировать)</a></td>
                    <td><a target="_blank" href="<?= $certificate['view_pdf']; ?>">Смотреть в pdf</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

