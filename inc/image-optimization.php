<?php
/**
 * RecruitPro Theme Image Optimization
 *
 * This file handles image optimization features for the RecruitPro recruitment
 * website theme. It includes responsive images, lazy loading, WebP conversion,
 * compression, SEO optimization, and accessibility features specifically
 * designed for recruitment agency websites and professional content.
 *
 * @package RecruitPro
 * @subpackage Theme/Images
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/image-optimization.php
 * Purpose: Image optimization and responsive image management
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Image Optimization Class
 * 
 * Handles all image optimization functionality including responsive images,
 * lazy loading, compression, and accessibility features.
 *
 * @since 1.0.0
 */
class RecruitPro_Image_Optimization {

    /**
     * Custom image sizes for recruitment content
     * 
     * @since 1.0.0
     * @var array
     */
    private $image_sizes = array();

    /**
     * Supported image formats
     * 
     * @since 1.0.0
     * @var array
     */
    private $supported_formats = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_image_sizes();
        $this->init_supported_formats();
        
        add_action('after_setup_theme', array($this, 'add_custom_image_sizes'));
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_filter('wp_get_attachment_image_attributes', array($this, 'add_responsive_image_attributes'), 10, 3);
        add_filter('the_content', array($this, 'optimize_content_images'));
        add_filter('post_thumbnail_html', array($this, 'optimize_featured_images'), 10, 5);
        add_filter('wp_calculate_image_srcset', array($this, 'customize_image_srcset'), 10, 5);
        add_filter('max_srcset_image_width', array($this, 'max_srcset_width'));
        add_filter('wp_get_attachment_image_src', array($this, 'maybe_use_webp'), 10, 4);
        add_filter('upload_mimes', array($this, 'add_webp_upload_support'));
        add_filter('wp_handle_upload_prefilter', array($this, 'validate_image_upload'));
        add_action('wp_head', array($this, 'output_image_optimization_css'), 15);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_lazy_loading_script'));
        add_action('add_attachment', array($this, 'generate_webp_versions'));
    }

    /**
     * Initialize custom image sizes for recruitment content
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_image_sizes() {
        
        $this->image_sizes = array(
            // Hero and banner images
            'recruitpro-hero-desktop' => array(
                'width' => 1920,
                'height' => 800,
                'crop' => true,
                'description' => __('Hero banner for desktop (1920x800)', 'recruitpro'),
                'usage' => 'hero',
            ),
            'recruitpro-hero-tablet' => array(
                'width' => 1024,
                'height' => 600,
                'crop' => true,
                'description' => __('Hero banner for tablet (1024x600)', 'recruitpro'),
                'usage' => 'hero',
            ),
            'recruitpro-hero-mobile' => array(
                'width' => 768,
                'height' => 500,
                'crop' => true,
                'description' => __('Hero banner for mobile (768x500)', 'recruitpro'),
                'usage' => 'hero',
            ),
            
            // Team and profile images
            'recruitpro-team-large' => array(
                'width' => 400,
                'height' => 400,
                'crop' => true,
                'description' => __('Large team member photo (400x400)', 'recruitpro'),
                'usage' => 'team',
            ),
            'recruitpro-team-medium' => array(
                'width' => 300,
                'height' => 300,
                'crop' => true,
                'description' => __('Medium team member photo (300x300)', 'recruitpro'),
                'usage' => 'team',
            ),
            'recruitpro-team-small' => array(
                'width' => 150,
                'height' => 150,
                'crop' => true,
                'description' => __('Small team member photo (150x150)', 'recruitpro'),
                'usage' => 'team',
            ),
            
            // Client and company logos
            'recruitpro-client-logo-large' => array(
                'width' => 300,
                'height' => 150,
                'crop' => false,
                'description' => __('Large client logo (max 300x150)', 'recruitpro'),
                'usage' => 'logo',
            ),
            'recruitpro-client-logo-medium' => array(
                'width' => 200,
                'height' => 100,
                'crop' => false,
                'description' => __('Medium client logo (max 200x100)', 'recruitpro'),
                'usage' => 'logo',
            ),
            'recruitpro-client-logo-small' => array(
                'width' => 120,
                'height' => 60,
                'crop' => false,
                'description' => __('Small client logo (max 120x60)', 'recruitpro'),
                'usage' => 'logo',
            ),
            
            // Blog and content images
            'recruitpro-blog-featured' => array(
                'width' => 800,
                'height' => 450,
                'crop' => true,
                'description' => __('Blog featured image (800x450)', 'recruitpro'),
                'usage' => 'content',
            ),
            'recruitpro-blog-grid' => array(
                'width' => 600,
                'height' => 400,
                'crop' => true,
                'description' => __('Blog grid thumbnail (600x400)', 'recruitpro'),
                'usage' => 'content',
            ),
            'recruitpro-blog-list' => array(
                'width' => 400,
                'height' => 250,
                'crop' => true,
                'description' => __('Blog list thumbnail (400x250)', 'recruitpro'),
                'usage' => 'content',
            ),
            
            // Service and feature images
            'recruitpro-service-large' => array(
                'width' => 600,
                'height' => 400,
                'crop' => true,
                'description' => __('Large service image (600x400)', 'recruitpro'),
                'usage' => 'service',
            ),
            'recruitpro-service-medium' => array(
                'width' => 400,
                'height' => 300,
                'crop' => true,
                'description' => __('Medium service image (400x300)', 'recruitpro'),
                'usage' => 'service',
            ),
            'recruitpro-service-icon' => array(
                'width' => 100,
                'height' => 100,
                'crop' => true,
                'description' => __('Service icon (100x100)', 'recruitpro'),
                'usage' => 'service',
            ),
            
            // Gallery and portfolio images
            'recruitpro-gallery-large' => array(
                'width' => 1200,
                'height' => 800,
                'crop' => true,
                'description' => __('Large gallery image (1200x800)', 'recruitpro'),
                'usage' => 'gallery',
            ),
            'recruitpro-gallery-thumb' => array(
                'width' => 300,
                'height' => 200,
                'crop' => true,
                'description' => __('Gallery thumbnail (300x200)', 'recruitpro'),
                'usage' => 'gallery',
            ),
            
            // Social media optimized
            'recruitpro-social-share' => array(
                'width' => 1200,
                'height' => 630,
                'crop' => true,
                'description' => __('Social media sharing image (1200x630)', 'recruitpro'),
                'usage' => 'social',
            ),
        );
    }

    /**
     * Initialize supported image formats
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_supported_formats() {
        
        $this->supported_formats = array(
            'jpeg' => array(
                'mime' => 'image/jpeg',
                'extensions' => array('jpg', 'jpeg'),
                'quality' => 85,
                'compression' => true,
            ),
            'png' => array(
                'mime' => 'image/png',
                'extensions' => array('png'),
                'quality' => 90,
                'compression' => true,
            ),
            'webp' => array(
                'mime' => 'image/webp',
                'extensions' => array('webp'),
                'quality' => 80,
                'compression' => true,
            ),
            'avif' => array(
                'mime' => 'image/avif',
                'extensions' => array('avif'),
                'quality' => 75,
                'compression' => true,
            ),
            'svg' => array(
                'mime' => 'image/svg+xml',
                'extensions' => array('svg'),
                'quality' => 100,
                'compression' => false,
            ),
        );
    }

    /**
     * Add custom image sizes to WordPress
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_custom_image_sizes() {
        
        foreach ($this->image_sizes as $size_name => $size_data) {
            add_image_size(
                $size_name,
                $size_data['width'],
                $size_data['height'],
                $size_data['crop']
            );
        }
    }

    /**
     * Add image optimization customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {

        // =================================================================
        // IMAGE OPTIMIZATION SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_image_optimization', array(
            'title' => __('Image Optimization', 'recruitpro'),
            'description' => __('Configure image optimization settings for better performance and user experience on your recruitment website.', 'recruitpro'),
            'priority' => 135,
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
            'description' => __('Load images only when they become visible for better performance.', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'checkbox',
        ));

        // WebP conversion
        $wp_customize->add_setting('recruitpro_enable_webp', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_webp', array(
            'label' => __('Enable WebP Conversion', 'recruitpro'),
            'description' => __('Convert images to WebP format for smaller file sizes.', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'checkbox',
        ));

        // Image quality
        $wp_customize->add_setting('recruitpro_image_quality', array(
            'default' => 85,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_image_quality', array(
            'label' => __('Image Quality (%)', 'recruitpro'),
            'description' => __('Balance between image quality and file size (recommended: 85%).', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 60,
                'max' => 100,
                'step' => 5,
            ),
        ));

        // Maximum image width
        $wp_customize->add_setting('recruitpro_max_image_width', array(
            'default' => 2048,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_max_image_width', array(
            'label' => __('Maximum Image Width (pixels)', 'recruitpro'),
            'description' => __('Resize large images on upload to save space.', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1200,
                'max' => 4096,
                'step' => 128,
            ),
        ));

        // Responsive images
        $wp_customize->add_setting('recruitpro_responsive_images', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_responsive_images', array(
            'label' => __('Enable Responsive Images', 'recruitpro'),
            'description' => __('Automatically serve appropriate image sizes based on device.', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'checkbox',
        ));

        // CDN support
        $wp_customize->add_setting('recruitpro_image_cdn_url', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_image_cdn_url', array(
            'label' => __('Image CDN URL', 'recruitpro'),
            'description' => __('Optional: CDN URL for serving images (e.g., https://cdn.example.com).', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'url',
        ));

        // Alt text enforcement
        $wp_customize->add_setting('recruitpro_enforce_alt_text', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enforce_alt_text', array(
            'label' => __('Enforce Alt Text', 'recruitpro'),
            'description' => __('Require alt text for accessibility compliance.', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'checkbox',
        ));

        // Image compression level
        $wp_customize->add_setting('recruitpro_compression_level', array(
            'default' => 'balanced',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_compression_level', array(
            'label' => __('Compression Level', 'recruitpro'),
            'description' => __('Choose compression strategy for your recruitment images.', 'recruitpro'),
            'section' => 'recruitpro_image_optimization',
            'type' => 'select',
            'choices' => array(
                'maximum_quality' => __('Maximum Quality (Larger files)', 'recruitpro'),
                'balanced' => __('Balanced (Recommended)', 'recruitpro'),
                'maximum_compression' => __('Maximum Compression (Smaller files)', 'recruitpro'),
            ),
        ));
    }

    /**
     * Add responsive image attributes
     * 
     * @since 1.0.0
     * @param array $attr Image attributes
     * @param WP_Post $attachment Image attachment post
     * @param string $size Image size
     * @return array Modified attributes
     */
    public function add_responsive_image_attributes($attr, $attachment, $size) {
        
        if (!get_theme_mod('recruitpro_responsive_images', true)) {
            return $attr;
        }

        // Add lazy loading attributes if enabled
        if (get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            $attr['loading'] = 'lazy';
            $attr['class'] = isset($attr['class']) ? $attr['class'] . ' lazy-load' : 'lazy-load';
        }

        // Add proper alt text if missing
        if (empty($attr['alt'])) {
            $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            if (empty($alt_text) && get_theme_mod('recruitpro_enforce_alt_text', true)) {
                $attr['alt'] = $attachment->post_title;
            }
        }

        // Add dimensions for better layout stability
        if (!empty($size) && is_string($size)) {
            $image_meta = wp_get_attachment_metadata($attachment->ID);
            if (!empty($image_meta['sizes'][$size])) {
                $attr['width'] = $image_meta['sizes'][$size]['width'];
                $attr['height'] = $image_meta['sizes'][$size]['height'];
            }
        }

        // Add recruitment-specific classes
        $attr['class'] = isset($attr['class']) ? $attr['class'] . ' recruitpro-image' : 'recruitpro-image';

        return $attr;
    }

    /**
     * Optimize images in content
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Optimized content
     */
    public function optimize_content_images($content) {
        
        if (is_admin() || is_feed()) {
            return $content;
        }

        // Add lazy loading to content images
        if (get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            $content = preg_replace('/<img(?![^>]*loading=)/', '<img loading="lazy"', $content);
        }

        // Add responsive classes
        $content = preg_replace('/<img(?![^>]*class=)/', '<img class="recruitpro-content-image"', $content);
        $content = preg_replace('/<img([^>]*class=["\'])((?!.*recruitpro-content-image).*)(["\'][^>]*)>/', '<img$1$2 recruitpro-content-image$3>', $content);

        return $content;
    }

    /**
     * Optimize featured images
     * 
     * @since 1.0.0
     * @param string $html Featured image HTML
     * @param int $post_id Post ID
     * @param int $post_thumbnail_id Thumbnail ID
     * @param string $size Image size
     * @param array $attr Image attributes
     * @return string Optimized HTML
     */
    public function optimize_featured_images($html, $post_id, $post_thumbnail_id, $size, $attr) {
        
        if (empty($html)) {
            return $html;
        }

        // Add recruitment-specific classes to featured images
        $html = str_replace('<img ', '<img class="recruitpro-featured-image" ', $html);

        // Add schema markup for better SEO
        $html = str_replace('<img ', '<img itemprop="image" ', $html);

        return $html;
    }

    /**
     * Customize image srcset
     * 
     * @since 1.0.0
     * @param array $sources Srcset sources
     * @param array $size_array Image size array
     * @param string $image_src Image source URL
     * @param array $image_meta Image metadata
     * @param int $attachment_id Attachment ID
     * @return array Modified srcset sources
     */
    public function customize_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        
        if (!get_theme_mod('recruitpro_responsive_images', true)) {
            return $sources;
        }

        // Filter out very large images for mobile
        if (wp_is_mobile()) {
            foreach ($sources as $width => $source) {
                if ($width > 1024) {
                    unset($sources[$width]);
                }
            }
        }

        return $sources;
    }

    /**
     * Set maximum srcset width
     * 
     * @since 1.0.0
     * @param int $max_width Current max width
     * @return int Modified max width
     */
    public function max_srcset_width($max_width) {
        
        $custom_max = get_theme_mod('recruitpro_max_image_width', 2048);
        return min($max_width, $custom_max);
    }

    /**
     * Maybe serve WebP version of image
     * 
     * @since 1.0.0
     * @param array $image Image data
     * @param int $attachment_id Attachment ID
     * @param string $size Image size
     * @param bool $icon Whether image is an icon
     * @return array Modified image data
     */
    public function maybe_use_webp($image, $attachment_id, $size, $icon) {
        
        if (!get_theme_mod('recruitpro_enable_webp', true) || $icon) {
            return $image;
        }

        // Check if browser supports WebP
        if (!$this->browser_supports_webp()) {
            return $image;
        }

        // Get WebP version if it exists
        $webp_url = $this->get_webp_url($image[0]);
        if ($webp_url) {
            $image[0] = $webp_url;
        }

        return $image;
    }

    /**
     * Add WebP upload support
     * 
     * @since 1.0.0
     * @param array $mimes Allowed mime types
     * @return array Modified mime types
     */
    public function add_webp_upload_support($mimes) {
        
        if (get_theme_mod('recruitpro_enable_webp', true)) {
            $mimes['webp'] = 'image/webp';
        }

        return $mimes;
    }

    /**
     * Validate image upload
     * 
     * @since 1.0.0
     * @param array $file Upload file data
     * @return array Modified file data or error
     */
    public function validate_image_upload($file) {
        
        if (!$this->is_image_file($file['type'])) {
            return $file;
        }

        // Check file size limits
        $max_size = get_theme_mod('recruitpro_max_upload_size', 5242880); // 5MB default
        if ($file['size'] > $max_size) {
            $file['error'] = sprintf(
                __('Image file size must be less than %s.', 'recruitpro'),
                size_format($max_size)
            );
            return $file;
        }

        // Check dimensions
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info) {
            $max_width = get_theme_mod('recruitpro_max_image_width', 2048);
            $max_height = get_theme_mod('recruitpro_max_image_height', 2048);
            
            if ($image_info[0] > $max_width || $image_info[1] > $max_height) {
                // Optionally resize instead of rejecting
                if (get_theme_mod('recruitpro_auto_resize', true)) {
                    $file = $this->resize_uploaded_image($file, $max_width, $max_height);
                } else {
                    $file['error'] = sprintf(
                        __('Image dimensions must be less than %dx%d pixels.', 'recruitpro'),
                        $max_width,
                        $max_height
                    );
                }
            }
        }

        return $file;
    }

    /**
     * Output image optimization CSS
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_image_optimization_css() {
        
        ?>
        <style id="recruitpro-image-optimization">
        /* Image optimization and responsive styles */
        .recruitpro-image,
        .recruitpro-content-image,
        .recruitpro-featured-image {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        /* Lazy loading placeholder */
        .lazy-load {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .lazy-load.loaded {
            opacity: 1;
        }
        
        /* Image loading states */
        .recruitpro-image[loading="lazy"] {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: recruitpro-loading 1.5s infinite;
        }
        
        @keyframes recruitpro-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Team member images */
        .recruitpro-team-image {
            border-radius: 50%;
            object-fit: cover;
            aspect-ratio: 1;
        }
        
        /* Client logo images */
        .recruitpro-client-logo {
            object-fit: contain;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }
        
        .recruitpro-client-logo:hover {
            filter: grayscale(0%);
        }
        
        /* Hero images */
        .recruitpro-hero-image {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }
        
        /* Gallery images */
        .recruitpro-gallery-image {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .recruitpro-gallery-image:hover {
            transform: scale(1.05);
        }
        
        /* Image captions */
        .wp-caption {
            max-width: 100%;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
            margin: 1em 0;
        }
        
        .wp-caption-text {
            font-size: 0.9em;
            color: #666;
            text-align: center;
            margin: 0.5em 0 0;
            font-style: italic;
        }
        
        /* Responsive image containers */
        .image-container {
            position: relative;
            overflow: hidden;
        }
        
        .image-container::before {
            content: '';
            display: block;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
        }
        
        .image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .recruitpro-hero-image {
                object-position: center center;
            }
            
            .recruitpro-gallery-image:hover {
                transform: none;
            }
        }
        
        /* Print styles */
        @media print {
            .lazy-load {
                opacity: 1 !important;
            }
            
            .recruitpro-image,
            .recruitpro-content-image {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
        </style>
        <?php
    }

    /**
     * Enqueue lazy loading script
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_lazy_loading_script() {
        
        if (!get_theme_mod('recruitpro_enable_lazy_loading', true)) {
            return;
        }

        wp_enqueue_script(
            'recruitpro-lazy-loading',
            get_template_directory_uri() . '/assets/js/lazy-loading.js',
            array(),
            wp_get_theme()->get('Version'),
            true
        );

        // Localize script with settings
        wp_localize_script('recruitpro-lazy-loading', 'recruitpro_lazy_loading', array(
            'threshold' => get_theme_mod('recruitpro_lazy_threshold', '50px'),
            'fade_in' => get_theme_mod('recruitpro_lazy_fade_in', true),
            'placeholder_color' => get_theme_mod('recruitpro_lazy_placeholder_color', '#f0f0f0'),
        ));
    }

    /**
     * Generate WebP versions of uploaded images
     * 
     * @since 1.0.0
     * @param int $attachment_id Attachment ID
     * @return void
     */
    public function generate_webp_versions($attachment_id) {
        
        if (!get_theme_mod('recruitpro_enable_webp', true)) {
            return;
        }

        if (!wp_attachment_is_image($attachment_id)) {
            return;
        }

        $this->create_webp_versions($attachment_id);
    }

    /**
     * Check if browser supports WebP
     * 
     * @since 1.0.0
     * @return bool Whether browser supports WebP
     */
    private function browser_supports_webp() {
        
        static $supports_webp = null;
        
        if ($supports_webp !== null) {
            return $supports_webp;
        }

        $accept_header = $_SERVER['HTTP_ACCEPT'] ?? '';
        $supports_webp = strpos($accept_header, 'image/webp') !== false;
        
        return $supports_webp;
    }

    /**
     * Get WebP URL for an image
     * 
     * @since 1.0.0
     * @param string $image_url Original image URL
     * @return string|false WebP URL or false if not available
     */
    private function get_webp_url($image_url) {
        
        $webp_url = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image_url);
        
        // Check if WebP file exists
        $webp_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $webp_url);
        
        if (file_exists($webp_path)) {
            return $webp_url;
        }
        
        return false;
    }

    /**
     * Check if file is an image
     * 
     * @since 1.0.0
     * @param string $mime_type File mime type
     * @return bool Whether file is an image
     */
    private function is_image_file($mime_type) {
        
        $image_types = array(
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        );
        
        return in_array($mime_type, $image_types);
    }

    /**
     * Resize uploaded image
     * 
     * @since 1.0.0
     * @param array $file Upload file data
     * @param int $max_width Maximum width
     * @param int $max_height Maximum height
     * @return array Modified file data
     */
    private function resize_uploaded_image($file, $max_width, $max_height) {
        
        if (!function_exists('wp_get_image_editor')) {
            return $file;
        }

        $editor = wp_get_image_editor($file['tmp_name']);
        
        if (is_wp_error($editor)) {
            return $file;
        }

        $editor->resize($max_width, $max_height, false);
        $resized = $editor->save($file['tmp_name']);
        
        if (!is_wp_error($resized)) {
            $file['size'] = filesize($file['tmp_name']);
        }
        
        return $file;
    }

    /**
     * Create WebP versions of image sizes
     * 
     * @since 1.0.0
     * @param int $attachment_id Attachment ID
     * @return void
     */
    private function create_webp_versions($attachment_id) {
        
        if (!function_exists('imagewebp')) {
            return; // WebP not supported
        }

        $metadata = wp_get_attachment_metadata($attachment_id);
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/' . $metadata['file'];
        
        // Create WebP version of original
        $this->convert_to_webp($file_path);
        
        // Create WebP versions of all sizes
        if (!empty($metadata['sizes'])) {
            $file_dir = dirname($file_path);
            
            foreach ($metadata['sizes'] as $size_data) {
                $size_path = $file_dir . '/' . $size_data['file'];
                $this->convert_to_webp($size_path);
            }
        }
    }

    /**
     * Convert image to WebP
     * 
     * @since 1.0.0
     * @param string $source_path Source image path
     * @return bool Success status
     */
    private function convert_to_webp($source_path) {
        
        if (!file_exists($source_path)) {
            return false;
        }

        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $source_path);
        
        if (file_exists($webp_path)) {
            return true; // Already exists
        }

        $image_info = getimagesize($source_path);
        
        if (!$image_info) {
            return false;
        }

        $quality = get_theme_mod('recruitpro_image_quality', 80);
        
        switch ($image_info['mime']) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source_path);
                break;
            default:
                return false;
        }
        
        if (!$image) {
            return false;
        }

        $result = imagewebp($image, $webp_path, $quality);
        imagedestroy($image);
        
        return $result;
    }

    /**
     * Get custom image sizes
     * 
     * @since 1.0.0
     * @return array Custom image sizes
     */
    public function get_custom_image_sizes() {
        return $this->image_sizes;
    }

    /**
     * Get image size by usage
     * 
     * @since 1.0.0
     * @param string $usage Image usage type
     * @return array Image sizes for usage
     */
    public function get_sizes_by_usage($usage) {
        
        $filtered_sizes = array();
        
        foreach ($this->image_sizes as $size_name => $size_data) {
            if (isset($size_data['usage']) && $size_data['usage'] === $usage) {
                $filtered_sizes[$size_name] = $size_data;
            }
        }
        
        return $filtered_sizes;
    }
}

