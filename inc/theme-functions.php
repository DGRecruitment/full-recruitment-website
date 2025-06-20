<?php
/**
 * RecruitPro Theme Core Functions
 *
 * This file contains the core functionality and helper functions for the
 * RecruitPro recruitment website theme. It includes utility functions,
 * color scheme management, customizer helpers, plugin integration checks,
 * and recruitment-specific functionality.
 *
 * @package RecruitPro
 * @subpackage Theme/Functions
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/theme-functions.php
 * Purpose: Core theme functionality and utilities
 * Dependencies: WordPress core
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme version constant
 */
if (!defined('RECRUITPRO_THEME_VERSION')) {
    define('RECRUITPRO_THEME_VERSION', '1.0.0');
}

/**
 * =================================================================
 * CORE UTILITY FUNCTIONS
 * =================================================================
 */

if (!function_exists('recruitpro_get_theme_version')) :
    /**
     * Get theme version
     *
     * @since 1.0.0
     * @return string Theme version
     */
    function recruitpro_get_theme_version() {
        return RECRUITPRO_THEME_VERSION;
    }
endif;

if (!function_exists('recruitpro_get_option')) :
    /**
     * Get theme customizer option with fallback
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $default Default value
     * @return mixed Option value or default
     */
    function recruitpro_get_option($option_name, $default = '') {
        return get_theme_mod($option_name, $default);
    }
endif;

if (!function_exists('recruitpro_is_development_mode')) :
    /**
     * Check if theme is in development mode
     *
     * @since 1.0.0
     * @return bool True if in development mode
     */
    function recruitpro_is_development_mode() {
        return defined('WP_DEBUG') && WP_DEBUG && defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
    }
endif;

if (!function_exists('recruitpro_get_asset_url')) :
    /**
     * Get asset URL with version parameter
     *
     * @since 1.0.0
     * @param string $asset_path Asset path relative to theme directory
     * @return string Full asset URL with version
     */
    function recruitpro_get_asset_url($asset_path) {
        $url = get_template_directory_uri() . '/' . ltrim($asset_path, '/');
        $version = recruitpro_is_development_mode() ? time() : recruitpro_get_theme_version();
        return add_query_arg('ver', $version, $url);
    }
endif;

/**
 * =================================================================
 * PLUGIN INTEGRATION HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_has_crm')) :
    /**
     * Check if CRM plugin is active and functional
     *
     * @since 1.0.0
     * @return bool True if CRM plugin is available
     */
    function recruitpro_has_crm() {
        return class_exists('RecruitPro_CRM') && function_exists('recruitpro_crm_is_active');
    }
endif;

if (!function_exists('recruitpro_has_jobs_plugin')) :
    /**
     * Check if Jobs plugin is active
     *
     * @since 1.0.0
     * @return bool True if Jobs plugin is available
     */
    function recruitpro_has_jobs_plugin() {
        return class_exists('RecruitPro_Jobs');
    }
endif;

if (!function_exists('recruitpro_has_seo_plugin')) :
    /**
     * Check if SEO plugin is active
     *
     * @since 1.0.0
     * @return bool True if SEO plugin is available
     */
    function recruitpro_has_seo_plugin() {
        return class_exists('RecruitPro_SEO');
    }
endif;

if (!function_exists('recruitpro_has_social_plugin')) :
    /**
     * Check if Social Media plugin is active
     *
     * @since 1.0.0
     * @return bool True if Social plugin is available
     */
    function recruitpro_has_social_plugin() {
        return class_exists('RecruitPro_Social_Media');
    }
endif;

if (!function_exists('recruitpro_has_forms_plugin')) :
    /**
     * Check if Forms plugin is active
     *
     * @since 1.0.0
     * @return bool True if Forms plugin is available
     */
    function recruitpro_has_forms_plugin() {
        return class_exists('RecruitPro_Forms');
    }
endif;

if (!function_exists('recruitpro_has_security_plugin')) :
    /**
     * Check if Security plugin is active
     *
     * @since 1.0.0
     * @return bool True if Security plugin is available
     */
    function recruitpro_has_security_plugin() {
        return class_exists('RecruitPro_Security');
    }
