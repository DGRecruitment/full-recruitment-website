<?php
/**
 * RecruitPro Theme Layout Options
 *
 * This file handles layout customization options for the RecruitPro recruitment
 * website theme. It includes page layouts, sidebar configurations, content
 * width controls, grid systems, and responsive layout management specifically
 * designed for recruitment agencies and professional business websites.
 *
 * @package RecruitPro
 * @subpackage Theme/Layout
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/layout-options.php
 * Purpose: Layout customization and responsive design controls
 * Dependencies: WordPress Customizer API, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Layout Options Class
 * 
 * Handles all layout customization functionality including page layouts,
 * sidebar management, responsive design, and professional layouts.
 *
 * @since 1.0.0
 */
class RecruitPro_Layout_Options {

    /**
     * Available layout types
     * 
     * @since 1.0.0
     * @var array
     */
    private $layout_types = array();

    /**
     * Sidebar configurations
     * 
     * @since 1.0.0
     * @var array
     */
    private $sidebar_configs = array();

    /**
     * Content width settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $content_widths = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_layout_types();
        $this->init_sidebar_configs();
        $this->init_content_widths();
        
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_filter('body_class', array($this, 'add_layout_body_classes'));
        add_action('wp_head', array($this, 'output_layout_css'), 15);
        add_action('widgets_init', array($this, 'register_layout_sidebars'));
        add_filter('post_class', array($this, 'add_layout_post_classes'), 10, 3);
        add_action('recruitpro_layout_before_content', array($this, 'layout_content_wrapper_start'));
        add_action('recruitpro_layout_after_content', array($this, 'layout_content_wrapper_end'));
    }

    /**
     * Initialize available layout types
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_layout_types() {
        
        $this->layout_types = array(
            // Full width layouts
            'fullwidth' => array(
                'name' => __('Full Width', 'recruitpro'),
                'description' => __('Content spans the full width of the container', 'recruitpro'),
                'container_class' => 'layout-fullwidth',
                'content_class' => 'content-fullwidth',
                'has_sidebar' => false,
                'best_for' => array('landing-pages', 'hero-sections', 'portfolios'),
            ),
            'fullwidth_contained' => array(
                'name' => __('Full Width Contained', 'recruitpro'),
                'description' => __('Full width with maximum width constraint', 'recruitpro'),
                'container_class' => 'layout-fullwidth-contained',
                'content_class' => 'content-contained',
                'has_sidebar' => false,
                'best_for' => array('home-page', 'services', 'about'),
            ),
            
            // Sidebar layouts
            'sidebar_right' => array(
                'name' => __('Right Sidebar', 'recruitpro'),
                'description' => __('Main content with sidebar on the right', 'recruitpro'),
                'container_class' => 'layout-sidebar-right',
                'content_class' => 'content-with-sidebar',
                'sidebar_class' => 'sidebar-right',
                'has_sidebar' => true,
                'sidebar_position' => 'right',
                'content_width' => '70%',
                'sidebar_width' => '30%',
                'best_for' => array('blog', 'news', 'resources'),
            ),
            'sidebar_left' => array(
                'name' => __('Left Sidebar', 'recruitpro'),
                'description' => __('Main content with sidebar on the left', 'recruitpro'),
                'container_class' => 'layout-sidebar-left',
                'content_class' => 'content-with-sidebar',
                'sidebar_class' => 'sidebar-left',
                'has_sidebar' => true,
                'sidebar_position' => 'left',
                'content_width' => '70%',
                'sidebar_width' => '30%',
                'best_for' => array('documentation', 'guides'),
            ),
            'dual_sidebar' => array(
                'name' => __('Dual Sidebar', 'recruitpro'),
                'description' => __('Content with sidebars on both sides', 'recruitpro'),
                'container_class' => 'layout-dual-sidebar',
                'content_class' => 'content-with-dual-sidebar',
                'sidebar_class' => 'sidebar-dual',
                'has_sidebar' => true,
                'sidebar_position' => 'both',
                'content_width' => '60%',
                'sidebar_width' => '20%',
                'best_for' => array('complex-content', 'dashboards'),
            ),
            
            // Professional layouts
            'corporate' => array(
                'name' => __('Corporate Layout', 'recruitpro'),
                'description' => __('Professional layout for corporate pages', 'recruitpro'),
                'container_class' => 'layout-corporate',
                'content_class' => 'content-corporate',
                'has_sidebar' => false,
                'padding' => 'large',
                'best_for' => array('services', 'team', 'clients'),
            ),
            'executive' => array(
                'name' => __('Executive Layout', 'recruitpro'),
                'description' => __('Premium layout for executive content', 'recruitpro'),
                'container_class' => 'layout-executive',
                'content_class' => 'content-executive',
                'has_sidebar' => false,
                'padding' => 'extra-large',
                'max_width' => '800px',
                'best_for' => array('leadership', 'executive-search'),
            ),
            'recruitment_focused' => array(
                'name' => __('Recruitment Focused', 'recruitpro'),
                'description' => __('Optimized layout for recruitment content', 'recruitpro'),
                'container_class' => 'layout-recruitment',
                'content_class' => 'content-recruitment',
                'has_sidebar' => true,
                'sidebar_position' => 'right',
                'content_width' => '75%',
                'sidebar_width' => '25%',
                'best_for' => array('jobs', 'candidates', 'career-advice'),
            ),
            
            // Grid layouts
            'two_column' => array(
                'name' => __('Two Column Grid', 'recruitpro'),
                'description' => __('Equal width two-column layout', 'recruitpro'),
                'container_class' => 'layout-two-column',
                'content_class' => 'content-grid-2',
                'has_sidebar' => false,
                'grid_columns' => 2,
                'best_for' => array('services', 'features', 'team-grid'),
            ),
            'three_column' => array(
                'name' => __('Three Column Grid', 'recruitpro'),
                'description' => __('Equal width three-column layout', 'recruitpro'),
                'container_class' => 'layout-three-column',
                'content_class' => 'content-grid-3',
                'has_sidebar' => false,
                'grid_columns' => 3,
                'best_for' => array('services', 'testimonials', 'client-logos'),
            ),
            'four_column' => array(
                'name' => __('Four Column Grid', 'recruitpro'),
                'description' => __('Equal width four-column layout', 'recruitpro'),
                'container_class' => 'layout-four-column',
                'content_class' => 'content-grid-4',
                'has_sidebar' => false,
                'grid_columns' => 4,
                'best_for' => array('team-grid', 'statistics', 'icons'),
            ),
            
            // Specialized layouts
            'landing_page' => array(
                'name' => __('Landing Page', 'recruitpro'),
                'description' => __('Conversion-optimized landing page layout', 'recruitpro'),
                'container_class' => 'layout-landing',
                'content_class' => 'content-landing',
                'has_sidebar' => false,
                'remove_header_footer' => true,
                'best_for' => array('campaigns', 'lead-generation'),
            ),
            'minimal' => array(
                'name' => __('Minimal', 'recruitpro'),
                'description' => __('Clean, distraction-free layout', 'recruitpro'),
                'container_class' => 'layout-minimal',
                'content_class' => 'content-minimal',
                'has_sidebar' => false,
                'padding' => 'minimal',
                'max_width' => '700px',
                'best_for' => array('reading', 'privacy-policy', 'terms'),
            ),
        );
    }

    /**
     * Initialize sidebar configurations
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_sidebar_configs() {
        
        $this->sidebar_configs = array(
            'primary_sidebar' => array(
                'name' => __('Primary Sidebar', 'recruitpro'),
                'id' => 'primary-sidebar',
                'description' => __('Main sidebar area for blog and content pages', 'recruitpro'),
                'default_widgets' => array('search', 'recent_posts', 'categories'),
            ),
            'secondary_sidebar' => array(
                'name' => __('Secondary Sidebar', 'recruitpro'),
                'id' => 'secondary-sidebar',
                'description' => __('Secondary sidebar for dual sidebar layouts', 'recruitpro'),
                'default_widgets' => array('recent_comments', 'tag_cloud'),
            ),
            'recruitment_sidebar' => array(
                'name' => __('Recruitment Sidebar', 'recruitpro'),
                'id' => 'recruitment-sidebar',
                'description' => __('Specialized sidebar for recruitment content', 'recruitpro'),
                'default_widgets' => array('job_search', 'featured_jobs', 'recruitment_contact'),
            ),
            'page_sidebar' => array(
                'name' => __('Page Sidebar', 'recruitpro'),
                'id' => 'page-sidebar',
                'description' => __('Sidebar for static pages', 'recruitpro'),
                'default_widgets' => array('contact_info', 'social_links'),
            ),
        );
    }

    /**
     * Initialize content width settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_content_widths() {
        
        $this->content_widths = array(
            'narrow' => array(
                'name' => __('Narrow (800px)', 'recruitpro'),
                'width' => 800,
                'description' => __('Perfect for reading and text-heavy content', 'recruitpro'),
            ),
            'medium' => array(
                'name' => __('Medium (1000px)', 'recruitpro'),
                'width' => 1000,
                'description' => __('Balanced layout for most content types', 'recruitpro'),
            ),
            'wide' => array(
                'name' => __('Wide (1200px)', 'recruitpro'),
                'width' => 1200,
                'description' => __('Standard width for professional websites', 'recruitpro'),
            ),
            'extra_wide' => array(
                'name' => __('Extra Wide (1400px)', 'recruitpro'),
                'width' => 1400,
                'description' => __('Maximum width for large screens', 'recruitpro'),
            ),
            'full' => array(
                'name' => __('Full Width (100%)', 'recruitpro'),
                'width' => '100%',
                'description' => __('Spans the entire viewport width', 'recruitpro'),
            ),
        );
    }

    /**
     * Add layout customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {

        // =================================================================
        // LAYOUT PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_layout_panel', array(
            'title' => __('Layout Options', 'recruitpro'),
            'description' => __('Configure page layouts, sidebars, content width, and responsive design settings for your recruitment website.', 'recruitpro'),
            'priority' => 115,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // GENERAL LAYOUT SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_general_layout', array(
            'title' => __('General Layout', 'recruitpro'),
            'description' => __('Configure the overall site layout and container settings.', 'recruitpro'),
            'panel' => 'recruitpro_layout_panel',
            'priority' => 10,
        ));

        // Site Layout
        $wp_customize->add_setting('recruitpro_site_layout', array(
            'default' => 'wide',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_site_layout', array(
            'label' => __('Site Layout', 'recruitpro'),
            'description' => __('Choose the overall layout style for your recruitment website.', 'recruitpro'),
            'section' => 'recruitpro_general_layout',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // Content Width
        $wp_customize->add_setting('recruitpro_content_width', array(
            'default' => 'wide',
            'sanitize_callback' => array($this, 'sanitize_content_width'),
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_content_width', array(
            'label' => __('Content Width', 'recruitpro'),
            'description' => __('Set the maximum width for your content area.', 'recruitpro'),
            'section' => 'recruitpro_general_layout',
            'type' => 'select',
            'choices' => $this->get_content_width_choices(),
        ));

        // Container Padding
        $wp_customize->add_setting('recruitpro_container_padding', array(
            'default' => 20,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_container_padding', array(
            'label' => __('Container Padding (pixels)', 'recruitpro'),
            'description' => __('Space around the main container.', 'recruitpro'),
            'section' => 'recruitpro_general_layout',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 60,
                'step' => 5,
            ),
        ));

        // Enable Boxed Layout
        $wp_customize->add_setting('recruitpro_boxed_layout', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_boxed_layout', array(
            'label' => __('Enable Boxed Layout', 'recruitpro'),
            'description' => __('Wrap the entire site in a centered container.', 'recruitpro'),
            'section' => 'recruitpro_general_layout',
            'type' => 'checkbox',
        ));

        // =================================================================
        // PAGE LAYOUTS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_page_layouts', array(
            'title' => __('Page Layouts', 'recruitpro'),
            'description' => __('Configure layouts for different page types.', 'recruitpro'),
            'panel' => 'recruitpro_layout_panel',
            'priority' => 20,
        ));

        // Homepage Layout
        $wp_customize->add_setting('recruitpro_homepage_layout', array(
            'default' => 'fullwidth_contained',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_homepage_layout', array(
            'label' => __('Homepage Layout', 'recruitpro'),
            'section' => 'recruitpro_page_layouts',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // Blog Layout
        $wp_customize->add_setting('recruitpro_blog_layout', array(
            'default' => 'sidebar_right',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_blog_layout', array(
            'label' => __('Blog Layout', 'recruitpro'),
            'section' => 'recruitpro_page_layouts',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // Single Post Layout
        $wp_customize->add_setting('recruitpro_single_post_layout', array(
            'default' => 'sidebar_right',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_single_post_layout', array(
            'label' => __('Single Post Layout', 'recruitpro'),
            'section' => 'recruitpro_page_layouts',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // Page Layout
        $wp_customize->add_setting('recruitpro_page_layout', array(
            'default' => 'fullwidth_contained',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_page_layout', array(
            'label' => __('Default Page Layout', 'recruitpro'),
            'section' => 'recruitpro_page_layouts',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // Archive Layout
        $wp_customize->add_setting('recruitpro_archive_layout', array(
            'default' => 'sidebar_right',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_archive_layout', array(
            'label' => __('Archive Layout', 'recruitpro'),
            'section' => 'recruitpro_page_layouts',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // Search Results Layout
        $wp_customize->add_setting('recruitpro_search_layout', array(
            'default' => 'sidebar_right',
            'sanitize_callback' => array($this, 'sanitize_layout_choice'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_search_layout', array(
            'label' => __('Search Results Layout', 'recruitpro'),
            'section' => 'recruitpro_page_layouts',
            'type' => 'select',
            'choices' => $this->get_layout_choices(),
        ));

        // =================================================================
        // SIDEBAR CONFIGURATION SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_sidebar_config', array(
            'title' => __('Sidebar Configuration', 'recruitpro'),
            'description' => __('Configure sidebar areas and their behavior.', 'recruitpro'),
            'panel' => 'recruitpro_layout_panel',
            'priority' => 30,
        ));

        // Sidebar Width
        $wp_customize->add_setting('recruitpro_sidebar_width', array(
            'default' => 30,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_sidebar_width', array(
            'label' => __('Sidebar Width (%)', 'recruitpro'),
            'description' => __('Width of sidebar relative to container.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_config',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 40,
                'step' => 1,
            ),
        ));

        // Sidebar Gap
        $wp_customize->add_setting('recruitpro_sidebar_gap', array(
            'default' => 40,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_sidebar_gap', array(
            'label' => __('Sidebar Gap (pixels)', 'recruitpro'),
            'description' => __('Space between content and sidebar.', 'recruitpro'),
            'section' => 'recruitpro_sidebar_config',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 80,
                'step' => 5,
            ),
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
            'section' => 'recruitpro_sidebar_config',
            'type' => 'checkbox',
        ));

        // Mobile Sidebar Behavior
        $wp_customize->add_setting('recruitpro_mobile_sidebar_behavior', array(
            'default' => 'below_content',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_sidebar_behavior', array(
            'label' => __('Mobile Sidebar Behavior', 'recruitpro'),
            'section' => 'recruitpro_sidebar_config',
            'type' => 'select',
            'choices' => array(
                'below_content' => __('Show Below Content', 'recruitpro'),
                'hidden' => __('Hide on Mobile', 'recruitpro'),
                'collapsible' => __('Collapsible Toggle', 'recruitpro'),
            ),
        ));

        // =================================================================
        // RESPONSIVE LAYOUT SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_responsive_layout', array(
            'title' => __('Responsive Layout', 'recruitpro'),
            'description' => __('Configure how layouts adapt to different screen sizes.', 'recruitpro'),
            'panel' => 'recruitpro_layout_panel',
            'priority' => 40,
        ));

        // Mobile Layout
        $wp_customize->add_setting('recruitpro_mobile_layout', array(
            'default' => 'single_column',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_layout', array(
            'label' => __('Mobile Layout', 'recruitpro'),
            'description' => __('Layout behavior on mobile devices.', 'recruitpro'),
            'section' => 'recruitpro_responsive_layout',
            'type' => 'select',
            'choices' => array(
                'single_column' => __('Single Column (Recommended)', 'recruitpro'),
                'maintain_layout' => __('Maintain Desktop Layout', 'recruitpro'),
                'mobile_optimized' => __('Mobile Optimized Layout', 'recruitpro'),
            ),
        ));

        // Tablet Layout
        $wp_customize->add_setting('recruitpro_tablet_layout', array(
            'default' => 'adaptive',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_tablet_layout', array(
            'label' => __('Tablet Layout', 'recruitpro'),
            'description' => __('Layout behavior on tablet devices.', 'recruitpro'),
            'section' => 'recruitpro_responsive_layout',
            'type' => 'select',
            'choices' => array(
                'adaptive' => __('Adaptive (Recommended)', 'recruitpro'),
                'desktop_like' => __('Desktop-like Layout', 'recruitpro'),
                'mobile_like' => __('Mobile-like Layout', 'recruitpro'),
            ),
        ));

        // Breakpoints
        $wp_customize->add_setting('recruitpro_mobile_breakpoint', array(
            'default' => 768,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_mobile_breakpoint', array(
            'label' => __('Mobile Breakpoint (pixels)', 'recruitpro'),
            'description' => __('Screen width where mobile layout activates.', 'recruitpro'),
            'section' => 'recruitpro_responsive_layout',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 480,
                'max' => 1024,
                'step' => 1,
            ),
        ));

        $wp_customize->add_setting('recruitpro_tablet_breakpoint', array(
            'default' => 1024,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_tablet_breakpoint', array(
            'label' => __('Tablet Breakpoint (pixels)', 'recruitpro'),
            'description' => __('Screen width where tablet layout activates.', 'recruitpro'),
            'section' => 'recruitpro_responsive_layout',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 768,
                'max' => 1200,
                'step' => 1,
            ),
        ));

        // =================================================================
        // GRID SYSTEM SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_grid_system', array(
            'title' => __('Grid System', 'recruitpro'),
            'description' => __('Configure the CSS Grid and Flexbox system for content layouts.', 'recruitpro'),
            'panel' => 'recruitpro_layout_panel',
            'priority' => 50,
        ));

        // Grid Type
        $wp_customize->add_setting('recruitpro_grid_type', array(
            'default' => 'css_grid',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_grid_type', array(
            'label' => __('Grid System Type', 'recruitpro'),
            'section' => 'recruitpro_grid_system',
            'type' => 'select',
            'choices' => array(
                'css_grid' => __('CSS Grid (Modern)', 'recruitpro'),
                'flexbox' => __('Flexbox (Compatible)', 'recruitpro'),
                'float' => __('Float (Legacy)', 'recruitpro'),
            ),
        ));

        // Grid Gap
        $wp_customize->add_setting('recruitpro_grid_gap', array(
            'default' => 30,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_grid_gap', array(
            'label' => __('Grid Gap (pixels)', 'recruitpro'),
            'description' => __('Space between grid items.', 'recruitpro'),
            'section' => 'recruitpro_grid_system',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 10,
                'max' => 60,
                'step' => 5,
            ),
        ));

        // Default Columns
        $wp_customize->add_setting('recruitpro_default_columns', array(
            'default' => 3,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        ));

        $wp_customize->add_control('recruitpro_default_columns', array(
            'label' => __('Default Grid Columns', 'recruitpro'),
            'description' => __('Default number of columns for grid layouts.', 'recruitpro'),
            'section' => 'recruitpro_grid_system',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 6,
                'step' => 1,
            ),
        ));
    }

    /**
     * Add layout body classes
     * 
     * @since 1.0.0
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_layout_body_classes($classes) {
        
        // Get current layout
        $layout = $this->get_current_layout();
        
        if (isset($this->layout_types[$layout])) {
            $layout_config = $this->layout_types[$layout];
            
            // Add layout class
            $classes[] = $layout_config['container_class'];
            
            // Add sidebar class
            if ($layout_config['has_sidebar']) {
                $classes[] = 'has-sidebar';
                if (isset($layout_config['sidebar_position'])) {
                    $classes[] = 'sidebar-' . $layout_config['sidebar_position'];
                }
            } else {
                $classes[] = 'no-sidebar';
            }
        }
        
        // Add content width class
        $content_width = get_theme_mod('recruitpro_content_width', 'wide');
        $classes[] = 'content-width-' . $content_width;
        
        // Add boxed layout class
        if (get_theme_mod('recruitpro_boxed_layout', false)) {
            $classes[] = 'boxed-layout';
        }
        
        // Add grid system class
        $grid_type = get_theme_mod('recruitpro_grid_type', 'css_grid');
        $classes[] = 'grid-' . str_replace('_', '-', $grid_type);
        
        // Add responsive classes
        $classes[] = 'responsive-layout';
        
        // Add mobile layout class
        $mobile_layout = get_theme_mod('recruitpro_mobile_layout', 'single_column');
        $classes[] = 'mobile-' . str_replace('_', '-', $mobile_layout);
        
        return $classes;
    }

    /**
     * Add layout post classes
     * 
     * @since 1.0.0
     * @param array $classes Existing post classes
     * @param string $class Additional class
     * @param int $post_id Post ID
     * @return array Modified post classes
     */
    public function add_layout_post_classes($classes, $class, $post_id) {
        
        $layout = $this->get_current_layout();
        
        if (isset($this->layout_types[$layout])) {
            $layout_config = $this->layout_types[$layout];
            
            // Add content class
            $classes[] = $layout_config['content_class'];
            
            // Add grid column class if applicable
            if (isset($layout_config['grid_columns'])) {
                $classes[] = 'grid-item';
                $classes[] = 'grid-columns-' . $layout_config['grid_columns'];
            }
        }
        
        return $classes;
    }

