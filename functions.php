<?php
/**
 * RecruitPro Theme Functions
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Theme Setup
 * Sets up theme defaults and registers support for various WordPress features
 */
if (!function_exists('recruitpro_setup')) :
    function recruitpro_setup() {
        // Make theme available for translation
        load_theme_textdomain('recruitpro', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head
        add_theme_support('automatic-feed-links');

        // Let WordPress manage the document title
        add_theme_support('title-tag');

        // Enable support for Post Thumbnails on posts and pages
        add_theme_support('post-thumbnails');

        // Add custom image sizes for recruitment content
        add_image_size('recruitpro-hero', 1920, 800, true);
        add_image_size('recruitpro-featured', 800, 400, true);
        add_image_size('recruitpro-thumbnail', 400, 300, true);
        add_image_size('recruitpro-candidate-avatar', 150, 150, true);
        add_image_size('recruitpro-company-logo', 200, 100, true);
        add_image_size('recruitpro-team-member', 300, 400, true);

        // Register navigation menus
        register_nav_menus(array(
            'primary'       => esc_html__('Primary Menu', 'recruitpro'),
            'secondary'     => esc_html__('Secondary Menu', 'recruitpro'),
            'footer'        => esc_html__('Footer Menu', 'recruitpro'),
            'social'        => esc_html__('Social Media Menu', 'recruitpro'),
        ));

        // Switch default core markup for search form, comment form, and comments
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        ));

        // Set up the WordPress core custom background feature
        add_theme_support('custom-background', apply_filters('recruitpro_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));

        // Add theme support for selective refresh for widgets
        add_theme_support('customize-selective-refresh-widgets');

        // Add support for core custom logo
        add_theme_support('custom-logo', array(
            'height'      => 100,
            'width'       => 300,
            'flex-width'  => true,
            'flex-height' => true,
            'header-text' => array('site-title', 'site-description'),
        ));

        // Add support for custom header
        add_theme_support('custom-header', array(
            'default-image'      => '',
            'default-text-color' => '2563eb',
            'width'              => 1920,
            'height'             => 400,
            'flex-width'         => true,
            'flex-height'        => true,
        ));

        // Add support for Block Editor color palette
        add_theme_support('editor-color-palette', array(
            array(
                'name'  => esc_attr__('Primary Blue', 'recruitpro'),
                'slug'  => 'primary',
                'color' => '#2563eb',
            ),
            array(
                'name'  => esc_attr__('Secondary Green', 'recruitpro'),
                'slug'  => 'secondary',
                'color' => '#059669',
            ),
            array(
                'name'  => esc_attr__('Accent Red', 'recruitpro'),
                'slug'  => 'accent',
                'color' => '#dc2626',
            ),
            array(
                'name'  => esc_attr__('Text Dark', 'recruitpro'),
                'slug'  => 'text-dark',
                'color' => '#1e293b',
            ),
            array(
                'name'  => esc_attr__('Text Light', 'recruitpro'),
                'slug'  => 'text-light',
                'color' => '#64748b',
            ),
            array(
                'name'  => esc_attr__('Background', 'recruitpro'),
                'slug'  => 'background',
                'color' => '#ffffff',
            ),
            array(
                'name'  => esc_attr__('Surface', 'recruitpro'),
                'slug'  => 'surface',
                'color' => '#f8fafc',
            ),
        ));

        // Add support for responsive embeds
        add_theme_support('responsive-embeds');

        // Add support for wide and full alignment
        add_theme_support('align-wide');

        // Add support for editor styles
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');

        // Support for Classic Editor (as requested)
        add_theme_support('classic-editor');

        // Add support for Elementor
        add_theme_support('elementor');

        // Add content width for better media handling
        if (!isset($content_width)) {
            $content_width = 1200;
        }
    }
endif;
add_action('after_setup_theme', 'recruitpro_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet
 */
function recruitpro_content_width() {
    $GLOBALS['content_width'] = apply_filters('recruitpro_content_width', 1200);
}
add_action('after_setup_theme', 'recruitpro_content_width', 0);

/**
 * Register widget area
 */
function recruitpro_widgets_init() {
    // Main Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Main Sidebar', 'recruitpro'),
        'id'            => 'sidebar-main',
        'description'   => esc_html__('Add widgets here to appear in the main sidebar.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Job Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Job Sidebar', 'recruitpro'),
        'id'            => 'sidebar-jobs',
        'description'   => esc_html__('Add widgets here to appear on job pages.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Footer Widget Areas
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(esc_html__('Footer %d', 'recruitpro'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(esc_html__('Add widgets here to appear in footer column %d.', 'recruitpro'), $i),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
    }

    // Homepage Widget Areas
    register_sidebar(array(
        'name'          => esc_html__('Homepage Hero', 'recruitpro'),
        'id'            => 'homepage-hero',
        'description'   => esc_html__('Add widgets here to appear in the homepage hero section.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Homepage Features', 'recruitpro'),
        'id'            => 'homepage-features',
        'description'   => esc_html__('Add widgets here to appear in the homepage features section.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => esc_html__('Homepage CTA', 'recruitpro'),
        'id'            => 'homepage-cta',
        'description'   => esc_html__('Add widgets here to appear in the homepage call-to-action section.', 'recruitpro'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'recruitpro_widgets_init');

/**
 * Enqueue scripts and styles
 */
function recruitpro_scripts() {
    $theme_version = wp_get_theme()->get('Version');

    // Main stylesheet
    wp_enqueue_style('recruitpro-style', get_stylesheet_uri(), array(), $theme_version);

    // Additional theme styles
    wp_enqueue_style('recruitpro-main', get_template_directory_uri() . '/assets/css/main.css', array('recruitpro-style'), $theme_version);

    // Google Fonts with display=swap for performance
    wp_enqueue_style('recruitpro-fonts', recruitpro_get_google_fonts_url(), array(), null);

    // Main theme JavaScript
    wp_enqueue_script('recruitpro-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), $theme_version, true);

    // Navigation script
    wp_enqueue_script('recruitpro-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array('jquery'), $theme_version, true);

    // AI Chat Widget (theme-level only - website content knowledge)
    if (get_theme_mod('recruitpro_enable_ai_chat', true)) {
        wp_enqueue_script('recruitpro-ai-chat', get_template_directory_uri() . '/assets/js/ai-chat.js', array('jquery'), $theme_version, true);
        wp_localize_script('recruitpro-ai-chat', 'recruitpro_ai_chat', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('recruitpro_ai_chat_nonce'),
            'enabled'  => get_theme_mod('recruitpro_enable_ai_chat', true),
            'position' => get_theme_mod('recruitpro_ai_chat_position', 'bottom-right'),
        ));
    }

    // Mobile menu toggle
    wp_enqueue_script('recruitpro-mobile-menu', get_template_directory_uri() . '/assets/js/mobile-menu.js', array('jquery'), $theme_version, true);

    // Smooth scrolling
    wp_enqueue_script('recruitpro-smooth-scroll', get_template_directory_uri() . '/assets/js/smooth-scroll.js', array(), $theme_version, true);

    // Modal system for job applications
    wp_enqueue_script('recruitpro-modals', get_template_directory_uri() . '/assets/js/modal-system.js', array('jquery'), $theme_version, true);

    // Lazy loading for images
    wp_enqueue_script('recruitpro-lazy-loading', get_template_directory_uri() . '/assets/js/lazy-loading.js', array(), $theme_version, true);

    // Back to top button
    wp_enqueue_script('recruitpro-back-to-top', get_template_directory_uri() . '/assets/js/back-to-top.js', array('jquery'), $theme_version, true);

    // Theme customizer live preview
    if (is_customize_preview()) {
        wp_enqueue_script('recruitpro-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), $theme_version, true);
    }

    // Comments reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Conditional scripts for specific pages
    if (is_page_template('page-templates/page-contact.php')) {
        wp_enqueue_script('recruitpro-contact-form', get_template_directory_uri() . '/assets/js/contact-form.js', array('jquery'), $theme_version, true);
    }

    // Job-related scripts (when jobs plugin is active)
    if (recruitpro_is_jobs_page() || is_singular('job')) {
        wp_enqueue_script('recruitpro-jobs', get_template_directory_uri() . '/assets/js/job-interface.js', array('jquery'), $theme_version, true);
        wp_localize_script('recruitpro-jobs', 'recruitpro_jobs', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('recruitpro_jobs_nonce'),
        ));
    }

    // Localize main script with theme data
    wp_localize_script('recruitpro-main', 'recruitpro_theme', array(
        'ajax_url'           => admin_url('admin-ajax.php'),
        'nonce'              => wp_create_nonce('recruitpro_theme_nonce'),
        'site_url'           => home_url('/'),
        'theme_url'          => get_template_directory_uri(),
        'is_mobile'          => wp_is_mobile(),
        'is_rtl'             => is_rtl(),
        'current_language'   => get_locale(),
        'crm_integration'    => class_exists('RecruitPro_CRM'),
        'jobs_integration'   => class_exists('RecruitPro_Jobs'),
        'seo_integration'    => class_exists('RecruitPro_SEO'),
        'forms_integration'  => class_exists('RecruitPro_Forms'),
        'registration_disabled' => true, // No registration allowed
    ));
}
add_action('wp_enqueue_scripts', 'recruitpro_scripts');

/**
 * Get Google Fonts URL
 */
function recruitpro_get_google_fonts_url() {
    $fonts_url = '';
    $fonts     = array();
    $subsets   = 'latin,latin-ext';

    // Primary font: Inter
    if ('off' !== _x('on', 'Inter font: on or off', 'recruitpro')) {
        $fonts[] = 'Inter:300,400,500,600,700,900';
    }

    // Secondary font: Poppins
    if ('off' !== _x('on', 'Poppins font: on or off', 'recruitpro')) {
        $fonts[] = 'Poppins:300,400,500,600,700,900';
    }

    if ($fonts) {
        $fonts_url = add_query_arg(array(
            'family'  => urlencode(implode('|', $fonts)),
            'subset'  => urlencode($subsets),
            'display' => 'swap',
        ), 'https://fonts.googleapis.com/css');
    }

    return $fonts_url;
}

/**
 * Include theme includes files
 */
require get_template_directory() . '/inc/theme-functions.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/enqueue-scripts.php';

// Load performance optimizations
require get_template_directory() . '/inc/performance.php';

// Load security enhancements
require get_template_directory() . '/inc/security.php';

// Load accessibility features
require get_template_directory() . '/inc/accessibility.php';

// Load SEO optimization (basic theme-level only)
require get_template_directory() . '/inc/seo-optimization.php';

// Load schema markup (basic theme-level)
require get_template_directory() . '/inc/schema-markup.php';

// Load Classic Editor integration
require get_template_directory() . '/inc/classic-editor.php';

// Load Elementor integration
require get_template_directory() . '/inc/elementor-integration.php';

// Load multilingual support
require get_template_directory() . '/inc/multilingual.php';

// Load social integration (basic theme-level)
require get_template_directory() . '/inc/social-integration.php';

// Load mobile optimization
require get_template_directory() . '/inc/mobile-optimization.php';

// Load hooks and filters
require get_template_directory() . '/inc/hooks-filters.php';

/**
 * Helper Functions
 */

/**
 * Check if current page is jobs-related
 */
function recruitpro_is_jobs_page() {
    return (is_post_type_archive('job') || is_tax('job_category') || is_tax('job_location') || is_tax('job_type'));
}

/**
 * Get theme customizer option with fallback
 */
function recruitpro_get_option($option_name, $default = '') {
    return get_theme_mod($option_name, $default);
}

/**
 * Check if CRM plugin is active and functional
 */
function recruitpro_has_crm() {
    return class_exists('RecruitPro_CRM') && function_exists('recruitpro_crm_is_active');
}

/**
 * Check if Jobs plugin is active
 */
function recruitpro_has_jobs_plugin() {
    return class_exists('RecruitPro_Jobs');
}

/**
 * Check if SEO plugin is active
 */
function recruitpro_has_seo_plugin() {
    return class_exists('RecruitPro_SEO');
}

/**
 * Disable user registration (as per requirements)
 * 
 * NOTE: This theme does NOT handle user portals or registration
 * - Candidates apply via forms only (no accounts created)
 * - Client portals are generated by CRM plugin with personalized links
 * - No front-end user dashboards or portal pages in theme
 */
add_filter('pre_option_users_can_register', '__return_zero');

/**
 * Remove registration-related admin menu items and links
 */
function recruitpro_remove_registration_links() {
    remove_action('wp_head', 'wp_resource_hints', 2);
    
    // Remove registration link from login form
    add_filter('login_display_language_dropdown', '__return_false');
    
    // Remove users menu for non-admins (only admin/CRM should manage users)
    if (!current_user_can('administrator')) {
        remove_menu_page('users.php');
    }
}
add_action('admin_init', 'recruitpro_remove_registration_links');

/**
 * Security: Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Security: Remove RSD link
 */
remove_action('wp_head', 'rsd_link');

/**
 * Security: Remove Windows Live Writer link
 */
remove_action('wp_head', 'wlwmanifest_link');

/**
 * Security: Remove shortlink
 */
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Security: Remove REST API links
 */
remove_action('wp_head', 'rest_output_link_wp_head');

/**
 * Performance: Remove emoji scripts
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/**
 * Performance: Defer parsing of JavaScript
 */
function recruitpro_defer_parsing_of_js($url) {
    if (is_admin()) {
        return $url;
    }

    if (false === strpos($url, '.js')) {
        return $url;
    }

    if (strpos($url, 'jquery.js')) {
        return $url;
    }

    return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'recruitpro_defer_parsing_of_js', 10);

/**
 * AI Chat Widget AJAX Handler (Theme-level - website content only)
 */
function recruitpro_ai_chat_handler() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_ai_chat_nonce')) {
        wp_die('Security check failed');
    }

    $message = sanitize_text_field($_POST['message']);
    
    // Simple AI chat responses based on website content only
    // This is theme-level AI, not CRM AI
    $responses = array(
        'services' => 'We offer comprehensive recruitment services including permanent placement, temporary staffing, executive search, and talent consultation. How can we help you today?',
        'contact' => 'You can reach us through our contact form, call us directly, or visit our office. Our team is ready to assist you with your recruitment needs.',
        'jobs' => 'You can browse our current job openings on our jobs page. We regularly update our listings with new opportunities across various industries.',
        'about' => 'We are a professional recruitment agency specializing in connecting talented candidates with leading employers. Our experienced team is dedicated to finding the perfect match for both parties.',
        'default' => 'Thank you for your message. Our team will get back to you shortly. In the meantime, feel free to browse our services or current job openings.',
    );

    // Simple keyword matching for demo purposes
    $response = $responses['default'];
    $message_lower = strtolower($message);

    if (strpos($message_lower, 'service') !== false || strpos($message_lower, 'what do you do') !== false) {
        $response = $responses['services'];
    } elseif (strpos($message_lower, 'contact') !== false || strpos($message_lower, 'phone') !== false || strpos($message_lower, 'email') !== false) {
        $response = $responses['contact'];
    } elseif (strpos($message_lower, 'job') !== false || strpos($message_lower, 'career') !== false || strpos($message_lower, 'position') !== false) {
        $response = $responses['jobs'];
    } elseif (strpos($message_lower, 'about') !== false || strpos($message_lower, 'who are you') !== false || strpos($message_lower, 'company') !== false) {
        $response = $responses['about'];
    }

    wp_send_json_success(array(
        'message' => $response,
        'timestamp' => current_time('c'),
    ));
}
add_action('wp_ajax_recruitpro_ai_chat', 'recruitpro_ai_chat_handler');
add_action('wp_ajax_nopriv_recruitpro_ai_chat', 'recruitpro_ai_chat_handler');

/**
 * Job Application Form AJAX Handler (Connects to CRM if available)
 */
function recruitpro_job_application_handler() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_jobs_nonce')) {
        wp_die('Security check failed');
    }

    // Sanitize form data
    $application_data = array(
        'job_id'        => intval($_POST['job_id']),
        'name'          => sanitize_text_field($_POST['name']),
        'email'         => sanitize_email($_POST['email']),
        'phone'         => sanitize_text_field($_POST['phone']),
        'message'       => sanitize_textarea_field($_POST['message']),
        'submitted_at'  => current_time('mysql'),
        'ip_address'    => $_SERVER['REMOTE_ADDR'],
    );

    // Handle CV file upload
    if (!empty($_FILES['cv_file']['name'])) {
        $uploaded_file = wp_handle_upload($_FILES['cv_file'], array('test_form' => false));
        
        if ($uploaded_file && !isset($uploaded_file['error'])) {
            $application_data['cv_file_url'] = $uploaded_file['url'];
            $application_data['cv_file_path'] = $uploaded_file['file'];
        }
    }

    // If CRM plugin is active, send data there
    if (recruitpro_has_crm() && function_exists('recruitpro_crm_process_application')) {
        $result = recruitpro_crm_process_application($application_data);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => esc_html__('Your application has been submitted successfully!', 'recruitpro'),
                'redirect' => false,
            ));
        } else {
            wp_send_json_error(array(
                'message' => esc_html__('There was an error processing your application. Please try again.', 'recruitpro'),
            ));
        }
    } else {
        // Fallback: Store in WordPress options table temporarily
        $applications = get_option('recruitpro_pending_applications', array());
        $application_data['id'] = uniqid('app_');
        $applications[] = $application_data;
        update_option('recruitpro_pending_applications', $applications);

        // Send notification email to admin
        $admin_email = get_option('admin_email');
        $subject = sprintf(esc_html__('New Job Application - %s', 'recruitpro'), $application_data['name']);
        $message = sprintf(
            esc_html__('New job application received:\n\nName: %s\nEmail: %s\nPhone: %s\nJob ID: %d\nMessage: %s', 'recruitpro'),
            $application_data['name'],
            $application_data['email'],
            $application_data['phone'],
            $application_data['job_id'],
            $application_data['message']
        );
        
        wp_mail($admin_email, $subject, $message);

        wp_send_json_success(array(
            'message' => esc_html__('Your application has been received! We will contact you soon.', 'recruitpro'),
            'redirect' => false,
        ));
    }
}
add_action('wp_ajax_recruitpro_job_application', 'recruitpro_job_application_handler');
add_action('wp_ajax_nopriv_recruitpro_job_application', 'recruitpro_job_application_handler');

/**
 * Contact Form AJAX Handler
 */
function recruitpro_contact_form_handler() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_theme_nonce')) {
        wp_die('Security check failed');
    }

    // Sanitize form data
    $contact_data = array(
        'name'          => sanitize_text_field($_POST['name']),
        'email'         => sanitize_email($_POST['email']),
        'phone'         => sanitize_text_field($_POST['phone']),
        'subject'       => sanitize_text_field($_POST['subject']),
        'message'       => sanitize_textarea_field($_POST['message']),
        'submitted_at'  => current_time('mysql'),
        'ip_address'    => $_SERVER['REMOTE_ADDR'],
    );

    // Send notification email to admin
    $admin_email = get_option('admin_email');
    $subject = sprintf(esc_html__('New Contact Form Message - %s', 'recruitpro'), $contact_data['subject']);
    $message = sprintf(
        esc_html__('New contact form message:\n\nName: %s\nEmail: %s\nPhone: %s\nSubject: %s\nMessage: %s', 'recruitpro'),
        $contact_data['name'],
        $contact_data['email'],
        $contact_data['phone'],
        $contact_data['subject'],
        $contact_data['message']
    );
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    if (wp_mail($admin_email, $subject, $message, $headers)) {
        wp_send_json_success(array(
            'message' => esc_html__('Thank you for your message! We will get back to you soon.', 'recruitpro'),
        ));
    } else {
        wp_send_json_error(array(
            'message' => esc_html__('There was an error sending your message. Please try again.', 'recruitpro'),
        ));
    }
}
add_action('wp_ajax_recruitpro_contact_form', 'recruitpro_contact_form_handler');
add_action('wp_ajax_nopriv_recruitpro_contact_form', 'recruitpro_contact_form_handler');

