<?php
/**
 * RecruitPro Theme Custom Widgets
 *
 * This file contains custom widget classes specifically designed for recruitment
 * websites and HR agencies. These widgets provide professional, conversion-focused
 * functionality for showcasing services, building trust, generating leads, and
 * enhancing the user experience for both candidates and employers.
 *
 * @package RecruitPro
 * @subpackage Theme/Widgets
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/widgets.php
 * Purpose: Custom recruitment-focused widget classes
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only widgets, no plugin overlap)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * COMPANY CONTACT INFO WIDGET
 * =================================================================
 * 
 * Display professional contact information for recruitment agencies.
 * Perfect for sidebars and footer areas.
 */

class RecruitPro_Contact_Info_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'recruitpro_contact_info',
            __('RecruitPro: Contact Information', 'recruitpro'),
            array(
                'description' => __('Display professional contact information with icons and Schema markup.', 'recruitpro'),
                'classname' => 'recruitpro-contact-info-widget',
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $company_name = !empty($instance['company_name']) ? $instance['company_name'] : get_bloginfo('name');
        $address = !empty($instance['address']) ? $instance['address'] : '';
        $phone = !empty($instance['phone']) ? $instance['phone'] : '';
        $email = !empty($instance['email']) ? $instance['email'] : '';
        $hours = !empty($instance['hours']) ? $instance['hours'] : '';
        $show_schema = isset($instance['show_schema']) ? $instance['show_schema'] : true;

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        $schema_attrs = $show_schema ? 'itemscope itemtype="https://schema.org/LocalBusiness"' : '';
        ?>

        <div class="contact-info-content" <?php echo $schema_attrs; ?>>
            <?php if ($company_name && $show_schema) : ?>
                <meta itemprop="name" content="<?php echo esc_attr($company_name); ?>">
                <meta itemprop="description" content="<?php echo esc_attr__('Professional recruitment services', 'recruitpro'); ?>">
            <?php endif; ?>

            <ul class="contact-info-list">
                <?php if ($address) : ?>
                    <li class="contact-address" <?php echo $show_schema ? 'itemprop="address" itemscope itemtype="https://schema.org/PostalAddress"' : ''; ?>>
                        <i class="contact-icon fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span class="contact-text" <?php echo $show_schema ? 'itemprop="streetAddress"' : ''; ?>>
                            <?php echo wp_kses_post($address); ?>
                        </span>
                    </li>
                <?php endif; ?>

                <?php if ($phone) : ?>
                    <li class="contact-phone">
                        <i class="contact-icon fas fa-phone" aria-hidden="true"></i>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" 
                           class="contact-link" 
                           <?php echo $show_schema ? 'itemprop="telephone"' : ''; ?>>
                            <?php echo esc_html($phone); ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($email) : ?>
                    <li class="contact-email">
                        <i class="contact-icon fas fa-envelope" aria-hidden="true"></i>
                        <a href="mailto:<?php echo esc_attr($email); ?>" 
                           class="contact-link" 
                           <?php echo $show_schema ? 'itemprop="email"' : ''; ?>>
                            <?php echo esc_html($email); ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($hours) : ?>
                    <li class="contact-hours" <?php echo $show_schema ? 'itemprop="openingHours"' : ''; ?>>
                        <i class="contact-icon fas fa-clock" aria-hidden="true"></i>
                        <span class="contact-text">
                            <?php echo wp_kses_post($hours); ?>
                        </span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Widget form
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Contact Us', 'recruitpro');
        $company_name = isset($instance['company_name']) ? $instance['company_name'] : '';
        $address = isset($instance['address']) ? $instance['address'] : '';
        $phone = isset($instance['phone']) ? $instance['phone'] : '';
        $email = isset($instance['email']) ? $instance['email'] : '';
        $hours = isset($instance['hours']) ? $instance['hours'] : '';
        $show_schema = isset($instance['show_schema']) ? $instance['show_schema'] : true;
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('company_name')); ?>"><?php esc_html_e('Company Name:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('company_name')); ?>" name="<?php echo esc_attr($this->get_field_name('company_name')); ?>" type="text" value="<?php echo esc_attr($company_name); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('address')); ?>"><?php esc_html_e('Address:', 'recruitpro'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('address')); ?>" name="<?php echo esc_attr($this->get_field_name('address')); ?>" rows="3"><?php echo esc_textarea($address); ?></textarea>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('phone')); ?>"><?php esc_html_e('Phone:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('phone')); ?>" name="<?php echo esc_attr($this->get_field_name('phone')); ?>" type="text" value="<?php echo esc_attr($phone); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('email')); ?>"><?php esc_html_e('Email:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('email')); ?>" name="<?php echo esc_attr($this->get_field_name('email')); ?>" type="email" value="<?php echo esc_attr($email); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('hours')); ?>"><?php esc_html_e('Business Hours:', 'recruitpro'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('hours')); ?>" name="<?php echo esc_attr($this->get_field_name('hours')); ?>" rows="3"><?php echo esc_textarea($hours); ?></textarea>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_schema); ?> id="<?php echo esc_attr($this->get_field_id('show_schema')); ?>" name="<?php echo esc_attr($this->get_field_name('show_schema')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_schema')); ?>"><?php esc_html_e('Enable Schema.org markup', 'recruitpro'); ?></label>
        </p>

        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['company_name'] = !empty($new_instance['company_name']) ? sanitize_text_field($new_instance['company_name']) : '';
        $instance['address'] = !empty($new_instance['address']) ? wp_kses_post($new_instance['address']) : '';
        $instance['phone'] = !empty($new_instance['phone']) ? sanitize_text_field($new_instance['phone']) : '';
        $instance['email'] = !empty($new_instance['email']) ? sanitize_email($new_instance['email']) : '';
        $instance['hours'] = !empty($new_instance['hours']) ? wp_kses_post($new_instance['hours']) : '';
        $instance['show_schema'] = !empty($new_instance['show_schema']) ? 1 : 0;

        return $instance;
    }
}

