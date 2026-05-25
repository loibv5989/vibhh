<?php

defined( 'ABSPATH' ) || exit;

$post_id    = get_the_ID();
$categories = get_the_category();
$category   = $categories ? $categories[0] : null;
$widget_title = __('Related Post', 'lbv');
?>
<div class="sidebar">
    <div class="widget popular-posts">
        <div class="widget-title"><?php echo __($widget_title, 'lbv');?></div>
        <div id="popular-posts-container">
            <?php

            $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $posts_per_page = 5;

            $post_tags = wp_get_post_tags($post_id);
            $post_categories = wp_get_post_categories($post_id);
            $post_type  = get_post_type($post_id);

            $query_args = array(
                    'post_type' => $post_type,
                    'posts_per_page' => $posts_per_page,
                    'orderby' => 'comment_count',
                    'order' => 'DESC',
                    'paged' => $paged,
                    'post__not_in' => array($post_id)
            );

            if (!empty($post_tags)) {

                $tag_ids = array();
                foreach ($post_tags as $tag) {
                    if ($tag->count > 1) {
                        $tag_ids[] = $tag->term_id;
                    }
                }

                if (!empty($tag_ids)) {
                    $query_args['post_type'] = ['post'];
                    $query_args['tag__in'] = $tag_ids;
                }
            }

            elseif (!empty($post_categories)) {
                $query_args['category__in'] = $post_categories;
            }

            $popular_posts = new WP_Query($query_args);

            if ($popular_posts->have_posts()) :
                while ($popular_posts->have_posts()) : $popular_posts->the_post();
                    $idol_id = get_the_ID();
                    $post_type = get_post_type($idol_id);
                    ?>
                    <div class="popular-post-item">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="post-info">
                            <div class="post-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                            <div class="post-meta">
                                <?= get_the_modified_date(); ?>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            endif;
            ?>
        </div>

        <?php if ($popular_posts->max_num_pages > 1) : ?>
            <div class="popular-posts-nav" data-current-page="<?php echo $paged; ?>" data-max-pages="<?php echo $popular_posts->max_num_pages; ?>"
                 data-reference-post-id="<?php echo $post_id; ?>"
                 data-post-type="<?php echo esc_attr(get_post_type($post_id)); ?>">
                <button class="prev-popular" <?php echo ($paged <= 1) ? 'disabled' : ''; ?> aria-label="Previous">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button class="next-popular" <?php echo ($paged >= $popular_posts->max_num_pages) ? 'disabled' : ''; ?> aria-label="Next">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</div>