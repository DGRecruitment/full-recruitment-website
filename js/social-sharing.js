/**
 * RecruitPro Theme - Social Sharing Module
 * 
 * Handles frontend social sharing functionality for job listings, blog posts,
 * and company pages. This is presentation layer only - no posting/management.
 * 
 * @package RecruitPro
 * @subpackage Assets/JS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Social Sharing Module
    const RecruitProSocialSharing = {
        
        /**
         * Initialize social sharing functionality
         */
        init: function() {
            this.bindEvents();
            this.setupShareButtons();
            this.initSocialMetrics();
            this.loadShareCounts();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Share button click handlers
            $(document).on('click', '.rp-share-btn', this.handleShareClick);
            $(document).on('click', '.rp-copy-link', this.copyToClipboard);
            $(document).on('click', '.rp-share-toggle', this.toggleShareMenu);
            $(document).on('click', '.rp-share-print', this.printPage);
            $(document).on('click', '.rp-share-email', this.emailShare);
            
            // Close share menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.rp-share-container').length) {
                    $('.rp-share-menu').removeClass('active');
                }
            });

            // Mobile share menu
            $(document).on('click', '.rp-mobile-share', this.handleMobileShare);
            
            // Track share events for analytics
            $(document).on('click', '.rp-share-btn', this.trackShareEvent);
        },

        /**
         * Setup share buttons based on content type
         */
        setupShareButtons: function() {
            const self = this;
            
            $('.rp-social-share').each(function() {
                const $container = $(this);
                const contentType = $container.data('content-type') || 'page';
                const contentId = $container.data('content-id') || '';
                const pageUrl = $container.data('url') || window.location.href;
                const pageTitle = $container.data('title') || document.title;
                const pageDescription = $container.data('description') || '';
                const pageImage = $container.data('image') || '';
                
                // Generate share URLs
                const shareUrls = self.generateShareUrls(pageUrl, pageTitle, pageDescription, pageImage);
                
                // Update share button URLs
                $container.find('.rp-share-facebook').attr('href', shareUrls.facebook);
                $container.find('.rp-share-twitter').attr('href', shareUrls.twitter);
                $container.find('.rp-share-linkedin').attr('href', shareUrls.linkedin);
                $container.find('.rp-share-whatsapp').attr('href', shareUrls.whatsapp);
                $container.find('.rp-share-telegram').attr('href', shareUrls.telegram);
                $container.find('.rp-share-reddit').attr('href', shareUrls.reddit);
                $container.find('.rp-share-pinterest').attr('href', shareUrls.pinterest);
                
                // Set copy link data
                $container.find('.rp-copy-link').data('url', pageUrl);
            });
        },

        /**
         * Generate social media share URLs
         */
        generateShareUrls: function(url, title, description, image) {
            const encodedUrl = encodeURIComponent(url);
            const encodedTitle = encodeURIComponent(title);
            const encodedDescription = encodeURIComponent(description);
            const encodedImage = encodeURIComponent(image);
            
            return {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
                twitter: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
                whatsapp: `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`,
                telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`,
                reddit: `https://reddit.com/submit?url=${encodedUrl}&title=${encodedTitle}`,
                pinterest: `https://pinterest.com/pin/create/button/?url=${encodedUrl}&description=${encodedTitle}${image ? '&media=' + encodedImage : ''}`,
                email: `mailto:?subject=${encodedTitle}&body=${encodedDescription}%0A%0A${encodedUrl}`,
                print: 'javascript:window.print();'
            };
        },

        /**
         * Handle share button clicks
         */
        handleShareClick: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const shareUrl = $btn.attr('href');
            const platform = $btn.data('platform');
            
            if (shareUrl && shareUrl !== '#' && !shareUrl.includes('javascript:')) {
                // Open share popup
                RecruitProSocialSharing.openSharePopup(shareUrl, platform);
                
                // Add visual feedback
                $btn.addClass('shared');
                setTimeout(() => $btn.removeClass('shared'), 1000);
            }
        },

        /**
         * Open social share popup window
         */
        openSharePopup: function(url, platform) {
            const popupFeatures = this.getPopupFeatures(platform);
            
            const popup = window.open(
                url,
                `share_${platform}`,
                popupFeatures
            );
            
            if (popup && popup.focus) {
                popup.focus();
            }
            
            return popup;
        },

        /**
         * Get popup window features based on platform
         */
        getPopupFeatures: function(platform) {
            const features = {
                facebook: 'width=626,height=436',
                twitter: 'width=550,height=420',
                linkedin: 'width=550,height=420',
                pinterest: 'width=750,height=320',
                reddit: 'width=550,height=420',
                default: 'width=600,height=400'
            };
            
            const baseFeatures = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes';
            const sizeFeatures = features[platform] || features.default;
            
            return `${baseFeatures},${sizeFeatures}`;
        },

        /**
         * Copy link to clipboard
         */
        copyToClipboard: function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const url = $btn.data('url') || window.location.href;
            
            // Modern clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    RecruitProSocialSharing.showCopyFeedback($btn, 'Link copied!');
                }).catch(() => {
                    RecruitProSocialSharing.fallbackCopyToClipboard(url, $btn);
                });
            } else {
                // Fallback for older browsers
                RecruitProSocialSharing.fallbackCopyToClipboard(url, $btn);
            }
        },

        /**
         * Fallback copy to clipboard method
         */
        fallbackCopyToClipboard: function(text, $btn) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                const message = successful ? 'Link copied!' : 'Copy failed';
                this.showCopyFeedback($btn, message);
            } catch (err) {
                this.showCopyFeedback($btn, 'Copy not supported');
            }
            
            document.body.removeChild(textArea);
        },

        /**
         * Show copy feedback to user
         */
        showCopyFeedback: function($btn, message) {
            const originalText = $btn.text();
            const originalIcon = $btn.find('i').attr('class');
            
            $btn.addClass('copied');
            $btn.find('i').attr('class', 'fas fa-check');
            $btn.find('.share-text').text(message);
            
            setTimeout(() => {
                $btn.removeClass('copied');
                $btn.find('i').attr('class', originalIcon);
                $btn.find('.share-text').text(originalText);
            }, 2000);
        },

        /**
         * Toggle share menu visibility
         */
        toggleShareMenu: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $menu = $(this).siblings('.rp-share-menu');
            const $allMenus = $('.rp-share-menu');
            
            // Close other open menus
            $allMenus.not($menu).removeClass('active');
            
            // Toggle current menu
            $menu.toggleClass('active');
        },

        /**
         * Print page functionality
         */
        printPage: function(e) {
            e.preventDefault();
            
            // Add print class to body for print-specific styles
            $('body').addClass('printing');
            
            setTimeout(() => {
                window.print();
                $('body').removeClass('printing');
            }, 100);
        },

        /**
         * Email share functionality
         */
        emailShare: function(e) {
            e.preventDefault();
            
            const $container = $(this).closest('.rp-social-share');
            const url = $container.data('url') || window.location.href;
            const title = $container.data('title') || document.title;
            const description = $container.data('description') || '';
            
            const mailtoUrl = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(description + '\n\n' + url)}`;
            window.location.href = mailtoUrl;
        },

        /**
         * Handle mobile native sharing
         */
        handleMobileShare: function(e) {
            e.preventDefault();
            
            const $container = $(this).closest('.rp-social-share');
            const url = $container.data('url') || window.location.href;
            const title = $container.data('title') || document.title;
            const text = $container.data('description') || '';
            
            // Check if Web Share API is supported
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: url
                }).catch(err => {
                    console.log('Error sharing:', err);
                });
            } else {
                // Fallback to share menu
                $(this).siblings('.rp-share-menu').addClass('active');
            }
        },

        /**
         * Initialize social metrics display
         */
        initSocialMetrics: function() {
            // Only initialize if metrics are enabled
            if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.show_share_counts) {
                this.loadShareCounts();
            }
        },

        /**
         * Load share counts from API
         */
        loadShareCounts: function() {
            $('.rp-social-share').each(function() {
                const $container = $(this);
                const url = $container.data('url') || window.location.href;
                const showCounts = $container.data('show-counts');
                
                if (showCounts) {
                    RecruitProSocialSharing.fetchShareCounts(url, $container);
                }
            });
        },

        /**
         * Fetch share counts from various APIs
         */
        fetchShareCounts: function(url, $container) {
            // Facebook (requires App ID - optional)
            if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.facebook_app_id) {
                $.get(`https://graph.facebook.com/?id=${encodeURIComponent(url)}&access_token=${recruitpro_theme.facebook_app_id}`)
                    .done(function(data) {
                        if (data.share && data.share.share_count) {
                            $container.find('.rp-share-facebook .share-count').text(
                                RecruitProSocialSharing.formatCount(data.share.share_count)
                            );
                        }
                    });
            }
            
            // LinkedIn (no public API for share counts)
            // Twitter (no longer provides public share counts)
            // Pinterest (no public API for share counts)
            
            // Use a combined share count service if available
            this.fetchCombinedShareCounts(url, $container);
        },

        /**
         * Fetch combined share counts from a service
         */
        fetchCombinedShareCounts: function(url, $container) {
            // This would integrate with a share count service
            // For now, we'll show a placeholder or use local storage
            const shareData = this.getLocalShareData(url);
            
            if (shareData) {
                Object.keys(shareData).forEach(platform => {
                    const count = shareData[platform];
                    if (count > 0) {
                        $container.find(`.rp-share-${platform} .share-count`).text(
                            this.formatCount(count)
                        );
                    }
                });
            }
        },

        /**
         * Get local share data from storage
         */
        getLocalShareData: function(url) {
            try {
                const data = localStorage.getItem(`rp_shares_${btoa(url)}`);
                return data ? JSON.parse(data) : null;
            } catch (e) {
                return null;
            }
        },

        /**
         * Save local share data
         */
        saveLocalShareData: function(url, platform) {
            try {
                const key = `rp_shares_${btoa(url)}`;
                const data = this.getLocalShareData(url) || {};
                data[platform] = (data[platform] || 0) + 1;
                localStorage.setItem(key, JSON.stringify(data));
            } catch (e) {
                // Storage not available
            }
        },

        /**
         * Format share count numbers
         */
        formatCount: function(count) {
            if (count < 1000) return count.toString();
            if (count < 1000000) return (count / 1000).toFixed(1) + 'K';
            return (count / 1000000).toFixed(1) + 'M';
        },

        /**
         * Track share events for analytics
         */
        trackShareEvent: function() {
            const platform = $(this).data('platform');
            const contentType = $(this).closest('.rp-social-share').data('content-type');
            const contentId = $(this).closest('.rp-social-share').data('content-id');
            const url = $(this).closest('.rp-social-share').data('url') || window.location.href;
            
            // Save to local storage for tracking
            RecruitProSocialSharing.saveLocalShareData(url, platform);
            
            // Send to Google Analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'share', {
                    'event_category': 'Social',
                    'event_label': platform,
                    'custom_map': {
                        'content_type': contentType,
                        'content_id': contentId
                    }
                });
            }
            
            // Send to WordPress analytics if available
            if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.ajax_url) {
                $.post(recruitpro_theme.ajax_url, {
                    action: 'recruitpro_track_share',
                    platform: platform,
                    content_type: contentType,
                    content_id: contentId,
                    url: url,
                    nonce: recruitpro_theme.nonce
                });
            }
        },

        /**
         * Responsive behavior for share buttons
         */
        handleResponsive: function() {
            const $shareContainers = $('.rp-social-share');
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            
            $shareContainers.each(function() {
                const $container = $(this);
                
                if (isMobile) {
                    // Show mobile-optimized version
                    $container.addClass('mobile-view');
                    
                    // Show only primary platforms
                    $container.find('.rp-share-btn').hide();
                    $container.find('.rp-share-facebook, .rp-share-twitter, .rp-share-whatsapp, .rp-copy-link').show();
                } else {
                    // Show desktop version
                    $container.removeClass('mobile-view');
                    $container.find('.rp-share-btn').show();
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RecruitProSocialSharing.init();
        
        // Handle responsive changes
        $(window).on('resize', RecruitProSocialSharing.handleResponsive);
        RecruitProSocialSharing.handleResponsive();
    });

    // Expose to global scope for external use
    window.RecruitProSocialSharing = RecruitProSocialSharing;

})(jQuery);

