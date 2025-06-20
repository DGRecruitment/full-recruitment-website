<?php
/**
 * RecruitPro Theme Color Schemes
 *
 * Manages color schemes, palettes, and dynamic color generation
 * Provides professional color combinations suitable for recruitment websites
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize color scheme system
 */
function recruitpro_init_color_schemes() {
    // Register default color schemes
    recruitpro_register_default_color_schemes();
    
    // Setup customizer integration
    add_action('customize_register', 'recruitpro_color_scheme_customizer');
    
    // Generate dynamic CSS
    add_action('wp_head', 'recruitpro_output_color_scheme_css');
    
    // Add admin color scheme preview
    add_action('admin_head', 'recruitpro_admin_color_preview');
    
    // Handle color scheme switching
    add_action('wp_ajax_recruitpro_preview_color_scheme', 'recruitpro_ajax_preview_color_scheme');
}
add_action('after_setup_theme', 'recruitpro_init_color_schemes');

/**
 * Register default color schemes
 */
function recruitpro_register_default_color_schemes() {
    $schemes = array(
        'professional_blue' => array(
            'label' => esc_html__('Professional Blue', 'recruitpro'),
            'colors' => array(
                'primary'         => '#0073aa',
                'secondary'       => '#005177',
                'accent'          => '#00a0d2',
                'text_primary'    => '#333333',
                'text_secondary'  => '#666666',
                'text_light'      => '#999999',
                'background'      => '#ffffff',
                'background_alt'  => '#f8f9fa',
                'border'          => '#e1e1e1',
                'success'         => '#28a745',
                'warning'         => '#ffc107',
                'error'           => '#dc3545',
                'info'            => '#17a2b8',
            ),
        ),
        'corporate_green' => array(
            'label' => esc_html__('Corporate Green', 'recruitpro'),
            'colors' => array(
                'primary'         => '#28a745',
                'secondary'       => '#1e7e34',
                'accent'          => '#20c997',
                'text_primary'    => '#212529',
                'text_secondary'  => '#6c757d',
                'text_light'      => '#adb5bd',
                'background'      => '#ffffff',
                'background_alt'  => '#f8f9fa',
                'border'          => '#dee2e6',
                'success'         => '#28a745',
                'warning'         => '#ffc107',
                'error'           => '#dc3545',
                'info'            => '#17a2b8',
            ),
        ),
        'executive_navy' => array(
            'label' => esc_html__('Executive Navy', 'recruitpro'),
            'colors' => array(
                'primary'         => '#1a237e',
                'secondary'       => '#3949ab',
                'accent'          => '#5c6bc0',
                'text_primary'    => '#212121',
                'text_secondary'  => '#424242',
                'text_light'      => '#757575',
                'background'      => '#ffffff',
                'background_alt'  => '#fafafa',
                'border'          => '#e0e0e0',
                'success'         => '#4caf50',
                'warning'         => '#ff9800',
                'error'           => '#f44336',
                'info'            => '#2196f3',
            ),
        ),
        'modern_purple' => array(
            'label' => esc_html__('Modern Purple', 'recruitpro'),
            'colors' => array(
                'primary'         => '#6f42c1',
                'secondary'       => '#5a2d91',
                'accent'          => '#8c7ae6',
                'text_primary'    => '#2c3e50',
                'text_secondary'  => '#5a6c7d',
                'text_light'      => '#95a5a6',
                'background'      => '#ffffff',
                'background_alt'  => '#f8f9fc',
                'border'          => '#e9ecef',
                'success'         => '#27ae60',
                'warning'         => '#f39c12',
                'error'           => '#e74c3c',
                'info'            => '#3498db',
            ),
        ),
        'warm_orange' => array(
            'label' => esc_html__('Warm Orange', 'recruitpro'),
            'colors' => array(
                'primary'         => '#fd7e14',
                'secondary'       => '#e8590c',
                'accent'          => '#ff8c42',
                'text_primary'    => '#343a40',
                'text_secondary'  => '#6c757d',
                'text_light'      => '#adb5bd',
                'background'      => '#ffffff',
                'background_alt'  => '#fff8f5',
                'border'          => '#dee2e6',
                'success'         => '#28a745',
                'warning'         => '#ffc107',
                'error'           => '#dc3545',
                'info'            => '#17a2b8',
            ),
        ),
        'elegant_gray' => array(
            'label' => esc_html__('Elegant Gray', 'recruitpro'),
            'colors' => array(
                'primary'         => '#495057',
                'secondary'       => '#343a40',
                'accent'          => '#6c757d',
                'text_primary'    => '#212529',
                'text_secondary'  => '#495057',
                'text_light'      => '#6c757d',
                'background'      => '#ffffff',
                'background_alt'  => '#f8f9fa',
                'border'          => '#e9ecef',
                'success'         => '#28a745',
                'warning'         => '#ffc107',
                'error'           => '#dc3545',
                'info'            => '#17a2b8',
            ),
        ),
    );
    
    // Allow themes and plugins to register additional schemes
    $schemes = apply_filters('recruitpro_color_schemes', $schemes);
    
    // Store schemes in global
    $GLOBALS['recruitpro_color_schemes'] = $schemes;
}

