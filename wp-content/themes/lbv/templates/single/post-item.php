<?php
$is_home = isset($args['is_home']) ? $args['is_home'] : false;
$heading = ($is_home || is_front_page()) ? 'h3' : 'h2';
$post_id = get_the_ID();
?>
<article class="post-item">
    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php
                $thumbnail = get_the_post_thumbnail(null, 'medium');
                $thumbnail = preg_replace('/(width|height|sizes|srcset)="[^"]*"\s*/', '', $thumbnail);
                echo $thumbnail;
                ?>
            </a>
        </div>
        <?php if ( current_user_can('administrator') ) : ?>
            <a href="<?php echo get_edit_post_link( $post_id ); ?>" class="post-edit-link"><?php echo __('Edit', 'lbv')?></a>
        <?php endif; ?>
    <?php endif; ?>
    <div class="post-content <?= ($is_home || is_front_page()) ? 'idol-content' : '' ?>">
        <<?php echo $heading; ?> class="post-title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </<?php echo $heading; ?>>
        <?php
        if (get_post_type() === 'post') : ?>
        <div class="post-meta">
            <span class="post-date">
                <?php echo apply_filters( 'lbv_modified_date', get_the_modified_date('', $post_id), $post_id ); ?>
            </span>
        </div>
        <?php else: $helper = Idols_Helper::get_instance();
            echo $helper->get_profile_summary_sentence($post_id);
            echo $helper->get_group_meta_html( $post_id );
        ?>
        <?php endif;?>
    </div>
</article>