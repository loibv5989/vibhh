<?php
defined('ABSPATH') || exit;
if (post_password_required() || (!comments_open() && !pings_open())) {
    return;
}
?>

<section id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <div class="lbv-comments">
            <div class="comments-title">
                <?php
                $comments_number = get_comments_number();
                if ('1' === $comments_number) {
                    echo __('1 comment', 'lbv');
                } else {
                    printf(__('%s comments', 'lbv'), number_format_i18n($comments_number));
                }
                ?>
            </div>
            <ul class="comment-list">
                <?php
                require_once LBV_THEME_DIR . 'includes/walker-comment.php';
                wp_list_comments(array(
                        'walker'      => new Lbv_Walker_Comment(),
                        'style'       => 'ul',
                        'short_ping'  => true,
                        'avatar_size' => 32,
                        'max_depth'   => 3,
                ));
                ?>
            </ul>
            <?php
            $post_ID = get_the_ID();
            $top_level_comments = get_comments([
                    'post_id' => $post_ID,
                    'parent' => 0,
                    'count' => true,
                    'status' => 'approve',
            ]);

            $comments_per_page = get_option('comments_per_page');
            $total_pages = ceil($top_level_comments / $comments_per_page);
            $c_page = get_query_var('cpage') ? get_query_var('cpage') : 1;

            if ($c_page > 1) :
                ?>
                <button id="load-more-comments" class="load-more-comments" data-post="<?php echo absint($post_ID); ?>" data-cpage="<?php echo absint($c_page); ?>" data-total-pages="<?php echo esc_attr($total_pages); ?>">
                    <?php echo __('Load comments', 'lbv'); ?>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php echo __('Comments are closed.', 'lbv')?></p>
    <?php endif; ?>
    <?php
    comment_form(array(
            'title_reply_before' => '<div id="reply-title" class="comment-reply-title">',
            'title_reply_after'  => '</div>',
            'title_reply' => __('Write a comment', 'lbv'),
            'label_submit' => __('Post comment', 'lbv'),
            'comment_field' => sprintf(
                    '<p class="comment-form-comment"><textarea id="comment" name="comment" placeholder="%s" aria-required="true"></textarea></p>',
                    esc_attr__('Write a comment...', 'lbv')
            ),
            'fields' => array(),
            'comment_notes_before' => '',
            'comment_notes_after' => '',
    ));
    ?>
</section>