/**
 * Get registered color schemes
 */
function recruitpro_get_color_schemes() {
    if (!isset($GLOBALS['recruitpro_color_schemes'])) {
        recruitpro_register_default_color_schemes();
    }
    
    return $GLOBALS['recruitpro_color_schemes'];
}

/**
 * Get current color scheme
 */
function recruitpro_get_current_color_scheme() {
    $current_scheme = get_theme_mod('recruitpro_color_scheme', 'professional_blue');
    $schemes = recruitpro_get_color_schemes();
    
    return isset($schemes[$current_scheme]) ? $schemes[$current_scheme] : $schemes['professional_blue'];
}

/**
 * Get specific color from current scheme
 */
function recruitpro_get_color($color_name, $default = '#000000') {
    $scheme = recruitpro_get_current_color_scheme();
    
    if (isset($scheme['colors'][$color_name])) {
        return $scheme['colors'][$color_name];
    }
    
    // Try custom colors
    $custom_color = get_theme_mod('recruitpro_custom_' . $color_name);
    if ($custom_color) {
        return $custom_color;
    }
    
    return $default;
}

/**
 * Add color scheme customizer controls
 */
function recruitpro_color_scheme_customizer($wp_customize) {
    // Color Scheme section
    $wp_customize->add_section('recruitpro_color_scheme', array(
        'title'    => esc_html__('Color Scheme', 'recruitpro'),
        'priority' => 40,
    ));
    
    // Color scheme selector
    $wp_customize->add_setting('recruitpro_color_scheme', array(
        'default'           => 'professional_blue',
        'sanitize_callback' => 'recruitpro_sanitize_color_scheme',
        'transport'         => 'postMessage',
    ));
    
    $schemes = recruitpro_get_color_schemes();
    $scheme_choices = array();
    foreach ($schemes as $key => $scheme) {
        $scheme_choices[$key] = $scheme['label'];
    }
    
    $wp_customize->add_control('recruitpro_color_scheme', array(
        'label'    => esc_html__('Choose Color Scheme', 'recruitpro'),
        'section'  => 'recruitpro_color_scheme',
        'type'     => 'select',
        'choices'  => $scheme_choices,
    ));
    
    // Custom color overrides
    $color_settings = array(
        'primary'         => esc_html__('Primary Color', 'recruitpro'),
        'secondary'       => esc_html__('Secondary Color', 'recruitpro'),
        'accent'          => esc_html__('Accent Color', 'recruitpro'),
        'text_primary'    => esc_html__('Primary Text', 'recruitpro'),
        'text_secondary'  => esc_html__('Secondary Text', 'recruitpro'),
        'background'      => esc_html__('Background', 'recruitpro'),
        'background_alt'  => esc_html__('Alternate Background', 'recruitpro'),
    );
    
    foreach ($color_settings as $setting => $label) {
        $wp_customize->add_setting('recruitpro_custom_' . $setting, array(
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ));
        
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'recruitpro_custom_' . $setting, array(
            'label'       => $label,
            'section'     => 'recruitpro_color_scheme',
            'description' => sprintf(esc_html__('Override the %s from the selected scheme', 'recruitpro'), strtolower($label)),
        )));
    }
    
    // Enable dark mode toggle
    $wp_customize->add_setting('recruitpro_enable_dark_mode', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_dark_mode', array(
        'label'   => esc_html__('Enable Dark Mode Option', 'recruitpro'),
        'section' => 'recruitpro_color_scheme',
        'type'    => 'checkbox',
    ));
    
    // Color accessibility options
    $wp_customize->add_setting('recruitpro_high_contrast', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_high_contrast', array(
        'label'       => esc_html__('High Contrast Mode', 'recruitpro'),
        'section'     => 'recruitpro_color_scheme',
        'type'        => 'checkbox',
        'description' => esc_html__('Increases contrast for better accessibility', 'recruitpro'),
    ));
    
    // Add live preview script
    $wp_customize->add_setting('recruitpro_color_preview_script', array(
        'default' => '',
        'transport' => 'postMessage',
    ));
}

