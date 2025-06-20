<?php
/**
 * RecruitPro Theme Debug Tools
 *
 * Debug and development tools for theme troubleshooting and performance monitoring
 * Only active when WP_DEBUG is enabled or for administrators
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize debug tools
 */
function recruitpro_init_debug_tools() {
    // Only enable debug tools when appropriate
    if (!recruitpro_should_enable_debug_tools()) {
        return;
    }
    
    // Setup debug logging
    recruitpro_setup_debug_logging();
    
    // Setup performance monitoring
    recruitpro_setup_performance_monitoring();
    
    // Setup template debugging
    recruitpro_setup_template_debugging();
    
    // Setup hook debugging
    recruitpro_setup_hook_debugging();
    
    // Setup asset debugging
    recruitpro_setup_asset_debugging();
    
    // Setup database debugging
    recruitpro_setup_database_debugging();
    
    // Add admin debug interface
    add_action('admin_menu', 'recruitpro_add_debug_admin_menu');
    
    // Add debug information to admin bar
    add_action('admin_bar_menu', 'recruitpro_add_debug_admin_bar', 100);
    
    // Add AJAX handlers for debug tools
    add_action('wp_ajax_recruitpro_debug_action', 'recruitpro_handle_debug_ajax');
}
add_action('after_setup_theme', 'recruitpro_init_debug_tools');

/**
 * Check if debug tools should be enabled
 */
function recruitpro_should_enable_debug_tools() {
    // Enable if WP_DEBUG is true
    if (defined('WP_DEBUG') && WP_DEBUG) {
        return true;
    }
    
    // Enable if user is administrator and debug is explicitly enabled
    if (current_user_can('manage_options') && get_theme_mod('recruitpro_enable_debug_tools', false)) {
        return true;
    }
    
    // Enable if theme debug constant is defined
    if (defined('RECRUITPRO_DEBUG') && RECRUITPRO_DEBUG) {
        return true;
    }
    
    return false;
}

/**
 * Setup debug logging
 */
function recruitpro_setup_debug_logging() {
    // Create debug log directory if it doesn't exist
    $log_dir = wp_upload_dir()['basedir'] . '/recruitpro-debug';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
        
        // Add .htaccess to protect log files
        file_put_contents($log_dir . '/.htaccess', "Deny from all\n");
    }
    
    // Setup error handlers
    add_action('wp_footer', 'recruitpro_log_javascript_errors');
    add_action('wp_head', 'recruitpro_log_css_errors');
    
    // Log theme-specific errors
    add_action('recruitpro_error', 'recruitpro_log_theme_error', 10, 2);
}

/**
 * Log theme error
 */
function recruitpro_log_theme_error($error_message, $context = array()) {
    $log_entry = array(
        'timestamp' => current_time('Y-m-d H:i:s'),
        'message' => $error_message,
        'context' => $context,
        'url' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        'user_id' => get_current_user_id(),
    );
    
    $log_file = wp_upload_dir()['basedir'] . '/recruitpro-debug/theme-errors.log';
    error_log(wp_json_encode($log_entry) . "\n", 3, $log_file);
}

/**
 * Setup performance monitoring
 */
function recruitpro_setup_performance_monitoring() {
    // Start performance timer
    if (!defined('RECRUITPRO_START_TIME')) {
        define('RECRUITPRO_START_TIME', microtime(true));
    }
    
    // Monitor memory usage
    add_action('wp_head', 'recruitpro_log_memory_usage');
    add_action('wp_footer', 'recruitpro_display_performance_stats');
    
    // Monitor database queries
    add_filter('query', 'recruitpro_monitor_database_queries');
    
    // Monitor asset loading
    add_action('wp_enqueue_scripts', 'recruitpro_monitor_asset_loading', 999);
}

/**
 * Log memory usage
 */
function recruitpro_log_memory_usage() {
    $memory_usage = array(
        'current' => memory_get_usage(true),
        'peak' => memory_get_peak_usage(true),
        'limit' => wp_convert_hr_to_bytes(ini_get('memory_limit')),
    );
    
    // Log if memory usage is high
    if ($memory_usage['current'] > ($memory_usage['limit'] * 0.8)) {
        recruitpro_log_theme_error('High memory usage detected', $memory_usage);
    }
}

