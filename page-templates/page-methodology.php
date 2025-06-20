<?php
/**
 * Template Name: Recruitment Methodology Page
 *
 * Professional methodology page template for recruitment agencies showcasing
 * their systematic approach, frameworks, quality assurance methods, and
 * proven strategies. This template demonstrates expertise and builds trust
 * by explaining the scientific and strategic approach to recruitment.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-methodology.php
 * Purpose: Recruitment methodology and approach showcase
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Framework explanation, quality assurance, success metrics, tools
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_methodology_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_methodology_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_framework = get_theme_mod('recruitpro_methodology_show_framework', true);
$show_quality_assurance = get_theme_mod('recruitpro_methodology_show_qa', true);
$show_assessment = get_theme_mod('recruitpro_methodology_show_assessment', true);
$show_technology = get_theme_mod('recruitpro_methodology_show_technology', true);
$show_success_metrics = get_theme_mod('recruitpro_methodology_show_metrics', true);
$show_best_practices = get_theme_mod('recruitpro_methodology_show_practices', true);
$show_continuous_improvement = get_theme_mod('recruitpro_methodology_show_improvement', true);
$methodology_layout = get_theme_mod('recruitpro_methodology_layout', 'detailed');

// Company methodology data
$years_experience = get_theme_mod('recruitpro_stats_years', '15+');
$success_rate = get_theme_mod('recruitpro_methodology_success_rate', '95%');
$avg_placement_time = get_theme_mod('recruitpro_methodology_placement_time', '21 days');
$client_retention = get_theme_mod('recruitpro_methodology_client_retention', '92%');

// Schema.org markup for methodology page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => sprintf(__('Comprehensive recruitment methodology and systematic approach used by %s. Learn about our proven frameworks, quality assurance, and success strategies.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Recruitment Methodology',
        'description' => 'Professional recruitment strategies and systematic approaches'
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

<main id="primary" class="site-main methodology-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header methodology-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('At %s, our recruitment methodology is built on %s years of industry experience and proven strategies. We combine systematic approaches with cutting-edge technology to deliver exceptional results for both clients and candidates.', 'recruitpro'), esc_html($company_name), esc_html($years_experience)); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Methodology Overview -->
                    <section class="methodology-overview" id="methodology-overview">
                        <div class="overview-container">
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Our Proven Approach', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('A systematic, data-driven methodology that consistently delivers exceptional recruitment outcomes', 'recruitpro'); ?></p>
                            </div>

                            <div class="methodology-stats">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($success_rate); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Success Rate', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('Successful placements within 6 months', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($avg_placement_time); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Average Placement Time', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('From brief to successful hire', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($client_retention); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Client Retention Rate', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('Clients who return for additional hires', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($years_experience); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Years of Experience', 'recruitpro'); ?></div>
                                        <div class="stat-description"><?php esc_html_e('Continuous methodology refinement', 'recruitpro'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Core Recruitment Framework -->
                    <?php if ($show_framework) : ?>
                        <section class="recruitment-framework" id="recruitment-framework">
                            <div class="framework-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Core Recruitment Framework', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Our systematic 6-phase framework ensures consistent quality and successful outcomes', 'recruitpro'); ?></p>
                                </div>

                                <div class="framework-phases">
                                    
                                    <div class="phase-item" data-phase="1">
                                        <div class="phase-content">
                                            <div class="phase-header">
                                                <div class="phase-number">01</div>
                                                <h3 class="phase-title"><?php esc_html_e('Strategic Consultation', 'recruitpro'); ?></h3>
                                            </div>
                                            <div class="phase-details">
                                                <p class="phase-description"><?php esc_html_e('Deep-dive analysis of role requirements, company culture, and strategic objectives. We create detailed hiring profiles and success criteria.', 'recruitpro'); ?></p>
                                                <ul class="phase-activities">
                                                    <li><?php esc_html_e('Stakeholder interviews and requirements gathering', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Role specification and competency mapping', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Cultural fit assessment framework design', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Search strategy and timeline development', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="phase-item" data-phase="2">
                                        <div class="phase-content">
                                            <div class="phase-header">
                                                <div class="phase-number">02</div>
                                                <h3 class="phase-title"><?php esc_html_e('Market Research & Mapping', 'recruitpro'); ?></h3>
                                            </div>
                                            <div class="phase-details">
                                                <p class="phase-description"><?php esc_html_e('Comprehensive market analysis and talent mapping to identify the best candidates within the target market segment.', 'recruitpro'); ?></p>
                                                <ul class="phase-activities">
                                                    <li><?php esc_html_e('Industry landscape analysis and salary benchmarking', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Talent pool mapping and competitor analysis', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Multi-channel sourcing strategy implementation', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Passive candidate identification and engagement', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="phase-item" data-phase="3">
                                        <div class="phase-content">
                                            <div class="phase-header">
                                                <div class="phase-number">03</div>
                                                <h3 class="phase-title"><?php esc_html_e('Candidate Sourcing & Attraction', 'recruitpro'); ?></h3>
                                            </div>
                                            <div class="phase-details">
                                                <p class="phase-description"><?php esc_html_e('Multi-channel approach to attract and engage top-tier candidates using both traditional and innovative sourcing methods.', 'recruitpro'); ?></p>
                                                <ul class="phase-activities">
                                                    <li><?php esc_html_e('Executive search and headhunting campaigns', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Digital marketing and employer branding', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Professional network leveraging and referrals', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Social recruiting and talent community building', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="phase-item" data-phase="4">
                                        <div class="phase-content">
                                            <div class="phase-header">
                                                <div class="phase-number">04</div>
                                                <h3 class="phase-title"><?php esc_html_e('Comprehensive Assessment', 'recruitpro'); ?></h3>
                                            </div>
                                            <div class="phase-details">
                                                <p class="phase-description"><?php esc_html_e('Rigorous multi-stage assessment process combining technical evaluation, cultural fit analysis, and performance prediction.', 'recruitpro'); ?></p>
                                                <ul class="phase-activities">
                                                    <li><?php esc_html_e('Competency-based interviewing and behavioral assessment', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Technical skills evaluation and case studies', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Psychometric testing and personality profiling', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Reference checking and background verification', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="phase-item" data-phase="5">
                                        <div class="phase-content">
                                            <div class="phase-header">
                                                <div class="phase-number">05</div>
                                                <h3 class="phase-title"><?php esc_html_e('Presentation & Negotiation', 'recruitpro'); ?></h3>
                                            </div>
                                            <div class="phase-details">
                                                <p class="phase-description"><?php esc_html_e('Strategic candidate presentation and expert negotiation support to ensure successful placement and mutual satisfaction.', 'recruitpro'); ?></p>
                                                <ul class="phase-activities">
                                                    <li><?php esc_html_e('Detailed candidate profiles and recommendation reports', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Interview coordination and feedback management', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Offer negotiation and package optimization', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Decision facilitation and acceptance support', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="phase-item" data-phase="6">
                                        <div class="phase-content">
                                            <div class="phase-header">
                                                <div class="phase-number">06</div>
                                                <h3 class="phase-title"><?php esc_html_e('Onboarding & Follow-up', 'recruitpro'); ?></h3>
                                            </div>
                                            <div class="phase-details">
                                                <p class="phase-description"><?php esc_html_e('Comprehensive onboarding support and ongoing follow-up to ensure successful integration and long-term placement success.', 'recruitpro'); ?></p>
                                                <ul class="phase-activities">
                                                    <li><?php esc_html_e('Pre-boarding support and documentation assistance', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Integration coaching and expectation management', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Performance monitoring and relationship management', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Warranty period support and issue resolution', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Quality Assurance Framework -->
                    <?php if ($show_quality_assurance) : ?>
                        <section class="quality-assurance" id="quality-assurance">
                            <div class="qa-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Quality Assurance Framework', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Rigorous quality controls and standards that ensure exceptional outcomes every time', 'recruitpro'); ?></p>
                                </div>

                                <div class="qa-pillars">
                                    
                                    <div class="qa-pillar">
                                        <div class="pillar-icon">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="pillar-title"><?php esc_html_e('Research Excellence', 'recruitpro'); ?></h3>
                                        <div class="pillar-content">
                                            <p><?php esc_html_e('Comprehensive market research and data-driven insights ensure we understand the landscape thoroughly before beginning any search.', 'recruitpro'); ?></p>
                                            <ul class="pillar-standards">
                                                <li><?php esc_html_e('Minimum 10 data sources per search', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Industry trend analysis and benchmarking', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Competitor intelligence and mapping', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Continuous market monitoring and updates', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="qa-pillar">
                                        <div class="pillar-icon">
                                            <i class="fas fa-user-check" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="pillar-title"><?php esc_html_e('Candidate Verification', 'recruitpro'); ?></h3>
                                        <div class="pillar-content">
                                            <p><?php esc_html_e('Multi-layered verification process ensures every candidate meets our high standards for authenticity and capability.', 'recruitpro'); ?></p>
                                            <ul class="pillar-standards">
                                                <li><?php esc_html_e('Identity and credential verification', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Employment history authentication', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Professional reference validation', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Skills assessment and testing', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="qa-pillar">
                                        <div class="pillar-icon">
                                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="pillar-title"><?php esc_html_e('Performance Tracking', 'recruitpro'); ?></h3>
                                        <div class="pillar-content">
                                            <p><?php esc_html_e('Continuous monitoring and measurement of key performance indicators to ensure optimal outcomes and continuous improvement.', 'recruitpro'); ?></p>
                                            <ul class="pillar-standards">
                                                <li><?php esc_html_e('Time-to-hire optimization tracking', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Quality of hire success metrics', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Client satisfaction measurement', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Candidate experience evaluation', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="qa-pillar">
                                        <div class="pillar-icon">
                                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="pillar-title"><?php esc_html_e('Compliance & Ethics', 'recruitpro'); ?></h3>
                                        <div class="pillar-content">
                                            <p><?php esc_html_e('Strict adherence to industry standards, legal requirements, and ethical practices in all recruitment activities.', 'recruitpro'); ?></p>
                                            <ul class="pillar-standards">
                                                <li><?php esc_html_e('GDPR and data protection compliance', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Equal opportunity and diversity practices', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Professional standards certification', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Confidentiality and discretion protocols', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Assessment Methodology -->
                    <?php if ($show_assessment) : ?>
                        <section class="assessment-methodology" id="assessment-methodology">
                            <div class="assessment-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Assessment Methodology', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Scientific approach to candidate evaluation combining multiple assessment techniques', 'recruitpro'); ?></p>
                                </div>

                                <div class="assessment-methods">
                                    
                                    <div class="method-card">
                                        <div class="method-header">
                                            <div class="method-icon">
                                                <i class="fas fa-comments" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="method-title"><?php esc_html_e('Competency-Based Interviews', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="method-content">
                                            <p class="method-description"><?php esc_html_e('Structured interviews focusing on past behavior and achievements to predict future performance.', 'recruitpro'); ?></p>
                                            <div class="method-details">
                                                <h4><?php esc_html_e('Assessment Areas:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('Technical competencies and expertise', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Leadership and management capabilities', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Problem-solving and decision-making', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Communication and interpersonal skills', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="method-card">
                                        <div class="method-header">
                                            <div class="method-icon">
                                                <i class="fas fa-brain" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="method-title"><?php esc_html_e('Psychometric Testing', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="method-content">
                                            <p class="method-description"><?php esc_html_e('Scientific personality and cognitive assessments to evaluate cultural fit and potential.', 'recruitpro'); ?></p>
                                            <div class="method-details">
                                                <h4><?php esc_html_e('Testing Types:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('Personality profiling and work style analysis', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Cognitive ability and reasoning tests', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Emotional intelligence assessment', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Motivation and values alignment', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="method-card">
                                        <div class="method-header">
                                            <div class="method-icon">
                                                <i class="fas fa-laptop-code" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="method-title"><?php esc_html_e('Skills Assessment', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="method-content">
                                            <p class="method-description"><?php esc_html_e('Practical evaluation of technical and functional skills through real-world scenarios and case studies.', 'recruitpro'); ?></p>
                                            <div class="method-details">
                                                <h4><?php esc_html_e('Assessment Methods:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('Technical skills testing and simulations', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Case study analysis and presentation', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Portfolio review and work samples', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Role-specific scenario exercises', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="method-card">
                                        <div class="method-header">
                                            <div class="method-icon">
                                                <i class="fas fa-handshake" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="method-title"><?php esc_html_e('Cultural Fit Analysis', 'recruitpro'); ?></h3>
                                        </div>
                                        <div class="method-content">
                                            <p class="method-description"><?php esc_html_e('Comprehensive evaluation of candidate alignment with organizational culture and values.', 'recruitpro'); ?></p>
                                            <div class="method-details">
                                                <h4><?php esc_html_e('Evaluation Areas:', 'recruitpro'); ?></h4>
                                                <ul>
                                                    <li><?php esc_html_e('Values alignment and cultural match', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Work environment preferences', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Team dynamics and collaboration style', 'recruitpro'); ?></li>
                                                    <li><?php esc_html_e('Growth mindset and adaptability', 'recruitpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Technology & Tools -->
                    <?php if ($show_technology) : ?>
                        <section class="technology-tools" id="technology-tools">
                            <div class="technology-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Technology & Tools', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Cutting-edge technology stack that enhances efficiency and improves outcomes', 'recruitpro'); ?></p>
                                </div>

                                <div class="technology-categories">
                                    
                                    <div class="tech-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-database" aria-hidden="true"></i>
                                            <?php esc_html_e('Candidate Management', 'recruitpro'); ?>
                                        </h3>
                                        <div class="tech-tools">
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('Advanced CRM System', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Comprehensive candidate database with AI-powered matching and tracking capabilities.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('CV Parsing Technology', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Automated CV analysis and data extraction for efficient candidate profiling.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tech-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <?php esc_html_e('Sourcing & Research', 'recruitpro'); ?>
                                        </h3>
                                        <div class="tech-tools">
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('Multi-Platform Sourcing', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Integrated access to major job boards, social networks, and professional databases.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('Market Intelligence Tools', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Real-time market data, salary benchmarking, and industry trend analysis.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tech-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                            <?php esc_html_e('Analytics & Reporting', 'recruitpro'); ?>
                                        </h3>
                                        <div class="tech-tools">
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('Performance Analytics', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Comprehensive metrics tracking and performance analysis with predictive insights.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('Custom Reporting', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Tailored reports and dashboards for clients and internal performance monitoring.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tech-category">
                                        <h3 class="category-title">
                                            <i class="fas fa-robot" aria-hidden="true"></i>
                                            <?php esc_html_e('AI & Automation', 'recruitpro'); ?>
                                        </h3>
                                        <div class="tech-tools">
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('AI-Powered Matching', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Machine learning algorithms for intelligent candidate-job matching and scoring.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="tool-item">
                                                <h4><?php esc_html_e('Process Automation', 'recruitpro'); ?></h4>
                                                <p><?php esc_html_e('Workflow automation for routine tasks, scheduling, and communication management.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Success Metrics -->
                    <?php if ($show_success_metrics) : ?>
                        <section class="success-metrics" id="success-metrics">
                            <div class="metrics-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Success Measurement', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Key performance indicators that demonstrate our commitment to excellence', 'recruitpro'); ?></p>
                                </div>

                                <div class="metrics-grid">
                                    
                                    <div class="metric-category">
                                        <h3 class="metric-category-title"><?php esc_html_e('Quality Metrics', 'recruitpro'); ?></h3>
                                        <div class="metric-items">
                                            <div class="metric-item">
                                                <div class="metric-icon">
                                                    <i class="fas fa-trophy" aria-hidden="true"></i>
                                                </div>
                                                <div class="metric-content">
                                                    <h4 class="metric-name"><?php esc_html_e('Hire Success Rate', 'recruitpro'); ?></h4>
                                                    <div class="metric-value"><?php echo esc_html($success_rate); ?></div>
                                                    <p class="metric-description"><?php esc_html_e('Candidates successfully completing probation period', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="metric-item">
                                                <div class="metric-icon">
                                                    <i class="fas fa-star" aria-hidden="true"></i>
                                                </div>
                                                <div class="metric-content">
                                                    <h4 class="metric-name"><?php esc_html_e('Client Satisfaction', 'recruitpro'); ?></h4>
                                                    <div class="metric-value">4.8/5</div>
                                                    <p class="metric-description"><?php esc_html_e('Average client satisfaction rating', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="metric-category">
                                        <h3 class="metric-category-title"><?php esc_html_e('Efficiency Metrics', 'recruitpro'); ?></h3>
                                        <div class="metric-items">
                                            <div class="metric-item">
                                                <div class="metric-icon">
                                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                                </div>
                                                <div class="metric-content">
                                                    <h4 class="metric-name"><?php esc_html_e('Time to Hire', 'recruitpro'); ?></h4>
                                                    <div class="metric-value"><?php echo esc_html($avg_placement_time); ?></div>
                                                    <p class="metric-description"><?php esc_html_e('Average time from brief to placement', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="metric-item">
                                                <div class="metric-icon">
                                                    <i class="fas fa-redo" aria-hidden="true"></i>
                                                </div>
                                                <div class="metric-content">
                                                    <h4 class="metric-name"><?php esc_html_e('Client Retention', 'recruitpro'); ?></h4>
                                                    <div class="metric-value"><?php echo esc_html($client_retention); ?></div>
                                                    <p class="metric-description"><?php esc_html_e('Clients returning for additional services', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="metric-category">
                                        <h3 class="metric-category-title"><?php esc_html_e('Impact Metrics', 'recruitpro'); ?></h3>
                                        <div class="metric-items">
                                            <div class="metric-item">
                                                <div class="metric-icon">
                                                    <i class="fas fa-chart-line" aria-hidden="true"></i>
                                                </div>
                                                <div class="metric-content">
                                                    <h4 class="metric-name"><?php esc_html_e('Performance Improvement', 'recruitpro'); ?></h4>
                                                    <div class="metric-value">87%</div>
                                                    <p class="metric-description"><?php esc_html_e('Clients reporting improved team performance', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                            <div class="metric-item">
                                                <div class="metric-icon">
                                                    <i class="fas fa-users" aria-hidden="true"></i>
                                                </div>
                                                <div class="metric-content">
                                                    <h4 class="metric-name"><?php esc_html_e('Diversity Success', 'recruitpro'); ?></h4>
                                                    <div class="metric-value">78%</div>
                                                    <p class="metric-description"><?php esc_html_e('Improvement in workplace diversity metrics', 'recruitpro'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Best Practices -->
                    <?php if ($show_best_practices) : ?>
                        <section class="best-practices" id="best-practices">
                            <div class="practices-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Industry Best Practices', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Adherence to global standards and innovative practices that set us apart', 'recruitpro'); ?></p>
                                </div>

                                <div class="practices-grid">
                                    
                                    <div class="practice-item">
                                        <div class="practice-icon">
                                            <i class="fas fa-balance-scale" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="practice-title"><?php esc_html_e('Ethical Recruitment', 'recruitpro'); ?></h3>
                                        <p class="practice-description"><?php esc_html_e('Commitment to fair, transparent, and ethical recruitment practices that respect all stakeholders.', 'recruitpro'); ?></p>
                                        <ul class="practice-standards">
                                            <li><?php esc_html_e('Diversity and inclusion initiatives', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Transparent fee structures', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Confidentiality protocols', 'recruitpro'); ?></li>
                                        </ul>
                                    </div>

                                    <div class="practice-item">
                                        <div class="practice-icon">
                                            <i class="fas fa-globe" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="practice-title"><?php esc_html_e('Global Standards', 'recruitpro'); ?></h3>
                                        <p class="practice-description"><?php esc_html_e('Adherence to international recruitment standards and regulatory requirements across all markets.', 'recruitpro'); ?></p>
                                        <ul class="practice-standards">
                                            <li><?php esc_html_e('ISO compliance certification', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('GDPR data protection standards', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Professional association memberships', 'recruitpro'); ?></li>
                                        </ul>
                                    </div>

                                    <div class="practice-item">
                                        <div class="practice-icon">
                                            <i class="fas fa-lightbulb" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="practice-title"><?php esc_html_e('Innovation Leadership', 'recruitpro'); ?></h3>
                                        <p class="practice-description"><?php esc_html_e('Continuous innovation in recruitment methodologies and adoption of emerging technologies.', 'recruitpro'); ?></p>
                                        <ul class="practice-standards">
                                            <li><?php esc_html_e('AI and machine learning integration', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Virtual reality assessment tools', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Predictive analytics implementation', 'recruitpro'); ?></li>
                                        </ul>
                                    </div>

                                    <div class="practice-item">
                                        <div class="practice-icon">
                                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="practice-title"><?php esc_html_e('Continuous Learning', 'recruitpro'); ?></h3>
                                        <p class="practice-description"><?php esc_html_e('Commitment to ongoing education and professional development for our entire team.', 'recruitpro'); ?></p>
                                        <ul class="practice-standards">
                                            <li><?php esc_html_e('Industry certification maintenance', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Regular training and upskilling', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Knowledge sharing initiatives', 'recruitpro'); ?></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Continuous Improvement -->
                    <?php if ($show_continuous_improvement) : ?>
                        <section class="continuous-improvement" id="continuous-improvement">
                            <div class="improvement-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Continuous Improvement', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Our commitment to evolving and enhancing our methodology based on results and feedback', 'recruitpro'); ?></p>
                                </div>

                                <div class="improvement-cycle">
                                    
                                    <div class="cycle-step" data-step="1">
                                        <div class="step-content">
                                            <div class="step-icon">
                                                <i class="fas fa-chart-pie" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="step-title"><?php esc_html_e('Data Collection', 'recruitpro'); ?></h3>
                                            <p class="step-description"><?php esc_html_e('Systematic gathering of performance data, client feedback, and market insights to identify improvement opportunities.', 'recruitpro'); ?></p>
                                        </div>
                                    </div>

                                    <div class="cycle-step" data-step="2">
                                        <div class="step-content">
                                            <div class="step-icon">
                                                <i class="fas fa-search-plus" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="step-title"><?php esc_html_e('Analysis & Review', 'recruitpro'); ?></h3>
                                            <p class="step-description"><?php esc_html_e('Comprehensive analysis of collected data to identify trends, patterns, and areas for enhancement.', 'recruitpro'); ?></p>
                                        </div>
                                    </div>

                                    <div class="cycle-step" data-step="3">
                                        <div class="step-content">
                                            <div class="step-icon">
                                                <i class="fas fa-cogs" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="step-title"><?php esc_html_e('Process Enhancement', 'recruitpro'); ?></h3>
                                            <p class="step-description"><?php esc_html_e('Implementation of improvements and refinements to our methodology based on analytical insights.', 'recruitpro'); ?></p>
                                        </div>
                                    </div>

                                    <div class="cycle-step" data-step="4">
                                        <div class="step-content">
                                            <div class="step-icon">
                                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="step-title"><?php esc_html_e('Validation & Testing', 'recruitpro'); ?></h3>
                                            <p class="step-description"><?php esc_html_e('Rigorous testing and validation of improvements to ensure they deliver measurable benefits.', 'recruitpro'); ?></p>
                                        </div>
                                    </div>

                                </div>

                                <div class="improvement-initiatives">
                                    <h3 class="initiatives-title"><?php esc_html_e('Recent Methodology Enhancements', 'recruitpro'); ?></h3>
                                    <div class="initiatives-grid">
                                        <div class="initiative-item">
                                            <h4><?php esc_html_e('AI-Enhanced Screening', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Integration of advanced AI algorithms to improve candidate screening accuracy by 35%.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="initiative-item">
                                            <h4><?php esc_html_e('Virtual Assessment Tools', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Implementation of VR-based assessment scenarios for better role-specific evaluation.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="initiative-item">
                                            <h4><?php esc_html_e('Predictive Analytics', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Advanced analytics to predict candidate success and reduce turnover by 28%.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="initiative-item">
                                            <h4><?php esc_html_e('Enhanced Diversity Tracking', 'recruitpro'); ?></h4>
                                            <p><?php esc_html_e('Improved diversity metrics and bias reduction protocols in our selection process.', 'recruitpro'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <section class="methodology-cta" id="methodology-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Experience Our Methodology', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('See how our proven methodology can deliver exceptional recruitment results for your organization. Let\'s discuss your specific requirements and demonstrate our approach.', 'recruitpro'); ?></p>
                                
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary btn-large">
                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                        <?php esc_html_e('Schedule Consultation', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/case-studies/')); ?>" class="btn btn-outline btn-large">
                                        <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                        <?php esc_html_e('View Case Studies', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('methodology-sidebar')) : ?>
                <aside id="secondary" class="widget-area methodology-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('methodology-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   METHODOLOGY PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE METHODOLOGY PAGE FEATURES:

✅ SYSTEMATIC RECRUITMENT FRAMEWORK
- 6-phase detailed recruitment process
- Strategic consultation and planning
- Market research and talent mapping
- Comprehensive candidate assessment
- Quality assurance at every stage

✅ QUALITY ASSURANCE FRAMEWORK
- Research excellence standards
- Multi-layered candidate verification
- Performance tracking and optimization
- Compliance and ethics protocols
- Continuous quality monitoring

✅ SCIENTIFIC ASSESSMENT METHODOLOGY
- Competency-based interviewing
- Psychometric testing and profiling
- Technical skills evaluation
- Cultural fit analysis
- Predictive performance modeling

✅ TECHNOLOGY & INNOVATION
- Advanced CRM and database systems
- AI-powered candidate matching
- Market intelligence tools
- Process automation and efficiency
- Predictive analytics integration

✅ SUCCESS MEASUREMENT FRAMEWORK
- Quality metrics and KPIs
- Efficiency tracking and optimization
- Impact measurement and ROI
- Client satisfaction monitoring
- Continuous improvement tracking

✅ INDUSTRY BEST PRACTICES
- Ethical recruitment standards
- Global compliance and regulations
- Innovation leadership
- Continuous learning and development
- Professional certification maintenance

✅ CONTINUOUS IMPROVEMENT CYCLE
- Data collection and analysis
- Process enhancement implementation
- Validation and testing protocols
- Regular methodology updates
- Innovation integration

✅ PROFESSIONAL PRESENTATION
- Detailed framework explanations
- Visual process representations
- Statistics and success metrics
- Technology showcase
- Trust building elements

✅ TRUST & CREDIBILITY BUILDING
- Proven success statistics
- Transparent methodology explanation
- Professional standards adherence
- Innovation leadership demonstration
- Continuous improvement commitment

✅ SCHEMA.ORG OPTIMIZATION
- WebPage structured data
- Professional service markup
- Methodology and process schema
- Organization information
- Trust signals optimization

PERFECT FOR:
- Demonstrating recruitment expertise
- Building client confidence
- Differentiating from competitors
- Showcasing systematic approach
- Professional credibility building

BUSINESS BENEFITS:
- Increased client trust
- Competitive differentiation
- Professional positioning
- Methodology transparency
- Quality assurance demonstration

RECRUITMENT INDUSTRY SPECIFIC:
- Comprehensive assessment methods
- Industry standard compliance
- Professional recruitment framework
- Quality assurance protocols
- Success measurement criteria

TECHNICAL FEATURES:
- WordPress customizer integration
- Conditional section display
- Mobile-responsive design
- Professional accessibility
- Performance optimized

*/
?>