/**
 * Sanitize color scheme selection
 */
function recruitpro_sanitize_color_scheme($input) {
    $schemes = recruitpro_get_color_schemes();
    return array_key_exists($input, $schemes) ? $input : 'professional_blue';
}

/**
 * Output color scheme CSS
 */
function recruitpro_output_color_scheme_css() {
    $scheme = recruitpro_get_current_color_scheme();
    $high_contrast = get_theme_mod('recruitpro_high_contrast', false);
    $dark_mode_enabled = get_theme_mod('recruitpro_enable_dark_mode', false);
    
    ?>
    <style id="recruitpro-color-scheme-css">
    :root {
        /* Color Scheme Variables */
        --recruitpro-primary: <?php echo esc_html(recruitpro_get_color('primary')); ?>;
        --recruitpro-secondary: <?php echo esc_html(recruitpro_get_color('secondary')); ?>;
        --recruitpro-accent: <?php echo esc_html(recruitpro_get_color('accent')); ?>;
        --recruitpro-text-primary: <?php echo esc_html(recruitpro_get_color('text_primary')); ?>;
        --recruitpro-text-secondary: <?php echo esc_html(recruitpro_get_color('text_secondary')); ?>;
        --recruitpro-text-light: <?php echo esc_html(recruitpro_get_color('text_light')); ?>;
        --recruitpro-background: <?php echo esc_html(recruitpro_get_color('background')); ?>;
        --recruitpro-background-alt: <?php echo esc_html(recruitpro_get_color('background_alt')); ?>;
        --recruitpro-border: <?php echo esc_html(recruitpro_get_color('border')); ?>;
        --recruitpro-success: <?php echo esc_html(recruitpro_get_color('success')); ?>;
        --recruitpro-warning: <?php echo esc_html(recruitpro_get_color('warning')); ?>;
        --recruitpro-error: <?php echo esc_html(recruitpro_get_color('error')); ?>;
        --recruitpro-info: <?php echo esc_html(recruitpro_get_color('info')); ?>;
        
        /* Computed Colors */
        --recruitpro-primary-hover: <?php echo esc_html(recruitpro_adjust_color_brightness(recruitpro_get_color('primary'), -0.1)); ?>;
        --recruitpro-primary-light: <?php echo esc_html(recruitpro_adjust_color_brightness(recruitpro_get_color('primary'), 0.3)); ?>;
        --recruitpro-primary-dark: <?php echo esc_html(recruitpro_adjust_color_brightness(recruitpro_get_color('primary'), -0.2)); ?>;
        --recruitpro-text-on-primary: <?php echo esc_html(recruitpro_get_contrasting_text_color(recruitpro_get_color('primary'))); ?>;
        --recruitpro-text-on-secondary: <?php echo esc_html(recruitpro_get_contrasting_text_color(recruitpro_get_color('secondary'))); ?>;
        
        /* Shadow and Transparency */
        --recruitpro-box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        --recruitpro-box-shadow-hover: 0 4px 16px rgba(0, 0, 0, 0.15);
        --recruitpro-overlay: rgba(0, 0, 0, 0.5);
    }
    
    <?php if ($high_contrast) : ?>
    /* High Contrast Adjustments */
    :root {
        --recruitpro-border: #000000;
        --recruitpro-text-primary: #000000;
        --recruitpro-text-secondary: #333333;
        --recruitpro-box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
    
    .recruitpro-button,
    .button,
    input[type="submit"],
    input[type="button"] {
        border: 2px solid currentColor !important;
    }
    
    a:focus,
    button:focus,
    input:focus,
    textarea:focus,
    select:focus {
        outline: 3px solid var(--recruitpro-primary) !important;
        outline-offset: 2px !important;
    }
    <?php endif; ?>
    
    <?php if ($dark_mode_enabled) : ?>
    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        :root {
            --recruitpro-background: #1a1a1a;
            --recruitpro-background-alt: #2d2d2d;
            --recruitpro-text-primary: #ffffff;
            --recruitpro-text-secondary: #cccccc;
            --recruitpro-text-light: #999999;
            --recruitpro-border: #404040;
            --recruitpro-box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
            --recruitpro-overlay: rgba(255, 255, 255, 0.1);
        }
    }
    
    [data-theme="dark"] {
        --recruitpro-background: #1a1a1a;
        --recruitpro-background-alt: #2d2d2d;
        --recruitpro-text-primary: #ffffff;
        --recruitpro-text-secondary: #cccccc;
        --recruitpro-text-light: #999999;
        --recruitpro-border: #404040;
        --recruitpro-box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
    }
    <?php endif; ?>
    
    /* Apply Colors to Elements */
    body {
        background-color: var(--recruitpro-background);
        color: var(--recruitpro-text-primary);
    }
    
    .site-header {
        background-color: var(--recruitpro-background);
        border-bottom: 1px solid var(--recruitpro-border);
    }
    
    .site-footer {
        background-color: var(--recruitpro-background-alt);
        border-top: 1px solid var(--recruitpro-border);
    }
    
    .recruitpro-button,
    .button-primary,
    input[type="submit"] {
        background-color: var(--recruitpro-primary);
        color: var(--recruitpro-text-on-primary);
        border-color: var(--recruitpro-primary);
    }
    
    .recruitpro-button:hover,
    .button-primary:hover,
    input[type="submit"]:hover {
        background-color: var(--recruitpro-primary-hover);
        border-color: var(--recruitpro-primary-hover);
    }
    
    .recruitpro-button-secondary {
        background-color: var(--recruitpro-secondary);
        color: var(--recruitpro-text-on-secondary);
        border-color: var(--recruitpro-secondary);
    }
    
    .recruitpro-button-outline {
        background-color: transparent;
        color: var(--recruitpro-primary);
        border: 2px solid var(--recruitpro-primary);
    }
    
    .recruitpro-button-outline:hover {
        background-color: var(--recruitpro-primary);
        color: var(--recruitpro-text-on-primary);
    }
    
    a {
        color: var(--recruitpro-primary);
    }
    
    a:hover,
    a:focus {
        color: var(--recruitpro-primary-hover);
    }
    
    h1, h2, h3, h4, h5, h6 {
        color: var(--recruitpro-text-primary);
    }
    
    .job-title {
        color: var(--recruitpro-primary);
        border-bottom-color: var(--recruitpro-primary);
    }
    
    .job-requirements {
        border-left-color: var(--recruitpro-success);
    }
    
    .job-benefits {
        border-left-color: var(--recruitpro-info);
    }
    
    .highlight-box {
        background-color: var(--recruitpro-primary-light);
        border-color: var(--recruitpro-primary);
    }
    
    .company-info {
        background-color: var(--recruitpro-background-alt);
        border-color: var(--recruitpro-border);
    }
    
    .recruitpro-card {
        background-color: var(--recruitpro-background);
        border: 1px solid var(--recruitpro-border);
        box-shadow: var(--recruitpro-box-shadow);
    }
    
    .recruitpro-card:hover {
        box-shadow: var(--recruitpro-box-shadow-hover);
    }
    
    .alert-success {
        background-color: var(--recruitpro-success);
        color: white;
    }
    
    .alert-warning {
        background-color: var(--recruitpro-warning);
        color: var(--recruitpro-text-primary);
    }
    
    .alert-error {
        background-color: var(--recruitpro-error);
        color: white;
    }
    
    .alert-info {
        background-color: var(--recruitpro-info);
        color: white;
    }
    
    /* Form Elements */
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="url"],
    textarea,
    select {
        background-color: var(--recruitpro-background);
        border: 1px solid var(--recruitpro-border);
        color: var(--recruitpro-text-primary);
    }
    
    input:focus,
    textarea:focus,
    select:focus {
        border-color: var(--recruitpro-primary);
        box-shadow: 0 0 0 2px var(--recruitpro-primary-light);
    }
    
    /* Navigation */
    .main-navigation a {
        color: var(--recruitpro-text-primary);
    }
    
    .main-navigation a:hover {
        color: var(--recruitpro-primary);
    }
    
    .main-navigation .current-menu-item > a {
        color: var(--recruitpro-primary);
    }
    
    /* Widget Areas */
    .widget {
        background-color: var(--recruitpro-background-alt);
        border: 1px solid var(--recruitpro-border);
    }
    
    .widget-title {
        color: var(--recruitpro-text-primary);
        border-bottom: 2px solid var(--recruitpro-primary);
    }
    </style>
    <?php
}

