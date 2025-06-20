<?php
/**
 * RecruitPro Theme Taxonomies Management
 *
 * This file handles theme-level custom taxonomies for recruitment websites.
 * Manages taxonomies for custom post types, industry classifications, skill sets,
 * and recruitment-specific categorization. Works alongside but separate from
 * Jobs plugin taxonomies to avoid conflicts.
 *
 * @package RecruitPro
 * @subpackage Theme/Taxonomies
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/taxonomies.php
 * Purpose: Theme-level taxonomy registration and management
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality, separate from Jobs plugin)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Taxonomies Manager Class
 * 
 * Handles all theme-level taxonomy functionality including registration,
 * management, admin interfaces, and recruitment-specific categorization.
 *
 * @since 1.0.0
 */
class RecruitPro_Taxonomies_Manager {

    /**
     * Theme taxonomies configuration
     * 
     * @since 1.0.0
     * @var array
     */
    private $theme_taxonomies = array();

    /**
     * Industry classifications
     * 
     * @since 1.0.0
     * @var array
     */
    private $industry_classifications = array();

    /**
     * Skill categories for content organization
     * 
     * @since 1.0.0
     * @var array
     */
    private $skill_categories = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_taxonomy_configurations();
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        add_action('init', array($this, 'register_theme_taxonomies'), 5);
        add_action('init', array($this, 'register_industry_taxonomies'), 6);
        add_action('init', array($this, 'register_skill_taxonomies'), 7);
        
        // Admin customizations
        add_action('admin_init', array($this, 'customize_taxonomy_admin'));
        add_filter('manage_edit-testimonial_category_columns', array($this, 'add_custom_taxonomy_columns'));
        add_filter('manage_testimonial_category_custom_column', array($this, 'fill_custom_taxonomy_columns'), 10, 3);
        
        // Frontend enhancements
        add_action('wp_enqueue_scripts', array($this, 'enqueue_taxonomy_styles'));
        add_filter('get_the_archive_title', array($this, 'customize_archive_titles'));
        add_filter('get_the_archive_description', array($this, 'customize_archive_descriptions'));
        
        // REST API support
        add_action('rest_api_init', array($this, 'register_taxonomy_rest_fields'));
        
        // Widget and shortcode support
        add_action('widgets_init', array($this, 'register_taxonomy_widgets'));
        add_shortcode('recruitpro_taxonomy_list', array($this, 'taxonomy_list_shortcode'));
        add_shortcode('recruitpro_taxonomy_cloud', array($this, 'taxonomy_cloud_shortcode'));
        
        // Schema.org markup
        add_action('wp_head', array($this, 'add_taxonomy_schema'));
        
