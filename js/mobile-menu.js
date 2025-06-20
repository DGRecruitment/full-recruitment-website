/**
 * Mobile Menu Component - RecruitPro Theme
 * 
 * Advanced mobile navigation system with touch gestures, accessibility features,
 * and smooth animations for recruitment agency websites
 * 
 * Features:
 * - Hamburger menu with smooth animations
 * - Touch/swipe gesture support
 * - Submenu handling with accordion behavior
 * - Full accessibility support (ARIA, keyboard navigation)
 * - Performance optimized animations
 * - Auto-close on outside tap/click
 * - Responsive breakpoint management
 * - Integration with header scroll behavior
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Mobile menu namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.MobileMenu = window.RecruitPro.MobileMenu || {};

    /**
     * Mobile Menu Component
     */
    const MobileMenu = {
        
        // Configuration
        config: {
            selectors: {
                menuToggle: '.mobile-menu-toggle',
                hamburger: '.hamburger',
                hamburgerLine: '.hamburger-line',
                mobileNav: '.mobile-navigation',
                mainNav: '.main-navigation',
                menuItem: '.menu-item',
                menuLink: '.menu-item > a',
                subMenu: '.sub-menu',
                menuItemHasChildren: '.menu-item-has-children',
                menuOverlay: '.mobile-menu-overlay',
                searchToggle: '.mobile-search-toggle',
                searchForm: '.mobile-search-form',
                langSwitcher: '.mobile-language-switcher'
            },
            classes: {
                active: 'active',
                open: 'menu-open',
                visible: 'visible',
                expanded: 'expanded',
                collapsed: 'collapsed',
                animating: 'animating',
                noScroll: 'no-scroll',
                hamburgerActive: 'hamburger-active',
                submenuOpen: 'submenu-open',
                touchDevice: 'touch-device'
            },
            breakpoint: 1024,
            animation: {
                duration: 300,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                stagger: 50
            },
            touch: {
                threshold: 50,
                maxTime: 300,
                enabled: true
            },
            aria: {
                expanded: 'aria-expanded',
                hidden: 'aria-hidden',
                label: 'aria-label',
                controls: 'aria-controls'
            }
        },

        // State management
        state: {
            isOpen: false,
            isMobile: false,
            isAnimating: false,
            activeSubmenu: null,
            touchStartX: 0,
            touchStartY: 0,
            touchStartTime: 0,
            lastFocusedElement: null,
            hasTouch: false,
            scrollY: 0
        },

        /**
         * Initialize the mobile menu system
         */
        init: function() {
            if (this.initialized) return;
            
            this.detectTouch();
            this.cacheElements();
            this.createMobileElements();
            this.bindEvents();
            this.setupAccessibility();
            this.detectBreakpoint();
            
            this.initialized = true;
            console.log('RecruitPro Mobile Menu initialized');
        },

        /**
         * Detect touch device capabilities
         */
        detectTouch: function() {
            this.state.hasTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            
            if (this.state.hasTouch) {
                $('html').addClass(this.config.classes.touchDevice);
            }
        },

        /**
         * Cache DOM elements
         */
        cacheElements: function() {
            this.cache = {
                $window: $(window),
                $document: $(document),
                $body: $('body'),
                $html: $('html'),
                $header: $('.header-main'),
                $menuToggle: $(this.config.selectors.menuToggle),
                $hamburger: $(this.config.selectors.hamburger),
                $mobileNav: $(this.config.selectors.mobileNav),
                $mainNav: $(this.config.selectors.mainNav),
                $menuItems: null,
                $subMenus: null,
                $menuOverlay: null
            };

            // Cache menu items after mobile nav is created
            this.cacheMenuElements();
        },

        /**
         * Cache menu-specific elements
         */
        cacheMenuElements: function() {
            this.cache.$menuItems = this.cache.$mobileNav.find(this.config.selectors.menuItem);
            this.cache.$subMenus = this.cache.$mobileNav.find(this.config.selectors.subMenu);
            this.cache.$menuOverlay = $(this.config.selectors.menuOverlay);
        },

        /**
         * Create mobile menu elements if they don't exist
         */
        createMobileElements: function() {
            // Create mobile menu toggle if it doesn't exist
            if (!this.cache.$menuToggle.length) {
                this.createMobileToggle();
            }

            // Create mobile navigation structure
            if (!this.cache.$mobileNav.length) {
                this.createMobileNavigation();
            }

            // Create overlay
            if (!this.cache.$menuOverlay.length) {
                this.createMenuOverlay();
            }

            // Update cache after creation
            this.cacheElements();
        },

        /**
         * Create mobile menu toggle button
         */
        createMobileToggle: function() {
            const toggleHTML = `
                <button class="mobile-menu-toggle" 
                        aria-expanded="false"
                        aria-controls="mobile-navigation"
                        aria-label="Toggle mobile menu">
                    <span class="hamburger">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                    <span class="menu-text">
                        <span class="menu-text-open">Menu</span>
                        <span class="menu-text-close">Close</span>
                    </span>
                </button>
            `;

            // Insert toggle button in header
            if (this.cache.$header.length) {
                this.cache.$header.find('.header-container').append(toggleHTML);
            } else {
                $('body').prepend('<div class="mobile-menu-container">' + toggleHTML + '</div>');
            }
        },

        /**
         * Create mobile navigation structure
         */
        createMobileNavigation: function() {
            // Clone main navigation
            const $mainNavClone = this.cache.$mainNav.clone();
            
            const mobileNavHTML = `
                <nav class="mobile-navigation" 
                     id="mobile-navigation"
                     role="navigation"
                     aria-label="Mobile Navigation"
                     aria-hidden="true">
                    <div class="mobile-nav-header">
                        <div class="mobile-nav-branding">
                            <img src="${this.getMobileLogo()}" alt="Logo" class="mobile-logo">
                        </div>
                        <button class="mobile-nav-close" aria-label="Close mobile menu">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="mobile-nav-content">
                        <div class="mobile-nav-menu">
                            ${$mainNavClone.html() || this.getDefaultMenuHTML()}
                        </div>
                        <div class="mobile-nav-actions">
                            <div class="mobile-search">
                                <button class="mobile-search-toggle" aria-label="Toggle search">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.35-4.35"></path>
                                    </svg>
                                    <span>Search</span>
                                </button>
                                <form class="mobile-search-form" role="search" aria-hidden="true">
                                    <input type="search" placeholder="Search..." aria-label="Search">
                                    <button type="submit" aria-label="Submit search">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <path d="m21 21-4.35-4.35"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <div class="mobile-cta">
                                <a href="/contact" class="btn btn-primary">Get Started</a>
                            </div>
                        </div>
                    </div>
                </nav>
            `;

            $('body').append(mobileNavHTML);
        },

        /**
         * Create menu overlay
         */
        createMenuOverlay: function() {
            const overlayHTML = `
                <div class="mobile-menu-overlay" aria-hidden="true"></div>
            `;
            $('body').append(overlayHTML);
        },

        /**
         * Get mobile logo URL
         */
        getMobileLogo: function() {
            const $logo = $('.site-logo img, .header-logo img').first();
            return $logo.length ? $logo.attr('src') : '';
        },

        /**
         * Get default menu HTML if no main navigation exists
         */
        getDefaultMenuHTML: function() {
            return `
                <ul class="mobile-menu">
                    <li class="menu-item"><a href="/">Home</a></li>
                    <li class="menu-item"><a href="/jobs">Jobs</a></li>
                    <li class="menu-item"><a href="/about">About</a></li>
                    <li class="menu-item"><a href="/contact">Contact</a></li>
                </ul>
            `;
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Menu toggle button click
            this.cache.$document.on('click', this.config.selectors.menuToggle, (e) => {
                e.preventDefault();
                this.toggle();
            });

            // Close button click
            this.cache.$document.on('click', '.mobile-nav-close', (e) => {
                e.preventDefault();
                this.close();
            });

            // Overlay click
            this.cache.$document.on('click', this.config.selectors.menuOverlay, () => {
                this.close();
            });

            // Submenu handling
            this.cache.$document.on('click', '.mobile-navigation .menu-item-has-children > a', function(e) {
                e.preventDefault();
                self.toggleSubmenu($(this).parent());
            });

            // Regular menu item clicks
            this.cache.$document.on('click', '.mobile-navigation .menu-item:not(.menu-item-has-children) > a', () => {
                this.close();
            });

            // Search toggle
            this.cache.$document.on('click', this.config.selectors.searchToggle, (e) => {
                e.preventDefault();
                this.toggleSearch();
            });

            // Window resize
            this.cache.$window.on('resize', this.utils.debounce(() => {
                this.handleResize();
            }, 250));

            // Keyboard events
            this.cache.$document.on('keydown', (e) => this.handleKeyboard(e));

            // Touch events
            if (this.state.hasTouch) {
                this.bindTouchEvents();
            }

            // Focus trap when menu is open
            this.cache.$document.on('focusin', (e) => this.handleFocusTrap(e));

            // Prevent body scroll when menu is open
            this.cache.$document.on('touchmove', (e) => {
                if (this.state.isOpen) {
                    e.preventDefault();
                }
            });
        },

        /**
         * Bind touch events for gesture support
         */
        bindTouchEvents: function() {
            // Touch events on the mobile navigation
            this.cache.$document.on('touchstart', this.config.selectors.mobileNav, (e) => {
                this.handleTouchStart(e);
            });

            this.cache.$document.on('touchmove', this.config.selectors.mobileNav, (e) => {
                this.handleTouchMove(e);
            });

            this.cache.$document.on('touchend', this.config.selectors.mobileNav, (e) => {
                this.handleTouchEnd(e);
            });

            // Swipe from edge to open menu
            this.cache.$document.on('touchstart', (e) => {
                this.handleEdgeSwipeStart(e);
            });

            this.cache.$document.on('touchend', (e) => {
                this.handleEdgeSwipeEnd(e);
            });
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add ARIA attributes to menu items with children
            this.cache.$document.find('.mobile-navigation .menu-item-has-children').each(function() {
                const $item = $(this);
                const $link = $item.children('a');
                const $submenu = $item.find('.sub-menu');
                const submenuId = 'submenu-' + Math.random().toString(36).substr(2, 9);

                $link.attr({
                    'aria-expanded': 'false',
                    'aria-controls': submenuId
                });

                $submenu.attr({
                    'id': submenuId,
                    'aria-hidden': 'true'
                });

                // Add visual indicator
                $link.append('<span class="submenu-indicator" aria-hidden="true">+</span>');
            });

            // Add role and aria-label to search form
            $('.mobile-search-form').attr({
                'role': 'search',
                'aria-label': 'Site search'
            });
        },

        /**
         * Detect current breakpoint
         */
        detectBreakpoint: function() {
            this.state.isMobile = this.cache.$window.width() < this.config.breakpoint;
            
            if (this.state.isMobile) {
                this.cache.$body.addClass('is-mobile');
                this.cache.$menuToggle.show();
            } else {
                this.cache.$body.removeClass('is-mobile');
                this.cache.$menuToggle.hide();
                if (this.state.isOpen) {
                    this.close();
                }
            }
        },

        /**
         * Toggle mobile menu
         */
        toggle: function() {
            if (this.state.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        /**
         * Open mobile menu
         */
        open: function() {
            if (this.state.isOpen || this.state.isAnimating) return;

            this.state.isAnimating = true;
            this.state.isOpen = true;
            this.state.lastFocusedElement = document.activeElement;

            // Store current scroll position
            this.state.scrollY = window.pageYOffset;

            // Add classes
            this.cache.$body.addClass(this.config.classes.open)
                             .addClass(this.config.classes.noScroll);
            this.cache.$menuToggle.addClass(this.config.classes.active)
                                   .attr(this.config.aria.expanded, 'true');
            this.cache.$mobileNav.addClass(this.config.classes.visible)
                                  .attr(this.config.aria.hidden, 'false');
            this.cache.$menuOverlay.addClass(this.config.classes.visible);

            // Animate menu items with stagger
            this.animateMenuItems('in');

            // Set focus to first menu item
            setTimeout(() => {
                this.cache.$mobileNav.find('.menu-item:first-child a').focus();
                this.state.isAnimating = false;
            }, this.config.animation.duration);

            // Trigger custom event
            this.cache.$document.trigger('recruitpro:mobile-menu:opened');
        },

        /**
         * Close mobile menu
         */
        close: function() {
            if (!this.state.isOpen || this.state.isAnimating) return;

            this.state.isAnimating = true;
            this.state.isOpen = false;

            // Remove classes
            this.cache.$body.removeClass(this.config.classes.open)
                             .removeClass(this.config.classes.noScroll);
            this.cache.$menuToggle.removeClass(this.config.classes.active)
                                   .attr(this.config.aria.expanded, 'false');
            this.cache.$mobileNav.removeClass(this.config.classes.visible)
                                  .attr(this.config.aria.hidden, 'true');
            this.cache.$menuOverlay.removeClass(this.config.classes.visible);

            // Close any open submenus
            this.closeAllSubmenus();

            // Animate menu items out
            this.animateMenuItems('out');

            // Restore focus
            setTimeout(() => {
                if (this.state.lastFocusedElement) {
                    this.state.lastFocusedElement.focus();
                }
                this.state.isAnimating = false;
            }, this.config.animation.duration);

            // Restore scroll position
            window.scrollTo(0, this.state.scrollY);

            // Trigger custom event
            this.cache.$document.trigger('recruitpro:mobile-menu:closed');
        },

        /**
         * Toggle submenu
         */
        toggleSubmenu: function($menuItem) {
            const $link = $menuItem.children('a');
            const $submenu = $menuItem.find('.sub-menu');
            const isOpen = $menuItem.hasClass(this.config.classes.submenuOpen);

            if (isOpen) {
                this.closeSubmenu($menuItem);
            } else {
                // Close other submenus first
                this.closeAllSubmenus();
                this.openSubmenu($menuItem);
            }
        },

        /**
         * Open submenu
         */
        openSubmenu: function($menuItem) {
            const $link = $menuItem.children('a');
            const $submenu = $menuItem.find('.sub-menu');
            const $indicator = $link.find('.submenu-indicator');

            $menuItem.addClass(this.config.classes.submenuOpen);
            $link.attr(this.config.aria.expanded, 'true');
            $submenu.attr(this.config.aria.hidden, 'false');
            $indicator.text('−');

            // Animate submenu
            $submenu.slideDown(this.config.animation.duration);

            this.state.activeSubmenu = $menuItem;
        },

        /**
         * Close submenu
         */
        closeSubmenu: function($menuItem) {
            const $link = $menuItem.children('a');
            const $submenu = $menuItem.find('.sub-menu');
            const $indicator = $link.find('.submenu-indicator');

            $menuItem.removeClass(this.config.classes.submenuOpen);
            $link.attr(this.config.aria.expanded, 'false');
            $submenu.attr(this.config.aria.hidden, 'true');
            $indicator.text('+');

            // Animate submenu
            $submenu.slideUp(this.config.animation.duration);

            if (this.state.activeSubmenu && this.state.activeSubmenu.is($menuItem)) {
                this.state.activeSubmenu = null;
            }
        },

        /**
         * Close all submenus
         */
        closeAllSubmenus: function() {
            this.cache.$mobileNav.find('.menu-item-has-children').each((index, element) => {
                this.closeSubmenu($(element));
            });
        },

        /**
         * Toggle mobile search
         */
        toggleSearch: function() {
            const $searchForm = $('.mobile-search-form');
            const isVisible = $searchForm.attr('aria-hidden') === 'false';

            if (isVisible) {
                $searchForm.attr('aria-hidden', 'true').slideUp();
            } else {
                $searchForm.attr('aria-hidden', 'false').slideDown(() => {
                    $searchForm.find('input').focus();
                });
            }
        },

        /**
         * Animate menu items with stagger effect
         */
        animateMenuItems: function(direction) {
            const $items = this.cache.$mobileNav.find('.menu-item');
            const delay = direction === 'in' ? this.config.animation.stagger : 0;

            $items.each((index, element) => {
                const $item = $(element);
                
                setTimeout(() => {
                    if (direction === 'in') {
                        $item.addClass('animate-in');
                    } else {
                        $item.removeClass('animate-in');
                    }
                }, index * delay);
            });
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboard: function(e) {
            if (!this.state.isOpen) return;

            switch (e.keyCode) {
                case 27: // Escape
                    e.preventDefault();
                    this.close();
                    break;
                    
                case 38: // Up arrow
                    e.preventDefault();
                    this.navigateMenu('up');
                    break;
                    
                case 40: // Down arrow
                    e.preventDefault();
                    this.navigateMenu('down');
                    break;
                    
                case 37: // Left arrow
                    if (this.state.activeSubmenu) {
                        e.preventDefault();
                        this.closeSubmenu(this.state.activeSubmenu);
                    }
                    break;
                    
                case 39: // Right arrow
                    const $focused = $(document.activeElement);
                    const $parent = $focused.closest('.menu-item-has-children');
                    if ($parent.length) {
                        e.preventDefault();
                        this.openSubmenu($parent);
                    }
                    break;
            }
        },

        /**
         * Navigate menu with keyboard
         */
        navigateMenu: function(direction) {
            const $focusable = this.cache.$mobileNav.find('a:visible');
            const $focused = $(document.activeElement);
            const currentIndex = $focusable.index($focused);
            
            let newIndex;
            if (direction === 'up') {
                newIndex = currentIndex > 0 ? currentIndex - 1 : $focusable.length - 1;
            } else {
                newIndex = currentIndex < $focusable.length - 1 ? currentIndex + 1 : 0;
            }
            
            $focusable.eq(newIndex).focus();
        },

        /**
         * Handle focus trap
         */
        handleFocusTrap: function(e) {
            if (!this.state.isOpen) return;

            const $focusable = this.cache.$mobileNav.find('a:visible, button:visible, input:visible');
            const $firstFocusable = $focusable.first();
            const $lastFocusable = $focusable.last();

            if (!this.cache.$mobileNav[0].contains(e.target)) {
                $firstFocusable.focus();
            }
        },

        /**
         * Handle touch start
         */
        handleTouchStart: function(e) {
            this.state.touchStartX = e.originalEvent.touches[0].clientX;
            this.state.touchStartY = e.originalEvent.touches[0].clientY;
            this.state.touchStartTime = Date.now();
        },

        /**
         * Handle touch move
         */
        handleTouchMove: function(e) {
            // Allow vertical scrolling within menu
            const deltaY = Math.abs(e.originalEvent.touches[0].clientY - this.state.touchStartY);
            const deltaX = Math.abs(e.originalEvent.touches[0].clientX - this.state.touchStartX);
            
            if (deltaX > deltaY) {
                e.preventDefault();
            }
        },

        /**
         * Handle touch end
         */
        handleTouchEnd: function(e) {
            const touchEndX = e.originalEvent.changedTouches[0].clientX;
            const deltaX = touchEndX - this.state.touchStartX;
            const deltaTime = Date.now() - this.state.touchStartTime;

            // Check for swipe left to close
            if (deltaX < -this.config.touch.threshold && 
                deltaTime < this.config.touch.maxTime) {
                this.close();
            }
        },

        /**
         * Handle edge swipe start
         */
        handleEdgeSwipeStart: function(e) {
            if (this.state.isOpen) return;

            const touch = e.originalEvent.touches[0];
            if (touch.clientX < 20) { // Left edge
                this.state.touchStartX = touch.clientX;
                this.state.touchStartTime = Date.now();
            }
        },

        /**
         * Handle edge swipe end
         */
        handleEdgeSwipeEnd: function(e) {
            if (this.state.isOpen) return;

            const touch = e.originalEvent.changedTouches[0];
            const deltaX = touch.clientX - this.state.touchStartX;
            const deltaTime = Date.now() - this.state.touchStartTime;

            // Check for swipe right from edge to open
            if (deltaX > this.config.touch.threshold && 
                deltaTime < this.config.touch.maxTime &&
                this.state.touchStartX < 20) {
                this.open();
            }
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            this.detectBreakpoint();
            
            // Close menu if switching to desktop
            if (!this.state.isMobile && this.state.isOpen) {
                this.close();
            }
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
            open: function() {
                MobileMenu.open();
            },

            close: function() {
                MobileMenu.close();
            },

            toggle: function() {
                MobileMenu.toggle();
            },

            isOpen: function() {
                return MobileMenu.state.isOpen;
            },

            refresh: function() {
                MobileMenu.cacheElements();
                MobileMenu.setupAccessibility();
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        MobileMenu.init();
    });

    // Expose public API
    window.RecruitPro.MobileMenu = MobileMenu.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        MobileMenu.cacheElements();
        MobileMenu.setupAccessibility();
    });

})(jQuery);