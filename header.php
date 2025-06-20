<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package RecruitPro
 * @since 1.0.0
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'recruitpro'); ?></a>

        <header id="masthead" class="site-header" role="banner">
            <?php
            // Get header style from customizer
            $header_style = get_theme_mod('recruitpro_header_style', 'main');
            
            // Include appropriate header template part based on customizer setting
            switch ($header_style) {
                case 'minimal':
                    get_template_part('template-parts/header/header', 'minimal');
                    break;
                    
                case 'centered':
                    get_template_part('template-parts/header/header', 'centered');
                    break;
                    
                case 'sticky':
                    get_template_part('template-parts/header/header', 'sticky');
                    break;
                    
                case 'transparent':
                    get_template_part('template-parts/header/header', 'transparent');
                    break;
                    
                default:
                    get_template_part('template-parts/header/header', 'main');
                    break;
            }
            ?>
        </header><!-- #masthead -->

        <?php
        // Show breadcrumbs on internal pages (not homepage)
        if (!is_front_page()) {
            get_template_part('template-parts/header/breadcrumbs');
        }
        ?>

        <div id="content" class="site-content">