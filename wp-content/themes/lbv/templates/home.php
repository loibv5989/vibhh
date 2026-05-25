<?php
$posts_per_page = LBV_Theme_Settings::get_instance()->lbv_home_post_per_page();
?>
<main class="home-page">
    <section class="search-section">
        <p class="search-header"><?php _e('Hello, what are you looking for?', 'lbv'); ?></p>
        <div class="search-form-wrapper">
            <form role="search" method="get" class="search-page-form" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="search-input-group">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="search" class="search-page-input s-home" name="s" value="<?php echo get_search_query(); ?>" aria-label="Search"
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
    </section>

    <?php echo apply_filters( 'lbv_home_after_featured', '' ); ?>

    <section class="latest-posts latest-posts-blog">
        <h2><?php _e('Latest Posts', 'lbv'); ?></h2>
        <div class="posts-list" id="posts-container-blog">
            <?php
            $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => $posts_per_page,
                    'post_status'    => 'publish',
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'paged'          => 1
            );

            $latest_posts = new WP_Query($args);

            if ($latest_posts->have_posts()) :
                while ($latest_posts->have_posts()) : $latest_posts->the_post();
                    get_template_part('templates/single/post-item', 'home');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <?php if ($latest_posts->max_num_pages > 1): ?>
            <div class="load-more-wrapper">
                <button type="button" class="load-more-btn-post"
                        data-page="1"
                        data-max="<?php echo esc_attr($latest_posts->max_num_pages); ?>"
                        data-context="home"
                        data-archive="0"
                        data-post_type="post"
                        data-target="#posts-container-blog">
                <span class="btn-content">
                    <span class="btn-text"><?php _e('Show more →', 'lbv'); ?></span>
                </span>
                    <span class="btn-loading" style="display:none;">
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
        <?php endif; ?>
    </section>
    <?php echo apply_filters( 'lbv_home_after_loadmore', '' ); ?>
</main>