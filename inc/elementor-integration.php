<?php
/**
 * RecruitPro Theme Elementor Integration
 *
 * Complete Elementor Page Builder integration with recruitment-focused widgets
 * Supports Elementor Pro Theme Builder and custom content elements
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize Elementor integration
 */
function recruitpro_init_elementor_integration() {
    // Check if Elementor is active
    if (!defined('ELEMENTOR_VERSION')) {
        return;
    }
    
    // Basic Elementor support
    recruitpro_add_elementor_support();
    
    // Theme Builder integration
    recruitpro_setup_theme_builder_integration();
    
    // Custom widgets and elements
    recruitpro_register_custom_elementor_widgets();
    
    // Global settings integration
    recruitpro_setup_global_settings_integration();
    
    // Dynamic content integration
    recruitpro_setup_dynamic_content_integration();
    
    // Custom CSS and styling
    recruitpro_setup_elementor_styling();
    
    // Performance optimizations
    recruitpro_setup_elementor_optimizations();
}
add_action('after_setup_theme', 'recruitpro_init_elementor_integration');

/**
 * Add basic Elementor support
 */
function recruitpro_add_elementor_support() {
    // Add theme support for Elementor
    add_theme_support('elementor');
    
    // Add theme support for Elementor Pro features
    if (defined('ELEMENTOR_PRO_VERSION')) {
        add_theme_support('elementor-pro');
    }
    
    // Add post type support for custom post types
    add_post_type_support('testimonial', 'elementor');
    add_post_type_support('team_member', 'elementor');
    add_post_type_support('service', 'elementor');
    add_post_type_support('success_story', 'elementor');
    
    // Set default page template to full width for Elementor
    add_action('template_redirect', 'recruitpro_set_elementor_page_template');
}

/**
 * Set full width template for Elementor pages
 */
function recruitpro_set_elementor_page_template() {
    if (!is_singular()) {
        return;
    }
    
    $post_id = get_the_ID();
    
    // Check if page is built with Elementor
    if (\Elementor\Plugin::$instance->documents->get($post_id)->is_built_with_elementor()) {
        // Remove sidebar for Elementor pages by default
        add_filter('recruitpro_page_layout', function() {
            return 'no-sidebar';
        });
    }
}

/**
 * Setup Theme Builder integration
 */
function recruitpro_setup_theme_builder_integration() {
    if (!defined('ELEMENTOR_PRO_VERSION')) {
        return;
    }
    
    // Register theme locations
    add_action('elementor/theme/register_locations', 'recruitpro_register_elementor_locations');
    
    // Handle theme builder templates
    add_action('elementor/theme/before_do_header', 'recruitpro_elementor_before_header');
    add_action('elementor/theme/after_do_header', 'recruitpro_elementor_after_header');
    add_action('elementor/theme/before_do_footer', 'recruitpro_elementor_before_footer');
    add_action('elementor/theme/after_do_footer', 'recruitpro_elementor_after_footer');
    
    // Custom template conditions
    add_filter('elementor_pro/theme_builder/conditions_cache', 'recruitpro_add_custom_conditions');
}

/**
 * Register Elementor theme locations
 */
function recruitpro_register_elementor_locations($elementor_theme_manager) {
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
    $elementor_theme_manager->register_location('single');
    $elementor_theme_manager->register_location('archive');
    
    // Custom recruitment-specific locations
    $elementor_theme_manager->register_location('job_single');
    $elementor_theme_manager->register_location('job_archive');
    $elementor_theme_manager->register_location('team_member_single');
    $elementor_theme_manager->register_location('testimonial_archive');
}

/**
 * Handle header template override
 */
function recruitpro_elementor_before_header() {
    $header_id = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('header');
    
    if (!empty($header_id)) {
        // Remove theme header hooks
        remove_action('recruitpro_header', 'recruitpro_site_header');
        remove_action('recruitpro_header', 'recruitpro_main_navigation');
        
        // Add Elementor header class
        add_filter('body_class', function($classes) {
            $classes[] = 'elementor-header-active';
            return $classes;
        });
    }
}

function recruitpro_elementor_after_header() {
    // Add any post-header processing if needed
}

/**
 * Handle footer template override
 */
