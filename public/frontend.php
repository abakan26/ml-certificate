<?php
require 'certificate-verification-shortcode.php';

add_action('wp_enqueue_scripts', function (){
    wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('ml-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
    wp_enqueue_script('ml-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', ['jquery', 'ml-popper']);
});

add_shortcode( 'certificate_verification', 'certificate_verification_callback' );
//[certificate_verification]

add_action('wp_ajax_ml_verify_certificate_by_fio', 'existCertificateByFIO');
add_action('wp_ajax_nopriv_ml_verify_certificate_by_fio', 'existCertificateByFIO');

function existCertificateByFIO()
{
    $result = Certificate::getCertificateByFIO(
        $_POST['graduate_first_name'],
        $_POST['graduate_last_name'],
        $_POST['graduate_surname']
    );

    if (empty($result)) {
        die(json_encode([
            'status' => 'warning',
            'message' => 'Данный не соответствуют ни одному результату'
        ]));
    }
    die(json_encode([
        'status' => 'success',
        'message' => 'Подтверждено'
    ]));

}


add_action('wp_ajax_ml_verify_certificate_by_series', function (){
    die(json_encode(['status' => 'success', 'message' => 'wp_ajax_ml_verify_certificate_by_series']));
});
add_action('wp_ajax_nopriv_ml_verify_certificate_by_series', function (){
    die(json_encode(['status' => 'success', 'message' => 'wp_ajax_nopriv_ml_verify_certificate_by_series']));
});