<?php
/*
 * Plugin Name:  Ml student certificate
 */
if (!defined('ABSPATH')) {
    exit;
}

define('PLUGIN_NAME', 'ml-certificate');
define('PLUGIN_PATH', __DIR__);
define('PLUGIN_URI', plugin_dir_url('ml-certificate/ml-certificate.php'));
define('PLUGIN_ASSETS_URI', plugin_dir_url('ml-certificate/ml-certificate.php') . '/assets');

// capabilities
define('CERTIFICATE_DELIVERY', 'certificate-delivery'); // Выдача сертификатов
define('CERTIFICATE_EDIT', 'certificate-edit'); // Редактирование выданных сертификатов (стр. Выданные сертификаты)

define('GRADUATES_VIEW', 'graduates-view');
define('GRADUATES_EDIT', 'graduates-edit');
define('CATEGORY_ONLINE_COURSES', 36);

spl_autoload_register(function ($class_name) {
    $file_name = __DIR__ . "/core/{$class_name}.php";
    if (file_exists($file_name)) require $file_name;
});
require_once __DIR__ . '/admin/admin.php';
require_once __DIR__ . '/public/frontend.php';

register_activation_hook(__FILE__, 'ml_certificate_activate');
add_action("init", function (){
    if (get_current_user_id() === 48) {

    }
});

function ml_certificate_activate()
{
    $role_administrator = get_role('administrator');
    $role_administrator->add_cap(CERTIFICATE_DELIVERY);
    $role_administrator->add_cap(CERTIFICATE_EDIT);
    $role_administrator->add_cap(GRADUATES_VIEW);
    $role_administrator->add_cap(GRADUATES_EDIT);
    $role_coach = get_role('coach');
    $role_coach->add_cap(CERTIFICATE_DELIVERY);
}