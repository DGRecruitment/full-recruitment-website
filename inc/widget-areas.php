<?php
/**
 * RecruitPro Theme Widget Areas Registration
 *
 * This file handles the registration and management of all widget areas (sidebars)
 * for the RecruitPro recruitment website theme. It includes strategic widget placements
 * for recruitment agencies, HR consultancies, and professional service providers.
 * Each widget area is optimized for specific content types and user interactions.
 *
 * @package RecruitPro
 * @subpackage Theme/Widgets
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/widget-areas.php
 * Purpose: Widget area registration and management
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * WIDGET AREAS REGISTRATION
 * =================================================================
 * 
 * Register all widget areas for the RecruitPro theme.
 * Organized by location and purpose for recruitment websites.
 */

/**
 * Register widget areas
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_register_widget_areas() {
    
    // =================================================================
    // PRIMARY SIDEBAR AREAS
    // =================================================================
    
    // Main Sidebar - Primary content sidebar
    register_sidebar(array(
        'name'          => __('Main Sidebar', 'recruitpro'),
        'id'            => 'sidebar-main',
        'description'   => __('Primary sidebar area for blog posts, pages, and general content. Perfect for search widgets, recent posts, and company information.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget sidebar-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title sidebar-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'main-sidebar-widget',
    ));

    // Jobs Sidebar - Job listings and career pages
    register_sidebar(array(
        'name'          => __('Jobs Sidebar', 'recruitpro'),
        'id'            => 'sidebar-jobs',
        'description'   => __('Sidebar for job listings and career-related pages. Ideal for job search filters, featured jobs, and recruitment CTAs.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget jobs-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title jobs-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'jobs-sidebar-widget',
    ));

    // Services Sidebar - Service pages and consultation areas
    register_sidebar(array(
        'name'          => __('Services Sidebar', 'recruitpro'),
        'id'            => 'sidebar-services',
        'description'   => __('Sidebar for recruitment services pages. Perfect for service highlights, testimonials, and contact forms.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget services-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title services-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'services-sidebar-widget',
    ));

    // =================================================================
    // HOMEPAGE WIDGET AREAS
    // =================================================================
    
    // Homepage Hero Section
    register_sidebar(array(
        'name'          => __('Homepage Hero', 'recruitpro'),
        'id'            => 'homepage-hero',
        'description'   => __('Hero section widgets for the homepage. Use for main call-to-action, search forms, or featured content.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget hero-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title hero-widget-title">',
        'after_title'   => '</h2>',
        'class'         => 'hero-section-widget',
    ));

    // Homepage Features Section
    register_sidebar(array(
        'name'          => __('Homepage Features', 'recruitpro'),
        'id'            => 'homepage-features',
        'description'   => __('Features section below the hero. Showcase your recruitment services, benefits, or company highlights.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget features-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title features-widget-title">',
        'after_title'   => '</h2>',
        'class'         => 'features-section-widget',
    ));

    // Homepage Statistics
    register_sidebar(array(
        'name'          => __('Homepage Statistics', 'recruitpro'),
        'id'            => 'homepage-stats',
        'description'   => __('Statistics section for homepage. Display success metrics, placement rates, client numbers, and achievements.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget stats-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title stats-widget-title">',
        'after_title'   => '</h2>',
        'class'         => 'stats-section-widget',
    ));

    // Homepage Testimonials
    register_sidebar(array(
        'name'          => __('Homepage Testimonials', 'recruitpro'),
        'id'            => 'homepage-testimonials',
        'description'   => __('Client and candidate testimonials section. Build trust with success stories and positive feedback.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget testimonials-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title testimonials-widget-title">',
        'after_title'   => '</h2>',
        'class'         => 'testimonials-section-widget',
    ));

    // Homepage Call-to-Action
    register_sidebar(array(
        'name'          => __('Homepage CTA', 'recruitpro'),
        'id'            => 'homepage-cta',
        'description'   => __('Final call-to-action section before footer. Encourage visitors to contact you or apply for jobs.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget cta-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title cta-widget-title">',
        'after_title'   => '</h2>',
        'class'         => 'cta-section-widget',
    ));

    // =================================================================
    // FOOTER WIDGET AREAS
    // =================================================================
    
    // Get number of footer columns from customizer
    $footer_columns = get_theme_mod('recruitpro_footer_widget_columns', 4);
    $footer_columns = max(1, min(6, intval($footer_columns))); // Ensure between 1-6
    
    // Register footer widget areas dynamically
    for ($i = 1; $i <= $footer_columns; $i++) {
        register_sidebar(array(
            'name'          => sprintf(__('Footer Column %d', 'recruitpro'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(__('Footer widget area %d. Perfect for contact information, links, social media, or company details.', 'recruitpro'), $i),
            'before_widget' => '<section id="%1$s" class="widget footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title footer-widget-title">',
            'after_title'   => '</h3>',
            'class'         => 'footer-column-widget',
        ));
    }

    // Footer Bottom - Above copyright
    register_sidebar(array(
        'name'          => __('Footer Bottom', 'recruitpro'),
        'id'            => 'footer-bottom',
        'description'   => __('Bottom footer area above copyright. Ideal for additional links, certifications, or legal information.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget footer-bottom-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="widget-title footer-bottom-widget-title">',
        'after_title'   => '</h4>',
        'class'         => 'footer-bottom-widget',
    ));

    // =================================================================
    // SPECIALIZED RECRUITMENT WIDGET AREAS
    // =================================================================
    
    // Job Search Widget Area
    register_sidebar(array(
        'name'          => __('Job Search Area', 'recruitpro'),
        'id'            => 'job-search-area',
        'description'   => __('Dedicated area for job search functionality. Use for search forms, filters, and quick job category links.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget job-search-widget %2$s" role="search">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title job-search-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'job-search-widget',
    ));

    // Featured Jobs Widget Area
    register_sidebar(array(
        'name'          => __('Featured Jobs', 'recruitpro'),
        'id'            => 'featured-jobs',
        'description'   => __('Showcase featured or urgent job openings. Perfect for highlighting premium positions and hot opportunities.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget featured-jobs-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title featured-jobs-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'featured-jobs-widget',
    ));

    // Client Showcase
    register_sidebar(array(
        'name'          => __('Client Showcase', 'recruitpro'),
        'id'            => 'client-showcase',
        'description'   => __('Display client logos, success stories, or company partnerships. Build credibility and trust.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget client-showcase-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title client-showcase-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'client-showcase-widget',
    ));

    // Industry Expertise
    register_sidebar(array(
        'name'          => __('Industry Expertise', 'recruitpro'),
        'id'            => 'industry-expertise',
        'description'   => __('Highlight your recruitment specializations and industry expertise. Perfect for sector-specific content.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget industry-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title industry-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'industry-expertise-widget',
    ));

    // =================================================================
    // CONTENT-SPECIFIC WIDGET AREAS
    // =================================================================
    
    // Blog Sidebar - Specific to blog posts
    register_sidebar(array(
        'name'          => __('Blog Sidebar', 'recruitpro'),
        'id'            => 'blog-sidebar',
        'description'   => __('Sidebar specifically for blog posts and archives. Include related posts, categories, and recruitment insights.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget blog-sidebar-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title blog-sidebar-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'blog-sidebar-widget',
    ));

    // About Page Sidebar
    register_sidebar(array(
        'name'          => __('About Page Sidebar', 'recruitpro'),
        'id'            => 'about-sidebar',
        'description'   => __('Sidebar for about and company information pages. Showcase team, awards, certifications, and company values.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget about-sidebar-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title about-sidebar-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'about-sidebar-widget',
    ));

    // Contact Page Sidebar
    register_sidebar(array(
        'name'          => __('Contact Page Sidebar', 'recruitpro'),
        'id'            => 'contact-sidebar',
        'description'   => __('Sidebar for contact pages. Include office locations, contact methods, business hours, and additional contact forms.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget contact-sidebar-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title contact-sidebar-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'contact-sidebar-widget',
    ));

    // =================================================================
    // MOBILE-SPECIFIC WIDGET AREAS
    // =================================================================
    
    // Mobile Header Widget
    register_sidebar(array(
        'name'          => __('Mobile Header', 'recruitpro'),
        'id'            => 'mobile-header',
        'description'   => __('Widget area for mobile header. Include quick actions, contact buttons, or urgent job alerts for mobile users.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget mobile-header-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="widget-title mobile-header-widget-title">',
        'after_title'   => '</h4>',
        'class'         => 'mobile-header-widget',
    ));

    // Mobile Sticky CTA
    register_sidebar(array(
        'name'          => __('Mobile Sticky CTA', 'recruitpro'),
        'id'            => 'mobile-sticky-cta',
        'description'   => __('Sticky call-to-action area for mobile devices. Perfect for persistent contact buttons or application forms.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget mobile-sticky-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h5 class="widget-title mobile-sticky-widget-title sr-only">',
        'after_title'   => '</h5>',
        'class'         => 'mobile-sticky-widget',
    ));

    // =================================================================
    // OFFLINE/FALLBACK WIDGET AREAS
    // =================================================================
    
    // Offline Content Area
    register_sidebar(array(
        'name'          => __('Offline Content', 'recruitpro'),
        'id'            => 'offline-content',
        'description'   => __('Content area for offline or maintenance mode. Include essential contact information and updates.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget offline-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title offline-widget-title">',
        'after_title'   => '</h2>',
        'class'         => 'offline-content-widget',
    ));

    // 404 Error Page Sidebar
    register_sidebar(array(
        'name'          => __('404 Error Sidebar', 'recruitpro'),
        'id'            => 'error-404-sidebar',
        'description'   => __('Sidebar for 404 error pages. Help users find what they\'re looking for with popular jobs and navigation.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget error-404-widget %2$s" role="complementary">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title error-404-widget-title">',
        'after_title'   => '</h3>',
        'class'         => 'error-404-widget',
    ));

    // =================================================================
    // ACTION HOOK FOR ADDITIONAL WIDGET AREAS
    // =================================================================
    
    /**
     * Allow plugins and child themes to register additional widget areas
     *
     * @since 1.0.0
     */
    do_action('recruitpro_register_additional_widget_areas');
}