function recruitpro_elementor_before_footer() {
    $footer_id = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('footer');
    
    if (!empty($footer_id)) {
        // Remove theme footer hooks
        remove_action('recruitpro_footer', 'recruitpro_site_footer');
        remove_action('recruitpro_footer', 'recruitpro_footer_widgets');
        
        // Add Elementor footer class
        add_filter('body_class', function($classes) {
            $classes[] = 'elementor-footer-active';
            return $classes;
        });
    }
}

function recruitpro_elementor_after_footer() {
    // Add any post-footer processing if needed
}

/**
 * Add custom template conditions
 */
function recruitpro_add_custom_conditions($conditions) {
    // Add recruitment-specific conditions
    $conditions['recruitment'] = array(
        'job_single' => esc_html__('Job Single', 'recruitpro'),
        'job_archive' => esc_html__('Job Archive', 'recruitpro'),
        'testimonial_archive' => esc_html__('Testimonial Archive', 'recruitpro'),
        'team_archive' => esc_html__('Team Archive', 'recruitpro'),
        'service_archive' => esc_html__('Service Archive', 'recruitpro'),
    );
    
    return $conditions;
}

/**
 * Register custom Elementor widgets
 */
function recruitpro_register_custom_elementor_widgets() {
    // Register widgets after Elementor is loaded
    add_action('elementor/widgets/widgets_registered', 'recruitpro_register_widgets');
    
    // Register widget categories
    add_action('elementor/elements/categories_registered', 'recruitpro_register_widget_categories');
    
    // Register controls
    add_action('elementor/controls/controls_registered', 'recruitpro_register_custom_controls');
}

/**
 * Register widget categories
 */
function recruitpro_register_widget_categories($elements_manager) {
    $elements_manager->add_category(
        'recruitpro',
        array(
            'title' => esc_html__('RecruitPro Elements', 'recruitpro'),
            'icon' => 'fa fa-plug',
        )
    );
    
    $elements_manager->add_category(
        'recruitpro-recruitment',
        array(
            'title' => esc_html__('Recruitment Content', 'recruitpro'),
            'icon' => 'fa fa-users',
        )
    );
}

/**
 * Register custom widgets
 */
function recruitpro_register_widgets() {
    // Include widget classes
    require_once get_template_directory() . '/inc/elementor-widgets/class-testimonials-widget.php';
    require_once get_template_directory() . '/inc/elementor-widgets/class-team-members-widget.php';
    require_once get_template_directory() . '/inc/elementor-widgets/class-services-widget.php';
    require_once get_template_directory() . '/inc/elementor-widgets/class-success-stories-widget.php';
    require_once get_template_directory() . '/inc/elementor-widgets/class-job-search-widget.php';
    require_once get_template_directory() . '/inc/elementor-widgets/class-contact-info-widget.php';
    require_once get_template_directory() . '/inc/elementor-widgets/class-stats-counter-widget.php';
    
    // Register widgets
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Testimonials_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Team_Members_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Services_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Success_Stories_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Job_Search_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Contact_Info_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \RecruitPro\Elementor\Stats_Counter_Widget());
}

/**
 * Register custom controls
 */
function recruitpro_register_custom_controls() {
    // Register recruitment-specific controls if needed
    // This can be extended for custom control types
}

/**
 * Setup global settings integration
 */
function recruitpro_setup_global_settings_integration() {
    // Sync theme colors with Elementor Global Colors
    add_action('elementor/kit/register_tabs', 'recruitpro_register_elementor_kit_settings');
    
    // Update Elementor colors when theme colors change
    add_action('customize_save_after', 'recruitpro_sync_colors_to_elementor');
    
    // Sync fonts
    add_action('elementor/fonts/groups/register', 'recruitpro_register_font_groups');
}

/**
 * Register kit settings
 */
