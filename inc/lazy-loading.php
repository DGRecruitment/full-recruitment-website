<?php
/**
 * RecruitPro Theme Lazy Loading System
 *
 * This file handles advanced lazy loading functionality for the RecruitPro
 * recruitment website theme. It includes image lazy loading, content lazy
 * loading, progressive enhancement, performance optimization, and professional
 * loading states specifically designed for recruitment agency websites.
 *
 * @package RecruitPro
 * @subpackage Theme/Performance
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/lazy-loading.php
 * Purpose: Advanced lazy loading and performance optimization
 * Dependencies: WordPress core, Intersection Observer API
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Lazy Loading Class
 * 
 * Handles all lazy loading functionality including images, content,
 * progressive loading, and performance optimization.
 *
 * @since 1.0.0
 */
class RecruitPro_Lazy_Loading {

    /**
     * Lazy loading strategies
     * 
     * @since 1.0.0
     * @var array
     */
    private $loading_strategies = array();

    /**
     * Content types for lazy loading
     * 
     * @since 1.0.0
     * @var array
     */
    private $content_types = array();

    /**
     * Performance thresholds
     * 
     * @since 1.0.0
     * @var array
     */
    private $performance_thresholds = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_loading_strategies();
        $this->init_content_types();
        $this->init_performance_thresholds();
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_lazy_loading_assets'));
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_filter('the_content', array($this, 'process_content_lazy_loading'));
        add_filter('post_thumbnail_html', array($this, 'process_thumbnail_lazy_loading'), 10, 5);
        add_filter('wp_get_attachment_image', array($this, 'process_attachment_lazy_loading'), 10, 5);
        add_action('wp_head', array($this, 'output_lazy_loading_styles'), 20);
        add_action('wp_footer', array($this, 'output_lazy_loading_script'));
        add_filter('body_class', array($this, 'add_lazy_loading_body_classes'));
        add_action('wp_ajax_recruitpro_lazy_content', array($this, 'ajax_load_content'));
        add_action('wp_ajax_nopriv_recruitpro_lazy_content', array($this, 'ajax_load_content'));
    }

    /**
     * Initialize loading strategies
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_loading_strategies() {
        
        $this->loading_strategies = array(
            'intersection_observer' => array(
                'name' => __('Intersection Observer (Modern)', 'recruitpro'),
                'description' => __('Uses modern Intersection Observer API for efficient lazy loading', 'recruitpro'),
                'browser_support' => '95%',
                'performance' => 'excellent',
                'fallback' => 'scroll_listener',
            ),
            'scroll_listener' => array(
                'name' => __('Scroll Listener (Compatible)', 'recruitpro'),
                'description' => __('Traditional scroll-based lazy loading with debouncing', 'recruitpro'),
                'browser_support' => '100%',
                'performance' => 'good',
                'fallback' => 'immediate',
            ),
            'viewport_detection' => array(
                'name' => __('Viewport Detection (Hybrid)', 'recruitpro'),
                'description' => __('Combines multiple detection methods for reliability', 'recruitpro'),
                'browser_support' => '100%',
                'performance' => 'very good',
                'fallback' => 'scroll_listener',
            ),
            'progressive' => array(
                'name' => __('Progressive Loading', 'recruitpro'),
                'description' => __('Loads content in priority order based on importance', 'recruitpro'),
                'browser_support' => '100%',
                'performance' => 'excellent',
                'fallback' => 'immediate',
            ),
            'immediate' => array(
                'name' => __('Immediate Loading (Disabled)', 'recruitpro'),
                'description' => __('Loads all content immediately (no lazy loading)', 'recruitpro'),
                'browser_support' => '100%',
                'performance' => 'poor',
                'fallback' => null,
            ),
        );
    }

    /**
     * Initialize content types for lazy loading
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_content_types() {
        
        $this->content_types = array(
            // Image types
            'hero_images' => array(
                'name' => __('Hero Images', 'recruitpro'),
                'description' => __('Large banner and hero section images', 'recruitpro'),
                'priority' => 'high',
                'threshold' => '50px',
                'fade_duration' => '600ms',
                'placeholder' => 'gradient',
                'preload_count' => 1,
            ),
            'team_photos' => array(
                'name' => __('Team Photos', 'recruitpro'),
                'description' => __('Staff and recruitment team member photos', 'recruitpro'),
                'priority' => 'medium',
                'threshold' => '100px',
                'fade_duration' => '400ms',
                'placeholder' => 'blur',
                'preload_count' => 3,
            ),
            'client_logos' => array(
                'name' => __('Client Logos', 'recruitpro'),
                'description' => __('Company logos and client branding', 'recruitpro'),
                'priority' => 'medium',
                'threshold' => '150px',
                'fade_duration' => '300ms',
                'placeholder' => 'solid',
                'preload_count' => 6,
            ),
            'content_images' => array(
                'name' => __('Content Images', 'recruitpro'),
                'description' => __('Blog posts and article images', 'recruitpro'),
                'priority' => 'low',
                'threshold' => '200px',
                'fade_duration' => '500ms',
                'placeholder' => 'skeleton',
                'preload_count' => 2,
            ),
            'service_images' => array(
                'name' => __('Service Images', 'recruitpro'),
                'description' => __('Recruitment service and feature images', 'recruitpro'),
                'priority' => 'medium',
                'threshold' => '100px',
                'fade_duration' => '400ms',
                'placeholder' => 'gradient',
                'preload_count' => 4,
            ),
            'gallery_images' => array(
                'name' => __('Gallery Images', 'recruitpro'),
                'description' => __('Office photos and gallery content', 'recruitpro'),
                'priority' => 'low',
                'threshold' => '300px',
                'fade_duration' => '600ms',
                'placeholder' => 'blur',
                'preload_count' => 0,
            ),
            
            // Content types
            'testimonials' => array(
                'name' => __('Testimonials', 'recruitpro'),
                'description' => __('Client testimonials and reviews', 'recruitpro'),
                'priority' => 'medium',
                'threshold' => '150px',
                'fade_duration' => '500ms',
                'placeholder' => 'skeleton',
                'ajax_load' => true,
            ),
            'job_listings' => array(
                'name' => __('Job Listings', 'recruitpro'),
                'description' => __('Job postings and career opportunities', 'recruitpro'),
                'priority' => 'high',
                'threshold' => '100px',
                'fade_duration' => '400ms',
                'placeholder' => 'skeleton',
                'ajax_load' => true,
            ),
            'case_studies' => array(
                'name' => __('Case Studies', 'recruitpro'),
                'description' => __('Recruitment success stories and case studies', 'recruitpro'),
                'priority' => 'medium',
                'threshold' => '200px',
                'fade_duration' => '500ms',
                'placeholder' => 'skeleton',
                'ajax_load' => true,
            ),
            'blog_content' => array(
                'name' => __('Blog Content', 'recruitpro'),
                'description' => __('Career advice and blog articles', 'recruitpro'),
                'priority' => 'low',
                'threshold' => '250px',
                'fade_duration' => '600ms',
                'placeholder' => 'skeleton',
                'ajax_load' => false,
            ),
            
            // Interactive elements
            'contact_forms' => array(
                'name' => __('Contact Forms', 'recruitpro'),
                'description' => __('Contact and application forms', 'recruitpro'),
                'priority' => 'high',
                'threshold' => '50px',
                'fade_duration' => '300ms',
                'placeholder' => 'skeleton',
                'ajax_load' => false,
            ),
            'social_feeds' => array(
                'name' => __('Social Media Feeds', 'recruitpro'),
                'description' => __('LinkedIn and social media integrations', 'recruitpro'),
                'priority' => 'low',
                'threshold' => '400px',
                'fade_duration' => '500ms',
                'placeholder' => 'skeleton',
                'ajax_load' => true,
            ),
            'maps' => array(
                'name' => __('Maps', 'recruitpro'),
                'description' => __('Office location maps and directions', 'recruitpro'),
                'priority' => 'low',
                'threshold' => '300px',
                'fade_duration' => '400ms',
                'placeholder' => 'skeleton',
                'ajax_load' => true,
            ),
        );
    }

    /**
     * Initialize performance thresholds
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_performance_thresholds() {
        
        $this->performance_thresholds = array(
            'fast_connection' => array(
                'name' => __('Fast Connection', 'recruitpro'),
                'conditions' => array('4g', 'ethernet', 'wifi'),
                'preload_images' => 6,
                'preload_content' => 3,
                'lazy_threshold' => '100px',
                'aggressive_loading' => true,
            ),
            'medium_connection' => array(
                'name' => __('Medium Connection', 'recruitpro'),
                'conditions' => array('3g', 'slow-2g'),
                'preload_images' => 3,
                'preload_content' => 2,
                'lazy_threshold' => '200px',
                'aggressive_loading' => false,
            ),
            'slow_connection' => array(
                'name' => __('Slow Connection', 'recruitpro'),
                'conditions' => array('2g', 'slow-2g'),
                'preload_images' => 1,
                'preload_content' => 1,
                'lazy_threshold' => '400px',
                'aggressive_loading' => false,
            ),
            'save_data' => array(
                'name' => __('Data Saver Mode', 'recruitpro'),
                'conditions' => array('save-data'),
                'preload_images' => 0,
                'preload_content' => 0,
                'lazy_threshold' => '600px',
                'aggressive_loading' => false,
            ),
        );
    }

    /**
     * Add lazy loading customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {

        // =================================================================
        // LAZY LOADING SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_lazy_loading', array(
            'title' => __('Lazy Loading', 'recruitpro'),
            'description' => __('Configure lazy loading settings for optimal performance on your recruitment website.', 'recruitpro'),
            'priority' => 140,
            'capability' => 'edit_theme_options',
        ));

        // Enable lazy loading
        $wp_customize->add_setting('recruitpro_enable_lazy_loading', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_lazy_loading', array(
            'label' => __('Enable Lazy Loading', 'recruitpro'),
            'description' => __('Improve page load performance by loading content as needed.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'checkbox',
        ));

        // Loading strategy
        $wp_customize->add_setting('recruitpro_lazy_strategy', array(
            'default' => 'intersection_observer',
            'sanitize_callback' => array($this, 'sanitize_strategy'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_strategy', array(
            'label' => __('Loading Strategy', 'recruitpro'),
            'description' => __('Choose the lazy loading detection method.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'select',
            'choices' => $this->get_strategy_choices(),
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Lazy threshold
        $wp_customize->add_setting('recruitpro_lazy_threshold', array(
            'default' => 150,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_threshold', array(
            'label' => __('Loading Threshold (pixels)', 'recruitpro'),
            'description' => __('Distance from viewport when loading should begin.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 1000,
                'step' => 50,
            ),
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Fade animation
        $wp_customize->add_setting('recruitpro_lazy_fade_animation', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_fade_animation', array(
            'label' => __('Enable Fade Animation', 'recruitpro'),
            'description' => __('Smooth fade-in effect when content loads.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'checkbox',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Animation duration
        $wp_customize->add_setting('recruitpro_lazy_fade_duration', array(
            'default' => 400,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_fade_duration', array(
            'label' => __('Fade Duration (milliseconds)', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 100,
                'max' => 1000,
                'step' => 100,
            ),
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true) && 
                       get_theme_mod('recruitpro_lazy_fade_animation', true);
            },
        ));

        // Placeholder style
        $wp_customize->add_setting('recruitpro_lazy_placeholder_style', array(
            'default' => 'skeleton',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_placeholder_style', array(
            'label' => __('Placeholder Style', 'recruitpro'),
            'description' => __('Visual placeholder while content loads.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'select',
            'choices' => array(
                'skeleton' => __('Skeleton Screen (Professional)', 'recruitpro'),
                'blur' => __('Blur Effect', 'recruitpro'),
                'gradient' => __('Gradient Animation', 'recruitpro'),
                'solid' => __('Solid Color', 'recruitpro'),
                'none' => __('No Placeholder', 'recruitpro'),
            ),
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Preload count
        $wp_customize->add_setting('recruitpro_lazy_preload_count', array(
            'default' => 3,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_preload_count', array(
            'label' => __('Preload Count', 'recruitpro'),
            'description' => __('Number of images to load immediately (above fold).', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 0,
                'max' => 10,
                'step' => 1,
            ),
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Adaptive loading
        $wp_customize->add_setting('recruitpro_lazy_adaptive_loading', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_adaptive_loading', array(
            'label' => __('Enable Adaptive Loading', 'recruitpro'),
            'description' => __('Adjust loading based on connection speed and data preferences.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'checkbox',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Mobile optimization
        $wp_customize->add_setting('recruitpro_lazy_mobile_optimization', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_mobile_optimization', array(
            'label' => __('Mobile Optimization', 'recruitpro'),
            'description' => __('Enhanced lazy loading settings for mobile devices.', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'checkbox',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));

        // Performance monitoring
        $wp_customize->add_setting('recruitpro_lazy_performance_monitoring', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_performance_monitoring', array(
            'label' => __('Enable Performance Monitoring', 'recruitpro'),
            'description' => __('Track lazy loading performance metrics (for development).', 'recruitpro'),
            'section' => 'recruitpro_lazy_loading',
            'type' => 'checkbox',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_lazy_loading', true);
            },
        ));
    }

    /**
     * Enqueue lazy loading assets
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_lazy_loading_assets() {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            return;
        }

        $theme_version = wp_get_theme()->get('Version');
        $strategy = get_theme_mod('recruitpro_lazy_strategy', 'intersection_observer');
        
        // Enqueue appropriate script based on strategy
        switch ($strategy) {
            case 'intersection_observer':
                wp_enqueue_script(
                    'recruitpro-lazy-loading-io',
                    get_template_directory_uri() . '/assets/js/lazy-loading-intersection-observer.js',
                    array(),
                    $theme_version,
                    true
                );
                break;
                
            case 'scroll_listener':
                wp_enqueue_script(
                    'recruitpro-lazy-loading-scroll',
                    get_template_directory_uri() . '/assets/js/lazy-loading-scroll.js',
                    array(),
                    $theme_version,
                    true
                );
                break;
                
            case 'viewport_detection':
                wp_enqueue_script(
                    'recruitpro-lazy-loading-viewport',
                    get_template_directory_uri() . '/assets/js/lazy-loading-viewport.js',
                    array(),
                    $theme_version,
                    true
                );
                break;
                
            case 'progressive':
                wp_enqueue_script(
                    'recruitpro-lazy-loading-progressive',
                    get_template_directory_uri() . '/assets/js/lazy-loading-progressive.js',
                    array(),
                    $theme_version,
                    true
                );
                break;
        }
        
        // Localize script with settings
        $script_handle = 'recruitpro-lazy-loading-' . str_replace('_', '-', $strategy);
        
        wp_localize_script($script_handle, 'recruitpro_lazy_loading', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_lazy_loading_nonce'),
            'strategy' => $strategy,
            'threshold' => get_theme_mod('recruitpro_lazy_threshold', 150),
            'fade_animation' => get_theme_mod('recruitpro_lazy_fade_animation', true),
            'fade_duration' => get_theme_mod('recruitpro_lazy_fade_duration', 400),
            'placeholder_style' => get_theme_mod('recruitpro_lazy_placeholder_style', 'skeleton'),
            'preload_count' => get_theme_mod('recruitpro_lazy_preload_count', 3),
            'adaptive_loading' => get_theme_mod('recruitpro_lazy_adaptive_loading', true),
            'mobile_optimization' => get_theme_mod('recruitpro_lazy_mobile_optimization', true),
            'performance_monitoring' => get_theme_mod('recruitpro_lazy_performance_monitoring', false),
            'is_mobile' => wp_is_mobile(),
            'connection_type' => $this->get_connection_type(),
            'content_types' => $this->content_types,
            'strings' => array(
                'loading' => __('Loading...', 'recruitpro'),
                'load_more' => __('Load More', 'recruitpro'),
                'loading_error' => __('Error loading content. Please try again.', 'recruitpro'),
                'retry' => __('Retry', 'recruitpro'),
            ),
        ));
    }

    /**
     * Process content for lazy loading
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Modified content
     */
    public function process_content_lazy_loading($content) {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true) || is_admin() || is_feed()) {
            return $content;
        }

        // Process images in content
        $content = preg_replace_callback(
            '/<img([^>]+)>/i',
            array($this, 'process_image_tag'),
            $content
        );

        // Process lazy content blocks
        $content = $this->process_lazy_content_blocks($content);

        return $content;
    }

    /**
     * Process thumbnail for lazy loading
     * 
     * @since 1.0.0
     * @param string $html Thumbnail HTML
     * @param int $post_id Post ID
     * @param int $post_thumbnail_id Thumbnail ID
     * @param string $size Image size
     * @param array $attr Image attributes
     * @return string Modified HTML
     */
    public function process_thumbnail_lazy_loading($html, $post_id, $post_thumbnail_id, $size, $attr) {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true) || empty($html)) {
            return $html;
        }

        return preg_replace_callback(
            '/<img([^>]+)>/i',
            array($this, 'process_image_tag'),
            $html
        );
    }

    /**
     * Process attachment image for lazy loading
     * 
     * @since 1.0.0
     * @param string $html Image HTML
     * @param int $attachment_id Attachment ID
     * @param string $size Image size
     * @param bool $icon Whether image is an icon
     * @param array $attr Image attributes
     * @return string Modified HTML
     */
    public function process_attachment_lazy_loading($html, $attachment_id, $size, $icon, $attr) {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true) || $icon || empty($html)) {
            return $html;
        }

        return preg_replace_callback(
            '/<img([^>]+)>/i',
            array($this, 'process_image_tag'),
            $html
        );
    }

    /**
     * Process individual image tag
     * 
     * @since 1.0.0
     * @param array $matches Regex matches
     * @return string Modified image tag
     */
    public function process_image_tag($matches) {
        
        $img_tag = $matches[0];
        $attributes = $matches[1];
        
        // Skip if already processed or has loading attribute
        if (strpos($attributes, 'data-src') !== false || strpos($attributes, 'loading=') !== false) {
            return $img_tag;
        }
        
        // Skip if marked as eager loading
        if (strpos($attributes, 'loading="eager"') !== false) {
            return $img_tag;
        }
        
        // Determine image type and priority
        $image_type = $this->determine_image_type($attributes);
        $image_config = isset($this->content_types[$image_type]) ? $this->content_types[$image_type] : $this->content_types['content_images'];
        
        // Check if this image should be preloaded
        static $preload_count = 0;
        $max_preload = get_theme_mod('recruitpro_lazy_preload_count', 3);
        
        if ($preload_count < $max_preload && $image_config['priority'] === 'high') {
            $preload_count++;
            return $img_tag; // Don't lazy load this image
        }
        
        // Extract src attribute
        preg_match('/src="([^"]+)"/i', $attributes, $src_matches);
        if (empty($src_matches[1])) {
            return $img_tag;
        }
        
        $src = $src_matches[1];
        
        // Generate placeholder
        $placeholder = $this->generate_placeholder($src, $image_config);
        
        // Build lazy loading attributes
        $lazy_attributes = array(
            'data-src="' . esc_attr($src) . '"',
            'src="' . esc_attr($placeholder) . '"',
            'class="' . $this->get_lazy_classes($attributes, $image_type) . '"',
            'data-lazy-type="' . esc_attr($image_type) . '"',
            'loading="lazy"',
        );
        
        // Add srcset if present
        if (preg_match('/srcset="([^"]+)"/i', $attributes, $srcset_matches)) {
            $lazy_attributes[] = 'data-srcset="' . esc_attr($srcset_matches[1]) . '"';
            $attributes = preg_replace('/srcset="[^"]+"/i', '', $attributes);
        }
        
        // Add sizes if present
        if (preg_match('/sizes="([^"]+)"/i', $attributes, $sizes_matches)) {
            $lazy_attributes[] = 'data-sizes="' . esc_attr($sizes_matches[1]) . '"';
        }
        
        // Remove original src and add lazy attributes
        $attributes = preg_replace('/src="[^"]+"/i', '', $attributes);
        $attributes = trim($attributes . ' ' . implode(' ', $lazy_attributes));
        
        return '<img ' . $attributes . '>';
    }

    /**
     * Determine image type from attributes
     * 
     * @since 1.0.0
     * @param string $attributes Image attributes
     * @return string Image type
     */
    private function determine_image_type($attributes) {
        
        // Check for class-based type detection
        if (strpos($attributes, 'hero') !== false || strpos($attributes, 'banner') !== false) {
            return 'hero_images';
        }
        
        if (strpos($attributes, 'team') !== false || strpos($attributes, 'staff') !== false) {
            return 'team_photos';
        }
        
        if (strpos($attributes, 'client') !== false || strpos($attributes, 'logo') !== false) {
            return 'client_logos';
        }
        
        if (strpos($attributes, 'service') !== false || strpos($attributes, 'feature') !== false) {
            return 'service_images';
        }
        
        if (strpos($attributes, 'gallery') !== false) {
            return 'gallery_images';
        }
        
        // Check for size-based detection
        if (preg_match('/width="(\d+)"/i', $attributes, $width_matches)) {
            $width = intval($width_matches[1]);
            
            if ($width >= 1200) {
                return 'hero_images';
            } elseif ($width <= 200) {
                return 'client_logos';
            } elseif ($width <= 400) {
                return 'team_photos';
            }
        }
        
        return 'content_images';
    }

    /**
     * Generate placeholder for image
     * 
     * @since 1.0.0
     * @param string $src Original image source
     * @param array $config Image configuration
     * @return string Placeholder source
     */
    private function generate_placeholder($src, $config) {
        
        $placeholder_style = get_theme_mod('recruitpro_lazy_placeholder_style', 'skeleton');
        
        switch ($placeholder_style) {
            case 'blur':
                return $this->generate_blur_placeholder($src);
                
            case 'gradient':
                return $this->generate_gradient_placeholder();
                
            case 'solid':
                return $this->generate_solid_placeholder();
                
            case 'skeleton':
                return $this->generate_skeleton_placeholder($config);
                
            case 'none':
            default:
                return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E';
        }
    }

    /**
     * Generate blur placeholder
     * 
     * @since 1.0.0
     * @param string $src Original image source
     * @return string Blur placeholder
     */
    private function generate_blur_placeholder($src) {
        
        // For now, return a simple placeholder
        // In a full implementation, this would generate a low-quality blurred version
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="100%25" height="100%25" fill="%23f0f0f0"/%3E%3C/svg%3E';
    }

    /**
     * Generate gradient placeholder
     * 
     * @since 1.0.0
     * @return string Gradient placeholder
     */
    private function generate_gradient_placeholder() {
        
        $primary_color = get_theme_mod('recruitpro_primary_color', '#1e40af');
        $secondary_color = get_theme_mod('recruitpro_secondary_color', '#64748b');
        
        $gradient = "linear-gradient(45deg, {$primary_color}20, {$secondary_color}20)";
        
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Cdefs%3E%3ClinearGradient id="grad" x1="0%25" y1="0%25" x2="100%25" y2="100%25"%3E%3Cstop offset="0%25" style="stop-color:' . urlencode($primary_color) . '20;stop-opacity:1" /%3E%3Cstop offset="100%25" style="stop-color:' . urlencode($secondary_color) . '20;stop-opacity:1" /%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grad)" /%3E%3C/svg%3E';
    }

    /**
     * Generate solid color placeholder
     * 
     * @since 1.0.0
     * @return string Solid placeholder
     */
    private function generate_solid_placeholder() {
        
        $bg_color = get_theme_mod('recruitpro_lazy_placeholder_color', '#f8fafc');
        
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="100%25" height="100%25" fill="' . urlencode($bg_color) . '"/%3E%3C/svg%3E';
    }

    /**
     * Generate skeleton placeholder
     * 
     * @since 1.0.0
     * @param array $config Image configuration
     * @return string Skeleton placeholder
     */
    private function generate_skeleton_placeholder($config) {
        
        // Professional skeleton screen for recruitment websites
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="100%25" height="100%25" fill="%23f1f5f9"/%3E%3Crect x="20" y="20" width="60" height="20" fill="%23e2e8f0" rx="4"/%3E%3Crect x="20" y="50" width="80" height="10" fill="%23e2e8f0" rx="2"/%3E%3Crect x="20" y="70" width="60" height="10" fill="%23e2e8f0" rx="2"/%3E%3Cstyle%3E rect%7Banimation:pulse 1.5s ease-in-out infinite alternate%7D @keyframes pulse%7Bfrom%7Bopacity:1%7Dto%7Bopacity:0.5%7D%7D%3C/style%3E%3C/svg%3E';
    }

    /**
     * Get lazy loading classes
     * 
     * @since 1.0.0
     * @param string $attributes Existing attributes
     * @param string $image_type Image type
     * @return string CSS classes
     */
    private function get_lazy_classes($attributes, $image_type) {
        
        $classes = array('lazy-load', 'lazy-' . str_replace('_', '-', $image_type));
        
        // Extract existing classes
        if (preg_match('/class="([^"]+)"/i', $attributes, $class_matches)) {
            $existing_classes = explode(' ', $class_matches[1]);
            $classes = array_merge($classes, $existing_classes);
        }
        
        return implode(' ', array_unique($classes));
    }

    /**
     * Process lazy content blocks
     * 
     * @since 1.0.0
     * @param string $content Content to process
     * @return string Modified content
     */
    private function process_lazy_content_blocks($content) {
        
        // Process testimonials
        $content = preg_replace_callback(
            '/\[testimonials[^\]]*\]/',
            array($this, 'create_lazy_content_block'),
            $content
        );
        
        // Process job listings
        $content = preg_replace_callback(
            '/\[jobs[^\]]*\]/',
            array($this, 'create_lazy_content_block'),
            $content
        );
        
        // Process social feeds
        $content = preg_replace_callback(
            '/\[social_feed[^\]]*\]/',
            array($this, 'create_lazy_content_block'),
            $content
        );
        
        return $content;
    }

    /**
     * Create lazy content block
     * 
     * @since 1.0.0
     * @param array $matches Regex matches
     * @return string Lazy content block
     */
    public function create_lazy_content_block($matches) {
        
        $shortcode = $matches[0];
        $block_id = 'lazy-block-' . uniqid();
        
        return '<div class="lazy-content-block" data-lazy-content="' . esc_attr($shortcode) . '" data-block-id="' . esc_attr($block_id) . '">
            <div class="lazy-content-placeholder">
                <div class="skeleton-loader">
                    <div class="skeleton-header"></div>
                    <div class="skeleton-content"></div>
                    <div class="skeleton-footer"></div>
                </div>
            </div>
        </div>';
    }

    /**
     * Output lazy loading styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_lazy_loading_styles() {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            return;
        }

        $fade_duration = get_theme_mod('recruitpro_lazy_fade_duration', 400);
        $placeholder_style = get_theme_mod('recruitpro_lazy_placeholder_style', 'skeleton');
        
        ?>
        <style id="recruitpro-lazy-loading-styles">
        /* Lazy Loading Styles */
        .lazy-load {
            opacity: 0;
            transition: opacity <?php echo $fade_duration; ?>ms ease-in-out;
        }
        
        .lazy-load.loaded {
            opacity: 1;
        }
        
        .lazy-load.loading {
            opacity: 0.5;
        }
        
        .lazy-load.error {
            opacity: 0.3;
            filter: grayscale(100%);
        }
        
        /* Placeholder Styles */
        <?php if ($placeholder_style === 'skeleton'): ?>
        .skeleton-loader {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }
        
        .skeleton-header {
            height: 20px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-bottom: 10px;
            width: 60%;
        }
        
        .skeleton-content {
            height: 10px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-bottom: 6px;
        }
        
        .skeleton-content:nth-child(2) { width: 80%; }
        .skeleton-content:nth-child(3) { width: 60%; }
        .skeleton-content:nth-child(4) { width: 90%; }
        
        .skeleton-footer {
            height: 15px;
            background: #e2e8f0;
            border-radius: 3px;
            width: 40%;
            margin-top: 15px;
        }
        
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        <?php endif; ?>
        
        /* Content Block Lazy Loading */
        .lazy-content-block {
            min-height: 200px;
            position: relative;
        }
        
        .lazy-content-placeholder {
            padding: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .lazy-content-block.loading .lazy-content-placeholder {
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        /* Professional Loading Indicators */
        .lazy-loading-indicator {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
        }
        
        .lazy-loading-indicator::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-top: 2px solid #1e40af;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Error States */
        .lazy-error {
            text-align: center;
            padding: 20px;
            color: #dc2626;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
        }
        
        .lazy-retry-button {
            background: #1e40af;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .lazy-retry-button:hover {
            background: #1d4ed8;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .lazy-load {
                transition-duration: <?php echo min($fade_duration, 200); ?>ms;
            }
            
            .skeleton-loader {
                animation-duration: 1s;
            }
        }
        
        /* Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            .lazy-load {
                transition: none;
            }
            
            .skeleton-loader {
                animation: none;
                background: #f0f0f0;
            }
        }
        
        /* Print Styles */
        @media print {
            .lazy-load {
                opacity: 1 !important;
            }
            
            .lazy-content-placeholder {
                display: none;
            }
        }
        </style>
        <?php
    }

    /**
     * Output lazy loading script initialization
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_lazy_loading_script() {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            return;
        }

        ?>
        <script>
        // Lazy Loading Fallback for No-JS
        document.documentElement.classList.add('js-enabled');
        
        // Critical above-the-fold image loading
        window.addEventListener('DOMContentLoaded', function() {
            var criticalImages = document.querySelectorAll('.lazy-load.critical, .hero .lazy-load');
            criticalImages.forEach(function(img) {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                }
            });
        });
        </script>
        <noscript>
            <style>
                .lazy-load { opacity: 1 !important; }
                .lazy-content-placeholder { display: none; }
            </style>
        </noscript>
        <?php
    }

    /**
     * Add lazy loading body classes
     * 
     * @since 1.0.0
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_lazy_loading_body_classes($classes) {
        
        if (get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            $classes[] = 'lazy-loading-enabled';
            
            $strategy = get_theme_mod('recruitpro_lazy_strategy', 'intersection_observer');
            $classes[] = 'lazy-strategy-' . str_replace('_', '-', $strategy);
            
            if (get_theme_mod('recruitpro_lazy_adaptive_loading', true)) {
                $classes[] = 'adaptive-loading';
            }
            
            if (get_theme_mod('recruitpro_lazy_mobile_optimization', true) && wp_is_mobile()) {
                $classes[] = 'mobile-optimized-loading';
            }
        }
        
        return $classes;
    }

    /**
     * AJAX handler for lazy content loading
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_load_content() {
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_lazy_loading_nonce')) {
            wp_die('Security check failed');
        }
        
        $shortcode = sanitize_text_field($_POST['shortcode']);
        $block_id = sanitize_text_field($_POST['block_id']);
        
        // Process the shortcode
        $content = do_shortcode($shortcode);
        
        wp_send_json_success(array(
            'content' => $content,
            'block_id' => $block_id,
        ));
    }

    /**
     * Get connection type for adaptive loading
     * 
     * @since 1.0.0
     * @return string Connection type
     */
    private function get_connection_type() {
        
        // Check for Save-Data header
        if (isset($_SERVER['HTTP_SAVE_DATA']) && $_SERVER['HTTP_SAVE_DATA'] === 'on') {
            return 'save_data';
        }
        
        // Default to medium for server-side detection
        return 'medium_connection';
    }

    /**
     * Get strategy choices for customizer
     * 
     * @since 1.0.0
     * @return array Strategy choices
     */
    private function get_strategy_choices() {
        
        $choices = array();
        
        foreach ($this->loading_strategies as $key => $strategy) {
            $choices[$key] = $strategy['name'] . ' (' . $strategy['browser_support'] . ' support, ' . $strategy['performance'] . ' performance)';
        }
        
        return $choices;
    }

    /**
     * Sanitize strategy selection
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_strategy($input) {
        
        $valid_strategies = array_keys($this->loading_strategies);
        return in_array($input, $valid_strategies) ? $input : 'intersection_observer';
    }

    /**
     * Get loading performance metrics
     * 
     * @since 1.0.0
     * @return array Performance metrics
     */
    public function get_performance_metrics() {
        
        if (!get_theme_mod('recruitpro_lazy_performance_monitoring', false)) {
            return array();
        }
        
        return array(
            'images_loaded' => 0,
            'content_blocks_loaded' => 0,
            'average_load_time' => 0,
            'bandwidth_saved' => 0,
        );
    }
}

