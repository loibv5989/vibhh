<?php
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="site-wrap">
    <div class="container">
        <?php
        if ( function_exists( 'yoast_breadcrumb' ) ) {
            yoast_breadcrumb( '<nav class="breadcrumbs">', '</nav>' );
        } elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
            rank_math_the_breadcrumbs();
        }
        ?>
        <div class="content-wrapper">
            <?php echo lbv_table_of_contents(); ?>
            <main id="primary" class="content-area">
                <?php
                while ( have_posts() ) : the_post();
                    $post_id = get_the_ID();
                    $title = apply_filters( 'lbv_header_title', get_the_title(), $post_id );

                    $special_post_types = ['idol', 'group', 'photo', 'actor', 'v_star'];
                    $current_post_type  = get_post_type();

                    $header_classes = ['entry-header'];
                    if ( in_array( $current_post_type, $special_post_types, true ) ) {
                        $header_classes[] = 'lbv-header';
                    }

                    ?>
                    <article id="<?php echo esc_attr( get_post_type() . '-' . $post_id ); ?>" <?php post_class(); ?>>
                        <header class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>">
                            <h1 class="entry-title"><?php echo esc_html( $title ); ?></h1>

                            <?php if ( 'post' === $current_post_type ) : ?>
                                <div class="entry-meta">
                                    <?php
                                    $author_id = get_the_author_meta( 'ID' );
                                    $author    = get_userdata( $author_id );
                                    $is_admin  = in_array( 'administrator', (array) $author->roles, true );
                                    ?>

                                    <span class="<?php echo $is_admin ? 'admin' : 'author'; ?> meta-review">
                                        <?php esc_html_e( 'Last updated:', 'lbv' ); ?>
                                        <span class="meta-line">
                                            <span class="date">
                                                <?php echo esc_html( apply_filters( 'lbv_modified_date', get_the_modified_date( '', $post_id ), $post_id ) ); ?>
                                            </span>
                                            <?php esc_html_e( 'by', 'lbv' ); ?>
                                            <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"
                                               title="<?php echo esc_attr( sprintf( __( 'View all content by %s', 'lbv' ), get_the_author() ) ); ?>">
                                                <span><?php echo esc_html( get_the_author() ); ?></span>
                                            </a>
                                        </span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>

                        <footer class="entry-footer">
                            <?php $tags = get_the_tags();
                            if ( $tags && ! is_wp_error( $tags ) ) :
                                $tags = array_slice( $tags, 0, 8 );
                                ?>
                                <section class="post-tags">
                                    <span class="tags-label">TAGGED:</span>
                                    <?php
                                    $tag_links = [];
                                    foreach ( $tags as $tag ) {
                                        $hashtag = preg_replace('/[^\p{L}\p{N}]/u', '', ucwords($tag->name));
                                        $tag_links[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" rel="tag">#' . esc_html( $hashtag ) . '</a>';
                                    }
                                    echo implode( ', ', $tag_links );
                                    ?>
                                </section>
                            <?php endif; ?>

                            <?php if ( in_array( $current_post_type, $special_post_types, true ) ) : ?>
                                <section class="entry-meta lbv-meta">
                                    <div class="post-meta">
                                        <?php
                                        $author_id = get_the_author_meta( 'ID' );
                                        $author    = get_userdata( $author_id );
                                        $is_admin  = in_array( 'administrator', (array) $author->roles, true );
                                        ?>
                                        <span class="<?php echo $is_admin ? 'admin' : 'author'; ?> meta-review">
                                        <?php esc_html_e( 'Last reviewed on', 'lbv' ); ?>
                                        <span class="date">
                                            <?php echo esc_html( apply_filters( 'lbv_modified_date', get_the_modified_date( '', $post_id ), $post_id ) ); ?>
                                        </span>

                                        <span class="meta-line">
                                            <?php esc_html_e( 'by', 'lbv' ); ?>
                                            <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"
                                               title="<?php echo esc_attr( sprintf( __( 'View all content by %s', 'lbv' ), get_the_author() ) ); ?>">
                                                <span><?php echo esc_html( get_the_author() ); ?></span>
                                            </a>
                                        </span>
                                    </span>
                                    </div>
                                </section>
                            <?php endif; ?>
                        </footer>

                        <?php get_template_part('templates/single/share-post', 'share-post' ); ?>
                        <?php apply_filters('lbv_post_after_footer', $post_id); ?>

                        <?php
                        if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;
                        ?>
                    </article>
                <?php endwhile; ?>
            </main>

            <aside id="sidebar-wrap" class="sidebar-area">
                <?php get_sidebar(); ?>
            </aside>
        </div>
    </div>
</div>
<?php get_footer(); ?>
