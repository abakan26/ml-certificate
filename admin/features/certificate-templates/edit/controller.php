<?php
$certificate_template_id = intval($_GET['certificate_id']);
$certificate = CertificateTemplate::getTemplate($certificate_template_id);
$data['title'] = $certificate->name;
$content = $certificate->getContent();
$data['image'] = $certificate->getImgSrc();
$fields = [];
/* TODO связать с глобальной настройкой $FIELDS*/
foreach ($certificate->getFields() as $field) {
    $globalField = Field::getFieldByCode($field->getCode());
    if($globalField->getName() !== $field->getName()) {
        $field->setName($globalField->getName());
    }
    $fields[] = $field;
}
/* TODO */

$data['attachment_id'] = $content->attachment_id;
$data['download'] = CertificateTemplate::getDownloadLink($certificate_template_id);
include 'view.php';
