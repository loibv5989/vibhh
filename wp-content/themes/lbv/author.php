<?php

defined( 'ABSPATH' ) || exit;

get_header();
$author = get_queried_object();
$author_id = $author->ID;
$author_name = $author->display_name;
$author_bio = get_the_author_meta('description', $author_id);
$lbvSettings = LBV_Theme_Settings::get_instance();
?>
<main class="author-page">
    <div class="container">
        <div class="author-layout">
            <div class="author-header">
                <div class="author-avatar">
                    <?php echo get_avatar($author_id, 100); ?>
                </div>
                <div class="author-info">
                    <h1 class="author-name">
                        <?php echo esc_html($author_name); ?>

                        <?php if (get_the_author_meta('user_url', $author_id)) : ?>
                            <span class="verified-badge">
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708z"/>
                                </svg>
                            </span>
                        <?php endif; ?>
                    </h1>

                    <?php if ($author_bio) : ?>
                        <p class="author-bio"><?php echo esc_html($author_bio); ?></p>
                    <?php endif; ?>

                    <?php
                    $twitter  = get_user_meta( $author_id, 'twitter', true );
                    $facebook = get_user_meta( $author_id, 'facebook', true );

                    if ( $facebook || $twitter ) : ?>
                        <div class="author-social">
                            <span class="follow-label"><?php _e('FOLLOW:'); ?></span>

                            <?php if ( $facebook ) : ?>
                                <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ( $twitter ) : ?>
                                <a href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener" aria-label="Twitter">
                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.657l-5.214-6.817-5.966 6.817h-3.31l7.73-8.835-8.156-10.465h6.826l4.713 6.231 5.48-6.231zm-1.161 17.52h1.833l-7.977-10.544h-1.97l7.977 10.544z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
            <div class="author-content">
                <?php if (have_posts()) : ?>
                    <div class="posts-list">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : the_post();
                                get_template_part('templates/single/post-item', 'author');
                             endwhile; ?>
                        <?php else : ?>
                            <p class="no-posts"><?php _e('No posts found.', 'lbv'); ?></p>
                        <?php endif; ?>
                    </div>
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
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
