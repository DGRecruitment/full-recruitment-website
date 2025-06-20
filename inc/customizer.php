<?php
/**
 * RecruitPro Theme Customizer
 *
 * WordPress Customizer integration with recruitment-focused settings
 * Provides professional customization options for recruitment agencies
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add postMessage support for site title and description for the Theme Customizer
 */
function recruitpro_customize_register($wp_customize) {
    // Remove default sections we'll replace
    $wp_customize->remove_section('colors');
    
    // Enhance default sections
    recruitpro_enhance_default_sections($wp_customize);
    
    // Add custom sections
    recruitpro_add_site_identity_settings($wp_customize);
    recruitpro_add_header_settings($wp_customize);
    recruitpro_add_homepage_settings($wp_customize);
    recruitpro_add_typography_settings($wp_customize);
    recruitpro_add_layout_settings($wp_customize);
    recruitpro_add_footer_settings($wp_customize);
    recruitpro_add_contact_settings($wp_customize);
    recruitpro_add_social_media_settings($wp_customize);
    recruitpro_add_seo_settings($wp_customize);
    recruitpro_add_performance_settings($wp_customize);
    recruitpro_add_advanced_settings($wp_customize);
    
    // Add live preview scripts
    recruitpro_add_customizer_scripts($wp_customize);
}
add_action('customize_register', 'recruitpro_customize_register');

/**
 * Enhance default WordPress Customizer sections
 */
function recruitpro_enhance_default_sections($wp_customize) {
    // Site Title & Tagline enhancements
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    
    // Header text color
    if (isset($wp_customize->get_setting('header_textcolor')->default)) {
        $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';
    }
    
    // Custom site identity section title
    $wp_customize->get_section('title_tagline')->title = esc_html__('Site Identity & Branding', 'recruitpro');
    $wp_customize->get_section('title_tagline')->priority = 20;
}

/**
 * Add enhanced site identity settings
 */
