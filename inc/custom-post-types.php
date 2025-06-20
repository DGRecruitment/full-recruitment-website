<?php
/**
 * RecruitPro Theme Custom Post Types
 *
 * Theme-level custom post types for recruitment website presentation
 * Note: Job management is handled by the RecruitPro Jobs plugin, not here
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize custom post types
 */
function recruitpro_init_custom_post_types() {
    // Register theme-level custom post types
    recruitpro_register_testimonials_post_type();
    recruitpro_register_team_members_post_type();
    recruitpro_register_services_post_type();
    recruitpro_register_success_stories_post_type();
    
    // Register associated taxonomies
    recruitpro_register_custom_taxonomies();
    
    // Add meta boxes
    add_action('add_meta_boxes', 'recruitpro_add_custom_meta_boxes');
    
    // Save meta box data
    add_action('save_post', 'recruitpro_save_custom_meta_boxes');
    
    // Customize admin columns
    recruitpro_customize_admin_columns();
    
    // Add custom post type support
    recruitpro_add_post_type_support();
}
add_action('init', 'recruitpro_init_custom_post_types');

/**
 * Register Testimonials post type
 */
function recruitpro_register_testimonials_post_type() {
    $labels = array(
        'name'                  => esc_html_x('Testimonials', 'Post type general name', 'recruitpro'),
        'singular_name'         => esc_html_x('Testimonial', 'Post type singular name', 'recruitpro'),
        'menu_name'             => esc_html_x('Testimonials', 'Admin Menu text', 'recruitpro'),
        'name_admin_bar'        => esc_html_x('Testimonial', 'Add New on Toolbar', 'recruitpro'),
        'add_new'               => esc_html__('Add New', 'recruitpro'),
        'add_new_item'          => esc_html__('Add New Testimonial', 'recruitpro'),
        'new_item'              => esc_html__('New Testimonial', 'recruitpro'),
        'edit_item'             => esc_html__('Edit Testimonial', 'recruitpro'),
        'view_item'             => esc_html__('View Testimonial', 'recruitpro'),
        'all_items'             => esc_html__('All Testimonials', 'recruitpro'),
        'search_items'          => esc_html__('Search Testimonials', 'recruitpro'),
        'parent_item_colon'     => esc_html__('Parent Testimonials:', 'recruitpro'),
        'not_found'             => esc_html__('No testimonials found.', 'recruitpro'),
        'not_found_in_trash'    => esc_html__('No testimonials found in Trash.', 'recruitpro'),
        'featured_image'        => esc_html_x('Client Photo', 'Overrides the "Featured Image" phrase', 'recruitpro'),
        'set_featured_image'    => esc_html_x('Set client photo', 'Overrides the "Set featured image" phrase', 'recruitpro'),
        'remove_featured_image' => esc_html_x('Remove client photo', 'Overrides the "Remove featured image" phrase', 'recruitpro'),
        'use_featured_image'    => esc_html_x('Use as client photo', 'Overrides the "Use as featured image" phrase', 'recruitpro'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'testimonial'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-format-quote',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'show_in_customizer' => true,
    );

    register_post_type('testimonial', $args);
}

/**
 * Register Team Members post type
 */
function recruitpro_register_team_members_post_type() {
    $labels = array(
        'name'                  => esc_html_x('Team Members', 'Post type general name', 'recruitpro'),
        'singular_name'         => esc_html_x('Team Member', 'Post type singular name', 'recruitpro'),
        'menu_name'             => esc_html_x('Team', 'Admin Menu text', 'recruitpro'),
        'name_admin_bar'        => esc_html_x('Team Member', 'Add New on Toolbar', 'recruitpro'),
        'add_new'               => esc_html__('Add New', 'recruitpro'),
        'add_new_item'          => esc_html__('Add New Team Member', 'recruitpro'),
        'new_item'              => esc_html__('New Team Member', 'recruitpro'),
        'edit_item'             => esc_html__('Edit Team Member', 'recruitpro'),
        'view_item'             => esc_html__('View Team Member', 'recruitpro'),
        'all_items'             => esc_html__('All Team Members', 'recruitpro'),
        'search_items'          => esc_html__('Search Team Members', 'recruitpro'),
        'parent_item_colon'     => esc_html__('Parent Team Members:', 'recruitpro'),
        'not_found'             => esc_html__('No team members found.', 'recruitpro'),
        'not_found_in_trash'    => esc_html__('No team members found in Trash.', 'recruitpro'),
        'featured_image'        => esc_html_x('Team Member Photo', 'Overrides the "Featured Image" phrase', 'recruitpro'),
        'set_featured_image'    => esc_html_x('Set team member photo', 'Overrides the "Set featured image" phrase', 'recruitpro'),
        'remove_featured_image' => esc_html_x('Remove team member photo', 'Overrides the "Remove featured image" phrase', 'recruitpro'),
        'use_featured_image'    => esc_html_x('Use as team member photo', 'Overrides the "Use as featured image" phrase', 'recruitpro'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'team-member'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 26,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'show_in_customizer' => true,
    );

    register_post_type('team_member', $args);
}

/**
 * Register Services post type
 */
function recruitpro_register_services_post_type() {
    $labels = array(
        'name'                  => esc_html_x('Services', 'Post type general name', 'recruitpro'),
        'singular_name'         => esc_html_x('Service', 'Post type singular name', 'recruitpro'),
        'menu_name'             => esc_html_x('Services', 'Admin Menu text', 'recruitpro'),
        'name_admin_bar'        => esc_html_x('Service', 'Add New on Toolbar', 'recruitpro'),
        'add_new'               => esc_html__('Add New', 'recruitpro'),
        'add_new_item'          => esc_html__('Add New Service', 'recruitpro'),
        'new_item'              => esc_html__('New Service', 'recruitpro'),
        'edit_item'             => esc_html__('Edit Service', 'recruitpro'),
        'view_item'             => esc_html__('View Service', 'recruitpro'),
        'all_items'             => esc_html__('All Services', 'recruitpro'),
        'search_items'          => esc_html__('Search Services', 'recruitpro'),
        'parent_item_colon'     => esc_html__('Parent Services:', 'recruitpro'),
        'not_found'             => esc_html__('No services found.', 'recruitpro'),
        'not_found_in_trash'    => esc_html__('No services found in Trash.', 'recruitpro'),
        'featured_image'        => esc_html_x('Service Image', 'Overrides the "Featured Image" phrase', 'recruitpro'),
        'set_featured_image'    => esc_html_x('Set service image', 'Overrides the "Set featured image" phrase', 'recruitpro'),
        'remove_featured_image' => esc_html_x('Remove service image', 'Overrides the "Remove featured image" phrase', 'recruitpro'),
        'use_featured_image'    => esc_html_x('Use as service image', 'Overrides the "Use as featured image" phrase', 'recruitpro'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'service'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 27,
        'menu_icon'          => 'dashicons-awards',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'show_in_customizer' => true,
    );

    register_post_type('service', $args);
}

/**
 * Register Success Stories post type
 */
function recruitpro_register_success_stories_post_type() {
    $labels = array(
        'name'                  => esc_html_x('Success Stories', 'Post type general name', 'recruitpro'),
        'singular_name'         => esc_html_x('Success Story', 'Post type singular name', 'recruitpro'),
        'menu_name'             => esc_html_x('Success Stories', 'Admin Menu text', 'recruitpro'),
        'name_admin_bar'        => esc_html_x('Success Story', 'Add New on Toolbar', 'recruitpro'),
        'add_new'               => esc_html__('Add New', 'recruitpro'),
        'add_new_item'          => esc_html__('Add New Success Story', 'recruitpro'),
        'new_item'              => esc_html__('New Success Story', 'recruitpro'),
        'edit_item'             => esc_html__('Edit Success Story', 'recruitpro'),
        'view_item'             => esc_html__('View Success Story', 'recruitpro'),
        'all_items'             => esc_html__('All Success Stories', 'recruitpro'),
        'search_items'          => esc_html__('Search Success Stories', 'recruitpro'),
        'parent_item_colon'     => esc_html__('Parent Success Stories:', 'recruitpro'),
        'not_found'             => esc_html__('No success stories found.', 'recruitpro'),
        'not_found_in_trash'    => esc_html__('No success stories found in Trash.', 'recruitpro'),
        'featured_image'        => esc_html_x('Story Image', 'Overrides the "Featured Image" phrase', 'recruitpro'),
        'set_featured_image'    => esc_html_x('Set story image', 'Overrides the "Set featured image" phrase', 'recruitpro'),
        'remove_featured_image' => esc_html_x('Remove story image', 'Overrides the "Remove featured image" phrase', 'recruitpro'),
        'use_featured_image'    => esc_html_x('Use as story image', 'Overrides the "Use as featured image" phrase', 'recruitpro'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'success-story'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 28,
        'menu_icon'          => 'dashicons-star-filled',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'show_in_customizer' => true,
    );

    register_post_type('success_story', $args);
}

/**
 * Register custom taxonomies
 */
function recruitpro_register_custom_taxonomies() {
    // Testimonial categories
    register_taxonomy('testimonial_category', 'testimonial', array(
        'labels' => array(
            'name'              => esc_html_x('Testimonial Categories', 'taxonomy general name', 'recruitpro'),
            'singular_name'     => esc_html_x('Testimonial Category', 'taxonomy singular name', 'recruitpro'),
            'search_items'      => esc_html__('Search Categories', 'recruitpro'),
            'all_items'         => esc_html__('All Categories', 'recruitpro'),
            'parent_item'       => esc_html__('Parent Category', 'recruitpro'),
            'parent_item_colon' => esc_html__('Parent Category:', 'recruitpro'),
            'edit_item'         => esc_html__('Edit Category', 'recruitpro'),
            'update_item'       => esc_html__('Update Category', 'recruitpro'),
            'add_new_item'      => esc_html__('Add New Category', 'recruitpro'),
            'new_item_name'     => esc_html__('New Category Name', 'recruitpro'),
            'menu_name'         => esc_html__('Categories', 'recruitpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'testimonial-category'),
    ));
    
    // Team departments
    register_taxonomy('department', 'team_member', array(
        'labels' => array(
            'name'              => esc_html_x('Departments', 'taxonomy general name', 'recruitpro'),
            'singular_name'     => esc_html_x('Department', 'taxonomy singular name', 'recruitpro'),
            'search_items'      => esc_html__('Search Departments', 'recruitpro'),
            'all_items'         => esc_html__('All Departments', 'recruitpro'),
            'parent_item'       => esc_html__('Parent Department', 'recruitpro'),
            'parent_item_colon' => esc_html__('Parent Department:', 'recruitpro'),
            'edit_item'         => esc_html__('Edit Department', 'recruitpro'),
            'update_item'       => esc_html__('Update Department', 'recruitpro'),
            'add_new_item'      => esc_html__('Add New Department', 'recruitpro'),
            'new_item_name'     => esc_html__('New Department Name', 'recruitpro'),
            'menu_name'         => esc_html__('Departments', 'recruitpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'department'),
    ));
    
    // Service categories
    register_taxonomy('service_category', 'service', array(
        'labels' => array(
            'name'              => esc_html_x('Service Categories', 'taxonomy general name', 'recruitpro'),
            'singular_name'     => esc_html_x('Service Category', 'taxonomy singular name', 'recruitpro'),
            'search_items'      => esc_html__('Search Categories', 'recruitpro'),
            'all_items'         => esc_html__('All Categories', 'recruitpro'),
            'parent_item'       => esc_html__('Parent Category', 'recruitpro'),
            'parent_item_colon' => esc_html__('Parent Category:', 'recruitpro'),
            'edit_item'         => esc_html__('Edit Category', 'recruitpro'),
            'update_item'       => esc_html__('Update Category', 'recruitpro'),
            'add_new_item'      => esc_html__('Add New Category', 'recruitpro'),
            'new_item_name'     => esc_html__('New Category Name', 'recruitpro'),
            'menu_name'         => esc_html__('Categories', 'recruitpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'service-category'),
    ));
}

/**
 * Add custom meta boxes
 */
function recruitpro_add_custom_meta_boxes() {
    // Testimonial meta boxes
    add_meta_box(
        'testimonial_details',
        esc_html__('Testimonial Details', 'recruitpro'),
        'recruitpro_testimonial_details_meta_box',
        'testimonial',
        'normal',
        'high'
    );
    
    // Team member meta boxes
    add_meta_box(
        'team_member_details',
        esc_html__('Team Member Details', 'recruitpro'),
        'recruitpro_team_member_details_meta_box',
        'team_member',
        'normal',
        'high'
    );
    
    // Service meta boxes
    add_meta_box(
        'service_details',
        esc_html__('Service Details', 'recruitpro'),
        'recruitpro_service_details_meta_box',
        'service',
        'normal',
        'high'
    );
    
    // Success story meta boxes
    add_meta_box(
        'success_story_details',
        esc_html__('Success Story Details', 'recruitpro'),
        'recruitpro_success_story_details_meta_box',
        'success_story',
        'normal',
        'high'
    );
}

/**
 * Testimonial details meta box
 */
function recruitpro_testimonial_details_meta_box($post) {
    wp_nonce_field('recruitpro_testimonial_meta_box', 'recruitpro_testimonial_meta_nonce');
    
    $client_name = get_post_meta($post->ID, '_testimonial_client_name', true);
    $client_position = get_post_meta($post->ID, '_testimonial_client_position', true);
    $client_company = get_post_meta($post->ID, '_testimonial_client_company', true);
    $rating = get_post_meta($post->ID, '_testimonial_rating', true);
    $featured = get_post_meta($post->ID, '_testimonial_featured', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="testimonial_client_name"><?php esc_html_e('Client Name', 'recruitpro'); ?></label></th>
            <td><input type="text" id="testimonial_client_name" name="testimonial_client_name" value="<?php echo esc_attr($client_name); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="testimonial_client_position"><?php esc_html_e('Client Position', 'recruitpro'); ?></label></th>
            <td><input type="text" id="testimonial_client_position" name="testimonial_client_position" value="<?php echo esc_attr($client_position); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="testimonial_client_company"><?php esc_html_e('Client Company', 'recruitpro'); ?></label></th>
            <td><input type="text" id="testimonial_client_company" name="testimonial_client_company" value="<?php echo esc_attr($client_company); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="testimonial_rating"><?php esc_html_e('Rating', 'recruitpro'); ?></label></th>
            <td>
                <select id="testimonial_rating" name="testimonial_rating">
                    <option value=""><?php esc_html_e('Select Rating', 'recruitpro'); ?></option>
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?> <?php echo _n('Star', 'Stars', $i, 'recruitpro'); ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="testimonial_featured"><?php esc_html_e('Featured Testimonial', 'recruitpro'); ?></label></th>
            <td><input type="checkbox" id="testimonial_featured" name="testimonial_featured" value="1" <?php checked($featured, 1); ?> /> <?php esc_html_e('Display prominently on homepage', 'recruitpro'); ?></td>
        </tr>
    </table>
    <?php
}

/**
 * Team member details meta box
 */
function recruitpro_team_member_details_meta_box($post) {
    wp_nonce_field('recruitpro_team_member_meta_box', 'recruitpro_team_member_meta_nonce');
    
    $position = get_post_meta($post->ID, '_team_member_position', true);
    $email = get_post_meta($post->ID, '_team_member_email', true);
    $phone = get_post_meta($post->ID, '_team_member_phone', true);
    $linkedin = get_post_meta($post->ID, '_team_member_linkedin', true);
    $specialties = get_post_meta($post->ID, '_team_member_specialties', true);
    $experience = get_post_meta($post->ID, '_team_member_experience', true);
    $certifications = get_post_meta($post->ID, '_team_member_certifications', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="team_member_position"><?php esc_html_e('Position/Title', 'recruitpro'); ?></label></th>
            <td><input type="text" id="team_member_position" name="team_member_position" value="<?php echo esc_attr($position); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="team_member_email"><?php esc_html_e('Email Address', 'recruitpro'); ?></label></th>
            <td><input type="email" id="team_member_email" name="team_member_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="team_member_phone"><?php esc_html_e('Phone Number', 'recruitpro'); ?></label></th>
            <td><input type="tel" id="team_member_phone" name="team_member_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="team_member_linkedin"><?php esc_html_e('LinkedIn Profile', 'recruitpro'); ?></label></th>
            <td><input type="url" id="team_member_linkedin" name="team_member_linkedin" value="<?php echo esc_attr($linkedin); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="team_member_specialties"><?php esc_html_e('Recruitment Specialties', 'recruitpro'); ?></label></th>
            <td><textarea id="team_member_specialties" name="team_member_specialties" rows="3" class="large-text"><?php echo esc_textarea($specialties); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="team_member_experience"><?php esc_html_e('Years of Experience', 'recruitpro'); ?></label></th>
            <td><input type="number" id="team_member_experience" name="team_member_experience" value="<?php echo esc_attr($experience); ?>" min="0" max="50" /></td>
        </tr>
        <tr>
            <th><label for="team_member_certifications"><?php esc_html_e('Professional Certifications', 'recruitpro'); ?></label></th>
            <td><textarea id="team_member_certifications" name="team_member_certifications" rows="3" class="large-text"><?php echo esc_textarea($certifications); ?></textarea></td>
        </tr>
    </table>
    <?php
}

/**
 * Service details meta box
 */
function recruitpro_service_details_meta_box($post) {
    wp_nonce_field('recruitpro_service_meta_box', 'recruitpro_service_meta_nonce');
    
    $icon = get_post_meta($post->ID, '_service_icon', true);
    $features = get_post_meta($post->ID, '_service_features', true);
    $pricing_note = get_post_meta($post->ID, '_service_pricing_note', true);
    $cta_text = get_post_meta($post->ID, '_service_cta_text', true);
    $cta_url = get_post_meta($post->ID, '_service_cta_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="service_icon"><?php esc_html_e('Service Icon', 'recruitpro'); ?></label></th>
            <td>
                <select id="service_icon" name="service_icon">
                    <option value=""><?php esc_html_e('Select Icon', 'recruitpro'); ?></option>
                    <?php
                    $icons = array(
                        'dashicons-groups' => esc_html__('Team/Groups', 'recruitpro'),
                        'dashicons-businessperson' => esc_html__('Business Person', 'recruitpro'),
                        'dashicons-search' => esc_html__('Search', 'recruitpro'),
                        'dashicons-awards' => esc_html__('Awards', 'recruitpro'),
                        'dashicons-analytics' => esc_html__('Analytics', 'recruitpro'),
                        'dashicons-admin-users' => esc_html__('Users', 'recruitpro'),
                        'dashicons-networking' => esc_html__('Networking', 'recruitpro'),
                    );
                    foreach ($icons as $value => $label) :
                    ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($icon, $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="service_features"><?php esc_html_e('Key Features', 'recruitpro'); ?></label></th>
            <td>
                <textarea id="service_features" name="service_features" rows="5" class="large-text" placeholder="<?php esc_attr_e('One feature per line', 'recruitpro'); ?>"><?php echo esc_textarea($features); ?></textarea>
                <p class="description"><?php esc_html_e('Enter one feature per line', 'recruitpro'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_pricing_note"><?php esc_html_e('Pricing Note', 'recruitpro'); ?></label></th>
            <td><input type="text" id="service_pricing_note" name="service_pricing_note" value="<?php echo esc_attr($pricing_note); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g., Starting from $500', 'recruitpro'); ?>" /></td>
        </tr>
        <tr>
            <th><label for="service_cta_text"><?php esc_html_e('Call-to-Action Text', 'recruitpro'); ?></label></th>
            <td><input type="text" id="service_cta_text" name="service_cta_text" value="<?php echo esc_attr($cta_text); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g., Learn More', 'recruitpro'); ?>" /></td>
        </tr>
        <tr>
            <th><label for="service_cta_url"><?php esc_html_e('Call-to-Action URL', 'recruitpro'); ?></label></th>
            <td><input type="url" id="service_cta_url" name="service_cta_url" value="<?php echo esc_attr($cta_url); ?>" class="regular-text" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Success story details meta box
 */
function recruitpro_success_story_details_meta_box($post) {
    wp_nonce_field('recruitpro_success_story_meta_box', 'recruitpro_success_story_meta_nonce');
    
    $candidate_name = get_post_meta($post->ID, '_success_story_candidate_name', true);
    $company_name = get_post_meta($post->ID, '_success_story_company_name', true);
    $position_filled = get_post_meta($post->ID, '_success_story_position_filled', true);
    $time_to_fill = get_post_meta($post->ID, '_success_story_time_to_fill', true);
    $salary_range = get_post_meta($post->ID, '_success_story_salary_range', true);
    $challenge = get_post_meta($post->ID, '_success_story_challenge', true);
    $solution = get_post_meta($post->ID, '_success_story_solution', true);
    $result = get_post_meta($post->ID, '_success_story_result', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="success_story_candidate_name"><?php esc_html_e('Candidate Name', 'recruitpro'); ?></label></th>
            <td><input type="text" id="success_story_candidate_name" name="success_story_candidate_name" value="<?php echo esc_attr($candidate_name); ?>" class="regular-text" placeholder="<?php esc_attr_e('Use initials or first name only for privacy', 'recruitpro'); ?>" /></td>
        </tr>
        <tr>
            <th><label for="success_story_company_name"><?php esc_html_e('Company Name', 'recruitpro'); ?></label></th>
            <td><input type="text" id="success_story_company_name" name="success_story_company_name" value="<?php echo esc_attr($company_name); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="success_story_position_filled"><?php esc_html_e('Position Filled', 'recruitpro'); ?></label></th>
            <td><input type="text" id="success_story_position_filled" name="success_story_position_filled" value="<?php echo esc_attr($position_filled); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="success_story_time_to_fill"><?php esc_html_e('Time to Fill', 'recruitpro'); ?></label></th>
            <td><input type="text" id="success_story_time_to_fill" name="success_story_time_to_fill" value="<?php echo esc_attr($time_to_fill); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g., 2 weeks', 'recruitpro'); ?>" /></td>
        </tr>
        <tr>
            <th><label for="success_story_salary_range"><?php esc_html_e('Salary Range', 'recruitpro'); ?></label></th>
            <td><input type="text" id="success_story_salary_range" name="success_story_salary_range" value="<?php echo esc_attr($salary_range); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g., $50,000 - $60,000', 'recruitpro'); ?>" /></td>
        </tr>
        <tr>
            <th><label for="success_story_challenge"><?php esc_html_e('Challenge', 'recruitpro'); ?></label></th>
            <td><textarea id="success_story_challenge" name="success_story_challenge" rows="3" class="large-text"><?php echo esc_textarea($challenge); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="success_story_solution"><?php esc_html_e('Solution', 'recruitpro'); ?></label></th>
            <td><textarea id="success_story_solution" name="success_story_solution" rows="3" class="large-text"><?php echo esc_textarea($solution); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="success_story_result"><?php esc_html_e('Result', 'recruitpro'); ?></label></th>
            <td><textarea id="success_story_result" name="success_story_result" rows="3" class="large-text"><?php echo esc_textarea($result); ?></textarea></td>
        </tr>
    </table>
    <?php
}

/**
 * Save meta box data
 */
function recruitpro_save_custom_meta_boxes($post_id) {
    // Check if user has permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save testimonial meta
    if (isset($_POST['recruitpro_testimonial_meta_nonce']) && wp_verify_nonce($_POST['recruitpro_testimonial_meta_nonce'], 'recruitpro_testimonial_meta_box')) {
        $testimonial_fields = array(
            'testimonial_client_name',
            'testimonial_client_position',
            'testimonial_client_company',
            'testimonial_rating',
        );
        
        foreach ($testimonial_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Handle checkbox
        $featured = isset($_POST['testimonial_featured']) ? 1 : 0;
        update_post_meta($post_id, '_testimonial_featured', $featured);
    }
    
    // Save team member meta
    if (isset($_POST['recruitpro_team_member_meta_nonce']) && wp_verify_nonce($_POST['recruitpro_team_member_meta_nonce'], 'recruitpro_team_member_meta_box')) {
        $team_fields = array(
            'team_member_position' => 'sanitize_text_field',
            'team_member_email' => 'sanitize_email',
            'team_member_phone' => 'sanitize_text_field',
            'team_member_linkedin' => 'esc_url_raw',
            'team_member_specialties' => 'sanitize_textarea_field',
            'team_member_experience' => 'absint',
            'team_member_certifications' => 'sanitize_textarea_field',
        );
        
        foreach ($team_fields as $field => $sanitize_function) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, call_user_func($sanitize_function, $_POST[$field]));
            }
        }
    }
    
    // Save service meta
    if (isset($_POST['recruitpro_service_meta_nonce']) && wp_verify_nonce($_POST['recruitpro_service_meta_nonce'], 'recruitpro_service_meta_box')) {
        $service_fields = array(
            'service_icon' => 'sanitize_text_field',
            'service_features' => 'sanitize_textarea_field',
            'service_pricing_note' => 'sanitize_text_field',
            'service_cta_text' => 'sanitize_text_field',
            'service_cta_url' => 'esc_url_raw',
        );
        
        foreach ($service_fields as $field => $sanitize_function) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, call_user_func($sanitize_function, $_POST[$field]));
            }
        }
    }
    
    // Save success story meta
    if (isset($_POST['recruitpro_success_story_meta_nonce']) && wp_verify_nonce($_POST['recruitpro_success_story_meta_nonce'], 'recruitpro_success_story_meta_box')) {
        $success_fields = array(
            'success_story_candidate_name' => 'sanitize_text_field',
            'success_story_company_name' => 'sanitize_text_field',
            'success_story_position_filled' => 'sanitize_text_field',
            'success_story_time_to_fill' => 'sanitize_text_field',
            'success_story_salary_range' => 'sanitize_text_field',
            'success_story_challenge' => 'sanitize_textarea_field',
            'success_story_solution' => 'sanitize_textarea_field',
            'success_story_result' => 'sanitize_textarea_field',
        );
        
        foreach ($success_fields as $field => $sanitize_function) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, call_user_func($sanitize_function, $_POST[$field]));
            }
        }
    }
}

/**
 * Customize admin columns
 */
function recruitpro_customize_admin_columns() {
    // Testimonials columns
    add_filter('manage_testimonial_posts_columns', 'recruitpro_testimonial_admin_columns');
    add_action('manage_testimonial_posts_custom_column', 'recruitpro_testimonial_admin_column_content', 10, 2);
    
    // Team members columns
    add_filter('manage_team_member_posts_columns', 'recruitpro_team_member_admin_columns');
    add_action('manage_team_member_posts_custom_column', 'recruitpro_team_member_admin_column_content', 10, 2);
    
    // Services columns
    add_filter('manage_service_posts_columns', 'recruitpro_service_admin_columns');
    add_action('manage_service_posts_custom_column', 'recruitpro_service_admin_column_content', 10, 2);
    
    // Success stories columns
    add_filter('manage_success_story_posts_columns', 'recruitpro_success_story_admin_columns');
    add_action('manage_success_story_posts_custom_column', 'recruitpro_success_story_admin_column_content', 10, 2);
}

/**
 * Testimonial admin columns
 */
function recruitpro_testimonial_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['client_name'] = esc_html__('Client Name', 'recruitpro');
    $new_columns['company'] = esc_html__('Company', 'recruitpro');
    $new_columns['rating'] = esc_html__('Rating', 'recruitpro');
    $new_columns['featured'] = esc_html__('Featured', 'recruitpro');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

/**
 * Testimonial admin column content
 */
function recruitpro_testimonial_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'client_name':
            echo esc_html(get_post_meta($post_id, '_testimonial_client_name', true));
            break;
        case 'company':
            echo esc_html(get_post_meta($post_id, '_testimonial_client_company', true));
            break;
        case 'rating':
            $rating = get_post_meta($post_id, '_testimonial_rating', true);
            if ($rating) {
                echo str_repeat('★', intval($rating)) . str_repeat('☆', 5 - intval($rating));
            }
            break;
        case 'featured':
            $featured = get_post_meta($post_id, '_testimonial_featured', true);
            echo $featured ? '✓' : '—';
            break;
    }
}

/**
 * Team member admin columns
 */
function recruitpro_team_member_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['position'] = esc_html__('Position', 'recruitpro');
    $new_columns['department'] = esc_html__('Department', 'recruitpro');
    $new_columns['experience'] = esc_html__('Experience', 'recruitpro');
    $new_columns['email'] = esc_html__('Email', 'recruitpro');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

/**
 * Team member admin column content
 */
function recruitpro_team_member_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'position':
            echo esc_html(get_post_meta($post_id, '_team_member_position', true));
            break;
        case 'department':
            $terms = get_the_terms($post_id, 'department');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html($terms[0]->name);
            }
            break;
        case 'experience':
            $experience = get_post_meta($post_id, '_team_member_experience', true);
            if ($experience) {
                printf(_n('%d year', '%d years', $experience, 'recruitpro'), $experience);
            }
            break;
        case 'email':
            $email = get_post_meta($post_id, '_team_member_email', true);
            if ($email) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            }
            break;
    }
}

/**
 * Service admin columns
 */
function recruitpro_service_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['category'] = esc_html__('Category', 'recruitpro');
    $new_columns['icon'] = esc_html__('Icon', 'recruitpro');
    $new_columns['pricing'] = esc_html__('Pricing', 'recruitpro');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

/**
 * Service admin column content
 */
function recruitpro_service_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'category':
            $terms = get_the_terms($post_id, 'service_category');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html($terms[0]->name);
            }
            break;
        case 'icon':
            $icon = get_post_meta($post_id, '_service_icon', true);
            if ($icon) {
                echo '<span class="dashicons ' . esc_attr($icon) . '"></span>';
            }
            break;
        case 'pricing':
            echo esc_html(get_post_meta($post_id, '_service_pricing_note', true));
            break;
    }
}