function recruitpro_register_elementor_kit_settings($kit) {
    // Sync theme color scheme
    $theme_colors = recruitpro_get_current_color_scheme();
    
    if (!empty($theme_colors['colors'])) {
        $elementor_colors = array();
        
        $color_mapping = array(
            'primary' => esc_html__('Primary', 'recruitpro'),
            'secondary' => esc_html__('Secondary', 'recruitpro'),
            'accent' => esc_html__('Accent', 'recruitpro'),
            'text_primary' => esc_html__('Text', 'recruitpro'),
        );
        
        foreach ($color_mapping as $theme_color => $label) {
            if (isset($theme_colors['colors'][$theme_color])) {
                $elementor_colors[] = array(
                    'id' => 'recruitpro_' . $theme_color,
                    'title' => $label,
                    'color' => $theme_colors['colors'][$theme_color],
                );
            }
        }
        
        // Update Elementor kit colors
        if (!empty($elementor_colors)) {
            update_option('elementor_global_colors', $elementor_colors);
        }
    }
}

/**
 * Sync colors to Elementor
 */
function recruitpro_sync_colors_to_elementor() {
    // This function is called when customizer settings are saved
    recruitpro_register_elementor_kit_settings(null);
}

/**
 * Register font groups
 */
function recruitpro_register_font_groups($font_groups) {
    $font_groups[] = array(
        'label' => esc_html__('RecruitPro Theme Fonts', 'recruitpro'),
        'fonts' => array(
            get_theme_mod('recruitpro_heading_font', 'Roboto'),
            get_theme_mod('recruitpro_body_font', 'Open Sans'),
        ),
    );
    
    return $font_groups;
}

/**
 * Setup dynamic content integration
 */
function recruitpro_setup_dynamic_content_integration() {
    if (!defined('ELEMENTOR_PRO_VERSION')) {
        return;
    }
    
    // Register dynamic tags
    add_action('elementor_pro/dynamic_tags/register_tags', 'recruitpro_register_dynamic_tags');
    
    // Add custom fields support
    add_filter('elementor_pro/dynamic_tags/acf/field_types', 'recruitpro_add_acf_field_types');
}

/**
 * Register dynamic tags
 */
function recruitpro_register_dynamic_tags($dynamic_tags) {
    // Include dynamic tag classes
    require_once get_template_directory() . '/inc/elementor-dynamic-tags/class-contact-info-tag.php';
    require_once get_template_directory() . '/inc/elementor-dynamic-tags/class-company-info-tag.php';
    require_once get_template_directory() . '/inc/elementor-dynamic-tags/class-social-links-tag.php';
    
    // Register tags
    $dynamic_tags->register_tag(new \RecruitPro\Elementor\Contact_Info_Tag());
    $dynamic_tags->register_tag(new \RecruitPro\Elementor\Company_Info_Tag());
    $dynamic_tags->register_tag(new \RecruitPro\Elementor\Social_Links_Tag());
}

/**
 * Add ACF field types support
 */
function recruitpro_add_acf_field_types($field_types) {
    // Add support for recruitment-specific field types
    $field_types[] = 'recruitment_rating';
    $field_types[] = 'recruitment_salary';
    $field_types[] = 'recruitment_experience';
    
    return $field_types;
}

/**
 * Setup Elementor styling
 */
function recruitpro_setup_elementor_styling() {
    // Add custom CSS for Elementor integration
    add_action('elementor/frontend/after_enqueue_styles', 'recruitpro_elementor_frontend_styles');
    
    // Add editor styles
    add_action('elementor/editor/after_enqueue_styles', 'recruitpro_elementor_editor_styles');
    
    // Add custom CSS variables
    add_action('elementor/css-file/post/enqueue', 'recruitpro_elementor_add_css_variables');
    
    // Override theme styles for Elementor pages
    add_filter('recruitpro_elementor_page_styles', 'recruitpro_elementor_page_specific_styles');
}

/**
 * Enqueue frontend styles for Elementor
 */
function recruitpro_elementor_frontend_styles() {
    wp_enqueue_style(
        'recruitpro-elementor',
        get_template_directory_uri() . '/assets/css/elementor.css',
        array('elementor-frontend'),
        wp_get_theme()->get('Version')
    );
    
    // Add inline CSS for color scheme integration
    $css = recruitpro_generate_elementor_color_css();
    wp_add_inline_style('recruitpro-elementor', $css);
}

/**
 * Generate Elementor color CSS
 */
