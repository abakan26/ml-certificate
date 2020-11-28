<?php

define('PLUGIN_ADMIN_PATH', __DIR__);
define('PLUGIN_ADMIN_URI', PLUGIN_URI . '/admin');
spl_autoload_register(function ($class_name) {
    $file_name = __DIR__ . "/core/{$class_name}.php";
    if (file_exists($file_name)) require $file_name;
});

require_once __DIR__ . '/register-admin-pages.php';
require_once __DIR__ . '/profile.php';
require_once __DIR__ . '/woocommerce-product.php';
require_once __DIR__ . '/customer.php';
require_once __DIR__ . '/templates/functions.php';
require_once __DIR__ . '/fields-config.php';
if (wp_doing_ajax()) {
    require_once 'ajax.php';
}

function getFIO($userId)
{
    return get_user_meta($userId, 'last_name', true) . ' ' .
        get_user_meta($userId, 'first_name', true) . ' ' .
        get_user_meta($userId, 'surname', true);
}