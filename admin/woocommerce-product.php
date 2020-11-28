<?php

add_filter('woocommerce_product_data_tabs', 'add_certificate_tab', 10, 1);
function add_certificate_tab($tabs)
{

    $tabs['special_panel'] = array(
        'label' => 'Привязка сертификата',
        'target' => 'certificate_product_data',
        'class' => [],
        'priority' => 20,
    );

    return $tabs;
}

add_action('woocommerce_product_data_panels', 'add_certificate_fields');
function add_certificate_fields()
{
    $has_certificate = get_post_meta(get_the_ID(), 'has_certificate', true);

    $no_certificate_class = empty($has_certificate) ? ' no_certificate' : '';
    echo '<div id="certificate_product_data" class="panel woocommerce_options_panel ">';
    echo '<div class="options_group options_group_certificate' . $no_certificate_class . '">';


    woocommerce_wp_checkbox([
        'id' => 'has_certificate',
        'name' => 'certificate[has_certificate]',
        'label' => __('Выдается сертификат', 'woocommerce'),
        'cbvalue' => 'yes',
        'value' => $has_certificate
    ]);

    $how_to_issue = get_post_meta(get_the_ID(), 'how_to_issue', true);
    woocommerce_wp_radio(array(
        'id' => 'how_to_issue',
        'name' => 'certificate[how_to_issue]',
        'label' => 'Как будет выдаваться сертификат',
        'value' => $how_to_issue ? $how_to_issue : 'employee',
        'wrapper_class' => 'show_if_yes_certificate',
        'options' => array(
            'employee' => 'выдается сотрудником',
            'onbuy' => 'выдается при покупке',
        )
    ));


    woocommerce_wp_text_input(array(
        'id' => 'certificate_series',
        'name' => 'certificate[certificate_series]',
        'label' => __('Серия сертификата', 'woocommerce'),
        'placeholder' => 'CA',
        'desc_tip' => 'false',
        'description' => __('Укажите серию сертификата (до пяти символов)', 'woocommerce'),
        'value' => get_post_meta(get_the_ID(), 'certificate_series', true),
        'wrapper_class' => 'show_if_yes_certificate'
    ));

    woocommerce_wp_text_input(array(
        'id' => 'course_name',
        'name' => 'certificate[course_name]',
        'label' => __('Название материала', 'woocommerce'),
        'placeholder' => 'Название материала',
        'desc_tip' => 'false',
        'description' => __('Укажите Название материала, которое будет указано в сертификате', 'woocommerce'),
        'value' => get_post_meta(get_the_ID(), 'course_name', true),
        'wrapper_class' => 'show_if_yes_certificate'
    ));

    $options = ['' => 'Выберите шаблон'];
    foreach (CertificateTemplate::getCertificateTemplates() as $template) {
        $options[$template->id] = $template->name;
    }

    woocommerce_wp_select(array(
        'id' => 'template_id',
        'name' => 'certificate[template_id]',
        'label' => 'Шаблон сертификата',
        'wrapper_class' => 'show_if_yes_certificate',
        'description' => 'Какой шаблон сертификата использовать?',
        'desc_tip' => true,
        'style' => 'margin-bottom:40px;',
        'value' => get_post_meta(get_the_ID(), 'template_id', true),
        'options' => $options
    ));

    echo '</div>';
    echo '</div>';

}

add_action('woocommerce_process_product_meta', 'save_certificate_fields', 10);
function save_certificate_fields($post_id)
{
    if (!isset($_POST['certificate'])) return false;
    if (isset($_POST['certificate']['has_certificate'])) {
        foreach ($_POST['certificate'] as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
        foreach (Certificate::getCertificatesByProductId($post_id, 'ids', true) as $certificate_id){
            Certificate::update($certificate_id, [
               'certificate_template_id' => $_POST['certificate']['template_id'],
               'series' => $_POST['certificate']['certificate_series'],
               'course_name' => $_POST['certificate']['course_name'],
            ]);
        }
    } else {
        delete_post_meta($post_id, 'has_certificate');
        foreach ($_POST['certificate'] as $key => $value) {
            delete_post_meta($post_id, $key);
        }
    }

}

add_action('save_post_product', 'updateCertificateDataFromProduct', 10, 1);
function updateCertificateDataFromProduct($postId)
{
    foreach (Certificate::getCertificatesByProductId($postId, 'ids') as $certificate_id){
        Certificate::update($certificate_id, [
            'certificate_name' => get_post($postId)->post_excerpt
        ]);
    }
}

add_action('admin_footer', 'add_style_script_certificate_fields');
function add_style_script_certificate_fields()
{

    ?>
    <style>
        .no_certificate .show_if_yes_certificate {
            display: none;
        }
    </style>
    <script>
        jQuery('#has_certificate').on('change', function () {
            if (jQuery('#has_certificate').prop('checked')) {
                jQuery('.options_group_certificate').removeClass('no_certificate');
            } else {
                jQuery('.options_group_certificate').addClass('no_certificate');
            }
        })

    </script>
    <?php

}