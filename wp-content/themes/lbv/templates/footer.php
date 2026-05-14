<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-wrapper">
            <?php if ( LBV_SOCIAL_ENABLE ) : ?>
            <div class="footer-social">
                <a rel="me noopener" target="_blank" href="<?= esc_url( LBV_SOCIAL_FACEBOOK ); ?>" class="social-link" aria-label="Facebook">
                    <?= lbv_social_icon('facebook'); ?></a>
                <a rel="me noopener" target="_blank" href="<?= esc_url( LBV_SOCIAL_X ); ?>" class="social-link" aria-label="X (Twitter)">
                    <?= lbv_social_icon('x'); ?></a>
                <a rel="me noopener" target="_blank" href="<?= esc_url( LBV_SOCIAL_TIKTOK ); ?>" class="social-link" aria-label="TikTok">
                    <?= lbv_social_icon('tiktok'); ?></a>
                <a rel="me noopener" target="_blank" href="<?= esc_url( LBV_SOCIAL_YOUTUBE ); ?>" class="social-link" aria-label="YouTube">
                    <?= lbv_social_icon('youtube'); ?></a>
                <a rel="me noopener" target="_blank" href="<?= esc_url( LBV_SOCIAL_THREADS ); ?>" class="social-link" aria-label="Threads">
                    <?= lbv_social_icon('threads'); ?></a>
                <a rel="me noopener" target="_blank" href="<?= esc_url( LBV_SOCIAL_INSTAGRAM ); ?>" class="social-link" aria-label="Instagram">
                    <?= lbv_social_icon('instagram'); ?></a>
            </div>
            <?php endif; ?>
            <?php
            $footer_menus = array( 'footer-menu-2' );
            $locations = get_nav_menu_locations();
            $has_menu = false;

            foreach ( $footer_menus as $loc ) {
                if ( has_nav_menu( $loc ) ) {
                    $has_menu = true;
                    break;
                }
            }

            if ( $has_menu ) : ?>
                <div class="footer-links">
                    <?php foreach ( $footer_menus as $loc ) :
                        if ( has_nav_menu( $loc ) ) :
                            $menu_obj = wp_get_nav_menu_object( $locations[ $loc ] );
                            ?>
                            <?php
                            wp_nav_menu( array(
                                    'theme_location' => $loc,
                                    'container'      => false,
                                    'menu_class'     => 'footer-menu',
                                    'walker'         => new Lbv_Walker_Footer_Menu(),
                                    'fallback_cb'    => false,
                                    'depth'          => 1,
                            ) );
                            ?>
                        <?php endif;
                    endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="footer-bottom">
            <div class="copyright">
                <p>© <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
