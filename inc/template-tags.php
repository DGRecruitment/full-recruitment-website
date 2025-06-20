<?php
/**
 * RecruitPro Theme Template Tags
 *
 * This file contains custom template tags and helper functions for the
 * RecruitPro recruitment website theme. These functions provide reusable
 * HTML output for common template elements, post metadata, navigation,
 * and recruitment-specific content display.
 *
 * @package RecruitPro
 * @subpackage Theme/TemplateTags
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/template-tags.php
 * Purpose: Template helper functions and output tags
 * Dependencies: WordPress core, theme functions
 * Conflicts: None (theme-only functionality)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * POST META AND ENTRY INFORMATION
 * =================================================================
 */

if (!function_exists('recruitpro_posted_on')) :
    /**
     * Prints HTML with meta information for the current post-date/time
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf(
            $time_string,
            esc_attr(get_the_date(DATE_W3C)),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date(DATE_W3C)),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x('Posted on %s', 'post date', 'recruitpro'),
            '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
endif;

if (!function_exists('recruitpro_posted_by')) :
    /**
     * Prints HTML with meta information for the current author
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_posted_by() {
        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x('by %s', 'post author', 'recruitpro'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
endif;

if (!function_exists('recruitpro_entry_meta')) :
    /**
     * Prints HTML with complete post meta information
     *
     * @since 1.0.0
     * @param bool $show_author Whether to show author
     * @param bool $show_date Whether to show date
     * @param bool $show_categories Whether to show categories
     * @param bool $show_tags Whether to show tags
     * @param bool $show_reading_time Whether to show reading time
     * @return void
     */
    function recruitpro_entry_meta($show_author = true, $show_date = true, $show_categories = true, $show_tags = false, $show_reading_time = true) {
        echo '<div class="entry-meta">';

        // Post author
        if ($show_author && !is_page()) {
            echo '<div class="meta-item meta-author">';
            echo '<i class="fas fa-user" aria-hidden="true"></i>';
            recruitpro_posted_by();
            echo '</div>';
        }

        // Post date
        if ($show_date) {
            echo '<div class="meta-item meta-date">';
            echo '<i class="fas fa-calendar" aria-hidden="true"></i>';
            recruitpro_posted_on();
            echo '</div>';
        }

        // Reading time
        if ($show_reading_time && !is_page()) {
            $reading_time = recruitpro_get_reading_time();
            if ($reading_time) {
                echo '<div class="meta-item meta-reading-time">';
                echo '<i class="fas fa-clock" aria-hidden="true"></i>';
                echo '<span class="reading-time">' . esc_html($reading_time) . '</span>';
                echo '</div>';
            }
        }

        // Post categories
        if ($show_categories && has_category()) {
            echo '<div class="meta-item meta-categories">';
            echo '<i class="fas fa-folder" aria-hidden="true"></i>';
            echo '<span class="cat-links">';
            /* translators: used between list items, there is a space after the comma */
            the_category(esc_html__(', ', 'recruitpro'));
            echo '</span>';
            echo '</div>';
        }

        // Post tags
        if ($show_tags && has_tag()) {
            echo '<div class="meta-item meta-tags">';
            echo '<i class="fas fa-tags" aria-hidden="true"></i>';
            echo '<span class="tags-links">';
            /* translators: used between list items, there is a space after the comma */
            the_tags('', esc_html__(', ', 'recruitpro'));
            echo '</span>';
            echo '</div>';
        }

        // Comments count
        if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
            echo '<div class="meta-item meta-comments">';
            echo '<i class="fas fa-comments" aria-hidden="true"></i>';
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'recruitpro'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );
            echo '</span>';
            echo '</div>';
        }

        echo '</div>';
    }
endif;

