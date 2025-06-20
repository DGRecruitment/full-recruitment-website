<?php
/**
 * RecruitPro Theme Contact Form System
 *
 * Basic contact form functionality for recruitment websites
 * Integrates with advanced form plugins when available
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize contact form system
 */
function recruitpro_init_contact_forms() {
    // Setup form handling
    recruitpro_setup_form_handling();
    
    // Add form shortcodes
    recruitpro_register_form_shortcodes();
    
    // Setup form validation
    recruitpro_setup_form_validation();
    
    // Add contact form styles
    add_action('wp_enqueue_scripts', 'recruitpro_contact_form_styles');
    
    // Setup admin interface
    add_action('customize_register', 'recruitpro_contact_form_customizer');
    
    // Add AJAX handling
    add_action('wp_ajax_recruitpro_submit_contact_form', 'recruitpro_handle_contact_form_ajax');
    add_action('wp_ajax_nopriv_recruitpro_submit_contact_form', 'recruitpro_handle_contact_form_ajax');
    
    // Add admin menu for form submissions
    add_action('admin_menu', 'recruitpro_add_contact_forms_admin_menu');
}
add_action('after_setup_theme', 'recruitpro_init_contact_forms');

/**
 * Setup form handling
 */
function recruitpro_setup_form_handling() {
    // Handle form submissions
    add_action('init', 'recruitpro_handle_form_submissions');
    
    // Create database table for form submissions
    add_action('after_switch_theme', 'recruitpro_create_contact_forms_table');
    
    // Setup email notifications
    add_action('recruitpro_form_submitted', 'recruitpro_send_form_notification', 10, 2);
    
    // Setup auto-responder
    add_action('recruitpro_form_submitted', 'recruitpro_send_auto_responder', 10, 2);
}

/**
 * Create contact forms database table
 */
