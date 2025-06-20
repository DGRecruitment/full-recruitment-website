<?php
/**
 * The template for displaying the front page
 *
 * Professional homepage template for recruitment agencies featuring hero section,
 * services showcase, testimonials, industry insights, and comprehensive CTAs.
 * Designed specifically for recruitment industry with focus on candidate attraction,
 * client engagement, and professional presentation of services and expertise.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/front-page.php
 * Purpose: Main homepage template for recruitment agencies
 * Context: Primary landing page for candidates and clients
 * Features: Hero section, services, testimonials, job previews, contact forms
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get customizer options
$hero_title = get_theme_mod('recruitpro_hero_title', __('Find Your Perfect Career Match', 'recruitpro'));
$hero_subtitle = get_theme_mod('recruitpro_hero_subtitle', __('Professional recruitment services connecting talented individuals with leading companies across industries.', 'recruitpro'));
$hero_cta_text = get_theme_mod('recruitpro_hero_cta_text', __('Explore Opportunities', 'recruitpro'));
$hero_cta_url = get_theme_mod('recruitpro_hero_cta_url', home_url('/jobs/'));
$hero_secondary_cta_text = get_theme_mod('recruitpro_hero_secondary_cta_text', __('For Employers', 'recruitpro'));
$hero_secondary_cta_url = get_theme_mod('recruitpro_hero_secondary_cta_url', home_url('/employers/'));

// Check if using page content or theme template
$use_page_content = get_theme_mod('recruitpro_front_page_content', 'template');

?>

<main id="primary" class="site-main front-page-main">
    
    <?php if ($use_page_content === 'page' && have_posts()) : ?>
        
        <!-- Page Content Mode -->
        <?php while (have_posts()) : the_post(); ?>
            <div class="page-content-wrapper">
                <div class="container">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endwhile; ?>
        
    <?php else : ?>
        
        <!-- Template Mode (Default) -->
        
        <!-- Hero Section -->
        <section class="hero-section recruitment-hero" id="hero">
            <div class="hero-background">
                <?php if (get_theme_mod('recruitpro_hero_background_image')) : ?>
                    <div class="hero-image" style="background-image: url('<?php echo esc_url(get_theme_mod('recruitpro_hero_background_image')); ?>')"></div>
                <?php endif; ?>
                <div class="hero-overlay"></div>
            </div>
            
            <div class="container">
                <div class="row align-items-center min-vh-80">
                    <div class="col-lg-7 col-md-8">
                        <div class="hero-content">
                            <h1 class="hero-title">
                                <?php echo wp_kses_post($hero_title); ?>
                            </h1>
                            
                            <p class="hero-subtitle">
                                <?php echo esc_html($hero_subtitle); ?>
                            </p>
                            
                            <div class="hero-actions">
                                <a href="<?php echo esc_url($hero_cta_url); ?>" class="btn btn-primary btn-lg hero-cta">
                                    <?php echo esc_html($hero_cta_text); ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="btn-icon">
                                        <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                    </svg>
                                </a>
                                
                                <a href="<?php echo esc_url($hero_secondary_cta_url); ?>" class="btn btn-outline btn-lg hero-secondary-cta">
                                    <?php echo esc_html($hero_secondary_cta_text); ?>
                                </a>
                            </div>
                            
                            <!-- Hero Stats -->
                            <div class="hero-stats">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo esc_html(get_theme_mod('recruitpro_stat_candidates', '5000+')); ?></span>
                                        <span class="stat-label"><?php esc_html_e('Candidates Placed', 'recruitpro'); ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo esc_html(get_theme_mod('recruitpro_stat_companies', '500+')); ?></span>
                                        <span class="stat-label"><?php esc_html_e('Partner Companies', 'recruitpro'); ?></span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number"><?php echo esc_html(get_theme_mod('recruitpro_stat_success_rate', '95%')); ?></span>
                                        <span class="stat-label"><?php esc_html_e('Success Rate', 'recruitpro'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-5 col-md-4">
                        <div class="hero-form-wrapper">
                            <!-- Quick Job Search Form -->
                            <div class="hero-search-form">
                                <h3 class="form-title"><?php esc_html_e('Find Your Next Role', 'recruitpro'); ?></h3>
                                <form class="job-search-form" method="get" action="<?php echo esc_url(home_url('/jobs/')); ?>">
                                    <div class="form-group">
                                        <label for="job-keywords" class="screen-reader-text"><?php esc_html_e('Job keywords', 'recruitpro'); ?></label>
                                        <input type="text" id="job-keywords" name="keywords" class="form-control" 
                                               placeholder="<?php esc_attr_e('Job title, keywords...', 'recruitpro'); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="job-location" class="screen-reader-text"><?php esc_html_e('Location', 'recruitpro'); ?></label>
                                        <input type="text" id="job-location" name="location" class="form-control" 
                                               placeholder="<?php esc_attr_e('City, state, country...', 'recruitpro'); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="job-category" class="screen-reader-text"><?php esc_html_e('Category', 'recruitpro'); ?></label>
                                        <select id="job-category" name="category" class="form-control">
                                            <option value=""><?php esc_html_e('All Categories', 'recruitpro'); ?></option>
                                            <option value="technology"><?php esc_html_e('Technology', 'recruitpro'); ?></option>
                                            <option value="healthcare"><?php esc_html_e('Healthcare', 'recruitpro'); ?></option>
                                            <option value="finance"><?php esc_html_e('Finance', 'recruitpro'); ?></option>
                                            <option value="marketing"><?php esc_html_e('Marketing', 'recruitpro'); ?></option>
                                            <option value="sales"><?php esc_html_e('Sales', 'recruitpro'); ?></option>
                                            <option value="operations"><?php esc_html_e('Operations', 'recruitpro'); ?></option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <?php esc_html_e('Search Jobs', 'recruitpro'); ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                        </svg>
                                    </button>
                                </form>
                                
                                <div class="quick-links">
                                    <span class="quick-links-label"><?php esc_html_e('Popular:', 'recruitpro'); ?></span>
                                    <a href="<?php echo esc_url(home_url('/jobs/?category=remote')); ?>" class="quick-link">
                                        <?php esc_html_e('Remote Jobs', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/jobs/?type=full-time')); ?>" class="quick-link">
                                        <?php esc_html_e('Full-time', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/jobs/?level=senior')); ?>" class="quick-link">
                                        <?php esc_html_e('Senior Level', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section" id="services">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title">
                        <?php echo esc_html(get_theme_mod('recruitpro_services_title', __('Our Recruitment Services', 'recruitpro'))); ?>
                    </h2>
                    <p class="section-subtitle">
                        <?php echo esc_html(get_theme_mod('recruitpro_services_subtitle', __('Comprehensive recruitment solutions tailored to your industry and needs.', 'recruitpro'))); ?>
                    </p>
                </div>
                
                <div class="row services-grid">
                    <!-- Permanent Placement -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A2.997 2.997 0 0 0 17.11 7H16.5c-.8 0-1.54.37-2.01.99l-1.04 1.38c-.17.23-.26.5-.26.78s.09.55.26.78l.83 1.1.07.11c.09.16.14.34.14.53 0 .61-.49 1.1-1.1 1.1-.31 0-.58-.16-.74-.42l-.83-1.1c-.17-.23-.26-.5-.26-.78s.09-.55.26-.78l1.04-1.38C12.96 9.37 13.7 9 14.5 9h.61c.46 0 .88-.21 1.16-.56L17.31 7H20v11z"/>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Permanent Placement', 'recruitpro'); ?></h3>
                            <p class="service-description">
                                <?php esc_html_e('Find the perfect permanent hires for your organization with our comprehensive screening and matching process.', 'recruitpro'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/services/permanent-placement/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Contract Staffing -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2zM20 19.5c0 .28-.22.5-.5.5h-15c-.28 0-.5-.22-.5-.5v-12h16v12z"/>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Contract Staffing', 'recruitpro'); ?></h3>
                            <p class="service-description">
                                <?php esc_html_e('Flexible contract and temporary staffing solutions to meet your project-based and seasonal requirements.', 'recruitpro'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/services/contract-staffing/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Executive Search -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Executive Search', 'recruitpro'); ?></h3>
                            <p class="service-description">
                                <?php esc_html_e('Specialized executive recruitment for C-level and senior management positions across all industries.', 'recruitpro'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/services/executive-search/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Industry Specialization -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Industry Expertise', 'recruitpro'); ?></h3>
                            <p class="service-description">
                                <?php esc_html_e('Deep industry knowledge and specialized recruitment across technology, healthcare, finance, and more.', 'recruitpro'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/industries/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Talent Consulting -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm-1 16H9V7h9v14z"/>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('Talent Consulting', 'recruitpro'); ?></h3>
                            <p class="service-description">
                                <?php esc_html_e('Strategic talent acquisition consulting to optimize your recruitment process and employer branding.', 'recruitpro'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/services/talent-consulting/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- RPO Services -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <h3 class="service-title"><?php esc_html_e('RPO Services', 'recruitpro'); ?></h3>
                            <p class="service-description">
                                <?php esc_html_e('Recruitment Process Outsourcing solutions to scale your hiring and reduce recruitment costs.', 'recruitpro'); ?>
                            </p>
                            <a href="<?php echo esc_url(home_url('/services/rpo/')); ?>" class="service-link">
                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="why-choose-section bg-light" id="why-choose-us">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="why-choose-content">
                            <h2 class="section-title">
                                <?php echo esc_html(get_theme_mod('recruitpro_why_choose_title', __('Why Choose Our Recruitment Agency?', 'recruitpro'))); ?>
                            </h2>
                            
                            <p class="section-description">
                                <?php echo esc_html(get_theme_mod('recruitpro_why_choose_description', __('With over a decade of recruitment excellence, we understand what it takes to match the right talent with the right opportunities.', 'recruitpro'))); ?>
                            </p>
                            
                            <div class="why-choose-features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                                        </svg>
                                    </div>
                                    <div class="feature-content">
                                        <h4><?php esc_html_e('Proven Track Record', 'recruitpro'); ?></h4>
                                        <p><?php esc_html_e('Over 10 years of successful placements across diverse industries and roles.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                                        </svg>
                                    </div>
                                    <div class="feature-content">
                                        <h4><?php esc_html_e('Industry Expertise', 'recruitpro'); ?></h4>
                                        <p><?php esc_html_e('Deep understanding of market trends and industry-specific requirements.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2z"/>
                                        </svg>
                                    </div>
                                    <div class="feature-content">
                                        <h4><?php esc_html_e('Personalized Service', 'recruitpro'); ?></h4>
                                        <p><?php esc_html_e('Tailored recruitment strategies that align with your unique needs and goals.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>
                                    <div class="feature-content">
                                        <h4><?php esc_html_e('Quality Guarantee', 'recruitpro'); ?></h4>
                                        <p><?php esc_html_e('Comprehensive screening process and placement guarantee for peace of mind.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="why-choose-image">
                            <?php if (get_theme_mod('recruitpro_why_choose_image')) : ?>
                                <img src="<?php echo esc_url(get_theme_mod('recruitpro_why_choose_image')); ?>" 
                                     alt="<?php esc_attr_e('Why choose our recruitment services', 'recruitpro'); ?>"
                                     class="img-fluid rounded">
                            <?php else : ?>
                                <div class="placeholder-image">
                                    <svg width="100" height="100" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                    <p><?php esc_html_e('Professional Recruitment Excellence', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Jobs Section -->
        <section class="latest-jobs-section" id="latest-jobs">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title">
                        <?php echo esc_html(get_theme_mod('recruitpro_jobs_title', __('Latest Job Opportunities', 'recruitpro'))); ?>
                    </h2>
                    <p class="section-subtitle">
                        <?php echo esc_html(get_theme_mod('recruitpro_jobs_subtitle', __('Discover exciting career opportunities from our trusted partners.', 'recruitpro'))); ?>
                    </p>
                </div>
                
                <div class="jobs-container">
                    <?php
                    // Get latest jobs (this will work when jobs plugin is active)
                    $latest_jobs = get_posts(array(
                        'post_type'      => 'job',
                        'posts_per_page' => 6,
                        'post_status'    => 'publish',
                        'meta_query'     => array(
                            array(
                                'key'     => '_job_status',
                                'value'   => 'active',
                                'compare' => '='
                            )
                        )
                    ));
                    
                    if (!empty($latest_jobs)) :
                        echo '<div class="row jobs-grid">';
                        foreach ($latest_jobs as $job) :
                            setup_postdata($job);
                            ?>
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="job-card">
                                    <div class="job-header">
                                        <h3 class="job-title">
                                            <a href="<?php echo esc_url(get_permalink($job->ID)); ?>">
                                                <?php echo esc_html(get_the_title($job->ID)); ?>
                                            </a>
                                        </h3>
                                        <span class="job-type"><?php echo esc_html(get_post_meta($job->ID, '_job_type', true)); ?></span>
                                    </div>
                                    
                                    <div class="job-meta">
                                        <span class="job-company"><?php echo esc_html(get_post_meta($job->ID, '_company_name', true)); ?></span>
                                        <span class="job-location"><?php echo esc_html(get_post_meta($job->ID, '_job_location', true)); ?></span>
                                        <span class="job-salary"><?php echo esc_html(get_post_meta($job->ID, '_job_salary', true)); ?></span>
                                    </div>
                                    
                                    <div class="job-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt($job->ID), 20); ?>
                                    </div>
                                    
                                    <div class="job-footer">
                                        <span class="job-posted"><?php echo esc_html(get_the_date('', $job->ID)); ?></span>
                                        <a href="<?php echo esc_url(get_permalink($job->ID)); ?>" class="job-apply-btn">
                                            <?php esc_html_e('View Details', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endforeach;
                        echo '</div>';
                        wp_reset_postdata();
                    else :
                        // Fallback when no jobs plugin or no jobs available
                        ?>
                        <div class="no-jobs-placeholder">
                            <div class="text-center">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor" class="placeholder-icon">
                                    <path d="M20 6h-2.5l-1.4-1.4C15.9 4.3 15.6 4 15.3 4H8.7c-.3 0-.6.3-.8.6L6.5 6H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM14 13h-4v-2h4v2z"/>
                                </svg>
                                <h3><?php esc_html_e('Jobs Coming Soon', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We are currently updating our job listings. Please check back soon or contact us directly for available positions.', 'recruitpro'); ?></p>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary">
                                    <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                                </a>
                            </div>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline btn-lg">
                        <?php esc_html_e('View All Jobs', 'recruitpro'); ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section bg-light" id="testimonials">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title">
                        <?php echo esc_html(get_theme_mod('recruitpro_testimonials_title', __('Client Success Stories', 'recruitpro'))); ?>
                    </h2>
                    <p class="section-subtitle">
                        <?php echo esc_html(get_theme_mod('recruitpro_testimonials_subtitle', __('Hear from candidates and companies who found success through our services.', 'recruitpro'))); ?>
                    </p>
                </div>
                
                <div class="testimonials-slider" id="testimonials-slider">
                    <div class="row">
                        <!-- Testimonial 1 -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <div class="quote-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                                        </svg>
                                    </div>
                                    <blockquote class="testimonial-text">
                                        "<?php echo esc_html(get_theme_mod('recruitpro_testimonial_1_text', __('The team found me the perfect role that aligned with my career goals. Their professionalism and industry knowledge made all the difference.', 'recruitpro'))); ?>"
                                    </blockquote>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-avatar">
                                        <?php if (get_theme_mod('recruitpro_testimonial_1_image')) : ?>
                                            <img src="<?php echo esc_url(get_theme_mod('recruitpro_testimonial_1_image')); ?>" 
                                                 alt="<?php echo esc_attr(get_theme_mod('recruitpro_testimonial_1_name', 'Client')); ?>">
                                        <?php else : ?>
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="author-info">
                                        <cite class="author-name">
                                            <?php echo esc_html(get_theme_mod('recruitpro_testimonial_1_name', __('Sarah Johnson', 'recruitpro'))); ?>
                                        </cite>
                                        <span class="author-role">
                                            <?php echo esc_html(get_theme_mod('recruitpro_testimonial_1_role', __('Marketing Manager', 'recruitpro'))); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 2 -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <div class="quote-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                                        </svg>
                                    </div>
                                    <blockquote class="testimonial-text">
                                        "<?php echo esc_html(get_theme_mod('recruitpro_testimonial_2_text', __('Excellent service from start to finish. They understood our company culture and delivered candidates who were a perfect fit.', 'recruitpro'))); ?>"
                                    </blockquote>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-avatar">
                                        <?php if (get_theme_mod('recruitpro_testimonial_2_image')) : ?>
                                            <img src="<?php echo esc_url(get_theme_mod('recruitpro_testimonial_2_image')); ?>" 
                                                 alt="<?php echo esc_attr(get_theme_mod('recruitpro_testimonial_2_name', 'Client')); ?>">
                                        <?php else : ?>
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="author-info">
                                        <cite class="author-name">
                                            <?php echo esc_html(get_theme_mod('recruitpro_testimonial_2_name', __('Michael Chen', 'recruitpro'))); ?>
                                        </cite>
                                        <span class="author-role">
                                            <?php echo esc_html(get_theme_mod('recruitpro_testimonial_2_role', __('HR Director', 'recruitpro'))); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 3 -->
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <div class="quote-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                                        </svg>
                                    </div>
                                    <blockquote class="testimonial-text">
                                        "<?php echo esc_html(get_theme_mod('recruitpro_testimonial_3_text', __('Professional, responsive, and results-driven. They helped us build an amazing team that has transformed our business.', 'recruitpro'))); ?>"
                                    </blockquote>
                                </div>
                                <div class="testimonial-author">
                                    <div class="author-avatar">
                                        <?php if (get_theme_mod('recruitpro_testimonial_3_image')) : ?>
                                            <img src="<?php echo esc_url(get_theme_mod('recruitpro_testimonial_3_image')); ?>" 
                                                 alt="<?php echo esc_attr(get_theme_mod('recruitpro_testimonial_3_name', 'Client')); ?>">
                                        <?php else : ?>
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="author-info">
                                        <cite class="author-name">
                                            <?php echo esc_html(get_theme_mod('recruitpro_testimonial_3_name', __('Emily Rodriguez', 'recruitpro'))); ?>
                                        </cite>
                                        <span class="author-role">
                                            <?php echo esc_html(get_theme_mod('recruitpro_testimonial_3_role', __('CEO', 'recruitpro'))); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Blog Posts Section -->
        <section class="blog-section" id="blog">
            <div class="container">
                <div class="section-header text-center">
                    <h2 class="section-title">
                        <?php echo esc_html(get_theme_mod('recruitpro_blog_title', __('Industry Insights & Career Advice', 'recruitpro'))); ?>
                    </h2>
                    <p class="section-subtitle">
                        <?php echo esc_html(get_theme_mod('recruitpro_blog_subtitle', __('Stay updated with the latest recruitment trends and career development tips.', 'recruitpro'))); ?>
                    </p>
                </div>
                
                <div class="row blog-grid">
                    <?php
                    // Get latest blog posts
                    $latest_posts = get_posts(array(
                        'numberposts' => 3,
                        'post_status' => 'publish',
                        'orderby'     => 'date',
                        'order'       => 'DESC',
                    ));
                    
                    if (!empty($latest_posts)) :
                        foreach ($latest_posts as $post) :
                            setup_postdata($post);
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <article class="blog-card">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="blog-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                            </a>
                                            <div class="blog-category">
                                                <?php
                                                $categories = get_the_category();
                                                if (!empty($categories)) {
                                                    echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">';
                                                    echo esc_html($categories[0]->name);
                                                    echo '</a>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="blog-content">
                                        <header class="blog-header">
                                            <h3 class="blog-title">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h3>
                                            
                                            <div class="blog-meta">
                                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                    <?php echo esc_html(get_the_date()); ?>
                                                </time>
                                                <span class="reading-time">
                                                    <?php echo esc_html(recruitpro_get_reading_time()); ?> 
                                                    <?php esc_html_e('min read', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                        </header>
                                        
                                        <div class="blog-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>
                                        
                                        <footer class="blog-footer">
                                            <a href="<?php the_permalink(); ?>" class="read-more">
                                                <?php esc_html_e('Read More', 'recruitpro'); ?>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                </svg>
                                            </a>
                                        </footer>
                                    </div>
                                </article>
                            </div>
                            <?php
                        endforeach;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="col-12">
                            <div class="no-posts text-center">
                                <p><?php esc_html_e('No blog posts available at the moment.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                        <?php
                    endif;
                    ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-outline btn-lg">
                        <?php esc_html_e('View All Articles', 'recruitpro'); ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section bg-primary text-white" id="cta">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="cta-content">
                            <h2 class="cta-title">
                                <?php echo esc_html(get_theme_mod('recruitpro_cta_title', __('Ready to Take the Next Step?', 'recruitpro'))); ?>
                            </h2>
                            <p class="cta-description">
                                <?php echo esc_html(get_theme_mod('recruitpro_cta_description', __('Whether you\'re looking for your next career opportunity or need to hire top talent, we\'re here to help you succeed.', 'recruitpro'))); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="cta-buttons">
                            <a href="<?php echo esc_url(get_theme_mod('recruitpro_cta_primary_url', home_url('/contact/'))); ?>" 
                               class="btn btn-white btn-lg">
                                <?php echo esc_html(get_theme_mod('recruitpro_cta_primary_text', __('Get Started Today', 'recruitpro'))); ?>
                            </a>
                            <a href="<?php echo esc_url(get_theme_mod('recruitpro_cta_secondary_url', home_url('/about/'))); ?>" 
                               class="btn btn-outline-white btn-lg">
                                <?php echo esc_html(get_theme_mod('recruitpro_cta_secondary_text', __('Learn More', 'recruitpro'))); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <?php endif; ?>

</main>

<?php
get_footer();

/**
 * Helper function to get reading time
 */
if (!function_exists('recruitpro_get_reading_time')) {
    function recruitpro_get_reading_time() {
        $content = get_the_content();
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // 200 words per minute
        return max(1, $reading_time);
    }
}
?>