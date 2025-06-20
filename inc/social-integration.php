<?php
/**
 * RecruitPro Theme Social Media Integration
 *
 * This file handles basic social media integration for the RecruitPro recruitment
 * website theme. It includes Open Graph meta tags, Twitter Cards, social sharing,
 * social login preparation, and recruitment-specific social features. Works at
 * theme level without conflicting with advanced social media plugins.
 *
 * @package RecruitPro
 * @subpackage Theme/Social
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/social-integration.php
 * Purpose: Basic social media integration and Open Graph support
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Social Integration Class
 * 
 * Handles all theme-level social media functionality including meta tags,
 * sharing, social profiles, and recruitment-specific features.
 *
 * @since 1.0.0
 */
class RecruitPro_Social_Integration {

    /**
     * Supported social networks for recruitment agencies
     * 
     * @since 1.0.0
     * @var array
     */
    private $social_networks = array();

    /**
     * Social sharing platforms
     * 
     * @since 1.0.0
     * @var array
     */
    private $sharing_platforms = array();

    /**
     * Open Graph configuration
     * 
     * @since 1.0.0
     * @var array
     */
    private $og_config = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_social_networks();
        $this->init_sharing_platforms();
        $this->init_og_config();
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Meta tags and Open Graph
        add_action('wp_head', array($this, 'output_open_graph_tags'), 5);
        add_action('wp_head', array($this, 'output_twitter_card_tags'), 6);
        add_action('wp_head', array($this, 'output_social_meta_tags'), 7);
        
