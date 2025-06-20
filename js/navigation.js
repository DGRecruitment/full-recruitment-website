/**
 * Navigation Component - RecruitPro Theme
 * 
 * Comprehensive navigation system for recruitment agency websites
 * Handles desktop navigation, sticky header, dropdowns, breadcrumbs, and search
 * 
 * Features:
 * - Sticky header with scroll behavior
 * - Multi-level dropdown menus
 * - Smart search functionality
 * - Breadcrumb navigation
 * - Smooth scrolling navigation
 * - Header animations and transitions
 * - Integration with mobile menu system
 * - Accessibility support (ARIA, keyboard navigation)
 * - Performance optimizations
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Navigation namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.Navigation = window.RecruitPro.Navigation || {};

    /**
     * Navigation Component
     */
    const Navigation = {
        
        // Configuration
        config: {
            selectors: {
                header: '.site-header',
                stickyHeader: '.header-sticky-wrapper',
                navigation: '.main-navigation',
                navMenu: '.nav-menu',
                menuItem: '.menu-item',
                menuLink: '.menu-item > a',
                subMenu: '.sub-menu',
                dropdownMenu: '.dropdown-menu',
                menuToggle: '.menu-toggle',
                searchToggle: '.search-toggle',
                searchForm: '.header-search',
                searchInput: '.header-search input',
                searchDropdown: '.search-dropdown',
                breadcrumbs: '.breadcrumb-navigation',
                breadcrumbLink: '.breadcrumb-link',
                headerActions: '.header-actions',
                ctaButton: '.header-cta',
                socialLinks: '.header-social',
                contactInfo: '.header-contact',
                languageSwitcher: '.header-language'
            },
            classes: {
                sticky: 'is-sticky',
                scrolled: 'is-scrolled',
                scrollUp: 'scroll-up',
                scrollDown: 'scroll-down',
                hasDropdown: 'has-dropdown',
                dropdownOpen: 'dropdown-open',
                menuOpen: 'menu-open',
                searchActive: 'search-active',
                loading: 'loading',
                fixed: 'header-fixed',
                transparent: 'header-transparent',
                minimal: 'header-minimal'
            },
            breakpoints: {
                mobile: 768,
                tablet: 1024,
                desktop: 1200
            },
            scroll: {
                threshold: 100,
                hideThreshold: 300,
                throttle: 16
            },
            animation: {
                duration: 300,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
            },
            search: {
                minLength: 2,
                debounceDelay: 300
            }
        },

        // State management
        state: {
            isSticky: false,
            isScrolled: false,
            scrollDirection: 'up',
            lastScrollTop: 0,
            currentScrollTop: 0,
            isSearchOpen: false,
            activeDropdown: null,
            isMobile: false,
            isTablet: false,
            headerHeight: 0,
            searchResults: [],
            isSearching: false
        },

        /**
         * Initialize the navigation system
         */
        init: function() {
            if (this.initialized) return;
            
            this.detectEnvironment();
            this.cacheElements();
            this.setupStickyHeader();
            this.bindEvents();
            this.setupDropdowns();
            this.setupSearch();
            this.setupBreadcrumbs();
            this.setupAccessibility();
            this.initializeScrollBehavior();
            
            this.initialized = true;
            console.log('RecruitPro Navigation initialized');
        },

        /**
         * Detect environment and screen size
         */
        detectEnvironment: function() {
            const windowWidth = window.innerWidth;
            this.state.isMobile = windowWidth < this.config.breakpoints.mobile;
            this.state.isTablet = windowWidth >= this.config.breakpoints.mobile && windowWidth < this.config.breakpoints.desktop;
            
            // Update body classes
            $('body').toggleClass('is-mobile', this.state.isMobile)
                     .toggleClass('is-tablet', this.state.isTablet)
                     .toggleClass('is-desktop', !this.state.isMobile && !this.state.isTablet);
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
                $header: $(this.config.selectors.header),
                $stickyHeader: $(this.config.selectors.stickyHeader),
                $navigation: $(this.config.selectors.navigation),
                $navMenu: $(this.config.selectors.navMenu),
                $menuItems: $(this.config.selectors.menuItem),
                $subMenus: $(this.config.selectors.subMenu),
                $searchToggle: $(this.config.selectors.searchToggle),
                $searchForm: $(this.config.selectors.searchForm),
                $searchInput: $(this.config.selectors.searchInput),
                $searchDropdown: $(this.config.selectors.searchDropdown),
                $breadcrumbs: $(this.config.selectors.breadcrumbs),
                $headerActions: $(this.config.selectors.headerActions)
            };

            // Store initial header height
            this.state.headerHeight = this.cache.$header.outerHeight() || 80;
        },

        /**
         * Setup sticky header functionality
         */
        setupStickyHeader: function() {
            if (!this.cache.$stickyHeader.length) {
                this.createStickyHeader();
            }

            // Set initial sticky state
            this.updateStickyState();
        },

        /**
         * Create sticky header if it doesn't exist
         */
        createStickyHeader: function() {
            const $headerClone = this.cache.$header.clone();
            $headerClone.addClass('header-sticky-wrapper')
                       .attr('id', 'sticky-header')
                       .css({
                           position: 'fixed',
                           top: 0,
                           left: 0,
                           right: 0,
                           zIndex: 1000,
                           transform: 'translateY(-100%)',
                           transition: `transform ${this.config.animation.duration}ms ${this.config.animation.easing}`
                       });

            this.cache.$body.prepend($headerClone);
            this.cache.$stickyHeader = $headerClone;
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Window events
            this.cache.$window.on('scroll', this.utils.throttle(() => {
                this.handleScroll();
            }, this.config.scroll.throttle));

            this.cache.$window.on('resize', this.utils.debounce(() => {
                this.handleResize();
            }, 250));

            // Navigation events
            this.cache.$document.on('mouseenter', this.config.selectors.menuItem, function() {
                if (!self.state.isMobile) {
                    self.openDropdown($(this));
                }
            });

            this.cache.$document.on('mouseleave', this.config.selectors.menuItem, function() {
                if (!self.state.isMobile) {
                    self.closeDropdown($(this));
                }
            });

            // Dropdown link clicks
            this.cache.$document.on('click', '.has-dropdown > a', function(e) {
                if (self.state.isMobile) {
                    e.preventDefault();
                    self.toggleDropdown($(this).parent());
                }
            });

            // Search events
            this.cache.$document.on('click', this.config.selectors.searchToggle, (e) => {
                e.preventDefault();
                this.toggleSearch();
            });

            this.cache.$document.on('input', this.config.selectors.searchInput, this.utils.debounce((e) => {
                this.handleSearchInput(e);
            }, this.config.search.debounceDelay));

            this.cache.$document.on('submit', this.config.selectors.searchForm, (e) => {
                this.handleSearchSubmit(e);
            });

            // Click outside to close dropdowns and search
            this.cache.$document.on('click', (e) => {
                if (!$(e.target).closest(this.config.selectors.navigation).length) {
                    this.closeAllDropdowns();
                }
                if (!$(e.target).closest(this.config.selectors.searchForm).length) {
                    this.closeSearch();
                }
            });

            // Keyboard navigation
            this.cache.$document.on('keydown', (e) => this.handleKeyboard(e));

            // Breadcrumb navigation
            this.cache.$document.on('click', this.config.selectors.breadcrumbLink, (e) => {
                this.handleBreadcrumbClick(e);
            });

            // Smooth scroll for anchor links
            this.cache.$document.on('click', 'a[href^="#"]', (e) => {
                this.handleAnchorClick(e);
            });

            // Header visibility on focus (accessibility)
            this.cache.$document.on('focusin', this.config.selectors.navigation + ' a', () => {
                this.showStickyHeader();
            });
        },

        /**
         * Setup dropdown menus
         */
        setupDropdowns: function() {
            // Add dropdown indicators
            this.cache.$menuItems.each(function() {
                const $item = $(this);
                const $subMenu = $item.find(this.config.selectors.subMenu);
                
                if ($subMenu.length) {
                    $item.addClass('has-dropdown');
                    
                    // Add dropdown arrow
                    const $link = $item.children('a');
                    if (!$link.find('.dropdown-arrow').length) {
                        $link.append(`
                            <span class="dropdown-arrow" aria-hidden="true">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="6,9 12,15 18,9"></polyline>
                                </svg>
                            </span>
                        `);
                    }
                }
            }.bind(this));

            // Setup mega menus if present
            this.setupMegaMenus();
        },

        /**
         * Setup mega menus
         */
        setupMegaMenus: function() {
            $('.mega-menu').each(function() {
                const $megaMenu = $(this);
                const $menuItem = $megaMenu.closest('.menu-item');
                
                $menuItem.addClass('has-mega-menu');
                
                // Position mega menu
                $megaMenu.css({
                    position: 'absolute',
                    top: '100%',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    width: '800px',
                    maxWidth: '90vw'
                });
            });
        },

        /**
         * Setup search functionality
         */
        setupSearch: function() {
            // Create search dropdown if it doesn't exist
            if (!this.cache.$searchDropdown.length) {
                this.createSearchDropdown();
            }

            // Setup search autocomplete
            this.setupSearchAutocomplete();
        },

        /**
         * Create search dropdown
         */
        createSearchDropdown: function() {
            const searchDropdownHTML = `
                <div class="search-dropdown">
                    <div class="search-results">
                        <div class="search-loading" style="display: none;">
                            <div class="loading-spinner"></div>
                            <span>Searching...</span>
                        </div>
                        <div class="search-results-list"></div>
                        <div class="search-no-results" style="display: none;">
                            <p>No results found. Try a different search term.</p>
                        </div>
                    </div>
                    <div class="search-footer">
                        <a href="#" class="search-view-all">View all results</a>
                    </div>
                </div>
            `;

            this.cache.$searchForm.append(searchDropdownHTML);
            this.cache.$searchDropdown = this.cache.$searchForm.find('.search-dropdown');
        },

        /**
         * Setup search autocomplete
         */
        setupSearchAutocomplete: function() {
            // This would integrate with the search system
            // For now, we'll set up the UI structure
        },

        /**
         * Setup breadcrumb navigation
         */
        setupBreadcrumbs: function() {
            this.cache.$breadcrumbs.find('a').on('click', (e) => {
                this.handleBreadcrumbClick(e);
            });

            // Add structured data for breadcrumbs
            this.addBreadcrumbStructuredData();
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add ARIA attributes
            this.cache.$menuItems.filter('.has-dropdown').each(function() {
                const $item = $(this);
                const $link = $item.children('a');
                const $subMenu = $item.find('.sub-menu');
                const submenuId = 'submenu-' + Math.random().toString(36).substr(2, 9);

                $link.attr({
                    'aria-expanded': 'false',
                    'aria-haspopup': 'menu',
                    'aria-controls': submenuId
                });

                $subMenu.attr({
                    'id': submenuId,
                    'role': 'menu',
                    'aria-hidden': 'true'
                });

                $subMenu.find('a').attr('role', 'menuitem');
            });

            // Add skip links
            this.addSkipLinks();
        },

        /**
         * Add skip links for accessibility
         */
        addSkipLinks: function() {
            if (!$('.skip-links').length) {
                const skipLinksHTML = `
                    <div class="skip-links">
                        <a href="#main-content" class="skip-link">Skip to main content</a>
                        <a href="#main-navigation" class="skip-link">Skip to navigation</a>
                        <a href="#footer" class="skip-link">Skip to footer</a>
                    </div>
                `;
                this.cache.$body.prepend(skipLinksHTML);
            }
        },

        /**
         * Initialize scroll behavior
         */
        initializeScrollBehavior: function() {
            this.state.lastScrollTop = this.cache.$window.scrollTop();
            this.handleScroll();
        },

        /**
         * Handle window scroll
         */
        handleScroll: function() {
            this.state.currentScrollTop = this.cache.$window.scrollTop();
            
            // Determine scroll direction
            if (this.state.currentScrollTop > this.state.lastScrollTop) {
                this.state.scrollDirection = 'down';
            } else if (this.state.currentScrollTop < this.state.lastScrollTop) {
                this.state.scrollDirection = 'up';
            }

            // Update scroll states
            this.updateScrollStates();
            this.updateStickyHeader();
            this.updateHeaderBackground();

            this.state.lastScrollTop = this.state.currentScrollTop;
        },

        /**
         * Update scroll-related states
         */
        updateScrollStates: function() {
            const isScrolled = this.state.currentScrollTop > this.config.scroll.threshold;
            
            if (isScrolled !== this.state.isScrolled) {
                this.state.isScrolled = isScrolled;
                this.cache.$header.toggleClass(this.config.classes.scrolled, isScrolled);
                this.cache.$body.toggleClass('header-scrolled', isScrolled);
            }
        },

        /**
         * Update sticky header
         */
        updateStickyHeader: function() {
            if (!this.cache.$stickyHeader.length) return;

            const shouldShowSticky = this.state.currentScrollTop > this.state.headerHeight;
            const shouldHideOnScroll = this.state.currentScrollTop > this.config.scroll.hideThreshold && 
                                     this.state.scrollDirection === 'down';

            if (shouldShowSticky && !shouldHideOnScroll) {
                this.showStickyHeader();
            } else {
                this.hideStickyHeader();
            }
        },

        /**
         * Show sticky header
         */
        showStickyHeader: function() {
            if (!this.state.isSticky) {
                this.state.isSticky = true;
                this.cache.$stickyHeader.addClass(this.config.classes.sticky)
                                        .css('transform', 'translateY(0)');
            }
        },

        /**
         * Hide sticky header
         */
        hideStickyHeader: function() {
            if (this.state.isSticky) {
                this.state.isSticky = false;
                this.cache.$stickyHeader.removeClass(this.config.classes.sticky)
                                        .css('transform', 'translateY(-100%)');
            }
        },

        /**
         * Update header background opacity
         */
        updateHeaderBackground: function() {
            const opacity = Math.min(this.state.currentScrollTop / this.config.scroll.threshold, 1);
            
            if (this.cache.$header.hasClass('header-transparent')) {
                this.cache.$header.css('background-color', `rgba(255, 255, 255, ${opacity * 0.95})`);
            }
        },

        /**
         * Update sticky state
         */
        updateStickyState: function() {
            // Implementation for sticky state updates
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            this.detectEnvironment();
            this.state.headerHeight = this.cache.$header.outerHeight() || 80;
            this.closeAllDropdowns();
            this.closeSearch();
        },

        /**
         * Open dropdown menu
         */
        openDropdown: function($menuItem) {
            if (!$menuItem.hasClass('has-dropdown')) return;

            // Close other dropdowns first
            this.closeAllDropdowns();

            const $link = $menuItem.children('a');
            const $subMenu = $menuItem.find('.sub-menu');

            $menuItem.addClass(this.config.classes.dropdownOpen);
            $link.attr('aria-expanded', 'true');
            $subMenu.attr('aria-hidden', 'false');

            this.state.activeDropdown = $menuItem;

            // Position dropdown if needed
            this.positionDropdown($menuItem, $subMenu);
        },

        /**
         * Close dropdown menu
         */
        closeDropdown: function($menuItem) {
            if (!$menuItem.hasClass('has-dropdown')) return;

            const $link = $menuItem.children('a');
            const $subMenu = $menuItem.find('.sub-menu');

            $menuItem.removeClass(this.config.classes.dropdownOpen);
            $link.attr('aria-expanded', 'false');
            $subMenu.attr('aria-hidden', 'true');

            if (this.state.activeDropdown && this.state.activeDropdown.is($menuItem)) {
                this.state.activeDropdown = null;
            }
        },

        /**
         * Toggle dropdown menu
         */
        toggleDropdown: function($menuItem) {
            if ($menuItem.hasClass(this.config.classes.dropdownOpen)) {
                this.closeDropdown($menuItem);
            } else {
                this.openDropdown($menuItem);
            }
        },

        /**
         * Close all dropdown menus
         */
        closeAllDropdowns: function() {
            this.cache.$menuItems.filter('.has-dropdown').each((index, element) => {
                this.closeDropdown($(element));
            });
        },

        /**
         * Position dropdown menu
         */
        positionDropdown: function($menuItem, $subMenu) {
            const windowWidth = this.cache.$window.width();
            const menuOffset = $menuItem.offset();
            const menuWidth = $subMenu.outerWidth();
            const rightEdge = menuOffset.left + menuWidth;

            // Adjust position if dropdown would go off-screen
            if (rightEdge > windowWidth) {
                $subMenu.addClass('dropdown-right');
            } else {
                $subMenu.removeClass('dropdown-right');
            }
        },

        /**
         * Toggle search dropdown
         */
        toggleSearch: function() {
            if (this.state.isSearchOpen) {
                this.closeSearch();
            } else {
                this.openSearch();
            }
        },

        /**
         * Open search dropdown
         */
        openSearch: function() {
            this.state.isSearchOpen = true;
            this.cache.$searchForm.addClass(this.config.classes.searchActive);
            this.cache.$searchDropdown.addClass('active');
            this.cache.$searchToggle.attr('aria-expanded', 'true');
            
            // Focus search input
            setTimeout(() => {
                this.cache.$searchInput.focus();
            }, 100);
        },

        /**
         * Close search dropdown
         */
        closeSearch: function() {
            this.state.isSearchOpen = false;
            this.cache.$searchForm.removeClass(this.config.classes.searchActive);
            this.cache.$searchDropdown.removeClass('active');
            this.cache.$searchToggle.attr('aria-expanded', 'false');
        },

        /**
         * Handle search input
         */
        handleSearchInput: function(e) {
            const query = $(e.target).val().trim();
            
            if (query.length >= this.config.search.minLength) {
                this.performSearch(query);
            } else {
                this.clearSearchResults();
            }
        },

        /**
         * Perform search
         */
        performSearch: function(query) {
            if (this.state.isSearching) return;

            this.state.isSearching = true;
            this.showSearchLoading();

            // Trigger custom event for search plugins to handle
            this.cache.$document.trigger('recruitpro:search', {
                query: query,
                callback: (results) => {
                    this.displaySearchResults(results);
                    this.state.isSearching = false;
                    this.hideSearchLoading();
                }
            });

            // Fallback if no search handler
            setTimeout(() => {
                if (this.state.isSearching) {
                    this.state.isSearching = false;
                    this.hideSearchLoading();
                    this.showNoResults();
                }
            }, 3000);
        },

        /**
         * Handle search form submission
         */
        handleSearchSubmit: function(e) {
            e.preventDefault();
            const query = this.cache.$searchInput.val().trim();
            
            if (query) {
                // Redirect to search results page
                window.location.href = `${window.location.origin}/?s=${encodeURIComponent(query)}`;
            }
        },

        /**
         * Display search results
         */
        displaySearchResults: function(results) {
            const $resultsList = this.cache.$searchDropdown.find('.search-results-list');
            
            if (results && results.length > 0) {
                const resultsHTML = results.map(result => `
                    <div class="search-result-item">
                        <h4><a href="${result.url}">${result.title}</a></h4>
                        <p>${result.excerpt}</p>
                        <span class="result-type">${result.type}</span>
                    </div>
                `).join('');
                
                $resultsList.html(resultsHTML);
                this.cache.$searchDropdown.find('.search-no-results').hide();
            } else {
                this.showNoResults();
            }
        },

        /**
         * Show search loading state
         */
        showSearchLoading: function() {
            this.cache.$searchDropdown.find('.search-loading').show();
            this.cache.$searchDropdown.find('.search-results-list').hide();
            this.cache.$searchDropdown.find('.search-no-results').hide();
        },

        /**
         * Hide search loading state
         */
        hideSearchLoading: function() {
            this.cache.$searchDropdown.find('.search-loading').hide();
            this.cache.$searchDropdown.find('.search-results-list').show();
        },

        /**
         * Show no results message
         */
        showNoResults: function() {
            this.cache.$searchDropdown.find('.search-results-list').hide();
            this.cache.$searchDropdown.find('.search-no-results').show();
        },

        /**
         * Clear search results
         */
        clearSearchResults: function() {
            this.cache.$searchDropdown.find('.search-results-list').empty();
            this.cache.$searchDropdown.find('.search-no-results').hide();
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboard: function(e) {
            switch (e.keyCode) {
                case 27: // Escape
                    this.closeAllDropdowns();
                    this.closeSearch();
                    break;
                    
                case 9: // Tab
                    // Handle tab navigation in dropdowns
                    if (this.state.activeDropdown) {
                        this.handleDropdownTabNavigation(e);
                    }
                    break;
                    
                case 13: // Enter
                    if ($(e.target).hasClass('has-dropdown')) {
                        e.preventDefault();
                        this.toggleDropdown($(e.target).parent());
                    }
                    break;
                    
                case 38: // Up arrow
                case 40: // Down arrow
                    if (this.state.isSearchOpen) {
                        this.handleSearchNavigation(e);
                    }
                    break;
            }
        },

        /**
         * Handle dropdown tab navigation
         */
        handleDropdownTabNavigation: function(e) {
            const $dropdown = this.state.activeDropdown.find('.sub-menu');
            const $focusable = $dropdown.find('a');
            const $focused = $(document.activeElement);
            const currentIndex = $focusable.index($focused);

            if (e.shiftKey) {
                // Shift+Tab - previous item
                if (currentIndex === 0) {
                    e.preventDefault();
                    this.closeDropdown(this.state.activeDropdown);
                    this.state.activeDropdown.children('a').focus();
                }
            } else {
                // Tab - next item
                if (currentIndex === $focusable.length - 1) {
                    this.closeDropdown(this.state.activeDropdown);
                }
            }
        },

        /**
         * Handle search navigation
         */
        handleSearchNavigation: function(e) {
            e.preventDefault();
            const $results = this.cache.$searchDropdown.find('.search-result-item a');
            const $focused = $(document.activeElement);
            const currentIndex = $results.index($focused);

            if (e.keyCode === 40) { // Down arrow
                const nextIndex = currentIndex < $results.length - 1 ? currentIndex + 1 : 0;
                $results.eq(nextIndex).focus();
            } else if (e.keyCode === 38) { // Up arrow
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : $results.length - 1;
                $results.eq(prevIndex).focus();
            }
        },

        /**
         * Handle breadcrumb clicks
         */
        handleBreadcrumbClick: function(e) {
            // Allow normal navigation, but trigger custom event
            this.cache.$document.trigger('recruitpro:breadcrumb-click', {
                url: $(e.currentTarget).attr('href'),
                text: $(e.currentTarget).text()
            });
        },

        /**
         * Add breadcrumb structured data
         */
        addBreadcrumbStructuredData: function() {
            const breadcrumbs = [];
            this.cache.$breadcrumbs.find('.breadcrumb-link').each(function(index) {
                const $link = $(this);
                breadcrumbs.push({
                    '@type': 'ListItem',
                    'position': index + 1,
                    'name': $link.text(),
                    'item': $link.attr('href')
                });
            });

            if (breadcrumbs.length > 0) {
                const structuredData = {
                    '@context': 'https://schema.org',
                    '@type': 'BreadcrumbList',
                    'itemListElement': breadcrumbs
                };

                $('head').append(`<script type="application/ld+json">${JSON.stringify(structuredData)}</script>`);
            }
        },

        /**
         * Handle anchor link clicks (smooth scrolling)
         */
        handleAnchorClick: function(e) {
            const $link = $(e.currentTarget);
            const href = $link.attr('href');
            
            if (href.startsWith('#') && href.length > 1) {
                const $target = $(href);
                
                if ($target.length) {
                    e.preventDefault();
                    
                    const offset = this.state.headerHeight + 20;
                    const targetPosition = $target.offset().top - offset;
                    
                    $('html, body').animate({
                        scrollTop: targetPosition
                    }, this.config.animation.duration, 'swing', () => {
                        // Update URL without page jump
                        if (window.history && window.history.pushState) {
                            window.history.pushState(null, null, href);
                        }
                        
                        // Set focus for accessibility
                        $target.attr('tabindex', '-1').focus();
                    });
                }
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
            openDropdown: function(menuItem) {
                Navigation.openDropdown($(menuItem));
            },

            closeDropdown: function(menuItem) {
                Navigation.closeDropdown($(menuItem));
            },

            closeAllDropdowns: function() {
                Navigation.closeAllDropdowns();
            },

            openSearch: function() {
                Navigation.openSearch();
            },

            closeSearch: function() {
                Navigation.closeSearch();
            },

            showStickyHeader: function() {
                Navigation.showStickyHeader();
            },

            hideStickyHeader: function() {
                Navigation.hideStickyHeader();
            },

            refresh: function() {
                Navigation.cacheElements();
                Navigation.setupDropdowns();
                Navigation.setupAccessibility();
            },

            getState: function() {
                return Navigation.state;
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        Navigation.init();
    });

    // Expose public API
    window.RecruitPro.Navigation = Navigation.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        Navigation.cacheElements();
        Navigation.setupDropdowns();
        Navigation.setupAccessibility();
    });

})(jQuery);