<?php
const EVENT_STOCK_FIELD_NAME = 'event_stock_field';
const EVENT_ALL_TICKET_COUNT = 'all_ticket_field';
add_action('woocommerce_product_options_general_product_data', 'add_event_stock_field');
function add_event_stock_field()
{
    global $product, $post;
    echo '<div class="options_group">';

    woocommerce_wp_text_input(array(
        'id' => EVENT_ALL_TICKET_COUNT,
        'label' => __('Всего билетов (если 0 не выводиться): ', 'woocommerce'),
        'type' => 'number',
        'placeholder' => 'Количество билетов',
        'desc_tip' => 'true',
        'description' => __('Введите общее количество билетов на событие', 'woocommerce'),
        'custom_attributes' => array(
            'step' => 'any',
            'min' => '0',
        ),
    ));
    woocommerce_wp_text_input(array(
        'id' => EVENT_STOCK_FIELD_NAME,
        'label' => __('Осталось билетов: ', 'woocommerce'),
        'type' => 'number',
        'placeholder' => 'Осталось билетов',
        'desc_tip' => 'true',
        'description' => __('Введите количество оставшихся билетов на событие', 'woocommerce'),
        'custom_attributes' => array(
            'step' => 'any',
            'min' => '0',
        ),
    ));
    echo '</div>';
}

add_action('woocommerce_process_product_meta', 'save_event_stock_field', 10);
function save_event_stock_field($post_id)
{
    if (!(isset($_POST['woocommerce_meta_nonce'], $_POST[EVENT_STOCK_FIELD_NAME], $_POST[EVENT_ALL_TICKET_COUNT])
        || wp_verify_nonce(sanitize_key($_POST['woocommerce_meta_nonce']), 'woocommerce_save_data'))) return;
    $event_stock = intval($_POST[EVENT_STOCK_FIELD_NAME]);
    $all_stock = intval($_POST[EVENT_ALL_TICKET_COUNT]);
    update_post_meta(
        $post_id,
        EVENT_STOCK_FIELD_NAME,
        $event_stock
    );
    update_post_meta(
        $post_id,
        EVENT_ALL_TICKET_COUNT,
        $all_stock
    );

}


add_action('woocommerce_order_status_completed', function ($order_id) {
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $product_q = get_post_meta($product_id, EVENT_STOCK_FIELD_NAME, true);
        if ($product_q) {
            update_post_meta($product_id, EVENT_STOCK_FIELD_NAME, $product_q - 1);
        }
    }
});

add_action('init', function () {

    function strtotimetz($str, $timezone)
    {
        return strtotime(
            $str, strtotime(
                (new \DateTimeZone($timezone))->getOffset(new \DateTime) - (new \DateTimeZone(date_default_timezone_get()))->getOffset(new \DateTime) . ' seconds'
            )
        );
    }
    if (get_current_user_id() === 833) {
//        var_dump(date('H:i:s', strtotime('20:35:00')));
//        var_dump(date('H:i:s', strtotimetz('20:35:00', ini_get('date.timezone'))));
    }
    if (isset($_GET['hack']) && $_GET['hack'] == 1) {
//          wp_schedule_single_event(strtotimetz('21:10:00', ini_get('date.timezone')), 'true_hook_1');
        wp_schedule_single_event(strtotime('21:19:00') - 10800, 'true_hook_1');
    }

});
add_action('true_hook_1', function () {
    file_put_contents(__DIR__ . '/text.txt', '\n 110000111', FILE_APPEND);
});


add_action( 'pre_get_posts', 'process_admin_shop_order_practicum_filter' );
function process_admin_shop_order_practicum_filter( $query ) {
    global $pagenow;

    if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['filter_shop_order_practicum'] )
        && $_GET['filter_shop_order_practicum'] != '' && $_GET['post_type'] == 'shop_order' ) {

//        $meta_query = $query->get( 'meta_query' );

        $meta_key_query = [
            'meta_key' => 'order_practicum',
//            'value'    => esc_attr( $_GET['filter_shop_order_practicum'] ),
        ];
        $query->set( 'meta_query', $meta_key_query );
    }
}
