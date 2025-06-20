<?php
/**
 * RecruitPro Theme Basic Meta Tags Management
 *
 * This file handles essential meta tags for the RecruitPro recruitment theme.
 * It provides basic SEO meta tags, Open Graph, and recruitment-specific tags
 * without conflicting with the advanced SEO plugin. This is the theme-level
 * implementation focusing on core presentation needs only.
 *
 * @package RecruitPro
 * @subpackage Theme/Meta
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/meta-tags.php
 * Purpose: Basic theme-level meta tags (non-conflicting with SEO plugin)
 * Dependencies: WordPress core, theme customizer
 * Conflicts: None (designed to work with RecruitPro SEO plugin)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Basic Meta Tags Class
 * 
 * Handles essential meta tags for the theme without conflicting
 * with the advanced SEO plugin functionality.
 *
 * @since 1.0.0
 */
class RecruitPro_Basic_Meta_Tags {

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        add_action('wp_head', array($this, 'output_basic_meta_tags'), 1);
        add_action('wp_head', array($this, 'output_theme_meta_tags'), 5);
        add_action('wp_head', array($this, 'output_recruitment_meta_tags'), 10);
        add_action('customize_register', array($this, 'add_basic_meta_customizer_options'));
        add_filter('document_title_separator', array($this, 'customize_title_separator'));
    }

    /**
     * Output basic meta tags required by theme
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_basic_meta_tags() {
        
        // Only output if SEO plugin is not handling these
        if ($this->seo_plugin_active()) {
            return;
        }
        
        // Basic charset and viewport (always needed)
        echo '<meta charset="' . get_bloginfo('charset') . '">' . "\n";
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">' . "\n";
        
        // Theme color for mobile browsers
        $primary_color = get_theme_mod('recruitpro_primary_color', '#1e40af');
        echo '<meta name="theme-color" content="' . esc_attr($primary_color) . '">' . "\n";
        
        // Basic description (only if no SEO plugin)
        $description = $this->get_basic_description();
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
        
        // Basic robots tag
        $robots = $this->get_basic_robots();
        if ($robots) {
            echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
        }
        
        // Canonical URL (basic implementation)
        $canonical = $this->get_basic_canonical();
        if ($canonical) {
            echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
        }
    }

    /**
     * Output theme-specific meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_theme_meta_tags() {
        
        // Generator tag (theme identification)
        echo '<meta name="generator" content="RecruitPro Theme ' . wp_get_theme()->get('Version') . '">' . "\n";
        
        // Theme-specific tags
        echo '<meta name="theme-name" content="RecruitPro">' . "\n";
        echo '<meta name="theme-type" content="recruitment-agency">' . "\n";
        
        // Mobile app meta tags
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">' . "\n";
        
        // Format detection
        echo '<meta name="format-detection" content="telephone=yes">' . "\n";
        echo '<meta name="format-detection" content="email=yes">' . "\n";
        
        // Prefetch DNS for common resources
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
        
        // Preconnect for critical resources
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }

    /**
     * Output recruitment-specific meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_recruitment_meta_tags() {
        
        // Industry-specific meta tags
        $industry = get_theme_mod('recruitpro_company_industry', 'Recruitment');
        if ($industry) {
            echo '<meta name="industry" content="' . esc_attr($industry) . '">' . "\n";
        }
        
        // Business type
        echo '<meta name="business-type" content="recruitment-agency">' . "\n";
        
        // Service areas (basic implementation)
        $service_areas = get_theme_mod('recruitpro_service_areas', '');
        if ($service_areas) {
            echo '<meta name="service-areas" content="' . esc_attr($service_areas) . '">' . "\n";
        }
        
        // Specializations
        $specializations = get_theme_mod('recruitpro_specializations', '');
        if ($specializations) {
            echo '<meta name="specializations" content="' . esc_attr($specializations) . '">' . "\n";
        }
        
        // Basic Open Graph tags (only if SEO plugin not active)
        if (!$this->seo_plugin_active()) {
            $this->output_basic_open_graph();
        }
        
        // Job posting specific tags
        if (is_singular('job')) {
            $this->output_job_meta_tags();
        }
        
        // Company page specific tags
        if (is_page_template('page-about.php') || is_page('about')) {
            $this->output_company_meta_tags();
        }
    }

    /**
     * Output basic Open Graph tags
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_basic_open_graph() {
        
        // Basic OG tags
        echo '<meta property="og:type" content="' . esc_attr($this->get_og_type()) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($this->get_page_title()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($this->get_current_url()) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        
        // Description
        $description = $this->get_basic_description();
        if ($description) {
            echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        }
        
        // Image
        $image = $this->get_default_og_image();
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        }
        
        // Basic Twitter Card
        echo '<meta name="twitter:card" content="summary">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($this->get_page_title()) . '">' . "\n";
        if ($description) {
            echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        }
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
    }

    /**
     * Output job-specific meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_job_meta_tags() {
        
        // Job-specific meta tags (basic implementation)
        $job_id = get_the_ID();
        
        // Employment type
        $employment_type = get_post_meta($job_id, '_employment_type', true);
        if ($employment_type) {
            echo '<meta name="job-employment-type" content="' . esc_attr($employment_type) . '">' . "\n";
        }
        
        // Job location
        $job_location = get_post_meta($job_id, '_job_location', true);
        if ($job_location) {
            echo '<meta name="job-location" content="' . esc_attr($job_location) . '">' . "\n";
        }
        
        // Salary range
        $salary_min = get_post_meta($job_id, '_salary_min', true);
        $salary_max = get_post_meta($job_id, '_salary_max', true);
        if ($salary_min || $salary_max) {
            $salary_range = '';
            if ($salary_min && $salary_max) {
                $salary_range = $salary_min . '-' . $salary_max;
            } elseif ($salary_min) {
                $salary_range = 'From ' . $salary_min;
            } elseif ($salary_max) {
                $salary_range = 'Up to ' . $salary_max;
            }
            
            if ($salary_range) {
                echo '<meta name="job-salary-range" content="' . esc_attr($salary_range) . '">' . "\n";
            }
        }
        
        // Job category
        $job_categories = get_the_terms($job_id, 'job_category');
        if ($job_categories && !is_wp_error($job_categories)) {
            $categories = array();
            foreach ($job_categories as $category) {
                $categories[] = $category->name;
            }
            echo '<meta name="job-category" content="' . esc_attr(implode(', ', $categories)) . '">' . "\n";
        }
    }

    /**
     * Output company-specific meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_company_meta_tags() {
        
        // Company information
        $company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
        $founded_year = get_theme_mod('recruitpro_company_founded', '');
        $employee_count = get_theme_mod('recruitpro_company_size', '');
        
        if ($founded_year) {
            echo '<meta name="company-founded" content="' . esc_attr($founded_year) . '">' . "\n";
        }
        
        if ($employee_count) {
            echo '<meta name="company-size" content="' . esc_attr($employee_count) . '">' . "\n";
        }
        
        // Contact information
        $phone = get_theme_mod('recruitpro_contact_phone', '');
        $email = get_theme_mod('recruitpro_contact_email', '');
        
        if ($phone) {
            echo '<meta name="company-phone" content="' . esc_attr($phone) . '">' . "\n";
        }
        
        if ($email) {
            echo '<meta name="company-email" content="' . esc_attr($email) . '">' . "\n";
        }
    }

    /**
     * Add basic meta customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_basic_meta_customizer_options($wp_customize) {
        
        // Basic Meta Section
        $wp_customize->add_section('recruitpro_basic_meta', array(
            'title' => __('Basic Meta Tags', 'recruitpro'),
            'description' => __('Basic meta tag settings. Advanced SEO features are handled by the SEO plugin.', 'recruitpro'),
            'priority' => 140,
            'capability' => 'edit_theme_options',
        ));

        // Title Separator
        $wp_customize->add_setting('recruitpro_title_separator', array(
            'default' => '-',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_title_separator', array(
            'label' => __('Title Separator', 'recruitpro'),
            'section' => 'recruitpro_basic_meta',
            'type' => 'select',
            'choices' => array(
                '-' => '- (Dash)',
                '|' => '| (Pipe)',
                '·' => '· (Bullet)',
                '»' => '» (Double Arrow)',
                '/' => '/ (Slash)',
            ),
        ));

        // Company Industry
        $wp_customize->add_setting('recruitpro_company_industry', array(
            'default' => 'Recruitment',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_company_industry', array(
            'label' => __('Company Industry', 'recruitpro'),
            'description' => __('Your primary industry/business type.', 'recruitpro'),
            'section' => 'recruitpro_basic_meta',
            'type' => 'text',
        ));

        // Service Areas
        $wp_customize->add_setting('recruitpro_service_areas', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_service_areas', array(
            'label' => __('Service Areas', 'recruitpro'),
            'description' => __('Geographic areas you serve (comma-separated).', 'recruitpro'),
            'section' => 'recruitpro_basic_meta',
            'type' => 'text',
        ));

        // Specializations
        $wp_customize->add_setting('recruitpro_specializations', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_specializations', array(
            'label' => __('Specializations', 'recruitpro'),
            'description' => __('Your recruitment specializations (comma-separated).', 'recruitpro'),
            'section' => 'recruitpro_basic_meta',
            'type' => 'text',
        ));

        // Default OG Image
        $wp_customize->add_setting('recruitpro_default_og_image', array(
            'default' => '',
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'recruitpro_default_og_image', array(
            'label' => __('Default Social Sharing Image', 'recruitpro'),
            'description' => __('Default image for social media sharing (1200x630 recommended).', 'recruitpro'),
            'section' => 'recruitpro_basic_meta',
            'mime_type' => 'image',
        )));
    }

    /**
     * Customize title separator
     * 
     * @since 1.0.0
     * @param string $separator Current separator
     * @return string Modified separator
     */
    public function customize_title_separator($separator) {
        
        return get_theme_mod('recruitpro_title_separator', '-');
    }

    /**
     * Check if SEO plugin is active
     * 
     * @since 1.0.0
     * @return bool True if SEO plugin is active
     */
    private function seo_plugin_active() {
        
        return class_exists('RecruitPro_SEO') || 
               class_exists('WPSEO_Options') || 
               function_exists('rank_math') ||
               class_exists('All_in_One_SEO_Pack');
    }

    /**
     * Get basic page description
     * 
     * @since 1.0.0
     * @return string Description
     */
    private function get_basic_description() {
        
        if (is_front_page()) {
            return get_bloginfo('description');
        } elseif (is_singular()) {
            $excerpt = get_the_excerpt();
            return $excerpt ? wp_trim_words($excerpt, 25, '...') : '';
        } elseif (is_category()) {
            return category_description() ?: sprintf(__('Browse %s career advice and insights.', 'recruitpro'), single_cat_title('', false));
        } elseif (is_tag()) {
            return tag_description() ?: sprintf(__('Articles tagged with %s.', 'recruitpro'), single_tag_title('', false));
        }
        
        return get_bloginfo('description');
    }

    /**
     * Get basic robots directive
     * 
     * @since 1.0.0
     * @return string Robots directive
     */
    private function get_basic_robots() {
        
        if (is_search() || is_404()) {
            return 'noindex, nofollow';
        } elseif (is_admin() || is_feed()) {
            return 'noindex';
        }
        
        return 'index, follow';
    }

    /**
     * Get basic canonical URL
     * 
     * @since 1.0.0
     * @return string|null Canonical URL
     */
    private function get_basic_canonical() {
        
        if (is_front_page()) {
            return home_url('/');
        } elseif (is_singular()) {
            return get_permalink();
        }
        
        return null;
    }

    /**
     * Get Open Graph type
     * 
     * @since 1.0.0
     * @return string OG type
     */
    private function get_og_type() {
        
        if (is_front_page()) {
            return 'website';
        } elseif (is_singular()) {
            return 'article';
        }
        
        return 'website';
    }

    /**
     * Get current page title
     * 
     * @since 1.0.0
     * @return string Page title
     */
    private function get_page_title() {
        
        if (is_front_page()) {
            return get_bloginfo('name') . ' - ' . get_bloginfo('description');
        } elseif (is_singular()) {
            return get_the_title();
        }
        
        return wp_get_document_title();
    }

    /**
     * Get current URL
     * 
     * @since 1.0.0
     * @return string Current URL
     */
    private function get_current_url() {
        
        return home_url(add_query_arg(null, null));
    }

    /**
     * Get default Open Graph image
     * 
     * @since 1.0.0
     * @return string|null Image URL
     */
    private function get_default_og_image() {
        
        // Try featured image first
        if (is_singular() && has_post_thumbnail()) {
            $image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($image_data) {
                return $image_data[0];
            }
        }
        
        // Fallback to default OG image from customizer
        $default_image_id = get_theme_mod('recruitpro_default_og_image', '');
        if ($default_image_id) {
            $image_data = wp_get_attachment_image_src($default_image_id, 'large');
            if ($image_data) {
                return $image_data[0];
            }
        }
        
        // Fallback to site logo
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $logo_data = wp_get_attachment_image_src($logo_id, 'large');
            if ($logo_data) {
                return $logo_data[0];
            }
        }
        
        return null;
    }
}

// Initialize basic meta tags
new RecruitPro_Basic_Meta_Tags();

/**
 * Helper Functions
 */

/**
 * Get meta description for current page
 * 
 * @since 1.0.0
 * @return string Meta description
 */
function recruitpro_get_meta_description() {
    
    $meta_manager = new RecruitPro_Basic_Meta_Tags();
    return $meta_manager->get_basic_description();
}

/**
 * Check if advanced SEO features are available
 * 
 * @since 1.0.0
 * @return bool True if advanced SEO is available
 */
function recruitpro_has_advanced_seo() {
    
    return class_exists('RecruitPro_SEO');
}

/**
 * Output recruitment-specific meta tags for custom implementations
 * 
 * @since 1.0.0
 * @param array $meta_data Meta tag data
 * @return void
 */
function recruitpro_output_custom_meta($meta_data) {
    
    if (!is_array($meta_data)) {
        return;
    }
    
    foreach ($meta_data as $name => $content) {
        if (!empty($content)) {
            echo '<meta name="' . esc_attr($name) . '" content="' . esc_attr($content) . '">' . "\n";
        }
    }
}

?>