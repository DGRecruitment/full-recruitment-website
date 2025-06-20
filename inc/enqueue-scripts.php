<?php
/**
 * RecruitPro Theme Scripts and Styles Enqueue
 *
 * This file handles the proper enqueuing of CSS and JavaScript files
 * for the RecruitPro recruitment website theme. It includes performance
 * optimizations, conditional loading, and plugin conflict prevention.
 *
 * @package RecruitPro
 * @subpackage Theme
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/enqueue-scripts.php
 * Purpose: Centralized script and style management
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (designed to avoid plugin conflicts)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue theme styles and scripts
 * 
 * This function handles all CSS and JavaScript enqueuing for the theme.
 * It includes performance optimizations and conditional loading.
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_enqueue_scripts() {
    
    // Get theme version for cache busting
    $theme_version = wp_get_theme()->get('Version');
    
    // Check if we're in debug mode
    $is_debug = defined('WP_DEBUG') && WP_DEBUG;
    $min_suffix = $is_debug ? '' : '.min';
    
    // =================================================================
    // CSS STYLESHEETS
    // =================================================================
    
    // Main theme stylesheet (always load first)
    wp_enqueue_style(
        'recruitpro-main-style',
        get_template_directory_uri() . '/assets/css/main' . $min_suffix . '.css',
        array(),
        $theme_version,
        'all'
    );
    
    // Responsive styles (mobile-first approach)
    wp_enqueue_style(
        'recruitpro-responsive',
        get_template_directory_uri() . '/assets/css/responsive' . $min_suffix . '.css',
        array('recruitpro-main-style'),
        $theme_version,
        'all'
    );
    
    // Elementor integration styles (only if Elementor is active)
    if (class_exists('Elementor\Plugin')) {
        wp_enqueue_style(
            'recruitpro-elementor-integration',
            get_template_directory_uri() . '/assets/css/elementor-integration' . $min_suffix . '.css',
            array('recruitpro-main-style'),
            $theme_version,
            'all'
        );
    }
    
    // Classic Editor styles (only if Classic Editor is active)
    if (function_exists('the_classic_editor_plugin_has_access')) {
        wp_enqueue_style(
            'recruitpro-classic-editor',
            get_template_directory_uri() . '/assets/css/classic-editor' . $min_suffix . '.css',
            array('recruitpro-main-style'),
            $theme_version,
            'all'
        );
    }
    
    // Gutenberg editor styles (only if Gutenberg is active)
    if (function_exists('register_block_type')) {
        wp_enqueue_style(
            'recruitpro-gutenberg',
            get_template_directory_uri() . '/assets/css/gutenberg-editor' . $min_suffix . '.css',
            array('recruitpro-main-style'),
            $theme_version,
            'all'
        );
    }
    
    // RTL language support
    if (is_rtl()) {
        wp_enqueue_style(
            'recruitpro-rtl',
            get_template_directory_uri() . '/assets/css/rtl' . $min_suffix . '.css',
            array('recruitpro-main-style'),
            $theme_version,
            'all'
        );
    }
    
    // Animations and transitions (only if not in reduced motion mode)
    if (!get_theme_mod('recruitpro_reduce_motion', false)) {
        wp_enqueue_style(
            'recruitpro-animations',
            get_template_directory_uri() . '/assets/css/animations' . $min_suffix . '.css',
            array('recruitpro-main-style'),
            $theme_version,
            'all'
        );
    }
    
    // Accessibility enhancements
    wp_enqueue_style(
        'recruitpro-accessibility',
        get_template_directory_uri() . '/assets/css/accessibility' . $min_suffix . '.css',
        array('recruitpro-main-style'),
        $theme_version,
        'all'
    );
    
    // Performance optimization styles
    wp_enqueue_style(
        'recruitpro-performance',
        get_template_directory_uri() . '/assets/css/performance' . $min_suffix . '.css',
        array('recruitpro-main-style'),
        $theme_version,
        'all'
    );
    
    // Print styles
    wp_enqueue_style(
        'recruitpro-print',
        get_template_directory_uri() . '/assets/css/print' . $min_suffix . '.css',
        array('recruitpro-main-style'),
        $theme_version,
        'print'
    );
    
    // =================================================================
    // GOOGLE FONTS
    // =================================================================
    
    // Get selected fonts from customizer
    $primary_font = get_theme_mod('recruitpro_primary_font', 'Inter');
    $secondary_font = get_theme_mod('recruitpro_secondary_font', 'Roboto');
    $heading_font = get_theme_mod('recruitpro_heading_font', 'Poppins');
    
    // Build Google Fonts URL
    $google_fonts_url = recruitpro_build_google_fonts_url($primary_font, $secondary_font, $heading_font);
    
    if ($google_fonts_url) {
        wp_enqueue_style(
            'recruitpro-google-fonts',
            $google_fonts_url,
            array(),
            $theme_version
        );
    }
    
    // =================================================================
    // JAVASCRIPT FILES
    // =================================================================
    
    // jQuery (WordPress core, but ensure it's loaded)
    wp_enqueue_script('jquery');
    
    // Main theme JavaScript
    wp_enqueue_script(
        'recruitpro-main-js',
        get_template_directory_uri() . '/assets/js/main' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // Navigation functionality
    wp_enqueue_script(
        'recruitpro-navigation',
        get_template_directory_uri() . '/assets/js/navigation' . $min_suffix . '.js',
        array('jquery', 'recruitpro-main-js'),
        $theme_version,
        true
    );
    
    // Mobile menu functionality
    wp_enqueue_script(
        'recruitpro-mobile-menu',
        get_template_directory_uri() . '/assets/js/mobile-menu' . $min_suffix . '.js',
        array('jquery', 'recruitpro-navigation'),
        $theme_version,
        true
    );
    
    // Accessibility features
    wp_enqueue_script(
        'recruitpro-accessibility',
        get_template_directory_uri() . '/assets/js/accessibility' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // Performance optimization (lazy loading, etc.)
    wp_enqueue_script(
        'recruitpro-performance',
        get_template_directory_uri() . '/assets/js/performance' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // Image lazy loading
    wp_enqueue_script(
        'recruitpro-lazy-loading',
        get_template_directory_uri() . '/assets/js/lazy-loading' . $min_suffix . '.js',
        array('jquery', 'recruitpro-performance'),
        $theme_version,
        true
    );
    
    // Smooth scrolling
    wp_enqueue_script(
        'recruitpro-smooth-scroll',
        get_template_directory_uri() . '/assets/js/smooth-scroll' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // Back to top button
    wp_enqueue_script(
        'recruitpro-back-to-top',
        get_template_directory_uri() . '/assets/js/back-to-top' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // Modal system
    wp_enqueue_script(
        'recruitpro-modal-system',
        get_template_directory_uri() . '/assets/js/modal-system' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // UI components (tabs, accordions)
    wp_enqueue_script(
        'recruitpro-ui-components',
        get_template_directory_uri() . '/assets/js/tabs-accordions' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // Image gallery
    if (is_page_template('page-gallery.php') || is_singular('post')) {
        wp_enqueue_script(
            'recruitpro-image-gallery',
            get_template_directory_uri() . '/assets/js/image-gallery' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // Video player (only on pages with video)
    if (has_shortcode(get_post()->post_content ?? '', 'video') || 
        get_post_meta(get_the_ID(), '_recruitpro_has_video', true)) {
        wp_enqueue_script(
            'recruitpro-video-player',
            get_template_directory_uri() . '/assets/js/video-player' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // Basic search interface (theme-level only)
    wp_enqueue_script(
        'recruitpro-search-interface',
        get_template_directory_uri() . '/assets/js/search-interface' . $min_suffix . '.js',
        array('jquery'),
        $theme_version,
        true
    );
    
    // AI Chat Widget (theme-level - website content only)
    if (get_theme_mod('recruitpro_enable_ai_chat', true)) {
        wp_enqueue_script(
            'recruitpro-ai-chat-widget',
            get_template_directory_uri() . '/assets/js/ai-chat-widget' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
        
        // Localize AI chat script
        wp_localize_script('recruitpro-ai-chat-widget', 'recruitpro_ai_chat', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_ai_chat_nonce'),
            'strings' => array(
                'placeholder' => __('Ask us anything about our services...', 'recruitpro'),
                'send_button' => __('Send', 'recruitpro'),
                'thinking' => __('Thinking...', 'recruitpro'),
                'error' => __('Sorry, something went wrong. Please try again.', 'recruitpro'),
                'welcome' => __('Hi! How can we help you today?', 'recruitpro')
            ),
            'settings' => array(
                'position' => get_theme_mod('recruitpro_ai_chat_position', 'bottom-right'),
                'theme' => get_theme_mod('recruitpro_ai_chat_theme', 'light'),
                'auto_open' => get_theme_mod('recruitpro_ai_chat_auto_open', false)
            )
        ));
    }
    
    // Elementor integration (only if Elementor is active)
    if (class_exists('Elementor\Plugin')) {
        wp_enqueue_script(
            'recruitpro-elementor-integration',
            get_template_directory_uri() . '/assets/js/elementor-integration' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // Classic Editor integration (only if Classic Editor is active)
    if (function_exists('the_classic_editor_plugin_has_access')) {
        wp_enqueue_script(
            'recruitpro-classic-editor',
            get_template_directory_uri() . '/assets/js/classic-editor' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // =================================================================
    // LOCALIZATION
    // =================================================================
    
    // Localize main script with theme data
    wp_localize_script('recruitpro-main-js', 'recruitpro_theme_data', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('recruitpro_theme_nonce'),
        'theme_url' => get_template_directory_uri(),
        'is_mobile' => wp_is_mobile(),
        'is_rtl' => is_rtl(),
        'strings' => array(
            'loading' => __('Loading...', 'recruitpro'),
            'error' => __('An error occurred. Please try again.', 'recruitpro'),
            'success' => __('Success!', 'recruitpro'),
            'close' => __('Close', 'recruitpro'),
            'menu_toggle' => __('Toggle Menu', 'recruitpro'),
            'search_placeholder' => __('Search...', 'recruitpro'),
            'no_results' => __('No results found.', 'recruitpro')
        ),
        'settings' => array(
            'smooth_scroll' => get_theme_mod('recruitpro_smooth_scroll', true),
            'lazy_loading' => get_theme_mod('recruitpro_lazy_loading', true),
            'back_to_top' => get_theme_mod('recruitpro_back_to_top', true),
            'mobile_breakpoint' => get_theme_mod('recruitpro_mobile_breakpoint', 768)
        )
    ));
    
    // =================================================================
    // CONDITIONAL LOADING
    // =================================================================
    
    // Comments reply script (only on singular posts with comments)
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // Contact form scripts (only on contact page)
    if (is_page_template('page-contact.php') || is_page('contact')) {
        wp_enqueue_script(
            'recruitpro-contact-form',
            get_template_directory_uri() . '/assets/js/contact-form' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // About page scripts (only on about page)
    if (is_page_template('page-about.php') || is_page('about')) {
        wp_enqueue_script(
            'recruitpro-about-page',
            get_template_directory_uri() . '/assets/js/about-page' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
    
    // Blog page scripts (only on blog pages)
    if (is_home() || is_category() || is_tag() || is_author() || is_date()) {
        wp_enqueue_script(
            'recruitpro-blog-scripts',
            get_template_directory_uri() . '/assets/js/blog-scripts' . $min_suffix . '.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
}

/**
 * Enqueue admin scripts and styles
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_admin_enqueue_scripts($hook) {
    
    $theme_version = wp_get_theme()->get('Version');
    
    // Admin styles for better integration
    wp_enqueue_style(
        'recruitpro-admin-style',
        get_template_directory_uri() . '/assets/css/admin.css',
        array(),
        $theme_version
    );
    
    // Customizer preview scripts
    if ('customize.php' === $hook) {
        wp_enqueue_script(
            'recruitpro-customizer',
            get_template_directory_uri() . '/assets/js/customizer.js',
            array('jquery', 'customize-preview'),
            $theme_version,
            true
        );
    }
    
    // Theme options scripts
    if ('appearance_page_recruitpro-options' === $hook) {
        wp_enqueue_script(
            'recruitpro-admin-options',
            get_template_directory_uri() . '/assets/js/admin-options.js',
            array('jquery'),
            $theme_version,
            true
        );
    }
}

/**
 * Build Google Fonts URL
 * 
 * @since 1.0.0
 * @param string $primary_font Primary font family
 * @param string $secondary_font Secondary font family
 * @param string $heading_font Heading font family
 * @return string|false Google Fonts URL or false if no fonts needed
 */
