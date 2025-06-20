/**
 * RecruitPro Theme - Theme Switcher Module
 * 
 * Handles dark/light mode switching with user preference detection,
 * persistence, smooth transitions, and accessibility features.
 * 
 * @package RecruitPro
 * @subpackage Assets/JS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Theme Switcher Module
    const RecruitProThemeSwitcher = {
        
        // Configuration
        config: {
            storageKey: 'recruitpro_theme_preference',
            defaultTheme: 'light',
            supportedThemes: ['light', 'dark', 'auto'],
            transitionDuration: 300,
            enableTransitions: true,
            enableSystemDetection: true,
            announcementDuration: 3000
        },

        // Cache elements
        cache: {
            $html: null,
            $body: null,
            $toggles: null,
            $announcement: null
        },

        // Current state
        state: {
            currentTheme: null,
            systemPreference: null,
            userPreference: null,
            isTransitioning: false
        },

        /**
         * Initialize theme switcher
         */
        init: function() {
            this.cacheElements();
            this.detectSystemPreference();
            this.loadUserPreference();
            this.determineInitialTheme();
            this.createToggleButtons();
            this.createAnnouncementArea();
            this.bindEvents();
            this.applyTheme(this.state.currentTheme, false);
            this.setupMediaQueryListener();
            this.setupCustomizerIntegration();
        },

        /**
         * Cache DOM elements
         */
        cacheElements: function() {
            this.cache.$html = $('html');
            this.cache.$body = $(document.body);
        },

        /**
         * Detect system color scheme preference
         */
        detectSystemPreference: function() {
            if (!this.config.enableSystemDetection) {
                this.state.systemPreference = this.config.defaultTheme;
                return;
            }

            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                this.state.systemPreference = 'dark';
            } else {
                this.state.systemPreference = 'light';
            }
        },

        /**
         * Load user preference from storage
         */
        loadUserPreference: function() {
            try {
                const stored = localStorage.getItem(this.config.storageKey);
                if (stored && this.config.supportedThemes.includes(stored)) {
                    this.state.userPreference = stored;
                } else {
                    this.state.userPreference = 'auto';
                }
            } catch (e) {
                // Storage not available
                this.state.userPreference = 'auto';
            }
        },

        /**
         * Save user preference to storage
         */
        saveUserPreference: function(theme) {
            try {
                localStorage.setItem(this.config.storageKey, theme);
                this.state.userPreference = theme;
            } catch (e) {
                // Storage not available
                console.warn('RecruitPro Theme Switcher: Unable to save preference to localStorage');
            }
        },

        /**
         * Determine initial theme based on user preference and system settings
         */
        determineInitialTheme: function() {
            if (this.state.userPreference === 'auto') {
                this.state.currentTheme = this.state.systemPreference;
            } else {
                this.state.currentTheme = this.state.userPreference;
            }
        },

        /**
         * Create theme toggle buttons
         */
        createToggleButtons: function() {
            // Create main toggle button
            this.createMainToggleButton();
            
            // Create customizer toggle if in customizer
            if (window.wp && window.wp.customize) {
                this.createCustomizerToggle();
            }
            
            // Find existing toggle elements
            this.cache.$toggles = $('.rp-theme-toggle');
            
            // Update toggle states
            this.updateToggleStates();
        },

        /**
         * Create main theme toggle button
         */
        createMainToggleButton: function() {
            const toggleHTML = `
                <div class="rp-theme-switcher" id="rp-theme-switcher">
                    <button type="button" class="rp-theme-toggle rp-theme-toggle-main" 
                            aria-label="Toggle theme" 
                            title="Switch between light and dark themes"
                            data-theme-toggle="main">
                        <span class="toggle-icon toggle-icon-light" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"/>
                                <line x1="12" y1="1" x2="12" y2="3"/>
                                <line x1="12" y1="21" x2="12" y2="23"/>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                <line x1="1" y1="12" x2="3" y2="12"/>
                                <line x1="21" y1="12" x2="23" y2="12"/>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                            </svg>
                        </span>
                        <span class="toggle-icon toggle-icon-dark" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                            </svg>
                        </span>
                        <span class="toggle-icon toggle-icon-auto" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </span>
                        <span class="toggle-text sr-only">Theme: <span class="current-theme-text">Auto</span></span>
                    </button>
                    
                    <div class="rp-theme-menu" id="rp-theme-menu" hidden>
                        <button type="button" class="rp-theme-option" data-theme="light">
                            <span class="theme-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="5"/>
                                    <line x1="12" y1="1" x2="12" y2="3"/>
                                    <line x1="12" y1="21" x2="12" y2="23"/>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                    <line x1="1" y1="12" x2="3" y2="12"/>
                                    <line x1="21" y1="12" x2="23" y2="12"/>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                </svg>
                            </span>
                            <span class="theme-label">Light</span>
                            <span class="theme-check" aria-hidden="true">✓</span>
                        </button>
                        <button type="button" class="rp-theme-option" data-theme="dark">
                            <span class="theme-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                                </svg>
                            </span>
                            <span class="theme-label">Dark</span>
                            <span class="theme-check" aria-hidden="true">✓</span>
                        </button>
                        <button type="button" class="rp-theme-option" data-theme="auto">
                            <span class="theme-icon" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                    <line x1="8" y1="21" x2="16" y2="21"/>
                                    <line x1="12" y1="17" x2="12" y2="21"/>
                                </svg>
                            </span>
                            <span class="theme-label">Auto</span>
                            <span class="theme-check" aria-hidden="true">✓</span>
                        </button>
                    </div>
                </div>
            `;

            // Add to header or footer based on customizer setting
            const placement = this.getTogglePlacement();
            $(placement).append(toggleHTML);
        },

        /**
         * Get toggle button placement
         */
        getTogglePlacement: function() {
            if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.theme_toggle_position) {
                const position = recruitpro_theme.theme_toggle_position;
                switch (position) {
                    case 'header-right':
                        return '.header-actions, .site-header';
                    case 'header-left':
                        return '.site-branding, .site-header';
                    case 'footer':
                        return '.site-footer';
                    case 'floating':
                        return 'body';
                    default:
                        return '.header-actions, .site-header';
                }
            }
            return '.header-actions, .site-header';
        },

        /**
         * Create customizer theme toggle
         */
        createCustomizerToggle: function() {
            if (!window.wp || !window.wp.customize) return;

            const customizerToggle = `
                <div class="customize-control customize-control-theme-switcher">
                    <label class="customize-control-title">Theme Mode</label>
                    <div class="customize-control-content">
                        <div class="rp-customizer-theme-switcher">
                            <button type="button" class="rp-theme-toggle" data-theme-toggle="customizer">
                                <span class="toggle-content">
                                    <span class="current-theme-icon"></span>
                                    <span class="current-theme-label"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Add to customizer sidebar
            $('.customize-control:first').before(customizerToggle);
        },

        /**
         * Create announcement area for accessibility
         */
        createAnnouncementArea: function() {
            const announcementHTML = `
                <div id="rp-theme-announcement" class="rp-theme-announcement sr-only" 
                     aria-live="polite" aria-atomic="true"></div>
            `;
            this.cache.$body.append(announcementHTML);
            this.cache.$announcement = $('#rp-theme-announcement');
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Toggle button clicks
            $(document).on('click', '.rp-theme-toggle', this.handleToggleClick.bind(this));
            
            // Theme option clicks
            $(document).on('click', '.rp-theme-option', this.handleThemeOptionClick.bind(this));
            
            // Keyboard events
            $(document).on('keydown', '.rp-theme-toggle', this.handleToggleKeydown.bind(this));
            $(document).on('keydown', '.rp-theme-menu', this.handleMenuKeydown.bind(this));
            
            // Close menu when clicking outside
            $(document).on('click', this.handleOutsideClick.bind(this));
            
            // Window focus event to sync with other tabs
            $(window).on('focus', this.handleWindowFocus.bind(this));
            
            // Storage event for cross-tab synchronization
            $(window).on('storage', this.handleStorageChange.bind(this));
        },

        /**
         * Handle toggle button click
         */
        handleToggleClick: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $toggle = $(e.currentTarget);
            const toggleType = $toggle.data('theme-toggle');
            
            if (toggleType === 'main') {
                // Show/hide theme menu
                const $menu = $('#rp-theme-menu');
                const isOpen = !$menu.attr('hidden');
                
                if (isOpen) {
                    this.closeThemeMenu();
                } else {
                    this.openThemeMenu();
                }
            } else {
                // Simple toggle for other buttons
                this.cycleTheme();
            }
        },

        /**
         * Handle theme option click
         */
        handleThemeOptionClick: function(e) {
            e.preventDefault();
            
            const $option = $(e.currentTarget);
            const theme = $option.data('theme');
            
            this.setTheme(theme);
            this.closeThemeMenu();
        },

        /**
         * Handle toggle keydown
         */
        handleToggleKeydown: function(e) {
            switch (e.key) {
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    $(e.currentTarget).click();
                    break;
                
                case 'ArrowDown':
                    e.preventDefault();
                    this.openThemeMenu();
                    $('#rp-theme-menu .rp-theme-option:first').focus();
                    break;
                
                case 'Escape':
                    this.closeThemeMenu();
                    break;
            }
        },

        /**
         * Handle menu keydown
         */
        handleMenuKeydown: function(e) {
            const $menu = $(e.currentTarget);
            const $options = $menu.find('.rp-theme-option');
            const $focused = $options.filter(':focus');
            const currentIndex = $options.index($focused);
            
            switch (e.key) {
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : $options.length - 1;
                    $options.eq(prevIndex).focus();
                    break;
                
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = currentIndex < $options.length - 1 ? currentIndex + 1 : 0;
                    $options.eq(nextIndex).focus();
                    break;
                
                case 'Home':
                    e.preventDefault();
                    $options.first().focus();
                    break;
                
                case 'End':
                    e.preventDefault();
                    $options.last().focus();
                    break;
                
                case 'Escape':
                    e.preventDefault();
                    this.closeThemeMenu();
                    $('.rp-theme-toggle-main').focus();
                    break;
                
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    $focused.click();
                    break;
            }
        },

        /**
         * Handle outside click
         */
        handleOutsideClick: function(e) {
            if (!$(e.target).closest('.rp-theme-switcher').length) {
                this.closeThemeMenu();
            }
        },

        /**
         * Handle window focus
         */
        handleWindowFocus: function() {
            // Sync theme with other tabs
            const storedPreference = this.getStoredPreference();
            if (storedPreference !== this.state.userPreference) {
                this.loadUserPreference();
                this.determineInitialTheme();
                this.applyTheme(this.state.currentTheme, false);
                this.updateToggleStates();
            }
        },

        /**
         * Handle storage change
         */
        handleStorageChange: function(e) {
            if (e.key === this.config.storageKey) {
                this.loadUserPreference();
                this.determineInitialTheme();
                this.applyTheme(this.state.currentTheme, false);
                this.updateToggleStates();
            }
        },

        /**
         * Open theme menu
         */
        openThemeMenu: function() {
            const $menu = $('#rp-theme-menu');
            const $toggle = $('.rp-theme-toggle-main');
            
            $menu.removeAttr('hidden');
            $toggle.attr('aria-expanded', 'true');
            
            // Position menu
            this.positionThemeMenu();
            
            // Focus first option
            setTimeout(() => {
                $menu.find('.rp-theme-option:first').focus();
            }, 50);
        },

        /**
         * Close theme menu
         */
        closeThemeMenu: function() {
            const $menu = $('#rp-theme-menu');
            const $toggle = $('.rp-theme-toggle-main');
            
            $menu.attr('hidden', true);
            $toggle.attr('aria-expanded', 'false');
        },

        /**
         * Position theme menu
         */
        positionThemeMenu: function() {
            const $menu = $('#rp-theme-menu');
            const $toggle = $('.rp-theme-toggle-main');
            
            if ($menu.length && $toggle.length) {
                const toggleRect = $toggle[0].getBoundingClientRect();
                const menuHeight = $menu.outerHeight();
                const windowHeight = $(window).height();
                
                // Position below or above toggle based on available space
                if (toggleRect.bottom + menuHeight > windowHeight) {
                    $menu.addClass('menu-above');
                } else {
                    $menu.removeClass('menu-above');
                }
            }
        },

        /**
         * Cycle through themes
         */
        cycleTheme: function() {
            const currentIndex = this.config.supportedThemes.indexOf(this.state.userPreference);
            const nextIndex = (currentIndex + 1) % this.config.supportedThemes.length;
            const nextTheme = this.config.supportedThemes[nextIndex];
            
            this.setTheme(nextTheme);
        },

        /**
         * Set theme
         */
        setTheme: function(theme) {
            if (!this.config.supportedThemes.includes(theme)) {
                console.warn(`RecruitPro Theme Switcher: Unsupported theme "${theme}"`);
                return;
            }
            
            this.saveUserPreference(theme);
            
            // Determine actual theme to apply
            const actualTheme = theme === 'auto' ? this.state.systemPreference : theme;
            
            this.applyTheme(actualTheme, true);
            this.updateToggleStates();
            this.announceThemeChange(theme);
            
            // Trigger custom event
            $(document).trigger('themeChanged', [theme, actualTheme]);
        },

        /**
         * Apply theme to document
         */
        applyTheme: function(theme, withTransition = true) {
            if (this.state.isTransitioning) return;
            
            const previousTheme = this.state.currentTheme;
            this.state.currentTheme = theme;
            
            // Add transition class if enabled
            if (withTransition && this.config.enableTransitions) {
                this.state.isTransitioning = true;
                this.cache.$body.addClass('rp-theme-transitioning');
                
                setTimeout(() => {
                    this.state.isTransitioning = false;
                    this.cache.$body.removeClass('rp-theme-transitioning');
                }, this.config.transitionDuration);
            }
            
            // Update HTML attributes
            this.cache.$html.attr('data-theme', theme);
            this.cache.$body.removeClass('light-theme dark-theme')
                            .addClass(`${theme}-theme`);
            
            // Update meta theme-color
            this.updateMetaThemeColor(theme);
            
            // Update favicon if different themes have different favicons
            this.updateFavicon(theme);
            
            // Trigger WordPress customizer refresh if in customizer
            if (window.wp && window.wp.customize && window.wp.customize.preview) {
                window.wp.customize.preview.send('themeChanged', {
                    theme: theme,
                    previousTheme: previousTheme
                });
            }
        },

        /**
         * Update meta theme-color
         */
        updateMetaThemeColor: function(theme) {
            const themeColors = {
                light: '#ffffff',
                dark: '#111827'
            };
            
            let $metaThemeColor = $('meta[name="theme-color"]');
            if (!$metaThemeColor.length) {
                $metaThemeColor = $('<meta name="theme-color">');
                $('head').append($metaThemeColor);
            }
            
            $metaThemeColor.attr('content', themeColors[theme] || themeColors.light);
        },

        /**
         * Update favicon based on theme
         */
        updateFavicon: function(theme) {
            if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.theme_favicons) {
                const favicons = recruitpro_theme.theme_favicons;
                if (favicons[theme]) {
                    $('link[rel="icon"]').attr('href', favicons[theme]);
                }
            }
        },

        /**
         * Update toggle button states
         */
        updateToggleStates: function() {
            const currentTheme = this.state.userPreference;
            const actualTheme = this.state.currentTheme;
            
            // Update main toggle
            $('.rp-theme-toggle-main').attr('data-current-theme', currentTheme);
            $('.current-theme-text').text(this.getThemeLabel(currentTheme));
            
            // Update theme options
            $('.rp-theme-option').removeClass('active');
            $(`.rp-theme-option[data-theme="${currentTheme}"]`).addClass('active');
            
            // Update ARIA labels
            $('.rp-theme-toggle').attr('aria-label', `Current theme: ${this.getThemeLabel(currentTheme)}. Click to change.`);
        },

        /**
         * Get theme label
         */
        getThemeLabel: function(theme) {
            const labels = {
                light: 'Light',
                dark: 'Dark',
                auto: 'Auto'
            };
            return labels[theme] || theme;
        },

        /**
         * Announce theme change for accessibility
         */
        announceThemeChange: function(theme) {
            const message = `Theme changed to ${this.getThemeLabel(theme)} mode`;
            
            if (this.cache.$announcement) {
                this.cache.$announcement.text(message);
                
                // Clear announcement after delay
                setTimeout(() => {
                    this.cache.$announcement.text('');
                }, this.config.announcementDuration);
            }
        },

        /**
         * Setup media query listener for system preference changes
         */
        setupMediaQueryListener: function() {
            if (!this.config.enableSystemDetection) return;
            
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            const handleChange = (e) => {
                this.state.systemPreference = e.matches ? 'dark' : 'light';
                
                // Update theme if user preference is auto
                if (this.state.userPreference === 'auto') {
                    this.applyTheme(this.state.systemPreference, true);
                    this.updateToggleStates();
                }
            };
            
            // Modern browsers
            if (mediaQuery.addEventListener) {
                mediaQuery.addEventListener('change', handleChange);
            } else {
                // Legacy browsers
                mediaQuery.addListener(handleChange);
            }
        },

        /**
         * Setup customizer integration
         */
        setupCustomizerIntegration: function() {
            if (!window.wp || !window.wp.customize) return;
            
            // Listen for customizer settings
            wp.customize.bind('ready', () => {
                // Add theme switcher control to customizer
                this.addCustomizerControl();
            });
        },

        /**
         * Add customizer control
         */
        addCustomizerControl: function() {
            if (!window.wp || !window.wp.customize) return;
            
            // This would integrate with WordPress customizer
            // Implementation depends on theme customizer structure
        },

        /**
         * Get stored preference
         */
        getStoredPreference: function() {
            try {
                return localStorage.getItem(this.config.storageKey) || 'auto';
            } catch (e) {
                return 'auto';
            }
        },

        /**
         * Public API methods
         */
        getTheme: function() {
            return {
                current: this.state.currentTheme,
                user: this.state.userPreference,
                system: this.state.systemPreference
            };
        },

        setThemePublic: function(theme) {
            this.setTheme(theme);
        },

        toggleTheme: function() {
            this.cycleTheme();
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RecruitProThemeSwitcher.init();
    });

    // Expose to global scope for external use
    window.RecruitProThemeSwitcher = RecruitProThemeSwitcher;

    // jQuery plugin interface
    $.fn.recruitProThemeSwitcher = function(options) {
        const settings = $.extend(RecruitProThemeSwitcher.config, options);
        RecruitProThemeSwitcher.config = settings;
        
        return this.each(function() {
            // Initialize theme switcher on specific elements
            // if needed for custom implementations
        });
    };

})(jQuery);

/**
 * CSS Transitions for theme switching
 * Applied via JavaScript for smooth transitions
 */
const themeTransitionCSS = `
    .rp-theme-transitioning,
    .rp-theme-transitioning *,
    .rp-theme-transitioning *:before,
    .rp-theme-transitioning *:after {
        transition: background-color 300ms ease-in-out,
                    color 300ms ease-in-out,
                    border-color 300ms ease-in-out,
                    box-shadow 300ms ease-in-out !important;
        transition-delay: 0s !important;
    }
`;

// Inject transition CSS
const style = document.createElement('style');
style.textContent = themeTransitionCSS;
document.head.appendChild(style);

/**
 * Utility functions for external use
 */
window.RecruitProThemeUtils = {
    
    /**
     * Check if user prefers dark mode
     */
    prefersDarkMode: function() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    },
    
    /**
     * Check if user prefers reduced motion
     */
    prefersReducedMotion: function() {
        return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    },
    
    /**
     * Get current theme
     */
    getCurrentTheme: function() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    },
    
    /**
     * Check if theme is dark
     */
    isDarkTheme: function() {
        return this.getCurrentTheme() === 'dark';
    },
    
    /**
     * Watch for theme changes
     */
    onThemeChange: function(callback) {
        if (typeof callback === 'function') {
            $(document).on('themeChanged', function(e, userTheme, actualTheme) {
                callback(userTheme, actualTheme);
            });
        }
    }
};