<?php
/**
 * RecruitPro Theme Mobile Optimization
 *
 * Comprehensive mobile optimization system for the RecruitPro recruitment theme.
 * Provides intelligent mobile device detection, touch-friendly interfaces,
 * performance optimizations, PWA features, and mobile-specific enhancements
 * for recruitment agency websites.
 *
 * @package RecruitPro
 * @subpackage Theme/Mobile
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/mobile-optimization.php
 * Purpose: Mobile-first optimization and enhancements
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-level mobile optimization)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Mobile Optimization Class
 * 
 * Handles all mobile-specific optimizations, device detection,
 * and performance enhancements for mobile users.
 *
 * @since 1.0.0
 */
class RecruitPro_Mobile_Optimization {

    /**
     * Mobile device detection cache
     * 
     * @since 1.0.0
     * @var array
     */
    private $device_cache = array();

    /**
     * Mobile optimization settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $mobile_settings = array();

    /**
     * Touch device capabilities
     * 
     * @since 1.0.0
     * @var array
     */
    private $touch_capabilities = array();

    /**
     * Performance metrics for mobile
     * 
     * @since 1.0.0
     * @var array
     */
    private $mobile_metrics = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_mobile_settings();
        $this->init_device_detection();
        
        // Core mobile hooks
        add_action('wp_head', array($this, 'output_mobile_meta_tags'), 1);
        add_action('wp_head', array($this, 'output_mobile_styles'), 10);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_mobile_assets'), 15);
        add_action('wp_footer', array($this, 'output_mobile_scripts'), 20);
        
        // Mobile-specific optimizations
        add_filter('body_class', array($this, 'add_mobile_body_classes'));
        add_filter('wp_nav_menu_args', array($this, 'optimize_mobile_menu'));
        add_filter('wp_get_attachment_image_attributes', array($this, 'optimize_mobile_images'), 10, 3);
        add_filter('the_content', array($this, 'optimize_mobile_content'));
        
        // Touch and gesture support
        add_action('wp_head', array($this, 'add_touch_icon_support'));
        add_action('wp_head', array($this, 'add_viewport_meta_tag'));
        add_action('wp_head', array($this, 'add_mobile_theme_color'));
        
