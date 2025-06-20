<?php
/**
 * The template for displaying category archive pages
 *
 * Professional category template for recruitment agencies showcasing articles
 * organized by topic categories such as career advice, industry insights,
 * recruitment trends, and professional development. Features advanced content
 * filtering, search functionality, and optimized layouts for expert content.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/category.php
 * Purpose: Category archive pages with recruitment focus
 * Dependencies: WordPress core, theme functions
 * Features: Category-specific content, expert filtering, professional presentation
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get category information
$category = get_queried_object();
$category_id = $category->term_id;
$category_name = $category->name;
$category_description = $category->description;
$category_slug = $category->slug;

// Get customizer settings
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_category_sidebar_position', 'right');
$posts_layout = get_theme_mod('recruitpro_category_layout', 'list');
$show_category_image = get_theme_mod('recruitpro_category_show_image', true);
$show_post_count = get_theme_mod('recruitpro_category_show_count', true);
$show_related_categories = get_theme_mod('recruitpro_category_show_related', true);
$enable_sorting = get_theme_mod('recruitpro_category_enable_sorting', true);
$enable_filtering = get_theme_mod('recruitpro_category_enable_filtering', true);

// Get category statistics
$total_posts = $category->count;
global $wp_query;
$current_page = max(1, get_query_var('paged'));
$max_pages = $wp_query->max_num_pages;

// Get category image if available
$category_image_id = get_term_meta($category_id, 'category_image', true);
$category_color = get_term_meta($category_id, 'category_color', true) ?: '#2c3e50';
$category_icon = get_term_meta($category_id, 'category_icon', true);

// Recruitment industry categories mapping
$recruitment_categories = array(
    'career-advice' => array(
        'icon' => 'fas fa-user-graduate',
        'color' => '#3498db',
        'description' => esc_html__('Professional development and career guidance for job seekers', 'recruitpro')
    ),
    'recruitment-trends' => array(
        'icon' => 'fas fa-chart-line',
        'color' => '#e74c3c',
        'description' => esc_html__('Latest trends and insights in the recruitment industry', 'recruitpro')
    ),
    'interview-tips' => array(
        'icon' => 'fas fa-comments',
        'color' => '#f39c12',
        'description' => esc_html__('Expert tips and strategies for successful interviews', 'recruitpro')
    ),
    'industry-insights' => array(
        'icon' => 'fas fa-lightbulb',
        'color' => '#9b59b6',
        'description' => esc_html__('Deep insights into various industries and sectors', 'recruitpro')
    ),
    'hr-resources' => array(
        'icon' => 'fas fa-users-cog',
        'color' => '#1abc9c',
        'description' => esc_html__('Resources and tools for HR professionals', 'recruitpro')
    ),
    'job-market' => array(
        'icon' => 'fas fa-briefcase',
        'color' => '#34495e',
        'description' => esc_html__('Analysis and updates on job market conditions', 'recruitpro')
    ),
    'skills-development' => array(
        'icon' => 'fas fa-cogs',
        'color' => '#e67e22',
        'description' => esc_html__('Professional skills enhancement and training', 'recruitpro')
    ),
    'company-culture' => array(
        'icon' => 'fas fa-heart',
        'color' => '#e91e63',
        'description' => esc_html__('Building positive workplace culture and employee engagement', 'recruitpro')
    )
);

// Get category info from mapping or defaults
$category_info = array();
if (isset($recruitment_categories[$category_slug])) {
    $category_info = $recruitment_categories[$category_slug];
} else {
    $category_info = array(
        'icon' => $category_icon ?: 'fas fa-folder',
        'color' => $category_color,
        'description' => $category_description ?: sprintf(esc_html__('Articles about %s in the recruitment industry', 'recruitpro'), $category_name)
    );
}

// Schema.org markup for category
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $category_name,
    'description' => $category_description ?: $category_info['description'],
    'url' => get_category_link($category_id),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => $category_name,
        'description' => $category_description ?: $category_info['description']
    ),
    'mainEntity' => array(
        '@type' => 'ItemList',
        'numberOfItems' => $total_posts
    )
);

// Get related categories
$related_categories = array();
if ($show_related_categories) {
    $related_categories = get_categories(array(
        'exclude' => $category_id,
        'number' => 6,
        'orderby' => 'count',
        'order' => 'DESC',
        'hide_empty' => true
    ));
}

// Get category authors (users who have posted in this category)
$category_authors = get_users(array(
    'who' => 'authors',
    'has_published_posts' => array('post'),
    'meta_query' => array(
        array(
            'key' => 'category_expertise',
            'value' => $category_slug,
            'compare' => 'LIKE'
        )
    ),
    'number' => 5
));

// Fallback: get authors from posts in this category
if (empty($category_authors)) {
    $category_posts = get_posts(array(
        'category' => $category_id,
        'numberposts' => 20,
        'fields' => 'ids'
    ));
    
    if ($category_posts) {
        $author_ids = array();
        foreach ($category_posts as $post_id) {
            $author_ids[] = get_post_field('post_author', $post_id);
        }
        $author_ids = array_unique($author_ids);
        
        $category_authors = get_users(array(
            'include' => array_slice($author_ids, 0, 5)
        ));
    }
}
?>

<div id="primary" class="content-area category-page">
    <main id="main" class="site-main">
        
        <?php if ($show_breadcrumbs) : ?>
            <div class="breadcrumbs-wrapper">
                <div class="container">
                    <?php recruitpro_breadcrumbs(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            
            <!-- Category Header -->
            <header class="category-header" style="<?php echo esc_attr('--category-color: ' . $category_info['color']); ?>">
                <div class="category-header-content">
                    <div class="row align-items-center">
                        
                        <!-- Category Info -->
                        <div class="col-lg-8">
                            <div class="category-info-wrapper">
                                <div class="category-icon-title">
                                    <div class="category-icon">
                                        <i class="<?php echo esc_attr($category_info['icon']); ?>"></i>
                                    </div>
                                    <div class="category-title-group">
                                        <h1 class="category-title"><?php echo esc_html($category_name); ?></h1>
                                        <div class="category-meta">
                                            <?php if ($show_post_count) : ?>
                                                <span class="posts-count">
                                                    <?php 
                                                    printf(
                                                        _n(
                                                            '%s article',
                                                            '%s articles',
                                                            $total_posts,
                                                            'recruitpro'
                                                        ),
                                                        '<strong>' . number_format_i18n($total_posts) . '</strong>'
                                                    );
                                                    ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if ($max_pages > 1) : ?>
                                                <span class="page-info">
                                                    <?php 
                                                    printf(
                                                        esc_html__('Page %1$s of %2$s', 'recruitpro'),
                                                        '<strong>' . number_format_i18n($current_page) . '</strong>',
                                                        '<strong>' . number_format_i18n($max_pages) . '</strong>'
                                                    );
                                                    ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($category_description || $category_info['description']) : ?>
                                    <div class="category-description">
                                        <?php echo wp_kses_post($category_description ?: $category_info['description']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Category Image -->
                        <?php if ($show_category_image) : ?>
                            <div class="col-lg-4">
                                <div class="category-visual">
                                    <?php if ($category_image_id) : ?>
                                        <div class="category-image">
                                            <?php echo wp_get_attachment_image($category_image_id, 'medium_large', false, array('alt' => $category_name)); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="category-illustration">
                                            <div class="illustration-icon">
                                                <i class="<?php echo esc_attr($category_info['icon']); ?>"></i>
                                            </div>
                                            <div class="illustration-pattern"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
                
                <!-- Category Stats Bar -->
                <div class="category-stats-bar">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="stats-wrapper">
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-newspaper"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-number"><?php echo esc_html(number_format($total_posts)); ?></span>
                                        <span class="stat-label"><?php esc_html_e('Articles', 'recruitpro'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-users"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-number"><?php echo esc_html(count($category_authors)); ?></span>
                                        <span class="stat-label"><?php esc_html_e('Experts', 'recruitpro'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-clock"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-number">
                                            <?php 
                                            $latest_post = get_posts(array('category' => $category_id, 'numberposts' => 1));
                                            echo $latest_post ? esc_html(human_time_diff(strtotime($latest_post[0]->post_date), current_time('timestamp'))) : esc_html__('N/A', 'recruitpro');
                                            ?>
                                        </span>
                                        <span class="stat-label"><?php esc_html_e('Last Updated', 'recruitpro'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-star"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-number"><?php esc_html_e('Expert', 'recruitpro'); ?></span>
                                        <span class="stat-label"><?php esc_html_e('Content', 'recruitpro'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Category Controls -->
            <?php if ($enable_sorting || $enable_filtering) : ?>
                <div class="category-controls">
                    <div class="row align-items-center">
                        
                        <!-- Content Filters -->
                        <?php if ($enable_filtering) : ?>
                            <div class="col-lg-8">
                                <div class="content-filters">
                                    <div class="filter-group">
                                        <label for="content-type-filter" class="filter-label"><?php esc_html_e('Content Type:', 'recruitpro'); ?></label>
                                        <select id="content-type-filter" class="filter-select">
                                            <option value=""><?php esc_html_e('All Types', 'recruitpro'); ?></option>
                                            <option value="guides"><?php esc_html_e('Guides & Tutorials', 'recruitpro'); ?></option>
                                            <option value="insights"><?php esc_html_e('Industry Insights', 'recruitpro'); ?></option>
                                            <option value="tips"><?php esc_html_e('Tips & Advice', 'recruitpro'); ?></option>
                                            <option value="trends"><?php esc_html_e('Trends & Analysis', 'recruitpro'); ?></option>
                                            <option value="resources"><?php esc_html_e('Resources & Tools', 'recruitpro'); ?></option>
                                        </select>
                                    </div>
                                    
                                    <div class="filter-group">
                                        <label for="reading-time-filter" class="filter-label"><?php esc_html_e('Reading Time:', 'recruitpro'); ?></label>
                                        <select id="reading-time-filter" class="filter-select">
                                            <option value=""><?php esc_html_e('Any Length', 'recruitpro'); ?></option>
                                            <option value="quick"><?php esc_html_e('Quick Read (1-3 min)', 'recruitpro'); ?></option>
                                            <option value="medium"><?php esc_html_e('Medium Read (4-8 min)', 'recruitpro'); ?></option>
                                            <option value="long"><?php esc_html_e('In-Depth (8+ min)', 'recruitpro'); ?></option>
                                        </select>
                                    </div>
                                    
                                    <div class="filter-group">
                                        <label for="author-filter" class="filter-label"><?php esc_html_e('Author:', 'recruitpro'); ?></label>
                                        <select id="author-filter" class="filter-select">
                                            <option value=""><?php esc_html_e('All Authors', 'recruitpro'); ?></option>
                                            <?php foreach ($category_authors as $author) : ?>
                                                <option value="<?php echo esc_attr($author->ID); ?>">
                                                    <?php echo esc_html($author->display_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Sorting Options -->
                        <?php if ($enable_sorting) : ?>
                            <div class="col-lg-4">
                                <div class="sorting-controls">
                                    <div class="sort-group">
                                        <label for="sort-filter" class="filter-label"><?php esc_html_e('Sort by:', 'recruitpro'); ?></label>
                                        <select id="sort-filter" class="filter-select">
                                            <option value="date-desc"><?php esc_html_e('Newest First', 'recruitpro'); ?></option>
                                            <option value="date-asc"><?php esc_html_e('Oldest First', 'recruitpro'); ?></option>
                                            <option value="popular"><?php esc_html_e('Most Popular', 'recruitpro'); ?></option>
                                            <option value="commented"><?php esc_html_e('Most Discussed', 'recruitpro'); ?></option>
                                            <option value="title-asc"><?php esc_html_e('Title A-Z', 'recruitpro'); ?></option>
                                        </select>
                                    </div>
                                    
                                    <div class="view-toggle">
                                        <button type="button" class="view-toggle-btn <?php echo ($posts_layout === 'list') ? 'active' : ''; ?>" data-layout="list" title="<?php esc_attr_e('List View', 'recruitpro'); ?>">
                                            <i class="fas fa-list"></i>
                                        </button>
                                        <button type="button" class="view-toggle-btn <?php echo ($posts_layout === 'grid') ? 'active' : ''; ?>" data-layout="grid" title="<?php esc_attr_e('Grid View', 'recruitpro'); ?>">
                                            <i class="fas fa-th"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
            <?php endif; ?>

            <!-- Category Content -->
            <div class="category-content">
                <div class="row">
                    
                    <!-- Main Content -->
                    <div class="<?php echo ($sidebar_position === 'left') ? 'col-lg-9 order-2' : (($sidebar_position === 'right') ? 'col-lg-9' : 'col-12'); ?>">
                        
                        <?php if (have_posts()) : ?>
                            
                            <div class="posts-container <?php echo esc_attr($posts_layout); ?>-layout" id="posts-container">
                                
                                <?php while (have_posts()) : the_post(); ?>
                                    
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item category-post-item'); ?>>
                                        
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                                    <?php 
                                                    $thumbnail_size = ($posts_layout === 'grid') ? 'medium' : 'medium_large';
                                                    the_post_thumbnail($thumbnail_size, array(
                                                        'alt' => the_title_attribute(array('echo' => false)),
                                                        'loading' => 'lazy'
                                                    )); 
                                                    ?>
                                                </a>
                                                
                                                <!-- Content Type Badge -->
                                                <div class="content-type-badge">
                                                    <?php
                                                    $post_tags = get_the_tags();
                                                    $content_type = 'Article';
                                                    
                                                    if ($post_tags) {
                                                        foreach ($post_tags as $tag) {
                                                            if (in_array($tag->slug, array('guide', 'tutorial', 'how-to'))) {
                                                                $content_type = 'Guide';
                                                                break;
                                                            } elseif (in_array($tag->slug, array('insight', 'analysis', 'report'))) {
                                                                $content_type = 'Insight';
                                                                break;
                                                            } elseif (in_array($tag->slug, array('tip', 'advice', 'recommendation'))) {
                                                                $content_type = 'Tips';
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    
                                                    echo esc_html($content_type);
                                                    ?>
                                                </div>
                                                
                                                <!-- Reading Time -->
                                                <div class="reading-time">
                                                    <i class="fas fa-clock"></i>
                                                    <?php echo recruitpro_reading_time(); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="post-content">
                                            
                                            <div class="post-meta">
                                                <span class="post-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                        <?php echo esc_html(get_the_date()); ?>
                                                    </time>
                                                </span>
                                                
                                                <span class="post-author">
                                                    <i class="fas fa-user"></i>
                                                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                        <?php echo esc_html(get_the_author()); ?>
                                                    </a>
                                                </span>
                                                
                                                <?php if (comments_open() || get_comments_number()) : ?>
                                                    <span class="post-comments">
                                                        <i class="fas fa-comments"></i>
                                                        <a href="<?php comments_link(); ?>">
                                                            <?php 
                                                            printf(
                                                                _n(
                                                                    '%s Comment',
                                                                    '%s Comments',
                                                                    get_comments_number(),
                                                                    'recruitpro'
                                                                ),
                                                                number_format_i18n(get_comments_number())
                                                            );
                                                            ?>
                                                        </a>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <span class="post-views">
                                                    <i class="fas fa-eye"></i>
                                                    <?php echo esc_html(get_post_meta(get_the_ID(), 'post_views', true) ?: '0'); ?> <?php esc_html_e('views', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                            
                                            <header class="post-header">
                                                <h2 class="post-title">
                                                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h2>
                                            </header>
                                            
                                            <div class="post-excerpt">
                                                <?php 
                                                if (has_excerpt()) {
                                                    the_excerpt();
                                                } else {
                                                    echo wp_trim_words(get_the_content(), 30, '...');
                                                }
                                                ?>
                                            </div>
                                            
                                            <?php if (has_tag()) : ?>
                                                <div class="post-tags">
                                                    <i class="fas fa-tags"></i>
                                                    <?php the_tags('', ', ', ''); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="post-actions">
                                                <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                    <?php esc_html_e('Read Full Article', 'recruitpro'); ?>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                
                                                <div class="post-engagement">
                                                    <button class="engagement-btn like-btn" data-post-id="<?php the_ID(); ?>">
                                                        <i class="far fa-heart"></i>
                                                        <span class="like-count"><?php echo esc_html(get_post_meta(get_the_ID(), 'post_likes', true) ?: '0'); ?></span>
                                                    </button>
                                                    
                                                    <button class="engagement-btn share-btn" data-post-id="<?php the_ID(); ?>" data-post-title="<?php echo esc_attr(get_the_title()); ?>" data-post-url="<?php echo esc_attr(get_permalink()); ?>">
                                                        <i class="fas fa-share-alt"></i>
                                                        <?php esc_html_e('Share', 'recruitpro'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                    </article>
                                    
                                <?php endwhile; ?>
                                
                            </div>
                            
                            <!-- Pagination -->
                            <div class="category-pagination">
                                <?php
                                the_posts_pagination(array(
                                    'mid_size' => 2,
                                    'prev_text' => '<i class="fas fa-chevron-left"></i> ' . esc_html__('Previous', 'recruitpro'),
                                    'next_text' => esc_html__('Next', 'recruitpro') . ' <i class="fas fa-chevron-right"></i>',
                                    'before_page_number' => '<span class="screen-reader-text">' . esc_html__('Page', 'recruitpro') . ' </span>',
                                ));
                                ?>
                            </div>
                            
                        <?php else : ?>
                            
                            <!-- No Posts Found -->
                            <div class="no-posts-found">
                                <div class="no-posts-content">
                                    <div class="no-posts-icon">
                                        <i class="<?php echo esc_attr($category_info['icon']); ?>"></i>
                                    </div>
                                    <h2 class="no-posts-title"><?php esc_html_e('No Articles in This Category', 'recruitpro'); ?></h2>
                                    <p class="no-posts-message">
                                        <?php printf(esc_html__('We\'re working on adding content to the %s category. Check back soon for expert insights and valuable resources.', 'recruitpro'), '<strong>' . esc_html($category_name) . '</strong>'); ?>
                                    </p>
                                    
                                    <div class="alternative-suggestions">
                                        <h3><?php esc_html_e('Explore Other Topics', 'recruitpro'); ?></h3>
                                        <div class="suggestion-categories">
                                            <?php foreach (array_slice($related_categories, 0, 4) as $related_cat) : ?>
                                                <a href="<?php echo esc_url(get_category_link($related_cat->term_id)); ?>" class="suggestion-link">
                                                    <?php 
                                                    $related_slug = $related_cat->slug;
                                                    $related_icon = isset($recruitment_categories[$related_slug]) ? $recruitment_categories[$related_slug]['icon'] : 'fas fa-folder';
                                                    ?>
                                                    <i class="<?php echo esc_attr($related_icon); ?>"></i>
                                                    <?php echo esc_html($related_cat->name); ?>
                                                    <span class="suggestion-count"><?php echo esc_html($related_cat->count); ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="no-posts-actions">
                                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-primary">
                                            <i class="fas fa-arrow-left"></i>
                                            <?php esc_html_e('Browse All Articles', 'recruitpro'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary">
                                            <i class="fas fa-envelope"></i>
                                            <?php esc_html_e('Request Content', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>

                    <!-- Sidebar -->
                    <?php if ($sidebar_position !== 'none') : ?>
                        <aside class="<?php echo ($sidebar_position === 'left') ? 'col-lg-3 order-1' : 'col-lg-3'; ?> sidebar category-sidebar">
                            
                            <!-- Category Experts Widget -->
                            <?php if (!empty($category_authors)) : ?>
                                <div class="widget category-experts-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Category Experts', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <p><?php printf(esc_html__('Meet our %s specialists:', 'recruitpro'), esc_html(strtolower($category_name))); ?></p>
                                        
                                        <div class="experts-list">
                                            <?php foreach ($category_authors as $expert) : ?>
                                                <div class="expert-item">
                                                    <div class="expert-avatar">
                                                        <a href="<?php echo esc_url(get_author_posts_url($expert->ID)); ?>">
                                                            <?php echo get_avatar($expert->ID, 50); ?>
                                                        </a>
                                                    </div>
                                                    <div class="expert-info">
                                                        <h4 class="expert-name">
                                                            <a href="<?php echo esc_url(get_author_posts_url($expert->ID)); ?>">
                                                                <?php echo esc_html($expert->display_name); ?>
                                                            </a>
                                                        </h4>
                                                        <span class="expert-title">
                                                            <?php echo esc_html(get_the_author_meta('job_title', $expert->ID) ?: esc_html__('Recruitment Specialist', 'recruitpro')); ?>
                                                        </span>
                                                        <div class="expert-stats">
                                                            <?php 
                                                            $expert_posts = count_user_posts($expert->ID, 'post');
                                                            printf(
                                                                _n(
                                                                    '%s article',
                                                                    '%s articles',
                                                                    $expert_posts,
                                                                    'recruitpro'
                                                                ),
                                                                number_format_i18n($expert_posts)
                                                            );
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Related Categories Widget -->
                            <?php if ($show_related_categories && !empty($related_categories)) : ?>
                                <div class="widget related-categories-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Related Topics', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <div class="related-categories-list">
                                            <?php foreach ($related_categories as $related_cat) : ?>
                                                <div class="related-category-item">
                                                    <a href="<?php echo esc_url(get_category_link($related_cat->term_id)); ?>" class="category-link">
                                                        <div class="category-link-icon">
                                                            <?php 
                                                            $related_slug = $related_cat->slug;
                                                            $related_info = isset($recruitment_categories[$related_slug]) ? $recruitment_categories[$related_slug] : array('icon' => 'fas fa-folder', 'color' => '#6c757d');
                                                            ?>
                                                            <i class="<?php echo esc_attr($related_info['icon']); ?>" style="color: <?php echo esc_attr($related_info['color']); ?>"></i>
                                                        </div>
                                                        <div class="category-link-content">
                                                            <span class="category-link-name"><?php echo esc_html($related_cat->name); ?></span>
                                                            <span class="category-link-count"><?php echo esc_html($related_cat->count); ?> <?php esc_html_e('articles', 'recruitpro'); ?></span>
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Popular in Category Widget -->
                            <div class="widget popular-category-widget">
                                <h3 class="widget-title"><?php printf(esc_html__('Popular in %s', 'recruitpro'), esc_html($category_name)); ?></h3>
                                <div class="widget-content">
                                    <?php
                                    $popular_posts = get_posts(array(
                                        'category' => $category_id,
                                        'numberposts' => 5,
                                        'orderby' => 'comment_count',
                                        'order' => 'DESC',
                                        'post_status' => 'publish'
                                    ));
                                    
                                    if ($popular_posts) :
                                    ?>
                                        <ul class="popular-posts-list">
                                            <?php foreach ($popular_posts as $popular_post) : ?>
                                                <li class="popular-post-item">
                                                    <?php if (has_post_thumbnail($popular_post->ID)) : ?>
                                                        <div class="popular-post-thumbnail">
                                                            <a href="<?php echo esc_url(get_permalink($popular_post->ID)); ?>">
                                                                <?php echo get_the_post_thumbnail($popular_post->ID, 'thumbnail'); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="popular-post-content">
                                                        <h4 class="popular-post-title">
                                                            <a href="<?php echo esc_url(get_permalink($popular_post->ID)); ?>">
                                                                <?php echo esc_html(get_the_title($popular_post->ID)); ?>
                                                            </a>
                                                        </h4>
                                                        <div class="popular-post-meta">
                                                            <span class="post-date">
                                                                <i class="fas fa-calendar"></i>
                                                                <?php echo esc_html(get_the_date('', $popular_post->ID)); ?>
                                                            </span>
                                                            <span class="post-comments">
                                                                <i class="fas fa-comments"></i>
                                                                <?php echo esc_html(get_comments_number($popular_post->ID)); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p><?php esc_html_e('No popular articles yet in this category.', 'recruitpro'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Category Newsletter Widget -->
                            <div class="widget category-newsletter-widget">
                                <h3 class="widget-title"><?php printf(esc_html__('%s Updates', 'recruitpro'), esc_html($category_name)); ?></h3>
                                <div class="widget-content">
                                    <div class="newsletter-icon">
                                        <i class="<?php echo esc_attr($category_info['icon']); ?>"></i>
                                    </div>
                                    <p><?php printf(esc_html__('Get the latest %s articles and insights delivered to your inbox.', 'recruitpro'), esc_html(strtolower($category_name))); ?></p>
                                    
                                    <form class="category-newsletter-form" method="post" action="">
                                        <div class="form-group">
                                            <input type="email" 
                                                   class="form-control" 
                                                   placeholder="<?php esc_attr_e('Your email address', 'recruitpro'); ?>" 
                                                   name="email"
                                                   required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-paper-plane"></i>
                                            <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                        </button>
                                        <input type="hidden" name="category" value="<?php echo esc_attr($category_slug); ?>">
                                        <input type="hidden" name="action" value="category_newsletter_signup">
                                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('category_newsletter_nonce'); ?>">
                                    </form>
                                    
                                    <small class="newsletter-note">
                                        <?php esc_html_e('Weekly digest • No spam • Unsubscribe anytime', 'recruitpro'); ?>
                                    </small>
                                </div>
                            </div>

                            <?php if (is_active_sidebar('category-sidebar')) : ?>
                                <?php dynamic_sidebar('category-sidebar'); ?>
                            <?php endif; ?>
                            
                        </aside>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
        
    </main>
</div>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<style>
/* Category Page Specific Styles */
.category-page {
    background: #f8f9fa;
    min-height: 80vh;
}

