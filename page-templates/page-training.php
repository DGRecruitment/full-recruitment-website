<?php
/**
 * Template Name: Training Page
 *
 * Professional training and development page template for recruitment agencies
 * showcasing training programs, workshops, certification courses, and professional
 * development opportunities for both candidates and corporate clients. Features
 * comprehensive training catalog, registration systems, and skills development tracking.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-training.php
 * Purpose: Training programs and professional development showcase
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Training catalog, course registration, progress tracking, certificates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_training_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_training_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$training_layout = get_theme_mod('recruitpro_training_layout', 'grid');
$show_categories = get_theme_mod('recruitpro_training_show_categories', true);
$show_pricing = get_theme_mod('recruitpro_training_show_pricing', true);
$show_duration = get_theme_mod('recruitpro_training_show_duration', true);
$show_certification = get_theme_mod('recruitpro_training_show_certification', true);
$courses_per_page = get_theme_mod('recruitpro_training_per_page', 9);
$enable_filtering = get_theme_mod('recruitpro_training_enable_filtering', true);
$show_instructor_info = get_theme_mod('recruitpro_training_show_instructors', true);
$show_testimonials = get_theme_mod('recruitpro_training_show_testimonials', true);

// Training categories for recruitment industry
$training_categories = array(
    'career-development' => array(
        'name' => esc_html__('Career Development', 'recruitpro'),
        'description' => esc_html__('Professional growth and career advancement programs', 'recruitpro'),
        'icon' => 'fas fa-chart-line',
        'target_audience' => 'candidates'
    ),
    'interview-skills' => array(
        'name' => esc_html__('Interview Skills', 'recruitpro'),
        'description' => esc_html__('Interview preparation and performance enhancement', 'recruitpro'),
        'icon' => 'fas fa-comments',
        'target_audience' => 'candidates'
    ),
    'resume-writing' => array(
        'name' => esc_html__('Resume Writing', 'recruitpro'),
        'description' => esc_html__('Professional resume and CV optimization', 'recruitpro'),
        'icon' => 'fas fa-file-alt',
        'target_audience' => 'candidates'
    ),
    'leadership-training' => array(
        'name' => esc_html__('Leadership Training', 'recruitpro'),
        'description' => esc_html__('Management and leadership development programs', 'recruitpro'),
        'icon' => 'fas fa-users-cog',
        'target_audience' => 'both'
    ),
    'technical-skills' => array(
        'name' => esc_html__('Technical Skills', 'recruitpro'),
        'description' => esc_html__('Industry-specific technical competencies', 'recruitpro'),
        'icon' => 'fas fa-laptop-code',
        'target_audience' => 'candidates'
    ),
    'hr-development' => array(
        'name' => esc_html__('HR Development', 'recruitpro'),
        'description' => esc_html__('Human resources and talent management training', 'recruitpro'),
        'icon' => 'fas fa-user-tie',
        'target_audience' => 'corporate'
    ),
    'soft-skills' => array(
        'name' => esc_html__('Soft Skills', 'recruitpro'),
        'description' => esc_html__('Communication, teamwork, and interpersonal skills', 'recruitpro'),
        'icon' => 'fas fa-handshake',
        'target_audience' => 'both'
    ),
    'certification-prep' => array(
        'name' => esc_html__('Certification Preparation', 'recruitpro'),
        'description' => esc_html__('Professional certification and exam preparation', 'recruitpro'),
        'icon' => 'fas fa-certificate',
        'target_audience' => 'candidates'
    )
);

// Training formats and delivery methods
$training_formats = array(
    'online' => array(
        'name' => esc_html__('Online Training', 'recruitpro'),
        'description' => esc_html__('Self-paced online learning modules', 'recruitpro'),
        'icon' => 'fas fa-desktop'
    ),
    'webinar' => array(
        'name' => esc_html__('Live Webinars', 'recruitpro'),
        'description' => esc_html__('Interactive live online sessions', 'recruitpro'),
        'icon' => 'fas fa-video'
    ),
    'workshop' => array(
        'name' => esc_html__('Workshops', 'recruitpro'),
        'description' => esc_html__('Hands-on practical workshops', 'recruitpro'),
        'icon' => 'fas fa-tools'
    ),
    'one-on-one' => array(
        'name' => esc_html__('One-on-One Coaching', 'recruitpro'),
        'description' => esc_html__('Personalized individual training sessions', 'recruitpro'),
        'icon' => 'fas fa-user-graduate'
    ),
    'corporate' => array(
        'name' => esc_html__('Corporate Training', 'recruitpro'),
        'description' => esc_html__('Customized company-wide training programs', 'recruitpro'),
        'icon' => 'fas fa-building'
    )
);

// Schema.org markup for training page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => get_the_content() ? wp_strip_all_tags(get_the_content()) : sprintf(__('Professional training and development programs offered by %s. Career advancement courses, skills development, and certification preparation for job seekers and corporate clients.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    ),
    'about' => array(
        '@type' => 'Thing',
        'name' => 'Professional Training Programs',
        'description' => 'Career development and skills training for recruitment industry'
    ),
    'offers' => array(
        '@type' => 'AggregateOffer',
        'name' => 'Training Programs',
        'description' => 'Professional development and skills training courses'
    )
);

// Sample training courses data (this would typically come from custom post type or plugin)
$featured_courses = array(
    array(
        'title' => esc_html__('Professional Interview Mastery', 'recruitpro'),
        'description' => esc_html__('Comprehensive interview preparation and performance enhancement program covering behavioral questions, technical assessments, and confidence building.', 'recruitpro'),
        'category' => 'interview-skills',
        'format' => 'online',
        'duration' => '6 weeks',
        'price' => '$299',
        'certification' => true,
        'level' => 'beginner',
        'rating' => 4.8,
        'students' => 1247,
        'instructor' => 'Sarah Johnson, Career Coach'
    ),
    array(
        'title' => esc_html__('Executive Leadership Development', 'recruitpro'),
        'description' => esc_html__('Advanced leadership training for senior professionals and executives focusing on strategic thinking, team management, and organizational leadership.', 'recruitpro'),
        'category' => 'leadership-training',
        'format' => 'workshop',
        'duration' => '3 months',
        'price' => '$1,899',
        'certification' => true,
        'level' => 'advanced',
        'rating' => 4.9,
        'students' => 356,
        'instructor' => 'Michael Chen, Executive Coach'
    ),
    array(
        'title' => esc_html__('Digital Marketing Essentials', 'recruitpro'),
        'description' => esc_html__('Complete digital marketing course covering SEO, social media, content marketing, and analytics for career advancement.', 'recruitpro'),
        'category' => 'technical-skills',
        'format' => 'webinar',
        'duration' => '8 weeks',
        'price' => '$549',
        'certification' => true,
        'level' => 'intermediate',
        'rating' => 4.7,
        'students' => 892,
        'instructor' => 'Emma Rodriguez, Digital Strategist'
    ),
    array(
        'title' => esc_html__('Resume Writing Excellence', 'recruitpro'),
        'description' => esc_html__('Professional resume and cover letter writing workshop with ATS optimization and personal branding strategies.', 'recruitpro'),
        'category' => 'resume-writing',
        'format' => 'one-on-one',
        'duration' => '2 weeks',
        'price' => '$199',
        'certification' => false,
        'level' => 'beginner',
        'rating' => 4.6,
        'students' => 2156,
        'instructor' => 'David Thompson, Career Writer'
    ),
    array(
        'title' => esc_html__('HR Analytics and Data-Driven Decisions', 'recruitpro'),
        'description' => esc_html__('Advanced HR analytics training for human resources professionals covering workforce analytics, predictive modeling, and data visualization.', 'recruitpro'),
        'category' => 'hr-development',
        'format' => 'corporate',
        'duration' => '5 days',
        'price' => 'Custom Pricing',
        'certification' => true,
        'level' => 'advanced',
        'rating' => 4.8,
        'students' => 178,
        'instructor' => 'Dr. Lisa Park, HR Analytics Expert'
    ),
    array(
        'title' => esc_html__('Effective Communication in the Workplace', 'recruitpro'),
        'description' => esc_html__('Essential soft skills training focusing on professional communication, conflict resolution, and collaborative teamwork.', 'recruitpro'),
        'category' => 'soft-skills',
        'format' => 'online',
        'duration' => '4 weeks',
        'price' => '$179',
        'certification' => false,
        'level' => 'beginner',
        'rating' => 4.5,
        'students' => 3421,
        'instructor' => 'Jennifer Martinez, Communications Specialist'
    )
);

// Training statistics
$total_courses = count($featured_courses);
$total_students = array_sum(array_column($featured_courses, 'students'));
$avg_rating = round(array_sum(array_column($featured_courses, 'rating')) / count($featured_courses), 1);
$certified_courses = count(array_filter($featured_courses, function($course) { return $course['certification']; }));
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main training-page">
        
        <?php if ($show_breadcrumbs) : ?>
            <div class="breadcrumbs-wrapper">
                <div class="container">
                    <?php recruitpro_breadcrumbs(); ?>
                </div>
            </div>
        <?php endif; ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('training-content'); ?>>
            
            <!-- Page Header -->
            <header class="page-header training-header">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <?php if ($show_page_title) : ?>
                                <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php endif; ?>
                            
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('Advance your career with professional training programs from %s. We offer comprehensive courses designed to enhance your skills, boost your confidence, and accelerate your professional growth in today\'s competitive job market.', 'recruitpro'), esc_html($company_name)); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="training-stats">
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html($total_courses); ?>+</span>
                                    <span class="stat-label"><?php esc_html_e('Training Courses', 'recruitpro'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html(number_format($total_students)); ?>+</span>
                                    <span class="stat-label"><?php esc_html_e('Students Trained', 'recruitpro'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html($avg_rating); ?>/5</span>
                                    <span class="stat-label"><?php esc_html_e('Average Rating', 'recruitpro'); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number"><?php echo esc_html($certified_courses); ?></span>
                                    <span class="stat-label"><?php esc_html_e('Certified Programs', 'recruitpro'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="training-main-content">
                <div class="container">
                    <div class="row">
                        
                        <!-- Main Content -->
                        <div class="<?php echo ($sidebar_position === 'left') ? 'col-lg-9 order-2' : (($sidebar_position === 'right') ? 'col-lg-9' : 'col-12'); ?>">
                            
                            <!-- Training Categories -->
                            <?php if ($show_categories && $training_categories) : ?>
                                <section class="training-categories-section">
                                    <h2 class="section-title"><?php esc_html_e('Training Categories', 'recruitpro'); ?></h2>
                                    <div class="training-categories-grid">
                                        <?php foreach ($training_categories as $cat_key => $category) : ?>
                                            <div class="category-card" data-category="<?php echo esc_attr($cat_key); ?>">
                                                <div class="category-icon">
                                                    <i class="<?php echo esc_attr($category['icon']); ?>"></i>
                                                </div>
                                                <h3 class="category-name"><?php echo esc_html($category['name']); ?></h3>
                                                <p class="category-description"><?php echo esc_html($category['description']); ?></p>
                                                <span class="category-audience"><?php 
                                                    switch($category['target_audience']) {
                                                        case 'candidates':
                                                            esc_html_e('For Job Seekers', 'recruitpro');
                                                            break;
                                                        case 'corporate':
                                                            esc_html_e('For Employers', 'recruitpro');
                                                            break;
                                                        case 'both':
                                                            esc_html_e('For Everyone', 'recruitpro');
                                                            break;
                                                    }
                                                ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <!-- Training Formats -->
                            <section class="training-formats-section">
                                <h2 class="section-title"><?php esc_html_e('Training Formats', 'recruitpro'); ?></h2>
                                <div class="training-formats-grid">
                                    <?php foreach ($training_formats as $format_key => $format) : ?>
                                        <div class="format-card">
                                            <div class="format-icon">
                                                <i class="<?php echo esc_attr($format['icon']); ?>"></i>
                                            </div>
                                            <h3 class="format-name"><?php echo esc_html($format['name']); ?></h3>
                                            <p class="format-description"><?php echo esc_html($format['description']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>

                            <!-- Course Filtering -->
                            <?php if ($enable_filtering) : ?>
                                <section class="course-filtering-section">
                                    <div class="filter-controls">
                                        <div class="filter-group">
                                            <label for="category-filter"><?php esc_html_e('Category:', 'recruitpro'); ?></label>
                                            <select id="category-filter" class="form-control">
                                                <option value=""><?php esc_html_e('All Categories', 'recruitpro'); ?></option>
                                                <?php foreach ($training_categories as $cat_key => $category) : ?>
                                                    <option value="<?php echo esc_attr($cat_key); ?>"><?php echo esc_html($category['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="filter-group">
                                            <label for="format-filter"><?php esc_html_e('Format:', 'recruitpro'); ?></label>
                                            <select id="format-filter" class="form-control">
                                                <option value=""><?php esc_html_e('All Formats', 'recruitpro'); ?></option>
                                                <?php foreach ($training_formats as $format_key => $format) : ?>
                                                    <option value="<?php echo esc_attr($format_key); ?>"><?php echo esc_html($format['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="filter-group">
                                            <label for="level-filter"><?php esc_html_e('Level:', 'recruitpro'); ?></label>
                                            <select id="level-filter" class="form-control">
                                                <option value=""><?php esc_html_e('All Levels', 'recruitpro'); ?></option>
                                                <option value="beginner"><?php esc_html_e('Beginner', 'recruitpro'); ?></option>
                                                <option value="intermediate"><?php esc_html_e('Intermediate', 'recruitpro'); ?></option>
                                                <option value="advanced"><?php esc_html_e('Advanced', 'recruitpro'); ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="filter-group">
                                            <label>
                                                <input type="checkbox" id="certification-filter" value="1">
                                                <?php esc_html_e('Certification Available', 'recruitpro'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                            <?php endif; ?>

            <!-- Featured Courses -->
            <section class="featured-courses-section">
                <h2 class="section-title"><?php esc_html_e('Featured Training Courses', 'recruitpro'); ?></h2>
                <div class="courses-grid <?php echo esc_attr($training_layout); ?>">
                    <?php foreach ($featured_courses as $course) : ?>
                        <div class="course-card" data-category="<?php echo esc_attr($course['category']); ?>" data-format="<?php echo esc_attr($course['format']); ?>" data-level="<?php echo esc_attr($course['level']); ?>" data-certification="<?php echo $course['certification'] ? '1' : '0'; ?>">
                            <div class="course-header">
                                <div class="course-category">
                                    <i class="<?php echo esc_attr($training_categories[$course['category']]['icon']); ?>"></i>
                                    <?php echo esc_html($training_categories[$course['category']]['name']); ?>
                                </div>
                                <?php if ($course['certification']) : ?>
                                    <div class="certification-badge">
                                        <i class="fas fa-certificate"></i>
                                        <?php esc_html_e('Certified', 'recruitpro'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="course-content">
                                <h3 class="course-title"><?php echo esc_html($course['title']); ?></h3>
                                <p class="course-description"><?php echo esc_html($course['description']); ?></p>
                                
                                <div class="course-meta">
                                    <div class="course-rating">
                                        <div class="stars">
                                            <?php 
                                            $rating = $course['rating'];
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= floor($rating)) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } elseif ($i <= ceil($rating)) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="rating-text"><?php echo esc_html($rating); ?> (<?php echo esc_html(number_format($course['students'])); ?> <?php esc_html_e('students', 'recruitpro'); ?>)</span>
                                    </div>
                                    
                                    <div class="course-details">
                                        <div class="detail-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo esc_html($course['duration']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-desktop"></i>
                                            <span><?php echo esc_html($training_formats[$course['format']]['name']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-signal"></i>
                                            <span><?php echo esc_html(ucfirst($course['level'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($show_instructor_info) : ?>
                                        <div class="course-instructor">
                                            <i class="fas fa-user-tie"></i>
                                            <span><?php echo esc_html($course['instructor']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="course-footer">
                                <div class="course-price">
                                    <?php if ($show_pricing) : ?>
                                        <span class="price"><?php echo esc_html($course['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="course-actions">
                                    <button class="btn btn-primary btn-enroll" data-course="<?php echo esc_attr($course['title']); ?>">
                                        <?php esc_html_e('Enroll Now', 'recruitpro'); ?>
                                    </button>
                                    <button class="btn btn-secondary btn-learn-more" data-course="<?php echo esc_attr($course['title']); ?>">
                                        <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Corporate Training Section -->
            <section class="corporate-training-section">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="section-title"><?php esc_html_e('Corporate Training Solutions', 'recruitpro'); ?></h2>
                        <p class="section-description">
                            <?php printf(esc_html__('Transform your organization with customized training programs designed specifically for your team. %s offers comprehensive corporate training solutions that align with your business objectives and enhance your workforce capabilities.', 'recruitpro'), esc_html($company_name)); ?>
                        </p>
                        <ul class="corporate-benefits">
                            <li><i class="fas fa-check-circle"></i> <?php esc_html_e('Customized curriculum development', 'recruitpro'); ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php esc_html_e('On-site and virtual delivery options', 'recruitpro'); ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php esc_html_e('Progress tracking and analytics', 'recruitpro'); ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php esc_html_e('Professional certification programs', 'recruitpro'); ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php esc_html_e('Flexible scheduling and formats', 'recruitpro'); ?></li>
                            <li><i class="fas fa-check-circle"></i> <?php esc_html_e('Ongoing support and consultation', 'recruitpro'); ?></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <div class="corporate-cta">
                            <h3><?php esc_html_e('Ready to Get Started?', 'recruitpro'); ?></h3>
                            <p><?php esc_html_e('Contact us for a customized training proposal.', 'recruitpro'); ?></p>
                            <button class="btn btn-primary btn-lg btn-corporate-inquiry">
                                <?php esc_html_e('Request Corporate Quote', 'recruitpro'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Training Benefits -->
            <section class="training-benefits-section">
                <h2 class="section-title"><?php esc_html_e('Why Choose Our Training Programs?', 'recruitpro'); ?></h2>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="benefit-title"><?php esc_html_e('Expert Instructors', 'recruitpro'); ?></h3>
                        <p class="benefit-description"><?php esc_html_e('Learn from industry professionals with proven track records and extensive experience in their respective fields.', 'recruitpro'); ?></p>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h3 class="benefit-title"><?php esc_html_e('Professional Certification', 'recruitpro'); ?></h3>
                        <p class="benefit-description"><?php esc_html_e('Earn recognized certifications that validate your skills and enhance your professional credibility.', 'recruitpro'); ?></p>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="benefit-title"><?php esc_html_e('Networking Opportunities', 'recruitpro'); ?></h3>
                        <p class="benefit-description"><?php esc_html_e('Connect with like-minded professionals and expand your professional network through our training programs.', 'recruitpro'); ?></p>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="benefit-title"><?php esc_html_e('Career Advancement', 'recruitpro'); ?></h3>
                        <p class="benefit-description"><?php esc_html_e('Develop the skills and knowledge needed to advance your career and achieve your professional goals.', 'recruitpro'); ?></p>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="benefit-title"><?php esc_html_e('Flexible Learning', 'recruitpro'); ?></h3>
                        <p class="benefit-description"><?php esc_html_e('Choose from various formats and schedules that fit your lifestyle and learning preferences.', 'recruitpro'); ?></p>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="benefit-title"><?php esc_html_e('Ongoing Support', 'recruitpro'); ?></h3>
                        <p class="benefit-description"><?php esc_html_e('Receive continuous support and guidance throughout your learning journey and beyond.', 'recruitpro'); ?></p>
                    </div>
                </div>
            </section>

            <!-- Success Stories -->
            <?php if ($show_testimonials) : ?>
                <section class="training-testimonials-section">
                    <h2 class="section-title"><?php esc_html_e('Success Stories', 'recruitpro'); ?></h2>
                    <div class="testimonials-grid">
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"The interview skills training completely transformed my confidence. I went from struggling with interviews to receiving multiple job offers. The personalized feedback and practical exercises made all the difference."</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-info">
                                    <h4>Maria Rodriguez</h4>
                                    <span>Marketing Manager</span>
                                </div>
                                <div class="testimonial-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"Our team's leadership skills improved dramatically after the corporate training program. The ROI was evident within months as productivity and employee satisfaction increased significantly."</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-info">
                                    <h4>James Wilson</h4>
                                    <span>HR Director, TechCorp</span>
                                </div>
                                <div class="testimonial-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <p>"The digital marketing course was exactly what I needed to pivot my career. The practical projects and industry insights helped me land my dream job in just two months after completion."</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-info">
                                    <h4>Alexandra Chen</h4>
                                    <span>Digital Marketing Specialist</span>
                                </div>
                                <div class="testimonial-rating">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        </div>

        <!-- Sidebar -->
        <?php if ($sidebar_position !== 'none') : ?>
            <aside class="<?php echo ($sidebar_position === 'left') ? 'col-lg-3 order-1' : 'col-lg-3'; ?> sidebar training-sidebar">
                
                <!-- Quick Enrollment -->
                <div class="widget quick-enrollment-widget">
                    <h3 class="widget-title"><?php esc_html_e('Quick Enrollment', 'recruitpro'); ?></h3>
                    <div class="widget-content">
                        <p><?php esc_html_e('Ready to start your professional development journey?', 'recruitpro'); ?></p>
                        <button class="btn btn-primary btn-block btn-quick-enroll">
                            <?php esc_html_e('Browse All Courses', 'recruitpro'); ?>
                        </button>
                        <button class="btn btn-secondary btn-block btn-consultation">
                            <?php esc_html_e('Schedule Consultation', 'recruitpro'); ?>
                        </button>
                    </div>
                </div>

                <!-- Training Calendar -->
                <div class="widget training-calendar-widget">
                    <h3 class="widget-title"><?php esc_html_e('Upcoming Sessions', 'recruitpro'); ?></h3>
                    <div class="widget-content">
                        <div class="calendar-event">
                            <div class="event-date">
                                <span class="day">15</span>
                                <span class="month"><?php esc_html_e('Jul', 'recruitpro'); ?></span>
                            </div>
                            <div class="event-info">
                                <h4><?php esc_html_e('Interview Skills Workshop', 'recruitpro'); ?></h4>
                                <span class="event-time">2:00 PM - 4:00 PM</span>
                            </div>
                        </div>
                        
                        <div class="calendar-event">
                            <div class="event-date">
                                <span class="day">22</span>
                                <span class="month"><?php esc_html_e('Jul', 'recruitpro'); ?></span>
                            </div>
                            <div class="event-info">
                                <h4><?php esc_html_e('Leadership Webinar', 'recruitpro'); ?></h4>
                                <span class="event-time">10:00 AM - 12:00 PM</span>
                            </div>
                        </div>
                        
                        <div class="calendar-event">
                            <div class="event-date">
                                <span class="day">29</span>
                                <span class="month"><?php esc_html_e('Jul', 'recruitpro'); ?></span>
                            </div>
                            <div class="event-info">
                                <h4><?php esc_html_e('Resume Writing Clinic', 'recruitpro'); ?></h4>
                                <span class="event-time">1:00 PM - 3:00 PM</span>
                            </div>
                        </div>
                        
                        <a href="#" class="view-all-events"><?php esc_html_e('View All Events', 'recruitpro'); ?></a>
                    </div>
                </div>

                <!-- Popular Courses -->
                <div class="widget popular-courses-widget">
                    <h3 class="widget-title"><?php esc_html_e('Popular Courses', 'recruitpro'); ?></h3>
                    <div class="widget-content">
                        <?php 
                        $popular_courses = array_slice($featured_courses, 0, 3);
                        foreach ($popular_courses as $course) : 
                        ?>
                            <div class="popular-course-item">
                                <h4 class="course-title"><?php echo esc_html($course['title']); ?></h4>
                                <div class="course-meta">
                                    <span class="course-price"><?php echo esc_html($course['price']); ?></span>
                                    <span class="course-rating">
                                        <i class="fas fa-star"></i>
                                        <?php echo esc_html($course['rating']); ?>
                                    </span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <?php esc_html_e('Learn More', 'recruitpro'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="widget training-contact-widget">
                    <h3 class="widget-title"><?php esc_html_e('Need Help?', 'recruitpro'); ?></h3>
                    <div class="widget-content">
                        <p><?php esc_html_e('Our training specialists are here to help you choose the right program.', 'recruitpro'); ?></p>
                        
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo esc_html(get_theme_mod('recruitpro_phone', '+1 (555) 123-4567')); ?></span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo esc_html(get_theme_mod('recruitpro_email', 'training@recruitpro.com')); ?></span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <span><?php esc_html_e('Mon-Fri: 9AM-6PM', 'recruitpro'); ?></span>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary btn-block btn-contact-training">
                            <?php esc_html_e('Contact Training Team', 'recruitpro'); ?>
                        </button>
                    </div>
                </div>

                <?php if (is_active_sidebar('training-sidebar')) : ?>
                    <?php dynamic_sidebar('training-sidebar'); ?>
                <?php endif; ?>
                
            </aside>
        <?php endif; ?>

    </div>
</div>
</div>

<!-- Training Enrollment Modal -->
<div class="modal fade" id="enrollmentModal" tabindex="-1" role="dialog" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollmentModalLabel"><?php esc_html_e('Course Enrollment', 'recruitpro'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_attr_e('Close', 'recruitpro'); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="enrollmentForm" class="training-enrollment-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="enroll-first-name"><?php esc_html_e('First Name', 'recruitpro'); ?> <span class="required">*</span></label>
                                <input type="text" class="form-control" id="enroll-first-name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="enroll-last-name"><?php esc_html_e('Last Name', 'recruitpro'); ?> <span class="required">*</span></label>
                                <input type="text" class="form-control" id="enroll-last-name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="enroll-email"><?php esc_html_e('Email Address', 'recruitpro'); ?> <span class="required">*</span></label>
                                <input type="email" class="form-control" id="enroll-email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="enroll-phone"><?php esc_html_e('Phone Number', 'recruitpro'); ?></label>
                                <input type="tel" class="form-control" id="enroll-phone" name="phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enroll-course"><?php esc_html_e('Course', 'recruitpro'); ?> <span class="required">*</span></label>
                        <input type="text" class="form-control" id="enroll-course" name="course" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="enroll-experience"><?php esc_html_e('Current Experience Level', 'recruitpro'); ?></label>
                        <select class="form-control" id="enroll-experience" name="experience_level">
                            <option value=""><?php esc_html_e('Select Experience Level', 'recruitpro'); ?></option>
                            <option value="beginner"><?php esc_html_e('Beginner', 'recruitpro'); ?></option>
                            <option value="intermediate"><?php esc_html_e('Intermediate', 'recruitpro'); ?></option>
                            <option value="advanced"><?php esc_html_e('Advanced', 'recruitpro'); ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="enroll-goals"><?php esc_html_e('Learning Goals', 'recruitpro'); ?></label>
                        <textarea class="form-control" id="enroll-goals" name="learning_goals" rows="3" placeholder="<?php esc_attr_e('What do you hope to achieve with this training?', 'recruitpro'); ?>"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="enroll-company"><?php esc_html_e('Company/Organization', 'recruitpro'); ?></label>
                        <input type="text" class="form-control" id="enroll-company" name="company">
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enroll-newsletter" name="newsletter" value="1">
                            <label class="form-check-label" for="enroll-newsletter">
                                <?php esc_html_e('Subscribe to our training newsletter for updates and special offers', 'recruitpro'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enroll-terms" name="terms" value="1" required>
                            <label class="form-check-label" for="enroll-terms">
                                <?php printf(esc_html__('I agree to the %s and %s', 'recruitpro'), 
                                    '<a href="#" target="_blank">Terms of Service</a>', 
                                    '<a href="#" target="_blank">Privacy Policy</a>'
                                ); ?> <span class="required">*</span>
                            </label>
                        </div>
                    </div>
                    
                    <input type="hidden" name="action" value="submit_training_enrollment">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('training_enrollment_nonce'); ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e('Cancel', 'recruitpro'); ?></button>
                <button type="submit" form="enrollmentForm" class="btn btn-primary"><?php esc_html_e('Submit Enrollment', 'recruitpro'); ?></button>
            </div>
        </div>
    </div>
</div>

</article>
</main>
</div>

<!-- Schema.org JSON-LD -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<style>
/* Training Page Specific Styles */
.training-page .training-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    margin-bottom: 3rem;
}

