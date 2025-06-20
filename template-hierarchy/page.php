<?php
/**
 * The template for displaying all pages
 *
 * Professional page template for recruitment agencies handling static content
 * including About, Services, Contact, Privacy, and other company pages.
 * Features flexible layout options, professional presentation, and industry-
 * specific content organization designed for recruitment business needs.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/page.php
 * Purpose: Default template for all static pages
 * Context: Recruitment agency static content
 * Features: Flexible layout, professional styling, business focus
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page settings and layout options
$page_layout = recruitpro_get_page_layout();
$show_page_header = recruitpro_show_page_header();
$page_style = recruitpro_get_page_style();
$show_comments = recruitpro_show_page_comments();

?>

<main id="primary" class="site-main page-main <?php echo esc_attr($page_style['main_class']); ?>">
    
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Page Header -->
        <?php if ($show_page_header) : ?>
            <header class="page-header <?php echo esc_attr($page_style['header_class']); ?>">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="page-header-content">
                                
                                <!-- Breadcrumbs -->
                                <?php if (function_exists('recruitpro_breadcrumbs') && get_theme_mod('recruitpro_show_breadcrumbs', true)) : ?>
                                    <nav class="breadcrumbs-nav" aria-label="<?php esc_attr_e('Breadcrumb navigation', 'recruitpro'); ?>">
                                        <?php recruitpro_breadcrumbs(); ?>
                                    </nav>
                                <?php endif; ?>
                                
                                <!-- Page Title -->
                                <h1 class="page-title"><?php the_title(); ?></h1>
                                
                                <!-- Page Subtitle -->
                                <?php 
                                $page_subtitle = get_post_meta(get_the_ID(), '_page_subtitle', true);
                                if (!empty($page_subtitle)) : ?>
                                    <p class="page-subtitle"><?php echo esc_html($page_subtitle); ?></p>
                                <?php endif; ?>
                                
                                <!-- Page Meta -->
                                <?php if ($page_style['show_meta']) : ?>
                                    <div class="page-meta">
                                        <div class="meta-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                                            </svg>
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php 
                                                printf(
                                                    esc_html__('Last updated: %s', 'recruitpro'),
                                                    get_the_modified_date()
                                                ); 
                                                ?>
                                            </time>
                                        </div>
                                        
                                        <?php 
                                        $reading_time = recruitpro_get_reading_time();
                                        if ($reading_time > 0) : ?>
                                            <div class="meta-item">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                <span class="reading-time">
                                                    <?php echo esc_html($reading_time); ?> 
                                                    <?php esc_html_e('min read', 'recruitpro'); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="page-content-wrapper">
            <div class="container">
                <div class="row">
                    <div class="<?php echo esc_attr($page_layout['content_class']); ?>">
                        
                        <article id="post-<?php the_ID(); ?>" <?php post_class('page-article'); ?>>
                            
                            <!-- Featured Image -->
                            <?php if (has_post_thumbnail() && $page_style['show_featured_image']) : ?>
                                <div class="page-featured-image">
                                    <?php
                                    the_post_thumbnail('large', array(
                                        'alt' => get_the_title(),
                                        'class' => 'img-fluid rounded',
                                        'loading' => 'lazy',
                                    ));
                                    ?>
                                    
                                    <!-- Image Caption -->
                                    <?php 
                                    $caption = get_the_post_thumbnail_caption();
                                    if (!empty($caption)) : ?>
                                        <figcaption class="featured-image-caption">
                                            <?php echo esc_html($caption); ?>
                                        </figcaption>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Page Content -->
                            <div class="page-content-area">
                                
                                <!-- Content Intro -->
                                <?php 
                                $page_intro = get_post_meta(get_the_ID(), '_page_intro', true);
                                if (!empty($page_intro)) : ?>
                                    <div class="page-intro">
                                        <?php echo wp_kses_post($page_intro); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Main Content -->
                                <div class="entry-content">
                                    <?php
                                    the_content();
                                    
                                    // Handle page breaks
                                    wp_link_pages(array(
                                        'before'      => '<nav class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'recruitpro') . '</span>',
                                        'after'       => '</nav>',
                                        'link_before' => '<span class="page-number">',
                                        'link_after'  => '</span>',
                                        'pagelink'    => '<span class="screen-reader-text">' . esc_html__('Page', 'recruitpro') . ' </span>%',
                                        'separator'   => '<span class="screen-reader-text">, </span>',
                                    ));
                                    ?>
                                </div>

                                <!-- Page Footer Content -->
                                <?php 
                                $page_footer_content = get_post_meta(get_the_ID(), '_page_footer_content', true);
                                if (!empty($page_footer_content)) : ?>
                                    <div class="page-footer-content">
                                        <?php echo wp_kses_post($page_footer_content); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Custom Fields Display -->
                                <?php echo recruitpro_display_page_custom_fields(); ?>

                                <!-- Related Pages -->
                                <?php if ($page_style['show_related_pages']) : ?>
                                    <div class="related-pages-section">
                                        <h3 class="related-pages-title">
                                            <?php esc_html_e('Related Information', 'recruitpro'); ?>
                                        </h3>
                                        <?php echo recruitpro_get_related_pages(); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Call-to-Action -->
                                <?php 
                                $show_cta = get_post_meta(get_the_ID(), '_show_page_cta', true);
                                $cta_title = get_post_meta(get_the_ID(), '_page_cta_title', true);
                                $cta_text = get_post_meta(get_the_ID(), '_page_cta_text', true);
                                $cta_button_text = get_post_meta(get_the_ID(), '_page_cta_button_text', true);
                                $cta_button_url = get_post_meta(get_the_ID(), '_page_cta_button_url', true);
                                
                                if ($show_cta === '1' && (!empty($cta_title) || !empty($cta_text))) : ?>
                                    <div class="page-cta-section">
                                        <div class="page-cta-content">
                                            <?php if (!empty($cta_title)) : ?>
                                                <h3 class="page-cta-title"><?php echo esc_html($cta_title); ?></h3>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($cta_text)) : ?>
                                                <p class="page-cta-text"><?php echo esc_html($cta_text); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($cta_button_text) && !empty($cta_button_url)) : ?>
                                                <a href="<?php echo esc_url($cta_button_url); ?>" class="btn btn-primary page-cta-button">
                                                    <?php echo esc_html($cta_button_text); ?>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                    </svg>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Page Footer -->
                            <footer class="page-footer">
                                <div class="page-footer-meta">
                                    
                                    <!-- Last Modified Date -->
                                    <div class="page-updated">
                                        <small class="text-muted">
                                            <?php
                                            printf(
                                                esc_html__('Last updated on %s', 'recruitpro'),
                                                '<time datetime="' . esc_attr(get_the_modified_date('c')) . '">' . 
                                                esc_html(get_the_modified_date()) . '</time>'
                                            );
                                            ?>
                                        </small>
                                    </div>
                                    
                                    <!-- Edit Link -->
                                    <?php
                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
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
                                    ?>
                                </div>

                                <!-- Social Sharing -->
                                <?php if ($page_style['show_social_sharing']) : ?>
                                    <div class="page-social-sharing">
                                        <h4 class="sharing-title"><?php esc_html_e('Share this page:', 'recruitpro'); ?></h4>
                                        <?php echo recruitpro_get_social_sharing_buttons(); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Page Navigation -->
                                <?php if ($page_style['show_page_navigation']) : ?>
                                    <nav class="page-navigation" aria-label="<?php esc_attr_e('Page navigation', 'recruitpro'); ?>">
                                        <?php
                                        $prev_page = recruitpro_get_previous_page();
                                        $next_page = recruitpro_get_next_page();
                                        
                                        if ($prev_page || $next_page) : ?>
                                            <div class="page-nav-links">
                                                <?php if ($prev_page) : ?>
                                                    <div class="nav-previous">
                                                        <a href="<?php echo esc_url(get_permalink($prev_page->ID)); ?>" rel="prev">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                                                            </svg>
                                                            <span class="nav-title"><?php echo esc_html(get_the_title($prev_page->ID)); ?></span>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($next_page) : ?>
                                                    <div class="nav-next">
                                                        <a href="<?php echo esc_url(get_permalink($next_page->ID)); ?>" rel="next">
                                                            <span class="nav-title"><?php echo esc_html(get_the_title($next_page->ID)); ?></span>
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </nav>
                                <?php endif; ?>
                            </footer>
                        </article>

                        <!-- Comments -->
                        <?php if ($show_comments && (comments_open() || get_comments_number())) : ?>
                            <div class="page-comments-section">
                                <?php comments_template(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <?php if ($page_layout['show_sidebar']) : ?>
                        <aside class="<?php echo esc_attr($page_layout['sidebar_class']); ?>">
                            <div class="page-sidebar">
                                
                                <!-- Page-specific sidebar content -->
                                <?php echo recruitpro_get_page_sidebar_content(); ?>
                                
                                <!-- Regular sidebar -->
                                <?php
                                if (is_active_sidebar('sidebar-1')) {
                                    dynamic_sidebar('sidebar-1');
                                } else {
                                    // Fallback sidebar for pages
                                    recruitpro_fallback_page_sidebar();
                                }
                                ?>
                            </div>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    
    <?php endwhile; ?>

</main>

<?php
get_footer();

/**
 * Helper Functions for Page Template
 */

