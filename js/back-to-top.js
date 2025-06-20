/**
 * Back to Top Component - RecruitPro Theme
 * 
 * Smooth scrolling back-to-top functionality for recruitment websites
 * Optimized for long job listings and content pages
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Back to Top Manager
     */
    const RecruitProBackToTop = {
        
        // Configuration
        config: {
            // Scroll position to show button (pixels)
            showAfter: 300,
            
            // Animation duration for scroll (milliseconds)
            scrollDuration: 600,
            
            // Animation easing
            scrollEasing: 'swing',
            
            // Button position
            position: {
                bottom: 30,
                right: 30
            },
            
            // Button styling
            buttonSize: 50,
            iconSize: 20,
            
            // Throttle delay for scroll events (milliseconds)
            throttleDelay: 100,
            
            // Auto-hide delay when not scrolling (milliseconds)
            autoHideDelay: 3000,
            
            // Mobile specific settings
            mobile: {
                showAfter: 200,
                position: {
                    bottom: 20,
                    right: 20
                },
                buttonSize: 45
            }
        },

        // State management
        state: {
            isVisible: false,
            isScrolling: false,
            isMobile: false,
            lastScrollTop: 0,
            scrollDirection: 'down',
            hideTimeout: null,
            isInitialized: false
        },

        // Cache DOM elements
        cache: {
            $window: $(window),
            $document: $(document),
            $body: $('body'),
            $html: $('html'),
            $button: null,
            $progressRing: null
        },

        /**
         * Initialize back to top functionality
         */
        init: function() {
            if (this.state.isInitialized) return;
            
            this.detectEnvironment();
            this.createButton();
            this.bindEvents();
            this.setupAccessibility();
            
            this.state.isInitialized = true;
            
            console.log('RecruitPro Back to Top initialized');
        },

        /**
         * Detect environment and device capabilities
         */
        detectEnvironment: function() {
            this.state.isMobile = this.cache.$window.width() < 768;
            
            // Detect reduced motion preference
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.config.scrollDuration = 0;
                this.cache.$body.addClass('reduce-motion');
            }
            
            // Detect touch device
            if ('ontouchstart' in window) {
                this.cache.$body.addClass('touch-device');
            }
        },

        /**
         * Create back to top button
         */
        createButton: function() {
            const isMobile = this.state.isMobile;
            const config = isMobile ? this.config.mobile : this.config;
            
            // Create button HTML
            const buttonHTML = `
                <button type="button" 
                        id="back-to-top" 
                        class="back-to-top-btn" 
                        aria-label="Back to top"
                        title="Back to top"
                        style="display: none;">
                    <div class="btn-content">
                        <div class="progress-ring">
                            <svg class="progress-ring-svg" width="${config.buttonSize}" height="${config.buttonSize}">
                                <circle class="progress-ring-circle-bg" 
                                        cx="${config.buttonSize / 2}" 
                                        cy="${config.buttonSize / 2}" 
                                        r="${(config.buttonSize / 2) - 3}"></circle>
                                <circle class="progress-ring-circle" 
                                        cx="${config.buttonSize / 2}" 
                                        cy="${config.buttonSize / 2}" 
                                        r="${(config.buttonSize / 2) - 3}"></circle>
                            </svg>
                        </div>
                        <div class="btn-icon">
                            <svg width="${config.iconSize || 20}" height="${config.iconSize || 20}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m18 15-6-6-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <span class="sr-only">Scroll to top of page</span>
                </button>
            `;

            // Add button to page
            this.cache.$body.append(buttonHTML);
            this.cache.$button = $('#back-to-top');
            this.cache.$progressRing = this.cache.$button.find('.progress-ring-circle');
            
            // Apply styling
            this.styleButton();
            
            // Initialize progress ring
            this.initProgressRing();
        },

        /**
         * Apply CSS styling to button
         */
        styleButton: function() {
            const isMobile = this.state.isMobile;
            const config = isMobile ? this.config.mobile : this.config;
            
            this.cache.$button.css({
                position: 'fixed',
                bottom: config.position.bottom + 'px',
                right: config.position.right + 'px',
                width: config.buttonSize + 'px',
                height: config.buttonSize + 'px',
                borderRadius: '50%',
                backgroundColor: '#2563eb',
                color: '#ffffff',
                border: 'none',
                cursor: 'pointer',
                zIndex: 9999,
                boxShadow: '0 4px 12px rgba(37, 99, 235, 0.3)',
                transition: 'all 0.3s ease',
                display: 'none'
            });

            // Hover effects
            this.cache.$button.hover(
                function() {
                    $(this).css({
                        backgroundColor: '#1d4ed8',
                        transform: 'translateY(-2px)',
                        boxShadow: '0 6px 16px rgba(37, 99, 235, 0.4)'
                    });
                },
                function() {
                    $(this).css({
                        backgroundColor: '#2563eb',
                        transform: 'translateY(0)',
                        boxShadow: '0 4px 12px rgba(37, 99, 235, 0.3)'
                    });
                }
            );

            // Style the progress ring
            this.cache.$button.find('.progress-ring-circle-bg').css({
                fill: 'none',
                stroke: 'rgba(255, 255, 255, 0.2)',
                strokeWidth: '2'
            });

            this.cache.$button.find('.progress-ring-circle').css({
                fill: 'none',
                stroke: '#ffffff',
                strokeWidth: '2',
                strokeLinecap: 'round',
                transform: 'rotate(-90deg)',
                transformOrigin: '50% 50%',
                transition: 'stroke-dashoffset 0.1s ease'
            });
        },

        /**
         * Initialize progress ring
         */
        initProgressRing: function() {
            const radius = (this.config.buttonSize / 2) - 3;
            const circumference = radius * 2 * Math.PI;
            
            this.cache.$progressRing.css({
                strokeDasharray: circumference + ' ' + circumference,
                strokeDashoffset: circumference
            });
            
            this.progressRingCircumference = circumference;
        },

        /**
         * Update progress ring based on scroll position
         */
        updateProgressRing: function(scrollPercent) {
            if (!this.cache.$progressRing.length) return;
            
            const offset = this.progressRingCircumference - (scrollPercent / 100) * this.progressRingCircumference;
            this.cache.$progressRing.css('strokeDashoffset', offset);
        },

        /**
         * Bind all events
         */
        bindEvents: function() {
            this.bindScrollEvents();
            this.bindClickEvents();
            this.bindResizeEvents();
            this.bindKeyboardEvents();
            this.bindAccessibilityEvents();
        },

        /**
         * Bind scroll events
         */
        bindScrollEvents: function() {
            const self = this;
            
            // Throttled scroll handler
            this.cache.$window.on('scroll', this.throttle(function() {
                self.handleScroll();
            }, this.config.throttleDelay));
            
            // Scroll start event
            this.cache.$window.on('scroll', function() {
                self.state.isScrolling = true;
                clearTimeout(self.state.hideTimeout);
            });
            
            // Scroll stop event (using timeout)
            this.cache.$window.on('scroll', function() {
                clearTimeout(self.scrollStopTimeout);
                self.scrollStopTimeout = setTimeout(function() {
                    self.state.isScrolling = false;
                    self.handleScrollStop();
                }, 150);
            });
        },

        /**
         * Handle scroll events
         */
        handleScroll: function() {
            const scrollTop = this.cache.$window.scrollTop();
            const documentHeight = this.cache.$document.height();
            const windowHeight = this.cache.$window.height();
            const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
            
            // Determine scroll direction
            if (scrollTop > this.state.lastScrollTop) {
                this.state.scrollDirection = 'down';
            } else {
                this.state.scrollDirection = 'up';
            }
            this.state.lastScrollTop = scrollTop;
            
            // Update progress ring
            this.updateProgressRing(Math.min(scrollPercent, 100));
            
            // Show/hide button logic
            const showThreshold = this.state.isMobile ? this.config.mobile.showAfter : this.config.showAfter;
            
            if (scrollTop > showThreshold) {
                this.showButton();
            } else {
                this.hideButton();
            }
            
            // Auto-hide when scrolling down on mobile
            if (this.state.isMobile && this.state.scrollDirection === 'down' && this.state.isVisible) {
                this.cache.$button.addClass('scrolling-down');
            } else {
                this.cache.$button.removeClass('scrolling-down');
            }
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:back-to-top-scroll', [scrollTop, scrollPercent]);
        },

        /**
         * Handle scroll stop
         */
        handleScrollStop: function() {
            // Auto-hide button after inactivity (optional)
            if (this.config.autoHideDelay > 0 && this.state.isVisible) {
                this.state.hideTimeout = setTimeout(() => {
                    if (!this.state.isScrolling) {
                        this.cache.$button.addClass('auto-hide');
                    }
                }, this.config.autoHideDelay);
            }
        },

        /**
         * Bind click events
         */
        bindClickEvents: function() {
            const self = this;
            
            // Button click
            this.cache.$button.on('click', function(e) {
                e.preventDefault();
                self.scrollToTop();
            });
            
            // Remove auto-hide on interaction
            this.cache.$button.on('mouseenter focus', function() {
                $(this).removeClass('auto-hide');
                clearTimeout(self.state.hideTimeout);
            });
        },

        /**
         * Bind resize events
         */
        bindResizeEvents: function() {
            const self = this;
            
            this.cache.$window.on('resize', this.debounce(function() {
                self.handleResize();
            }, 250));
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            const wasMobile = this.state.isMobile;
            this.detectEnvironment();
            
            // Recreate button if mobile state changed
            if (wasMobile !== this.state.isMobile) {
                this.cache.$button.remove();
                this.createButton();
            }
        },

        /**
         * Bind keyboard events
         */
        bindKeyboardEvents: function() {
            const self = this;
            
            // Keyboard shortcuts
            this.cache.$document.on('keydown', function(e) {
                // Ctrl/Cmd + Home: Scroll to top
                if ((e.ctrlKey || e.metaKey) && e.keyCode === 36) {
                    e.preventDefault();
                    self.scrollToTop();
                }
                
                // End key: Show back to top button
                if (e.keyCode === 35) {
                    setTimeout(() => {
                        if (self.cache.$window.scrollTop() > self.config.showAfter) {
                            self.cache.$button.focus();
                        }
                    }, 100);
                }
            });
            
            // Button keyboard interaction
            this.cache.$button.on('keydown', function(e) {
                if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
                    e.preventDefault();
                    self.scrollToTop();
                }
            });
        },

        /**
         * Bind accessibility events
         */
        bindAccessibilityEvents: function() {
            const self = this;
            
            // Focus management
            this.cache.$button.on('focus', function() {
                $(this).addClass('focused');
                
                // Announce to screen readers
                if (window.RecruitProAccessibility) {
                    window.RecruitProAccessibility.announceToScreenReader('Back to top button focused');
                }
            });
            
            this.cache.$button.on('blur', function() {
                $(this).removeClass('focused');
            });
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // ARIA attributes
            this.cache.$button.attr({
                'role': 'button',
                'tabindex': '0',
                'aria-label': 'Scroll to top of page',
                'title': 'Back to top (Ctrl+Home)'
            });
            
            // Add to skip links if accessibility module exists
            if (window.RecruitProAccessibility && $('.skip-links').length) {
                $('.skip-links').append('<a href="#top" class="skip-link">Back to top</a>');
            }
        },

        /**
         * Show button with animation
         */
        showButton: function() {
            if (this.state.isVisible) return;
            
            this.state.isVisible = true;
            
            // CSS animation approach
            this.cache.$button.css('display', 'block').addClass('visible');
            
            // jQuery fallback
            if (!this.cache.$button.hasClass('visible')) {
                this.cache.$button.fadeIn(300);
            }
            
            // Remove auto-hide class
            this.cache.$button.removeClass('auto-hide');
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:back-to-top-show');
        },

        /**
         * Hide button with animation
         */
        hideButton: function() {
            if (!this.state.isVisible) return;
            
            this.state.isVisible = false;
            
            // CSS animation approach
            this.cache.$button.removeClass('visible');
            
            // jQuery fallback
            setTimeout(() => {
                if (!this.cache.$button.hasClass('visible')) {
                    this.cache.$button.fadeOut(300);
                }
            }, 300);
            
            // Clear timeouts
            clearTimeout(this.state.hideTimeout);
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:back-to-top-hide');
        },

        /**
         * Scroll to top with smooth animation
         */
        scrollToTop: function() {
            // Announce to screen readers
            if (window.RecruitProAccessibility) {
                window.RecruitProAccessibility.announceToScreenReader('Scrolling to top of page');
            }
            
            // Add scrolling state
            this.cache.$button.addClass('scrolling');
            this.cache.$body.addClass('scrolling-to-top');
            
            // Smooth scroll animation
            const scrollTarget = 0;
            const duration = this.config.scrollDuration;
            
            if (duration > 0) {
                $('html, body').animate({
                    scrollTop: scrollTarget
                }, {
                    duration: duration,
                    easing: this.config.scrollEasing,
                    complete: () => {
                        this.onScrollComplete();
                    }
                });
            } else {
                // Instant scroll for reduced motion
                this.cache.$window.scrollTop(scrollTarget);
                this.onScrollComplete();
            }
            
            // Focus management for accessibility
            this.focusTopElement();
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:back-to-top-click');
        },

        /**
         * Handle scroll completion
         */
        onScrollComplete: function() {
            // Remove scrolling state
            this.cache.$button.removeClass('scrolling');
            this.cache.$body.removeClass('scrolling-to-top');
            
            // Hide button after reaching top
            setTimeout(() => {
                this.hideButton();
            }, 500);
            
            // Announce completion to screen readers
            if (window.RecruitProAccessibility) {
                window.RecruitProAccessibility.announceToScreenReader('Reached top of page');
            }
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:back-to-top-complete');
        },

        /**
         * Focus top element for accessibility
         */
        focusTopElement: function() {
            // Try to focus skip link or main heading
            const $focusTarget = $('.skip-link').first() || $('h1').first() || $('#main-content');
            
            if ($focusTarget.length) {
                setTimeout(() => {
                    $focusTarget.focus();
                }, this.config.scrollDuration + 100);
            }
        },

        /**
         * Throttle function for performance
         */
        throttle: function(func, wait) {
            let inThrottle;
            return function executedFunction(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, wait);
                }
            };
        },

        /**
         * Debounce function for performance
         */
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

        /**
         * Destroy back to top functionality
         */
        destroy: function() {
            if (!this.state.isInitialized) return;
            
            // Remove event listeners
            this.cache.$window.off('scroll resize');
            this.cache.$document.off('keydown');
            
            // Remove button
            if (this.cache.$button) {
                this.cache.$button.remove();
            }
            
            // Clear timeouts
            clearTimeout(this.state.hideTimeout);
            clearTimeout(this.scrollStopTimeout);
            
            // Reset state
            this.state.isInitialized = false;
            
            console.log('RecruitPro Back to Top destroyed');
        },

        /**
         * Update configuration
         */
        updateConfig: function(newConfig) {
            this.config = $.extend(true, this.config, newConfig);
            
            // Recreate button with new config
            if (this.state.isInitialized) {
                this.cache.$button.remove();
                this.createButton();
            }
        },

        /**
         * Get current state
         */
        getState: function() {
            return {
                isVisible: this.state.isVisible,
                isScrolling: this.state.isScrolling,
                scrollDirection: this.state.scrollDirection,
                lastScrollTop: this.state.lastScrollTop
            };
        },

        /**
         * Public API methods
         */
        show: function() {
            this.showButton();
        },

        hide: function() {
            this.hideButton();
        },

        scrollTop: function() {
            this.scrollToTop();
        },

        isVisible: function() {
            return this.state.isVisible;
        }
    };

    /**
     * CSS Styles (injected via JavaScript)
     */
    const injectStyles = function() {
        const styles = `
            <style id="back-to-top-styles">
                .back-to-top-btn {
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(20px) scale(0.8);
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }
                
                .back-to-top-btn.visible {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(0) scale(1);
                }
                
                .back-to-top-btn.auto-hide {
                    opacity: 0.6;
                }
                
                .back-to-top-btn.scrolling-down {
                    transform: translateY(10px);
                    opacity: 0.8;
                }
                
                .back-to-top-btn.scrolling {
                    animation: pulse 0.6s infinite;
                }
                
                .back-to-top-btn.focused {
                    outline: 2px solid #ffffff;
                    outline-offset: 2px;
                }
                
                .back-to-top-btn .btn-content {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    height: 100%;
                }
                
                .back-to-top-btn .progress-ring {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }
                
                .back-to-top-btn .btn-icon {
                    position: relative;
                    z-index: 2;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }
                
                /* High contrast mode */
                .high-contrast-mode .back-to-top-btn {
                    background-color: #000000 !important;
                    color: #ffffff !important;
                    border: 2px solid #ffffff !important;
                }
                
                /* Reduced motion */
                .reduce-motion .back-to-top-btn {
                    transition: none !important;
                    animation: none !important;
                }
                
                /* Mobile specific */
                @media (max-width: 767px) {
                    .back-to-top-btn.scrolling-down {
                        transform: translateX(100px);
                    }
                }
                
                /* Print styles */
                @media print {
                    .back-to-top-btn {
                        display: none !important;
                    }
                }
            </style>
        `;
        
        if (!$('#back-to-top-styles').length) {
            $('head').append(styles);
        }
    };

    /**
     * Expose to global scope
     */
    window.RecruitProBackToTop = RecruitProBackToTop;

    /**
     * Auto-initialize when DOM is ready
     */
    $(document).ready(function() {
        injectStyles();
        RecruitProBackToTop.init();
    });

    /**
     * Integration with main theme
     */
    if (window.RecruitPro) {
        window.RecruitPro.backToTop = RecruitProBackToTop;
    }

})(jQuery);

/**
 * Back to Top Features Summary:
 * 
 * ✅ Smooth Scrolling Animation
 * ✅ Progress Ring Indicator
 * ✅ Mobile-Responsive Design
 * ✅ Accessibility Support (WCAG 2.1)
 * ✅ Keyboard Navigation
 * ✅ Screen Reader Support
 * ✅ Auto-Show/Hide Logic
 * ✅ Customizable Configuration
 * ✅ Performance Optimized
 * ✅ Touch Device Support
 * ✅ High Contrast Mode
 * ✅ Reduced Motion Support
 * ✅ Integration Ready
 * ✅ Event System
 * ✅ Recruitment Website Optimized
 */