if (!function_exists('recruitpro_entry_footer')) :
    /**
     * Prints HTML with meta information for the categories, tags and comments
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_entry_footer() {
        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'recruitpro'));
        
        echo '<footer class="entry-footer">';

        if ($tags_list) {
            printf(
                '<div class="tags-links">' .
                '<span class="tags-title">' . esc_html__('Tagged:', 'recruitpro') . '</span> %1$s' .
                '</div>',
                $tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            );
        }

        // Social sharing
        if (function_exists('recruitpro_social_sharing_buttons')) {
            recruitpro_social_sharing_buttons('after');
        }

        // Edit link
        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit <span class="screen-reader-text">%s</span>', 'recruitpro'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            ),
            '<div class="edit-link">',
            '</div>'
        );

        echo '</footer>';
    }
endif;

/**
 * =================================================================
 * READING TIME AND CONTENT ANALYSIS
 * =================================================================
 */

if (!function_exists('recruitpro_get_reading_time')) :
    /**
     * Calculate and return reading time for a post
     *
     * @since 1.0.0
     * @param int $post_id Post ID (optional)
     * @return string Reading time string
     */
    function recruitpro_get_reading_time($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute

        if ($reading_time < 1) {
            $reading_time = 1;
        }

        return sprintf(
            /* translators: %d: reading time in minutes */
            _n('%d min read', '%d min read', $reading_time, 'recruitpro'),
            $reading_time
        );
    }
endif;

if (!function_exists('recruitpro_get_excerpt')) :
    /**
     * Get custom excerpt with specified length
     *
     * @since 1.0.0
     * @param int $length Excerpt length in words
     * @param string $more More text
     * @return string Custom excerpt
     */
    function recruitpro_get_excerpt($length = 25, $more = '...') {
        if (has_excerpt()) {
            return get_the_excerpt();
        }

        $content = get_the_content();
        $content = strip_shortcodes($content);
        $content = wp_strip_all_tags($content);
        
        $words = explode(' ', $content);
        
        if (count($words) > $length) {
            $words = array_slice($words, 0, $length);
            $excerpt = implode(' ', $words) . $more;
        } else {
            $excerpt = implode(' ', $words);
        }
        
        return $excerpt;
    }
endif;

/**
 * =================================================================
 * NAVIGATION AND PAGINATION
 * =================================================================
 */

if (!function_exists('recruitpro_post_navigation')) :
    /**
     * Display navigation to next/previous post when applicable
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_post_navigation() {
        the_post_navigation(
            array(
                'prev_text' => '<div class="nav-subtitle">' . esc_html__('Previous:', 'recruitpro') . '</div><div class="nav-title">%title</div>',
                'next_text' => '<div class="nav-subtitle">' . esc_html__('Next:', 'recruitpro') . '</div><div class="nav-title">%title</div>',
                'class'     => 'post-navigation',
            )
        );
    }
endif;

if (!function_exists('recruitpro_posts_navigation')) :
    /**
     * Display navigation to next/previous set of posts when applicable
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_posts_navigation() {
        the_posts_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous', 'recruitpro') . '</span> <span class="nav-title">' . esc_html__('Older posts', 'recruitpro') . '</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Next', 'recruitpro') . '</span> <span class="nav-title">' . esc_html__('Newer posts', 'recruitpro') . '</span>',
                'class'     => 'posts-navigation',
            )
        );
    }
endif;

if (!function_exists('recruitpro_posts_pagination')) :
    /**
     * Display numbered pagination
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_posts_pagination() {
        the_posts_pagination(
            array(
                'mid_size'  => 2,
                'prev_text' => '<i class="fas fa-arrow-left"></i> ' . esc_html__('Previous', 'recruitpro'),
                'next_text' => esc_html__('Next', 'recruitpro') . ' <i class="fas fa-arrow-right"></i>',
                'class'     => 'pagination',
            )
        );
    }
endif;

/**
 * =================================================================
 * CUSTOM POST TYPE TEMPLATE TAGS
 * =================================================================
 */

