<?php
/**
 * RecruitPro Theme Updates Management
 *
 * Comprehensive theme update system handling version management, automatic updates,
 * update notifications, compatibility checks, and rollback functionality.
 * Designed specifically for the RecruitPro recruitment website theme with
 * enterprise-grade update management and licensing integration.
 *
 * @package RecruitPro
 * @subpackage Theme/Updates
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/theme-updates.php
 * Purpose: Theme version management and automated update system
 * Dependencies: WordPress core, cURL, JSON API
 * Conflicts: None (standalone update system)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Theme Updates Manager Class
 * 
 * Handles all aspects of theme updates including version checking,
 * automatic downloads, installation, rollback, and licensing.
 *
 * @since 1.0.0
 */
class RecruitPro_Theme_Updates {

    /**
     * Current theme version
     *
     * @since 1.0.0
     * @var string
     */
    private $current_version;

    /**
     * Theme slug for updates
     *
     * @since 1.0.0
     * @var string
     */
    private $theme_slug = 'recruitpro';

    /**
     * Update server URL
     *
     * @since 1.0.0
     * @var string
     */
    private $update_server = 'https://updates.recruitpro-theme.com/api/v1/';

    /**
     * License information
     *
     * @since 1.0.0
     * @var array
     */
    private $license_data = array();

    /**
     * Update transient key
     *
     * @since 1.0.0
     * @var string
     */
    private $transient_key = 'recruitpro_theme_update_data';

    /**
     * Update check interval (12 hours)
     *
     * @since 1.0.0
     * @var int
     */
    private $check_interval = 43200;

    /**
     * Debug mode for updates
     *
     * @since 1.0.0
     * @var bool
     */
    private $debug_mode = false;

    /**
     * Available update data
     *
     * @since 1.0.0
     * @var array
     */
    private $update_data = null;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        // Get current theme version
        $theme = wp_get_theme();
        $this->current_version = $theme->get('Version');
        
        // Initialize debug mode
        $this->debug_mode = defined('RECRUITPRO_UPDATE_DEBUG') && RECRUITPRO_UPDATE_DEBUG;
        
        // Load license data
        $this->load_license_data();
        
        // Initialize update hooks
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks for updates
     *
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Core update hooks
        add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_updates'));
        add_filter('themes_api', array($this, 'themes_api_call'), 10, 3);
        
        // Admin interface hooks
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_notices', array($this, 'admin_notices'));
        
        // AJAX handlers
        add_action('wp_ajax_recruitpro_check_updates', array($this, 'ajax_check_updates'));
        add_action('wp_ajax_recruitpro_install_update', array($this, 'ajax_install_update'));
        add_action('wp_ajax_recruitpro_rollback_theme', array($this, 'ajax_rollback_theme'));
        add_action('wp_ajax_recruitpro_activate_license', array($this, 'ajax_activate_license'));
        add_action('wp_ajax_recruitpro_deactivate_license', array($this, 'ajax_deactivate_license'));
        
        // Automatic update hooks
        add_filter('auto_update_theme', array($this, 'auto_update_theme'), 10, 2);
        add_action('automatic_updates_complete', array($this, 'automatic_updates_complete'));
        
        // Theme installation hooks
        add_action('upgrader_process_complete', array($this, 'upgrade_completed'), 10, 2);
        add_action('upgrader_pre_install', array($this, 'backup_before_upgrade'), 10, 2);
        
        // Scheduled update checks
        add_action('recruitpro_daily_update_check', array($this, 'daily_update_check'));
        
        // Register scheduled event
        if (!wp_next_scheduled('recruitpro_daily_update_check')) {
            wp_schedule_event(time(), 'daily', 'recruitpro_daily_update_check');
        }
        
