<?php

const PLUGIN_ADMIN_PATH = __DIR__;
const PLUGIN_ADMIN_URI = PLUGIN_URI . '/admin';

require_once PLUGIN_ADMIN_PATH . '/fields-config.php';
require_once PLUGIN_ADMIN_PATH . '/register-admin-pages.php';
require_once PLUGIN_ADMIN_PATH . '/features/view-certificates-in-profiles/main.php';
require_once PLUGIN_ADMIN_PATH . '/features/profile-fields/main.php';
require_once PLUGIN_ADMIN_PATH . '/features/woocommerce-support/main.php';
require_once PLUGIN_ADMIN_PATH . '/customer.php';
if (wp_doing_ajax()) {
    require_once 'ajax.php';
    $selection = new Selection();
    $selection->init();
}

add_action('admin_notices', [new MLC_AdminNotice(), 'displayAdminNotice']);

function getFIO($userId): string
{
    return get_user_meta($userId, 'last_name', true) . ' ' .
        get_user_meta($userId, 'first_name', true) . ' ' .
        get_user_meta($userId, 'surname', true);
}
