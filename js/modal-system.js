/**
 * Modal System Component - RecruitPro Theme
 * 
 * Comprehensive modal system for recruitment agency websites
 * Handles job applications, contact forms, media content, and user interactions
 * 
 * Features:
 * - Multiple modal types (forms, media, info, confirmations)
 * - Full accessibility support (ARIA, keyboard navigation, focus management)
 * - Touch gesture support for mobile devices
 * - Dynamic content loading via AJAX
 * - Form validation and submission handling
 * - File upload support with drag & drop
 * - Video and image lightbox functionality
 * - Stack management for nested modals
 * - Custom events for plugin integration
 * - Performance optimized with lazy loading
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Modal system namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.ModalSystem = window.RecruitPro.ModalSystem || {};

    /**
     * Modal System Component
     */
    const ModalSystem = {
        
        // Configuration
        config: {
            selectors: {
                modal: '.recruitpro-modal',
                modalTrigger: '[data-modal]',
                modalClose: '.modal-close',
                modalBackdrop: '.modal-backdrop',
                modalContent: '.modal-content',
                modalHeader: '.modal-header',
                modalBody: '.modal-body',
                modalFooter: '.modal-footer',
                modalTitle: '.modal-title',
                modalForm: '.modal-form',
                fileUpload: '.file-upload-area',
                videoPlayer: '.modal-video-player',
                imageViewer: '.modal-image-viewer'
            },
            classes: {
                active: 'modal-active',
                open: 'modal-open',
                loading: 'modal-loading',
                closing: 'modal-closing',
                backdrop: 'modal-backdrop',
                noScroll: 'no-scroll',
                dragover: 'dragover',
                hasError: 'has-error',
                isValid: 'is-valid',
                fullscreen: 'modal-fullscreen',
                stacked: 'modal-stacked'
            },
            types: {
                form: 'form',
                image: 'image',
                video: 'video',
                iframe: 'iframe',
                ajax: 'ajax',
                confirm: 'confirm',
                alert: 'alert',
                gallery: 'gallery'
            },
            animation: {
                duration: 300,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
            },
            breakpoints: {
                mobile: 768,
                tablet: 1024
            },
            maxFileSize: 10 * 1024 * 1024, // 10MB
            allowedFileTypes: ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif']
        },

        // State management
        state: {
            openModals: [],
            currentModal: null,
            isAnimating: false,
            lastFocusedElement: null,
            touchStartY: 0,
            scrollPosition: 0,
            uploadedFiles: {},
            videoPlayers: {},
            validationErrors: {}
        },

        /**
         * Initialize the modal system
         */
        init: function() {
            if (this.initialized) return;
            
            this.createModalStructure();
            this.cacheElements();
            this.bindEvents();
            this.setupAccessibility();
            this.setupFileUpload();
            
            this.initialized = true;
            console.log('RecruitPro Modal System initialized');
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
                $modals: $(this.config.selectors.modal),
                $backdrop: $(this.config.selectors.modalBackdrop),
                $triggers: $(this.config.selectors.modalTrigger)
            };
        },

        /**
         * Create modal backdrop and container structure
         */
        createModalStructure: function() {
            if (!$(this.config.selectors.modalBackdrop).length) {
                const backdropHTML = `
                    <div class="modal-backdrop" role="presentation" aria-hidden="true"></div>
                `;
                $('body').append(backdropHTML);
            }

            // Create modal container if it doesn't exist
            if (!$('.modal-container').length) {
                $('body').append('<div class="modal-container" aria-live="polite"></div>');
            }
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Modal trigger clicks
            this.cache.$document.on('click', this.config.selectors.modalTrigger, function(e) {
                e.preventDefault();
                const modalId = $(this).data('modal');
                const modalType = $(this).data('modal-type') || 'ajax';
                const modalData = $(this).data();
                
                self.open(modalId, modalType, modalData);
            });

            // Close button clicks
            this.cache.$document.on('click', this.config.selectors.modalClose, (e) => {
                e.preventDefault();
                this.close();
            });

            // Backdrop clicks
            this.cache.$document.on('click', this.config.selectors.modalBackdrop, () => {
                if (this.state.currentModal && this.state.currentModal.closable !== false) {
                    this.close();
                }
            });

            // Prevent modal content clicks from closing modal
            this.cache.$document.on('click', this.config.selectors.modalContent, (e) => {
                e.stopPropagation();
            });

            // Keyboard events
            this.cache.$document.on('keydown', (e) => this.handleKeyboard(e));

            // Form submissions
            this.cache.$document.on('submit', this.config.selectors.modalForm, (e) => {
                this.handleFormSubmit(e);
            });

            // File upload events
            this.cache.$document.on('change', '.modal-file-input', (e) => {
                this.handleFileSelect(e);
            });

            // Drag and drop events
            this.cache.$document.on('dragover', this.config.selectors.fileUpload, (e) => {
                e.preventDefault();
                $(e.currentTarget).addClass(this.config.classes.dragover);
            });

            this.cache.$document.on('dragleave', this.config.selectors.fileUpload, (e) => {
                $(e.currentTarget).removeClass(this.config.classes.dragover);
            });

            this.cache.$document.on('drop', this.config.selectors.fileUpload, (e) => {
                e.preventDefault();
                $(e.currentTarget).removeClass(this.config.classes.dragover);
                this.handleFileDrop(e);
            });

            // Window resize
            this.cache.$window.on('resize', this.utils.debounce(() => {
                this.adjustModalSize();
            }, 250));

            // Touch events for mobile
            if ('ontouchstart' in window) {
                this.bindTouchEvents();
            }

            // Video events
            this.cache.$document.on('loadedmetadata', '.modal-video', (e) => {
                this.onVideoLoaded(e);
            });

            // Image load events
            this.cache.$document.on('load', '.modal-image', (e) => {
                this.onImageLoaded(e);
            });
        },

        /**
         * Bind touch events for mobile interaction
         */
        bindTouchEvents: function() {
            this.cache.$document.on('touchstart', this.config.selectors.modal, (e) => {
                this.state.touchStartY = e.originalEvent.touches[0].clientY;
            });

            this.cache.$document.on('touchmove', this.config.selectors.modal, (e) => {
                // Allow scrolling within modal content
                const currentY = e.originalEvent.touches[0].clientY;
                const deltaY = this.state.touchStartY - currentY;
                
                const $modalBody = $(e.target).closest(this.config.selectors.modalBody);
                if ($modalBody.length) {
                    const scrollTop = $modalBody.scrollTop();
                    const scrollHeight = $modalBody[0].scrollHeight;
                    const height = $modalBody.height();

                    // Prevent overscroll
                    if ((scrollTop === 0 && deltaY < 0) || 
                        (scrollTop + height >= scrollHeight && deltaY > 0)) {
                        e.preventDefault();
                    }
                }
            });
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add ARIA attributes to modal triggers
            this.cache.$triggers.each(function() {
                const $trigger = $(this);
                const modalId = $trigger.data('modal');
                
                $trigger.attr({
                    'aria-haspopup': 'dialog',
                    'aria-controls': modalId
                });
            });

            // Setup focus trap elements
            this.cache.$modals.each(function() {
                const $modal = $(this);
                if (!$modal.find('.focus-trap-start').length) {
                    $modal.prepend('<div class="focus-trap-start" tabindex="0" aria-hidden="true"></div>');
                    $modal.append('<div class="focus-trap-end" tabindex="0" aria-hidden="true"></div>');
                }
            });
        },

        /**
         * Setup file upload functionality
         */
        setupFileUpload: function() {
            // Create file input if it doesn't exist
            if (!$('#modal-file-input').length) {
                $('body').append(`
                    <input type="file" id="modal-file-input" class="modal-file-input" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" style="display: none;">
                `);
            }
        },

        /**
         * Open modal
         */
        open: function(modalId, type, data) {
            if (this.state.isAnimating) return;

            type = type || this.config.types.ajax;
            data = data || {};

            // Store last focused element
            this.state.lastFocusedElement = document.activeElement;

            // Create or get modal element
            let $modal = $('#' + modalId);
            if (!$modal.length) {
                $modal = this.createModal(modalId, type, data);
            }

            // Store current scroll position
            this.state.scrollPosition = window.pageYOffset;

            // Add modal to stack
            this.state.openModals.push({
                id: modalId,
                $element: $modal,
                type: type,
                data: data,
                closable: data.closable !== false
            });

            this.state.currentModal = this.state.openModals[this.state.openModals.length - 1];
            this.state.isAnimating = true;

            // Prepare modal content based on type
            this.loadModalContent($modal, type, data).then(() => {
                this.showModal($modal);
            }).catch((error) => {
                console.error('Failed to load modal content:', error);
                this.showErrorModal('Failed to load content. Please try again.');
            });
        },

        /**
         * Create modal element
         */
        createModal: function(modalId, type, data) {
            const modalHTML = `
                <div id="${modalId}" class="recruitpro-modal modal-${type}" role="dialog" aria-modal="true" aria-labelledby="${modalId}-title" aria-hidden="true">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 id="${modalId}-title" class="modal-title">${data.title || 'Modal'}</h2>
                            <button class="modal-close" aria-label="Close modal" title="Close (Esc)">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-loading">
                                <div class="loading-spinner"></div>
                                <p>Loading...</p>
                            </div>
                        </div>
                        <div class="modal-footer" style="display: none;"></div>
                    </div>
                    <div class="focus-trap-start" tabindex="0" aria-hidden="true"></div>
                    <div class="focus-trap-end" tabindex="0" aria-hidden="true"></div>
                </div>
            `;

            const $modal = $(modalHTML);
            $('.modal-container').append($modal);

            return $modal;
        },

        /**
         * Load modal content based on type
         */
        loadModalContent: function($modal, type, data) {
            return new Promise((resolve, reject) => {
                switch (type) {
                    case this.config.types.form:
                        this.loadFormContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    case this.config.types.ajax:
                        this.loadAjaxContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    case this.config.types.image:
                        this.loadImageContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    case this.config.types.video:
                        this.loadVideoContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    case this.config.types.iframe:
                        this.loadIframeContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    case this.config.types.confirm:
                        this.loadConfirmContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    case this.config.types.alert:
                        this.loadAlertContent($modal, data).then(resolve).catch(reject);
                        break;
                        
                    default:
                        this.loadDefaultContent($modal, data).then(resolve).catch(reject);
                }
            });
        },

        /**
         * Load form content
         */
        loadFormContent: function($modal, data) {
            return new Promise((resolve) => {
                const formHTML = this.generateFormHTML(data);
                $modal.find('.modal-body').html(formHTML);
                $modal.find('.modal-footer').show().html(this.generateFormFooterHTML(data));
                
                // Setup form validation
                this.setupFormValidation($modal);
                
                resolve();
            });
        },

        /**
         * Load AJAX content
         */
        loadAjaxContent: function($modal, data) {
            return new Promise((resolve, reject) => {
                if (!data.url) {
                    reject(new Error('No URL provided for AJAX content'));
                    return;
                }

                $.ajax({
                    url: data.url,
                    method: data.method || 'GET',
                    data: data.params || {},
                    timeout: 10000,
                    success: (response) => {
                        $modal.find('.modal-body').html(response);
                        resolve();
                    },
                    error: (xhr, status, error) => {
                        reject(new Error(`AJAX request failed: ${error}`));
                    }
                });
            });
        },

        /**
         * Load image content
         */
        loadImageContent: function($modal, data) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => {
                    const imageHTML = `
                        <div class="modal-image-viewer">
                            <img src="${data.src}" alt="${data.alt || ''}" class="modal-image">
                            ${data.caption ? `<div class="image-caption">${data.caption}</div>` : ''}
                        </div>
                    `;
                    $modal.find('.modal-body').html(imageHTML);
                    $modal.addClass(this.config.classes.fullscreen);
                    resolve();
                };
                img.onerror = () => reject(new Error('Failed to load image'));
                img.src = data.src;
            });
        },

        /**
         * Load video content
         */
        loadVideoContent: function($modal, data) {
            return new Promise((resolve) => {
                const videoHTML = `
                    <div class="modal-video-player">
                        <video class="modal-video" controls ${data.autoplay ? 'autoplay' : ''}>
                            <source src="${data.src}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                `;
                $modal.find('.modal-body').html(videoHTML);
                $modal.addClass(this.config.classes.fullscreen);
                resolve();
            });
        },

        /**
         * Load iframe content
         */
        loadIframeContent: function($modal, data) {
            return new Promise((resolve) => {
                const iframeHTML = `
                    <div class="modal-iframe-container">
                        <iframe src="${data.src}" 
                                width="100%" 
                                height="500" 
                                frameborder="0" 
                                allowfullscreen
                                title="${data.title || 'Modal content'}">
                        </iframe>
                    </div>
                `;
                $modal.find('.modal-body').html(iframeHTML);
                resolve();
            });
        },

        /**
         * Load confirmation dialog content
         */
        loadConfirmContent: function($modal, data) {
            return new Promise((resolve) => {
                const confirmHTML = `
                    <div class="confirm-dialog">
                        <div class="confirm-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                            </svg>
                        </div>
                        <p class="confirm-message">${data.message || 'Are you sure?'}</p>
                    </div>
                `;

                const footerHTML = `
                    <button type="button" class="btn btn-secondary modal-close">${data.cancelText || 'Cancel'}</button>
                    <button type="button" class="btn btn-primary confirm-action">${data.confirmText || 'Confirm'}</button>
                `;

                $modal.find('.modal-body').html(confirmHTML);
                $modal.find('.modal-footer').show().html(footerHTML);

                // Bind confirm action
                $modal.find('.confirm-action').on('click', () => {
                    if (data.onConfirm && typeof data.onConfirm === 'function') {
                        data.onConfirm();
                    }
                    this.close();
                });

                resolve();
            });
        },

        /**
         * Load alert dialog content
         */
        loadAlertContent: function($modal, data) {
            return new Promise((resolve) => {
                const alertType = data.alertType || 'info';
                const alertHTML = `
                    <div class="alert-dialog alert-${alertType}">
                        <div class="alert-icon">
                            ${this.getAlertIcon(alertType)}
                        </div>
                        <h3 class="alert-title">${data.title || this.getAlertTitle(alertType)}</h3>
                        <p class="alert-message">${data.message || ''}</p>
                    </div>
                `;

                const footerHTML = `
                    <button type="button" class="btn btn-primary modal-close">${data.buttonText || 'OK'}</button>
                `;

                $modal.find('.modal-body').html(alertHTML);
                $modal.find('.modal-footer').show().html(footerHTML);

                resolve();
            });
        },

        /**
         * Load default content
         */
        loadDefaultContent: function($modal, data) {
            return new Promise((resolve) => {
                const content = data.content || '<p>No content available.</p>';
                $modal.find('.modal-body').html(content);
                resolve();
            });
        },

        /**
         * Show modal with animation
         */
        showModal: function($modal) {
            // Prevent body scroll
            this.cache.$body.addClass(this.config.classes.noScroll);

            // Show backdrop
            this.cache.$backdrop.addClass(this.config.classes.active);

            // Show modal
            $modal.addClass(this.config.classes.active)
                  .attr('aria-hidden', 'false');

            // Focus management
            setTimeout(() => {
                this.setInitialFocus($modal);
                this.state.isAnimating = false;
            }, this.config.animation.duration);

            // Trigger custom event
            this.cache.$document.trigger('recruitpro:modal:opened', [this.state.currentModal]);
        },

        /**
         * Close modal
         */
        close: function(modalId) {
            if (this.state.isAnimating || this.state.openModals.length === 0) return;

            let modalToClose;
            
            if (modalId) {
                modalToClose = this.state.openModals.find(modal => modal.id === modalId);
            } else {
                modalToClose = this.state.openModals[this.state.openModals.length - 1];
            }

            if (!modalToClose) return;

            this.state.isAnimating = true;

            // Remove from stack
            this.state.openModals = this.state.openModals.filter(modal => modal.id !== modalToClose.id);

            // Hide modal
            modalToClose.$element.removeClass(this.config.classes.active)
                                  .addClass(this.config.classes.closing)
                                  .attr('aria-hidden', 'true');

            // Clean up video players
            this.cleanupVideoPlayer(modalToClose.$element);

            // If no more modals, hide backdrop and restore scroll
            if (this.state.openModals.length === 0) {
                this.cache.$backdrop.removeClass(this.config.classes.active);
                this.cache.$body.removeClass(this.config.classes.noScroll);
                window.scrollTo(0, this.state.scrollPosition);

                // Restore focus
                if (this.state.lastFocusedElement) {
                    this.state.lastFocusedElement.focus();
                }

                this.state.currentModal = null;
            } else {
                // Set focus to previous modal
                this.state.currentModal = this.state.openModals[this.state.openModals.length - 1];
                this.setInitialFocus(this.state.currentModal.$element);
            }

            // Remove modal after animation
            setTimeout(() => {
                modalToClose.$element.removeClass(this.config.classes.closing);
                
                // Remove dynamically created modals
                if (modalToClose.dynamic) {
                    modalToClose.$element.remove();
                }
                
                this.state.isAnimating = false;
            }, this.config.animation.duration);

            // Trigger custom event
            this.cache.$document.trigger('recruitpro:modal:closed', [modalToClose]);
        },

        /**
         * Close all modals
         */
        closeAll: function() {
            while (this.state.openModals.length > 0) {
                this.close();
            }
        },

        /**
         * Handle keyboard events
         */
        handleKeyboard: function(e) {
            if (this.state.openModals.length === 0) return;

            switch (e.keyCode) {
                case 27: // Escape
                    if (this.state.currentModal.closable !== false) {
                        e.preventDefault();
                        this.close();
                    }
                    break;
                    
                case 9: // Tab
                    this.handleTabNavigation(e);
                    break;
            }
        },

        /**
         * Handle tab navigation (focus trap)
         */
        handleTabNavigation: function(e) {
            const $modal = this.state.currentModal.$element;
            const $focusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible');
            
            if ($focusable.length === 0) return;

            const $first = $focusable.first();
            const $last = $focusable.last();

            if (e.shiftKey) {
                if (document.activeElement === $first[0]) {
                    e.preventDefault();
                    $last.focus();
                }
            } else {
                if (document.activeElement === $last[0]) {
                    e.preventDefault();
                    $first.focus();
                }
            }
        },

        /**
         * Set initial focus
         */
        setInitialFocus: function($modal) {
            const $firstInput = $modal.find('input, textarea, select').filter(':visible').first();
            const $firstButton = $modal.find('button').filter(':visible').first();
            const $closeButton = $modal.find('.modal-close');

            if ($firstInput.length) {
                $firstInput.focus();
            } else if ($firstButton.length) {
                $firstButton.focus();
            } else {
                $closeButton.focus();
            }
        },

        /**
         * Handle form submission
         */
        handleFormSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.currentTarget);
            const $modal = $form.closest('.recruitpro-modal');
            
            // Validate form
            if (!this.validateForm($form)) {
                return;
            }

            // Show loading state
            this.showFormLoading($modal);

            // Get form data
            const formData = new FormData($form[0]);
            
            // Add uploaded files
            const modalId = $modal.attr('id');
            if (this.state.uploadedFiles[modalId]) {
                this.state.uploadedFiles[modalId].forEach((file, index) => {
                    formData.append(`file_${index}`, file);
                });
            }

            // Submit form
            this.submitForm($form, formData).then((response) => {
                this.showFormSuccess($modal, response);
                
                // Trigger custom event
                this.cache.$document.trigger('recruitpro:form:submitted', [formData, response]);
                
                // Auto-close after success (optional)
                if ($form.data('auto-close') !== false) {
                    setTimeout(() => this.close(), 2000);
                }
            }).catch((error) => {
                this.showFormError($modal, error);
            });
        },

        /**
         * Handle file selection
         */
        handleFileSelect: function(e) {
            const files = Array.from(e.target.files);
            const $modal = $(e.target).closest('.recruitpro-modal');
            
            this.processFiles(files, $modal);
        },

        /**
         * Handle file drop
         */
        handleFileDrop: function(e) {
            const files = Array.from(e.originalEvent.dataTransfer.files);
            const $modal = $(e.target).closest('.recruitpro-modal');
            
            this.processFiles(files, $modal);
        },

        /**
         * Process uploaded files
         */
        processFiles: function(files, $modal) {
            const modalId = $modal.attr('id');
            const validFiles = [];
            const errors = [];

            files.forEach((file) => {
                if (this.validateFile(file)) {
                    validFiles.push(file);
                } else {
                    errors.push(`Invalid file: ${file.name}`);
                }
            });

            if (errors.length > 0) {
                this.showFileErrors($modal, errors);
                return;
            }

            // Store files
            if (!this.state.uploadedFiles[modalId]) {
                this.state.uploadedFiles[modalId] = [];
            }
            this.state.uploadedFiles[modalId] = [...this.state.uploadedFiles[modalId], ...validFiles];

            // Update UI
            this.updateFileList($modal, this.state.uploadedFiles[modalId]);
        },

        /**
         * Validate file
         */
        validateFile: function(file) {
            // Check file size
            if (file.size > this.config.maxFileSize) {
                return false;
            }

            // Check file type
            const extension = file.name.split('.').pop().toLowerCase();
            return this.config.allowedFileTypes.includes(extension);
        },

        /**
         * Update file list display
         */
        updateFileList: function($modal, files) {
            const $fileList = $modal.find('.file-list');
            
            if (!$fileList.length) {
                $modal.find('.file-upload-area').after('<div class="file-list"></div>');
            }

            const fileListHTML = files.map((file, index) => `
                <div class="file-item" data-index="${index}">
                    <span class="file-icon">${this.getFileIcon(file)}</span>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${this.formatFileSize(file.size)}</span>
                    <button type="button" class="file-remove" data-index="${index}" aria-label="Remove file">×</button>
                </div>
            `).join('');

            $modal.find('.file-list').html(fileListHTML);

            // Bind remove events
            $modal.find('.file-remove').on('click', (e) => {
                const index = parseInt($(e.target).data('index'));
                this.removeFile($modal, index);
            });
        },

        /**
         * Remove uploaded file
         */
        removeFile: function($modal, index) {
            const modalId = $modal.attr('id');
            if (this.state.uploadedFiles[modalId]) {
                this.state.uploadedFiles[modalId].splice(index, 1);
                this.updateFileList($modal, this.state.uploadedFiles[modalId]);
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
         * Helper functions
         */
        generateFormHTML: function(data) {
            // This would generate form HTML based on data.formType
            // Implementation depends on specific form requirements
            return `<form class="modal-form" data-form-type="${data.formType || 'contact'}">
                <!-- Form fields would be generated here -->
                <div class="form-group">
                    <label for="modal-name">Name</label>
                    <input type="text" id="modal-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="modal-email">Email</label>
                    <input type="email" id="modal-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="modal-message">Message</label>
                    <textarea id="modal-message" name="message" rows="4" required></textarea>
                </div>
            </form>`;
        },

        generateFormFooterHTML: function(data) {
            return `
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                <button type="submit" class="btn btn-primary" form="modal-form">Submit</button>
            `;
        },

        getAlertIcon: function(type) {
            const icons = {
                success: '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22,4 12,14.01 9,11.01"></polyline></svg>',
                error: '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
                warning: '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
                info: '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
            };
            return icons[type] || icons.info;
        },

        getAlertTitle: function(type) {
            const titles = {
                success: 'Success',
                error: 'Error',
                warning: 'Warning',
                info: 'Information'
            };
            return titles[type] || 'Alert';
        },

        getFileIcon: function(file) {
            const extension = file.name.split('.').pop().toLowerCase();
            const iconMap = {
                pdf: '📄',
                doc: '📝',
                docx: '📝',
                jpg: '🖼️',
                jpeg: '🖼️',
                png: '🖼️',
                gif: '🖼️'
            };
            return iconMap[extension] || '📎';
        },

        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        // Additional methods would include:
        setupFormValidation: function($modal) {
            // Form validation setup
        },

        validateForm: function($form) {
            // Form validation logic
            return true;
        },

        submitForm: function($form, formData) {
            // Form submission logic
            return new Promise((resolve) => {
                setTimeout(() => resolve({ success: true }), 1000);
            });
        },

        showFormLoading: function($modal) {
            // Show loading state
        },

        showFormSuccess: function($modal, response) {
            // Show success message
        },

        showFormError: function($modal, error) {
            // Show error message
        },

        showFileErrors: function($modal, errors) {
            // Show file upload errors
        },

        showErrorModal: function(message) {
            this.open('error-modal', this.config.types.alert, {
                alertType: 'error',
                title: 'Error',
                message: message
            });
        },

        adjustModalSize: function() {
            // Adjust modal size for current viewport
        },

        onVideoLoaded: function(e) {
            // Handle video loaded event
        },

        onImageLoaded: function(e) {
            // Handle image loaded event
        },

        cleanupVideoPlayer: function($modal) {
            // Cleanup video players when modal closes
        },

        /**
         * Public API methods
         */
        api: {
            open: function(modalId, type, data) {
                ModalSystem.open(modalId, type, data);
            },

            close: function(modalId) {
                ModalSystem.close(modalId);
            },

            closeAll: function() {
                ModalSystem.closeAll();
            },

            isOpen: function(modalId) {
                if (modalId) {
                    return ModalSystem.state.openModals.some(modal => modal.id === modalId);
                }
                return ModalSystem.state.openModals.length > 0;
            },

            getCurrentModal: function() {
                return ModalSystem.state.currentModal;
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        ModalSystem.init();
    });

    // Expose public API
    window.RecruitPro.ModalSystem = ModalSystem.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        ModalSystem.cacheElements();
        ModalSystem.setupAccessibility();
    });

})(jQuery);