    /**
     * Output layout CSS
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_layout_css() {
        
        $content_width = $this->get_content_width_value();
        $sidebar_width = get_theme_mod('recruitpro_sidebar_width', 30);
        $sidebar_gap = get_theme_mod('recruitpro_sidebar_gap', 40);
        $container_padding = get_theme_mod('recruitpro_container_padding', 20);
        $grid_gap = get_theme_mod('recruitpro_grid_gap', 30);
        $mobile_breakpoint = get_theme_mod('recruitpro_mobile_breakpoint', 768);
        $tablet_breakpoint = get_theme_mod('recruitpro_tablet_breakpoint', 1024);
        $grid_type = get_theme_mod('recruitpro_grid_type', 'css_grid');

        ?>
        <style id="recruitpro-layout-customizations">
        /* Layout Variables */
        :root {
            --recruitpro-content-width: <?php echo is_numeric($content_width) ? $content_width . 'px' : $content_width; ?>;
            --recruitpro-sidebar-width: <?php echo $sidebar_width; ?>%;
            --recruitpro-content-width-with-sidebar: <?php echo (100 - $sidebar_width); ?>%;
            --recruitpro-sidebar-gap: <?php echo $sidebar_gap; ?>px;
            --recruitpro-container-padding: <?php echo $container_padding; ?>px;
            --recruitpro-grid-gap: <?php echo $grid_gap; ?>px;
            --recruitpro-mobile-breakpoint: <?php echo $mobile_breakpoint; ?>px;
            --recruitpro-tablet-breakpoint: <?php echo $tablet_breakpoint; ?>px;
        }

