<!DOCTYPE html>
<html <?php language_attributes(); ?> prefix="og: https://ogp.me/ns#">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php if (defined('LBV_CDN_URL') && LBV_CDN_URL !== '') : ?>
<link rel="preconnect" href="<?= esc_url(LBV_CDN_URL) ?>" crossorigin>
<link rel="preload" href="<?= esc_url(LBV_THEME_URI . 'assets/css/main.css?ver=' . LBV_THEME_VERSION); ?>" as="style">
<?php
    if (is_singular(['post'])):
        echo '<link rel="preload" href="' . esc_url(LBV_THEME_URI . 'assets/css/post.css?ver=' . LBV_THEME_VERSION) . '" as="style">' . "\n";
    endif;
endif;
?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open();
get_template_part( 'templates/header', 'header' );
?>