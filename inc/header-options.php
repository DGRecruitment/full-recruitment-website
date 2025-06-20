<?php
/**
 * RecruitPro Theme Header Customizer Options
 *
 * This file handles all header-related customization options for the
 * RecruitPro recruitment website theme. It provides comprehensive header
 * customization including logo management, navigation settings, contact
 * information, call-to-action elements, and professional recruitment
 * agency features.
 *
 * @package RecruitPro
 * @subpackage Theme/Customizer
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/header-options.php
 * Purpose: Header customizer panel and sections
 * Dependencies: WordPress Customizer API, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add header customizer options
 * 
 * @since 1.0.0
 * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
 * @return void
 */
function recruitpro_header_customizer_options($wp_customize) {

    // =================================================================
    // HEADER PANEL
    // =================================================================
    
    $wp_customize->add_panel('recruitpro_header_panel', array(
        'title' => __('Header Options', 'recruitpro'),
        'description' => __('Customize all header elements including logo, navigation, contact information, and call-to-action elements for your recruitment website.', 'recruitpro'),
        'priority' => 110,
        'capability' => 'edit_theme_options',
    ));

    // =================================================================
    // HEADER LAYOUT SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_header_layout', array(
        'title' => __('Header Layout & Style', 'recruitpro'),
        'description' => __('Configure the overall header layout and visual style.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 10,
    ));

    // Header Layout Style
    $wp_customize->add_setting('recruitpro_header_layout_style', array(
        'default' => 'professional',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_layout_style', array(
        'label' => __('Header Layout Style', 'recruitpro'),
        'description' => __('Choose the header layout that best represents your recruitment agency.', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
        'type' => 'select',
        'choices' => array(
            'minimal' => __('Minimal - Logo + Navigation Only', 'recruitpro'),
            'professional' => __('Professional - Logo + Navigation + Contact Info', 'recruitpro'),
            'corporate' => __('Corporate - Full Header with CTA', 'recruitpro'),
            'executive' => __('Executive - Premium Layout with All Elements', 'recruitpro'),
            'modern' => __('Modern - Centered Layout with Search', 'recruitpro'),
        ),
    ));

    // Header Height
    $wp_customize->add_setting('recruitpro_header_height', array(
        'default' => 80,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_height', array(
        'label' => __('Header Height (pixels)', 'recruitpro'),
        'description' => __('Set the height of the header area.', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 60,
            'max' => 150,
            'step' => 5,
        ),
    ));

    // Header Background Type
    $wp_customize->add_setting('recruitpro_header_background_type', array(
        'default' => 'color',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_background_type', array(
        'label' => __('Header Background Type', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
        'type' => 'select',
        'choices' => array(
            'color' => __('Solid Color', 'recruitpro'),
            'gradient' => __('Gradient', 'recruitpro'),
            'transparent' => __('Transparent', 'recruitpro'),
        ),
    ));

    // Header Background Color
    $wp_customize->add_setting('recruitpro_header_background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_header_background_color', array(
        'label' => __('Header Background Color', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
        'active_callback' => function() {
            return in_array(get_theme_mod('recruitpro_header_background_type', 'color'), array('color', 'gradient'));
        },
    )));

    // Header Text Color
    $wp_customize->add_setting('recruitpro_header_text_color', array(
        'default' => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_header_text_color', array(
        'label' => __('Header Text Color', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
    )));

    // Sticky Header
    $wp_customize->add_setting('recruitpro_header_sticky', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_sticky', array(
        'label' => __('Enable Sticky Header', 'recruitpro'),
        'description' => __('Header remains visible when scrolling down the page.', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
        'type' => 'checkbox',
    ));

    // Header Shadow
    $wp_customize->add_setting('recruitpro_header_shadow', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_shadow', array(
        'label' => __('Enable Header Shadow', 'recruitpro'),
        'description' => __('Add subtle shadow below header for depth.', 'recruitpro'),
        'section' => 'recruitpro_header_layout',
        'type' => 'checkbox',
    ));

    // =================================================================
    // LOGO & BRANDING SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_header_logo', array(
        'title' => __('Logo & Branding', 'recruitpro'),
        'description' => __('Configure your recruitment agency logo and branding elements.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 20,
    ));

    // Logo Type
    $wp_customize->add_setting('recruitpro_logo_type', array(
        'default' => 'image',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_logo_type', array(
        'label' => __('Logo Type', 'recruitpro'),
        'section' => 'recruitpro_header_logo',
        'type' => 'select',
        'choices' => array(
            'image' => __('Image Logo', 'recruitpro'),
            'text' => __('Text Logo', 'recruitpro'),
            'image_text' => __('Image + Text Combination', 'recruitpro'),
        ),
    ));

    // Logo Image (inherited from site identity, but with recruitment-specific guidance)
    $wp_customize->get_control('custom_logo')->description = __('Upload your recruitment agency logo. Recommended size: 200x60 pixels for best results.', 'recruitpro');

    // Text Logo (Site Title)
    $wp_customize->add_setting('recruitpro_text_logo_font_size', array(
        'default' => 28,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_text_logo_font_size', array(
        'label' => __('Text Logo Font Size', 'recruitpro'),
        'section' => 'recruitpro_header_logo',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 18,
            'max' => 48,
            'step' => 1,
        ),
        'active_callback' => function() {
            return in_array(get_theme_mod('recruitpro_logo_type', 'image'), array('text', 'image_text'));
        },
    ));

    // Logo Width
    $wp_customize->add_setting('recruitpro_logo_width', array(
        'default' => 180,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_logo_width', array(
        'label' => __('Logo Width (pixels)', 'recruitpro'),
        'section' => 'recruitpro_header_logo',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 100,
            'max' => 400,
            'step' => 5,
        ),
        'active_callback' => function() {
            return in_array(get_theme_mod('recruitpro_logo_type', 'image'), array('image', 'image_text'));
        },
    ));

    // Agency Tagline
    $wp_customize->add_setting('recruitpro_agency_tagline', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_agency_tagline', array(
        'label' => __('Agency Tagline', 'recruitpro'),
        'description' => __('Short tagline displayed below logo (e.g., "Professional Recruitment Solutions").', 'recruitpro'),
        'section' => 'recruitpro_header_logo',
        'type' => 'text',
    ));

    // =================================================================
    // CONTACT INFORMATION SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_header_contact', array(
        'title' => __('Contact Information', 'recruitpro'),
        'description' => __('Display contact information in the header for easy client and candidate access.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 30,
    ));

    // Enable Header Contact Info
    $wp_customize->add_setting('recruitpro_header_contact_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_contact_enable', array(
        'label' => __('Enable Header Contact Information', 'recruitpro'),
        'section' => 'recruitpro_header_contact',
        'type' => 'checkbox',
    ));

    // Phone Number
    $wp_customize->add_setting('recruitpro_header_phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_phone', array(
        'label' => __('Phone Number', 'recruitpro'),
        'description' => __('Main contact phone number for immediate inquiries.', 'recruitpro'),
        'section' => 'recruitpro_header_contact',
        'type' => 'tel',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_contact_enable', true);
        },
    ));

    // Email Address
    $wp_customize->add_setting('recruitpro_header_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_email', array(
        'label' => __('Email Address', 'recruitpro'),
        'description' => __('Main contact email for general inquiries.', 'recruitpro'),
        'section' => 'recruitpro_header_contact',
        'type' => 'email',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_contact_enable', true);
        },
    ));

    // Business Hours
    $wp_customize->add_setting('recruitpro_header_hours', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_hours', array(
        'label' => __('Business Hours', 'recruitpro'),
        'description' => __('Brief business hours (e.g., "Mon-Fri 9-6").', 'recruitpro'),
        'section' => 'recruitpro_header_contact',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_contact_enable', true);
        },
    ));

    // Office Location
    $wp_customize->add_setting('recruitpro_header_location', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_location', array(
        'label' => __('Office Location', 'recruitpro'),
        'description' => __('City or region (e.g., "London, UK" or "New York").', 'recruitpro'),
        'section' => 'recruitpro_header_contact',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_contact_enable', true);
        },
    ));

    // =================================================================
    // CALL-TO-ACTION SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_header_cta', array(
        'title' => __('Call-to-Action Elements', 'recruitpro'),
        'description' => __('Add prominent call-to-action buttons and elements to drive engagement.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 40,
    ));

    // Enable Primary CTA Button
    $wp_customize->add_setting('recruitpro_header_cta_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_cta_enable', array(
        'label' => __('Enable Primary CTA Button', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'checkbox',
    ));

    // Primary CTA Text
    $wp_customize->add_setting('recruitpro_header_cta_text', array(
        'default' => __('Post a Job', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_cta_text', array(
        'label' => __('Primary CTA Button Text', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_cta_enable', true);
        },
    ));

    // Primary CTA URL
    $wp_customize->add_setting('recruitpro_header_cta_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_cta_url', array(
        'label' => __('Primary CTA Button URL', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'url',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_cta_enable', true);
        },
    ));

    // Primary CTA Style
    $wp_customize->add_setting('recruitpro_header_cta_style', array(
        'default' => 'primary',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_cta_style', array(
        'label' => __('Primary CTA Button Style', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'select',
        'choices' => array(
            'primary' => __('Primary (Filled)', 'recruitpro'),
            'secondary' => __('Secondary (Outlined)', 'recruitpro'),
            'ghost' => __('Ghost (Text Only)', 'recruitpro'),
        ),
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_cta_enable', true);
        },
    ));

    // Enable Secondary CTA
    $wp_customize->add_setting('recruitpro_header_cta2_enable', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_cta2_enable', array(
        'label' => __('Enable Secondary CTA Button', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'checkbox',
    ));

    // Secondary CTA Text
    $wp_customize->add_setting('recruitpro_header_cta2_text', array(
        'default' => __('Find Jobs', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_cta2_text', array(
        'label' => __('Secondary CTA Button Text', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_cta2_enable', false);
        },
    ));

    // Secondary CTA URL
    $wp_customize->add_setting('recruitpro_header_cta2_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_cta2_url', array(
        'label' => __('Secondary CTA Button URL', 'recruitpro'),
        'section' => 'recruitpro_header_cta',
        'type' => 'url',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_cta2_enable', false);
        },
    ));

    // =================================================================
    // NAVIGATION SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_header_navigation', array(
        'title' => __('Navigation Settings', 'recruitpro'),
        'description' => __('Configure navigation menu settings and behavior.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 50,
    ));

    // Navigation Style
    $wp_customize->add_setting('recruitpro_nav_style', array(
        'default' => 'horizontal',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_nav_style', array(
        'label' => __('Navigation Style', 'recruitpro'),
        'section' => 'recruitpro_header_navigation',
        'type' => 'select',
        'choices' => array(
            'horizontal' => __('Horizontal Menu', 'recruitpro'),
            'centered' => __('Centered Below Logo', 'recruitpro'),
            'split' => __('Split Navigation', 'recruitpro'),
        ),
    ));

    // Enable Mega Menu
    $wp_customize->add_setting('recruitpro_nav_mega_menu', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_nav_mega_menu', array(
        'label' => __('Enable Mega Menu Support', 'recruitpro'),
        'description' => __('Allow multi-column dropdown menus for extensive service listings.', 'recruitpro'),
        'section' => 'recruitpro_header_navigation',
        'type' => 'checkbox',
    ));

    // Mobile Menu Style
    $wp_customize->add_setting('recruitpro_mobile_nav_style', array(
        'default' => 'slide_in',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_mobile_nav_style', array(
        'label' => __('Mobile Menu Style', 'recruitpro'),
        'section' => 'recruitpro_header_navigation',
        'type' => 'select',
        'choices' => array(
            'slide_in' => __('Slide In from Side', 'recruitpro'),
            'dropdown' => __('Dropdown Below Header', 'recruitpro'),
            'fullscreen' => __('Fullscreen Overlay', 'recruitpro'),
        ),
    ));

    // =================================================================
    // SEARCH & FEATURES SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_header_features', array(
        'title' => __('Search & Features', 'recruitpro'),
        'description' => __('Configure additional header features like search, language switcher, and AI chat.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 60,
    ));

    // Enable Header Search
    $wp_customize->add_setting('recruitpro_header_search_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_search_enable', array(
        'label' => __('Enable Header Search', 'recruitpro'),
        'description' => __('Show search icon/form in header for job and content search.', 'recruitpro'),
        'section' => 'recruitpro_header_features',
        'type' => 'checkbox',
    ));

    // Search Placeholder
    $wp_customize->add_setting('recruitpro_header_search_placeholder', array(
        'default' => __('Search jobs, candidates, or services...', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_header_search_placeholder', array(
        'label' => __('Search Placeholder Text', 'recruitpro'),
        'section' => 'recruitpro_header_features',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_header_search_enable', true);
        },
    ));

    // Enable Language Switcher
    $wp_customize->add_setting('recruitpro_header_language_switcher', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_language_switcher', array(
        'label' => __('Enable Language Switcher', 'recruitpro'),
        'description' => __('Show language selector for multi-language recruitment websites.', 'recruitpro'),
        'section' => 'recruitpro_header_features',
        'type' => 'checkbox',
    ));

    // Social Media Links in Header
    $wp_customize->add_setting('recruitpro_header_social_enable', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_social_enable', array(
        'label' => __('Enable Social Media Links', 'recruitpro'),
        'description' => __('Show social media icons in header (LinkedIn, etc.).', 'recruitpro'),
        'section' => 'recruitpro_header_features',
        'type' => 'checkbox',
    ));

    // AI Chat Widget in Header
    $wp_customize->add_setting('recruitpro_header_ai_chat_icon', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_header_ai_chat_icon', array(
        'label' => __('Show AI Chat Icon in Header', 'recruitpro'),
        'description' => __('Display AI chat activation icon in header area.', 'recruitpro'),
        'section' => 'recruitpro_header_features',
        'type' => 'checkbox',
    ));

    // =================================================================
    // MOBILE HEADER SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_mobile_header', array(
        'title' => __('Mobile Header', 'recruitpro'),
        'description' => __('Specific settings for mobile header layout and behavior.', 'recruitpro'),
        'panel' => 'recruitpro_header_panel',
        'priority' => 70,
    ));

    // Mobile Header Height
    $wp_customize->add_setting('recruitpro_mobile_header_height', array(
        'default' => 60,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_mobile_header_height', array(
        'label' => __('Mobile Header Height (pixels)', 'recruitpro'),
        'section' => 'recruitpro_mobile_header',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 50,
            'max' => 100,
            'step' => 5,
        ),
    ));

    // Mobile Logo Width
    $wp_customize->add_setting('recruitpro_mobile_logo_width', array(
        'default' => 120,
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_mobile_logo_width', array(
        'label' => __('Mobile Logo Width (pixels)', 'recruitpro'),
        'section' => 'recruitpro_mobile_header',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 80,
            'max' => 200,
            'step' => 5,
        ),
    ));

    // Show Contact in Mobile
    $wp_customize->add_setting('recruitpro_mobile_show_contact', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_mobile_show_contact', array(
        'label' => __('Show Contact Info on Mobile', 'recruitpro'),
        'description' => __('Display simplified contact information on mobile devices.', 'recruitpro'),
        'section' => 'recruitpro_mobile_header',
        'type' => 'checkbox',
    ));

    // Mobile CTA Button
    $wp_customize->add_setting('recruitpro_mobile_show_cta', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_mobile_show_cta', array(
        'label' => __('Show CTA Button on Mobile', 'recruitpro'),
        'description' => __('Display primary CTA button on mobile devices.', 'recruitpro'),
        'section' => 'recruitpro_mobile_header',
        'type' => 'checkbox',
    ));
}

