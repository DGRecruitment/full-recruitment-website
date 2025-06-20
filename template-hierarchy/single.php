<?php
/**
 * The template for displaying all single posts
 *
 * Professional single post template for recruitment agencies featuring
 * comprehensive article presentation, author information, social sharing,
 * and industry-specific content enhancement. Designed for recruitment
 * insights, career advice, industry analysis, and thought leadership content.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/single.php
 * Purpose: Single post template for recruitment industry content
 * Context: Individual blog posts and articles
 * Features: Professional presentation, social sharing, author bio, related posts
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get post settings and layout options
$post_layout = recruitpro_get_single_post_layout();
$show_author_bio = get_theme_mod('recruitpro_show_author_bio', true);
$show_social_sharing = get_theme_mod('recruitpro_show_social_sharing', true);
$show_related_posts = get_theme_mod('recruitpro_show_related_posts', true);
$show_post_navigation = get_theme_mod('recruitpro_show_post_navigation', true);

?>

<main id="primary" class="site-main single-main">
    <div class="container">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <div class="row">
                <div class="<?php echo esc_attr($post_layout['content_class']); ?>">
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>
                        
                        <!-- Post Header -->
                        <header class="entry-header single-post-header">
                            
                            <!-- Breadcrumbs -->
                            <?php if (function_exists('recruitpro_breadcrumbs') && get_theme_mod('recruitpro_show_breadcrumbs', true)) : ?>
                                <nav class="breadcrumbs-nav" aria-label="<?php esc_attr_e('Breadcrumb navigation', 'recruitpro'); ?>">
                                    <?php recruitpro_breadcrumbs(); ?>
                                </nav>
                            <?php endif; ?>
                            
                            <!-- Post Categories -->
                            <div class="entry-categories">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    foreach ($categories as $category) {
                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-badge">';
                                        echo esc_html($category->name);
                                        echo '</a>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <!-- Post Title -->
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                            
                            <!-- Post Subtitle -->
                            <?php 
                            $post_subtitle = get_post_meta(get_the_ID(), '_post_subtitle', true);
                            if (!empty($post_subtitle)) : ?>
                                <p class="entry-subtitle"><?php echo esc_html($post_subtitle); ?></p>
                            <?php endif; ?>
                            
                            <!-- Post Meta -->
                            <div class="entry-meta">
                                <div class="meta-items">
                                    
                                    <!-- Author Information -->
                                    <div class="meta-item meta-author">
                                        <div class="author-avatar">
                                            <?php echo get_avatar(get_the_author_meta('ID'), 32, '', get_the_author_meta('display_name')); ?>
                                        </div>
                                        <div class="author-details">
                                            <span class="author-label"><?php esc_html_e('By', 'recruitpro'); ?></span>
                                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-name">
                                                <?php echo esc_html(get_the_author_meta('display_name')); ?>
                                            </a>
                                            
                                            <!-- Author Title -->
                                            <?php 
                                            $author_title = get_the_author_meta('job_title');
                                            if (!empty($author_title)) : ?>
                                                <span class="author-title"><?php echo esc_html($author_title); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Publication Date -->
                                    <div class="meta-item meta-date">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                                        </svg>
                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="published">
                                            <?php echo esc_html(get_the_date()); ?>
                                        </time>
                                        
                                        <!-- Updated Date -->
                                        <?php if (get_the_date() !== get_the_modified_date()) : ?>
                                            <span class="updated-label"><?php esc_html_e('Updated:', 'recruitpro'); ?></span>
                                            <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>" class="updated">
                                                <?php echo esc_html(get_the_modified_date()); ?>
                                            </time>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Reading Time -->
                                    <div class="meta-item meta-reading-time">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                            <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                        </svg>
                                        <span class="reading-time">
                                            <?php echo esc_html(recruitpro_get_reading_time()); ?> 
                                            <?php esc_html_e('min read', 'recruitpro'); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Comments Count -->
                                    <?php if (comments_open() || get_comments_number()) : ?>
                                        <div class="meta-item meta-comments">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21.99 4c0-1.1-.89-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.89 2 2 2h14l4 4-.01-18z"/>
                                            </svg>
                                            <a href="#comments" class="comments-link">
                                                <?php
                                                printf(
                                                    esc_html(_n('%s Comment', '%s Comments', get_comments_number(), 'recruitpro')),
                                                    number_format_i18n(get_comments_number())
                                                );
                                                ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- View Count -->
                                    <?php 
                                    $view_count = get_post_meta(get_the_ID(), 'post_views_count', true);
                                    if (!empty($view_count)) : ?>
                                        <div class="meta-item meta-views">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                            </svg>
                                            <span class="view-count">
                                                <?php echo esc_html(number_format_i18n($view_count)); ?> 
                                                <?php esc_html_e('views', 'recruitpro'); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Social Sharing (Top) -->
                                <?php if ($show_social_sharing) : ?>
                                    <div class="entry-sharing entry-sharing-top">
                                        <span class="sharing-label"><?php esc_html_e('Share:', 'recruitpro'); ?></span>
                                        <?php echo recruitpro_get_social_sharing_buttons(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="entry-featured-image">
                                <?php
                                the_post_thumbnail('large', array(
                                    'alt' => get_the_title(),
                                    'class' => 'img-fluid featured-image',
                                    'loading' => 'eager', // First image should load immediately
                                ));
                                ?>
                                
                                <!-- Image Caption -->
                                <?php 
                                $caption = get_the_post_thumbnail_caption();
                                if (!empty($caption)) : ?>
                                    <figcaption class="featured-image-caption">
                                        <?php echo esc_html($caption); ?>
                                    </figcaption>
                                <?php endif; ?>
                                
                                <!-- Image Credit -->
                                <?php 
                                $image_credit = get_post_meta(get_the_ID(), '_featured_image_credit', true);
                                if (!empty($image_credit)) : ?>
                                    <div class="image-credit">
                                        <small><?php echo esc_html($image_credit); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Post Content -->
                        <div class="entry-content">
                            
                            <!-- Content Introduction -->
                            <?php 
                            $post_intro = get_post_meta(get_the_ID(), '_post_introduction', true);
                            if (!empty($post_intro)) : ?>
                                <div class="entry-introduction">
                                    <?php echo wp_kses_post($post_intro); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Table of Contents -->
                            <?php if (get_post_meta(get_the_ID(), '_show_table_of_contents', true) === '1') : ?>
                                <div class="table-of-contents">
                                    <h3 class="toc-title"><?php esc_html_e('Table of Contents', 'recruitpro'); ?></h3>
                                    <?php echo recruitpro_generate_table_of_contents(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Main Content -->
                            <div class="content-body">
                                <?php
                                the_content(sprintf(
                                    wp_kses(
                                        __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'recruitpro'),
                                        array(
                                            'span' => array(
                                                'class' => array(),
                                            ),
                                        )
                                    ),
                                    wp_kses_post(get_the_title())
                                ));

                                // Handle page breaks
                                wp_link_pages(array(
                                    'before'      => '<nav class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'recruitpro') . '</span>',
                                    'after'       => '</nav>',
                                    'link_before' => '<span class="page-number">',
                                    'link_after'  => '</span>',
                                    'pagelink'    => '<span class="screen-reader-text">' . esc_html__('Page', 'recruitpro') . ' </span>%',
                                    'separator'   => '<span class="screen-reader-text">, </span>',
                                ));
                                ?>
                            </div>
                            
                            <!-- Content Summary -->
                            <?php 
                            $post_summary = get_post_meta(get_the_ID(), '_post_summary', true);
                            if (!empty($post_summary)) : ?>
                                <div class="entry-summary-box">
                                    <h3 class="summary-title"><?php esc_html_e('Key Takeaways', 'recruitpro'); ?></h3>
                                    <div class="summary-content">
                                        <?php echo wp_kses_post($post_summary); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Post Footer -->
                        <footer class="entry-footer">
                            
                            <!-- Post Tags -->
                            <?php
                            $tags = get_the_tags();
                            if (!empty($tags)) : ?>
                                <div class="entry-tags">
                                    <h4 class="tags-title">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
                                        </svg>
                                        <?php esc_html_e('Tags:', 'recruitpro'); ?>
                                    </h4>
                                    <div class="tags-list">
                                        <?php
                                        foreach ($tags as $tag) {
                                            echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-link" rel="tag">';
                                            echo esc_html($tag->name);
                                            echo '</a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Social Sharing (Bottom) -->
                            <?php if ($show_social_sharing) : ?>
                                <div class="entry-sharing entry-sharing-bottom">
                                    <h4 class="sharing-title">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
                                        </svg>
                                        <?php esc_html_e('Share this article:', 'recruitpro'); ?>
                                    </h4>
                                    <div class="sharing-buttons">
                                        <?php echo recruitpro_get_social_sharing_buttons(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Post Meta Footer -->
                            <div class="entry-meta-footer">
                                <div class="meta-footer-left">
                                    <!-- Last Updated -->
                                    <small class="last-updated">
                                        <?php
                                        printf(
                                            esc_html__('Last updated: %s', 'recruitpro'),
                                            '<time datetime="' . esc_attr(get_the_modified_date('c')) . '">' . 
                                            esc_html(get_the_modified_date()) . '</time>'
                                        );
                                        ?>
                                    </small>
                                </div>
                                
                                <div class="meta-footer-right">
                                    <!-- Edit Link -->
                                    <?php
                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
                                                __('Edit <span class="screen-reader-text">%s</span>', 'recruitpro'),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            wp_kses_post(get_the_title())
                                        ),
                                        '<span class="edit-link">',
                                        '</span>'
                                    );
                                    ?>
                                </div>
                            </div>
                        </footer>
                    </article>

                    <!-- Author Bio -->
                    <?php if ($show_author_bio) : ?>
                        <div class="author-bio-section">
                            <?php echo recruitpro_get_author_bio(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Navigation -->
                    <?php if ($show_post_navigation) : ?>
                        <nav class="post-navigation" aria-label="<?php esc_attr_e('Post navigation', 'recruitpro'); ?>">
                            <div class="nav-links">
                                <?php
                                $prev_post = get_previous_post();
                                $next_post = get_next_post();
                                
                                if ($prev_post || $next_post) : ?>
                                    
                                    <?php if ($prev_post) : ?>
                                        <div class="nav-previous">
                                            <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" rel="prev">
                                                <div class="nav-direction">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                                                    </svg>
                                                    <span><?php esc_html_e('Previous Article', 'recruitpro'); ?></span>
                                                </div>
                                                <h4 class="nav-title"><?php echo esc_html(get_the_title($prev_post)); ?></h4>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($next_post) : ?>
                                        <div class="nav-next">
                                            <a href="<?php echo esc_url(get_permalink($next_post)); ?>" rel="next">
                                                <div class="nav-direction">
                                                    <span><?php esc_html_e('Next Article', 'recruitpro'); ?></span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                    </svg>
                                                </div>
                                                <h4 class="nav-title"><?php echo esc_html(get_the_title($next_post)); ?></h4>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                <?php endif; ?>
                            </div>
                        </nav>
                    <?php endif; ?>

                    <!-- Related Posts -->
                    <?php if ($show_related_posts) : ?>
                        <div class="related-posts-section">
                            <?php echo recruitpro_get_related_posts(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Comments -->
                    <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if (comments_open() || get_comments_number()) {
                        comments_template();
                    }
                    ?>
                </div>

                <!-- Sidebar -->
                <?php if ($post_layout['show_sidebar']) : ?>
                    <aside class="<?php echo esc_attr($post_layout['sidebar_class']); ?>">
                        <div class="single-post-sidebar">
                            
                            <!-- Table of Contents Sidebar -->
                            <?php if (get_post_meta(get_the_ID(), '_show_sidebar_toc', true) === '1') : ?>
                                <div class="widget widget-table-of-contents">
                                    <h3 class="widget-title"><?php esc_html_e('In This Article', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <?php echo recruitpro_generate_table_of_contents(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Newsletter Signup -->
                            <div class="widget widget-newsletter-single">
                                <h3 class="widget-title"><?php esc_html_e('Never Miss an Update', 'recruitpro'); ?></h3>
                                <div class="widget-content">
                                    <p><?php esc_html_e('Get the latest recruitment insights and career advice delivered to your inbox.', 'recruitpro'); ?></p>
                                    <form class="newsletter-form-sidebar">
                                        <input type="email" placeholder="<?php esc_attr_e('Your email address', 'recruitpro'); ?>" required>
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                                            <?php esc_html_e('Subscribe Now', 'recruitpro'); ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Regular Sidebar -->
                            <?php get_sidebar(); ?>
                        </div>
                    </aside>
                <?php endif; ?>
            </div>
            
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();

/**
 * Helper Functions for Single Post Template
 */

