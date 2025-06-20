<?php
/**
 * RecruitPro Theme Classic Editor Integration
 *
 * Provides Classic Editor support throughout the theme for better content creation
 * Ensures compatibility with recruitment-focused content and maintains user preferences
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize Classic Editor integration
 */
function recruitpro_init_classic_editor() {
    // Check if Classic Editor plugin is available
    if (recruitpro_is_classic_editor_available()) {
        recruitpro_setup_classic_editor_preferences();
        recruitpro_configure_editor_settings();
        recruitpro_add_editor_enhancements();
        recruitpro_setup_meta_box_integration();
    } else {
        // Recommend Classic Editor installation
        add_action('admin_notices', 'recruitpro_classic_editor_notice');
    }
    
    // Always provide fallback support
    recruitpro_setup_editor_fallbacks();
}
add_action('admin_init', 'recruitpro_init_classic_editor');

/**
 * Check if Classic Editor is available
 */
function recruitpro_is_classic_editor_available() {
    return class_exists('Classic_Editor') || function_exists('classic_editor_init_actions');
}

/**
 * Setup Classic Editor preferences
 */
function recruitpro_setup_classic_editor_preferences() {
    // Set default editor preference for theme
    add_filter('classic_editor_default_editor', 'recruitpro_set_default_editor');
    
    // Allow per-post-type editor settings
    add_filter('classic_editor_enabled_editors_for_post_type', 'recruitpro_editor_for_post_type', 10, 2);
    
    // Customize editor choice interface
    add_filter('classic_editor_add_edit_link', 'recruitpro_customize_editor_links', 10, 3);
    
    // Handle theme-specific editor preferences
    add_action('admin_init', 'recruitpro_handle_editor_preferences');
}

/**
 * Set default editor based on theme preferences
 */
function recruitpro_set_default_editor($default) {
    // Check user preference first
    $user_preference = get_user_meta(get_current_user_id(), 'recruitpro_preferred_editor', true);
    if ($user_preference) {
        return $user_preference;
    }
    
    // Check theme setting
    $theme_preference = get_theme_mod('recruitpro_default_editor', 'classic');
    
    // Return preference or fallback to classic
    return in_array($theme_preference, array('classic', 'block')) ? $theme_preference : 'classic';
}

/**
 * Configure editor availability per post type
 */
function recruitpro_editor_for_post_type($editors, $post_type) {
    // Post types that should prefer Classic Editor
    $classic_preferred = array(
        'post',           // Blog posts
        'page',           // Pages
        'job',            // Job listings (when jobs plugin active)
        'testimonial',    // Testimonials
        'team_member',    // Team members
    );
    
    // Allow plugins to modify the list
    $classic_preferred = apply_filters('recruitpro_classic_editor_post_types', $classic_preferred);
    
    if (in_array($post_type, $classic_preferred)) {
        // For recruitment content, prefer Classic Editor but allow both
        return array('classic_editor' => true, 'block_editor' => true);
    }
    
    return $editors;
}

/**
 * Customize editor choice links
 */
function recruitpro_customize_editor_links($add_link, $post, $settings) {
    // Add custom CSS class for theme styling
    if ($add_link) {
        add_filter('edit_post_link', 'recruitpro_style_editor_links');
    }
    
    return $add_link;
}

/**
 * Style editor links
 */
function recruitpro_style_editor_links($link) {
    return str_replace('class="', 'class="recruitpro-editor-link ', $link);
}

/**
 * Handle editor preferences
 */
function recruitpro_handle_editor_preferences() {
    // Save user editor preference
    if (isset($_POST['recruitpro_save_editor_preference']) && wp_verify_nonce($_POST['recruitpro_editor_nonce'], 'save_editor_preference')) {
        $preference = sanitize_text_field($_POST['recruitpro_editor_preference']);
        if (in_array($preference, array('classic', 'block'))) {
            update_user_meta(get_current_user_id(), 'recruitpro_preferred_editor', $preference);
            add_action('admin_notices', 'recruitpro_editor_preference_saved_notice');
        }
    }
}

/**
 * Configure editor settings for recruitment content
 */