if (!function_exists('recruitpro_testimonial_meta')) :
    /**
     * Display testimonial meta information
     *
     * @since 1.0.0
     * @param int $post_id Testimonial post ID
     * @return void
     */
    function recruitpro_testimonial_meta($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $client_name = get_post_meta($post_id, '_testimonial_client_name', true);
        $client_position = get_post_meta($post_id, '_testimonial_client_position', true);
        $client_company = get_post_meta($post_id, '_testimonial_client_company', true);
        $rating = get_post_meta($post_id, '_testimonial_rating', true);

        echo '<div class="testimonial-meta">';
        
        if ($client_name) {
            echo '<div class="testimonial-author">';
            echo '<h4 class="client-name">' . esc_html($client_name) . '</h4>';
            
            if ($client_position || $client_company) {
                echo '<div class="client-details">';
                if ($client_position) {
                    echo '<span class="client-position">' . esc_html($client_position) . '</span>';
                }
                if ($client_position && $client_company) {
                    echo ' <span class="separator">at</span> ';
                }
                if ($client_company) {
                    echo '<span class="client-company">' . esc_html($client_company) . '</span>';
                }
                echo '</div>';
            }
            echo '</div>';
        }

        if ($rating && $rating > 0) {
            echo '<div class="testimonial-rating">';
            echo '<div class="rating-stars" aria-label="' . sprintf(esc_attr__('Rating: %d out of 5 stars', 'recruitpro'), $rating) . '">';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    echo '<i class="fas fa-star" aria-hidden="true"></i>';
                } else {
                    echo '<i class="far fa-star" aria-hidden="true"></i>';
                }
            }
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
    }
endif;

if (!function_exists('recruitpro_team_member_meta')) :
    /**
     * Display team member meta information
     *
     * @since 1.0.0
     * @param int $post_id Team member post ID
     * @return void
     */
    function recruitpro_team_member_meta($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $position = get_post_meta($post_id, '_team_member_position', true);
        $department = get_post_meta($post_id, '_team_member_department', true);
        $email = get_post_meta($post_id, '_team_member_email', true);
        $phone = get_post_meta($post_id, '_team_member_phone', true);
        $linkedin = get_post_meta($post_id, '_team_member_linkedin', true);

        echo '<div class="team-member-meta">';
        
        if ($position) {
            echo '<div class="member-position">' . esc_html($position) . '</div>';
        }

        if ($department) {
            echo '<div class="member-department">' . esc_html($department) . '</div>';
        }

        if ($email || $phone || $linkedin) {
            echo '<div class="member-contact">';
            
            if ($email) {
                echo '<a href="mailto:' . esc_attr($email) . '" class="member-email">';
                echo '<i class="fas fa-envelope" aria-hidden="true"></i> ';
                echo esc_html($email);
                echo '</a>';
            }
            
            if ($phone) {
                echo '<a href="tel:' . esc_attr($phone) . '" class="member-phone">';
                echo '<i class="fas fa-phone" aria-hidden="true"></i> ';
                echo esc_html($phone);
                echo '</a>';
            }
            
            if ($linkedin) {
                echo '<a href="' . esc_url($linkedin) . '" class="member-linkedin" target="_blank" rel="noopener">';
                echo '<i class="fab fa-linkedin" aria-hidden="true"></i> ';
                echo esc_html__('LinkedIn', 'recruitpro');
                echo '</a>';
            }
            
            echo '</div>';
        }

        echo '</div>';
    }
endif;

if (!function_exists('recruitpro_service_meta')) :
    /**
     * Display service meta information
     *
     * @since 1.0.0
     * @param int $post_id Service post ID
     * @return void
     */
    function recruitpro_service_meta($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $duration = get_post_meta($post_id, '_service_duration', true);
        $price_range = get_post_meta($post_id, '_service_price_range', true);
        $delivery_method = get_post_meta($post_id, '_service_delivery_method', true);

        echo '<div class="service-meta">';
        
        if ($duration) {
            echo '<div class="service-duration">';
            echo '<i class="fas fa-clock" aria-hidden="true"></i>';
            echo '<span>' . esc_html($duration) . '</span>';
            echo '</div>';
        }

        if ($price_range) {
            echo '<div class="service-price">';
            echo '<i class="fas fa-dollar-sign" aria-hidden="true"></i>';
            echo '<span>' . esc_html($price_range) . '</span>';
            echo '</div>';
        }

        if ($delivery_method) {
            echo '<div class="service-delivery">';
            echo '<i class="fas fa-cog" aria-hidden="true"></i>';
            echo '<span>' . esc_html($delivery_method) . '</span>';
            echo '</div>';
        }

        echo '</div>';
    }