/**
 * Header selective refresh
 * 
 * @since 1.0.0
 * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
 * @return void
 */
function recruitpro_header_selective_refresh($wp_customize) {
    
    // Agency tagline selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_agency_tagline', array(
        'selector' => '.agency-tagline',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_agency_tagline', '');
        },
    ));
    
    // Header phone selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_header_phone', array(
        'selector' => '.header-phone',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_header_phone', '');
        },
    ));
    
    // Header email selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_header_email', array(
        'selector' => '.header-email',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_header_email', '');
        },
    ));
    
    // CTA button selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_header_cta_text', array(
        'selector' => '.header-cta-primary',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_header_cta_text', __('Post a Job', 'recruitpro'));
        },
    ));
}

// Hook into customizer
add_action('customize_register', 'recruitpro_header_customizer_options');
add_action('customize_register', 'recruitpro_header_selective_refresh');

/**
 * Header customizer CSS output
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_header_customizer_css() {
    
    $header_height = get_theme_mod('recruitpro_header_height', 80);
    $mobile_header_height = get_theme_mod('recruitpro_mobile_header_height', 60);
    $header_bg_color = get_theme_mod('recruitpro_header_background_color', '#ffffff');
    $header_text_color = get_theme_mod('recruitpro_header_text_color', '#1e293b');
    $logo_width = get_theme_mod('recruitpro_logo_width', 180);
    $mobile_logo_width = get_theme_mod('recruitpro_mobile_logo_width', 120);
    $text_logo_size = get_theme_mod('recruitpro_text_logo_font_size', 28);
    $header_shadow = get_theme_mod('recruitpro_header_shadow', true);

    $css = "
    .site-header {
        height: {$header_height}px;
        background-color: {$header_bg_color};
        color: {$header_text_color};
    }
    
    .site-header,
    .site-header a {
        color: {$header_text_color};
    }
    
    .custom-logo {
        max-width: {$logo_width}px;
        height: auto;
    }
    
    .site-title {
        font-size: {$text_logo_size}px;
        color: {$header_text_color};
    }
    ";

    if ($header_shadow) {
        $css .= "
        .site-header {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        ";
    }

    // Mobile styles
    $css .= "
    @media (max-width: 768px) {
        .site-header {
            height: {$mobile_header_height}px;
        }
        
        .custom-logo {
            max-width: {$mobile_logo_width}px;
        }
        
        .site-title {
            font-size: " . ($text_logo_size * 0.8) . "px;
        }
    }
    ";
    
    wp_add_inline_style('recruitpro-main-style', $css);
}

add_action('wp_enqueue_scripts', 'recruitpro_header_customizer_css');

/**
 * Add header body classes based on customizer settings
 * 
 * @since 1.0.0
 * @param array $classes Existing body classes
 * @return array Modified body classes
 */
