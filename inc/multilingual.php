<?php
/**
 * RecruitPro Theme Multilingual Support
 *
 * Comprehensive multilingual support system for the RecruitPro recruitment theme.
 * Provides language switching, RTL support, translation integration, and
 * recruitment-specific multilingual features for international agencies.
 *
 * @package RecruitPro
 * @subpackage Theme/Multilingual
 * @since 1.0.0
 * @author RecruitPro Team
 * @copyright 2024 RecruitPro Team
 * @license GPL v2 or later
 *
 * File: /recruitpro-theme/inc/multilingual.php
 * Purpose: Theme-level multilingual support (basic implementation)
 * Dependencies: WordPress core, SEO plugin (for advanced features)
 * Conflicts: None (designed to work with translation plugins)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RecruitPro Multilingual Manager Class
 * 
 * Handles basic multilingual functionality for the theme.
 * Advanced features like comprehensive language lists and hreflang
 * management are handled by the SEO plugin to avoid conflicts.
 *
 * @since 1.0.0
 */
class RecruitPro_Multilingual_Manager {

    /**
     * Supported languages (basic theme-level support)
     * 
     * @since 1.0.0
     * @var array
     */
    private $basic_languages = array();

    /**
     * RTL languages
     * 
     * @since 1.0.0
     * @var array
     */
    private $rtl_languages = array();

    /**
     * Current language information
     * 
     * @since 1.0.0
     * @var array
     */
    private $current_language = array();

    /**
     * Multilingual settings
     * 
     * @since 1.0.0
     * @var array
     */
    private $multilingual_settings = array();

    /**
     * Translation plugins compatibility
     * 
     * @since 1.0.0
     * @var array
     */
    private $plugin_compatibility = array();

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_basic_languages();
        $this->init_rtl_languages();
        $this->init_multilingual_settings();
        $this->detect_current_language();
        $this->check_plugin_compatibility();
        
        // Core multilingual hooks
        add_action('init', array($this, 'setup_multilingual_support'));
        add_action('wp_head', array($this, 'output_language_meta_tags'), 5);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_language_assets'));
        
        // Language detection and switching
        add_filter('body_class', array($this, 'add_language_body_classes'));
        add_filter('language_attributes', array($this, 'add_language_attributes'));
        add_action('wp_head', array($this, 'output_rtl_styles'), 20);
        
        // Content and menu modifications
        add_filter('wp_nav_menu_args', array($this, 'localize_menu_args'));
        add_filter('the_content', array($this, 'optimize_multilingual_content'));
        add_filter('get_search_form', array($this, 'localize_search_form'));
        
        // Theme customization
        add_action('customize_register', array($this, 'add_multilingual_customizer_options'));
        add_filter('theme_mod_blogdescription', array($this, 'get_localized_tagline'));
        
        // Template hooks
        add_action('recruitpro_header', array($this, 'display_language_switcher'), 15);
        add_action('recruitpro_footer', array($this, 'display_footer_language_info'), 25);
        
        // Translation plugin integration
        add_action('plugins_loaded', array($this, 'integrate_translation_plugins'));
        
        // AJAX handlers
        add_action('wp_ajax_recruitpro_switch_language', array($this, 'ajax_switch_language'));
        add_action('wp_ajax_nopriv_recruitpro_switch_language', array($this, 'ajax_switch_language'));
        
