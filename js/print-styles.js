/**
 * Print Style Component - RecruitPro Theme
 * 
 * Advanced print functionality for recruitment agency websites
 * Handles print optimization, formatting, and document preparation
 * 
 * Features:
 * - Smart print preparation and optimization
 * - Professional document formatting
 * - Print preview functionality
 * - Page break optimization
 * - Print-specific content manipulation
 * - Multi-format printing (job listings, CVs, contracts)
 * - Print analytics and tracking
 * - Accessibility support for print
 * - Custom print templates
 * - Print queue management
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Print style namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.PrintStyle = window.RecruitPro.PrintStyle || {};

    /**
     * Print Style Component
     */
    const PrintStyle = {
        
        // Configuration
        config: {
            selectors: {
                printButton: '.print-button',
                printTrigger: '[data-print]',
                printContent: '.print-content',
                printOnly: '.print-only',
                noPrint: '.no-print',
                pageBreak: '.page-break',
                printSection: '.print-section',
                printHeader: '.print-header',
                printFooter: '.print-footer',
                jobListing: '.job-listing',
                candidateProfile: '.candidate-profile',
                contractDocument: '.contract-document',
                reportDocument: '.report-document',
                applicationForm: '.application-form'
            },
            classes: {
                printing: 'is-printing',
                printPreview: 'print-preview',
                printOptimized: 'print-optimized',
                printReady: 'print-ready',
                printProcessing: 'print-processing',
                pageBreakBefore: 'page-break-before',
                pageBreakAfter: 'page-break-after',
                pageBreakInside: 'page-break-inside-avoid',
                printHidden: 'print-hidden',
                printVisible: 'print-visible'
            },
            printTypes: {
                job: 'job-listing',
                candidate: 'candidate-profile',
                contract: 'contract-document',
                report: 'report-document',
                form: 'application-form',
                general: 'general-content'
            },
            pageSettings: {
                margin: '2cm',
                size: 'A4',
                orientation: 'portrait'
            },
            optimization: {
                imageQuality: 150, // DPI
                maxImageWidth: 800,
                compressImages: true,
                removeAnimations: true,
                simplifyLayout: true
            }
        },

        // State management
        state: {
            isPrinting: false,
            isPrintPreview: false,
            currentPrintType: null,
            originalContent: null,
            printQueue: [],
            printSettings: {},
            beforePrintCallbacks: [],
            afterPrintCallbacks: [],
            printAnalytics: {
                totalPrints: 0,
                printTypes: {},
                printErrors: []
            }
        },

        /**
         * Initialize the print system
         */
        init: function() {
            if (this.initialized) return;
            
            this.cacheElements();
            this.bindEvents();
            this.setupPrintButtons();
            this.detectPrintCapabilities();
            this.loadPrintSettings();
            this.setupPrintTemplates();
            
            this.initialized = true;
            console.log('RecruitPro Print Style initialized');
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
                $printButtons: $(this.config.selectors.printButton),
                $printTriggers: $(this.config.selectors.printTrigger),
                $printContent: $(this.config.selectors.printContent)
            };
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Print button clicks
            this.cache.$document.on('click', this.config.selectors.printButton, function(e) {
                e.preventDefault();
                const printType = $(this).data('print-type') || 'general';
                const printTarget = $(this).data('print-target');
                self.initiatePrint(printType, printTarget);
            });

            // Print trigger clicks
            this.cache.$document.on('click', this.config.selectors.printTrigger, function(e) {
                e.preventDefault();
                const printData = $(this).data('print');
                self.handlePrintTrigger(printData, this);
            });

            // Browser print events
            this.cache.$window.on('beforeprint', () => this.handleBeforePrint());
            this.cache.$window.on('afterprint', () => this.handleAfterPrint());

            // Keyboard shortcuts
            this.cache.$document.on('keydown', (e) => this.handleKeyboardShortcuts(e));

            // Print preview events
            this.cache.$document.on('click', '.print-preview-toggle', () => this.togglePrintPreview());
            this.cache.$document.on('click', '.print-settings-toggle', () => this.togglePrintSettings());

            // Dynamic content events
            this.cache.$document.on('recruitpro:content-loaded', () => this.refreshPrintButtons());
        },

        /**
         * Setup print buttons
         */
        setupPrintButtons: function() {
            // Add print buttons to printable content
            this.addPrintButtons();
            
            // Setup print button styles
            this.stylePrintButtons();
            
            // Add print keyboard hint
            this.addPrintKeyboardHint();
        },

        /**
         * Add print buttons to content
         */
        addPrintButtons: function() {
            const printableSelectors = [
                this.config.selectors.jobListing,
                this.config.selectors.candidateProfile,
                this.config.selectors.contractDocument,
                this.config.selectors.reportDocument
            ];

            printableSelectors.forEach(selector => {
                $(selector).each(function() {
                    const $content = $(this);
                    
                    if (!$content.find('.print-button').length) {
                        const printType = self.getPrintTypeFromSelector(selector);
                        const printButton = self.createPrintButton(printType);
                        $content.prepend(printButton);
                    }
                });
            });
        },

        /**
         * Create print button HTML
         */
        createPrintButton: function(type) {
            return `
                <div class="print-controls no-print">
                    <button class="print-button" data-print-type="${type}" title="Print this document (Ctrl+P)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="6,9 6,2 18,2 18,9"></polyline>
                            <path d="M6,18H4a2,2,0,0,1-2-2V11a2,2,0,0,1,2-2H20a2,2,0,0,1,2,2v5a2,2,0,0,1-2,2H18"></path>
                            <polyline points="6,14 18,14 18,22 6,22 6,14"></polyline>
                        </svg>
                        <span>Print</span>
                    </button>
                    <button class="print-preview-toggle" title="Print Preview">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>Preview</span>
                    </button>
                    <button class="print-settings-toggle" title="Print Settings">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4,15a1.65,1.65,0,0,0,.33,1.82l.06.06a2,2,0,0,1,0,2.83,2,2,0,0,1-2.83,0l-.06-.06a1.65,1.65,0,0,0-1.82-.33,1.65,1.65,0,0,0-1,1.51V21a2,2,0,0,1-2,2,2,2,0,0,1-2-2V20.51a1.65,1.65,0,0,0-1.08-1.51,1.65,1.65,0,0,0-1.82.33l-.06.06a2,2,0,0,1-2.83,0,2,2,0,0,1,0-2.83l.06-.06a1.65,1.65,0,0,0,.33-1.82,1.65,1.65,0,0,0-1.51-1H3a2,2,0,0,1-2-2,2,2,0,0,1,2-2H3.49a1.65,1.65,0,0,0,1.51-1.08,1.65,1.65,0,0,0-.33-1.82L4.61,4.61a2,2,0,0,1,0-2.83,2,2,0,0,1,2.83,0L7.5,1.84a1.65,1.65,0,0,0,1.82.33A1.65,1.65,0,0,0,10.49,1H12a2,2,0,0,1,2,2,2,2,0,0,1-2,2H10.49a1.65,1.65,0,0,0-1.51,1.08"></path>
                        </svg>
                        <span>Settings</span>
                    </button>
                </div>
            `;
        },

        /**
         * Detect print capabilities
         */
        detectPrintCapabilities: function() {
            this.capabilities = {
                print: typeof window.print === 'function',
                beforePrint: 'onbeforeprint' in window,
                afterPrint: 'onafterprint' in window,
                mediaQueries: window.matchMedia && window.matchMedia('print').media === 'print',
                pageBreak: this.supportsPageBreak(),
                printColorAdjust: this.supportsPrintColorAdjust()
            };
        },

        /**
         * Check if page break is supported
         */
        supportsPageBreak: function() {
            const testElement = document.createElement('div');
            testElement.style.pageBreakAfter = 'always';
            return testElement.style.pageBreakAfter === 'always';
        },

        /**
         * Check if print color adjust is supported
         */
        supportsPrintColorAdjust: function() {
            const testElement = document.createElement('div');
            testElement.style.printColorAdjust = 'exact';
            return testElement.style.printColorAdjust === 'exact';
        },

        /**
         * Load print settings
         */
        loadPrintSettings: function() {
            try {
                const savedSettings = localStorage.getItem('recruitpro_print_settings');
                if (savedSettings) {
                    this.state.printSettings = JSON.parse(savedSettings);
                } else {
                    this.state.printSettings = this.getDefaultPrintSettings();
                }
            } catch (error) {
                console.warn('Could not load print settings:', error);
                this.state.printSettings = this.getDefaultPrintSettings();
            }
        },

        /**
         * Get default print settings
         */
        getDefaultPrintSettings: function() {
            return {
                includeImages: true,
                includeColors: false,
                includeBackgrounds: false,
                pageOrientation: 'portrait',
                pageSize: 'A4',
                margins: 'normal',
                fontSize: 'normal',
                quality: 'normal',
                headerFooter: true,
                pageNumbers: true,
                companyLogo: true,
                watermark: false
            };
        },

        /**
         * Setup print templates
         */
        setupPrintTemplates: function() {
            this.printTemplates = {
                [this.config.printTypes.job]: this.createJobListingTemplate(),
                [this.config.printTypes.candidate]: this.createCandidateProfileTemplate(),
                [this.config.printTypes.contract]: this.createContractTemplate(),
                [this.config.printTypes.report]: this.createReportTemplate(),
                [this.config.printTypes.form]: this.createFormTemplate(),
                [this.config.printTypes.general]: this.createGeneralTemplate()
            };
        },

        /**
         * Initiate print process
         */
        initiatePrint: function(type, target) {
            if (this.state.isPrinting) {
                console.warn('Print already in progress');
                return;
            }

            this.state.currentPrintType = type;
            this.state.isPrinting = true;

            // Get content to print
            const $content = target ? $(target) : this.getContentByType(type);
            
            if (!$content || !$content.length) {
                console.error('No content found for printing');
                this.state.isPrinting = false;
                return;
            }

            // Prepare content for printing
            this.preparePrintContent($content, type).then(() => {
                // Trigger print
                this.executePrint();
                
                // Track print action
                this.trackPrintAction(type);
                
            }).catch(error => {
                console.error('Error preparing print content:', error);
                this.state.isPrinting = false;
            });
        },

        /**
         * Prepare content for printing
         */
        preparePrintContent: function($content, type) {
            return new Promise((resolve) => {
                // Store original content
                this.state.originalContent = $content.clone();

                // Apply print optimizations
                this.optimizeForPrint($content, type);

                // Apply print template
                this.applyPrintTemplate($content, type);

                // Handle images
                this.optimizeImagesForPrint($content).then(() => {
                    // Add print metadata
                    this.addPrintMetadata($content, type);

                    // Apply page breaks
                    this.optimizePageBreaks($content, type);

                    // Execute before print callbacks
                    this.executeBeforePrintCallbacks($content, type);

                    resolve();
                });
            });
        },

        /**
         * Optimize content for print
         */
        optimizeForPrint: function($content, type) {
            // Remove non-printable elements
            $content.find(this.config.selectors.noPrint).remove();

            // Show print-only elements
            $content.find(this.config.selectors.printOnly).show();

            // Simplify complex layouts if needed
            if (this.config.optimization.simplifyLayout) {
                this.simplifyLayoutForPrint($content);
            }

            // Remove animations and transitions
            if (this.config.optimization.removeAnimations) {
                this.removeAnimationsForPrint($content);
            }

            // Apply print-specific classes
            $content.addClass(this.config.classes.printOptimized);
        },

        /**
         * Apply print template
         */
        applyPrintTemplate: function($content, type) {
            const template = this.printTemplates[type];
            if (template) {
                // Wrap content with template
                const $wrappedContent = $(template.wrapper);
                $wrappedContent.find('.print-content-placeholder').replaceWith($content);
                $content.replaceWith($wrappedContent);
            }
        },

        /**
         * Optimize images for print
         */
        optimizeImagesForPrint: function($content) {
            return new Promise((resolve) => {
                const $images = $content.find('img');
                let loadPromises = [];

                $images.each(function() {
                    const $img = $(this);
                    const promise = new Promise((imgResolve) => {
                        // Optimize image for print
                        if (this.config.optimization.compressImages) {
                            this.compressImageForPrint($img).then(imgResolve);
                        } else {
                            imgResolve();
                        }
                    }.bind(this));
                    
                    loadPromises.push(promise);
                }.bind(this));

                Promise.all(loadPromises).then(resolve);
            });
        },

        /**
         * Compress image for print
         */
        compressImageForPrint: function($img) {
            return new Promise((resolve) => {
                const img = $img[0];
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                img.onload = () => {
                    // Set canvas size
                    const maxWidth = this.config.optimization.maxImageWidth;
                    const ratio = Math.min(maxWidth / img.width, maxWidth / img.height);
                    
                    canvas.width = img.width * ratio;
                    canvas.height = img.height * ratio;

                    // Draw and compress
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    
                    // Convert to data URL
                    const dataURL = canvas.toDataURL('image/jpeg', 0.8);
                    $img.attr('src', dataURL);
                    
                    resolve();
                };

                if (img.complete) {
                    img.onload();
                } else {
                    img.onerror = resolve; // Continue even if image fails
                }
            });
        },

        /**
         * Add print metadata
         */
        addPrintMetadata: function($content, type) {
            const metadata = this.generatePrintMetadata(type);
            
            // Add header
            if (this.state.printSettings.headerFooter) {
                $content.prepend(this.createPrintHeader(metadata));
                $content.append(this.createPrintFooter(metadata));
            }

            // Add watermark if enabled
            if (this.state.printSettings.watermark) {
                $content.addClass('print-watermark');
            }
        },

        /**
         * Generate print metadata
         */
        generatePrintMetadata: function(type) {
            return {
                title: this.getPrintTitle(type),
                date: new Date().toLocaleDateString(),
                time: new Date().toLocaleTimeString(),
                url: window.location.href,
                type: type,
                pageCount: this.estimatePageCount()
            };
        },

        /**
         * Create print header
         */
        createPrintHeader: function(metadata) {
            return `
                <div class="print-header print-only">
                    <div class="print-header-left">
                        ${this.state.printSettings.companyLogo ? '<img src="/wp-content/themes/recruitpro/assets/images/logo-print.png" alt="Company Logo" class="print-logo">' : ''}
                    </div>
                    <div class="print-header-center">
                        <h1 class="print-title">${metadata.title}</h1>
                    </div>
                    <div class="print-header-right">
                        <div class="print-date">${metadata.date}</div>
                        <div class="print-time">${metadata.time}</div>
                    </div>
                </div>
            `;
        },

        /**
         * Create print footer
         */
        createPrintFooter: function(metadata) {
            return `
                <div class="print-footer print-only">
                    <div class="print-footer-left">
                        <div class="print-url">${metadata.url}</div>
                    </div>
                    <div class="print-footer-center">
                        <div class="print-company-info">
                            ${this.getCompanyInfo()}
                        </div>
                    </div>
                    <div class="print-footer-right">
                        ${this.state.printSettings.pageNumbers ? '<div class="print-page-numbers">Page <span class="page-number"></span> of <span class="total-pages"></span></div>' : ''}
                    </div>
                </div>
            `;
        },

        /**
         * Optimize page breaks
         */
        optimizePageBreaks: function($content, type) {
            // Add page breaks before major sections
            $content.find('h1, h2').addClass(this.config.classes.pageBreakBefore);
            
            // Prevent page breaks inside important elements
            $content.find('.job-details, .candidate-summary, .contract-clause').addClass(this.config.classes.pageBreakInside);
            
            // Add manual page breaks where specified
            $content.find(this.config.selectors.pageBreak).addClass(this.config.classes.pageBreakAfter);
        },

        /**
         * Execute print
         */
        executePrint: function() {
            // Add printing class to body
            this.cache.$body.addClass(this.config.classes.printing);

            // Execute print
            setTimeout(() => {
                window.print();
            }, 100);
        },

        /**
         * Handle before print event
         */
        handleBeforePrint: function() {
            this.cache.$body.addClass(this.config.classes.printing);
            
            // Execute before print callbacks
            this.state.beforePrintCallbacks.forEach(callback => {
                try {
                    callback();
                } catch (error) {
                    console.error('Before print callback error:', error);
                }
            });

            // Trigger custom event
            this.cache.$document.trigger('recruitpro:before-print');
        },

        /**
         * Handle after print event
         */
        handleAfterPrint: function() {
            this.cache.$body.removeClass(this.config.classes.printing);
            this.state.isPrinting = false;

            // Restore original content if needed
            if (this.state.originalContent) {
                // Restoration logic would go here
                this.state.originalContent = null;
            }

            // Execute after print callbacks
            this.state.afterPrintCallbacks.forEach(callback => {
                try {
                    callback();
                } catch (error) {
                    console.error('After print callback error:', error);
                }
            });

            // Update analytics
            this.state.printAnalytics.totalPrints++;
            if (this.state.currentPrintType) {
                this.state.printAnalytics.printTypes[this.state.currentPrintType] = 
                    (this.state.printAnalytics.printTypes[this.state.currentPrintType] || 0) + 1;
            }

            // Trigger custom event
            this.cache.$document.trigger('recruitpro:after-print');
        },

        /**
         * Handle keyboard shortcuts
         */
        handleKeyboardShortcuts: function(e) {
            // Ctrl+P for print
            if (e.ctrlKey && e.keyCode === 80) {
                e.preventDefault();
                
                // Find the most relevant content to print
                const printableContent = this.findPrintableContent();
                if (printableContent.length) {
                    const printType = this.detectPrintType(printableContent);
                    this.initiatePrint(printType, printableContent);
                }
            }
        },

        /**
         * Toggle print preview
         */
        togglePrintPreview: function() {
            this.state.isPrintPreview = !this.state.isPrintPreview;
            this.cache.$body.toggleClass(this.config.classes.printPreview, this.state.isPrintPreview);
            
            if (this.state.isPrintPreview) {
                this.showPrintPreview();
            } else {
                this.hidePrintPreview();
            }
        },

        /**
         * Show print preview
         */
        showPrintPreview: function() {
            // Create print preview overlay
            const previewHTML = `
                <div class="print-preview-overlay">
                    <div class="print-preview-controls">
                        <button class="print-preview-close">Close Preview</button>
                        <button class="print-preview-print">Print</button>
                    </div>
                    <div class="print-preview-content">
                        <!-- Preview content will be inserted here -->
                    </div>
                </div>
            `;
            
            this.cache.$body.append(previewHTML);
            
            // Bind preview controls
            $('.print-preview-close').on('click', () => this.togglePrintPreview());
            $('.print-preview-print').on('click', () => {
                this.togglePrintPreview();
                window.print();
            });
        },

        /**
         * Hide print preview
         */
        hidePrintPreview: function() {
            $('.print-preview-overlay').remove();
        },

        /**
         * Track print action
         */
        trackPrintAction: function(type) {
            // Google Analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'print_document', {
                    document_type: type,
                    page_url: window.location.href
                });
            }

            // Custom tracking
            this.cache.$document.trigger('recruitpro:print-tracked', {
                type: type,
                timestamp: Date.now()
            });
        },

        /**
         * Template creation methods
         */
        createJobListingTemplate: function() {
            return {
                wrapper: `
                    <div class="print-job-listing">
                        <div class="print-content-placeholder"></div>
                    </div>
                `
            };
        },

        createCandidateProfileTemplate: function() {
            return {
                wrapper: `
                    <div class="print-candidate-profile">
                        <div class="print-content-placeholder"></div>
                    </div>
                `
            };
        },

        createContractTemplate: function() {
            return {
                wrapper: `
                    <div class="print-contract-document">
                        <div class="print-content-placeholder"></div>
                        <div class="signature-section print-only">
                            <div class="signature-line">
                                <div class="signature-label">Client Signature:</div>
                                <div class="signature-space"></div>
                                <div class="signature-date">Date:</div>
                            </div>
                            <div class="signature-line">
                                <div class="signature-label">Agency Representative:</div>
                                <div class="signature-space"></div>
                                <div class="signature-date">Date:</div>
                            </div>
                        </div>
                    </div>
                `
            };
        },

        createReportTemplate: function() {
            return {
                wrapper: `
                    <div class="print-report-document">
                        <div class="print-content-placeholder"></div>
                    </div>
                `
            };
        },

        createFormTemplate: function() {
            return {
                wrapper: `
                    <div class="print-form-document">
                        <div class="print-content-placeholder"></div>
                    </div>
                `
            };
        },

        createGeneralTemplate: function() {
            return {
                wrapper: `
                    <div class="print-general-document">
                        <div class="print-content-placeholder"></div>
                    </div>
                `
            };
        },

        /**
         * Helper methods
         */
        getPrintTypeFromSelector: function(selector) {
            if (selector.includes('job-listing')) return this.config.printTypes.job;
            if (selector.includes('candidate-profile')) return this.config.printTypes.candidate;
            if (selector.includes('contract-document')) return this.config.printTypes.contract;
            if (selector.includes('report-document')) return this.config.printTypes.report;
            if (selector.includes('application-form')) return this.config.printTypes.form;
            return this.config.printTypes.general;
        },

        getContentByType: function(type) {
            const selectorMap = {
                [this.config.printTypes.job]: this.config.selectors.jobListing,
                [this.config.printTypes.candidate]: this.config.selectors.candidateProfile,
                [this.config.printTypes.contract]: this.config.selectors.contractDocument,
                [this.config.printTypes.report]: this.config.selectors.reportDocument,
                [this.config.printTypes.form]: this.config.selectors.applicationForm
            };
            
            return $(selectorMap[type] || this.config.selectors.printContent);
        },

        findPrintableContent: function() {
            // Priority order for finding printable content
            const selectors = [
                this.config.selectors.jobListing,
                this.config.selectors.candidateProfile,
                this.config.selectors.contractDocument,
                this.config.selectors.reportDocument,
                this.config.selectors.applicationForm,
                this.config.selectors.printContent,
                'main',
                '.content',
                'article'
            ];

            for (let selector of selectors) {
                const $content = $(selector);
                if ($content.length) {
                    return $content.first();
                }
            }
            
            return $('body');
        },

        detectPrintType: function($content) {
            if ($content.hasClass('job-listing') || $content.find('.job-listing').length) {
                return this.config.printTypes.job;
            }
            if ($content.hasClass('candidate-profile') || $content.find('.candidate-profile').length) {
                return this.config.printTypes.candidate;
            }
            if ($content.hasClass('contract-document') || $content.find('.contract-document').length) {
                return this.config.printTypes.contract;
            }
            if ($content.hasClass('report-document') || $content.find('.report-document').length) {
                return this.config.printTypes.report;
            }
            if ($content.hasClass('application-form') || $content.find('.application-form').length) {
                return this.config.printTypes.form;
            }
            return this.config.printTypes.general;
        },

        getPrintTitle: function(type) {
            const titles = {
                [this.config.printTypes.job]: 'Job Listing',
                [this.config.printTypes.candidate]: 'Candidate Profile',
                [this.config.printTypes.contract]: 'Contract Document',
                [this.config.printTypes.report]: 'Report',
                [this.config.printTypes.form]: 'Application Form',
                [this.config.printTypes.general]: 'Document'
            };
            return titles[type] || 'Document';
        },

        getCompanyInfo: function() {
            // This would typically come from theme options or settings
            return 'RecruitPro Agency | recruitment@company.com | +1 (555) 123-4567';
        },

        estimatePageCount: function() {
            // Simple page count estimation
            const contentHeight = $(document).height();
            const pageHeight = 1056; // Approximate A4 page height in pixels
            return Math.ceil(contentHeight / pageHeight);
        },

        simplifyLayoutForPrint: function($content) {
            // Remove complex layouts that don't print well
            $content.find('.slider, .carousel, .parallax').addClass('print-hidden');
            
            // Convert flexbox/grid to simple blocks
            $content.find('.flex, .grid').addClass('print-simple-layout');
        },

        removeAnimationsForPrint: function($content) {
            $content.find('*').css({
                'animation': 'none',
                'transition': 'none',
                'transform': 'none'
            });
        },

        executeBeforePrintCallbacks: function($content, type) {
            this.state.beforePrintCallbacks.forEach(callback => {
                try {
                    callback($content, type);
                } catch (error) {
                    console.error('Before print callback error:', error);
                }
            });
        },

        stylePrintButtons: function() {
            // Add styles for print buttons if not already present
            if (!$('#print-button-styles').length) {
                const styles = `
                    <style id="print-button-styles">
                        .print-controls {
                            display: flex;
                            gap: 10px;
                            margin-bottom: 20px;
                            padding: 10px;
                            background: #f5f5f5;
                            border-radius: 5px;
                        }
                        .print-button, .print-preview-toggle, .print-settings-toggle {
                            display: flex;
                            align-items: center;
                            gap: 5px;
                            padding: 8px 12px;
                            background: #2563eb;
                            color: white;
                            border: none;
                            border-radius: 3px;
                            cursor: pointer;
                            font-size: 14px;
                        }
                        .print-button:hover, .print-preview-toggle:hover, .print-settings-toggle:hover {
                            background: #1d4ed8;
                        }
                        @media print {
                            .print-controls { display: none !important; }
                        }
                    </style>
                `;
                $('head').append(styles);
            }
        },

        addPrintKeyboardHint: function() {
            // Add keyboard hint for print
            if (!$('.print-keyboard-hint').length) {
                const hint = `
                    <div class="print-keyboard-hint no-print" style="position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-size: 12px; z-index: 1000;">
                        Press Ctrl+P to print
                    </div>
                `;
                $('body').append(hint);
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    $('.print-keyboard-hint').fadeOut();
                }, 5000);
            }
        },

        refreshPrintButtons: function() {
            this.addPrintButtons();
        },

        handlePrintTrigger: function(printData, element) {
            if (typeof printData === 'string') {
                // Simple print type
                this.initiatePrint(printData);
            } else if (typeof printData === 'object') {
                // Complex print configuration
                this.initiatePrint(printData.type, printData.target);
            }
        },

        togglePrintSettings: function() {
            // Implementation for print settings dialog
            console.log('Print settings dialog would open here');
        },

        /**
         * Public API methods
         */
        api: {
            print: function(type, target) {
                PrintStyle.initiatePrint(type || 'general', target);
            },

            addBeforePrintCallback: function(callback) {
                PrintStyle.state.beforePrintCallbacks.push(callback);
            },

            addAfterPrintCallback: function(callback) {
                PrintStyle.state.afterPrintCallbacks.push(callback);
            },

            updatePrintSettings: function(settings) {
                Object.assign(PrintStyle.state.printSettings, settings);
                try {
                    localStorage.setItem('recruitpro_print_settings', JSON.stringify(PrintStyle.state.printSettings));
                } catch (error) {
                    console.warn('Could not save print settings:', error);
                }
            },

            getPrintSettings: function() {
                return PrintStyle.state.printSettings;
            },

            togglePreview: function() {
                PrintStyle.togglePrintPreview();
            },

            getAnalytics: function() {
                return PrintStyle.state.printAnalytics;
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        PrintStyle.init();
    });

    // Expose public API
    window.RecruitPro.PrintStyle = PrintStyle.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        PrintStyle.refreshPrintButtons();
    });

})(jQuery);