/**
 * =================================================================
 * RECRUITMENT STATISTICS WIDGET
 * =================================================================
 * 
 * Display key recruitment metrics and achievements to build credibility.
 */

class RecruitPro_Statistics_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'recruitpro_statistics',
            __('RecruitPro: Statistics Counter', 'recruitpro'),
            array(
                'description' => __('Display recruitment statistics and achievements with animated counters.', 'recruitpro'),
                'classname' => 'recruitpro-statistics-widget',
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $stats = !empty($instance['stats']) ? $instance['stats'] : array();
        $layout = !empty($instance['layout']) ? $instance['layout'] : 'grid';
        $animation = isset($instance['animation']) ? $instance['animation'] : true;

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        if (!empty($stats)) :
        ?>

        <div class="statistics-content layout-<?php echo esc_attr($layout); ?>" <?php echo $animation ? 'data-animate="true"' : ''; ?>>
            <div class="statistics-grid">
                <?php foreach ($stats as $stat) : 
                    if (empty($stat['number']) && empty($stat['label'])) continue;
                ?>
                    <div class="stat-item">
                        <div class="stat-number" data-number="<?php echo esc_attr($stat['number']); ?>">
                            <?php if (!empty($stat['prefix'])) : ?>
                                <span class="stat-prefix"><?php echo esc_html($stat['prefix']); ?></span>
                            <?php endif; ?>
                            <span class="stat-value" <?php echo $animation ? 'data-counter="true"' : ''; ?>>
                                <?php echo esc_html($stat['number']); ?>
                            </span>
                            <?php if (!empty($stat['suffix'])) : ?>
                                <span class="stat-suffix"><?php echo esc_html($stat['suffix']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($stat['label'])) : ?>
                            <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($stat['description'])) : ?>
                            <div class="stat-description"><?php echo esc_html($stat['description']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($animation) : ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statWidget = document.querySelector('.recruitpro-statistics-widget');
            if (statWidget && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const counters = entry.target.querySelectorAll('[data-counter="true"]');
                            counters.forEach(function(counter) {
                                const target = parseInt(counter.dataset.number) || 0;
                                let current = 0;
                                const increment = target / 100;
                                const timer = setInterval(function() {
                                    current += increment;
                                    if (current >= target) {
                                        counter.textContent = target;
                                        clearInterval(timer);
                                    } else {
                                        counter.textContent = Math.floor(current);
                                    }
                                }, 50);
                            });
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                observer.observe(statWidget);
            }
        });
        </script>
        <?php endif; ?>

        <?php
        endif;

        echo $args['after_widget'];
    }

    /**
     * Widget form
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Our Success', 'recruitpro');
        $stats = isset($instance['stats']) ? $instance['stats'] : array(
            array('number' => '500', 'suffix' => '+', 'label' => 'Jobs Filled', 'description' => ''),
            array('number' => '1200', 'suffix' => '+', 'label' => 'Happy Candidates', 'description' => ''),
            array('number' => '98', 'suffix' => '%', 'label' => 'Success Rate', 'description' => ''),
        );
        $layout = isset($instance['layout']) ? $instance['layout'] : 'grid';
        $animation = isset($instance['animation']) ? $instance['animation'] : true;
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>"><?php esc_html_e('Layout:', 'recruitpro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" name="<?php echo esc_attr($this->get_field_name('layout')); ?>">
                <option value="grid" <?php selected($layout, 'grid'); ?>><?php esc_html_e('Grid', 'recruitpro'); ?></option>
                <option value="inline" <?php selected($layout, 'inline'); ?>><?php esc_html_e('Inline', 'recruitpro'); ?></option>
                <option value="vertical" <?php selected($layout, 'vertical'); ?>><?php esc_html_e('Vertical', 'recruitpro'); ?></option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($animation); ?> id="<?php echo esc_attr($this->get_field_id('animation')); ?>" name="<?php echo esc_attr($this->get_field_name('animation')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('animation')); ?>"><?php esc_html_e('Enable counter animation', 'recruitpro'); ?></label>
        </p>

        <h4><?php esc_html_e('Statistics:', 'recruitpro'); ?></h4>
        <div class="recruitpro-repeater-fields">
            <?php for ($i = 0; $i < 6; $i++) : 
                $stat = isset($stats[$i]) ? $stats[$i] : array('number' => '', 'prefix' => '', 'suffix' => '', 'label' => '', 'description' => '');
            ?>
                <div class="recruitpro-repeater-item" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                    <p>
                        <label><?php esc_html_e('Number:', 'recruitpro'); ?></label>
                        <input class="widefat" name="<?php echo esc_attr($this->get_field_name('stats')); ?>[<?php echo $i; ?>][number]" type="text" value="<?php echo esc_attr($stat['number']); ?>" placeholder="500">
                    </p>
                    <p>
                        <label><?php esc_html_e('Prefix:', 'recruitpro'); ?></label>
                        <input class="widefat" name="<?php echo esc_attr($this->get_field_name('stats')); ?>[<?php echo $i; ?>][prefix]" type="text" value="<?php echo esc_attr($stat['prefix']); ?>" placeholder="$">
                    </p>
                    <p>
                        <label><?php esc_html_e('Suffix:', 'recruitpro'); ?></label>
                        <input class="widefat" name="<?php echo esc_attr($this->get_field_name('stats')); ?>[<?php echo $i; ?>][suffix]" type="text" value="<?php echo esc_attr($stat['suffix']); ?>" placeholder="+">
                    </p>
                    <p>
                        <label><?php esc_html_e('Label:', 'recruitpro'); ?></label>
                        <input class="widefat" name="<?php echo esc_attr($this->get_field_name('stats')); ?>[<?php echo $i; ?>][label]" type="text" value="<?php echo esc_attr($stat['label']); ?>" placeholder="Jobs Filled">
                    </p>
                    <p>
                        <label><?php esc_html_e('Description:', 'recruitpro'); ?></label>
                        <input class="widefat" name="<?php echo esc_attr($this->get_field_name('stats')); ?>[<?php echo $i; ?>][description]" type="text" value="<?php echo esc_attr($stat['description']); ?>" placeholder="Optional description">
                    </p>
                </div>
            <?php endfor; ?>
        </div>

        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['layout'] = !empty($new_instance['layout']) ? sanitize_text_field($new_instance['layout']) : 'grid';
        $instance['animation'] = !empty($new_instance['animation']) ? 1 : 0;

        $instance['stats'] = array();
        if (isset($new_instance['stats']) && is_array($new_instance['stats'])) {
            foreach ($new_instance['stats'] as $stat) {
                if (!empty($stat['number']) || !empty($stat['label'])) {
                    $instance['stats'][] = array(
                        'number' => sanitize_text_field($stat['number']),
                        'prefix' => sanitize_text_field($stat['prefix']),
                        'suffix' => sanitize_text_field($stat['suffix']),
                        'label' => sanitize_text_field($stat['label']),
                        'description' => sanitize_text_field($stat['description']),
                    );
                }
            }
        }

        return $instance;
    }
}

/**
 * =================================================================
 * RECENT JOBS WIDGET
 * =================================================================
 * 
 * Display recent job postings with professional styling.
 * Note: This is a basic theme widget. Full job functionality handled by plugin.
 */

