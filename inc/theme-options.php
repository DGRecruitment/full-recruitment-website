<?php
/**
 * RecruitPro Theme Options Configuration
 *
 * Central theme options management system for the RecruitPro recruitment website theme.
 * This file handles all theme-level configuration, default values, option management,
 * and provides a unified interface for theme customization. Specifically designed
 * for recruitment agencies and HR consultancies.
 *
 * @package RecruitPro
 * @subpackage Theme/Options
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/theme-options.php
 * Purpose: Central theme options configuration and management
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Theme Options Class
 * 
 * Manages all theme configuration, default values, and option processing.
 * Provides a centralized system for theme customization.
 *
 * @since 1.0.0
 */
class RecruitPro_Theme_Options {

    /**
     * Theme option defaults
     *
     * @since 1.0.0
     * @var array
     */
    private static $defaults = null;

    /**
     * Option groups for organization
     *
     * @since 1.0.0
     * @var array
     */
    private static $option_groups = array(
        'site_identity',
        'colors_typography',
        'layout_structure',
        'header_navigation',
        'homepage_content',
        'jobs_display',
        'company_information',
        'contact_details',
        'social_media',
        'footer_configuration',
        'seo_optimization',
        'performance_settings',
        'security_features',
        'recruitment_features',
        'multilingual_support',
        'integrations',
        'advanced_settings'
    );

    /**
     * Initialize theme options system
     *
     * @since 1.0.0
     * @return void
     */
    public static function init() {
        // Set up default values
        self::setup_defaults();
        
        // Register theme options hooks
        add_action('init', array(__CLASS__, 'register_options'));
        add_action('wp_loaded', array(__CLASS__, 'process_options'));
        add_action('customize_register', array(__CLASS__, 'integrate_customizer'));
        
        // Add admin interface
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_admin_options'));
        
        // Theme activation/deactivation hooks
        add_action('after_switch_theme', array(__CLASS__, 'theme_activation'));
        add_action('switch_theme', array(__CLASS__, 'theme_deactivation'));
        
        // AJAX handlers for dynamic options
        add_action('wp_ajax_recruitpro_save_options', array(__CLASS__, 'ajax_save_options'));
        add_action('wp_ajax_recruitpro_reset_options', array(__CLASS__, 'ajax_reset_options'));
        add_action('wp_ajax_recruitpro_import_options', array(__CLASS__, 'ajax_import_options'));
        add_action('wp_ajax_recruitpro_export_options', array(__CLASS__, 'ajax_export_options'));
    }

