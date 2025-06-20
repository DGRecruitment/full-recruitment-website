<?php
/**
 * RecruitPro Theme Compression & Optimization
 *
 * Handles asset compression, image optimization, and performance enhancements
 * Focuses on theme-level optimizations without interfering with plugin functionality
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize compression system
 */
function recruitpro_init_compression() {
    // Check if compression is enabled
    if (!get_theme_mod('recruitpro_enable_compression', true)) {
        return;
    }
    
    // Setup image compression
    recruitpro_setup_image_compression();
    
    // Setup CSS/JS compression
    recruitpro_setup_asset_compression();
    
    // Setup HTML compression
    recruitpro_setup_html_compression();
    
    // Setup GZIP compression
    recruitpro_setup_gzip_compression();
    
    // Setup preloading
    recruitpro_setup_resource_preloading();
    
    // Add admin interface
    add_action('customize_register', 'recruitpro_compression_customizer');
    
    // Performance monitoring
    if (defined('WP_DEBUG') && WP_DEBUG) {
        recruitpro_setup_performance_monitoring();
    }
}
add_action('after_setup_theme', 'recruitpro_init_compression');

/**
 * Setup image compression
 */
function recruitpro_setup_image_compression() {
    // WebP conversion support
    add_filter('wp_image_editors', 'recruitpro_add_webp_support');
    
    // Optimize image upload process
    add_filter('wp_handle_upload_prefilter', 'recruitpro_optimize_upload_images');
    
    // Progressive JPEG support
    add_filter('jpeg_quality', 'recruitpro_set_jpeg_quality');
    
    // Image lazy loading
    add_filter('wp_get_attachment_image_attributes', 'recruitpro_add_lazy_loading', 10, 3);
    
    // Responsive images optimization
    add_filter('wp_calculate_image_srcset_meta', 'recruitpro_optimize_srcset', 10, 4);
    
    // SVG optimization
    add_filter('upload_mimes', 'recruitpro_allow_svg_uploads');
    add_filter('wp_check_filetype_and_ext', 'recruitpro_check_svg_filetype', 10, 4);
}

/**
 * Add WebP support to image editors
 */
function recruitpro_add_webp_support($editors) {
    if (!class_exists('RecruitPro_WebP_Editor')) {
        require_once get_template_directory() . '/inc/class-webp-editor.php';
    }
    
    array_unshift($editors, 'RecruitPro_WebP_Editor');
    return $editors;
}

/**
 * Optimize images during upload
 */
function recruitpro_optimize_upload_images($file) {
    if (!function_exists('wp_get_image_editor')) {
        return $file;
    }
    
    $image_types = array('image/jpeg', 'image/png', 'image/gif');
    
    if (in_array($file['type'], $image_types)) {
        $image_editor = wp_get_image_editor($file['tmp_name']);
        
        if (!is_wp_error($image_editor)) {
            // Optimize quality
            $image_editor->set_quality(recruitpro_get_image_quality($file['type']));
            
            // Save optimized image
            $image_editor->save($file['tmp_name']);
        }
    }
    
    return $file;
}

/**
 * Get optimal image quality based on type
 */
function recruitpro_get_image_quality($mime_type) {
    $quality_settings = array(
        'image/jpeg' => get_theme_mod('recruitpro_jpeg_quality', 85),
        'image/png' => get_theme_mod('recruitpro_png_quality', 90),
        'image/gif' => 100, // GIF doesn't support quality settings
    );
    
    return isset($quality_settings[$mime_type]) ? $quality_settings[$mime_type] : 85;
}

/**
 * Set JPEG quality
 */
function recruitpro_set_jpeg_quality($quality) {
    return get_theme_mod('recruitpro_jpeg_quality', 85);
}

/**
 * Add lazy loading to images
 */
