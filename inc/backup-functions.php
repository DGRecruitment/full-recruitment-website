<?php
/**
 * RecruitPro Theme Backup Functions
 *
 * Basic backup and restore functionality for theme settings and configurations
 * Note: This handles ONLY theme-level data, not plugin or CRM data
 *
 * @package RecruitPro
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add backup options to theme admin page
 */
function recruitpro_add_backup_options() {
    add_action('recruitpro_admin_page_content', 'recruitpro_display_backup_section');
}
add_action('admin_init', 'recruitpro_add_backup_options');

/**
 * Display backup section in admin
 */
function recruitpro_display_backup_section() {
    ?>
    <div class="postbox">
        <h2 class="hndle"><?php esc_html_e('Theme Backup & Restore', 'recruitpro'); ?></h2>
        <div class="inside">
            <div class="recruitpro-backup-section">
                <h3><?php esc_html_e('Export Theme Settings', 'recruitpro'); ?></h3>
                <p><?php esc_html_e('Export your current theme customizer settings, widget configurations, and menu assignments.', 'recruitpro'); ?></p>
                <form method="post" action="">
                    <?php wp_nonce_field('recruitpro_export_settings', 'recruitpro_export_nonce'); ?>
                    <input type="hidden" name="recruitpro_action" value="export_settings">
                    <p>
                        <input type="submit" class="button button-secondary" value="<?php esc_attr_e('Export Theme Settings', 'recruitpro'); ?>">
                    </p>
                </form>
                
                <hr>
                
                <h3><?php esc_html_e('Import Theme Settings', 'recruitpro'); ?></h3>
                <p><?php esc_html_e('Import previously exported theme settings. This will overwrite current settings.', 'recruitpro'); ?></p>
                <form method="post" action="" enctype="multipart/form-data">
                    <?php wp_nonce_field('recruitpro_import_settings', 'recruitpro_import_nonce'); ?>
                    <input type="hidden" name="recruitpro_action" value="import_settings">
                    <p>
                        <input type="file" name="recruitpro_import_file" accept=".json" required>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="recruitpro_backup_current" value="1" checked>
                            <?php esc_html_e('Create backup of current settings before import', 'recruitpro'); ?>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button button-secondary" value="<?php esc_attr_e('Import Theme Settings', 'recruitpro'); ?>">
                    </p>
                </form>
                
                <hr>
                
                <h3><?php esc_html_e('Reset to Defaults', 'recruitpro'); ?></h3>
                <p class="description" style="color: #d63638;">
                    <?php esc_html_e('Warning: This will reset all theme customizer settings to their default values. This action cannot be undone.', 'recruitpro'); ?>
                </p>
                <form method="post" action="" onsubmit="return confirm('<?php esc_js_e('Are you sure you want to reset all theme settings? This cannot be undone.', 'recruitpro'); ?>');">
                    <?php wp_nonce_field('recruitpro_reset_settings', 'recruitpro_reset_nonce'); ?>
                    <input type="hidden" name="recruitpro_action" value="reset_settings">
                    <p>
                        <label>
                            <input type="checkbox" name="recruitpro_backup_before_reset" value="1" checked>
                            <?php esc_html_e('Create backup before reset', 'recruitpro'); ?>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button button-secondary" value="<?php esc_attr_e('Reset Theme Settings', 'recruitpro'); ?>">
                    </p>
                </form>
            </div>
        </div>
    </div>
    
    <style>
    .recruitpro-backup-section {
        max-width: 600px;
    }
    .recruitpro-backup-section h3 {
        margin-top: 20px;
        margin-bottom: 10px;
    }
    .recruitpro-backup-section hr {
        margin: 30px 0;
        border: none;
        border-top: 1px solid #ddd;
    }
    .recruitpro-backup-section .description {
        font-style: italic;
        margin-bottom: 10px;
    }
    </style>
    <?php
}

/**
 * Handle backup actions
 */
function recruitpro_handle_backup_actions() {
    if (!isset($_POST['recruitpro_action']) || !current_user_can('manage_options')) {
        return;
    }
    
    $action = sanitize_text_field($_POST['recruitpro_action']);
    
    switch ($action) {
        case 'export_settings':
            if (wp_verify_nonce($_POST['recruitpro_export_nonce'], 'recruitpro_export_settings')) {
                recruitpro_export_theme_settings();
            }
            break;
            
        case 'import_settings':
            if (wp_verify_nonce($_POST['recruitpro_import_nonce'], 'recruitpro_import_settings')) {
                recruitpro_import_theme_settings();
            }
            break;
            
        case 'reset_settings':
            if (wp_verify_nonce($_POST['recruitpro_reset_nonce'], 'recruitpro_reset_settings')) {
                recruitpro_reset_theme_settings();
            }
            break;
    }
}
add_action('admin_init', 'recruitpro_handle_backup_actions');

