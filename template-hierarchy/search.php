<?php
/**
 * The template for displaying search results
 *
 * Professional search results template for recruitment agencies featuring
 * advanced search functionality, content filtering, and industry-specific
 * result presentation. Designed to help job seekers find opportunities and
 * clients discover relevant recruitment services and insights efficiently.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/search.php
 * Purpose: Display search results with professional filtering and presentation
 * Context: Recruitment industry content search
 * Features: Advanced filtering, content categorization, professional layout
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get search parameters
$search_query = get_search_query();
$search_results_count = $GLOBALS['wp_query']->found_posts;
$current_page = get_query_var('paged') ? get_query_var('paged') : 1;
$posts_per_page = get_option('posts_per_page');

// Calculate result statistics
$start_result = (($current_page - 1) * $posts_per_page) + 1;
$end_result = min($current_page * $posts_per_page, $search_results_count);

?>

<main id="primary" class="site-main search-main">
    <div class="container">
        
        <!-- Search Header -->
        <header class="search-header">
            <div class="row">
                <div class="col-12">
                    <div class="search-header-content">
                        
                        <!-- Search Title -->
                        <h1 class="search-title">
                            <?php
                            if (!empty($search_query)) {
                                printf(
                                    esc_html__('Search Results for "%s"', 'recruitpro'),
                                    '<span class="search-term">' . esc_html($search_query) . '</span>'
                                );
                            } else {
                                esc_html_e('Search Results', 'recruitpro');
                            }
                            ?>
                        </h1>
                        
                        <!-- Search Stats -->
                        <div class="search-stats">
                            <?php if ($search_results_count > 0) : ?>
                                <p class="search-count">
                                    <?php
                                    printf(
                                        esc_html(_n(
                                            'Found %1$s result (%2$s seconds)',
                                            'Found %1$s results (%2$s seconds)',
                                            $search_results_count,
                                            'recruitpro'
                                        )),
                                        '<strong>' . number_format_i18n($search_results_count) . '</strong>',
                                        '<span class="search-time">' . timer_stop() . '</span>'
                                    );
                                    ?>
                                </p>
                                
                                <p class="search-range">
                                    <?php
                                    printf(
                                        esc_html__('Showing %1$s-%2$s of %3$s results', 'recruitpro'),
                                        number_format_i18n($start_result),
                                        number_format_i18n($end_result),
                                        number_format_i18n($search_results_count)
                                    );
                                    ?>
                                </p>
                            <?php else : ?>
                                <p class="search-count">
                                    <?php esc_html_e('No results found', 'recruitpro'); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Enhanced Search Form -->
                        <div class="search-form-enhanced">
                            <form class="enhanced-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                <div class="search-input-wrapper">
                                    <div class="search-input-group">
                                        <input type="search" 
                                               name="s" 
                                               class="search-field" 
                                               placeholder="<?php esc_attr_e('Refine your search...', 'recruitpro'); ?>"
                                               value="<?php echo esc_attr($search_query); ?>"
                                               autocomplete="off">
                                        
                                        <div class="search-filters">
                                            <select name="search_type" class="search-type-filter">
                                                <option value=""><?php esc_html_e('All Content', 'recruitpro'); ?></option>
                                                <option value="job" <?php selected(get_query_var('search_type'), 'job'); ?>>
                                                    <?php esc_html_e('Job Opportunities', 'recruitpro'); ?>
                                                </option>
                                                <option value="post" <?php selected(get_query_var('search_type'), 'post'); ?>>
                                                    <?php esc_html_e('Articles & Insights', 'recruitpro'); ?>
                                                </option>
                                                <option value="page" <?php selected(get_query_var('search_type'), 'page'); ?>>
                                                    <?php esc_html_e('Company Information', 'recruitpro'); ?>
                                                </option>
                                            </select>
                                            
                                            <select name="search_category" class="search-category-filter">
                                                <option value=""><?php esc_html_e('All Categories', 'recruitpro'); ?></option>
                                                <?php
                                                $categories = get_categories(array('hide_empty' => true));
                                                foreach ($categories as $category) {
                                                    echo '<option value="' . esc_attr($category->slug) . '" ' . 
                                                         selected(get_query_var('search_category'), $category->slug, false) . '>' . 
                                                         esc_html($category->name) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <button type="submit" class="search-submit">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                            </svg>
                                            <span class="screen-reader-text"><?php esc_html_e('Search', 'recruitpro'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Search Content -->
        <div class="row">
            <div class="col-lg-8">
                
                <!-- Search Results Controls -->
                <?php if ($search_results_count > 0) : ?>
                    <div class="search-controls">
                        <div class="search-controls-left">
                            <!-- Content Type Filter -->
                            <div class="content-type-tabs">
                                <?php
                                $content_types = recruitpro_get_search_content_types();
                                $current_type = get_query_var('search_type');
                                
                                foreach ($content_types as $type => $data) {
                                    $active_class = ($current_type === $type || (empty($current_type) && $type === 'all')) ? 'active' : '';
                                    $url = add_query_arg(array('s' => $search_query, 'search_type' => $type === 'all' ? '' : $type), home_url('/'));
                                    
                                    echo '<a href="' . esc_url($url) . '" class="content-type-tab ' . $active_class . '">';
                                    echo esc_html($data['label']);
                                    echo ' <span class="type-count">(' . $data['count'] . ')</span>';
                                    echo '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="search-controls-right">
                            <!-- Sort Options -->
                            <div class="search-sort">
                                <select id="search-sort-select" class="search-sort-select">
                                    <option value="relevance"><?php esc_html_e('Most Relevant', 'recruitpro'); ?></option>
                                    <option value="date-desc"><?php esc_html_e('Newest First', 'recruitpro'); ?></option>
                                    <option value="date-asc"><?php esc_html_e('Oldest First', 'recruitpro'); ?></option>
                                    <option value="title-asc"><?php esc_html_e('Title A-Z', 'recruitpro'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Search Results -->
                <div class="search-results-container">
                    
                    <?php if (have_posts()) : ?>
                        
                        <!-- Results List -->
                        <div class="search-results-list">
                            
                            <?php while (have_posts()) : the_post(); ?>
                                
                                <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                                    
                                    <!-- Result Header -->
                                    <header class="result-header">
                                        
                                        <!-- Content Type Badge -->
                                        <div class="result-meta-top">
                                            <span class="result-type-badge result-type-<?php echo esc_attr(get_post_type()); ?>">
                                                <?php echo esc_html(recruitpro_get_post_type_label()); ?>
                                            </span>
                                            
                                            <!-- Job-specific meta -->
                                            <?php if (get_post_type() === 'job') : ?>
                                                <div class="job-meta-badges">
                                                    <?php
                                                    $job_type = get_post_meta(get_the_ID(), '_job_type', true);
                                                    $job_location = get_post_meta(get_the_ID(), '_job_location', true);
                                                    
                                                    if ($job_type) {
                                                        echo '<span class="job-type-badge">' . esc_html($job_type) . '</span>';
                                                    }
                                                    
                                                    if ($job_location) {
                                                        echo '<span class="job-location-badge">' . esc_html($job_location) . '</span>';
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Result Title -->
                                        <h2 class="result-title">
                                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                <?php
                                                // Highlight search terms in title
                                                $title = get_the_title();
                                                echo recruitpro_highlight_search_terms($title, $search_query);
                                                ?>
                                            </a>
                                        </h2>
                                        
                                        <!-- Result URL -->
                                        <div class="result-url">
                                            <a href="<?php the_permalink(); ?>" class="result-permalink">
                                                <?php echo esc_url(get_permalink()); ?>
                                            </a>
                                        </div>
                                    </header>

                                    <!-- Result Content -->
                                    <div class="result-content">
                                        
                                        <!-- Result Excerpt -->
                                        <div class="result-excerpt">
                                            <?php
                                            $excerpt = recruitpro_get_search_excerpt($search_query);
                                            echo wp_kses_post($excerpt);
                                            ?>
                                        </div>
                                        
                                        <!-- Result Meta -->
                                        <div class="result-meta">
                                            <div class="meta-items">
                                                <!-- Date -->
                                                <div class="meta-item meta-date">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                                                    </svg>
                                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                        <?php echo esc_html(get_the_date()); ?>
                                                    </time>
                                                </div>
                                                
                                                <!-- Author (for posts) -->
                                                <?php if (get_post_type() === 'post') : ?>
                                                    <div class="meta-item meta-author">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                                        </svg>
                                                        <span class="author-name"><?php echo esc_html(get_the_author()); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Categories -->
                                                <?php if (get_post_type() === 'post') : ?>
                                                    <?php
                                                    $categories = get_the_category();
                                                    if (!empty($categories)) :
                                                        ?>
                                                        <div class="meta-item meta-categories">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                                            </svg>
                                                            <?php
                                                            $cat_links = array();
                                                            foreach (array_slice($categories, 0, 2) as $category) {
                                                                $cat_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                                            }
                                                            echo implode(', ', $cat_links);
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <!-- Reading Time -->
                                                <div class="meta-item meta-reading-time">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    <span class="reading-time">
                                                        <?php echo esc_html(recruitpro_get_reading_time()); ?> 
                                                        <?php esc_html_e('min read', 'recruitpro'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="result-actions">
                                                <a href="<?php the_permalink(); ?>" class="result-read-more">
                                                    <?php
                                                    if (get_post_type() === 'job') {
                                                        esc_html_e('View Job Details', 'recruitpro');
                                                    } else {
                                                        esc_html_e('Read More', 'recruitpro');
                                                    }
                                                    ?>
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                
                            <?php endwhile; ?>
                            
                        </div>

                        <!-- Pagination -->
                        <nav class="search-pagination" aria-label="<?php esc_attr_e('Search results pagination', 'recruitpro'); ?>">
                            <?php
                            echo paginate_links(array(
                                'total'     => $GLOBALS['wp_query']->max_num_pages,
                                'current'   => max(1, get_query_var('paged')),
                                'format'    => '?paged=%#%',
                                'show_all'  => false,
                                'end_size'  => 1,
                                'mid_size'  => 2,
                                'prev_next' => true,
                                'prev_text' => esc_html__('‹ Previous', 'recruitpro'),
                                'next_text' => esc_html__('Next ›', 'recruitpro'),
                                'add_args'  => array(
                                    's' => $search_query,
                                    'search_type' => get_query_var('search_type'),
                                    'search_category' => get_query_var('search_category'),
                                ),
                            ));
                            ?>
                        </nav>

                    <?php else : ?>
                        
                        <!-- No Results Found -->
                        <div class="no-search-results">
                            <div class="no-results-content">
                                
                                <!-- No Results Icon -->
                                <div class="no-results-icon">
                                    <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                    </svg>
                                </div>
                                
                                <!-- No Results Title -->
                                <h2 class="no-results-title">
                                    <?php
                                    if (!empty($search_query)) {
                                        printf(
                                            esc_html__('No results found for "%s"', 'recruitpro'),
                                            '<em>' . esc_html($search_query) . '</em>'
                                        );
                                    } else {
                                        esc_html_e('No search results found', 'recruitpro');
                                    }
                                    ?>
                                </h2>
                                
                                <!-- No Results Description -->
                                <p class="no-results-description">
                                    <?php esc_html_e('We couldn\'t find any content matching your search criteria. Try adjusting your search terms or explore our suggestions below.', 'recruitpro'); ?>
                                </p>
                                
                                <!-- Search Suggestions -->
                                <div class="search-suggestions">
                                    <h3><?php esc_html_e('Search Suggestions:', 'recruitpro'); ?></h3>
                                    <ul class="suggestions-list">
                                        <li><?php esc_html_e('Check your spelling and try again', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Try different or more general keywords', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Use fewer keywords for broader results', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Try searching without filters', 'recruitpro'); ?></li>
                                    </ul>
                                </div>
                                
                                <!-- Alternative Search -->
                                <div class="alternative-search">
                                    <h3><?php esc_html_e('Try a new search:', 'recruitpro'); ?></h3>
                                    <?php get_search_form(); ?>
                                </div>
                                
                                <!-- Popular Searches -->
                                <div class="popular-searches">
                                    <h3><?php esc_html_e('Popular Searches:', 'recruitpro'); ?></h3>
                                    <div class="popular-search-tags">
                                        <?php
                                        $popular_searches = array(
                                            'remote jobs' => home_url('/?s=remote+jobs'),
                                            'software engineer' => home_url('/?s=software+engineer'),
                                            'marketing manager' => home_url('/?s=marketing+manager'),
                                            'career advice' => home_url('/?s=career+advice'),
                                            'recruitment tips' => home_url('/?s=recruitment+tips'),
                                            'salary guide' => home_url('/?s=salary+guide'),
                                        );
                                        
                                        foreach ($popular_searches as $term => $url) {
                                            echo '<a href="' . esc_url($url) . '" class="popular-search-tag">' . esc_html($term) . '</a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <!-- Helpful Links -->
                                <div class="helpful-links">
                                    <h3><?php esc_html_e('Or explore these sections:', 'recruitpro'); ?></h3>
                                    <div class="helpful-links-grid">
                                        <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="helpful-link">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M20 6h-2.5l-1.4-1.4C15.9 4.3 15.6 4 15.3 4H8.7c-.3 0-.6.3-.8.6L6.5 6H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                            </svg>
                                            <span class="link-text">
                                                <strong><?php esc_html_e('Current Job Opportunities', 'recruitpro'); ?></strong>
                                                <small><?php esc_html_e('Browse available positions', 'recruitpro'); ?></small>
                                            </span>
                                        </a>
                                        
                                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="helpful-link">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                            </svg>
                                            <span class="link-text">
                                                <strong><?php esc_html_e('Industry Insights', 'recruitpro'); ?></strong>
                                                <small><?php esc_html_e('Career advice and trends', 'recruitpro'); ?></small>
                                            </span>
                                        </a>
                                        
                                        <a href="<?php echo esc_url(home_url('/services/')); ?>" class="helpful-link">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span class="link-text">
                                                <strong><?php esc_html_e('Our Services', 'recruitpro'); ?></strong>
                                                <small><?php esc_html_e('Recruitment solutions', 'recruitpro'); ?></small>
                                            </span>
                                        </a>
                                        
                                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="helpful-link">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            <span class="link-text">
                                                <strong><?php esc_html_e('Contact Us', 'recruitpro'); ?></strong>
                                                <small><?php esc_html_e('Get personalized help', 'recruitpro'); ?></small>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="col-lg-4">
                <div class="search-sidebar">
                    
                    <!-- Search Tips Widget -->
                    <div class="widget widget-search-tips">
                        <h3 class="widget-title"><?php esc_html_e('Search Tips', 'recruitpro'); ?></h3>
                        <div class="search-tips-content">
                            <ul class="tips-list">
                                <li><?php esc_html_e('Use specific job titles for better results', 'recruitpro'); ?></li>
                                <li><?php esc_html_e('Try location-based searches (e.g., "remote" or city names)', 'recruitpro'); ?></li>
                                <li><?php esc_html_e('Use quotation marks for exact phrases', 'recruitpro'); ?></li>
                                <li><?php esc_html_e('Filter by content type to narrow results', 'recruitpro'); ?></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Popular Content Widget -->
                    <div class="widget widget-popular-content">
                        <h3 class="widget-title"><?php esc_html_e('Most Searched Content', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_popular_search_content(); ?>
                    </div>

                    <!-- Related Categories Widget -->
                    <div class="widget widget-search-categories">
                        <h3 class="widget-title"><?php esc_html_e('Browse by Category', 'recruitpro'); ?></h3>
                        <ul class="search-categories-list">
                            <?php
                            $categories = get_categories(array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 8,
                                'hide_empty' => true
                            ));
                            
                            foreach ($categories as $category) {
                                echo '<li class="category-item">';
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">';
                                echo esc_html($category->name);
                                echo '<span class="category-count">(' . $category->count . ')</span>';
                                echo '</a>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Recent Content Widget -->
                    <div class="widget widget-recent-content">
                        <h3 class="widget-title"><?php esc_html_e('Recent Content', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_recent_search_content(); ?>
                    </div>

                    <!-- Regular Sidebar -->
                    <?php
                    if (is_active_sidebar('sidebar-1')) {
                        dynamic_sidebar('sidebar-1');
                    }
                    ?>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php
get_footer();

/**
 * Helper Functions for Search Template
 */

