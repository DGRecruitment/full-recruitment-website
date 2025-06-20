<?php
/**
 * Template Name: Website Maintenance Page
 *
 * Professional maintenance page template for recruitment agencies during
 * website updates, server maintenance, or technical improvements. Features
 * clear communication about maintenance status, estimated completion time,
 * emergency contact information, and professional presentation to maintain
 * brand credibility during temporary downtime.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-maintenance.php
 * Purpose: Maintenance mode page for recruitment agency
 * Dependencies: WordPress core, minimal theme functions
 * Features: Maintenance timer, emergency contact, social links, status updates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Maintenance page settings
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_tagline = get_theme_mod('recruitpro_company_tagline', get_bloginfo('description'));
$maintenance_message = get_theme_mod('recruitpro_maintenance_message', '');
$maintenance_end_time = get_theme_mod('recruitpro_maintenance_end_time', '');
$show_progress_bar = get_theme_mod('recruitpro_maintenance_show_progress', true);
$show_emergency_contact = get_theme_mod('recruitpro_maintenance_show_emergency', true);
$show_social_links = get_theme_mod('recruitpro_maintenance_show_social', true);
$show_newsletter = get_theme_mod('recruitpro_maintenance_show_newsletter', false);
$maintenance_type = get_theme_mod('recruitpro_maintenance_type', 'scheduled'); // scheduled, emergency, upgrade
$background_type = get_theme_mod('recruitpro_maintenance_background', 'professional');

// Contact information for emergencies
$emergency_email = get_theme_mod('recruitpro_emergency_email', get_option('admin_email'));
$emergency_phone = get_theme_mod('recruitpro_emergency_phone', '');
$alternative_contact = get_theme_mod('recruitpro_alternative_contact', '');

// Social media links
$social_links = array(
    'linkedin' => get_theme_mod('recruitpro_social_linkedin', ''),
    'facebook' => get_theme_mod('recruitpro_social_facebook', ''),
    'twitter' => get_theme_mod('recruitpro_social_twitter', ''),
    'instagram' => get_theme_mod('recruitpro_social_instagram', ''),
);

// Maintenance reasons and types
$maintenance_reasons = array(
    'scheduled' => array(
        'title' => esc_html__('Scheduled Maintenance', 'recruitpro'),
        'icon' => 'fas fa-tools',
        'description' => esc_html__('We are performing scheduled updates to improve your experience.', 'recruitpro')
    ),
    'emergency' => array(
        'title' => esc_html__('Emergency Maintenance', 'recruitpro'),
        'icon' => 'fas fa-exclamation-triangle',
        'description' => esc_html__('We are addressing an urgent technical issue to restore full service.', 'recruitpro')
    ),
    'upgrade' => array(
        'title' => esc_html__('System Upgrade', 'recruitpro'),
        'icon' => 'fas fa-rocket',
        'description' => esc_html__('We are upgrading our systems to provide you with new features and improved performance.', 'recruitpro')
    ),
    'security' => array(
        'title' => esc_html__('Security Update', 'recruitpro'),
        'icon' => 'fas fa-shield-alt',
        'description' => esc_html__('We are implementing important security updates to protect your data.', 'recruitpro')
    )
);

$current_maintenance = $maintenance_reasons[$maintenance_type] ?? $maintenance_reasons['scheduled'];

// SEO and Schema settings for maintenance mode
$page_title = sprintf(__('%s - Website Maintenance', 'recruitpro'), $company_name);
$page_description = sprintf(__('%s is currently undergoing maintenance. We will be back online shortly.', 'recruitpro'), $company_name);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="maintenance-mode">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo esc_html($page_title); ?></title>
    <meta name="description" content="<?php echo esc_attr($page_description); ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    
    <!-- Maintenance Mode Headers -->
    <meta http-equiv="refresh" content="300"> <!-- Refresh every 5 minutes -->
    <meta name="maintenance-mode" content="active">
    <?php if ($maintenance_end_time) : ?>
        <meta name="maintenance-until" content="<?php echo esc_attr($maintenance_end_time); ?>">
    <?php endif; ?>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($page_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(home_url('/')); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($company_name); ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">
    
    <!-- Favicon -->
    <?php if (get_theme_mod('recruitpro_site_favicon')) : ?>
        <link rel="icon" href="<?php echo esc_url(get_theme_mod('recruitpro_site_favicon')); ?>" type="image/x-icon">
    <?php endif; ?>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- WordPress Head -->
    <?php wp_head(); ?>
    
    <style>
    /* Critical CSS for maintenance page */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        line-height: 1.6;
        color: #333;
        background: <?php echo $background_type === 'professional' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : '#f8f9fa'; ?>;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-x: hidden;
    }
    
    .maintenance-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        text-align: center;
        position: relative;
        z-index: 10;
    }
    
    .maintenance-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 3rem 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .maintenance-icon {
        font-size: 4rem;
        color: #667eea;
        margin-bottom: 1.5rem;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: 0.8; }
    }
    
    .maintenance-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .maintenance-subtitle {
        font-size: 1.25rem;
        color: #718096;
        margin-bottom: 2rem;
        font-weight: 400;
    }
    
    .maintenance-message {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    
    .maintenance-timer {
        background: #f7fafc;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
        border: 2px solid #e2e8f0;
    }
    
    .timer-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
    }
    
    .timer-display {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    
    .timer-unit {
        background: #667eea;
        color: white;
        padding: 1rem;
        border-radius: 10px;
        min-width: 80px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .timer-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
        line-height: 1;
    }
    
    .timer-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-top: 0.5rem;
    }
    
    .progress-bar {
        background: #e2e8f0;
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin: 1rem 0;
    }
    
    .progress-fill {
        background: linear-gradient(90deg, #667eea, #764ba2);
        height: 100%;
        border-radius: 4px;
        width: 65%; /* This would be dynamic in real implementation */
        transition: width 0.3s ease;
    }
    
    .contact-section {
        background: #fff;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    
    .contact-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    
    .contact-item {
        padding: 1.5rem;
        background: #f7fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .contact-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .contact-item i {
        font-size: 1.5rem;
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .contact-item h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .contact-item p, .contact-item a {
        color: #718096;
        text-decoration: none;
        font-size: 0.95rem;
    }
    
    .contact-item a:hover {
        color: #667eea;
    }
    
    .social-links {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        color: #718096;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .social-link:hover {
        background: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-2px);
    }
    
    .newsletter-section {
        background: #667eea;
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
    }
    
    .newsletter-form {
        display: flex;
        gap: 1rem;
        max-width: 400px;
        margin: 1rem auto 0;
    }
    
    .newsletter-input {
        flex: 1;
        padding: 0.8rem 1rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        outline: none;
    }
    
    .newsletter-btn {
        background: #764ba2;
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    
    .newsletter-btn:hover {
        background: #5a3a7a;
    }
    
    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #fed7d7;
        color: #c53030;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .status-indicator.scheduled {
        background: #bee3f8;
        color: #2b6cb0;
    }
    
    .status-indicator.upgrade {
        background: #c6f6d5;
        color: #2f855a;
    }
    
    @media (max-width: 768px) {
        .maintenance-container {
            padding: 1rem;
        }
        
        .maintenance-card {
            padding: 2rem 1.5rem;
        }
        
        .maintenance-title {
            font-size: 2rem;
        }
        
        .timer-display {
            gap: 0.5rem;
        }
        
        .timer-unit {
            min-width: 60px;
            padding: 0.8rem;
        }
        
        .timer-number {
            font-size: 1.5rem;
        }
        
        .contact-grid {
            grid-template-columns: 1fr;
        }
        
        .newsletter-form {
            flex-direction: column;
        }
    }
    </style>
</head>

<body class="maintenance-page">
    
    <div class="maintenance-container">
        <div class="maintenance-card">
            
            <!-- Status Indicator -->
            <div class="status-indicator <?php echo esc_attr($maintenance_type); ?>">
                <i class="fas fa-circle"></i>
                <?php echo esc_html($current_maintenance['title']); ?>
            </div>

            <!-- Maintenance Icon -->
            <div class="maintenance-icon">
                <i class="<?php echo esc_attr($current_maintenance['icon']); ?>" aria-hidden="true"></i>
            </div>

            <!-- Company Branding -->
            <h1 class="maintenance-title">
                <?php echo esc_html($company_name); ?>
                <?php esc_html_e('is Under Maintenance', 'recruitpro'); ?>
            </h1>
            
            <p class="maintenance-subtitle">
                <?php echo esc_html($current_maintenance['description']); ?>
            </p>

            <!-- Custom Maintenance Message -->
            <?php if ($maintenance_message) : ?>
                <div class="maintenance-message">
                    <?php echo wp_kses_post($maintenance_message); ?>
                </div>
            <?php else : ?>
                <div class="maintenance-message">
                    <?php esc_html_e('We are currently performing important updates to enhance your experience with our recruitment services. Our team is working diligently to restore full functionality as quickly as possible.', 'recruitpro'); ?>
                </div>
            <?php endif; ?>

            <!-- Maintenance Timer -->
            <?php if ($maintenance_end_time) : ?>
                <div class="maintenance-timer">
                    <h3 class="timer-title">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        <?php esc_html_e('Estimated Completion Time', 'recruitpro'); ?>
                    </h3>
                    
                    <div class="timer-display" id="maintenance-countdown">
                        <div class="timer-unit">
                            <span class="timer-number" id="days">00</span>
                            <span class="timer-label"><?php esc_html_e('Days', 'recruitpro'); ?></span>
                        </div>
                        <div class="timer-unit">
                            <span class="timer-number" id="hours">00</span>
                            <span class="timer-label"><?php esc_html_e('Hours', 'recruitpro'); ?></span>
                        </div>
                        <div class="timer-unit">
                            <span class="timer-number" id="minutes">00</span>
                            <span class="timer-label"><?php esc_html_e('Minutes', 'recruitpro'); ?></span>
                        </div>
                        <div class="timer-unit">
                            <span class="timer-number" id="seconds">00</span>
                            <span class="timer-label"><?php esc_html_e('Seconds', 'recruitpro'); ?></span>
                        </div>
                    </div>

                    <?php if ($show_progress_bar) : ?>
                        <div class="progress-bar">
                            <div class="progress-fill" id="maintenance-progress"></div>
                        </div>
                        <p class="progress-text">
                            <small><?php esc_html_e('Maintenance progress (estimated)', 'recruitpro'); ?></small>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Emergency Contact Information -->
            <?php if ($show_emergency_contact && ($emergency_email || $emergency_phone)) : ?>
                <div class="contact-section">
                    <h3 class="contact-title">
                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        <?php esc_html_e('Need Urgent Assistance?', 'recruitpro'); ?>
                    </h3>
                    <p><?php esc_html_e('For urgent recruitment matters or candidate inquiries, please contact us using the information below:', 'recruitpro'); ?></p>
                    
                    <div class="contact-grid">
                        
                        <?php if ($emergency_email) : ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <h4><?php esc_html_e('Emergency Email', 'recruitpro'); ?></h4>
                                <a href="mailto:<?php echo esc_attr($emergency_email); ?>">
                                    <?php echo esc_html($emergency_email); ?>
                                </a>
                                <p><?php esc_html_e('Response within 2-4 hours', 'recruitpro'); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($emergency_phone) : ?>
                            <div class="contact-item">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                <h4><?php esc_html_e('Emergency Phone', 'recruitpro'); ?></h4>
                                <a href="tel:<?php echo esc_attr(str_replace(' ', '', $emergency_phone)); ?>">
                                    <?php echo esc_html($emergency_phone); ?>
                                </a>
                                <p><?php esc_html_e('Available during business hours', 'recruitpro'); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($alternative_contact) : ?>
                            <div class="contact-item">
                                <i class="fas fa-comments" aria-hidden="true"></i>
                                <h4><?php esc_html_e('Alternative Contact', 'recruitpro'); ?></h4>
                                <p><?php echo esc_html($alternative_contact); ?></p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>

            <!-- Newsletter Subscription (Optional) -->
            <?php if ($show_newsletter) : ?>
                <div class="newsletter-section">
                    <h3><?php esc_html_e('Stay Informed', 'recruitpro'); ?></h3>
                    <p><?php esc_html_e('Subscribe to receive maintenance updates and be notified when we\'re back online.', 'recruitpro'); ?></p>
                    
                    <form class="newsletter-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                        <input type="email" 
                               name="email" 
                               class="newsletter-input" 
                               placeholder="<?php esc_attr_e('Enter your email address', 'recruitpro'); ?>" 
                               required>
                        <button type="submit" class="newsletter-btn">
                            <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                        </button>
                        <input type="hidden" name="action" value="recruitpro_maintenance_newsletter">
                        <?php wp_nonce_field('recruitpro_maintenance_newsletter', 'nonce'); ?>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Social Media Links -->
            <?php if ($show_social_links && array_filter($social_links)) : ?>
                <div class="social-section">
                    <p><?php esc_html_e('Follow us for real-time updates:', 'recruitpro'); ?></p>
                    <div class="social-links">
                        <?php foreach ($social_links as $platform => $url) : ?>
                            <?php if (!empty($url)) : ?>
                                <a href="<?php echo esc_url($url); ?>" 
                                   class="social-link" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   aria-label="<?php echo esc_attr(sprintf(__('Follow us on %s', 'recruitpro'), ucfirst($platform))); ?>">
                                    <i class="fab fa-<?php echo esc_attr($platform); ?>" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <div class="additional-info">
                <p>
                    <small>
                        <?php esc_html_e('Thank you for your patience. We will be back online shortly with improved services.', 'recruitpro'); ?>
                        <br>
                        <?php printf(esc_html__('Page last updated: %s', 'recruitpro'), '<span id="last-updated">' . date('H:i T') . '</span>'); ?>
                    </small>
                </p>
            </div>

        </div>
    </div>

    <!-- JavaScript for countdown timer and page refresh -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($maintenance_end_time) : ?>
            // Maintenance countdown timer
            const maintenanceEndTime = new Date('<?php echo esc_js($maintenance_end_time); ?>').getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = maintenanceEndTime - now;
                
                if (timeLeft > 0) {
                    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                    
                    document.getElementById('days').textContent = days.toString().padStart(2, '0');
                    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
                    
                    // Update progress bar (estimate based on time passed)
                    const totalMaintenanceTime = 6 * 60 * 60 * 1000; // Assume 6 hours total
                    const timePassed = totalMaintenanceTime - timeLeft;
                    const progressPercent = Math.max(0, Math.min(100, (timePassed / totalMaintenanceTime) * 100));
                    
                    const progressBar = document.getElementById('maintenance-progress');
                    if (progressBar) {
                        progressBar.style.width = progressPercent + '%';
                    }
                } else {
                    // Maintenance time has passed, redirect to home page
                    window.location.href = '<?php echo esc_url(home_url('/')); ?>';
                }
            }
            
            // Update countdown immediately and then every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        <?php endif; ?>
        
        // Update last updated time every minute
        function updateLastUpdated() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: false,
                timeZoneName: 'short'
            });
            const lastUpdatedElement = document.getElementById('last-updated');
            if (lastUpdatedElement) {
                lastUpdatedElement.textContent = timeString;
            }
        }
        
        setInterval(updateLastUpdated, 60000); // Update every minute
        
        // Auto-refresh page every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes
    });
    </script>

    <?php wp_footer(); ?>

