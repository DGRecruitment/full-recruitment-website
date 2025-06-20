<?php
/**
 * RecruitPro Theme Cache Optimization
 *
 * Theme-level caching and performance optimization
 * Note: This handles ONLY theme presentation layer caching, not plugin data
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize cache optimization
 */
function recruitpro_init_cache_optimization() {
    // Enable object caching for theme functions
    recruitpro_enable_object_caching();
    
    // Setup transient management
    recruitpro_setup_transient_management();
    
    // Enable query optimization
    recruitpro_optimize_database_queries();
    
    // Setup image optimization
    recruitpro_setup_image_optimization();
    
    // Enable asset optimization
    recruitpro_optimize_assets();
    
    // Setup cache invalidation
    recruitpro_setup_cache_invalidation();
}
add_action('after_setup_theme', 'recruitpro_init_cache_optimization');

/**
 * Enable object caching for theme functions
 */
function recruitpro_enable_object_caching() {
    // Cache customizer options
    add_filter('theme_mod_' . get_template(), 'recruitpro_cache_theme_mod', 10, 2);
    
    // Cache navigation menus
    add_filter('wp_nav_menu', 'recruitpro_cache_nav_menu', 10, 2);
    
    // Cache widget output
    add_filter('widget_display_callback', 'recruitpro_cache_widget_output', 10, 3);
}

/**
 * Cache theme modifications
 */
function recruitpro_cache_theme_mod($value, $name) {
    $cache_key = 'recruitpro_theme_mod_' . $name;
    $cached_value = wp_cache_get($cache_key, 'theme_mods');
    
    if ($cached_value === false) {
        wp_cache_set($cache_key, $value, 'theme_mods', HOUR_IN_SECONDS);
        return $value;
    }
    
    return $cached_value;
}

/**
 * Cache navigation menus
 */
function recruitpro_cache_nav_menu($nav_menu, $args) {
    // Only cache menus with theme locations
    if (empty($args->theme_location)) {
        return $nav_menu;
    }
    
    $cache_key = 'recruitpro_nav_menu_' . md5($args->theme_location . serialize($args));
    $cached_menu = get_transient($cache_key);
    
    if ($cached_menu === false) {
        set_transient($cache_key, $nav_menu, HOUR_IN_SECONDS);
        return $nav_menu;
    }
    
    return $cached_menu;
}

/**
 * Cache widget output
 */
function recruitpro_cache_widget_output($instance, $widget, $args) {
    // Don't cache dynamic widgets or in customizer
    if (is_customize_preview() || recruitpro_is_dynamic_widget($widget)) {
        return $instance;
    }
    
    $cache_key = 'recruitpro_widget_' . md5($widget->id . serialize($instance));
    $cached_output = get_transient($cache_key);
    
    if ($cached_output !== false) {
        echo $cached_output;
        return false; // Prevent widget from running
    }
    
    // Start output buffering to cache the widget
    ob_start();
    
    return $instance;
}

/**
 * Check if widget should not be cached
 */
function recruitpro_is_dynamic_widget($widget) {
    $dynamic_widgets = array(
        'recent_posts',
        'recent_comments',
        'calendar',
        'rss',
    );
    
    // Allow plugins to add widgets that shouldn't be cached
    $dynamic_widgets = apply_filters('recruitpro_dynamic_widgets', $dynamic_widgets);
    
    return in_array($widget->id_base, $dynamic_widgets);
}

/**
 * Setup transient management
 */
function recruitpro_setup_transient_management() {
    // Register cleanup hooks
    add_action('recruitpro_cleanup_transients', 'recruitpro_cleanup_expired_transients');
    
    // Schedule cleanup if not already scheduled
    if (!wp_next_scheduled('recruitpro_cleanup_transients')) {
        wp_schedule_event(time(), 'daily', 'recruitpro_cleanup_transients');
    }
    
    // Clear transients on theme switch
    add_action('switch_theme', 'recruitpro_clear_theme_transients');
}

/**
 * Cleanup expired transients
 */