/**
 * Newsletter Signup AJAX Handler
 */
function recruitpro_newsletter_signup_handler() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_theme_nonce')) {
        wp_die('Security check failed');
    }

    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => esc_html__('Please enter a valid email address.', 'recruitpro'),
        ));
    }

    // If CRM plugin is active, add to newsletter list
    if (recruitpro_has_crm() && function_exists('recruitpro_crm_add_newsletter_subscriber')) {
        $result = recruitpro_crm_add_newsletter_subscriber($email);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => esc_html__('Thank you for subscribing to our newsletter!', 'recruitpro'),
            ));
        } else {
            wp_send_json_error(array(
                'message' => esc_html__('This email is already subscribed or there was an error.', 'recruitpro'),
            ));
        }
    } else {
        // Fallback: Store in WordPress options
        $subscribers = get_option('recruitpro_newsletter_subscribers', array());
        
        if (!in_array($email, $subscribers)) {
            $subscribers[] = $email;
            update_option('recruitpro_newsletter_subscribers', $subscribers);
            
            wp_send_json_success(array(
                'message' => esc_html__('Thank you for subscribing to our newsletter!', 'recruitpro'),
            ));
        } else {
            wp_send_json_error(array(
                'message' => esc_html__('This email is already subscribed.', 'recruitpro'),
            ));
        }
    }
}
add_action('wp_ajax_recruitpro_newsletter_signup', 'recruitpro_newsletter_signup_handler');
add_action('wp_ajax_nopriv_recruitpro_newsletter_signup', 'recruitpro_newsletter_signup_handler');