endif;

/**
 * =================================================================
 * PAGE AND CONTENT TYPE DETECTION
 * =================================================================
 */

if (!function_exists('recruitpro_is_jobs_page')) :
    /**
     * Check if current page is jobs-related
     *
     * @since 1.0.0
     * @return bool True if on jobs page
     */
    function recruitpro_is_jobs_page() {
        return (is_post_type_archive('job') || is_tax('job_category') || is_tax('job_location') || is_tax('job_type') || is_singular('job'));
    }
endif;

if (!function_exists('recruitpro_is_recruitment_page')) :
    /**
     * Check if current page is recruitment-related
     *
     * @since 1.0.0
     * @return bool True if on recruitment page
     */
    function recruitpro_is_recruitment_page() {
        return (
            recruitpro_is_jobs_page() ||
            is_singular(array('testimonial', 'team_member', 'service', 'success_story')) ||
            is_post_type_archive(array('testimonial', 'team_member', 'service', 'success_story')) ||
            is_tax(array('recruitment_industry', 'recruitment_specialization', 'skill_category'))
        );
    }
endif;

if (!function_exists('recruitpro_is_company_page')) :
    /**
     * Check if current page is company-related
     *
     * @since 1.0.0
     * @return bool True if on company page
     */
    function recruitpro_is_company_page() {
        $company_pages = array('about', 'about-us', 'team', 'our-team', 'company', 'services', 'our-services');
        $current_page = get_post_field('post_name', get_queried_object_id());
        
        return in_array($current_page, $company_pages) || is_page_template(array(
            'page-templates/page-about.php',
            'page-templates/page-team.php',
            'page-templates/page-services.php'
        ));
    }
endif;

/**
 * =================================================================
 * COLOR SCHEME MANAGEMENT
 * =================================================================
 */

if (!function_exists('recruitpro_get_color_schemes')) :
    /**
     * Get available color schemes
     *
     * @since 1.0.0
     * @return array Color schemes
     */
    function recruitpro_get_color_schemes() {
        $schemes = array(
            'professional_blue' => array(
                'label' => __('Professional Blue', 'recruitpro'),
                'description' => __('Trust and professionalism - ideal for corporate recruitment', 'recruitpro'),
                'colors' => array(
                    'primary' => '#0073aa',
                    'secondary' => '#005177',
                    'accent' => '#00a0d2',
                    'text_primary' => '#333333',
                    'text_secondary' => '#666666',
                    'text_light' => '#999999',
                    'background' => '#ffffff',
                    'background_alt' => '#f8f9fa',
                    'border' => '#e5e7eb',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'error' => '#ef4444',
                ),
            ),
            'executive_navy' => array(
                'label' => __('Executive Navy', 'recruitpro'),
                'description' => __('Premium and authoritative - perfect for executive search', 'recruitpro'),
                'colors' => array(
                    'primary' => '#1e3a8a',
                    'secondary' => '#1e40af',
                    'accent' => '#3b82f6',
                    'text_primary' => '#1f2937',
                    'text_secondary' => '#4b5563',
                    'text_light' => '#6b7280',
                    'background' => '#ffffff',
                    'background_alt' => '#f1f5f9',
                    'border' => '#d1d5db',
                    'success' => '#059669',
                    'warning' => '#d97706',
                    'error' => '#dc2626',
                ),
            ),
            'modern_green' => array(
                'label' => __('Modern Green', 'recruitpro'),
                'description' => __('Growth and innovation - great for tech recruitment', 'recruitpro'),
                'colors' => array(
                    'primary' => '#10b981',
                    'secondary' => '#059669',
                    'accent' => '#34d399',
                    'text_primary' => '#1f2937',
                    'text_secondary' => '#374151',
                    'text_light' => '#6b7280',
                    'background' => '#ffffff',
                    'background_alt' => '#f0fdf4',
                    'border' => '#d1d5db',
                    'success' => '#16a34a',
                    'warning' => '#ca8a04',
                    'error' => '#dc2626',
                ),
            ),
            'creative_purple' => array(
                'label' => __('Creative Purple', 'recruitpro'),
                'description' => __('Innovation and creativity - ideal for creative industries', 'recruitpro'),
                'colors' => array(
                    'primary' => '#7c3aed',
                    'secondary' => '#6d28d9',
                    'accent' => '#8b5cf6',
                    'text_primary' => '#1f2937',
                    'text_secondary' => '#374151',
                    'text_light' => '#6b7280',
                    'background' => '#ffffff',
                    'background_alt' => '#faf5ff',
                    'border' => '#d1d5db',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'error' => '#ef4444',
                ),
            ),
            'warm_orange' => array(
                'label' => __('Warm Orange', 'recruitpro'),
                'description' => __('Energy and enthusiasm - perfect for dynamic agencies', 'recruitpro'),
                'colors' => array(
                    'primary' => '#ea580c',
                    'secondary' => '#c2410c',
                    'accent' => '#fb923c',
                    'text_primary' => '#1f2937',
                    'text_secondary' => '#374151',
                    'text_light' => '#6b7280',
                    'background' => '#ffffff',
                    'background_alt' => '#fff7ed',
                    'border' => '#d1d5db',
                    'success' => '#10b981',
                    'warning' => '#d97706',
                    'error' => '#dc2626',
                ),
            ),
            'minimal_gray' => array(
                'label' => __('Minimal Gray', 'recruitpro'),
                'description' => __('Clean and sophisticated - universal professional appeal', 'recruitpro'),
                'colors' => array(
                    'primary' => '#374151',
                    'secondary' => '#1f2937',
                    'accent' => '#6b7280',
                    'text_primary' => '#111827',
                    'text_secondary' => '#374151',
                    'text_light' => '#6b7280',
                    'background' => '#ffffff',
                    'background_alt' => '#f9fafb',
                    'border' => '#e5e7eb',
                    'success' => '#10b981',
                    'warning' => '#f59e0b',
                    'error' => '#ef4444',
                ),
            ),
        );

        return apply_filters('recruitpro_color_schemes', $schemes);
    }
