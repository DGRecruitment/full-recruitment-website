<?php
/**
 * RecruitPro Theme Admin Functions
 *
 * Admin-specific functions and customizations for the WordPress dashboard
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add RecruitPro admin menu
 */
function recruitpro_add_admin_menu() {
    add_theme_page(
        esc_html__('RecruitPro Theme Options', 'recruitpro'),
        esc_html__('RecruitPro Theme', 'recruitpro'),
        'manage_options',
        'recruitpro-theme-options',
        'recruitpro_theme_options_page'
    );
}
add_action('admin_menu', 'recruitpro_add_admin_menu');

/**
 * Theme options page callback
 */
function recruitpro_theme_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="recruitpro-admin-content">
            <div class="recruitpro-admin-main">
                <div class="postbox">
                    <h2 class="hndle"><?php esc_html_e('Theme Status', 'recruitpro'); ?></h2>
                    <div class="inside">
                        <?php recruitpro_display_theme_status(); ?>
                    </div>
                </div>
                
                <div class="postbox">
                    <h2 class="hndle"><?php esc_html_e('Plugin Integration Status', 'recruitpro'); ?></h2>
                    <div class="inside">
                        <?php recruitpro_display_plugin_status(); ?>
                    </div>
                </div>
                
                <div class="postbox">
                    <h2 class="hndle"><?php esc_html_e('Theme Configuration', 'recruitpro'); ?></h2>
                    <div class="inside">
                        <p><?php esc_html_e('Use the WordPress Customizer to configure theme settings.', 'recruitpro'); ?></p>
                        <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button button-primary">
                            <?php esc_html_e('Open Customizer', 'recruitpro'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="recruitpro-admin-sidebar">
                <div class="postbox">
                    <h2 class="hndle"><?php esc_html_e('Quick Actions', 'recruitpro'); ?></h2>
                    <div class="inside">
                        <p><a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button">
                            <?php esc_html_e('Customize Theme', 'recruitpro'); ?>
                        </a></p>
                        <p><a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="button">
                            <?php esc_html_e('Manage Menus', 'recruitpro'); ?>
                        </a></p>
                        <p><a href="<?php echo esc_url(admin_url('widgets.php')); ?>" class="button">
                            <?php esc_html_e('Manage Widgets', 'recruitpro'); ?>
                        </a></p>
                    </div>
                </div>
                
                <div class="postbox">
                    <h2 class="hndle"><?php esc_html_e('Documentation', 'recruitpro'); ?></h2>
                    <div class="inside">
                        <p><?php esc_html_e('Need help? Check out the documentation.', 'recruitpro'); ?></p>
                        <p><a href="#" class="button" target="_blank">
                            <?php esc_html_e('View Documentation', 'recruitpro'); ?>
                        </a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .recruitpro-admin-content {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .recruitpro-admin-main {
        flex: 2;
    }
    .recruitpro-admin-sidebar {
        flex: 1;
    }
    .recruitpro-status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    .recruitpro-status-item:last-child {
        border-bottom: none;
    }
    .recruitpro-status-badge {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-active {
        background-color: #4CAF50;
        color: white;
    }
    .status-inactive {
        background-color: #f44336;
        color: white;
    }
    .status-warning {
        background-color: #ff9800;
        color: white;
    }
    </style>
    <?php
}

/**
 * Display theme status information
 */
function recruitpro_display_theme_status() {
    $theme = wp_get_theme();
    ?>
    <div class="recruitpro-status-item">
        <span><?php esc_html_e('Theme Version', 'recruitpro'); ?></span>
        <span class="recruitpro-status-badge status-active"><?php echo esc_html($theme->get('Version')); ?></span>
    </div>
    <div class="recruitpro-status-item">
        <span><?php esc_html_e('WordPress Version', 'recruitpro'); ?></span>
        <span class="recruitpro-status-badge <?php echo version_compare(get_bloginfo('version'), '5.0', '>=') ? 'status-active' : 'status-warning'; ?>">
            <?php echo esc_html(get_bloginfo('version')); ?>
        </span>
    </div>
    <div class="recruitpro-status-item">
        <span><?php esc_html_e('PHP Version', 'recruitpro'); ?></span>
        <span class="recruitpro-status-badge <?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'status-active' : 'status-warning'; ?>">
            <?php echo esc_html(PHP_VERSION); ?>
        </span>
    </div>
    <div class="recruitpro-status-item">
        <span><?php esc_html_e('Classic Editor Support', 'recruitpro'); ?></span>
        <span class="recruitpro-status-badge status-active"><?php esc_html_e('Active', 'recruitpro'); ?></span>
    </div>
    <div class="recruitpro-status-item">
        <span><?php esc_html_e('Elementor Compatible', 'recruitpro'); ?></span>
        <span class="recruitpro-status-badge status-active"><?php esc_html_e('Yes', 'recruitpro'); ?></span>
    </div>
    <?php
}

/**
 * Display plugin integration status
 */
function recruitpro_display_plugin_status() {
    $plugins = array(
        'RecruitPro CRM' => class_exists('RecruitPro_CRM'),
        'RecruitPro Jobs' => class_exists('RecruitPro_Jobs'),
        'RecruitPro SEO' => class_exists('RecruitPro_SEO'),
        'RecruitPro Security' => class_exists('RecruitPro_Security'),
        'RecruitPro Social' => class_exists('RecruitPro_Social'),
        'RecruitPro Forms' => class_exists('RecruitPro_Forms'),
        'Classic Editor' => class_exists('Classic_Editor'),
        'Elementor' => class_exists('Elementor\Plugin'),
    );
    
    foreach ($plugins as $plugin_name => $is_active) {
        ?>
        <div class="recruitpro-status-item">
            <span><?php echo esc_html($plugin_name); ?></span>
            <span class="recruitpro-status-badge <?php echo $is_active ? 'status-active' : 'status-inactive'; ?>">
                <?php echo $is_active ? esc_html__('Active', 'recruitpro') : esc_html__('Inactive', 'recruitpro'); ?>
            </span>
        </div>
        <?php
    }
}

/**
 * Add admin notices for theme requirements
 */
function recruitpro_admin_notices() {
    // Check WordPress version
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
                <?php esc_html_e('This theme requires WordPress 5.0 or higher. Please update WordPress.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
    }
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
                <?php esc_html_e('This theme requires PHP 7.4 or higher. Please contact your hosting provider.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
    }
    
    // Recommend RecruitPro plugins
    if (!class_exists('RecruitPro_CRM')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
                <?php esc_html_e('Install the RecruitPro CRM plugin for full recruitment functionality.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
    }
    
    // Recommend Classic Editor for better compatibility
    if (!class_exists('Classic_Editor')) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p>
                <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
                <?php esc_html_e('Install the Classic Editor plugin for the best editing experience.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'recruitpro_admin_notices');

/**
 * Disable user registration notice in admin
 */
function recruitpro_disable_registration_notice() {
    if (get_option('users_can_register')) {
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php esc_html_e('RecruitPro Theme:', 'recruitpro'); ?></strong>
                <?php esc_html_e('User registration is disabled by design. Candidates apply via forms, clients access via CRM-generated links.', 'recruitpro'); ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'recruitpro_disable_registration_notice');

/**
 * Customize admin footer text
 */
function recruitpro_admin_footer_text($text) {
    return sprintf(
        esc_html__('Thank you for using %1$s theme. | %2$s', 'recruitpro'),
        '<strong>RecruitPro</strong>',
        $text
    );
}
add_filter('admin_footer_text', 'recruitpro_admin_footer_text');

/**
 * Add theme-specific admin styles
 */
function recruitpro_admin_styles() {
    ?>
    <style>
    .recruitpro-admin-logo {
        max-width: 200px;
        margin-bottom: 20px;
    }
    .recruitpro-admin-notice {
        border-left: 4px solid #0073aa;
        padding: 12px;
        margin: 20px 0;
        background: #f7f7f7;
    }
    .recruitpro-admin-notice h3 {
        margin-top: 0;
        color: #0073aa;
    }
    </style>
    <?php
}
add_action('admin_head', 'recruitpro_admin_styles');

/**
 * Remove unnecessary admin menu items for non-admin users
 */
function recruitpro_remove_admin_menus() {
    // Only remove for non-administrators
    if (!current_user_can('administrator')) {
        // Remove comments menu (recruitment sites typically don't use comments)
        remove_menu_page('edit-comments.php');
        
        // Remove users menu (no front-end registration)
        remove_menu_page('users.php');
        
        // Remove tools menu for non-admins
        remove_menu_page('tools.php');
    }
}
add_action('admin_menu', 'recruitpro_remove_admin_menus', 999);

/**
 * Customize dashboard widgets for recruitment focus
 */
function recruitpro_dashboard_widgets() {
    global $wp_meta_boxes;
    
    // Remove default WordPress widgets
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    
    // Add custom RecruitPro widget
    wp_add_dashboard_widget(
        'recruitpro_dashboard_widget',
        esc_html__('RecruitPro Theme Status', 'recruitpro'),
        'recruitpro_dashboard_widget_function'
    );
}
add_action('wp_dashboard_setup', 'recruitpro_dashboard_widgets');

/**
 * RecruitPro dashboard widget function
 */
function recruitpro_dashboard_widget_function() {
    ?>
    <div class="recruitpro-dashboard-widget">
        <h3><?php esc_html_e('Theme Information', 'recruitpro'); ?></h3>
        <p>
            <strong><?php esc_html_e('Version:', 'recruitpro'); ?></strong> 
            <?php echo esc_html(wp_get_theme()->get('Version')); ?>
        </p>
        <p>
            <strong><?php esc_html_e('Status:', 'recruitpro'); ?></strong>
            <span style="color: #46b450;"><?php esc_html_e('Active & Optimized', 'recruitpro'); ?></span>
        </p>
        
        <h3><?php esc_html_e('Quick Links', 'recruitpro'); ?></h3>
        <ul>
            <li><a href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php esc_html_e('Customize Theme', 'recruitpro'); ?></a></li>
            <li><a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><?php esc_html_e('Manage Menus', 'recruitpro'); ?></a></li>
            <li><a href="<?php echo esc_url(admin_url('widgets.php')); ?>"><?php esc_html_e('Manage Widgets', 'recruitpro'); ?></a></li>
            <?php if (class_exists('RecruitPro_CRM')) : ?>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=recruitpro-crm')); ?>"><?php esc_html_e('CRM Dashboard', 'recruitpro'); ?></a></li>
            <?php endif; ?>
        </ul>
        
        <h3><?php esc_html_e('Performance Tips', 'recruitpro'); ?></h3>
        <ul>
            <li><?php esc_html_e('Enable caching for better performance', 'recruitpro'); ?></li>
            <li><?php esc_html_e('Optimize images for faster loading', 'recruitpro'); ?></li>
            <li><?php esc_html_e('Regular database cleanup recommended', 'recruitpro'); ?></li>
        </ul>
    </div>
    <?php
}

/**
 * Add custom columns to posts/pages admin
 */
function recruitpro_add_custom_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        // Add template column after title
        if ($key === 'title') {
            $new_columns['template'] = esc_html__('Template', 'recruitpro');
        }
    }
    
    return $new_columns;
}
add_filter('manage_pages_columns', 'recruitpro_add_custom_columns');

/**
 * Populate custom columns
 */
function recruitpro_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'template':
            $template = get_page_template_slug($post_id);
            if ($template) {
                $template_name = basename($template, '.php');
                echo esc_html(ucwords(str_replace(array('page-', '-'), array('', ' '), $template_name)));
            } else {
                echo esc_html__('Default', 'recruitpro');
            }
            break;
    }
}
add_action('manage_pages_custom_column', 'recruitpro_custom_column_content', 10, 2);

/**
 * Add contextual help
 */
function recruitpro_add_contextual_help() {
    $screen = get_current_screen();
    
    if ($screen->id === 'appearance_page_recruitpro-theme-options') {
        $screen->add_help_tab(array(
            'id'      => 'recruitpro-overview',
            'title'   => esc_html__('Overview', 'recruitpro'),
            'content' => '<p>' . esc_html__('The RecruitPro theme is designed specifically for recruitment agencies and HR professionals.', 'recruitpro') . '</p>',
        ));
        
        $screen->add_help_tab(array(
            'id'      => 'recruitpro-customization',
            'title'   => esc_html__('Customization', 'recruitpro'),
            'content' => '<p>' . esc_html__('Use the WordPress Customizer to modify theme colors, fonts, and layout options.', 'recruitpro') . '</p>',
        ));
        
        $screen->set_help_sidebar(
            '<p><strong>' . esc_html__('For more information:', 'recruitpro') . '</strong></p>' .
            '<p><a href="#" target="_blank">' . esc_html__('Theme Documentation', 'recruitpro') . '</a></p>' .
            '<p><a href="#" target="_blank">' . esc_html__('Support Forum', 'recruitpro') . '</a></p>'
        );
    }
}
add_action('load-appearance_page_recruitpro-theme-options', 'recruitpro_add_contextual_help');

/**
 * Prevent theme conflicts with plugins
 */
function recruitpro_prevent_conflicts() {
    // Ensure theme doesn't override plugin functionality
    if (!function_exists('recruitpro_crm_is_active')) {
        function recruitpro_crm_is_active() {
            return class_exists('RecruitPro_CRM');
        }
    }
    
    // Add hook for plugins to register their admin pages
    do_action('recruitpro_admin_init');
}
add_action('admin_init', 'recruitpro_prevent_conflicts');

/**
 * Theme activation hook
 */
function recruitpro_theme_activation() {
    // Set default customizer options
    $defaults = array(
        'recruitpro_logo' => '',
        'recruitpro_primary_color' => '#0073aa',
        'recruitpro_secondary_color' => '#005177',
        'recruitpro_enable_ai_chat' => true,
        'recruitpro_ai_chat_position' => 'bottom-right',
    );
    
    foreach ($defaults as $option => $value) {
        if (!get_theme_mod($option)) {
            set_theme_mod($option, $value);
        }
    }
    
    // Create default pages if they don't exist
    $pages = array(
        'jobs' => esc_html__('Jobs', 'recruitpro'),
        'about' => esc_html__('About Us', 'recruitpro'),
        'contact' => esc_html__('Contact', 'recruitpro'),
        'blog' => esc_html__('Blog', 'recruitpro'),
    );
    
    foreach ($pages as $slug => $title) {
        if (!get_page_by_path($slug)) {
            wp_insert_post(array(
                'post_title' => $title,
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => sprintf(esc_html__('This is the %s page. You can edit this content in the WordPress admin.', 'recruitpro'), $title),
            ));
        }
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'recruitpro_theme_activation');

/**
 * Theme deactivation hook
 */
function recruitpro_theme_deactivation() {
    // Cleanup temporary data only, keep user content
    delete_transient('recruitpro_google_fonts');
    flush_rewrite_rules();
}
add_action('switch_theme', 'recruitpro_theme_deactivation');