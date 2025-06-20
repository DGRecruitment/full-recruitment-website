<?php
/**
 * RecruitPro Theme Sidebar Options
 *
 * This file handles sidebar customization options for the RecruitPro recruitment
 * website theme. It provides comprehensive sidebar management including layout
 * controls, widget styling, behavior settings, mobile responsiveness, and
 * recruitment-specific sidebar functionality designed for HR agencies.
 *
 * @package RecruitPro
 * @subpackage Theme/Sidebars
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/sidebar-options.php
 * Purpose: Sidebar customization and widget area management
 * Dependencies: WordPress Customizer API, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Sidebar Options Class
 * 
 * Handles all sidebar customization functionality including layout controls,
 * widget styling, behavior settings, and recruitment-specific features.
 *
 * @since 1.0.0
 */
class RecruitPro_Sidebar_Options {

    /**
     * Available sidebar positions
     * 
     * @since 1.0.0
     * @var array
     */
    private $sidebar_positions = array();

    /**
     * Available widget styles
     * 
     * @since 1.0.0
     * @var array
     */
    private $widget_styles = array();

    /**
     * Sidebar layouts for different content types
     * 
     * @since 1.0.0
     * @var array
     */
    private $sidebar_layouts = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_sidebar_configurations();
        $this->init_hooks();
    }

    /**
     * Initialize sidebar configurations
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_sidebar_configurations() {
        $this->init_sidebar_positions();
        $this->init_widget_styles();
        $this->init_sidebar_layouts();
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        add_action('customize_register', array($this, 'register_customizer_options'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_sidebar_styles'));
        add_action('wp_head', array($this, 'output_sidebar_custom_css'));
        add_filter('body_class', array($this, 'add_sidebar_body_classes'));
        add_action('wp_footer', array($this, 'output_sidebar_scripts'));
        
        // Admin hooks
        add_action('admin_init', array($this, 'register_sidebar_settings'));
        add_action('admin_menu', array($this, 'add_sidebar_admin_page'));
        add_action('wp_ajax_recruitpro_preview_sidebar', array($this, 'ajax_preview_sidebar'));
    }

    /**
     * Initialize sidebar positions
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_sidebar_positions() {
        $this->sidebar_positions = array(
            'left' => array(
                'name' => __('Left Sidebar', 'recruitpro'),
                'description' => __('Sidebar appears on the left side of content', 'recruitpro'),
                'css_class' => 'sidebar-left',
                'content_order' => array('sidebar', 'content'),
            ),
            'right' => array(
                'name' => __('Right Sidebar', 'recruitpro'),
                'description' => __('Sidebar appears on the right side of content', 'recruitpro'),
                'css_class' => 'sidebar-right',
                'content_order' => array('content', 'sidebar'),
            ),
            'dual' => array(
                'name' => __('Dual Sidebars', 'recruitpro'),
                'description' => __('Content centered with sidebars on both sides', 'recruitpro'),
                'css_class' => 'sidebar-dual',
                'content_order' => array('sidebar-left', 'content', 'sidebar-right'),
            ),
            'none' => array(
                'name' => __('No Sidebar', 'recruitpro'),
                'description' => __('Full width content without sidebar', 'recruitpro'),
                'css_class' => 'no-sidebar',
                'content_order' => array('content'),
            ),
        );
    }

    /**
     * Initialize widget styles
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_widget_styles() {
        $this->widget_styles = array(
            'default' => array(
                'name' => __('Default Style', 'recruitpro'),
                'description' => __('Standard widget appearance with subtle borders', 'recruitpro'),
                'css_class' => 'widget-style-default',
            ),
            'modern' => array(
                'name' => __('Modern Cards', 'recruitpro'),
                'description' => __('Card-based design with shadows and rounded corners', 'recruitpro'),
                'css_class' => 'widget-style-modern',
            ),
            'minimal' => array(
                'name' => __('Minimal Clean', 'recruitpro'),
                'description' => __('Clean minimal design without borders', 'recruitpro'),
                'css_class' => 'widget-style-minimal',
            ),
            'professional' => array(
                'name' => __('Professional', 'recruitpro'),
                'description' => __('Business-focused design for recruitment agencies', 'recruitpro'),
                'css_class' => 'widget-style-professional',
            ),
            'highlight' => array(
                'name' => __('Highlighted', 'recruitpro'),
                'description' => __('Eye-catching design with colored backgrounds', 'recruitpro'),
                'css_class' => 'widget-style-highlight',
            ),
        );
    }

    /**
     * Initialize sidebar layouts
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_sidebar_layouts() {
        $this->sidebar_layouts = array(
            'default' => array(
                'name' => __('Standard Layout', 'recruitpro'),
                'description' => __('Default sidebar layout with normal spacing', 'recruitpro'),
                'sidebar_width' => 300,
                'content_gap' => 40,
                'mobile_behavior' => 'below',
            ),
            'narrow' => array(
                'name' => __('Narrow Sidebar', 'recruitpro'),
                'description' => __('Compact sidebar for more content space', 'recruitpro'),
                'sidebar_width' => 250,
                'content_gap' => 30,
                'mobile_behavior' => 'below',
            ),
            'wide' => array(
                'name' => __('Wide Sidebar', 'recruitpro'),
                'description' => __('Spacious sidebar for detailed information', 'recruitpro'),
                'sidebar_width' => 350,
                'content_gap' => 50,
                'mobile_behavior' => 'below',
            ),
            'split' => array(
                'name' => __('Split Layout', 'recruitpro'),
                'description' => __('Equal width sidebar and content areas', 'recruitpro'),
                'sidebar_width' => '50%',
                'content_gap' => 40,
                'mobile_behavior' => 'stack',
            ),
        );
    }

    /**
     * Register customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function register_customizer_options($wp_customize) {

        // =================================================================
        // SIDEBAR PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_sidebar_panel', array(
            'title' => __('Sidebar Options', 'recruitpro'),
            'description' => __('Customize sidebar layout, styling, and behavior for your recruitment website.', 'recruitpro'),
            'priority' => 140,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // SIDEBAR LAYOUT SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_sidebar_layout', array(
            'title' => __('Sidebar Layout', 'recruitpro'),
            'description' => __('Configure sidebar position and layout settings.', 'recruitpro'),
            'panel' => 'recruitpro_sidebar_panel',
            'priority' => 10,
        ));

        // Default Sidebar Position
        $wp_customize->add_setting('recruitpro_default_sidebar_position', array(
            'default' => 'right',
            'sanitize_callback' => array($this, 'sanitize_sidebar_position'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_default_sidebar_position', array(
            'label' => __('Default Sidebar Position', 'recruitpro'),
            'description' => __('Choose the default position for sidebars.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_layout',
            'type' => 'select',
            'choices' => $this->get_sidebar_position_choices(),
        ));

        // Blog/Archive Sidebar Position
        $wp_customize->add_setting('recruitpro_blog_sidebar_position', array(
            'default' => 'right',
            'sanitize_callback' => array($this, 'sanitize_sidebar_position'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_blog_sidebar_position', array(
            'label' => __('Blog/Archive Sidebar Position', 'recruitpro'),
            'description' => __('Sidebar position for blog and archive pages.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_layout',
            'type' => 'select',
            'choices' => $this->get_sidebar_position_choices(),
        ));

        // Job Pages Sidebar Position
        $wp_customize->add_setting('recruitpro_job_sidebar_position', array(
            'default' => 'right',
            'sanitize_callback' => array($this, 'sanitize_sidebar_position'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_job_sidebar_position', array(
            'label' => __('Job Pages Sidebar Position', 'recruitpro'),
            'description' => __('Sidebar position for individual job listings.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_layout',
            'type' => 'select',
            'choices' => $this->get_sidebar_position_choices(),
        ));

        // Sidebar Layout Style
        $wp_customize->add_setting('recruitpro_sidebar_layout_style', array(
            'default' => 'default',
            'sanitize_callback' => array($this, 'sanitize_sidebar_layout'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sidebar_layout_style', array(
            'label' => __('Sidebar Layout Style', 'recruitpro'),
            'description' => __('Choose the overall sidebar layout configuration.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_layout',
            'type' => 'select',
            'choices' => $this->get_sidebar_layout_choices(),
        ));

        // =================================================================
        // SIDEBAR DIMENSIONS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_sidebar_dimensions', array(
            'title' => __('Sidebar Dimensions', 'recruitpro'),
            'description' => __('Control sidebar width, spacing, and responsive behavior.', 'recruitpro'),
            'panel' => 'recruitpro_sidebar_panel',
            'priority' => 20,
        ));

        // Sidebar Width
        $wp_customize->add_setting('recruitpro_sidebar_width', array(
            'default' => 300,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_sidebar_width', array(
            'label' => __('Sidebar Width (pixels)', 'recruitpro'),
            'description' => __('Set the width of the sidebar area.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_dimensions',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 200,
                'max' => 400,
                'step' => 10,
            ),
        ));

        // Content Gap
        $wp_customize->add_setting('recruitpro_sidebar_content_gap', array(
            'default' => 40,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_sidebar_content_gap', array(
            'label' => __('Content Gap (pixels)', 'recruitpro'),
            'description' => __('Space between sidebar and main content.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_dimensions',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 80,
                'step' => 5,
            ),
        ));

        // Widget Spacing
        $wp_customize->add_setting('recruitpro_widget_spacing', array(
            'default' => 30,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_widget_spacing', array(
            'label' => __('Widget Spacing (pixels)', 'recruitpro'),
            'description' => __('Vertical space between widgets.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_dimensions',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 15,
                'max' => 60,
                'step' => 5,
            ),
        ));

        // =================================================================
        // WIDGET STYLING SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_widget_styling', array(
            'title' => __('Widget Styling', 'recruitpro'),
            'description' => __('Customize the appearance of sidebar widgets.', 'recruitpro'),
            'panel' => 'recruitpro_sidebar_panel',
            'priority' => 30,
        ));

        // Widget Style
        $wp_customize->add_setting('recruitpro_widget_style', array(
            'default' => 'default',
            'sanitize_callback' => array($this, 'sanitize_widget_style'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_widget_style', array(
            'label' => __('Widget Style', 'recruitpro'),
            'description' => __('Choose the visual style for sidebar widgets.', 'recruitpro'),
            'section' => 'recruitpro_widget_styling',
            'type' => 'select',
            'choices' => $this->get_widget_style_choices(),
        ));

        // Widget Background Color
        $wp_customize->add_setting('recruitpro_widget_background_color', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_widget_background_color', array(
            'label' => __('Widget Background Color', 'recruitpro'),
            'description' => __('Background color for sidebar widgets.', 'recruitpro'),
            'section' => 'recruitpro_widget_styling',
        )));

        // Widget Border Color
        $wp_customize->add_setting('recruitpro_widget_border_color', array(
            'default' => '#e5e7eb',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_widget_border_color', array(
            'label' => __('Widget Border Color', 'recruitpro'),
            'description' => __('Border color for sidebar widgets.', 'recruitpro'),
            'section' => 'recruitpro_widget_styling',
        )));

        // Widget Title Color
        $wp_customize->add_setting('recruitpro_widget_title_color', array(
            'default' => '#1f2937',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_widget_title_color', array(
            'label' => __('Widget Title Color', 'recruitpro'),
            'description' => __('Color for widget titles.', 'recruitpro'),
            'section' => 'recruitpro_widget_styling',
        )));

        // Widget Text Color
        $wp_customize->add_setting('recruitpro_widget_text_color', array(
            'default' => '#4b5563',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_widget_text_color', array(
            'label' => __('Widget Text Color', 'recruitpro'),
            'description' => __('Color for widget content text.', 'recruitpro'),
            'section' => 'recruitpro_widget_styling',
        )));

        // Widget Border Radius
        $wp_customize->add_setting('recruitpro_widget_border_radius', array(
            'default' => 8,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_widget_border_radius', array(
            'label' => __('Widget Border Radius (pixels)', 'recruitpro'),
            'description' => __('Rounded corners for widgets.', 'recruitpro'),
            'section' => 'recruitpro_widget_styling',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 20,
                'step' => 1,
            ),
        ));

        // =================================================================
        // MOBILE BEHAVIOR SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_sidebar_mobile', array(
            'title' => __('Mobile Behavior', 'recruitpro'),
            'description' => __('Configure how sidebars behave on mobile devices.', 'recruitpro'),
            'panel' => 'recruitpro_sidebar_panel',
            'priority' => 40,
        ));

        // Mobile Sidebar Display
        $wp_customize->add_setting('recruitpro_mobile_sidebar_display', array(
            'default' => 'below',
            'sanitize_callback' => array($this, 'sanitize_mobile_behavior'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_sidebar_display', array(
            'label' => __('Mobile Sidebar Display', 'recruitpro'),
            'description' => __('How to display sidebar on mobile devices.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_mobile',
            'type' => 'select',
            'choices' => array(
                'below' => __('Below Content', 'recruitpro'),
                'above' => __('Above Content', 'recruitpro'),
                'collapsible' => __('Collapsible Toggle', 'recruitpro'),
                'hidden' => __('Hidden on Mobile', 'recruitpro'),
            ),
        ));

        // Mobile Breakpoint
        $wp_customize->add_setting('recruitpro_sidebar_mobile_breakpoint', array(
            'default' => 768,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sidebar_mobile_breakpoint', array(
            'label' => __('Mobile Breakpoint (pixels)', 'recruitpro'),
            'description' => __('Screen width where sidebar switches to mobile layout.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_mobile',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 480,
                'max' => 1024,
                'step' => 1,
            ),
        ));

        // =================================================================
        // RECRUITMENT FEATURES SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_sidebar_recruitment', array(
            'title' => __('Recruitment Features', 'recruitpro'),
            'description' => __('Special sidebar features for recruitment websites.', 'recruitpro'),
            'panel' => 'recruitpro_sidebar_panel',
            'priority' => 50,
        ));

        // Enable Job Search in Sidebar
        $wp_customize->add_setting('recruitpro_sidebar_job_search', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sidebar_job_search', array(
            'label' => __('Enable Job Search Widget', 'recruitpro'),
            'description' => __('Add a job search form to sidebar areas.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_recruitment',
            'type' => 'checkbox',
        ));

        // Enable Contact CTA
        $wp_customize->add_setting('recruitpro_sidebar_contact_cta', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sidebar_contact_cta', array(
            'label' => __('Enable Contact CTA Widget', 'recruitpro'),
            'description' => __('Add a contact call-to-action widget.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_recruitment',
            'type' => 'checkbox',
        ));

        // Enable Social Proof
        $wp_customize->add_setting('recruitpro_sidebar_social_proof', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sidebar_social_proof', array(
            'label' => __('Enable Social Proof Widget', 'recruitpro'),
            'description' => __('Show testimonials or statistics in sidebar.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_recruitment',
            'type' => 'checkbox',
        ));

        // Sticky Sidebar
        $wp_customize->add_setting('recruitpro_sticky_sidebar', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sticky_sidebar', array(
            'label' => __('Enable Sticky Sidebar', 'recruitpro'),
            'description' => __('Sidebar follows scroll for better usability.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_recruitment',
            'type' => 'checkbox',
        ));

        // =================================================================
        // ADVANCED OPTIONS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_sidebar_advanced', array(
            'title' => __('Advanced Options', 'recruitpro'),
            'description' => __('Advanced sidebar customization options.', 'recruitpro'),
            'panel' => 'recruitpro_sidebar_panel',
            'priority' => 60,
        ));

        // Custom CSS Classes
        $wp_customize->add_setting('recruitpro_sidebar_custom_classes', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sidebar_custom_classes', array(
            'label' => __('Custom CSS Classes', 'recruitpro'),
            'description' => __('Add custom CSS classes to sidebar containers.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_advanced',
            'type' => 'text',
        ));

        // Widget Animation
        $wp_customize->add_setting('recruitpro_widget_animation', array(
            'default' => 'none',
            'sanitize_callback' => array($this, 'sanitize_animation'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_widget_animation', array(
            'label' => __('Widget Animation', 'recruitpro'),
            'description' => __('Animation effect when widgets come into view.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_advanced',
            'type' => 'select',
            'choices' => array(
                'none' => __('No Animation', 'recruitpro'),
                'fade-in' => __('Fade In', 'recruitpro'),
                'slide-up' => __('Slide Up', 'recruitpro'),
                'slide-left' => __('Slide Left', 'recruitpro'),
                'scale' => __('Scale', 'recruitpro'),
            ),
        ));
    }

    /**
     * Enqueue sidebar styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_sidebar_styles() {
        wp_enqueue_style(
            'recruitpro-sidebar-styles',
            get_template_directory_uri() . '/assets/css/sidebar.css',
            array(),
            RECRUITPRO_THEME_VERSION
        );

        // Conditional scripts for sticky sidebar
        if (get_theme_mod('recruitpro_sticky_sidebar', true)) {
            wp_enqueue_script(
                'recruitpro-sticky-sidebar',
                get_template_directory_uri() . '/assets/js/sticky-sidebar.js',
                array('jquery'),
                RECRUITPRO_THEME_VERSION,
                true
            );
        }

        // Mobile sidebar toggle script
        if (get_theme_mod('recruitpro_mobile_sidebar_display', 'below') === 'collapsible') {
            wp_enqueue_script(
                'recruitpro-mobile-sidebar',
                get_template_directory_uri() . '/assets/js/mobile-sidebar.js',
                array('jquery'),
                RECRUITPRO_THEME_VERSION,
                true
            );
        }

        // Widget animation scripts
        if (get_theme_mod('recruitpro_widget_animation', 'none') !== 'none') {
            wp_enqueue_script(
                'recruitpro-widget-animations',
                get_template_directory_uri() . '/assets/js/widget-animations.js',
                array('jquery'),
                RECRUITPRO_THEME_VERSION,
                true
            );
        }
    }

    /**
     * Output custom CSS for sidebar customizations
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_sidebar_custom_css() {
        $sidebar_width = get_theme_mod('recruitpro_sidebar_width', 300);
        $content_gap = get_theme_mod('recruitpro_sidebar_content_gap', 40);
        $widget_spacing = get_theme_mod('recruitpro_widget_spacing', 30);
        $widget_bg_color = get_theme_mod('recruitpro_widget_background_color', '#ffffff');
        $widget_border_color = get_theme_mod('recruitpro_widget_border_color', '#e5e7eb');
        $widget_title_color = get_theme_mod('recruitpro_widget_title_color', '#1f2937');
        $widget_text_color = get_theme_mod('recruitpro_widget_text_color', '#4b5563');
        $widget_border_radius = get_theme_mod('recruitpro_widget_border_radius', 8);
        $mobile_breakpoint = get_theme_mod('recruitpro_sidebar_mobile_breakpoint', 768);

        echo '<style type="text/css" id="recruitpro-sidebar-custom-css">';
        
        // Sidebar dimensions
        echo "
        .sidebar-area {
            width: {$sidebar_width}px;
        }
        .content-sidebar-wrap {
            gap: {$content_gap}px;
        }
        .sidebar .widget {
            margin-bottom: {$widget_spacing}px;
            background-color: {$widget_bg_color};
            border: 1px solid {$widget_border_color};
            border-radius: {$widget_border_radius}px;
        }
        .sidebar .widget-title {
            color: {$widget_title_color};
        }
        .sidebar .widget,
        .sidebar .widget p,
        .sidebar .widget li {
            color: {$widget_text_color};
        }
        ";

        // Mobile responsive styles
        echo "
        @media (max-width: {$mobile_breakpoint}px) {
            .sidebar-area {
                width: 100%;
            }
            .content-sidebar-wrap {
                flex-direction: column;
                gap: {$widget_spacing}px;
            }
        }
        ";

        echo '</style>';
    }

    /**
     * Add sidebar-related body classes
     * 
     * @since 1.0.0
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_sidebar_body_classes($classes) {
        $sidebar_position = $this->get_current_sidebar_position();
        $widget_style = get_theme_mod('recruitpro_widget_style', 'default');
        $mobile_behavior = get_theme_mod('recruitpro_mobile_sidebar_display', 'below');

        // Add sidebar position class
        if (isset($this->sidebar_positions[$sidebar_position])) {
            $classes[] = $this->sidebar_positions[$sidebar_position]['css_class'];
        }

        // Add widget style class
        if (isset($this->widget_styles[$widget_style])) {
            $classes[] = $this->widget_styles[$widget_style]['css_class'];
        }

        // Add mobile behavior class
        $classes[] = 'mobile-sidebar-' . $mobile_behavior;

        // Add sticky sidebar class
        if (get_theme_mod('recruitpro_sticky_sidebar', true)) {
            $classes[] = 'has-sticky-sidebar';
        }

        return $classes;
    }

    /**
     * Output sidebar JavaScript
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_sidebar_scripts() {
        $animation = get_theme_mod('recruitpro_widget_animation', 'none');
        
        if ($animation !== 'none') {
            echo '<script type="text/javascript">';
            echo '
            jQuery(document).ready(function($) {
                $(".sidebar .widget").addClass("widget-animate widget-animate-' . esc_js($animation) . '");
                
                // Intersection Observer for animations
                if ("IntersectionObserver" in window) {
                    const observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add("widget-animate-active");
                            }
                        });
                    }, { threshold: 0.1 });
                    
                    $(".widget-animate").each(function() {
                        observer.observe(this);
                    });
                }
            });
            ';
            echo '</script>';
        }
    }

    /**
     * Register admin settings
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_sidebar_settings() {
        register_setting('recruitpro_sidebar_options', 'recruitpro_sidebar_presets');
        register_setting('recruitpro_sidebar_options', 'recruitpro_default_widgets');
    }

    /**
     * Add sidebar admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_sidebar_admin_page() {
        add_theme_page(
            __('Sidebar Manager', 'recruitpro'),
            __('Sidebar Manager', 'recruitpro'),
            'manage_options',
            'recruitpro-sidebar-manager',
            array($this, 'render_admin_page')
        );
    }

    /**
     * Render admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('RecruitPro Sidebar Manager', 'recruitpro'); ?></h1>
            <p><?php _e('Manage sidebar configurations and presets for your recruitment website.', 'recruitpro'); ?></p>
            
            <div class="sidebar-manager-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#presets" class="nav-tab nav-tab-active"><?php _e('Presets', 'recruitpro'); ?></a>
                    <a href="#widgets" class="nav-tab"><?php _e('Default Widgets', 'recruitpro'); ?></a>
                    <a href="#import-export" class="nav-tab"><?php _e('Import/Export', 'recruitpro'); ?></a>
                </nav>
                
                <div id="presets" class="tab-content active">
                    <h2><?php _e('Sidebar Presets', 'recruitpro'); ?></h2>
                    <p><?php _e('Quick preset configurations for different types of recruitment websites.', 'recruitpro'); ?></p>
                    
                    <div class="preset-grid">
                        <?php $this->render_sidebar_presets(); ?>
                    </div>
                </div>
                
                <div id="widgets" class="tab-content">
                    <h2><?php _e('Default Widget Configuration', 'recruitpro'); ?></h2>
                    <p><?php _e('Set default widgets for new sidebar areas.', 'recruitpro'); ?></p>
                    
                    <?php $this->render_default_widgets_form(); ?>
                </div>
                
                <div id="import-export" class="tab-content">
                    <h2><?php _e('Import/Export Sidebar Settings', 'recruitpro'); ?></h2>
                    
                    <?php $this->render_import_export_form(); ?>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
        });
        </script>
        <?php
    }

    /**
     * Render sidebar presets
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_sidebar_presets() {
        $presets = array(
            'agency' => array(
                'name' => __('Recruitment Agency', 'recruitpro'),
                'description' => __('Optimized for recruitment agencies with job search and contact forms', 'recruitpro'),
                'settings' => array(
                    'position' => 'right',
                    'style' => 'professional',
                    'width' => 300,
                ),
            ),
            'corporate' => array(
                'name' => __('Corporate HR', 'recruitpro'),
                'description' => __('Professional layout for corporate HR departments', 'recruitpro'),
                'settings' => array(
                    'position' => 'right',
                    'style' => 'minimal',
                    'width' => 280,
                ),
            ),
            'consulting' => array(
                'name' => __('HR Consulting', 'recruitpro'),
                'description' => __('Service-focused layout for HR consultants', 'recruitpro'),
                'settings' => array(
                    'position' => 'right',
                    'style' => 'modern',
                    'width' => 320,
                ),
            ),
        );

        foreach ($presets as $preset_id => $preset) {
            ?>
            <div class="preset-card">
                <h3><?php echo esc_html($preset['name']); ?></h3>
                <p><?php echo esc_html($preset['description']); ?></p>
                <button type="button" class="button apply-preset" data-preset="<?php echo esc_attr($preset_id); ?>">
                    <?php _e('Apply Preset', 'recruitpro'); ?>
                </button>
            </div>
            <?php
        }
    }

    /**
     * Render default widgets form
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_default_widgets_form() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('recruitpro_sidebar_options'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Default Sidebar Widgets', 'recruitpro'); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="recruitpro_default_widgets[]" value="search">
                                <?php _e('Search Widget', 'recruitpro'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="recruitpro_default_widgets[]" value="recent_posts">
                                <?php _e('Recent Posts', 'recruitpro'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="recruitpro_default_widgets[]" value="job_search">
                                <?php _e('Job Search', 'recruitpro'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="recruitpro_default_widgets[]" value="contact_info">
                                <?php _e('Contact Information', 'recruitpro'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render import/export form
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_import_export_form() {
        ?>
        <div class="import-export-section">
            <h3><?php _e('Export Settings', 'recruitpro'); ?></h3>
            <p><?php _e('Export your current sidebar settings to use on another site.', 'recruitpro'); ?></p>
            <button type="button" id="export-sidebar-settings" class="button">
                <?php _e('Export Sidebar Settings', 'recruitpro'); ?>
            </button>
            
            <hr>
            
            <h3><?php _e('Import Settings', 'recruitpro'); ?></h3>
            <p><?php _e('Import sidebar settings from another RecruitPro installation.', 'recruitpro'); ?></p>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="sidebar_settings_file" accept=".json">
                <input type="hidden" name="action" value="import_sidebar_settings">
                <?php wp_nonce_field('import_sidebar_settings', 'sidebar_import_nonce'); ?>
                <button type="submit" class="button"><?php _e('Import Settings', 'recruitpro'); ?></button>
            </form>
        </div>
        <?php
    }

    /**
     * AJAX preview sidebar
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_preview_sidebar() {
        check_ajax_referer('recruitpro_preview_sidebar', 'nonce');
        
        $preset = sanitize_text_field($_POST['preset']);
        // Return preview HTML for the preset
        
        wp_die();
    }

    /**
     * Get current sidebar position based on content type
     * 
     * @since 1.0.0
     * @return string Sidebar position
     */
    private function get_current_sidebar_position() {
        if (is_singular('job') || is_post_type_archive('job')) {
            return get_theme_mod('recruitpro_job_sidebar_position', 'right');
        } elseif (is_home() || is_archive() || is_search()) {
            return get_theme_mod('recruitpro_blog_sidebar_position', 'right');
        }
        
        return get_theme_mod('recruitpro_default_sidebar_position', 'right');
    }

    /**
     * Get sidebar position choices
     * 
     * @since 1.0.0
     * @return array Position choices
     */
    private function get_sidebar_position_choices() {
        $choices = array();
        foreach ($this->sidebar_positions as $key => $position) {
            $choices[$key] = $position['name'];
        }
        return $choices;
    }

    /**
     * Get widget style choices
     * 
     * @since 1.0.0
     * @return array Style choices
     */
    private function get_widget_style_choices() {
        $choices = array();
        foreach ($this->widget_styles as $key => $style) {
            $choices[$key] = $style['name'];
        }
        return $choices;
    }

    /**
     * Get sidebar layout choices
     * 
     * @since 1.0.0
     * @return array Layout choices
     */
    private function get_sidebar_layout_choices() {
        $choices = array();
        foreach ($this->sidebar_layouts as $key => $layout) {
            $choices[$key] = $layout['name'];
        }
        return $choices;
    }

    /**
     * Sanitize sidebar position
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_sidebar_position($input) {
        return array_key_exists($input, $this->sidebar_positions) ? $input : 'right';
    }

    /**
     * Sanitize widget style
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_widget_style($input) {
        return array_key_exists($input, $this->widget_styles) ? $input : 'default';
    }

    /**
     * Sanitize sidebar layout
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_sidebar_layout($input) {
        return array_key_exists($input, $this->sidebar_layouts) ? $input : 'default';
    }

    /**
     * Sanitize mobile behavior
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_mobile_behavior($input) {
        $valid_options = array('below', 'above', 'collapsible', 'hidden');
        return in_array($input, $valid_options) ? $input : 'below';
    }

    /**
     * Sanitize animation
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_animation($input) {
        $valid_animations = array('none', 'fade-in', 'slide-up', 'slide-left', 'scale');
        return in_array($input, $valid_animations) ? $input : 'none';
    }
}

// Initialize the sidebar options manager
if (class_exists('RecruitPro_Sidebar_Options')) {
    new RecruitPro_Sidebar_Options();
}

/**
 * Helper function to get sidebar configuration
 * 
 * @since 1.0.0
 * @param string $context Context for sidebar (default, blog, job, etc.)
 * @return array Sidebar configuration
 */