function recruitpro_generate_elementor_color_css() {
    $theme_colors = recruitpro_get_current_color_scheme();
    
    if (empty($theme_colors['colors'])) {
        return '';
    }
    
    $css = '';
    
    // Generate CSS variables for Elementor widgets
    $css .= '.elementor-widget-recruitpro-testimonials,';
    $css .= '.elementor-widget-recruitpro-team,';
    $css .= '.elementor-widget-recruitpro-services {';
    
    foreach ($theme_colors['colors'] as $color_name => $color_value) {
        $css .= '--recruitpro-' . str_replace('_', '-', $color_name) . ': ' . $color_value . ';';
    }
    
    $css .= '}';
    
    // Override Elementor default colors
    $css .= '.elementor-kit-' . get_option('elementor_active_kit', 1) . ' {';
    $css .= '--e-global-color-recruitpro-primary: ' . ($theme_colors['colors']['primary'] ?? '#0073aa') . ';';
    $css .= '--e-global-color-recruitpro-secondary: ' . ($theme_colors['colors']['secondary'] ?? '#005177') . ';';
    $css .= '--e-global-color-recruitpro-accent: ' . ($theme_colors['colors']['accent'] ?? '#00a0d2') . ';';
    $css .= '--e-global-color-recruitpro-text: ' . ($theme_colors['colors']['text_primary'] ?? '#333333') . ';';
    $css .= '}';
    
    return $css;
}

/**
 * Enqueue editor styles for Elementor
 */
function recruitpro_elementor_editor_styles() {
    wp_enqueue_style(
        'recruitpro-elementor-editor',
        get_template_directory_uri() . '/assets/css/elementor-editor.css',
        array('elementor-editor'),
        wp_get_theme()->get('Version')
    );
}

/**
 * Add CSS variables to Elementor pages
 */
function recruitpro_elementor_add_css_variables($css_file) {
    // Add theme CSS variables to Elementor generated CSS
    $custom_css = ':root {';
    
    $theme_colors = recruitpro_get_current_color_scheme();
    if (!empty($theme_colors['colors'])) {
        foreach ($theme_colors['colors'] as $color_name => $color_value) {
            $custom_css .= '--recruitpro-' . str_replace('_', '-', $color_name) . ': ' . $color_value . ';';
        }
    }
    
    $custom_css .= '}';
    
    $css_file->add_css($custom_css);
}

/**
 * Page-specific styles for Elementor
 */
function recruitpro_elementor_page_specific_styles($post_id) {
    // Check if page is built with Elementor
    if (!\Elementor\Plugin::$instance->documents->get($post_id)->is_built_with_elementor()) {
        return;
    }
    
    // Add full-width styling
    ?>
    <style>
    .elementor-page .site-main {
        padding: 0;
        margin: 0;
    }
    
    .elementor-page .container {
        max-width: none;
        padding: 0;
    }
    
    .elementor-page .content-area {
        width: 100%;
    }
    
    .elementor-page .site-content {
        display: block;
    }
    
    /* Hide theme elements that conflict with Elementor */
    .elementor-header-active .site-header {
        display: none;
    }
    
    .elementor-footer-active .site-footer {
        display: none;
    }
    
    /* Ensure proper spacing */
    .elementor-page.elementor-header-active .site-content {
        padding-top: 0;
    }
    
    .elementor-page.elementor-footer-active .site-content {
        padding-bottom: 0;
    }
    </style>
    <?php
}

/**
 * Setup performance optimizations
 */
function recruitpro_setup_elementor_optimizations() {
    // Optimize Elementor loading
    add_action('elementor/frontend/before_enqueue_scripts', 'recruitpro_elementor_optimize_loading');
    
    // Remove unused Elementor widgets
    add_action('elementor/widgets/widgets_registered', 'recruitpro_unregister_unused_widgets', 15);
    
    // Optimize fonts loading
    add_filter('elementor/frontend/print_google_fonts', 'recruitpro_optimize_elementor_fonts');
    
    // Disable Elementor features not needed
    add_action('init', 'recruitpro_disable_unused_elementor_features');
}

/**
 * Optimize Elementor loading
 */
function recruitpro_elementor_optimize_loading() {
    // Defer non-critical Elementor scripts
    add_filter('script_loader_tag', function($tag, $handle) {
        if (strpos($handle, 'elementor') !== false && strpos($handle, 'frontend') !== false) {
            return str_replace(' src', ' defer src', $tag);
        }
        return $tag;
    }, 10, 2);
}

