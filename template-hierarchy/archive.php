<?php
/**
 * The template for displaying archive pages
 *
 * Professional archive template for recruitment agencies handling blog posts,
 * category archives, tag archives, author archives, and date archives.
 * Features advanced filtering, search functionality, and optimized layouts
 * for recruitment-focused content presentation.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/archive.php
 * Purpose: Archive pages for posts, categories, tags, authors, and dates
 * Dependencies: WordPress core, theme functions
 * Features: Advanced filtering, pagination, search, responsive layout
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get archive information
$archive_title = get_the_archive_title();
$archive_description = get_the_archive_description();
$queried_object = get_queried_object();

// Get customizer settings
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_archive_sidebar_position', 'right');
$posts_layout = get_theme_mod('recruitpro_archive_layout', 'list');
$posts_per_page = get_theme_mod('recruitpro_archive_posts_per_page', get_option('posts_per_page'));
$show_excerpts = get_theme_mod('recruitpro_archive_show_excerpts', true);
$excerpt_length = get_theme_mod('recruitpro_archive_excerpt_length', 25);
$show_featured_images = get_theme_mod('recruitpro_archive_show_images', true);
$show_post_meta = get_theme_mod('recruitpro_archive_show_meta', true);
$show_read_more = get_theme_mod('recruitpro_archive_show_read_more', true);
$enable_filtering = get_theme_mod('recruitpro_archive_enable_filtering', true);
$enable_search = get_theme_mod('recruitpro_archive_enable_search', true);

// Archive type detection
$is_category = is_category();
$is_tag = is_tag();
$is_author = is_author();
$is_date = is_date();
$is_tax = is_tax();
$post_type = get_post_type();

// Get total posts count
global $wp_query;
$total_posts = $wp_query->found_posts;
$current_page = max(1, get_query_var('paged'));
$max_pages = $wp_query->max_num_pages;

// Schema.org markup
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $archive_title,
    'description' => $archive_description ? wp_strip_all_tags($archive_description) : sprintf(__('Archive page for %s', 'recruitpro'), $archive_title),
    'url' => get_pagenum_link(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'mainEntity' => array(
        '@type' => 'ItemList',
        'numberOfItems' => $total_posts
    )
);

// Add specific schema for different archive types
if ($is_category && $queried_object) {
    $schema_data['about'] = array(
        '@type' => 'Thing',
        'name' => $queried_object->name,
        'description' => $queried_object->description
    );
} elseif ($is_author && $queried_object) {
    $schema_data['author'] = array(
        '@type' => 'Person',
        'name' => $queried_object->display_name,
        'description' => get_the_author_meta('description', $queried_object->ID)
    );
}

// Get categories for filtering
$categories = get_categories(array(
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 10
));

// Get tags for filtering
$tags = get_tags(array(
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 15
));

// Get authors with posts
$authors = get_users(array(
    'who' => 'authors',
    'has_published_posts' => array('post'),
    'orderby' => 'post_count',
    'order' => 'DESC'
));
?>

<div id="primary" class="content-area archive-page">
    <main id="main" class="site-main">
        
        <?php if ($show_breadcrumbs) : ?>
            <div class="breadcrumbs-wrapper">
                <div class="container">
                    <?php recruitpro_breadcrumbs(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            
            <!-- Archive Header -->
            <header class="archive-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="archive-title-wrapper">
                            <h1 class="archive-title"><?php echo wp_kses_post($archive_title); ?></h1>
                            
                            <?php if ($archive_description) : ?>
                                <div class="archive-description">
                                    <?php echo wp_kses_post($archive_description); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="archive-meta">
                                <span class="posts-count">
                                    <?php 
                                    printf(
                                        _n(
                                            '%s article found',
                                            '%s articles found',
                                            $total_posts,
                                            'recruitpro'
                                        ),
                                        '<strong>' . number_format_i18n($total_posts) . '</strong>'
                                    );
                                    ?>
                                </span>
                                
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
                    
                    <div class="col-lg-4">
                        <div class="archive-stats">
                            <?php if ($is_category && $queried_object) : ?>
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-folder"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-label"><?php esc_html_e('Category', 'recruitpro'); ?></span>
                                        <span class="stat-value"><?php echo esc_html($queried_object->name); ?></span>
                                    </div>
                                </div>
                            <?php elseif ($is_tag && $queried_object) : ?>
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-tag"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-label"><?php esc_html_e('Tag', 'recruitpro'); ?></span>
                                        <span class="stat-value"><?php echo esc_html($queried_object->name); ?></span>
                                    </div>
                                </div>
                            <?php elseif ($is_author && $queried_object) : ?>
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-user"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-label"><?php esc_html_e('Author', 'recruitpro'); ?></span>
                                        <span class="stat-value"><?php echo esc_html($queried_object->display_name); ?></span>
                                    </div>
                                </div>
                            <?php elseif ($is_date) : ?>
                                <div class="stat-item">
                                    <span class="stat-icon"><i class="fas fa-calendar"></i></span>
                                    <div class="stat-content">
                                        <span class="stat-label"><?php esc_html_e('Archive', 'recruitpro'); ?></span>
                                        <span class="stat-value"><?php echo get_the_date('F Y'); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Archive Controls -->
            <div class="archive-controls">
                <div class="row align-items-center">
                    
                    <!-- Search -->
                    <?php if ($enable_search) : ?>
                        <div class="col-lg-4">
                            <div class="archive-search">
                                <form role="search" method="get" class="search-form archive-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                                    <div class="search-input-wrapper">
                                        <input type="search" 
                                               class="search-field" 
                                               placeholder="<?php esc_attr_e('Search articles...', 'recruitpro'); ?>" 
                                               value="<?php echo get_search_query(); ?>" 
                                               name="s" 
                                               autocomplete="off" />
                                        <button type="submit" class="search-submit">
                                            <i class="fas fa-search"></i>
                                            <span class="sr-only"><?php esc_html_e('Search', 'recruitpro'); ?></span>
                                        </button>
                                    </div>
                                    <?php if (!is_home() && !is_front_page()) : ?>
                                        <input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Filtering and Sorting -->
                    <?php if ($enable_filtering) : ?>
                        <div class="col-lg-8">
                            <div class="archive-filters">
                                <div class="filter-group">
                                    <label for="category-filter" class="filter-label"><?php esc_html_e('Category:', 'recruitpro'); ?></label>
                                    <select id="category-filter" class="filter-select">
                                        <option value=""><?php esc_html_e('All Categories', 'recruitpro'); ?></option>
                                        <?php foreach ($categories as $category) : ?>
                                            <option value="<?php echo esc_attr($category->slug); ?>" <?php selected(is_category($category->term_id)); ?>>
                                                <?php echo esc_html($category->name); ?> (<?php echo esc_html($category->count); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="sort-filter" class="filter-label"><?php esc_html_e('Sort:', 'recruitpro'); ?></label>
                                    <select id="sort-filter" class="filter-select">
                                        <option value="date-desc" <?php selected(get_query_var('orderby'), 'date'); ?>><?php esc_html_e('Newest First', 'recruitpro'); ?></option>
                                        <option value="date-asc"><?php esc_html_e('Oldest First', 'recruitpro'); ?></option>
                                        <option value="title-asc"><?php esc_html_e('Title A-Z', 'recruitpro'); ?></option>
                                        <option value="title-desc"><?php esc_html_e('Title Z-A', 'recruitpro'); ?></option>
                                        <option value="comment-count"><?php esc_html_e('Most Commented', 'recruitpro'); ?></option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="layout-toggle" class="filter-label"><?php esc_html_e('View:', 'recruitpro'); ?></label>
                                    <div class="layout-toggle-group">
                                        <button type="button" class="layout-toggle <?php echo ($posts_layout === 'list') ? 'active' : ''; ?>" data-layout="list" title="<?php esc_attr_e('List View', 'recruitpro'); ?>">
                                            <i class="fas fa-list"></i>
                                        </button>
                                        <button type="button" class="layout-toggle <?php echo ($posts_layout === 'grid') ? 'active' : ''; ?>" data-layout="grid" title="<?php esc_attr_e('Grid View', 'recruitpro'); ?>">
                                            <i class="fas fa-th"></i>
                                        </button>
                                        <button type="button" class="layout-toggle <?php echo ($posts_layout === 'masonry') ? 'active' : ''; ?>" data-layout="masonry" title="<?php esc_attr_e('Masonry View', 'recruitpro'); ?>">
                                            <i class="fas fa-th-large"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>

            <div class="archive-content">
                <div class="row">
                    
                    <!-- Main Content -->
                    <div class="<?php echo ($sidebar_position === 'left') ? 'col-lg-9 order-2' : (($sidebar_position === 'right') ? 'col-lg-9' : 'col-12'); ?>">
                        
                        <?php if (have_posts()) : ?>
                            
                            <div class="posts-container <?php echo esc_attr($posts_layout); ?>-layout" id="posts-container">
                                
                                <?php while (have_posts()) : the_post(); ?>
                                    
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                                        
                                        <?php if ($show_featured_images && has_post_thumbnail()) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                                    <?php 
                                                    $thumbnail_size = ($posts_layout === 'grid' || $posts_layout === 'masonry') ? 'medium' : 'medium_large';
                                                    the_post_thumbnail($thumbnail_size, array(
                                                        'alt' => the_title_attribute(array('echo' => false)),
                                                        'loading' => 'lazy'
                                                    )); 
                                                    ?>
                                                </a>
                                                
                                                <!-- Post Format Icon -->
                                                <?php if (get_post_format()) : ?>
                                                    <div class="post-format-icon">
                                                        <?php 
                                                        $format = get_post_format();
                                                        switch ($format) {
                                                            case 'video':
                                                                echo '<i class="fas fa-play"></i>';
                                                                break;
                                                            case 'audio':
                                                                echo '<i class="fas fa-music"></i>';
                                                                break;
                                                            case 'gallery':
                                                                echo '<i class="fas fa-images"></i>';
                                                                break;
                                                            case 'quote':
                                                                echo '<i class="fas fa-quote-left"></i>';
                                                                break;
                                                            case 'link':
                                                                echo '<i class="fas fa-link"></i>';
                                                                break;
                                                            default:
                                                                echo '<i class="fas fa-file-alt"></i>';
                                                        }
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="post-content">
                                            
                                            <?php if ($show_post_meta) : ?>
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
                                                    
                                                    <?php if (has_category()) : ?>
                                                        <span class="post-categories">
                                                            <i class="fas fa-folder"></i>
                                                            <?php the_category(', '); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    
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
                                                    
                                                    <span class="post-reading-time">
                                                        <i class="fas fa-clock"></i>
                                                        <?php echo recruitpro_reading_time(); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <header class="post-header">
                                                <h2 class="post-title">
                                                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h2>
                                            </header>
                                            
                                            <?php if ($show_excerpts) : ?>
                                                <div class="post-excerpt">
                                                    <?php 
                                                    if (has_excerpt()) {
                                                        the_excerpt();
                                                    } else {
                                                        echo wp_trim_words(get_the_content(), $excerpt_length, '...');
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (has_tag()) : ?>
                                                <div class="post-tags">
                                                    <i class="fas fa-tags"></i>
                                                    <?php the_tags('', ', ', ''); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($show_read_more) : ?>
                                                <div class="post-actions">
                                                    <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                        <?php esc_html_e('Read More', 'recruitpro'); ?>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            
                                        </div>
                                        
                                    </article>
                                    
                                <?php endwhile; ?>
                                
                            </div>
                            
                            <!-- Pagination -->
                            <div class="archive-pagination">
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
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h2 class="no-posts-title"><?php esc_html_e('No Articles Found', 'recruitpro'); ?></h2>
                                    <p class="no-posts-message">
                                        <?php esc_html_e('We couldn\'t find any articles matching your criteria. Try adjusting your search or browse our categories below.', 'recruitpro'); ?>
                                    </p>
                                    
                                    <!-- Alternative Content -->
                                    <div class="alternative-content">
                                        <h3><?php esc_html_e('Browse Categories', 'recruitpro'); ?></h3>
                                        <div class="category-links">
                                            <?php 
                                            foreach (array_slice($categories, 0, 6) as $category) {
                                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-link">';
                                                echo '<i class="fas fa-folder"></i>';
                                                echo esc_html($category->name) . ' (' . esc_html($category->count) . ')';
                                                echo '</a>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="no-posts-actions">
                                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-primary">
                                            <i class="fas fa-arrow-left"></i>
                                            <?php esc_html_e('Back to Blog', 'recruitpro'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-secondary">
                                            <i class="fas fa-home"></i>
                                            <?php esc_html_e('Go Home', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>

                    <!-- Sidebar -->
                    <?php if ($sidebar_position !== 'none') : ?>
                        <aside class="<?php echo ($sidebar_position === 'left') ? 'col-lg-3 order-1' : 'col-lg-3'; ?> sidebar archive-sidebar">
                            
                            <!-- Categories Widget -->
                            <?php if (!empty($categories)) : ?>
                                <div class="widget categories-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Categories', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <ul class="categories-list">
                                            <?php foreach ($categories as $category) : ?>
                                                <li class="category-item <?php echo is_category($category->term_id) ? 'current-category' : ''; ?>">
                                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                                        <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                                        <span class="category-count"><?php echo esc_html($category->count); ?></span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Popular Tags -->
                            <?php if (!empty($tags)) : ?>
                                <div class="widget tags-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Popular Tags', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <div class="tags-cloud">
                                            <?php foreach ($tags as $tag) : ?>
                                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
                                                   class="tag-link <?php echo is_tag($tag->slug) ? 'current-tag' : ''; ?>"
                                                   style="font-size: <?php echo min(16, 12 + ($tag->count * 2)); ?>px;">
                                                    <?php echo esc_html($tag->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Recent Posts -->
                            <div class="widget recent-posts-widget">
                                <h3 class="widget-title"><?php esc_html_e('Recent Articles', 'recruitpro'); ?></h3>
                                <div class="widget-content">
                                    <?php
                                    $recent_posts = get_posts(array(
                                        'numberposts' => 5,
                                        'post_status' => 'publish',
                                        'orderby' => 'date',
                                        'order' => 'DESC',
                                        'exclude' => array(get_the_ID())
                                    ));
                                    
                                    if ($recent_posts) :
                                    ?>
                                        <ul class="recent-posts-list">
                                            <?php foreach ($recent_posts as $recent_post) : ?>
                                                <li class="recent-post-item">
                                                    <?php if (has_post_thumbnail($recent_post->ID)) : ?>
                                                        <div class="recent-post-thumbnail">
                                                            <a href="<?php echo esc_url(get_permalink($recent_post->ID)); ?>">
                                                                <?php echo get_the_post_thumbnail($recent_post->ID, 'thumbnail'); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="recent-post-content">
                                                        <h4 class="recent-post-title">
                                                            <a href="<?php echo esc_url(get_permalink($recent_post->ID)); ?>">
                                                                <?php echo esc_html(get_the_title($recent_post->ID)); ?>
                                                            </a>
                                                        </h4>
                                                        <div class="recent-post-meta">
                                                            <span class="recent-post-date">
                                                                <i class="fas fa-calendar"></i>
                                                                <?php echo esc_html(get_the_date('', $recent_post->ID)); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Authors -->
                            <?php if (!empty($authors) && !$is_author) : ?>
                                <div class="widget authors-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Our Authors', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <ul class="authors-list">
                                            <?php foreach (array_slice($authors, 0, 5) as $author) : ?>
                                                <li class="author-item">
                                                    <div class="author-avatar">
                                                        <?php echo get_avatar($author->ID, 40); ?>
                                                    </div>
                                                    <div class="author-info">
                                                        <h4 class="author-name">
                                                            <a href="<?php echo esc_url(get_author_posts_url($author->ID)); ?>">
                                                                <?php echo esc_html($author->display_name); ?>
                                                            </a>
                                                        </h4>
                                                        <span class="author-posts-count">
                                                            <?php 
                                                            printf(
                                                                _n(
                                                                    '%s article',
                                                                    '%s articles',
                                                                    count_user_posts($author->ID),
                                                                    'recruitpro'
                                                                ),
                                                                number_format_i18n(count_user_posts($author->ID))
                                                            );
                                                            ?>
                                                        </span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Archive Calendar -->
                            <?php if (!$is_date) : ?>
                                <div class="widget calendar-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Archive Calendar', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <?php get_calendar(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (is_active_sidebar('archive-sidebar')) : ?>
                                <?php dynamic_sidebar('archive-sidebar'); ?>
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
/* Archive Page Specific Styles */
.archive-page {
    background: #f8f9fa;
    min-height: 80vh;
}

