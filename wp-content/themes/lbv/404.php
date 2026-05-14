<?php
get_header();
?>

<div class="error-404-wrapper">
    <div class="error-404-container">
        <div class="error-404-image">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/404.webp" alt="404 Error" />
        </div>
        <h1 class="error-404-title"><?php _e("Something's wrong here...", 'lbv'); ?></h1>
        <p class="error-404-description">
            <?php _e('It looks like nothing was found at this location. The page you were looking for does not exist or was loading incorrectly.', 'lbv'); ?>
        </p>
        <div class="error-404-search">
            <div class="search-form-wrapper">
                <form role="search" method="get" class="search-page-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-group">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="search" class="search-page-input" name="s" value="<?php echo get_search_query(); ?>"
                               placeholder="<?php echo esc_attr_x('What are you searching for?', 'placeholder', 'lbv'); ?>" autocomplete="off" required>
                        <button type="submit" class="search-page-submit" aria-label="Search">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8l4 4-4 4"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="error-404-button">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-return-home"><?php _e('Return to Home', 'lbv');?></a>
        </div>
    </div>
</div>

<?php
get_footer();
?>