function recruitpro_build_google_fonts_url($primary_font, $secondary_font, $heading_font) {
    
    // Default system fonts that don't need Google Fonts
    $system_fonts = array(
        'Arial', 'Arial Black', 'Helvetica', 'Times New Roman', 'Times', 
        'Courier New', 'Courier', 'Verdana', 'Georgia', 'Palatino', 
        'Garamond', 'Bookman', 'Comic Sans MS', 'Trebuchet MS', 
        'Arial Narrow', 'Brush Script MT', 'System UI', 'sans-serif', 
        'serif', 'monospace'
    );
    
    $google_fonts = array();
    
    // Check each font and add to Google Fonts array if needed
    $fonts_to_check = array($primary_font, $secondary_font, $heading_font);
    
    foreach ($fonts_to_check as $font) {
        if (!in_array($font, $system_fonts) && !empty($font)) {
            $google_fonts[] = $font . ':300,400,500,600,700';
        }
    }
    
    // Remove duplicates
    $google_fonts = array_unique($google_fonts);
    
    if (empty($google_fonts)) {
        return false;
    }
    
    // Build URL
    $fonts_url = add_query_arg(array(
        'family' => implode('|', $google_fonts),
        'display' => 'swap'
    ), 'https://fonts.googleapis.com/css');
    
    return $fonts_url;
}