/**
 * Export theme settings to JSON file
 */
function recruitpro_export_theme_settings() {
    // Collect all theme-related data
    $export_data = array(
        'version' => wp_get_theme()->get('Version'),
        'timestamp' => current_time('timestamp'),
        'site_url' => home_url(),
        'data' => array(
            'customizer_settings' => recruitpro_get_customizer_settings(),
            'widget_data' => recruitpro_get_widget_data(),
            'menu_assignments' => recruitpro_get_menu_assignments(),
            'theme_options' => recruitpro_get_theme_options(),
        )
    );
    
    // Add hook for plugins to add their theme-related data
    $export_data = apply_filters('recruitpro_export_data', $export_data);
    
    $json_data = wp_json_encode($export_data, JSON_PRETTY_PRINT);
    
    if ($json_data === false) {
        wp_die(esc_html__('Error creating export file. Please try again.', 'recruitpro'));
    }
    
    $filename = 'recruitpro-theme-settings-' . date('Y-m-d-H-i-s') . '.json';
    
    // Send file to browser
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($json_data));
    
    echo $json_data;
    exit;
}

/**
 * Import theme settings from JSON file
 */
function recruitpro_import_theme_settings() {
    if (!isset($_FILES['recruitpro_import_file']) || $_FILES['recruitpro_import_file']['error'] !== UPLOAD_ERR_OK) {
        add_settings_error('recruitpro_import', 'file_error', esc_html__('Error uploading file. Please try again.', 'recruitpro'));
        return;
    }
    
    $file = $_FILES['recruitpro_import_file'];
    
    // Validate file type
    if ($file['type'] !== 'application/json' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json') {
        add_settings_error('recruitpro_import', 'invalid_file', esc_html__('Please upload a valid JSON file.', 'recruitpro'));
        return;
    }
    
    // Read file content
    $content = file_get_contents($file['tmp_name']);
    if ($content === false) {
        add_settings_error('recruitpro_import', 'read_error', esc_html__('Error reading file content.', 'recruitpro'));
        return;
    }
    
    // Parse JSON
    $import_data = json_decode($content, true);
    if ($import_data === null) {
        add_settings_error('recruitpro_import', 'json_error', esc_html__('Invalid JSON file format.', 'recruitpro'));
        return;
    }
    
    // Validate import data
    if (!isset($import_data['data']) || !is_array($import_data['data'])) {
        add_settings_error('recruitpro_import', 'invalid_data', esc_html__('Invalid theme settings file.', 'recruitpro'));
        return;
    }
    
    // Create backup of current settings if requested
    if (isset($_POST['recruitpro_backup_current']) && $_POST['recruitpro_backup_current'] === '1') {
        recruitpro_create_automatic_backup('before_import');
    }
    
    // Import settings
    $data = $import_data['data'];
    
    // Import customizer settings
    if (isset($data['customizer_settings'])) {
        recruitpro_import_customizer_settings($data['customizer_settings']);
    }
    
    // Import widget data
    if (isset($data['widget_data'])) {
        recruitpro_import_widget_data($data['widget_data']);
    }
    
    // Import menu assignments
    if (isset($data['menu_assignments'])) {
        recruitpro_import_menu_assignments($data['menu_assignments']);
    }
    
    // Import theme options
    if (isset($data['theme_options'])) {
        recruitpro_import_theme_options($data['theme_options']);
    }
    
    // Allow plugins to import their data
    do_action('recruitpro_import_data', $import_data);
    
    add_settings_error('recruitpro_import', 'success', esc_html__('Theme settings imported successfully!', 'recruitpro'), 'updated');
    
    // Redirect to avoid resubmission
    wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
    exit;
}

/**
 * Reset theme settings to defaults
 */
function recruitpro_reset_theme_settings() {
    // Create backup before reset if requested
    if (isset($_POST['recruitpro_backup_before_reset']) && $_POST['recruitpro_backup_before_reset'] === '1') {
        recruitpro_create_automatic_backup('before_reset');
    }
    
    // Remove all customizer settings
    $customizer_settings = recruitpro_get_customizer_settings();
    foreach ($customizer_settings as $setting => $value) {
        remove_theme_mod($setting);
    }
    
    // Reset widget data to defaults
    recruitpro_reset_widget_data();
    
    // Reset menu assignments
    recruitpro_reset_menu_assignments();
    
    // Reset theme options
    recruitpro_reset_theme_options();
    
    // Allow plugins to reset their theme-related data
    do_action('recruitpro_reset_theme_data');
    
    add_settings_error('recruitpro_reset', 'success', esc_html__('Theme settings reset to defaults successfully!', 'recruitpro'), 'updated');
    
    // Redirect to avoid resubmission
    wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
    exit;
}

/**
 * Get all customizer settings
 */