/**
 * Display performance statistics
 */
function recruitpro_display_performance_stats() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $end_time = microtime(true);
    $execution_time = $end_time - RECRUITPRO_START_TIME;
    $memory_usage = memory_get_peak_usage(true);
    $query_count = get_num_queries();
    
    ?>
    <div id="recruitpro-debug-stats" style="position: fixed; bottom: 0; right: 0; background: rgba(0,0,0,0.9); color: white; padding: 10px; font-size: 12px; z-index: 999999; font-family: monospace;">
        <div><strong>RecruitPro Debug Stats</strong></div>
        <div>Execution Time: <?php echo number_format($execution_time, 4); ?>s</div>
        <div>Memory Usage: <?php echo size_format($memory_usage); ?></div>
        <div>Database Queries: <?php echo $query_count; ?></div>
        <div>Template: <?php echo recruitpro_get_current_template(); ?></div>
        <div><a href="#" onclick="this.parentNode.parentNode.style.display='none'; return false;" style="color: #fff;">Close</a></div>
    </div>
    <?php
}

/**
 * Get current template
 */
function recruitpro_get_current_template() {
    global $template;
    return basename($template);
}

/**
 * Monitor database queries
 */
function recruitpro_monitor_database_queries($query) {
    // Log slow queries (>1 second)
    $start_time = microtime(true);
    
    add_action('shutdown', function() use ($query, $start_time) {
        $execution_time = microtime(true) - $start_time;
        if ($execution_time > 1.0) {
            recruitpro_log_theme_error('Slow database query detected', array(
                'query' => $query,
                'execution_time' => $execution_time,
            ));
        }
    });
    
    return $query;
}

/**
 * Setup template debugging
 */
function recruitpro_setup_template_debugging() {
    // Add template comments in debug mode
    add_action('wp_head', 'recruitpro_add_template_comments');
    
    // Monitor template loading
    add_filter('template_include', 'recruitpro_monitor_template_loading');
    
    // Debug template hierarchy
    add_action('template_redirect', 'recruitpro_debug_template_hierarchy');
}

/**
 * Add template comments
 */
function recruitpro_add_template_comments() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    echo "\n<!-- RecruitPro Theme Debug: Template: " . recruitpro_get_current_template() . " -->\n";
    echo "<!-- Query: " . recruitpro_get_current_query_info() . " -->\n";
}

/**
 * Get current query info
 */
function recruitpro_get_current_query_info() {
    global $wp_query;
    
    $query_info = array();
    
    if (is_home()) $query_info[] = 'home';
    if (is_front_page()) $query_info[] = 'front_page';
    if (is_single()) $query_info[] = 'single(' . get_post_type() . ')';
    if (is_page()) $query_info[] = 'page';
    if (is_archive()) $query_info[] = 'archive';
    if (is_category()) $query_info[] = 'category';
    if (is_tag()) $query_info[] = 'tag';
    if (is_author()) $query_info[] = 'author';
    if (is_date()) $query_info[] = 'date';
    if (is_search()) $query_info[] = 'search';
    if (is_404()) $query_info[] = '404';
    
    return implode(', ', $query_info);
}

/**
 * Monitor template loading
 */
function recruitpro_monitor_template_loading($template) {
    // Log template loading for debugging
    if (current_user_can('manage_options')) {
        recruitpro_log_theme_error('Template loaded: ' . basename($template), array(
            'template_path' => $template,
            'query_vars' => array_keys($GLOBALS['wp_query']->query_vars),
        ));
    }
    
    return $template;
}

/**
 * Debug template hierarchy
 */
function recruitpro_debug_template_hierarchy() {
    if (!current_user_can('manage_options') || !isset($_GET['debug_template'])) {
        return;
    }
    
    global $wp_query;
    
    $template_hierarchy = array();
    
    if (is_singular()) {
        $post_type = get_post_type();
        $template_hierarchy = array(
            'single-' . $post_type . '-' . get_the_ID() . '.php',
            'single-' . $post_type . '.php',
            'single.php',
            'singular.php',
            'index.php'
        );
    } elseif (is_home()) {
        $template_hierarchy = array(
            'home.php',
            'index.php'
        );
    }
    
    wp_die('<pre>' . print_r($template_hierarchy, true) . '</pre>');
}