function recruitpro_create_contact_forms_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'recruitpro_contact_forms';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        form_type varchar(50) DEFAULT 'contact',
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20),
        company varchar(100),
        position varchar(100),
        message text,
        form_data longtext,
        ip_address varchar(45),
        user_agent text,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'new',
        admin_notes text,
        PRIMARY KEY (id),
        INDEX form_type_idx (form_type),
        INDEX status_idx (status),
        INDEX submitted_at_idx (submitted_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Handle form submissions
 */
function recruitpro_handle_form_submissions() {
    if (!isset($_POST['recruitpro_form_submit']) || !wp_verify_nonce($_POST['recruitpro_form_nonce'], 'recruitpro_contact_form')) {
        return;
    }
    
    // Validate and sanitize form data
    $form_data = recruitpro_validate_form_data($_POST);
    
    if (is_wp_error($form_data)) {
        // Store error for display
        set_transient('recruitpro_form_error_' . session_id(), $form_data->get_error_message(), 300);
        wp_redirect(add_query_arg('form_error', '1', wp_get_referer()));
        exit;
    }
    
    // Save form submission
    $submission_id = recruitpro_save_form_submission($form_data);
    
    if ($submission_id) {
        // Trigger actions
        do_action('recruitpro_form_submitted', $submission_id, $form_data);
        
        // Redirect with success message
        wp_redirect(add_query_arg('form_success', '1', wp_get_referer()));
        exit;
    } else {
        // Database error
        set_transient('recruitpro_form_error_' . session_id(), esc_html__('Database error. Please try again.', 'recruitpro'), 300);
        wp_redirect(add_query_arg('form_error', '1', wp_get_referer()));
        exit;
    }
}

/**
 * Validate form data
 */
function recruitpro_validate_form_data($data) {
    $errors = new WP_Error();
    
    // Required fields validation
    if (empty($data['contact_name'])) {
        $errors->add('name_required', esc_html__('Name is required.', 'recruitpro'));
    }
    
    if (empty($data['contact_email'])) {
        $errors->add('email_required', esc_html__('Email is required.', 'recruitpro'));
    } elseif (!is_email($data['contact_email'])) {
        $errors->add('email_invalid', esc_html__('Please enter a valid email address.', 'recruitpro'));
    }
    
    if (empty($data['contact_message'])) {
        $errors->add('message_required', esc_html__('Message is required.', 'recruitpro'));
    }
    
    // Spam protection
    if (!empty($data['honeypot'])) {
        $errors->add('spam_detected', esc_html__('Spam detected.', 'recruitpro'));
    }
    
    // Time-based protection
    if (isset($data['form_time'])) {
        $time_elapsed = time() - intval($data['form_time']);
        if ($time_elapsed < 3) {
            $errors->add('form_too_fast', esc_html__('Form submitted too quickly. Please try again.', 'recruitpro'));
        }
    }
    
    // Content validation
    $message = sanitize_textarea_field($data['contact_message']);
    if (strlen($message) < 10) {
        $errors->add('message_too_short', esc_html__('Message must be at least 10 characters long.', 'recruitpro'));
    }
    
    // Check for spam content
    if (recruitpro_is_spam_content($message)) {
        $errors->add('spam_content', esc_html__('Message contains prohibited content.', 'recruitpro'));
    }
    
    if ($errors->has_errors()) {
        return $errors;
    }
    
    // Return sanitized data
    return array(
        'form_type' => sanitize_text_field($data['form_type'] ?? 'contact'),
        'name' => sanitize_text_field($data['contact_name']),
        'email' => sanitize_email($data['contact_email']),
        'phone' => sanitize_text_field($data['contact_phone'] ?? ''),
        'company' => sanitize_text_field($data['contact_company'] ?? ''),
        'position' => sanitize_text_field($data['contact_position'] ?? ''),
        'message' => sanitize_textarea_field($data['contact_message']),
        'form_data' => wp_json_encode($data),
        'ip_address' => recruitpro_get_user_ip(),
        'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
    );
}

/**
 * Check for spam content
 */
function recruitpro_is_spam_content($content) {
    $spam_patterns = array(
        '/\b(viagra|cialis|casino|poker)\b/i',
        '/\b(cheap|discount|free money)\b/i',
        '/https?:\/\/[^\s]+\.(tk|ml|ga|cf)/i',
        '/\b(SEO|backlink|link building)\b/i',
    );
    
    foreach ($spam_patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get user IP address
 */
function recruitpro_get_user_ip() {
    $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR');
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * Save form submission
 */
function recruitpro_save_form_submission($data) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'recruitpro_contact_forms';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'form_type' => $data['form_type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'],
            'position' => $data['position'],
            'message' => $data['message'],
            'form_data' => $data['form_data'],
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    return $result ? $wpdb->insert_id : false;
}

/**
 * Send form notification email
 */
function recruitpro_send_form_notification($submission_id, $form_data) {
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    
    $subject = sprintf(
        esc_html__('[%s] New %s Form Submission', 'recruitpro'),
        $site_name,
        ucfirst($form_data['form_type'])
    );
    
    $message = recruitpro_get_notification_email_template($form_data, $submission_id);
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $form_data['name'] . ' <' . $form_data['email'] . '>',
    );
    
    wp_mail($admin_email, $subject, $message, $headers);
}

/**
 * Get notification email template
 */
function recruitpro_get_notification_email_template($form_data, $submission_id) {
    ob_start();
    ?>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #0073aa; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #0073aa; }
            .footer { background: #f8f9fa; padding: 15px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2><?php echo esc_html(get_bloginfo('name')); ?></h2>
            <p><?php esc_html_e('New Contact Form Submission', 'recruitpro'); ?></p>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="label"><?php esc_html_e('Form Type:', 'recruitpro'); ?></span>
                <?php echo esc_html(ucfirst($form_data['form_type'])); ?>
            </div>
            
            <div class="field">
                <span class="label"><?php esc_html_e('Name:', 'recruitpro'); ?></span>
                <?php echo esc_html($form_data['name']); ?>
            </div>
            
            <div class="field">
                <span class="label"><?php esc_html_e('Email:', 'recruitpro'); ?></span>
                <a href="mailto:<?php echo esc_attr($form_data['email']); ?>"><?php echo esc_html($form_data['email']); ?></a>
            </div>
            
            <?php if (!empty($form_data['phone'])) : ?>
            <div class="field">
                <span class="label"><?php esc_html_e('Phone:', 'recruitpro'); ?></span>
                <?php echo esc_html($form_data['phone']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($form_data['company'])) : ?>
            <div class="field">
                <span class="label"><?php esc_html_e('Company:', 'recruitpro'); ?></span>
                <?php echo esc_html($form_data['company']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($form_data['position'])) : ?>
            <div class="field">
                <span class="label"><?php esc_html_e('Position:', 'recruitpro'); ?></span>
                <?php echo esc_html($form_data['position']); ?>
            </div>
            <?php endif; ?>
            
            <div class="field">
                <span class="label"><?php esc_html_e('Message:', 'recruitpro'); ?></span><br>
                <?php echo nl2br(esc_html($form_data['message'])); ?>
            </div>
        </div>
        
        <div class="footer">
            <p>
                <?php esc_html_e('Submission ID:', 'recruitpro'); ?> #<?php echo esc_html($submission_id); ?><br>
                <?php esc_html_e('Submitted:', 'recruitpro'); ?> <?php echo esc_html(current_time('Y-m-d H:i:s')); ?><br>
                <?php esc_html_e('IP Address:', 'recruitpro'); ?> <?php echo esc_html($form_data['ip_address']); ?>
            </p>
            <p>
                <a href="<?php echo esc_url(admin_url('admin.php?page=recruitpro-contact-forms&view=' . $submission_id)); ?>">
                    <?php esc_html_e('View in Admin', 'recruitpro'); ?>
                </a>
            </p>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Send auto-responder email
 */
function recruitpro_send_auto_responder($submission_id, $form_data) {
    if (!get_theme_mod('recruitpro_enable_auto_responder', true)) {
        return;
    }
    
    $site_name = get_bloginfo('name');
    $subject = sprintf(
        esc_html__('Thank you for contacting %s', 'recruitpro'),
        $site_name
    );
    
    $message = recruitpro_get_auto_responder_template($form_data);
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $site_name . ' <' . get_option('admin_email') . '>',
    );
    
    wp_mail($form_data['email'], $subject, $message, $headers);
}

/**
 * Get auto-responder email template
 */
function recruitpro_get_auto_responder_template($form_data) {
    $custom_message = get_theme_mod('recruitpro_auto_responder_message', '');
    
    if (empty($custom_message)) {
        $custom_message = sprintf(
            esc_html__('Thank you for contacting us, %s. We have received your message and will get back to you within 24 hours. Our team is committed to providing excellent service and will address your inquiry promptly.', 'recruitpro'),
            esc_html($form_data['name'])
        );
    } else {
        $custom_message = str_replace('{name}', esc_html($form_data['name']), $custom_message);
    }
    
    ob_start();
    ?>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #0073aa; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <h2><?php echo esc_html(get_bloginfo('name')); ?></h2>
        </div>
        
        <div class="content">
            <p><?php echo wp_kses_post($custom_message); ?></p>
            
            <p><?php esc_html_e('For reference, here is a summary of your submission:', 'recruitpro'); ?></p>
            
            <ul>
                <li><strong><?php esc_html_e('Name:', 'recruitpro'); ?></strong> <?php echo esc_html($form_data['name']); ?></li>
                <li><strong><?php esc_html_e('Email:', 'recruitpro'); ?></strong> <?php echo esc_html($form_data['email']); ?></li>
                <?php if (!empty($form_data['company'])) : ?>
                <li><strong><?php esc_html_e('Company:', 'recruitpro'); ?></strong> <?php echo esc_html($form_data['company']); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="footer">
            <p><?php echo esc_html(get_bloginfo('name')); ?> | <?php echo esc_html(home_url()); ?></p>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Register form shortcodes
 */
function recruitpro_register_form_shortcodes() {
    add_shortcode('recruitpro_contact_form', 'recruitpro_contact_form_shortcode');
    add_shortcode('recruitpro_job_inquiry_form', 'recruitpro_job_inquiry_form_shortcode');
    add_shortcode('recruitpro_client_form', 'recruitpro_client_form_shortcode');
}

/**
 * Contact form shortcode
 */
function recruitpro_contact_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => esc_html__('Contact Us', 'recruitpro'),
        'type' => 'contact',
        'show_company' => 'true',
        'show_phone' => 'true',
    ), $atts, 'recruitpro_contact_form');
    
    return recruitpro_get_contact_form_html($atts);
}

/**
 * Job inquiry form shortcode
 */
function recruitpro_job_inquiry_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => esc_html__('Job Inquiry', 'recruitpro'),
        'type' => 'job_inquiry',
        'show_company' => 'false',
        'show_phone' => 'true',
        'show_position' => 'true',
    ), $atts, 'recruitpro_job_inquiry_form');
    
    return recruitpro_get_contact_form_html($atts);
}

