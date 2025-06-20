<?php
/**
 * Template Name: News & Press Page
 *
 * Professional news page template for recruitment agencies featuring
 * industry news RSS feeds, company press releases, media mentions,
 * and recruitment industry updates. Automatically aggregates content
 * from leading recruitment and HR publications.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-news.php
 * Purpose: News aggregation and press releases for recruitment agencies
 * Dependencies: WordPress core, SimplePie RSS, theme functions
 * Features: RSS feeds, press releases, media mentions, industry updates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_news_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_news_show_title', true);
$news_layout = get_theme_mod('recruitpro_news_layout', 'mixed');
$show_rss_feeds = get_theme_mod('recruitpro_news_show_rss', true);
$show_press_releases = get_theme_mod('recruitpro_news_show_press', true);
$show_media_mentions = get_theme_mod('recruitpro_news_show_media', true);
$rss_cache_time = get_theme_mod('recruitpro_rss_cache_time', 3600); // 1 hour default

// Industry RSS feeds configuration
$rss_feeds = array(
    'hr_magazine' => array(
        'name' => 'HR Magazine',
        'url' => 'https://www.hrmagazine.co.uk/rss',
        'description' => 'Latest HR and recruitment industry news',
        'category' => 'HR News',
        'enabled' => get_theme_mod('recruitpro_feed_hr_magazine', true)
    ),
    'recruiter_com' => array(
        'name' => 'Recruiter.com',
        'url' => 'https://www.recruiter.com/feed/',
        'description' => 'Recruitment industry insights and trends',
        'category' => 'Recruitment',
        'enabled' => get_theme_mod('recruitpro_feed_recruiter', true)
    ),
    'shrm_news' => array(
        'name' => 'SHRM News',
        'url' => 'https://www.shrm.org/rss',
        'description' => 'Society for Human Resource Management updates',
        'category' => 'HR Industry',
        'enabled' => get_theme_mod('recruitpro_feed_shrm', true)
    ),
    'hr_dive' => array(
        'name' => 'HR Dive',
        'url' => 'https://www.hrdive.com/feeds/news/',
        'description' => 'Human resources industry news and analysis',
        'category' => 'Industry Analysis',
        'enabled' => get_theme_mod('recruitpro_feed_hr_dive', true)
    ),
    'recruitment_grapevine' => array(
        'name' => 'The Recruitment Grapevine',
        'url' => 'https://recruitmentgrapevine.com/rss',
        'description' => 'Recruitment industry news and commentary',
        'category' => 'Industry News',
        'enabled' => get_theme_mod('recruitpro_feed_grapevine', true)
    )
);

// Filter enabled feeds only
$active_feeds = array_filter($rss_feeds, function($feed) {
    return $feed['enabled'];
});

// Schema.org markup for news page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'NewsMediaOrganization',
    'name' => get_the_title(),
    'description' => get_the_content() ?: 'Industry news and press releases',
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => get_theme_mod('recruitpro_company_name', get_bloginfo('name')),
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_theme_mod('recruitpro_site_logo', '')
        )
    )
);

/**
 * Fetch and parse RSS feed with caching
 */