// Initialize lazy loading system
new RecruitPro_Lazy_Loading();

/**
 * Helper function to check if lazy loading is enabled
 * 
 * @since 1.0.0
 * @return bool Whether lazy loading is enabled
 */
function recruitpro_is_lazy_loading_enabled() {
    return get_theme_mod('recruitpro_enable_lazy_loading', true);
}

/**
 * Helper function to get lazy loading threshold
 * 
 * @since 1.0.0
 * @return int Lazy loading threshold in pixels
 */
function recruitpro_get_lazy_threshold() {
    return get_theme_mod('recruitpro_lazy_threshold', 150);
}

/**
 * Helper function to manually trigger lazy loading for custom content
 * 
 * @since 1.0.0
 * @param string $content Content to process
 * @return string Processed content
 */
function recruitpro_apply_lazy_loading($content) {
    
    if (!recruitpro_is_lazy_loading_enabled()) {
        return $content;
    }
    
    static $lazy_loading_instance = null;
    
    if (is_null($lazy_loading_instance)) {
        $lazy_loading_instance = new RecruitPro_Lazy_Loading();
    }
    
    return $lazy_loading_instance->process_content_lazy_loading($content);
}

/**
 * Helper function to create lazy content block
 * 
 * @since 1.0.0
 * @param string $shortcode Shortcode to lazy load
 * @param array $args Additional arguments
 * @return string Lazy content block HTML
 */
function recruitpro_create_lazy_block($shortcode, $args = array()) {
    
    if (!recruitpro_is_lazy_loading_enabled()) {
        return do_shortcode($shortcode);
    }
    
    $defaults = array(
        'placeholder' => 'skeleton',
        'priority' => 'medium',
        'threshold' => recruitpro_get_lazy_threshold(),
    );
    
    $args = wp_parse_args($args, $defaults);
    $block_id = 'lazy-block-' . uniqid();
    
    return '<div class="lazy-content-block" data-lazy-content="' . esc_attr($shortcode) . '" data-block-id="' . esc_attr($block_id) . '" data-priority="' . esc_attr($args['priority']) . '" data-threshold="' . esc_attr($args['threshold']) . '">
        <div class="lazy-content-placeholder ' . esc_attr($args['placeholder']) . '">
            <div class="skeleton-loader">
                <div class="skeleton-header"></div>
                <div class="skeleton-content"></div>
                <div class="skeleton-footer"></div>
            </div>
        </div>
    </div>';
}

?>