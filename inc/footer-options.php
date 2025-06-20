<?php
/**
 * RecruitPro Theme Footer Customizer Options
 *
 * This file handles all footer-related customization options for the
 * RecruitPro recruitment website theme. It provides comprehensive footer
 * customization including contact information, social links, legal compliance,
 * newsletter signup, and multi-language support.
 *
 * @package RecruitPro
 * @subpackage Theme/Customizer
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/footer-options.php
 * Purpose: Footer customizer panel and sections
 * Dependencies: WordPress Customizer API, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add footer customizer options
 * 
 * @since 1.0.0
 * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
 * @return void
 */
function recruitpro_footer_customizer_options($wp_customize) {

    // =================================================================
    // FOOTER PANEL
    // =================================================================
    
    $wp_customize->add_panel('recruitpro_footer_panel', array(
        'title' => __('Footer Options', 'recruitpro'),
        'description' => __('Customize all footer elements including contact information, social links, legal compliance, and layout options.', 'recruitpro'),
        'priority' => 130,
        'capability' => 'edit_theme_options',
    ));

    // =================================================================
    // FOOTER LAYOUT SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_layout', array(
        'title' => __('Footer Layout', 'recruitpro'),
        'description' => __('Configure the overall footer layout and structure.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 10,
    ));

    // Footer Layout Style
    $wp_customize->add_setting('recruitpro_footer_layout_style', array(
        'default' => 'four_columns',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_footer_layout_style', array(
        'label' => __('Footer Layout Style', 'recruitpro'),
        'description' => __('Choose the footer layout structure.', 'recruitpro'),
        'section' => 'recruitpro_footer_layout',
        'type' => 'select',
        'choices' => array(
            'simple' => __('Simple - Single Row', 'recruitpro'),
            'two_columns' => __('Two Columns', 'recruitpro'),
            'three_columns' => __('Three Columns', 'recruitpro'),
            'four_columns' => __('Four Columns (Recommended)', 'recruitpro'),
            'mega_footer' => __('Mega Footer - Full Width', 'recruitpro'),
        ),
    ));

    // Footer Background
    $wp_customize->add_setting('recruitpro_footer_background_type', array(
        'default' => 'color',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_background_type', array(
        'label' => __('Footer Background Type', 'recruitpro'),
        'section' => 'recruitpro_footer_layout',
        'type' => 'select',
        'choices' => array(
            'color' => __('Solid Color', 'recruitpro'),
            'gradient' => __('Gradient', 'recruitpro'),
            'image' => __('Background Image', 'recruitpro'),
        ),
    ));

    // Footer Background Color
    $wp_customize->add_setting('recruitpro_footer_background_color', array(
        'default' => '#1e293b',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_footer_background_color', array(
        'label' => __('Footer Background Color', 'recruitpro'),
        'section' => 'recruitpro_footer_layout',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_background_type', 'color') === 'color';
        },
    )));

    // Footer Text Color
    $wp_customize->add_setting('recruitpro_footer_text_color', array(
        'default' => '#e2e8f0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_footer_text_color', array(
        'label' => __('Footer Text Color', 'recruitpro'),
        'section' => 'recruitpro_footer_layout',
    )));

    // Footer Enable/Disable
    $wp_customize->add_setting('recruitpro_footer_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_footer_enable', array(
        'label' => __('Enable Footer', 'recruitpro'),
        'description' => __('Show or hide the entire footer section.', 'recruitpro'),
        'section' => 'recruitpro_footer_layout',
        'type' => 'checkbox',
    ));

    // =================================================================
    // COMPANY INFORMATION SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_company_info', array(
        'title' => __('Company Information', 'recruitpro'),
        'description' => __('Add your recruitment agency contact details and company information.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 20,
    ));

    // Company Name
    $wp_customize->add_setting('recruitpro_footer_company_name', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_company_name', array(
        'label' => __('Company Name', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'type' => 'text',
    ));

    // Company Description
    $wp_customize->add_setting('recruitpro_footer_company_description', array(
        'default' => __('Professional recruitment agency specializing in connecting talented candidates with leading employers.', 'recruitpro'),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_company_description', array(
        'label' => __('Company Description', 'recruitpro'),
        'description' => __('Brief description of your recruitment services.', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'type' => 'textarea',
    ));

    // Company Logo for Footer
    $wp_customize->add_setting('recruitpro_footer_logo', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'recruitpro_footer_logo', array(
        'label' => __('Footer Logo', 'recruitpro'),
        'description' => __('Upload a logo for the footer (recommended: white/light version).', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'mime_type' => 'image',
    )));

    // Contact Address
    $wp_customize->add_setting('recruitpro_footer_address', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_address', array(
        'label' => __('Office Address', 'recruitpro'),
        'description' => __('Full business address including street, city, postal code.', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'type' => 'textarea',
    ));

    // Contact Phone
    $wp_customize->add_setting('recruitpro_footer_phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_phone', array(
        'label' => __('Phone Number', 'recruitpro'),
        'description' => __('Main contact phone number.', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'type' => 'tel',
    ));

    // Contact Email
    $wp_customize->add_setting('recruitpro_footer_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_email', array(
        'label' => __('Contact Email', 'recruitpro'),
        'description' => __('Main contact email address.', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'type' => 'email',
    ));

    // Business Hours
    $wp_customize->add_setting('recruitpro_footer_business_hours', array(
        'default' => __('Monday - Friday: 9:00 AM - 6:00 PM', 'recruitpro'),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_business_hours', array(
        'label' => __('Business Hours', 'recruitpro'),
        'section' => 'recruitpro_footer_company_info',
        'type' => 'textarea',
    ));

    // =================================================================
    // SOCIAL MEDIA LINKS SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_social_links', array(
        'title' => __('Social Media Links', 'recruitpro'),
        'description' => __('Add social media profiles for your recruitment agency.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 30,
    ));

    // Enable Social Links
    $wp_customize->add_setting('recruitpro_footer_social_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_social_enable', array(
        'label' => __('Enable Social Media Links', 'recruitpro'),
        'section' => 'recruitpro_footer_social_links',
        'type' => 'checkbox',
    ));

    // Social Media Platforms
    $social_platforms = array(
        'linkedin' => array('LinkedIn', 'Professional networking'),
        'facebook' => array('Facebook', 'Company page'),
        'twitter' => array('Twitter/X', 'Company updates'),
        'instagram' => array('Instagram', 'Company culture'),
        'youtube' => array('YouTube', 'Company videos'),
        'glassdoor' => array('Glassdoor', 'Company reviews'),
        'indeed' => array('Indeed', 'Job postings'),
        'xing' => array('XING', 'Professional networking (Europe)'),
        'tiktok' => array('TikTok', 'Company culture videos'),
        'pinterest' => array('Pinterest', 'Career inspiration'),
    );

    foreach ($social_platforms as $platform => $details) {
        $wp_customize->add_setting("recruitpro_footer_social_{$platform}", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control("recruitpro_footer_social_{$platform}", array(
            'label' => $details[0] . ' URL',
            'description' => $details[1],
            'section' => 'recruitpro_footer_social_links',
            'type' => 'url',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_footer_social_enable', true);
            },
        ));
    }

    // Social Links Style
    $wp_customize->add_setting('recruitpro_footer_social_style', array(
        'default' => 'icons_with_text',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_social_style', array(
        'label' => __('Social Links Display Style', 'recruitpro'),
        'section' => 'recruitpro_footer_social_links',
        'type' => 'select',
        'choices' => array(
            'icons_only' => __('Icons Only', 'recruitpro'),
            'icons_with_text' => __('Icons with Text', 'recruitpro'),
            'text_only' => __('Text Only', 'recruitpro'),
        ),
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_social_enable', true);
        },
    ));

    // =================================================================
    // QUICK LINKS SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_quick_links', array(
        'title' => __('Quick Links', 'recruitpro'),
        'description' => __('Add important page links to the footer.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 40,
    ));

    // Enable Quick Links
    $wp_customize->add_setting('recruitpro_footer_quick_links_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_quick_links_enable', array(
        'label' => __('Enable Quick Links', 'recruitpro'),
        'section' => 'recruitpro_footer_quick_links',
        'type' => 'checkbox',
    ));

    // Quick Links Menu
    $wp_customize->add_setting('recruitpro_footer_quick_links_menu', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_footer_quick_links_menu', array(
        'label' => __('Quick Links Menu', 'recruitpro'),
        'description' => __('Select a menu to display as quick links.', 'recruitpro'),
        'section' => 'recruitpro_footer_quick_links',
        'type' => 'select',
        'choices' => recruitpro_get_menu_choices(),
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_quick_links_enable', true);
        },
    ));

    // Quick Links Title
    $wp_customize->add_setting('recruitpro_footer_quick_links_title', array(
        'default' => __('Quick Links', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_quick_links_title', array(
        'label' => __('Quick Links Section Title', 'recruitpro'),
        'section' => 'recruitpro_footer_quick_links',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_quick_links_enable', true);
        },
    ));

    // =================================================================
    // LEGAL & COMPLIANCE SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_legal', array(
        'title' => __('Legal & Compliance', 'recruitpro'),
        'description' => __('Configure legal information and compliance details for recruitment industry.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 50,
    ));

    // Copyright Text
    $wp_customize->add_setting('recruitpro_footer_copyright', array(
        'default' => sprintf(__('© %s %s. All rights reserved.', 'recruitpro'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_copyright', array(
        'label' => __('Copyright Text', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'textarea',
    ));

    // Recruitment License Number
    $wp_customize->add_setting('recruitpro_footer_license_number', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_license_number', array(
        'label' => __('Recruitment License Number', 'recruitpro'),
        'description' => __('Your recruitment agency license or registration number.', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'text',
    ));

    // Regulatory Body
    $wp_customize->add_setting('recruitpro_footer_regulatory_body', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_regulatory_body', array(
        'label' => __('Regulatory Body', 'recruitpro'),
        'description' => __('Name of the regulatory authority (e.g., REC, APSCo, etc.).', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'text',
    ));

    // GDPR Compliance Message
    $wp_customize->add_setting('recruitpro_footer_gdpr_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_gdpr_enable', array(
        'label' => __('Enable GDPR Compliance Notice', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'checkbox',
    ));

    // Privacy Policy Link
    $wp_customize->add_setting('recruitpro_footer_privacy_page', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_privacy_page', array(
        'label' => __('Privacy Policy Page', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'dropdown-pages',
    ));

    // Terms & Conditions Link
    $wp_customize->add_setting('recruitpro_footer_terms_page', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_terms_page', array(
        'label' => __('Terms & Conditions Page', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'dropdown-pages',
    ));

    // Cookie Policy Link
    $wp_customize->add_setting('recruitpro_footer_cookie_page', array(
        'default' => '',
        'sanitize_callback' => 'absint',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_cookie_page', array(
        'label' => __('Cookie Policy Page', 'recruitpro'),
        'section' => 'recruitpro_footer_legal',
        'type' => 'dropdown-pages',
    ));

    // =================================================================
    // NEWSLETTER SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_newsletter', array(
        'title' => __('Newsletter Signup', 'recruitpro'),
        'description' => __('Configure newsletter subscription form in footer.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 60,
    ));

    // Enable Newsletter
    $wp_customize->add_setting('recruitpro_footer_newsletter_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_newsletter_enable', array(
        'label' => __('Enable Newsletter Signup', 'recruitpro'),
        'section' => 'recruitpro_footer_newsletter',
        'type' => 'checkbox',
    ));

    // Newsletter Title
    $wp_customize->add_setting('recruitpro_footer_newsletter_title', array(
        'default' => __('Stay Updated', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_newsletter_title', array(
        'label' => __('Newsletter Section Title', 'recruitpro'),
        'section' => 'recruitpro_footer_newsletter',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_newsletter_enable', true);
        },
    ));

    // Newsletter Description
    $wp_customize->add_setting('recruitpro_footer_newsletter_description', array(
        'default' => __('Subscribe to our newsletter for the latest job opportunities and recruitment insights.', 'recruitpro'),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_newsletter_description', array(
        'label' => __('Newsletter Description', 'recruitpro'),
        'section' => 'recruitpro_footer_newsletter',
        'type' => 'textarea',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_newsletter_enable', true);
        },
    ));

    // Newsletter Shortcode
    $wp_customize->add_setting('recruitpro_footer_newsletter_shortcode', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_footer_newsletter_shortcode', array(
        'label' => __('Newsletter Form Shortcode', 'recruitpro'),
        'description' => __('Enter the shortcode for your newsletter form (e.g., [newsletter_form]).', 'recruitpro'),
        'section' => 'recruitpro_footer_newsletter',
        'type' => 'text',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_newsletter_enable', true);
        },
    ));

    // =================================================================
    // FOOTER WIDGETS SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_widgets', array(
        'title' => __('Footer Widgets', 'recruitpro'),
        'description' => __('Configure footer widget areas.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 70,
    ));

    // Enable Footer Widgets
    $wp_customize->add_setting('recruitpro_footer_widgets_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_footer_widgets_enable', array(
        'label' => __('Enable Footer Widget Areas', 'recruitpro'),
        'description' => __('Show widget areas in footer for additional content.', 'recruitpro'),
        'section' => 'recruitpro_footer_widgets',
        'type' => 'checkbox',
    ));

    // Number of Widget Areas
    $wp_customize->add_setting('recruitpro_footer_widget_columns', array(
        'default' => 4,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recruitpro_footer_widget_columns', array(
        'label' => __('Number of Widget Columns', 'recruitpro'),
        'section' => 'recruitpro_footer_widgets',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 6,
            'step' => 1,
        ),
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_widgets_enable', true);
        },
    ));

    // =================================================================
    // FOOTER BOTTOM SECTION
    // =================================================================
    
    $wp_customize->add_section('recruitpro_footer_bottom', array(
        'title' => __('Footer Bottom Bar', 'recruitpro'),
        'description' => __('Configure the bottom footer bar with copyright and additional links.', 'recruitpro'),
        'panel' => 'recruitpro_footer_panel',
        'priority' => 80,
    ));

    // Enable Footer Bottom
    $wp_customize->add_setting('recruitpro_footer_bottom_enable', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_bottom_enable', array(
        'label' => __('Enable Footer Bottom Bar', 'recruitpro'),
        'section' => 'recruitpro_footer_bottom',
        'type' => 'checkbox',
    ));

    // Footer Bottom Layout
    $wp_customize->add_setting('recruitpro_footer_bottom_layout', array(
        'default' => 'split',
        'sanitize_callback' => 'recruitpro_sanitize_select',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_bottom_layout', array(
        'label' => __('Bottom Bar Layout', 'recruitpro'),
        'section' => 'recruitpro_footer_bottom',
        'type' => 'select',
        'choices' => array(
            'left' => __('Copyright Left', 'recruitpro'),
            'center' => __('Copyright Center', 'recruitpro'),
            'right' => __('Copyright Right', 'recruitpro'),
            'split' => __('Copyright Left, Links Right', 'recruitpro'),
        ),
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_bottom_enable', true);
        },
    ));

    // Additional Footer Text
    $wp_customize->add_setting('recruitpro_footer_additional_text', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('recruitpro_footer_additional_text', array(
        'label' => __('Additional Footer Text', 'recruitpro'),
        'description' => __('Extra text to display in footer bottom (e.g., "Powered by...", certifications).', 'recruitpro'),
        'section' => 'recruitpro_footer_bottom',
        'type' => 'textarea',
        'active_callback' => function() {
            return get_theme_mod('recruitpro_footer_bottom_enable', true);
        },
    ));
}

