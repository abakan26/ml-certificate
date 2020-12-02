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
    include 'templates/certificate-template-edit.php';
} else {
    $certificates = CertificateTemplate::getCertificateTemplates();
    echo "<pre>";

    echo "</pre>";
    $data['certificates'] = array_map(function ($certificate) {
        $certificates = Certificate::query([
           'filter' => ['certificate_template_id' =>  $certificate->id]
        ]);
        $products = get_posts([
            'post_type' => 'product',
            'meta_query' => [
                [
                    'key' => 'template_id',
                    'value' => $certificate->id
                ]
            ]

        ]);
        $products_ids = array_map(function ($p) {return $p->ID;}, $products);

        return [
            'template_id' => $certificate->id,
            'name' => $certificate->name,
            'edit_url' => CertificateTemplate::getEditLink($certificate->id),
            'view_pdf' => CertificateTemplate::getDownloadLink($certificate->id),
            'product_link' => admin_url("edit.php?post_type=product&product_ids=" . implode(',', $products_ids)),
            'product_count' => count($products_ids),
            'certificate_count' => $certificates['total'],
            'certificate_link' => admin_url("admin.php?page=ml_certificate_edit&certificate_template_id=" . $certificate->id),
        ];
    }, $certificates);
    include 'templates/certificate-template-list.php';
}