function recruitpro_cleanup_expired_transients() {
    global $wpdb;
    
    // Delete expired transients (theme-specific only)
    $wpdb->query($wpdb->prepare("
        DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE %s 
        AND option_value < %d
    ", '_transient_timeout_recruitpro_%', time()));
    
    // Delete orphaned transients
    $wpdb->query("
        DELETE FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient_recruitpro_%' 
        AND option_name NOT LIKE '_transient_timeout_%'
        AND REPLACE(option_name, '_transient_', '_transient_timeout_') NOT IN (
            SELECT option_name FROM {$wpdb->options}
        )
    ");
}

/**
 * Clear theme-specific transients
 */
function recruitpro_clear_theme_transients() {
    global $wpdb;
    
    // Clear all theme transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_%'");
    
    // Clear object cache
    wp_cache_flush_group('theme_mods');
    wp_cache_flush_group('recruitpro_cache');
}

/**
 * Optimize database queries
 */
function recruitpro_optimize_database_queries() {
    // Cache expensive queries
    add_action('pre_get_posts', 'recruitpro_optimize_main_query');
    
    // Reduce database queries for customizer
    add_filter('customize_save_response', 'recruitpro_optimize_customizer_queries');
    
    // Cache post meta queries
    add_filter('get_post_metadata', 'recruitpro_cache_post_meta', 10, 4);
}

/**
 * Optimize main query
 */
function recruitpro_optimize_main_query($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // Cache query results for public pages
    if (!is_user_logged_in()) {
        $cache_key = 'recruitpro_main_query_' . md5(serialize($query->query_vars));
        $cached_posts = get_transient($cache_key);
        
        if ($cached_posts !== false) {
            $query->posts = $cached_posts;
            $query->post_count = count($cached_posts);
            return;
        }
        
        // Cache the query result after it's executed
        add_action('the_posts', function($posts) use ($cache_key) {
            if (!empty($posts)) {
                set_transient($cache_key, $posts, 15 * MINUTE_IN_SECONDS);
            }
            return $posts;
        });
    }
}

/**
 * Cache post meta queries
 */
function recruitpro_cache_post_meta($metadata, $object_id, $meta_key, $single) {
    // Only cache theme-related meta
    $theme_meta_keys = array(
        '_recruitpro_featured_image',
        '_recruitpro_page_layout',
        '_recruitpro_header_style',
        '_recruitpro_footer_style',
    );
    
    if (!in_array($meta_key, $theme_meta_keys)) {
        return null; // Let WordPress handle it normally
    }
    
    $cache_key = "recruitpro_meta_{$object_id}_{$meta_key}";
    $cached_meta = wp_cache_get($cache_key, 'post_meta');
    
    if ($cached_meta !== false) {
        return $single ? array($cached_meta) : $cached_meta;
    }
    
    return null; // Let WordPress handle it and we'll cache the result
}

/**
 * Setup image optimization
 */
function recruitpro_setup_image_optimization() {
    // Enable WebP support
    add_filter('wp_image_editors', 'recruitpro_enable_webp_support');
    
    // Optimize image loading
    add_filter('wp_get_attachment_image_attributes', 'recruitpro_optimize_image_attributes', 10, 3);
    
    // Add responsive images
    add_filter('wp_calculate_image_srcset', 'recruitpro_optimize_srcset', 10, 5);
    
    // Lazy load images
    add_filter('wp_lazy_loading_enabled', 'recruitpro_enable_lazy_loading', 10, 3);
}

/**
 * Enable WebP support
 */
function recruitpro_enable_webp_support($editors) {
    // Add WebP support if available
    if (function_exists('imagewebp')) {
        array_unshift($editors, 'WP_Image_Editor_GD');
    }
    
    return $editors;
}

/**
 * Optimize image attributes
 */
function recruitpro_optimize_image_attributes($attr, $attachment, $size) {
    // Add loading attribute for better performance
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    
    // Add decode attribute for better rendering
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    
    // Cache image dimensions
    if (!isset($attr['width']) || !isset($attr['height'])) {
        $cache_key = "recruitpro_image_size_{$attachment->ID}_{$size}";
        $cached_size = wp_cache_get($cache_key, 'image_sizes');
        
        if ($cached_size === false) {
            $image_meta = wp_get_attachment_metadata($attachment->ID);
            if ($image_meta && isset($image_meta['sizes'][$size])) {
                $cached_size = array(
                    'width' => $image_meta['sizes'][$size]['width'],
                    'height' => $image_meta['sizes'][$size]['height']
                );
                wp_cache_set($cache_key, $cached_size, 'image_sizes', DAY_IN_SECONDS);
            }
        }
        
        if ($cached_size && is_array($cached_size)) {
            $attr['width'] = $cached_size['width'];
            $attr['height'] = $cached_size['height'];
        }
    }
    
    return $attr;
}

/**
 * Optimize srcset generation
 */
function recruitpro_optimize_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    // Cache srcset to avoid regenerating
    $cache_key = "recruitpro_srcset_{$attachment_id}_" . md5(serialize($size_array));
    $cached_srcset = wp_cache_get($cache_key, 'image_srcsets');
    
    if ($cached_srcset !== false) {
        return $cached_srcset;
    }
    
    // Cache the generated srcset
    wp_cache_set($cache_key, $sources, 'image_srcsets', DAY_IN_SECONDS);
    
    return $sources;
}

/**
 * Enable lazy loading
 */
function recruitpro_enable_lazy_loading($default, $tag_name, $context) {
    // Enable for images and iframes in content
    if (in_array($tag_name, array('img', 'iframe')) && $context === 'the_content') {
        return true;
    }
    
    return $default;
}

/**
 * Optimize assets (CSS/JS)
 */
function recruitpro_optimize_assets() {
    // Defer non-critical scripts
    add_filter('script_loader_tag', 'recruitpro_defer_scripts', 10, 2);
    
    // Preload critical resources
    add_action('wp_head', 'recruitpro_preload_critical_resources');
    
    // Minify inline CSS/JS
    add_filter('style_loader_tag', 'recruitpro_minify_inline_styles', 10, 2);
    
    // Combine small CSS files
    add_action('wp_print_styles', 'recruitpro_combine_small_styles');
}

/**
 * Defer non-critical scripts
 */
function recruitpro_defer_scripts($tag, $handle) {
    // Scripts that should not be deferred
    $no_defer = array(
        'jquery',
        'jquery-core',
        'jquery-migrate',
        'recruitpro-critical',
    );
    
    // Don't defer critical scripts
    if (in_array($handle, $no_defer) || is_admin()) {
        return $tag;
    }
    
    // Don't defer scripts with dependencies on jQuery in head
    if (strpos($tag, "id='{$handle}-js'") !== false) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}

/**
 * Preload critical resources
 */
function recruitpro_preload_critical_resources() {
    // Preload critical CSS
    $critical_css = get_template_directory_uri() . '/assets/css/critical.css';
    if (file_exists(get_template_directory() . '/assets/css/critical.css')) {
        echo '<link rel="preload" href="' . esc_url($critical_css) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    }
    
    // Preload web fonts
    $google_fonts_url = recruitpro_get_google_fonts_url();
    if ($google_fonts_url) {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="preload" href="' . esc_url($google_fonts_url) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    }
    
    // Preload hero image if on homepage
    if (is_front_page()) {
        $hero_image = get_theme_mod('recruitpro_hero_image');
        if ($hero_image) {
            echo '<link rel="preload" href="' . esc_url($hero_image) . '" as="image">' . "\n";
        }
    }
}

/**
 * Minify inline styles
 */
function recruitpro_minify_inline_styles($tag, $handle) {
    // Only minify theme styles
    if (strpos($handle, 'recruitpro') === false) {
        return $tag;
    }
    
    // Minify inline styles
    $tag = preg_replace('/\s+/', ' ', $tag);
    $tag = str_replace(array("\n", "\r", "\t"), '', $tag);
    
    return $tag;
}

/**
 * Combine small CSS files
 */
function recruitpro_combine_small_styles() {
    global $wp_styles;
    
    $theme_styles = array();
    $combined_css = '';
    
    // Find theme-related small CSS files
    foreach ($wp_styles->queue as $handle) {
        if (strpos($handle, 'recruitpro') !== false) {
            $style = $wp_styles->registered[$handle];
            if ($style && $style->src) {
                $file_path = str_replace(get_template_directory_uri(), get_template_directory(), $style->src);
                if (file_exists($file_path) && filesize($file_path) < 10240) { // Less than 10KB
                    $theme_styles[] = $handle;
                    $combined_css .= file_get_contents($file_path);
                }
            }
        }
    }
    
    // Remove individual styles and add combined
    if (!empty($theme_styles) && strlen($combined_css) > 0) {
        foreach ($theme_styles as $handle) {
            wp_dequeue_style($handle);
        }
        
        // Cache combined CSS
        $cache_key = 'recruitpro_combined_css_' . md5($combined_css);
        $cached_css = get_transient($cache_key);
        
        if ($cached_css === false) {
            $cached_css = recruitpro_minify_css($combined_css);
            set_transient($cache_key, $cached_css, DAY_IN_SECONDS);
        }
        
        wp_add_inline_style('recruitpro-style', $cached_css);
    }
}

/**
 * Minify CSS
 */
function recruitpro_minify_css($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Remove unnecessary whitespace
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
    
    // Remove other unnecessary characters
    $css = str_replace(array('; ', ' ;', ' {', '{ ', '} ', ' }', ' :', ': ', ' ,', ', '), array(';', ';', '{', '{', '}', '}', ':', ':', ',', ','), $css);
    
    return trim($css);
}

/**
 * Setup cache invalidation
 */
function recruitpro_setup_cache_invalidation() {
    // Clear cache when theme is customized
    add_action('customize_save_after', 'recruitpro_clear_customizer_cache');
    
    // Clear cache when menus are updated
    add_action('wp_update_nav_menu', 'recruitpro_clear_menu_cache');
    
    // Clear cache when widgets are updated
    add_action('widget_update_callback', 'recruitpro_clear_widget_cache');
    
    // Clear cache when posts are updated
    add_action('save_post', 'recruitpro_clear_post_cache');
    
    // Clear cache when theme options are updated
    add_action('update_option_theme_mods_' . get_template(), 'recruitpro_clear_theme_mod_cache');
}

/**
 * Clear customizer cache
 */
function recruitpro_clear_customizer_cache($wp_customize) {
    recruitpro_clear_theme_transients();
    
    // Clear page cache plugins if available
    recruitpro_clear_external_cache();
}

/**
 * Clear menu cache
 */
function recruitpro_clear_menu_cache($menu_id) {
    // Clear menu-related transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_nav_menu_%'");
    
    wp_cache_flush_group('nav_menus');
}

/**
 * Clear widget cache
 */
function recruitpro_clear_widget_cache($instance, $widget, $old_instance) {
    // Clear widget-related transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_widget_%'");
    
    wp_cache_flush_group('widgets');
    
    return $instance;
}

/**
 * Clear post cache
 */
function recruitpro_clear_post_cache($post_id) {
    // Only clear cache for public post types
    $post_type = get_post_type($post_id);
    $public_post_types = get_post_types(array('public' => true));
    
    if (!in_array($post_type, $public_post_types)) {
        return;
    }
    
    // Clear related transients
    delete_transient('recruitpro_main_query_' . $post_id);
    
    // Clear homepage cache if this might affect it
    if (in_array($post_type, array('post', 'page'))) {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_main_query_%'");
    }
}

/**
 * Clear theme mod cache
 */
function recruitpro_clear_theme_mod_cache($option, $old_value, $value) {
    wp_cache_flush_group('theme_mods');
    
    // Clear related transients
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_theme_mod_%'");
}

/**
 * Clear external cache plugins
 */
function recruitpro_clear_external_cache() {
    // W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }
    
    // WP Super Cache
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
    
    // WP Rocket
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }
    
    // LiteSpeed Cache
    if (class_exists('LiteSpeed_Cache_API')) {
        LiteSpeed_Cache_API::purge_all();
    }
    
    // Cloudflare
    if (class_exists('CF\WordPress\Hooks')) {
        do_action('cloudflare_purge_everything');
    }
    
    // WP Fastest Cache
    if (function_exists('wpfc_clear_all_cache')) {
        wpfc_clear_all_cache(true);
    }
}

/**
 * Get cache status for admin display
 */
function recruitpro_get_cache_status() {
    $status = array(
        'object_cache' => function_exists('wp_cache_get') && wp_using_ext_object_cache(),
        'page_cache' => recruitpro_detect_page_cache(),
        'transients_count' => recruitpro_count_theme_transients(),
        'cache_size' => recruitpro_calculate_cache_size(),
    );
    
    return $status;
}

/**
 * Detect page cache
 */
function recruitpro_detect_page_cache() {
    // Check for common caching plugins
    $cache_plugins = array(
        'w3-total-cache/w3-total-cache.php',
        'wp-super-cache/wp-cache.php',
        'wp-rocket/wp-rocket.php',
        'litespeed-cache/litespeed-cache.php',
        'wp-fastest-cache/wpFastestCache.php',
    );
    
    foreach ($cache_plugins as $plugin) {
        if (is_plugin_active($plugin)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Count theme transients
 */
function recruitpro_count_theme_transients() {
    global $wpdb;
    
    $count = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient%recruitpro_%'
    ");
    
    return intval($count);
}

/**
 * Calculate cache size
 */
function recruitpro_calculate_cache_size() {
    global $wpdb;
    
    $size = $wpdb->get_var("
        SELECT SUM(LENGTH(option_value)) 
        FROM {$wpdb->options} 
        WHERE option_name LIKE '_transient%recruitpro_%'
    ");
    
    return intval($size);
}

/**
 * Display cache status in admin
 */
function recruitpro_display_cache_status() {
    $status = recruitpro_get_cache_status();
    ?>
    <div class="postbox">
        <h2 class="hndle"><?php esc_html_e('Cache Status', 'recruitpro'); ?></h2>
        <div class="inside">
            <div class="recruitpro-cache-status">
                <div class="cache-status-item">
                    <span><?php esc_html_e('Object Cache', 'recruitpro'); ?></span>
                    <span class="cache-status-badge <?php echo $status['object_cache'] ? 'cache-active' : 'cache-inactive'; ?>">
                        <?php echo $status['object_cache'] ? esc_html__('Active', 'recruitpro') : esc_html__('Inactive', 'recruitpro'); ?>
                    </span>
                </div>
                <div class="cache-status-item">
                    <span><?php esc_html_e('Page Cache', 'recruitpro'); ?></span>
                    <span class="cache-status-badge <?php echo $status['page_cache'] ? 'cache-active' : 'cache-inactive'; ?>">
                        <?php echo $status['page_cache'] ? esc_html__('Detected', 'recruitpro') : esc_html__('Not Detected', 'recruitpro'); ?>
                    </span>
                </div>
                <div class="cache-status-item">
                    <span><?php esc_html_e('Theme Transients', 'recruitpro'); ?></span>
                    <span class="cache-status-badge cache-info">
                        <?php echo esc_html($status['transients_count']); ?>
                    </span>
                </div>
                <div class="cache-status-item">
                    <span><?php esc_html_e('Cache Size', 'recruitpro'); ?></span>
                    <span class="cache-status-badge cache-info">
                        <?php echo esc_html(size_format($status['cache_size'])); ?>
                    </span>
                </div>
            </div>
            
            <div class="cache-actions">
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('recruitpro_clear_cache', 'cache_nonce'); ?>
                    <input type="hidden" name="action" value="clear_theme_cache">
                    <input type="submit" class="button" value="<?php esc_attr_e('Clear Theme Cache', 'recruitpro'); ?>">
                </form>
                
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('recruitpro_clear_cache', 'cache_nonce'); ?>
                    <input type="hidden" name="action" value="clear_all_cache">
                    <input type="submit" class="button" value="<?php esc_attr_e('Clear All Cache', 'recruitpro'); ?>">
                </form>
            </div>
        </div>
    </div>
    
    <style>
    .recruitpro-cache-status .cache-status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .cache-status-badge {
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    .cache-active {
        background-color: #4CAF50;
        color: white;
    }
    .cache-inactive {
        background-color: #f44336;
        color: white;
    }
    .cache-info {
        background-color: #2196F3;
        color: white;
    }
    .cache-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .cache-actions .button {
        margin-right: 10px;
    }
    </style>
    <?php
}

/**
 * Handle cache clearing actions
 */
function recruitpro_handle_cache_actions() {
    if (!isset($_POST['action']) || !current_user_can('manage_options')) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['cache_nonce'], 'recruitpro_clear_cache')) {
        return;
    }
    
    switch ($_POST['action']) {
        case 'clear_theme_cache':
            recruitpro_clear_theme_transients();
            add_settings_error('cache_clear', 'success', esc_html__('Theme cache cleared successfully!', 'recruitpro'), 'updated');
            break;
            
        case 'clear_all_cache':
            recruitpro_clear_theme_transients();
            recruitpro_clear_external_cache();
            add_settings_error('cache_clear', 'success', esc_html__('All cache cleared successfully!', 'recruitpro'), 'updated');
            break;
    }
}
add_action('admin_init', 'recruitpro_handle_cache_actions');

/**
 * Clear caches on theme deactivation
 */
function recruitpro_clear_cache_on_deactivation() {
    recruitpro_clear_theme_transients();
    wp_clear_scheduled_hook('recruitpro_cleanup_transients');
}
add_action('switch_theme', 'recruitpro_clear_cache_on_deactivation');

/**
 * Performance monitoring
 */
function recruitpro_monitor_performance() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        add_action('wp_footer', 'recruitpro_display_performance_stats');
    }
}
add_action('init', 'recruitpro_monitor_performance');

/**
 * Display performance stats in debug mode
 */
function recruitpro_display_performance_stats() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $memory_usage = memory_get_peak_usage(true);
    $query_count = get_num_queries();
    $load_time = timer_stop(0);
    
    ?>
    <!-- RecruitPro Performance Stats -->
    <div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; font-size: 12px; z-index: 9999;">
        <strong>Performance:</strong><br>
        Memory: <?php echo size_format($memory_usage); ?><br>
        Queries: <?php echo $query_count; ?><br>
        Load Time: <?php echo $load_time; ?>s
    </div>
    <?php
}