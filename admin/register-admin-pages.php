<?php
//add_admin_menu_user_list_page
//add_admin_menu_certificate_page
// current_user_can( 'special_test' )
add_action('admin_menu', 'add_admin_menu_graduates_page');
function add_admin_menu_graduates_page()
{

    $my_page = add_menu_page(
        'Выпускники академии СППМ',
        'Выпускники',
        GRADUATES_VIEW,
        'ml_graduates',
        'render_graduates_page',
        'dashicons-portfolio',
        2
    );
    add_action('load-' . $my_page, 'load_graduates_page_js');
}


function load_graduates_page_js()
{
    add_action('admin_enqueue_scripts', 'enqueue_script_graduates_page');
}

function load_graduates_page_bootstrap()
{
    add_action('admin_enqueue_scripts', 'enqueue_script_graduates_page_bootstrap');
}

function enqueue_script_graduates_page()
{
    wp_enqueue_style('font-awesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('jquery-ui', PLUGIN_ASSETS_URI . '/css/jquery-ui.min.css');
    wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css', null, 2);
    wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_add_inline_style( 'ml-style', FontHandler::generateDynamic() );

    wp_enqueue_script("jquery", PLUGIN_ASSETS_URI . '/js/jquery-3.5.1.min.js');
    wp_enqueue_script("ml-jquery-ui", PLUGIN_ASSETS_URI . '/js/jquery-ui.min.js', ['jquery'], '', true);
    wp_enqueue_script('ml-templates', PLUGIN_ASSETS_URI . '/js/script.js', ['jquery', 'ml-jquery-ui'], '2.3.1', true);
}

function enqueue_script_graduates_page_bootstrap()
{
    wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css', ['ml-fontawesome', 'ml-fontawesome']);
    wp_enqueue_style('ml-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('ml-fontawesome', PLUGIN_ASSETS_URI . '/css/font-awesome-5.15.1.min.css');
    wp_enqueue_script('ml-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
    wp_enqueue_script('ml-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', ['jquery', 'ml-popper']);
}

function render_graduates_page()
{
    include 'templates/graduates.php';
}

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
    add_action('load-' . $my_page, 'load_graduates_page_bootstrap');
}

function render_graduates_list_page()
{
    include 'certificate-issuance.php';
}


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
    add_action('load-' . $my_page, 'load_graduates_page_js');
}

function render_certificate_templates()
{
    include 'certificate-templates.php';
}

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
    add_action('load-' . $my_page, 'load_graduates_page_bootstrap');
    add_action('load-' . $my_page, 'load_ml_certificate_edit');
}

function load_ml_certificate_edit()
{
    add_action('admin_enqueue_scripts', function () {
        wp_enqueue_script("formToObject", PLUGIN_ASSETS_URI . '/js/formToObject.min.js', false, random_int(1, 100));
        wp_enqueue_script("certificate", PLUGIN_ASSETS_URI . '/js/certificate.js', ['formToObject'], random_int(1, 100));
        wp_enqueue_style('ml-style', PLUGIN_ASSETS_URI . '/css/style.css');
    });
}

function render_edit_certificate_page()
{
    include 'certificate-edit.php';
}
