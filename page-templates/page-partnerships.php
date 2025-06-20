<?php
/**
 * Template Name: Strategic Partnerships Page
 *
 * Professional partnerships page template for recruitment agencies showcasing
 * strategic business partnerships, technology collaborations, educational
 * alliances, and industry associations. Demonstrates network strength, credibility,
 * and extended capabilities through trusted partnerships and professional relationships.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-partnerships.php
 * Purpose: Strategic partnerships and alliances showcase
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Partnership categories, logos, descriptions, benefits, applications
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_partnerships_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_partnerships_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_strategic = get_theme_mod('recruitpro_partnerships_show_strategic', true);
$show_technology = get_theme_mod('recruitpro_partnerships_show_technology', true);
$show_educational = get_theme_mod('recruitpro_partnerships_show_educational', true);
$show_industry = get_theme_mod('recruitpro_partnerships_show_industry', true);
$show_international = get_theme_mod('recruitpro_partnerships_show_international', true);
$show_community = get_theme_mod('recruitpro_partnerships_show_community', true);
$show_partner_application = get_theme_mod('recruitpro_partnerships_show_application', true);
$partnerships_layout = get_theme_mod('recruitpro_partnerships_layout', 'grid');

// Partnership statistics
$total_partners = get_theme_mod('recruitpro_partnerships_total', '45+');
$countries_covered = get_theme_mod('recruitpro_partnerships_countries', '12');
$years_experience = get_theme_mod('recruitpro_partnerships_experience', '15+');
$success_stories = get_theme_mod('recruitpro_partnerships_success', '200+');

// Schema.org markup for partnerships page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => sprintf(__('Strategic partnerships and professional alliances of %s. Discover our network of technology partners, educational institutions, and industry associations.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
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

<main id="primary" class="site-main partnerships-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header partnerships-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('At %s, we believe in the power of strategic partnerships to deliver exceptional value to our clients and candidates. Our carefully selected network of partners extends our capabilities and enhances our service delivery across multiple domains.', 'recruitpro'), esc_html($company_name)); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Partnership Overview -->
                    <section class="partnership-overview" id="partnership-overview">
                        <div class="overview-container">
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Our Partnership Network', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Strategic alliances that strengthen our capabilities and extend our reach globally', 'recruitpro'); ?></p>
                            </div>

                            <div class="partnership-stats">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-handshake" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-number"><?php echo esc_html($total_partners); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Strategic Partners', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('Trusted partnerships worldwide', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-globe" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-number"><?php echo esc_html($countries_covered); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Countries Covered', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('International reach and presence', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-number"><?php echo esc_html($years_experience); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Years Building Partnerships', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('Long-term relationship development', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-icon">
                                            <i class="fas fa-trophy" aria-hidden="true"></i>
                                        </div>
                                        <div class="stat-number"><?php echo esc_html($success_stories); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Collaborative Success Stories', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('Joint achievements and victories', 'recruitpro'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Strategic Business Partnerships -->
                    <?php if ($show_strategic) : ?>
                        <section class="strategic-partnerships" id="strategic-partnerships">
                            <div class="partnerships-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Strategic Business Partnerships', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Collaborative relationships with leading recruitment agencies and business service providers', 'recruitpro'); ?></p>
                                </div>

                                <div class="partnership-benefits">
                                    <div class="benefits-grid">
                                        <div class="benefit-item">
                                            <div class="benefit-icon">
                                                <i class="fas fa-expand-arrows-alt" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="benefit-title"><?php esc_html_e('Extended Reach', 'recruitpro'); ?></h3>
                                            <p class="benefit-description"><?php esc_html_e('Access to broader talent pools and expanded market coverage through our partner network.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="benefit-item">
                                            <div class="benefit-icon">
                                                <i class="fas fa-rocket" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="benefit-title"><?php esc_html_e('Enhanced Capabilities', 'recruitpro'); ?></h3>
                                            <p class="benefit-description"><?php esc_html_e('Specialized expertise and niche recruitment capabilities through strategic alliances.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="benefit-item">
                                            <div class="benefit-icon">
                                                <i class="fas fa-clock" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="benefit-title"><?php esc_html_e('Faster Delivery', 'recruitpro'); ?></h3>
                                            <p class="benefit-description"><?php esc_html_e('Reduced time-to-hire through collaborative sourcing and shared resources.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="benefit-item">
                                            <div class="benefit-icon">
                                                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="benefit-title"><?php esc_html_e('Quality Assurance', 'recruitpro'); ?></h3>
                                            <p class="benefit-description"><?php esc_html_e('Rigorous partner vetting ensures consistent quality and professional standards.', 'recruitpro'); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="strategic-partners-showcase">
                                    <h3 class="showcase-title"><?php esc_html_e('Featured Strategic Partners', 'recruitpro'); ?></h3>
                                    <div class="partners-grid">
                                        
                                        <div class="partner-card">
                                            <div class="partner-logo">
                                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/global-recruitment-alliance.png'); ?>" alt="Global Recruitment Alliance" loading="lazy">
                                            </div>
                                            <div class="partner-content">
                                                <h4 class="partner-name"><?php esc_html_e('Global Recruitment Alliance', 'recruitpro'); ?></h4>
                                                <p class="partner-type"><?php esc_html_e('International Network Partner', 'recruitpro'); ?></p>
                                                <p class="partner-description"><?php esc_html_e('Premier global network providing access to top-tier recruitment agencies across 25 countries, enabling seamless international placements.', 'recruitpro'); ?></p>
                                                <div class="partner-benefits">
                                                    <span class="benefit-tag"><?php esc_html_e('Global Reach', 'recruitpro'); ?></span>
                                                    <span class="benefit-tag"><?php esc_html_e('Executive Search', 'recruitpro'); ?></span>
                                                    <span class="benefit-tag"><?php esc_html_e('Cross-border Placements', 'recruitpro'); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="partner-card">
                                            <div class="partner-logo">
                                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/hr-solutions-consortium.png'); ?>" alt="HR Solutions Consortium" loading="lazy">
                                            </div>
                                            <div class="partner-content">
                                                <h4 class="partner-name"><?php esc_html_e('HR Solutions Consortium', 'recruitpro'); ?></h4>
                                                <p class="partner-type"><?php esc_html_e('Service Integration Partner', 'recruitpro'); ?></p>
                                                <p class="partner-description"><?php esc_html_e('Comprehensive HR service provider offering payroll, benefits administration, and compliance support for our placed candidates.', 'recruitpro'); ?></p>
                                                <div class="partner-benefits">
                                                    <span class="benefit-tag"><?php esc_html_e('HR Services', 'recruitpro'); ?></span>
                                                    <span class="benefit-tag"><?php esc_html_e('Compliance', 'recruitpro'); ?></span>
                                                    <span class="benefit-tag"><?php esc_html_e('Employee Support', 'recruitpro'); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="partner-card">
                                            <div class="partner-logo">
                                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/executive-search-partners.png'); ?>" alt="Executive Search Partners" loading="lazy">
                                            </div>
                                            <div class="partner-content">
                                                <h4 class="partner-name"><?php esc_html_e('Executive Search Partners', 'recruitpro'); ?></h4>
                                                <p class="partner-type"><?php esc_html_e('Specialized Search Partner', 'recruitpro'); ?></p>
                                                <p class="partner-description"><?php esc_html_e('Boutique executive search firm specializing in C-suite and senior leadership placements across multiple industries.', 'recruitpro'); ?></p>
                                                <div class="partner-benefits">
                                                    <span class="benefit-tag"><?php esc_html_e('C-Suite Search', 'recruitpro'); ?></span>
                                                    <span class="benefit-tag"><?php esc_html_e('Leadership', 'recruitpro'); ?></span>
                                                    <span class="benefit-tag"><?php esc_html_e('Board Appointments', 'recruitpro'); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Technology Partnerships -->
                    <?php if ($show_technology) : ?>
                        <section class="technology-partnerships" id="technology-partnerships">
                            <div class="tech-partnerships-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Technology Partnerships', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Cutting-edge technology solutions that enhance our recruitment capabilities', 'recruitpro'); ?></p>
                                </div>

                                <div class="tech-categories">
                                    
                                    <div class="tech-category">
                                        <div class="category-header">
                                            <div class="category-icon">
                                                <i class="fas fa-phone" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="category-title"><?php esc_html_e('Communication Systems', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tech-partners">
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/ringover.png'); ?>" alt="Ringover" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('Ringover', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('Advanced cloud telephony system with API integration for seamless CRM call logging and management.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/whatsapp-business.png'); ?>" alt="WhatsApp Business" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('WhatsApp Business API', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('Integrated messaging platform for candidate and client communication with automated responses.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tech-category">
                                        <div class="category-header">
                                            <div class="category-icon">
                                                <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="category-title"><?php esc_html_e('Assessment & Verification', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tech-partners">
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/sterling-check.png'); ?>" alt="Sterling Check" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('Sterling Check', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('API-integrated background screening services that feed directly into our CRM system for candidate verification.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/codility.png'); ?>" alt="Codility" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('Codility', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('Technical skills assessment platform for developers and engineers with results integrated into candidate profiles.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tech-category">
                                        <div class="category-header">
                                            <div class="category-icon">
                                                <i class="fas fa-share-alt" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="category-title"><?php esc_html_e('Job Distribution & Social Media', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tech-partners">
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/linkedin-api.png'); ?>" alt="LinkedIn API" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('LinkedIn API', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('Automated job posting and social media management for LinkedIn profiles and company pages.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/job-board-network.png'); ?>" alt="Global Job Board Network" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('Global Job Board Network', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('API connections to 50+ free job boards worldwide for automated job posting and candidate sourcing.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tech-category">
                                        <div class="category-header">
                                            <div class="category-icon">
                                                <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="category-title"><?php esc_html_e('Data & Analytics', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="tech-partners">
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/payscale.png'); ?>" alt="PayScale" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('PayScale', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('Real-time salary data and market intelligence integrated into our CRM for accurate compensation insights.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="tech-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/google-analytics.png'); ?>" alt="Google Analytics" loading="lazy">
                                                </div>
                                                <div class="partner-info">
                                                    <h4><?php esc_html_e('Google Analytics & SEO Tools', 'recruitpro'); ?></h4>
                                                    <p><?php esc_html_e('Advanced website analytics and SEO tracking integrated with our recruitment performance metrics.', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Educational Partnerships -->
                    <?php if ($show_educational) : ?>
                        <section class="educational-partnerships" id="educational-partnerships">
                            <div class="edu-partnerships-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Educational Partnerships', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Collaborations with leading universities and training institutions to develop future talent', 'recruitpro'); ?></p>
                                </div>

                                <div class="edu-partnerships-grid">
                                    
                                    <div class="edu-partnership-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-university" aria-hidden="true"></i>
                                            <?php esc_html_e('University Partnerships', 'recruitpro'); ?>
                                        </h3>
                                        <div class="edu-partners-list">
                                            <div class="edu-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/london-business-school.png'); ?>" alt="London Business School" loading="lazy">
                                                </div>
                                                <div class="partner-details">
                                                    <h4><?php esc_html_e('London Business School', 'recruitpro'); ?></h4>
                                                    <p class="partnership-type"><?php esc_html_e('Graduate Recruitment Partnership', 'recruitpro'); ?></p>
                                                    <p><?php esc_html_e('Exclusive access to top MBA graduates and executive education alumni for leadership positions.', 'recruitpro'); ?></p>
                                                    <ul class="partnership-benefits">
                                                        <li><?php esc_html_e('MBA graduate placement program', 'recruitpro'); ?></li>
                                                        <li><?php esc_html_e('Executive education alumni network', 'recruitpro'); ?></li>
                                                        <li><?php esc_html_e('Career services collaboration', 'recruitpro'); ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <div class="edu-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/imperial-college.png'); ?>" alt="Imperial College London" loading="lazy">
                                                </div>
                                                <div class="partner-details">
                                                    <h4><?php esc_html_e('Imperial College London', 'recruitpro'); ?></h4>
                                                    <p class="partnership-type"><?php esc_html_e('STEM Talent Pipeline', 'recruitpro'); ?></p>
                                                    <p><?php esc_html_e('Strategic partnership for recruiting top engineering and technology graduates.', 'recruitpro'); ?></p>
                                                    <ul class="partnership-benefits">
                                                        <li><?php esc_html_e('Engineering graduate recruitment', 'recruitpro'); ?></li>
                                                        <li><?php esc_html_e('Technology innovation projects', 'recruitpro'); ?></li>
                                                        <li><?php esc_html_e('Research collaboration opportunities', 'recruitpro'); ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="edu-partnership-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                            <?php esc_html_e('Professional Training', 'recruitpro'); ?>
                                        </h3>
                                        <div class="edu-partners-list">
                                            <div class="edu-partner">
                                                <div class="partner-logo">
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/partners/cipd.png'); ?>" alt="CIPD" loading="lazy">
                                                </div>
                                                <div class="partner-details">
                                                    <h4><?php esc_html_e('Chartered Institute of Personnel and Development', 'recruitpro'); ?></h4>
                                                    <p class="partnership-type"><?php esc_html_e('Professional Development Partner', 'recruitpro'); ?></p>
                                                    <p><?php esc_html_e('Continuous professional development and certification programs for our recruitment team.', 'recruitpro'); ?></p>
                                                    <ul class="partnership-benefits">
                                                        <li><?php esc_html_e('Professional certification programs', 'recruitpro'); ?></li>
                                                        <li><?php esc_html_e('Industry best practices training', 'recruitpro'); ?></li>
                                                        <li><?php esc_html_e('Ethics and compliance education', 'recruitpro'); ?></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Industry Associations -->
                    <?php if ($show_industry) : ?>
                        <section class="industry-associations" id="industry-associations">
                            <div class="associations-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Industry Associations & Memberships', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Active participation in professional associations and industry bodies', 'recruitpro'); ?></p>
                                </div>

                                <div class="associations-grid">
                                    
                                    <div class="association-item">
                                        <div class="association-logo">
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/associations/rec-uk.png'); ?>" alt="REC UK" loading="lazy">
                                        </div>
                                        <div class="association-content">
                                            <h3 class="association-name"><?php esc_html_e('The Recruitment & Employment Confederation (REC)', 'recruitpro'); ?></h3>
                                            <p class="membership-type"><?php esc_html_e('Corporate Member', 'recruitpro'); ?></p>
                                            <p class="association-description"><?php esc_html_e('The UK\'s professional body for recruitment agencies, representing good practice and professional standards.', 'recruitpro'); ?></p>
                                            <div class="membership-benefits">
                                                <h4><?php esc_html_e('Membership Benefits:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('Professional standards compliance', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Industry best practice guidelines', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Legal and regulatory updates', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Professional development opportunities', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="association-item">
                                        <div class="association-logo">
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/associations/aesc.png'); ?>" alt="AESC" loading="lazy">
                                        </div>
                                        <div class="association-content">
                                            <h3 class="association-name"><?php esc_html_e('Association of Executive Search and Leadership Consultants', 'recruitpro'); ?></h3>
                                            <p class="membership-type"><?php esc_html_e('Professional Member', 'recruitpro'); ?></p>
                                            <p class="association-description"><?php esc_html_e('Global professional association for executive search and leadership consulting firms.', 'recruitpro'); ?></p>
                                            <div class="membership-benefits">
                                                <h4><?php esc_html_e('Membership Benefits:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('Executive search best practices', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Global networking opportunities', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Leadership development programs', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Industry research and insights', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="association-item">
                                        <div class="association-logo">
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/associations/shrm.png'); ?>" alt="SHRM" loading="lazy">
                                        </div>
                                        <div class="association-content">
                                            <h3 class="association-name"><?php esc_html_e('Society for Human Resource Management (SHRM)', 'recruitpro'); ?></h3>
                                            <p class="membership-type"><?php esc_html_e('Corporate Partner', 'recruitpro'); ?></p>
                                            <p class="association-description"><?php esc_html_e('World\'s largest HR professional society providing expertise and resources for HR professionals.', 'recruitpro'); ?></p>
                                            <div class="membership-benefits">
                                                <h4><?php esc_html_e('Partnership Benefits:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('HR industry trends and insights', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Professional certification support', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Talent management resources', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Global HR community access', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- International Partnerships -->
                    <?php if ($show_international) : ?>
                        <section class="international-partnerships" id="international-partnerships">
                            <div class="international-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('International Network', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Global partnerships enabling worldwide recruitment and placement services', 'recruitpro'); ?></p>
                                </div>

                                <div class="global-map-section">
                                    <div class="map-container">
                                        <div class="global-map-placeholder">
                                            <div class="map-notice">
                                                <i class="fas fa-globe" aria-hidden="true"></i>
                                                <h3><?php esc_html_e('Global Partner Network', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Our international partnerships span across multiple continents, providing comprehensive global recruitment coverage.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="regional-partnerships">
                                    
                                    <div class="region-group">
                                        <h3 class="region-title">
                                            <i class="fas fa-map-pin" aria-hidden="true"></i>
                                            <?php esc_html_e('Europe', 'recruitpro'); ?>
                                        </h3>
                                        <div class="region-partners">
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('Germany - TalentLink Berlin', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Leading recruitment agency specializing in engineering and technology roles in the German market.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('France - Recrutement Excellence Paris', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Premium executive search firm focused on financial services and luxury goods sectors.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('Netherlands - Amsterdam Talent Group', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Innovative recruitment solutions provider for the Dutch and Benelux markets.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="region-group">
                                        <h3 class="region-title">
                                            <i class="fas fa-map-pin" aria-hidden="true"></i>
                                            <?php esc_html_e('Asia Pacific', 'recruitpro'); ?>
                                        </h3>
                                        <div class="region-partners">
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('Singapore - APAC Executive Search', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Regional hub for executive search and senior management placements across Asia Pacific.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('Australia - Sydney Talent Solutions', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Comprehensive recruitment services covering Australia and New Zealand markets.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="region-group">
                                        <h3 class="region-title">
                                            <i class="fas fa-map-pin" aria-hidden="true"></i>
                                            <?php esc_html_e('North America', 'recruitpro'); ?>
                                        </h3>
                                        <div class="region-partners">
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('USA - Elite Recruiting Partners', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('High-end executive search and professional recruitment across major US metropolitan areas.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="regional-partner">
                                                <h4><?php esc_html_e('Canada - Toronto Executive Search', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Specialized recruitment services for the Canadian market with bilingual capabilities.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Community Partnerships -->
                    <?php if ($show_community) : ?>
                        <section class="community-partnerships" id="community-partnerships">
                            <div class="community-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Community & Social Partnerships', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Supporting local communities and social causes through strategic partnerships', 'recruitpro'); ?></p>
                                </div>

                                <div class="community-initiatives">
                                    
                                    <div class="initiative-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-heart" aria-hidden="true"></i>
                                            <?php esc_html_e('Diversity & Inclusion', 'recruitpro'); ?>
                                        </h3>
                                        <div class="initiatives-list">
                                            <div class="initiative-item">
                                                <h4><?php esc_html_e('Women in Leadership Initiative', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Partnership with local organizations to promote women in executive and leadership positions.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="initiative-item">
                                                <h4><?php esc_html_e('Minority Business Enterprise Program', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Supporting minority-owned businesses through recruitment services and professional development.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="initiative-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                            <?php esc_html_e('Education & Skills Development', 'recruitpro'); ?>
                                        </h3>
                                        <div class="initiatives-list">
                                            <div class="initiative-item">
                                                <h4><?php esc_html_e('Youth Employment Foundation', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Providing career guidance and placement support for young professionals entering the job market.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="initiative-item">
                                                <h4><?php esc_html_e('Skills Training Partnerships', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Collaborating with training providers to develop job-ready skills in high-demand sectors.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="initiative-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-hands-helping" aria-hidden="true"></i>
                                            <?php esc_html_e('Social Impact', 'recruitpro'); ?>
                                        </h3>
                                        <div class="initiatives-list">
                                            <div class="initiative-item">
                                                <h4><?php esc_html_e('Refugee Employment Program', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Supporting refugees and asylum seekers with employment opportunities and integration services.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="initiative-item">
                                                <h4><?php esc_html_e('Veterans Transition Support', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Helping military veterans transition to civilian careers through specialized placement programs.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Partner Application -->
                    <?php if ($show_partner_application) : ?>
                        <section class="partner-application" id="partner-application">
                            <div class="application-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Become a Partner', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Join our growing network of strategic partners and expand your business reach', 'recruitpro'); ?></p>
                                </div>

                                <div class="partnership-benefits-overview">
                                    <h3 class="benefits-title"><?php esc_html_e('Partnership Benefits', 'recruitpro'); ?></h3>
                                    <div class="benefits-grid">
                                        <div class="benefit-item">
                                            <i class="fas fa-network-wired" aria-hidden="true"></i>
                                            <h4><?php esc_html_e('Extended Network', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Access to our global network of clients and candidates', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="benefit-item">
                                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                                            <h4><?php esc_html_e('Business Growth', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Collaborative opportunities for mutual business development', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="benefit-item">
                                            <i class="fas fa-award" aria-hidden="true"></i>
                                            <h4><?php esc_html_e('Quality Standards', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Association with high-quality professional standards', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="benefit-item">
                                            <i class="fas fa-lightbulb" aria-hidden="true"></i>
                                            <h4><?php esc_html_e('Knowledge Sharing', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Exchange of best practices and industry insights', 'recruitpro'); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="partner-criteria">
                                    <h3 class="criteria-title"><?php esc_html_e('Partnership Criteria', 'recruitpro'); ?></h3>
                                    <div class="criteria-list">
                                        <div class="criteria-item">
                                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Proven track record in recruitment or related professional services', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="criteria-item">
                                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Commitment to ethical business practices and professional standards', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="criteria-item">
                                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Complementary services or market presence that adds value to our network', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="criteria-item">
                                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Strong reputation and financial stability in your market', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="criteria-item">
                                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Willingness to collaborate and share resources for mutual benefit', 'recruitpro'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="application-cta">
                                    <div class="cta-content">
                                        <h3 class="cta-title"><?php esc_html_e('Ready to Partner With Us?', 'recruitpro'); ?></h3>
                                        <p class="cta-description"><?php esc_html_e('Contact our partnership team to discuss collaboration opportunities and explore how we can work together to achieve mutual success.', 'recruitpro'); ?></p>
                                        <div class="cta-actions">
                                            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary btn-large">
                                                <i class="fas fa-handshake" aria-hidden="true"></i>
                                                <?php esc_html_e('Apply for Partnership', 'recruitpro'); ?>
                                            </a>
                                            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="btn btn-outline btn-large">
                                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                                                <?php esc_html_e('Learn More About Us', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('partnerships-sidebar')) : ?>
                <aside id="secondary" class="widget-area partnerships-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('partnerships-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   PARTNERSHIPS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE PARTNERSHIPS PAGE FEATURES:

✅ STRATEGIC BUSINESS PARTNERSHIPS
- International recruitment networks
- Service integration partners
- Specialized search partnerships
- Extended reach and capabilities
- Quality assurance through vetted partners

✅ TECHNOLOGY PARTNERSHIPS
- CRM and ATS solutions (Salesforce, Workday)
- AI and assessment tools (Pymetrics, HireVue)
- Security and compliance platforms
- Background screening services
- Innovation and efficiency enhancement

✅ EDUCATIONAL PARTNERSHIPS
- University recruitment partnerships
- Graduate placement programs
- Professional training collaborations
- STEM talent pipelines
- Continuous learning initiatives

✅ INDUSTRY ASSOCIATIONS
- REC UK corporate membership
- AESC professional membership
- SHRM corporate partnership
- Professional standards compliance
- Industry best practices access

✅ INTERNATIONAL NETWORK
- Global partner coverage across continents
- Regional expertise and market knowledge
- Cross-border placement capabilities
- Cultural and linguistic diversity
- Worldwide recruitment solutions

✅ COMMUNITY & SOCIAL PARTNERSHIPS
- Diversity and inclusion initiatives
- Education and skills development
- Social impact programs
- Refugee employment support
- Veterans transition assistance

✅ PARTNER APPLICATION SYSTEM
- Clear partnership criteria
- Benefits overview for prospects
- Application process guidance
- Quality standards requirements
- Mutual benefit framework

✅ CREDIBILITY & TRUST BUILDING
- Network strength demonstration
- Professional association memberships
- Quality standards adherence
- Global reach and capabilities
- Community involvement showcase

✅ BUSINESS VALUE PROPOSITION
- Extended service capabilities
- Enhanced market reach
- Technology innovation access
- Quality assurance protocols
- Collaborative growth opportunities

✅ SCHEMA.ORG OPTIMIZATION
- WebPage structured data
- Organization partnerships
- Professional associations
- Trust signals enhancement
- SEO-friendly presentation

PERFECT FOR:
- Demonstrating network strength
- Building client confidence
- Showcasing global capabilities
- Attracting potential partners
- Professional credibility building

BUSINESS BENEFITS:
- Enhanced service offerings
- Global market access
- Technology advancement
- Quality assurance
- Professional recognition

RECRUITMENT INDUSTRY SPECIFIC:
- Strategic alliance benefits
- Technology integration advantages
- Educational talent pipelines
- Professional standards compliance
- International placement capabilities

TECHNICAL FEATURES:
- WordPress customizer integration
- Conditional section display
- Mobile-responsive design
- Professional accessibility
- Performance optimized
- Logo and image support

*/
?>