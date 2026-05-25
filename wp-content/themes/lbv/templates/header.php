<?php
if (!defined('ABSPATH')) exit;
$lbvSettings = LBV_Theme_Settings::get_instance();
?>
<div class="header-wrapper" id="site-header">
    <header class="header" id="header">
        <div class="header-container">
            <div class="header-left">
                <button class="menu-btn" id="menu-btn" aria-label="Open menu">
                    <span class="burger-icon"><span></span><span></span><span></span></span>
                </button>
                <button class="close-btn" id="close-btn" aria-label="Close menu">
                    <span class="close-icon"><span></span><span></span></span>
                </button>
                <div class="logo-wrap">
                    <?php if (is_front_page()) :?>
                        <h1 class="logo-title is-hidden"><?php bloginfo('name'); ?></h1>
                        <p class="site-description is-hidden"><?php bloginfo('description'); ?></p>
                    <?php endif; ?><?php $lbvSettings->lbv_site_logo(); ?>
                </div>
                <nav class="nav nav-menu">
                    <?php
                    wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'items_wrap' => '%3$s',
                            'walker' => new Lbv_Walker_Nav_Menu()
                    ));
                    ?>
                </nav>
            </div>

            <div class="header-right">
                <div class="user-container">
                    <?php if (is_user_logged_in()) :
                        $current_user = wp_get_current_user();
                        $display_name = $current_user->display_name;
                        $user_email = $current_user->user_email;
                        $logout_url = $lbvSettings->lbv_logout_url();
                        $user_profile_url = get_author_posts_url( $current_user->ID );
                        ?>
                        <button class="icon-btn" id="userBtn" title="<?php echo esc_attr($display_name); ?>">
                            <span class="lbv-welcome"><?php echo get_avatar( $current_user->ID, 22 ); ?></span>
                        </button>
                        <div class="user-dropdown">
                            <div class="user-dropdown-header">
                                <span><a href="<?php echo esc_url( $user_profile_url ); ?>"><?php echo esc_html( $display_name ); ?></a></span>
                                <p><?php echo esc_html($user_email); ?></p>
                            </div>
                            <div class="my-account">
                                <a href="<?php echo home_url('my-account/'); ?>" class="user-dropdown-item">
                                    <span><?php _e('My Account', 'lbv'); ?></span>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </a>
                                <a href="<?php echo home_url('loved-reads/'); ?>" class="user-dropdown-item">
                                    <span><?php _e('Loved Reads', 'lbv'); ?></span>
                                    <span class="loved-reads-icon">❤️</span>
                                </a>
                                <a rel="nofollow" href="<?php echo esc_url($logout_url); ?>" class="user-dropdown-item lbv-logout">
                                    <span><?php _e('Sign Out', 'lbv'); ?></span>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <button class="icon-btn" id="userBtn" title="<?php _e('User', 'lbv'); ?>">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>
                        <div class="user-dropdown">
                            <a rel="nofollow" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="user-dropdown-item">
                                <span><?php _e('Login', 'lbv'); ?></span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </a>
                            <a rel="nofollow" href="<?php echo esc_url(wp_registration_url()); ?>" class="user-dropdown-item">
                                <span><?php _e('Register', 'lbv'); ?></span>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="search-container">
                    <button class="icon-btn" id="searchBtn" title="Search">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <div class="search-box" id="searchBox">
                        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
                            <div class="search-input-wrapper">
                                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" class="search-input header-search" name="s" placeholder="<?php echo __('What are you searching for?', 'lbv'); ?>"
                                       id="searchInput" required minlength="2" aria-label="Search" autocomplete="off">
                                <button type="submit" class="search-submit-btn" aria-label="Search">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8l4 4-4 4"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                        <div class="search-dropdown"></div>
                    </div>
                </div>
                <div class="dark-mode-toggle">
                    <div class="dark-mode-slide-btn mode-icon-dark" data-title="<?= __( 'Switch to Light', 'lbv' ); ?>">
                        <span class="sr-only"><?= __( 'Switch to Light', 'lbv' ); ?></span>
                    </div>
                    <div class="dark-mode-slide-btn mode-icon-default" data-title="<?= __( 'Switch to Dark', 'lbv' ); ?>">
                        <span class="sr-only"><?= __( 'Switch to Dark', 'lbv' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>
