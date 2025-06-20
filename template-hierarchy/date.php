<?php
/**
 * The template for displaying date archives
 *
 * Professional date archive template for recruitment agencies displaying content
 * organized by publication date. Features include monthly industry insights,
 * seasonal hiring trends, historical job market analysis, and chronological
 * company updates with enhanced navigation and filtering capabilities.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/date.php
 * Purpose: Display posts organized by date (year, month, day archives)
 * Context: Recruitment industry content chronology
 * Features: Professional styling, industry insights, seasonal trends
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get current date archive information
$archive_year = get_query_var('year');
$archive_month = get_query_var('monthnum');
$archive_day = get_query_var('day');

// Determine archive type and generate appropriate title
$archive_type = 'yearly';
if ($archive_day) {
    $archive_type = 'daily';
} elseif ($archive_month) {
    $archive_type = 'monthly';
}

// Get archive statistics
$total_posts = $wp_query->found_posts;
$posts_per_page = get_option('posts_per_page');
$current_page = get_query_var('paged') ? get_query_var('paged') : 1;

?>

<main id="primary" class="site-main date-archive-main">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                
                <!-- Archive Header -->
                <header class="page-header date-archive-header">
                    <div class="archive-meta">
                        <span class="archive-type"><?php esc_html_e('Date Archive', 'recruitpro'); ?></span>
                        <time class="archive-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                            <?php echo esc_html(recruitpro_get_date_archive_title()); ?>
                        </time>
                    </div>
                    
                    <h1 class="page-title">
                        <?php echo recruitpro_get_professional_date_title(); ?>
                    </h1>
                    
                    <div class="archive-description">
                        <?php echo recruitpro_get_date_archive_description(); ?>
                    </div>

                    <!-- Archive Statistics -->
                    <div class="archive-stats">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo esc_html(number_format_i18n($total_posts)); ?></span>
                                <span class="stat-label"><?php esc_html_e('Articles', 'recruitpro'); ?></span>
                            </div>
                            
                            <?php if ($archive_type === 'monthly') : ?>
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html(recruitpro_get_monthly_post_count()); ?></span>
                                    <span class="stat-label"><?php esc_html_e('This Month', 'recruitpro'); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="stat-item">
                                <span class="stat-number"><?php echo esc_html(recruitpro_get_archive_reading_time()); ?></span>
                                <span class="stat-label"><?php esc_html_e('Min Read', 'recruitpro'); ?></span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Archive Navigation -->
                <nav class="date-archive-navigation">
                    <div class="archive-nav-wrapper">
                        <!-- Date Range Selector -->
                        <div class="date-range-selector">
                            <label for="archive-date-select" class="screen-reader-text">
                                <?php esc_html_e('Select archive date', 'recruitpro'); ?>
                            </label>
                            <select id="archive-date-select" class="archive-date-select">
                                <option value=""><?php esc_html_e('Navigate to...', 'recruitpro'); ?></option>
                                <?php echo recruitpro_get_date_archive_options(); ?>
                            </select>
                        </div>

                        <!-- View Toggle -->
                        <div class="view-toggle">
                            <button type="button" class="view-toggle-btn active" data-view="list" 
                                    aria-label="<?php esc_attr_e('List view', 'recruitpro'); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                                </svg>
                            </button>
                            <button type="button" class="view-toggle-btn" data-view="grid"
                                    aria-label="<?php esc_attr_e('Grid view', 'recruitpro'); ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM10 13H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zM20 3h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM20 13h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1z"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Sort Options -->
                        <div class="sort-options">
                            <select id="archive-sort" class="archive-sort-select">
                                <option value="date-desc"><?php esc_html_e('Newest First', 'recruitpro'); ?></option>
                                <option value="date-asc"><?php esc_html_e('Oldest First', 'recruitpro'); ?></option>
                                <option value="title-asc"><?php esc_html_e('Title A-Z', 'recruitpro'); ?></option>
                                <option value="comment-count"><?php esc_html_e('Most Discussed', 'recruitpro'); ?></option>
                            </select>
                        </div>
                    </div>
                </nav>

                <!-- Archive Content -->
                <div class="date-archive-content">
                    <?php if (have_posts()) : ?>
                        
                        <!-- Seasonal Insights (for monthly/yearly archives) -->
                        <?php if ($archive_type !== 'daily') : ?>
                            <div class="seasonal-insights">
                                <?php echo recruitpro_get_seasonal_recruitment_insights(); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Posts Loop -->
                        <div class="posts-container" data-view="list">
                            <?php
                            // Start the Loop
                            while (have_posts()) :
                                the_post();
                                ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('archive-post-item'); ?>>
                                    
                                    <!-- Post Thumbnail -->
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>" 
                                               aria-label="<?php the_title_attribute(); ?>">
                                                <?php
                                                the_post_thumbnail('medium', array(
                                                    'alt' => get_the_title(),
                                                    'loading' => 'lazy',
                                                ));
                                                ?>
                                            </a>
                                            
                                            <!-- Post Type Badge -->
                                            <span class="post-type-badge">
                                                <?php echo esc_html(recruitpro_get_post_type_label()); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Post Content -->
                                    <div class="post-content-wrapper">
                                        <header class="entry-header">
                                            <!-- Post Categories -->
                                            <div class="post-categories">
                                                <?php
                                                $categories = get_the_category();
                                                if (!empty($categories)) {
                                                    echo '<span class="category-list">';
                                                    foreach ($categories as $category) {
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-link">';
                                                        echo esc_html($category->name);
                                                        echo '</a>';
                                                    }
                                                    echo '</span>';
                                                }
                                                ?>
                                            </div>

                                            <!-- Post Title -->
                                            <h2 class="entry-title">
                                                <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h2>

                                            <!-- Post Meta -->
                                            <div class="entry-meta">
                                                <time class="entry-date published" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                    <?php echo esc_html(get_the_date()); ?>
                                                </time>
                                                
                                                <span class="meta-separator">•</span>
                                                
                                                <span class="reading-time">
                                                    <?php echo esc_html(recruitpro_get_reading_time()); ?> 
                                                    <?php esc_html_e('min read', 'recruitpro'); ?>
                                                </span>
                                                
                                                <?php if (get_comments_number() > 0) : ?>
                                                    <span class="meta-separator">•</span>
                                                    <a href="<?php comments_link(); ?>" class="comments-link">
                                                        <?php
                                                        printf(
                                                            esc_html(_n('%s comment', '%s comments', get_comments_number(), 'recruitpro')),
                                                            number_format_i18n(get_comments_number())
                                                        );
                                                        ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </header>

                                        <!-- Post Excerpt -->
                                        <div class="entry-summary">
                                            <?php
                                            if (has_excerpt()) {
                                                the_excerpt();
                                            } else {
                                                echo '<p>' . wp_trim_words(get_the_content(), 25, '...') . '</p>';
                                            }
                                            ?>
                                        </div>

                                        <!-- Post Footer -->
                                        <footer class="entry-footer">
                                            <div class="post-tags">
                                                <?php
                                                $tags = get_the_tags();
                                                if (!empty($tags)) {
                                                    echo '<span class="tags-label">' . esc_html__('Tags:', 'recruitpro') . '</span>';
                                                    foreach ($tags as $tag) {
                                                        echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-link">';
                                                        echo esc_html($tag->name);
                                                        echo '</a>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            
                                            <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                <?php esc_html_e('Continue Reading', 'recruitpro'); ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                </svg>
                                            </a>
                                        </footer>
                                    </div>
                                </article>
                                <?php
                            endwhile;
                            ?>
                        </div>

                        <!-- Pagination -->
                        <nav class="archive-pagination" aria-label="<?php esc_attr_e('Archive pagination', 'recruitpro'); ?>">
                            <?php
                            echo paginate_links(array(
                                'total'     => $wp_query->max_num_pages,
                                'current'   => max(1, get_query_var('paged')),
                                'format'    => '?paged=%#%',
                                'show_all'  => false,
                                'end_size'  => 1,
                                'mid_size'  => 2,
                                'prev_next' => true,
                                'prev_text' => esc_html__('‹ Previous', 'recruitpro'),
                                'next_text' => esc_html__('Next ›', 'recruitpro'),
                                'add_args'  => false,
                                'add_fragment' => '',
                            ));
                            ?>
                        </nav>

                    <?php else : ?>
                        
                        <!-- No Posts Found -->
                        <div class="no-posts-found">
                            <div class="no-posts-content">
                                <h2 class="no-posts-title">
                                    <?php esc_html_e('No Content Found', 'recruitpro'); ?>
                                </h2>
                                
                                <p class="no-posts-description">
                                    <?php
                                    printf(
                                        esc_html__('No professional content was published in %s. Explore our other resources or check back later.', 'recruitpro'),
                                        '<strong>' . recruitpro_get_date_archive_title() . '</strong>'
                                    );
                                    ?>
                                </p>
                                
                                <div class="no-posts-actions">
                                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-primary">
                                        <?php esc_html_e('View All Articles', 'recruitpro'); ?>
                                    </a>
                                    
                                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline">
                                        <?php esc_html_e('Browse Current Jobs', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Suggested Content -->
                            <div class="suggested-content">
                                <h3><?php esc_html_e('You Might Be Interested In', 'recruitpro'); ?></h3>
                                <?php echo recruitpro_get_suggested_archive_content(); ?>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>

                <!-- Archive Timeline (for yearly archives) -->
                <?php if ($archive_type === 'yearly' && have_posts()) : ?>
                    <div class="archive-timeline">
                        <h3><?php esc_html_e('Timeline Overview', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_yearly_timeline(); ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <aside class="col-lg-4 col-md-12">
                <div class="sidebar-content">
                    
                    <!-- Archive Navigation Widget -->
                    <div class="widget widget-archive-navigation">
                        <h3 class="widget-title"><?php esc_html_e('Archive Navigation', 'recruitpro'); ?></h3>
                        <div class="archive-calendar">
                            <?php get_calendar(); ?>
                        </div>
                        
                        <div class="quick-links">
                            <h4><?php esc_html_e('Quick Access', 'recruitpro'); ?></h4>
                            <ul class="quick-links-list">
                                <li><a href="<?php echo esc_url(get_year_link(date('Y'))); ?>"><?php esc_html_e('This Year', 'recruitpro'); ?></a></li>
                                <li><a href="<?php echo esc_url(get_month_link(date('Y'), date('m'))); ?>"><?php esc_html_e('This Month', 'recruitpro'); ?></a></li>
                                <li><a href="<?php echo esc_url(get_year_link(date('Y') - 1)); ?>"><?php esc_html_e('Last Year', 'recruitpro'); ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Related Archives -->
                    <div class="widget widget-related-archives">
                        <h3 class="widget-title"><?php esc_html_e('Related Time Periods', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_related_date_archives(); ?>
                    </div>

                    <!-- Popular from Period -->
                    <div class="widget widget-popular-period">
                        <h3 class="widget-title"><?php esc_html_e('Popular from This Period', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_popular_from_period(); ?>
                    </div>

                    <!-- Regular Sidebar -->
                    <?php get_sidebar(); ?>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php
get_footer();

/**
 * Helper Functions for Date Archive
 */