function recruitpro_header_body_classes($classes) {
    
    $header_layout = get_theme_mod('recruitpro_header_layout_style', 'professional');
    $classes[] = 'header-layout-' . $header_layout;
    
    if (get_theme_mod('recruitpro_header_sticky', true)) {
        $classes[] = 'header-sticky';
    }
    
    if (get_theme_mod('recruitpro_header_contact_enable', true)) {
        $classes[] = 'header-has-contact';
    }
    
    if (get_theme_mod('recruitpro_header_cta_enable', true)) {
        $classes[] = 'header-has-cta';
    }
    
    $nav_style = get_theme_mod('recruitpro_nav_style', 'horizontal');
    $classes[] = 'nav-style-' . $nav_style;
    
    $mobile_nav_style = get_theme_mod('recruitpro_mobile_nav_style', 'slide_in');
    $classes[] = 'mobile-nav-' . str_replace('_', '-', $mobile_nav_style);
    
    return $classes;
}

add_filter('body_class', 'recruitpro_header_body_classes');

/**
 * Register header widget areas if needed
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_header_widgets_init() {
    
    // Header widget area for additional content
    register_sidebar(array(
        'name' => __('Header Widget Area', 'recruitpro'),
        'id' => 'header-widget-area',
        'description' => __('Widget area for additional header content (e.g., contact info, social links).', 'recruitpro'),
        'before_widget' => '<div id="%1$s" class="header-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="header-widget-title">',
        'after_title' => '</h4>',
    ));
}

add_action('widgets_init', 'recruitpro_header_widgets_init');

/**
 * Helper function to get header contact information
 * 
 * @since 1.0.0
 * @return array Header contact information
 */
