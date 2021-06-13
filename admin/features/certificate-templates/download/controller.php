<?php
$certificate_template_id = intval($_GET['download']);
$certificate = CertificateTemplate::getTemplate($certificate_template_id);
$content = $certificate->getContent();
$fields = $certificate->getFields();
$fields = array_map(function ($field) {
    return array_merge((array)$field, ['text' => $field->example_text]);
}, $fields);
$image_src = $certificate->getImgSrc();
$template = CertificateTemplate::getTemplate($certificate_template_id);
$generator = new CertificateGenerator($template);
$generator->render();
