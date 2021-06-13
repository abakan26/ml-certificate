<?php
require 'certificate-verification-shortcode.php';
require 'search-results.php';

add_action('wp_enqueue_scripts', function (){
    wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('ml-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
    wp_enqueue_script('ml-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', ['jquery', 'ml-popper']);
});

add_shortcode( 'certificate_verification', 'certificate_verification_callback' );

//[certificate_verification]

add_action('wp_ajax_ml_verify_certificate_by_fio', 'existCertificateByAjax');
add_action('wp_ajax_nopriv_ml_verify_certificate_by_fio', 'existCertificateByAjax');
add_action('wp_ajax_ml_verify_certificate_by_series', 'existCertificateByAjax');
add_action('wp_ajax_nopriv_ml_verify_certificate_by_series', 'existCertificateByAjax');
add_action('mbl-body-top', function () {
    if (empty($_COOKIE['knowledgebase-page'])):
        include "modal.php";
    endif;
});

add_filter( 'template_include', 'portfolio_page_template', 99 );
function portfolio_page_template( $template ) {
    if ( is_page( 'proverka-vydannyh-sertifikatov' )  ) {
        return $template = __DIR__ . '/page.php';
    }
    return $template;
}


function existCertificateByAjax()
{
    $result = [];
    if ($_POST['action'] === 'ml_verify_certificate_by_fio') {
        $result = Certificate::getGroupingCertificateByFIO(
            $_POST['graduate_last_name'],
            $_POST['graduate_first_name'],
            $_POST['graduate_surname']
        );
    } elseif ($_POST['action'] === 'ml_verify_certificate_by_series') {
        $certificates = Certificate::getCertificateBySeriesNumber(
            $_POST['series'],
            $_POST['number']
        );
        foreach ($certificates as $certificate) {
            $result[$certificate->user_id]['fio'] = $certificate->getFIO();
            $result[$certificate->user_id]['certificates'][] = $certificate;
        }
    } else {
        die();
    }

    if (empty($result)) {
        die(json_encode([
            'status' => 'warning',
            'message' => 'По вашему запросу не найдено ни одного результата. Проверьте пожалуйста правильность введенных данных'
        ]));
    }
    die(json_encode([
        'status' => 'success',
        'message' => printSearchResult($result)
    ]));

}
