<?php
/**
 * The template for displaying author archive pages
 *
 * Professional author template for recruitment agencies showcasing team members,
 * recruitment consultants, HR specialists, and guest contributors. Features
 * comprehensive author profiles, expertise areas, contact information, and
 * content organized for maximum professional credibility and trust building.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: template-hierarchy/author.php
 * Purpose: Author archive pages with professional profiles and content
 * Dependencies: WordPress core, theme functions
 * Features: Author profiles, specializations, contact info, content filtering
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get author information
$author_id = get_query_var('author');
$author = get_userdata($author_id);

if (!$author) {
    get_template_part('template-hierarchy/404');
    return;
}

// Get author meta and custom fields
$author_bio = get_the_author_meta('description', $author_id);
$author_website = get_the_author_meta('user_url', $author_id);
$author_email = get_the_author_meta('user_email', $author_id);

// Custom author fields (these would typically be added by a plugin or theme options)
$author_title = get_the_author_meta('job_title', $author_id) ?: esc_html__('Recruitment Specialist', 'recruitpro');
$author_department = get_the_author_meta('department', $author_id);
$author_phone = get_the_author_meta('phone', $author_id);
$author_linkedin = get_the_author_meta('linkedin', $author_id);
$author_twitter = get_the_author_meta('twitter', $author_id);
$author_specializations = get_the_author_meta('specializations', $author_id);
$author_certifications = get_the_author_meta('certifications', $author_id);
$author_experience_years = get_the_author_meta('experience_years', $author_id);
$author_location = get_the_author_meta('location', $author_id);
$author_languages = get_the_author_meta('languages', $author_id);

// Get customizer settings
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_author_sidebar_position', 'right');
$posts_layout = get_theme_mod('recruitpro_author_layout', 'list');
$show_contact_info = get_theme_mod('recruitpro_author_show_contact', true);
$show_social_links = get_theme_mod('recruitpro_author_show_social', true);
$show_author_stats = get_theme_mod('recruitpro_author_show_stats', true);
$show_related_authors = get_theme_mod('recruitpro_author_show_related', true);

// Get author statistics
$total_posts = count_user_posts($author_id, 'post');
$first_post = get_posts(array(
    'author' => $author_id,
    'numberposts' => 1,
    'orderby' => 'date',
    'order' => 'ASC'
));
$member_since = $first_post ? get_the_date('F Y', $first_post[0]->ID) : get_the_date('F Y', '', $author->user_registered);

// Get posts pagination
global $wp_query;
$current_page = max(1, get_query_var('paged'));
$max_pages = $wp_query->max_num_pages;

// Schema.org markup for author
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $author->display_name,
    'description' => $author_bio ? wp_strip_all_tags($author_bio) : sprintf(__('Recruitment professional at %s specializing in talent acquisition and career development.', 'recruitpro'), $company_name),
    'url' => get_author_posts_url($author_id),
    'image' => get_avatar_url($author_id, array('size' => 300)),
    'jobTitle' => $author_title,
    'worksFor' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    )
);

// Add additional schema properties if available
if ($author_website) {
    $schema_data['sameAs'][] = $author_website;
}
if ($author_linkedin) {
    $schema_data['sameAs'][] = $author_linkedin;
}
if ($author_twitter) {
    $schema_data['sameAs'][] = $author_twitter;
}

// Default specializations for recruitment professionals
$default_specializations = array(
    'executive-search' => esc_html__('Executive Search', 'recruitpro'),
    'technical-recruitment' => esc_html__('Technical Recruitment', 'recruitpro'),
    'hr-consulting' => esc_html__('HR Consulting', 'recruitpro'),
    'talent-acquisition' => esc_html__('Talent Acquisition', 'recruitpro'),
    'career-coaching' => esc_html__('Career Coaching', 'recruitpro'),
    'industry-expertise' => esc_html__('Industry Expertise', 'recruitpro'),
    'candidate-assessment' => esc_html__('Candidate Assessment', 'recruitpro'),
    'employer-branding' => esc_html__('Employer Branding', 'recruitpro')
);

// Parse specializations
$author_spec_array = array();
if ($author_specializations) {
    $author_spec_array = is_array($author_specializations) ? $author_specializations : explode(',', $author_specializations);
} else {
    // Assign some default specializations
    $author_spec_array = array_slice(array_keys($default_specializations), 0, 3);
}

// Get related authors (same department or similar role)
$related_authors = array();
if ($show_related_authors) {
    $related_authors = get_users(array(
        'exclude' => array($author_id),
        'who' => 'authors',
        'has_published_posts' => array('post'),
        'number' => 4,
        'orderby' => 'post_count',
        'order' => 'DESC'
    ));
}
?>

<div id="primary" class="content-area author-page">
    <main id="main" class="site-main">
        
        <?php if ($show_breadcrumbs) : ?>
            <div class="breadcrumbs-wrapper">
                <div class="container">
                    <?php recruitpro_breadcrumbs(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            
            <!-- Author Profile Header -->
            <header class="author-header">
                <div class="author-profile-card">
                    <div class="row align-items-center">
                        
                        <!-- Author Avatar & Basic Info -->
                        <div class="col-lg-4">
                            <div class="author-avatar-section">
                                <div class="author-avatar">
                                    <?php echo get_avatar($author_id, 200, '', $author->display_name, array('class' => 'avatar-img')); ?>
                                    <?php if ($show_author_stats) : ?>
                                        <div class="author-badge">
                                            <i class="fas fa-certificate"></i>
                                            <span><?php esc_html_e('Team Member', 'recruitpro'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($show_social_links && ($author_linkedin || $author_twitter || $author_website)) : ?>
                                    <div class="author-social-links">
                                        <?php if ($author_linkedin) : ?>
                                            <a href="<?php echo esc_url($author_linkedin); ?>" target="_blank" rel="nofollow" class="social-link linkedin" title="<?php esc_attr_e('LinkedIn Profile', 'recruitpro'); ?>">
                                                <i class="fab fa-linkedin"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($author_twitter) : ?>
                                            <a href="<?php echo esc_url($author_twitter); ?>" target="_blank" rel="nofollow" class="social-link twitter" title="<?php esc_attr_e('Twitter Profile', 'recruitpro'); ?>">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($author_website) : ?>
                                            <a href="<?php echo esc_url($author_website); ?>" target="_blank" rel="nofollow" class="social-link website" title="<?php esc_attr_e('Personal Website', 'recruitpro'); ?>">
                                                <i class="fas fa-globe"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Author Details -->
                        <div class="col-lg-8">
                            <div class="author-details">
                                <h1 class="author-name"><?php echo esc_html($author->display_name); ?></h1>
                                
                                <div class="author-title">
                                    <?php echo esc_html($author_title); ?>
                                    <?php if ($author_department) : ?>
                                        <span class="department">, <?php echo esc_html($author_department); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($author_bio) : ?>
                                    <div class="author-bio">
                                        <?php echo wp_kses_post(wpautop($author_bio)); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Author Quick Stats -->
                                <?php if ($show_author_stats) : ?>
                                    <div class="author-quick-stats">
                                        <div class="stat-item">
                                            <span class="stat-icon"><i class="fas fa-edit"></i></span>
                                            <div class="stat-content">
                                                <span class="stat-number"><?php echo esc_html(number_format($total_posts)); ?></span>
                                                <span class="stat-label"><?php esc_html_e('Articles', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php if ($author_experience_years) : ?>
                                            <div class="stat-item">
                                                <span class="stat-icon"><i class="fas fa-clock"></i></span>
                                                <div class="stat-content">
                                                    <span class="stat-number"><?php echo esc_html($author_experience_years); ?>+</span>
                                                    <span class="stat-label"><?php esc_html_e('Years Experience', 'recruitpro'); ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="stat-item">
                                            <span class="stat-icon"><i class="fas fa-calendar"></i></span>
                                            <div class="stat-content">
                                                <span class="stat-number"><?php echo esc_html($member_since); ?></span>
                                                <span class="stat-label"><?php esc_html_e('Member Since', 'recruitpro'); ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php if ($author_location) : ?>
                                            <div class="stat-item">
                                                <span class="stat-icon"><i class="fas fa-map-marker-alt"></i></span>
                                                <div class="stat-content">
                                                    <span class="stat-number"><?php echo esc_html($author_location); ?></span>
                                                    <span class="stat-label"><?php esc_html_e('Location', 'recruitpro'); ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Contact Button -->
                                <?php if ($show_contact_info && ($author_email || $author_phone)) : ?>
                                    <div class="author-contact-actions">
                                        <button class="btn btn-primary btn-contact-author" data-author-id="<?php echo esc_attr($author_id); ?>">
                                            <i class="fas fa-envelope"></i>
                                            <?php esc_html_e('Contact Me', 'recruitpro'); ?>
                                        </button>
                                        
                                        <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="btn btn-secondary">
                                            <i class="fas fa-user"></i>
                                            <?php esc_html_e('View Profile', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </header>

            <!-- Author Expertise & Specializations -->
            <?php if (!empty($author_spec_array) || $author_certifications || $author_languages) : ?>
                <section class="author-expertise-section">
                    <div class="row">
                        
                        <!-- Specializations -->
                        <?php if (!empty($author_spec_array)) : ?>
                            <div class="col-lg-6">
                                <div class="expertise-card">
                                    <h2 class="expertise-title">
                                        <i class="fas fa-star"></i>
                                        <?php esc_html_e('Specializations', 'recruitpro'); ?>
                                    </h2>
                                    <div class="specializations-grid">
                                        <?php foreach ($author_spec_array as $spec) : ?>
                                            <div class="specialization-item">
                                                <i class="fas fa-check-circle"></i>
                                                <span><?php echo esc_html(isset($default_specializations[trim($spec)]) ? $default_specializations[trim($spec)] : trim($spec)); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Additional Credentials -->
                        <div class="col-lg-6">
                            <div class="credentials-card">
                                <h2 class="credentials-title">
                                    <i class="fas fa-award"></i>
                                    <?php esc_html_e('Professional Profile', 'recruitpro'); ?>
                                </h2>
                                
                                <?php if ($author_certifications) : ?>
                                    <div class="credential-item">
                                        <h3><?php esc_html_e('Certifications', 'recruitpro'); ?></h3>
                                        <p><?php echo esc_html($author_certifications); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($author_languages) : ?>
                                    <div class="credential-item">
                                        <h3><?php esc_html_e('Languages', 'recruitpro'); ?></h3>
                                        <p><?php echo esc_html($author_languages); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="credential-item">
                                    <h3><?php esc_html_e('Areas of Focus', 'recruitpro'); ?></h3>
                                    <p><?php esc_html_e('Talent acquisition, candidate assessment, employer branding, and strategic HR consulting with focus on building long-term partnerships.', 'recruitpro'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </section>
            <?php endif; ?>

            <!-- Author Content -->
            <div class="author-content">
                <div class="row">
                    
                    <!-- Main Content -->
                    <div class="<?php echo ($sidebar_position === 'left') ? 'col-lg-9 order-2' : (($sidebar_position === 'right') ? 'col-lg-9' : 'col-12'); ?>">
                        
                        <!-- Content Header -->
                        <div class="content-header">
                            <h2 class="content-title">
                                <?php printf(esc_html__('Articles by %s', 'recruitpro'), esc_html($author->display_name)); ?>
                                <span class="articles-count">(<?php echo esc_html(number_format($total_posts)); ?>)</span>
                            </h2>
                            
                            <?php if ($max_pages > 1) : ?>
                                <div class="content-meta">
                                    <?php 
                                    printf(
                                        esc_html__('Page %1$s of %2$s', 'recruitpro'),
                                        '<strong>' . number_format_i18n($current_page) . '</strong>',
                                        '<strong>' . number_format_i18n($max_pages) . '</strong>'
                                    );
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Posts List -->
                        <?php if (have_posts()) : ?>
                            
                            <div class="posts-container <?php echo esc_attr($posts_layout); ?>-layout" id="posts-container">
                                
                                <?php while (have_posts()) : the_post(); ?>
                                    
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item author-post-item'); ?>>
                                        
                                        <?php if (has_post_thumbnail()) : ?>
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                                    <?php 
                                                    $thumbnail_size = ($posts_layout === 'grid') ? 'medium' : 'medium_large';
                                                    the_post_thumbnail($thumbnail_size, array(
                                                        'alt' => the_title_attribute(array('echo' => false)),
                                                        'loading' => 'lazy'
                                                    )); 
                                                    ?>
                                                </a>
                                                
                                                <!-- Reading Time -->
                                                <div class="reading-time">
                                                    <i class="fas fa-clock"></i>
                                                    <?php echo recruitpro_reading_time(); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="post-content">
                                            
                                            <div class="post-meta">
                                                <span class="post-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                        <?php echo esc_html(get_the_date()); ?>
                                                    </time>
                                                </span>
                                                
                                                <?php if (has_category()) : ?>
                                                    <span class="post-categories">
                                                        <i class="fas fa-folder"></i>
                                                        <?php the_category(', '); ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (comments_open() || get_comments_number()) : ?>
                                                    <span class="post-comments">
                                                        <i class="fas fa-comments"></i>
                                                        <a href="<?php comments_link(); ?>">
                                                            <?php 
                                                            printf(
                                                                _n(
                                                                    '%s Comment',
                                                                    '%s Comments',
                                                                    get_comments_number(),
                                                                    'recruitpro'
                                                                ),
                                                                number_format_i18n(get_comments_number())
                                                            );
                                                            ?>
                                                        </a>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <header class="post-header">
                                                <h3 class="post-title">
                                                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h3>
                                            </header>
                                            
                                            <div class="post-excerpt">
                                                <?php 
                                                if (has_excerpt()) {
                                                    the_excerpt();
                                                } else {
                                                    echo wp_trim_words(get_the_content(), 25, '...');
                                                }
                                                ?>
                                            </div>
                                            
                                            <?php if (has_tag()) : ?>
                                                <div class="post-tags">
                                                    <i class="fas fa-tags"></i>
                                                    <?php the_tags('', ', ', ''); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="post-actions">
                                                <a href="<?php the_permalink(); ?>" class="read-more-link">
                                                    <?php esc_html_e('Read Article', 'recruitpro'); ?>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                            
                                        </div>
                                        
                                    </article>
                                    
                                <?php endwhile; ?>
                                
                            </div>
                            
                            <!-- Pagination -->
                            <div class="author-pagination">
                                <?php
                                the_posts_pagination(array(
                                    'mid_size' => 2,
                                    'prev_text' => '<i class="fas fa-chevron-left"></i> ' . esc_html__('Previous', 'recruitpro'),
                                    'next_text' => esc_html__('Next', 'recruitpro') . ' <i class="fas fa-chevron-right"></i>',
                                    'before_page_number' => '<span class="screen-reader-text">' . esc_html__('Page', 'recruitpro') . ' </span>',
                                ));
                                ?>
                            </div>
                            
                        <?php else : ?>
                            
                            <!-- No Posts Found -->
                            <div class="no-posts-found">
                                <div class="no-posts-content">
                                    <div class="no-posts-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <h3 class="no-posts-title"><?php esc_html_e('No Articles Yet', 'recruitpro'); ?></h3>
                                    <p class="no-posts-message">
                                        <?php printf(esc_html__('%s hasn\'t published any articles yet. Check back soon for insights and expertise from our recruitment team.', 'recruitpro'), esc_html($author->display_name)); ?>
                                    </p>
                                    
                                    <div class="no-posts-actions">
                                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="btn btn-primary">
                                            <i class="fas fa-arrow-left"></i>
                                            <?php esc_html_e('Browse All Articles', 'recruitpro'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(home_url('/team/')); ?>" class="btn btn-secondary">
                                            <i class="fas fa-users"></i>
                                            <?php esc_html_e('Meet Our Team', 'recruitpro'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>

                    <!-- Sidebar -->
                    <?php if ($sidebar_position !== 'none') : ?>
                        <aside class="<?php echo ($sidebar_position === 'left') ? 'col-lg-3 order-1' : 'col-lg-3'; ?> sidebar author-sidebar">
                            
                            <!-- Contact Widget -->
                            <?php if ($show_contact_info && ($author_email || $author_phone)) : ?>
                                <div class="widget contact-author-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Get In Touch', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <p><?php printf(esc_html__('Have questions about recruitment or career opportunities? Contact %s directly.', 'recruitpro'), esc_html($author->display_name)); ?></p>
                                        
                                        <div class="contact-methods">
                                            <?php if ($author_email) : ?>
                                                <div class="contact-method">
                                                    <i class="fas fa-envelope"></i>
                                                    <div class="contact-info">
                                                        <span class="contact-label"><?php esc_html_e('Email', 'recruitpro'); ?></span>
                                                        <a href="mailto:<?php echo esc_attr($author_email); ?>" class="contact-value">
                                                            <?php echo esc_html($author_email); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($author_phone) : ?>
                                                <div class="contact-method">
                                                    <i class="fas fa-phone"></i>
                                                    <div class="contact-info">
                                                        <span class="contact-label"><?php esc_html_e('Phone', 'recruitpro'); ?></span>
                                                        <a href="tel:<?php echo esc_attr($author_phone); ?>" class="contact-value">
                                                            <?php echo esc_html($author_phone); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <button class="btn btn-primary btn-block btn-contact-modal" data-author-id="<?php echo esc_attr($author_id); ?>">
                                            <i class="fas fa-paper-plane"></i>
                                            <?php esc_html_e('Send Message', 'recruitpro'); ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Author Categories -->
                            <div class="widget author-categories-widget">
                                <h3 class="widget-title"><?php esc_html_e('Article Topics', 'recruitpro'); ?></h3>
                                <div class="widget-content">
                                    <?php
                                    // Get categories from author's posts
                                    $author_categories = get_terms(array(
                                        'taxonomy' => 'category',
                                        'hide_empty' => true,
                                        'object_ids' => get_posts(array(
                                            'author' => $author_id,
                                            'post_status' => 'publish',
                                            'numberposts' => -1,
                                            'fields' => 'ids'
                                        ))
                                    ));
                                    
                                    if ($author_categories && !is_wp_error($author_categories)) :
                                    ?>
                                        <ul class="author-categories-list">
                                            <?php foreach ($author_categories as $category) : ?>
                                                <li class="category-item">
                                                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                                        <span class="category-name"><?php echo esc_html($category->name); ?></span>
                                                        <span class="category-count"><?php echo esc_html($category->count); ?></span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <p><?php esc_html_e('No categories found for this author.', 'recruitpro'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Related Authors -->
                            <?php if ($show_related_authors && !empty($related_authors)) : ?>
                                <div class="widget related-authors-widget">
                                    <h3 class="widget-title"><?php esc_html_e('Other Team Members', 'recruitpro'); ?></h3>
                                    <div class="widget-content">
                                        <div class="related-authors-list">
                                            <?php foreach ($related_authors as $related_author) : ?>
                                                <div class="related-author-item">
                                                    <div class="related-author-avatar">
                                                        <a href="<?php echo esc_url(get_author_posts_url($related_author->ID)); ?>">
                                                            <?php echo get_avatar($related_author->ID, 50); ?>
                                                        </a>
                                                    </div>
                                                    <div class="related-author-info">
                                                        <h4 class="related-author-name">
                                                            <a href="<?php echo esc_url(get_author_posts_url($related_author->ID)); ?>">
                                                                <?php echo esc_html($related_author->display_name); ?>
                                                            </a>
                                                        </h4>
                                                        <span class="related-author-title">
                                                            <?php echo esc_html(get_the_author_meta('job_title', $related_author->ID) ?: esc_html__('Recruitment Specialist', 'recruitpro')); ?>
                                                        </span>
                                                        <div class="related-author-stats">
                                                            <?php 
                                                            $related_posts_count = count_user_posts($related_author->ID, 'post');
                                                            printf(
                                                                _n(
                                                                    '%s article',
                                                                    '%s articles',
                                                                    $related_posts_count,
                                                                    'recruitpro'
                                                                ),
                                                                number_format_i18n($related_posts_count)
                                                            );
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Author Newsletter -->
                            <div class="widget author-newsletter-widget">
                                <h3 class="widget-title"><?php esc_html_e('Stay Updated', 'recruitpro'); ?></h3>
                                <div class="widget-content">
                                    <p><?php printf(esc_html__('Subscribe to get the latest insights and articles from %s and our recruitment team.', 'recruitpro'), esc_html($author->display_name)); ?></p>
                                    
                                    <form class="newsletter-form" method="post" action="">
                                        <div class="form-group">
                                            <input type="email" 
                                                   class="form-control" 
                                                   placeholder="<?php esc_attr_e('Your email address', 'recruitpro'); ?>" 
                                                   required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-bell"></i>
                                            <?php esc_html_e('Subscribe', 'recruitpro'); ?>
                                        </button>
                                    </form>
                                    
                                    <small class="newsletter-note">
                                        <?php esc_html_e('We respect your privacy. Unsubscribe at any time.', 'recruitpro'); ?>
                                    </small>
                                </div>
                            </div>

                            <?php if (is_active_sidebar('author-sidebar')) : ?>
                                <?php dynamic_sidebar('author-sidebar'); ?>
                            <?php endif; ?>
                            
                        </aside>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
        
    </main>
</div>

<!-- Contact Author Modal -->
<div class="modal fade" id="contactAuthorModal" tabindex="-1" role="dialog" aria-labelledby="contactAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactAuthorModalLabel">
                    <?php printf(esc_html__('Contact %s', 'recruitpro'), esc_html($author->display_name)); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_attr_e('Close', 'recruitpro'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="contactAuthorForm" class="contact-author-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact-name"><?php esc_html_e('Your Name', 'recruitpro'); ?> <span class="required">*</span></label>
                                <input type="text" class="form-control" id="contact-name" name="contact_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact-email"><?php esc_html_e('Your Email', 'recruitpro'); ?> <span class="required">*</span></label>
                                <input type="email" class="form-control" id="contact-email" name="contact_email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact-phone"><?php esc_html_e('Phone Number', 'recruitpro'); ?></label>
                                <input type="tel" class="form-control" id="contact-phone" name="contact_phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact-subject"><?php esc_html_e('Subject', 'recruitpro'); ?> <span class="required">*</span></label>
                                <select class="form-control" id="contact-subject" name="contact_subject" required>
                                    <option value=""><?php esc_html_e('Select a topic', 'recruitpro'); ?></option>
                                    <option value="job-inquiry"><?php esc_html_e('Job Opportunity Inquiry', 'recruitpro'); ?></option>
                                    <option value="career-advice"><?php esc_html_e('Career Advice', 'recruitpro'); ?></option>
                                    <option value="recruitment-services"><?php esc_html_e('Recruitment Services', 'recruitpro'); ?></option>
                                    <option value="general-inquiry"><?php esc_html_e('General Inquiry', 'recruitpro'); ?></option>
                                    <option value="other"><?php esc_html_e('Other', 'recruitpro'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact-message"><?php esc_html_e('Message', 'recruitpro'); ?> <span class="required">*</span></label>
                        <textarea class="form-control" id="contact-message" name="contact_message" rows="5" required placeholder="<?php esc_attr_e('Please describe how we can help you...', 'recruitpro'); ?>"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="contact-consent" name="contact_consent" value="1" required>
                            <label class="form-check-label" for="contact-consent">
                                <?php printf(esc_html__('I agree to the %s and %s and consent to being contacted about recruitment opportunities.', 'recruitpro'), 
                                    '<a href="#" target="_blank">Terms of Service</a>', 
                                    '<a href="#" target="_blank">Privacy Policy</a>'
                                ); ?> <span class="required">*</span>
                            </label>
                        </div>
                    </div>
                    
                    <input type="hidden" name="author_id" value="<?php echo esc_attr($author_id); ?>">
                    <input type="hidden" name="action" value="contact_author">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('contact_author_nonce'); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e('Cancel', 'recruitpro'); ?></button>
                <button type="submit" form="contactAuthorForm" class="btn btn-primary"><?php esc_html_e('Send Message', 'recruitpro'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<style>
/* Author Page Specific Styles */
.author-page {
    background: #f8f9fa;
    min-height: 80vh;
}