/**
 * Adjust color brightness
 */
function recruitpro_adjust_color_brightness($hex, $adjustment) {
    // Remove # if present
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust brightness
    $r = max(0, min(255, $r + ($adjustment * 255)));
    $g = max(0, min(255, $g + ($adjustment * 255)));
    $b = max(0, min(255, $b + ($adjustment * 255)));
    
    // Convert back to hex
    return sprintf('#%02x%02x%02x', round($r), round($g), round($b));
}

/**
 * Get contrasting text color (black or white)
 */
function recruitpro_get_contrasting_text_color($hex) {
    // Remove # if present
    $hex = ltrim($hex, '#');
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Calculate luminance
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    
    // Return black for light colors, white for dark colors
    return $luminance > 0.5 ? '#000000' : '#ffffff';
}

/**
 * Generate color palette variations
 */
function recruitpro_generate_color_palette($base_color, $count = 5) {
    $palette = array();
    $step = 2 / ($count - 1); // Range from -1 to +1
    
    for ($i = 0; $i < $count; $i++) {
        $adjustment = -1 + ($i * $step);
        $palette[] = recruitpro_adjust_color_brightness($base_color, $adjustment * 0.3);
    }
    
    return $palette;
}

/**
 * Check color accessibility (WCAG compliance)
 */