endif;

/**
 * =================================================================
 * RECRUITMENT-SPECIFIC TEMPLATE TAGS
 * =================================================================
 */

if (!function_exists('recruitpro_job_application_button')) :
    /**
     * Display job application button
     *
     * @since 1.0.0
     * @param int $job_id Job post ID
     * @return void
     */
    function recruitpro_job_application_button($job_id = null) {
        if (!$job_id) {
            $job_id = get_the_ID();
        }

        // Only show if Jobs plugin is active
        if (!function_exists('recruitpro_has_jobs_plugin') || !recruitpro_has_jobs_plugin()) {
            return;
        }

        $application_method = get_post_meta($job_id, '_job_application_method', true);
        $external_url = get_post_meta($job_id, '_job_external_url', true);
        $application_email = get_post_meta($job_id, '_job_application_email', true);

        echo '<div class="job-application-button">';

        if ($application_method === 'external' && $external_url) {
            echo '<a href="' . esc_url($external_url) . '" class="btn btn-primary btn-large" target="_blank" rel="noopener">';
            echo '<i class="fas fa-external-link-alt" aria-hidden="true"></i> ';
            echo esc_html__('Apply Now', 'recruitpro');
            echo '</a>';
        } elseif ($application_method === 'email' && $application_email) {
            echo '<a href="mailto:' . esc_attr($application_email) . '?subject=' . esc_attr(sprintf(__('Application for %s', 'recruitpro'), get_the_title($job_id))) . '" class="btn btn-primary btn-large">';
            echo '<i class="fas fa-envelope" aria-hidden="true"></i> ';
            echo esc_html__('Apply via Email', 'recruitpro');
            echo '</a>';
        } else {
            // Default application form
            echo '<button type="button" class="btn btn-primary btn-large" data-job-id="' . esc_attr($job_id) . '" onclick="recruitpro_open_application_form(' . esc_js($job_id) . ')">';
            echo '<i class="fas fa-paper-plane" aria-hidden="true"></i> ';
            echo esc_html__('Apply Now', 'recruitpro');
            echo '</button>';
        }

        echo '</div>';
    }
endif;

if (!function_exists('recruitpro_job_meta')) :
    /**
     * Display job meta information
     *
     * @since 1.0.0
     * @param int $job_id Job post ID
     * @return void
     */
    function recruitpro_job_meta($job_id = null) {
        if (!$job_id) {
            $job_id = get_the_ID();
        }

        // Only show if Jobs plugin is active
        if (!function_exists('recruitpro_has_jobs_plugin') || !recruitpro_has_jobs_plugin()) {
            return;
        }

        $job_type = get_post_meta($job_id, '_job_type', true);
        $job_location = get_post_meta($job_id, '_job_location', true);
        $salary_range = get_post_meta($job_id, '_job_salary_range', true);
        $experience_level = get_post_meta($job_id, '_job_experience_level', true);
        $posted_date = get_the_date('', $job_id);

        echo '<div class="job-meta">';
        
        if ($job_type) {
            echo '<div class="job-meta-item job-type">';
            echo '<i class="fas fa-briefcase" aria-hidden="true"></i>';
            echo '<span>' . esc_html($job_type) . '</span>';
            echo '</div>';
        }

        if ($job_location) {
            echo '<div class="job-meta-item job-location">';
            echo '<i class="fas fa-map-marker-alt" aria-hidden="true"></i>';
            echo '<span>' . esc_html($job_location) . '</span>';
            echo '</div>';
        }

        if ($salary_range) {
            echo '<div class="job-meta-item job-salary">';
            echo '<i class="fas fa-dollar-sign" aria-hidden="true"></i>';
            echo '<span>' . esc_html($salary_range) . '</span>';
            echo '</div>';
        }

        if ($experience_level) {
            echo '<div class="job-meta-item job-experience">';
            echo '<i class="fas fa-user-graduate" aria-hidden="true"></i>';
            echo '<span>' . esc_html($experience_level) . '</span>';
            echo '</div>';
        }

        echo '<div class="job-meta-item job-posted">';
        echo '<i class="fas fa-calendar" aria-hidden="true"></i>';
        echo '<span>' . sprintf(esc_html__('Posted %s', 'recruitpro'), $posted_date) . '</span>';
        echo '</div>';

        echo '</div>';
    }
