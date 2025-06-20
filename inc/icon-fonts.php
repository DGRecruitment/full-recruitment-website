<?php
/**
 * RecruitPro Theme Icon Fonts Management
 *
 * This file handles icon font integration for the RecruitPro recruitment
 * website theme. It includes Font Awesome, custom recruitment icons,
 * performance optimization, accessibility features, and professional
 * icon management specifically designed for recruitment agencies.
 *
 * @package RecruitPro
 * @subpackage Theme/Icons
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/icon-fonts.php
 * Purpose: Icon font management and optimization
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Icon Fonts Management Class
 * 
 * Handles all icon font functionality including loading,
 * optimization, and accessibility features.
 *
 * @since 1.0.0
 */
class RecruitPro_Icon_Fonts {

    /**
     * Available icon sets
     * 
     * @since 1.0.0
     * @var array
     */
    private $icon_sets = array();

    /**
     * Recruitment-specific icons mapping
     * 
     * @since 1.0.0
     * @var array
     */
    private $recruitment_icons = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_icon_sets();
        $this->init_recruitment_icons();
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_icon_fonts'));
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_action('wp_head', array($this, 'output_icon_preload_links'), 1);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_icons'));
        add_shortcode('recruitpro_icon', array($this, 'icon_shortcode'));
    }

    /**
     * Initialize available icon sets
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_icon_sets() {
        
        $this->icon_sets = array(
            'font_awesome' => array(
                'name' => __('Font Awesome', 'recruitpro'),
                'description' => __('Comprehensive icon library with professional icons', 'recruitpro'),
                'version' => '6.4.0',
                'cdn_url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
                'local_file' => 'assets/fonts/font-awesome/css/all.min.css',
                'prefix' => 'fa',
                'styles' => array('solid', 'regular', 'brands'),
                'size' => '180kb',
                'icons_count' => '2000+',
            ),
            'recruitpro_custom' => array(
                'name' => __('RecruitPro Custom Icons', 'recruitpro'),
                'description' => __('Custom recruitment industry specific icons', 'recruitpro'),
                'version' => '1.0.0',
                'local_file' => 'assets/fonts/recruitpro-icons/recruitpro-icons.css',
                'prefix' => 'rp',
                'styles' => array('solid'),
                'size' => '25kb',
                'icons_count' => '50+',
            ),
            'feather_icons' => array(
                'name' => __('Feather Icons', 'recruitpro'),
                'description' => __('Simple, clean line icons perfect for modern interfaces', 'recruitpro'),
                'version' => '4.29.0',
                'cdn_url' => 'https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css',
                'local_file' => 'assets/fonts/feather-icons/feather.css',
                'prefix' => 'feather',
                'styles' => array('line'),
                'size' => '45kb',
                'icons_count' => '280+',
            ),
            'lucide_icons' => array(
                'name' => __('Lucide Icons', 'recruitpro'),
                'description' => __('Beautiful, clean icons for modern web applications', 'recruitpro'),
                'version' => '0.263.1',
                'cdn_url' => 'https://cdn.jsdelivr.net/npm/lucide@0.263.1/dist/umd/lucide.min.css',
                'local_file' => 'assets/fonts/lucide-icons/lucide.css',
                'prefix' => 'lucide',
                'styles' => array('line'),
                'size' => '40kb',
                'icons_count' => '1200+',
            ),
            'system_icons' => array(
                'name' => __('System Icons', 'recruitpro'),
                'description' => __('Native system icons for fastest performance', 'recruitpro'),
                'version' => '1.0.0',
                'local_file' => 'assets/fonts/system-icons/system-icons.css',
                'prefix' => 'sys',
                'styles' => array('system'),
                'size' => '5kb',
                'icons_count' => '20+',
            ),
        );
    }

    /**
     * Initialize recruitment-specific icons mapping
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_recruitment_icons() {
        
        $this->recruitment_icons = array(
            // Core recruitment functions
            'candidate' => array(
                'fa' => 'fa-solid fa-user-tie',
                'rp' => 'rp-candidate',
                'feather' => 'feather-user',
                'system' => 'sys-person',
                'label' => __('Candidate', 'recruitpro'),
                'category' => 'core',
            ),
            'job_posting' => array(
                'fa' => 'fa-solid fa-briefcase',
                'rp' => 'rp-job-posting',
                'feather' => 'feather-briefcase',
                'system' => 'sys-briefcase',
                'label' => __('Job Posting', 'recruitpro'),
                'category' => 'core',
            ),
            'client' => array(
                'fa' => 'fa-solid fa-building',
                'rp' => 'rp-client',
                'feather' => 'feather-building',
                'system' => 'sys-building',
                'label' => __('Client Company', 'recruitpro'),
                'category' => 'core',
            ),
            'interview' => array(
                'fa' => 'fa-solid fa-comments',
                'rp' => 'rp-interview',
                'feather' => 'feather-message-circle',
                'system' => 'sys-chat',
                'label' => __('Interview', 'recruitpro'),
                'category' => 'core',
            ),
            'cv_resume' => array(
                'fa' => 'fa-solid fa-file-user',
                'rp' => 'rp-cv',
                'feather' => 'feather-file-text',
                'system' => 'sys-document',
                'label' => __('CV/Resume', 'recruitpro'),
                'category' => 'core',
            ),
            'search' => array(
                'fa' => 'fa-solid fa-magnifying-glass',
                'rp' => 'rp-search',
                'feather' => 'feather-search',
                'system' => 'sys-search',
                'label' => __('Search', 'recruitpro'),
                'category' => 'core',
            ),
            'placement' => array(
                'fa' => 'fa-solid fa-handshake',
                'rp' => 'rp-placement',
                'feather' => 'feather-check-circle',
                'system' => 'sys-checkmark',
                'label' => __('Placement', 'recruitpro'),
                'category' => 'core',
            ),
            
            // Industry sectors
            'healthcare' => array(
                'fa' => 'fa-solid fa-user-doctor',
                'rp' => 'rp-healthcare',
                'feather' => 'feather-heart',
                'system' => 'sys-medical',
                'label' => __('Healthcare', 'recruitpro'),
                'category' => 'industry',
            ),
            'technology' => array(
                'fa' => 'fa-solid fa-code',
                'rp' => 'rp-technology',
                'feather' => 'feather-monitor',
                'system' => 'sys-computer',
                'label' => __('Technology', 'recruitpro'),
                'category' => 'industry',
            ),
            'finance' => array(
                'fa' => 'fa-solid fa-chart-line',
                'rp' => 'rp-finance',
                'feather' => 'feather-trending-up',
                'system' => 'sys-chart',
                'label' => __('Finance', 'recruitpro'),
                'category' => 'industry',
            ),
            'education' => array(
                'fa' => 'fa-solid fa-graduation-cap',
                'rp' => 'rp-education',
                'feather' => 'feather-book',
                'system' => 'sys-book',
                'label' => __('Education', 'recruitpro'),
                'category' => 'industry',
            ),
            'engineering' => array(
                'fa' => 'fa-solid fa-gear',
                'rp' => 'rp-engineering',
                'feather' => 'feather-settings',
                'system' => 'sys-gear',
                'label' => __('Engineering', 'recruitpro'),
                'category' => 'industry',
            ),
            'sales' => array(
                'fa' => 'fa-solid fa-chart-column',
                'rp' => 'rp-sales',
                'feather' => 'feather-bar-chart',
                'system' => 'sys-graph',
                'label' => __('Sales', 'recruitpro'),
                'category' => 'industry',
            ),
            'marketing' => array(
                'fa' => 'fa-solid fa-bullhorn',
                'rp' => 'rp-marketing',
                'feather' => 'feather-speaker',
                'system' => 'sys-megaphone',
                'label' => __('Marketing', 'recruitpro'),
                'category' => 'industry',
            ),
            'legal' => array(
                'fa' => 'fa-solid fa-scale-balanced',
                'rp' => 'rp-legal',
                'feather' => 'feather-shield',
                'system' => 'sys-shield',
                'label' => __('Legal', 'recruitpro'),
                'category' => 'industry',
            ),
            
            // Communication & Contact
            'phone' => array(
                'fa' => 'fa-solid fa-phone',
                'rp' => 'rp-phone',
                'feather' => 'feather-phone',
                'system' => 'sys-phone',
                'label' => __('Phone', 'recruitpro'),
                'category' => 'contact',
            ),
            'email' => array(
                'fa' => 'fa-solid fa-envelope',
                'rp' => 'rp-email',
                'feather' => 'feather-mail',
                'system' => 'sys-mail',
                'label' => __('Email', 'recruitpro'),
                'category' => 'contact',
            ),
            'location' => array(
                'fa' => 'fa-solid fa-location-dot',
                'rp' => 'rp-location',
                'feather' => 'feather-map-pin',
                'system' => 'sys-location',
                'label' => __('Location', 'recruitpro'),
                'category' => 'contact',
            ),
            'calendar' => array(
                'fa' => 'fa-solid fa-calendar',
                'rp' => 'rp-calendar',
                'feather' => 'feather-calendar',
                'system' => 'sys-calendar',
                'label' => __('Calendar', 'recruitpro'),
                'category' => 'contact',
            ),
            'clock' => array(
                'fa' => 'fa-solid fa-clock',
                'rp' => 'rp-clock',
                'feather' => 'feather-clock',
                'system' => 'sys-clock',
                'label' => __('Time', 'recruitpro'),
                'category' => 'contact',
            ),
            
            // Social Media & Professional Networks
            'linkedin' => array(
                'fa' => 'fa-brands fa-linkedin',
                'rp' => 'rp-linkedin',
                'feather' => 'feather-linkedin',
                'system' => 'sys-linkedin',
                'label' => __('LinkedIn', 'recruitpro'),
                'category' => 'social',
            ),
            'facebook' => array(
                'fa' => 'fa-brands fa-facebook',
                'rp' => 'rp-facebook',
                'feather' => 'feather-facebook',
                'system' => 'sys-facebook',
                'label' => __('Facebook', 'recruitpro'),
                'category' => 'social',
            ),
            'twitter' => array(
                'fa' => 'fa-brands fa-x-twitter',
                'rp' => 'rp-twitter',
                'feather' => 'feather-twitter',
                'system' => 'sys-twitter',
                'label' => __('Twitter/X', 'recruitpro'),
                'category' => 'social',
            ),
            'instagram' => array(
                'fa' => 'fa-brands fa-instagram',
                'rp' => 'rp-instagram',
                'feather' => 'feather-instagram',
                'system' => 'sys-instagram',
                'label' => __('Instagram', 'recruitpro'),
                'category' => 'social',
            ),
            'glassdoor' => array(
                'fa' => 'fa-solid fa-door-open',
                'rp' => 'rp-glassdoor',
                'feather' => 'feather-external-link',
                'system' => 'sys-external',
                'label' => __('Glassdoor', 'recruitpro'),
                'category' => 'social',
            ),
            'indeed' => array(
                'fa' => 'fa-solid fa-i',
                'rp' => 'rp-indeed',
                'feather' => 'feather-globe',
                'system' => 'sys-web',
                'label' => __('Indeed', 'recruitpro'),
                'category' => 'social',
            ),
            
            // UI Elements
            'arrow_right' => array(
                'fa' => 'fa-solid fa-arrow-right',
                'rp' => 'rp-arrow-right',
                'feather' => 'feather-arrow-right',
                'system' => 'sys-arrow-right',
                'label' => __('Arrow Right', 'recruitpro'),
                'category' => 'ui',
            ),
            'arrow_left' => array(
                'fa' => 'fa-solid fa-arrow-left',
                'rp' => 'rp-arrow-left',
                'feather' => 'feather-arrow-left',
                'system' => 'sys-arrow-left',
                'label' => __('Arrow Left', 'recruitpro'),
                'category' => 'ui',
            ),
            'close' => array(
                'fa' => 'fa-solid fa-xmark',
                'rp' => 'rp-close',
                'feather' => 'feather-x',
                'system' => 'sys-close',
                'label' => __('Close', 'recruitpro'),
                'category' => 'ui',
            ),
            'menu' => array(
                'fa' => 'fa-solid fa-bars',
                'rp' => 'rp-menu',
                'feather' => 'feather-menu',
                'system' => 'sys-menu',
                'label' => __('Menu', 'recruitpro'),
                'category' => 'ui',
            ),
            'download' => array(
                'fa' => 'fa-solid fa-download',
                'rp' => 'rp-download',
                'feather' => 'feather-download',
                'system' => 'sys-download',
                'label' => __('Download', 'recruitpro'),
                'category' => 'ui',
            ),
            'upload' => array(
                'fa' => 'fa-solid fa-upload',
                'rp' => 'rp-upload',
                'feather' => 'feather-upload',
                'system' => 'sys-upload',
                'label' => __('Upload', 'recruitpro'),
                'category' => 'ui',
            ),
            'check' => array(
                'fa' => 'fa-solid fa-check',
                'rp' => 'rp-check',
                'feather' => 'feather-check',
                'system' => 'sys-check',
                'label' => __('Check', 'recruitpro'),
                'category' => 'ui',
            ),
            'star' => array(
                'fa' => 'fa-solid fa-star',
                'rp' => 'rp-star',
                'feather' => 'feather-star',
                'system' => 'sys-star',
                'label' => __('Star', 'recruitpro'),
                'category' => 'ui',
            ),
        );
    }

    /**
     * Add icon fonts customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {

        // =================================================================
        // ICON FONTS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_icon_fonts', array(
            'title' => __('Icon Fonts', 'recruitpro'),
            'description' => __('Configure icon fonts for your recruitment website. Choose between different icon sets for optimal performance and style.', 'recruitpro'),
            'priority' => 125,
            'capability' => 'edit_theme_options',
        ));

        // Primary Icon Set
        $wp_customize->add_setting('recruitpro_primary_icon_set', array(
            'default' => 'font_awesome',
            'sanitize_callback' => array($this, 'sanitize_icon_set'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_primary_icon_set', array(
            'label' => __('Primary Icon Set', 'recruitpro'),
            'description' => __('Choose the main icon set for your website.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'select',
            'choices' => $this->get_icon_set_choices(),
        ));

        // Icon Loading Method
        $wp_customize->add_setting('recruitpro_icon_loading_method', array(
            'default' => 'local',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_icon_loading_method', array(
            'label' => __('Icon Loading Method', 'recruitpro'),
            'description' => __('Choose how to load icon fonts for best performance.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'select',
            'choices' => array(
                'local' => __('Local Files (Best Performance & Privacy)', 'recruitpro'),
                'cdn' => __('CDN (Always Latest Version)', 'recruitpro'),
                'inline' => __('Inline SVG (Best Performance)', 'recruitpro'),
            ),
        ));

        // Enable Icon Preloading
        $wp_customize->add_setting('recruitpro_icon_preload', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_icon_preload', array(
            'label' => __('Enable Icon Font Preloading', 'recruitpro'),
            'description' => __('Preload icon fonts for faster rendering.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'checkbox',
        ));

        // Icon Display Size
        $wp_customize->add_setting('recruitpro_icon_base_size', array(
            'default' => 16,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_icon_base_size', array(
            'label' => __('Base Icon Size (pixels)', 'recruitpro'),
            'description' => __('Default size for icons throughout the site.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 12,
                'max' => 32,
                'step' => 1,
            ),
        ));

        // Enable Custom Icons
        $wp_customize->add_setting('recruitpro_enable_custom_icons', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_custom_icons', array(
            'label' => __('Enable RecruitPro Custom Icons', 'recruitpro'),
            'description' => __('Load recruitment-specific custom icons.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'checkbox',
        ));

        // Icon Accessibility
        $wp_customize->add_setting('recruitpro_icon_accessibility', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_icon_accessibility', array(
            'label' => __('Enhanced Icon Accessibility', 'recruitpro'),
            'description' => __('Add proper ARIA labels and screen reader support for icons.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'checkbox',
        ));

        // Fallback Options
        $wp_customize->add_setting('recruitpro_icon_fallback', array(
            'default' => 'unicode',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_icon_fallback', array(
            'label' => __('Icon Fallback Method', 'recruitpro'),
            'description' => __('What to show if icon fonts fail to load.', 'recruitpro'),
            'section' => 'recruitpro_icon_fonts',
            'type' => 'select',
            'choices' => array(
                'unicode' => __('Unicode Symbols', 'recruitpro'),
                'text' => __('Text Labels', 'recruitpro'),
                'hide' => __('Hide Icons', 'recruitpro'),
            ),
        ));
    }

    /**
     * Enqueue icon fonts
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_icon_fonts() {
        
        $primary_icon_set = get_theme_mod('recruitpro_primary_icon_set', 'font_awesome');
        $loading_method = get_theme_mod('recruitpro_icon_loading_method', 'local');
        $enable_custom = get_theme_mod('recruitpro_enable_custom_icons', true);
        
        // Load primary icon set
        $this->load_icon_set($primary_icon_set, $loading_method);
        
        // Load custom RecruitPro icons if enabled
        if ($enable_custom && $primary_icon_set !== 'recruitpro_custom') {
            $this->load_icon_set('recruitpro_custom', 'local');
        }
        
        // Add icon CSS variables and customizations
        add_action('wp_head', array($this, 'output_icon_css'), 20);
    }

    /**
     * Load specific icon set
     * 
     * @since 1.0.0
     * @param string $icon_set Icon set to load
     * @param string $method Loading method
     * @return void
     */
    private function load_icon_set($icon_set, $method = 'local') {
        
        if (!isset($this->icon_sets[$icon_set])) {
            return;
        }
        
        $set = $this->icon_sets[$icon_set];
        $theme_version = wp_get_theme()->get('Version');
        
        switch ($method) {
            case 'cdn':
                if (isset($set['cdn_url'])) {
                    wp_enqueue_style(
                        "recruitpro-icons-{$icon_set}",
                        $set['cdn_url'],
                        array(),
                        $set['version']
                    );
                }
                break;
                
            case 'local':
                $local_path = get_template_directory_uri() . '/' . $set['local_file'];
                if (file_exists(get_template_directory() . '/' . $set['local_file'])) {
                    wp_enqueue_style(
                        "recruitpro-icons-{$icon_set}",
                        $local_path,
                        array(),
                        $theme_version
                    );
                }
                break;
                
            case 'inline':
                // For inline SVG, we'll load a JavaScript handler
                wp_enqueue_script(
                    "recruitpro-svg-icons-{$icon_set}",
                    get_template_directory_uri() . '/assets/js/svg-icons.js',
                    array('jquery'),
                    $theme_version,
                    true
                );
                break;
        }
    }

    /**
     * Output preload links for icon fonts
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_icon_preload_links() {
        
        if (!get_theme_mod('recruitpro_icon_preload', true)) {
            return;
        }
        
        $primary_icon_set = get_theme_mod('recruitpro_primary_icon_set', 'font_awesome');
        $loading_method = get_theme_mod('recruitpro_icon_loading_method', 'local');
        
        if ($loading_method === 'local' && isset($this->icon_sets[$primary_icon_set])) {
            $set = $this->icon_sets[$primary_icon_set];
            $font_path = get_template_directory_uri() . '/' . $set['local_file'];
            
            echo '<link rel="preload" href="' . esc_url($font_path) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
        }
    }

    /**
     * Output icon CSS customizations
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_icon_css() {
        
        $base_size = get_theme_mod('recruitpro_icon_base_size', 16);
        $primary_color = get_theme_mod('recruitpro_primary_color', '#1e40af');
        $fallback_method = get_theme_mod('recruitpro_icon_fallback', 'unicode');
        
        ?>
        <style id="recruitpro-icon-customizations">
        :root {
            --recruitpro-icon-size: <?php echo intval($base_size); ?>px;
            --recruitpro-icon-color: <?php echo esc_attr($primary_color); ?>;
            --recruitpro-icon-size-sm: <?php echo intval($base_size * 0.875); ?>px;
            --recruitpro-icon-size-lg: <?php echo intval($base_size * 1.25); ?>px;
            --recruitpro-icon-size-xl: <?php echo intval($base_size * 1.5); ?>px;
        }
        
        .recruitpro-icon,
        .rp-icon {
            font-size: var(--recruitpro-icon-size);
            color: var(--recruitpro-icon-color);
            display: inline-block;
            line-height: 1;
            vertical-align: middle;
        }
        
        .recruitpro-icon.icon-sm { font-size: var(--recruitpro-icon-size-sm); }
        .recruitpro-icon.icon-lg { font-size: var(--recruitpro-icon-size-lg); }
        .recruitpro-icon.icon-xl { font-size: var(--recruitpro-icon-size-xl); }
        
        /* Accessibility enhancements */
        .recruitpro-icon[aria-hidden="true"] {
            speak: none;
        }
        
        .recruitpro-icon:not([aria-label]):not([aria-labelledby]):not([title]) {
            speak: none;
        }
        
        /* Icon categories */
        .icon-category-core { color: var(--recruitpro-primary); }
        .icon-category-industry { color: var(--recruitpro-secondary); }
        .icon-category-contact { color: var(--recruitpro-accent); }
        .icon-category-social { color: var(--recruitpro-text-light); }
        .icon-category-ui { color: var(--recruitpro-text-dark); }
        
        <?php if ($fallback_method === 'unicode'): ?>
        /* Unicode fallbacks */
        .fa-user-tie::before { content: "👔"; }
        .fa-briefcase::before { content: "💼"; }
        .fa-building::before { content: "🏢"; }
        .fa-comments::before { content: "💬"; }
        .fa-file-user::before { content: "📄"; }
        .fa-magnifying-glass::before { content: "🔍"; }
        .fa-handshake::before { content: "🤝"; }
        .fa-phone::before { content: "📞"; }
        .fa-envelope::before { content: "✉️"; }
        .fa-location-dot::before { content: "📍"; }
        <?php elseif ($fallback_method === 'text'): ?>
        /* Text fallbacks */
        .fa-user-tie::before { content: "Candidate"; }
        .fa-briefcase::before { content: "Job"; }
        .fa-building::before { content: "Company"; }
        .fa-comments::before { content: "Chat"; }
        .fa-file-user::before { content: "CV"; }
        .fa-magnifying-glass::before { content: "Search"; }
        .fa-handshake::before { content: "Success"; }
        .fa-phone::before { content: "Phone"; }
        .fa-envelope::before { content: "Email"; }
        .fa-location-dot::before { content: "Location"; }
        <?php endif; ?>
        </style>
        <?php
    }

    /**
     * Icon shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Icon HTML
     */
    public function icon_shortcode($atts) {
        
        $atts = shortcode_atts(array(
            'name' => '',
            'set' => '', // auto-detect if empty
            'size' => '',
            'color' => '',
            'class' => '',
            'label' => '',
            'decorative' => 'false',
        ), $atts, 'recruitpro_icon');

        if (empty($atts['name'])) {
            return '';
        }

        return $this->get_icon_html($atts['name'], $atts);
    }

    /**
     * Get icon HTML
     * 
     * @since 1.0.0
     * @param string $icon_name Icon name
     * @param array $args Icon arguments
     * @return string Icon HTML
     */
    public function get_icon_html($icon_name, $args = array()) {
        
        $defaults = array(
            'set' => get_theme_mod('recruitpro_primary_icon_set', 'font_awesome'),
            'size' => '',
            'color' => '',
            'class' => '',
            'label' => '',
            'decorative' => false,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Get icon mapping
        if (!isset($this->recruitment_icons[$icon_name])) {
            return '';
        }
        
        $icon_data = $this->recruitment_icons[$icon_name];
        $icon_set = $args['set'];
        
        // Get icon class for the selected set
        $icon_class = isset($icon_data[$icon_set]) ? $icon_data[$icon_set] : $icon_data['fa'];
        
        // Build CSS classes
        $classes = array('recruitpro-icon', 'rp-icon');
        
        if (!empty($args['class'])) {
            $classes[] = $args['class'];
        }
        
        if (!empty($args['size'])) {
            $classes[] = 'icon-' . $args['size'];
        }
        
        if (isset($icon_data['category'])) {
            $classes[] = 'icon-category-' . $icon_data['category'];
        }
        
        $classes[] = $icon_class;
        
        // Build attributes
        $attributes = array(
            'class' => implode(' ', $classes),
        );
        
        // Accessibility attributes
        if (get_theme_mod('recruitpro_icon_accessibility', true)) {
            if ($args['decorative'] || empty($args['label'])) {
                $attributes['aria-hidden'] = 'true';
            } else {
                $attributes['aria-label'] = $args['label'] ?: $icon_data['label'];
                $attributes['role'] = 'img';
            }
        }
        
        // Custom styles
        $styles = array();
        if (!empty($args['color'])) {
            $styles[] = 'color: ' . esc_attr($args['color']);
        }
        if (!empty($styles)) {
            $attributes['style'] = implode('; ', $styles);
        }
        
        // Build attribute string
        $attr_string = '';
        foreach ($attributes as $key => $value) {
            $attr_string .= ' ' . $key . '="' . esc_attr($value) . '"';
        }
        
        return '<i' . $attr_string . '></i>';
    }

    /**
     * Enqueue admin icons
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_admin_icons() {
        
        // Load icons in customizer
        if (is_customize_preview()) {
            $this->enqueue_icon_fonts();
        }
        
        // Load minimal icon set for admin
        wp_enqueue_style(
            'recruitpro-admin-icons',
            get_template_directory_uri() . '/assets/css/admin-icons.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }

    /**
     * Get icon set choices for customizer
     * 
     * @since 1.0.0
     * @return array Icon set choices
     */
    private function get_icon_set_choices() {
        
        $choices = array();
        
        foreach ($this->icon_sets as $key => $set) {
            $choices[$key] = $set['name'] . ' (' . $set['size'] . ', ' . $set['icons_count'] . ')';
        }
        
        return $choices;
    }

    /**
     * Sanitize icon set selection
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_icon_set($input) {
        
        $valid_sets = array_keys($this->icon_sets);
        return in_array($input, $valid_sets) ? $input : 'font_awesome';
    }

    /**
     * Get available recruitment icons
     * 
     * @since 1.0.0
     * @return array Available icons
     */
    public function get_recruitment_icons() {
        return $this->recruitment_icons;
    }

    /**
     * Get icons by category
     * 
     * @since 1.0.0
     * @param string $category Icon category
     * @return array Icons in category
     */
    public function get_icons_by_category($category) {
        
        $filtered_icons = array();
        
        foreach ($this->recruitment_icons as $key => $icon) {
            if (isset($icon['category']) && $icon['category'] === $category) {
                $filtered_icons[$key] = $icon;
            }
        }
        
        return $filtered_icons;
    }
}