        // Admin integration
        if (is_admin()) {
            add_action('admin_init', array($this, 'register_translatable_strings'));
            add_action('admin_menu', array($this, 'add_multilingual_admin_page'));
        }
    }

    /**
     * Initialize basic languages (theme-level fallback)
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_basic_languages() {
        
        // Basic language support (theme-level fallback)
        // Full language list is managed by the SEO plugin
        $this->basic_languages = array(
            'en' => array(
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en',
                'locale' => 'en_US',
                'hreflang' => 'en',
                'flag' => '🇺🇸',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Job',
                    'jobs' => 'Jobs',
                    'career' => 'Career',
                    'careers' => 'Careers',
                    'apply' => 'Apply',
                    'candidate' => 'Candidate',
                    'recruiter' => 'Recruiter',
                    'company' => 'Company',
                ),
            ),
            'fr' => array(
                'name' => 'French',
                'native_name' => 'Français',
                'code' => 'fr',
                'locale' => 'fr_FR',
                'hreflang' => 'fr',
                'flag' => '🇫🇷',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Emploi',
                    'jobs' => 'Emplois',
                    'career' => 'Carrière',
                    'careers' => 'Carrières',
                    'apply' => 'Postuler',
                    'candidate' => 'Candidat',
                    'recruiter' => 'Recruteur',
                    'company' => 'Entreprise',
                ),
            ),
            'de' => array(
                'name' => 'German',
                'native_name' => 'Deutsch',
                'code' => 'de',
                'locale' => 'de_DE',
                'hreflang' => 'de',
                'flag' => '🇩🇪',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Stelle',
                    'jobs' => 'Stellen',
                    'career' => 'Karriere',
                    'careers' => 'Karrieren',
                    'apply' => 'Bewerben',
                    'candidate' => 'Kandidat',
                    'recruiter' => 'Personalvermittler',
                    'company' => 'Unternehmen',
                ),
            ),
            'es' => array(
                'name' => 'Spanish',
                'native_name' => 'Español',
                'code' => 'es',
                'locale' => 'es_ES',
                'hreflang' => 'es',
                'flag' => '🇪🇸',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Trabajo',
                    'jobs' => 'Trabajos',
                    'career' => 'Carrera',
                    'careers' => 'Carreras',
                    'apply' => 'Aplicar',
                    'candidate' => 'Candidato',
                    'recruiter' => 'Reclutador',
                    'company' => 'Empresa',
                ),
            ),
            'it' => array(
                'name' => 'Italian',
                'native_name' => 'Italiano',
                'code' => 'it',
                'locale' => 'it_IT',
                'hreflang' => 'it',
                'flag' => '🇮🇹',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Lavoro',
                    'jobs' => 'Lavori',
                    'career' => 'Carriera',
                    'careers' => 'Carriere',
                    'apply' => 'Candidati',
                    'candidate' => 'Candidato',
                    'recruiter' => 'Reclutatore',
                    'company' => 'Azienda',
                ),
            ),
            'pt' => array(
                'name' => 'Portuguese',
                'native_name' => 'Português',
                'code' => 'pt',
                'locale' => 'pt_PT',
                'hreflang' => 'pt',
                'flag' => '🇵🇹',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Emprego',
                    'jobs' => 'Empregos',
                    'career' => 'Carreira',
                    'careers' => 'Carreiras',
                    'apply' => 'Candidatar',
                    'candidate' => 'Candidato',
                    'recruiter' => 'Recrutador',
                    'company' => 'Empresa',
                ),
            ),
            'nl' => array(
                'name' => 'Dutch',
                'native_name' => 'Nederlands',
                'code' => 'nl',
                'locale' => 'nl_NL',
                'hreflang' => 'nl',
                'flag' => '🇳🇱',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Baan',
                    'jobs' => 'Banen',
                    'career' => 'Carrière',
                    'careers' => 'Carrières',
                    'apply' => 'Solliciteren',
                    'candidate' => 'Kandidaat',
                    'recruiter' => 'Recruiter',
                    'company' => 'Bedrijf',
                ),
            ),
            'ro' => array(
                'name' => 'Romanian',
                'native_name' => 'Română',
                'code' => 'ro',
                'locale' => 'ro_RO',
                'hreflang' => 'ro',
                'flag' => '🇷🇴',
                'direction' => 'ltr',
                'charset' => 'UTF-8',
                'recruitment_terms' => array(
                    'job' => 'Loc de muncă',
                    'jobs' => 'Locuri de muncă',
                    'career' => 'Carieră',
                    'careers' => 'Cariere',
                    'apply' => 'Aplică',
                    'candidate' => 'Candidat',
                    'recruiter' => 'Recrutor',
                    'company' => 'Companie',
                ),
            ),
        );
        
        // Allow SEO plugin to extend the language list
        $this->basic_languages = apply_filters('recruitpro_basic_languages', $this->basic_languages);
    }

    /**
     * Initialize RTL languages
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_rtl_languages() {
        
        $this->rtl_languages = array(
            'ar' => array(
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'code' => 'ar',
                'locale' => 'ar',
                'hreflang' => 'ar',
                'flag' => '🇸🇦',
                'direction' => 'rtl',
                'charset' => 'UTF-8',
                'font_family' => '"Noto Sans Arabic", "Amiri", "Cairo", "Tajawal", sans-serif',
                'recruitment_terms' => array(
                    'job' => 'وظيفة',
                    'jobs' => 'وظائف',
                    'career' => 'مهنة',
                    'careers' => 'مهن',
                    'apply' => 'تقدم',
                    'candidate' => 'مرشح',
                    'recruiter' => 'موظف توظيف',
                    'company' => 'شركة',
                ),
            ),
            'he' => array(
                'name' => 'Hebrew',
                'native_name' => 'עברית',
                'code' => 'he',
                'locale' => 'he_IL',
                'hreflang' => 'he',
                'flag' => '🇮🇱',
                'direction' => 'rtl',
                'charset' => 'UTF-8',
                'font_family' => '"Noto Sans Hebrew", "Frank Ruehl CLM", "SBL Hebrew", sans-serif',
                'recruitment_terms' => array(
                    'job' => 'עבודה',
                    'jobs' => 'עבודות',
                    'career' => 'קריירה',
                    'careers' => 'קריירות',
                    'apply' => 'להגיש מועמדות',
                    'candidate' => 'מועמד',
                    'recruiter' => 'מגייס',
                    'company' => 'חברה',
                ),
            ),
            'fa' => array(
                'name' => 'Persian',
                'native_name' => 'فارسی',
                'code' => 'fa',
                'locale' => 'fa_IR',
                'hreflang' => 'fa',
                'flag' => '🇮🇷',
                'direction' => 'rtl',
                'charset' => 'UTF-8',
                'font_family' => '"Noto Sans Persian", "Vazir", "Shabnam", sans-serif',
                'recruitment_terms' => array(
                    'job' => 'شغل',
                    'jobs' => 'مشاغل',
                    'career' => 'شغل',
                    'careers' => 'مشاغل',
                    'apply' => 'درخواست',
                    'candidate' => 'نامزد',
                    'recruiter' => 'استخدام کننده',
                    'company' => 'شرکت',
                ),
            ),
            'ur' => array(
                'name' => 'Urdu',
                'native_name' => 'اردو',
                'code' => 'ur',
                'locale' => 'ur_PK',
                'hreflang' => 'ur',
                'flag' => '🇵🇰',
                'direction' => 'rtl',
                'charset' => 'UTF-8',
                'font_family' => '"Noto Sans Urdu", "Jameel Noori Nastaleeq", sans-serif',
                'recruitment_terms' => array(
                    'job' => 'ملازمت',
                    'jobs' => 'ملازمتیں',
                    'career' => 'کیریئر',
                    'careers' => 'کیریئرز',
                    'apply' => 'درخواست',
                    'candidate' => 'امیدوار',
                    'recruiter' => 'بھرتی کنندہ',
                    'company' => 'کمپنی',
                ),
            ),
        );
        
        // Merge RTL languages into basic languages
        $this->basic_languages = array_merge($this->basic_languages, $this->rtl_languages);
    }

    /**
     * Initialize multilingual settings
     * 
     * @since 1.0.0
     * @return void
     */
    private function init_multilingual_settings() {
        
        $this->multilingual_settings = array(
            'enable_multilingual' => get_theme_mod('recruitpro_enable_multilingual', true),
            'default_language' => get_theme_mod('recruitpro_default_language', 'en'),
            'show_language_switcher' => get_theme_mod('recruitpro_show_language_switcher', true),
            'language_switcher_style' => get_theme_mod('recruitpro_language_switcher_style', 'dropdown'),
            'show_flag_icons' => get_theme_mod('recruitpro_show_flag_icons', true),
            'show_language_names' => get_theme_mod('recruitpro_show_language_names', true),
            'rtl_optimization' => get_theme_mod('recruitpro_rtl_optimization', true),
            'auto_detect_language' => get_theme_mod('recruitpro_auto_detect_language', false),
            'fallback_language' => get_theme_mod('recruitpro_fallback_language', 'en'),
            'translate_job_terms' => get_theme_mod('recruitpro_translate_job_terms', true),
            'seo_integration' => get_theme_mod('recruitpro_seo_integration', true),
            'hreflang_management' => get_theme_mod('recruitpro_hreflang_management', 'seo_plugin'),
        );
    }

    /**
     * Detect current language
     * 
     * @since 1.0.0
     * @return void
     */
    private function detect_current_language() {
        
        // Try to get from SEO plugin first (this is the authoritative source)
        if (function_exists('recruitpro_seo_get_current_language') && $this->multilingual_settings['seo_integration']) {
            $seo_language = recruitpro_seo_get_current_language();
            if ($seo_language) {
                $this->current_language = $seo_language;
                return;
            }
        }
        
        // Try to get from translation plugins
        $plugin_language = $this->get_language_from_plugins();
        if ($plugin_language) {
            $this->current_language = $plugin_language;
            return;
        }
        
        // Fallback detection methods
        $detected_language = $this->detect_language_fallback();
        $this->current_language = $detected_language;
    }

    /**
     * Detect language using fallback methods
     * 
     * @since 1.0.0
     * @return array Language information
     */
    private function detect_language_fallback() {
        
        $default_lang = $this->multilingual_settings['default_language'];
        
        // Method 1: WordPress locale
        $wp_locale = get_locale();
        $lang_code = substr($wp_locale, 0, 2);
        
        if (isset($this->basic_languages[$lang_code])) {
            return $this->basic_languages[$lang_code];
        }
        
        // Method 2: URL path detection
        $request_uri = trim($_SERVER['REQUEST_URI'] ?? '', '/');
        $uri_parts = explode('/', $request_uri);
        
        if (!empty($uri_parts[0]) && strlen($uri_parts[0]) === 2) {
            $detected_lang = strtolower($uri_parts[0]);
            if (isset($this->basic_languages[$detected_lang])) {
                return $this->basic_languages[$detected_lang];
            }
        }
        
        // Method 3: Browser language detection (if auto-detect enabled)
        if ($this->multilingual_settings['auto_detect_language']) {
            $browser_lang = $this->detect_browser_language();
            if ($browser_lang && isset($this->basic_languages[$browser_lang])) {
                return $this->basic_languages[$browser_lang];
            }
        }
        
        // Method 4: User preference (for logged-in users)
        if (is_user_logged_in()) {
            $user_lang = get_user_meta(get_current_user_id(), 'recruitpro_language_preference', true);
            if ($user_lang && isset($this->basic_languages[$user_lang])) {
                return $this->basic_languages[$user_lang];
            }
        }
        
        // Fallback to default language
        return $this->basic_languages[$default_lang] ?? $this->basic_languages['en'];
    }

    /**
     * Detect browser language
     * 
     * @since 1.0.0
     * @return string|false Browser language code or false
     */
    private function detect_browser_language() {
        
        $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        
        if (empty($accept_language)) {
            return false;
        }
        
        // Parse Accept-Language header
        preg_match_all('/([a-z]{2}(-[A-Z]{2})?)\s*(;\s*q\s*=\s*([01](\.[0-9]+)?))?/', $accept_language, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $lang) {
                $lang_code = substr($lang, 0, 2);
                if (isset($this->basic_languages[$lang_code])) {
                    return $lang_code;
                }
            }
        }
        
        return false;
    }

    /**
     * Check translation plugin compatibility
     * 
     * @since 1.0.0
     * @return void
     */
    private function check_plugin_compatibility() {
        
        $this->plugin_compatibility = array(
            'wpml' => class_exists('SitePress'),
            'polylang' => function_exists('pll_languages_list'),
            'translatepress' => class_exists('TRP_Translate_Press'),
            'weglot' => class_exists('Weglot\\Client\\Client'),
            'qtranslate' => function_exists('qtranxf_getLanguage'),
            'seo_plugin' => function_exists('recruitpro_seo_get_current_language'),
        );
    }

    /**
     * Get language from translation plugins
     * 
     * @since 1.0.0
     * @return array|false Language information or false
     */
    private function get_language_from_plugins() {
        
        // WPML integration
        if ($this->plugin_compatibility['wpml']) {
            $wpml_lang = apply_filters('wpml_current_language', null);
            if ($wpml_lang && isset($this->basic_languages[$wpml_lang])) {
                return $this->basic_languages[$wpml_lang];
            }
        }
        
        // Polylang integration
        if ($this->plugin_compatibility['polylang']) {
            $polylang_lang = pll_current_language();
            if ($polylang_lang && isset($this->basic_languages[$polylang_lang])) {
                return $this->basic_languages[$polylang_lang];
            }
        }
        
        // TranslatePress integration
        if ($this->plugin_compatibility['translatepress']) {
            $trp_lang = get_locale();
            $lang_code = substr($trp_lang, 0, 2);
            if (isset($this->basic_languages[$lang_code])) {
                return $this->basic_languages[$lang_code];
            }
        }
        
        return false;
    }

    /**
     * Setup multilingual support
     * 
     * @since 1.0.0
     * @return void
     */
    public function setup_multilingual_support() {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return;
        }
        
        // Load textdomain
        load_theme_textdomain('recruitpro', get_template_directory() . '/languages');
        
        // Register translatable strings
        $this->register_theme_strings();
        
        // Setup RTL support
        if ($this->is_rtl_language($this->current_language['code'])) {
            $this->setup_rtl_support();
        }
        
        // Initialize recruitment term translations
        if ($this->multilingual_settings['translate_job_terms']) {
            $this->setup_recruitment_term_translations();
        }
    }

    /**
     * Register theme translatable strings
     * 
     * @since 1.0.0
     * @return void
     */
    private function register_theme_strings() {
        
        $translatable_strings = array(
            'hero_title' => get_theme_mod('recruitpro_hero_title', ''),
            'hero_subtitle' => get_theme_mod('recruitpro_hero_subtitle', ''),
            'footer_copyright' => get_theme_mod('recruitpro_footer_copyright', ''),
            'contact_cta_text' => get_theme_mod('recruitpro_contact_cta_text', ''),
            'services_heading' => get_theme_mod('recruitpro_services_heading', ''),
            'about_excerpt' => get_theme_mod('recruitpro_about_excerpt', ''),
        );
        
        // Register with translation plugins
        foreach ($translatable_strings as $key => $value) {
            if (!empty($value)) {
                
                // WPML registration
                if ($this->plugin_compatibility['wpml'] && function_exists('icl_register_string')) {
                    icl_register_string('RecruitPro Theme', ucfirst(str_replace('_', ' ', $key)), $value);
                }
                
                // Polylang registration
                if ($this->plugin_compatibility['polylang'] && function_exists('pll_register_string')) {
                    pll_register_string($key, $value, 'RecruitPro Theme');
                }
            }
        }
    }

    /**
     * Setup RTL support
     * 
     * @since 1.0.0
     * @return void
     */
    private function setup_rtl_support() {
        
        if (!$this->multilingual_settings['rtl_optimization']) {
            return;
        }
        
        // Add RTL body class
        add_filter('body_class', function($classes) {
            $classes[] = 'rtl-language';
            $classes[] = 'lang-' . $this->current_language['code'];
            return $classes;
        });
        
        // Set text direction
        add_filter('language_attributes', function($attributes) {
            return $attributes . ' dir="rtl"';
        });
    }

    /**
     * Setup recruitment term translations
     * 
     * @since 1.0.0
     * @return void
     */
    private function setup_recruitment_term_translations() {
        
        $current_lang = $this->current_language['code'];
        
        if (isset($this->basic_languages[$current_lang]['recruitment_terms'])) {
            $terms = $this->basic_languages[$current_lang]['recruitment_terms'];
            
            // Make recruitment terms available globally
            global $recruitpro_recruitment_terms;
            $recruitpro_recruitment_terms = $terms;
            
            // Filter post type labels
            add_filter('post_type_labels_job', array($this, 'translate_job_post_type_labels'));
        }
    }

    /**
     * Translate job post type labels
     * 
     * @since 1.0.0
     * @param object $labels Post type labels
     * @return object Modified labels
     */
    public function translate_job_post_type_labels($labels) {
        
        global $recruitpro_recruitment_terms;
        
        if (!$recruitpro_recruitment_terms) {
            return $labels;
        }
        
        $terms = $recruitpro_recruitment_terms;
        
        $labels->name = $terms['jobs'] ?? $labels->name;
        $labels->singular_name = $terms['job'] ?? $labels->singular_name;
        $labels->add_new_item = sprintf(__('Add New %s', 'recruitpro'), $terms['job'] ?? 'Job');
        $labels->edit_item = sprintf(__('Edit %s', 'recruitpro'), $terms['job'] ?? 'Job');
        $labels->new_item = sprintf(__('New %s', 'recruitpro'), $terms['job'] ?? 'Job');
        $labels->view_item = sprintf(__('View %s', 'recruitpro'), $terms['job'] ?? 'Job');
        $labels->search_items = sprintf(__('Search %s', 'recruitpro'), $terms['jobs'] ?? 'Jobs');
        $labels->not_found = sprintf(__('No %s found', 'recruitpro'), strtolower($terms['jobs'] ?? 'jobs'));
        
        return $labels;
    }

    /**
     * Output language meta tags
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_language_meta_tags() {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return;
        }
        
        $current_lang = $this->current_language;
        
        // Language meta tags
        echo '<meta name="language" content="' . esc_attr($current_lang['code']) . '">' . "\n";
        echo '<meta name="content-language" content="' . esc_attr($current_lang['hreflang']) . '">' . "\n";
        
        // Alternate language links (hreflang)
        if ($this->multilingual_settings['hreflang_management'] === 'theme' || !function_exists('recruitpro_seo_output_hreflang')) {
            $this->output_basic_hreflang_tags();
        }
        
        // RTL-specific meta tags
        if ($this->is_rtl_language($current_lang['code'])) {
            echo '<meta name="text-direction" content="rtl">' . "\n";
        }
    }

    /**
     * Output basic hreflang tags (fallback)
     * 
     * @since 1.0.0
     * @return void
     */
    private function output_basic_hreflang_tags() {
        
        // Get available translations for current page
        $translations = $this->get_page_translations();
        
        if (empty($translations)) {
            return;
        }
        
        foreach ($translations as $lang_code => $translation) {
            if (!empty($translation['url'])) {
                echo '<link rel="alternate" hreflang="' . esc_attr($translation['hreflang']) . '" href="' . esc_url($translation['url']) . '">' . "\n";
            }
        }
        
        // x-default hreflang
        $default_lang = $this->multilingual_settings['default_language'];
        if (isset($translations[$default_lang])) {
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($translations[$default_lang]['url']) . '">' . "\n";
        }
    }

    /**
     * Get page translations (basic implementation)
     * 
     * @since 1.0.0
     * @return array Page translations
     */
    private function get_page_translations() {
        
        // If SEO plugin is active, delegate to it
        if (function_exists('recruitpro_seo_get_page_translations')) {
            $current_object_id = get_queried_object_id();
            return recruitpro_seo_get_page_translations($current_object_id);
        }
        
        // Basic fallback: return current page only
        $current_url = home_url(add_query_arg(null, null));
        $current_lang = $this->current_language;
        
        return array(
            $current_lang['code'] => array(
                'url' => $current_url,
                'hreflang' => $current_lang['hreflang'],
                'name' => $current_lang['name'],
                'native_name' => $current_lang['native_name'],
            ),
        );
    }

    /**
     * Enqueue language-specific assets
     * 
     * @since 1.0.0
     * @return void
     */
    public function enqueue_language_assets() {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return;
        }
        
        $current_lang = $this->current_language['code'];
        $theme_version = wp_get_theme()->get('Version');
        
        // RTL stylesheet
        if ($this->is_rtl_language($current_lang)) {
            wp_enqueue_style(
                'recruitpro-rtl',
                get_template_directory_uri() . '/assets/css/rtl.css',
                array('recruitpro-main-style'),
                $theme_version,
                'all'
            );
        }
        
        // Language-specific fonts
        $this->enqueue_language_fonts($current_lang);
        
        // Language switcher scripts
        if ($this->multilingual_settings['show_language_switcher']) {
            wp_enqueue_script(
                'recruitpro-language-switcher',
                get_template_directory_uri() . '/assets/js/language-switcher.js',
                array('jquery'),
                $theme_version,
                true
            );
            
            wp_localize_script('recruitpro-language-switcher', 'recruitproLanguage', array(
                'currentLanguage' => $current_lang,
                'availableLanguages' => $this->get_available_languages(),
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('recruitpro_language_switch'),
            ));
        }
    }

    /**
     * Enqueue language-specific fonts
     * 
     * @since 1.0.0
     * @param string $lang_code Language code
     * @return void
     */
    private function enqueue_language_fonts($lang_code) {
        
        if (!isset($this->basic_languages[$lang_code]['font_family'])) {
            return;
        }
        
        $font_family = $this->basic_languages[$lang_code]['font_family'];
        
        // Arabic fonts
        if ($lang_code === 'ar') {
            wp_enqueue_style(
                'recruitpro-arabic-fonts',
                'https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap',
                array(),
                null
            );
        }
        
        // Hebrew fonts
        if ($lang_code === 'he') {
            wp_enqueue_style(
                'recruitpro-hebrew-fonts',
                'https://fonts.googleapis.com/css2?family=Noto+Sans+Hebrew:wght@300;400;500;600;700&display=swap',
                array(),
                null
            );
        }
        
        // Add language-specific font CSS
        wp_add_inline_style('recruitpro-main-style', "
            .lang-{$lang_code} body,
            .lang-{$lang_code} .language-specific-text {
                font-family: {$font_family};
            }
        ");
    }

    /**
     * Add language body classes
     * 
     * @since 1.0.0
     * @param array $classes Existing body classes
     * @return array Modified body classes
     */
    public function add_language_body_classes($classes) {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return $classes;
        }
        
        $current_lang = $this->current_language['code'];
        
        // Language-specific classes
        $classes[] = 'lang-' . $current_lang;
        $classes[] = 'locale-' . str_replace('_', '-', $this->current_language['locale']);
        
        // RTL class
        if ($this->is_rtl_language($current_lang)) {
            $classes[] = 'rtl';
            $classes[] = 'text-direction-rtl';
        } else {
            $classes[] = 'ltr';
            $classes[] = 'text-direction-ltr';
        }
        
        // Multilingual support class
        $classes[] = 'multilingual-enabled';
        
        return $classes;
    }

    /**
     * Add language attributes
     * 
     * @since 1.0.0
     * @param string $attributes Existing language attributes
     * @return string Modified language attributes
     */
    public function add_language_attributes($attributes) {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return $attributes;
        }
        
        $current_lang = $this->current_language;
        
        // Update language attribute
        $attributes = preg_replace('/lang="[^"]*"/', 'lang="' . esc_attr($current_lang['hreflang']) . '"', $attributes);
        
        // Add text direction
        if ($this->is_rtl_language($current_lang['code'])) {
            $attributes .= ' dir="rtl"';
        }
        
        return $attributes;
    }

    /**
     * Output RTL styles
     * 
     * @since 1.0.0
     * @return void
     */
    public function output_rtl_styles() {
        
        if (!$this->is_rtl_language($this->current_language['code'])) {
            return;
        }
        
        // Inline RTL optimizations
        echo '<style id="recruitpro-rtl-inline">';
        echo 'body { direction: rtl; text-align: right; }';
        echo '.alignleft { float: right; margin: 0 0 1em 1em; }';
        echo '.alignright { float: left; margin: 0 1em 1em 0; }';
        echo '.wp-caption.alignleft { margin: 0 0 1em 1em; }';
        echo '.wp-caption.alignright { margin: 0 1em 1em 0; }';
        echo '</style>';
    }

    /**
     * Display language switcher
     * 
     * @since 1.0.0
     * @return void
     */
    public function display_language_switcher() {
        
        if (!$this->multilingual_settings['show_language_switcher']) {
            return;
        }
        
        $available_languages = $this->get_available_languages();
        
        if (count($available_languages) <= 1) {
            return;
        }
        
        // Use template part for language switcher
        get_template_part('template-parts/header/language-switcher');
    }

    /**
     * Get available languages
     * 
     * @since 1.0.0
     * @return array Available languages
     */
    private function get_available_languages() {
        
        // If SEO plugin is active, get from there
        if (function_exists('recruitpro_seo_get_available_languages')) {
            return recruitpro_seo_get_available_languages();
        }
        
        // If translation plugin is active
        if ($this->plugin_compatibility['wpml']) {
            return $this->get_wpml_languages();
        }
        
        if ($this->plugin_compatibility['polylang']) {
            return $this->get_polylang_languages();
        }
        
        // Basic fallback: return configured languages
        $enabled_languages = get_theme_mod('recruitpro_enabled_languages', array('en'));
        $available = array();
        
        foreach ($enabled_languages as $lang_code) {
            if (isset($this->basic_languages[$lang_code])) {
                $available[$lang_code] = $this->basic_languages[$lang_code];
            }
        }
        
        return $available;
    }

    /**
     * Get WPML languages
     * 
     * @since 1.0.0
     * @return array WPML languages
     */
    private function get_wpml_languages() {
        
        if (!function_exists('icl_get_languages')) {
            return array();
        }
        
        $wpml_languages = icl_get_languages('skip_missing=0&orderby=code');
        $available = array();
        
        foreach ($wpml_languages as $lang) {
            $lang_code = $lang['language_code'];
            if (isset($this->basic_languages[$lang_code])) {
                $available[$lang_code] = array_merge(
                    $this->basic_languages[$lang_code],
                    array(
                        'url' => $lang['url'],
                        'active' => $lang['active'],
                    )
                );
            }
        }
        
        return $available;
    }

    /**
     * Get Polylang languages
     * 
     * @since 1.0.0
     * @return array Polylang languages
     */
    private function get_polylang_languages() {
        
        if (!function_exists('pll_the_languages')) {
            return array();
        }
        
        $polylang_languages = pll_the_languages(array('raw' => 1));
        $available = array();
        
        foreach ($polylang_languages as $lang) {
            $lang_code = $lang['slug'];
            if (isset($this->basic_languages[$lang_code])) {
                $available[$lang_code] = array_merge(
                    $this->basic_languages[$lang_code],
                    array(
                        'url' => $lang['url'],
                        'current' => $lang['current_lang'],
                    )
                );
            }
        }
        
        return $available;
    }

    /**
     * Localize menu arguments
     * 
     * @since 1.0.0
     * @param array $args Menu arguments
     * @return array Modified menu arguments
     */
    public function localize_menu_args($args) {
        
        // Add language-specific menu classes
        if (isset($args['menu_class'])) {
            $args['menu_class'] .= ' lang-' . $this->current_language['code'];
        }
        
        return $args;
    }

    /**
     * Optimize multilingual content
     * 
     * @since 1.0.0
     * @param string $content Post content
     * @return string Modified content
     */
    public function optimize_multilingual_content($content) {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return $content;
        }
        
        $current_lang = $this->current_language['code'];
        
        // Add language-specific content wrapper
        $content = '<div class="content-lang-' . esc_attr($current_lang) . '">' . $content . '</div>';
        
        return $content;
    }

    /**
     * Localize search form
     * 
     * @since 1.0.0
     * @param string $form Search form HTML
     * @return string Modified search form
     */
    public function localize_search_form($form) {
        
        $current_lang = $this->current_language['code'];
        
        // Add language parameter to search form
        $hidden_input = '<input type="hidden" name="lang" value="' . esc_attr($current_lang) . '">';
        $form = str_replace('</form>', $hidden_input . '</form>', $form);
        
        return $form;
    }

    /**
     * Get localized tagline
     * 
     * @since 1.0.0
     * @param string $tagline Current tagline
     * @return string Localized tagline
     */
    public function get_localized_tagline($tagline) {
        
        // Try to get translated version from plugins
        if ($this->plugin_compatibility['wpml'] && function_exists('icl_t')) {
            return icl_t('RecruitPro Theme', 'Tagline', $tagline);
        }
        
        if ($this->plugin_compatibility['polylang'] && function_exists('pll__')) {
            return pll__($tagline);
        }
        
        return $tagline;
    }

    /**
     * Display footer language information
     * 
     * @since 1.0.0
     * @return void
     */
    public function display_footer_language_info() {
        
        if (!$this->multilingual_settings['enable_multilingual']) {
            return;
        }
        
        $current_lang = $this->current_language;
        
        echo '<div class="footer-language-info">';
        echo '<span class="current-language-display">';
        echo esc_html(sprintf(__('Content in %s', 'recruitpro'), $current_lang['native_name']));
        echo '</span>';
        echo '</div>';
    }

    /**
     * Add multilingual customizer options
     * 
     * @since 1.0.0
     * @param WP_Customize_Manager $wp_customize WordPress Customizer Manager
     * @return void
     */
    public function add_multilingual_customizer_options($wp_customize) {
        
        // Multilingual Panel
        $wp_customize->add_panel('recruitpro_multilingual_panel', array(
            'title' => __('Multilingual Settings', 'recruitpro'),
            'description' => __('Configure multilingual support and language options.', 'recruitpro'),
            'priority' => 140,
            'capability' => 'edit_theme_options',
        ));

        // General Multilingual Section
        $wp_customize->add_section('recruitpro_multilingual_general', array(
            'title' => __('General Settings', 'recruitpro'),
            'panel' => 'recruitpro_multilingual_panel',
            'priority' => 10,
        ));

        // Enable multilingual support
        $wp_customize->add_setting('recruitpro_enable_multilingual', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_enable_multilingual', array(
            'label' => __('Enable Multilingual Support', 'recruitpro'),
            'description' => __('Enable multilingual features and language switching.', 'recruitpro'),
            'section' => 'recruitpro_multilingual_general',
            'type' => 'checkbox',
        ));

        // Default language
        $wp_customize->add_setting('recruitpro_default_language', array(
            'default' => 'en',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_default_language', array(
            'label' => __('Default Language', 'recruitpro'),
            'section' => 'recruitpro_multilingual_general',
            'type' => 'select',
            'choices' => $this->get_language_choices(),
        ));

        // Language Switcher Section
        $wp_customize->add_section('recruitpro_language_switcher', array(
            'title' => __('Language Switcher', 'recruitpro'),
            'panel' => 'recruitpro_multilingual_panel',
            'priority' => 20,
        ));

        // Show language switcher
        $wp_customize->add_setting('recruitpro_show_language_switcher', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_show_language_switcher', array(
            'label' => __('Show Language Switcher', 'recruitpro'),
            'description' => __('Display language switcher in header.', 'recruitpro'),
            'section' => 'recruitpro_language_switcher',
            'type' => 'checkbox',
        ));

        // Show flag icons
        $wp_customize->add_setting('recruitpro_show_flag_icons', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_show_flag_icons', array(
            'label' => __('Show Flag Icons', 'recruitpro'),
            'description' => __('Display flag icons in language switcher.', 'recruitpro'),
            'section' => 'recruitpro_language_switcher',
            'type' => 'checkbox',
        ));

        // RTL Support Section
        $wp_customize->add_section('recruitpro_rtl_support', array(
            'title' => __('RTL Support', 'recruitpro'),
            'panel' => 'recruitpro_multilingual_panel',
            'priority' => 30,
        ));

        // RTL optimization
        $wp_customize->add_setting('recruitpro_rtl_optimization', array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('recruitpro_rtl_optimization', array(
            'label' => __('Enable RTL Optimization', 'recruitpro'),
            'description' => __('Optimize layout and styling for right-to-left languages.', 'recruitpro'),
            'section' => 'recruitpro_rtl_support',
            'type' => 'checkbox',
        ));
    }

    /**
     * Get language choices for customizer
     * 
     * @since 1.0.0
     * @return array Language choices
     */
    private function get_language_choices() {
        
        $choices = array();
        
        foreach ($this->basic_languages as $code => $language) {
            $choices[$code] = $language['native_name'] . ' (' . strtoupper($code) . ')';
        }
        
        return $choices;
    }

    /**
     * Integrate with translation plugins
     * 
     * @since 1.0.0
     * @return void
     */
    public function integrate_translation_plugins() {
        
        // WPML integration
        if ($this->plugin_compatibility['wpml']) {
            $this->setup_wpml_integration();
        }
        
        // Polylang integration
        if ($this->plugin_compatibility['polylang']) {
            $this->setup_polylang_integration();
        }
        
        // TranslatePress integration
        if ($this->plugin_compatibility['translatepress']) {
            $this->setup_translatepress_integration();
        }
    }

    /**
     * Setup WPML integration
     * 
     * @since 1.0.0
     * @return void
     */
    private function setup_wpml_integration() {
        
        // Register theme strings with WPML
        add_action('init', array($this, 'register_wpml_strings'));
        
        // WPML configuration
        add_filter('wpml_config_array', array($this, 'wpml_config'));
    }

    /**
     * Setup Polylang integration
     * 
     * @since 1.0.0
     * @return void
     */
    private function setup_polylang_integration() {
        
        // Register theme strings with Polylang
        add_action('init', array($this, 'register_polylang_strings'));
    }

    /**
     * Setup TranslatePress integration
     * 
     * @since 1.0.0
     * @return void
     */
    private function setup_translatepress_integration() {
        
        // Add TranslatePress compatibility filters
        add_filter('trp_skip_selectors_from_dynamic_translation', array($this, 'translatepress_skip_selectors'));
    }

    /**
     * Register translatable strings (admin)
     * 
     * @since 1.0.0
     * @return void
     */
    public function register_translatable_strings() {
        
        // This method handles admin-side string registration
        // Implementation depends on active translation plugins
    }

    /**
     * Add multilingual admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function add_multilingual_admin_page() {
        
        add_theme_page(
            __('Multilingual Settings', 'recruitpro'),
            __('Multilingual', 'recruitpro'),
            'manage_options',
            'recruitpro-multilingual',
            array($this, 'display_multilingual_admin_page')
        );
    }

    /**
     * Display multilingual admin page
     * 
     * @since 1.0.0
     * @return void
     */
    public function display_multilingual_admin_page() {
        
        echo '<div class="wrap">';
        echo '<h1>' . __('RecruitPro Multilingual Settings', 'recruitpro') . '</h1>';
        echo '<p>' . __('Configure multilingual support for your recruitment website.', 'recruitpro') . '</p>';
        
        // Display current language status
        echo '<h2>' . __('Current Language Status', 'recruitpro') . '</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<tr><th>' . __('Current Language', 'recruitpro') . '</th><td>' . esc_html($this->current_language['native_name']) . '</td></tr>';
        echo '<tr><th>' . __('Language Code', 'recruitpro') . '</th><td>' . esc_html($this->current_language['code']) . '</td></tr>';
        echo '<tr><th>' . __('Text Direction', 'recruitpro') . '</th><td>' . esc_html($this->current_language['direction']) . '</td></tr>';
        echo '</table>';
        
        echo '</div>';
    }

    /**
     * AJAX language switch handler
     * 
     * @since 1.0.0
     * @return void
     */
    public function ajax_switch_language() {
        
        if (!wp_verify_nonce($_POST['nonce'], 'recruitpro_language_switch')) {
            wp_die('Security check failed');
        }
        
        $target_language = sanitize_text_field($_POST['language']);
        $current_url = esc_url_raw($_POST['current_url']);
        
        // Get target URL for language
        $target_url = $this->get_language_url($target_language, $current_url);
        
        wp_send_json_success(array(
            'redirect_url' => $target_url,
            'language' => $target_language,
        ));
    }

    /**
     * Get URL for specific language
     * 
     * @since 1.0.0
     * @param string $language Language code
     * @param string $current_url Current URL
     * @return string Language-specific URL
     */
    private function get_language_url($language, $current_url) {
        
        // If SEO plugin handles this, delegate to it
        if (function_exists('recruitpro_seo_get_language_url')) {
            return recruitpro_seo_get_language_url($language, $current_url);
        }
        
        // Basic implementation: add language parameter
        $separator = strpos($current_url, '?') !== false ? '&' : '?';
        return $current_url . $separator . 'lang=' . $language;
    }

    /**
     * Check if language is RTL
     * 
     * @since 1.0.0
     * @param string $language_code Language code
     * @return bool True if RTL language
     */
    public function is_rtl_language($language_code) {
        
        return isset($this->rtl_languages[$language_code]);
    }

    /**
     * Get current language
     * 
     * @since 1.0.0
     * @return array Current language information
     */
    public function get_current_language() {
        
        return $this->current_language;
    }

    /**
     * Get recruitment term translation
     * 
     * @since 1.0.0
     * @param string $term Term to translate
     * @param string $language Language code (optional)
     * @return string Translated term
     */
    public function get_recruitment_term($term, $language = null) {
        
        if (!$language) {
            $language = $this->current_language['code'];
        }
        
        if (isset($this->basic_languages[$language]['recruitment_terms'][$term])) {
            return $this->basic_languages[$language]['recruitment_terms'][$term];
        }
        
        return $term; // Fallback to original term
    }
}