endif;

if (!function_exists('recruitpro_get_current_color_scheme')) :
    /**
     * Get current active color scheme
     *
     * @since 1.0.0
     * @return array Current color scheme
     */
    function recruitpro_get_current_color_scheme() {
        $schemes = recruitpro_get_color_schemes();
        $current_scheme = get_theme_mod('recruitpro_color_scheme', 'professional_blue');
        
        return isset($schemes[$current_scheme]) ? $schemes[$current_scheme] : $schemes['professional_blue'];
    }
endif;

if (!function_exists('recruitpro_get_color')) :
    /**
     * Get specific color from current scheme
     *
     * @since 1.0.0
     * @param string $color_name Color name
     * @param string $default Default color value
     * @return string Color value
     */
    function recruitpro_get_color($color_name, $default = '#000000') {
        $scheme = recruitpro_get_current_color_scheme();
        
        // Try custom color override first
        $custom_color = get_theme_mod('recruitpro_custom_' . $color_name);
        if ($custom_color) {
            return $custom_color;
        }
        
        // Use scheme color
        if (isset($scheme['colors'][$color_name])) {
            return $scheme['colors'][$color_name];
        }
        
        return $default;
    }
endif;

/**
 * =================================================================
 * TYPOGRAPHY HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_google_fonts')) :
    /**
     * Get Google Fonts configuration
     *
     * @since 1.0.0
     * @return array Google Fonts configuration
     */
    function recruitpro_get_google_fonts() {
        $fonts = array(
            'Inter' => array(
                'label' => 'Inter',
                'weights' => array(300, 400, 500, 600, 700),
                'category' => 'sans-serif',
                'recommended_for' => array('body', 'ui'),
            ),
            'Poppins' => array(
                'label' => 'Poppins',
                'weights' => array(300, 400, 500, 600, 700, 800),
                'category' => 'sans-serif',
                'recommended_for' => array('headings'),
            ),
            'Roboto' => array(
                'label' => 'Roboto',
                'weights' => array(300, 400, 500, 700),
                'category' => 'sans-serif',
                'recommended_for' => array('body', 'ui'),
            ),
            'Open Sans' => array(
                'label' => 'Open Sans',
                'weights' => array(300, 400, 600, 700),
                'category' => 'sans-serif',
                'recommended_for' => array('body'),
            ),
            'Merriweather' => array(
                'label' => 'Merriweather',
                'weights' => array(300, 400, 700),
                'category' => 'serif',
                'recommended_for' => array('headings', 'content'),
            ),
            'Playfair Display' => array(
                'label' => 'Playfair Display',
                'weights' => array(400, 500, 600, 700),
                'category' => 'serif',
                'recommended_for' => array('headings'),
            ),
        );

        return apply_filters('recruitpro_google_fonts', $fonts);
    }