class RecruitPro_Recent_Jobs_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'recruitpro_recent_jobs',
            __('RecruitPro: Recent Jobs', 'recruitpro'),
            array(
                'description' => __('Display recent job postings (basic theme widget - enhanced by jobs plugin).', 'recruitpro'),
                'classname' => 'recruitpro-recent-jobs-widget',
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Jobs', 'recruitpro');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : true;
        $show_location = isset($instance['show_location']) ? $instance['show_location'] : true;

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        // Check if jobs plugin is active and has custom post type
        $post_type = 'job';
        if (!post_type_exists($post_type)) {
            // Fallback to regular posts with job category
            $post_type = 'post';
            $category_args = array('category_name' => 'jobs');
        } else {
            $category_args = array();
        }

        $recent_jobs = wp_get_recent_posts(array_merge(array(
            'numberposts' => $number,
            'post_status' => 'publish',
            'post_type' => $post_type,
        ), $category_args), OBJECT);

        if ($recent_jobs) :
        ?>

        <div class="recent-jobs-content">
            <ul class="recent-jobs-list">
                <?php foreach ($recent_jobs as $job) : ?>
                    <li class="recent-job-item">
                        <h4 class="job-title">
                            <a href="<?php echo esc_url(get_permalink($job->ID)); ?>" class="job-link">
                                <?php echo esc_html($job->post_title); ?>
                            </a>
                        </h4>
                        
                        <div class="job-meta">
                            <?php if ($show_location) : 
                                $location = get_post_meta($job->ID, 'job_location', true);
                                if (!$location && function_exists('get_field')) {
                                    $location = get_field('location', $job->ID);
                                }
                                if ($location) :
                            ?>
                                <span class="job-location">
                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                    <?php echo esc_html($location); ?>
                                </span>
                            <?php endif; endif; ?>

                            <?php if ($show_date) : ?>
                                <span class="job-date">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    <?php echo esc_html(get_the_date('', $job->ID)); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($job->post_excerpt)) : ?>
                            <div class="job-excerpt">
                                <?php echo esc_html(wp_trim_words($job->post_excerpt, 15)); ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="recent-jobs-footer">
                <?php 
                $jobs_page_url = post_type_exists('job') ? get_post_type_archive_link('job') : home_url('/jobs/');
                ?>
                <a href="<?php echo esc_url($jobs_page_url); ?>" class="view-all-jobs-link">
                    <?php esc_html_e('View All Jobs', 'recruitpro'); ?>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <?php
        else :
        ?>
            <div class="no-jobs-message">
                <p><?php esc_html_e('No job postings available at the moment.', 'recruitpro'); ?></p>
                <?php if (current_user_can('publish_posts')) : ?>
                    <p><a href="<?php echo esc_url(admin_url('post-new.php?post_type=job')); ?>"><?php esc_html_e('Add a job posting', 'recruitpro'); ?></a></p>
                <?php endif; ?>
            </div>
        <?php
        endif;

        echo $args['after_widget'];
    }

    /**
     * Widget form
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Recent Jobs', 'recruitpro');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : true;
        $show_location = isset($instance['show_location']) ? $instance['show_location'] : true;
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of jobs to show:', 'recruitpro'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>"><?php esc_html_e('Show publication date', 'recruitpro'); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_location); ?> id="<?php echo esc_attr($this->get_field_id('show_location')); ?>" name="<?php echo esc_attr($this->get_field_name('show_location')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_location')); ?>"><?php esc_html_e('Show job location', 'recruitpro'); ?></label>
        </p>

        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = !empty($new_instance['number']) ? absint($new_instance['number']) : 5;
        $instance['show_date'] = !empty($new_instance['show_date']) ? 1 : 0;
        $instance['show_location'] = !empty($new_instance['show_location']) ? 1 : 0;

        return $instance;
    }
}

/**
 * =================================================================
 * CALL TO ACTION WIDGET
 * =================================================================
 * 
 * Professional call-to-action widget for recruitment conversions.
 */

