<?php
/**
 * The template for displaying the blog homepage
 *
 * Professional blog homepage template for recruitment agencies featuring industry
 * insights, career advice, hiring trends, and expert commentary. Designed to
 * establish thought leadership in the recruitment industry while providing
 * valuable content for both job seekers and hiring managers.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/home.php
 * Purpose: Blog homepage template for recruitment industry content
 * Context: Main blog listing page for posts
 * Features: Featured posts, category filters, professional layout
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = get_option('posts_per_page');

// Get customizer options
$blog_title = get_theme_mod('recruitpro_blog_title', __('Industry Insights & Expert Commentary', 'recruitpro'));
$blog_subtitle = get_theme_mod('recruitpro_blog_subtitle', __('Stay ahead with the latest recruitment trends, career advice, and industry analysis from our team of experts.', 'recruitpro'));
$show_featured_section = get_theme_mod('recruitpro_show_featured_posts', true);

?>

<main id="primary" class="site-main blog-main">
    <div class="container">
        
        <!-- Blog Header -->
        <header class="blog-header">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="blog-title"><?php echo esc_html($blog_title); ?></h1>
                    <p class="blog-subtitle"><?php echo esc_html($blog_subtitle); ?></p>
                    
                    <!-- Blog Stats -->
                    <div class="blog-stats">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo esc_html(wp_count_posts()->publish); ?></span>
                                <span class="stat-label"><?php esc_html_e('Articles Published', 'recruitpro'); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo esc_html(count(get_categories(array('hide_empty' => true)))); ?></span>
                                <span class="stat-label"><?php esc_html_e('Topic Categories', 'recruitpro'); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number"><?php echo esc_html(recruitpro_get_total_reading_time()); ?></span>
                                <span class="stat-label"><?php esc_html_e('Hours of Content', 'recruitpro'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Newsletter Signup Section -->
        <section class="newsletter-section">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="newsletter-card">
                        <div class="newsletter-content">
                            <h3 class="newsletter-title">
                                <?php esc_html_e('Stay Updated with Industry Insights', 'recruitpro'); ?>
                            </h3>
                            <p class="newsletter-description">
                                <?php esc_html_e('Get weekly recruitment trends, career tips, and industry analysis delivered to your inbox.', 'recruitpro'); ?>
                            </p>
                            
                            <form class="newsletter-form" method="post" action="#" id="newsletter-signup">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="email" id="newsletter-email" name="email" 
                                               class="form-control" 
                                               placeholder="<?php esc_attr_e('Enter your professional email', 'recruitpro'); ?>" 
                                               required>
                                        <button type="submit" class="btn btn-primary">
                                            <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="newsletter-privacy">
                                    <?php 
                                    printf(
                                        esc_html__('By subscribing, you agree to our %1$sPrivacy Policy%2$s. Unsubscribe anytime.', 'recruitpro'),
                                        '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
                                        '</a>'
                                    );
                                    ?>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($show_featured_section && $paged <= 1) : ?>
        <!-- Featured Posts Section -->
        <section class="featured-posts-section">
            <div class="section-header">
                <h2 class="section-title"><?php esc_html_e('Featured Articles', 'recruitpro'); ?></h2>
                <p class="section-subtitle"><?php esc_html_e('Hand-picked insights from our recruitment experts', 'recruitpro'); ?></p>
            </div>
            
            <?php
            // Get featured posts
            $featured_posts = get_posts(array(
                'numberposts' => 3,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_featured_post',
                        'value' => '1',
                        'compare' => '='
                    )
                ),
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            // If no featured posts, get latest 3 posts
            if (empty($featured_posts)) {
                $featured_posts = get_posts(array(
                    'numberposts' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
            }

            if (!empty($featured_posts)) : ?>
                <div class="featured-posts-grid">
                    <div class="row">
                        <?php 
                        $featured_count = 0;
                        foreach ($featured_posts as $featured_post) : 
                            setup_postdata($featured_post);
                            $featured_count++;
                            $featured_class = ($featured_count === 1) ? 'featured-large' : 'featured-small';
                            $col_class = ($featured_count === 1) ? 'col-lg-8' : 'col-lg-4';
                            ?>
                            <div class="<?php echo esc_attr($col_class); ?> col-md-6">
                                <article class="featured-post <?php echo esc_attr($featured_class); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="featured-thumbnail">
                                            <a href="<?php echo esc_url(get_permalink($featured_post->ID)); ?>">
                                                <?php echo get_the_post_thumbnail($featured_post->ID, 'large', array('class' => 'img-fluid')); ?>
                                            </a>
                                            
                                            <!-- Featured Badge -->
                                            <span class="featured-badge">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                <?php esc_html_e('Featured', 'recruitpro'); ?>
                                            </span>
                                            
                                            <!-- Category -->
                                            <div class="post-category">
                                                <?php
                                                $categories = get_the_category($featured_post->ID);
                                                if (!empty($categories)) {
                                                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">';
                                                    echo esc_html($categories[0]->name);
                                                    echo '</a>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="featured-content">
                                        <header class="featured-header">
                                            <h3 class="featured-title">
                                                <a href="<?php echo esc_url(get_permalink($featured_post->ID)); ?>">
                                                    <?php echo esc_html(get_the_title($featured_post->ID)); ?>
                                                </a>
                                            </h3>
                                            
                                            <div class="featured-meta">
                                                <time datetime="<?php echo esc_attr(get_the_date('c', $featured_post->ID)); ?>">
                                                    <?php echo esc_html(get_the_date('', $featured_post->ID)); ?>
                                                </time>
                                                <span class="meta-separator">•</span>
                                                <span class="reading-time">
                                                    <?php echo esc_html(recruitpro_get_post_reading_time($featured_post->ID)); ?> 
                                                    <?php esc_html_e('min read', 'recruitpro'); ?>
                                                </span>
                                                <span class="meta-separator">•</span>
                                                <span class="author">
                                                    <?php echo esc_html(get_the_author_meta('display_name', $featured_post->post_author)); ?>
                                                </span>
                                            </div>
                                        </header>
                                        
                                        <div class="featured-excerpt">
                                            <?php
                                            $excerpt_length = ($featured_count === 1) ? 30 : 20;
                                            echo wp_trim_words(get_the_excerpt($featured_post->ID), $excerpt_length);
                                            ?>
                                        </div>
                                        
                                        <footer class="featured-footer">
                                            <a href="<?php echo esc_url(get_permalink($featured_post->ID)); ?>" class="read-more">
                                                <?php esc_html_e('Read Full Article', 'recruitpro'); ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                </svg>
                                            </a>
                                        </footer>
                                    </div>
                                </article>
                            </div>
                            <?php 
                            if ($featured_count === 1) echo '</div><div class="row">';
                        endforeach; 
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <!-- Main Content Area -->
        <div class="row">
            <div class="col-lg-8">
                
                <!-- Category Filter -->
                <div class="category-filter">
                    <div class="filter-header">
                        <h3 class="filter-title"><?php esc_html_e('Browse by Topic', 'recruitpro'); ?></h3>
                    </div>
                    
                    <div class="category-tabs">
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" 
                           class="category-tab <?php echo (!is_category()) ? 'active' : ''; ?>">
                            <?php esc_html_e('All Articles', 'recruitpro'); ?>
                        </a>
                        
                        <?php
                        $categories = get_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 6,
                            'hide_empty' => true
                        ));
                        
                        foreach ($categories as $category) {
                            $is_active = (is_category($category->term_id)) ? 'active' : '';
                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-tab ' . $is_active . '">';
                            echo esc_html($category->name);
                            echo ' <span class="category-count">(' . $category->count . ')</span>';
                            echo '</a>';
                        }
                        ?>
                        
                        <a href="<?php echo esc_url(home_url('/categories/')); ?>" class="category-tab category-all">
                            <?php esc_html_e('View All Topics', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>

                <!-- Posts Container -->
                <div class="posts-container">
                    
                    <!-- Posts Header -->
                    <div class="posts-header">
                        <div class="posts-count">
                            <?php
                            global $wp_query;
                            $total_posts = $wp_query->found_posts;
                            $start_post = (($paged - 1) * $posts_per_page) + 1;
                            $end_post = min($paged * $posts_per_page, $total_posts);
                            
                            printf(
                                esc_html__('Showing %1$s-%2$s of %3$s articles', 'recruitpro'),
                                number_format_i18n($start_post),
                                number_format_i18n($end_post),
                                number_format_i18n($total_posts)
                            );
                            ?>
                        </div>
                        
                        <div class="posts-sort">
                            <select id="posts-sort-select" class="sort-select">
                                <option value="date-desc"><?php esc_html_e('Latest First', 'recruitpro'); ?></option>
                                <option value="date-asc"><?php esc_html_e('Oldest First', 'recruitpro'); ?></option>
                                <option value="title-asc"><?php esc_html_e('Title A-Z', 'recruitpro'); ?></option>
                                <option value="popular"><?php esc_html_e('Most Popular', 'recruitpro'); ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Posts Loop -->
                    <div class="posts-list">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post-item'); ?>>
                                    
                                    <!-- Post Thumbnail -->
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                <?php
                                                the_post_thumbnail('medium', array(
                                                    'alt' => get_the_title(),
                                                    'loading' => 'lazy',
                                                ));
                                                ?>
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
                                                    foreach ($categories as $category) {
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-link">';
                                                        echo esc_html($category->name);
                                                        echo '</a>';
                                                    }
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
                                                <div class="meta-item">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                                    </svg>
                                                    <span class="author-name"><?php echo esc_html(get_the_author()); ?></span>
                                                </div>
                                                
                                                <div class="meta-item">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                                                    </svg>
                                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                        <?php echo esc_html(get_the_date()); ?>
                                                    </time>
                                                </div>
                                                
                                                <div class="meta-item">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    <span class="reading-time">
                                                        <?php echo esc_html(recruitpro_get_post_reading_time()); ?> 
                                                        <?php esc_html_e('min read', 'recruitpro'); ?>
                                                    </span>
                                                </div>
                                                
                                                <?php if (get_comments_number() > 0) : ?>
                                                    <div class="meta-item">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M21.99 4c0-1.1-.89-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.89 2 2 2h14l4 4-.01-18z"/>
                                                        </svg>
                                                        <a href="<?php comments_link(); ?>" class="comments-link">
                                                            <?php
                                                            printf(
                                                                esc_html(_n('%s comment', '%s comments', get_comments_number(), 'recruitpro')),
                                                                number_format_i18n(get_comments_number())
                                                            );
                                                            ?>
                                                        </a>
                                                    </div>
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
                                                    foreach (array_slice($tags, 0, 3) as $tag) {
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
                            <?php endwhile; ?>

                            <!-- Pagination -->
                            <nav class="blog-pagination" aria-label="<?php esc_attr_e('Blog pagination', 'recruitpro'); ?>">
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
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" class="no-posts-icon">
                                        <path d="M20 6h-2.5l-1.4-1.4C15.9 4.3 15.6 4 15.3 4H8.7c-.3 0-.6.3-.8.6L6.5 6H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                    
                                    <h2 class="no-posts-title">
                                        <?php esc_html_e('No Articles Found', 'recruitpro'); ?>
                                    </h2>
                                    
                                    <p class="no-posts-description">
                                        <?php esc_html_e('We haven\'t published any articles yet, but we\'re working on creating valuable content for recruitment professionals and job seekers.', 'recruitpro'); ?>
                                    </p>
                                    
                                    <div class="no-posts-actions">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                                            <?php esc_html_e('Back to Homepage', 'recruitpro'); ?>
                                        </a>
                                        
                                        <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline">
                                            <?php esc_html_e('Browse Jobs', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="col-lg-4">
                <div class="blog-sidebar">
                    
                    <!-- Search Widget -->
                    <div class="widget widget-search">
                        <h3 class="widget-title"><?php esc_html_e('Search Articles', 'recruitpro'); ?></h3>
                        <form class="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="search-input-group">
                                <input type="search" name="s" class="search-field" 
                                       placeholder="<?php esc_attr_e('Search recruitment insights...', 'recruitpro'); ?>"
                                       value="<?php echo get_search_query(); ?>">
                                <button type="submit" class="search-submit">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Popular Posts Widget -->
                    <div class="widget widget-popular-posts">
                        <h3 class="widget-title"><?php esc_html_e('Most Popular', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_popular_posts_widget(); ?>
                    </div>

                    <!-- Categories Widget -->
                    <div class="widget widget-categories">
                        <h3 class="widget-title"><?php esc_html_e('Topics', 'recruitpro'); ?></h3>
                        <ul class="category-list">
                            <?php
                            $categories = get_categories(array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'hide_empty' => true
                            ));
                            
                            foreach ($categories as $category) {
                                echo '<li class="category-item">';
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-link">';
                                echo esc_html($category->name);
                                echo '<span class="post-count">(' . $category->count . ')</span>';
                                echo '</a>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Recent Comments Widget -->
                    <div class="widget widget-recent-comments">
                        <h3 class="widget-title"><?php esc_html_e('Recent Discussions', 'recruitpro'); ?></h3>
                        <?php echo recruitpro_get_recent_comments_widget(); ?>
                    </div>

                    <!-- Archives Widget -->
                    <div class="widget widget-archives">
                        <h3 class="widget-title"><?php esc_html_e('Archives', 'recruitpro'); ?></h3>
                        <ul class="archive-list">
                            <?php wp_get_archives(array('type' => 'monthly', 'show_post_count' => true)); ?>
                        </ul>
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
 * Helper Functions for Blog Homepage
 */

