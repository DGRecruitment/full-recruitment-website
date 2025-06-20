<?php
/**
 * RecruitPro Theme SEO Optimization Manager
 *
 * Basic SEO optimizations at the theme level for recruitment websites.
 * Provides essential SEO enhancements, content optimization, and local SEO
 * features without conflicting with the main SEO plugin. Focuses on
 * recruitment-specific SEO requirements.
 *
 * @package RecruitPro
 * @subpackage Theme/SEO
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/seo-optimization.php
 * Purpose: Theme-level SEO optimizations for recruitment websites
 * Dependencies: WordPress core, theme functions
 * Features: Basic SEO, content optimization, local SEO, recruitment focus
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro SEO Manager Class
 * 
 * Handles basic SEO functionality at the theme level including
 * content optimization, local SEO, and recruitment-specific features.
 *
 * @since 1.0.0
 */
class RecruitPro_SEO_Manager {

    /**
     * SEO settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $seo_settings = array();

    /**
     * Local business data
     * 
     * @since 1.0.0
     * @var array
     */
    private $business_data = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
        $this->load_business_data();
    }

    /**
     * Initialize SEO settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->seo_settings = array(
            // Core SEO settings
            'enable_basic_seo' => get_theme_mod('recruitpro_enable_basic_seo', true),
            'enable_local_seo' => get_theme_mod('recruitpro_enable_local_seo', true),
            'enable_content_optimization' => get_theme_mod('recruitpro_enable_content_optimization', true),
            'enable_image_optimization' => get_theme_mod('recruitpro_enable_image_optimization', true),
            
            // Title and meta settings
            'title_separator' => get_theme_mod('recruitpro_title_separator', '|'),
            'title_format' => get_theme_mod('recruitpro_title_format', 'page_title | site_name'),
            'auto_meta_description' => get_theme_mod('recruitpro_auto_meta_description', true),
            'meta_description_length' => get_theme_mod('recruitpro_meta_description_length', 160),
            
            // Content optimization settings
            'optimize_headings' => get_theme_mod('recruitpro_optimize_headings', true),
            'optimize_images' => get_theme_mod('recruitpro_optimize_images', true),
            'add_alt_text' => get_theme_mod('recruitpro_add_alt_text', true),
            'optimize_links' => get_theme_mod('recruitpro_optimize_links', true),
            
            // Local SEO settings
            'business_name' => get_theme_mod('recruitpro_business_name', get_bloginfo('name')),
            'business_type' => get_theme_mod('recruitpro_business_type', 'ProfessionalService'),
            'service_areas' => get_theme_mod('recruitpro_service_areas', array()),
            'business_hours' => get_theme_mod('recruitpro_business_hours', array()),
            
            // Recruitment specific SEO
            'job_seo_optimization' => get_theme_mod('recruitpro_job_seo_optimization', true),
            'candidate_seo_privacy' => get_theme_mod('recruitpro_candidate_seo_privacy', true),
            'industry_keywords' => get_theme_mod('recruitpro_industry_keywords', array()),
            'location_keywords' => get_theme_mod('recruitpro_location_keywords', array()),
            
            // Technical SEO
            'clean_urls' => get_theme_mod('recruitpro_clean_urls', true),
            'remove_category_base' => get_theme_mod('recruitpro_remove_category_base', false),
            'optimize_permalinks' => get_theme_mod('recruitpro_optimize_permalinks', true),
            'enable_breadcrumbs' => get_theme_mod('recruitpro_enable_breadcrumbs', true),
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        if (!$this->seo_settings['enable_basic_seo']) {
            return;
        }

        // Only add hooks if main SEO plugin is not active
        if (!$this->is_seo_plugin_active()) {
            // Title optimization hooks
            add_filter('document_title_separator', array($this, 'customize_title_separator'));
            add_filter('document_title_parts', array($this, 'optimize_document_title'));
            
            // Meta description hooks
            if ($this->seo_settings['auto_meta_description']) {
                add_action('wp_head', array($this, 'add_auto_meta_description'), 5);
            }
        }
        
        // Content optimization hooks (always active)
        if ($this->seo_settings['enable_content_optimization']) {
            add_filter('the_content', array($this, 'optimize_content_seo'), 20);
            add_filter('the_excerpt', array($this, 'optimize_excerpt_seo'), 20);
        }
        
        // Image optimization hooks
        if ($this->seo_settings['optimize_images']) {
            add_filter('wp_get_attachment_image_attributes', array($this, 'optimize_image_attributes'), 10, 3);
            add_filter('the_content', array($this, 'optimize_content_images'), 25);
        }
        
        // Local SEO hooks
        if ($this->seo_settings['enable_local_seo']) {
            add_action('wp_head', array($this, 'add_local_business_schema'), 15);
            add_filter('recruitpro_footer_text', array($this, 'add_local_seo_footer'));
        }
        
        // Job-specific SEO hooks
        if ($this->seo_settings['job_seo_optimization'] && recruitpro_has_jobs_plugin()) {
            add_filter('single_post_title', array($this, 'optimize_job_title'), 10, 2);
            add_action('wp_head', array($this, 'add_job_seo_meta'), 10);
            add_filter('the_content', array($this, 'optimize_job_content'), 15);
        }
        
        // URL optimization hooks
        if ($this->seo_settings['clean_urls']) {
            add_action('init', array($this, 'optimize_urls'));
        }
        
        // Admin hooks
        add_action('customize_register', array($this, 'customize_register_seo'));
        add_action('admin_init', array($this, 'seo_admin_notices'));
        
        // Performance SEO hooks
        add_action('wp_head', array($this, 'add_seo_performance_tags'), 1);
        add_filter('wp_resource_hints', array($this, 'add_seo_resource_hints'), 10, 2);
        
        // Sitemap hints (if no SEO plugin)
        if (!$this->is_seo_plugin_active()) {
            add_action('wp_head', array($this, 'add_sitemap_hints'), 1);
        }
        
        // Integration hooks for main SEO plugin
        add_action('recruitpro_seo_plugin_activated', array($this, 'handle_seo_plugin_activation'));
        add_filter('recruitpro_seo_data', array($this, 'provide_theme_seo_data'));
    }

    /**
     * Load business data for local SEO
     * 
     * @since 1.0.0
     * @return void
     */
    private function load_business_data() {
        $this->business_data = array(
            'name' => $this->seo_settings['business_name'],
            'type' => $this->seo_settings['business_type'],
            'address' => array(
                'street' => get_theme_mod('recruitpro_business_address_street', ''),
                'city' => get_theme_mod('recruitpro_business_address_city', ''),
                'region' => get_theme_mod('recruitpro_business_address_region', ''),
                'postal_code' => get_theme_mod('recruitpro_business_address_postal', ''),
                'country' => get_theme_mod('recruitpro_business_address_country', ''),
            ),
            'contact' => array(
                'phone' => get_theme_mod('recruitpro_business_phone', ''),
                'email' => get_theme_mod('recruitpro_business_email', ''),
                'website' => home_url(),
            ),
            'social' => array(
                'linkedin' => get_theme_mod('recruitpro_social_linkedin', ''),
                'facebook' => get_theme_mod('recruitpro_social_facebook', ''),
                'twitter' => get_theme_mod('recruitpro_social_twitter', ''),
            ),
            'specialties' => get_theme_mod('recruitpro_business_specialties', array()),
            'service_areas' => $this->seo_settings['service_areas'],
            'established' => get_theme_mod('recruitpro_business_established', ''),
        );
    }

    /**
     * Check if main SEO plugin is active
     * 
     * @since 1.0.0
     * @return bool True if SEO plugin is active
     */
    private function is_seo_plugin_active() {
        return class_exists('RecruitPro_SEO') || 
               class_exists('WPSEO_Frontend') || 
               class_exists('RankMath') || 
               function_exists('aioseo');
    }

    /**
     * Customize title separator
     * 
     * @since 1.0.0
     * @param string $separator Default separator
     * @return string Custom separator
     */
    public function customize_title_separator($separator) {
        return $this->seo_settings['title_separator'];
    }

    /**
     * Optimize document title
     * 
     * @since 1.0.0
     * @param array $title Title parts
     * @return array Optimized title parts
     */
    public function optimize_document_title($title) {
        // Customize titles for recruitment pages
        if (is_front_page()) {
            $tagline = get_bloginfo('description');
            if ($tagline) {
                $title['tagline'] = $tagline;
            }
        } elseif (is_singular('job')) {
            $job_location = get_post_meta(get_the_ID(), '_job_location', true);
            $job_type = get_post_meta(get_the_ID(), '_job_type', true);
            
            if ($job_location) {
                $title['title'] = get_the_title() . ' in ' . $job_location;
            }
            
            if ($job_type) {
                $title['title'] .= ' - ' . ucfirst($job_type) . ' Position';
            }
        } elseif (is_post_type_archive('job')) {
            $title['title'] = __('Current Job Opportunities', 'recruitpro');
        } elseif (is_tax('job_category')) {
            $term = get_queried_object();
            $title['title'] = sprintf(__('%s Jobs', 'recruitpro'), $term->name);
        } elseif (is_tax('job_location')) {
            $term = get_queried_object();
            $title['title'] = sprintf(__('Jobs in %s', 'recruitpro'), $term->name);
        } elseif (is_category()) {
            $title['title'] = sprintf(__('Career Advice: %s', 'recruitpro'), single_cat_title('', false));
        } elseif (is_search()) {
            $search_query = get_search_query();
            if (recruitpro_is_jobs_page()) {
                $title['title'] = sprintf(__('Job Search Results for "%s"', 'recruitpro'), $search_query);
            } else {
                $title['title'] = sprintf(__('Search Results for "%s"', 'recruitpro'), $search_query);
            }
        }

        return $title;
    }

    /**
     * Add automatic meta description
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_auto_meta_description() {
        $description = $this->generate_meta_description();
        
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        }
    }

    /**
     * Generate meta description based on content
     * 
     * @since 1.0.0
     * @return string Meta description
     */
    private function generate_meta_description() {
        $description = '';
        $max_length = $this->seo_settings['meta_description_length'];

        if (is_front_page()) {
            $description = get_bloginfo('description');
            if (!$description) {
                $company_name = get_bloginfo('name');
                $description = sprintf(
                    __('%s - Professional recruitment agency providing expert staffing solutions and career opportunities.', 'recruitpro'),
                    $company_name
                );
            }
        } elseif (is_singular()) {
            global $post;
            
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $content = wp_strip_all_tags($post->post_content);
                $description = wp_trim_words($content, 25);
            }
            
            // Add recruitment-specific context for jobs
            if (get_post_type() === 'job') {
                $location = get_post_meta(get_the_ID(), '_job_location', true);
                $company = get_post_meta(get_the_ID(), '_job_company', true) ?: get_bloginfo('name');
                
                if ($location) {
                    $description = sprintf(
                        __('Apply for %s position at %s in %s. %s', 'recruitpro'),
                        get_the_title(),
                        $company,
                        $location,
                        $description
                    );
                }
            }
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $description = $term->description;
            
            if (!$description) {
                if (is_tax('job_category')) {
                    $description = sprintf(
                        __('Browse %s job opportunities. Find your next career move with our expert recruitment team.', 'recruitpro'),
                        $term->name
                    );
                } elseif (is_tax('job_location')) {
                    $description = sprintf(
                        __('Find jobs in %s. Browse current opportunities and apply with our recruitment experts.', 'recruitpro'),
                        $term->name
                    );
                } else {
                    $description = sprintf(
                        __('Browse articles about %s. Career advice and industry insights from recruitment professionals.', 'recruitpro'),
                        $term->name
                    );
                }
            }
        } elseif (is_search()) {
            $search_query = get_search_query();
            $description = sprintf(
                __('Search results for "%s". Find relevant job opportunities and career resources.', 'recruitpro'),
                $search_query
            );
        }

        // Trim to max length
        if (strlen($description) > $max_length) {
            $description = wp_trim_words($description, 25);
            if (strlen($description) > $max_length) {
                $description = substr($description, 0, $max_length - 3) . '...';
            }
        }

        return $description;
    }

    /**
     * Optimize content for SEO
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Optimized content
     */
    public function optimize_content_seo($content) {
        if (is_admin() || is_feed()) {
            return $content;
        }

        // Add recruitment-specific keyword context
        if (is_singular('job')) {
            $content = $this->add_job_keywords_context($content);
        }

        // Optimize headings structure
        if ($this->seo_settings['optimize_headings']) {
            $content = $this->optimize_heading_structure($content);
        }

        // Add internal linking opportunities
        if ($this->seo_settings['optimize_links']) {
            $content = $this->add_contextual_links($content);
        }

        return $content;
    }

    /**
     * Add job-specific keyword context
     * 
     * @since 1.0.0
     * @param string $content Job content
     * @return string Enhanced content
     */
    private function add_job_keywords_context($content) {
        global $post;
        
        $job_title = get_the_title();
        $location = get_post_meta($post->ID, '_job_location', true);
        $job_type = get_post_meta($post->ID, '_job_type', true);
        $company = get_post_meta($post->ID, '_job_company', true) ?: get_bloginfo('name');

        // Add structured content for better SEO
        $seo_context = '';
        
        if ($location) {
            $seo_context .= sprintf(
                __('This %s position is located in %s and offers excellent career opportunities.', 'recruitpro'),
                $job_title,
                $location
            ) . ' ';
        }
        
        if ($job_type) {
            $seo_context .= sprintf(
                __('We are seeking candidates for this %s %s role.', 'recruitpro'),
                $job_type,
                strtolower($job_title)
            ) . ' ';
        }

        // Add recruitment call-to-action
        $seo_context .= sprintf(
            __('Apply now for this %s opportunity at %s or contact our recruitment team for more information.', 'recruitpro'),
            $job_title,
            $company
        );

        // Append to content with proper markup
        $content .= '<div class="job-seo-context" style="font-size: 0.9em; margin-top: 2em; padding: 1em; background: #f8f9fa; border-left: 3px solid #007cba;">';
        $content .= '<p>' . $seo_context . '</p>';
        $content .= '</div>';

        return $content;
    }

    /**
     * Optimize heading structure
     * 
     * @since 1.0.0
     * @param string $content Content to optimize
     * @return string Content with optimized headings
     */
    private function optimize_heading_structure($content) {
        // Ensure proper heading hierarchy
        $content = preg_replace_callback(
            '/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i',
            function($matches) {
                $level = intval($matches[1]);
                $text = $matches[2];
                
                // Add recruitment keywords to headings when relevant
                if (is_singular('job') && stripos($text, 'requirements') !== false) {
                    $text = __('Job Requirements', 'recruitpro') . ' - ' . $text;
                } elseif (is_singular('job') && stripos($text, 'responsibilities') !== false) {
                    $text = __('Key Responsibilities', 'recruitpro') . ' - ' . $text;
                }
                
                return "<h{$level}>{$text}</h{$level}>";
            },
            $content
        );

        return $content;
    }

    /**
     * Add contextual internal links
     * 
     * @since 1.0.0
     * @param string $content Content to optimize
     * @return string Content with added links
     */
    private function add_contextual_links($content) {
        // Define recruitment-related keywords to link
        $link_opportunities = array(
            'career opportunities' => home_url('/jobs/'),
            'job search' => home_url('/jobs/'),
            'recruitment process' => home_url('/about/'),
            'career advice' => home_url('/blog/'),
        );

        foreach ($link_opportunities as $keyword => $url) {
            if (stripos($content, $keyword) !== false && !stripos($content, 'href="' . $url . '"')) {
                $content = preg_replace(
                    '/\b(' . preg_quote($keyword, '/') . ')\b/i',
                    '<a href="' . esc_url($url) . '" title="' . esc_attr(ucfirst($keyword)) . '">$1</a>',
                    $content,
                    1 // Only replace first occurrence
                );
            }
        }

        return $content;
    }

    /**
     * Optimize image attributes for SEO
     * 
     * @since 1.0.0
     * @param array $attr Image attributes
     * @param WP_Post $attachment Attachment object
     * @param string $size Image size
     * @return array Optimized attributes
     */
    public function optimize_image_attributes($attr, $attachment, $size) {
        // Add missing alt text
        if (empty($attr['alt']) && $this->seo_settings['add_alt_text']) {
            $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            
            if (!$alt_text) {
                // Generate alt text based on image title or post context
                $alt_text = $attachment->post_title;
                
                if (empty($alt_text) && is_singular()) {
                    $alt_text = get_the_title() . ' - ' . __('Related Image', 'recruitpro');
                }
                
                if (is_singular('job')) {
                    $alt_text = get_the_title() . ' - ' . __('Job Opportunity', 'recruitpro');
                }
            }
            
            $attr['alt'] = esc_attr($alt_text);
        }

        // Add title attribute for better accessibility and SEO
        if (empty($attr['title'])) {
            $attr['title'] = esc_attr($attachment->post_title);
        }

        return $attr;
    }

    /**
     * Optimize images in content
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Content with optimized images
     */
    public function optimize_content_images($content) {
        // Add missing alt attributes to images in content
        $content = preg_replace_callback(
            '/<img([^>]+)>/i',
            function($matches) {
                $img_tag = $matches[0];
                
                // Check if alt attribute is missing
                if (strpos($img_tag, 'alt=') === false) {
                    $alt_text = '';
                    
                    // Try to extract title or other attributes
                    if (preg_match('/title="([^"]*)"/', $img_tag, $title_matches)) {
                        $alt_text = $title_matches[1];
                    } elseif (is_singular()) {
                        $alt_text = get_the_title() . ' - ' . __('Image', 'recruitpro');
                    }
                    
                    if ($alt_text) {
                        $img_tag = str_replace('<img', '<img alt="' . esc_attr($alt_text) . '"', $img_tag);
                    }
                }
                
                return $img_tag;
            },
            $content
        );

        return $content;
    }

    /**
     * Add local business schema
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_local_business_schema() {
        if (!is_front_page() || $this->is_seo_plugin_active()) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $this->business_data['type'],
            'name' => $this->business_data['name'],
            'url' => home_url(),
            'description' => get_bloginfo('description'),
        );

        // Add address if available
        $address_parts = array_filter($this->business_data['address']);
        if (!empty($address_parts)) {
            $schema['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $this->business_data['address']['street'],
                'addressLocality' => $this->business_data['address']['city'],
                'addressRegion' => $this->business_data['address']['region'],
                'postalCode' => $this->business_data['address']['postal_code'],
                'addressCountry' => $this->business_data['address']['country'],
            );
        }

        // Add contact information
        if (!empty($this->business_data['contact']['phone'])) {
            $schema['telephone'] = $this->business_data['contact']['phone'];
        }

        if (!empty($this->business_data['contact']['email'])) {
            $schema['email'] = $this->business_data['contact']['email'];
        }

        // Add social media profiles
        $social_profiles = array_filter($this->business_data['social']);
        if (!empty($social_profiles)) {
            $schema['sameAs'] = array_values($social_profiles);
        }

        // Add service areas
        if (!empty($this->business_data['service_areas'])) {
            $schema['areaServed'] = $this->business_data['service_areas'];
        }

        // Add specialties
        if (!empty($this->business_data['specialties'])) {
            $schema['knowsAbout'] = $this->business_data['specialties'];
        }

        // Add founding date
        if (!empty($this->business_data['established'])) {
            $schema['foundingDate'] = $this->business_data['established'];
        }

        echo '<script type="application/ld+json">';
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        echo '</script>' . "\n";
    }

    /**
     * Add job-specific SEO meta
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_job_seo_meta() {
        if (!is_singular('job')) {
            return;
        }

        global $post;
        
        // Job-specific meta tags
        $job_location = get_post_meta($post->ID, '_job_location', true);
        $job_type = get_post_meta($post->ID, '_job_type', true);
        $salary_min = get_post_meta($post->ID, '_job_salary_min', true);
        $salary_max = get_post_meta($post->ID, '_job_salary_max', true);

        if ($job_location) {
            echo '<meta name="job-location" content="' . esc_attr($job_location) . '">' . "\n";
        }

        if ($job_type) {
            echo '<meta name="employment-type" content="' . esc_attr($job_type) . '">' . "\n";
        }

        if ($salary_min && $salary_max) {
            echo '<meta name="salary-range" content="' . esc_attr($salary_min . '-' . $salary_max) . '">' . "\n";
        }

        // Add job posting specific Open Graph tags
        echo '<meta property="og:type" content="job">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($this->generate_meta_description()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
    }

    /**
     * Optimize job title
     * 
     * @since 1.0.0
     * @param string $title Current title
     * @param WP_Post $post Post object
     * @return string Optimized title
     */
    public function optimize_job_title($title, $post) {
        if ($post && $post->post_type === 'job') {
            $location = get_post_meta($post->ID, '_job_location', true);
            if ($location) {
                $title .= ' - ' . $location;
            }
        }
        return $title;
    }

    /**
     * Optimize job content
     * 
     * @since 1.0.0
     * @param string $content Job content
     * @return string Optimized content
     */
    public function optimize_job_content($content) {
        if (!is_singular('job')) {
            return $content;
        }

        // Add structured information for better SEO
        global $post;
        
        $application_url = get_post_meta($post->ID, '_job_application_url', true);
        if (!$application_url) {
            $application_url = home_url('/apply/?job=' . $post->ID);
        }

        // Add call-to-action section
        $cta_section = '<div class="job-cta-section" style="margin-top: 2em; padding: 1.5em; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px;">';
        $cta_section .= '<h3>' . __('Ready to Apply?', 'recruitpro') . '</h3>';
        $cta_section .= '<p>' . sprintf(
            __('Take the next step in your career and apply for this %s position today.', 'recruitpro'),
            get_the_title()
        ) . '</p>';
        $cta_section .= '<a href="' . esc_url($application_url) . '" class="button button-primary" style="display: inline-block; padding: 12px 24px; background: #0ea5e9; color: white; text-decoration: none; border-radius: 6px;">';
        $cta_section .= __('Apply Now', 'recruitpro') . '</a>';
        $cta_section .= '</div>';

        return $content . $cta_section;
    }

    /**
     * Optimize URLs
     * 
     * @since 1.0.0
     * @return void
     */
    public function optimize_urls() {
        // Remove category base if enabled
        if ($this->seo_settings['remove_category_base']) {
            add_action('init', function() {
                global $wp_rewrite;
                $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
            });
        }

        // Add custom rewrite rules for jobs
        if (recruitpro_has_jobs_plugin()) {
            add_rewrite_rule(
                '^jobs/([^/]+)/([^/]+)/?$',
                'index.php?post_type=job&job_category=$matches[1]&job_location=$matches[2]',
                'top'
            );
        }
    }

    /**
     * Add SEO performance tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_seo_performance_tags() {
        // Preload critical resources for SEO
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        
        // Add prefetch for likely next pages
        if (is_front_page()) {
            echo '<link rel="prefetch" href="' . esc_url(home_url('/jobs/')) . '">' . "\n";
            echo '<link rel="prefetch" href="' . esc_url(home_url('/about/')) . '">' . "\n";
        }
    }

    /**
     * Add SEO resource hints
     * 
     * @since 1.0.0
     * @param array $urls Resource URLs
     * @param string $relation_type Relation type
     * @return array Modified URLs
     */
    public function add_seo_resource_hints($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            $urls[] = 'https://maps.googleapis.com';
            $urls[] = 'https://www.google-analytics.com';
        }

        return $urls;
    }

    /**
     * Add sitemap hints
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_sitemap_hints() {
        // Basic sitemap reference (if no SEO plugin handles this)
        $sitemap_url = home_url('/sitemap.xml');
        echo '<link rel="sitemap" type="application/xml" title="Sitemap" href="' . esc_url($sitemap_url) . '">' . "\n";
    }

    /**
     * Add local SEO footer content
     * 
     * @since 1.0.0
     * @param string $footer_text Current footer text
     * @return string Enhanced footer text
     */
    public function add_local_seo_footer($footer_text) {
        $business_name = $this->business_data['name'];
        $city = $this->business_data['address']['city'];
        
        if ($city) {
            $local_text = sprintf(
                __('%s - Professional Recruitment Services in %s', 'recruitpro'),
                $business_name,
                $city
            );
            
            $footer_text = $local_text . ' | ' . $footer_text;
        }
        
        return $footer_text;
    }

    /**
     * Handle SEO plugin activation
     * 
     * @since 1.0.0
     * @return void
     */
    public function handle_seo_plugin_activation() {
        // Disable theme SEO features when main plugin is activated
        remove_action('wp_head', array($this, 'add_auto_meta_description'), 5);
        remove_filter('document_title_separator', array($this, 'customize_title_separator'));
        remove_filter('document_title_parts', array($this, 'optimize_document_title'));
        
        // Transfer settings to main plugin if possible
        if (function_exists('recruitpro_seo_import_theme_settings')) {
            recruitpro_seo_import_theme_settings($this->seo_settings);
        }
    }

    /**
     * Provide theme SEO data to main plugin
     * 
     * @since 1.0.0
     * @param array $seo_data Current SEO data
     * @return array Enhanced SEO data
     */
    public function provide_theme_seo_data($seo_data) {
        $seo_data['theme_settings'] = $this->seo_settings;
        $seo_data['business_data'] = $this->business_data;
        $seo_data['recruitment_keywords'] = $this->get_recruitment_keywords();
        
        return $seo_data;
    }

    /**
     * Get recruitment industry keywords
     * 
     * @since 1.0.0
     * @return array Recruitment keywords
     */
    private function get_recruitment_keywords() {
        $keywords = array(
            'primary' => array(
                'recruitment', 'staffing', 'jobs', 'careers', 'employment',
                'hiring', 'talent', 'candidates', 'positions', 'opportunities'
            ),
            'location' => $this->seo_settings['location_keywords'],
            'industry' => $this->seo_settings['industry_keywords'],
            'long_tail' => array(
                'find qualified candidates',
                'professional recruitment services',
                'expert staffing solutions',
                'career development opportunities',
                'talent acquisition specialists'
            ),
        );
        
        return $keywords;
    }

    /**
     * SEO admin notices
     * 
     * @since 1.0.0
     * @return void
     */
    public function seo_admin_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check for SEO conflicts
        if ($this->is_seo_plugin_active() && $this->seo_settings['auto_meta_description']) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-info"><p>';
                echo __('RecruitPro: An SEO plugin is active. Some theme SEO features have been automatically disabled to prevent conflicts.', 'recruitpro');
                echo '</p></div>';
            });
        }

        // Check for missing business information
        $missing_data = array();
        if (empty($this->business_data['address']['city'])) {
            $missing_data[] = __('business address', 'recruitpro');
        }
        if (empty($this->business_data['contact']['phone'])) {
            $missing_data[] = __('phone number', 'recruitpro');
        }

        if (!empty($missing_data)) {
            add_action('admin_notices', function() use ($missing_data) {
                echo '<div class="notice notice-warning"><p>';
                echo sprintf(
                    __('RecruitPro SEO: Consider adding %s in the theme customizer for better local SEO.', 'recruitpro'),
                    implode(' and ', $missing_data)
                );
                echo '</p></div>';
            });
        }
    }

    /**
     * Add SEO settings to customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_seo($wp_customize) {
        // SEO section
        $wp_customize->add_section('recruitpro_seo', array(
            'title' => __('Basic SEO Settings', 'recruitpro'),
            'description' => __('Basic SEO settings for your recruitment website. Advanced features are handled by the SEO plugin.', 'recruitpro'),
            'priority' => 65,
        ));

        // Enable basic SEO
        $wp_customize->add_setting('recruitpro_enable_basic_seo', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_basic_seo', array(
            'label' => __('Enable Basic SEO', 'recruitpro'),
            'description' => __('Enable theme-level SEO optimizations.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'checkbox',
        ));

        // Title separator
        $wp_customize->add_setting('recruitpro_title_separator', array(
            'default' => '|',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_title_separator', array(
            'label' => __('Title Separator', 'recruitpro'),
            'description' => __('Character used to separate page title from site name.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'select',
            'choices' => array(
                '|' => '|',
                '-' => '-',
                '•' => '•',
                '>' => '>',
                '~' => '~',
            ),
        ));

        // Auto meta description
        $wp_customize->add_setting('recruitpro_auto_meta_description', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_auto_meta_description', array(
            'label' => __('Auto Meta Descriptions', 'recruitpro'),
            'description' => __('Automatically generate meta descriptions for pages without them.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'checkbox',
        ));

        // Enable local SEO
        $wp_customize->add_setting('recruitpro_enable_local_seo', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_local_seo', array(
            'label' => __('Enable Local SEO', 'recruitpro'),
            'description' => __('Add local business schema and location-based optimizations.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'checkbox',
        ));

        // Business name
        $wp_customize->add_setting('recruitpro_business_name', array(
            'default' => get_bloginfo('name'),
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_business_name', array(
            'label' => __('Business Name', 'recruitpro'),
            'description' => __('Official name of your recruitment business.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'text',
        ));

        // Business city
        $wp_customize->add_setting('recruitpro_business_address_city', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_business_address_city', array(
            'label' => __('Business City', 'recruitpro'),
            'description' => __('City where your recruitment business is located.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'text',
        ));

        // Job SEO optimization
        $wp_customize->add_setting('recruitpro_job_seo_optimization', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_job_seo_optimization', array(
            'label' => __('Optimize Job Listings for SEO', 'recruitpro'),
            'description' => __('Add SEO enhancements to job postings for better search visibility.', 'recruitpro'),
            'section' => 'recruitpro_seo',
            'type' => 'checkbox',
        ));
    }
}

// Initialize the SEO manager
if (class_exists('RecruitPro_SEO_Manager')) {
    new RecruitPro_SEO_Manager();
}

/**
 * Helper function to check if basic SEO is enabled
 * 
 * @since 1.0.0
 * @return bool True if basic SEO is enabled
 */
