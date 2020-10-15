<?php
add_action('show_user_profile', 'view_user_profile_available_certificates', 10);
add_action('show_user_profile', 'view_user_profile_available_certificates_no_admin', 10);
add_action('edit_user_profile', 'view_user_profile_available_certificates', 10);
add_action('admin_print_scripts-profile.php', 'disabled_edit_any_fields');


function view_user_profile_available_certificates_no_admin($profileuser)
{
    $userID = $profileuser->ID;
    $certificates = array_map(function ($certificate) use ($userID){
        return [
            'text' => "get_term($certificate->term_id, 'wpm-category')->name",
            'view' => admin_url( 'profile.php' ) . '?certificate_id=' . $certificate->id,
            'download' => admin_url( 'profile.php' ) . '?certificate_id='. $certificate->id.'&download=1',
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