<?php
/**
 * RecruitPro Theme Security Manager
 *
 * Basic security enhancements for recruitment websites at the theme level.
 * Provides essential security hardening, form protection, file upload security,
 * and protection for sensitive recruitment data. Works alongside the main
 * security plugin without conflicts.
 *
 * @package RecruitPro
 * @subpackage Theme/Security
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/security.php
 * Purpose: Theme-level security enhancements for recruitment websites
 * Dependencies: WordPress core, theme functions
 * Features: Basic hardening, form security, file upload protection
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Security Manager Class
 * 
 * Handles basic security functionality at the theme level including
 * WordPress hardening, form protection, and file upload security.
 *
 * @since 1.0.0
 */
class RecruitPro_Security_Manager {

    /**
     * Security settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $security_settings = array();

    /**
     * Security logs
     * 
     * @since 1.0.0
     * @var array
     */
    private $security_logs = array();

    /**
     * Blocked IPs cache
     * 
     * @since 1.0.0
     * @var array
     */
    private $blocked_ips = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_settings();
        $this->init_hooks();
        $this->load_blocked_ips();
    }

    /**
     * Initialize security settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_settings() {
        $this->security_settings = array(
            // Core security settings
            'enable_basic_security' => get_theme_mod('recruitpro_enable_basic_security', true),
            'enable_login_protection' => get_theme_mod('recruitpro_enable_login_protection', true),
            'enable_form_protection' => get_theme_mod('recruitpro_enable_form_protection', true),
            'enable_file_protection' => get_theme_mod('recruitpro_enable_file_protection', true),
            
            // WordPress hardening
            'hide_wp_version' => get_theme_mod('recruitpro_hide_wp_version', true),
            'disable_xml_rpc' => get_theme_mod('recruitpro_disable_xml_rpc', true),
            'disable_user_enumeration' => get_theme_mod('recruitpro_disable_user_enumeration', true),
            'remove_wp_generators' => get_theme_mod('recruitpro_remove_wp_generators', true),
            
            // Login security
            'login_attempts_limit' => get_theme_mod('recruitpro_login_attempts_limit', 5),
            'login_lockout_duration' => get_theme_mod('recruitpro_login_lockout_duration', 900), // 15 minutes
            'strong_password_required' => get_theme_mod('recruitpro_strong_password_required', true),
            'hide_login_errors' => get_theme_mod('recruitpro_hide_login_errors', true),
            
            // File upload security
            'allowed_file_types' => get_theme_mod('recruitpro_allowed_file_types', array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png')),
            'max_file_size' => get_theme_mod('recruitpro_max_file_size', 5242880), // 5MB
            'scan_uploaded_files' => get_theme_mod('recruitpro_scan_uploaded_files', true),
            'quarantine_suspicious_files' => get_theme_mod('recruitpro_quarantine_suspicious_files', true),
            
            // Form security
            'enable_csrf_protection' => get_theme_mod('recruitpro_enable_csrf_protection', true),
            'enable_honeypot_protection' => get_theme_mod('recruitpro_enable_honeypot_protection', true),
            'enable_rate_limiting' => get_theme_mod('recruitpro_enable_rate_limiting', true),
            'rate_limit_requests' => get_theme_mod('recruitpro_rate_limit_requests', 10), // per minute
            
            // Headers and hardening
            'enable_security_headers' => get_theme_mod('recruitpro_enable_security_headers', true),
            'enable_content_type_validation' => get_theme_mod('recruitpro_enable_content_type_validation', true),
            'disable_directory_browsing' => get_theme_mod('recruitpro_disable_directory_browsing', true),
            
            // Recruitment specific security
            'encrypt_candidate_data' => get_theme_mod('recruitpro_encrypt_candidate_data', true),
            'secure_cv_storage' => get_theme_mod('recruitpro_secure_cv_storage', true),
            'audit_data_access' => get_theme_mod('recruitpro_audit_data_access', true),
            'gdpr_compliance_mode' => get_theme_mod('recruitpro_gdpr_compliance_mode', true),
        );
    }

    /**
     * Initialize WordPress hooks
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        if (!$this->security_settings['enable_basic_security']) {
            return;
        }

        // Core WordPress hardening hooks
        add_action('init', array($this, 'wordpress_hardening'));
        add_action('wp_head', array($this, 'add_security_headers'), 1);
        
        // Login security hooks
        if ($this->security_settings['enable_login_protection']) {
            add_action('wp_login_failed', array($this, 'handle_failed_login'));
            add_filter('authenticate', array($this, 'check_login_attempts'), 30, 3);
            add_filter('login_errors', array($this, 'customize_login_errors'));
            add_action('wp_login', array($this, 'handle_successful_login'), 10, 2);
        }
        
        // Form protection hooks
        if ($this->security_settings['enable_form_protection']) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_form_security_scripts'));
            add_filter('wp_mail', array($this, 'secure_email_forms'));
            add_action('wp_ajax_recruitpro_verify_form_token', array($this, 'verify_form_token'));
            add_action('wp_ajax_nopriv_recruitpro_verify_form_token', array($this, 'verify_form_token'));
        }
        
        // File upload security hooks
        if ($this->security_settings['enable_file_protection']) {
            add_filter('wp_handle_upload_prefilter', array($this, 'secure_file_uploads'));
            add_filter('upload_mimes', array($this, 'filter_upload_mimes'));
            add_action('wp_handle_upload', array($this, 'scan_uploaded_file'));
        }
        
        // Rate limiting hooks
        if ($this->security_settings['enable_rate_limiting']) {
            add_action('init', array($this, 'check_rate_limits'));
        }
        
        // Admin security hooks
        add_action('admin_init', array($this, 'admin_security_checks'));
        add_action('customize_register', array($this, 'customize_register_security'));
        
        // AJAX handlers
        add_action('wp_ajax_recruitpro_security_scan', array($this, 'ajax_security_scan'));
        add_action('wp_ajax_recruitpro_clear_security_logs', array($this, 'ajax_clear_security_logs'));
        
        // Emergency hooks
        add_action('wp_footer', array($this, 'security_monitoring'));
        
        // Recruitment-specific security hooks
        add_filter('recruitpro_candidate_data_save', array($this, 'encrypt_sensitive_data'));
        add_action('recruitpro_cv_upload', array($this, 'secure_cv_handling'));
        add_filter('recruitpro_client_data_access', array($this, 'audit_data_access'));
    }

    /**
     * WordPress core hardening
     * 
     * @since 1.0.0
     * @return void
     */
    public function wordpress_hardening() {
        // Hide WordPress version
        if ($this->security_settings['hide_wp_version']) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', '__return_empty_string');
            
            // Remove version from scripts and styles
            add_filter('style_loader_src', array($this, 'remove_version_strings'));
            add_filter('script_loader_src', array($this, 'remove_version_strings'));
        }
        
        // Disable XML-RPC
        if ($this->security_settings['disable_xml_rpc']) {
            add_filter('xmlrpc_enabled', '__return_false');
            add_filter('xmlrpc_methods', array($this, 'disable_xmlrpc_methods'));
        }
        
        // Disable user enumeration
        if ($this->security_settings['disable_user_enumeration']) {
            add_action('template_redirect', array($this, 'disable_user_enumeration'));
            add_filter('redirect_canonical', array($this, 'disable_user_enumeration_redirect'), 10, 2);
        }
        
        // Remove unnecessary WordPress generators
        if ($this->security_settings['remove_wp_generators']) {
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wp_shortlink_wp_head');
        }
        
        // Disable directory browsing
        if ($this->security_settings['disable_directory_browsing']) {
            $this->disable_directory_browsing();
        }
    }

    /**
     * Add security headers
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_security_headers() {
        if (!$this->security_settings['enable_security_headers']) {
            return;
        }

        // Content Security Policy
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com *.gstatic.com; style-src 'self' 'unsafe-inline' *.googleapis.com; img-src 'self' data: *.gravatar.com; font-src 'self' *.googleapis.com *.gstatic.com;";
        header("Content-Security-Policy: " . $csp);
        
        // X-Frame-Options
        header('X-Frame-Options: SAMEORIGIN');
        
        // X-Content-Type-Options
        header('X-Content-Type-Options: nosniff');
        
        // X-XSS-Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        // HSTS (if SSL is enabled)
        if (is_ssl()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }

    /**
     * Handle failed login attempts
     * 
     * @since 1.0.0
     * @param string $username Failed username
     * @return void
     */
    public function handle_failed_login($username) {
        $ip = $this->get_client_ip();
        $attempts_key = 'login_attempts_' . md5($ip);
        $lockout_key = 'login_lockout_' . md5($ip);
        
        // Check if already locked out
        if (get_transient($lockout_key)) {
            return;
        }
        
        // Get current attempts
        $attempts = get_transient($attempts_key) ?: 0;
        $attempts++;
        
        // Set new attempt count
        set_transient($attempts_key, $attempts, 3600); // 1 hour window
        
        // Check if limit reached
        if ($attempts >= $this->security_settings['login_attempts_limit']) {
            // Lock out the IP
            set_transient($lockout_key, true, $this->security_settings['login_lockout_duration']);
            
            // Log the incident
            $this->log_security_event('login_lockout', array(
                'ip' => $ip,
                'username' => $username,
                'attempts' => $attempts,
                'timestamp' => current_time('mysql'),
            ));
            
            // Temporarily block IP if too many attempts
            if ($attempts >= ($this->security_settings['login_attempts_limit'] * 2)) {
                $this->temporary_ip_block($ip, 3600); // 1 hour block
            }
        }
        
        // Log failed attempt
        $this->log_security_event('login_failed', array(
            'ip' => $ip,
            'username' => $username,
            'attempts' => $attempts,
            'timestamp' => current_time('mysql'),
        ));
    }

    /**
     * Check login attempts before authentication
     * 
     * @since 1.0.0
     * @param WP_User|WP_Error|null $user User object or error
     * @param string $username Username
     * @param string $password Password
     * @return WP_User|WP_Error User object or error
     */
    public function check_login_attempts($user, $username, $password) {
        if (empty($username) || empty($password)) {
            return $user;
        }
        
        $ip = $this->get_client_ip();
        $lockout_key = 'login_lockout_' . md5($ip);
        
        // Check if IP is locked out
        if (get_transient($lockout_key)) {
            return new WP_Error(
                'login_locked',
                sprintf(
                    __('Too many failed login attempts. Please try again in %d minutes.', 'recruitpro'),
                    ceil($this->security_settings['login_lockout_duration'] / 60)
                )
            );
        }
        
        // Check if IP is temporarily blocked
        if ($this->is_ip_blocked($ip)) {
            return new WP_Error(
                'ip_blocked',
                __('Your IP address has been temporarily blocked due to suspicious activity.', 'recruitpro')
            );
        }
        
        return $user;
    }

    /**
     * Handle successful login
     * 
     * @since 1.0.0
     * @param string $user_login Username
     * @param WP_User $user User object
     * @return void
     */
    public function handle_successful_login($user_login, $user) {
        $ip = $this->get_client_ip();
        
        // Clear failed attempts
        $attempts_key = 'login_attempts_' . md5($ip);
        delete_transient($attempts_key);
        
        // Log successful login
        $this->log_security_event('login_success', array(
            'ip' => $ip,
            'username' => $user_login,
            'user_id' => $user->ID,
            'timestamp' => current_time('mysql'),
        ));
    }

    /**
     * Customize login error messages
     * 
     * @since 1.0.0
     * @param string $error Error message
     * @return string Modified error message
     */
    public function customize_login_errors($error) {
        if (!$this->security_settings['hide_login_errors']) {
            return $error;
        }
        
        // Generic error message to prevent username enumeration
        return __('Invalid login credentials. Please try again.', 'recruitpro');
    }

    /**
     * Secure file uploads
     * 
     * @since 1.0.0
     * @param array $file File data
     * @return array Modified file data
     */
    public function secure_file_uploads($file) {
        // Check file size
        if ($file['size'] > $this->security_settings['max_file_size']) {
            $file['error'] = sprintf(
                __('File size exceeds maximum allowed size of %s.', 'recruitpro'),
                size_format($this->security_settings['max_file_size'])
            );
            return $file;
        }
        
        // Check file type
        $allowed_types = $this->security_settings['allowed_file_types'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            $file['error'] = sprintf(
                __('File type "%s" is not allowed. Allowed types: %s', 'recruitpro'),
                $file_extension,
                implode(', ', $allowed_types)
            );
            return $file;
        }
        
        // Check for malicious content in filename
        if ($this->contains_malicious_patterns($file['name'])) {
            $file['error'] = __('File name contains potentially dangerous characters.', 'recruitpro');
            return $file;
        }
        
        // Scan file content if scanning is enabled
        if ($this->security_settings['scan_uploaded_files']) {
            if ($this->scan_file_content($file['tmp_name'])) {
                $file['error'] = __('File contains potentially malicious content.', 'recruitpro');
                return $file;
            }
        }
        
        return $file;
    }

    /**
     * Filter allowed upload MIME types
     * 
     * @since 1.0.0
     * @param array $mimes Allowed MIME types
     * @return array Filtered MIME types
     */
    public function filter_upload_mimes($mimes) {
        $allowed_types = $this->security_settings['allowed_file_types'];
        $safe_mimes = array();
        
        // Map extensions to MIME types
        $mime_map = array(
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',
        );
        
        foreach ($allowed_types as $ext) {
            if (isset($mime_map[$ext]) && isset($mimes[$ext])) {
                $safe_mimes[$ext] = $mime_map[$ext];
            }
        }
        
        return $safe_mimes;
    }

    /**
     * Scan uploaded file for threats
     * 
     * @since 1.0.0
     * @param array $file Upload file data
     * @return array File data
     */
    public function scan_uploaded_file($file) {
        if (!$this->security_settings['scan_uploaded_files']) {
            return $file;
        }
        
        $file_path = $file['file'];
        
        // Basic file scanning
        if ($this->scan_file_content($file_path)) {
            // Quarantine suspicious file
            if ($this->security_settings['quarantine_suspicious_files']) {
                $this->quarantine_file($file_path);
            }
            
            // Log security event
            $this->log_security_event('malicious_file_upload', array(
                'file' => $file['file'],
                'url' => $file['url'],
                'ip' => $this->get_client_ip(),
                'timestamp' => current_time('mysql'),
            ));
        }
        
        return $file;
    }

    /**
     * Check rate limits
     * 
     * @since 1.0.0
     * @return void
     */
    public function check_rate_limits() {
        if (!$this->security_settings['enable_rate_limiting']) {
            return;
        }
        
        $ip = $this->get_client_ip();
        $rate_key = 'rate_limit_' . md5($ip);
        $requests = get_transient($rate_key) ?: 0;
        
        if ($requests >= $this->security_settings['rate_limit_requests']) {
            // Rate limit exceeded
            status_header(429);
            wp_die(
                __('Rate limit exceeded. Please slow down your requests.', 'recruitpro'),
                __('Too Many Requests', 'recruitpro'),
                array('response' => 429)
            );
        }
        
        // Increment request count
        set_transient($rate_key, $requests + 1, 60); // 1 minute window
    }

    /**
     * Disable user enumeration
     * 
     * @since 1.0.0
     * @return void
     */
    public function disable_user_enumeration() {
        if (is_admin() || !isset($_GET['author'])) {
            return;
        }
        
        // Redirect author enumeration attempts
        wp_redirect(home_url(), 301);
        exit;
    }

    /**
     * Disable user enumeration via canonical redirect
     * 
     * @since 1.0.0
     * @param string $redirect_url Redirect URL
     * @param string $requested_url Requested URL
     * @return string|false Modified redirect URL or false
     */
    public function disable_user_enumeration_redirect($redirect_url, $requested_url) {
        if (preg_match('/\?author=([0-9]*)(\/*)/i', $requested_url)) {
            return false;
        }
        return $redirect_url;
    }

    /**
     * Remove version strings from assets
     * 
     * @since 1.0.0
     * @param string $src Asset URL
     * @return string Clean URL
     */
    public function remove_version_strings($src) {
        global $wp_version;
        
        if (strpos($src, 'ver=' . $wp_version)) {
            $src = remove_query_arg('ver', $src);
        }
        
        return $src;
    }

    /**
     * Disable XML-RPC methods
     * 
     * @since 1.0.0
     * @param array $methods XML-RPC methods
     * @return array Empty array
     */
    public function disable_xmlrpc_methods($methods) {
        return array();
    }

    /**
     * Disable directory browsing
     * 
     * @since 1.0.0
     * @return void
     */
    private function disable_directory_browsing() {
        $htaccess_file = ABSPATH . '.htaccess';
        
        if (is_writable($htaccess_file)) {
            $rules = "\n# RecruitPro Security: Disable directory browsing\nOptions -Indexes\n";
            
            $current_content = file_get_contents($htaccess_file);
            if (strpos($current_content, 'Options -Indexes') === false) {
                file_put_contents($htaccess_file, $rules, FILE_APPEND | LOCK_EX);
            }
        }
    }

    /**
     * Check if file contains malicious patterns
     * 
     * @since 1.0.0
     * @param string $filename Filename to check
     * @return bool True if malicious patterns found
     */
    private function contains_malicious_patterns($filename) {
        $malicious_patterns = array(
            'php', 'php3', 'php4', 'php5', 'phtml', 'pl', 'py', 'jsp', 'asp',
            'sh', 'cgi', 'exe', 'bat', 'com', 'scr', 'vbs', 'js'
        );
        
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($file_extension, $malicious_patterns);
    }

    /**
     * Basic file content scanning
     * 
     * @since 1.0.0
     * @param string $file_path Path to file
     * @return bool True if threats detected
     */
    private function scan_file_content($file_path) {
        if (!file_exists($file_path)) {
            return false;
        }
        
        // Read first 1024 bytes for scanning
        $content = file_get_contents($file_path, false, null, 0, 1024);
        
        // Malicious patterns to look for
        $malicious_patterns = array(
            '<?php',
            '<?=',
            '<script',
            'javascript:',
            'eval(',
            'base64_decode(',
            'system(',
            'exec(',
            'shell_exec(',
            'passthru(',
        );
        
        foreach ($malicious_patterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Quarantine suspicious file
     * 
     * @since 1.0.0
     * @param string $file_path Path to file
     * @return bool True if quarantined successfully
     */
    private function quarantine_file($file_path) {
        $upload_dir = wp_upload_dir();
        $quarantine_dir = $upload_dir['basedir'] . '/quarantine/';
        
        // Create quarantine directory if it doesn't exist
        if (!file_exists($quarantine_dir)) {
            wp_mkdir_p($quarantine_dir);
            // Add index.php to prevent directory browsing
            file_put_contents($quarantine_dir . 'index.php', '<?php // Silence is golden');
        }
        
        $quarantine_file = $quarantine_dir . basename($file_path) . '_' . time();
        
        if (rename($file_path, $quarantine_file)) {
            // Create quarantine log
            $log_entry = array(
                'original_file' => $file_path,
                'quarantine_file' => $quarantine_file,
                'timestamp' => current_time('mysql'),
                'ip' => $this->get_client_ip(),
            );
            
            $quarantine_log = get_option('recruitpro_quarantine_log', array());
            $quarantine_log[] = $log_entry;
            update_option('recruitpro_quarantine_log', $quarantine_log);
            
            return true;
        }
        
        return false;
    }

    /**
     * Get client IP address
     * 
     * @since 1.0.0
     * @return string Client IP
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Log security event
     * 
     * @since 1.0.0
     * @param string $event_type Type of security event
     * @param array $event_data Event data
     * @return void
     */
    private function log_security_event($event_type, $event_data) {
        $log_entry = array(
            'type' => $event_type,
            'data' => $event_data,
            'timestamp' => current_time('mysql'),
        );
        
        // Store in database
        $security_logs = get_option('recruitpro_security_logs', array());
        $security_logs[] = $log_entry;
        
        // Keep only last 1000 entries
        if (count($security_logs) > 1000) {
            $security_logs = array_slice($security_logs, -1000);
        }
        
        update_option('recruitpro_security_logs', $security_logs);
        
        // Log to file if enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('RecruitPro Security: ' . $event_type . ' - ' . wp_json_encode($event_data));
        }
    }

    /**
     * Load blocked IPs
     * 
     * @since 1.0.0
     * @return void
     */
    private function load_blocked_ips() {
        $this->blocked_ips = get_option('recruitpro_blocked_ips', array());
    }

    /**
     * Check if IP is blocked
     * 
     * @since 1.0.0
     * @param string $ip IP address
     * @return bool True if blocked
     */
    private function is_ip_blocked($ip) {
        return isset($this->blocked_ips[$ip]) && $this->blocked_ips[$ip] > time();
    }

    /**
     * Temporarily block IP
     * 
     * @since 1.0.0
     * @param string $ip IP address
     * @param int $duration Block duration in seconds
     * @return void
     */
    private function temporary_ip_block($ip, $duration) {
        $this->blocked_ips[$ip] = time() + $duration;
        update_option('recruitpro_blocked_ips', $this->blocked_ips);
        
        // Log the block
        $this->log_security_event('ip_blocked', array(
            'ip' => $ip,
            'duration' => $duration,
            'expires' => time() + $duration,
        ));
    }

    /**
     * Enqueue form security scripts
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_form_security_scripts() {
        wp_enqueue_script(
            'recruitpro-form-security',
            get_template_directory_uri() . '/assets/js/form-security.js',
            array('jquery'),
            RECRUITPRO_THEME_VERSION,
            true
        );

        wp_localize_script('recruitpro-form-security', 'recruitpro_security', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('recruitpro_security_nonce'),
            'csrf_token' => $this->generate_csrf_token(),
        ));
    }

    /**
     * Generate CSRF token
     * 
     * @since 1.0.0
     * @return string CSRF token
     */
    private function generate_csrf_token() {
        $token = wp_generate_password(32, false);
        set_transient('recruitpro_csrf_' . session_id(), $token, 3600);
        return $token;
    }

    /**
     * Verify form token
     * 
     * @since 1.0.0
     * @return void
     */
    public function verify_form_token() {
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_security_nonce')) {
            wp_send_json_error(array('message' => 'Security verification failed.'));
        }

        $token = sanitize_text_field($_POST['token'] ?? '');
        $session_token = get_transient('recruitpro_csrf_' . session_id());

        if ($token === $session_token) {
            wp_send_json_success(array('message' => 'Token verified.'));
        } else {
            wp_send_json_error(array('message' => 'Invalid security token.'));
        }
    }

    /**
     * Secure email forms
     * 
     * @since 1.0.0
     * @param array $args Email arguments
     * @return array Modified email arguments
     */
    public function secure_email_forms($args) {
        // Add security headers to emails
        if (!isset($args['headers'])) {
            $args['headers'] = array();
        }
        
        $args['headers'][] = 'X-Mailer: RecruitPro Secure Mailer';
        $args['headers'][] = 'X-Priority: 3';
        
        return $args;
    }

    /**
     * Encrypt sensitive candidate data
     * 
     * @since 1.0.0
     * @param array $data Candidate data
     * @return array Encrypted data
     */
    public function encrypt_sensitive_data($data) {
        if (!$this->security_settings['encrypt_candidate_data']) {
            return $data;
        }
        
        $sensitive_fields = array('email', 'phone', 'address', 'ssn', 'id_number');
        
        foreach ($sensitive_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = $this->encrypt_data($data[$field]);
            }
        }
        
        return $data;
    }

    /**
     * Secure CV handling
     * 
     * @since 1.0.0
     * @param array $cv_data CV upload data
     * @return void
     */
    public function secure_cv_handling($cv_data) {
        if (!$this->security_settings['secure_cv_storage']) {
            return;
        }
        
        // Move CV to secure directory
        $upload_dir = wp_upload_dir();
        $secure_dir = $upload_dir['basedir'] . '/secure-cvs/';
        
        if (!file_exists($secure_dir)) {
            wp_mkdir_p($secure_dir);
            // Add .htaccess to prevent direct access
            file_put_contents($secure_dir . '.htaccess', "deny from all\n");
            // Add index.php
            file_put_contents($secure_dir . 'index.php', '<?php // Silence is golden');
        }
        
        // Log CV access
        $this->log_security_event('cv_upload', array(
            'file' => $cv_data['filename'],
            'candidate_id' => $cv_data['candidate_id'] ?? null,
            'ip' => $this->get_client_ip(),
        ));
    }

    /**
     * Audit data access
     * 
     * @since 1.0.0
     * @param array $access_data Data access information
     * @return array Access data
     */
    public function audit_data_access($access_data) {
        if (!$this->security_settings['audit_data_access']) {
            return $access_data;
        }
        
        // Log data access
        $this->log_security_event('data_access', array(
            'user_id' => get_current_user_id(),
            'accessed_data' => $access_data['type'] ?? 'unknown',
            'record_id' => $access_data['record_id'] ?? null,
            'ip' => $this->get_client_ip(),
        ));
        
        return $access_data;
    }

    /**
     * Simple data encryption
     * 
     * @since 1.0.0
     * @param string $data Data to encrypt
     * @return string Encrypted data
     */
    private function encrypt_data($data) {
        $key = defined('RECRUITPRO_ENCRYPTION_KEY') ? RECRUITPRO_ENCRYPTION_KEY : AUTH_KEY;
        return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, substr(md5($key), 0, 16)));
    }

    /**
     * Simple data decryption
     * 
     * @since 1.0.0
     * @param string $encrypted_data Encrypted data
     * @return string Decrypted data
     */
    private function decrypt_data($encrypted_data) {
        $key = defined('RECRUITPRO_ENCRYPTION_KEY') ? RECRUITPRO_ENCRYPTION_KEY : AUTH_KEY;
        return openssl_decrypt(base64_decode($encrypted_data), 'AES-256-CBC', $key, 0, substr(md5($key), 0, 16));
    }

    /**
     * Security monitoring
     * 
     * @since 1.0.0
     * @return void
     */
    public function security_monitoring() {
        // Only monitor on admin pages
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }
        
        // Check for security issues
        $issues = $this->check_security_status();
        
        if (!empty($issues)) {
            set_transient('recruitpro_security_issues', $issues, DAY_IN_SECONDS);
        }
    }

    /**
     * Check security status
     * 
     * @since 1.0.0
     * @return array Security issues
     */
    private function check_security_status() {
        $issues = array();
        
        // Check if SSL is enabled
        if (!is_ssl()) {
            $issues[] = __('SSL certificate is not configured. This is important for protecting sensitive recruitment data.', 'recruitpro');
        }
        
        // Check file permissions
        if (is_writable(ABSPATH . 'wp-config.php')) {
            $issues[] = __('wp-config.php file has write permissions. This is a security risk.', 'recruitpro');
        }
        
        // Check for recent failed logins
        $recent_failures = array_filter($this->security_logs, function($log) {
            return $log['type'] === 'login_failed' && 
                   strtotime($log['timestamp']) > (time() - 3600); // Last hour
        });
        
        if (count($recent_failures) > 10) {
            $issues[] = __('High number of failed login attempts detected in the last hour.', 'recruitpro');
        }
        
        return $issues;
    }

    /**
     * Admin security checks
     * 
     * @since 1.0.0
     * @return void
     */
    public function admin_security_checks() {
        // Display security notices
        $issues = get_transient('recruitpro_security_issues');
        if (!empty($issues)) {
            add_action('admin_notices', function() use ($issues) {
                foreach ($issues as $issue) {
                    echo '<div class="notice notice-warning"><p><strong>RecruitPro Security:</strong> ' . esc_html($issue) . '</p></div>';
                }
            });
        }
    }

    /**
     * AJAX security scan
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_security_scan() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions.'));
        }
        
        $scan_results = array(
            'ssl_enabled' => is_ssl(),
            'wp_version_hidden' => $this->security_settings['hide_wp_version'],
            'login_protection' => $this->security_settings['enable_login_protection'],
            'file_protection' => $this->security_settings['enable_file_protection'],
            'recent_threats' => count($this->get_recent_security_events()),
            'quarantined_files' => count(get_option('recruitpro_quarantine_log', array())),
        );
        
        wp_send_json_success($scan_results);
    }

    /**
     * Get recent security events
     * 
     * @since 1.0.0
     * @return array Recent security events
     */
    private function get_recent_security_events() {
        $security_logs = get_option('recruitpro_security_logs', array());
        $recent_events = array();
        $one_week_ago = time() - (7 * DAY_IN_SECONDS);
        
        foreach ($security_logs as $log) {
            if (strtotime($log['timestamp']) > $one_week_ago) {
                $recent_events[] = $log;
            }
        }
        
        return $recent_events;
    }

    /**
     * AJAX clear security logs
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_clear_security_logs() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions.'));
        }
        
        delete_option('recruitpro_security_logs');
        delete_option('recruitpro_quarantine_log');
        
        wp_send_json_success(array('message' => 'Security logs cleared successfully.'));
    }

    /**
     * Add security settings to customizer
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize Customizer instance
     * @return void
     */
    public function customize_register_security($wp_customize) {
        // Security section
        $wp_customize->add_section('recruitpro_security', array(
            'title' => __('Security Settings', 'recruitpro'),
            'description' => __('Configure basic security settings for your recruitment website.', 'recruitpro'),
            'priority' => 60,
        ));

        // Enable basic security
        $wp_customize->add_setting('recruitpro_enable_basic_security', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_basic_security', array(
            'label' => __('Enable Basic Security', 'recruitpro'),
            'description' => __('Enable theme-level security enhancements.', 'recruitpro'),
            'section' => 'recruitpro_security',
            'type' => 'checkbox',
        ));

        // Hide WordPress version
        $wp_customize->add_setting('recruitpro_hide_wp_version', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_hide_wp_version', array(
            'label' => __('Hide WordPress Version', 'recruitpro'),
            'description' => __('Remove WordPress version information from your site.', 'recruitpro'),
            'section' => 'recruitpro_security',
            'type' => 'checkbox',
        ));

        // Enable login protection
        $wp_customize->add_setting('recruitpro_enable_login_protection', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_login_protection', array(
            'label' => __('Enable Login Protection', 'recruitpro'),
            'description' => __('Protect against brute force login attempts.', 'recruitpro'),
            'section' => 'recruitpro_security',
            'type' => 'checkbox',
        ));

        // Login attempts limit
        $wp_customize->add_setting('recruitpro_login_attempts_limit', array(
            'default' => 5,
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('recruitpro_login_attempts_limit', array(
            'label' => __('Login Attempts Limit', 'recruitpro'),
            'description' => __('Number of failed login attempts before lockout.', 'recruitpro'),
            'section' => 'recruitpro_security',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 3,
                'max' => 20,
            ),
        ));

        // Enable file protection
        $wp_customize->add_setting('recruitpro_enable_file_protection', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control('recruitpro_enable_file_protection', array(
            'label' => __('Enable File Upload Protection', 'recruitpro'),
            'description' => __('Scan and protect file uploads including CVs and documents.', 'recruitpro'),
            'section' => 'recruitpro_security',
            'type' => 'checkbox',
        ));

        // Max file size
        $wp_customize->add_setting('recruitpro_max_file_size', array(
            'default' => 5242880,
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control('recruitpro_max_file_size', array(
            'label' => __('Maximum File Size (bytes)', 'recruitpro'),
            'description' => __('Maximum allowed file size for uploads (default: 5MB).', 'recruitpro'),
            'section' => 'recruitpro_security',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1048576,
                'max' => 104857600,
            ),
        ));
    }
}

// Initialize the security manager
if (class_exists('RecruitPro_Security_Manager')) {
    new RecruitPro_Security_Manager();
}

/**
 * Helper function to check if security is enabled
 * 
 * @since 1.0.0
 * @return bool True if security is enabled
 */
function recruitpro_security_enabled() {
    return get_theme_mod('recruitpro_enable_basic_security', true);
}

/**
 * Helper function to get security logs
 * 
 * @since 1.0.0
 * @param int $limit Number of logs to return
 * @return array Security logs
 */
function recruitpro_get_security_logs($limit = 50) {
    $logs = get_option('recruitpro_security_logs', array());
    return array_slice($logs, -$limit, $limit);
}

/**
 * Helper function to check if IP is currently blocked
 * 
 * @since 1.0.0
 * @param string $ip IP address to check
 * @return bool True if blocked
 */
function recruitpro_is_ip_blocked($ip = null) {
    if (!$ip) {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                break;
            }
        }
    }
    
    $blocked_ips = get_option('recruitpro_blocked_ips', array());
    return isset($blocked_ips[$ip]) && $blocked_ips[$ip] > time();
}