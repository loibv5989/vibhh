<?php

defined('ABSPATH') || exit;

get_header();

global $wp_query;
$search_query = get_search_query();
$result_count = $wp_query->found_posts;
?>

<main class="archive-page search-page">
    <div class="container">
        <div class="archive-header search-header">
            <h1 class="archive-title">
                <?php
                printf(
                    __('Search Results for: %s', 'lbv'),
                    '<span class="search-query">' . esc_html($search_query) . '</span>'
                );
                ?>
            </h1>
            <?php if ($result_count > 0): ?>
                <p class="archive-description">
                    <?php
                    printf(
                        _n('Found %s result', 'Found %s results', $result_count, 'lbv'),
                        '<strong>' . number_format_i18n($result_count) . '</strong>'
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="archive-content">
            <div class="search-form-wrapper">
                <form role="search" method="get" class="search-page-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-group">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="search" class="search-page-input" name="s" value="<?php echo get_search_query(); ?>"
                               placeholder="<?php echo esc_attr_x('What are you searching for?', 'placeholder', 'lbv'); ?>" autocomplete="off" required>
                        <button type="submit" class="search-page-submit" aria-label="Search">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8l4 4-4 4"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="posts-list" id="posts-container">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('templates/single/post-item'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p class="no-posts-found"><?php _e('No posts were found.', 'lbv')?></p>
                <?php endif; ?>
            </div>
            <?php
            global $wp_query;
            if ($wp_query->max_num_pages > 1): ?>
                <?php
                $loadMore = LBV_Theme_Settings::get_instance()->lbv_search_load_more();
                if ($loadMore): ?>
                    <div class="load-more-wrapper">
                        <button type="button" class="load-more-btn"
                                data-page="1"
                                data-max="<?php echo $wp_query->max_num_pages; ?>"
                                data-search="<?php echo esc_attr($search_query); ?>"
                                data-context="search">
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