/**
 * Custom body classes for better styling control
 */
function recruitpro_body_classes($classes) {
    // Add theme version class
    $classes[] = 'recruitpro-theme';
    
    // Add CRM integration status
    if (recruitpro_has_crm()) {
        $classes[] = 'has-crm-integration';
    }
    
    // Add jobs plugin status
    if (recruitpro_has_jobs_plugin()) {
        $classes[] = 'has-jobs-integration';
    }
    
    // Add page-specific classes
    if (is_front_page()) {
        $classes[] = 'front-page';
    }
    
    if (recruitpro_is_jobs_page()) {
        $classes[] = 'jobs-page';
    }
    
    return $classes;
}
add_filter('body_class', 'recruitpro_body_classes');

/**
 * Add custom post classes
 */
function recruitpro_post_classes($classes, $class, $post_id) {
    // Add recruitment-specific classes
    if (get_post_type($post_id) === 'job') {
        $classes[] = 'job-listing';
        
        // Add featured job class
        if (get_post_meta($post_id, '_featured_job', true)) {
            $classes[] = 'featured-job';
        }
    }
    
    return $classes;
}
add_filter('post_class', 'recruitpro_post_classes', 10, 3);

/**
 * Admin notice if required plugins are not active
 */
function recruitpro_admin_notices() {
    if (!recruitpro_has_crm()) {
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('RecruitPro Theme: The RecruitPro CRM plugin is recommended for full functionality.', 'recruitpro');
        echo '</p></div>';
    }
}
add_action('admin_notices', 'recruitpro_admin_notices');

/**
 * Theme activation hook
 */
function recruitpro_theme_activation() {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set default theme options
    if (!get_theme_mod('recruitpro_primary_color')) {
        set_theme_mod('recruitpro_primary_color', '#2563eb');
    }
    
    if (!get_theme_mod('recruitpro_secondary_color')) {
        set_theme_mod('recruitpro_secondary_color', '#059669');
    }
    
    // Create default pages if they don't exist
    $default_pages = array(
        'about'     => 'About Us',
        'services'  => 'Our Services',
        'contact'   => 'Contact Us',
        'jobs'      => 'Current Opportunities',
        'privacy'   => 'Privacy Policy',
        'terms'     => 'Terms of Service',
    );
    
    foreach ($default_pages as $slug => $title) {
        if (!get_page_by_path($slug)) {
            wp_insert_post(array(
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => sprintf(esc_html__('This is the %s page. Please edit this page to add your content.', 'recruitpro'), $title),
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ));
        }
    }
}
add_action('after_switch_theme', 'recruitpro_theme_activation');

// End of functions.php