function recruitpro_configure_editor_settings() {
    // Enhance TinyMCE for recruitment content
    add_filter('tiny_mce_before_init', 'recruitpro_configure_tinymce');
    
    // Add custom editor styles
    add_action('admin_init', 'recruitpro_add_editor_styles');
    
    // Configure quicktags for classic editor
    add_action('admin_print_footer_scripts', 'recruitpro_add_quicktags');
    
    // Add recruitment-specific formatting options
    add_filter('mce_buttons', 'recruitpro_add_tinymce_buttons');
    add_filter('mce_buttons_2', 'recruitpro_add_tinymce_buttons_row2');
}

/**
 * Configure TinyMCE settings
 */
function recruitpro_configure_tinymce($init) {
    // Enhanced paste settings for recruitment content
    $init['paste_auto_cleanup_on_paste'] = true;
    $init['paste_strip_class_attributes'] = 'mso';
    $init['paste_remove_spans'] = true;
    $init['paste_remove_styles'] = true;
    
    // Better content formatting
    $init['remove_linebreaks'] = false;
    $init['convert_newlines_to_brs'] = false;
    $init['force_p_newlines'] = true;
    $init['forced_root_block'] = 'p';
    
    // Allow more HTML tags for recruitment content
    $init['extended_valid_elements'] = 'div[class|id|style],span[class|id|style],section[class|id],article[class|id],aside[class|id],header[class|id],footer[class|id],nav[class|id],figure[class|id],figcaption[class|id]';
    
    // Custom formats for recruitment content
    $style_formats = array(
        array(
            'title' => esc_html__('Job Title', 'recruitpro'),
            'selector' => 'h1,h2,h3,h4,h5,h6,p,div',
            'classes' => 'job-title'
        ),
        array(
            'title' => esc_html__('Job Requirements', 'recruitpro'),
            'selector' => 'ul,ol',
            'classes' => 'job-requirements'
        ),
        array(
            'title' => esc_html__('Job Benefits', 'recruitpro'),
            'selector' => 'ul,ol',
            'classes' => 'job-benefits'
        ),
        array(
            'title' => esc_html__('Company Info', 'recruitpro'),
            'selector' => 'div,p',
            'classes' => 'company-info'
        ),
        array(
            'title' => esc_html__('Highlight Box', 'recruitpro'),
            'selector' => 'div,p',
            'classes' => 'highlight-box'
        ),
        array(
            'title' => esc_html__('Call to Action', 'recruitpro'),
            'selector' => 'div,p,a',
            'classes' => 'cta-button'
        ),
    );
    
    $init['style_formats'] = wp_json_encode($style_formats);
    
    return $init;
}

/**
 * Add editor styles
 */
function recruitpro_add_editor_styles() {
    // Add custom editor stylesheet
    $editor_style_path = get_template_directory_uri() . '/assets/css/editor-style.css';
    if (file_exists(get_template_directory() . '/assets/css/editor-style.css')) {
        add_editor_style($editor_style_path);
    }
    
    // Add recruitment-specific styles inline
    add_action('admin_head', 'recruitpro_inline_editor_styles');
}

/**
 * Add inline editor styles
 */
function recruitpro_inline_editor_styles() {
    ?>
    <style>
    /* RecruitPro Editor Styles */
    .mce-content-body .job-title {
        color: #0073aa;
        font-weight: bold;
        border-bottom: 2px solid #0073aa;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    .mce-content-body .job-requirements {
        background: #f8f9fa;
        padding: 15px;
        border-left: 4px solid #28a745;
        margin: 15px 0;
    }
    .mce-content-body .job-benefits {
        background: #f8f9fa;
        padding: 15px;
        border-left: 4px solid #007bff;
        margin: 15px 0;
    }
    .mce-content-body .company-info {
        background: #fff3cd;
        padding: 15px;
        border: 1px solid #ffeaa7;
        border-radius: 4px;
        margin: 15px 0;
    }
    .mce-content-body .highlight-box {
        background: #e7f3ff;
        padding: 20px;
        border: 2px solid #0073aa;
        border-radius: 8px;
        margin: 20px 0;
        text-align: center;
    }
    .mce-content-body .cta-button {
        display: inline-block;
        background: #0073aa;
        color: white !important;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        text-align: center;
    }
    
    /* Editor enhancements */
    .recruitpro-editor-link {
        font-weight: bold;
        color: #0073aa;
    }
    </style>
    <?php
}