function recruitpro_get_header_contact_info() {
    
    if (!get_theme_mod('recruitpro_header_contact_enable', true)) {
        return array();
    }

    return array(
        'phone' => get_theme_mod('recruitpro_header_phone', ''),
        'email' => get_theme_mod('recruitpro_header_email', ''),
        'hours' => get_theme_mod('recruitpro_header_hours', ''),
        'location' => get_theme_mod('recruitpro_header_location', ''),
    );
}

/**
 * Helper function to get header CTA buttons
 * 
 * @since 1.0.0
 * @return array Header CTA buttons
 */
function recruitpro_get_header_cta_buttons() {
    
    $buttons = array();
    
    if (get_theme_mod('recruitpro_header_cta_enable', true)) {
        $buttons['primary'] = array(
            'text' => get_theme_mod('recruitpro_header_cta_text', __('Post a Job', 'recruitpro')),
            'url' => get_theme_mod('recruitpro_header_cta_url', ''),
            'style' => get_theme_mod('recruitpro_header_cta_style', 'primary'),
        );
    }
    
    if (get_theme_mod('recruitpro_header_cta2_enable', false)) {
        $buttons['secondary'] = array(
            'text' => get_theme_mod('recruitpro_header_cta2_text', __('Find Jobs', 'recruitpro')),
            'url' => get_theme_mod('recruitpro_header_cta2_url', ''),
            'style' => 'secondary',
        );
    }
    
    return $buttons;
}

?>