// Initialize multilingual manager
new RecruitPro_Multilingual_Manager();

/**
 * Helper Functions
 */

/**
 * Get current language information
 * 
 * @since 1.0.0
 * @return array Current language data
 */
function recruitpro_get_current_language() {
    
    static $multilingual_manager = null;
    
    if (is_null($multilingual_manager)) {
        $multilingual_manager = new RecruitPro_Multilingual_Manager();
    }
    
    return $multilingual_manager->get_current_language();
}

/**
 * Check if current language is RTL
 * 
 * @since 1.0.0
 * @return bool True if RTL language
 */
function recruitpro_is_rtl_language() {
    
    static $multilingual_manager = null;
    
    if (is_null($multilingual_manager)) {
        $multilingual_manager = new RecruitPro_Multilingual_Manager();
    }
    
    $current_lang = $multilingual_manager->get_current_language();
    return $multilingual_manager->is_rtl_language($current_lang['code']);
}

/**
 * Get recruitment term translation
 * 
 * @since 1.0.0
 * @param string $term Term to translate
 * @param string $language Language code (optional)
 * @return string Translated term
 */
function recruitpro_get_recruitment_term($term, $language = null) {
    
    static $multilingual_manager = null;
    
    if (is_null($multilingual_manager)) {
        $multilingual_manager = new RecruitPro_Multilingual_Manager();
    }
    
    return $multilingual_manager->get_recruitment_term($term, $language);
}

/**
 * Display localized recruitment term
 * 
 * @since 1.0.0
 * @param string $term Term to display
 * @param string $language Language code (optional)
 * @return void
 */
function recruitpro_the_recruitment_term($term, $language = null) {
    
    echo esc_html(recruitpro_get_recruitment_term($term, $language));
}

/**
 * Get language-specific CSS class
 * 
 * @since 1.0.0
 * @param string $base_class Base CSS class
 * @return string Language-specific CSS class
 */
function recruitpro_get_language_class($base_class = '') {
    
    $current_lang = recruitpro_get_current_language();
    $classes = array($base_class);
    
    if ($current_lang) {
        $classes[] = 'lang-' . $current_lang['code'];
        
        if (recruitpro_is_rtl_language()) {
            $classes[] = 'rtl';
        }
    }
    
    return implode(' ', array_filter($classes));
}

?>