endif;

if (!function_exists('recruitpro_get_font_url')) :
    /**
     * Get Google Fonts URL
     *
     * @since 1.0.0
     * @return string Font URL
     */
    function recruitpro_get_font_url() {
        $font_families = array();
        $subsets = 'latin,latin-ext';

        // Get selected fonts
        $body_font = get_theme_mod('recruitpro_body_font', 'Inter');
        $heading_font = get_theme_mod('recruitpro_heading_font', 'Poppins');

        $fonts_config = recruitpro_get_google_fonts();

        // Add body font
        if (isset($fonts_config[$body_font])) {
            $weights = implode(',', $fonts_config[$body_font]['weights']);
            $font_families[] = $body_font . ':' . $weights;
        }

        // Add heading font if different
        if ($heading_font !== $body_font && isset($fonts_config[$heading_font])) {
            $weights = implode(',', $fonts_config[$heading_font]['weights']);
            $font_families[] = $heading_font . ':' . $weights;
        }

        if (empty($font_families)) {
            return '';
        }

        $query_args = array(
            'family' => urlencode(implode('|', $font_families)),
            'subset' => urlencode($subsets),
            'display' => 'swap',
        );

        return add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }
endif;

/**
 * =================================================================
 * COMPANY INFORMATION HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_company_info')) :
    /**
     * Get company information
     *
     * @since 1.0.0
     * @return array Company information
     */
    function recruitpro_get_company_info() {
        return array(
            'name' => get_theme_mod('recruitpro_company_name', get_bloginfo('name')),
            'description' => get_theme_mod('recruitpro_company_description', get_bloginfo('description')),
            'logo' => get_theme_mod('recruitpro_company_logo', ''),
            'address' => get_theme_mod('recruitpro_company_address', ''),
            'phone' => get_theme_mod('recruitpro_company_phone', ''),
            'email' => get_theme_mod('recruitpro_company_email', get_option('admin_email')),
            'founded' => get_theme_mod('recruitpro_company_founded', ''),
            'employees' => get_theme_mod('recruitpro_company_employees', ''),
            'industry' => get_theme_mod('recruitpro_company_industry', 'Recruitment'),
            'specializations' => get_theme_mod('recruitpro_company_specializations', array()),
        );
    }
endif;

if (!function_exists('recruitpro_get_contact_info')) :
    /**
     * Get formatted contact information
     *
     * @since 1.0.0
     * @return array Contact information
     */
    function recruitpro_get_contact_info() {
        $company_info = recruitpro_get_company_info();
        
        return array(
            'phone' => array(
                'raw' => $company_info['phone'],
                'formatted' => recruitpro_format_phone($company_info['phone']),
                'url' => 'tel:' . preg_replace('/[^0-9+]/', '', $company_info['phone']),
            ),
            'email' => array(
                'raw' => $company_info['email'],
                'formatted' => $company_info['email'],
                'url' => 'mailto:' . $company_info['email'],
            ),
            'address' => array(
                'raw' => $company_info['address'],
                'formatted' => nl2br($company_info['address']),
                'url' => 'https://maps.google.com/?q=' . urlencode($company_info['address']),
            ),
        );
    }
endif;