.archive-header {
    background: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-bottom: 1px solid #e1e5e9;
}

.archive-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.archive-description {
    font-size: 1.2rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.archive-meta {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    align-items: center;
}

.archive-meta span {
    color: #495057;
    font-size: 0.95rem;
}

.archive-stats {
    text-align: right;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    justify-content: flex-end;
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}

.stat-content {
    text-align: right;
}

.stat-label {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

/* Archive Controls */
.archive-controls {
    background: white;
    padding: 1.5rem 0;
    margin-bottom: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.archive-search .search-input-wrapper {
    position: relative;
}

.archive-search .search-field {
    width: 100%;
    padding: 0.75rem 3rem 0.75rem 1rem;
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color 0.3s ease;
}

.archive-search .search-field:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb), 0.1);
}

.archive-search .search-submit {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0.5rem;
    transition: color 0.3s ease;
}

.archive-search .search-submit:hover {
    color: var(--primary-color);
}

.archive-filters {
    display: flex;
    gap: 1.5rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
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
    min-width: 150px;
    transition: border-color 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.layout-toggle-group {
    display: flex;
    border: 1px solid #e1e5e9;
    border-radius: 4px;
    overflow: hidden;
}

.layout-toggle {
    padding: 0.5rem 0.75rem;
    background: white;
    border: none;
    cursor: pointer;
    color: #6c757d;
    transition: all 0.3s ease;
    border-right: 1px solid #e1e5e9;
}

.layout-toggle:last-child {
    border-right: none;
}

.layout-toggle:hover,
.layout-toggle.active {
    background: var(--primary-color);
    color: white;
}

/* Posts Container */
.posts-container {
    margin-bottom: 3rem;
}

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

.posts-container.masonry-layout {
    column-count: 2;
    column-gap: 2rem;
}

.posts-container.masonry-layout .post-item {
    break-inside: avoid;
    margin-bottom: 2rem;
}

/* Post Items */
.post-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.post-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.list-layout .post-item {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.list-layout .post-thumbnail {
    flex-shrink: 0;
    width: 300px;
}

.list-layout .post-content {
    flex: 1;
    padding: 2rem;
}

.grid-layout .post-item,
.masonry-layout .post-item {
    display: flex;
    flex-direction: column;
}

.grid-layout .post-content,
.masonry-layout .post-content {
    padding: 1.5rem;
    flex: 1;
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

.post-item:hover .post-thumbnail img {
    transform: scale(1.05);
}

.post-format-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
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
    color: var(--primary-color);
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
    color: var(--primary-color);
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
    color: var(--primary-color);
}

.post-actions {
    margin-top: auto;
}

.read-more-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.read-more-link:hover {
    color: var(--secondary-color);
    transform: translateX(5px);
}

/* Pagination */
.archive-pagination {
    margin: 3rem 0;
    text-align: center;
}

.archive-pagination .nav-links {
    display: inline-flex;
    gap: 0.5rem;
    align-items: center;
}

.archive-pagination .page-numbers {
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

.archive-pagination .page-numbers:hover,
.archive-pagination .page-numbers.current {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
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
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    color: #6c757d;
    font-size: 2rem;
}

.no-posts-title {
    font-size: 2rem;
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

.alternative-content {
    margin: 2rem 0;
}

.alternative-content h3 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.category-links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    margin-bottom: 2rem;
}

.category-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
    border-radius: 20px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
}

.category-link:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.no-posts-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Sidebar */
.archive-sidebar .widget {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.archive-sidebar .widget-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.categories-list,
.authors-list,
.recent-posts-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-item,
.author-item,
.recent-post-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.category-item:last-child,
.author-item:last-child,
.recent-post-item:last-child {
    border-bottom: none;
}

.category-item a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #495057;
    text-decoration: none;
    transition: color 0.3s ease;
}

.category-item a:hover,
.category-item.current-category a {
    color: var(--primary-color);
}

.category-count {
    background: #f8f9fa;
    color: #6c757d;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
}

.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-link {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
    border-radius: 15px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
}

.tag-link:hover,
.tag-link.current-tag {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-1px);
}

.recent-post-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.recent-post-thumbnail {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    border-radius: 6px;
    overflow: hidden;
}

.recent-post-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.recent-post-content {
    flex: 1;
}

.recent-post-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
}

