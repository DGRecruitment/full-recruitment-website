<?php
/**
 * RecruitPro Theme Typography Management
 *
 * Comprehensive typography system for the RecruitPro recruitment website theme.
 * This file handles font management, typography settings, Google Fonts integration,
 * professional font combinations, and accessibility features specifically designed
 * for recruitment agencies and HR consultancies.
 *
 * @package RecruitPro
 * @subpackage Theme/Typography
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/typography.php
 * Purpose: Typography management and font configuration system
 * Dependencies: WordPress core, theme functions, customizer
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Typography Management Class
 * 
 * Handles all typography functionality including font selection,
 * sizing, spacing, and professional combinations for recruitment websites.
 *
 * @since 1.0.0
 */
class RecruitPro_Typography {

    /**
     * Typography presets for different recruitment sectors
     *
     * @since 1.0.0
     * @var array
     */
    private static $typography_presets = array();

    /**
     * Available font families
     *
     * @since 1.0.0
     * @var array
     */
    private static $font_families = array();

    /**
     * Font size scales
     *
     * @since 1.0.0
     * @var array
     */
    private static $font_scales = array();

    /**
     * Default typography settings
     *
     * @since 1.0.0
     * @var array
     */
    private static $typography_defaults = array();

    /**
     * CSS variables for typography
     *
     * @since 1.0.0
     * @var array
     */
    private static $css_variables = array();

    /**
     * Initialize typography system
     *
     * @since 1.0.0
     * @return void
     */
    public static function init() {
        // Set up typography data
        self::setup_typography_presets();
        self::setup_font_families();
        self::setup_font_scales();
        self::setup_typography_defaults();
        
        // WordPress hooks
        add_action('customize_register', array(__CLASS__, 'add_customizer_settings'));
        add_action('wp_head', array(__CLASS__, 'output_typography_css'), 15);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_typography_fonts'));
        add_action('wp_head', array(__CLASS__, 'output_font_preconnect'), 1);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_typography'));
        
        // Typography filters
        add_filter('recruitpro_typography_css_variables', array(__CLASS__, 'get_css_variables'));
        add_filter('recruitpro_font_families', array(__CLASS__, 'get_font_families'));
        add_filter('recruitpro_typography_presets', array(__CLASS__, 'get_typography_presets'));
        
        // Admin interface
        add_action('admin_menu', array(__CLASS__, 'add_typography_admin_page'));
        add_action('admin_init', array(__CLASS__, 'register_typography_settings'));
        
        // AJAX handlers for dynamic typography preview
        add_action('wp_ajax_recruitpro_preview_typography', array(__CLASS__, 'ajax_preview_typography'));
        add_action('wp_ajax_recruitpro_apply_typography_preset', array(__CLASS__, 'ajax_apply_typography_preset'));
    }

    /**
     * Set up typography presets for different recruitment industries
     *
     * @since 1.0.0
     * @return void
     */
    private static function setup_typography_presets() {
        self::$typography_presets = array(
            'corporate_executive' => array(
                'name' => __('Corporate Executive', 'recruitpro'),
                'description' => __('Professional and authoritative typography for executive recruitment and corporate agencies.', 'recruitpro'),
                'primary_font' => 'Roboto',
                'secondary_font' => 'Open Sans',
                'heading_font' => 'Playfair Display',
                'body_font' => 'Roboto',
                'menu_font' => 'Roboto',
                'font_size_base' => 16,
                'line_height_base' => 1.6,
                'font_weight_normal' => 400,
                'font_weight_medium' => 500,
                'font_weight_bold' => 700,
                'letter_spacing_normal' => '0em',
                'letter_spacing_headings' => '-0.01em',
                'font_sizes' => array(
                    'h1' => 48,
                    'h2' => 40,
                    'h3' => 32,
                    'h4' => 24,
                    'h5' => 20,
                    'h6' => 18,
                    'body' => 16,
                    'small' => 14,
                    'large' => 18,
                ),
                'sector' => 'executive',
                'mood' => 'professional',
            ),
            'modern_tech' => array(
                'name' => __('Modern Tech', 'recruitpro'),
                'description' => __('Clean and innovative typography for technology and startup recruitment.', 'recruitpro'),
                'primary_font' => 'Inter',
                'secondary_font' => 'Inter',
                'heading_font' => 'Inter',
                'body_font' => 'Inter',
                'menu_font' => 'Inter',
                'font_size_base' => 16,
                'line_height_base' => 1.7,
                'font_weight_normal' => 400,
                'font_weight_medium' => 500,
                'font_weight_bold' => 600,
                'letter_spacing_normal' => '0em',
                'letter_spacing_headings' => '-0.02em',
                'font_sizes' => array(
                    'h1' => 56,
                    'h2' => 44,
                    'h3' => 36,
                    'h4' => 28,
                    'h5' => 22,
                    'h6' => 18,
                    'body' => 16,
                    'small' => 14,
                    'large' => 18,
                ),
                'sector' => 'technology',
                'mood' => 'innovative',
            ),
            'healthcare_professional' => array(
                'name' => __('Healthcare Professional', 'recruitpro'),
                'description' => __('Trustworthy and readable typography for healthcare and medical recruitment.', 'recruitpro'),
                'primary_font' => 'Source Sans Pro',
                'secondary_font' => 'Source Sans Pro',
                'heading_font' => 'Merriweather',
                'body_font' => 'Source Sans Pro',
                'menu_font' => 'Source Sans Pro',
                'font_size_base' => 17,
                'line_height_base' => 1.65,
                'font_weight_normal' => 400,
                'font_weight_medium' => 600,
                'font_weight_bold' => 700,
                'letter_spacing_normal' => '0em',
                'letter_spacing_headings' => '0em',
                'font_sizes' => array(
                    'h1' => 44,
                    'h2' => 36,
                    'h3' => 30,
                    'h4' => 24,
                    'h5' => 20,
                    'h6' => 18,
                    'body' => 17,
                    'small' => 15,
                    'large' => 19,
                ),
                'sector' => 'healthcare',
                'mood' => 'trustworthy',
            ),
            'finance_banking' => array(
                'name' => __('Finance & Banking', 'recruitpro'),
                'description' => __('Traditional and reliable typography for financial services recruitment.', 'recruitpro'),
                'primary_font' => 'Lato',
                'secondary_font' => 'Lato',
                'heading_font' => 'Crimson Text',
                'body_font' => 'Lato',
                'menu_font' => 'Lato',
                'font_size_base' => 16,
                'line_height_base' => 1.6,
                'font_weight_normal' => 400,
                'font_weight_medium' => 600,
                'font_weight_bold' => 700,
                'letter_spacing_normal' => '0em',
                'letter_spacing_headings' => '0em',
                'font_sizes' => array(
                    'h1' => 42,
                    'h2' => 36,
                    'h3' => 30,
                    'h4' => 24,
                    'h5' => 20,
                    'h6' => 18,
                    'body' => 16,
                    'small' => 14,
                    'large' => 18,
                ),
                'sector' => 'finance',
                'mood' => 'traditional',
            ),
            'creative_agencies' => array(
                'name' => __('Creative Agencies', 'recruitpro'),
                'description' => __('Stylish and expressive typography for creative industry recruitment.', 'recruitpro'),
                'primary_font' => 'Nunito Sans',
                'secondary_font' => 'Nunito Sans',
                'heading_font' => 'Abril Fatface',
                'body_font' => 'Nunito Sans',
                'menu_font' => 'Nunito Sans',
                'font_size_base' => 16,
                'line_height_base' => 1.7,
                'font_weight_normal' => 400,
                'font_weight_medium' => 600,
                'font_weight_bold' => 700,
                'letter_spacing_normal' => '0.01em',
                'letter_spacing_headings' => '-0.01em',
                'font_sizes' => array(
                    'h1' => 52,
                    'h2' => 42,
                    'h3' => 34,
                    'h4' => 26,
                    'h5' => 22,
                    'h6' => 18,
                    'body' => 16,
                    'small' => 14,
                    'large' => 18,
                ),
                'sector' => 'creative',
                'mood' => 'expressive',
            ),
            'manufacturing_industrial' => array(
                'name' => __('Manufacturing & Industrial', 'recruitpro'),
                'description' => __('Strong and reliable typography for industrial and manufacturing recruitment.', 'recruitpro'),
                'primary_font' => 'Rubik',
                'secondary_font' => 'Rubik',
                'heading_font' => 'Oswald',
                'body_font' => 'Rubik',
                'menu_font' => 'Rubik',
                'font_size_base' => 16,
                'line_height_base' => 1.6,
                'font_weight_normal' => 400,
                'font_weight_medium' => 500,
                'font_weight_bold' => 700,
                'letter_spacing_normal' => '0em',
                'letter_spacing_headings' => '0.02em',
                'font_sizes' => array(
                    'h1' => 46,
                    'h2' => 38,
                    'h3' => 32,
                    'h4' => 26,
                    'h5' => 22,
                    'h6' => 18,
                    'body' => 16,
                    'small' => 14,
                    'large' => 18,
                ),
                'sector' => 'industrial',
                'mood' => 'strong',
            ),
            'minimal_clean' => array(
                'name' => __('Minimal Clean', 'recruitpro'),
                'description' => __('Simple and elegant typography for minimalist recruitment websites.', 'recruitpro'),
                'primary_font' => 'System UI',
                'secondary_font' => 'System UI',
                'heading_font' => 'System UI',
                'body_font' => 'System UI',
                'menu_font' => 'System UI',
                'font_size_base' => 16,
                'line_height_base' => 1.65,
                'font_weight_normal' => 400,
                'font_weight_medium' => 500,
                'font_weight_bold' => 600,
                'letter_spacing_normal' => '0em',
                'letter_spacing_headings' => '-0.01em',
                'font_sizes' => array(
                    'h1' => 48,
                    'h2' => 38,
                    'h3' => 30,
                    'h4' => 24,
                    'h5' => 20,
                    'h6' => 18,
                    'body' => 16,
                    'small' => 14,
                    'large' => 18,
                ),
                'sector' => 'universal',
                'mood' => 'minimal',
            ),
        );

        // Allow customization of presets
        self::$typography_presets = apply_filters('recruitpro_typography_presets', self::$typography_presets);
    }

