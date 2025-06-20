<?php
/**
 * The sidebar containing the main widget area
 *
 * Professional sidebar template for recruitment agencies featuring industry-
 * specific widgets, call-to-action sections, and recruitment-focused content.
 * Designed to provide valuable resources for job seekers and employers while
 * maintaining professional presentation and driving engagement with agency services.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/sidebar.php
 * Purpose: Main sidebar template with professional widgets
 * Context: Recruitment industry sidebar content
 * Features: Professional widgets, CTAs, industry-focused content
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if sidebar should be displayed
if (!is_active_sidebar('sidebar-1') && !recruitpro_has_fallback_content()) {
    return;
}

// Get current context for sidebar customization
$sidebar_context = recruitpro_get_sidebar_context();

?>

<aside id="secondary" class="widget-area sidebar-area <?php echo esc_attr($sidebar_context['class']); ?>" role="complementary">
    <div class="sidebar-inner">
        
        <?php if (is_active_sidebar('sidebar-1')) : ?>
            
            <!-- Dynamic Sidebar Content -->
            <?php dynamic_sidebar('sidebar-1'); ?>
            
        <?php endif; ?>

        <!-- Fallback Professional Content -->
        <?php if (!is_active_sidebar('sidebar-1') || get_theme_mod('recruitpro_show_fallback_widgets', true)) : ?>
            
            <!-- Search Widget -->
            <?php if ($sidebar_context['show_search']) : ?>
                <div class="widget widget-search">
                    <h3 class="widget-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <?php echo esc_html($sidebar_context['search_title']); ?>
                    </h3>
                    <div class="widget-content">
                        <form class="sidebar-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="search-input-wrapper">
                                <input type="search" 
                                       name="s" 
                                       class="search-field" 
                                       placeholder="<?php echo esc_attr($sidebar_context['search_placeholder']); ?>"
                                       value="<?php echo esc_attr(get_search_query()); ?>"
                                       aria-label="<?php esc_attr_e('Search content', 'recruitpro'); ?>">
                                <button type="submit" class="search-submit" aria-label="<?php esc_attr_e('Submit search', 'recruitpro'); ?>">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Quick Search Links -->
                        <div class="quick-search-links">
                            <span class="quick-search-label"><?php esc_html_e('Popular:', 'recruitpro'); ?></span>
                            <a href="<?php echo esc_url(home_url('/?s=remote+jobs')); ?>" class="quick-search-link">
                                <?php esc_html_e('Remote Jobs', 'recruitpro'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/?s=career+advice')); ?>" class="quick-search-link">
                                <?php esc_html_e('Career Advice', 'recruitpro'); ?>
                            </a>
                            <a href="<?php echo esc_url(home_url('/?s=salary+guide')); ?>" class="quick-search-link">
                                <?php esc_html_e('Salary Guide', 'recruitpro'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Information Widget -->
            <div class="widget widget-contact-info">
                <h3 class="widget-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <?php esc_html_e('Get in Touch', 'recruitpro'); ?>
                </h3>
                <div class="widget-content">
                    <div class="contact-info-list">
                        
                        <!-- Phone -->
                        <?php $phone = get_theme_mod('recruitpro_phone', '+1 (555) 123-4567'); ?>
                        <div class="contact-item contact-phone">
                            <div class="contact-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label"><?php esc_html_e('Phone:', 'recruitpro'); ?></span>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" class="contact-value">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <?php $email = get_theme_mod('recruitpro_email', 'contact@recruitpro.com'); ?>
                        <div class="contact-item contact-email">
                            <div class="contact-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label"><?php esc_html_e('Email:', 'recruitpro'); ?></span>
                                <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-value">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Office Hours -->
                        <div class="contact-item contact-hours">
                            <div class="contact-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                                    <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label"><?php esc_html_e('Office Hours:', 'recruitpro'); ?></span>
                                <span class="contact-value">
                                    <?php echo esc_html(get_theme_mod('recruitpro_office_hours', __('Mon-Fri: 8:00 AM - 6:00 PM', 'recruitpro'))); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Address -->
                        <?php $address = get_theme_mod('recruitpro_address', ''); ?>
                        <?php if (!empty($address)) : ?>
                            <div class="contact-item contact-address">
                                <div class="contact-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <div class="contact-details">
                                    <span class="contact-label"><?php esc_html_e('Address:', 'recruitpro'); ?></span>
                                    <span class="contact-value"><?php echo esc_html($address); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="contact-cta">
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary btn-sm btn-block">
                            <?php esc_html_e('Contact Us Today', 'recruitpro'); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Content Widget -->
            <?php if ($sidebar_context['show_recent']) : ?>
                <div class="widget widget-recent-posts">
                    <h3 class="widget-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <?php echo esc_html($sidebar_context['recent_title']); ?>
                    </h3>
                    <div class="widget-content">
                        <?php echo recruitpro_get_recent_content_widget($sidebar_context['recent_post_type']); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Newsletter Signup Widget -->
            <div class="widget widget-newsletter">
                <h3 class="widget-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <?php esc_html_e('Industry Updates', 'recruitpro'); ?>
                </h3>
                <div class="widget-content">
                    <p class="newsletter-description">
                        <?php esc_html_e('Get weekly recruitment insights, career tips, and industry trends delivered to your inbox.', 'recruitpro'); ?>
                    </p>
                    
                    <form class="newsletter-form" method="post" action="#" id="sidebar-newsletter-signup">
                        <div class="newsletter-input-group">
                            <input type="email" 
                                   name="newsletter_email" 
                                   class="newsletter-field" 
                                   placeholder="<?php esc_attr_e('Your professional email', 'recruitpro'); ?>" 
                                   required
                                   aria-label="<?php esc_attr_e('Email address for newsletter', 'recruitpro'); ?>">
                            <button type="submit" class="newsletter-submit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                                <span class="screen-reader-text"><?php esc_html_e('Subscribe', 'recruitpro'); ?></span>
                            </button>
                        </div>
                        
                        <p class="newsletter-privacy">
                            <small>
                                <?php 
                                printf(
                                    esc_html__('By subscribing, you agree to our %1$sPrivacy Policy%2$s. Unsubscribe anytime.', 'recruitpro'),
                                    '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
                                    '</a>'
                                );
                                ?>
                            </small>
                        </p>
                    </form>
                </div>
            </div>

            <!-- Categories Widget -->
            <?php if ($sidebar_context['show_categories']) : ?>
                <div class="widget widget-categories">
                    <h3 class="widget-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        <?php esc_html_e('Browse Topics', 'recruitpro'); ?>
                    </h3>
                    <div class="widget-content">
                        <ul class="categories-list">
                            <?php
                            $categories = get_categories(array(
                                'orderby' => 'count',
                                'order' => 'DESC',
                                'number' => 8,
                                'hide_empty' => true
                            ));
                            
                            foreach ($categories as $category) {
                                echo '<li class="category-item">';
                                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="category-link">';
                                echo '<span class="category-name">' . esc_html($category->name) . '</span>';
                                echo '<span class="category-count">(' . $category->count . ')</span>';
                                echo '</a>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                        
                        <div class="categories-footer">
                            <a href="<?php echo esc_url(home_url('/categories/')); ?>" class="view-all-categories">
                                <?php esc_html_e('View All Topics', 'recruitpro'); ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M8.59 16.58L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.42z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Professional CTA Widget -->
            <div class="widget widget-professional-cta">
                <h3 class="widget-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A2.997 2.997 0 0 0 17.11 7H16.5c-.8 0-1.54.37-2.01.99l-1.04 1.38c-.17.23-.26.5-.26.78s.09.55.26.78l.83 1.1.07.11c.09.16.14.34.14.53 0 .61-.49 1.1-1.1 1.1-.31 0-.58-.16-.74-.42l-.83-1.1c-.17-.23-.26-.5-.26-.78s.09-.55.26-.78l1.04-1.38C12.96 9.37 13.7 9 14.5 9h.61c.46 0 .88-.21 1.16-.56L17.31 7H20v11z"/>
                    </svg>
                    <?php echo esc_html($sidebar_context['cta_title']); ?>
                </h3>
                <div class="widget-content">
                    <p class="cta-description">
                        <?php echo esc_html($sidebar_context['cta_description']); ?>
                    </p>
                    
                    <div class="cta-buttons">
                        <a href="<?php echo esc_url($sidebar_context['cta_primary_url']); ?>" class="btn btn-primary btn-sm btn-block">
                            <?php echo esc_html($sidebar_context['cta_primary_text']); ?>
                        </a>
                        
                        <a href="<?php echo esc_url($sidebar_context['cta_secondary_url']); ?>" class="btn btn-outline btn-sm btn-block">
                            <?php echo esc_html($sidebar_context['cta_secondary_text']); ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Social Media Widget -->
            <div class="widget widget-social-media">
                <h3 class="widget-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                        <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
                    </svg>
                    <?php esc_html_e('Follow Us', 'recruitpro'); ?>
                </h3>
                <div class="widget-content">
                    <p class="social-description">
                        <?php esc_html_e('Stay connected with us on social media for the latest updates and industry insights.', 'recruitpro'); ?>
                    </p>
                    
                    <div class="social-links">
                        <?php
                        $social_links = array(
                            'linkedin' => array(
                                'url' => get_theme_mod('recruitpro_linkedin_url', '#'),
                                'label' => 'LinkedIn',
                                'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>'
                            ),
                            'twitter' => array(
                                'url' => get_theme_mod('recruitpro_twitter_url', '#'),
                                'label' => 'Twitter',
                                'icon' => '<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>'
                            ),
                            'facebook' => array(
                                'url' => get_theme_mod('recruitpro_facebook_url', '#'),
                                'label' => 'Facebook',
                                'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>'
                            ),
                            'instagram' => array(
                                'url' => get_theme_mod('recruitpro_instagram_url', '#'),
                                'label' => 'Instagram',
                                'icon' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>'
                            )
                        );
                        
                        foreach ($social_links as $platform => $data) {
                            if (!empty($data['url']) && $data['url'] !== '#') {
                                echo '<a href="' . esc_url($data['url']) . '" class="social-link social-' . esc_attr($platform) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr(sprintf(__('Follow us on %s', 'recruitpro'), $data['label'])) . '">';
                                echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">' . $data['icon'] . '</svg>';
                                echo '<span class="social-label">' . esc_html($data['label']) . '</span>';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Archive Widget -->
            <?php if ($sidebar_context['show_archives']) : ?>
                <div class="widget widget-archives">
                    <h3 class="widget-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2.5-9H18V1h-2v1H8V1H6v1H4.5C3.11 2 2 3.11 2 4.5v15C2 20.89 3.11 22 4.5 22h15c1.39 0 2.5-1.11 2.5-2.5v-15C22 3.11 20.89 2 19.5 2z"/>
                        </svg>
                        <?php esc_html_e('Content Archives', 'recruitpro'); ?>
                    </h3>
                    <div class="widget-content">
                        <ul class="archives-list">
                            <?php wp_get_archives(array(
                                'type' => 'monthly',
                                'show_post_count' => true,
                                'limit' => 12,
                                'format' => 'html'
                            )); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Links Widget -->
            <div class="widget widget-quick-links">
                <h3 class="widget-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="widget-icon">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    <?php esc_html_e('Quick Links', 'recruitpro'); ?>
                </h3>
                <div class="widget-content">
                    <ul class="quick-links-list">
                        <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About Our Agency', 'recruitpro'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/services/')); ?>"><?php esc_html_e('Recruitment Services', 'recruitpro'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/jobs/')); ?>"><?php esc_html_e('Current Opportunities', 'recruitpro'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Career Insights', 'recruitpro'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'recruitpro'); ?></a></li>
                        <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacy Policy', 'recruitpro'); ?></a></li>
                    </ul>
                </div>
            </div>

        <?php endif; ?>
    </div>
</aside>

<?php
/**
 * Helper Functions for Sidebar Template
 */