if (!function_exists('recruitpro_format_phone')) :
    /**
     * Format phone number for display
     *
     * @since 1.0.0
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    function recruitpro_format_phone($phone) {
        // Remove all non-numeric characters except +
        $clean = preg_replace('/[^0-9+]/', '', $phone);
        
        // Basic formatting for common patterns
        if (strlen($clean) === 10) {
            return sprintf('(%s) %s-%s', 
                substr($clean, 0, 3),
                substr($clean, 3, 3),
                substr($clean, 6, 4)
            );
        } elseif (strlen($clean) === 11 && substr($clean, 0, 1) === '1') {
            return sprintf('+1 (%s) %s-%s',
                substr($clean, 1, 3),
                substr($clean, 4, 3),
                substr($clean, 7, 4)
            );
        }
        
        return $phone; // Return original if no pattern matches
    }
endif;

/**
 * =================================================================
 * SOCIAL MEDIA HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_social_profiles')) :
    /**
     * Get social media profiles
     *
     * @since 1.0.0
     * @return array Social media profiles
     */
    function recruitpro_get_social_profiles() {
        $profiles = array(
            'linkedin' => get_theme_mod('recruitpro_social_linkedin', ''),
            'facebook' => get_theme_mod('recruitpro_social_facebook', ''),
            'twitter' => get_theme_mod('recruitpro_social_twitter', ''),
            'instagram' => get_theme_mod('recruitpro_social_instagram', ''),
            'youtube' => get_theme_mod('recruitpro_social_youtube', ''),
            'glassdoor' => get_theme_mod('recruitpro_social_glassdoor', ''),
            'indeed' => get_theme_mod('recruitpro_social_indeed', ''),
        );

        // Remove empty profiles
        return array_filter($profiles);
    }
endif;

/**
 * =================================================================
 * CONTENT AND LAYOUT HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_content_layout')) :
    /**
     * Get content layout for current page
     *
     * @since 1.0.0
     * @return string Layout type
     */
    function recruitpro_get_content_layout() {
        $layout = 'sidebar-right'; // Default

        if (is_singular()) {
            $post_layout = get_post_meta(get_the_ID(), '_recruitpro_layout', true);
            if ($post_layout) {
                $layout = $post_layout;
            } elseif (is_page()) {
                $layout = get_theme_mod('recruitpro_page_layout', 'fullwidth');
            } elseif (is_single()) {
                $layout = get_theme_mod('recruitpro_single_layout', 'sidebar-right');
            }
        } elseif (is_home() || is_archive()) {
            $layout = get_theme_mod('recruitpro_archive_layout', 'sidebar-right');
        } elseif (recruitpro_is_jobs_page()) {
            $layout = get_theme_mod('recruitpro_jobs_layout', 'sidebar-right');
        }

        return apply_filters('recruitpro_content_layout', $layout);
    }
endif;

if (!function_exists('recruitpro_has_sidebar')) :
    /**
     * Check if current page should have sidebar
     *
     * @since 1.0.0
     * @return bool True if page has sidebar
     */
    function recruitpro_has_sidebar() {
        $layout = recruitpro_get_content_layout();
        return in_array($layout, array('sidebar-left', 'sidebar-right'));
    }
endif;

if (!function_exists('recruitpro_get_sidebar_position')) :
    /**
     * Get sidebar position for current page
     *
     * @since 1.0.0
     * @return string Sidebar position (left|right|none)
     */
    function recruitpro_get_sidebar_position() {
        $layout = recruitpro_get_content_layout();
        
        if ($layout === 'sidebar-left') {
            return 'left';
        } elseif ($layout === 'sidebar-right') {
            return 'right';
        }
        
        return 'none';
    }
endif;

/**
 * =================================================================
 * RECRUITMENT SPECIFIC HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_job_types')) :
    /**
     * Get available job types
     *
     * @since 1.0.0
     * @return array Job types
     */
    function recruitpro_get_job_types() {
        $types = array(
            'full-time' => __('Full Time', 'recruitpro'),
            'part-time' => __('Part Time', 'recruitpro'),
            'contract' => __('Contract', 'recruitpro'),
            'temporary' => __('Temporary', 'recruitpro'),
            'freelance' => __('Freelance', 'recruitpro'),
            'internship' => __('Internship', 'recruitpro'),
            'volunteer' => __('Volunteer', 'recruitpro'),
        );

        return apply_filters('recruitpro_job_types', $types);
    }
endif;

