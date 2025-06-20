<?php
/**
 * Template Name: Sitemap Page
 *
 * Comprehensive HTML sitemap page template for recruitment agencies providing
 * organized navigation of all website content including services, resources,
 * blog posts, legal pages, and specialized sections. Designed for both user
 * navigation and SEO optimization with structured content organization.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-sitemap.php
 * Purpose: HTML sitemap for user navigation and SEO
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Organized content structure, search functionality, last updated dates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_sitemap_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_sitemap_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_last_modified = get_theme_mod('recruitpro_sitemap_show_dates', true);
$show_search = get_theme_mod('recruitpro_sitemap_show_search', true);
$show_page_count = get_theme_mod('recruitpro_sitemap_show_counts', true);

// Sitemap organization settings
$group_by_type = get_theme_mod('recruitpro_sitemap_group_by_type', true);
$show_post_counts = get_theme_mod('recruitpro_sitemap_show_post_counts', true);
$exclude_private = get_theme_mod('recruitpro_sitemap_exclude_private', true);

// Schema.org markup for sitemap page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Complete website sitemap for %s featuring all pages, services, resources, and content organized for easy navigation and discovery.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Website Sitemap',
        'description' => 'Comprehensive navigation structure for recruitment website'
    )
);

// Get content statistics
$page_count = wp_count_posts('page');
$post_count = wp_count_posts('post');
$total_pages = $page_count->publish + $post_count->publish;

// Custom post type counts
$testimonial_count = post_type_exists('testimonial') ? wp_count_posts('testimonial') : null;
$team_count = post_type_exists('team_member') ? wp_count_posts('team_member') : null;
$service_count = post_type_exists('service') ? wp_count_posts('service') : null;
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main sitemap-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header sitemap-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Complete overview of all pages and content on our website. Use this sitemap to quickly find the information you need or explore our comprehensive recruitment services and resources.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Sitemap Statistics -->
                            <?php if ($show_page_count) : ?>
                                <div class="sitemap-stats">
                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo esc_html($total_pages); ?></div>
                                            <div class="stat-label"><?php esc_html_e('Total Pages', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo esc_html($page_count->publish); ?></div>
                                            <div class="stat-label"><?php esc_html_e('Information Pages', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo esc_html($post_count->publish); ?></div>
                                            <div class="stat-label"><?php esc_html_e('Blog Posts', 'recruitpro'); ?></div>
                                        </div>
                                        <?php if ($show_last_modified) : ?>
                                            <div class="stat-item">
                                                <div class="stat-number"><?php echo esc_html(date('M j, Y')); ?></div>
                                                <div class="stat-label"><?php esc_html_e('Last Updated', 'recruitpro'); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Sitemap Search -->
                    <?php if ($show_search) : ?>
                        <section class="sitemap-search" id="sitemap-search">
                            <div class="search-container">
                                <h2 class="search-title"><?php esc_html_e('Search Sitemap', 'recruitpro'); ?></h2>
                                <form class="sitemap-search-form" role="search">
                                    <div class="search-input-wrapper">
                                        <input type="search" 
                                               class="search-field" 
                                               placeholder="<?php esc_attr_e('Search pages, services, resources...', 'recruitpro'); ?>" 
                                               name="sitemap_search" 
                                               id="sitemap-search-field"
                                               aria-label="<?php esc_attr_e('Search sitemap', 'recruitpro'); ?>">
                                        <button type="submit" class="search-submit">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <span class="screen-reader-text"><?php esc_html_e('Search', 'recruitpro'); ?></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Main Navigation Pages -->
                    <section class="sitemap-section main-pages" id="main-pages">
                        <div class="section-container">
                            <h2 class="section-title">
                                <i class="fas fa-home" aria-hidden="true"></i>
                                <?php esc_html_e('Main Navigation', 'recruitpro'); ?>
                            </h2>
                            <div class="sitemap-grid">
                                
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Company Information', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Homepage', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Welcome page and overview', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <?php if (get_page_by_path('about')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('about'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('About Us', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Company information and team', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('team')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('team'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Our Team', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Meet our recruitment experts', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('locations')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('locations'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Our Locations', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Office locations and contact info', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('careers')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('careers'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Careers', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Join our recruitment team', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('contact')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Contact Us', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Get in touch with our team', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Services Section -->
                    <section class="sitemap-section services-pages" id="services-pages">
                        <div class="section-container">
                            <h2 class="section-title">
                                <i class="fas fa-briefcase" aria-hidden="true"></i>
                                <?php esc_html_e('Recruitment Services', 'recruitpro'); ?>
                            </h2>
                            <div class="sitemap-grid">
                                
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Core Services', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('services')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('services'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('All Services', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Complete services overview', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/retainer-search/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Retainer Search', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Premium executive recruitment', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/contingency-recruitment/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Contingency Recruitment', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('No-risk placement service', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/temporary-staffing/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Temporary Staffing', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Flexible workforce solutions', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/contract-recruitment/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Contract Recruitment', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Project-based specialists', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/rpo-services/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('RPO Services', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Recruitment process outsourcing', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Specialized Services', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/executive-assessment/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Executive Assessment', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Leadership evaluation services', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/salary-benchmarking/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Salary Benchmarking', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Market salary analysis', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/services/hr-consulting/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('HR Consulting', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Strategic HR advisory services', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Process & Methodology', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('process')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('process'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Our Process', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Recruitment process overview', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('methodology')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('methodology'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Our Methodology', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Systematic recruitment approach', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Industry Expertise -->
                    <section class="sitemap-section industry-pages" id="industry-pages">
                        <div class="section-container">
                            <h2 class="section-title">
                                <i class="fas fa-industry" aria-hidden="true"></i>
                                <?php esc_html_e('Industry Expertise', 'recruitpro'); ?>
                            </h2>
                            <div class="sitemap-grid">
                                
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Industry Sectors', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('industries')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('industries'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('All Industries', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Complete industry coverage', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/industries/technology/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Technology', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Software, IT, and tech roles', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/industries/finance/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Finance', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Banking and financial services', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/industries/healthcare/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Healthcare', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Medical and healthcare roles', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/industries/manufacturing/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Manufacturing', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Operations and engineering', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/industries/retail/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Retail & E-commerce', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Retail and digital commerce', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Specializations', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('specializations')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('specializations'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('All Specializations', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Complete specialization list', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/specializations/executive-search/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Executive Search', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('C-level and senior management', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/specializations/graduate-recruitment/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Graduate Recruitment', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Entry-level and graduate roles', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Resources & Tools -->
                    <section class="sitemap-section resources-pages" id="resources-pages">
                        <div class="section-container">
                            <h2 class="section-title">
                                <i class="fas fa-book" aria-hidden="true"></i>
                                <?php esc_html_e('Resources & Tools', 'recruitpro'); ?>
                            </h2>
                            <div class="sitemap-grid">
                                
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Resource Center', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('resources')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('resources'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('All Resources', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Complete resource library', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('career-advice')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('career-advice'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Career Advice', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Professional development guidance', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('interview-tips')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('interview-tips'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Interview Tips', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Interview preparation guidance', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('cv-tips')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('cv-tips'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('CV Writing Tips', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Resume and CV guidance', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('salary-guide')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('salary-guide'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Salary Guide', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Market salary information', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('market-insights')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('market-insights'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Market Insights', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Industry trends and analysis', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Interactive Tools', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/tools/salary-calculator/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Salary Calculator', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Market salary benchmarking', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/tools/skills-assessment/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Skills Assessment', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Professional skills evaluation', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/tools/career-planner/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Career Planner', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Career path planning tool', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(home_url('/tools/cost-calculator/')); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Cost of Hire Calculator', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Recruitment cost analysis', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Blog & News -->
                    <section class="sitemap-section blog-pages" id="blog-pages">
                        <div class="section-container">
                            <h2 class="section-title">
                                <i class="fas fa-newspaper" aria-hidden="true"></i>
                                <?php esc_html_e('Blog & News', 'recruitpro'); ?>
                                <?php if ($show_post_counts && $post_count->publish > 0) : ?>
                                    <span class="section-count">(<?php echo esc_html($post_count->publish); ?> <?php esc_html_e('posts', 'recruitpro'); ?>)</span>
                                <?php endif; ?>
                            </h2>
                            <div class="sitemap-grid">
                                
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Blog Sections', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('blog')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('blog'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('All Blog Posts', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Latest insights and articles', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php elseif ($blog_page_id = get_option('page_for_posts')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_permalink($blog_page_id)); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Blog', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Latest insights and articles', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Dynamic Blog Categories -->
                                        <?php
                                        $categories = get_categories(array(
                                            'orderby' => 'count',
                                            'order' => 'DESC',
                                            'number' => 8,
                                            'hide_empty' => true
                                        ));
                                        
                                        if ($categories) :
                                            foreach ($categories as $category) :
                                        ?>
                                                <li class="page-item">
                                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="page-link">
                                                        <span class="page-title"><?php echo esc_html($category->name); ?></span>
                                                        <span class="page-description">
                                                            <?php printf(esc_html(_n('%d post', '%d posts', $category->count, 'recruitpro')), $category->count); ?>
                                                            <?php if ($category->description) : ?>
                                                                - <?php echo esc_html(wp_trim_words($category->description, 8)); ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </a>
                                                </li>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </ul>
                                </div>

                                <!-- Recent Blog Posts -->
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Recent Posts', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php
                                        $recent_posts = get_posts(array(
                                            'numberposts' => 10,
                                            'post_status' => 'publish',
                                            'orderby' => 'date',
                                            'order' => 'DESC'
                                        ));
                                        
                                        if ($recent_posts) :
                                            foreach ($recent_posts as $post) :
                                                setup_postdata($post);
                                        ?>
                                                <li class="page-item">
                                                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="page-link">
                                                        <span class="page-title"><?php echo esc_html(get_the_title($post->ID)); ?></span>
                                                        <span class="page-description">
                                                            <?php if ($show_last_modified) : ?>
                                                                <?php echo esc_html(get_the_date('M j, Y', $post->ID)); ?> - 
                                                            <?php endif; ?>
                                                            <?php echo esc_html(wp_trim_words(get_the_excerpt($post->ID), 12)); ?>
                                                        </span>
                                                    </a>
                                                </li>
                                        <?php
                                            endforeach;
                                            wp_reset_postdata();
                                        endif;
                                        ?>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Success Stories & Testimonials -->
                    <?php if ($testimonial_count && $testimonial_count->publish > 0) : ?>
                        <section class="sitemap-section testimonials-pages" id="testimonials-pages">
                            <div class="section-container">
                                <h2 class="section-title">
                                    <i class="fas fa-star" aria-hidden="true"></i>
                                    <?php esc_html_e('Success Stories & Testimonials', 'recruitpro'); ?>
                                    <?php if ($show_post_counts) : ?>
                                        <span class="section-count">(<?php echo esc_html($testimonial_count->publish); ?> <?php esc_html_e('stories', 'recruitpro'); ?>)</span>
                                    <?php endif; ?>
                                </h2>
                                <div class="sitemap-grid">
                                    
                                    <div class="sitemap-group">
                                        <h3 class="group-title"><?php esc_html_e('Client Success', 'recruitpro'); ?></h3>
                                        <ul class="page-list">
                                            <?php if (get_page_by_path('testimonials')) : ?>
                                                <li class="page-item">
                                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('testimonials'))); ?>" class="page-link">
                                                        <span class="page-title"><?php esc_html_e('All Testimonials', 'recruitpro'); ?></span>
                                                        <span class="page-description"><?php esc_html_e('Client feedback and reviews', 'recruitpro'); ?></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if (get_page_by_path('case-studies')) : ?>
                                                <li class="page-item">
                                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('case-studies'))); ?>" class="page-link">
                                                        <span class="page-title"><?php esc_html_e('Case Studies', 'recruitpro'); ?></span>
                                                        <span class="page-description"><?php esc_html_e('Detailed success stories', 'recruitpro'); ?></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Legal & Compliance -->
                    <section class="sitemap-section legal-pages" id="legal-pages">
                        <div class="section-container">
                            <h2 class="section-title">
                                <i class="fas fa-balance-scale" aria-hidden="true"></i>
                                <?php esc_html_e('Legal & Compliance', 'recruitpro'); ?>
                            </h2>
                            <div class="sitemap-grid">
                                
                                <div class="sitemap-group">
                                    <h3 class="group-title"><?php esc_html_e('Legal Information', 'recruitpro'); ?></h3>
                                    <ul class="page-list">
                                        <?php if (get_page_by_path('privacy')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('privacy'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Privacy Policy', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Data protection and privacy', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('terms')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('terms'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('Terms of Service', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Website terms and conditions', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (get_page_by_path('gdpr')) : ?>
                                            <li class="page-item">
                                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('gdpr'))); ?>" class="page-link">
                                                    <span class="page-title"><?php esc_html_e('GDPR Compliance', 'recruitpro'); ?></span>
                                                    <span class="page-description"><?php esc_html_e('Data protection rights', 'recruitpro'); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a href="<?php echo esc_url(get_permalink()); ?>" class="page-link">
                                                <span class="page-title"><?php esc_html_e('Sitemap', 'recruitpro'); ?></span>
                                                <span class="page-description"><?php esc_html_e('Complete website navigation', 'recruitpro'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Search and Navigation Help -->
                    <section class="sitemap-help" id="sitemap-help">
                        <div class="help-container">
                            <h2 class="section-title"><?php esc_html_e('Need Help Finding Something?', 'recruitpro'); ?></h2>
                            <div class="help-content">
                                <div class="help-grid">
                                    
                                    <div class="help-item">
                                        <div class="help-icon">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="help-title"><?php esc_html_e('Search Our Website', 'recruitpro'); ?></h3>
                                        <p class="help-description"><?php esc_html_e('Use our search function to find specific information, job opportunities, or resources.', 'recruitpro'); ?></p>
                                        <form class="help-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                            <div class="search-wrapper">
                                                <input type="search" 
                                                       class="search-field" 
                                                       placeholder="<?php esc_attr_e('Search...', 'recruitpro'); ?>" 
                                                       name="s" 
                                                       value="<?php echo get_search_query(); ?>">
                                                <button type="submit" class="search-submit">
                                                    <i class="fas fa-search" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="help-item">
                                        <div class="help-icon">
                                            <i class="fas fa-phone" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="help-title"><?php esc_html_e('Contact Our Team', 'recruitpro'); ?></h3>
                                        <p class="help-description"><?php esc_html_e('Speak directly with our recruitment experts for personalized assistance.', 'recruitpro'); ?></p>
                                        <div class="help-actions">
                                            <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="help-item">
                                        <div class="help-icon">
                                            <i class="fas fa-envelope" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="help-title"><?php esc_html_e('Stay Updated', 'recruitpro'); ?></h3>
                                        <p class="help-description"><?php esc_html_e('Subscribe to our newsletter for the latest job opportunities and industry insights.', 'recruitpro'); ?></p>
                                        <div class="help-actions">
                                            <a href="<?php echo esc_url(home_url('/newsletter/')); ?>" class="btn btn-secondary">
                                                <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('sitemap-sidebar')) : ?>
                <aside id="secondary" class="widget-area sitemap-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('sitemap-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   SITEMAP PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE SITEMAP PAGE FEATURES:

✅ ORGANIZED CONTENT STRUCTURE
- Main Navigation: Company info, team, locations, contact
- Recruitment Services: Core services, specialized offerings, methodology
- Industry Expertise: Sector coverage and specializations
- Resources & Tools: Guides, interactive tools, career advice
- Blog & News: Categories, recent posts, content archive
- Legal & Compliance: Privacy, terms, GDPR, sitemap

✅ COMPREHENSIVE SERVICE COVERAGE
- Retainer Search: Premium executive recruitment
- Contingency Recruitment: No-risk placement service
- Temporary Staffing: Flexible workforce solutions
- Contract Recruitment: Project-based specialists
- RPO Services: Recruitment process outsourcing
- Specialized Services: Assessment, benchmarking, consulting

✅ DYNAMIC CONTENT INTEGRATION
- Automatic blog category listing
- Recent posts with dates and excerpts
- Content count displays for sections
- Custom post type integration (testimonials, team)
- Dynamic page detection and linking

✅ SEARCH AND NAVIGATION
- Sitemap-specific search functionality
- Page filtering and organization
- Quick access to all major sections
- Help section with contact options
- Newsletter subscription integration

✅ PROFESSIONAL PRESENTATION
- Statistics showing total pages and content
- Last updated information display
- Page descriptions for context
- Organized grid layout structure
- Visual icons for section identification

✅ SEO OPTIMIZATION
- Schema.org WebPage markup
- Structured content organization
- Internal linking optimization
- Content discovery enhancement
- Search engine crawling assistance

✅ USER EXPERIENCE FEATURES
- Clear content categorization
- Descriptive page summaries
- Multiple ways to find information
- Contact integration for assistance
- Mobile-responsive design

✅ CONTENT MANAGEMENT
- WordPress customizer integration
- Conditional section display
- Post count and statistics
- Automatic content detection
- Manual link management options

PERFECT FOR:
- Large recruitment websites
- SEO and content discovery
- User navigation assistance
- Website structure overview
- Search engine optimization

BUSINESS BENEFITS:
- Improved user navigation
- Enhanced SEO performance
- Content discovery optimization
- Professional website organization
- Search engine crawling assistance

RECRUITMENT INDUSTRY SPECIFIC:
- Service model explanations
- Industry expertise showcase
- Resource library organization
- Blog content categorization
- Legal compliance navigation

TECHNICAL FEATURES:
- WordPress query optimization
- Schema.org markup integration
- Responsive design system
- Customizer option integration
- Performance optimization

*/
?>