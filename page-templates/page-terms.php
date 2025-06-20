<?php
/**
 * Template Name: Terms of Service Page
 *
 * Comprehensive terms of service page template for recruitment agencies
 * providing detailed legal terms, service conditions, liability disclaimers,
 * and user responsibilities. Essential for legal compliance and professional
 * protection in recruitment operations.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-terms.php
 * Purpose: Terms of service and legal conditions
 * Dependencies: WordPress core, theme functions, legal compliance
 * Features: Comprehensive legal terms, service conditions, liability protection
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_terms_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_terms_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_legal_name = get_theme_mod('recruitpro_company_legal_name', $company_name);
$company_address = get_theme_mod('recruitpro_company_address', '');
$company_registration = get_theme_mod('recruitpro_company_registration', '');
$recruitment_license = get_theme_mod('recruitpro_recruitment_license', '');
$show_table_of_contents = get_theme_mod('recruitpro_terms_show_toc', true);

// Terms of service settings
$terms_last_updated = get_theme_mod('recruitpro_terms_last_updated', date('Y-m-d'));
$terms_effective_date = get_theme_mod('recruitpro_terms_effective_date', '2024-01-01');
$governing_law = get_theme_mod('recruitpro_terms_governing_law', 'United Kingdom');
$jurisdiction = get_theme_mod('recruitpro_terms_jurisdiction', 'England and Wales');

// Contact information
$contact_email = get_theme_mod('recruitpro_contact_email', get_option('admin_email'));
$contact_phone = get_theme_mod('recruitpro_contact_phone', '');
$legal_contact_email = get_theme_mod('recruitpro_legal_contact_email', $contact_email);

// Schema.org markup for terms page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => sprintf(__('Terms of Service for %s - Legal terms and conditions for using our recruitment services and website. Professional compliance and user responsibilities.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Terms of Service',
        'description' => 'Legal terms and conditions for recruitment services and website usage'
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

<main id="primary" class="site-main terms-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header terms-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <div class="terms-meta">
                                <div class="meta-item">
                                    <strong><?php esc_html_e('Effective Date:', 'recruitpro'); ?></strong>
                                    <?php echo esc_html(date('F j, Y', strtotime($terms_effective_date))); ?>
                                </div>
                                <div class="meta-item">
                                    <strong><?php esc_html_e('Last Updated:', 'recruitpro'); ?></strong>
                                    <?php echo esc_html(date('F j, Y', strtotime($terms_last_updated))); ?>
                                </div>
                            </div>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('These Terms of Service govern your use of %s recruitment services and website. By accessing our services, you agree to be bound by these terms and conditions.', 'recruitpro'), esc_html($company_name)); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Table of Contents -->
                    <?php if ($show_table_of_contents) : ?>
                        <section class="terms-toc" id="table-of-contents">
                            <div class="toc-container">
                                <h2 class="toc-title"><?php esc_html_e('Table of Contents', 'recruitpro'); ?></h2>
                                <nav class="toc-navigation" aria-label="<?php esc_attr_e('Terms of Service Navigation', 'recruitpro'); ?>">
                                    <ol class="toc-list">
                                        <li><a href="#acceptance-terms"><?php esc_html_e('Acceptance of Terms', 'recruitpro'); ?></a></li>
                                        <li><a href="#services-description"><?php esc_html_e('Description of Services', 'recruitpro'); ?></a></li>
                                        <li><a href="#eligibility-use"><?php esc_html_e('Eligibility and Use', 'recruitpro'); ?></a></li>
                                        <li><a href="#user-responsibilities"><?php esc_html_e('User Responsibilities', 'recruitpro'); ?></a></li>
                                        <li><a href="#service-terms"><?php esc_html_e('Recruitment Service Terms', 'recruitpro'); ?></a></li>
                                        <li><a href="#fees-payment"><?php esc_html_e('Fees and Payment', 'recruitpro'); ?></a></li>
                                        <li><a href="#intellectual-property"><?php esc_html_e('Intellectual Property', 'recruitpro'); ?></a></li>
                                        <li><a href="#confidentiality"><?php esc_html_e('Confidentiality', 'recruitpro'); ?></a></li>
                                        <li><a href="#data-protection"><?php esc_html_e('Data Protection', 'recruitpro'); ?></a></li>
                                        <li><a href="#disclaimers"><?php esc_html_e('Disclaimers', 'recruitpro'); ?></a></li>
                                        <li><a href="#limitation-liability"><?php esc_html_e('Limitation of Liability', 'recruitpro'); ?></a></li>
                                        <li><a href="#termination"><?php esc_html_e('Termination', 'recruitpro'); ?></a></li>
                                        <li><a href="#governing-law"><?php esc_html_e('Governing Law', 'recruitpro'); ?></a></li>
                                        <li><a href="#dispute-resolution"><?php esc_html_e('Dispute Resolution', 'recruitpro'); ?></a></li>
                                        <li><a href="#changes-terms"><?php esc_html_e('Changes to Terms', 'recruitpro'); ?></a></li>
                                        <li><a href="#contact-information"><?php esc_html_e('Contact Information', 'recruitpro'); ?></a></li>
                                    </ol>
                                </nav>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- 1. Acceptance of Terms -->
                    <section class="terms-section" id="acceptance-terms">
                        <h2 class="section-title"><?php esc_html_e('1. Acceptance of Terms', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php printf(esc_html__('By accessing and using the services provided by %s ("%s", "we", "us", or "our"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.', 'recruitpro'), esc_html($company_legal_name), esc_html($company_name)); ?></p>
                            
                            <div class="agreement-scope">
                                <h3><?php esc_html_e('Scope of Agreement', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('This agreement applies to:', 'recruitpro'); ?></p>
                                <ul class="scope-list">
                                    <li><?php esc_html_e('Use of our website and online services', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('All recruitment and staffing services', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Consultation and advisory services', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('All communications and interactions with our team', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Access to resources, tools, and content', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="legal-capacity">
                                <h3><?php esc_html_e('Legal Capacity', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('By using our services, you represent that you have the legal authority to enter into this agreement on behalf of yourself or the organization you represent.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Description of Services -->
                    <section class="terms-section" id="services-description">
                        <h2 class="section-title"><?php esc_html_e('2. Description of Services', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php printf(esc_html__('%s provides professional recruitment and talent acquisition services to businesses and organizations across various industries.', 'recruitpro'), esc_html($company_name)); ?></p>

                            <div class="service-categories">
                                
                                <div class="service-category">
                                    <h3 class="category-title"><?php esc_html_e('Recruitment Services', 'recruitpro'); ?></h3>
                                    <ul class="service-list">
                                        <li><?php esc_html_e('Executive search and leadership recruitment', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Permanent placement services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Temporary and contract staffing', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Contingency recruitment services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Retained search services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Recruitment process outsourcing (RPO)', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="service-category">
                                    <h3 class="category-title"><?php esc_html_e('Additional Services', 'recruitpro'); ?></h3>
                                    <ul class="service-list">
                                        <li><?php esc_html_e('Executive assessment and evaluation', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Salary benchmarking and market analysis', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('HR consulting and advisory services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Employer branding and attraction strategies', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Talent pipeline development', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Career coaching and guidance', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>

                            <div class="service-limitations">
                                <h3><?php esc_html_e('Service Limitations', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Our services are subject to availability, market conditions, and regulatory requirements. We reserve the right to modify, suspend, or discontinue any service at any time with appropriate notice.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 3. Eligibility and Use -->
                    <section class="terms-section" id="eligibility-use">
                        <h2 class="section-title"><?php esc_html_e('3. Eligibility and Use', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Use of our services is subject to the following eligibility requirements and usage conditions:', 'recruitpro'); ?></p>

                            <div class="eligibility-requirements">
                                <h3><?php esc_html_e('Eligibility Requirements', 'recruitpro'); ?></h3>
                                <ul class="requirement-list">
                                    <li><?php esc_html_e('You must be at least 18 years of age', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('You must have legal authority to enter employment relationships', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Organizations must be legitimately established and registered', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('All provided information must be accurate and truthful', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Compliance with applicable employment laws and regulations', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="prohibited-uses">
                                <h3><?php esc_html_e('Prohibited Uses', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('You may not use our services for:', 'recruitpro'); ?></p>
                                <ul class="prohibited-list">
                                    <li><?php esc_html_e('Any unlawful purpose or activity', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Discrimination based on protected characteristics', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Harassment, intimidation, or abusive behavior', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Unauthorized access to systems or data', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Distribution of malicious software or content', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Interference with our services or security measures', 'recruitpro'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- 4. User Responsibilities -->
                    <section class="terms-section" id="user-responsibilities">
                        <h2 class="section-title"><?php esc_html_e('4. User Responsibilities', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Users of our services have specific responsibilities to ensure professional and lawful conduct:', 'recruitpro'); ?></p>

                            <div class="responsibility-categories">
                                
                                <div class="responsibility-category">
                                    <h3 class="category-title"><?php esc_html_e('For Job Seekers/Candidates', 'recruitpro'); ?></h3>
                                    <ul class="responsibility-list">
                                        <li><?php esc_html_e('Provide accurate and up-to-date personal and professional information', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Respond promptly to communications and interview requests', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Maintain confidentiality of client information and opportunities', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Honor commitments and professional obligations', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Notify us immediately of any changes in availability or circumstances', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="responsibility-category">
                                    <h3 class="category-title"><?php esc_html_e('For Employers/Clients', 'recruitpro'); ?></h3>
                                    <ul class="responsibility-list">
                                        <li><?php esc_html_e('Provide accurate job descriptions and requirements', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Comply with all applicable employment laws and regulations', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Conduct fair and non-discriminatory hiring practices', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Pay agreed fees within specified timeframes', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Provide timely feedback on candidates and recruitment progress', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>

                            <div class="general-conduct">
                                <h3><?php esc_html_e('General Conduct Requirements', 'recruitpro'); ?></h3>
                                <ul class="conduct-list">
                                    <li><?php esc_html_e('Maintain professional and respectful communication', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Respect intellectual property rights and confidentiality', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Comply with data protection and privacy requirements', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Report any concerns or violations promptly', 'recruitpro'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- 5. Recruitment Service Terms -->
                    <section class="terms-section" id="service-terms">
                        <h2 class="section-title"><?php esc_html_e('5. Recruitment Service Terms', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Specific terms and conditions apply to our recruitment and staffing services:', 'recruitpro'); ?></p>

                            <div class="service-terms-categories">
                                
                                <div class="service-terms-category">
                                    <h3 class="terms-title"><?php esc_html_e('Service Agreements', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('Individual service agreements may be required for specific engagements, which will supplement these general terms. In case of conflict, specific agreement terms will prevail.', 'recruitpro'); ?></p>
                                </div>

                                <div class="service-terms-category">
                                    <h3 class="terms-title"><?php esc_html_e('Placement Guarantees', 'recruitpro'); ?></h3>
                                    <ul class="guarantee-list">
                                        <li><?php esc_html_e('Permanent placements: 3-6 month replacement guarantee period', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Temporary assignments: Quality assurance and replacement support', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Executive search: Extended guarantee periods as specified in agreements', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Guarantees apply only to performance-related terminations', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="service-terms-category">
                                    <h3 class="terms-title"><?php esc_html_e('Service Standards', 'recruitpro'); ?></h3>
                                    <ul class="standards-list">
                                        <li><?php esc_html_e('Professional screening and assessment of all candidates', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Reference checking and employment verification', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Compliance with equal opportunity employment practices', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Regular communication and progress updates', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Confidential handling of all information and processes', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- 6. Fees and Payment -->
                    <section class="terms-section" id="fees-payment">
                        <h2 class="section-title"><?php esc_html_e('6. Fees and Payment', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Our fees and payment terms are as follows:', 'recruitpro'); ?></p>

                            <div class="fee-structure">
                                <h3><?php esc_html_e('Fee Structure', 'recruitpro'); ?></h3>
                                <ul class="fee-list">
                                    <li><?php esc_html_e('Contingency recruitment: 15-25% of annual salary', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Retained search: 25-35% of annual salary', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Temporary staffing: Margin on hourly rates', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Consultation services: Hourly or project-based rates', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="payment-terms">
                                <h3><?php esc_html_e('Payment Terms', 'recruitpro'); ?></h3>
                                <ul class="payment-list">
                                    <li><?php esc_html_e('Contingency fees: Due upon candidate start date', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Retained search: Phased payments as specified in agreement', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Invoice payment: 30 days net unless otherwise agreed', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Late payment: Interest charges may apply as per local regulations', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="expenses-costs">
                                <h3><?php esc_html_e('Expenses and Additional Costs', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Reasonable expenses incurred in the course of recruitment activities may be charged separately with prior approval. This may include advertising, travel, assessment tools, and background checking services.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 7. Intellectual Property -->
                    <section class="terms-section" id="intellectual-property">
                        <h2 class="section-title"><?php esc_html_e('7. Intellectual Property', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Intellectual property rights relating to our services and content are protected as follows:', 'recruitpro'); ?></p>

                            <div class="ip-ownership">
                                <h3><?php esc_html_e('Our Intellectual Property', 'recruitpro'); ?></h3>
                                <ul class="ip-list">
                                    <li><?php esc_html_e('Website content, design, and functionality', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Recruitment methodologies and processes', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Assessment tools and evaluation frameworks', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Training materials and resources', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Brand names, logos, and trademarks', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="user-content">
                                <h3><?php esc_html_e('User-Provided Content', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('You retain ownership of content you provide to us (such as CVs, job descriptions, company information) but grant us license to use such content for the purpose of providing our services.', 'recruitpro'); ?></p>
                            </div>

                            <div class="usage-restrictions">
                                <h3><?php esc_html_e('Usage Restrictions', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('You may not reproduce, distribute, modify, or create derivative works of our intellectual property without express written permission.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 8. Confidentiality -->
                    <section class="terms-section" id="confidentiality">
                        <h2 class="section-title"><?php esc_html_e('8. Confidentiality', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Confidentiality is fundamental to our recruitment services. We maintain strict confidentiality protocols:', 'recruitpro'); ?></p>

                            <div class="confidentiality-commitments">
                                <h3><?php esc_html_e('Our Confidentiality Commitments', 'recruitpro'); ?></h3>
                                <ul class="confidentiality-list">
                                    <li><?php esc_html_e('Candidate information protected and shared only with authorized parties', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Client business information kept strictly confidential', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Salary and compensation details protected', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Search processes conducted with appropriate discretion', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Data security measures implemented and maintained', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="mutual-confidentiality">
                                <h3><?php esc_html_e('Mutual Confidentiality', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('All parties agree to maintain confidentiality of sensitive information shared during the recruitment process. This includes business strategies, compensation details, and proprietary information.', 'recruitpro'); ?></p>
                            </div>

                            <div class="confidentiality-exceptions">
                                <h3><?php esc_html_e('Exceptions to Confidentiality', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Confidentiality obligations do not apply to information that is publicly available, independently developed, or required to be disclosed by law or legal process.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 9. Data Protection -->
                    <section class="terms-section" id="data-protection">
                        <h2 class="section-title"><?php esc_html_e('9. Data Protection', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We are committed to protecting personal data in accordance with applicable data protection laws and regulations:', 'recruitpro'); ?></p>

                            <div class="data-compliance">
                                <h3><?php esc_html_e('Legal Compliance', 'recruitpro'); ?></h3>
                                <ul class="compliance-list">
                                    <li><?php esc_html_e('GDPR compliance for EU data subjects', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Local data protection law compliance', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Industry-specific privacy requirements', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('International data transfer safeguards', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="data-processing">
                                <h3><?php esc_html_e('Data Processing Principles', 'recruitpro'); ?></h3>
                                <ul class="processing-list">
                                    <li><?php esc_html_e('Data collected only for legitimate recruitment purposes', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Processing based on legal grounds as defined in our Privacy Policy', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Data retention in accordance with legal requirements and business needs', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Individual rights respected and facilitated', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="privacy-reference">
                                <h3><?php esc_html_e('Privacy Policy Reference', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('For detailed information about how we collect, use, and protect your personal data, please refer to our Privacy Policy, which forms part of these terms.', 'recruitpro'); ?></p>
                                <a href="<?php echo esc_url(get_page_link(get_page_by_path('privacy'))); ?>" class="privacy-link">
                                    <?php esc_html_e('View Privacy Policy', 'recruitpro'); ?>
                                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- 10. Disclaimers -->
                    <section class="terms-section" id="disclaimers">
                        <h2 class="section-title"><?php esc_html_e('10. Disclaimers', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Our services are provided subject to the following disclaimers:', 'recruitpro'); ?></p>

                            <div class="service-disclaimers">
                                <h3><?php esc_html_e('Service Disclaimers', 'recruitpro'); ?></h3>
                                <ul class="disclaimer-list">
                                    <li><?php esc_html_e('No guarantee of successful placement or employment outcomes', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Market conditions and candidate availability may affect results', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Third-party services and tools used are subject to their own terms', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Accuracy of candidate or client information cannot be absolutely guaranteed', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="website-disclaimers">
                                <h3><?php esc_html_e('Website and Content Disclaimers', 'recruitpro'); ?></h3>
                                <ul class="website-disclaimer-list">
                                    <li><?php esc_html_e('Website provided "as is" without warranties of any kind', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Content accuracy and completeness not guaranteed', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Technical issues or service interruptions may occur', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('External links provided for convenience only', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="professional-advice">
                                <h3><?php esc_html_e('Professional Advice Disclaimer', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Our services and content are for general guidance only and should not be considered as legal, financial, or professional advice. Users should seek independent professional advice for specific situations.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 11. Limitation of Liability -->
                    <section class="terms-section" id="limitation-liability">
                        <h2 class="section-title"><?php esc_html_e('11. Limitation of Liability', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Our liability is limited as follows:', 'recruitpro'); ?></p>

                            <div class="liability-limitations">
                                <h3><?php esc_html_e('General Limitations', 'recruitpro'); ?></h3>
                                <ul class="limitation-list">
                                    <li><?php esc_html_e('Total liability limited to fees paid for specific services', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('No liability for indirect, consequential, or punitive damages', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('No liability for third-party actions or omissions', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Limitation applies regardless of the basis of the claim', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="excluded-damages">
                                <h3><?php esc_html_e('Excluded Damages', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We shall not be liable for:', 'recruitpro'); ?></p>
                                <ul class="exclusion-list">
                                    <li><?php esc_html_e('Lost profits or business opportunities', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Business interruption or operational losses', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Reputational damage or goodwill loss', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Data loss or corruption (subject to data protection obligations)', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="liability-exceptions">
                                <h3><?php esc_html_e('Exceptions to Limitations', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Limitations do not apply to liability that cannot be excluded by law, including death or personal injury caused by negligence, fraud, or intentional misconduct.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 12. Termination -->
                    <section class="terms-section" id="termination">
                        <h2 class="section-title"><?php esc_html_e('12. Termination', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('These terms and our service relationship may be terminated under the following circumstances:', 'recruitpro'); ?></p>

                            <div class="termination-grounds">
                                <h3><?php esc_html_e('Grounds for Termination', 'recruitpro'); ?></h3>
                                <ul class="termination-list">
                                    <li><?php esc_html_e('Mutual agreement between parties', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Breach of terms and conditions', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Non-payment of fees or charges', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Unlawful or unethical conduct', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Completion of service engagement', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="termination-process">
                                <h3><?php esc_html_e('Termination Process', 'recruitpro'); ?></h3>
                                <ul class="process-list">
                                    <li><?php esc_html_e('Written notice required for termination', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Opportunity to remedy breaches where appropriate', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Immediate termination for serious breaches', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Final invoicing and payment settlement', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="post-termination">
                                <h3><?php esc_html_e('Post-Termination Obligations', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Following termination, confidentiality obligations, intellectual property rights, and payment obligations remain in effect. Data handling will continue in accordance with our Privacy Policy and applicable law.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 13. Governing Law -->
                    <section class="terms-section" id="governing-law">
                        <h2 class="section-title"><?php esc_html_e('13. Governing Law', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php printf(esc_html__('These terms are governed by the laws of %s, and any disputes will be subject to the jurisdiction of the courts of %s.', 'recruitpro'), esc_html($governing_law), esc_html($jurisdiction)); ?></p>

                            <div class="legal-framework">
                                <h3><?php esc_html_e('Applicable Legal Framework', 'recruitpro'); ?></h3>
                                <ul class="legal-list">
                                    <li><?php esc_html_e('Employment law and regulations', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Data protection and privacy legislation', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Consumer protection laws where applicable', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Professional and industry-specific regulations', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="international-considerations">
                                <h3><?php esc_html_e('International Considerations', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('For international clients and candidates, additional laws and regulations may apply. We will work within the applicable legal frameworks of relevant jurisdictions.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 14. Dispute Resolution -->
                    <section class="terms-section" id="dispute-resolution">
                        <h2 class="section-title"><?php esc_html_e('14. Dispute Resolution', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We are committed to resolving disputes efficiently and professionally through the following process:', 'recruitpro'); ?></p>

                            <div class="resolution-process">
                                <h3><?php esc_html_e('Resolution Process', 'recruitpro'); ?></h3>
                                <ol class="resolution-steps">
                                    <li>
                                        <strong><?php esc_html_e('Direct Communication:', 'recruitpro'); ?></strong>
                                        <?php esc_html_e('Initial attempt to resolve through direct discussion with our team', 'recruitpro'); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e('Formal Complaint:', 'recruitpro'); ?></strong>
                                        <?php esc_html_e('Written complaint submitted through our formal process', 'recruitpro'); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e('Management Review:', 'recruitpro'); ?></strong>
                                        <?php esc_html_e('Senior management review and response within 14 days', 'recruitpro'); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e('Mediation:', 'recruitpro'); ?></strong>
                                        <?php esc_html_e('Professional mediation if mutual agreement cannot be reached', 'recruitpro'); ?>
                                    </li>
                                    <li>
                                        <strong><?php esc_html_e('Legal Proceedings:', 'recruitpro'); ?></strong>
                                        <?php esc_html_e('Court proceedings as a last resort', 'recruitpro'); ?>
                                    </li>
                                </ol>
                            </div>

                            <div class="complaint-procedure">
                                <h3><?php esc_html_e('How to Make a Complaint', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('To submit a formal complaint:', 'recruitpro'); ?></p>
                                <ul class="complaint-steps">
                                    <li><?php esc_html_e('Email your complaint to our legal contact address', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Include all relevant details and supporting documentation', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Specify the outcome you are seeking', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('We will acknowledge receipt within 48 hours', 'recruitpro'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- 15. Changes to Terms -->
                    <section class="terms-section" id="changes-terms">
                        <h2 class="section-title"><?php esc_html_e('15. Changes to These Terms', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We may update these terms from time to time to reflect changes in our services, legal requirements, or business practices.', 'recruitpro'); ?></p>

                            <div class="change-notification">
                                <h3><?php esc_html_e('How We Notify You of Changes', 'recruitpro'); ?></h3>
                                <ul class="notification-methods">
                                    <li><?php esc_html_e('Email notification to registered users and clients', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Website announcement and banner notification', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Updated "Last Modified" date on this page', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Direct communication for significant changes', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="effective-periods">
                                <h3><?php esc_html_e('Effective Periods', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('Changes will become effective 30 days after notification unless a shorter period is required by law. Continued use of our services after changes become effective constitutes acceptance of the revised terms.', 'recruitpro'); ?></p>
                            </div>

                            <div class="disagreement-with-changes">
                                <h3><?php esc_html_e('If You Disagree with Changes', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('If you do not agree with changes to these terms, you may terminate your use of our services. For ongoing engagements, we will work with you to complete services under existing terms where possible.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 16. Contact Information -->
                    <section class="terms-section" id="contact-information">
                        <h2 class="section-title"><?php esc_html_e('16. Contact Information', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('For questions about these terms or to exercise any rights, please contact us:', 'recruitpro'); ?></p>

                            <div class="contact-details">
                                
                                <div class="contact-method">
                                    <h3 class="contact-title">
                                        <i class="fas fa-building" aria-hidden="true"></i>
                                        <?php esc_html_e('Company Information', 'recruitpro'); ?>
                                    </h3>
                                    <div class="contact-info">
                                        <p class="contact-detail">
                                            <strong><?php esc_html_e('Legal Name:', 'recruitpro'); ?></strong> 
                                            <?php echo esc_html($company_legal_name); ?>
                                        </p>
                                        <?php if ($company_registration) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Registration Number:', 'recruitpro'); ?></strong> 
                                                <?php echo esc_html($company_registration); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($recruitment_license) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Recruitment License:', 'recruitpro'); ?></strong> 
                                                <?php echo esc_html($recruitment_license); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($company_address) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Registered Address:', 'recruitpro'); ?></strong><br>
                                                <?php echo nl2br(esc_html($company_address)); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="contact-method">
                                    <h3 class="contact-title">
                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                        <?php esc_html_e('Legal Contact', 'recruitpro'); ?>
                                    </h3>
                                    <div class="contact-info">
                                        <p class="contact-detail">
                                            <strong><?php esc_html_e('Legal Inquiries:', 'recruitpro'); ?></strong> 
                                            <a href="mailto:<?php echo esc_attr($legal_contact_email); ?>?subject=Terms of Service Inquiry"><?php echo esc_html($legal_contact_email); ?></a>
                                        </p>
                                        <p class="contact-detail">
                                            <strong><?php esc_html_e('General Contact:', 'recruitpro'); ?></strong> 
                                            <a href="mailto:<?php echo esc_attr($contact_email); ?>?subject=Terms Question"><?php echo esc_html($contact_email); ?></a>
                                        </p>
                                        <?php if ($contact_phone) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Phone:', 'recruitpro'); ?></strong> 
                                                <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>

                            <div class="response-commitment">
                                <h3><?php esc_html_e('Response Commitment', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We are committed to responding to all inquiries about these terms within 5 business days. For urgent legal matters, please indicate the urgency in your communication.', 'recruitpro'); ?></p>
                            </div>

                            <div class="additional-resources">
                                <h3><?php esc_html_e('Additional Legal Resources', 'recruitpro'); ?></h3>
                                <ul class="resource-links">
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('privacy'))); ?>"><?php esc_html_e('Privacy Policy', 'recruitpro'); ?></a></li>
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('gdpr'))); ?>"><?php esc_html_e('GDPR Compliance', 'recruitpro'); ?></a></li>
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>"><?php esc_html_e('Contact Us', 'recruitpro'); ?></a></li>
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('sitemap'))); ?>"><?php esc_html_e('Site Map', 'recruitpro'); ?></a></li>
                                </ul>
                            </div>

                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('terms-sidebar')) : ?>
                <aside id="secondary" class="widget-area terms-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('terms-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   TERMS OF SERVICE PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE TERMS OF SERVICE FEATURES:

✅ COMPLETE LEGAL DOCUMENTATION
- Acceptance of terms and scope of agreement
- Detailed service descriptions and limitations
- User eligibility requirements and prohibited uses
- Comprehensive user responsibilities for all parties
- Recruitment-specific service terms and guarantees

✅ RECRUITMENT INDUSTRY SPECIFIC
- Placement guarantees and service standards
- Professional screening and assessment requirements
- Equal opportunity employment compliance
- Confidential search process protocols
- Industry-specific fee structures and payment terms

✅ INTELLECTUAL PROPERTY PROTECTION
- Website content and methodology protection
- Assessment tools and evaluation frameworks
- Brand names, logos, and trademark rights
- User-provided content licensing terms
- Usage restrictions and permission requirements

✅ COMPREHENSIVE LIABILITY FRAMEWORK
- Service disclaimers and market condition factors
- Website and content accuracy disclaimers
- Professional advice limitation disclaimers
- Liability limitations and damage exclusions
- Exceptions for legally required protections

✅ PROFESSIONAL SERVICE TERMS
- Individual service agreement supplementation
- Placement guarantee periods (3-6 months)
- Quality assurance and replacement support
- Professional standards and compliance requirements
- Confidential information handling protocols

✅ LEGAL COMPLIANCE STRUCTURE
- Data protection and privacy law compliance
- Employment law and regulatory adherence
- International considerations and frameworks
- Governing law and jurisdiction specifications
- Professional and industry regulation compliance

✅ DISPUTE RESOLUTION PROCESS
- Five-step resolution procedure (communication to legal)
- Formal complaint submission process
- Management review and response timeframes
- Professional mediation and legal proceedings
- Clear escalation path and commitment timing

✅ BUSINESS PROTECTION MEASURES
- Fee structure transparency (contingency, retained, temporary)
- Payment terms and late payment provisions
- Expense approval and additional cost protocols
- Termination grounds and process requirements
- Post-termination obligation maintenance

✅ PROFESSIONAL PRESENTATION
- Table of contents navigation system
- Clear section organization and numbering
- Legal meta information (effective dates, updates)
- Professional typography and accessibility
- Mobile-responsive design and presentation

✅ CONTACT AND COMPLIANCE INTEGRATION
- Legal entity information and registration details
- Multiple contact methods for legal inquiries
- Response commitment timeframes (5 business days)
- Additional legal resource linking
- Professional escalation and support procedures

PERFECT FOR:
- Recruitment agencies and executive search firms
- HR consultancies and staffing companies
- Professional services organizations
- Employment law compliance requirements
- Legal risk mitigation and protection

BUSINESS BENEFITS:
- Comprehensive legal protection and risk mitigation
- Professional credibility and compliance demonstration
- Clear service expectations and responsibility definition
- Dispute prevention through clear terms
- Professional relationship framework establishment

RECRUITMENT INDUSTRY SPECIFIC:
- Employment law compliance and protection
- Professional standards and service guarantees
- Confidentiality and data protection protocols
- Industry-specific fee structures and terms
- Placement guarantee and replacement provisions

LEGAL COMPLIANCE:
- Terms acceptance and legal capacity verification
- Service limitation and disclaimer protections
- Intellectual property and confidentiality safeguards
- Data protection and privacy law integration
- Governing law and dispute resolution frameworks

TECHNICAL FEATURES:
- Schema.org markup for SEO optimization
- WordPress customizer integration and settings
- Mobile-responsive design and accessibility
- Professional navigation and user experience
- Legal document structure and organization

*/
?>