function recruitpro_get_customizer_settings() {
    $settings = array();
    $theme_mods = get_theme_mods();
    
    if (!empty($theme_mods)) {
        foreach ($theme_mods as $key => $value) {
            $settings[$key] = $value;
        }
    }
    
    return $settings;
}

/**
 * Import customizer settings
 */
function recruitpro_import_customizer_settings($settings) {
    if (!is_array($settings)) {
        return false;
    }
    
    foreach ($settings as $key => $value) {
        set_theme_mod($key, $value);
    }
    
    return true;
}

/**
 * Get widget data for theme sidebars
 */
function recruitpro_get_widget_data() {
    $widget_data = array();
    $sidebars_widgets = wp_get_sidebars_widgets();
    
    if (!empty($sidebars_widgets)) {
        $widget_data['sidebars'] = $sidebars_widgets;
        
        // Get widget instance data
        $widget_data['instances'] = array();
        foreach ($sidebars_widgets as $sidebar => $widgets) {
            if (is_array($widgets)) {
                foreach ($widgets as $widget) {
                    $widget_type = substr($widget, 0, strrpos($widget, '-'));
                    $widget_instances = get_option('widget_' . $widget_type);
                    if ($widget_instances) {
                        $widget_data['instances'][$widget_type] = $widget_instances;
                    }
                }
            }
        }
    }
    
    return $widget_data;
}

/**
 * Import widget data
 */
function recruitpro_import_widget_data($widget_data) {
    if (!is_array($widget_data) || !isset($widget_data['sidebars'])) {
        return false;
    }
    
    // Import sidebar assignments
    wp_set_sidebars_widgets($widget_data['sidebars']);
    
    // Import widget instances
    if (isset($widget_data['instances']) && is_array($widget_data['instances'])) {
        foreach ($widget_data['instances'] as $widget_type => $instances) {
            update_option('widget_' . $widget_type, $instances);
        }
    }
    
    return true;
}

/**
 * Reset widget data
 */
function recruitpro_reset_widget_data() {
    // Clear all sidebars
    wp_set_sidebars_widgets(array());
}

/**
 * Get menu assignments
 */
function recruitpro_get_menu_assignments() {
    return get_theme_mod('nav_menu_locations', array());
}

/**
 * Import menu assignments
 */
function recruitpro_import_menu_assignments($assignments) {
    if (!is_array($assignments)) {
        return false;
    }
    
    set_theme_mod('nav_menu_locations', $assignments);
    return true;
}

/**
 * Reset menu assignments
 */
function recruitpro_reset_menu_assignments() {
    remove_theme_mod('nav_menu_locations');
}

/**
 * Get theme-specific options (not customizer)
 */
function recruitpro_get_theme_options() {
    $options = array();
    
    // Get any theme-specific options stored as wp_options
    $theme_options = array(
        'recruitpro_version',
        'recruitpro_setup_complete',
        'recruitpro_backup_count',
    );
    
    foreach ($theme_options as $option) {
        $value = get_option($option);
        if ($value !== false) {
            $options[$option] = $value;
        }
    }
    
    return $options;
}

/**
 * Import theme options
 */
function recruitpro_import_theme_options($options) {
    if (!is_array($options)) {
        return false;
    }
    
    foreach ($options as $key => $value) {
        update_option($key, $value);
    }
    
    return true;
}

/**
 * Reset theme options
 */
function recruitpro_reset_theme_options() {
    $theme_options = array(
        'recruitpro_setup_complete',
        'recruitpro_backup_count',
    );
    
    foreach ($theme_options as $option) {
        delete_option($option);
    }
}

/**
 * Create automatic backup
 */
function recruitpro_create_automatic_backup($reason = 'manual') {
    $backup_data = array(
        'version' => wp_get_theme()->get('Version'),
        'timestamp' => current_time('timestamp'),
        'reason' => $reason,
        'site_url' => home_url(),
        'data' => array(
            'customizer_settings' => recruitpro_get_customizer_settings(),
            'widget_data' => recruitpro_get_widget_data(),
            'menu_assignments' => recruitpro_get_menu_assignments(),
            'theme_options' => recruitpro_get_theme_options(),
        )
    );
    
    $backup_data = apply_filters('recruitpro_backup_data', $backup_data);
    
    // Store backup in database (limit to last 5 backups)
    $backups = get_option('recruitpro_automatic_backups', array());
    
    // Add new backup
    $backups[] = $backup_data;
    
    // Keep only last 5 backups
    if (count($backups) > 5) {
        $backups = array_slice($backups, -5);
    }
    
    update_option('recruitpro_automatic_backups', $backups);
    
    // Update backup count
    $count = get_option('recruitpro_backup_count', 0);
    update_option('recruitpro_backup_count', $count + 1);
    
    return true;
}

