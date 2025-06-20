/**
 * Image Gallery Component - RecruitPro Theme
 * 
 * Handles image galleries with lightbox functionality, lazy loading,
 * and responsive behavior for recruitment agency websites
 * 
 * Features:
 * - Lightbox modal with navigation
 * - Keyboard navigation support
 * - Touch/swipe gestures for mobile
 * - Lazy loading integration
 * - Accessibility features
 * - Performance optimization
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Gallery namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.Gallery = window.RecruitPro.Gallery || {};

    /**
     * Image Gallery Component
     */
    const ImageGallery = {
        
        // Configuration
        config: {
            selectors: {
                gallery: '.recruitpro-gallery',
                galleryItem: '.gallery-item',
                galleryImage: '.gallery-image',
                galleryLink: '.gallery-link',
                lightbox: '#recruitpro-lightbox',
                lightboxContent: '.lightbox-content',
                lightboxImage: '.lightbox-image',
                lightboxCaption: '.lightbox-caption',
                lightboxNav: '.lightbox-nav',
                lightboxClose: '.lightbox-close',
                lightboxPrev: '.lightbox-prev',
                lightboxNext: '.lightbox-next',
                lightboxCounter: '.lightbox-counter'
            },
            classes: {
                active: 'gallery-active',
                loading: 'gallery-loading',
                loaded: 'gallery-loaded',
                lightboxOpen: 'lightbox-open',
                lightboxLoading: 'lightbox-loading',
                noScroll: 'no-scroll',
                fadeIn: 'fade-in',
                fadeOut: 'fade-out'
            },
            animation: {
                duration: 300,
                easing: 'ease-in-out'
            },
            touch: {
                threshold: 50,
                startX: 0,
                startY: 0,
                enabled: true
            }
        },

        // State management
        state: {
            galleries: [],
            currentGallery: null,
            currentIndex: 0,
            isLightboxOpen: false,
            isLoading: false,
            touchStartX: 0,
            touchStartY: 0,
            preloadedImages: {}
        },

        /**
         * Initialize the gallery system
         */
        init: function() {
            if (this.initialized) return;
            
            this.createLightboxHTML();
            this.cacheElements();
            this.bindEvents();
            this.setupGalleries();
            this.setupAccessibility();
            
            this.initialized = true;
            console.log('RecruitPro Image Gallery initialized');
        },

        /**
         * Cache DOM elements
         */
        cacheElements: function() {
            this.cache = {
                $body: $('body'),
                $window: $(window),
                $document: $(document),
                $lightbox: $(this.config.selectors.lightbox),
                $lightboxContent: null,
                $lightboxImage: null,
                $lightboxCaption: null,
                $lightboxCounter: null,
                $lightboxPrev: null,
                $lightboxNext: null,
                $lightboxClose: null
            };

            // Cache lightbox elements after creation
            this.cacheLightboxElements();
        },

        /**
         * Cache lightbox specific elements
         */
        cacheLightboxElements: function() {
            if (this.cache.$lightbox.length) {
                this.cache.$lightboxContent = this.cache.$lightbox.find(this.config.selectors.lightboxContent);
                this.cache.$lightboxImage = this.cache.$lightbox.find(this.config.selectors.lightboxImage);
                this.cache.$lightboxCaption = this.cache.$lightbox.find(this.config.selectors.lightboxCaption);
                this.cache.$lightboxCounter = this.cache.$lightbox.find(this.config.selectors.lightboxCounter);
                this.cache.$lightboxPrev = this.cache.$lightbox.find(this.config.selectors.lightboxPrev);
                this.cache.$lightboxNext = this.cache.$lightbox.find(this.config.selectors.lightboxNext);
                this.cache.$lightboxClose = this.cache.$lightbox.find(this.config.selectors.lightboxClose);
            }
        },

        /**
         * Create lightbox HTML structure
         */
        createLightboxHTML: function() {
            if ($(this.config.selectors.lightbox).length) return;

            const lightboxHTML = `
                <div id="recruitpro-lightbox" class="recruitpro-lightbox" role="dialog" aria-modal="true" aria-label="Image Gallery">
                    <div class="lightbox-overlay"></div>
                    <div class="lightbox-content">
                        <button class="lightbox-close" aria-label="Close gallery" title="Close (Esc)">
                            <span class="sr-only">Close</span>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        
                        <div class="lightbox-main">
                            <img class="lightbox-image" src="" alt="" />
                            <div class="lightbox-loading">
                                <div class="loading-spinner"></div>
                            </div>
                        </div>
                        
                        <div class="lightbox-nav">
                            <button class="lightbox-prev" aria-label="Previous image" title="Previous (←)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="15,18 9,12 15,6"></polyline>
                                </svg>
                            </button>
                            <button class="lightbox-next" aria-label="Next image" title="Next (→)">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="9,18 15,12 9,6"></polyline>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="lightbox-info">
                            <div class="lightbox-caption"></div>
                            <div class="lightbox-counter"></div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(lightboxHTML);
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Gallery item clicks
            $(document).on('click', this.config.selectors.galleryLink, function(e) {
                e.preventDefault();
                const $gallery = $(this).closest(self.config.selectors.gallery);
                const $item = $(this).closest(self.config.selectors.galleryItem);
                const index = $item.index();
                
                self.openLightbox($gallery, index);
            });

            // Lightbox controls
            this.cache.$document.on('click', this.config.selectors.lightboxClose, () => this.closeLightbox());
            this.cache.$document.on('click', this.config.selectors.lightboxPrev, () => this.previousImage());
            this.cache.$document.on('click', this.config.selectors.lightboxNext, () => this.nextImage());

            // Lightbox overlay click
            this.cache.$document.on('click', '.lightbox-overlay', () => this.closeLightbox());

            // Keyboard navigation
            this.cache.$document.on('keydown', (e) => this.handleKeyboard(e));

            // Touch events for mobile
            if (this.config.touch.enabled) {
                this.bindTouchEvents();
            }

            // Window resize
            this.cache.$window.on('resize', this.utils.debounce(() => {
                if (this.state.isLightboxOpen) {
                    this.adjustLightboxSize();
                }
            }, 250));

            // Image load events
            this.cache.$document.on('load', '.lightbox-image', () => this.onImageLoad());
            this.cache.$document.on('error', '.lightbox-image', () => this.onImageError());
        },

        /**
         * Bind touch events for mobile navigation
         */
        bindTouchEvents: function() {
            this.cache.$document.on('touchstart', this.config.selectors.lightbox, (e) => {
                this.state.touchStartX = e.originalEvent.touches[0].clientX;
                this.state.touchStartY = e.originalEvent.touches[0].clientY;
            });

            this.cache.$document.on('touchend', this.config.selectors.lightbox, (e) => {
                if (!this.state.isLightboxOpen) return;

                const touchEndX = e.originalEvent.changedTouches[0].clientX;
                const touchEndY = e.originalEvent.changedTouches[0].clientY;
                const deltaX = touchEndX - this.state.touchStartX;
                const deltaY = touchEndY - this.state.touchStartY;

                // Prevent vertical scrolling interference
                if (Math.abs(deltaY) > Math.abs(deltaX)) return;

                if (Math.abs(deltaX) > this.config.touch.threshold) {
                    if (deltaX > 0) {
                        this.previousImage();
                    } else {
                        this.nextImage();
                    }
                }
            });
        },

        /**
         * Setup all galleries on the page
         */
        setupGalleries: function() {
            const self = this;
            
            $(this.config.selectors.gallery).each(function() {
                self.initializeGallery($(this));
            });
        },

        /**
         * Initialize a single gallery
         */
        initializeGallery: function($gallery) {
            const galleryId = $gallery.attr('id') || 'gallery-' + this.state.galleries.length;
            $gallery.attr('id', galleryId);

            const galleryData = {
                id: galleryId,
                $element: $gallery,
                items: [],
                settings: this.getGallerySettings($gallery)
            };

            // Process gallery items
            $gallery.find(this.config.selectors.galleryItem).each(function(index) {
                const $item = $(this);
                const $link = $item.find(self.config.selectors.galleryLink);
                const $image = $item.find(self.config.selectors.galleryImage);

                if ($link.length && $image.length) {
                    const itemData = {
                        index: index,
                        $element: $item,
                        $link: $link,
                        $image: $image,
                        src: $link.attr('href') || $image.attr('src'),
                        caption: $link.attr('title') || $image.attr('alt') || '',
                        alt: $image.attr('alt') || ''
                    };

                    galleryData.items.push(itemData);
                }
            });

            this.state.galleries.push(galleryData);
            this.enhanceGalleryAccessibility($gallery);
        },

        /**
         * Get gallery-specific settings
         */
        getGallerySettings: function($gallery) {
            return {
                autoplay: $gallery.data('autoplay') || false,
                loop: $gallery.data('loop') !== false,
                showCaption: $gallery.data('show-caption') !== false,
                showCounter: $gallery.data('show-counter') !== false,
                animation: $gallery.data('animation') || 'fade'
            };
        },

        /**
         * Open lightbox with specific gallery and image
         */
        openLightbox: function($gallery, index) {
            const galleryId = $gallery.attr('id');
            const gallery = this.state.galleries.find(g => g.id === galleryId);
            
            if (!gallery || !gallery.items[index]) return;

            this.state.currentGallery = gallery;
            this.state.currentIndex = index;
            this.state.isLightboxOpen = true;

            // Prevent body scroll
            this.cache.$body.addClass(this.config.classes.noScroll);

            // Show lightbox
            this.cache.$lightbox.addClass(this.config.classes.lightboxOpen);

            // Load and display image
            this.displayImage(index);

            // Update navigation
            this.updateNavigation();

            // Set focus for accessibility
            this.cache.$lightboxClose.focus();

            // Trigger custom event
            this.cache.$lightbox.trigger('recruitpro:lightbox:opened', [gallery, index]);
        },

        /**
         * Close lightbox
         */
        closeLightbox: function() {
            if (!this.state.isLightboxOpen) return;

            this.state.isLightboxOpen = false;
            this.cache.$body.removeClass(this.config.classes.noScroll);
            this.cache.$lightbox.removeClass(this.config.classes.lightboxOpen);

            // Clear current gallery reference
            setTimeout(() => {
                this.state.currentGallery = null;
                this.state.currentIndex = 0;
            }, this.config.animation.duration);

            // Trigger custom event
            this.cache.$lightbox.trigger('recruitpro:lightbox:closed');
        },

        /**
         * Display image at specified index
         */
        displayImage: function(index) {
            const gallery = this.state.currentGallery;
            if (!gallery || !gallery.items[index]) return;

            const item = gallery.items[index];
            this.state.currentIndex = index;

            // Show loading state
            this.cache.$lightbox.addClass(this.config.classes.lightboxLoading);

            // Preload image
            this.preloadImage(item.src).then(() => {
                // Update image
                this.cache.$lightboxImage.attr({
                    'src': item.src,
                    'alt': item.alt
                });

                // Update caption
                if (gallery.settings.showCaption && item.caption) {
                    this.cache.$lightboxCaption.html(item.caption).show();
                } else {
                    this.cache.$lightboxCaption.hide();
                }

                // Update counter
                if (gallery.settings.showCounter) {
                    this.cache.$lightboxCounter.text(`${index + 1} / ${gallery.items.length}`).show();
                } else {
                    this.cache.$lightboxCounter.hide();
                }

                // Remove loading state
                this.cache.$lightbox.removeClass(this.config.classes.lightboxLoading);

                // Preload adjacent images
                this.preloadAdjacentImages();

            }).catch(() => {
                this.onImageError();
            });
        },

        /**
         * Navigate to previous image
         */
        previousImage: function() {
            if (!this.state.currentGallery) return;

            const gallery = this.state.currentGallery;
            let newIndex = this.state.currentIndex - 1;

            if (newIndex < 0) {
                newIndex = gallery.settings.loop ? gallery.items.length - 1 : 0;
            }

            if (newIndex !== this.state.currentIndex) {
                this.displayImage(newIndex);
                this.updateNavigation();
            }
        },

        /**
         * Navigate to next image
         */
        nextImage: function() {
            if (!this.state.currentGallery) return;

            const gallery = this.state.currentGallery;
            let newIndex = this.state.currentIndex + 1;

            if (newIndex >= gallery.items.length) {
                newIndex = gallery.settings.loop ? 0 : gallery.items.length - 1;
            }

            if (newIndex !== this.state.currentIndex) {
                this.displayImage(newIndex);
                this.updateNavigation();
            }
        },

        /**
         * Update navigation buttons state
         */
        updateNavigation: function() {
            const gallery = this.state.currentGallery;
            if (!gallery) return;

            const isFirst = this.state.currentIndex === 0;
            const isLast = this.state.currentIndex === gallery.items.length - 1;

            // Show/hide navigation if only one image
            if (gallery.items.length <= 1) {
                this.cache.$lightboxNav.hide();
                return;
            } else {
                this.cache.$lightboxNav.show();
            }

            // Update previous button
            if (!gallery.settings.loop && isFirst) {
                this.cache.$lightboxPrev.prop('disabled', true).attr('aria-disabled', 'true');
            } else {
                this.cache.$lightboxPrev.prop('disabled', false).attr('aria-disabled', 'false');
            }

            // Update next button
            if (!gallery.settings.loop && isLast) {
                this.cache.$lightboxNext.prop('disabled', true).attr('aria-disabled', 'true');
            } else {
                this.cache.$lightboxNext.prop('disabled', false).attr('aria-disabled', 'false');
            }
        },

        /**
         * Handle keyboard navigation
         */
        handleKeyboard: function(e) {
            if (!this.state.isLightboxOpen) return;

            switch (e.keyCode) {
                case 27: // Escape
                    e.preventDefault();
                    this.closeLightbox();
                    break;
                case 37: // Left arrow
                    e.preventDefault();
                    this.previousImage();
                    break;
                case 39: // Right arrow
                    e.preventDefault();
                    this.nextImage();
                    break;
                case 36: // Home
                    e.preventDefault();
                    this.displayImage(0);
                    this.updateNavigation();
                    break;
                case 35: // End
                    e.preventDefault();
                    const lastIndex = this.state.currentGallery.items.length - 1;
                    this.displayImage(lastIndex);
                    this.updateNavigation();
                    break;
            }
        },

        /**
         * Preload image
         */
        preloadImage: function(src) {
            return new Promise((resolve, reject) => {
                if (this.state.preloadedImages[src]) {
                    resolve();
                    return;
                }

                const img = new Image();
                img.onload = () => {
                    this.state.preloadedImages[src] = true;
                    resolve();
                };
                img.onerror = reject;
                img.src = src;
            });
        },

        /**
         * Preload adjacent images for better UX
         */
        preloadAdjacentImages: function() {
            const gallery = this.state.currentGallery;
            if (!gallery) return;

            const currentIndex = this.state.currentIndex;
            const items = gallery.items;

            // Preload next image
            const nextIndex = (currentIndex + 1) % items.length;
            if (items[nextIndex]) {
                this.preloadImage(items[nextIndex].src);
            }

            // Preload previous image
            const prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
            if (items[prevIndex]) {
                this.preloadImage(items[prevIndex].src);
            }
        },

        /**
         * Handle image load success
         */
        onImageLoad: function() {
            this.cache.$lightbox.removeClass(this.config.classes.lightboxLoading);
            this.adjustLightboxSize();
        },

        /**
         * Handle image load error
         */
        onImageError: function() {
            this.cache.$lightbox.removeClass(this.config.classes.lightboxLoading);
            this.cache.$lightboxImage.attr('alt', 'Image failed to load');
            console.error('Gallery image failed to load');
        },

        /**
         * Adjust lightbox size for current viewport
         */
        adjustLightboxSize: function() {
            // This method can be extended for responsive adjustments
            const windowHeight = this.cache.$window.height();
            const windowWidth = this.cache.$window.width();
            
            // Adjust for mobile
            if (windowWidth < 768) {
                this.cache.$lightboxContent.addClass('mobile-view');
            } else {
                this.cache.$lightboxContent.removeClass('mobile-view');
            }
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add ARIA labels and roles
            $(this.config.selectors.gallery).each(function() {
                $(this).attr({
                    'role': 'region',
                    'aria-label': 'Image gallery'
                });
            });

            // Ensure proper focus management
            this.cache.$document.on('focus', this.config.selectors.lightbox, (e) => {
                if (!this.cache.$lightbox.find(e.target).length) {
                    this.cache.$lightboxClose.focus();
                }
            });
        },

        /**
         * Enhance gallery accessibility
         */
        enhanceGalleryAccessibility: function($gallery) {
            $gallery.find(this.config.selectors.galleryLink).each(function(index) {
                const $link = $(this);
                const $image = $link.find('img');
                const caption = $link.attr('title') || $image.attr('alt') || '';
                
                $link.attr({
                    'aria-label': `Open image ${index + 1}${caption ? ': ' + caption : ''} in lightbox`,
                    'role': 'button'
                });
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
            open: function(galleryId, index) {
                const $gallery = $('#' + galleryId);
                if ($gallery.length) {
                    ImageGallery.openLightbox($gallery, index || 0);
                }
            },

            close: function() {
                ImageGallery.closeLightbox();
            },

            next: function() {
                ImageGallery.nextImage();
            },

            previous: function() {
                ImageGallery.previousImage();
            },

            refresh: function() {
                ImageGallery.state.galleries = [];
                ImageGallery.setupGalleries();
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        ImageGallery.init();
    });

    // Expose public API
    window.RecruitPro.Gallery = ImageGallery.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        ImageGallery.setupGalleries();
    });

})(jQuery);