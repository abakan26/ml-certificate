<?php
add_action('admin_menu', 'add_admin_menu_certificate_templates_submenu_page');
function add_admin_menu_certificate_templates_submenu_page()
{
    $my_page = add_submenu_page('ml_graduates',
        'Шаблоны сертификатов',
        'Шаблоны сертификатов',
        CERTIFICATE_EDIT,
        'ml_certificate_templates',
        'render_certificate_templates'
    );
    add_action('load-' . $my_page, 'load_certificate_templates_page_js');

    function load_certificate_templates_page_js()
    {
        add_action('admin_enqueue_scripts', function ()
        {
            wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
            wp_enqueue_style('jquery-ui', PLUGIN_ASSETS_URI . '/css/jquery-ui.min.css');
            wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css', null, 2);
            wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
            wp_add_inline_style( 'ml-style', FontHandler::generateDynamic() );

            wp_enqueue_script("jquery", PLUGIN_ASSETS_URI . '/js/jquery-3.5.1.min.js');
            wp_enqueue_script("ml-jquery-ui", PLUGIN_ASSETS_URI . '/js/jquery-ui.min.js', ['jquery'], '', true);
            wp_enqueue_script('ml-templates', PLUGIN_ASSETS_URI . '/js/script.js', ['jquery', 'ml-jquery-ui'], '2.3.1', true);
        });
    }
}