/**
 * Check if fallback content should be displayed
 */
function recruitpro_has_fallback_content() {
    return get_theme_mod('recruitpro_show_fallback_widgets', true);
}

/**
 * Get sidebar context based on current page
 */
function recruitpro_get_sidebar_context() {
    $context = array(
        'class' => 'sidebar-default',
        'show_search' => true,
        'show_recent' => true,
        'show_categories' => true,
        'show_archives' => true,
        'search_title' => esc_html__('Search', 'recruitpro'),
        'search_placeholder' => esc_attr__('Search content...', 'recruitpro'),
        'recent_title' => esc_html__('Recent Posts', 'recruitpro'),
        'recent_post_type' => 'post',
        'cta_title' => esc_html__('Need Recruitment Help?', 'recruitpro'),
        'cta_description' => esc_html__('Our recruitment experts are ready to help you find the perfect job or hire the right talent.', 'recruitpro'),
        'cta_primary_text' => esc_html__('Get Started', 'recruitpro'),
        'cta_primary_url' => home_url('/contact/'),
        'cta_secondary_text' => esc_html__('Learn More', 'recruitpro'),
        'cta_secondary_url' => home_url('/services/'),
    );
    
    // Customize based on current page context
    if (is_home() || is_category() || is_tag() || is_archive()) {
        $context['class'] = 'sidebar-blog';
        $context['search_title'] = esc_html__('Search Articles', 'recruitpro');
        $context['search_placeholder'] = esc_attr__('Search insights...', 'recruitpro');
    } elseif (is_page()) {
        $context['class'] = 'sidebar-page';
        $context['show_archives'] = false;
        $context['recent_title'] = esc_html__('Related Content', 'recruitpro');
        
        // Customize for specific pages
        if (is_page_template('page-contact.php')) {
            $context['cta_title'] = esc_html__('More Ways to Connect', 'recruitpro');
            $context['cta_description'] = esc_html__('Explore different ways to get in touch and find the information you need.', 'recruitpro');
        } elseif (is_page_template('page-about.php')) {
            $context['cta_title'] = esc_html__('Ready to Work Together?', 'recruitpro');
            $context['cta_description'] = esc_html__('Let us help you achieve your recruitment goals with our professional services.', 'recruitpro');
        }
    } elseif (is_search()) {
        $context['class'] = 'sidebar-search';
        $context['show_search'] = false; // Search form is in main content
        $context['search_title'] = esc_html__('Refine Search', 'recruitpro');
    } elseif (is_single()) {
        $context['class'] = 'sidebar-single';
        $context['show_categories'] = true;
        
        if (get_post_type() === 'job') {
            $context['recent_title'] = esc_html__('Similar Opportunities', 'recruitpro');
            $context['recent_post_type'] = 'job';
            $context['cta_title'] = esc_html__('Looking for More Jobs?', 'recruitpro');
            $context['cta_description'] = esc_html__('Explore more career opportunities or get personalized job recommendations.', 'recruitpro');
            $context['cta_primary_text'] = esc_html__('Browse All Jobs', 'recruitpro');
            $context['cta_primary_url'] = home_url('/jobs/');
        }
    }
    
    return $context;
}