    /**
     * Set up default theme option values
     *
     * @since 1.0.0
     * @return void
     */
    private static function setup_defaults() {
        self::$defaults = array(
            // =================================================================
            // SITE IDENTITY & BRANDING
            // =================================================================
            'recruitpro_site_logo' => '',
            'recruitpro_site_logo_retina' => '',
            'recruitpro_logo_width' => 200,
            'recruitpro_logo_height' => 60,
            'recruitpro_favicon' => '',
            'recruitpro_site_title_display' => true,
            'recruitpro_tagline_display' => true,
            'recruitpro_brand_colors_enable' => true,

            // =================================================================
            // COLORS & TYPOGRAPHY
            // =================================================================
            'recruitpro_color_scheme' => 'professional_blue',
            'recruitpro_primary_color' => '#2563eb',
            'recruitpro_secondary_color' => '#64748b',
            'recruitpro_accent_color' => '#10b981',
            'recruitpro_text_color' => '#1f2937',
            'recruitpro_background_color' => '#ffffff',
            'recruitpro_header_background' => '#ffffff',
            'recruitpro_footer_background' => '#1f2937',
            'recruitpro_custom_colors_enable' => false,

            // Typography
            'recruitpro_body_font_family' => 'Inter',
            'recruitpro_heading_font_family' => 'Poppins',
            'recruitpro_font_size_base' => 16,
            'recruitpro_line_height_base' => 1.6,
            'recruitpro_font_weight_normal' => 400,
            'recruitpro_font_weight_bold' => 600,
            'recruitpro_google_fonts_enable' => true,
            'recruitpro_font_display_swap' => true,

            // =================================================================
            // LAYOUT & STRUCTURE
            // =================================================================
            'recruitpro_layout_style' => 'full_width',
            'recruitpro_content_width' => 1200,
            'recruitpro_sidebar_layout' => 'right',
            'recruitpro_sidebar_width' => 300,
            'recruitpro_boxed_layout' => false,
            'recruitpro_container_padding' => 20,
            'recruitpro_section_spacing' => 60,
            'recruitpro_responsive_breakpoints' => array(
                'mobile' => 768,
                'tablet' => 1024,
                'desktop' => 1200
            ),

            // =================================================================
            // HEADER & NAVIGATION
            // =================================================================
            'recruitpro_header_layout' => 'default',
            'recruitpro_header_sticky' => true,
            'recruitpro_header_transparent' => false,
            'recruitpro_header_search_enable' => true,
            'recruitpro_header_cta_enable' => true,
            'recruitpro_header_cta_text' => __('Get Started', 'recruitpro'),
            'recruitpro_header_cta_url' => '/contact',
            'recruitpro_header_cta_style' => 'button',
            'recruitpro_header_social_enable' => false,

            // Navigation
            'recruitpro_nav_style' => 'horizontal',
            'recruitpro_nav_dropdown_style' => 'default',
            'recruitpro_mobile_menu_style' => 'slide',
            'recruitpro_breadcrumbs_enable' => true,
            'recruitpro_breadcrumbs_homepage' => true,
            'recruitpro_mega_menu_enable' => false,

            // =================================================================
            // HOMEPAGE CONTENT
            // =================================================================
            'recruitpro_hero_enable' => true,
            'recruitpro_hero_layout' => 'centered',
            'recruitpro_hero_title' => __('Find Your Dream Career', 'recruitpro'),
            'recruitpro_hero_subtitle' => __('Connect with top employers and discover exciting opportunities in your field.', 'recruitpro'),
            'recruitpro_hero_cta_primary_text' => __('Browse Jobs', 'recruitpro'),
            'recruitpro_hero_cta_primary_url' => '/jobs',
            'recruitpro_hero_cta_secondary_text' => __('Submit CV', 'recruitpro'),
            'recruitpro_hero_cta_secondary_url' => '/apply',
            'recruitpro_hero_background_type' => 'image',
            'recruitpro_hero_background_image' => '',
            'recruitpro_hero_background_video' => '',
            'recruitpro_hero_overlay_enable' => true,
            'recruitpro_hero_overlay_color' => 'rgba(0,0,0,0.5)',

            // Features Section
            'recruitpro_features_enable' => true,
            'recruitpro_features_title' => __('Why Choose Us', 'recruitpro'),
            'recruitpro_features_subtitle' => __('Professional recruitment services tailored to your needs', 'recruitpro'),
            'recruitpro_features_layout' => 'grid_3_columns',
            'recruitpro_features_items' => array(
                array(
                    'icon' => 'fas fa-users',
                    'title' => __('Expert Recruiters', 'recruitpro'),
                    'description' => __('Our experienced team understands your industry and finds the perfect match.', 'recruitpro'),
                ),
                array(
                    'icon' => 'fas fa-chart-line',
                    'title' => __('Career Growth', 'recruitpro'),
                    'description' => __('We focus on opportunities that advance your career and professional development.', 'recruitpro'),
                ),
                array(
                    'icon' => 'fas fa-handshake',
                    'title' => __('Trusted Partners', 'recruitpro'),
                    'description' => __('Strong relationships with leading companies across multiple industries.', 'recruitpro'),
                ),
            ),

            // =================================================================
            // JOBS DISPLAY & FUNCTIONALITY
            // =================================================================
            'recruitpro_jobs_per_page' => 12,
            'recruitpro_job_layout' => 'grid',
            'recruitpro_job_excerpt_length' => 150,
            'recruitpro_job_meta_display' => array('location', 'type', 'salary', 'date'),
            'recruitpro_job_search_enable' => true,
            'recruitpro_job_filters_enable' => true,
            'recruitpro_job_categories_display' => true,
            'recruitpro_job_featured_enable' => true,
            'recruitpro_job_application_form' => 'detailed',
            'recruitpro_job_apply_button_text' => __('Apply Now', 'recruitpro'),
            'recruitpro_job_share_enable' => true,
            'recruitpro_job_related_enable' => true,
            'recruitpro_job_alerts_enable' => true,

            // Job Schema Markup
            'recruitpro_job_schema_enable' => true,
            'recruitpro_job_google_indexing' => true,
            'recruitpro_job_structured_data' => true,

            // =================================================================
            // COMPANY INFORMATION
            // =================================================================
            'recruitpro_company_name' => get_bloginfo('name'),
            'recruitpro_company_description' => get_bloginfo('description'),
            'recruitpro_company_logo' => '',
            'recruitpro_company_address' => '',
            'recruitpro_company_city' => '',
            'recruitpro_company_state' => '',
            'recruitpro_company_country' => '',
            'recruitpro_company_postal_code' => '',
            'recruitpro_company_phone' => '',
            'recruitpro_company_email' => get_option('admin_email'),
            'recruitpro_company_website' => home_url(),
            'recruitpro_company_founded' => '',
            'recruitpro_company_size' => '',
            'recruitpro_company_industry' => 'recruitment',

            // =================================================================
            // CONTACT DETAILS & COMMUNICATION
            // =================================================================
            'recruitpro_contact_enable' => true,
            'recruitpro_contact_phone_display' => true,
            'recruitpro_contact_email_display' => true,
            'recruitpro_contact_address_display' => true,
            'recruitpro_contact_hours_enable' => true,
            'recruitpro_contact_hours' => __('Monday - Friday: 9:00 AM - 6:00 PM', 'recruitpro'),
            'recruitpro_contact_form_enable' => true,
            'recruitpro_contact_map_enable' => true,
            'recruitpro_contact_map_api_key' => '',
            'recruitpro_contact_whatsapp' => '',
            'recruitpro_contact_skype' => '',

            // =================================================================
            // SOCIAL MEDIA INTEGRATION
            // =================================================================
            'recruitpro_social_enable' => true,
            'recruitpro_social_linkedin' => '',
            'recruitpro_social_facebook' => '',
            'recruitpro_social_twitter' => '',
            'recruitpro_social_instagram' => '',
            'recruitpro_social_youtube' => '',
            'recruitpro_social_indeed' => '',
            'recruitpro_social_glassdoor' => '',
            'recruitpro_social_xing' => '',
            'recruitpro_social_new_window' => true,
            'recruitpro_social_nofollow' => true,
            'recruitpro_social_header_display' => false,
            'recruitpro_social_footer_display' => true,

            // =================================================================
            // FOOTER CONFIGURATION
            // =================================================================
            'recruitpro_footer_layout' => 'four_columns',
            'recruitpro_footer_widgets_enable' => true,
            'recruitpro_footer_social_enable' => true,
            'recruitpro_footer_copyright' => sprintf(
                __('© %s %s. All rights reserved.', 'recruitpro'),
                date('Y'),
                get_bloginfo('name')
            ),
            'recruitpro_footer_privacy_link' => '/privacy-policy',
            'recruitpro_footer_terms_link' => '/terms-of-service',
            'recruitpro_footer_back_to_top' => true,
            'recruitpro_footer_newsletter_enable' => true,

            // =================================================================
            // SEO OPTIMIZATION (THEME LEVEL)
            // =================================================================
            'recruitpro_seo_meta_tags' => true,
            'recruitpro_seo_open_graph' => true,
            'recruitpro_seo_twitter_cards' => true,
            'recruitpro_seo_schema_markup' => true,
            'recruitpro_seo_xml_sitemap' => false, // Handled by plugin
            'recruitpro_seo_breadcrumbs_schema' => true,
            'recruitpro_seo_job_schema' => true,
            'recruitpro_seo_company_schema' => true,

            // =================================================================
            // PERFORMANCE SETTINGS
            // =================================================================
            'recruitpro_performance_lazy_loading' => true,
            'recruitpro_performance_image_optimization' => true,
            'recruitpro_performance_css_minify' => true,
            'recruitpro_performance_js_minify' => true,
            'recruitpro_performance_gzip_enable' => true,
            'recruitpro_performance_cache_enable' => false, // Handled by plugin
            'recruitpro_performance_cdn_enable' => false,
            'recruitpro_performance_preload_fonts' => true,

            // =================================================================
            // SECURITY FEATURES (THEME LEVEL)
            // =================================================================
            'recruitpro_security_disable_registration' => true,
            'recruitpro_security_hide_wp_version' => true,
            'recruitpro_security_disable_file_editing' => true,
            'recruitpro_security_remove_wp_meta' => true,
            'recruitpro_security_secure_headers' => true,

            // =================================================================
            // RECRUITMENT-SPECIFIC FEATURES
            // =================================================================
            'recruitpro_recruitment_cv_upload' => true,
            'recruitpro_recruitment_cv_formats' => array('pdf', 'doc', 'docx'),
            'recruitpro_recruitment_cv_max_size' => 5, // MB
            'recruitpro_recruitment_application_notifications' => true,
            'recruitpro_recruitment_auto_response' => true,
            'recruitpro_recruitment_auto_response_text' => __('Thank you for your application. We will review your CV and contact you if there is a suitable match.', 'recruitpro'),
            'recruitpro_recruitment_candidate_tracking' => false, // Handled by CRM plugin
            'recruitpro_recruitment_job_alerts' => true,

            // =================================================================
            // MULTILINGUAL SUPPORT
            // =================================================================
            'recruitpro_multilingual_enable' => false,
            'recruitpro_multilingual_default_language' => 'en',
            'recruitpro_multilingual_languages' => array('en'),
            'recruitpro_multilingual_hreflang' => true,
            'recruitpro_multilingual_language_switcher' => true,
            'recruitpro_multilingual_auto_translate' => false,

            // =================================================================
            // THIRD-PARTY INTEGRATIONS
            // =================================================================
            'recruitpro_google_analytics' => '',
            'recruitpro_facebook_pixel' => '',
            'recruitpro_google_tag_manager' => '',
            'recruitpro_hotjar_tracking' => '',
            'recruitpro_linkedin_tracking' => '',
            'recruitpro_recaptcha_site_key' => '',
            'recruitpro_recaptcha_secret_key' => '',
            'recruitpro_mailchimp_api_key' => '',
            'recruitpro_elementor_integration' => true,
            'recruitpro_classic_editor_integration' => true,

            // =================================================================
            // ADVANCED SETTINGS
            // =================================================================
            'recruitpro_custom_css' => '',
            'recruitpro_custom_js' => '',
            'recruitpro_custom_head_code' => '',
            'recruitpro_custom_footer_code' => '',
            'recruitpro_developer_mode' => false,
            'recruitpro_debug_mode' => false,
            'recruitpro_maintenance_mode' => false,
            'recruitpro_coming_soon_mode' => false,
            'recruitpro_white_label_mode' => false,
        );

        // Allow plugins and child themes to modify defaults
        self::$defaults = apply_filters('recruitpro_theme_option_defaults', self::$defaults);
    }