/**
 * Success story admin columns
 */
function recruitpro_success_story_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['position'] = esc_html__('Position', 'recruitpro');
    $new_columns['company'] = esc_html__('Company', 'recruitpro');
    $new_columns['time_to_fill'] = esc_html__('Time to Fill', 'recruitpro');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

/**
 * Success story admin column content
 */
function recruitpro_success_story_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'position':
            echo esc_html(get_post_meta($post_id, '_success_story_position_filled', true));
            break;
        case 'company':
            echo esc_html(get_post_meta($post_id, '_success_story_company_name', true));
            break;
        case 'time_to_fill':
            echo esc_html(get_post_meta($post_id, '_success_story_time_to_fill', true));
            break;
    }
}

/**
 * Add post type support
 */
function recruitpro_add_post_type_support() {
    // Add theme support for custom post types
    add_post_type_support('testimonial', 'custom-fields');
    add_post_type_support('team_member', 'custom-fields');
    add_post_type_support('service', 'custom-fields');
    add_post_type_support('success_story', 'custom-fields');
    
    // Add Elementor support if available
    if (defined('ELEMENTOR_VERSION')) {
        add_post_type_support('testimonial', 'elementor');
        add_post_type_support('team_member', 'elementor');
        add_post_type_support('service', 'elementor');
        add_post_type_support('success_story', 'elementor');
    }
}