        // Admin menu and pages
        add_action('admin_menu', array($this, 'add_taxonomy_admin_pages'));
        add_action('wp_ajax_recruitpro_taxonomy_bulk_action', array($this, 'handle_taxonomy_bulk_actions'));
    }

    /**
     * Initialize taxonomy configurations
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_taxonomy_configurations() {
        $this->init_theme_taxonomies();
        $this->init_industry_classifications();
        $this->init_skill_categories();
    }

    /**
     * Initialize theme-level taxonomies
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_theme_taxonomies() {
        $this->theme_taxonomies = array(
            'testimonial_category' => array(
                'post_types' => array('testimonial'),
                'labels' => array(
                    'name' => __('Testimonial Categories', 'recruitpro'),
                    'singular_name' => __('Testimonial Category', 'recruitpro'),
                    'menu_name' => __('Categories', 'recruitpro'),
                    'search_items' => __('Search Categories', 'recruitpro'),
                    'all_items' => __('All Categories', 'recruitpro'),
                    'parent_item' => __('Parent Category', 'recruitpro'),
                    'parent_item_colon' => __('Parent Category:', 'recruitpro'),
                    'edit_item' => __('Edit Category', 'recruitpro'),
                    'update_item' => __('Update Category', 'recruitpro'),
                    'add_new_item' => __('Add New Category', 'recruitpro'),
                    'new_item_name' => __('New Category Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'testimonial-category'),
                    'meta_box_cb' => 'post_categories_meta_box',
                ),
                'description' => __('Categorize testimonials by client type, industry, or service', 'recruitpro'),
            ),
            'testimonial_source' => array(
                'post_types' => array('testimonial'),
                'labels' => array(
                    'name' => __('Testimonial Sources', 'recruitpro'),
                    'singular_name' => __('Testimonial Source', 'recruitpro'),
                    'menu_name' => __('Sources', 'recruitpro'),
                    'search_items' => __('Search Sources', 'recruitpro'),
                    'all_items' => __('All Sources', 'recruitpro'),
                    'edit_item' => __('Edit Source', 'recruitpro'),
                    'update_item' => __('Update Source', 'recruitpro'),
                    'add_new_item' => __('Add New Source', 'recruitpro'),
                    'new_item_name' => __('New Source Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'testimonial-source'),
                ),
                'description' => __('Track where testimonials came from (LinkedIn, Google, Email, etc.)', 'recruitpro'),
            ),
            'team_department' => array(
                'post_types' => array('team_member'),
                'labels' => array(
                    'name' => __('Departments', 'recruitpro'),
                    'singular_name' => __('Department', 'recruitpro'),
                    'menu_name' => __('Departments', 'recruitpro'),
                    'search_items' => __('Search Departments', 'recruitpro'),
                    'all_items' => __('All Departments', 'recruitpro'),
                    'parent_item' => __('Parent Department', 'recruitpro'),
                    'parent_item_colon' => __('Parent Department:', 'recruitpro'),
                    'edit_item' => __('Edit Department', 'recruitpro'),
                    'update_item' => __('Update Department', 'recruitpro'),
                    'add_new_item' => __('Add New Department', 'recruitpro'),
                    'new_item_name' => __('New Department Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'department'),
                ),
                'description' => __('Organize team members by department (HR, Sales, Operations, etc.)', 'recruitpro'),
            ),
            'team_expertise' => array(
                'post_types' => array('team_member'),
                'labels' => array(
                    'name' => __('Expertise Areas', 'recruitpro'),
                    'singular_name' => __('Expertise Area', 'recruitpro'),
                    'menu_name' => __('Expertise', 'recruitpro'),
                    'search_items' => __('Search Expertise', 'recruitpro'),
                    'all_items' => __('All Expertise', 'recruitpro'),
                    'edit_item' => __('Edit Expertise', 'recruitpro'),
                    'update_item' => __('Update Expertise', 'recruitpro'),
                    'add_new_item' => __('Add New Expertise', 'recruitpro'),
                    'new_item_name' => __('New Expertise Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'expertise'),
                ),
                'description' => __('Tag team members by their areas of expertise', 'recruitpro'),
            ),
            'service_category' => array(
                'post_types' => array('service'),
                'labels' => array(
                    'name' => __('Service Categories', 'recruitpro'),
                    'singular_name' => __('Service Category', 'recruitpro'),
                    'menu_name' => __('Categories', 'recruitpro'),
                    'search_items' => __('Search Categories', 'recruitpro'),
                    'all_items' => __('All Categories', 'recruitpro'),
                    'parent_item' => __('Parent Category', 'recruitpro'),
                    'parent_item_colon' => __('Parent Category:', 'recruitpro'),
                    'edit_item' => __('Edit Category', 'recruitpro'),
                    'update_item' => __('Update Category', 'recruitpro'),
                    'add_new_item' => __('Add New Category', 'recruitpro'),
                    'new_item_name' => __('New Category Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'service-category'),
                ),
                'description' => __('Categorize services (Recruitment, HR Consulting, Training, etc.)', 'recruitpro'),
            ),
            'service_delivery_method' => array(
                'post_types' => array('service'),
                'labels' => array(
                    'name' => __('Delivery Methods', 'recruitpro'),
                    'singular_name' => __('Delivery Method', 'recruitpro'),
                    'menu_name' => __('Delivery', 'recruitpro'),
                    'search_items' => __('Search Methods', 'recruitpro'),
                    'all_items' => __('All Methods', 'recruitpro'),
                    'edit_item' => __('Edit Method', 'recruitpro'),
                    'update_item' => __('Update Method', 'recruitpro'),
                    'add_new_item' => __('Add New Method', 'recruitpro'),
                    'new_item_name' => __('New Method Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'delivery-method'),
                ),
                'description' => __('How services are delivered (On-site, Remote, Hybrid)', 'recruitpro'),
            ),
            'success_story_category' => array(
                'post_types' => array('success_story'),
                'labels' => array(
                    'name' => __('Success Story Categories', 'recruitpro'),
                    'singular_name' => __('Success Story Category', 'recruitpro'),
                    'menu_name' => __('Categories', 'recruitpro'),
                    'search_items' => __('Search Categories', 'recruitpro'),
                    'all_items' => __('All Categories', 'recruitpro'),
                    'parent_item' => __('Parent Category', 'recruitpro'),
                    'parent_item_colon' => __('Parent Category:', 'recruitpro'),
                    'edit_item' => __('Edit Category', 'recruitpro'),
                    'update_item' => __('Update Category', 'recruitpro'),
                    'add_new_item' => __('Add New Category', 'recruitpro'),
                    'new_item_name' => __('New Category Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_admin_column' => true,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'success-category'),
                ),
                'description' => __('Categorize success stories by type or outcome', 'recruitpro'),
            ),
        );
    }

    /**
     * Initialize industry classifications
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_industry_classifications() {
        $this->industry_classifications = array(
            'recruitment_industry' => array(
                'post_types' => array('post', 'page', 'testimonial', 'success_story'),
                'labels' => array(
                    'name' => __('Industries', 'recruitpro'),
                    'singular_name' => __('Industry', 'recruitpro'),
                    'menu_name' => __('Industries', 'recruitpro'),
                    'search_items' => __('Search Industries', 'recruitpro'),
                    'all_items' => __('All Industries', 'recruitpro'),
                    'parent_item' => __('Parent Industry', 'recruitpro'),
                    'parent_item_colon' => __('Parent Industry:', 'recruitpro'),
                    'edit_item' => __('Edit Industry', 'recruitpro'),
                    'update_item' => __('Update Industry', 'recruitpro'),
                    'add_new_item' => __('Add New Industry', 'recruitpro'),
                    'new_item_name' => __('New Industry Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'industry'),
                ),
                'description' => __('Classify content by industry sector for better organization', 'recruitpro'),
                'predefined_terms' => array(
                    'technology' => __('Technology', 'recruitpro'),
                    'healthcare' => __('Healthcare', 'recruitpro'),
                    'finance' => __('Finance & Banking', 'recruitpro'),
                    'manufacturing' => __('Manufacturing', 'recruitpro'),
                    'retail' => __('Retail & E-commerce', 'recruitpro'),
                    'education' => __('Education', 'recruitpro'),
                    'construction' => __('Construction', 'recruitpro'),
                    'hospitality' => __('Hospitality & Tourism', 'recruitpro'),
                    'energy' => __('Energy & Utilities', 'recruitpro'),
                    'government' => __('Government & Public Sector', 'recruitpro'),
                    'non-profit' => __('Non-Profit', 'recruitpro'),
                    'media' => __('Media & Entertainment', 'recruitpro'),
                    'transportation' => __('Transportation & Logistics', 'recruitpro'),
                    'real-estate' => __('Real Estate', 'recruitpro'),
                    'legal' => __('Legal Services', 'recruitpro'),
                ),
            ),
            'recruitment_specialization' => array(
                'post_types' => array('post', 'page', 'team_member', 'service'),
                'labels' => array(
                    'name' => __('Recruitment Specializations', 'recruitpro'),
                    'singular_name' => __('Specialization', 'recruitpro'),
                    'menu_name' => __('Specializations', 'recruitpro'),
                    'search_items' => __('Search Specializations', 'recruitpro'),
                    'all_items' => __('All Specializations', 'recruitpro'),
                    'edit_item' => __('Edit Specialization', 'recruitpro'),
                    'update_item' => __('Update Specialization', 'recruitpro'),
                    'add_new_item' => __('Add New Specialization', 'recruitpro'),
                    'new_item_name' => __('New Specialization Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'specialization'),
                ),
                'description' => __('Recruitment specialization areas and focus domains', 'recruitpro'),
                'predefined_terms' => array(
                    'executive-search' => __('Executive Search', 'recruitpro'),
                    'permanent-placement' => __('Permanent Placement', 'recruitpro'),
                    'temporary-staffing' => __('Temporary Staffing', 'recruitpro'),
                    'contract-recruitment' => __('Contract Recruitment', 'recruitpro'),
                    'volume-recruitment' => __('Volume Recruitment', 'recruitpro'),
                    'graduate-recruitment' => __('Graduate Recruitment', 'recruitpro'),
                    'international-recruitment' => __('International Recruitment', 'recruitpro'),
                    'diversity-recruitment' => __('Diversity & Inclusion', 'recruitpro'),
                    'remote-recruitment' => __('Remote Workforce', 'recruitpro'),
                    'startup-recruitment' => __('Startup Recruitment', 'recruitpro'),
                ),
            ),
            'content_topics' => array(
                'post_types' => array('post'),
                'labels' => array(
                    'name' => __('Content Topics', 'recruitpro'),
                    'singular_name' => __('Topic', 'recruitpro'),
                    'menu_name' => __('Topics', 'recruitpro'),
                    'search_items' => __('Search Topics', 'recruitpro'),
                    'all_items' => __('All Topics', 'recruitpro'),
                    'edit_item' => __('Edit Topic', 'recruitpro'),
                    'update_item' => __('Update Topic', 'recruitpro'),
                    'add_new_item' => __('Add New Topic', 'recruitpro'),
                    'new_item_name' => __('New Topic Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'topic'),
                ),
                'description' => __('Organize blog content by recruitment and HR topics', 'recruitpro'),
                'predefined_terms' => array(
                    'career-advice' => __('Career Advice', 'recruitpro'),
                    'interview-tips' => __('Interview Tips', 'recruitpro'),
                    'resume-writing' => __('Resume Writing', 'recruitpro'),
                    'job-search-strategies' => __('Job Search Strategies', 'recruitpro'),
                    'salary-negotiation' => __('Salary Negotiation', 'recruitpro'),
                    'workplace-culture' => __('Workplace Culture', 'recruitpro'),
                    'industry-insights' => __('Industry Insights', 'recruitpro'),
                    'hr-trends' => __('HR Trends', 'recruitpro'),
                    'recruitment-technology' => __('Recruitment Technology', 'recruitpro'),
                    'employer-branding' => __('Employer Branding', 'recruitpro'),
                ),
            ),
        );
    }

    /**
     * Initialize skill categories
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_skill_categories() {
        $this->skill_categories = array(
            'skill_category' => array(
                'post_types' => array('post', 'team_member'),
                'labels' => array(
                    'name' => __('Skill Categories', 'recruitpro'),
                    'singular_name' => __('Skill Category', 'recruitpro'),
                    'menu_name' => __('Skill Categories', 'recruitpro'),
                    'search_items' => __('Search Skill Categories', 'recruitpro'),
                    'all_items' => __('All Skill Categories', 'recruitpro'),
                    'parent_item' => __('Parent Category', 'recruitpro'),
                    'parent_item_colon' => __('Parent Category:', 'recruitpro'),
                    'edit_item' => __('Edit Skill Category', 'recruitpro'),
                    'update_item' => __('Update Skill Category', 'recruitpro'),
                    'add_new_item' => __('Add New Skill Category', 'recruitpro'),
                    'new_item_name' => __('New Skill Category Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => true,
                    'show_ui' => true,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'skill-category'),
                ),
                'description' => __('Categorize skills by type (Technical, Soft Skills, Certifications)', 'recruitpro'),
                'predefined_terms' => array(
                    'technical-skills' => __('Technical Skills', 'recruitpro'),
                    'soft-skills' => __('Soft Skills', 'recruitpro'),
                    'leadership-skills' => __('Leadership Skills', 'recruitpro'),
                    'communication-skills' => __('Communication Skills', 'recruitpro'),
                    'analytical-skills' => __('Analytical Skills', 'recruitpro'),
                    'project-management' => __('Project Management', 'recruitpro'),
                    'sales-skills' => __('Sales Skills', 'recruitpro'),
                    'digital-skills' => __('Digital Skills', 'recruitpro'),
                    'languages' => __('Languages', 'recruitpro'),
                    'certifications' => __('Certifications', 'recruitpro'),
                ),
            ),
            'experience_level' => array(
                'post_types' => array('post', 'testimonial', 'success_story'),
                'labels' => array(
                    'name' => __('Experience Levels', 'recruitpro'),
                    'singular_name' => __('Experience Level', 'recruitpro'),
                    'menu_name' => __('Experience Levels', 'recruitpro'),
                    'search_items' => __('Search Experience Levels', 'recruitpro'),
                    'all_items' => __('All Experience Levels', 'recruitpro'),
                    'edit_item' => __('Edit Experience Level', 'recruitpro'),
                    'update_item' => __('Update Experience Level', 'recruitpro'),
                    'add_new_item' => __('Add New Experience Level', 'recruitpro'),
                    'new_item_name' => __('New Experience Level Name', 'recruitpro'),
                ),
                'args' => array(
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_admin_column' => false,
                    'query_var' => true,
                    'show_in_rest' => true,
                    'rewrite' => array('slug' => 'experience-level'),
                ),
                'description' => __('Tag content by target experience level', 'recruitpro'),
                'predefined_terms' => array(
                    'entry-level' => __('Entry Level', 'recruitpro'),
                    'junior' => __('Junior (1-3 years)', 'recruitpro'),
                    'mid-level' => __('Mid-Level (3-7 years)', 'recruitpro'),
                    'senior' => __('Senior (7+ years)', 'recruitpro'),
                    'lead' => __('Lead/Principal', 'recruitpro'),
                    'management' => __('Management', 'recruitpro'),
                    'executive' => __('Executive/C-Level', 'recruitpro'),
                    'director' => __('Director', 'recruitpro'),
                ),
            ),
        );
    }

    /**
     * Register theme-level taxonomies
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_theme_taxonomies() {
        foreach ($this->theme_taxonomies as $taxonomy => $config) {
            register_taxonomy($taxonomy, $config['post_types'], $config['args']);
            
            // Add predefined terms if specified
            if (isset($config['predefined_terms'])) {
                $this->add_predefined_terms($taxonomy, $config['predefined_terms']);
            }
        }
    }

    /**
     * Register industry classification taxonomies
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_industry_taxonomies() {
        foreach ($this->industry_classifications as $taxonomy => $config) {
            register_taxonomy($taxonomy, $config['post_types'], $config['args']);
            
            // Add predefined terms
            if (isset($config['predefined_terms'])) {
                $this->add_predefined_terms($taxonomy, $config['predefined_terms']);
            }
        }
    }

    /**
     * Register skill-related taxonomies
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_skill_taxonomies() {
        foreach ($this->skill_categories as $taxonomy => $config) {
            register_taxonomy($taxonomy, $config['post_types'], $config['args']);
            
            // Add predefined terms
            if (isset($config['predefined_terms'])) {
                $this->add_predefined_terms($taxonomy, $config['predefined_terms']);
            }
        }
    }

    /**
     * Add predefined terms to taxonomy
     * 
     * @since 1.0.0
     * @param string $taxonomy Taxonomy name
     * @param array $terms Array of terms
     * @return void
     */
    private function add_predefined_terms($taxonomy, $terms) {
        // Only add terms on theme activation to avoid duplicates
        if (get_option('recruitpro_' . $taxonomy . '_terms_added')) {
            return;
        }

        foreach ($terms as $slug => $name) {
            if (!term_exists($slug, $taxonomy)) {
                wp_insert_term($name, $taxonomy, array('slug' => $slug));
            }
        }

        // Mark terms as added
        update_option('recruitpro_' . $taxonomy . '_terms_added', true);
    }

    /**
     * Customize taxonomy admin interface
     * 
     * @since 1.0.0
     * @return void
     */
    public function customize_taxonomy_admin() {
        // Add custom fields to taxonomy edit forms
        foreach (array_keys($this->theme_taxonomies) as $taxonomy) {
            add_action($taxonomy . '_add_form_fields', array($this, 'add_taxonomy_custom_fields'));
            add_action($taxonomy . '_edit_form_fields', array($this, 'edit_taxonomy_custom_fields'), 10, 2);
            add_action('edited_' . $taxonomy, array($this, 'save_taxonomy_custom_fields'));
            add_action('create_' . $taxonomy, array($this, 'save_taxonomy_custom_fields'));
        }
    }

    /**
     * Add custom fields to taxonomy add form
     * 
     * @since 1.0.0
     * @param string $taxonomy Taxonomy name
     * @return void
     */
    public function add_taxonomy_custom_fields($taxonomy) {
        ?>
        <div class="form-field term-icon-wrap">
            <label for="term-icon"><?php _e('Icon', 'recruitpro'); ?></label>
            <input type="text" name="term_icon" id="term-icon" value="" size="40" placeholder="fas fa-briefcase">
            <p><?php _e('Font Awesome icon class for this term', 'recruitpro'); ?></p>
        </div>
        
        <div class="form-field term-color-wrap">
            <label for="term-color"><?php _e('Color', 'recruitpro'); ?></label>
            <input type="color" name="term_color" id="term-color" value="#0073aa">
            <p><?php _e('Display color for this term', 'recruitpro'); ?></p>
        </div>
        
        <div class="form-field term-featured-wrap">
            <label for="term-featured">
                <input type="checkbox" name="term_featured" id="term-featured" value="1">
                <?php _e('Featured', 'recruitpro'); ?>
            </label>
            <p><?php _e('Mark this term as featured for special display', 'recruitpro'); ?></p>
        </div>
        <?php
    }

    /**
     * Add custom fields to taxonomy edit form
     * 
     * @since 1.0.0
     * @param WP_Term $term Current term object
     * @param string $taxonomy Current taxonomy slug
     * @return void
     */
    public function edit_taxonomy_custom_fields($term, $taxonomy) {
        $term_icon = get_term_meta($term->term_id, 'term_icon', true);
        $term_color = get_term_meta($term->term_id, 'term_color', true);
        $term_featured = get_term_meta($term->term_id, 'term_featured', true);
        ?>
        <tr class="form-field term-icon-wrap">
            <th scope="row"><label for="term-icon"><?php _e('Icon', 'recruitpro'); ?></label></th>
            <td>
                <input type="text" name="term_icon" id="term-icon" value="<?php echo esc_attr($term_icon); ?>" size="40" placeholder="fas fa-briefcase">
                <p class="description"><?php _e('Font Awesome icon class for this term', 'recruitpro'); ?></p>
            </td>
        </tr>
        
        <tr class="form-field term-color-wrap">
            <th scope="row"><label for="term-color"><?php _e('Color', 'recruitpro'); ?></label></th>
            <td>
                <input type="color" name="term_color" id="term-color" value="<?php echo esc_attr($term_color ?: '#0073aa'); ?>">
                <p class="description"><?php _e('Display color for this term', 'recruitpro'); ?></p>
            </td>
        </tr>
        
        <tr class="form-field term-featured-wrap">
            <th scope="row"><?php _e('Featured', 'recruitpro'); ?></th>
            <td>
                <label for="term-featured">
                    <input type="checkbox" name="term_featured" id="term-featured" value="1" <?php checked($term_featured, '1'); ?>>
                    <?php _e('Mark this term as featured for special display', 'recruitpro'); ?>
                </label>
            </td>
        </tr>
        <?php
    }

    /**
     * Save taxonomy custom fields
     * 
     * @since 1.0.0
     * @param int $term_id Term ID
     * @return void
     */
    public function save_taxonomy_custom_fields($term_id) {
        if (isset($_POST['term_icon'])) {
            update_term_meta($term_id, 'term_icon', sanitize_text_field($_POST['term_icon']));
        }
        
        if (isset($_POST['term_color'])) {
            update_term_meta($term_id, 'term_color', sanitize_hex_color($_POST['term_color']));
        }
        
        $term_featured = isset($_POST['term_featured']) ? '1' : '0';
        update_term_meta($term_id, 'term_featured', $term_featured);
    }

    /**
     * Add custom columns to taxonomy admin
     * 
     * @since 1.0.0
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function add_custom_taxonomy_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['name'] = $columns['name'];
        $new_columns['icon'] = __('Icon', 'recruitpro');
        $new_columns['color'] = __('Color', 'recruitpro');
        $new_columns['featured'] = __('Featured', 'recruitpro');
        $new_columns['posts'] = $columns['posts'];
        
        return $new_columns;
    }

    /**
     * Fill custom taxonomy columns
     * 
     * @since 1.0.0
     * @param string $content Column content
     * @param string $column_name Column name
     * @param int $term_id Term ID
     * @return string Column content
     */
    public function fill_custom_taxonomy_columns($content, $column_name, $term_id) {
        switch ($column_name) {
            case 'icon':
                $icon = get_term_meta($term_id, 'term_icon', true);
                if ($icon) {
                    $content = '<i class="' . esc_attr($icon) . '"></i> ' . esc_html($icon);
                } else {
                    $content = '<span class="dashicons dashicons-minus"></span>';
                }
                break;
                
            case 'color':
                $color = get_term_meta($term_id, 'term_color', true);
                if ($color) {
                    $content = '<span class="color-indicator" style="background-color: ' . esc_attr($color) . '; width: 20px; height: 20px; display: inline-block; border-radius: 3px; border: 1px solid #ddd;"></span> ' . esc_html($color);
                } else {
                    $content = '<span class="dashicons dashicons-minus"></span>';
                }
                break;
                
            case 'featured':
                $featured = get_term_meta($term_id, 'term_featured', true);
                if ($featured) {
                    $content = '<span class="dashicons dashicons-star-filled" style="color: #ffb900;"></span>';
                } else {
                    $content = '<span class="dashicons dashicons-star-empty" style="color: #ddd;"></span>';
                }
                break;
        }
        
        return $content;
    }

    /**
     * Enqueue taxonomy-related styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_taxonomy_styles() {
        wp_enqueue_style(
            'recruitpro-taxonomies',
            get_template_directory_uri() . '/assets/css/taxonomies.css',
            array(),
            RECRUITPRO_THEME_VERSION
        );
    }

    /**
     * Customize archive titles
     * 
     * @since 1.0.0
     * @param string $title Archive title
     * @return string Modified title
     */
    public function customize_archive_titles($title) {
        if (is_tax()) {
            $term = get_queried_object();
            $taxonomy_config = $this->get_taxonomy_config($term->taxonomy);
            
            if ($taxonomy_config) {
                $icon = get_term_meta($term->term_id, 'term_icon', true);
                if ($icon) {
                    $title = '<i class="' . esc_attr($icon) . '"></i> ' . $term->name;
                } else {
                    $title = $term->name;
                }
            }
        }
        
        return $title;
    }

    /**
     * Customize archive descriptions
     * 
     * @since 1.0.0
     * @param string $description Archive description
     * @return string Modified description
     */
    public function customize_archive_descriptions($description) {
        if (is_tax() && empty($description)) {
            $term = get_queried_object();
            $taxonomy_config = $this->get_taxonomy_config($term->taxonomy);
            
            if ($taxonomy_config && isset($taxonomy_config['description'])) {
                $description = sprintf(
                    __('Browse all content related to %s. %s', 'recruitpro'),
                    $term->name,
                    $taxonomy_config['description']
                );
            }
        }
        
        return $description;
    }

    /**
     * Register REST API fields for taxonomies
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_taxonomy_rest_fields() {
        $taxonomies = array_merge(
            array_keys($this->theme_taxonomies),
            array_keys($this->industry_classifications),
            array_keys($this->skill_categories)
        );

        foreach ($taxonomies as $taxonomy) {
            register_rest_field($taxonomy, 'term_icon', array(
                'get_callback' => function($term) {
                    return get_term_meta($term['id'], 'term_icon', true);
                },
                'schema' => array(
                    'description' => __('Term icon class', 'recruitpro'),
                    'type' => 'string',
                ),
            ));

            register_rest_field($taxonomy, 'term_color', array(
                'get_callback' => function($term) {
                    return get_term_meta($term['id'], 'term_color', true);
                },
                'schema' => array(
                    'description' => __('Term color', 'recruitpro'),
                    'type' => 'string',
                ),
            ));

            register_rest_field($taxonomy, 'term_featured', array(
                'get_callback' => function($term) {
                    return get_term_meta($term['id'], 'term_featured', true);
                },
                'schema' => array(
                    'description' => __('Is term featured', 'recruitpro'),
                    'type' => 'boolean',
                ),
            ));
        }
    }

    /**
     * Register taxonomy widgets
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_taxonomy_widgets() {
        require_once get_template_directory() . '/inc/widgets/class-taxonomy-cloud-widget.php';
        require_once get_template_directory() . '/inc/widgets/class-featured-terms-widget.php';
        
        register_widget('RecruitPro_Taxonomy_Cloud_Widget');
        register_widget('RecruitPro_Featured_Terms_Widget');
    }

    /**
     * Taxonomy list shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Taxonomy list HTML
     */
    public function taxonomy_list_shortcode($atts) {
        $atts = shortcode_atts(array(
            'taxonomy' => 'recruitment_industry',
            'number' => 10,
            'style' => 'list', // list, grid, inline
            'show_count' => 'false',
            'show_icons' => 'true',
            'featured_only' => 'false',
            'class' => '',
        ), $atts);

        $args = array(
            'taxonomy' => $atts['taxonomy'],
            'number' => intval($atts['number']),
            'hide_empty' => true,
        );

        if ($atts['featured_only'] === 'true') {
            $args['meta_query'] = array(
                array(
                    'key' => 'term_featured',
                    'value' => '1',
                    'compare' => '='
                )
            );
        }

        $terms = get_terms($args);

        if (empty($terms) || is_wp_error($terms)) {
            return '<p>' . __('No terms found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-taxonomy-list', 'style-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if ($atts['style'] === 'list'): ?>
                <ul class="taxonomy-list">
                    <?php foreach ($terms as $term): ?>
                        <li class="taxonomy-item">
                            <a href="<?php echo esc_url(get_term_link($term)); ?>" class="taxonomy-link">
                                <?php if ($atts['show_icons'] === 'true'): ?>
                                    <?php $icon = get_term_meta($term->term_id, 'term_icon', true); ?>
                                    <?php if ($icon): ?>
                                        <i class="<?php echo esc_attr($icon); ?>"></i>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <span class="taxonomy-name"><?php echo esc_html($term->name); ?></span>
                                <?php if ($atts['show_count'] === 'true'): ?>
                                    <span class="taxonomy-count">(<?php echo $term->count; ?>)</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="taxonomy-grid">
                    <?php foreach ($terms as $term): ?>
                        <div class="taxonomy-item">
                            <a href="<?php echo esc_url(get_term_link($term)); ?>" class="taxonomy-link">
                                <?php if ($atts['show_icons'] === 'true'): ?>
                                    <?php $icon = get_term_meta($term->term_id, 'term_icon', true); ?>
                                    <?php if ($icon): ?>
                                        <i class="<?php echo esc_attr($icon); ?>"></i>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <span class="taxonomy-name"><?php echo esc_html($term->name); ?></span>
                                <?php if ($atts['show_count'] === 'true'): ?>
                                    <span class="taxonomy-count">(<?php echo $term->count; ?>)</span>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Taxonomy cloud shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Taxonomy cloud HTML
     */
    public function taxonomy_cloud_shortcode($atts) {
        $atts = shortcode_atts(array(
            'taxonomy' => 'recruitment_industry',
            'number' => 20,
            'smallest' => 12,
            'largest' => 22,
            'unit' => 'px',
            'class' => '',
        ), $atts);

        $terms = get_terms(array(
            'taxonomy' => $atts['taxonomy'],
            'number' => intval($atts['number']),
            'hide_empty' => true,
        ));

        if (empty($terms) || is_wp_error($terms)) {
            return '<p>' . __('No terms found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-taxonomy-cloud');
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        // Calculate font sizes based on post count
        $min_count = min(array_column($terms, 'count'));
        $max_count = max(array_column($terms, 'count'));
        $range = max($max_count - $min_count, 1);

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($terms as $term): ?>
                <?php
                $size = $atts['smallest'] + (($term->count - $min_count) / $range) * ($atts['largest'] - $atts['smallest']);
                $color = get_term_meta($term->term_id, 'term_color', true);
                $style = 'font-size: ' . round($size) . $atts['unit'] . ';';
                if ($color) {
                    $style .= ' color: ' . $color . ';';
                }
                ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>" 
                   class="taxonomy-cloud-item" 
                   style="<?php echo esc_attr($style); ?>"
                   title="<?php echo esc_attr($term->name . ' (' . $term->count . ')'); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Add taxonomy schema markup
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_taxonomy_schema() {
        if (!is_tax()) {
            return;
        }

        $term = get_queried_object();
        $taxonomy_config = $this->get_taxonomy_config($term->taxonomy);

        if (!$taxonomy_config) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $term->name,
            'description' => $term->description ?: $taxonomy_config['description'],
            'url' => get_term_link($term),
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }

    /**
     * Add taxonomy admin pages
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_taxonomy_admin_pages() {
        add_theme_page(
            __('Taxonomy Manager', 'recruitpro'),
            __('Taxonomies', 'recruitpro'),
            'manage_options',
            'recruitpro-taxonomies',
            array($this, 'render_taxonomy_admin_page')
        );
    }

    /**
     * Render taxonomy admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function render_taxonomy_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('RecruitPro Taxonomy Manager', 'recruitpro'); ?></h1>
            <p><?php _e('Manage and organize your recruitment website content with powerful taxonomy tools.', 'recruitpro'); ?></p>
            
            <div class="taxonomy-admin-tabs">
                <nav class="nav-tab-wrapper">
                    <a href="#overview" class="nav-tab nav-tab-active"><?php _e('Overview', 'recruitpro'); ?></a>
                    <a href="#theme-taxonomies" class="nav-tab"><?php _e('Theme Taxonomies', 'recruitpro'); ?></a>
                    <a href="#industry" class="nav-tab"><?php _e('Industries', 'recruitpro'); ?></a>
                    <a href="#skills" class="nav-tab"><?php _e('Skills', 'recruitpro'); ?></a>
                    <a href="#tools" class="nav-tab"><?php _e('Tools', 'recruitpro'); ?></a>
                </nav>
                
                <div id="overview" class="tab-content active">
                    <h2><?php _e('Taxonomy Overview', 'recruitpro'); ?></h2>
                    <?php $this->render_taxonomy_overview(); ?>
                </div>
                
                <div id="theme-taxonomies" class="tab-content">
                    <h2><?php _e('Theme Taxonomies', 'recruitpro'); ?></h2>
                    <?php $this->render_theme_taxonomies_table(); ?>
                </div>
                
                <div id="industry" class="tab-content">
                    <h2><?php _e('Industry Classifications', 'recruitpro'); ?></h2>
                    <?php $this->render_industry_taxonomies_table(); ?>
                </div>
                
                <div id="skills" class="tab-content">
                    <h2><?php _e('Skill Categories', 'recruitpro'); ?></h2>
                    <?php $this->render_skill_taxonomies_table(); ?>
                </div>
                
                <div id="tools" class="tab-content">
                    <h2><?php _e('Taxonomy Tools', 'recruitpro'); ?></h2>
                    <?php $this->render_taxonomy_tools(); ?>
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
     * Render taxonomy overview
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_taxonomy_overview() {
        $all_taxonomies = array_merge(
            $this->theme_taxonomies,
            $this->industry_classifications,
            $this->skill_categories
        );
        ?>
        <div class="taxonomy-overview-grid">
            <div class="overview-card">
                <h3><?php _e('Total Taxonomies', 'recruitpro'); ?></h3>
                <div class="overview-number"><?php echo count($all_taxonomies); ?></div>
                <p><?php _e('Active theme taxonomies', 'recruitpro'); ?></p>
            </div>
            
            <div class="overview-card">
                <h3><?php _e('Theme Taxonomies', 'recruitpro'); ?></h3>
                <div class="overview-number"><?php echo count($this->theme_taxonomies); ?></div>
                <p><?php _e('Content organization taxonomies', 'recruitpro'); ?></p>
            </div>
            
            <div class="overview-card">
                <h3><?php _e('Industry Classifications', 'recruitpro'); ?></h3>
                <div class="overview-number"><?php echo count($this->industry_classifications); ?></div>
                <p><?php _e('Business sector taxonomies', 'recruitpro'); ?></p>
            </div>
            
            <div class="overview-card">
                <h3><?php _e('Skill Categories', 'recruitpro'); ?></h3>
                <div class="overview-number"><?php echo count($this->skill_categories); ?></div>
                <p><?php _e('Professional skill taxonomies', 'recruitpro'); ?></p>
            </div>
        </div>
        
        <div class="taxonomy-status">
            <h3><?php _e('Quick Actions', 'recruitpro'); ?></h3>
            <p>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=recruitment_industry'); ?>" class="button">
                    <?php _e('Manage Industries', 'recruitpro'); ?>
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=recruitment_specialization'); ?>" class="button">
                    <?php _e('Manage Specializations', 'recruitpro'); ?>
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=skill_category'); ?>" class="button">
                    <?php _e('Manage Skills', 'recruitpro'); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Render theme taxonomies table
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_theme_taxonomies_table() {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Taxonomy', 'recruitpro'); ?></th>
                    <th><?php _e('Post Types', 'recruitpro'); ?></th>
                    <th><?php _e('Terms', 'recruitpro'); ?></th>
                    <th><?php _e('Description', 'recruitpro'); ?></th>
                    <th><?php _e('Actions', 'recruitpro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->theme_taxonomies as $taxonomy => $config): ?>
                    <?php $term_count = wp_count_terms(array('taxonomy' => $taxonomy)); ?>
                    <tr>
                        <td><strong><?php echo esc_html($config['labels']['name']); ?></strong></td>
                        <td><?php echo esc_html(implode(', ', $config['post_types'])); ?></td>
                        <td><?php echo esc_html($term_count); ?></td>
                        <td><?php echo esc_html($config['description'] ?? ''); ?></td>
                        <td>
                            <a href="<?php echo admin_url('edit-tags.php?taxonomy=' . $taxonomy); ?>" class="button button-small">
                                <?php _e('Manage', 'recruitpro'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Render industry taxonomies table
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_industry_taxonomies_table() {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Taxonomy', 'recruitpro'); ?></th>
                    <th><?php _e('Terms', 'recruitpro'); ?></th>
                    <th><?php _e('Description', 'recruitpro'); ?></th>
                    <th><?php _e('Actions', 'recruitpro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->industry_classifications as $taxonomy => $config): ?>
                    <?php $term_count = wp_count_terms(array('taxonomy' => $taxonomy)); ?>
                    <tr>
                        <td><strong><?php echo esc_html($config['labels']['name']); ?></strong></td>
                        <td><?php echo esc_html($term_count); ?></td>
                        <td><?php echo esc_html($config['description']); ?></td>
                        <td>
                            <a href="<?php echo admin_url('edit-tags.php?taxonomy=' . $taxonomy); ?>" class="button button-small">
                                <?php _e('Manage', 'recruitpro'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Render skill taxonomies table
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_skill_taxonomies_table() {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Taxonomy', 'recruitpro'); ?></th>
                    <th><?php _e('Terms', 'recruitpro'); ?></th>
                    <th><?php _e('Description', 'recruitpro'); ?></th>
                    <th><?php _e('Actions', 'recruitpro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->skill_categories as $taxonomy => $config): ?>
                    <?php $term_count = wp_count_terms(array('taxonomy' => $taxonomy)); ?>
                    <tr>
                        <td><strong><?php echo esc_html($config['labels']['name']); ?></strong></td>
                        <td><?php echo esc_html($term_count); ?></td>
                        <td><?php echo esc_html($config['description']); ?></td>
                        <td>
                            <a href="<?php echo admin_url('edit-tags.php?taxonomy=' . $taxonomy); ?>" class="button button-small">
                                <?php _e('Manage', 'recruitpro'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Render taxonomy tools
     * 
     * @since 1.0.0
     * @return void
     */
    private function render_taxonomy_tools() {
        ?>
        <div class="taxonomy-tools">
            <div class="tool-section">
                <h3><?php _e('Bulk Operations', 'recruitpro'); ?></h3>
                <p><?php _e('Perform bulk operations on taxonomy terms.', 'recruitpro'); ?></p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('recruitpro_taxonomy_bulk', 'taxonomy_bulk_nonce'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Action', 'recruitpro'); ?></th>
                            <td>
                                <select name="bulk_action">
                                    <option value=""><?php _e('Select Action', 'recruitpro'); ?></option>
                                    <option value="reset_colors"><?php _e('Reset All Colors', 'recruitpro'); ?></option>
                                    <option value="clear_icons"><?php _e('Clear All Icons', 'recruitpro'); ?></option>
                                    <option value="unfeature_all"><?php _e('Remove Featured Status', 'recruitpro'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Taxonomy', 'recruitpro'); ?></th>
                            <td>
                                <select name="target_taxonomy">
                                    <option value=""><?php _e('Select Taxonomy', 'recruitpro'); ?></option>
                                    <?php foreach (array_merge($this->theme_taxonomies, $this->industry_classifications, $this->skill_categories) as $taxonomy => $config): ?>
                                        <option value="<?php echo esc_attr($taxonomy); ?>">
                                            <?php echo esc_html($config['labels']['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="submit_bulk_action" class="button button-primary" value="<?php _e('Execute', 'recruitpro'); ?>">
                    </p>
                </form>
            </div>
            
            <div class="tool-section">
                <h3><?php _e('Import/Export', 'recruitpro'); ?></h3>
                <p><?php _e('Import or export taxonomy terms and their metadata.', 'recruitpro'); ?></p>
                
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('themes.php?page=recruitpro-taxonomies&action=export'), 'export_taxonomies'); ?>" class="button">
                        <?php _e('Export Taxonomies', 'recruitpro'); ?>
                    </a>
                </p>
                
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('recruitpro_taxonomy_import', 'taxonomy_import_nonce'); ?>
                    <input type="file" name="taxonomy_file" accept=".json">
                    <input type="submit" name="submit_import" class="button" value="<?php _e('Import Taxonomies', 'recruitpro'); ?>">
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Handle taxonomy bulk actions
     * 
     * @since 1.0.0
     * @return void
     */
    public function handle_taxonomy_bulk_actions() {
        check_ajax_referer('recruitpro_taxonomy_bulk', 'nonce');
        
        $action = sanitize_text_field($_POST['action']);
        $taxonomy = sanitize_text_field($_POST['taxonomy']);
        
        // Handle different bulk actions
        $result = false;
        
        switch ($action) {
            case 'reset_colors':
                $result = $this->bulk_reset_term_colors($taxonomy);
                break;
            case 'clear_icons':
                $result = $this->bulk_clear_term_icons($taxonomy);
                break;
            case 'unfeature_all':
                $result = $this->bulk_unfeature_terms($taxonomy);
                break;
        }
        
        if ($result) {
            wp_send_json_success(array('message' => __('Bulk action completed successfully.', 'recruitpro')));
        } else {
            wp_send_json_error(array('message' => __('Bulk action failed.', 'recruitpro')));
        }
    }

    /**
     * Bulk reset term colors
     * 
     * @since 1.0.0
     * @param string $taxonomy Taxonomy name
     * @return bool Success status
     */
    private function bulk_reset_term_colors($taxonomy) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        
        foreach ($terms as $term) {
            delete_term_meta($term->term_id, 'term_color');
        }
        
        return true;
    }

    /**
     * Bulk clear term icons
     * 
     * @since 1.0.0
     * @param string $taxonomy Taxonomy name
     * @return bool Success status
     */
    private function bulk_clear_term_icons($taxonomy) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        
        foreach ($terms as $term) {
            delete_term_meta($term->term_id, 'term_icon');
        }
        
        return true;
    }

    /**
     * Bulk unfeature terms
     * 
     * @since 1.0.0
     * @param string $taxonomy Taxonomy name
     * @return bool Success status
     */
    private function bulk_unfeature_terms($taxonomy) {
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
        
        foreach ($terms as $term) {
            update_term_meta($term->term_id, 'term_featured', '0');
        }
        
        return true;
    }

    /**
     * Get taxonomy configuration
     * 
     * @since 1.0.0
     * @param string $taxonomy Taxonomy name
     * @return array|false Taxonomy configuration or false
     */
    private function get_taxonomy_config($taxonomy) {
        $all_taxonomies = array_merge(
            $this->theme_taxonomies,
            $this->industry_classifications,
            $this->skill_categories
        );
        
        return isset($all_taxonomies[$taxonomy]) ? $all_taxonomies[$taxonomy] : false;
    }
}

// Initialize the taxonomies manager
if (class_exists('RecruitPro_Taxonomies_Manager')) {
    new RecruitPro_Taxonomies_Manager();
}

/**
 * Helper functions for taxonomy management
 */

/**
 * Get all theme taxonomies
 * 
 * @since 1.0.0
 * @return array Theme taxonomy names
 */
function recruitpro_get_theme_taxonomies() {
    return array(
        'testimonial_category', 'testimonial_source', 'team_department', 'team_expertise',
        'service_category', 'service_delivery_method', 'success_story_category',
        'recruitment_industry', 'recruitment_specialization', 'content_topics',
        'skill_category', 'experience_level'
    );
}

/**
 * Get terms with icons
 * 
 * @since 1.0.0
 * @param string $taxonomy Taxonomy name
 * @param array $args Additional arguments
 * @return array Terms with icon data
 */
function recruitpro_get_terms_with_icons($taxonomy, $args = array()) {
    $terms = get_terms(array_merge(array('taxonomy' => $taxonomy), $args));
    
    if (empty($terms) || is_wp_error($terms)) {
        return array();
    }
    
    foreach ($terms as &$term) {
        $term->icon = get_term_meta($term->term_id, 'term_icon', true);
        $term->color = get_term_meta($term->term_id, 'term_color', true);
        $term->featured = get_term_meta($term->term_id, 'term_featured', true);
    }
    
    return $terms;
}

/**
 * Get featured terms
 * 
 * @since 1.0.0
 * @param string $taxonomy Taxonomy name
 * @param int $number Number of terms
 * @return array Featured terms
 */
function recruitpro_get_featured_terms($taxonomy, $number = 5) {
    return get_terms(array(
        'taxonomy' => $taxonomy,
        'number' => $number,
        'meta_key' => 'term_featured',
        'meta_value' => '1',
        'hide_empty' => true,
    ));
}

/**
 * Display term with icon
 * 
 * @since 1.0.0
 * @param WP_Term $term Term object
 * @param bool $link Whether to wrap in link
 * @return string Term HTML with icon
 */
function recruitpro_display_term_with_icon($term, $link = true) {
    $icon = get_term_meta($term->term_id, 'term_icon', true);
    $color = get_term_meta($term->term_id, 'term_color', true);
    
    $icon_html = '';
    if ($icon) {
        $style = $color ? 'style="color: ' . esc_attr($color) . '"' : '';
        $icon_html = '<i class="' . esc_attr($icon) . '" ' . $style . '></i> ';
    }
    
    $content = $icon_html . esc_html($term->name);
    
    if ($link) {
        return '<a href="' . esc_url(get_term_link($term)) . '" class="term-link">' . $content . '</a>';
    }
    
    return '<span class="term-display">' . $content . '</span>';
}

/**
 * Check if taxonomy is recruitment-related
 * 
 * @since 1.0.0
 * @param string $taxonomy Taxonomy name
 * @return bool True if recruitment-related
 */
function recruitpro_is_recruitment_taxonomy($taxonomy) {
    $recruitment_taxonomies = array(
        'recruitment_industry', 'recruitment_specialization', 'skill_category',
        'experience_level', 'team_expertise', 'service_category'
    );
    
    return in_array($taxonomy, $recruitment_taxonomies);
}