endif;

/**
 * =================================================================
 * BREADCRUMBS INTEGRATION
 * =================================================================
 */

if (!function_exists('recruitpro_breadcrumbs')) :
    /**
     * Display breadcrumbs navigation
     *
     * @since 1.0.0
     * @param array $args Breadcrumb arguments
     * @return void
     */
    function recruitpro_breadcrumbs($args = array()) {
        $defaults = array(
            'show_home' => true,
            'show_current' => true,
            'separator' => '<i class="fas fa-chevron-right" aria-hidden="true"></i>',
            'class' => 'breadcrumbs',
        );

        $args = wp_parse_args($args, $defaults);

        // Use the breadcrumbs function if available
        if (function_exists('recruitpro_get_breadcrumbs')) {
            $breadcrumbs = recruitpro_get_breadcrumbs();
        } else {
            // Fallback simple breadcrumbs
            $breadcrumbs = recruitpro_simple_breadcrumbs();
        }

        if (empty($breadcrumbs)) {
            return;
        }

        echo '<nav class="' . esc_attr($args['class']) . '" aria-label="' . esc_attr__('Breadcrumbs', 'recruitpro') . '">';
        echo '<ol class="breadcrumb-list">';

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $is_last = ($index === count($breadcrumbs) - 1);
            
            echo '<li class="breadcrumb-item">';
            
            if (!$is_last && !empty($breadcrumb['url'])) {
                echo '<a href="' . esc_url($breadcrumb['url']) . '">' . esc_html($breadcrumb['title']) . '</a>';
            } else {
                echo '<span class="current">' . esc_html($breadcrumb['title']) . '</span>';
            }
            
            if (!$is_last) {
                echo ' <span class="breadcrumb-separator">' . $args['separator'] . '</span> ';
            }
            
            echo '</li>';
        }

        echo '</ol>';
        echo '</nav>';
    }
endif;

if (!function_exists('recruitpro_simple_breadcrumbs')) :
    /**
     * Generate simple breadcrumbs as fallback
     *
     * @since 1.0.0
     * @return array Breadcrumb items
     */
    function recruitpro_simple_breadcrumbs() {
        $breadcrumbs = array();

        // Home
        $breadcrumbs[] = array(
            'title' => __('Home', 'recruitpro'),
            'url' => home_url('/'),
        );

        if (is_single()) {
            // Single post
            if (has_category()) {
                $categories = get_the_category();
                $breadcrumbs[] = array(
                    'title' => $categories[0]->name,
                    'url' => get_category_link($categories[0]->term_id),
                );
            }
            $breadcrumbs[] = array(
                'title' => get_the_title(),
                'url' => '',
            );
        } elseif (is_page()) {
            // Page
            $breadcrumbs[] = array(
                'title' => get_the_title(),
                'url' => '',
            );
        } elseif (is_category()) {
            // Category archive
            $breadcrumbs[] = array(
                'title' => single_cat_title('', false),
                'url' => '',
            );
        } elseif (is_tag()) {
            // Tag archive
            $breadcrumbs[] = array(
                'title' => single_tag_title('', false),
                'url' => '',
            );
        } elseif (is_archive()) {
            // Other archives
            $breadcrumbs[] = array(
                'title' => get_the_archive_title(),
                'url' => '',
            );
        } elseif (is_search()) {
            // Search results
            $breadcrumbs[] = array(
                'title' => sprintf(__('Search Results for: %s', 'recruitpro'), get_search_query()),
                'url' => '',
            );
        }

        return $breadcrumbs;
    }
