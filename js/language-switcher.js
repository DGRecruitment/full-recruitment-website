/**
 * Language Switcher Component - RecruitPro Theme
 * 
 * Handles interactive language switching functionality with hreflang support
 * Integrates with RecruitPro SEO Plugin for translation management
 * 
 * Features:
 * - Dropdown language selection
 * - Mobile-responsive interface
 * - Keyboard navigation support
 * - Accessibility features
 * - URL management for translations
 * - Integration with SEO plugin
 * - Analytics tracking for language switches
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Language switcher namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.LanguageSwitcher = window.RecruitPro.LanguageSwitcher || {};

    /**
     * Language Switcher Component
     */
    const LanguageSwitcher = {
        
        // Configuration
        config: {
            selectors: {
                switcher: '.language-switcher',
                toggle: '.language-toggle',
                menu: '.language-menu',
                item: '.language-item',
                link: '.language-link',
                select: '.language-select',
                mobileFlag: '.mobile-flag',
                desktopSwitcher: '.desktop-language',
                mobileSwitcher: '.mobile-language'
            },
            classes: {
                active: 'active',
                open: 'open',
                loading: 'loading',
                expanded: 'expanded',
                focused: 'focused',
                mobile: 'mobile-view',
                desktop: 'desktop-view'
            },
            animation: {
                duration: 200,
                easing: 'ease-out'
            },
            breakpoint: 768,
            closeDelay: 300
        },

        // State management
        state: {
            isOpen: false,
            isMobile: false,
            currentLanguage: null,
            availableLanguages: [],
            isLoading: false,
            touchStartY: 0,
            closeTimer: null
        },

        /**
         * Initialize the language switcher
         */
        init: function() {
            if (this.initialized) return;
            
            this.cacheElements();
            this.detectEnvironment();
            this.bindEvents();
            this.setupAccessibility();
            this.loadLanguageData();
            
            this.initialized = true;
            console.log('RecruitPro Language Switcher initialized');
        },

        /**
         * Cache DOM elements
         */
        cacheElements: function() {
            this.cache = {
                $window: $(window),
                $document: $(document),
                $body: $('body'),
                $switchers: $(this.config.selectors.switcher),
                $toggles: $(this.config.selectors.toggle),
                $menus: $(this.config.selectors.menu),
                $selects: $(this.config.selectors.select)
            };

            // Cache individual switcher elements
            this.cache.$switchers.each((index, element) => {
                const $switcher = $(element);
                $switcher.data('switcher-cache', {
                    $toggle: $switcher.find(this.config.selectors.toggle),
                    $menu: $switcher.find(this.config.selectors.menu),
                    $links: $switcher.find(this.config.selectors.link),
                    $select: $switcher.find(this.config.selectors.select)
                });
            });
        },

        /**
         * Detect mobile/desktop environment
         */
        detectEnvironment: function() {
            this.state.isMobile = this.cache.$window.width() < this.config.breakpoint;
            this.updateView();
        },

        /**
         * Update view based on screen size
         */
        updateView: function() {
            this.cache.$switchers.each((index, element) => {
                const $switcher = $(element);
                if (this.state.isMobile) {
                    $switcher.addClass(this.config.classes.mobile).removeClass(this.config.classes.desktop);
                } else {
                    $switcher.addClass(this.config.classes.desktop).removeClass(this.config.classes.mobile);
                }
            });
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Toggle button clicks
            this.cache.$document.on('click', this.config.selectors.toggle, function(e) {
                e.preventDefault();
                e.stopPropagation();
                const $switcher = $(this).closest(self.config.selectors.switcher);
                self.toggleDropdown($switcher);
            });

            // Language link clicks
            this.cache.$document.on('click', this.config.selectors.link, function(e) {
                e.preventDefault();
                const $link = $(this);
                const $switcher = $link.closest(self.config.selectors.switcher);
                self.switchLanguage($link, $switcher);
            });

            // Mobile select changes
            this.cache.$document.on('change', this.config.selectors.select, function(e) {
                const url = $(this).val();
                if (url) {
                    self.navigateToLanguage(url, $(this).find(':selected').data('hreflang'));
                }
            });

            // Keyboard navigation
            this.cache.$document.on('keydown', this.config.selectors.switcher, (e) => {
                this.handleKeyboard(e);
            });

            // Click outside to close
            this.cache.$document.on('click', (e) => {
                if (!$(e.target).closest(this.config.selectors.switcher).length) {
                    this.closeAllDropdowns();
                }
            });

            // Window resize
            this.cache.$window.on('resize', this.utils.debounce(() => {
                this.detectEnvironment();
            }, 250));

            // Mouse enter/leave for hover behavior
            this.cache.$document.on('mouseenter', this.config.selectors.switcher, function() {
                if (!self.state.isMobile) {
                    clearTimeout(self.state.closeTimer);
                }
            });

            this.cache.$document.on('mouseleave', this.config.selectors.switcher, function() {
                if (!self.state.isMobile) {
                    self.state.closeTimer = setTimeout(() => {
                        self.closeDropdown($(this));
                    }, self.config.closeDelay);
                }
            });

            // Focus events for accessibility
            this.cache.$document.on('focus', this.config.selectors.link, function() {
                $(this).closest(self.config.selectors.switcher).addClass(self.config.classes.focused);
            });

            this.cache.$document.on('blur', this.config.selectors.link, function() {
                setTimeout(() => {
                    const $switcher = $(this).closest(self.config.selectors.switcher);
                    if (!$switcher.find(':focus').length) {
                        $switcher.removeClass(self.config.classes.focused);
                        self.closeDropdown($switcher);
                    }
                }, 10);
            });

            // Touch events for mobile
            if ('ontouchstart' in window) {
                this.bindTouchEvents();
            }
        },

        /**
         * Bind touch events for mobile interaction
         */
        bindTouchEvents: function() {
            this.cache.$document.on('touchstart', this.config.selectors.menu, (e) => {
                this.state.touchStartY = e.originalEvent.touches[0].clientY;
            });

            this.cache.$document.on('touchmove', this.config.selectors.menu, (e) => {
                // Prevent body scroll when scrolling in dropdown
                const currentY = e.originalEvent.touches[0].clientY;
                const deltaY = this.state.touchStartY - currentY;
                
                const $menu = $(e.currentTarget);
                const scrollTop = $menu.scrollTop();
                const scrollHeight = $menu[0].scrollHeight;
                const height = $menu.height();

                if ((scrollTop === 0 && deltaY < 0) || 
                    (scrollTop + height >= scrollHeight && deltaY > 0)) {
                    e.preventDefault();
                }
            });
        },

        /**
         * Toggle dropdown open/close
         */
        toggleDropdown: function($switcher) {
            const cache = $switcher.data('switcher-cache');
            if (!cache) return;

            const isOpen = cache.$toggle.attr('aria-expanded') === 'true';
            
            if (isOpen) {
                this.closeDropdown($switcher);
            } else {
                this.openDropdown($switcher);
            }
        },

        /**
         * Open dropdown
         */
        openDropdown: function($switcher) {
            const cache = $switcher.data('switcher-cache');
            if (!cache) return;

            // Close other dropdowns first
            this.closeAllDropdowns();

            // Open this dropdown
            cache.$toggle.attr('aria-expanded', 'true');
            cache.$menu.addClass(this.config.classes.open);
            $switcher.addClass(this.config.classes.active);
            
            this.state.isOpen = true;

            // Focus first link for keyboard navigation
            cache.$links.first().focus();

            // Trigger custom event
            $switcher.trigger('recruitpro:language-switcher:opened');
        },

        /**
         * Close dropdown
         */
        closeDropdown: function($switcher) {
            const cache = $switcher.data('switcher-cache');
            if (!cache) return;

            cache.$toggle.attr('aria-expanded', 'false');
            cache.$menu.removeClass(this.config.classes.open);
            $switcher.removeClass(this.config.classes.active);
            
            this.state.isOpen = false;

            // Trigger custom event
            $switcher.trigger('recruitpro:language-switcher:closed');
        },

        /**
         * Close all dropdowns
         */
        closeAllDropdowns: function() {
            this.cache.$switchers.each((index, element) => {
                this.closeDropdown($(element));
            });
        },

        /**
         * Switch to selected language
         */
        switchLanguage: function($link, $switcher) {
            const url = $link.attr('href');
            const hreflang = $link.attr('hreflang');
            const languageCode = $link.attr('lang');
            
            if (!url) return;

            // Add loading state
            this.setLoadingState($switcher, true);

            // Track language switch
            this.trackLanguageSwitch(languageCode, hreflang);

            // Navigate to new language
            this.navigateToLanguage(url, hreflang);
        },

        /**
         * Navigate to language URL
         */
        navigateToLanguage: function(url, hreflang) {
            // Store language preference
            this.storeLanguagePreference(hreflang);

            // Add transition class for smooth experience
            this.cache.$body.addClass('page-transitioning');

            // Navigate
            window.location.href = url;
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboard: function(e) {
            const $switcher = $(e.currentTarget);
            const cache = $switcher.data('switcher-cache');
            if (!cache) return;

            const isOpen = cache.$toggle.attr('aria-expanded') === 'true';

            switch (e.keyCode) {
                case 13: // Enter
                case 32: // Space
                    if ($(e.target).is(cache.$toggle)) {
                        e.preventDefault();
                        this.toggleDropdown($switcher);
                    }
                    break;
                    
                case 27: // Escape
                    if (isOpen) {
                        e.preventDefault();
                        this.closeDropdown($switcher);
                        cache.$toggle.focus();
                    }
                    break;
                    
                case 38: // Up arrow
                    if (isOpen) {
                        e.preventDefault();
                        this.navigateLinks($switcher, 'up');
                    }
                    break;
                    
                case 40: // Down arrow
                    if (isOpen) {
                        e.preventDefault();
                        this.navigateLinks($switcher, 'down');
                    } else if ($(e.target).is(cache.$toggle)) {
                        e.preventDefault();
                        this.openDropdown($switcher);
                    }
                    break;
                    
                case 36: // Home
                    if (isOpen) {
                        e.preventDefault();
                        cache.$links.first().focus();
                    }
                    break;
                    
                case 35: // End
                    if (isOpen) {
                        e.preventDefault();
                        cache.$links.last().focus();
                    }
                    break;
            }
        },

        /**
         * Navigate between language links with keyboard
         */
        navigateLinks: function($switcher, direction) {
            const cache = $switcher.data('switcher-cache');
            if (!cache) return;

            const $focused = cache.$links.filter(':focus');
            const $links = cache.$links;
            const currentIndex = $links.index($focused);
            
            let newIndex;
            if (direction === 'up') {
                newIndex = currentIndex > 0 ? currentIndex - 1 : $links.length - 1;
            } else {
                newIndex = currentIndex < $links.length - 1 ? currentIndex + 1 : 0;
            }
            
            $links.eq(newIndex).focus();
        },

        /**
         * Set loading state
         */
        setLoadingState: function($switcher, loading) {
            if (loading) {
                $switcher.addClass(this.config.classes.loading);
                this.state.isLoading = true;
            } else {
                $switcher.removeClass(this.config.classes.loading);
                this.state.isLoading = false;
            }
        },

        /**
         * Load language data from server
         */
        loadLanguageData: function() {
            // Check if language data is available globally
            if (typeof recruitpro_language_data !== 'undefined') {
                this.state.currentLanguage = recruitpro_language_data.current;
                this.state.availableLanguages = recruitpro_language_data.available;
                this.updateLanguageDisplay();
            }
        },

        /**
         * Update language display based on current data
         */
        updateLanguageDisplay: function() {
            if (!this.state.currentLanguage) return;

            this.cache.$switchers.each((index, element) => {
                const $switcher = $(element);
                const cache = $switcher.data('switcher-cache');
                
                if (cache && cache.$toggle) {
                    // Update current language display
                    const $currentCode = cache.$toggle.find('.language-code');
                    if ($currentCode.length) {
                        $currentCode.text(this.state.currentLanguage.code.toUpperCase());
                    }
                }
            });
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            this.cache.$switchers.each((index, element) => {
                const $switcher = $(element);
                const cache = $switcher.data('switcher-cache');
                
                if (cache) {
                    // Add ARIA attributes
                    cache.$toggle.attr({
                        'aria-expanded': 'false',
                        'aria-haspopup': 'menu'
                    });

                    cache.$menu.attr({
                        'role': 'menu',
                        'aria-label': 'Language selection'
                    });

                    cache.$links.attr('role', 'menuitem');

                    // Add unique IDs for aria-labelledby
                    const switcherId = 'language-switcher-' + index;
                    cache.$toggle.attr('id', switcherId + '-button');
                    cache.$menu.attr('aria-labelledby', switcherId + '-button');
                }
            });
        },

        /**
         * Store language preference
         */
        storeLanguagePreference: function(hreflang) {
            try {
                if (localStorage) {
                    localStorage.setItem('recruitpro_preferred_language', hreflang);
                }
                
                // Also set a cookie as fallback
                document.cookie = `recruitpro_lang=${hreflang}; path=/; max-age=${30 * 24 * 60 * 60}`;
            } catch (e) {
                console.warn('Could not store language preference:', e);
            }
        },

        /**
         * Track language switch for analytics
         */
        trackLanguageSwitch: function(languageCode, hreflang) {
            // Google Analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'language_switch', {
                    'language_code': languageCode,
                    'hreflang': hreflang,
                    'page_url': window.location.href
                });
            }

            // Custom event for other tracking systems
            this.cache.$document.trigger('recruitpro:language-switched', {
                languageCode: languageCode,
                hreflang: hreflang,
                previousLanguage: this.state.currentLanguage
            });
        },

        /**
         * Utility functions
         */
        utils: {
            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            throttle: function(func, limit) {
                let inThrottle;
                return function() {
                    const args = arguments;
                    const context = this;
                    if (!inThrottle) {
                        func.apply(context, args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                };
            }
        },

        /**
         * Public API methods
         */
        api: {
            open: function(switcherId) {
                const $switcher = switcherId ? $('#' + switcherId) : LanguageSwitcher.cache.$switchers.first();
                if ($switcher.length) {
                    LanguageSwitcher.openDropdown($switcher);
                }
            },

            close: function(switcherId) {
                if (switcherId) {
                    const $switcher = $('#' + switcherId);
                    if ($switcher.length) {
                        LanguageSwitcher.closeDropdown($switcher);
                    }
                } else {
                    LanguageSwitcher.closeAllDropdowns();
                }
            },

            refresh: function() {
                LanguageSwitcher.loadLanguageData();
                LanguageSwitcher.updateLanguageDisplay();
            },

            getCurrentLanguage: function() {
                return LanguageSwitcher.state.currentLanguage;
            },

            getAvailableLanguages: function() {
                return LanguageSwitcher.state.availableLanguages;
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        LanguageSwitcher.init();
    });

    // Expose public API
    window.RecruitPro.LanguageSwitcher = LanguageSwitcher.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        LanguageSwitcher.cacheElements();
        LanguageSwitcher.setupAccessibility();
    });

    // Handle page visibility changes
    $(document).on('visibilitychange', function() {
        if (document.hidden) {
            LanguageSwitcher.closeAllDropdowns();
        }
    });

})(jQuery);