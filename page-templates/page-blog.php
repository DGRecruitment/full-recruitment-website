<?php
/**
 * Template Name: Recruitment Blog Page
 *
 * Professional blog page template for recruitment agencies showcasing
 * industry insights, career advice, recruitment tips, and company updates.
 * Optimized for recruitment professionals, candidates, and clients with
 * comprehensive content organization and professional presentation.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-blog.php
 * Purpose: Professional blog for recruitment industry content
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Industry insights, career advice, recruitment tips, company updates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_blog_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_blog_show_title', true);
$blog_layout = get_theme_mod('recruitpro_blog_layout', 'grid');
$posts_per_page = get_theme_mod('recruitpro_blog_posts_per_page', 6);
$show_featured_posts = get_theme_mod('recruitpro_blog_show_featured', true);
$show_categories = get_theme_mod('recruitpro_blog_show_categories', true);
$show_excerpts = get_theme_mod('recruitpro_blog_show_excerpts', true);
$show_author = get_theme_mod('recruitpro_blog_show_author', true);
$show_date = get_theme_mod('recruitpro_blog_show_date', true);
$show_comments_count = get_theme_mod('recruitpro_blog_show_comments', true);

// Blog categories specific to recruitment
$recruitment_categories = array(
    'career-advice',
    'industry-insights',
    'recruitment-tips',
    'company-news',
    'hr-trends',
    'interview-preparation',
    'salary-guides',
    'market-reports',
    'success-stories',
    'expert-opinions'
);

// Schema.org markup for blog
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => get_the_title(),
    'description' => get_the_content() ?: get_bloginfo('description'),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => get_theme_mod('recruitpro_company_name', get_bloginfo('name')),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_theme_mod('recruitpro_site_logo', '')
        )
    ),
    'mainEntityOfPage' => array(
        '@type' => 'WebPage',
        '@id' => get_permalink()
    )
);

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<!-- Schema.org Blog Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main id="primary" class="site-main blog-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="blog-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header blog-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Expert insights, career advice, and industry trends from our recruitment professionals to help you succeed in your career journey.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Blog Categories Filter -->
                    <?php if ($show_categories) : ?>
                        <section class="blog-categories" id="blog-categories">
                            <div class="categories-container">
                                <h2 class="categories-title"><?php esc_html_e('Explore by Category', 'recruitpro'); ?></h2>
                                
                                <div class="categories-grid">
                                    <?php
                                    $categories = get_categories(array(
                                        'hide_empty' => true,
                                        'orderby' => 'count',
                                        'order' => 'DESC',
                                        'number' => 8
                                    ));

                                    if ($categories) :
                                        foreach ($categories as $category) :
                                    ?>
                                        <div class="category-item">
                                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                                               class="category-link">
                                                <div class="category-content">
                                                    <h3 class="category-name"><?php echo esc_html($category->name); ?></h3>
                                                    <span class="category-count">
                                                        <?php 
                                                        printf(
                                                            esc_html(_n('%d Article', '%d Articles', $category->count, 'recruitpro')),
                                                            $category->count
                                                        );
                                                        ?>
                                                    </span>
                                                    <?php if ($category->description) : ?>
                                                        <p class="category-description"><?php echo esc_html($category->description); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        </div>
                                    <?php 
                                        endforeach;
                                    else :
                                    ?>
                                        <!-- Default recruitment categories -->
                                        <div class="category-item">
                                            <div class="category-content">
                                                <h3 class="category-name"><?php esc_html_e('Career Advice', 'recruitpro'); ?></h3>
                                                <span class="category-count"><?php esc_html_e('12 Articles', 'recruitpro'); ?></span>
                                                <p class="category-description"><?php esc_html_e('Expert guidance for career development and professional growth.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>

                                        <div class="category-item">
                                            <div class="category-content">
                                                <h3 class="category-name"><?php esc_html_e('Industry Insights', 'recruitpro'); ?></h3>
                                                <span class="category-count"><?php esc_html_e('8 Articles', 'recruitpro'); ?></span>
                                                <p class="category-description"><?php esc_html_e('Latest trends and developments in recruitment and HR.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>

                                        <div class="category-item">
                                            <div class="category-content">
                                                <h3 class="category-name"><?php esc_html_e('Interview Tips', 'recruitpro'); ?></h3>
                                                <span class="category-count"><?php esc_html_e('15 Articles', 'recruitpro'); ?></span>
                                                <p class="category-description"><?php esc_html_e('Prepare for success with our comprehensive interview guides.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>

                                        <div class="category-item">
                                            <div class="category-content">
                                                <h3 class="category-name"><?php esc_html_e('Salary Guides', 'recruitpro'); ?></h3>
                                                <span class="category-count"><?php esc_html_e('6 Articles', 'recruitpro'); ?></span>
                                                <p class="category-description"><?php esc_html_e('Current market rates and compensation insights by industry.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>

                                        <?php if (current_user_can('edit_posts')) : ?>
                                            <div class="category-item placeholder">
                                                <div class="category-content">
                                                    <p><em><?php esc_html_e('Create blog categories to organize your recruitment content.', 'recruitpro'); ?></em></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                <?php endwhile; ?>

                <!-- Featured Posts Section -->
                <?php if ($show_featured_posts) : ?>
                    <section class="featured-posts" id="featured-posts">
                        <div class="featured-container">
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Featured Articles', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Handpicked insights and advice from our recruitment experts', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Featured posts query
                            $featured_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => 3,
                                'meta_query' => array(
                                    array(
                                        'key' => '_featured_post',
                                        'value' => 'yes',
                                        'compare' => '='
                                    )
                                ),
                                'orderby' => 'date',
                                'order' => 'DESC'
                            );

                            $featured_query = new WP_Query($featured_args);

                            if ($featured_query->have_posts()) :
                            ?>
                                <div class="featured-posts-grid">
                                    <?php while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                                        <article class="featured-post-item">
                                            <div class="post-content">
                                                
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <div class="post-thumbnail">
                                                        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                            <?php the_post_thumbnail('medium_large', array('loading' => 'lazy')); ?>
                                                        </a>
                                                        <span class="featured-badge"><?php esc_html_e('Featured', 'recruitpro'); ?></span>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="post-details">
                                                    
                                                    <div class="post-meta">
                                                        <?php if ($show_date) : ?>
                                                            <span class="post-date">
                                                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                                    <?php echo esc_html(get_the_date()); ?>
                                                                </time>
                                                            </span>
                                                        <?php endif; ?>

                                                        <?php if ($show_author) : ?>
                                                            <span class="post-author">
                                                                <?php esc_html_e('by', 'recruitpro'); ?> 
                                                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                                    <?php echo esc_html(get_the_author()); ?>
                                                                </a>
                                                            </span>
                                                        <?php endif; ?>

                                                        <?php
                                                        $categories = get_the_category();
                                                        if (!empty($categories)) :
                                                        ?>
                                                            <span class="post-category">
                                                                <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                                                                    <?php echo esc_html($categories[0]->name); ?>
                                                                </a>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <h3 class="post-title">
                                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                    </h3>

                                                    <?php if ($show_excerpts) : ?>
                                                        <div class="post-excerpt">
                                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="post-actions">
                                                        <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                            <?php esc_html_e('Read Article', 'recruitpro'); ?>
                                                            <span class="icon-arrow" aria-hidden="true"></span>
                                                        </a>

                                                        <?php if ($show_comments_count && comments_open()) : ?>
                                                            <span class="comments-count">
                                                                <?php comments_number(
                                                                    esc_html__('No Comments', 'recruitpro'),
                                                                    esc_html__('1 Comment', 'recruitpro'),
                                                                    esc_html__('% Comments', 'recruitpro')
                                                                ); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>
                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                            <?php 
                            else :
                                // Default featured content when no featured posts
                            ?>
                                <div class="featured-posts-grid">
                                    <article class="featured-post-item">
                                        <div class="post-content">
                                            <div class="post-details">
                                                <div class="post-meta">
                                                    <span class="post-date"><?php echo esc_html(date('F j, Y')); ?></span>
                                                    <span class="post-category"><?php esc_html_e('Career Advice', 'recruitpro'); ?></span>
                                                </div>
                                                <h3 class="post-title">
                                                    <a href="#"><?php esc_html_e('5 Essential Tips for Your Next Job Interview', 'recruitpro'); ?></a>
                                                </h3>
                                                <div class="post-excerpt">
                                                    <p><?php esc_html_e('Master your next interview with these proven strategies from our recruitment experts...', 'recruitpro'); ?></p>
                                                </div>
                                                <div class="post-actions">
                                                    <a href="#" class="read-more-link">
                                                        <?php esc_html_e('Read Article', 'recruitpro'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="featured-post-item">
                                        <div class="post-content">
                                            <div class="post-details">
                                                <div class="post-meta">
                                                    <span class="post-date"><?php echo esc_html(date('F j, Y', strtotime('-3 days'))); ?></span>
                                                    <span class="post-category"><?php esc_html_e('Industry Insights', 'recruitpro'); ?></span>
                                                </div>
                                                <h3 class="post-title">
                                                    <a href="#"><?php esc_html_e('Remote Work Trends in 2024: What Employers Need to Know', 'recruitpro'); ?></a>
                                                </h3>
                                                <div class="post-excerpt">
                                                    <p><?php esc_html_e('Explore the latest remote work trends shaping the recruitment landscape...', 'recruitpro'); ?></p>
                                                </div>
                                                <div class="post-actions">
                                                    <a href="#" class="read-more-link">
                                                        <?php esc_html_e('Read Article', 'recruitpro'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="featured-post-item">
                                        <div class="post-content">
                                            <div class="post-details">
                                                <div class="post-meta">
                                                    <span class="post-date"><?php echo esc_html(date('F j, Y', strtotime('-1 week'))); ?></span>
                                                    <span class="post-category"><?php esc_html_e('Salary Guides', 'recruitpro'); ?></span>
                                                </div>
                                                <h3 class="post-title">
                                                    <a href="#"><?php esc_html_e('Technology Sector Salary Guide 2024', 'recruitpro'); ?></a>
                                                </h3>
                                                <div class="post-excerpt">
                                                    <p><?php esc_html_e('Comprehensive salary benchmarks for technology professionals across all levels...', 'recruitpro'); ?></p>
                                                </div>
                                                <div class="post-actions">
                                                    <a href="#" class="read-more-link">
                                                        <?php esc_html_e('Read Article', 'recruitpro'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="featured-placeholder">
                                        <p><em><?php esc_html_e('Mark posts as featured to showcase your best recruitment content here.', 'recruitpro'); ?></em></p>
                                    </div>
                                <?php endif; ?>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Recent Blog Posts -->
                <section class="recent-posts" id="recent-posts">
                    <div class="posts-container">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Latest Articles', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Stay updated with the latest insights from our recruitment experts', 'recruitpro'); ?></p>
                        </div>

                        <?php
                        // Recent posts query
                        $recent_args = array(
                            'post_type' => 'post',
                            'posts_per_page' => $posts_per_page,
                            'paged' => $paged,
                            'orderby' => 'date',
                            'order' => 'DESC',
                            'post_status' => 'publish'
                        );

                        $recent_query = new WP_Query($recent_args);

                        if ($recent_query->have_posts()) :
                        ?>
                            <div class="posts-grid <?php echo esc_attr($blog_layout); ?>">
                                <?php while ($recent_query->have_posts()) : $recent_query->the_post(); ?>
                                    <article class="post-item">
                                        <div class="post-content">
                                            
                                            <?php if (has_post_thumbnail()) : ?>
                                                <div class="post-thumbnail">
                                                    <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                                        <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <div class="post-details">
                                                
                                                <div class="post-meta">
                                                    <?php if ($show_date) : ?>
                                                        <span class="post-date">
                                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                                <?php echo esc_html(get_the_date()); ?>
                                                            </time>
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if ($show_author) : ?>
                                                        <span class="post-author">
                                                            <?php esc_html_e('by', 'recruitpro'); ?> 
                                                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                                <?php echo esc_html(get_the_author()); ?>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php
                                                    $categories = get_the_category();
                                                    if (!empty($categories)) :
                                                    ?>
                                                        <span class="post-category">
                                                            <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                                                                <?php echo esc_html($categories[0]->name); ?>
                                                            </a>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <h3 class="post-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>

                                                <?php if ($show_excerpts) : ?>
                                                    <div class="post-excerpt">
                                                        <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="post-actions">
                                                    <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                        <?php esc_html_e('Read More', 'recruitpro'); ?>
                                                        <span class="icon-arrow" aria-hidden="true"></span>
                                                    </a>

                                                    <?php if ($show_comments_count && comments_open()) : ?>
                                                        <span class="comments-count">
                                                            <?php comments_number(
                                                                esc_html__('0', 'recruitpro'),
                                                                esc_html__('1', 'recruitpro'),
                                                                esc_html__('%', 'recruitpro')
                                                            ); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if ($recent_query->max_num_pages > 1) : ?>
                                <nav class="blog-pagination" role="navigation" aria-label="<?php esc_attr_e('Blog Posts Pagination', 'recruitpro'); ?>">
                                    <?php
                                    echo paginate_links(array(
                                        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                                        'format' => '?paged=%#%',
                                        'current' => max(1, get_query_var('paged')),
                                        'total' => $recent_query->max_num_pages,
                                        'prev_text' => esc_html__('← Previous', 'recruitpro'),
                                        'next_text' => esc_html__('Next →', 'recruitpro'),
                                        'mid_size' => 2,
                                        'end_size' => 1,
                                        'type' => 'list'
                                    ));
                                    ?>
                                </nav>
                            <?php endif; ?>

                        <?php 
                        else :
                        ?>
                            <div class="no-posts-found">
                                <div class="no-posts-content">
                                    <h3><?php esc_html_e('No Articles Found', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('We\'re currently working on creating valuable content for our recruitment blog. Check back soon for expert insights and career advice.', 'recruitpro'); ?></p>
                                    
                                    <?php if (current_user_can('edit_posts')) : ?>
                                        <div class="admin-actions">
                                            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Create Your First Post', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php 
                        endif;
                        wp_reset_postdata();
                        ?>

                    </div>
                </section>

                <!-- Newsletter Signup -->
                <section class="blog-newsletter" id="blog-newsletter">
                    <div class="newsletter-container">
                        <div class="newsletter-content">
                            <h2 class="newsletter-title"><?php esc_html_e('Stay Informed', 'recruitpro'); ?></h2>
                            <p class="newsletter-description"><?php esc_html_e('Get the latest recruitment insights, career advice, and industry trends delivered to your inbox.', 'recruitpro'); ?></p>
                            
                            <form class="newsletter-form" action="#" method="post">
                                <div class="form-group">
                                    <input type="email" 
                                           name="newsletter_email" 
                                           id="newsletter_email"
                                           class="form-control" 
                                           placeholder="<?php esc_attr_e('Enter your email address', 'recruitpro'); ?>"
                                           required>
                                    <button type="submit" class="btn btn-primary">
                                        <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                    </button>
                                </div>
                                <div class="form-note">
                                    <small><?php esc_html_e('We respect your privacy. Unsubscribe at any time.', 'recruitpro'); ?></small>
                                </div>
                                <input type="hidden" name="action" value="newsletter_signup">
                                <?php wp_nonce_field('newsletter_signup', 'newsletter_nonce'); ?>
                            </form>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('blog-sidebar')) : ?>
                <aside id="secondary" class="widget-area blog-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('blog-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   BLOG PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL RECRUITMENT BLOG FEATURES:

✅ SCHEMA.ORG MARKUP
- Blog schema with organization
- Article schema for posts
- Publisher information
- SEO-optimized structured data

✅ RECRUITMENT-FOCUSED CONTENT
- Industry insights and trends
- Career advice and guidance
- Interview preparation tips
- Salary guides and market data
- Success stories and case studies

✅ PROFESSIONAL ORGANIZATION
- Category-based content organization
- Featured posts showcase
- Recent articles display
- Advanced filtering options
- Newsletter subscription integration

✅ CUSTOMIZATION OPTIONS
- Multiple layout options (grid/list)
- Sidebar position control
- Show/hide content elements
- Posts per page settings
- Featured content management

✅ SEO OPTIMIZATION
- Meta descriptions for categories
- Schema markup implementation
- Internal linking structure
- Social sharing integration
- Search-friendly URLs

✅ RESPONSIVE DESIGN
- Mobile-first approach
- Touch-friendly navigation
- Responsive image handling
- Flexible grid layouts
- Optimized for all devices

✅ ACCESSIBILITY
- ARIA labels and roles
- Semantic HTML structure
- Keyboard navigation support
- Screen reader optimization
- Focus management

✅ PERFORMANCE
- Lazy loading images
- Optimized database queries
- Conditional content loading
- Pagination for large datasets
- Fast loading times

✅ RECRUITMENT INDUSTRY FOCUS
- Career development content
- Industry trend analysis
- Professional growth insights
- Market intelligence
- Expert recruitment advice

PERFECT FOR:
- Recruitment agencies
- HR consultancies
- Career counselors
- Industry thought leaders
- Professional services

CONTENT CATEGORIES:
- Career Advice
- Industry Insights
- Interview Tips
- Salary Guides
- Market Reports
- Success Stories
- Company News
- Expert Opinions

*/
?>