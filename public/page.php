<?php get_header() ?>
<style>
    #masthead {
        display: none;
    }

    .flex-logo-wrap {
        margin: auto;
    }

    .site-content {
        padding-top: 10px;
        padding-bottom: 0 !important;
    }
    @media (min-width: 768px) {
        .site-content {
            padding-top: 50px;
        }
    }

    .footer-row {
        background: #F9F9F9;
        border-top-color: #E7E7E7;
    }

    .footer-row {
        background: #f9f9f9;
        border-top: 1px solid #e7e7e7;
        color: #929292;
        padding: 14px 0;
        z-index: 500;
    }

    .col-xs-12 {
        width: 100%;
    }

    .footer-row .footer-user-agreement {
        text-align: center;
    }

    p {
        margin: 0 0 10px;
    }

    a {
        font-family: "PT Sans", sans-serif;
        -webkit-transition: all 0.2s ease-out;
        -moz-transition: all 0.2s ease-out;
        -ms-transition: all 0.2s ease-out;
        transition: all 0.2s ease-out;
        text-decoration: none;
        color: #5395bc;
        font-size: 15px;
    }

    .brand-row .flex-logo-wrap span.wpm_default_header .brand-logo, .brand-row .flex-logo-wrap a.wpm_default_header .brand-logo {
        display: block;
        margin: 0 auto;
        width: auto;
        max-width: 100%;
        max-height: 200px;
    }

    .brand-row .flex-logo-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        /*min-height: 200px;*/
    }
    .entry-title {
        text-align: center;
    }
</style>
<div class="container">
    <?php wpm_render_partial('header-cover'); ?>
</div>
<div class="wrap mt-5">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
            while (have_posts()) :
                the_post();

                get_template_part('template-parts/page/content', 'page');

                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

            endwhile; // End the loop.
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->
</div><!-- .wrap -->
<?php wpm_render_partial('footer') ?>
