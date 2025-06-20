<?php
/**
 * RecruitPro Theme Google Fonts Management
 *
 * This file handles Google Fonts integration for the RecruitPro recruitment
 * website theme. It includes font selection, optimization, performance
 * enhancements, and professional typography combinations suitable for
 * recruitment agencies and HR consultancies.
 *
 * @package RecruitPro
 * @subpackage Theme/Typography
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/google-fonts.php
 * Purpose: Google Fonts management and optimization
 * Dependencies: WordPress core, theme functions, customizer
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Google Fonts Management Class
 * 
 * Handles all Google Fonts functionality including selection,
 * optimization, and performance enhancements.
 *
 * @since 1.0.0
 */
class RecruitPro_Google_Fonts {

    /**
     * Font combinations for recruitment websites
     * 
     * @since 1.0.0
     * @var array
     */
    private $font_combinations = array();

    /**
     * Available Google Fonts
     * 
     * @since 1.0.0
     * @var array
     */
    private $available_fonts = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_font_combinations();
        $this->init_available_fonts();
        
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_action('wp_head', array($this, 'output_preconnect_links'), 1);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_google_fonts'));
        add_action('wp_head', array($this, 'output_font_css_variables'), 20);
        add_filter('recruitpro_customizer_fonts', array($this, 'get_font_choices'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_fonts'));
    }

    /**
     * Initialize professional font combinations for recruitment websites
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_font_combinations() {
        
        $this->font_combinations = array(
            'professional_modern' => array(
                'name' => __('Professional Modern', 'recruitpro'),
                'description' => __('Clean and modern fonts perfect for corporate recruitment.', 'recruitpro'),
                'primary' => 'Inter',
                'secondary' => 'Inter',
                'heading' => 'Poppins',
                'body_weight' => '400',
                'heading_weight' => '600',
            ),
            'corporate_classic' => array(
                'name' => __('Corporate Classic', 'recruitpro'),
                'description' => __('Traditional professional fonts for established agencies.', 'recruitpro'),
                'primary' => 'Open Sans',
                'secondary' => 'Open Sans',
                'heading' => 'Montserrat',
                'body_weight' => '400',
                'heading_weight' => '600',
            ),
            'executive_elegant' => array(
                'name' => __('Executive Elegant', 'recruitpro'),
                'description' => __('Sophisticated fonts for executive search firms.', 'recruitpro'),
                'primary' => 'Source Sans Pro',
                'secondary' => 'Source Sans Pro',
                'heading' => 'Playfair Display',
                'body_weight' => '400',
                'heading_weight' => '700',
            ),
            'tech_contemporary' => array(
                'name' => __('Tech Contemporary', 'recruitpro'),
                'description' => __('Modern fonts ideal for tech recruitment agencies.', 'recruitpro'),
                'primary' => 'Roboto',
                'secondary' => 'Roboto',
                'heading' => 'Nunito Sans',
                'body_weight' => '400',
                'heading_weight' => '700',
            ),
            'creative_dynamic' => array(
                'name' => __('Creative Dynamic', 'recruitpro'),
                'description' => __('Energetic fonts for creative industry recruitment.', 'recruitpro'),
                'primary' => 'Lato',
                'secondary' => 'Lato',
                'heading' => 'Raleway',
                'body_weight' => '400',
                'heading_weight' => '600',
            ),
            'healthcare_trustworthy' => array(
                'name' => __('Healthcare Trustworthy', 'recruitpro'),
                'description' => __('Reliable fonts for healthcare recruitment specialists.', 'recruitpro'),
                'primary' => 'Noto Sans',
                'secondary' => 'Noto Sans',
                'heading' => 'Merriweather',
                'body_weight' => '400',
                'heading_weight' => '700',
            ),
            'finance_authoritative' => array(
                'name' => __('Finance Authoritative', 'recruitpro'),
                'description' => __('Professional fonts for financial recruitment firms.', 'recruitpro'),
                'primary' => 'IBM Plex Sans',
                'secondary' => 'IBM Plex Sans',
                'heading' => 'IBM Plex Serif',
                'body_weight' => '400',
                'heading_weight' => '600',
            ),
            'startup_innovative' => array(
                'name' => __('Startup Innovative', 'recruitpro'),
                'description' => __('Fresh fonts for startup and scale-up recruitment.', 'recruitpro'),
                'primary' => 'Work Sans',
                'secondary' => 'Work Sans',
                'heading' => 'Space Grotesk',
                'body_weight' => '400',
                'heading_weight' => '600',
            ),
        );
    }

    /**
     * Initialize available Google Fonts list
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_available_fonts() {
        
        $this->available_fonts = array(
            // Sans-serif fonts - Professional and modern
            'Inter' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '500', '600', '700'),
                'description' => __('Modern, highly readable font perfect for digital interfaces', 'recruitpro'),
            ),
            'Open Sans' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '600', '700'),
                'description' => __('Friendly and professional, excellent for body text', 'recruitpro'),
            ),
            'Source Sans Pro' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '600', '700'),
                'description' => __('Clean and elegant, designed for user interfaces', 'recruitpro'),
            ),
            'Roboto' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '500', '700'),
                'description' => __('Google\'s signature font, modern and versatile', 'recruitpro'),
            ),
            'Lato' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '700'),
                'description' => __('Warm and approachable yet professional', 'recruitpro'),
            ),
            'Montserrat' => array(
                'category' => 'sans-serif',
                'weights' => array('400', '500', '600', '700'),
                'description' => __('Geometric and contemporary, great for headings', 'recruitpro'),
            ),
            'Poppins' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '500', '600', '700'),
                'description' => __('Rounded and friendly, perfect for modern brands', 'recruitpro'),
            ),
            'Nunito Sans' => array(
                'category' => 'sans-serif',
                'weights' => array('400', '600', '700'),
                'description' => __('Balanced and readable, excellent for tech companies', 'recruitpro'),
            ),
            'Work Sans' => array(
                'category' => 'sans-serif',
                'weights' => array('400', '500', '600', '700'),
                'description' => __('Optimized for on-screen reading, very legible', 'recruitpro'),
            ),
            'IBM Plex Sans' => array(
                'category' => 'sans-serif',
                'weights' => array('400', '500', '600', '700'),
                'description' => __('Corporate and reliable, designed by IBM', 'recruitpro'),
            ),
            'Noto Sans' => array(
                'category' => 'sans-serif',
                'weights' => array('400', '700'),
                'description' => __('Harmonious and comprehensive, supports many languages', 'recruitpro'),
            ),
            'Raleway' => array(
                'category' => 'sans-serif',
                'weights' => array('300', '400', '500', '600', '700'),
                'description' => __('Elegant and sophisticated, great for creative brands', 'recruitpro'),
            ),
            'Space Grotesk' => array(
                'category' => 'sans-serif',
                'weights' => array('400', '500', '600', '700'),
                'description' => __('Modern and distinctive, perfect for tech startups', 'recruitpro'),
            ),

            // Serif fonts - Traditional and trustworthy
            'Merriweather' => array(
                'category' => 'serif',
                'weights' => array('400', '700'),
                'description' => __('Designed for readability, excellent for body text', 'recruitpro'),
            ),
            'Playfair Display' => array(
                'category' => 'serif',
                'weights' => array('400', '700'),
                'description' => __('Elegant and distinctive, perfect for headings', 'recruitpro'),
            ),
            'IBM Plex Serif' => array(
                'category' => 'serif',
                'weights' => array('400', '600', '700'),
                'description' => __('Modern serif with corporate reliability', 'recruitpro'),
            ),
            'Crimson Text' => array(
                'category' => 'serif',
                'weights' => array('400', '600', '700'),
                'description' => __('Classic and readable, inspired by old-style fonts', 'recruitpro'),
            ),
            'Lora' => array(
                'category' => 'serif',
                'weights' => array('400', '700'),
                'description' => __('Well-balanced contemporary serif', 'recruitpro'),
            ),

            // System fonts - Performance and privacy
            'System UI' => array(
                'category' => 'system',
                'weights' => array('400', '700'),
                'description' => __('Native system font, fastest loading and privacy-friendly', 'recruitpro'),
            ),
            'Arial' => array(
                'category' => 'system',
                'weights' => array('400', '700'),
                'description' => __('Universal system font, reliable fallback', 'recruitpro'),
            ),
            'Helvetica' => array(
                'category' => 'system',
                'weights' => array('400', '700'),
                'description' => __('Classic professional font, widely available', 'recruitpro'),
            ),
        );
    }

    /**
     * Add Google Fonts customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {

        // =================================================================
        // TYPOGRAPHY PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_typography_panel', array(
            'title' => __('Typography & Fonts', 'recruitpro'),
            'description' => __('Configure fonts and typography settings for your recruitment website. Choose professional combinations that reflect your brand.', 'recruitpro'),
            'priority' => 120,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // FONT COMBINATIONS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_font_combinations', array(
            'title' => __('Font Combinations', 'recruitpro'),
            'description' => __('Choose from professionally curated font combinations for different recruitment industry sectors.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 10,
        ));

        // Font combination selector
        $wp_customize->add_setting('recruitpro_font_combination', array(
            'default' => 'professional_modern',
            'sanitize_callback' => array($this, 'sanitize_font_combination'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_font_combination', array(
            'label' => __('Font Combination', 'recruitpro'),
            'description' => __('Select a pre-designed font combination that matches your recruitment industry.', 'recruitpro'),
            'section' => 'recruitpro_font_combinations',
            'type' => 'select',
            'choices' => $this->get_font_combination_choices(),
        ));

        // Enable custom fonts override
        $wp_customize->add_setting('recruitpro_enable_custom_fonts', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_custom_fonts', array(
            'label' => __('Enable Custom Font Selection', 'recruitpro'),
            'description' => __('Override the font combination with custom font choices.', 'recruitpro'),
            'section' => 'recruitpro_font_combinations',
            'type' => 'checkbox',
        ));

        // =================================================================
        // CUSTOM FONT SELECTION SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_custom_fonts', array(
            'title' => __('Custom Font Selection', 'recruitpro'),
            'description' => __('Manually select fonts for different elements. Only use if you want to override the professional combinations.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 20,
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_custom_fonts', false);
            },
        ));

        // Primary font (body text)
        $wp_customize->add_setting('recruitpro_primary_font', array(
            'default' => 'Inter',
            'sanitize_callback' => array($this, 'sanitize_font_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_primary_font', array(
            'label' => __('Primary Font (Body Text)', 'recruitpro'),
            'description' => __('Font used for main body text and content.', 'recruitpro'),
            'section' => 'recruitpro_custom_fonts',
            'type' => 'select',
            'choices' => $this->get_font_choices(),
        ));

        // Heading font
        $wp_customize->add_setting('recruitpro_heading_font', array(
            'default' => 'Poppins',
            'sanitize_callback' => array($this, 'sanitize_font_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_heading_font', array(
            'label' => __('Heading Font', 'recruitpro'),
            'description' => __('Font used for headings (H1, H2, H3, etc.).', 'recruitpro'),
            'section' => 'recruitpro_custom_fonts',
            'type' => 'select',
            'choices' => $this->get_font_choices(),
        ));

        // Secondary font (UI elements)
        $wp_customize->add_setting('recruitpro_secondary_font', array(
            'default' => 'Inter',
            'sanitize_callback' => array($this, 'sanitize_font_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_secondary_font', array(
            'label' => __('Secondary Font (UI Elements)', 'recruitpro'),
            'description' => __('Font used for navigation, buttons, and UI elements.', 'recruitpro'),
            'section' => 'recruitpro_custom_fonts',
            'type' => 'select',
            'choices' => $this->get_font_choices(),
        ));

        // =================================================================
        // FONT WEIGHTS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_font_weights', array(
            'title' => __('Font Weights', 'recruitpro'),
            'description' => __('Configure font weights for different text elements.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 30,
        ));

        // Body font weight
        $wp_customize->add_setting('recruitpro_body_font_weight', array(
            'default' => '400',
            'sanitize_callback' => array($this, 'sanitize_font_weight'),
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_body_font_weight', array(
            'label' => __('Body Text Weight', 'recruitpro'),
            'section' => 'recruitpro_font_weights',
            'type' => 'select',
            'choices' => array(
                '300' => __('Light (300)', 'recruitpro'),
                '400' => __('Regular (400)', 'recruitpro'),
                '500' => __('Medium (500)', 'recruitpro'),
            ),
        ));

        // Heading font weight
        $wp_customize->add_setting('recruitpro_heading_font_weight', array(
            'default' => '600',
            'sanitize_callback' => array($this, 'sanitize_font_weight'),
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_heading_font_weight', array(
            'label' => __('Heading Weight', 'recruitpro'),
            'section' => 'recruitpro_font_weights',
            'type' => 'select',
            'choices' => array(
                '500' => __('Medium (500)', 'recruitpro'),
                '600' => __('Semi-Bold (600)', 'recruitpro'),
                '700' => __('Bold (700)', 'recruitpro'),
            ),
        ));

        // =================================================================
        // FONT OPTIMIZATION SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_font_optimization', array(
            'title' => __('Font Optimization', 'recruitpro'),
            'description' => __('Configure font loading optimization and performance settings.', 'recruitpro'),
            'panel' => 'recruitpro_typography_panel',
            'priority' => 40,
        ));

        // Enable font display swap
        $wp_customize->add_setting('recruitpro_font_display_swap', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_font_display_swap', array(
            'label' => __('Enable Font Display Swap', 'recruitpro'),
            'description' => __('Improves performance by showing fallback fonts while Google Fonts load.', 'recruitpro'),
            'section' => 'recruitpro_font_optimization',
            'type' => 'checkbox',
        ));

        // Enable font preloading
        $wp_customize->add_setting('recruitpro_font_preload', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_font_preload', array(
            'label' => __('Enable Font Preloading', 'recruitpro'),
            'description' => __('Preload critical fonts for faster rendering.', 'recruitpro'),
            'section' => 'recruitpro_font_optimization',
            'type' => 'checkbox',
        ));

        // Local font hosting (GDPR-friendly)
        $wp_customize->add_setting('recruitpro_local_font_hosting', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_local_font_hosting', array(
            'label' => __('Enable Local Font Hosting', 'recruitpro'),
            'description' => __('Host Google Fonts locally for better privacy compliance (GDPR).', 'recruitpro'),
            'section' => 'recruitpro_font_optimization',
            'type' => 'checkbox',
        ));

        // Fallback fonts
        $wp_customize->add_setting('recruitpro_enable_fallback_fonts', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_fallback_fonts', array(
            'label' => __('Enable System Font Fallbacks', 'recruitpro'),
            'description' => __('Use system fonts as fallbacks for better performance and privacy.', 'recruitpro'),
            'section' => 'recruitpro_font_optimization',
            'type' => 'checkbox',
        ));
    }

    /**
     * Output preconnect links for Google Fonts
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_preconnect_links() {
        
        // Don't load if local hosting is enabled
        if (get_theme_mod('recruitpro_local_font_hosting', false)) {
            return;
        }

        // Don't load if using only system fonts
        $fonts_to_load = $this->get_fonts_to_load();
        if (empty($fonts_to_load)) {
            return;
        }

        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }

    /**
     * Enqueue Google Fonts
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_google_fonts() {
        
        // Don't load if local hosting is enabled
        if (get_theme_mod('recruitpro_local_font_hosting', false)) {
            $this->enqueue_local_fonts();
            return;
        }

        $fonts_to_load = $this->get_fonts_to_load();
        
        if (empty($fonts_to_load)) {
            return;
        }

        $font_url = $this->build_google_fonts_url($fonts_to_load);
        
        if ($font_url) {
            wp_enqueue_style(
                'recruitpro-google-fonts',
                $font_url,
                array(),
                wp_get_theme()->get('Version')
            );
        }
    }

    /**
     * Get fonts that need to be loaded
     * 
     * @since 1.0.0
     * @return array Fonts to load
     */
    private function get_fonts_to_load() {
        
        $fonts_to_load = array();
        
        // Get current font settings
        if (get_theme_mod('recruitpro_enable_custom_fonts', false)) {
            // Custom font selection
            $primary_font = get_theme_mod('recruitpro_primary_font', 'Inter');
            $heading_font = get_theme_mod('recruitpro_heading_font', 'Poppins');
            $secondary_font = get_theme_mod('recruitpro_secondary_font', 'Inter');
        } else {
            // Font combination
            $combination_key = get_theme_mod('recruitpro_font_combination', 'professional_modern');
            $combination = $this->font_combinations[$combination_key] ?? $this->font_combinations['professional_modern'];
            
            $primary_font = $combination['primary'];
            $heading_font = $combination['heading'];
            $secondary_font = $combination['secondary'];
        }

        $all_fonts = array($primary_font, $heading_font, $secondary_font);
        $unique_fonts = array_unique($all_fonts);

        // Filter out system fonts
        foreach ($unique_fonts as $font) {
            if ($this->is_google_font($font)) {
                $fonts_to_load[] = $font;
            }
        }

        return $fonts_to_load;
    }

    /**
     * Check if font is a Google Font
     * 
     * @since 1.0.0
     * @param string $font Font name
     * @return bool Whether font is a Google Font
     */
    private function is_google_font($font) {
        
        $system_fonts = array('System UI', 'Arial', 'Helvetica', 'Times New Roman', 'Georgia');
        return !in_array($font, $system_fonts);
    }

    /**
     * Build Google Fonts URL
     * 
     * @since 1.0.0
     * @param array $fonts Fonts to load
     * @return string|false Google Fonts URL or false
     */
    private function build_google_fonts_url($fonts) {
        
        if (empty($fonts)) {
            return false;
        }

        $font_families = array();
        
        foreach ($fonts as $font) {
            if (isset($this->available_fonts[$font])) {
                $weights = $this->available_fonts[$font]['weights'];
                $font_families[] = str_replace(' ', '+', $font) . ':' . implode(',', $weights);
            }
        }

        if (empty($font_families)) {
            return false;
        }

        $query_args = array(
            'family' => implode('|', $font_families),
        );

        // Add font display swap for performance
        if (get_theme_mod('recruitpro_font_display_swap', true)) {
            $query_args['display'] = 'swap';
        }

        return add_query_arg($query_args, 'https://fonts.googleapis.com/css');
    }

    /**
     * Enqueue local fonts (GDPR-friendly option)
     * 
     * @since 1.0.0
     * @return void
     */
    private function enqueue_local_fonts() {
        
        // This would load locally hosted font files
        // Implementation would depend on a local fonts solution
        
        wp_enqueue_style(
            'recruitpro-local-fonts',
            get_template_directory_uri() . '/assets/fonts/local-fonts.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }

    /**
     * Output CSS variables for fonts
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_font_css_variables() {
        
        $primary_font = $this->get_current_primary_font();
        $heading_font = $this->get_current_heading_font();
        $secondary_font = $this->get_current_secondary_font();
        
        $body_weight = get_theme_mod('recruitpro_body_font_weight', '400');
        $heading_weight = get_theme_mod('recruitpro_heading_font_weight', '600');
        
        // Build font stacks with fallbacks
        $primary_stack = $this->build_font_stack($primary_font);
        $heading_stack = $this->build_font_stack($heading_font);
        $secondary_stack = $this->build_font_stack($secondary_font);

        ?>
        <style id="recruitpro-font-variables">
        :root {
            --recruitpro-font-primary: <?php echo $primary_stack; ?>;
            --recruitpro-font-heading: <?php echo $heading_stack; ?>;
            --recruitpro-font-secondary: <?php echo $secondary_stack; ?>;
            --recruitpro-font-weight-body: <?php echo $body_weight; ?>;
            --recruitpro-font-weight-heading: <?php echo $heading_weight; ?>;
        }
        
        body {
            font-family: var(--recruitpro-font-primary);
            font-weight: var(--recruitpro-font-weight-body);
        }
        
        h1, h2, h3, h4, h5, h6,
        .heading-font {
            font-family: var(--recruitpro-font-heading);
            font-weight: var(--recruitpro-font-weight-heading);
        }
        
        .nav-menu,
        .btn,
        .button,
        .form-control,
        .secondary-font {
            font-family: var(--recruitpro-font-secondary);
        }
        </style>
        <?php
    }

    /**
     * Build font stack with fallbacks
     * 
     * @since 1.0.0
     * @param string $font Primary font
     * @return string Complete font stack
     */
    private function build_font_stack($font) {
        
        $fallbacks = $this->get_font_fallbacks($font);
        $font_stack = "'{$font}'";
        
        if (get_theme_mod('recruitpro_enable_fallback_fonts', true)) {
            $font_stack .= ', ' . implode(', ', $fallbacks);
        }
        
        return $font_stack;
    }

    /**
     * Get appropriate fallbacks for a font
     * 
     * @since 1.0.0
     * @param string $font Font name
     * @return array Fallback fonts
     */
    private function get_font_fallbacks($font) {
        
        if (!isset($this->available_fonts[$font])) {
            return array('system-ui', 'sans-serif');
        }

        $category = $this->available_fonts[$font]['category'];
        
        switch ($category) {
            case 'serif':
                return array('Georgia', 'Times New Roman', 'serif');
                
            case 'system':
                return array('system-ui', '-apple-system', 'BlinkMacSystemFont', 'sans-serif');
                
            case 'sans-serif':
            default:
                return array('system-ui', '-apple-system', 'BlinkMacSystemFont', 'Helvetica Neue', 'Arial', 'sans-serif');
        }
    }

    /**
     * Get current primary font
     * 
     * @since 1.0.0
     * @return string Primary font name
     */
    private function get_current_primary_font() {
        
        if (get_theme_mod('recruitpro_enable_custom_fonts', false)) {
            return get_theme_mod('recruitpro_primary_font', 'Inter');
        }
        
        $combination_key = get_theme_mod('recruitpro_font_combination', 'professional_modern');
        $combination = $this->font_combinations[$combination_key] ?? $this->font_combinations['professional_modern'];
        
        return $combination['primary'];
    }

    /**
     * Get current heading font
     * 
     * @since 1.0.0
     * @return string Heading font name
     */
    private function get_current_heading_font() {
        
        if (get_theme_mod('recruitpro_enable_custom_fonts', false)) {
            return get_theme_mod('recruitpro_heading_font', 'Poppins');
        }
        
        $combination_key = get_theme_mod('recruitpro_font_combination', 'professional_modern');
        $combination = $this->font_combinations[$combination_key] ?? $this->font_combinations['professional_modern'];
        
        return $combination['heading'];
    }

    /**
     * Get current secondary font
     * 
     * @since 1.0.0
     * @return string Secondary font name
     */
    private function get_current_secondary_font() {
        
        if (get_theme_mod('recruitpro_enable_custom_fonts', false)) {
            return get_theme_mod('recruitpro_secondary_font', 'Inter');
        }
        
        $combination_key = get_theme_mod('recruitpro_font_combination', 'professional_modern');
        $combination = $this->font_combinations[$combination_key] ?? $this->font_combinations['professional_modern'];
        
        return $combination['secondary'];
    }

    /**
     * Get font combination choices for customizer
     * 
     * @since 1.0.0
     * @return array Font combination choices
     */
    private function get_font_combination_choices() {
        
        $choices = array();
        
        foreach ($this->font_combinations as $key => $combination) {
            $choices[$key] = $combination['name'] . ' - ' . $combination['description'];
        }
        
        return $choices;
    }

    /**
     * Get font choices for customizer
     * 
     * @since 1.0.0
     * @return array Font choices
     */
    public function get_font_choices() {
        
        $choices = array();
        
        // Group fonts by category
        $categories = array(
            'sans-serif' => __('Sans-serif Fonts', 'recruitpro'),
            'serif' => __('Serif Fonts', 'recruitpro'),
            'system' => __('System Fonts', 'recruitpro'),
        );

        foreach ($categories as $cat_key => $cat_name) {
            $choices[$cat_name] = array();
            
            foreach ($this->available_fonts as $font_name => $font_data) {
                if ($font_data['category'] === $cat_key) {
                    $choices[$cat_name][$font_name] = $font_name . ' - ' . $font_data['description'];
                }
            }
        }
        
        return $choices;
    }

    /**
     * Enqueue fonts in admin
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_admin_fonts() {
        
        // Load fonts in customizer preview
        if (is_customize_preview()) {
            $this->enqueue_google_fonts();
        }
    }

    /**
     * Sanitize font combination
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_font_combination($input) {
        
        $valid_combinations = array_keys($this->font_combinations);
        return in_array($input, $valid_combinations) ? $input : 'professional_modern';
    }

    /**
     * Sanitize font choice
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_font_choice($input) {
        
        $valid_fonts = array_keys($this->available_fonts);
        return in_array($input, $valid_fonts) ? $input : 'Inter';
    }

    /**
     * Sanitize font weight
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_font_weight($input) {
        
        $valid_weights = array('300', '400', '500', '600', '700');
        return in_array($input, $valid_weights) ? $input : '400';
    }
}

// Initialize Google Fonts management
new RecruitPro_Google_Fonts();

/**
 * Helper function to get current font settings
 * 
 * @since 1.0.0
 * @param string $type Font type (primary, heading, secondary)
 * @return string Font name
 */
function recruitpro_get_font($type = 'primary') {
    
    static $fonts_instance = null;
    
    if (is_null($fonts_instance)) {
        $fonts_instance = new RecruitPro_Google_Fonts();
    }
    
    switch ($type) {
        case 'heading':
            return $fonts_instance->get_current_heading_font();
        case 'secondary':
            return $fonts_instance->get_current_secondary_font();
        case 'primary':
        default:
            return $fonts_instance->get_current_primary_font();
    }
}

?>