<?php
add_action('admin_print_scripts-profile.php', 'disabled_edit_any_fields');
add_action('admin_print_scripts-user-edit.php', 'disabled_edit_any_fields');
add_action( 'profile_update',  'updateCustomerFieldInCertificate', 10, 1);
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
