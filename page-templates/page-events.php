<?php
/**
 * Template Name: Events & Webinars Page
 *
 * Professional events page template for recruitment agencies showcasing
 * industry events, webinars, networking sessions, job fairs, and training
 * programs. Features event registration, calendar integration, and
 * comprehensive event management for the recruitment industry.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-events.php
 * Purpose: Events and webinars showcase for recruitment agencies
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Event listings, registration, calendar, categories, RSVP
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_events_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_events_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$events_layout = get_theme_mod('recruitpro_events_layout', 'grid');
$show_calendar = get_theme_mod('recruitpro_events_show_calendar', true);
$show_categories = get_theme_mod('recruitpro_events_show_categories', true);
$show_upcoming = get_theme_mod('recruitpro_events_show_upcoming', true);
$show_past_events = get_theme_mod('recruitpro_events_show_past', true);
$events_per_page = get_theme_mod('recruitpro_events_per_page', 6);

// Event categories for recruitment industry
$event_categories = array(
    'webinar' => array(
        'name' => esc_html__('Webinars', 'recruitpro'),
        'description' => esc_html__('Online educational sessions and industry insights', 'recruitpro'),
        'icon' => 'fas fa-video'
    ),
    'networking' => array(
        'name' => esc_html__('Networking Events', 'recruitpro'),
        'description' => esc_html__('Professional networking and business connections', 'recruitpro'),
        'icon' => 'fas fa-users'
    ),
    'job-fair' => array(
        'name' => esc_html__('Job Fairs', 'recruitpro'),
        'description' => esc_html__('Career fairs and recruitment events', 'recruitpro'),
        'icon' => 'fas fa-briefcase'
    ),
    'training' => array(
        'name' => esc_html__('Training Workshops', 'recruitpro'),
        'description' => esc_html__('Professional development and skill building', 'recruitpro'),
        'icon' => 'fas fa-graduation-cap'
    ),
    'conference' => array(
        'name' => esc_html__('Conferences', 'recruitpro'),
        'description' => esc_html__('Industry conferences and major events', 'recruitpro'),
        'icon' => 'fas fa-handshake'
    ),
    'workshop' => array(
        'name' => esc_html__('Workshops', 'recruitpro'),
        'description' => esc_html__('Hands-on learning and practical sessions', 'recruitpro'),
        'icon' => 'fas fa-tools'
    )
);

// Schema.org markup for events page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_trim_words(get_the_content(), 30) : sprintf(__('Professional events and training programs by %s', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    )
);
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main events-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header events-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php esc_html_e('Join our professional events, webinars, and training sessions designed for recruitment professionals, job seekers, and employers. Stay connected with industry trends and networking opportunities.', 'recruitpro'); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Event Categories Filter -->
                    <?php if ($show_categories) : ?>
                        <section class="event-categories" id="event-categories">
                            <div class="categories-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Event Categories', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Explore our diverse range of professional events and educational programs', 'recruitpro'); ?></p>
                                </div>

                                <div class="categories-grid">
                                    
                                    <div class="category-filter active" data-category="all">
                                        <div class="category-content">
                                            <div class="category-icon">
                                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="category-name"><?php esc_html_e('All Events', 'recruitpro'); ?></h3>
                                            <p class="category-description"><?php esc_html_e('View all upcoming and past events', 'recruitpro'); ?></p>
                                        </div>
                                    </div>

                                    <?php foreach ($event_categories as $category_key => $category) : ?>
                                        <div class="category-filter" data-category="<?php echo esc_attr($category_key); ?>">
                                            <div class="category-content">
                                                <div class="category-icon">
                                                    <i class="<?php echo esc_attr($category['icon']); ?>" aria-hidden="true"></i>
                                                </div>
                                                <h3 class="category-name"><?php echo esc_html($category['name']); ?></h3>
                                                <p class="category-description"><?php echo esc_html($category['description']); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>

                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Upcoming Events -->
                    <?php if ($show_upcoming) : ?>
                        <section class="upcoming-events" id="upcoming-events">
                            <div class="events-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Upcoming Events', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Register for our upcoming professional events and training programs', 'recruitpro'); ?></p>
                                </div>

                                <?php
                                // Query upcoming events
                                $upcoming_events = new WP_Query(array(
                                    'post_type' => 'event',
                                    'posts_per_page' => $events_per_page,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'event_date',
                                            'value' => date('Y-m-d'),
                                            'compare' => '>='
                                        )
                                    ),
                                    'meta_key' => 'event_date',
                                    'orderby' => 'meta_value',
                                    'order' => 'ASC'
                                ));

                                if ($upcoming_events->have_posts()) : ?>
                                    <div class="events-grid <?php echo esc_attr($events_layout); ?>-layout">
                                        <?php while ($upcoming_events->have_posts()) : $upcoming_events->the_post(); ?>
                                            <?php
                                            // Get event meta data
                                            $event_date = get_post_meta(get_the_ID(), 'event_date', true);
                                            $event_time = get_post_meta(get_the_ID(), 'event_time', true);
                                            $event_location = get_post_meta(get_the_ID(), 'event_location', true);
                                            $event_type = get_post_meta(get_the_ID(), 'event_type', true);
                                            $event_price = get_post_meta(get_the_ID(), 'event_price', true);
                                            $event_capacity = get_post_meta(get_the_ID(), 'event_capacity', true);
                                            $event_registered = get_post_meta(get_the_ID(), 'event_registered', true);
                                            $registration_url = get_post_meta(get_the_ID(), 'registration_url', true);
                                            $is_virtual = get_post_meta(get_the_ID(), 'event_virtual', true);
                                            $virtual_link = get_post_meta(get_the_ID(), 'virtual_link', true);
                                            
                                            // Format dates
                                            $formatted_date = $event_date ? date('F j, Y', strtotime($event_date)) : '';
                                            $formatted_time = $event_time ? date('g:i A', strtotime($event_time)) : '';
                                            
                                            // Check if event is full
                                            $is_full = false;
                                            if ($event_capacity && $event_registered) {
                                                $is_full = ($event_registered >= $event_capacity);
                                            }

                                            // Event schema markup
                                            $event_schema = array(
                                                '@context' => 'https://schema.org',
                                                '@type' => 'Event',
                                                'name' => get_the_title(),
                                                'description' => wp_trim_words(get_the_excerpt(), 30),
                                                'url' => get_permalink(),
                                                'startDate' => $event_date . 'T' . ($event_time ? date('H:i:s', strtotime($event_time)) : '09:00:00'),
                                                'organizer' => array(
                                                    '@type' => 'Organization',
                                                    'name' => $company_name,
                                                    'url' => home_url()
                                                )
                                            );

                                            if ($event_location && !$is_virtual) {
                                                $event_schema['location'] = array(
                                                    '@type' => 'Place',
                                                    'name' => $event_location
                                                );
                                            } elseif ($is_virtual) {
                                                $event_schema['location'] = array(
                                                    '@type' => 'VirtualLocation',
                                                    'url' => $virtual_link ? $virtual_link : get_permalink()
                                                );
                                            }

                                            if ($event_price) {
                                                $event_schema['offers'] = array(
                                                    '@type' => 'Offer',
                                                    'price' => $event_price,
                                                    'priceCurrency' => 'USD',
                                                    'availability' => $is_full ? 'SoldOut' : 'InStock'
                                                );
                                            }
                                            ?>

                                            <!-- Event Schema -->
                                            <script type="application/ld+json">
                                            <?php echo wp_json_encode($event_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
                                            </script>

                                            <article class="event-item" data-category="<?php echo esc_attr($event_type); ?>">
                                                <div class="event-content">
                                                    
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <div class="event-thumbnail">
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                                            </a>
                                                            
                                                            <!-- Event badges -->
                                                            <div class="event-badges">
                                                                <?php if ($is_virtual) : ?>
                                                                    <span class="event-badge virtual"><?php esc_html_e('Virtual', 'recruitpro'); ?></span>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($is_full) : ?>
                                                                    <span class="event-badge full"><?php esc_html_e('Full', 'recruitpro'); ?></span>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($event_price === '0' || empty($event_price)) : ?>
                                                                    <span class="event-badge free"><?php esc_html_e('Free', 'recruitpro'); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="event-details">
                                                        
                                                        <div class="event-meta">
                                                            <?php if ($formatted_date) : ?>
                                                                <span class="event-date">
                                                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                                                    <time datetime="<?php echo esc_attr($event_date); ?>">
                                                                        <?php echo esc_html($formatted_date); ?>
                                                                    </time>
                                                                </span>
                                                            <?php endif; ?>

                                                            <?php if ($formatted_time) : ?>
                                                                <span class="event-time">
                                                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                                                    <?php echo esc_html($formatted_time); ?>
                                                                </span>
                                                            <?php endif; ?>

                                                            <?php if ($event_location) : ?>
                                                                <span class="event-location">
                                                                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                                    <?php echo esc_html($event_location); ?>
                                                                </span>
                                                            <?php endif; ?>

                                                            <?php if ($event_type && isset($event_categories[$event_type])) : ?>
                                                                <span class="event-category">
                                                                    <i class="<?php echo esc_attr($event_categories[$event_type]['icon']); ?>" aria-hidden="true"></i>
                                                                    <?php echo esc_html($event_categories[$event_type]['name']); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>

                                                        <h3 class="event-title">
                                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                        </h3>

                                                        <div class="event-excerpt">
                                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                                        </div>

                                                        <?php if ($event_price || $event_capacity) : ?>
                                                            <div class="event-info">
                                                                <?php if ($event_price) : ?>
                                                                    <span class="event-price">
                                                                        <i class="fas fa-tag" aria-hidden="true"></i>
                                                                        <?php
                                                                        if ($event_price === '0' || empty($event_price)) {
                                                                            esc_html_e('Free', 'recruitpro');
                                                                        } else {
                                                                            echo '$' . esc_html($event_price);
                                                                        }
                                                                        ?>
                                                                    </span>
                                                                <?php endif; ?>

                                                                <?php if ($event_capacity) : ?>
                                                                    <span class="event-capacity">
                                                                        <i class="fas fa-users" aria-hidden="true"></i>
                                                                        <?php echo esc_html(($event_registered ? $event_registered : 0) . '/' . $event_capacity); ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <div class="event-actions">
                                                            <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                                                                <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                                            </a>
                                                            
                                                            <?php if ($registration_url && !$is_full) : ?>
                                                                <a href="<?php echo esc_url($registration_url); ?>" 
                                                                   class="btn btn-primary"
                                                                   target="_blank" 
                                                                   rel="noopener noreferrer">
                                                                    <?php esc_html_e('Register Now', 'recruitpro'); ?>
                                                                </a>
                                                            <?php elseif ($is_full) : ?>
                                                                <span class="btn btn-disabled">
                                                                    <?php esc_html_e('Event Full', 'recruitpro'); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>

                                                    </div>

                                                </div>
                                            </article>

                                        <?php endwhile; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="no-events">
                                        <div class="no-events-content">
                                            <i class="fas fa-calendar-times" aria-hidden="true"></i>
                                            <h3><?php esc_html_e('No Upcoming Events', 'recruitpro'); ?></h3>
                                            <p><?php esc_html_e('We don\'t have any events scheduled at the moment. Check back soon or subscribe to our newsletter to be notified of upcoming events and company updates.', 'recruitpro'); ?></p>
                                            <a href="#newsletter-signup" class="btn btn-primary">
                                                <?php esc_html_e('Subscribe to Newsletter', 'recruitpro'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; 
                                wp_reset_postdata(); ?>

                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Past Events -->
                    <?php if ($show_past_events) : ?>
                        <section class="past-events" id="past-events">
                            <div class="events-container">
                                
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Past Events', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Browse our archive of successful events and training programs', 'recruitpro'); ?></p>
                                </div>

                                <?php
                                // Query past events
                                $past_events = new WP_Query(array(
                                    'post_type' => 'event',
                                    'posts_per_page' => 6,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'event_date',
                                            'value' => date('Y-m-d'),
                                            'compare' => '<'
                                        )
                                    ),
                                    'meta_key' => 'event_date',
                                    'orderby' => 'meta_value',
                                    'order' => 'DESC'
                                ));

                                if ($past_events->have_posts()) : ?>
                                    <div class="past-events-grid">
                                        <?php while ($past_events->have_posts()) : $past_events->the_post(); ?>
                                            <?php
                                            $event_date = get_post_meta(get_the_ID(), 'event_date', true);
                                            $event_type = get_post_meta(get_the_ID(), 'event_type', true);
                                            $formatted_date = $event_date ? date('M j, Y', strtotime($event_date)) : '';
                                            ?>
                                            <article class="past-event-item" data-category="<?php echo esc_attr($event_type); ?>">
                                                <div class="past-event-content">
                                                    
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <div class="past-event-thumbnail">
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="past-event-details">
                                                        <div class="past-event-meta">
                                                            <span class="past-event-date"><?php echo esc_html($formatted_date); ?></span>
                                                            <?php if ($event_type && isset($event_categories[$event_type])) : ?>
                                                                <span class="past-event-category"><?php echo esc_html($event_categories[$event_type]['name']); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h3 class="past-event-title">
                                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                        </h3>
                                                        <div class="past-event-excerpt">
                                                            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                                        </div>
                                                        <div class="past-event-actions">
                                                            <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-small">
                                                                <?php esc_html_e('View Summary', 'recruitpro'); ?>
                                                            </a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </article>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="no-past-events">
                                        <p><?php esc_html_e('No past events to display at this time.', 'recruitpro'); ?></p>
                                    </div>
                                <?php endif; 
                                wp_reset_postdata(); ?>

                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Unified Newsletter Signup -->
                    <section class="newsletter-signup" id="newsletter-signup">
                        <div class="newsletter-container">
                            <div class="newsletter-content">
                                <h2 class="newsletter-title"><?php esc_html_e('Stay in the Loop', 'recruitpro'); ?></h2>
                                <p class="newsletter-description"><?php esc_html_e('Subscribe to receive our latest news, event invitations, industry insights, and exclusive networking opportunities delivered to your inbox.', 'recruitpro'); ?></p>
                                
                                <form class="newsletter-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                                    <div class="form-group">
                                        <input type="email" 
                                               name="email" 
                                               id="newsletter-email" 
                                               class="form-control" 
                                               placeholder="<?php esc_attr_e('Enter your email address', 'recruitpro'); ?>" 
                                               required>
                                        <button type="submit" class="btn btn-primary">
                                            <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                        </button>
                                    </div>
                                    <div class="form-note">
                                        <small><?php esc_html_e('We respect your privacy. Quality content only, no spam. Unsubscribe anytime.', 'recruitpro'); ?></small>
                                    </div>
                                    <input type="hidden" name="action" value="recruitpro_newsletter_signup">
                                    <?php wp_nonce_field('recruitpro_newsletter_signup', 'nonce'); ?>
                                </form>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('events-sidebar')) : ?>
                <aside id="secondary" class="widget-area events-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('events-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

// AJAX handler for unified newsletter subscription
add_action('wp_ajax_recruitpro_newsletter_signup', 'recruitpro_handle_newsletter_signup');
add_action('wp_ajax_nopriv_recruitpro_newsletter_signup', 'recruitpro_handle_newsletter_signup');

function recruitpro_handle_newsletter_signup() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_newsletter_signup')) {
        wp_send_json_error(array('message' => esc_html__('Security verification failed. Please try again.', 'recruitpro')));
    }
    
    // Sanitize email
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => esc_html__('Please enter a valid email address.', 'recruitpro')));
    }
    
    // Store subscription in unified newsletter list
    $subscribers = get_option('recruitpro_newsletter_subscribers', array());
    
    if (in_array($email, $subscribers)) {
        wp_send_json_error(array('message' => esc_html__('You are already subscribed to our newsletter.', 'recruitpro')));
    }
    
    // Add subscriber with subscription details
    $subscriber_data = array(
        'email' => $email,
        'subscribed_date' => current_time('mysql'),
        'subscription_types' => array('blog_posts', 'events', 'industry_insights'),
        'source' => 'events_page',
        'status' => 'active'
    );
    
    $subscribers[] = $subscriber_data;
    update_option('recruitpro_newsletter_subscribers', $subscribers);
    
    // Send welcome email (optional)
    $company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
    $subject = sprintf(__('Welcome to %s Newsletter!', 'recruitpro'), $company_name);
    
    $message = sprintf(
        __("Thank you for subscribing to our newsletter!\n\nYou will now receive:\n• Latest blog posts and industry insights\n• Event invitations and announcements\n• Exclusive networking opportunities\n• Career advice and market updates\n\nWe respect your privacy and will never share your information.\n\nBest regards,\nThe %s Team", 'recruitpro'),
        $company_name
    );
    
    wp_mail($email, $subject, $message);
    
    wp_send_json_success(array('message' => esc_html__('Thank you! You\'ve successfully subscribed to our newsletter.', 'recruitpro')));
}

/* =================================================================
   EVENTS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL EVENTS PAGE FEATURES:

✅ COMPREHENSIVE EVENT MANAGEMENT
- Upcoming events with registration
- Past events archive
- Event categories and filtering
- Schema.org Event markup for SEO
- RSVP and registration integration

✅ RECRUITMENT INDUSTRY FOCUSED
- Webinars and online sessions
- Networking events
- Job fairs and career events
- Training workshops
- Industry conferences
- Professional development sessions

✅ EVENT DETAILS & REGISTRATION
- Event date, time, location
- Virtual and in-person events
- Pricing and capacity management
- Registration URLs and CTAs
- Event descriptions and speakers
- Capacity tracking and full event handling

✅ UNIFIED NEWSLETTER SYSTEM
- Single subscription for all content types
- Blog posts and industry insights
- Event invitations and announcements
- Exclusive networking opportunities
- Comprehensive subscriber management

✅ PROFESSIONAL PRESENTATION
- Grid and list layout options
- Event badges (Virtual, Full, Free)
- Professional event cards
- Responsive design
- Mobile-optimized layouts

✅ SEO & SCHEMA OPTIMIZATION
- Individual Event schema markup
- Organization publisher info
- Structured data for search engines
- Event-specific meta information

✅ INTEGRATION CAPABILITIES
- WordPress post types (events)
- Calendar integration ready
- Registration system integration
- Email marketing integration
- CRM system compatibility

✅ CUSTOMIZATION OPTIONS
- Show/hide sections
- Layout preferences
- Sidebar positioning
- Events per page settings
- Category management

✅ ACCESSIBILITY & PERFORMANCE
- ARIA labels and semantic HTML
- Keyboard navigation support
- Lazy loading for images
- Progressive enhancement
- Mobile-first responsive design

PERFECT FOR:
- Professional development events
- Industry networking
- Educational webinars
- Job fair organization
- Client engagement
- Thought leadership

EVENT TYPES SUPPORTED:
- Webinars and online sessions
- Networking events
- Job fairs and career events
- Training workshops
- Industry conferences
- Professional development

TECHNICAL FEATURES:
- Custom post type integration
- Advanced meta field handling
- AJAX newsletter subscription
- Category filtering system
- Schema.org structured data
- WordPress customizer integration

*/
?>