        // Clean up on theme deactivation
        add_action('switch_theme', array($this, 'cleanup_update_data'));
    }

    /**
     * Load license data from options
     *
     * @since 1.0.0
     * @return void
     */
    private function load_license_data() {
        $this->license_data = get_option('recruitpro_license_data', array(
            'license_key' => '',
            'license_status' => 'inactive',
            'license_expires' => '',
            'license_type' => 'free',
            'activations_left' => 0,
            'customer_name' => '',
            'customer_email' => '',
            'purchase_date' => '',
            'support_expires' => '',
            'last_checked' => 0
        ));
    }

    /**
     * Save license data to options
     *
     * @since 1.0.0
     * @return void
     */
    private function save_license_data() {
        update_option('recruitpro_license_data', $this->license_data);
    }

    /**
     * Admin initialization
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_init() {
        // Register settings
        register_setting('recruitpro_updates', 'recruitpro_auto_updates', array(
            'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ));
        
        register_setting('recruitpro_updates', 'recruitpro_beta_updates', array(
            'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ));
        
        register_setting('recruitpro_updates', 'recruitpro_update_notifications', array(
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean'
        ));
        
        // Check for manual update requests
        $this->handle_manual_actions();
    }

    /**
     * Handle manual update actions
     *
     * @since 1.0.0
     * @return void
     */
    private function handle_manual_actions() {
        if (!current_user_can('update_themes')) {
            return;
        }

        // Force update check
        if (isset($_GET['recruitpro_check_updates']) && wp_verify_nonce($_GET['_wpnonce'], 'recruitpro_check_updates')) {
            delete_transient($this->transient_key);
            $this->check_for_updates_now();
            wp_redirect(add_query_arg('recruitpro_update_checked', 'true', remove_query_arg(array('recruitpro_check_updates', '_wpnonce'))));
            exit;
        }

        // Manual rollback
        if (isset($_GET['recruitpro_rollback']) && wp_verify_nonce($_GET['_wpnonce'], 'recruitpro_rollback')) {
            $this->perform_rollback();
        }
    }

    /**
     * Add admin menu for theme updates
     *
     * @since 1.0.0
     * @return void
     */
    public function add_admin_menu() {
        add_theme_page(
            __('RecruitPro Updates', 'recruitpro'),
            __('Theme Updates', 'recruitpro'),
            'update_themes',
            'recruitpro-updates',
            array($this, 'admin_page')
        );
    }

    /**
     * Display admin notices for updates
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_notices() {
        // Don't show on updates page
        if (isset($_GET['page']) && $_GET['page'] === 'recruitpro-updates') {
            return;
        }

        // Update available notice
        if ($this->has_update() && get_option('recruitpro_update_notifications', true)) {
            $update_data = $this->get_update_data();
            ?>
            <div class="notice notice-info is-dismissible recruitpro-update-notice">
                <h3><?php esc_html_e('RecruitPro Theme Update Available', 'recruitpro'); ?></h3>
                <p>
                    <?php
                    printf(
                        __('Version %s is now available! You are currently running version %s.', 'recruitpro'),
                        '<strong>' . esc_html($update_data['new_version']) . '</strong>',
                        '<strong>' . esc_html($this->current_version) . '</strong>'
                    );
                    ?>
                </p>
                <p>
                    <a href="<?php echo esc_url(admin_url('themes.php?page=recruitpro-updates')); ?>" class="button button-primary">
                        <?php esc_html_e('View Update Details', 'recruitpro'); ?>
                    </a>
                    <?php if ($this->can_auto_update()) : ?>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=' . get_template()), 'upgrade-theme_' . get_template())); ?>" class="button">
                            <?php esc_html_e('Update Now', 'recruitpro'); ?>
                        </a>
                    <?php endif; ?>
                </p>
            </div>
            <?php
        }

        // License expiration notice
        if ($this->is_license_expiring()) {
            $days_left = $this->get_license_days_remaining();
            ?>
            <div class="notice notice-warning is-dismissible">
                <h3><?php esc_html_e('RecruitPro License Expiring', 'recruitpro'); ?></h3>
                <p>
                    <?php
                    printf(
                        _n(
                            'Your RecruitPro license expires in %d day. Renew now to continue receiving updates.',
                            'Your RecruitPro license expires in %d days. Renew now to continue receiving updates.',
                            $days_left,
                            'recruitpro'
                        ),
                        $days_left
                    );
                    ?>
                </p>
                <p>
                    <a href="<?php echo esc_url(admin_url('themes.php?page=recruitpro-updates&tab=license')); ?>" class="button button-primary">
                        <?php esc_html_e('Manage License', 'recruitpro'); ?>
                    </a>
                </p>
            </div>
            <?php
        }

        // Update success notice
        if (isset($_GET['recruitpro_update_checked']) && $_GET['recruitpro_update_checked'] === 'true') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e('Update check completed successfully!', 'recruitpro'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Check for theme updates
     *
     * @since 1.0.0
     * @param object $transient The update transient
     * @return object Modified transient
     */
    public function check_for_updates($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        // Get cached update data
        $update_data = get_transient($this->transient_key);
        
        // Check if we need to fetch new data
        if ($update_data === false || $this->debug_mode) {
            $update_data = $this->fetch_update_data();
            
            if ($update_data !== false) {
                set_transient($this->transient_key, $update_data, $this->check_interval);
            }
        }

        // Add update to transient if available
        if ($update_data && isset($update_data['new_version']) && version_compare($this->current_version, $update_data['new_version'], '<')) {
            $transient->response[get_template()] = array(
                'theme' => get_template(),
                'new_version' => $update_data['new_version'],
                'url' => $update_data['details_url'],
                'package' => $update_data['download_url'],
                'requires' => $update_data['requires'],
                'tested' => $update_data['tested'],
                'requires_php' => $update_data['requires_php'],
                'compatibility' => $update_data['compatibility']
            );
        }

        return $transient;
    }

    /**
     * Fetch update data from server
     *
     * @since 1.0.0
     * @return array|false Update data or false on failure
     */
    private function fetch_update_data() {
        $request_args = array(
            'action' => 'check_updates',
            'theme_slug' => $this->theme_slug,
            'current_version' => $this->current_version,
            'site_url' => home_url(),
            'license_key' => $this->license_data['license_key'],
            'beta_updates' => get_option('recruitpro_beta_updates', false),
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'plugins' => $this->get_plugin_data(),
            'theme_options' => $this->get_theme_options_summary()
        );

        $response = $this->make_api_request('check-updates', $request_args);
        
        if ($response === false) {
            $this->log_error('Failed to fetch update data from server');
            return false;
        }

        if (isset($response['error'])) {
            $this->log_error('Update server error: ' . $response['error']);
            return false;
        }

        return $response;
    }

    /**
     * Handle themes API calls for update details
     *
     * @since 1.0.0
     * @param false|object|array $result The result object or array
     * @param string $action The type of information being requested
     * @param object $args Theme API arguments
     * @return false|object|array Modified result
     */
    public function themes_api_call($result, $action, $args) {
        if ($action !== 'theme_information' || $args->slug !== $this->theme_slug) {
            return $result;
        }

        $update_data = $this->get_update_data();
        
        if (!$update_data) {
            return $result;
        }

        return (object) array(
            'name' => $update_data['name'],
            'slug' => $this->theme_slug,
            'version' => $update_data['new_version'],
            'author' => $update_data['author'],
            'author_profile' => $update_data['author_profile'],
            'requires' => $update_data['requires'],
            'tested' => $update_data['tested'],
            'requires_php' => $update_data['requires_php'],
            'rating' => $update_data['rating'],
            'num_ratings' => $update_data['num_ratings'],
            'downloaded' => $update_data['downloaded'],
            'last_updated' => $update_data['last_updated'],
            'homepage' => $update_data['homepage'],
            'description' => $update_data['description'],
            'short_description' => $update_data['short_description'],
            'download_link' => $update_data['download_url'],
            'tags' => $update_data['tags'],
            'sections' => array(
                'description' => $update_data['description'],
                'changelog' => $update_data['changelog'],
                'installation' => $update_data['installation'],
                'faq' => $update_data['faq'],
                'screenshots' => $update_data['screenshots']
            ),
            'banners' => $update_data['banners'],
            'icons' => $update_data['icons']
        );
    }

    /**
     * Determine if theme should auto-update
     *
     * @since 1.0.0
     * @param bool $update Whether to update
     * @param object $item Update item
     * @return bool Whether to auto-update
     */
    public function auto_update_theme($update, $item) {
        if (isset($item->theme) && $item->theme === get_template()) {
            $auto_updates = get_option('recruitpro_auto_updates', false);
            $license_valid = $this->is_license_valid();
            
            return $auto_updates && $license_valid && $this->can_auto_update();
        }
        
        return $update;
    }

    /**
     * Handle automatic updates completion
     *
     * @since 1.0.0
     * @param array $results Update results
     * @return void
     */
    public function automatic_updates_complete($results) {
        if (isset($results['theme']) && !empty($results['theme'])) {
            foreach ($results['theme'] as $theme_result) {
                if ($theme_result->item->theme === get_template()) {
                    $this->log_update_event('automatic_update_completed', array(
                        'old_version' => $this->current_version,
                        'new_version' => $theme_result->item->new_version,
                        'result' => $theme_result->result
                    ));
                    
                    // Send notification email if enabled
                    $this->send_update_notification($theme_result);
                }
            }
        }
    }

    /**
     * Handle theme upgrade completion
     *
     * @since 1.0.0
     * @param object $upgrader_object WP_Upgrader instance
     * @param array $hook_extra Extra information
     * @return void
     */
    public function upgrade_completed($upgrader_object, $hook_extra) {
        if (isset($hook_extra['type']) && $hook_extra['type'] === 'theme' && 
            isset($hook_extra['themes']) && in_array(get_template(), $hook_extra['themes'])) {
            
            // Clear update cache
            delete_transient($this->transient_key);
            
            // Run post-update tasks
            $this->run_post_update_tasks();
            
            // Log the update
            $this->log_update_event('manual_update_completed', array(
                'old_version' => $this->current_version,
                'upgrader_class' => get_class($upgrader_object)
            ));
        }
    }

    /**
     * Create backup before upgrade
     *
     * @since 1.0.0
     * @param bool $response Installation response
     * @param array $hook_extra Extra information
     * @return bool Installation response
     */
    public function backup_before_upgrade($response, $hook_extra) {
        if (isset($hook_extra['type']) && $hook_extra['type'] === 'theme' && 
            isset($hook_extra['theme']) && $hook_extra['theme'] === get_template()) {
            
            $this->create_backup();
        }
        
        return $response;
    }

    /**
     * Create theme backup
     *
     * @since 1.0.0
     * @return bool Success status
     */
    private function create_backup() {
        $backup_dir = wp_upload_dir()['basedir'] . '/recruitpro-backups/';
        
        if (!wp_mkdir_p($backup_dir)) {
            $this->log_error('Failed to create backup directory');
            return false;
        }

        $backup_file = $backup_dir . 'recruitpro-theme-backup-' . $this->current_version . '-' . date('Y-m-d-H-i-s') . '.zip';
        $theme_dir = get_template_directory();

        // Create zip backup
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            
            if ($zip->open($backup_file, ZipArchive::CREATE) === TRUE) {
                $this->add_directory_to_zip($zip, $theme_dir, get_template());
                $zip->close();
                
                $this->log_update_event('backup_created', array(
                    'backup_file' => $backup_file,
                    'theme_version' => $this->current_version
                ));
                
                return true;
            }
        }

        // Fallback: Simple copy method
        return $this->create_simple_backup($backup_dir);
    }

    /**
     * Add directory to zip archive recursively
     *
     * @since 1.0.0
     * @param ZipArchive $zip Zip archive
     * @param string $source_dir Source directory
     * @param string $base_dir Base directory name in zip
     * @return void
     */
    private function add_directory_to_zip($zip, $source_dir, $base_dir) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $file_path = $file->getRealPath();
            $relative_path = $base_dir . '/' . substr($file_path, strlen($source_dir) + 1);

            if ($file->isDir()) {
                $zip->addEmptyDir($relative_path);
            } elseif ($file->isFile()) {
                $zip->addFile($file_path, $relative_path);
            }
        }
    }

    /**
     * Create simple backup using copy
     *
     * @since 1.0.0
     * @param string $backup_dir Backup directory
     * @return bool Success status
     */
    private function create_simple_backup($backup_dir) {
        $theme_dir = get_template_directory();
        $backup_theme_dir = $backup_dir . get_template() . '-' . $this->current_version . '/';

        if (!wp_mkdir_p($backup_theme_dir)) {
            return false;
        }

        return $this->copy_directory($theme_dir, $backup_theme_dir);
    }

    /**
     * Copy directory recursively
     *
     * @since 1.0.0
     * @param string $source Source directory
     * @param string $destination Destination directory
     * @return bool Success status
     */
    private function copy_directory($source, $destination) {
        if (!is_dir($source)) {
            return false;
        }

        if (!wp_mkdir_p($destination)) {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $dest_path = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($file->isDir()) {
                wp_mkdir_p($dest_path);
            } else {
                copy($file, $dest_path);
            }
        }

        return true;
    }

    /**
     * Perform theme rollback
     *
     * @since 1.0.0
     * @param string $version Version to rollback to
     * @return bool Success status
     */
    public function perform_rollback($version = null) {
        if (!current_user_can('update_themes')) {
            return false;
        }

        $backup_dir = wp_upload_dir()['basedir'] . '/recruitpro-backups/';
        
        if ($version === null) {
            // Find latest backup
            $backups = glob($backup_dir . 'recruitpro-theme-backup-*.zip');
            if (empty($backups)) {
                $this->log_error('No backups available for rollback');
                return false;
            }
            
            // Get the most recent backup
            usort($backups, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            $backup_file = $backups[0];
        } else {
            $backup_file = $backup_dir . 'recruitpro-theme-backup-' . $version . '*.zip';
            $backups = glob($backup_file);
            
            if (empty($backups)) {
                $this->log_error('Backup not found for version: ' . $version);
                return false;
            }
            
            $backup_file = $backups[0];
        }

        // Extract backup
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            
            if ($zip->open($backup_file) === TRUE) {
                $theme_dir = get_template_directory();
                
                // Remove current theme files
                $this->remove_directory_contents($theme_dir);
                
                // Extract backup
                $zip->extractTo(dirname($theme_dir));
                $zip->close();
                
                $this->log_update_event('rollback_completed', array(
                    'backup_file' => $backup_file,
                    'rollback_version' => $version
                ));
                
                return true;
            }
        }

        return false;
    }

    /**
     * Remove directory contents
     *
     * @since 1.0.0
     * @param string $dir Directory path
     * @return bool Success status
     */
    private function remove_directory_contents($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return true;
    }

    /**
     * Run post-update tasks
     *
     * @since 1.0.0
     * @return void
     */
    private function run_post_update_tasks() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear caches
        wp_cache_flush();
        
        // Update database if needed
        $this->maybe_update_database();
        
        // Run migration scripts
        do_action('recruitpro_theme_updated', $this->current_version);
        
        // Clean old backups
        $this->clean_old_backups();
    }

    /**
     * Maybe update database schema
     *
     * @since 1.0.0
     * @return void
     */
    private function maybe_update_database() {
        $db_version = get_option('recruitpro_theme_db_version', '1.0.0');
        
        if (version_compare($db_version, $this->current_version, '<')) {
            // Run database updates
            do_action('recruitpro_update_database', $db_version, $this->current_version);
            
            // Update stored version
            update_option('recruitpro_theme_db_version', $this->current_version);
        }
    }

    /**
     * Clean old backup files
     *
     * @since 1.0.0
     * @return void
     */
    private function clean_old_backups() {
        $backup_dir = wp_upload_dir()['basedir'] . '/recruitpro-backups/';
        $max_backups = apply_filters('recruitpro_max_backups', 5);
        
        $backups = glob($backup_dir . 'recruitpro-theme-backup-*.zip');
        
        if (count($backups) > $max_backups) {
            // Sort by modification time (oldest first)
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove excess backups
            $to_remove = array_slice($backups, 0, count($backups) - $max_backups);
            
            foreach ($to_remove as $backup_file) {
                unlink($backup_file);
            }
        }
    }

    /**
     * Daily update check (scheduled event)
     *
     * @since 1.0.0
     * @return void
     */
    public function daily_update_check() {
        // Only check if auto-updates are enabled or notifications are on
        if (!get_option('recruitpro_auto_updates', false) && !get_option('recruitpro_update_notifications', true)) {
            return;
        }

        // Force update check
        delete_transient($this->transient_key);
        $this->check_for_updates_now();
    }

    /**
     * Force update check now
     *
     * @since 1.0.0
     * @return array|false Update data or false
     */
    public function check_for_updates_now() {
        $update_data = $this->fetch_update_data();
        
        if ($update_data !== false) {
            set_transient($this->transient_key, $update_data, $this->check_interval);
        }
        
        return $update_data;
    }

    /**
     * Get update data
     *
     * @since 1.0.0
     * @return array|false Update data or false
     */
    public function get_update_data() {
        if ($this->update_data === null) {
            $this->update_data = get_transient($this->transient_key);
        }
        
        return $this->update_data;
    }

    /**
     * Check if update is available
     *
     * @since 1.0.0
     * @return bool True if update available
     */
    public function has_update() {
        $update_data = $this->get_update_data();
        
        return $update_data && isset($update_data['new_version']) && 
               version_compare($this->current_version, $update_data['new_version'], '<');
    }

    /**
     * Check if can auto-update
     *
     * @since 1.0.0
     * @return bool True if can auto-update
     */
    public function can_auto_update() {
        return $this->is_license_valid() && !$this->is_customized();
    }

    /**
     * Check if theme is customized
     *
     * @since 1.0.0
     * @return bool True if customized
     */
    private function is_customized() {
        // Check for child theme
        if (is_child_theme()) {
            return true;
        }
        
        // Check for custom modifications
        $theme_dir = get_template_directory();
        $custom_files = array(
            $theme_dir . '/custom.css',
            $theme_dir . '/custom.js',
            $theme_dir . '/custom-functions.php'
        );
        
        foreach ($custom_files as $file) {
            if (file_exists($file)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if license is valid
     *
     * @since 1.0.0
     * @return bool True if valid
     */
    public function is_license_valid() {
        if (empty($this->license_data['license_key'])) {
            return false;
        }
        
        if ($this->license_data['license_status'] !== 'active') {
            return false;
        }
        
        // Check expiration
        if (!empty($this->license_data['license_expires'])) {
            $expires = strtotime($this->license_data['license_expires']);
            if ($expires && $expires < time()) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if license is expiring soon
     *
     * @since 1.0.0
     * @return bool True if expiring within 30 days
     */
    public function is_license_expiring() {
        if (!$this->is_license_valid() || empty($this->license_data['license_expires'])) {
            return false;
        }
        
        $expires = strtotime($this->license_data['license_expires']);
        $days_left = ($expires - time()) / DAY_IN_SECONDS;
        
        return $days_left <= 30 && $days_left > 0;
    }

    /**
     * Get license days remaining
     *
     * @since 1.0.0
     * @return int Days remaining
     */
    public function get_license_days_remaining() {
        if (empty($this->license_data['license_expires'])) {
            return 0;
        }
        
        $expires = strtotime($this->license_data['license_expires']);
        $days_left = ($expires - time()) / DAY_IN_SECONDS;
        
        return max(0, floor($days_left));
    }

    /**
     * Make API request to update server
     *
     * @since 1.0.0
     * @param string $endpoint API endpoint
     * @param array $args Request arguments
     * @return array|false Response data or false on failure
     */
    private function make_api_request($endpoint, $args = array()) {
        $url = $this->update_server . $endpoint;
        
        $request_args = array(
            'method' => 'POST',
            'timeout' => 30,
            'body' => wp_json_encode($args),
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'RecruitPro Theme/' . $this->current_version . '; ' . home_url()
            )
        );
        
        $response = wp_remote_request($url, $request_args);
        
        if (is_wp_error($response)) {
            $this->log_error('API request failed: ' . $response->get_error_message());
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            $this->log_error('API request failed with code: ' . $response_code);
            return false;
        }
        
        $data = json_decode($response_body, true);
        
        if ($data === null) {
            $this->log_error('Invalid JSON response from API');
            return false;
        }
        
        return $data;
    }

    /**
     * Get plugin data for compatibility checks
     *
     * @since 1.0.0
     * @return array Plugin data
     */
    private function get_plugin_data() {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugins = get_plugins();
        $active_plugins = get_option('active_plugins', array());
        
        $plugin_data = array();
        
        foreach ($active_plugins as $plugin_file) {
            if (isset($plugins[$plugin_file])) {
                $plugin_data[] = array(
                    'name' => $plugins[$plugin_file]['Name'],
                    'version' => $plugins[$plugin_file]['Version'],
                    'file' => $plugin_file
                );
            }
        }
        
        return $plugin_data;
    }

    /**
     * Get theme options summary
     *
     * @since 1.0.0
     * @return array Theme options summary
     */
    private function get_theme_options_summary() {
        return array(
            'color_scheme' => get_theme_mod('recruitpro_color_scheme', 'professional_blue'),
            'layout_style' => get_theme_mod('recruitpro_layout_style', 'full_width'),
            'custom_css' => !empty(get_theme_mod('recruitpro_custom_css', '')),
            'child_theme' => is_child_theme(),
            'customizations' => $this->is_customized()
        );
    }

    /**
     * Log update events
     *
     * @since 1.0.0
     * @param string $event Event type
     * @param array $data Event data
     * @return void
     */
    private function log_update_event($event, $data = array()) {
        $log_entry = array(
            'timestamp' => current_time('timestamp'),
            'event' => $event,
            'data' => $data,
            'user_id' => get_current_user_id(),
            'wp_version' => get_bloginfo('version'),
            'theme_version' => $this->current_version
        );
        
        $log = get_option('recruitpro_update_log', array());
        array_unshift($log, $log_entry);
        
        // Keep only last 50 entries
        $log = array_slice($log, 0, 50);
        
        update_option('recruitpro_update_log', $log);
        
        // Also log to debug file if enabled
        if ($this->debug_mode) {
            error_log('RecruitPro Update: ' . $event . ' - ' . wp_json_encode($data));
        }
    }

    /**
     * Log error messages
     *
     * @since 1.0.0
     * @param string $message Error message
     * @return void
     */
    private function log_error($message) {
        $this->log_update_event('error', array('message' => $message));
        
        if ($this->debug_mode) {
            error_log('RecruitPro Update Error: ' . $message);
        }
    }

    /**
     * Send update notification email
     *
     * @since 1.0.0
     * @param object $update_result Update result
     * @return void
     */
    private function send_update_notification($update_result) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $site_url = home_url();
        
        if ($update_result->result) {
            $subject = sprintf(__('[%s] RecruitPro Theme Updated Successfully', 'recruitpro'), $site_name);
            $message = sprintf(
                __("Hello,\n\nYour RecruitPro theme has been automatically updated to version %s.\n\nSite: %s\nUpdate Time: %s\n\nBest regards,\nRecruitPro Update System", 'recruitpro'),
                $update_result->item->new_version,
                $site_url,
                current_time('Y-m-d H:i:s')
            );
        } else {
            $subject = sprintf(__('[%s] RecruitPro Theme Update Failed', 'recruitpro'), $site_name);
            $message = sprintf(
                __("Hello,\n\nThe automatic update of your RecruitPro theme has failed.\n\nSite: %s\nAttempted Version: %s\nUpdate Time: %s\n\nPlease check your site and update manually if needed.\n\nBest regards,\nRecruitPro Update System", 'recruitpro'),
                $site_url,
                $update_result->item->new_version,
                current_time('Y-m-d H:i:s')
            );
        }
        
        wp_mail($admin_email, $subject, $message);
    }

    /**
     * Cleanup update data on theme switch
     *
     * @since 1.0.0
     * @return void
     */
    public function cleanup_update_data() {
        // Clear scheduled events
        wp_clear_scheduled_hook('recruitpro_daily_update_check');
        
        // Clear transients
        delete_transient($this->transient_key);
    }

    /**
     * Admin page for theme updates
     *
     * @since 1.0.0
     * @return void
     */
    public function admin_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'updates';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('RecruitPro Theme Updates', 'recruitpro'); ?></h1>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=recruitpro-updates&tab=updates" class="nav-tab <?php echo $active_tab === 'updates' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Updates', 'recruitpro'); ?>
                </a>
                <a href="?page=recruitpro-updates&tab=license" class="nav-tab <?php echo $active_tab === 'license' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('License', 'recruitpro'); ?>
                </a>
                <a href="?page=recruitpro-updates&tab=settings" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Settings', 'recruitpro'); ?>
                </a>
                <a href="?page=recruitpro-updates&tab=log" class="nav-tab <?php echo $active_tab === 'log' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Update Log', 'recruitpro'); ?>
                </a>
            </nav>
            
            <div class="tab-content">
                <?php
                switch ($active_tab) {
                    case 'license':
                        $this->render_license_tab();
                        break;
                    case 'settings':
                        $this->render_settings_tab();
                        break;
                    case 'log':
                        $this->render_log_tab();
                        break;
                    default:
                        $this->render_updates_tab();
                        break;
                }
                ?>
            </div>
        </div>
        
        <style>
        .tab-content {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            margin-top: -1px;
        }
        .update-info {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
        }
        .license-status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .license-status.active {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .license-status.inactive {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .update-log {
            max-height: 400px;
            overflow-y: auto;
            background: #f5f5f5;
            padding: 10px;
            font-family: monospace;
        }
        </style>
        <?php
    }

    /**
     * Render updates tab
     *
     * @since 1.0.0
     * @return void
     */
    private function render_updates_tab() {
        $update_data = $this->get_update_data();
        ?>
        <h2><?php esc_html_e('Theme Update Status', 'recruitpro'); ?></h2>
        
        <div class="update-info">
            <h3><?php esc_html_e('Current Version', 'recruitpro'); ?>: <?php echo esc_html($this->current_version); ?></h3>
            
            <?php if ($this->has_update()) : ?>
                <div class="notice notice-info">
                    <h4><?php esc_html_e('Update Available', 'recruitpro'); ?></h4>
                    <p>
                        <?php
                        printf(
                            __('Version %s is available. %s', 'recruitpro'),
                            '<strong>' . esc_html($update_data['new_version']) . '</strong>',
                            '<a href="' . esc_url($update_data['details_url']) . '">' . __('View details', 'recruitpro') . '</a>'
                        );
                        ?>
                    </p>
                    
                    <?php if ($this->can_auto_update()) : ?>
                        <p>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=' . get_template()), 'upgrade-theme_' . get_template())); ?>" class="button button-primary">
                                <?php esc_html_e('Update Now', 'recruitpro'); ?>
                            </a>
                        </p>
                    <?php else : ?>
                        <p class="description">
                            <?php esc_html_e('Automatic updates are not available. Please check your license or customizations.', 'recruitpro'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="notice notice-success">
                    <p><?php esc_html_e('You are running the latest version of RecruitPro theme.', 'recruitpro'); ?></p>
                </div>
            <?php endif; ?>
            
            <p>
                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg('recruitpro_check_updates', 'true'), 'recruitpro_check_updates')); ?>" class="button">
                    <?php esc_html_e('Check for Updates', 'recruitpro'); ?>
                </a>
            </p>
        </div>
        
        <?php if (!empty($update_data['changelog'])) : ?>
            <h3><?php esc_html_e('Changelog', 'recruitpro'); ?></h3>
            <div class="update-info">
                <?php echo wp_kses_post($update_data['changelog']); ?>
            </div>
        <?php endif; ?>
        <?php
    }

    /**
     * Render license tab
     *
     * @since 1.0.0
     * @return void
     */
    private function render_license_tab() {
        $license_status = $this->license_data['license_status'];
        ?>
        <h2><?php esc_html_e('License Management', 'recruitpro'); ?></h2>
        
        <div class="license-status <?php echo esc_attr($license_status); ?>">
            <h3>
                <?php esc_html_e('License Status', 'recruitpro'); ?>: 
                <span><?php echo esc_html(ucfirst($license_status)); ?></span>
            </h3>
            
            <?php if ($license_status === 'active') : ?>
                <p><?php esc_html_e('Your license is active and you will receive automatic updates.', 'recruitpro'); ?></p>
                <?php if (!empty($this->license_data['license_expires'])) : ?>
                    <p>
                        <?php
                        printf(
                            __('License expires: %s', 'recruitpro'),
                            '<strong>' . esc_html(date('F j, Y', strtotime($this->license_data['license_expires']))) . '</strong>'
                        );
                        ?>
                    </p>
                <?php endif; ?>
            <?php else : ?>
                <p><?php esc_html_e('Your license is not active. Activate your license to receive updates.', 'recruitpro'); ?></p>
            <?php endif; ?>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('recruitpro_license_action', 'recruitpro_license_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('License Key', 'recruitpro'); ?></th>
                    <td>
                        <input type="text" name="license_key" value="<?php echo esc_attr($this->license_data['license_key']); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your RecruitPro license key to activate updates.', 'recruitpro'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php if ($license_status === 'active') : ?>
                <p class="submit">
                    <input type="submit" name="deactivate_license" class="button-secondary" value="<?php esc_attr_e('Deactivate License', 'recruitpro'); ?>" />
                </p>
            <?php else : ?>
                <p class="submit">
                    <input type="submit" name="activate_license" class="button-primary" value="<?php esc_attr_e('Activate License', 'recruitpro'); ?>" />
                </p>
            <?php endif; ?>
        </form>
        <?php
    }

    /**
     * Render settings tab
     *
     * @since 1.0.0
     * @return void
     */
    private function render_settings_tab() {
        ?>
        <h2><?php esc_html_e('Update Settings', 'recruitpro'); ?></h2>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('recruitpro_updates');
            do_settings_sections('recruitpro_updates');
            ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Automatic Updates', 'recruitpro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="recruitpro_auto_updates" value="1" <?php checked(get_option('recruitpro_auto_updates', false)); ?> />
                            <?php esc_html_e('Enable automatic theme updates', 'recruitpro'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Automatically update the theme when new versions are available.', 'recruitpro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php esc_html_e('Beta Updates', 'recruitpro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="recruitpro_beta_updates" value="1" <?php checked(get_option('recruitpro_beta_updates', false)); ?> />
                            <?php esc_html_e('Receive beta version updates', 'recruitpro'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Get early access to new features (not recommended for production sites).', 'recruitpro'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php esc_html_e('Update Notifications', 'recruitpro'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="recruitpro_update_notifications" value="1" <?php checked(get_option('recruitpro_update_notifications', true)); ?> />
                            <?php esc_html_e('Show update notifications in admin', 'recruitpro'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('Display notifications when updates are available.', 'recruitpro'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Render log tab
     *
     * @since 1.0.0
     * @return void
     */
    private function render_log_tab() {
        $log = get_option('recruitpro_update_log', array());
        ?>
        <h2><?php esc_html_e('Update Log', 'recruitpro'); ?></h2>
        
        <?php if (empty($log)) : ?>
            <p><?php esc_html_e('No update events logged yet.', 'recruitpro'); ?></p>
        <?php else : ?>
            <div class="update-log">
                <?php foreach ($log as $entry) : ?>
                    <div class="log-entry">
                        <strong><?php echo esc_html(date('Y-m-d H:i:s', $entry['timestamp'])); ?></strong> - 
                        <?php echo esc_html($entry['event']); ?>
                        <?php if (!empty($entry['data'])) : ?>
                            <br><small><?php echo esc_html(wp_json_encode($entry['data'])); ?></small>
                        <?php endif; ?>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php
    }

    /**
     * AJAX handler for checking updates
     *
     * @since 1.0.0
     * @return void
     */
    public function ajax_check_updates() {
        check_ajax_referer('recruitpro_updates_nonce', 'nonce');
        
        if (!current_user_can('update_themes')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $update_data = $this->check_for_updates_now();
        
        if ($update_data !== false) {
            wp_send_json_success(array(
                'message' => __('Update check completed.', 'recruitpro'),
                'has_update' => $this->has_update(),
                'update_data' => $update_data
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to check for updates.', 'recruitpro')
            ));
        }
    }

    /**
     * AJAX handler for installing update
     *
     * @since 1.0.0
     * @return void
     */
    public function ajax_install_update() {
        check_ajax_referer('recruitpro_updates_nonce', 'nonce');
        
        if (!current_user_can('update_themes')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        // This would trigger the normal WordPress update process
        wp_send_json_success(array(
            'message' => __('Update process initiated.', 'recruitpro'),
            'redirect' => wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=' . get_template()), 'upgrade-theme_' . get_template())
        ));
    }

    /**
     * AJAX handler for theme rollback
     *
     * @since 1.0.0
     * @return void
     */
    public function ajax_rollback_theme() {
        check_ajax_referer('recruitpro_updates_nonce', 'nonce');
        
        if (!current_user_can('update_themes')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $version = isset($_POST['version']) ? sanitize_text_field($_POST['version']) : null;
        $success = $this->perform_rollback($version);
        
        if ($success) {
            wp_send_json_success(array(
                'message' => __('Theme rollback completed successfully.', 'recruitpro')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Theme rollback failed.', 'recruitpro')
            ));
        }
    }

    /**
     * AJAX handler for license activation
     *
     * @since 1.0.0
     * @return void
     */
    public function ajax_activate_license() {
        check_ajax_referer('recruitpro_license_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $license_key = sanitize_text_field($_POST['license_key']);
        $result = $this->activate_license($license_key);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('License activated successfully.', 'recruitpro')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('License activation failed.', 'recruitpro')
            ));
        }
    }

    /**
     * AJAX handler for license deactivation
     *
     * @since 1.0.0
     * @return void
     */
    public function ajax_deactivate_license() {
        check_ajax_referer('recruitpro_license_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'recruitpro'));
        }

        $result = $this->deactivate_license();
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('License deactivated successfully.', 'recruitpro')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('License deactivation failed.', 'recruitpro')
            ));
        }
    }

    /**
     * Activate license
     *
     * @since 1.0.0
     * @param string $license_key License key
     * @return bool Success status
     */
    private function activate_license($license_key) {
        $request_args = array(
            'action' => 'activate_license',
            'license_key' => $license_key,
            'site_url' => home_url(),
            'theme_version' => $this->current_version
        );

        $response = $this->make_api_request('license', $request_args);
        
        if ($response && isset($response['status']) && $response['status'] === 'active') {
            $this->license_data = array_merge($this->license_data, $response);
            $this->save_license_data();
            
            $this->log_update_event('license_activated', array(
                'license_key' => substr($license_key, 0, 8) . '...',
                'expires' => $response['expires']
            ));
            
            return true;
        }
        
        return false;
    }

    /**
     * Deactivate license
     *
     * @since 1.0.0
     * @return bool Success status
     */
    private function deactivate_license() {
        $request_args = array(
            'action' => 'deactivate_license',
            'license_key' => $this->license_data['license_key'],
            'site_url' => home_url()
        );

        $response = $this->make_api_request('license', $request_args);
        
        if ($response && isset($response['status']) && $response['status'] === 'deactivated') {
            $this->license_data['license_status'] = 'inactive';
            $this->save_license_data();
            
            $this->log_update_event('license_deactivated');
            
            return true;
        }
        
        return false;
    }
}

// Initialize the theme updates system
if (is_admin()) {
    new RecruitPro_Theme_Updates();
}

/**
 * Convenience functions for accessing update system
 */

if (!function_exists('recruitpro_check_for_updates')) {
    /**
     * Check for theme updates
     *
     * @since 1.0.0
     * @return bool True if update available
     */
    function recruitpro_check_for_updates() {
        $updates = new RecruitPro_Theme_Updates();
        return $updates->has_update();
    }
}

if (!function_exists('recruitpro_get_update_data')) {
    /**
     * Get update data
     *
     * @since 1.0.0
     * @return array|false Update data or false
     */
    function recruitpro_get_update_data() {
        $updates = new RecruitPro_Theme_Updates();
        return $updates->get_update_data();
    }
}

if (!function_exists('recruitpro_is_license_valid')) {
    /**
     * Check if license is valid
     *
     * @since 1.0.0
     * @return bool True if valid
     */
    function recruitpro_is_license_valid() {
        $updates = new RecruitPro_Theme_Updates();
        return $updates->is_license_valid();
    }
}

// End of theme-updates.php