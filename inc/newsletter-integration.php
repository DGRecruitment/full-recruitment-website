<?php
/**
 * RecruitPro Theme Newsletter Integration
 *
 * General newsletter subscription system for blog posts and company news.
 * This handles ONLY newsletter subscriptions for general content updates,
 * NOT job alerts (which are handled separately by the jobs/CRM system).
 *
 * @package RecruitPro
 * @subpackage Theme/Newsletter
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/newsletter-integration.php
 * Purpose: General newsletter subscription for blog posts and company news
 * Dependencies: RecruitPro CRM Plugin (for email marketing functionality)
 * Note: Job alerts are handled separately by the jobs plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Newsletter Integration Class
 * 
 * Handles general newsletter subscriptions for blog posts and company updates.
 * Job alerts are handled separately by the jobs plugin.
 *
 * @since 1.0.0
 */
class RecruitPro_Newsletter_Integration {

    /**
     * Newsletter settings from theme customizer
     * 
     * @since 1.0.0
     * @var array
     */
    private $newsletter_settings = array();

    /**
     * CRM integration status
     * 
     * @since 1.0.0
     * @var bool
     */
    private $crm_available = false;

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->check_crm_availability();
        $this->init_hooks();
    }

    /**
     * Initialize newsletter settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->newsletter_settings = array(
            'enabled' => get_theme_mod('recruitpro_newsletter_enabled', true),
            'double_optin' => get_theme_mod('recruitpro_newsletter_double_optin', true),
            'show_name_fields' => get_theme_mod('recruitpro_newsletter_show_names', true),
            'show_company_field' => get_theme_mod('recruitpro_newsletter_show_company', true),
            'show_interests' => get_theme_mod('recruitpro_newsletter_show_interests', true),
            'privacy_required' => get_theme_mod('recruitpro_newsletter_privacy_required', true),
            'honeypot_protection' => get_theme_mod('recruitpro_newsletter_honeypot', true),
            'rate_limiting' => get_theme_mod('recruitpro_newsletter_rate_limiting', true),
            'success_message' => get_theme_mod('recruitpro_newsletter_success_message', __('Thank you for subscribing to our newsletter!', 'recruitpro')),
            'redirect_after_signup' => get_theme_mod('recruitpro_newsletter_redirect_page', ''),
            'auto_send_new_posts' => get_theme_mod('recruitpro_newsletter_auto_send_posts', true),
            'auto_send_new_pages' => get_theme_mod('recruitpro_newsletter_auto_send_pages', false),
        );
    }

    /**
     * Check if CRM plugin is available and active
     * 
     * @since 1.0.0
     * @return void
     */
    private function check_crm_availability() {
        $this->crm_available = (
            function_exists('recruitpro_crm_add_newsletter_subscriber') &&
            function_exists('recruitpro_crm_email_marketing_enabled') &&
            recruitpro_crm_email_marketing_enabled()
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // AJAX handlers for newsletter subscription
        add_action('wp_ajax_recruitpro_newsletter_subscribe', array($this, 'handle_newsletter_subscription'));
        add_action('wp_ajax_nopriv_recruitpro_newsletter_subscribe', array($this, 'handle_newsletter_subscription'));
        
        // Shortcode for newsletter form
        add_shortcode('recruitpro_newsletter_form', array($this, 'render_newsletter_form'));
        
        // Enqueue scripts for newsletter functionality
        add_action('wp_enqueue_scripts', array($this, 'enqueue_newsletter_scripts'));
        
        // Add newsletter form to customizer
        add_action('customize_register', array($this, 'customize_register_newsletter'));
        
        // Widget for newsletter subscription
        add_action('widgets_init', array($this, 'register_newsletter_widget'));
        
        // Auto-send newsletter triggers
        add_action('publish_post', array($this, 'trigger_newsletter_for_new_post'), 10, 2);
        add_action('publish_page', array($this, 'trigger_newsletter_for_new_page'), 10, 2);
        
        // Admin notification hooks
        add_action('recruitpro_newsletter_new_subscriber', array($this, 'notify_admin_new_subscriber'), 10, 1);
    }

    /**
     * Handle newsletter subscription AJAX request
     * 
     * @since 1.0.0
     * @return void
     */
    public function handle_newsletter_subscription() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['newsletter_nonce'], 'recruitpro_newsletter_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security verification failed. Please try again.', 'recruitpro')
            ));
        }

        // Check if newsletter is enabled
        if (!$this->newsletter_settings['enabled']) {
            wp_send_json_error(array(
                'message' => __('Newsletter subscription is currently disabled.', 'recruitpro')
            ));
        }

        // Check if CRM is available
        if (!$this->crm_available) {
            wp_send_json_error(array(
                'message' => __('Email marketing system is not available. Please contact administrator.', 'recruitpro')
            ));
        }

        // Rate limiting check
        if ($this->newsletter_settings['rate_limiting'] && $this->is_rate_limited()) {
            wp_send_json_error(array(
                'message' => __('Too many subscription attempts. Please try again later.', 'recruitpro')
            ));
        }

        // Honeypot protection
        if ($this->newsletter_settings['honeypot_protection'] && !empty($_POST['newsletter_website'])) {
            wp_send_json_error(array(
                'message' => __('Spam detected. Please try again.', 'recruitpro')
            ));
        }

        // Sanitize and validate input data
        $email = sanitize_email($_POST['newsletter_email']);
        $first_name = sanitize_text_field($_POST['newsletter_first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['newsletter_last_name'] ?? '');
        $company = sanitize_text_field($_POST['newsletter_company'] ?? '');
        $subscriber_type = sanitize_text_field($_POST['newsletter_subscriber_type'] ?? 'general');
        $interests = isset($_POST['newsletter_interests']) ? array_map('sanitize_text_field', $_POST['newsletter_interests']) : array();
        $privacy_consent = isset($_POST['newsletter_privacy_consent']) ? (bool) $_POST['newsletter_privacy_consent'] : false;

        // Validate email
        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Please enter a valid email address.', 'recruitpro')
            ));
        }

        // Check privacy consent if required
        if ($this->newsletter_settings['privacy_required'] && !$privacy_consent) {
            wp_send_json_error(array(
                'message' => __('Please accept our privacy policy to continue.', 'recruitpro')
            ));
        }

        // Prepare subscriber data for CRM
        $subscriber_data = array(
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $company,
            'subscriber_type' => $subscriber_type, // general, potential_client, partner, etc.
            'source' => 'website_newsletter',
            'subscription_type' => 'newsletter', // NOT job_alerts
            'content_interests' => $interests, // blog topics, not job categories
            'marketing_consent' => true,
            'privacy_consent' => $privacy_consent,
            'double_optin' => $this->newsletter_settings['double_optin'],
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'subscription_date' => current_time('mysql'),
            'subscription_source_url' => wp_get_referer() ?: home_url(),
            'newsletter_preferences' => array(
                'blog_posts' => true,
                'company_news' => true,
                'industry_insights' => in_array('industry_insights', $interests),
                'case_studies' => in_array('case_studies', $interests),
                'events' => in_array('events', $interests),
            ),
        );

        // Add subscriber to CRM email marketing system
        $result = recruitpro_crm_add_newsletter_subscriber($subscriber_data);

        if ($result['success']) {
            // Log successful subscription
            $this->log_subscription_attempt($email, 'success');

            // Trigger action for other plugins/systems
            do_action('recruitpro_newsletter_new_subscriber', $result['subscriber_id'], $subscriber_data);

            $response_data = array(
                'message' => $this->newsletter_settings['success_message'],
                'subscriber_id' => $result['subscriber_id']
            );

            // Add redirect URL if set
            if (!empty($this->newsletter_settings['redirect_after_signup'])) {
                $redirect_page = get_permalink($this->newsletter_settings['redirect_after_signup']);
                if ($redirect_page) {
                    $response_data['redirect_url'] = $redirect_page;
                }
            }

            wp_send_json_success($response_data);
        } else {
            // Log failed subscription
            $this->log_subscription_attempt($email, 'failed', $result['message']);

            wp_send_json_error(array(
                'message' => $result['message'] ?: __('Subscription failed. Please try again.', 'recruitpro')
            ));
        }
    }

    /**
     * Trigger newsletter when new blog post is published
     * 
     * @since 1.0.0
     * @param int $post_id Post ID
     * @param WP_Post $post Post object
     * @return void
     */
    public function trigger_newsletter_for_new_post($post_id, $post) {
        if (!$this->newsletter_settings['auto_send_new_posts'] || !$this->crm_available) {
            return;
        }

        // Only send for blog posts, not other post types
        if ($post->post_type !== 'post') {
            return;
        }

        // Don't send for revisions, auto-drafts, etc.
        if (wp_is_post_revision($post_id) || $post->post_status !== 'publish') {
            return;
        }

        // Check if this is actually a new post (not an update)
        $post_date = strtotime($post->post_date);
        $modified_date = strtotime($post->post_modified);
        
        // If modified time is significantly after post time, it's likely an update
        if (($modified_date - $post_date) > 300) { // 5 minutes grace period
            return;
        }

        // Prepare newsletter data
        $newsletter_data = array(
            'type' => 'new_blog_post',
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_excerpt' => get_the_excerpt($post),
            'post_url' => get_permalink($post_id),
            'post_author' => get_the_author_meta('display_name', $post->post_author),
            'post_categories' => wp_get_post_categories($post_id, array('fields' => 'names')),
            'send_to_segments' => array('newsletter_subscribers'),
            'schedule_send' => false, // Send immediately
        );

        // Send via CRM email marketing system
        if (function_exists('recruitpro_crm_send_newsletter_campaign')) {
            recruitpro_crm_send_newsletter_campaign($newsletter_data);
        }

        // Log the newsletter sending
        if (function_exists('recruitpro_crm_log_newsletter_activity')) {
            recruitpro_crm_log_newsletter_activity(array(
                'action' => 'auto_newsletter_sent',
                'trigger' => 'new_blog_post',
                'post_id' => $post_id,
                'timestamp' => current_time('mysql'),
            ));
        }
    }

    /**
     * Trigger newsletter when important page is published
     * 
     * @since 1.0.0
     * @param int $post_id Post ID
     * @param WP_Post $post Post object
     * @return void
     */
    public function trigger_newsletter_for_new_page($post_id, $post) {
        if (!$this->newsletter_settings['auto_send_new_pages'] || !$this->crm_available) {
            return;
        }

        // Only send for specific important pages (configurable)
        $important_pages = get_theme_mod('recruitpro_newsletter_important_pages', array());
        if (!in_array($post_id, $important_pages)) {
            return;
        }

        // Don't send for revisions, auto-drafts, etc.
        if (wp_is_post_revision($post_id) || $post->post_status !== 'publish') {
            return;
        }

        // Prepare newsletter data
        $newsletter_data = array(
            'type' => 'new_page',
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'post_excerpt' => get_the_excerpt($post),
            'post_url' => get_permalink($post_id),
            'send_to_segments' => array('newsletter_subscribers'),
            'schedule_send' => false,
        );

        // Send via CRM email marketing system
        if (function_exists('recruitpro_crm_send_newsletter_campaign')) {
            recruitpro_crm_send_newsletter_campaign($newsletter_data);
        }
    }

    /**
     * Render newsletter subscription form
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Newsletter form HTML
     */
    public function render_newsletter_form($atts = array()) {
        if (!$this->newsletter_settings['enabled'] || !$this->crm_available) {
            return '';
        }

        $atts = shortcode_atts(array(
            'style' => 'default',
            'show_names' => $this->newsletter_settings['show_name_fields'],
            'show_company' => $this->newsletter_settings['show_company_field'],
            'show_interests' => $this->newsletter_settings['show_interests'],
            'show_subscriber_type' => true,
            'button_text' => __('Subscribe to Newsletter', 'recruitpro'),
            'placeholder_email' => __('Enter your email address', 'recruitpro'),
            'placeholder_first_name' => __('First Name', 'recruitpro'),
            'placeholder_last_name' => __('Last Name', 'recruitpro'),
            'placeholder_company' => __('Company (Optional)', 'recruitpro'),
            'title' => __('Stay Updated', 'recruitpro'),
            'description' => __('Subscribe to our newsletter for the latest industry insights, company news, and blog updates.', 'recruitpro'),
        ), $atts);

        ob_start();
        ?>
        <div class="recruitpro-newsletter-form-wrapper" data-style="<?php echo esc_attr($atts['style']); ?>">
            <?php if (!empty($atts['title'])): ?>
            <h3 class="newsletter-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($atts['description'])): ?>
            <p class="newsletter-description"><?php echo esc_html($atts['description']); ?></p>
            <?php endif; ?>

            <form class="recruitpro-newsletter-form" id="recruitpro-newsletter-form">
                <?php wp_nonce_field('recruitpro_newsletter_nonce', 'newsletter_nonce'); ?>
                
                <!-- Honeypot field for spam protection -->
                <?php if ($this->newsletter_settings['honeypot_protection']): ?>
                <div style="position: absolute; left: -9999px;">
                    <input type="text" name="newsletter_website" id="newsletter_website" value="" />
                </div>
                <?php endif; ?>

                <div class="newsletter-form-fields">
                    <!-- Name fields -->
                    <?php if ($atts['show_names']): ?>
                    <div class="newsletter-name-fields">
                        <div class="newsletter-field">
                            <input type="text" 
                                   name="newsletter_first_name" 
                                   id="newsletter_first_name"
                                   placeholder="<?php echo esc_attr($atts['placeholder_first_name']); ?>"
                                   class="newsletter-input newsletter-first-name" />
                        </div>
                        <div class="newsletter-field">
                            <input type="text" 
                                   name="newsletter_last_name" 
                                   id="newsletter_last_name"
                                   placeholder="<?php echo esc_attr($atts['placeholder_last_name']); ?>"
                                   class="newsletter-input newsletter-last-name" />
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Email field -->
                    <div class="newsletter-field newsletter-email-field">
                        <input type="email" 
                               name="newsletter_email" 
                               id="newsletter_email"
                               placeholder="<?php echo esc_attr($atts['placeholder_email']); ?>"
                               class="newsletter-input newsletter-email"
                               required />
                    </div>

                    <!-- Company field -->
                    <?php if ($atts['show_company']): ?>
                    <div class="newsletter-field newsletter-company-field">
                        <input type="text" 
                               name="newsletter_company" 
                               id="newsletter_company"
                               placeholder="<?php echo esc_attr($atts['placeholder_company']); ?>"
                               class="newsletter-input newsletter-company" />
                    </div>
                    <?php endif; ?>

                    <!-- Subscriber type -->
                    <?php if ($atts['show_subscriber_type']): ?>
                    <div class="newsletter-field newsletter-subscriber-type-field">
                        <label class="newsletter-label"><?php _e('I am a:', 'recruitpro'); ?></label>
                        <select name="newsletter_subscriber_type" id="newsletter_subscriber_type" class="newsletter-select">
                            <option value="general"><?php _e('General Visitor', 'recruitpro'); ?></option>
                            <option value="potential_client"><?php _e('Potential Client', 'recruitpro'); ?></option>
                            <option value="current_client"><?php _e('Current Client', 'recruitpro'); ?></option>
                            <option value="partner"><?php _e('Partner/Vendor', 'recruitpro'); ?></option>
                            <option value="media"><?php _e('Media/Press', 'recruitpro'); ?></option>
                            <option value="other"><?php _e('Other', 'recruitpro'); ?></option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Content interests -->
                    <?php if ($atts['show_interests']): ?>
                    <div class="newsletter-field newsletter-interests-field">
                        <label class="newsletter-label"><?php _e('Content Interests:', 'recruitpro'); ?></label>
                        <div class="newsletter-interests">
                            <?php
                            $available_interests = $this->get_content_interests();
                            foreach ($available_interests as $interest_key => $interest_label):
                            ?>
                            <label class="newsletter-interest-item">
                                <input type="checkbox" 
                                       name="newsletter_interests[]" 
                                       value="<?php echo esc_attr($interest_key); ?>" />
                                <span><?php echo esc_html($interest_label); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Privacy consent -->
                    <?php if ($this->newsletter_settings['privacy_required']): ?>
                    <div class="newsletter-field newsletter-privacy-field">
                        <label class="newsletter-checkbox-label">
                            <input type="checkbox" 
                                   name="newsletter_privacy_consent" 
                                   id="newsletter_privacy_consent"
                                   value="1" 
                                   required />
                            <span>
                                <?php
                                printf(
                                    __('I agree to the %s and consent to receiving newsletter communications about company news and blog updates.', 'recruitpro'),
                                    '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">' . __('Privacy Policy', 'recruitpro') . '</a>'
                                );
                                ?>
                            </span>
                        </label>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Submit button -->
                <div class="newsletter-submit-wrapper">
                    <button type="submit" class="newsletter-submit-btn">
                        <span class="button-text"><?php echo esc_html($atts['button_text']); ?></span>
                        <span class="button-loading" style="display: none;"><?php _e('Subscribing...', 'recruitpro'); ?></span>
                    </button>
                </div>

                <!-- Messages container -->
                <div class="newsletter-messages" id="newsletter-messages"></div>
            </form>

            <p class="newsletter-note">
                <?php _e('Note: This is for general newsletter updates. For job alerts, please visit our jobs page.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get available content interests (not job categories)
     * 
     * @since 1.0.0
     * @return array Available content interests
     */
    private function get_content_interests() {
        if (function_exists('recruitpro_crm_get_newsletter_content_interests')) {
            return recruitpro_crm_get_newsletter_content_interests();
        }

        // Fallback content interests
        return array(
            'industry_insights' => __('Industry Insights', 'recruitpro'),
            'case_studies' => __('Case Studies', 'recruitpro'),
            'company_news' => __('Company News', 'recruitpro'),
            'events' => __('Events & Webinars', 'recruitpro'),
            'best_practices' => __('Best Practices', 'recruitpro'),
            'market_trends' => __('Market Trends', 'recruitpro'),
        );
    }

    /**
     * Check if current IP is rate limited
     * 
     * @since 1.0.0
     * @return bool True if rate limited
     */
    private function is_rate_limited() {
        $ip = $this->get_client_ip();
        $transient_key = 'recruitpro_newsletter_rate_limit_' . md5($ip);
        $attempts = get_transient($transient_key);

        if ($attempts && $attempts >= 3) {
            return true;
        }

        // Increment attempts
        set_transient($transient_key, ($attempts ? $attempts + 1 : 1), HOUR_IN_SECONDS);
        
        return false;
    }

    /**
     * Get client IP address
     * 
     * @since 1.0.0
     * @return string Client IP address
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Log subscription attempt
     * 
     * @since 1.0.0
     * @param string $email Email address
     * @param string $status Attempt status
     * @param string $message Optional message
     * @return void
     */
    private function log_subscription_attempt($email, $status, $message = '') {
        if (function_exists('recruitpro_crm_log_newsletter_activity')) {
            recruitpro_crm_log_newsletter_activity(array(
                'email' => $email,
                'action' => 'newsletter_subscription_attempt',
                'status' => $status,
                'message' => $message,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'timestamp' => current_time('mysql'),
            ));
        }
    }

    /**
     * Notify admin of new newsletter subscriber
     * 
     * @since 1.0.0
     * @param int $subscriber_id Subscriber ID
     * @return void
     */
    public function notify_admin_new_subscriber($subscriber_id) {
        $admin_email = get_option('admin_email');
        if (!$admin_email) {
            return;
        }

        $subject = sprintf(__('[%s] New Newsletter Subscriber', 'recruitpro'), get_bloginfo('name'));
        $message = sprintf(__('A new subscriber has joined your newsletter: %s', 'recruitpro'), $subscriber_id);

        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Enqueue newsletter scripts and styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_newsletter_scripts() {
        if (!$this->newsletter_settings['enabled']) {
            return;
        }

        wp_enqueue_script(
            'recruitpro-newsletter',
            get_template_directory_uri() . '/assets/js/newsletter.js',
            array('jquery'),
            RECRUITPRO_THEME_VERSION,
            true
        );

        wp_localize_script('recruitpro-newsletter', 'recruitpro_newsletter_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_newsletter_nonce'),
            'messages' => array(
                'processing' => __('Processing...', 'recruitpro'),
                'error_general' => __('An error occurred. Please try again.', 'recruitpro'),
                'error_email' => __('Please enter a valid email address.', 'recruitpro'),
                'error_privacy' => __('Please accept our privacy policy.', 'recruitpro'),
            ),
        ));
    }

    /**
     * Add newsletter settings to theme customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_newsletter($wp_customize) {
        // Newsletter section
        $wp_customize->add_section('recruitpro_newsletter', array(
            'title' => __('Newsletter Integration', 'recruitpro'),
            'description' => __('Configure newsletter subscription settings for blog posts and company news. Job alerts are handled separately.', 'recruitpro'),
            'priority' => 35,
        ));

        // Enable newsletter
        $wp_customize->add_setting('recruitpro_newsletter_enabled', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_newsletter_enabled', array(
            'label' => __('Enable Newsletter Subscription', 'recruitpro'),
            'section' => 'recruitpro_newsletter',
            'type' => 'checkbox',
        ));

        // Auto-send for new blog posts
        $wp_customize->add_setting('recruitpro_newsletter_auto_send_posts', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_newsletter_auto_send_posts', array(
            'label' => __('Auto-send Newsletter for New Blog Posts', 'recruitpro'),
            'description' => __('Automatically send newsletter to subscribers when new blog posts are published.', 'recruitpro'),
            'section' => 'recruitpro_newsletter',
            'type' => 'checkbox',
        ));

        // Show company field
        $wp_customize->add_setting('recruitpro_newsletter_show_company', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_newsletter_show_company', array(
            'label' => __('Show Company Field', 'recruitpro'),
            'description' => __('Show company field for potential clients and partners.', 'recruitpro'),
            'section' => 'recruitpro_newsletter',
            'type' => 'checkbox',
        ));

        // Success message
        $wp_customize->add_setting('recruitpro_newsletter_success_message', array(
            'default' => __('Thank you for subscribing to our newsletter!', 'recruitpro'),
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('recruitpro_newsletter_success_message', array(
            'label' => __('Success Message', 'recruitpro'),
            'section' => 'recruitpro_newsletter',
            'type' => 'text',
        ));
    }

    /**
     * Register newsletter widget
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_newsletter_widget() {
        register_widget('RecruitPro_Newsletter_Widget');
    }
}

/**
 * Newsletter Widget Class
 * 
 * @since 1.0.0
 */
class RecruitPro_Newsletter_Widget extends WP_Widget {

    /**
     * Widget constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        parent::__construct(
            'recruitpro_newsletter_widget',
            __('RecruitPro Newsletter', 'recruitpro'),
            array(
                'description' => __('Newsletter subscription form for blog posts and company news (not job alerts).', 'recruitpro'),
            )
        );
    }

    /**
     * Widget output
     * 
     * @since 1.0.0
     * @param array $args Display arguments
     * @param array $instance Widget instance
     * @return void
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Subscribe to Newsletter', 'recruitpro');
        $description = !empty($instance['description']) ? $instance['description'] : __('Get the latest blog posts and company news.', 'recruitpro');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Subscribe', 'recruitpro');

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        if ($description) {
            echo '<p class="newsletter-widget-description">' . esc_html($description) . '</p>';
        }

        echo do_shortcode('[recruitpro_newsletter_form button_text="' . esc_attr($button_text) . '" style="widget"]');

        echo $args['after_widget'];
    }

    /**
     * Widget form
     * 
     * @since 1.0.0
     * @param array $instance Widget instance
     * @return string|void
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Subscribe to Newsletter', 'recruitpro');
        $description = !empty($instance['description']) ? $instance['description'] : __('Get the latest blog posts and company news.', 'recruitpro');
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : __('Subscribe', 'recruitpro');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php _e('Description:', 'recruitpro'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>" rows="3"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php _e('Button Text:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>
        <?php
    }

    /**
     * Update widget
     * 
     * @since 1.0.0
     * @param array $new_instance New widget instance
     * @param array $old_instance Old widget instance
     * @return array Updated instance
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? sanitize_textarea_field($new_instance['description']) : '';
        $instance['button_text'] = (!empty($new_instance['button_text'])) ? sanitize_text_field($new_instance['button_text']) : '';

        return $instance;
    }
}

// Initialize the newsletter integration
if (class_exists('RecruitPro_Newsletter_Integration')) {
    new RecruitPro_Newsletter_Integration();
}

/**
 * Helper function to check if newsletter is enabled
 * 
 * @since 1.0.0
 * @return bool True if newsletter is enabled
 */
function recruitpro_newsletter_enabled() {
    return get_theme_mod('recruitpro_newsletter_enabled', true);
}

/**
 * Helper function to display newsletter form
 * 
 * @since 1.0.0
 * @param array $args Form arguments
 * @return void
 */
function recruitpro_display_newsletter_form($args = array()) {
    if (recruitpro_newsletter_enabled()) {
        echo do_shortcode('[recruitpro_newsletter_form]');
    }
}