/**
 * Client form shortcode
 */
function recruitpro_client_form_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => esc_html__('Client Inquiry', 'recruitpro'),
        'type' => 'client_inquiry',
        'show_company' => 'true',
        'show_phone' => 'true',
        'show_position' => 'true',
    ), $atts, 'recruitpro_client_form');
    
    return recruitpro_get_contact_form_html($atts);
}

/**
 * Get contact form HTML
 */
function recruitpro_get_contact_form_html($atts) {
    // Check if advanced form plugin is available
    if (recruitpro_has_advanced_form_plugin()) {
        return recruitpro_get_advanced_form_fallback($atts);
    }
    
    // Show success/error messages
    $messages = recruitpro_get_form_messages();
    
    ob_start();
    ?>
    <div class="recruitpro-contact-form-wrapper">
        <?php if ($messages) : ?>
            <div class="form-messages">
                <?php echo $messages; ?>
            </div>
        <?php endif; ?>
        
        <div class="recruitpro-contact-form">
            <?php if (!empty($atts['title'])) : ?>
                <h3 class="form-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <form method="post" action="" class="contact-form" id="recruitpro-contact-form">
                <?php wp_nonce_field('recruitpro_contact_form', 'recruitpro_form_nonce'); ?>
                <input type="hidden" name="form_type" value="<?php echo esc_attr($atts['type']); ?>">
                <input type="hidden" name="form_time" value="<?php echo time(); ?>">
                
                <!-- Honeypot field -->
                <div style="position: absolute; left: -9999px;">
                    <label for="honeypot"><?php esc_html_e('Leave this field empty', 'recruitpro'); ?></label>
                    <input type="text" name="honeypot" id="honeypot" value="" autocomplete="off" tabindex="-1">
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <label for="contact_name"><?php esc_html_e('Full Name', 'recruitpro'); ?> <span class="required">*</span></label>
                        <input type="text" name="contact_name" id="contact_name" required>
                    </div>
                    
                    <div class="form-field">
                        <label for="contact_email"><?php esc_html_e('Email Address', 'recruitpro'); ?> <span class="required">*</span></label>
                        <input type="email" name="contact_email" id="contact_email" required>
                    </div>
                </div>
                
                <?php if ($atts['show_phone'] === 'true') : ?>
                <div class="form-row">
                    <div class="form-field">
                        <label for="contact_phone"><?php esc_html_e('Phone Number', 'recruitpro'); ?></label>
                        <input type="tel" name="contact_phone" id="contact_phone">
                    </div>
                    
                    <?php if ($atts['show_company'] === 'true') : ?>
                    <div class="form-field">
                        <label for="contact_company"><?php esc_html_e('Company', 'recruitpro'); ?></label>
                        <input type="text" name="contact_company" id="contact_company">
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($atts['show_position'] === 'true') : ?>
                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="contact_position"><?php esc_html_e('Position/Role', 'recruitpro'); ?></label>
                        <input type="text" name="contact_position" id="contact_position" placeholder="<?php esc_attr_e('e.g., Software Developer, HR Manager', 'recruitpro'); ?>">
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-field full-width">
                        <label for="contact_message"><?php esc_html_e('Message', 'recruitpro'); ?> <span class="required">*</span></label>
                        <textarea name="contact_message" id="contact_message" rows="6" required placeholder="<?php esc_attr_e('Please describe your inquiry...', 'recruitpro'); ?>"></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-field full-width">
                        <button type="submit" name="recruitpro_form_submit" class="submit-button recruitpro-button">
                            <?php esc_html_e('Send Message', 'recruitpro'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Check if advanced form plugin is available
 */
function recruitpro_has_advanced_form_plugin() {
    return class_exists('RecruitPro_Forms') || defined('WPCF7_VERSION') || class_exists('GFForms');
}

/**
 * Get advanced form fallback
 */
function recruitpro_get_advanced_form_fallback($atts) {
    if (class_exists('RecruitPro_Forms')) {
        return do_shortcode('[recruitpro_form type="' . esc_attr($atts['type']) . '"]');
    }
    
    // Fallback message
    return '<div class="form-notice">' . 
           esc_html__('Advanced form functionality is available with the RecruitPro Forms plugin.', 'recruitpro') . 
           '</div>';
}

/**
 * Get form messages
 */
function recruitpro_get_form_messages() {
    $messages = '';
    
    if (isset($_GET['form_success'])) {
        $messages .= '<div class="form-message success">' . 
                    esc_html__('Thank you! Your message has been sent successfully. We will get back to you soon.', 'recruitpro') . 
                    '</div>';
    }
    
    if (isset($_GET['form_error'])) {
        $error_message = get_transient('recruitpro_form_error_' . session_id());
        if ($error_message) {
            $messages .= '<div class="form-message error">' . esc_html($error_message) . '</div>';
            delete_transient('recruitpro_form_error_' . session_id());
        } else {
            $messages .= '<div class="form-message error">' . 
                        esc_html__('There was an error submitting your form. Please try again.', 'recruitpro') . 
                        '</div>';
        }
    }
    
    return $messages;
}

/**
 * Setup form validation
 */
function recruitpro_setup_form_validation() {
    // Add JavaScript validation
    add_action('wp_footer', 'recruitpro_contact_form_validation_script');
}

/**
 * Contact form validation script
 */
function recruitpro_contact_form_validation_script() {
    if (!wp_script_is('recruitpro-contact-form', 'enqueued')) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('recruitpro-contact-form');
        if (!form) return;
        
        // Real-time validation
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(function(input) {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showFormError('<?php esc_js_e('Please fill in all required fields correctly.', 'recruitpro'); ?>');
            }
        });
        
        function validateField(field) {
            const value = field.value.trim();
            let isValid = true;
            
            // Required field check
            if (field.hasAttribute('required') && !value) {
                showFieldError(field, '<?php esc_js_e('This field is required.', 'recruitpro'); ?>');
                isValid = false;
            }
            
            // Email validation
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showFieldError(field, '<?php esc_js_e('Please enter a valid email address.', 'recruitpro'); ?>');
                    isValid = false;
                }
            }
            
            // Message length validation
            if (field.name === 'contact_message' && value && value.length < 10) {
                showFieldError(field, '<?php esc_js_e('Message must be at least 10 characters long.', 'recruitpro'); ?>');
                isValid = false;
            }
            
            if (isValid) {
                clearFieldError(field);
            }
            
            return isValid;
        }
        
        function showFieldError(field, message) {
            clearFieldError(field);
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            
            field.parentNode.appendChild(errorDiv);
            field.classList.add('error');
        }
        
        function clearFieldError(field) {
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            field.classList.remove('error');
        }
        
        function showFormError(message) {
            let errorContainer = form.querySelector('.form-error');
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'form-error';
                form.insertBefore(errorContainer, form.firstChild);
            }
            errorContainer.textContent = message;
            errorContainer.scrollIntoView({behavior: 'smooth'});
        }
    });
    </script>
    <?php
}

