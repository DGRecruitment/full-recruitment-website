<?php
/**
 * RecruitPro Theme Compatibility
 *
 * Ensures compatibility with WordPress versions, plugins, browsers, and server environments
 * Provides fallbacks and graceful degradation for better user experience
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize compatibility checks and fixes
 */
function recruitpro_init_compatibility() {
    // WordPress version compatibility
    recruitpro_check_wordpress_compatibility();
    
    // PHP version compatibility
    recruitpro_check_php_compatibility();
    
    // Plugin compatibility
    recruitpro_setup_plugin_compatibility();
    
    // Browser compatibility
    recruitpro_setup_browser_compatibility();
    
    // Server environment compatibility
    recruitpro_check_server_compatibility();
    
    // Theme compatibility
    recruitpro_setup_theme_compatibility();
}
add_action('after_setup_theme', 'recruitpro_init_compatibility');

/**
 * WordPress version compatibility
 */
function recruitpro_check_wordpress_compatibility() {
    $min_wp_version = '5.0';
    $current_version = get_bloginfo('version');
    
    if (version_compare($current_version, $min_wp_version, '<')) {
        add_action('admin_notices', 'recruitpro_wordpress_version_notice');
        
        // Disable features that require newer WordPress
        remove_theme_support('block-templates');
        remove_theme_support('block-template-parts');
        
        // Add compatibility shims
        recruitpro_add_wordpress_compatibility_shims();
    }
    
    // WordPress 6.0+ compatibility
    if (version_compare($current_version, '6.0', '>=')) {
        // Enable newer features
        add_theme_support('appearance-tools');
        add_theme_support('border');
        add_theme_support('link-color');
        add_theme_support('spacing');
        add_theme_support('typography');
    }
    
    // WordPress 5.8+ compatibility for block widgets
    if (version_compare($current_version, '5.8', '>=')) {
        add_theme_support('widgets-block-editor');
    }
}

/**
 * WordPress version notice
 */