/**
 * Get single post layout configuration
 */
function recruitpro_get_single_post_layout() {
    $post_id = get_the_ID();
    
    // Check for custom layout setting
    $hide_sidebar = get_post_meta($post_id, '_hide_sidebar', true);
    $show_sidebar = ($hide_sidebar !== '1');
    
    // Check theme customizer setting
    if ($show_sidebar) {
        $show_sidebar = get_theme_mod('recruitpro_single_show_sidebar', true);
    }
    
    return array(
        'show_sidebar' => $show_sidebar,
        'content_class' => $show_sidebar ? 'col-lg-8 col-md-12' : 'col-12',
        'sidebar_class' => 'col-lg-4 col-md-12',
    );
}

/**
 * Get reading time for post content
 */
function recruitpro_get_reading_time() {
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time);
}

/**
 * Get social sharing buttons
 */
function recruitpro_get_social_sharing_buttons() {
    $post_url = get_permalink();
    $post_title = get_the_title();
    $post_excerpt = get_the_excerpt();
    
    $buttons = array(
        'linkedin' => array(
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($post_url),
            'label' => 'LinkedIn',
            'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'
        ),
        'twitter' => array(
            'url' => 'https://twitter.com/intent/tweet?url=' . urlencode($post_url) . '&text=' . urlencode($post_title),
            'label' => 'Twitter',
            'icon' => '<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>'
        ),
        'facebook' => array(
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($post_url),
            'label' => 'Facebook',
            'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'
        ),
        'email' => array(
            'url' => 'mailto:?subject=' . urlencode($post_title) . '&body=' . urlencode($post_excerpt . ' ' . $post_url),
            'label' => 'Email',
            'icon' => '<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>'
        ),
    );
    
    $output = '<div class="social-sharing-buttons">';
    foreach ($buttons as $platform => $data) {
        $output .= sprintf(
            '<a href="%s" target="_blank" rel="noopener noreferrer" class="social-share-btn social-share-%s" aria-label="%s">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">%s</svg>
                <span class="btn-text">%s</span>
            </a>',
            esc_url($data['url']),
            esc_attr($platform),
            esc_attr(sprintf(__('Share on %s', 'recruitpro'), $data['label'])),
            $data['icon'],
            esc_html($data['label'])
        );
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Get author bio section
 */
function recruitpro_get_author_bio() {
    $author_id = get_the_author_meta('ID');
    $author_bio = get_the_author_meta('description');
    $author_name = get_the_author_meta('display_name');
    $author_job_title = get_the_author_meta('job_title');
    $author_company = get_the_author_meta('company');
    $author_website = get_the_author_meta('user_url');
    $author_linkedin = get_the_author_meta('linkedin');
    $author_twitter = get_the_author_meta('twitter');
    
    if (empty($author_bio)) {
        return '';
    }
    
    $output = '<div class="author-bio-card">';
    $output .= '<div class="author-bio-header">';
    $output .= '<h3 class="author-bio-title">' . esc_html__('About the Author', 'recruitpro') . '</h3>';
    $output .= '</div>';
    
    $output .= '<div class="author-bio-content">';
    $output .= '<div class="author-bio-avatar">';
    $output .= get_avatar($author_id, 80, '', $author_name, array('class' => 'author-avatar-large'));
    $output .= '</div>';
    
    $output .= '<div class="author-bio-details">';
    $output .= '<h4 class="author-bio-name">' . esc_html($author_name) . '</h4>';
    
    if (!empty($author_job_title)) {
        $output .= '<p class="author-bio-title-job">' . esc_html($author_job_title);
        if (!empty($author_company)) {
            $output .= ' ' . esc_html__('at', 'recruitpro') . ' ' . esc_html($author_company);
        }
        $output .= '</p>';
    }
    
    $output .= '<div class="author-bio-description">' . wp_kses_post($author_bio) . '</div>';
    
    // Author links
    $author_links = array();
    if (!empty($author_website)) {
        $author_links[] = '<a href="' . esc_url($author_website) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Website', 'recruitpro') . '</a>';
    }
    if (!empty($author_linkedin)) {
        $author_links[] = '<a href="' . esc_url($author_linkedin) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('LinkedIn', 'recruitpro') . '</a>';
    }
    if (!empty($author_twitter)) {
        $author_links[] = '<a href="' . esc_url($author_twitter) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Twitter', 'recruitpro') . '</a>';
    }
    
    if (!empty($author_links)) {
        $output .= '<div class="author-bio-links">' . implode(' • ', $author_links) . '</div>';
    }
    
    $output .= '<div class="author-bio-posts">';
    $output .= '<a href="' . esc_url(get_author_posts_url($author_id)) . '" class="author-posts-link">';
    $output .= esc_html__('View all posts by', 'recruitpro') . ' ' . esc_html($author_name);
    $output .= '</a>';
    $output .= '</div>';
    
    $output .= '</div>'; // .author-bio-details
    $output .= '</div>'; // .author-bio-content
    $output .= '</div>'; // .author-bio-card
    
    return $output;
}

/**
 * Get related posts
 */
function recruitpro_get_related_posts() {
    $post_id = get_the_ID();
    $categories = get_the_category($post_id);
    
    if (empty($categories)) {
        return '';
    }
    
    $category_ids = array();
    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }
    
    $related_posts = get_posts(array(
        'numberposts' => 3,
        'post_status' => 'publish',
        'post__not_in' => array($post_id),
        'category__in' => $category_ids,
        'orderby' => 'rand',
    ));
    
    if (empty($related_posts)) {
        // Fallback to recent posts
        $related_posts = get_posts(array(
            'numberposts' => 3,
            'post_status' => 'publish',
            'post__not_in' => array($post_id),
            'orderby' => 'date',
            'order' => 'DESC',
        ));
    }
    
    if (empty($related_posts)) {
        return '';
    }
    
    $output = '<div class="related-posts-container">';
    $output .= '<h3 class="related-posts-title">' . esc_html__('Related Articles', 'recruitpro') . '</h3>';
    $output .= '<div class="row related-posts-grid">';
    
    foreach ($related_posts as $post) {
        setup_postdata($post);
        
        $output .= '<div class="col-md-4">';
        $output .= '<article class="related-post-item">';
        
        // Thumbnail
        if (has_post_thumbnail($post->ID)) {
            $output .= '<div class="related-post-thumbnail">';
            $output .= '<a href="' . esc_url(get_permalink($post->ID)) . '">';
            $output .= get_the_post_thumbnail($post->ID, 'medium', array('class' => 'img-fluid'));
            $output .= '</a>';
            $output .= '</div>';
        }
        
        $output .= '<div class="related-post-content">';
        $output .= '<h4 class="related-post-title">';
        $output .= '<a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html(get_the_title($post->ID)) . '</a>';
        $output .= '</h4>';
        
        $output .= '<div class="related-post-meta">';
        $output .= '<time datetime="' . esc_attr(get_the_date('c', $post->ID)) . '">' . esc_html(get_the_date('', $post->ID)) . '</time>';
        $output .= '</div>';
        
        $output .= '<div class="related-post-excerpt">';
        $output .= wp_trim_words(get_the_excerpt($post->ID), 15);
        $output .= '</div>';
        
        $output .= '</div>'; // .related-post-content
        $output .= '</article>';
        $output .= '</div>';
    }
    
    wp_reset_postdata();
    
    $output .= '</div>'; // .row
    $output .= '</div>'; // .related-posts-container
    
    return $output;
}

/**
 * Generate table of contents from post content
 */
function recruitpro_generate_table_of_contents() {
    $content = get_the_content();
    
    // Find all headings
    preg_match_all('/<h([2-6])([^>]*)>(.*?)<\/h[2-6]>/i', $content, $matches);
    
    if (empty($matches[0])) {
        return '<p>' . esc_html__('No headings found in content.', 'recruitpro') . '</p>';
    }
    
    $toc = '<ol class="table-of-contents-list">';
    
    for ($i = 0; $i < count($matches[0]); $i++) {
        $level = $matches[1][$i];
        $attributes = $matches[2][$i];
        $title = strip_tags($matches[3][$i]);
        
        // Generate ID if not present
        $id = '';
        if (preg_match('/id="([^"]*)"/', $attributes, $id_match)) {
            $id = $id_match[1];
        } else {
            $id = sanitize_title($title);
        }
        
        $toc .= '<li class="toc-item toc-level-' . esc_attr($level) . '">';
        $toc .= '<a href="#' . esc_attr($id) . '" class="toc-link">' . esc_html($title) . '</a>';
        $toc .= '</li>';
    }
    
    $toc .= '</ol>';
    
    return $toc;
}

/**
 * Increment post view count
 */
function recruitpro_increment_post_views() {
    if (is_single() && !is_user_logged_in()) {
        $post_id = get_the_ID();
        $count = get_post_meta($post_id, 'post_views_count', true);
        $count = empty($count) ? 0 : intval($count);
        $count++;
        update_post_meta($post_id, 'post_views_count', $count);
    }
}
add_action('wp_head', 'recruitpro_increment_post_views');
?>