/**
 * Handle AJAX form submission
 */
function recruitpro_handle_contact_form_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_contact_form_ajax')) {
        wp_send_json_error(array('message' => esc_html__('Security check failed.', 'recruitpro')));
    }
    
    // Validate form data
    $form_data = recruitpro_validate_form_data($_POST);
    
    if (is_wp_error($form_data)) {
        wp_send_json_error(array('message' => $form_data->get_error_message()));
    }
    
    // Save form submission
    $submission_id = recruitpro_save_form_submission($form_data);
    
    if ($submission_id) {
        // Trigger actions
        do_action('recruitpro_form_submitted', $submission_id, $form_data);
        
        wp_send_json_success(array(
            'message' => esc_html__('Thank you! Your message has been sent successfully.', 'recruitpro'),
            'submission_id' => $submission_id
        ));
    } else {
        wp_send_json_error(array('message' => esc_html__('Database error. Please try again.', 'recruitpro')));
    }
}

/**
 * Contact form styles
 */
function recruitpro_contact_form_styles() {
    wp_enqueue_style('recruitpro-contact-form', get_template_directory_uri() . '/assets/css/contact-form.css', array('recruitpro-style'), wp_get_theme()->get('Version'));
    wp_enqueue_script('recruitpro-contact-form', get_template_directory_uri() . '/assets/js/contact-form.js', array('jquery'), wp_get_theme()->get('Version'), true);
    
    // Localize script for AJAX
    wp_localize_script('recruitpro-contact-form', 'recruitproContactForm', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('recruitpro_contact_form_ajax'),
        'messages' => array(
            'sending' => esc_html__('Sending...', 'recruitpro'),
            'success' => esc_html__('Message sent successfully!', 'recruitpro'),
            'error' => esc_html__('Error sending message. Please try again.', 'recruitpro'),
        )
    ));
}

