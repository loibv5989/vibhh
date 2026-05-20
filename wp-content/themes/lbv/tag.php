<?php

defined('ABSPATH') || exit;

get_header();

$tag = get_queried_object();
$tag_id = $tag->term_id;
$tag_name = $tag->name;
$tag_description = $tag->description;
?>

<main class="archive-page">
    <div class="container">
        <?php
        if ( function_exists( 'yoast_breadcrumb' ) ) {
            yoast_breadcrumb( '<nav class="breadcrumbs">', '</nav>' );
        } elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
            rank_math_the_breadcrumbs();
        }
        ?>
        <div class="archive-header">
            <h1 class="archive-title"><?php echo esc_html($tag_name); ?></h1>
            <?php if ($tag_description): ?>
                <p class="archive-description"><?php echo esc_html($tag_description); ?></p>
            <?php endif; ?>
        </div>
        <div class="archive-content">
            <div class="posts-list" id="posts-container">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('templates/single/post-item'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p class="no-posts"><?php _e('No posts found.', 'lbv'); ?></p>
                <?php endif; ?>
            </div>
            <?php
            global $wp_query;
            if ($wp_query->max_num_pages > 1) : ?>
                <?php
                $loadMore = LBV_Theme_Settings::get_instance()->lbv_tag_load_more();
                if ($loadMore): ?>
                    <div class="load-more-wrapper">
                        <button type="button" class="load-more-btn"
                                data-page="1"
                                data-max="<?php echo $wp_query->max_num_pages; ?>"
                                data-archive="<?php echo $tag_id; ?>"
                                data-context="tag">
                            <span class="btn-content">
                                <span class="btn-text"><?php _e('Show more →', 'lbv'); ?></span>
                            </span>
                            <span class="btn-loading" style="display: none;">
                            <svg class="spinner" width="24" height="24" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none"
                                        stroke-linecap="round" stroke-dasharray="60" stroke-dashoffset="60">
                                    <animate attributeName="stroke-dashoffset" dur="1.5s" repeatCount="indefinite" from="60" to="0"/>
                                    <animateTransform attributeName="transform" type="rotate" dur="2s" repeatCount="indefinite" from="0 12 12" to="360 12 12"/>
                                </circle>
                            </svg>
                            <span><?php _e('Loading...', 'lbv'); ?></span>
                        </span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="pagination">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '← ',
                            'next_text' => ' →',
                            'type' => 'list',
                            'mid_size' => 1,
                            'end_size' => 1,
                        ));
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
