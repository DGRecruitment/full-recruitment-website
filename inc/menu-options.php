<?php
/**
 * RecruitPro Theme Menu Options
 *
 * This file handles navigation menu customization options for the RecruitPro
 * recruitment website theme. It includes menu styling, behavior, accessibility,
 * mega menu support, mobile navigation, and professional menu configurations
 * specifically designed for recruitment agencies and HR consultancies.
 *
 * @package RecruitPro
 * @subpackage Theme/Navigation
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/menu-options.php
 * Purpose: Navigation menu customization and management
 * Dependencies: WordPress core, theme functions, customizer
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Menu Options Class
 * 
 * Handles all navigation menu functionality including styling,
 * behavior, accessibility, and professional menu configurations.
 *
 * @since 1.0.0
 */
class RecruitPro_Menu_Options {

    /**
     * Menu locations configuration
     * 
     * @since 1.0.0
     * @var array
     */
    private $menu_locations = array();

    /**
     * Menu styles and configurations
     * 
     * @since 1.0.0
     * @var array
     */
    private $menu_styles = array();

    /**
     * Mobile menu behaviors
     * 
     * @since 1.0.0
     * @var array
     */
    private $mobile_behaviors = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_menu_locations();
        $this->init_menu_styles();
        $this->init_mobile_behaviors();
        