if (!function_exists('recruitpro_get_experience_levels')) :
    /**
     * Get available experience levels
     *
     * @since 1.0.0
     * @return array Experience levels
     */
    function recruitpro_get_experience_levels() {
        $levels = array(
            'entry-level' => __('Entry Level', 'recruitpro'),
            'junior' => __('Junior (1-3 years)', 'recruitpro'),
            'mid-level' => __('Mid-Level (3-7 years)', 'recruitpro'),
            'senior' => __('Senior (7+ years)', 'recruitpro'),
            'lead' => __('Lead/Principal', 'recruitpro'),
            'management' => __('Management', 'recruitpro'),
            'executive' => __('Executive/C-Level', 'recruitpro'),
        );

        return apply_filters('recruitpro_experience_levels', $levels);
    }
endif;

if (!function_exists('recruitpro_get_industries')) :
    /**
     * Get available industries
     *
     * @since 1.0.0
     * @return array Industries
     */
    function recruitpro_get_industries() {
        $industries = array(
            'technology' => __('Technology', 'recruitpro'),
            'healthcare' => __('Healthcare', 'recruitpro'),
            'finance' => __('Finance & Banking', 'recruitpro'),
            'manufacturing' => __('Manufacturing', 'recruitpro'),
            'retail' => __('Retail & E-commerce', 'recruitpro'),
            'education' => __('Education', 'recruitpro'),
            'construction' => __('Construction', 'recruitpro'),
            'hospitality' => __('Hospitality & Tourism', 'recruitpro'),
            'energy' => __('Energy & Utilities', 'recruitpro'),
            'government' => __('Government & Public Sector', 'recruitpro'),
            'non-profit' => __('Non-Profit', 'recruitpro'),
            'media' => __('Media & Entertainment', 'recruitpro'),
            'transportation' => __('Transportation & Logistics', 'recruitpro'),
            'real-estate' => __('Real Estate', 'recruitpro'),
            'legal' => __('Legal Services', 'recruitpro'),
        );

        return apply_filters('recruitpro_industries', $industries);
    }
endif;

/**
 * =================================================================
 * PERFORMANCE AND OPTIMIZATION HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_image_sizes')) :
    /**
     * Get custom image sizes
     *
     * @since 1.0.0
     * @return array Image sizes
     */
    function recruitpro_get_image_sizes() {
        return array(
            'recruitpro-hero' => array(1920, 1080, true),
            'recruitpro-featured' => array(800, 450, true),
            'recruitpro-team' => array(400, 400, true),
            'recruitpro-testimonial' => array(300, 300, true),
            'recruitpro-service' => array(600, 400, true),
            'recruitpro-blog' => array(600, 400, true),
        );
    }
endif;

if (!function_exists('recruitpro_optimize_query')) :
    /**
     * Optimize WordPress queries for performance
     *
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    function recruitpro_optimize_query($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Optimize home page query
        if (is_home()) {
            $posts_per_page = get_theme_mod('recruitpro_blog_posts_per_page', 10);
            $query->set('posts_per_page', $posts_per_page);
        }

        // Optimize search results
        if (is_search()) {
            $query->set('posts_per_page', 10);
            // Exclude certain post types from search if needed
            if (!recruitpro_has_jobs_plugin()) {
                $query->set('post_type', array('post', 'page'));
            }
        }
    }
endif;

/**
 * =================================================================
 * SANITIZATION AND VALIDATION HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_sanitize_select')) :
    /**
     * Sanitize select field
     *
     * @since 1.0.0
     * @param string $input Input value
     * @param array $valid_choices Valid choices
     * @return string Sanitized value
     */
    function recruitpro_sanitize_select($input, $valid_choices = array()) {
        if (empty($valid_choices)) {
            return sanitize_text_field($input);
        }
        
        return array_key_exists($input, $valid_choices) ? $input : '';
    }
endif;

