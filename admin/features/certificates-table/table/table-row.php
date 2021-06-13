<?php
function renderRow($props, $uniqKey, $return = false)
{
    $member = get_user_by("ID", $props->user_id);
    $responsible_person = get_user_by("ID", $props->responsible_person);

    if ($return) ob_start();
    ?>
    <tr role="row" class="<?= $uniqKey % 2 ? 'odd' : 'even'; ?>" id="certificate_tr_<?= $props->certificate_id ?>">
        <td scope="row" class="check-column">
            <input type="checkbox" name="certificates[]" id="certificate_<?= $props->certificate_id ?>" value="<?= $props->certificate_id ?>">
        </td>
        <td>
            <a href="<?= get_edit_user_link($member->ID); ?>" target="_blank">
                <?= $member->user_login ?>
            </a>
        </td>
        <td><?= $props->graduate_last_name ?></td>
        <td><?= $props->graduate_first_name ?></td>
        <td><?= $props->graduate_surname ?></td>
        <td><?= $props->certificate_name ?></td>
        <td><?= date('d-m-Y', strtotime($props->date_issue)) ?></td>
        </td>
        <td>
            <a href="<?= get_edit_user_link($responsible_person->ID); ?>"
               target="_blank">
                <?= $responsible_person->user_login ?>
            </a>
        </td>
        <td>
            <a href="<?= CertificateTemplate::getTemplateLink($props->certificate_template_id) ?>" target="_blank">
                <?= CertificateTemplate::getNameById($props->certificate_template_id) ?>
            </a>
        </td>
        <td>
            <a target="_blank"
                href="<?= admin_url('admin.php?page=ml_certificate_edit&mode=view&certificate_id=' .
                    $props->certificate_id) ?>">
                <span class="fa fa-eye"></span>
            </a>
        </td>
        <td><?= date('d-m-Y', strtotime($props->create_date)) ?></td>
    </tr>
    <?php if ($return) return ob_get_clean();
}

function renderTbody($certificates, $return = false)
{
    $body = '';
    foreach ($certificates as $key => $value) {
        $body .= renderRow($value, $key, true);
    }
    if ($return) return $body;
    echo $body;
}

function getPagination($params)
{
    $link = $params['link'];
    $current = $params['page'];
    $total = $params['total'];
    $maxCountNumbers = 5;
    $paginationNumbers = [];
    $paginationNumbers[] = $current;
    $iter = 1;
    while (count($paginationNumbers) < $maxCountNumbers) {
        $leftPageNumber = $current - $iter;
        $rightPageNumber = $current + $iter;
        if ($leftPageNumber >= 1) {
            $paginationNumbers[] = $leftPageNumber;
        }
        if ($rightPageNumber <= $total) {
            $paginationNumbers[] = $rightPageNumber;
        }
        if ($leftPageNumber < 1 && $rightPageNumber > $total) {
            break;
        }
        $iter++;
    }
    sort($paginationNumbers);
    ob_start();
    if (count($paginationNumbers) !== 1) {
        include 'pagination.php';
    }
    return ob_get_clean();
}

function renderResultCount($pageNum, $perPage, $totalPages, $total): string
{
    if ($total <= 0) return '';
    $from = $perPage * ($pageNum - 1)  + 1;
    $to = min($perPage * $pageNum, $total);
    return "Элементы с $from по $to из $total";
}