// Register widget areas on widgets_init
add_action('widgets_init', 'recruitpro_register_widget_areas');

/**
 * =================================================================
 * WIDGET AREA HELPER FUNCTIONS
 * =================================================================
 */

/**
 * Check if a widget area has active widgets
 *
 * @since 1.0.0
 * @param string $widget_area Widget area ID
 * @return bool True if has active widgets
 */
function recruitpro_has_widgets($widget_area) {
    return is_active_sidebar($widget_area);
}

/**
 * Display widget area with fallback content
 *
 * @since 1.0.0
 * @param string $widget_area Widget area ID
 * @param array $args Optional arguments
 * @return void
 */
function recruitpro_display_widget_area($widget_area, $args = array()) {
    $defaults = array(
        'before_area' => '',
        'after_area' => '',
        'fallback_content' => '',
        'show_if_empty' => false,
        'wrapper_class' => '',
    );

    $args = wp_parse_args($args, $defaults);

    // Check if widget area exists and has widgets
    if (!recruitpro_has_widgets($widget_area)) {
        if (!$args['show_if_empty'] && empty($args['fallback_content'])) {
            return;
        }
    }

    // Output wrapper start
    if (!empty($args['before_area'])) {
        echo $args['before_area'];
    }

    if (!empty($args['wrapper_class'])) {
        echo '<div class="' . esc_attr($args['wrapper_class']) . '">';
    }

    // Display widgets or fallback
    if (recruitpro_has_widgets($widget_area)) {
        dynamic_sidebar($widget_area);
    } elseif (!empty($args['fallback_content'])) {
        echo $args['fallback_content'];
    }

    // Output wrapper end
    if (!empty($args['wrapper_class'])) {
        echo '</div>';
    }

    if (!empty($args['after_area'])) {
        echo $args['after_area'];
    }
}