        /* Main Container */
        .site-container {
            max-width: var(--recruitpro-content-width);
            margin: 0 auto;
            padding: 0 var(--recruitpro-container-padding);
        }

        <?php if (get_theme_mod('recruitpro_boxed_layout', false)): ?>
        .boxed-layout .site-container {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background: #fff;
        }
        <?php endif; ?>

        /* Layout Styles */
        .layout-fullwidth .site-container {
            max-width: 100%;
            padding: 0;
        }

        .layout-fullwidth-contained .site-container {
            max-width: var(--recruitpro-content-width);
        }

        /* Sidebar Layouts */
        .has-sidebar .content-area {
            display: <?php echo $grid_type === 'css_grid' ? 'grid' : 'flex'; ?>;
            <?php if ($grid_type === 'css_grid'): ?>
            grid-template-columns: var(--recruitpro-content-width-with-sidebar) var(--recruitpro-sidebar-width);
            grid-gap: var(--recruitpro-sidebar-gap);
            <?php else: ?>
            gap: var(--recruitpro-sidebar-gap);
            <?php endif; ?>
        }

        .sidebar-left .content-area {
            <?php if ($grid_type === 'css_grid'): ?>
            grid-template-columns: var(--recruitpro-sidebar-width) var(--recruitpro-content-width-with-sidebar);
            <?php else: ?>
            flex-direction: row-reverse;
            <?php endif; ?>
        }