class RecruitPro_CTA_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'recruitpro_cta',
            __('RecruitPro: Call to Action', 'recruitpro'),
            array(
                'description' => __('Professional call-to-action box for recruitment conversions.', 'recruitpro'),
                'classname' => 'recruitpro-cta-widget',
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $subtitle = !empty($instance['subtitle']) ? $instance['subtitle'] : '';
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : '';
        $button_url = !empty($instance['button_url']) ? $instance['button_url'] : '';
        $button_style = !empty($instance['button_style']) ? $instance['button_style'] : 'primary';
        $background_color = !empty($instance['background_color']) ? $instance['background_color'] : '';
        $text_color = !empty($instance['text_color']) ? $instance['text_color'] : '';

        $style_attr = '';
        if ($background_color || $text_color) {
            $styles = array();
            if ($background_color) {
                $styles[] = 'background-color: ' . esc_attr($background_color);
            }
            if ($text_color) {
                $styles[] = 'color: ' . esc_attr($text_color);
            }
            $style_attr = 'style="' . implode('; ', $styles) . '"';
        }
        ?>

        <div class="cta-content" <?php echo $style_attr; ?>>
            <?php if ($title) : ?>
                <?php echo $args['before_title']; ?>
                <?php echo apply_filters('widget_title', $title); ?>
                <?php echo $args['after_title']; ?>
            <?php endif; ?>

            <?php if ($subtitle) : ?>
                <div class="cta-subtitle"><?php echo esc_html($subtitle); ?></div>
            <?php endif; ?>

            <?php if ($description) : ?>
                <div class="cta-description"><?php echo wp_kses_post($description); ?></div>
            <?php endif; ?>

            <?php if ($button_text && $button_url) : ?>
                <div class="cta-button-wrapper">
                    <a href="<?php echo esc_url($button_url); ?>" class="cta-button button-<?php echo esc_attr($button_style); ?>">
                        <?php echo esc_html($button_text); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Widget form
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Ready to Find Your Next Role?', 'recruitpro');
        $subtitle = isset($instance['subtitle']) ? $instance['subtitle'] : '';
        $description = isset($instance['description']) ? $instance['description'] : '';
        $button_text = isset($instance['button_text']) ? $instance['button_text'] : __('Get Started', 'recruitpro');
        $button_url = isset($instance['button_url']) ? $instance['button_url'] : '';
        $button_style = isset($instance['button_style']) ? $instance['button_style'] : 'primary';
        $background_color = isset($instance['background_color']) ? $instance['background_color'] : '';
        $text_color = isset($instance['text_color']) ? $instance['text_color'] : '';
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>"><?php esc_html_e('Subtitle:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description:', 'recruitpro'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>" rows="4"><?php echo esc_textarea($description); ?></textarea>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php esc_html_e('Button Text:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_url')); ?>"><?php esc_html_e('Button URL:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_url')); ?>" name="<?php echo esc_attr($this->get_field_name('button_url')); ?>" type="url" value="<?php echo esc_attr($button_url); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_style')); ?>"><?php esc_html_e('Button Style:', 'recruitpro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('button_style')); ?>" name="<?php echo esc_attr($this->get_field_name('button_style')); ?>">
                <option value="primary" <?php selected($button_style, 'primary'); ?>><?php esc_html_e('Primary', 'recruitpro'); ?></option>
                <option value="secondary" <?php selected($button_style, 'secondary'); ?>><?php esc_html_e('Secondary', 'recruitpro'); ?></option>
                <option value="outline" <?php selected($button_style, 'outline'); ?>><?php esc_html_e('Outline', 'recruitpro'); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('background_color')); ?>"><?php esc_html_e('Background Color:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('background_color')); ?>" name="<?php echo esc_attr($this->get_field_name('background_color')); ?>" type="color" value="<?php echo esc_attr($background_color); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('text_color')); ?>"><?php esc_html_e('Text Color:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('text_color')); ?>" name="<?php echo esc_attr($this->get_field_name('text_color')); ?>" type="color" value="<?php echo esc_attr($text_color); ?>">
        </p>

        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['subtitle'] = !empty($new_instance['subtitle']) ? sanitize_text_field($new_instance['subtitle']) : '';
        $instance['description'] = !empty($new_instance['description']) ? wp_kses_post($new_instance['description']) : '';
        $instance['button_text'] = !empty($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : '';
        $instance['button_url'] = !empty($new_instance['button_url']) ? esc_url_raw($new_instance['button_url']) : '';
        $instance['button_style'] = !empty($new_instance['button_style']) ? sanitize_text_field($new_instance['button_style']) : 'primary';
        $instance['background_color'] = !empty($new_instance['background_color']) ? sanitize_hex_color($new_instance['background_color']) : '';
        $instance['text_color'] = !empty($new_instance['text_color']) ? sanitize_hex_color($new_instance['text_color']) : '';

        return $instance;
    }
}