        // Social sharing
        add_filter('the_content', array($this, 'add_social_sharing_buttons'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_social_scripts'));
        
        // Customizer integration
        add_action('customize_register', array($this, 'register_customizer_options'));
        
        // Widget registration
        add_action('widgets_init', array($this, 'register_social_widgets'));
        
        // Schema.org markup
        add_action('wp_footer', array($this, 'output_social_schema'));
        
        // Social login preparation
        add_action('login_form', array($this, 'add_social_login_buttons'));
        add_action('wp_ajax_recruitpro_social_login_prep', array($this, 'handle_social_login_prep'));
        add_action('wp_ajax_nopriv_recruitpro_social_login_prep', array($this, 'handle_social_login_prep'));
        
        // Content filters for social optimization
        add_filter('document_title_parts', array($this, 'optimize_social_title'));
        add_filter('get_the_excerpt', array($this, 'optimize_social_excerpt'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_social_admin_page'));
        add_action('wp_ajax_recruitpro_test_social_sharing', array($this, 'test_social_sharing'));
    }

    /**
     * Initialize social networks configuration
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_social_networks() {
        $this->social_networks = array(
            'linkedin' => array(
                'name' => __('LinkedIn', 'recruitpro'),
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0a66c2',
                'priority' => 1, // Highest priority for recruitment
                'description' => __('Professional networking platform', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => true,
                'login_support' => true,
                'business_focus' => true,
            ),
            'facebook' => array(
                'name' => __('Facebook', 'recruitpro'),
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877f2',
                'priority' => 2,
                'description' => __('Social networking platform', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => true,
                'login_support' => true,
                'business_focus' => true,
            ),
            'twitter' => array(
                'name' => __('Twitter/X', 'recruitpro'),
                'icon' => 'fab fa-twitter',
                'color' => '#1da1f2',
                'priority' => 3,
                'description' => __('Microblogging platform', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => true,
                'login_support' => false,
                'business_focus' => true,
            ),
            'instagram' => array(
                'name' => __('Instagram', 'recruitpro'),
                'icon' => 'fab fa-instagram',
                'color' => '#e4405f',
                'priority' => 4,
                'description' => __('Photo and video sharing', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => false,
                'login_support' => false,
                'business_focus' => true,
            ),
            'youtube' => array(
                'name' => __('YouTube', 'recruitpro'),
                'icon' => 'fab fa-youtube',
                'color' => '#ff0000',
                'priority' => 5,
                'description' => __('Video sharing platform', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => false,
                'login_support' => false,
                'business_focus' => true,
            ),
            'tiktok' => array(
                'name' => __('TikTok', 'recruitpro'),
                'icon' => 'fab fa-tiktok',
                'color' => '#000000',
                'priority' => 6,
                'description' => __('Short-form video platform', 'recruitpro'),
                'api_support' => false,
                'sharing_support' => false,
                'login_support' => false,
                'business_focus' => false,
            ),
            'glassdoor' => array(
                'name' => __('Glassdoor', 'recruitpro'),
                'icon' => 'fas fa-building',
                'color' => '#0caa41',
                'priority' => 7,
                'description' => __('Employee reviews and job listings', 'recruitpro'),
                'api_support' => false,
                'sharing_support' => false,
                'login_support' => false,
                'business_focus' => true,
            ),
            'indeed' => array(
                'name' => __('Indeed', 'recruitpro'),
                'icon' => 'fas fa-briefcase',
                'color' => '#2557a7',
                'priority' => 8,
                'description' => __('Job search platform', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => false,
                'login_support' => false,
                'business_focus' => true,
            ),
            'xing' => array(
                'name' => __('Xing', 'recruitpro'),
                'icon' => 'fab fa-xing',
                'color' => '#026466',
                'priority' => 9,
                'description' => __('Professional networking (Europe)', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => true,
                'login_support' => true,
                'business_focus' => true,
            ),
            'pinterest' => array(
                'name' => __('Pinterest', 'recruitpro'),
                'icon' => 'fab fa-pinterest-p',
                'color' => '#bd081c',
                'priority' => 10,
                'description' => __('Visual discovery platform', 'recruitpro'),
                'api_support' => true,
                'sharing_support' => true,
                'login_support' => false,
                'business_focus' => false,
            ),
        );
    }

    /**
     * Initialize sharing platforms
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_sharing_platforms() {
        $this->sharing_platforms = array(
            'linkedin' => array(
                'name' => __('Share on LinkedIn', 'recruitpro'),
                'url_template' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0a66c2',
                'supports_text' => false,
                'recruitment_priority' => 1,
            ),
            'facebook' => array(
                'name' => __('Share on Facebook', 'recruitpro'),
                'url_template' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877f2',
                'supports_text' => false,
                'recruitment_priority' => 2,
            ),
            'twitter' => array(
                'name' => __('Share on Twitter', 'recruitpro'),
                'url_template' => 'https://twitter.com/intent/tweet?url={url}&text={text}',
                'icon' => 'fab fa-twitter',
                'color' => '#1da1f2',
                'supports_text' => true,
                'recruitment_priority' => 3,
            ),
            'email' => array(
                'name' => __('Share via Email', 'recruitpro'),
                'url_template' => 'mailto:?subject={title}&body={text}%20{url}',
                'icon' => 'fas fa-envelope',
                'color' => '#6c757d',
                'supports_text' => true,
                'recruitment_priority' => 4,
            ),
            'whatsapp' => array(
                'name' => __('Share on WhatsApp', 'recruitpro'),
                'url_template' => 'https://wa.me/?text={text}%20{url}',
                'icon' => 'fab fa-whatsapp',
                'color' => '#25d366',
                'supports_text' => true,
                'recruitment_priority' => 5,
            ),
            'copy_link' => array(
                'name' => __('Copy Link', 'recruitpro'),
                'url_template' => '{url}',
                'icon' => 'fas fa-link',
                'color' => '#6c757d',
                'supports_text' => false,
                'recruitment_priority' => 6,
                'requires_js' => true,
            ),
        );
    }

    /**
     * Initialize Open Graph configuration
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_og_config() {
        $this->og_config = array(
            'site_name' => get_bloginfo('name'),
            'default_type' => 'website',
            'default_image' => get_theme_mod('recruitpro_default_og_image', ''),
            'image_width' => 1200,
            'image_height' => 630,
            'locale' => get_locale(),
            'enable_auto_tags' => get_theme_mod('recruitpro_auto_og_tags', true),
            'enable_job_specific' => get_theme_mod('recruitpro_job_og_tags', true),
        );
    }

    /**
     * Register customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function register_customizer_options($wp_customize) {

        // =================================================================
        // SOCIAL MEDIA PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_social_panel', array(
            'title' => __('Social Media', 'recruitpro'),
            'description' => __('Configure social media integration for your recruitment website.', 'recruitpro'),
            'priority' => 150,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // SOCIAL PROFILES SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_social_profiles', array(
            'title' => __('Social Media Profiles', 'recruitpro'),
            'description' => __('Add your social media profile URLs.', 'recruitpro'),
            'panel' => 'recruitpro_social_panel',
            'priority' => 10,
        ));

        // Add settings for each social network
        foreach ($this->social_networks as $network_id => $network) {
            $wp_customize->add_setting('recruitpro_social_' . $network_id, array(
                'default' => '',
                'sanitize_callback' => 'esc_url_raw',
                'transport' => 'refresh',
            ));

            $wp_customize->add_control('recruitpro_social_' . $network_id, array(
                'label' => sprintf(__('%s URL', 'recruitpro'), $network['name']),
                'description' => $network['description'],
                'section' => 'recruitpro_social_profiles',
                'type' => 'url',
            ));
        }

        // =================================================================
        // OPEN GRAPH SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_open_graph', array(
            'title' => __('Open Graph & Meta Tags', 'recruitpro'),
            'description' => __('Configure Open Graph and social media meta tags.', 'recruitpro'),
            'panel' => 'recruitpro_social_panel',
            'priority' => 20,
        ));

        // Enable Open Graph
        $wp_customize->add_setting('recruitpro_enable_open_graph', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_open_graph', array(
            'label' => __('Enable Open Graph Tags', 'recruitpro'),
            'description' => __('Automatically generate Open Graph meta tags for social sharing.', 'recruitpro'),
            'section' => 'recruitpro_open_graph',
            'type' => 'checkbox',
        ));

        // Default OG Image
        $wp_customize->add_setting('recruitpro_default_og_image', array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'recruitpro_default_og_image', array(
            'label' => __('Default Social Sharing Image', 'recruitpro'),
            'description' => __('Image to use when no specific image is available (1200x630 recommended).', 'recruitpro'),
            'section' => 'recruitpro_open_graph',
        )));

        // Twitter Card Type
        $wp_customize->add_setting('recruitpro_twitter_card_type', array(
            'default' => 'summary_large_image',
            'sanitize_callback' => array($this, 'sanitize_twitter_card_type'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_twitter_card_type', array(
            'label' => __('Twitter Card Type', 'recruitpro'),
            'description' => __('Choose the Twitter Card format.', 'recruitpro'),
            'section' => 'recruitpro_open_graph',
            'type' => 'select',
            'choices' => array(
                'summary' => __('Summary', 'recruitpro'),
                'summary_large_image' => __('Summary with Large Image', 'recruitpro'),
            ),
        ));

        // =================================================================
        // SOCIAL SHARING SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_social_sharing', array(
            'title' => __('Social Sharing', 'recruitpro'),
            'description' => __('Configure social sharing buttons and behavior.', 'recruitpro'),
            'panel' => 'recruitpro_social_panel',
            'priority' => 30,
        ));

        // Enable Social Sharing
        $wp_customize->add_setting('recruitpro_enable_social_sharing', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_social_sharing', array(
            'label' => __('Enable Social Sharing Buttons', 'recruitpro'),
            'description' => __('Add social sharing buttons to posts and pages.', 'recruitpro'),
            'section' => 'recruitpro_social_sharing',
            'type' => 'checkbox',
        ));

        // Sharing Button Style
        $wp_customize->add_setting('recruitpro_sharing_button_style', array(
            'default' => 'icon_text',
            'sanitize_callback' => array($this, 'sanitize_sharing_style'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sharing_button_style', array(
            'label' => __('Sharing Button Style', 'recruitpro'),
            'description' => __('Choose how sharing buttons appear.', 'recruitpro'),
            'section' => 'recruitpro_social_sharing',
            'type' => 'select',
            'choices' => array(
                'icon_only' => __('Icon Only', 'recruitpro'),
                'text_only' => __('Text Only', 'recruitpro'),
                'icon_text' => __('Icon + Text', 'recruitpro'),
            ),
        ));

        // Sharing Platforms
        $wp_customize->add_setting('recruitpro_sharing_platforms', array(
            'default' => 'linkedin,facebook,twitter,email',
            'sanitize_callback' => array($this, 'sanitize_sharing_platforms'),
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sharing_platforms', array(
            'label' => __('Sharing Platforms', 'recruitpro'),
            'description' => __('Select which platforms to include in sharing buttons.', 'recruitpro'),
            'section' => 'recruitpro_social_sharing',
            'type' => 'textarea',
        ));

        // Show on Job Posts
        $wp_customize->add_setting('recruitpro_sharing_on_jobs', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_sharing_on_jobs', array(
            'label' => __('Show Sharing on Job Posts', 'recruitpro'),
            'description' => __('Display sharing buttons on individual job listings.', 'recruitpro'),
            'section' => 'recruitpro_social_sharing',
            'type' => 'checkbox',
        ));

        // =================================================================
        // SOCIAL LOGIN SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_social_login', array(
            'title' => __('Social Login (Preparation)', 'recruitpro'),
            'description' => __('Prepare social login features for future plugin integration.', 'recruitpro'),
            'panel' => 'recruitpro_social_panel',
            'priority' => 40,
        ));

        // Enable Social Login Prep
        $wp_customize->add_setting('recruitpro_enable_social_login_prep', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_social_login_prep', array(
            'label' => __('Prepare Social Login Interface', 'recruitpro'),
            'description' => __('Add social login buttons (requires plugin for functionality).', 'recruitpro'),
            'section' => 'recruitpro_social_login',
            'type' => 'checkbox',
        ));
    }

    /**
     * Output Open Graph tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_open_graph_tags() {
        if (!get_theme_mod('recruitpro_enable_open_graph', true) || $this->seo_plugin_active()) {
            return;
        }

        $og_type = $this->get_og_type();
        $og_title = $this->get_og_title();
        $og_description = $this->get_og_description();
        $og_url = $this->get_og_url();
        $og_image = $this->get_og_image();
        $og_site_name = $this->og_config['site_name'];
        $og_locale = $this->og_config['locale'];

        echo "<!-- Open Graph Meta Tags by RecruitPro Theme -->\n";
        echo '<meta property="og:type" content="' . esc_attr($og_type) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($og_title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($og_description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($og_url) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr($og_site_name) . '">' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr($og_locale) . '">' . "\n";

        if ($og_image) {
            echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
            echo '<meta property="og:image:width" content="' . esc_attr($this->og_config['image_width']) . '">' . "\n";
            echo '<meta property="og:image:height" content="' . esc_attr($this->og_config['image_height']) . '">' . "\n";
        }

        // Job-specific Open Graph tags
        if (is_singular('job') && get_theme_mod('recruitpro_job_og_tags', true)) {
            $this->output_job_og_tags();
        }

        // Company-specific tags
        if (is_page_template('page-about.php') || is_page('about')) {
            $this->output_company_og_tags();
        }
    }

    /**
     * Output Twitter Card tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_twitter_card_tags() {
        if (!get_theme_mod('recruitpro_enable_open_graph', true) || $this->seo_plugin_active()) {
            return;
        }

        $card_type = get_theme_mod('recruitpro_twitter_card_type', 'summary_large_image');
        $title = $this->get_og_title();
        $description = $this->get_og_description();
        $image = $this->get_og_image();
        $twitter_handle = get_theme_mod('recruitpro_social_twitter', '');

        echo "<!-- Twitter Card Meta Tags by RecruitPro Theme -->\n";
        echo '<meta name="twitter:card" content="' . esc_attr($card_type) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";

        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }

        if ($twitter_handle) {
            $twitter_username = $this->extract_twitter_username($twitter_handle);
            if ($twitter_username) {
                echo '<meta name="twitter:site" content="@' . esc_attr($twitter_username) . '">' . "\n";
            }
        }
    }

    /**
     * Output additional social meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_social_meta_tags() {
        if ($this->seo_plugin_active()) {
            return;
        }

        // LinkedIn specific tags
        $linkedin_url = get_theme_mod('recruitpro_social_linkedin', '');
        if ($linkedin_url) {
            echo '<meta property="article:publisher" content="' . esc_url($linkedin_url) . '">' . "\n";
        }

        // Facebook app ID (if available)
        $facebook_app_id = get_theme_mod('recruitpro_facebook_app_id', '');
        if ($facebook_app_id) {
            echo '<meta property="fb:app_id" content="' . esc_attr($facebook_app_id) . '">' . "\n";
        }

        // Pinterest verification
        $pinterest_verification = get_theme_mod('recruitpro_pinterest_verification', '');
        if ($pinterest_verification) {
            echo '<meta name="p:domain_verify" content="' . esc_attr($pinterest_verification) . '">' . "\n";
        }
    }

    /**
     * Add social sharing buttons to content
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Modified content with sharing buttons
     */
    public function add_social_sharing_buttons($content) {
        if (!get_theme_mod('recruitpro_enable_social_sharing', true) || is_admin() || is_feed()) {
            return $content;
        }

        // Check if we should show sharing buttons
        if (!$this->should_show_sharing_buttons()) {
            return $content;
        }

        $sharing_buttons = $this->get_sharing_buttons();
        
        // Add sharing buttons after content
        $content .= '<div class="recruitpro-social-sharing">';
        $content .= '<h4 class="sharing-title">' . __('Share this:', 'recruitpro') . '</h4>';
        $content .= '<div class="sharing-buttons">' . $sharing_buttons . '</div>';
        $content .= '</div>';

        return $content;
    }

    /**
     * Get sharing buttons HTML
     * 
     * @since 1.0.0
     * @return string Sharing buttons HTML
     */
    private function get_sharing_buttons() {
        $platforms = get_theme_mod('recruitpro_sharing_platforms', 'linkedin,facebook,twitter,email');
        $platform_list = array_map('trim', explode(',', $platforms));
        $button_style = get_theme_mod('recruitpro_sharing_button_style', 'icon_text');
        
        $buttons = '';
        $post_url = get_permalink();
        $post_title = get_the_title();
        $post_excerpt = get_the_excerpt();

        foreach ($platform_list as $platform) {
            if (!isset($this->sharing_platforms[$platform])) {
                continue;
            }

            $platform_data = $this->sharing_platforms[$platform];
            $share_url = $this->build_sharing_url($platform, $post_url, $post_title, $post_excerpt);

            $button_text = '';
            $button_icon = '';

            if ($button_style === 'text_only' || $button_style === 'icon_text') {
                $button_text = '<span class="share-text">' . esc_html($platform_data['name']) . '</span>';
            }

            if ($button_style === 'icon_only' || $button_style === 'icon_text') {
                $button_icon = '<i class="' . esc_attr($platform_data['icon']) . '" aria-hidden="true"></i>';
            }

            $button_class = 'share-button share-' . $platform;
            if (isset($platform_data['requires_js']) && $platform_data['requires_js']) {
                $button_class .= ' requires-js';
            }

            $buttons .= sprintf(
                '<a href="%s" class="%s" style="--share-color: %s" target="_blank" rel="noopener" data-platform="%s" data-url="%s">%s%s</a>',
                esc_url($share_url),
                esc_attr($button_class),
                esc_attr($platform_data['color']),
                esc_attr($platform),
                esc_url($post_url),
                $button_icon,
                $button_text
            );
        }

        return $buttons;
    }

    /**
     * Build sharing URL for platform
     * 
     * @since 1.0.0
     * @param string $platform Platform ID
     * @param string $url Post URL
     * @param string $title Post title
     * @param string $text Post excerpt
     * @return string Sharing URL
     */
    private function build_sharing_url($platform, $url, $title, $text) {
        if (!isset($this->sharing_platforms[$platform])) {
            return '#';
        }

        $platform_data = $this->sharing_platforms[$platform];
        $share_url = $platform_data['url_template'];

        // Replace placeholders
        $replacements = array(
            '{url}' => urlencode($url),
            '{title}' => urlencode($title),
            '{text}' => urlencode($text),
        );

        foreach ($replacements as $placeholder => $value) {
            $share_url = str_replace($placeholder, $value, $share_url);
        }

        return $share_url;
    }

    /**
     * Enqueue social scripts and styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_social_scripts() {
        // Social sharing styles
        wp_enqueue_style(
            'recruitpro-social-sharing',
            get_template_directory_uri() . '/assets/css/social-sharing.css',
            array(),
            RECRUITPRO_THEME_VERSION
        );

        // Social sharing JavaScript
        wp_enqueue_script(
            'recruitpro-social-sharing',
            get_template_directory_uri() . '/assets/js/social-sharing.js',
            array('jquery'),
            RECRUITPRO_THEME_VERSION,
            true
        );

        // Pass data to JavaScript
        wp_localize_script('recruitpro-social-sharing', 'recruitproSocial', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_social_nonce'),
            'copyText' => __('Link copied to clipboard!', 'recruitpro'),
            'shareText' => __('Share this', 'recruitpro'),
        ));
    }

    /**
     * Register social widgets
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_social_widgets() {
        require_once get_template_directory() . '/inc/widgets/class-social-profiles-widget.php';
        require_once get_template_directory() . '/inc/widgets/class-social-follow-widget.php';
        
        register_widget('RecruitPro_Social_Profiles_Widget');
        register_widget('RecruitPro_Social_Follow_Widget');
    }

    /**
     * Output social schema markup
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_social_schema() {
        $social_profiles = $this->get_active_social_profiles();
        
        if (empty($social_profiles)) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'sameAs' => $social_profiles,
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }

    /**
     * Add social login buttons preparation
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_social_login_buttons() {
        if (!get_theme_mod('recruitpro_enable_social_login_prep', false)) {
            return;
        }

        echo '<div class="recruitpro-social-login-prep">';
        echo '<p class="social-login-notice">' . __('Social login will be available when the social media plugin is activated.', 'recruitpro') . '</p>';
        echo '<div class="social-login-buttons-placeholder">';
        
        $login_platforms = array('linkedin', 'facebook', 'google');
        foreach ($login_platforms as $platform) {
            if (isset($this->social_networks[$platform]) && $this->social_networks[$platform]['login_support']) {
                $network = $this->social_networks[$platform];
                echo '<button type="button" class="button social-login-prep" disabled>';
                echo '<i class="' . esc_attr($network['icon']) . '"></i> ';
                echo sprintf(__('Continue with %s', 'recruitpro'), $network['name']);
                echo '</button>';
            }
        }
        
        echo '</div>';
        echo '</div>';
    }

    /**
     * Add social admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_social_admin_page() {
        add_theme_page(
            __('Social Media Settings', 'recruitpro'),
            __('Social Media', 'recruitpro'),
            'manage_options',
            'recruitpro-social-settings',
            array($this, 'render_social_admin_page')
        );
    }

    /**
     * Render social admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function render_social_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('RecruitPro Social Media Settings', 'recruitpro'); ?></h1>
            
            <div class="social-admin-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#profiles" class="nav-tab nav-tab-active"><?php _e('Social Profiles', 'recruitpro'); ?></a>
                    <a href="#sharing" class="nav-tab"><?php _e('Social Sharing', 'recruitpro'); ?></a>
                    <a href="#testing" class="nav-tab"><?php _e('Testing Tools', 'recruitpro'); ?></a>
                </nav>
                
                <div id="profiles" class="tab-content active">
                    <h2><?php _e('Social Media Profiles', 'recruitpro'); ?></h2>
                    <p><?php _e('Configure your social media profiles for recruitment networking.', 'recruitpro'); ?></p>
                    
                    <?php $this->render_social_profiles_table(); ?>
                </div>
                
                <div id="sharing" class="tab-content">
                    <h2><?php _e('Social Sharing Configuration', 'recruitpro'); ?></h2>
                    <p><?php _e('Test and configure social sharing functionality.', 'recruitpro'); ?></p>
                    
                    <?php $this->render_sharing_test_tools(); ?>
                </div>
                
                <div id="testing" class="tab-content">
                    <h2><?php _e('Social Media Testing Tools', 'recruitpro'); ?></h2>
                    
                    <?php $this->render_social_testing_tools(); ?>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.nav-tab').click(function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
        });
        </script>
        <?php
    }

    /**
     * Render social profiles table
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_social_profiles_table() {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Platform', 'recruitpro'); ?></th>
                    <th><?php _e('Current URL', 'recruitpro'); ?></th>
                    <th><?php _e('Status', 'recruitpro'); ?></th>
                    <th><?php _e('Business Focus', 'recruitpro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->social_networks as $network_id => $network): ?>
                    <?php 
                    $url = get_theme_mod('recruitpro_social_' . $network_id, '');
                    $status = !empty($url) ? __('Configured', 'recruitpro') : __('Not Set', 'recruitpro');
                    $status_class = !empty($url) ? 'status-configured' : 'status-empty';
                    ?>
                    <tr>
                        <td>
                            <i class="<?php echo esc_attr($network['icon']); ?>" style="color: <?php echo esc_attr($network['color']); ?>"></i>
                            <strong><?php echo esc_html($network['name']); ?></strong>
                            <br><small><?php echo esc_html($network['description']); ?></small>
                        </td>
                        <td>
                            <?php if ($url): ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html($url); ?>
                                </a>
                            <?php else: ?>
                                <em><?php _e('No URL configured', 'recruitpro'); ?></em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-indicator <?php echo esc_attr($status_class); ?>">
                                <?php echo esc_html($status); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($network['business_focus']): ?>
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                <?php _e('Recommended', 'recruitpro'); ?>
                            <?php else: ?>
                                <span class="dashicons dashicons-minus" style="color: #ddd;"></span>
                                <?php _e('Optional', 'recruitpro'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p>
            <a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=recruitpro_social_profiles')); ?>" 
               class="button button-primary">
                <?php _e('Configure Social Profiles', 'recruitpro'); ?>
            </a>
        </p>
        <?php
    }

    /**
     * Render sharing test tools
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_sharing_test_tools() {
        ?>
        <div class="sharing-test-tools">
            <h3><?php _e('Test Social Sharing', 'recruitpro'); ?></h3>
            <p><?php _e('Test how your content appears when shared on social media.', 'recruitpro'); ?></p>
            
            <form id="social-sharing-test">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('URL to Test', 'recruitpro'); ?></th>
                        <td>
                            <input type="url" id="test-url" class="regular-text" 
                                   value="<?php echo esc_url(home_url()); ?>" 
                                   placeholder="<?php _e('Enter URL to test', 'recruitpro'); ?>">
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="button" id="test-sharing" class="button button-primary">
                        <?php _e('Test Sharing', 'recruitpro'); ?>
                    </button>
                </p>
            </form>
            
            <div id="sharing-test-results" style="display: none;">
                <h4><?php _e('Sharing Preview', 'recruitpro'); ?></h4>
                <div id="sharing-preview-content"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Render social testing tools
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_social_testing_tools() {
        ?>
        <div class="social-testing-tools">
            <h3><?php _e('External Validation Tools', 'recruitpro'); ?></h3>
            <p><?php _e('Use these external tools to validate your social media integration.', 'recruitpro'); ?></p>
            
            <div class="testing-tools-grid">
                <div class="testing-tool">
                    <h4><?php _e('Facebook Sharing Debugger', 'recruitpro'); ?></h4>
                    <p><?php _e('Test how your URLs appear when shared on Facebook.', 'recruitpro'); ?></p>
                    <a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener" class="button">
                        <?php _e('Open Facebook Debugger', 'recruitpro'); ?>
                    </a>
                </div>
                
                <div class="testing-tool">
                    <h4><?php _e('LinkedIn Post Inspector', 'recruitpro'); ?></h4>
                    <p><?php _e('Validate Open Graph tags for LinkedIn sharing.', 'recruitpro'); ?></p>
                    <a href="https://www.linkedin.com/post-inspector/" target="_blank" rel="noopener" class="button">
                        <?php _e('Open LinkedIn Inspector', 'recruitpro'); ?>
                    </a>
                </div>
                
                <div class="testing-tool">
                    <h4><?php _e('Twitter Card Validator', 'recruitpro'); ?></h4>
                    <p><?php _e('Preview and validate Twitter Cards.', 'recruitpro'); ?></p>
                    <a href="https://cards-dev.twitter.com/validator" target="_blank" rel="noopener" class="button">
                        <?php _e('Open Twitter Validator', 'recruitpro'); ?>
                    </a>
                </div>
                
                <div class="testing-tool">
                    <h4><?php _e('Open Graph Checker', 'recruitpro'); ?></h4>
                    <p><?php _e('General Open Graph tags validation.', 'recruitpro'); ?></p>
                    <a href="https://opengraphcheck.com/" target="_blank" rel="noopener" class="button">
                        <?php _e('Open OG Checker', 'recruitpro'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Helper methods and utilities
     */

    /**
     * Check if SEO plugin is active
     * 
     * @since 1.0.0
     * @return bool True if SEO plugin is handling meta tags
     */
    private function seo_plugin_active() {
        return (
            class_exists('RecruitPro_SEO') ||
            class_exists('WPSEO_Frontend') ||
            class_exists('RankMath\\Frontend\\Frontend') ||
            function_exists('aioseo')
        );
    }

    /**
     * Get Open Graph type
     * 
     * @since 1.0.0
     * @return string OG type
     */
    private function get_og_type() {
        if (is_singular('job')) {
            return 'article';
        } elseif (is_singular()) {
            return 'article';
        } else {
            return 'website';
        }
    }

    /**
     * Get Open Graph title
     * 
     * @since 1.0.0
     * @return string OG title
     */
    private function get_og_title() {
        if (is_singular()) {
            return get_the_title();
        } elseif (is_home()) {
            return get_bloginfo('name');
        } else {
            return wp_get_document_title();
        }
    }

    /**
     * Get Open Graph description
     * 
     * @since 1.0.0
     * @return string OG description
     */
    private function get_og_description() {
        if (is_singular() && has_excerpt()) {
            return get_the_excerpt();
        } elseif (is_singular()) {
            $content = get_the_content();
            return wp_trim_words(strip_tags($content), 55);
        } else {
            return get_bloginfo('description');
        }
    }

    /**
     * Get Open Graph URL
     * 
     * @since 1.0.0
     * @return string OG URL
     */
    private function get_og_url() {
        global $wp;
        return home_url($wp->request);
    }

    /**
     * Get Open Graph image
     * 
     * @since 1.0.0
     * @return string OG image URL
     */
    private function get_og_image() {
        // Featured image for posts
        if (is_singular() && has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image = wp_get_attachment_image_src($image_id, 'large');
            if ($image) {
                return $image[0];
            }
        }

        // Default OG image
        $default_image = get_theme_mod('recruitpro_default_og_image', '');
        if ($default_image) {
            return $default_image;
        }

        // Site logo as fallback
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $logo = wp_get_attachment_image_src($custom_logo_id, 'large');
            if ($logo) {
                return $logo[0];
            }
        }

        return '';
    }

    /**
     * Get active social profiles
     * 
     * @since 1.0.0
     * @return array Active social profile URLs
     */
    private function get_active_social_profiles() {
        $profiles = array();
        
        foreach ($this->social_networks as $network_id => $network) {
            $url = get_theme_mod('recruitpro_social_' . $network_id, '');
            if (!empty($url)) {
                $profiles[] = $url;
            }
        }
        
        return $profiles;
    }

    /**
     * Extract Twitter username from URL
     * 
     * @since 1.0.0
     * @param string $url Twitter profile URL
     * @return string|false Twitter username or false
     */
    private function extract_twitter_username($url) {
        if (preg_match('/twitter\.com\/([a-zA-Z0-9_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Check if sharing buttons should be shown
     * 
     * @since 1.0.0
     * @return bool True if sharing buttons should be displayed
     */
    private function should_show_sharing_buttons() {
        if (is_singular('post')) {
            return true;
        }
        
        if (is_singular('job') && get_theme_mod('recruitpro_sharing_on_jobs', true)) {
            return true;
        }
        
        if (is_page() && get_theme_mod('recruitpro_sharing_on_pages', false)) {
            return true;
        }
        
        return false;
    }

    /**
     * Output job-specific Open Graph tags
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_job_og_tags() {
        if (!is_singular('job')) {
            return;
        }

        $job_id = get_the_ID();
        $job_type = get_post_meta($job_id, '_job_type', true);
        $job_location = get_post_meta($job_id, '_job_location', true);
        $salary = get_post_meta($job_id, '_job_salary', true);

        if ($job_type) {
            echo '<meta property="job:job_type" content="' . esc_attr($job_type) . '">' . "\n";
        }

        if ($job_location) {
            echo '<meta property="job:location" content="' . esc_attr($job_location) . '">' . "\n";
        }

        if ($salary) {
            echo '<meta property="job:salary" content="' . esc_attr($salary) . '">' . "\n";
        }
    }

    /**
     * Output company-specific Open Graph tags
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_company_og_tags() {
        echo '<meta property="og:type" content="business.business">' . "\n";
        
        $company_name = get_theme_mod('recruitpro_company_name', '');
        if ($company_name) {
            echo '<meta property="business:contact_data:locality" content="' . esc_attr($company_name) . '">' . "\n";
        }
    }

    /**
     * Sanitization methods
     */

    /**
     * Sanitize Twitter card type
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_twitter_card_type($input) {
        $valid_types = array('summary', 'summary_large_image');
        return in_array($input, $valid_types) ? $input : 'summary_large_image';
    }

    /**
     * Sanitize sharing style
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_sharing_style($input) {
        $valid_styles = array('icon_only', 'text_only', 'icon_text');
        return in_array($input, $valid_styles) ? $input : 'icon_text';
    }

    /**
     * Sanitize sharing platforms
     * 
     * @since 1.0.0
     * @param string $input Input value
     * @return string Sanitized value
     */
    public function sanitize_sharing_platforms($input) {
        $platforms = array_map('trim', explode(',', $input));
        $valid_platforms = array_keys($this->sharing_platforms);
        $sanitized_platforms = array_intersect($platforms, $valid_platforms);
        return implode(',', $sanitized_platforms);
    }

    /**
     * Optimize social title
     * 
     * @since 1.0.0
     * @param array $title_parts Title parts
     * @return array Modified title parts
     */
    public function optimize_social_title($title_parts) {
        // Ensure title is optimized for social sharing
        if (is_singular('job')) {
            $job_type = get_post_meta(get_the_ID(), '_job_type', true);
            $job_location = get_post_meta(get_the_ID(), '_job_location', true);
            
            if ($job_type || $job_location) {
                $title_parts['title'] .= ' - ' . $job_type;
                if ($job_location) {
                    $title_parts['title'] .= ' in ' . $job_location;
                }
            }
        }
        
        return $title_parts;
    }

    /**
     * Optimize social excerpt
     * 
     * @since 1.0.0
     * @param string $excerpt Post excerpt
     * @return string Optimized excerpt
     */
    public function optimize_social_excerpt($excerpt) {
        // Ensure excerpt is optimized for social sharing
        if (empty($excerpt) && is_singular('job')) {
            $job_description = get_post_meta(get_the_ID(), '_job_description', true);
            if ($job_description) {
                $excerpt = wp_trim_words($job_description, 25);
            }
        }
        
        return $excerpt;
    }

    /**
     * Handle AJAX requests and testing
     */

    /**
     * Handle social login preparation
     * 
     * @since 1.0.0
     * @return void
     */
    public function handle_social_login_prep() {
        check_ajax_referer('recruitpro_social_nonce', 'nonce');
        
        wp_send_json_error(array(
            'message' => __('Social login requires the RecruitPro Social Media plugin to be activated.', 'recruitpro')
        ));
    }

    /**
     * Test social sharing
     * 
     * @since 1.0.0
     * @return void
     */
    public function test_social_sharing() {
        check_ajax_referer('recruitpro_social_nonce', 'nonce');
        
        $url = sanitize_url($_POST['url']);
        if (!$url) {
            wp_send_json_error(array('message' => __('Invalid URL', 'recruitpro')));
        }
        
        // Basic URL validation and preview generation
        $preview_data = array(
            'url' => $url,
            'title' => 'Test Title',
            'description' => 'Test description for social sharing.',
            'image' => get_theme_mod('recruitpro_default_og_image', ''),
        );
        
        wp_send_json_success($preview_data);
    }
}

// Initialize the social integration manager
if (class_exists('RecruitPro_Social_Integration')) {
    new RecruitPro_Social_Integration();
}

/**
 * Helper functions for social integration
 */

/**
 * Get social media profiles
 * 
 * @since 1.0.0
 * @return array Social media profile URLs
 */
function recruitpro_get_social_profiles() {
    $social_integration = new RecruitPro_Social_Integration();
    return $social_integration->get_active_social_profiles();
}

/**
 * Display social sharing buttons
 * 
 * @since 1.0.0
 * @param string $position Position (before/after content)
 * @return void
 */
function recruitpro_social_sharing_buttons($position = 'after') {
    if (!get_theme_mod('recruitpro_enable_social_sharing', true)) {
        return;
    }

    $sharing_position = get_theme_mod('recruitpro_sharing_position', 'after');
    if ($sharing_position !== $position) {
        return;
    }

    $social_integration = new RecruitPro_Social_Integration();
    echo $social_integration->get_sharing_buttons();
}

/**
 * Check if social media plugin is active
 * 
 * @since 1.0.0
 * @return bool True if social media plugin is active
 */
function recruitpro_has_social_plugin() {
    return class_exists('RecruitPro_Social_Media');
}

/**
 * Get recruitment-focused social networks
 * 
 * @since 1.0.0
 * @return array Recruitment-focused social networks
 */
function recruitpro_get_recruitment_social_networks() {
    $networks = array('linkedin', 'glassdoor', 'indeed', 'xing');
    return apply_filters('recruitpro_recruitment_social_networks', $networks);
}

/**
 * Generate social media schema markup
 * 
 * @since 1.0.0
 * @return string Schema markup
 */
function recruitpro_get_social_schema() {
    $social_integration = new RecruitPro_Social_Integration();
    $profiles = $social_integration->get_active_social_profiles();
    
    if (empty($profiles)) {
        return '';
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'sameAs' => $profiles,
    );

    return wp_json_encode($schema, JSON_UNESCAPED_SLASHES);
}