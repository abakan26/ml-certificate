<?php
/**
 * @global Field $field
 */

?>

<div class="view" id="<?= $field->code ?>">
    <div data-view="position" class="drop-element"
         style="top: <?= $field->position->top ?>px; left: <?= $field->position->left ?>px;">
        <div data-view="size" class="meta-element resizable"
             style="width: <?= $field->size->width ?>px">
            <div data-view="text" class="meta-wrap"
                 style="text-align: <?= $field->text_align ?>">
                <div data-view="font" class="meta-name"
                     style="font-size: <?= $field->font_size ?>px;
                             font-weight: <?= $field->font_weight ?>;
                             font-family: <?= $field->font_family ?>;
                             color: <?= $field->color ?>;
                             ">
                    <?= $field->example_text ?>
                </div>
            </div>
        </div>
    </div>
</div>
