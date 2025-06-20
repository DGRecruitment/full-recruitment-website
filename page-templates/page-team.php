<?php
/**
 * Template Name: Team Page
 *
 * Professional team page template for recruitment agencies showcasing
 * recruitment consultants, HR specialists, and support staff. Features
 * detailed team member profiles, department organization, specializations,
 * and professional credentials designed to build trust and credibility.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-team.php
 * Purpose: Team members showcase and professional profiles
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Team profiles, departments, specializations, contact information
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_team_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_team_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_departments = get_theme_mod('recruitpro_team_show_departments', true);
$show_specializations = get_theme_mod('recruitpro_team_show_specializations', true);
$show_achievements = get_theme_mod('recruitpro_team_show_achievements', true);

// Team organization settings
$team_layout = get_theme_mod('recruitpro_team_layout', 'grid');
$members_per_page = get_theme_mod('recruitpro_team_per_page', -1);
$show_contact_info = get_theme_mod('recruitpro_team_show_contact', true);
$show_bio_excerpts = get_theme_mod('recruitpro_team_show_bio', true);
$enable_filtering = get_theme_mod('recruitpro_team_enable_filtering', true);

// Schema.org markup for team page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Meet our professional recruitment team at %s. Experienced consultants specializing in talent acquisition across multiple industries and sectors.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Recruitment Team',
        'description' => 'Professional recruitment consultants and HR specialists'
    )
);

// Get team statistics
$total_team_members = 0;
$departments = array();
$specializations = array();

// Check if team_member post type exists
$team_exists = post_type_exists('team_member');
if ($team_exists) {
    $team_count = wp_count_posts('team_member');
    $total_team_members = $team_count->publish;
    
    // Get departments and specializations for filtering
    $departments = get_terms(array(
        'taxonomy' => 'team_department',
        'hide_empty' => true,
        'orderby' => 'name'
    ));
    
    $specializations = get_terms(array(
        'taxonomy' => 'team_specialization', 
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC'
    ));
}
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main team-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header team-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Meet our experienced team of recruitment professionals dedicated to connecting exceptional talent with outstanding opportunities. Our consultants bring deep industry knowledge and proven track records in talent acquisition.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Team Statistics -->
                            <div class="team-stats">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo esc_html($total_team_members); ?>+</div>
                                        <div class="stat-label"><?php esc_html_e('Team Members', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">15+</div>
                                        <div class="stat-label"><?php esc_html_e('Years Experience', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">50+</div>
                                        <div class="stat-label"><?php esc_html_e('Industries Covered', 'recruitpro'); ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">95%</div>
                                        <div class="stat-label"><?php esc_html_e('Success Rate', 'recruitpro'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </header>
                    <?php endif; ?>

                    <!-- Team Filters -->
                    <?php if ($enable_filtering && ($departments || $specializations)) : ?>
                        <section class="team-filters" id="team-filters">
                            <div class="filters-container">
                                <h2 class="filters-title"><?php esc_html_e('Filter Our Team', 'recruitpro'); ?></h2>
                                
                                <div class="filter-groups">
                                    
                                    <!-- Department Filters -->
                                    <?php if ($departments && $show_departments) : ?>
                                        <div class="filter-group">
                                            <h3 class="filter-group-title"><?php esc_html_e('By Department', 'recruitpro'); ?></h3>
                                            <div class="filter-buttons">
                                                <button class="filter-btn active" data-filter="all" data-type="department">
                                                    <?php esc_html_e('All Departments', 'recruitpro'); ?>
                                                </button>
                                                <?php foreach ($departments as $department) : ?>
                                                    <button class="filter-btn" data-filter="<?php echo esc_attr($department->slug); ?>" data-type="department">
                                                        <i class="fas fa-users" aria-hidden="true"></i>
                                                        <?php echo esc_html($department->name); ?>
                                                        <span class="count">(<?php echo esc_html($department->count); ?>)</span>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Specialization Filters -->
                                    <?php if ($specializations && $show_specializations) : ?>
                                        <div class="filter-group">
                                            <h3 class="filter-group-title"><?php esc_html_e('By Specialization', 'recruitpro'); ?></h3>
                                            <div class="filter-buttons">
                                                <button class="filter-btn active" data-filter="all" data-type="specialization">
                                                    <?php esc_html_e('All Specializations', 'recruitpro'); ?>
                                                </button>
                                                <?php foreach ($specializations as $specialization) : ?>
                                                    <button class="filter-btn" data-filter="<?php echo esc_attr($specialization->slug); ?>" data-type="specialization">
                                                        <i class="fas fa-briefcase" aria-hidden="true"></i>
                                                        <?php echo esc_html($specialization->name); ?>
                                                        <span class="count">(<?php echo esc_html($specialization->count); ?>)</span>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Team Members Section -->
                    <section class="team-members" id="team-members">
                        <div class="team-container">
                            
                            <?php if ($team_exists) : ?>
                                <?php
                                // Query team members
                                $team_args = array(
                                    'post_type' => 'team_member',
                                    'posts_per_page' => $members_per_page,
                                    'post_status' => 'publish',
                                    'meta_key' => '_team_order',
                                    'orderby' => array(
                                        'meta_value_num' => 'ASC',
                                        'date' => 'DESC'
                                    ),
                                    'order' => 'ASC'
                                );

                                $team_query = new WP_Query($team_args);

                                if ($team_query->have_posts()) :
                                ?>
                                    <div class="team-grid <?php echo esc_attr($team_layout); ?>">
                                        
                                        <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
                                            <?php
                                            // Get team member meta data
                                            $position = get_post_meta(get_the_ID(), '_team_position', true);
                                            $department = get_post_meta(get_the_ID(), '_team_department', true);
                                            $experience_years = get_post_meta(get_the_ID(), '_team_experience', true);
                                            $specialization = get_post_meta(get_the_ID(), '_team_specialization', true);
                                            $certifications = get_post_meta(get_the_ID(), '_team_certifications', true);
                                            $languages = get_post_meta(get_the_ID(), '_team_languages', true);
                                            $achievements = get_post_meta(get_the_ID(), '_team_achievements', true);
                                            
                                            // Contact information
                                            $email = get_post_meta(get_the_ID(), '_team_email', true);
                                            $phone = get_post_meta(get_the_ID(), '_team_phone', true);
                                            $linkedin = get_post_meta(get_the_ID(), '_team_linkedin', true);
                                            $direct_dial = get_post_meta(get_the_ID(), '_team_direct_dial', true);
                                            
                                            // Get taxonomies
                                            $member_departments = get_the_terms(get_the_ID(), 'team_department');
                                            $member_specializations = get_the_terms(get_the_ID(), 'team_specialization');
                                            
                                            // Build filter classes
                                            $filter_classes = array();
                                            if ($member_departments) {
                                                foreach ($member_departments as $dept) {
                                                    $filter_classes[] = 'dept-' . $dept->slug;
                                                }
                                            }
                                            if ($member_specializations) {
                                                foreach ($member_specializations as $spec) {
                                                    $filter_classes[] = 'spec-' . $spec->slug;
                                                }
                                            }
                                            ?>
                                            
                                            <article class="team-member-card <?php echo esc_attr(implode(' ', $filter_classes)); ?>" 
                                                     itemscope itemtype="https://schema.org/Person">
                                                
                                                <!-- Member Photo -->
                                                <div class="member-photo">
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <?php the_post_thumbnail('recruitpro-team-large', array(
                                                            'alt' => get_the_title(),
                                                            'itemprop' => 'image'
                                                        )); ?>
                                                    <?php else : ?>
                                                        <div class="placeholder-photo">
                                                            <i class="fas fa-user" aria-hidden="true"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Status Indicator -->
                                                    <div class="member-status">
                                                        <span class="status-indicator available">
                                                            <i class="fas fa-circle" aria-hidden="true"></i>
                                                            <?php esc_html_e('Available', 'recruitpro'); ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Member Information -->
                                                <div class="member-info">
                                                    
                                                    <!-- Basic Info -->
                                                    <div class="member-basic">
                                                        <h3 class="member-name" itemprop="name"><?php the_title(); ?></h3>
                                                        
                                                        <?php if ($position) : ?>
                                                            <p class="member-position" itemprop="jobTitle"><?php echo esc_html($position); ?></p>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($department) : ?>
                                                            <p class="member-department">
                                                                <i class="fas fa-building" aria-hidden="true"></i>
                                                                <span itemprop="department"><?php echo esc_html($department); ?></span>
                                                            </p>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($experience_years) : ?>
                                                            <p class="member-experience">
                                                                <i class="fas fa-calendar" aria-hidden="true"></i>
                                                                <?php printf(esc_html(_n('%d year experience', '%d years experience', $experience_years, 'recruitpro')), $experience_years); ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Bio/Excerpt -->
                                                    <?php if ($show_bio_excerpts && (has_excerpt() || get_the_content())) : ?>
                                                        <div class="member-bio" itemprop="description">
                                                            <?php if (has_excerpt()) : ?>
                                                                <?php the_excerpt(); ?>
                                                            <?php else : ?>
                                                                <p><?php echo esc_html(wp_trim_words(get_the_content(), 25)); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Specializations -->
                                                    <?php if ($member_specializations && $show_specializations) : ?>
                                                        <div class="member-specializations">
                                                            <h4 class="specializations-title"><?php esc_html_e('Specializations:', 'recruitpro'); ?></h4>
                                                            <div class="specialization-tags">
                                                                <?php foreach ($member_specializations as $spec) : ?>
                                                                    <span class="specialization-tag" itemprop="knowsAbout">
                                                                        <?php echo esc_html($spec->name); ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Key Achievements -->
                                                    <?php if ($achievements && $show_achievements) : ?>
                                                        <div class="member-achievements">
                                                            <h4 class="achievements-title"><?php esc_html_e('Key Achievements:', 'recruitpro'); ?></h4>
                                                            <ul class="achievements-list">
                                                                <?php 
                                                                $achievement_items = explode("\n", $achievements);
                                                                foreach ($achievement_items as $achievement) :
                                                                    if (trim($achievement)) :
                                                                ?>
                                                                    <li class="achievement-item">
                                                                        <i class="fas fa-award" aria-hidden="true"></i>
                                                                        <?php echo esc_html(trim($achievement)); ?>
                                                                    </li>
                                                                <?php 
                                                                    endif;
                                                                endforeach; 
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Professional Details -->
                                                    <div class="member-details">
                                                        
                                                        <!-- Certifications -->
                                                        <?php if ($certifications) : ?>
                                                            <div class="detail-item">
                                                                <span class="detail-label">
                                                                    <i class="fas fa-certificate" aria-hidden="true"></i>
                                                                    <?php esc_html_e('Certifications:', 'recruitpro'); ?>
                                                                </span>
                                                                <span class="detail-value"><?php echo esc_html($certifications); ?></span>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- Languages -->
                                                        <?php if ($languages) : ?>
                                                            <div class="detail-item">
                                                                <span class="detail-label">
                                                                    <i class="fas fa-globe" aria-hidden="true"></i>
                                                                    <?php esc_html_e('Languages:', 'recruitpro'); ?>
                                                                </span>
                                                                <span class="detail-value" itemprop="knowsLanguage"><?php echo esc_html($languages); ?></span>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>

                                                    <!-- Contact Information -->
                                                    <?php if ($show_contact_info && ($email || $phone || $linkedin || $direct_dial)) : ?>
                                                        <div class="member-contact">
                                                            <h4 class="contact-title"><?php esc_html_e('Contact Information', 'recruitpro'); ?></h4>
                                                            <div class="contact-methods">
                                                                
                                                                <?php if ($email) : ?>
                                                                    <a href="mailto:<?php echo esc_attr($email); ?>" 
                                                                       class="contact-method email" 
                                                                       itemprop="email"
                                                                       aria-label="<?php echo esc_attr(sprintf(__('Email %s', 'recruitpro'), get_the_title())); ?>">
                                                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                                                        <span class="contact-label"><?php esc_html_e('Email', 'recruitpro'); ?></span>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($phone) : ?>
                                                                    <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $phone)); ?>" 
                                                                       class="contact-method phone"
                                                                       itemprop="telephone"
                                                                       aria-label="<?php echo esc_attr(sprintf(__('Call %s', 'recruitpro'), get_the_title())); ?>">
                                                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                                                        <span class="contact-label"><?php esc_html_e('Phone', 'recruitpro'); ?></span>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($direct_dial) : ?>
                                                                    <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $direct_dial)); ?>" 
                                                                       class="contact-method direct-dial"
                                                                       aria-label="<?php echo esc_attr(sprintf(__('Direct dial %s', 'recruitpro'), get_the_title())); ?>">
                                                                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                                                        <span class="contact-label"><?php esc_html_e('Direct', 'recruitpro'); ?></span>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($linkedin) : ?>
                                                                    <a href="<?php echo esc_url($linkedin); ?>" 
                                                                       class="contact-method linkedin" 
                                                                       target="_blank" 
                                                                       rel="noopener noreferrer"
                                                                       itemprop="sameAs"
                                                                       aria-label="<?php echo esc_attr(sprintf(__('LinkedIn profile of %s', 'recruitpro'), get_the_title())); ?>">
                                                                        <i class="fab fa-linkedin" aria-hidden="true"></i>
                                                                        <span class="contact-label"><?php esc_html_e('LinkedIn', 'recruitpro'); ?></span>
                                                                    </a>
                                                                <?php endif; ?>

                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Member Actions -->
                                                    <div class="member-actions">
                                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                                            <?php esc_html_e('View Full Profile', 'recruitpro'); ?>
                                                        </a>
                                                        <?php if ($email) : ?>
                                                            <a href="mailto:<?php echo esc_attr($email); ?>?subject=<?php echo esc_attr(sprintf(__('Inquiry via %s website', 'recruitpro'), $company_name)); ?>" 
                                                               class="btn btn-secondary">
                                                                <?php esc_html_e('Get in Touch', 'recruitpro'); ?>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>

                                            </article>

                                        <?php endwhile; ?>
                                        
                                    </div>

                                    <!-- Pagination -->
                                    <?php if ($team_query->max_num_pages > 1) : ?>
                                        <div class="team-pagination">
                                            <?php
                                            echo paginate_links(array(
                                                'total' => $team_query->max_num_pages,
                                                'current' => max(1, get_query_var('paged')),
                                                'format' => '?paged=%#%',
                                                'show_all' => false,
                                                'type' => 'list',
                                                'end_size' => 3,
                                                'mid_size' => 3,
                                                'prev_next' => true,
                                                'prev_text' => __('« Previous', 'recruitpro'),
                                                'next_text' => __('Next »', 'recruitpro'),
                                                'add_args' => false,
                                                'add_fragment' => '#team-members',
                                            ));
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                <?php
                                wp_reset_postdata();
                                else :
                                ?>
                                    <!-- No team members found -->
                                    <div class="no-team-members">
                                        <div class="no-content-message">
                                            <i class="fas fa-users" aria-hidden="true"></i>
                                            <h3><?php esc_html_e('Team Information Coming Soon', 'recruitpro'); ?></h3>
                                            <p><?php esc_html_e('We\'re currently updating our team profiles. Please check back soon or contact us directly to learn more about our recruitment professionals.', 'recruitpro'); ?></p>
                                            <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary">
                                                <?php esc_html_e('Contact Our Team', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php else : ?>
                                <!-- Fallback content when team post type doesn't exist -->
                                <div class="team-fallback">
                                    <div class="fallback-content">
                                        <h2><?php esc_html_e('Our Expert Team', 'recruitpro'); ?></h2>
                                        <p><?php esc_html_e('Our experienced team of recruitment professionals brings together decades of combined experience in talent acquisition, HR consulting, and industry expertise. We are committed to delivering exceptional results for both candidates and clients.', 'recruitpro'); ?></p>
                                        
                                        <div class="team-highlights">
                                            <div class="highlight-item">
                                                <h3><?php esc_html_e('Senior Consultants', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Experienced recruitment professionals with deep industry knowledge and proven track records in executive search and talent acquisition.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="highlight-item">
                                                <h3><?php esc_html_e('Industry Specialists', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Dedicated specialists focusing on specific industries and roles, ensuring expert knowledge and targeted recruitment approaches.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="highlight-item">
                                                <h3><?php esc_html_e('Support Team', 'recruitpro'); ?></h3>
                                                <p><?php esc_html_e('Professional support staff ensuring smooth processes, excellent client service, and comprehensive candidate experience.', 'recruitpro'); ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="team-cta">
                                            <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                                <?php esc_html_e('Meet Our Team', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </section>

                    <!-- Team Achievements -->
                    <?php if ($show_achievements) : ?>
                        <section class="team-achievements" id="team-achievements">
                            <div class="achievements-container">
                                <h2 class="section-title"><?php esc_html_e('Team Achievements & Recognition', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Our team\'s professional accomplishments and industry recognition', 'recruitpro'); ?></p>
                                
                                <div class="achievements-grid">
                                    
                                    <div class="achievement-item">
                                        <div class="achievement-icon">
                                            <i class="fas fa-award" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="achievement-title"><?php esc_html_e('Industry Awards', 'recruitpro'); ?></h3>
                                        <p class="achievement-description"><?php esc_html_e('Multiple team members recognized with industry excellence awards and professional certifications.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="achievement-item">
                                        <div class="achievement-icon">
                                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="achievement-title"><?php esc_html_e('Professional Certifications', 'recruitpro'); ?></h3>
                                        <p class="achievement-description"><?php esc_html_e('Certified professionals with credentials from leading HR and recruitment organizations.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="achievement-item">
                                        <div class="achievement-icon">
                                            <i class="fas fa-handshake" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="achievement-title"><?php esc_html_e('Client Success Rate', 'recruitpro'); ?></h3>
                                        <p class="achievement-description"><?php esc_html_e('Consistently achieving 95%+ placement success rates and exceptional client satisfaction scores.', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="achievement-item">
                                        <div class="achievement-icon">
                                            <i class="fas fa-globe" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="achievement-title"><?php esc_html_e('Global Expertise', 'recruitpro'); ?></h3>
                                        <p class="achievement-description"><?php esc_html_e('International recruitment experience with multilingual capabilities and global market knowledge.', 'recruitpro'); ?></p>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Contact Our Team -->
                    <section class="team-contact-cta" id="team-contact">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Ready to Work with Our Team?', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Connect with our recruitment experts to discuss your talent needs or career opportunities. Our team is ready to provide personalized solutions and professional guidance.', 'recruitpro'); ?></p>
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>" class="btn btn-primary btn-lg">
                                        <?php esc_html_e('Contact Our Team', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_page_link(get_page_by_path('services'))); ?>" class="btn btn-secondary btn-lg">
                                        <?php esc_html_e('View Our Services', 'recruitpro'); ?>
                                    </a>
                                </div>
                                <div class="cta-contact-info">
                                    <span class="contact-item">
                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                        <a href="tel:+1234567890"><?php esc_html_e('Call: (123) 456-7890', 'recruitpro'); ?></a>
                                    </span>
                                    <span class="contact-item">
                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                        <a href="mailto:team@example.com"><?php esc_html_e('Email: team@example.com', 'recruitpro'); ?></a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('team-sidebar')) : ?>
                <aside id="secondary" class="widget-area team-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('team-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   TEAM PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE TEAM PAGE FEATURES:

✅ PROFESSIONAL TEAM SHOWCASE
- Individual team member cards with photos and detailed profiles
- Position titles, departments, and years of experience
- Professional bio excerpts and full descriptions
- Specializations and areas of expertise display
- Contact information with multiple communication methods

✅ ADVANCED FILTERING SYSTEM
- Filter by department (e.g., Executive Search, Contract, Temp)
- Filter by specialization (e.g., Technology, Finance, Healthcare)
- Dynamic filter counts and interactive buttons
- Real-time content filtering without page refresh

✅ COMPREHENSIVE MEMBER PROFILES
- Professional photos with placeholder fallbacks
- Job titles, departments, and experience levels
- Specialization tags and areas of expertise
- Key achievements and professional accomplishments
- Certifications and professional credentials
- Language capabilities and international experience

✅ CONTACT INTEGRATION
- Direct email links with pre-filled subjects
- Phone numbers with click-to-call functionality
- Direct dial numbers for immediate access
- LinkedIn profile integration
- Professional contact method organization

✅ SCHEMA.ORG OPTIMIZATION
- Person schema markup for each team member
- Professional relationship and organization data
- Contact information and social profile linking
- SEO-optimized structured data implementation

✅ TEAM STATISTICS & ACHIEVEMENTS
- Total team member count and experience metrics
- Success rate and client satisfaction statistics
- Industry coverage and global expertise highlights
- Professional recognition and award displays

✅ RESPONSIVE DESIGN & ACCESSIBILITY
- Mobile-first responsive grid layouts
- Touch-friendly interface elements
- Screen reader optimization and ARIA labels
- Keyboard navigation support

✅ CONTENT MANAGEMENT INTEGRATION
- Custom post type support for team members
- Taxonomy integration for departments and specializations
- WordPress customizer theme options
- Conditional content display based on settings

✅ PROFESSIONAL PRESENTATION
- Clean, modern card-based layout design
- Professional color scheme and typography
- Visual status indicators and availability
- Organized information hierarchy

✅ BUSINESS FUNCTIONALITY
- Lead generation through team member contact
- Professional credibility and trust building
- Expertise demonstration and specialization showcase
- Client relationship facilitation

PERFECT FOR:
- Recruitment agencies and executive search firms
- HR consultancies and staffing companies
- Professional services organizations
- Talent acquisition teams
- Business development and client relations

BUSINESS BENEFITS:
- Enhanced professional credibility and trust
- Direct client-consultant relationship building
- Expertise and specialization demonstration
- Lead generation through team member profiles
- Improved client experience and accessibility

RECRUITMENT INDUSTRY SPECIFIC:
- Consultant specialization and industry expertise
- Years of experience and track record display
- Professional certification and credential showcase
- Department organization and service area coverage
- Client relationship management and accessibility

TECHNICAL FEATURES:
- WordPress custom post type integration
- Advanced filtering and taxonomy support
- Schema.org markup for SEO optimization
- Responsive design and mobile optimization
- Performance optimized queries and loading

*/
?>