/**
 * Get page layout configuration
 */
function recruitpro_get_page_layout() {
    $page_id = get_the_ID();
    
    // Check for custom layout setting
    $custom_layout = get_post_meta($page_id, '_page_layout', true);
    $hide_sidebar = get_post_meta($page_id, '_hide_sidebar', true);
    
    // Default layout settings
    $show_sidebar = ($hide_sidebar !== '1') && ($custom_layout !== 'full-width');
    
    // Specific pages that typically don't need sidebars
    $no_sidebar_pages = array('contact', 'landing', 'home', 'front-page');
    $page_template = get_page_template_slug($page_id);
    
    foreach ($no_sidebar_pages as $template_name) {
        if (strpos($page_template, $template_name) !== false) {
            $show_sidebar = false;
            break;
        }
    }
    
    return array(
        'show_sidebar' => $show_sidebar,
        'content_class' => $show_sidebar ? 'col-lg-8 col-md-12' : 'col-12',
        'sidebar_class' => 'col-lg-4 col-md-12',
        'layout_type' => $custom_layout ?: 'default',
    );
}

/**
 * Determine if page header should be shown
 */
function recruitpro_show_page_header() {
    $page_id = get_the_ID();
    $hide_header = get_post_meta($page_id, '_hide_page_header', true);
    
    // Don't show header for certain page templates
    $no_header_templates = array('page-landing', 'page-coming-soon', 'page-maintenance');
    $page_template = get_page_template_slug($page_id);
    
    if (in_array($page_template, $no_header_templates)) {
        return false;
    }
    
    return ($hide_header !== '1');
}