        add_action('after_setup_theme', array($this, 'register_menu_locations'));
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_filter('wp_nav_menu_args', array($this, 'modify_menu_args'));
        add_filter('nav_menu_link_attributes', array($this, 'add_menu_link_attributes'), 10, 4);
        add_filter('nav_menu_css_class', array($this, 'add_menu_item_classes'), 10, 4);
        add_filter('wp_nav_menu_items', array($this, 'add_menu_extras'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_menu_assets'));
        add_action('wp_head', array($this, 'output_menu_styles'), 25);
        add_action('wp_footer', array($this, 'output_menu_scripts'));
        add_filter('body_class', array($this, 'add_menu_body_classes'));
        add_action('wp_ajax_recruitpro_mobile_menu', array($this, 'ajax_mobile_menu_content'));
        add_action('wp_ajax_nopriv_recruitpro_mobile_menu', array($this, 'ajax_mobile_menu_content'));
    }

    /**
     * Initialize menu locations
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_menu_locations() {
        
        $this->menu_locations = array(
            'primary' => array(
                'name' => __('Primary Navigation', 'recruitpro'),
                'description' => __('Main navigation menu in header', 'recruitpro'),
                'location' => 'header',
                'supports' => array('mega_menu', 'dropdowns', 'icons', 'descriptions'),
                'default_items' => array('Home', 'About', 'Services', 'Jobs', 'Contact'),
            ),
            'footer' => array(
                'name' => __('Footer Navigation', 'recruitpro'),
                'description' => __('Footer menu for important links', 'recruitpro'),
                'location' => 'footer',
                'supports' => array('simple'),
                'default_items' => array('Privacy Policy', 'Terms', 'Careers', 'Contact'),
            ),
            'mobile' => array(
                'name' => __('Mobile Navigation', 'recruitpro'),
                'description' => __('Special mobile menu configuration', 'recruitpro'),
                'location' => 'mobile',
                'supports' => array('accordion', 'search', 'cta_button'),
                'default_items' => array('Home', 'Services', 'Jobs', 'About', 'Contact'),
            ),
            'top_bar' => array(
                'name' => __('Top Bar Menu', 'recruitpro'),
                'description' => __('Small utility menu above header', 'recruitpro'),
                'location' => 'top_bar',
                'supports' => array('simple', 'social_links'),
                'default_items' => array('Client Login', 'Candidate Portal', 'Support'),
            ),
            'services' => array(
                'name' => __('Services Menu', 'recruitpro'),
                'description' => __('Recruitment services navigation', 'recruitpro'),
                'location' => 'services',
                'supports' => array('mega_menu', 'icons', 'descriptions'),
                'default_items' => array('Permanent Placement', 'Temporary Staffing', 'Executive Search'),
            ),
            'candidates' => array(
                'name' => __('Candidates Menu', 'recruitpro'),
                'description' => __('Candidate-focused navigation', 'recruitpro'),
                'location' => 'candidates',
                'supports' => array('dropdowns', 'icons'),
                'default_items' => array('Find Jobs', 'Career Advice', 'Upload CV', 'Profile'),
            ),
            'clients' => array(
                'name' => __('Clients Menu', 'recruitpro'),
                'description' => __('Client and employer navigation', 'recruitpro'),
                'location' => 'clients',
                'supports' => array('dropdowns', 'cta_button'),
                'default_items' => array('Post Job', 'Our Process', 'Success Stories', 'Contact'),
            ),
        );
    }

    /**
     * Initialize menu styles
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_menu_styles() {
        
        $this->menu_styles = array(
            'horizontal' => array(
                'name' => __('Horizontal', 'recruitpro'),
                'description' => __('Traditional horizontal menu bar', 'recruitpro'),
                'layout' => 'horizontal',
                'dropdowns' => 'below',
                'suitable_for' => array('primary', 'footer'),
            ),
            'horizontal_centered' => array(
                'name' => __('Horizontal Centered', 'recruitpro'),
                'description' => __('Centered horizontal navigation', 'recruitpro'),
                'layout' => 'horizontal',
                'alignment' => 'center',
                'dropdowns' => 'below',
                'suitable_for' => array('primary'),
            ),
            'vertical_sidebar' => array(
                'name' => __('Vertical Sidebar', 'recruitpro'),
                'description' => __('Vertical menu in sidebar', 'recruitpro'),
                'layout' => 'vertical',
                'dropdowns' => 'right',
                'suitable_for' => array('services', 'candidates'),
            ),
            'mega_menu' => array(
                'name' => __('Mega Menu', 'recruitpro'),
                'description' => __('Full-width dropdown with multiple columns', 'recruitpro'),
                'layout' => 'horizontal',
                'dropdowns' => 'mega',
                'columns' => 'multiple',
                'suitable_for' => array('primary', 'services'),
            ),
            'tab_style' => array(
                'name' => __('Tab Style', 'recruitpro'),
                'description' => __('Tab-like navigation appearance', 'recruitpro'),
                'layout' => 'horizontal',
                'style' => 'tabs',
                'suitable_for' => array('services', 'candidates'),
            ),
            'pill_style' => array(
                'name' => __('Pill Style', 'recruitpro'),
                'description' => __('Rounded pill-shaped menu items', 'recruitpro'),
                'layout' => 'horizontal',
                'style' => 'pills',
                'suitable_for' => array('primary', 'top_bar'),
            ),
            'corporate' => array(
                'name' => __('Corporate', 'recruitpro'),
                'description' => __('Professional corporate styling', 'recruitpro'),
                'layout' => 'horizontal',
                'style' => 'corporate',
                'spacing' => 'wide',
                'suitable_for' => array('primary', 'clients'),
            ),
            'modern_minimal' => array(
                'name' => __('Modern Minimal', 'recruitpro'),
                'description' => __('Clean, minimal modern design', 'recruitpro'),
                'layout' => 'horizontal',
                'style' => 'minimal',
                'spacing' => 'compact',
                'suitable_for' => array('primary', 'footer'),
            ),
        );
    }

    /**
     * Initialize mobile menu behaviors
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_mobile_behaviors() {
        
        $this->mobile_behaviors = array(
            'slide_left' => array(
                'name' => __('Slide from Left', 'recruitpro'),
                'description' => __('Menu slides in from the left side', 'recruitpro'),
                'animation' => 'slide',
                'direction' => 'left',
                'overlay' => true,
            ),
            'slide_right' => array(
                'name' => __('Slide from Right', 'recruitpro'),
                'description' => __('Menu slides in from the right side', 'recruitpro'),
                'animation' => 'slide',
                'direction' => 'right',
                'overlay' => true,
            ),
            'slide_top' => array(
                'name' => __('Slide from Top', 'recruitpro'),
                'description' => __('Menu slides down from header', 'recruitpro'),
                'animation' => 'slide',
                'direction' => 'top',
                'overlay' => false,
            ),
            'fullscreen_overlay' => array(
                'name' => __('Fullscreen Overlay', 'recruitpro'),
                'description' => __('Full screen menu overlay', 'recruitpro'),
                'animation' => 'fade',
                'direction' => 'center',
                'overlay' => true,
                'fullscreen' => true,
            ),
            'accordion' => array(
                'name' => __('Accordion Style', 'recruitpro'),
                'description' => __('Expandable accordion menu', 'recruitpro'),
                'animation' => 'expand',
                'direction' => 'top',
                'overlay' => false,
                'collapsible' => true,
            ),
            'push_content' => array(
                'name' => __('Push Content', 'recruitpro'),
                'description' => __('Menu pushes page content aside', 'recruitpro'),
                'animation' => 'push',
                'direction' => 'left',
                'overlay' => false,
            ),
        );
    }

    /**
     * Register menu locations
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_menu_locations() {
        
        $locations = array();
        
        foreach ($this->menu_locations as $location_key => $location_config) {
            $locations[$location_key] = $location_config['name'];
        }
        
        register_nav_menus($locations);
    }

    /**
     * Add menu customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {

        // =================================================================
        // MENU PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_menu_panel', array(
            'title' => __('Menu Options', 'recruitpro'),
            'description' => __('Configure navigation menus, styling, behavior, and mobile navigation for your recruitment website.', 'recruitpro'),
            'priority' => 120,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // PRIMARY MENU SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_primary_menu', array(
            'title' => __('Primary Menu', 'recruitpro'),
            'description' => __('Configure the main navigation menu in your header.', 'recruitpro'),
            'panel' => 'recruitpro_menu_panel',
            'priority' => 10,
        ));

        // Primary Menu Style
        $wp_customize->add_setting('recruitpro_primary_menu_style', array(
            'default' => 'corporate',
            'sanitize_callback' => array($this, 'sanitize_menu_style'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_primary_menu_style', array(
            'label' => __('Primary Menu Style', 'recruitpro'),
            'description' => __('Choose the styling for your main navigation menu.', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'select',
            'choices' => $this->get_menu_style_choices('primary'),
        ));

        // Menu Alignment
        $wp_customize->add_setting('recruitpro_primary_menu_alignment', array(
            'default' => 'right',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_primary_menu_alignment', array(
            'label' => __('Menu Alignment', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'select',
            'choices' => array(
                'left' => __('Left Aligned', 'recruitpro'),
                'center' => __('Center Aligned', 'recruitpro'),
                'right' => __('Right Aligned', 'recruitpro'),
                'justified' => __('Justified', 'recruitpro'),
            ),
        ));

        // Enable Mega Menu
        $wp_customize->add_setting('recruitpro_enable_mega_menu', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_mega_menu', array(
            'label' => __('Enable Mega Menu', 'recruitpro'),
            'description' => __('Allow multi-column dropdown menus for extensive service listings.', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'checkbox',
        ));

        // Menu Item Spacing
        $wp_customize->add_setting('recruitpro_menu_item_spacing', array(
            'default' => 20,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_menu_item_spacing', array(
            'label' => __('Menu Item Spacing (pixels)', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 10,
                'max' => 50,
                'step' => 5,
            ),
        ));

        // Enable Menu Icons
        $wp_customize->add_setting('recruitpro_menu_enable_icons', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_menu_enable_icons', array(
            'label' => __('Enable Menu Icons', 'recruitpro'),
            'description' => __('Show icons next to menu items when available.', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'checkbox',
        ));

        // Menu Typography
        $wp_customize->add_setting('recruitpro_menu_font_size', array(
            'default' => 16,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_menu_font_size', array(
            'label' => __('Menu Font Size (pixels)', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 12,
                'max' => 24,
                'step' => 1,
            ),
        ));

        $wp_customize->add_setting('recruitpro_menu_font_weight', array(
            'default' => '500',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_menu_font_weight', array(
            'label' => __('Menu Font Weight', 'recruitpro'),
            'section' => 'recruitpro_primary_menu',
            'type' => 'select',
            'choices' => array(
                '400' => __('Normal (400)', 'recruitpro'),
                '500' => __('Medium (500)', 'recruitpro'),
                '600' => __('Semi-Bold (600)', 'recruitpro'),
                '700' => __('Bold (700)', 'recruitpro'),
            ),
        ));

        // =================================================================
        // MOBILE MENU SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_mobile_menu', array(
            'title' => __('Mobile Menu', 'recruitpro'),
            'description' => __('Configure mobile navigation behavior and styling.', 'recruitpro'),
            'panel' => 'recruitpro_menu_panel',
            'priority' => 20,
        ));

        // Mobile Menu Behavior
        $wp_customize->add_setting('recruitpro_mobile_menu_behavior', array(
            'default' => 'slide_left',
            'sanitize_callback' => array($this, 'sanitize_mobile_behavior'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_behavior', array(
            'label' => __('Mobile Menu Behavior', 'recruitpro'),
            'description' => __('How the mobile menu appears and behaves.', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'select',
            'choices' => $this->get_mobile_behavior_choices(),
        ));

        // Mobile Menu Breakpoint
        $wp_customize->add_setting('recruitpro_mobile_menu_breakpoint', array(
            'default' => 768,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_breakpoint', array(
            'label' => __('Mobile Menu Breakpoint (pixels)', 'recruitpro'),
            'description' => __('Screen width where mobile menu activates.', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 480,
                'max' => 1024,
                'step' => 1,
            ),
        ));

        // Mobile Menu Toggle Style
        $wp_customize->add_setting('recruitpro_mobile_toggle_style', array(
            'default' => 'hamburger',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_toggle_style', array(
            'label' => __('Mobile Toggle Style', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'select',
            'choices' => array(
                'hamburger' => __('Hamburger Icon (3 lines)', 'recruitpro'),
                'dots' => __('3 Dots Menu', 'recruitpro'),
                'plus' => __('Plus Icon', 'recruitpro'),
                'menu_text' => __('Text "Menu"', 'recruitpro'),
                'custom' => __('Custom Icon', 'recruitpro'),
            ),
        ));

        // Enable Mobile Search
        $wp_customize->add_setting('recruitpro_mobile_menu_search', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_search', array(
            'label' => __('Enable Mobile Menu Search', 'recruitpro'),
            'description' => __('Include search functionality in mobile menu.', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'checkbox',
        ));

        // Mobile Menu CTA Button
        $wp_customize->add_setting('recruitpro_mobile_menu_cta_enable', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_cta_enable', array(
            'label' => __('Enable Mobile CTA Button', 'recruitpro'),
            'description' => __('Show call-to-action button in mobile menu.', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'checkbox',
        ));

        $wp_customize->add_setting('recruitpro_mobile_menu_cta_text', array(
            'default' => __('Post a Job', 'recruitpro'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_cta_text', array(
            'label' => __('Mobile CTA Button Text', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'text',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_mobile_menu_cta_enable', true);
            },
        ));

        $wp_customize->add_setting('recruitpro_mobile_menu_cta_url', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_cta_url', array(
            'label' => __('Mobile CTA Button URL', 'recruitpro'),
            'section' => 'recruitpro_mobile_menu',
            'type' => 'url',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_mobile_menu_cta_enable', true);
            },
        ));

        // =================================================================
        // DROPDOWN MENU SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_dropdown_menu', array(
            'title' => __('Dropdown Menus', 'recruitpro'),
            'description' => __('Configure dropdown and submenu styling and behavior.', 'recruitpro'),
            'panel' => 'recruitpro_menu_panel',
            'priority' => 30,
        ));

        // Dropdown Animation
        $wp_customize->add_setting('recruitpro_dropdown_animation', array(
            'default' => 'fade_slide',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_dropdown_animation', array(
            'label' => __('Dropdown Animation', 'recruitpro'),
            'section' => 'recruitpro_dropdown_menu',
            'type' => 'select',
            'choices' => array(
                'none' => __('No Animation', 'recruitpro'),
                'fade' => __('Fade In', 'recruitpro'),
                'slide_down' => __('Slide Down', 'recruitpro'),
                'fade_slide' => __('Fade + Slide', 'recruitpro'),
                'zoom' => __('Zoom In', 'recruitpro'),
            ),
        ));

        // Dropdown Animation Speed
        $wp_customize->add_setting('recruitpro_dropdown_speed', array(
            'default' => 300,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_dropdown_speed', array(
            'label' => __('Animation Speed (milliseconds)', 'recruitpro'),
            'section' => 'recruitpro_dropdown_menu',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 100,
                'max' => 1000,
                'step' => 50,
            ),
        ));

        // Dropdown Width
        $wp_customize->add_setting('recruitpro_dropdown_width', array(
            'default' => 220,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_dropdown_width', array(
            'label' => __('Dropdown Width (pixels)', 'recruitpro'),
            'section' => 'recruitpro_dropdown_menu',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 180,
                'max' => 400,
                'step' => 20,
            ),
        ));

        // Enable Menu Descriptions
        $wp_customize->add_setting('recruitpro_menu_descriptions', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_menu_descriptions', array(
            'label' => __('Enable Menu Descriptions', 'recruitpro'),
            'description' => __('Show menu item descriptions in dropdowns.', 'recruitpro'),
            'section' => 'recruitpro_dropdown_menu',
            'type' => 'checkbox',
        ));

        // =================================================================
        // MENU COLORS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_menu_colors', array(
            'title' => __('Menu Colors', 'recruitpro'),
            'description' => __('Customize menu colors and hover effects.', 'recruitpro'),
            'panel' => 'recruitpro_menu_panel',
            'priority' => 40,
        ));

        // Menu Text Color
        $wp_customize->add_setting('recruitpro_menu_text_color', array(
            'default' => '#1e293b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_menu_text_color', array(
            'label' => __('Menu Text Color', 'recruitpro'),
            'section' => 'recruitpro_menu_colors',
        )));

        // Menu Hover Color
        $wp_customize->add_setting('recruitpro_menu_hover_color', array(
            'default' => '#1e40af',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_menu_hover_color', array(
            'label' => __('Menu Hover Color', 'recruitpro'),
            'section' => 'recruitpro_menu_colors',
        )));

        // Active Menu Color
        $wp_customize->add_setting('recruitpro_menu_active_color', array(
            'default' => '#1e40af',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_menu_active_color', array(
            'label' => __('Active Menu Item Color', 'recruitpro'),
            'section' => 'recruitpro_menu_colors',
        )));

        // Dropdown Background
        $wp_customize->add_setting('recruitpro_dropdown_background', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_dropdown_background', array(
            'label' => __('Dropdown Background Color', 'recruitpro'),
            'section' => 'recruitpro_menu_colors',
        )));

        // Mobile Menu Background
        $wp_customize->add_setting('recruitpro_mobile_menu_background', array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_mobile_menu_background', array(
            'label' => __('Mobile Menu Background Color', 'recruitpro'),
            'section' => 'recruitpro_menu_colors',
        )));

        // =================================================================
        // MENU ACCESSIBILITY SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_menu_accessibility', array(
            'title' => __('Menu Accessibility', 'recruitpro'),
            'description' => __('Configure accessibility features for navigation menus.', 'recruitpro'),
            'panel' => 'recruitpro_menu_panel',
            'priority' => 50,
        ));

        // Enable Keyboard Navigation
        $wp_customize->add_setting('recruitpro_menu_keyboard_navigation', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_menu_keyboard_navigation', array(
            'label' => __('Enable Keyboard Navigation', 'recruitpro'),
            'description' => __('Full keyboard navigation support for accessibility.', 'recruitpro'),
            'section' => 'recruitpro_menu_accessibility',
            'type' => 'checkbox',
        ));

        // Focus Indicators
        $wp_customize->add_setting('recruitpro_menu_focus_indicators', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_menu_focus_indicators', array(
            'label' => __('Enhanced Focus Indicators', 'recruitpro'),
            'description' => __('Clear visual focus indicators for keyboard users.', 'recruitpro'),
            'section' => 'recruitpro_menu_accessibility',
            'type' => 'checkbox',
        ));

        // ARIA Labels
        $wp_customize->add_setting('recruitpro_menu_aria_labels', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_menu_aria_labels', array(
            'label' => __('Enhanced ARIA Labels', 'recruitpro'),
            'description' => __('Comprehensive ARIA labeling for screen readers.', 'recruitpro'),
            'section' => 'recruitpro_menu_accessibility',
            'type' => 'checkbox',
        ));

        // Skip Navigation
        $wp_customize->add_setting('recruitpro_menu_skip_navigation', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_menu_skip_navigation', array(
            'label' => __('Skip Navigation Links', 'recruitpro'),
            'description' => __('Add skip navigation links for screen readers.', 'recruitpro'),
            'section' => 'recruitpro_menu_accessibility',
            'type' => 'checkbox',
        ));
    }

    /**
     * Modify menu arguments
     * 
     * @since 1.0.0
     * @param array $args Menu arguments
     * @return array Modified arguments
     */
    public function modify_menu_args($args) {
        
        // Add custom walker for enhanced functionality
        if (isset($args['theme_location'])) {
            switch ($args['theme_location']) {
                case 'primary':
                    if (get_theme_mod('recruitpro_enable_mega_menu', true)) {
                        $args['walker'] = new RecruitPro_Mega_Menu_Walker();
                    }
                    break;
                    
                case 'mobile':
                    $args['walker'] = new RecruitPro_Mobile_Menu_Walker();
                    break;
            }
        }
        
        // Add menu container class
        if (empty($args['container_class'])) {
            $args['container_class'] = 'menu-container';
        }
        
        // Add menu class
        if (empty($args['menu_class'])) {
            $args['menu_class'] = 'nav-menu';
        }
        
        return $args;
    }

    /**
     * Add menu link attributes
     * 
     * @since 1.0.0
     * @param array $atts Link attributes
     * @param WP_Post $item Menu item
     * @param stdClass $args Menu arguments
     * @param int $depth Menu depth
     * @return array Modified attributes
     */
    public function add_menu_link_attributes($atts, $item, $args, $depth) {
        
        // Add accessibility attributes
        if (get_theme_mod('recruitpro_menu_aria_labels', true)) {
            
            // Add aria-current for current page
            if (in_array('current-menu-item', $item->classes)) {
                $atts['aria-current'] = 'page';
            }
            
            // Add aria-expanded for dropdown menus
            if (in_array('menu-item-has-children', $item->classes)) {
                $atts['aria-expanded'] = 'false';
                $atts['aria-haspopup'] = 'true';
            }
        }
        
        // Add data attributes for enhanced functionality
        $atts['data-menu-item-id'] = $item->ID;
        
        // Add role for better accessibility
        if ($depth > 0) {
            $atts['role'] = 'menuitem';
        }
        
        return $atts;
    }

    /**
     * Add menu item classes
     * 
     * @since 1.0.0
     * @param array $classes Menu item classes
     * @param WP_Post $item Menu item
     * @param stdClass $args Menu arguments
     * @param int $depth Menu depth
     * @return array Modified classes
     */
    public function add_menu_item_classes($classes, $item, $args, $depth) {
        
        // Add depth class
        $classes[] = 'menu-depth-' . $depth;
        
        // Add icon class if icons are enabled
        if (get_theme_mod('recruitpro_menu_enable_icons', false)) {
            $icon = get_post_meta($item->ID, '_recruitpro_menu_icon', true);
            if (!empty($icon)) {
                $classes[] = 'has-icon';
                $classes[] = 'icon-' . $icon;
            }
        }
        
        // Add CTA class for call-to-action items
        $is_cta = get_post_meta($item->ID, '_recruitpro_menu_cta', true);
        if ($is_cta) {
            $classes[] = 'menu-cta-item';
        }
        
        // Add mega menu classes
        if (get_theme_mod('recruitpro_enable_mega_menu', true) && $depth === 0) {
            $mega_menu = get_post_meta($item->ID, '_recruitpro_mega_menu', true);
            if ($mega_menu) {
                $classes[] = 'has-mega-menu';
            }
        }
        
        return $classes;
    }

    /**
     * Add menu extras (search, CTA, etc.)
     * 
     * @since 1.0.0
     * @param string $items Menu items HTML
     * @param stdClass $args Menu arguments
     * @return string Modified menu HTML
     */
    public function add_menu_extras($items, $args) {
        
        if (!isset($args->theme_location)) {
            return $items;
        }
        
        switch ($args->theme_location) {
            case 'primary':
                // Add search to primary menu
                if (get_theme_mod('recruitpro_primary_menu_search', false)) {
                    $items .= '<li class="menu-item menu-search">';
                    $items .= '<a href="#" class="search-toggle" aria-label="' . esc_attr__('Search', 'recruitpro') . '">';
                    $items .= recruitpro_get_icon('search', array('decorative' => true));
                    $items .= '</a></li>';
                }
                
                // Add CTA button to primary menu
                $cta_text = get_theme_mod('recruitpro_header_cta_text', '');
                $cta_url = get_theme_mod('recruitpro_header_cta_url', '');
                if (!empty($cta_text) && !empty($cta_url)) {
                    $items .= '<li class="menu-item menu-cta">';
                    $items .= '<a href="' . esc_url($cta_url) . '" class="menu-cta-button">';
                    $items .= esc_html($cta_text);
                    $items .= '</a></li>';
                }
                break;
                
            case 'mobile':
                // Add search to mobile menu
                if (get_theme_mod('recruitpro_mobile_menu_search', true)) {
                    $search_form = get_search_form(false);
                    $items = '<li class="menu-item menu-search-form">' . $search_form . '</li>' . $items;
                }
                
                // Add CTA to mobile menu
                if (get_theme_mod('recruitpro_mobile_menu_cta_enable', true)) {
                    $cta_text = get_theme_mod('recruitpro_mobile_menu_cta_text', __('Post a Job', 'recruitpro'));
                    $cta_url = get_theme_mod('recruitpro_mobile_menu_cta_url', '');
                    if (!empty($cta_url)) {
                        $items .= '<li class="menu-item menu-cta mobile-cta">';
                        $items .= '<a href="' . esc_url($cta_url) . '" class="mobile-cta-button">';
                        $items .= esc_html($cta_text);
                        $items .= '</a></li>';
                    }
                }
                break;
        }
        
        return $items;
    }

    /**
     * Enqueue menu assets
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_menu_assets() {
        
        $theme_version = wp_get_theme()->get('Version');
        
        // Main menu script
        wp_enqueue_script(
            'recruitpro-menu-navigation',
            get_template_directory_uri() . '/assets/js/menu-navigation.js',
            array('jquery'),
            $theme_version,
            true
        );
        
        // Mobile menu script
        $mobile_behavior = get_theme_mod('recruitpro_mobile_menu_behavior', 'slide_left');
        wp_enqueue_script(
            'recruitpro-mobile-menu',
            get_template_directory_uri() . '/assets/js/mobile-menu-' . str_replace('_', '-', $mobile_behavior) . '.js',
            array('jquery'),
            $theme_version,
            true
        );
        
        // Mega menu script if enabled
        if (get_theme_mod('recruitpro_enable_mega_menu', true)) {
            wp_enqueue_script(
                'recruitpro-mega-menu',
                get_template_directory_uri() . '/assets/js/mega-menu.js',
                array('jquery'),
                $theme_version,
                true
            );
        }
        
        // Localize menu scripts
        wp_localize_script('recruitpro-menu-navigation', 'recruitpro_menu', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_menu_nonce'),
            'mobile_breakpoint' => get_theme_mod('recruitpro_mobile_menu_breakpoint', 768),
            'dropdown_animation' => get_theme_mod('recruitpro_dropdown_animation', 'fade_slide'),
            'dropdown_speed' => get_theme_mod('recruitpro_dropdown_speed', 300),
            'keyboard_navigation' => get_theme_mod('recruitpro_menu_keyboard_navigation', true),
            'strings' => array(
                'menu_toggle' => __('Toggle Menu', 'recruitpro'),
                'submenu_toggle' => __('Toggle Submenu', 'recruitpro'),
                'close_menu' => __('Close Menu', 'recruitpro'),
                'search_placeholder' => __('Search jobs, services...', 'recruitpro'),
            ),
        ));
    }

    /**
     * Output menu styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_menu_styles() {
        
        $menu_text_color = get_theme_mod('recruitpro_menu_text_color', '#1e293b');
        $menu_hover_color = get_theme_mod('recruitpro_menu_hover_color', '#1e40af');
        $menu_active_color = get_theme_mod('recruitpro_menu_active_color', '#1e40af');
        $dropdown_background = get_theme_mod('recruitpro_dropdown_background', '#ffffff');
        $mobile_menu_background = get_theme_mod('recruitpro_mobile_menu_background', '#ffffff');
        $menu_font_size = get_theme_mod('recruitpro_menu_font_size', 16);
        $menu_font_weight = get_theme_mod('recruitpro_menu_font_weight', '500');
        $menu_item_spacing = get_theme_mod('recruitpro_menu_item_spacing', 20);
        $dropdown_width = get_theme_mod('recruitpro_dropdown_width', 220);
        $mobile_breakpoint = get_theme_mod('recruitpro_mobile_menu_breakpoint', 768);

        ?>
        <style id="recruitpro-menu-customizations">
        /* Menu Variables */
        :root {
            --recruitpro-menu-text-color: <?php echo esc_attr($menu_text_color); ?>;
            --recruitpro-menu-hover-color: <?php echo esc_attr($menu_hover_color); ?>;
            --recruitpro-menu-active-color: <?php echo esc_attr($menu_active_color); ?>;
            --recruitpro-dropdown-background: <?php echo esc_attr($dropdown_background); ?>;
            --recruitpro-mobile-menu-background: <?php echo esc_attr($mobile_menu_background); ?>;
            --recruitpro-menu-font-size: <?php echo intval($menu_font_size); ?>px;
            --recruitpro-menu-font-weight: <?php echo esc_attr($menu_font_weight); ?>;
            --recruitpro-menu-item-spacing: <?php echo intval($menu_item_spacing); ?>px;
            --recruitpro-dropdown-width: <?php echo intval($dropdown_width); ?>px;
            --recruitpro-mobile-breakpoint: <?php echo intval($mobile_breakpoint); ?>px;
        }

        /* Primary Menu Styles */
        .nav-menu {
            font-size: var(--recruitpro-menu-font-size);
            font-weight: var(--recruitpro-menu-font-weight);
        }

        .nav-menu a {
            color: var(--recruitpro-menu-text-color);
            text-decoration: none;
            transition: color 0.3s ease;
            padding: 10px var(--recruitpro-menu-item-spacing);
            display: block;
        }

        .nav-menu a:hover,
        .nav-menu a:focus {
            color: var(--recruitpro-menu-hover-color);
        }

        .nav-menu .current-menu-item > a,
        .nav-menu .current-menu-ancestor > a {
            color: var(--recruitpro-menu-active-color);
        }

        /* Dropdown Styles */
        .nav-menu .sub-menu {
            background: var(--recruitpro-dropdown-background);
            min-width: var(--recruitpro-dropdown-width);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 10px 0;
        }

        .nav-menu .sub-menu a {
            padding: 8px 20px;
            font-size: 14px;
        }

        /* Menu Icons */
        .has-icon .menu-icon {
            margin-right: 8px;
            font-size: 16px;
        }

        /* CTA Menu Items */
        .menu-cta-button,
        .mobile-cta-button {
            background: var(--recruitpro-menu-hover-color) !important;
            color: white !important;
            border-radius: 6px;
            padding: 10px 20px !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .menu-cta-button:hover,
        .mobile-cta-button:hover {
            background: var(--recruitpro-menu-active-color) !important;
            transform: translateY(-1px);
        }

        /* Mobile Menu Styles */
        @media (max-width: <?php echo intval($mobile_breakpoint); ?>px) {
            .mobile-menu {
                background: var(--recruitpro-mobile-menu-background);
            }

            .mobile-menu .nav-menu {
                flex-direction: column;
            }

            .mobile-menu .nav-menu a {
                padding: 15px 20px;
                border-bottom: 1px solid #f1f5f9;
            }

            .mobile-menu .sub-menu {
                position: static;
                box-shadow: none;
                background: transparent;
                padding-left: 20px;
            }
        }

        /* Accessibility Enhancements */
        <?php if (get_theme_mod('recruitpro_menu_focus_indicators', true)): ?>
        .nav-menu a:focus {
            outline: 2px solid var(--recruitpro-menu-hover-color);
            outline-offset: 2px;
        }
        <?php endif; ?>

        /* Menu Animations */
        .nav-menu .sub-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .nav-menu .menu-item:hover .sub-menu,
        .nav-menu .menu-item:focus-within .sub-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Mega Menu Styles */
        .has-mega-menu .mega-menu {
            background: var(--recruitpro-dropdown-background);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            width: 100%;
            max-width: 1000px;
        }

        /* Professional Recruitment Menu Styles */
        .menu-container.recruitment-focused {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 8px;
            padding: 5px;
        }

        .menu-container.recruitment-focused .nav-menu a {
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .menu-container.recruitment-focused .nav-menu a:hover {
            background: rgba(30, 64, 175, 0.1);
            transform: translateY(-1px);
        }
        </style>
        <?php
    }

    /**
     * Output menu scripts
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_menu_scripts() {
        
        if (get_theme_mod('recruitpro_menu_skip_navigation', true)) {
            ?>
            <script>
            // Skip navigation functionality
            document.addEventListener('DOMContentLoaded', function() {
                const skipLinks = document.querySelectorAll('.skip-link');
                skipLinks.forEach(function(link) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.focus();
                            target.scrollIntoView();
                        }
                    });
                });
            });
            </script>
            <?php
        }
    }

    /**
     * Add menu body classes
     * 
     * @since 1.0.0
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_menu_body_classes($classes) {
        
        $primary_style = get_theme_mod('recruitpro_primary_menu_style', 'corporate');
        $classes[] = 'menu-style-' . str_replace('_', '-', $primary_style);
        
        $mobile_behavior = get_theme_mod('recruitpro_mobile_menu_behavior', 'slide_left');
        $classes[] = 'mobile-menu-' . str_replace('_', '-', $mobile_behavior);
        
        if (get_theme_mod('recruitpro_enable_mega_menu', true)) {
            $classes[] = 'has-mega-menu';
        }
        
        if (get_theme_mod('recruitpro_menu_enable_icons', false)) {
            $classes[] = 'menu-icons-enabled';
        }
        
        if (get_theme_mod('recruitpro_menu_keyboard_navigation', true)) {
            $classes[] = 'keyboard-navigation-enabled';
        }
        
        return $classes;
    }

    /**
     * AJAX handler for mobile menu content
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_mobile_menu_content() {
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_menu_nonce')) {
            wp_die('Security check failed');
        }
        
        $menu_location = sanitize_text_field($_POST['menu_location']);
        
        // Get menu content
        $menu_content = wp_nav_menu(array(
            'theme_location' => $menu_location,
            'echo' => false,
            'container' => 'nav',
            'container_class' => 'mobile-menu-content',
            'fallback_cb' => false,
        ));
        
        wp_send_json_success(array(
            'menu_content' => $menu_content,
        ));
    }

    /**
     * Get menu style choices for customizer
     * 
     * @since 1.0.0
     * @param string $location Menu location
     * @return array Style choices
     */
    private function get_menu_style_choices($location = 'primary') {
        
        $choices = array();
        
        foreach ($this->menu_styles as $key => $style) {
            if (empty($style['suitable_for']) || in_array($location, $style['suitable_for'])) {
                $choices[$key] = $style['name'] . ' - ' . $style['description'];
            }
        }
        
        return $choices;
    }

    /**
     * Get mobile behavior choices for customizer
     * 
     * @since 1.0.0
     * @return array Behavior choices
     */
    private function get_mobile_behavior_choices() {
        
        $choices = array();
        
        foreach ($this->mobile_behaviors as $key => $behavior) {
            $choices[$key] = $behavior['name'] . ' - ' . $behavior['description'];
        }
        
        return $choices;
    }

    /**
     * Sanitize menu style selection
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_menu_style($input) {
        
        $valid_styles = array_keys($this->menu_styles);
        return in_array($input, $valid_styles) ? $input : 'corporate';
    }

    /**
     * Sanitize mobile behavior selection
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_mobile_behavior($input) {
        
        $valid_behaviors = array_keys($this->mobile_behaviors);
        return in_array($input, $valid_behaviors) ? $input : 'slide_left';
    }

    /**
     * Get menu configuration
     * 
     * @since 1.0.0
     * @param string $location Menu location
     * @return array Menu configuration
     */
    public function get_menu_config($location) {
        
        return isset($this->menu_locations[$location]) ? $this->menu_locations[$location] : array();
    }

    /**
     * Check if location supports feature
     * 
     * @since 1.0.0
     * @param string $location Menu location
     * @param string $feature Feature to check
     * @return bool Whether feature is supported
     */
    public function supports_feature($location, $feature) {
        
        $config = $this->get_menu_config($location);
        return !empty($config['supports']) && in_array($feature, $config['supports']);
    }
}

// Initialize menu options
new RecruitPro_Menu_Options();

/**
 * Helper function to get menu configuration
 * 
 * @since 1.0.0
 * @param string $location Menu location
 * @return array Menu configuration
 */
function recruitpro_get_menu_config($location) {
    
    static $menu_manager = null;
    
    if (is_null($menu_manager)) {
        $menu_manager = new RecruitPro_Menu_Options();
    }
    
    return $menu_manager->get_menu_config($location);
}

/**
 * Helper function to check if menu supports feature
 * 
 * @since 1.0.0
 * @param string $location Menu location
 * @param string $feature Feature to check
 * @return bool Whether feature is supported
 */
function recruitpro_menu_supports($location, $feature) {
    
    static $menu_manager = null;
    
    if (is_null($menu_manager)) {
        $menu_manager = new RecruitPro_Menu_Options();
    }
    
    return $menu_manager->supports_feature($location, $feature);
}

/**
 * Helper function to render custom menu
 * 
 * @since 1.0.0
 * @param string $location Menu location
 * @param array $args Additional arguments
 * @return void
 */
function recruitpro_render_menu($location, $args = array()) {
    
    $defaults = array(
        'theme_location' => $location,
        'container' => 'nav',
        'container_class' => 'menu-container menu-' . $location,
        'menu_class' => 'nav-menu',
        'fallback_cb' => false,
        'depth' => 0,
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Add location-specific classes
    $config = recruitpro_get_menu_config($location);
    if (!empty($config)) {
        $args['container_class'] .= ' menu-location-' . $location;
        
        if (!empty($config['supports'])) {
            foreach ($config['supports'] as $feature) {
                $args['container_class'] .= ' supports-' . str_replace('_', '-', $feature);
            }
        }
    }
    
    wp_nav_menu($args);
}

?>