/**
 * Get professional date archive title
 */
function recruitpro_get_professional_date_title() {
    global $wp_query;
    
    $archive_year = get_query_var('year');
    $archive_month = get_query_var('monthnum');
    $archive_day = get_query_var('day');
    
    if ($archive_day) {
        return sprintf(
            esc_html__('Daily Insights: %s', 'recruitpro'),
            get_the_date('F j, Y')
        );
    } elseif ($archive_month) {
        return sprintf(
            esc_html__('Monthly Review: %s', 'recruitpro'),
            get_the_date('F Y')
        );
    } else {
        return sprintf(
            esc_html__('Annual Report: %s', 'recruitpro'),
            $archive_year
        );
    }
}

/**
 * Get date archive description
 */
function recruitpro_get_date_archive_description() {
    $archive_year = get_query_var('year');
    $archive_month = get_query_var('monthnum');
    $archive_day = get_query_var('day');
    
    if ($archive_day) {
        return sprintf(
            '<p>%s</p>',
            esc_html__('Professional insights and industry updates published on this day.', 'recruitpro')
        );
    } elseif ($archive_month) {
        $month_name = date('F', mktime(0, 0, 0, $archive_month, 1));
        return sprintf(
            '<p>%s</p>',
            sprintf(
                esc_html__('Explore recruitment trends, career advice, and industry insights from %s %s.', 'recruitpro'),
                $month_name,
                $archive_year
            )
        );
    } else {
        return sprintf(
            '<p>%s</p>',
            sprintf(
                esc_html__('A comprehensive overview of recruitment industry developments and insights throughout %s.', 'recruitpro'),
                $archive_year
            )
        );
    }
}