/**
 * Get page style configuration
 */
function recruitpro_get_page_style() {
    $page_id = get_the_ID();
    $page_template = get_page_template_slug($page_id);
    
    $styles = array(
        'main_class' => 'page-standard',
        'header_class' => 'page-header-standard',
        'show_meta' => true,
        'show_featured_image' => true,
        'show_related_pages' => false,
        'show_social_sharing' => false,
        'show_page_navigation' => false,
    );
    
    // Customize styles based on page type
    if (strpos($page_template, 'contact') !== false) {
        $styles['main_class'] = 'page-contact';
        $styles['show_related_pages'] = true;
    } elseif (strpos($page_template, 'about') !== false) {
        $styles['main_class'] = 'page-about';
        $styles['show_social_sharing'] = true;
        $styles['show_related_pages'] = true;
    } elseif (strpos($page_template, 'services') !== false) {
        $styles['main_class'] = 'page-services';
        $styles['show_related_pages'] = true;
    } elseif (strpos($page_template, 'privacy') !== false || strpos($page_template, 'terms') !== false) {
        $styles['main_class'] = 'page-legal';
        $styles['show_meta'] = true;
        $styles['show_page_navigation'] = true;
    }
    
    return $styles;
}

/**
 * Determine if page comments should be shown
 */
