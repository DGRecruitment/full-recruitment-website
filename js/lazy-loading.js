/**
 * Lazy Loading Component - RecruitPro Theme
 * 
 * Advanced lazy loading system for images, videos, iframes, and content sections
 * Optimized for performance, accessibility, and user experience
 * 
 * Features:
 * - IntersectionObserver API with fallbacks
 * - Progressive image loading with blur-up technique
 * - Video and iframe lazy loading
 * - Content section lazy loading
 * - Error handling and retry mechanisms
 * - Performance monitoring
 * - Accessibility support
 * - Memory optimization
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Lazy loading namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.LazyLoading = window.RecruitPro.LazyLoading || {};

    /**
     * Lazy Loading Component
     */
    const LazyLoading = {
        
        // Configuration
        config: {
            selectors: {
                image: 'img[data-src], img[loading="lazy"]',
                backgroundImage: '[data-bg-src]',
                video: 'video[data-src]',
                iframe: 'iframe[data-src]',
                content: '[data-lazy-content]',
                placeholder: '.lazy-placeholder',
                progressive: '.progressive-image'
            },
            classes: {
                lazy: 'lazy-load',
                loading: 'lazy-loading',
                loaded: 'lazy-loaded',
                error: 'lazy-error',
                placeholder: 'lazy-placeholder',
                progressive: 'progressive-image',
                fadeIn: 'fade-in',
                visible: 'visible',
                blur: 'blur-up'
            },
            attributes: {
                src: 'data-src',
                srcset: 'data-srcset',
                sizes: 'data-sizes',
                bgSrc: 'data-bg-src',
                lazyContent: 'data-lazy-content',
                retryCount: 'data-retry-count',
                placeholder: 'data-placeholder'
            },
            observer: {
                rootMargin: '50px 0px',
                threshold: [0, 0.1, 0.5, 1.0]
            },
            animation: {
                duration: 300,
                easing: 'ease-out'
            },
            retry: {
                maxAttempts: 3,
                delay: 1000
            },
            progressive: {
                quality: 10,
                blur: 5
            }
        },

        // State management
        state: {
            observers: {
                image: null,
                content: null,
                background: null,
                media: null
            },
            loadedCount: 0,
            totalCount: 0,
            isSupported: false,
            performance: {
                startTime: 0,
                loadTimes: [],
                errors: []
            },
            queue: {
                images: [],
                content: [],
                media: []
            }
        },

        /**
         * Initialize the lazy loading system
         */
        init: function() {
            if (this.initialized) return;
            
            this.detectSupport();
            this.cacheElements();
            this.bindEvents();
            this.setupObservers();
            this.processExistingElements();
            this.startPerformanceMonitoring();
            
            this.initialized = true;
            console.log('RecruitPro Lazy Loading initialized');
        },

        /**
         * Detect browser support
         */
        detectSupport: function() {
            this.state.isSupported = {
                intersectionObserver: 'IntersectionObserver' in window,
                requestIdleCallback: 'requestIdleCallback' in window,
                webp: this.detectWebPSupport(),
                avif: this.detectAVIFSupport()
            };
        },

        /**
         * Cache DOM elements and count totals
         */
        cacheElements: function() {
            this.cache = {
                $window: $(window),
                $document: $(document),
                $body: $('body'),
                $images: $(this.config.selectors.image),
                $backgroundImages: $(this.config.selectors.backgroundImage),
                $videos: $(this.config.selectors.video),
                $iframes: $(this.config.selectors.iframe),
                $content: $(this.config.selectors.content)
            };

            // Count total elements to lazy load
            this.state.totalCount = 
                this.cache.$images.length + 
                this.cache.$backgroundImages.length + 
                this.cache.$videos.length + 
                this.cache.$iframes.length + 
                this.cache.$content.length;
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Window events
            this.cache.$window.on('load', () => this.onWindowLoad());
            this.cache.$window.on('resize', this.utils.debounce(() => this.handleResize(), 250));

            // Document events
            this.cache.$document.on('recruitpro:content-loaded', () => this.refresh());
            this.cache.$document.on('recruitpro:image-error', (e, element) => this.handleImageError(element));

            // Page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.resumeLoading();
                }
            });

            // Network information changes
            if ('connection' in navigator) {
                navigator.connection.addEventListener('change', () => {
                    this.adjustLoadingStrategy();
                });
            }
        },

        /**
         * Setup intersection observers
         */
        setupObservers: function() {
            if (!this.state.isSupported.intersectionObserver) {
                this.setupFallback();
                return;
            }

            // Image observer
            this.state.observers.image = new IntersectionObserver(
                (entries) => this.handleImageIntersection(entries),
                this.config.observer
            );

            // Content observer with different settings
            this.state.observers.content = new IntersectionObserver(
                (entries) => this.handleContentIntersection(entries),
                {
                    ...this.config.observer,
                    rootMargin: '100px 0px'
                }
            );

            // Background image observer
            this.state.observers.background = new IntersectionObserver(
                (entries) => this.handleBackgroundIntersection(entries),
                this.config.observer
            );

            // Media observer (videos, iframes)
            this.state.observers.media = new IntersectionObserver(
                (entries) => this.handleMediaIntersection(entries),
                {
                    ...this.config.observer,
                    rootMargin: '200px 0px'
                }
            );
        },

        /**
         * Process existing elements on page
         */
        processExistingElements: function() {
            this.processImages();
            this.processBackgroundImages();
            this.processVideos();
            this.processIframes();
            this.processContent();
        },

        /**
         * Process images for lazy loading
         */
        processImages: function() {
            this.cache.$images.each((index, element) => {
                const $img = $(element);
                const src = $img.attr(this.config.attributes.src) || $img.attr('src');
                
                if (src && !$img.hasClass(this.config.classes.loaded)) {
                    this.prepareImage($img);
                    
                    if (this.state.observers.image) {
                        this.state.observers.image.observe(element);
                    }
                }
            });
        },

        /**
         * Process background images
         */
        processBackgroundImages: function() {
            this.cache.$backgroundImages.each((index, element) => {
                const $element = $(element);
                const bgSrc = $element.attr(this.config.attributes.bgSrc);
                
                if (bgSrc && !$element.hasClass(this.config.classes.loaded)) {
                    this.prepareBackgroundImage($element);
                    
                    if (this.state.observers.background) {
                        this.state.observers.background.observe(element);
                    }
                }
            });
        },

        /**
         * Process videos for lazy loading
         */
        processVideos: function() {
            this.cache.$videos.each((index, element) => {
                const $video = $(element);
                const src = $video.attr(this.config.attributes.src);
                
                if (src && !$video.hasClass(this.config.classes.loaded)) {
                    this.prepareVideo($video);
                    
                    if (this.state.observers.media) {
                        this.state.observers.media.observe(element);
                    }
                }
            });
        },

        /**
         * Process iframes for lazy loading
         */
        processIframes: function() {
            this.cache.$iframes.each((index, element) => {
                const $iframe = $(element);
                const src = $iframe.attr(this.config.attributes.src);
                
                if (src && !$iframe.hasClass(this.config.classes.loaded)) {
                    this.prepareIframe($iframe);
                    
                    if (this.state.observers.media) {
                        this.state.observers.media.observe(element);
                    }
                }
            });
        },

        /**
         * Process content sections
         */
        processContent: function() {
            this.cache.$content.each((index, element) => {
                const $content = $(element);
                
                if (!$content.hasClass(this.config.classes.loaded)) {
                    this.prepareContent($content);
                    
                    if (this.state.observers.content) {
                        this.state.observers.content.observe(element);
                    }
                }
            });
        },

        /**
         * Handle image intersection
         */
        handleImageIntersection: function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const $img = $(entry.target);
                    this.loadImage($img);
                    this.state.observers.image.unobserve(entry.target);
                }
            });
        },

        /**
         * Handle content intersection
         */
        handleContentIntersection: function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const $content = $(entry.target);
                    this.loadContent($content);
                    this.state.observers.content.unobserve(entry.target);
                }
            });
        },

        /**
         * Handle background image intersection
         */
        handleBackgroundIntersection: function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const $element = $(entry.target);
                    this.loadBackgroundImage($element);
                    this.state.observers.background.unobserve(entry.target);
                }
            });
        },

        /**
         * Handle media intersection
         */
        handleMediaIntersection: function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const $element = $(entry.target);
                    
                    if ($element.is('video')) {
                        this.loadVideo($element);
                    } else if ($element.is('iframe')) {
                        this.loadIframe($element);
                    }
                    
                    this.state.observers.media.unobserve(entry.target);
                }
            });
        },

        /**
         * Prepare image for lazy loading
         */
        prepareImage: function($img) {
            $img.addClass(this.config.classes.lazy);
            
            // Add placeholder if not exists
            if (!$img.attr('src') && !$img.hasClass(this.config.classes.placeholder)) {
                const placeholder = this.createImagePlaceholder($img);
                $img.attr('src', placeholder);
            }

            // Setup progressive loading if enabled
            if ($img.hasClass(this.config.classes.progressive)) {
                this.setupProgressiveImage($img);
            }

            // Add loading attribute for modern browsers
            if (!$img.attr('loading')) {
                $img.attr('loading', 'lazy');
            }
        },

        /**
         * Prepare background image
         */
        prepareBackgroundImage: function($element) {
            $element.addClass(this.config.classes.lazy);
        },

        /**
         * Prepare video for lazy loading
         */
        prepareVideo: function($video) {
            $video.addClass(this.config.classes.lazy);
            $video.attr('preload', 'none');
        },

        /**
         * Prepare iframe for lazy loading
         */
        prepareIframe: function($iframe) {
            $iframe.addClass(this.config.classes.lazy);
            
            // Create placeholder for iframe
            const placeholder = this.createIframePlaceholder($iframe);
            $iframe.before(placeholder);
        },

        /**
         * Prepare content for lazy loading
         */
        prepareContent: function($content) {
            $content.addClass(this.config.classes.lazy);
            $content.addClass(this.config.classes.fadeIn);
        },

        /**
         * Load image
         */
        loadImage: function($img) {
            const startTime = performance.now();
            const src = $img.attr(this.config.attributes.src);
            const srcset = $img.attr(this.config.attributes.srcset);
            const sizes = $img.attr(this.config.attributes.sizes);
            
            if (!src) return;

            $img.addClass(this.config.classes.loading);

            // Create new image for preloading
            const newImg = new Image();
            
            newImg.onload = () => {
                this.onImageLoad($img, newImg, startTime);
            };
            
            newImg.onerror = () => {
                this.onImageError($img);
            };

            // Set attributes
            if (srcset) newImg.srcset = srcset;
            if (sizes) newImg.sizes = sizes;
            
            // Start loading
            newImg.src = src;
        },

        /**
         * Handle successful image load
         */
        onImageLoad: function($img, newImg, startTime) {
            const loadTime = performance.now() - startTime;
            this.state.performance.loadTimes.push(loadTime);

            // Update image attributes
            $img.attr('src', newImg.src);
            if (newImg.srcset) $img.attr('srcset', newImg.srcset);
            if (newImg.sizes) $img.attr('sizes', newImg.sizes);

            // Update classes
            $img.removeClass(this.config.classes.loading)
                .removeClass(this.config.classes.lazy)
                .addClass(this.config.classes.loaded);

            // Progressive image transition
            if ($img.hasClass(this.config.classes.progressive)) {
                this.finishProgressiveImage($img);
            }

            // Accessibility update
            if (!$img.attr('alt')) {
                $img.attr('alt', this.generateAltText($img));
            }

            // Update counter
            this.state.loadedCount++;
            this.updateProgress();

            // Trigger custom event
            $img.trigger('recruitpro:image-loaded', [loadTime]);
        },

        /**
         * Handle image load error
         */
        onImageError: function($img) {
            const retryCount = parseInt($img.attr(this.config.attributes.retryCount) || '0');
            
            if (retryCount < this.config.retry.maxAttempts) {
                // Retry loading
                setTimeout(() => {
                    $img.attr(this.config.attributes.retryCount, retryCount + 1);
                    this.loadImage($img);
                }, this.config.retry.delay * (retryCount + 1));
            } else {
                // Show error state
                $img.removeClass(this.config.classes.loading)
                    .removeClass(this.config.classes.lazy)
                    .addClass(this.config.classes.error);
                
                // Set fallback image
                const fallback = this.createErrorPlaceholder($img);
                $img.attr('src', fallback);
                
                this.state.performance.errors.push({
                    element: $img[0],
                    src: $img.attr(this.config.attributes.src),
                    timestamp: Date.now()
                });
            }
        },

        /**
         * Load background image
         */
        loadBackgroundImage: function($element) {
            const bgSrc = $element.attr(this.config.attributes.bgSrc);
            if (!bgSrc) return;

            $element.addClass(this.config.classes.loading);

            const img = new Image();
            img.onload = () => {
                $element.css('background-image', `url(${bgSrc})`)
                        .removeClass(this.config.classes.loading)
                        .removeClass(this.config.classes.lazy)
                        .addClass(this.config.classes.loaded);
                
                this.state.loadedCount++;
                this.updateProgress();
            };
            
            img.onerror = () => {
                $element.removeClass(this.config.classes.loading)
                        .addClass(this.config.classes.error);
            };
            
            img.src = bgSrc;
        },

        /**
         * Load video
         */
        loadVideo: function($video) {
            const src = $video.attr(this.config.attributes.src);
            if (!src) return;

            $video.addClass(this.config.classes.loading);
            $video.attr('src', src);
            $video.attr('preload', 'metadata');

            $video.on('loadedmetadata', () => {
                $video.removeClass(this.config.classes.loading)
                      .removeClass(this.config.classes.lazy)
                      .addClass(this.config.classes.loaded);
                
                this.state.loadedCount++;
                this.updateProgress();
            });

            $video.on('error', () => {
                $video.removeClass(this.config.classes.loading)
                      .addClass(this.config.classes.error);
            });
        },

        /**
         * Load iframe
         */
        loadIframe: function($iframe) {
            const src = $iframe.attr(this.config.attributes.src);
            if (!src) return;

            $iframe.addClass(this.config.classes.loading);
            $iframe.attr('src', src);

            $iframe.on('load', () => {
                $iframe.removeClass(this.config.classes.loading)
                       .removeClass(this.config.classes.lazy)
                       .addClass(this.config.classes.loaded);
                
                // Remove placeholder
                $iframe.prev('.iframe-placeholder').remove();
                
                this.state.loadedCount++;
                this.updateProgress();
            });

            $iframe.on('error', () => {
                $iframe.removeClass(this.config.classes.loading)
                       .addClass(this.config.classes.error);
            });
        },

        /**
         * Load content section
         */
        loadContent: function($content) {
            const contentSrc = $content.attr(this.config.attributes.lazyContent);
            
            if (contentSrc) {
                // Load external content
                this.loadExternalContent($content, contentSrc);
            } else {
                // Just reveal existing content
                $content.removeClass(this.config.classes.lazy)
                        .addClass(this.config.classes.loaded)
                        .addClass(this.config.classes.visible);
                
                this.state.loadedCount++;
                this.updateProgress();
            }
        },

        /**
         * Load external content via AJAX
         */
        loadExternalContent: function($content, src) {
            $content.addClass(this.config.classes.loading);

            $.ajax({
                url: src,
                method: 'GET',
                timeout: 10000,
                success: (data) => {
                    $content.html(data)
                            .removeClass(this.config.classes.loading)
                            .removeClass(this.config.classes.lazy)
                            .addClass(this.config.classes.loaded)
                            .addClass(this.config.classes.visible);
                    
                    // Process any new lazy elements in loaded content
                    this.processNewContent($content);
                    
                    this.state.loadedCount++;
                    this.updateProgress();
                },
                error: () => {
                    $content.removeClass(this.config.classes.loading)
                            .addClass(this.config.classes.error);
                }
            });
        },

        /**
         * Create image placeholder
         */
        createImagePlaceholder: function($img) {
            const width = $img.width() || 400;
            const height = $img.height() || 300;
            
            // Generate SVG placeholder
            return `data:image/svg+xml;base64,${btoa(`
                <svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
                    <rect width="100%" height="100%" fill="#f1f5f9"/>
                    <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#94a3b8" font-family="Arial, sans-serif" font-size="14">Loading...</text>
                </svg>
            `)}`;
        },

        /**
         * Create error placeholder
         */
        createErrorPlaceholder: function($img) {
            const width = $img.width() || 400;
            const height = $img.height() || 300;
            
            return `data:image/svg+xml;base64,${btoa(`
                <svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
                    <rect width="100%" height="100%" fill="#fef2f2"/>
                    <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#ef4444" font-family="Arial, sans-serif" font-size="14">Failed to load</text>
                </svg>
            `)}`;
        },

        /**
         * Create iframe placeholder
         */
        createIframePlaceholder: function($iframe) {
            const width = $iframe.attr('width') || '100%';
            const height = $iframe.attr('height') || '315';
            
            return $(`
                <div class="iframe-placeholder" style="width: ${width}; height: ${height}px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="text-align: center; color: #64748b;">
                        <div style="font-size: 24px; margin-bottom: 8px;">📺</div>
                        <div>Click to load content</div>
                    </div>
                </div>
            `);
        },

        /**
         * Setup progressive image loading
         */
        setupProgressiveImage: function($img) {
            const src = $img.attr(this.config.attributes.src);
            if (!src) return;

            // Create low-quality placeholder
            const lowQualitySrc = this.createLowQualityVersion(src);
            
            if (lowQualitySrc) {
                $img.attr('src', lowQualitySrc)
                    .addClass(this.config.classes.blur)
                    .css('filter', `blur(${this.config.progressive.blur}px)`);
            }
        },

        /**
         * Finish progressive image loading
         */
        finishProgressiveImage: function($img) {
            $img.removeClass(this.config.classes.blur)
                .css('filter', 'none');
        },

        /**
         * Create low quality version of image
         */
        createLowQualityVersion: function(src) {
            // This would typically be handled server-side
            // For now, return a simple placeholder
            return src.replace(/\.(jpg|jpeg|png|webp)$/i, '_low.$1');
        },

        /**
         * Generate alt text from image filename
         */
        generateAltText: function($img) {
            const src = $img.attr('src');
            if (!src) return '';
            
            const filename = src.split('/').pop().split('.')[0];
            return filename.replace(/[-_]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        /**
         * Update loading progress
         */
        updateProgress: function() {
            const progress = Math.round((this.state.loadedCount / this.state.totalCount) * 100);
            
            this.cache.$document.trigger('recruitpro:lazy-loading-progress', {
                loaded: this.state.loadedCount,
                total: this.state.totalCount,
                percentage: progress
            });

            if (this.state.loadedCount >= this.state.totalCount) {
                this.onLoadingComplete();
            }
        },

        /**
         * Handle loading completion
         */
        onLoadingComplete: function() {
            this.cache.$body.addClass('lazy-loading-complete');
            
            this.cache.$document.trigger('recruitpro:lazy-loading-complete', {
                totalTime: performance.now() - this.state.performance.startTime,
                averageLoadTime: this.getAverageLoadTime(),
                errors: this.state.performance.errors.length
            });
        },

        /**
         * Get average load time
         */
        getAverageLoadTime: function() {
            const times = this.state.performance.loadTimes;
            return times.length > 0 ? times.reduce((a, b) => a + b) / times.length : 0;
        },

        /**
         * Start performance monitoring
         */
        startPerformanceMonitoring: function() {
            this.state.performance.startTime = performance.now();
        },

        /**
         * Detect WebP support
         */
        detectWebPSupport: function() {
            const canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = 1;
            return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
        },

        /**
         * Detect AVIF support
         */
        detectAVIFSupport: function() {
            const canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = 1;
            return canvas.toDataURL('image/avif').indexOf('data:image/avif') === 0;
        },

        /**
         * Adjust loading strategy based on network
         */
        adjustLoadingStrategy: function() {
            if ('connection' in navigator) {
                const connection = navigator.connection;
                
                if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                    // Reduce quality for slow connections
                    this.config.progressive.quality = 5;
                    this.config.observer.rootMargin = '25px 0px';
                } else if (connection.effectiveType === '4g') {
                    // Higher quality for fast connections
                    this.config.progressive.quality = 20;
                    this.config.observer.rootMargin = '100px 0px';
                }
            }
        },

        /**
         * Setup fallback for browsers without IntersectionObserver
         */
        setupFallback: function() {
            console.warn('IntersectionObserver not supported, using scroll fallback');
            
            const self = this;
            
            function checkVisibility() {
                const scrollTop = self.cache.$window.scrollTop();
                const windowHeight = self.cache.$window.height();
                const viewportBottom = scrollTop + windowHeight;
                
                // Check images
                self.cache.$images.filter('.' + self.config.classes.lazy).each(function() {
                    const $img = $(this);
                    const elementTop = $img.offset().top;
                    
                    if (elementTop < viewportBottom + 100) {
                        self.loadImage($img);
                    }
                });
                
                // Check other elements...
            }
            
            this.cache.$window.on('scroll resize', this.utils.throttle(checkVisibility, 100));
            checkVisibility();
        },

        /**
         * Process new content added dynamically
         */
        processNewContent: function($container) {
            const $newImages = $container.find(this.config.selectors.image);
            const $newContent = $container.find(this.config.selectors.content);
            
            $newImages.each((index, element) => {
                const $img = $(element);
                if (!$img.hasClass(this.config.classes.loaded)) {
                    this.prepareImage($img);
                    if (this.state.observers.image) {
                        this.state.observers.image.observe(element);
                    }
                }
            });
            
            $newContent.each((index, element) => {
                const $content = $(element);
                if (!$content.hasClass(this.config.classes.loaded)) {
                    this.prepareContent($content);
                    if (this.state.observers.content) {
                        this.state.observers.content.observe(element);
                    }
                }
            });
        },

        /**
         * Refresh lazy loading for new content
         */
        refresh: function() {
            this.cacheElements();
            this.processExistingElements();
        },

        /**
         * Resume loading when page becomes visible
         */
        resumeLoading: function() {
            // Resume any paused operations
            this.cache.$document.trigger('recruitpro:lazy-loading-resumed');
        },

        /**
         * Handle window load
         */
        onWindowLoad: function() {
            // Load any critical images immediately
            $('.critical-image.' + this.config.classes.lazy).each((index, element) => {
                this.loadImage($(element));
            });
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            // Recalculate visibility for fallback method
            if (!this.state.isSupported.intersectionObserver) {
                this.cache.$window.trigger('scroll');
            }
        },

        /**
         * Handle image error from external sources
         */
        handleImageError: function(element) {
            this.onImageError($(element));
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
            load: function(selector) {
                const $elements = $(selector);
                $elements.each((index, element) => {
                    const $element = $(element);
                    if ($element.is('img')) {
                        LazyLoading.loadImage($element);
                    } else if ($element.attr(LazyLoading.config.attributes.bgSrc)) {
                        LazyLoading.loadBackgroundImage($element);
                    } else if ($element.is('video')) {
                        LazyLoading.loadVideo($element);
                    } else if ($element.is('iframe')) {
                        LazyLoading.loadIframe($element);
                    } else {
                        LazyLoading.loadContent($element);
                    }
                });
            },

            refresh: function() {
                LazyLoading.refresh();
            },

            getStats: function() {
                return {
                    loaded: LazyLoading.state.loadedCount,
                    total: LazyLoading.state.totalCount,
                    errors: LazyLoading.state.performance.errors.length,
                    averageLoadTime: LazyLoading.getAverageLoadTime()
                };
            },

            isSupported: function() {
                return LazyLoading.state.isSupported;
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        LazyLoading.init();
    });

    // Expose public API
    window.RecruitPro.LazyLoading = LazyLoading.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        LazyLoading.refresh();
    });

})(jQuery);