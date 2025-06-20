<?php
/**
 * RecruitPro Theme Hooks and Filters
 *
 * This file contains all WordPress hooks and filters for the RecruitPro
 * recruitment website theme. It includes custom actions, filters, and
 * WordPress core integrations for performance, security, accessibility,
 * and recruitment-specific functionality at the theme level.
 *
 * @package RecruitPro
 * @subpackage Theme/Hooks
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/hooks-filters.php
 * Purpose: Theme-level WordPress hooks and filters
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality, no plugin overlap)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * THEME SETUP AND INITIALIZATION HOOKS
 * =================================================================
 */

/**
 * Theme setup - runs after theme is loaded
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_theme_setup() {
    
    // Add theme support for various features
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('custom-background');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'navigation-widgets',
    ));
    
    // Add support for responsive embeds
    add_theme_support('responsive-embeds');
    
    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    
    // Add support for block editor features
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Navigation', 'recruitpro'),
        'footer' => __('Footer Navigation', 'recruitpro'),
        'mobile' => __('Mobile Navigation', 'recruitpro'),
        'top-bar' => __('Top Bar Navigation', 'recruitpro'),
    ));
    
    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
    
    // Add image sizes for recruitment content
    add_image_size('recruitpro-hero', 1920, 800, true);
    add_image_size('recruitpro-featured', 800, 600, true);
    add_image_size('recruitpro-thumbnail', 400, 300, true);
    add_image_size('recruitpro-team', 350, 350, true);
    add_image_size('recruitpro-client-logo', 200, 100, false);
    add_image_size('recruitpro-blog-grid', 600, 400, true);
    
    // Load theme textdomain for translations
    load_theme_textdomain('recruitpro', get_template_directory() . '/languages');
}

add_action('after_setup_theme', 'recruitpro_theme_setup');

/**
 * =================================================================
 * CONTENT AND TEMPLATE HOOKS
 * =================================================================
 */

/**
 * Modify excerpt length for recruitment content
 * 
 * @since 1.0.0
 * @param int $length Current excerpt length
 * @return int Modified excerpt length
 */
function recruitpro_excerpt_length($length) {
    
    // Different excerpt lengths for different contexts
    if (is_home() || is_archive()) {
        return 25; // Shorter for listing pages
    }
    
    if (is_search()) {
        return 30; // Medium for search results
    }
    
    return 20; // Default shorter length for recruitment content
}

add_filter('excerpt_length', 'recruitpro_excerpt_length');

/**
 * Customize excerpt "more" text
 * 
 * @since 1.0.0
 * @param string $more Current "more" text
 * @return string Modified "more" text
 */
function recruitpro_excerpt_more($more) {
    
    if (is_admin()) {
        return $more;
    }
    
    return '&hellip; <a href="' . get_permalink() . '" class="read-more-link">' . __('Read More', 'recruitpro') . '</a>';
}

add_filter('excerpt_more', 'recruitpro_excerpt_more');

/**
 * Add custom body classes
 * 
 * @since 1.0.0
 * @param array $classes Existing body classes
 * @return array Modified body classes
 */
function recruitpro_body_classes($classes) {
    
    // Add layout classes
    $layout = get_theme_mod('recruitpro_site_layout', 'fullwidth');
    $classes[] = 'layout-' . $layout;
    
    // Add color scheme class
    $color_scheme = get_theme_mod('recruitpro_color_scheme', 'professional');
    $classes[] = 'color-scheme-' . $color_scheme;
    
    // Add page-specific classes
    if (is_front_page()) {
        $classes[] = 'is-front-page';
    }
    
    if (is_page_template()) {
        $template = get_page_template_slug();
        $classes[] = 'page-template-' . sanitize_html_class(str_replace('/', '-', $template));
    }
    
    // Add mobile detection class
    if (wp_is_mobile()) {
        $classes[] = 'is-mobile-device';
    }
    
    // Add browser detection
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strpos($user_agent, 'Chrome') !== false) {
        $classes[] = 'browser-chrome';
    } elseif (strpos($user_agent, 'Firefox') !== false) {
        $classes[] = 'browser-firefox';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        $classes[] = 'browser-safari';
    }
    
    // Add accessibility class if enabled
    if (get_theme_mod('recruitpro_accessibility_mode', false)) {
        $classes[] = 'accessibility-enhanced';
    }
    
    return $classes;
}

add_filter('body_class', 'recruitpro_body_classes');

