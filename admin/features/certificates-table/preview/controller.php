<?php
$certificate_id = intval($_GET['certificate_id']);
$certificate = Certificate::getCertificate($certificate_id);
$generator = CertificateGenerator::getCertificateGeneratorByCertificate($certificate);
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $generator->render('certificate.pdf', CertificateGenerator::DOWNLOAD);
    exit();
}
$generator->render();
exit();
