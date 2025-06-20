<?php
/**
 * RecruitPro Theme Asset Minification
 *
 * Handles CSS and JavaScript minification, combining, and optimization
 * for the RecruitPro recruitment theme. Provides intelligent asset
 * optimization with caching, conditional loading, and performance enhancements.
 *
 * @package RecruitPro
 * @subpackage Theme/Performance
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/minification.php
 * Purpose: Asset minification and optimization
 * Dependencies: WordPress core, theme enqueue system
 * Conflicts: None (compatible with caching plugins)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Asset Minification Class
 * 
 * Handles intelligent minification and optimization of CSS and JavaScript
 * files for improved performance while maintaining functionality.
 *
 * @since 1.0.0
 */
class RecruitPro_Asset_Minification {

    /**
     * Cache directory for minified files
     * 
     * @since 1.0.0
     * @var string
     */
    private $cache_dir;

    /**
     * Cache URL for minified files
     * 
     * @since 1.0.0
     * @var string
     */
    private $cache_url;

    /**
     * Minification settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $settings;

    /**
     * Performance metrics
     * 
     * @since 1.0.0
     * @var array
     */
    private $metrics;

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_cache_directory();
        $this->init_settings();
        $this->init_metrics();
        
        // Only run minification if enabled and not in debug mode
        if ($this->is_minification_enabled()) {
            add_action('wp_enqueue_scripts', array($this, 'process_assets'), 999);
            add_filter('style_loader_tag', array($this, 'minify_css_output'), 10, 4);
            add_filter('script_loader_tag', array($this, 'optimize_js_output'), 10, 3);
            add_action('wp_head', array($this, 'output_critical_css'), 1);
            add_action('wp_footer', array($this, 'output_deferred_assets'), 999);
        }
        
        // Admin hooks
        add_action('customize_register', array($this, 'add_minification_customizer_options'));
        add_action('admin_post_recruitpro_clear_minification_cache', array($this, 'clear_cache'));
        add_action('wp_ajax_recruitpro_minification_stats', array($this, 'get_minification_stats'));
        
        // Cleanup hooks
        add_action('switch_theme', array($this, 'clear_cache'));
        add_action('upgrader_process_complete', array($this, 'clear_cache'));
        
