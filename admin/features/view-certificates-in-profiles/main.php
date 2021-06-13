<?php
add_action('show_user_profile', 'view_user_profile_available_certificates', 10);
add_action('show_user_profile', 'view_user_profile_available_certificates_no_admin', 10);
add_action('edit_user_profile', 'view_user_profile_available_certificates_no_admin', 10);

function view_user_profile_available_certificates_no_admin($profileuser)
{
    $userID = $profileuser->ID;
    $userIdParam = parse_url(get_edit_user_link( $userID ))['query'];
    $baseUrl = strtok(get_edit_user_link( $userID ), '?') . '?' . ($userIdParam ? $userIdParam . '&' : '');

    $certificates = array_map(function ($certificate) use ($userID, $baseUrl){
        $baseQueryParams = '';
        if ($certificate->id !== 0) {
            $viewParams = http_build_query(['certificate_id' => $certificate->id]);
            $downloadParams = http_build_query(['certificate_id' => $certificate->id, 'download' => 1]);
            $downloadParamsJpg = http_build_query(['certificate_id' => $certificate->id, 'type' => 'jpg', 'download' => 1]);
        } else {
            $viewParams = http_build_query(['prid' => $certificate->product_id, 'autogen' => 1]);
            $downloadParams = http_build_query(['prid' => $certificate->product_id, 'autogen' => 1, 'download' => 1]);
            $downloadParamsJpg = http_build_query([
                'prid' => $certificate->product_id,
                'autogen' => 1,
                'download' => 1,
                'type' => 'jpg'
            ]);
        }

        return [
            'text' => $certificate->certificate_name,
            'view' => $baseUrl . $viewParams,
            'download_jpg' => $baseUrl . $downloadParamsJpg,
            'download' => $baseUrl . $downloadParams,
        ];

    }, Certificate::getCustomerCertificates($profileuser->ID));

    if (isset($_GET['certificate_id']) && !empty($_GET['certificate_id'])) {
        $certificate_id = intval($_GET['certificate_id']);
        if(Certificate::isCustomerCertificate($userID, $certificate_id)){
            $certificate = Certificate::getCertificate($certificate_id);
            $generator = CertificateGenerator::getCertificateGeneratorByCertificate($certificate);
            if (isset($_GET['download']) && !empty($_GET['download'])) {
                if (isset($_GET['type']) && $_GET['type'] === 'jpg') {
                    $generatorJPG = new CertificateGeneratorJPG($generator);
                    $generatorJPG->render('certificate.jpg', CertificateGenerator::DOWNLOAD);
                    exit();
                }
                $generator->render('certificate.pdf',  CertificateGenerator::DOWNLOAD);
                exit();
            }
            $generator->render();
            exit();
        }
    } else if (isset($_GET['prid']) && !empty($_GET['prid']) ){
        $certificate = Certificate::autoGenerateCertificate([
            'user_id' => $userID,
            'product_id' => intval($_GET['prid']),
        ]);

        $generator = CertificateGenerator::getCertificateGeneratorByCertificate($certificate);
        if (isset($_GET['download']) && !empty($_GET['download'])) {
            $generator->render('certificate.pdf',  CertificateGenerator::DOWNLOAD);
            exit();
        }
        $generator->render();
        exit();
    }
    include 'view.php';
}

function view_user_profile_available_certificates($profileuser)
{
    $certificates = [];
    $userID = $profileuser->ID;
}
