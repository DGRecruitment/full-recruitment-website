<?php
/**
 * RecruitPro Theme Shortcodes System
 *
 * Basic shortcodes for recruitment websites including content display,
 * company information, team members, and recruitment-specific elements.
 * These are theme-level shortcodes that work alongside plugin shortcodes.
 *
 * @package RecruitPro
 * @subpackage Theme/Shortcodes
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/shortcodes.php
 * Purpose: Theme-level shortcodes for recruitment content
 * Dependencies: WordPress core, theme functions
 * Features: Content display, company info, team showcase, testimonials
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Shortcodes Manager Class
 * 
 * Handles all theme-level shortcodes for recruitment websites
 * without conflicting with plugin functionality.
 *
 * @since 1.0.0
 */
class RecruitPro_Shortcodes_Manager {

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_shortcodes();
        $this->init_hooks();
    }

    /**
     * Initialize all shortcodes
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_shortcodes() {
        // Company Information Shortcodes
        add_shortcode('recruitpro_company_info', array($this, 'company_info_shortcode'));
        add_shortcode('recruitpro_contact_info', array($this, 'contact_info_shortcode'));
        add_shortcode('recruitpro_business_hours', array($this, 'business_hours_shortcode'));
        add_shortcode('recruitpro_social_links', array($this, 'social_links_shortcode'));
        
        // Content Display Shortcodes
        add_shortcode('recruitpro_button', array($this, 'button_shortcode'));
        add_shortcode('recruitpro_highlight', array($this, 'highlight_shortcode'));
        add_shortcode('recruitpro_alert', array($this, 'alert_shortcode'));
        add_shortcode('recruitpro_columns', array($this, 'columns_shortcode'));
        add_shortcode('recruitpro_column', array($this, 'column_shortcode'));
        
        // Recruitment Specific Shortcodes
        add_shortcode('recruitpro_testimonials', array($this, 'testimonials_shortcode'));
        add_shortcode('recruitpro_team_members', array($this, 'team_members_shortcode'));
        add_shortcode('recruitpro_services', array($this, 'services_shortcode'));
        add_shortcode('recruitpro_stats', array($this, 'stats_shortcode'));
        add_shortcode('recruitpro_process_steps', array($this, 'process_steps_shortcode'));
        
        // Content Integration Shortcodes
        add_shortcode('recruitpro_recent_posts', array($this, 'recent_posts_shortcode'));
        add_shortcode('recruitpro_newsletter_form', array($this, 'newsletter_form_shortcode'));
        add_shortcode('recruitpro_search_form', array($this, 'search_form_shortcode'));
        
        // Layout and Design Shortcodes
        add_shortcode('recruitpro_hero_section', array($this, 'hero_section_shortcode'));
        add_shortcode('recruitpro_cta_section', array($this, 'cta_section_shortcode'));
        add_shortcode('recruitpro_icon_box', array($this, 'icon_box_shortcode'));
        add_shortcode('recruitpro_accordion', array($this, 'accordion_shortcode'));
        add_shortcode('recruitpro_tabs', array($this, 'tabs_shortcode'));
        
        // Job-related Display Shortcodes (basic, non-conflicting)
        add_shortcode('recruitpro_job_search_basic', array($this, 'job_search_basic_shortcode'));
        add_shortcode('recruitpro_job_categories', array($this, 'job_categories_shortcode'));
        add_shortcode('recruitpro_career_advice', array($this, 'career_advice_shortcode'));
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Enqueue shortcode styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_shortcode_styles'));
        
        // Add shortcode support to widgets
        add_filter('widget_text', 'do_shortcode');
        
        // Register shortcode UI (for Classic Editor)
        add_action('media_buttons', array($this, 'add_shortcode_button'));
        add_action('admin_footer', array($this, 'shortcode_popup_content'));
        
        // Admin scripts
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    /**
     * Company Information Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Company information HTML
     */
    public function company_info_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show' => 'name,description', // name, description, founded, employees
            'style' => 'default',
            'class' => '',
        ), $atts);

        $show_items = array_map('trim', explode(',', $atts['show']));
        $classes = array('recruitpro-company-info', 'style-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if (in_array('name', $show_items)): ?>
                <div class="company-name">
                    <h3><?php echo esc_html(get_theme_mod('recruitpro_company_name', get_bloginfo('name'))); ?></h3>
                </div>
            <?php endif; ?>
            
            <?php if (in_array('description', $show_items)): ?>
                <div class="company-description">
                    <p><?php echo esc_html(get_theme_mod('recruitpro_company_description', get_bloginfo('description'))); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (in_array('founded', $show_items)): ?>
                <?php $founded = get_theme_mod('recruitpro_company_founded', ''); ?>
                <?php if ($founded): ?>
                    <div class="company-founded">
                        <span class="label"><?php _e('Founded:', 'recruitpro'); ?></span>
                        <span class="value"><?php echo esc_html($founded); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (in_array('employees', $show_items)): ?>
                <?php $employees = get_theme_mod('recruitpro_company_employees', ''); ?>
                <?php if ($employees): ?>
                    <div class="company-employees">
                        <span class="label"><?php _e('Team Size:', 'recruitpro'); ?></span>
                        <span class="value"><?php echo esc_html($employees); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Contact Information Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Contact information HTML
     */
    public function contact_info_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show' => 'phone,email,address', // phone, email, address, hours
            'style' => 'list', // list, inline, card
            'icons' => 'true',
            'class' => '',
        ), $atts);

        $show_items = array_map('trim', explode(',', $atts['show']));
        $show_icons = ($atts['icons'] === 'true');
        $classes = array('recruitpro-contact-info', 'style-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if (in_array('phone', $show_items)): ?>
                <?php $phone = get_theme_mod('recruitpro_contact_phone', ''); ?>
                <?php if ($phone): ?>
                    <div class="contact-item contact-phone">
                        <?php if ($show_icons): ?>
                            <span class="contact-icon">📞</span>
                        <?php endif; ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (in_array('email', $show_items)): ?>
                <?php $email = get_theme_mod('recruitpro_contact_email', ''); ?>
                <?php if ($email): ?>
                    <div class="contact-item contact-email">
                        <?php if ($show_icons): ?>
                            <span class="contact-icon">✉️</span>
                        <?php endif; ?>
                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (in_array('address', $show_items)): ?>
                <?php $address = get_theme_mod('recruitpro_contact_address', ''); ?>
                <?php if ($address): ?>
                    <div class="contact-item contact-address">
                        <?php if ($show_icons): ?>
                            <span class="contact-icon">📍</span>
                        <?php endif; ?>
                        <span><?php echo esc_html($address); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (in_array('hours', $show_items)): ?>
                <?php $hours = get_theme_mod('recruitpro_business_hours', ''); ?>
                <?php if ($hours): ?>
                    <div class="contact-item contact-hours">
                        <?php if ($show_icons): ?>
                            <span class="contact-icon">🕒</span>
                        <?php endif; ?>
                        <span><?php echo esc_html($hours); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Button Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Button text
     * @return string Button HTML
     */
    public function button_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'url' => '#',
            'style' => 'primary', // primary, secondary, outline, ghost
            'size' => 'medium', // small, medium, large
            'target' => '_self',
            'icon' => '',
            'class' => '',
        ), $atts);

        $classes = array(
            'recruitpro-button',
            'btn',
            'btn-' . $atts['style'],
            'btn-' . $atts['size']
        );
        
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $icon_html = '';
        if (!empty($atts['icon'])) {
            $icon_html = '<span class="btn-icon">' . esc_html($atts['icon']) . '</span>';
        }

        return sprintf(
            '<a href="%s" target="%s" class="%s">%s%s</a>',
            esc_url($atts['url']),
            esc_attr($atts['target']),
            esc_attr(implode(' ', $classes)),
            $icon_html,
            wp_kses_post($content)
        );
    }

    /**
     * Highlight/Callout Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Content to highlight
     * @return string Highlighted content HTML
     */
    public function highlight_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'type' => 'info', // info, success, warning, error
            'title' => '',
            'icon' => '',
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-highlight', 'highlight-' . $atts['type']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if (!empty($atts['title']) || !empty($atts['icon'])): ?>
                <div class="highlight-header">
                    <?php if (!empty($atts['icon'])): ?>
                        <span class="highlight-icon"><?php echo esc_html($atts['icon']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($atts['title'])): ?>
                        <h4 class="highlight-title"><?php echo esc_html($atts['title']); ?></h4>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="highlight-content">
                <?php echo wp_kses_post(do_shortcode($content)); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Alert Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Alert content
     * @return string Alert HTML
     */
    public function alert_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'type' => 'info', // info, success, warning, danger
            'dismissible' => 'false',
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-alert', 'alert', 'alert-' . $atts['type']);
        if ($atts['dismissible'] === 'true') {
            $classes[] = 'alert-dismissible';
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $dismiss_button = '';
        if ($atts['dismissible'] === 'true') {
            $dismiss_button = '<button type="button" class="alert-dismiss" aria-label="' . __('Close', 'recruitpro') . '">×</button>';
        }

        return sprintf(
            '<div class="%s" role="alert">%s%s</div>',
            esc_attr(implode(' ', $classes)),
            wp_kses_post(do_shortcode($content)),
            $dismiss_button
        );
    }

    /**
     * Columns Container Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Column content
     * @return string Columns container HTML
     */
    public function columns_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'gap' => 'medium', // small, medium, large
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-columns', 'columns-gap-' . $atts['gap']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        return sprintf(
            '<div class="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            do_shortcode($content)
        );
    }

    /**
     * Individual Column Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Column content
     * @return string Column HTML
     */
    public function column_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'width' => '1/2', // 1/2, 1/3, 2/3, 1/4, 3/4, 1/6, 5/6
            'class' => '',
        ), $atts);

        // Convert fraction to class name
        $width_class = 'col-' . str_replace('/', '-', $atts['width']);
        
        $classes = array('recruitpro-column', $width_class);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        return sprintf(
            '<div class="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            do_shortcode($content)
        );
    }

    /**
     * Testimonials Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Testimonials HTML
     */
    public function testimonials_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => 3,
            'category' => '',
            'style' => 'grid', // grid, slider, list
            'show_image' => 'true',
            'show_company' => 'true',
            'class' => '',
        ), $atts);

        // Get testimonials from customizer or posts
        $testimonials = $this->get_testimonials_data($atts['count'], $atts['category']);
        
        if (empty($testimonials)) {
            return '<p>' . __('No testimonials found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-testimonials', 'testimonials-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-item">
                    <div class="testimonial-content">
                        <blockquote>
                            <p><?php echo wp_kses_post($testimonial['content']); ?></p>
                        </blockquote>
                    </div>
                    <div class="testimonial-author">
                        <?php if ($atts['show_image'] === 'true' && !empty($testimonial['image'])): ?>
                            <div class="author-image">
                                <img src="<?php echo esc_url($testimonial['image']); ?>" alt="<?php echo esc_attr($testimonial['name']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="author-info">
                            <cite class="author-name"><?php echo esc_html($testimonial['name']); ?></cite>
                            <?php if (!empty($testimonial['position'])): ?>
                                <span class="author-position"><?php echo esc_html($testimonial['position']); ?></span>
                            <?php endif; ?>
                            <?php if ($atts['show_company'] === 'true' && !empty($testimonial['company'])): ?>
                                <span class="author-company"><?php echo esc_html($testimonial['company']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Team Members Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Team members HTML
     */
    public function team_members_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => 4,
            'department' => '',
            'style' => 'grid', // grid, list, slider
            'show_bio' => 'true',
            'show_social' => 'true',
            'class' => '',
        ), $atts);

        $team_members = $this->get_team_members_data($atts['count'], $atts['department']);
        
        if (empty($team_members)) {
            return '<p>' . __('No team members found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-team-members', 'team-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($team_members as $member): ?>
                <div class="team-member-item">
                    <?php if (!empty($member['image'])): ?>
                        <div class="member-image">
                            <img src="<?php echo esc_url($member['image']); ?>" alt="<?php echo esc_attr($member['name']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="member-info">
                        <h4 class="member-name"><?php echo esc_html($member['name']); ?></h4>
                        <?php if (!empty($member['position'])): ?>
                            <span class="member-position"><?php echo esc_html($member['position']); ?></span>
                        <?php endif; ?>
                        <?php if ($atts['show_bio'] === 'true' && !empty($member['bio'])): ?>
                            <p class="member-bio"><?php echo wp_kses_post($member['bio']); ?></p>
                        <?php endif; ?>
                        <?php if ($atts['show_social'] === 'true' && !empty($member['social'])): ?>
                            <div class="member-social">
                                <?php foreach ($member['social'] as $platform => $url): ?>
                                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" class="social-link social-<?php echo esc_attr($platform); ?>">
                                        <span class="screen-reader-text"><?php echo esc_html(ucfirst($platform)); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Stats/Numbers Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Stats HTML
     */
    public function stats_shortcode($atts) {
        $atts = shortcode_atts(array(
            'stats' => '', // JSON string or comma-separated values
            'style' => 'grid', // grid, inline, card
            'animated' => 'true',
            'class' => '',
        ), $atts);

        $stats_data = $this->parse_stats_data($atts['stats']);
        
        if (empty($stats_data)) {
            return '<p>' . __('No statistics data provided.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-stats', 'stats-' . $atts['style']);
        if ($atts['animated'] === 'true') {
            $classes[] = 'stats-animated';
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($stats_data as $stat): ?>
                <div class="stat-item">
                    <div class="stat-number" data-target="<?php echo esc_attr($stat['number']); ?>">
                        <?php echo esc_html($stat['number']); ?>
                    </div>
                    <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
                    <?php if (!empty($stat['description'])): ?>
                        <div class="stat-description"><?php echo wp_kses_post($stat['description']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Icon Box Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Box content
     * @return string Icon box HTML
     */
    public function icon_box_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'icon' => '',
            'title' => '',
            'style' => 'default', // default, bordered, filled
            'link' => '',
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-icon-box', 'icon-box-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $is_linked = !empty($atts['link']);
        $tag = $is_linked ? 'a' : 'div';
        $tag_attrs = $is_linked ? 'href="' . esc_url($atts['link']) . '"' : '';

        ob_start();
        ?>
        <<?php echo $tag; ?> class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo $tag_attrs; ?>>
            <?php if (!empty($atts['icon'])): ?>
                <div class="icon-box-icon">
                    <span class="icon"><?php echo wp_kses_post($atts['icon']); ?></span>
                </div>
            <?php endif; ?>
            <div class="icon-box-content">
                <?php if (!empty($atts['title'])): ?>
                    <h4 class="icon-box-title"><?php echo esc_html($atts['title']); ?></h4>
                <?php endif; ?>
                <?php if (!empty($content)): ?>
                    <div class="icon-box-description">
                        <?php echo wp_kses_post(do_shortcode($content)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </<?php echo $tag; ?>>
        <?php
        return ob_get_clean();
    }

    /**
     * Newsletter Form Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Newsletter form HTML
     */
    public function newsletter_form_shortcode($atts) {
        // Use the newsletter integration if available
        if (function_exists('recruitpro_display_newsletter_form')) {
            return recruitpro_display_newsletter_form($atts);
        }

        $atts = shortcode_atts(array(
            'title' => __('Subscribe to Our Newsletter', 'recruitpro'),
            'description' => __('Get the latest job opportunities and recruitment insights.', 'recruitpro'),
            'style' => 'default',
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-newsletter-form', 'newsletter-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if (!empty($atts['title'])): ?>
                <h3 class="newsletter-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            <?php if (!empty($atts['description'])): ?>
                <p class="newsletter-description"><?php echo esc_html($atts['description']); ?></p>
            <?php endif; ?>
            <form class="newsletter-signup-form" method="post">
                <div class="form-group">
                    <input type="email" name="email" placeholder="<?php esc_attr_e('Enter your email address', 'recruitpro'); ?>" required>
                    <button type="submit" class="btn btn-primary">
                        <?php _e('Subscribe', 'recruitpro'); ?>
                    </button>
                </div>
                <div class="form-privacy">
                    <small><?php _e('We respect your privacy. Unsubscribe at any time.', 'recruitpro'); ?></small>
                </div>
                <?php wp_nonce_field('recruitpro_newsletter_signup', 'newsletter_nonce'); ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Recent Posts Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Recent posts HTML
     */
    public function recent_posts_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => 3,
            'category' => '',
            'show_excerpt' => 'true',
            'show_date' => 'true',
            'show_author' => 'false',
            'style' => 'grid', // grid, list
            'class' => '',
        ), $atts);

        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => intval($atts['count']),
            'post_status' => 'publish',
        );

        if (!empty($atts['category'])) {
            $query_args['category_name'] = $atts['category'];
        }

        $posts = new WP_Query($query_args);
        
        if (!$posts->have_posts()) {
            return '<p>' . __('No posts found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-recent-posts', 'posts-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php while ($posts->have_posts()): $posts->the_post(); ?>
                <article class="post-item">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="post-content">
                        <h4 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <?php if ($atts['show_date'] === 'true' || $atts['show_author'] === 'true'): ?>
                            <div class="post-meta">
                                <?php if ($atts['show_date'] === 'true'): ?>
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                <?php endif; ?>
                                <?php if ($atts['show_author'] === 'true'): ?>
                                    <span class="post-author"><?php the_author(); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($atts['show_excerpt'] === 'true'): ?>
                            <div class="post-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Job Search Basic Shortcode (non-conflicting)
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Basic job search form HTML
     */
    public function job_search_basic_shortcode($atts) {
        $atts = shortcode_atts(array(
            'placeholder' => __('Search jobs...', 'recruitpro'),
            'style' => 'default',
            'show_location' => 'true',
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-job-search-basic', 'job-search-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $action_url = recruitpro_has_jobs_plugin() ? home_url('/jobs/') : home_url('/');

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <form class="job-search-form" method="get" action="<?php echo esc_url($action_url); ?>">
                <div class="search-fields">
                    <div class="search-field">
                        <input type="search" name="s" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
                    </div>
                    <?php if ($atts['show_location'] === 'true'): ?>
                        <div class="location-field">
                            <input type="text" name="location" placeholder="<?php esc_attr_e('Location', 'recruitpro'); ?>" value="<?php echo esc_attr($_GET['location'] ?? ''); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="search-submit">
                        <button type="submit" class="btn btn-primary">
                            <?php _e('Search Jobs', 'recruitpro'); ?>
                        </button>
                    </div>
                </div>
                <?php if (recruitpro_has_jobs_plugin()): ?>
                    <input type="hidden" name="post_type" value="job">
                <?php endif; ?>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * CTA Section Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content CTA content
     * @return string CTA section HTML
     */
    public function cta_section_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'title' => '',
            'subtitle' => '',
            'button_text' => __('Get Started', 'recruitpro'),
            'button_url' => '#',
            'button_style' => 'primary',
            'background' => '',
            'style' => 'default', // default, centered, split
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-cta-section', 'cta-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $style_attr = '';
        if (!empty($atts['background'])) {
            $style_attr = 'style="background-image: url(' . esc_url($atts['background']) . ');"';
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo $style_attr; ?>>
            <div class="cta-content">
                <?php if (!empty($atts['title'])): ?>
                    <h2 class="cta-title"><?php echo esc_html($atts['title']); ?></h2>
                <?php endif; ?>
                <?php if (!empty($atts['subtitle'])): ?>
                    <p class="cta-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
                <?php if (!empty($content)): ?>
                    <div class="cta-description">
                        <?php echo wp_kses_post(do_shortcode($content)); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($atts['button_text'])): ?>
                    <div class="cta-button">
                        <a href="<?php echo esc_url($atts['button_url']); ?>" class="btn btn-<?php echo esc_attr($atts['button_style']); ?>">
                            <?php echo esc_html($atts['button_text']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get testimonials data from customizer or fallback
     * 
     * @since 1.0.0
     * @param int $count Number of testimonials
     * @param string $category Category filter
     * @return array Testimonials data
     */
    private function get_testimonials_data($count, $category = '') {
        // Try to get from customizer first
        $testimonials = get_theme_mod('recruitpro_testimonials', array());
        
        if (empty($testimonials)) {
            // Fallback sample data
            $testimonials = array(
                array(
                    'content' => __('RecruitPro helped us find the perfect candidates for our team. Their expertise in our industry made all the difference.', 'recruitpro'),
                    'name' => __('Sarah Johnson', 'recruitpro'),
                    'position' => __('HR Director', 'recruitpro'),
                    'company' => __('Tech Innovations Inc.', 'recruitpro'),
                    'image' => '',
                ),
                array(
                    'content' => __('Professional, efficient, and results-driven. We highly recommend their recruitment services.', 'recruitpro'),
                    'name' => __('Michael Chen', 'recruitpro'),
                    'position' => __('CEO', 'recruitpro'),
                    'company' => __('Digital Solutions Ltd.', 'recruitpro'),
                    'image' => '',
                ),
                array(
                    'content' => __('Outstanding support throughout the entire hiring process. They truly understand our needs.', 'recruitpro'),
                    'name' => __('Emily Rodriguez', 'recruitpro'),
                    'position' => __('Operations Manager', 'recruitpro'),
                    'company' => __('Growth Partners', 'recruitpro'),
                    'image' => '',
                ),
            );
        }
        
        return array_slice($testimonials, 0, $count);
    }

    /**
     * Get team members data from customizer or fallback
     * 
     * @since 1.0.0
     * @param int $count Number of team members
     * @param string $department Department filter
     * @return array Team members data
     */
    private function get_team_members_data($count, $department = '') {
        // Try to get from customizer first
        $team_members = get_theme_mod('recruitpro_team_members', array());
        
        if (empty($team_members)) {
            // Fallback sample data
            $team_members = array(
                array(
                    'name' => __('John Smith', 'recruitpro'),
                    'position' => __('Senior Recruitment Consultant', 'recruitpro'),
                    'bio' => __('Specialized in technology and engineering recruitment with over 8 years of experience.', 'recruitpro'),
                    'image' => '',
                    'social' => array(),
                ),
                array(
                    'name' => __('Lisa Brown', 'recruitpro'),
                    'position' => __('Healthcare Recruitment Specialist', 'recruitpro'),
                    'bio' => __('Expert in healthcare and medical recruitment, connecting top talent with leading institutions.', 'recruitpro'),
                    'image' => '',
                    'social' => array(),
                ),
                array(
                    'name' => __('David Wilson', 'recruitpro'),
                    'position' => __('Executive Search Director', 'recruitpro'),
                    'bio' => __('Leading executive search and C-level recruitment across multiple industries.', 'recruitpro'),
                    'image' => '',
                    'social' => array(),
                ),
                array(
                    'name' => __('Maria Garcia', 'recruitpro'),
                    'position' => __('Finance Recruitment Manager', 'recruitpro'),
                    'bio' => __('Specializing in finance and accounting roles, from entry-level to senior management.', 'recruitpro'),
                    'image' => '',
                    'social' => array(),
                ),
            );
        }
        
        return array_slice($team_members, 0, $count);
    }

    /**
     * Parse stats data from shortcode attribute
     * 
     * @since 1.0.0
     * @param string $stats_string Stats data string
     * @return array Parsed stats data
     */
    private function parse_stats_data($stats_string) {
        if (empty($stats_string)) {
            // Default stats for recruitment agency
            return array(
                array(
                    'number' => '500+',
                    'label' => __('Successful Placements', 'recruitpro'),
                    'description' => '',
                ),
                array(
                    'number' => '50+',
                    'label' => __('Partner Companies', 'recruitpro'),
                    'description' => '',
                ),
                array(
                    'number' => '95%',
                    'label' => __('Client Satisfaction', 'recruitpro'),
                    'description' => '',
                ),
                array(
                    'number' => '10+',
                    'label' => __('Years Experience', 'recruitpro'),
                    'description' => '',
                ),
            );
        }

        // Try to decode as JSON first
        $json_data = json_decode($stats_string, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json_data;
        }

        // Parse as simple format: number:label,number:label
        $stats = array();
        $pairs = explode(',', $stats_string);
        
        foreach ($pairs as $pair) {
            $parts = explode(':', trim($pair));
            if (count($parts) === 2) {
                $stats[] = array(
                    'number' => trim($parts[0]),
                    'label' => trim($parts[1]),
                    'description' => '',
                );
            }
        }
        
        return $stats;
    }

    /**
     * Enqueue shortcode styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_shortcode_styles() {
        wp_enqueue_style(
            'recruitpro-shortcodes',
            get_template_directory_uri() . '/assets/css/shortcodes.css',
            array(),
            RECRUITPRO_THEME_VERSION
        );

        // Enqueue scripts for interactive shortcodes
        wp_enqueue_script(
            'recruitpro-shortcodes',
            get_template_directory_uri() . '/assets/js/shortcodes.js',
            array('jquery'),
            RECRUITPRO_THEME_VERSION,
            true
        );
    }

    /**
     * Add shortcode button to editor
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_shortcode_button() {
        if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
            echo '<button type="button" id="recruitpro-shortcode-button" class="button" title="' . __('RecruitPro Shortcodes', 'recruitpro') . '">';
            echo '<span class="wp-media-buttons-icon dashicons dashicons-admin-tools"></span> ';
            echo __('RecruitPro Shortcodes', 'recruitpro');
            echo '</button>';
        }
    }

    /**
     * Shortcode popup content
     * 
     * @since 1.0.0
     * @return void
     */
    public function shortcode_popup_content() {
        ?>
        <div id="recruitpro-shortcode-popup" style="display: none;">
            <div class="shortcode-categories">
                <h3><?php _e('RecruitPro Shortcodes', 'recruitpro'); ?></h3>
                <div class="shortcode-category">
                    <h4><?php _e('Company Information', 'recruitpro'); ?></h4>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_company_info show=&quot;name,description&quot;]">
                        <?php _e('Company Info', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_contact_info show=&quot;phone,email,address&quot;]">
                        <?php _e('Contact Info', 'recruitpro'); ?>
                    </button>
                </div>
                <div class="shortcode-category">
                    <h4><?php _e('Content Elements', 'recruitpro'); ?></h4>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_button url=&quot;#&quot; style=&quot;primary&quot;]Button Text[/recruitpro_button]">
                        <?php _e('Button', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_highlight type=&quot;info&quot; title=&quot;Title&quot;]Content[/recruitpro_highlight]">
                        <?php _e('Highlight Box', 'recruitpro'); ?>
                    </button>
                </div>
                <div class="shortcode-category">
                    <h4><?php _e('Recruitment Content', 'recruitpro'); ?></h4>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_testimonials count=&quot;3&quot; style=&quot;grid&quot;]">
                        <?php _e('Testimonials', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_team_members count=&quot;4&quot; style=&quot;grid&quot;]">
                        <?php _e('Team Members', 'recruitpro'); ?>
                    </button>
                    <button type="button" class="shortcode-insert button" data-shortcode="[recruitpro_stats]">
                        <?php _e('Statistics', 'recruitpro'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Admin scripts for shortcode popup
     * 
     * @since 1.0.0
     * @param string $hook Current admin page
     * @return void
     */
    public function admin_scripts($hook) {
        if (in_array($hook, array('post.php', 'post-new.php', 'page.php', 'page-new.php'))) {
            wp_enqueue_script(
                'recruitpro-shortcode-admin',
                get_template_directory_uri() . '/assets/js/shortcode-admin.js',
                array('jquery'),
                RECRUITPRO_THEME_VERSION,
                true
            );

            wp_enqueue_style(
                'recruitpro-shortcode-admin',
                get_template_directory_uri() . '/assets/css/shortcode-admin.css',
                array(),
                RECRUITPRO_THEME_VERSION
            );
        }
    }

    /**
     * Additional shortcodes for advanced functionality
     */

    /**
     * Business Hours Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Business hours HTML
     */
    public function business_hours_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'list', // list, table
            'show_current' => 'true',
            'class' => '',
        ), $atts);

        $business_hours = get_theme_mod('recruitpro_business_hours_detailed', array(
            'monday' => '9:00 AM - 5:00 PM',
            'tuesday' => '9:00 AM - 5:00 PM',
            'wednesday' => '9:00 AM - 5:00 PM',
            'thursday' => '9:00 AM - 5:00 PM',
            'friday' => '9:00 AM - 5:00 PM',
            'saturday' => 'Closed',
            'sunday' => 'Closed',
        ));

        $classes = array('recruitpro-business-hours', 'hours-' . $atts['style']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $current_day = strtolower(date('l'));
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php if ($atts['style'] === 'table'): ?>
                <table class="hours-table">
                    <?php foreach ($business_hours as $day => $hours): ?>
                        <tr class="<?php echo ($day === $current_day && $atts['show_current'] === 'true') ? 'current-day' : ''; ?>">
                            <td class="day-name"><?php echo esc_html(ucfirst($day)); ?></td>
                            <td class="day-hours"><?php echo esc_html($hours); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <ul class="hours-list">
                    <?php foreach ($business_hours as $day => $hours): ?>
                        <li class="hours-item <?php echo ($day === $current_day && $atts['show_current'] === 'true') ? 'current-day' : ''; ?>">
                            <span class="day-name"><?php echo esc_html(ucfirst($day)); ?></span>
                            <span class="day-hours"><?php echo esc_html($hours); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Social Links Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Social links HTML
     */
    public function social_links_shortcode($atts) {
        $atts = shortcode_atts(array(
            'platforms' => 'linkedin,facebook,twitter', // comma-separated
            'style' => 'icons', // icons, text, both
            'size' => 'medium', // small, medium, large
            'target' => '_blank',
            'class' => '',
        ), $atts);

        $platforms = array_map('trim', explode(',', $atts['platforms']));
        $social_links = array(
            'linkedin' => get_theme_mod('recruitpro_social_linkedin', ''),
            'facebook' => get_theme_mod('recruitpro_social_facebook', ''),
            'twitter' => get_theme_mod('recruitpro_social_twitter', ''),
            'instagram' => get_theme_mod('recruitpro_social_instagram', ''),
            'youtube' => get_theme_mod('recruitpro_social_youtube', ''),
        );

        $classes = array('recruitpro-social-links', 'social-' . $atts['style'], 'social-' . $atts['size']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $has_links = false;
        foreach ($platforms as $platform) {
            if (!empty($social_links[$platform])) {
                $has_links = true;
                break;
            }
        }

        if (!$has_links) {
            return '';
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($platforms as $platform): ?>
                <?php if (!empty($social_links[$platform])): ?>
                    <a href="<?php echo esc_url($social_links[$platform]); ?>" 
                       target="<?php echo esc_attr($atts['target']); ?>" 
                       rel="noopener" 
                       class="social-link social-<?php echo esc_attr($platform); ?>">
                        <?php if (in_array($atts['style'], array('icons', 'both'))): ?>
                            <span class="social-icon" aria-hidden="true"></span>
                        <?php endif; ?>
                        <?php if (in_array($atts['style'], array('text', 'both'))): ?>
                            <span class="social-text"><?php echo esc_html(ucfirst($platform)); ?></span>
                        <?php endif; ?>
                        <span class="screen-reader-text"><?php echo esc_html(ucfirst($platform)); ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Search Form Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Search form HTML
     */
    public function search_form_shortcode($atts) {
        if (function_exists('recruitpro_get_search_form')) {
            return recruitpro_get_search_form($atts);
        }

        $atts = shortcode_atts(array(
            'placeholder' => __('Search...', 'recruitpro'),
            'style' => 'default',
            'show_categories' => 'false',
            'class' => '',
        ), $atts);

        return get_search_form(false);
    }

    /**
     * Accordion Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Accordion content
     * @return string Accordion HTML
     */
    public function accordion_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'style' => 'default',
            'multiple' => 'false', // Allow multiple panels open
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-accordion', 'accordion-' . $atts['style']);
        if ($atts['multiple'] === 'true') {
            $classes[] = 'accordion-multiple';
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        return sprintf(
            '<div class="%s" data-accordion="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            esc_attr($atts['multiple']),
            do_shortcode($content)
        );
    }

    /**
     * Tabs Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Tabs content
     * @return string Tabs HTML
     */
    public function tabs_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'style' => 'default', // default, pills, underline
            'position' => 'top', // top, left, right
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-tabs', 'tabs-' . $atts['style'], 'tabs-' . $atts['position']);
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        return sprintf(
            '<div class="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            do_shortcode($content)
        );
    }

    /**
     * Job Categories Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Job categories HTML
     */
    public function job_categories_shortcode($atts) {
        if (!recruitpro_has_jobs_plugin()) {
            return '<p>' . __('Jobs plugin is not active.', 'recruitpro') . '</p>';
        }

        $atts = shortcode_atts(array(
            'count' => 8,
            'show_count' => 'true',
            'style' => 'grid', // grid, list
            'columns' => 4,
            'class' => '',
        ), $atts);

        $categories = get_terms(array(
            'taxonomy' => 'job_category',
            'number' => intval($atts['count']),
            'hide_empty' => true,
        ));

        if (empty($categories) || is_wp_error($categories)) {
            return '<p>' . __('No job categories found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-job-categories', 'categories-' . $atts['style']);
        if ($atts['style'] === 'grid') {
            $classes[] = 'categories-columns-' . $atts['columns'];
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($categories as $category): ?>
                <div class="category-item">
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-link">
                        <h4 class="category-name"><?php echo esc_html($category->name); ?></h4>
                        <?php if ($atts['show_count'] === 'true'): ?>
                            <span class="category-count">
                                <?php echo sprintf(_n('%d job', '%d jobs', $category->count, 'recruitpro'), $category->count); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($category->description)): ?>
                            <p class="category-description"><?php echo esc_html($category->description); ?></p>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Career Advice Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Career advice posts HTML
     */
    public function career_advice_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => 3,
            'category' => 'career-advice',
            'show_excerpt' => 'true',
            'show_date' => 'true',
            'style' => 'grid',
            'class' => '',
        ), $atts);

        return $this->recent_posts_shortcode($atts);
    }

    /**
     * Process Steps Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Process steps HTML
     */
    public function process_steps_shortcode($atts) {
        $atts = shortcode_atts(array(
            'steps' => '', // JSON string of steps
            'style' => 'horizontal', // horizontal, vertical
            'numbered' => 'true',
            'class' => '',
        ), $atts);

        $steps_data = $this->parse_process_steps($atts['steps']);
        
        if (empty($steps_data)) {
            return '<p>' . __('No process steps provided.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-process-steps', 'steps-' . $atts['style']);
        if ($atts['numbered'] === 'true') {
            $classes[] = 'steps-numbered';
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($steps_data as $index => $step): ?>
                <div class="process-step">
                    <?php if ($atts['numbered'] === 'true'): ?>
                        <div class="step-number"><?php echo ($index + 1); ?></div>
                    <?php endif; ?>
                    <div class="step-content">
                        <?php if (!empty($step['title'])): ?>
                            <h4 class="step-title"><?php echo esc_html($step['title']); ?></h4>
                        <?php endif; ?>
                        <?php if (!empty($step['description'])): ?>
                            <p class="step-description"><?php echo wp_kses_post($step['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Parse process steps data
     * 
     * @since 1.0.0
     * @param string $steps_string Steps data string
     * @return array Parsed steps data
     */
    private function parse_process_steps($steps_string) {
        if (empty($steps_string)) {
            // Default recruitment process steps
            return array(
                array(
                    'title' => __('Initial Consultation', 'recruitpro'),
                    'description' => __('We meet with you to understand your specific requirements and company culture.', 'recruitpro'),
                ),
                array(
                    'title' => __('Candidate Sourcing', 'recruitpro'),
                    'description' => __('Our team actively searches for qualified candidates through our extensive network.', 'recruitpro'),
                ),
                array(
                    'title' => __('Screening & Interviews', 'recruitpro'),
                    'description' => __('We conduct thorough screenings and interviews to ensure candidate quality.', 'recruitpro'),
                ),
                array(
                    'title' => __('Presentation & Placement', 'recruitpro'),
                    'description' => __('We present the best candidates and support the final placement process.', 'recruitpro'),
                ),
            );
        }

        // Try to decode as JSON
        $json_data = json_decode($steps_string, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json_data;
        }

        return array();
    }

    /**
     * Services Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Services HTML
     */
    public function services_shortcode($atts) {
        $atts = shortcode_atts(array(
            'count' => 4,
            'style' => 'grid', // grid, list
            'show_description' => 'true',
            'columns' => 2,
            'class' => '',
        ), $atts);

        $services = $this->get_services_data($atts['count']);
        
        if (empty($services)) {
            return '<p>' . __('No services found.', 'recruitpro') . '</p>';
        }

        $classes = array('recruitpro-services', 'services-' . $atts['style']);
        if ($atts['style'] === 'grid') {
            $classes[] = 'services-columns-' . $atts['columns'];
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
            <?php foreach ($services as $service): ?>
                <div class="service-item">
                    <?php if (!empty($service['icon'])): ?>
                        <div class="service-icon">
                            <?php echo wp_kses_post($service['icon']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="service-content">
                        <h4 class="service-title"><?php echo esc_html($service['title']); ?></h4>
                        <?php if ($atts['show_description'] === 'true' && !empty($service['description'])): ?>
                            <p class="service-description"><?php echo wp_kses_post($service['description']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get services data
     * 
     * @since 1.0.0
     * @param int $count Number of services
     * @return array Services data
     */
    private function get_services_data($count) {
        // Try to get from customizer first
        $services = get_theme_mod('recruitpro_services', array());
        
        if (empty($services)) {
            // Default recruitment services
            $services = array(
                array(
                    'title' => __('Permanent Recruitment', 'recruitpro'),
                    'description' => __('Find the perfect permanent employees for your organization with our comprehensive recruitment process.', 'recruitpro'),
                    'icon' => '👥',
                ),
                array(
                    'title' => __('Temporary Staffing', 'recruitpro'),
                    'description' => __('Flexible temporary staffing solutions to meet your short-term business needs and project requirements.', 'recruitpro'),
                    'icon' => '⏰',
                ),
                array(
                    'title' => __('Executive Search', 'recruitpro'),
                    'description' => __('Specialized executive search services for C-level and senior management positions across industries.', 'recruitpro'),
                    'icon' => '🎯',
                ),
                array(
                    'title' => __('Contract Recruitment', 'recruitpro'),
                    'description' => __('Connect with skilled contractors and consultants for specific projects and specialized roles.', 'recruitpro'),
                    'icon' => '📋',
                ),
            );
        }
        
        return array_slice($services, 0, $count);
    }

    /**
     * Hero Section Shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @param string $content Hero content
     * @return string Hero section HTML
     */
    public function hero_section_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'title' => '',
            'subtitle' => '',
            'background' => '',
            'height' => 'medium', // small, medium, large, full
            'overlay' => 'dark', // dark, light, none
            'text_align' => 'center', // left, center, right
            'button_text' => '',
            'button_url' => '',
            'button_style' => 'primary',
            'class' => '',
        ), $atts);

        $classes = array('recruitpro-hero-section', 'hero-' . $atts['height'], 'hero-text-' . $atts['text_align']);
        if (!empty($atts['overlay']) && $atts['overlay'] !== 'none') {
            $classes[] = 'hero-overlay-' . $atts['overlay'];
        }
        if (!empty($atts['class'])) {
            $classes[] = $atts['class'];
        }

        $style_attr = '';
        if (!empty($atts['background'])) {
            $style_attr = 'style="background-image: url(' . esc_url($atts['background']) . ');"';
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo $style_attr; ?>>
            <div class="hero-content">
                <?php if (!empty($atts['title'])): ?>
                    <h1 class="hero-title"><?php echo esc_html($atts['title']); ?></h1>
                <?php endif; ?>
                <?php if (!empty($atts['subtitle'])): ?>
                    <p class="hero-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
                <?php if (!empty($content)): ?>
                    <div class="hero-description">
                        <?php echo wp_kses_post(do_shortcode($content)); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($atts['button_text'])): ?>
                    <div class="hero-button">
                        <a href="<?php echo esc_url($atts['button_url']); ?>" class="btn btn-<?php echo esc_attr($atts['button_style']); ?>">
                            <?php echo esc_html($atts['button_text']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize the shortcodes manager
if (class_exists('RecruitPro_Shortcodes_Manager')) {
    new RecruitPro_Shortcodes_Manager();
}

/**
 * Helper function to check if shortcodes are enabled
 * 
 * @since 1.0.0
 * @return bool True if shortcodes are enabled
 */
function recruitpro_shortcodes_enabled() {
    return apply_filters('recruitpro_enable_shortcodes', true);
}

/**
 * Helper function to get available shortcodes
 * 
 * @since 1.0.0
 * @return array Available shortcodes
 */
function recruitpro_get_available_shortcodes() {
    return array(
        'company_info', 'contact_info', 'business_hours', 'social_links',
        'button', 'highlight', 'alert', 'columns', 'column',
        'testimonials', 'team_members', 'services', 'stats', 'process_steps',
        'recent_posts', 'newsletter_form', 'search_form',
        'hero_section', 'cta_section', 'icon_box', 'accordion', 'tabs',
        'job_search_basic', 'job_categories', 'career_advice'
    );
}