/**
 * Add contact form customizer settings
 */
function recruitpro_contact_form_customizer($wp_customize) {
    // Contact Forms section
    $wp_customize->add_section('recruitpro_contact_forms', array(
        'title'    => esc_html__('Contact Forms', 'recruitpro'),
        'priority' => 140,
    ));
    
    // Enable auto-responder
    $wp_customize->add_setting('recruitpro_enable_auto_responder', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_enable_auto_responder', array(
        'label'   => esc_html__('Enable Auto-Responder', 'recruitpro'),
        'section' => 'recruitpro_contact_forms',
        'type'    => 'checkbox',
    ));
    
    // Auto-responder message
    $wp_customize->add_setting('recruitpro_auto_responder_message', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('recruitpro_auto_responder_message', array(
        'label'       => esc_html__('Auto-Responder Message', 'recruitpro'),
        'section'     => 'recruitpro_contact_forms',
        'type'        => 'textarea',
        'description' => esc_html__('Use {name} to include the sender\'s name. Leave empty for default message.', 'recruitpro'),
    ));
    
    // Notification email
    $wp_customize->add_setting('recruitpro_notification_email', array(
        'default'           => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('recruitpro_notification_email', array(
        'label'   => esc_html__('Notification Email', 'recruitpro'),
        'section' => 'recruitpro_contact_forms',
        'type'    => 'email',
    ));
}

/**
 * Add admin menu for contact forms
 */
function recruitpro_add_contact_forms_admin_menu() {
    add_menu_page(
        esc_html__('Contact Forms', 'recruitpro'),
        esc_html__('Contact Forms', 'recruitpro'),
        'manage_options',
        'recruitpro-contact-forms',
        'recruitpro_contact_forms_admin_page',
        'dashicons-email-alt',
        30
    );
}

/**
 * Contact forms admin page
 */
function recruitpro_contact_forms_admin_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'recruitpro_contact_forms';
    
    // Handle actions
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $action = sanitize_text_field($_GET['action']);
        $id = intval($_GET['id']);
        
        if ($action === 'delete' && wp_verify_nonce($_GET['_wpnonce'], 'delete_form_' . $id)) {
            $wpdb->delete($table_name, array('id' => $id), array('%d'));
            echo '<div class="notice notice-success"><p>' . esc_html__('Form submission deleted.', 'recruitpro') . '</p></div>';
        }
    }
    
    // Get submissions
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;
    
    $submissions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));
    
    $total_submissions = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_submissions / $per_page);
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Contact Form Submissions', 'recruitpro'); ?></h1>
        
        <div class="tablenav top">
            <div class="alignleft actions">
                <span class="displaying-num">
                    <?php printf(_n('%s item', '%s items', $total_submissions, 'recruitpro'), number_format_i18n($total_submissions)); ?>
                </span>
            </div>
            <?php if ($total_pages > 1) : ?>
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $page
                    ));
                    ?>
                </div>
            <?php endif; ?>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Date', 'recruitpro'); ?></th>
                    <th><?php esc_html_e('Type', 'recruitpro'); ?></th>
                    <th><?php esc_html_e('Name', 'recruitpro'); ?></th>
                    <th><?php esc_html_e('Email', 'recruitpro'); ?></th>
                    <th><?php esc_html_e('Company', 'recruitpro'); ?></th>
                    <th><?php esc_html_e('Message', 'recruitpro'); ?></th>
                    <th><?php esc_html_e('Actions', 'recruitpro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($submissions) : ?>
                    <?php foreach ($submissions as $submission) : ?>
                        <tr>
                            <td><?php echo esc_html(mysql2date('Y-m-d H:i', $submission->submitted_at)); ?></td>
                            <td><?php echo esc_html(ucfirst($submission->form_type)); ?></td>
                            <td><?php echo esc_html($submission->name); ?></td>
                            <td><a href="mailto:<?php echo esc_attr($submission->email); ?>"><?php echo esc_html($submission->email); ?></a></td>
                            <td><?php echo esc_html($submission->company ?: '—'); ?></td>
                            <td><?php echo esc_html(wp_trim_words($submission->message, 10)); ?></td>
                            <td>
                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'delete', 'id' => $submission->id)), 'delete_form_' . $submission->id)); ?>" 
                                   onclick="return confirm('<?php esc_js_e('Are you sure you want to delete this submission?', 'recruitpro'); ?>')" 
                                   class="button button-small">
                                    <?php esc_html_e('Delete', 'recruitpro'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7"><?php esc_html_e('No form submissions found.', 'recruitpro'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Template function to display contact form
 */
function recruitpro_contact_form($args = array()) {
    $defaults = array(
        'title' => esc_html__('Contact Us', 'recruitpro'),
        'type' => 'contact',
        'show_company' => 'true',
        'show_phone' => 'true',
    );
    
    $args = wp_parse_args($args, $defaults);
    echo recruitpro_get_contact_form_html($args);
}

/**
 * Template function to check if forms are available
 */
function recruitpro_has_contact_forms() {
    return true; // Theme always provides basic contact forms
}

/**
 * Get form submission count
 */
function recruitpro_get_form_submission_count($form_type = '') {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'recruitpro_contact_forms';
    
    if ($form_type) {
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE form_type = %s",
            $form_type
        ));
    }
    
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
}