/**
 * Setup hook debugging
 */
function recruitpro_setup_hook_debugging() {
    if (!isset($_GET['debug_hooks'])) {
        return;
    }
    
    // Log all hooks and filters
    add_action('all', 'recruitpro_log_hook_execution');
}

/**
 * Log hook execution
 */
function recruitpro_log_hook_execution($hook) {
    global $wp_filter;
    
    // Only log theme-related hooks
    if (strpos($hook, 'recruitpro') !== false || in_array($hook, array('wp_head', 'wp_footer', 'wp_enqueue_scripts'))) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        recruitpro_log_theme_error("Hook executed: $hook", array(
            'backtrace' => array_slice($backtrace, 0, 3),
            'callbacks' => isset($wp_filter[$hook]) ? count($wp_filter[$hook]) : 0,
        ));
    }
}

/**
 * Setup asset debugging
 */
function recruitpro_setup_asset_debugging() {
    // Monitor script and style loading
    add_action('wp_print_scripts', 'recruitpro_debug_scripts');
    add_action('wp_print_styles', 'recruitpro_debug_styles');
    
    // Check for missing assets
    add_action('wp_footer', 'recruitpro_check_missing_assets');
}

/**
 * Debug scripts
 */
function recruitpro_debug_scripts() {
    if (!current_user_can('manage_options') || !isset($_GET['debug_assets'])) {
        return;
    }
    
    global $wp_scripts;
    
    echo "\n<!-- RecruitPro Debug: Enqueued Scripts -->\n";
    foreach ($wp_scripts->queue as $handle) {
        $script = $wp_scripts->registered[$handle];
        echo "<!-- Script: $handle | Source: " . ($script->src ?? 'inline') . " -->\n";
    }
}

/**
 * Debug styles
 */
function recruitpro_debug_styles() {
    if (!current_user_can('manage_options') || !isset($_GET['debug_assets'])) {
        return;
    }
    
    global $wp_styles;
    
    echo "\n<!-- RecruitPro Debug: Enqueued Styles -->\n";
    foreach ($wp_styles->queue as $handle) {
        $style = $wp_styles->registered[$handle];
        echo "<!-- Style: $handle | Source: " . ($style->src ?? 'inline') . " -->\n";
    }
}

/**
 * Check for missing assets
 */
function recruitpro_check_missing_assets() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $theme_assets = array(
        get_template_directory_uri() . '/assets/css/main.css',
        get_template_directory_uri() . '/assets/js/main.js',
    );
    
    foreach ($theme_assets as $asset) {
        $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $asset);
        if (!file_exists($local_path)) {
            recruitpro_log_theme_error('Missing theme asset', array('asset' => $asset));
        }
    }
}

/**
 * Setup database debugging
 */
function recruitpro_setup_database_debugging() {
    // Log custom post type queries
    add_action('pre_get_posts', 'recruitpro_debug_custom_queries');
    
    // Monitor theme-related database operations
    add_filter('posts_request', 'recruitpro_log_database_requests');
}

/**
 * Debug custom queries
 */
function recruitpro_debug_custom_queries($query) {
    if (!$query->is_main_query() || is_admin()) {
        return;
    }
    
    // Log queries for custom post types
    $post_type = $query->get('post_type');
    if (in_array($post_type, array('testimonial', 'team_member', 'service', 'success_story'))) {
        recruitpro_log_theme_error('Custom post type query', array(
            'post_type' => $post_type,
            'query_vars' => $query->query_vars,
        ));
    }
}

/**
 * Log database requests
 */
function recruitpro_log_database_requests($request) {
    // Only log theme-related requests
    if (strpos($request, 'recruitpro') !== false || 
        strpos($request, 'testimonial') !== false || 
        strpos($request, 'team_member') !== false) {
        
        recruitpro_log_theme_error('Database request', array(
            'query' => $request,
            'backtrace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5),
        ));
    }
    
    return $request;
}

/**
 * Log JavaScript errors
 */
function recruitpro_log_javascript_errors() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <script>
    window.addEventListener('error', function(e) {
        // Only log errors from theme scripts
        if (e.filename && e.filename.indexOf('recruitpro') !== -1) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=recruitpro_debug_action&type=js_error&message=' + encodeURIComponent(e.message) + 
                      '&filename=' + encodeURIComponent(e.filename) + 
                      '&lineno=' + e.lineno + 
                      '&nonce=<?php echo wp_create_nonce('recruitpro_debug_nonce'); ?>'
            });
        }
    });
    </script>
    <?php
}

