<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package RecruitPro
 * @since 1.0.0
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-wrapper">
            
            <?php if (is_home() && !is_front_page()) : ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                    <?php
                    $description = get_the_archive_description();
                    if ($description) :
                        echo '<div class="archive-description">' . wp_kses_post($description) . '</div>';
                    endif;
                    ?>
                </header>
            <?php endif; ?>

            <?php if (is_front_page()) : ?>
                <!-- Homepage Hero Section -->
                <?php if (is_active_sidebar('homepage-hero')) : ?>
                    <section class="hero-section">
                        <?php dynamic_sidebar('homepage-hero'); ?>
                    </section>
                <?php else : ?>
                    <!-- Default Hero Section -->
                    <section class="hero-section default-hero">
                        <div class="hero-content">
                            <h1 class="hero-title">
                                <?php 
                                $hero_title = get_theme_mod('recruitpro_hero_title', esc_html__('Find Your Perfect Career Match', 'recruitpro'));
                                echo esc_html($hero_title);
                                ?>
                            </h1>
                            <p class="hero-subtitle">
                                <?php 
                                $hero_subtitle = get_theme_mod('recruitpro_hero_subtitle', esc_html__('We connect talented professionals with leading companies across all industries.', 'recruitpro'));
                                echo esc_html($hero_subtitle);
                                ?>
                            </p>
                            <div class="hero-actions">
                                <a href="<?php echo esc_url(get_permalink(get_page_by_path('jobs'))); ?>" class="btn btn-primary btn-lg">
                                    <?php esc_html_e('Browse Jobs', 'recruitpro'); ?>
                                </a>
                                <a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>" class="btn btn-outline btn-lg">
                                    <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                                </a>
                            </div>
                        </div>
                        <?php if (get_theme_mod('recruitpro_hero_image')) : ?>
                            <div class="hero-image">
                                <img src="<?php echo esc_url(get_theme_mod('recruitpro_hero_image')); ?>" 
                                     alt="<?php esc_attr_e('Professional recruitment services', 'recruitpro'); ?>"
                                     loading="eager">
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

                <!-- Homepage Features Section -->
                <?php if (is_active_sidebar('homepage-features')) : ?>
                    <section class="features-section page-section">
                        <div class="section-header">
                            <h2 class="section-title">
                                <?php echo esc_html(get_theme_mod('recruitpro_features_title', esc_html__('Why Choose Our Recruitment Services', 'recruitpro'))); ?>
                            </h2>
                            <p class="section-subtitle">
                                <?php echo esc_html(get_theme_mod('recruitpro_features_subtitle', esc_html__('We provide comprehensive recruitment solutions tailored to your needs.', 'recruitpro'))); ?>
                            </p>
                        </div>
                        <?php dynamic_sidebar('homepage-features'); ?>
                    </section>
                <?php else : ?>
                    <!-- Default Features Section -->
                    <section class="features-section page-section">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Why Choose Our Recruitment Services', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('We provide comprehensive recruitment solutions tailored to your needs.', 'recruitpro'); ?></p>
                        </div>
                        <div class="features-grid grid grid-cols-1 md:grid-cols-3">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2L2 7V10C2 16 6 20.5 12 22C18 20.5 22 16 22 10V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h3 class="feature-title"><?php esc_html_e('Expert Matching', 'recruitpro'); ?></h3>
                                <p class="feature-description">
                                    <?php esc_html_e('Our experienced team uses advanced matching algorithms to find the perfect fit for both candidates and employers.', 'recruitpro'); ?>
                                </p>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 21V19C17 17.9 16.1 17 15 17H9C7.9 17 7 17.9 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h3 class="feature-title"><?php esc_html_e('Personalized Service', 'recruitpro'); ?></h3>
                                <p class="feature-description">
                                    <?php esc_html_e('We provide dedicated support throughout the entire recruitment process, ensuring the best experience for all parties.', 'recruitpro'); ?>
                                </p>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <h3 class="feature-title"><?php esc_html_e('Fast Results', 'recruitpro'); ?></h3>
                                <p class="feature-description">
                                    <?php esc_html_e('Our streamlined process and extensive network ensure quick turnaround times without compromising quality.', 'recruitpro'); ?>
                                </p>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Recent Jobs Section (if jobs plugin is active) -->
                <?php if (recruitpro_has_jobs_plugin()) : ?>
                    <section class="recent-jobs-section page-section">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Latest Job Opportunities', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Discover your next career move with our current openings.', 'recruitpro'); ?></p>
                        </div>
                        
                        <?php
                        // Query recent jobs
                        $recent_jobs = new WP_Query(array(
                            'post_type'      => 'job',
                            'posts_per_page' => 6,
                            'post_status'    => 'publish',
                            'meta_query'     => array(
                                array(
                                    'key'     => '_job_expires',
                                    'value'   => current_time('Y-m-d'),
                                    'compare' => '>=',
                                    'type'    => 'DATE'
                                )
                            )
                        ));

                        if ($recent_jobs->have_posts()) : ?>
                            <div class="jobs-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                <?php while ($recent_jobs->have_posts()) : $recent_jobs->the_post(); ?>
                                    <article class="job-card" itemscope itemtype="https://schema.org/JobPosting">
                                        <div class="job-header">
                                            <div class="job-info">
                                                <h3 class="job-title" itemprop="title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                <div class="job-company" itemprop="hiringOrganization" itemscope itemtype="https://schema.org/Organization">
                                                    <span itemprop="name"><?php echo esc_html(get_post_meta(get_the_ID(), '_company_name', true)); ?></span>
                                                </div>
                                                <div class="job-location" itemprop="jobLocation" itemscope itemtype="https://schema.org/Place">
                                                    <span itemprop="address"><?php echo esc_html(get_post_meta(get_the_ID(), '_job_location', true)); ?></span>
                                                </div>
                                            </div>
                                            <?php if (get_post_meta(get_the_ID(), '_job_salary', true)) : ?>
                                                <div class="job-salary" itemprop="baseSalary" itemscope itemtype="https://schema.org/MonetaryAmount">
                                                    <span itemprop="value"><?php echo esc_html(get_post_meta(get_the_ID(), '_job_salary', true)); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="job-meta">
                                            <?php if (get_post_meta(get_the_ID(), '_job_type', true)) : ?>
                                                <span class="job-type" itemprop="employmentType"><?php echo esc_html(get_post_meta(get_the_ID(), '_job_type', true)); ?></span>
                                            <?php endif; ?>
                                            <span class="job-posted"><?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'); ?></span>
                                        </div>
                                        
                                        <div class="job-excerpt" itemprop="description">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>
                                        
                                        <div class="job-actions">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php esc_html_e('View Details', 'recruitpro'); ?></a>
                                            <button class="btn btn-ghost apply-now-btn" data-job-id="<?php echo get_the_ID(); ?>"><?php esc_html_e('Quick Apply', 'recruitpro'); ?></button>
                                        </div>
                                        
                                        <!-- Schema markup -->
                                        <meta itemprop="datePosted" content="<?php echo get_the_date('c'); ?>">
                                        <meta itemprop="validThrough" content="<?php echo esc_attr(get_post_meta(get_the_ID(), '_job_expires', true)); ?>">
                                    </article>
                                <?php endwhile; ?>
                            </div>
                            
                            <div class="text-center" style="margin-top: 2rem;">
                                <a href="<?php echo esc_url(get_post_type_archive_link('job')); ?>" class="btn btn-outline btn-lg">
                                    <?php esc_html_e('View All Jobs', 'recruitpro'); ?>
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="no-jobs-message">
                                <p><?php esc_html_e('No job openings available at the moment. Please check back soon!', 'recruitpro'); ?></p>
                            </div>
                        <?php endif;
                        wp_reset_postdata();
                        ?>
                    </section>
                <?php endif; ?>

                <!-- Recent Blog Posts Section -->
                <section class="recent-posts-section page-section">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('Career Insights & News', 'recruitpro'); ?></h2>
                        <p class="section-subtitle"><?php esc_html_e('Stay updated with the latest recruitment trends and career advice.', 'recruitpro'); ?></p>
                    </div>
                    
                    <?php
                    // Query recent blog posts
                    $recent_posts = new WP_Query(array(
                        'post_type'      => 'post',
                        'posts_per_page' => 3,
                        'post_status'    => 'publish'
                    ));

                    if ($recent_posts->have_posts()) : ?>
                        <div class="posts-grid grid grid-cols-1 md:grid-cols-3">
                            <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                                <article class="post-card" itemscope itemtype="https://schema.org/BlogPosting">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('recruitpro-featured', array('itemprop' => 'image')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content">
                                        <h3 class="post-title" itemprop="headline">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        
                                        <div class="post-meta">
                                            <time class="post-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                                <?php echo get_the_date(); ?>
                                            </time>
                                            <span class="post-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                                <?php esc_html_e('by', 'recruitpro'); ?> <span itemprop="name"><?php the_author(); ?></span>
                                            </span>
                                        </div>
                                        
                                        <div class="post-excerpt" itemprop="description">
                                            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                        </div>
                                        
                                        <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More', 'recruitpro'); ?></a>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    <?php endif;
                    wp_reset_postdata();
                    ?>
                </section>

                <!-- CTA Section -->
                <?php if (is_active_sidebar('homepage-cta')) : ?>
                    <section class="cta-section">
                        <?php dynamic_sidebar('homepage-cta'); ?>
                    </section>
                <?php else : ?>
                    <!-- Default CTA Section -->
                    <section class="cta-section">
                        <div class="cta-content">
                            <h2 class="cta-title"><?php esc_html_e('Ready to Find Your Perfect Match?', 'recruitpro'); ?></h2>
                            <p class="cta-subtitle"><?php esc_html_e('Whether you\'re looking for talent or your next opportunity, we\'re here to help.', 'recruitpro'); ?></p>
                            <div class="cta-actions">
                                <a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                    <?php esc_html_e('Get Started Today', 'recruitpro'); ?>
                                </a>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

            <?php else : ?>
                <!-- Non-homepage content -->
                <div class="content-area">
                    <?php if (have_posts()) : ?>
                        
                        <?php if (is_home() && is_front_page()) : ?>
                            <header class="page-header">
                                <h1 class="page-title"><?php esc_html_e('Latest Posts', 'recruitpro'); ?></h1>
                            </header>
                        <?php endif; ?>

                        <div class="posts-container">
                            <?php while (have_posts()) : the_post(); ?>
                                
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?> itemscope itemtype="https://schema.org/BlogPosting">
                                    
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('recruitpro-featured', array('itemprop' => 'image')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content">
                                        <header class="entry-header">
                                            <h2 class="post-title" itemprop="headline">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            
                                            <div class="post-meta">
                                                <time class="post-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                                                    <?php echo get_the_date(); ?>
                                                </time>
                                                <span class="post-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                                    <?php esc_html_e('by', 'recruitpro'); ?> <span itemprop="name"><?php the_author(); ?></span>
                                                </span>
                                                <?php if (has_category()) : ?>
                                                    <span class="post-categories">
                                                        <?php esc_html_e('in', 'recruitpro'); ?> <?php the_category(', '); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </header>
                                        
                                        <div class="post-excerpt" itemprop="description">
                                            <?php the_excerpt(); ?>
                                        </div>
                                        
                                        <footer class="entry-footer">
                                            <a href="<?php the_permalink(); ?>" class="read-more btn btn-outline">
                                                <?php esc_html_e('Read Full Article', 'recruitpro'); ?>
                                            </a>
                                        </footer>
                                    </div>
                                </article>

                            <?php endwhile; ?>
                        </div>

                        <?php
                        // Pagination
                        the_posts_pagination(array(
                            'mid_size'  => 2,
                            'prev_text' => esc_html__('&laquo; Previous', 'recruitpro'),
                            'next_text' => esc_html__('Next &raquo;', 'recruitpro'),
                            'screen_reader_text' => esc_html__('Posts navigation', 'recruitpro'),
                        ));
                        ?>

                    <?php else : ?>
                        
                        <section class="no-results not-found">
                            <header class="page-header">
                                <h1 class="page-title"><?php esc_html_e('Nothing here', 'recruitpro'); ?></h1>
                            </header>

                            <div class="page-content">
                                <?php if (is_home() && current_user_can('publish_posts')) : ?>
                                    <p>
                                        <?php
                                        printf(
                                            wp_kses(
                                                __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'recruitpro'),
                                                array(
                                                    'a' => array(
                                                        'href' => array(),
                                                    ),
                                                )
                                            ),
                                            esc_url(admin_url('post-new.php'))
                                        );
                                        ?>
                                    </p>
                                <?php elseif (is_search()) : ?>
                                    <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'recruitpro'); ?></p>
                                    <?php get_search_form(); ?>
                                <?php else : ?>
                                    <p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'recruitpro'); ?></p>
                                    <?php get_search_form(); ?>
                                <?php endif; ?>
                            </div>
                        </section>

                    <?php endif; ?>
                </div><!-- .content-area -->

                <?php get_sidebar(); ?>
                
            <?php endif; ?>

        </div><!-- .content-wrapper -->
    </div><!-- .container -->
</main><!-- #primary -->

<?php
get_footer();