/**
 * =================================================================
 * SOCIAL MEDIA WIDGET
 * =================================================================
 * 
 * Professional social media links widget for recruitment agencies.
 */

class RecruitPro_Social_Media_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'recruitpro_social_media',
            __('RecruitPro: Social Media Links', 'recruitpro'),
            array(
                'description' => __('Display professional social media links for recruitment agencies.', 'recruitpro'),
                'classname' => 'recruitpro-social-media-widget',
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : '';
        $style = !empty($instance['style']) ? $instance['style'] : 'icons';
        $size = !empty($instance['size']) ? $instance['size'] : 'medium';
        $target = isset($instance['target']) ? $instance['target'] : true;

        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        // Get social media links from customizer or widget settings
        $social_links = array(
            'linkedin' => array(
                'url' => !empty($instance['linkedin']) ? $instance['linkedin'] : get_theme_mod('recruitpro_social_linkedin', ''),
                'label' => __('LinkedIn', 'recruitpro'),
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0077b5',
            ),
            'facebook' => array(
                'url' => !empty($instance['facebook']) ? $instance['facebook'] : get_theme_mod('recruitpro_social_facebook', ''),
                'label' => __('Facebook', 'recruitpro'),
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877f2',
            ),
            'twitter' => array(
                'url' => !empty($instance['twitter']) ? $instance['twitter'] : get_theme_mod('recruitpro_social_twitter', ''),
                'label' => __('Twitter', 'recruitpro'),
                'icon' => 'fab fa-twitter',
                'color' => '#1da1f2',
            ),
            'instagram' => array(
                'url' => !empty($instance['instagram']) ? $instance['instagram'] : get_theme_mod('recruitpro_social_instagram', ''),
                'label' => __('Instagram', 'recruitpro'),
                'icon' => 'fab fa-instagram',
                'color' => '#e4405f',
            ),
            'youtube' => array(
                'url' => !empty($instance['youtube']) ? $instance['youtube'] : get_theme_mod('recruitpro_social_youtube', ''),
                'label' => __('YouTube', 'recruitpro'),
                'icon' => 'fab fa-youtube',
                'color' => '#ff0000',
            ),
        );

        // Filter out empty links
        $active_links = array_filter($social_links, function($link) {
            return !empty($link['url']);
        });

        if (!empty($active_links)) :
        ?>

        <div class="social-media-content style-<?php echo esc_attr($style); ?> size-<?php echo esc_attr($size); ?>">
            <ul class="social-links-list">
                <?php foreach ($active_links as $platform => $link) : ?>
                    <li class="social-link-item social-<?php echo esc_attr($platform); ?>">
                        <a href="<?php echo esc_url($link['url']); ?>" 
                           class="social-link" 
                           <?php echo $target ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                           aria-label="<?php echo esc_attr($link['label']); ?>"
                           title="<?php echo esc_attr($link['label']); ?>">
                            <?php if ($style === 'icons' || $style === 'both') : ?>
                                <i class="social-icon <?php echo esc_attr($link['icon']); ?>" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php if ($style === 'text' || $style === 'both') : ?>
                                <span class="social-text"><?php echo esc_html($link['label']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
        endif;

        echo $args['after_widget'];
    }

    /**
     * Widget form
     */
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Follow Us', 'recruitpro');
        $style = isset($instance['style']) ? $instance['style'] : 'icons';
        $size = isset($instance['size']) ? $instance['size'] : 'medium';
        $target = isset($instance['target']) ? $instance['target'] : true;
        
        $linkedin = isset($instance['linkedin']) ? $instance['linkedin'] : '';
        $facebook = isset($instance['facebook']) ? $instance['facebook'] : '';
        $twitter = isset($instance['twitter']) ? $instance['twitter'] : '';
        $instagram = isset($instance['instagram']) ? $instance['instagram'] : '';
        $youtube = isset($instance['youtube']) ? $instance['youtube'] : '';
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Display Style:', 'recruitpro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="icons" <?php selected($style, 'icons'); ?>><?php esc_html_e('Icons Only', 'recruitpro'); ?></option>
                <option value="text" <?php selected($style, 'text'); ?>><?php esc_html_e('Text Only', 'recruitpro'); ?></option>
                <option value="both" <?php selected($style, 'both'); ?>><?php esc_html_e('Icons + Text', 'recruitpro'); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('size')); ?>"><?php esc_html_e('Size:', 'recruitpro'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('size')); ?>" name="<?php echo esc_attr($this->get_field_name('size')); ?>">
                <option value="small" <?php selected($size, 'small'); ?>><?php esc_html_e('Small', 'recruitpro'); ?></option>
                <option value="medium" <?php selected($size, 'medium'); ?>><?php esc_html_e('Medium', 'recruitpro'); ?></option>
                <option value="large" <?php selected($size, 'large'); ?>><?php esc_html_e('Large', 'recruitpro'); ?></option>
            </select>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked($target); ?> id="<?php echo esc_attr($this->get_field_id('target')); ?>" name="<?php echo esc_attr($this->get_field_name('target')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('target')); ?>"><?php esc_html_e('Open links in new tab', 'recruitpro'); ?></label>
        </p>

        <h4><?php esc_html_e('Social Media URLs:', 'recruitpro'); ?></h4>
        <p><small><?php esc_html_e('Leave empty to use theme customizer settings or hide the link.', 'recruitpro'); ?></small></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('linkedin')); ?>"><?php esc_html_e('LinkedIn URL:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('linkedin')); ?>" name="<?php echo esc_attr($this->get_field_name('linkedin')); ?>" type="url" value="<?php echo esc_attr($linkedin); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('facebook')); ?>"><?php esc_html_e('Facebook URL:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('facebook')); ?>" name="<?php echo esc_attr($this->get_field_name('facebook')); ?>" type="url" value="<?php echo esc_attr($facebook); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('twitter')); ?>"><?php esc_html_e('Twitter URL:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('twitter')); ?>" name="<?php echo esc_attr($this->get_field_name('twitter')); ?>" type="url" value="<?php echo esc_attr($twitter); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('instagram')); ?>"><?php esc_html_e('Instagram URL:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('instagram')); ?>" name="<?php echo esc_attr($this->get_field_name('instagram')); ?>" type="url" value="<?php echo esc_attr($instagram); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('youtube')); ?>"><?php esc_html_e('YouTube URL:', 'recruitpro'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('youtube')); ?>" name="<?php echo esc_attr($this->get_field_name('youtube')); ?>" type="url" value="<?php echo esc_attr($youtube); ?>">
        </p>

        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['style'] = !empty($new_instance['style']) ? sanitize_text_field($new_instance['style']) : 'icons';
        $instance['size'] = !empty($new_instance['size']) ? sanitize_text_field($new_instance['size']) : 'medium';
        $instance['target'] = !empty($new_instance['target']) ? 1 : 0;
        
        $instance['linkedin'] = !empty($new_instance['linkedin']) ? esc_url_raw($new_instance['linkedin']) : '';
        $instance['facebook'] = !empty($new_instance['facebook']) ? esc_url_raw($new_instance['facebook']) : '';
        $instance['twitter'] = !empty($new_instance['twitter']) ? esc_url_raw($new_instance['twitter']) : '';
        $instance['instagram'] = !empty($new_instance['instagram']) ? esc_url_raw($new_instance['instagram']) : '';
        $instance['youtube'] = !empty($new_instance['youtube']) ? esc_url_raw($new_instance['youtube']) : '';

        return $instance;
    }
}

