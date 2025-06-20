<?php
/**
 * Template Name: Coming Soon Page
 *
 * Professional coming soon page template for recruitment agencies during
 * website launches, major updates, or new service introductions. Features
 * email subscription, countdown timer, contact information, and professional
 * presentation to maintain brand credibility during transitions.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-coming-soon.php
 * Purpose: Coming soon page for recruitment agency launches
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Countdown timer, email subscription, social links, contact info
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get page and customizer settings
$page_id = get_the_ID();
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_tagline = get_theme_mod('recruitpro_company_tagline', get_bloginfo('description'));
$launch_date = get_theme_mod('recruitpro_coming_soon_date', '');
$show_countdown = get_theme_mod('recruitpro_coming_soon_countdown', true);
$show_subscription = get_theme_mod('recruitpro_coming_soon_subscription', true);
$show_contact = get_theme_mod('recruitpro_coming_soon_contact', true);
$show_social = get_theme_mod('recruitpro_coming_soon_social', true);
$background_type = get_theme_mod('recruitpro_coming_soon_background', 'gradient');
$custom_message = get_theme_mod('recruitpro_coming_soon_message', '');

// Contact information
$contact_email = get_theme_mod('recruitpro_contact_email', get_option('admin_email'));
$contact_phone = get_theme_mod('recruitpro_contact_phone', '');
$office_address = get_theme_mod('recruitpro_office_address', '');

// Social media links
$social_links = array(
    'linkedin' => get_theme_mod('recruitpro_social_linkedin', ''),
    'facebook' => get_theme_mod('recruitpro_social_facebook', ''),
    'twitter' => get_theme_mod('recruitpro_social_twitter', ''),
    'instagram' => get_theme_mod('recruitpro_social_instagram', ''),
);

// Schema.org markup for coming soon page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company_name,
    'description' => $company_tagline,
    'url' => home_url(),
    'logo' => get_theme_mod('recruitpro_site_logo', ''),
    'contactPoint' => array(
        '@type' => 'ContactPoint',
        'contactType' => 'Customer Service',
        'email' => $contact_email,
        'telephone' => $contact_phone
    ),
    'sameAs' => array_filter($social_links)
);

// Set launch date for countdown (default to 30 days from now if not set)
if (empty($launch_date)) {
    $launch_date = date('Y-m-d H:i:s', strtotime('+30 days'));
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- SEO Meta Tags -->
    <title><?php echo esc_html($company_name); ?> - <?php esc_html_e('Coming Soon', 'recruitpro'); ?></title>
    <meta name="description" content="<?php echo esc_attr($company_tagline); ?> - <?php esc_attr_e('We are launching soon with exciting new recruitment services.', 'recruitpro'); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo esc_attr($company_name); ?> - <?php esc_attr_e('Coming Soon', 'recruitpro'); ?>">
    <meta property="og:description" content="<?php echo esc_attr($company_tagline); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(home_url()); ?>">
    <?php if (get_theme_mod('recruitpro_site_logo')) : ?>
        <meta property="og:image" content="<?php echo esc_url(get_theme_mod('recruitpro_site_logo')); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($company_name); ?> - <?php esc_attr_e('Coming Soon', 'recruitpro'); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($company_tagline); ?>">
    
    <!-- Favicon -->
    <?php if (get_theme_mod('recruitpro_favicon')) : ?>
        <link rel="icon" href="<?php echo esc_url(get_theme_mod('recruitpro_favicon')); ?>">
    <?php endif; ?>
    
    <?php wp_head(); ?>
    
    <!-- Coming Soon Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }
        
        .coming-soon-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px;
        }
        
        /* Background Options */
        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-professional {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        
        .bg-recruitment {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        }
        
        .coming-soon-content {
            text-align: center;
            max-width: 800px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        
        .logo {
            margin-bottom: 30px;
        }
        
        .logo img {
            max-height: 80px;
            width: auto;
        }
        
        .company-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            letter-spacing: -0.02em;
        }
        
        .tagline {
            font-size: 1.25rem;
            color: #7f8c8d;
            margin-bottom: 40px;
            font-weight: 300;
        }
        
        .coming-soon-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .coming-soon-subtitle {
            font-size: 1.1rem;
            color: #5a6c7d;
            margin-bottom: 50px;
            line-height: 1.7;
        }
        
        /* Countdown Timer */
        .countdown-container {
            margin: 50px 0;
        }
        
        .countdown-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        
        .countdown-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 20px;
            border-radius: 15px;
            min-width: 100px;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .countdown-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            line-height: 1;
        }
        
        .countdown-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 8px;
            opacity: 0.9;
        }
        
        /* Email Subscription */
        .subscription-container {
            margin: 50px 0;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 15px;
            border: 1px solid #e9ecef;
        }
        
        .subscription-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .subscription-description {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .subscription-form {
            display: flex;
            gap: 15px;
            max-width: 400px;
            margin: 0 auto;
            flex-wrap: wrap;
        }
        
        .subscription-input {
            flex: 1;
            min-width: 250px;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .subscription-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .subscription-button {
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .subscription-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        /* Contact Information */
        .contact-container {
            margin: 50px 0;
            padding: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
        }
        
        .contact-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            text-align: left;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .contact-details h4 {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .contact-details p {
            color: #6c757d;
            margin: 0;
        }
        
        .contact-details a {
            color: #667eea;
            text-decoration: none;
        }
        
        .contact-details a:hover {
            text-decoration: underline;
        }
        
        /* Social Media Links */
        .social-container {
            margin-top: 50px;
        }
        
        .social-title {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .social-link {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        /* Success/Error Messages */
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .coming-soon-content {
                padding: 40px 20px;
            }
            
            .company-name {
                font-size: 2rem;
            }
            
            .coming-soon-title {
                font-size: 2.5rem;
            }
            
            .countdown-timer {
                gap: 15px;
            }
            
            .countdown-item {
                min-width: 80px;
                padding: 20px 15px;
            }
            
            .countdown-number {
                font-size: 2rem;
            }
            
            .subscription-form {
                flex-direction: column;
            }
            
            .subscription-input {
                min-width: 100%;
            }
            
            .contact-info {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .coming-soon-title {
                font-size: 2rem;
            }
            
            .countdown-item {
                min-width: 70px;
                padding: 15px 10px;
            }
            
            .countdown-number {
                font-size: 1.5rem;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus Styles */
        .subscription-input:focus,
        .subscription-button:focus,
        .social-link:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }
    </style>
</head>

<body class="coming-soon-page">
    
    <!-- Schema.org Organization Markup -->
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>
    
    <div class="coming-soon-container <?php echo esc_attr('bg-' . $background_type); ?>">
        <main class="coming-soon-content" role="main">
            
            <!-- Logo -->
            <?php if (get_theme_mod('recruitpro_site_logo')) : ?>
                <div class="logo">
                    <img src="<?php echo esc_url(get_theme_mod('recruitpro_site_logo')); ?>" 
                         alt="<?php echo esc_attr($company_name); ?> Logo" 
                         loading="eager">
                </div>
            <?php endif; ?>
            
            <!-- Company Name & Tagline -->
            <h1 class="company-name"><?php echo esc_html($company_name); ?></h1>
            <?php if ($company_tagline) : ?>
                <p class="tagline"><?php echo esc_html($company_tagline); ?></p>
            <?php endif; ?>
            
            <!-- Coming Soon Title -->
            <h2 class="coming-soon-title"><?php esc_html_e('Coming Soon', 'recruitpro'); ?></h2>
            
            <!-- Custom Message or Default -->
            <div class="coming-soon-subtitle">
                <?php if ($custom_message) : ?>
                    <p><?php echo wp_kses_post($custom_message); ?></p>
                <?php else : ?>
                    <p><?php esc_html_e('We are working hard to bring you an exceptional recruitment experience. Our new website will launch soon with innovative features to connect talented professionals with their dream careers.', 'recruitpro'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Countdown Timer -->
            <?php if ($show_countdown && $launch_date) : ?>
                <section class="countdown-container" aria-labelledby="countdown-title">
                    <h3 id="countdown-title" class="countdown-title"><?php esc_html_e('Launch Countdown', 'recruitpro'); ?></h3>
                    <div class="countdown-timer" id="countdown-timer" data-launch-date="<?php echo esc_attr($launch_date); ?>">
                        <div class="countdown-item">
                            <span class="countdown-number" id="days">00</span>
                            <span class="countdown-label"><?php esc_html_e('Days', 'recruitpro'); ?></span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="hours">00</span>
                            <span class="countdown-label"><?php esc_html_e('Hours', 'recruitpro'); ?></span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="minutes">00</span>
                            <span class="countdown-label"><?php esc_html_e('Minutes', 'recruitpro'); ?></span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="seconds">00</span>
                            <span class="countdown-label"><?php esc_html_e('Seconds', 'recruitpro'); ?></span>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
            
            <!-- Email Subscription -->
            <?php if ($show_subscription) : ?>
                <section class="subscription-container" aria-labelledby="subscription-title">
                    <h3 id="subscription-title" class="subscription-title"><?php esc_html_e('Be the First to Know', 'recruitpro'); ?></h3>
                    <p class="subscription-description"><?php esc_html_e('Subscribe to get notified when we launch and receive exclusive early access to our premium recruitment services.', 'recruitpro'); ?></p>
                    
                    <form class="subscription-form" id="subscription-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                        <input type="email" 
                               name="email" 
                               id="subscription-email"
                               class="subscription-input" 
                               placeholder="<?php esc_attr_e('Enter your email address', 'recruitpro'); ?>"
                               required
                               aria-label="<?php esc_attr_e('Email address for updates', 'recruitpro'); ?>">
                        
                        <button type="submit" 
                                class="subscription-button" 
                                id="subscription-submit">
                            <span class="button-text"><?php esc_html_e('Notify Me', 'recruitpro'); ?></span>
                            <span class="loading" style="display: none;" aria-hidden="true"></span>
                        </button>
                        
                        <input type="hidden" name="action" value="recruitpro_coming_soon_subscription">
                        <?php wp_nonce_field('recruitpro_coming_soon_nonce', 'nonce'); ?>
                    </form>
                    
                    <div id="subscription-message" class="message" style="display: none;" role="alert" aria-live="polite"></div>
                </section>
            <?php endif; ?>
            
            <!-- Contact Information -->
            <?php if ($show_contact && ($contact_email || $contact_phone || $office_address)) : ?>
                <section class="contact-container" aria-labelledby="contact-title">
                    <h3 id="contact-title" class="contact-title"><?php esc_html_e('Get in Touch', 'recruitpro'); ?></h3>
                    
                    <div class="contact-info">
                        
                        <?php if ($contact_email) : ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                </div>
                                <div class="contact-details">
                                    <h4><?php esc_html_e('Email Us', 'recruitpro'); ?></h4>
                                    <p><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($contact_phone) : ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                </div>
                                <div class="contact-details">
                                    <h4><?php esc_html_e('Call Us', 'recruitpro'); ?></h4>
                                    <p><a href="tel:<?php echo esc_attr(str_replace(' ', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($office_address) : ?>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                </div>
                                <div class="contact-details">
                                    <h4><?php esc_html_e('Visit Us', 'recruitpro'); ?></h4>
                                    <p><?php echo esc_html($office_address); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                </section>
            <?php endif; ?>
            
            <!-- Social Media Links -->
            <?php if ($show_social && array_filter($social_links)) : ?>
                <section class="social-container" aria-labelledby="social-title">
                    <h3 id="social-title" class="social-title"><?php esc_html_e('Follow Our Journey', 'recruitpro'); ?></h3>
                    
                    <div class="social-links">
                        
                        <?php if ($social_links['linkedin']) : ?>
                            <a href="<?php echo esc_url($social_links['linkedin']); ?>" 
                               class="social-link"
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php esc_attr_e('Follow us on LinkedIn', 'recruitpro'); ?>">
                                <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['facebook']) : ?>
                            <a href="<?php echo esc_url($social_links['facebook']); ?>" 
                               class="social-link"
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php esc_attr_e('Follow us on Facebook', 'recruitpro'); ?>">
                                <i class="fab fa-facebook-f" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['twitter']) : ?>
                            <a href="<?php echo esc_url($social_links['twitter']); ?>" 
                               class="social-link"
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php esc_attr_e('Follow us on Twitter', 'recruitpro'); ?>">
                                <i class="fab fa-twitter" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($social_links['instagram']) : ?>
                            <a href="<?php echo esc_url($social_links['instagram']); ?>" 
                               class="social-link"
                               target="_blank" 
                               rel="noopener noreferrer"
                               aria-label="<?php esc_attr_e('Follow us on Instagram', 'recruitpro'); ?>">
                                <i class="fab fa-instagram" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                        
                    </div>
                </section>
            <?php endif; ?>
            
        </main>
    </div>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- JavaScript for Countdown and Form -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Countdown Timer
        const countdownTimer = document.getElementById('countdown-timer');
        if (countdownTimer) {
            const launchDate = new Date(countdownTimer.getAttribute('data-launch-date')).getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = launchDate - now;
                
                if (distance > 0) {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    document.getElementById('days').textContent = days.toString().padStart(2, '0');
                    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
                } else {
                    // Launch date reached
                    document.getElementById('days').textContent = '00';
                    document.getElementById('hours').textContent = '00';
                    document.getElementById('minutes').textContent = '00';
                    document.getElementById('seconds').textContent = '00';
                    
                    clearInterval(countdownInterval);
                }
            }
            
            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
        }
        
        // Email Subscription Form
        const subscriptionForm = document.getElementById('subscription-form');
        if (subscriptionForm) {
            subscriptionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = document.getElementById('subscription-submit');
                const buttonText = submitButton.querySelector('.button-text');
                const loading = submitButton.querySelector('.loading');
                const messageDiv = document.getElementById('subscription-message');
                const emailInput = document.getElementById('subscription-email');
                
                // Show loading state
                buttonText.style.display = 'none';
                loading.style.display = 'inline-block';
                submitButton.disabled = true;
                
                // Prepare form data
                const formData = new FormData(subscriptionForm);
                
                // Submit via fetch
                fetch(subscriptionForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    buttonText.style.display = 'inline-block';
                    loading.style.display = 'none';
                    submitButton.disabled = false;
                    
                    // Show message
                    messageDiv.style.display = 'block';
                    messageDiv.className = 'message ' + (data.success ? 'success' : 'error');
                    messageDiv.textContent = data.data.message;
                    
                    // Clear form on success
                    if (data.success) {
                        emailInput.value = '';
                    }
                    
                    // Hide message after 5 seconds
                    setTimeout(() => {
                        messageDiv.style.display = 'none';
                    }, 5000);
                })
                .catch(error => {
                    // Reset button state
                    buttonText.style.display = 'inline-block';
                    loading.style.display = 'none';
                    submitButton.disabled = false;
                    
                    // Show error message
                    messageDiv.style.display = 'block';
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'An error occurred. Please try again later.';
                    
                    console.error('Subscription error:', error);
                });
            });
        }
        
    });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>