/**
 * Get widget area configuration
 *
 * @since 1.0.0
 * @param string $widget_area Widget area ID
 * @return array|false Widget area config or false if not found
 */
function recruitpro_get_widget_area_config($widget_area) {
    global $wp_registered_sidebars;
    
    if (isset($wp_registered_sidebars[$widget_area])) {
        return $wp_registered_sidebars[$widget_area];
    }
    
    return false;
}

/**
 * Display footer widget areas in responsive grid
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_display_footer_widgets() {
    $footer_columns = get_theme_mod('recruitpro_footer_widget_columns', 4);
    $active_widgets = array();
    
    // Check which footer widgets are active
    for ($i = 1; $i <= $footer_columns; $i++) {
        if (recruitpro_has_widgets('footer-' . $i)) {
            $active_widgets[] = 'footer-' . $i;
        }
    }
    
    if (empty($active_widgets)) {
        return;
    }
    
    $widget_count = count($active_widgets);
    $grid_class = 'footer-widgets-grid footer-widgets-' . $widget_count . '-cols';
    
    echo '<div class="footer-widgets-area">';
    echo '<div class="container">';
    echo '<div class="' . esc_attr($grid_class) . '">';
    
    foreach ($active_widgets as $widget_area) {
        echo '<div class="footer-widget-column">';
        dynamic_sidebar($widget_area);
        echo '</div>';
    }
    
    echo '</div>'; // .footer-widgets-grid
    echo '</div>'; // .container
    echo '</div>'; // .footer-widgets-area
}

/**
 * Display homepage widget sections
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_display_homepage_widgets() {
    $homepage_sections = array(
        'homepage-hero' => array(
            'wrapper_class' => 'hero-section',
            'container' => true,
        ),
        'homepage-features' => array(
            'wrapper_class' => 'features-section',
            'container' => true,
        ),
        'homepage-stats' => array(
            'wrapper_class' => 'stats-section',
            'container' => true,
        ),
        'homepage-testimonials' => array(
            'wrapper_class' => 'testimonials-section',
            'container' => true,
        ),
        'homepage-cta' => array(
            'wrapper_class' => 'cta-section',
            'container' => true,
        ),
    );
    
    foreach ($homepage_sections as $section_id => $section_config) {
        if (recruitpro_has_widgets($section_id)) {
            echo '<section class="homepage-section ' . esc_attr($section_config['wrapper_class']) . '">';
            
            if ($section_config['container']) {
                echo '<div class="container">';
            }
            
            dynamic_sidebar($section_id);
            
            if ($section_config['container']) {
                echo '</div>';
            }
            
            echo '</section>';
        }
    }
}

/**
 * Get appropriate sidebar for current context
 *
 * @since 1.0.0
 * @return string Sidebar ID
 */