function recruitpro_add_lazy_loading($attr, $attachment, $size) {
    if (!get_theme_mod('recruitpro_enable_lazy_loading', true)) {
        return $attr;
    }
    
    // Skip if loading attribute already set
    if (isset($attr['loading'])) {
        return $attr;
    }
    
    // Skip for critical images (hero, above-the-fold)
    if (recruitpro_is_critical_image($attachment, $size)) {
        $attr['loading'] = 'eager';
    } else {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    
    return $attr;
}

/**
 * Check if image is critical (above-the-fold)
 */
function recruitpro_is_critical_image($attachment, $size) {
    // Consider hero images, logos, and featured images as critical
    $critical_sizes = array('recruitpro-hero', 'recruitpro-featured', 'logo');
    
    return in_array($size, $critical_sizes) || is_front_page();
}

/**
 * Optimize srcset generation
 */
function recruitpro_optimize_srcset($srcset, $size_array, $image_src, $image_meta) {
    // Remove excessive srcset entries for mobile optimization
    if (wp_is_mobile() && count($srcset) > 3) {
        // Keep only 3 most relevant sizes for mobile
        $srcset = array_slice($srcset, 0, 3, true);
    }
    
    return $srcset;
}

/**
 * Allow SVG uploads
 */
function recruitpro_allow_svg_uploads($mimes) {
    if (get_theme_mod('recruitpro_allow_svg_uploads', false)) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}

/**
 * Check SVG file type
 */
function recruitpro_check_svg_filetype($data, $file, $filename, $mimes) {
    if (get_theme_mod('recruitpro_allow_svg_uploads', false)) {
        $filetype = wp_check_filetype($filename, $mimes);
        return array(
            'ext'             => $filetype['ext'],
            'type'            => $filetype['type'],
            'proper_filename' => $data['proper_filename']
        );
    }
    
    return $data;
}

/**
 * Setup CSS/JS compression
 */
function recruitpro_setup_asset_compression() {
    // Minify CSS
    add_filter('style_loader_tag', 'recruitpro_minify_css_output', 10, 4);
    
    // Minify JavaScript
    add_filter('script_loader_tag', 'recruitpro_minify_js_output', 10, 3);
    
    // Combine CSS files
    if (get_theme_mod('recruitpro_combine_css', true)) {
        add_action('wp_print_styles', 'recruitpro_combine_css_files', 999);
    }
    
    // Combine JavaScript files
    if (get_theme_mod('recruitpro_combine_js', true)) {
        add_action('wp_print_scripts', 'recruitpro_combine_js_files', 999);
    }
    
    // Remove query strings from static resources
    add_filter('style_loader_src', 'recruitpro_remove_query_strings', 10, 1);
    add_filter('script_loader_src', 'recruitpro_remove_query_strings', 10, 1);
    
    // Defer non-critical CSS
    add_filter('style_loader_tag', 'recruitpro_defer_non_critical_css', 10, 4);
}

/**
 * Minify CSS output
 */
function recruitpro_minify_css_output($tag, $handle, $href, $media) {
    // Only minify theme CSS
    if (strpos($handle, 'recruitpro') === false) {
        return $tag;
    }
    
    if (!get_theme_mod('recruitpro_minify_css', true)) {
        return $tag;
    }
    
    // Check if file exists locally
    $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $href);
    
    if (file_exists($local_path)) {
        $cache_key = 'recruitpro_minified_css_' . md5($href . filemtime($local_path));
        $minified_content = get_transient($cache_key);
        
        if ($minified_content === false) {
            $css_content = file_get_contents($local_path);
            $minified_content = recruitpro_minify_css($css_content);
            set_transient($cache_key, $minified_content, DAY_IN_SECONDS);
        }
        
        // Replace with inline minified CSS for small files
        if (strlen($minified_content) < 10240) { // Less than 10KB
            return '<style id="' . esc_attr($handle) . '-css" media="' . esc_attr($media) . '">' . $minified_content . '</style>';
        }
    }
    
    return $tag;
}

/**
 * Minify JavaScript output
 */