/**
 * Social sharing utility functions
 */
window.RecruitProSocialUtils = {
    
    /**
     * Generate social share HTML
     */
    generateShareButtons: function(options) {
        const defaults = {
            url: window.location.href,
            title: document.title,
            description: '',
            image: '',
            platforms: ['facebook', 'twitter', 'linkedin', 'whatsapp'],
            showCounts: false,
            showLabels: true,
            size: 'medium', // small, medium, large
            style: 'buttons' // buttons, icons, minimal
        };
        
        const settings = Object.assign(defaults, options);
        
        const platformData = {
            facebook: { icon: 'fab fa-facebook-f', label: 'Facebook', color: '#1877f2' },
            twitter: { icon: 'fab fa-twitter', label: 'Twitter', color: '#1da1f2' },
            linkedin: { icon: 'fab fa-linkedin-in', label: 'LinkedIn', color: '#0077b5' },
            whatsapp: { icon: 'fab fa-whatsapp', label: 'WhatsApp', color: '#25d366' },
            telegram: { icon: 'fab fa-telegram', label: 'Telegram', color: '#0088cc' },
            reddit: { icon: 'fab fa-reddit', label: 'Reddit', color: '#ff4500' },
            pinterest: { icon: 'fab fa-pinterest', label: 'Pinterest', color: '#bd081c' }
        };
        
        let html = `<div class="rp-social-share" data-url="${settings.url}" data-title="${settings.title}" data-description="${settings.description}" data-image="${settings.image}" data-show-counts="${settings.showCounts}">`;
        
        settings.platforms.forEach(platform => {
            if (platformData[platform]) {
                const data = platformData[platform];
                html += `
                    <a href="#" class="rp-share-btn rp-share-${platform}" data-platform="${platform}" style="--platform-color: ${data.color}">
                        <i class="${data.icon}"></i>
                        ${settings.showLabels ? `<span class="share-text">${data.label}</span>` : ''}
                        ${settings.showCounts ? '<span class="share-count"></span>' : ''}
                    </a>
                `;
            }
        });
        
        // Add copy link button
        html += `
            <a href="#" class="rp-copy-link" data-url="${settings.url}">
                <i class="fas fa-link"></i>
                ${settings.showLabels ? '<span class="share-text">Copy Link</span>' : ''}
            </a>
        `;
        
        html += '</div>';
        
        return html;
    }
};