/**
 * Get recent content widget based on post type
 */
function recruitpro_get_recent_content_widget($post_type = 'post') {
    $recent_posts = get_posts(array(
        'numberposts' => 5,
        'post_type' => $post_type,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (empty($recent_posts)) {
        return '<p class="no-recent-content">' . 
               sprintf(esc_html__('No recent %s available.', 'recruitpro'), $post_type === 'job' ? 'opportunities' : 'content') . 
               '</p>';
    }
    
    $output = '<ul class="recent-posts-list">';
    foreach ($recent_posts as $post) {
        $post_title = get_the_title($post->ID);
        $post_date = get_the_date('', $post->ID);
        $post_url = get_permalink($post->ID);
        
        // Add job-specific meta
        $meta_html = '';
        if ($post_type === 'job') {
            $job_location = get_post_meta($post->ID, '_job_location', true);
            $job_type = get_post_meta($post->ID, '_job_type', true);
            
            if ($job_location || $job_type) {
                $meta_parts = array_filter(array($job_type, $job_location));
                $meta_html = '<span class="job-meta">' . implode(' • ', $meta_parts) . '</span>';
            }
        }
        
        $output .= sprintf(
            '<li class="recent-post-item">
                <h4 class="recent-post-title">
                    <a href="%s" title="%s">%s</a>
                </h4>
                <div class="recent-post-meta">
                    <time datetime="%s" class="recent-post-date">%s</time>
                    %s
                </div>
            </li>',
            esc_url($post_url),
            esc_attr($post_title),
            esc_html($post_title),
            esc_attr(get_the_date('c', $post->ID)),
            esc_html($post_date),
            $meta_html
        );
    }
    $output .= '</ul>';
    
    return $output;
}
?>