.recent-post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.recent-post-title a:hover {
    color: var(--primary-color);
}

.recent-post-meta {
    font-size: 0.8rem;
    color: #6c757d;
}

.recent-post-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.author-item {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.author-avatar {
    flex-shrink: 0;
    border-radius: 50%;
    overflow: hidden;
}

.author-name {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.author-name a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.author-name a:hover {
    color: var(--primary-color);
}

.author-posts-count {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Calendar Widget */
.calendar-widget table {
    width: 100%;
    border-collapse: collapse;
}

.calendar-widget th,
.calendar-widget td {
    text-align: center;
    padding: 0.5rem;
    border: 1px solid #f1f3f4;
}

.calendar-widget th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.calendar-widget td a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.calendar-widget td a:hover {
    color: var(--secondary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .archive-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .stat-item {
        justify-content: flex-start;
        text-align: left;
    }
    
    .stat-content {
        text-align: left;
    }
    
    .archive-filters {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-select {
        width: 100%;
        min-width: auto;
    }
    
    .posts-container.grid-layout {
        grid-template-columns: 1fr;
    }
    
    .posts-container.masonry-layout {
        column-count: 1;
    }
    
    .list-layout .post-item {
        flex-direction: column;
        gap: 0;
    }
    
    .list-layout .post-thumbnail {
        width: 100%;
    }
    
    .list-layout .post-content {
        padding: 1.5rem;
    }
    
    .no-posts-actions {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 576px) {
    .archive-header {
        padding: 2rem 0;
    }
    
    .archive-title {
        font-size: 1.5rem;
    }
    
    .post-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .post-title {
        font-size: 1.2rem;
    }
    
    .category-links {
        flex-direction: column;
        align-items: center;
    }
    
    .category-link {
        width: 100%;
        max-width: 200px;
        justify-content: center;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .post-item,
    .read-more-link,
    .category-link,
    .tag-link {
        transition: none;
    }
    
    .post-item:hover {
        transform: none;
    }
    
    .read-more-link:hover {
        transform: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .post-item,
    .archive-sidebar .widget {
        border: 2px solid #000;
    }
    
    .archive-controls {
        border: 1px solid #000;
    }
}

/* Print styles */
@media print {
    .archive-controls,
    .archive-pagination,
    .archive-sidebar {
        display: none;
    }
    
    .post-item {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    
    .post-thumbnail img {
        max-height: 200px;
        object-fit: cover;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Layout toggle functionality
    const layoutToggles = document.querySelectorAll('.layout-toggle');
    const postsContainer = document.getElementById('posts-container');
    
    layoutToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const layout = this.dataset.layout;
            
            // Update active state
            layoutToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update container layout
            if (postsContainer) {
                postsContainer.className = `posts-container ${layout}-layout`;
            }
            
            // Store preference
            localStorage.setItem('archive_layout_preference', layout);
            
            // Trigger masonry layout if needed
            if (layout === 'masonry') {
                setTimeout(initMasonryLayout, 100);
            }
        });
    });
    
    // Load saved layout preference
    const savedLayout = localStorage.getItem('archive_layout_preference');
    if (savedLayout && postsContainer) {
        const layoutToggle = document.querySelector(`[data-layout="${savedLayout}"]`);
        if (layoutToggle) {
            layoutToggle.click();
        }
    }
    
    // Filtering functionality
    const categoryFilter = document.getElementById('category-filter');
    const sortFilter = document.getElementById('sort-filter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const category = this.value;
            if (category) {
                window.location.href = `${window.location.origin}/category/${category}/`;
            } else {
                window.location.href = `${window.location.origin}/blog/`;
            }
        });
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            const sort = this.value;
            const url = new URL(window.location);
            
            switch(sort) {
                case 'date-desc':
                    url.searchParams.delete('orderby');
                    url.searchParams.delete('order');
                    break;
                case 'date-asc':
                    url.searchParams.set('orderby', 'date');
                    url.searchParams.set('order', 'asc');
                    break;
                case 'title-asc':
                    url.searchParams.set('orderby', 'title');
                    url.searchParams.set('order', 'asc');
                    break;
                case 'title-desc':
                    url.searchParams.set('orderby', 'title');
                    url.searchParams.set('order', 'desc');
                    break;
                case 'comment-count':
                    url.searchParams.set('orderby', 'comment_count');
                    url.searchParams.set('order', 'desc');
                    break;
            }
            
            window.location.href = url.toString();
        });
    }
    
    // Masonry layout initialization
    function initMasonryLayout() {
        if (postsContainer && postsContainer.classList.contains('masonry-layout')) {
            // Simple masonry implementation
            const items = postsContainer.querySelectorAll('.post-item');
            items.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
                item.style.animation = 'fadeInUp 0.6s ease forwards';
            });
        }
    }
    
    // Infinite scroll (optional)
    if (window.innerWidth > 768) {
        let loading = false;
        let currentPage = <?php echo $current_page; ?>;
        const maxPages = <?php echo $max_pages; ?>;
        
        function loadMorePosts() {
            if (loading || currentPage >= maxPages) return;
            
            loading = true;
            currentPage++;
            
            const formData = new FormData();
            formData.append('action', 'load_more_archive_posts');
            formData.append('page', currentPage);
            formData.append('nonce', '<?php echo wp_create_nonce("archive_load_more"); ?>');
            
            // Add current query parameters
            const urlParams = new URLSearchParams(window.location.search);
            for (const [key, value] of urlParams.entries()) {
                formData.append(key, value);
            }
            
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.html) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.data.html;
                    
                    const newPosts = tempDiv.querySelectorAll('.post-item');
                    newPosts.forEach(post => {
                        postsContainer.appendChild(post);
                        
                        // Animate new posts
                        post.style.opacity = '0';
                        post.style.transform = 'translateY(30px)';
                        
                        setTimeout(() => {
                            post.style.transition = 'all 0.6s ease';
                            post.style.opacity = '1';
                            post.style.transform = 'translateY(0)';
                        }, 100);
                    });
                    
                    // Re-initialize masonry if needed
                    if (postsContainer.classList.contains('masonry-layout')) {
                        initMasonryLayout();
                    }
                }
                loading = false;
            })
            .catch(error => {
                console.error('Error loading more posts:', error);
                loading = false;
            });
        }
        
        // Scroll event for infinite loading
        window.addEventListener('scroll', function() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
                loadMorePosts();
            }
        });
    }
    
    // Reading time calculation
    function calculateReadingTime() {
        const excerpts = document.querySelectorAll('.post-excerpt');
        excerpts.forEach(excerpt => {
            const text = excerpt.textContent;
            const wordCount = text.split(/\s+/).length;
            const readingTime = Math.ceil(wordCount / 200); // Average reading speed
            
            const readingTimeElement = excerpt.closest('.post-item').querySelector('.post-reading-time');
            if (readingTimeElement && !readingTimeElement.textContent.includes('min')) {
                readingTimeElement.innerHTML = `<i class="fas fa-clock"></i> ${readingTime} min read`;
            }
        });
    }
    
    calculateReadingTime();
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Post interaction tracking
    const postItems = document.querySelectorAll('.post-item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const postId = entry.target.id.replace('post-', '');
                
                // Track post view
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'post_view', {
                        'post_id': postId,
                        'content_type': 'archive'
                    });
                }
            }
        });
    }, { threshold: 0.5 });
    
    postItems.forEach(item => observer.observe(item));
    
    // Enhanced accessibility
    const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
    
    // Skip to content functionality
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab' && e.shiftKey && document.activeElement === focusableElements[0]) {
            // Focus on skip link if available
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) {
                e.preventDefault();
                skipLink.focus();
            }
        }
    });
    
    // Keyboard navigation for layout toggles
    layoutToggles.forEach(toggle => {
        toggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
    
});

// Utility function for reading time calculation
function recruitpro_reading_time() {
    // This would typically be calculated server-side
    return '<?php echo esc_html__("3 min read", "recruitpro"); ?>';
}
</script>

<?php get_footer(); ?>