/**
 * Add custom post classes
 * 
 * @since 1.0.0
 * @param array $classes Existing post classes
 * @param string $class Additional class
 * @param int $post_id Post ID
 * @return array Modified post classes
 */
function recruitpro_post_classes($classes, $class, $post_id) {
    
    // Add featured post class
    if (get_post_meta($post_id, '_recruitpro_featured_post', true)) {
        $classes[] = 'is-featured-post';
    }
    
    // Add post format classes for better styling
    $post_format = get_post_format($post_id);
    if ($post_format) {
        $classes[] = 'format-' . $post_format;
    }
    
    // Add reading time class
    $reading_time = recruitpro_get_reading_time($post_id);
    if ($reading_time <= 3) {
        $classes[] = 'quick-read';
    } elseif ($reading_time <= 7) {
        $classes[] = 'medium-read';
    } else {
        $classes[] = 'long-read';
    }
    
    return $classes;
}

add_filter('post_class', 'recruitpro_post_classes', 10, 3);

/**
 * =================================================================
 * SEO AND META HOOKS
 * =================================================================
 */

/**
 * Add custom meta tags to head
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_meta_tags() {
    
    // Add viewport meta tag
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">' . "\n";
    
    // Add theme color for mobile browsers
    $primary_color = get_theme_mod('recruitpro_primary_color', '#1e40af');
    echo '<meta name="theme-color" content="' . esc_attr($primary_color) . '">' . "\n";
    
    // Add recruitment-specific meta tags
    if (is_front_page()) {
        $company_name = get_bloginfo('name');
        $description = get_bloginfo('description');
        
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($company_name) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '">' . "\n";
        
        // Add structured data for organization
        $schema_data = recruitpro_get_organization_schema();
        echo '<script type="application/ld+json">' . wp_json_encode($schema_data) . '</script>' . "\n";
    }
    
    // Add canonical URL
    if (is_singular() && !is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    }
}

add_action('wp_head', 'recruitpro_meta_tags', 1);

/**
 * Optimize title tag output
 * 
 * @since 1.0.0
 * @param string $title Current title
 * @return string Modified title
 */
function recruitpro_document_title($title) {
    
    // Add site name to all pages except front page
    if (!is_front_page()) {
        $site_name = get_bloginfo('name');
        $title['site'] = $site_name;
    }
    
    // Customize titles for different page types
    if (is_category()) {
        $title['title'] = sprintf(__('Career Advice: %s', 'recruitpro'), single_cat_title('', false));
    }
    
    if (is_tag()) {
        $title['title'] = sprintf(__('Topics: %s', 'recruitpro'), single_tag_title('', false));
    }
    
    if (is_search()) {
        $title['title'] = sprintf(__('Search Results for: %s', 'recruitpro'), get_search_query());
    }
    
    return $title;
}

add_filter('document_title_parts', 'recruitpro_document_title');

/**
 * =================================================================
 * PERFORMANCE OPTIMIZATION HOOKS
 * =================================================================
 */

/**
 * Optimize image loading with lazy loading
 * 
 * @since 1.0.0
 * @param string $content Post content
 * @return string Modified content with lazy loading
 */
function recruitpro_add_lazy_loading($content) {
    
    if (is_admin() || is_feed()) {
        return $content;
    }
    
    // Add loading="lazy" to images
    $content = preg_replace('/<img(.*?)(?<!loading=[\'"]\w+[\'"])(?<!loading=[\'"]\w+[\'"].*?)>/i', '<img$1 loading="lazy">', $content);
    
    return $content;
}

add_filter('the_content', 'recruitpro_add_lazy_loading');

/**
 * Remove unnecessary WordPress features for performance
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_remove_unnecessary_features() {
    
    // Remove emoji support if disabled
    if (!get_theme_mod('recruitpro_enable_emojis', false)) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
    
    // Remove query strings from static resources
    if (get_theme_mod('recruitpro_remove_query_strings', true)) {
        add_filter('script_loader_src', 'recruitpro_remove_query_strings_from_src', 15, 1);
        add_filter('style_loader_src', 'recruitpro_remove_query_strings_from_src', 15, 1);
    }
    
    // Disable XML-RPC if not needed
    if (!get_theme_mod('recruitpro_enable_xmlrpc', false)) {
        add_filter('xmlrpc_enabled', '__return_false');
    }
}

add_action('init', 'recruitpro_remove_unnecessary_features');

/**
 * Remove query strings from static resources
 * 
 * @since 1.0.0
 * @param string $src Resource URL
 * @return string Clean URL without query strings
 */