        .layout-dual-sidebar .content-area {
            <?php if ($grid_type === 'css_grid'): ?>
            grid-template-columns: 20% 60% 20%;
            <?php endif; ?>
        }

        <?php if ($grid_type === 'flexbox'): ?>
        .has-sidebar .main-content {
            flex: 1;
        }

        .has-sidebar .sidebar {
            flex: 0 0 var(--recruitpro-sidebar-width);
        }
        <?php endif; ?>

        /* Grid Layouts */
        .content-grid-2,
        .content-grid-3,
        .content-grid-4 {
            display: <?php echo $grid_type === 'css_grid' ? 'grid' : 'flex'; ?>;
            gap: var(--recruitpro-grid-gap);
            <?php if ($grid_type === 'flexbox'): ?>
            flex-wrap: wrap;
            <?php endif; ?>
        }

        <?php if ($grid_type === 'css_grid'): ?>
        .content-grid-2 { grid-template-columns: repeat(2, 1fr); }
        .content-grid-3 { grid-template-columns: repeat(3, 1fr); }
        .content-grid-4 { grid-template-columns: repeat(4, 1fr); }
        <?php else: ?>
        .content-grid-2 .grid-item { flex: 0 0 calc(50% - var(--recruitpro-grid-gap) / 2); }
        .content-grid-3 .grid-item { flex: 0 0 calc(33.333% - var(--recruitpro-grid-gap) * 2 / 3); }
        .content-grid-4 .grid-item { flex: 0 0 calc(25% - var(--recruitpro-grid-gap) * 3 / 4); }
        <?php endif; ?>