    /**
     * Get theme option value with fallback to default
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $default_value Default value if option not found
     * @return mixed Option value
     */
    public static function get_option($option_name, $default_value = null) {
        // Initialize defaults if not set
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        // Get from customizer first
        $value = get_theme_mod($option_name, null);

        // If not in customizer, check theme options
        if ($value === null) {
            $value = get_option($option_name, null);
        }

        // If still null, use provided default or defaults array
        if ($value === null) {
            if ($default_value !== null) {
                $value = $default_value;
            } elseif (isset(self::$defaults[$option_name])) {
                $value = self::$defaults[$option_name];
            }
        }

        // Apply filters for customization
        return apply_filters("recruitpro_theme_option_{$option_name}", $value);
    }

    /**
     * Set theme option value
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $value Option value
     * @param bool $use_customizer Whether to use customizer
     * @return bool Success status
     */
    public static function set_option($option_name, $value, $use_customizer = true) {
        if ($use_customizer) {
            set_theme_mod($option_name, $value);
        } else {
            update_option($option_name, $value);
        }

        // Clear any caching
        wp_cache_delete($option_name, 'recruitpro_options');

        return true;
    }

    /**
     * Get all theme options
     *
     * @since 1.0.0
     * @return array All theme options
     */
    public static function get_all_options() {
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        $options = array();
        foreach (self::$defaults as $option_name => $default_value) {
            $options[$option_name] = self::get_option($option_name);
        }

        return $options;
    }