function recruitpro_seo_enabled() {
    return get_theme_mod('recruitpro_enable_basic_seo', true);
}

/**
 * Helper function to get optimized title
 * 
 * @since 1.0.0
 * @param string $title Current title
 * @return string Optimized title
 */
function recruitpro_get_optimized_title($title = '') {
    if (empty($title)) {
        $title = wp_get_document_title();
    }
    
    return apply_filters('recruitpro_optimized_title', $title);
}

/**
 * Helper function to get meta description
 * 
 * @since 1.0.0
 * @return string Meta description
 */
function recruitpro_get_meta_description() {
    $seo_manager = new RecruitPro_SEO_Manager();
    return $seo_manager->generate_meta_description();
}

/**
 * Helper function to add recruitment keywords to content
 * 
 * @since 1.0.0
 * @param string $content Content to enhance
 * @return string Enhanced content
 */
function recruitpro_add_recruitment_keywords($content) {
    // Add contextual recruitment keywords
    $keywords = array(
        'job' => 'career opportunity',
        'position' => 'professional role',
        'apply' => 'submit application',
        'candidate' => 'job seeker',
    );
    
    foreach ($keywords as $search => $replace) {
        if (stripos($content, $search) !== false) {
            // Add semantic variety
            $content = preg_replace(
                '/\b' . preg_quote($search, '/') . '\b/i',
                '$0 (' . $replace . ')',
                $content,
                1
            );
        }
    }
    
    return $content;
}