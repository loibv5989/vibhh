<?php

class Lbv_Walker_Comment extends Walker_Comment {

    protected function html5_comment($comment, $depth, $args) {
        $tag = 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, $args['avatar_size']); ?>
            </div>
            <div class="comment-content-wrapper">
                <div class="comment-bubble">
                    <div class="comment-author-name">
                        <?php
                        $author_name = get_comment_author( $comment );
                        $user = get_userdata( $comment->user_id );
                        ?>
                        <strong class="fn">
                            <?php if ( $user ) : ?>
                                <a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>">
                                    <?php echo esc_html( $author_name ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html__( 'Anonymous User', 'lbv' ); ?>
                            <?php endif; ?>
                        </strong>
                    </div>
                    <div class="comment-content">
                        <?php comment_text(); ?>
                    </div>
                </div>

                <div class="comment-meta">
                    <time datetime="<?php comment_date('c'); ?>">
                        <?php
                        $time_diff = human_time_diff(get_comment_time('U'), current_time('timestamp'));
                        printf(__('%s ago', 'lbv'), $time_diff);
                        ?>
                    </time>
                    <span class="meta-separator">·</span>
                    <?php
                    comment_reply_link(array_merge($args, array(
                        'add_below' => 'comment',
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                        'reply_text' => __('Reply', 'lbv'),
                        'before'    => '',
                        'after'     => '',
                    )));
                    ?>

                    <?php if (is_user_logged_in() && get_current_user_id() === intval($comment->user_id)) : ?>
                        <span class="meta-separator">·</span>
                        <a href="#" class="edit-comment-btn" data-editing="1" data-commentid="<?php echo esc_attr($comment->comment_ID); ?>"><?php echo __(' [Edit]', 'lbv') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