/**
 * Enqueue block editor styles
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_block_editor_styles() {
    
    $theme_version = wp_get_theme()->get('Version');
    
    // Block editor styles
    wp_enqueue_style(
        'recruitpro-block-editor-styles',
        get_template_directory_uri() . '/assets/css/block-editor.css',
        array(),
        $theme_version
    );
}

/**
 * Add async/defer attributes to scripts
 * 
 * @since 1.0.0
 * @param string $tag Script tag
 * @param string $handle Script handle
 * @param string $src Script source
 * @return string Modified script tag
 */
function recruitpro_add_async_defer_attributes($tag, $handle, $src) {
    
    // Scripts to defer
    $defer_scripts = array(
        'recruitpro-performance',
        'recruitpro-lazy-loading',
        'recruitpro-ui-components',
        'recruitpro-back-to-top'
    );
    
    // Scripts to load async
    $async_scripts = array(
        'recruitpro-ai-chat-widget',
        'recruitpro-analytics'
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace('<script ', '<script defer ', $tag);
    }
    
    if (in_array($handle, $async_scripts)) {
        return str_replace('<script ', '<script async ', $tag);
    }
    
    return $tag;
}

/**
 * Preload critical resources
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_preload_resources() {
    
    // Preload main CSS
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/main.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    
    // Preload critical JavaScript
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/js/main.js" as="script">' . "\n";
    
    // Preload Google Fonts if enabled
    $primary_font = get_theme_mod('recruitpro_primary_font', 'Inter');
    if (!empty($primary_font) && $primary_font !== 'Arial') {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'recruitpro_enqueue_scripts');
add_action('admin_enqueue_scripts', 'recruitpro_admin_enqueue_scripts');
add_action('enqueue_block_editor_assets', 'recruitpro_block_editor_styles');
add_filter('script_loader_tag', 'recruitpro_add_async_defer_attributes', 10, 3);
add_action('wp_head', 'recruitpro_preload_resources', 1);

/**
 * Remove plugin conflicts and optimize loading
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_optimize_plugin_scripts() {
    
    // Don't run in admin
    if (is_admin()) {
        return;
    }
    
    // Remove unnecessary plugin scripts on specific pages
    if (is_front_page()) {
        // Example: Remove contact form 7 scripts on homepage if not needed
        wp_dequeue_script('contact-form-7');
        wp_dequeue_style('contact-form-7');
    }
    
    // Optimize jQuery loading
    if (!is_admin() && !is_customize_preview()) {
        wp_deregister_script('jquery');
        wp_register_script(
            'jquery',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
            array(),
            '3.6.0',
            true
        );
        wp_enqueue_script('jquery');
    }
}

add_action('wp_enqueue_scripts', 'recruitpro_optimize_plugin_scripts', 100);

/**
 * Theme-specific inline styles
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_inline_styles() {
    
    // Get customizer colors
    $primary_color = get_theme_mod('recruitpro_primary_color', '#1e40af');
    $secondary_color = get_theme_mod('recruitpro_secondary_color', '#64748b');
    $accent_color = get_theme_mod('recruitpro_accent_color', '#06b6d4');
    
    // Custom CSS variables
    $custom_css = "
    :root {
        --recruitpro-primary: {$primary_color};
        --recruitpro-secondary: {$secondary_color};
        --recruitpro-accent: {$accent_color};
        --recruitpro-primary-rgb: " . recruitpro_hex_to_rgb($primary_color) . ";
        --recruitpro-secondary-rgb: " . recruitpro_hex_to_rgb($secondary_color) . ";
        --recruitpro-accent-rgb: " . recruitpro_hex_to_rgb($accent_color) . ";
    }
    ";
    
    wp_add_inline_style('recruitpro-main-style', $custom_css);
}

add_action('wp_enqueue_scripts', 'recruitpro_inline_styles');

/**
 * Convert hex color to RGB values
 * 
 * @since 1.0.0
 * @param string $hex Hex color value
 * @return string RGB values separated by commas
 */
function recruitpro_hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    
    return $r . ', ' . $g . ', ' . $b;
}

?>