    /**
     * Get options by group
     *
     * @since 1.0.0
     * @param string $group Option group name
     * @return array Options in group
     */
    public static function get_options_by_group($group) {
        $all_options = self::get_all_options();
        $group_options = array();

        $prefix = "recruitpro_{$group}_";
        foreach ($all_options as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $group_options[$key] = $value;
            }
        }

        return $group_options;
    }

    /**
     * Reset options to defaults
     *
     * @since 1.0.0
     * @param string $group Optional group to reset
     * @return bool Success status
     */
    public static function reset_options($group = null) {
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        $options_to_reset = self::$defaults;

        if ($group !== null) {
            $prefix = "recruitpro_{$group}_";
            $options_to_reset = array();
            foreach (self::$defaults as $key => $value) {
                if (strpos($key, $prefix) === 0) {
                    $options_to_reset[$key] = $value;
                }
            }
        }

        foreach ($options_to_reset as $option_name => $default_value) {
            // Remove from customizer
            remove_theme_mod($option_name);
            // Remove from options table
            delete_option($option_name);
        }

        // Clear caching
        wp_cache_flush();

        return true;
    }

    /**
     * Register theme options for WordPress
     *
     * @since 1.0.0
     * @return void
     */
    public static function register_options() {
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        foreach (self::$defaults as $option_name => $default_value) {
            register_setting('recruitpro_theme_options', $option_name, array(
                'type' => gettype($default_value),
                'description' => sprintf(__('RecruitPro theme option: %s', 'recruitpro'), $option_name),
                'sanitize_callback' => array(__CLASS__, 'sanitize_option'),
                'default' => $default_value,
            ));
        }
    }

    /**
     * Sanitize theme option value
     *
     * @since 1.0.0
     * @param mixed $value Option value
     * @return mixed Sanitized value
     */
    public static function sanitize_option($value) {
        // Apply general sanitization based on value type
        if (is_string($value)) {
            // Check if it's HTML content
            if (strpos($value, '<') !== false) {
                $value = wp_kses_post($value);
            } else {
                $value = sanitize_text_field($value);
            }
        } elseif (is_array($value)) {
            $value = array_map(array(__CLASS__, 'sanitize_option'), $value);
        } elseif (is_bool($value)) {
            $value = (bool) $value;
        } elseif (is_numeric($value)) {
            $value = is_float($value) ? floatval($value) : intval($value);
        }

        return $value;
    }

    /**
     * Process theme options on WordPress load
     *
     * @since 1.0.0
     * @return void
     */
    public static function process_options() {
        // Handle option form submissions
        if (isset($_POST['recruitpro_save_options']) && wp_verify_nonce($_POST['recruitpro_nonce'], 'recruitpro_save_options')) {
            self::save_posted_options();
        }

        // Handle reset requests
        if (isset($_POST['recruitpro_reset_options']) && wp_verify_nonce($_POST['recruitpro_nonce'], 'recruitpro_reset_options')) {
            $group = isset($_POST['reset_group']) ? sanitize_text_field($_POST['reset_group']) : null;
            self::reset_options($group);
            wp_redirect(add_query_arg('reset', 'success'));
            exit;
        }
    }

    /**
     * Save posted options from admin form
     *
     * @since 1.0.0
     * @return void
     */
    private static function save_posted_options() {
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        foreach (self::$defaults as $option_name => $default_value) {
            if (isset($_POST[$option_name])) {
                $value = $_POST[$option_name];
                $sanitized_value = self::sanitize_option($value);
                self::set_option($option_name, $sanitized_value);
            }
        }

        wp_redirect(add_query_arg('updated', 'success'));
        exit;
    }

    /**
     * Integrate with WordPress Customizer
     *
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer manager
     * @return void
     */
    public static function integrate_customizer($wp_customize) {
        // Register customizer sections and controls
        // This integrates with the existing customizer.php file
        do_action('recruitpro_theme_options_customizer', $wp_customize);
    }

    /**
     * Add admin menu for theme options
     *
     * @since 1.0.0
     * @return void
     */
    public static function add_admin_menu() {
        add_theme_page(
            __('RecruitPro Theme Options', 'recruitpro'),
            __('Theme Options', 'recruitpro'),
            'manage_options',
            'recruitpro-theme-options',
            array(__CLASS__, 'admin_page')
        );
    }

    /**
     * Register admin options
     *
     * @since 1.0.0
     * @return void
     */
    public static function register_admin_options() {
        // Register option groups for admin interface
        foreach (self::$option_groups as $group) {
            register_setting(
                "recruitpro_options_{$group}",
                "recruitpro_options_{$group}",
                array(__CLASS__, 'sanitize_option')
            );
        }
    }

    /**
     * Theme options admin page
     *
     * @since 1.0.0
     * @return void
     */
    public static function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('RecruitPro Theme Options', 'recruitpro'); ?></h1>
            
            <?php if (isset($_GET['updated']) && $_GET['updated'] === 'success') : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Theme options saved successfully!', 'recruitpro'); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success') : ?>
                <div class="notice notice-info is-dismissible">
                    <p><?php esc_html_e('Theme options reset to defaults!', 'recruitpro'); ?></p>
                </div>
            <?php endif; ?>

            <div class="recruitpro-admin-content">
                <div class="recruitpro-admin-header">
                    <p><?php esc_html_e('Configure your RecruitPro theme settings. For live preview and easy customization, use the WordPress Customizer.', 'recruitpro'); ?></p>
                    <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button button-primary">
                        <?php esc_html_e('Open Customizer', 'recruitpro'); ?>
                    </a>
                </div>

                <div class="recruitpro-options-tabs">
                    <nav class="nav-tab-wrapper">
                        <?php foreach (self::$option_groups as $group) : ?>
                            <a href="#<?php echo esc_attr($group); ?>" class="nav-tab">
                                <?php echo esc_html(self::get_group_label($group)); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <form method="post" action="">
                        <?php wp_nonce_field('recruitpro_save_options', 'recruitpro_nonce'); ?>
                        
                        <?php foreach (self::$option_groups as $group) : ?>
                            <div id="<?php echo esc_attr($group); ?>" class="tab-content">
                                <h2><?php echo esc_html(self::get_group_label($group)); ?></h2>
                                <?php self::render_group_options($group); ?>
                            </div>
                        <?php endforeach; ?>

                        <p class="submit">
                            <input type="submit" name="recruitpro_save_options" class="button-primary" value="<?php esc_attr_e('Save Options', 'recruitpro'); ?>" />
                            <input type="submit" name="recruitpro_reset_options" class="button-secondary" value="<?php esc_attr_e('Reset to Defaults', 'recruitpro'); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to reset all options to defaults?', 'recruitpro'); ?>');" />
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <style>
        .recruitpro-admin-content {
            max-width: 1200px;
        }
        .recruitpro-admin-header {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .recruitpro-options-tabs .tab-content {
            display: none;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .recruitpro-options-tabs .tab-content.active {
            display: block;
        }
        .option-row {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .option-row:last-child {
            border-bottom: none;
        }
        .option-label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .option-description {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // Tab functionality
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                $('.nav-tab').removeClass('nav-tab-active');
                $('.tab-content').removeClass('active');
                $(this).addClass('nav-tab-active');
                $(target).addClass('active');
            });
            
            // Activate first tab
            $('.nav-tab:first').click();
        });
        </script>
        <?php
    }

    /**
     * Get group label for display
     *
     * @since 1.0.0
     * @param string $group Group name
     * @return string Group label
     */
    private static function get_group_label($group) {
        $labels = array(
            'site_identity' => __('Site Identity', 'recruitpro'),
            'colors_typography' => __('Colors & Typography', 'recruitpro'),
            'layout_structure' => __('Layout & Structure', 'recruitpro'),
            'header_navigation' => __('Header & Navigation', 'recruitpro'),
            'homepage_content' => __('Homepage Content', 'recruitpro'),
            'jobs_display' => __('Jobs Display', 'recruitpro'),
            'company_information' => __('Company Information', 'recruitpro'),
            'contact_details' => __('Contact Details', 'recruitpro'),
            'social_media' => __('Social Media', 'recruitpro'),
            'footer_configuration' => __('Footer Configuration', 'recruitpro'),
            'seo_optimization' => __('SEO Optimization', 'recruitpro'),
            'performance_settings' => __('Performance Settings', 'recruitpro'),
            'security_features' => __('Security Features', 'recruitpro'),
            'recruitment_features' => __('Recruitment Features', 'recruitpro'),
            'multilingual_support' => __('Multilingual Support', 'recruitpro'),
            'integrations' => __('Integrations', 'recruitpro'),
            'advanced_settings' => __('Advanced Settings', 'recruitpro'),
        );

        return isset($labels[$group]) ? $labels[$group] : ucwords(str_replace('_', ' ', $group));
    }

    /**
     * Render options for a specific group
     *
     * @since 1.0.0
     * @param string $group Group name
     * @return void
     */
    private static function render_group_options($group) {
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        $prefix = "recruitpro_{$group}_";
        $group_options = array();

        foreach (self::$defaults as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $group_options[$key] = $value;
            }
        }

        if (empty($group_options)) {
            echo '<p>' . esc_html__('No options available for this group.', 'recruitpro') . '</p>';
            return;
        }

        foreach ($group_options as $option_name => $default_value) {
            $current_value = self::get_option($option_name);
            $option_label = self::get_option_label($option_name);
            $option_description = self::get_option_description($option_name);

            echo '<div class="option-row">';
            echo '<div class="option-label">' . esc_html($option_label) . '</div>';
            
            self::render_option_field($option_name, $current_value, $default_value);
            
            if ($option_description) {
                echo '<div class="option-description">' . esc_html($option_description) . '</div>';
            }
            echo '</div>';
        }
    }

    /**
     * Render individual option field
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $current_value Current value
     * @param mixed $default_value Default value
     * @return void
     */
    private static function render_option_field($option_name, $current_value, $default_value) {
        $field_type = self::get_option_field_type($option_name, $default_value);

        switch ($field_type) {
            case 'text':
                echo '<input type="text" name="' . esc_attr($option_name) . '" value="' . esc_attr($current_value) . '" class="regular-text" />';
                break;

            case 'textarea':
                echo '<textarea name="' . esc_attr($option_name) . '" rows="4" cols="50" class="large-text">' . esc_textarea($current_value) . '</textarea>';
                break;

            case 'checkbox':
                echo '<label><input type="checkbox" name="' . esc_attr($option_name) . '" value="1" ' . checked($current_value, true, false) . ' /> ' . esc_html__('Enable', 'recruitpro') . '</label>';
                break;

            case 'number':
                echo '<input type="number" name="' . esc_attr($option_name) . '" value="' . esc_attr($current_value) . '" class="small-text" />';
                break;

            case 'color':
                echo '<input type="color" name="' . esc_attr($option_name) . '" value="' . esc_attr($current_value) . '" />';
                break;

            case 'select':
                $options = self::get_option_choices($option_name);
                echo '<select name="' . esc_attr($option_name) . '">';
                foreach ($options as $value => $label) {
                    echo '<option value="' . esc_attr($value) . '" ' . selected($current_value, $value, false) . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';
                break;

            default:
                echo '<input type="text" name="' . esc_attr($option_name) . '" value="' . esc_attr($current_value) . '" class="regular-text" />';
        }
    }

    /**
     * Get option field type
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $default_value Default value
     * @return string Field type
     */
    private static function get_option_field_type($option_name, $default_value) {
        // Color fields
        if (strpos($option_name, '_color') !== false) {
            return 'color';
        }

        // Checkbox fields
        if (is_bool($default_value) || strpos($option_name, '_enable') !== false) {
            return 'checkbox';
        }

        // Number fields
        if (is_numeric($default_value)) {
            return 'number';
        }

        // Textarea fields
        if (strpos($option_name, '_description') !== false || 
            strpos($option_name, '_content') !== false ||
            strpos($option_name, '_text') !== false ||
            strlen($default_value) > 100) {
            return 'textarea';
        }

        // Select fields
        if (strpos($option_name, '_layout') !== false ||
            strpos($option_name, '_style') !== false ||
            strpos($option_name, '_scheme') !== false) {
            return 'select';
        }

        return 'text';
    }

    /**
     * Get option label for display
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @return string Option label
     */
    private static function get_option_label($option_name) {
        // Remove prefix and convert to title case
        $label = str_replace('recruitpro_', '', $option_name);
        $label = str_replace('_', ' ', $label);
        $label = ucwords($label);
        
        return $label;
    }

    /**
     * Get option description
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @return string Option description
     */
    private static function get_option_description($option_name) {
        // Basic descriptions for common options
        $descriptions = array(
            'recruitpro_primary_color' => __('Main brand color used throughout the site.', 'recruitpro'),
            'recruitpro_logo_width' => __('Maximum width for the logo in pixels.', 'recruitpro'),
            'recruitpro_jobs_per_page' => __('Number of jobs to display per page.', 'recruitpro'),
            'recruitpro_security_disable_registration' => __('Prevent new user registrations (recommended for recruitment sites).', 'recruitpro'),
        );

        return isset($descriptions[$option_name]) ? $descriptions[$option_name] : '';
    }

    /**
     * Get option choices for select fields
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @return array Option choices
     */
    private static function get_option_choices($option_name) {
        $choices = array();

        switch ($option_name) {
            case 'recruitpro_color_scheme':
                $choices = array(
                    'professional_blue' => __('Professional Blue', 'recruitpro'),
                    'corporate_gray' => __('Corporate Gray', 'recruitpro'),
                    'modern_green' => __('Modern Green', 'recruitpro'),
                    'executive_navy' => __('Executive Navy', 'recruitpro'),
                    'custom' => __('Custom Colors', 'recruitpro'),
                );
                break;

            case 'recruitpro_header_layout':
                $choices = array(
                    'default' => __('Default', 'recruitpro'),
                    'centered' => __('Centered', 'recruitpro'),
                    'split' => __('Split Layout', 'recruitpro'),
                    'minimal' => __('Minimal', 'recruitpro'),
                );
                break;

            case 'recruitpro_footer_layout':
                $choices = array(
                    'four_columns' => __('Four Columns', 'recruitpro'),
                    'three_columns' => __('Three Columns', 'recruitpro'),
                    'two_columns' => __('Two Columns', 'recruitpro'),
                    'single_column' => __('Single Column', 'recruitpro'),
                );
                break;

            default:
                $choices = array();
        }

        return $choices;
    }

    /**
     * Theme activation handler
     *
     * @since 1.0.0
     * @return void
     */
    public static function theme_activation() {
        // Set default options on theme activation
        if (self::$defaults === null) {
            self::setup_defaults();
        }

        foreach (self::$defaults as $option_name => $default_value) {
            if (get_theme_mod($option_name, null) === null) {
                set_theme_mod($option_name, $default_value);
            }
        }

        // Flush rewrite rules
        flush_rewrite_rules();

        // Create default pages if needed
        self::create_default_pages();
    }

    /**
     * Theme deactivation handler
     *
     * @since 1.0.0
     * @return void
     */
    public static function theme_deactivation() {
        // Optional: Clean up theme-specific data
        // Note: Usually we don't remove user customizations on theme switch
        
        do_action('recruitpro_theme_deactivated');
    }

    /**
     * Create default pages required by the theme
     *
     * @since 1.0.0
     * @return void
     */
    private static function create_default_pages() {
        $default_pages = array(
            'jobs' => array(
                'title' => __('Current Opportunities', 'recruitpro'),
                'content' => __('Browse our current job openings and find your next career opportunity.', 'recruitpro'),
            ),
            'apply' => array(
                'title' => __('Submit Your CV', 'recruitpro'),
                'content' => __('Upload your CV and we will match you with suitable opportunities.', 'recruitpro'),
            ),
            'about' => array(
                'title' => __('About Us', 'recruitpro'),
                'content' => __('Learn more about our recruitment services and team.', 'recruitpro'),
            ),
            'contact' => array(
                'title' => __('Contact Us', 'recruitpro'),
                'content' => __('Get in touch with our recruitment specialists.', 'recruitpro'),
            ),
        );

        foreach ($default_pages as $slug => $page_data) {
            if (!get_page_by_path($slug)) {
                wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_name' => $slug,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                ));
            }
        }
    }

    /**
     * AJAX handler for saving options
     *
     * @since 1.0.0
     * @return void
     */
    public static function ajax_save_options() {
        check_ajax_referer('recruitpro_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $options = isset($_POST['options']) ? $_POST['options'] : array();
        
        foreach ($options as $option_name => $value) {
            $sanitized_value = self::sanitize_option($value);
            self::set_option($option_name, $sanitized_value);
        }

        wp_send_json_success(array(
            'message' => __('Options saved successfully!', 'recruitpro')
        ));
    }

    /**
     * AJAX handler for resetting options
     *
     * @since 1.0.0
     * @return void
     */
    public static function ajax_reset_options() {
        check_ajax_referer('recruitpro_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $group = isset($_POST['group']) ? sanitize_text_field($_POST['group']) : null;
        $success = self::reset_options($group);

        if ($success) {
            wp_send_json_success(array(
                'message' => __('Options reset successfully!', 'recruitpro')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Error resetting options.', 'recruitpro')
            ));
        }
    }

    /**
     * AJAX handler for importing options
     *
     * @since 1.0.0
     * @return void
     */
    public static function ajax_import_options() {
        check_ajax_referer('recruitpro_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $import_data = isset($_POST['import_data']) ? $_POST['import_data'] : '';
        $decoded_data = json_decode(stripslashes($import_data), true);

        if ($decoded_data && is_array($decoded_data)) {
            foreach ($decoded_data as $option_name => $value) {
                if (array_key_exists($option_name, self::$defaults)) {
                    $sanitized_value = self::sanitize_option($value);
                    self::set_option($option_name, $sanitized_value);
                }
            }

            wp_send_json_success(array(
                'message' => __('Options imported successfully!', 'recruitpro')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Invalid import data.', 'recruitpro')
            ));
        }
    }

    /**
     * AJAX handler for exporting options
     *
     * @since 1.0.0
     * @return void
     */
    public static function ajax_export_options() {
        check_ajax_referer('recruitpro_options_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $all_options = self::get_all_options();
        
        wp_send_json_success(array(
            'data' => $all_options,
            'filename' => 'recruitpro-theme-options-' . date('Y-m-d') . '.json'
        ));
    }

    /**
     * Get default values (static method for external access)
     *
     * @since 1.0.0
     * @return array Default values
     */
    public static function get_defaults() {
        if (self::$defaults === null) {
            self::setup_defaults();
        }
        return self::$defaults;
    }

    /**
     * Check if option exists
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @return bool Whether option exists
     */
    public static function option_exists($option_name) {
        if (self::$defaults === null) {
            self::setup_defaults();
        }
        return array_key_exists($option_name, self::$defaults);
    }
}

// Initialize the theme options system
RecruitPro_Theme_Options::init();

/**
 * Convenience functions for accessing theme options
 */

if (!function_exists('recruitpro_get_option')) {
    /**
     * Get theme option value
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $default Default value
     * @return mixed Option value
     */
    function recruitpro_get_option($option_name, $default = null) {
        return RecruitPro_Theme_Options::get_option($option_name, $default);
    }
}

if (!function_exists('recruitpro_set_option')) {
    /**
     * Set theme option value
     *
     * @since 1.0.0
     * @param string $option_name Option name
     * @param mixed $value Option value
     * @return bool Success status
     */
    function recruitpro_set_option($option_name, $value) {
        return RecruitPro_Theme_Options::set_option($option_name, $value);
    }
}

if (!function_exists('recruitpro_get_color_scheme')) {
    /**
     * Get active color scheme
     *
     * @since 1.0.0
     * @return array Color scheme values
     */
    function recruitpro_get_color_scheme() {
        $scheme = recruitpro_get_option('recruitpro_color_scheme', 'professional_blue');
        
        $schemes = array(
            'professional_blue' => array(
                'primary' => '#2563eb',
                'secondary' => '#64748b',
                'accent' => '#10b981',
                'text' => '#1f2937',
                'background' => '#ffffff',
            ),
            'corporate_gray' => array(
                'primary' => '#4b5563',
                'secondary' => '#9ca3af',
                'accent' => '#f59e0b',
                'text' => '#111827',
                'background' => '#f9fafb',
            ),
            'modern_green' => array(
                'primary' => '#059669',
                'secondary' => '#6b7280',
                'accent' => '#3b82f6',
                'text' => '#1f2937',
                'background' => '#ffffff',
            ),
            'executive_navy' => array(
                'primary' => '#1e3a8a',
                'secondary' => '#64748b',
                'accent' => '#dc2626',
                'text' => '#1f2937',
                'background' => '#ffffff',
            ),
        );

        if ($scheme === 'custom') {
            return array(
                'primary' => recruitpro_get_option('recruitpro_primary_color', '#2563eb'),
                'secondary' => recruitpro_get_option('recruitpro_secondary_color', '#64748b'),
                'accent' => recruitpro_get_option('recruitpro_accent_color', '#10b981'),
                'text' => recruitpro_get_option('recruitpro_text_color', '#1f2937'),
                'background' => recruitpro_get_option('recruitpro_background_color', '#ffffff'),
            );
        }

        return isset($schemes[$scheme]) ? $schemes[$scheme] : $schemes['professional_blue'];
    }
}

// End of theme-options.php