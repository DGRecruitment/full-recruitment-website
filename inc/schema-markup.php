<?php
/**
 * RecruitPro Theme Schema Markup System
 *
 * Comprehensive structured data implementation for recruitment websites.
 * Includes Google Jobs schema, Organization data, and all necessary
 * Schema.org markups for optimal SEO and rich snippets in search results.
 *
 * @package RecruitPro
 * @subpackage Theme/Schema
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/schema-markup.php
 * Purpose: Schema.org structured data for recruitment websites
 * Dependencies: WordPress core, theme functions
 * Features: Google Jobs schema, Organization data, SEO optimization
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Schema Markup Manager Class
 * 
 * Handles all structured data output for the recruitment website
 * including job postings, organization info, and content schemas.
 *
 * @since 1.0.0
 */
class RecruitPro_Schema_Manager {

    /**
     * Schema settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $schema_settings = array();

    /**
     * Organization data cache
     * 
     * @since 1.0.0
     * @var array
     */
    private $organization_data = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
        $this->load_organization_data();
    }

    /**
     * Initialize schema settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->schema_settings = array(
            'enable_schema' => get_theme_mod('recruitpro_enable_schema', true),
            'enable_google_jobs' => get_theme_mod('recruitpro_enable_google_jobs_schema', true),
            'enable_organization_schema' => get_theme_mod('recruitpro_enable_organization_schema', true),
            'enable_breadcrumb_schema' => get_theme_mod('recruitpro_enable_breadcrumb_schema', true),
            'enable_article_schema' => get_theme_mod('recruitpro_enable_article_schema', true),
            'enable_person_schema' => get_theme_mod('recruitpro_enable_person_schema', true),
            'enable_service_schema' => get_theme_mod('recruitpro_enable_service_schema', true),
            'enable_website_schema' => get_theme_mod('recruitpro_enable_website_schema', true),
            'validate_schema' => get_theme_mod('recruitpro_validate_schema', true),
            'schema_debug_mode' => get_theme_mod('recruitpro_schema_debug_mode', false),
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        if (!$this->schema_settings['enable_schema']) {
            return;
        }

        // Core schema hooks
        add_action('wp_head', array($this, 'output_website_schema'), 1);
        add_action('wp_head', array($this, 'output_organization_schema'), 2);
        add_action('wp_head', array($this, 'output_page_specific_schema'), 10);
        
        // Job-specific schema hooks
        add_action('wp_head', array($this, 'output_job_schema'), 5);
        
        // Content-specific schema hooks
        add_filter('the_content', array($this, 'add_article_schema'));
        
        // Breadcrumb schema integration
        add_filter('recruitpro_breadcrumb_schema', array($this, 'generate_breadcrumb_schema'), 10, 2);
        
        // Admin hooks
        add_action('customize_register', array($this, 'customize_register_schema'));
        add_action('admin_init', array($this, 'schema_validation_check'));
        
        // Debug hooks
        if ($this->schema_settings['schema_debug_mode']) {
            add_action('wp_footer', array($this, 'output_schema_debug'));
        }
    }

    /**
     * Load organization data from theme customizer
     * 
     * @since 1.0.0
     * @return void
     */
    private function load_organization_data() {
        $this->organization_data = array(
            'name' => get_theme_mod('recruitpro_company_name', get_bloginfo('name')),
            'legal_name' => get_theme_mod('recruitpro_company_legal_name', get_bloginfo('name')),
            'description' => get_theme_mod('recruitpro_company_description', get_bloginfo('description')),
            'url' => home_url(),
            'logo' => get_theme_mod('recruitpro_company_logo', ''),
            'founded' => get_theme_mod('recruitpro_company_founded', ''),
            'employees' => get_theme_mod('recruitpro_company_employees', ''),
            'industry' => get_theme_mod('recruitpro_company_industry', 'Human Resources'),
            
            // Contact information
            'phone' => get_theme_mod('recruitpro_company_phone', ''),
            'email' => get_theme_mod('recruitpro_company_email', ''),
            'address' => array(
                'street' => get_theme_mod('recruitpro_company_address_street', ''),
                'city' => get_theme_mod('recruitpro_company_address_city', ''),
                'region' => get_theme_mod('recruitpro_company_address_region', ''),
                'postal_code' => get_theme_mod('recruitpro_company_address_postal', ''),
                'country' => get_theme_mod('recruitpro_company_address_country', ''),
            ),
            
            // Social media
            'social_media' => array(
                'linkedin' => get_theme_mod('recruitpro_social_linkedin', ''),
                'facebook' => get_theme_mod('recruitpro_social_facebook', ''),
                'twitter' => get_theme_mod('recruitpro_social_twitter', ''),
                'instagram' => get_theme_mod('recruitpro_social_instagram', ''),
            ),
            
            // Business details
            'registration_number' => get_theme_mod('recruitpro_company_registration', ''),
            'vat_number' => get_theme_mod('recruitpro_company_vat', ''),
            'license_number' => get_theme_mod('recruitpro_recruitment_license', ''),
        );
    }

    /**
     * Output website schema
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_website_schema() {
        if (!$this->schema_settings['enable_website_schema'] || !is_home()) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'inLanguage' => get_locale(),
        );

        // Add search action if search is enabled
        if (get_theme_mod('recruitpro_enable_search', true)) {
            $schema['potentialAction'] = array(
                '@type' => 'SearchAction',
                'target' => array(
                    '@type' => 'EntryPoint',
                    'urlTemplate' => home_url('/?s={search_term_string}'),
                ),
                'query-input' => 'required name=search_term_string',
            );
        }

        // Add publisher information
        if (!empty($this->organization_data['name'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $this->organization_data['name'],
                'url' => home_url(),
            );

            if (!empty($this->organization_data['logo'])) {
                $schema['publisher']['logo'] = array(
                    '@type' => 'ImageObject',
                    'url' => $this->organization_data['logo'],
                );
            }
        }

        $this->output_schema_json($schema, 'website');
    }

    /**
     * Output organization schema
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_organization_schema() {
        if (!$this->schema_settings['enable_organization_schema']) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => home_url() . '#organization',
            'name' => $this->organization_data['name'],
            'url' => $this->organization_data['url'],
        );

        // Add legal name if different
        if (!empty($this->organization_data['legal_name']) && 
            $this->organization_data['legal_name'] !== $this->organization_data['name']) {
            $schema['legalName'] = $this->organization_data['legal_name'];
        }

        // Add description
        if (!empty($this->organization_data['description'])) {
            $schema['description'] = $this->organization_data['description'];
        }

        // Add logo
        if (!empty($this->organization_data['logo'])) {
            $schema['logo'] = array(
                '@type' => 'ImageObject',
                'url' => $this->organization_data['logo'],
                'width' => 300,
                'height' => 300,
            );
        }

        // Add founding date
        if (!empty($this->organization_data['founded'])) {
            $schema['foundingDate'] = $this->organization_data['founded'];
        }

        // Add industry/category
        if (!empty($this->organization_data['industry'])) {
            $schema['knowsAbout'] = array('Human Resources', 'Recruitment', 'Talent Acquisition');
            $schema['industry'] = $this->organization_data['industry'];
        }

        // Add contact information
        $contact_points = array();
        
        if (!empty($this->organization_data['phone'])) {
            $contact_points[] = array(
                '@type' => 'ContactPoint',
                'telephone' => $this->organization_data['phone'],
                'contactType' => 'customer service',
                'availableLanguage' => array('English'),
            );
        }

        if (!empty($this->organization_data['email'])) {
            $contact_points[] = array(
                '@type' => 'ContactPoint',
                'email' => $this->organization_data['email'],
                'contactType' => 'customer service',
            );
        }

        if (!empty($contact_points)) {
            $schema['contactPoint'] = $contact_points;
        }

        // Add address
        $address_parts = array_filter($this->organization_data['address']);
        if (!empty($address_parts)) {
            $address = array('@type' => 'PostalAddress');
            
            if (!empty($this->organization_data['address']['street'])) {
                $address['streetAddress'] = $this->organization_data['address']['street'];
            }
            if (!empty($this->organization_data['address']['city'])) {
                $address['addressLocality'] = $this->organization_data['address']['city'];
            }
            if (!empty($this->organization_data['address']['region'])) {
                $address['addressRegion'] = $this->organization_data['address']['region'];
            }
            if (!empty($this->organization_data['address']['postal_code'])) {
                $address['postalCode'] = $this->organization_data['address']['postal_code'];
            }
            if (!empty($this->organization_data['address']['country'])) {
                $address['addressCountry'] = $this->organization_data['address']['country'];
            }

            if (count($address) > 1) { // More than just @type
                $schema['address'] = $address;
            }
        }

        // Add social media profiles
        $social_profiles = array_filter($this->organization_data['social_media']);
        if (!empty($social_profiles)) {
            $schema['sameAs'] = array_values($social_profiles);
        }

        // Add business identifiers
        if (!empty($this->organization_data['registration_number'])) {
            $schema['identifier'] = array(
                '@type' => 'PropertyValue',
                'name' => 'Company Registration Number',
                'value' => $this->organization_data['registration_number'],
            );
        }

        // Add employee count if available
        if (!empty($this->organization_data['employees'])) {
            $schema['numberOfEmployees'] = array(
                '@type' => 'QuantitativeValue',
                'value' => $this->organization_data['employees'],
            );
        }

        $this->output_schema_json($schema, 'organization');
    }

    /**
     * Output job posting schema
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_job_schema() {
        if (!$this->schema_settings['enable_google_jobs'] || !is_singular('job')) {
            return;
        }

        global $post;
        
        // Get job data from post meta (assumes jobs plugin structure)
        $job_title = get_the_title($post);
        $job_description = get_the_content();
        $job_location = get_post_meta($post->ID, '_job_location', true);
        $job_type = get_post_meta($post->ID, '_job_type', true);
        $salary_min = get_post_meta($post->ID, '_job_salary_min', true);
        $salary_max = get_post_meta($post->ID, '_job_salary_max', true);
        $salary_currency = get_post_meta($post->ID, '_job_salary_currency', true) ?: 'USD';
        $salary_period = get_post_meta($post->ID, '_job_salary_period', true) ?: 'YEAR';
        $employment_type = get_post_meta($post->ID, '_job_employment_type', true) ?: 'FULL_TIME';
        $job_deadline = get_post_meta($post->ID, '_job_deadline', true);
        $remote_work = get_post_meta($post->ID, '_job_remote_work', true);
        $experience_level = get_post_meta($post->ID, '_job_experience_level', true);
        $required_skills = get_post_meta($post->ID, '_job_required_skills', true);
        $benefits = get_post_meta($post->ID, '_job_benefits', true);

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            '@id' => get_permalink($post) . '#job',
            'title' => $job_title,
            'description' => wp_strip_all_tags($job_description),
            'datePosted' => get_the_date('c', $post),
            'url' => get_permalink($post),
            'identifier' => array(
                '@type' => 'PropertyValue',
                'name' => 'Job ID',
                'value' => $post->ID,
            ),
        );

        // Add hiring organization
        $schema['hiringOrganization'] = array(
            '@type' => 'Organization',
            'name' => $this->organization_data['name'],
            'url' => $this->organization_data['url'],
        );

        if (!empty($this->organization_data['logo'])) {
            $schema['hiringOrganization']['logo'] = $this->organization_data['logo'];
        }

        // Add job location
        if (!empty($job_location)) {
            $schema['jobLocation'] = array(
                '@type' => 'Place',
                'address' => array(
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job_location,
                ),
            );

            // Check if remote work is allowed
            if ($remote_work === 'yes' || $remote_work === '1') {
                $schema['jobLocationType'] = 'TELECOMMUTE';
            }
        }

        // Add employment type
        $employment_types = array(
            'full-time' => 'FULL_TIME',
            'part-time' => 'PART_TIME',
            'contract' => 'CONTRACTOR',
            'temporary' => 'TEMPORARY',
            'internship' => 'INTERN',
            'volunteer' => 'VOLUNTEER',
        );

        if (!empty($job_type) && isset($employment_types[$job_type])) {
            $schema['employmentType'] = $employment_types[$job_type];
        } elseif (!empty($employment_type)) {
            $schema['employmentType'] = strtoupper($employment_type);
        }

        // Add salary information
        if (!empty($salary_min) || !empty($salary_max)) {
            $base_salary = array(
                '@type' => 'MonetaryAmount',
                'currency' => $salary_currency,
            );

            if (!empty($salary_min) && !empty($salary_max)) {
                $base_salary['value'] = array(
                    '@type' => 'QuantitativeValue',
                    'minValue' => floatval($salary_min),
                    'maxValue' => floatval($salary_max),
                    'unitText' => strtoupper($salary_period),
                );
            } elseif (!empty($salary_min)) {
                $base_salary['value'] = array(
                    '@type' => 'QuantitativeValue',
                    'value' => floatval($salary_min),
                    'unitText' => strtoupper($salary_period),
                );
            }

            $schema['baseSalary'] = $base_salary;
        }

        // Add application deadline
        if (!empty($job_deadline)) {
            $schema['validThrough'] = date('c', strtotime($job_deadline));
        }

        // Add experience requirements
        if (!empty($experience_level)) {
            $experience_levels = array(
                'entry-level' => 'Entry level',
                'mid-level' => 'Mid level',
                'senior-level' => 'Senior level',
                'executive' => 'Executive',
            );

            if (isset($experience_levels[$experience_level])) {
                $schema['experienceRequirements'] = $experience_levels[$experience_level];
            }
        }

        // Add required skills
        if (!empty($required_skills)) {
            if (is_array($required_skills)) {
                $schema['skills'] = $required_skills;
            } else {
                $schema['skills'] = explode(',', $required_skills);
            }
        }

        // Add benefits
        if (!empty($benefits)) {
            if (is_array($benefits)) {
                $schema['jobBenefits'] = $benefits;
            } else {
                $schema['jobBenefits'] = explode(',', $benefits);
            }
        }

        // Add industry if available
        $job_categories = get_the_terms($post->ID, 'job_category');
        if (!empty($job_categories) && !is_wp_error($job_categories)) {
            $schema['industry'] = wp_list_pluck($job_categories, 'name');
        }

        $this->output_schema_json($schema, 'jobposting');
    }

    /**
     * Output page-specific schema
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_page_specific_schema() {
        if (is_singular('post')) {
            $this->output_article_schema();
        } elseif (is_page()) {
            $this->output_webpage_schema();
        } elseif (is_author()) {
            $this->output_person_schema();
        } elseif (is_home() || is_category() || is_tag()) {
            $this->output_blog_schema();
        }
    }

    /**
     * Output article schema for blog posts
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_article_schema() {
        if (!$this->schema_settings['enable_article_schema']) {
            return;
        }

        global $post;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => get_permalink($post) . '#article',
            'headline' => get_the_title($post),
            'description' => get_the_excerpt($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
            'url' => get_permalink($post),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink($post),
            ),
        );

        // Add author information
        $author_id = $post->post_author;
        $schema['author'] = array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name', $author_id),
            'url' => get_author_posts_url($author_id),
        );

        // Add author image if available
        $author_avatar = get_avatar_url($author_id, array('size' => 300));
        if ($author_avatar) {
            $schema['author']['image'] = array(
                '@type' => 'ImageObject',
                'url' => $author_avatar,
                'width' => 300,
                'height' => 300,
            );
        }

        // Add publisher information
        $schema['publisher'] = array(
            '@type' => 'Organization',
            'name' => $this->organization_data['name'],
            'url' => $this->organization_data['url'],
        );

        if (!empty($this->organization_data['logo'])) {
            $schema['publisher']['logo'] = array(
                '@type' => 'ImageObject',
                'url' => $this->organization_data['logo'],
                'width' => 300,
                'height' => 300,
            );
        }

        // Add featured image
        if (has_post_thumbnail($post)) {
            $thumbnail_id = get_post_thumbnail_id($post);
            $thumbnail_url = get_the_post_thumbnail_url($post, 'full');
            $thumbnail_meta = wp_get_attachment_metadata($thumbnail_id);

            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $thumbnail_url,
                'width' => $thumbnail_meta['width'] ?? 1200,
                'height' => $thumbnail_meta['height'] ?? 630,
            );
        }

        // Add categories and tags
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            $schema['articleSection'] = wp_list_pluck($categories, 'name');
        }

        $tags = get_the_tags($post->ID);
        if (!empty($tags)) {
            $schema['keywords'] = wp_list_pluck($tags, 'name');
        }

        // Add word count
        $content = get_post_field('post_content', $post);
        $word_count = str_word_count(wp_strip_all_tags($content));
        if ($word_count > 0) {
            $schema['wordCount'] = $word_count;
        }

        $this->output_schema_json($schema, 'article');
    }

    /**
     * Output webpage schema for static pages
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_webpage_schema() {
        global $post;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => get_permalink($post) . '#webpage',
            'name' => get_the_title($post),
            'description' => get_the_excerpt($post) ?: wp_trim_words(get_the_content(), 20),
            'url' => get_permalink($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
        );

        // Add breadcrumb reference
        if ($this->schema_settings['enable_breadcrumb_schema']) {
            $schema['breadcrumb'] = array(
                '@type' => 'BreadcrumbList',
                '@id' => get_permalink($post) . '#breadcrumb',
            );
        }

        // Add organization reference
        $schema['publisher'] = array(
            '@id' => home_url() . '#organization',
        );

        $this->output_schema_json($schema, 'webpage');
    }

    /**
     * Output person schema for team members/author pages
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_person_schema() {
        if (!$this->schema_settings['enable_person_schema'] || !is_author()) {
            return;
        }

        $author_id = get_queried_object_id();

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            '@id' => get_author_posts_url($author_id) . '#person',
            'name' => get_the_author_meta('display_name', $author_id),
            'description' => get_the_author_meta('description', $author_id),
            'url' => get_author_posts_url($author_id),
        );

        // Add author image
        $author_avatar = get_avatar_url($author_id, array('size' => 300));
        if ($author_avatar) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $author_avatar,
                'width' => 300,
                'height' => 300,
            );
        }

        // Add job title if available (custom field)
        $job_title = get_the_author_meta('job_title', $author_id);
        if (!empty($job_title)) {
            $schema['jobTitle'] = $job_title;
        }

        // Add organization affiliation
        $schema['worksFor'] = array(
            '@id' => home_url() . '#organization',
        );

        // Add social profiles
        $social_fields = array(
            'twitter' => 'twitter',
            'linkedin' => 'linkedin_profile',
            'facebook' => 'facebook',
        );

        $social_profiles = array();
        foreach ($social_fields as $platform => $field) {
            $profile_url = get_the_author_meta($field, $author_id);
            if (!empty($profile_url)) {
                $social_profiles[] = $profile_url;
            }
        }

        if (!empty($social_profiles)) {
            $schema['sameAs'] = $social_profiles;
        }

        $this->output_schema_json($schema, 'person');
    }

    /**
     * Output blog schema for blog pages
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_blog_schema() {
        if (!is_home() && !is_category() && !is_tag()) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            '@id' => get_pagenum_link() . '#blog',
            'url' => get_pagenum_link(),
        );

        if (is_home()) {
            $schema['name'] = get_bloginfo('name') . ' Blog';
            $schema['description'] = get_bloginfo('description');
        } elseif (is_category()) {
            $category = get_queried_object();
            $schema['name'] = $category->name;
            $schema['description'] = $category->description;
        } elseif (is_tag()) {
            $tag = get_queried_object();
            $schema['name'] = $tag->name;
            $schema['description'] = $tag->description;
        }

        // Add publisher
        $schema['publisher'] = array(
            '@id' => home_url() . '#organization',
        );

        $this->output_schema_json($schema, 'blog');
    }

    /**
     * Generate breadcrumb schema
     * 
     * @since 1.0.0
     * @param array $breadcrumbs Breadcrumb items
     * @param array $args Breadcrumb arguments
     * @return string JSON-LD schema
     */
    public function generate_breadcrumb_schema($breadcrumbs, $args = array()) {
        if (!$this->schema_settings['enable_breadcrumb_schema'] || empty($breadcrumbs)) {
            return '';
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(),
        );

        $position = 1;
        foreach ($breadcrumbs as $breadcrumb) {
            $item = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $breadcrumb['title'] ?? $breadcrumb['text'],
            );

            if (!empty($breadcrumb['url'])) {
                $item['item'] = array(
                    '@type' => 'WebPage',
                    '@id' => $breadcrumb['url'],
                );
            }

            $schema['itemListElement'][] = $item;
            $position++;
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Output schema JSON-LD
     * 
     * @since 1.0.0
     * @param array $schema Schema data
     * @param string $type Schema type for identification
     * @return void
     */
    private function output_schema_json($schema, $type = '') {
        if (empty($schema)) {
            return;
        }

        // Validate schema if enabled
        if ($this->schema_settings['validate_schema']) {
            $schema = $this->validate_schema_data($schema);
        }

        // Apply filters for customization
        $schema = apply_filters('recruitpro_schema_output', $schema, $type);
        $schema = apply_filters("recruitpro_schema_output_{$type}", $schema);

        // Output JSON-LD
        echo '<script type="application/ld+json">';
        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo '</script>' . "\n";

        // Log for debugging if enabled
        if ($this->schema_settings['schema_debug_mode']) {
            error_log("RecruitPro Schema Output ({$type}): " . json_encode($schema));
        }
    }

    /**
     * Validate schema data
     * 
     * @since 1.0.0
     * @param array $schema Schema data
     * @return array Validated schema data
     */
    private function validate_schema_data($schema) {
        // Remove empty values
        $schema = array_filter($schema, function($value) {
            if (is_array($value)) {
                return !empty(array_filter($value));
            }
            return !empty($value);
        });

        // Ensure required fields are present based on type
        if (isset($schema['@type'])) {
            switch ($schema['@type']) {
                case 'JobPosting':
                    $required_fields = array('title', 'description', 'hiringOrganization');
                    break;
                case 'Organization':
                    $required_fields = array('name', 'url');
                    break;
                case 'Article':
                    $required_fields = array('headline', 'author', 'publisher');
                    break;
                default:
                    $required_fields = array();
            }

            foreach ($required_fields as $field) {
                if (empty($schema[$field])) {
                    error_log("RecruitPro Schema Validation Warning: Missing required field '{$field}' for type '{$schema['@type']}'");
                }
            }
        }

        return $schema;
    }

    /**
     * Schema validation check for admin
     * 
     * @since 1.0.0
     * @return void
     */
    public function schema_validation_check() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check for common schema issues
        $issues = array();

        // Check organization data completeness
        if (empty($this->organization_data['name'])) {
            $issues[] = __('Organization name is missing from schema configuration.', 'recruitpro');
        }

        if (empty($this->organization_data['logo'])) {
            $issues[] = __('Organization logo is missing for better schema markup.', 'recruitpro');
        }

        // Store issues for admin notice
        if (!empty($issues)) {
            set_transient('recruitpro_schema_issues', $issues, DAY_IN_SECONDS);
        }
    }

    /**
     * Output schema debug information
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_schema_debug() {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<!-- RecruitPro Schema Debug Information -->' . "\n";
        echo '<!-- Schema settings: ' . json_encode($this->schema_settings) . ' -->' . "\n";
        echo '<!-- Organization data: ' . json_encode($this->organization_data) . ' -->' . "\n";
        echo '<!-- Current page type: ' . $this->get_current_page_type() . ' -->' . "\n";
    }

    /**
     * Get current page type for debugging
     * 
     * @since 1.0.0
     * @return string Page type
     */
    private function get_current_page_type() {
        if (is_front_page()) return 'front_page';
        if (is_home()) return 'blog_home';
        if (is_singular('job')) return 'job_single';
        if (is_singular('post')) return 'post_single';
        if (is_page()) return 'page';
        if (is_category()) return 'category';
        if (is_tag()) return 'tag';
        if (is_author()) return 'author';
        if (is_search()) return 'search';
        if (is_404()) return '404';
        return 'unknown';
    }

    /**
     * Add schema settings to customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_schema($wp_customize) {
        // Schema section
        $wp_customize->add_section('recruitpro_schema', array(
            'title' => __('Schema & Structured Data', 'recruitpro'),
            'description' => __('Configure structured data for better SEO and search visibility.', 'recruitpro'),
            'priority' => 50,
        ));

        // Enable schema
        $wp_customize->add_setting('recruitpro_enable_schema', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_schema', array(
            'label' => __('Enable Schema Markup', 'recruitpro'),
            'description' => __('Enable structured data output for better SEO.', 'recruitpro'),
            'section' => 'recruitpro_schema',
            'type' => 'checkbox',
        ));

        // Enable Google Jobs schema
        $wp_customize->add_setting('recruitpro_enable_google_jobs_schema', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_google_jobs_schema', array(
            'label' => __('Enable Google Jobs Schema', 'recruitpro'),
            'description' => __('Add JobPosting structured data for Google Jobs integration.', 'recruitpro'),
            'section' => 'recruitpro_schema',
            'type' => 'checkbox',
        ));

        // Company information section
        $wp_customize->add_setting('recruitpro_company_legal_name', array(
            'default' => get_bloginfo('name'),
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_company_legal_name', array(
            'label' => __('Company Legal Name', 'recruitpro'),
            'description' => __('Official legal name of your recruitment agency.', 'recruitpro'),
            'section' => 'recruitpro_schema',
            'type' => 'text',
        ));

        // Company industry
        $wp_customize->add_setting('recruitpro_company_industry', array(
            'default' => 'Human Resources',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_company_industry', array(
            'label' => __('Company Industry', 'recruitpro'),
            'section' => 'recruitpro_schema',
            'type' => 'select',
            'choices' => array(
                'Human Resources' => __('Human Resources', 'recruitpro'),
                'Staffing and Recruiting' => __('Staffing and Recruiting', 'recruitpro'),
                'Management Consulting' => __('Management Consulting', 'recruitpro'),
                'Professional Services' => __('Professional Services', 'recruitpro'),
            ),
        ));

        // Schema debug mode
        $wp_customize->add_setting('recruitpro_schema_debug_mode', array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_schema_debug_mode', array(
            'label' => __('Schema Debug Mode', 'recruitpro'),
            'description' => __('Enable debug output for schema development (for developers only).', 'recruitpro'),
            'section' => 'recruitpro_schema',
            'type' => 'checkbox',
        ));
    }
}

// Initialize the schema manager
if (class_exists('RecruitPro_Schema_Manager')) {
    new RecruitPro_Schema_Manager();
}

/**
 * Helper function to output schema markup
 * 
 * @since 1.0.0
 * @param array $schema Schema data
 * @param string $type Schema type
 * @return void
 */
