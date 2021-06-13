<?php
/**
 * @var int $current
 * @var int $total
 * @var string $link
 * @var array $paginationNumbers
 */
?>
<ul class="pagination">
    <? if ($current !== 1): ?>
        <li class="page-item">
            <a class="page-link" aria-controls="dataTable" data-page-num="1" tabindex="0" href="<?= $link . '1'; ?>">
                В начало
            </a>
        </li>
        <li class="paginate_button page-item previous" id="dataTable_previous">
            <a class="page-link" data-page-num="<?= $current - 1 ?>" href="<?= $link . (string)($current - 1); ?>" aria-label="Previous">
                <span aria-hidden="true" class="fa fa-arrow-left"></span>
            </a>
        </li>
    <? endif; ?>

    <? foreach ($paginationNumbers as $number): ?>
        <? if ($number === $current): ?>
            <li class="paginate_button page-item active">
                <span class="page-link"  aria-controls="dataTable" data-page-num="<?= $current; ?>" tabindex="0">
                    <?= $current; ?>
                </span>
            </li>
        <? else: ?>
            <li class="paginate_button page-item">
                <a class="page-link" aria-controls="dataTable" data-page-num="<?= $number; ?>" tabindex="0"
                   href="<?= $link . $number; ?>">
                    <?= $number; ?>
                </a>
            </li>
        <? endif; ?>
    <? endforeach; ?>

    <? if ($current !== $total): ?>
        <li class="page-item">
            <a class="page-link" data-page-num="<?= $current + 1 ?>" href="<?= $link . (string)($current + 1); ?>"
               aria-label="Next">
                <span aria-hidden="true" class="fa fa-arrow-right"></span>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link" data-page-num="<?= $total ?>" href="<?= $link . $total; ?>">В конец</a>
        </li>
    <? endif; ?>
</ul>