        /* Professional Layouts */
        .layout-corporate .site-container {
            padding: 60px var(--recruitpro-container-padding);
        }

        .layout-executive .site-container {
            max-width: 800px;
            padding: 80px var(--recruitpro-container-padding);
        }

        .layout-minimal .site-container {
            max-width: 700px;
            padding: 40px var(--recruitpro-container-padding);
        }

        /* Sticky Sidebar */
        <?php if (get_theme_mod('recruitpro_sticky_sidebar', true)): ?>
        .sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        <?php endif; ?>

        /* Responsive Design */
        @media (max-width: <?php echo $tablet_breakpoint; ?>px) {
            .content-grid-4 {
                <?php if ($grid_type === 'css_grid'): ?>
                grid-template-columns: repeat(2, 1fr);
                <?php endif; ?>
            }
            
            <?php if ($grid_type === 'flexbox'): ?>
            .content-grid-4 .grid-item {
                flex: 0 0 calc(50% - var(--recruitpro-grid-gap) / 2);
            }
            <?php endif; ?>
        }

        @media (max-width: <?php echo $mobile_breakpoint; ?>px) {
            /* Mobile Layout Adjustments */
            .site-container {
                padding: 0 15px;
            }

            <?php if (get_theme_mod('recruitpro_mobile_layout', 'single_column') === 'single_column'): ?>
            .has-sidebar .content-area {
                display: block;
            }

            .sidebar {
                position: static;
                margin-top: 40px;
            }

            <?php $mobile_sidebar_behavior = get_theme_mod('recruitpro_mobile_sidebar_behavior', 'below_content'); ?>
            <?php if ($mobile_sidebar_behavior === 'hidden'): ?>
            .sidebar {
                display: none;
            }
            <?php endif; ?>
            <?php endif; ?>

            /* Grid Layouts on Mobile */
            .content-grid-2,
            .content-grid-3,
            .content-grid-4 {
                <?php if ($grid_type === 'css_grid'): ?>
                grid-template-columns: 1fr;
                <?php else: ?>
                flex-direction: column;
                <?php endif; ?>
            }

            <?php if ($grid_type === 'flexbox'): ?>
            .grid-item {
                flex: 1 1 auto !important;
            }
            <?php endif; ?>

            /* Professional Layout Mobile Adjustments */
            .layout-corporate .site-container,
            .layout-executive .site-container {
                padding: 30px 15px;
            }
        }