function recruitpro_remove_query_strings_from_src($src) {
    
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    
    return $src;
}

/**
 * =================================================================
 * SECURITY ENHANCEMENT HOOKS
 * =================================================================
 */

/**
 * Remove WordPress version info for security
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_remove_wp_version() {
    
    // Remove version from scripts and styles
    add_filter('style_loader_src', 'recruitpro_remove_wp_version_strings');
    add_filter('script_loader_src', 'recruitpro_remove_wp_version_strings');
    
    // Remove generator meta tag
    remove_action('wp_head', 'wp_generator');
    
    // Remove version from RSS feeds
    add_filter('the_generator', '__return_empty_string');
}

add_action('init', 'recruitpro_remove_wp_version');

/**
 * Remove version strings from enqueued files
 * 
 * @since 1.0.0
 * @param string $src File URL
 * @return string Clean URL
 */
function recruitpro_remove_wp_version_strings($src) {
    
    global $wp_version;
    
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    
    if (!empty($query['ver']) && $query['ver'] === $wp_version) {
        $src = remove_query_arg('ver', $src);
    }
    
    return $src;
}

/**
 * Enhance login security
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_login_security() {
    
    // Remove login error details
    add_filter('login_errors', function() {
        return __('Invalid login credentials.', 'recruitpro');
    });
    
    // Add security headers
    add_action('login_head', function() {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
    });
}

add_action('init', 'recruitpro_login_security');

/**
 * =================================================================
 * ACCESSIBILITY ENHANCEMENT HOOKS
 * =================================================================
 */

/**
 * Add accessibility improvements
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_accessibility_enhancements() {
    
    // Add skip links
    add_action('wp_body_open', 'recruitpro_add_skip_links');
    
    // Enhance navigation accessibility
    add_filter('nav_menu_link_attributes', 'recruitpro_nav_menu_link_attributes', 10, 4);
    
    // Add ARIA labels to widgets
    add_filter('dynamic_sidebar_params', 'recruitpro_widget_accessibility');
}

add_action('init', 'recruitpro_accessibility_enhancements');

/**
 * Add skip navigation links
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_add_skip_links() {
    ?>
    <div class="skip-links screen-reader-text">
        <a href="#main-content" class="skip-link"><?php _e('Skip to main content', 'recruitpro'); ?></a>
        <a href="#primary-navigation" class="skip-link"><?php _e('Skip to navigation', 'recruitpro'); ?></a>
        <a href="#footer" class="skip-link"><?php _e('Skip to footer', 'recruitpro'); ?></a>
    </div>
    <?php
}

/**
 * Add accessibility attributes to navigation links
 * 
 * @since 1.0.0
 * @param array $atts Link attributes
 * @param WP_Post $item Menu item
 * @param stdClass $args Menu arguments
 * @param int $depth Menu depth
 * @return array Modified attributes
 */
function recruitpro_nav_menu_link_attributes($atts, $item, $args, $depth) {
    
    // Add aria-current for current page
    if (in_array('current-menu-item', $item->classes)) {
        $atts['aria-current'] = 'page';
    }
    
    // Add aria-expanded for dropdown menus
    if (in_array('menu-item-has-children', $item->classes)) {
        $atts['aria-expanded'] = 'false';
        $atts['aria-haspopup'] = 'true';
    }
    
    return $atts;
}

/**
 * Add accessibility attributes to widgets
 * 
 * @since 1.0.0
 * @param array $params Widget parameters
 * @return array Modified parameters
 */
function recruitpro_widget_accessibility($params) {
    
    // Add proper heading hierarchy
    $params[0]['before_title'] = str_replace('<h2', '<h3', $params[0]['before_title']);
    $params[0]['after_title'] = str_replace('</h2>', '</h3>', $params[0]['after_title']);
    
    return $params;
}

/**
 * =================================================================
 * SEARCH AND FILTERING HOOKS
 * =================================================================
 */

/**
 * Enhance search functionality
 * 
 * @since 1.0.0
 * @param WP_Query $query Main query object
 * @return void
 */
function recruitpro_enhance_search($query) {
    
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        
        // Include pages in search results
        $query->set('post_type', array('post', 'page'));
        
        // Exclude certain pages from search
        $excluded_pages = get_theme_mod('recruitpro_search_excluded_pages', array());
        if (!empty($excluded_pages)) {
            $query->set('post__not_in', $excluded_pages);
        }
        
        // Set posts per page for search results
        $query->set('posts_per_page', get_theme_mod('recruitpro_search_posts_per_page', 10));
    }
}