function recruitpro_show_page_comments() {
    $page_id = get_the_ID();
    $disable_comments = get_post_meta($page_id, '_disable_comments', true);
    
    // Generally, recruitment agencies don't need comments on most pages
    $no_comments_templates = array('contact', 'privacy', 'terms', 'landing');
    $page_template = get_page_template_slug($page_id);
    
    foreach ($no_comments_templates as $template_name) {
        if (strpos($page_template, $template_name) !== false) {
            return false;
        }
    }
    
    return ($disable_comments !== '1') && comments_open();
}

/**
 * Get reading time for page content
 */
function recruitpro_get_reading_time() {
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute
    return max(1, $reading_time);
}

/**
 * Display custom fields for pages
 */
function recruitpro_display_page_custom_fields() {
    $page_id = get_the_ID();
    $custom_fields = get_post_meta($page_id);
    
    // Define fields to display
    $display_fields = array(
        '_company_phone' => array('label' => 'Phone', 'icon' => 'phone'),
        '_company_email' => array('label' => 'Email', 'icon' => 'email'),
        '_company_address' => array('label' => 'Address', 'icon' => 'location'),
        '_office_hours' => array('label' => 'Office Hours', 'icon' => 'clock'),
        '_certifications' => array('label' => 'Certifications', 'icon' => 'award'),
    );
    
    $output = '';
    $has_fields = false;
    
    foreach ($display_fields as $field_key => $field_info) {
        if (isset($custom_fields[$field_key]) && !empty($custom_fields[$field_key][0])) {
            if (!$has_fields) {
                $output .= '<div class="page-custom-fields"><h3>' . esc_html__('Additional Information', 'recruitpro') . '</h3><dl class="custom-fields-list">';
                $has_fields = true;
            }
            
            $value = $custom_fields[$field_key][0];
            $output .= sprintf(
                '<div class="custom-field-item"><dt class="field-label">%s</dt><dd class="field-value">%s</dd></div>',
                esc_html($field_info['label']),
                esc_html($value)
            );
        }
    }
    
    if ($has_fields) {
        $output .= '</dl></div>';
    }
    
    return $output;
}

/**
 * Get related pages
 */
function recruitpro_get_related_pages() {
    $current_page_id = get_the_ID();
    $current_page_template = get_page_template_slug($current_page_id);
    
    // Define related page relationships
    $related_pages_map = array(
        'page-about.php' => array('page-team.php', 'page-services.php', 'page-contact.php'),
        'page-services.php' => array('page-about.php', 'page-process.php', 'page-contact.php'),
        'page-contact.php' => array('page-about.php', 'page-services.php', 'page-locations.php'),
        'page-privacy.php' => array('page-terms.php', 'page-gdpr.php'),
        'page-terms.php' => array('page-privacy.php', 'page-gdpr.php'),
    );
    
    $related_templates = array();
    if (isset($related_pages_map[$current_page_template])) {
        $related_templates = $related_pages_map[$current_page_template];
    }
    
    if (empty($related_templates)) {
        // Fallback: get recent pages
        $related_pages = get_posts(array(
            'post_type' => 'page',
            'posts_per_page' => 3,
            'post_status' => 'publish',
            'post__not_in' => array($current_page_id),
            'orderby' => 'date',
            'order' => 'DESC',
        ));
    } else {
        // Get pages by templates
        $related_pages = array();
        foreach ($related_templates as $template) {
            $pages = get_posts(array(
                'post_type' => 'page',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_wp_page_template',
                        'value' => $template,
                        'compare' => '='
                    )
                )
            ));
            
            if (!empty($pages)) {
                $related_pages = array_merge($related_pages, $pages);
            }
        }
    }
    
    if (empty($related_pages)) {
        return '';
    }
    
    $output = '<div class="related-pages-grid"><div class="row">';
    foreach (array_slice($related_pages, 0, 3) as $page) {
        $output .= sprintf(
            '<div class="col-md-4">
                <div class="related-page-item">
                    <h4 class="related-page-title">
                        <a href="%s">%s</a>
                    </h4>
                    <p class="related-page-excerpt">%s</p>
                </div>
            </div>',
            esc_url(get_permalink($page->ID)),
            esc_html(get_the_title($page->ID)),
            esc_html(wp_trim_words(get_the_excerpt($page->ID), 15))
        );
    }
    $output .= '</div></div>';
    
    return $output;
}

