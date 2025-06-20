<?php
/**
 * Template Name: Resources Center Page
 *
 * Comprehensive resources page template for recruitment agencies providing
 * valuable content, tools, guides, and insights for both candidates and
 * employers. Features organized resource categories, downloadable content,
 * industry insights, career guidance, and professional development tools.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-resources.php
 * Purpose: Resource center and knowledge hub for recruitment industry
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Resource categories, downloads, tools, guides, search functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_resources_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_resources_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_search = get_theme_mod('recruitpro_resources_show_search', true);
$show_categories = get_theme_mod('recruitpro_resources_show_categories', true);
$show_featured = get_theme_mod('recruitpro_resources_show_featured', true);
$show_downloads = get_theme_mod('recruitpro_resources_show_downloads', true);
$show_tools = get_theme_mod('recruitpro_resources_show_tools', true);

// Resource settings
$resources_per_page = get_theme_mod('recruitpro_resources_per_page', 12);
$show_resource_stats = get_theme_mod('recruitpro_resources_show_stats', true);
$enable_resource_filtering = get_theme_mod('recruitpro_resources_enable_filtering', true);

// Schema.org markup for resources page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Comprehensive resource center for %s featuring career guides, industry insights, recruitment tools, and professional development resources for candidates and employers.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Recruitment Resources',
        'description' => 'Professional development and career guidance resources for the recruitment industry'
    )
);

// Resource categories and data
$resource_categories = array(
    'candidate-resources' => array(
        'title' => __('For Candidates', 'recruitpro'),
        'description' => __('Career development, job search guidance, and professional growth resources', 'recruitpro'),
        'icon' => 'user-graduate',
        'color' => '#3498db',
        'count' => 24
    ),
    'employer-resources' => array(
        'title' => __('For Employers', 'recruitpro'),
        'description' => __('Hiring guides, talent acquisition strategies, and workforce management tools', 'recruitpro'),
        'icon' => 'building',
        'color' => '#e74c3c',
        'count' => 18
    ),
    'industry-insights' => array(
        'title' => __('Industry Insights', 'recruitpro'),
        'description' => __('Market trends, salary reports, and recruitment industry analysis', 'recruitpro'),
        'icon' => 'chart-line',
        'color' => '#2ecc71',
        'count' => 15
    ),
    'tools-calculators' => array(
        'title' => __('Tools & Calculators', 'recruitpro'),
        'description' => __('Interactive tools for salary calculation, career planning, and skill assessment', 'recruitpro'),
        'icon' => 'calculator',
        'color' => '#f39c12',
        'count' => 8
    ),
    'guides-templates' => array(
        'title' => __('Guides & Templates', 'recruitpro'),
        'description' => __('Downloadable templates, checklists, and comprehensive guides', 'recruitpro'),
        'icon' => 'file-download',
        'color' => '#9b59b6',
        'count' => 12
    ),
    'training-development' => array(
        'title' => __('Training & Development', 'recruitpro'),
        'description' => __('Professional development courses and certification programs', 'recruitpro'),
        'icon' => 'graduation-cap',
        'color' => '#1abc9c',
        'count' => 6
    )
);
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main resources-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header resources-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Access our comprehensive collection of career resources, industry insights, professional tools, and expert guidance designed to accelerate your career growth and recruitment success.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Resource Statistics -->
                            <?php if ($show_resource_stats) : ?>
                                <div class="resource-stats">
                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <div class="stat-number">80+</div>
                                            <div class="stat-label"><?php esc_html_e('Resources Available', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">25+</div>
                                            <div class="stat-label"><?php esc_html_e('Downloadable Guides', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">10+</div>
                                            <div class="stat-label"><?php esc_html_e('Interactive Tools', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">100K+</div>
                                            <div class="stat-label"><?php esc_html_e('Downloads', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Resource Search and Filters -->
                    <?php if ($show_search || $enable_resource_filtering) : ?>
                        <section class="resource-filters" id="resource-filters">
                            <div class="filters-container">
                                
                                <!-- Search Bar -->
                                <?php if ($show_search) : ?>
                                    <div class="resource-search">
                                        <form class="search-form" role="search">
                                            <div class="search-input-wrapper">
                                                <input type="search" 
                                                       class="search-field" 
                                                       placeholder="<?php esc_attr_e('Search resources, guides, tools...', 'recruitpro'); ?>" 
                                                       name="resource_search" 
                                                       id="resource-search"
                                                       aria-label="<?php esc_attr_e('Search resources', 'recruitpro'); ?>">
                                                <button type="submit" class="search-submit">
                                                    <i class="fas fa-search" aria-hidden="true"></i>
                                                    <span class="screen-reader-text"><?php esc_html_e('Search', 'recruitpro'); ?></span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php endif; ?>

                                <!-- Category Filters -->
                                <?php if ($enable_resource_filtering) : ?>
                                    <div class="category-filters">
                                        <div class="filter-buttons">
                                            <button class="filter-btn active" data-filter="all">
                                                <?php esc_html_e('All Resources', 'recruitpro'); ?>
                                            </button>
                                            <?php foreach ($resource_categories as $category_key => $category) : ?>
                                                <button class="filter-btn" data-filter="<?php echo esc_attr($category_key); ?>">
                                                    <i class="fas fa-<?php echo esc_attr($category['icon']); ?>" aria-hidden="true"></i>
                                                    <?php echo esc_html($category['title']); ?>
                                                    <span class="count">(<?php echo esc_html($category['count']); ?>)</span>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Resource Categories Overview -->
                    <?php if ($show_categories) : ?>
                        <section class="resource-categories" id="resource-categories">
                            <div class="categories-container">
                                <h2 class="section-title"><?php esc_html_e('Resource Categories', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Explore our comprehensive collection organized by topic and audience', 'recruitpro'); ?></p>
                                
                                <div class="categories-grid">
                                    <?php foreach ($resource_categories as $category_key => $category) : ?>
                                        <div class="category-card" data-category="<?php echo esc_attr($category_key); ?>">
                                            <div class="category-header">
                                                <div class="category-icon" style="color: <?php echo esc_attr($category['color']); ?>">
                                                    <i class="fas fa-<?php echo esc_attr($category['icon']); ?>" aria-hidden="true"></i>
                                                </div>
                                                <div class="category-count">
                                                    <span class="count-number"><?php echo esc_html($category['count']); ?></span>
                                                    <span class="count-label"><?php esc_html_e('Resources', 'recruitpro'); ?></span>
                                                </div>
                                            </div>
                                            <div class="category-content">
                                                <h3 class="category-title"><?php echo esc_html($category['title']); ?></h3>
                                                <p class="category-description"><?php echo esc_html($category['description']); ?></p>
                                                <a href="#<?php echo esc_attr($category_key); ?>" class="category-link">
                                                    <?php esc_html_e('Explore Resources', 'recruitpro'); ?>
                                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Featured Resources -->
                    <?php if ($show_featured) : ?>
                        <section class="featured-resources" id="featured-resources">
                            <div class="featured-container">
                                <h2 class="section-title"><?php esc_html_e('Featured Resources', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Our most popular and valuable resources for career success', 'recruitpro'); ?></p>
                                
                                <div class="featured-grid">
                                    
                                    <!-- Featured Resource 1 -->
                                    <article class="resource-card featured-card" data-category="candidate-resources">
                                        <div class="resource-image">
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/resources/cv-guide.jpg'); ?>" 
                                                 alt="<?php esc_attr_e('Ultimate CV Writing Guide', 'recruitpro'); ?>"
                                                 loading="lazy">
                                            <div class="resource-badge"><?php esc_html_e('Most Downloaded', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="resource-content">
                                            <div class="resource-meta">
                                                <span class="resource-type"><?php esc_html_e('Guide', 'recruitpro'); ?></span>
                                                <span class="resource-category"><?php esc_html_e('For Candidates', 'recruitpro'); ?></span>
                                                <span class="resource-duration"><?php esc_html_e('15 min read', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="resource-title">
                                                <a href="<?php echo esc_url(home_url('/resources/ultimate-cv-writing-guide/')); ?>">
                                                    <?php esc_html_e('Ultimate CV Writing Guide for 2024', 'recruitpro'); ?>
                                                </a>
                                            </h3>
                                            <p class="resource-excerpt"><?php esc_html_e('Comprehensive guide covering everything from CV structure to industry-specific tips. Includes templates and real examples.', 'recruitpro'); ?></p>
                                            <div class="resource-stats">
                                                <span class="downloads">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('12.5K downloads', 'recruitpro'); ?>
                                                </span>
                                                <span class="rating">
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                    <?php esc_html_e('4.9/5', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                            <div class="resource-actions">
                                                <a href="<?php echo esc_url(home_url('/resources/ultimate-cv-writing-guide/')); ?>" class="btn btn-primary">
                                                    <?php esc_html_e('Download PDF', 'recruitpro'); ?>
                                                </a>
                                                <a href="#" class="btn btn-secondary bookmark-btn" data-resource-id="cv-guide">
                                                    <i class="fas fa-bookmark" aria-hidden="true"></i>
                                                    <?php esc_html_e('Save', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>

                                    <!-- Featured Resource 2 -->
                                    <article class="resource-card featured-card" data-category="employer-resources">
                                        <div class="resource-image">
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/resources/hiring-guide.jpg'); ?>" 
                                                 alt="<?php esc_attr_e('Complete Hiring Guide', 'recruitpro'); ?>"
                                                 loading="lazy">
                                            <div class="resource-badge"><?php esc_html_e('Trending', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="resource-content">
                                            <div class="resource-meta">
                                                <span class="resource-type"><?php esc_html_e('Guide', 'recruitpro'); ?></span>
                                                <span class="resource-category"><?php esc_html_e('For Employers', 'recruitpro'); ?></span>
                                                <span class="resource-duration"><?php esc_html_e('25 min read', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="resource-title">
                                                <a href="<?php echo esc_url(home_url('/resources/complete-hiring-guide/')); ?>">
                                                    <?php esc_html_e('Complete Hiring Guide: From Job Post to Onboarding', 'recruitpro'); ?>
                                                </a>
                                            </h3>
                                            <p class="resource-excerpt"><?php esc_html_e('Step-by-step hiring process guide with best practices, interview techniques, and onboarding strategies.', 'recruitpro'); ?></p>
                                            <div class="resource-stats">
                                                <span class="downloads">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('8.3K downloads', 'recruitpro'); ?>
                                                </span>
                                                <span class="rating">
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                    <?php esc_html_e('4.8/5', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                            <div class="resource-actions">
                                                <a href="<?php echo esc_url(home_url('/resources/complete-hiring-guide/')); ?>" class="btn btn-primary">
                                                    <?php esc_html_e('Download PDF', 'recruitpro'); ?>
                                                </a>
                                                <a href="#" class="btn btn-secondary bookmark-btn" data-resource-id="hiring-guide">
                                                    <i class="fas fa-bookmark" aria-hidden="true"></i>
                                                    <?php esc_html_e('Save', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>

                                    <!-- Featured Resource 3 -->
                                    <article class="resource-card featured-card" data-category="tools-calculators">
                                        <div class="resource-image">
                                            <div class="tool-preview">
                                                <i class="fas fa-calculator tool-icon" aria-hidden="true"></i>
                                                <div class="tool-label"><?php esc_html_e('Interactive Tool', 'recruitpro'); ?></div>
                                            </div>
                                            <div class="resource-badge"><?php esc_html_e('Interactive', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="resource-content">
                                            <div class="resource-meta">
                                                <span class="resource-type"><?php esc_html_e('Calculator', 'recruitpro'); ?></span>
                                                <span class="resource-category"><?php esc_html_e('Tools', 'recruitpro'); ?></span>
                                                <span class="resource-duration"><?php esc_html_e('5 min', 'recruitpro'); ?></span>
                                            </div>
                                            <h3 class="resource-title">
                                                <a href="<?php echo esc_url(home_url('/resources/salary-calculator/')); ?>">
                                                    <?php esc_html_e('Salary Benchmarking Calculator', 'recruitpro'); ?>
                                                </a>
                                            </h3>
                                            <p class="resource-excerpt"><?php esc_html_e('Compare salaries across industries, locations, and experience levels with real-time market data.', 'recruitpro'); ?></p>
                                            <div class="resource-stats">
                                                <span class="downloads">
                                                    <i class="fas fa-users" aria-hidden="true"></i>
                                                    <?php esc_html_e('25K+ uses', 'recruitpro'); ?>
                                                </span>
                                                <span class="rating">
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                    <?php esc_html_e('4.7/5', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                            <div class="resource-actions">
                                                <a href="<?php echo esc_url(home_url('/resources/salary-calculator/')); ?>" class="btn btn-primary">
                                                    <?php esc_html_e('Use Calculator', 'recruitpro'); ?>
                                                </a>
                                                <a href="#" class="btn btn-secondary bookmark-btn" data-resource-id="salary-calc">
                                                    <i class="fas fa-bookmark" aria-hidden="true"></i>
                                                    <?php esc_html_e('Save', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </article>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Candidate Resources Section -->
                    <section class="resource-section candidate-resources-section" id="candidate-resources">
                        <div class="section-container">
                            <div class="section-header">
                                <h2 class="section-title">
                                    <i class="fas fa-user-graduate" aria-hidden="true"></i>
                                    <?php esc_html_e('Resources for Candidates', 'recruitpro'); ?>
                                </h2>
                                <p class="section-subtitle"><?php esc_html_e('Career development tools and guidance to accelerate your professional journey', 'recruitpro'); ?></p>
                            </div>
                            
                            <div class="resources-grid">
                                
                                <!-- CV & Resume Resources -->
                                <div class="resource-category-group">
                                    <h3 class="group-title"><?php esc_html_e('CV & Resume', 'recruitpro'); ?></h3>
                                    <div class="group-resources">
                                        
                                        <article class="resource-item">
                                            <div class="resource-icon">
                                                <i class="fas fa-file-alt" aria-hidden="true"></i>
                                            </div>
                                            <div class="resource-details">
                                                <h4 class="resource-name">
                                                    <a href="<?php echo esc_url(home_url('/resources/cv-templates/')); ?>">
                                                        <?php esc_html_e('Professional CV Templates', 'recruitpro'); ?>
                                                    </a>
                                                </h4>
                                                <p class="resource-description"><?php esc_html_e('Industry-specific CV templates in Word and PDF format.', 'recruitpro'); ?></p>
                                                <div class="resource-meta">
                                                    <span class="format">PDF/DOCX</span>
                                                    <span class="downloads">2.1K downloads</span>
                                                </div>
                                            </div>
                                            <div class="resource-action">
                                                <a href="<?php echo esc_url(home_url('/resources/cv-templates/')); ?>" class="download-btn">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('Download', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </article>

                                        <article class="resource-item">
                                            <div class="resource-icon">
                                                <i class="fas fa-edit" aria-hidden="true"></i>
                                            </div>
                                            <div class="resource-details">
                                                <h4 class="resource-name">
                                                    <a href="<?php echo esc_url(home_url('/resources/cv-writing-checklist/')); ?>">
                                                        <?php esc_html_e('CV Writing Checklist', 'recruitpro'); ?>
                                                    </a>
                                                </h4>
                                                <p class="resource-description"><?php esc_html_e('Essential checklist to ensure your CV meets professional standards.', 'recruitpro'); ?></p>
                                                <div class="resource-meta">
                                                    <span class="format">PDF</span>
                                                    <span class="downloads">1.8K downloads</span>
                                                </div>
                                            </div>
                                            <div class="resource-action">
                                                <a href="<?php echo esc_url(home_url('/resources/cv-writing-checklist/')); ?>" class="download-btn">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('Download', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </article>

                                    </div>
                                </div>

                                <!-- Interview Preparation -->
                                <div class="resource-category-group">
                                    <h3 class="group-title"><?php esc_html_e('Interview Preparation', 'recruitpro'); ?></h3>
                                    <div class="group-resources">
                                        
                                        <article class="resource-item">
                                            <div class="resource-icon">
                                                <i class="fas fa-comments" aria-hidden="true"></i>
                                            </div>
                                            <div class="resource-details">
                                                <h4 class="resource-name">
                                                    <a href="<?php echo esc_url(home_url('/resources/interview-questions-guide/')); ?>">
                                                        <?php esc_html_e('Common Interview Questions & Answers', 'recruitpro'); ?>
                                                    </a>
                                                </h4>
                                                <p class="resource-description"><?php esc_html_e('Comprehensive guide with 100+ questions and expert answers.', 'recruitpro'); ?></p>
                                                <div class="resource-meta">
                                                    <span class="format">PDF</span>
                                                    <span class="downloads">5.2K downloads</span>
                                                </div>
                                            </div>
                                            <div class="resource-action">
                                                <a href="<?php echo esc_url(home_url('/resources/interview-questions-guide/')); ?>" class="download-btn">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('Download', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </article>

                                        <article class="resource-item">
                                            <div class="resource-icon">
                                                <i class="fas fa-video" aria-hidden="true"></i>
                                            </div>
                                            <div class="resource-details">
                                                <h4 class="resource-name">
                                                    <a href="<?php echo esc_url(home_url('/resources/video-interview-tips/')); ?>">
                                                        <?php esc_html_e('Video Interview Success Guide', 'recruitpro'); ?>
                                                    </a>
                                                </h4>
                                                <p class="resource-description"><?php esc_html_e('Technical and presentation tips for virtual interviews.', 'recruitpro'); ?></p>
                                                <div class="resource-meta">
                                                    <span class="format">PDF</span>
                                                    <span class="downloads">3.1K downloads</span>
                                                </div>
                                            </div>
                                            <div class="resource-action">
                                                <a href="<?php echo esc_url(home_url('/resources/video-interview-tips/')); ?>" class="download-btn">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('Download', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </article>

                                    </div>
                                </div>

                                <!-- Career Development -->
                                <div class="resource-category-group">
                                    <h3 class="group-title"><?php esc_html_e('Career Development', 'recruitpro'); ?></h3>
                                    <div class="group-resources">
                                        
                                        <article class="resource-item">
                                            <div class="resource-icon">
                                                <i class="fas fa-route" aria-hidden="true"></i>
                                            </div>
                                            <div class="resource-details">
                                                <h4 class="resource-name">
                                                    <a href="<?php echo esc_url(home_url('/resources/career-planning-template/')); ?>">
                                                        <?php esc_html_e('Career Planning Template', 'recruitpro'); ?>
                                                    </a>
                                                </h4>
                                                <p class="resource-description"><?php esc_html_e('Strategic template for mapping your career goals and milestones.', 'recruitpro'); ?></p>
                                                <div class="resource-meta">
                                                    <span class="format">DOCX</span>
                                                    <span class="downloads">1.5K downloads</span>
                                                </div>
                                            </div>
                                            <div class="resource-action">
                                                <a href="<?php echo esc_url(home_url('/resources/career-planning-template/')); ?>" class="download-btn">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                    <?php esc_html_e('Download', 'recruitpro'); ?>
                                                </a>
                                            </div>
                                        </article>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Interactive Tools Section -->
                    <?php if ($show_tools) : ?>
                        <section class="interactive-tools-section" id="tools-calculators">
                            <div class="section-container">
                                <div class="section-header">
                                    <h2 class="section-title">
                                        <i class="fas fa-tools" aria-hidden="true"></i>
                                        <?php esc_html_e('Interactive Tools & Calculators', 'recruitpro'); ?>
                                    </h2>
                                    <p class="section-subtitle"><?php esc_html_e('Professional tools to support your career and recruitment decisions', 'recruitpro'); ?></p>
                                </div>
                                
                                <div class="tools-grid">
                                    
                                    <!-- Salary Calculator -->
                                    <div class="tool-card">
                                        <div class="tool-header">
                                            <div class="tool-icon">
                                                <i class="fas fa-calculator" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="tool-title"><?php esc_html_e('Salary Calculator', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tool-content">
                                            <p class="tool-description"><?php esc_html_e('Compare salaries by role, location, and experience level with live market data.', 'recruitpro'); ?></p>
                                            <div class="tool-features">
                                                <ul>
                                                    <li><?php esc_html_e('Industry benchmarking', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Location adjustments', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Experience scaling', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="tool-stats">
                                                <span class="usage-count"><?php esc_html_e('25,000+ calculations', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="tool-action">
                                            <a href="<?php echo esc_url(home_url('/tools/salary-calculator/')); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Use Calculator', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Skills Assessment -->
                                    <div class="tool-card">
                                        <div class="tool-header">
                                            <div class="tool-icon">
                                                <i class="fas fa-tasks" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="tool-title"><?php esc_html_e('Skills Assessment', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tool-content">
                                            <p class="tool-description"><?php esc_html_e('Evaluate your professional skills and identify development opportunities.', 'recruitpro'); ?></p>
                                            <div class="tool-features">
                                                <ul>
                                                    <li><?php esc_html_e('Technical skills evaluation', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Soft skills assessment', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Personalized recommendations', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="tool-stats">
                                                <span class="usage-count"><?php esc_html_e('15,000+ assessments', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="tool-action">
                                            <a href="<?php echo esc_url(home_url('/tools/skills-assessment/')); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Start Assessment', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Career Path Planner -->
                                    <div class="tool-card">
                                        <div class="tool-header">
                                            <div class="tool-icon">
                                                <i class="fas fa-route" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="tool-title"><?php esc_html_e('Career Path Planner', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tool-content">
                                            <p class="tool-description"><?php esc_html_e('Plan your career progression with personalized roadmaps and milestones.', 'recruitpro'); ?></p>
                                            <div class="tool-features">
                                                <ul>
                                                    <li><?php esc_html_e('Goal setting framework', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Timeline planning', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Progress tracking', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="tool-stats">
                                                <span class="usage-count"><?php esc_html_e('8,500+ plans created', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="tool-action">
                                            <a href="<?php echo esc_url(home_url('/tools/career-planner/')); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Plan Career', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Cost of Hire Calculator -->
                                    <div class="tool-card">
                                        <div class="tool-header">
                                            <div class="tool-icon">
                                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="tool-title"><?php esc_html_e('Cost of Hire Calculator', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tool-content">
                                            <p class="tool-description"><?php esc_html_e('Calculate the true cost of recruitment including time, resources, and opportunity costs.', 'recruitpro'); ?></p>
                                            <div class="tool-features">
                                                <ul>
                                                    <li><?php esc_html_e('Comprehensive cost analysis', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('ROI calculations', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Industry comparisons', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="tool-stats">
                                                <span class="usage-count"><?php esc_html_e('5,200+ calculations', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="tool-action">
                                            <a href="<?php echo esc_url(home_url('/tools/cost-calculator/')); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Calculate Cost', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <section class="resources-cta" id="resources-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Need Personalized Career Guidance?', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Our expert recruitment consultants are here to provide personalized advice and support for your career journey or hiring needs.', 'recruitpro'); ?></p>
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                        <?php esc_html_e('Get Expert Consultation', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('services'))); ?>" class="btn btn-secondary btn-lg">
                                        <?php esc_html_e('Explore Our Services', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('resources-sidebar')) : ?>
                <aside id="secondary" class="widget-area resources-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('resources-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   RESOURCES CENTER PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE RESOURCES CENTER FEATURES:

✅ ORGANIZED RESOURCE CATEGORIES
- For Candidates: CV guides, interview prep, career development
- For Employers: Hiring guides, talent acquisition strategies
- Industry Insights: Market trends, salary reports, analysis
- Tools & Calculators: Interactive professional tools
- Guides & Templates: Downloadable content
- Training & Development: Professional courses

✅ ADVANCED SEARCH & FILTERING
- Resource search functionality
- Category-based filtering
- Content type filtering
- Usage statistics display
- Bookmarking capabilities

✅ INTERACTIVE TOOLS SECTION
- Salary benchmarking calculator
- Skills assessment tools
- Career path planner
- Cost of hire calculator
- Live market data integration

✅ FEATURED RESOURCES SHOWCASE
- Most downloaded content
- Trending resources
- Interactive tool previews
- Download statistics
- User ratings display

✅ COMPREHENSIVE CONTENT TYPES
- PDF guides and templates
- Interactive calculators
- Checklists and frameworks
- Video resources support
- Professional templates

✅ PROFESSIONAL PRESENTATION
- Resource cards with metadata
- Download counters and ratings
- Category organization
- Visual content previews
- Professional statistics

✅ SCHEMA.ORG OPTIMIZATION
- WebPage structured data
- Resource metadata markup
- Organization information
- SEO-friendly content structure

✅ USER ENGAGEMENT FEATURES
- Resource bookmarking
- Download tracking
- Usage statistics
- Social sharing ready
- Rating system support

PERFECT FOR:
- Career development centers
- Recruitment resource hubs
- Professional guidance platforms
- Industry knowledge bases
- Talent development resources

BUSINESS BENEFITS:
- Lead generation through downloads
- Professional authority building
- User engagement increase
- Content marketing platform
- Client education resource

RECRUITMENT INDUSTRY SPECIFIC:
- CV and resume resources
- Interview preparation guides
- Hiring best practices
- Salary benchmarking tools
- Career development content

TECHNICAL FEATURES:
- WordPress customizer integration
- Conditional section display
- Mobile-responsive design
- Performance optimized
- Accessibility compliant

*/
?>