/**
 * Get date archive title for navigation
 */
function recruitpro_get_date_archive_title() {
    $archive_year = get_query_var('year');
    $archive_month = get_query_var('monthnum');
    $archive_day = get_query_var('day');
    
    if ($archive_day) {
        return get_the_date('F j, Y');
    } elseif ($archive_month) {
        return get_the_date('F Y');
    } else {
        return $archive_year;
    }
}

/**
 * Get monthly post count
 */
function recruitpro_get_monthly_post_count() {
    global $wp_query;
    return $wp_query->found_posts;
}

/**
 * Get archive reading time estimate
 */
function recruitpro_get_archive_reading_time() {
    global $wp_query;
    $total_words = 0;
    
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            $content = get_the_content();
            $word_count = str_word_count(strip_tags($content));
            $total_words += $word_count;
        }
        wp_reset_postdata();
    }
    
    // Average reading speed: 200 words per minute
    $reading_time = ceil($total_words / 200);
    return max(1, $reading_time);
}

/**
 * Get date archive options for navigation
 */
function recruitpro_get_date_archive_options() {
    $options = '';
    
    // Get available years with posts
    $years = $GLOBALS['wpdb']->get_results(
        "SELECT YEAR(post_date) AS year, COUNT(*) as post_count 
         FROM {$GLOBALS['wpdb']->posts} 
         WHERE post_status = 'publish' AND post_type = 'post'
         GROUP BY YEAR(post_date) 
         ORDER BY year DESC"
    );
    
    foreach ($years as $year_data) {
        $year = $year_data->year;
        $count = $year_data->post_count;
        
        $options .= sprintf(
            '<option value="%s">%s (%s %s)</option>',
            esc_attr(get_year_link($year)),
            esc_html($year),
            esc_html(number_format_i18n($count)),
            esc_html(_n('post', 'posts', $count, 'recruitpro'))
        );
        
        // Get months for current year
        $months = $GLOBALS['wpdb']->get_results(
            $GLOBALS['wpdb']->prepare(
                "SELECT MONTH(post_date) AS month, COUNT(*) as post_count 
                 FROM {$GLOBALS['wpdb']->posts} 
                 WHERE post_status = 'publish' AND post_type = 'post' AND YEAR(post_date) = %d
                 GROUP BY MONTH(post_date) 
                 ORDER BY month DESC",
                $year
            )
        );
        
        foreach ($months as $month_data) {
            $month = $month_data->month;
            $month_count = $month_data->post_count;
            $month_name = date('F', mktime(0, 0, 0, $month, 1));
            
            $options .= sprintf(
                '<option value="%s">&nbsp;&nbsp;%s %s (%s %s)</option>',
                esc_attr(get_month_link($year, $month)),
                esc_html($month_name),
                esc_html($year),
                esc_html(number_format_i18n($month_count)),
                esc_html(_n('post', 'posts', $month_count, 'recruitpro'))
            );
        }
    }
    
    return $options;
}