        /* Print Styles */
        @media print {
            .sidebar {
                display: none;
            }

            .has-sidebar .content-area {
                display: block;
            }

            .site-container {
                max-width: none;
                padding: 0;
            }
        }
        </style>
        <?php
    }

    /**
     * Register layout sidebars
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_layout_sidebars() {
        
        foreach ($this->sidebar_configs as $sidebar_key => $sidebar_config) {
            register_sidebar(array(
                'name' => $sidebar_config['name'],
                'id' => $sidebar_config['id'],
                'description' => $sidebar_config['description'],
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="widget-title">',
                'after_title' => '</h3>',
            ));
        }
    }

    /**
     * Layout content wrapper start
     * 
     * @since 1.0.0
     * @return void
     */
    public function layout_content_wrapper_start() {
        
        $layout = $this->get_current_layout();
        
        if (isset($this->layout_types[$layout])) {
            $layout_config = $this->layout_types[$layout];
            
            echo '<div class="content-area ' . esc_attr($layout_config['container_class']) . '">';
            echo '<main class="main-content ' . esc_attr($layout_config['content_class']) . '" role="main">';
        }
    }

    /**
     * Layout content wrapper end
     * 
     * @since 1.0.0
     * @return void
     */
    public function layout_content_wrapper_end() {
        
        echo '</main>';
        
        $layout = $this->get_current_layout();
        
        if (isset($this->layout_types[$layout]) && $this->layout_types[$layout]['has_sidebar']) {
            $this->render_sidebar($layout);
        }
        
        echo '</div>';
    }

    /**
     * Render sidebar for current layout
     * 
     * @since 1.0.0
     * @param string $layout Current layout
     * @return void
     */
    private function render_sidebar($layout) {
        
        if (!isset($this->layout_types[$layout])) {
            return;
        }
        
        $layout_config = $this->layout_types[$layout];
        $sidebar_class = isset($layout_config['sidebar_class']) ? $layout_config['sidebar_class'] : 'sidebar';
        
        echo '<aside class="sidebar ' . esc_attr($sidebar_class) . '" role="complementary">';
        
        // Determine which sidebar to show
        $sidebar_id = $this->get_sidebar_for_context();
        
        if (is_active_sidebar($sidebar_id)) {
            dynamic_sidebar($sidebar_id);
        } else {
            // Show default widgets if no widgets are set
            $this->render_default_sidebar_content();
        }
        
        echo '</aside>';
    }

    /**
     * Get current layout based on context
     * 
     * @since 1.0.0
     * @return string Current layout
     */
    private function get_current_layout() {
        
        // Check for page-specific layout meta
        if (is_singular()) {
            $post_layout = get_post_meta(get_the_ID(), '_recruitpro_layout', true);
            if (!empty($post_layout) && isset($this->layout_types[$post_layout])) {
                return $post_layout;
            }
        }
        
        // Get layout based on page type
        if (is_front_page()) {
            return get_theme_mod('recruitpro_homepage_layout', 'fullwidth_contained');
        }
        
        if (is_home() || is_category() || is_tag() || is_author() || is_date()) {
            return get_theme_mod('recruitpro_blog_layout', 'sidebar_right');
        }
        
        if (is_single()) {
            return get_theme_mod('recruitpro_single_post_layout', 'sidebar_right');
        }
        
        if (is_page()) {
            return get_theme_mod('recruitpro_page_layout', 'fullwidth_contained');
        }
        
        if (is_search()) {
            return get_theme_mod('recruitpro_search_layout', 'sidebar_right');
        }
        
        if (is_archive()) {
            return get_theme_mod('recruitpro_archive_layout', 'sidebar_right');
        }
        
        // Default
        return get_theme_mod('recruitpro_site_layout', 'wide');
    }

    /**
     * Get sidebar ID for current context
     * 
     * @since 1.0.0
     * @return string Sidebar ID
     */
    private function get_sidebar_for_context() {
        
        if (is_single() || is_home() || is_category() || is_tag() || is_author() || is_date()) {
            return 'primary-sidebar';
        }
        
        if (is_page()) {
            return 'page-sidebar';
        }
        
        // Check if this is recruitment-related content
        if ($this->is_recruitment_context()) {
            return 'recruitment-sidebar';
        }
        
        return 'primary-sidebar';
    }

    /**
     * Check if current context is recruitment-related
     * 
     * @since 1.0.0
     * @return bool Whether context is recruitment-related
     */
    private function is_recruitment_context() {
        
        // Check for recruitment-related categories, tags, or post types
        if (is_category(array('jobs', 'careers', 'recruitment', 'candidates'))) {
            return true;
        }
        
        if (is_tag(array('hiring', 'job-search', 'career-advice'))) {
            return true;
        }
        
        if (is_singular() && has_category(array('jobs', 'careers', 'recruitment'))) {
            return true;
        }
        
        return false;
    }

    /**
     * Render default sidebar content
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_default_sidebar_content() {
        ?>
        <div class="widget widget-default">
            <h3 class="widget-title"><?php _e('Quick Links', 'recruitpro'); ?></h3>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About Us', 'recruitpro'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/services')); ?>"><?php _e('Our Services', 'recruitpro'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact Us', 'recruitpro'); ?></a></li>
            </ul>
        </div>
        <?php
    }

    /**
     * Get content width value
     * 
     * @since 1.0.0
     * @return mixed Content width value
     */
    private function get_content_width_value() {
        
        $width_key = get_theme_mod('recruitpro_content_width', 'wide');
        
        if (isset($this->content_widths[$width_key])) {
            return $this->content_widths[$width_key]['width'];
        }
        
        return 1200; // Default width
    }

    /**
     * Get layout choices for customizer
     * 
     * @since 1.0.0
     * @return array Layout choices
     */
    private function get_layout_choices() {
        
        $choices = array();
        
        foreach ($this->layout_types as $key => $layout) {
            $choices[$key] = $layout['name'] . ' - ' . $layout['description'];
        }
        
        return $choices;
    }

    /**
     * Get content width choices for customizer
     * 
     * @since 1.0.0
     * @return array Content width choices
     */
    private function get_content_width_choices() {
        
        $choices = array();
        
        foreach ($this->content_widths as $key => $width) {
            $choices[$key] = $width['name'] . ' - ' . $width['description'];
        }
        
        return $choices;
    }

    /**
     * Sanitize layout choice
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_layout_choice($input) {
        
        $valid_layouts = array_keys($this->layout_types);
        return in_array($input, $valid_layouts) ? $input : 'fullwidth_contained';
    }

    /**
     * Sanitize content width choice
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_content_width($input) {
        
        $valid_widths = array_keys($this->content_widths);
        return in_array($input, $valid_widths) ? $input : 'wide';
    }

    /**
     * Get layout configuration
     * 
     * @since 1.0.0
     * @param string $layout Layout key
     * @return array Layout configuration
     */
    public function get_layout_config($layout = null) {
        
        if (is_null($layout)) {
            $layout = $this->get_current_layout();
        }
        
        return isset($this->layout_types[$layout]) ? $this->layout_types[$layout] : array();
    }

    /**
     * Check if current layout has sidebar
     * 
     * @since 1.0.0
     * @return bool Whether layout has sidebar
     */
    public function has_sidebar() {
        
        $layout = $this->get_current_layout();
        $config = $this->get_layout_config($layout);
        
        return isset($config['has_sidebar']) ? $config['has_sidebar'] : false;
    }
}

