<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * Professional 404 error page template designed specifically for recruitment agencies.
 * Features helpful navigation, search functionality, popular content suggestions,
 * and conversion-focused CTAs to guide visitors back to relevant content and services.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/404.php
 * Purpose: 404 error page handling with user-friendly recovery options
 * Dependencies: WordPress core, theme functions
 * Features: Search suggestions, popular content, contact options
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get customizer settings
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_search = get_theme_mod('recruitpro_404_show_search', true);
$show_popular_content = get_theme_mod('recruitpro_404_show_popular', true);
$show_contact_info = get_theme_mod('recruitpro_404_show_contact', true);
$show_recent_jobs = get_theme_mod('recruitpro_404_show_recent_jobs', true);
$custom_404_message = get_theme_mod('recruitpro_404_custom_message', '');
$custom_404_title = get_theme_mod('recruitpro_404_custom_title', '');

// Log 404 errors for analytics
if (function_exists('recruitpro_log_404_error')) {
    recruitpro_log_404_error($_SERVER['REQUEST_URI']);
}

// Schema.org markup for 404 page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '404 - Page Not Found',
    'description' => sprintf(__('The requested page could not be found on %s. Use our search or navigation to find what you\'re looking for.', 'recruitpro'), $company_name),
    'url' => home_url($_SERVER['REQUEST_URI']),
    'mainEntity' => array(
        '@type' => 'Thing',
        'name' => '404 Error',
        'description' => 'Page not found error'
    ),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    )
);

// Get popular content for suggestions
$popular_pages = array(
    array(
        'title' => __('Job Opportunities', 'recruitpro'),
        'url' => home_url('/jobs/'),
        'description' => __('Browse current job openings and career opportunities', 'recruitpro'),
        'icon' => 'fas fa-briefcase'
    ),
    array(
        'title' => __('Our Services', 'recruitpro'),
        'url' => home_url('/services/'),
        'description' => __('Learn about our recruitment and staffing services', 'recruitpro'),
        'icon' => 'fas fa-handshake'
    ),
    array(
        'title' => __('Contact Us', 'recruitpro'),
        'url' => home_url('/contact/'),
        'description' => __('Get in touch with our recruitment team', 'recruitpro'),
        'icon' => 'fas fa-phone'
    ),
    array(
        'title' => __('About Us', 'recruitpro'),
        'url' => home_url('/about/'),
        'description' => __('Discover our company story and expertise', 'recruitpro'),
        'icon' => 'fas fa-users'
    ),
    array(
        'title' => __('Career Advice', 'recruitpro'),
        'url' => home_url('/blog/'),
        'description' => __('Read career tips and industry insights', 'recruitpro'),
        'icon' => 'fas fa-lightbulb'
    ),
    array(
        'title' => __('Upload Resume', 'recruitpro'),
        'url' => home_url('/upload-resume/'),
        'description' => __('Submit your CV for future opportunities', 'recruitpro'),
        'icon' => 'fas fa-upload'
    )
);

// Get recent blog posts if available
$recent_posts = get_posts(array(
    'numberposts' => 3,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
));

// Get recent jobs if jobs plugin is active
$recent_jobs = array();
if (class_exists('RecruitPro_Jobs') || post_type_exists('job')) {
    $recent_jobs = get_posts(array(
        'post_type' => 'job',
        'numberposts' => 3,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'job_status',
                'value' => 'active',
                'compare' => '='
            )
        )
    ));
}
?>

