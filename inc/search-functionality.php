<?php
/**
 * RecruitPro Theme Search Functionality
 *
 * Comprehensive search system for recruitment websites including
 * content search, job search, AJAX autocomplete, search filters,
 * and integration with CRM candidate search capabilities.
 *
 * @package RecruitPro
 * @subpackage Theme/Search
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/search-functionality.php
 * Purpose: Enhanced search functionality for recruitment websites
 * Dependencies: WordPress core, theme functions, optional jobs/CRM plugins
 * Features: AJAX search, job filtering, content search, search analytics
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Search Manager Class
 * 
 * Handles all search functionality including content search,
 * job search, autocomplete, and integration with recruitment plugins.
 *
 * @since 1.0.0
 */
class RecruitPro_Search_Manager {

    /**
     * Search settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $search_settings = array();

    /**
     * Search analytics data
     * 
     * @since 1.0.0
     * @var array
     */
    private $search_analytics = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
    }

    /**
     * Initialize search settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->search_settings = array(
            // Core search settings
            'enable_enhanced_search' => get_theme_mod('recruitpro_enable_enhanced_search', true),
            'enable_ajax_search' => get_theme_mod('recruitpro_enable_ajax_search', true),
            'enable_search_suggestions' => get_theme_mod('recruitpro_enable_search_suggestions', true),
            'enable_search_analytics' => get_theme_mod('recruitpro_enable_search_analytics', true),
            
            // Search behavior settings
            'search_posts_per_page' => get_theme_mod('recruitpro_search_posts_per_page', 10),
            'search_excerpt_length' => get_theme_mod('recruitpro_search_excerpt_length', 25),
            'min_search_chars' => get_theme_mod('recruitpro_min_search_chars', 2),
            'search_delay' => get_theme_mod('recruitpro_search_delay', 300), // milliseconds
            
            // Content types to include in search
            'search_post_types' => get_theme_mod('recruitpro_search_post_types', array('post', 'page', 'job')),
            'search_taxonomies' => get_theme_mod('recruitpro_search_taxonomies', array('category', 'post_tag', 'job_category')),
            'exclude_pages' => get_theme_mod('recruitpro_search_exclude_pages', array()),
            
            // Job search specific settings
            'enable_job_search_filters' => get_theme_mod('recruitpro_enable_job_search_filters', true),
            'job_search_radius' => get_theme_mod('recruitpro_job_search_radius', 50),
            'job_search_units' => get_theme_mod('recruitpro_job_search_units', 'miles'),
            
            // Advanced search features
            'enable_search_highlighting' => get_theme_mod('recruitpro_enable_search_highlighting', true),
            'enable_search_history' => get_theme_mod('recruitpro_enable_search_history', true),
            'enable_popular_searches' => get_theme_mod('recruitpro_enable_popular_searches', true),
            'enable_search_export' => get_theme_mod('recruitpro_enable_search_export', false),
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        if (!$this->search_settings['enable_enhanced_search']) {
            return;
        }

        // Core search hooks
        add_action('pre_get_posts', array($this, 'enhance_search_query'));
        add_filter('get_search_form', array($this, 'custom_search_form'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_search_scripts'));
        
        // AJAX search hooks
        if ($this->search_settings['enable_ajax_search']) {
            add_action('wp_ajax_recruitpro_ajax_search', array($this, 'ajax_search_handler'));
            add_action('wp_ajax_nopriv_recruitpro_ajax_search', array($this, 'ajax_search_handler'));
            
            add_action('wp_ajax_recruitpro_search_suggestions', array($this, 'ajax_search_suggestions'));
            add_action('wp_ajax_nopriv_recruitpro_search_suggestions', array($this, 'ajax_search_suggestions'));
        }
        
        // Search analytics hooks
        if ($this->search_settings['enable_search_analytics']) {
            add_action('wp_head', array($this, 'track_search_terms'));
            add_action('wp_ajax_recruitpro_track_search', array($this, 'ajax_track_search'));
            add_action('wp_ajax_nopriv_recruitpro_track_search', array($this, 'ajax_track_search'));
        }
        
        // Content enhancement hooks
        add_filter('the_content', array($this, 'highlight_search_terms'));
        add_filter('the_excerpt', array($this, 'highlight_search_terms'));
        add_filter('the_title', array($this, 'highlight_search_terms'));
        
        // Job search integration hooks
        add_action('recruitpro_job_search_form', array($this, 'render_job_search_filters'));
        add_filter('recruitpro_job_search_args', array($this, 'modify_job_search_args'));
        
        // Search result enhancement
        add_action('pre_get_posts', array($this, 'improve_search_relevance'));
        add_filter('posts_search', array($this, 'enhance_search_sql'), 10, 2);
        
        // Admin hooks
        add_action('customize_register', array($this, 'customize_register_search'));
        add_action('admin_menu', array($this, 'add_search_analytics_page'));
        
        // Shortcode for search functionality
        add_shortcode('recruitpro_search_form', array($this, 'search_form_shortcode'));
        add_shortcode('recruitpro_job_search', array($this, 'job_search_shortcode'));
        add_shortcode('recruitpro_popular_searches', array($this, 'popular_searches_shortcode'));
    }

    /**
     * Enhance the main search query
     * 
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    public function enhance_search_query($query) {
        if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
            return;
        }

        // Set post types to search
        $post_types = $this->search_settings['search_post_types'];
        
        // Remove 'job' from post types if jobs plugin is not active
        if (!recruitpro_has_jobs_plugin()) {
            $post_types = array_diff($post_types, array('job'));
        }
        
        $query->set('post_type', $post_types);
        
        // Set posts per page
        $query->set('posts_per_page', $this->search_settings['search_posts_per_page']);
        
        // Exclude specific pages
        if (!empty($this->search_settings['exclude_pages'])) {
            $query->set('post__not_in', $this->search_settings['exclude_pages']);
        }
        
        // Only include published posts
        $query->set('post_status', 'publish');
        
        // Improve search ordering
        $query->set('orderby', 'relevance');
        $query->set('order', 'DESC');
    }

    /**
     * Improve search relevance scoring
     * 
     * @since 1.0.0
     * @param WP_Query $query Query object
     * @return void
     */
    public function improve_search_relevance($query) {
        if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
            return;
        }

        // Add meta query for better job search results
        if (in_array('job', $query->get('post_type', array()))) {
            $meta_query = $query->get('meta_query', array());
            
            // Prioritize active jobs
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key' => '_job_status',
                    'value' => 'active',
                    'compare' => '=',
                ),
                array(
                    'key' => '_job_status',
                    'compare' => 'NOT EXISTS',
                ),
            );
            
            $query->set('meta_query', $meta_query);
        }
    }

    /**
     * Enhance search SQL for better results
     * 
     * @since 1.0.0
     * @param string $search_sql Search SQL
     * @param WP_Query $query Query object
     * @return string Enhanced search SQL
     */
    public function enhance_search_sql($search_sql, $query) {
        if (!$query->is_main_query() || !$query->is_search()) {
            return $search_sql;
        }

        global $wpdb;
        
        $search_terms = $query->get('search_terms');
        if (empty($search_terms)) {
            return $search_sql;
        }

        // Build enhanced search SQL
        $enhanced_search = '';
        foreach ($search_terms as $term) {
            $term = $wpdb->esc_like($term);
            $term = '%' . $term . '%';
            
            if (!empty($enhanced_search)) {
                $enhanced_search .= ' AND ';
            }
            
            // Search in title (higher weight), content, excerpt, and meta
            $enhanced_search .= "(
                ({$wpdb->posts}.post_title LIKE '{$term}') OR
                ({$wpdb->posts}.post_content LIKE '{$term}') OR
                ({$wpdb->posts}.post_excerpt LIKE '{$term}') OR
                EXISTS (
                    SELECT 1 FROM {$wpdb->postmeta} pm 
                    WHERE pm.post_id = {$wpdb->posts}.ID 
                    AND pm.meta_value LIKE '{$term}'
                )
            )";
        }

        return " AND ({$enhanced_search}) ";
    }

    /**
     * Custom search form
     * 
     * @since 1.0.0
     * @param string $form Default search form
     * @return string Custom search form
     */
    public function custom_search_form($form) {
        $unique_id = uniqid('search-form-');
        $placeholder = __('Search jobs, articles, and more...', 'recruitpro');
        
        // Customize placeholder based on context
        if (recruitpro_is_jobs_page()) {
            $placeholder = __('Search jobs by title, location, or company...', 'recruitpro');
        } elseif (is_home() || is_category() || is_tag()) {
            $placeholder = __('Search articles and content...', 'recruitpro');
        }

        ob_start();
        ?>
        <form role="search" method="get" class="search-form recruitpro-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-form-wrapper">
                <label for="<?php echo esc_attr($unique_id); ?>" class="screen-reader-text">
                    <?php _e('Search for:', 'recruitpro'); ?>
                </label>
                
                <div class="search-input-wrapper">
                    <input type="search" 
                           id="<?php echo esc_attr($unique_id); ?>" 
                           class="search-field" 
                           placeholder="<?php echo esc_attr($placeholder); ?>" 
                           value="<?php echo get_search_query(); ?>" 
                           name="s" 
                           autocomplete="off"
                           data-min-chars="<?php echo esc_attr($this->search_settings['min_search_chars']); ?>"
                           data-delay="<?php echo esc_attr($this->search_settings['search_delay']); ?>" />
                    
                    <?php if ($this->search_settings['enable_ajax_search']): ?>
                    <div class="search-suggestions" id="search-suggestions-<?php echo esc_attr($unique_id); ?>">
                        <!-- AJAX suggestions will be loaded here -->
                    </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="search-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span class="screen-reader-text"><?php _e('Search', 'recruitpro'); ?></span>
                </button>
            </div>
            
            <?php wp_nonce_field('recruitpro_search_nonce', 'search_nonce'); ?>
            
            <?php if ($this->search_settings['enable_search_analytics']): ?>
            <input type="hidden" name="search_source" value="form" />
            <?php endif; ?>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX search handler
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_search_handler() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_search_nonce')) {
            wp_send_json_error(array('message' => __('Security verification failed.', 'recruitpro')));
        }

        $search_query = sanitize_text_field($_POST['s'] ?? '');
        $search_type = sanitize_text_field($_POST['search_type'] ?? 'all');
        $page = absint($_POST['page'] ?? 1);

        if (strlen($search_query) < $this->search_settings['min_search_chars']) {
            wp_send_json_error(array('message' => __('Search query too short.', 'recruitpro')));
        }

        // Build search arguments
        $search_args = array(
            's' => $search_query,
            'post_type' => $this->get_search_post_types($search_type),
            'posts_per_page' => min($this->search_settings['search_posts_per_page'], 20),
            'paged' => $page,
            'post_status' => 'publish',
        );

        // Apply filters for different search types
        if ($search_type === 'jobs' && recruitpro_has_jobs_plugin()) {
            $search_args = $this->apply_job_search_filters($search_args);
        }

        // Perform search
        $search_query_obj = new WP_Query($search_args);
        $results = array();

        if ($search_query_obj->have_posts()) {
            while ($search_query_obj->have_posts()) {
                $search_query_obj->the_post();
                
                $result = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'excerpt' => $this->get_search_excerpt(get_the_content(), $search_query),
                    'url' => get_permalink(),
                    'type' => get_post_type(),
                    'date' => get_the_date('c'),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                );

                // Add job-specific data
                if (get_post_type() === 'job') {
                    $result['job_location'] = get_post_meta(get_the_ID(), '_job_location', true);
                    $result['job_type'] = get_post_meta(get_the_ID(), '_job_type', true);
                    $result['company'] = get_post_meta(get_the_ID(), '_job_company', true);
                }

                $results[] = $result;
            }
        }

        wp_reset_postdata();

        // Track search if analytics enabled
        if ($this->search_settings['enable_search_analytics']) {
            $this->track_search_query($search_query, $search_type, count($results));
        }

        wp_send_json_success(array(
            'results' => $results,
            'total' => $search_query_obj->found_posts,
            'pages' => $search_query_obj->max_num_pages,
            'current_page' => $page,
            'query' => $search_query,
            'type' => $search_type,
        ));
    }

    /**
     * AJAX search suggestions handler
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_search_suggestions() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_search_nonce')) {
            wp_send_json_error(array('message' => __('Security verification failed.', 'recruitpro')));
        }

        $query = sanitize_text_field($_POST['q'] ?? '');
        
        if (strlen($query) < $this->search_settings['min_search_chars']) {
            wp_send_json_success(array('suggestions' => array()));
        }

        $suggestions = array();

        // Get post title suggestions
        $title_suggestions = $this->get_title_suggestions($query);
        $suggestions = array_merge($suggestions, $title_suggestions);

        // Get taxonomy suggestions
        $taxonomy_suggestions = $this->get_taxonomy_suggestions($query);
        $suggestions = array_merge($suggestions, $taxonomy_suggestions);

        // Get popular searches
        if ($this->search_settings['enable_popular_searches']) {
            $popular_suggestions = $this->get_popular_search_suggestions($query);
            $suggestions = array_merge($suggestions, $popular_suggestions);
        }

        // Remove duplicates and limit results
        $suggestions = array_unique($suggestions, SORT_REGULAR);
        $suggestions = array_slice($suggestions, 0, 10);

        wp_send_json_success(array('suggestions' => $suggestions));
    }

    /**
     * Get title suggestions
     * 
     * @since 1.0.0
     * @param string $query Search query
     * @return array Title suggestions
     */
    private function get_title_suggestions($query) {
        global $wpdb;
        
        $query = $wpdb->esc_like($query);
        $post_types = "'" . implode("','", $this->search_settings['search_post_types']) . "'";
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT post_title, post_type, ID
            FROM {$wpdb->posts} 
            WHERE post_title LIKE %s 
            AND post_type IN ({$post_types})
            AND post_status = 'publish'
            ORDER BY post_title ASC
            LIMIT 5
        ", '%' . $query . '%'));

        $suggestions = array();
        foreach ($results as $result) {
            $suggestions[] = array(
                'text' => $result->post_title,
                'type' => 'post',
                'post_type' => $result->post_type,
                'url' => get_permalink($result->ID),
                'id' => $result->ID,
            );
        }

        return $suggestions;
    }

    /**
     * Get taxonomy suggestions
     * 
     * @since 1.0.0
     * @param string $query Search query
     * @return array Taxonomy suggestions
     */
    private function get_taxonomy_suggestions($query) {
        $suggestions = array();
        $taxonomies = $this->search_settings['search_taxonomies'];
        
        foreach ($taxonomies as $taxonomy) {
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'name__like' => $query,
                'hide_empty' => true,
                'number' => 3,
            ));

            foreach ($terms as $term) {
                $suggestions[] = array(
                    'text' => $term->name,
                    'type' => 'taxonomy',
                    'taxonomy' => $taxonomy,
                    'url' => get_term_link($term),
                    'id' => $term->term_id,
                );
            }
        }

        return $suggestions;
    }

    /**
     * Get popular search suggestions
     * 
     * @since 1.0.0
     * @param string $query Search query
     * @return array Popular search suggestions
     */
    private function get_popular_search_suggestions($query) {
        $popular_searches = get_option('recruitpro_popular_searches', array());
        $suggestions = array();

        foreach ($popular_searches as $search => $count) {
            if (stripos($search, $query) !== false) {
                $suggestions[] = array(
                    'text' => $search,
                    'type' => 'popular',
                    'count' => $count,
                );
            }
        }

        // Sort by popularity
        usort($suggestions, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return array_slice($suggestions, 0, 3);
    }

    /**
     * Get search post types based on search type
     * 
     * @since 1.0.0
     * @param string $search_type Search type
     * @return array Post types to search
     */
    private function get_search_post_types($search_type) {
        switch ($search_type) {
            case 'jobs':
                return recruitpro_has_jobs_plugin() ? array('job') : array();
            case 'content':
                return array('post', 'page');
            case 'posts':
                return array('post');
            case 'pages':
                return array('page');
            default:
                return $this->search_settings['search_post_types'];
        }
    }

    /**
     * Apply job search filters
     * 
     * @since 1.0.0
     * @param array $search_args Search arguments
     * @return array Modified search arguments
     */
    private function apply_job_search_filters($search_args) {
        // Job location filter
        if (!empty($_POST['job_location'])) {
            $search_args['meta_query'][] = array(
                'key' => '_job_location',
                'value' => sanitize_text_field($_POST['job_location']),
                'compare' => 'LIKE',
            );
        }

        // Job type filter
        if (!empty($_POST['job_type'])) {
            $search_args['meta_query'][] = array(
                'key' => '_job_type',
                'value' => sanitize_text_field($_POST['job_type']),
                'compare' => '=',
            );
        }

        // Job category filter
        if (!empty($_POST['job_category'])) {
            $search_args['tax_query'][] = array(
                'taxonomy' => 'job_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($_POST['job_category']),
            );
        }

        // Salary range filter
        if (!empty($_POST['salary_min']) || !empty($_POST['salary_max'])) {
            $salary_query = array('relation' => 'AND');
            
            if (!empty($_POST['salary_min'])) {
                $salary_query[] = array(
                    'key' => '_job_salary_min',
                    'value' => absint($_POST['salary_min']),
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }
            
            if (!empty($_POST['salary_max'])) {
                $salary_query[] = array(
                    'key' => '_job_salary_max',
                    'value' => absint($_POST['salary_max']),
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }
            
            $search_args['meta_query'][] = $salary_query;
        }

        return $search_args;
    }

    /**
     * Get search excerpt with highlighting
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @param string $search_query Search query
     * @return string Search excerpt
     */
    private function get_search_excerpt($content, $search_query) {
        $content = wp_strip_all_tags($content);
        $excerpt_length = $this->search_settings['search_excerpt_length'];
        
        // Find the search term in content
        $search_pos = stripos($content, $search_query);
        
        if ($search_pos !== false) {
            // Extract text around the search term
            $start = max(0, $search_pos - ($excerpt_length * 2));
            $excerpt = substr($content, $start, $excerpt_length * 8);
            
            if ($start > 0) {
                $excerpt = '...' . $excerpt;
            }
            
            if (strlen($content) > $start + ($excerpt_length * 8)) {
                $excerpt .= '...';
            }
        } else {
            // Fallback to regular excerpt
            $excerpt = wp_trim_words($content, $excerpt_length);
        }

        return $excerpt;
    }

    /**
     * Highlight search terms in content
     * 
     * @since 1.0.0
     * @param string $content Content to highlight
     * @return string Content with highlighted search terms
     */
    public function highlight_search_terms($content) {
        if (!is_search() || !$this->search_settings['enable_search_highlighting']) {
            return $content;
        }

        $search_query = get_search_query();
        if (empty($search_query)) {
            return $content;
        }

        // Split search query into terms
        $search_terms = explode(' ', $search_query);
        
        foreach ($search_terms as $term) {
            if (strlen($term) >= 3) { // Only highlight terms with 3+ characters
                $content = preg_replace(
                    '/(' . preg_quote($term, '/') . ')/i',
                    '<mark class="search-highlight">$1</mark>',
                    $content
                );
            }
        }

        return $content;
    }

    /**
     * Track search query for analytics
     * 
     * @since 1.0.0
     * @param string $query Search query
     * @param string $type Search type
     * @param int $results_count Number of results
     * @return void
     */
    private function track_search_query($query, $type, $results_count) {
        $searches = get_option('recruitpro_search_analytics', array());
        $date = date('Y-m-d');
        
        // Initialize structure if needed
        if (!isset($searches[$date])) {
            $searches[$date] = array();
        }
        
        // Track the search
        $search_data = array(
            'query' => $query,
            'type' => $type,
            'results' => $results_count,
            'timestamp' => time(),
            'ip' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        );
        
        $searches[$date][] = $search_data;
        
        // Keep only last 30 days
        $searches = array_slice($searches, -30, 30, true);
        
        update_option('recruitpro_search_analytics', $searches);
        
        // Update popular searches
        $this->update_popular_searches($query);
    }

    /**
     * Update popular searches counter
     * 
     * @since 1.0.0
     * @param string $query Search query
     * @return void
     */
    private function update_popular_searches($query) {
        $popular_searches = get_option('recruitpro_popular_searches', array());
        
        if (isset($popular_searches[$query])) {
            $popular_searches[$query]++;
        } else {
            $popular_searches[$query] = 1;
        }
        
        // Sort by popularity and keep top 100
        arsort($popular_searches);
        $popular_searches = array_slice($popular_searches, 0, 100, true);
        
        update_option('recruitpro_popular_searches', $popular_searches);
    }

    /**
     * Get client IP address
     * 
     * @since 1.0.0
     * @return string Client IP
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Track search terms in head
     * 
     * @since 1.0.0
     * @return void
     */
    public function track_search_terms() {
        if (!is_search()) {
            return;
        }

        $search_query = get_search_query();
        if (empty($search_query)) {
            return;
        }

        // Track the search view
        $this->track_search_query($search_query, 'view', 0);
    }

    /**
     * AJAX track search handler
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_track_search() {
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_search_nonce')) {
            wp_send_json_error();
        }

        $query = sanitize_text_field($_POST['query'] ?? '');
        $action = sanitize_text_field($_POST['search_action'] ?? 'click');
        
        if (!empty($query)) {
            $this->track_search_query($query, $action, 0);
        }

        wp_send_json_success();
    }

    /**
     * Enqueue search scripts
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_search_scripts() {
        if (!$this->search_settings['enable_ajax_search']) {
            return;
        }

        wp_enqueue_script(
            'recruitpro-search',
            get_template_directory_uri() . '/assets/js/search.js',
            array('jquery'),
            RECRUITPRO_THEME_VERSION,
            true
        );

        wp_localize_script('recruitpro-search', 'recruitpro_search_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_search_nonce'),
            'min_chars' => $this->search_settings['min_search_chars'],
            'delay' => $this->search_settings['search_delay'],
            'strings' => array(
                'searching' => __('Searching...', 'recruitpro'),
                'no_results' => __('No results found.', 'recruitpro'),
                'error' => __('Search error. Please try again.', 'recruitpro'),
                'load_more' => __('Load More Results', 'recruitpro'),
                'view_all' => __('View All Results', 'recruitpro'),
            ),
        ));
    }

    /**
     * Render job search filters
     * 
     * @since 1.0.0
     * @return void
     */
    public function render_job_search_filters() {
        if (!$this->search_settings['enable_job_search_filters'] || !recruitpro_has_jobs_plugin()) {
            return;
        }

        ?>
        <div class="job-search-filters">
            <div class="filter-group">
                <label for="job-location-filter"><?php _e('Location', 'recruitpro'); ?></label>
                <select name="job_location" id="job-location-filter">
                    <option value=""><?php _e('All Locations', 'recruitpro'); ?></option>
                    <?php
                    $locations = get_terms(array(
                        'taxonomy' => 'job_location',
                        'hide_empty' => true,
                    ));
                    foreach ($locations as $location) {
                        echo '<option value="' . esc_attr($location->slug) . '">' . esc_html($location->name) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="job-type-filter"><?php _e('Job Type', 'recruitpro'); ?></label>
                <select name="job_type" id="job-type-filter">
                    <option value=""><?php _e('All Types', 'recruitpro'); ?></option>
                    <option value="full-time"><?php _e('Full Time', 'recruitpro'); ?></option>
                    <option value="part-time"><?php _e('Part Time', 'recruitpro'); ?></option>
                    <option value="contract"><?php _e('Contract', 'recruitpro'); ?></option>
                    <option value="temporary"><?php _e('Temporary', 'recruitpro'); ?></option>
                </select>
            </div>

            <div class="filter-group">
                <label for="job-category-filter"><?php _e('Category', 'recruitpro'); ?></label>
                <select name="job_category" id="job-category-filter">
                    <option value=""><?php _e('All Categories', 'recruitpro'); ?></option>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'job_category',
                        'hide_empty' => true,
                    ));
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
    }

    /**
     * Modify job search arguments
     * 
     * @since 1.0.0
     * @param array $args Search arguments
     * @return array Modified arguments
     */
    public function modify_job_search_args($args) {
        // Add relevance ordering for job searches
        if (isset($args['s']) && !empty($args['s'])) {
            $args['orderby'] = 'relevance';
            $args['order'] = 'DESC';
        }

        return $args;
    }

    /**
     * Add search analytics admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_search_analytics_page() {
        if (!$this->search_settings['enable_search_analytics']) {
            return;
        }

        add_theme_page(
            __('Search Analytics', 'recruitpro'),
            __('Search Analytics', 'recruitpro'),
            'manage_options',
            'recruitpro-search-analytics',
            array($this, 'render_search_analytics_page')
        );
    }

    /**
     * Render search analytics page
     * 
     * @since 1.0.0
     * @return void
     */
    public function render_search_analytics_page() {
        $analytics = get_option('recruitpro_search_analytics', array());
        $popular_searches = get_option('recruitpro_popular_searches', array());

        ?>
        <div class="wrap">
            <h1><?php _e('Search Analytics', 'recruitpro'); ?></h1>
            
            <div class="search-analytics-dashboard">
                <div class="analytics-cards">
                    <div class="analytics-card">
                        <h3><?php _e('Total Searches (30 days)', 'recruitpro'); ?></h3>
                        <p class="big-number"><?php echo array_sum(array_map('count', $analytics)); ?></p>
                    </div>
                    
                    <div class="analytics-card">
                        <h3><?php _e('Unique Search Terms', 'recruitpro'); ?></h3>
                        <p class="big-number"><?php echo count($popular_searches); ?></p>
                    </div>
                    
                    <div class="analytics-card">
                        <h3><?php _e('Most Popular Search', 'recruitpro'); ?></h3>
                        <p><?php echo !empty($popular_searches) ? esc_html(array_keys($popular_searches)[0]) : __('No data', 'recruitpro'); ?></p>
                    </div>
                </div>

                <div class="analytics-tables">
                    <div class="popular-searches">
                        <h3><?php _e('Popular Search Terms', 'recruitpro'); ?></h3>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Search Term', 'recruitpro'); ?></th>
                                    <th><?php _e('Count', 'recruitpro'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($popular_searches, 0, 20, true) as $term => $count): ?>
                                <tr>
                                    <td><?php echo esc_html($term); ?></td>
                                    <td><?php echo esc_html($count); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Search form shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Search form HTML
     */
    public function search_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'default',
            'placeholder' => __('Search...', 'recruitpro'),
            'show_filters' => false,
        ), $atts);

        ob_start();
        echo $this->custom_search_form('');
        return ob_get_clean();
    }

    /**
     * Job search shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Job search form HTML
     */
    public function job_search_shortcode($atts) {
        if (!recruitpro_has_jobs_plugin()) {
            return '<p>' . __('Jobs plugin is not active.', 'recruitpro') . '</p>';
        }

        $atts = shortcode_atts(array(
            'show_filters' => true,
            'style' => 'default',
        ), $atts);

        ob_start();
        ?>
        <div class="job-search-form-wrapper">
            <?php echo $this->custom_search_form(''); ?>
            <?php if ($atts['show_filters']): ?>
                <?php $this->render_job_search_filters(); ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Popular searches shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Popular searches HTML
     */
    public function popular_searches_shortcode($atts) {
        if (!$this->search_settings['enable_popular_searches']) {
            return '';
        }

        $atts = shortcode_atts(array(
            'limit' => 10,
            'title' => __('Popular Searches', 'recruitpro'),
        ), $atts);

        $popular_searches = get_option('recruitpro_popular_searches', array());
        $popular_searches = array_slice($popular_searches, 0, $atts['limit'], true);

        if (empty($popular_searches)) {
            return '';
        }

        ob_start();
        ?>
        <div class="popular-searches-widget">
            <?php if (!empty($atts['title'])): ?>
            <h4 class="widget-title"><?php echo esc_html($atts['title']); ?></h4>
            <?php endif; ?>
            
            <ul class="popular-searches-list">
                <?php foreach ($popular_searches as $term => $count): ?>
                <li class="popular-search-item">
                    <a href="<?php echo esc_url(home_url('/?s=' . urlencode($term))); ?>" class="popular-search-link">
                        <?php echo esc_html($term); ?>
                        <span class="search-count">(<?php echo esc_html($count); ?>)</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Add search settings to customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_search($wp_customize) {
        // Search section
        $wp_customize->add_section('recruitpro_search', array(
            'title' => __('Search Settings', 'recruitpro'),
            'description' => __('Configure search functionality and behavior.', 'recruitpro'),
            'priority' => 55,
        ));

        // Enable enhanced search
        $wp_customize->add_setting('recruitpro_enable_enhanced_search', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_enhanced_search', array(
            'label' => __('Enable Enhanced Search', 'recruitpro'),
            'description' => __('Enable advanced search functionality.', 'recruitpro'),
            'section' => 'recruitpro_search',
            'type' => 'checkbox',
        ));

        // Enable AJAX search
        $wp_customize->add_setting('recruitpro_enable_ajax_search', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_ajax_search', array(
            'label' => __('Enable AJAX Search', 'recruitpro'),
            'description' => __('Enable live search with suggestions and instant results.', 'recruitpro'),
            'section' => 'recruitpro_search',
            'type' => 'checkbox',
        ));

        // Search posts per page
        $wp_customize->add_setting('recruitpro_search_posts_per_page', array(
            'default' => 10,
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('recruitpro_search_posts_per_page', array(
            'label' => __('Results per Page', 'recruitpro'),
            'description' => __('Number of search results to show per page.', 'recruitpro'),
            'section' => 'recruitpro_search',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 50,
            ),
        ));

        // Enable search analytics
        $wp_customize->add_setting('recruitpro_enable_search_analytics', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_search_analytics', array(
            'label' => __('Enable Search Analytics', 'recruitpro'),
            'description' => __('Track search queries and popular terms.', 'recruitpro'),
            'section' => 'recruitpro_search',
            'type' => 'checkbox',
        ));

        // Enable job search filters
        $wp_customize->add_setting('recruitpro_enable_job_search_filters', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_job_search_filters', array(
            'label' => __('Enable Job Search Filters', 'recruitpro'),
            'description' => __('Show advanced filters for job searches.', 'recruitpro'),
            'section' => 'recruitpro_search',
            'type' => 'checkbox',
        ));
    }
}