function recruitpro_wordpress_version_notice() {
    ?>
    <div class="notice notice-warning">
        <p>
            <strong><?php esc_html_e('RecruitPro Theme Warning:', 'recruitpro'); ?></strong>
            <?php
            printf(
                esc_html__('This theme requires WordPress 5.0 or higher. You are running WordPress %s. Please update WordPress for the best experience.', 'recruitpro'),
                esc_html(get_bloginfo('version'))
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Add WordPress compatibility shims
 */
function recruitpro_add_wordpress_compatibility_shims() {
    // Add wp_body_open fallback for WordPress < 5.2
    if (!function_exists('wp_body_open')) {
        function wp_body_open() {
            do_action('wp_body_open');
        }
    }
    
    // Add wp_is_block_theme fallback for WordPress < 5.9
    if (!function_exists('wp_is_block_theme')) {
        function wp_is_block_theme() {
            return false;
        }
    }
    
    // Add get_block_wrapper_attributes fallback for WordPress < 5.6
    if (!function_exists('get_block_wrapper_attributes')) {
        function get_block_wrapper_attributes($extra_attributes = array()) {
            $attributes = array();
            
            foreach ($extra_attributes as $attribute_name => $attribute_value) {
                $attributes[] = $attribute_name . '="' . esc_attr($attribute_value) . '"';
            }
            
            return implode(' ', $attributes);
        }
    }
}

/**
 * PHP version compatibility
 */
function recruitpro_check_php_compatibility() {
    $min_php_version = '7.4';
    $current_version = PHP_VERSION;
    
    if (version_compare($current_version, $min_php_version, '<')) {
        add_action('admin_notices', 'recruitpro_php_version_notice');
        
        // Disable features that require newer PHP
        recruitpro_disable_modern_php_features();
    }
    
    // PHP 8.0+ compatibility
    if (version_compare($current_version, '8.0', '>=')) {
        // Enable PHP 8 specific optimizations
        recruitpro_enable_php8_optimizations();
    }
}

/**
 * PHP version notice
 */
function recruitpro_php_version_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <strong><?php esc_html_e('RecruitPro Theme Error:', 'recruitpro'); ?></strong>
            <?php
            printf(
                esc_html__('This theme requires PHP 7.4 or higher. You are running PHP %s. Please contact your hosting provider to upgrade PHP.', 'recruitpro'),
                esc_html(PHP_VERSION)
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Disable modern PHP features for older versions
 */
function recruitpro_disable_modern_php_features() {
    // Disable features that use PHP 7.4+ syntax
    remove_action('wp_head', 'recruitpro_output_schema_markup');
    
    // Add fallback functions for arrow functions and other modern syntax
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        // Provide fallbacks for array functions
        if (!function_exists('array_key_first')) {
            function array_key_first(array $arr) {
                foreach ($arr as $key => $unused) {
                    return $key;
                }
                return null;
            }
        }
        
        if (!function_exists('array_key_last')) {
            function array_key_last(array $arr) {
                return key(array_slice($arr, -1, 1, true));
            }
        }
    }
}

/**
 * Enable PHP 8 optimizations
 */
function recruitpro_enable_php8_optimizations() {
    // Enable opcode caching optimizations
    if (function_exists('opcache_get_status')) {
        $opcache_status = opcache_get_status();
        if ($opcache_status && $opcache_status['opcache_enabled']) {
            // Optimize for opcache
            add_filter('recruitpro_enable_advanced_caching', '__return_true');
        }
    }
    
    // Enable JIT if available
    if (function_exists('opcache_get_configuration')) {
        $config = opcache_get_configuration();
        if (isset($config['directives']['opcache.jit']) && $config['directives']['opcache.jit']) {
            add_filter('recruitpro_enable_jit_optimizations', '__return_true');
        }
    }
}

/**
 * Setup plugin compatibility
 */
function recruitpro_setup_plugin_compatibility() {
    // Elementor compatibility
    if (defined('ELEMENTOR_VERSION')) {
        recruitpro_setup_elementor_compatibility();
    }
    
    // WooCommerce compatibility
    if (class_exists('WooCommerce')) {
        recruitpro_setup_woocommerce_compatibility();
    }
    
    // Yoast SEO compatibility
    if (defined('WPSEO_VERSION')) {
        recruitpro_setup_yoast_compatibility();
    }
    
    // Contact Form 7 compatibility
    if (defined('WPCF7_VERSION')) {
        recruitpro_setup_cf7_compatibility();
    }
    
    // Gravity Forms compatibility
    if (class_exists('GFForms')) {
        recruitpro_setup_gravity_forms_compatibility();
    }
    
    // WPML compatibility
    if (defined('ICL_SITEPRESS_VERSION')) {
        recruitpro_setup_wpml_compatibility();
    }
    
    // bbPress compatibility
    if (function_exists('is_bbpress')) {
        recruitpro_setup_bbpress_compatibility();
    }
    
    // BuddyPress compatibility
    if (function_exists('is_buddypress')) {
        recruitpro_setup_buddypress_compatibility();
    }
    
    // WP Super Cache compatibility
    if (defined('WP_CACHE') && WP_CACHE) {
        recruitpro_setup_cache_plugin_compatibility();
    }
    
    // Jetpack compatibility
    if (defined('JETPACK__VERSION')) {
        recruitpro_setup_jetpack_compatibility();
    }
}

/**
 * Elementor compatibility
 */
function recruitpro_setup_elementor_compatibility() {
    // Register Elementor locations
    add_action('elementor/theme/register_locations', 'recruitpro_register_elementor_locations');
    
    // Elementor Pro Theme Builder compatibility
    if (defined('ELEMENTOR_PRO_VERSION')) {
        add_theme_support('elementor-pro');
        
        // Disable theme headers/footers when Elementor templates are active
        add_action('elementor/theme/before_do_header', function() {
            remove_action('recruitpro_header', 'recruitpro_site_header');
        });
        
        add_action('elementor/theme/before_do_footer', function() {
            remove_action('recruitpro_footer', 'recruitpro_site_footer');
        });
    }
    
    // Custom CSS for Elementor
    add_action('elementor/frontend/after_enqueue_styles', 'recruitpro_elementor_custom_css');
    
    // Elementor color scheme integration
    add_filter('elementor/system_info/environment/theme', 'recruitpro_elementor_theme_info');
}

/**
 * Register Elementor locations
 */
function recruitpro_register_elementor_locations($elementor_theme_manager) {
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
    $elementor_theme_manager->register_location('single');
    $elementor_theme_manager->register_location('archive');
}

/**
 * Elementor custom CSS
 */
function recruitpro_elementor_custom_css() {
    wp_add_inline_style('elementor-frontend', '
        .elementor-page .site-header,
        .elementor-page .site-footer {
            display: none;
        }
        .elementor-kit-' . get_option('elementor_active_kit') . ' {
            --e-global-color-primary: ' . recruitpro_get_color('primary') . ';
            --e-global-color-secondary: ' . recruitpro_get_color('secondary') . ';
            --e-global-color-text: ' . recruitpro_get_color('text_primary') . ';
            --e-global-color-accent: ' . recruitpro_get_color('accent') . ';
        }
    ');
}

/**
 * Elementor theme info
 */
function recruitpro_elementor_theme_info($theme_info) {
    $theme_info['recruitpro_compatibility'] = array(
        'label' => esc_html__('RecruitPro Compatibility', 'recruitpro'),
        'value' => esc_html__('Full', 'recruitpro'),
    );
    
    return $theme_info;
}

/**
 * WooCommerce compatibility
 */
function recruitpro_setup_woocommerce_compatibility() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Disable WooCommerce styles and use theme styles
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');
    
    // Custom WooCommerce CSS
    add_action('wp_enqueue_scripts', 'recruitpro_woocommerce_styles');
    
    // Remove WooCommerce wrapper
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    
    // Add theme wrapper
    add_action('woocommerce_before_main_content', 'recruitpro_woocommerce_wrapper_start', 10);
    add_action('woocommerce_after_main_content', 'recruitpro_woocommerce_wrapper_end', 10);
}

/**
 * WooCommerce wrapper start
 */
function recruitpro_woocommerce_wrapper_start() {
    echo '<div class="recruitpro-woocommerce-wrapper">';
}

/**
 * WooCommerce wrapper end
 */
function recruitpro_woocommerce_wrapper_end() {
    echo '</div>';
}

/**
 * WooCommerce styles
 */
function recruitpro_woocommerce_styles() {
    wp_enqueue_style('recruitpro-woocommerce', get_template_directory_uri() . '/assets/css/woocommerce.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
}

/**
 * Yoast SEO compatibility
 */
function recruitpro_setup_yoast_compatibility() {
    // Remove Yoast breadcrumbs if theme breadcrumbs are enabled
    if (function_exists('recruitpro_breadcrumbs')) {
        add_filter('wpseo_breadcrumb_output', '__return_false');
    }
    
    // Add theme color scheme to Yoast
    add_filter('wpseo_schema_website', 'recruitpro_yoast_schema_website');
    
    // Disable Yoast CSS if theme handles it
    add_action('template_redirect', function() {
        if (current_theme_supports('yoast-seo-breadcrumbs')) {
            wp_dequeue_style('yst_plugin_tools');
        }
    });
}

/**
 * Yoast schema website
 */
function recruitpro_yoast_schema_website($data) {
    $data['potentialAction'] = array(
        '@type' => 'SearchAction',
        'target' => home_url('/?s={search_term_string}'),
        'query-input' => 'required name=search_term_string'
    );
    
    return $data;
}

/**
 * Contact Form 7 compatibility
 */
function recruitpro_setup_cf7_compatibility() {
    // Remove CF7 default styles
    add_filter('wpcf7_load_css', '__return_false');
    
    // Add custom CF7 styles
    add_action('wp_enqueue_scripts', 'recruitpro_cf7_styles');
    
    // Custom CF7 form styling
    add_filter('wpcf7_form_class_attr', 'recruitpro_cf7_form_class');
}

/**
 * CF7 styles
 */
function recruitpro_cf7_styles() {
    if (function_exists('wpcf7_enqueue_scripts')) {
        wp_enqueue_style('recruitpro-cf7', get_template_directory_uri() . '/assets/css/contact-form-7.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
    }
}

/**
 * CF7 form class
 */
function recruitpro_cf7_form_class($class) {
    return $class . ' recruitpro-form';
}

/**
 * Gravity Forms compatibility
 */
function recruitpro_setup_gravity_forms_compatibility() {
    // Disable Gravity Forms CSS
    add_filter('pre_option_rg_gforms_disable_css', '__return_true');
    
    // Add custom Gravity Forms styles
    add_action('wp_enqueue_scripts', 'recruitpro_gravity_forms_styles');
    
    // Custom form wrapper
    add_filter('gform_form_tag', 'recruitpro_gform_form_tag', 10, 2);
}

/**
 * Gravity Forms styles
 */
function recruitpro_gravity_forms_styles() {
    if (class_exists('GFForms')) {
        wp_enqueue_style('recruitpro-gravity-forms', get_template_directory_uri() . '/assets/css/gravity-forms.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
    }
}

/**
 * Gravity Forms form tag
 */
function recruitpro_gform_form_tag($form_tag, $form) {
    return str_replace('gform_wrapper', 'gform_wrapper recruitpro-form-wrapper', $form_tag);
}

/**
 * WPML compatibility
 */
function recruitpro_setup_wpml_compatibility() {
    // Register theme strings for translation
    if (function_exists('icl_register_string')) {
        icl_register_string('RecruitPro Theme', 'Footer Copyright', get_theme_mod('recruitpro_footer_copyright', ''));
        icl_register_string('RecruitPro Theme', 'Homepage Hero Title', get_theme_mod('recruitpro_hero_title', ''));
        icl_register_string('RecruitPro Theme', 'Homepage Hero Subtitle', get_theme_mod('recruitpro_hero_subtitle', ''));
    }
    
    // Language switcher integration
    add_action('recruitpro_header', 'recruitpro_wpml_language_switcher');
    
    // WPML configuration
    add_filter('wpml_config_array', 'recruitpro_wpml_config');
}

/**
 * WPML language switcher
 */
function recruitpro_wpml_language_switcher() {
    if (function_exists('icl_get_languages')) {
        $languages = icl_get_languages('skip_missing=0&orderby=code');
        
        if (count($languages) > 1) {
            echo '<div class="recruitpro-language-switcher">';
            foreach ($languages as $l) {
                if ($l['active']) {
                    echo '<span class="current-language">' . esc_html($l['native_name']) . '</span>';
                } else {
                    echo '<a href="' . esc_url($l['url']) . '">' . esc_html($l['native_name']) . '</a>';
                }
            }
            echo '</div>';
        }
    }
}

/**
 * WPML configuration
 */
function recruitpro_wpml_config($config) {
    $config['wpml-config']['admin-texts']['key'][] = array(
        'key' => 'theme_mods_recruitpro',
        'attr' => array('encoding' => 'base64')
    );
    
    return $config;
}

/**
 * bbPress compatibility
 */
function recruitpro_setup_bbpress_compatibility() {
    add_theme_support('bbpress');
    
    // Custom bbPress styles
    add_action('wp_enqueue_scripts', 'recruitpro_bbpress_styles');
    
    // Remove bbPress default styles
    add_action('bbp_enqueue_scripts', function() {
        wp_dequeue_style('bbp-default');
    });
}

/**
 * bbPress styles
 */
function recruitpro_bbpress_styles() {
    if (function_exists('is_bbpress') && is_bbpress()) {
        wp_enqueue_style('recruitpro-bbpress', get_template_directory_uri() . '/assets/css/bbpress.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
    }
}

/**
 * BuddyPress compatibility
 */
function recruitpro_setup_buddypress_compatibility() {
    add_theme_support('buddypress');
    
    // Custom BuddyPress styles
    add_action('wp_enqueue_scripts', 'recruitpro_buddypress_styles');
}

/**
 * BuddyPress styles
 */
function recruitpro_buddypress_styles() {
    if (function_exists('is_buddypress') && is_buddypress()) {
        wp_enqueue_style('recruitpro-buddypress', get_template_directory_uri() . '/assets/css/buddypress.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
    }
}

/**
 * Cache plugin compatibility
 */
function recruitpro_setup_cache_plugin_compatibility() {
    // Add cache busting for dynamic content
    add_filter('recruitpro_dynamic_content_cache_key', 'recruitpro_cache_bust_dynamic_content');
    
    // Exclude dynamic pages from caching
    if (function_exists('wp_cache_add_non_persistent_groups')) {
        wp_cache_add_non_persistent_groups(array('recruitpro_dynamic'));
    }
}

/**
 * Cache bust dynamic content
 */
function recruitpro_cache_bust_dynamic_content($cache_key) {
    return $cache_key . '_' . current_time('timestamp');
}

/**
 * Jetpack compatibility
 */
function recruitpro_setup_jetpack_compatibility() {
    // Add theme support for Jetpack features
    add_theme_support('infinite-scroll', array(
        'container' => 'main',
        'render'    => 'recruitpro_infinite_scroll_render',
        'footer'    => 'page',
    ));
    
    add_theme_support('jetpack-responsive-videos');
    add_theme_support('jetpack-social-menu');
    
    // Custom Jetpack styles
    add_action('wp_enqueue_scripts', 'recruitpro_jetpack_styles');
}

/**
 * Jetpack infinite scroll render
 */
function recruitpro_infinite_scroll_render() {
    while (have_posts()) {
        the_post();
        get_template_part('template-parts/content', get_post_type());
    }
}

/**
 * Jetpack styles
 */
function recruitpro_jetpack_styles() {
    if (defined('JETPACK__VERSION')) {
        wp_enqueue_style('recruitpro-jetpack', get_template_directory_uri() . '/assets/css/jetpack.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
    }
}

/**
 * Browser compatibility
 */
function recruitpro_setup_browser_compatibility() {
    // Add browser detection
    add_action('wp_head', 'recruitpro_browser_detection');
    
    // Polyfills for older browsers
    add_action('wp_enqueue_scripts', 'recruitpro_browser_polyfills');
    
    // CSS fixes for specific browsers
    add_action('wp_head', 'recruitpro_browser_css_fixes');
}

/**
 * Browser detection
 */
function recruitpro_browser_detection() {
    ?>
    <script>
    (function() {
        var html = document.documentElement;
        var ua = navigator.userAgent.toLowerCase();
        
        // Add browser classes
        if (ua.indexOf('chrome') > -1 && ua.indexOf('edge') === -1) {
            html.className += ' chrome';
        } else if (ua.indexOf('firefox') > -1) {
            html.className += ' firefox';
        } else if (ua.indexOf('safari') > -1 && ua.indexOf('chrome') === -1) {
            html.className += ' safari';
        } else if (ua.indexOf('edge') > -1) {
            html.className += ' edge';
        } else if (ua.indexOf('trident') > -1) {
            html.className += ' ie';
        }
        
        // Add mobile detection
        if (/Mobi|Android/i.test(navigator.userAgent)) {
            html.className += ' mobile';
        }
        
        // Add touch detection
        if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
            html.className += ' touch';
        } else {
            html.className += ' no-touch';
        }
        
        // Feature detection
        if (typeof CSS !== 'undefined' && CSS.supports && CSS.supports('display', 'grid')) {
            html.className += ' css-grid';
        } else {
            html.className += ' no-css-grid';
        }
        
        if (typeof CSS !== 'undefined' && CSS.supports && CSS.supports('display', 'flex')) {
            html.className += ' css-flexbox';
        } else {
            html.className += ' no-css-flexbox';
        }
    })();
    </script>
    <?php
}

/**
 * Browser polyfills
 */
function recruitpro_browser_polyfills() {
    // Polyfill for older browsers
    wp_enqueue_script('recruitpro-polyfills', get_template_directory_uri() . '/assets/js/polyfills.js', array(), wp_get_theme()->get('Version'), false);
    
    // CSS Grid polyfill for IE
    wp_enqueue_script('css-grid-polyfill', 'https://cdnjs.cloudflare.com/ajax/libs/css-grid-polyfill/0.1.0/css-polyfills.min.js', array(), '0.1.0', false);
    wp_script_add_data('css-grid-polyfill', 'conditional', 'IE');
}

/**
 * Browser CSS fixes
 */
function recruitpro_browser_css_fixes() {
    ?>
    <style>
    /* Safari specific fixes */
    .safari .recruitpro-button {
        -webkit-appearance: none;
    }
    
    /* Firefox specific fixes */
    .firefox input[type="submit"] {
        -moz-appearance: none;
    }
    
    /* IE specific fixes */
    .ie .css-grid-fallback {
        display: block;
    }
    
    /* Mobile specific fixes */
    .mobile .recruitpro-navigation {
        position: relative;
    }
    
    /* Touch device fixes */
    .touch .hover-effect:hover {
        transform: none;
    }
    
    /* CSS Grid fallback */
    .no-css-grid .grid-layout {
        display: flex;
        flex-wrap: wrap;
    }
    
    .no-css-grid .grid-item {
        flex: 1 1 300px;
        margin: 10px;
    }
    
    /* Flexbox fallback */
    .no-css-flexbox .flex-layout {
        display: table;
        width: 100%;
    }
    
    .no-css-flexbox .flex-item {
        display: table-cell;
        vertical-align: top;
    }
    </style>
    <?php
}

/**
 * Server environment compatibility
 */
function recruitpro_check_server_compatibility() {
    // Check memory limit
    $memory_limit = ini_get('memory_limit');
    if ($memory_limit && intval($memory_limit) < 256) {
        add_action('admin_notices', 'recruitpro_memory_limit_notice');
    }
    
    // Check max execution time
    $max_execution_time = ini_get('max_execution_time');
    if ($max_execution_time && intval($max_execution_time) < 60) {
        add_action('admin_notices', 'recruitpro_execution_time_notice');
    }
    
    // Check required extensions
    recruitpro_check_required_extensions();
    
    // Check file permissions
    recruitpro_check_file_permissions();
}

/**
 * Memory limit notice
 */
function recruitpro_memory_limit_notice() {
    ?>
    <div class="notice notice-warning">
        <p>
            <strong><?php esc_html_e('RecruitPro Theme Warning:', 'recruitpro'); ?></strong>
            <?php esc_html_e('Your server\'s memory limit is below the recommended 256M. This may cause issues with theme functionality.', 'recruitpro'); ?>
        </p>
    </div>
    <?php
}

/**
 * Execution time notice
 */
function recruitpro_execution_time_notice() {
    ?>
    <div class="notice notice-warning">
        <p>
            <strong><?php esc_html_e('RecruitPro Theme Warning:', 'recruitpro'); ?></strong>
            <?php esc_html_e('Your server\'s max execution time is below the recommended 60 seconds. This may cause timeouts during theme operations.', 'recruitpro'); ?>
        </p>
    </div>
    <?php
}

/**
 * Check required PHP extensions
 */
function recruitpro_check_required_extensions() {
    $required_extensions = array(
        'gd' => esc_html__('Image processing', 'recruitpro'),
        'curl' => esc_html__('External API calls', 'recruitpro'),
        'mbstring' => esc_html__('Multi-byte string handling', 'recruitpro'),
        'json' => esc_html__('JSON data processing', 'recruitpro'),
        'zip' => esc_html__('File compression', 'recruitpro'),
    );
    
    $missing_extensions = array();
    
    foreach ($required_extensions as $extension => $description) {
        if (!extension_loaded($extension)) {
            $missing_extensions[$extension] = $description;
        }
    }
    
    if (!empty($missing_extensions)) {
        add_action('admin_notices', function() use ($missing_extensions) {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php esc_html_e('RecruitPro Theme Error:', 'recruitpro'); ?></strong>
                    <?php esc_html_e('The following required PHP extensions are missing:', 'recruitpro'); ?>
                </p>
                <ul>
                    <?php foreach ($missing_extensions as $extension => $description) : ?>
                        <li><?php echo esc_html($extension . ' - ' . $description); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><?php esc_html_e('Please contact your hosting provider to install these extensions.', 'recruitpro'); ?></p>
            </div>
            <?php
        });
    }
}

/**
 * Check file permissions
 */
function recruitpro_check_file_permissions() {
    $upload_dir = wp_upload_dir();
    
    if (!wp_is_writable($upload_dir['basedir'])) {
        add_action('admin_notices', 'recruitpro_file_permissions_notice');
    }
}

/**
 * File permissions notice
 */
function recruitpro_file_permissions_notice() {
    ?>
    <div class="notice notice-warning">
        <p>
            <strong><?php esc_html_e('RecruitPro Theme Warning:', 'recruitpro'); ?></strong>
            <?php esc_html_e('The uploads directory is not writable. Some theme features may not work properly.', 'recruitpro'); ?>
        </p>
    </div>
    <?php
}

/**
 * Setup theme compatibility
 */
function recruitpro_setup_theme_compatibility() {
    // Child theme compatibility
    if (is_child_theme()) {
        recruitpro_setup_child_theme_compatibility();
    }
    
    // Theme switching compatibility
    add_action('switch_theme', 'recruitpro_theme_switch_cleanup');
    
    // Plugin conflict detection
    add_action('admin_init', 'recruitpro_detect_plugin_conflicts');
}

/**
 * Child theme compatibility
 */
function recruitpro_setup_child_theme_compatibility() {
    // Ensure child theme can override parent functions
    if (!function_exists('recruitpro_child_theme_setup')) {
        function recruitpro_child_theme_setup() {
            // Hook for child theme setup
            do_action('recruitpro_child_theme_init');
        }
        add_action('after_setup_theme', 'recruitpro_child_theme_setup', 15);
    }
}

/**
 * Theme switch cleanup
 */
function recruitpro_theme_switch_cleanup() {
    // Clear theme-specific caches
    delete_transient('recruitpro_compatibility_check');
    
    // Clear any theme-specific options if needed
    if (get_option('recruitpro_clear_data_on_switch')) {
        delete_option('recruitpro_customizer_backup');
    }
}

/**
 * Detect plugin conflicts
 */
function recruitpro_detect_plugin_conflicts() {
    $conflicting_plugins = array(
        'some-conflicting-plugin/plugin.php' => esc_html__('This plugin may cause styling conflicts', 'recruitpro'),
    );
    
    foreach ($conflicting_plugins as $plugin => $message) {
        if (is_plugin_active($plugin)) {
            add_action('admin_notices', function() use ($plugin, $message) {
                ?>
                <div class="notice notice-warning">
                    <p>
                        <strong><?php esc_html_e('RecruitPro Theme Warning:', 'recruitpro'); ?></strong>
                        <?php echo esc_html($message); ?>
                    </p>
                </div>
                <?php
            });
        }
    }
}

/**
 * Get compatibility status
 */
function recruitpro_get_compatibility_status() {
    $status = array(
        'wordpress' => version_compare(get_bloginfo('version'), '5.0', '>='),
        'php' => version_compare(PHP_VERSION, '7.4', '>='),
        'memory' => intval(ini_get('memory_limit')) >= 256,
        'extensions' => extension_loaded('gd') && extension_loaded('curl'),
        'permissions' => wp_is_writable(wp_upload_dir()['basedir']),
    );
    
    return $status;
}

/**
 * Display compatibility status in admin
 */
function recruitpro_display_compatibility_status() {
    $status = recruitpro_get_compatibility_status();
    
    ?>
    <div class="postbox">
        <h2 class="hndle"><?php esc_html_e('Compatibility Status', 'recruitpro'); ?></h2>
        <div class="inside">
            <div class="recruitpro-compatibility-status">
                <?php foreach ($status as $check => $passed) : ?>
                    <div class="compatibility-item">
                        <span><?php echo esc_html(ucfirst(str_replace('_', ' ', $check))); ?></span>
                        <span class="status-badge <?php echo $passed ? 'status-pass' : 'status-fail'; ?>">
                            <?php echo $passed ? '✓' : '✗'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <style>
    .recruitpro-compatibility-status .compatibility-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .status-badge {
        font-weight: bold;
        font-size: 16px;
    }
    .status-pass {
        color: #4CAF50;
    }
    .status-fail {
        color: #f44336;
    }
    </style>
    <?php
}

/**
 * Emergency compatibility mode
 */
function recruitpro_emergency_compatibility_mode() {
    // Disable all theme enhancements and use basic functionality only
    remove_all_actions('wp_enqueue_scripts');
    remove_all_actions('wp_head');
    remove_all_actions('wp_footer');
    
    // Re-add essential WordPress hooks
    add_action('wp_head', 'wp_head');
    add_action('wp_footer', 'wp_footer');
    
    // Basic theme styles only
    add_action('wp_enqueue_scripts', function() {
        wp_enqueue_style('recruitpro-emergency', get_template_directory_uri() . '/assets/css/emergency.css');
    });
}

// Emergency mode trigger (uncomment in case of severe compatibility issues)
// if (defined('RECRUITPRO_EMERGENCY_MODE') && RECRUITPRO_EMERGENCY_MODE) {
//     recruitpro_emergency_compatibility_mode();
// }