function recruitpro_add_site_identity_settings($wp_customize) {
    // Company tagline (different from site tagline)
    $wp_customize->add_setting('recruitpro_company_tagline', array(
        'default'           => esc_html__('Your recruitment partner', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_company_tagline', array(
        'label'   => esc_html__('Company Tagline', 'recruitpro'),
        'section' => 'title_tagline',
        'type'    => 'text',
        'description' => esc_html__('Professional tagline for your recruitment agency', 'recruitpro'),
    ));
    
    // Company established year
    $wp_customize->add_setting('recruitpro_established_year', array(
        'default'           => date('Y'),
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('recruitpro_established_year', array(
        'label'   => esc_html__('Established Year', 'recruitpro'),
        'section' => 'title_tagline',
        'type'    => 'number',
        'input_attrs' => array(
            'min' => 1900,
            'max' => date('Y'),
        ),
    ));
    
    // Company registration number
    $wp_customize->add_setting('recruitpro_company_registration', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_company_registration', array(
        'label'   => esc_html__('Company Registration Number', 'recruitpro'),
        'section' => 'title_tagline',
        'type'    => 'text',
    ));
    
    // Professional license number
    $wp_customize->add_setting('recruitpro_license_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_license_number', array(
        'label'   => esc_html__('Recruitment License Number', 'recruitpro'),
        'section' => 'title_tagline',
        'type'    => 'text',
        'description' => esc_html__('Professional recruitment license (if applicable)', 'recruitpro'),
    ));
}

/**
 * Add header settings
 */
function recruitpro_add_header_settings($wp_customize) {
    // Header section
    $wp_customize->add_section('recruitpro_header', array(
        'title'    => esc_html__('Header Settings', 'recruitpro'),
        'priority' => 30,
    ));
    
    // Header layout
    $wp_customize->add_setting('recruitpro_header_layout', array(
        'default'           => 'standard',
        'sanitize_callback' => 'recruitpro_sanitize_header_layout',
    ));
    
    $wp_customize->add_control('recruitpro_header_layout', array(
        'label'   => esc_html__('Header Layout', 'recruitpro'),
        'section' => 'recruitpro_header',
        'type'    => 'select',
        'choices' => array(
            'standard'  => esc_html__('Standard', 'recruitpro'),
            'centered'  => esc_html__('Centered Logo', 'recruitpro'),
            'split'     => esc_html__('Split Navigation', 'recruitpro'),
            'minimal'   => esc_html__('Minimal', 'recruitpro'),
        ),
    ));
    
    // Sticky header
    $wp_customize->add_setting('recruitpro_sticky_header', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_sticky_header', array(
        'label'   => esc_html__('Sticky Header', 'recruitpro'),
        'section' => 'recruitpro_header',
        'type'    => 'checkbox',
    ));
    
    // Header contact info
    $wp_customize->add_setting('recruitpro_header_show_contact', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_header_show_contact', array(
        'label'   => esc_html__('Show Contact Info in Header', 'recruitpro'),
        'section' => 'recruitpro_header',
        'type'    => 'checkbox',
    ));
    
    // Header CTA button
    $wp_customize->add_setting('recruitpro_header_cta_text', array(
        'default'           => esc_html__('Get Started', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_header_cta_text', array(
        'label'   => esc_html__('Header CTA Button Text', 'recruitpro'),
        'section' => 'recruitpro_header',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('recruitpro_header_cta_url', array(
        'default'           => '#contact',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('recruitpro_header_cta_url', array(
        'label'   => esc_html__('Header CTA Button URL', 'recruitpro'),
        'section' => 'recruitpro_header',
        'type'    => 'url',
    ));
    
    // Search in header
    $wp_customize->add_setting('recruitpro_header_search', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_header_search', array(
        'label'   => esc_html__('Show Search in Header', 'recruitpro'),
        'section' => 'recruitpro_header',
        'type'    => 'checkbox',
    ));
}

/**
 * Add homepage settings
 */
function recruitpro_add_homepage_settings($wp_customize) {
    // Homepage section
    $wp_customize->add_section('recruitpro_homepage', array(
        'title'    => esc_html__('Homepage Settings', 'recruitpro'),
        'priority' => 35,
    ));
    
    // Hero section
    $wp_customize->add_setting('recruitpro_enable_hero', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_hero', array(
        'label'   => esc_html__('Enable Hero Section', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'checkbox',
    ));
    
    // Hero title
    $wp_customize->add_setting('recruitpro_hero_title', array(
        'default'           => esc_html__('Find Your Perfect Career Match', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_hero_title', array(
        'label'   => esc_html__('Hero Title', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'text',
    ));
    
    // Hero subtitle
    $wp_customize->add_setting('recruitpro_hero_subtitle', array(
        'default'           => esc_html__('Professional recruitment services connecting top talent with leading companies', 'recruitpro'),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_hero_subtitle', array(
        'label'   => esc_html__('Hero Subtitle', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'textarea',
    ));
    
    // Hero background image
    $wp_customize->add_setting('recruitpro_hero_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'recruitpro_hero_background', array(
        'label'   => esc_html__('Hero Background Image', 'recruitpro'),
        'section' => 'recruitpro_homepage',
    )));
    
    // Hero CTA buttons
    $wp_customize->add_setting('recruitpro_hero_cta_primary_text', array(
        'default'           => esc_html__('Find Jobs', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_hero_cta_primary_text', array(
        'label'   => esc_html__('Primary CTA Text', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('recruitpro_hero_cta_primary_url', array(
        'default'           => '/jobs/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('recruitpro_hero_cta_primary_url', array(
        'label'   => esc_html__('Primary CTA URL', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'url',
    ));
    
    $wp_customize->add_setting('recruitpro_hero_cta_secondary_text', array(
        'default'           => esc_html__('Post a Job', 'recruitpro'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_hero_cta_secondary_text', array(
        'label'   => esc_html__('Secondary CTA Text', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('recruitpro_hero_cta_secondary_url', array(
        'default'           => '/contact/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('recruitpro_hero_cta_secondary_url', array(
        'label'   => esc_html__('Secondary CTA URL', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'url',
    ));
    
    // Features section
    $wp_customize->add_setting('recruitpro_enable_features', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_features', array(
        'label'   => esc_html__('Enable Features Section', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'checkbox',
    ));
    
    // Statistics section
    $wp_customize->add_setting('recruitpro_enable_stats', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_stats', array(
        'label'   => esc_html__('Enable Statistics Section', 'recruitpro'),
        'section' => 'recruitpro_homepage',
        'type'    => 'checkbox',
    ));
    
    // Stats data
    $stats = array(
        'jobs_placed' => array('label' => esc_html__('Jobs Placed', 'recruitpro'), 'default' => '500+'),
        'companies' => array('label' => esc_html__('Partner Companies', 'recruitpro'), 'default' => '100+'),
        'candidates' => array('label' => esc_html__('Registered Candidates', 'recruitpro'), 'default' => '5000+'),
        'success_rate' => array('label' => esc_html__('Success Rate', 'recruitpro'), 'default' => '95%'),
    );
    
    foreach ($stats as $stat_key => $stat_data) {
        $wp_customize->add_setting('recruitpro_stat_' . $stat_key, array(
            'default'           => $stat_data['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        ));
        
        $wp_customize->add_control('recruitpro_stat_' . $stat_key, array(
            'label'   => $stat_data['label'],
            'section' => 'recruitpro_homepage',
            'type'    => 'text',
        ));
    }
}

/**
 * Add typography settings
 */
function recruitpro_add_typography_settings($wp_customize) {
    // Typography section
    $wp_customize->add_section('recruitpro_typography', array(
        'title'    => esc_html__('Typography', 'recruitpro'),
        'priority' => 50,
    ));
    
    // Google Fonts
    $wp_customize->add_setting('recruitpro_google_fonts', array(
        'default'           => 'Open Sans:400,600,700',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_google_fonts', array(
        'label'   => esc_html__('Google Fonts', 'recruitpro'),
        'section' => 'recruitpro_typography',
        'type'    => 'select',
        'choices' => recruitpro_get_google_fonts_choices(),
    ));
    
    // Heading font
    $wp_customize->add_setting('recruitpro_heading_font', array(
        'default'           => 'Roboto',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_heading_font', array(
        'label'   => esc_html__('Heading Font', 'recruitpro'),
        'section' => 'recruitpro_typography',
        'type'    => 'select',
        'choices' => recruitpro_get_font_choices(),
    ));
    
    // Body font
    $wp_customize->add_setting('recruitpro_body_font', array(
        'default'           => 'Open Sans',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_body_font', array(
        'label'   => esc_html__('Body Font', 'recruitpro'),
        'section' => 'recruitpro_typography',
        'type'    => 'select',
        'choices' => recruitpro_get_font_choices(),
    ));
    
    // Font sizes
    $font_sizes = array(
        'heading_size' => array('label' => esc_html__('Heading Size', 'recruitpro'), 'default' => 36),
        'body_size' => array('label' => esc_html__('Body Size', 'recruitpro'), 'default' => 16),
        'menu_size' => array('label' => esc_html__('Menu Size', 'recruitpro'), 'default' => 16),
    );
    
    foreach ($font_sizes as $size_key => $size_data) {
        $wp_customize->add_setting('recruitpro_' . $size_key, array(
            'default'           => $size_data['default'],
            'sanitize_callback' => 'absint',
        ));
        
        $wp_customize->add_control('recruitpro_' . $size_key, array(
            'label'       => $size_data['label'],
            'section'     => 'recruitpro_typography',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 12,
                'max'  => 48,
                'step' => 1,
            ),
        ));
    }
}

/**
 * Add layout settings
 */
function recruitpro_add_layout_settings($wp_customize) {
    // Layout section
    $wp_customize->add_section('recruitpro_layout', array(
        'title'    => esc_html__('Layout Settings', 'recruitpro'),
        'priority' => 60,
    ));
    
    // Site layout
    $wp_customize->add_setting('recruitpro_site_layout', array(
        'default'           => 'fullwidth',
        'sanitize_callback' => 'recruitpro_sanitize_layout',
    ));
    
    $wp_customize->add_control('recruitpro_site_layout', array(
        'label'   => esc_html__('Site Layout', 'recruitpro'),
        'section' => 'recruitpro_layout',
        'type'    => 'select',
        'choices' => array(
            'fullwidth' => esc_html__('Full Width', 'recruitpro'),
            'boxed'     => esc_html__('Boxed', 'recruitpro'),
            'framed'    => esc_html__('Framed', 'recruitpro'),
        ),
    ));
    
    // Container width
    $wp_customize->add_setting('recruitpro_container_width', array(
        'default'           => 1200,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('recruitpro_container_width', array(
        'label'       => esc_html__('Container Width (px)', 'recruitpro'),
        'section'     => 'recruitpro_layout',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 1000,
            'max'  => 1400,
            'step' => 10,
        ),
    ));
    
    // Sidebar settings
    $wp_customize->add_setting('recruitpro_sidebar_width', array(
        'default'           => 25,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('recruitpro_sidebar_width', array(
        'label'       => esc_html__('Sidebar Width (%)', 'recruitpro'),
        'section'     => 'recruitpro_layout',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 20,
            'max'  => 35,
            'step' => 1,
        ),
    ));
    
    // Default page layout
    $wp_customize->add_setting('recruitpro_default_layout', array(
        'default'           => 'right-sidebar',
        'sanitize_callback' => 'recruitpro_sanitize_page_layout',
    ));
    
    $wp_customize->add_control('recruitpro_default_layout', array(
        'label'   => esc_html__('Default Page Layout', 'recruitpro'),
        'section' => 'recruitpro_layout',
        'type'    => 'select',
        'choices' => array(
            'no-sidebar'    => esc_html__('No Sidebar', 'recruitpro'),
            'left-sidebar'  => esc_html__('Left Sidebar', 'recruitpro'),
            'right-sidebar' => esc_html__('Right Sidebar', 'recruitpro'),
        ),
    ));
}

/**
 * Add footer settings
 */
function recruitpro_add_footer_settings($wp_customize) {
    // Footer section
    $wp_customize->add_section('recruitpro_footer', array(
        'title'    => esc_html__('Footer Settings', 'recruitpro'),
        'priority' => 70,
    ));
    
    // Footer layout
    $wp_customize->add_setting('recruitpro_footer_layout', array(
        'default'           => '4-columns',
        'sanitize_callback' => 'recruitpro_sanitize_footer_layout',
    ));
    
    $wp_customize->add_control('recruitpro_footer_layout', array(
        'label'   => esc_html__('Footer Layout', 'recruitpro'),
        'section' => 'recruitpro_footer',
        'type'    => 'select',
        'choices' => array(
            '1-column'  => esc_html__('1 Column', 'recruitpro'),
            '2-columns' => esc_html__('2 Columns', 'recruitpro'),
            '3-columns' => esc_html__('3 Columns', 'recruitpro'),
            '4-columns' => esc_html__('4 Columns', 'recruitpro'),
        ),
    ));
    
    // Footer copyright
    $wp_customize->add_setting('recruitpro_footer_copyright', array(
        'default'           => sprintf(esc_html__('© %s %s. All rights reserved.', 'recruitpro'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_footer_copyright', array(
        'label'   => esc_html__('Copyright Text', 'recruitpro'),
        'section' => 'recruitpro_footer',
        'type'    => 'textarea',
    ));
    
    // Footer additional text
    $wp_customize->add_setting('recruitpro_footer_additional_text', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'postMessage',
    ));
    
    $wp_customize->add_control('recruitpro_footer_additional_text', array(
        'label'   => esc_html__('Additional Footer Text', 'recruitpro'),
        'section' => 'recruitpro_footer',
        'type'    => 'textarea',
        'description' => esc_html__('License numbers, certifications, etc.', 'recruitpro'),
    ));
    
    // Back to top button
    $wp_customize->add_setting('recruitpro_back_to_top', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_back_to_top', array(
        'label'   => esc_html__('Show Back to Top Button', 'recruitpro'),
        'section' => 'recruitpro_footer',
        'type'    => 'checkbox',
    ));
}

/**
 * Add contact information settings
 */
function recruitpro_add_contact_settings($wp_customize) {
    // Contact Information section
    $wp_customize->add_section('recruitpro_contact_info', array(
        'title'    => esc_html__('Contact Information', 'recruitpro'),
        'priority' => 80,
    ));
    
    // Contact details
    $contact_fields = array(
        'phone' => array(
            'label' => esc_html__('Phone Number', 'recruitpro'),
            'type' => 'tel',
            'default' => '',
        ),
        'email' => array(
            'label' => esc_html__('Email Address', 'recruitpro'),
            'type' => 'email',
            'default' => get_option('admin_email'),
        ),
        'address' => array(
            'label' => esc_html__('Office Address', 'recruitpro'),
            'type' => 'textarea',
            'default' => '',
        ),
        'office_hours' => array(
            'label' => esc_html__('Office Hours', 'recruitpro'),
            'type' => 'text',
            'default' => esc_html__('Mon-Fri: 9:00 AM - 6:00 PM', 'recruitpro'),
        ),
    );
    
    foreach ($contact_fields as $field_key => $field_data) {
        $wp_customize->add_setting('recruitpro_contact_' . $field_key, array(
            'default'           => $field_data['default'],
            'sanitize_callback' => $field_data['type'] === 'textarea' ? 'sanitize_textarea_field' : 
                                  ($field_data['type'] === 'email' ? 'sanitize_email' : 'sanitize_text_field'),
            'transport'         => 'postMessage',
        ));
        
        $wp_customize->add_control('recruitpro_contact_' . $field_key, array(
            'label'   => $field_data['label'],
            'section' => 'recruitpro_contact_info',
            'type'    => $field_data['type'],
        ));
    }
    
    // Emergency contact
    $wp_customize->add_setting('recruitpro_emergency_contact', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_emergency_contact', array(
        'label'   => esc_html__('Emergency/After Hours Contact', 'recruitpro'),
        'section' => 'recruitpro_contact_info',
        'type'    => 'tel',
    ));
}

/**
 * Add social media settings
 */
function recruitpro_add_social_media_settings($wp_customize) {
    // Social Media section
    $wp_customize->add_section('recruitpro_social_media', array(
        'title'    => esc_html__('Social Media', 'recruitpro'),
        'priority' => 90,
    ));
    
    // Social media platforms
    $social_platforms = array(
        'linkedin' => esc_html__('LinkedIn', 'recruitpro'),
        'facebook' => esc_html__('Facebook', 'recruitpro'),
        'twitter' => esc_html__('Twitter (X)', 'recruitpro'),
        'instagram' => esc_html__('Instagram', 'recruitpro'),
        'youtube' => esc_html__('YouTube', 'recruitpro'),
        'glassdoor' => esc_html__('Glassdoor', 'recruitpro'),
        'indeed' => esc_html__('Indeed', 'recruitpro'),
    );
    
    foreach ($social_platforms as $platform => $label) {
        $wp_customize->add_setting('recruitpro_social_' . $platform, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control('recruitpro_social_' . $platform, array(
            'label'   => $label . ' ' . esc_html__('URL', 'recruitpro'),
            'section' => 'recruitpro_social_media',
            'type'    => 'url',
        ));
    }
    
    // Social media display options
    $wp_customize->add_setting('recruitpro_social_open_new_tab', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_social_open_new_tab', array(
        'label'   => esc_html__('Open Social Links in New Tab', 'recruitpro'),
        'section' => 'recruitpro_social_media',
        'type'    => 'checkbox',
    ));
}

/**
 * Add SEO settings
 */
function recruitpro_add_seo_settings($wp_customize) {
    // SEO section
    $wp_customize->add_section('recruitpro_seo', array(
        'title'    => esc_html__('SEO Settings', 'recruitpro'),
        'priority' => 100,
    ));
    
    // Schema markup
    $wp_customize->add_setting('recruitpro_enable_schema', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_schema', array(
        'label'   => esc_html__('Enable Schema Markup', 'recruitpro'),
        'section' => 'recruitpro_seo',
        'type'    => 'checkbox',
    ));
    
    // Breadcrumbs
    $wp_customize->add_setting('recruitpro_enable_breadcrumbs', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_breadcrumbs', array(
        'label'   => esc_html__('Enable Breadcrumbs', 'recruitpro'),
        'section' => 'recruitpro_seo',
        'type'    => 'checkbox',
    ));
    
    // Meta descriptions
    $wp_customize->add_setting('recruitpro_meta_description', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('recruitpro_meta_description', array(
        'label'       => esc_html__('Default Meta Description', 'recruitpro'),
        'section'     => 'recruitpro_seo',
        'type'        => 'textarea',
        'description' => esc_html__('Used when no specific meta description is set', 'recruitpro'),
    ));
}

/**
 * Add performance settings
 */
function recruitpro_add_performance_settings($wp_customize) {
    // Performance section
    $wp_customize->add_section('recruitpro_performance', array(
        'title'    => esc_html__('Performance', 'recruitpro'),
        'priority' => 110,
    ));
    
    // Preload fonts
    $wp_customize->add_setting('recruitpro_preload_fonts', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_preload_fonts', array(
        'label'   => esc_html__('Preload Web Fonts', 'recruitpro'),
        'section' => 'recruitpro_performance',
        'type'    => 'checkbox',
    ));
    
    // Optimize images
    $wp_customize->add_setting('recruitpro_optimize_images', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_optimize_images', array(
        'label'   => esc_html__('Optimize Images', 'recruitpro'),
        'section' => 'recruitpro_performance',
        'type'    => 'checkbox',
    ));
    
    // Smooth scrolling
    $wp_customize->add_setting('recruitpro_smooth_scrolling', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_smooth_scrolling', array(
        'label'   => esc_html__('Enable Smooth Scrolling', 'recruitpro'),
        'section' => 'recruitpro_performance',
        'type'    => 'checkbox',
    ));
}

/**
 * Add advanced settings
 */
function recruitpro_add_advanced_settings($wp_customize) {
    // Advanced section
    $wp_customize->add_section('recruitpro_advanced', array(
        'title'    => esc_html__('Advanced Settings', 'recruitpro'),
        'priority' => 120,
    ));
    
    // Custom CSS
    $wp_customize->add_setting('recruitpro_custom_css', array(
        'default'           => '',
        'sanitize_callback' => 'wp_strip_all_tags',
    ));
    
    $wp_customize->add_control('recruitpro_custom_css', array(
        'label'       => esc_html__('Custom CSS', 'recruitpro'),
        'section'     => 'recruitpro_advanced',
        'type'        => 'textarea',
        'description' => esc_html__('Additional CSS rules will be added to the theme', 'recruitpro'),
    ));
    
    // Custom JavaScript
    $wp_customize->add_setting('recruitpro_custom_js', array(
        'default'           => '',
        'sanitize_callback' => 'wp_strip_all_tags',
    ));
    
    $wp_customize->add_control('recruitpro_custom_js', array(
        'label'       => esc_html__('Custom JavaScript', 'recruitpro'),
        'section'     => 'recruitpro_advanced',
        'type'        => 'textarea',
        'description' => esc_html__('Custom JavaScript will be added to the footer', 'recruitpro'),
    ));
    
    // Google Analytics
    $wp_customize->add_setting('recruitpro_google_analytics', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_google_analytics', array(
        'label'       => esc_html__('Google Analytics ID', 'recruitpro'),
        'section'     => 'recruitpro_advanced',
        'type'        => 'text',
        'description' => esc_html__('Enter your Google Analytics tracking ID (e.g., G-XXXXXXXXXX)', 'recruitpro'),
    ));
    
    // Facebook Pixel
    $wp_customize->add_setting('recruitpro_facebook_pixel', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('recruitpro_facebook_pixel', array(
        'label'       => esc_html__('Facebook Pixel ID', 'recruitpro'),
        'section'     => 'recruitpro_advanced',
        'type'        => 'text',
        'description' => esc_html__('Enter your Facebook Pixel ID for conversion tracking', 'recruitpro'),
    ));
}

/**
 * Add customizer preview scripts
 */
function recruitpro_add_customizer_scripts($wp_customize) {
    // Add live preview for postMessage settings
    $wp_customize->add_setting('recruitpro_customizer_css', array(
        'default' => '',
        'sanitize_callback' => 'wp_strip_all_tags',
        'transport' => 'postMessage',
    ));
}

/**
 * Sanitization functions
 */
function recruitpro_sanitize_header_layout($input) {
    $valid = array('standard', 'centered', 'split', 'minimal');
    return in_array($input, $valid) ? $input : 'standard';
}

function recruitpro_sanitize_layout($input) {
    $valid = array('fullwidth', 'boxed', 'framed');
    return in_array($input, $valid) ? $input : 'fullwidth';
}

function recruitpro_sanitize_page_layout($input) {
    $valid = array('no-sidebar', 'left-sidebar', 'right-sidebar');
    return in_array($input, $valid) ? $input : 'right-sidebar';
}

function recruitpro_sanitize_footer_layout($input) {
    $valid = array('1-column', '2-columns', '3-columns', '4-columns');
    return in_array($input, $valid) ? $input : '4-columns';
}

/**
 * Get Google Fonts choices
 */
function recruitpro_get_google_fonts_choices() {
    return array(
        'Open Sans:400,600,700' => 'Open Sans',
        'Roboto:400,500,700' => 'Roboto',
        'Lato:400,700' => 'Lato',
        'Montserrat:400,500,600,700' => 'Montserrat',
        'Source Sans Pro:400,600,700' => 'Source Sans Pro',
        'Poppins:400,500,600,700' => 'Poppins',
        'Nunito:400,600,700' => 'Nunito',
        'Inter:400,500,600,700' => 'Inter',
    );
}

/**
 * Get font choices
 */
function recruitpro_get_font_choices() {
    return array(
        'Open Sans' => 'Open Sans',
        'Roboto' => 'Roboto',
        'Lato' => 'Lato',
        'Montserrat' => 'Montserrat',
        'Source Sans Pro' => 'Source Sans Pro',
        'Poppins' => 'Poppins',
        'Nunito' => 'Nunito',
        'Inter' => 'Inter',
        'Arial' => 'Arial',
        'Helvetica' => 'Helvetica',
        'Georgia' => 'Georgia',
        'Times' => 'Times',
    );
}

/**
 * Enqueue customizer script
 */
function recruitpro_customize_preview_js() {
    wp_enqueue_script('recruitpro-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), wp_get_theme()->get('Version'), true);
}
add_action('customize_preview_init', 'recruitpro_customize_preview_js');

/**
 * Output custom CSS from customizer
 */
function recruitpro_customizer_css() {
    ?>
    <style type="text/css" id="recruitpro-customizer-css">
    <?php
    // Container width
    $container_width = get_theme_mod('recruitpro_container_width', 1200);
    ?>
    .container, .site-content {
        max-width: <?php echo absint($container_width); ?>px;
    }
    
    <?php
    // Typography
    $heading_font = get_theme_mod('recruitpro_heading_font', 'Roboto');
    $body_font = get_theme_mod('recruitpro_body_font', 'Open Sans');
    $heading_size = get_theme_mod('recruitpro_heading_size', 36);
    $body_size = get_theme_mod('recruitpro_body_size', 16);
    ?>
    h1, h2, h3, h4, h5, h6 {
        font-family: <?php echo esc_html($heading_font); ?>, sans-serif;
    }
    
    body {
        font-family: <?php echo esc_html($body_font); ?>, sans-serif;
        font-size: <?php echo absint($body_size); ?>px;
    }
    
    h1 {
        font-size: <?php echo absint($heading_size); ?>px;
    }
    
    <?php
    // Sidebar width
    $sidebar_width = get_theme_mod('recruitpro_sidebar_width', 25);
    $content_width = 100 - $sidebar_width;
    ?>
    .has-sidebar .site-main {
        width: <?php echo absint($content_width); ?>%;
    }
    
    .sidebar {
        width: <?php echo absint($sidebar_width); ?>%;
    }
    
    <?php
    // Custom CSS
    $custom_css = get_theme_mod('recruitpro_custom_css', '');
    if (!empty($custom_css)) {
        echo wp_strip_all_tags($custom_css);
    }
    ?>
    </style>
    <?php
}
add_action('wp_head', 'recruitpro_customizer_css');

/**
 * Output custom JavaScript from customizer
 */
function recruitpro_customizer_js() {
    $custom_js = get_theme_mod('recruitpro_custom_js', '');
    $google_analytics = get_theme_mod('recruitpro_google_analytics', '');
    $facebook_pixel = get_theme_mod('recruitpro_facebook_pixel', '');
    
    if (!empty($custom_js) || !empty($google_analytics) || !empty($facebook_pixel)) {
        ?>
        <script>
        <?php
        // Google Analytics
        if (!empty($google_analytics)) {
            ?>
            gtag('config', '<?php echo esc_js($google_analytics); ?>');
            <?php
        }
        
        // Facebook Pixel
        if (!empty($facebook_pixel)) {
            ?>
            fbq('init', '<?php echo esc_js($facebook_pixel); ?>');
            fbq('track', 'PageView');
            <?php
        }
        
        // Custom JavaScript
        if (!empty($custom_js)) {
            echo wp_strip_all_tags($custom_js);
        }
        ?>
        </script>
        <?php
    }
}
add_action('wp_footer', 'recruitpro_customizer_js');

/**
 * Add selective refresh support
 */
function recruitpro_customize_selective_refresh($wp_customize) {
    // Ensure selective refresh is available
    if (!isset($wp_customize->selective_refresh)) {
        return;
    }
    
    // Site title
    $wp_customize->selective_refresh->add_partial('blogname', array(
        'selector'        => '.site-title a',
        'render_callback' => function() {
            return get_bloginfo('name', 'display');
        },
    ));
    
    // Site tagline
    $wp_customize->selective_refresh->add_partial('blogdescription', array(
        'selector'        => '.site-description',
        'render_callback' => function() {
            return get_bloginfo('description', 'display');
        },
    ));
    
    // Company tagline
    $wp_customize->selective_refresh->add_partial('recruitpro_company_tagline', array(
        'selector'        => '.company-tagline',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_company_tagline', esc_html__('Your recruitment partner', 'recruitpro'));
        },
    ));
    
    // Hero title
    $wp_customize->selective_refresh->add_partial('recruitpro_hero_title', array(
        'selector'        => '.hero-title',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_hero_title', esc_html__('Find Your Perfect Career Match', 'recruitpro'));
        },
    ));
    
    // Footer copyright
    $wp_customize->selective_refresh->add_partial('recruitpro_footer_copyright', array(
        'selector'        => '.footer-copyright',
        'render_callback' => function() {
            return get_theme_mod('recruitpro_footer_copyright', sprintf(esc_html__('© %s %s. All rights reserved.', 'recruitpro'), date('Y'), get_bloginfo('name')));
        },
    ));
}
add_action('customize_register', 'recruitpro_customize_selective_refresh', 20);

/**
 * Customizer controls enhancements
 */
function recruitpro_customize_controls_enqueue_scripts() {
    wp_enqueue_script('recruitpro-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array('customize-controls'), wp_get_theme()->get('Version'), true);
    wp_enqueue_style('recruitpro-customize-controls', get_template_directory_uri() . '/assets/css/customize-controls.css', array(), wp_get_theme()->get('Version'));
}
add_action('customize_controls_enqueue_scripts', 'recruitpro_customize_controls_enqueue_scripts');