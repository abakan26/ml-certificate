<?php
function printSearchResult($certificates)
{

    ob_start();
    ?>
    <p class="h5 mb-2">Найденные результаты</p>
    <table class="table table-bordered certificate-search-result">
        <thead>
        <tr class="bg-primary text-white">
            <th class="text-center" scope="col"><em>ФИО</em></th>
            <th class="text-center" scope="col"><em>Название сертификата</em></th>
            <th class="text-center" scope="col"><em>Серия</em></th>
            <th class="text-center" scope="col"><em>Номер</em></th>
            <th class="text-center" scope="col"><em>Дата выдачи</em></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($certificates as $userId => $result): ?>
            <tr>
                <th class="result-fio table-light text-center" rowspan="<?= count($result['certificates']) ?>" scope="row"><?= $result['fio'] ?></th>
            <?php foreach ($result['certificates'] as $key => $certificate): ?>
                <?php if ($key !== 0): ?>
                <tr>
                <?php endif; ?>
                    <td class="result-certificate-name text-center"> <?= $certificate->getCertificateName(); ?> </td>
                    <td class="text-center"> <?= $certificate->series; ?> </td>
                    <td class="text-center"> <?= $certificate->number; ?> </td>
                    <td class="text-center"> <?= $certificate->getDateIssue(); ?> </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <?php
    return ob_get_clean();
}