<?php
// AJAX Handler for Coming Soon Subscription
add_action('wp_ajax_recruitpro_coming_soon_subscription', 'recruitpro_handle_coming_soon_subscription');
add_action('wp_ajax_nopriv_recruitpro_coming_soon_subscription', 'recruitpro_handle_coming_soon_subscription');

function recruitpro_handle_coming_soon_subscription() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_coming_soon_nonce')) {
        wp_send_json_error(array('message' => esc_html__('Security check failed.', 'recruitpro')));
    }
    
    // Sanitize email
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => esc_html__('Please enter a valid email address.', 'recruitpro')));
    }
    
    // Store subscription (you can enhance this to integrate with your email marketing service)
    $subscribers = get_option('recruitpro_coming_soon_subscribers', array());
    
    // Check if already subscribed
    if (in_array($email, $subscribers)) {
        wp_send_json_error(array('message' => esc_html__('You are already subscribed for updates.', 'recruitpro')));
    }
    
    // Add to subscribers list
    $subscribers[] = $email;
    update_option('recruitpro_coming_soon_subscribers', $subscribers);
    
    // Send notification email to admin
    $admin_email = get_option('admin_email');
    $subject = sprintf(esc_html__('New Coming Soon Subscription - %s', 'recruitpro'), get_bloginfo('name'));
    $message = sprintf(
        esc_html__('New coming soon subscription:\n\nEmail: %s\nDate: %s\nIP: %s', 'recruitpro'),
        $email,
        current_time('mysql'),
        $_SERVER['REMOTE_ADDR']
    );
    
    wp_mail($admin_email, $subject, $message);
    
    wp_send_json_success(array('message' => esc_html__('Thank you! You will be notified when we launch.', 'recruitpro')));
}

