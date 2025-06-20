<?php
/**
 * RecruitPro Theme Pagination System
 *
 * Comprehensive pagination system for recruitment websites with support for
 * blog posts, job listings, search results, and custom post types.
 * Features AJAX loading, SEO optimization, and accessibility compliance.
 *
 * @package RecruitPro
 * @subpackage Theme/Pagination
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/pagination.php
 * Purpose: Advanced pagination system for all content types
 * Dependencies: WordPress core, theme functions
 * Features: AJAX loading, SEO optimization, accessibility compliance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Pagination Manager Class
 * 
 * Handles all pagination functionality for the theme including
 * blog posts, job listings, and custom content types.
 *
 * @since 1.0.0
 */
class RecruitPro_Pagination_Manager {

    /**
     * Pagination settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $pagination_settings = array();

    /**
     * Default pagination arguments
     * 
     * @since 1.0.0
     * @var array
     */
    private $default_args = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
        $this->set_default_args();
    }

    /**
     * Initialize pagination settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->pagination_settings = array(
            'enable_ajax_pagination' => get_theme_mod('recruitpro_enable_ajax_pagination', true),
            'pagination_style' => get_theme_mod('recruitpro_pagination_style', 'numbers'),
            'posts_per_page' => get_theme_mod('recruitpro_posts_per_page', get_option('posts_per_page', 10)),
            'jobs_per_page' => get_theme_mod('recruitpro_jobs_per_page', 12),
            'show_pagination_info' => get_theme_mod('recruitpro_show_pagination_info', true),
            'infinite_scroll' => get_theme_mod('recruitpro_infinite_scroll', false),
            'load_more_button' => get_theme_mod('recruitpro_load_more_button', true),
            'pagination_position' => get_theme_mod('recruitpro_pagination_position', 'bottom'),
            'mobile_pagination_style' => get_theme_mod('recruitpro_mobile_pagination_style', 'compact'),
        );
    }

    /**
     * Set default pagination arguments
     * 
     * @since 1.0.0
     * @return void
     */
    private function set_default_args() {
        $this->default_args = array(
            'mid_size' => 2,
            'end_size' => 1,
            'prev_text' => '<span class="pagination-icon pagination-prev" aria-hidden="true">&laquo;</span><span class="pagination-text">' . __('Previous', 'recruitpro') . '</span>',
            'next_text' => '<span class="pagination-text">' . __('Next', 'recruitpro') . '</span><span class="pagination-icon pagination-next" aria-hidden="true">&raquo;</span>',
            'screen_reader_text' => __('Page navigation', 'recruitpro'),
            'aria_label' => __('Page navigation', 'recruitpro'),
            'class' => 'recruitpro-pagination',
            'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'recruitpro') . ' </span>',
            'type' => 'array',
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // AJAX pagination handlers
        add_action('wp_ajax_recruitpro_load_more_posts', array($this, 'ajax_load_more_posts'));
        add_action('wp_ajax_nopriv_recruitpro_load_more_posts', array($this, 'ajax_load_more_posts'));
        
        add_action('wp_ajax_recruitpro_load_more_jobs', array($this, 'ajax_load_more_jobs'));
        add_action('wp_ajax_nopriv_recruitpro_load_more_jobs', array($this, 'ajax_load_more_jobs'));
        
        // Enqueue pagination scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_pagination_scripts'));
        
        // Customizer settings
        add_action('customize_register', array($this, 'customize_register_pagination'));
        
        // Modify main query for custom posts per page
        add_action('pre_get_posts', array($this, 'modify_posts_per_page'));
        
        // Add schema markup for pagination
        add_action('wp_head', array($this, 'add_pagination_schema'));
    }

    /**
     * Main pagination function
     * 
     * @since 1.0.0
     * @param array $args Pagination arguments
     * @return string Pagination HTML
     */
    public function render_pagination($args = array()) {
        global $wp_query;

        // Merge with defaults
        $args = wp_parse_args($args, $this->default_args);

        // Get current page and total pages
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $total_pages = $wp_query->max_num_pages;

        // Don't show pagination if there's only one page
        if ($total_pages <= 1) {
            return '';
        }

        // Determine pagination style based on context and settings
        $pagination_style = $this->get_context_pagination_style();

        // Generate pagination based on style
        switch ($pagination_style) {
            case 'load_more':
                return $this->render_load_more_pagination($args);
            case 'infinite_scroll':
                return $this->render_infinite_scroll_pagination($args);
            case 'numbers':
            default:
                return $this->render_numbers_pagination($args);
        }
    }

    /**
     * Render numbered pagination
     * 
     * @since 1.0.0
     * @param array $args Pagination arguments
     * @return string Pagination HTML
     */
    private function render_numbers_pagination($args) {
        global $wp_query;

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $total_pages = $wp_query->max_num_pages;

        // Generate pagination links
        $pagination_links = paginate_links(array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '?paged=%#%',
            'current' => max(1, $paged),
            'total' => $total_pages,
            'mid_size' => $args['mid_size'],
            'end_size' => $args['end_size'],
            'prev_text' => $args['prev_text'],
            'next_text' => $args['next_text'],
            'type' => 'array',
            'add_args' => false,
        ));

        if (!$pagination_links) {
            return '';
        }

        // Build HTML structure
        $html = $this->build_pagination_wrapper_start($args);
        
        // Add pagination info if enabled
        if ($this->pagination_settings['show_pagination_info']) {
            $html .= $this->get_pagination_info($paged, $total_pages);
        }

        // Add pagination navigation
        $html .= '<nav class="pagination-navigation" role="navigation" aria-label="' . esc_attr($args['aria_label']) . '">';
        $html .= '<ul class="pagination-list">';

        foreach ($pagination_links as $link) {
            $html .= '<li class="pagination-item">' . $link . '</li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';
        
        $html .= $this->build_pagination_wrapper_end();

        return $html;
    }

    /**
     * Render load more button pagination
     * 
     * @since 1.0.0
     * @param array $args Pagination arguments
     * @return string Pagination HTML
     */
    private function render_load_more_pagination($args) {
        global $wp_query;

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $total_pages = $wp_query->max_num_pages;

        // Don't show load more if we're on the last page
        if ($paged >= $total_pages) {
            return '';
        }

        $post_type = get_post_type() ?: 'post';
        $next_page = $paged + 1;

        $html = $this->build_pagination_wrapper_start($args);
        
        // Add pagination info
        if ($this->pagination_settings['show_pagination_info']) {
            $html .= $this->get_pagination_info($paged, $total_pages);
        }

        // Load more button
        $html .= '<div class="load-more-wrapper">';
        $html .= '<button type="button" class="load-more-btn" ';
        $html .= 'data-post-type="' . esc_attr($post_type) . '" ';
        $html .= 'data-page="' . esc_attr($next_page) . '" ';
        $html .= 'data-max-pages="' . esc_attr($total_pages) . '" ';
        $html .= 'data-query="' . esc_attr(json_encode($wp_query->query_vars)) . '">';
        $html .= '<span class="btn-text">' . __('Load More', 'recruitpro') . '</span>';
        $html .= '<span class="btn-loading" style="display: none;">' . __('Loading...', 'recruitpro') . '</span>';
        $html .= '</button>';
        $html .= '</div>';

        $html .= $this->build_pagination_wrapper_end();

        return $html;
    }

    /**
     * Render infinite scroll pagination
     * 
     * @since 1.0.0
     * @param array $args Pagination arguments
     * @return string Pagination HTML
     */
    private function render_infinite_scroll_pagination($args) {
        global $wp_query;

        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $total_pages = $wp_query->max_num_pages;

        // Don't show if we're on the last page
        if ($paged >= $total_pages) {
            return '';
        }

        $post_type = get_post_type() ?: 'post';
        $next_page = $paged + 1;

        $html = '<div class="infinite-scroll-pagination" style="display: none;">';
        $html .= '<div class="infinite-scroll-trigger" ';
        $html .= 'data-post-type="' . esc_attr($post_type) . '" ';
        $html .= 'data-page="' . esc_attr($next_page) . '" ';
        $html .= 'data-max-pages="' . esc_attr($total_pages) . '" ';
        $html .= 'data-query="' . esc_attr(json_encode($wp_query->query_vars)) . '">';
        $html .= '</div>';
        
        // Loading indicator
        $html .= '<div class="infinite-scroll-loading">';
        $html .= '<span class="loading-text">' . __('Loading more content...', 'recruitpro') . '</span>';
        $html .= '</div>';
        
        $html .= '</div>';

        return $html;
    }

    /**
     * Get pagination info text
     * 
     * @since 1.0.0
     * @param int $current_page Current page number
     * @param int $total_pages Total number of pages
     * @return string Pagination info HTML
     */
    private function get_pagination_info($current_page, $total_pages) {
        global $wp_query;

        $total_posts = $wp_query->found_posts;
        $posts_per_page = $wp_query->query_vars['posts_per_page'];
        
        $start = (($current_page - 1) * $posts_per_page) + 1;
        $end = min($current_page * $posts_per_page, $total_posts);

        $html = '<div class="pagination-info">';
        $html .= '<span class="pagination-info-text">';
        
        if ($total_posts > 0) {
            $html .= sprintf(
                __('Showing %1$d-%2$d of %3$d results', 'recruitpro'),
                $start,
                $end,
                $total_posts
            );
        } else {
            $html .= __('No results found', 'recruitpro');
        }
        
        $html .= '</span>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Build pagination wrapper start
     * 
     * @since 1.0.0
     * @param array $args Pagination arguments
     * @return string HTML
     */
    private function build_pagination_wrapper_start($args) {
        $classes = array($args['class']);
        
        // Add context-specific classes
        if (is_home() || is_category() || is_tag() || is_archive()) {
            $classes[] = 'blog-pagination';
        } elseif (recruitpro_is_jobs_page()) {
            $classes[] = 'jobs-pagination';
        } elseif (is_search()) {
            $classes[] = 'search-pagination';
        }

        // Add style-specific classes
        $classes[] = 'pagination-style-' . $this->get_context_pagination_style();

        // Add AJAX class if enabled
        if ($this->pagination_settings['enable_ajax_pagination']) {
            $classes[] = 'ajax-pagination';
        }

        $html = '<div class="' . esc_attr(implode(' ', $classes)) . '">';
        
        return $html;
    }

    /**
     * Build pagination wrapper end
     * 
     * @since 1.0.0
     * @return string HTML
     */
    private function build_pagination_wrapper_end() {
        return '</div>';
    }

    /**
     * Get pagination style based on context
     * 
     * @since 1.0.0
     * @return string Pagination style
     */
    private function get_context_pagination_style() {
        // Mobile check
        if (wp_is_mobile() && $this->pagination_settings['mobile_pagination_style'] === 'load_more') {
            return 'load_more';
        }

        // Infinite scroll
        if ($this->pagination_settings['infinite_scroll']) {
            return 'infinite_scroll';
        }

        // Load more button
        if ($this->pagination_settings['load_more_button'] && !is_paged()) {
            return 'load_more';
        }

        // Default to numbers
        return $this->pagination_settings['pagination_style'];
    }

    /**
     * AJAX handler for loading more posts
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_load_more_posts() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['pagination_nonce'], 'recruitpro_pagination_nonce')) {
            wp_send_json_error(array('message' => __('Security verification failed.', 'recruitpro')));
        }

        $page = intval($_POST['page']);
        $post_type = sanitize_text_field($_POST['post_type']);
        $query_vars = json_decode(stripslashes($_POST['query']), true);

        // Modify query vars for pagination
        $query_vars['paged'] = $page;
        $query_vars['post_status'] = 'publish';

        // Create new query
        $query = new WP_Query($query_vars);

        if ($query->have_posts()) {
            ob_start();

            while ($query->have_posts()) {
                $query->the_post();
                
                // Load appropriate template part based on post type
                if ($post_type === 'job') {
                    get_template_part('template-parts/content/content', 'job');
                } else {
                    get_template_part('template-parts/content/content', get_post_type());
                }
            }

            $content = ob_get_clean();
            wp_reset_postdata();

            wp_send_json_success(array(
                'content' => $content,
                'page' => $page,
                'max_pages' => $query->max_num_pages,
                'has_more' => $page < $query->max_num_pages,
            ));
        } else {
            wp_send_json_error(array('message' => __('No more posts found.', 'recruitpro')));
        }
    }

    /**
     * AJAX handler for loading more jobs
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_load_more_jobs() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['pagination_nonce'], 'recruitpro_pagination_nonce')) {
            wp_send_json_error(array('message' => __('Security verification failed.', 'recruitpro')));
        }

        $page = intval($_POST['page']);
        $query_vars = json_decode(stripslashes($_POST['query']), true);

        // Ensure we're loading jobs
        $query_vars['post_type'] = 'job';
        $query_vars['paged'] = $page;
        $query_vars['post_status'] = 'publish';

        // Apply job-specific filters if available
        if (function_exists('recruitpro_jobs_apply_filters')) {
            $query_vars = recruitpro_jobs_apply_filters($query_vars);
        }

        // Create new query
        $query = new WP_Query($query_vars);

        if ($query->have_posts()) {
            ob_start();

            while ($query->have_posts()) {
                $query->the_post();
                get_template_part('template-parts/jobs/job', 'card');
            }

            $content = ob_get_clean();
            wp_reset_postdata();

            wp_send_json_success(array(
                'content' => $content,
                'page' => $page,
                'max_pages' => $query->max_num_pages,
                'has_more' => $page < $query->max_num_pages,
                'total_jobs' => $query->found_posts,
            ));
        } else {
            wp_send_json_error(array('message' => __('No more jobs found.', 'recruitpro')));
        }
    }

    /**
     * Enqueue pagination scripts and styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_pagination_scripts() {
        if (!$this->pagination_settings['enable_ajax_pagination']) {
            return;
        }

        wp_enqueue_script(
            'recruitpro-pagination',
            get_template_directory_uri() . '/assets/js/pagination.js',
            array('jquery'),
            RECRUITPRO_THEME_VERSION,
            true
        );

        wp_localize_script('recruitpro-pagination', 'recruitpro_pagination_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_pagination_nonce'),
            'loading_text' => __('Loading...', 'recruitpro'),
            'load_more_text' => __('Load More', 'recruitpro'),
            'no_more_text' => __('No more content', 'recruitpro'),
            'error_text' => __('Error loading content. Please try again.', 'recruitpro'),
            'infinite_scroll_enabled' => $this->pagination_settings['infinite_scroll'],
        ));
    }

    /**
     * Modify posts per page for different post types
     * 
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    public function modify_posts_per_page($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        // Jobs archive
        if ($query->is_post_type_archive('job')) {
            $query->set('posts_per_page', $this->pagination_settings['jobs_per_page']);
        }

        // Search results
        if ($query->is_search()) {
            $query->set('posts_per_page', $this->pagination_settings['posts_per_page']);
        }
    }

    /**
     * Add pagination schema markup
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_pagination_schema() {
        if (!is_paged()) {
            return;
        }

        global $wp_query;
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $total_pages = $wp_query->max_num_pages;

        // Previous page link
        if ($paged > 1) {
            $prev_link = get_pagenum_link($paged - 1);
            echo '<link rel="prev" href="' . esc_url($prev_link) . '" />' . "\n";
        }

        // Next page link
        if ($paged < $total_pages) {
            $next_link = get_pagenum_link($paged + 1);
            echo '<link rel="next" href="' . esc_url($next_link) . '" />' . "\n";
        }
    }

    /**
     * Add pagination settings to customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_pagination($wp_customize) {
        // Pagination section
        $wp_customize->add_section('recruitpro_pagination', array(
            'title' => __('Pagination Settings', 'recruitpro'),
            'description' => __('Configure pagination behavior for blog posts, job listings, and archives.', 'recruitpro'),
            'priority' => 40,
        ));

        // Enable AJAX pagination
        $wp_customize->add_setting('recruitpro_enable_ajax_pagination', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_ajax_pagination', array(
            'label' => __('Enable AJAX Pagination', 'recruitpro'),
            'description' => __('Load content without page refreshes.', 'recruitpro'),
            'section' => 'recruitpro_pagination',
            'type' => 'checkbox',
        ));

        // Pagination style
        $wp_customize->add_setting('recruitpro_pagination_style', array(
            'default' => 'numbers',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_pagination_style', array(
            'label' => __('Pagination Style', 'recruitpro'),
            'section' => 'recruitpro_pagination',
            'type' => 'select',
            'choices' => array(
                'numbers' => __('Page Numbers', 'recruitpro'),
                'load_more' => __('Load More Button', 'recruitpro'),
                'infinite_scroll' => __('Infinite Scroll', 'recruitpro'),
            ),
        ));

        // Jobs per page
        $wp_customize->add_setting('recruitpro_jobs_per_page', array(
            'default' => 12,
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('recruitpro_jobs_per_page', array(
            'label' => __('Jobs per Page', 'recruitpro'),
            'description' => __('Number of job listings to show per page.', 'recruitpro'),
            'section' => 'recruitpro_pagination',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 50,
            ),
        ));

        // Show pagination info
        $wp_customize->add_setting('recruitpro_show_pagination_info', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_show_pagination_info', array(
            'label' => __('Show Pagination Info', 'recruitpro'),
            'description' => __('Display "Showing X-Y of Z results" text.', 'recruitpro'),
            'section' => 'recruitpro_pagination',
            'type' => 'checkbox',
        ));

        // Mobile pagination style
        $wp_customize->add_setting('recruitpro_mobile_pagination_style', array(
            'default' => 'compact',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_mobile_pagination_style', array(
            'label' => __('Mobile Pagination Style', 'recruitpro'),
            'section' => 'recruitpro_pagination',
            'type' => 'select',
            'choices' => array(
                'compact' => __('Compact Numbers', 'recruitpro'),
                'load_more' => __('Load More Button', 'recruitpro'),
                'infinite_scroll' => __('Infinite Scroll', 'recruitpro'),
            ),
        ));
    }
}

// Initialize the pagination manager
if (class_exists('RecruitPro_Pagination_Manager')) {
    new RecruitPro_Pagination_Manager();
}

/**
 * Helper function to display pagination
 * 
 * @since 1.0.0
 * @param array $args Pagination arguments
 * @return void
 */
function recruitpro_pagination($args = array()) {
    $pagination_manager = new RecruitPro_Pagination_Manager();
    echo $pagination_manager->render_pagination($args);
}

/**
 * Helper function to get pagination
 * 
 * @since 1.0.0
 * @param array $args Pagination arguments
 * @return string Pagination HTML
 */
function recruitpro_get_pagination($args = array()) {
    $pagination_manager = new RecruitPro_Pagination_Manager();
    return $pagination_manager->render_pagination($args);
}

/**
 * Job-specific pagination function
 * 
 * @since 1.0.0
 * @param array $args Pagination arguments
 * @return void
 */
function recruitpro_jobs_pagination($args = array()) {
    $defaults = array(
        'class' => 'recruitpro-pagination jobs-pagination',
        'aria_label' => __('Job listings navigation', 'recruitpro'),
    );
    
    $args = wp_parse_args($args, $defaults);
    recruitpro_pagination($args);
}

/**
 * Blog-specific pagination function
 * 
 * @since 1.0.0
 * @param array $args Pagination arguments
 * @return void
 */
function recruitpro_blog_pagination($args = array()) {
    $defaults = array(
        'class' => 'recruitpro-pagination blog-pagination',
        'aria_label' => __('Blog posts navigation', 'recruitpro'),
    );
    
    $args = wp_parse_args($args, $defaults);
    recruitpro_pagination($args);
}

/**
 * Search results pagination function
 * 
 * @since 1.0.0
 * @param array $args Pagination arguments
 * @return void
 */
function recruitpro_search_pagination($args = array()) {
    $defaults = array(
        'class' => 'recruitpro-pagination search-pagination',
        'aria_label' => __('Search results navigation', 'recruitpro'),
    );
    
    $args = wp_parse_args($args, $defaults);
    recruitpro_pagination($args);
}

/**
 * Check if current query has pagination
 * 
 * @since 1.0.0
 * @return bool True if pagination is available
 */
function recruitpro_has_pagination() {
    global $wp_query;
    return $wp_query->max_num_pages > 1;
}

/**
 * Get pagination data for JavaScript
 * 
 * @since 1.0.0
 * @return array Pagination data
 */
function recruitpro_get_pagination_data() {
    global $wp_query;
    
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    
    return array(
        'current_page' => $paged,
        'total_pages' => $wp_query->max_num_pages,
        'total_posts' => $wp_query->found_posts,
        'posts_per_page' => $wp_query->query_vars['posts_per_page'],
        'has_more' => $paged < $wp_query->max_num_pages,
    );
}