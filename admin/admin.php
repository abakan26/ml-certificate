<?php

define('PLUGIN_ADMIN_PATH', __DIR__);
define('PLUGIN_ADMIN_URI', PLUGIN_URI . '/admin');

require_once PLUGIN_ADMIN_PATH . '/register-admin-pages.php';
require_once PLUGIN_ADMIN_PATH . '/profile.php';
require_once PLUGIN_ADMIN_PATH . '/woocommerce-product.php';
require_once PLUGIN_ADMIN_PATH . '/customer.php';
require_once PLUGIN_ADMIN_PATH . '/templates/functions.php';
require_once PLUGIN_ADMIN_PATH . '/fields-config.php';
if (wp_doing_ajax()) {
    require_once 'ajax.php';
}

function getFIO($userId)
{
    return get_user_meta($userId, 'last_name', true) . ' ' .
        get_user_meta($userId, 'first_name', true) . ' ' .
        get_user_meta($userId, 'surname', true);
}