function recruitpro_output_schema($schema, $type = '') {
    $schema_manager = new RecruitPro_Schema_Manager();
    echo '<script type="application/ld+json">';
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    echo '</script>' . "\n";
}

/**
 * Helper function to get organization schema
 * 
 * @since 1.0.0
 * @return array Organization schema data
 */
function recruitpro_get_organization_schema() {
    $schema_manager = new RecruitPro_Schema_Manager();
    return $schema_manager->organization_data;
}

/**
 * Helper function to check if schema is enabled
 * 
 * @since 1.0.0
 * @return bool True if schema is enabled
 */
function recruitpro_schema_enabled() {
    return get_theme_mod('recruitpro_enable_schema', true);
}

/**
 * Helper function to get job schema data
 * 
 * @since 1.0.0
 * @param int $job_id Job post ID
 * @return array Job schema data
 */
function recruitpro_get_job_schema($job_id) {
    if (!recruitpro_schema_enabled() || get_post_type($job_id) !== 'job') {
        return array();
    }

    // This would typically integrate with the jobs plugin
    // For now, return basic structure
    return array(
        '@context' => 'https://schema.org',
        '@type' => 'JobPosting',
        'title' => get_the_title($job_id),
        'description' => get_the_excerpt($job_id),
        'datePosted' => get_the_date('c', $job_id),
        'url' => get_permalink($job_id),
    );
}