/**
 * Get search content types with counts
 */
function recruitpro_get_search_content_types() {
    $search_query = get_search_query();
    
    $types = array(
        'all' => array('label' => esc_html__('All', 'recruitpro'), 'count' => 0),
        'job' => array('label' => esc_html__('Jobs', 'recruitpro'), 'count' => 0),
        'post' => array('label' => esc_html__('Articles', 'recruitpro'), 'count' => 0),
        'page' => array('label' => esc_html__('Pages', 'recruitpro'), 'count' => 0),
    );
    
    // Get counts for each type
    foreach (array_keys($types) as $type) {
        if ($type === 'all') {
            $types[$type]['count'] = $GLOBALS['wp_query']->found_posts;
        } else {
            $query = new WP_Query(array(
                's' => $search_query,
                'post_type' => $type,
                'posts_per_page' => -1,
                'fields' => 'ids',
            ));
            $types[$type]['count'] = $query->found_posts;
        }
    }
    
    return $types;
}

/**
 * Get post type label for search results
 */
function recruitpro_get_post_type_label() {
    $post_type = get_post_type();
    
    $labels = array(
        'post' => esc_html__('Article', 'recruitpro'),
        'page' => esc_html__('Page', 'recruitpro'),
        'job' => esc_html__('Job Opportunity', 'recruitpro'),
        'case_study' => esc_html__('Case Study', 'recruitpro'),
        'testimonial' => esc_html__('Testimonial', 'recruitpro'),
    );
    
    return isset($labels[$post_type]) ? $labels[$post_type] : esc_html__('Content', 'recruitpro');
}

