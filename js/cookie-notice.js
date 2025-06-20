/**
 * Cookie Notice & Consent Management - RecruitPro Theme
 * 
 * GDPR/CCPA compliant cookie consent system for recruitment websites
 * Professional cookie management for candidate and client data protection
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Cookie Notice Manager
     */
    const RecruitProCookieNotice = {
        
        // Configuration
        config: {
            // Cookie names
            cookieName: 'recruitpro_cookie_consent',
            settingsCookieName: 'recruitpro_cookie_settings',
            
            // Cookie expiration (days)
            cookieExpiry: 365,
            
            // Auto-show banner delay (milliseconds)
            showDelay: 1000,
            
            // Banner position
            bannerPosition: 'bottom', // 'top' or 'bottom'
            
            // Consent mode
            consentMode: 'opt-in', // 'opt-in' or 'opt-out'
            
            // Cookie categories
            categories: {
                essential: {
                    name: 'Essential',
                    description: 'Required for basic website functionality, job applications, and security.',
                    required: true,
                    enabled: true,
                    cookies: [
                        'PHPSESSID', 'wordpress_*', 'wp-settings-*', 'wp_*',
                        'recruitpro_session', 'csrf_token', 'application_form_*'
                    ]
                },
                functional: {
                    name: 'Functional',
                    description: 'Enhance your experience with features like job search preferences and form auto-save.',
                    required: false,
                    enabled: false,
                    cookies: [
                        'job_search_preferences', 'form_autosave_*', 'language_preference',
                        'accessibility_settings', 'theme_preferences'
                    ]
                },
                analytics: {
                    name: 'Analytics',
                    description: 'Help us improve our recruitment services by analyzing how visitors use our site.',
                    required: false,
                    enabled: false,
                    cookies: [
                        '_ga', '_ga_*', '_gid', '_gat', '_gtag_*',
                        'recruitpro_analytics', 'job_view_tracking'
                    ]
                },
                marketing: {
                    name: 'Marketing',
                    description: 'Show you relevant job opportunities and recruitment content based on your interests.',
                    required: false,
                    enabled: false,
                    cookies: [
                        '_fbp', '_fbc', 'linkedin_*', 'indeed_*',
                        'recruitment_ads', 'job_recommendations'
                    ]
                }
            },
            
            // Texts and translations
            texts: {
                en: {
                    bannerTitle: 'We value your privacy',
                    bannerText: 'We use cookies to enhance your job search experience, analyze site traffic, and show you relevant opportunities. You can customize your preferences or accept all cookies.',
                    acceptAll: 'Accept All',
                    rejectAll: 'Reject All',
                    customize: 'Customize Settings',
                    settingsTitle: 'Cookie Preferences',
                    settingsIntro: 'Choose which cookies you allow to improve your recruitment experience:',
                    save: 'Save Preferences',
                    close: 'Close',
                    privacyPolicy: 'Privacy Policy',
                    cookiePolicy: 'Cookie Policy',
                    learnMore: 'Learn More',
                    poweredBy: 'Powered by RecruitPro'
                }
            },
            
            // Third-party integrations to manage
            integrations: {
                googleAnalytics: {
                    category: 'analytics',
                    script: 'gtag',
                    enabled: false
                },
                facebookPixel: {
                    category: 'marketing',
                    script: 'fbq',
                    enabled: false
                },
                linkedInInsight: {
                    category: 'marketing',
                    script: '_linkedin_partner_id',
                    enabled: false
                },
                hotjar: {
                    category: 'analytics',
                    script: 'hj',
                    enabled: false
                }
            }
        },

        // State management
        state: {
            isInitialized: false,
            consentGiven: false,
            bannerVisible: false,
            settingsVisible: false,
            currentLanguage: 'en',
            userLocation: null,
            requiresConsent: true,
            settingsChanged: false
        },

        // Cache DOM elements
        cache: {
            $window: $(window),
            $document: $(document),
            $body: $('body'),
            $html: $('html'),
            $banner: null,
            $settings: null,
            $backdrop: null
        },

        /**
         * Initialize cookie notice system
         */
        init: function() {
            if (this.state.isInitialized) return;
            
            this.detectUserEnvironment();
            this.loadUserConsent();
            this.createBannerHTML();
            this.createSettingsHTML();
            this.bindEvents();
            this.setupAccessibility();
            this.checkThirdPartyIntegrations();
            
            // Show banner if consent needed
            if (this.state.requiresConsent && !this.state.consentGiven) {
                setTimeout(() => {
                    this.showBanner();
                }, this.config.showDelay);
            }
            
            this.state.isInitialized = true;
            this.triggerConsentEvents();
            
            console.log('RecruitPro Cookie Notice initialized');
        },

        /**
         * Detect user environment and privacy requirements
         */
        detectUserEnvironment: function() {
            // Detect language
            this.state.currentLanguage = document.documentElement.lang || 'en';
            
            // Detect if user likely needs consent (EU/EEA, California, etc.)
            this.detectPrivacyJurisdiction();
            
            // Check if consent already given
            const existingConsent = this.getCookie(this.config.cookieName);
            if (existingConsent) {
                this.state.consentGiven = true;
                this.loadConsentSettings();
            }
        },

        /**
         * Detect privacy jurisdiction requirements
         */
        detectPrivacyJurisdiction: function() {
            // This would typically use geolocation or server-side detection
            // For now, we'll assume consent is required for all users
            this.state.requiresConsent = true;
            
            // You could enhance this with:
            // - IP geolocation
            // - Server-side country detection
            // - User agent analysis
            // - Time zone detection
        },

        /**
         * Load user consent settings
         */
        loadUserConsent: function() {
            const consentData = this.getCookie(this.config.cookieName);
            const settingsData = this.getCookie(this.config.settingsCookieName);
            
            if (consentData && settingsData) {
                try {
                    const settings = JSON.parse(decodeURIComponent(settingsData));
                    
                    // Update category states
                    Object.keys(this.config.categories).forEach(categoryKey => {
                        if (settings.hasOwnProperty(categoryKey)) {
                            this.config.categories[categoryKey].enabled = settings[categoryKey];
                        }
                    });
                    
                    this.state.consentGiven = true;
                    this.applyConsentSettings();
                } catch (e) {
                    console.warn('Failed to parse cookie consent settings:', e);
                }
            }
        },

        /**
         * Create banner HTML
         */
        createBannerHTML: function() {
            const texts = this.config.texts[this.state.currentLanguage] || this.config.texts.en;
            const positionClass = this.config.bannerPosition === 'top' ? 'cookie-banner-top' : 'cookie-banner-bottom';
            
            const bannerHTML = `
                <div id="cookie-consent-banner" class="cookie-banner ${positionClass}" role="dialog" aria-labelledby="cookie-banner-title" aria-describedby="cookie-banner-text" style="display: none;">
                    <div class="cookie-banner-container">
                        <div class="cookie-banner-content">
                            <div class="cookie-banner-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <circle cx="12" cy="12" r="4"/>
                                    <circle cx="21.5" cy="10.5" r="1.5"/>
                                    <circle cx="16.5" cy="6.5" r="1.5"/>
                                    <circle cx="7.5" cy="3.5" r="1.5"/>
                                    <circle cx="2.5" cy="13.5" r="1.5"/>
                                    <circle cx="5.5" cy="21.5" r="1.5"/>
                                </svg>
                            </div>
                            <div class="cookie-banner-text">
                                <h3 id="cookie-banner-title">${texts.bannerTitle}</h3>
                                <p id="cookie-banner-text">${texts.bannerText}</p>
                                <div class="cookie-banner-links">
                                    <a href="/privacy-policy" class="cookie-link" target="_blank">${texts.privacyPolicy}</a>
                                    <a href="/cookie-policy" class="cookie-link" target="_blank">${texts.cookiePolicy}</a>
                                </div>
                            </div>
                        </div>
                        <div class="cookie-banner-actions">
                            <button type="button" class="cookie-btn cookie-btn-reject" data-action="reject">${texts.rejectAll}</button>
                            <button type="button" class="cookie-btn cookie-btn-customize" data-action="customize">${texts.customize}</button>
                            <button type="button" class="cookie-btn cookie-btn-accept" data-action="accept">${texts.acceptAll}</button>
                        </div>
                    </div>
                    <button type="button" class="cookie-banner-close" aria-label="Close cookie notice">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            this.cache.$body.append(bannerHTML);
            this.cache.$banner = $('#cookie-consent-banner');
        },

        /**
         * Create settings modal HTML
         */
        createSettingsHTML: function() {
            const texts = this.config.texts[this.state.currentLanguage] || this.config.texts.en;
            
            let categoriesHTML = '';
            Object.keys(this.config.categories).forEach(categoryKey => {
                const category = this.config.categories[categoryKey];
                const isRequired = category.required;
                const isChecked = category.enabled ? 'checked' : '';
                const isDisabled = isRequired ? 'disabled' : '';
                
                categoriesHTML += `
                    <div class="cookie-category" data-category="${categoryKey}">
                        <div class="cookie-category-header">
                            <div class="cookie-toggle">
                                <input type="checkbox" 
                                       id="cookie-${categoryKey}" 
                                       name="cookie-categories" 
                                       value="${categoryKey}" 
                                       ${isChecked} 
                                       ${isDisabled}
                                       aria-describedby="cookie-${categoryKey}-desc">
                                <label for="cookie-${categoryKey}" class="cookie-toggle-label">
                                    <span class="cookie-toggle-switch"></span>
                                </label>
                            </div>
                            <div class="cookie-category-info">
                                <h4 class="cookie-category-name">
                                    ${category.name}
                                    ${isRequired ? '<span class="cookie-required">(Required)</span>' : ''}
                                </h4>
                                <p id="cookie-${categoryKey}-desc" class="cookie-category-desc">${category.description}</p>
                            </div>
                        </div>
                        <div class="cookie-category-details">
                            <button type="button" class="cookie-details-toggle" aria-expanded="false" aria-controls="cookie-${categoryKey}-cookies">
                                View cookies <span class="cookie-arrow">▼</span>
                            </button>
                            <div id="cookie-${categoryKey}-cookies" class="cookie-list" style="display: none;">
                                <ul>
                                    ${category.cookies.map(cookie => `<li><code>${cookie}</code></li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            const settingsHTML = `
                <div id="cookie-settings-modal" class="cookie-settings-modal" role="dialog" aria-labelledby="cookie-settings-title" aria-hidden="true" style="display: none;">
                    <div class="cookie-settings-backdrop"></div>
                    <div class="cookie-settings-container">
                        <div class="cookie-settings-header">
                            <h2 id="cookie-settings-title">${texts.settingsTitle}</h2>
                            <button type="button" class="cookie-settings-close" aria-label="Close cookie settings">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="cookie-settings-body">
                            <p class="cookie-settings-intro">${texts.settingsIntro}</p>
                            <div class="cookie-categories">
                                ${categoriesHTML}
                            </div>
                            <div class="cookie-settings-info">
                                <h3>About Cookies</h3>
                                <p>Cookies help us provide you with the best recruitment experience. We use them to remember your job search preferences, analyze how you use our services, and show you relevant opportunities.</p>
                                <div class="cookie-policy-links">
                                    <a href="/privacy-policy" target="_blank" class="cookie-link">${texts.privacyPolicy}</a>
                                    <a href="/cookie-policy" target="_blank" class="cookie-link">${texts.cookiePolicy}</a>
                                </div>
                            </div>
                        </div>
                        <div class="cookie-settings-footer">
                            <button type="button" class="cookie-btn cookie-btn-secondary" data-action="reject-all">${texts.rejectAll}</button>
                            <div class="cookie-settings-actions">
                                <button type="button" class="cookie-btn cookie-btn-secondary" data-action="close">${texts.close}</button>
                                <button type="button" class="cookie-btn cookie-btn-primary" data-action="save">${texts.save}</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            this.cache.$body.append(settingsHTML);
            this.cache.$settings = $('#cookie-settings-modal');
            this.cache.$backdrop = $('.cookie-settings-backdrop');
        },

        /**
         * Bind all events
         */
        bindEvents: function() {
            this.bindBannerEvents();
            this.bindSettingsEvents();
            this.bindGlobalEvents();
        },

        /**
         * Bind banner events
         */
        bindBannerEvents: function() {
            const self = this;
            
            // Banner action buttons
            this.cache.$banner.on('click', '[data-action]', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                self.handleBannerAction(action);
            });
            
            // Close button
            this.cache.$banner.on('click', '.cookie-banner-close', function(e) {
                e.preventDefault();
                self.hideBanner();
            });
            
            // Link clicks (don't close banner)
            this.cache.$banner.on('click', '.cookie-link', function(e) {
                // Let links open normally
            });
        },

        /**
         * Bind settings modal events
         */
        bindSettingsEvents: function() {
            const self = this;
            
            // Settings action buttons
            this.cache.$settings.on('click', '[data-action]', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                self.handleSettingsAction(action);
            });
            
            // Close button
            this.cache.$settings.on('click', '.cookie-settings-close', function(e) {
                e.preventDefault();
                self.hideSettings();
            });
            
            // Backdrop click
            this.cache.$backdrop.on('click', function(e) {
                if (e.target === this) {
                    self.hideSettings();
                }
            });
            
            // Category toggles
            this.cache.$settings.on('change', 'input[name="cookie-categories"]', function() {
                const category = $(this).val();
                const enabled = $(this).is(':checked');
                self.config.categories[category].enabled = enabled;
                self.state.settingsChanged = true;
            });
            
            // Details toggles
            this.cache.$settings.on('click', '.cookie-details-toggle', function(e) {
                e.preventDefault();
                const $button = $(this);
                const $details = $button.siblings('.cookie-list');
                const isExpanded = $button.attr('aria-expanded') === 'true';
                
                $details.slideToggle(200);
                $button.attr('aria-expanded', !isExpanded);
                $button.find('.cookie-arrow').text(isExpanded ? '▼' : '▲');
            });
        },

        /**
         * Bind global events
         */
        bindGlobalEvents: function() {
            const self = this;
            
            // Keyboard events
            this.cache.$document.on('keydown', function(e) {
                if (e.keyCode === 27) { // Escape key
                    if (self.state.settingsVisible) {
                        self.hideSettings();
                    } else if (self.state.bannerVisible) {
                        self.hideBanner();
                    }
                }
            });
            
            // Focus trap for settings modal
            this.cache.$settings.on('keydown', function(e) {
                if (e.keyCode === 9) { // Tab key
                    self.trapFocus(e, this);
                }
            });
            
            // Settings trigger (for manual opening)
            this.cache.$document.on('click', '[data-cookie-settings]', function(e) {
                e.preventDefault();
                self.showSettings();
            });
        },

        /**
         * Handle banner actions
         */
        handleBannerAction: function(action) {
            switch (action) {
                case 'accept':
                    this.acceptAllCookies();
                    break;
                case 'reject':
                    this.rejectAllCookies();
                    break;
                case 'customize':
                    this.showSettings();
                    break;
            }
        },

        /**
         * Handle settings actions
         */
        handleSettingsAction: function(action) {
            switch (action) {
                case 'save':
                    this.saveConsentSettings();
                    break;
                case 'close':
                    this.hideSettings();
                    break;
                case 'reject-all':
                    this.rejectAllCookies();
                    this.hideSettings();
                    break;
            }
        },

        /**
         * Accept all cookies
         */
        acceptAllCookies: function() {
            // Enable all categories
            Object.keys(this.config.categories).forEach(categoryKey => {
                this.config.categories[categoryKey].enabled = true;
            });
            
            this.saveConsentSettings();
            this.hideBanner();
            this.announceToScreenReader('All cookies accepted');
        },

        /**
         * Reject all optional cookies
         */
        rejectAllCookies: function() {
            // Only enable essential cookies
            Object.keys(this.config.categories).forEach(categoryKey => {
                const category = this.config.categories[categoryKey];
                category.enabled = category.required;
            });
            
            this.saveConsentSettings();
            this.hideBanner();
            this.announceToScreenReader('Optional cookies rejected');
        },

        /**
         * Save consent settings
         */
        saveConsentSettings: function() {
            const settings = {};
            Object.keys(this.config.categories).forEach(categoryKey => {
                settings[categoryKey] = this.config.categories[categoryKey].enabled;
            });
            
            // Save consent timestamp
            const consentData = {
                timestamp: Date.now(),
                version: '1.0',
                accepted: true
            };
            
            // Set cookies
            this.setCookie(this.config.cookieName, JSON.stringify(consentData), this.config.cookieExpiry);
            this.setCookie(this.config.settingsCookieName, JSON.stringify(settings), this.config.cookieExpiry);
            
            // Update state
            this.state.consentGiven = true;
            this.state.settingsChanged = false;
            
            // Apply settings
            this.applyConsentSettings();
            
            // Trigger events
            this.triggerConsentEvents();
            
            this.announceToScreenReader('Cookie preferences saved');
        },

        /**
         * Apply consent settings to third-party integrations
         */
        applyConsentSettings: function() {
            Object.keys(this.config.integrations).forEach(integrationKey => {
                const integration = this.config.integrations[integrationKey];
                const category = this.config.categories[integration.category];
                
                if (category && category.enabled) {
                    this.enableIntegration(integrationKey);
                } else {
                    this.disableIntegration(integrationKey);
                }
            });
            
            // Remove non-essential cookies if disabled
            this.cleanupCookies();
        },

        /**
         * Enable third-party integration
         */
        enableIntegration: function(integrationKey) {
            const integration = this.config.integrations[integrationKey];
            
            switch (integrationKey) {
                case 'googleAnalytics':
                    this.enableGoogleAnalytics();
                    break;
                case 'facebookPixel':
                    this.enableFacebookPixel();
                    break;
                case 'linkedInInsight':
                    this.enableLinkedInInsight();
                    break;
                case 'hotjar':
                    this.enableHotjar();
                    break;
            }
            
            integration.enabled = true;
            
            // Trigger custom event
            this.cache.$document.trigger('recruitpro:integration-enabled', [integrationKey]);
        },

        /**
         * Disable third-party integration
         */
        disableIntegration: function(integrationKey) {
            const integration = this.config.integrations[integrationKey];
            
            switch (integrationKey) {
                case 'googleAnalytics':
                    this.disableGoogleAnalytics();
                    break;
                case 'facebookPixel':
                    this.disableFacebookPixel();
                    break;
                case 'linkedInInsight':
                    this.disableLinkedInInsight();
                    break;
                case 'hotjar':
                    this.disableHotjar();
                    break;
            }
            
            integration.enabled = false;
            
            // Trigger custom event
            this.cache.$document.trigger('recruitpro:integration-disabled', [integrationKey]);
        },

        /**
         * Google Analytics integration
         */
        enableGoogleAnalytics: function() {
            if (typeof gtag !== 'undefined') {
                gtag('consent', 'update', {
                    'analytics_storage': 'granted'
                });
            }
        },

        disableGoogleAnalytics: function() {
            if (typeof gtag !== 'undefined') {
                gtag('consent', 'update', {
                    'analytics_storage': 'denied'
                });
            }
        },

        /**
         * Facebook Pixel integration
         */
        enableFacebookPixel: function() {
            if (typeof fbq !== 'undefined') {
                fbq('consent', 'grant');
            }
        },

        disableFacebookPixel: function() {
            if (typeof fbq !== 'undefined') {
                fbq('consent', 'revoke');
            }
        },

        /**
         * LinkedIn Insight integration
         */
        enableLinkedInInsight: function() {
            // LinkedIn Insight Tag consent
            if (typeof _linkedin_partner_id !== 'undefined') {
                // Re-enable LinkedIn tracking
            }
        },

        disableLinkedInInsight: function() {
            // Disable LinkedIn tracking
        },

        /**
         * Hotjar integration
         */
        enableHotjar: function() {
            if (typeof hj !== 'undefined') {
                hj('consent', 'grant');
            }
        },

        disableHotjar: function() {
            if (typeof hj !== 'undefined') {
                hj('consent', 'revoke');
            }
        },

        /**
         * Clean up cookies based on consent
         */
        cleanupCookies: function() {
            Object.keys(this.config.categories).forEach(categoryKey => {
                const category = this.config.categories[categoryKey];
                
                if (!category.enabled && !category.required) {
                    // Remove cookies for disabled categories
                    category.cookies.forEach(cookieName => {
                        this.deleteCookie(cookieName);
                    });
                }
            });
        },

        /**
         * Show banner
         */
        showBanner: function() {
            if (this.state.bannerVisible) return;
            
            this.cache.$banner.fadeIn(300);
            this.cache.$body.addClass('cookie-banner-visible');
            this.state.bannerVisible = true;
            
            // Focus first button for accessibility
            setTimeout(() => {
                this.cache.$banner.find('.cookie-btn').first().focus();
            }, 350);
            
            // Trigger event
            this.cache.$document.trigger('recruitpro:cookie-banner-shown');
        },

        /**
         * Hide banner
         */
        hideBanner: function() {
            if (!this.state.bannerVisible) return;
            
            this.cache.$banner.fadeOut(300);
            this.cache.$body.removeClass('cookie-banner-visible');
            this.state.bannerVisible = false;
            
            // Trigger event
            this.cache.$document.trigger('recruitpro:cookie-banner-hidden');
        },

        /**
         * Show settings modal
         */
        showSettings: function() {
            if (this.state.settingsVisible) return;
            
            // Update checkboxes to match current state
            Object.keys(this.config.categories).forEach(categoryKey => {
                const category = this.config.categories[categoryKey];
                const $checkbox = this.cache.$settings.find(`#cookie-${categoryKey}`);
                $checkbox.prop('checked', category.enabled);
            });
            
            this.cache.$settings.fadeIn(300);
            this.cache.$body.addClass('cookie-settings-visible');
            this.state.settingsVisible = true;
            
            // Focus first interactive element
            setTimeout(() => {
                this.cache.$settings.find('input:not(:disabled)').first().focus();
            }, 350);
            
            // Trigger event
            this.cache.$document.trigger('recruitpro:cookie-settings-shown');
        },

        /**
         * Hide settings modal
         */
        hideSettings: function() {
            if (!this.state.settingsVisible) return;
            
            this.cache.$settings.fadeOut(300);
            this.cache.$body.removeClass('cookie-settings-visible');
            this.state.settingsVisible = false;
            
            // If settings were changed but not saved, revert
            if (this.state.settingsChanged) {
                this.loadConsentSettings();
                this.state.settingsChanged = false;
            }
            
            // Trigger event
            this.cache.$document.trigger('recruitpro:cookie-settings-hidden');
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add ARIA live region for announcements
            if (!$('#cookie-aria-live').length) {
                this.cache.$body.append('<div id="cookie-aria-live" aria-live="polite" aria-atomic="true" class="sr-only"></div>');
            }
            
            // Keyboard navigation
            this.setupKeyboardNavigation();
        },

        /**
         * Setup keyboard navigation
         */
        setupKeyboardNavigation: function() {
            // Focus management for banner
            this.cache.$banner.on('keydown', (e) => {
                if (e.keyCode === 9) { // Tab key
                    this.trapFocus(e, this.cache.$banner[0]);
                }
            });
        },

        /**
         * Trap focus within element
         */
        trapFocus: function(e, container) {
            const focusableElements = $(container).find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const firstElement = focusableElements.first();
            const lastElement = focusableElements.last();
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement[0]) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement[0]) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        },

        /**
         * Announce to screen readers
         */
        announceToScreenReader: function(message) {
            const $liveRegion = $('#cookie-aria-live');
            if ($liveRegion.length) {
                $liveRegion.text(message);
                setTimeout(() => {
                    $liveRegion.text('');
                }, 1000);
            }
        },

        /**
         * Check existing third-party integrations
         */
        checkThirdPartyIntegrations: function() {
            // Check which integrations are already loaded
            if (typeof gtag !== 'undefined') {
                this.config.integrations.googleAnalytics.loaded = true;
            }
            
            if (typeof fbq !== 'undefined') {
                this.config.integrations.facebookPixel.loaded = true;
            }
            
            if (typeof hj !== 'undefined') {
                this.config.integrations.hotjar.loaded = true;
            }
        },

        /**
         * Trigger consent events
         */
        triggerConsentEvents: function() {
            const consentData = {
                categories: {},
                timestamp: Date.now()
            };
            
            Object.keys(this.config.categories).forEach(categoryKey => {
                consentData.categories[categoryKey] = this.config.categories[categoryKey].enabled;
            });
            
            // Custom events
            this.cache.$document.trigger('recruitpro:cookie-consent-updated', [consentData]);
            
            // Standard events for third-party integrations
            if (window.dataLayer) {
                window.dataLayer.push({
                    event: 'cookie_consent_updated',
                    consent_categories: consentData.categories
                });
            }
        },

        /**
         * Cookie utility functions
         */
        setCookie: function(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${encodeURIComponent(value)};expires=${expires.toUTCString()};path=/;secure;samesite=strict`;
        },

        getCookie: function(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(c.substring(nameEQ.length, c.length));
                }
            }
            return null;
        },

        deleteCookie: function(name) {
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/`;
            
            // Also try with domain variations
            const domain = window.location.hostname;
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=${domain}`;
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=.${domain}`;
        },

        /**
         * Public API methods
         */
        showCookieBanner: function() {
            this.showBanner();
        },

        showCookieSettings: function() {
            this.showSettings();
        },

        getConsentStatus: function() {
            const status = {};
            Object.keys(this.config.categories).forEach(categoryKey => {
                status[categoryKey] = this.config.categories[categoryKey].enabled;
            });
            return status;
        },

        hasConsent: function(category) {
            return this.config.categories[category] && this.config.categories[category].enabled;
        },

        revokeConsent: function() {
            this.rejectAllCookies();
            this.showBanner();
        },

        updateSettings: function(newSettings) {
            Object.keys(newSettings).forEach(categoryKey => {
                if (this.config.categories[categoryKey] && !this.config.categories[categoryKey].required) {
                    this.config.categories[categoryKey].enabled = newSettings[categoryKey];
                }
            });
            
            this.saveConsentSettings();
        }
    };

    /**
     * Add CSS styles
     */
    const injectStyles = function() {
        const styles = `
            <style id="cookie-notice-styles">
                .cookie-banner {
                    position: fixed;
                    left: 0;
                    right: 0;
                    background: #ffffff;
                    border: 1px solid #e5e7eb;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                    z-index: 999999;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    font-size: 14px;
                    line-height: 1.5;
                }
                
                .cookie-banner-bottom {
                    bottom: 0;
                    border-bottom: none;
                    border-radius: 12px 12px 0 0;
                }
                
                .cookie-banner-top {
                    top: 0;
                    border-top: none;
                    border-radius: 0 0 12px 12px;
                }
                
                .cookie-banner-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                    display: flex;
                    align-items: center;
                    gap: 20px;
                }
                
                .cookie-banner-content {
                    flex: 1;
                    display: flex;
                    align-items: flex-start;
                    gap: 15px;
                }
                
                .cookie-banner-icon {
                    color: #2563eb;
                    flex-shrink: 0;
                    margin-top: 2px;
                }
                
                .cookie-banner-text h3 {
                    margin: 0 0 8px 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: #1f2937;
                }
                
                .cookie-banner-text p {
                    margin: 0 0 10px 0;
                    color: #6b7280;
                }
                
                .cookie-banner-links {
                    display: flex;
                    gap: 15px;
                }
                
                .cookie-link {
                    color: #2563eb;
                    text-decoration: underline;
                    font-size: 13px;
                }
                
                .cookie-link:hover {
                    color: #1d4ed8;
                }
                
                .cookie-banner-actions {
                    display: flex;
                    gap: 10px;
                    flex-shrink: 0;
                }
                
                .cookie-btn {
                    padding: 10px 16px;
                    border: none;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    white-space: nowrap;
                }
                
                .cookie-btn-accept {
                    background: #2563eb;
                    color: #ffffff;
                }
                
                .cookie-btn-accept:hover {
                    background: #1d4ed8;
                }
                
                .cookie-btn-customize {
                    background: #f3f4f6;
                    color: #374151;
                    border: 1px solid #d1d5db;
                }
                
                .cookie-btn-customize:hover {
                    background: #e5e7eb;
                }
                
                .cookie-btn-reject {
                    background: transparent;
                    color: #6b7280;
                    text-decoration: underline;
                }
                
                .cookie-btn-reject:hover {
                    color: #374151;
                }
                
                .cookie-banner-close {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: #9ca3af;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }
                
                .cookie-banner-close:hover {
                    background: #f3f4f6;
                    color: #374151;
                }
                
                /* Settings Modal */
                .cookie-settings-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1000000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
                
                .cookie-settings-backdrop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                }
                
                .cookie-settings-container {
                    position: relative;
                    background: #ffffff;
                    border-radius: 12px;
                    width: 90%;
                    max-width: 600px;
                    max-height: 90vh;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                }
                
                .cookie-settings-header {
                    padding: 24px;
                    border-bottom: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .cookie-settings-header h2 {
                    margin: 0;
                    font-size: 20px;
                    font-weight: 600;
                    color: #1f2937;
                }
                
                .cookie-settings-close {
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    color: #9ca3af;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }
                
                .cookie-settings-close:hover {
                    background: #f3f4f6;
                    color: #374151;
                }
                
                .cookie-settings-body {
                    padding: 24px;
                    max-height: 60vh;
                    overflow-y: auto;
                }
                
                .cookie-settings-intro {
                    margin: 0 0 24px 0;
                    color: #6b7280;
                }
                
                .cookie-category {
                    margin-bottom: 24px;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    padding: 16px;
                }
                
                .cookie-category-header {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                }
                
                .cookie-toggle {
                    position: relative;
                    flex-shrink: 0;
                }
                
                .cookie-toggle input {
                    position: absolute;
                    opacity: 0;
                    width: 0;
                    height: 0;
                }
                
                .cookie-toggle-label {
                    display: block;
                    width: 44px;
                    height: 24px;
                    background: #d1d5db;
                    border-radius: 12px;
                    cursor: pointer;
                    position: relative;
                    transition: background 0.3s ease;
                }
                
                .cookie-toggle-switch {
                    position: absolute;
                    top: 2px;
                    left: 2px;
                    width: 20px;
                    height: 20px;
                    background: #ffffff;
                    border-radius: 50%;
                    transition: transform 0.3s ease;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                }
                
                .cookie-toggle input:checked + .cookie-toggle-label {
                    background: #2563eb;
                }
                
                .cookie-toggle input:checked + .cookie-toggle-label .cookie-toggle-switch {
                    transform: translateX(20px);
                }
                
                .cookie-toggle input:disabled + .cookie-toggle-label {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
                
                .cookie-category-name {
                    margin: 0 0 4px 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: #1f2937;
                }
                
                .cookie-required {
                    font-size: 12px;
                    color: #059669;
                    font-weight: 500;
                }
                
                .cookie-category-desc {
                    margin: 0;
                    color: #6b7280;
                    font-size: 14px;
                }
                
                .cookie-details-toggle {
                    margin-top: 12px;
                    background: none;
                    border: none;
                    color: #2563eb;
                    font-size: 14px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                }
                
                .cookie-details-toggle:hover {
                    color: #1d4ed8;
                }
                
                .cookie-arrow {
                    font-size: 12px;
                    transition: transform 0.2s ease;
                }
                
                .cookie-list {
                    margin-top: 8px;
                    padding-top: 8px;
                    border-top: 1px solid #f3f4f6;
                }
                
                .cookie-list ul {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                }
                
                .cookie-list code {
                    background: #f3f4f6;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 12px;
                    color: #374151;
                }
                
                .cookie-settings-footer {
                    padding: 24px;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .cookie-settings-actions {
                    display: flex;
                    gap: 10px;
                }
                
                .cookie-btn-primary {
                    background: #2563eb;
                    color: #ffffff;
                }
                
                .cookie-btn-primary:hover {
                    background: #1d4ed8;
                }
                
                .cookie-btn-secondary {
                    background: #f3f4f6;
                    color: #374151;
                    border: 1px solid #d1d5db;
                }
                
                .cookie-btn-secondary:hover {
                    background: #e5e7eb;
                }
                
                /* Responsive */
                @media (max-width: 768px) {
                    .cookie-banner-container {
                        flex-direction: column;
                        align-items: stretch;
                        padding: 16px;
                    }
                    
                    .cookie-banner-actions {
                        justify-content: space-between;
                        margin-top: 16px;
                    }
                    
                    .cookie-btn {
                        flex: 1;
                        text-align: center;
                    }
                    
                    .cookie-settings-container {
                        width: 95%;
                        margin: 20px;
                    }
                    
                    .cookie-settings-header,
                    .cookie-settings-body,
                    .cookie-settings-footer {
                        padding: 16px;
                    }
                    
                    .cookie-settings-footer {
                        flex-direction: column;
                        gap: 12px;
                    }
                    
                    .cookie-settings-actions {
                        width: 100%;
                        justify-content: space-between;
                    }
                    
                    .cookie-btn {
                        min-width: auto;
                    }
                }
                
                /* Accessibility */
                .sr-only {
                    position: absolute;
                    width: 1px;
                    height: 1px;
                    padding: 0;
                    margin: -1px;
                    overflow: hidden;
                    clip: rect(0, 0, 0, 0);
                    white-space: nowrap;
                    border: 0;
                }
                
                /* High contrast mode */
                @media (prefers-contrast: high) {
                    .cookie-banner {
                        border-width: 2px;
                    }
                    
                    .cookie-btn {
                        border-width: 2px;
                        border-style: solid;
                    }
                    
                    .cookie-btn-accept {
                        border-color: #2563eb;
                    }
                    
                    .cookie-btn-customize {
                        border-color: #374151;
                    }
                }
                
                /* Reduced motion */
                @media (prefers-reduced-motion: reduce) {
                    .cookie-banner,
                    .cookie-settings-modal,
                    .cookie-btn,
                    .cookie-toggle-label,
                    .cookie-toggle-switch {
                        transition: none !important;
                    }
                }
                
                /* Print styles */
                @media print {
                    .cookie-banner,
                    .cookie-settings-modal {
                        display: none !important;
                    }
                }
            </style>
        `;
        
        if (!$('#cookie-notice-styles').length) {
            $('head').append(styles);
        }
    };

    /**
     * Expose to global scope
     */
    window.RecruitProCookieNotice = RecruitProCookieNotice;

    /**
     * Auto-initialize when DOM is ready
     */
    $(document).ready(function() {
        injectStyles();
        
        // Initialize after a short delay to ensure other scripts are loaded
        setTimeout(() => {
            RecruitProCookieNotice.init();
        }, 500);
    });

    /**
     * Integration with main theme
     */
    if (window.RecruitPro) {
        window.RecruitPro.cookieNotice = RecruitProCookieNotice;
    }

})(jQuery);

/**
 * Cookie Notice Features Summary:
 * 
 * ✅ GDPR/CCPA Compliance
 * ✅ Granular Cookie Categories
 * ✅ Professional Banner Design
 * ✅ Settings Modal with Toggles
 * ✅ Third-Party Integration Management
 * ✅ Accessibility Support (WCAG 2.1)
 * ✅ Keyboard Navigation
 * ✅ Screen Reader Support
 * ✅ Mobile Responsive Design
 * ✅ Consent Storage & Management
 * ✅ Cookie Cleanup System
 * ✅ Multi-Language Ready
 * ✅ Privacy Policy Integration
 * ✅ Recruitment-Specific Categories
 * ✅ Performance Optimized
 */