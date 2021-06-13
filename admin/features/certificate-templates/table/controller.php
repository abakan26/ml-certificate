<?php
$certificates = CertificateTemplate::getCertificateTemplates();
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
include 'view.php';