/**
 * Get social sharing buttons
 */
function recruitpro_get_social_sharing_buttons() {
    $page_url = get_permalink();
    $page_title = get_the_title();
    
    $buttons = array(
        'linkedin' => array(
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($page_url),
            'label' => 'LinkedIn',
            'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'
        ),
        'twitter' => array(
            'url' => 'https://twitter.com/intent/tweet?url=' . urlencode($page_url) . '&text=' . urlencode($page_title),
            'label' => 'Twitter',
            'icon' => '<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>'
        ),
        'facebook' => array(
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($page_url),
            'label' => 'Facebook',
            'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'
        ),
    );
    
    $output = '<div class="social-sharing-buttons">';
    foreach ($buttons as $platform => $data) {
        $output .= sprintf(
            '<a href="%s" target="_blank" rel="noopener noreferrer" class="social-share-btn social-share-%s" aria-label="%s">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">%s</svg>
                <span class="btn-text">%s</span>
            </a>',
            esc_url($data['url']),
            esc_attr($platform),
            esc_attr(sprintf(__('Share on %s', 'recruitpro'), $data['label'])),
            $data['icon'],
            esc_html($data['label'])
        );
    }
    $output .= '</div>';
    
    return $output;
}

/**
 * Get previous page in hierarchy
 */
function recruitpro_get_previous_page() {
    $current_page = get_post();
    
    // For child pages, get previous sibling
    if ($current_page->post_parent) {
        $siblings = get_pages(array(
            'parent' => $current_page->post_parent,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
        ));
        
        $current_index = false;
        foreach ($siblings as $index => $sibling) {
            if ($sibling->ID === $current_page->ID) {
                $current_index = $index;
                break;
            }
        }
        
        if ($current_index !== false && $current_index > 0) {
            return $siblings[$current_index - 1];
        }
    }
    
    return null;
}

/**
 * Get next page in hierarchy
 */
function recruitpro_get_next_page() {
    $current_page = get_post();
    
    // For child pages, get next sibling
    if ($current_page->post_parent) {
        $siblings = get_pages(array(
            'parent' => $current_page->post_parent,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
        ));
        
        $current_index = false;
        foreach ($siblings as $index => $sibling) {
            if ($sibling->ID === $current_page->ID) {
                $current_index = $index;
                break;
            }
        }
        
        if ($current_index !== false && $current_index < count($siblings) - 1) {
            return $siblings[$current_index + 1];
        }
    }
    
    return null;
}

/**
 * Get page-specific sidebar content
 */
function recruitpro_get_page_sidebar_content() {
    $page_template = get_page_template_slug();
    $output = '';
    
    if (strpos($page_template, 'contact') !== false) {
        $output .= recruitpro_get_contact_sidebar_widget();
    } elseif (strpos($page_template, 'about') !== false) {
        $output .= recruitpro_get_about_sidebar_widget();
    } elseif (strpos($page_template, 'services') !== false) {
        $output .= recruitpro_get_services_sidebar_widget();
    }
    
    return $output;
}

