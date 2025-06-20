<?php
/**
 * Template Name: Contact Page
 *
 * Professional contact page template for recruitment agencies featuring
 * general inquiry contact form, office locations, business hours, and team
 * contact details. Includes multiple security layers for spam protection
 * while maintaining SEO optimization and professional presentation.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-contact.php
 * Purpose: General contact page for recruitment agency communication
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Contact form, office locations, business hours, team contacts, security
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_contact_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_contact_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_contact_form = get_theme_mod('recruitpro_contact_show_form', true);
$show_office_locations = get_theme_mod('recruitpro_contact_show_locations', true);
$show_business_hours = get_theme_mod('recruitpro_contact_show_hours', true);
$show_team_contacts = get_theme_mod('recruitpro_contact_show_team', true);
$show_map = get_theme_mod('recruitpro_contact_show_map', true);

// Contact information
$main_phone = get_theme_mod('recruitpro_contact_phone', '');
$main_email = get_theme_mod('recruitpro_contact_email', get_option('admin_email'));
$main_address = get_theme_mod('recruitpro_contact_address', '');
$business_hours = get_theme_mod('recruitpro_business_hours', '');
$emergency_contact = get_theme_mod('recruitpro_emergency_contact', '');
$whatsapp_number = get_theme_mod('recruitpro_whatsapp_number', '');
$linkedin_profile = get_theme_mod('recruitpro_social_linkedin', '');
$google_maps_embed = get_theme_mod('recruitpro_google_maps_embed', '');

// Security settings (won't conflict with security plugin)
$enable_honeypot = get_theme_mod('recruitpro_contact_honeypot', true);
$enable_time_check = get_theme_mod('recruitpro_contact_time_check', true);
$enable_rate_limiting = get_theme_mod('recruitpro_contact_rate_limit', true);
$recaptcha_site_key = get_theme_mod('recruitpro_recaptcha_site_key', '');
$enable_recaptcha = !empty($recaptcha_site_key);

// Schema.org markup for contact page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company_name,
    'url' => home_url(),
    'logo' => get_theme_mod('recruitpro_site_logo', ''),
    'contactPoint' => array(
        '@type' => 'ContactPoint',
        'contactType' => 'Customer Service',
        'telephone' => $main_phone,
        'email' => $main_email,
        'areaServed' => 'Global',
        'availableLanguage' => array('English'),
        'hoursAvailable' => array(
            '@type' => 'OpeningHoursSpecification',
            'description' => $business_hours
        )
    ),
    'address' => array(
        '@type' => 'PostalAddress',
        'streetAddress' => $main_address
    )
);
?>

<!-- Schema.org Organization with Contact Info Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- reCAPTCHA v3 (if enabled) -->
<?php if ($enable_recaptcha) : ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr($recaptcha_site_key); ?>"></script>
<?php endif; ?>

<main id="primary" class="site-main contact-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="contact-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header contact-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('We\'re here to help you with any inquiries about our recruitment services. Get in touch with our professional team today.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Contact Methods Overview -->
                    <section class="contact-methods-overview" id="contact-methods">
                        <div class="methods-container">
                            <div class="methods-grid">
                                
                                <?php if ($main_phone) : ?>
                                    <div class="contact-method">
                                        <div class="method-icon">
                                            <i class="fas fa-phone" aria-hidden="true"></i>
                                        </div>
                                        <div class="method-content">
                                            <h3 class="method-title"><?php esc_html_e('Call Us', 'recruitpro'); ?></h3>
                                            <p class="method-description"><?php esc_html_e('Speak directly with our recruitment consultants', 'recruitpro'); ?></p>
                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $main_phone)); ?>" 
                                               class="method-link">
                                                <?php echo esc_html($main_phone); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($main_email) : ?>
                                    <div class="contact-method">
                                        <div class="method-icon">
                                            <i class="fas fa-envelope" aria-hidden="true"></i>
                                        </div>
                                        <div class="method-content">
                                            <h3 class="method-title"><?php esc_html_e('Email Us', 'recruitpro'); ?></h3>
                                            <p class="method-description"><?php esc_html_e('Send us your inquiries and questions', 'recruitpro'); ?></p>
                                            <a href="mailto:<?php echo esc_attr($main_email); ?>" 
                                               class="method-link">
                                                <?php echo esc_html($main_email); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($whatsapp_number) : ?>
                                    <div class="contact-method">
                                        <div class="method-icon">
                                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                        </div>
                                        <div class="method-content">
                                            <h3 class="method-title"><?php esc_html_e('WhatsApp', 'recruitpro'); ?></h3>
                                            <p class="method-description"><?php esc_html_e('Quick messages and instant communication', 'recruitpro'); ?></p>
                                            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^+\d]/', '', $whatsapp_number)); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="method-link">
                                                <?php echo esc_html($whatsapp_number); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($linkedin_profile) : ?>
                                    <div class="contact-method">
                                        <div class="method-icon">
                                            <i class="fab fa-linkedin" aria-hidden="true"></i>
                                        </div>
                                        <div class="method-content">
                                            <h3 class="method-title"><?php esc_html_e('LinkedIn', 'recruitpro'); ?></h3>
                                            <p class="method-description"><?php esc_html_e('Connect with us professionally', 'recruitpro'); ?></p>
                                            <a href="<?php echo esc_url($linkedin_profile); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="method-link">
                                                <?php esc_html_e('Follow Us', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

                <!-- Contact Form Section -->
                <?php if ($show_contact_form) : ?>
                    <section class="contact-form-section" id="contact-form">
                        <div class="form-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Send Us a Message', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Fill out the form below and we\'ll get back to you as soon as possible', 'recruitpro'); ?></p>
                            </div>

                            <form class="contact-form" id="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                                
                                <!-- Honeypot Field (Hidden from users, catches bots) -->
                                <?php if ($enable_honeypot) : ?>
                                    <div class="honeypot-field" style="position: absolute; left: -9999px; top: -9999px;" aria-hidden="true">
                                        <label for="website-url"><?php esc_html_e('Website URL (leave blank)', 'recruitpro'); ?></label>
                                        <input type="url" 
                                               id="website-url" 
                                               name="website_url" 
                                               tabindex="-1" 
                                               autocomplete="off">
                                    </div>
                                <?php endif; ?>

                                <!-- Time Check Field (Hidden, prevents too-fast submissions) -->
                                <?php if ($enable_time_check) : ?>
                                    <input type="hidden" 
                                           name="form_start_time" 
                                           value="<?php echo esc_attr(time()); ?>">
                                <?php endif; ?>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact-name"><?php esc_html_e('Full Name', 'recruitpro'); ?> <span class="required">*</span></label>
                                        <input type="text" 
                                               id="contact-name" 
                                               name="name" 
                                               class="form-control"
                                               required 
                                               maxlength="100"
                                               pattern="[A-Za-z\s\-\']+"
                                               title="<?php esc_attr_e('Please enter a valid name (letters, spaces, hyphens, and apostrophes only)', 'recruitpro'); ?>"
                                               aria-describedby="contact-name-error">
                                        <div id="contact-name-error" class="error-message" role="alert"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-email"><?php esc_html_e('Email Address', 'recruitpro'); ?> <span class="required">*</span></label>
                                        <input type="email" 
                                               id="contact-email" 
                                               name="email" 
                                               class="form-control"
                                               required 
                                               maxlength="254"
                                               aria-describedby="contact-email-error">
                                        <div id="contact-email-error" class="error-message" role="alert"></div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact-phone"><?php esc_html_e('Phone Number', 'recruitpro'); ?></label>
                                        <input type="tel" 
                                               id="contact-phone" 
                                               name="phone" 
                                               class="form-control"
                                               maxlength="20"
                                               pattern="[\+]?[0-9\s\-\(\)]+"
                                               title="<?php esc_attr_e('Please enter a valid phone number', 'recruitpro'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="contact-company"><?php esc_html_e('Company/Organization', 'recruitpro'); ?></label>
                                        <input type="text" 
                                               id="contact-company" 
                                               name="company" 
                                               class="form-control"
                                               maxlength="100">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact-subject"><?php esc_html_e('Subject', 'recruitpro'); ?> <span class="required">*</span></label>
                                    <select id="contact-subject" 
                                            name="subject" 
                                            class="form-control"
                                            required>
                                        <option value=""><?php esc_html_e('Select inquiry type', 'recruitpro'); ?></option>
                                        <option value="general"><?php esc_html_e('General Information', 'recruitpro'); ?></option>
                                        <option value="services"><?php esc_html_e('Our Services', 'recruitpro'); ?></option>
                                        <option value="partnership"><?php esc_html_e('Partnership Opportunity', 'recruitpro'); ?></option>
                                        <option value="careers"><?php esc_html_e('Career Opportunities', 'recruitpro'); ?></option>
                                        <option value="media"><?php esc_html_e('Media Inquiry', 'recruitpro'); ?></option>
                                        <option value="feedback"><?php esc_html_e('Feedback', 'recruitpro'); ?></option>
                                        <option value="other"><?php esc_html_e('Other', 'recruitpro'); ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="contact-message"><?php esc_html_e('Message', 'recruitpro'); ?> <span class="required">*</span></label>
                                    <textarea id="contact-message" 
                                              name="message" 
                                              class="form-control"
                                              rows="6" 
                                              required 
                                              maxlength="2000"
                                              placeholder="<?php esc_attr_e('Please provide details about your inquiry...', 'recruitpro'); ?>"
                                              aria-describedby="message-counter"></textarea>
                                    <div id="message-counter" class="character-counter">
                                        <span class="current-count">0</span> / <span class="max-count">2000</span> <?php esc_html_e('characters', 'recruitpro'); ?>
                                    </div>
                                </div>

                                <div class="form-group form-privacy">
                                    <label class="checkbox-label">
                                        <input type="checkbox" 
                                               name="privacy_consent" 
                                               value="1" 
                                               required>
                                        <span class="checkbox-custom"></span>
                                        <?php esc_html_e('I agree to the', 'recruitpro'); ?> 
                                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" 
                                           target="_blank" 
                                           rel="noopener noreferrer">
                                            <?php esc_html_e('Privacy Policy', 'recruitpro'); ?>
                                        </a>
                                        <?php esc_html_e('and consent to data processing.', 'recruitpro'); ?> <span class="required">*</span>
                                    </label>
                                </div>

                                <div class="form-group form-newsletter">
                                    <label class="checkbox-label">
                                        <input type="checkbox" 
                                               name="newsletter_consent" 
                                               value="1">
                                        <span class="checkbox-custom"></span>
                                        <?php esc_html_e('I would like to receive updates about recruitment services and industry insights.', 'recruitpro'); ?>
                                    </label>
                                </div>

                                <!-- reCAPTCHA v3 (if enabled) -->
                                <?php if ($enable_recaptcha) : ?>
                                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                                <?php endif; ?>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary" id="contact-submit">
                                        <span class="button-text"><?php esc_html_e('Send Message', 'recruitpro'); ?></span>
                                        <span class="button-loading" style="display: none;" aria-hidden="true">
                                            <i class="fas fa-spinner fa-spin"></i>
                                            <?php esc_html_e('Sending...', 'recruitpro'); ?>
                                        </span>
                                    </button>
                                </div>

                                <input type="hidden" name="action" value="recruitpro_contact_form">
                                <input type="hidden" name="page_id" value="<?php echo esc_attr($page_id); ?>">
                                <input type="hidden" name="user_agent" value="">
                                <input type="hidden" name="referrer" value="">
                                <?php wp_nonce_field('recruitpro_contact_nonce', 'nonce'); ?>
                            </form>

                            <!-- Success/Error Messages -->
                            <div id="contact-form-message" class="form-message" style="display: none;" role="alert" aria-live="polite"></div>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Office Locations -->
                <?php if ($show_office_locations) : ?>
                    <section class="office-locations-section" id="office-locations">
                        <div class="locations-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Our Office Locations', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Visit us at any of our convenient locations worldwide', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Get office locations from custom fields or use defaults
                            $office_locations = get_post_meta($page_id, '_office_locations', true);
                            
                            if (!$office_locations) {
                                // Default office locations
                                $office_locations = array(
                                    array(
                                        'name' => esc_html__('Head Office', 'recruitpro'),
                                        'address' => $main_address ?: '123 Business Street, London, UK',
                                        'phone' => $main_phone ?: '+44 20 1234 5678',
                                        'email' => $main_email,
                                        'hours' => $business_hours ?: 'Monday - Friday: 9:00 AM - 6:00 PM',
                                        'is_headquarters' => true
                                    )
                                );
                            }

                            if ($office_locations && is_array($office_locations)) :
                            ?>
                                <div class="office-locations-grid">
                                    <?php foreach ($office_locations as $location) : ?>
                                        <div class="office-location-item">
                                            <div class="location-content">
                                                
                                                <div class="location-header">
                                                    <h3 class="location-name">
                                                        <?php echo esc_html($location['name']); ?>
                                                        <?php if (!empty($location['is_headquarters'])) : ?>
                                                            <span class="headquarters-badge"><?php esc_html_e('HQ', 'recruitpro'); ?></span>
                                                        <?php endif; ?>
                                                    </h3>
                                                </div>

                                                <div class="location-details">
                                                    
                                                    <?php if (!empty($location['address'])) : ?>
                                                        <div class="location-item">
                                                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                            <span><?php echo esc_html($location['address']); ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($location['phone'])) : ?>
                                                        <div class="location-item">
                                                            <i class="fas fa-phone" aria-hidden="true"></i>
                                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $location['phone'])); ?>">
                                                                <?php echo esc_html($location['phone']); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($location['email'])) : ?>
                                                        <div class="location-item">
                                                            <i class="fas fa-envelope" aria-hidden="true"></i>
                                                            <a href="mailto:<?php echo esc_attr($location['email']); ?>">
                                                                <?php echo esc_html($location['email']); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($location['hours'])) : ?>
                                                        <div class="location-item">
                                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                                            <span><?php echo esc_html($location['hours']); ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                </div>

                                                <div class="location-actions">
                                                    <?php if (!empty($location['address'])) : ?>
                                                        <a href="https://maps.google.com/?q=<?php echo urlencode($location['address']); ?>" 
                                                           target="_blank" 
                                                           rel="noopener noreferrer"
                                                           class="btn btn-outline">
                                                            <?php esc_html_e('Get Directions', 'recruitpro'); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Google Maps Integration -->
                <?php if ($show_map && $google_maps_embed) : ?>
                    <section class="google-maps-section" id="google-maps">
                        <div class="maps-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Find Us on the Map', 'recruitpro'); ?></h2>
                            </div>

                            <div class="google-maps-embed">
                                <iframe src="<?php echo esc_url($google_maps_embed); ?>"
                                        width="100%" 
                                        height="400" 
                                        style="border:0;" 
                                        allowfullscreen="" 
                                        loading="lazy" 
                                        referrerpolicy="no-referrer-when-downgrade"
                                        title="<?php esc_attr_e('Office Location Map', 'recruitpro'); ?>">
                                </iframe>
                            </div>

                        </div>
                    </section>
                <?php endif; ?>

                <!-- Business Hours & Emergency Contact -->
                <?php if ($show_business_hours || $emergency_contact) : ?>
                    <section class="business-info-section" id="business-info">
                        <div class="business-info-container">
                            
                            <div class="business-info-grid">
                                
                                <?php if ($show_business_hours && $business_hours) : ?>
                                    <div class="business-info-item">
                                        <h3 class="info-title"><?php esc_html_e('Business Hours', 'recruitpro'); ?></h3>
                                        <div class="info-content">
                                            <?php echo wp_kses_post(nl2br($business_hours)); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($emergency_contact) : ?>
                                    <div class="business-info-item">
                                        <h3 class="info-title"><?php esc_html_e('Emergency Contact', 'recruitpro'); ?></h3>
                                        <div class="info-content">
                                            <p><?php esc_html_e('For urgent recruitment matters outside business hours:', 'recruitpro'); ?></p>
                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $emergency_contact)); ?>" 
                                               class="emergency-contact-link">
                                                <?php echo esc_html($emergency_contact); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Team Contact Information -->
                <?php if ($show_team_contacts) : ?>
                    <section class="team-contacts-section" id="team-contacts">
                        <div class="team-contacts-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Meet Our Team', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Connect directly with our recruitment specialists', 'recruitpro'); ?></p>
                            </div>

                            <?php
                            // Query team members
                            $team_args = array(
                                'post_type' => 'team_member',
                                'posts_per_page' => 6,
                                'orderby' => 'menu_order',
                                'order' => 'ASC',
                                'meta_query' => array(
                                    array(
                                        'key' => '_show_on_contact_page',
                                        'value' => 'yes',
                                        'compare' => '='
                                    )
                                )
                            );

                            $team_query = new WP_Query($team_args);

                            if ($team_query->have_posts()) :
                            ?>
                                <div class="team-contacts-grid">
                                    <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
                                        <?php
                                        $position = get_post_meta(get_the_ID(), '_team_position', true);
                                        $email = get_post_meta(get_the_ID(), '_team_email', true);
                                        $phone = get_post_meta(get_the_ID(), '_team_phone', true);
                                        $linkedin = get_post_meta(get_the_ID(), '_team_linkedin', true);
                                        $specializations = get_post_meta(get_the_ID(), '_team_specializations', true);
                                        ?>
                                        <div class="team-contact-item">
                                            <div class="team-member-card">
                                                
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <div class="member-photo">
                                                        <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="member-info">
                                                    <h3 class="member-name"><?php the_title(); ?></h3>
                                                    <?php if ($position) : ?>
                                                        <div class="member-position"><?php echo esc_html($position); ?></div>
                                                    <?php endif; ?>

                                                    <?php if ($specializations) : ?>
                                                        <div class="member-specializations">
                                                            <strong><?php esc_html_e('Specializes in:', 'recruitpro'); ?></strong>
                                                            <span><?php echo esc_html($specializations); ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="member-contacts">
                                                        
                                                        <?php if ($email) : ?>
                                                            <a href="mailto:<?php echo esc_attr($email); ?>" 
                                                               class="contact-link"
                                                               title="<?php esc_attr_e('Send Email', 'recruitpro'); ?>">
                                                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                                                <span class="sr-only"><?php esc_html_e('Send Email', 'recruitpro'); ?></span>
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($phone) : ?>
                                                            <a href="tel:<?php echo esc_attr(preg_replace('/[^+\d]/', '', $phone)); ?>" 
                                                               class="contact-link"
                                                               title="<?php esc_attr_e('Call Phone', 'recruitpro'); ?>">
                                                                <i class="fas fa-phone" aria-hidden="true"></i>
                                                                <span class="sr-only"><?php esc_html_e('Call Phone', 'recruitpro'); ?></span>
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($linkedin) : ?>
                                                            <a href="<?php echo esc_url($linkedin); ?>" 
                                                               class="contact-link"
                                                               target="_blank" 
                                                               rel="noopener noreferrer"
                                                               title="<?php esc_attr_e('Connect on LinkedIn', 'recruitpro'); ?>">
                                                                <i class="fab fa-linkedin" aria-hidden="true"></i>
                                                                <span class="sr-only"><?php esc_html_e('Connect on LinkedIn', 'recruitpro'); ?></span>
                                                            </a>
                                                        <?php endif; ?>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php 
                            else :
                            ?>
                                <!-- Default team contacts -->
                                <div class="team-contacts-grid">
                                    <div class="team-contact-item">
                                        <div class="team-member-card">
                                            <div class="member-info">
                                                <h3 class="member-name"><?php esc_html_e('Sarah Johnson', 'recruitpro'); ?></h3>
                                                <div class="member-position"><?php esc_html_e('Senior Recruitment Consultant', 'recruitpro'); ?></div>
                                                <div class="member-specializations">
                                                    <strong><?php esc_html_e('Specializes in:', 'recruitpro'); ?></strong>
                                                    <span><?php esc_html_e('Technology & IT roles', 'recruitpro'); ?></span>
                                                </div>
                                                <div class="member-contacts">
                                                    <a href="mailto:sarah@example.com" class="contact-link">
                                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="tel:+1234567890" class="contact-link">
                                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="team-contact-item">
                                        <div class="team-member-card">
                                            <div class="member-info">
                                                <h3 class="member-name"><?php esc_html_e('Michael Chen', 'recruitpro'); ?></h3>
                                                <div class="member-position"><?php esc_html_e('Finance Recruitment Specialist', 'recruitpro'); ?></div>
                                                <div class="member-specializations">
                                                    <strong><?php esc_html_e('Specializes in:', 'recruitpro'); ?></strong>
                                                    <span><?php esc_html_e('Banking & Finance roles', 'recruitpro'); ?></span>
                                                </div>
                                                <div class="member-contacts">
                                                    <a href="mailto:michael@example.com" class="contact-link">
                                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="tel:+1234567891" class="contact-link">
                                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="team-contact-item">
                                        <div class="team-member-card">
                                            <div class="member-info">
                                                <h3 class="member-name"><?php esc_html_e('Emma Wilson', 'recruitpro'); ?></h3>
                                                <div class="member-position"><?php esc_html_e('Director of Client Services', 'recruitpro'); ?></div>
                                                <div class="member-specializations">
                                                    <strong><?php esc_html_e('Specializes in:', 'recruitpro'); ?></strong>
                                                    <span><?php esc_html_e('C-level & Executive roles', 'recruitpro'); ?></span>
                                                </div>
                                                <div class="member-contacts">
                                                    <a href="mailto:emma@example.com" class="contact-link">
                                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="tel:+1234567892" class="contact-link">
                                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (current_user_can('edit_posts')) : ?>
                                    <div class="team-contacts-placeholder">
                                        <p><em><?php esc_html_e('Create team member posts to display your recruitment specialists here.', 'recruitpro'); ?></em></p>
                                    </div>
                                <?php endif; ?>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>

                        </div>
                    </section>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('contact-sidebar')) : ?>
                <aside id="secondary" class="widget-area contact-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('contact-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<!-- Contact Form JavaScript with Security -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const contactForm = document.getElementById('contact-form');
    const messageTextarea = document.getElementById('contact-message');
    const messageCounter = document.getElementById('message-counter');
    const currentCount = messageCounter?.querySelector('.current-count');
    
    // Character counter for message field
    if (messageTextarea && currentCount) {
        messageTextarea.addEventListener('input', function() {
            const count = this.value.length;
            currentCount.textContent = count;
            
            // Visual feedback for character limit
            if (count > 1800) {
                messageCounter.style.color = '#e74c3c';
            } else if (count > 1500) {
                messageCounter.style.color = '#f39c12';
            } else {
                messageCounter.style.color = '#7f8c8d';
            }
        });
    }
    
    // Add security metadata before submission
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // Add user agent and referrer for security analysis
            this.querySelector('input[name="user_agent"]').value = navigator.userAgent;
            this.querySelector('input[name="referrer"]').value = document.referrer;
            
            <?php if ($enable_recaptcha) : ?>
            // Generate reCAPTCHA token
            e.preventDefault();
            const form = this;
            
            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo esc_js($recaptcha_site_key); ?>', {action: 'contact_form'})
                .then(function(token) {
                    document.getElementById('recaptcha_token').value = token;
                    submitContactForm(form);
                });
            });
            <?php else : ?>
            // Submit form normally if no reCAPTCHA
            submitContactForm(this);
            e.preventDefault();
            <?php endif; ?>
        });
    }
    
    function submitContactForm(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const buttonText = submitButton.querySelector('.button-text');
        const buttonLoading = submitButton.querySelector('.button-loading');
        const messageDiv = document.getElementById('contact-form-message');
        
        // Show loading state
        buttonText.style.display = 'none';
        buttonLoading.style.display = 'inline-block';
        submitButton.disabled = true;
        
        // Submit form
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            buttonText.style.display = 'inline-block';
            buttonLoading.style.display = 'none';
            submitButton.disabled = false;
            
            // Show message
            messageDiv.style.display = 'block';
            messageDiv.className = 'form-message ' + (data.success ? 'success' : 'error');
            messageDiv.textContent = data.data.message;
            
            // Clear form on success
            if (data.success) {
                form.reset();
                if (currentCount) currentCount.textContent = '0';
            }
            
            // Scroll to message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Hide message after 10 seconds
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 10000);
        })
        .catch(error => {
            // Reset button state
            buttonText.style.display = 'inline-block';
            buttonLoading.style.display = 'none';
            submitButton.disabled = false;
            
            // Show error message
            messageDiv.style.display = 'block';
            messageDiv.className = 'form-message error';
            messageDiv.textContent = 'An error occurred. Please try again later.';
            
            console.error('Form submission error:', error);
        });
    }
    
});
</script>

<?php
get_footer();

// AJAX Handler for Contact Form with Security
add_action('wp_ajax_recruitpro_contact_form', 'recruitpro_handle_secure_contact_form');
add_action('wp_ajax_nopriv_recruitpro_contact_form', 'recruitpro_handle_secure_contact_form');

function recruitpro_handle_secure_contact_form() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_contact_nonce')) {
        wp_send_json_error(array('message' => esc_html__('Security check failed.', 'recruitpro')));
    }
    
    // Rate limiting check (won't conflict with security plugin)
    $enable_rate_limiting = get_theme_mod('recruitpro_contact_rate_limit', true);
    if ($enable_rate_limiting) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $rate_limit_key = 'contact_form_' . md5($ip);
        $submissions = get_transient($rate_limit_key) ?: 0;
        
        if ($submissions >= 3) { // Max 3 submissions per hour
            wp_send_json_error(array('message' => esc_html__('Too many submissions. Please try again later.', 'recruitpro')));
        }
        
        set_transient($rate_limit_key, $submissions + 1, HOUR_IN_SECONDS);
    }
    
    // Honeypot check
    $enable_honeypot = get_theme_mod('recruitpro_contact_honeypot', true);
    if ($enable_honeypot && !empty($_POST['website_url'])) {
        wp_send_json_error(array('message' => esc_html__('Spam detected.', 'recruitpro')));
    }
    
    // Time check (prevent too-fast submissions)
    $enable_time_check = get_theme_mod('recruitpro_contact_time_check', true);
    if ($enable_time_check) {
        $form_start_time = intval($_POST['form_start_time']);
        $current_time = time();
        $time_diff = $current_time - $form_start_time;
        
        if ($time_diff < 5) { // Must take at least 5 seconds to fill form
            wp_send_json_error(array('message' => esc_html__('Please take more time to fill out the form.', 'recruitpro')));
        }
    }
    
    // reCAPTCHA verification (if enabled)
    $recaptcha_secret = get_theme_mod('recruitpro_recaptcha_secret_key', '');
    if (!empty($recaptcha_secret) && !empty($_POST['recaptcha_token'])) {
        $recaptcha_response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret' => $recaptcha_secret,
                'response' => $_POST['recaptcha_token'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        ));
        
        if (!is_wp_error($recaptcha_response)) {
            $recaptcha_data = json_decode(wp_remote_retrieve_body($recaptcha_response), true);
            if (!$recaptcha_data['success'] || $recaptcha_data['score'] < 0.5) {
                wp_send_json_error(array('message' => esc_html__('reCAPTCHA verification failed.', 'recruitpro')));
            }
        }
    }
    
    // Sanitize and validate form data
    $contact_data = array(
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
        'phone' => sanitize_text_field($_POST['phone']),
        'company' => sanitize_text_field($_POST['company']),
        'subject' => sanitize_text_field($_POST['subject']),
        'message' => sanitize_textarea_field($_POST['message']),
        'privacy_consent' => !empty($_POST['privacy_consent']),
        'newsletter_consent' => !empty($_POST['newsletter_consent']),
        'page_id' => intval($_POST['page_id']),
        'user_agent' => sanitize_text_field($_POST['user_agent']),
        'referrer' => esc_url_raw($_POST['referrer']),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'submitted_at' => current_time('mysql')
    );
    
    // Validation
    if (empty($contact_data['name']) || empty($contact_data['email']) || empty($contact_data['subject']) || empty($contact_data['message'])) {
        wp_send_json_error(array('message' => esc_html__('Please fill in all required fields.', 'recruitpro')));
    }
    
    if (!is_email($contact_data['email'])) {
        wp_send_json_error(array('message' => esc_html__('Please enter a valid email address.', 'recruitpro')));
    }
    
    if (!$contact_data['privacy_consent']) {
        wp_send_json_error(array('message' => esc_html__('Please accept the privacy policy.', 'recruitpro')));
    }
    
    // Additional content validation
    if (strlen($contact_data['message']) < 10) {
        wp_send_json_error(array('message' => esc_html__('Message must be at least 10 characters long.', 'recruitpro')));
    }
    
    if (strlen($contact_data['message']) > 2000) {
        wp_send_json_error(array('message' => esc_html__('Message is too long. Please limit to 2000 characters.', 'recruitpro')));
    }
    
    // Store submission (for security analysis and CRM integration)
    $submission_id = wp_insert_post(array(
        'post_type' => 'contact_submission',
        'post_status' => 'private',
        'post_title' => sprintf('Contact: %s - %s', $contact_data['name'], $contact_data['subject']),
        'post_content' => $contact_data['message'],
        'meta_input' => $contact_data
    ));
    
    // Send notification email
    $admin_email = get_option('admin_email');
    $subject = sprintf(esc_html__('New Contact Form Message - %s', 'recruitpro'), $contact_data['subject']);
    
    $message = sprintf(
        "New contact form submission:\n\n" .
        "Name: %s\n" .
        "Email: %s\n" .
        "Phone: %s\n" .
        "Company: %s\n" .
        "Subject: %s\n" .
        "Message:\n%s\n\n" .
        "Newsletter consent: %s\n" .
        "Submitted: %s\n" .
        "IP: %s",
        $contact_data['name'],
        $contact_data['email'],
        $contact_data['phone'],
        $contact_data['company'],
        $contact_data['subject'],
        $contact_data['message'],
        $contact_data['newsletter_consent'] ? 'Yes' : 'No',
        $contact_data['submitted_at'],
        $contact_data['ip_address']
    );
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    if (wp_mail($admin_email, $subject, $message, $headers)) {
        wp_send_json_success(array('message' => esc_html__('Thank you for your message! We will get back to you within 24 hours.', 'recruitpro')));
    } else {
        wp_send_json_error(array('message' => esc_html__('There was an error sending your message. Please try calling us directly.', 'recruitpro')));
    }
}

/* =================================================================
   SECURE CONTACT PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL CONTACT PAGE WITH SECURITY FEATURES:

✅ GENERAL INQUIRY FORM ONLY
- Simple, focused contact form
- Professional inquiry handling
- Clean user experience
- Single-purpose design

✅ MULTI-LAYER SECURITY (Non-conflicting)
- Honeypot field (hidden bot trap)
- Time-based validation (prevents rapid submissions)
- Rate limiting (3 submissions per hour per IP)
- reCAPTCHA v3 integration (optional)
- Form data validation and sanitization
- CSRF protection with WordPress nonces

✅ PROFESSIONAL VALIDATION
- Required field validation
- Email format verification
- Message length constraints (10-2000 characters)
- Character counter with visual feedback
- Privacy policy consent requirement

✅ COMPREHENSIVE CONTACT INFO
- Multiple contact methods
- Office locations with maps
- Business hours display
- Team member contacts
- Emergency contact information

✅ SEO & PERFORMANCE OPTIMIZED
- Schema.org Organization markup
- Clean HTML structure
- Fast loading forms
- Mobile-responsive design
- Accessibility compliant (WCAG 2.1)

✅ SECURITY FEATURES WON'T CONFLICT WITH:
- Security plugins (different scope)
- SEO plugins (complementary features)
- Caching plugins (form uses AJAX)
- Firewall plugins (theme-level validation)

✅ INTEGRATION FEATURES
- WordPress customizer settings
- Widget area support
- Team member post types
- Google Maps embedding
- AJAX form submission

SECURITY LAYERS:
1. Honeypot (bot detection)
2. Time validation (human behavior)
3. Rate limiting (spam prevention)
4. reCAPTCHA v3 (optional)
5. Input validation (data security)
6. CSRF protection (WordPress nonces)

PERFECT FOR:
- General business inquiries
- Service information requests
- Partnership opportunities
- Media inquiries
- Professional communication

*/
?>