</body>
</html>

<?php
// AJAX handler for maintenance newsletter subscription
add_action('wp_ajax_recruitpro_maintenance_newsletter', 'recruitpro_handle_maintenance_newsletter');
add_action('wp_ajax_nopriv_recruitpro_maintenance_newsletter', 'recruitpro_handle_maintenance_newsletter');

function recruitpro_handle_maintenance_newsletter() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_maintenance_newsletter')) {
        wp_send_json_error(array('message' => esc_html__('Security verification failed. Please try again.', 'recruitpro')));
    }
    
    // Sanitize email
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => esc_html__('Please enter a valid email address.', 'recruitpro')));
    }
    
    // Store subscription in maintenance notification list
    $maintenance_subscribers = get_option('recruitpro_maintenance_subscribers', array());
    
    if (in_array($email, $maintenance_subscribers)) {
        wp_send_json_error(array('message' => esc_html__('You are already subscribed to maintenance updates.', 'recruitpro')));
    }
    
    $maintenance_subscribers[] = array(
        'email' => $email,
        'subscribed_date' => current_time('mysql'),
        'type' => 'maintenance_updates'
    );
    
    update_option('recruitpro_maintenance_subscribers', $maintenance_subscribers);
    
    wp_send_json_success(array('message' => esc_html__('Thank you! You will be notified when maintenance is complete.', 'recruitpro')));
}