/**
 * =================================================================
 * REGISTER ALL WIDGETS
 * =================================================================
 */

/**
 * Register RecruitPro custom widgets
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_register_custom_widgets() {
    register_widget('RecruitPro_Contact_Info_Widget');
    register_widget('RecruitPro_Statistics_Widget');
    register_widget('RecruitPro_Recent_Jobs_Widget');
    register_widget('RecruitPro_CTA_Widget');
    register_widget('RecruitPro_Social_Media_Widget');

    /**
     * Allow plugins to register additional recruitment widgets
     *
     * @since 1.0.0
     */
    do_action('recruitpro_register_additional_widgets');
}

// Register widgets after WordPress initializes
add_action('widgets_init', 'recruitpro_register_custom_widgets');

/**
 * =================================================================
 * WIDGET STYLES AND SCRIPTS
 * =================================================================
 */

/**
 * Enqueue widget styles and scripts
 *
 * @since 1.0.0
 * @return void
 */
function recruitpro_enqueue_widget_assets() {
    // Only load if widgets are active
    $widget_areas = array(
        'recruitpro_contact_info',
        'recruitpro_statistics',
        'recruitpro_recent_jobs',
        'recruitpro_cta',
        'recruitpro_social_media'
    );

    $has_widgets = false;
    foreach ($widget_areas as $widget) {
        if (is_active_widget(false, false, $widget)) {
            $has_widgets = true;
            break;
        }
    }

    if ($has_widgets) {
        // Enqueue Font Awesome for icons (if not already loaded)
        if (!wp_style_is('font-awesome', 'enqueued')) {
            wp_enqueue_style(
                'recruitpro-fontawesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
                array(),
                '6.4.0'
            );
        }

        // Widget-specific styles (included in main theme stylesheet)
        wp_add_inline_style('recruitpro-style', recruitpro_get_widget_styles());
    }
}