    /**
     * Set up available font families
     *
     * @since 1.0.0
     * @return void
     */
    private static function setup_font_families() {
        self::$font_families = array(
            // Google Fonts - Sans Serif
            'google_sans' => array(
                'Inter' => array(
                    'name' => 'Inter',
                    'category' => 'sans-serif',
                    'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Modern, clean sans-serif designed for user interfaces', 'recruitpro'),
                    'recommended_for' => array('body', 'headings', 'ui'),
                    'performance_score' => 95,
                ),
                'Roboto' => array(
                    'name' => 'Roboto',
                    'category' => 'sans-serif',
                    'variants' => array('100', '300', '400', '500', '700', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Friendly and open curves with a mechanical skeleton', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 90,
                ),
                'Open Sans' => array(
                    'name' => 'Open Sans',
                    'category' => 'sans-serif',
                    'variants' => array('300', '400', '500', '600', '700', '800'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Humanist sans serif with upright stress and open forms', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 88,
                ),
                'Source Sans Pro' => array(
                    'name' => 'Source Sans Pro',
                    'category' => 'sans-serif',
                    'variants' => array('200', '300', '400', '600', '700', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Professional-grade sans serif by Adobe', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 92,
                ),
                'Lato' => array(
                    'name' => 'Lato',
                    'category' => 'sans-serif',
                    'variants' => array('100', '300', '400', '700', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Humanist style with classical proportions', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 87,
                ),
                'Nunito Sans' => array(
                    'name' => 'Nunito Sans',
                    'category' => 'sans-serif',
                    'variants' => array('200', '300', '400', '600', '700', '800', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Rounded terminals and balanced letter forms', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 85,
                ),
                'Rubik' => array(
                    'name' => 'Rubik',
                    'category' => 'sans-serif',
                    'variants' => array('300', '400', '500', '600', '700', '800', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Modern geometric sans with slightly rounded corners', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 86,
                ),
                'Poppins' => array(
                    'name' => 'Poppins',
                    'category' => 'sans-serif',
                    'variants' => array('100', '200', '300', '400', '500', '600', '700', '800', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Geometric sans serif with perfect circles', 'recruitpro'),
                    'recommended_for' => array('headings', 'ui'),
                    'performance_score' => 83,
                ),
            ),

            // Google Fonts - Serif
            'google_serif' => array(
                'Playfair Display' => array(
                    'name' => 'Playfair Display',
                    'category' => 'serif',
                    'variants' => array('400', '500', '600', '700', '800', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('High-contrast and distinctive serif for display use', 'recruitpro'),
                    'recommended_for' => array('headings', 'display'),
                    'performance_score' => 80,
                ),
                'Merriweather' => array(
                    'name' => 'Merriweather',
                    'category' => 'serif',
                    'variants' => array('300', '400', '700', '900'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Readable serif designed for screens', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 85,
                ),
                'Crimson Text' => array(
                    'name' => 'Crimson Text',
                    'category' => 'serif',
                    'variants' => array('400', '600', '700'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Classic book typography with modern refinements', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 82,
                ),
                'Abril Fatface' => array(
                    'name' => 'Abril Fatface',
                    'category' => 'serif',
                    'variants' => array('400'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Contemporary take on classic display serif', 'recruitpro'),
                    'recommended_for' => array('headings', 'display'),
                    'performance_score' => 78,
                ),
                'Oswald' => array(
                    'name' => 'Oswald',
                    'category' => 'sans-serif',
                    'variants' => array('200', '300', '400', '500', '600', '700'),
                    'subsets' => array('latin', 'latin-ext'),
                    'description' => __('Condensed sans serif inspired by classic poster design', 'recruitpro'),
                    'recommended_for' => array('headings', 'display'),
                    'performance_score' => 84,
                ),
            ),

            // System Fonts
            'system_fonts' => array(
                'System UI' => array(
                    'name' => 'System UI',
                    'category' => 'system',
                    'variants' => array('400', '500', '600', '700'),
                    'subsets' => array('latin'),
                    'description' => __('Native system font stack for optimal performance', 'recruitpro'),
                    'recommended_for' => array('body', 'headings', 'ui'),
                    'performance_score' => 100,
                    'fallback_stack' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif',
                ),
                'Arial' => array(
                    'name' => 'Arial',
                    'category' => 'system',
                    'variants' => array('400', '700'),
                    'subsets' => array('latin'),
                    'description' => __('Universal system font, reliable and widely available', 'recruitpro'),
                    'recommended_for' => array('body', 'fallback'),
                    'performance_score' => 100,
                    'fallback_stack' => 'Arial, "Helvetica Neue", Helvetica, sans-serif',
                ),
                'Georgia' => array(
                    'name' => 'Georgia',
                    'category' => 'system',
                    'variants' => array('400', '700'),
                    'subsets' => array('latin'),
                    'description' => __('Screen-optimized serif with excellent readability', 'recruitpro'),
                    'recommended_for' => array('body', 'headings'),
                    'performance_score' => 100,
                    'fallback_stack' => 'Georgia, "Times New Roman", Times, serif',
                ),
            ),
        );

        // Allow customization of font families
        self::$font_families = apply_filters('recruitpro_font_families', self::$font_families);
    }

    /**
     * Set up font scales
     *
     * @since 1.0.0
     * @return void
     */
    private static function setup_font_scales() {
        self::$font_scales = array(
            'minor_second' => array(
                'name' => __('Minor Second (1.067)', 'recruitpro'),
                'ratio' => 1.067,
                'description' => __('Subtle scale for dense information', 'recruitpro'),
                'recommended_for' => 'corporate',
            ),
            'major_second' => array(
                'name' => __('Major Second (1.125)', 'recruitpro'),
                'ratio' => 1.125,
                'description' => __('Classic scale for professional layouts', 'recruitpro'),
                'recommended_for' => 'professional',
            ),
            'minor_third' => array(
                'name' => __('Minor Third (1.2)', 'recruitpro'),
                'ratio' => 1.2,
                'description' => __('Balanced scale for most recruitment sites', 'recruitpro'),
                'recommended_for' => 'general',
            ),
            'major_third' => array(
                'name' => __('Major Third (1.25)', 'recruitpro'),
                'ratio' => 1.25,
                'description' => __('Clear hierarchy for content-heavy sites', 'recruitpro'),
                'recommended_for' => 'content',
            ),
            'perfect_fourth' => array(
                'name' => __('Perfect Fourth (1.333)', 'recruitpro'),
                'ratio' => 1.333,
                'description' => __('Strong contrast for modern layouts', 'recruitpro'),
                'recommended_for' => 'modern',
            ),
            'augmented_fourth' => array(
                'name' => __('Augmented Fourth (1.414)', 'recruitpro'),
                'ratio' => 1.414,
                'description' => __('Dynamic scale for creative agencies', 'recruitpro'),
                'recommended_for' => 'creative',
            ),
            'perfect_fifth' => array(
                'name' => __('Perfect Fifth (1.5)', 'recruitpro'),
                'ratio' => 1.5,
                'description' => __('Bold scale for executive recruitment', 'recruitpro'),
                'recommended_for' => 'executive',
            ),
            'golden_ratio' => array(
                'name' => __('Golden Ratio (1.618)', 'recruitpro'),
                'ratio' => 1.618,
                'description' => __('Harmonious scale based on natural proportions', 'recruitpro'),
                'recommended_for' => 'premium',
            ),
        );
    }

    /**
     * Set up typography defaults
     *
     * @since 1.0.0
     * @return void
     */
    private static function setup_typography_defaults() {
        self::$typography_defaults = array(
            // Typography preset
            'typography_preset' => 'corporate_executive',
            'custom_typography' => false,

            // Primary fonts
            'primary_font_family' => 'Roboto',
            'secondary_font_family' => 'Open Sans',
            'heading_font_family' => 'Playfair Display',
            'body_font_family' => 'Roboto',
            'menu_font_family' => 'Roboto',

            // Font sizes (in pixels)
            'font_size_base' => 16,
            'font_size_h1' => 48,
            'font_size_h2' => 40,
            'font_size_h3' => 32,
            'font_size_h4' => 24,
            'font_size_h5' => 20,
            'font_size_h6' => 18,
            'font_size_small' => 14,
            'font_size_large' => 18,
            'font_size_menu' => 16,
            'font_size_button' => 16,

            // Line heights
            'line_height_base' => 1.6,
            'line_height_headings' => 1.2,
            'line_height_menu' => 1.4,

            // Font weights
            'font_weight_light' => 300,
            'font_weight_normal' => 400,
            'font_weight_medium' => 500,
            'font_weight_semibold' => 600,
            'font_weight_bold' => 700,
            'font_weight_extra_bold' => 800,
            'font_weight_black' => 900,

            // Letter spacing
            'letter_spacing_normal' => '0em',
            'letter_spacing_headings' => '-0.01em',
            'letter_spacing_menu' => '0.02em',
            'letter_spacing_buttons' => '0.05em',

            // Font scale
            'font_scale' => 'major_third',
            'font_scale_ratio' => 1.25,

            // Google Fonts settings
            'google_fonts_enabled' => true,
            'google_fonts_display' => 'swap',
            'google_fonts_preload' => true,
            'google_fonts_subsets' => array('latin'),

            // Performance settings
            'font_optimization' => true,
            'font_preload' => true,
            'font_fallbacks' => true,
            'local_font_fallback' => true,

            // Accessibility settings
            'accessibility_mode' => false,
            'high_contrast_mode' => false,
            'large_text_mode' => false,
            'dyslexia_friendly_mode' => false,

            // Advanced typography
            'text_rendering_optimization' => true,
            'font_smoothing' => true,
            'kerning' => true,
            'ligatures' => true,

            // Mobile typography
            'mobile_font_scaling' => true,
            'mobile_line_height_adjustment' => 0.1,
            'mobile_letter_spacing_adjustment' => 0.01,

            // Print typography
            'print_font_optimization' => true,
            'print_font_family' => 'Georgia',
            'print_font_size_adjustment' => 1.1,
        );

        // Allow customization of defaults
        self::$typography_defaults = apply_filters('recruitpro_typography_defaults', self::$typography_defaults);
    }

    /**
     * Add customizer typography settings
     *
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public static function add_customizer_settings($wp_customize) {
        // =================================================================
        // TYPOGRAPHY PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_typography_panel', array(
            'title' => __('Typography & Fonts', 'recruitpro'),
            'description' => __('Configure typography settings, font combinations, and text styling for your recruitment website.', 'recruitpro'),
            'priority' => 50,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // TYPOGRAPHY PRESETS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_typography_presets', array(
            'title' => __('Typography Presets', 'recruitpro'),
            'description' => __('Choose from professionally designed typography combinations tailored for different recruitment industries.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 10,
        ));

        // Typography preset selector
        $wp_customize->add_setting('recruitpro_typography_preset', array(
            'default' => self::$typography_defaults['typography_preset'],
            'sanitize_callback' => array(__CLASS__, 'sanitize_typography_preset'),
            'transport' => 'refresh',
        ));

        $preset_choices = array();
        foreach (self::$typography_presets as $key => $preset) {
            $preset_choices[$key] = $preset['name'];
        }

        $wp_customize->add_control('recruitpro_typography_preset', array(
            'label' => __('Typography Preset', 'recruitpro'),
            'description' => __('Select a typography combination designed for your industry sector.', 'recruitpro'),
            'section' => 'recruitpro_typography_presets',
            'type' => 'select',
            'choices' => $preset_choices,
        ));

        // Custom typography toggle
        $wp_customize->add_setting('recruitpro_custom_typography', array(
            'default' => self::$typography_defaults['custom_typography'],
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_custom_typography', array(
            'label' => __('Enable Custom Typography', 'recruitpro'),
            'description' => __('Override preset with custom font selections and sizing.', 'recruitpro'),
            'section' => 'recruitpro_typography_presets',
            'type' => 'checkbox',
        ));

        // =================================================================
        // FONT FAMILIES SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_font_families', array(
            'title' => __('Font Families', 'recruitpro'),
            'description' => __('Select specific font families for different elements of your website.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 20,
            'active_callback' => array(__CLASS__, 'is_custom_typography_enabled'),
        ));

        // Font family choices
        $font_choices = self::get_font_family_choices();

        $font_settings = array(
            'heading_font_family' => __('Heading Font', 'recruitpro'),
            'body_font_family' => __('Body Font', 'recruitpro'),
            'menu_font_family' => __('Menu Font', 'recruitpro'),
        );

        foreach ($font_settings as $setting => $label) {
            $wp_customize->add_setting('recruitpro_' . $setting, array(
                'default' => self::$typography_defaults[$setting],
                'sanitize_callback' => 'sanitize_text_field',
                'transport' => 'postMessage',
            ));

            $wp_customize->add_control('recruitpro_' . $setting, array(
                'label' => $label,
                'section' => 'recruitpro_font_families',
                'type' => 'select',
                'choices' => $font_choices,
            ));
        }

        // =================================================================
        // FONT SIZES SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_font_sizes', array(
            'title' => __('Font Sizes', 'recruitpro'),
            'description' => __('Adjust font sizes for different text elements. Sizes are in pixels.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 30,
            'active_callback' => array(__CLASS__, 'is_custom_typography_enabled'),
        ));

        // Base font size
        $wp_customize->add_setting('recruitpro_font_size_base', array(
            'default' => self::$typography_defaults['font_size_base'],
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_font_size_base', array(
            'label' => __('Base Font Size (px)', 'recruitpro'),
            'description' => __('Base font size that other sizes are calculated from.', 'recruitpro'),
            'section' => 'recruitpro_font_sizes',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 12,
                'max' => 24,
                'step' => 1,
            ),
        ));

        // Font scale
        $wp_customize->add_setting('recruitpro_font_scale', array(
            'default' => self::$typography_defaults['font_scale'],
            'sanitize_callback' => array(__CLASS__, 'sanitize_font_scale'),
            'transport' => 'postMessage',
        ));

        $scale_choices = array();
        foreach (self::$font_scales as $key => $scale) {
            $scale_choices[$key] = $scale['name'];
        }

        $wp_customize->add_control('recruitpro_font_scale', array(
            'label' => __('Font Scale', 'recruitpro'),
            'description' => __('Proportional relationship between font sizes.', 'recruitpro'),
            'section' => 'recruitpro_font_sizes',
            'type' => 'select',
            'choices' => $scale_choices,
        ));

        // Individual heading sizes
        $heading_sizes = array(
            'h1' => __('H1 Font Size (px)', 'recruitpro'),
            'h2' => __('H2 Font Size (px)', 'recruitpro'),
            'h3' => __('H3 Font Size (px)', 'recruitpro'),
            'h4' => __('H4 Font Size (px)', 'recruitpro'),
            'h5' => __('H5 Font Size (px)', 'recruitpro'),
            'h6' => __('H6 Font Size (px)', 'recruitpro'),
        );

        foreach ($heading_sizes as $heading => $label) {
            $wp_customize->add_setting('recruitpro_font_size_' . $heading, array(
                'default' => self::$typography_defaults['font_size_' . $heading],
                'sanitize_callback' => 'absint',
                'transport' => 'postMessage',
            ));

            $wp_customize->add_control('recruitpro_font_size_' . $heading, array(
                'label' => $label,
                'section' => 'recruitpro_font_sizes',
                'type' => 'range',
                'input_attrs' => array(
                    'min' => 14,
                    'max' => 72,
                    'step' => 1,
                ),
            ));
        }

        // =================================================================
        // SPACING & WEIGHTS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_typography_spacing', array(
            'title' => __('Spacing & Weights', 'recruitpro'),
            'description' => __('Fine-tune line heights, letter spacing, and font weights.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 40,
            'active_callback' => array(__CLASS__, 'is_custom_typography_enabled'),
        ));

        // Line height
        $wp_customize->add_setting('recruitpro_line_height_base', array(
            'default' => self::$typography_defaults['line_height_base'],
            'sanitize_callback' => array(__CLASS__, 'sanitize_decimal'),
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_line_height_base', array(
            'label' => __('Base Line Height', 'recruitpro'),
            'description' => __('Line height for body text (1.2 = 120% of font size).', 'recruitpro'),
            'section' => 'recruitpro_typography_spacing',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 1.0,
                'max' => 2.0,
                'step' => 0.1,
            ),
        ));

        // Heading line height
        $wp_customize->add_setting('recruitpro_line_height_headings', array(
            'default' => self::$typography_defaults['line_height_headings'],
            'sanitize_callback' => array(__CLASS__, 'sanitize_decimal'),
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_line_height_headings', array(
            'label' => __('Heading Line Height', 'recruitpro'),
            'description' => __('Line height for headings.', 'recruitpro'),
            'section' => 'recruitpro_typography_spacing',
            'type' => 'range',
            'input_attrs' => array(
                'min' => 1.0,
                'max' => 1.8,
                'step' => 0.1,
            ),
        ));

        // Letter spacing
        $wp_customize->add_setting('recruitpro_letter_spacing_normal', array(
            'default' => self::$typography_defaults['letter_spacing_normal'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_letter_spacing_normal', array(
            'label' => __('Letter Spacing (em)', 'recruitpro'),
            'description' => __('Letter spacing for body text (0.01em = slight spacing).', 'recruitpro'),
            'section' => 'recruitpro_typography_spacing',
            'type' => 'text',
        ));

        // =================================================================
        // GOOGLE FONTS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_google_fonts', array(
            'title' => __('Google Fonts', 'recruitpro'),
            'description' => __('Configure Google Fonts loading and optimization settings.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 50,
        ));

        // Google Fonts enabled
        $wp_customize->add_setting('recruitpro_google_fonts_enabled', array(
            'default' => self::$typography_defaults['google_fonts_enabled'],
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_google_fonts_enabled', array(
            'label' => __('Enable Google Fonts', 'recruitpro'),
            'description' => __('Load Google Fonts for better typography. Disable for privacy or performance.', 'recruitpro'),
            'section' => 'recruitpro_google_fonts',
            'type' => 'checkbox',
        ));

        // Font display strategy
        $wp_customize->add_setting('recruitpro_google_fonts_display', array(
            'default' => self::$typography_defaults['google_fonts_display'],
            'sanitize_callback' => array(__CLASS__, 'sanitize_font_display'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_google_fonts_display', array(
            'label' => __('Font Display Strategy', 'recruitpro'),
            'description' => __('How fonts should be displayed while loading.', 'recruitpro'),
            'section' => 'recruitpro_google_fonts',
            'type' => 'select',
            'choices' => array(
                'auto' => __('Auto', 'recruitpro'),
                'block' => __('Block', 'recruitpro'),
                'swap' => __('Swap (Recommended)', 'recruitpro'),
                'fallback' => __('Fallback', 'recruitpro'),
                'optional' => __('Optional', 'recruitpro'),
            ),
            'active_callback' => array(__CLASS__, 'is_google_fonts_enabled'),
        ));

        // =================================================================
        // ACCESSIBILITY SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_typography_accessibility', array(
            'title' => __('Accessibility', 'recruitpro'),
            'description' => __('Typography accessibility features for better readability.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 60,
        ));

        // High contrast mode
        $wp_customize->add_setting('recruitpro_high_contrast_mode', array(
            'default' => self::$typography_defaults['high_contrast_mode'],
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_high_contrast_mode', array(
            'label' => __('High Contrast Mode', 'recruitpro'),
            'description' => __('Increase text contrast for better readability.', 'recruitpro'),
            'section' => 'recruitpro_typography_accessibility',
            'type' => 'checkbox',
        ));

        // Large text mode
        $wp_customize->add_setting('recruitpro_large_text_mode', array(
            'default' => self::$typography_defaults['large_text_mode'],
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_large_text_mode', array(
            'label' => __('Large Text Mode', 'recruitpro'),
            'description' => __('Increase all font sizes by 20% for better accessibility.', 'recruitpro'),
            'section' => 'recruitpro_typography_accessibility',
            'type' => 'checkbox',
        ));

        // Dyslexia friendly mode
        $wp_customize->add_setting('recruitpro_dyslexia_friendly_mode', array(
            'default' => self::$typography_defaults['dyslexia_friendly_mode'],
            'sanitize_callback' => 'rest_sanitize_boolean',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_dyslexia_friendly_mode', array(
            'label' => __('Dyslexia Friendly Mode', 'recruitpro'),
            'description' => __('Use dyslexia-friendly font and spacing adjustments.', 'recruitpro'),
            'section' => 'recruitpro_typography_accessibility',
            'type' => 'checkbox',
        ));
    }

    /**
     * Output typography CSS
     *
     * @since 1.0.0
     * @return void
     */
    public static function output_typography_css() {
        $custom_typography = get_theme_mod('recruitpro_custom_typography', self::$typography_defaults['custom_typography']);
        
        if ($custom_typography) {
            $css = self::generate_custom_typography_css();
        } else {
            $preset = get_theme_mod('recruitpro_typography_preset', self::$typography_defaults['typography_preset']);
            $css = self::generate_preset_typography_css($preset);
        }

        // Add accessibility adjustments
        $css .= self::generate_accessibility_css();

        if (!empty($css)) {
            echo '<style id="recruitpro-typography-styles" type="text/css">' . wp_strip_all_tags($css) . '</style>';
        }
    }

    /**
     * Generate custom typography CSS
     *
     * @since 1.0.0
     * @return string CSS rules
     */
    private static function generate_custom_typography_css() {
        $css = '';

        // Get settings
        $base_size = get_theme_mod('recruitpro_font_size_base', self::$typography_defaults['font_size_base']);
        $heading_font = get_theme_mod('recruitpro_heading_font_family', self::$typography_defaults['heading_font_family']);
        $body_font = get_theme_mod('recruitpro_body_font_family', self::$typography_defaults['body_font_family']);
        $menu_font = get_theme_mod('recruitpro_menu_font_family', self::$typography_defaults['menu_font_family']);
        $line_height_base = get_theme_mod('recruitpro_line_height_base', self::$typography_defaults['line_height_base']);
        $line_height_headings = get_theme_mod('recruitpro_line_height_headings', self::$typography_defaults['line_height_headings']);
        $letter_spacing = get_theme_mod('recruitpro_letter_spacing_normal', self::$typography_defaults['letter_spacing_normal']);

        // CSS Variables
        $css .= ':root {';
        $css .= '--recruitpro-font-size-base: ' . intval($base_size) . 'px;';
        $css .= '--recruitpro-font-family-heading: ' . self::get_font_stack($heading_font) . ';';
        $css .= '--recruitpro-font-family-body: ' . self::get_font_stack($body_font) . ';';
        $css .= '--recruitpro-font-family-menu: ' . self::get_font_stack($menu_font) . ';';
        $css .= '--recruitpro-line-height-base: ' . floatval($line_height_base) . ';';
        $css .= '--recruitpro-line-height-headings: ' . floatval($line_height_headings) . ';';
        $css .= '--recruitpro-letter-spacing-normal: ' . esc_attr($letter_spacing) . ';';

        // Font sizes
        $headings = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($headings as $heading) {
            $size = get_theme_mod('recruitpro_font_size_' . $heading, self::$typography_defaults['font_size_' . $heading]);
            $css .= '--recruitpro-font-size-' . $heading . ': ' . intval($size) . 'px;';
        }

        $css .= '}';

        // Base typography
        $css .= 'body {';
        $css .= 'font-family: var(--recruitpro-font-family-body);';
        $css .= 'font-size: var(--recruitpro-font-size-base);';
        $css .= 'line-height: var(--recruitpro-line-height-base);';
        $css .= 'letter-spacing: var(--recruitpro-letter-spacing-normal);';
        $css .= '}';

        // Headings
        $css .= 'h1, h2, h3, h4, h5, h6 {';
        $css .= 'font-family: var(--recruitpro-font-family-heading);';
        $css .= 'line-height: var(--recruitpro-line-height-headings);';
        $css .= '}';

        foreach ($headings as $heading) {
            $css .= $heading . ' { font-size: var(--recruitpro-font-size-' . $heading . '); }';
        }

        // Menu typography
        $css .= '.main-navigation, .main-navigation a {';
        $css .= 'font-family: var(--recruitpro-font-family-menu);';
        $css .= '}';

        return $css;
    }

    /**
     * Generate preset typography CSS
     *
     * @since 1.0.0
     * @param string $preset Preset name
     * @return string CSS rules
     */
    private static function generate_preset_typography_css($preset) {
        if (!isset(self::$typography_presets[$preset])) {
            $preset = 'corporate_executive';
        }

        $preset_data = self::$typography_presets[$preset];
        $css = '';

        // CSS Variables from preset
        $css .= ':root {';
        $css .= '--recruitpro-font-family-heading: ' . self::get_font_stack($preset_data['heading_font']) . ';';
        $css .= '--recruitpro-font-family-body: ' . self::get_font_stack($preset_data['body_font']) . ';';
        $css .= '--recruitpro-font-family-menu: ' . self::get_font_stack($preset_data['menu_font']) . ';';
        $css .= '--recruitpro-font-size-base: ' . intval($preset_data['font_size_base']) . 'px;';
        $css .= '--recruitpro-line-height-base: ' . floatval($preset_data['line_height_base']) . ';';
        $css .= '--recruitpro-font-weight-normal: ' . intval($preset_data['font_weight_normal']) . ';';
        $css .= '--recruitpro-font-weight-bold: ' . intval($preset_data['font_weight_bold']) . ';';
        $css .= '--recruitpro-letter-spacing-normal: ' . esc_attr($preset_data['letter_spacing_normal']) . ';';
        $css .= '--recruitpro-letter-spacing-headings: ' . esc_attr($preset_data['letter_spacing_headings']) . ';';

        // Font sizes from preset
        foreach ($preset_data['font_sizes'] as $element => $size) {
            $css .= '--recruitpro-font-size-' . $element . ': ' . intval($size) . 'px;';
        }

        $css .= '}';

        // Apply typography
        $css .= 'body {';
        $css .= 'font-family: var(--recruitpro-font-family-body);';
        $css .= 'font-size: var(--recruitpro-font-size-base);';
        $css .= 'line-height: var(--recruitpro-line-height-base);';
        $css .= 'font-weight: var(--recruitpro-font-weight-normal);';
        $css .= 'letter-spacing: var(--recruitpro-letter-spacing-normal);';
        $css .= '}';

        $css .= 'h1, h2, h3, h4, h5, h6 {';
        $css .= 'font-family: var(--recruitpro-font-family-heading);';
        $css .= 'font-weight: var(--recruitpro-font-weight-bold);';
        $css .= 'letter-spacing: var(--recruitpro-letter-spacing-headings);';
        $css .= '}';

        // Individual heading sizes
        $headings = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($headings as $heading) {
            if (isset($preset_data['font_sizes'][$heading])) {
                $css .= $heading . ' { font-size: var(--recruitpro-font-size-' . $heading . '); }';
            }
        }

        return $css;
    }

    /**
     * Generate accessibility CSS
     *
     * @since 1.0.0
     * @return string CSS rules
     */
    private static function generate_accessibility_css() {
        $css = '';

        // High contrast mode
        if (get_theme_mod('recruitpro_high_contrast_mode', self::$typography_defaults['high_contrast_mode'])) {
            $css .= '.high-contrast-mode {';
            $css .= 'color: #000000 !important;';
            $css .= 'background-color: #ffffff !important;';
            $css .= '}';
            $css .= '.high-contrast-mode a { color: #0000ff !important; }';
            $css .= '.high-contrast-mode a:visited { color: #800080 !important; }';
        }

        // Large text mode
        if (get_theme_mod('recruitpro_large_text_mode', self::$typography_defaults['large_text_mode'])) {
            $css .= '.large-text-mode {';
            $css .= 'font-size: 120% !important;';
            $css .= '}';
        }

        // Dyslexia friendly mode
        if (get_theme_mod('recruitpro_dyslexia_friendly_mode', self::$typography_defaults['dyslexia_friendly_mode'])) {
            $css .= '.dyslexia-friendly-mode {';
            $css .= 'font-family: "OpenDyslexic", Arial, sans-serif !important;';
            $css .= 'letter-spacing: 0.05em !important;';
            $css .= 'word-spacing: 0.16em !important;';
            $css .= 'line-height: 1.8 !important;';
            $css .= '}';
        }

        return $css;
    }

    /**
     * Get font stack for a font family
     *
     * @since 1.0.0
     * @param string $font_family Font family name
     * @return string Complete font stack
     */
    private static function get_font_stack($font_family) {
        // Check if it's a system font with predefined stack
        if (isset(self::$font_families['system_fonts'][$font_family]['fallback_stack'])) {
            return self::$font_families['system_fonts'][$font_family]['fallback_stack'];
        }

        // For Google Fonts, add appropriate fallbacks
        $fallback_stacks = array(
            'sans-serif' => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif',
            'serif' => '"Times New Roman", Times, Georgia, serif',
            'monospace' => '"Courier New", Courier, monospace',
        );

        // Determine font category
        $category = 'sans-serif'; // default
        foreach (self::$font_families as $group => $fonts) {
            if (isset($fonts[$font_family])) {
                $category = $fonts[$font_family]['category'];
                break;
            }
        }

        // Quote font name if it contains spaces
        $quoted_font = (strpos($font_family, ' ') !== false) ? '"' . $font_family . '"' : $font_family;
        
        return $quoted_font . ', ' . $fallback_stacks[$category];
    }

    /**
     * Enqueue typography fonts
     *
     * @since 1.0.0
     * @return void
     */
    public static function enqueue_typography_fonts() {
        if (!get_theme_mod('recruitpro_google_fonts_enabled', self::$typography_defaults['google_fonts_enabled'])) {
            return;
        }

        $fonts_to_load = array();
        $custom_typography = get_theme_mod('recruitpro_custom_typography', self::$typography_defaults['custom_typography']);

        if ($custom_typography) {
            // Custom typography fonts
            $heading_font = get_theme_mod('recruitpro_heading_font_family', self::$typography_defaults['heading_font_family']);
            $body_font = get_theme_mod('recruitpro_body_font_family', self::$typography_defaults['body_font_family']);
            $menu_font = get_theme_mod('recruitpro_menu_font_family', self::$typography_defaults['menu_font_family']);

            $fonts_to_load = array_unique(array($heading_font, $body_font, $menu_font));
        } else {
            // Preset fonts
            $preset = get_theme_mod('recruitpro_typography_preset', self::$typography_defaults['typography_preset']);
            if (isset(self::$typography_presets[$preset])) {
                $preset_data = self::$typography_presets[$preset];
                $fonts_to_load = array_unique(array(
                    $preset_data['heading_font'],
                    $preset_data['body_font'],
                    $preset_data['menu_font']
                ));
            }
        }

        // Filter out system fonts
        $google_fonts = array();
        foreach ($fonts_to_load as $font) {
            if (!self::is_system_font($font) && !empty($font)) {
                $google_fonts[] = $font;
            }
        }

        if (!empty($google_fonts)) {
            $font_url = self::build_google_fonts_url($google_fonts);
            if ($font_url) {
                wp_enqueue_style('recruitpro-google-fonts', $font_url, array(), null);
            }
        }
    }

    /**
     * Output font preconnect links
     *
     * @since 1.0.0
     * @return void
     */
    public static function output_font_preconnect() {
        if (!get_theme_mod('recruitpro_google_fonts_enabled', self::$typography_defaults['google_fonts_enabled'])) {
            return;
        }

        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    }

    /**
     * Build Google Fonts URL
     *
     * @since 1.0.0
     * @param array $fonts Array of font family names
     * @return string|false Google Fonts URL or false
     */
    private static function build_google_fonts_url($fonts) {
        if (empty($fonts)) {
            return false;
        }

        $font_families = array();
        $display = get_theme_mod('recruitpro_google_fonts_display', self::$typography_defaults['google_fonts_display']);

        foreach ($fonts as $font) {
            // Get font variants for this font
            $variants = self::get_font_variants($font);
            if (!empty($variants)) {
                $font_families[] = str_replace(' ', '+', $font) . ':' . implode(',', $variants);
            }
        }

        if (empty($font_families)) {
            return false;
        }

        $args = array(
            'family' => implode('|', $font_families),
            'display' => $display,
            'subset' => 'latin,latin-ext',
        );

        return add_query_arg($args, 'https://fonts.googleapis.com/css2');
    }

    /**
     * Get font variants for a Google Font
     *
     * @since 1.0.0
     * @param string $font_family Font family name
     * @return array Font variants
     */
    private static function get_font_variants($font_family) {
        // Check if font exists in our font families array
        foreach (self::$font_families as $group => $fonts) {
            if (isset($fonts[$font_family]) && isset($fonts[$font_family]['variants'])) {
                return $fonts[$font_family]['variants'];
            }
        }

        // Default variants for unknown fonts
        return array('400', '600', '700');
    }

    /**
     * Check if font is a system font
     *
     * @since 1.0.0
     * @param string $font_family Font family name
     * @return bool True if system font
     */
    private static function is_system_font($font_family) {
        return isset(self::$font_families['system_fonts'][$font_family]);
    }

    /**
     * Get font family choices for customizer
     *
     * @since 1.0.0
     * @return array Font choices
     */
    private static function get_font_family_choices() {
        $choices = array();

        // Add system fonts first
        $choices[__('System Fonts', 'recruitpro')] = array();
        foreach (self::$font_families['system_fonts'] as $font => $data) {
            $choices[__('System Fonts', 'recruitpro')][$font] = $data['name'];
        }

        // Add Google Fonts
        $choices[__('Google Fonts - Sans Serif', 'recruitpro')] = array();
        foreach (self::$font_families['google_sans'] as $font => $data) {
            $choices[__('Google Fonts - Sans Serif', 'recruitpro')][$font] = $data['name'];
        }

        $choices[__('Google Fonts - Serif', 'recruitpro')] = array();
        foreach (self::$font_families['google_serif'] as $font => $data) {
            $choices[__('Google Fonts - Serif', 'recruitpro')][$font] = $data['name'];
        }

        return $choices;
    }

    /**
     * Enqueue admin typography
     *
     * @since 1.0.0
     * @param string $hook_suffix Current admin page
     * @return void
     */
    public static function enqueue_admin_typography($hook_suffix) {
        // Only load on customizer and typography admin pages
        if (!in_array($hook_suffix, array('customize.php', 'appearance_page_recruitpro-typography'))) {
            return;
        }

        // Load fonts for preview
        self::enqueue_typography_fonts();
    }

    /**
     * Add typography admin page
     *
     * @since 1.0.0
     * @return void
     */
    public static function add_typography_admin_page() {
        add_theme_page(
            __('Typography Settings', 'recruitpro'),
            __('Typography', 'recruitpro'),
            'edit_theme_options',
            'recruitpro-typography',
            array(__CLASS__, 'render_typography_admin_page')
        );
    }

    /**
     * Register typography settings for admin
     *
     * @since 1.0.0
     * @return void
     */
    public static function register_typography_settings() {
        register_setting('recruitpro_typography', 'recruitpro_typography_options', array(
            'sanitize_callback' => array(__CLASS__, 'sanitize_typography_options'),
        ));
    }

    /**
     * Render typography admin page
     *
     * @since 1.0.0
     * @return void
     */
    public static function render_typography_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Typography Settings', 'recruitpro'); ?></h1>
            
            <div class="recruitpro-typography-admin">
                <div class="typography-preview">
                    <h2><?php esc_html_e('Typography Preview', 'recruitpro'); ?></h2>
                    <div class="preview-content">
                        <h1>Heading 1 - Your Dream Career Awaits</h1>
                        <h2>Heading 2 - Professional Recruitment Services</h2>
                        <h3>Heading 3 - Find the Perfect Match</h3>
                        <h4>Heading 4 - Industry Expertise</h4>
                        <h5>Heading 5 - Career Opportunities</h5>
                        <h6>Heading 6 - Contact Information</h6>
                        <p>Body text: We are a leading recruitment agency specializing in connecting top talent with exceptional opportunities. Our team of experienced recruiters understands the unique requirements of different industries and works tirelessly to ensure the perfect match between candidates and employers.</p>
                        <p><strong>Bold text:</strong> Professional recruitment solutions for the modern workforce.</p>
                        <p><em>Italic text:</em> Connecting careers, building futures.</p>
                        <ul>
                            <li>Executive search and placement</li>
                            <li>Permanent and contract recruitment</li>
                            <li>Career consultation services</li>
                        </ul>
                    </div>
                </div>

                <div class="typography-controls">
                    <h2><?php esc_html_e('Quick Actions', 'recruitpro'); ?></h2>
                    <p>
                        <a href="<?php echo esc_url(admin_url('customize.php?autofocus[panel]=recruitpro_typography_panel')); ?>" class="button button-primary">
                            <?php esc_html_e('Open Typography Customizer', 'recruitpro'); ?>
                        </a>
                    </p>
                    
                    <h3><?php esc_html_e('Typography Presets', 'recruitpro'); ?></h3>
                    <div class="preset-grid">
                        <?php foreach (self::$typography_presets as $key => $preset) : ?>
                            <div class="preset-card">
                                <h4><?php echo esc_html($preset['name']); ?></h4>
                                <p><?php echo esc_html($preset['description']); ?></p>
                                <p class="preset-fonts">
                                    <small>
                                        <?php printf(__('Heading: %s | Body: %s', 'recruitpro'), $preset['heading_font'], $preset['body_font']); ?>
                                    </small>
                                </p>
                                <button class="button apply-preset" data-preset="<?php echo esc_attr($key); ?>">
                                    <?php esc_html_e('Apply Preset', 'recruitpro'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .recruitpro-typography-admin {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        .typography-preview {
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .preview-content {
            font-family: var(--recruitpro-font-family-body, inherit);
            line-height: var(--recruitpro-line-height-base, 1.6);
        }
        .typography-controls {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            height: fit-content;
        }
        .preset-grid {
            display: grid;
            gap: 15px;
        }
        .preset-card {
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 4px;
            background: #fafafa;
        }
        .preset-card h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: 600;
        }
        .preset-card p {
            margin: 5px 0;
            font-size: 13px;
        }
        .preset-fonts {
            color: #666;
        }
        .apply-preset {
            margin-top: 10px;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('.apply-preset').click(function() {
                var preset = $(this).data('preset');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'recruitpro_apply_typography_preset',
                        preset: preset,
                        nonce: '<?php echo wp_create_nonce('recruitpro_typography_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Failed to apply preset');
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * AJAX handler for applying typography preset
     *
     * @since 1.0.0
     * @return void
     */
    public static function ajax_apply_typography_preset() {
        check_ajax_referer('recruitpro_typography_nonce', 'nonce');
        
        if (!current_user_can('edit_theme_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $preset = sanitize_key($_POST['preset']);
        
        if (!isset(self::$typography_presets[$preset])) {
            wp_send_json_error(array('message' => __('Invalid preset.', 'recruitpro')));
        }

        // Apply preset
        set_theme_mod('recruitpro_typography_preset', $preset);
        set_theme_mod('recruitpro_custom_typography', false);

        wp_send_json_success(array('message' => __('Preset applied successfully.', 'recruitpro')));
    }

    /**
     * AJAX handler for typography preview
     *
     * @since 1.0.0
     * @return void
     */
    public static function ajax_preview_typography() {
        check_ajax_referer('recruitpro_typography_nonce', 'nonce');
        
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
        
        // Generate preview CSS
        $css = self::generate_preview_css($settings);
        
        wp_send_json_success(array('css' => $css));
    }

    /**
     * Generate preview CSS for AJAX
     *
     * @since 1.0.0
     * @param array $settings Typography settings
     * @return string CSS rules
     */
    private static function generate_preview_css($settings) {
        // This would generate CSS based on the provided settings
        // Implementation would depend on the specific preview requirements
        return '';
    }

    /**
     * Get typography presets
     *
     * @since 1.0.0
     * @return array Typography presets
     */
    public static function get_typography_presets() {
        return self::$typography_presets;
    }

    /**
     * Get font families
     *
     * @since 1.0.0
     * @return array Font families
     */
    public static function get_font_families() {
        return self::$font_families;
    }

    /**
     * Get CSS variables for typography
     *
     * @since 1.0.0
     * @return array CSS variables
     */
    public static function get_css_variables() {
        // Generate CSS variables based on current settings
        $variables = array();
        
        $custom_typography = get_theme_mod('recruitpro_custom_typography', self::$typography_defaults['custom_typography']);
        
        if ($custom_typography) {
            // Custom variables
            $variables['--recruitpro-font-size-base'] = get_theme_mod('recruitpro_font_size_base', self::$typography_defaults['font_size_base']) . 'px';
            $variables['--recruitpro-line-height-base'] = get_theme_mod('recruitpro_line_height_base', self::$typography_defaults['line_height_base']);
        } else {
            // Preset variables
            $preset = get_theme_mod('recruitpro_typography_preset', self::$typography_defaults['typography_preset']);
            if (isset(self::$typography_presets[$preset])) {
                $preset_data = self::$typography_presets[$preset];
                $variables['--recruitpro-font-size-base'] = $preset_data['font_size_base'] . 'px';
                $variables['--recruitpro-line-height-base'] = $preset_data['line_height_base'];
            }
        }

        return $variables;
    }

    /**
     * Sanitization functions
     */

    public static function sanitize_typography_preset($input) {
        return array_key_exists($input, self::$typography_presets) ? $input : 'corporate_executive';
    }

    public static function sanitize_font_scale($input) {
        return array_key_exists($input, self::$font_scales) ? $input : 'major_third';
    }

    public static function sanitize_font_display($input) {
        $valid = array('auto', 'block', 'swap', 'fallback', 'optional');
        return in_array($input, $valid) ? $input : 'swap';
    }

    public static function sanitize_decimal($input) {
        return max(0.5, min(3.0, floatval($input)));
    }

    public static function sanitize_typography_options($input) {
        // Sanitize array of typography options
        $sanitized = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $sanitized[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * Active callback functions
     */

    public static function is_custom_typography_enabled() {
        return get_theme_mod('recruitpro_custom_typography', self::$typography_defaults['custom_typography']);
    }

    public static function is_google_fonts_enabled() {
        return get_theme_mod('recruitpro_google_fonts_enabled', self::$typography_defaults['google_fonts_enabled']);
    }
}

// Initialize typography system
RecruitPro_Typography::init();

/**
 * Convenience functions for accessing typography system
 */

if (!function_exists('recruitpro_get_typography_preset')) {
    /**
     * Get current typography preset
     *
     * @since 1.0.0
     * @return string Preset name
     */
    function recruitpro_get_typography_preset() {
        return get_theme_mod('recruitpro_typography_preset', 'corporate_executive');
    }
}

if (!function_exists('recruitpro_get_font_family')) {
    /**
     * Get font family for element
     *
     * @since 1.0.0
     * @param string $element Element type (heading, body, menu)
     * @return string Font family
     */
    function recruitpro_get_font_family($element = 'body') {
        $custom_typography = get_theme_mod('recruitpro_custom_typography', false);
        
        if ($custom_typography) {
            return get_theme_mod('recruitpro_' . $element . '_font_family', 'Roboto');
        } else {
            $preset = recruitpro_get_typography_preset();
            $presets = RecruitPro_Typography::get_typography_presets();
            
            if (isset($presets[$preset])) {
                return $presets[$preset][$element . '_font'];
            }
        }
        
        return 'Roboto';
    }
}

if (!function_exists('recruitpro_get_font_size')) {
    /**
     * Get font size for element
     *
     * @since 1.0.0
     * @param string $element Element type (base, h1, h2, etc.)
     * @return int Font size in pixels
     */
    function recruitpro_get_font_size($element = 'base') {
        $custom_typography = get_theme_mod('recruitpro_custom_typography', false);
        
        if ($custom_typography) {
            return get_theme_mod('recruitpro_font_size_' . $element, 16);
        } else {
            $preset = recruitpro_get_typography_preset();
            $presets = RecruitPro_Typography::get_typography_presets();
            
            if (isset($presets[$preset]['font_sizes'][$element])) {
                return $presets[$preset]['font_sizes'][$element];
            }
        }
        
        return 16;
    }
}

if (!function_exists('recruitpro_typography_css_variables')) {
    /**
     * Output typography CSS variables
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_typography_css_variables() {
        $variables = RecruitPro_Typography::get_css_variables();
        
        if (!empty($variables)) {
            echo '<style id="recruitpro-typography-variables">';
            echo ':root {';
            foreach ($variables as $property => $value) {
                echo esc_attr($property) . ': ' . esc_attr($value) . ';';
            }
            echo '}';
            echo '</style>';
        }
    }
}

// End of typography.php