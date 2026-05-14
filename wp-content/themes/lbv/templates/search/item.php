<div class="search-result-item">
    <div class="result-content">
        <div class="result-meta">
            <span class="result-author">
                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"
                   title="<?php echo esc_attr(get_the_author()); ?>">
                    <?php echo get_avatar(get_the_author_meta('ID'), 20, '', '', array('class' => 'author-avatar')); ?>
                    <span class="author-name"><?php echo esc_html(get_the_author()); ?></span>
                </a>
            </span>
        </div>

        <h2 class="result-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <div class="result-excerpt">
            <?php
            $excerpt = get_the_excerpt();
            $content = $excerpt ? $excerpt : get_the_content();
            echo wp_trim_words($content, 25, '...');
            ?>
        </div>

        <?php
        $tags = $this->get_limited_tags();
        if ($tags) :
            ?>
            <div class="result-tags">
                <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-item">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="result-date">
            <?php
            $time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
            printf(__('%s ago', 'lbv'), $time_diff);
            ?>
        </div>
    </div>

    <?php if (has_post_thumbnail()) : ?>
        <div class="result-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium', array('class' => 'result-image')); ?>
            </a>
        </div>
    <?php endif; ?>
</div>