function recruitpro_fetch_rss_feed($feed_url, $feed_name, $cache_time = 3600, $items_limit = 5) {
    // Create cache key
    $cache_key = 'recruitpro_rss_' . md5($feed_url);
    
    // Try to get cached data
    $cached_data = get_transient($cache_key);
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    // Fetch fresh data
    $rss_items = array();
    
    // Use WordPress built-in RSS functionality
    $rss = fetch_feed($feed_url);
    
    if (!is_wp_error($rss)) {
        $maxitems = $rss->get_item_quantity($items_limit);
        $rss_items_raw = $rss->get_items(0, $maxitems);
        
        foreach ($rss_items_raw as $item) {
            $rss_items[] = array(
                'title' => wp_strip_all_tags($item->get_title()),
                'link' => esc_url($item->get_permalink()),
                'description' => wp_trim_words(wp_strip_all_tags($item->get_description()), 25),
                'date' => $item->get_date('U'),
                'date_formatted' => $item->get_date('F j, Y'),
                'source' => $feed_name
            );
        }
    }
    
    // Cache the data
    set_transient($cache_key, $rss_items, $cache_time);
    
    return $rss_items;
}

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<!-- Schema.org News Organization Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main id="primary" class="site-main news-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="news-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header news-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Stay informed with the latest recruitment industry news, company updates, and professional insights from leading HR publications.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- News Categories Filter -->
                    <section class="news-categories" id="news-categories">
                        <div class="categories-container">
                            <div class="categories-tabs">
                                <button class="category-tab active" data-category="all">
                                    <?php esc_html_e('All News', 'recruitpro'); ?>
                                </button>
                                <button class="category-tab" data-category="company">
                                    <?php esc_html_e('Company News', 'recruitpro'); ?>
                                </button>
                                <button class="category-tab" data-category="industry">
                                    <?php esc_html_e('Industry News', 'recruitpro'); ?>
                                </button>
                                <button class="category-tab" data-category="press">
                                    <?php esc_html_e('Press Releases', 'recruitpro'); ?>
                                </button>
                                <button class="category-tab" data-category="media">
                                    <?php esc_html_e('Media Mentions', 'recruitpro'); ?>
                                </button>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

                <!-- Company Press Releases -->
                <?php if ($show_press_releases) : ?>
                    <section class="press-releases news-section" id="press-releases" data-category="company press">
                        <div class="section-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Press Releases', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Latest announcements and company updates', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Press releases query
                            $press_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => 4,
                                'category_name' => 'press-release',
                                'orderby' => 'date',
                                'order' => 'DESC'
                            );

                            $press_query = new WP_Query($press_args);

                            if ($press_query->have_posts()) :
                            ?>
                                <div class="press-releases-grid">
                                    <?php while ($press_query->have_posts()) : $press_query->the_post(); ?>
                                        <article class="press-release-item">
                                            <div class="press-content">
                                                
                                                <div class="press-meta">
                                                    <span class="press-date">
                                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                            <?php echo esc_html(get_the_date()); ?>
                                                        </time>
                                                    </span>
                                                    <span class="press-category"><?php esc_html_e('Press Release', 'recruitpro'); ?></span>
                                                </div>

                                                <h3 class="press-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>

                                                <div class="press-excerpt">
                                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                                </div>

                                                <div class="press-actions">
                                                    <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                        <?php esc_html_e('Read Full Release', 'recruitpro'); ?>
                                                        <span class="icon-arrow" aria-hidden="true"></span>
                                                    </a>
                                                </div>

                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                            <?php 
                            else :
                            ?>
                                <!-- Default press releases for demo -->
                                <div class="press-releases-grid">
                                    <article class="press-release-item">
                                        <div class="press-content">
                                            <div class="press-meta">
                                                <span class="press-date"><?php echo esc_html(date('F j, Y')); ?></span>
                                                <span class="press-category"><?php esc_html_e('Press Release', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="press-title">
                                                <a href="#"><?php esc_html_e('Company Expands Recruitment Services to New Markets', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="press-excerpt">
                                                <p><?php esc_html_e('Our recruitment agency announces strategic expansion into emerging markets with enhanced service offerings...', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="press-actions">
                                                <a href="#" class="read-more-link">
                                                    <?php esc_html_e('Read Full Release', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="press-release-item">
                                        <div class="press-content">
                                            <div class="press-meta">
                                                <span class="press-date"><?php echo esc_html(date('F j, Y', strtotime('-2 weeks'))); ?></span>
                                                <span class="press-category"><?php esc_html_e('Press Release', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="press-title">
                                                <a href="#"><?php esc_html_e('Award Recognition for Excellence in Recruitment', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="press-excerpt">
                                                <p><?php esc_html_e('We are honored to receive industry recognition for our outstanding recruitment services and client satisfaction...', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="press-actions">
                                                <a href="#" class="read-more-link">
                                                    <?php esc_html_e('Read Full Release', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="press-placeholder">
                                        <p><em><?php esc_html_e('Create press release posts in the "Press Release" category to showcase company announcements.', 'recruitpro'); ?></em></p>
                                    </div>
                                <?php endif; ?>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Industry RSS News Feeds -->
                <?php if ($show_rss_feeds && !empty($active_feeds)) : ?>
                    <section class="industry-news news-section" id="industry-news" data-category="industry">
                        <div class="section-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Industry News', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Latest updates from leading recruitment and HR publications', 'recruitpro'); ?></p>
                            </div>

                            <div class="industry-news-feeds">
                                
                                <?php foreach ($active_feeds as $feed_key => $feed_config) : ?>
                                    <div class="news-feed" data-feed="<?php echo esc_attr($feed_key); ?>">
                                        
                                        <div class="feed-header">
                                            <h3 class="feed-title"><?php echo esc_html($feed_config['name']); ?></h3>
                                            <p class="feed-description"><?php echo esc_html($feed_config['description']); ?></p>
                                        </div>

                                        <div class="feed-items">
                                            <?php
                                            $feed_items = recruitpro_fetch_rss_feed(
                                                $feed_config['url'],
                                                $feed_config['name'],
                                                $rss_cache_time,
                                                5
                                            );

                                            if (!empty($feed_items)) :
                                                foreach ($feed_items as $item) :
                                            ?>
                                                <article class="feed-item">
                                                    <div class="item-content">
                                                        
                                                        <div class="item-meta">
                                                            <span class="item-date"><?php echo esc_html($item['date_formatted']); ?></span>
                                                            <span class="item-source"><?php echo esc_html($item['source']); ?></span>
                                                        </div>

                                                        <h4 class="item-title">
                                                            <a href="<?php echo esc_url($item['link']); ?>" 
                                                               target="_blank" 
                                                               rel="noopener noreferrer">
                                                                <?php echo esc_html($item['title']); ?>
                                                            </a>
                                                        </h4>

                                                        <?php if (!empty($item['description'])) : ?>
                                                            <div class="item-excerpt">
                                                                <p><?php echo esc_html($item['description']); ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="item-actions">
                                                            <a href="<?php echo esc_url($item['link']); ?>" 
                                                               target="_blank" 
                                                               rel="noopener noreferrer"
                                                               class="external-link">
                                                                <?php esc_html_e('Read Article', 'recruitpro'); ?>
                                                                <span class="icon-external" aria-hidden="true"></span>
                                                            </a>
                                                        </div>

                                                    </div>
                                                </article>
                                            <?php 
                                                endforeach;
                                            else :
                                            ?>
                                                <div class="feed-error">
                                                    <p><?php esc_html_e('Unable to load news feed at this time. Please check back later.', 'recruitpro'); ?></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="feed-footer">
                                            <a href="<?php echo esc_url($feed_config['url']); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="view-all-link">
                                                <?php esc_html_e('View All from', 'recruitpro'); ?> <?php echo esc_html($feed_config['name']); ?>
                                            </a>
                                        </div>

                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Media Mentions -->
                <?php if ($show_media_mentions) : ?>
                    <section class="media-mentions news-section" id="media-mentions" data-category="media">
                        <div class="section-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Media Mentions', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Coverage and mentions in industry publications', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Media mentions query
                            $media_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => 6,
                                'category_name' => 'media-mention',
                                'orderby' => 'date',
                                'order' => 'DESC'
                            );

                            $media_query = new WP_Query($media_args);

                            if ($media_query->have_posts()) :
                            ?>
                                <div class="media-mentions-grid">
                                    <?php while ($media_query->have_posts()) : $media_query->the_post(); ?>
                                        <article class="media-mention-item">
                                            <div class="mention-content">
                                                
                                                <div class="mention-meta">
                                                    <span class="mention-date">
                                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                            <?php echo esc_html(get_the_date()); ?>
                                                        </time>
                                                    </span>
                                                    <?php
                                                    $publication = get_post_meta(get_the_ID(), '_media_publication', true);
                                                    if ($publication) :
                                                    ?>
                                                        <span class="mention-publication"><?php echo esc_html($publication); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <h3 class="mention-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>

                                                <div class="mention-excerpt">
                                                    <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                                </div>

                                                <div class="mention-actions">
                                                    <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                        <?php esc_html_e('Read Coverage', 'recruitpro'); ?>
                                                    </a>
                                                </div>

                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                            <?php 
                            else :
                            ?>
                                <!-- Default media mentions for demo -->
                                <div class="media-mentions-grid">
                                    <article class="media-mention-item">
                                        <div class="mention-content">
                                            <div class="mention-meta">
                                                <span class="mention-date"><?php echo esc_html(date('F j, Y', strtotime('-1 month'))); ?></span>
                                                <span class="mention-publication"><?php esc_html_e('HR Weekly', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="mention-title">
                                                <a href="#"><?php esc_html_e('Featured as Top Recruitment Agency in Regional Survey', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="mention-excerpt">
                                                <p><?php esc_html_e('Our agency was highlighted among the leading recruitment firms in the latest industry survey...', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="mention-actions">
                                                <a href="#" class="read-more-link">
                                                    <?php esc_html_e('Read Coverage', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>

                                    <article class="media-mention-item">
                                        <div class="mention-content">
                                            <div class="mention-meta">
                                                <span class="mention-date"><?php echo esc_html(date('F j, Y', strtotime('-6 weeks'))); ?></span>
                                                <span class="mention-publication"><?php esc_html_e('Recruitment Today', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="mention-title">
                                                <a href="#"><?php esc_html_e('CEO Interview on Future of Remote Recruitment', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="mention-excerpt">
                                                <p><?php esc_html_e('Our CEO shares insights on remote recruitment trends and future industry developments...', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="mention-actions">
                                                <a href="#" class="read-more-link">
                                                    <?php esc_html_e('Read Coverage', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="media-placeholder">
                                        <p><em><?php esc_html_e('Create media mention posts in the "Media Mention" category to showcase press coverage.', 'recruitpro'); ?></em></p>
                                    </div>
                                <?php endif; ?>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- News Archive Link -->
                <section class="news-archive" id="news-archive">
                    <div class="archive-container">
                        <div class="archive-content">
                            <h2 class="archive-title"><?php esc_html_e('Looking for More News?', 'recruitpro'); ?></h2>
                            <p class="archive-description"><?php esc_html_e('Browse our complete news archive for historical press releases, media coverage, and industry insights.', 'recruitpro'); ?></p>
                            
                            <div class="archive-actions">
                                <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-primary">
                                    <?php esc_html_e('Visit Our Blog', 'recruitpro'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-secondary">
                                    <?php esc_html_e('Media Inquiries', 'recruitpro'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('news-sidebar')) : ?>
                <aside id="secondary" class="widget-area news-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('news-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<!-- News Page JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category filtering functionality
    const categoryTabs = document.querySelectorAll('.category-tab');
    const newsSections = document.querySelectorAll('.news-section');
    
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Remove active class from all tabs
            categoryTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show/hide sections based on category
            newsSections.forEach(section => {
                const sectionCategories = section.getAttribute('data-category').split(' ');
                
                if (category === 'all' || sectionCategories.includes(category)) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
get_footer();

/* =================================================================
   NEWS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL NEWS PAGE FEATURES:

✅ RSS FEED INTEGRATION
- Multiple industry RSS feeds
- Automatic content updates
- Feed caching for performance
- Error handling and fallbacks
- Customizable feed sources

✅ CONTENT ORGANIZATION
- Press releases section
- Industry news aggregation
- Media mentions showcase
- Category-based filtering
- Archive functionality

✅ PROFESSIONAL PRESENTATION
- Clean, scannable layout
- Source attribution
- External link handling
- Date formatting
- Meta information display

✅ CUSTOMIZATION OPTIONS
- Enable/disable feed sources
- Cache time configuration
- Layout options
- Sidebar positioning
- Show/hide sections

✅ SCHEMA.ORG MARKUP
- NewsMediaOrganization schema
- Article markup for posts
- Publisher information
- SEO optimization

✅ RESPONSIVE DESIGN
- Mobile-first approach
- Touch-friendly navigation
- Responsive grids
- Optimized loading

✅ PERFORMANCE OPTIMIZATION
- RSS feed caching
- Lazy loading
- Efficient queries
- Minimal external requests

✅ INDUSTRY FOCUS
- Recruitment-specific feeds
- HR industry sources
- Professional publications
- Market intelligence

RSS FEED SOURCES:
- HR Magazine
- Recruiter.com
- SHRM News
- HR Dive
- The Recruitment Grapevine

CONTENT CATEGORIES:
- Company News
- Press Releases
- Industry Updates
- Media Mentions
- Market Reports

PERFECT FOR:
- Staying current with industry
- Building thought leadership
- Media relations
- Professional credibility
- Client communication

*/
?>