function recruitpro_get_sidebar_config($context = 'default') {
    $config = array(
        'position' => get_theme_mod('recruitpro_default_sidebar_position', 'right'),
        'width' => get_theme_mod('recruitpro_sidebar_width', 300),
        'style' => get_theme_mod('recruitpro_widget_style', 'default'),
        'spacing' => get_theme_mod('recruitpro_widget_spacing', 30),
        'sticky' => get_theme_mod('recruitpro_sticky_sidebar', true),
    );

    // Context-specific overrides
    switch ($context) {
        case 'blog':
            $config['position'] = get_theme_mod('recruitpro_blog_sidebar_position', 'right');
            break;
        case 'job':
            $config['position'] = get_theme_mod('recruitpro_job_sidebar_position', 'right');
            break;
    }

    return apply_filters('recruitpro_sidebar_config', $config, $context);
}

/**
 * Helper function to check if sidebar should be displayed
 * 
 * @since 1.0.0
 * @param string $context Context for sidebar check
 * @return bool True if sidebar should be displayed
 */
function recruitpro_has_sidebar($context = 'default') {
    $config = recruitpro_get_sidebar_config($context);
    
    if ($config['position'] === 'none') {
        return false;
    }

    // Check if any sidebar area has widgets
    $sidebar_areas = array('sidebar-main', 'sidebar-jobs');
    
    foreach ($sidebar_areas as $area) {
        if (is_active_sidebar($area)) {
            return true;
        }
    }

    return false;
}

/**
 * Helper function to get sidebar classes
 * 
 * @since 1.0.0
 * @param string $context Context for sidebar
 * @return string Sidebar classes
 */
function recruitpro_get_sidebar_classes($context = 'default') {
    $config = recruitpro_get_sidebar_config($context);
    $classes = array('sidebar-area');
    
    $classes[] = 'sidebar-' . $config['position'];
    $classes[] = 'widget-style-' . $config['style'];
    
    if ($config['sticky']) {
        $classes[] = 'sidebar-sticky';
    }

    return implode(' ', apply_filters('recruitpro_sidebar_classes', $classes, $context));
}