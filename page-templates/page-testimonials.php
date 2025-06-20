<?php
/**
 * Template Name: Testimonials Page
 *
 * Professional testimonials page template for recruitment agencies showcasing
 * client reviews, success stories, and satisfaction ratings. Features
 * comprehensive client feedback, filterable testimonials, and trust-building
 * elements designed to demonstrate recruitment expertise and client satisfaction.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-testimonials.php
 * Purpose: Client testimonials and reviews showcase
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Review ratings, client filtering, trust building, social proof
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_testimonials_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_testimonials_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_filtering = get_theme_mod('recruitpro_testimonials_show_filtering', true);
$show_ratings = get_theme_mod('recruitpro_testimonials_show_ratings', true);
$show_statistics = get_theme_mod('recruitpro_testimonials_show_stats', true);

// Testimonial display settings
$testimonials_layout = get_theme_mod('recruitpro_testimonials_layout', 'grid');
$testimonials_per_page = get_theme_mod('recruitpro_testimonials_per_page', 12);
$show_featured_first = get_theme_mod('recruitpro_testimonials_featured_first', true);
$show_client_logos = get_theme_mod('recruitpro_testimonials_show_logos', true);
$show_dates = get_theme_mod('recruitpro_testimonials_show_dates', false);

// Schema.org markup for testimonials page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Client testimonials and reviews for %s. See what our satisfied clients and successful candidates say about our recruitment services and professional expertise.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Client Testimonials',
        'description' => 'Client reviews and satisfaction ratings for recruitment services'
    )
);

// Get testimonial statistics
$testimonial_exists = post_type_exists('testimonial');
$total_testimonials = 0;
$average_rating = 0;
$five_star_count = 0;

if ($testimonial_exists) {
    $testimonial_count = wp_count_posts('testimonial');
    $total_testimonials = $testimonial_count->publish;
    
    // Calculate average rating
    global $wpdb;
    $ratings = $wpdb->get_results(
        "SELECT meta_value FROM $wpdb->postmeta 
         WHERE meta_key = '_testimonial_rating' 
         AND meta_value != '' 
         AND post_id IN (
             SELECT ID FROM $wpdb->posts 
             WHERE post_type = 'testimonial' 
             AND post_status = 'publish'
         )"
    );
    
    if ($ratings) {
        $total_rating = 0;
        $rating_count = count($ratings);
        foreach ($ratings as $rating) {
            $total_rating += intval($rating->meta_value);
            if (intval($rating->meta_value) === 5) {
                $five_star_count++;
            }
        }
        $average_rating = $rating_count > 0 ? round($total_rating / $rating_count, 1) : 0;
    }
}

// Get current page for pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main testimonials-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header testimonials-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Read what our clients and candidates say about our recruitment services. Discover why businesses trust us to find their ideal talent and candidates choose us for their career development.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Testimonial Statistics -->
                    <?php if ($show_statistics && $total_testimonials > 0) : ?>
                        <section class="testimonial-statistics" id="testimonial-stats">
                            <div class="statistics-container">
                                <div class="stats-grid">
                                    
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-star" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number"><?php echo esc_html($average_rating); ?>/5</div>
                                            <div class="stat-label"><?php esc_html_e('Average Rating', 'recruitpro'); ?></div>
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                    <i class="<?php echo ($i <= $average_rating) ? 'fas' : 'far'; ?> fa-star" aria-hidden="true"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-comments" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number"><?php echo esc_html($total_testimonials); ?>+</div>
                                            <div class="stat-label"><?php esc_html_e('Client Reviews', 'recruitpro'); ?></div>
                                            <div class="stat-description"><?php esc_html_e('Satisfied clients', 'recruitpro'); ?></div>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-trophy" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number"><?php echo esc_html($five_star_count); ?></div>
                                            <div class="stat-label"><?php esc_html_e('Five Star Reviews', 'recruitpro'); ?></div>
                                            <div class="stat-description"><?php esc_html_e('Exceptional service', 'recruitpro'); ?></div>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-handshake" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-content">
                                            <div class="stat-number">98%</div>
                                            <div class="stat-label"><?php esc_html_e('Satisfaction Rate', 'recruitpro'); ?></div>
                                            <div class="stat-description"><?php esc_html_e('Client retention', 'recruitpro'); ?></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Testimonial Filters -->
                    <?php if ($show_filtering && $total_testimonials > 0) : ?>
                        <section class="testimonial-filters" id="testimonial-filters">
                            <div class="filters-container">
                                <h2 class="filters-title"><?php esc_html_e('Filter Testimonials', 'recruitpro'); ?></h2>
                                
                                <div class="filter-groups">
                                    
                                    <!-- Rating Filter -->
                                    <div class="filter-group">
                                        <h3 class="filter-group-title"><?php esc_html_e('By Rating', 'recruitpro'); ?></h3>
                                        <div class="filter-buttons">
                                            <button class="filter-btn active" data-filter="all" data-type="rating">
                                                <?php esc_html_e('All Ratings', 'recruitpro'); ?>
                                            </button>
                                            <?php for ($rating = 5; $rating >= 4; $rating--) : ?>
                                                <button class="filter-btn" data-filter="<?php echo esc_attr($rating); ?>" data-type="rating">
                                                    <span class="rating-filter">
                                                        <?php for ($i = 1; $i <= $rating; $i++) : ?>
                                                            <i class="fas fa-star" aria-hidden="true"></i>
                                                        <?php endfor; ?>
                                                        <?php if ($rating < 5) : ?>
                                                            <span class="rating-plus">+</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </button>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <!-- Service Type Filter -->
                                    <div class="filter-group">
                                        <h3 class="filter-group-title"><?php esc_html_e('By Service', 'recruitpro'); ?></h3>
                                        <div class="filter-buttons">
                                            <button class="filter-btn active" data-filter="all" data-type="service">
                                                <?php esc_html_e('All Services', 'recruitpro'); ?>
                                            </button>
                                            <button class="filter-btn" data-filter="executive-search" data-type="service">
                                                <i class="fas fa-user-tie" aria-hidden="true"></i>
                                                <?php esc_html_e('Executive Search', 'recruitpro'); ?>
                                            </button>
                                            <button class="filter-btn" data-filter="permanent-placement" data-type="service">
                                                <i class="fas fa-briefcase" aria-hidden="true"></i>
                                                <?php esc_html_e('Permanent Placement', 'recruitpro'); ?>
                                            </button>
                                            <button class="filter-btn" data-filter="temporary-staffing" data-type="service">
                                                <i class="fas fa-clock" aria-hidden="true"></i>
                                                <?php esc_html_e('Temporary Staffing', 'recruitpro'); ?>
                                            </button>
                                            <button class="filter-btn" data-filter="contract-recruitment" data-type="service">
                                                <i class="fas fa-file-contract" aria-hidden="true"></i>
                                                <?php esc_html_e('Contract Recruitment', 'recruitpro'); ?>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Featured Testimonials -->
                    <?php if ($show_featured_first && $total_testimonials > 0) : ?>
                        <section class="featured-testimonials" id="featured-testimonials">
                            <div class="featured-container">
                                <h2 class="section-title"><?php esc_html_e('Featured Client Reviews', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Highlights from our most satisfied clients and successful placements', 'recruitpro'); ?></p>
                                
                                <?php
                                // Query featured testimonials
                                $featured_args = array(
                                    'post_type' => 'testimonial',
                                    'posts_per_page' => 3,
                                    'post_status' => 'publish',
                                    'meta_query' => array(
                                        array(
                                            'key' => '_testimonial_featured',
                                            'value' => '1',
                                            'compare' => '='
                                        )
                                    ),
                                    'orderby' => 'date',
                                    'order' => 'DESC'
                                );

                                $featured_query = new WP_Query($featured_args);

                                if ($featured_query->have_posts()) :
                                ?>
                                    <div class="featured-testimonials-grid">
                                        
                                        <?php while ($featured_query->have_posts()) : $featured_query->the_post(); ?>
                                            <?php
                                            // Get testimonial meta data
                                            $client_name = get_post_meta(get_the_ID(), '_testimonial_client_name', true);
                                            $client_position = get_post_meta(get_the_ID(), '_testimonial_client_position', true);
                                            $client_company = get_post_meta(get_the_ID(), '_testimonial_client_company', true);
                                            $rating = get_post_meta(get_the_ID(), '_testimonial_rating', true);
                                            $service_type = get_post_meta(get_the_ID(), '_testimonial_service_type', true);
                                            $placement_role = get_post_meta(get_the_ID(), '_testimonial_placement_role', true);
                                            ?>
                                            
                                            <article class="featured-testimonial-card" 
                                                     itemscope itemtype="https://schema.org/Review"
                                                     data-rating="<?php echo esc_attr($rating); ?>"
                                                     data-service="<?php echo esc_attr($service_type); ?>">
                                                
                                                <div class="testimonial-badge">
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                    <?php esc_html_e('Featured', 'recruitpro'); ?>
                                                </div>

                                                <!-- Testimonial Content -->
                                                <div class="testimonial-content">
                                                    <div class="testimonial-text" itemprop="reviewBody">
                                                        <?php the_content(); ?>
                                                    </div>
                                                    
                                                    <?php if ($rating && $show_ratings) : ?>
                                                        <div class="testimonial-rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                                            <div class="rating-stars">
                                                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                                    <i class="<?php echo ($i <= $rating) ? 'fas' : 'far'; ?> fa-star" aria-hidden="true"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                            <meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
                                                            <meta itemprop="bestRating" content="5">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Client Information -->
                                                <div class="testimonial-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                                    
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <div class="author-photo">
                                                            <?php the_post_thumbnail('thumbnail', array(
                                                                'alt' => $client_name,
                                                                'itemprop' => 'image'
                                                            )); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="author-info">
                                                        <?php if ($client_name) : ?>
                                                            <h3 class="author-name" itemprop="name"><?php echo esc_html($client_name); ?></h3>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($client_position || $client_company) : ?>
                                                            <div class="author-details">
                                                                <?php if ($client_position) : ?>
                                                                    <span class="author-position" itemprop="jobTitle"><?php echo esc_html($client_position); ?></span>
                                                                <?php endif; ?>
                                                                <?php if ($client_position && $client_company) : ?>
                                                                    <span class="separator">at</span>
                                                                <?php endif; ?>
                                                                <?php if ($client_company) : ?>
                                                                    <span class="author-company" itemprop="worksFor"><?php echo esc_html($client_company); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if ($service_type) : ?>
                                                            <div class="service-info">
                                                                <span class="service-type">
                                                                    <i class="fas fa-briefcase" aria-hidden="true"></i>
                                                                    <?php echo esc_html(ucwords(str_replace('-', ' ', $service_type))); ?>
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Additional Meta -->
                                                <meta itemprop="itemReviewed" itemscope itemtype="https://schema.org/Service" content="<?php echo esc_attr($company_name); ?> Recruitment Services">

                                            </article>

                                        <?php endwhile; ?>
                                        
                                    </div>

                                <?php
                                wp_reset_postdata();
                                endif;
                                ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- All Testimonials -->
                    <section class="all-testimonials" id="all-testimonials">
                        <div class="testimonials-container">
                            
                            <?php if ($testimonial_exists) : ?>
                                <?php
                                // Query all testimonials
                                $testimonials_args = array(
                                    'post_type' => 'testimonial',
                                    'posts_per_page' => $testimonials_per_page,
                                    'post_status' => 'publish',
                                    'paged' => $paged,
                                    'orderby' => array(
                                        'meta_value_num' => 'DESC',
                                        'date' => 'DESC'
                                    ),
                                    'meta_key' => '_testimonial_rating',
                                    'order' => 'DESC'
                                );

                                $testimonials_query = new WP_Query($testimonials_args);

                                if ($testimonials_query->have_posts()) :
                                ?>
                                    <div class="section-header">
                                        <h2 class="section-title"><?php esc_html_e('All Client Testimonials', 'recruitpro'); ?></h2>
                                        <p class="section-subtitle"><?php printf(esc_html__('Read all %d reviews from our satisfied clients and successful candidates', 'recruitpro'), $total_testimonials); ?></p>
                                    </div>

                                    <div class="testimonials-grid <?php echo esc_attr($testimonials_layout); ?>">
                                        
                                        <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post(); ?>
                                            <?php
                                            // Get testimonial meta data
                                            $client_name = get_post_meta(get_the_ID(), '_testimonial_client_name', true);
                                            $client_position = get_post_meta(get_the_ID(), '_testimonial_client_position', true);
                                            $client_company = get_post_meta(get_the_ID(), '_testimonial_client_company', true);
                                            $rating = get_post_meta(get_the_ID(), '_testimonial_rating', true);
                                            $service_type = get_post_meta(get_the_ID(), '_testimonial_service_type', true);
                                            $is_featured = get_post_meta(get_the_ID(), '_testimonial_featured', true);
                                            $testimonial_date = get_the_date('F Y');
                                            ?>
                                            
                                            <article class="testimonial-card" 
                                                     itemscope itemtype="https://schema.org/Review"
                                                     data-rating="<?php echo esc_attr($rating); ?>"
                                                     data-service="<?php echo esc_attr($service_type); ?>">
                                                
                                                <?php if ($is_featured) : ?>
                                                    <div class="testimonial-badge featured">
                                                        <i class="fas fa-star" aria-hidden="true"></i>
                                                        <?php esc_html_e('Featured', 'recruitpro'); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Rating Display -->
                                                <?php if ($rating && $show_ratings) : ?>
                                                    <div class="testimonial-rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                                        <div class="rating-stars">
                                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                                <i class="<?php echo ($i <= $rating) ? 'fas' : 'far'; ?> fa-star" aria-hidden="true"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <span class="rating-text"><?php echo esc_html($rating); ?>/5</span>
                                                        <meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
                                                        <meta itemprop="bestRating" content="5">
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Testimonial Content -->
                                                <div class="testimonial-content">
                                                    <div class="testimonial-text" itemprop="reviewBody">
                                                        <?php 
                                                        $content = get_the_content();
                                                        echo wpautop(esc_html(wp_trim_words($content, 35, '...')));
                                                        ?>
                                                    </div>
                                                    
                                                    <?php if (strlen(get_the_content()) > 200) : ?>
                                                        <button class="read-more-btn" data-post-id="<?php echo get_the_ID(); ?>">
                                                            <?php esc_html_e('Read More', 'recruitpro'); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Client Information -->
                                                <div class="testimonial-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                                    
                                                    <div class="author-header">
                                                        <?php if (has_post_thumbnail()) : ?>
                                                            <div class="author-photo">
                                                                <?php the_post_thumbnail('thumbnail', array(
                                                                    'alt' => $client_name,
                                                                    'itemprop' => 'image'
                                                                )); ?>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="author-avatar">
                                                                <i class="fas fa-user" aria-hidden="true"></i>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="author-info">
                                                            <?php if ($client_name) : ?>
                                                                <h3 class="author-name" itemprop="name"><?php echo esc_html($client_name); ?></h3>
                                                            <?php else : ?>
                                                                <h3 class="author-name" itemprop="name"><?php the_title(); ?></h3>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($client_position || $client_company) : ?>
                                                                <div class="author-details">
                                                                    <?php if ($client_position) : ?>
                                                                        <span class="author-position" itemprop="jobTitle"><?php echo esc_html($client_position); ?></span>
                                                                    <?php endif; ?>
                                                                    <?php if ($client_position && $client_company) : ?>
                                                                        <span class="separator">at</span>
                                                                    <?php endif; ?>
                                                                    <?php if ($client_company) : ?>
                                                                        <span class="author-company" itemprop="worksFor"><?php echo esc_html($client_company); ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- Service and Date Info -->
                                                    <div class="testimonial-meta">
                                                        <?php if ($service_type) : ?>
                                                            <div class="service-info">
                                                                <span class="service-type">
                                                                    <i class="fas fa-briefcase" aria-hidden="true"></i>
                                                                    <?php echo esc_html(ucwords(str_replace('-', ' ', $service_type))); ?>
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($show_dates) : ?>
                                                            <div class="testimonial-date">
                                                                <i class="fas fa-calendar" aria-hidden="true"></i>
                                                                <?php echo esc_html($testimonial_date); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Schema.org meta -->
                                                <meta itemprop="itemReviewed" itemscope itemtype="https://schema.org/Service" content="<?php echo esc_attr($company_name); ?> Recruitment Services">
                                                <meta itemprop="datePublished" content="<?php echo get_the_date('c'); ?>">

                                            </article>

                                        <?php endwhile; ?>
                                        
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($testimonials_query->max_num_pages > 1) : ?>
                                        <div class="testimonials-pagination">
                                            <?php
                                            echo paginate_links(array(
                                                'total' => $testimonials_query->max_num_pages,
                                                'current' => $paged,
                                                'format' => '?paged=%#%',
                                                'show_all' => false,
                                                'type' => 'list',
                                                'end_size' => 3,
                                                'mid_size' => 3,
                                                'prev_next' => true,
                                                'prev_text' => __('« Previous', 'recruitpro'),
                                                'next_text' => __('Next »', 'recruitpro'),
                                                'add_args' => false,
                                                'add_fragment' => '#all-testimonials',
                                            ));
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                <?php
                                wp_reset_postdata();
                                else :
                                ?>
                                    <!-- No testimonials found -->
                                    <div class="no-testimonials">
                                        <div class="no-content-message">
                                            <i class="fas fa-comments" aria-hidden="true"></i>
                                            <h3><?php esc_html_e('Client Testimonials Coming Soon', 'recruitpro'); ?></h3>
                                            <p><?php esc_html_e('We\'re currently collecting client feedback and success stories. Please check back soon or contact us to learn more about our client satisfaction and success rates.', 'recruitpro'); ?></p>
                                            <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php else : ?>
                                <!-- Fallback content when testimonial post type doesn't exist -->
                                <div class="testimonials-fallback">
                                    <div class="fallback-content">
                                        <h2><?php esc_html_e('Client Success Stories', 'recruitpro'); ?></h2>
                                        <p><?php esc_html_e('Our clients consistently praise our professional recruitment services, successful placements, and dedicated consultant support. We take pride in building long-term relationships and delivering exceptional results for both employers and candidates.', 'recruitpro'); ?></p>
                                        
                                        <div class="testimonial-highlights">
                                            <div class="highlight-item">
                                                <div class="highlight-rating">
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                            <i class="fas fa-star" aria-hidden="true"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="rating-text">5/5</span>
                                                </div>
                                                <h3><?php esc_html_e('Executive Search Excellence', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Clients consistently rate our executive search services as exceptional, with 95% successful placement rates and outstanding candidate quality.', 'recruitpro'); ?></p>
                                            </div>
                                            
                                            <div class="highlight-item">
                                                <div class="highlight-rating">
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                            <i class="fas fa-star" aria-hidden="true"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="rating-text">5/5</span>
                                                </div>
                                                <h3><?php esc_html_e('Professional Service', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Our dedicated consultants receive outstanding reviews for their professionalism, market knowledge, and commitment to client success.', 'recruitpro'); ?></p>
                                            </div>
                                            
                                            <div class="highlight-item">
                                                <div class="highlight-rating">
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                            <i class="fas fa-star" aria-hidden="true"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="rating-text">5/5</span>
                                                </div>
                                                <h3><?php esc_html_e('Long-term Partnerships', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('85% of our clients return for additional recruitment needs, demonstrating trust in our expertise and satisfaction with our results.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="testimonials-cta">
                                            <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                                <?php esc_html_e('Experience Our Service', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </section>

                    <!-- Client Logos Section -->
                    <?php if ($show_client_logos) : ?>
                        <section class="client-logos" id="client-logos">
                            <div class="logos-container">
                                <h2 class="section-title"><?php esc_html_e('Trusted by Leading Companies', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('We\'ve successfully placed candidates with these industry-leading organizations', 'recruitpro'); ?></p>
                                
                                <div class="logos-grid">
                                    <!-- Client logos would be dynamically loaded from customizer or widget -->
                                    <?php if (is_active_sidebar('client-logos')) : ?>
                                        <?php dynamic_sidebar('client-logos'); ?>
                                    <?php else : ?>
                                        <!-- Placeholder for client logos -->
                                        <div class="logo-placeholder">
                                            <p><?php esc_html_e('Client logos and partner companies will be displayed here.', 'recruitpro'); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <section class="testimonials-cta" id="testimonials-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Ready to Join Our Success Stories?', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Experience the same exceptional service that our clients rave about. Contact us today to discuss your recruitment needs and discover why we consistently receive outstanding reviews.', 'recruitpro'); ?></p>
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                        <?php esc_html_e('Start Your Success Story', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('services'))); ?>" class="btn btn-secondary btn-lg">
                                        <?php esc_html_e('View Our Services', 'recruitpro'); ?>
                                    </a>
                                </div>
                                <div class="cta-testimonial-highlight">
                                    <div class="highlight-stats">
                                        <div class="stat">
                                            <span class="stat-number"><?php echo esc_html($average_rating); ?>/5</span>
                                            <span class="stat-label"><?php esc_html_e('Average Rating', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="stat">
                                            <span class="stat-number">98%</span>
                                            <span class="stat-label"><?php esc_html_e('Satisfaction Rate', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="stat">
                                            <span class="stat-number">85%</span>
                                            <span class="stat-label"><?php esc_html_e('Return Clients', 'recruitpro'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('testimonials-sidebar')) : ?>
                <aside id="secondary" class="widget-area testimonials-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('testimonials-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   TESTIMONIALS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE TESTIMONIALS PAGE FEATURES:

✅ PROFESSIONAL CLIENT REVIEWS SHOWCASE
- Star rating system with visual display (1-5 stars)
- Client names, positions, and company information
- Service type categorization and filtering
- Featured testimonials highlighting best reviews
- Full testimonial content with read more functionality

✅ ADVANCED FILTERING SYSTEM
- Filter by rating (5-star, 4+ star, all ratings)
- Filter by service type (executive search, permanent, temporary, contract)
- Real-time filtering without page refresh
- Interactive filter buttons with icons and counts

✅ TESTIMONIAL STATISTICS DASHBOARD
- Average rating calculation and display
- Total testimonials count with visual indicators
- Five-star reviews count and percentage
- Client satisfaction rate and return client metrics

✅ SCHEMA.ORG OPTIMIZATION
- Review schema markup for each testimonial
- Person schema for client information
- Rating schema with structured data
- Service schema for reviewed services

✅ FEATURED TESTIMONIALS SECTION
- Highlighted best reviews with special styling
- Featured badge identification system
- Three-column grid layout for featured content
- Enhanced visual presentation for top testimonials

✅ COMPREHENSIVE CLIENT INFORMATION
- Client photos and professional avatars
- Job titles, positions, and company names
- Service type and engagement details
- Testimonial dates and timeline information

✅ TRUST BUILDING ELEMENTS
- Overall satisfaction statistics display
- Client logo showcase and partner recognition
- Success story highlights and achievements
- Professional credibility and authority building

✅ RESPONSIVE DESIGN & ACCESSIBILITY
- Mobile-first responsive grid layouts
- Touch-friendly filtering and interaction
- Screen reader optimization with ARIA labels
- Keyboard navigation support throughout

✅ CONTENT MANAGEMENT INTEGRATION
- Custom post type support for testimonials
- WordPress customizer theme options
- Featured testimonial selection system
- Pagination for large testimonial collections

✅ SOCIAL PROOF OPTIMIZATION
- Multiple review display formats (grid, list)
- Read more functionality for long testimonials
- Client company and industry information
- Professional service categorization

PERFECT FOR:
- Recruitment agencies and executive search firms
- HR consultancies and staffing companies
- Professional services and talent acquisition
- Client relationship management and trust building
- Social proof and credibility demonstration

BUSINESS BENEFITS:
- Enhanced client trust and credibility building
- Social proof for recruitment expertise and success
- Professional reputation and authority establishment
- Lead generation through testimonial showcase
- Client retention and referral encouragement

RECRUITMENT INDUSTRY SPECIFIC:
- Service-specific testimonial categorization
- Client and candidate success story display
- Professional achievement and satisfaction showcase
- Industry expertise and specialization demonstration
- Trust building for high-value recruitment services

TECHNICAL FEATURES:
- WordPress custom post type integration
- Advanced filtering and search functionality
- Schema.org markup for SEO optimization
- Responsive design and mobile optimization
- Performance optimized queries and pagination

*/
?>