function recruitpro_get_contextual_sidebar() {
    // Jobs-related pages
    if (is_post_type_archive('job') || is_singular('job') || is_tax('job_category') || is_tax('job_location')) {
        return 'sidebar-jobs';
    }
    
    // Blog-related pages
    if (is_home() || is_archive() || is_single() && get_post_type() === 'post') {
        return 'blog-sidebar';
    }
    
    // Specific page templates
    if (is_page()) {
        global $post;
        $page_slug = $post->post_name;
        
        switch ($page_slug) {
            case 'about':
            case 'about-us':
            case 'our-company':
                return 'about-sidebar';
            
            case 'contact':
            case 'contact-us':
                return 'contact-sidebar';
            
            case 'services':
            case 'our-services':
                return 'sidebar-services';
            
            default:
                return 'sidebar-main';
        }
    }
    
    // Default sidebar
    return 'sidebar-main';
}

/**
 * Register recruitment-specific widgets
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_register_custom_widgets() {
    // This function can be extended to register custom widgets
    // that are specific to recruitment websites
    
    /**
     * Allow registration of custom recruitment widgets
     *
     * @since 1.0.0
     */
    do_action('recruitpro_register_custom_widgets');
}

// Register custom widgets after WordPress core widgets
add_action('widgets_init', 'recruitpro_register_custom_widgets', 20);

/**
 * Add widget area classes to body
 *
 * @since 1.0.0
 * @param array $classes Body classes
 * @return array Modified body classes
 */