/**
 * Get available menu choices for customizer
 * 
 * @since 1.0.0
 * @return array Menu choices
 */
function recruitpro_get_menu_choices() {
    $menus = wp_get_nav_menus();
    $choices = array('' => __('Select a menu', 'recruitpro'));
    
    foreach ($menus as $menu) {
        $choices[$menu->term_id] = $menu->name;
    }
    
    return $choices;
}

/**
 * Sanitize select options
 * 
 * @since 1.0.0
 * @param string $input Input value
 * @param object $setting Customizer setting object
 * @return string Sanitized value
 */
function recruitpro_sanitize_select($input, $setting = null) {
    $input = sanitize_key($input);
    
    if ($setting) {
        $choices = $setting->manager->get_control($setting->id)->choices;
        return array_key_exists($input, $choices) ? $input : $setting->default;
    }
    
    return $input;
}

/**
 * Footer selective refresh
 * 
 * @since 1.0.0
 * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
 * @return void
 */
function recruitpro_footer_selective_refresh($wp_customize) {
    
    // Company name selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_footer_company_name', array(
        'selector' => '.footer-company-name',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_footer_company_name', get_bloginfo('name'));
        },
    ));
    
    // Company description selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_footer_company_description', array(
        'selector' => '.footer-company-description',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_footer_company_description', '');
        },
    ));
    
    // Copyright selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_footer_copyright', array(
        'selector' => '.footer-copyright',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_footer_copyright', '');
        },
    ));
    
    // Newsletter title selective refresh
    $wp_customize->selective_refresh->add_partial('recruitpro_footer_newsletter_title', array(
        'selector' => '.footer-newsletter-title',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_footer_newsletter_title', __('Stay Updated', 'recruitpro'));
        },
    ));
}