.training-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
    background: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.training-categories-grid,
.training-formats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.category-card,
.format-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    border: 1px solid #e1e5e9;
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover,
.format-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-color: var(--primary-color);
}

.category-icon,
.format-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.category-audience {
    display: inline-block;
    background: var(--secondary-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    margin-top: 1rem;
}

.filter-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: end;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.course-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e1e5e9;
    transition: all 0.3s ease;
}

.course-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.course-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e1e5e9;
}

.course-category {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--primary-color);
    font-weight: 600;
}

.certification-badge {
    background: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.course-content {
    padding: 1.5rem;
}

.course-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.course-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.course-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.stars {
    color: #ffc107;
}

.rating-text {
    font-size: 0.9rem;
    color: #6c757d;
}

.course-details {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.course-instructor {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #495057;
    margin-top: 1rem;
}

.course-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e1e5e9;
}

.course-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.course-actions {
    display: flex;
    gap: 0.5rem;
}

.corporate-training-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    margin: 4rem 0;
    border-radius: 12px;
}

.corporate-training-section .container {
    position: relative;
    z-index: 2;
}

.corporate-benefits {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
}

.corporate-benefits li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.corporate-benefits .fas {
    color: #28a745;
    font-size: 1.2rem;
}

.corporate-cta {
    background: rgba(255, 255, 255, 0.1);
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.benefit-item {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e1e5e9;
    transition: all 0.3s ease;
}

.benefit-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.benefit-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-size: 2rem;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.testimonial-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.testimonial-content p {
    font-style: italic;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    color: #495057;
}

.testimonial-author {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.author-info h4 {
    margin: 0;
    color: #2c3e50;
}

.author-info span {
    color: #6c757d;
    font-size: 0.9rem;
}

.training-sidebar .widget {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e1e5e9;
}

.training-sidebar .widget-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.calendar-event {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e1e5e9;
}

.calendar-event:last-child {
    border-bottom: none;
}

.event-date {
    flex-shrink: 0;
    text-align: center;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem;
    border-radius: 8px;
    min-width: 60px;
}

.event-date .day {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
}

.event-date .month {
    display: block;
    font-size: 0.8rem;
    text-transform: uppercase;
}

.event-info h4 {
    font-size: 0.95rem;
    margin: 0 0 0.25rem 0;
    color: #2c3e50;
}

.event-time {
    font-size: 0.85rem;
    color: #6c757d;
}

.popular-course-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e1e5e9;
}

.popular-course-item:last-child {
    border-bottom: none;
}

.popular-course-item .course-title {
    font-size: 0.95rem;
    margin: 0 0 0.5rem 0;
    color: #2c3e50;
}

.popular-course-item .course-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.popular-course-item .course-price {
    font-weight: 600;
    color: var(--primary-color);
}

.popular-course-item .course-rating {
    color: #ffc107;
    font-size: 0.9rem;
}

.contact-info {
    margin: 1rem 0;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}

.contact-item i {
    color: var(--primary-color);
    width: 16px;
    text-align: center;
}

/* Modal Styles */
.training-enrollment-form .required {
    color: #dc3545;
}

.training-enrollment-form .form-group {
    margin-bottom: 1.5rem;
}

.training-enrollment-form .form-check {
    margin-top: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .training-stats {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .courses-grid {
        grid-template-columns: 1fr;
    }
    
    .course-footer {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .course-actions {
        justify-content: center;
        width: 100%;
    }
    
    .corporate-training-section .row {
        flex-direction: column;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
    }
    
    .testimonials-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .training-header {
        padding: 2rem 0;
    }
    
    .category-card,
    .format-card {
        padding: 1.5rem;
    }
    
    .course-card {
        margin-bottom: 1rem;
    }
    
    .detail-item {
        flex-basis: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Course filtering functionality
    const categoryFilter = document.getElementById('category-filter');
    const formatFilter = document.getElementById('format-filter');
    const levelFilter = document.getElementById('level-filter');
    const certificationFilter = document.getElementById('certification-filter');
    const courseCards = document.querySelectorAll('.course-card');

    function filterCourses() {
        const categoryValue = categoryFilter?.value || '';
        const formatValue = formatFilter?.value || '';
        const levelValue = levelFilter?.value || '';
        const certificationValue = certificationFilter?.checked ? '1' : '';

        courseCards.forEach(card => {
            const cardCategory = card.dataset.category || '';
            const cardFormat = card.dataset.format || '';
            const cardLevel = card.dataset.level || '';
            const cardCertification = card.dataset.certification || '';

            const categoryMatch = !categoryValue || cardCategory === categoryValue;
            const formatMatch = !formatValue || cardFormat === formatValue;
            const levelMatch = !levelValue || cardLevel === levelValue;
            const certificationMatch = !certificationValue || cardCertification === certificationValue;

            if (categoryMatch && formatMatch && levelMatch && certificationMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add event listeners for filters
    if (categoryFilter) categoryFilter.addEventListener('change', filterCourses);
    if (formatFilter) formatFilter.addEventListener('change', filterCourses);
    if (levelFilter) levelFilter.addEventListener('change', filterCourses);
    if (certificationFilter) certificationFilter.addEventListener('change', filterCourses);

    // Course enrollment modal
    const enrollButtons = document.querySelectorAll('.btn-enroll');
    const enrollmentModal = document.getElementById('enrollmentModal');
    const enrollmentForm = document.getElementById('enrollmentForm');
    const courseInput = document.getElementById('enroll-course');

    enrollButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const courseName = this.dataset.course;
            if (courseInput) {
                courseInput.value = courseName;
            }
            if (enrollmentModal) {
                $(enrollmentModal).modal('show');
            }
        });
    });

    // Form submission
    if (enrollmentForm) {
        enrollmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Submitting...';
            submitButton.disabled = true;

            // Submit form via AJAX
            fetch(recruitpro_theme.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your enrollment request! We will contact you soon with course details.');
                    $(enrollmentModal).modal('hide');
                    enrollmentForm.reset();
                } else {
                    alert('There was an error submitting your enrollment. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your enrollment. Please try again.');
            })
            .finally(() => {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        });
    }

    // Category card clicks
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const category = this.dataset.category;
            if (categoryFilter) {
                categoryFilter.value = category;
                filterCourses();
                
                // Scroll to courses section
                const coursesSection = document.querySelector('.featured-courses-section');
                if (coursesSection) {
                    coursesSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });

    // Learn more button functionality
    const learnMoreButtons = document.querySelectorAll('.btn-learn-more');
    learnMoreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const courseName = this.dataset.course;
            // This could open a detailed course modal or navigate to a course page
            alert(`More information about "${courseName}" will be displayed here.`);
        });
    });

    // Quick action buttons
    const quickEnrollBtn = document.querySelector('.btn-quick-enroll');
    const consultationBtn = document.querySelector('.btn-consultation');
    const corporateInquiryBtn = document.querySelector('.btn-corporate-inquiry');
    const contactTrainingBtn = document.querySelector('.btn-contact-training');

    if (quickEnrollBtn) {
        quickEnrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const coursesSection = document.querySelector('.featured-courses-section');
            if (coursesSection) {
                coursesSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    if (consultationBtn) {
        consultationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Open consultation booking modal or redirect to contact page
            window.location.href = '/contact';
        });
    }

    if (corporateInquiryBtn) {
        corporateInquiryBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Open corporate training inquiry form
            alert('Corporate training inquiry form will be displayed here.');
        });
    }

    if (contactTrainingBtn) {
        contactTrainingBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Open contact modal or redirect to contact page
            window.location.href = '/contact';
        });
    }
});
</script>

<?php get_footer(); ?>