// Initialize icon fonts management
new RecruitPro_Icon_Fonts();

/**
 * Helper function to display an icon
 * 
 * @since 1.0.0
 * @param string $icon_name Icon name
 * @param array $args Icon arguments
 * @return void
 */
function recruitpro_icon($icon_name, $args = array()) {
    
    static $icon_manager = null;
    
    if (is_null($icon_manager)) {
        $icon_manager = new RecruitPro_Icon_Fonts();
    }
    
    echo $icon_manager->get_icon_html($icon_name, $args);
}

/**
 * Helper function to get icon HTML
 * 
 * @since 1.0.0
 * @param string $icon_name Icon name
 * @param array $args Icon arguments
 * @return string Icon HTML
 */
function recruitpro_get_icon($icon_name, $args = array()) {
    
    static $icon_manager = null;
    
    if (is_null($icon_manager)) {
        $icon_manager = new RecruitPro_Icon_Fonts();
    }
    
    return $icon_manager->get_icon_html($icon_name, $args);
}

/**
 * Helper function to check if icon exists
 * 
 * @since 1.0.0
 * @param string $icon_name Icon name
 * @return bool Whether icon exists
 */
function recruitpro_icon_exists($icon_name) {
    
    static $icon_manager = null;
    
    if (is_null($icon_manager)) {
        $icon_manager = new RecruitPro_Icon_Fonts();
    }
    
    $icons = $icon_manager->get_recruitment_icons();
    return isset($icons[$icon_name]);
}

?>