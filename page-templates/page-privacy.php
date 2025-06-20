<?php
/**
 * Template Name: Privacy Policy Page
 *
 * Comprehensive privacy policy page template for recruitment agencies
 * providing detailed privacy information, data handling practices, and
 * legal compliance documentation. Essential for transparency and regulatory
 * compliance in recruitment operations handling candidate and client data.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-privacy.php
 * Purpose: Privacy policy and data protection information
 * Dependencies: WordPress core, theme functions, legal compliance
 * Features: Comprehensive privacy documentation, legal transparency, user rights
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_privacy_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_privacy_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_legal_name = get_theme_mod('recruitpro_company_legal_name', $company_name);
$company_address = get_theme_mod('recruitpro_company_address', '');
$company_registration = get_theme_mod('recruitpro_company_registration', '');
$dpo_name = get_theme_mod('recruitpro_dpo_name', '');
$dpo_email = get_theme_mod('recruitpro_dpo_email', get_option('admin_email'));
$show_table_of_contents = get_theme_mod('recruitpro_privacy_show_toc', true);

// Privacy policy settings
$policy_last_updated = get_theme_mod('recruitpro_privacy_last_updated', date('Y-m-d'));
$policy_effective_date = get_theme_mod('recruitpro_privacy_effective_date', '2018-05-25');
$data_retention_period = get_theme_mod('recruitpro_privacy_retention_period', '7 years');
$cookie_retention = get_theme_mod('recruitpro_privacy_cookie_retention', '2 years');

// Contact information
$contact_phone = get_theme_mod('recruitpro_contact_phone', '');
$contact_email = get_theme_mod('recruitpro_contact_email', get_option('admin_email'));

// Schema.org markup for privacy policy page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => sprintf(__('Privacy Policy for %s - Comprehensive information about how we collect, use, and protect your personal data in our recruitment services.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Privacy Policy',
        'description' => 'Data protection and privacy practices for recruitment services'
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

<main id="primary" class="site-main privacy-policy-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header privacy-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <div class="policy-meta">
                                <div class="meta-item">
                                    <strong><?php esc_html_e('Effective Date:', 'recruitpro'); ?></strong>
                                    <?php echo esc_html(date('F j, Y', strtotime($policy_effective_date))); ?>
                                </div>
                                <div class="meta-item">
                                    <strong><?php esc_html_e('Last Updated:', 'recruitpro'); ?></strong>
                                    <?php echo esc_html(date('F j, Y', strtotime($policy_last_updated))); ?>
                                </div>
                            </div>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('This Privacy Policy explains how %s collects, uses, and protects your personal data when you use our recruitment services or visit our website. We are committed to protecting your privacy and ensuring transparency in our data handling practices.', 'recruitpro'), esc_html($company_name)); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Table of Contents -->
                    <?php if ($show_table_of_contents) : ?>
                        <section class="privacy-toc" id="table-of-contents">
                            <div class="toc-container">
                                <h2 class="toc-title"><?php esc_html_e('Table of Contents', 'recruitpro'); ?></h2>
                                <nav class="toc-navigation" aria-label="<?php esc_attr_e('Privacy Policy Navigation', 'recruitpro'); ?>">
                                    <ol class="toc-list">
                                        <li><a href="#data-controller"><?php esc_html_e('Data Controller Information', 'recruitpro'); ?></a></li>
                                        <li><a href="#data-we-collect"><?php esc_html_e('Information We Collect', 'recruitpro'); ?></a></li>
                                        <li><a href="#how-we-use-data"><?php esc_html_e('How We Use Your Information', 'recruitpro'); ?></a></li>
                                        <li><a href="#legal-basis"><?php esc_html_e('Legal Basis for Processing', 'recruitpro'); ?></a></li>
                                        <li><a href="#data-sharing"><?php esc_html_e('Data Sharing and Disclosure', 'recruitpro'); ?></a></li>
                                        <li><a href="#international-transfers"><?php esc_html_e('International Data Transfers', 'recruitpro'); ?></a></li>
                                        <li><a href="#data-retention"><?php esc_html_e('Data Retention', 'recruitpro'); ?></a></li>
                                        <li><a href="#your-rights"><?php esc_html_e('Your Privacy Rights', 'recruitpro'); ?></a></li>
                                        <li><a href="#security-measures"><?php esc_html_e('Security Measures', 'recruitpro'); ?></a></li>
                                        <li><a href="#cookies-tracking"><?php esc_html_e('Cookies and Tracking', 'recruitpro'); ?></a></li>
                                        <li><a href="#third-party-services"><?php esc_html_e('Third-Party Services', 'recruitpro'); ?></a></li>
                                        <li><a href="#children-privacy"><?php esc_html_e('Children\'s Privacy', 'recruitpro'); ?></a></li>
                                        <li><a href="#policy-changes"><?php esc_html_e('Changes to This Policy', 'recruitpro'); ?></a></li>
                                        <li><a href="#contact-information"><?php esc_html_e('Contact Information', 'recruitpro'); ?></a></li>
                                    </ol>
                                </nav>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- 1. Data Controller Information -->
                    <section class="privacy-section" id="data-controller">
                        <h2 class="section-title"><?php esc_html_e('1. Data Controller Information', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php printf(esc_html__('The data controller responsible for your personal data is %s, a recruitment agency specializing in professional placement services.', 'recruitpro'), esc_html($company_legal_name)); ?></p>
                            
                            <div class="contact-details">
                                <h3><?php esc_html_e('Company Details:', 'recruitpro'); ?></h3>
                                <ul class="company-info">
                                    <li><strong><?php esc_html_e('Legal Name:', 'recruitpro'); ?></strong> <?php echo esc_html($company_legal_name); ?></li>
                                    <?php if ($company_registration) : ?>
                                        <li><strong><?php esc_html_e('Registration Number:', 'recruitpro'); ?></strong> <?php echo esc_html($company_registration); ?></li>
                                    <?php endif; ?>
                                    <?php if ($company_address) : ?>
                                        <li><strong><?php esc_html_e('Address:', 'recruitpro'); ?></strong> <?php echo esc_html($company_address); ?></li>
                                    <?php endif; ?>
                                    <li><strong><?php esc_html_e('Email:', 'recruitpro'); ?></strong> <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></li>
                                    <?php if ($contact_phone) : ?>
                                        <li><strong><?php esc_html_e('Phone:', 'recruitpro'); ?></strong> <a href="tel:<?php echo esc_attr(str_replace(' ', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>

                            <?php if ($dpo_name || $dpo_email) : ?>
                                <div class="dpo-details">
                                    <h3><?php esc_html_e('Data Protection Officer:', 'recruitpro'); ?></h3>
                                    <ul class="dpo-info">
                                        <?php if ($dpo_name) : ?>
                                            <li><strong><?php esc_html_e('Name:', 'recruitpro'); ?></strong> <?php echo esc_html($dpo_name); ?></li>
                                        <?php endif; ?>
                                        <li><strong><?php esc_html_e('Email:', 'recruitpro'); ?></strong> <a href="mailto:<?php echo esc_attr($dpo_email); ?>"><?php echo esc_html($dpo_email); ?></a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- 2. Information We Collect -->
                    <section class="privacy-section" id="data-we-collect">
                        <h2 class="section-title"><?php esc_html_e('2. Information We Collect', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('As a recruitment agency, we collect various types of personal information to provide our services effectively. The information we collect depends on how you interact with our services.', 'recruitpro'); ?></p>

                            <div class="data-categories">
                                
                                <div class="data-category">
                                    <h3 class="category-title"><?php esc_html_e('Personal and Contact Information', 'recruitpro'); ?></h3>
                                    <ul class="data-list">
                                        <li><?php esc_html_e('Full name and preferred name', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Email address and phone number', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Home address and postal code', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Date of birth and nationality', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Emergency contact information', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="data-category">
                                    <h3 class="category-title"><?php esc_html_e('Professional Information', 'recruitpro'); ?></h3>
                                    <ul class="data-list">
                                        <li><?php esc_html_e('CV/Resume and cover letters', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Employment history and work experience', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Education and qualifications', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Skills, competencies, and certifications', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Professional references and recommendations', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Salary expectations and availability', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Work authorization status', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="data-category">
                                    <h3 class="category-title"><?php esc_html_e('Recruitment Process Information', 'recruitpro'); ?></h3>
                                    <ul class="data-list">
                                        <li><?php esc_html_e('Interview notes and assessments', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Test results and evaluation scores', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Background check and verification results', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Reference check outcomes', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Communication records (emails, calls, messages)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Meeting and appointment records', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="data-category">
                                    <h3 class="category-title"><?php esc_html_e('Website and Technical Information', 'recruitpro'); ?></h3>
                                    <ul class="data-list">
                                        <li><?php esc_html_e('IP address and device information', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Browser type and operating system', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Pages visited and time spent on site', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Search queries and job applications', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Cookie preferences and tracking data', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Login credentials and account information', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="data-category">
                                    <h3 class="category-title"><?php esc_html_e('Sensitive Personal Data', 'recruitpro'); ?></h3>
                                    <p class="category-notice"><?php esc_html_e('We may collect certain sensitive personal data only when legally required or with your explicit consent:', 'recruitpro'); ?></p>
                                    <ul class="data-list">
                                        <li><?php esc_html_e('Equal opportunity monitoring data (voluntary)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Disability information for accommodation purposes', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Criminal history (where legally required)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Health information for specific roles', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- 3. How We Use Your Information -->
                    <section class="privacy-section" id="how-we-use-data">
                        <h2 class="section-title"><?php esc_html_e('3. How We Use Your Information', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We use your personal information for various purposes related to our recruitment services and business operations:', 'recruitpro'); ?></p>

                            <div class="usage-purposes">
                                
                                <div class="purpose-category">
                                    <h3 class="purpose-title"><?php esc_html_e('Core Recruitment Services', 'recruitpro'); ?></h3>
                                    <ul class="purpose-list">
                                        <li><?php esc_html_e('Matching candidates with suitable job opportunities', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Presenting candidate profiles to potential employers', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Conducting interviews and assessments', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Facilitating the recruitment and placement process', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Providing career advice and guidance', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Managing ongoing candidate relationships', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="purpose-category">
                                    <h3 class="purpose-title"><?php esc_html_e('Verification and Due Diligence', 'recruitpro'); ?></h3>
                                    <ul class="purpose-list">
                                        <li><?php esc_html_e('Verifying identity and qualifications', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Conducting reference checks', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Performing background screening when required', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Confirming work authorization status', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Ensuring compliance with legal requirements', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="purpose-category">
                                    <h3 class="purpose-title"><?php esc_html_e('Communication and Marketing', 'recruitpro'); ?></h3>
                                    <ul class="purpose-list">
                                        <li><?php esc_html_e('Communicating about job opportunities', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Sending recruitment-related updates', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Providing industry insights and career advice', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Marketing our services (with consent)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Conducting surveys and feedback collection', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="purpose-category">
                                    <h3 class="purpose-title"><?php esc_html_e('Business Operations', 'recruitpro'); ?></h3>
                                    <ul class="purpose-list">
                                        <li><?php esc_html_e('Managing our website and online services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Analyzing service usage and improving our offerings', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Maintaining accurate records and databases', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Processing payments and managing invoices', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Complying with legal and regulatory obligations', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Protecting against fraud and security threats', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- 4. Legal Basis for Processing -->
                    <section class="privacy-section" id="legal-basis">
                        <h2 class="section-title"><?php esc_html_e('4. Legal Basis for Processing', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Under GDPR, we must have a legal basis for processing your personal data. We rely on the following legal bases:', 'recruitpro'); ?></p>

                            <div class="legal-bases">
                                
                                <div class="legal-basis-item">
                                    <h3 class="basis-title"><?php esc_html_e('Legitimate Interest', 'recruitpro'); ?></h3>
                                    <p class="basis-description"><?php esc_html_e('We process most personal data based on our legitimate interest in providing recruitment services. This includes:', 'recruitpro'); ?></p>
                                    <ul class="basis-activities">
                                        <li><?php esc_html_e('Candidate sourcing and matching', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Building and maintaining candidate databases', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Presenting candidates to clients', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Business development and networking', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="legal-basis-item">
                                    <h3 class="basis-title"><?php esc_html_e('Consent', 'recruitpro'); ?></h3>
                                    <p class="basis-description"><?php esc_html_e('We rely on your consent for:', 'recruitpro'); ?></p>
                                    <ul class="basis-activities">
                                        <li><?php esc_html_e('Marketing communications and newsletters', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Processing sensitive personal data', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Cookies and tracking technologies', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Sharing data with specific third parties', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="legal-basis-item">
                                    <h3 class="basis-title"><?php esc_html_e('Contract Performance', 'recruitpro'); ?></h3>
                                    <p class="basis-description"><?php esc_html_e('We process data to fulfill our contractual obligations:', 'recruitpro'); ?></p>
                                    <ul class="basis-activities">
                                        <li><?php esc_html_e('Delivering agreed recruitment services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Managing candidate placements', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Processing payments and invoicing', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Providing support and customer service', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="legal-basis-item">
                                    <h3 class="basis-title"><?php esc_html_e('Legal Obligation', 'recruitpro'); ?></h3>
                                    <p class="basis-description"><?php esc_html_e('We process data to comply with legal requirements:', 'recruitpro'); ?></p>
                                    <ul class="basis-activities">
                                        <li><?php esc_html_e('Right to work verification', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Tax and employment law compliance', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Record keeping requirements', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Regulatory reporting obligations', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </section>

                    <!-- 5. Data Sharing and Disclosure -->
                    <section class="privacy-section" id="data-sharing">
                        <h2 class="section-title"><?php esc_html_e('5. Data Sharing and Disclosure', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We may share your personal data with third parties in specific circumstances. We ensure appropriate safeguards are in place for all data sharing.', 'recruitpro'); ?></p>

                            <div class="sharing-categories">
                                
                                <div class="sharing-category">
                                    <h3 class="sharing-title"><?php esc_html_e('Clients and Employers', 'recruitpro'); ?></h3>
                                    <p class="sharing-description"><?php esc_html_e('We share candidate information with potential employers as part of our recruitment services:', 'recruitpro'); ?></p>
                                    <ul class="sharing-details">
                                        <li><?php esc_html_e('CV and professional profiles', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Interview assessments and feedback', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Reference check results', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Relevant contact information', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="sharing-category">
                                    <h3 class="sharing-title"><?php esc_html_e('Service Providers', 'recruitpro'); ?></h3>
                                    <p class="sharing-description"><?php esc_html_e('We work with trusted service providers who assist with our operations:', 'recruitpro'); ?></p>
                                    <ul class="sharing-details">
                                        <li><?php esc_html_e('Background screening companies', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Skills assessment platforms', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Communication service providers (Ringover, WhatsApp)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('IT and website hosting services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Payment processing services', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="sharing-category">
                                    <h3 class="sharing-title"><?php esc_html_e('Strategic Partners', 'recruitpro'); ?></h3>
                                    <p class="sharing-description"><?php esc_html_e('We may share data with our strategic partners when appropriate:', 'recruitpro'); ?></p>
                                    <ul class="sharing-details">
                                        <li><?php esc_html_e('International recruitment network partners', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Specialized recruitment agencies', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Educational institutions and training providers', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Professional development partners', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="sharing-category">
                                    <h3 class="sharing-title"><?php esc_html_e('Legal Requirements', 'recruitpro'); ?></h3>
                                    <p class="sharing-description"><?php esc_html_e('We may disclose personal data when legally required:', 'recruitpro'); ?></p>
                                    <ul class="sharing-details">
                                        <li><?php esc_html_e('Court orders and legal proceedings', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Regulatory investigations', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Law enforcement requests', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Protection of rights and safety', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>

                            <div class="sharing-safeguards">
                                <h3><?php esc_html_e('Data Sharing Safeguards', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('All data sharing is subject to:', 'recruitpro'); ?></p>
                                <ul class="safeguards-list">
                                    <li><?php esc_html_e('Contractual data protection agreements', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Strict confidentiality requirements', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Purpose limitation and data minimization', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Security and encryption measures', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Regular compliance monitoring', 'recruitpro'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- 6. International Data Transfers -->
                    <section class="privacy-section" id="international-transfers">
                        <h2 class="section-title"><?php esc_html_e('6. International Data Transfers', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('As a recruitment agency with international partnerships, we may transfer your personal data outside your country of residence. We ensure appropriate safeguards are in place for all international transfers.', 'recruitpro'); ?></p>

                            <div class="transfer-scenarios">
                                <h3><?php esc_html_e('When We Transfer Data Internationally', 'recruitpro'); ?></h3>
                                <ul class="transfer-list">
                                    <li><?php esc_html_e('International job placements and cross-border recruitment', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Collaboration with international recruitment partners', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Use of cloud-based services and technology platforms', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Background screening in multiple jurisdictions', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Global client and candidate database management', 'recruitpro'); ?></li>
                                </ul>
                            </div>

                            <div class="transfer-safeguards">
                                <h3><?php esc_html_e('Transfer Safeguards', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We protect international data transfers through:', 'recruitpro'); ?></p>
                                <ul class="safeguards-list">
                                    <li><?php esc_html_e('European Commission adequacy decisions', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Standard Contractual Clauses (SCCs)', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Binding Corporate Rules where applicable', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Explicit consent for specific transfers', 'recruitpro'); ?></li>
                                    <li><?php esc_html_e('Regular assessment of transfer risks', 'recruitpro'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- 7. Data Retention -->
                    <section class="privacy-section" id="data-retention">
                        <h2 class="section-title"><?php esc_html_e('7. Data Retention', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We retain personal data only for as long as necessary to fulfill the purposes for which it was collected, comply with legal obligations, and resolve disputes.', 'recruitpro'); ?></p>

                            <div class="retention-periods">
                                
                                <div class="retention-category">
                                    <h3 class="retention-title"><?php esc_html_e('Candidate Data', 'recruitpro'); ?></h3>
                                    <div class="retention-details">
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Active Candidates:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('2 years after last contact or application', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Placed Candidates:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php echo esc_html($data_retention_period); ?> <?php esc_html_e('for legal compliance', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Unsuccessful Applications:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('6 months after recruitment process completion', 'recruitpro'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="retention-category">
                                    <h3 class="retention-title"><?php esc_html_e('Client Data', 'recruitpro'); ?></h3>
                                    <div class="retention-details">
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Active Clients:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('Duration of relationship plus 3 years', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Contract and Invoice Data:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php echo esc_html($data_retention_period); ?> <?php esc_html_e('for tax and legal compliance', 'recruitpro'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="retention-category">
                                    <h3 class="retention-title"><?php esc_html_e('Website and Communication Data', 'recruitpro'); ?></h3>
                                    <div class="retention-details">
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Website Analytics:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('26 months (anonymized after 14 months)', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Email Communications:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('3 years or duration of relationship', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Cookies:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php echo esc_html($cookie_retention); ?> <?php esc_html_e('maximum', 'recruitpro'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="retention-category">
                                    <h3 class="retention-title"><?php esc_html_e('Marketing Data', 'recruitpro'); ?></h3>
                                    <div class="retention-details">
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Newsletter Subscribers:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('Until consent is withdrawn', 'recruitpro'); ?></span>
                                        </div>
                                        <div class="retention-item">
                                            <span class="retention-type"><?php esc_html_e('Marketing Preferences:', 'recruitpro'); ?></span>
                                            <span class="retention-period"><?php esc_html_e('3 years after last interaction', 'recruitpro'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="retention-notice">
                                <h3><?php esc_html_e('Automatic Deletion', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We have automated systems to ensure data is deleted according to our retention schedule. You will receive advance notice before any automatic deletion, giving you the opportunity to request an extension if you wish to continue receiving our services.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 8. Your Privacy Rights -->
                    <section class="privacy-section" id="your-rights">
                        <h2 class="section-title"><?php esc_html_e('8. Your Privacy Rights', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('You have several rights regarding your personal data. These rights may vary depending on your location and applicable privacy laws.', 'recruitpro'); ?></p>

                            <div class="privacy-rights">
                                
                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Access', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Request a copy of all personal data we hold about you, including how we use it and who we share it with.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Rectification', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Request correction of inaccurate or incomplete personal data we hold about you.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Erasure', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Request deletion of your personal data in certain circumstances, such as when it is no longer necessary for the original purpose.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-pause" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Restrict Processing', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Request limitation of how we process your personal data in specific situations.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-download" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Data Portability', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Request your personal data in a portable format to transfer to another service provider.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-stop" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Object', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Object to processing based on legitimate interest or for direct marketing purposes.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-times-circle" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Withdraw Consent', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('Withdraw your consent at any time where processing is based on consent.', 'recruitpro'); ?></p>
                                </div>

                                <div class="right-item">
                                    <h3 class="right-title">
                                        <i class="fas fa-gavel" aria-hidden="true"></i>
                                        <?php esc_html_e('Right to Lodge a Complaint', 'recruitpro'); ?>
                                    </h3>
                                    <p class="right-description"><?php esc_html_e('File a complaint with your local data protection authority if you believe we have not handled your data correctly.', 'recruitpro'); ?></p>
                                </div>

                            </div>

                            <div class="rights-exercise">
                                <h3><?php esc_html_e('How to Exercise Your Rights', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('To exercise any of these rights, please contact us using the information provided at the end of this policy. We will respond to your request within 30 days. You may need to provide identification to verify your identity.', 'recruitpro'); ?></p>
                                
                                <div class="rights-actions">
                                    <a href="<?php echo esc_url(home_url('/gdpr-compliance/')); ?>" class="btn btn-primary">
                                        <?php esc_html_e('Submit Data Request', 'recruitpro'); ?>
                                    </a>
                                    <a href="mailto:<?php echo esc_attr($dpo_email); ?>" class="btn btn-outline">
                                        <?php esc_html_e('Contact Data Protection Officer', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 9. Security Measures -->
                    <section class="privacy-section" id="security-measures">
                        <h2 class="section-title"><?php esc_html_e('9. Security Measures', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We implement comprehensive security measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.', 'recruitpro'); ?></p>

                            <div class="security-categories">
                                
                                <div class="security-category">
                                    <h3 class="security-title">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        <?php esc_html_e('Technical Safeguards', 'recruitpro'); ?>
                                    </h3>
                                    <ul class="security-measures">
                                        <li><?php esc_html_e('SSL/TLS encryption for data transmission', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Database encryption for stored data', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Secure cloud hosting with ISO 27001 certification', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Regular security updates and patches', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Firewall protection and intrusion detection', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Secure backup and disaster recovery systems', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="security-category">
                                    <h3 class="security-title">
                                        <i class="fas fa-users-cog" aria-hidden="true"></i>
                                        <?php esc_html_e('Access Controls', 'recruitpro'); ?>
                                    </h3>
                                    <ul class="security-measures">
                                        <li><?php esc_html_e('Multi-factor authentication for system access', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Role-based access control (RBAC)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Regular access reviews and deprovisioning', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Strong password policies', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Automatic session timeouts', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Audit logs and monitoring', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="security-category">
                                    <h3 class="security-title">
                                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                        <?php esc_html_e('Organizational Measures', 'recruitpro'); ?>
                                    </h3>
                                    <ul class="security-measures">
                                        <li><?php esc_html_e('Regular staff security training', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Data protection impact assessments', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Incident response procedures', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Third-party security assessments', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Regular security audits and penetration testing', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Clear desk and clean screen policies', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>

                            <div class="breach-notification">
                                <h3><?php esc_html_e('Data Breach Response', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('In the unlikely event of a data breach, we will notify relevant authorities within 72 hours and affected individuals without undue delay, where required by law. We will provide clear information about the breach and steps taken to mitigate any risks.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                   <!-- 10. Cookies and Tracking -->
                    <section class="privacy-section" id="cookies-tracking">
                        <h2 class="section-title"><?php esc_html_e('10. Cookies and Tracking Technologies', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('We use cookies and similar tracking technologies to enhance your experience on our website and provide personalized services.', 'recruitpro'); ?></p>

                            <div class="cookies-categories">
                                
                                <div class="cookie-category">
                                    <h3 class="cookie-title"><?php esc_html_e('Essential Cookies', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('These cookies are necessary for the website to function properly and cannot be disabled. They include:', 'recruitpro'); ?></p>
                                    <ul class="cookie-list">
                                        <li><?php esc_html_e('Session management and user authentication', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Security and fraud prevention', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Load balancing and website functionality', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Form submission and data processing', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="cookie-category">
                                    <h3 class="cookie-title"><?php esc_html_e('Analytics Cookies', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('We use analytics cookies to understand how visitors interact with our website:', 'recruitpro'); ?></p>
                                    <ul class="cookie-list">
                                        <li><?php esc_html_e('Google Analytics for website performance analysis', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('User behavior tracking and heatmap analysis', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Job application and conversion tracking', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Website optimization and A/B testing', 'recruitpro'); ?></li>
                                    </ul>
                                    <p><strong><?php esc_html_e('Retention Period:', 'recruitpro'); ?></strong> <?php echo esc_html($cookie_retention); ?></p>
                                </div>

                                <div class="cookie-category">
                                    <h3 class="cookie-title"><?php esc_html_e('Marketing Cookies', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('These cookies help us provide relevant recruitment opportunities and career advice:', 'recruitpro'); ?></p>
                                    <ul class="cookie-list">
                                        <li><?php esc_html_e('Job recommendation personalization', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Email marketing campaign tracking', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Social media integration and sharing', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Targeted advertising and retargeting', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>

                            <div class="cookie-management">
                                <h3><?php esc_html_e('Managing Cookies', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('You can control and manage cookies through your browser settings or our cookie preference center. Most browsers allow you to refuse or delete cookies, but this may limit website functionality.', 'recruitpro'); ?></p>
                                <p><a href="#" class="cookie-settings-link"><?php esc_html_e('Manage Cookie Preferences', 'recruitpro'); ?></a></p>
                            </div>
                        </div>
                    </section>

                    <!-- 11. Third-Party Services -->
                    <section class="privacy-section" id="third-party-services">
                        <h2 class="section-title"><?php esc_html_e('11. Third-Party Services and Integrations', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('Our recruitment services integrate with various third-party platforms and tools to enhance our capabilities and provide comprehensive services.', 'recruitpro'); ?></p>

                            <div class="third-party-categories">
                                
                                <div class="service-category">
                                    <h3 class="service-title"><?php esc_html_e('Professional Networks', 'recruitpro'); ?></h3>
                                    <ul class="service-list">
                                        <li><?php esc_html_e('LinkedIn for professional networking and candidate sourcing', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Indeed, Glassdoor, and other job boards for posting positions', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Industry-specific platforms for specialized recruitment', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Professional association databases and networks', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="service-category">
                                    <h3 class="service-title"><?php esc_html_e('Assessment and Testing Tools', 'recruitpro'); ?></h3>
                                    <ul class="service-list">
                                        <li><?php esc_html_e('Psychometric testing and personality assessment platforms', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Technical skills testing and coding challenges', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Background check and verification services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Reference checking and employment verification', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="service-category">
                                    <h3 class="service-title"><?php esc_html_e('Communication and Collaboration', 'recruitpro'); ?></h3>
                                    <ul class="service-list">
                                        <li><?php esc_html_e('Video interviewing platforms (Zoom, Teams, etc.)', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Email marketing and communication tools', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Calendar scheduling and appointment management', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Document sharing and collaboration platforms', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                            </div>

                            <div class="data-sharing-notice">
                                <h3><?php esc_html_e('Data Sharing with Third Parties', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('When using these services, some of your data may be shared with third parties under appropriate data processing agreements. We ensure all third-party processors meet our data protection standards and comply with applicable privacy regulations.', 'recruitpro'); ?></p>
                            </div>
                        </div>
                    </section>

                    <!-- 12. Children's Privacy -->
                    <section class="privacy-section" id="children-privacy">
                        <h2 class="section-title"><?php esc_html_e('12. Children\'s Privacy Protection', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <div class="children-protection">
                                <p><?php esc_html_e('Our recruitment services are designed for adults seeking employment opportunities. We do not knowingly collect personal information from children under the age of 16 (or the minimum age for processing personal data in your jurisdiction).', 'recruitpro'); ?></p>
                                
                                <div class="age-restrictions">
                                    <h3><?php esc_html_e('Age Requirements', 'recruitpro'); ?></h3>
                                    <ul class="requirements-list">
                                        <li><?php esc_html_e('Minimum age of 16 for job applications and CV submissions', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Minimum age of 18 for most professional recruitment services', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Parental consent required for users under 18 in certain jurisdictions', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Educational and internship programs may have different age requirements', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="inadvertent-collection">
                                    <h3><?php esc_html_e('Inadvertent Data Collection', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('If we become aware that we have inadvertently collected personal information from a child under the minimum age, we will take immediate steps to delete such information from our records. Parents or guardians who believe we may have collected information from their child should contact us immediately.', 'recruitpro'); ?></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 13. Changes to This Privacy Policy -->
                    <section class="privacy-section" id="policy-changes">
                        <h2 class="section-title"><?php esc_html_e('13. Changes to This Privacy Policy', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <div class="policy-updates">
                                <p><?php esc_html_e('We may update this Privacy Policy from time to time to reflect changes in our practices, services, legal requirements, or for other operational, legal, or regulatory reasons.', 'recruitpro'); ?></p>

                                <div class="notification-process">
                                    <h3><?php esc_html_e('How We Notify You of Changes', 'recruitpro'); ?></h3>
                                    <ul class="notification-methods">
                                        <li><?php esc_html_e('Email notification to registered users and clients', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Website banner announcement for significant changes', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Updated "Last Modified" date at the top of this policy', 'recruitpro'); ?></li>
                                        <li><?php esc_html_e('Direct communication for material changes affecting your rights', 'recruitpro'); ?></li>
                                    </ul>
                                </div>

                                <div class="effective-dates">
                                    <h3><?php esc_html_e('Effective Dates', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('Changes to this Privacy Policy will become effective 30 days after posting the updated version on our website, unless a shorter period is required by law or the changes are minor corrections or clarifications.', 'recruitpro'); ?></p>
                                    <p><?php esc_html_e('Your continued use of our services after the effective date constitutes acceptance of the revised Privacy Policy.', 'recruitpro'); ?></p>
                                </div>

                                <div class="version-history">
                                    <h3><?php esc_html_e('Policy Version History', 'recruitpro'); ?></h3>
                                    <ul class="version-list">
                                        <li><strong><?php esc_html_e('Current Version:', 'recruitpro'); ?></strong> <?php echo esc_html(date('F j, Y', strtotime($policy_last_updated))); ?></li>
                                        <li><strong><?php esc_html_e('Previous Version:', 'recruitpro'); ?></strong> <?php esc_html_e('Available upon request', 'recruitpro'); ?></li>
                                        <li><strong><?php esc_html_e('Original Policy:', 'recruitpro'); ?></strong> <?php echo esc_html(date('F j, Y', strtotime($policy_effective_date))); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 14. Contact Information -->
                    <section class="privacy-section" id="contact-information">
                        <h2 class="section-title"><?php esc_html_e('14. Contact Information', 'recruitpro'); ?></h2>
                        <div class="section-content">
                            <p><?php esc_html_e('If you have any questions about this Privacy Policy, our data handling practices, or wish to exercise your privacy rights, please contact us using the information below:', 'recruitpro'); ?></p>

                            <div class="contact-details">
                                
                                <div class="contact-method">
                                    <h3 class="contact-title">
                                        <i class="fas fa-user-shield" aria-hidden="true"></i>
                                        <?php esc_html_e('Data Protection Officer', 'recruitpro'); ?>
                                    </h3>
                                    <div class="contact-info">
                                        <?php if ($dpo_name) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Name:', 'recruitpro'); ?></strong> 
                                                <?php echo esc_html($dpo_name); ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="contact-detail">
                                            <strong><?php esc_html_e('Email:', 'recruitpro'); ?></strong> 
                                            <a href="mailto:<?php echo esc_attr($dpo_email); ?>?subject=Privacy Policy Inquiry"><?php echo esc_html($dpo_email); ?></a>
                                        </p>
                                        <p class="contact-description"><?php esc_html_e('For privacy-related inquiries, data subject requests, and GDPR compliance matters.', 'recruitpro'); ?></p>
                                    </div>
                                </div>

                                <div class="contact-method">
                                    <h3 class="contact-title">
                                        <i class="fas fa-building" aria-hidden="true"></i>
                                        <?php esc_html_e('Company Contact', 'recruitpro'); ?>
                                    </h3>
                                    <div class="contact-info">
                                        <p class="contact-detail">
                                            <strong><?php esc_html_e('Company:', 'recruitpro'); ?></strong> 
                                            <?php echo esc_html($company_legal_name); ?>
                                        </p>
                                        <p class="contact-detail">
                                            <strong><?php esc_html_e('Email:', 'recruitpro'); ?></strong> 
                                            <a href="mailto:<?php echo esc_attr($contact_email); ?>?subject=Privacy Policy Question"><?php echo esc_html($contact_email); ?></a>
                                        </p>
                                        <?php if ($contact_phone) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Phone:', 'recruitpro'); ?></strong> 
                                                <a href="tel:<?php echo esc_attr(str_replace([' ', '-', '(', ')'], '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($company_address) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Address:', 'recruitpro'); ?></strong><br>
                                                <?php echo nl2br(esc_html($company_address)); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($company_registration) : ?>
                                            <p class="contact-detail">
                                                <strong><?php esc_html_e('Registration:', 'recruitpro'); ?></strong> 
                                                <?php echo esc_html($company_registration); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>

                            <div class="response-timeframe">
                                <h3><?php esc_html_e('Response Timeframe', 'recruitpro'); ?></h3>
                                <p><?php esc_html_e('We are committed to responding to all privacy-related inquiries within 30 days of receipt. For urgent matters or data subject requests, we will acknowledge your request within 72 hours and provide a full response as quickly as possible.', 'recruitpro'); ?></p>
                            </div>

                            <div class="supervisory-authority">
                                <h3 class="authority-title">
                                    <i class="fas fa-balance-scale" aria-hidden="true"></i>
                                    <?php esc_html_e('Supervisory Authority', 'recruitpro'); ?>
                                </h3>
                                <p><?php esc_html_e('You have the right to lodge a complaint with your local data protection supervisory authority if you believe we have not adequately addressed your privacy concerns.', 'recruitpro'); ?></p>
                                <p><?php esc_html_e('You can find contact information for European data protection authorities at:', 'recruitpro'); ?></p>
                                <p><a href="https://edpb.europa.eu/about-edpb/about-edpb/members_en" target="_blank" rel="noopener noreferrer" class="external-link">
                                    <?php esc_html_e('European Data Protection Board - Member Authorities', 'recruitpro'); ?>
                                    <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                </a></p>
                            </div>

                            <div class="additional-resources">
                                <h3><?php esc_html_e('Additional Resources', 'recruitpro'); ?></h3>
                                <ul class="resource-links">
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('gdpr'))); ?>"><?php esc_html_e('GDPR Compliance Information', 'recruitpro'); ?></a></li>
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('terms'))); ?>"><?php esc_html_e('Terms of Service', 'recruitpro'); ?></a></li>
                                    <li><a href="#" class="cookie-policy-link"><?php esc_html_e('Cookie Policy', 'recruitpro'); ?></a></li>
                                    <li><a href="<?php echo esc_url(get_page_link(get_page_by_path('contact'))); ?>"><?php esc_html_e('Contact Us', 'recruitpro'); ?></a></li>
                                </ul>
                            </div>

                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('privacy-sidebar')) : ?>
                <aside id="secondary" class="widget-area privacy-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('privacy-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   PRIVACY POLICY PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE PRIVACY POLICY FEATURES:

✅ COMPLETE LEGAL DOCUMENTATION
- Data controller information
- Comprehensive data collection details
- Clear usage explanations
- Legal basis for processing
- Data sharing and disclosure policies

✅ RECRUITMENT INDUSTRY SPECIFIC
- CV and application data handling
- Client and candidate data management
- Background screening information
- Reference check procedures
- International placement data transfers

✅ GDPR COMPLIANCE
- All 8 privacy rights explained
- Legal basis for each processing activity
- Data retention schedules
- International transfer safeguards
- Breach notification procedures

✅ TECHNICAL TRANSPARENCY
- Website data collection
- Cookie and tracking policies
- Third-party service integration
- Security measures and safeguards
- Data protection technologies

✅ USER-FRIENDLY PRESENTATION
- Table of contents navigation
- Clear section organization
- Professional typography
- Mobile-responsive design
- Accessibility compliance

✅ CONTACT AND SUPPORT
- Data Protection Officer details
- Multiple contact methods
- Rights exercise procedures
- Supervisory authority information
- Complaint process guidance

✅ BUSINESS INTEGRATION
- Partnership data sharing
- Service provider information
- International network details
- Technology platform disclosures
- Compliance monitoring

✅ LEGAL COMPLIANCE
- GDPR requirements fulfillment
- Data retention schedules
- Consent management
- Rights exercise procedures
- Regulatory reporting

✅ SCHEMA.ORG OPTIMIZATION
- WebPage structured data
- Privacy policy markup
- Search engine optimization
- Professional presentation
- Trust signals

✅ MAINTENANCE FEATURES
- Last updated tracking
- Effective date management
- Version control ready
- Easy content updates
- Policy change notifications

PERFECT FOR:
- Legal compliance requirements
- Regulatory compliance
- Trust building with users
- Professional credibility
- Risk mitigation
- Transparency demonstration

BUSINESS BENEFITS:
- Legal protection
- Regulatory compliance
- User trust building
- Professional positioning
- Risk management
- Transparency commitment

RECRUITMENT INDUSTRY SPECIFIC:
- Candidate data protection
- Client confidentiality
- CV handling procedures
- Background check policies
- International placement rules

TECHNICAL FEATURES:
- WordPress customizer integration
- Schema.org optimization
- Mobile-responsive design
- Accessibility compliance
- Performance optimized
- Easy content management

*/
?>