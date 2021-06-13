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

    echo '<div class="series-group">';
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
    echo '<p class="series-error-msg">Такая серия уже существует</p>';
    echo '<style>.series-list{
            font-size: 14px;width: 130px;
            position:absolute;
            right: calc(50% - 275px);
            top: 5px;
            }.series-group{position:relative;}
            .series-error {
            border-color: red!important;
            }
            .series-error-msg {
                color: red;
                display: none;
                margin: 0;
                padding: 0 9px;
                height: 0;
                position: relative;
                top: -15px;
            }
            </style>
            <script>
                (function($) {
                  $("#certificate_series").on("blur", verifyCertificateSeries);
                  $("#certificate_series").on("input", hideError);
                  function hideError() {
                    $("#certificate_series").removeClass("series-error");
                    $("#publish").prop("disabled", false);
                    $(".series-error-msg").hide();
                  }
                  function verifyCertificateSeries(event) {
                    event.preventDefault();
                    const body = new FormData();
                    body.append("series",  event.target.value);
                    fetch(ajaxurl + "?action=ml_verify_certificate_series", {
                        method: "POST",
                        body: body,
                        
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === "success") {
                             hideError()
                        } else{
                             $("#certificate_series").addClass("series-error");
                             $("#publish").prop("disabled", true);
                             $(".series-error-msg").show();
                        }
                    }).catch(e => alert(e))
                    
                  }
                })(jQuery);
            </script>
            ';
    echo '<select class="series-list">';
    foreach (Course::getAllSeries() as $key => $series) {
        $marker = $key + 1;
        echo "<option>" . $marker . ". " . $series . '</option>';
    }
    echo '</select>';
    echo '</div>';

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

    woocommerce_wp_textarea_input(array(
        'id' => 'field1',
        'name' => 'certificate[field1]',
        'label' => __('Доп поле 1', 'woocommerce'),
        'placeholder' => 'Доп поле 1',
        'desc_tip' => 'false',
        'value' => get_post_meta(get_the_ID(), 'field1', true),
        'wrapper_class' => 'show_if_yes_certificate'
    ));

    woocommerce_wp_textarea_input(array(
        'id' => 'field2',
        'name' => 'certificate[field2]',
        'label' => __('Доп поле 2', 'woocommerce'),
        'placeholder' => 'Доп поле 2',
        'desc_tip' => 'false',
        'value' => get_post_meta(get_the_ID(), 'field2', true),
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
    if (!isset($_POST['certificate'])) return;
    if (isset($_POST['certificate']['has_certificate'])) {
        $series = $_POST['certificate']['certificate_series'];
        $seriesExist = Course::existCertificateSeries($post_id, $series);
        foreach ($_POST['certificate'] as $key => $value) {
            if ($seriesExist && $key === 'certificate_series'){
                MLC_AdminNotice::displayError('Такая серия уже существует');
                continue;
            }
            update_post_meta($post_id, $key, $value);
        }
        $updatingFields = [
            'certificate_template_id' => $_POST['certificate']['template_id'],
            'course_name' => $_POST['certificate']['course_name'],
        ];
        if (!$seriesExist){
            $updatingFields['series'] = $_POST['certificate']['certificate_series'];
        }
        foreach (Certificate::getCertificatesByProductId($post_id, 'ids', true) as $certificate_id){
            Certificate::update($certificate_id, $updatingFields);
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

add_action( 'pre_get_posts', 'add_event_table_filters_handler' );
function add_event_table_filters_handler( $query ){

    $cs = function_exists('get_current_screen') ? get_current_screen() : null;

    // убедимся что мы на нужной странице админки
    if( ! is_admin() || empty($cs->post_type) || $cs->post_type != 'product' || $cs->id != 'edit-product' )
        return;


    if(isset($_GET['product_ids']) && !empty($_GET['product_ids'])){
        $product_ids = array_map('intval', explode(',', $_GET['product_ids']));
        $query->set( 'post__in', $product_ids);
    }
}


//ml_verify_certificate_series
add_action('wp_ajax_ml_verify_certificate_series', function () {
    $series = $_POST['series'];
    $seriesExist = Course::existCertificateSeries(0, $series);
    if ($seriesExist){
        die( json_encode([
            "status" => "error",
            "data" => $_POST['series']
        ]) );
    }
    die( json_encode([
        "status" => "success",
        "data" => $_POST['series']
    ]) );
});