/**
 * Log CSS errors
 */
function recruitpro_log_css_errors() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <script>
    // Monitor for missing CSS files
    document.addEventListener('DOMContentLoaded', function() {
        var links = document.querySelectorAll('link[rel="stylesheet"]');
        links.forEach(function(link) {
            if (link.href.indexOf('recruitpro') !== -1) {
                link.addEventListener('error', function() {
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=recruitpro_debug_action&type=css_error&href=' + encodeURIComponent(link.href) + 
                              '&nonce=<?php echo wp_create_nonce('recruitpro_debug_nonce'); ?>'
                    });
                });
            }
        });
    });
    </script>
    <?php
}

/**
 * Handle debug AJAX requests
 */
function recruitpro_handle_debug_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_debug_nonce') || !current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    $type = sanitize_text_field($_POST['type']);
    
    switch ($type) {
        case 'js_error':
            recruitpro_log_theme_error('JavaScript error', array(
                'message' => sanitize_text_field($_POST['message']),
                'filename' => sanitize_text_field($_POST['filename']),
                'lineno' => intval($_POST['lineno']),
            ));
            break;
            
        case 'css_error':
            recruitpro_log_theme_error('CSS loading error', array(
                'href' => esc_url_raw($_POST['href']),
            ));
            break;
    }
    
    wp_die();
}

/**
 * Add debug admin menu
 */
function recruitpro_add_debug_admin_menu() {
    add_management_page(
        esc_html__('RecruitPro Debug', 'recruitpro'),
        esc_html__('RecruitPro Debug', 'recruitpro'),
        'manage_options',
        'recruitpro-debug',
        'recruitpro_debug_admin_page'
    );
}

/**
 * Debug admin page
 */
