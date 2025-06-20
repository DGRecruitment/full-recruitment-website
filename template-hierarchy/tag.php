<?php
/**
 * The template for displaying tag archives
 *
 * Professional tag archive template for recruitment agencies featuring
 * tag-based content organization, industry insights categorization, and
 * professional presentation of recruitment-related topics. Designed to
 * showcase expertise in specific recruitment areas and help visitors
 * discover relevant content efficiently.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/tag.php
 * Purpose: Tag archive template for recruitment industry content
 * Context: Tag-based content organization and display
 * Features: Professional presentation, related tags, industry focus
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get current tag and archive information
$current_tag = get_queried_object();
$tag_name = $current_tag->name;
$tag_description = $current_tag->description;
$tag_slug = $current_tag->slug;
$tag_id = $current_tag->term_id;

// Get pagination info
$current_page = get_query_var('paged') ? get_query_var('paged') : 1;
$total_posts = $GLOBALS['wp_query']->found_posts;
$posts_per_page = get_option('posts_per_page');

?>

<main id="primary" class="site-main tag-archive-main">
    <div class="container">
        
        <!-- Tag Archive Header -->
        <header class="archive-header tag-archive-header">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="archive-header-content">
                        
                        <!-- Breadcrumbs -->
                        <?php if (function_exists('recruitpro_breadcrumbs') && get_theme_mod('recruitpro_show_breadcrumbs', true)) : ?>
                            <nav class="breadcrumbs-nav" aria-label="<?php esc_attr_e('Breadcrumb navigation', 'recruitpro'); ?>">
                                <?php recruitpro_breadcrumbs(); ?>
                            </nav>
                        <?php endif; ?>
                        
                        <!-- Archive Type Badge -->
                        <div class="archive-type-badge">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                            </svg>
                            <span><?php esc_html_e('Topic', 'recruitpro'); ?></span>
                        </div>
                        
                        <!-- Tag Title -->
                        <h1 class="archive-title tag-title">
                            <?php 
                            printf(
                                esc_html__('Articles tagged: %s', 'recruitpro'),
                                '<span class="tag-name">' . esc_html($tag_name) . '</span>'
                            );
                            ?>
                        </h1>
                        
                        <!-- Tag Description -->
                        <?php if (!empty($tag_description)) : ?>
                            <div class="archive-description tag-description">
                                <?php echo wp_kses_post($tag_description); ?>
                            </div>
                        <?php else : ?>
                            <div class="archive-description tag-description">
                                <p>
                                    <?php 
                                    printf(
                                        esc_html__('Explore our collection of articles and insights related to %s in the recruitment industry.', 'recruitpro'),
                                        '<strong>' . esc_html($tag_name) . '</strong>'
                                    );
                                    ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Archive Statistics -->
                        <div class="archive-stats">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html(number_format_i18n($total_posts)); ?></span>
                                    <span class="stat-label">
                                        <?php echo esc_html(_n('Article', 'Articles', $total_posts, 'recruitpro')); ?>
                                    </span>
                                </div>
                                
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html(recruitpro_get_tag_reading_time()); ?></span>
                                    <span class="stat-label"><?php esc_html_e('Min Read', 'recruitpro'); ?></span>
                                </div>
                                
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html(recruitpro_get_tag_popularity_score()); ?></span>
                                    <span class="stat-label"><?php esc_html_e('Popularity', 'recruitpro'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Archive Content -->
        <div class="row">
            <div class="col-lg-8">
                
                <!-- Archive Controls -->
                <div class="archive-controls">
                    <div class="archive-controls-left">
                        <!-- Content Summary -->
                        <div class="content-summary">
                            <?php
                            $start_post = (($current_page - 1) * $posts_per_page) + 1;
                            $end_post = min($current_page * $posts_per_page, $total_posts);
                            
                            printf(
                                esc_html__('Showing %1$s-%2$s of %3$s articles', 'recruitpro'),
                                number_format_i18n($start_post),
                                number_format_i18n($end_post),
                                number_format_i18n($total_posts)
                            );
                            ?>
                        </div>
                    </div>
                    
                    <div class="archive-controls-right">
                        <!-- Sort Options -->
                        <div class="sort-options">
                            <select id="tag-sort-select" class="sort-select">
                                <option value="date-desc"><?php esc_html_e('Latest First', 'recruitpro'); ?></option>
                                <option value="date-asc"><?php esc_html_e('Oldest First', 'recruitpro'); ?></option>
                                <option value="title-asc"><?php esc_html_e('Title A-Z', 'recruitpro'); ?></option>
                                <option value="popular"><?php esc_html_e('Most Popular', 'recruitpro'); ?></option>
                                <option value="comments"><?php esc_html_e('Most Discussed', 'recruitpro'); ?></option>
                            </select>
                        </div>
                        
                        <!-- View Toggle -->
                        <div class="view-toggle">
                            <button type="button" class="view-toggle-btn active" data-view="list" 
                                    aria-label="<?php esc_attr_e('List view', 'recruitpro'); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                                </svg>
                            </button>
                            <button type="button" class="view-toggle-btn" data-view="grid"
                                    aria-label="<?php esc_attr_e('Grid view', 'recruitpro'); ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM10 13H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1zM20 3h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM20 13h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Posts Container -->
                <div class="posts-container" data-view="list">
                    
                    <?php if (have_posts()) : ?>
                        
                        <!-- Featured Post (First Post) -->
                        <?php if ($current_page === 1 && have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('featured-tag-post'); ?>>
                                
                                <!-- Featured Badge -->
                                <div class="featured-badge">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <?php esc_html_e('Featured', 'recruitpro'); ?>
                                </div>
                                
                                <!-- Post Thumbnail -->
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail featured-thumbnail">
                                        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                            <?php the_post_thumbnail('large', array('loading' => 'lazy')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Post Content -->
                                <div class="post-content">
                                    <header class="entry-header">
                                        <!-- Post Categories -->
                                        <div class="post-categories">
                                            <?php
                                            $categories = get_the_category();
                                            if (!empty($categories)) {
                                                foreach (array_slice($categories, 0, 2) as $category) {
                                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-link">';
                                                    echo esc_html($category->name);
                                                    echo '</a>';
                                                }
                                            }
                                            ?>
                                        </div>

                                        <!-- Post Title -->
                                        <h2 class="entry-title featured-title">
                                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>

                                        <!-- Post Meta -->
                                        <div class="entry-meta">
                                            <div class="meta-item">
                                                <?php echo get_avatar(get_the_author_meta('ID'), 24); ?>
                                                <span class="author-name"><?php echo esc_html(get_the_author()); ?></span>
                                            </div>
                                            
                                            <div class="meta-item">
                                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                    <?php echo esc_html(get_the_date()); ?>
                                                </time>
                                            </div>
                                            
                                            <div class="meta-item">
                                                <span class="reading-time">
                                                    <?php echo esc_html(recruitpro_get_post_reading_time()); ?> 
                                                    <?php esc_html_e('min read', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </header>

                                    <!-- Post Excerpt -->
                                    <div class="entry-summary">
                                        <?php
                                        if (has_excerpt()) {
                                            the_excerpt();
                                        } else {
                                            echo '<p>' . wp_trim_words(get_the_content(), 30, '...') . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Post Footer -->
                                    <footer class="entry-footer">
                                        <a href="<?php the_permalink(); ?>" class="read-more-link featured-read-more">
                                            <?php esc_html_e('Read Full Article', 'recruitpro'); ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                            </svg>
                                        </a>
                                    </footer>
                                </div>
                            </article>
                        <?php endif; ?>

                        <!-- Regular Posts -->
                        <div class="tag-posts-list">
                            <?php while (have_posts()) : the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('tag-post-item'); ?>>
                                    
                                    <!-- Post Thumbnail -->
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Post Content -->
                                    <div class="post-content">
                                        <header class="entry-header">
                                            <!-- Post Categories -->
                                            <div class="post-categories">
                                                <?php
                                                $categories = get_the_category();
                                                if (!empty($categories)) {
                                                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="category-link">';
                                                    echo esc_html($categories[0]->name);
                                                    echo '</a>';
                                                }
                                                ?>
                                            </div>

                                            <!-- Post Title -->
                                            <h3 class="entry-title">
                                                <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h3>

                                            <!-- Post Meta -->
                                            <div class="entry-meta">
                                                <div class="meta-left">
                                                    <div class="meta-item">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                                        </svg>
                                                        <span class="author-name"><?php echo esc_html(get_the_author()); ?></span>
                                                    </div>
                                                    
                                                    <div class="meta-item">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                                                        </svg>
                                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                            <?php echo esc_html(get_the_date()); ?>
                                                        </time>
                                                    </div>
                                                </div>
                                                
                                                <div class="meta-right">
                                                    <div class="meta-item">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                        <span class="reading-time">
                                                            <?php echo esc_html(recruitpro_get_post_reading_time()); ?> 
                                                            <?php esc_html_e('min', 'recruitpro'); ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <?php if (get_comments_number() > 0) : ?>
                                                        <div class="meta-item">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M21.99 4c0-1.1-.89-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.89 2 2 2h14l4 4-.01-18z"/>
                                                            </svg>
                                                            <a href="<?php comments_link(); ?>" class="comments-link">
                                                                <?php echo esc_html(get_comments_number()); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
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
                                                $post_tags = get_the_tags();
                                                if (!empty($post_tags)) {
                                                    foreach (array_slice($post_tags, 0, 3) as $tag) {
                                                        if ($tag->term_id !== $tag_id) { // Don't show current tag
                                                            echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-link">';
                                                            echo esc_html($tag->name);
                                                            echo '</a>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            
                                            <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                <?php esc_html_e('Continue Reading', 'recruitpro'); ?>
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                </svg>
                                            </a>
                                        </footer>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>

                        <!-- Pagination -->
                        <nav class="archive-pagination" aria-label="<?php esc_attr_e('Tag archive pagination', 'recruitpro'); ?>">
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
                                'add_args'  => false,
                                'add_fragment' => '',
                            ));
                            ?>
                        </nav>

                    <?php else : ?>
                        
                        <!-- No Posts Found -->
                        <div class="no-posts-found">
                            <div class="no-posts-content">
                                <div class="no-posts-icon">
                                    <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                                    </svg>
                                </div>
                                
                                <h2 class="no-posts-title">
                                    <?php 
                                    printf(
                                        esc_html__('No articles found for "%s"', 'recruitpro'),
                                        esc_html($tag_name)
                                    );
                                    ?>
                                </h2>
                                
                                <p class="no-posts-description">
                                    <?php 
                                    printf(
                                        esc_html__('We haven\'t published any content with the "%s" tag yet. Explore our other recruitment insights and resources below.', 'recruitpro'),
                                        esc_html($tag_name)
                                    );
                                    ?>
                                </p>
                                
                                <div class="no-posts-actions">
                                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-primary">
                                        <?php esc_html_e('Browse All Articles', 'recruitpro'); ?>
                                    </a>
                                    
                                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline">
                                        <?php esc_html_e('View Job Opportunities', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Suggested Tags -->
                            <div class="suggested-tags">
                                <h3><?php esc_html_e('Related Topics', 'recruitpro'); ?></h3>
                                <?php echo recruitpro_get_related_tags(); ?>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="col-lg-4">
                <div class="tag-archive-sidebar">
                    
                    <!-- Current Tag Info Widget -->
                    <div class="widget widget-current-tag">
                        <h3 class="widget-title"><?php esc_html_e('About This Topic', 'recruitpro'); ?></h3>
                        <div class="widget-content">
                            <div class="current-tag-info">
                                <div class="tag-icon">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                                    </svg>
                                </div>
                                
                                <h4 class="tag-name"><?php echo esc_html($tag_name); ?></h4>
                                
                                <?php if (!empty($tag_description)) : ?>
                                    <p class="tag-description"><?php echo esc_html($tag_description); ?></p>
                                <?php endif; ?>
                                
                                <div class="tag-stats">
                                    <div class="tag-stat">
                                        <strong><?php echo esc_html(number_format_i18n($total_posts)); ?></strong>
                                        <span><?php echo esc_html(_n('Article', 'Articles', $total_posts, 'recruitpro')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Tags Widget -->
                    <div class="widget widget-related-tags">
                        <h3 class="widget-title"><?php esc_html_e('Related Topics', 'recruitpro'); ?></h3>
                        <div class="widget-content">
                            <?php echo recruitpro_get_related_tags_widget(); ?>
                        </div>
                    </div>

                    <!-- Popular Tags Widget -->
                    <div class="widget widget-popular-tags">
                        <h3 class="widget-title"><?php esc_html_e('Popular Topics', 'recruitpro'); ?></h3>
                        <div class="widget-content">
                            <?php echo recruitpro_get_popular_tags_widget(); ?>
                        </div>
                    </div>

                    <!-- Tag Cloud Widget -->
                    <div class="widget widget-tag-cloud">
                        <h3 class="widget-title"><?php esc_html_e('Explore All Topics', 'recruitpro'); ?></h3>
                        <div class="widget-content">
                            <?php
                            wp_tag_cloud(array(
                                'smallest' => 12,
                                'largest' => 18,
                                'unit' => 'px',
                                'number' => 30,
                                'format' => 'flat',
                                'separator' => ' ',
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'show_count' => false,
                                'echo' => true
                            ));
                            ?>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="widget widget-newsletter-tag">
                        <h3 class="widget-title"><?php esc_html_e('Stay Updated', 'recruitpro'); ?></h3>
                        <div class="widget-content">
                            <p>
                                <?php 
                                printf(
                                    esc_html__('Get the latest articles about %s and other recruitment topics delivered to your inbox.', 'recruitpro'),
                                    '<strong>' . esc_html($tag_name) . '</strong>'
                                );
                                ?>
                            </p>
                            <form class="newsletter-form-tag">
                                <input type="email" placeholder="<?php esc_attr_e('Your email address', 'recruitpro'); ?>" required>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                </button>
                            </form>
                        </div>
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
 * Helper Functions for Tag Archive Template
 */

