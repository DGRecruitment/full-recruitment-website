<?php
/**
 * RecruitPro Theme GDPR Compliance System
 *
 * This file handles GDPR (General Data Protection Regulation) compliance
 * features specifically designed for recruitment agencies. It includes
 * cookie consent management, privacy controls, data protection notices,
 * and compliance tools required for handling candidate and client data.
 *
 * @package RecruitPro
 * @subpackage Theme/GDPR
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/gdpr-compliance.php
 * Purpose: GDPR compliance management for recruitment industry
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GDPR Compliance Class
 * 
 * Handles all GDPR compliance features for the recruitment website
 *
 * @since 1.0.0
 */
class RecruitPro_GDPR_Compliance {

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_cookie_banner'));
        add_action('wp_ajax_recruitpro_cookie_consent', array($this, 'handle_cookie_consent'));
        add_action('wp_ajax_nopriv_recruitpro_cookie_consent', array($this, 'handle_cookie_consent'));
        add_action('customize_register', array($this, 'add_customizer_options'));
        add_shortcode('recruitpro_privacy_controls', array($this, 'privacy_controls_shortcode'));
        add_shortcode('recruitpro_data_request', array($this, 'data_request_shortcode'));
    }

    /**
     * Initialize GDPR compliance features
     * 
     * @since 1.0.0
     * @return void
     */
    public function init() {
        
        // Add privacy policy page if it doesn't exist
        $this->create_default_privacy_pages();
        
        // Register GDPR compliance settings
        $this->register_settings();
        
        // Handle data requests
        add_action('wp', array($this, 'handle_data_requests'));
        
        // Add GDPR notices to forms
        add_filter('comment_form_defaults', array($this, 'add_gdpr_to_comment_form'));
        
        // Privacy-friendly optimizations
        $this->optimize_for_privacy();
    }

    /**
     * Enqueue GDPR compliance scripts and styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_scripts() {
        
        $theme_version = wp_get_theme()->get('Version');
        
        // GDPR compliance styles
        wp_enqueue_style(
            'recruitpro-gdpr-styles',
            get_template_directory_uri() . '/assets/css/gdpr-compliance.css',
            array(),
            $theme_version
        );
        
        // GDPR compliance JavaScript
        wp_enqueue_script(
            'recruitpro-gdpr-scripts',
            get_template_directory_uri() . '/assets/js/gdpr-compliance.js',
            array('jquery'),
            $theme_version,
            true
        );
        
        // Localize script
        wp_localize_script('recruitpro-gdpr-scripts', 'recruitpro_gdpr', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_gdpr_nonce'),
            'strings' => array(
                'accept_all' => __('Accept All Cookies', 'recruitpro'),
                'reject_all' => __('Reject All', 'recruitpro'),
                'customize' => __('Customize Settings', 'recruitpro'),
                'save_preferences' => __('Save Preferences', 'recruitpro'),
                'privacy_policy' => __('Privacy Policy', 'recruitpro'),
                'cookie_policy' => __('Cookie Policy', 'recruitpro'),
                'success_message' => __('Your preferences have been saved.', 'recruitpro'),
                'error_message' => __('Error saving preferences. Please try again.', 'recruitpro'),
            ),
            'settings' => array(
                'banner_position' => get_theme_mod('recruitpro_gdpr_banner_position', 'bottom'),
                'auto_hide_delay' => get_theme_mod('recruitpro_gdpr_auto_hide_delay', 0),
                'respect_dnt' => get_theme_mod('recruitpro_gdpr_respect_dnt', true),
                'consent_duration' => get_theme_mod('recruitpro_gdpr_consent_duration', 365),
            )
        ));
    }

    /**
     * Add GDPR customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_customizer_options($wp_customize) {
        
        // =================================================================
        // GDPR COMPLIANCE PANEL
        // =================================================================
        
        $wp_customize->add_panel('recruitpro_gdpr_panel', array(
            'title' => __('GDPR Compliance', 'recruitpro'),
            'description' => __('Configure GDPR compliance settings for your recruitment website. Essential for handling candidate and client data legally.', 'recruitpro'),
            'priority' => 140,
            'capability' => 'edit_theme_options',
        ));

        // =================================================================
        // COOKIE CONSENT SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_gdpr_cookies', array(
            'title' => __('Cookie Consent', 'recruitpro'),
            'description' => __('Configure cookie consent banner and management.', 'recruitpro'),
            'panel' => 'recruitpro_gdpr_panel',
            'priority' => 10,
        ));

        // Enable cookie consent banner
        $wp_customize->add_setting('recruitpro_gdpr_enable_banner', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_enable_banner', array(
            'label' => __('Enable Cookie Consent Banner', 'recruitpro'),
            'description' => __('Show cookie consent banner to comply with GDPR requirements.', 'recruitpro'),
            'section' => 'recruitpro_gdpr_cookies',
            'type' => 'checkbox',
        ));

        // Banner message
        $wp_customize->add_setting('recruitpro_gdpr_banner_message', array(
            'default' => __('We use cookies to enhance your experience on our recruitment website. This includes cookies for analytics, functionality, and marketing. By continuing to use our site, you consent to our use of cookies in accordance with our Privacy Policy.', 'recruitpro'),
            'sanitize_callback' => 'wp_kses_post',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_banner_message', array(
            'label' => __('Cookie Banner Message', 'recruitpro'),
            'description' => __('Message displayed in the cookie consent banner.', 'recruitpro'),
            'section' => 'recruitpro_gdpr_cookies',
            'type' => 'textarea',
        ));

        // Banner position
        $wp_customize->add_setting('recruitpro_gdpr_banner_position', array(
            'default' => 'bottom',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_banner_position', array(
            'label' => __('Banner Position', 'recruitpro'),
            'section' => 'recruitpro_gdpr_cookies',
            'type' => 'select',
            'choices' => array(
                'top' => __('Top of page', 'recruitpro'),
                'bottom' => __('Bottom of page', 'recruitpro'),
                'floating' => __('Floating overlay', 'recruitpro'),
            ),
        ));

        // Consent duration
        $wp_customize->add_setting('recruitpro_gdpr_consent_duration', array(
            'default' => 365,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_consent_duration', array(
            'label' => __('Consent Duration (days)', 'recruitpro'),
            'description' => __('How long to remember user consent choices.', 'recruitpro'),
            'section' => 'recruitpro_gdpr_cookies',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 1095,
                'step' => 1,
            ),
        ));

        // =================================================================
        // PRIVACY CONTROLS SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_gdpr_privacy', array(
            'title' => __('Privacy Controls', 'recruitpro'),
            'description' => __('Configure data privacy and user control options.', 'recruitpro'),
            'panel' => 'recruitpro_gdpr_panel',
            'priority' => 20,
        ));

        // Enable data portability
        $wp_customize->add_setting('recruitpro_gdpr_enable_data_export', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_enable_data_export', array(
            'label' => __('Enable Data Export', 'recruitpro'),
            'description' => __('Allow users to request their personal data for download.', 'recruitpro'),
            'section' => 'recruitpro_gdpr_privacy',
            'type' => 'checkbox',
        ));

        // Enable data deletion
        $wp_customize->add_setting('recruitpro_gdpr_enable_data_deletion', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_enable_data_deletion', array(
            'label' => __('Enable Data Deletion Requests', 'recruitpro'),
            'description' => __('Allow users to request deletion of their personal data.', 'recruitpro'),
            'section' => 'recruitpro_gdpr_privacy',
            'type' => 'checkbox',
        ));

        // Data retention period
        $wp_customize->add_setting('recruitpro_gdpr_data_retention_period', array(
            'default' => 1095, // 3 years default for recruitment
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_data_retention_period', array(
            'label' => __('Data Retention Period (days)', 'recruitpro'),
            'description' => __('How long to keep candidate data (recommended: 3 years for recruitment).', 'recruitpro'),
            'section' => 'recruitpro_gdpr_privacy',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 365,
                'max' => 3650,
                'step' => 1,
            ),
        ));

        // =================================================================
        // COMPLIANCE NOTICES SECTION
        // =================================================================
        
        $wp_customize->add_section('recruitpro_gdpr_notices', array(
            'title' => __('Compliance Notices', 'recruitpro'),
            'description' => __('Configure privacy notices and legal compliance messages.', 'recruitpro'),
            'panel' => 'recruitpro_gdpr_panel',
            'priority' => 30,
        ));

        // Privacy notice for forms
        $wp_customize->add_setting('recruitpro_gdpr_form_notice', array(
            'default' => __('By submitting this form, you consent to our processing of your personal data in accordance with our Privacy Policy. Your data will be used for recruitment purposes only.', 'recruitpro'),
            'sanitize_callback' => 'wp_kses_post',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_form_notice', array(
            'label' => __('Form Privacy Notice', 'recruitpro'),
            'description' => __('Privacy notice displayed on forms (job applications, contact forms).', 'recruitpro'),
            'section' => 'recruitpro_gdpr_notices',
            'type' => 'textarea',
        ));

        // CV upload notice
        $wp_customize->add_setting('recruitpro_gdpr_cv_notice', array(
            'default' => __('Your CV and personal data will be stored securely and used solely for recruitment purposes. You have the right to access, modify, or request deletion of your data at any time.', 'recruitpro'),
            'sanitize_callback' => 'wp_kses_post',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_cv_notice', array(
            'label' => __('CV Upload Privacy Notice', 'recruitpro'),
            'description' => __('Specific notice for CV/resume uploads.', 'recruitpro'),
            'section' => 'recruitpro_gdpr_notices',
            'type' => 'textarea',
        ));

        // Data protection officer contact
        $wp_customize->add_setting('recruitpro_gdpr_dpo_email', array(
            'default' => '',
            'sanitize_callback' => 'sanitize_email',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_gdpr_dpo_email', array(
            'label' => __('Data Protection Officer Email', 'recruitpro'),
            'description' => __('Email address for privacy-related inquiries (if applicable).', 'recruitpro'),
            'section' => 'recruitpro_gdpr_notices',
            'type' => 'email',
        ));
    }

    /**
     * Render cookie consent banner
     * 
     * @since 1.0.0
     * @return void
     */
    public function render_cookie_banner() {
        
        // Check if banner is enabled
        if (!get_theme_mod('recruitpro_gdpr_enable_banner', true)) {
            return;
        }

        // Check if user has already consented
        if (isset($_COOKIE['recruitpro_gdpr_consent'])) {
            return;
        }

        // Respect Do Not Track header
        if (get_theme_mod('recruitpro_gdpr_respect_dnt', true) && 
            function_exists('getallheaders') && 
            isset(getallheaders()['DNT']) && 
            getallheaders()['DNT'] == '1') {
            return;
        }

        $banner_message = get_theme_mod('recruitpro_gdpr_banner_message', '');
        $banner_position = get_theme_mod('recruitpro_gdpr_banner_position', 'bottom');
        $privacy_page = get_privacy_policy_url();
        
        ?>
        <div id="recruitpro-gdpr-banner" class="gdpr-banner gdpr-banner-<?php echo esc_attr($banner_position); ?>" style="display: none;">
            <div class="gdpr-banner-content">
                <div class="gdpr-banner-message">
                    <?php echo wp_kses_post($banner_message); ?>
                    <?php if ($privacy_page): ?>
                        <a href="<?php echo esc_url($privacy_page); ?>" class="gdpr-privacy-link">
                            <?php _e('Privacy Policy', 'recruitpro'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="gdpr-banner-actions">
                    <button type="button" class="gdpr-btn gdpr-btn-accept" data-consent="all">
                        <?php _e('Accept All', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="gdpr-btn gdpr-btn-reject" data-consent="essential">
                        <?php _e('Essential Only', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="gdpr-btn gdpr-btn-customize" data-action="customize">
                        <?php _e('Customize', 'recruitpro'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Cookie customization panel -->
            <div id="gdpr-customize-panel" class="gdpr-customize-panel" style="display: none;">
                <h3><?php _e('Cookie Preferences', 'recruitpro'); ?></h3>
                <div class="gdpr-cookie-categories">
                    
                    <div class="gdpr-cookie-category">
                        <label class="gdpr-cookie-label">
                            <input type="checkbox" name="gdpr_essential" checked disabled>
                            <span class="gdpr-cookie-name"><?php _e('Essential Cookies', 'recruitpro'); ?></span>
                        </label>
                        <p class="gdpr-cookie-description">
                            <?php _e('These cookies are necessary for the website to function and cannot be switched off. They are usually only set in response to actions made by you which amount to a request for services.', 'recruitpro'); ?>
                        </p>
                    </div>
                    
                    <div class="gdpr-cookie-category">
                        <label class="gdpr-cookie-label">
                            <input type="checkbox" name="gdpr_analytics" checked>
                            <span class="gdpr-cookie-name"><?php _e('Analytics Cookies', 'recruitpro'); ?></span>
                        </label>
                        <p class="gdpr-cookie-description">
                            <?php _e('These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously.', 'recruitpro'); ?>
                        </p>
                    </div>
                    
                    <div class="gdpr-cookie-category">
                        <label class="gdpr-cookie-label">
                            <input type="checkbox" name="gdpr_marketing" checked>
                            <span class="gdpr-cookie-name"><?php _e('Marketing Cookies', 'recruitpro'); ?></span>
                        </label>
                        <p class="gdpr-cookie-description">
                            <?php _e('These cookies are used to deliver relevant job opportunities and recruitment content based on your interests.', 'recruitpro'); ?>
                        </p>
                    </div>
                    
                    <div class="gdpr-cookie-category">
                        <label class="gdpr-cookie-label">
                            <input type="checkbox" name="gdpr_functional" checked>
                            <span class="gdpr-cookie-name"><?php _e('Functional Cookies', 'recruitpro'); ?></span>
                        </label>
                        <p class="gdpr-cookie-description">
                            <?php _e('These cookies enable enhanced functionality and personalization, such as remembering your preferences and settings.', 'recruitpro'); ?>
                        </p>
                    </div>
                </div>
                
                <div class="gdpr-customize-actions">
                    <button type="button" class="gdpr-btn gdpr-btn-save" data-action="save-custom">
                        <?php _e('Save Preferences', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="gdpr-btn gdpr-btn-back" data-action="back">
                        <?php _e('Back', 'recruitpro'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Handle cookie consent AJAX request
     * 
     * @since 1.0.0
     * @return void
     */
    public function handle_cookie_consent() {
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_gdpr_nonce')) {
            wp_die('Security check failed');
        }

        $consent_type = sanitize_text_field($_POST['consent_type']);
        $custom_preferences = isset($_POST['preferences']) ? 
            array_map('sanitize_text_field', $_POST['preferences']) : array();

        $consent_duration = get_theme_mod('recruitpro_gdpr_consent_duration', 365);
        $expire_time = time() + ($consent_duration * 24 * 60 * 60);

        // Set consent cookie
        $consent_value = json_encode(array(
            'type' => $consent_type,
            'preferences' => $custom_preferences,
            'timestamp' => time(),
        ));

        setcookie('recruitpro_gdpr_consent', $consent_value, $expire_time, '/');

        // Log consent for compliance records
        $this->log_consent_action($consent_type, $custom_preferences);

        wp_send_json_success(array(
            'message' => __('Your privacy preferences have been saved.', 'recruitpro'),
        ));
    }

    /**
     * Privacy controls shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function privacy_controls_shortcode($atts) {
        
        $atts = shortcode_atts(array(
            'show_export' => 'true',
            'show_delete' => 'true',
            'show_update' => 'true',
        ), $atts, 'recruitpro_privacy_controls');

        ob_start();
        ?>
        <div class="recruitpro-privacy-controls">
            <h3><?php _e('Your Privacy Rights', 'recruitpro'); ?></h3>
            <p><?php _e('As a recruitment candidate, you have specific rights regarding your personal data:', 'recruitpro'); ?></p>
            
            <div class="privacy-controls-grid">
                
                <?php if ($atts['show_export'] === 'true' && get_theme_mod('recruitpro_gdpr_enable_data_export', true)): ?>
                <div class="privacy-control-item">
                    <h4><?php _e('Data Portability', 'recruitpro'); ?></h4>
                    <p><?php _e('Request a copy of all personal data we hold about you.', 'recruitpro'); ?></p>
                    <button type="button" class="privacy-btn" data-action="export-data">
                        <?php _e('Request Data Export', 'recruitpro'); ?>
                    </button>
                </div>
                <?php endif; ?>
                
                <?php if ($atts['show_delete'] === 'true' && get_theme_mod('recruitpro_gdpr_enable_data_deletion', true)): ?>
                <div class="privacy-control-item">
                    <h4><?php _e('Right to be Forgotten', 'recruitpro'); ?></h4>
                    <p><?php _e('Request deletion of your personal data from our systems.', 'recruitpro'); ?></p>
                    <button type="button" class="privacy-btn privacy-btn-danger" data-action="delete-data">
                        <?php _e('Request Data Deletion', 'recruitpro'); ?>
                    </button>
                </div>
                <?php endif; ?>
                
                <?php if ($atts['show_update'] === 'true'): ?>
                <div class="privacy-control-item">
                    <h4><?php _e('Data Rectification', 'recruitpro'); ?></h4>
                    <p><?php _e('Update or correct your personal information.', 'recruitpro'); ?></p>
                    <button type="button" class="privacy-btn" data-action="update-data">
                        <?php _e('Update My Data', 'recruitpro'); ?>
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="privacy-control-item">
                    <h4><?php _e('Cookie Preferences', 'recruitpro'); ?></h4>
                    <p><?php _e('Manage your cookie and tracking preferences.', 'recruitpro'); ?></p>
                    <button type="button" class="privacy-btn" data-action="manage-cookies">
                        <?php _e('Manage Cookies', 'recruitpro'); ?>
                    </button>
                </div>
            </div>
            
            <div class="privacy-contact-info">
                <p>
                    <?php _e('For privacy-related questions, contact us at:', 'recruitpro'); ?>
                    <?php 
                    $dpo_email = get_theme_mod('recruitpro_gdpr_dpo_email', '');
                    if ($dpo_email): ?>
                        <a href="mailto:<?php echo esc_attr($dpo_email); ?>"><?php echo esc_html($dpo_email); ?></a>
                    <?php else: ?>
                        <a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>"><?php echo esc_html(get_option('admin_email')); ?></a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Data request shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function data_request_shortcode($atts) {
        
        $atts = shortcode_atts(array(
            'type' => 'export', // export, delete, update
        ), $atts, 'recruitpro_data_request');

        ob_start();
        ?>
        <div class="recruitpro-data-request-form">
            <form id="gdpr-data-request-form" method="post">
                <?php wp_nonce_field('recruitpro_data_request', 'gdpr_nonce'); ?>
                <input type="hidden" name="request_type" value="<?php echo esc_attr($atts['type']); ?>">
                
                <div class="form-row">
                    <label for="requester_email"><?php _e('Your Email Address', 'recruitpro'); ?> *</label>
                    <input type="email" id="requester_email" name="requester_email" required>
                </div>
                
                <div class="form-row">
                    <label for="requester_name"><?php _e('Full Name', 'recruitpro'); ?> *</label>
                    <input type="text" id="requester_name" name="requester_name" required>
                </div>
                
                <?php if ($atts['type'] === 'delete'): ?>
                <div class="form-row">
                    <label for="deletion_reason"><?php _e('Reason for Deletion (Optional)', 'recruitpro'); ?></label>
                    <textarea id="deletion_reason" name="deletion_reason" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="confirm_deletion" required>
                        <?php _e('I understand that this action cannot be undone and will permanently remove my data.', 'recruitpro'); ?>
                    </label>
                </div>
                <?php endif; ?>
                
                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="identity_confirmation" required>
                        <?php _e('I confirm that I am the person whose data is being requested, or I am authorized to act on their behalf.', 'recruitpro'); ?>
                    </label>
                </div>
                
                <div class="gdpr-form-notice">
                    <?php echo wp_kses_post(get_theme_mod('recruitpro_gdpr_form_notice', '')); ?>
                </div>
                
                <button type="submit" class="privacy-btn">
                    <?php 
                    switch ($atts['type']) {
                        case 'export':
                            _e('Submit Export Request', 'recruitpro');
                            break;
                        case 'delete':
                            _e('Submit Deletion Request', 'recruitpro');
                            break;
                        case 'update':
                            _e('Submit Update Request', 'recruitpro');
                            break;
                    }
                    ?>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Add GDPR notice to comment form
     * 
     * @since 1.0.0
     * @param array $defaults Comment form defaults
     * @return array Modified defaults
     */
    public function add_gdpr_to_comment_form($defaults) {
        
        $gdpr_notice = get_theme_mod('recruitpro_gdpr_form_notice', '');
        
        if ($gdpr_notice) {
            $defaults['comment_notes_after'] = '<div class="gdpr-form-notice">' . wp_kses_post($gdpr_notice) . '</div>';
        }
        
        return $defaults;
    }

    /**
     * Handle data requests
     * 
     * @since 1.0.0
     * @return void
     */
    public function handle_data_requests() {
        
        if (!isset($_POST['gdpr_nonce']) || !wp_verify_nonce($_POST['gdpr_nonce'], 'recruitpro_data_request')) {
            return;
        }

        $request_type = sanitize_text_field($_POST['request_type']);
        $requester_email = sanitize_email($_POST['requester_email']);
        $requester_name = sanitize_text_field($_POST['requester_name']);

        // Create WordPress privacy request
        $request_id = wp_create_user_request($requester_email, $request_type);

        if (is_wp_error($request_id)) {
            wp_die($request_id->get_error_message());
        }

        // Log the request
        $this->log_privacy_request($request_type, $requester_email, $requester_name);

        // Redirect with success message
        wp_redirect(add_query_arg('gdpr_request', 'submitted', wp_get_referer()));
        exit;
    }

    /**
     * Create default privacy pages
     * 
     * @since 1.0.0
     * @return void
     */
    private function create_default_privacy_pages() {
        
        // Check if privacy policy page exists
        if (!get_option('wp_page_for_privacy_policy')) {
            
            $privacy_content = $this->get_default_privacy_policy_content();
            
            $privacy_page = wp_insert_post(array(
                'post_title' => __('Privacy Policy', 'recruitpro'),
                'post_content' => $privacy_content,
                'post_status' => 'draft',
                'post_type' => 'page',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
            ));

            if ($privacy_page && !is_wp_error($privacy_page)) {
                update_option('wp_page_for_privacy_policy', $privacy_page);
            }
        }
    }

    /**
     * Get default privacy policy content for recruitment agencies
     * 
     * @since 1.0.0
     * @return string Privacy policy content
     */
    private function get_default_privacy_policy_content() {
        
        return __('
        <h2>Privacy Policy for Recruitment Services</h2>
        
        <h3>1. Information We Collect</h3>
        <p>As a recruitment agency, we collect personal information necessary for our recruitment services, including:</p>
        <ul>
            <li>Contact information (name, email, phone number, address)</li>
            <li>Professional information (CV, work history, skills, qualifications)</li>
            <li>Interview notes and assessments</li>
            <li>References and background check information</li>
        </ul>
        
        <h3>2. How We Use Your Information</h3>
        <p>We use your personal data for:</p>
        <ul>
            <li>Matching you with suitable job opportunities</li>
            <li>Communicating with you about positions and services</li>
            <li>Conducting interviews and assessments</li>
            <li>Verifying your qualifications and references</li>
            <li>Complying with legal and regulatory requirements</li>
        </ul>
        
        <h3>3. Data Sharing</h3>
        <p>We may share your information with:</p>
        <ul>
            <li>Potential employers (with your consent)</li>
            <li>Third-party service providers</li>
            <li>Legal authorities when required by law</li>
        </ul>
        
        <h3>4. Your Rights</h3>
        <p>Under GDPR, you have the right to:</p>
        <ul>
            <li>Access your personal data</li>
            <li>Correct inaccurate information</li>
            <li>Request deletion of your data</li>
            <li>Object to processing</li>
            <li>Data portability</li>
        </ul>
        
        <h3>5. Data Retention</h3>
        <p>We retain your data for up to 3 years after your last interaction with us, unless you request earlier deletion or we are required to keep it longer for legal reasons.</p>
        
        <h3>6. Contact Us</h3>
        <p>For privacy-related questions, contact us at: [YOUR_EMAIL]</p>
        ', 'recruitpro');
    }

    /**
     * Register GDPR settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function register_settings() {
        
        // Register settings for GDPR compliance
        register_setting('recruitpro_gdpr_settings', 'recruitpro_gdpr_consent_log');
        register_setting('recruitpro_gdpr_settings', 'recruitpro_gdpr_request_log');
    }

    /**
     * Log consent action for compliance records
     * 
     * @since 1.0.0
     * @param string $consent_type Type of consent
     * @param array $preferences User preferences
     * @return void
     */
    private function log_consent_action($consent_type, $preferences) {
        
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'ip_address' => $this->get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'consent_type' => $consent_type,
            'preferences' => $preferences,
        );

        $existing_log = get_option('recruitpro_gdpr_consent_log', array());
        $existing_log[] = $log_entry;
        
        // Keep only last 1000 entries
        if (count($existing_log) > 1000) {
            $existing_log = array_slice($existing_log, -1000);
        }
        
        update_option('recruitpro_gdpr_consent_log', $existing_log);
    }

    /**
     * Log privacy request for compliance records
     * 
     * @since 1.0.0
     * @param string $request_type Type of request
     * @param string $email Requester email
     * @param string $name Requester name
     * @return void
     */
    private function log_privacy_request($request_type, $email, $name) {
        
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'request_type' => $request_type,
            'requester_email' => $email,
            'requester_name' => $name,
            'ip_address' => $this->get_user_ip(),
            'status' => 'submitted',
        );

        $existing_log = get_option('recruitpro_gdpr_request_log', array());
        $existing_log[] = $log_entry;
        
        update_option('recruitpro_gdpr_request_log', $existing_log);
    }

    /**
     * Get user IP address (privacy-friendly)
     * 
     * @since 1.0.0
     * @return string Anonymized IP address
     */
    private function get_user_ip() {
        
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Anonymize IP for privacy (remove last octet)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip_parts = explode('.', $ip);
            $ip_parts[3] = '0';
            $ip = implode('.', $ip_parts);
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // For IPv6, anonymize last 64 bits
            $ip = inet_ntop(inet_pton($ip) & pack('a16', str_repeat(chr(255), 8) . str_repeat(chr(0), 8)));
        }
        
        return $ip;
    }

    /**
     * Privacy-friendly optimizations
     * 
     * @since 1.0.0
     * @return void
     */
    private function optimize_for_privacy() {
        
        // Remove WordPress version from RSS feeds
        add_filter('the_generator', '__return_empty_string');
        
        // Disable pingbacks/trackbacks
        add_filter('xmlrpc_enabled', '__return_false');
        
        // Remove unnecessary meta tags
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        
        // Anonymize comment IP addresses
        add_filter('pre_comment_user_ip', array($this, 'anonymize_comment_ip'));
    }

    /**
     * Anonymize comment IP addresses
     * 
     * @since 1.0.0
     * @param string $ip IP address
     * @return string Anonymized IP address
     */
    public function anonymize_comment_ip($ip) {
        return $this->get_user_ip();
    }
}

// Initialize GDPR compliance
new RecruitPro_GDPR_Compliance();

/**
 * Helper function to check if user has given consent
 * 
 * @since 1.0.0
 * @param string $type Consent type to check (analytics, marketing, functional)
 * @return bool Whether user has given consent
 */
function recruitpro_has_gdpr_consent($type = 'all') {
    
    if (!isset($_COOKIE['recruitpro_gdpr_consent'])) {
        return false;
    }

    $consent_data = json_decode($_COOKIE['recruitpro_gdpr_consent'], true);
    
    if (!$consent_data) {
        return false;
    }

    if ($type === 'all') {
        return $consent_data['type'] === 'all';
    }

    if ($consent_data['type'] === 'all') {
        return true;
    }

    if ($consent_data['type'] === 'custom' && isset($consent_data['preferences'])) {
        return in_array($type, $consent_data['preferences']);
    }

    return false;
}

/**
 * Helper function to display GDPR-compliant analytics code
 * 
 * @since 1.0.0
 * @return void
 */
function recruitpro_gdpr_analytics() {
    
    if (!recruitpro_has_gdpr_consent('analytics')) {
        return;
    }

    // Add analytics code here only if consent is given
    // Example: Google Analytics, Facebook Pixel, etc.
}

add_action('wp_footer', 'recruitpro_gdpr_analytics');

?>