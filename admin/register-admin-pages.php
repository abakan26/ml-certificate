<?php
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
    add_action('load-' . $my_page, function () {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('index.f8631684', PLUGIN_ASSETS_URI . '/index.d2249185.css', null, '1.1.6');
            wp_enqueue_script("index.d7dfb2a8", PLUGIN_ASSETS_URI . '/index.e4a11556.js', null, '1.1.9');
        });
    });
}

add_filter('script_loader_tag', 'add_type_attribute', 10, 3);
function add_type_attribute($tag, $handle, $src)
{
    if ('index.d7dfb2a8' !== $handle) {
        return $tag;
    }
    $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    return $tag;
}

function render_graduates_page()
{
    include 'templates/graduates.php';
}

require_once 'features/issuance/main.php';
require_once 'features/certificate-templates/main.php';
require_once 'features/certificates-table/main.php';