/**
 * Get seasonal recruitment insights
 */
function recruitpro_get_seasonal_recruitment_insights() {
    $archive_month = get_query_var('monthnum');
    
    if (!$archive_month) {
        return '';
    }
    
    $seasonal_insights = array(
        1 => esc_html__('January traditionally marks a surge in job searching as professionals set new career goals.', 'recruitpro'),
        2 => esc_html__('February sees increased hiring activity as companies finalize their annual recruitment strategies.', 'recruitpro'),
        3 => esc_html__('March begins the spring hiring season with renewed corporate budgets and expansion plans.', 'recruitpro'),
        4 => esc_html__('April continues the spring momentum with peak hiring activity across most industries.', 'recruitpro'),
        5 => esc_html__('May represents optimal timing for career transitions before summer vacation seasons.', 'recruitpro'),
        6 => esc_html__('June focuses on graduate recruitment and summer internship programs.', 'recruitpro'),
        7 => esc_html__('July typically shows slower hiring activity due to vacation schedules.', 'recruitpro'),
        8 => esc_html__('August marks preparation for autumn hiring cycles and strategic planning.', 'recruitpro'),
        9 => esc_html__('September launches the second major hiring season as business activity resumes.', 'recruitpro'),
        10 => esc_html__('October maintains strong hiring momentum with budget planning for next year.', 'recruitpro'),
        11 => esc_html__('November focuses on year-end hiring needs and holiday seasonal positions.', 'recruitpro'),
        12 => esc_html__('December emphasizes planning and preparation for upcoming year recruitment goals.', 'recruitpro'),
    );
    
    if (isset($seasonal_insights[$archive_month])) {
        return sprintf(
            '<div class="seasonal-insight">
                <h4>%s</h4>
                <p>%s</p>
            </div>',
            esc_html__('Seasonal Insight', 'recruitpro'),
            $seasonal_insights[$archive_month]
        );
    }
    
    return '';
}

