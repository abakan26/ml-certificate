<?php
function events_shortcode_callback($atts)
{
    $params = [
        'post_type' => 'product',
        'order' => 'ASC',
        'orderBy' => 'ID',
        'tax_query' => [
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => '249'
        ]

    ];
    // 'post__in' => [4382, 4383, 4384],
    $query = new WP_Query($params);
    ob_start();
    ?>
    <style>
        @media (max-width: 575px) {
            .entry-header .entry-title {
                margin-bottom: 30px;
            }
        }
        body.page:not(.twentyseventeen-front-page) .entry-title {
            font-size: 22px;
        }

        @media (min-width: 768px) {
            body.page:not(.twentyseventeen-front-page) .entry-title {
                font-size: 27px;
            }
        }

        .certificate-search-result th, .certificate-search-result td {
            padding: 0.25rem;
            font-size: 14px;
        }

        .page.page-one-column:not(.twentyseventeen-front-page) #primary {
            max-width: unset;
        }
        @media (max-width: 575px) {
            .default-page-wrap .editor-entry {
                padding: 15px;
            }
        }
        .events_row {
            display: flex;
            flex-wrap: wrap;
        }
        .events_col {
            order: 1;
            margin-bottom: 45px;
        }
        .tickets {
            flex: 0 0 100%;
            max-width: 100%;
            order: 2;
        }

        .ticket {
            position: relative;
            border: 1px solid #EAEAEA;
            background: #FAFAFA;
            border-radius: 5px;
            padding: 14px 20px;
            display: flex;
            flex-wrap: wrap;
            box-sizing: border-box;
            color: #3f3f3f;
            justify-content: space-between;
            align-items: center;
            max-width: 760px;
            margin-bottom: 45px;
            transition: 0.3s ease-in;
        }

        .ticket:hover {
            box-shadow: 0 2px 8px 0 rgba(50, 50, 50, 0.08);
        }

        .ticket__column {
            flex: 0 0 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .ticket__name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .ticket__description {
            font-size: 14px;
            margin: 0 0 10px;
        }

        @media (min-width: 768px) {
            .ticket {
                align-items: flex-start;
            }

            .ticket__column {
                flex: 0 0 60%;
                max-width: 60%;
            }

            .ticket__name {
                font-size: 18px;
            }

            .ticket__description {
                font-size: 16px;
                margin: 0;
            }
        }

        .ticket__price {
            font-size: 18px;
        }

        .ticket__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            font-size: 18px;
            font-weight: 400;
            color: #FFFFFF!important;
            background-color: #3F79DD;
            border: none;
            height: 42px;
            border-radius: 4px;
            box-shadow: none !important;
            box-sizing: border-box;
            text-decoration: none!important;
        }

        .ticket__btn:hover {
            color: #FFFFFF !important;
            background-color: #336CDD;
            text-decoration: none!important;
            box-shadow: none !important;
        }

        .ticket__badge {
            position: absolute;
            top: -25px;
            left: 0;
            display: inline-block;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            color: #fff;
            background-color: #88b9ef;
        }

        .ticket__btn_disabled {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
    <div class="tickets" style="display: none;">
        <?php while ($query->have_posts()): ?>
            <?php
            $query->the_post();
            global $post;
            $product = wc_get_product(get_the_ID());
            $count = get_post_meta($post->ID, 'event_stock_field', true);
            $has_ticket = get_post_meta($post->ID, 'all_ticket_field', true);
            ?>

            <?php if ($has_ticket): ?>
                <div class="ticket">
                    <span class="ticket__badge">
                        Осталось: <span style="color:<?= $count < 4 ? '#ec0e0e' : '#fff' ?>"><?= $count ?></span>
                    </span>
                    <div class="ticket__column">
                        <p class="ticket__name">
                            <?= $product->get_title() ?>
                        </p>
                        <div class="ticket__description">
                            <?= $product->get_description() ?>
                        </div>
                    </div>
                    <div>
                        Цена:
                        <b class="ticket__price">
                            <?= $product->get_price() ?> руб.
                        </b>
                    </div>
                    <div>
                        <a href="<?= $count ? get_home_url() . "?add-to-cart={$post->ID}" : '#' ?>"
                           class="ticket__btn<?php if (!$count): ?> ticket__btn_disabled<?php endif; ?>"
                           target="_blank"
                        >
                            Оплатить
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>
    <script>
        (function ($) {
            const AJAX_URL = "/wp-admin/admin-ajax.php";
        })(jQuery);
        (function ($) {
            $(document).ready(function (event) {
                $('.events_row').append($('.tickets'));
                $('.tickets').show();
            });
        })(jQuery)
    </script>
    <?php
    return ob_get_clean();
}
