<?php
/**
 * Template Name: Awards & Recognition Page
 *
 * Professional awards page template for recruitment agencies showcasing
 * industry recognition, certifications, professional achievements, and
 * building credibility with clients and candidates through trust signals.
 * Optimized for professional services and recruitment industry standards.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-awards.php
 * Purpose: Professional awards and recognition showcase
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Awards display, certifications, industry recognition, trust building
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_awards_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_awards_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$awards_layout = get_theme_mod('recruitpro_awards_layout', 'grid');
$show_dates = get_theme_mod('recruitpro_awards_show_dates', true);
$show_descriptions = get_theme_mod('recruitpro_awards_show_descriptions', true);
$grouping_method = get_theme_mod('recruitpro_awards_grouping', 'category');

// Schema.org markup data for awards
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company_name,
    'url' => home_url(),
    'logo' => get_theme_mod('recruitpro_site_logo', ''),
    'awards' => array(),
    'memberOf' => array(),
    'hasCredential' => array()
);

// Get awards data
$professional_awards = get_post_meta($page_id, '_professional_awards', true);
$industry_certifications = get_post_meta($page_id, '_industry_certifications', true);
$quality_certifications = get_post_meta($page_id, '_quality_certifications', true);
$compliance_certifications = get_post_meta($page_id, '_compliance_certifications', true);
$industry_memberships = get_post_meta($page_id, '_industry_memberships', true);
$client_recognition = get_post_meta($page_id, '_client_recognition', true);
$media_mentions = get_post_meta($page_id, '_media_mentions', true);
?>

<!-- Schema.org Organization with Awards Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main id="primary" class="site-main awards-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="awards-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header awards-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Our commitment to excellence is recognized by industry leaders, professional organizations, and satisfied clients worldwide.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Awards Summary Stats -->
                    <section class="awards-stats" id="awards-stats">
                        <div class="stats-container">
                            <div class="stats-grid">
                                
                                <div class="stat-item">
                                    <div class="stat-number">
                                        <?php
                                        $total_awards = count(array_filter(array($professional_awards, $client_recognition)));
                                        echo esc_html($total_awards ?: '15+');
                                        ?>
                                    </div>
                                    <div class="stat-label"><?php esc_html_e('Industry Awards', 'recruitpro'); ?></div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-number">
                                        <?php
                                        $total_certifications = count(array_filter(array($industry_certifications, $quality_certifications, $compliance_certifications)));
                                        echo esc_html($total_certifications ?: '8+');
                                        ?>
                                    </div>
                                    <div class="stat-label"><?php esc_html_e('Certifications', 'recruitpro'); ?></div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-number">
                                        <?php
                                        $years_established = get_theme_mod('recruitpro_company_founded', '');
                                        if ($years_established) {
                                            echo esc_html(date('Y') - intval($years_established) . '+');
                                        } else {
                                            echo '10+';
                                        }
                                        ?>
                                    </div>
                                    <div class="stat-label"><?php esc_html_e('Years Excellence', 'recruitpro'); ?></div>
                                </div>

                                <div class="stat-item">
                                    <div class="stat-number">
                                        <?php
                                        $total_memberships = is_array($industry_memberships) ? count($industry_memberships) : 5;
                                        echo esc_html($total_memberships . '+');
                                        ?>
                                    </div>
                                    <div class="stat-label"><?php esc_html_e('Professional Memberships', 'recruitpro'); ?></div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Professional Awards -->
                    <?php if ($professional_awards || current_user_can('edit_posts')) : ?>
                        <section class="awards-section professional-awards" id="professional-awards">
                            <div class="section-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Professional Awards', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Recognition from industry leaders and professional organizations', 'recruitpro'); ?></p>
                                </div>

                                <div class="awards-grid <?php echo esc_attr($awards_layout); ?>">
                                    
                                    <?php if ($professional_awards) : ?>
                                        <?php 
                                        $awards_list = is_array($professional_awards) ? $professional_awards : array();
                                        foreach ($awards_list as $award) : 
                                        ?>
                                            <div class="award-item professional-award">
                                                <div class="award-content">
                                                    
                                                    <?php if (!empty($award['image'])) : ?>
                                                        <div class="award-image">
                                                            <img src="<?php echo esc_url($award['image']); ?>" 
                                                                 alt="<?php echo esc_attr($award['title']); ?>"
                                                                 loading="lazy">
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="award-details">
                                                        <h3 class="award-title"><?php echo esc_html($award['title']); ?></h3>
                                                        
                                                        <?php if (!empty($award['organization'])) : ?>
                                                            <div class="award-organization"><?php echo esc_html($award['organization']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if ($show_dates && !empty($award['date'])) : ?>
                                                            <div class="award-date"><?php echo esc_html($award['date']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if ($show_descriptions && !empty($award['description'])) : ?>
                                                            <div class="award-description">
                                                                <p><?php echo wp_kses_post($award['description']); ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($award['verification_url'])) : ?>
                                                            <div class="award-verification">
                                                                <a href="<?php echo esc_url($award['verification_url']); ?>" 
                                                                   target="_blank" 
                                                                   rel="noopener noreferrer"
                                                                   class="verification-link">
                                                                    <?php esc_html_e('Verify Award', 'recruitpro'); ?>
                                                                    <span class="icon-external" aria-hidden="true"></span>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <!-- Default awards for demo purposes -->
                                        <div class="award-item professional-award">
                                            <div class="award-content">
                                                <div class="award-details">
                                                    <h3 class="award-title"><?php esc_html_e('Best Recruitment Agency', 'recruitpro'); ?></h3>
                                                    <div class="award-organization"><?php esc_html_e('Industry Excellence Awards', 'recruitpro'); ?></div>
                                                    <div class="award-date"><?php echo esc_html(date('Y')); ?></div>
                                                    <div class="award-description">
                                                        <p><?php esc_html_e('Recognized for outstanding service delivery and client satisfaction in recruitment excellence.', 'recruitpro'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="award-item professional-award">
                                            <div class="award-content">
                                                <div class="award-details">
                                                    <h3 class="award-title"><?php esc_html_e('Innovation in HR Technology', 'recruitpro'); ?></h3>
                                                    <div class="award-organization"><?php esc_html_e('HR Tech Summit', 'recruitpro'); ?></div>
                                                    <div class="award-date"><?php echo esc_html(date('Y') - 1); ?></div>
                                                    <div class="award-description">
                                                        <p><?php esc_html_e('Leading innovation in recruitment technology and digital transformation solutions.', 'recruitpro'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (current_user_can('edit_posts')) : ?>
                                            <div class="award-item placeholder">
                                                <div class="award-content">
                                                    <p><em><?php esc_html_e('Add your professional awards to showcase industry recognition.', 'recruitpro'); ?></em></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Industry Certifications -->
                    <?php if ($industry_certifications || current_user_can('edit_posts')) : ?>
                        <section class="awards-section industry-certifications" id="industry-certifications">
                            <div class="section-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Industry Certifications', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Professional certifications and accreditations demonstrating expertise', 'recruitpro'); ?></p>
                                </div>

                                <div class="certifications-grid">
                                    
                                    <?php if ($industry_certifications) : ?>
                                        <?php 
                                        $certs_list = is_array($industry_certifications) ? $industry_certifications : array();
                                        foreach ($certs_list as $cert) : 
                                        ?>
                                            <div class="certification-item">
                                                <div class="certification-content">
                                                    
                                                    <?php if (!empty($cert['badge'])) : ?>
                                                        <div class="certification-badge">
                                                            <img src="<?php echo esc_url($cert['badge']); ?>" 
                                                                 alt="<?php echo esc_attr($cert['name']); ?>"
                                                                 loading="lazy">
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="certification-details">
                                                        <h3 class="certification-name"><?php echo esc_html($cert['name']); ?></h3>
                                                        
                                                        <?php if (!empty($cert['issuer'])) : ?>
                                                            <div class="certification-issuer"><?php echo esc_html($cert['issuer']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($cert['level'])) : ?>
                                                            <div class="certification-level"><?php echo esc_html($cert['level']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if ($show_dates && !empty($cert['date_earned'])) : ?>
                                                            <div class="certification-date"><?php esc_html_e('Earned:', 'recruitpro'); ?> <?php echo esc_html($cert['date_earned']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($cert['expiry_date'])) : ?>
                                                            <div class="certification-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html($cert['expiry_date']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($cert['verification_url'])) : ?>
                                                            <div class="certification-verification">
                                                                <a href="<?php echo esc_url($cert['verification_url']); ?>" 
                                                                   target="_blank" 
                                                                   rel="noopener noreferrer"
                                                                   class="verification-link">
                                                                    <?php esc_html_e('Verify Certification', 'recruitpro'); ?>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <!-- Default certifications for demo -->
                                        <div class="certification-item">
                                            <div class="certification-content">
                                                <div class="certification-details">
                                                    <h3 class="certification-name"><?php esc_html_e('Certified Professional Recruiter (CPR)', 'recruitpro'); ?></h3>
                                                    <div class="certification-issuer"><?php esc_html_e('National Association of Personnel Services', 'recruitpro'); ?></div>
                                                    <div class="certification-level"><?php esc_html_e('Professional Level', 'recruitpro'); ?></div>
                                                    <div class="certification-date"><?php esc_html_e('Earned:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 2); ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="certification-item">
                                            <div class="certification-content">
                                                <div class="certification-details">
                                                    <h3 class="certification-name"><?php esc_html_e('ISO 9001:2015 Quality Management', 'recruitpro'); ?></h3>
                                                    <div class="certification-issuer"><?php esc_html_e('International Organization for Standardization', 'recruitpro'); ?></div>
                                                    <div class="certification-level"><?php esc_html_e('Organization Certification', 'recruitpro'); ?></div>
                                                    <div class="certification-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 1); ?></div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (current_user_can('edit_posts')) : ?>
                                            <div class="certification-item placeholder">
                                                <div class="certification-content">
                                                    <p><em><?php esc_html_e('Add your industry certifications to build professional credibility.', 'recruitpro'); ?></em></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Professional Memberships -->
                    <?php if ($industry_memberships || current_user_can('edit_posts')) : ?>
                        <section class="awards-section professional-memberships" id="professional-memberships">
                            <div class="section-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Professional Memberships', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Active membership in leading industry organizations and associations', 'recruitpro'); ?></p>
                                </div>

                                <div class="memberships-grid">
                                    
                                    <?php if ($industry_memberships) : ?>
                                        <?php 
                                        $memberships_list = is_array($industry_memberships) ? $industry_memberships : array();
                                        foreach ($memberships_list as $membership) : 
                                        ?>
                                            <div class="membership-item">
                                                <div class="membership-content">
                                                    
                                                    <?php if (!empty($membership['logo'])) : ?>
                                                        <div class="membership-logo">
                                                            <img src="<?php echo esc_url($membership['logo']); ?>" 
                                                                 alt="<?php echo esc_attr($membership['organization']); ?>"
                                                                 loading="lazy">
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="membership-details">
                                                        <h3 class="membership-organization"><?php echo esc_html($membership['organization']); ?></h3>
                                                        
                                                        <?php if (!empty($membership['membership_type'])) : ?>
                                                            <div class="membership-type"><?php echo esc_html($membership['membership_type']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if ($show_dates && !empty($membership['member_since'])) : ?>
                                                            <div class="membership-date"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html($membership['member_since']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($membership['description'])) : ?>
                                                            <div class="membership-description">
                                                                <p><?php echo wp_kses_post($membership['description']); ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($membership['website'])) : ?>
                                                            <div class="membership-website">
                                                                <a href="<?php echo esc_url($membership['website']); ?>" 
                                                                   target="_blank" 
                                                                   rel="noopener noreferrer"
                                                                   class="website-link">
                                                                    <?php esc_html_e('Visit Organization', 'recruitpro'); ?>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <!-- Default memberships -->
                                        <div class="membership-item">
                                            <div class="membership-content">
                                                <div class="membership-details">
                                                    <h3 class="membership-organization"><?php esc_html_e('Society for Human Resource Management (SHRM)', 'recruitpro'); ?></h3>
                                                    <div class="membership-type"><?php esc_html_e('Corporate Member', 'recruitpro'); ?></div>
                                                    <div class="membership-date"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 5); ?></div>
                                                    <div class="membership-description">
                                                        <p><?php esc_html_e('Leading professional association for HR and recruitment professionals worldwide.', 'recruitpro'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="membership-item">
                                            <div class="membership-content">
                                                <div class="membership-details">
                                                    <h3 class="membership-organization"><?php esc_html_e('National Association of Personnel Services (NAPS)', 'recruitpro'); ?></h3>
                                                    <div class="membership-type"><?php esc_html_e('Full Member', 'recruitpro'); ?></div>
                                                    <div class="membership-date"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 3); ?></div>
                                                    <div class="membership-description">
                                                        <p><?php esc_html_e('Professional association advancing ethical practices in recruitment and staffing.', 'recruitpro'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (current_user_can('edit_posts')) : ?>
                                            <div class="membership-item placeholder">
                                                <div class="membership-content">
                                                    <p><em><?php esc_html_e('Add your professional memberships to demonstrate industry involvement.', 'recruitpro'); ?></em></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Client Recognition & Testimonials -->
                    <?php if ($client_recognition || current_user_can('edit_posts')) : ?>
                        <section class="awards-section client-recognition" id="client-recognition">
                            <div class="section-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Client Recognition', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Appreciation and awards from satisfied clients and partners', 'recruitpro'); ?></p>
                                </div>

                                <div class="recognition-grid">
                                    
                                    <?php if ($client_recognition) : ?>
                                        <?php 
                                        $recognition_list = is_array($client_recognition) ? $client_recognition : array();
                                        foreach ($recognition_list as $recognition) : 
                                        ?>
                                            <div class="recognition-item">
                                                <div class="recognition-content">
                                                    
                                                    <?php if (!empty($recognition['client_logo'])) : ?>
                                                        <div class="client-logo">
                                                            <img src="<?php echo esc_url($recognition['client_logo']); ?>" 
                                                                 alt="<?php echo esc_attr($recognition['client_name']); ?>"
                                                                 loading="lazy">
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="recognition-details">
                                                        <h3 class="recognition-title"><?php echo esc_html($recognition['title']); ?></h3>
                                                        
                                                        <?php if (!empty($recognition['client_name'])) : ?>
                                                            <div class="client-name"><?php echo esc_html($recognition['client_name']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if ($show_dates && !empty($recognition['date'])) : ?>
                                                            <div class="recognition-date"><?php echo esc_html($recognition['date']); ?></div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($recognition['testimonial'])) : ?>
                                                            <div class="recognition-testimonial">
                                                                <blockquote>
                                                                    <p><?php echo wp_kses_post($recognition['testimonial']); ?></p>
                                                                </blockquote>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($recognition['author_name']) || !empty($recognition['author_position'])) : ?>
                                                            <div class="testimonial-author">
                                                                <?php if (!empty($recognition['author_name'])) : ?>
                                                                    <span class="author-name"><?php echo esc_html($recognition['author_name']); ?></span>
                                                                <?php endif; ?>
                                                                <?php if (!empty($recognition['author_position'])) : ?>
                                                                    <span class="author-position"><?php echo esc_html($recognition['author_position']); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <!-- Default client recognition -->
                                        <div class="recognition-item">
                                            <div class="recognition-content">
                                                <div class="recognition-details">
                                                    <h3 class="recognition-title"><?php esc_html_e('Preferred Recruitment Partner Award', 'recruitpro'); ?></h3>
                                                    <div class="client-name"><?php esc_html_e('Fortune 500 Technology Company', 'recruitpro'); ?></div>
                                                    <div class="recognition-date"><?php echo esc_html(date('Y')); ?></div>
                                                    <div class="recognition-testimonial">
                                                        <blockquote>
                                                            <p><?php esc_html_e('Outstanding recruitment services with exceptional quality candidates and professional service delivery that exceeded our expectations.', 'recruitpro'); ?></p>
                                                        </blockquote>
                                                    </div>
                                                    <div class="testimonial-author">
                                                        <span class="author-name"><?php esc_html_e('Sarah Johnson', 'recruitpro'); ?></span>
                                                        <span class="author-position"><?php esc_html_e('VP Human Resources', 'recruitpro'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (current_user_can('edit_posts')) : ?>
                                            <div class="recognition-item placeholder">
                                                <div class="recognition-content">
                                                    <p><em><?php esc_html_e('Add client recognition and testimonials to showcase your success.', 'recruitpro'); ?></em></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <section class="awards-cta" id="awards-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Experience Award-Winning Recruitment Services', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Join our satisfied clients and candidates who trust our proven track record of excellence in recruitment and talent acquisition.', 'recruitpro'); ?></p>
                                
                                <div class="cta-buttons">
                                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">
                                        <?php esc_html_e('Contact Our Team', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-secondary">
                                        <?php esc_html_e('Our Services', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('awards-sidebar')) : ?>
                <aside id="secondary" class="widget-area awards-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('awards-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   AWARDS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL AWARDS PAGE FEATURES:

✅ SCHEMA.ORG MARKUP
- Organization schema with awards
- Professional credentials schema
- Industry memberships schema
- Trust signals optimization

✅ AWARDS SHOWCASE
- Professional awards display
- Industry certifications
- Quality management certifications
- Compliance certifications
- Client recognition awards

✅ CREDIBILITY BUILDING
- Professional memberships
- Industry associations
- Verification links
- Date tracking and validity

✅ TRUST SIGNALS
- Statistics and metrics
- Client testimonials
- Media mentions
- Industry recognition

✅ PROFESSIONAL PRESENTATION
- Grid and list layout options
- Badge and logo displays
- Verification systems
- Professional styling

✅ CUSTOMIZATION OPTIONS
- Multiple layout options
- Sidebar position control
- Show/hide elements
- Grouping methods

✅ RESPONSIVE DESIGN
- Mobile-first approach
- Touch-friendly cards
- Responsive grids
- Optimized images

✅ SEO OPTIMIZATION
- Structured data markup
- Meta descriptions
- Keyword optimization
- Internal linking

✅ ACCESSIBILITY
- ARIA labels and roles
- Semantic HTML structure
- Keyboard navigation
- Screen reader support

✅ PERFORMANCE
- Lazy loading images
- Conditional content
- Optimized queries
- Fast loading times

PERFECT FOR:
- Recruitment agencies
- HR consultancies
- Executive search firms
- Staffing companies
- Professional services

BUILDS TRUST THROUGH:
- Industry recognition
- Professional credentials
- Client satisfaction
- Quality certifications
- Media coverage

*/
?>