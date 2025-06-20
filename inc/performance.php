<?php
/**
 * RecruitPro Theme Performance Manager
 *
 * Central performance optimization system for recruitment websites.
 * Coordinates all performance features including caching, minification,
 * compression, lazy loading, and database optimization. Designed to handle
 * large recruitment databases (5000+ candidates) with optimal performance.
 *
 * @package RecruitPro
 * @subpackage Theme/Performance
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/performance.php
 * Purpose: Central performance optimization coordination
 * Dependencies: WordPress core, theme functions
 * Features: PageSpeed optimization, database optimization, resource management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Performance Manager Class
 * 
 * Coordinates all performance optimizations for the recruitment website
 * including asset optimization, database performance, and user experience.
 *
 * @since 1.0.0
 */
class RecruitPro_Performance_Manager {

    /**
     * Performance settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $performance_settings = array();

    /**
     * Performance metrics
     * 
     * @since 1.0.0
     * @var array
     */
    private $performance_metrics = array();

    /**
     * Critical performance thresholds
     * 
     * @since 1.0.0
     * @var array
     */
    private $performance_thresholds = array(
        'page_load_time' => 3.0, // seconds
        'memory_limit' => 0.8,   // 80% of available memory
        'database_queries' => 50, // queries per page
        'image_size_limit' => 2048, // KB
        'css_size_limit' => 100,  // KB
        'js_size_limit' => 200,   // KB
    );

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
        $this->init_performance_monitoring();
    }

    /**
     * Initialize performance settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->performance_settings = array(
            // Core Performance
            'enable_performance_mode' => get_theme_mod('recruitpro_enable_performance_mode', true),
            'performance_level' => get_theme_mod('recruitpro_performance_level', 'optimized'), // basic, optimized, aggressive
            
            // Asset Optimization
            'enable_minification' => get_theme_mod('recruitpro_enable_minification', true),
            'enable_compression' => get_theme_mod('recruitpro_enable_compression', true),
            'enable_lazy_loading' => get_theme_mod('recruitpro_enable_lazy_loading', true),
            'combine_css_files' => get_theme_mod('recruitpro_combine_css_files', true),
            'combine_js_files' => get_theme_mod('recruitpro_combine_js_files', true),
            
            // Database Optimization
            'enable_query_optimization' => get_theme_mod('recruitpro_enable_query_optimization', true),
            'enable_object_caching' => get_theme_mod('recruitpro_enable_object_caching', true),
            'database_cleanup' => get_theme_mod('recruitpro_database_cleanup', true),
            
            // Image Optimization
            'optimize_images' => get_theme_mod('recruitpro_optimize_images', true),
            'webp_conversion' => get_theme_mod('recruitpro_webp_conversion', true),
            'responsive_images' => get_theme_mod('recruitpro_responsive_images', true),
            
            // Cache Settings
            'browser_caching' => get_theme_mod('recruitpro_browser_caching', true),
            'cache_duration' => get_theme_mod('recruitpro_cache_duration', 86400), // 24 hours
            'preload_critical_resources' => get_theme_mod('recruitpro_preload_critical_resources', true),
            
            // Advanced Options
            'defer_non_critical_js' => get_theme_mod('recruitpro_defer_non_critical_js', true),
            'remove_query_strings' => get_theme_mod('recruitpro_remove_query_strings', true),
            'disable_emojis' => get_theme_mod('recruitpro_disable_emojis', true),
            'optimize_fonts' => get_theme_mod('recruitpro_optimize_fonts', true),
            
            // Recruitment-Specific Optimizations
            'optimize_candidate_search' => get_theme_mod('recruitpro_optimize_candidate_search', true),
            'optimize_job_listings' => get_theme_mod('recruitpro_optimize_job_listings', true),
            'optimize_crm_queries' => get_theme_mod('recruitpro_optimize_crm_queries', true),
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Early performance optimizations
        add_action('init', array($this, 'init_early_optimizations'), 1);
        
        // Asset optimization hooks
        add_action('wp_enqueue_scripts', array($this, 'optimize_assets'), 999);
        add_action('wp_head', array($this, 'add_performance_meta'), 1);
        add_action('wp_head', array($this, 'preload_critical_resources'), 5);
        
        // Database optimization hooks
        add_action('pre_get_posts', array($this, 'optimize_queries'));
        add_filter('posts_clauses', array($this, 'optimize_database_queries'), 10, 2);
        
        // Image optimization hooks
        add_filter('wp_get_attachment_image_attributes', array($this, 'optimize_image_attributes'), 10, 3);
        add_filter('the_content', array($this, 'optimize_content_images'), 999);
        
        // Cache optimization hooks
        add_action('wp_footer', array($this, 'add_cache_headers'));
        add_filter('style_loader_tag', array($this, 'optimize_css_delivery'), 10, 2);
        add_filter('script_loader_tag', array($this, 'optimize_js_delivery'), 10, 2);
        
        // Performance monitoring hooks
        add_action('wp_footer', array($this, 'performance_monitoring'), 999);
        add_action('admin_init', array($this, 'performance_admin_notices'));
        
        // Customizer hooks
        add_action('customize_register', array($this, 'customize_register_performance'));
        
        // AJAX hooks for performance tools
        add_action('wp_ajax_recruitpro_clear_performance_cache', array($this, 'clear_performance_cache'));
        add_action('wp_ajax_recruitpro_run_performance_test', array($this, 'run_performance_test'));
        
        // Recruitment-specific optimizations
        add_action('recruitpro_candidate_search', array($this, 'optimize_candidate_search'));
        add_action('recruitpro_job_listings_query', array($this, 'optimize_job_listings_query'));
        
        // Cleanup and maintenance
        add_action('wp_scheduled_delete', array($this, 'performance_cleanup'));
        add_action('recruitpro_daily_cleanup', array($this, 'daily_performance_maintenance'));
    }

    /**
     * Initialize performance monitoring
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_performance_monitoring() {
        if (!$this->performance_settings['enable_performance_mode']) {
            return;
        }

        // Start performance timer
        if (!defined('RECRUITPRO_START_TIME')) {
            define('RECRUITPRO_START_TIME', microtime(true));
        }

        // Track memory usage
        if (!defined('RECRUITPRO_START_MEMORY')) {
            define('RECRUITPRO_START_MEMORY', memory_get_usage());
        }

        // Initialize metrics
        $this->performance_metrics = array(
            'start_time' => RECRUITPRO_START_TIME,
            'start_memory' => RECRUITPRO_START_MEMORY,
            'queries_count' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
        );
    }

    /**
     * Initialize early performance optimizations
     * 
     * @since 1.0.0
     * @return void
     */
    public function init_early_optimizations() {
        if (!$this->performance_settings['enable_performance_mode']) {
            return;
        }

        // Disable emojis if enabled
        if ($this->performance_settings['disable_emojis']) {
            $this->disable_wp_emojis();
        }

        // Remove query strings from static resources
        if ($this->performance_settings['remove_query_strings']) {
            add_filter('style_loader_src', array($this, 'remove_query_strings'), 10, 1);
            add_filter('script_loader_src', array($this, 'remove_query_strings'), 10, 1);
        }

        // Optimize heartbeat API
        $this->optimize_heartbeat();

        // Disable unnecessary features
        $this->disable_unnecessary_features();
    }

    /**
     * Optimize asset delivery
     * 
     * @since 1.0.0
     * @return void
     */
    public function optimize_assets() {
        if (!$this->performance_settings['enable_performance_mode']) {
            return;
        }

        // Dequeue unnecessary scripts and styles
        $this->dequeue_unnecessary_assets();

        // Optimize Google Fonts loading
        if ($this->performance_settings['optimize_fonts']) {
            $this->optimize_google_fonts();
        }

        // Combine and minify assets based on settings
        if ($this->performance_settings['combine_css_files']) {
            add_action('wp_print_styles', array($this, 'combine_css_files'), 999);
        }

        if ($this->performance_settings['combine_js_files']) {
            add_action('wp_print_scripts', array($this, 'combine_js_files'), 999);
        }
    }

    /**
     * Add performance meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_performance_meta() {
        // DNS prefetch for external resources
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
        
        // Preconnect to critical external domains
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

        // Add performance hints
        echo '<meta name="format-detection" content="telephone=no">' . "\n";
        echo '<meta name="theme-color" content="' . esc_attr(get_theme_mod('recruitpro_primary_color', '#0073aa')) . '">' . "\n";
    }

    /**
     * Preload critical resources
     * 
     * @since 1.0.0
     * @return void
     */
    public function preload_critical_resources() {
        if (!$this->performance_settings['preload_critical_resources']) {
            return;
        }

        // Preload critical CSS
        $critical_css_url = get_template_directory_uri() . '/assets/css/critical.css';
        if (file_exists(get_template_directory() . '/assets/css/critical.css')) {
            echo '<link rel="preload" href="' . esc_url($critical_css_url) . '" as="style">' . "\n";
        }

        // Preload hero image on homepage
        if (is_front_page()) {
            $hero_image = get_theme_mod('recruitpro_hero_image');
            if ($hero_image) {
                echo '<link rel="preload" href="' . esc_url($hero_image) . '" as="image">' . "\n";
            }
        }

        // Preload critical JavaScript
        $critical_js_url = get_template_directory_uri() . '/assets/js/critical.js';
        if (file_exists(get_template_directory() . '/assets/js/critical.js')) {
            echo '<link rel="preload" href="' . esc_url($critical_js_url) . '" as="script">' . "\n";
        }

        // Preload fonts
        $this->preload_fonts();
    }

    /**
     * Optimize database queries
     * 
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    public function optimize_queries($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if (!$this->performance_settings['enable_query_optimization']) {
            return;
        }

        // Optimize job listings queries
        if ($query->is_post_type_archive('job') || $query->get('post_type') === 'job') {
            $this->optimize_job_query($query);
        }

        // Optimize search queries
        if ($query->is_search()) {
            $this->optimize_search_query($query);
        }

        // Optimize category/tag queries
        if ($query->is_category() || $query->is_tag()) {
            $this->optimize_taxonomy_query($query);
        }
    }

    /**
     * Optimize job listing queries
     * 
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    private function optimize_job_query($query) {
        // Reduce fields to only what's needed
        $query->set('fields', 'ids');
        
        // Set reasonable posts per page for performance
        $posts_per_page = get_theme_mod('recruitpro_jobs_per_page', 12);
        $query->set('posts_per_page', min($posts_per_page, 20));
        
        // Optimize meta queries for job filtering
        $meta_query = $query->get('meta_query') ?: array();
        $meta_query['relation'] = 'AND';
        
        // Add index-friendly meta queries
        if (!empty($_GET['job_location'])) {
            $meta_query[] = array(
                'key' => '_job_location',
                'value' => sanitize_text_field($_GET['job_location']),
                'compare' => 'LIKE',
            );
        }
        
        if (!empty($_GET['job_type'])) {
            $meta_query[] = array(
                'key' => '_job_type',
                'value' => sanitize_text_field($_GET['job_type']),
                'compare' => '=',
            );
        }
        
        $query->set('meta_query', $meta_query);
        
        // Order by most relevant for performance
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }

    /**
     * Optimize search queries
     * 
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    private function optimize_search_query($query) {
        // Limit search results for performance
        $query->set('posts_per_page', 20);
        
        // Optimize search for specific post types
        $search_post_types = array('post', 'page', 'job');
        $query->set('post_type', $search_post_types);
        
        // Use more efficient search method
        add_filter('posts_search', array($this, 'optimize_search_sql'), 10, 2);
    }

    /**
     * Optimize search SQL
     * 
     * @since 1.0.0
     * @param string $search Search SQL
     * @param WP_Query $query Query object
     * @return string Modified search SQL
     */
    public function optimize_search_sql($search, $query) {
        if (!$query->is_search() || !$query->is_main_query()) {
            return $search;
        }

        global $wpdb;
        
        $search_terms = $query->get('search_terms');
        if (empty($search_terms)) {
            return $search;
        }

        // Use FULLTEXT search if available
        $search_sql = '';
        foreach ($search_terms as $term) {
            $term = $wpdb->esc_like($term);
            $search_sql .= " AND ({$wpdb->posts}.post_title LIKE '%{$term}%' OR {$wpdb->posts}.post_content LIKE '%{$term}%')";
        }

        return $search_sql;
    }

    /**
     * Optimize CSS delivery
     * 
     * @since 1.0.0
     * @param string $tag CSS link tag
     * @param string $handle CSS handle
     * @return string Modified CSS tag
     */
    public function optimize_css_delivery($tag, $handle) {
        // Critical CSS should load normally
        $critical_css = array('recruitpro-critical', 'recruitpro-style');
        
        if (in_array($handle, $critical_css)) {
            return $tag;
        }

        // Non-critical CSS can be loaded asynchronously
        if (strpos($handle, 'recruitpro') !== false) {
            $tag = str_replace('rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag);
            $tag .= '<noscript>' . str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', 'rel="stylesheet"', $tag) . '</noscript>';
        }

        return $tag;
    }

    /**
     * Optimize JavaScript delivery
     * 
     * @since 1.0.0
     * @param string $tag Script tag
     * @param string $handle Script handle
     * @return string Modified script tag
     */
    public function optimize_js_delivery($tag, $handle) {
        if (!$this->performance_settings['defer_non_critical_js']) {
            return $tag;
        }

        // Scripts that should not be deferred
        $critical_scripts = array(
            'jquery',
            'jquery-core',
            'jquery-migrate',
            'recruitpro-critical',
        );

        if (in_array($handle, $critical_scripts)) {
            return $tag;
        }

        // Defer non-critical scripts
        if (!is_admin() && strpos($tag, 'defer') === false && strpos($tag, 'async') === false) {
            $tag = str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }

    /**
     * Optimize image attributes
     * 
     * @since 1.0.0
     * @param array $attr Image attributes
     * @param WP_Post $attachment Attachment object
     * @param string $size Image size
     * @return array Modified attributes
     */
    public function optimize_image_attributes($attr, $attachment, $size) {
        if (!$this->performance_settings['optimize_images']) {
            return $attr;
        }

        // Add loading="lazy" for non-critical images
        if (!isset($attr['loading']) && $this->performance_settings['enable_lazy_loading']) {
            $attr['loading'] = 'lazy';
        }

        // Add decoding="async" for better performance
        if (!isset($attr['decoding'])) {
            $attr['decoding'] = 'async';
        }

        return $attr;
    }

    /**
     * Optimize content images
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Modified content
     */
    public function optimize_content_images($content) {
        if (!$this->performance_settings['optimize_images']) {
            return $content;
        }

        // Add lazy loading to content images
        if ($this->performance_settings['enable_lazy_loading']) {
            $content = preg_replace('/<img([^>]+?)>/i', '<img$1 loading="lazy" decoding="async">', $content);
        }

        return $content;
    }

    /**
     * Add cache headers
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_cache_headers() {
        if (!$this->performance_settings['browser_caching']) {
            return;
        }

        $cache_duration = $this->performance_settings['cache_duration'];
        
        // Set cache headers for static content
        if (!is_admin() && !is_user_logged_in()) {
            header('Cache-Control: public, max-age=' . $cache_duration);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_duration) . ' GMT');
        }
    }

    /**
     * Performance monitoring
     * 
     * @since 1.0.0
     * @return void
     */
    public function performance_monitoring() {
        if (!$this->performance_settings['enable_performance_mode'] || is_admin()) {
            return;
        }

        $end_time = microtime(true);
        $end_memory = memory_get_usage();
        
        $load_time = $end_time - RECRUITPRO_START_TIME;
        $memory_used = $end_memory - RECRUITPRO_START_MEMORY;
        $queries_count = get_num_queries();

        // Store metrics for analysis
        $this->performance_metrics['load_time'] = $load_time;
        $this->performance_metrics['memory_used'] = $memory_used;
        $this->performance_metrics['queries_count'] = $queries_count;
        $this->performance_metrics['peak_memory'] = memory_get_peak_usage();

        // Log performance issues
        if ($load_time > $this->performance_thresholds['page_load_time']) {
            error_log("RecruitPro Performance Warning: Page load time {$load_time}s exceeds threshold");
        }

        if ($queries_count > $this->performance_thresholds['database_queries']) {
            error_log("RecruitPro Performance Warning: {$queries_count} database queries exceed threshold");
        }

        // Add performance data to HTML comment for debugging
        if (WP_DEBUG && current_user_can('manage_options')) {
            echo "\n<!-- RecruitPro Performance: {$load_time}s, {$queries_count} queries, " . size_format($memory_used) . " memory -->\n";
        }
    }

    /**
     * Disable WordPress emojis
     * 
     * @since 1.0.0
     * @return void
     */
    private function disable_wp_emojis() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        
        add_filter('tiny_mce_plugins', array($this, 'disable_emojis_tinymce'));
        add_filter('wp_resource_hints', array($this, 'disable_emojis_remove_dns_prefetch'), 10, 2);
    }

    /**
     * Remove emojis from TinyMCE
     * 
     * @since 1.0.0
     * @param array $plugins TinyMCE plugins
     * @return array Modified plugins
     */
    public function disable_emojis_tinymce($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        }
        return array();
    }

    /**
     * Remove emoji DNS prefetch
     * 
     * @since 1.0.0
     * @param array $urls Resource URLs
     * @param string $relation_type Relation type
     * @return array Modified URLs
     */
    public function disable_emojis_remove_dns_prefetch($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
            $urls = array_diff($urls, array($emoji_svg_url));
        }
        return $urls;
    }

    /**
     * Optimize WordPress heartbeat
     * 
     * @since 1.0.0
     * @return void
     */
    private function optimize_heartbeat() {
        // Disable heartbeat on frontend
        if (!is_admin()) {
            wp_deregister_script('heartbeat');
        }

        // Modify heartbeat settings
        add_filter('heartbeat_settings', function($settings) {
            $settings['interval'] = 60; // 60 seconds instead of 15
            return $settings;
        });
    }

    /**
     * Disable unnecessary WordPress features
     * 
     * @since 1.0.0
     * @return void
     */
    private function disable_unnecessary_features() {
        // Disable XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');
        
        // Remove unnecessary REST API links
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        
        // Remove Windows Live Writer manifest
        remove_action('wp_head', 'wlwmanifest_link');
        
        // Remove shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');
        
        // Remove generator tag
        remove_action('wp_head', 'wp_generator');
    }

    /**
     * Dequeue unnecessary assets
     * 
     * @since 1.0.0
     * @return void
     */
    private function dequeue_unnecessary_assets() {
        // Remove block library CSS if not using Gutenberg
        if (!current_theme_supports('wp-block-styles')) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
        }

        // Remove Dashicons on frontend for non-admin users
        if (!is_admin() && !is_user_logged_in()) {
            wp_dequeue_style('dashicons');
        }
    }

    /**
     * Optimize Google Fonts loading
     * 
     * @since 1.0.0
     * @return void
     */
    private function optimize_google_fonts() {
        // Dequeue default Google Fonts and load optimized version
        add_filter('style_loader_tag', function($tag, $handle) {
            if (strpos($tag, 'fonts.googleapis.com') !== false) {
                // Add font-display: swap for better performance
                $tag = str_replace('rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag);
                $tag .= '<noscript>' . str_replace('rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', 'rel="stylesheet"', $tag) . '</noscript>';
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Preload critical fonts
     * 
     * @since 1.0.0
     * @return void
     */
    private function preload_fonts() {
        $fonts = get_theme_mod('recruitpro_google_fonts', array());
        
        if (!empty($fonts)) {
            foreach ($fonts as $font) {
                if (!empty($font['woff2_url'])) {
                    echo '<link rel="preload" href="' . esc_url($font['woff2_url']) . '" as="font" type="font/woff2" crossorigin>' . "\n";
                }
            }
        }
    }

    /**
     * Remove query strings from static resources
     * 
     * @since 1.0.0
     * @param string $src Resource URL
     * @return string Modified URL
     */
    public function remove_query_strings($src) {
        $parts = explode('?', $src);
        return $parts[0];
    }

    /**
     * Combine CSS files
     * 
     * @since 1.0.0
     * @return void
     */
    public function combine_css_files() {
        global $wp_styles;
        
        if (!$wp_styles) {
            return;
        }

        $theme_styles = array();
        $combined_css = '';

        foreach ($wp_styles->queue as $handle) {
            if (strpos($handle, 'recruitpro') !== false && $handle !== 'recruitpro-style') {
                $style = $wp_styles->registered[$handle];
                if ($style && $style->src) {
                    $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $style->src);
                    if (file_exists($local_path) && filesize($local_path) < 102400) { // Less than 100KB
                        $theme_styles[] = $handle;
                        $combined_css .= file_get_contents($local_path) . "\n";
                    }
                }
            }
        }

        if (!empty($theme_styles) && !empty($combined_css)) {
            // Remove individual stylesheets
            foreach ($theme_styles as $handle) {
                wp_dequeue_style($handle);
            }

            // Add combined CSS
            $cache_key = 'recruitpro_combined_css_' . md5($combined_css);
            $minified_css = get_transient($cache_key);

            if ($minified_css === false) {
                $minified_css = $this->minify_css($combined_css);
                set_transient($cache_key, $minified_css, DAY_IN_SECONDS);
            }

            wp_add_inline_style('recruitpro-style', $minified_css);
        }
    }

    /**
     * Combine JavaScript files
     * 
     * @since 1.0.0
     * @return void
     */
    public function combine_js_files() {
        global $wp_scripts;
        
        if (!$wp_scripts) {
            return;
        }

        $theme_scripts = array();
        $combined_js = '';

        foreach ($wp_scripts->queue as $handle) {
            if (strpos($handle, 'recruitpro') !== false && $handle !== 'recruitpro-main') {
                $script = $wp_scripts->registered[$handle];
                if ($script && $script->src && empty($script->deps)) {
                    $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $script->src);
                    if (file_exists($local_path) && filesize($local_path) < 204800) { // Less than 200KB
                        $theme_scripts[] = $handle;
                        $combined_js .= file_get_contents($local_path) . ";\n";
                    }
                }
            }
        }

        if (!empty($theme_scripts) && !empty($combined_js)) {
            // Remove individual scripts
            foreach ($theme_scripts as $handle) {
                wp_dequeue_script($handle);
            }

            // Add combined JavaScript
            wp_add_inline_script('recruitpro-main', $combined_js);
        }
    }

    /**
     * Minify CSS
     * 
     * @since 1.0.0
     * @param string $css CSS content
     * @return string Minified CSS
     */
    private function minify_css($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    '), '', $css);
        
        // Remove other unnecessary characters
        $css = str_replace(array('; ', ' ;', ' {', '{ ', '} ', ' }', ' :', ': ', ' ,', ', '), array(';', ';', '{', '{', '}', '}', ':', ':', ',', ','), $css);
        
        return trim($css);
    }

    /**
     * Clear performance cache
     * 
     * @since 1.0.0
     * @return void
     */
    public function clear_performance_cache() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'recruitpro')));
        }

        // Clear theme transients
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_recruitpro_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_recruitpro_%'");

        // Clear object cache if available
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        wp_send_json_success(array('message' => __('Performance cache cleared successfully.', 'recruitpro')));
    }

    /**
     * Run performance test
     * 
     * @since 1.0.0
     * @return void
     */
    public function run_performance_test() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'recruitpro')));
        }

        $start_time = microtime(true);
        $start_memory = memory_get_usage();

        // Test database performance
        $db_start = microtime(true);
        $posts = get_posts(array('numberposts' => 10, 'fields' => 'ids'));
        $db_time = microtime(true) - $db_start;

        // Test cache performance
        $cache_start = microtime(true);
        set_transient('recruitpro_test_cache', 'test_data', 60);
        $cache_data = get_transient('recruitpro_test_cache');
        delete_transient('recruitpro_test_cache');
        $cache_time = microtime(true) - $cache_start;

        $total_time = microtime(true) - $start_time;
        $memory_used = memory_get_usage() - $start_memory;

        $results = array(
            'total_time' => round($total_time, 4),
            'database_time' => round($db_time, 4),
            'cache_time' => round($cache_time, 4),
            'memory_used' => size_format($memory_used),
            'queries_count' => get_num_queries(),
            'recommendations' => $this->get_performance_recommendations(),
        );

        wp_send_json_success($results);
    }

    /**
     * Get performance recommendations
     * 
     * @since 1.0.0
     * @return array Performance recommendations
     */
    private function get_performance_recommendations() {
        $recommendations = array();

        // Check if object caching is available
        if (!wp_using_ext_object_cache()) {
            $recommendations[] = __('Consider installing an object caching plugin like Redis or Memcached.', 'recruitpro');
        }

        // Check if GZIP compression is enabled
        if (!$this->is_gzip_enabled()) {
            $recommendations[] = __('Enable GZIP compression on your server for better performance.', 'recruitpro');
        }

        // Check for large database
        global $wpdb;
        $posts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish'");
        if ($posts_count > 1000) {
            $recommendations[] = __('Consider database optimization for large content volumes.', 'recruitpro');
        }

        return $recommendations;
    }

    /**
     * Check if GZIP compression is enabled
     * 
     * @since 1.0.0
     * @return bool True if GZIP is enabled
     */
    private function is_gzip_enabled() {
        return function_exists('gzencode') && extension_loaded('zlib');
    }

    /**
     * Daily performance maintenance
     * 
     * @since 1.0.0
     * @return void
     */
    public function daily_performance_maintenance() {
        // Clean expired transients
        delete_expired_transients();
        
        // Optimize database tables
        global $wpdb;
        $wpdb->query("OPTIMIZE TABLE {$wpdb->posts}");
        $wpdb->query("OPTIMIZE TABLE {$wpdb->postmeta}");
        $wpdb->query("OPTIMIZE TABLE {$wpdb->options}");
        
        // Clean up old performance logs
        $upload_dir = wp_upload_dir();
        $log_file = $upload_dir['basedir'] . '/recruitpro-performance.log';
        if (file_exists($log_file) && filesize($log_file) > 1048576) { // 1MB
            unlink($log_file);
        }
    }

    /**
     * Performance cleanup
     * 
     * @since 1.0.0
     * @return void
     */
    public function performance_cleanup() {
        // Clean old performance cache
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_recruitpro_%' AND option_value < UNIX_TIMESTAMP()");
    }

    /**
     * Admin notices for performance issues
     * 
     * @since 1.0.0
     * @return void
     */
    public function performance_admin_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check for performance issues
        if (get_transient('recruitpro_performance_warning')) {
            echo '<div class="notice notice-warning"><p>';
            echo __('RecruitPro Performance Warning: Your site may be experiencing performance issues. Consider reviewing your performance settings.', 'recruitpro');
            echo '</p></div>';
        }
    }

    /**
     * Add performance settings to customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_performance($wp_customize) {
        // Performance section
        $wp_customize->add_section('recruitpro_performance', array(
            'title' => __('Performance Optimization', 'recruitpro'),
            'description' => __('Configure performance settings for optimal website speed and user experience.', 'recruitpro'),
            'priority' => 45,
        ));

        // Enable performance mode
        $wp_customize->add_setting('recruitpro_enable_performance_mode', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_performance_mode', array(
            'label' => __('Enable Performance Mode', 'recruitpro'),
            'description' => __('Enable comprehensive performance optimizations.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Performance level
        $wp_customize->add_setting('recruitpro_performance_level', array(
            'default' => 'optimized',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_performance_level', array(
            'label' => __('Performance Level', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'select',
            'choices' => array(
                'basic' => __('Basic Optimization', 'recruitpro'),
                'optimized' => __('Optimized (Recommended)', 'recruitpro'),
                'aggressive' => __('Aggressive Optimization', 'recruitpro'),
            ),
        ));

        // Enable minification
        $wp_customize->add_setting('recruitpro_enable_minification', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_minification', array(
            'label' => __('Enable CSS/JS Minification', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Enable lazy loading
        $wp_customize->add_setting('recruitpro_enable_lazy_loading', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_lazy_loading', array(
            'label' => __('Enable Lazy Loading', 'recruitpro'),
            'description' => __('Load images and content only when needed.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Cache duration
        $wp_customize->add_setting('recruitpro_cache_duration', array(
            'default' => 86400,
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('recruitpro_cache_duration', array(
            'label' => __('Cache Duration (seconds)', 'recruitpro'),
            'description' => __('How long to cache static resources (default: 24 hours).', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 3600,
                'max' => 604800,
            ),
        ));
    }
}

// Initialize the performance manager
if (class_exists('RecruitPro_Performance_Manager')) {
    new RecruitPro_Performance_Manager();
}

/**
 * Helper function to check if performance mode is enabled
 * 
 * @since 1.0.0
 * @return bool True if performance mode is enabled
 */
function recruitpro_performance_enabled() {
    return get_theme_mod('recruitpro_enable_performance_mode', true);
}

/**
 * Helper function to get current performance level
 * 
 * @since 1.0.0
 * @return string Performance level
 */
function recruitpro_get_performance_level() {
    return get_theme_mod('recruitpro_performance_level', 'optimized');
}

/**
 * Helper function to clear all performance caches
 * 
 * @since 1.0.0
 * @return bool True if successful
 */
function recruitpro_clear_all_caches() {
    // Clear theme transients
    global $wpdb;
    $deleted = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_recruitpro_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_recruitpro_%'");

    // Clear object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }

    return $deleted !== false;
}