endif;

/**
 * =================================================================
 * SCHEMA MARKUP HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_article_schema')) :
    /**
     * Output Article schema markup
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_article_schema() {
        if (!is_single()) {
            return;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => get_the_excerpt() ?: recruitpro_get_excerpt(25),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
                'url' => get_author_posts_url(get_the_author_meta('ID')),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
            ),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'url' => get_permalink(),
        );

        // Add image if available
        if (has_post_thumbnail()) {
            $image_id = get_post_thumbnail_id();
            $image_data = wp_get_attachment_image_src($image_id, 'large');
            if ($image_data) {
                $schema['image'] = array(
                    '@type' => 'ImageObject',
                    'url' => $image_data[0],
                    'width' => $image_data[1],
                    'height' => $image_data[2],
                );
            }
        }

        echo '<script type="application/ld+json">';
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES);
        echo '</script>';
    }
endif;

/**
 * =================================================================
 * ACCESSIBILITY HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_skip_link_target')) :
    /**
     * Output skip link target for accessibility
     *
     * @since 1.0.0
     * @return void
     */
    function recruitpro_skip_link_target() {
        echo '<div id="content" class="skip-link-target" tabindex="-1"></div>';
    }
endif;

if (!function_exists('recruitpro_screen_reader_text')) :
    /**
     * Wrap text for screen readers only
     *
     * @since 1.0.0
     * @param string $text Text to wrap
     * @return string Wrapped text
     */
    function recruitpro_screen_reader_text($text) {
        return '<span class="screen-reader-text">' . esc_html($text) . '</span>';
    }
endif;

/**
 * =================================================================
 * UTILITY FUNCTIONS
 * =================================================================
 */

if (!function_exists('recruitpro_get_svg_icon')) :
    /**
     * Get SVG icon markup
     *
     * @since 1.0.0
     * @param string $icon Icon name
     * @param array $args Icon arguments
     * @return string SVG markup
     */
    function recruitpro_get_svg_icon($icon, $args = array()) {
        $defaults = array(
            'size' => 24,
            'class' => '',
            'aria_hidden' => true,
        );

        $args = wp_parse_args($args, $defaults);
        
        $aria_hidden = $args['aria_hidden'] ? ' aria-hidden="true"' : '';
        $class = $args['class'] ? ' class="' . esc_attr($args['class']) . '"' : '';

        // Simple SVG icons for common use cases
        $icons = array(
            'search' => '<svg width="' . $args['size'] . '" height="' . $args['size'] . '" viewBox="0 0 24 24" fill="currentColor"' . $class . $aria_hidden . '><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>',
            'menu' => '<svg width="' . $args['size'] . '" height="' . $args['size'] . '" viewBox="0 0 24 24" fill="currentColor"' . $class . $aria_hidden . '><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>',
            'close' => '<svg width="' . $args['size'] . '" height="' . $args['size'] . '" viewBox="0 0 24 24" fill="currentColor"' . $class . $aria_hidden . '><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>',
            'arrow-right' => '<svg width="' . $args['size'] . '" height="' . $args['size'] . '" viewBox="0 0 24 24" fill="currentColor"' . $class . $aria_hidden . '><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>',
            'arrow-left' => '<svg width="' . $args['size'] . '" height="' . $args['size'] . '" viewBox="0 0 24 24" fill="currentColor"' . $class . $aria_hidden . '><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg>',
        );

        return isset($icons[$icon]) ? $icons[$icon] : '';
    }
endif;

if (!function_exists('recruitpro_get_post_thumbnail_caption')) :
    /**
     * Get featured image caption
     *
     * @since 1.0.0
     * @param int $post_id Post ID
     * @return string Image caption
     */
    function recruitpro_get_post_thumbnail_caption($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        if (!has_post_thumbnail($post_id)) {
            return '';
        }

        $thumbnail_id = get_post_thumbnail_id($post_id);
        return wp_get_attachment_caption($thumbnail_id);
    }
