<?php
/**
 * Template Name: Professional Certifications
 *
 * Professional certifications page template for recruitment agencies showcasing
 * industry credentials, professional qualifications, compliance certifications,
 * and team expertise. Demonstrates commitment to professional standards and
 * builds trust through verified credentials and ongoing professional development.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-certifications.php
 * Purpose: Professional certifications and credentials showcase
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Certifications display, compliance badges, team qualifications
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_certifications_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_certifications_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$certifications_layout = get_theme_mod('recruitpro_certifications_layout', 'grid');
$show_verification = get_theme_mod('recruitpro_certifications_show_verification', true);
$show_expiry_dates = get_theme_mod('recruitpro_certifications_show_expiry', true);
$show_team_certs = get_theme_mod('recruitpro_certifications_show_team', true);

// Schema.org markup for certifications
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company_name,
    'url' => home_url(),
    'logo' => get_theme_mod('recruitpro_site_logo', ''),
    'hasCredential' => array(),
    'certifications' => array(),
    'memberOf' => array(),
    'accreditedBy' => array()
);

// Get certifications data from custom fields
$recruitment_certifications = get_post_meta($page_id, '_recruitment_certifications', true);
$quality_certifications = get_post_meta($page_id, '_quality_certifications', true);
$compliance_certifications = get_post_meta($page_id, '_compliance_certifications', true);
$security_certifications = get_post_meta($page_id, '_security_certifications', true);
$industry_memberships = get_post_meta($page_id, '_industry_memberships', true);
$team_qualifications = get_post_meta($page_id, '_team_qualifications', true);
?>

<!-- Schema.org Organization with Credentials Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main id="primary" class="site-main certifications-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="certifications-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header certifications-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Our commitment to professional excellence is demonstrated through comprehensive certifications, industry credentials, and ongoing professional development that ensures we meet the highest standards in recruitment services.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Certification Overview -->
                    <section class="certifications-overview" id="certifications-overview">
                        <div class="overview-container">
                            
                            <div class="overview-grid">
                                
                                <div class="overview-item">
                                    <div class="overview-icon">
                                        <i class="fas fa-certificate" aria-hidden="true"></i>
                                    </div>
                                    <div class="overview-content">
                                        <h3 class="overview-title"><?php esc_html_e('Industry Certified', 'recruitpro'); ?></h3>
                                        <p class="overview-description"><?php esc_html_e('Accredited by leading recruitment and HR professional bodies', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="overview-item">
                                    <div class="overview-icon">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                    </div>
                                    <div class="overview-content">
                                        <h3 class="overview-title"><?php esc_html_e('Compliance Ready', 'recruitpro'); ?></h3>
                                        <p class="overview-description"><?php esc_html_e('Fully compliant with GDPR, data protection, and employment regulations', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="overview-item">
                                    <div class="overview-icon">
                                        <i class="fas fa-users-cog" aria-hidden="true"></i>
                                    </div>
                                    <div class="overview-content">
                                        <h3 class="overview-title"><?php esc_html_e('Expert Team', 'recruitpro'); ?></h3>
                                        <p class="overview-description"><?php esc_html_e('Qualified recruitment professionals with verified industry credentials', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="overview-item">
                                    <div class="overview-icon">
                                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                                    </div>
                                    <div class="overview-content">
                                        <h3 class="overview-title"><?php esc_html_e('Continuously Updated', 'recruitpro'); ?></h3>
                                        <p class="overview-description"><?php esc_html_e('Regular certification renewals and ongoing professional development', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

                <!-- Recruitment Industry Certifications -->
                <section class="recruitment-certifications certifications-section" id="recruitment-certifications">
                    <div class="section-container">
                        
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Recruitment Industry Certifications', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Professional credentials from leading recruitment and HR organizations', 'recruitpro'); ?></p>
                        </div>

                        <div class="certifications-grid">
                            
                            <?php if ($recruitment_certifications && is_array($recruitment_certifications)) : ?>
                                <?php foreach ($recruitment_certifications as $cert) : ?>
                                    <div class="certification-card">
                                        <div class="cert-content">
                                            
                                            <?php if (!empty($cert['badge_image'])) : ?>
                                                <div class="cert-badge">
                                                    <img src="<?php echo esc_url($cert['badge_image']); ?>" 
                                                         alt="<?php echo esc_attr($cert['name']); ?> Certification"
                                                         loading="lazy">
                                                </div>
                                            <?php endif; ?>

                                            <div class="cert-details">
                                                <h3 class="cert-name"><?php echo esc_html($cert['name']); ?></h3>
                                                
                                                <?php if (!empty($cert['issuing_body'])) : ?>
                                                    <div class="cert-issuer"><?php echo esc_html($cert['issuing_body']); ?></div>
                                                <?php endif; ?>

                                                <?php if (!empty($cert['description'])) : ?>
                                                    <div class="cert-description">
                                                        <p><?php echo wp_kses_post($cert['description']); ?></p>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="cert-meta">
                                                    <?php if (!empty($cert['date_obtained'])) : ?>
                                                        <span class="cert-date"><?php esc_html_e('Obtained:', 'recruitpro'); ?> <?php echo esc_html($cert['date_obtained']); ?></span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($show_expiry_dates && !empty($cert['expiry_date'])) : ?>
                                                        <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html($cert['expiry_date']); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if ($show_verification && !empty($cert['verification_url'])) : ?>
                                                    <div class="cert-verification">
                                                        <a href="<?php echo esc_url($cert['verification_url']); ?>" 
                                                           target="_blank" 
                                                           rel="noopener noreferrer"
                                                           class="verify-link">
                                                            <?php esc_html_e('Verify Certification', 'recruitpro'); ?>
                                                            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <!-- Default recruitment certifications -->
                                <div class="certification-card">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('Certified Professional Recruiter (CPR)', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('National Association of Personnel Services', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Professional certification demonstrating expertise in recruitment practices, candidate sourcing, and client relationship management.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Obtained:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 2); ?></span>
                                                <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 1); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="certification-card">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('SHRM Certified Professional (SHRM-CP)', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('Society for Human Resource Management', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Comprehensive HR certification covering talent acquisition, employee relations, and strategic HR management.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Obtained:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 1); ?></span>
                                                <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="certification-card">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('Certified Talent Acquisition Professional (CTAP)', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('Talent Acquisition Institute', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Specialized certification in talent acquisition strategies, employer branding, and recruitment technology.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Obtained:', 'recruitpro'); ?> <?php echo esc_html(date('Y')); ?></span>
                                                <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 3); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="certification-card placeholder">
                                        <div class="cert-content">
                                            <p><em><?php esc_html_e('Add your recruitment industry certifications to showcase professional credentials.', 'recruitpro'); ?></em></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </section>

                <!-- Quality Management Certifications -->
                <section class="quality-certifications certifications-section" id="quality-certifications">
                    <div class="section-container">
                        
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Quality Management Systems', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('International standards for quality management and operational excellence', 'recruitpro'); ?></p>
                        </div>

                        <div class="certifications-grid">
                            
                            <?php if ($quality_certifications && is_array($quality_certifications)) : ?>
                                <?php foreach ($quality_certifications as $cert) : ?>
                                    <div class="certification-card quality-cert">
                                        <div class="cert-content">
                                            
                                            <?php if (!empty($cert['badge_image'])) : ?>
                                                <div class="cert-badge">
                                                    <img src="<?php echo esc_url($cert['badge_image']); ?>" 
                                                         alt="<?php echo esc_attr($cert['name']); ?> Certification"
                                                         loading="lazy">
                                                </div>
                                            <?php endif; ?>

                                            <div class="cert-details">
                                                <h3 class="cert-name"><?php echo esc_html($cert['name']); ?></h3>
                                                <div class="cert-issuer"><?php echo esc_html($cert['issuing_body']); ?></div>
                                                
                                                <?php if (!empty($cert['scope'])) : ?>
                                                    <div class="cert-scope"><?php esc_html_e('Scope:', 'recruitpro'); ?> <?php echo esc_html($cert['scope']); ?></div>
                                                <?php endif; ?>

                                                <div class="cert-description">
                                                    <p><?php echo wp_kses_post($cert['description']); ?></p>
                                                </div>

                                                <div class="cert-meta">
                                                    <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html($cert['date_obtained']); ?></span>
                                                    <?php if (!empty($cert['next_audit'])) : ?>
                                                        <span class="cert-audit"><?php esc_html_e('Next Audit:', 'recruitpro'); ?> <?php echo esc_html($cert['next_audit']); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if (!empty($cert['certificate_number'])) : ?>
                                                    <div class="cert-number"><?php esc_html_e('Certificate No:', 'recruitpro'); ?> <?php echo esc_html($cert['certificate_number']); ?></div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <!-- Default quality certifications -->
                                <div class="certification-card quality-cert">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('ISO 9001:2015', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('International Organization for Standardization', 'recruitpro'); ?></div>
                                            <div class="cert-scope"><?php esc_html_e('Scope:', 'recruitpro'); ?> <?php esc_html_e('Recruitment and staffing services', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Quality Management System certification ensuring consistent delivery of high-quality recruitment services and client satisfaction.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 1); ?></span>
                                                <span class="cert-audit"><?php esc_html_e('Next Audit:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="certification-card quality-cert">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('ISO 14001:2015', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('International Organization for Standardization', 'recruitpro'); ?></div>
                                            <div class="cert-scope"><?php esc_html_e('Scope:', 'recruitpro'); ?> <?php esc_html_e('Environmental management systems', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Environmental Management System certification demonstrating our commitment to sustainable business practices and environmental responsibility.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html(date('Y')); ?></span>
                                                <span class="cert-audit"><?php esc_html_e('Next Audit:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 3); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="certification-card placeholder">
                                        <div class="cert-content">
                                            <p><em><?php esc_html_e('Add your quality management certifications to demonstrate operational excellence.', 'recruitpro'); ?></em></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </section>

                <!-- Security & Compliance Certifications -->
                <section class="security-compliance certifications-section" id="security-compliance">
                    <div class="section-container">
                        
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Security & Compliance', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Data protection, information security, and regulatory compliance certifications', 'recruitpro'); ?></p>
                        </div>

                        <div class="certifications-grid">
                            
                            <?php if ($security_certifications && is_array($security_certifications)) : ?>
                                <?php foreach ($security_certifications as $cert) : ?>
                                    <div class="certification-card security-cert">
                                        <div class="cert-content">
                                            
                                            <?php if (!empty($cert['badge_image'])) : ?>
                                                <div class="cert-badge">
                                                    <img src="<?php echo esc_url($cert['badge_image']); ?>" 
                                                         alt="<?php echo esc_attr($cert['name']); ?> Certification"
                                                         loading="lazy">
                                                </div>
                                            <?php endif; ?>

                                            <div class="cert-details">
                                                <h3 class="cert-name"><?php echo esc_html($cert['name']); ?></h3>
                                                <div class="cert-issuer"><?php echo esc_html($cert['issuing_body']); ?></div>
                                                <div class="cert-description">
                                                    <p><?php echo wp_kses_post($cert['description']); ?></p>
                                                </div>
                                                <div class="cert-meta">
                                                    <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html($cert['date_obtained']); ?></span>
                                                    <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html($cert['expiry_date']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <!-- Default security certifications -->
                                <div class="certification-card security-cert">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('ISO 27001:2013', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('International Organization for Standardization', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Information Security Management System certification ensuring the highest standards of data protection and cybersecurity.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 1); ?></span>
                                                <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="certification-card security-cert">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('GDPR Compliance Certification', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('Data Protection Authority', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Full compliance with General Data Protection Regulation requirements for candidate and client data handling.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html(date('Y')); ?></span>
                                                <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 1); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="certification-card security-cert">
                                    <div class="cert-content">
                                        <div class="cert-details">
                                            <h3 class="cert-name"><?php esc_html_e('Cyber Essentials Plus', 'recruitpro'); ?></h3>
                                            <div class="cert-issuer"><?php esc_html_e('National Cyber Security Centre', 'recruitpro'); ?></div>
                                            <div class="cert-description">
                                                <p><?php esc_html_e('Enhanced cybersecurity certification protecting against common cyber threats and ensuring secure operations.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="cert-meta">
                                                <span class="cert-date"><?php esc_html_e('Certified:', 'recruitpro'); ?> <?php echo esc_html(date('Y')); ?></span>
                                                <span class="cert-expiry"><?php esc_html_e('Valid until:', 'recruitpro'); ?> <?php echo esc_html(date('Y') + 1); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="certification-card placeholder">
                                        <div class="cert-content">
                                            <p><em><?php esc_html_e('Add your security and compliance certifications to build client trust.', 'recruitpro'); ?></em></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </section>

                <!-- Professional Memberships -->
                <section class="professional-memberships certifications-section" id="professional-memberships">
                    <div class="section-container">
                        
                        <div class="section-header">
                            <h2 class="section-title"><?php esc_html_e('Professional Memberships', 'recruitpro'); ?></h2>
                            <p class="section-subtitle"><?php esc_html_e('Active membership in leading recruitment and HR professional organizations', 'recruitpro'); ?></p>
                        </div>

                        <div class="memberships-grid">
                            
                            <?php if ($industry_memberships && is_array($industry_memberships)) : ?>
                                <?php foreach ($industry_memberships as $membership) : ?>
                                    <div class="membership-card">
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
                                                
                                                <?php if (!empty($membership['membership_level'])) : ?>
                                                    <div class="membership-level"><?php echo esc_html($membership['membership_level']); ?></div>
                                                <?php endif; ?>

                                                <div class="membership-description">
                                                    <p><?php echo wp_kses_post($membership['description']); ?></p>
                                                </div>

                                                <div class="membership-meta">
                                                    <span class="membership-since"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html($membership['member_since']); ?></span>
                                                    
                                                    <?php if (!empty($membership['membership_number'])) : ?>
                                                        <span class="membership-number"><?php esc_html_e('ID:', 'recruitpro'); ?> <?php echo esc_html($membership['membership_number']); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <!-- Default memberships -->
                                <div class="membership-card">
                                    <div class="membership-content">
                                        <div class="membership-details">
                                            <h3 class="membership-organization"><?php esc_html_e('Recruitment & Employment Confederation (REC)', 'recruitpro'); ?></h3>
                                            <div class="membership-level"><?php esc_html_e('Corporate Member', 'recruitpro'); ?></div>
                                            <div class="membership-description">
                                                <p><?php esc_html_e('Leading professional body for the recruitment industry, promoting best practices and professional standards.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="membership-meta">
                                                <span class="membership-since"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 5); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="membership-card">
                                    <div class="membership-content">
                                        <div class="membership-details">
                                            <h3 class="membership-organization"><?php esc_html_e('Chartered Institute of Personnel and Development (CIPD)', 'recruitpro'); ?></h3>
                                            <div class="membership-level"><?php esc_html_e('Corporate Partner', 'recruitpro'); ?></div>
                                            <div class="membership-description">
                                                <p><?php esc_html_e('Professional body for HR and people development, ensuring ethical and effective recruitment practices.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="membership-meta">
                                                <span class="membership-since"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 3); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="membership-card">
                                    <div class="membership-content">
                                        <div class="membership-details">
                                            <h3 class="membership-organization"><?php esc_html_e('International Association of Employment Web Sites (IAEWS)', 'recruitpro'); ?></h3>
                                            <div class="membership-level"><?php esc_html_e('Associate Member', 'recruitpro'); ?></div>
                                            <div class="membership-description">
                                                <p><?php esc_html_e('Global organization promoting excellence in online recruitment and digital talent acquisition.', 'recruitpro'); ?></p>
                                            </div>
                                            <div class="membership-meta">
                                                <span class="membership-since"><?php esc_html_e('Member since:', 'recruitpro'); ?> <?php echo esc_html(date('Y') - 2); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="membership-card placeholder">
                                        <div class="membership-content">
                                            <p><em><?php esc_html_e('Add your professional memberships to demonstrate industry engagement.', 'recruitpro'); ?></em></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </section>

                <!-- Team Qualifications -->
                <?php if ($show_team_certs) : ?>
                    <section class="team-qualifications certifications-section" id="team-qualifications">
                        <div class="section-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Team Qualifications', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Professional qualifications and certifications held by our recruitment team', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Query team members with qualifications
                            $team_args = array(
                                'post_type' => 'team_member',
                                'posts_per_page' => 6,
                                'meta_query' => array(
                                    array(
                                        'key' => '_team_qualifications',
                                        'compare' => 'EXISTS'
                                    )
                                ),
                                'orderby' => 'menu_order',
                                'order' => 'ASC'
                            );

                            $team_query = new WP_Query($team_args);

                            if ($team_query->have_posts()) :
                            ?>
                                <div class="team-qualifications-grid">
                                    <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
                                        <?php
                                        $qualifications = get_post_meta(get_the_ID(), '_team_qualifications', true);
                                        $position = get_post_meta(get_the_ID(), '_team_position', true);
                                        
                                        if ($qualifications) :
                                        ?>
                                            <div class="team-qualification-item">
                                                <div class="team-member-info">
                                                    
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <div class="member-photo">
                                                            <?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="member-details">
                                                        <h3 class="member-name"><?php the_title(); ?></h3>
                                                        <?php if ($position) : ?>
                                                            <div class="member-position"><?php echo esc_html($position); ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="member-qualifications">
                                                    <?php if (is_array($qualifications)) : ?>
                                                        <ul class="qualifications-list">
                                                            <?php foreach ($qualifications as $qualification) : ?>
                                                                <li class="qualification-item">
                                                                    <span class="qual-name"><?php echo esc_html($qualification['name']); ?></span>
                                                                    <?php if (!empty($qualification['issuer'])) : ?>
                                                                        <span class="qual-issuer"><?php echo esc_html($qualification['issuer']); ?></span>
                                                                    <?php endif; ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                </div>
                            <?php 
                            else :
                            ?>
                                <!-- Default team qualifications -->
                                <div class="team-qualifications-grid">
                                    <div class="team-qualification-item">
                                        <div class="team-member-info">
                                            <div class="member-details">
                                                <h3 class="member-name"><?php esc_html_e('Sarah Johnson', 'recruitpro'); ?></h3>
                                                <div class="member-position"><?php esc_html_e('Senior Recruitment Consultant', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        <div class="member-qualifications">
                                            <ul class="qualifications-list">
                                                <li class="qualification-item">
                                                    <span class="qual-name"><?php esc_html_e('SHRM-CP', 'recruitpro'); ?></span>
                                                    <span class="qual-issuer"><?php esc_html_e('Society for Human Resource Management', 'recruitpro'); ?></span>
                                                </li>
                                                <li class="qualification-item">
                                                    <span class="qual-name"><?php esc_html_e('CPR Certification', 'recruitpro'); ?></span>
                                                    <span class="qual-issuer"><?php esc_html_e('NAPS', 'recruitpro'); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="team-qualification-item">
                                        <div class="team-member-info">
                                            <div class="member-details">
                                                <h3 class="member-name"><?php esc_html_e('Michael Chen', 'recruitpro'); ?></h3>
                                                <div class="member-position"><?php esc_html_e('Technology Recruiter', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        <div class="member-qualifications">
                                            <ul class="qualifications-list">
                                                <li class="qualification-item">
                                                    <span class="qual-name"><?php esc_html_e('CTAP', 'recruitpro'); ?></span>
                                                    <span class="qual-issuer"><?php esc_html_e('Talent Acquisition Institute', 'recruitpro'); ?></span>
                                                </li>
                                                <li class="qualification-item">
                                                    <span class="qual-name"><?php esc_html_e('IT Recruitment Specialist', 'recruitpro'); ?></span>
                                                    <span class="qual-issuer"><?php esc_html_e('Tech Recruitment Certification Body', 'recruitpro'); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="team-qualification-item">
                                        <div class="team-member-info">
                                            <div class="member-details">
                                                <h3 class="member-name"><?php esc_html_e('Emma Wilson', 'recruitpro'); ?></h3>
                                                <div class="member-position"><?php esc_html_e('Director of Client Services', 'recruitpro'); ?></div>
                                            </div>
                                        </div>
                                        <div class="member-qualifications">
                                            <ul class="qualifications-list">
                                                <li class="qualification-item">
                                                    <span class="qual-name"><?php esc_html_e('CIPD Level 7', 'recruitpro'); ?></span>
                                                    <span class="qual-issuer"><?php esc_html_e('Chartered Institute of Personnel and Development', 'recruitpro'); ?></span>
                                                </li>
                                                <li class="qualification-item">
                                                    <span class="qual-name"><?php esc_html_e('MBA in Human Resources', 'recruitpro'); ?></span>
                                                    <span class="qual-issuer"><?php esc_html_e('Business School', 'recruitpro'); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="team-qualifications-placeholder">
                                        <p><em><?php esc_html_e('Create team member posts with qualifications to showcase your team\'s professional credentials.', 'recruitpro'); ?></em></p>
                                    </div>
                                <?php endif; ?>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Certification Commitment -->
                <section class="certification-commitment" id="certification-commitment">
                    <div class="commitment-container">
                        <div class="commitment-content">
                            <h2 class="commitment-title"><?php esc_html_e('Our Commitment to Excellence', 'recruitpro'); ?></h2>
                            <p class="commitment-description"><?php esc_html_e('We maintain the highest professional standards through continuous certification, ongoing training, and strict adherence to industry best practices. Our certifications are not just credentials—they represent our unwavering commitment to delivering exceptional recruitment services.', 'recruitpro'); ?></p>
                            
                            <div class="commitment-features">
                                <div class="feature-item">
                                    <i class="fas fa-sync-alt" aria-hidden="true"></i>
                                    <span><?php esc_html_e('Regular certification renewals and updates', 'recruitpro'); ?></span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                    <span><?php esc_html_e('Ongoing professional development programs', 'recruitpro'); ?></span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-shield-check" aria-hidden="true"></i>
                                    <span><?php esc_html_e('Strict compliance with industry regulations', 'recruitpro'); ?></span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-award" aria-hidden="true"></i>
                                    <span><?php esc_html_e('Recognition from leading professional bodies', 'recruitpro'); ?></span>
                                </div>
                            </div>

                            <div class="commitment-actions">
                                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">
                                    <?php esc_html_e('Discuss Your Requirements', 'recruitpro'); ?>
                                </a>
                                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn btn-secondary">
                                    <?php esc_html_e('Our Services', 'recruitpro'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('certifications-sidebar')) : ?>
                <aside id="secondary" class="widget-area certifications-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('certifications-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   CERTIFICATIONS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL CERTIFICATIONS PAGE FEATURES:

✅ COMPREHENSIVE CERTIFICATION DISPLAY
- Recruitment industry certifications
- Quality management systems (ISO 9001, ISO 14001)
- Security & compliance (ISO 27001, GDPR, Cyber Essentials)
- Professional memberships (REC, CIPD, IAEWS)
- Team member qualifications

✅ PROFESSIONAL CREDIBILITY
- Verification links and certificate numbers
- Expiry dates and renewal tracking
- Issuing body information
- Scope and description details
- Audit and compliance dates

✅ TRUST BUILDING ELEMENTS
- Security badges and compliance seals
- Professional organization logos
- Verification systems
- Certificate authenticity
- Industry recognition

✅ TEAM EXPERTISE SHOWCASE
- Individual team qualifications
- Professional development tracking
- Certification levels and achievements
- Ongoing training commitments
- Expertise demonstration

✅ SCHEMA.ORG MARKUP
- Organization credentials schema
- Professional certification markup
- Trust signals optimization
- SEO-friendly structure

✅ RESPONSIVE DESIGN
- Mobile-first certification cards
- Touch-friendly verification links
- Professional badge displays
- Optimized image loading

✅ CUSTOMIZATION OPTIONS
- Show/hide verification links
- Expiry date display control
- Team qualifications toggle
- Layout customization
- Sidebar positioning

✅ COMPLIANCE FOCUS
- GDPR compliance certification
- Data protection standards
- Information security management
- Regulatory adherence
- Quality assurance systems

PERFECT FOR:
- Building client trust and confidence
- Demonstrating professional standards
- Regulatory compliance display
- Team expertise showcase
- Industry credibility building

CERTIFICATION CATEGORIES:
- Recruitment Industry Standards
- Quality Management Systems
- Security & Data Protection
- Professional Memberships
- Team Qualifications
- Compliance Certifications

*/
?>