/**
 * Highlight search terms in text
 */
function recruitpro_highlight_search_terms($text, $search_query) {
    if (empty($search_query) || empty($text)) {
        return $text;
    }
    
    $search_terms = explode(' ', $search_query);
    
    foreach ($search_terms as $term) {
        if (strlen($term) > 2) { // Only highlight terms longer than 2 characters
            $text = preg_replace(
                '/(' . preg_quote($term, '/') . ')/i',
                '<mark class="search-highlight">$1</mark>',
                $text
            );
        }
    }
    
    return $text;
}

/**
 * Get enhanced search excerpt
 */
function recruitpro_get_search_excerpt($search_query) {
    $content = get_the_content();
    $excerpt = get_the_excerpt();
    
    // If we have a search query, try to find relevant excerpts
    if (!empty($search_query) && !empty($content)) {
        $search_terms = explode(' ', $search_query);
        $content_lower = strtolower($content);
        
        foreach ($search_terms as $term) {
            if (strlen($term) > 2) {
                $pos = strpos($content_lower, strtolower($term));
                if ($pos !== false) {
                    // Extract context around the found term
                    $start = max(0, $pos - 100);
                    $length = 250;
                    $excerpt = substr($content, $start, $length);
                    
                    // Clean up the excerpt
                    $excerpt = strip_tags($excerpt);
                    $excerpt = '...' . trim($excerpt) . '...';
                    break;
                }
            }
        }
    }
    
    // Fallback to regular excerpt
    if (empty($excerpt)) {
        $excerpt = wp_trim_words($content, 25, '...');
    }
    
    // Highlight search terms
    return recruitpro_highlight_search_terms($excerpt, $search_query);
}