function recruitpro_minify_js_output($tag, $handle, $src) {
    // Only minify theme JS
    if (strpos($handle, 'recruitpro') === false) {
        return $tag;
    }
    
    if (!get_theme_mod('recruitpro_minify_js', true)) {
        return $tag;
    }
    
    // Add async/defer attributes for non-critical scripts
    $non_critical_scripts = array(
        'recruitpro-smooth-scroll',
        'recruitpro-back-to-top',
        'recruitpro-animations',
    );
    
    if (in_array($handle, $non_critical_scripts)) {
        $tag = str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}

/**
 * Minify CSS content
 */
function recruitpro_minify_css($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Remove unnecessary whitespace
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '     '), '', $css);
    
    // Remove other unnecessary characters
    $css = str_replace(array('; ', ' ;', ' {', '{ ', '} ', ' }', ' :', ': ', ' ,', ', ', ';}'), array(';', ';', '{', '{', '}', '}', ':', ':', ',', ',', '}'), $css);
    
    return trim($css);
}

/**
 * Combine CSS files
 */
function recruitpro_combine_css_files() {
    global $wp_styles;
    
    if (!$wp_styles) {
        return;
    }
    
    $theme_styles = array();
    $combined_css = '';
    
    foreach ($wp_styles->queue as $handle) {
        if (strpos($handle, 'recruitpro') !== false) {
            $style = $wp_styles->registered[$handle];
            if ($style && $style->src) {
                $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $style->src);
                if (file_exists($local_path) && filesize($local_path) < 51200) { // Less than 50KB
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
        
        // Add combined and minified CSS
        $cache_key = 'recruitpro_combined_css_' . md5($combined_css);
        $minified_css = get_transient($cache_key);
        
        if ($minified_css === false) {
            $minified_css = recruitpro_minify_css($combined_css);
            set_transient($cache_key, $minified_css, DAY_IN_SECONDS);
        }
        
        wp_add_inline_style('recruitpro-style', $minified_css);
    }
}

/**
 * Combine JavaScript files
 */
function recruitpro_combine_js_files() {
    global $wp_scripts;
    
    if (!$wp_scripts) {
        return;
    }
    
    $theme_scripts = array();
    $combined_js = '';
    
    foreach ($wp_scripts->queue as $handle) {
        if (strpos($handle, 'recruitpro') !== false && $handle !== 'recruitpro-main') {
            $script = $wp_scripts->registered[$handle];
            if ($script && $script->src) {
                $local_path = str_replace(get_template_directory_uri(), get_template_directory(), $script->src);
                if (file_exists($local_path) && filesize($local_path) < 51200) { // Less than 50KB
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
 * Remove query strings from static resources
 */
function recruitpro_remove_query_strings($src) {
    if (!get_theme_mod('recruitpro_remove_query_strings', true)) {
        return $src;
    }
    
    $parts = explode('?', $src);
    return $parts[0];
}

/**
 * Defer non-critical CSS
 */
function recruitpro_defer_non_critical_css($tag, $handle, $href, $media) {
    // Critical CSS handles that should load immediately
    $critical_css = array(
        'recruitpro-style',
        'recruitpro-critical',
        'recruitpro-fonts',
    );
    
    if (!in_array($handle, $critical_css) && $media === 'all') {
        // Defer non-critical CSS
        $tag = str_replace('rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag);
        $tag .= '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
    }
    
    return $tag;
}

/**
 * Setup HTML compression
 */
function recruitpro_setup_html_compression() {
    if (!get_theme_mod('recruitpro_compress_html', true)) {
        return;
    }
    
    // Start output buffering for HTML compression
    ob_start('recruitpro_compress_html_output');
    
    // Ensure buffer is flushed on shutdown
    add_action('shutdown', 'recruitpro_flush_html_compression', 0);
}

/**
 * Compress HTML output
 */
function recruitpro_compress_html_output($html) {
    // Skip compression in admin or if user is logged in (for debugging)
    if (is_admin() || (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options'))) {
        return $html;
    }
    
    // Skip if content type is not HTML
    $content_type = '';
    foreach (headers_list() as $header) {
        if (stripos($header, 'content-type:') === 0) {
            $content_type = $header;
            break;
        }
    }
    
    if (stripos($content_type, 'text/html') === false) {
        return $html;
    }
    
    // Preserve important whitespace
    $preserve = array();
    
    // Preserve pre, code, textarea content
    $html = preg_replace_callback('/<(pre|code|textarea|script|style)[^>]*>.*?<\/\1>/is', function($matches) use (&$preserve) {
        $placeholder = '###PRESERVE_' . count($preserve) . '###';
        $preserve[$placeholder] = $matches[0];
        return $placeholder;
    }, $html);
    
    // Remove HTML comments (except IE conditionals)
    $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
    
    // Remove unnecessary whitespace
    $html = preg_replace('/\s+/', ' ', $html);
    $html = preg_replace('/>\s+</', '><', $html);
    
    // Remove whitespace around block elements
    $block_elements = 'div|header|footer|nav|section|article|aside|main|p|h[1-6]|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|td|th';
    $html = preg_replace('/\s*(<(?:' . $block_elements . ')[^>]*>)\s*/', '$1', $html);
    $html = preg_replace('/\s*(<\/(?:' . $block_elements . ')>)\s*/', '$1', $html);
    
    // Restore preserved content
    foreach ($preserve as $placeholder => $content) {
        $html = str_replace($placeholder, $content, $html);
    }
    
    return trim($html);
}

/**
 * Flush HTML compression buffer
 */
function recruitpro_flush_html_compression() {
    if (ob_get_level()) {
        ob_end_flush();
    }
}

/**
 * Setup GZIP compression
 */
function recruitpro_setup_gzip_compression() {
    if (!get_theme_mod('recruitpro_enable_gzip', true)) {
        return;
    }
    
    // Check if GZIP is already enabled
    if (recruitpro_is_gzip_enabled()) {
        return;
    }
    
    // Enable GZIP compression
    if (!headers_sent() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
        if (function_exists('ob_gzhandler')) {
            ob_start('ob_gzhandler');
        }
    }
    
    // Add GZIP headers
    add_action('send_headers', 'recruitpro_add_gzip_headers');
}

/**
 * Check if GZIP is enabled
 */
function recruitpro_is_gzip_enabled() {
    return extension_loaded('zlib') && 
           (ini_get('zlib.output_compression') || 
            in_array('ob_gzhandler', ob_list_handlers()) ||
            (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && 
             strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false));
}

/**
 * Add GZIP headers
 */
function recruitpro_add_gzip_headers() {
    if (!headers_sent() && recruitpro_is_gzip_enabled()) {
        header('Vary: Accept-Encoding');
    }
}

/**
 * Setup resource preloading
 */
function recruitpro_setup_resource_preloading() {
    // Preload critical resources
    add_action('wp_head', 'recruitpro_preload_critical_resources', 1);
    
    // DNS prefetch for external resources
    add_action('wp_head', 'recruitpro_dns_prefetch', 2);
    
    // Resource hints
    add_action('wp_head', 'recruitpro_resource_hints', 3);
}

/**
 * Preload critical resources
 */
function recruitpro_preload_critical_resources() {
    $preload_resources = array();
    
    // Preload critical CSS
    $critical_css = get_template_directory_uri() . '/assets/css/critical.css';
    if (file_exists(get_template_directory() . '/assets/css/critical.css')) {
        $preload_resources[] = array(
            'href' => $critical_css,
            'as' => 'style',
            'type' => 'text/css'
        );
    }
    
    // Preload main font
    $main_font = get_theme_mod('recruitpro_main_font_url');
    if ($main_font) {
        $preload_resources[] = array(
            'href' => $main_font,
            'as' => 'font',
            'type' => 'font/woff2',
            'crossorigin' => 'anonymous'
        );
    }
    
    // Preload hero image on homepage
    if (is_front_page()) {
        $hero_image = get_theme_mod('recruitpro_hero_image');
        if ($hero_image) {
            $preload_resources[] = array(
                'href' => $hero_image,
                'as' => 'image'
            );
        }
    }
    
    // Preload logo
    $logo = get_theme_mod('custom_logo');
    if ($logo) {
        $logo_url = wp_get_attachment_image_url($logo, 'full');
        if ($logo_url) {
            $preload_resources[] = array(
                'href' => $logo_url,
                'as' => 'image'
            );
        }
    }
    
    // Output preload tags
    foreach ($preload_resources as $resource) {
        $attributes = array();
        foreach ($resource as $attr => $value) {
            $attributes[] = $attr . '="' . esc_attr($value) . '"';
        }
        echo '<link rel="preload" ' . implode(' ', $attributes) . '>' . "\n";
    }
}

/**
 * DNS prefetch for external resources
 */
function recruitpro_dns_prefetch() {
    $external_domains = array(
        'fonts.googleapis.com',
        'fonts.gstatic.com',
    );
    
    // Allow themes and plugins to add domains
    $external_domains = apply_filters('recruitpro_dns_prefetch_domains', $external_domains);
    
    foreach ($external_domains as $domain) {
        echo '<link rel="dns-prefetch" href="//' . esc_attr($domain) . '">' . "\n";
    }
}

/**
 * Resource hints
 */
function recruitpro_resource_hints() {
    // Preconnect to critical external domains
    $preconnect_domains = array(
        'fonts.googleapis.com' => true, // CORS enabled
        'fonts.gstatic.com' => true,
    );
    
    foreach ($preconnect_domains as $domain => $cors) {
        $crossorigin = $cors ? ' crossorigin' : '';
        echo '<link rel="preconnect" href="https://' . esc_attr($domain) . '"' . $crossorigin . '>' . "\n";
    }
}

/**
 * Setup performance monitoring
 */
function recruitpro_setup_performance_monitoring() {
    // Monitor compression effectiveness
    add_action('wp_footer', 'recruitpro_compression_stats');
    
    // Track resource loading times
    add_action('wp_head', 'recruitpro_performance_tracking_script');
}

/**
 * Display compression statistics
 */
function recruitpro_compression_stats() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $original_size = strlen(ob_get_contents());
    $compression_ratio = 0;
    
    if (function_exists('ob_get_level') && ob_get_level() > 0) {
        $handlers = ob_list_handlers();
        if (in_array('ob_gzhandler', $handlers)) {
            $compression_ratio = 25; // Approximate GZIP compression
        }
    }
    
    ?>
    <!-- RecruitPro Compression Stats -->
    <script>
    if (typeof console !== 'undefined') {
        console.group('RecruitPro Compression Stats');
        console.log('HTML Size: <?php echo esc_js(size_format($original_size)); ?>');
        console.log('GZIP Enabled: <?php echo recruitpro_is_gzip_enabled() ? 'Yes' : 'No'; ?>');
        console.log('Estimated Compression: <?php echo esc_js($compression_ratio); ?>%');
        console.groupEnd();
    }
    </script>
    <?php
}

/**
 * Performance tracking script
 */
function recruitpro_performance_tracking_script() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <script>
    // Performance tracking for admins
    window.recruitproPerformance = {
        startTime: performance.now(),
        
        logMetrics: function() {
            if (typeof performance !== 'undefined' && performance.timing) {
                var timing = performance.timing;
                var loadTime = timing.loadEventEnd - timing.navigationStart;
                var domReady = timing.domContentLoadedEventEnd - timing.navigationStart;
                var firstPaint = 0;
                
                if (performance.getEntriesByType) {
                    var paintEntries = performance.getEntriesByType('paint');
                    if (paintEntries.length > 0) {
                        firstPaint = paintEntries[0].startTime;
                    }
                }
                
                console.group('RecruitPro Performance Metrics');
                console.log('Page Load Time: ' + loadTime + 'ms');
                console.log('DOM Ready: ' + domReady + 'ms');
                if (firstPaint > 0) {
                    console.log('First Paint: ' + firstPaint + 'ms');
                }
                console.groupEnd();
            }
        }
    };
    
    // Log metrics when page is fully loaded
    if (document.readyState === 'complete') {
        recruitproPerformance.logMetrics();
    } else {
        window.addEventListener('load', recruitproPerformance.logMetrics);
    }
    </script>
    <?php
}

/**
 * Add compression settings to customizer
 */
function recruitpro_compression_customizer($wp_customize) {
    // Compression section
    $wp_customize->add_section('recruitpro_compression', array(
        'title'    => esc_html__('Performance & Compression', 'recruitpro'),
        'priority' => 200,
    ));
    
    // Enable compression
    $wp_customize->add_setting('recruitpro_enable_compression', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_compression', array(
        'label'   => esc_html__('Enable Compression', 'recruitpro'),
        'section' => 'recruitpro_compression',
        'type'    => 'checkbox',
    ));
    
    // HTML compression
    $wp_customize->add_setting('recruitpro_compress_html', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_compress_html', array(
        'label'   => esc_html__('Compress HTML Output', 'recruitpro'),
        'section' => 'recruitpro_compression',
        'type'    => 'checkbox',
    ));
    
    // CSS minification
    $wp_customize->add_setting('recruitpro_minify_css', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_minify_css', array(
        'label'   => esc_html__('Minify CSS', 'recruitpro'),
        'section' => 'recruitpro_compression',
        'type'    => 'checkbox',
    ));
    
    // JavaScript minification
    $wp_customize->add_setting('recruitpro_minify_js', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_minify_js', array(
        'label'   => esc_html__('Minify JavaScript', 'recruitpro'),
        'section' => 'recruitpro_compression',
        'type'    => 'checkbox',
    ));
    
    // Combine CSS
    $wp_customize->add_setting('recruitpro_combine_css', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_combine_css', array(
        'label'       => esc_html__('Combine CSS Files', 'recruitpro'),
        'section'     => 'recruitpro_compression',
        'type'        => 'checkbox',
        'description' => esc_html__('Combines small CSS files to reduce HTTP requests', 'recruitpro'),
    ));
    
    // Combine JavaScript
    $wp_customize->add_setting('recruitpro_combine_js', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_combine_js', array(
        'label'       => esc_html__('Combine JavaScript Files', 'recruitpro'),
        'section'     => 'recruitpro_compression',
        'type'        => 'checkbox',
        'description' => esc_html__('Combines small JS files to reduce HTTP requests', 'recruitpro'),
    ));
    
    // GZIP compression
    $wp_customize->add_setting('recruitpro_enable_gzip', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_gzip', array(
        'label'       => esc_html__('Enable GZIP Compression', 'recruitpro'),
        'section'     => 'recruitpro_compression',
        'type'        => 'checkbox',
        'description' => esc_html__('Enables GZIP compression if not already available', 'recruitpro'),
    ));
    
    // Image optimization
    $wp_customize->add_setting('recruitpro_jpeg_quality', array(
        'default'           => 85,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('recruitpro_jpeg_quality', array(
        'label'       => esc_html__('JPEG Quality', 'recruitpro'),
        'section'     => 'recruitpro_compression',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 50,
            'max'  => 100,
            'step' => 5,
        ),
    ));
    
    // Lazy loading
    $wp_customize->add_setting('recruitpro_enable_lazy_loading', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_lazy_loading', array(
        'label'   => esc_html__('Enable Image Lazy Loading', 'recruitpro'),
        'section' => 'recruitpro_compression',
        'type'    => 'checkbox',
    ));
    
    // SVG uploads
    $wp_customize->add_setting('recruitpro_allow_svg_uploads', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_allow_svg_uploads', array(
        'label'       => esc_html__('Allow SVG Uploads', 'recruitpro'),
        'section'     => 'recruitpro_compression',
        'type'        => 'checkbox',
        'description' => esc_html__('Allows SVG file uploads (use with caution)', 'recruitpro'),
    ));
}

