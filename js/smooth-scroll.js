/**
 * Smooth Scroll Component - RecruitPro Theme
 * 
 * Comprehensive smooth scrolling system for recruitment agency websites
 * Handles anchor navigation, scroll animations, and performance optimization
 * 
 * Features:
 * - Smooth anchor link navigation
 * - Scroll-triggered animations and reveals
 * - Parallax scrolling effects
 * - Navigation highlighting
 * - Back to top functionality
 * - Performance optimization with RAF
 * - Accessibility support (respects prefers-reduced-motion)
 * - Cross-browser compatibility
 * - Mobile optimization
 * - Integration with navigation system
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Smooth scroll namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.SmoothScroll = window.RecruitPro.SmoothScroll || {};

    /**
     * Smooth Scroll Component
     */
    const SmoothScroll = {
        
        // Configuration
        config: {
            selectors: {
                anchorLinks: 'a[href^="#"]:not([href="#"])',
                backToTop: '.back-to-top',
                scrollElements: '.scroll-reveal',
                parallaxElements: '.parallax',
                navigationLinks: '.main-navigation a, .mobile-navigation a',
                skipLinks: '.skip-link',
                scrollTriggers: '[data-scroll-trigger]',
                progressBar: '.scroll-progress',
                sections: 'section, .scroll-section',
                contentSections: '[id]'
            },
            classes: {
                scrolling: 'is-scrolling',
                revealed: 'revealed',
                visible: 'visible',
                active: 'scroll-active',
                animated: 'scroll-animated',
                parallaxEnabled: 'parallax-enabled',
                backToTopVisible: 'back-to-top-visible',
                reduceMotion: 'reduce-motion'
            },
            animation: {
                duration: 800,
                easing: 'easeInOutCubic',
                offset: 80,
                threshold: 0.1,
                stagger: 100
            },
            parallax: {
                enabled: true,
                speed: 0.5,
                maxSpeed: 1.0,
                minSpeed: 0.1
            },
            performance: {
                throttleDelay: 16,
                debounceDelay: 100,
                useRAF: true,
                observerRootMargin: '50px 0px'
            },
            backToTop: {
                showThreshold: 300,
                hideThreshold: 100
            }
        },

        // State management
        state: {
            isScrolling: false,
            isAnimating: false,
            currentSection: null,
            scrollY: 0,
            windowHeight: 0,
            documentHeight: 0,
            observers: {
                reveal: null,
                navigation: null,
                parallax: null
            },
            animations: {
                queue: [],
                running: []
            },
            parallaxElements: [],
            revealsElements: [],
            navigationSections: [],
            backToTopVisible: false,
            lastScrollY: 0,
            scrollDirection: 'down',
            prefersReducedMotion: false,
            raf: null
        },

        // Easing functions
        easingFunctions: {
            linear: function(t) { return t; },
            easeInQuad: function(t) { return t * t; },
            easeOutQuad: function(t) { return t * (2 - t); },
            easeInOutQuad: function(t) { return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t; },
            easeInCubic: function(t) { return t * t * t; },
            easeOutCubic: function(t) { return (--t) * t * t + 1; },
            easeInOutCubic: function(t) { return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1; },
            easeInQuart: function(t) { return t * t * t * t; },
            easeOutQuart: function(t) { return 1 - (--t) * t * t * t; },
            easeInOutQuart: function(t) { return t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t; }
        },

        /**
         * Initialize the smooth scroll system
         */
        init: function() {
            if (this.initialized) return;
            
            this.detectCapabilities();
            this.cacheElements();
            this.setupObservers();
            this.bindEvents();
            this.initializeScrollElements();
            this.initializeParallax();
            this.setupBackToTop();
            this.startScrollLoop();
            
            this.initialized = true;
            console.log('RecruitPro Smooth Scroll initialized');
        },

        /**
         * Detect browser capabilities and user preferences
         */
        detectCapabilities: function() {
            // Check for reduced motion preference
            this.state.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            if (this.state.prefersReducedMotion) {
                document.documentElement.classList.add(this.config.classes.reduceMotion);
                this.config.animation.duration = 0;
                this.config.parallax.enabled = false;
            }

            // Check browser capabilities
            this.capabilities = {
                intersectionObserver: 'IntersectionObserver' in window,
                requestAnimationFrame: 'requestAnimationFrame' in window,
                smoothScrollSupport: 'scrollBehavior' in document.documentElement.style,
                cssTransforms: this.supportsCSSSTransforms(),
                cssTransitions: this.supportsCSSTransitions()
            };

            // Enable native smooth scroll if supported and not disabled
            if (this.capabilities.smoothScrollSupport && !this.state.prefersReducedMotion) {
                document.documentElement.style.scrollBehavior = 'smooth';
            }
        },

        /**
         * Check CSS 3D transforms support
         */
        supportsCSSSTransforms: function() {
            const div = document.createElement('div');
            return typeof div.style.transform !== 'undefined' ||
                   typeof div.style.webkitTransform !== 'undefined' ||
                   typeof div.style.mozTransform !== 'undefined';
        },

        /**
         * Check CSS transitions support
         */
        supportsCSSTransitions: function() {
            const div = document.createElement('div');
            return typeof div.style.transition !== 'undefined' ||
                   typeof div.style.webkitTransition !== 'undefined' ||
                   typeof div.style.mozTransition !== 'undefined';
        },

        /**
         * Cache DOM elements and measurements
         */
        cacheElements: function() {
            this.cache = {
                $window: $(window),
                $document: $(document),
                $body: $('body'),
                $html: $('html'),
                $anchorLinks: $(this.config.selectors.anchorLinks),
                $backToTop: $(this.config.selectors.backToTop),
                $scrollElements: $(this.config.selectors.scrollElements),
                $parallaxElements: $(this.config.selectors.parallaxElements),
                $navigationLinks: $(this.config.selectors.navigationLinks),
                $progressBar: $(this.config.selectors.progressBar),
                $sections: $(this.config.selectors.sections),
                $contentSections: $(this.config.selectors.contentSections)
            };

            this.updateMeasurements();
        },

        /**
         * Update window and document measurements
         */
        updateMeasurements: function() {
            this.state.windowHeight = this.cache.$window.height();
            this.state.documentHeight = this.cache.$document.height();
            this.state.scrollY = this.cache.$window.scrollTop();
        },

        /**
         * Setup Intersection Observers
         */
        setupObservers: function() {
            if (!this.capabilities.intersectionObserver) {
                this.setupFallbackScrollDetection();
                return;
            }

            // Scroll reveal observer
            this.state.observers.reveal = new IntersectionObserver(
                (entries) => this.handleRevealIntersection(entries),
                {
                    rootMargin: this.config.performance.observerRootMargin,
                    threshold: [0, this.config.animation.threshold, 0.5, 1]
                }
            );

            // Navigation highlighting observer
            this.state.observers.navigation = new IntersectionObserver(
                (entries) => this.handleNavigationIntersection(entries),
                {
                    rootMargin: '-20% 0px -70% 0px',
                    threshold: 0
                }
            );

            // Parallax observer (for performance)
            this.state.observers.parallax = new IntersectionObserver(
                (entries) => this.handleParallaxIntersection(entries),
                {
                    rootMargin: '100px 0px',
                    threshold: 0
                }
            );
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Anchor link clicks
            this.cache.$document.on('click', this.config.selectors.anchorLinks, (e) => {
                this.handleAnchorClick(e);
            });

            // Back to top button
            this.cache.$document.on('click', this.config.selectors.backToTop, (e) => {
                this.scrollToTop(e);
            });

            // Window events
            this.cache.$window.on('scroll', this.utils.throttle(() => {
                this.handleScroll();
            }, this.config.performance.throttleDelay));

            this.cache.$window.on('resize', this.utils.debounce(() => {
                this.handleResize();
            }, this.config.performance.debounceDelay));

            // Keyboard navigation
            this.cache.$document.on('keydown', (e) => this.handleKeyboard(e));

            // Skip link handling
            this.cache.$document.on('click', this.config.selectors.skipLinks, (e) => {
                this.handleSkipLink(e);
            });

            // Custom scroll triggers
            this.cache.$document.on('click', this.config.selectors.scrollTriggers, (e) => {
                this.handleScrollTrigger(e);
            });

            // Page visibility changes
            document.addEventListener('visibilitychange', () => {
                this.handleVisibilityChange();
            });

            // Reduced motion changes
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
                mediaQuery.addListener((e) => this.handleReducedMotionChange(e));
            }
        },

        /**
         * Initialize scroll reveal elements
         */
        initializeScrollElements: function() {
            this.cache.$scrollElements.each((index, element) => {
                const $element = $(element);
                
                // Setup element data
                const revealData = this.getRevealData($element);
                $element.data('scroll-reveal', revealData);
                
                // Add to reveals array
                this.state.revealsElements.push({
                    element: element,
                    $element: $element,
                    data: revealData,
                    revealed: false
                });

                // Observe element
                if (this.state.observers.reveal) {
                    this.state.observers.reveal.observe(element);
                }
            });
        },

        /**
         * Get reveal animation data from element
         */
        getRevealData: function($element) {
            return {
                animation: $element.data('scroll-animation') || 'fadeInUp',
                delay: parseInt($element.data('scroll-delay')) || 0,
                duration: parseInt($element.data('scroll-duration')) || this.config.animation.duration,
                easing: $element.data('scroll-easing') || this.config.animation.easing,
                distance: parseInt($element.data('scroll-distance')) || 30,
                scale: parseFloat($element.data('scroll-scale')) || 1,
                threshold: parseFloat($element.data('scroll-threshold')) || this.config.animation.threshold
            };
        },

        /**
         * Initialize parallax elements
         */
        initializeParallax: function() {
            if (!this.config.parallax.enabled || this.state.prefersReducedMotion) {
                return;
            }

            this.cache.$parallaxElements.each((index, element) => {
                const $element = $(element);
                
                // Setup parallax data
                const parallaxData = this.getParallaxData($element);
                $element.data('parallax', parallaxData);
                
                // Add to parallax array
                this.state.parallaxElements.push({
                    element: element,
                    $element: $element,
                    data: parallaxData,
                    isVisible: false,
                    transform: 0
                });

                // Observe element
                if (this.state.observers.parallax) {
                    this.state.observers.parallax.observe(element);
                }
            });
        },

        /**
         * Get parallax data from element
         */
        getParallaxData: function($element) {
            let speed = parseFloat($element.data('parallax-speed'));
            if (isNaN(speed)) speed = this.config.parallax.speed;
            
            // Clamp speed values
            speed = Math.max(this.config.parallax.minSpeed, 
                    Math.min(this.config.parallax.maxSpeed, speed));

            return {
                speed: speed,
                direction: $element.data('parallax-direction') || 'vertical',
                offset: parseInt($element.data('parallax-offset')) || 0,
                boundaries: $element.data('parallax-boundaries') || null
            };
        },

        /**
         * Setup back to top button
         */
        setupBackToTop: function() {
            if (!this.cache.$backToTop.length) {
                this.createBackToTopButton();
            }

            // Initial state
            this.updateBackToTopVisibility();
        },

        /**
         * Create back to top button if it doesn't exist
         */
        createBackToTopButton: function() {
            const buttonHTML = `
                <button class="back-to-top" aria-label="Back to top" title="Back to top">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <line x1="12" y1="19" x2="12" y2="5"></line>
                        <polyline points="5,12 12,5 19,12"></polyline>
                    </svg>
                </button>
            `;
            
            this.cache.$body.append(buttonHTML);
            this.cache.$backToTop = $('.back-to-top');
        },

        /**
         * Start the main scroll loop
         */
        startScrollLoop: function() {
            if (!this.config.performance.useRAF || !this.capabilities.requestAnimationFrame) {
                return;
            }

            const scrollLoop = () => {
                this.updateScrollBasedAnimations();
                this.state.raf = requestAnimationFrame(scrollLoop);
            };

            this.state.raf = requestAnimationFrame(scrollLoop);
        },

        /**
         * Handle anchor link clicks
         */
        handleAnchorClick: function(e) {
            const $link = $(e.currentTarget);
            const href = $link.attr('href');
            
            // Skip if it's just a hash
            if (href === '#') return;

            const targetId = href.substring(1);
            const $target = $('#' + targetId);

            if (!$target.length) return;

            e.preventDefault();

            // Calculate scroll position
            const headerHeight = this.getHeaderHeight();
            const targetOffset = $target.offset().top - headerHeight - this.config.animation.offset;

            // Scroll to target
            this.scrollToPosition(targetOffset, {
                duration: this.config.animation.duration,
                easing: this.config.animation.easing,
                callback: () => {
                    this.updateURL(href);
                    this.setFocusToTarget($target);
                    this.highlightNavigationItem($link);
                }
            });
        },

        /**
         * Scroll to specific position
         */
        scrollToPosition: function(targetY, options = {}) {
            if (this.state.isAnimating) return;

            const settings = {
                duration: options.duration || this.config.animation.duration,
                easing: options.easing || this.config.animation.easing,
                callback: options.callback || null
            };

            // Use native smooth scroll if available and no custom callback
            if (this.capabilities.smoothScrollSupport && !settings.callback && !this.state.prefersReducedMotion) {
                window.scrollTo({
                    top: targetY,
                    behavior: 'smooth'
                });
                return;
            }

            // Custom smooth scroll animation
            this.animateScroll(targetY, settings);
        },

        /**
         * Animate scroll with custom easing
         */
        animateScroll: function(targetY, settings) {
            if (this.state.prefersReducedMotion) {
                window.scrollTo(0, targetY);
                if (settings.callback) settings.callback();
                return;
            }

            this.state.isAnimating = true;
            this.cache.$body.addClass(this.config.classes.scrolling);

            const startY = this.cache.$window.scrollTop();
            const distance = targetY - startY;
            const startTime = performance.now();

            const easingFunction = this.easingFunctions[settings.easing] || this.easingFunctions.easeInOutCubic;

            const animateStep = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / settings.duration, 1);
                const easeProgress = easingFunction(progress);
                const currentY = startY + (distance * easeProgress);

                window.scrollTo(0, currentY);

                if (progress < 1) {
                    requestAnimationFrame(animateStep);
                } else {
                    this.state.isAnimating = false;
                    this.cache.$body.removeClass(this.config.classes.scrolling);
                    if (settings.callback) settings.callback();
                }
            };

            requestAnimationFrame(animateStep);
        },

        /**
         * Scroll to top
         */
        scrollToTop: function(e) {
            if (e) e.preventDefault();
            
            this.scrollToPosition(0, {
                duration: this.config.animation.duration,
                easing: this.config.animation.easing
            });
        },

        /**
         * Handle main scroll events
         */
        handleScroll: function() {
            this.updateScrollState();
            this.updateBackToTopVisibility();
            this.updateScrollProgress();
            
            if (!this.config.performance.useRAF) {
                this.updateScrollBasedAnimations();
            }
        },

        /**
         * Update scroll state
         */
        updateScrollState: function() {
            this.state.lastScrollY = this.state.scrollY;
            this.state.scrollY = this.cache.$window.scrollTop();
            this.state.scrollDirection = this.state.scrollY > this.state.lastScrollY ? 'down' : 'up';
        },

        /**
         * Update scroll-based animations
         */
        updateScrollBasedAnimations: function() {
            if (this.state.prefersReducedMotion) return;

            this.updateParallaxElements();
            this.updateRevealElements();
        },

        /**
         * Update parallax elements
         */
        updateParallaxElements: function() {
            this.state.parallaxElements.forEach(item => {
                if (!item.isVisible) return;

                const elementRect = item.element.getBoundingClientRect();
                const elementCenter = elementRect.top + elementRect.height / 2;
                const viewportCenter = this.state.windowHeight / 2;
                const distance = elementCenter - viewportCenter;
                
                let transform = distance * item.data.speed;

                // Apply boundaries if specified
                if (item.data.boundaries) {
                    const maxTransform = parseInt(item.data.boundaries);
                    transform = Math.max(-maxTransform, Math.min(maxTransform, transform));
                }

                // Apply transform
                if (item.data.direction === 'horizontal') {
                    item.$element.css('transform', `translateX(${transform}px)`);
                } else {
                    item.$element.css('transform', `translateY(${transform}px)`);
                }

                item.transform = transform;
            });
        },

        /**
         * Update reveal elements without intersection observer
         */
        updateRevealElements: function() {
            if (this.capabilities.intersectionObserver) return;

            this.state.revealsElements.forEach(item => {
                if (item.revealed) return;

                const elementRect = item.element.getBoundingClientRect();
                const elementTop = elementRect.top;
                const triggerPoint = this.state.windowHeight * (1 - item.data.threshold);

                if (elementTop < triggerPoint) {
                    this.revealElement(item);
                }
            });
        },

        /**
         * Update back to top button visibility
         */
        updateBackToTopVisibility: function() {
            const shouldShow = this.state.scrollY > this.config.backToTop.showThreshold;
            
            if (shouldShow !== this.state.backToTopVisible) {
                this.state.backToTopVisible = shouldShow;
                
                if (shouldShow) {
                    this.cache.$backToTop.addClass(this.config.classes.backToTopVisible);
                } else {
                    this.cache.$backToTop.removeClass(this.config.classes.backToTopVisible);
                }
            }
        },

        /**
         * Update scroll progress bar
         */
        updateScrollProgress: function() {
            if (!this.cache.$progressBar.length) return;

            const maxScroll = this.state.documentHeight - this.state.windowHeight;
            const scrollPercent = (this.state.scrollY / maxScroll) * 100;
            
            this.cache.$progressBar.css('width', Math.min(100, Math.max(0, scrollPercent)) + '%');
        },

        /**
         * Handle reveal intersection
         */
        handleRevealIntersection: function(entries) {
            entries.forEach(entry => {
                const item = this.state.revealsElements.find(item => item.element === entry.target);
                if (!item || item.revealed) return;

                if (entry.isIntersecting && entry.intersectionRatio >= item.data.threshold) {
                    this.revealElement(item);
                }
            });
        },

        /**
         * Handle navigation intersection
         */
        handleNavigationIntersection: function(entries) {
            entries.forEach(entry => {
                const sectionId = entry.target.id;
                if (!sectionId) return;

                if (entry.isIntersecting) {
                    this.highlightNavigationSection(sectionId);
                }
            });
        },

        /**
         * Handle parallax intersection
         */
        handleParallaxIntersection: function(entries) {
            entries.forEach(entry => {
                const item = this.state.parallaxElements.find(item => item.element === entry.target);
                if (!item) return;

                item.isVisible = entry.isIntersecting;
                
                if (!entry.isIntersecting) {
                    // Reset transform when not visible
                    item.$element.css('transform', '');
                }
            });
        },

        /**
         * Reveal element with animation
         */
        revealElement: function(item) {
            item.revealed = true;
            
            setTimeout(() => {
                item.$element.addClass(this.config.classes.revealed);
                
                // Trigger custom event
                this.cache.$document.trigger('recruitpro:element-revealed', [item.element, item.data]);
            }, item.data.delay);
        },

        /**
         * Highlight navigation section
         */
        highlightNavigationSection: function(sectionId) {
            if (this.state.currentSection === sectionId) return;
            
            this.state.currentSection = sectionId;
            
            // Remove active class from all navigation links
            this.cache.$navigationLinks.removeClass(this.config.classes.active);
            
            // Add active class to current section link
            this.cache.$navigationLinks.filter(`[href="#${sectionId}"]`).addClass(this.config.classes.active);
            
            // Trigger custom event
            this.cache.$document.trigger('recruitpro:section-active', [sectionId]);
        },

        /**
         * Highlight navigation item
         */
        highlightNavigationItem: function($link) {
            this.cache.$navigationLinks.removeClass(this.config.classes.active);
            $link.addClass(this.config.classes.active);
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboard: function(e) {
            // Page Up/Down
            if (e.key === 'PageUp' || e.key === 'PageDown') {
                if (!this.state.prefersReducedMotion) {
                    e.preventDefault();
                    const direction = e.key === 'PageUp' ? -1 : 1;
                    const distance = this.state.windowHeight * 0.8 * direction;
                    const targetY = Math.max(0, this.state.scrollY + distance);
                    
                    this.scrollToPosition(targetY);
                }
            }
            
            // Home/End
            if (e.key === 'Home' && e.ctrlKey) {
                e.preventDefault();
                this.scrollToTop();
            }
            
            if (e.key === 'End' && e.ctrlKey) {
                e.preventDefault();
                this.scrollToPosition(this.state.documentHeight);
            }
        },

        /**
         * Handle skip link clicks
         */
        handleSkipLink: function(e) {
            e.preventDefault();
            
            const $link = $(e.currentTarget);
            const href = $link.attr('href');
            const $target = $(href);
            
            if ($target.length) {
                this.scrollToPosition($target.offset().top, {
                    duration: this.config.animation.duration / 2,
                    callback: () => {
                        this.setFocusToTarget($target);
                    }
                });
            }
        },

        /**
         * Handle custom scroll triggers
         */
        handleScrollTrigger: function(e) {
            e.preventDefault();
            
            const $trigger = $(e.currentTarget);
            const action = $trigger.data('scroll-trigger');
            const target = $trigger.data('scroll-target');
            const offset = parseInt($trigger.data('scroll-offset')) || 0;
            
            switch (action) {
                case 'scroll-to':
                    if (target) {
                        const $target = $(target);
                        if ($target.length) {
                            const targetY = $target.offset().top + offset;
                            this.scrollToPosition(targetY);
                        }
                    }
                    break;
                    
                case 'scroll-to-top':
                    this.scrollToTop();
                    break;
                    
                case 'scroll-to-bottom':
                    this.scrollToPosition(this.state.documentHeight);
                    break;
            }
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            this.updateMeasurements();
            
            // Recalculate reveal elements positions
            this.state.revealsElements.forEach(item => {
                if (!item.revealed && !this.capabilities.intersectionObserver) {
                    // Force recheck for fallback method
                    const elementRect = item.element.getBoundingClientRect();
                    const elementTop = elementRect.top;
                    const triggerPoint = this.state.windowHeight * (1 - item.data.threshold);

                    if (elementTop < triggerPoint) {
                        this.revealElement(item);
                    }
                }
            });
        },

        /**
         * Handle page visibility changes
         */
        handleVisibilityChange: function() {
            if (document.hidden) {
                // Pause animations when page is hidden
                if (this.state.raf) {
                    cancelAnimationFrame(this.state.raf);
                    this.state.raf = null;
                }
            } else {
                // Resume animations when page becomes visible
                if (this.config.performance.useRAF && !this.state.raf) {
                    this.startScrollLoop();
                }
            }
        },

        /**
         * Handle reduced motion preference changes
         */
        handleReducedMotionChange: function(e) {
            this.state.prefersReducedMotion = e.matches;
            
            if (this.state.prefersReducedMotion) {
                document.documentElement.classList.add(this.config.classes.reduceMotion);
                this.config.animation.duration = 0;
                this.config.parallax.enabled = false;
                
                // Disable native smooth scroll
                document.documentElement.style.scrollBehavior = 'auto';
                
                // Stop parallax animations
                this.state.parallaxElements.forEach(item => {
                    item.$element.css('transform', '');
                });
            } else {
                document.documentElement.classList.remove(this.config.classes.reduceMotion);
                this.config.animation.duration = 800;
                this.config.parallax.enabled = true;
                
                // Re-enable native smooth scroll
                if (this.capabilities.smoothScrollSupport) {
                    document.documentElement.style.scrollBehavior = 'smooth';
                }
            }
        },

        /**
         * Setup fallback scroll detection for browsers without Intersection Observer
         */
        setupFallbackScrollDetection: function() {
            console.warn('Intersection Observer not supported, using scroll fallback');
            
            // Initialize navigation sections for fallback
            this.cache.$sections.each((index, section) => {
                const sectionId = section.id;
                if (sectionId) {
                    this.state.navigationSections.push({
                        id: sectionId,
                        element: section,
                        top: $(section).offset().top
                    });
                }
            });
        },

        /**
         * Get current header height for offset calculations
         */
        getHeaderHeight: function() {
            const $header = $('.header-main, .site-header');
            return $header.length ? $header.outerHeight() : 0;
        },

        /**
         * Update URL without page jump
         */
        updateURL: function(hash) {
            if (window.history && window.history.pushState) {
                const url = window.location.pathname + window.location.search + hash;
                window.history.pushState({}, '', url);
            }
        },

        /**
         * Set focus to target element for accessibility
         */
        setFocusToTarget: function($target) {
            // Make element focusable if it isn't already
            if (!$target.attr('tabindex')) {
                $target.attr('tabindex', '-1');
            }
            
            // Set focus
            $target.focus();
            
            // Remove tabindex after focus (clean up)
            setTimeout(() => {
                if ($target.attr('tabindex') === '-1') {
                    $target.removeAttr('tabindex');
                }
            }, 1000);
        },

        /**
         * Utility functions
         */
        utils: {
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
            },

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
            }
        },

        /**
         * Public API methods
         */
        api: {
            scrollTo: function(target, options) {
                const $target = $(target);
                if ($target.length) {
                    const targetY = $target.offset().top - SmoothScroll.getHeaderHeight();
                    SmoothScroll.scrollToPosition(targetY, options);
                }
            },

            scrollToTop: function(options) {
                SmoothScroll.scrollToPosition(0, options);
            },

            scrollToPosition: function(position, options) {
                SmoothScroll.scrollToPosition(position, options);
            },

            enableParallax: function() {
                SmoothScroll.config.parallax.enabled = true;
            },

            disableParallax: function() {
                SmoothScroll.config.parallax.enabled = false;
                SmoothScroll.state.parallaxElements.forEach(item => {
                    item.$element.css('transform', '');
                });
            },

            revealElement: function(element) {
                const item = SmoothScroll.state.revealsElements.find(item => 
                    item.element === element || item.$element.is(element)
                );
                if (item && !item.revealed) {
                    SmoothScroll.revealElement(item);
                }
            },

            refresh: function() {
                SmoothScroll.updateMeasurements();
                SmoothScroll.cacheElements();
                SmoothScroll.initializeScrollElements();
                SmoothScroll.initializeParallax();
            },

            getScrollState: function() {
                return {
                    scrollY: SmoothScroll.state.scrollY,
                    scrollDirection: SmoothScroll.state.scrollDirection,
                    currentSection: SmoothScroll.state.currentSection,
                    isScrolling: SmoothScroll.state.isScrolling
                };
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        SmoothScroll.init();
    });

    // Expose public API
    window.RecruitPro.SmoothScroll = SmoothScroll.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        SmoothScroll.cacheElements();
        SmoothScroll.initializeScrollElements();
        SmoothScroll.initializeParallax();
    });

    // Clean up on page unload
    $(window).on('beforeunload', function() {
        if (SmoothScroll.state.raf) {
            cancelAnimationFrame(SmoothScroll.state.raf);
        }
    });

})(jQuery);