/* Category Header */
.category-header {
    background: white;
    margin-bottom: 2rem;
    overflow: hidden;
    position: relative;
}

.category-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--category-color, var(--primary-color));
}

.category-header-content {
    padding: 3rem 0 2rem;
}

.category-icon-title {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.category-icon {
    width: 80px;
    height: 80px;
    background: var(--category-color, var(--primary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    flex-shrink: 0;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.category-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.category-meta {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    align-items: center;
    font-size: 0.95rem;
    color: #6c757d;
}

.category-description {
    font-size: 1.2rem;
    color: #6c757d;
    line-height: 1.6;
    max-width: 600px;
}

.category-visual {
    text-align: center;
}

.category-image img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.category-illustration {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--category-color, var(--primary-color)) 0%, rgba(255,255,255,0.1) 100%);
    border-radius: 50%;
}

.illustration-icon {
    font-size: 4rem;
    color: white;
    z-index: 2;
}

.illustration-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="4"/></g></svg>') repeat;
    border-radius: 50%;
}

.category-stats-bar {
    background: #f8f9fa;
    border-top: 1px solid #e1e5e9;
    padding: 1.5rem 0;
}

.stats-wrapper {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-align: center;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--category-color, var(--primary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.stat-content {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.stat-number {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.stat-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Category Controls */
.category-controls {
    background: white;
    padding: 1.5rem 0;
    margin-bottom: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.content-filters {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 180px;
}

.filter-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}

.filter-select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e1e5e9;
    border-radius: 4px;
    background: white;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--category-color, var(--primary-color));
}

.sorting-controls {
    display: flex;
    gap: 1.5rem;
    align-items: end;
    justify-content: flex-end;
}

.sort-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 150px;
}

.view-toggle {
    display: flex;
    border: 1px solid #e1e5e9;
    border-radius: 4px;
    overflow: hidden;
}

.view-toggle-btn {
    padding: 0.5rem 0.75rem;
    background: white;
    border: none;
    cursor: pointer;
    color: #6c757d;
    transition: all 0.3s ease;
    border-right: 1px solid #e1e5e9;
}

.view-toggle-btn:last-child {
    border-right: none;
}

.view-toggle-btn:hover,
.view-toggle-btn.active {
    background: var(--category-color, var(--primary-color));
    color: white;
}

/* Posts */
.posts-container.list-layout {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.posts-container.grid-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.category-post-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-top: 3px solid var(--category-color, var(--primary-color));
}

.category-post-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.list-layout .category-post-item {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.list-layout .post-thumbnail {
    flex-shrink: 0;
    width: 300px;
    position: relative;
}

.list-layout .post-content {
    flex: 1;
    padding: 2rem;
}

.grid-layout .post-content {
    padding: 1.5rem;
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
}

.post-thumbnail img {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease;
}

.category-post-item:hover .post-thumbnail img {
    transform: scale(1.05);
}

.content-type-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: var(--category-color, var(--primary-color));
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.reading-time {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.post-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.post-meta a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-meta a:hover {
    color: var(--category-color, var(--primary-color));
}

.post-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: var(--category-color, var(--primary-color));
}

.post-excerpt {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.post-tags {
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.post-tags a {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-tags a:hover {
    color: var(--category-color, var(--primary-color));
}

.post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.read-more-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--category-color, var(--primary-color));
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.read-more-link:hover {
    transform: translateX(5px);
    text-decoration: none;
}

.post-engagement {
    display: flex;
    gap: 0.75rem;
}

.engagement-btn {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.engagement-btn:hover {
    color: var(--category-color, var(--primary-color));
}

/* No Posts Found */
.no-posts-found {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.no-posts-icon {
    width: 100px;
    height: 100px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    color: var(--category-color, var(--primary-color));
    font-size: 2.5rem;
}

.no-posts-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.no-posts-message {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.alternative-suggestions {
    margin: 2rem 0;
}

.alternative-suggestions h3 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.suggestion-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.suggestion-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    color: #495057;
    text-decoration: none;
    transition: all 0.3s ease;
}

.suggestion-link:hover {
    background: var(--category-color, var(--primary-color));
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

.suggestion-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-left: auto;
}

.no-posts-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Sidebar */
.category-sidebar .widget {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    border-top: 3px solid var(--category-color, var(--primary-color));
}

.category-sidebar .widget-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--category-color, var(--primary-color));
    padding-bottom: 0.5rem;
}

.experts-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.expert-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.expert-item:hover {
    background: #e9ecef;
    transform: translateY(-1px);
}

.expert-avatar {
    flex-shrink: 0;
}

.expert-avatar img {
    border-radius: 50%;
}

.expert-info {
    flex: 1;
}

.expert-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.expert-name a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.expert-name a:hover {
    color: var(--category-color, var(--primary-color));
}

.expert-title {
    display: block;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.expert-stats {
    font-size: 0.8rem;
    color: #495057;
}

.related-categories-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.related-category-item {
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.related-category-item:hover {
    transform: translateY(-2px);
}

.category-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    color: inherit;
    text-decoration: none;
    transition: all 0.3s ease;
}

.category-link:hover {
    background: #e9ecef;
    text-decoration: none;
}

.category-link-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    flex-shrink: 0;
}

.category-link-content {
    flex: 1;
}

.category-link-name {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.category-link-count {
    font-size: 0.85rem;
    color: #6c757d;
}

.popular-posts-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.popular-post-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.popular-post-item:last-child {
    border-bottom: none;
}

.popular-post-thumbnail {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 6px;
    overflow: hidden;
}

.popular-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.popular-post-content {
    flex: 1;
}

.popular-post-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.popular-post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.popular-post-title a:hover {
    color: var(--category-color, var(--primary-color));
}

.popular-post-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.8rem;
    color: #6c757d;
}

.popular-post-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.category-newsletter-widget {
    text-align: center;
}

.newsletter-icon {
    width: 60px;
    height: 60px;
    background: var(--category-color, var(--primary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.category-newsletter-form {
    margin: 1.5rem 0;
}

.newsletter-note {
    color: #6c757d;
    font-size: 0.8rem;
    line-height: 1.4;
}

/* Pagination */
.category-pagination {
    margin: 3rem 0;
    text-align: center;
}

.category-pagination .nav-links {
    display: inline-flex;
    gap: 0.5rem;
    align-items: center;
}

.category-pagination .page-numbers {
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid #e1e5e9;
    color: #495057;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-pagination .page-numbers:hover,
.category-pagination .page-numbers.current {
    background: var(--category-color, var(--primary-color));
    color: white;
    border-color: var(--category-color, var(--primary-color));
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-title {
        font-size: 2rem;
    }
    
    .category-icon-title {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .category-meta {
        justify-content: center;
        flex-direction: column;
        gap: 1rem;
    }
    
    .stats-wrapper {
        flex-direction: column;
        text-align: center;
    }
    
    .content-filters {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .sorting-controls {
        flex-direction: column;
        gap: 1rem;
    }
    
    .posts-container.grid-layout {
        grid-template-columns: 1fr;
    }
    
    .list-layout .category-post-item {
        flex-direction: column;
        gap: 0;
    }
    
    .list-layout .post-thumbnail {
        width: 100%;
    }
    
    .list-layout .post-content {
        padding: 1.5rem;
    }
    
    .post-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .suggestion-categories {
        grid-template-columns: 1fr;
    }
    
    .no-posts-actions {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 576px) {
    .category-header-content {
        padding: 2rem 0 1rem;
    }
    
    .category-title {
        font-size: 1.5rem;
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .stat-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .post-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .expert-item {
        flex-direction: column;
        text-align: center;
    }
    
    .popular-post-item {
        flex-direction: column;
        text-align: center;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .category-post-item,
    .expert-item,
    .suggestion-link,
    .read-more-link {
        transition: none;
    }
    
    .category-post-item:hover,
    .expert-item:hover,
    .suggestion-link:hover {
        transform: none;
    }
    
    .read-more-link:hover {
        transform: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .category-post-item,
    .category-sidebar .widget {
        border: 2px solid #000;
    }
    
    .category-controls {
        border: 1px solid #000;
    }
}

/* Print styles */
@media print {
    .category-controls,
    .category-pagination,
    .category-sidebar,
    .post-engagement {
        display: none;
    }
    
    .category-post-item {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    
    .category-header {
        background: white;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // View toggle functionality
    const viewToggles = document.querySelectorAll('.view-toggle-btn');
    const postsContainer = document.getElementById('posts-container');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const layout = this.dataset.layout;
            
            // Update active state
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update container layout
            if (postsContainer) {
                postsContainer.className = `posts-container ${layout}-layout`;
            }
            
            // Store preference
            localStorage.setItem('category_layout_preference', layout);
        });
    });
    
    // Load saved layout preference
    const savedLayout = localStorage.getItem('category_layout_preference');
    if (savedLayout && postsContainer) {
        const layoutToggle = document.querySelector(`[data-layout="${savedLayout}"]`);
        if (layoutToggle) {
            layoutToggle.click();
        }
    }
    
    // Filtering functionality
    const filters = document.querySelectorAll('.filter-select');
    
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            // In a real implementation, this would filter posts via AJAX
            console.log('Filter changed:', this.id, this.value);
        });
    });
    
    // Post engagement (like and share)
    const likeButtons = document.querySelectorAll('.like-btn');
    const shareButtons = document.querySelectorAll('.share-btn');
    
    likeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const likeCount = this.querySelector('.like-count');
            const icon = this.querySelector('i');
            
            // Toggle like state
            if (icon.classList.contains('far')) {
                icon.className = 'fas fa-heart';
                likeCount.textContent = parseInt(likeCount.textContent) + 1;
                this.style.color = '#e74c3c';
            } else {
                icon.className = 'far fa-heart';
                likeCount.textContent = parseInt(likeCount.textContent) - 1;
                this.style.color = '';
            }
            
            // Track like event
            if (typeof gtag !== 'undefined') {
                gtag('event', 'post_like', {
                    'post_id': postId,
                    'category': '<?php echo esc_js($category_slug); ?>'
                });
            }
        });
    });
    
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const postTitle = this.dataset.postTitle;
            const postUrl = this.dataset.postUrl;
            
            if (navigator.share) {
                navigator.share({
                    title: postTitle,
                    url: postUrl
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(postUrl).then(() => {
                    showNotification('Link copied to clipboard!');
                });
            }
            
            // Track share event
            if (typeof gtag !== 'undefined') {
                gtag('event', 'post_share', {
                    'post_title': postTitle,
                    'category': '<?php echo esc_js($category_slug); ?>'
                });
            }
        });
    });
    
    // Category newsletter subscription
    const newsletterForms = document.querySelectorAll('.category-newsletter-form');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const email = formData.get('email');
            const category = formData.get('category');
            
            // Submit via AJAX
            fetch(recruitpro_theme.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Successfully subscribed to <?php echo esc_js($category_name); ?> updates!');
                    this.reset();
                } else {
                    showNotification('Subscription failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Subscription failed. Please try again.', 'error');
            });
        });
    });
    
    // Show notification function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 6px;
            z-index: 9999;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateY(0)';
            notification.style.opacity = '1';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateY(-20px)';
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Track category page interactions
    if (typeof gtag !== 'undefined') {
        // Track category page view
        gtag('event', 'category_page_view', {
            'category_name': '<?php echo esc_js($category_name); ?>',
            'category_slug': '<?php echo esc_js($category_slug); ?>',
            'posts_count': <?php echo esc_js($total_posts); ?>
        });
        
        // Track filter usage
        filters.forEach(filter => {
            filter.addEventListener('change', function() {
                gtag('event', 'category_filter_used', {
                    'filter_type': this.id,
                    'filter_value': this.value,
                    'category': '<?php echo esc_js($category_slug); ?>'
                });
            });
        });
        
        // Track expert profile clicks
        const expertLinks = document.querySelectorAll('.expert-name a');
        expertLinks.forEach(link => {
            link.addEventListener('click', function() {
                gtag('event', 'expert_profile_click', {
                    'expert_name': this.textContent.trim(),
                    'source_category': '<?php echo esc_js($category_slug); ?>'
                });
            });
        });
    }
    
    // Lazy loading for post thumbnails
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Keyboard shortcuts for filtering
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case '1':
                    e.preventDefault();
                    const listToggle = document.querySelector('[data-layout="list"]');
                    if (listToggle) listToggle.click();
                    break;
                case '2':
                    e.preventDefault();
                    const gridToggle = document.querySelector('[data-layout="grid"]');
                    if (gridToggle) gridToggle.click();
                    break;
            }
        }
    });
    
});

// Utility function for reading time calculation
function recruitpro_reading_time() {
    // This would typically be calculated server-side
    return '<?php echo esc_html__("5 min read", "recruitpro"); ?>';
}
</script>

<?php get_footer(); ?>