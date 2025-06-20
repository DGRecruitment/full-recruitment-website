/**
 * Performance Optimization Component - RecruitPro Theme
 * 
 * Comprehensive performance monitoring and optimization system
 * Designed for recruitment agency websites with focus on Core Web Vitals
 * 
 * Features:
 * - Web Vitals monitoring (LCP, FID, CLS, TTFB)
 * - Resource optimization (preloading, prefetching, lazy loading)
 * - Memory management and cleanup
 * - Network optimization and adaptive loading
 * - Performance budget monitoring
 * - Third-party script optimization
 * - Image optimization coordination
 * - Cache management strategies
 * - Performance analytics integration
 * - Real User Monitoring (RUM)
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Performance namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.Performance = window.RecruitPro.Performance || {};

    /**
     * Performance Optimization Component
     */
    const Performance = {
        
        // Configuration
        config: {
            // Web Vitals thresholds
            vitals: {
                lcp: { good: 2500, poor: 4000 },      // Largest Contentful Paint (ms)
                fid: { good: 100, poor: 300 },        // First Input Delay (ms)
                cls: { good: 0.1, poor: 0.25 },       // Cumulative Layout Shift
                ttfb: { good: 800, poor: 1800 },      // Time to First Byte (ms)
                fcp: { good: 1800, poor: 3000 },      // First Contentful Paint (ms)
                inp: { good: 200, poor: 500 }         // Interaction to Next Paint (ms)
            },
            
            // Performance budgets
            budgets: {
                totalSize: 2 * 1024 * 1024,           // 2MB total page size
                imageSize: 1 * 1024 * 1024,           // 1MB for images
                scriptSize: 300 * 1024,               // 300KB for scripts
                styleSize: 100 * 1024,                // 100KB for styles
                fontSize: 200 * 1024,                 // 200KB for fonts
                maxRequests: 50,                      // Maximum HTTP requests
                maxDomElements: 1500                   // Maximum DOM elements
            },
            
            // Resource priorities
            resources: {
                critical: ['css', 'fonts', 'above-fold-images'],
                important: ['main-js', 'hero-content', 'navigation'],
                deferred: ['analytics', 'social-widgets', 'below-fold-content'],
                lazy: ['videos', 'heavy-images', 'third-party-scripts']
            },
            
            // Network thresholds
            network: {
                slow: ['slow-2g', '2g'],
                medium: ['3g'],
                fast: ['4g']
            },
            
            // Monitoring intervals
            monitoring: {
                vitalsCheck: 5000,                     // Check vitals every 5s
                memoryCheck: 10000,                    // Check memory every 10s
                resourceCheck: 15000,                  // Check resources every 15s
                cleanupInterval: 30000                 // Cleanup every 30s
            }
        },

        // State management
        state: {
            vitals: {
                lcp: null,
                fid: null,
                cls: null,
                ttfb: null,
                fcp: null,
                inp: null
            },
            resources: {
                loaded: [],
                failed: [],
                pending: [],
                totalSize: 0,
                totalRequests: 0
            },
            performance: {
                domInteractive: 0,
                domComplete: 0,
                loadTime: 0,
                firstPaint: 0,
                firstContentfulPaint: 0
            },
            network: {
                type: 'unknown',
                effectiveType: 'unknown',
                downlink: 0,
                rtt: 0,
                saveData: false
            },
            memory: {
                used: 0,
                total: 0,
                limit: 0
            },
            observers: {
                vitals: null,
                resources: null,
                memory: null
            },
            isMonitoring: false,
            optimizations: {
                applied: [],
                deferred: [],
                failed: []
            }
        },

        /**
         * Initialize the performance system
         */
        init: function() {
            if (this.initialized) return;
            
            this.detectCapabilities();
            this.setupWebVitalsMonitoring();
            this.setupResourceMonitoring();
            this.setupNetworkMonitoring();
            this.setupMemoryMonitoring();
            this.applyOptimizations();
            this.startMonitoring();
            this.bindEvents();
            
            this.initialized = true;
            console.log('RecruitPro Performance System initialized');
        },

        /**
         * Detect browser capabilities and features
         */
        detectCapabilities: function() {
            this.capabilities = {
                webVitals: 'PerformanceObserver' in window,
                resourceTiming: 'performance' in window && 'getEntriesByType' in performance,
                navigationTiming: 'performance' in window && 'timing' in performance,
                memoryInfo: 'memory' in performance,
                networkInfo: 'connection' in navigator,
                intersectionObserver: 'IntersectionObserver' in window,
                requestIdleCallback: 'requestIdleCallback' in window,
                serviceWorker: 'serviceWorker' in navigator,
                webP: this.supportsWebP(),
                avif: this.supportsAVIF(),
                preload: this.supportsPreload(),
                prefetch: this.supportsPrefetch()
            };
        },

        /**
         * Setup Web Vitals monitoring
         */
        setupWebVitalsMonitoring: function() {
            if (!this.capabilities.webVitals) {
                console.warn('Web Vitals monitoring not supported');
                return;
            }

            try {
                // Largest Contentful Paint (LCP)
                this.observeVital('largest-contentful-paint', (entry) => {
                    this.state.vitals.lcp = entry.startTime;
                    this.reportVital('LCP', entry.startTime);
                });

                // First Input Delay (FID)
                this.observeVital('first-input', (entry) => {
                    this.state.vitals.fid = entry.processingStart - entry.startTime;
                    this.reportVital('FID', this.state.vitals.fid);
                });

                // Cumulative Layout Shift (CLS)
                this.observeVital('layout-shift', (entry) => {
                    if (!entry.hadRecentInput) {
                        this.state.vitals.cls = (this.state.vitals.cls || 0) + entry.value;
                        this.reportVital('CLS', this.state.vitals.cls);
                    }
                });

                // First Contentful Paint (FCP)
                this.observeVital('paint', (entry) => {
                    if (entry.name === 'first-contentful-paint') {
                        this.state.vitals.fcp = entry.startTime;
                        this.reportVital('FCP', entry.startTime);
                    }
                });

                // Navigation timing for TTFB
                this.calculateTTFB();

            } catch (error) {
                console.error('Error setting up Web Vitals monitoring:', error);
            }
        },

        /**
         * Observe a specific vital metric
         */
        observeVital: function(type, callback) {
            try {
                const observer = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach(callback);
                });
                
                observer.observe({ type: type, buffered: true });
                return observer;
            } catch (error) {
                console.warn(`Could not observe ${type}:`, error);
                return null;
            }
        },

        /**
         * Calculate Time to First Byte (TTFB)
         */
        calculateTTFB: function() {
            if (this.capabilities.navigationTiming) {
                const timing = performance.timing;
                const ttfb = timing.responseStart - timing.navigationStart;
                this.state.vitals.ttfb = ttfb;
                this.reportVital('TTFB', ttfb);
            }
        },

        /**
         * Setup resource monitoring
         */
        setupResourceMonitoring: function() {
            if (!this.capabilities.resourceTiming) return;

            // Monitor resource loading
            this.state.observers.resources = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach((entry) => {
                    this.trackResource(entry);
                });
            });

            this.state.observers.resources.observe({ 
                type: 'resource',
                buffered: true 
            });

            // Monitor navigation
            this.trackNavigation();
        },

        /**
         * Track individual resource loading
         */
        trackResource: function(entry) {
            const resource = {
                name: entry.name,
                type: this.getResourceType(entry),
                size: entry.transferSize || entry.encodedBodySize || 0,
                duration: entry.duration,
                startTime: entry.startTime,
                status: entry.responseStatus || 200
            };

            this.state.resources.totalSize += resource.size;
            this.state.resources.totalRequests++;

            if (resource.status >= 200 && resource.status < 300) {
                this.state.resources.loaded.push(resource);
            } else {
                this.state.resources.failed.push(resource);
            }

            // Check performance budgets
            this.checkPerformanceBudgets();

            // Optimize resource loading if needed
            this.optimizeResourceLoading(resource);
        },

        /**
         * Get resource type from entry
         */
        getResourceType: function(entry) {
            if (entry.initiatorType) {
                return entry.initiatorType;
            }
            
            const name = entry.name.toLowerCase();
            if (name.includes('.css')) return 'stylesheet';
            if (name.includes('.js')) return 'script';
            if (name.match(/\.(jpg|jpeg|png|gif|webp|avif|svg)$/)) return 'img';
            if (name.match(/\.(woff|woff2|ttf|otf)$/)) return 'font';
            if (name.match(/\.(mp4|webm|ogg)$/)) return 'video';
            
            return 'other';
        },

        /**
         * Track navigation performance
         */
        trackNavigation: function() {
            if (!this.capabilities.navigationTiming) return;

            window.addEventListener('load', () => {
                const timing = performance.timing;
                
                this.state.performance = {
                    domInteractive: timing.domInteractive - timing.navigationStart,
                    domComplete: timing.domComplete - timing.navigationStart,
                    loadTime: timing.loadEventEnd - timing.navigationStart,
                    firstPaint: this.getFirstPaint(),
                    firstContentfulPaint: this.state.vitals.fcp || 0
                };

                this.reportPerformanceMetrics();
            });
        },

        /**
         * Get First Paint timing
         */
        getFirstPaint: function() {
            const paintEntries = performance.getEntriesByType('paint');
            const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
            return firstPaint ? firstPaint.startTime : 0;
        },

        /**
         * Setup network monitoring
         */
        setupNetworkMonitoring: function() {
            if (!this.capabilities.networkInfo) return;

            const connection = navigator.connection;
            
            this.state.network = {
                type: connection.type || 'unknown',
                effectiveType: connection.effectiveType || 'unknown',
                downlink: connection.downlink || 0,
                rtt: connection.rtt || 0,
                saveData: connection.saveData || false
            };

            // Listen for network changes
            connection.addEventListener('change', () => {
                this.handleNetworkChange();
            });

            // Apply network-based optimizations
            this.applyNetworkOptimizations();
        },

        /**
         * Handle network changes
         */
        handleNetworkChange: function() {
            const connection = navigator.connection;
            
            this.state.network = {
                type: connection.type || 'unknown',
                effectiveType: connection.effectiveType || 'unknown',
                downlink: connection.downlink || 0,
                rtt: connection.rtt || 0,
                saveData: connection.saveData || false
            };

            this.applyNetworkOptimizations();
            
            // Trigger custom event
            $(document).trigger('recruitpro:network-change', [this.state.network]);
        },

        /**
         * Setup memory monitoring
         */
        setupMemoryMonitoring: function() {
            if (!this.capabilities.memoryInfo) return;

            this.updateMemoryInfo();
            
            // Monitor memory periodically
            setInterval(() => {
                this.updateMemoryInfo();
                this.checkMemoryUsage();
            }, this.config.monitoring.memoryCheck);
        },

        /**
         * Update memory information
         */
        updateMemoryInfo: function() {
            if (performance.memory) {
                this.state.memory = {
                    used: performance.memory.usedJSHeapSize,
                    total: performance.memory.totalJSHeapSize,
                    limit: performance.memory.jsHeapSizeLimit
                };
            }
        },

        /**
         * Check memory usage and cleanup if needed
         */
        checkMemoryUsage: function() {
            const memoryUsagePercent = (this.state.memory.used / this.state.memory.limit) * 100;
            
            if (memoryUsagePercent > 80) {
                console.warn('High memory usage detected:', memoryUsagePercent + '%');
                this.performMemoryCleanup();
            }
        },

        /**
         * Perform memory cleanup
         */
        performMemoryCleanup: function() {
            // Clean up unused observers
            this.cleanupObservers();
            
            // Clear old performance entries
            if (performance.clearResourceTimings) {
                performance.clearResourceTimings();
            }
            
            // Trigger garbage collection hint
            if (window.gc) {
                window.gc();
            }
            
            // Trigger custom cleanup event
            $(document).trigger('recruitpro:memory-cleanup');
        },

        /**
         * Apply performance optimizations
         */
        applyOptimizations: function() {
            this.preloadCriticalResources();
            this.prefetchImportantResources();
            this.optimizeImages();
            this.optimizeScripts();
            this.optimizeFonts();
            this.setupServiceWorker();
            this.enableCompression();
        },

        /**
         * Preload critical resources
         */
        preloadCriticalResources: function() {
            if (!this.capabilities.preload) return;

            const criticalResources = [
                { href: '/wp-content/themes/recruitpro/assets/css/main.css', as: 'style' },
                { href: '/wp-content/themes/recruitpro/assets/js/main.js', as: 'script' },
                { href: '/wp-content/themes/recruitpro/assets/fonts/inter-regular.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' }
            ];

            criticalResources.forEach(resource => {
                this.preloadResource(resource);
            });
        },

        /**
         * Preload a specific resource
         */
        preloadResource: function(resource) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource.href;
            link.as = resource.as;
            
            if (resource.type) link.type = resource.type;
            if (resource.crossorigin) link.crossOrigin = resource.crossorigin;
            
            document.head.appendChild(link);
            
            this.state.optimizations.applied.push(`preload:${resource.href}`);
        },

        /**
         * Prefetch important resources
         */
        prefetchImportantResources: function() {
            if (!this.capabilities.prefetch) return;

            const importantResources = [
                '/jobs/',
                '/about/',
                '/contact/',
                '/wp-content/themes/recruitpro/assets/js/navigation.js'
            ];

            importantResources.forEach(resource => {
                this.prefetchResource(resource);
            });
        },

        /**
         * Prefetch a specific resource
         */
        prefetchResource: function(href) {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = href;
            
            document.head.appendChild(link);
            
            this.state.optimizations.applied.push(`prefetch:${href}`);
        },

        /**
         * Optimize images
         */
        optimizeImages: function() {
            // Set up responsive images
            this.setupResponsiveImages();
            
            // Enable native lazy loading
            this.enableNativeLazyLoading();
            
            // Optimize image formats
            this.optimizeImageFormats();
        },

        /**
         * Setup responsive images
         */
        setupResponsiveImages: function() {
            $('img').each(function() {
                const $img = $(this);
                
                if (!$img.attr('sizes') && !$img.attr('srcset')) {
                    // Add default responsive behavior
                    const src = $img.attr('src');
                    if (src) {
                        const baseName = src.replace(/\.[^/.]+$/, "");
                        const extension = src.split('.').pop();
                        
                        // Generate srcset for different sizes
                        const srcset = [
                            `${baseName}-480w.${extension} 480w`,
                            `${baseName}-768w.${extension} 768w`,
                            `${baseName}-1024w.${extension} 1024w`,
                            `${baseName}-1920w.${extension} 1920w`
                        ].join(', ');
                        
                        $img.attr('srcset', srcset);
                        $img.attr('sizes', '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw');
                    }
                }
            });
        },

        /**
         * Enable native lazy loading
         */
        enableNativeLazyLoading: function() {
            $('img, iframe').each(function() {
                const $element = $(this);
                
                if (!$element.attr('loading')) {
                    // Don't lazy load above-the-fold images
                    const elementTop = $element.offset().top;
                    const viewportHeight = $(window).height();
                    
                    if (elementTop > viewportHeight) {
                        $element.attr('loading', 'lazy');
                    }
                }
            });
        },

        /**
         * Optimize image formats
         */
        optimizeImageFormats: function() {
            if (this.capabilities.webP || this.capabilities.avif) {
                $('img').each(function() {
                    const $img = $(this);
                    const src = $img.attr('src');
                    
                    if (src && !src.includes('data:')) {
                        // Convert to modern format if supported
                        if (this.capabilities.avif && !src.includes('.avif')) {
                            $img.attr('src', src.replace(/\.(jpg|jpeg|png)$/i, '.avif'));
                        } else if (this.capabilities.webP && !src.includes('.webp')) {
                            $img.attr('src', src.replace(/\.(jpg|jpeg|png)$/i, '.webp'));
                        }
                    }
                }.bind(this));
            }
        },

        /**
         * Optimize scripts
         */
        optimizeScripts: function() {
            // Defer non-critical scripts
            $('script[src]').each(function() {
                const $script = $(this);
                const src = $script.attr('src');
                
                // Skip critical scripts
                if (src && !src.includes('main.js') && !src.includes('jquery')) {
                    if (!$script.attr('async') && !$script.attr('defer')) {
                        $script.attr('defer', 'defer');
                    }
                }
            });

            // Load third-party scripts asynchronously
            this.loadThirdPartyScriptsAsync();
        },

        /**
         * Load third-party scripts asynchronously
         */
        loadThirdPartyScriptsAsync: function() {
            const thirdPartyScripts = [
                'google-analytics.com',
                'googletagmanager.com',
                'facebook.net',
                'twitter.com',
                'linkedin.com'
            ];

            $('script[src]').each(function() {
                const $script = $(this);
                const src = $script.attr('src');
                
                if (src) {
                    thirdPartyScripts.forEach(domain => {
                        if (src.includes(domain)) {
                            $script.attr('async', 'async');
                        }
                    });
                }
            });
        },

        /**
         * Optimize fonts
         */
        optimizeFonts: function() {
            // Add font-display: swap to font faces
            const fontFaces = document.querySelectorAll('style');
            fontFaces.forEach(style => {
                if (style.textContent.includes('@font-face')) {
                    style.textContent = style.textContent.replace(
                        /@font-face\s*{([^}]+)}/g,
                        (match, rules) => {
                            if (!rules.includes('font-display')) {
                                return match.replace('}', 'font-display: swap; }');
                            }
                            return match;
                        }
                    );
                }
            });
        },

        /**
         * Setup service worker
         */
        setupServiceWorker: function() {
            if (!this.capabilities.serviceWorker) return;

            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registered:', registration);
                        this.state.optimizations.applied.push('service-worker');
                    })
                    .catch(error => {
                        console.log('Service Worker registration failed:', error);
                        this.state.optimizations.failed.push('service-worker');
                    });
            }
        },

        /**
         * Enable compression
         */
        enableCompression: function() {
            // This is typically handled server-side
            // Here we just check if compression is enabled
            const testImg = new Image();
            testImg.onload = () => {
                if (testImg.naturalWidth > 0) {
                    this.state.optimizations.applied.push('image-compression');
                }
            };
            testImg.src = 'data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==';
        },

        /**
         * Apply network-based optimizations
         */
        applyNetworkOptimizations: function() {
            const effectiveType = this.state.network.effectiveType;
            
            if (this.config.network.slow.includes(effectiveType) || this.state.network.saveData) {
                this.applySlowNetworkOptimizations();
            } else if (this.config.network.fast.includes(effectiveType)) {
                this.applyFastNetworkOptimizations();
            }
        },

        /**
         * Apply optimizations for slow networks
         */
        applySlowNetworkOptimizations: function() {
            // Reduce image quality
            $('img').each(function() {
                const $img = $(this);
                $img.addClass('low-quality');
            });

            // Disable auto-play videos
            $('video[autoplay]').each(function() {
                $(this).removeAttr('autoplay').attr('data-autoplay', 'disabled');
            });

            // Defer non-essential content
            $('.heavy-content').addClass('defer-loading');
            
            this.state.optimizations.applied.push('slow-network-optimizations');
        },

        /**
         * Apply optimizations for fast networks
         */
        applyFastNetworkOptimizations: function() {
            // Preload additional resources
            this.preloadAdditionalResources();
            
            // Enable higher quality images
            $('img.low-quality').removeClass('low-quality');
            
            this.state.optimizations.applied.push('fast-network-optimizations');
        },

        /**
         * Preload additional resources for fast networks
         */
        preloadAdditionalResources: function() {
            const additionalResources = [
                '/wp-content/themes/recruitpro/assets/js/modal-system.js',
                '/wp-content/themes/recruitpro/assets/js/image-gallery.js'
            ];

            additionalResources.forEach(resource => {
                this.prefetchResource(resource);
            });
        },

        /**
         * Check performance budgets
         */
        checkPerformanceBudgets: function() {
            const budgets = this.config.budgets;
            const warnings = [];

            if (this.state.resources.totalSize > budgets.totalSize) {
                warnings.push(`Total page size (${this.formatBytes(this.state.resources.totalSize)}) exceeds budget (${this.formatBytes(budgets.totalSize)})`);
            }

            if (this.state.resources.totalRequests > budgets.maxRequests) {
                warnings.push(`Total requests (${this.state.resources.totalRequests}) exceeds budget (${budgets.maxRequests})`);
            }

            const domElements = document.querySelectorAll('*').length;
            if (domElements > budgets.maxDomElements) {
                warnings.push(`DOM elements (${domElements}) exceeds budget (${budgets.maxDomElements})`);
            }

            if (warnings.length > 0) {
                console.warn('Performance budget violations:', warnings);
                this.reportBudgetViolations(warnings);
            }
        },

        /**
         * Start monitoring
         */
        startMonitoring: function() {
            this.state.isMonitoring = true;
            
            // Start periodic checks
            setInterval(() => {
                this.checkPerformanceBudgets();
                this.updateMemoryInfo();
            }, this.config.monitoring.vitalsCheck);

            // Cleanup interval
            setInterval(() => {
                this.performMemoryCleanup();
            }, this.config.monitoring.cleanupInterval);
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.pauseMonitoring();
                } else {
                    this.resumeMonitoring();
                }
            });

            // Page unload
            window.addEventListener('beforeunload', () => {
                this.reportFinalMetrics();
            });

            // Custom events
            $(document).on('recruitpro:heavy-content-loaded', () => {
                this.checkPerformanceBudgets();
            });
        },

        /**
         * Report Web Vital
         */
        reportVital: function(name, value) {
            const threshold = this.config.vitals[name.toLowerCase()];
            let rating = 'good';
            
            if (threshold) {
                if (value > threshold.poor) {
                    rating = 'poor';
                } else if (value > threshold.good) {
                    rating = 'needs-improvement';
                }
            }

            // Send to analytics
            this.sendToAnalytics('web-vital', {
                metric: name,
                value: value,
                rating: rating
            });

            // Trigger custom event
            $(document).trigger('recruitpro:web-vital', {
                metric: name,
                value: value,
                rating: rating
            });
        },

        /**
         * Report performance metrics
         */
        reportPerformanceMetrics: function() {
            this.sendToAnalytics('performance-metrics', this.state.performance);
        },

        /**
         * Report budget violations
         */
        reportBudgetViolations: function(violations) {
            this.sendToAnalytics('budget-violations', { violations: violations });
        },

        /**
         * Report final metrics on page unload
         */
        reportFinalMetrics: function() {
            const finalReport = {
                vitals: this.state.vitals,
                performance: this.state.performance,
                resources: {
                    totalSize: this.state.resources.totalSize,
                    totalRequests: this.state.resources.totalRequests,
                    failed: this.state.resources.failed.length
                },
                optimizations: this.state.optimizations
            };

            this.sendToAnalytics('final-report', finalReport);
        },

        /**
         * Send data to analytics
         */
        sendToAnalytics: function(eventName, data) {
            // Google Analytics 4
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, data);
            }

            // Custom analytics endpoint
            if (window.recruitpro_analytics_endpoint) {
                navigator.sendBeacon(window.recruitpro_analytics_endpoint, JSON.stringify({
                    event: eventName,
                    data: data,
                    timestamp: Date.now(),
                    url: window.location.href
                }));
            }
        },

        /**
         * Pause monitoring
         */
        pauseMonitoring: function() {
            this.state.isMonitoring = false;
        },

        /**
         * Resume monitoring
         */
        resumeMonitoring: function() {
            this.state.isMonitoring = true;
        },

        /**
         * Cleanup observers
         */
        cleanupObservers: function() {
            Object.values(this.state.observers).forEach(observer => {
                if (observer && observer.disconnect) {
                    observer.disconnect();
                }
            });
        },

        /**
         * Helper functions
         */
        formatBytes: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        supportsWebP: function() {
            const canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = 1;
            return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
        },

        supportsAVIF: function() {
            const canvas = document.createElement('canvas');
            canvas.width = 1;
            canvas.height = 1;
            return canvas.toDataURL('image/avif').indexOf('data:image/avif') === 0;
        },

        supportsPreload: function() {
            const link = document.createElement('link');
            return link.relList && link.relList.supports && link.relList.supports('preload');
        },

        supportsPrefetch: function() {
            const link = document.createElement('link');
            return link.relList && link.relList.supports && link.relList.supports('prefetch');
        },

        /**
         * Public API methods
         */
        api: {
            getVitals: function() {
                return Performance.state.vitals;
            },

            getPerformanceMetrics: function() {
                return Performance.state.performance;
            },

            getResourceStats: function() {
                return Performance.state.resources;
            },

            getNetworkInfo: function() {
                return Performance.state.network;
            },

            getMemoryInfo: function() {
                return Performance.state.memory;
            },

            preloadResource: function(resource) {
                Performance.preloadResource(resource);
            },

            prefetchResource: function(href) {
                Performance.prefetchResource(href);
            },

            checkBudgets: function() {
                Performance.checkPerformanceBudgets();
            },

            cleanup: function() {
                Performance.performMemoryCleanup();
            },

            getOptimizations: function() {
                return Performance.state.optimizations;
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        Performance.init();
    });

    // Expose public API
    window.RecruitPro.Performance = Performance.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        Performance.checkPerformanceBudgets();
    });

})(jQuery);