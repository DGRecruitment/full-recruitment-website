<?php
/**
 * Accessibility Enhancements for RecruitPro Theme
 *
 * WCAG 2.1 AA compliant accessibility features specifically designed for
 * recruitment agencies and job seekers. Provides comprehensive accessibility
 * support for all recruitment-related functionality.
 *
 * @package RecruitPro
 * @subpackage Inc
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Accessibility Class
 */
class RecruitPro_Accessibility {

    /**
     * Initialize accessibility features
     */
    public static function init() {
        add_action('wp_head', array(__CLASS__, 'add_accessibility_metadata'));
        add_action('wp_head', array(__CLASS__, 'add_skip_links_styles'));
        add_action('wp_footer', array(__CLASS__, 'add_accessibility_enhancements'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_accessibility_assets'));
        add_action('wp_body_open', array(__CLASS__, 'add_skip_links'));
        add_action('wp_body_open', array(__CLASS__, 'add_accessibility_toolbar'));
        
        // Form accessibility enhancements
        add_filter('comment_form_defaults', array(__CLASS__, 'enhance_comment_form_accessibility'));
        add_filter('get_search_form', array(__CLASS__, 'enhance_search_form_accessibility'));
        
        // Content accessibility filters
        add_filter('the_content', array(__CLASS__, 'enhance_content_accessibility'));
        add_filter('wp_nav_menu_args', array(__CLASS__, 'enhance_menu_accessibility'));
        add_filter('wp_nav_menu_items', array(__CLASS__, 'add_menu_accessibility_attributes'), 10, 2);
        
        // Job listing accessibility (theme-level only)
        add_filter('recruitpro_job_card_output', array(__CLASS__, 'enhance_job_card_accessibility'));
        add_filter('recruitpro_job_form_output', array(__CLASS__, 'enhance_job_form_accessibility'));
        
        // Widget accessibility
        add_filter('widget_title', array(__CLASS__, 'enhance_widget_title_accessibility'), 10, 3);
        
        // Image accessibility
        add_filter('wp_get_attachment_image_attributes', array(__CLASS__, 'enhance_image_accessibility'));
        
        // Admin accessibility for recruitment team
        if (is_admin()) {
            add_action('admin_init', array(__CLASS__, 'enhance_admin_accessibility'));
        }
    }

    /**
     * Add accessibility metadata to document head
     */
    public static function add_accessibility_metadata() {
        ?>
        <!-- Accessibility Metadata -->
        <meta name="accessibility-compliance" content="WCAG 2.1 AA">
        <meta name="recruitment-accessibility" content="optimized">
        <meta name="assistive-technology" content="screen-reader-optimized">
        
        <!-- Accessibility Settings -->
        <script type="application/json" id="accessibility-settings">
        {
            "skipLinksEnabled": true,
            "keyboardNavigationEnabled": true,
            "highContrastMode": false,
            "textScalingEnabled": true,
            "reducedMotionSupport": true,
            "recruitmentOptimized": true,
            "multiLanguageSupport": true,
            "assistiveTechSupport": true
        }
        </script>
        <?php
    }

    /**
     * Add skip links styles to head
     */
    public static function add_skip_links_styles() {
        ?>
        <style id="accessibility-skip-links-css">
        /* Skip Links - WCAG 2.1 Compliant */
        .skip-links {
            position: absolute;
            top: -10000px;
            left: -10000px;
            z-index: 999999;
            background: #000;
            color: #fff;
            padding: 0.5rem 1rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .skip-links:focus {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999999;
        }
        
        .skip-links:focus,
        .skip-links:active {
            clip: auto !important;
            height: auto !important;
            margin: 0 !important;
            overflow: visible !important;
            position: absolute !important;
            width: auto !important;
        }

        /* Screen Reader Only Text */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        .sr-only-focusable:focus {
            position: static !important;
            width: auto !important;
            height: auto !important;
            padding: 0.5rem !important;
            margin: 0 !important;
            overflow: visible !important;
            clip: auto !important;
            white-space: normal !important;
            background: #000 !important;
            color: #fff !important;
            border: 2px solid #fff !important;
        }
        </style>
        <?php
    }

    /**
     * Add skip links to page
     */
    public static function add_skip_links() {
        ?>
        <!-- Skip Links for Keyboard Navigation -->
        <div class="skip-links-wrapper" role="navigation" aria-label="<?php esc_attr_e('Skip navigation links', 'recruitpro'); ?>">
            <a href="#main-content" class="skip-links">
                <?php esc_html_e('Skip to main content', 'recruitpro'); ?>
            </a>
            <a href="#main-navigation" class="skip-links">
                <?php esc_html_e('Skip to navigation', 'recruitpro'); ?>
            </a>
            <?php if (is_active_sidebar('sidebar-1')) : ?>
            <a href="#secondary" class="skip-links">
                <?php esc_html_e('Skip to sidebar', 'recruitpro'); ?>
            </a>
            <?php endif; ?>
            <a href="#footer" class="skip-links">
                <?php esc_html_e('Skip to footer', 'recruitpro'); ?>
            </a>
            <?php if (self::has_job_listings()) : ?>
            <a href="#job-listings" class="skip-links">
                <?php esc_html_e('Skip to job listings', 'recruitpro'); ?>
            </a>
            <?php endif; ?>
            <?php if (self::has_job_search()) : ?>
            <a href="#job-search" class="skip-links">
                <?php esc_html_e('Skip to job search', 'recruitpro'); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Add accessibility toolbar
     */
    public static function add_accessibility_toolbar() {
        if (!get_theme_mod('recruitpro_show_accessibility_toolbar', true)) {
            return;
        }
        ?>
        <!-- Accessibility Toolbar -->
        <div id="accessibility-toolbar" class="accessibility-toolbar" role="toolbar" aria-label="<?php esc_attr_e('Accessibility options', 'recruitpro'); ?>">
            <button type="button" class="accessibility-toggle" aria-expanded="false" aria-controls="accessibility-panel" title="<?php esc_attr_e('Accessibility Options', 'recruitpro'); ?>">
                <span class="accessibility-icon" aria-hidden="true">♿</span>
                <span class="sr-only"><?php esc_html_e('Accessibility Options', 'recruitpro'); ?></span>
            </button>
            
            <div id="accessibility-panel" class="accessibility-panel" hidden>
                <div class="accessibility-panel-header">
                    <h3><?php esc_html_e('Accessibility Options', 'recruitpro'); ?></h3>
                    <button type="button" class="accessibility-close" aria-label="<?php esc_attr_e('Close accessibility options', 'recruitpro'); ?>">×</button>
                </div>
                
                <div class="accessibility-panel-content">
                    <!-- Font Size Controls -->
                    <div class="accessibility-control">
                        <label for="font-size-control"><?php esc_html_e('Text Size', 'recruitpro'); ?></label>
                        <div class="font-size-controls">
                            <button type="button" class="font-size-btn" data-action="decrease" aria-label="<?php esc_attr_e('Decrease text size', 'recruitpro'); ?>">A-</button>
                            <button type="button" class="font-size-btn" data-action="reset" aria-label="<?php esc_attr_e('Reset text size', 'recruitpro'); ?>">A</button>
                            <button type="button" class="font-size-btn" data-action="increase" aria-label="<?php esc_attr_e('Increase text size', 'recruitpro'); ?>">A+</button>
                        </div>
                    </div>
                    
                    <!-- High Contrast Mode -->
                    <div class="accessibility-control">
                        <label for="high-contrast-toggle"><?php esc_html_e('High Contrast', 'recruitpro'); ?></label>
                        <button type="button" id="high-contrast-toggle" class="accessibility-toggle-btn" role="switch" aria-checked="false">
                            <span class="toggle-track">
                                <span class="toggle-thumb"></span>
                            </span>
                            <span class="btn-text"><?php esc_html_e('Off', 'recruitpro'); ?></span>
                            <span class="btn-status" aria-live="polite"><?php esc_html_e('Off', 'recruitpro'); ?></span>
                        </button>
                    </div>
                    
                    <!-- Reduce Motion -->
                    <div class="accessibility-control">
                        <label for="reduce-motion-toggle"><?php esc_html_e('Reduce Motion', 'recruitpro'); ?></label>
                        <button type="button" id="reduce-motion-toggle" class="accessibility-toggle-btn" role="switch" aria-checked="false">
                            <span class="toggle-track">
                                <span class="toggle-thumb"></span>
                            </span>
                            <span class="btn-text"><?php esc_html_e('Off', 'recruitpro'); ?></span>
                            <span class="btn-status" aria-live="polite"><?php esc_html_e('Off', 'recruitpro'); ?></span>
                        </button>
                    </div>
                    
                    <!-- Focus Indicators -->
                    <div class="accessibility-control">
                        <label for="focus-indicators-toggle"><?php esc_html_e('Enhanced Focus', 'recruitpro'); ?></label>
                        <button type="button" id="focus-indicators-toggle" class="accessibility-toggle-btn" role="switch" aria-checked="false">
                            <span class="toggle-track">
                                <span class="toggle-thumb"></span>
                            </span>
                            <span class="btn-text"><?php esc_html_e('Off', 'recruitpro'); ?></span>
                            <span class="btn-status" aria-live="polite"><?php esc_html_e('Off', 'recruitpro'); ?></span>
                        </button>
                    </div>
                    
                    <!-- Keyboard Navigation Help -->
                    <div class="accessibility-control">
                        <button type="button" class="accessibility-help-btn" data-action="show-keyboard-help">
                            <?php esc_html_e('Keyboard Navigation Help', 'recruitpro'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="accessibility-panel-footer">
                    <button type="button" class="accessibility-reset-btn" data-action="reset-all">
                        <?php esc_html_e('Reset All Settings', 'recruitpro'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Add accessibility enhancements to footer
     */
    public static function add_accessibility_enhancements() {
        ?>
        <!-- ARIA Live Region for Screen Reader Announcements -->
        <div id="aria-live-region" aria-live="polite" aria-atomic="true" class="sr-only"></div>
        <div id="aria-live-assertive" aria-live="assertive" aria-atomic="true" class="sr-only"></div>
        
        <!-- Keyboard Navigation Help Modal -->
        <div id="keyboard-help-modal" class="accessibility-modal" hidden role="dialog" aria-labelledby="keyboard-help-title" aria-modal="true">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="keyboard-help-title"><?php esc_html_e('Keyboard Navigation Guide', 'recruitpro'); ?></h2>
                    <button type="button" class="modal-close" aria-label="<?php esc_attr_e('Close help modal', 'recruitpro'); ?>">×</button>
                </div>
                <div class="modal-body">
                    <h3><?php esc_html_e('General Navigation', 'recruitpro'); ?></h3>
                    <ul>
                        <li><kbd>Tab</kbd> - <?php esc_html_e('Move to next interactive element', 'recruitpro'); ?></li>
                        <li><kbd>Shift + Tab</kbd> - <?php esc_html_e('Move to previous interactive element', 'recruitpro'); ?></li>
                        <li><kbd>Enter</kbd> - <?php esc_html_e('Activate buttons and links', 'recruitpro'); ?></li>
                        <li><kbd>Space</kbd> - <?php esc_html_e('Activate buttons and checkboxes', 'recruitpro'); ?></li>
                        <li><kbd>Escape</kbd> - <?php esc_html_e('Close modals and dropdowns', 'recruitpro'); ?></li>
                    </ul>
                    
                    <h3><?php esc_html_e('Job Search & Applications', 'recruitpro'); ?></h3>
                    <ul>
                        <li><kbd>Ctrl + F</kbd> - <?php esc_html_e('Search for jobs on current page', 'recruitpro'); ?></li>
                        <li><kbd>Enter</kbd> - <?php esc_html_e('Submit job application form', 'recruitpro'); ?></li>
                        <li><kbd>Tab</kbd> - <?php esc_html_e('Navigate through job listing details', 'recruitpro'); ?></li>
                    </ul>
                    
                    <h3><?php esc_html_e('Forms & File Uploads', 'recruitpro'); ?></h3>
                    <ul>
                        <li><kbd>Tab</kbd> - <?php esc_html_e('Move between form fields', 'recruitpro'); ?></li>
                        <li><kbd>Space</kbd> - <?php esc_html_e('Open file upload dialog', 'recruitpro'); ?></li>
                        <li><kbd>Arrow Keys</kbd> - <?php esc_html_e('Navigate radio buttons and select options', 'recruitpro'); ?></li>
                    </ul>
                    
                    <h3><?php esc_html_e('Accessibility Features', 'recruitpro'); ?></h3>
                    <ul>
                        <li><kbd>Alt + A</kbd> - <?php esc_html_e('Open accessibility toolbar', 'recruitpro'); ?></li>
                        <li><kbd>Alt + 1</kbd> - <?php esc_html_e('Jump to main content', 'recruitpro'); ?></li>
                        <li><kbd>Alt + 2</kbd> - <?php esc_html_e('Jump to navigation', 'recruitpro'); ?></li>
                        <li><kbd>Alt + 3</kbd> - <?php esc_html_e('Jump to job search', 'recruitpro'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Focus Indicator for Enhanced Visibility -->
        <div id="focus-indicator" class="focus-indicator" aria-hidden="true"></div>
        
        <script>
        // Initialize accessibility features on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof RecruitProAccessibility !== 'undefined') {
                RecruitProAccessibility.init();
            }
        });
        
        // Keyboard shortcut for accessibility toolbar
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === 'a') {
                e.preventDefault();
                const toolbar = document.getElementById('accessibility-toolbar');
                if (toolbar) {
                    const toggle = toolbar.querySelector('.accessibility-toggle');
                    if (toggle) {
                        toggle.click();
                        toggle.focus();
                    }
                }
            }
        });
        </script>
        <?php
    }

    /**
     * Enqueue accessibility assets
     */
    public static function enqueue_accessibility_assets() {
        // Accessibility JavaScript
        wp_enqueue_script(
            'recruitpro-accessibility',
            get_template_directory_uri() . '/assets/js/accessibility.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );

        // Accessibility CSS
        wp_enqueue_style(
            'recruitpro-accessibility',
            get_template_directory_uri() . '/assets/css/accessibility.css',
            array(),
            wp_get_theme()->get('Version')
        );

        // Localize accessibility script
        wp_localize_script('recruitpro-accessibility', 'recruitproAccessibility', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_accessibility_nonce'),
            'strings' => array(
                'fontSizeIncreased' => __('Font size increased', 'recruitpro'),
                'fontSizeDecreased' => __('Font size decreased', 'recruitpro'),
                'fontSizeReset' => __('Font size reset to default', 'recruitpro'),
                'highContrastOn' => __('High contrast mode enabled', 'recruitpro'),
                'highContrastOff' => __('High contrast mode disabled', 'recruitpro'),
                'motionReduced' => __('Motion effects reduced', 'recruitpro'),
                'motionRestored' => __('Motion effects restored', 'recruitpro'),
                'focusEnhanced' => __('Focus indicators enhanced', 'recruitpro'),
                'focusNormal' => __('Focus indicators set to normal', 'recruitpro'),
                'settingsReset' => __('All accessibility settings reset', 'recruitpro'),
                'jobApplicationError' => __('Error in job application form. Please check required fields.', 'recruitpro'),
                'fileUploadSuccess' => __('File uploaded successfully', 'recruitpro'),
                'fileUploadError' => __('Error uploading file. Please try again.', 'recruitpro'),
                'searchResultsLoaded' => __('Search results updated', 'recruitpro'),
                'noJobsFound' => __('No jobs found matching your criteria', 'recruitpro'),
                'filtersApplied' => __('Job filters applied successfully', 'recruitpro'),
                'pageLoading' => __('Page is loading, please wait', 'recruitpro'),
                'formSubmitted' => __('Form submitted successfully', 'recruitpro')
            ),
            'settings' => array(
                'enableKeyboardShortcuts' => get_theme_mod('recruitpro_enable_keyboard_shortcuts', true),
                'enableAriaLive' => get_theme_mod('recruitpro_enable_aria_live', true),
                'enableFocusManagement' => get_theme_mod('recruitpro_enable_focus_management', true),
                'enableHighContrast' => get_theme_mod('recruitpro_enable_high_contrast', true),
                'enableTextScaling' => get_theme_mod('recruitpro_enable_text_scaling', true),
                'defaultFontSize' => get_theme_mod('recruitpro_default_font_size', 16),
                'maxFontSize' => get_theme_mod('recruitpro_max_font_size', 24)
            )
        ));
    }

    /**
     * Enhance comment form accessibility
     */
    public static function enhance_comment_form_accessibility($defaults) {
        $defaults['title_reply'] = '<span id="reply-title" class="comment-reply-title">' . $defaults['title_reply'] . '</span>';
        $defaults['must_log_in'] = '<p class="must-log-in" role="alert">' . 
            sprintf(
                __('You must be <a href="%s" aria-label="Log in to post a comment">logged in</a> to post a comment.', 'recruitpro'),
                wp_login_url(apply_filters('the_permalink', get_permalink()))
            ) . '</p>';

        $defaults['fields']['author'] = '<p class="comment-form-author">' .
            '<label for="author">' . __('Name', 'recruitpro') . ' <span class="required" aria-label="required">*</span></label>' .
            '<input id="author" name="author" type="text" aria-required="true" aria-describedby="author-notes" size="30" maxlength="245" autocomplete="name" />' .
            '<span id="author-notes" class="form-help sr-only">' . __('Your full name is required', 'recruitpro') . '</span>' .
            '</p>';

        $defaults['fields']['email'] = '<p class="comment-form-email">' .
            '<label for="email">' . __('Email', 'recruitpro') . ' <span class="required" aria-label="required">*</span></label>' .
            '<input id="email" name="email" type="email" aria-required="true" aria-describedby="email-notes" size="30" maxlength="100" autocomplete="email" />' .
            '<span id="email-notes" class="form-help sr-only">' . __('Your email address will not be published', 'recruitpro') . '</span>' .
            '</p>';

        $defaults['comment_field'] = '<p class="comment-form-comment">' .
            '<label for="comment">' . __('Comment', 'recruitpro') . ' <span class="required" aria-label="required">*</span></label>' .
            '<textarea id="comment" name="comment" aria-required="true" aria-describedby="comment-notes" cols="45" rows="8"></textarea>' .
            '<span id="comment-notes" class="form-help sr-only">' . __('Share your thoughts about this job posting', 'recruitpro') . '</span>' .
            '</p>';

        return $defaults;
    }

    /**
     * Enhance search form accessibility
     */
    public static function enhance_search_form_accessibility($form) {
        $form = str_replace('type="search"', 'type="search" aria-label="' . esc_attr__('Search for jobs and content', 'recruitpro') . '"', $form);
        $form = str_replace('placeholder="', 'placeholder="' . esc_attr__('Search jobs, companies, locations...', 'recruitpro') . '" aria-describedby="search-help" ', $form);
        
        // Add search help text
        $help_text = '<span id="search-help" class="search-help sr-only">' . 
            __('Enter keywords to search for jobs, companies, or locations. Use specific terms for better results.', 'recruitpro') . 
            '</span>';
        
        $form = str_replace('</form>', $help_text . '</form>', $form);
        
        return $form;
    }

    /**
     * Enhance content accessibility
     */
    public static function enhance_content_accessibility($content) {
        // Add heading structure indicators
        $content = preg_replace('/<h([1-6])([^>]*)>/', '<h$1$2><span class="sr-only">Heading level $1: </span>', $content);
        
        // Enhance table accessibility
        $content = preg_replace_callback('/<table([^>]*)>/', function($matches) {
            $attributes = $matches[1];
            if (strpos($attributes, 'role=') === false) {
                $attributes .= ' role="table"';
            }
            if (strpos($attributes, 'aria-label=') === false) {
                $attributes .= ' aria-label="' . esc_attr__('Data table', 'recruitpro') . '"';
            }
            return '<table' . $attributes . '>';
        }, $content);
        
        // Add scope attributes to table headers
        $content = str_replace('<th>', '<th scope="col">', $content);
        
        return $content;
    }

    /**
     * Enhance menu accessibility
     */
    public static function enhance_menu_accessibility($args) {
        if (!isset($args['container_id'])) {
            $args['container_id'] = 'main-navigation';
        }
        
        if (!isset($args['menu_id'])) {
            $args['menu_id'] = 'primary-menu';
        }
        
        return $args;
    }

    /**
     * Add accessibility attributes to menu items
     */
    public static function add_menu_accessibility_attributes($items, $args) {
        // Add ARIA attributes for dropdown menus
        $items = preg_replace_callback(
            '/(<li[^>]*class="[^"]*menu-item-has-children[^"]*"[^>]*>.*?<a[^>]*>.*?<\/a>)/s',
            function($matches) {
                $link = $matches[1];
                if (strpos($link, 'aria-haspopup') === false) {
                    $link = str_replace('<a', '<a aria-haspopup="true" aria-expanded="false"', $link);
                }
                return $link;
            },
            $items
        );
        
        return $items;
    }

    /**
     * Enhance job card accessibility
     */
    public static function enhance_job_card_accessibility($content) {
        // Add ARIA labels and roles for job cards
        $content = str_replace(
            '<article class="job-card',
            '<article class="job-card" role="article" aria-labelledby="job-title-' . get_the_ID() . '"',
            $content
        );
        
        // Enhance job title with proper heading structure
        $content = preg_replace(
            '/<h([1-6])([^>]*class="[^"]*job-title[^"]*"[^>]*)>/',
            '<h$1$2 id="job-title-' . get_the_ID() . '">',
            $content
        );
        
        return $content;
    }

    /**
     * Enhance job form accessibility
     */
    public static function enhance_job_form_accessibility($content) {
        // Add fieldsets and legends for form sections
        $content = preg_replace(
            '/(<div class="form-section[^"]*"[^>]*>)/',
            '$1<fieldset><legend class="sr-only">' . __('Form section', 'recruitpro') . '</legend>',
            $content
        );
        
        // Add required field indicators
        $content = str_replace(
            'required>',
            'required aria-required="true" aria-describedby="field-help-' . uniqid() . '">',
            $content
        );
        
        return $content;
    }

    /**
     * Enhance widget title accessibility
     */
    public static function enhance_widget_title_accessibility($title, $instance, $id_base) {
        if (empty($title)) {
            return $title;
        }
        
        // Add proper heading structure
        $title = '<span class="widget-title-text">' . $title . '</span>';
        
        return $title;
    }

    /**
     * Enhance image accessibility
     */
    public static function enhance_image_accessibility($attr) {
        // Ensure all images have alt text
        if (empty($attr['alt']) && !empty($attr['src'])) {
            $attr['alt'] = __('Image', 'recruitpro');
        }
        
        // Add loading attribute for better performance
        if (empty($attr['loading'])) {
            $attr['loading'] = 'lazy';
        }
        
        return $attr;
    }

    /**
     * Enhance admin accessibility for recruitment team
     */
    public static function enhance_admin_accessibility() {
        // Add admin accessibility enhancements
        add_action('admin_enqueue_scripts', function() {
            wp_enqueue_script(
                'recruitpro-admin-accessibility',
                get_template_directory_uri() . '/assets/js/admin-accessibility.js',
                array('jquery'),
                wp_get_theme()->get('Version'),
                true
            );
        });
        
        // Add accessibility notices for recruitment team
        add_action('admin_notices', function() {
            if (current_user_can('manage_options')) {
                $accessibility_issues = self::check_accessibility_issues();
                if (!empty($accessibility_issues)) {
                    echo '<div class="notice notice-warning is-dismissible accessibility-notice">';
                    echo '<p><strong>' . __('Accessibility Issues Detected', 'recruitpro') . '</strong></p>';
                    echo '<ul>';
                    foreach ($accessibility_issues as $issue) {
                        echo '<li>' . esc_html($issue) . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
            }
        });
    }

    /**
     * Check for common accessibility issues
     */
    private static function check_accessibility_issues() {
        $issues = array();
        
        // Check if alt text is missing on images
        $images_without_alt = get_posts(array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'meta_query' => array(
                array(
                    'key' => '_wp_attachment_image_alt',
                    'compare' => 'NOT EXISTS'
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($images_without_alt)) {
            $issues[] = __('Some images are missing alt text. This affects screen reader users.', 'recruitpro');
        }
        
        // Check if menu has proper structure
        $menu_locations = get_nav_menu_locations();
        if (empty($menu_locations)) {
            $issues[] = __('Navigation menus are not properly configured for accessibility.', 'recruitpro');
        }
        
        return $issues;
    }

    /**
     * Helper function to check if page has job listings
     */
    private static function has_job_listings() {
        return is_post_type_archive('job') || is_tax('job_category') || is_tax('job_location') || is_page_template('page-jobs.php');
    }

    /**
     * Helper function to check if page has job search
     */
    private static function has_job_search() {
        return is_search() || is_page_template('page-job-search.php') || self::has_job_listings();
    }
}

// Initialize accessibility features
RecruitPro_Accessibility::init();

/**
 * Accessibility utility functions
 */

/**
 * Output screen reader only text
 */
function recruitpro_sr_only($text) {
    return '<span class="sr-only">' . esc_html($text) . '</span>';
}

/**
 * Output ARIA live region announcement
 */
function recruitpro_announce($message, $priority = 'polite') {
    if (wp_doing_ajax()) {
        wp_send_json_success(array(
            'announcement' => esc_html($message),
            'priority' => $priority
        ));
    }
}

/**
 * Check if accessibility toolbar should be shown
 */
function recruitpro_show_accessibility_toolbar() {
    return get_theme_mod('recruitpro_show_accessibility_toolbar', true);
}

/**
 * Get accessibility statement URL
 */
function recruitpro_get_accessibility_statement_url() {
    $url = get_theme_mod('recruitpro_accessibility_statement_url', home_url('/accessibility/'));
    return esc_url($url);
}

/**
 * Output accessibility-compliant heading
 */
function recruitpro_accessibility_heading($level, $text, $class = '', $id = '') {
    $level = absint($level);
    if ($level < 1 || $level > 6) {
        $level = 2;
    }
    
    $attributes = '';
    if (!empty($class)) {
        $attributes .= ' class="' . esc_attr($class) . '"';
    }
    if (!empty($id)) {
        $attributes .= ' id="' . esc_attr($id) . '"';
    }
    
    echo "<h{$level}{$attributes}>" . esc_html($text) . "</h{$level}>";
}

/**
 * Output accessibility-compliant button
 */
function recruitpro_accessibility_button($text, $action = '', $class = '', $attributes = array()) {
    $class = 'btn ' . $class;
    $attrs = 'class="' . esc_attr($class) . '"';
    
    if (!empty($action)) {
        $attrs .= ' data-action="' . esc_attr($action) . '"';
    }
    
    foreach ($attributes as $name => $value) {
        $attrs .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
    }
    
    echo '<button type="button" ' . $attrs . '>' . esc_html($text) . '</button>';
}