endif;

/**
 * =================================================================
 * COMMENTS TEMPLATE TAGS
 * =================================================================
 */

if (!function_exists('recruitpro_comment')) :
    /**
     * Template for comments and pingbacks
     *
     * @since 1.0.0
     * @param WP_Comment $comment Comment object
     * @param array $args Comment arguments
     * @param int $depth Comment depth
     * @return void
     */
    function recruitpro_comment($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;

        if ('pingback' === $comment->comment_type || 'trackback' === $comment->comment_type) : ?>

        <li id="comment-<?php comment_ID(); ?>" <?php comment_class('ping'); ?>>
            <div class="comment-body">
                <?php esc_html_e('Pingback:', 'recruitpro'); ?> <?php comment_author_link(); ?> <?php edit_comment_link(esc_html__('Edit', 'recruitpro'), '<span class="edit-link">', '</span>'); ?>
            </div>

        <?php else : ?>

        <li id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                <footer class="comment-meta">
                    <div class="comment-author vcard">
                        <?php if (0 !== $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']); ?>
                        <?php
                        /* translators: %s: comment author link */
                        printf(__('%s <span class="says">says:</span>', 'recruitpro'),
                            sprintf('<cite class="fn">%s</cite>', get_comment_author_link($comment))
                        );
                        ?>
                    </div>

                    <div class="comment-metadata">
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                            <time datetime="<?php comment_time('c'); ?>">
                                <?php
                                /* translators: 1: comment date, 2: comment time */
                                printf(esc_html__('%1$s at %2$s', 'recruitpro'), get_comment_date('', $comment), get_comment_time());
                                ?>
                            </time>
                        </a>
                        <?php edit_comment_link(esc_html__('Edit', 'recruitpro'), '<span class="edit-link">', '</span>'); ?>
                    </div>

                    <?php if ('0' === $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'recruitpro'); ?></p>
                    <?php endif; ?>
                </footer>

                <div class="comment-content">
                    <?php comment_text(); ?>
                </div>

                <?php
                comment_reply_link(array_merge($args, array(
                    'add_below' => 'div-comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '<div class="reply">',
                    'after'     => '</div>',
                )));
                ?>
            </article>

        <?php endif;
    }
endif;

/**
 * =================================================================
 * INTEGRATION HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_plugin_integration_notice')) :
    /**
     * Show plugin integration notices
     *
     * @since 1.0.0
     * @param string $plugin_name Plugin name
     * @param string $feature_name Feature name
     * @return void
     */
    function recruitpro_plugin_integration_notice($plugin_name, $feature_name) {
        echo '<div class="plugin-integration-notice">';
        echo '<p>';
        printf(
            /* translators: 1: feature name, 2: plugin name */
            esc_html__('%1$s requires the %2$s plugin to be installed and activated.', 'recruitpro'),
            esc_html($feature_name),
            esc_html($plugin_name)
        );
        echo '</p>';
        echo '</div>';
    }
endif;

/**
 * =================================================================
 * PERFORMANCE HELPERS
 * =================================================================
 */

if (!function_exists('recruitpro_lazy_load_images')) :
    /**
     * Add lazy loading attributes to images
     *
     * @since 1.0.0
     * @param string $content Content with images
     * @return string Modified content
     */
    function recruitpro_lazy_load_images($content) {
        // Add loading="lazy" to images if not already present
        $content = preg_replace('/<img(?![^>]*loading=)([^>]*)>/i', '<img loading="lazy"$1>', $content);
        return $content;
    }
endif;

// Initialize template tags
add_action('init', function() {
    // Add automatic lazy loading to content images
    if (get_theme_mod('recruitpro_enable_lazy_loading', true)) {
        add_filter('the_content', 'recruitpro_lazy_load_images');
    }
    
    // Add article schema to single posts
    if (get_theme_mod('recruitpro_enable_article_schema', true)) {
        add_action('wp_head', 'recruitpro_article_schema');
    }
});