<?php
/**
 * Template Name: Careers Page
 *
 * Professional careers page template for recruitment agencies showcasing
 * employment opportunities within the agency itself. Designed to attract
 * talented recruitment professionals, HR specialists, and support staff
 * to join the team. Highlights company culture, benefits, and growth opportunities.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-careers.php
 * Purpose: Careers page for recruitment agency employment
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Open positions, company culture, benefits, application process
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_careers_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_careers_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_benefits = get_theme_mod('recruitpro_careers_show_benefits', true);
$show_culture = get_theme_mod('recruitpro_careers_show_culture', true);
$show_testimonials = get_theme_mod('recruitpro_careers_show_testimonials', true);
$show_perks = get_theme_mod('recruitpro_careers_show_perks', true);

// Company data
$company_size = get_theme_mod('recruitpro_company_size', '');
$founded_year = get_theme_mod('recruitpro_company_founded', '');
$office_locations = get_theme_mod('recruitpro_office_locations', '');

// Schema.org markup for careers page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ?: 'Join our recruitment team and advance your career in the recruitment industry',
    'url' => get_permalink(),
    'isPartOf' => array(
        '@type' => 'WebSite',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'sameAs' => array_filter(array(
            get_theme_mod('recruitpro_social_linkedin', ''),
            get_theme_mod('recruitpro_social_facebook', ''),
            get_theme_mod('recruitpro_social_twitter', ''),
        ))
    )
);

// Get career opportunities (posts in 'career-opportunity' category)
$careers_args = array(
    'post_type' => 'post',
    'posts_per_page' => 10,
    'category_name' => 'career-opportunity',
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish'
);

$careers_query = new WP_Query($careers_args);
?>

<!-- Schema.org WebPage Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main id="primary" class="site-main careers-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="careers-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header careers-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Join our dynamic recruitment team and build a rewarding career helping others achieve their professional goals. We offer exceptional opportunities for growth, competitive compensation, and a supportive work environment.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Why Work With Us -->
                    <section class="why-work-with-us" id="why-work-with-us">
                        <div class="section-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Why Choose a Career With Us?', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Discover what makes our recruitment agency an exceptional place to build your career', 'recruitpro'); ?></p>
                            </div>

                            <div class="why-work-grid">
                                
                                <div class="reason-item">
                                    <div class="reason-content">
                                        <div class="reason-icon">
                                            <i class="fas fa-rocket" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="reason-title"><?php esc_html_e('Career Acceleration', 'recruitpro'); ?></h3>
                                        <p class="reason-description"><?php esc_html_e('Fast-track your career with mentorship programs, professional development opportunities, and clear advancement paths in the recruitment industry.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="reason-item">
                                    <div class="reason-content">
                                        <div class="reason-icon">
                                            <i class="fas fa-users" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="reason-title"><?php esc_html_e('Collaborative Culture', 'recruitpro'); ?></h3>
                                        <p class="reason-description"><?php esc_html_e('Work alongside passionate professionals in a supportive environment that values teamwork, innovation, and mutual success.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="reason-item">
                                    <div class="reason-content">
                                        <div class="reason-icon">
                                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="reason-title"><?php esc_html_e('Unlimited Earning Potential', 'recruitpro'); ?></h3>
                                        <p class="reason-description"><?php esc_html_e('Benefit from competitive base salaries, performance bonuses, and commission structures that reward your success and dedication.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="reason-item">
                                    <div class="reason-content">
                                        <div class="reason-icon">
                                            <i class="fas fa-globe" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="reason-title"><?php esc_html_e('Industry Impact', 'recruitpro'); ?></h3>
                                        <p class="reason-description"><?php esc_html_e('Make a real difference by connecting talented professionals with career-changing opportunities and helping businesses find the right talent.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="reason-item">
                                    <div class="reason-content">
                                        <div class="reason-icon">
                                            <i class="fas fa-laptop" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="reason-title"><?php esc_html_e('Modern Technology', 'recruitpro'); ?></h3>
                                        <p class="reason-description"><?php esc_html_e('Work with cutting-edge recruitment technology, AI-powered tools, and advanced CRM systems that enhance your productivity.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="reason-item">
                                    <div class="reason-content">
                                        <div class="reason-icon">
                                            <i class="fas fa-balance-scale" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="reason-title"><?php esc_html_e('Work-Life Balance', 'recruitpro'); ?></h3>
                                        <p class="reason-description"><?php esc_html_e('Enjoy flexible working arrangements, remote work options, and a culture that values personal well-being and professional success.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

                <!-- Company Stats -->
                <section class="company-stats" id="company-stats">
                    <div class="stats-container">
                        <div class="stats-grid">
                            
                            <div class="stat-item">
                                <div class="stat-number">
                                    <?php 
                                    $team_size = get_theme_mod('recruitpro_team_size', '25+');
                                    echo esc_html($team_size);
                                    ?>
                                </div>
                                <div class="stat-label"><?php esc_html_e('Team Members', 'recruitpro'); ?></div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-number">
                                    <?php 
                                    if ($founded_year) {
                                        echo esc_html(date('Y') - intval($founded_year) . '+');
                                    } else {
                                        echo '10+';
                                    }
                                    ?>
                                </div>
                                <div class="stat-label"><?php esc_html_e('Years Experience', 'recruitpro'); ?></div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-number">
                                    <?php 
                                    $placements = get_theme_mod('recruitpro_annual_placements', '500+');
                                    echo esc_html($placements);
                                    ?>
                                </div>
                                <div class="stat-label"><?php esc_html_e('Annual Placements', 'recruitpro'); ?></div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-number">
                                    <?php 
                                    $locations = $office_locations ? count(explode(',', $office_locations)) : 3;
                                    echo esc_html($locations . '+');
                                    ?>
                                </div>
                                <div class="stat-label"><?php esc_html_e('Office Locations', 'recruitpro'); ?></div>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Current Opportunities -->
                <section class="current-opportunities" id="current-opportunities">
                    <div class="opportunities-container">
                        
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Current Opportunities', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Explore our open positions and find your next career opportunity', 'recruitpro'); ?></p>
                        </div>

                        <?php if ($careers_query->have_posts()) : ?>
                            <div class="opportunities-grid">
                                <?php while ($careers_query->have_posts()) : $careers_query->the_post(); ?>
                                    <article class="opportunity-item">
                                        <div class="opportunity-content">
                                            
                                            <div class="opportunity-header">
                                                <h3 class="opportunity-title">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                
                                                <div class="opportunity-meta">
                                                    <?php
                                                    $department = get_post_meta(get_the_ID(), '_job_department', true);
                                                    $location = get_post_meta(get_the_ID(), '_job_location', true);
                                                    $type = get_post_meta(get_the_ID(), '_job_type', true);
                                                    ?>
                                                    
                                                    <?php if ($department) : ?>
                                                        <span class="opportunity-department"><?php echo esc_html($department); ?></span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($location) : ?>
                                                        <span class="opportunity-location">
                                                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                            <?php echo esc_html($location); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($type) : ?>
                                                        <span class="opportunity-type"><?php echo esc_html($type); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="opportunity-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                                            </div>

                                            <div class="opportunity-requirements">
                                                <?php
                                                $requirements = get_post_meta(get_the_ID(), '_job_requirements', true);
                                                if ($requirements) :
                                                    $req_list = explode(',', $requirements);
                                                    if (count($req_list) > 0) :
                                                ?>
                                                    <h4 class="requirements-title"><?php esc_html_e('Key Requirements:', 'recruitpro'); ?></h4>
                                                    <ul class="requirements-list">
                                                        <?php foreach (array_slice($req_list, 0, 3) as $requirement) : ?>
                                                            <li><?php echo esc_html(trim($requirement)); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php 
                                                    endif;
                                                endif; 
                                                ?>
                                            </div>

                                            <div class="opportunity-actions">
                                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                                    <?php esc_html_e('View Details', 'recruitpro'); ?>
                                                </a>
                                                
                                                <?php
                                                $application_email = get_post_meta(get_the_ID(), '_application_email', true);
                                                if (!$application_email) {
                                                    $application_email = get_theme_mod('recruitpro_careers_email', get_option('admin_email'));
                                                }
                                                ?>
                                                <a href="mailto:<?php echo esc_attr($application_email); ?>?subject=Application for <?php echo esc_attr(get_the_title()); ?>" 
                                                   class="btn btn-secondary">
                                                    <?php esc_html_e('Apply Now', 'recruitpro'); ?>
                                                </a>
                                            </div>

                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                        <?php 
                        else :
                        ?>
                            <!-- Default opportunities when no posts exist -->
                            <div class="opportunities-grid">
                                <article class="opportunity-item">
                                    <div class="opportunity-content">
                                        <div class="opportunity-header">
                                            <h3 class="opportunity-title">
                                                <a href="#"><?php esc_html_e('Senior Recruitment Consultant', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="opportunity-meta">
                                                <span class="opportunity-department"><?php esc_html_e('Recruitment', 'recruitpro'); ?></span>
                                                <span class="opportunity-location">
                                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                    <?php esc_html_e('London, UK', 'recruitpro'); ?>
                                                </span>
                                                <span class="opportunity-type"><?php esc_html_e('Full-time', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="opportunity-excerpt">
                                            <p><?php esc_html_e('Join our dynamic recruitment team as a Senior Consultant, managing high-level executive searches and building strong client relationships across multiple industries.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="opportunity-requirements">
                                            <h4 class="requirements-title"><?php esc_html_e('Key Requirements:', 'recruitpro'); ?></h4>
                                            <ul class="requirements-list">
                                                <li><?php esc_html_e('3+ years recruitment experience', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Strong communication skills', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Target-driven mindset', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="opportunity-actions">
                                            <a href="#" class="btn btn-primary"><?php esc_html_e('View Details', 'recruitpro'); ?></a>
                                            <a href="mailto:careers@example.com?subject=Senior Recruitment Consultant Application" class="btn btn-secondary"><?php esc_html_e('Apply Now', 'recruitpro'); ?></a>
                                        </div>
                                    </div>
                                </article>

                                <article class="opportunity-item">
                                    <div class="opportunity-content">
                                        <div class="opportunity-header">
                                            <h3 class="opportunity-title">
                                                <a href="#"><?php esc_html_e('Junior Recruitment Consultant', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="opportunity-meta">
                                                <span class="opportunity-department"><?php esc_html_e('Recruitment', 'recruitpro'); ?></span>
                                                <span class="opportunity-location">
                                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                    <?php esc_html_e('Manchester, UK', 'recruitpro'); ?>
                                                </span>
                                                <span class="opportunity-type"><?php esc_html_e('Full-time', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="opportunity-excerpt">
                                            <p><?php esc_html_e('Start your recruitment career with comprehensive training, mentorship, and support. Perfect opportunity for graduates or career changers entering recruitment.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="opportunity-requirements">
                                            <h4 class="requirements-title"><?php esc_html_e('Key Requirements:', 'recruitpro'); ?></h4>
                                            <ul class="requirements-list">
                                                <li><?php esc_html_e('Graduate or equivalent experience', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Excellent communication skills', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Ambitious and driven', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="opportunity-actions">
                                            <a href="#" class="btn btn-primary"><?php esc_html_e('View Details', 'recruitpro'); ?></a>
                                            <a href="mailto:careers@example.com?subject=Junior Recruitment Consultant Application" class="btn btn-secondary"><?php esc_html_e('Apply Now', 'recruitpro'); ?></a>
                                        </div>
                                    </div>
                                </article>

                                <article class="opportunity-item">
                                    <div class="opportunity-content">
                                        <div class="opportunity-header">
                                            <h3 class="opportunity-title">
                                                <a href="#"><?php esc_html_e('HR Operations Specialist', 'recruitpro'); ?></a>
                                            </h3>
                                            <div class="opportunity-meta">
                                                <span class="opportunity-department"><?php esc_html_e('Human Resources', 'recruitpro'); ?></span>
                                                <span class="opportunity-location">
                                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                    <?php esc_html_e('Remote/Hybrid', 'recruitpro'); ?>
                                                </span>
                                                <span class="opportunity-type"><?php esc_html_e('Full-time', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        <div class="opportunity-excerpt">
                                            <p><?php esc_html_e('Support our internal HR operations with employee onboarding, compliance, and administrative support. Great opportunity for HR professionals.', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="opportunity-requirements">
                                            <h4 class="requirements-title"><?php esc_html_e('Key Requirements:', 'recruitpro'); ?></h4>
                                            <ul class="requirements-list">
                                                <li><?php esc_html_e('HR degree or CIPD qualification', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('2+ years HR experience', 'recruitpro'); ?></li>
                                                <li><?php esc_html_e('Detail-oriented and organized', 'recruitpro'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="opportunity-actions">
                                            <a href="#" class="btn btn-primary"><?php esc_html_e('View Details', 'recruitpro'); ?></a>
                                            <a href="mailto:careers@example.com?subject=HR Operations Specialist Application" class="btn btn-secondary"><?php esc_html_e('Apply Now', 'recruitpro'); ?></a>
                                        </div>
                                    </div>
                                </article>
                            </div>

                            <?php if (current_user_can('edit_posts')) : ?>
                                <div class="opportunities-placeholder">
                                    <p><em><?php esc_html_e('Create career opportunity posts in the "Career Opportunity" category to display current openings.', 'recruitpro'); ?></em></p>
                                </div>
                            <?php endif; ?>
                        <?php 
                        endif;
                        wp_reset_postdata();
                        ?>

                    </div>
                </section>

                <!-- Benefits & Perks -->
                <?php if ($show_benefits || $show_perks) : ?>
                    <section class="benefits-perks" id="benefits-perks">
                        <div class="benefits-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Benefits & Perks', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Comprehensive benefits package designed to support your career and well-being', 'recruitpro'); ?></p>
                            </div>

                            <div class="benefits-grid">
                                
                                <div class="benefit-category">
                                    <h3 class="category-title"><?php esc_html_e('Financial Benefits', 'recruitpro'); ?></h3>
                                    <ul class="benefits-list">
                                        <li>
                                            <i class="fas fa-pound-sign" aria-hidden="true"></i>
                                            <?php esc_html_e('Competitive base salary + commission', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-gift" aria-hidden="true"></i>
                                            <?php esc_html_e('Performance bonuses and incentives', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-piggy-bank" aria-hidden="true"></i>
                                            <?php esc_html_e('Company pension scheme', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                                            <?php esc_html_e('Share options for senior roles', 'recruitpro'); ?>
                                        </li>
                                    </ul>
                                </div>

                                <div class="benefit-category">
                                    <h3 class="category-title"><?php esc_html_e('Health & Wellness', 'recruitpro'); ?></h3>
                                    <ul class="benefits-list">
                                        <li>
                                            <i class="fas fa-heartbeat" aria-hidden="true"></i>
                                            <?php esc_html_e('Private healthcare insurance', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-tooth" aria-hidden="true"></i>
                                            <?php esc_html_e('Dental and optical coverage', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-dumbbell" aria-hidden="true"></i>
                                            <?php esc_html_e('Gym membership allowance', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-spa" aria-hidden="true"></i>
                                            <?php esc_html_e('Mental health support programs', 'recruitpro'); ?>
                                        </li>
                                    </ul>
                                </div>

                                <div class="benefit-category">
                                    <h3 class="category-title"><?php esc_html_e('Work-Life Balance', 'recruitpro'); ?></h3>
                                    <ul class="benefits-list">
                                        <li>
                                            <i class="fas fa-home" aria-hidden="true"></i>
                                            <?php esc_html_e('Flexible working arrangements', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-calendar-plus" aria-hidden="true"></i>
                                            <?php esc_html_e('25 days holiday + bank holidays', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-baby" aria-hidden="true"></i>
                                            <?php esc_html_e('Enhanced parental leave', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                            <?php esc_html_e('Flexible start and finish times', 'recruitpro'); ?>
                                        </li>
                                    </ul>
                                </div>

                                <div class="benefit-category">
                                    <h3 class="category-title"><?php esc_html_e('Professional Development', 'recruitpro'); ?></h3>
                                    <ul class="benefits-list">
                                        <li>
                                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                            <?php esc_html_e('Professional training programs', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-certificate" aria-hidden="true"></i>
                                            <?php esc_html_e('Industry certification support', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-users-cog" aria-hidden="true"></i>
                                            <?php esc_html_e('Mentorship and coaching', 'recruitpro'); ?>
                                        </li>
                                        <li>
                                            <i class="fas fa-rocket" aria-hidden="true"></i>
                                            <?php esc_html_e('Clear career progression paths', 'recruitpro'); ?>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Team Testimonials -->
                <?php if ($show_testimonials) : ?>
                    <section class="team-testimonials" id="team-testimonials">
                        <div class="testimonials-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('What Our Team Says', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Hear from our team members about their experience working with us', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Employee testimonials query
                            $testimonials_args = array(
                                'post_type' => 'testimonial',
                                'posts_per_page' => 3,
                                'meta_query' => array(
                                    array(
                                        'key' => '_testimonial_type',
                                        'value' => 'employee',
                                        'compare' => '='
                                    )
                                ),
                                'orderby' => 'rand'
                            );

                            $testimonials_query = new WP_Query($testimonials_args);

                            if ($testimonials_query->have_posts()) :
                            ?>
                                <div class="testimonials-grid">
                                    <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post(); ?>
                                        <div class="testimonial-item">
                                            <div class="testimonial-content">
                                                
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <div class="testimonial-avatar">
                                                        <?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="testimonial-text">
                                                    <blockquote>
                                                        <p><?php echo wp_kses_post(get_the_content()); ?></p>
                                                    </blockquote>
                                                </div>

                                                <div class="testimonial-author">
                                                    <h4 class="author-name"><?php the_title(); ?></h4>
                                                    <?php
                                                    $position = get_post_meta(get_the_ID(), '_author_position', true);
                                                    if ($position) :
                                                    ?>
                                                        <span class="author-position"><?php echo esc_html($position); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php 
                            else :
                            ?>
                                <!-- Default employee testimonials -->
                                <div class="testimonials-grid">
                                    <div class="testimonial-item">
                                        <div class="testimonial-content">
                                            <div class="testimonial-text">
                                                <blockquote>
                                                    <p><?php esc_html_e('The support and mentorship I\'ve received here has been incredible. In just two years, I\'ve progressed from junior consultant to team leader, and the learning opportunities are endless.', 'recruitpro'); ?></p>
                                                </blockquote>
                                            </div>
                                            <div class="testimonial-author">
                                                <h4 class="author-name"><?php esc_html_e('Sarah Johnson', 'recruitpro'); ?></h4>
                                                <span class="author-position"><?php esc_html_e('Senior Recruitment Consultant', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="testimonial-item">
                                        <div class="testimonial-content">
                                            <div class="testimonial-text">
                                                <blockquote>
                                                    <p><?php esc_html_e('The collaborative culture and focus on innovation makes every day exciting. I love working with cutting-edge technology and being part of a forward-thinking team.', 'recruitpro'); ?></p>
                                                </blockquote>
                                            </div>
                                            <div class="testimonial-author">
                                                <h4 class="author-name"><?php esc_html_e('Michael Chen', 'recruitpro'); ?></h4>
                                                <span class="author-position"><?php esc_html_e('Technology Recruitment Specialist', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="testimonial-item">
                                        <div class="testimonial-content">
                                            <div class="testimonial-text">
                                                <blockquote>
                                                    <p><?php esc_html_e('The work-life balance is fantastic, and the earning potential is truly unlimited. I\'ve achieved more here in three years than I did in my previous decade of recruitment.', 'recruitpro'); ?></p>
                                                </blockquote>
                                            </div>
                                            <div class="testimonial-author">
                                                <h4 class="author-name"><?php esc_html_e('Emma Wilson', 'recruitpro'); ?></h4>
                                                <span class="author-position"><?php esc_html_e('Director of Client Services', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="testimonials-placeholder">
                                        <p><em><?php esc_html_e('Create employee testimonial posts to showcase team member experiences.', 'recruitpro'); ?></em></p>
                                    </div>
                                <?php endif; ?>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Application Process -->
                <section class="application-process" id="application-process">
                    <div class="process-container">
                        
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Application Process', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Simple, straightforward steps to join our team', 'recruitpro'); ?></p>
                        </div>

                        <div class="process-steps">
                            
                            <div class="process-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h3 class="step-title"><?php esc_html_e('Submit Application', 'recruitpro'); ?></h3>
                                    <p class="step-description"><?php esc_html_e('Send your CV and cover letter for your chosen position. We review all applications thoroughly.', 'recruitpro'); ?></p>
                                </div>
                            </div>

                            <div class="process-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h3 class="step-title"><?php esc_html_e('Initial Screening', 'recruitpro'); ?></h3>
                                    <p class="step-description"><?php esc_html_e('Phone or video call to discuss your background, experience, and career aspirations.', 'recruitpro'); ?></p>
                                </div>
                            </div>

                            <div class="process-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h3 class="step-title"><?php esc_html_e('Face-to-Face Interview', 'recruitpro'); ?></h3>
                                    <p class="step-description"><?php esc_html_e('Meet the team and learn more about the role, company culture, and growth opportunities.', 'recruitpro'); ?></p>
                                </div>
                            </div>

                            <div class="process-step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h3 class="step-title"><?php esc_html_e('Final Decision', 'recruitpro'); ?></h3>
                                    <p class="step-description"><?php esc_html_e('Reference checks, offer discussion, and onboarding planning for successful candidates.', 'recruitpro'); ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Call to Action -->
                <section class="careers-cta" id="careers-cta">
                    <div class="cta-container">
                        <div class="cta-content">
                            <h2 class="cta-title"><?php esc_html_e('Ready to Start Your Recruitment Career?', 'recruitpro'); ?></h2>
                            <p class="cta-description"><?php esc_html_e('Join our dynamic team and build a rewarding career in the recruitment industry. We\'re always looking for talented individuals who share our passion for connecting people with opportunities.', 'recruitpro'); ?></p>
                            
                            <div class="cta-actions">
                                <?php
                                $careers_email = get_theme_mod('recruitpro_careers_email', get_option('admin_email'));
                                ?>
                                <a href="mailto:<?php echo esc_attr($careers_email); ?>?subject=Career Inquiry" class="btn btn-primary">
                                    <?php esc_html_e('Send Your CV', 'recruitpro'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-secondary">
                                    <?php esc_html_e('Get in Touch', 'recruitpro'); ?>
                                </a>
                            </div>

                            <div class="cta-note">
                                <p><small><?php esc_html_e('Don\'t see the perfect role? Send us your CV anyway - we\'re always interested in hearing from talented recruitment professionals.', 'recruitpro'); ?></small></p>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('careers-sidebar')) : ?>
                <aside id="secondary" class="widget-area careers-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('careers-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   CAREERS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL CAREERS PAGE FEATURES:

✅ RECRUITMENT AGENCY EMPLOYMENT
- Internal job opportunities
- Career progression paths
- Professional development focus
- Team member testimonials
- Company culture showcase

✅ COMPREHENSIVE BENEFITS
- Financial benefits and commission
- Health and wellness programs
- Work-life balance initiatives
- Professional development support
- Career advancement opportunities

✅ CURRENT OPPORTUNITIES
- Dynamic job listings from posts
- Department and location info
- Key requirements display
- Direct application links
- Detailed job descriptions

✅ COMPANY CULTURE
- Why work with us section
- Team statistics and achievements
- Employee testimonials
- Professional environment showcase
- Values and mission alignment

✅ APPLICATION PROCESS
- Clear step-by-step guidance
- Transparent hiring process
- Multiple contact methods
- Direct email applications
- Professional communication

✅ SCHEMA.ORG MARKUP
- WebPage schema optimization
- Organization information
- Job posting compatibility
- SEO-friendly structure

✅ RESPONSIVE DESIGN
- Mobile-first approach
- Touch-friendly interface
- Optimized for all devices
- Professional presentation

✅ CUSTOMIZATION OPTIONS
- Show/hide sections
- Sidebar positioning
- Content management
- Email configuration
- Layout control

PERFECT FOR:
- Attracting recruitment talent
- Internal team building
- Professional credibility
- Career-focused branding
- Talent acquisition

CONTENT MANAGEMENT:
- Career opportunity posts
- Employee testimonials
- Benefits customization
- Company statistics
- Contact information

*/
?>