<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and is the
 * fallback template for all content. For recruitment agencies, this template
 * handles all content types professionally when no specific template exists.
 * Features professional layout, industry-appropriate content display, and
 * comprehensive fallback functionality for recruitment industry content.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/index.php
 * Purpose: Universal fallback template for all content types
 * Context: Recruitment industry content display
 * Features: Flexible layout, professional presentation, industry focus
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Determine content context and layout
$context = recruitpro_get_content_context();
$layout_class = recruitpro_get_layout_class();
$show_sidebar = recruitpro_show_sidebar();

?>

<main id="primary" class="site-main index-main <?php echo esc_attr($context['main_class']); ?>">
    <div class="container">
        
        <!-- Content Header -->
        <?php if ($context['show_header']) : ?>
            <header class="content-header <?php echo esc_attr($context['header_class']); ?>">
                <div class="row">
                    <div class="col-12">
                        <div class="header-content">
                            
                            <!-- Page Title -->
                            <h1 class="content-title">
                                <?php echo wp_kses_post($context['title']); ?>
                            </h1>
                            
                            <!-- Page Description -->
                            <?php if (!empty($context['description'])) : ?>
                                <div class="content-description">
                                    <?php echo wp_kses_post($context['description']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Content Meta -->
                            <?php if (!empty($context['meta'])) : ?>
                                <div class="content-meta">
                                    <?php echo wp_kses_post($context['meta']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Breadcrumbs -->
                            <?php if (function_exists('recruitpro_breadcrumbs')) : ?>
                                <nav class="breadcrumbs-nav" aria-label="<?php esc_attr_e('Breadcrumb navigation', 'recruitpro'); ?>">
                                    <?php recruitpro_breadcrumbs(); ?>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>
        <?php endif; ?>

        <!-- Main Content Area -->
        <div class="row">
            <div class="<?php echo esc_attr($layout_class['content']); ?>">
                
                <!-- Content Navigation (Top) -->
                <?php if ($context['show_navigation']) : ?>
                    <nav class="content-navigation content-navigation-top">
                        <?php recruitpro_content_navigation(); ?>
                    </nav>
                <?php endif; ?>

                <!-- Content Loop -->
                <div class="content-wrapper <?php echo esc_attr($context['content_class']); ?>">
                    
                    <?php if (have_posts()) : ?>
                        
                        <!-- Content List -->
                        <div class="content-list <?php echo esc_attr($context['list_class']); ?>">
                            
                            <?php
                            // Start the Loop
                            while (have_posts()) :
                                the_post();
                                
                                // Get appropriate template part based on context
                                $template_part = recruitpro_get_template_part_name();
                                
                                if ($template_part) {
                                    get_template_part($template_part['slug'], $template_part['name']);
                                } else {
                                    // Fallback content display
                                    recruitpro_display_fallback_content();
                                }
                                
                            endwhile;
                            ?>
                            
                        </div>

                        <!-- Pagination -->
                        <nav class="content-pagination" aria-label="<?php esc_attr_e('Content pagination', 'recruitpro'); ?>">
                            <?php
                            // Use different pagination based on context
                            if ($context['pagination_type'] === 'posts') {
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
                            } else {
                                the_posts_navigation(array(
                                    'prev_text' => esc_html__('‹ Older Posts', 'recruitpro'),
                                    'next_text' => esc_html__('Newer Posts ›', 'recruitpro'),
                                ));
                            }
                            ?>
                        </nav>

                        <!-- Content Navigation (Bottom) -->
                        <?php if ($context['show_navigation']) : ?>
                            <nav class="content-navigation content-navigation-bottom">
                                <?php recruitpro_content_navigation(); ?>
                            </nav>
                        <?php endif; ?>

                    <?php else : ?>
                        
                        <!-- No Content Found -->
                        <div class="no-content-found">
                            <div class="no-content-wrapper">
                                <div class="no-content-icon">
                                    <?php echo recruitpro_get_no_content_icon(); ?>
                                </div>
                                
                                <div class="no-content-content">
                                    <h2 class="no-content-title">
                                        <?php echo esc_html($context['no_content_title']); ?>
                                    </h2>
                                    
                                    <p class="no-content-description">
                                        <?php echo esc_html($context['no_content_description']); ?>
                                    </p>
                                    
                                    <!-- Search Form -->
                                    <?php if ($context['show_search']) : ?>
                                        <div class="no-content-search">
                                            <h3><?php esc_html_e('Try Searching', 'recruitpro'); ?></h3>
                                            <?php get_search_form(); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Helpful Links -->
                                    <div class="no-content-links">
                                        <h3><?php esc_html_e('Helpful Links', 'recruitpro'); ?></h3>
                                        <div class="helpful-links-grid">
                                            <a href="<?php echo esc_url(home_url('/')); ?>" class="helpful-link">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                                </svg>
                                                <?php esc_html_e('Homepage', 'recruitpro'); ?>
                                            </a>
                                            
                                            <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="helpful-link">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M20 6h-2.5l-1.4-1.4C15.9 4.3 15.6 4 15.3 4H8.7c-.3 0-.6.3-.8.6L6.5 6H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                                </svg>
                                                <?php esc_html_e('Job Opportunities', 'recruitpro'); ?>
                                            </a>
                                            
                                            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="helpful-link">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                                </svg>
                                                <?php esc_html_e('Industry Insights', 'recruitpro'); ?>
                                            </a>
                                            
                                            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="helpful-link">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                                </svg>
                                                <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Recent Content -->
                                    <?php if ($context['show_recent']) : ?>
                                        <div class="no-content-recent">
                                            <h3><?php esc_html_e('Recent Content', 'recruitpro'); ?></h3>
                                            <?php echo recruitpro_get_recent_content(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <?php if ($show_sidebar) : ?>
                <aside class="<?php echo esc_attr($layout_class['sidebar']); ?>">
                    <div class="sidebar-content">
                        <?php
                        // Context-specific sidebar
                        $sidebar_name = $context['sidebar_name'];
                        if (is_active_sidebar($sidebar_name)) {
                            dynamic_sidebar($sidebar_name);
                        } else {
                            // Fallback sidebar content
                            recruitpro_fallback_sidebar_content();
                        }
                        ?>
                    </div>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();

/**
 * Helper Functions for Index Template
 */

/**
 * Get content context based on current query
 */
function recruitpro_get_content_context() {
    global $wp_query;
    
    $context = array(
        'show_header' => true,
        'show_navigation' => false,
        'show_search' => true,
        'show_recent' => true,
        'pagination_type' => 'posts',
        'main_class' => 'index-main',
        'header_class' => 'generic-header',
        'content_class' => 'generic-content',
        'list_class' => 'post-list',
        'sidebar_name' => 'sidebar-1',
    );
    
    if (is_home() || is_front_page()) {
        $context = array_merge($context, array(
            'title' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'meta' => '',
            'main_class' => 'home-main',
            'header_class' => 'home-header',
            'no_content_title' => esc_html__('Welcome to Our Recruitment Agency', 'recruitpro'),
            'no_content_description' => esc_html__('We help connect talented professionals with exciting career opportunities.', 'recruitpro'),
        ));
    } elseif (is_search()) {
        $search_query = get_search_query();
        $context = array_merge($context, array(
            'title' => sprintf(esc_html__('Search Results for: %s', 'recruitpro'), '<span class="search-term">' . esc_html($search_query) . '</span>'),
            'description' => sprintf(esc_html__('Showing recruitment-related content matching "%s"', 'recruitpro'), esc_html($search_query)),
            'meta' => sprintf(esc_html__('%s results found', 'recruitpro'), number_format_i18n($wp_query->found_posts)),
            'main_class' => 'search-main',
            'header_class' => 'search-header',
            'show_search' => false,
            'no_content_title' => sprintf(esc_html__('No Results for "%s"', 'recruitpro'), esc_html($search_query)),
            'no_content_description' => esc_html__('Try different keywords or browse our job opportunities and resources.', 'recruitpro'),
        ));
    } elseif (is_archive()) {
        $context = array_merge($context, array(
            'title' => get_the_archive_title(),
            'description' => get_the_archive_description(),
            'meta' => sprintf(esc_html__('%s articles in this collection', 'recruitpro'), number_format_i18n($wp_query->found_posts)),
            'main_class' => 'archive-main',
            'header_class' => 'archive-header',
            'show_navigation' => true,
            'no_content_title' => esc_html__('No Content in This Archive', 'recruitpro'),
            'no_content_description' => esc_html__('This archive doesn\'t contain any published content yet.', 'recruitpro'),
        ));
    } elseif (is_single()) {
        $context = array_merge($context, array(
            'title' => get_the_title(),
            'description' => '',
            'meta' => recruitpro_get_post_meta(),
            'main_class' => 'single-main',
            'header_class' => 'single-header',
            'content_class' => 'single-content',
            'list_class' => 'single-post',
            'pagination_type' => 'single',
            'show_header' => false, // Single posts handle their own headers
            'no_content_title' => esc_html__('Content Not Available', 'recruitpro'),
            'no_content_description' => esc_html__('The requested content is not available or has been removed.', 'recruitpro'),
        ));
    } elseif (is_page()) {
        $context = array_merge($context, array(
            'title' => get_the_title(),
            'description' => '',
            'meta' => '',
            'main_class' => 'page-main',
            'header_class' => 'page-header',
            'content_class' => 'page-content',
            'list_class' => 'page-list',
            'show_header' => false, // Pages handle their own headers
            'no_content_title' => esc_html__('Page Not Available', 'recruitpro'),
            'no_content_description' => esc_html__('The requested page content is not available.', 'recruitpro'),
        ));
    } else {
        // Generic fallback
        $context = array_merge($context, array(
            'title' => esc_html__('Professional Recruitment Services', 'recruitpro'),
            'description' => esc_html__('Connecting talent with opportunity in the professional recruitment industry.', 'recruitpro'),
            'meta' => '',
            'no_content_title' => esc_html__('Content Not Found', 'recruitpro'),
            'no_content_description' => esc_html__('The content you\'re looking for is not available. Please try our search or browse our resources.', 'recruitpro'),
        ));
    }
    
    return $context;
}

/**
 * Get layout classes based on sidebar visibility
 */
function recruitpro_get_layout_class() {
    $show_sidebar = recruitpro_show_sidebar();
    
    if ($show_sidebar) {
        return array(
            'content' => 'col-lg-8 col-md-12',
            'sidebar' => 'col-lg-4 col-md-12',
        );
    } else {
        return array(
            'content' => 'col-12',
            'sidebar' => '',
        );
    }
}

/**
 * Determine if sidebar should be shown
 */
function recruitpro_show_sidebar() {
    // Don't show sidebar on certain page types
    if (is_front_page() || is_404()) {
        return false;
    }
    
    // Check if current page/post has sidebar disabled
    if (is_singular()) {
        $hide_sidebar = get_post_meta(get_the_ID(), '_hide_sidebar', true);
        if ($hide_sidebar === '1') {
            return false;
        }
    }
    
    // Check theme customizer setting
    $show_sidebar = get_theme_mod('recruitpro_show_sidebar', true);
    
    return $show_sidebar;
}

/**
 * Get appropriate template part name
 */
function recruitpro_get_template_part_name() {
    $post_type = get_post_type();
    
    // Check for specific template parts
    $template_parts = array(
        'job' => array('slug' => 'template-parts/content', 'name' => 'job'),
        'case_study' => array('slug' => 'template-parts/content', 'name' => 'case-study'),
        'testimonial' => array('slug' => 'template-parts/content', 'name' => 'testimonial'),
        'team_member' => array('slug' => 'template-parts/content', 'name' => 'team-member'),
    );
    
    if (isset($template_parts[$post_type])) {
        return $template_parts[$post_type];
    }
    
    // Default template parts based on context
    if (is_search()) {
        return array('slug' => 'template-parts/content', 'name' => 'search');
    } elseif (is_archive()) {
        return array('slug' => 'template-parts/content', 'name' => 'archive');
    } else {
        return array('slug' => 'template-parts/content', 'name' => 'excerpt');
    }
}

/**
 * Display fallback content when no specific template part exists
 */
function recruitpro_display_fallback_content() {
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('fallback-content-item'); ?>>
        
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
                <h2 class="entry-title">
                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php the_title(); ?>
                    </a>
                </h2>
                
                <div class="entry-meta">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                        <?php echo esc_html(get_the_date()); ?>
                    </time>
                    
                    <?php if (get_post_type() === 'post') : ?>
                        <span class="meta-separator">•</span>
                        <span class="author">
                            <?php echo esc_html(get_the_author()); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (get_comments_number() > 0) : ?>
                        <span class="meta-separator">•</span>
                        <a href="<?php comments_link(); ?>">
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

            <div class="entry-summary">
                <?php
                if (has_excerpt()) {
                    the_excerpt();
                } else {
                    echo '<p>' . wp_trim_words(get_the_content(), 25, '...') . '</p>';
                }
                ?>
            </div>

            <footer class="entry-footer">
                <a href="<?php the_permalink(); ?>" class="read-more">
                    <?php esc_html_e('Continue Reading', 'recruitpro'); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                    </svg>
                </a>
            </footer>
        </div>
    </article>
    <?php
}

/**
 * Get content navigation
 */
function recruitpro_content_navigation() {
    if (is_archive()) {
        ?>
        <div class="archive-navigation">
            <div class="archive-filters">
                <!-- Add archive-specific navigation here -->
            </div>
        </div>
        <?php
    }
}

/**
 * Get no content icon based on context
 */
function recruitpro_get_no_content_icon() {
    if (is_search()) {
        return '<svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </svg>';
    } else {
        return '<svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 6h-2.5l-1.4-1.4C15.9 4.3 15.6 4 15.3 4H8.7c-.3 0-.6.3-.8.6L6.5 6H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
        </svg>';
    }
}

/**
 * Get post meta information
 */
function recruitpro_get_post_meta() {
    $meta_parts = array();
    
    // Published date
    $meta_parts[] = '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>';
    
    // Author (for posts)
    if (get_post_type() === 'post') {
        $meta_parts[] = '<span class="author">' . esc_html(get_the_author()) . '</span>';
    }
    
    // Categories (for posts)
    if (get_post_type() === 'post') {
        $categories = get_the_category();
        if (!empty($categories)) {
            $cat_links = array();
            foreach ($categories as $category) {
                $cat_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
            }
            $meta_parts[] = '<span class="categories">' . implode(', ', $cat_links) . '</span>';
        }
    }
    
    return implode(' • ', $meta_parts);
}

/**
 * Get recent content for no content fallback
 */
function recruitpro_get_recent_content() {
    $recent_posts = get_posts(array(
        'numberposts' => 3,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    
    if (empty($recent_posts)) {
        return '<p>' . esc_html__('No recent content available.', 'recruitpro') . '</p>';
    }
    
    $output = '<ul class="recent-content-list">';
    foreach ($recent_posts as $post) {
        $output .= sprintf(
            '<li><a href="%s">%s</a></li>',
            esc_url(get_permalink($post->ID)),
            esc_html(get_the_title($post->ID))
        );
    }
    $output .= '</ul>';
    
    return $output;
}

/**
 * Fallback sidebar content
 */
function recruitpro_fallback_sidebar_content() {
    ?>
    <!-- Search Widget -->
    <div class="widget widget-search">
        <h3 class="widget-title"><?php esc_html_e('Search', 'recruitpro'); ?></h3>
        <?php get_search_form(); ?>
    </div>

    <!-- Recent Posts -->
    <div class="widget widget-recent-posts">
        <h3 class="widget-title"><?php esc_html_e('Recent Posts', 'recruitpro'); ?></h3>
        <?php
        $recent_posts = wp_get_recent_posts(array('numberposts' => 5));
        if (!empty($recent_posts)) {
            echo '<ul>';
            foreach ($recent_posts as $post) {
                echo '<li><a href="' . esc_url(get_permalink($post['ID'])) . '">' . esc_html($post['post_title']) . '</a></li>';
            }
            echo '</ul>';
        }
        ?>
    </div>

    <!-- Categories -->
    <div class="widget widget-categories">
        <h3 class="widget-title"><?php esc_html_e('Categories', 'recruitpro'); ?></h3>
        <?php wp_list_categories(array('title_li' => '')); ?>
    </div>
    <?php
}
?>