// Initialize layout options
new RecruitPro_Layout_Options();

/**
 * Helper function to get current layout
 * 
 * @since 1.0.0
 * @return string Current layout
 */
function recruitpro_get_layout() {
    
    static $layout_manager = null;
    
    if (is_null($layout_manager)) {
        $layout_manager = new RecruitPro_Layout_Options();
    }
    
    return $layout_manager->get_layout_config();
}

/**
 * Helper function to check if layout has sidebar
 * 
 * @since 1.0.0
 * @return bool Whether layout has sidebar
 */
function recruitpro_has_sidebar() {
    
    static $layout_manager = null;
    
    if (is_null($layout_manager)) {
        $layout_manager = new RecruitPro_Layout_Options();
    }
    
    return $layout_manager->has_sidebar();
}

/**
 * Helper function to get layout classes
 * 
 * @since 1.0.0
 * @param string $element Element to get classes for
 * @return string Layout classes
 */
function recruitpro_get_layout_classes($element = 'container') {
    
    $layout = recruitpro_get_layout();
    $classes = array();
    
    switch ($element) {
        case 'container':
            $classes[] = isset($layout['container_class']) ? $layout['container_class'] : '';
            break;
        case 'content':
            $classes[] = isset($layout['content_class']) ? $layout['content_class'] : '';
            break;
        case 'sidebar':
            $classes[] = isset($layout['sidebar_class']) ? $layout['sidebar_class'] : '';
            break;
    }
    
    return implode(' ', array_filter($classes));
}

?>