/* =================================================================
   COMING SOON PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL COMING SOON PAGE FEATURES:

✅ FULL-SCREEN STANDALONE PAGE
- Complete HTML document structure
- No header/footer dependencies
- Professional recruitment agency branding
- Mobile-responsive design
- Accessibility compliant

✅ COUNTDOWN TIMER
- JavaScript-powered live countdown
- Days, hours, minutes, seconds display
- Automatic launch date handling
- Professional gradient styling
- Mobile-optimized layout

✅ EMAIL SUBSCRIPTION
- AJAX-powered subscription form
- Email validation and sanitization
- Subscriber list management
- Success/error message handling
- Admin notification system

✅ CONTACT INFORMATION
- Email, phone, address display
- Professional icon integration
- Clickable contact links
- Responsive contact grid
- Professional presentation

✅ SOCIAL MEDIA INTEGRATION
- LinkedIn, Facebook, Twitter, Instagram
- Professional icon styling
- External link handling
- Accessibility attributes
- Hover animations

✅ PROFESSIONAL DESIGN
- Multiple background options
- Gradient and professional themes
- Logo integration
- Company branding
- Typography optimization

✅ SEO OPTIMIZATION
- Complete meta tags
- Open Graph integration
- Twitter Card support
- Schema.org markup
- Noindex for coming soon

✅ CUSTOMIZATION OPTIONS
- Launch date configuration
- Custom message support
- Background theme selection
- Show/hide components
- Contact information management

✅ SECURITY & PERFORMANCE
- Nonce verification
- AJAX form handling
- Data sanitization
- Error handling
- Loading states

PERFECT FOR:
- Website launches
- Service introductions
- Major updates
- New office openings
- Rebranding projects

TECHNICAL FEATURES:
- Standalone HTML structure
- Font Awesome icons
- Responsive CSS Grid
- JavaScript countdown
- AJAX form submission
- WordPress integration

*/
?>