// Initialize the search manager
if (class_exists('RecruitPro_Search_Manager')) {
    new RecruitPro_Search_Manager();
}

/**
 * Helper function to display search form
 * 
 * @since 1.0.0
 * @param array $args Search form arguments
 * @return void
 */
function recruitpro_search_form($args = array()) {
    $search_manager = new RecruitPro_Search_Manager();
    echo $search_manager->custom_search_form('');
}

/**
 * Helper function to get search form
 * 
 * @since 1.0.0
 * @param array $args Search form arguments
 * @return string Search form HTML
 */
function recruitpro_get_search_form($args = array()) {
    $search_manager = new RecruitPro_Search_Manager();
    return $search_manager->custom_search_form('');
}

/**
 * Helper function to check if enhanced search is enabled
 * 
 * @since 1.0.0
 * @return bool True if enhanced search is enabled
 */
function recruitpro_search_enabled() {
    return get_theme_mod('recruitpro_enable_enhanced_search', true);
}

/**
 * Helper function to get popular searches
 * 
 * @since 1.0.0
 * @param int $limit Number of searches to return
 * @return array Popular searches
 */
function recruitpro_get_popular_searches($limit = 10) {
    $popular_searches = get_option('recruitpro_popular_searches', array());
    return array_slice($popular_searches, 0, $limit, true);
}