/**
 * Get post type label for archive display
 */
function recruitpro_get_post_type_label() {
    $post_type = get_post_type();
    
    switch ($post_type) {
        case 'post':
            return esc_html__('Article', 'recruitpro');
        case 'job':
            return esc_html__('Job Listing', 'recruitpro');
        case 'case_study':
            return esc_html__('Case Study', 'recruitpro');
        case 'industry_report':
            return esc_html__('Industry Report', 'recruitpro');
        default:
            $post_type_obj = get_post_type_object($post_type);
            return $post_type_obj ? $post_type_obj->labels->singular_name : esc_html__('Content', 'recruitpro');
    }
}

/**
 * Get reading time for individual post
 */
function recruitpro_get_reading_time() {
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time);
}

/**
 * Get suggested archive content when no posts found
 */
function recruitpro_get_suggested_archive_content() {
    $recent_posts = get_posts(array(
        'numberposts' => 3,
        'post_status' => 'publish',
        'orderby'     => 'date',
        'order'       => 'DESC',
    ));
    
    if (empty($recent_posts)) {
        return '<p>' . esc_html__('No recent content available.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="suggested-posts">';
    foreach ($recent_posts as $post) {
        setup_postdata($post);
        $output .= sprintf(
            '<li><a href="%s">%s</a> <span class="post-date">%s</span></li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html(get_the_date('', $post->ID))
        );
    }
    $output .= '</ul>';
    
    wp_reset_postdata();
    return $output;
}

/**
 * Get yearly timeline for yearly archives
 */
function recruitpro_get_yearly_timeline() {
    $archive_year = get_query_var('year');
    
    $timeline_posts = get_posts(array(
        'numberposts' => 12,
        'date_query'  => array(
            array(
                'year' => $archive_year,
            ),
        ),
        'orderby'     => 'date',
        'order'       => 'ASC',
    ));
    
    if (empty($timeline_posts)) {
        return '<p>' . esc_html__('No timeline data available.', 'recruitpro') . '</p>';
    }
    
    $output = '<div class="timeline-container">';
    $current_month = '';
    
    foreach ($timeline_posts as $post) {
        $post_month = get_the_date('F', $post->ID);
        
        if ($post_month !== $current_month) {
            if ($current_month !== '') {
                $output .= '</div>'; // Close previous month
            }
            $output .= sprintf(
                '<div class="timeline-month"><h4>%s</h4><ul class="timeline-posts">',
                esc_html($post_month)
            );
            $current_month = $post_month;
        }
        
        $output .= sprintf(
            '<li><a href="%s">%s</a> <span class="timeline-date">%s</span></li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html(get_the_date('j', $post->ID))
        );
    }
    
    $output .= '</ul></div></div>'; // Close last month and container
    
    return $output;
}

/**
 * Get related date archives
 */
function recruitpro_get_related_date_archives() {
    $current_year = get_query_var('year');
    $current_month = get_query_var('monthnum');
    
    $output = '<ul class="related-archives">';
    
    // Previous and next year
    if ($current_year) {
        $prev_year = $current_year - 1;
        $next_year = $current_year + 1;
        
        $output .= sprintf(
            '<li><a href="%s">%s %s</a></li>',
            esc_url(get_year_link($prev_year)),
            esc_html__('Previous Year:', 'recruitpro'),
            esc_html($prev_year)
        );
        
        if ($next_year <= date('Y')) {
            $output .= sprintf(
                '<li><a href="%s">%s %s</a></li>',
                esc_url(get_year_link($next_year)),
                esc_html__('Next Year:', 'recruitpro'),
                esc_html($next_year)
            );
        }
    }
    
    // Previous and next month
    if ($current_month && $current_year) {
        $prev_month = $current_month - 1;
        $prev_year_for_month = $current_year;
        
        if ($prev_month < 1) {
            $prev_month = 12;
            $prev_year_for_month = $current_year - 1;
        }
        
        $next_month = $current_month + 1;
        $next_year_for_month = $current_year;
        
        if ($next_month > 12) {
            $next_month = 1;
            $next_year_for_month = $current_year + 1;
        }
        
        $output .= sprintf(
            '<li><a href="%s">%s %s</a></li>',
            esc_url(get_month_link($prev_year_for_month, $prev_month)),
            esc_html__('Previous Month:', 'recruitpro'),
            esc_html(date('F Y', mktime(0, 0, 0, $prev_month, 1, $prev_year_for_month)))
        );
        
        if ($next_year_for_month <= date('Y') && $next_month <= date('n')) {
            $output .= sprintf(
                '<li><a href="%s">%s %s</a></li>',
                esc_url(get_month_link($next_year_for_month, $next_month)),
                esc_html__('Next Month:', 'recruitpro'),
                esc_html(date('F Y', mktime(0, 0, 0, $next_month, 1, $next_year_for_month)))
            );
        }
    }
    
    $output .= '</ul>';
    
    return $output;
}

/**
 * Get popular posts from current archive period
 */
function recruitpro_get_popular_from_period() {
    $archive_year = get_query_var('year');
    $archive_month = get_query_var('monthnum');
    $archive_day = get_query_var('day');
    
    $date_query = array();
    
    if ($archive_day && $archive_month && $archive_year) {
        $date_query = array(
            'year'  => $archive_year,
            'month' => $archive_month,
            'day'   => $archive_day,
        );
    } elseif ($archive_month && $archive_year) {
        $date_query = array(
            'year'  => $archive_year,
            'month' => $archive_month,
        );
    } elseif ($archive_year) {
        $date_query = array(
            'year' => $archive_year,
        );
    }
    
    $popular_posts = get_posts(array(
        'numberposts' => 5,
        'date_query'  => array($date_query),
        'meta_key'    => 'post_views_count',
        'orderby'     => 'meta_value_num',
        'order'       => 'DESC',
    ));
    
    if (empty($popular_posts)) {
        return '<p>' . esc_html__('No popular content available for this period.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="popular-posts">';
    foreach ($popular_posts as $post) {
        $views = get_post_meta($post->ID, 'post_views_count', true);
        $output .= sprintf(
            '<li>
                <a href="%s">%s</a>
                <span class="post-views">%s %s</span>
            </li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html(number_format_i18n($views ?: 0)),
            esc_html__('views', 'recruitpro')
        );
    }
    $output .= '</ul>';
    
    return $output;
}
?>