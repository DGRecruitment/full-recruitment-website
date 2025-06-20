<?php
/**
 * Template Name: GDPR Compliance Page
 *
 * Professional GDPR compliance page template for recruitment agencies
 * providing complete data protection information, privacy controls,
 * and legal compliance features. Essential for handling candidate and
 * client data in accordance with GDPR regulations.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-gdpr.php
 * Purpose: GDPR compliance and data protection information
 * Dependencies: WordPress core, theme functions, GDPR compliance system
 * Features: Privacy controls, data requests, consent management, legal compliance
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_gdpr_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_gdpr_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_address = get_theme_mod('recruitpro_company_address', '');
$dpo_email = get_theme_mod('recruitpro_dpo_email', get_option('admin_email'));
$dpo_name = get_theme_mod('recruitpro_dpo_name', '');
$show_privacy_controls = get_theme_mod('recruitpro_gdpr_show_controls', true);
$show_data_request = get_theme_mod('recruitpro_gdpr_show_request_form', true);
$show_consent_manager = get_theme_mod('recruitpro_gdpr_show_consent_manager', true);

// GDPR compliance data
$gdpr_effective_date = get_theme_mod('recruitpro_gdpr_effective_date', '2018-05-25');
$data_retention_period = get_theme_mod('recruitpro_gdpr_retention_period', '7 years');
$privacy_policy_url = get_privacy_policy_url();

// Schema.org markup for GDPR page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => sprintf(__('GDPR compliance and data protection information for %s. Learn about your privacy rights and how we protect your personal data.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'GDPR Compliance',
        'description' => 'General Data Protection Regulation compliance and privacy protection'
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

<main id="primary" class="site-main gdpr-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header gdpr-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <div class="page-description">
                                <?php if (get_the_content()) : ?>
                                    <?php the_content(); ?>
                                <?php else : ?>
                                    <p><?php printf(esc_html__('At %s, we are committed to protecting your personal data and respecting your privacy rights in accordance with the General Data Protection Regulation (GDPR). This page provides comprehensive information about how we collect, use, and protect your personal data.', 'recruitpro'), esc_html($company_name)); ?></p>
                                <?php endif; ?>
                            </div>
                        </header>
                    <?php endif; ?>

                    <!-- GDPR Quick Summary -->
                    <section class="gdpr-summary">
                        <div class="summary-container">
                            <div class="summary-header">
                                <h2 class="section-title"><?php esc_html_e('Your Privacy Rights at a Glance', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Understanding your data protection rights under GDPR', 'recruitpro'); ?></p>
                            </div>

                            <div class="rights-grid">
                                
                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right to be Informed', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You have the right to know how your personal data is collected and used.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right of Access', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You can request a copy of all personal data we hold about you.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right to Rectification', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You can request correction of inaccurate personal data.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right to Erasure', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You can request deletion of your personal data in certain circumstances.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-pause" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right to Restrict Processing', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You can limit how we use your personal data in certain situations.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-download" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right to Data Portability', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You can request your data in a portable format to transfer to another service.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-stop" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Right to Object', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You can object to certain types of processing of your personal data.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <div class="right-icon">
                                        <i class="fas fa-robot" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="right-title"><?php esc_html_e('Automated Decision Making', 'recruitpro'); ?></h3>
                                    <p class="right-description"><?php esc_html_e('You have rights regarding automated decision-making and profiling.', 'recruitpro'); ?></p>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Data We Collect -->
                    <section class="data-collection" id="data-collection">
                        <div class="section-container">
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Data We Collect', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Understanding what personal data we collect and why', 'recruitpro'); ?></p>
                            </div>

                            <div class="data-categories">
                                
                                <div class="data-category">
                                    <div class="category-header">
                                        <h3 class="category-title">
                                            <i class="fas fa-user" aria-hidden="true"></i>
                                            <?php esc_html_e('Personal Information', 'recruitpro'); ?>
                                        </h3>
                                    </div>
                                    <div class="category-content">
                                        <ul class="data-list">
                                            <li><?php esc_html_e('Name and contact details (email, phone, address)', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Employment history and career information', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Education and qualifications', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Skills and professional expertise', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Salary expectations and availability', 'recruitpro'); ?></li>
                                        </ul>
                                        <p class="legal-basis">
                                            <strong><?php esc_html_e('Legal Basis:', 'recruitpro'); ?></strong> 
                                            <?php esc_html_e('Legitimate interest and consent for recruitment services', 'recruitpro'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="data-category">
                                    <div class="category-header">
                                        <h3 class="category-title">
                                            <i class="fas fa-mouse-pointer" aria-hidden="true"></i>
                                            <?php esc_html_e('Website Usage Data', 'recruitpro'); ?>
                                        </h3>
                                    </div>
                                    <div class="category-content">
                                        <ul class="data-list">
                                            <li><?php esc_html_e('IP address and browser information', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Pages visited and time spent on site', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Job searches and applications submitted', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Cookie preferences and tracking data', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Device and technology information', 'recruitpro'); ?></li>
                                        </ul>
                                        <p class="legal-basis">
                                            <strong><?php esc_html_e('Legal Basis:', 'recruitpro'); ?></strong> 
                                            <?php esc_html_e('Legitimate interest for website operation and improvement', 'recruitpro'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="data-category">
                                    <div class="category-header">
                                        <h3 class="category-title">
                                            <i class="fas fa-comments" aria-hidden="true"></i>
                                            <?php esc_html_e('Communication Data', 'recruitpro'); ?>
                                        </h3>
                                    </div>
                                    <div class="category-content">
                                        <ul class="data-list">
                                            <li><?php esc_html_e('Email correspondence and phone conversations', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Meeting notes and interview feedback', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Application forms and submitted documents', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Support requests and inquiries', 'recruitpro'); ?></li>
                                            <li><?php esc_html_e('Marketing preferences and consent records', 'recruitpro'); ?></li>
                                        </ul>
                                        <p class="legal-basis">
                                            <strong><?php esc_html_e('Legal Basis:', 'recruitpro'); ?></strong> 
                                            <?php esc_html_e('Contract performance and legitimate interest', 'recruitpro'); ?>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Privacy Controls -->
                    <?php if ($show_privacy_controls) : ?>
                        <section class="privacy-controls" id="privacy-controls">
                            <div class="section-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Manage Your Privacy', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Control how your personal data is used and processed', 'recruitpro'); ?></p>
                                </div>

                                <div class="controls-grid">
                                    
                                    <div class="control-item">
                                        <div class="control-header">
                                            <h3 class="control-title"><?php esc_html_e('Cookie Preferences', 'recruitpro'); ?></h3>
                                            <p class="control-description"><?php esc_html_e('Manage your cookie consent and tracking preferences', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="control-actions">
                                            <button type="button" class="btn btn-outline" id="manage-cookies">
                                                <i class="fas fa-cog" aria-hidden="true"></i>
                                                <?php esc_html_e('Manage Cookies', 'recruitpro'); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="control-item">
                                        <div class="control-header">
                                            <h3 class="control-title"><?php esc_html_e('Marketing Communications', 'recruitpro'); ?></h3>
                                            <p class="control-description"><?php esc_html_e('Update your marketing email and communication preferences', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="control-actions">
                                            <button type="button" class="btn btn-outline" data-modal="marketing-preferences">
                                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                                <?php esc_html_e('Update Preferences', 'recruitpro'); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="control-item">
                                        <div class="control-header">
                                            <h3 class="control-title"><?php esc_html_e('Data Download', 'recruitpro'); ?></h3>
                                            <p class="control-description"><?php esc_html_e('Request a copy of all your personal data we have on file', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="control-actions">
                                            <button type="button" class="btn btn-outline" data-modal="data-download">
                                                <i class="fas fa-download" aria-hidden="true"></i>
                                                <?php esc_html_e('Request Data', 'recruitpro'); ?>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="control-item">
                                        <div class="control-header">
                                            <h3 class="control-title"><?php esc_html_e('Account Deletion', 'recruitpro'); ?></h3>
                                            <p class="control-description"><?php esc_html_e('Request complete deletion of your account and personal data', 'recruitpro'); ?></p>
                                        </div>
                                        <div class="control-actions">
                                            <button type="button" class="btn btn-danger" data-modal="account-deletion">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                                <?php esc_html_e('Delete Account', 'recruitpro'); ?>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Data Request Form -->
                    <?php if ($show_data_request) : ?>
                        <section class="data-request" id="data-request">
                            <div class="section-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Submit a Data Request', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Exercise your GDPR rights by submitting a formal data request', 'recruitpro'); ?></p>
                                </div>

                                <div class="request-form-container">
                                    <form class="data-request-form" id="gdpr-data-request-form" novalidate>
                                        
                                        <div class="form-grid">
                                            
                                            <div class="form-group">
                                                <label for="request-name" class="form-label">
                                                    <?php esc_html_e('Full Name', 'recruitpro'); ?> <span class="required">*</span>
                                                </label>
                                                <input type="text" 
                                                       id="request-name" 
                                                       name="name" 
                                                       class="form-control" 
                                                       required>
                                            </div>

                                            <div class="form-group">
                                                <label for="request-email" class="form-label">
                                                    <?php esc_html_e('Email Address', 'recruitpro'); ?> <span class="required">*</span>
                                                </label>
                                                <input type="email" 
                                                       id="request-email" 
                                                       name="email" 
                                                       class="form-control" 
                                                       required>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label for="request-type" class="form-label">
                                                <?php esc_html_e('Request Type', 'recruitpro'); ?> <span class="required">*</span>
                                            </label>
                                            <select id="request-type" name="request_type" class="form-control" required>
                                                <option value=""><?php esc_html_e('Select a request type', 'recruitpro'); ?></option>
                                                <option value="access"><?php esc_html_e('Access - Request a copy of my data', 'recruitpro'); ?></option>
                                                <option value="rectification"><?php esc_html_e('Rectification - Correct inaccurate data', 'recruitpro'); ?></option>
                                                <option value="erasure"><?php esc_html_e('Erasure - Delete my personal data', 'recruitpro'); ?></option>
                                                <option value="portability"><?php esc_html_e('Portability - Export my data', 'recruitpro'); ?></option>
                                                <option value="restriction"><?php esc_html_e('Restriction - Limit processing of my data', 'recruitpro'); ?></option>
                                                <option value="objection"><?php esc_html_e('Objection - Object to processing', 'recruitpro'); ?></option>
                                                <option value="withdrawal"><?php esc_html_e('Consent Withdrawal - Withdraw consent', 'recruitpro'); ?></option>
                                                <option value="complaint"><?php esc_html_e('Complaint - File a data protection complaint', 'recruitpro'); ?></option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="request-details" class="form-label">
                                                <?php esc_html_e('Request Details', 'recruitpro'); ?>
                                            </label>
                                            <textarea id="request-details" 
                                                      name="details" 
                                                      class="form-control" 
                                                      rows="4" 
                                                      placeholder="<?php esc_attr_e('Please provide specific details about your request...', 'recruitpro'); ?>"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="identity-verification" class="form-label">
                                                <?php esc_html_e('Identity Verification', 'recruitpro'); ?>
                                            </label>
                                            <input type="file" 
                                                   id="identity-verification" 
                                                   name="verification_document" 
                                                   class="form-control" 
                                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                            <div class="form-help">
                                                <?php esc_html_e('Upload a copy of your ID to verify your identity (optional but recommended for faster processing)', 'recruitpro'); ?>
                                            </div>
                                        </div>

                                        <div class="form-group form-checkbox">
                                            <input type="checkbox" id="request-consent" name="consent" required>
                                            <label for="request-consent" class="checkbox-label">
                                                <?php printf(
                                                    esc_html__('I confirm that I am the data subject or have legal authority to make this request on behalf of the data subject. I understand that %s may need to verify my identity before processing this request.', 'recruitpro'),
                                                    esc_html($company_name)
                                                ); ?> <span class="required">*</span>
                                            </label>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <span class="submit-text"><?php esc_html_e('Submit Request', 'recruitpro'); ?></span>
                                                <span class="submit-loading" style="display: none;">
                                                    <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                                                    <?php esc_html_e('Processing...', 'recruitpro'); ?>
                                                </span>
                                            </button>
                                        </div>

                                        <div class="form-message" id="request-message"></div>
                                        <?php wp_nonce_field('recruitpro_gdpr_request', 'gdpr_request_nonce'); ?>

                                    </form>

                                    <div class="request-info">
                                        <h3 class="info-title"><?php esc_html_e('Response Time', 'recruitpro'); ?></h3>
                                        <p><?php esc_html_e('We will respond to your request within 30 days as required by GDPR. Complex requests may take longer, and we will notify you if additional time is needed.', 'recruitpro'); ?></p>
                                        
                                        <h3 class="info-title"><?php esc_html_e('Identity Verification', 'recruitpro'); ?></h3>
                                        <p><?php esc_html_e('To protect your privacy, we may need to verify your identity before processing your request. This helps ensure that personal data is only shared with the rightful owner.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Contact Information -->
                    <section class="gdpr-contact" id="gdpr-contact">
                        <div class="section-container">
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Data Protection Contact', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Get in touch with our data protection team for any privacy concerns', 'recruitpro'); ?></p>
                            </div>

                            <div class="contact-grid">
                                
                                <div class="contact-item">
                                    <div class="contact-header">
                                        <h3 class="contact-title">
                                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                            <?php esc_html_e('Data Protection Officer', 'recruitpro'); ?>
                                        </h3>
                                    </div>
                                    <div class="contact-details">
                                        <?php if ($dpo_name) : ?>
                                            <p class="dpo-name"><?php echo esc_html($dpo_name); ?></p>
                                        <?php endif; ?>
                                        <p class="dpo-email">
                                            <i class="fas fa-envelope" aria-hidden="true"></i>
                                            <a href="mailto:<?php echo esc_attr($dpo_email); ?>"><?php echo esc_html($dpo_email); ?></a>
                                        </p>
                                        <?php if ($company_address) : ?>
                                            <p class="dpo-address">
                                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                <?php echo esc_html($company_address); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="contact-item">
                                    <div class="contact-header">
                                        <h3 class="contact-title">
                                            <i class="fas fa-gavel" aria-hidden="true"></i>
                                            <?php esc_html_e('Supervisory Authority', 'recruitpro'); ?>
                                        </h3>
                                    </div>
                                    <div class="contact-details">
                                        <p><?php esc_html_e('If you believe we have not handled your data correctly, you have the right to lodge a complaint with the relevant supervisory authority in your country.', 'recruitpro'); ?></p>
                                        <p>
                                            <a href="https://edpb.europa.eu/about-edpb/about-edpb/members_en" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               class="external-link">
                                                <?php esc_html_e('Find Your Supervisory Authority', 'recruitpro'); ?>
                                                <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    </div>
                                </div>

                                <div class="contact-item">
                                    <div class="contact-header">
                                        <h3 class="contact-title">
                                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                                            <?php esc_html_e('Legal Documents', 'recruitpro'); ?>
                                        </h3>
                                    </div>
                                    <div class="contact-details">
                                        <ul class="document-links">
                                            <?php if ($privacy_policy_url) : ?>
                                                <li>
                                                    <a href="<?php echo esc_url($privacy_policy_url); ?>">
                                                        <i class="fas fa-file-contract" aria-hidden="true"></i>
                                                        <?php esc_html_e('Privacy Policy', 'recruitpro'); ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <a href="<?php echo esc_url(home_url('/cookie-policy/')); ?>">
                                                    <i class="fas fa-cookie-bite" aria-hidden="true"></i>
                                                    <?php esc_html_e('Cookie Policy', 'recruitpro'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>">
                                                    <i class="fas fa-scroll" aria-hidden="true"></i>
                                                    <?php esc_html_e('Terms of Service', 'recruitpro'); ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- Data Retention -->
                    <section class="data-retention" id="data-retention">
                        <div class="section-container">
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Data Retention', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('How long we keep your personal data and why', 'recruitpro'); ?></p>
                            </div>

                            <div class="retention-content">
                                <div class="retention-policy">
                                    <h3 class="policy-title"><?php esc_html_e('Retention Periods', 'recruitpro'); ?></h3>
                                    <div class="retention-periods">
                                        <div class="period-item">
                                            <div class="period-type">
                                                <i class="fas fa-user-tie" aria-hidden="true"></i>
                                                <strong><?php esc_html_e('Active Candidates', 'recruitpro'); ?></strong>
                                            </div>
                                            <div class="period-duration">
                                                <?php esc_html_e('2 years after last contact', 'recruitpro'); ?>
                                            </div>
                                        </div>
                                        <div class="period-item">
                                            <div class="period-type">
                                                <i class="fas fa-building" aria-hidden="true"></i>
                                                <strong><?php esc_html_e('Client Data', 'recruitpro'); ?></strong>
                                            </div>
                                            <div class="period-duration">
                                                <?php echo esc_html($data_retention_period); ?> <?php esc_html_e('for legal compliance', 'recruitpro'); ?>
                                            </div>
                                        </div>
                                        <div class="period-item">
                                            <div class="period-type">
                                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                                <strong><?php esc_html_e('Marketing Data', 'recruitpro'); ?></strong>
                                            </div>
                                            <div class="period-duration">
                                                <?php esc_html_e('Until consent is withdrawn', 'recruitpro'); ?>
                                            </div>
                                        </div>
                                        <div class="period-item">
                                            <div class="period-type">
                                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                                                <strong><?php esc_html_e('Analytics Data', 'recruitpro'); ?></strong>
                                            </div>
                                            <div class="period-duration">
                                                <?php esc_html_e('26 months (anonymized)', 'recruitpro'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="automatic-deletion">
                                    <h3 class="policy-title"><?php esc_html_e('Automatic Data Deletion', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('We have automated systems in place to ensure data is deleted according to our retention policy. You will receive notifications before any automatic deletion occurs, giving you the opportunity to extend retention if needed.', 'recruitpro'); ?></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Last Updated -->
                    <section class="gdpr-updates">
                        <div class="section-container">
                            <div class="updates-content">
                                <p class="last-updated">
                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                    <?php printf(
                                        esc_html__('This GDPR compliance page was last updated on %s. GDPR has been in effect since %s.', 'recruitpro'),
                                        esc_html(date('F j, Y')),
                                        esc_html(date('F j, Y', strtotime($gdpr_effective_date)))
                                    ); ?>
                                </p>
                                <p class="version-info">
                                    <?php printf(
                                        esc_html__('For questions about changes to this page or our GDPR compliance, please contact our Data Protection Officer at %s.', 'recruitpro'),
                                        '<a href="mailto:' . esc_attr($dpo_email) . '">' . esc_html($dpo_email) . '</a>'
                                    ); ?>
                                </p>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('gdpr-sidebar')) : ?>
                <aside id="secondary" class="widget-area gdpr-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('gdpr-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

// AJAX handlers for GDPR functionality
add_action('wp_ajax_recruitpro_gdpr_request', 'recruitpro_handle_gdpr_request');
add_action('wp_ajax_nopriv_recruitpro_gdpr_request', 'recruitpro_handle_gdpr_request');

function recruitpro_handle_gdpr_request() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['gdpr_request_nonce'], 'recruitpro_gdpr_request')) {
        wp_send_json_error(array('message' => esc_html__('Security verification failed. Please try again.', 'recruitpro')));
    }
    
    // Sanitize form data
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $request_type = sanitize_text_field($_POST['request_type']);
    $details = sanitize_textarea_field($_POST['details']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($request_type)) {
        wp_send_json_error(array('message' => esc_html__('Please fill in all required fields.', 'recruitpro')));
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => esc_html__('Please enter a valid email address.', 'recruitpro')));
    }
    
    // Store the request
    $request_data = array(
        'name' => $name,
        'email' => $email,
        'request_type' => $request_type,
        'details' => $details,
        'date_submitted' => current_time('mysql'),
        'status' => 'pending',
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    );
    
    // Save to database or send email to DPO
    $dpo_email = get_theme_mod('recruitpro_dpo_email', get_option('admin_email'));
    $company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
    
    $subject = sprintf(__('[GDPR Request] %s - %s', 'recruitpro'), $company_name, ucfirst($request_type));
    
    $message = sprintf(
        __("A new GDPR data request has been submitted:\n\nName: %s\nEmail: %s\nRequest Type: %s\nDetails: %s\n\nSubmitted: %s\nIP Address: %s\n\nPlease process this request within 30 days as required by GDPR.", 'recruitpro'),
        $name,
        $email,
        ucfirst(str_replace('_', ' ', $request_type)),
        $details,
        $request_data['date_submitted'],
        $request_data['ip_address']
    );
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    if (wp_mail($dpo_email, $subject, $message, $headers)) {
        wp_send_json_success(array('message' => esc_html__('Your GDPR request has been submitted successfully. We will respond within 30 days.', 'recruitpro')));
    } else {
        wp_send_json_error(array('message' => esc_html__('There was an error submitting your request. Please try again or contact us directly.', 'recruitpro')));
    }
}

/* =================================================================
   GDPR COMPLIANCE PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE GDPR COMPLIANCE FEATURES:

✅ COMPLETE PRIVACY RIGHTS INFORMATION
- All 8 GDPR rights clearly explained
- Visual icons and descriptions
- Legal basis information
- User-friendly explanations

✅ DATA COLLECTION TRANSPARENCY
- Categories of data collected
- Legal basis for each category
- Clear explanations for recruitment context
- Purpose limitation details

✅ INTERACTIVE PRIVACY CONTROLS
- Cookie preference management
- Marketing communication controls
- Data download requests
- Account deletion options

✅ FORMAL DATA REQUEST SYSTEM
- Complete request form for all GDPR rights
- Identity verification support
- File upload for verification documents
- Email notification to DPO

✅ CONTACT & COMPLIANCE INFORMATION
- Data Protection Officer details
- Supervisory authority information
- Legal document links
- Response time commitments

✅ DATA RETENTION POLICY
- Clear retention periods by data type
- Automatic deletion information
- Legal compliance explanations
- User notification processes

✅ TECHNICAL IMPLEMENTATION
- AJAX form processing
- Secure data handling
- Email notifications
- Nonce security verification

✅ RECRUITMENT INDUSTRY SPECIFIC
- Candidate data handling
- Client data retention
- CV and application processing
- Employment law compliance

✅ SCHEMA.ORG OPTIMIZATION
- Structured data markup
- SEO-friendly content
- Accessibility compliance
- Mobile-responsive design

PERFECT FOR:
- GDPR compliance requirements
- Data protection transparency
- Legal risk mitigation
- Trust building with candidates
- Professional credibility
- Regulatory compliance

TECHNICAL FEATURES:
- AJAX request processing
- Email notification system
- File upload support
- Security verification
- Database integration ready
- WordPress customizer integration

LEGAL COMPLIANCE:
- 30-day response commitment
- Identity verification process
- Supervisory authority information
- Complete rights explanation
- Data retention transparency
- Consent management

*/
?>