/**
 * Get automatic backups list
 */
function recruitpro_get_automatic_backups() {
    return get_option('recruitpro_automatic_backups', array());
}

/**
 * Restore from automatic backup
 */
function recruitpro_restore_automatic_backup($backup_index) {
    $backups = recruitpro_get_automatic_backups();
    
    if (!isset($backups[$backup_index])) {
        return false;
    }
    
    $backup_data = $backups[$backup_index];
    
    if (!isset($backup_data['data'])) {
        return false;
    }
    
    // Create backup of current state before restore
    recruitpro_create_automatic_backup('before_restore');
    
    // Restore data
    $data = $backup_data['data'];
    
    if (isset($data['customizer_settings'])) {
        recruitpro_import_customizer_settings($data['customizer_settings']);
    }
    
    if (isset($data['widget_data'])) {
        recruitpro_import_widget_data($data['widget_data']);
    }
    
    if (isset($data['menu_assignments'])) {
        recruitpro_import_menu_assignments($data['menu_assignments']);
    }
    
    if (isset($data['theme_options'])) {
        recruitpro_import_theme_options($data['theme_options']);
    }
    
    do_action('recruitpro_restore_backup', $backup_data);
    
    return true;
}

/**
 * Display automatic backups in admin
 */
function recruitpro_display_automatic_backups() {
    $backups = recruitpro_get_automatic_backups();
    
    if (empty($backups)) {
        echo '<p>' . esc_html__('No automatic backups available.', 'recruitpro') . '</p>';
        return;
    }
    
    ?>
    <div class="postbox">
        <h2 class="hndle"><?php esc_html_e('Automatic Backups', 'recruitpro'); ?></h2>
        <div class="inside">
            <p><?php esc_html_e('Recent automatic backups of your theme settings:', 'recruitpro'); ?></p>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Date', 'recruitpro'); ?></th>
                        <th><?php esc_html_e('Reason', 'recruitpro'); ?></th>
                        <th><?php esc_html_e('Actions', 'recruitpro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($backups, true) as $index => $backup) : ?>
                        <tr>
                            <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $backup['timestamp'])); ?></td>
                            <td><?php echo esc_html(ucwords(str_replace('_', ' ', $backup['reason']))); ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('recruitpro_restore_backup', 'recruitpro_restore_nonce'); ?>
                                    <input type="hidden" name="recruitpro_action" value="restore_backup">
                                    <input type="hidden" name="backup_index" value="<?php echo esc_attr($index); ?>">
                                    <input type="submit" class="button button-small" value="<?php esc_attr_e('Restore', 'recruitpro'); ?>" 
                                           onclick="return confirm('<?php esc_js_e('Are you sure you want to restore this backup?', 'recruitpro'); ?>');">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * Handle automatic backup restore
 */
function recruitpro_handle_backup_restore() {
    if (!isset($_POST['recruitpro_action']) || $_POST['recruitpro_action'] !== 'restore_backup') {
        return;
    }
    
    if (!wp_verify_nonce($_POST['recruitpro_restore_nonce'], 'recruitpro_restore_backup')) {
        return;
    }
    
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $backup_index = intval($_POST['backup_index']);
    
    if (recruitpro_restore_automatic_backup($backup_index)) {
        add_settings_error('recruitpro_restore', 'success', esc_html__('Backup restored successfully!', 'recruitpro'), 'updated');
    } else {
        add_settings_error('recruitpro_restore', 'error', esc_html__('Error restoring backup. Please try again.', 'recruitpro'));
    }
    
    wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
    exit;
}
add_action('admin_init', 'recruitpro_handle_backup_restore');

/**
 * Schedule automatic backups before theme updates
 */
function recruitpro_schedule_backup_before_update() {
    recruitpro_create_automatic_backup('before_theme_update');
}
add_action('upgrader_process_complete', 'recruitpro_schedule_backup_before_update');

/**
 * Clean old backups (keep only last 10)
 */
function recruitpro_cleanup_old_backups() {
    $backups = get_option('recruitpro_automatic_backups', array());
    
    if (count($backups) > 10) {
        $backups = array_slice($backups, -10);
        update_option('recruitpro_automatic_backups', $backups);
    }
}

/**
 * Weekly backup cleanup
 */
if (!wp_next_scheduled('recruitpro_weekly_backup_cleanup')) {
    wp_schedule_event(time(), 'weekly', 'recruitpro_weekly_backup_cleanup');
}
add_action('recruitpro_weekly_backup_cleanup', 'recruitpro_cleanup_old_backups');

/**
 * Clear scheduled events on theme deactivation
 */
function recruitpro_clear_backup_schedules() {
    wp_clear_scheduled_hook('recruitpro_weekly_backup_cleanup');
}
add_action('switch_theme', 'recruitpro_clear_backup_schedules');