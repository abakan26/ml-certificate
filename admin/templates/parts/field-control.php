<?php
/**
 * @global Field $field
 */
?>
<fieldset class="card root" data-control="<?= $field->code ?>">
    <legend>Настройки поля <?= $field->name ?></legend>
    <div class="form-check mb-2">
        <input name="fields[<?= $field->code ?>][hide]" class="form-check-input" id="hide_field_<?= $field->code ?>"
               type="checkbox" value="1" <?=  $field->hide ? 'checked' : ''; ?>
               style="margin-top: .3rem!important;margin-left: -1.25rem!important;">
        <label class="form-check-label" for="hide_field">Скрыть это поле</label>
    </div>
    <div class="row">
        <div class="col-6">
            <label>
                X (px): <input data-position="x" class="form-control"
                               name="fields[<?= $field->code ?>][position][left]"
                               type="number">
            </label>
        </div>
        <div class="col-6">
            <label>
                Y (px): <input data-position="y" class="form-control"
                               name="fields[<?= $field->code ?>][position][top]"
                               type="number">
            </label>
        </div>
        <div class="col-6">
            <label>
                Ширина (px): <input data-size="width" class="form-control"
                                    name="fields[<?= $field->code ?>][size][width]"
                                    type="number">
            </label>
        </div>
        <div class="col-6">
            <label>
                Размер шрифта (px): <input data-font="size" class="form-control"
                                           name="fields[<?= $field->code ?>][font_size]"
                                           type="number">
            </label>
        </div>
        <div class="col-6">
            <label>
                Жирность:
                <select data-font="weight" class="form-control"
                        name="fields[<?= $field->code ?>][font_weight]">
                    <option value="normal">Нормальный</option>
                    <option value="bold">Жирный</option>
                </select>
            </label>
        </div>
        <div class="col-6">
            <label>
                Семейство шрифтов:
                <select data-font="family" class="form-control"
                        name="fields[<?= $field->code ?>][font_family]">
                    <?php foreach (FontHandler::getFontFamilies() as $fontFamily): ?>
                    <option value="<?= $fontFamily ?>"><?= $fontFamily ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="col-6">
            <label>
                Положение текста:
                <select data-text="align" class="form-control"
                        name="fields[<?= $field->code ?>][text_align]">
                    <option value="left">left</option>
                    <option value="center">center</option>
                    <option value="right">right</option>
                </select>
            </label>
        </div>
        <div class="col-6">
            <label>
                Цвет текста:
                <input  data-text="color" class="form-control"
                        name="fields[<?= $field->code ?>][color]"
                        type="color">
            </label>
        </div>
    </div>
    <input data-text="line_height"
           name="fields[<?= $field->code ?>][line_height]"
           value=""
           type="hidden">
    <input name="fields[<?= $field->code ?>][name]"
           value="<?= $field->name ?>"
           type="hidden">
    <input name="fields[<?= $field->code ?>][example_text]"
           value="<?= $field->example_text ?>"
           type="hidden">
</fieldset>