/**
 * Add custom TinyMCE buttons
 */
function recruitpro_add_tinymce_buttons($buttons) {
    // Add style format dropdown
    array_unshift($buttons, 'styleselect');
    
    // Add recruitment-specific buttons after existing ones
    $insert_pos = array_search('wp_more', $buttons);
    if ($insert_pos !== false) {
        array_splice($buttons, $insert_pos + 1, 0, 'recruitpro_job_template');
    } else {
        $buttons[] = 'recruitpro_job_template';
    }
    
    return $buttons;
}

/**
 * Add second row TinyMCE buttons
 */
function recruitpro_add_tinymce_buttons_row2($buttons) {
    // Add more formatting options
    $buttons[] = 'hr';
    $buttons[] = 'sub';
    $buttons[] = 'sup';
    $buttons[] = 'charmap';
    
    return $buttons;
}

/**
 * Add recruitment-specific quicktags
 */
function recruitpro_add_quicktags() {
    if (!wp_script_is('quicktags')) {
        return;
    }
    ?>
    <script type="text/javascript">
    if (typeof QTags !== 'undefined') {
        // Job title quicktag
        QTags.addButton('job_title', '<?php esc_html_e('Job Title', 'recruitpro'); ?>', '<div class="job-title">', '</div>');
        
        // Requirements list
        QTags.addButton('requirements', '<?php esc_html_e('Requirements', 'recruitpro'); ?>', '<ul class="job-requirements"><li>', '</li></ul>');
        
        // Benefits list
        QTags.addButton('benefits', '<?php esc_html_e('Benefits', 'recruitpro'); ?>', '<ul class="job-benefits"><li>', '</li></ul>');
        
        // Company info
        QTags.addButton('company_info', '<?php esc_html_e('Company Info', 'recruitpro'); ?>', '<div class="company-info">', '</div>');
        
        // Highlight box
        QTags.addButton('highlight', '<?php esc_html_e('Highlight', 'recruitpro'); ?>', '<div class="highlight-box">', '</div>');
        
        // Call to action
        QTags.addButton('cta', '<?php esc_html_e('CTA Button', 'recruitpro'); ?>', '<a href="#" class="cta-button">', '</a>');
    }
    </script>
    <?php
}

/**
 * Add editor enhancements
 */
function recruitpro_add_editor_enhancements() {
    // Add meta boxes for classic editor
    add_action('add_meta_boxes', 'recruitpro_add_editor_meta_boxes');
    
    // Add editor toolbar enhancements
    add_action('admin_print_footer_scripts', 'recruitpro_editor_toolbar_enhancements');
    
    // Add content templates
    add_action('edit_form_after_title', 'recruitpro_add_content_templates');
}

/**
 * Add meta boxes for editor enhancement
 */
function recruitpro_add_editor_meta_boxes() {
    $post_types = array('post', 'page', 'job');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'recruitpro_editor_help',
            esc_html__('Content Helper', 'recruitpro'),
            'recruitpro_editor_help_meta_box',
            $post_type,
            'side',
            'high'
        );
    }
}

/**
 * Editor help meta box
 */
function recruitpro_editor_help_meta_box($post) {
    ?>
    <div class="recruitpro-editor-help">
        <h4><?php esc_html_e('Quick Formatting', 'recruitpro'); ?></h4>
        <ul>
            <li><strong><?php esc_html_e('Job Title:', 'recruitpro'); ?></strong> <?php esc_html_e('Use the Job Title style for position names', 'recruitpro'); ?></li>
            <li><strong><?php esc_html_e('Requirements:', 'recruitpro'); ?></strong> <?php esc_html_e('Create bulleted lists with green border', 'recruitpro'); ?></li>
            <li><strong><?php esc_html_e('Benefits:', 'recruitpro'); ?></strong> <?php esc_html_e('Create bulleted lists with blue border', 'recruitpro'); ?></li>
            <li><strong><?php esc_html_e('Highlight:', 'recruitpro'); ?></strong> <?php esc_html_e('Draw attention to important information', 'recruitpro'); ?></li>
        </ul>
        
        <h4><?php esc_html_e('Content Templates', 'recruitpro'); ?></h4>
        <button type="button" class="button button-small" onclick="recruitproInsertJobTemplate()">
            <?php esc_html_e('Insert Job Template', 'recruitpro'); ?>
        </button>
        <button type="button" class="button button-small" onclick="recruitproInsertCompanyTemplate()">
            <?php esc_html_e('Insert Company Template', 'recruitpro'); ?>
        </button>
    </div>
    
    <style>
    .recruitpro-editor-help ul {
        font-size: 12px;
        margin: 10px 0;
    }
    .recruitpro-editor-help li {
        margin-bottom: 5px;
    }
    .recruitpro-editor-help .button {
        display: block;
        width: 100%;
        margin-bottom: 5px;
        text-align: center;
    }
    </style>
    <?php
}

