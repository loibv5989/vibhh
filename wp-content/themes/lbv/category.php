<?php

defined('ABSPATH') || exit;
get_header();

$category = get_queried_object();
$category_id   = $category->term_id;
$category_name = $category->name;
$category_description = $category->description;

$settings = LBV_Theme_Settings::get_instance();
$loadMore = $settings->lbv_category_load_more();

$is_profile = $settings->is_profile($category);

$archive_classes = ['archive-content'];
$posts_list = ['posts-list'];
if ($is_profile) {
    $archive_classes[] = 'profile-archive';
    $posts_list[] = 'profile-list';
}
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
            <h1 class="archive-title"><?php echo esc_html($category_name); ?></h1>
            <?php if ($category_description): ?>
                <p class="archive-description"><?php echo esc_html($category_description); ?></p>
            <?php endif; ?>
            <?php
            $term = get_queried_object();
            $children = get_terms([
                    'taxonomy'   => 'category',
                    'parent'     => $term->term_id,
                    'hide_empty' => true,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
            ]);
            ?>
            <?php if ( ! is_wp_error( $children ) && ! empty( $children ) ) : ?>
                <div class="subcategories">
                    <span class="sub-heading">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 7a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/>
                        </svg>
                        <?php esc_html_e('Filter by: ', 'lbv'); ?></span>
                    <?php foreach ( $children as $child ) : ?>
                    <span class="sub-item">
                        <a href="<?php echo esc_url( get_term_link( $child ) ); ?>">
                            <?php echo esc_html( $child->name ); ?>
                        </a>
                    </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="<?php echo esc_attr(implode(' ', $archive_classes)); ?>">

        <?php if (have_posts()) : ?>
            <div class="<?php echo esc_attr(implode(' ', $posts_list)); ?>" id="posts-container">
                <?php while (have_posts()) : the_post();
                    get_template_part(
                            'templates/single/post-item',
                            'archive',
                            [
                                    'category_profile' => $is_profile
                            ]
                    );
                    ?>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="no-posts"><?php _e('No posts found.', 'lbv'); ?></p>
        <?php endif; ?>

        <?php global $wp_query;
        if ($wp_query->max_num_pages > 1): ?>
            <?php if ($loadMore): ?>
                <div class="load-more-wrapper">
                    <button type="button" class="load-more-btn"
                            data-page="1"
                            data-max="<?php echo $wp_query->max_num_pages; ?>"
                            data-archive="<?php echo $category_id; ?>"
                            data-context="category">
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
