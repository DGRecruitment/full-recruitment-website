<?php
/**
 * Template Name: Office Locations Page
 *
 * Professional office locations page template for recruitment agencies showcasing
 * all business locations, regional offices, and service areas. Features detailed
 * location information, interactive maps, local team contacts, and regional
 * specializations to help candidates and clients connect with the nearest office.
 *
 * @package RecruitPro
 * @subpackage Templates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * Template: page-templates/page-locations.php
 * Purpose: Office locations showcase for recruitment agency
 * Dependencies: WordPress core, theme functions, Schema.org markup
 * Features: Multiple offices, maps, contact details, service areas, local teams
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get page data and customizer settings
$page_id = get_the_ID();
$show_breadcrumbs = get_theme_mod('recruitpro_breadcrumbs_enable', true);
$sidebar_position = get_theme_mod('recruitpro_locations_sidebar_position', 'right');
$show_page_title = get_theme_mod('recruitpro_locations_show_title', true);
$company_name = get_theme_mod('recruitpro_company_name', get_bloginfo('name'));
$show_overview_map = get_theme_mod('recruitpro_locations_show_overview_map', true);
$show_service_areas = get_theme_mod('recruitpro_locations_show_service_areas', true);
$show_local_teams = get_theme_mod('recruitpro_locations_show_teams', true);
$show_directions = get_theme_mod('recruitpro_locations_show_directions', true);
$show_office_hours = get_theme_mod('recruitpro_locations_show_hours', true);
$locations_layout = get_theme_mod('recruitpro_locations_layout', 'grid');

// Office locations data (can be managed through customizer or custom fields)
$office_locations = get_theme_mod('recruitpro_office_locations', array());

// Default locations if none configured
if (empty($office_locations)) {
    $office_locations = array(
        array(
            'id' => 'headquarters',
            'name' => esc_html__('London Headquarters', 'recruitpro'),
            'type' => 'headquarters',
            'address_line_1' => '123 Business Tower',
            'address_line_2' => 'Canary Wharf',
            'city' => 'London',
            'state' => 'England',
            'postal_code' => 'E14 5AB',
            'country' => 'United Kingdom',
            'phone' => '+44 20 7946 0958',
            'email' => 'london@recruitpro.com',
            'secondary_phone' => '', // Optional: emergency/after-hours number
            'latitude' => '51.5045',
            'longitude' => '-0.0195',
            'hours' => array(
                'monday' => '9:00 AM - 6:00 PM',
                'tuesday' => '9:00 AM - 6:00 PM',
                'wednesday' => '9:00 AM - 6:00 PM',
                'thursday' => '9:00 AM - 6:00 PM',
                'friday' => '9:00 AM - 5:00 PM',
                'saturday' => 'Closed',
                'sunday' => 'Closed'
            ),
            'specializations' => array('Executive Search', 'Financial Services', 'Technology', 'Legal'),
            'team_size' => '25',
            'established_year' => '2010',
            'languages' => array('English', 'French', 'German'),
            'parking' => true,
            'public_transport' => 'Canary Wharf Underground Station (2 min walk)',
            'google_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2482.6199!2d-0.0195!3d51.5045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTHCsDMwJzE2LjIiTiAwwrAwMScxMC4yIlc!5e0!3m2!1sen!2suk!4v1234567890',
            'office_image' => '',
            'description' => esc_html__('Our flagship headquarters in London\'s financial district, serving clients across the UK and Europe with comprehensive recruitment solutions.', 'recruitpro')
        ),
        array(
            'id' => 'manchester',
            'name' => esc_html__('Manchester Regional Office', 'recruitpro'),
            'type' => 'regional',
            'address_line_1' => '456 Northern Quarter',
            'address_line_2' => 'Deansgate',
            'city' => 'Manchester',
            'state' => 'England',
            'postal_code' => 'M1 1AA',
            'country' => 'United Kingdom',
            'phone' => '+44 161 123 4567',
            'email' => 'manchester@recruitpro.com',
            'secondary_phone' => '', // Optional: emergency/after-hours number
            'latitude' => '53.4808',
            'longitude' => '-2.2426',
            'hours' => array(
                'monday' => '9:00 AM - 5:30 PM',
                'tuesday' => '9:00 AM - 5:30 PM',
                'wednesday' => '9:00 AM - 5:30 PM',
                'thursday' => '9:00 AM - 5:30 PM',
                'friday' => '9:00 AM - 5:00 PM',
                'saturday' => 'Closed',
                'sunday' => 'Closed'
            ),
            'specializations' => array('Manufacturing', 'Healthcare', 'Education', 'Engineering'),
            'team_size' => '12',
            'established_year' => '2015',
            'languages' => array('English'),
            'parking' => true,
            'public_transport' => 'Deansgate Station (5 min walk)',
            'google_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2374.1234!2d-2.2426!3d53.4808!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTPCsDI4JzUxLjAiTiAywrAxNCczMy40Ilc!5e0!3m2!1sen!2suk!4v1234567891',
            'office_image' => '',
            'description' => esc_html__('Serving the North West region with specialized recruitment services for manufacturing, healthcare, and education sectors.', 'recruitpro')
        ),
        array(
            'id' => 'birmingham',
            'name' => esc_html__('Birmingham Office', 'recruitpro'),
            'type' => 'branch',
            'address_line_1' => '789 City Centre',
            'address_line_2' => 'Bull Ring',
            'city' => 'Birmingham',
            'state' => 'England',
            'postal_code' => 'B1 1AA',
            'country' => 'United Kingdom',
            'phone' => '+44 121 234 5678',
            'email' => 'birmingham@recruitpro.com',
            'secondary_phone' => '', // Optional: emergency/after-hours number
            'latitude' => '52.4862',
            'longitude' => '-1.8904',
            'hours' => array(
                'monday' => '9:00 AM - 5:30 PM',
                'tuesday' => '9:00 AM - 5:30 PM',
                'wednesday' => '9:00 AM - 5:30 PM',
                'thursday' => '9:00 AM - 5:30 PM',
                'friday' => '9:00 AM - 5:00 PM',
                'saturday' => 'Closed',
                'sunday' => 'Closed'
            ),
            'specializations' => array('Automotive', 'Retail', 'Logistics', 'Customer Service'),
            'team_size' => '8',
            'established_year' => '2018',
            'languages' => array('English'),
            'parking' => false,
            'public_transport' => 'Birmingham New Street (3 min walk)',
            'google_maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2428.5678!2d-1.8904!3d52.4862!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTLCsDI5JzEwLjMiTiAxwrA1MyczOC41Ilc!5e0!3m2!1sen!2suk!4v1234567892',
            'office_image' => '',
            'description' => esc_html__('Strategic Midlands location serving automotive, retail, and logistics recruitment across the central UK region.', 'recruitpro')
        )
    );
}

// Schema.org markup for locations page
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => get_the_title(),
    'description' => sprintf(__('Office locations and contact information for %s. Find your nearest recruitment office and connect with our local teams.', 'recruitpro'), $company_name),
    'url' => get_permalink(),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => $company_name,
        'url' => home_url()
    )
);

// Generate location schema for each office
$location_schemas = array();
foreach ($office_locations as $location) {
    $location_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $location['name'],
        'description' => $location['description'],
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => $location['address_line_1'] . ($location['address_line_2'] ? ', ' . $location['address_line_2'] : ''),
            'addressLocality' => $location['city'],
            'addressRegion' => $location['state'],
            'postalCode' => $location['postal_code'],
            'addressCountry' => $location['country']
        ),
        'telephone' => $location['phone'],
        'email' => $location['email'],
        'url' => get_permalink(),
        'parentOrganization' => array(
            '@type' => 'Organization',
            'name' => $company_name
        )
    );
    
    if (!empty($location['latitude']) && !empty($location['longitude'])) {
        $location_schema['geo'] = array(
            '@type' => 'GeoCoordinates',
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude']
        );
    }
    
    $location_schemas[] = $location_schema;
}
?>

<!-- Schema.org Markup -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<!-- Individual Location Schemas -->
<?php foreach ($location_schemas as $location_schema) : ?>
<script type="application/ld+json">
<?php echo wp_json_encode($location_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
<?php endforeach; ?>

<!-- Breadcrumbs -->
<?php if ($show_breadcrumbs && function_exists('recruitpro_breadcrumbs')) : ?>
    <?php recruitpro_breadcrumbs(); ?>
<?php endif; ?>

<main id="primary" class="site-main locations-page">
    
    <div class="container">
        <div class="page-content <?php echo esc_attr('sidebar-' . $sidebar_position); ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php while (have_posts()) : the_post(); ?>
                    
                    <!-- Page Header -->
                    <?php if ($show_page_title) : ?>
                        <header class="page-header locations-header">
                            <h1 class="page-title"><?php the_title(); ?></h1>
                            <?php if (get_the_content()) : ?>
                                <div class="page-description">
                                    <?php the_content(); ?>
                                </div>
                            <?php else : ?>
                                <div class="page-description">
                                    <p><?php printf(esc_html__('Discover our office locations worldwide. %s operates from multiple strategic locations to serve our clients and candidates effectively. Find your nearest office and connect with our local recruitment teams.', 'recruitpro'), esc_html($company_name)); ?></p>
                                </div>
                            <?php endif; ?>
                        </header>
                    <?php endif; ?>

                    <!-- Global Overview Map -->
                    <?php if ($show_overview_map && count($office_locations) > 1) : ?>
                        <section class="locations-overview-map" id="overview-map">
                            <div class="map-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Our Global Presence', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('Strategic locations to serve you better worldwide', 'recruitpro'); ?></p>
                                </div>

                                <div class="overview-statistics">
                                    <div class="stats-grid">
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo count($office_locations); ?></div>
                                            <div class="stat-label"><?php esc_html_e('Office Locations', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">
                                                <?php
                                                $total_team_size = 0;
                                                foreach ($office_locations as $location) {
                                                    if (!empty($location['team_size'])) {
                                                        $total_team_size += intval($location['team_size']);
                                                    }
                                                }
                                                echo $total_team_size . '+';
                                                ?>
                                            </div>
                                            <div class="stat-label"><?php esc_html_e('Team Members', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">
                                                <?php
                                                $unique_countries = array();
                                                foreach ($office_locations as $location) {
                                                    if (!empty($location['country']) && !in_array($location['country'], $unique_countries)) {
                                                        $unique_countries[] = $location['country'];
                                                    }
                                                }
                                                echo count($unique_countries);
                                                ?>
                                            </div>
                                            <div class="stat-label"><?php esc_html_e('Countries', 'recruitpro'); ?></div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">
                                                <?php
                                                $all_specializations = array();
                                                foreach ($office_locations as $location) {
                                                    if (!empty($location['specializations'])) {
                                                        $all_specializations = array_merge($all_specializations, $location['specializations']);
                                                    }
                                                }
                                                echo count(array_unique($all_specializations));
                                                ?>
                                            </div>
                                            <div class="stat-label"><?php esc_html_e('Specializations', 'recruitpro'); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Interactive Map Placeholder -->
                                <div class="interactive-map-placeholder">
                                    <div class="map-notice">
                                        <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                                        <h3><?php esc_html_e('Interactive Map', 'recruitpro'); ?></h3>
                                        <p><?php esc_html_e('Click on any location below to view detailed information and get directions to our offices.', 'recruitpro'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Office Locations Grid -->
                    <section class="office-locations-grid" id="office-locations">
                        <div class="locations-container">
                            
                            <div class="section-header">
                                <h2 class="section-title"><?php esc_html_e('Office Locations', 'recruitpro'); ?></h2>
                                <p class="section-subtitle"><?php esc_html_e('Detailed information about each of our office locations', 'recruitpro'); ?></p>
                            </div>

                            <div class="locations-filter">
                                <div class="filter-buttons">
                                    <button type="button" class="filter-btn active" data-filter="all">
                                        <?php esc_html_e('All Locations', 'recruitpro'); ?>
                                    </button>
                                    <button type="button" class="filter-btn" data-filter="headquarters">
                                        <?php esc_html_e('Headquarters', 'recruitpro'); ?>
                                    </button>
                                    <button type="button" class="filter-btn" data-filter="regional">
                                        <?php esc_html_e('Regional Offices', 'recruitpro'); ?>
                                    </button>
                                    <button type="button" class="filter-btn" data-filter="branch">
                                        <?php esc_html_e('Branch Offices', 'recruitpro'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="locations-grid <?php echo esc_attr($locations_layout); ?>-layout">
                                
                                <?php foreach ($office_locations as $location) : ?>
                                    <div class="location-item" data-location-type="<?php echo esc_attr($location['type']); ?>" data-location-id="<?php echo esc_attr($location['id']); ?>">
                                        <div class="location-content">
                                            
                                            <!-- Location Header -->
                                            <div class="location-header">
                                                <?php if (!empty($location['office_image'])) : ?>
                                                    <div class="location-image">
                                                        <img src="<?php echo esc_url($location['office_image']); ?>" 
                                                             alt="<?php echo esc_attr($location['name']); ?>" 
                                                             loading="lazy">
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="location-title-area">
                                                    <h3 class="location-name">
                                                        <?php echo esc_html($location['name']); ?>
                                                        <?php if ($location['type'] === 'headquarters') : ?>
                                                            <span class="location-badge headquarters"><?php esc_html_e('HQ', 'recruitpro'); ?></span>
                                                        <?php elseif ($location['type'] === 'regional') : ?>
                                                            <span class="location-badge regional"><?php esc_html_e('Regional', 'recruitpro'); ?></span>
                                                        <?php endif; ?>
                                                    </h3>
                                                    
                                                    <?php if (!empty($location['description'])) : ?>
                                                        <p class="location-description"><?php echo esc_html($location['description']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Location Details -->
                                            <div class="location-details">
                                                
                                                <!-- Address Information -->
                                                <div class="location-section address-section">
                                                    <h4 class="section-title">
                                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                                        <?php esc_html_e('Address', 'recruitpro'); ?>
                                                    </h4>
                                                    <div class="address-details">
                                                        <p class="address-line"><?php echo esc_html($location['address_line_1']); ?></p>
                                                        <?php if (!empty($location['address_line_2'])) : ?>
                                                            <p class="address-line"><?php echo esc_html($location['address_line_2']); ?></p>
                                                        <?php endif; ?>
                                                        <p class="address-line">
                                                            <?php echo esc_html($location['city'] . ', ' . $location['state'] . ' ' . $location['postal_code']); ?>
                                                        </p>
                                                        <p class="address-line"><?php echo esc_html($location['country']); ?></p>
                                                    </div>
                                                </div>

                                                <!-- Contact Information -->
                                                <div class="location-section contact-section">
                                                    <h4 class="section-title">
                                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                                        <?php esc_html_e('Contact', 'recruitpro'); ?>
                                                    </h4>
                                                    <div class="contact-details">
                                                        <div class="contact-item">
                                                            <span class="contact-label"><?php esc_html_e('Phone:', 'recruitpro'); ?></span>
                                                            <a href="tel:<?php echo esc_attr(str_replace(' ', '', $location['phone'])); ?>" class="contact-link">
                                                                <?php echo esc_html($location['phone']); ?>
                                                            </a>
                                                        </div>
                                                        
                                                        <div class="contact-item">
                                                            <span class="contact-label"><?php esc_html_e('Email:', 'recruitpro'); ?></span>
                                                            <a href="mailto:<?php echo esc_attr($location['email']); ?>" class="contact-link">
                                                                <?php echo esc_html($location['email']); ?>
                                                            </a>
                                                        </div>

                                                        <?php if (!empty($location['secondary_phone'])) : ?>
                                                            <div class="contact-item secondary">
                                                                <span class="contact-label"><?php esc_html_e('After Hours:', 'recruitpro'); ?></span>
                                                                <a href="tel:<?php echo esc_attr(str_replace(' ', '', $location['secondary_phone'])); ?>" class="contact-link">
                                                                    <?php echo esc_html($location['secondary_phone']); ?>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Business Hours -->
                                                <?php if ($show_office_hours && !empty($location['hours'])) : ?>
                                                    <div class="location-section hours-section">
                                                        <h4 class="section-title">
                                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                                            <?php esc_html_e('Business Hours', 'recruitpro'); ?>
                                                        </h4>
                                                        <div class="hours-details">
                                                            <?php
                                                            $days = array(
                                                                'monday' => esc_html__('Monday', 'recruitpro'),
                                                                'tuesday' => esc_html__('Tuesday', 'recruitpro'),
                                                                'wednesday' => esc_html__('Wednesday', 'recruitpro'),
                                                                'thursday' => esc_html__('Thursday', 'recruitpro'),
                                                                'friday' => esc_html__('Friday', 'recruitpro'),
                                                                'saturday' => esc_html__('Saturday', 'recruitpro'),
                                                                'sunday' => esc_html__('Sunday', 'recruitpro')
                                                            );
                                                            
                                                            foreach ($days as $day_key => $day_name) :
                                                                if (isset($location['hours'][$day_key])) :
                                                            ?>
                                                                <div class="hours-item">
                                                                    <span class="day-name"><?php echo esc_html($day_name); ?>:</span>
                                                                    <span class="day-hours <?php echo ($location['hours'][$day_key] === 'Closed') ? 'closed' : 'open'; ?>">
                                                                        <?php echo esc_html($location['hours'][$day_key]); ?>
                                                                    </span>
                                                                </div>
                                                            <?php
                                                                endif;
                                                            endforeach;
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Specializations -->
                                                <?php if (!empty($location['specializations'])) : ?>
                                                    <div class="location-section specializations-section">
                                                        <h4 class="section-title">
                                                            <i class="fas fa-briefcase" aria-hidden="true"></i>
                                                            <?php esc_html_e('Specializations', 'recruitpro'); ?>
                                                        </h4>
                                                        <div class="specializations-tags">
                                                            <?php foreach ($location['specializations'] as $specialization) : ?>
                                                                <span class="specialization-tag"><?php echo esc_html($specialization); ?></span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Transportation -->
                                                <?php if ($show_directions && (!empty($location['parking']) || !empty($location['public_transport']))) : ?>
                                                    <div class="location-section transport-section">
                                                        <h4 class="section-title">
                                                            <i class="fas fa-route" aria-hidden="true"></i>
                                                            <?php esc_html_e('Getting There', 'recruitpro'); ?>
                                                        </h4>
                                                        <div class="transport-details">
                                                            <?php if (!empty($location['public_transport'])) : ?>
                                                                <div class="transport-item">
                                                                    <i class="fas fa-subway" aria-hidden="true"></i>
                                                                    <span class="transport-info"><?php echo esc_html($location['public_transport']); ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <?php if (!empty($location['parking'])) : ?>
                                                                <div class="transport-item">
                                                                    <i class="fas fa-parking" aria-hidden="true"></i>
                                                                    <span class="transport-info">
                                                                        <?php echo $location['parking'] ? esc_html__('Parking Available', 'recruitpro') : esc_html__('No Parking', 'recruitpro'); ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Local Team Information -->
                                                <?php if ($show_local_teams && (!empty($location['team_size']) || !empty($location['languages']))) : ?>
                                                    <div class="location-section team-section">
                                                        <h4 class="section-title">
                                                            <i class="fas fa-users" aria-hidden="true"></i>
                                                            <?php esc_html_e('Local Team', 'recruitpro'); ?>
                                                        </h4>
                                                        <div class="team-details">
                                                            <?php if (!empty($location['team_size'])) : ?>
                                                                <div class="team-item">
                                                                    <span class="team-label"><?php esc_html_e('Team Size:', 'recruitpro'); ?></span>
                                                                    <span class="team-value"><?php echo esc_html($location['team_size']); ?> <?php esc_html_e('professionals', 'recruitpro'); ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <?php if (!empty($location['established_year'])) : ?>
                                                                <div class="team-item">
                                                                    <span class="team-label"><?php esc_html_e('Established:', 'recruitpro'); ?></span>
                                                                    <span class="team-value"><?php echo esc_html($location['established_year']); ?></span>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (!empty($location['languages'])) : ?>
                                                                <div class="team-item">
                                                                    <span class="team-label"><?php esc_html_e('Languages:', 'recruitpro'); ?></span>
                                                                    <span class="team-value"><?php echo esc_html(implode(', ', $location['languages'])); ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                            </div>

                                            <!-- Location Actions -->
                                            <div class="location-actions">
                                                
                                                <?php if (!empty($location['google_maps_embed']) || (!empty($location['latitude']) && !empty($location['longitude']))) : ?>
                                                    <button type="button" class="btn btn-outline location-map-btn" data-location="<?php echo esc_attr($location['id']); ?>">
                                                        <i class="fas fa-map" aria-hidden="true"></i>
                                                        <?php esc_html_e('View Map', 'recruitpro'); ?>
                                                    </button>
                                                <?php endif; ?>

                                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($location['address_line_1'] . ', ' . $location['city'] . ', ' . $location['country']); ?>" 
                                                   class="btn btn-primary" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer">
                                                    <i class="fas fa-directions" aria-hidden="true"></i>
                                                    <?php esc_html_e('Get Directions', 'recruitpro'); ?>
                                                </a>

                                                <a href="mailto:<?php echo esc_attr($location['email']); ?>" class="btn btn-outline">
                                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                                    <?php esc_html_e('Contact Office', 'recruitpro'); ?>
                                                </a>

                                            </div>

                                            <!-- Google Maps Modal (Hidden by default) -->
                                            <?php if (!empty($location['google_maps_embed'])) : ?>
                                                <div class="location-map-modal" id="map-modal-<?php echo esc_attr($location['id']); ?>" style="display: none;">
                                                    <div class="map-modal-content">
                                                        <div class="map-modal-header">
                                                            <h4 class="map-modal-title"><?php echo esc_html($location['name']); ?></h4>
                                                            <button type="button" class="map-modal-close" data-location="<?php echo esc_attr($location['id']); ?>">
                                                                <i class="fas fa-times" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                        <div class="map-modal-body">
                                                            <iframe src="<?php echo esc_url($location['google_maps_embed']); ?>"
                                                                    width="100%" 
                                                                    height="400" 
                                                                    style="border:0;" 
                                                                    allowfullscreen="" 
                                                                    loading="lazy" 
                                                                    referrerpolicy="no-referrer-when-downgrade"
                                                                    title="<?php echo esc_attr(sprintf(__('%s Location Map', 'recruitpro'), $location['name'])); ?>">
                                                            </iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </section>

                    <!-- Service Areas -->
                    <?php if ($show_service_areas) : ?>
                        <section class="service-areas" id="service-areas">
                            <div class="service-areas-container">
                                <div class="section-header">
                                    <h2 class="section-title"><?php esc_html_e('Service Areas', 'recruitpro'); ?></h2>
                                    <p class="section-subtitle"><?php esc_html_e('We serve clients and candidates across multiple regions and industries', 'recruitpro'); ?></p>
                                </div>

                                <div class="service-areas-content">
                                    <div class="areas-grid">
                                        
                                        <div class="area-item">
                                            <div class="area-icon">
                                                <i class="fas fa-globe-europe" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="area-title"><?php esc_html_e('United Kingdom', 'recruitpro'); ?></h3>
                                            <p class="area-description"><?php esc_html_e('Comprehensive coverage across England, Scotland, Wales, and Northern Ireland with specialized local expertise.', 'recruitpro'); ?></p>
                                            <div class="area-highlights">
                                                <span class="highlight-item">London & South East</span>
                                                <span class="highlight-item">Midlands</span>
                                                <span class="highlight-item">North West</span>
                                                <span class="highlight-item">Scotland</span>
                                            </div>
                                        </div>

                                        <div class="area-item">
                                            <div class="area-icon">
                                                <i class="fas fa-map" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="area-title"><?php esc_html_e('Europe', 'recruitpro'); ?></h3>
                                            <p class="area-description"><?php esc_html_e('Strategic partnerships and direct presence across major European markets for international placements.', 'recruitpro'); ?></p>
                                            <div class="area-highlights">
                                                <span class="highlight-item">Germany</span>
                                                <span class="highlight-item">France</span>
                                                <span class="highlight-item">Netherlands</span>
                                                <span class="highlight-item">Switzerland</span>
                                            </div>
                                        </div>

                                        <div class="area-item">
                                            <div class="area-icon">
                                                <i class="fas fa-laptop" aria-hidden="true"></i>
                                            </div>
                                            <h3 class="area-title"><?php esc_html_e('Remote & Global', 'recruitpro'); ?></h3>
                                            <p class="area-description"><?php esc_html_e('Supporting remote work arrangements and global placements for international companies and distributed teams.', 'recruitpro'); ?></p>
                                            <div class="area-highlights">
                                                <span class="highlight-item">Remote Work</span>
                                                <span class="highlight-item">Global Placements</span>
                                                <span class="highlight-item">Digital Nomads</span>
                                                <span class="highlight-item">International Clients</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Contact CTA -->
                    <section class="locations-cta" id="locations-cta">
                        <div class="cta-container">
                            <div class="cta-content">
                                <h2 class="cta-title"><?php esc_html_e('Ready to Connect?', 'recruitpro'); ?></h2>
                                <p class="cta-description"><?php esc_html_e('Choose your nearest location or contact our main office to discuss your recruitment needs.', 'recruitpro'); ?></p>
                                
                                <div class="cta-actions">
                                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary btn-large">
                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                        <?php esc_html_e('Contact Us', 'recruitpro'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(home_url('/jobs/')); ?>" class="btn btn-outline btn-large">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                        <?php esc_html_e('Browse Jobs', 'recruitpro'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                <?php endwhile; ?>

            </div>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none' && is_active_sidebar('locations-sidebar')) : ?>
                <aside id="secondary" class="widget-area locations-sidebar" role="complementary">
                    <div class="sidebar-inner">
                        <?php dynamic_sidebar('locations-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>

        </div>
    </div>

</main>

<script>
// Location filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const locationItems = document.querySelectorAll('.location-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter locations
            locationItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-location-type') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Map modal functionality
    const mapButtons = document.querySelectorAll('.location-map-btn');
    const mapModals = document.querySelectorAll('.location-map-modal');
    const mapCloseButtons = document.querySelectorAll('.map-modal-close');

    mapButtons.forEach(button => {
        button.addEventListener('click', function() {
            const locationId = this.getAttribute('data-location');
            const modal = document.getElementById('map-modal-' + locationId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.classList.add('modal-open');
            }
        });
    });

    mapCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const locationId = this.getAttribute('data-location');
            const modal = document.getElementById('map-modal-' + locationId);
            if (modal) {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
    });

    // Close modal on outside click
    mapModals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
    });
});
</script>

<?php
get_footer();

/* =================================================================
   OFFICE LOCATIONS PAGE TEMPLATE FEATURES SUMMARY
================================================================= */