        // PWA support
        if (get_theme_mod('recruitpro_enable_pwa', false)) {
            add_action('wp_head', array($this, 'add_pwa_manifest'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_pwa_service_worker'));
        }
        
        // Performance optimizations
        add_action('wp_head', array($this, 'add_mobile_preconnect_hints'), 2);
        add_filter('script_loader_tag', array($this, 'optimize_mobile_scripts'), 10, 3);
        add_filter('style_loader_tag', array($this, 'optimize_mobile_styles_loading'), 10, 4);
        
        // Mobile forms optimization
        add_filter('comment_form_defaults', array($this, 'optimize_mobile_comment_form'));
        add_filter('get_search_form', array($this, 'optimize_mobile_search_form'));
        
        // Customizer options
        add_action('customize_register', array($this, 'add_mobile_customizer_options'));
        
        // AJAX handlers
        add_action('wp_ajax_recruitpro_mobile_performance', array($this, 'get_mobile_performance_data'));
        add_action('wp_ajax_nopriv_recruitpro_mobile_performance', array($this, 'get_mobile_performance_data'));
        
        // Admin optimizations
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'admin_mobile_styles'));
        }
    }

    /**
     * Initialize mobile settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_mobile_settings() {
        
        $this->mobile_settings = array(
            'enable_mobile_optimization' => get_theme_mod('recruitpro_mobile_optimization', true),
            'mobile_menu_style' => get_theme_mod('recruitpro_mobile_menu_style', 'slide'),
            'touch_optimized' => get_theme_mod('recruitpro_touch_optimized', true),
            'mobile_performance_mode' => get_theme_mod('recruitpro_mobile_performance_mode', 'balanced'),
            'enable_gesture_support' => get_theme_mod('recruitpro_gesture_support', true),
            'mobile_image_quality' => get_theme_mod('recruitpro_mobile_image_quality', 85),
            'lazy_load_mobile' => get_theme_mod('recruitpro_lazy_load_mobile', true),
            'mobile_font_loading' => get_theme_mod('recruitpro_mobile_font_loading', 'swap'),
            'reduce_animations_mobile' => get_theme_mod('recruitpro_reduce_animations_mobile', false),
            'mobile_cache_strategy' => get_theme_mod('recruitpro_mobile_cache_strategy', 'aggressive'),
            'pwa_enabled' => get_theme_mod('recruitpro_enable_pwa', false),
            'offline_support' => get_theme_mod('recruitpro_offline_support', false),
            'mobile_debug' => get_theme_mod('recruitpro_mobile_debug', false),
        );
    }

    /**
     * Initialize device detection
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_device_detection() {
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $this->device_cache['user_agent'] = $user_agent;
        
        // Detect mobile device
        $this->device_cache['is_mobile'] = $this->detect_mobile_device($user_agent);
        
        // Detect tablet
        $this->device_cache['is_tablet'] = $this->detect_tablet_device($user_agent);
        
        // Detect touch capability
        $this->device_cache['is_touch'] = $this->detect_touch_device($user_agent);
        
        // Detect specific platforms
        $this->device_cache['platform'] = $this->detect_platform($user_agent);
        
        // Network detection
        $this->device_cache['connection'] = $this->detect_connection_type();
        
        // Screen capabilities
        $this->device_cache['screen_info'] = $this->detect_screen_capabilities();
    }

    /**
     * Detect mobile device
     * 
     * @since 1.0.0
     * @param string $user_agent User agent string
     * @return bool True if mobile device
     */
    private function detect_mobile_device($user_agent) {
        
        // Use WordPress built-in detection first
        if (wp_is_mobile()) {
            return true;
        }
        
        // Enhanced mobile detection patterns
        $mobile_patterns = array(
            '/Mobile|iP(hone|od|ad)|Android|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune/i',
            '/nokia|samsung|lg|motorola|htc|sony|asus|huawei|xiaomi|oppo|vivo|realme|oneplus/i',
        );
        
        foreach ($mobile_patterns as $pattern) {
            if (preg_match($pattern, $user_agent)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Detect tablet device
     * 
     * @since 1.0.0
     * @param string $user_agent User agent string
     * @return bool True if tablet device
     */
    private function detect_tablet_device($user_agent) {
        
        $tablet_patterns = array(
            '/iPad/i',
            '/Android.*Tablet/i',
            '/Windows.*Touch/i',
            '/Kindle|Silk/i',
            '/PlayBook/i',
            '/Galaxy.*Tab/i',
        );
        
        foreach ($tablet_patterns as $pattern) {
            if (preg_match($pattern, $user_agent)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Detect touch device
     * 
     * @since 1.0.0
     * @param string $user_agent User agent string
     * @return bool True if touch device
     */
    private function detect_touch_device($user_agent) {
        
        // Most mobile and tablet devices are touch-enabled
        if ($this->device_cache['is_mobile'] || $this->device_cache['is_tablet']) {
            return true;
        }
        
        // Touch patterns for other devices
        $touch_patterns = array(
            '/Touch/i',
            '/Tablet/i',
            '/Mobile/i',
        );
        
        foreach ($touch_patterns as $pattern) {
            if (preg_match($pattern, $user_agent)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Detect platform
     * 
     * @since 1.0.0
     * @param string $user_agent User agent string
     * @return string Platform name
     */
    private function detect_platform($user_agent) {
        
        if (preg_match('/iPhone|iPod|iPad/i', $user_agent)) {
            return 'ios';
        } elseif (preg_match('/Android/i', $user_agent)) {
            return 'android';
        } elseif (preg_match('/Windows Phone|IEMobile/i', $user_agent)) {
            return 'windows_mobile';
        } elseif (preg_match('/BlackBerry|BB10/i', $user_agent)) {
            return 'blackberry';
        } elseif (preg_match('/Windows/i', $user_agent)) {
            return 'windows';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $user_agent)) {
            return 'macos';
        } elseif (preg_match('/Linux/i', $user_agent)) {
            return 'linux';
        }
        
        return 'unknown';
    }

    /**
     * Detect connection type
     * 
     * @since 1.0.0
     * @return string Connection type
     */
    private function detect_connection_type() {
        
        // Check for Save-Data header (indicates slow connection)
        if (isset($_SERVER['HTTP_SAVE_DATA']) && $_SERVER['HTTP_SAVE_DATA'] === 'on') {
            return 'slow';
        }
        
        // Check for connection hint headers
        if (isset($_SERVER['HTTP_DOWNLINK'])) {
            $downlink = floatval($_SERVER['HTTP_DOWNLINK']);
            if ($downlink < 1.5) {
                return 'slow';
            } elseif ($downlink < 5.0) {
                return 'medium';
            } else {
                return 'fast';
            }
        }
        
        return 'unknown';
    }

    /**
     * Detect screen capabilities
     * 
     * @since 1.0.0
     * @return array Screen information
     */
    private function detect_screen_capabilities() {
        
        return array(
            'supports_webp' => $this->supports_webp(),
            'supports_avif' => $this->supports_avif(),
            'pixel_density' => $this->get_pixel_density(),
            'color_depth' => 'unknown', // Would need client-side detection
            'screen_size' => 'unknown', // Would need client-side detection
        );
    }

    /**
     * Check WebP support
     * 
     * @since 1.0.0
     * @return bool True if WebP is supported
     */
    private function supports_webp() {
        
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($accept, 'image/webp') !== false;
    }

    /**
     * Check AVIF support
     * 
     * @since 1.0.0
     * @return bool True if AVIF is supported
     */
    private function supports_avif() {
        
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($accept, 'image/avif') !== false;
    }

    /**
     * Get pixel density hint
     * 
     * @since 1.0.0
     * @return float Estimated pixel density
     */
    private function get_pixel_density() {
        
        // This is a rough estimate - real detection needs client-side JS
        if ($this->device_cache['platform'] === 'ios') {
            return 2.0; // Most iOS devices are Retina
        } elseif ($this->device_cache['platform'] === 'android') {
            return 1.5; // Average Android density
        }
        
        return 1.0; // Default density
    }

    /**
     * Output mobile meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_mobile_meta_tags() {
        
        if (!$this->mobile_settings['enable_mobile_optimization']) {
            return;
        }
        
        // Viewport meta tag
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=yes">' . "\n";
        
        // Mobile web app capable
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
        
        // Format detection
        echo '<meta name="format-detection" content="telephone=yes, email=yes, address=yes">' . "\n";
        
        // Disable automatic translation
        echo '<meta name="google" content="notranslate">' . "\n";
        
        // App title for mobile
        $app_title = get_theme_mod('recruitpro_mobile_app_title', get_bloginfo('name'));
        echo '<meta name="apple-mobile-web-app-title" content="' . esc_attr($app_title) . '">' . "\n";
        echo '<meta name="application-name" content="' . esc_attr($app_title) . '">' . "\n";
        
        // Mobile-specific meta for recruitment sites
        echo '<meta name="mobile-optimized" content="yes">' . "\n";
        echo '<meta name="recruitment-agency" content="mobile-ready">' . "\n";
    }

    /**
     * Add viewport meta tag
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_viewport_meta_tag() {
        
        // Already handled in output_mobile_meta_tags to avoid duplication
        // This method exists for potential customization
    }

    /**
     * Add mobile theme color
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_mobile_theme_color() {
        
        $theme_color = get_theme_mod('recruitpro_mobile_theme_color', get_theme_mod('recruitpro_primary_color', '#1e40af'));
        echo '<meta name="theme-color" content="' . esc_attr($theme_color) . '">' . "\n";
        echo '<meta name="msapplication-navbutton-color" content="' . esc_attr($theme_color) . '">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . esc_attr($theme_color) . '">' . "\n";
    }

    /**
     * Add touch icon support
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_touch_icon_support() {
        
        // Apple touch icons
        $touch_icon_sizes = array(57, 60, 72, 76, 114, 120, 144, 152, 180);
        
        foreach ($touch_icon_sizes as $size) {
            $icon_id = get_theme_mod('recruitpro_touch_icon_' . $size, '');
            if ($icon_id) {
                $icon_url = wp_get_attachment_image_url($icon_id, array($size, $size));
                if ($icon_url) {
                    echo '<link rel="apple-touch-icon" sizes="' . $size . 'x' . $size . '" href="' . esc_url($icon_url) . '">' . "\n";
                }
            }
        }
        
        // Fallback touch icon
        $default_touch_icon = get_theme_mod('recruitpro_default_touch_icon', '');
        if ($default_touch_icon) {
            $icon_url = wp_get_attachment_image_url($default_touch_icon, array(180, 180));
            if ($icon_url) {
                echo '<link rel="apple-touch-icon" href="' . esc_url($icon_url) . '">' . "\n";
            }
        }
        
        // Android/Chrome touch icons
        $android_icon_sizes = array(192, 512);
        
        foreach ($android_icon_sizes as $size) {
            $icon_id = get_theme_mod('recruitpro_android_icon_' . $size, '');
            if ($icon_id) {
                $icon_url = wp_get_attachment_image_url($icon_id, array($size, $size));
                if ($icon_url) {
                    echo '<link rel="icon" type="image/png" sizes="' . $size . 'x' . $size . '" href="' . esc_url($icon_url) . '">' . "\n";
                }
            }
        }
    }

    /**
     * Add PWA manifest
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_pwa_manifest() {
        
        $manifest_data = $this->generate_pwa_manifest();
        
        if ($manifest_data) {
            // Create manifest file
            $manifest_url = $this->create_manifest_file($manifest_data);
            
            if ($manifest_url) {
                echo '<link rel="manifest" href="' . esc_url($manifest_url) . '">' . "\n";
            }
        }
    }

    /**
     * Generate PWA manifest data
     * 
     * @since 1.0.0
     * @return array Manifest data
     */
    private function generate_pwa_manifest() {
        
        $app_name = get_theme_mod('recruitpro_pwa_app_name', get_bloginfo('name'));
        $app_description = get_theme_mod('recruitpro_pwa_description', get_bloginfo('description'));
        $theme_color = get_theme_mod('recruitpro_mobile_theme_color', '#1e40af');
        $background_color = get_theme_mod('recruitpro_pwa_background_color', '#ffffff');
        
        $manifest = array(
            'name' => $app_name,
            'short_name' => get_theme_mod('recruitpro_pwa_short_name', substr($app_name, 0, 12)),
            'description' => $app_description,
            'start_url' => home_url('/'),
            'display' => get_theme_mod('recruitpro_pwa_display', 'standalone'),
            'orientation' => get_theme_mod('recruitpro_pwa_orientation', 'portrait'),
            'theme_color' => $theme_color,
            'background_color' => $background_color,
            'scope' => home_url('/'),
            'categories' => array('recruitment', 'business', 'productivity'),
            'lang' => get_locale(),
            'dir' => is_rtl() ? 'rtl' : 'ltr',
            'icons' => $this->get_pwa_icons(),
        );
        
        return $manifest;
    }

    /**
     * Get PWA icons
     * 
     * @since 1.0.0
     * @return array Icon data
     */
    private function get_pwa_icons() {
        
        $icons = array();
        $icon_sizes = array(72, 96, 128, 144, 152, 192, 384, 512);
        
        foreach ($icon_sizes as $size) {
            $icon_id = get_theme_mod('recruitpro_pwa_icon_' . $size, '');
            if ($icon_id) {
                $icon_url = wp_get_attachment_image_url($icon_id, array($size, $size));
                if ($icon_url) {
                    $icons[] = array(
                        'src' => $icon_url,
                        'sizes' => $size . 'x' . $size,
                        'type' => 'image/png',
                        'purpose' => 'any maskable',
                    );
                }
            }
        }
        
        return $icons;
    }

    /**
     * Create manifest file
     * 
     * @since 1.0.0
     * @param array $manifest_data Manifest data
     * @return string|false Manifest URL or false on failure
     */
    private function create_manifest_file($manifest_data) {
        
        $upload_dir = wp_upload_dir();
        $manifest_dir = $upload_dir['basedir'] . '/recruitpro-pwa/';
        
        if (!file_exists($manifest_dir)) {
            wp_mkdir_p($manifest_dir);
        }
        
        $manifest_file = $manifest_dir . 'manifest.json';
        $manifest_json = wp_json_encode($manifest_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        if (file_put_contents($manifest_file, $manifest_json)) {
            return $upload_dir['baseurl'] . '/recruitpro-pwa/manifest.json';
        }
        
        return false;
    }

    /**
     * Enqueue PWA service worker
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_pwa_service_worker() {
        
        if (!$this->mobile_settings['pwa_enabled']) {
            return;
        }
        
        // Service worker registration script
        wp_enqueue_script(
            'recruitpro-pwa-sw',
            get_template_directory_uri() . '/assets/js/pwa-service-worker.js',
            array(),
            wp_get_theme()->get('Version'),
            true
        );
        
        wp_localize_script('recruitpro-pwa-sw', 'recruitproPWA', array(
            'swUrl' => get_template_directory_uri() . '/assets/js/sw.js',
            'offlineEnabled' => $this->mobile_settings['offline_support'],
            'cacheStrategy' => $this->mobile_settings['mobile_cache_strategy'],
            'version' => wp_get_theme()->get('Version'),
        ));
    }

    /**
     * Add mobile body classes
     * 
     * @since 1.0.0
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_mobile_body_classes($classes) {
        
        // Device type classes
        if ($this->device_cache['is_mobile']) {
            $classes[] = 'mobile-device';
        }
        
        if ($this->device_cache['is_tablet']) {
            $classes[] = 'tablet-device';
        }
        
        if ($this->device_cache['is_touch']) {
            $classes[] = 'touch-device';
        }
        
        // Platform classes
        if ($this->device_cache['platform'] !== 'unknown') {
            $classes[] = 'platform-' . $this->device_cache['platform'];
        }
        
        // Connection classes
        if ($this->device_cache['connection'] !== 'unknown') {
            $classes[] = 'connection-' . $this->device_cache['connection'];
        }
        
        // Mobile optimization classes
        if ($this->mobile_settings['enable_mobile_optimization']) {
            $classes[] = 'mobile-optimized';
        }
        
        if ($this->mobile_settings['touch_optimized']) {
            $classes[] = 'touch-optimized';
        }
        
        if ($this->mobile_settings['reduce_animations_mobile'] && $this->device_cache['is_mobile']) {
            $classes[] = 'reduced-motion';
        }
        
        // Performance mode classes
        $classes[] = 'mobile-performance-' . $this->mobile_settings['mobile_performance_mode'];
        
        return $classes;
    }

    /**
     * Enqueue mobile-specific assets
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_mobile_assets() {
        
        if (!$this->mobile_settings['enable_mobile_optimization']) {
            return;
        }
        
        $theme_version = wp_get_theme()->get('Version');
        $is_mobile = $this->device_cache['is_mobile'];
        $is_touch = $this->device_cache['is_touch'];
        
        // Mobile-specific CSS
        if ($is_mobile) {
            wp_enqueue_style(
                'recruitpro-mobile',
                get_template_directory_uri() . '/assets/css/mobile.css',
                array('recruitpro-main-style'),
                $theme_version,
                'all'
            );
        }
        
        // Touch-specific enhancements
        if ($is_touch && $this->mobile_settings['touch_optimized']) {
            wp_enqueue_style(
                'recruitpro-touch',
                get_template_directory_uri() . '/assets/css/touch.css',
                array('recruitpro-main-style'),
                $theme_version,
                'all'
            );
            
            wp_enqueue_script(
                'recruitpro-touch-gestures',
                get_template_directory_uri() . '/assets/js/touch-gestures.js',
                array('jquery'),
                $theme_version,
                true
            );
        }
        
        // Mobile performance mode assets
        if ($this->mobile_settings['mobile_performance_mode'] === 'performance') {
            wp_enqueue_script(
                'recruitpro-mobile-performance',
                get_template_directory_uri() . '/assets/js/mobile-performance.js',
                array('jquery'),
                $theme_version,
                true
            );
        }
        
        // Gesture support
        if ($this->mobile_settings['enable_gesture_support'] && $is_touch) {
            wp_enqueue_script(
                'recruitpro-gestures',
                get_template_directory_uri() . '/assets/js/gestures.js',
                array('jquery'),
                $theme_version,
                true
            );
        }
        
        // Mobile-specific JavaScript variables
        wp_localize_script('recruitpro-main', 'recruitproMobile', array(
            'isMobile' => $is_mobile,
            'isTablet' => $this->device_cache['is_tablet'],
            'isTouch' => $is_touch,
            'platform' => $this->device_cache['platform'],
            'connection' => $this->device_cache['connection'],
            'settings' => array(
                'touchOptimized' => $this->mobile_settings['touch_optimized'],
                'gesturesEnabled' => $this->mobile_settings['enable_gesture_support'],
                'performanceMode' => $this->mobile_settings['mobile_performance_mode'],
                'menuStyle' => $this->mobile_settings['mobile_menu_style'],
            ),
            'breakpoints' => array(
                'mobile' => 767,
                'tablet' => 1024,
                'desktop' => 1200,
            ),
        ));
    }

    /**
     * Output mobile-specific styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_mobile_styles() {
        
        if (!$this->mobile_settings['enable_mobile_optimization'] || !$this->device_cache['is_mobile']) {
            return;
        }
        
        $custom_css = '';
        
        // Performance mode styles
        if ($this->mobile_settings['mobile_performance_mode'] === 'performance') {
            $custom_css .= '
                .mobile-device * {
                    -webkit-transform: translateZ(0);
                    transform: translateZ(0);
                }
                .mobile-device .animation {
                    will-change: transform;
                }
            ';
        }
        
        // Reduced animations
        if ($this->mobile_settings['reduce_animations_mobile']) {
            $custom_css .= '
                .mobile-device * {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            ';
        }
        
        // Touch optimizations
        if ($this->mobile_settings['touch_optimized']) {
            $custom_css .= '
                .touch-device .btn,
                .touch-device .button,
                .touch-device input[type="submit"],
                .touch-device .touch-target {
                    min-height: 44px;
                    min-width: 44px;
                }
                .touch-device a {
                    -webkit-tap-highlight-color: rgba(0,0,0,0.1);
                }
            ';
        }
        
        // Connection-specific optimizations
        if ($this->device_cache['connection'] === 'slow') {
            $custom_css .= '
                .connection-slow .hero-background,
                .connection-slow .large-image {
                    background-image: none !important;
                }
                .connection-slow video {
                    display: none !important;
                }
            ';
        }
        
        if (!empty($custom_css)) {
            echo '<style id="recruitpro-mobile-optimization">' . $custom_css . '</style>' . "\n";
        }
    }

    /**
     * Output mobile scripts
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_mobile_scripts() {
        
        if (!$this->mobile_settings['enable_mobile_optimization'] || !$this->device_cache['is_mobile']) {
            return;
        }
        
        ?>
        <script>
        (function() {
            // Mobile device class toggle
            document.documentElement.classList.add('js-mobile');
            
            // Performance observer for mobile
            if ('PerformanceObserver' in window && window.recruitproMobile) {
                const observer = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.entryType === 'navigation') {
                            // Track mobile performance metrics
                            const metrics = {
                                loadTime: entry.loadEventEnd - entry.loadEventStart,
                                domContentLoaded: entry.domContentLoadedEventEnd - entry.domContentLoadedEventStart,
                                firstByte: entry.responseStart - entry.requestStart
                            };
                            
                            // Send metrics if debug mode is enabled
                            if (window.recruitproMobile.settings.debugMode) {
                                console.log('Mobile Performance Metrics:', metrics);
                            }
                        }
                    }
                });
                
                observer.observe({entryTypes: ['navigation']});
            }
            
            // Touch detection enhancement
            <?php if ($this->device_cache['is_touch']): ?>
            document.documentElement.classList.add('touch-enabled');
            
            // Add touch event listeners for enhanced interactions
            document.addEventListener('touchstart', function(e) {
                if (e.target.matches('.touch-feedback')) {
                    e.target.classList.add('touch-active');
                }
            }, {passive: true});
            
            document.addEventListener('touchend', function(e) {
                setTimeout(() => {
                    document.querySelectorAll('.touch-active').forEach(el => {
                        el.classList.remove('touch-active');
                    });
                }, 150);
            }, {passive: true});
            <?php endif; ?>
            
            // Connection-aware loading
            <?php if ($this->device_cache['connection'] === 'slow'): ?>
            document.documentElement.classList.add('slow-connection');
            
            // Defer non-critical images on slow connections
            document.querySelectorAll('img[data-src]').forEach(img => {
                if (!img.classList.contains('critical')) {
                    img.loading = 'lazy';
                }
            });
            <?php endif; ?>
            
        })();
        </script>
        <?php
    }

    /**
     * Add mobile preconnect hints
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_mobile_preconnect_hints() {
        
        if (!$this->device_cache['is_mobile']) {
            return;
        }
        
        // Essential preconnects for mobile
        $preconnects = array(
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
        );
        
        // Add CDN preconnects if configured
        $cdn_url = get_theme_mod('recruitpro_cdn_url', '');
        if ($cdn_url) {
            $preconnects[] = $cdn_url;
        }
        
        foreach ($preconnects as $url) {
            echo '<link rel="preconnect" href="' . esc_url($url) . '" crossorigin>' . "\n";
        }
        
        // DNS prefetch for external resources
        $dns_prefetch = array(
            '//www.google-analytics.com',
            '//www.googletagmanager.com',
        );
        
        foreach ($dns_prefetch as $url) {
            echo '<link rel="dns-prefetch" href="' . esc_url($url) . '">' . "\n";
        }
    }

    /**
     * Optimize mobile menu
     * 
     * @since 1.0.0
     * @param array $args Menu arguments
     * @return array Modified menu arguments
     */
    public function optimize_mobile_menu($args) {
        
        if (!$this->device_cache['is_mobile'] || !isset($args['theme_location'])) {
            return $args;
        }
        
        // Add mobile-specific classes
        if ($args['theme_location'] === 'primary') {
            $args['menu_class'] = ($args['menu_class'] ?? '') . ' mobile-optimized';
            $args['container_class'] = ($args['container_class'] ?? '') . ' mobile-menu-container';
            
            // Add mobile menu walker for enhanced functionality
            if (!isset($args['walker'])) {
                $args['walker'] = new RecruitPro_Mobile_Menu_Walker();
            }
        }
        
        return $args;
    }

    /**
     * Optimize mobile images
     * 
     * @since 1.0.0
     * @param array $attr Image attributes
     * @param WP_Post $attachment Attachment post object
     * @param string $size Image size
     * @return array Modified image attributes
     */
    public function optimize_mobile_images($attr, $attachment, $size) {
        
        if (!$this->device_cache['is_mobile']) {
            return $attr;
        }
        
        // Add mobile-specific loading attributes
        if ($this->mobile_settings['lazy_load_mobile']) {
            $attr['loading'] = 'lazy';
        }
        
        // Add mobile-optimized sizes
        if (!isset($attr['sizes']) && $this->device_cache['is_mobile']) {
            $attr['sizes'] = '(max-width: 767px) 100vw, (max-width: 1024px) 50vw, 25vw';
        }
        
        // Add mobile-specific classes
        $mobile_classes = array('mobile-optimized');
        
        if ($this->device_cache['connection'] === 'slow') {
            $mobile_classes[] = 'slow-connection';
        }
        
        if (isset($attr['class'])) {
            $attr['class'] .= ' ' . implode(' ', $mobile_classes);
        } else {
            $attr['class'] = implode(' ', $mobile_classes);
        }
        
        return $attr;
    }

    /**
     * Optimize mobile content
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Modified content
     */
    public function optimize_mobile_content($content) {
        
        if (!$this->device_cache['is_mobile'] || is_admin()) {
            return $content;
        }
        
        // Add mobile-friendly table wrapper
        $content = preg_replace('/<table(?![^>]*class[^>]*mobile-responsive)([^>]*)>/', '<div class="table-responsive"><table$1 class="mobile-responsive">', $content);
        $content = str_replace('</table>', '</table></div>', $content);
        
        // Optimize embedded videos for mobile
        $content = preg_replace_callback('/<iframe[^>]*(?:youtube|vimeo)[^>]*>.*?<\/iframe>/i', function($matches) {
            $iframe = $matches[0];
            if (strpos($iframe, 'mobile-responsive') === false) {
                $iframe = str_replace('<iframe', '<div class="video-responsive"><iframe', $iframe);
                $iframe = str_replace('</iframe>', '</iframe></div>', $iframe);
            }
            return $iframe;
        }, $content);
        
        return $content;
    }

    /**
     * Optimize mobile scripts
     * 
     * @since 1.0.0
     * @param string $tag Script tag
     * @param string $handle Script handle
     * @param string $src Script source
     * @return string Modified script tag
     */
    public function optimize_mobile_scripts($tag, $handle, $src) {
        
        if (!$this->device_cache['is_mobile']) {
            return $tag;
        }
        
        // Defer non-critical scripts on mobile
        $defer_scripts = array(
            'recruitpro-animations',
            'recruitpro-parallax',
            'recruitpro-video-background',
        );
        
        if (in_array($handle, $defer_scripts)) {
            $tag = str_replace(' src', ' defer src', $tag);
        }
        
        // Add async to analytics scripts
        $async_scripts = array(
            'google-analytics',
            'gtag',
            'facebook-pixel',
        );
        
        if (in_array($handle, $async_scripts)) {
            $tag = str_replace(' src', ' async src', $tag);
        }
        
        return $tag;
    }

    /**
     * Optimize mobile styles loading
     * 
     * @since 1.0.0
     * @param string $tag Style tag
     * @param string $handle Style handle
     * @param string $href Style source
     * @param string $media Media attribute
     * @return string Modified style tag
     */
    public function optimize_mobile_styles_loading($tag, $handle, $href, $media) {
        
        if (!$this->device_cache['is_mobile']) {
            return $tag;
        }
        
        // Defer non-critical CSS on mobile
        $defer_styles = array(
            'recruitpro-animations',
            'recruitpro-print',
        );
        
        if (in_array($handle, $defer_styles) && $media === 'all') {
            $tag = str_replace('rel="stylesheet"', 'rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $tag);
            $tag .= '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
        }
        
        return $tag;
    }

    /**
     * Optimize mobile comment form
     * 
     * @since 1.0.0
     * @param array $defaults Form defaults
     * @return array Modified defaults
     */
    public function optimize_mobile_comment_form($defaults) {
        
        if (!$this->device_cache['is_mobile']) {
            return $defaults;
        }
        
        // Add mobile-specific attributes
        $defaults['comment_field'] = str_replace('<textarea', '<textarea placeholder="' . esc_attr__('Your comment...', 'recruitpro') . '" rows="4"', $defaults['comment_field']);
        
        // Optimize form fields for mobile
        $mobile_field_attributes = 'autocomplete="on" autocapitalize="words"';
        
        foreach (array('author', 'email', 'url') as $field) {
            if (isset($defaults['fields'][$field])) {
                $defaults['fields'][$field] = str_replace('<input', '<input ' . $mobile_field_attributes, $defaults['fields'][$field]);
            }
        }
        
        return $defaults;
    }

    /**
     * Optimize mobile search form
     * 
     * @since 1.0.0
     * @param string $form Search form HTML
     * @return string Modified form
     */
    public function optimize_mobile_search_form($form) {
        
        if (!$this->device_cache['is_mobile']) {
            return $form;
        }
        
        // Add mobile-specific attributes
        $form = str_replace('<input type="search"', '<input type="search" autocomplete="on" autocapitalize="none" inputmode="search"', $form);
        $form = str_replace('class="search-field"', 'class="search-field mobile-optimized"', $form);
        
        return $form;
    }

    /**
     * Add mobile customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_mobile_customizer_options($wp_customize) {
        
        // Mobile Optimization Panel
        $wp_customize->add_panel('recruitpro_mobile_panel', array(
            'title' => __('Mobile Optimization', 'recruitpro'),
            'description' => __('Configure mobile-specific settings and optimizations.', 'recruitpro'),
            'priority' => 150,
            'capability' => 'edit_theme_options',
        ));

        // General Mobile Section
        $wp_customize->add_section('recruitpro_mobile_general', array(
            'title' => __('General Mobile Settings', 'recruitpro'),
            'panel' => 'recruitpro_mobile_panel',
            'priority' => 10,
        ));

        // Enable mobile optimization
        $wp_customize->add_setting('recruitpro_mobile_optimization', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_optimization', array(
            'label' => __('Enable Mobile Optimization', 'recruitpro'),
            'description' => __('Enable mobile-specific optimizations and enhancements.', 'recruitpro'),
            'section' => 'recruitpro_mobile_general',
            'type' => 'checkbox',
        ));

        // Touch optimization
        $wp_customize->add_setting('recruitpro_touch_optimized', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_touch_optimized', array(
            'label' => __('Touch-Friendly Interface', 'recruitpro'),
            'description' => __('Optimize buttons and interactions for touch devices.', 'recruitpro'),
            'section' => 'recruitpro_mobile_general',
            'type' => 'checkbox',
        ));

        // Mobile menu style
        $wp_customize->add_setting('recruitpro_mobile_menu_style', array(
            'default' => 'slide',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_menu_style', array(
            'label' => __('Mobile Menu Style', 'recruitpro'),
            'section' => 'recruitpro_mobile_general',
            'type' => 'select',
            'choices' => array(
                'slide' => __('Slide In', 'recruitpro'),
                'overlay' => __('Full Overlay', 'recruitpro'),
                'push' => __('Push Content', 'recruitpro'),
                'dropdown' => __('Dropdown', 'recruitpro'),
            ),
        ));

        // Performance Section
        $wp_customize->add_section('recruitpro_mobile_performance', array(
            'title' => __('Mobile Performance', 'recruitpro'),
            'panel' => 'recruitpro_mobile_panel',
            'priority' => 20,
        ));

        // Performance mode
        $wp_customize->add_setting('recruitpro_mobile_performance_mode', array(
            'default' => 'balanced',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_mobile_performance_mode', array(
            'label' => __('Mobile Performance Mode', 'recruitpro'),
            'section' => 'recruitpro_mobile_performance',
            'type' => 'select',
            'choices' => array(
                'performance' => __('Maximum Performance', 'recruitpro'),
                'balanced' => __('Balanced', 'recruitpro'),
                'quality' => __('Maximum Quality', 'recruitpro'),
            ),
        ));

        // Lazy loading
        $wp_customize->add_setting('recruitpro_lazy_load_mobile', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_lazy_load_mobile', array(
            'label' => __('Lazy Load Images on Mobile', 'recruitpro'),
            'description' => __('Improve mobile performance by lazy loading images.', 'recruitpro'),
            'section' => 'recruitpro_mobile_performance',
            'type' => 'checkbox',
        ));

        // Reduce animations
        $wp_customize->add_setting('recruitpro_reduce_animations_mobile', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_reduce_animations_mobile', array(
            'label' => __('Reduce Animations on Mobile', 'recruitpro'),
            'description' => __('Disable animations on mobile for better performance.', 'recruitpro'),
            'section' => 'recruitpro_mobile_performance',
            'type' => 'checkbox',
        ));

        // PWA Section
        $wp_customize->add_section('recruitpro_mobile_pwa', array(
            'title' => __('Progressive Web App', 'recruitpro'),
            'panel' => 'recruitpro_mobile_panel',
            'priority' => 30,
        ));

        // Enable PWA
        $wp_customize->add_setting('recruitpro_enable_pwa', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_pwa', array(
            'label' => __('Enable PWA Features', 'recruitpro'),
            'description' => __('Add Progressive Web App capabilities for mobile users.', 'recruitpro'),
            'section' => 'recruitpro_mobile_pwa',
            'type' => 'checkbox',
        ));

        // App name
        $wp_customize->add_setting('recruitpro_pwa_app_name', array(
            'default' => get_bloginfo('name'),
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_pwa_app_name', array(
            'label' => __('App Name', 'recruitpro'),
            'section' => 'recruitpro_mobile_pwa',
            'type' => 'text',
            'active_callback' => function() {
                return get_theme_mod('recruitpro_enable_pwa', false);
            },
        ));
    }

    /**
     * Admin mobile styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function admin_mobile_styles() {
        
        if (!$this->device_cache['is_mobile']) {
            return;
        }
        
        wp_add_inline_style('wp-admin', '
            @media (max-width: 767px) {
                .wp-admin .mobile-friendly {
                    padding: 15px;
                    font-size: 16px;
                }
                .wp-admin input, .wp-admin textarea, .wp-admin select {
                    min-height: 44px;
                }
            }
        ');
    }

    /**
     * Get mobile performance data
     * 
     * @since 1.0.0
     * @return void
     */
    public function get_mobile_performance_data() {
        
        wp_send_json_success(array(
            'device_info' => $this->device_cache,
            'settings' => $this->mobile_settings,
            'metrics' => $this->mobile_metrics,
        ));
    }

    /**
     * Check if current request is from mobile device
     * 
     * @since 1.0.0
     * @return bool True if mobile device
     */
    public function is_mobile() {
        
        return $this->device_cache['is_mobile'];
    }

    /**
     * Check if current request is from tablet device
     * 
     * @since 1.0.0
     * @return bool True if tablet device
     */
    public function is_tablet() {
        
        return $this->device_cache['is_tablet'];
    }

    /**
     * Check if current request is from touch device
     * 
     * @since 1.0.0
     * @return bool True if touch device
     */
    public function is_touch() {
        
        return $this->device_cache['is_touch'];
    }

    /**
     * Get device platform
     * 
     * @since 1.0.0
     * @return string Platform name
     */
    public function get_platform() {
        
        return $this->device_cache['platform'];
    }
}

/**
 * Mobile Menu Walker Class
 * 
 * Enhanced menu walker for mobile optimization
 *
 * @since 1.0.0
 */
class RecruitPro_Mobile_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Start the element output
     * 
     * @since 1.0.0
     * @param string $output Menu output
     * @param WP_Post $item Menu item
     * @param int $depth Menu depth
     * @param stdClass $args Menu arguments
     * @param int $id Menu item ID
     * @return void
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $classes[] = 'mobile-menu-item';
        
        if ($depth === 0) {
            $classes[] = 'mobile-menu-top-level';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        // Add mobile-specific attributes
        $attributes .= ' class="mobile-menu-link"';
        $attributes .= ' role="menuitem"';
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

// Initialize mobile optimization
new RecruitPro_Mobile_Optimization();

/**
 * Helper Functions
 */

/**
 * Check if current request is from mobile device
 * 
 * @since 1.0.0
 * @return bool True if mobile device
 */
function recruitpro_is_mobile() {
    
    static $mobile_optimization = null;
    
    if (is_null($mobile_optimization)) {
        $mobile_optimization = new RecruitPro_Mobile_Optimization();
    }
    
    return $mobile_optimization->is_mobile();
}

/**
 * Check if current request is from touch device
 * 
 * @since 1.0.0
 * @return bool True if touch device
 */
function recruitpro_is_touch() {
    
    static $mobile_optimization = null;
    
    if (is_null($mobile_optimization)) {
        $mobile_optimization = new RecruitPro_Mobile_Optimization();
    }
    
    return $mobile_optimization->is_touch();
}

/**
 * Get current device platform
 * 
 * @since 1.0.0
 * @return string Platform name
 */
function recruitpro_get_platform() {
    
    static $mobile_optimization = null;
    
    if (is_null($mobile_optimization)) {
        $mobile_optimization = new RecruitPro_Mobile_Optimization();
    }
    
    return $mobile_optimization->get_platform();
}

/**
 * Add mobile-optimized CSS class
 * 
 * @since 1.0.0
 * @param string $classes Existing classes
 * @return string Modified classes
 */
function recruitpro_mobile_css_class($classes = '') {
    
    if (recruitpro_is_mobile()) {
        $classes .= ' mobile-optimized';
    }
    
    if (recruitpro_is_touch()) {
        $classes .= ' touch-optimized';
    }
    
    return trim($classes);
}

?>