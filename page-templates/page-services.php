<?php
/**
 * Template Name: Services Page
 *
 * Comprehensive services page template for recruitment agencies showcasing
 * all recruitment services including retainer search, contingency recruitment,
 * temporary staffing, executive search, and specialized hiring solutions.
 * Features detailed service descriptions, pricing models, and client benefits.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-services.php
 * Purpose: Services showcase and business model explanation
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Service categories, pricing models, process explanation, benefits
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_services_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_services_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_pricing = get_theme_mod('recruitpro_services_show_pricing', true);
$show_process = get_theme_mod('recruitpro_services_show_process', true);
$show_guarantees = get_theme_mod('recruitpro_services_show_guarantees', true);
$show_comparison = get_theme_mod('recruitpro_services_show_comparison', true);

// Service categories settings
$show_recruitment_models = get_theme_mod('recruitpro_services_show_models', true);
$show_specialized_services = get_theme_mod('recruitpro_services_show_specialized', true);
$show_industry_focus = get_theme_mod('recruitpro_services_show_industries', true);

// Schema.org markup for services page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Comprehensive recruitment services by %s including retainer search, contingency recruitment, temporary staffing, and specialized hiring solutions for all industries.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Service',
        'name' => 'Recruitment Services',
        'description' => 'Professional recruitment and talent acquisition services'
    )
);

// Service data and pricing
$retainer_percentage = get_theme_mod('recruitpro_retainer_percentage', '25-35%');
$contingency_percentage = get_theme_mod('recruitpro_contingency_percentage', '15-25%');
$temp_margin = get_theme_mod('recruitpro_temp_margin', '15-25%');
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main services-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header services-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('We offer comprehensive recruitment solutions tailored to your specific needs. From executive search to temporary staffing, our experienced team delivers quality results across multiple recruitment models and industry sectors.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Recruitment Models Overview -->
                    <?php if ($show_recruitment_models) : ?>
                        <section class="recruitment-models" id="recruitment-models">
                            <div class="models-container">
                                <h2 class="section-title"><?php esc_html_e('Our Recruitment Models', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Choose the recruitment approach that best fits your hiring needs and budget', 'recruitpro'); ?></p>
                                
                                <div class="models-grid">
                                    
                                    <!-- Retainer Recruitment -->
                                    <div class="model-card retainer-model">
                                        <div class="model-header">
                                            <div class="model-icon">
                                                <i class="fas fa-handshake" aria-hidden="true"></i>
                                            </div>
                                            <div class="model-badge"><?php esc_html_e('Premium Service', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="model-content">
                                            <h3 class="model-title"><?php esc_html_e('Retainer Recruitment', 'recruitpro'); ?></h3>
                                            <p class="model-subtitle"><?php esc_html_e('Retained Executive Search', 'recruitpro'); ?></p>
                                            <p class="model-description"><?php esc_html_e('Premium recruitment service with dedicated search consultant, comprehensive market research, and exclusive candidate access. Ideal for senior executive and mission-critical positions.', 'recruitpro'); ?></p>
                                            
                                            <div class="model-features">
                                                <h4 class="features-title"><?php esc_html_e('Key Features:', 'recruitpro'); ?></h4>
                                                <ul class="features-list">
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Dedicated senior consultant assigned exclusively', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Comprehensive market mapping and research', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Access to passive candidates not actively looking', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Thorough candidate assessment and profiling', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('6-month replacement guarantee included', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Regular progress updates and market insights', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>

                                            <div class="model-pricing">
                                                <div class="pricing-structure">
                                                    <h4 class="pricing-title"><?php esc_html_e('Investment Structure:', 'recruitpro'); ?></h4>
                                                    <div class="pricing-breakdown">
                                                        <div class="pricing-item">
                                                            <span class="pricing-label"><?php esc_html_e('Total Fee:', 'recruitpro'); ?></span>
                                                            <span class="pricing-value"><?php echo esc_html($retainer_percentage); ?> <?php esc_html_e('of annual salary', 'recruitpro'); ?></span>
                                                        </div>
                                                        <div class="pricing-schedule">
                                                            <div class="schedule-item">
                                                                <span class="schedule-stage"><?php esc_html_e('1. Assignment:', 'recruitpro'); ?></span>
                                                                <span class="schedule-amount"><?php esc_html_e('30% upfront', 'recruitpro'); ?></span>
                                                            </div>
                                                            <div class="schedule-item">
                                                                <span class="schedule-stage"><?php esc_html_e('2. Shortlist:', 'recruitpro'); ?></span>
                                                                <span class="schedule-amount"><?php esc_html_e('40% on presentation', 'recruitpro'); ?></span>
                                                            </div>
                                                            <div class="schedule-item">
                                                                <span class="schedule-stage"><?php esc_html_e('3. Placement:', 'recruitpro'); ?></span>
                                                                <span class="schedule-amount"><?php esc_html_e('30% on start date', 'recruitpro'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="model-ideal-for">
                                                <h4 class="ideal-title"><?php esc_html_e('Ideal For:', 'recruitpro'); ?></h4>
                                                <ul class="ideal-list">
                                                    <li><?php esc_html_e('C-Level executives (CEO, CTO, CFO)', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Senior management positions ($100K+ salary)', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Mission-critical roles requiring specific expertise', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Confidential searches and succession planning', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Roles requiring extensive market research', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="model-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=retainer')); ?>" class="btn btn-primary btn-lg">
                                                <?php esc_html_e('Discuss Retainer Search', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Contingency Recruitment -->
                                    <div class="model-card contingency-model">
                                        <div class="model-header">
                                            <div class="model-icon">
                                                <i class="fas fa-rocket" aria-hidden="true"></i>
                                            </div>
                                            <div class="model-badge"><?php esc_html_e('No Risk', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="model-content">
                                            <h3 class="model-title"><?php esc_html_e('Contingency Recruitment', 'recruitpro'); ?></h3>
                                            <p class="model-subtitle"><?php esc_html_e('Success-Based Placement', 'recruitpro'); ?></p>
                                            <p class="model-description"><?php esc_html_e('Results-driven recruitment with no upfront costs. Fee only payable upon successful placement and candidate start date. Perfect for mid-level positions and multiple hiring needs.', 'recruitpro'); ?></p>
                                            
                                            <div class="model-features">
                                                <h4 class="features-title"><?php esc_html_e('Key Features:', 'recruitpro'); ?></h4>
                                                <ul class="features-list">
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('No upfront costs or risk to your business', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Fast turnaround time (2-4 weeks average)', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Access to active job seekers and database', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Multi-channel candidate sourcing approach', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('3-month replacement guarantee included', 'recruitpro'); ?></li>
                                                    <li><i class="fas fa-check" aria-hidden="true"></i><?php esc_html_e('Perfect for bulk hiring requirements', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>

                                            <div class="model-pricing">
                                                <div class="pricing-structure">
                                                    <h4 class="pricing-title"><?php esc_html_e('Investment Structure:', 'recruitpro'); ?></h4>
                                                    <div class="pricing-breakdown">
                                                        <div class="pricing-item">
                                                            <span class="pricing-label"><?php esc_html_e('Total Fee:', 'recruitpro'); ?></span>
                                                            <span class="pricing-value"><?php echo esc_html($contingency_percentage); ?> <?php esc_html_e('of annual salary', 'recruitpro'); ?></span>
                                                        </div>
                                                        <div class="pricing-schedule">
                                                            <div class="schedule-item">
                                                                <span class="schedule-stage"><?php esc_html_e('Payment Terms:', 'recruitpro'); ?></span>
                                                                <span class="schedule-amount"><?php esc_html_e('Fee due only on successful placement', 'recruitpro'); ?></span>
                                                            </div>
                                                            <div class="schedule-item">
                                                                <span class="schedule-stage"><?php esc_html_e('Invoice Date:', 'recruitpro'); ?></span>
                                                                <span class="schedule-amount"><?php esc_html_e('Candidate start date', 'recruitpro'); ?></span>
                                                            </div>
                                                            <div class="schedule-item">
                                                                <span class="schedule-stage"><?php esc_html_e('Payment Terms:', 'recruitpro'); ?></span>
                                                                <span class="schedule-amount"><?php esc_html_e('30 days net', 'recruitpro'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="model-ideal-for">
                                                <h4 class="ideal-title"><?php esc_html_e('Ideal For:', 'recruitpro'); ?></h4>
                                                <ul class="ideal-list">
                                                    <li><?php esc_html_e('Mid-level positions ($30K-$100K salary)', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Multiple similar role requirements', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Fast-growing companies needing quick hires', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Budget-conscious hiring with no upfront risk', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Standard industry roles with clear requirements', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="model-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=contingency')); ?>" class="btn btn-primary btn-lg">
                                                <?php esc_html_e('Start Contingency Search', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Service Comparison -->
                    <?php if ($show_comparison) : ?>
                        <section class="service-comparison" id="service-comparison">
                            <div class="comparison-container">
                                <h2 class="section-title"><?php esc_html_e('Retainer vs Contingency: Which is Right for You?', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Compare our recruitment models to choose the best approach for your hiring needs', 'recruitpro'); ?></p>
                                
                                <div class="comparison-table">
                                    <table class="comparison-grid">
                                        <thead>
                                            <tr>
                                                <th class="feature-column"><?php esc_html_e('Feature', 'recruitpro'); ?></th>
                                                <th class="retainer-column">
                                                    <div class="column-header">
                                                        <i class="fas fa-handshake" aria-hidden="true"></i>
                                                        <?php esc_html_e('Retainer Search', 'recruitpro'); ?>
                                                    </div>
                                                </th>
                                                <th class="contingency-column">
                                                    <div class="column-header">
                                                        <i class="fas fa-rocket" aria-hidden="true"></i>
                                                        <?php esc_html_e('Contingency Search', 'recruitpro'); ?>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Upfront Cost', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('30% fee upfront', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('No upfront cost', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Fee Structure', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php echo esc_html($retainer_percentage); ?> <?php esc_html_e('of salary', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php echo esc_html($contingency_percentage); ?> <?php esc_html_e('of salary', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Typical Timeline', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('6-12 weeks', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('2-6 weeks', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Exclusivity', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('Exclusive engagement', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('Multiple agencies possible', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Market Research', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('Comprehensive mapping', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('Standard research', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Candidate Pool', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('Active + passive candidates', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('Primarily active candidates', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Replacement Guarantee', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('6 months', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('3 months', 'recruitpro'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="feature-name"><?php esc_html_e('Ideal Salary Range', 'recruitpro'); ?></td>
                                                <td class="retainer-value"><?php esc_html_e('$100K+', 'recruitpro'); ?></td>
                                                <td class="contingency-value"><?php esc_html_e('$30K-$100K', 'recruitpro'); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Specialized Services -->
                    <?php if ($show_specialized_services) : ?>
                        <section class="specialized-services" id="specialized-services">
                            <div class="services-container">
                                <h2 class="section-title"><?php esc_html_e('Specialized Recruitment Services', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Additional recruitment solutions to meet your specific business needs', 'recruitpro'); ?></p>
                                
                                <div class="services-grid">
                                    
                                    <!-- Temporary Staffing -->
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-icon">
                                                <i class="fas fa-clock" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="service-title"><?php esc_html_e('Temporary Staffing', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="service-content">
                                            <p class="service-description"><?php esc_html_e('Flexible temporary and contract staffing solutions for short-term projects, seasonal demands, and immediate workforce needs.', 'recruitpro'); ?></p>
                                            <div class="service-features">
                                                <ul>
                                                    <li><?php esc_html_e('Same-day to long-term placements', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Payroll and administration handling', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Temp-to-perm conversion options', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Immediate start availability', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="service-pricing">
                                                <span class="pricing-label"><?php esc_html_e('Margin:', 'recruitpro'); ?></span>
                                                <span class="pricing-value"><?php echo esc_html($temp_margin); ?> <?php esc_html_e('on hourly rate', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="service-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=temporary')); ?>" class="btn btn-outline">
                                                <?php esc_html_e('Get Quote', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Contract Recruitment -->
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-icon">
                                                <i class="fas fa-file-contract" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="service-title"><?php esc_html_e('Contract Recruitment', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="service-content">
                                            <p class="service-description"><?php esc_html_e('Professional contract and interim management recruitment for project-based work and specialized assignments.', 'recruitpro'); ?></p>
                                            <div class="service-features">
                                                <ul>
                                                    <li><?php esc_html_e('3-18 month contract placements', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Interim management solutions', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Project-based specialists', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('IR35 compliance support', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="service-pricing">
                                                <span class="pricing-label"><?php esc_html_e('Fee:', 'recruitpro'); ?></span>
                                                <span class="pricing-value"><?php esc_html_e('8-12% of contract value', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="service-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=contract')); ?>" class="btn btn-outline">
                                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- RPO Services -->
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-icon">
                                                <i class="fas fa-cogs" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="service-title"><?php esc_html_e('RPO Services', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="service-content">
                                            <p class="service-description"><?php esc_html_e('Recruitment Process Outsourcing - complete management of your recruitment function with dedicated teams and processes.', 'recruitpro'); ?></p>
                                            <div class="service-features">
                                                <ul>
                                                    <li><?php esc_html_e('Dedicated recruitment team', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('End-to-end process management', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Technology and systems integration', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Scalable recruitment solutions', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="service-pricing">
                                                <span class="pricing-label"><?php esc_html_e('Investment:', 'recruitpro'); ?></span>
                                                <span class="pricing-value"><?php esc_html_e('Custom pricing model', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="service-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=rpo')); ?>" class="btn btn-outline">
                                                <?php esc_html_e('Discuss RPO', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Executive Assessment -->
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-icon">
                                                <i class="fas fa-user-check" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="service-title"><?php esc_html_e('Executive Assessment', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="service-content">
                                            <p class="service-description"><?php esc_html_e('Comprehensive executive assessment and leadership evaluation services for internal promotions and external hires.', 'recruitpro'); ?></p>
                                            <div class="service-features">
                                                <ul>
                                                    <li><?php esc_html_e('Psychometric testing and profiling', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Leadership competency assessment', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('360-degree feedback reviews', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Detailed assessment reports', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="service-pricing">
                                                <span class="pricing-label"><?php esc_html_e('Fee:', 'recruitpro'); ?></span>
                                                <span class="pricing-value"><?php esc_html_e('$2,500-$5,000 per assessment', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="service-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=assessment')); ?>" class="btn btn-outline">
                                                <?php esc_html_e('Get Quote', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Salary Benchmarking -->
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-icon">
                                                <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="service-title"><?php esc_html_e('Salary Benchmarking', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="service-content">
                                            <p class="service-description"><?php esc_html_e('Comprehensive salary and benefits benchmarking reports to ensure competitive compensation packages and attract top talent.', 'recruitpro'); ?></p>
                                            <div class="service-features">
                                                <ul>
                                                    <li><?php esc_html_e('Market salary analysis by role', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Industry and location comparisons', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Benefits benchmarking analysis', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Custom reporting and insights', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="service-pricing">
                                                <span class="pricing-label"><?php esc_html_e('Fee:', 'recruitpro'); ?></span>
                                                <span class="pricing-value"><?php esc_html_e('$500-$2,000 per report', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="service-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=benchmarking')); ?>" class="btn btn-outline">
                                                <?php esc_html_e('Request Report', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- HR Consulting -->
                                    <div class="service-card">
                                        <div class="service-header">
                                            <div class="service-icon">
                                                <i class="fas fa-users-cog" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="service-title"><?php esc_html_e('HR Consulting', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="service-content">
                                            <p class="service-description"><?php esc_html_e('Strategic HR consulting services including talent strategy development, recruitment process optimization, and organizational design.', 'recruitpro'); ?></p>
                                            <div class="service-features">
                                                <ul>
                                                    <li><?php esc_html_e('Talent acquisition strategy development', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Recruitment process optimization', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Employer branding consultation', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('HR technology implementation', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                            <div class="service-pricing">
                                                <span class="pricing-label"><?php esc_html_e('Rate:', 'recruitpro'); ?></span>
                                                <span class="pricing-value"><?php esc_html_e('$150-$300 per hour', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="service-action">
                                            <a href="<?php echo esc_url(home_url('/contact/?service=consulting')); ?>" class="btn btn-outline">
                                                <?php esc_html_e('Book Consultation', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Industry Expertise -->
                    <?php if ($show_industry_focus) : ?>
                        <section class="industry-expertise" id="industry-expertise">
                            <div class="expertise-container">
                                <h2 class="section-title"><?php esc_html_e('Industry Expertise', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Specialized recruitment knowledge across key industry sectors', 'recruitpro'); ?></p>
                                
                                <div class="industries-grid">
                                    
                                    <div class="industry-item">
                                        <div class="industry-icon">
                                            <i class="fas fa-laptop-code" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="industry-title"><?php esc_html_e('Technology', 'recruitpro'); ?></h3>
                                        <p class="industry-description"><?php esc_html_e('Software engineers, data scientists, product managers, and tech leadership across all technology sectors.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="industry-item">
                                        <div class="industry-icon">
                                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="industry-title"><?php esc_html_e('Finance', 'recruitpro'); ?></h3>
                                        <p class="industry-description"><?php esc_html_e('Investment banking, corporate finance, accounting, and financial services professionals at all levels.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="industry-item">
                                        <div class="industry-icon">
                                            <i class="fas fa-heartbeat" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="industry-title"><?php esc_html_e('Healthcare', 'recruitpro'); ?></h3>
                                        <p class="industry-description"><?php esc_html_e('Medical professionals, healthcare administrators, pharmaceutical, and life sciences talent.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="industry-item">
                                        <div class="industry-icon">
                                            <i class="fas fa-industry" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="industry-title"><?php esc_html_e('Manufacturing', 'recruitpro'); ?></h3>
                                        <p class="industry-description"><?php esc_html_e('Operations management, engineering, supply chain, and manufacturing leadership positions.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="industry-item">
                                        <div class="industry-icon">
                                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="industry-title"><?php esc_html_e('Retail & E-commerce', 'recruitpro'); ?></h3>
                                        <p class="industry-description"><?php esc_html_e('Retail management, e-commerce specialists, digital marketing, and customer experience roles.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="industry-item">
                                        <div class="industry-icon">
                                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="industry-title"><?php esc_html_e('Education', 'recruitpro'); ?></h3>
                                        <p class="industry-description"><?php esc_html_e('Academic leadership, educational technology, corporate training, and learning development professionals.', 'recruitpro'); ?></p>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Service Guarantees -->
                    <?php if ($show_guarantees) : ?>
                        <section class="service-guarantees" id="service-guarantees">
                            <div class="guarantees-container">
                                <h2 class="section-title"><?php esc_html_e('Our Service Guarantees', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Commitment to excellence backed by concrete guarantees', 'recruitpro'); ?></p>
                                
                                <div class="guarantees-grid">
                                    
                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-shield-check" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Replacement Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('Free replacement within guarantee period if placement leaves for performance-related reasons.', 'recruitpro'); ?></p>
                                        <div class="guarantee-terms">
                                            <span class="term-label"><?php esc_html_e('Retainer:', 'recruitpro'); ?></span>
                                            <span class="term-value"><?php esc_html_e('6 months', 'recruitpro'); ?></span>
                                            <span class="term-separator">|</span>
                                            <span class="term-label"><?php esc_html_e('Contingency:', 'recruitpro'); ?></span>
                                            <span class="term-value"><?php esc_html_e('3 months', 'recruitpro'); ?></span>
                                        </div>
                                    </div>

                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Timeline Commitment', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('Delivery of qualified shortlist within agreed timeframes or extended search at no additional cost.', 'recruitpro'); ?></p>
                                        <div class="guarantee-terms">
                                            <span class="term-label"><?php esc_html_e('Standard delivery:', 'recruitpro'); ?></span>
                                            <span class="term-value"><?php esc_html_e('Within agreed timeline', 'recruitpro'); ?></span>
                                        </div>
                                    </div>

                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-star" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Quality Assurance', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('All candidates thoroughly vetted against agreed specifications with comprehensive reference checks.', 'recruitpro'); ?></p>
                                        <div class="guarantee-terms">
                                            <span class="term-label"><?php esc_html_e('Minimum shortlist:', 'recruitpro'); ?></span>
                                            <span class="term-value"><?php esc_html_e('3-5 qualified candidates', 'recruitpro'); ?></span>
                                        </div>
                                    </div>

                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-lock" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Confidentiality', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('Complete confidentiality of search process with signed NDAs and secure candidate handling.', 'recruitpro'); ?></p>
                                        <div class="guarantee-terms">
                                            <span class="term-label"><?php esc_html_e('Protection:', 'recruitpro'); ?></span>
                                            <span class="term-value"><?php esc_html_e('Full confidentiality guaranteed', 'recruitpro'); ?></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <section class="services-cta" id="services-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Ready to Start Your Search?', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Contact our recruitment experts to discuss your specific requirements and choose the right service model for your hiring needs.', 'recruitpro'); ?></p>
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                        <?php esc_html_e('Discuss Your Requirements', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/resources/hiring-guide/')); ?>" class="btn btn-secondary btn-lg">
                                        <?php esc_html_e('Download Hiring Guide', 'recruitpro'); ?>
                                    </a>
                                </div>
                                <div class="cta-contact-info">
                                    <span class="contact-item">
                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                        <a href="tel:+1234567890"><?php esc_html_e('Call us: (123) 456-7890', 'recruitpro'); ?></a>
                                    </span>
                                    <span class="contact-item">
                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                        <a href="mailto:hello@example.com"><?php esc_html_e('Email: hello@example.com', 'recruitpro'); ?></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('services-sidebar')) : ?>
                <aside id="secondary" class="widget-area services-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('services-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   SERVICES PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE RECRUITMENT SERVICES FEATURES:

✅ DETAILED RECRUITMENT MODELS
- Retainer Recruitment: Premium exclusive search (25-35% fee, 6-month guarantee)
- Contingency Recruitment: No-risk success-based (15-25% fee, 3-month guarantee)
- Clear pricing structures and payment schedules
- Detailed feature comparisons and ideal use cases

✅ SERVICE COMPARISON TABLE
- Side-by-side retainer vs contingency comparison
- Upfront costs, timelines, exclusivity details
- Market research depth and candidate access
- Guarantee periods and salary ranges

✅ SPECIALIZED SERVICES PORTFOLIO
- Temporary Staffing: Flexible workforce solutions
- Contract Recruitment: Project-based placements
- RPO Services: Complete recruitment outsourcing
- Executive Assessment: Leadership evaluation
- Salary Benchmarking: Market analysis reports
- HR Consulting: Strategic talent advisory

✅ INDUSTRY EXPERTISE SHOWCASE
- Technology, Finance, Healthcare sectors
- Manufacturing, Retail, Education specialization
- Industry-specific recruitment knowledge
- Sector expertise demonstration

✅ COMPREHENSIVE SERVICE GUARANTEES
- Replacement guarantees (3-6 months)
- Timeline commitments and quality assurance
- Confidentiality and professional standards
- Client protection and risk mitigation

✅ PROFESSIONAL PRICING TRANSPARENCY
- Clear fee structures for each service
- Payment schedules and terms
- Investment levels and value propositions
- No hidden costs or surprise fees

✅ SCHEMA.ORG OPTIMIZATION
- Service page structured data
- Organization and service markup
- Professional SEO optimization
- Business credibility signals

✅ CONVERSION-FOCUSED DESIGN
- Strategic call-to-action placement
- Service-specific contact forms
- Multiple engagement options
- Professional trust building

PERFECT FOR:
- Recruitment agencies and consultancies
- Executive search firms
- HR service providers
- Talent acquisition companies
- Staffing organizations

BUSINESS BENEFITS:
- Clear service differentiation
- Transparent pricing communication
- Professional credibility building
- Lead generation optimization
- Client education and trust

RECRUITMENT INDUSTRY SPECIFIC:
- Retainer vs contingency explanation
- Industry-standard pricing models
- Professional service guarantees
- Recruitment process transparency
- Expert positioning and authority

TECHNICAL FEATURES:
- WordPress customizer integration
- Conditional section display
- Mobile-responsive design
- Contact form integration
- Service-specific tracking

*/
?>