/**
 * Get total reading time for all posts
 */
function recruitpro_get_total_reading_time() {
    $total_words = 0;
    $posts = get_posts(array(
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    ));
    
    foreach ($posts as $post_id) {
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $total_words += $word_count;
    }
    
    $total_hours = ceil(($total_words / 200) / 60); // 200 words per minute
    return max(1, $total_hours);
}

/**
 * Get reading time for individual post
 */
function recruitpro_get_post_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time);
}

/**
 * Get popular posts widget content
 */
function recruitpro_get_popular_posts_widget() {
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
        return '<p>' . esc_html__('No popular posts available yet.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="popular-posts-list">';
    foreach ($popular_posts as $post) {
        $output .= sprintf(
            '<li class="popular-post-item">
                <a href="%s" class="popular-post-link">
                    <h4 class="popular-post-title">%s</h4>
                    <span class="popular-post-date">%s</span>
                </a>
            </li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID)),
            esc_html(get_the_date('', $post->ID))
        );
    }
    $output .= '</ul>';
    
    return $output;
}

/**
 * Get recent comments widget content
 */
function recruitpro_get_recent_comments_widget() {
    $recent_comments = get_comments(array(
        'number' => 5,
        'status' => 'approve',
        'type' => 'comment'
    ));
    
    if (empty($recent_comments)) {
        return '<p>' . esc_html__('No recent comments yet.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="recent-comments-list">';
    foreach ($recent_comments as $comment) {
        $output .= sprintf(
            '<li class="recent-comment-item">
                <div class="comment-author">%s</div>
                <a href="%s" class="comment-link">%s</a>
                <span class="comment-date">%s</span>
            </li>',
            esc_html($comment->comment_author),
            esc_url(get_comment_link($comment->comment_ID)),
            esc_html(get_the_title($comment->comment_post_ID)),
            esc_html(get_comment_date('', $comment->comment_ID))
        );
    }
    $output .= '</ul>';
    
    return $output;
}
?>