// Initialize image optimization
new RecruitPro_Image_Optimization();

/**
 * Helper function to get optimized image
 * 
 * @since 1.0.0
 * @param int $attachment_id Attachment ID
 * @param string $size Image size
 * @param array $attr Additional attributes
 * @return string Image HTML
 */
function recruitpro_get_optimized_image($attachment_id, $size = 'medium', $attr = array()) {
    
    $default_attr = array(
        'class' => 'recruitpro-image',
        'loading' => get_theme_mod('recruitpro_enable_lazy_loading', true) ? 'lazy' : 'eager',
    );
    
    $attr = wp_parse_args($attr, $default_attr);
    
    return wp_get_attachment_image($attachment_id, $size, false, $attr);
}

/**
 * Helper function to get responsive image HTML
 * 
 * @since 1.0.0
 * @param int $attachment_id Attachment ID
 * @param array $sizes Responsive sizes
 * @param array $attr Additional attributes
 * @return string Responsive image HTML
 */
function recruitpro_get_responsive_image($attachment_id, $sizes = array(), $attr = array()) {
    
    $default_sizes = array(
        'mobile' => 'recruitpro-blog-list',
        'tablet' => 'recruitpro-blog-grid',
        'desktop' => 'recruitpro-blog-featured',
    );
    
    $sizes = wp_parse_args($sizes, $default_sizes);
    
    // Build srcset
    $srcset = array();
    foreach ($sizes as $breakpoint => $size) {
        $image_data = wp_get_attachment_image_src($attachment_id, $size);
        if ($image_data) {
            $srcset[] = $image_data[0] . ' ' . $image_data[1] . 'w';
        }
    }
    
    if (empty($srcset)) {
        return recruitpro_get_optimized_image($attachment_id, 'medium', $attr);
    }
    
    $attr['srcset'] = implode(', ', $srcset);
    $attr['sizes'] = '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw';
    
    return recruitpro_get_optimized_image($attachment_id, $sizes['desktop'], $attr);
}

/**
 * Helper function to check if image optimization is enabled
 * 
 * @since 1.0.0
 * @param string $feature Feature to check
 * @return bool Whether feature is enabled
 */
function recruitpro_is_image_optimization_enabled($feature = 'lazy_loading') {
    
    $features = array(
        'lazy_loading' => 'recruitpro_enable_lazy_loading',
        'webp' => 'recruitpro_enable_webp',
        'responsive' => 'recruitpro_responsive_images',
    );
    
    if (!isset($features[$feature])) {
        return false;
    }
    
    return get_theme_mod($features[$feature], true);
}

?>