if (!function_exists('recruitpro_sanitize_color_scheme')) :
    /**
     * Sanitize color scheme choice
     *
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    function recruitpro_sanitize_color_scheme($input) {
        $valid_schemes = array_keys(recruitpro_get_color_schemes());
        return in_array($input, $valid_schemes) ? $input : 'professional_blue';
    }
endif;

if (!function_exists('recruitpro_sanitize_checkbox')) :
    /**
     * Sanitize checkbox field
     *
     * @since 1.0.0
     * @param bool $input Input value
     * @return bool Sanitized value
     */
    function recruitpro_sanitize_checkbox($input) {
        return (bool) $input;
    }
endif;

if (!function_exists('recruitpro_sanitize_number_range')) :
    /**
     * Sanitize number within range
     *
     * @since 1.0.0
     * @param int $input Input value
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return int Sanitized value
     */
    function recruitpro_sanitize_number_range($input, $min = 1, $max = 100) {
        $number = absint($input);
        return min(max($number, $min), $max);
    }
endif;

/**
 * =================================================================
 * ERROR HANDLING AND DEBUGGING
 * =================================================================
 */

if (!function_exists('recruitpro_log_error')) :
    /**
     * Log error for debugging
     *
     * @since 1.0.0
     * @param string $message Error message
     * @param array $context Additional context
     * @return void
     */
    function recruitpro_log_error($message, $context = array()) {
        if (!recruitpro_is_development_mode()) {
            return;
        }

        error_log(sprintf(
            '[RecruitPro Theme] %s | Context: %s',
            $message,
            wp_json_encode($context)
        ));
    }
endif;

if (!function_exists('recruitpro_handle_template_error')) :
    /**
     * Handle template rendering errors gracefully
     *
     * @since 1.0.0
     * @param string $template_name Template name
     * @param string $fallback_content Fallback content
     * @return void
     */
    function recruitpro_handle_template_error($template_name, $fallback_content = '') {
        if (recruitpro_is_development_mode()) {
            echo '<div class="recruitpro-template-error">';
            echo '<p><strong>Template Error:</strong> ' . esc_html($template_name) . ' could not be loaded.</p>';
            echo '</div>';
        } elseif ($fallback_content) {
            echo $fallback_content;
        }
    }
endif;

/**
 * =================================================================
 * THEME CUSTOMIZER HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_get_customizer_defaults')) :
    /**
     * Get customizer default values
     *
     * @since 1.0.0
     * @return array Default values
     */
    function recruitpro_get_customizer_defaults() {
        return array(
            // Site Identity
            'recruitpro_logo' => '',
            'recruitpro_logo_width' => 200,
            'recruitpro_favicon' => '',
            
            // Colors
            'recruitpro_color_scheme' => 'professional_blue',
            'recruitpro_custom_primary' => '',
            'recruitpro_custom_secondary' => '',
            'recruitpro_custom_accent' => '',
            
            // Typography
            'recruitpro_body_font' => 'Inter',
            'recruitpro_heading_font' => 'Poppins',
            'recruitpro_font_size_base' => 16,
            'recruitpro_line_height_base' => 1.6,
            
            // Layout
            'recruitpro_content_layout' => 'sidebar-right',
            'recruitpro_sidebar_width' => 300,
            'recruitpro_content_width' => 1200,
            
            // Header
            'recruitpro_header_layout' => 'default',
            'recruitpro_header_sticky' => true,
            'recruitpro_header_search' => true,
            
            // Footer
            'recruitpro_footer_layout' => 'four_columns',
            'recruitpro_footer_copyright' => '',
            'recruitpro_footer_social' => true,
            
            // Company Info
            'recruitpro_company_name' => get_bloginfo('name'),
            'recruitpro_company_description' => get_bloginfo('description'),
            'recruitpro_company_phone' => '',
            'recruitpro_company_email' => get_option('admin_email'),
            'recruitpro_company_address' => '',
            
            // Performance
            'recruitpro_enable_lazy_loading' => true,
            'recruitpro_optimize_images' => true,
            'recruitpro_minify_css' => false,
            'recruitpro_minify_js' => false,
        );
    }
endif;

/**
 * =================================================================
 * INITIALIZATION AND SETUP
 * =================================================================
 */

