<?php
/**
 * Template Name: Homepage
 *
 * Professional homepage template for recruitment agencies and HR consultancies.
 * This template provides a comprehensive landing page designed to convert both
 * job seekers and employers. Features include hero section, services overview,
 * featured jobs, testimonials, company statistics, and strategic call-to-actions.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-home.php
 * Purpose: Homepage template for recruitment agency website
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Hero section, services, jobs, testimonials, stats, blog posts
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_tagline = get_theme_mod('recruitpro_company_tagline', get_bloginfo('description'));
$show_hero = get_theme_mod('recruitpro_home_show_hero', true);
$show_services = get_theme_mod('recruitpro_home_show_services', true);
$show_featured_jobs = get_theme_mod('recruitpro_home_show_jobs', true);
$show_statistics = get_theme_mod('recruitpro_home_show_stats', true);
$show_testimonials = get_theme_mod('recruitpro_home_show_testimonials', true);
$show_specializations = get_theme_mod('recruitpro_home_show_specializations', true);
$show_blog_posts = get_theme_mod('recruitpro_home_show_blog', true);
$show_newsletter = get_theme_mod('recruitpro_home_show_newsletter', true);
$show_cta_section = get_theme_mod('recruitpro_home_show_cta', true);

// Hero section content
$hero_title = get_theme_mod('recruitpro_hero_title', sprintf(__('Connect Talent with Opportunity at %s', 'recruitpro'), $company_name));
$hero_subtitle = get_theme_mod('recruitpro_hero_subtitle', __('Premier recruitment agency connecting exceptional candidates with leading employers across industries. Your next career move or perfect hire starts here.', 'recruitpro'));
$hero_background = get_theme_mod('recruitpro_hero_background', '');
$hero_cta_primary = get_theme_mod('recruitpro_hero_cta_primary', __('Find Your Dream Job', 'recruitpro'));
$hero_cta_secondary = get_theme_mod('recruitpro_hero_cta_secondary', __('Hire Top Talent', 'recruitpro'));

// Company statistics
$stats_placements = get_theme_mod('recruitpro_stats_placements', '2500+');
$stats_clients = get_theme_mod('recruitpro_stats_clients', '150+');
$stats_candidates = get_theme_mod('recruitpro_stats_candidates', '10,000+');
$stats_years = get_theme_mod('recruitpro_stats_years', '15+');

// Contact information
$contact_phone = get_theme_mod('recruitpro_contact_phone', '');
$contact_email = get_theme_mod('recruitpro_contact_email', get_option('admin_email'));

// Schema.org markup for homepage
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company_name,
    'description' => $company_tagline,
    'url' => home_url(),
    'logo' => get_theme_mod('recruitpro_site_logo', ''),
    'contactPoint' => array(
        '@type' => 'ContactPoint',
        'contactType' => 'customer service',
        'telephone' => $contact_phone,
        'email' => $contact_email
    ),
    'sameAs' => array_filter(array(
        get_theme_mod('recruitpro_social_linkedin', ''),
        get_theme_mod('recruitpro_social_facebook', ''),
        get_theme_mod('recruitpro_social_twitter', ''),
    ))
);
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<main id="primary" class="site-main homepage">

    <?php while (have_posts()) : the_post(); ?>

        <!-- Hero Section -->
        <?php if ($show_hero) : ?>
            <section class="hero-section" id="hero" <?php if ($hero_background) : ?>style="background-image: url(<?php echo esc_url($hero_background); ?>);"<?php endif; ?>>
                <div class="hero-overlay">
                    <div class="container">
                        <div class="hero-content">
                            
                            <div class="hero-text">
                                <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
                                <p class="hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
                                
                                <div class="hero-actions">
                                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-primary btn-large">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                        <?php echo esc_html($hero_cta_primary); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/services/')); ?>" class="btn btn-outline btn-large">
                                        <i class="fas fa-handshake" aria-hidden="true"></i>
                                        <?php echo esc_html($hero_cta_secondary); ?>
                                    </a>
                                </div>
                            </div>

                            <!-- Quick Job Search Form -->
                            <div class="hero-search">
                                <div class="search-form-container">
                                    <h3 class="search-title"><?php esc_html_e('Quick Job Search', 'recruitpro'); ?></h3>
                                    <form class="job-search-form" method="get" action="<?php echo esc_url(home_url('/jobs/')); ?>">
                                        <div class="search-fields">
                                            <div class="field-group">
                                                <label for="search-keywords" class="sr-only"><?php esc_html_e('Job Title or Keywords', 'recruitpro'); ?></label>
                                                <input type="text" 
                                                       id="search-keywords" 
                                                       name="keywords" 
                                                       class="search-input" 
                                                       placeholder="<?php esc_attr_e('Job title, company, or keywords', 'recruitpro'); ?>">
                                            </div>
                                            <div class="field-group">
                                                <label for="search-location" class="sr-only"><?php esc_html_e('Location', 'recruitpro'); ?></label>
                                                <input type="text" 
                                                       id="search-location" 
                                                       name="location" 
                                                       class="search-input" 
                                                       placeholder="<?php esc_attr_e('City, state, or remote', 'recruitpro'); ?>">
                                            </div>
                                            <div class="field-group">
                                                <button type="submit" class="btn btn-primary search-submit">
                                                    <i class="fas fa-search" aria-hidden="true"></i>
                                                    <?php esc_html_e('Search Jobs', 'recruitpro'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="search-suggestions">
                                        <span class="suggestions-label"><?php esc_html_e('Popular searches:', 'recruitpro'); ?></span>
                                        <a href="<?php echo esc_url(home_url('/jobs/?keywords=manager')); ?>" class="suggestion-tag">Manager</a>
                                        <a href="<?php echo esc_url(home_url('/jobs/?keywords=developer')); ?>" class="suggestion-tag">Developer</a>
                                        <a href="<?php echo esc_url(home_url('/jobs/?keywords=sales')); ?>" class="suggestion-tag">Sales</a>
                                        <a href="<?php echo esc_url(home_url('/jobs/?keywords=marketing')); ?>" class="suggestion-tag">Marketing</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Services Overview -->
        <?php if ($show_services) : ?>
            <section class="services-overview" id="services">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('Our Recruitment Services', 'recruitpro'); ?></h2>
                        <p class="section-subtitle"><?php esc_html_e('Comprehensive recruitment solutions for candidates and employers', 'recruitpro'); ?></p>
                    </div>

                    <div class="services-grid">
                        
                        <div class="service-item">
                            <div class="service-icon">
                                <i class="fas fa-user-tie" aria-hidden="true"></i>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Executive Search', 'recruitpro'); ?></h3>
                            <p class="service-description"><?php esc_html_e('Premium executive search and leadership recruitment for C-level and senior management positions across industries.', 'recruitpro'); ?></p>
                            <a href="<?php echo esc_url(home_url('/services/executive-search/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="service-item">
                            <div class="service-icon">
                                <i class="fas fa-users" aria-hidden="true"></i>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Permanent Placement', 'recruitpro'); ?></h3>
                            <p class="service-description"><?php esc_html_e('Full-time permanent recruitment across all levels, from entry-level to senior management positions.', 'recruitpro'); ?></p>
                            <a href="<?php echo esc_url(home_url('/services/permanent-placement/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="service-item">
                            <div class="service-icon">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Temporary Staffing', 'recruitpro'); ?></h3>
                            <p class="service-description"><?php esc_html_e('Flexible temporary and contract staffing solutions to meet your immediate workforce needs.', 'recruitpro'); ?></p>
                            <a href="<?php echo esc_url(home_url('/services/temporary-staffing/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="service-item">
                            <div class="service-icon">
                                <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Graduate Recruitment', 'recruitpro'); ?></h3>
                            <p class="service-description"><?php esc_html_e('Specialized graduate recruitment programs connecting emerging talent with career opportunities.', 'recruitpro'); ?></p>
                            <a href="<?php echo esc_url(home_url('/services/graduate-recruitment/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="service-item">
                            <div class="service-icon">
                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('HR Consulting', 'recruitpro'); ?></h3>
                            <p class="service-description"><?php esc_html_e('Strategic HR consulting services including talent strategy, workforce planning, and process optimization.', 'recruitpro'); ?></p>
                            <a href="<?php echo esc_url(home_url('/services/hr-consulting/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="service-item">
                            <div class="service-icon">
                                <i class="fas fa-handshake" aria-hidden="true"></i>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Career Coaching', 'recruitpro'); ?></h3>
                            <p class="service-description"><?php esc_html_e('Professional career coaching and guidance to help candidates achieve their career goals and potential.', 'recruitpro'); ?></p>
                            <a href="<?php echo esc_url(home_url('/services/career-coaching/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>

                    </div>

                    <div class="services-cta">
                        <a href="<?php echo esc_url(home_url('/services/')); ?>" class="btn btn-primary btn-large">
                            <?php esc_html_e('View All Services', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Company Statistics -->
        <?php if ($show_statistics) : ?>
            <section class="company-statistics" id="statistics">
                <div class="container">
                    <div class="statistics-content">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Our Track Record', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Numbers that speak to our recruitment success and client satisfaction', 'recruitpro'); ?></p>
                        </div>

                        <div class="stats-grid">
                            
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-trophy" aria-hidden="true"></i>
                                </div>
                                <div class="stat-number"><?php echo esc_html($stats_placements); ?></div>
                                <div class="stat-label"><?php esc_html_e('Successful Placements', 'recruitpro'); ?></div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-building" aria-hidden="true"></i>
                                </div>
                                <div class="stat-number"><?php echo esc_html($stats_clients); ?></div>
                                <div class="stat-label"><?php esc_html_e('Trusted Clients', 'recruitpro'); ?></div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-users" aria-hidden="true"></i>
                                </div>
                                <div class="stat-number"><?php echo esc_html($stats_candidates); ?></div>
                                <div class="stat-label"><?php esc_html_e('Registered Candidates', 'recruitpro'); ?></div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                </div>
                                <div class="stat-number"><?php echo esc_html($stats_years); ?></div>
                                <div class="stat-label"><?php esc_html_e('Years of Experience', 'recruitpro'); ?></div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Featured Jobs -->
        <?php if ($show_featured_jobs) : ?>
            <section class="featured-jobs" id="featured-jobs">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('Featured Opportunities', 'recruitpro'); ?></h2>
                        <p class="section-subtitle"><?php esc_html_e('Discover our latest job openings across various industries and locations', 'recruitpro'); ?></p>
                    </div>

                    <?php
                    // Query featured jobs (assuming job post type exists)
                    $featured_jobs = new WP_Query(array(
                        'post_type' => 'job',
                        'posts_per_page' => 6,
                        'meta_query' => array(
                            array(
                                'key' => 'featured_job',
                                'value' => '1',
                                'compare' => '='
                            )
                        )
                    ));

                    if ($featured_jobs->have_posts()) : ?>
                        <div class="jobs-grid">
                            <?php while ($featured_jobs->have_posts()) : $featured_jobs->the_post(); ?>
                                <?php
                                $job_location = get_post_meta(get_the_ID(), 'job_location', true);
                                $job_type = get_post_meta(get_the_ID(), 'job_type', true);
                                $job_salary = get_post_meta(get_the_ID(), 'job_salary', true);
                                $company_name = get_post_meta(get_the_ID(), 'company_name', true);
                                $job_remote = get_post_meta(get_the_ID(), 'job_remote', true);
                                ?>
                                <article class="job-item">
                                    <div class="job-content">
                                        
                                        <div class="job-header">
                                            <h3 class="job-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            <?php if ($company_name) : ?>
                                                <div class="job-company"><?php echo esc_html($company_name); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="job-meta">
                                            <?php if ($job_location) : ?>
                                                <span class="job-location">
                                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                    <?php echo esc_html($job_location); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if ($job_type) : ?>
                                                <span class="job-type">
                                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                                    <?php echo esc_html($job_type); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if ($job_salary) : ?>
                                                <span class="job-salary">
                                                    <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                                                    <?php echo esc_html($job_salary); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if ($job_remote) : ?>
                                                <span class="job-remote">
                                                    <i class="fas fa-home" aria-hidden="true"></i>
                                                    <?php esc_html_e('Remote Available', 'recruitpro'); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="job-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>

                                        <div class="job-actions">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Apply Now', 'recruitpro'); ?>
                                            </a>
                                            <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                                <?php esc_html_e('View Details', 'recruitpro'); ?>
                                            </a>
                                        </div>

                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-jobs">
                            <div class="no-jobs-content">
                                <i class="fas fa-briefcase" aria-hidden="true"></i>
                                <h3><?php esc_html_e('New Opportunities Coming Soon', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We\'re constantly adding new job opportunities. Register your interest to be notified of relevant positions.', 'recruitpro'); ?></p>
                                <a href="<?php echo esc_url(home_url('/register/')); ?>" class="btn btn-primary">
                                    <?php esc_html_e('Register Your Interest', 'recruitpro'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; 
                    wp_reset_postdata(); ?>

                    <div class="jobs-cta">
                        <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline btn-large">
                            <?php esc_html_e('View All Jobs', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Industry Specializations -->
        <?php if ($show_specializations) : ?>
            <section class="industry-specializations" id="specializations">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('Industry Expertise', 'recruitpro'); ?></h2>
                        <p class="section-subtitle"><?php esc_html_e('Deep specialization across key industries and sectors', 'recruitpro'); ?></p>
                    </div>

                    <div class="specializations-grid">
                        
                        <div class="specialization-item">
                            <div class="specialization-icon">
                                <i class="fas fa-laptop-code" aria-hidden="true"></i>
                            </div>
                            <h3 class="specialization-title"><?php esc_html_e('Technology', 'recruitpro'); ?></h3>
                            <p class="specialization-description"><?php esc_html_e('Software development, cybersecurity, data science, and IT infrastructure roles.', 'recruitpro'); ?></p>
                        </div>

                        <div class="specialization-item">
                            <div class="specialization-icon">
                                <i class="fas fa-heartbeat" aria-hidden="true"></i>
                            </div>
                            <h3 class="specialization-title"><?php esc_html_e('Healthcare', 'recruitpro'); ?></h3>
                            <p class="specialization-description"><?php esc_html_e('Medical professionals, healthcare administration, and pharmaceutical industry.', 'recruitpro'); ?></p>
                        </div>

                        <div class="specialization-item">
                            <div class="specialization-icon">
                                <i class="fas fa-chart-bar" aria-hidden="true"></i>
                            </div>
                            <h3 class="specialization-title"><?php esc_html_e('Finance', 'recruitpro'); ?></h3>
                            <p class="specialization-description"><?php esc_html_e('Banking, investment, accounting, and financial services professionals.', 'recruitpro'); ?></p>
                        </div>

                        <div class="specialization-item">
                            <div class="specialization-icon">
                                <i class="fas fa-cogs" aria-hidden="true"></i>
                            </div>
                            <h3 class="specialization-title"><?php esc_html_e('Engineering', 'recruitpro'); ?></h3>
                            <p class="specialization-description"><?php esc_html_e('Civil, mechanical, electrical, and software engineering positions.', 'recruitpro'); ?></p>
                        </div>

                        <div class="specialization-item">
                            <div class="specialization-icon">
                                <i class="fas fa-bullhorn" aria-hidden="true"></i>
                            </div>
                            <h3 class="specialization-title"><?php esc_html_e('Marketing & Sales', 'recruitpro'); ?></h3>
                            <p class="specialization-description"><?php esc_html_e('Digital marketing, sales leadership, and business development roles.', 'recruitpro'); ?></p>
                        </div>

                        <div class="specialization-item">
                            <div class="specialization-icon">
                                <i class="fas fa-gavel" aria-hidden="true"></i>
                            </div>
                            <h3 class="specialization-title"><?php esc_html_e('Legal', 'recruitpro'); ?></h3>
                            <p class="specialization-description"><?php esc_html_e('Corporate law, compliance, legal counsel, and paralegal positions.', 'recruitpro'); ?></p>
                        </div>

                    </div>

                    <div class="specializations-cta">
                        <a href="<?php echo esc_url(home_url('/industries/')); ?>" class="btn btn-outline">
                            <?php esc_html_e('Explore All Industries', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Client Testimonials -->
        <?php if ($show_testimonials) : ?>
            <section class="client-testimonials" id="testimonials">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('What Our Clients Say', 'recruitpro'); ?></h2>
                        <p class="section-subtitle"><?php esc_html_e('Real feedback from candidates and employers we\'ve successfully connected', 'recruitpro'); ?></p>
                    </div>

                    <div class="testimonials-grid">
                        
                        <!-- Employer Testimonial -->
                        <div class="testimonial-item employer-testimonial">
                            <div class="testimonial-content">
                                <div class="testimonial-quote">
                                    <i class="fas fa-quote-left" aria-hidden="true"></i>
                                    <p><?php echo esc_html(get_theme_mod('recruitpro_testimonial_1_content', 'Working with this recruitment agency transformed our hiring process. They delivered quality candidates who perfectly matched our company culture and requirements. Highly professional and efficient service.')); ?></p>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-info">
                                        <h4 class="author-name"><?php echo esc_html(get_theme_mod('recruitpro_testimonial_1_name', 'Sarah Johnson')); ?></h4>
                                        <p class="author-position"><?php echo esc_html(get_theme_mod('recruitpro_testimonial_1_position', 'HR Director, TechCorp Inc.')); ?></p>
                                    </div>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Candidate Testimonial -->
                        <div class="testimonial-item candidate-testimonial">
                            <div class="testimonial-content">
                                <div class="testimonial-quote">
                                    <i class="fas fa-quote-left" aria-hidden="true"></i>
                                    <p><?php echo esc_html(get_theme_mod('recruitpro_testimonial_2_content', 'They helped me land my dream job! The team was supportive throughout the entire process, from initial consultation to final negotiations. I couldn\'t have asked for better career guidance.')); ?></p>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-info">
                                        <h4 class="author-name"><?php echo esc_html(get_theme_mod('recruitpro_testimonial_2_name', 'Michael Chen')); ?></h4>
                                        <p class="author-position"><?php echo esc_html(get_theme_mod('recruitpro_testimonial_2_position', 'Software Engineer')); ?></p>
                                    </div>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Executive Testimonial -->
                        <div class="testimonial-item executive-testimonial">
                            <div class="testimonial-content">
                                <div class="testimonial-quote">
                                    <i class="fas fa-quote-left" aria-hidden="true"></i>
                                    <p><?php echo esc_html(get_theme_mod('recruitpro_testimonial_3_content', 'Outstanding executive search capabilities. They understood our complex requirements and delivered exceptional C-level candidates. Their industry knowledge and network are impressive.')); ?></p>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-info">
                                        <h4 class="author-name"><?php echo esc_html(get_theme_mod('recruitpro_testimonial_3_name', 'David Rodriguez')); ?></h4>
                                        <p class="author-position"><?php echo esc_html(get_theme_mod('recruitpro_testimonial_3_position', 'CEO, GlobalTech Solutions')); ?></p>
                                    </div>
                                    <div class="testimonial-rating">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="testimonials-cta">
                        <a href="<?php echo esc_url(home_url('/testimonials/')); ?>" class="btn btn-outline">
                            <?php esc_html_e('Read More Success Stories', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Latest Blog Posts -->
        <?php if ($show_blog_posts) : ?>
            <section class="latest-blog-posts" id="blog-posts">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('Industry Insights', 'recruitpro'); ?></h2>
                        <p class="section-subtitle"><?php esc_html_e('Stay informed with the latest recruitment trends, career advice, and industry news', 'recruitpro'); ?></p>
                    </div>

                    <?php
                    // Query latest blog posts
                    $blog_posts = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post_status' => 'publish'
                    ));

                    if ($blog_posts->have_posts()) : ?>
                        <div class="blog-posts-grid">
                            <?php while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
                                <article class="blog-post-item">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="post-content">
                                        <div class="post-meta">
                                            <span class="post-date">
                                                <i class="fas fa-calendar" aria-hidden="true"></i>
                                                <?php echo get_the_date('M j, Y'); ?>
                                            </span>
                                            <span class="post-category">
                                                <?php
                                                $categories = get_the_category();
                                                if ($categories) {
                                                    echo '<i class="fas fa-tag" aria-hidden="true"></i>' . esc_html($categories[0]->name);
                                                }
                                                ?>
                                            </span>
                                        </div>

                                        <h3 class="post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <div class="post-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>

                                        <div class="post-actions">
                                            <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                <?php esc_html_e('Read More', 'recruitpro'); ?>
                                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; 
                    wp_reset_postdata(); ?>

                    <div class="blog-cta">
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-outline">
                            <?php esc_html_e('Visit Our Blog', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Newsletter Signup -->
        <?php if ($show_newsletter) : ?>
            <section class="newsletter-signup" id="newsletter-signup">
                <div class="container">
                    <div class="newsletter-content">
                        <div class="newsletter-text">
                            <h2 class="newsletter-title"><?php esc_html_e('Stay in the Loop', 'recruitpro'); ?></h2>
                            <p class="newsletter-description"><?php esc_html_e('Subscribe to receive our latest news, event invitations, industry insights, and exclusive networking opportunities delivered to your inbox.', 'recruitpro'); ?></p>
                        </div>
                        
                        <form class="newsletter-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                            <div class="form-group">
                                <input type="email" 
                                       name="email" 
                                       id="newsletter-email" 
                                       class="form-control" 
                                       placeholder="<?php esc_attr_e('Enter your email address', 'recruitpro'); ?>" 
                                       required>
                                <button type="submit" class="btn btn-primary">
                                    <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                </button>
                            </div>
                            <div class="form-note">
                                <small><?php esc_html_e('We respect your privacy. Quality content only, no spam. Unsubscribe anytime.', 'recruitpro'); ?></small>
                            </div>
                            <input type="hidden" name="action" value="recruitpro_newsletter_signup">
                            <?php wp_nonce_field('recruitpro_newsletter_signup', 'nonce'); ?>
                        </form>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Call to Action Section -->
        <?php if ($show_cta_section) : ?>
            <section class="homepage-cta" id="homepage-cta">
                <div class="container">
                    <div class="cta-content">
                        <div class="cta-text">
                            <h2 class="cta-title"><?php esc_html_e('Ready to Take the Next Step?', 'recruitpro'); ?></h2>
                            <p class="cta-description"><?php esc_html_e('Whether you\'re looking for your next career opportunity or seeking top talent for your organization, we\'re here to help you succeed.', 'recruitpro'); ?></p>
                        </div>
                        
                        <div class="cta-actions">
                            <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-primary btn-large">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <?php esc_html_e('Browse Jobs', 'recruitpro'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline btn-large">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    <?php endwhile; ?>

</main>

<?php
get_footer();

/* =================================================================
   HOMEPAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE HOMEPAGE FEATURES:

✅ CONVERSION-OPTIMIZED HERO SECTION
- Compelling headline and value proposition
- Dual call-to-action buttons (candidates & employers)
- Quick job search form with suggestions
- Professional background image support
- Mobile-responsive design

✅ SERVICES OVERVIEW
- 6 core recruitment services displayed
- Executive search, permanent placement, temporary staffing
- Graduate recruitment, HR consulting, career coaching
- Professional icons and descriptions
- Direct links to service pages

✅ COMPANY CREDIBILITY
- Key statistics and achievements
- Successful placements counter
- Trusted clients showcase
- Years of experience display
- Professional presentation

✅ FEATURED JOB OPPORTUNITIES
- Dynamic job listings from WordPress
- Job meta information (location, type, salary)
- Company information display
- Remote work indicators
- Direct application links

✅ INDUSTRY SPECIALIZATIONS
- Technology, healthcare, finance sectors
- Engineering, marketing, legal expertise
- Professional industry icons
- Specialized service descriptions
- Industry page integration

✅ CLIENT TESTIMONIALS
- Employer and candidate testimonials
- Executive search success stories
- 5-star rating displays
- Professional author information
- Trust building elements

✅ LATEST BLOG CONTENT
- Recent industry insights
- Career advice articles
- Recruitment trend updates
- Category and date metadata
- Blog page integration

✅ UNIFIED NEWSLETTER SIGNUP
- Single subscription for all content types
- Privacy-compliant messaging
- AJAX form processing
- Professional presentation
- GDPR-friendly approach

✅ STRATEGIC CALL-TO-ACTION
- Dual-purpose CTA section
- Job seekers and employer paths
- Professional button styling
- Clear next steps guidance
- Conversion optimization

✅ SCHEMA.ORG OPTIMIZATION
- Organization markup
- Contact point information
- Social media integration
- SEO-friendly structure
- Rich snippet ready

✅ TECHNICAL FEATURES
- WordPress customizer integration
- Conditional section display
- Mobile-first responsive design
- Professional accessibility
- Performance optimized

PERFECT FOR:
- Recruitment agency homepages
- HR consultancy landing pages
- Executive search firm websites
- Staffing company presentations
- Professional service providers

CONVERSION ELEMENTS:
- Multiple call-to-action buttons
- Strategic contact information
- Trust signals throughout
- Professional testimonials
- Clear value propositions

RECRUITMENT INDUSTRY SPECIFIC:
- Job search functionality
- Candidate and employer paths
- Industry specialization display
- Professional service showcase
- Career-focused messaging

*/
?>