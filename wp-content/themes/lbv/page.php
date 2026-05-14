<?php

defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="single-page">
    <div class="content-wrapper">
        <main id="primary" class="content-area">
            <?php
            while ( have_posts() ) : the_post();
                ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                    $title = apply_filters('lbv_header_title', get_the_title(), get_the_ID());
                    $description = apply_filters('lbv_page_header_description', '', get_the_ID());

                    if (!empty($title)) :
                        ?>
                        <header class="page-header">
                            <?php if (!empty($title)) : ?>
                                <h1 class="page-title"><?php echo esc_html($title); ?></h1>
                            <?php endif; ?>

                            <?php if (!empty($description)) : ?>
                                <p class="page-description"><?php echo wp_kses_post($description); ?></p>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <div class="page-content">
                        <?php the_content(); ?>
                    </div>

                    <?php if ( comments_open() || get_comments_number() ) : ?>
                        <?php comments_template(); ?>
                    <?php endif; ?>
                </div>
            <?php
            endwhile;
            ?>
        </main>
    </div>
</div>

<?php get_footer(); ?>
