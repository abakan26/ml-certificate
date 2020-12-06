<?php
add_action('show_user_profile', 'view_user_profile_available_certificates', 10);
add_action('show_user_profile', 'view_user_profile_available_certificates_no_admin', 10);
add_action('edit_user_profile', 'view_user_profile_available_certificates_no_admin', 10);
add_action('admin_print_scripts-profile.php', 'disabled_edit_any_fields');
add_action( 'profile_update',  'updateCustomerFieldInCertificate', 10, 1);

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
        } else {
            $viewParams = http_build_query(['prid' => $certificate->product_id, 'autogen' => 1]);
            $downloadParams = http_build_query(['prid' => $certificate->product_id, 'autogen' => 1, 'download' => 1]);
        }

        return [
            'text' => $certificate->certificate_name,
            'view' => $baseUrl . $viewParams,
            'download' => $baseUrl . $downloadParams,
        ];

    }, Certificate::getCustomerCertificates($profileuser->ID));

    if (isset($_GET['certificate_id']) && !empty($_GET['certificate_id'])) {
        $certificate_id = intval($_GET['certificate_id']);
        if(Certificate::isCustomerCertificate($userID, $certificate_id)){
            $certificate = Certificate::getCertificate($certificate_id);
            $generator = CertificateGenerator::getCertificateGeneratorByCertificate($certificate);
            if (isset($_GET['download']) && !empty($_GET['download'])) {
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
    include 'templates/profile.php';
}

function view_user_profile_available_certificates($profileuser)
{
    $certificates = [];
    $userID = $profileuser->ID;
}

function disabled_edit_any_fields()
{
    if (!current_user_can('manage_options')) {
        $userID = get_current_user_id();
        $user = get_user_by('ID', $userID);

        ?>
        <script>
            window.addEventListener('load', function (event) {
                <?php if (!empty(get_user_meta($userID, 'first_name', true))): ?>
                document.getElementById('first_name').setAttribute('disabled', 'disabled');
                <?php endif; ?>
                <?php if (!empty(get_user_meta($userID, 'last_name', true))): ?>
                document.getElementById('last_name').setAttribute('disabled', 'disabled');
                <?php endif; ?>
                <?php if (!empty(get_user_meta($userID, 'surname', true))): ?>
                document.getElementById('surname').setAttribute('disabled', 'disabled');
                <?php endif; ?>
            });
        </script>
        <?php
    }
}

function updateCustomerFieldInCertificate($userId)
{
    foreach(Certificate::getCustomerCertificates($userId, 'table') as $certificate){
        Certificate::update($certificate->id, [
            'graduate_first_name' => get_user_meta($userId, 'first_name', true),
            'graduate_last_name' => get_user_meta($userId, 'last_name', true),
            'graduate_surname' => get_user_meta($userId, 'surname', true),
        ]);
    }
}