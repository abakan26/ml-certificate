<?php

?>

<?php if (!empty($certificates)): ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th><label for="twitter">Список доступных сертификатов</label></th>
            <td>
                <div class="mbl-settings-color">
                    <ul>
                        <?php foreach ($certificates as $certificate): ?>
                            <li>
                                <span><?= $certificate['text']; ?></span>
                                <a target="_blank" href="<?= $certificate['view']; ?>">
                                    Просмотр в pdf
                                </a>
                                <a target="_blank" href="<?= $certificate['download']; ?>" class="button" style="margin-left: 15px;">Скачать в pdf</a>
                                <a target="_blank" class="button" style="margin-left: 15px;" href="<?= $certificate['download_jpg']; ?>">
                                    Скачать в jpg
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>