/**
 * Add content templates
 */
function recruitpro_add_content_templates($post) {
    if ($post->post_type !== 'job') {
        return;
    }
    ?>
    <div class="recruitpro-content-templates" style="margin: 10px 0;">
        <label>
            <strong><?php esc_html_e('Quick Start Templates:', 'recruitpro'); ?></strong>
        </label>
        <select id="recruitpro-template-selector">
            <option value=""><?php esc_html_e('Choose a template...', 'recruitpro'); ?></option>
            <option value="basic_job"><?php esc_html_e('Basic Job Listing', 'recruitpro'); ?></option>
            <option value="detailed_job"><?php esc_html_e('Detailed Job Description', 'recruitpro'); ?></option>
            <option value="executive_job"><?php esc_html_e('Executive Position', 'recruitpro'); ?></option>
        </select>
        <button type="button" class="button" onclick="recruitproLoadTemplate()">
            <?php esc_html_e('Load Template', 'recruitpro'); ?>
        </button>
    </div>
    <?php
}

/**
 * Editor toolbar enhancements
 */
function recruitpro_editor_toolbar_enhancements() {
    ?>
    <script type="text/javascript">
    // Content templates
    function recruitproInsertJobTemplate() {
        var template = '<div class="job-title">Position Title</div>\n\n' +
                      '<div class="company-info">\n' +
                      '<strong>Company:</strong> Company Name\n' +
                      '<strong>Location:</strong> City, State\n' +
                      '<strong>Employment Type:</strong> Full-time\n' +
                      '</div>\n\n' +
                      '<h3>Job Description</h3>\n' +
                      '<p>Brief description of the role and responsibilities...</p>\n\n' +
                      '<h3>Requirements</h3>\n' +
                      '<ul class="job-requirements">\n' +
                      '<li>Requirement 1</li>\n' +
                      '<li>Requirement 2</li>\n' +
                      '<li>Requirement 3</li>\n' +
                      '</ul>\n\n' +
                      '<h3>Benefits</h3>\n' +
                      '<ul class="job-benefits">\n' +
                      '<li>Benefit 1</li>\n' +
                      '<li>Benefit 2</li>\n' +
                      '<li>Benefit 3</li>\n' +
                      '</ul>\n\n' +
                      '<div class="highlight-box">\n' +
                      '<a href="#" class="cta-button">Apply Now</a>\n' +
                      '</div>';
        
        recruitproInsertContent(template);
    }
    
    function recruitproInsertCompanyTemplate() {
        var template = '<div class="company-info">\n' +
                      '<h3>About Our Company</h3>\n' +
                      '<p>Company description and culture...</p>\n\n' +
                      '<strong>Industry:</strong> Industry Type\n' +
                      '<strong>Size:</strong> Number of employees\n' +
                      '<strong>Founded:</strong> Year\n' +
                      '</div>';
        
        recruitproInsertContent(template);
    }
    
    function recruitproLoadTemplate() {
        var selector = document.getElementById('recruitpro-template-selector');
        var template = selector.value;
        
        if (!template) return;
        
        var content = '';
        switch(template) {
            case 'basic_job':
                content = '<div class="job-title">[Job Title]</div>\n\n' +
                         '<p>[Brief job description]</p>\n\n' +
                         '<h3>Requirements</h3>\n' +
                         '<ul class="job-requirements">\n' +
                         '<li>[Requirement 1]</li>\n' +
                         '<li>[Requirement 2]</li>\n' +
                         '</ul>';
                break;
            case 'detailed_job':
                recruitproInsertJobTemplate();
                return;
            case 'executive_job':
                content = '<div class="job-title">[Executive Position Title]</div>\n\n' +
                         '<div class="company-info">\n' +
                         '<strong>Company:</strong> [Company Name]\n' +
                         '<strong>Reporting to:</strong> [Title]\n' +
                         '<strong>Location:</strong> [City, State]\n' +
                         '</div>\n\n' +
                         '<h3>Executive Summary</h3>\n' +
                         '<p>[Strategic role overview]</p>\n\n' +
                         '<h3>Key Responsibilities</h3>\n' +
                         '<ul class="job-requirements">\n' +
                         '<li>[Strategic responsibility 1]</li>\n' +
                         '<li>[Leadership responsibility 2]</li>\n' +
                         '</ul>\n\n' +
                         '<h3>Executive Requirements</h3>\n' +
                         '<ul class="job-requirements">\n' +
                         '<li>[Years of experience required]</li>\n' +
                         '<li>[Industry experience]</li>\n' +
                         '<li>[Leadership experience]</li>\n' +
                         '</ul>';
                break;
        }
        
        if (content) {
            recruitproInsertContent(content);
            selector.value = '';
        }
    }
    
    function recruitproInsertContent(content) {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, content);
        } else {
            // Fallback for text mode
            var editor = document.getElementById('content');
            if (editor) {
                var cursorPos = editor.selectionStart;
                var textBefore = editor.value.substring(0, cursorPos);
                var textAfter = editor.value.substring(cursorPos);
                editor.value = textBefore + content + textAfter;
                editor.selectionStart = editor.selectionEnd = cursorPos + content.length;
                editor.focus();
            }
        }
    }
    </script>
    <?php
}

