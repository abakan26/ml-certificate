<?php
global $wpdb;

if (isset($_GET['add']) && $_GET['add'] = 'certificate') {
    include 'templates/certificate-template-add.php';
} elseif (isset($_GET['download']) && !empty($_GET['download'])) {
    $certificate_template_id = intval($_GET['download']);
    $certificate = CertificateTemplate::getTemplate($certificate_template_id);
    $content = $certificate->getContent();
    $fields = $certificate->getFields();
    $fields = array_map(function ($field) {
        return array_merge((array)$field, ['text' => $field->example_text]);
    }, $fields);
    $image_src = $certificate->getImgSrc();
    include 'certificate-generator.php';

} elseif (isset($_GET['certificate_id']) && !empty($_GET['certificate_id'])) {
    $certificate_template_id = intval($_GET['certificate_id']);
    $certificate = CertificateTemplate::getTemplate($certificate_template_id);
    $data['title'] = $certificate->name;
    $content = $certificate->getContent();
    $data['image'] = $certificate->getImgSrc();
    $fields = [];

    /* TODO связать с глобальной настройкой $FIELDS*/
    foreach ($certificate->getFields() as $code => $field){
        $fields[] = new Field($field->name, $code, (array)$field);
    }
    /* TODO */

    $data['attachment_id'] = $content->attachment_id;
    $data['download'] = CertificateTemplate::getDownloadLink($certificate_template_id);
    include 'templates/certificate-template-edit.php';
} else {
    $certificates = CertificateTemplate::getCertificateTemplates();
    $data['certificates'] = array_map(function ($certificate) {
        return [
            'name' => $certificate->name,
            'edit_url' => CertificateTemplate::getEditLink($certificate->id),
            'view_pdf' => CertificateTemplate::getDownloadLink($certificate->id)
        ];
    }, $certificates);
    include 'templates/certificate-template-list.php';
}