/**
 * Get contact page sidebar widget
 */
function recruitpro_get_contact_sidebar_widget() {
    return '
    <div class="widget widget-contact-info">
        <h3 class="widget-title">' . esc_html__('Get in Touch', 'recruitpro') . '</h3>
        <div class="contact-info-list">
            <div class="contact-item">
                <strong>' . esc_html__('Phone:', 'recruitpro') . '</strong>
                <span>' . esc_html(get_theme_mod('recruitpro_phone', '+1 (555) 123-4567')) . '</span>
            </div>
            <div class="contact-item">
                <strong>' . esc_html__('Email:', 'recruitpro') . '</strong>
                <span>' . esc_html(get_theme_mod('recruitpro_email', 'contact@recruitpro.com')) . '</span>
            </div>
            <div class="contact-item">
                <strong>' . esc_html__('Office Hours:', 'recruitpro') . '</strong>
                <span>' . esc_html__('Monday - Friday: 8:00 AM - 6:00 PM', 'recruitpro') . '</span>
            </div>
        </div>
    </div>';
}

/**
 * Get about page sidebar widget
 */
function recruitpro_get_about_sidebar_widget() {
    return '
    <div class="widget widget-company-stats">
        <h3 class="widget-title">' . esc_html__('Company Highlights', 'recruitpro') . '</h3>
        <div class="stats-list">
            <div class="stat-item">
                <span class="stat-number">' . esc_html(get_theme_mod('recruitpro_years_experience', '15+')) . '</span>
                <span class="stat-label">' . esc_html__('Years Experience', 'recruitpro') . '</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">' . esc_html(get_theme_mod('recruitpro_candidates_placed', '5000+')) . '</span>
                <span class="stat-label">' . esc_html__('Candidates Placed', 'recruitpro') . '</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">' . esc_html(get_theme_mod('recruitpro_partner_companies', '500+')) . '</span>
                <span class="stat-label">' . esc_html__('Partner Companies', 'recruitpro') . '</span>
            </div>
        </div>
    </div>';
}

/**
 * Get services page sidebar widget
 */
function recruitpro_get_services_sidebar_widget() {
    return '
    <div class="widget widget-service-cta">
        <h3 class="widget-title">' . esc_html__('Need Custom Solutions?', 'recruitpro') . '</h3>
        <p>' . esc_html__('Every business has unique hiring needs. Let us create a customized recruitment strategy for your organization.', 'recruitpro') . '</p>
        <a href="' . esc_url(home_url('/contact/')) . '" class="btn btn-primary btn-block">
            ' . esc_html__('Discuss Your Needs', 'recruitpro') . '
        </a>
    </div>';
}

/**
 * Fallback sidebar for pages without specific content
 */
function recruitpro_fallback_page_sidebar() {
    ?>
    <!-- Page Navigation -->
    <div class="widget widget-page-navigation">
        <h3 class="widget-title"><?php esc_html_e('Quick Navigation', 'recruitpro'); ?></h3>
        <ul class="page-nav-list">
            <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About Us', 'recruitpro'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/services/')); ?>"><?php esc_html_e('Our Services', 'recruitpro'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/jobs/')); ?>"><?php esc_html_e('Current Opportunities', 'recruitpro'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'recruitpro'); ?></a></li>
        </ul>
    </div>

    <!-- Call to Action -->
    <div class="widget widget-cta">
        <h3 class="widget-title"><?php esc_html_e('Ready to Get Started?', 'recruitpro'); ?></h3>
        <p><?php esc_html_e('Connect with our recruitment experts to discuss your hiring needs or career goals.', 'recruitpro'); ?></p>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary btn-block">
            <?php esc_html_e('Get in Touch', 'recruitpro'); ?>
        </a>
    </div>
    <?php
}
?>