/**
 * Setup meta box integration
 */
function recruitpro_setup_meta_box_integration() {
    // Ensure meta boxes work properly with Classic Editor
    add_action('add_meta_boxes', 'recruitpro_reorder_meta_boxes', 999);
    
    // Add Classic Editor preference to user profile
    add_action('show_user_profile', 'recruitpro_editor_preference_fields');
    add_action('edit_user_profile', 'recruitpro_editor_preference_fields');
    add_action('personal_options_update', 'recruitpro_save_editor_preference');
    add_action('edit_user_profile_update', 'recruitpro_save_editor_preference');
}

/**
 * Reorder meta boxes for better Classic Editor experience
 */
function recruitpro_reorder_meta_boxes() {
    global $wp_meta_boxes;
    
    // Move editor help to high priority
    $post_types = array('post', 'page', 'job');
    foreach ($post_types as $post_type) {
        if (isset($wp_meta_boxes[$post_type]['side']['default']['recruitpro_editor_help'])) {
            $help_box = $wp_meta_boxes[$post_type]['side']['default']['recruitpro_editor_help'];
            unset($wp_meta_boxes[$post_type]['side']['default']['recruitpro_editor_help']);
            $wp_meta_boxes[$post_type]['side']['high']['recruitpro_editor_help'] = $help_box;
        }
    }
}

/**
 * Editor preference fields in user profile
 */