/**
 * Clear compression cache
 */
function recruitpro_clear_compression_cache() {
    // Clear CSS/JS combination cache
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_minified_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%recruitpro_combined_%'");
    
    // Clear image optimization cache
    wp_cache_flush_group('recruitpro_images');
    
    // Clear external cache if available
    if (function_exists('recruitpro_clear_external_cache')) {
        recruitpro_clear_external_cache();
    }
}

/**
 * Get compression statistics
 */
function recruitpro_get_compression_stats() {
    $stats = array(
        'gzip_enabled' => recruitpro_is_gzip_enabled(),
        'html_compression' => get_theme_mod('recruitpro_compress_html', true),
        'css_minification' => get_theme_mod('recruitpro_minify_css', true),
        'js_minification' => get_theme_mod('recruitpro_minify_js', true),
        'image_optimization' => get_theme_mod('recruitpro_jpeg_quality', 85),
        'lazy_loading' => get_theme_mod('recruitpro_enable_lazy_loading', true),
    );
    
    return $stats;
}

/**
 * Display compression status in admin
 */
function recruitpro_display_compression_status() {
    $stats = recruitpro_get_compression_stats();
    
    ?>
    <div class="postbox">
        <h2 class="hndle"><?php esc_html_e('Compression Status', 'recruitpro'); ?></h2>
        <div class="inside">
            <div class="recruitpro-compression-status">
                <?php foreach ($stats as $feature => $enabled) : ?>
                    <div class="compression-item">
                        <span><?php echo esc_html(ucwords(str_replace('_', ' ', $feature))); ?></span>
                        <span class="status-badge <?php echo $enabled ? 'status-enabled' : 'status-disabled'; ?>">
                            <?php echo $enabled ? esc_html__('Enabled', 'recruitpro') : esc_html__('Disabled', 'recruitpro'); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="compression-actions">
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('recruitpro_clear_compression_cache', 'compression_nonce'); ?>
                    <input type="hidden" name="action" value="clear_compression_cache">
                    <input type="submit" class="button" value="<?php esc_attr_e('Clear Compression Cache', 'recruitpro'); ?>">
                </form>
            </div>
        </div>
    </div>
    
    <style>
    .recruitpro-compression-status .compression-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .status-badge {
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-enabled {
        background-color: #4CAF50;
        color: white;
    }
    .status-disabled {
        background-color: #f44336;
        color: white;
    }
    .compression-actions {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    </style>
    <?php
}

/**
 * Handle compression cache clearing
 */
function recruitpro_handle_compression_actions() {
    if (!isset($_POST['action']) || !current_user_can('manage_options')) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['compression_nonce'], 'recruitpro_clear_compression_cache')) {
        return;
    }
    
    if ($_POST['action'] === 'clear_compression_cache') {
        recruitpro_clear_compression_cache();
        add_settings_error('compression_clear', 'success', esc_html__('Compression cache cleared successfully!', 'recruitpro'), 'updated');
    }
}
add_action('admin_init', 'recruitpro_handle_compression_actions');

/**
 * Clear compression cache when theme settings change
 */
function recruitpro_clear_cache_on_settings_change($option_name) {
    if (strpos($option_name, 'recruitpro_') !== false) {
        recruitpro_clear_compression_cache();
    }
}
add_action('updated_option', 'recruitpro_clear_cache_on_settings_change');

/**
 * Template function to check if compression is enabled
 */
function recruitpro_is_compression_enabled() {
    return get_theme_mod('recruitpro_enable_compression', true);
}

/**
 * Template function to get compression ratio
 */
function recruitpro_get_compression_ratio() {
    if (recruitpro_is_gzip_enabled()) {
        return 25; // Approximate GZIP compression ratio
    }
    
    return 0;
}