/**
 * Template functions for theme integration
 */

/**
 * Get testimonials
 */
function recruitpro_get_testimonials($args = array()) {
    $defaults = array(
        'post_type' => 'testimonial',
        'posts_per_page' => 3,
        'post_status' => 'publish',
        'meta_query' => array(),
    );
    
    // Get featured testimonials if requested
    if (isset($args['featured']) && $args['featured']) {
        $defaults['meta_query'][] = array(
            'key' => '_testimonial_featured',
            'value' => '1',
            'compare' => '='
        );
    }
    
    $args = wp_parse_args($args, $defaults);
    return new WP_Query($args);
}

/**
 * Get team members
 */
function recruitpro_get_team_members($args = array()) {
    $defaults = array(
        'post_type' => 'team_member',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    
    $args = wp_parse_args($args, $defaults);
    return new WP_Query($args);
}

/**
 * Get services
 */
function recruitpro_get_services($args = array()) {
    $defaults = array(
        'post_type' => 'service',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    
    $args = wp_parse_args($args, $defaults);
    return new WP_Query($args);
}

/**
 * Get success stories
 */
function recruitpro_get_success_stories($args = array()) {
    $defaults = array(
        'post_type' => 'success_story',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $args = wp_parse_args($args, $defaults);
    return new WP_Query($args);
}

/**
 * Display testimonial rating stars
 */
function recruitpro_display_testimonial_rating($post_id) {
    $rating = get_post_meta($post_id, '_testimonial_rating', true);
    if (!$rating) {
        return;
    }
    
    $rating = intval($rating);
    $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    
    echo '<div class="testimonial-rating" aria-label="' . sprintf(_n('%d star rating', '%d star rating', $rating, 'recruitpro'), $rating) . '">';
    echo '<span class="stars">' . esc_html($stars) . '</span>';
    echo '</div>';
}

/**
 * Check if custom post types are enabled
 */
function recruitpro_has_custom_post_types() {
    return post_type_exists('testimonial') && post_type_exists('team_member') && post_type_exists('service') && post_type_exists('success_story');
}