function recruitpro_editor_preference_fields($user) {
    $preference = get_user_meta($user->ID, 'recruitpro_preferred_editor', true);
    ?>
    <h3><?php esc_html_e('RecruitPro Editor Preferences', 'recruitpro'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="recruitpro_editor_preference"><?php esc_html_e('Preferred Editor', 'recruitpro'); ?></label></th>
            <td>
                <select id="recruitpro_editor_preference" name="recruitpro_editor_preference">
                    <option value=""><?php esc_html_e('Use default', 'recruitpro'); ?></option>
                    <option value="classic" <?php selected($preference, 'classic'); ?>><?php esc_html_e('Classic Editor', 'recruitpro'); ?></option>
                    <option value="block" <?php selected($preference, 'block'); ?>><?php esc_html_e('Block Editor', 'recruitpro'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Choose your preferred editor for creating recruitment content.', 'recruitpro'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save editor preference
 */
function recruitpro_save_editor_preference($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    
    $preference = sanitize_text_field($_POST['recruitpro_editor_preference']);
    if (in_array($preference, array('', 'classic', 'block'))) {
        update_user_meta($user_id, 'recruitpro_preferred_editor', $preference);
    }
}

/**
 * Setup editor fallbacks
 */
function recruitpro_setup_editor_fallbacks() {
    // Provide basic formatting even without Classic Editor plugin
    add_filter('wp_editor_settings', 'recruitpro_enhance_default_editor');
    
    // Add recruitment-specific CSS to default editor
    add_action('admin_head-post.php', 'recruitpro_fallback_editor_styles');
    add_action('admin_head-post-new.php', 'recruitpro_fallback_editor_styles');
}

/**
 * Enhance default editor settings
 */
function recruitpro_enhance_default_editor($settings) {
    // Add recruitment-friendly settings
    $settings['quicktags'] = true;
    $settings['tinymce'] = array(
        'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,alignjustify,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
        'toolbar2' => 'styleselect,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
    );
    
    return $settings;
}

/**
 * Fallback editor styles
 */
function recruitpro_fallback_editor_styles() {
    ?>
    <style>
    /* Fallback styles when Classic Editor plugin is not available */
    #wp-content-editor-tools {
        background: #f1f1f1;
        border: 1px solid #ddd;
        border-bottom: none;
        padding: 8px;
    }
    
    .wp-editor-container {
        border: 1px solid #ddd;
    }
    
    .recruitpro-editor-notice {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
    }
    </style>
    <?php
}

/**
 * Classic Editor recommendation notice
 */
function recruitpro_classic_editor_notice() {
    if (!current_user_can('install_plugins')) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, array('post', 'page', 'edit-post', 'edit-page'))) {
        return;
    }
    ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
            <?php esc_html_e('For the best content creation experience, we recommend installing the Classic Editor plugin.', 'recruitpro'); ?>
            <a href="<?php echo esc_url(admin_url('plugin-install.php?s=classic+editor&tab=search&type=term')); ?>" class="button button-small">
                <?php esc_html_e('Install Classic Editor', 'recruitpro'); ?>
            </a>
        </p>
    </div>
    <?php
}

/**
 * Editor preference saved notice
 */
function recruitpro_editor_preference_saved_notice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php esc_html_e('Editor preference saved successfully!', 'recruitpro'); ?></p>
    </div>
    <?php
}

/**
 * Get user's preferred editor
 */
function recruitpro_get_user_preferred_editor($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    
    $preference = get_user_meta($user_id, 'recruitpro_preferred_editor', true);
    
    if (empty($preference)) {
        $preference = get_theme_mod('recruitpro_default_editor', 'classic');
    }
    
    return $preference;
}

/**
 * Check if user prefers Classic Editor
 */
function recruitpro_user_prefers_classic_editor($user_id = null) {
    return recruitpro_get_user_preferred_editor($user_id) === 'classic';
}

/**
 * Add editor preference to customizer
 */
function recruitpro_add_editor_customizer_setting($wp_customize) {
    // Add editor section
    $wp_customize->add_section('recruitpro_editor_settings', array(
        'title'    => esc_html__('Editor Settings', 'recruitpro'),
        'priority' => 200,
    ));
    
    // Default editor setting
    $wp_customize->add_setting('recruitpro_default_editor', array(
        'default'           => 'classic',
        'sanitize_callback' => 'recruitpro_sanitize_editor_choice',
    ));
    
    $wp_customize->add_control('recruitpro_default_editor', array(
        'label'   => esc_html__('Default Editor', 'recruitpro'),
        'section' => 'recruitpro_editor_settings',
        'type'    => 'select',
        'choices' => array(
            'classic' => esc_html__('Classic Editor', 'recruitpro'),
            'block'   => esc_html__('Block Editor', 'recruitpro'),
        ),
    ));
}
add_action('customize_register', 'recruitpro_add_editor_customizer_setting');

/**
 * Sanitize editor choice
 */
function recruitpro_sanitize_editor_choice($input) {
    return in_array($input, array('classic', 'block')) ? $input : 'classic';
}

/**
 * Template tag to check if Classic Editor is active
 */
function recruitpro_is_using_classic_editor() {
    return recruitpro_is_classic_editor_available() && recruitpro_user_prefers_classic_editor();
}