function recruitpro_debug_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('RecruitPro Debug Tools', 'recruitpro'); ?></h1>
        
        <div class="recruitpro-debug-tabs">
            <h2 class="nav-tab-wrapper">
                <a href="#system-info" class="nav-tab nav-tab-active"><?php esc_html_e('System Info', 'recruitpro'); ?></a>
                <a href="#error-log" class="nav-tab"><?php esc_html_e('Error Log', 'recruitpro'); ?></a>
                <a href="#performance" class="nav-tab"><?php esc_html_e('Performance', 'recruitpro'); ?></a>
                <a href="#tools" class="nav-tab"><?php esc_html_e('Debug Tools', 'recruitpro'); ?></a>
            </h2>
            
            <div id="system-info" class="tab-content">
                <?php recruitpro_display_system_info(); ?>
            </div>
            
            <div id="error-log" class="tab-content" style="display: none;">
                <?php recruitpro_display_error_log(); ?>
            </div>
            
            <div id="performance" class="tab-content" style="display: none;">
                <?php recruitpro_display_performance_info(); ?>
            </div>
            
            <div id="tools" class="tab-content" style="display: none;">
                <?php recruitpro_display_debug_tools(); ?>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $($(this).attr('href')).show();
        });
    });
    </script>
    
    <style>
    .tab-content {
        background: #fff;
        padding: 20px;
        border: 1px solid #ccd0d4;
        border-top: none;
    }
    .debug-info-table {
        width: 100%;
        border-collapse: collapse;
    }
    .debug-info-table th,
    .debug-info-table td {
        padding: 8px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }
    .debug-info-table th {
        background: #f1f1f1;
        font-weight: bold;
    }
    .status-good { color: #46b450; }
    .status-warning { color: #ffb900; }
    .status-error { color: #dc3232; }
    </style>
    <?php
}

/**
 * Display system information
 */
function recruitpro_display_system_info() {
    $theme = wp_get_theme();
    $system_info = array(
        'Theme Version' => $theme->get('Version'),
        'WordPress Version' => get_bloginfo('version'),
        'PHP Version' => PHP_VERSION,
        'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'Memory Limit' => ini_get('memory_limit'),
        'Max Execution Time' => ini_get('max_execution_time') . 's',
        'Upload Max Filesize' => ini_get('upload_max_filesize'),
        'Post Max Size' => ini_get('post_max_size'),
        'WP Debug' => defined('WP_DEBUG') && WP_DEBUG ? 'Enabled' : 'Disabled',
        'WP Debug Log' => defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Enabled' : 'Disabled',
    );
    
    ?>
    <h3><?php esc_html_e('System Information', 'recruitpro'); ?></h3>
    <table class="debug-info-table">
        <?php foreach ($system_info as $label => $value) : ?>
            <tr>
                <th><?php echo esc_html($label); ?></th>
                <td><?php echo esc_html($value); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <h3><?php esc_html_e('Theme Status', 'recruitpro'); ?></h3>
    <?php recruitpro_display_theme_status(); ?>
    
    <h3><?php esc_html_e('Plugin Compatibility', 'recruitpro'); ?></h3>
    <?php recruitpro_display_plugin_compatibility(); ?>
    <?php
}

/**
 * Display theme status
 */
function recruitpro_display_theme_status() {
    $status_checks = array(
        'Custom Post Types' => post_type_exists('testimonial'),
        'Customizer Settings' => !empty(get_theme_mods()),
        'Assets Directory' => file_exists(get_template_directory() . '/assets'),
        'CSS Files' => file_exists(get_template_directory() . '/assets/css/main.css'),
        'JS Files' => file_exists(get_template_directory() . '/assets/js/main.js'),
    );
    
    ?>
    <table class="debug-info-table">
        <?php foreach ($status_checks as $check => $status) : ?>
            <tr>
                <th><?php echo esc_html($check); ?></th>
                <td class="<?php echo $status ? 'status-good' : 'status-error'; ?>">
                    <?php echo $status ? '✓ OK' : '✗ Failed'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
}

/**
 * Display plugin compatibility
 */
function recruitpro_display_plugin_compatibility() {
    $plugins = array(
        'RecruitPro CRM' => class_exists('RecruitPro_CRM'),
        'RecruitPro Jobs' => class_exists('RecruitPro_Jobs'),
        'Classic Editor' => class_exists('Classic_Editor'),
        'Elementor' => class_exists('Elementor\Plugin'),
        'Contact Form 7' => defined('WPCF7_VERSION'),
        'Yoast SEO' => defined('WPSEO_VERSION'),
    );
    
    ?>
    <table class="debug-info-table">
        <?php foreach ($plugins as $plugin => $active) : ?>
            <tr>
                <th><?php echo esc_html($plugin); ?></th>
                <td class="<?php echo $active ? 'status-good' : 'status-warning'; ?>">
                    <?php echo $active ? '✓ Active' : '— Not Active'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
}

/**
 * Display error log
 */
function recruitpro_display_error_log() {
    $log_file = wp_upload_dir()['basedir'] . '/recruitpro-debug/theme-errors.log';
    
    ?>
    <h3><?php esc_html_e('Recent Theme Errors', 'recruitpro'); ?></h3>
    
    <?php if (file_exists($log_file)) : ?>
        <div style="background: #f1f1f1; padding: 10px; height: 400px; overflow-y: scroll; font-family: monospace; font-size: 12px;">
            <?php
            $log_contents = file_get_contents($log_file);
            $log_lines = explode("\n", $log_contents);
            $recent_lines = array_slice($log_lines, -50); // Show last 50 entries
            
            foreach ($recent_lines as $line) {
                if (!empty($line)) {
                    $entry = json_decode($line, true);
                    if ($entry) {
                        echo '<div style="margin-bottom: 10px; padding: 5px; background: white; border-left: 3px solid #dc3232;">';
                        echo '<strong>' . esc_html($entry['timestamp']) . '</strong><br>';
                        echo esc_html($entry['message']) . '<br>';
                        if (!empty($entry['context'])) {
                            echo '<small style="color: #666;">' . esc_html(wp_json_encode($entry['context'])) . '</small>';
                        }
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        
        <p>
            <a href="<?php echo esc_url(add_query_arg('action', 'clear_error_log', wp_get_referer())); ?>" 
               class="button button-secondary"
               onclick="return confirm('<?php esc_js_e('Are you sure you want to clear the error log?', 'recruitpro'); ?>')">
                <?php esc_html_e('Clear Error Log', 'recruitpro'); ?>
            </a>
        </p>
    <?php else : ?>
        <p><?php esc_html_e('No error log found.', 'recruitpro'); ?></p>
    <?php endif; ?>
    <?php
}

/**
 * Display performance information
 */
function recruitpro_display_performance_info() {
    ?>
    <h3><?php esc_html_e('Performance Metrics', 'recruitpro'); ?></h3>
    
    <table class="debug-info-table">
        <tr>
            <th><?php esc_html_e('Current Memory Usage', 'recruitpro'); ?></th>
            <td><?php echo size_format(memory_get_usage(true)); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Peak Memory Usage', 'recruitpro'); ?></th>
            <td><?php echo size_format(memory_get_peak_usage(true)); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Database Queries', 'recruitpro'); ?></th>
            <td><?php echo get_num_queries(); ?></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Page Generation Time', 'recruitpro'); ?></th>
            <td><?php echo timer_stop(0); ?>s</td>
        </tr>
    </table>
    
    <h3><?php esc_html_e('Optimization Status', 'recruitpro'); ?></h3>
    <?php recruitpro_display_optimization_status(); ?>
    <?php
}

/**
 * Display optimization status
 */
function recruitpro_display_optimization_status() {
    $optimizations = array(
        'GZIP Compression' => function_exists('gzencode') && extension_loaded('zlib'),
        'Object Caching' => wp_using_ext_object_cache(),
        'Opcode Caching' => function_exists('opcache_get_status') && opcache_get_status(),
        'Image Optimization' => get_theme_mod('recruitpro_optimize_images', true),
        'CSS Minification' => get_theme_mod('recruitpro_minify_css', true),
        'JS Minification' => get_theme_mod('recruitpro_minify_js', true),
    );
    
    ?>
    <table class="debug-info-table">
        <?php foreach ($optimizations as $optimization => $enabled) : ?>
            <tr>
                <th><?php echo esc_html($optimization); ?></th>
                <td class="<?php echo $enabled ? 'status-good' : 'status-warning'; ?>">
                    <?php echo $enabled ? '✓ Enabled' : '— Disabled'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
}

/**
 * Display debug tools
 */
function recruitpro_display_debug_tools() {
    $current_url = home_url(add_query_arg(null, null));
    
    ?>
    <h3><?php esc_html_e('Debug URLs', 'recruitpro'); ?></h3>
    <p><?php esc_html_e('Add these parameters to any page URL for debugging:', 'recruitpro'); ?></p>
    
    <ul>
        <li><code>?debug_template=1</code> - <?php esc_html_e('Show template hierarchy', 'recruitpro'); ?></li>
        <li><code>?debug_hooks=1</code> - <?php esc_html_e('Log hook execution', 'recruitpro'); ?></li>
        <li><code>?debug_assets=1</code> - <?php esc_html_e('Show asset loading info', 'recruitpro'); ?></li>
        <li><code>?debug_queries=1</code> - <?php esc_html_e('Show database queries', 'recruitpro'); ?></li>
    </ul>
    
    <h3><?php esc_html_e('Quick Actions', 'recruitpro'); ?></h3>
    <p>
        <a href="<?php echo esc_url(add_query_arg('action', 'flush_rewrite_rules')); ?>" class="button">
            <?php esc_html_e('Flush Rewrite Rules', 'recruitpro'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg('action', 'clear_theme_cache')); ?>" class="button">
            <?php esc_html_e('Clear Theme Cache', 'recruitpro'); ?>
        </a>
        <a href="<?php echo esc_url(add_query_arg('action', 'regenerate_thumbnails')); ?>" class="button">
            <?php esc_html_e('Check Image Sizes', 'recruitpro'); ?>
        </a>
    </p>
    
    <h3><?php esc_html_e('Export Debug Information', 'recruitpro'); ?></h3>
    <p>
        <a href="<?php echo esc_url(add_query_arg('action', 'export_debug_info')); ?>" class="button button-primary">
            <?php esc_html_e('Download Debug Report', 'recruitpro'); ?>
        </a>
    </p>
    <?php
}

/**
 * Add debug information to admin bar
 */
function recruitpro_add_debug_admin_bar($admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $admin_bar->add_menu(array(
        'id'    => 'recruitpro-debug',
        'title' => 'Debug',
        'href'  => admin_url('tools.php?page=recruitpro-debug'),
    ));
    
    // Add quick debug links
    $admin_bar->add_menu(array(
        'id'     => 'recruitpro-debug-template',
        'parent' => 'recruitpro-debug',
        'title'  => 'Template: ' . recruitpro_get_current_template(),
        'href'   => add_query_arg('debug_template', '1'),
    ));
    
    $admin_bar->add_menu(array(
        'id'     => 'recruitpro-debug-memory',
        'parent' => 'recruitpro-debug',
        'title'  => 'Memory: ' . size_format(memory_get_usage(true)),
        'href'   => '#',
    ));
    
    $admin_bar->add_menu(array(
        'id'     => 'recruitpro-debug-queries',
        'parent' => 'recruitpro-debug',
        'title'  => 'Queries: ' . get_num_queries(),
        'href'   => '#',
    ));
}

/**
 * Handle debug actions
 */
function recruitpro_handle_debug_actions() {
    if (!isset($_GET['action']) || !current_user_can('manage_options')) {
        return;
    }
    
    $action = sanitize_text_field($_GET['action']);
    
    switch ($action) {
        case 'clear_error_log':
            $log_file = wp_upload_dir()['basedir'] . '/recruitpro-debug/theme-errors.log';
            if (file_exists($log_file)) {
                unlink($log_file);
            }
            wp_redirect(remove_query_arg('action'));
            exit;
            
        case 'flush_rewrite_rules':
            flush_rewrite_rules();
            wp_redirect(add_query_arg('message', 'rewrite_rules_flushed', remove_query_arg('action')));
            exit;
            
        case 'clear_theme_cache':
            if (function_exists('recruitpro_clear_theme_transients')) {
                recruitpro_clear_theme_transients();
            }
            wp_redirect(add_query_arg('message', 'cache_cleared', remove_query_arg('action')));
            exit;
            
        case 'export_debug_info':
            recruitpro_export_debug_report();
            exit;
    }
}
add_action('admin_init', 'recruitpro_handle_debug_actions');

/**
 * Export debug report
 */
function recruitpro_export_debug_report() {
    $theme = wp_get_theme();
    
    $debug_info = array(
        'theme' => array(
            'name' => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'template' => get_template(),
            'stylesheet' => get_stylesheet(),
        ),
        'wordpress' => array(
            'version' => get_bloginfo('version'),
            'multisite' => is_multisite(),
            'debug' => defined('WP_DEBUG') && WP_DEBUG,
        ),
        'server' => array(
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ),
        'performance' => array(
            'memory_usage' => memory_get_peak_usage(true),
            'query_count' => get_num_queries(),
            'page_load_time' => timer_stop(0),
        ),
        'plugins' => array(
            'active' => get_option('active_plugins'),
            'recruitment_plugins' => array(
                'recruitpro_crm' => class_exists('RecruitPro_CRM'),
                'recruitpro_jobs' => class_exists('RecruitPro_Jobs'),
            ),
        ),
        'customizer' => get_theme_mods(),
        'errors' => array(),
    );
    
    // Add recent errors if log exists
    $log_file = wp_upload_dir()['basedir'] . '/recruitpro-debug/theme-errors.log';
    if (file_exists($log_file)) {
        $log_contents = file_get_contents($log_file);
        $log_lines = explode("\n", $log_contents);
        $recent_lines = array_slice($log_lines, -20);
        
        foreach ($recent_lines as $line) {
            if (!empty($line)) {
                $entry = json_decode($line, true);
                if ($entry) {
                    $debug_info['errors'][] = $entry;
                }
            }
        }
    }
    
    $filename = 'recruitpro-debug-' . date('Y-m-d-H-i-s') . '.json';
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen(wp_json_encode($debug_info, JSON_PRETTY_PRINT)));
    
    echo wp_json_encode($debug_info, JSON_PRETTY_PRINT);
}

/**
 * Template function to check if debug mode is active
 */
function recruitpro_is_debug_mode() {
    return recruitpro_should_enable_debug_tools();
}

/**
 * Template function to log debug message
 */
function recruitpro_debug_log($message, $context = array()) {
    if (recruitpro_should_enable_debug_tools()) {
        recruitpro_log_theme_error($message, $context);
    }
}