/**
 * Initialize theme functions
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_init_theme_functions() {
    // Add image sizes
    $image_sizes = recruitpro_get_image_sizes();
    foreach ($image_sizes as $name => $size) {
        add_image_size($name, $size[0], $size[1], $size[2]);
    }

    // Optimize queries
    add_action('pre_get_posts', 'recruitpro_optimize_query');

    // Add theme support for various features
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add custom logo support
    add_theme_support('custom-logo', array(
        'height' => 250,
        'width' => 250,
        'flex-width' => true,
        'flex-height' => true,
    ));

    // Editor color palette
    $color_scheme = recruitpro_get_current_color_scheme();
    if (isset($color_scheme['colors'])) {
        $editor_colors = array();
        foreach ($color_scheme['colors'] as $name => $color) {
            $editor_colors[] = array(
                'name' => ucwords(str_replace('_', ' ', $name)),
                'slug' => str_replace('_', '-', $name),
                'color' => $color,
            );
        }
        add_theme_support('editor-color-palette', $editor_colors);
    }
}

// Initialize theme functions
add_action('after_setup_theme', 'recruitpro_init_theme_functions');

/**
 * =================================================================
 * PLUGIN COMPATIBILITY AND INTEGRATION
 * =================================================================
 */

/**
 * Ensure theme doesn't conflict with plugins
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_prevent_plugin_conflicts() {
    // Remove theme actions that might conflict with plugins
    if (recruitpro_has_seo_plugin()) {
        // Let SEO plugin handle meta tags
        remove_action('wp_head', 'recruitpro_output_basic_meta_tags');
    }

    if (recruitpro_has_social_plugin()) {
        // Let Social plugin handle sharing
        remove_action('the_content', 'recruitpro_add_social_sharing_buttons');
    }

    // Allow plugins to modify theme behavior
    do_action('recruitpro_theme_loaded');
}

add_action('init', 'recruitpro_prevent_plugin_conflicts');

/**
 * Theme compatibility notice
 *
 * @since 1.0.0
 * @param string $plugin_name Plugin name
 * @param string $minimum_version Minimum required version
 * @return void
 */
function recruitpro_compatibility_notice($plugin_name, $minimum_version = '') {
    $message = sprintf(
        __('RecruitPro theme works best with %s plugin.', 'recruitpro'),
        $plugin_name
    );
    
    if ($minimum_version) {
        $message .= sprintf(
            __(' Minimum version required: %s', 'recruitpro'),
            $minimum_version
        );
    }

    echo '<div class="notice notice-info"><p>' . esc_html($message) . '</p></div>';
}

/**
 * Check PHP version compatibility
 *
 * @since 1.0.0
 * @return bool True if PHP version is compatible
 */
function recruitpro_check_php_version() {
    $required_php = '7.4';
    if (version_compare(PHP_VERSION, $required_php, '<')) {
        add_action('admin_notices', function() use ($required_php) {
            echo '<div class="notice notice-error"><p>';
            printf(
                __('RecruitPro theme requires PHP %s or higher. You are running %s.', 'recruitpro'),
                $required_php,
                PHP_VERSION
            );
            echo '</p></div>';
        });
        return false;
    }
    return true;
}

// Check PHP version on theme activation
add_action('after_switch_theme', 'recruitpro_check_php_version');

/**
 * =================================================================
 * CLEANUP AND OPTIMIZATION
 * =================================================================
 */

/**
 * Clean up WordPress head
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_cleanup_wp_head() {
    // Remove unnecessary WordPress head items
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');

    // Remove emoji scripts
    if (!get_theme_mod('recruitpro_enable_emojis', false)) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
    }
}

add_action('init', 'recruitpro_cleanup_wp_head');

/**
 * Disable user registration (as per requirements)
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_disable_user_registration() {
    // Candidates apply via forms only (no accounts created)
    // Client portals are generated by CRM plugin with personalized links
    // No front-end user registration allowed
    
    if (!is_admin()) {
        add_filter('option_users_can_register', '__return_false');
    }
}

add_action('init', 'recruitpro_disable_user_registration');

// Make functions available globally
add_action('init', function() {
    // Ensure all functions are loaded and available
    if (!function_exists('recruitpro_get_option')) {
        recruitpro_log_error('Critical theme functions not loaded properly');
    }
});