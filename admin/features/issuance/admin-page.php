<?php
add_action('admin_menu', 'add_admin_menu_user_list_submenu_page');
function add_admin_menu_user_list_submenu_page()
{
    $my_page = add_submenu_page('ml_graduates',
        'Выдача сертификатов',
        'Выдача сертификатов',
        CERTIFICATE_DELIVERY,
        'ml_graduates_list',
        'render_graduates_list_page'
    );
    add_action('load-' . $my_page, function () {
        add_action('admin_enqueue_scripts', function ()
        {
            wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css', ['ml-fontawesome', 'ml-fontawesome']);
            wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
            wp_enqueue_style('ml-fontawesome', PLUGIN_ASSETS_URI . '/css/font-awesome-5.15.1.min.css');
            wp_enqueue_script('ml-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
            wp_enqueue_script('ml-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', ['jquery', 'ml-popper']);
        });
    });
}