// Hook into customizer
add_action('customize_register', 'recruitpro_footer_customizer_options');
add_action('customize_register', 'recruitpro_footer_selective_refresh');

/**
 * Footer customizer CSS output
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_footer_customizer_css() {
    
    $footer_bg_color = get_theme_mod('recruitpro_footer_background_color', '#1e293b');
    $footer_text_color = get_theme_mod('recruitpro_footer_text_color', '#e2e8f0');
    
    $css = "
    .site-footer {
        background-color: {$footer_bg_color};
        color: {$footer_text_color};
    }
    
    .site-footer a {
        color: {$footer_text_color};
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }
    
    .site-footer a:hover {
        opacity: 1;
        color: {$footer_text_color};
    }
    
    .footer-widget-title {
        color: {$footer_text_color};
        font-weight: 600;
    }
    ";
    
    wp_add_inline_style('recruitpro-main-style', $css);
}

add_action('wp_enqueue_scripts', 'recruitpro_footer_customizer_css');

/**
 * Register footer widget areas
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_footer_widgets_init() {
    
    $widget_columns = get_theme_mod('recruitpro_footer_widget_columns', 4);
    $widgets_enabled = get_theme_mod('recruitpro_footer_widgets_enable', true);
    
    if (!$widgets_enabled) {
        return;
    }
    
    for ($i = 1; $i <= $widget_columns; $i++) {
        register_sidebar(array(
            'name' => sprintf(__('Footer Widget Area %d', 'recruitpro'), $i),
            'id' => "footer-widget-{$i}",
            'description' => sprintf(__('Widget area %d in the footer section.', 'recruitpro'), $i),
            'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title footer-widget-title">',
            'after_title' => '</h3>',
        ));
    }
}

add_action('widgets_init', 'recruitpro_footer_widgets_init');

?>