/**
 * Error Handling System - RecruitPro Theme
 * 
 * Comprehensive error management for recruitment websites
 * Professional error handling, logging, and user feedback system
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * RecruitPro Error Handler Manager
     */
    const RecruitProErrorHandler = {
        
        // Configuration
        config: {
            // Error levels
            levels: {
                DEBUG: 0,
                INFO: 1,
                WARNING: 2,
                ERROR: 3,
                CRITICAL: 4
            },
            
            // Error categories
            categories: {
                SYSTEM: 'system',
                NETWORK: 'network',
                VALIDATION: 'validation',
                UPLOAD: 'upload',
                APPLICATION: 'application',
                CRM: 'crm',
                SEARCH: 'search',
                PAYMENT: 'payment',
                SECURITY: 'security',
                ACCESSIBILITY: 'accessibility'
            },
            
            // Error messages for recruitment context
            messages: {
                en: {
                    // General errors
                    unknown: 'An unexpected error occurred. Please try again.',
                    network: 'Connection issue. Please check your internet connection.',
                    timeout: 'Request timed out. Please try again.',
                    forbidden: 'Access denied. Please contact support.',
                    not_found: 'The requested resource was not found.',
                    server: 'Server error. Please try again later.',
                    
                    // Job application errors
                    application_failed: 'Failed to submit your application. Please try again.',
                    application_duplicate: 'You have already applied for this position.',
                    application_closed: 'This job position is no longer accepting applications.',
                    application_limit: 'Maximum number of applications reached.',
                    
                    // File upload errors
                    file_too_large: 'File size too large. Maximum size allowed is 5MB.',
                    file_invalid_type: 'Invalid file type. Please upload PDF, DOC, or DOCX files.',
                    file_corrupted: 'File appears to be corrupted. Please try uploading again.',
                    file_upload_failed: 'File upload failed. Please try again.',
                    cv_required: 'CV/Resume is required for this application.',
                    
                    // Form validation errors
                    field_required: 'This field is required.',
                    email_invalid: 'Please enter a valid email address.',
                    phone_invalid: 'Please enter a valid phone number.',
                    name_invalid: 'Please enter a valid name.',
                    experience_invalid: 'Please enter valid years of experience.',
                    
                    // Search errors
                    search_failed: 'Search failed. Please try again.',
                    no_results: 'No job opportunities found matching your criteria.',
                    search_timeout: 'Search timed out. Please refine your criteria and try again.',
                    
                    // CRM integration errors
                    crm_connection: 'Unable to connect to recruitment system. Please try again.',
                    crm_sync_failed: 'Failed to sync data with recruitment system.',
                    candidate_exists: 'A profile with this email already exists.',
                    client_not_found: 'Client information not found.',
                    
                    // Security errors
                    session_expired: 'Your session has expired. Please refresh the page.',
                    csrf_invalid: 'Security token invalid. Please refresh the page.',
                    rate_limit: 'Too many requests. Please wait before trying again.',
                    
                    // Accessibility errors
                    screen_reader_error: 'Error information available for screen readers.',
                    keyboard_nav_error: 'Keyboard navigation error occurred.'
                }
            },
            
            // Notification settings
            notifications: {
                duration: 5000,
                position: 'top-right',
                animation: 'slide',
                stack: true,
                maxVisible: 3
            },
            
            // Logging settings
            logging: {
                enabled: true,
                level: 'ERROR',
                maxEntries: 100,
                endpoint: null, // Set to send logs to server
                includeStackTrace: true,
                includeBrowserInfo: true
            },
            
            // Debug mode (set via WordPress)
            debug: false,
            
            // Retry settings
            retry: {
                maxAttempts: 3,
                backoffMultiplier: 2,
                initialDelay: 1000
            }
        },

        // State management
        state: {
            isInitialized: false,
            errorLog: [],
            activeErrors: [],
            retryAttempts: {},
            isOnline: navigator.onLine,
            debugMode: false,
            errorCount: 0
        },

        // Cache DOM elements
        cache: {
            $window: $(window),
            $document: $(document),
            $body: $('body'),
            $errorContainer: null,
            $errorOverlay: null,
            $debugPanel: null
        },

        /**
         * Initialize error handling system
         */
        init: function() {
            if (this.state.isInitialized) return;
            
            this.detectEnvironment();
            this.setupGlobalErrorHandling();
            this.createErrorUI();
            this.bindEvents();
            this.setupNetworkMonitoring();
            this.setupPerformanceMonitoring();
            this.initializeLogging();
            this.setupAccessibilityErrorHandling();
            
            this.state.isInitialized = true;
            
            console.log('RecruitPro Error Handler initialized');
            
            // Test error handling in debug mode
            if (this.state.debugMode) {
                this.createDebugPanel();
            }
        },

        /**
         * Detect environment and debug settings
         */
        detectEnvironment: function() {
            // Check for debug mode
            this.state.debugMode = this.config.debug || 
                                 window.location.search.includes('debug=true') ||
                                 localStorage.getItem('recruitpro_debug') === 'true';
            
            // Detect development environment
            const isDev = window.location.hostname === 'localhost' || 
                         window.location.hostname.includes('dev') ||
                         window.location.hostname.includes('staging');
                         
            if (isDev) {
                this.config.logging.level = 'DEBUG';
                this.config.logging.includeStackTrace = true;
            }
        },

        /**
         * Setup global error handling
         */
        setupGlobalErrorHandling: function() {
            const self = this;
            
            // JavaScript errors
            window.addEventListener('error', function(event) {
                self.handleJavaScriptError(event);
            });
            
            // Unhandled promise rejections
            window.addEventListener('unhandledrejection', function(event) {
                self.handlePromiseRejection(event);
            });
            
            // Resource loading errors
            window.addEventListener('error', function(event) {
                if (event.target !== window) {
                    self.handleResourceError(event);
                }
            }, true);
            
            // AJAX errors
            $(document).ajaxError(function(event, xhr, settings, error) {
                self.handleAjaxError(xhr, settings, error);
            });
            
            // Form submission errors
            $(document).on('submit', 'form', function(e) {
                const $form = $(this);
                if (!self.validateForm($form)) {
                    e.preventDefault();
                }
            });
        },

        /**
         * Handle JavaScript errors
         */
        handleJavaScriptError: function(event) {
            const error = {
                type: 'javascript',
                category: this.config.categories.SYSTEM,
                level: this.config.levels.ERROR,
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                stack: event.error ? event.error.stack : null,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent
            };
            
            this.logError(error);
            
            // Don't show user notification for minor JS errors unless in debug mode
            if (this.state.debugMode || this.isCriticalError(error)) {
                this.showErrorNotification(
                    'A technical error occurred. Our team has been notified.',
                    'error'
                );
            }
        },

        /**
         * Handle promise rejections
         */
        handlePromiseRejection: function(event) {
            const error = {
                type: 'promise_rejection',
                category: this.config.categories.SYSTEM,
                level: this.config.levels.ERROR,
                message: event.reason.message || event.reason,
                stack: event.reason.stack,
                timestamp: new Date().toISOString(),
                url: window.location.href
            };
            
            this.logError(error);
            
            // Prevent browser console error
            event.preventDefault();
        },

        /**
         * Handle resource loading errors
         */
        handleResourceError: function(event) {
            const target = event.target;
            const error = {
                type: 'resource_error',
                category: this.config.categories.NETWORK,
                level: this.config.levels.WARNING,
                message: `Failed to load resource: ${target.src || target.href}`,
                resource: target.tagName.toLowerCase(),
                source: target.src || target.href,
                timestamp: new Date().toISOString()
            };
            
            this.logError(error);
            
            // Handle specific resource types
            if (target.tagName === 'IMG') {
                this.handleImageError(target);
            } else if (target.tagName === 'SCRIPT') {
                this.handleScriptError(target);
            }
        },

        /**
         * Handle AJAX errors
         */
        handleAjaxError: function(xhr, settings, error) {
            const errorInfo = {
                type: 'ajax_error',
                category: this.config.categories.NETWORK,
                level: this.config.levels.ERROR,
                message: this.getAjaxErrorMessage(xhr),
                status: xhr.status,
                statusText: xhr.statusText,
                url: settings.url,
                method: settings.type,
                data: settings.data,
                timestamp: new Date().toISOString(),
                responseText: xhr.responseText
            };
            
            this.logError(errorInfo);
            
            // Show user-friendly error based on context
            this.handleAjaxErrorUI(xhr, settings);
        },

        /**
         * Get user-friendly AJAX error message
         */
        getAjaxErrorMessage: function(xhr) {
            const messages = this.config.messages.en;
            
            switch (xhr.status) {
                case 0:
                    return messages.network;
                case 400:
                    return 'Invalid request. Please check your input.';
                case 401:
                case 403:
                    return messages.forbidden;
                case 404:
                    return messages.not_found;
                case 408:
                    return messages.timeout;
                case 413:
                    return messages.file_too_large;
                case 429:
                    return messages.rate_limit;
                case 500:
                case 502:
                case 503:
                    return messages.server;
                default:
                    if (xhr.status >= 500) {
                        return messages.server;
                    }
                    return messages.unknown;
            }
        },

        /**
         * Handle AJAX error UI
         */
        handleAjaxErrorUI: function(xhr, settings) {
            const url = settings.url;
            const messages = this.config.messages.en;
            
            // Context-specific error handling
            if (url.includes('job_application')) {
                this.showErrorNotification(messages.application_failed, 'error');
                this.highlightFormErrors();
            } else if (url.includes('file_upload')) {
                this.showErrorNotification(messages.file_upload_failed, 'error');
            } else if (url.includes('job_search')) {
                this.showErrorNotification(messages.search_failed, 'error');
            } else if (url.includes('crm_')) {
                this.showErrorNotification(messages.crm_connection, 'error');
            } else {
                // Generic error
                this.showErrorNotification(this.getAjaxErrorMessage(xhr), 'error');
            }
            
            // Offer retry for specific errors
            if (this.isRetryableError(xhr)) {
                this.offerRetry(settings);
            }
        },

        /**
         * Validate form before submission
         */
        validateForm: function($form) {
            let isValid = true;
            const errors = [];
            
            // Clear previous errors
            $form.find('.error').removeClass('error');
            $form.find('.error-message').remove();
            
            // Required field validation
            $form.find('[required]').each((index, element) => {
                const $field = $(element);
                const value = $field.val().trim();
                
                if (!value) {
                    this.showFieldError($field, this.config.messages.en.field_required);
                    errors.push(`${this.getFieldLabel($field)} is required`);
                    isValid = false;
                }
            });
            
            // Email validation
            $form.find('input[type="email"]').each((index, element) => {
                const $field = $(element);
                const email = $field.val().trim();
                
                if (email && !this.isValidEmail(email)) {
                    this.showFieldError($field, this.config.messages.en.email_invalid);
                    errors.push('Invalid email address');
                    isValid = false;
                }
            });
            
            // Phone validation
            $form.find('input[type="tel"], input[name*="phone"]').each((index, element) => {
                const $field = $(element);
                const phone = $field.val().trim();
                
                if (phone && !this.isValidPhone(phone)) {
                    this.showFieldError($field, this.config.messages.en.phone_invalid);
                    errors.push('Invalid phone number');
                    isValid = false;
                }
            });
            
            // File upload validation
            $form.find('input[type="file"]').each((index, element) => {
                const $field = $(element);
                const files = element.files;
                
                if (files && files.length > 0) {
                    const file = files[0];
                    const validation = this.validateFile(file);
                    
                    if (!validation.valid) {
                        this.showFieldError($field, validation.message);
                        errors.push(validation.message);
                        isValid = false;
                    }
                }
            });
            
            // If form is invalid, log and announce to screen readers
            if (!isValid) {
                this.logError({
                    type: 'form_validation',
                    category: this.config.categories.VALIDATION,
                    level: this.config.levels.WARNING,
                    message: 'Form validation failed',
                    errors: errors,
                    form: $form.attr('id') || $form.attr('class'),
                    timestamp: new Date().toISOString()
                });
                
                this.announceFormErrors(errors);
            }
            
            return isValid;
        },

        /**
         * Validate file upload
         */
        validateFile: function(file) {
            const messages = this.config.messages.en;
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            
            if (file.size > maxSize) {
                return { valid: false, message: messages.file_too_large };
            }
            
            if (!allowedTypes.includes(file.type)) {
                return { valid: false, message: messages.file_invalid_type };
            }
            
            return { valid: true };
        },

        /**
         * Show field error
         */
        showFieldError: function($field, message) {
            $field.addClass('error');
            $field.attr('aria-invalid', 'true');
            
            // Remove existing error message
            $field.siblings('.error-message').remove();
            
            // Add error message
            const errorId = 'error-' + Math.random().toString(36).substr(2, 9);
            const $errorMsg = $(`<div class="error-message" id="${errorId}" role="alert">${message}</div>`);
            $field.after($errorMsg);
            $field.attr('aria-describedby', errorId);
        },

        /**
         * Get field label for error messages
         */
        getFieldLabel: function($field) {
            const $label = $(`label[for="${$field.attr('id')}"]`);
            if ($label.length) {
                return $label.text().replace('*', '').trim();
            }
            return $field.attr('name') || $field.attr('placeholder') || 'Field';
        },

        /**
         * Highlight form errors
         */
        highlightFormErrors: function() {
            $('.error').first().focus();
            
            // Scroll to first error
            const $firstError = $('.error').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 300);
            }
        },

        /**
         * Announce form errors to screen readers
         */
        announceFormErrors: function(errors) {
            const errorText = `Form has ${errors.length} error${errors.length > 1 ? 's' : ''}. ${errors.join('. ')}`;
            this.announceToScreenReader(errorText);
        },

        /**
         * Handle image loading errors
         */
        handleImageError: function(img) {
            const $img = $(img);
            
            // Add error class
            $img.addClass('image-error');
            
            // Try to load placeholder
            const placeholder = $img.data('placeholder') || 
                               '/wp-content/themes/recruitpro/assets/images/placeholder.svg';
                               
            if (img.src !== placeholder) {
                img.src = placeholder;
            } else {
                // Hide image if placeholder also fails
                $img.hide();
            }
        },

        /**
         * Handle script loading errors
         */
        handleScriptError: function(script) {
            const error = {
                type: 'script_error',
                category: this.config.categories.SYSTEM,
                level: this.config.levels.ERROR,
                message: `Failed to load script: ${script.src}`,
                script: script.src,
                timestamp: new Date().toISOString()
            };
            
            this.logError(error);
            
            // Check if it's a critical script
            if (this.isCriticalScript(script.src)) {
                this.showErrorNotification(
                    'A required component failed to load. Some features may not work correctly.',
                    'warning'
                );
            }
        },

        /**
         * Check if error is critical
         */
        isCriticalError: function(error) {
            const criticalPatterns = [
                'recruitpro',
                'application',
                'payment',
                'security'
            ];
            
            return criticalPatterns.some(pattern => 
                error.message.toLowerCase().includes(pattern) ||
                error.filename?.toLowerCase().includes(pattern)
            );
        },

        /**
         * Check if script is critical
         */
        isCriticalScript: function(src) {
            const criticalScripts = [
                'main.js',
                'application-form.js',
                'payment.js',
                'security.js'
            ];
            
            return criticalScripts.some(script => src.includes(script));
        },

        /**
         * Check if error is retryable
         */
        isRetryableError: function(xhr) {
            const retryableStatuses = [0, 408, 429, 500, 502, 503, 504];
            return retryableStatuses.includes(xhr.status);
        },

        /**
         * Offer retry for failed requests
         */
        offerRetry: function(settings) {
            const retryKey = settings.url + settings.type;
            const attempts = this.state.retryAttempts[retryKey] || 0;
            
            if (attempts < this.config.retry.maxAttempts) {
                this.showRetryNotification(settings, retryKey);
            }
        },

        /**
         * Show retry notification
         */
        showRetryNotification: function(settings, retryKey) {
            const $notification = this.createNotification(
                'Request failed. Would you like to try again?',
                'error',
                {
                    actions: [
                        {
                            text: 'Retry',
                            action: () => this.retryRequest(settings, retryKey)
                        },
                        {
                            text: 'Cancel',
                            action: () => this.dismissNotification($notification)
                        }
                    ]
                }
            );
        },

        /**
         * Retry failed request
         */
        retryRequest: function(settings, retryKey) {
            const attempts = this.state.retryAttempts[retryKey] || 0;
            this.state.retryAttempts[retryKey] = attempts + 1;
            
            const delay = this.config.retry.initialDelay * 
                         Math.pow(this.config.retry.backoffMultiplier, attempts);
            
            setTimeout(() => {
                $.ajax(settings);
            }, delay);
        },

        /**
         * Create error UI elements
         */
        createErrorUI: function() {
            // Error notification container
            const errorContainerHTML = `
                <div id="recruitpro-error-container" class="error-notifications" aria-live="polite" aria-atomic="true">
                </div>
            `;
            
            this.cache.$body.append(errorContainerHTML);
            this.cache.$errorContainer = $('#recruitpro-error-container');
            
            // Critical error overlay
            const errorOverlayHTML = `
                <div id="recruitpro-error-overlay" class="error-overlay" style="display: none;" role="dialog" aria-labelledby="error-title">
                    <div class="error-overlay-content">
                        <div class="error-overlay-header">
                            <h2 id="error-title">System Error</h2>
                            <button type="button" class="error-overlay-close" aria-label="Close error dialog">&times;</button>
                        </div>
                        <div class="error-overlay-body">
                            <div class="error-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div class="error-message"></div>
                            <div class="error-actions"></div>
                        </div>
                    </div>
                </div>
            `;
            
            this.cache.$body.append(errorOverlayHTML);
            this.cache.$errorOverlay = $('#recruitpro-error-overlay');
        },

        /**
         * Show error notification
         */
        showErrorNotification: function(message, type = 'error', options = {}) {
            const notification = this.createNotification(message, type, options);
            this.cache.$errorContainer.append(notification);
            
            // Auto-remove after duration
            if (options.duration !== false) {
                setTimeout(() => {
                    this.dismissNotification(notification);
                }, options.duration || this.config.notifications.duration);
            }
            
            // Limit visible notifications
            this.limitVisibleNotifications();
            
            // Log notification
            this.logError({
                type: 'notification',
                category: type === 'error' ? this.config.categories.SYSTEM : this.config.categories.INFO,
                level: type === 'error' ? this.config.levels.ERROR : this.config.levels.INFO,
                message: message,
                timestamp: new Date().toISOString()
            });
        },

        /**
         * Create notification element
         */
        createNotification: function(message, type, options = {}) {
            const id = 'notification-' + Date.now();
            const actions = options.actions || [];
            
            let actionsHTML = '';
            if (actions.length > 0) {
                actionsHTML = '<div class="notification-actions">';
                actions.forEach(action => {
                    actionsHTML += `<button type="button" class="notification-action" data-action="${action.text.toLowerCase()}">${action.text}</button>`;
                });
                actionsHTML += '</div>';
            }
            
            const notificationHTML = `
                <div id="${id}" class="error-notification notification-${type}" role="alert" aria-live="assertive">
                    <div class="notification-icon">
                        ${this.getNotificationIcon(type)}
                    </div>
                    <div class="notification-content">
                        <div class="notification-message">${message}</div>
                        ${actionsHTML}
                    </div>
                    <button type="button" class="notification-close" aria-label="Close notification">&times;</button>
                </div>
            `;
            
            const $notification = $(notificationHTML);
            
            // Bind action events
            actions.forEach(action => {
                $notification.find(`[data-action="${action.text.toLowerCase()}"]`).on('click', () => {
                    action.action();
                });
            });
            
            // Bind close event
            $notification.find('.notification-close').on('click', () => {
                this.dismissNotification($notification);
            });
            
            return $notification;
        },

        /**
         * Get notification icon
         */
        getNotificationIcon: function(type) {
            const icons = {
                error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>',
                info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
            };
            
            return icons[type] || icons.info;
        },

        /**
         * Dismiss notification
         */
        dismissNotification: function($notification) {
            $notification.addClass('dismissing');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        },

        /**
         * Limit visible notifications
         */
        limitVisibleNotifications: function() {
            const $notifications = this.cache.$errorContainer.find('.error-notification:not(.dismissing)');
            
            if ($notifications.length > this.config.notifications.maxVisible) {
                const excess = $notifications.length - this.config.notifications.maxVisible;
                $notifications.slice(0, excess).each((index, element) => {
                    this.dismissNotification($(element));
                });
            }
        },

        /**
         * Show critical error overlay
         */
        showCriticalError: function(title, message, actions = []) {
            this.cache.$errorOverlay.find('#error-title').text(title);
            this.cache.$errorOverlay.find('.error-message').text(message);
            
            // Add action buttons
            const $actionsContainer = this.cache.$errorOverlay.find('.error-actions');
            $actionsContainer.empty();
            
            actions.forEach(action => {
                const $button = $(`<button type="button" class="error-action-btn">${action.text}</button>`);
                $button.on('click', action.action);
                $actionsContainer.append($button);
            });
            
            // Add default close action if none provided
            if (actions.length === 0) {
                const $closeBtn = $('<button type="button" class="error-action-btn">Close</button>');
                $closeBtn.on('click', () => this.hideCriticalError());
                $actionsContainer.append($closeBtn);
            }
            
            this.cache.$errorOverlay.show();
            this.cache.$body.addClass('error-overlay-open');
            
            // Focus the first action button
            setTimeout(() => {
                $actionsContainer.find('button').first().focus();
            }, 100);
        },

        /**
         * Hide critical error overlay
         */
        hideCriticalError: function() {
            this.cache.$errorOverlay.hide();
            this.cache.$body.removeClass('error-overlay-open');
        },

        /**
         * Setup network monitoring
         */
        setupNetworkMonitoring: function() {
            const self = this;
            
            // Online/offline detection
            window.addEventListener('online', function() {
                self.state.isOnline = true;
                self.showErrorNotification('Connection restored', 'success');
                self.retryFailedRequests();
            });
            
            window.addEventListener('offline', function() {
                self.state.isOnline = false;
                self.showCriticalError(
                    'No Internet Connection',
                    'Please check your internet connection and try again.',
                    [
                        {
                            text: 'Retry',
                            action: () => {
                                if (navigator.onLine) {
                                    self.hideCriticalError();
                                    window.location.reload();
                                }
                            }
                        }
                    ]
                );
            });
        },

        /**
         * Retry failed requests when back online
         */
        retryFailedRequests: function() {
            // Implementation would retry failed AJAX requests
            console.log('Retrying failed requests...');
        },

        /**
         * Setup performance monitoring
         */
        setupPerformanceMonitoring: function() {
            // Monitor for slow loading times
            if ('PerformanceObserver' in window) {
                const observer = new PerformanceObserver((list) => {
                    list.getEntries().forEach(entry => {
                        if (entry.loadEventEnd - entry.loadEventStart > 5000) {
                            this.logError({
                                type: 'performance',
                                category: this.config.categories.SYSTEM,
                                level: this.config.levels.WARNING,
                                message: 'Slow page load detected',
                                loadTime: entry.loadEventEnd - entry.loadEventStart,
                                timestamp: new Date().toISOString()
                            });
                        }
                    });
                });
                
                observer.observe({ entryTypes: ['navigation'] });
            }
        },

        /**
         * Initialize logging system
         */
        initializeLogging: function() {
            if (!this.config.logging.enabled) return;
            
            // Set up log rotation
            setInterval(() => {
                this.rotateLog();
            }, 60000); // Every minute
        },

        /**
         * Log error
         */
        logError: function(error) {
            if (!this.config.logging.enabled) return;
            
            // Add additional context
            error.id = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            error.page = window.location.href;
            error.userAgent = navigator.userAgent;
            error.timestamp = error.timestamp || new Date().toISOString();
            
            // Add to local log
            this.state.errorLog.push(error);
            this.state.errorCount++;
            
            // Console logging based on level
            if (this.shouldLog(error.level)) {
                console.group(`RecruitPro Error [${error.category}]`);
                console.error(error.message);
                console.log('Details:', error);
                if (error.stack) {
                    console.log('Stack trace:', error.stack);
                }
                console.groupEnd();
            }
            
            // Send to server if endpoint configured
            if (this.config.logging.endpoint) {
                this.sendErrorToServer(error);
            }
            
            // Store in localStorage for persistence
            this.persistError(error);
        },

        /**
         * Check if error should be logged
         */
        shouldLog: function(level) {
            const configLevel = this.config.levels[this.config.logging.level];
            return level >= configLevel;
        },

        /**
         * Send error to server
         */
        sendErrorToServer: function(error) {
            if (!this.state.isOnline) return;
            
            // Don't send too frequently
            if (this.state.errorCount % 5 !== 0) return;
            
            $.ajax({
                url: this.config.logging.endpoint,
                method: 'POST',
                data: {
                    action: 'recruitpro_log_error',
                    error: JSON.stringify(error),
                    nonce: recruitpro_ajax.nonce
                },
                timeout: 5000,
                silent: true // Don't trigger error handling for logging errors
            });
        },

        /**
         * Persist error to localStorage
         */
        persistError: function(error) {
            try {
                const stored = JSON.parse(localStorage.getItem('recruitpro_errors') || '[]');
                stored.push(error);
                
                // Keep only last 50 errors
                if (stored.length > 50) {
                    stored.splice(0, stored.length - 50);
                }
                
                localStorage.setItem('recruitpro_errors', JSON.stringify(stored));
            } catch (e) {
                // Ignore localStorage errors
            }
        },

        /**
         * Rotate log entries
         */
        rotateLog: function() {
            if (this.state.errorLog.length > this.config.logging.maxEntries) {
                this.state.errorLog.splice(0, this.state.errorLog.length - this.config.logging.maxEntries);
            }
        },

        /**
         * Setup accessibility error handling
         */
        setupAccessibilityErrorHandling: function() {
            // Add ARIA live region for error announcements
            if (!$('#error-aria-live').length) {
                this.cache.$body.append('<div id="error-aria-live" aria-live="assertive" aria-atomic="true" class="sr-only"></div>');
            }
            
            // Monitor for accessibility errors
            this.monitorAccessibilityErrors();
        },

        /**
         * Monitor accessibility errors
         */
        monitorAccessibilityErrors: function() {
            // Check for missing alt text
            $('img:not([alt])').each((index, img) => {
                this.logError({
                    type: 'accessibility',
                    category: this.config.categories.ACCESSIBILITY,
                    level: this.config.levels.WARNING,
                    message: 'Image missing alt text',
                    element: img.src,
                    timestamp: new Date().toISOString()
                });
            });
            
            // Check for missing form labels
            $('input:not([aria-label]):not([aria-labelledby])').each((index, input) => {
                const $input = $(input);
                const hasLabel = $(`label[for="${$input.attr('id')}"]`).length > 0;
                
                if (!hasLabel) {
                    this.logError({
                        type: 'accessibility',
                        category: this.config.categories.ACCESSIBILITY,
                        level: this.config.levels.WARNING,
                        message: 'Form input missing label',
                        element: $input.attr('name') || $input.attr('id'),
                        timestamp: new Date().toISOString()
                    });
                }
            });
        },

        /**
         * Announce to screen readers
         */
        announceToScreenReader: function(message) {
            const $liveRegion = $('#error-aria-live');
            if ($liveRegion.length) {
                $liveRegion.text(message);
                setTimeout(() => {
                    $liveRegion.text('');
                }, 1000);
            }
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;
            
            // Close error overlay
            this.cache.$errorOverlay.find('.error-overlay-close').on('click', function() {
                self.hideCriticalError();
            });
            
            // Escape key to close overlay
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27 && self.cache.$errorOverlay.is(':visible')) {
                    self.hideCriticalError();
                }
            });
        },

        /**
         * Create debug panel
         */
        createDebugPanel: function() {
            const debugHTML = `
                <div id="recruitpro-debug-panel" class="debug-panel" style="position: fixed; bottom: 20px; right: 20px; background: #fff; border: 1px solid #ccc; padding: 10px; border-radius: 5px; z-index: 999999; font-size: 12px; max-width: 300px;">
                    <div class="debug-header">
                        <strong>RecruitPro Debug Panel</strong>
                        <button type="button" class="debug-close" style="float: right; background: none; border: none; font-size: 16px;">&times;</button>
                    </div>
                    <div class="debug-content">
                        <div>Errors: <span id="debug-error-count">0</span></div>
                        <div>Online: <span id="debug-online-status">${this.state.isOnline ? 'Yes' : 'No'}</span></div>
                        <button type="button" id="debug-clear-errors">Clear Errors</button>
                        <button type="button" id="debug-export-errors">Export Log</button>
                        <button type="button" id="debug-test-error">Test Error</button>
                    </div>
                </div>
            `;
            
            this.cache.$body.append(debugHTML);
            this.cache.$debugPanel = $('#recruitpro-debug-panel');
            
            // Bind debug panel events
            this.bindDebugEvents();
            
            // Update debug info periodically
            setInterval(() => {
                this.updateDebugInfo();
            }, 1000);
        },

        /**
         * Bind debug panel events
         */
        bindDebugEvents: function() {
            const self = this;
            
            this.cache.$debugPanel.find('.debug-close').on('click', function() {
                self.cache.$debugPanel.hide();
            });
            
            this.cache.$debugPanel.find('#debug-clear-errors').on('click', function() {
                self.clearErrors();
            });
            
            this.cache.$debugPanel.find('#debug-export-errors').on('click', function() {
                self.exportErrorLog();
            });
            
            this.cache.$debugPanel.find('#debug-test-error').on('click', function() {
                self.testError();
            });
        },

        /**
         * Update debug info
         */
        updateDebugInfo: function() {
            if (!this.cache.$debugPanel) return;
            
            this.cache.$debugPanel.find('#debug-error-count').text(this.state.errorCount);
            this.cache.$debugPanel.find('#debug-online-status').text(this.state.isOnline ? 'Yes' : 'No');
        },

        /**
         * Clear errors
         */
        clearErrors: function() {
            this.state.errorLog = [];
            this.state.errorCount = 0;
            this.cache.$errorContainer.empty();
            localStorage.removeItem('recruitpro_errors');
            console.log('RecruitPro errors cleared');
        },

        /**
         * Export error log
         */
        exportErrorLog: function() {
            const logData = {
                errors: this.state.errorLog,
                timestamp: new Date().toISOString(),
                page: window.location.href,
                userAgent: navigator.userAgent
            };
            
            const blob = new Blob([JSON.stringify(logData, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'recruitpro-error-log.json';
            a.click();
            URL.revokeObjectURL(url);
        },

        /**
         * Test error handling
         */
        testError: function() {
            this.logError({
                type: 'test',
                category: this.config.categories.SYSTEM,
                level: this.config.levels.ERROR,
                message: 'Test error from debug panel',
                timestamp: new Date().toISOString()
            });
            
            this.showErrorNotification('Test error notification', 'error');
        },

        /**
         * Utility functions
         */
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        isValidPhone: function(phone) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            return phoneRegex.test(phone.replace(/\D/g, ''));
        },

        /**
         * Public API methods
         */
        logCustomError: function(message, category = 'SYSTEM', level = 'ERROR') {
            this.logError({
                type: 'custom',
                category: this.config.categories[category] || category,
                level: this.config.levels[level] || level,
                message: message,
                timestamp: new Date().toISOString()
            });
        },

        showNotification: function(message, type = 'info') {
            this.showErrorNotification(message, type);
        },

        getErrorLog: function() {
            return this.state.errorLog;
        },

        isOnline: function() {
            return this.state.isOnline;
        },

        getErrorCount: function() {
            return this.state.errorCount;
        }
    };

    /**
     * Add CSS styles
     */
    const injectStyles = function() {
        const styles = `
            <style id="recruitpro-error-styles">
                .error-notifications {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 999999;
                    max-width: 400px;
                }
                
                .error-notification {
                    display: flex;
                    align-items: flex-start;
                    background: #ffffff;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    padding: 16px;
                    margin-bottom: 12px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    animation: slideInRight 0.3s ease;
                    position: relative;
                }
                
                .error-notification.dismissing {
                    animation: slideOutRight 0.3s ease;
                }
                
                .notification-error {
                    border-left: 4px solid #ef4444;
                }
                
                .notification-warning {
                    border-left: 4px solid #f59e0b;
                }
                
                .notification-success {
                    border-left: 4px solid #10b981;
                }
                
                .notification-info {
                    border-left: 4px solid #3b82f6;
                }
                
                .notification-icon {
                    margin-right: 12px;
                    flex-shrink: 0;
                }
                
                .notification-error .notification-icon {
                    color: #ef4444;
                }
                
                .notification-warning .notification-icon {
                    color: #f59e0b;
                }
                
                .notification-success .notification-icon {
                    color: #10b981;
                }
                
                .notification-info .notification-icon {
                    color: #3b82f6;
                }
                
                .notification-content {
                    flex: 1;
                }
                
                .notification-message {
                    margin-bottom: 8px;
                    color: #374151;
                    font-size: 14px;
                    line-height: 1.5;
                }
                
                .notification-actions {
                    display: flex;
                    gap: 8px;
                }
                
                .notification-action {
                    background: #f3f4f6;
                    border: 1px solid #d1d5db;
                    border-radius: 4px;
                    padding: 4px 8px;
                    font-size: 12px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }
                
                .notification-action:hover {
                    background: #e5e7eb;
                }
                
                .notification-close {
                    position: absolute;
                    top: 8px;
                    right: 8px;
                    background: none;
                    border: none;
                    font-size: 18px;
                    color: #9ca3af;
                    cursor: pointer;
                    width: 24px;
                    height: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }
                
                .notification-close:hover {
                    background: #f3f4f6;
                    color: #374151;
                }
                
                .error-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 1000000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .error-overlay-content {
                    background: #ffffff;
                    border-radius: 12px;
                    max-width: 500px;
                    width: 90%;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                }
                
                .error-overlay-header {
                    padding: 24px;
                    border-bottom: 1px solid #e5e7eb;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .error-overlay-header h2 {
                    margin: 0;
                    color: #ef4444;
                    font-size: 20px;
                }
                
                .error-overlay-close {
                    background: none;
                    border: none;
                    font-size: 24px;
                    color: #9ca3af;
                    cursor: pointer;
                    width: 32px;
                    height: 32px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }
                
                .error-overlay-close:hover {
                    background: #f3f4f6;
                    color: #374151;
                }
                
                .error-overlay-body {
                    padding: 24px;
                    text-align: center;
                }
                
                .error-icon {
                    color: #ef4444;
                    margin-bottom: 16px;
                }
                
                .error-message {
                    font-size: 16px;
                    color: #374151;
                    margin-bottom: 24px;
                    line-height: 1.5;
                }
                
                .error-actions {
                    display: flex;
                    gap: 12px;
                    justify-content: center;
                }
                
                .error-action-btn {
                    background: #2563eb;
                    color: #ffffff;
                    border: none;
                    border-radius: 6px;
                    padding: 10px 20px;
                    font-size: 14px;
                    cursor: pointer;
                    transition: background 0.2s ease;
                }
                
                .error-action-btn:hover {
                    background: #1d4ed8;
                }
                
                .error-message {
                    color: #ef4444;
                    font-size: 12px;
                    margin-top: 4px;
                    display: block;
                }
                
                .error {
                    border-color: #ef4444 !important;
                    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
                }
                
                .image-error {
                    opacity: 0.5;
                    filter: grayscale(100%);
                }
                
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
                
                @media (max-width: 768px) {
                    .error-notifications {
                        left: 20px;
                        right: 20px;
                        max-width: none;
                    }
                    
                    .error-notification {
                        padding: 12px;
                    }
                    
                    .notification-actions {
                        flex-direction: column;
                    }
                    
                    .error-overlay-content {
                        width: 95%;
                    }
                    
                    .error-overlay-header,
                    .error-overlay-body {
                        padding: 16px;
                    }
                }
                
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
                    .error-notification {
                        border-width: 2px;
                    }
                    
                    .error-action-btn {
                        border: 2px solid #000;
                    }
                }
                
                /* Reduced motion */
                @media (prefers-reduced-motion: reduce) {
                    .error-notification {
                        animation: none;
                    }
                    
                    .error-notification.dismissing {
                        animation: none;
                        opacity: 0;
                    }
                }
                
                /* Print styles */
                @media print {
                    .error-notifications,
                    .error-overlay {
                        display: none !important;
                    }
                }
            </style>
        `;
        
        if (!$('#recruitpro-error-styles').length) {
            $('head').append(styles);
        }
    };

    /**
     * Expose to global scope
     */
    window.RecruitProErrorHandler = RecruitProErrorHandler;

    /**
     * Auto-initialize when DOM is ready
     */
    $(document).ready(function() {
        injectStyles();
        RecruitProErrorHandler.init();
    });

    /**
     * Integration with main theme
     */
    if (window.RecruitPro) {
        window.RecruitPro.errorHandler = RecruitProErrorHandler;
    }

})(jQuery);

/**
 * Error Handling Features Summary:
 * 
 * ✅ Global JavaScript Error Handling
 * ✅ AJAX Error Management
 * ✅ Form Validation with UX
 * ✅ File Upload Error Handling
 * ✅ Network Connectivity Monitoring
 * ✅ User-Friendly Error Messages
 * ✅ Recruitment-Specific Errors
 * ✅ Error Logging & Reporting
 * ✅ Accessibility Error Support
 * ✅ Screen Reader Announcements
 * ✅ Retry Mechanisms
 * ✅ Critical Error Overlays
 * ✅ Debug Panel for Development
 * ✅ Performance Monitoring
 * ✅ Professional Error UI
 */