/*

COMPREHENSIVE OFFICE LOCATIONS FEATURES:

✅ GLOBAL PRESENCE OVERVIEW
- Interactive overview map placeholder
- Global statistics (offices, team size, countries)
- Professional presentation of worldwide reach
- Strategic location highlighting

✅ DETAILED LOCATION INFORMATION
- Complete address and contact details
- Business hours for each office
- Local team size and languages
- Established year and specializations
- Emergency contact information

✅ SIMPLIFIED CONTACT INFORMATION
- Primary phone number (required)
- Primary email address (required)
- Optional secondary phone (after-hours/emergency)
- Easy to edit and configure
- No obsolete contact methods (fax removed)

✅ INTERACTIVE FEATURES
- Location type filtering (All, HQ, Regional, Branch)
- Google Maps integration with modals
- Direct directions links
- Contact actions (email, phone)
- Professional map embeds

✅ STREAMLINED BUSINESS INFORMATION
- Essential contact methods only (phone + email)
- Optional after-hours contact if needed
- Complete business hours display
- Parking availability indicators
- Public transportation details

✅ RECRUITMENT SPECIALIZATIONS
- Industry focus by location
- Local expertise highlighting
- Service area coverage
- Regional market knowledge
- Specialized recruitment services

✅ SCHEMA.ORG OPTIMIZATION
- LocalBusiness markup for each office
- Geographic coordinates support
- Organization hierarchy
- Contact point information
- Address and telephone schema

✅ SERVICE AREAS COVERAGE
- Regional service mapping
- International presence
- Remote work support
- Global placement capabilities
- Partnership network display

✅ PROFESSIONAL PRESENTATION
- Grid and list layout options
- Mobile-responsive design
- Professional contact methods
- Trust building elements
- Clear call-to-action sections

✅ TECHNICAL FEATURES
- JavaScript filtering functionality
- Modal map integration
- Responsive design system
- Accessibility compliance
- Performance optimization

PERFECT FOR:
- Multi-location recruitment agencies
- International placement firms
- Regional office networks
- Global talent acquisition
- Client relationship management

BUSINESS BENEFITS:
- Local market credibility
- Regional expertise demonstration
- Client convenience
- Professional presentation
- Trust building through presence

RECRUITMENT INDUSTRY SPECIFIC:
- Industry specialization by location
- Local market knowledge
- Regional compliance understanding
- Cultural expertise demonstration
- Strategic location advantages

*/
?>