<?php
/**
 * RecruitPro Theme Comment System
 *
 * Enhanced comment system with recruitment-focused features
 * Includes spam protection, moderation, and professional styling
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize comment system
 */
function recruitpro_init_comment_system() {
    // Setup comment configuration
    recruitpro_configure_comment_settings();
    
    // Enhanced comment form
    add_filter('comment_form_defaults', 'recruitpro_customize_comment_form');
    add_filter('comment_form_field_comment', 'recruitpro_customize_comment_field');
    
    // Comment display customization
    add_filter('wp_list_comments_args', 'recruitpro_customize_comment_list');
    
    // Security and spam protection
    recruitpro_setup_comment_security();
    
    // Admin enhancements
    add_action('admin_init', 'recruitpro_setup_comment_admin');
    
    // Professional comment styling
    add_action('wp_head', 'recruitpro_comment_styles');
}
add_action('after_setup_theme', 'recruitpro_init_comment_system');

/**
 * Configure comment settings
 */
function recruitpro_configure_comment_settings() {
    // Disable comments on specific post types by default
    add_action('init', 'recruitpro_disable_comments_on_post_types');
    
    // Customize comment status for recruitment content
    add_filter('comments_open', 'recruitpro_control_comments_by_post_type', 10, 2);
    
    // Enable threaded comments
    if (!is_admin()) {
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}

/**
 * Disable comments on specific post types
 */
function recruitpro_disable_comments_on_post_types() {
    // Post types where comments should be disabled by default
    $no_comment_types = array(
        'page',           // Most pages don't need comments
        'job',            // Job listings use application forms, not comments
        'testimonial',    // Testimonials are curated content
        'team_member',    // Team member profiles don't need comments
    );
    
    // Allow customization via theme settings
    $no_comment_types = apply_filters('recruitpro_no_comment_post_types', $no_comment_types);
    
    foreach ($no_comment_types as $post_type) {
        // Remove comment support from post type
        if (post_type_exists($post_type)) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}

/**
 * Control comments by post type
 */
function recruitpro_control_comments_by_post_type($open, $post_id) {
    $post = get_post($post_id);
    
    if (!$post) {
        return $open;
    }
    
    // Enable comments only for blog posts by default
    $comment_enabled_types = array('post');
    
    // Allow customization
    $comment_enabled_types = apply_filters('recruitpro_comment_enabled_post_types', $comment_enabled_types);
    
    // Check if current post type allows comments
    if (!in_array($post->post_type, $comment_enabled_types)) {
        return false;
    }
    
    return $open;
}

/**
 * Customize comment form
 */
function recruitpro_customize_comment_form($defaults) {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true' required" : '');
    
    $defaults['title_reply'] = esc_html__('Leave a Professional Comment', 'recruitpro');
    $defaults['title_reply_to'] = esc_html__('Reply to %s', 'recruitpro');
    $defaults['cancel_reply_link'] = esc_html__('Cancel reply', 'recruitpro');
    $defaults['label_submit'] = esc_html__('Post Comment', 'recruitpro');
    $defaults['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s recruitpro-button" value="%4$s" />';
    $defaults['submit_field'] = '<p class="form-submit">%1$s %2$s</p>';
    
    $defaults['comment_notes_before'] = '<p class="comment-notes">' . 
        esc_html__('Your email address will not be published. Required fields are marked *', 'recruitpro') . 
        '</p>';
    
    $defaults['comment_notes_after'] = '<p class="comment-policy">' . 
        esc_html__('Please keep comments professional and relevant to the recruitment industry.', 'recruitpro') . 
        '</p>';
    
    // Custom fields
    $defaults['fields'] = array(
        'author' => '<p class="comment-form-author">' .
                   '<label for="author">' . esc_html__('Name', 'recruitpro') . ($req ? ' <span class="required">*</span>' : '') . '</label>' .
                   '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
        
        'email' => '<p class="comment-form-email">' .
                  '<label for="email">' . esc_html__('Email', 'recruitpro') . ($req ? ' <span class="required">*</span>' : '') . '</label>' .
                  '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
        
        'url' => '<p class="comment-form-url">' .
                '<label for="url">' . esc_html__('Website', 'recruitpro') . '</label>' .
                '<input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
        
        'cookies' => '<p class="comment-form-cookies-consent">' .
                    '<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . (empty($commenter['comment_author_email']) ? '' : ' checked="checked"') . ' />' .
                    '<label for="wp-comment-cookies-consent">' . esc_html__('Save my name, email, and website in this browser for the next time I comment.', 'recruitpro') . '</label></p>',
    );
    
    // Add professional context field
    if (get_theme_mod('recruitpro_comment_professional_context', true)) {
        $defaults['fields']['professional_context'] = '<p class="comment-form-professional-context">' .
            '<label for="professional_context">' . esc_html__('Professional Context (Optional)', 'recruitpro') . '</label>' .
            '<select id="professional_context" name="professional_context">' .
            '<option value="">' . esc_html__('Select your role...', 'recruitpro') . '</option>' .
            '<option value="hr_professional">' . esc_html__('HR Professional', 'recruitpro') . '</option>' .
            '<option value="recruiter">' . esc_html__('Recruiter', 'recruitpro') . '</option>' .
            '<option value="job_seeker">' . esc_html__('Job Seeker', 'recruitpro') . '</option>' .
            '<option value="employer">' . esc_html__('Employer', 'recruitpro') . '</option>' .
            '<option value="consultant">' . esc_html__('Consultant', 'recruitpro') . '</option>' .
            '<option value="other">' . esc_html__('Other', 'recruitpro') . '</option>' .
            '</select></p>';
    }
    
    return $defaults;
}

/**
 * Customize comment textarea field
 */
function recruitpro_customize_comment_field($field) {
    $field = '<p class="comment-form-comment">' .
             '<label for="comment">' . esc_html__('Comment', 'recruitpro') . ' <span class="required">*</span></label>' .
             '<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required placeholder="' . 
             esc_attr__('Share your professional insights...', 'recruitpro') . '"></textarea></p>';
    
    return $field;
}

/**
 * Customize comment list display
 */
function recruitpro_customize_comment_list($args) {
    $args['avatar_size'] = 60;
    $args['style'] = 'ol';
    $args['short_ping'] = true;
    $args['reply_text'] = esc_html__('Reply', 'recruitpro');
    $args['callback'] = 'recruitpro_custom_comment_callback';
    
    return $args;
}

/**
 * Custom comment callback function
 */
function recruitpro_custom_comment_callback($comment, $args, $depth) {
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    
    $commenter = wp_get_current_commenter();
    $show_pending_links = !empty($commenter['comment_author']);
    
    if ($commenter['comment_author_email']) {
        $moderation_note = esc_html__('Your comment is awaiting moderation.', 'recruitpro');
    } else {
        $moderation_note = esc_html__('Your comment is awaiting moderation. This is a preview, your comment will be visible after it has been approved.', 'recruitpro');
    }
    ?>
    
    <<?php echo $tag; ?> <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?> id="comment-<?php comment_ID(); ?>">
        <?php if ('div' != $args['style']) : ?>
            <div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
        <?php endif; ?>
        
        <div class="comment-content-wrapper">
            <div class="comment-author vcard">
                <?php if ($args['avatar_size'] != 0) : ?>
                    <div class="comment-avatar">
                        <?php echo get_avatar($comment, $args['avatar_size']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="comment-meta commentmetadata">
                    <div class="comment-author-name">
                        <?php
                        $author_name = get_comment_author_link();
                        if (!empty($author_name)) {
                            printf('<cite class="fn">%s</cite>', $author_name);
                        }
                        
                        // Display professional context if available
                        $professional_context = get_comment_meta(get_comment_ID(), 'professional_context', true);
                        if (!empty($professional_context)) {
                            $context_labels = array(
                                'hr_professional' => esc_html__('HR Professional', 'recruitpro'),
                                'recruiter' => esc_html__('Recruiter', 'recruitpro'),
                                'job_seeker' => esc_html__('Job Seeker', 'recruitpro'),
                                'employer' => esc_html__('Employer', 'recruitpro'),
                                'consultant' => esc_html__('Consultant', 'recruitpro'),
                                'other' => esc_html__('Industry Professional', 'recruitpro'),
                            );
                            
                            if (isset($context_labels[$professional_context])) {
                                echo '<span class="professional-context">' . esc_html($context_labels[$professional_context]) . '</span>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="comment-date">
                        <a href="<?php echo esc_url(get_comment_link(get_comment_ID())); ?>">
                            <?php
                            printf(
                                esc_html__('%1$s at %2$s', 'recruitpro'),
                                get_comment_date(),
                                get_comment_time()
                            );
                            ?>
                        </a>
                        <?php edit_comment_link(esc_html__('(Edit)', 'recruitpro'), '&nbsp;&nbsp;', ''); ?>
                    </div>
                </div>
            </div>
            
            <?php if ($comment->comment_approved == '0') : ?>
                <em class="comment-awaiting-moderation"><?php echo $moderation_note; ?></em>
                <br />
            <?php endif; ?>
            
            <div class="comment-text">
                <?php comment_text(); ?>
            </div>
            
            <div class="comment-reply">
                <?php
                comment_reply_link(array_merge($args, array(
                    'add_below' => $add_below,
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                )));
                ?>
            </div>
        </div>
        
        <?php if ('div' != $args['style']) : ?>
            </div>
        <?php endif; ?>
    <?php
}

/**
 * Setup comment security and spam protection
 */
function recruitpro_setup_comment_security() {
    // Add honeypot field for spam protection
    add_action('comment_form_after_fields', 'recruitpro_add_honeypot_field');
    add_action('comment_form_logged_in_after', 'recruitpro_add_honeypot_field');
    
    // Validate honeypot and other security measures
    add_filter('preprocess_comment', 'recruitpro_validate_comment_security');
    
    // Enhanced comment moderation
    add_filter('pre_comment_approved', 'recruitpro_enhanced_comment_moderation', 10, 2);
    
    // Save professional context
    add_action('comment_post', 'recruitpro_save_comment_meta');
    
    // Add admin column for professional context
    add_filter('manage_edit-comments_columns', 'recruitpro_add_comment_columns');
    add_action('manage_comments_custom_column', 'recruitpro_display_comment_columns', 10, 2);
}

/**
 * Add honeypot field for spam protection
 */
function recruitpro_add_honeypot_field() {
    ?>
    <p style="display: none !important;">
        <label for="recruitpro_honeypot"><?php esc_html_e('Leave this field empty', 'recruitpro'); ?></label>
        <input type="text" name="recruitpro_honeypot" id="recruitpro_honeypot" value="" autocomplete="off" tabindex="-1">
    </p>
    
    <input type="hidden" name="recruitpro_comment_nonce" value="<?php echo wp_create_nonce('recruitpro_comment_nonce'); ?>">
    <input type="hidden" name="recruitpro_comment_time" value="<?php echo time(); ?>">
    <?php
}

/**
 * Validate comment security
 */
function recruitpro_validate_comment_security($commentdata) {
    // Check honeypot
    if (!empty($_POST['recruitpro_honeypot'])) {
        wp_die(esc_html__('Spam detected. Comment not allowed.', 'recruitpro'));
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['recruitpro_comment_nonce'], 'recruitpro_comment_nonce')) {
        wp_die(esc_html__('Security check failed. Please try again.', 'recruitpro'));
    }
    
    // Check if comment was submitted too quickly (less than 5 seconds)
    if (isset($_POST['recruitpro_comment_time'])) {
        $time_elapsed = time() - intval($_POST['recruitpro_comment_time']);
        if ($time_elapsed < 5) {
            wp_die(esc_html__('Comment submitted too quickly. Please wait a moment and try again.', 'recruitpro'));
        }
    }
    
    // Enhanced content filtering for recruitment context
    $comment_content = $commentdata['comment_content'];
    
    // Check for common spam patterns
    $spam_patterns = array(
        '/\b(viagra|cialis|casino|poker|loan|credit)\b/i',
        '/\b(SEO|backlink|link building)\b/i',
        '/https?:\/\/[^\s]+\.(tk|ml|ga|cf)/i', // Suspicious domains
    );
    
    foreach ($spam_patterns as $pattern) {
        if (preg_match($pattern, $comment_content)) {
            wp_die(esc_html__('Comment contains prohibited content.', 'recruitpro'));
        }
    }
    
    return $commentdata;
}

/**
 * Enhanced comment moderation
 */
function recruitpro_enhanced_comment_moderation($approved, $commentdata) {
    // Auto-approve comments from known professional email domains
    $professional_domains = array(
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
        'company.com', 'organization.org', // Add more as needed
    );
    
    $email_domain = strtolower(substr(strrchr($commentdata['comment_author_email'], "@"), 1));
    
    // Check if it's from a professional domain
    if (in_array($email_domain, $professional_domains)) {
        // Additional checks for professional content
        $content = strtolower($commentdata['comment_content']);
        $professional_keywords = array(
            'recruitment', 'hiring', 'career', 'interview', 'resume', 'cv',
            'job', 'position', 'role', 'opportunity', 'candidate', 'talent',
            'hr', 'human resources', 'staffing', 'employment'
        );
        
        foreach ($professional_keywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return 1; // Auto-approve
            }
        }
    }
    
    return $approved;
}

/**
 * Save comment meta data
 */
function recruitpro_save_comment_meta($comment_id) {
    if (isset($_POST['professional_context']) && !empty($_POST['professional_context'])) {
        $context = sanitize_text_field($_POST['professional_context']);
        $allowed_contexts = array('hr_professional', 'recruiter', 'job_seeker', 'employer', 'consultant', 'other');
        
        if (in_array($context, $allowed_contexts)) {
            add_comment_meta($comment_id, 'professional_context', $context);
        }
    }
}

/**
 * Add custom columns to comments admin
 */
function recruitpro_add_comment_columns($columns) {
    $columns['professional_context'] = esc_html__('Professional Context', 'recruitpro');
    return $columns;
}

/**
 * Display custom comment columns
 */
function recruitpro_display_comment_columns($column, $comment_id) {
    if ($column === 'professional_context') {
        $context = get_comment_meta($comment_id, 'professional_context', true);
        if (!empty($context)) {
            $context_labels = array(
                'hr_professional' => esc_html__('HR Professional', 'recruitpro'),
                'recruiter' => esc_html__('Recruiter', 'recruitpro'),
                'job_seeker' => esc_html__('Job Seeker', 'recruitpro'),
                'employer' => esc_html__('Employer', 'recruitpro'),
                'consultant' => esc_html__('Consultant', 'recruitpro'),
                'other' => esc_html__('Other', 'recruitpro'),
            );
            
            echo isset($context_labels[$context]) ? esc_html($context_labels[$context]) : esc_html__('Unknown', 'recruitpro');
        } else {
            echo '—';
        }
    }
}

/**
 * Setup comment admin enhancements
 */
function recruitpro_setup_comment_admin() {
    // Add comment settings to customizer
    add_action('customize_register', 'recruitpro_comment_customizer_settings');
    
    // Admin notice about comment settings
    add_action('admin_notices', 'recruitpro_comment_admin_notice');
}

/**
 * Add comment settings to customizer
 */
function recruitpro_comment_customizer_settings($wp_customize) {
    // Comments section
    $wp_customize->add_section('recruitpro_comments', array(
        'title'    => esc_html__('Comments & Discussion', 'recruitpro'),
        'priority' => 130,
    ));
    
    // Enable professional context field
    $wp_customize->add_setting('recruitpro_comment_professional_context', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('recruitpro_comment_professional_context', array(
        'label'   => esc_html__('Show Professional Context Field', 'recruitpro'),
        'section' => 'recruitpro_comments',
        'type'    => 'checkbox',
    ));
    
    // Comment moderation level
    $wp_customize->add_setting('recruitpro_comment_moderation_level', array(
        'default'           => 'moderate',
        'sanitize_callback' => 'recruitpro_sanitize_moderation_level',
    ));
    
    $wp_customize->add_control('recruitpro_comment_moderation_level', array(
        'label'   => esc_html__('Comment Moderation Level', 'recruitpro'),
        'section' => 'recruitpro_comments',
        'type'    => 'select',
        'choices' => array(
            'strict'   => esc_html__('Strict (All comments moderated)', 'recruitpro'),
            'moderate' => esc_html__('Moderate (Auto-approve professional)', 'recruitpro'),
            'lenient'  => esc_html__('Lenient (Auto-approve most)', 'recruitpro'),
        ),
    ));
}

/**
 * Sanitize moderation level
 */
function recruitpro_sanitize_moderation_level($input) {
    return in_array($input, array('strict', 'moderate', 'lenient')) ? $input : 'moderate';
}

/**
 * Comment admin notice
 */
function recruitpro_comment_admin_notice() {
    $screen = get_current_screen();
    
    if ($screen && $screen->id === 'edit-comments' && !get_option('default_ping_status') && !get_option('default_comment_status')) {
        ?>
        <div class="notice notice-info">
            <p>
                <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
                <?php esc_html_e('Comments are currently disabled site-wide. You can enable them for blog posts in Settings > Discussion.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
    }
}

/**
 * Add professional comment styles
 */
function recruitpro_comment_styles() {
    if (!is_singular() || !comments_open()) {
        return;
    }
    ?>
    <style>
    /* RecruitPro Comment Styles */
    .comments-area {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 2px solid var(--recruitpro-border, #e1e1e1);
    }
    
    .comments-title {
        color: var(--recruitpro-primary, #0073aa);
        margin-bottom: 30px;
        font-size: 24px;
    }
    
    .comment-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .comment {
        margin-bottom: 30px;
        padding: 20px;
        background: var(--recruitpro-background-alt, #f8f9fa);
        border: 1px solid var(--recruitpro-border, #e1e1e1);
        border-radius: 8px;
    }
    
    .comment .children {
        margin-top: 20px;
        margin-left: 40px;
    }
    
    .comment-content-wrapper {
        display: flex;
        gap: 15px;
    }
    
    .comment-avatar img {
        border-radius: 50%;
        border: 2px solid var(--recruitpro-border, #e1e1e1);
    }
    
    .comment-meta {
        flex: 1;
    }
    
    .comment-author-name {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .comment-author-name cite {
        font-style: normal;
        color: var(--recruitpro-primary, #0073aa);
    }
    
    .professional-context {
        display: inline-block;
        background: var(--recruitpro-primary, #0073aa);
        color: white;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: 10px;
        font-weight: normal;
    }
    
    .comment-date {
        font-size: 12px;
        color: var(--recruitpro-text-secondary, #666);
    }
    
    .comment-date a {
        color: inherit;
        text-decoration: none;
    }
    
    .comment-date a:hover {
        color: var(--recruitpro-primary, #0073aa);
    }
    
    .comment-text {
        margin-top: 15px;
        line-height: 1.6;
    }
    
    .comment-reply {
        margin-top: 15px;
    }
    
    .comment-reply-link {
        background: var(--recruitpro-primary, #0073aa);
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        display: inline-block;
    }
    
    .comment-reply-link:hover {
        background: var(--recruitpro-primary-hover, #005177);
        color: white;
    }
    
    .comment-awaiting-moderation {
        background: var(--recruitpro-warning, #ffc107);
        color: #000;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    /* Comment Form */
    .comment-respond {
        margin-top: 40px;
        padding: 30px;
        background: var(--recruitpro-background, #fff);
        border: 1px solid var(--recruitpro-border, #e1e1e1);
        border-radius: 8px;
    }
    
    .comment-reply-title {
        color: var(--recruitpro-primary, #0073aa);
        margin-bottom: 20px;
    }
    
    .comment-form-author,
    .comment-form-email,
    .comment-form-url,
    .comment-form-professional-context,
    .comment-form-comment {
        margin-bottom: 20px;
    }
    
    .comment-form label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: var(--recruitpro-text-primary, #333);
    }
    
    .required {
        color: var(--recruitpro-error, #dc3545);
    }
    
    .comment-form input[type="text"],
    .comment-form input[type="email"],
    .comment-form input[type="url"],
    .comment-form select,
    .comment-form textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--recruitpro-border, #e1e1e1);
        border-radius: 4px;
        font-size: 14px;
        background: var(--recruitpro-background, #fff);
        color: var(--recruitpro-text-primary, #333);
    }
    
    .comment-form input:focus,
    .comment-form select:focus,
    .comment-form textarea:focus {
        border-color: var(--recruitpro-primary, #0073aa);
        box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
        outline: none;
    }
    
    .comment-form textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .recruitpro-button {
        background: var(--recruitpro-primary, #0073aa);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    
    .recruitpro-button:hover {
        background: var(--recruitpro-primary-hover, #005177);
    }
    
    .comment-notes,
    .comment-policy {
        font-size: 12px;
        color: var(--recruitpro-text-secondary, #666);
        margin-bottom: 15px;
    }
    
    .comment-form-cookies-consent {
        font-size: 12px;
    }
    
    .comment-form-cookies-consent label {
        display: inline;
        margin-left: 8px;
        font-weight: normal;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .comment .children {
            margin-left: 20px;
        }
        
        .comment-content-wrapper {
            flex-direction: column;
            gap: 10px;
        }
        
        .comment-avatar {
            align-self: flex-start;
        }
        
        .comment-respond {
            padding: 20px;
        }
    }
    </style>
    <?php
}

/**
 * Get comment count by professional context
 */
function recruitpro_get_comments_by_context($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $comments = get_comments(array('post_id' => $post_id, 'status' => 'approve'));
    $context_counts = array();
    
    foreach ($comments as $comment) {
        $context = get_comment_meta($comment->comment_ID, 'professional_context', true);
        if (!empty($context)) {
            if (!isset($context_counts[$context])) {
                $context_counts[$context] = 0;
            }
            $context_counts[$context]++;
        }
    }
    
    return $context_counts;
}

/**
 * Display professional context distribution
 */
function recruitpro_display_comment_context_stats($post_id = null) {
    $context_counts = recruitpro_get_comments_by_context($post_id);
    
    if (empty($context_counts)) {
        return;
    }
    
    $context_labels = array(
        'hr_professional' => esc_html__('HR Professionals', 'recruitpro'),
        'recruiter' => esc_html__('Recruiters', 'recruitpro'),
        'job_seeker' => esc_html__('Job Seekers', 'recruitpro'),
        'employer' => esc_html__('Employers', 'recruitpro'),
        'consultant' => esc_html__('Consultants', 'recruitpro'),
        'other' => esc_html__('Other Professionals', 'recruitpro'),
    );
    
    ?>
    <div class="comment-context-stats">
        <h4><?php esc_html_e('Comments by Professional Background:', 'recruitpro'); ?></h4>
        <ul>
            <?php foreach ($context_counts as $context => $count) : ?>
                <li>
                    <?php echo esc_html($context_labels[$context] ?? ucfirst($context)); ?>: 
                    <strong><?php echo intval($count); ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

/**
 * Template function to check if comments are enabled for recruitment
 */
function recruitpro_comments_enabled() {
    return comments_open() && !post_password_required();
}

/**
 * Template function to display professional comment form
 */
function recruitpro_professional_comment_form() {
    if (recruitpro_comments_enabled()) {
        comment_form();
    }
}