/**
 * Get total reading time for all posts in tag
 */
function recruitpro_get_tag_reading_time() {
    global $wp_query;
    $total_words = 0;
    
    // Get all posts in current tag
    $tag_posts = get_posts(array(
        'tag' => get_queried_object()->slug,
        'numberposts' => -1,
        'fields' => 'ids'
    ));
    
    foreach ($tag_posts as $post_id) {
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $total_words += $word_count;
    }
    
    $total_minutes = ceil($total_words / 200); // 200 words per minute
    return max(1, $total_minutes);
}

/**
 * Get tag popularity score
 */
function recruitpro_get_tag_popularity_score() {
    $current_tag = get_queried_object();
    $post_count = $current_tag->count;
    
    // Simple popularity calculation based on post count
    if ($post_count >= 50) {
        return esc_html__('High', 'recruitpro');
    } elseif ($post_count >= 20) {
        return esc_html__('Medium', 'recruitpro');
    } elseif ($post_count >= 5) {
        return esc_html__('Growing', 'recruitpro');
    } else {
        return esc_html__('New', 'recruitpro');
    }
}

/**
 * Get reading time for individual post
 */
function recruitpro_get_post_reading_time() {
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time);
}

/**
 * Get related tags
 */
function recruitpro_get_related_tags() {
    $current_tag = get_queried_object();
    
    // Get tags that appear in posts with the current tag
    $related_tags = get_tags(array(
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 8,
        'exclude' => array($current_tag->term_id),
        'hide_empty' => true
    ));
    
    if (empty($related_tags)) {
        return '<p>' . esc_html__('No related topics available.', 'recruitpro') . '</p>';
    }
    
    $output = '<div class="related-tags-grid">';
    foreach ($related_tags as $tag) {
        $output .= sprintf(
            '<a href="%s" class="related-tag-link">
                <span class="tag-name">%s</span>
                <span class="tag-count">%s %s</span>
            </a>',
            esc_url(get_tag_link($tag->term_id)),
            esc_html($tag->name),
            esc_html($tag->count),
            esc_html(_n('post', 'posts', $tag->count, 'recruitpro'))
        );
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Get related tags widget
 */
function recruitpro_get_related_tags_widget() {
    $current_tag = get_queried_object();
    
    // Get tags from same posts
    $posts_with_tag = get_posts(array(
        'tag' => $current_tag->slug,
        'numberposts' => -1,
        'fields' => 'ids'
    ));
    
    if (empty($posts_with_tag)) {
        return '<p>' . esc_html__('No related topics found.', 'recruitpro') . '</p>';
    }
    
    $all_tags = array();
    foreach ($posts_with_tag as $post_id) {
        $post_tags = get_the_tags($post_id);
        if (!empty($post_tags)) {
            foreach ($post_tags as $tag) {
                if ($tag->term_id !== $current_tag->term_id) {
                    if (!isset($all_tags[$tag->term_id])) {
                        $all_tags[$tag->term_id] = $tag;
                        $all_tags[$tag->term_id]->related_count = 0;
                    }
                    $all_tags[$tag->term_id]->related_count++;
                }
            }
        }
    }
    
    // Sort by related count
    usort($all_tags, function($a, $b) {
        return $b->related_count - $a->related_count;
    });
    
    $output = '<ul class="related-tags-list">';
    foreach (array_slice($all_tags, 0, 6) as $tag) {
        $output .= sprintf(
            '<li class="related-tag-item">
                <a href="%s" class="related-tag-link">
                    <span class="tag-name">%s</span>
                    <span class="tag-relation">%s %s</span>
                </a>
            </li>',
            esc_url(get_tag_link($tag->term_id)),
            esc_html($tag->name),
            esc_html($tag->related_count),
            esc_html(_n('related', 'related', $tag->related_count, 'recruitpro'))
        );
    }
    $output .= '</ul>';
    
    return $output;
}

/**
 * Get popular tags widget
 */
function recruitpro_get_popular_tags_widget() {
    $popular_tags = get_tags(array(
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 8,
        'hide_empty' => true
    ));
    
    if (empty($popular_tags)) {
        return '<p>' . esc_html__('No popular topics available.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="popular-tags-list">';
    foreach ($popular_tags as $tag) {
        $is_current = (get_queried_object()->term_id === $tag->term_id);
        $current_class = $is_current ? ' current-tag' : '';
        
        $output .= sprintf(
            '<li class="popular-tag-item%s">
                <a href="%s" class="popular-tag-link">
                    <span class="tag-name">%s</span>
                    <span class="tag-count">%s</span>
                </a>
            </li>',
            $current_class,
            esc_url(get_tag_link($tag->term_id)),
            esc_html($tag->name),
            esc_html(number_format_i18n($tag->count))
        );
    }
    $output .= '</ul>';
    
    return $output;
}
?>