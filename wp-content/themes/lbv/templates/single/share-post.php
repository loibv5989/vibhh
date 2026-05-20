<?php

defined( 'ABSPATH' ) || exit;

$post_url = urlencode(get_permalink());
$post_title = urlencode(get_the_title());
$theme_url = get_template_directory_uri();

$facebook_url = "https://www.facebook.com/sharer/sharer.php?u=" . $post_url;
$twitter_url = "https://x.com/intent/post?text=" . $post_title . "&url=" . $post_url;
$reddit_url = "https://www.reddit.com/submit?url=" . $post_url . "&title=" . $post_title;
$telegram_url = "https://t.me/share/url?url=" . $post_url . "&text=" . $post_title;
$threads_url = "https://www.threads.net/intent/post?text=" . $post_title . "%20" . $post_url;
?>

<section class="social-share-wrapper">
    <span class="share-label">
         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        <?php echo __('Share', 'lbv') ?>
    </span>
    <div class="social-share-buttons">
        <a href="<?php echo esc_url($reddit_url); ?>" target="_blank" rel="nofollow noopener" class="social-link reddit" aria-label="Share on Reddit">
            <?= lbv_social_icon('reddit'); ?>
        </a>
        <a href="<?php echo esc_url($telegram_url); ?>" target="_blank" rel="nofollow noopener" class="social-link telegram" aria-label="Share on Telegram">
            <?= lbv_social_icon('telegram'); ?>
        </a>

        <a href="<?php echo esc_url($threads_url); ?>" target="_blank" rel="nofollow noopener" class="social-link threads" aria-label="Share on Threads">
            <?= lbv_social_icon('threads'); ?>
        </a>
        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="nofollow noopener" class="social-link facebook" aria-label="Share on Facebook">
            <?= lbv_social_icon('facebook'); ?>
        </a>
        <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="nofollow noopener" class="social-link twitter" aria-label="Share on X">
            <?= lbv_social_icon('x'); ?>
        </a>
    </div>
</section>
<?php echo apply_filters( 'lbv_after_post_content', '' ); ?>