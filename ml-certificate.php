<?php
/*
 * Plugin Name:  Ml student certificate
 */
if (!defined('ABSPATH')) {
    exit;
}

define('PLUGIN_NAME', 'ml-certificate');
define('PLUGIN_URI', plugin_dir_url('ml-certificate/ml-certificate.php'));
define('CERTIFICATE_DELIVERY', 'certificate-delivery');
define('CERTIFICATE_TEMPLATES_EDIT', 'certificate-edit');
define('GRADUATES_VIEW', 'graduates-view');
define('GRADUATES_EDIT', 'graduates-edit');
define('CATEGORY_ONLINE_COURSES', 36);

require_once __DIR__ . '/admin/admin.php';

register_activation_hook(__FILE__, 'ml_certificate_activate');

function ml_certificate_activate()
{
    $role_administrator = get_role('administrator');
    $role_administrator->add_cap(CERTIFICATE_DELIVERY);
    $role_administrator->add_cap(CERTIFICATE_TEMPLATES_EDIT);
    $role_administrator->add_cap(GRADUATES_VIEW);
    $role_administrator->add_cap(GRADUATES_EDIT);
    $role_coach = get_role('coach');
    $role_coach->add_cap(CERTIFICATE_DELIVERY);
    $role_coach->add_cap(GRADUATES_VIEW);
}