function recruitpro_check_color_accessibility($foreground, $background) {
    $fg_luminance = recruitpro_get_color_luminance($foreground);
    $bg_luminance = recruitpro_get_color_luminance($background);
    
    $contrast_ratio = ($fg_luminance + 0.05) / ($bg_luminance + 0.05);
    if ($bg_luminance > $fg_luminance) {
        $contrast_ratio = 1 / $contrast_ratio;
    }
    
    return array(
        'ratio' => $contrast_ratio,
        'aa_normal' => $contrast_ratio >= 4.5,
        'aa_large' => $contrast_ratio >= 3,
        'aaa_normal' => $contrast_ratio >= 7,
        'aaa_large' => $contrast_ratio >= 4.5,
    );
}

/**
 * Get color luminance
 */
function recruitpro_get_color_luminance($hex) {
    $hex = ltrim($hex, '#');
    
    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;
    
    // Convert to linear RGB
    $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
    $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
    $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
    
    return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
}

/**
 * Add admin color preview
 */
function recruitpro_admin_color_preview() {
    $screen = get_current_screen();
    
    if ($screen && $screen->id === 'appearance_page_recruitpro-theme-options') {
        $scheme = recruitpro_get_current_color_scheme();
        ?>
        <style>
        .recruitpro-color-preview {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .color-swatch {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        .color-swatch::after {
            content: attr(data-color);
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            white-space: nowrap;
        }
        </style>
        
        <div class="recruitpro-color-preview">
            <?php foreach ($scheme['colors'] as $name => $color) : ?>
                <div class="color-swatch" style="background-color: <?php echo esc_attr($color); ?>" data-color="<?php echo esc_attr($name); ?>" title="<?php echo esc_attr($color); ?>"></div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

/**
 * AJAX handler for color scheme preview
 */
function recruitpro_ajax_preview_color_scheme() {
    if (!current_user_can('customize')) {
        wp_die('Unauthorized');
    }
    
    $scheme_id = sanitize_text_field($_POST['scheme']);
    $schemes = recruitpro_get_color_schemes();
    
    if (!isset($schemes[$scheme_id])) {
        wp_die('Invalid scheme');
    }
    
    $scheme = $schemes[$scheme_id];
    
    // Generate preview CSS
    ob_start();
    ?>
    :root {
        --recruitpro-primary: <?php echo esc_html($scheme['colors']['primary']); ?>;
        --recruitpro-secondary: <?php echo esc_html($scheme['colors']['secondary']); ?>;
        --recruitpro-accent: <?php echo esc_html($scheme['colors']['accent']); ?>;
        --recruitpro-text-primary: <?php echo esc_html($scheme['colors']['text_primary']); ?>;
        --recruitpro-background: <?php echo esc_html($scheme['colors']['background']); ?>;
    }
    <?php
    $css = ob_get_clean();
    
    wp_send_json_success(array(
        'css' => $css,
        'scheme' => $scheme,
    ));
}

/**
 * Add dark mode toggle functionality
 */
function recruitpro_add_dark_mode_toggle() {
    if (!get_theme_mod('recruitpro_enable_dark_mode', false)) {
        return;
    }
    
    ?>
    <button id="recruitpro-dark-mode-toggle" class="dark-mode-toggle" aria-label="<?php esc_attr_e('Toggle dark mode', 'recruitpro'); ?>">
        <span class="light-icon">☀️</span>
        <span class="dark-icon">🌙</span>
    </button>
    
    <script>
    (function() {
        const toggle = document.getElementById('recruitpro-dark-mode-toggle');
        const body = document.body;
        
        // Check for saved preference or default to light mode
        const currentTheme = localStorage.getItem('recruitpro-theme') || 'light';
        body.setAttribute('data-theme', currentTheme);
        
        toggle.addEventListener('click', function() {
            const theme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-theme', theme);
            localStorage.setItem('recruitpro-theme', theme);
        });
    })();
    </script>
    
    <style>
    .dark-mode-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background: var(--recruitpro-primary);
        color: var(--recruitpro-text-on-primary);
        cursor: pointer;
        z-index: 1000;
        box-shadow: var(--recruitpro-box-shadow);
        transition: all 0.3s ease;
    }
    
    .dark-mode-toggle:hover {
        transform: scale(1.1);
        box-shadow: var(--recruitpro-box-shadow-hover);
    }
    
    [data-theme="light"] .dark-icon {
        display: none;
    }
    
    [data-theme="dark"] .light-icon {
        display: none;
    }
    </style>
    <?php
}
add_action('wp_footer', 'recruitpro_add_dark_mode_toggle');

/**
 * Export color scheme as CSS variables
 */
function recruitpro_export_color_scheme_variables() {
    $scheme = recruitpro_get_current_color_scheme();
    $variables = array();
    
    foreach ($scheme['colors'] as $name => $color) {
        $variables['--recruitpro-' . str_replace('_', '-', $name)] = $color;
    }
    
    return $variables;
}

/**
 * Template function to get current color
 */
function recruitpro_color($name, $default = '#000000') {
    return recruitpro_get_color($name, $default);
}

/**
 * Template function to output color CSS variable
 */
function recruitpro_color_var($name) {
    echo 'var(--recruitpro-' . esc_attr(str_replace('_', '-', $name)) . ')';
}

/**
 * Get color scheme for Elementor integration
 */
function recruitpro_get_elementor_color_scheme() {
    $scheme = recruitpro_get_current_color_scheme();
    $elementor_colors = array();
    
    $color_mapping = array(
        'primary' => 'primary',
        'secondary' => 'secondary', 
        'text_primary' => 'text',
        'accent' => 'accent',
    );
    
    foreach ($color_mapping as $recruitpro_color => $elementor_color) {
        if (isset($scheme['colors'][$recruitpro_color])) {
            $elementor_colors[$elementor_color] = $scheme['colors'][$recruitpro_color];
        }
    }
    
    return $elementor_colors;
}

/**
 * Clear color scheme cache when changed
 */
function recruitpro_clear_color_scheme_cache($option_name) {
    if (strpos($option_name, 'recruitpro_color') !== false || strpos($option_name, 'recruitpro_custom') !== false) {
        // Clear any cached CSS
        delete_transient('recruitpro_color_scheme_css');
        
        // Clear external cache if available
        if (function_exists('recruitpro_clear_external_cache')) {
            recruitpro_clear_external_cache();
        }
    }
}
add_action('updated_option', 'recruitpro_clear_color_scheme_cache');