add_action('pre_get_posts', 'recruitpro_enhance_search');

/**
 * Add search form accessibility
 * 
 * @since 1.0.0
 * @param string $form Search form HTML
 * @return string Modified search form
 */
function recruitpro_search_form($form) {
    
    $unique_id = uniqid('search-form-');
    
    $form = '<form role="search" method="get" class="search-form" action="' . esc_url(home_url('/')) . '">
        <label for="' . $unique_id . '" class="screen-reader-text">' . __('Search for:', 'recruitpro') . '</label>
        <input type="search" id="' . $unique_id . '" class="search-field" placeholder="' . esc_attr(get_theme_mod('recruitpro_search_placeholder', __('Search...', 'recruitpro'))) . '" value="' . get_search_query() . '" name="s" required>
        <button type="submit" class="search-submit" aria-label="' . esc_attr__('Submit search', 'recruitpro') . '">
            <span class="search-icon" aria-hidden="true"></span>
            <span class="screen-reader-text">' . __('Search', 'recruitpro') . '</span>
        </button>
    </form>';
    
    return $form;
}

add_filter('get_search_form', 'recruitpro_search_form');

/**
 * =================================================================
 * COMMENT SYSTEM HOOKS
 * =================================================================
 */

/**
 * Enhance comment system
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_enhance_comments() {
    
    // Add comment form enhancements
    add_filter('comment_form_defaults', 'recruitpro_comment_form_defaults');
    
    // Add comment list enhancements
    add_filter('wp_list_comments_args', 'recruitpro_comment_list_args');
    
    // Add comment validation
    add_filter('preprocess_comment', 'recruitpro_validate_comment');
}

add_action('init', 'recruitpro_enhance_comments');

/**
 * Customize comment form defaults
 * 
 * @since 1.0.0
 * @param array $defaults Comment form defaults
 * @return array Modified defaults
 */
function recruitpro_comment_form_defaults($defaults) {
    
    $defaults['title_reply'] = __('Leave a Comment', 'recruitpro');
    $defaults['label_submit'] = __('Post Comment', 'recruitpro');
    $defaults['cancel_reply_link'] = __('Cancel Reply', 'recruitpro');
    
    // Add privacy notice if enabled
    if (get_theme_mod('recruitpro_gdpr_enable_comments', true)) {
        $privacy_text = get_theme_mod('recruitpro_comment_privacy_text', 
            __('Your email address will not be published. Required fields are marked *', 'recruitpro'));
        $defaults['comment_notes_before'] = '<p class="comment-notes">' . $privacy_text . '</p>';
    }
    
    return $defaults;
}

/**
 * Customize comment list arguments
 * 
 * @since 1.0.0
 * @param array $args Comment list arguments
 * @return array Modified arguments
 */
function recruitpro_comment_list_args($args) {
    
    $args['avatar_size'] = 60;
    $args['style'] = 'div';
    $args['short_ping'] = true;
    
    return $args;
}

/**
 * Add comment validation
 * 
 * @since 1.0.0
 * @param array $commentdata Comment data
 * @return array Modified comment data
 */
function recruitpro_validate_comment($commentdata) {
    
    // Check for spam keywords
    $spam_keywords = get_theme_mod('recruitpro_spam_keywords', array('casino', 'poker', 'viagra'));
    
    if (!empty($spam_keywords)) {
        $comment_content = strtolower($commentdata['comment_content']);
        
        foreach ($spam_keywords as $keyword) {
            if (strpos($comment_content, strtolower($keyword)) !== false) {
                wp_die(__('Your comment appears to be spam.', 'recruitpro'));
            }
        }
    }
    
    return $commentdata;
}

/**
 * =================================================================
 * CUSTOM TEMPLATE HOOKS
 * =================================================================
 */

/**
 * Add custom action hooks for template customization
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_add_template_hooks() {
    
    // Header hooks
    add_action('recruitpro_header_before', function() {
        do_action('recruitpro_before_header');
    });
    
    add_action('recruitpro_header_after', function() {
        do_action('recruitpro_after_header');
    });
    
    // Content hooks
    add_action('recruitpro_content_before', function() {
        do_action('recruitpro_before_content');
    });
    
    add_action('recruitpro_content_after', function() {
        do_action('recruitpro_after_content');
    });
    
    // Footer hooks
    add_action('recruitpro_footer_before', function() {
        do_action('recruitpro_before_footer');
    });
    
    add_action('recruitpro_footer_after', function() {
        do_action('recruitpro_after_footer');
    });
}

add_action('init', 'recruitpro_add_template_hooks');

/**
 * =================================================================
 * HELPER FUNCTIONS FOR HOOKS
 * =================================================================
 */