/**
 * Unregister unused Elementor widgets
 */
function recruitpro_unregister_unused_widgets() {
    // Get list of widgets to remove from customizer or theme options
    $unused_widgets = get_theme_mod('recruitpro_disabled_elementor_widgets', array());
    
    if (!empty($unused_widgets)) {
        foreach ($unused_widgets as $widget_name) {
            \Elementor\Plugin::instance()->widgets_manager->unregister_widget_type($widget_name);
        }
    }
}

/**
 * Optimize Elementor fonts
 */
function recruitpro_optimize_elementor_fonts($google_fonts) {
    // Combine with theme fonts to avoid duplicate loading
    $theme_fonts = get_theme_mod('recruitpro_google_fonts', 'Open Sans:400,600,700');
    
    // If theme fonts are already loaded, don't load them again through Elementor
    if (!empty($theme_fonts)) {
        $theme_font_families = explode(',', $theme_fonts);
        foreach ($theme_font_families as $theme_font) {
            $font_name = explode(':', $theme_font)[0];
            if (isset($google_fonts[$font_name])) {
                unset($google_fonts[$font_name]);
            }
        }
    }
    
    return $google_fonts;
}

/**
 * Disable unused Elementor features
 */
function recruitpro_disable_unused_elementor_features() {
    // Disable Elementor experiments that are not needed
    if (get_theme_mod('recruitpro_disable_elementor_experiments', true)) {
        update_option('elementor_experiment-e_hidden_wordpress_widgets', 'inactive');
        update_option('elementor_experiment-landing-pages', 'inactive');
        update_option('elementor_experiment-kit-elements-defaults', 'inactive');
    }
}

/**
 * Add Elementor compatibility information to system info
 */
function recruitpro_add_elementor_system_info($info) {
    $elementor_info = array(
        'elementor_version' => defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : 'Not installed',
        'elementor_pro_version' => defined('ELEMENTOR_PRO_VERSION') ? ELEMENTOR_PRO_VERSION : 'Not installed',
        'theme_builder_active' => defined('ELEMENTOR_PRO_VERSION') ? 'Yes' : 'No',
        'custom_widgets_count' => count(recruitpro_get_custom_elementor_widgets()),
        'global_colors_synced' => recruitpro_are_global_colors_synced() ? 'Yes' : 'No',
    );
    
    return array_merge($info, array('elementor' => $elementor_info));
}
add_filter('recruitpro_system_info', 'recruitpro_add_elementor_system_info');

/**
 * Get list of custom Elementor widgets
 */
function recruitpro_get_custom_elementor_widgets() {
    return array(
        'recruitpro-testimonials',
        'recruitpro-team-members',
        'recruitpro-services',
        'recruitpro-success-stories',
        'recruitpro-job-search',
        'recruitpro-contact-info',
        'recruitpro-stats-counter',
    );
}

/**
 * Check if global colors are synced
 */
function recruitpro_are_global_colors_synced() {
    $elementor_colors = get_option('elementor_global_colors', array());
    $theme_colors = recruitpro_get_current_color_scheme();
    
    if (empty($elementor_colors) || empty($theme_colors['colors'])) {
        return false;
    }
    
    // Check if primary color is synced
    foreach ($elementor_colors as $color) {
        if ($color['id'] === 'recruitpro_primary') {
            return $color['color'] === $theme_colors['colors']['primary'];
        }
    }
    
    return false;
}

/**
 * Add Elementor import/export support
 */
function recruitpro_add_elementor_import_export() {
    // Add theme-specific Elementor templates
    add_filter('elementor/template-library/sources/local/register_source', 'recruitpro_register_elementor_templates');
    
    // Export theme Elementor settings
    add_action('wp_ajax_recruitpro_export_elementor_settings', 'recruitpro_export_elementor_settings');
    
    // Import theme Elementor settings
    add_action('wp_ajax_recruitpro_import_elementor_settings', 'recruitpro_import_elementor_settings');
}
add_action('init', 'recruitpro_add_elementor_import_export');

/**
 * Register Elementor templates
 */
