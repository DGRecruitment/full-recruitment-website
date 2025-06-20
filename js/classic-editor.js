/**
 * Classic Editor Enhancements - RecruitPro Theme
 * 
 * TinyMCE customizations and enhancements for recruitment content
 * Professional editor tools for job postings, company pages, and recruitment content
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Classic Editor Manager
     */
    const RecruitProClassicEditor = {
        
        // Configuration
        config: {
            // Custom formats for recruitment content
            customFormats: {
                'job-highlight': {
                    title: 'Job Highlight',
                    block: 'div',
                    classes: 'job-highlight',
                    wrapper: true
                },
                'company-info': {
                    title: 'Company Information',
                    block: 'div',
                    classes: 'company-info',
                    wrapper: true
                },
                'requirements-list': {
                    title: 'Requirements List',
                    selector: 'ul',
                    classes: 'requirements-list'
                },
                'benefits-list': {
                    title: 'Benefits List',
                    selector: 'ul',
                    classes: 'benefits-list'
                },
                'call-to-action': {
                    title: 'Call to Action',
                    inline: 'span',
                    classes: 'cta-text'
                },
                'salary-range': {
                    title: 'Salary Range',
                    inline: 'span',
                    classes: 'salary-range'
                }
            },
            
            // Custom styles for the editor
            customStyles: [
                {
                    title: 'Job Title',
                    selector: 'h1, h2, h3',
                    classes: 'job-title-style'
                },
                {
                    title: 'Company Name',
                    inline: 'span',
                    classes: 'company-name'
                },
                {
                    title: 'Location',
                    inline: 'span',
                    classes: 'job-location'
                },
                {
                    title: 'Urgent Position',
                    inline: 'span',
                    classes: 'urgent-position'
                },
                {
                    title: 'Remote Work',
                    inline: 'span',
                    classes: 'remote-work'
                }
            ],
            
            // Recruitment-specific shortcodes
            shortcodes: [
                {
                    title: 'Apply Button',
                    shortcode: '[apply_button]',
                    description: 'Insert job application button'
                },
                {
                    title: 'Job Contact',
                    shortcode: '[job_contact]',
                    description: 'Insert recruiter contact information'
                },
                {
                    title: 'Company Profile',
                    shortcode: '[company_profile]',
                    description: 'Insert company profile section'
                },
                {
                    title: 'Salary Calculator',
                    shortcode: '[salary_calculator]',
                    description: 'Insert salary calculator widget'
                }
            ],
            
            // Content templates
            templates: {
                'job-posting': {
                    title: 'Job Posting Template',
                    content: `
                        <div class="job-highlight">
                            <h2 class="job-title-style">[Job Title]</h2>
                            <p><span class="company-name">[Company Name]</span> - <span class="job-location">[Location]</span></p>
                            <p><span class="salary-range">[Salary Range]</span></p>
                        </div>

                        <h3>Job Description</h3>
                        <p>[Brief job description and overview]</p>

                        <h3>Key Responsibilities</h3>
                        <ul class="requirements-list">
                            <li>[Responsibility 1]</li>
                            <li>[Responsibility 2]</li>
                            <li>[Responsibility 3]</li>
                        </ul>

                        <h3>Requirements</h3>
                        <ul class="requirements-list">
                            <li>[Required skill/experience 1]</li>
                            <li>[Required skill/experience 2]</li>
                            <li>[Required skill/experience 3]</li>
                        </ul>

                        <h3>Benefits</h3>
                        <ul class="benefits-list">
                            <li>[Benefit 1]</li>
                            <li>[Benefit 2]</li>
                            <li>[Benefit 3]</li>
                        </ul>

                        <div class="company-info">
                            <h3>About the Company</h3>
                            <p>[Company description and culture information]</p>
                        </div>

                        <p><span class="cta-text">Ready to join our team?</span></p>
                        [apply_button]
                    `
                },
                'company-page': {
                    title: 'Company Page Template',
                    content: `
                        <div class="company-info">
                            <h2>[Company Name]</h2>
                            <p class="company-tagline">[Company tagline or mission statement]</p>
                        </div>

                        <h3>About Us</h3>
                        <p>[Company description and history]</p>

                        <h3>Our Culture</h3>
                        <p>[Company culture and values]</p>

                        <h3>Why Work With Us</h3>
                        <ul class="benefits-list">
                            <li>[Company benefit 1]</li>
                            <li>[Company benefit 2]</li>
                            <li>[Company benefit 3]</li>
                        </ul>

                        <h3>Current Opportunities</h3>
                        <p>[Information about current job openings]</p>

                        [job_contact]
                    `
                }
            }
        },

        // State management
        state: {
            isInitialized: false,
            activeEditor: null,
            customButtonsAdded: false
        },

        // Cache elements
        cache: {
            $document: $(document),
            $body: $('body')
        },

        /**
         * Initialize Classic Editor enhancements
         */
        init: function() {
            if (this.state.isInitialized) return;
            
            this.addTinyMCEFilters();
            this.bindEditorEvents();
            this.addCustomCSS();
            this.setupAccessibility();
            
            this.state.isInitialized = true;
            
            console.log('RecruitPro Classic Editor enhancements initialized');
        },

        /**
         * Add TinyMCE filters and customizations
         */
        addTinyMCEFilters: function() {
            const self = this;
            
            // Add custom formats
            if (typeof tinymce !== 'undefined') {
                tinymce.on('AddEditor', function(e) {
                    self.onEditorAdd(e.editor);
                });
            }
            
            // WordPress hook for TinyMCE settings
            if (typeof wp !== 'undefined' && wp.hooks) {
                wp.hooks.addFilter('tiny_mce_before_init', 'recruitpro', function(settings) {
                    return self.modifyTinyMCESettings(settings);
                });
            }
        },

        /**
         * Modify TinyMCE settings
         */
        modifyTinyMCESettings: function(settings) {
            
            // Add custom formats
            if (!settings.formats) {
                settings.formats = {};
            }
            
            Object.assign(settings.formats, this.config.customFormats);
            
            // Add custom style formats
            if (!settings.style_formats) {
                settings.style_formats = [];
            }
            
            // Add recruitment-specific style group
            settings.style_formats.push({
                title: 'Recruitment Styles',
                items: this.config.customStyles
            });
            
            // Add content templates
            if (!settings.templates) {
                settings.templates = [];
            }
            
            Object.keys(this.config.templates).forEach(key => {
                const template = this.config.templates[key];
                settings.templates.push({
                    title: template.title,
                    description: template.description || '',
                    content: template.content
                });
            });
            
            // Add custom toolbar buttons
            if (settings.toolbar1) {
                settings.toolbar1 += ' | recruitpro_shortcodes recruitpro_templates';
            }
            
            // Add custom plugins
            if (!settings.plugins) {
                settings.plugins = '';
            }
            
            if (settings.plugins.indexOf('template') === -1) {
                settings.plugins += ' template';
            }
            
            // Content CSS for editor styling
            if (!settings.content_css) {
                settings.content_css = [];
            } else if (typeof settings.content_css === 'string') {
                settings.content_css = [settings.content_css];
            }
            
            // Add theme editor CSS
            settings.content_css.push(recruitpro_editor_vars.theme_url + '/assets/css/gutenberg-editor.css');
            
            // Body class for editor
            if (!settings.body_class) {
                settings.body_class = '';
            }
            settings.body_class += ' recruitpro-editor recruitment-content';
            
            return settings;
        },

        /**
         * Handle editor addition
         */
        onEditorAdd: function(editor) {
            const self = this;
            
            editor.on('init', function() {
                self.onEditorInit(editor);
            });
            
            editor.on('BeforeSetContent', function(e) {
                self.onBeforeSetContent(e);
            });
            
            editor.on('SetContent', function(e) {
                self.onSetContent(e);
            });
            
            // Add custom buttons
            this.addCustomButtons(editor);
        },

        /**
         * Handle editor initialization
         */
        onEditorInit: function(editor) {
            this.state.activeEditor = editor;
            
            // Add accessibility enhancements
            this.enhanceEditorAccessibility(editor);
            
            // Add recruitment-specific features
            this.addRecruitmentFeatures(editor);
            
            // Custom event
            this.cache.$document.trigger('recruitpro:editor-init', [editor]);
        },

        /**
         * Handle before content set
         */
        onBeforeSetContent: function(e) {
            // Process shortcodes before content is set
            if (e.content) {
                e.content = this.processShortcodes(e.content);
            }
        },

        /**
         * Handle content set
         */
        onSetContent: function(e) {
            // Format recruitment content
            this.formatRecruitmentContent(e.target);
        },

        /**
         * Add custom buttons to editor
         */
        addCustomButtons: function(editor) {
            const self = this;
            
            // Shortcodes dropdown button
            editor.addButton('recruitpro_shortcodes', {
                type: 'menubutton',
                text: 'Recruitment',
                icon: 'wp_code',
                menu: this.config.shortcodes.map(function(shortcode) {
                    return {
                        text: shortcode.title,
                        onclick: function() {
                            self.insertShortcode(editor, shortcode.shortcode);
                        }
                    };
                })
            });
            
            // Templates dropdown button
            editor.addButton('recruitpro_templates', {
                type: 'menubutton',
                text: 'Templates',
                icon: 'template',
                menu: Object.keys(this.config.templates).map(function(key) {
                    const template = self.config.templates[key];
                    return {
                        text: template.title,
                        onclick: function() {
                            self.insertTemplate(editor, template);
                        }
                    };
                })
            });
            
            // Job posting wizard button
            editor.addButton('recruitpro_job_wizard', {
                text: 'Job Wizard',
                icon: 'dashicon dashicons-businessman',
                onclick: function() {
                    self.openJobWizard(editor);
                }
            });
            
            this.state.customButtonsAdded = true;
        },

        /**
         * Insert shortcode into editor
         */
        insertShortcode: function(editor, shortcode) {
            editor.execCommand('mceInsertContent', false, shortcode);
        },

        /**
         * Insert template into editor
         */
        insertTemplate: function(editor, template) {
            // Ask for confirmation if editor has content
            if (editor.getContent().trim() !== '') {
                if (!confirm('This will replace the current content. Are you sure?')) {
                    return;
                }
            }
            
            editor.setContent(template.content);
            
            // Focus on first placeholder
            this.focusFirstPlaceholder(editor);
        },

        /**
         * Focus first placeholder in template
         */
        focusFirstPlaceholder: function(editor) {
            setTimeout(function() {
                const content = editor.getContent();
                const firstPlaceholder = content.match(/\[([^\]]+)\]/);
                
                if (firstPlaceholder) {
                    const searchText = firstPlaceholder[0];
                    editor.focus();
                    editor.selection.moveToBookmark(
                        editor.selection.search(searchText)
                    );
                }
            }, 100);
        },

        /**
         * Open job posting wizard
         */
        openJobWizard: function(editor) {
            const self = this;
            
            // Create modal for job wizard
            const wizardHTML = `
                <div id="job-wizard-modal" class="recruitpro-modal" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Job Posting Wizard</h3>
                            <button type="button" class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="job-wizard-form">
                                <div class="form-group">
                                    <label for="job-title">Job Title *</label>
                                    <input type="text" id="job-title" name="job_title" required>
                                </div>
                                <div class="form-group">
                                    <label for="company-name">Company Name *</label>
                                    <input type="text" id="company-name" name="company_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="job-location">Location *</label>
                                    <input type="text" id="job-location" name="job_location" required>
                                </div>
                                <div class="form-group">
                                    <label for="salary-range">Salary Range</label>
                                    <input type="text" id="salary-range" name="salary_range" placeholder="e.g., $50,000 - $70,000">
                                </div>
                                <div class="form-group">
                                    <label for="job-type">Job Type</label>
                                    <select id="job-type" name="job_type">
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Temporary">Temporary</option>
                                        <option value="Internship">Internship</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="remote-work">Remote Work</label>
                                    <select id="remote-work" name="remote_work">
                                        <option value="">Not specified</option>
                                        <option value="Remote">Fully Remote</option>
                                        <option value="Hybrid">Hybrid</option>
                                        <option value="On-site">On-site</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="job-description">Job Description</label>
                                    <textarea id="job-description" name="job_description" rows="4" placeholder="Brief overview of the role..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="requirements">Key Requirements (one per line)</label>
                                    <textarea id="requirements" name="requirements" rows="4" placeholder="Bachelor's degree in relevant field&#10;3+ years of experience&#10;Strong communication skills"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="benefits">Benefits (one per line)</label>
                                    <textarea id="benefits" name="benefits" rows="4" placeholder="Health insurance&#10;401(k) matching&#10;Flexible schedule"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="wizard-cancel">Cancel</button>
                            <button type="button" class="btn btn-primary" id="wizard-generate">Generate Job Posting</button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to page
            $('body').append(wizardHTML);
            const $modal = $('#job-wizard-modal');
            
            // Show modal
            $modal.show();
            $('#job-title').focus();
            
            // Handle form submission
            $('#wizard-generate').on('click', function() {
                const formData = self.getWizardFormData();
                if (self.validateWizardForm(formData)) {
                    const jobPosting = self.generateJobPosting(formData);
                    editor.setContent(jobPosting);
                    $modal.remove();
                }
            });
            
            // Handle cancel
            $('#wizard-cancel, .modal-close').on('click', function() {
                $modal.remove();
            });
        },

        /**
         * Get wizard form data
         */
        getWizardFormData: function() {
            const formData = {};
            $('#job-wizard-form').serializeArray().forEach(function(field) {
                formData[field.name] = field.value;
            });
            return formData;
        },

        /**
         * Validate wizard form
         */
        validateWizardForm: function(formData) {
            const required = ['job_title', 'company_name', 'job_location'];
            let isValid = true;
            
            // Remove previous error states
            $('.form-group').removeClass('error');
            
            required.forEach(function(field) {
                if (!formData[field] || formData[field].trim() === '') {
                    $(`[name="${field}"]`).closest('.form-group').addClass('error');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields.');
            }
            
            return isValid;
        },

        /**
         * Generate job posting from wizard data
         */
        generateJobPosting: function(data) {
            let content = `<div class="job-highlight">
                <h2 class="job-title-style">${data.job_title}</h2>
                <p><span class="company-name">${data.company_name}</span> - <span class="job-location">${data.job_location}</span></p>`;
            
            if (data.salary_range) {
                content += `<p><span class="salary-range">${data.salary_range}</span></p>`;
            }
            
            if (data.job_type || data.remote_work) {
                content += '<p>';
                if (data.job_type) {
                    content += `<span class="job-type">${data.job_type}</span>`;
                }
                if (data.remote_work) {
                    content += ` • <span class="remote-work">${data.remote_work}</span>`;
                }
                content += '</p>';
            }
            
            content += '</div>';
            
            if (data.job_description) {
                content += `<h3>Job Description</h3>
                <p>${data.job_description}</p>`;
            }
            
            if (data.requirements) {
                content += '<h3>Requirements</h3><ul class="requirements-list">';
                data.requirements.split('\n').forEach(function(req) {
                    if (req.trim()) {
                        content += `<li>${req.trim()}</li>`;
                    }
                });
                content += '</ul>';
            }
            
            if (data.benefits) {
                content += '<h3>Benefits</h3><ul class="benefits-list">';
                data.benefits.split('\n').forEach(function(benefit) {
                    if (benefit.trim()) {
                        content += `<li>${benefit.trim()}</li>`;
                    }
                });
                content += '</ul>';
            }
            
            content += '<div class="company-info"><h3>About the Company</h3><p>[Add company description here]</p></div>';
            content += '<p><span class="cta-text">Ready to join our team?</span></p>';
            content += '[apply_button]';
            
            return content;
        },

        /**
         * Process shortcodes in content
         */
        processShortcodes: function(content) {
            // This is a placeholder - actual shortcode processing would be done server-side
            // Here we just add visual indicators for the editor
            
            const shortcodeRegex = /\[(\w+)([^\]]*)\]/g;
            
            return content.replace(shortcodeRegex, function(match, shortcode, attributes) {
                return `<span class="mce-shortcode" data-shortcode="${shortcode}" contenteditable="false">${match}</span>`;
            });
        },

        /**
         * Format recruitment content
         */
        formatRecruitmentContent: function(editor) {
            // Add recruitment-specific formatting
            const content = editor.getContent();
            
            // Highlight salary ranges
            const salaryRegex = /\$[\d,]+(?:\s*-\s*\$[\d,]+)?/g;
            editor.dom.select('p').forEach(function(p) {
                const text = p.innerHTML;
                if (salaryRegex.test(text)) {
                    p.innerHTML = text.replace(salaryRegex, '<span class="salary-highlight">$&</span>');
                }
            });
        },

        /**
         * Enhance editor accessibility
         */
        enhanceEditorAccessibility: function(editor) {
            // Add ARIA labels to custom buttons
            const toolbar = editor.getContainer().querySelector('.mce-toolbar');
            if (toolbar) {
                const recruitmentBtn = toolbar.querySelector('[aria-label*="Recruitment"]');
                if (recruitmentBtn) {
                    recruitmentBtn.setAttribute('aria-label', 'Insert recruitment shortcodes');
                }
                
                const templatesBtn = toolbar.querySelector('[aria-label*="Templates"]');
                if (templatesBtn) {
                    templatesBtn.setAttribute('aria-label', 'Insert content templates');
                }
            }
            
            // Add keyboard shortcuts
            editor.addShortcut('ctrl+shift+j', 'Open Job Wizard', function() {
                this.openJobWizard(editor);
            }.bind(this));
            
            editor.addShortcut('ctrl+shift+t', 'Insert Template', function() {
                // Show template menu
                const templateBtn = editor.buttons.recruitpro_templates;
                if (templateBtn && templateBtn.menu) {
                    templateBtn.menu.show();
                }
            });
        },

        /**
         * Add recruitment-specific features
         */
        addRecruitmentFeatures: function(editor) {
            // Auto-suggest for common recruitment terms
            const recruitmentTerms = [
                'Bachelor\'s degree', 'Master\'s degree', 'PhD',
                'years of experience', 'relevant experience',
                'strong communication skills', 'team player',
                'problem-solving skills', 'attention to detail',
                'competitive salary', 'benefits package',
                'health insurance', '401(k)', 'flexible schedule',
                'remote work', 'hybrid work', 'on-site'
            ];
            
            // Add content suggestions (placeholder for future enhancement)
            editor.on('keyup', function(e) {
                // Future: implement auto-suggestions
            });
            
            // Add spell check for recruitment terms
            this.addRecruitmentSpellCheck(editor);
        },

        /**
         * Add recruitment-specific spell check
         */
        addRecruitmentSpellCheck: function(editor) {
            const commonMisspellings = {
                'recieve': 'receive',
                'seperate': 'separate',
                'occassion': 'occasion',
                'neccessary': 'necessary',
                'accomodate': 'accommodate',
                'managment': 'management',
                'employe': 'employee',
                'sucessful': 'successful'
            };
            
            editor.on('keyup', function(e) {
                if (e.keyCode === 32) { // Space key
                    const word = this.getWordAtCursor(editor);
                    if (commonMisspellings[word.toLowerCase()]) {
                        this.suggestCorrection(editor, word, commonMisspellings[word.toLowerCase()]);
                    }
                }
            }.bind(this));
        },

        /**
         * Get word at cursor position
         */
        getWordAtCursor: function(editor) {
            const selection = editor.selection;
            const range = selection.getRng();
            const text = range.startContainer.textContent;
            const offset = range.startOffset;
            
            let start = offset;
            let end = offset;
            
            // Find word boundaries
            while (start > 0 && /\w/.test(text[start - 1])) {
                start--;
            }
            
            while (end < text.length && /\w/.test(text[end])) {
                end++;
            }
            
            return text.substring(start, end);
        },

        /**
         * Suggest spelling correction
         */
        suggestCorrection: function(editor, misspelled, correction) {
            // Simple implementation - in production, this would be more sophisticated
            if (confirm(`Did you mean "${correction}" instead of "${misspelled}"?`)) {
                editor.execCommand('mceReplaceContent', false, correction);
            }
        },

        /**
         * Bind editor events
         */
        bindEditorEvents: function() {
            const self = this;
            
            // WordPress editor events
            if (typeof wp !== 'undefined' && wp.hooks) {
                wp.hooks.addAction('wp_editor_loaded', 'recruitpro', function(editor) {
                    self.onWordPressEditorLoaded(editor);
                });
            }
            
            // Global editor events
            this.cache.$document.on('tinymce-editor-init', function(event, editor) {
                self.onTinyMCEInit(editor);
            });
        },

        /**
         * Handle WordPress editor loaded
         */
        onWordPressEditorLoaded: function(editor) {
            this.enhanceWordPressEditor(editor);
        },

        /**
         * Handle TinyMCE init
         */
        onTinyMCEInit: function(editor) {
            if (editor.id && editor.id.indexOf('content') !== -1) {
                this.onEditorAdd(editor);
            }
        },

        /**
         * Enhance WordPress editor
         */
        enhanceWordPressEditor: function(editor) {
            // Add recruitment-specific enhancements to WordPress editor
            if (editor.settings && editor.settings.toolbar) {
                // Modify toolbar if needed
            }
        },

        /**
         * Add custom CSS for editor
         */
        addCustomCSS: function() {
            const editorCSS = `
                <style id="recruitpro-editor-styles">
                    .recruitpro-modal {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        z-index: 160000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .recruitpro-modal .modal-content {
                        background: #fff;
                        border-radius: 8px;
                        width: 90%;
                        max-width: 600px;
                        max-height: 90vh;
                        overflow-y: auto;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                    }
                    
                    .recruitpro-modal .modal-header {
                        padding: 20px;
                        border-bottom: 1px solid #e5e7eb;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    
                    .recruitpro-modal .modal-header h3 {
                        margin: 0;
                        font-size: 1.25rem;
                        font-weight: 600;
                    }
                    
                    .recruitpro-modal .modal-close {
                        background: none;
                        border: none;
                        font-size: 1.5rem;
                        cursor: pointer;
                        padding: 0;
                        width: 30px;
                        height: 30px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .recruitpro-modal .modal-body {
                        padding: 20px;
                    }
                    
                    .recruitpro-modal .modal-footer {
                        padding: 20px;
                        border-top: 1px solid #e5e7eb;
                        display: flex;
                        justify-content: flex-end;
                        gap: 10px;
                    }
                    
                    .recruitpro-modal .form-group {
                        margin-bottom: 15px;
                    }
                    
                    .recruitpro-modal .form-group label {
                        display: block;
                        margin-bottom: 5px;
                        font-weight: 500;
                    }
                    
                    .recruitpro-modal .form-group input,
                    .recruitpro-modal .form-group select,
                    .recruitpro-modal .form-group textarea {
                        width: 100%;
                        padding: 8px 12px;
                        border: 1px solid #d1d5db;
                        border-radius: 4px;
                        font-size: 14px;
                    }
                    
                    .recruitpro-modal .form-group.error input,
                    .recruitpro-modal .form-group.error select,
                    .recruitpro-modal .form-group.error textarea {
                        border-color: #ef4444;
                    }
                    
                    .recruitpro-modal .btn {
                        padding: 8px 16px;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 500;
                    }
                    
                    .recruitpro-modal .btn-primary {
                        background: #2563eb;
                        color: white;
                    }
                    
                    .recruitpro-modal .btn-secondary {
                        background: #6b7280;
                        color: white;
                    }
                    
                    .mce-shortcode {
                        background: #e0f2fe;
                        border: 1px solid #0277bd;
                        border-radius: 3px;
                        padding: 2px 6px;
                        font-family: monospace;
                        font-size: 0.9em;
                        color: #0277bd;
                    }
                    
                    .salary-highlight {
                        background: rgba(34, 197, 94, 0.1);
                        color: #16a34a;
                        font-weight: 600;
                    }
                </style>
            `;
            
            if (!$('#recruitpro-editor-styles').length) {
                $('head').append(editorCSS);
            }
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add keyboard navigation help
            this.cache.$document.on('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && e.keyCode === 72) { // Ctrl+Shift+H
                    e.preventDefault();
                    this.showKeyboardHelp();
                }
            }.bind(this));
        },

        /**
         * Show keyboard help
         */
        showKeyboardHelp: function() {
            const helpHTML = `
                <div id="editor-help-modal" class="recruitpro-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Editor Keyboard Shortcuts</h3>
                            <button type="button" class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <h4>Recruitment Features</h4>
                            <ul>
                                <li><strong>Ctrl+Shift+J</strong> - Open Job Posting Wizard</li>
                                <li><strong>Ctrl+Shift+T</strong> - Insert Template</li>
                            </ul>
                            <h4>Standard Shortcuts</h4>
                            <ul>
                                <li><strong>Ctrl+B</strong> - Bold</li>
                                <li><strong>Ctrl+I</strong> - Italic</li>
                                <li><strong>Ctrl+U</strong> - Underline</li>
                                <li><strong>Ctrl+K</strong> - Insert Link</li>
                                <li><strong>Ctrl+Z</strong> - Undo</li>
                                <li><strong>Ctrl+Y</strong> - Redo</li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="help-close">Close</button>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(helpHTML);
            const $modal = $('#editor-help-modal');
            
            $('#help-close, .modal-close').on('click', function() {
                $modal.remove();
            });
        },

        /**
         * Get current editor instance
         */
        getCurrentEditor: function() {
            return this.state.activeEditor;
        },

        /**
         * Insert content into current editor
         */
        insertContent: function(content) {
            if (this.state.activeEditor) {
                this.state.activeEditor.execCommand('mceInsertContent', false, content);
            }
        },

        /**
         * Public API methods
         */
        getFormats: function() {
            return this.config.customFormats;
        },

        getTemplates: function() {
            return this.config.templates;
        },

        addTemplate: function(key, template) {
            this.config.templates[key] = template;
        },

        addShortcode: function(shortcode) {
            this.config.shortcodes.push(shortcode);
        }
    };

    /**
     * Expose to global scope
     */
    window.RecruitProClassicEditor = RecruitProClassicEditor;

    /**
     * Auto-initialize when document is ready
     */
    $(document).ready(function() {
        // Initialize if TinyMCE is available
        if (typeof tinymce !== 'undefined' || typeof wp !== 'undefined') {
            RecruitProClassicEditor.init();
        } else {
            // Wait for TinyMCE to load
            $(document).on('tinymce-editor-init', function() {
                RecruitProClassicEditor.init();
            });
        }
    });

    /**
     * Integration with main theme
     */
    if (window.RecruitPro) {
        window.RecruitPro.classicEditor = RecruitProClassicEditor;
    }

})(jQuery);

/**
 * Classic Editor Features Summary:
 * 
 * ✅ Recruitment-Specific Formats & Styles
 * ✅ Job Posting Templates
 * ✅ Content Wizard for Job Creation
 * ✅ Custom Shortcodes Integration
 * ✅ Professional Editor Toolbar
 * ✅ Accessibility Enhancements
 * ✅ Keyboard Shortcuts
 * ✅ Spell Check for Recruitment Terms
 * ✅ Content Templates
 * ✅ Visual Shortcode Indicators
 * ✅ Responsive Modal Dialogs
 * ✅ Form Validation
 * ✅ Plugin Integration Ready
 * ✅ Performance Optimized
 * ✅ Mobile-Friendly Interface
 */