        // Asset combining
        if ($this->is_combining_enabled()) {
            add_action('wp_enqueue_scripts', array($this, 'combine_assets'), 998);
        }
    }

    /**
     * Initialize cache directory
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_cache_directory() {
        
        $upload_dir = wp_upload_dir();
        $this->cache_dir = $upload_dir['basedir'] . '/recruitpro-cache/minified/';
        $this->cache_url = $upload_dir['baseurl'] . '/recruitpro-cache/minified/';
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cache_dir)) {
            wp_mkdir_p($this->cache_dir);
            
            // Add .htaccess for cache control
            $htaccess_content = "# RecruitPro Minified Assets Cache\n";
            $htaccess_content .= "ExpiresActive On\n";
            $htaccess_content .= "ExpiresByType text/css \"access plus 1 year\"\n";
            $htaccess_content .= "ExpiresByType application/javascript \"access plus 1 year\"\n";
            $htaccess_content .= "ExpiresByType text/javascript \"access plus 1 year\"\n";
            $htaccess_content .= "\n# Gzip compression\n";
            $htaccess_content .= "<IfModule mod_deflate.c>\n";
            $htaccess_content .= "    AddOutputFilterByType DEFLATE text/css\n";
            $htaccess_content .= "    AddOutputFilterByType DEFLATE application/javascript\n";
            $htaccess_content .= "    AddOutputFilterByType DEFLATE text/javascript\n";
            $htaccess_content .= "</IfModule>\n";
            
            file_put_contents($this->cache_dir . '.htaccess', $htaccess_content);
        }
    }

    /**
     * Initialize minification settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        
        $this->settings = array(
            'enable_css_minification' => get_theme_mod('recruitpro_minify_css', true),
            'enable_js_minification' => get_theme_mod('recruitpro_minify_js', true),
            'enable_combining' => get_theme_mod('recruitpro_combine_assets', true),
            'enable_critical_css' => get_theme_mod('recruitpro_critical_css', true),
            'enable_deferred_loading' => get_theme_mod('recruitpro_defer_assets', true),
            'enable_preloading' => get_theme_mod('recruitpro_preload_assets', true),
            'cache_duration' => get_theme_mod('recruitpro_minification_cache_duration', DAY_IN_SECONDS),
            'exclude_handles' => get_theme_mod('recruitpro_minification_exclude', array()),
            'debug_mode' => get_theme_mod('recruitpro_minification_debug', false),
        );
        
        // Force disable in debug mode
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $this->settings['enable_css_minification'] = false;
            $this->settings['enable_js_minification'] = false;
            $this->settings['enable_combining'] = false;
        }
    }

    /**
     * Initialize performance metrics tracking
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_metrics() {
        
        $this->metrics = array(
            'css_files_processed' => 0,
            'js_files_processed' => 0,
            'css_size_saved' => 0,
            'js_size_saved' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
            'processing_time' => 0,
        );
    }

    /**
     * Check if minification is enabled
     * 
     * @since 1.0.0
     * @return bool
     */
    private function is_minification_enabled() {
        
        // Don't minify in admin, customizer, or if caching plugin handles it
        if (is_admin() || is_customize_preview() || $this->has_caching_plugin()) {
            return false;
        }
        
        return $this->settings['enable_css_minification'] || $this->settings['enable_js_minification'];
    }

    /**
     * Check if asset combining is enabled
     * 
     * @since 1.0.0
     * @return bool
     */
    private function is_combining_enabled() {
        
        return $this->settings['enable_combining'] && !is_customize_preview();
    }

    /**
     * Check if a caching plugin is active
     * 
     * @since 1.0.0
     * @return bool
     */
    private function has_caching_plugin() {
        
        $caching_plugins = array(
            'wp-rocket/wp-rocket.php',
            'w3-total-cache/w3-total-cache.php',
            'wp-super-cache/wp-cache.php',
            'wp-fastest-cache/wpFastestCache.php',
            'autoptimize/autoptimize.php',
            'wp-optimize/wp-optimize.php',
        );
        
        foreach ($caching_plugins as $plugin) {
            if (is_plugin_active($plugin)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Process and optimize assets
     * 
     * @since 1.0.0
     * @return void
     */
    public function process_assets() {
        
        global $wp_styles, $wp_scripts;
        
        $start_time = microtime(true);
        
        // Process CSS files
        if ($this->settings['enable_css_minification'] && $wp_styles) {
            $this->process_css_assets($wp_styles);
        }
        
        // Process JavaScript files
        if ($this->settings['enable_js_minification'] && $wp_scripts) {
            $this->process_js_assets($wp_scripts);
        }
        
        $this->metrics['processing_time'] = microtime(true) - $start_time;
        
        // Update metrics
        $this->update_performance_metrics();
    }

    /**
     * Process CSS assets
     * 
     * @since 1.0.0
     * @param WP_Styles $wp_styles WordPress styles object
     * @return void
     */
    private function process_css_assets($wp_styles) {
        
        foreach ($wp_styles->queue as $handle) {
            
            // Skip if handle is excluded
            if (in_array($handle, $this->settings['exclude_handles'])) {
                continue;
            }
            
            // Only process theme CSS files
            if (!$this->is_theme_asset($handle, 'style')) {
                continue;
            }
            
            $style = $wp_styles->registered[$handle];
            
            if ($style && $style->src) {
                $minified_file = $this->minify_css_file($style->src, $handle);
                
                if ($minified_file) {
                    // Replace original with minified version
                    $wp_styles->registered[$handle]->src = $minified_file;
                    $this->metrics['css_files_processed']++;
                }
            }
        }
    }

    /**
     * Process JavaScript assets
     * 
     * @since 1.0.0
     * @param WP_Scripts $wp_scripts WordPress scripts object
     * @return void
     */
    private function process_js_assets($wp_scripts) {
        
        foreach ($wp_scripts->queue as $handle) {
            
            // Skip if handle is excluded
            if (in_array($handle, $this->settings['exclude_handles'])) {
                continue;
            }
            
            // Only process theme JS files
            if (!$this->is_theme_asset($handle, 'script')) {
                continue;
            }
            
            $script = $wp_scripts->registered[$handle];
            
            if ($script && $script->src) {
                $minified_file = $this->minify_js_file($script->src, $handle);
                
                if ($minified_file) {
                    // Replace original with minified version
                    $wp_scripts->registered[$handle]->src = $minified_file;
                    $this->metrics['js_files_processed']++;
                }
            }
        }
    }

    /**
     * Minify CSS file
     * 
     * @since 1.0.0
     * @param string $src File source URL
     * @param string $handle Asset handle
     * @return string|false Minified file URL or false on failure
     */
    private function minify_css_file($src, $handle) {
        
        // Convert URL to local path
        $local_path = $this->url_to_path($src);
        
        if (!$local_path || !file_exists($local_path)) {
            return false;
        }
        
        // Generate cache filename
        $file_hash = md5($src . filemtime($local_path));
        $cache_file = $this->cache_dir . $handle . '-' . $file_hash . '.min.css';
        $cache_url = $this->cache_url . $handle . '-' . $file_hash . '.min.css';
        
        // Check if cached version exists
        if (file_exists($cache_file)) {
            $this->metrics['cache_hits']++;
            return $cache_url;
        }
        
        // Read original file
        $css_content = file_get_contents($local_path);
        if ($css_content === false) {
            return false;
        }
        
        $original_size = strlen($css_content);
        
        // Minify CSS content
        $minified_css = $this->minify_css_content($css_content, $local_path);
        
        if ($minified_css === false) {
            return false;
        }
        
        $minified_size = strlen($minified_css);
        $this->metrics['css_size_saved'] += ($original_size - $minified_size);
        
        // Save minified file
        if (file_put_contents($cache_file, $minified_css) === false) {
            return false;
        }
        
        $this->metrics['cache_misses']++;
        
        return $cache_url;
    }

    /**
     * Minify JavaScript file
     * 
     * @since 1.0.0
     * @param string $src File source URL
     * @param string $handle Asset handle
     * @return string|false Minified file URL or false on failure
     */
    private function minify_js_file($src, $handle) {
        
        // Convert URL to local path
        $local_path = $this->url_to_path($src);
        
        if (!$local_path || !file_exists($local_path)) {
            return false;
        }
        
        // Generate cache filename
        $file_hash = md5($src . filemtime($local_path));
        $cache_file = $this->cache_dir . $handle . '-' . $file_hash . '.min.js';
        $cache_url = $this->cache_url . $handle . '-' . $file_hash . '.min.js';
        
        // Check if cached version exists
        if (file_exists($cache_file)) {
            $this->metrics['cache_hits']++;
            return $cache_url;
        }
        
        // Read original file
        $js_content = file_get_contents($local_path);
        if ($js_content === false) {
            return false;
        }
        
        $original_size = strlen($js_content);
        
        // Minify JavaScript content
        $minified_js = $this->minify_js_content($js_content);
        
        if ($minified_js === false) {
            return false;
        }
        
        $minified_size = strlen($minified_js);
        $this->metrics['js_size_saved'] += ($original_size - $minified_size);
        
        // Save minified file
        if (file_put_contents($cache_file, $minified_js) === false) {
            return false;
        }
        
        $this->metrics['cache_misses']++;
        
        return $cache_url;
    }

    /**
     * Minify CSS content
     * 
     * @since 1.0.0
     * @param string $css CSS content
     * @param string $file_path Original file path for relative URL resolution
     * @return string Minified CSS content
     */
    private function minify_css_content($css, $file_path = '') {
        
        try {
            // Preserve important comments (license, etc.)
            $important_comments = array();
            $css = preg_replace_callback('/\/\*![\s\S]*?\*\//', function($matches) use (&$important_comments) {
                $placeholder = '/*IMPORTANT_COMMENT_' . count($important_comments) . '*/';
                $important_comments[] = $matches[0];
                return $placeholder;
            }, $css);
            
            // Remove regular comments
            $css = preg_replace('/\/\*(?!!)[\s\S]*?\*\//', '', $css);
            
            // Fix relative URLs if file path is provided
            if ($file_path) {
                $css = $this->fix_relative_urls($css, $file_path);
            }
            
            // Remove unnecessary whitespace
            $css = preg_replace('/\s+/', ' ', $css);
            
            // Remove whitespace around specific characters
            $css = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $css);
            
            // Remove empty rules
            $css = preg_replace('/[^{}]*\{\s*\}/', '', $css);
            
            // Remove trailing semicolons
            $css = preg_replace('/;\s*}/', '}', $css);
            
            // Remove quotes from font names when not needed
            $css = preg_replace('/font-family:\s*["\']([^"\',]+)["\']/i', 'font-family:$1', $css);
            
            // Optimize color values
            $css = preg_replace('/#([a-f0-9])\1([a-f0-9])\2([a-f0-9])\3/i', '#$1$2$3', $css);
            $css = str_replace(array('rgb(0,0,0)', 'rgb(255,255,255)'), array('#000', '#fff'), $css);
            
            // Remove unnecessary units
            $css = preg_replace('/(^|[^a-z])0(px|em|rem|%|cm|mm|in|pt|pc|ex|deg|grad|rad|turn|s|ms|hz|khz)/i', '$1 0', $css);
            
            // Restore important comments
            foreach ($important_comments as $index => $comment) {
                $css = str_replace('/*IMPORTANT_COMMENT_' . $index . '*/', $comment, $css);
            }
            
            return trim($css);
            
        } catch (Exception $e) {
            // Return original CSS if minification fails
            if ($this->settings['debug_mode']) {
                error_log('RecruitPro CSS Minification Error: ' . $e->getMessage());
            }
            return $css;
        }
    }

    /**
     * Minify JavaScript content
     * 
     * @since 1.0.0
     * @param string $js JavaScript content
     * @return string Minified JavaScript content
     */
    private function minify_js_content($js) {
        
        try {
            // Basic JavaScript minification
            // Note: For advanced minification, consider using external libraries
            
            // Remove single-line comments (but preserve URLs)
            $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
            
            // Remove multi-line comments
            $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
            
            // Remove unnecessary whitespace
            $js = preg_replace('/\s+/', ' ', $js);
            
            // Remove whitespace around operators and punctuation
            $js = preg_replace('/\s*([=+\-*\/&|!<>{}();,:])\s*/', '$1', $js);
            
            // Remove unnecessary semicolons
            $js = preg_replace('/;+/', ';', $js);
            
            // Remove trailing semicolons before closing braces
            $js = preg_replace('/;\s*}/', '}', $js);
            
            return trim($js);
            
        } catch (Exception $e) {
            // Return original JS if minification fails
            if ($this->settings['debug_mode']) {
                error_log('RecruitPro JS Minification Error: ' . $e->getMessage());
            }
            return $js;
        }
    }

    /**
     * Fix relative URLs in CSS
     * 
     * @since 1.0.0
     * @param string $css CSS content
     * @param string $file_path Original file path
     * @return string CSS with fixed URLs
     */
    private function fix_relative_urls($css, $file_path) {
        
        $file_dir = dirname($file_path);
        $site_url = site_url();
        
        return preg_replace_callback('/url\(\s*["\']?([^"\')]+)["\']?\s*\)/', function($matches) use ($file_dir, $site_url) {
            $url = $matches[1];
            
            // Skip absolute URLs and data URIs
            if (preg_match('/^(https?:|\/\/|data:)/', $url)) {
                return $matches[0];
            }
            
            // Convert relative URL to absolute
            if (strpos($url, '/') !== 0) {
                $absolute_path = realpath($file_dir . '/' . $url);
                if ($absolute_path && strpos($absolute_path, ABSPATH) === 0) {
                    $relative_path = str_replace(ABSPATH, '', $absolute_path);
                    $url = $site_url . '/' . str_replace('\\', '/', $relative_path);
                }
            }
            
            return 'url(' . $url . ')';
        }, $css);
    }

    /**
     * Combine CSS files
     * 
     * @since 1.0.0
     * @return void
     */
    public function combine_assets() {
        
        global $wp_styles, $wp_scripts;
        
        if ($this->settings['enable_combining']) {
            
            // Combine CSS files
            if ($wp_styles) {
                $this->combine_css_files($wp_styles);
            }
            
            // Combine JavaScript files
            if ($wp_scripts) {
                $this->combine_js_files($wp_scripts);
            }
        }
    }

    /**
     * Combine CSS files
     * 
     * @since 1.0.0
     * @param WP_Styles $wp_styles WordPress styles object
     * @return void
     */
    private function combine_css_files($wp_styles) {
        
        $theme_styles = array();
        $combined_content = '';
        $combined_size = 0;
        $max_combined_size = 100 * 1024; // 100KB limit
        
        foreach ($wp_styles->queue as $handle) {
            
            if (!$this->is_theme_asset($handle, 'style') || in_array($handle, $this->settings['exclude_handles'])) {
                continue;
            }
            
            $style = $wp_styles->registered[$handle];
            if (!$style || !$style->src) {
                continue;
            }
            
            $local_path = $this->url_to_path($style->src);
            if (!$local_path || !file_exists($local_path)) {
                continue;
            }
            
            $file_size = filesize($local_path);
            
            // Don't combine if it would exceed size limit
            if ($combined_size + $file_size > $max_combined_size) {
                break;
            }
            
            $theme_styles[] = $handle;
            $content = file_get_contents($local_path);
            $content = $this->fix_relative_urls($content, $local_path);
            $combined_content .= "\n/* " . $handle . " */\n" . $content;
            $combined_size += $file_size;
        }
        
        if (count($theme_styles) > 1 && !empty($combined_content)) {
            
            // Generate cache filename
            $cache_key = md5($combined_content);
            $cache_file = $this->cache_dir . 'combined-' . $cache_key . '.css';
            $cache_url = $this->cache_url . 'combined-' . $cache_key . '.css';
            
            if (!file_exists($cache_file)) {
                $minified_content = $this->minify_css_content($combined_content);
                file_put_contents($cache_file, $minified_content);
            }
            
            // Remove individual stylesheets
            foreach ($theme_styles as $handle) {
                wp_dequeue_style($handle);
            }
            
            // Enqueue combined stylesheet
            wp_enqueue_style('recruitpro-combined', $cache_url, array(), null);
        }
    }

    /**
     * Combine JavaScript files
     * 
     * @since 1.0.0
     * @param WP_Scripts $wp_scripts WordPress scripts object
     * @return void
     */
    private function combine_js_files($wp_scripts) {
        
        $theme_scripts = array();
        $combined_content = '';
        $combined_size = 0;
        $max_combined_size = 150 * 1024; // 150KB limit
        
        foreach ($wp_scripts->queue as $handle) {
            
            // Skip jQuery and other critical scripts
            if (in_array($handle, array('jquery', 'jquery-core', 'jquery-migrate'))) {
                continue;
            }
            
            if (!$this->is_theme_asset($handle, 'script') || in_array($handle, $this->settings['exclude_handles'])) {
                continue;
            }
            
            $script = $wp_scripts->registered[$handle];
            if (!$script || !$script->src) {
                continue;
            }
            
            $local_path = $this->url_to_path($script->src);
            if (!$local_path || !file_exists($local_path)) {
                continue;
            }
            
            $file_size = filesize($local_path);
            
            // Don't combine if it would exceed size limit
            if ($combined_size + $file_size > $max_combined_size) {
                break;
            }
            
            $theme_scripts[] = $handle;
            $content = file_get_contents($local_path);
            $combined_content .= "\n/* " . $handle . " */\n" . $content . ";\n";
            $combined_size += $file_size;
        }
        
        if (count($theme_scripts) > 1 && !empty($combined_content)) {
            
            // Generate cache filename
            $cache_key = md5($combined_content);
            $cache_file = $this->cache_dir . 'combined-' . $cache_key . '.js';
            $cache_url = $this->cache_url . 'combined-' . $cache_key . '.js';
            
            if (!file_exists($cache_file)) {
                $minified_content = $this->minify_js_content($combined_content);
                file_put_contents($cache_file, $minified_content);
            }
            
            // Remove individual scripts
            foreach ($theme_scripts as $handle) {
                wp_dequeue_script($handle);
            }
            
            // Enqueue combined script
            wp_enqueue_script('recruitpro-combined', $cache_url, array('jquery'), null, true);
        }
    }

    /**
     * Optimize CSS output
     * 
     * @since 1.0.0
     * @param string $tag Link tag
     * @param string $handle Style handle
     * @param string $href Style URL
     * @param string $media Media type
     * @return string Modified link tag
     */
    public function minify_css_output($tag, $handle, $href, $media) {
        
        // Add preload for critical CSS
        if ($this->is_critical_css($handle)) {
            $tag = str_replace('<link', '<link rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag);
            $tag .= '<noscript>' . $tag . '</noscript>';
        }
        
        // Add prefetch for non-critical CSS
        if ($this->settings['enable_preloading'] && !$this->is_critical_css($handle)) {
            $tag = str_replace('<link', '<link rel="prefetch"', $tag);
        }
        
        return $tag;
    }

    /**
     * Optimize JavaScript output
     * 
     * @since 1.0.0
     * @param string $tag Script tag
     * @param string $handle Script handle
     * @param string $src Script URL
     * @return string Modified script tag
     */
    public function optimize_js_output($tag, $handle, $src) {
        
        // Skip optimization for critical scripts
        if ($this->is_critical_js($handle)) {
            return $tag;
        }
        
        // Add defer attribute for non-critical scripts
        if ($this->settings['enable_deferred_loading'] && !$this->is_critical_js($handle)) {
            if (strpos($tag, 'defer') === false && strpos($tag, 'async') === false) {
                $tag = str_replace(' src', ' defer src', $tag);
            }
        }
        
        return $tag;
    }

    /**
     * Output critical CSS inline
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_critical_css() {
        
        if (!$this->settings['enable_critical_css']) {
            return;
        }
        
        $critical_css = $this->get_critical_css();
        
        if ($critical_css) {
            echo '<style id="recruitpro-critical-css">' . $critical_css . '</style>' . "\n";
        }
    }

    /**
     * Output deferred assets
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_deferred_assets() {
        
        if (!$this->settings['enable_deferred_loading']) {
            return;
        }
        
        // Output script to load deferred CSS
        ?>
        <script>
        (function() {
            var links = document.querySelectorAll('link[rel="prefetch"][as="style"]');
            for (var i = 0; i < links.length; i++) {
                var link = links[i];
                link.rel = 'stylesheet';
                link.removeAttribute('as');
            }
        })();
        </script>
        <?php
    }

    /**
     * Add minification customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_minification_customizer_options($wp_customize) {
        
        // Performance section
        $wp_customize->add_section('recruitpro_performance', array(
            'title' => __('Performance Optimization', 'recruitpro'),
            'description' => __('Configure asset minification and optimization settings.', 'recruitpro'),
            'priority' => 160,
            'capability' => 'edit_theme_options',
        ));

        // Enable CSS minification
        $wp_customize->add_setting('recruitpro_minify_css', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_minify_css', array(
            'label' => __('Minify CSS Files', 'recruitpro'),
            'description' => __('Minify CSS files to reduce file size and improve loading speed.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Enable JS minification
        $wp_customize->add_setting('recruitpro_minify_js', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_minify_js', array(
            'label' => __('Minify JavaScript Files', 'recruitpro'),
            'description' => __('Minify JavaScript files to reduce file size and improve loading speed.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Enable asset combining
        $wp_customize->add_setting('recruitpro_combine_assets', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_combine_assets', array(
            'label' => __('Combine Asset Files', 'recruitpro'),
            'description' => __('Combine multiple CSS and JS files to reduce HTTP requests.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Enable critical CSS
        $wp_customize->add_setting('recruitpro_critical_css', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_critical_css', array(
            'label' => __('Inline Critical CSS', 'recruitpro'),
            'description' => __('Inline critical CSS for faster initial page rendering.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Enable deferred loading
        $wp_customize->add_setting('recruitpro_defer_assets', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_defer_assets', array(
            'label' => __('Defer Non-Critical Assets', 'recruitpro'),
            'description' => __('Defer loading of non-critical CSS and JavaScript files.', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'checkbox',
        ));

        // Cache duration
        $wp_customize->add_setting('recruitpro_minification_cache_duration', array(
            'default' => DAY_IN_SECONDS,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_minification_cache_duration', array(
            'label' => __('Cache Duration (seconds)', 'recruitpro'),
            'description' => __('How long to cache minified files (86400 = 1 day).', 'recruitpro'),
            'section' => 'recruitpro_performance',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 3600,
                'max' => 604800,
                'step' => 3600,
            ),
        ));
    }

    /**
     * Clear minification cache
     * 
     * @since 1.0.0
     * @return void
     */
    public function clear_cache() {
        
        if (file_exists($this->cache_dir)) {
            $files = glob($this->cache_dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        
        // Clear transients
        delete_transient('recruitpro_critical_css');
        delete_transient('recruitpro_minification_stats');
        
        if (is_admin()) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                     __('Minification cache cleared successfully.', 'recruitpro') . '</p></div>';
            });
        }
    }

    /**
     * Get minification statistics
     * 
     * @since 1.0.0
     * @return void
     */
    public function get_minification_stats() {
        
        wp_send_json_success($this->metrics);
    }

    /**
     * Helper Functions
     */

    /**
     * Check if asset belongs to theme
     * 
     * @since 1.0.0
     * @param string $handle Asset handle
     * @param string $type Asset type (style|script)
     * @return bool
     */
    private function is_theme_asset($handle, $type) {
        
        global $wp_styles, $wp_scripts;
        
        $wp_assets = ($type === 'style') ? $wp_styles : $wp_scripts;
        
        if (!isset($wp_assets->registered[$handle])) {
            return false;
        }
        
        $asset = $wp_assets->registered[$handle];
        $theme_url = get_template_directory_uri();
        
        return $asset->src && strpos($asset->src, $theme_url) !== false;
    }

    /**
     * Convert URL to local file path
     * 
     * @since 1.0.0
     * @param string $url File URL
     * @return string|false Local file path or false if not local
     */
    private function url_to_path($url) {
        
        $theme_url = get_template_directory_uri();
        $theme_path = get_template_directory();
        
        if (strpos($url, $theme_url) === 0) {
            return str_replace($theme_url, $theme_path, $url);
        }
        
        return false;
    }

    /**
     * Check if CSS is critical
     * 
     * @since 1.0.0
     * @param string $handle CSS handle
     * @return bool
     */
    private function is_critical_css($handle) {
        
        $critical_handles = array(
            'recruitpro-style',
            'recruitpro-main-style',
            'recruitpro-critical',
        );
        
        return in_array($handle, $critical_handles);
    }

    /**
     * Check if JavaScript is critical
     * 
     * @since 1.0.0
     * @param string $handle JS handle
     * @return bool
     */
    private function is_critical_js($handle) {
        
        $critical_handles = array(
            'jquery',
            'jquery-core',
            'jquery-migrate',
            'recruitpro-main-js',
        );
        
        return in_array($handle, $critical_handles);
    }

    /**
     * Get critical CSS content
     * 
     * @since 1.0.0
     * @return string Critical CSS content
     */
    private function get_critical_css() {
        
        $critical_css = get_transient('recruitpro_critical_css');
        
        if ($critical_css === false) {
            
            // Define critical CSS based on page type
            $critical_css = '';
            
            if (is_front_page()) {
                $critical_css = $this->get_homepage_critical_css();
            } elseif (is_singular('job')) {
                $critical_css = $this->get_job_page_critical_css();
            } else {
                $critical_css = $this->get_default_critical_css();
            }
            
            set_transient('recruitpro_critical_css', $critical_css, $this->settings['cache_duration']);
        }
        
        return $critical_css;
    }

    /**
     * Get homepage critical CSS
     * 
     * @since 1.0.0
     * @return string Critical CSS for homepage
     */
    private function get_homepage_critical_css() {
        
        return '
        body{margin:0;font-family:Inter,sans-serif}
        .header{background:#fff;box-shadow:0 2px 4px rgba(0,0,0,.1)}
        .hero-section{background:#1e40af;color:#fff;padding:60px 0}
        .btn-primary{background:#1e40af;color:#fff;padding:12px 24px;border:none;border-radius:4px}
        .container{max-width:1200px;margin:0 auto;padding:0 20px}
        ';
    }

    /**
     * Get job page critical CSS
     * 
     * @since 1.0.0
     * @return string Critical CSS for job pages
     */
    private function get_job_page_critical_css() {
        
        return '
        body{margin:0;font-family:Inter,sans-serif}
        .header{background:#fff;box-shadow:0 2px 4px rgba(0,0,0,.1)}
        .job-header{background:#f8f9fa;padding:40px 0}
        .job-title{font-size:2rem;margin:0 0 10px 0}
        .job-meta{color:#6c757d;margin-bottom:20px}
        .apply-btn{background:#28a745;color:#fff;padding:12px 24px;border:none;border-radius:4px}
        ';
    }

    /**
     * Get default critical CSS
     * 
     * @since 1.0.0
     * @return string Default critical CSS
     */
    private function get_default_critical_css() {
        
        return '
        body{margin:0;font-family:Inter,sans-serif}
        .header{background:#fff;box-shadow:0 2px 4px rgba(0,0,0,.1)}
        .container{max-width:1200px;margin:0 auto;padding:0 20px}
        h1,h2,h3{margin-top:0;color:#333}
        .btn{padding:10px 20px;border:none;border-radius:4px;cursor:pointer}
        ';
    }

    /**
     * Update performance metrics
     * 
     * @since 1.0.0
     * @return void
     */
    private function update_performance_metrics() {
        
        $stored_metrics = get_option('recruitpro_minification_metrics', array());
        
        foreach ($this->metrics as $key => $value) {
            if (!isset($stored_metrics[$key])) {
                $stored_metrics[$key] = 0;
            }
            $stored_metrics[$key] += $value;
        }
        
        update_option('recruitpro_minification_metrics', $stored_metrics);
    }
}

// Initialize minification system
new RecruitPro_Asset_Minification();

/**
 * Helper Functions
 */

/**
 * Get minification statistics
 * 
 * @since 1.0.0
 * @return array Minification statistics
 */
function recruitpro_get_minification_stats() {
    
    return get_option('recruitpro_minification_metrics', array());
}

/**
 * Clear minification cache programmatically
 * 
 * @since 1.0.0
 * @return bool Success status
 */
function recruitpro_clear_minification_cache() {
    
    $minification = new RecruitPro_Asset_Minification();
    $minification->clear_cache();
    
    return true;
}

/**
 * Check if minification is active
 * 
 * @since 1.0.0
 * @return bool Minification status
 */
function recruitpro_is_minification_active() {
    
    return get_theme_mod('recruitpro_minify_css', true) || get_theme_mod('recruitpro_minify_js', true);
}

?>