function recruitpro_register_elementor_templates($source) {
    // Add recruitment-specific templates
    $templates_dir = get_template_directory() . '/elementor-templates/';
    
    if (is_dir($templates_dir)) {
        $templates = glob($templates_dir . '*.json');
        
        foreach ($templates as $template_file) {
            $template_data = json_decode(file_get_contents($template_file), true);
            if ($template_data) {
                $source->add_template($template_data);
            }
        }
    }
    
    return $source;
}

/**
 * Export Elementor settings
 */
function recruitpro_export_elementor_settings() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $settings = array(
        'global_colors' => get_option('elementor_global_colors', array()),
        'global_fonts' => get_option('elementor_global_fonts', array()),
        'kit_settings' => get_option('elementor_active_kit', 0),
        'theme_colors_sync' => recruitpro_are_global_colors_synced(),
    );
    
    wp_send_json_success($settings);
}

/**
 * Import Elementor settings
 */
function recruitpro_import_elementor_settings() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    if (!isset($_POST['settings'])) {
        wp_send_json_error('No settings provided');
    }
    
    $settings = json_decode(stripslashes($_POST['settings']), true);
    
    if (isset($settings['global_colors'])) {
        update_option('elementor_global_colors', $settings['global_colors']);
    }
    
    if (isset($settings['global_fonts'])) {
        update_option('elementor_global_fonts', $settings['global_fonts']);
    }
    
    wp_send_json_success('Settings imported successfully');
}

/**
 * Add Elementor debug information
 */
function recruitpro_add_elementor_debug_info() {
    if (!recruitpro_should_enable_debug_tools() || !defined('ELEMENTOR_VERSION')) {
        return;
    }
    
    add_action('wp_footer', function() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $post_id = get_the_ID();
        $is_elementor = \Elementor\Plugin::$instance->documents->get($post_id)->is_built_with_elementor();
        
        ?>
        <script>
        if (typeof console !== 'undefined') {
            console.group('RecruitPro Elementor Debug');
            console.log('Elementor Version: <?php echo ELEMENTOR_VERSION; ?>');
            console.log('Elementor Pro: <?php echo defined('ELEMENTOR_PRO_VERSION') ? ELEMENTOR_PRO_VERSION : 'Not Active'; ?>');
            console.log('Built with Elementor: <?php echo $is_elementor ? 'Yes' : 'No'; ?>');
            console.log('Active Kit ID: <?php echo get_option('elementor_active_kit', 0); ?>');
            console.log('Global Colors Synced: <?php echo recruitpro_are_global_colors_synced() ? 'Yes' : 'No'; ?>');
            console.groupEnd();
        }
        </script>
        <?php
    });
}
add_action('init', 'recruitpro_add_elementor_debug_info');

/**
 * Template functions for Elementor integration
 */

/**
 * Check if Elementor is active and supported
 */
function recruitpro_is_elementor_active() {
    return defined('ELEMENTOR_VERSION');
}

/**
 * Check if Elementor Pro is active
 */
function recruitpro_is_elementor_pro_active() {
    return defined('ELEMENTOR_PRO_VERSION');
}

/**
 * Check if current page is built with Elementor
 */
function recruitpro_is_elementor_page() {
    if (!recruitpro_is_elementor_active() || !is_singular()) {
        return false;
    }
    
    $post_id = get_the_ID();
    return \Elementor\Plugin::$instance->documents->get($post_id)->is_built_with_elementor();
}

/**
 * Get Elementor page content
 */
function recruitpro_get_elementor_content($post_id = null) {
    if (!recruitpro_is_elementor_active()) {
        return '';
    }
    
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return \Elementor\Plugin::$instance->frontend->get_builder_content($post_id);
}

/**
 * Display Elementor content
 */
function recruitpro_elementor_content($post_id = null) {
    echo recruitpro_get_elementor_content($post_id);
}

/**
 * Check if Elementor header is active
 */
function recruitpro_has_elementor_header() {
    if (!recruitpro_is_elementor_pro_active()) {
        return false;
    }
    
    $header_id = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('header');
    return !empty($header_id);
}

/**
 * Check if Elementor footer is active
 */
function recruitpro_has_elementor_footer() {
    if (!recruitpro_is_elementor_pro_active()) {
        return false;
    }
    
    $footer_id = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('footer');
    return !empty($footer_id);
}