add_action('wp_enqueue_scripts', 'recruitpro_enqueue_widget_assets');

/**
 * Get widget-specific CSS styles
 *
 * @since 1.0.0
 * @return string CSS styles
 */
function recruitpro_get_widget_styles() {
    $css = '
    /* RecruitPro Widget Styles */
    .recruitpro-contact-info-widget .contact-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .recruitpro-contact-info-widget .contact-info-list li {
        margin-bottom: 10px;
        display: flex;
        align-items: flex-start;
    }
    
    .recruitpro-contact-info-widget .contact-icon {
        margin-right: 8px;
        margin-top: 2px;
        flex-shrink: 0;
        width: 16px;
        text-align: center;
    }
    
    .recruitpro-statistics-widget .statistics-grid {
        display: grid;
        gap: 20px;
    }
    
    .recruitpro-statistics-widget .layout-grid .statistics-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
    
    .recruitpro-statistics-widget .layout-inline .statistics-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }
    
    .recruitpro-statistics-widget .stat-item {
        text-align: center;
    }
    
    .recruitpro-statistics-widget .stat-number {
        font-size: 2em;
        font-weight: bold;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .recruitpro-statistics-widget .stat-label {
        font-size: 0.9em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .recruitpro-recent-jobs-widget .recent-jobs-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .recruitpro-recent-jobs-widget .recent-job-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .recruitpro-recent-jobs-widget .recent-job-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .recruitpro-recent-jobs-widget .job-title {
        margin: 0 0 5px 0;
        font-size: 1em;
    }
    
    .recruitpro-recent-jobs-widget .job-meta {
        font-size: 0.85em;
        color: #666;
        margin-bottom: 5px;
    }
    
    .recruitpro-recent-jobs-widget .job-meta span {
        margin-right: 15px;
    }
    
    .recruitpro-recent-jobs-widget .job-meta i {
        margin-right: 4px;
    }
    
    .recruitpro-cta-widget .cta-content {
        padding: 20px;
        text-align: center;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    .recruitpro-cta-widget .cta-subtitle {
        font-size: 1.1em;
        margin-bottom: 10px;
        font-weight: 500;
    }
    
    .recruitpro-cta-widget .cta-description {
        margin-bottom: 20px;
    }
    
    .recruitpro-cta-widget .cta-button {
        display: inline-block;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .recruitpro-cta-widget .button-primary {
        background: var(--recruitpro-primary-color, #2563eb);
        color: white;
    }
    
    .recruitpro-cta-widget .button-secondary {
        background: var(--recruitpro-secondary-color, #6b7280);
        color: white;
    }
    
    .recruitpro-cta-widget .button-outline {
        border: 2px solid var(--recruitpro-primary-color, #2563eb);
        color: var(--recruitpro-primary-color, #2563eb);
        background: transparent;
    }
    
    .recruitpro-social-media-widget .social-links-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .recruitpro-social-media-widget.style-icons .social-links-list {
        justify-content: center;
    }
    
    .recruitpro-social-media-widget.style-text .social-links-list,
    .recruitpro-social-media-widget.style-both .social-links-list {
        flex-direction: column;
    }
    
    .recruitpro-social-media-widget .social-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .recruitpro-social-media-widget.size-small .social-icon {
        font-size: 16px;
    }
    
    .recruitpro-social-media-widget.size-medium .social-icon {
        font-size: 20px;
    }
    
    .recruitpro-social-media-widget.size-large .social-icon {
        font-size: 24px;
    }
    
    .recruitpro-social-media-widget .social-text {
        margin-left: 8px;
    }
    
    .recruitpro-social-media-widget.style-icons .social-text {
        display: none;
    }
    
    @media (max-width: 768px) {
        .recruitpro-statistics-widget .layout-grid .statistics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .recruitpro-statistics-widget .stat-number {
            font-size: 1.5em;
        }
        
        .recruitpro-cta-widget .cta-content {
            padding: 15px;
        }
    }
    ';

    return $css;
}

/**
 * =================================================================
 * WIDGET UTILITY FUNCTIONS
 * =================================================================
 */

/**
 * Check if a specific RecruitPro widget is active
 *
 * @since 1.0.0
 * @param string $widget_name Widget name
 * @return bool True if widget is active
 */
function recruitpro_is_widget_active($widget_name) {
    return is_active_widget(false, false, $widget_name);
}

/**
 * Get widget defaults for specific widget
 *
 * @since 1.0.0
 * @param string $widget_name Widget name
 * @return array Default values
 */
function recruitpro_get_widget_defaults($widget_name) {
    $defaults = array(
        'recruitpro_contact_info' => array(
            'title' => __('Contact Us', 'recruitpro'),
            'show_schema' => true,
        ),
        'recruitpro_statistics' => array(
            'title' => __('Our Success', 'recruitpro'),
            'layout' => 'grid',
            'animation' => true,
        ),
        'recruitpro_recent_jobs' => array(
            'title' => __('Recent Jobs', 'recruitpro'),
            'number' => 5,
            'show_date' => true,
            'show_location' => true,
        ),
        'recruitpro_cta' => array(
            'title' => __('Ready to Find Your Next Role?', 'recruitpro'),
            'button_text' => __('Get Started', 'recruitpro'),
            'button_style' => 'primary',
        ),
        'recruitpro_social_media' => array(
            'title' => __('Follow Us', 'recruitpro'),
            'style' => 'icons',
            'size' => 'medium',
            'target' => true,
        ),
    );

    return isset($defaults[$widget_name]) ? $defaults[$widget_name] : array();
}

/**
 * =================================================================
 * WIDGET DOCUMENTATION
 * =================================================================
 * 
 * Available RecruitPro Custom Widgets:
 * 
 * 1. CONTACT INFO WIDGET
 *    - Professional contact information display
 *    - Schema.org markup for SEO
 *    - Icons for address, phone, email, hours
 *    - Perfect for sidebars and footer
 * 
 * 2. STATISTICS WIDGET
 *    - Animated counter for achievements
 *    - Multiple layout options (grid, inline, vertical)
 *    - Customizable prefixes and suffixes
 *    - Great for building credibility
 * 
 * 3. RECENT JOBS WIDGET
 *    - Display latest job postings
 *    - Location and date display options
 *    - Automatic plugin integration
 *    - Fallback to blog posts
 * 
 * 4. CALL TO ACTION WIDGET
 *    - Professional conversion-focused design
 *    - Multiple button styles
 *    - Custom colors and styling
 *    - Perfect for lead generation
 * 
 * 5. SOCIAL MEDIA WIDGET
 *    - Professional social media links
 *    - Multiple display styles (icons, text, both)
 *    - Integration with theme customizer
 *    - Recruitment-focused platforms
 * 
 * All widgets are:
 * - Mobile-responsive
 * - Accessibility-compliant
 * - SEO-optimized
 * - Performance-focused
 * - Easy to customize
 * - Professional appearance
 */

// End of widgets.php