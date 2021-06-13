<?php
add_action('admin_menu', 'add_admin_menu_edit_certificate_submenu_page');
function add_admin_menu_edit_certificate_submenu_page()
{
    $my_page = add_submenu_page('ml_graduates',
        'Выданные сертификаты',
        'Выданные сертификаты',
        CERTIFICATE_EDIT,
        'ml_certificate_edit',
        'render_edit_certificate_page'
    );
    add_action('load-' . $my_page, function ()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css', ['ml-fontawesome', 'ml-fontawesome']);
            wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
            wp_enqueue_style('ml-fontawesome', PLUGIN_ASSETS_URI . '/css/font-awesome-5.15.1.min.css');
            wp_enqueue_script('ml-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
            wp_enqueue_script('ml-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', ['jquery', 'ml-popper']);
            wp_enqueue_script("formToObject", PLUGIN_ASSETS_URI . '/js/formToObject.min.js', false, '0.1.1');
            wp_enqueue_script("certificate", PLUGIN_ASSETS_URI . '/js/certificate.js', ['formToObject'], '0.1.33');
            wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css');
        });
    });
}
