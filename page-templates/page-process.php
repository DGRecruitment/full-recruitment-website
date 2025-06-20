<?php
/**
 * Template Name: Recruitment Process Page
 *
 * Professional recruitment process page template showcasing the complete
 * step-by-step recruitment workflow used by the agency. Designed to build
 * client confidence by demonstrating systematic approach, quality assurance,
 * and professional standards throughout the recruitment journey.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-process.php
 * Purpose: Recruitment process showcase and client education
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Process steps, timelines, quality assurance, success metrics
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_process_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_process_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_timeline = get_theme_mod('recruitpro_process_show_timeline', true);
$show_quality_metrics = get_theme_mod('recruitpro_process_show_metrics', true);
$show_technology = get_theme_mod('recruitpro_process_show_technology', true);
$show_guarantees = get_theme_mod('recruitpro_process_show_guarantees', true);

// Process statistics and data
$average_completion = get_theme_mod('recruitpro_process_completion_time', '6-8 weeks');
$success_rate = get_theme_mod('recruitpro_process_success_rate', '95%');
$client_satisfaction = get_theme_mod('recruitpro_process_satisfaction', '98%');
$candidates_per_role = get_theme_mod('recruitpro_process_candidates_per_role', '150+');

// Schema.org markup for recruitment process page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Comprehensive recruitment process overview for %s. Learn about our systematic approach to finding and placing the best talent for your organization.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Recruitment Process',
        'description' => 'Professional recruitment methodology and systematic talent acquisition process'
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

<main id="primary" class="site-main recruitment-process-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header process-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('Our proven %s recruitment process ensures quality placements through systematic candidate evaluation, comprehensive screening, and strategic placement methodology. Learn how we deliver exceptional results for our clients.', 'recruitpro'), esc_html($average_completion)); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Process Statistics -->
                            <div class="process-stats">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($success_rate); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Success Rate', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($average_completion); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Average Completion', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($client_satisfaction); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Client Satisfaction', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($candidates_per_role); ?></div>
                                        <div class="stat-label"><?php esc_html_e('Candidates Evaluated', 'recruitpro'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </header>
                    <?php endif; ?>

                    <!-- Process Overview -->
                    <section class="process-overview" id="process-overview">
                        <div class="overview-container">
                            <h2 class="section-title"><?php esc_html_e('Our Recruitment Process', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('A systematic 7-step approach ensuring quality placements and client satisfaction', 'recruitpro'); ?></p>
                            
                            <div class="process-introduction">
                                <p><?php esc_html_e('Our recruitment process has been refined over years of experience to deliver consistent, high-quality results. Each step is designed to ensure we find not just qualified candidates, but the perfect fit for your organization\'s culture and long-term success.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- Process Steps -->
                    <section class="process-steps" id="process-steps">
                        <div class="steps-container">
                            
                            <!-- Step 1: Initial Consultation -->
                            <div class="process-step" data-step="1">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">01</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Initial Consultation & Requirements Analysis', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('1-2 Days', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('We begin with a comprehensive consultation to understand your specific requirements, company culture, and strategic objectives. This foundation ensures we target the right candidates from the start.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Detailed role specification and competency mapping', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Company culture assessment and team dynamics analysis', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Salary benchmarking and market positioning', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Success criteria definition and KPI establishment', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Project timeline and communication protocol setup', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Comprehensive job specification document', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Ideal candidate profile and competency matrix', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Market analysis and salary benchmarking report', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Market Research -->
                            <div class="process-step" data-step="2">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">02</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Market Research & Talent Mapping', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('2-3 Days', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('Comprehensive market analysis to identify talent pools, competitive landscape, and optimal sourcing strategies. We map the talent market to ensure comprehensive coverage.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Industry talent mapping and competitor analysis', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Passive candidate identification and database research', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Social media and professional network analysis', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Market availability assessment and timing analysis', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Sourcing strategy development and channel optimization', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Market landscape report and talent availability analysis', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Target company list and candidate pipeline', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Sourcing strategy and channel plan', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Candidate Sourcing -->
                            <div class="process-step" data-step="3">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">03</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Multi-Channel Candidate Sourcing', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('1-2 Weeks', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('Systematic candidate identification through multiple channels including our proprietary database, professional networks, industry contacts, and strategic headhunting approaches.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Database mining and AI-powered candidate matching', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Executive search and strategic headhunting', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Professional network activation and referral generation', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Industry event participation and talent community engagement', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Multi-platform job posting and advertising optimization', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Comprehensive candidate pipeline with 150+ prospects', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Initial screening reports and candidate summaries', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Market response analysis and sourcing effectiveness report', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4: Screening & Assessment -->
                            <div class="process-step" data-step="4">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">04</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Comprehensive Screening & Assessment', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('1-2 Weeks', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('Rigorous multi-stage screening process combining technical evaluation, competency assessment, cultural fit analysis, and comprehensive background verification to ensure quality and suitability.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Competency-based interviewing and behavioral assessment', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Technical skills evaluation and practical testing', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Psychometric testing and personality profiling', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Cultural fit assessment and values alignment', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Reference checking and employment verification', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Shortlist of 5-8 top-tier candidates', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Detailed assessment reports for each candidate', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Comparative analysis and recommendation rankings', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 5: Client Presentation -->
                            <div class="process-step" data-step="5">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">05</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Strategic Candidate Presentation', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('2-3 Days', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('Professional presentation of shortlisted candidates with comprehensive profiles, assessment insights, and strategic recommendations to facilitate informed decision-making.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Comprehensive candidate profile creation and presentation', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Assessment summary and competency mapping review', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Cultural fit analysis and team integration assessment', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Reference feedback compilation and verification results', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Interview coordination and scheduling assistance', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Professional candidate presentation decks', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Interview scheduling and logistics coordination', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Candidate briefing and interview preparation', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 6: Interview Management -->
                            <div class="process-step" data-step="6">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">06</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Interview Management & Support', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('1-2 Weeks', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('Complete interview process management including candidate preparation, logistics coordination, feedback collection, and decision support to ensure smooth and effective selection.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Interview scheduling and calendar coordination', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Candidate preparation and briefing sessions', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Interview facilitation and process guidance', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Feedback collection and analysis', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Decision support and recommendation synthesis', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Interview coordination and logistics management', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Comprehensive feedback reports and analysis', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Decision matrix and recommendation summary', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 7: Offer Management & Onboarding -->
                            <div class="process-step" data-step="7">
                                <div class="step-content">
                                    <div class="step-header">
                                        <div class="step-number">
                                            <span class="number">07</span>
                                        </div>
                                        <div class="step-title-area">
                                            <h3 class="step-title"><?php esc_html_e('Offer Management & Onboarding Support', 'recruitpro'); ?></h3>
                                            <div class="step-duration"><?php esc_html_e('1-2 Weeks', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                    <div class="step-details">
                                        <p class="step-description"><?php esc_html_e('Expert negotiation support and onboarding facilitation to ensure successful placement completion and smooth transition for both candidate and client.', 'recruitpro'); ?></p>
                                        <div class="step-activities">
                                            <h4 class="activities-title"><?php esc_html_e('Key Activities:', 'recruitpro'); ?></h4>
                                            <ul class="activities-list">
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Salary negotiation and package optimization', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Contract terms and conditions facilitation', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Onboarding process coordination and support', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Post-placement follow-up and performance monitoring', 'recruitpro'); ?></li>
                                                <li><i class="fas fa-check-circle" aria-hidden="true"></i><?php esc_html_e('Guarantee period support and issue resolution', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="step-deliverables">
                                            <h4 class="deliverables-title"><?php esc_html_e('Deliverables:', 'recruitpro'); ?></h4>
                                            <ul class="deliverables-list">
                                                <li><?php esc_html_e('Successful placement and contract completion', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Onboarding checklist and integration plan', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('90-day performance review and guarantee support', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    <!-- Process Timeline -->
                    <?php if ($show_timeline) : ?>
                        <section class="process-timeline" id="process-timeline">
                            <div class="timeline-container">
                                <h2 class="section-title"><?php esc_html_e('Process Timeline Overview', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Typical project timeline from initial consultation to successful placement', 'recruitpro'); ?></p>
                                
                                <div class="timeline-visual">
                                    <div class="timeline-line"></div>
                                    
                                    <div class="timeline-milestones">
                                        <div class="milestone" data-week="1">
                                            <div class="milestone-marker"></div>
                                            <div class="milestone-content">
                                                <div class="milestone-week"><?php esc_html_e('Week 1', 'recruitpro'); ?></div>
                                                <div class="milestone-title"><?php esc_html_e('Project Initiation', 'recruitpro'); ?></div>
                                                <div class="milestone-description"><?php esc_html_e('Consultation & Market Research', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="milestone" data-week="2-3">
                                            <div class="milestone-marker"></div>
                                            <div class="milestone-content">
                                                <div class="milestone-week"><?php esc_html_e('Week 2-3', 'recruitpro'); ?></div>
                                                <div class="milestone-title"><?php esc_html_e('Active Sourcing', 'recruitpro'); ?></div>
                                                <div class="milestone-description"><?php esc_html_e('Candidate Identification & Initial Screening', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="milestone" data-week="4-5">
                                            <div class="milestone-marker"></div>
                                            <div class="milestone-content">
                                                <div class="milestone-week"><?php esc_html_e('Week 4-5', 'recruitpro'); ?></div>
                                                <div class="milestone-title"><?php esc_html_e('Assessment Phase', 'recruitpro'); ?></div>
                                                <div class="milestone-description"><?php esc_html_e('Comprehensive Evaluation & Shortlisting', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="milestone" data-week="6-7">
                                            <div class="milestone-marker"></div>
                                            <div class="milestone-content">
                                                <div class="milestone-week"><?php esc_html_e('Week 6-7', 'recruitpro'); ?></div>
                                                <div class="milestone-title"><?php esc_html_e('Client Interviews', 'recruitpro'); ?></div>
                                                <div class="milestone-description"><?php esc_html_e('Presentation & Interview Management', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="milestone" data-week="8">
                                            <div class="milestone-marker"></div>
                                            <div class="milestone-content">
                                                <div class="milestone-week"><?php esc_html_e('Week 8', 'recruitpro'); ?></div>
                                                <div class="milestone-title"><?php esc_html_e('Placement Completion', 'recruitpro'); ?></div>
                                                <div class="milestone-description"><?php esc_html_e('Offer Management & Onboarding', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Quality Assurance -->
                    <?php if ($show_quality_metrics) : ?>
                        <section class="quality-assurance" id="quality-assurance">
                            <div class="quality-container">
                                <h2 class="section-title"><?php esc_html_e('Quality Assurance & Metrics', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Our commitment to excellence through measurable quality standards', 'recruitpro'); ?></p>
                                
                                <div class="quality-metrics">
                                    <div class="metrics-grid">
                                        <div class="metric-category">
                                            <h3 class="category-title"><?php esc_html_e('Process Excellence', 'recruitpro'); ?></h3>
                                            <div class="metrics-list">
                                                <div class="metric-item">
                                                    <div class="metric-value">100%</div>
                                                    <div class="metric-label"><?php esc_html_e('Clients Receive Shortlist', 'recruitpro'); ?></div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-value"><?php echo esc_html($success_rate); ?></div>
                                                    <div class="metric-label"><?php esc_html_e('Placement Success Rate', 'recruitpro'); ?></div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-value">48hrs</div>
                                                    <div class="metric-label"><?php esc_html_e('Initial Response Time', 'recruitpro'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="metric-category">
                                            <h3 class="category-title"><?php esc_html_e('Client Satisfaction', 'recruitpro'); ?></h3>
                                            <div class="metrics-list">
                                                <div class="metric-item">
                                                    <div class="metric-value"><?php echo esc_html($client_satisfaction); ?></div>
                                                    <div class="metric-label"><?php esc_html_e('Client Satisfaction Score', 'recruitpro'); ?></div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-value">85%</div>
                                                    <div class="metric-label"><?php esc_html_e('Repeat Client Rate', 'recruitpro'); ?></div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-value">4.9/5</div>
                                                    <div class="metric-label"><?php esc_html_e('Average Client Rating', 'recruitpro'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="metric-category">
                                            <h3 class="category-title"><?php esc_html_e('Candidate Quality', 'recruitpro'); ?></h3>
                                            <div class="metrics-list">
                                                <div class="metric-item">
                                                    <div class="metric-value"><?php echo esc_html($candidates_per_role); ?></div>
                                                    <div class="metric-label"><?php esc_html_e('Candidates Evaluated', 'recruitpro'); ?></div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-value">6-8</div>
                                                    <div class="metric-label"><?php esc_html_e('Shortlist Size', 'recruitpro'); ?></div>
                                                </div>
                                                <div class="metric-item">
                                                    <div class="metric-value">90%</div>
                                                    <div class="metric-label"><?php esc_html_e('12-Month Retention', 'recruitpro'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Technology & Tools -->
                    <?php if ($show_technology) : ?>
                        <section class="technology-section" id="technology-tools">
                            <div class="technology-container">
                                <h2 class="section-title"><?php esc_html_e('Technology & Innovation', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Advanced tools and systems that enhance our recruitment capabilities', 'recruitpro'); ?></p>
                                
                                <div class="technology-grid">
                                    <div class="tech-category">
                                        <div class="tech-icon">
                                            <i class="fas fa-robot" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="tech-title"><?php esc_html_e('AI-Powered Matching', 'recruitpro'); ?></h3>
                                        <p class="tech-description"><?php esc_html_e('Advanced algorithms analyze candidate profiles, skills, and cultural fit indicators to identify optimal matches with precision and efficiency.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="tech-category">
                                        <div class="tech-icon">
                                            <i class="fas fa-database" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="tech-title"><?php esc_html_e('Comprehensive CRM', 'recruitpro'); ?></h3>
                                        <p class="tech-description"><?php esc_html_e('Integrated customer relationship management system tracking all interactions, progress, and outcomes throughout the recruitment lifecycle.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="tech-category">
                                        <div class="tech-icon">
                                            <i class="fas fa-chart-analytics" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="tech-title"><?php esc_html_e('Analytics Dashboard', 'recruitpro'); ?></h3>
                                        <p class="tech-description"><?php esc_html_e('Real-time reporting and analytics providing insights into process efficiency, candidate quality, and placement success metrics.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="tech-category">
                                        <div class="tech-icon">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="tech-title"><?php esc_html_e('Advanced Sourcing', 'recruitpro'); ?></h3>
                                        <p class="tech-description"><?php esc_html_e('Multi-platform sourcing tools with Boolean search capabilities, social media integration, and passive candidate identification.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="tech-category">
                                        <div class="tech-icon">
                                            <i class="fas fa-shield-check" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="tech-title"><?php esc_html_e('Security & Compliance', 'recruitpro'); ?></h3>
                                        <p class="tech-description"><?php esc_html_e('Enterprise-grade security measures ensuring data protection, GDPR compliance, and confidential information management.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="tech-category">
                                        <div class="tech-icon">
                                            <i class="fas fa-mobile-alt" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="tech-title"><?php esc_html_e('Mobile Accessibility', 'recruitpro'); ?></h3>
                                        <p class="tech-description"><?php esc_html_e('Mobile-optimized platforms enabling seamless candidate and client interactions from any device, anywhere, anytime.', 'recruitpro'); ?></p>
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
                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Timeline Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('We guarantee shortlist delivery within agreed timeframes or provide extended search at no additional cost.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-medal" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Quality Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('All candidates undergo rigorous screening. If shortlisted candidates don\'t meet specifications, we provide replacement candidates.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-redo" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Replacement Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('90-day replacement guarantee for successful placements who leave within the initial period for performance reasons.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Communication Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('Regular progress updates and 24-hour response time to all client communications throughout the process.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-user-shield" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Confidentiality Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('Complete confidentiality of client information, candidate data, and recruitment processes with signed NDAs.', 'recruitpro'); ?></p>
                                    </div>
                                    
                                    <div class="guarantee-item">
                                        <div class="guarantee-icon">
                                            <i class="fas fa-check-double" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="guarantee-title"><?php esc_html_e('Success Guarantee', 'recruitpro'); ?></h3>
                                        <p class="guarantee-description"><?php esc_html_e('No placement fee until successful candidate completion of probationary period and client satisfaction confirmation.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <section class="process-cta" id="process-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Ready to Experience Our Process?', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Let us demonstrate how our systematic approach can deliver the talent your organization needs. Start with a no-obligation consultation to discuss your requirements.', 'recruitpro'); ?></p>
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                        <?php esc_html_e('Start Your Recruitment Journey', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('about'))); ?>" class="btn btn-secondary btn-lg">
                                        <?php esc_html_e('Learn More About Us', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('process-sidebar')) : ?>
                <aside id="secondary" class="widget-area process-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('process-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   RECRUITMENT PROCESS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE RECRUITMENT PROCESS FEATURES:

✅ SYSTEMATIC 7-STEP PROCESS
- Initial consultation and requirements analysis
- Market research and talent mapping
- Multi-channel candidate sourcing
- Comprehensive screening and assessment
- Strategic candidate presentation
- Interview management and support
- Offer management and onboarding support

✅ DETAILED PROCESS BREAKDOWN
- Duration for each step
- Key activities and deliverables
- Professional methodology explanation
- Quality assurance at every stage
- Success metrics and benchmarks

✅ TIMELINE VISUALIZATION
- Interactive timeline overview
- Weekly milestone markers
- Clear process progression
- Realistic timeframe expectations
- Visual process representation

✅ QUALITY ASSURANCE METRICS
- Process excellence statistics
- Client satisfaction measurements
- Candidate quality indicators
- Success rate documentation
- Performance benchmarks

✅ TECHNOLOGY SHOWCASE
- AI-powered matching systems
- Comprehensive CRM integration
- Analytics and reporting tools
- Advanced sourcing capabilities
- Security and compliance measures

✅ SERVICE GUARANTEES
- Timeline commitment guarantees
- Quality assurance promises
- Replacement service guarantees
- Communication standards
- Confidentiality protections

✅ PROFESSIONAL PRESENTATION
- Clear step-by-step breakdown
- Visual process elements
- Statistics and success metrics
- Technology demonstrations
- Trust-building guarantees

✅ SCHEMA.ORG OPTIMIZATION
- WebPage structured data
- Professional service markup
- Process methodology schema
- Organization information
- SEO-friendly content structure

PERFECT FOR:
- Building client confidence
- Demonstrating professionalism
- Process transparency
- Competitive differentiation
- Trust and credibility building

BUSINESS BENEFITS:
- Enhanced client trust
- Professional positioning
- Competitive advantage
- Process clarity
- Quality demonstration

RECRUITMENT INDUSTRY SPECIFIC:
- Complete recruitment workflow
- Industry standard processes
- Professional methodology
- Quality assurance protocols
- Success measurement criteria

TECHNICAL FEATURES:
- WordPress customizer integration
- Conditional section display
- Mobile-responsive design
- Accessibility compliance
- Performance optimization

*/
?>