/* Author Profile Header */
.author-header {
    margin-bottom: 3rem;
}

.author-profile-card {
    background: white;
    border-radius: 12px;
    padding: 3rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
}

.author-profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.author-avatar-section {
    text-align: center;
}

.author-avatar {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.avatar-img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    object-fit: cover;
}

.author-badge {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    font-size: 1.2rem;
}

.author-social-links {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.social-link {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.social-link.linkedin {
    background: #0077b5;
}

.social-link.twitter {
    background: #1da1f2;
}

.social-link.website {
    background: #6c757d;
}

.social-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
    text-decoration: none;
}

.author-details {
    padding-left: 2rem;
}

.author-name {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.author-title {
    font-size: 1.3rem;
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.department {
    color: #6c757d;
    font-weight: 400;
}

.author-bio {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.author-quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.author-contact-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Author Expertise Section */
.author-expertise-section {
    margin-bottom: 3rem;
}

.expertise-card,
.credentials-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    height: 100%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.expertise-title,
.credentials-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.expertise-title i,
.credentials-title i {
    color: var(--primary-color);
}

.specializations-grid {
    display: grid;
    gap: 1rem;
}

.specialization-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid var(--primary-color);
}

.specialization-item i {
    color: var(--primary-color);
    font-size: 1.1rem;
}

.credential-item {
    margin-bottom: 1.5rem;
}

.credential-item h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.credential-item p {
    color: #6c757d;
    line-height: 1.6;
    margin: 0;
}

/* Content Section */
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e1e5e9;
}

.content-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.articles-count {
    color: #6c757d;
    font-weight: 400;
    font-size: 1rem;
}

.content-meta {
    color: #6c757d;
    font-size: 0.9rem;
}

/* Posts */
.posts-container.list-layout {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.posts-container.grid-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.author-post-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.author-post-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.list-layout .author-post-item {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.list-layout .post-thumbnail {
    flex-shrink: 0;
    width: 300px;
    position: relative;
}

.list-layout .post-content {
    flex: 1;
    padding: 2rem;
}

.grid-layout .post-content {
    padding: 1.5rem;
}

.post-thumbnail {
    position: relative;
    overflow: hidden;
}

.post-thumbnail img {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease;
}

.author-post-item:hover .post-thumbnail img {
    transform: scale(1.05);
}

.reading-time {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.post-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.post-meta a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-meta a:hover {
    color: var(--primary-color);
}

.post-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 1rem;
    line-height: 1.4;
}

.post-title a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: var(--primary-color);
}

.post-excerpt {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.post-tags {
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.post-tags a {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-tags a:hover {
    color: var(--primary-color);
}

.read-more-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.read-more-link:hover {
    color: var(--secondary-color);
    transform: translateX(5px);
    text-decoration: none;
}

/* No Posts Found */
.no-posts-found {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.no-posts-icon {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    color: #6c757d;
    font-size: 2rem;
}

.no-posts-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.no-posts-message {
    font-size: 1.1rem;
    color: #6c757d;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.no-posts-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Sidebar */
.author-sidebar .widget {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.author-sidebar .widget-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.contact-methods {
    margin: 1.5rem 0;
}

.contact-method {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.contact-method:last-child {
    border-bottom: none;
}

.contact-method i {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.contact-info {
    flex: 1;
}

.contact-label {
    display: block;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.contact-value {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.contact-value:hover {
    color: var(--primary-color);
    text-decoration: none;
}

.author-categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-item {
    border-bottom: 1px solid #f1f3f4;
    padding: 0.75rem 0;
}

.category-item:last-child {
    border-bottom: none;
}

.category-item a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #495057;
    text-decoration: none;
    transition: color 0.3s ease;
}

.category-item a:hover {
    color: var(--primary-color);
}

.category-count {
    background: #f8f9fa;
    color: #6c757d;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
}

.related-authors-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.related-author-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.related-author-item:hover {
    background: #e9ecef;
    transform: translateY(-1px);
}

.related-author-avatar {
    flex-shrink: 0;
}

.related-author-avatar img {
    border-radius: 50%;
}

.related-author-info {
    flex: 1;
}

.related-author-name {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.related-author-name a {
    color: #2c3e50;
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-author-name a:hover {
    color: var(--primary-color);
}

.related-author-title {
    display: block;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.related-author-stats {
    font-size: 0.8rem;
    color: #495057;
}

.newsletter-form {
    margin-bottom: 1rem;
}

.newsletter-note {
    color: #6c757d;
    font-size: 0.8rem;
    line-height: 1.4;
}

/* Contact Modal */
.contact-author-form .required {
    color: #dc3545;
}

.contact-author-form .form-group {
    margin-bottom: 1.5rem;
}

/* Pagination */
.author-pagination {
    margin: 3rem 0;
    text-align: center;
}

.author-pagination .nav-links {
    display: inline-flex;
    gap: 0.5rem;
    align-items: center;
}

.author-pagination .page-numbers {
    padding: 0.75rem 1rem;
    background: white;
    border: 1px solid #e1e5e9;
    color: #495057;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.author-pagination .page-numbers:hover,
.author-pagination .page-numbers.current {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .author-profile-card {
        padding: 2rem;
    }
    
    .author-details {
        padding-left: 0;
        margin-top: 2rem;
        text-align: center;
    }
    
    .author-name {
        font-size: 2rem;
    }
    
    .author-quick-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .author-contact-actions {
        justify-content: center;
    }
    
    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .posts-container.list-layout .author-post-item {
        flex-direction: column;
        gap: 0;
    }
    
    .list-layout .post-thumbnail {
        width: 100%;
    }
    
    .list-layout .post-content {
        padding: 1.5rem;
    }
    
    .posts-container.grid-layout {
        grid-template-columns: 1fr;
    }
    
    .no-posts-actions {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 576px) {
    .author-profile-card {
        padding: 1.5rem;
    }
    
    .avatar-img {
        width: 150px;
        height: 150px;
    }
    
    .author-name {
        font-size: 1.5rem;
    }
    
    .author-title {
        font-size: 1.1rem;
    }
    
    .author-quick-stats {
        grid-template-columns: 1fr;
    }
    
    .expertise-card,
    .credentials-card {
        padding: 1.5rem;
    }
    
    .post-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .contact-method {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .author-post-item,
    .social-link,
    .read-more-link {
        transition: none;
    }
    
    .author-post-item:hover,
    .social-link:hover {
        transform: none;
    }
    
    .read-more-link:hover {
        transform: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .author-profile-card,
    .expertise-card,
    .credentials-card,
    .author-post-item,
    .author-sidebar .widget {
        border: 2px solid #000;
    }
}

/* Print styles */
@media print {
    .author-sidebar,
    .author-contact-actions,
    .social-link,
    .author-pagination {
        display: none;
    }
    
    .author-profile-card,
    .expertise-card,
    .credentials-card,
    .author-post-item {
        box-shadow: none;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }
    
    .posts-container.list-layout .author-post-item {
        break-inside: avoid;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Contact author modal functionality
    const contactBtns = document.querySelectorAll('.btn-contact-author, .btn-contact-modal');
    const contactModal = document.getElementById('contactAuthorModal');
    const contactForm = document.getElementById('contactAuthorForm');
    
    contactBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (contactModal) {
                $(contactModal).modal('show');
            }
        });
    });
    
    // Form submission
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"], .modal-footer button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Sending...';
            submitButton.disabled = true;

            // Submit form via AJAX
            fetch(recruitpro_theme.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your message has been sent successfully! <?php echo esc_js($author->display_name); ?> will get back to you soon.');
                    $(contactModal).modal('hide');
                    contactForm.reset();
                } else {
                    alert('There was an error sending your message. Please try again or contact us directly.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error sending your message. Please try again.');
            })
            .finally(() => {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        });
    }
    
    // Newsletter subscription
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (email) {
                // Simulate subscription process
                alert('Thank you for subscribing! You\'ll receive updates from our recruitment team.');
                emailInput.value = '';
            }
        });
    });
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Track author page interactions
    if (typeof gtag !== 'undefined') {
        // Track author profile views
        gtag('event', 'author_profile_view', {
            'author_id': '<?php echo esc_js($author_id); ?>',
            'author_name': '<?php echo esc_js($author->display_name); ?>'
        });
        
        // Track contact button clicks
        contactBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                gtag('event', 'author_contact_initiated', {
                    'author_id': '<?php echo esc_js($author_id); ?>',
                    'author_name': '<?php echo esc_js($author->display_name); ?>'
                });
            });
        });
        
        // Track social link clicks
        const socialLinks = document.querySelectorAll('.social-link');
        socialLinks.forEach(link => {
            link.addEventListener('click', function() {
                const platform = this.classList.contains('linkedin') ? 'linkedin' : 
                               this.classList.contains('twitter') ? 'twitter' : 'website';
                
                gtag('event', 'author_social_click', {
                    'author_id': '<?php echo esc_js($author_id); ?>',
                    'platform': platform
                });
            });
        });
        
        // Track article clicks
        const articleLinks = document.querySelectorAll('.read-more-link, .post-title a');
        articleLinks.forEach(link => {
            link.addEventListener('click', function() {
                const postTitle = this.closest('.author-post-item').querySelector('.post-title a').textContent.trim();
                
                gtag('event', 'author_article_click', {
                    'author_id': '<?php echo esc_js($author_id); ?>',
                    'article_title': postTitle
                });
            });
        });
    }
    
    // Lazy loading for author avatars
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Enhanced accessibility
    const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
    
    // Keyboard navigation for modals
    if (contactModal) {
        contactModal.addEventListener('shown.bs.modal', function() {
            const firstInput = this.querySelector('input, textarea, select');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        contactModal.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).modal('hide');
            }
        });
    }
    
    // Reading time calculation for posts
    function calculateReadingTime() {
        const excerpts = document.querySelectorAll('.post-excerpt');
        excerpts.forEach(excerpt => {
            const text = excerpt.textContent;
            const wordCount = text.split(/\s+/).length;
            const readingTime = Math.ceil(wordCount / 200); // Average reading speed
            
            const readingTimeElement = excerpt.closest('.author-post-item').querySelector('.reading-time');
            if (readingTimeElement && !readingTimeElement.textContent.includes('min')) {
                readingTimeElement.innerHTML = `<i class="fas fa-clock"></i> ${readingTime} min read`;
            }
        });
    }
    
    calculateReadingTime();
    
});

// Utility function for reading time calculation
function recruitpro_reading_time() {
    // This would typically be calculated server-side
    return '<?php echo esc_html__("3 min read", "recruitpro"); ?>';
}
</script>

<?php get_footer(); ?>