<?php
/**
 * Template Name: About Us Page
 *
 * Professional about page template for recruitment agencies and HR consultancies.
 * This template provides a comprehensive layout for showcasing company information,
 * team members, values, achievements, and building trust with both candidates and clients.
 * Optimized for conversion and professional credibility in the recruitment industry.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-about.php
 * Purpose: Professional about page for recruitment agencies
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Team showcase, company values, testimonials, call-to-action
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_about_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_about_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$company_description = get_theme_mod('recruitpro_company_description', get_bloginfo('description'));
$founded_year = get_theme_mod('recruitpro_company_founded', '');
$company_size = get_theme_mod('recruitpro_company_size', '');
$headquarters = get_theme_mod('recruitpro_company_address', '');

// Schema.org markup data
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $company_name,
    'description' => $company_description,
    'url' => home_url(),
    'logo' => get_theme_mod('recruitpro_site_logo', ''),
    'foundingDate' => $founded_year,
    'numberOfEmployees' => $company_size,
    'address' => array(
        '@type' => 'PostalAddress',
        'streetAddress' => $headquarters
    ),
    'sameAs' => array_filter(array(
        get_theme_mod('recruitpro_social_linkedin', ''),
        get_theme_mod('recruitpro_social_facebook', ''),
        get_theme_mod('recruitpro_social_twitter', ''),
    ))
);
?>

<!-- Schema.org Organization Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main id="primary" class="site-main about-page" role="main">
    
    <?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <?php recruitpro_breadcrumbs(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="about-page-wrapper <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <div class="about-main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                
                    <!-- Page Header Section -->
                    <header class="about-page-header" itemscope itemtype="https://schema.org/Organization">
                        
                        <?php if ($show_page_title) : ?>
                            <div class="page-title-section">
                                <h1 class="page-title" itemprop="name"><?php the_title(); ?></h1>
                                
                                <?php if (has_excerpt()) : ?>
                                    <div class="page-subtitle" itemprop="description">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php elseif ($company_description) : ?>
                                    <div class="page-subtitle" itemprop="description">
                                        <?php echo esc_html($company_description); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Company Quick Facts -->
                        <?php if ($founded_year || $company_size || $headquarters) : ?>
                            <div class="company-quick-facts">
                                <div class="quick-facts-grid">
                                    
                                    <?php if ($founded_year) : ?>
                                        <div class="quick-fact-item">
                                            <div class="fact-icon">
                                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                            </div>
                                            <div class="fact-content">
                                                <div class="fact-label"><?php esc_html_e('Founded', 'recruitpro'); ?></div>
                                                <div class="fact-value" itemprop="foundingDate"><?php echo esc_html($founded_year); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($company_size) : ?>
                                        <div class="quick-fact-item">
                                            <div class="fact-icon">
                                                <i class="fas fa-users" aria-hidden="true"></i>
                                            </div>
                                            <div class="fact-content">
                                                <div class="fact-label"><?php esc_html_e('Team Size', 'recruitpro'); ?></div>
                                                <div class="fact-value" itemprop="numberOfEmployees"><?php echo esc_html($company_size); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($headquarters) : ?>
                                        <div class="quick-fact-item">
                                            <div class="fact-icon">
                                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                            </div>
                                            <div class="fact-content">
                                                <div class="fact-label"><?php esc_html_e('Headquarters', 'recruitpro'); ?></div>
                                                <div class="fact-value" itemprop="address"><?php echo esc_html($headquarters); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Success Rate (if available) -->
                                    <?php
                                    $success_rate = get_theme_mod('recruitpro_success_rate', '');
                                    if ($success_rate) :
                                    ?>
                                        <div class="quick-fact-item">
                                            <div class="fact-icon">
                                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                                            </div>
                                            <div class="fact-content">
                                                <div class="fact-label"><?php esc_html_e('Success Rate', 'recruitpro'); ?></div>
                                                <div class="fact-value"><?php echo esc_html($success_rate); ?>%</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endif; ?>

                    </header>

                    <!-- Main Content Area -->
                    <div class="about-page-content">
                        
                        <!-- Page Content -->
                        <div class="page-content-section">
                            <div class="entry-content" itemprop="description">
                                <?php
                                the_content();
                                
                                wp_link_pages(array(
                                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'recruitpro'),
                                    'after'  => '</div>',
                                ));
                                ?>
                            </div>
                        </div>

                        <!-- Company Mission, Vision & Values -->
                        <?php
                        $mission = get_post_meta($page_id, '_company_mission', true);
                        $vision = get_post_meta($page_id, '_company_vision', true);
                        $values = get_post_meta($page_id, '_company_values', true);
                        
                        if ($mission || $vision || $values) :
                        ?>
                            <section class="company-philosophy" id="philosophy">
                                <div class="philosophy-container">
                                    
                                    <div class="section-header">
                                        <h2 class="section-title"><?php esc_html_e('Our Philosophy', 'recruitpro'); ?></h2>
                                        <p class="section-subtitle"><?php esc_html_e('The principles that guide our recruitment excellence', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="philosophy-grid">
                                        
                                        <?php if ($mission) : ?>
                                            <div class="philosophy-item mission">
                                                <div class="philosophy-icon">
                                                    <i class="fas fa-bullseye" aria-hidden="true"></i>
                                                </div>
                                                <h3 class="philosophy-title"><?php esc_html_e('Our Mission', 'recruitpro'); ?></h3>
                                                <div class="philosophy-content">
                                                    <?php echo wp_kses_post($mission); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($vision) : ?>
                                            <div class="philosophy-item vision">
                                                <div class="philosophy-icon">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </div>
                                                <h3 class="philosophy-title"><?php esc_html_e('Our Vision', 'recruitpro'); ?></h3>
                                                <div class="philosophy-content">
                                                    <?php echo wp_kses_post($vision); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($values) : ?>
                                            <div class="philosophy-item values">
                                                <div class="philosophy-icon">
                                                    <i class="fas fa-heart" aria-hidden="true"></i>
                                                </div>
                                                <h3 class="philosophy-title"><?php esc_html_e('Our Values', 'recruitpro'); ?></h3>
                                                <div class="philosophy-content">
                                                    <?php echo wp_kses_post($values); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Company Statistics -->
                        <?php if (is_active_sidebar('homepage-stats')) : ?>
                            <section class="about-statistics" id="statistics">
                                <div class="statistics-container">
                                    <div class="section-header">
                                        <h2 class="section-title"><?php esc_html_e('Our Track Record', 'recruitpro'); ?></h2>
                                        <p class="section-subtitle"><?php esc_html_e('Numbers that speak to our recruitment success', 'recruitpro'); ?></p>
                                    </div>
                                    <div class="statistics-content">
                                        <?php dynamic_sidebar('homepage-stats'); ?>
                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Team Section -->
                        <?php
                        $show_team_section = get_post_meta($page_id, '_show_team_section', true);
                        if ($show_team_section !== 'no') :
                        ?>
                            <section class="about-team" id="team">
                                <div class="team-container">
                                    
                                    <div class="section-header">
                                        <h2 class="section-title"><?php esc_html_e('Meet Our Team', 'recruitpro'); ?></h2>
                                        <p class="section-subtitle"><?php esc_html_e('Experienced professionals dedicated to your success', 'recruitpro'); ?></p>
                                    </div>

                                    <?php
                                    // Query team members (if custom post type exists)
                                    $team_args = array(
                                        'post_type' => 'team_member',
                                        'posts_per_page' => 8,
                                        'post_status' => 'publish',
                                        'meta_key' => '_team_order',
                                        'orderby' => 'meta_value_num',
                                        'order' => 'ASC'
                                    );

                                    $team_query = new WP_Query($team_args);

                                    if ($team_query->have_posts()) :
                                    ?>
                                        <div class="team-grid">
                                            <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
                                                <div class="team-member-card" itemscope itemtype="https://schema.org/Person">
                                                    
                                                    <?php if (has_post_thumbnail()) : ?>
                                                        <div class="member-photo">
                                                            <?php the_post_thumbnail('recruitpro-team', array(
                                                                'alt' => get_the_title(),
                                                                'itemprop' => 'image'
                                                            )); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="member-info">
                                                        <h3 class="member-name" itemprop="name"><?php the_title(); ?></h3>
                                                        
                                                        <?php
                                                        $position = get_post_meta(get_the_ID(), '_team_position', true);
                                                        if ($position) :
                                                        ?>
                                                            <div class="member-position" itemprop="jobTitle"><?php echo esc_html($position); ?></div>
                                                        <?php endif; ?>

                                                        <?php if (has_excerpt()) : ?>
                                                            <div class="member-bio" itemprop="description"><?php the_excerpt(); ?></div>
                                                        <?php endif; ?>

                                                        <!-- Team member social links -->
                                                        <?php
                                                        $linkedin = get_post_meta(get_the_ID(), '_team_linkedin', true);
                                                        $email = get_post_meta(get_the_ID(), '_team_email', true);
                                                        
                                                        if ($linkedin || $email) :
                                                        ?>
                                                            <div class="member-social">
                                                                <?php if ($linkedin) : ?>
                                                                    <a href="<?php echo esc_url($linkedin); ?>" class="social-link linkedin" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(sprintf(__('%s on LinkedIn', 'recruitpro'), get_the_title())); ?>">
                                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($email) : ?>
                                                                    <a href="mailto:<?php echo esc_attr($email); ?>" class="social-link email" aria-label="<?php echo esc_attr(sprintf(__('Email %s', 'recruitpro'), get_the_title())); ?>">
                                                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>

                                        <!-- View All Team Link -->
                                        <?php if ($team_query->found_posts > 8) : ?>
                                            <div class="team-cta">
                                                <a href="<?php echo esc_url(get_post_type_archive_link('team_member')); ?>" class="btn btn-outline">
                                                    <?php esc_html_e('Meet Our Full Team', 'recruitpro'); ?>
                                                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                    <?php
                                    wp_reset_postdata();
                                    else :
                                    ?>
                                        <!-- Fallback team content -->
                                        <div class="team-fallback">
                                            <p><?php esc_html_e('Our experienced team of recruitment professionals is here to help you find the perfect career opportunity or the ideal candidate for your organization.', 'recruitpro'); ?></p>
                                            
                                            <?php if (current_user_can('edit_posts')) : ?>
                                                <p><em><?php esc_html_e('Add team members to showcase your recruitment experts.', 'recruitpro'); ?></em></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Company Achievements & Certifications -->
                        <?php
                        $achievements = get_post_meta($page_id, '_company_achievements', true);
                        $certifications = get_post_meta($page_id, '_company_certifications', true);
                        
                        if ($achievements || $certifications) :
                        ?>
                            <section class="about-achievements" id="achievements">
                                <div class="achievements-container">
                                    
                                    <div class="section-header">
                                        <h2 class="section-title"><?php esc_html_e('Recognition & Certifications', 'recruitpro'); ?></h2>
                                        <p class="section-subtitle"><?php esc_html_e('Industry recognition and professional credentials', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="achievements-grid">
                                        
                                        <?php if ($achievements) : ?>
                                            <div class="achievements-section">
                                                <h3 class="subsection-title"><?php esc_html_e('Awards & Recognition', 'recruitpro'); ?></h3>
                                                <div class="achievements-content">
                                                    <?php echo wp_kses_post($achievements); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($certifications) : ?>
                                            <div class="certifications-section">
                                                <h3 class="subsection-title"><?php esc_html_e('Professional Certifications', 'recruitpro'); ?></h3>
                                                <div class="certifications-content">
                                                    <?php echo wp_kses_post($certifications); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Client Testimonials -->
                        <?php
                        $testimonials_args = array(
                            'post_type' => 'testimonial',
                            'posts_per_page' => 3,
                            'post_status' => 'publish',
                            'meta_key' => '_testimonial_featured',
                            'meta_value' => 'yes'
                        );

                        $testimonials_query = new WP_Query($testimonials_args);

                        if ($testimonials_query->have_posts()) :
                        ?>
                            <section class="about-testimonials" id="testimonials">
                                <div class="testimonials-container">
                                    
                                    <div class="section-header">
                                        <h2 class="section-title"><?php esc_html_e('What Our Clients Say', 'recruitpro'); ?></h2>
                                        <p class="section-subtitle"><?php esc_html_e('Trusted by leading companies and satisfied candidates', 'recruitpro'); ?></p>
                                    </div>

                                    <div class="testimonials-slider">
                                        <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post(); ?>
                                            <div class="testimonial-item" itemscope itemtype="https://schema.org/Review">
                                                
                                                <div class="testimonial-content">
                                                    <div class="testimonial-text" itemprop="reviewBody">
                                                        <?php the_content(); ?>
                                                    </div>
                                                    
                                                    <div class="testimonial-rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                                        <?php
                                                        $rating = get_post_meta(get_the_ID(), '_testimonial_rating', true);
                                                        if ($rating) :
                                                            for ($i = 1; $i <= 5; $i++) :
                                                                echo '<i class="' . ($i <= $rating ? 'fas' : 'far') . ' fa-star" aria-hidden="true"></i>';
                                                            endfor;
                                                        ?>
                                                            <meta itemprop="ratingValue" content="<?php echo esc_attr($rating); ?>">
                                                            <meta itemprop="bestRating" content="5">
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="testimonial-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                                                    <div class="author-name" itemprop="name"><?php the_title(); ?></div>
                                                    <?php
                                                    $company = get_post_meta(get_the_ID(), '_testimonial_company', true);
                                                    $position = get_post_meta(get_the_ID(), '_testimonial_position', true);
                                                    
                                                    if ($position || $company) :
                                                    ?>
                                                        <div class="author-details">
                                                            <?php if ($position) : ?>
                                                                <span class="author-position" itemprop="jobTitle"><?php echo esc_html($position); ?></span>
                                                            <?php endif; ?>
                                                            <?php if ($company) : ?>
                                                                <span class="author-company" itemprop="worksFor"><?php echo esc_html($company); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <!-- View All Testimonials Link -->
                                    <div class="testimonials-cta">
                                        <a href="<?php echo esc_url(get_post_type_archive_link('testimonial')); ?>" class="btn btn-outline">
                                            <?php esc_html_e('Read More Reviews', 'recruitpro'); ?>
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>

                                </div>
                            </section>
                        <?php
                        wp_reset_postdata();
                        endif;
                        ?>

                        <!-- Call to Action Section -->
                        <section class="about-cta" id="cta">
                            <div class="cta-container">
                                <div class="cta-content">
                                    
                                    <h2 class="cta-title">
                                        <?php 
                                        echo esc_html(get_theme_mod('recruitpro_about_cta_title', __('Ready to Start Your Journey?', 'recruitpro')));
                                        ?>
                                    </h2>
                                    
                                    <p class="cta-description">
                                        <?php 
                                        echo esc_html(get_theme_mod('recruitpro_about_cta_description', __('Let our experienced team help you find the perfect career opportunity or ideal candidate.', 'recruitpro')));
                                        ?>
                                    </p>

                                    <div class="cta-buttons">
                                        
                                        <a href="<?php echo esc_url(get_theme_mod('recruitpro_about_cta_primary_url', '/jobs')); ?>" class="btn btn-primary">
                                            <?php echo esc_html(get_theme_mod('recruitpro_about_cta_primary_text', __('Browse Jobs', 'recruitpro'))); ?>
                                            <i class="fas fa-briefcase" aria-hidden="true"></i>
                                        </a>

                                        <a href="<?php echo esc_url(get_theme_mod('recruitpro_about_cta_secondary_url', '/contact')); ?>" class="btn btn-outline">
                                            <?php echo esc_html(get_theme_mod('recruitpro_about_cta_secondary_text', __('Contact Us', 'recruitpro'))); ?>
                                            <i class="fas fa-envelope" aria-hidden="true"></i>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>

                <?php endwhile; // End of the loop. ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('about-sidebar')) : ?>
                <aside id="secondary" class="widget-area about-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('about-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();

/* =================================================================
   ABOUT PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

PROFESSIONAL ABOUT PAGE FEATURES:

✅ SCHEMA.ORG MARKUP
- Complete Organization schema
- Person schema for team members
- Review schema for testimonials
- SEO-optimized structured data

✅ COMPANY INFORMATION
- Quick facts (founded, size, location)
- Mission, vision, and values
- Achievements and certifications
- Professional credibility building

✅ TEAM SHOWCASE
- Team member cards with photos
- Position and bio information
- Social media links (LinkedIn, email)
- Schema markup for each person

✅ TRUST BUILDING ELEMENTS
- Client testimonials with ratings
- Company statistics and achievements
- Professional certifications display
- Industry recognition showcase

✅ CONVERSION OPTIMIZATION
- Strategic call-to-action placement
- Multiple CTA options (jobs, contact)
- Clear value propositions
- Professional credibility focus

✅ CUSTOMIZATION OPTIONS
- Sidebar position control
- Breadcrumb navigation toggle
- Custom field integration
- Theme customizer integration

✅ RESPONSIVE DESIGN
- Mobile-first approach
- Flexible grid layouts
- Touch-friendly interactions
- Optimized for all devices

✅ ACCESSIBILITY
- ARIA labels and roles
- Semantic HTML structure
- Keyboard navigation support
- Screen reader optimization

✅ PERFORMANCE
- Conditional content loading
- Optimized image sizes
- Efficient database queries
- Lazy loading ready

✅ INTEGRATION
- Widget area support
- Custom post type integration
- Theme customizer settings
- Plugin compatibility ready

PERFECT FOR:
- Recruitment agencies
- HR consultancies
- Executive search firms
- Staffing companies
- Professional services

*/
?>