function recruitpro_widget_area_body_classes($classes) {
    // Add class if any homepage widgets are active
    $homepage_widgets = array('homepage-hero', 'homepage-features', 'homepage-stats', 'homepage-testimonials', 'homepage-cta');
    $has_homepage_widgets = false;
    
    foreach ($homepage_widgets as $widget_area) {
        if (recruitpro_has_widgets($widget_area)) {
            $has_homepage_widgets = true;
            break;
        }
    }
    
    if ($has_homepage_widgets) {
        $classes[] = 'has-homepage-widgets';
    }
    
    // Add class for footer widgets
    $footer_columns = get_theme_mod('recruitpro_footer_widget_columns', 4);
    $has_footer_widgets = false;
    
    for ($i = 1; $i <= $footer_columns; $i++) {
        if (recruitpro_has_widgets('footer-' . $i)) {
            $has_footer_widgets = true;
            break;
        }
    }
    
    if ($has_footer_widgets) {
        $classes[] = 'has-footer-widgets';
    }
    
    // Add class for current sidebar
    $current_sidebar = recruitpro_get_contextual_sidebar();
    if (recruitpro_has_widgets($current_sidebar)) {
        $classes[] = 'has-sidebar';
        $classes[] = 'sidebar-active-' . str_replace('sidebar-', '', $current_sidebar);
    } else {
        $classes[] = 'no-sidebar';
    }
    
    return $classes;
}

add_filter('body_class', 'recruitpro_widget_area_body_classes');

/**
 * Widget area performance optimization
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_optimize_widget_areas() {
    // Cache widget output for better performance
    if (!is_admin() && !is_customize_preview()) {
        add_filter('widget_display_callback', 'recruitpro_cache_widget_output', 10, 3);
    }
}

add_action('init', 'recruitpro_optimize_widget_areas');

/**
 * Cache widget output for performance
 *
 * @since 1.0.0
 * @param array $instance Widget instance
 * @param WP_Widget $widget Widget object
 * @param array $args Widget arguments
 * @return array Widget instance
 */
function recruitpro_cache_widget_output($instance, $widget, $args) {
    // Simple caching mechanism for widgets
    // This can be enhanced based on specific needs
    return $instance;
}

/**
 * Add schema markup to widget areas
 *
 * @since 1.0.0
 * @param array $args Widget arguments
 * @return array Modified arguments
 */
function recruitpro_add_widget_schema_markup($args) {
    // Add appropriate schema markup based on widget area
    if (isset($args['id'])) {
        switch ($args['id']) {
            case 'sidebar-main':
            case 'sidebar-jobs':
            case 'blog-sidebar':
                $args['before_widget'] = str_replace('role="complementary"', 'role="complementary" itemscope itemtype="https://schema.org/WPSideBar"', $args['before_widget']);
                break;
                
            case 'footer-1':
            case 'footer-2':
            case 'footer-3':
            case 'footer-4':
                $args['before_widget'] = str_replace('<section', '<section itemscope itemtype="https://schema.org/WPFooter"', $args['before_widget']);
                break;
        }
    }
    
    return $args;
}

add_filter('dynamic_sidebar_params', 'recruitpro_add_widget_schema_markup');

/**
 * =================================================================
 * WIDGET AREA DOCUMENTATION
 * =================================================================
 * 
 * Widget Area Usage Guide:
 * 
 * PRIMARY SIDEBARS:
 * - sidebar-main: General content sidebar
 * - sidebar-jobs: Job listings and career pages
 * - sidebar-services: Service and consultation pages
 * 
 * HOMEPAGE SECTIONS:
 * - homepage-hero: Main hero section
 * - homepage-features: Service/feature highlights
 * - homepage-stats: Company statistics
 * - homepage-testimonials: Client testimonials
 * - homepage-cta: Call-to-action section
 * 
 * FOOTER AREAS:
 * - footer-1 to footer-4: Dynamic footer columns
 * - footer-bottom: Bottom footer area
 * 
 * SPECIALIZED AREAS:
 * - job-search-area: Job search functionality
 * - featured-jobs: Highlighted positions
 * - client-showcase: Client logos/testimonials
 * - industry-expertise: Sector specializations
 * 
 * MOBILE AREAS:
 * - mobile-header: Mobile-specific header content
 * - mobile-sticky-cta: Persistent mobile actions
 * 
 * UTILITY AREAS:
 * - offline-content: Maintenance mode content
 * - error-404-sidebar: 404 page assistance
 */

// End of widget-areas.php