/**
 * Get reading time for search results
 */
function recruitpro_get_reading_time() {
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time);
}

/**
 * Get popular search content
 */
function recruitpro_get_popular_search_content() {
    $popular_posts = get_posts(array(
        'numberposts' => 5,
        'post_status' => 'publish',
        'meta_key' => 'post_views_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    ));
    
    if (empty($popular_posts)) {
        $popular_posts = get_posts(array(
            'numberposts' => 5,
            'post_status' => 'publish',
            'orderby' => 'comment_count',
            'order' => 'DESC'
        ));
    }
    
    if (empty($popular_posts)) {
        return '<p>' . esc_html__('No popular content available.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="popular-content-list">';
    foreach ($popular_posts as $post) {
        $output .= sprintf(
            '<li class="popular-content-item">
                <a href="%s" class="popular-content-link">
                    <span class="content-title">%s</span>
                    <span class="content-type">%s</span>
                </a>
            </li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html(recruitpro_get_post_type_label())
        );
    }
    $output .= '</ul>';
    
    return $output;
}

/**
 * Get recent search content
 */
function recruitpro_get_recent_search_content() {
    $recent_posts = get_posts(array(
        'numberposts' => 5,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (empty($recent_posts)) {
        return '<p>' . esc_html__('No recent content available.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="recent-content-list">';
    foreach ($recent_posts as $post) {
        $post_type_obj = get_post_type_object($post->post_type);
        $type_label = $post_type_obj ? $post_type_obj->labels->singular_name : 'Content';
        
        $output .= sprintf(
            '<li class="recent-content-item">
                <a href="%s" class="recent-content-link">
                    <span class="content-title">%s</span>
                    <div class="content-meta">
                        <span class="content-type">%s</span>
                        <span class="content-date">%s</span>
                    </div>
                </a>
            </li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html($type_label),
            esc_html(get_the_date('', $post->ID))
        );
    }
    $output .= '</ul>';
    
    return $output;
}
?>