<div id="primary" class="content-area error-404-page">
    <main id="main" class="site-main">
        
        <div class="container">
            <div class="error-404-wrapper">
                
                <!-- 404 Hero Section -->
                <section class="error-404-hero">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="error-404-content">
                                <div class="error-code">404</div>
                                
                                <h1 class="error-title">
                                    <?php 
                                    if ($custom_404_title) {
                                        echo esc_html($custom_404_title);
                                    } else {
                                        esc_html_e('Oops! Page Not Found', 'recruitpro');
                                    }
                                    ?>
                                </h1>
                                
                                <div class="error-message">
                                    <?php 
                                    if ($custom_404_message) {
                                        echo wp_kses_post($custom_404_message);
                                    } else {
                                        printf(
                                            esc_html__('The page you\'re looking for doesn\'t exist or may have been moved. Don\'t worry - %s is here to help you find what you need!', 'recruitpro'),
                                            '<strong>' . esc_html($company_name) . '</strong>'
                                        );
                                    }
                                    ?>
                                </div>
                                
                                <div class="error-actions">
                                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-home"></i>
                                        <?php esc_html_e('Go to Homepage', 'recruitpro'); ?>
                                    </a>
                                    
                                    <button class="btn btn-secondary btn-lg" onclick="window.history.back();">
                                        <i class="fas fa-arrow-left"></i>
                                        <?php esc_html_e('Go Back', 'recruitpro'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="error-404-illustration">
                                <div class="illustration-wrapper">
                                    <!-- SVG Illustration -->
                                    <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" class="error-svg">
                                        <!-- Background -->
                                        <rect width="400" height="300" fill="none"/>
                                        
                                        <!-- Desk -->
                                        <rect x="50" y="200" width="300" height="80" rx="10" fill="#8B4513" opacity="0.8"/>
                                        
                                        <!-- Computer Monitor -->
                                        <rect x="150" y="120" width="100" height="80" rx="5" fill="#2C3E50"/>
                                        <rect x="155" y="125" width="90" height="60" rx="2" fill="#34495E"/>
                                        <text x="200" y="155" font-family="Arial" font-size="20" font-weight="bold" fill="#E74C3C" text-anchor="middle">404</text>
                                        
                                        <!-- Monitor Stand -->
                                        <rect x="190" y="200" width="20" height="20" fill="#2C3E50"/>
                                        <rect x="170" y="220" width="60" height="10" rx="5" fill="#2C3E50"/>
                                        
                                        <!-- Papers scattered -->
                                        <rect x="80" y="180" width="40" height="30" rx="2" fill="#ECF0F1" transform="rotate(-15 100 195)"/>
                                        <rect x="280" y="185" width="35" height="25" rx="2" fill="#ECF0F1" transform="rotate(10 297 197)"/>
                                        
                                        <!-- Coffee mug -->
                                        <ellipse cx="320" cy="170" rx="15" ry="20" fill="#8B4513"/>
                                        <ellipse cx="320" cy="165" rx="12" ry="3" fill="#D2691E"/>
                                        <path d="M 335 165 Q 345 165 345 175 Q 345 185 335 185" stroke="#8B4513" stroke-width="3" fill="none"/>
                                        
                                        <!-- Magnifying glass -->
                                        <circle cx="120" cy="250" r="25" fill="none" stroke="#3498DB" stroke-width="4"/>
                                        <line x1="140" y1="270" x2="160" y2="290" stroke="#3498DB" stroke-width="4" stroke-linecap="round"/>
                                        
                                        <!-- Question marks floating -->
                                        <text x="300" y="80" font-family="Arial" font-size="30" fill="#95A5A6" opacity="0.6">?</text>
                                        <text x="100" y="100" font-family="Arial" font-size="25" fill="#95A5A6" opacity="0.4">?</text>
                                        <text x="350" y="120" font-family="Arial" font-size="20" fill="#95A5A6" opacity="0.5">?</text>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Search Section -->
                <?php if ($show_search) : ?>
                    <section class="error-search-section">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="search-wrapper">
                                    <h2 class="section-title"><?php esc_html_e('Search Our Website', 'recruitpro'); ?></h2>
                                    <p class="section-description">
                                        <?php esc_html_e('Try searching for what you were looking for using the search box below.', 'recruitpro'); ?>
                                    </p>
                                    
                                    <form role="search" method="get" class="search-form error-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                                        <div class="search-input-wrapper">
                                            <input type="search" 
                                                   class="search-field" 
                                                   placeholder="<?php esc_attr_e('Search for jobs, services, or information...', 'recruitpro'); ?>" 
                                                   value="<?php echo get_search_query(); ?>" 
                                                   name="s" 
                                                   autocomplete="off"
                                                   required />
                                            <button type="submit" class="search-submit">
                                                <i class="fas fa-search"></i>
                                                <span class="sr-only"><?php esc_html_e('Search', 'recruitpro'); ?></span>
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Popular search terms -->
                                    <div class="popular-searches">
                                        <span class="popular-label"><?php esc_html_e('Popular searches:', 'recruitpro'); ?></span>
                                        <a href="<?php echo esc_url(add_query_arg('s', 'jobs', home_url('/'))); ?>" class="search-tag">
                                            <?php esc_html_e('Jobs', 'recruitpro'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('s', 'careers', home_url('/'))); ?>" class="search-tag">
                                            <?php esc_html_e('Careers', 'recruitpro'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('s', 'recruitment', home_url('/'))); ?>" class="search-tag">
                                            <?php esc_html_e('Recruitment', 'recruitpro'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(add_query_arg('s', 'services', home_url('/'))); ?>" class="search-tag">
                                            <?php esc_html_e('Services', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Popular Pages Section -->
                <?php if ($show_popular_content && $popular_pages) : ?>
                    <section class="popular-pages-section">
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Popular Pages', 'recruitpro'); ?></h2>
                            <p class="section-description">
                                <?php esc_html_e('Here are some of our most visited pages that might help you find what you\'re looking for.', 'recruitpro'); ?>
                            </p>
                        </div>
                        
                        <div class="popular-pages-grid">
                            <?php foreach ($popular_pages as $page) : ?>
                                <div class="popular-page-card">
                                    <div class="page-icon">
                                        <i class="<?php echo esc_attr($page['icon']); ?>"></i>
                                    </div>
                                    <div class="page-content">
                                        <h3 class="page-title">
                                            <a href="<?php echo esc_url($page['url']); ?>">
                                                <?php echo esc_html($page['title']); ?>
                                            </a>
                                        </h3>
                                        <p class="page-description"><?php echo esc_html($page['description']); ?></p>
                                        <a href="<?php echo esc_url($page['url']); ?>" class="page-link">
                                            <?php esc_html_e('Visit Page', 'recruitpro'); ?>
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Recent Content Sections -->
                <div class="row">
                    
                    <!-- Recent Jobs -->
                    <?php if ($show_recent_jobs && !empty($recent_jobs)) : ?>
                        <div class="col-lg-6">
                            <section class="recent-jobs-section">
                                <h2 class="section-title"><?php esc_html_e('Recent Job Opportunities', 'recruitpro'); ?></h2>
                                <div class="recent-jobs-list">
                                    <?php foreach ($recent_jobs as $job) : ?>
                                        <div class="job-item">
                                            <h3 class="job-title">
                                                <a href="<?php echo esc_url(get_permalink($job->ID)); ?>">
                                                    <?php echo esc_html(get_the_title($job->ID)); ?>
                                                </a>
                                            </h3>
                                            <div class="job-meta">
                                                <?php
                                                $job_location = get_post_meta($job->ID, 'job_location', true);
                                                $job_type = get_post_meta($job->ID, 'job_type', true);
                                                ?>
                                                <?php if ($job_location) : ?>
                                                    <span class="job-location">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <?php echo esc_html($job_location); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($job_type) : ?>
                                                    <span class="job-type">
                                                        <i class="fas fa-briefcase"></i>
                                                        <?php echo esc_html($job_type); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="job-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt($job->ID), 15, '...'); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="section-footer">
                                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline-primary">
                                        <?php esc_html_e('View All Jobs', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </section>
                        </div>
                    <?php endif; ?>

                    <!-- Recent Blog Posts -->
                    <?php if (!empty($recent_posts)) : ?>
                        <div class="col-lg-6">
                            <section class="recent-posts-section">
                                <h2 class="section-title"><?php esc_html_e('Recent Career Articles', 'recruitpro'); ?></h2>
                                <div class="recent-posts-list">
                                    <?php foreach ($recent_posts as $post) : ?>
                                        <div class="post-item">
                                            <h3 class="post-title">
                                                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                                                    <?php echo esc_html(get_the_title($post->ID)); ?>
                                                </a>
                                            </h3>
                                            <div class="post-meta">
                                                <span class="post-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <?php echo get_the_date('', $post->ID); ?>
                                                </span>
                                                <span class="post-author">
                                                    <i class="fas fa-user"></i>
                                                    <?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?>
                                                </span>
                                            </div>
                                            <div class="post-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt($post->ID), 15, '...'); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="section-footer">
                                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-outline-primary">
                                        <?php esc_html_e('Read More Articles', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </section>
                        </div>
                    <?php endif; ?>
                    
                </div>

                <!-- Contact Section -->
                <?php if ($show_contact_info) : ?>
                    <section class="error-contact-section">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="contact-wrapper">
                                    <h2 class="section-title"><?php esc_html_e('Still Need Help?', 'recruitpro'); ?></h2>
                                    <p class="section-description">
                                        <?php printf(
                                            esc_html__('Can\'t find what you\'re looking for? Our recruitment team at %s is ready to assist you.', 'recruitpro'),
                                            esc_html($company_name)
                                        ); ?>
                                    </p>
                                    
                                    <div class="contact-options">
                                        <div class="contact-option">
                                            <div class="contact-icon">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="contact-content">
                                                <h3><?php esc_html_e('Call Us', 'recruitpro'); ?></h3>
                                                <p><?php echo esc_html(get_theme_mod('recruitpro_phone', '+1 (555) 123-4567')); ?></p>
                                                <a href="tel:<?php echo esc_attr(get_theme_mod('recruitpro_phone', '+15551234567')); ?>" class="contact-link">
                                                    <?php esc_html_e('Call Now', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="contact-option">
                                            <div class="contact-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="contact-content">
                                                <h3><?php esc_html_e('Email Us', 'recruitpro'); ?></h3>
                                                <p><?php echo esc_html(get_theme_mod('recruitpro_email', 'contact@recruitpro.com')); ?></p>
                                                <a href="mailto:<?php echo esc_attr(get_theme_mod('recruitpro_email', 'contact@recruitpro.com')); ?>" class="contact-link">
                                                    <?php esc_html_e('Send Email', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="contact-option">
                                            <div class="contact-icon">
                                                <i class="fas fa-comments"></i>
                                            </div>
                                            <div class="contact-content">
                                                <h3><?php esc_html_e('Live Chat', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Chat with our recruitment specialists', 'recruitpro'); ?></p>
                                                <button class="contact-link btn-start-chat">
                                                    <?php esc_html_e('Start Chat', 'recruitpro'); ?>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="contact-option">
                                            <div class="contact-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="contact-content">
                                                <h3><?php esc_html_e('Visit Us', 'recruitpro'); ?></h3>
                                                <p><?php echo esc_html(get_theme_mod('recruitpro_address', '123 Business Street, City, State 12345')); ?></p>
                                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="contact-link">
                                                    <?php esc_html_e('Get Directions', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- CTA Section -->
                <section class="error-cta-section">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="cta-wrapper">
                                <h2 class="cta-title"><?php esc_html_e('Don\'t Let This Stop Your Career Journey', 'recruitpro'); ?></h2>
                                <p class="cta-description">
                                    <?php printf(
                                        esc_html__('While you\'re here, why not explore what %s can do for your career or business? We\'re here to connect talent with opportunity.', 'recruitpro'),
                                        esc_html($company_name)
                                    ); ?>
                                </p>
                                
                                <div class="cta-buttons">
                                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search"></i>
                                        <?php esc_html_e('Browse Jobs', 'recruitpro'); ?>
                                    </a>
                                    
                                    <a href="<?php echo esc_url(home_url('/services/')); ?>" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-handshake"></i>
                                        <?php esc_html_e('Our Services', 'recruitpro'); ?>
                                    </a>
                                    
                                    <a href="<?php echo esc_url(home_url('/upload-resume/')); ?>" class="btn btn-outline-primary btn-lg">
                                        <i class="fas fa-upload"></i>
                                        <?php esc_html_e('Upload Resume', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
        
    </main>
</div>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<style>
/* 404 Error Page Specific Styles */
.error-404-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 80vh;
    padding: 2rem 0;
}

.error-404-wrapper {
    padding: 2rem 0;
}

/* Hero Section */
.error-404-hero {
    text-align: center;
    padding: 3rem 0;
    margin-bottom: 3rem;
}

.error-404-hero .row {
    align-items: center;
}

.error-code {
    font-size: 8rem;
    font-weight: 900;
    color: var(--primary-color);
    line-height: 1;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.error-message {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 2rem;
    line-height: 1.6;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.error-404-illustration {
    display: flex;
    justify-content: center;
    align-items: center;
}

.illustration-wrapper {
    max-width: 400px;
    width: 100%;
}

.error-svg {
    width: 100%;
    height: auto;
    filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
}

/* Search Section */
.error-search-section {
    background: white;
    padding: 3rem 0;
    margin-bottom: 3rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.search-wrapper {
    text-align: center;
}

.search-form {
    margin: 2rem 0;
}

.search-input-wrapper {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.search-field {
    width: 100%;
    padding: 1rem 4rem 1rem 1.5rem;
    font-size: 1.1rem;
    border: 2px solid #e1e5e9;
    border-radius: 50px;
    background: white;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.search-field:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.1);
}

.search-submit {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--primary-color);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.search-submit:hover {
    background: var(--secondary-color);
    transform: translateY(-50%) scale(1.05);
}

.popular-searches {
    margin-top: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    align-items: center;
}

.popular-label {
    font-weight: 600;
    color: #6c757d;
    margin-right: 0.5rem;
}

.search-tag {
    background: #f8f9fa;
    color: #495057;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid #e1e5e9;
}

.search-tag:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

/* Popular Pages Section */
.popular-pages-section {
    margin-bottom: 3rem;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.section-description {
    font-size: 1.1rem;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.popular-pages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.popular-page-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    text-align: center;
}

.popular-page-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.page-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-size: 2rem;
}

.page-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.page-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.page-title a:hover {
    color: var(--primary-color);
}

.page-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.page-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.page-link:hover {
    color: var(--secondary-color);
    text-decoration: none;
    transform: translateX(5px);
}

/* Recent Content Sections */
.recent-jobs-section,
.recent-posts-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
}

.recent-jobs-list,
.recent-posts-list {
    margin: 1.5rem 0;
}

.job-item,
.post-item {
    padding: 1.5rem 0;
    border-bottom: 1px solid #e1e5e9;
}

.job-item:last-child,
.post-item:last-child {
    border-bottom: none;
}

.job-title,
.post-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.job-title a,
.post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.job-title a:hover,
.post-title a:hover {
    color: var(--primary-color);
}

.job-meta,
.post-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: #6c757d;
    flex-wrap: wrap;
}

.job-meta span,
.post-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.job-excerpt,
.post-excerpt {
    color: #6c757d;
    line-height: 1.6;
}

.section-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e1e5e9;
}

/* Contact Section */
.error-contact-section {
    background: white;
    padding: 3rem 0;
    margin-bottom: 3rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.contact-wrapper {
    text-align: center;
}

.contact-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.contact-option {
    text-align: center;
    padding: 1.5rem;
    border: 1px solid #e1e5e9;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.contact-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

.contact-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.contact-content h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.contact-content p {
    color: #6c757d;
    margin-bottom: 1rem;
}

.contact-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border: 2px solid var(--primary-color);
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s ease;
    background: transparent;
    cursor: pointer;
}

.contact-link:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
}

/* CTA Section */
.error-cta-section {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 4rem 0;
    border-radius: 12px;
    text-align: center;
}

.cta-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-description {
    font-size: 1.2rem;
    margin-bottom: 2.5rem;
    opacity: 0.9;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-buttons .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.cta-buttons .btn-primary {
    background: white;
    color: var(--primary-color);
    border: 2px solid white;
}

.cta-buttons .btn-primary:hover {
    background: transparent;
    color: white;
}

.cta-buttons .btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.cta-buttons .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
}

.cta-buttons .btn-outline-primary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.cta-buttons .btn-outline-primary:hover {
    background: white;
    color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .error-code {
        font-size: 6rem;
    }
    
    .error-title {
        font-size: 2rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .popular-pages-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-options {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .popular-searches {
        justify-content: center;
    }
    
    .job-meta,
    .post-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 576px) {
    .error-404-page {
        padding: 1rem 0;
    }
    
    .error-404-wrapper {
        padding: 1rem 0;
    }
    
    .error-404-hero {
        padding: 2rem 0;
    }
    
    .error-code {
        font-size: 4rem;
    }
    
    .error-title {
        font-size: 1.5rem;
    }
    
    .error-message {
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .cta-title {
        font-size: 1.8rem;
    }
    
    .popular-page-card {
        padding: 1.5rem;
    }
    
    .recent-jobs-section,
    .recent-posts-section {
        padding: 1.5rem;
    }
}

/* Accessibility Enhancements */
@media (prefers-reduced-motion: reduce) {
    .popular-page-card,
    .contact-option,
    .search-submit,
    .page-link {
        transition: none;
    }
    
    .popular-page-card:hover,
    .contact-option:hover {
        transform: none;
    }
    
    .page-link:hover {
        transform: none;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .error-code {
        text-shadow: none;
    }
    
    .popular-page-card,
    .contact-option {
        border-width: 2px;
    }
    
    .search-field {
        border-width: 2px;
    }
}

/* Print styles */
@media print {
    .error-404-illustration,
    .cta-buttons,
    .contact-options {
        display: none;
    }
    
    .error-404-page {
        background: white;
    }
    
    .popular-page-card,
    .recent-jobs-section,
    .recent-posts-section {
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Enhanced search functionality
    const searchForm = document.querySelector('.error-search-form');
    const searchField = document.querySelector('.search-field');
    
    if (searchForm && searchField) {
        // Auto-focus search field
        searchField.focus();
        
        // Add search suggestions (if available)
        searchField.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length > 2) {
                // Could implement live search suggestions here
                console.log('Searching for:', query);
            }
        });
        
        // Track search attempts for analytics
        searchForm.addEventListener('submit', function(e) {
            const query = searchField.value.trim();
            if (query) {
                // Track 404 search attempt
                if (typeof gtag !== 'undefined') {
                    gtag('event', '404_search', {
                        'search_term': query,
                        'source_url': window.location.href
                    });
                }
            }
        });
    }
    
    // Popular search tags
    const searchTags = document.querySelectorAll('.search-tag');
    searchTags.forEach(tag => {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            const searchTerm = this.textContent.trim();
            if (searchField) {
                searchField.value = searchTerm;
                searchForm.submit();
            }
        });
    });
    
    // Chat functionality (placeholder)
    const chatButton = document.querySelector('.btn-start-chat');
    if (chatButton) {
        chatButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Check if chat widget is available
            if (typeof window.chatWidget !== 'undefined') {
                window.chatWidget.open();
            } else {
                // Fallback to contact page or form
                window.location.href = '/contact/';
            }
            
            // Track chat initiation from 404
            if (typeof gtag !== 'undefined') {
                gtag('event', 'chat_initiated', {
                    'source': '404_page'
                });
            }
        });
    }
    
    // Track 404 page view for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_view_404', {
            'page_title': document.title,
            'page_location': window.location.href,
            'referrer': document.referrer
        });
    }
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Lazy load images if any
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Add error logging for debugging
    window.addEventListener('error', function(e) {
        console.log('404 Page Error:', e.error);
        
        // Send error to analytics if needed
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exception', {
                'description': e.error.toString(),
                'fatal': false,
                'source': '404_page'
            });
        }
    });
    
    // Performance monitoring
    if ('PerformanceObserver' in window) {
        const observer = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.entryType === 'navigation') {
                    // Track 404 page load performance
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'timing_complete', {
                            'name': '404_page_load',
                            'value': Math.round(entry.loadEventEnd - entry.loadEventStart)
                        });
                    }
                }
            }
        });
        
        observer.observe({entryTypes: ['navigation']});
    }
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        // ESC key to go back
        if (e.key === 'Escape') {
            window.history.back();
        }
        
        // Enter key on focused elements
        if (e.key === 'Enter' && e.target.classList.contains('popular-page-card')) {
            const link = e.target.querySelector('a');
            if (link) {
                link.click();
            }
        }
    });
    
    // Add hover animations for cards
    const cards = document.querySelectorAll('.popular-page-card, .contact-option');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
});

// Utility function to log 404 errors (if tracking is enabled)
function recruitpro_log_404_error(url) {
    if (typeof gtag !== 'undefined') {
        gtag('event', '404_error', {
            'page_title': document.title,
            'page_location': url,
            'referrer': document.referrer,
            'user_agent': navigator.userAgent
        });
    }
    
    // Send to server for logging if endpoint exists
    if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.ajax_url) {
        fetch(recruitpro_theme.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'log_404_error',
                url: url,
                referrer: document.referrer,
                user_agent: navigator.userAgent,
                nonce: recruitpro_theme.nonce
            })
        }).catch(error => {
            console.log('Failed to log 404 error:', error);
        });
    }
}
</script>

<?php get_footer(); ?>