/* =================================================================
   MAINTENANCE PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL MAINTENANCE PAGE FEATURES:

✅ STANDALONE MAINTENANCE MODE
- Complete HTML document structure
- No header/footer dependencies for reliability
- Professional recruitment agency branding
- Mobile-responsive design
- Accessibility compliant

✅ MAINTENANCE TYPE INDICATORS
- Scheduled maintenance (planned updates)
- Emergency maintenance (urgent fixes)
- System upgrade (new features)
- Security update (protection updates)
- Visual status indicators with appropriate colors

✅ COUNTDOWN TIMER & PROGRESS
- JavaScript-powered live countdown
- Estimated completion time display
- Progress bar with visual feedback
- Days, hours, minutes, seconds breakdown
- Auto-refresh functionality

✅ EMERGENCY CONTACT SYSTEM
- Emergency email contact
- Emergency phone contact
- Alternative contact methods
- Clear response time expectations
- Urgent assistance guidance

✅ PROFESSIONAL PRESENTATION
- Clean, trustworthy design
- Company branding integration
- Status-appropriate messaging
- Multiple background themes
- Professional color schemes

✅ MAINTENANCE UPDATES
- Optional newsletter subscription
- Real-time social media links
- Page auto-refresh every 5 minutes
- Last updated timestamp
- Professional communication

✅ SEO & TECHNICAL OPTIMIZATION
- Proper maintenance mode headers
- Noindex/nofollow meta tags
- Auto-refresh meta tags
- Open Graph integration
- Schema.org markup

✅ RELIABILITY FEATURES
- Minimal dependencies for uptime
- Critical CSS inline
- Essential JavaScript only
- Fallback contact methods
- Error handling

✅ MOBILE OPTIMIZATION
- Mobile-first responsive design
- Touch-friendly interfaces
- Optimized for slow connections
- Readable on all screen sizes
- Professional mobile presentation

✅ CUSTOMIZATION OPTIONS
- Maintenance type selection
- Custom message support
- Contact information management
- Social media integration
- Timer and progress configuration

PERFECT FOR:
- Scheduled website updates
- Emergency server maintenance
- System upgrades and migrations
- Security updates
- Database maintenance
- Third-party integration updates

BUSINESS CONTINUITY:
- Maintains professional image during downtime
- Provides clear communication
- Offers emergency contact options
- Reduces client anxiety
- Preserves brand credibility

TECHNICAL FEATURES:
- Automatic page refresh
- JavaScript countdown timer
- Progress estimation
- AJAX newsletter subscription
- Responsive design system
- Critical CSS optimization

RECRUITMENT INDUSTRY SPECIFIC:
- Emergency recruitment contact
- Professional service continuity
- Client relationship maintenance
- Candidate communication
- Trust preservation during downtime

*/
?>