/**
 * Get organization schema data
 * 
 * @since 1.0.0
 * @return array Schema.org organization data
 */
function recruitpro_get_organization_schema() {
    
    $company_name = get_bloginfo('name');
    $description = get_bloginfo('description');
    $url = home_url('/');
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $company_name,
        'description' => $description,
        'url' => $url,
        'sameAs' => array(),
    );
    
    // Add logo if available
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        $schema['logo'] = $logo_url;
    }
    
    // Add social media links
    $social_platforms = array('linkedin', 'facebook', 'twitter', 'instagram');
    foreach ($social_platforms as $platform) {
        $url = get_theme_mod("recruitpro_footer_social_{$platform}", '');
        if ($url) {
            $schema['sameAs'][] = $url;
        }
    }
    
    // Add contact information
    $phone = get_theme_mod('recruitpro_footer_phone', '');
    $email = get_theme_mod('recruitpro_footer_email', '');
    $address = get_theme_mod('recruitpro_footer_address', '');
    
    if ($phone || $email || $address) {
        $schema['contactPoint'] = array(
            '@type' => 'ContactPoint',
            'contactType' => 'customer service',
        );
        
        if ($phone) {
            $schema['contactPoint']['telephone'] = $phone;
        }
        
        if ($email) {
            $schema['contactPoint']['email'] = $email;
        }
    }
    
    return $schema;
}

/**
 * Calculate reading time for posts
 * 
 * @since 1.0.0
 * @param int $post_id Post ID
 * @return int Reading time in minutes
 */
function recruitpro_get_reading_time($post_id) {
    
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Assuming 200 words per minute
    
    return max(1, $reading_time);
}

/**
 * =================================================================
 * PLUGIN INTEGRATION HOOKS
 * =================================================================
 */

/**
 * Add compatibility hooks for popular plugins
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_plugin_compatibility() {
    
    // Elementor compatibility
    if (class_exists('Elementor\Plugin')) {
        add_action('elementor/theme/register_locations', 'recruitpro_elementor_locations');
    }
    
    // WooCommerce compatibility (if used for premium services)
    if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
    
    // Contact Form 7 compatibility
    if (class_exists('WPCF7')) {
        add_filter('wpcf7_autop_or_not', '__return_false');
    }
    
    // Yoast SEO compatibility
    if (class_exists('WPSEO_Options')) {
        add_filter('wpseo_breadcrumb_links', 'recruitpro_yoast_breadcrumb_filter');
    }
}

add_action('init', 'recruitpro_plugin_compatibility');

/**
 * Register Elementor theme locations
 * 
 * @since 1.0.0
 * @param object $elementor_theme_manager Elementor theme manager
 * @return void
 */
function recruitpro_elementor_locations($elementor_theme_manager) {
    
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
    $elementor_theme_manager->register_location('single');
    $elementor_theme_manager->register_location('archive');
}

/**
 * Filter Yoast breadcrumbs for recruitment content
 * 
 * @since 1.0.0
 * @param array $links Breadcrumb links
 * @return array Modified breadcrumb links
 */
function recruitpro_yoast_breadcrumb_filter($links) {
    
    // Customize breadcrumbs for recruitment-specific content
    if (is_category()) {
        $links[1]['text'] = __('Career Advice', 'recruitpro');
    }
    
    return $links;
}

/**
 * =================================================================
 * MOBILE AND RESPONSIVE HOOKS
 * =================================================================
 */

/**
 * Add mobile-specific optimizations
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_mobile_optimizations() {
    
    if (wp_is_mobile()) {
        
        // Remove unnecessary scripts on mobile
        add_action('wp_enqueue_scripts', function() {
            if (get_theme_mod('recruitpro_mobile_remove_animations', true)) {
                wp_dequeue_style('recruitpro-animations');
            }
        }, 100);
        
        // Add mobile-specific meta tags
        add_action('wp_head', function() {
            echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
            echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
        });
    }
}

add_action('init', 'recruitpro_mobile_optimizations');

?>