/**
 * Main JavaScript File - RecruitPro Theme
 * 
 * Core frontend functionality for recruitment agency websites
 * Optimized for performance, accessibility, and mobile-first design
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Global RecruitPro object
    window.RecruitPro = window.RecruitPro || {};

    /**
     * Main Application Object
     */
    const RecruitProApp = {
        
        // Configuration
        config: {
            breakpoints: {
                xs: 320,
                sm: 576,
                md: 768,
                lg: 992,
                xl: 1200,
                xxl: 1400
            },
            scrollOffset: 70,
            animationDuration: 300,
            debounceDelay: 250,
            throttleDelay: 100
        },

        // Cache DOM elements
        cache: {
            $window: $(window),
            $document: $(document),
            $body: $('body'),
            $html: $('html'),
            $header: null,
            $mobileToggle: null,
            $navigation: null,
            $skipLink: null
        },

        // Application state
        state: {
            isMobile: false,
            isTablet: false,
            isDesktop: false,
            scrollDirection: 'down',
            lastScrollTop: 0,
            isMenuOpen: false,
            isRTL: false,
            currentBreakpoint: 'xs'
        },

        /**
         * Initialize the application
         */
        init: function() {
            this.cacheElements();
            this.detectEnvironment();
            this.bindEvents();
            this.initComponents();
            this.performance.init();
            this.accessibility.init();
            
            // Mark as initialized
            this.cache.$body.addClass('js-loaded');
            
            console.log('RecruitPro Theme initialized successfully');
        },

        /**
         * Cache frequently used DOM elements
         */
        cacheElements: function() {
            this.cache.$header = $('.header-main');
            this.cache.$mobileToggle = $('.mobile-menu-toggle');
            this.cache.$navigation = $('.main-navigation');
            this.cache.$skipLink = $('.skip-link');
        },

        /**
         * Detect environment and device capabilities
         */
        detectEnvironment: function() {
            // Detect device type
            this.state.isMobile = this.cache.$window.width() < this.config.breakpoints.md;
            this.state.isTablet = this.cache.$window.width() >= this.config.breakpoints.md && this.cache.$window.width() < this.config.breakpoints.lg;
            this.state.isDesktop = this.cache.$window.width() >= this.config.breakpoints.lg;
            
            // Detect RTL
            this.state.isRTL = this.cache.$html.attr('dir') === 'rtl' || this.cache.$body.hasClass('rtl');
            
            // Set current breakpoint
            this.updateBreakpoint();
            
            // Touch device detection
            if ('ontouchstart' in window) {
                this.cache.$body.addClass('touch-device');
            }
            
            // Reduced motion detection
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.cache.$body.addClass('reduce-motion');
            }
        },

        /**
         * Update current breakpoint
         */
        updateBreakpoint: function() {
            const width = this.cache.$window.width();
            
            if (width >= this.config.breakpoints.xxl) {
                this.state.currentBreakpoint = 'xxl';
            } else if (width >= this.config.breakpoints.xl) {
                this.state.currentBreakpoint = 'xl';
            } else if (width >= this.config.breakpoints.lg) {
                this.state.currentBreakpoint = 'lg';
            } else if (width >= this.config.breakpoints.md) {
                this.state.currentBreakpoint = 'md';
            } else if (width >= this.config.breakpoints.sm) {
                this.state.currentBreakpoint = 'sm';
            } else {
                this.state.currentBreakpoint = 'xs';
            }
        },

        /**
         * Bind global events
         */
        bindEvents: function() {
            // Window events
            this.cache.$window.on('resize', this.utils.debounce(this.handleResize.bind(this), this.config.debounceDelay));
            this.cache.$window.on('scroll', this.utils.throttle(this.handleScroll.bind(this), this.config.throttleDelay));
            this.cache.$window.on('orientationchange', this.handleOrientationChange.bind(this));
            
            // Document events
            this.cache.$document.on('keydown', this.handleKeydown.bind(this));
            this.cache.$document.on('click', 'a[href^="#"]', this.handleAnchorClick.bind(this));
            
            // Mobile menu events
            this.cache.$mobileToggle.on('click', this.navigation.toggleMobile.bind(this.navigation));
            
            // Form events
            this.cache.$document.on('submit', '.contact-form, .application-form', this.forms.handleSubmit.bind(this.forms));
            this.cache.$document.on('change', '.file-upload-input', this.forms.handleFileUpload.bind(this.forms));
            
            // Job listing events
            this.cache.$document.on('click', '.job-apply-btn', this.jobs.handleApplyClick.bind(this.jobs));
            this.cache.$document.on('click', '.job-favorite-btn', this.jobs.handleFavoriteClick.bind(this.jobs));
            
            // Modal events
            this.cache.$document.on('click', '[data-modal]', this.modals.open.bind(this.modals));
            this.cache.$document.on('click', '.modal-close, .modal-backdrop', this.modals.close.bind(this.modals));
            
            // Search events
            this.cache.$document.on('submit', '.search-form', this.search.handleSubmit.bind(this.search));
            this.cache.$document.on('input', '.search-input', this.utils.debounce(this.search.handleInput.bind(this.search), 300));
        },

        /**
         * Initialize components
         */
        initComponents: function() {
            this.navigation.init();
            this.forms.init();
            this.jobs.init();
            this.modals.init();
            this.search.init();
            this.animations.init();
            this.lazyLoading.init();
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            this.detectEnvironment();
            this.navigation.handleResize();
            this.animations.handleResize();
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:resize', [this.state.currentBreakpoint]);
        },

        /**
         * Handle window scroll
         */
        handleScroll: function() {
            const scrollTop = this.cache.$window.scrollTop();
            
            // Determine scroll direction
            if (scrollTop > this.state.lastScrollTop) {
                this.state.scrollDirection = 'down';
            } else {
                this.state.scrollDirection = 'up';
            }
            
            this.state.lastScrollTop = scrollTop;
            
            // Handle header behavior
            this.navigation.handleScroll(scrollTop);
            
            // Handle animations
            this.animations.handleScroll(scrollTop);
            
            // Trigger custom event
            this.cache.$window.trigger('recruitpro:scroll', [scrollTop, this.state.scrollDirection]);
        },

        /**
         * Handle orientation change
         */
        handleOrientationChange: function() {
            // Close mobile menu on orientation change
            if (this.state.isMenuOpen) {
                this.navigation.closeMobile();
            }
            
            // Recalculate after orientation change
            setTimeout(() => {
                this.detectEnvironment();
                this.cache.$window.trigger('recruitpro:orientationchange');
            }, 100);
        },

        /**
         * Handle global keydown events
         */
        handleKeydown: function(e) {
            // Escape key handling
            if (e.keyCode === 27) {
                if (this.state.isMenuOpen) {
                    this.navigation.closeMobile();
                }
                this.modals.closeAll();
            }
            
            // Skip link handling
            if (e.keyCode === 9 && e.shiftKey === false) {
                this.accessibility.handleTabForward(e);
            }
        },

        /**
         * Handle anchor link clicks (smooth scrolling)
         */
        handleAnchorClick: function(e) {
            const $link = $(e.currentTarget);
            const href = $link.attr('href');
            const target = href.split('#')[1];
            
            if (!target) return;
            
            const $target = $('#' + target);
            
            if ($target.length) {
                e.preventDefault();
                
                const offset = this.cache.$header.outerHeight() || this.config.scrollOffset;
                const targetPosition = $target.offset().top - offset;
                
                $('html, body').animate({
                    scrollTop: targetPosition
                }, this.config.animationDuration);
                
                // Update URL without page jump
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, null, href);
                }
                
                // Focus target for accessibility
                $target.focus();
            }
        }
    };

    /**
     * Navigation Component
     */
    RecruitProApp.navigation = {
        
        init: function() {
            this.setupMobileMenu();
            this.setupDropdowns();
            this.setupStickyHeader();
        },

        setupMobileMenu: function() {
            // Add mobile menu indicators
            $('.menu-item-has-children > a').on('click', function(e) {
                if (RecruitProApp.state.isMobile) {
                    e.preventDefault();
                    const $item = $(this).parent();
                    $item.toggleClass('open');
                    $item.find('.sub-menu').first().slideToggle(200);
                }
            });
        },

        setupDropdowns: function() {
            // Desktop dropdown handling
            $('.menu-item-has-children').hover(
                function() {
                    if (!RecruitProApp.state.isMobile) {
                        $(this).addClass('hover');
                    }
                },
                function() {
                    if (!RecruitProApp.state.isMobile) {
                        $(this).removeClass('hover');
                    }
                }
            );
        },

        setupStickyHeader: function() {
            if (RecruitProApp.cache.$header.hasClass('sticky-header')) {
                // Add scroll class for styling
                RecruitProApp.cache.$window.on('scroll', () => {
                    if (RecruitProApp.cache.$window.scrollTop() > 100) {
                        RecruitProApp.cache.$header.addClass('scrolled');
                    } else {
                        RecruitProApp.cache.$header.removeClass('scrolled');
                    }
                });
            }
        },

        toggleMobile: function(e) {
            e.preventDefault();
            
            if (RecruitProApp.state.isMenuOpen) {
                this.closeMobile();
            } else {
                this.openMobile();
            }
        },

        openMobile: function() {
            RecruitProApp.cache.$navigation.addClass('active');
            RecruitProApp.cache.$mobileToggle.addClass('active').attr('aria-expanded', 'true');
            RecruitProApp.cache.$body.addClass('menu-open');
            RecruitProApp.state.isMenuOpen = true;
            
            // Focus first menu item
            RecruitProApp.cache.$navigation.find('a').first().focus();
            
            // Prevent body scroll
            RecruitProApp.cache.$body.css('overflow', 'hidden');
        },

        closeMobile: function() {
            RecruitProApp.cache.$navigation.removeClass('active');
            RecruitProApp.cache.$mobileToggle.removeClass('active').attr('aria-expanded', 'false');
            RecruitProApp.cache.$body.removeClass('menu-open');
            RecruitProApp.state.isMenuOpen = false;
            
            // Restore body scroll
            RecruitProApp.cache.$body.css('overflow', '');
            
            // Close all sub-menus
            $('.menu-item-has-children').removeClass('open');
            $('.sub-menu').hide();
        },

        handleResize: function() {
            if (!RecruitProApp.state.isMobile && RecruitProApp.state.isMenuOpen) {
                this.closeMobile();
            }
        },

        handleScroll: function(scrollTop) {
            // Auto-hide header on scroll down (mobile)
            if (RecruitProApp.state.isMobile && RecruitProApp.cache.$header.hasClass('auto-hide')) {
                if (RecruitProApp.state.scrollDirection === 'down' && scrollTop > 100) {
                    RecruitProApp.cache.$header.addClass('hidden');
                } else if (RecruitProApp.state.scrollDirection === 'up') {
                    RecruitProApp.cache.$header.removeClass('hidden');
                }
            }
        }
    };

    /**
     * Forms Component
     */
    RecruitProApp.forms = {
        
        init: function() {
            this.setupValidation();
            this.setupFileUploads();
            this.setupFormEnhancements();
        },

        setupValidation: function() {
            // Add custom validation classes
            $('input[required], select[required], textarea[required]').on('blur', function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
            
            // Email validation
            $('input[type="email"]').on('blur', function() {
                const $field = $(this);
                const email = $field.val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    $field.addClass('error');
                } else {
                    $field.removeClass('error');
                }
            });
        },

        setupFileUploads: function() {
            // Enhanced file upload styling
            $('.file-upload-input').on('change', function() {
                const files = this.files;
                const $label = $(this).siblings('.file-upload-label');
                
                if (files.length > 0) {
                    const fileName = files[0].name;
                    $label.find('.file-name').text(fileName);
                    $label.addClass('has-file');
                } else {
                    $label.find('.file-name').text('');
                    $label.removeClass('has-file');
                }
            });
        },

        setupFormEnhancements: function() {
            // Auto-resize textareas
            $('textarea').on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Form field focus effects
            $('.form-control').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });
        },

        handleSubmit: function(e) {
            const $form = $(e.currentTarget);
            const formData = new FormData($form[0]);
            
            // Basic validation
            let hasErrors = false;
            $form.find('[required]').each(function() {
                const $field = $(this);
                if ($field.val().trim() === '') {
                    $field.addClass('error');
                    hasErrors = true;
                } else {
                    $field.removeClass('error');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                $form.find('.error').first().focus();
                return false;
            }
            
            // Show loading state
            const $submitBtn = $form.find('[type="submit"]');
            $submitBtn.prop('disabled', true).addClass('loading');
            
            // Let form submit naturally (will be handled by PHP/plugins)
            // This is just the theme layer - actual processing happens server-side
        },

        handleFileUpload: function(e) {
            const file = e.target.files[0];
            const $input = $(e.target);
            const $label = $input.siblings('.file-upload-label');
            
            if (file) {
                // File size validation (5MB limit)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert('File size must be less than 5MB');
                    $input.val('');
                    return;
                }
                
                // File type validation (PDF, DOC, DOCX)
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload only PDF, DOC, or DOCX files');
                    $input.val('');
                    return;
                }
                
                // Update label
                $label.find('.file-name').text(file.name);
                $label.addClass('has-file');
            }
        }
    };

    /**
     * Jobs Component
     */
    RecruitProApp.jobs = {
        
        init: function() {
            this.setupJobListings();
            this.setupFilters();
            this.setupSearch();
        },

        setupJobListings: function() {
            // Job listing hover effects
            $('.job-listing-item').hover(
                function() {
                    $(this).addClass('hovered');
                },
                function() {
                    $(this).removeClass('hovered');
                }
            );
        },

        setupFilters: function() {
            // Job filter handling
            $('.job-filter').on('change', function() {
                const filterType = $(this).data('filter');
                const filterValue = $(this).val();
                
                // Trigger custom event for plugin handling
                RecruitProApp.cache.$document.trigger('recruitpro:job-filter', [filterType, filterValue]);
            });
        },

        setupSearch: function() {
            // Job search functionality
            $('.job-search-input').on('input', RecruitProApp.utils.debounce(function() {
                const searchTerm = $(this).val();
                
                // Trigger custom event for plugin handling
                RecruitProApp.cache.$document.trigger('recruitpro:job-search', [searchTerm]);
            }, 300));
        },

        handleApplyClick: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const jobId = $btn.data('job-id');
            
            // Open application modal or redirect
            if ($('#application-modal').length) {
                RecruitProApp.modals.open(null, 'application-modal');
                $('#application-modal').find('[name="job_id"]').val(jobId);
            } else {
                // Redirect to application page
                window.location.href = $btn.attr('href');
            }
        },

        handleFavoriteClick: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const jobId = $btn.data('job-id');
            
            // Toggle favorite state (visual feedback)
            $btn.toggleClass('favorited');
            
            // Trigger custom event for plugin handling
            RecruitProApp.cache.$document.trigger('recruitpro:job-favorite', [jobId, $btn.hasClass('favorited')]);
        }
    };

    /**
     * Modals Component
     */
    RecruitProApp.modals = {
        
        init: function() {
            this.setupModalStructure();
            this.bindModalEvents();
        },

        setupModalStructure: function() {
            // Ensure modal backdrop exists
            if (!$('.modal-backdrop').length) {
                $('body').append('<div class="modal-backdrop"></div>');
            }
        },

        bindModalEvents: function() {
            // Close modal with escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27 && $('.modal.active').length) {
                    RecruitProApp.modals.closeAll();
                }
            });
        },

        open: function(e, modalId) {
            if (e) {
                e.preventDefault();
                modalId = $(e.currentTarget).data('modal');
            }
            
            const $modal = $('#' + modalId);
            
            if ($modal.length) {
                $modal.addClass('active');
                $('.modal-backdrop').addClass('active');
                RecruitProApp.cache.$body.addClass('modal-open');
                
                // Focus first focusable element
                const $focusable = $modal.find('input, button, select, textarea, a[href]').first();
                if ($focusable.length) {
                    setTimeout(() => $focusable.focus(), 100);
                }
                
                // Prevent body scroll
                RecruitProApp.cache.$body.css('overflow', 'hidden');
            }
        },

        close: function(e, modalId) {
            if (e) {
                e.preventDefault();
                const $target = $(e.target);
                
                if ($target.hasClass('modal-close') || $target.hasClass('modal-backdrop')) {
                    modalId = $target.closest('.modal').attr('id') || $('.modal.active').attr('id');
                }
            }
            
            if (modalId) {
                const $modal = $('#' + modalId);
                $modal.removeClass('active');
            } else {
                $('.modal.active').removeClass('active');
            }
            
            $('.modal-backdrop').removeClass('active');
            RecruitProApp.cache.$body.removeClass('modal-open');
            
            // Restore body scroll
            RecruitProApp.cache.$body.css('overflow', '');
        },

        closeAll: function() {
            $('.modal.active').removeClass('active');
            $('.modal-backdrop').removeClass('active');
            RecruitProApp.cache.$body.removeClass('modal-open').css('overflow', '');
        }
    };

    /**
     * Search Component
     */
    RecruitProApp.search = {
        
        init: function() {
            this.setupSearchForms();
            this.setupAutoComplete();
        },

        setupSearchForms: function() {
            // Search form enhancements
            $('.search-form').each(function() {
                const $form = $(this);
                const $input = $form.find('.search-input');
                const $button = $form.find('.search-button');
                
                // Show/hide search button based on input
                $input.on('input', function() {
                    if ($(this).val().trim() !== '') {
                        $button.addClass('active');
                    } else {
                        $button.removeClass('active');
                    }
                });
            });
        },

        setupAutoComplete: function() {
            // Basic autocomplete functionality
            $('.search-input[data-autocomplete]').on('input', RecruitProApp.utils.debounce(function() {
                const $input = $(this);
                const query = $input.val();
                
                if (query.length >= 3) {
                    // Trigger custom event for plugin handling
                    RecruitProApp.cache.$document.trigger('recruitpro:search-autocomplete', [query, $input]);
                }
            }, 300));
        },

        handleSubmit: function(e) {
            const $form = $(e.currentTarget);
            const $input = $form.find('.search-input');
            const query = $input.val().trim();
            
            if (query === '') {
                e.preventDefault();
                $input.focus();
                return false;
            }
            
            // Add loading state
            $form.addClass('loading');
        },

        handleInput: function(e) {
            const $input = $(e.currentTarget);
            const query = $input.val().trim();
            
            // Trigger live search event
            RecruitProApp.cache.$document.trigger('recruitpro:live-search', [query, $input]);
        }
    };

    /**
     * Animations Component
     */
    RecruitProApp.animations = {
        
        init: function() {
            this.setupScrollAnimations();
            this.setupHoverEffects();
        },

        setupScrollAnimations: function() {
            // Intersection Observer for scroll animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate-in');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                $('.animate-on-scroll').each(function() {
                    observer.observe(this);
                });
            }
        },

        setupHoverEffects: function() {
            // Enhanced hover effects for cards
            $('.job-listing-item, .feature-item, .team-member').hover(
                function() {
                    $(this).addClass('hover-active');
                },
                function() {
                    $(this).removeClass('hover-active');
                }
            );
        },

        handleResize: function() {
            // Recalculate animation triggers on resize
            $('.animate-on-scroll').removeClass('animate-in');
            this.setupScrollAnimations();
        },

        handleScroll: function(scrollTop) {
            // Parallax effects (if enabled)
            if ($('.parallax-element').length && !RecruitProApp.cache.$body.hasClass('reduce-motion')) {
                $('.parallax-element').each(function() {
                    const $element = $(this);
                    const speed = $element.data('parallax-speed') || 0.5;
                    const yPos = -(scrollTop * speed);
                    $element.css('transform', `translateY(${yPos}px)`);
                });
            }
        }
    };

    /**
     * Lazy Loading Component
     */
    RecruitProApp.lazyLoading = {
        
        init: function() {
            this.setupImageLazyLoading();
            this.setupContentLazyLoading();
        },

        setupImageLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                $('img[data-src]').each(function() {
                    imageObserver.observe(this);
                });
            }
        },

        setupContentLazyLoading: function() {
            // Lazy load heavy content sections
            if ('IntersectionObserver' in window) {
                const contentObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const element = entry.target;
                            $(element).trigger('recruitpro:lazy-load');
                            contentObserver.unobserve(element);
                        }
                    });
                }, {
                    rootMargin: '100px'
                });
                
                $('.lazy-content').each(function() {
                    contentObserver.observe(this);
                });
            }
        }
    };

    /**
     * Performance Utilities
     */
    RecruitProApp.performance = {
        
        init: function() {
            this.preloadCriticalResources();
            this.setupResourceHints();
            this.monitorPerformance();
        },

        preloadCriticalResources: function() {
            // Preload critical images
            const criticalImages = [
                '/wp-content/themes/recruitpro/assets/images/logo.svg',
                '/wp-content/themes/recruitpro/assets/images/hero-bg.jpg'
            ];
            
            criticalImages.forEach(src => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = src;
                document.head.appendChild(link);
            });
        },

        setupResourceHints: function() {
            // DNS prefetch for external resources
            const domains = [
                'fonts.googleapis.com',
                'fonts.gstatic.com',
                'api.recruitpro.com'
            ];
            
            domains.forEach(domain => {
                const link = document.createElement('link');
                link.rel = 'dns-prefetch';
                link.href = `//${domain}`;
                document.head.appendChild(link);
            });
        },

        monitorPerformance: function() {
            if ('PerformanceObserver' in window) {
                // Monitor Largest Contentful Paint (LCP)
                const observer = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    
                    if (lastEntry.startTime > 2500) {
                        console.warn('LCP is higher than recommended (2.5s):', lastEntry.startTime);
                    }
                });
                
                observer.observe({ entryTypes: ['largest-contentful-paint'] });
            }
        }
    };

    /**
     * Accessibility Component
     */
    RecruitProApp.accessibility = {
        
        init: function() {
            this.setupKeyboardNavigation();
            this.setupFocusManagement();
            this.setupScreenReaderSupport();
        },

        setupKeyboardNavigation: function() {
            // Enhanced keyboard navigation
            $(document).on('keydown', function(e) {
                // Tab key handling
                if (e.keyCode === 9) {
                    RecruitProApp.cache.$body.addClass('keyboard-navigation');
                }
            });
            
            // Remove keyboard navigation class on mouse click
            $(document).on('mousedown', function() {
                RecruitProApp.cache.$body.removeClass('keyboard-navigation');
            });
        },

        setupFocusManagement: function() {
            // Focus visible polyfill
            $('.btn, .form-control, a[href]').on('focus', function() {
                $(this).addClass('focus-visible');
            }).on('blur', function() {
                $(this).removeClass('focus-visible');
            });
        },

        setupScreenReaderSupport: function() {
            // ARIA live regions for dynamic content
            if (!$('#aria-live-region').length) {
                $('body').append('<div id="aria-live-region" aria-live="polite" aria-atomic="true" class="sr-only"></div>');
            }
        },

        announceToScreenReader: function(message) {
            $('#aria-live-region').text(message);
            setTimeout(() => {
                $('#aria-live-region').text('');
            }, 1000);
        },

        handleTabForward: function(e) {
            // Skip link functionality
            if (RecruitProApp.cache.$skipLink.is(':focus')) {
                const target = RecruitProApp.cache.$skipLink.attr('href');
                if (target) {
                    $(target).focus();
                }
            }
        }
    };

    /**
     * Utility Functions
     */
    RecruitProApp.utils = {
        
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

        isMobile: function() {
            return RecruitProApp.state.isMobile;
        },

        isTablet: function() {
            return RecruitProApp.state.isTablet;
        },

        isDesktop: function() {
            return RecruitProApp.state.isDesktop;
        },

        getCurrentBreakpoint: function() {
            return RecruitProApp.state.currentBreakpoint;
        },

        isRTL: function() {
            return RecruitProApp.state.isRTL;
        },

        escapeHtml: function(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };

    /**
     * Public API
     */
    window.RecruitPro = {
        app: RecruitProApp,
        utils: RecruitProApp.utils,
        navigation: RecruitProApp.navigation,
        forms: RecruitProApp.forms,
        jobs: RecruitProApp.jobs,
        modals: RecruitProApp.modals,
        search: RecruitProApp.search,
        animations: RecruitProApp.animations,
        accessibility: RecruitProApp.accessibility,
        performance: RecruitProApp.performance
    };

    /**
     * Initialize when DOM is ready
     */
    $(document).ready(function() {
        RecruitProApp.init();
    });

    /**
     * Expose to global scope for plugin integration
     */
    window.RecruitProApp = RecruitProApp;

})(jQuery);

/**
 * Custom Events Documentation
 * 
 * The theme triggers the following custom events that plugins can listen to:
 * 
 * - recruitpro:resize - Window resize with breakpoint info
 * - recruitpro:scroll - Window scroll with position and direction
 * - recruitpro:orientationchange - Device orientation change
 * - recruitpro:job-filter - Job filter change
 * - recruitpro:job-search - Job search input
 * - recruitpro:job-favorite - Job favorite toggle
 * - recruitpro:search-autocomplete - Search autocomplete request
 * - recruitpro:live-search - Live search input
 * - recruitpro:lazy-load - Content lazy loading trigger
 */