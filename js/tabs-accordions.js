/**
 * RecruitPro Theme - Tabs & Accordions UI Components
 * 
 * Handles tab navigation and accordion functionality for content organization.
 * Includes accessibility features, keyboard navigation, and responsive behavior.
 * 
 * @package RecruitPro
 * @subpackage Assets/JS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Tabs & Accordions Module
    const RecruitProUIComponents = {
        
        /**
         * Initialize UI components
         */
        init: function() {
            this.initTabs();
            this.initAccordions();
            this.bindEvents();
            this.handleResponsive();
        },

        /**
         * Initialize tab functionality
         */
        initTabs: function() {
            $('.rp-tabs').each(function() {
                const $tabContainer = $(this);
                const $tabList = $tabContainer.find('.rp-tabs-list');
                const $tabButtons = $tabList.find('.rp-tab-button');
                const $tabPanels = $tabContainer.find('.rp-tab-panel');
                
                // Setup ARIA attributes
                RecruitProUIComponents.setupTabsAria($tabContainer, $tabList, $tabButtons, $tabPanels);
                
                // Show first tab by default
                const activeTab = $tabContainer.data('active-tab') || 0;
                RecruitProUIComponents.showTab($tabContainer, activeTab);
                
                // Handle URL hash for deep linking
                const hash = window.location.hash;
                if (hash) {
                    const $targetTab = $tabButtons.filter(`[data-tab="${hash.substring(1)}"]`);
                    if ($targetTab.length) {
                        const tabIndex = $targetTab.index();
                        RecruitProUIComponents.showTab($tabContainer, tabIndex);
                    }
                }
            });
        },

        /**
         * Setup ARIA attributes for tabs
         */
        setupTabsAria: function($container, $tabList, $buttons, $panels) {
            const tabsId = $container.attr('id') || `rp-tabs-${Date.now()}`;
            $container.attr('id', tabsId);
            
            // Tab list setup
            $tabList.attr({
                'role': 'tablist',
                'aria-label': $container.data('aria-label') || 'Content tabs'
            });
            
            // Setup each tab and panel
            $buttons.each(function(index) {
                const $button = $(this);
                const $panel = $panels.eq(index);
                const tabId = `${tabsId}-tab-${index}`;
                const panelId = `${tabsId}-panel-${index}`;
                
                // Tab button setup
                $button.attr({
                    'role': 'tab',
                    'id': tabId,
                    'aria-controls': panelId,
                    'aria-selected': 'false',
                    'tabindex': '-1'
                });
                
                // Panel setup
                $panel.attr({
                    'role': 'tabpanel',
                    'id': panelId,
                    'aria-labelledby': tabId,
                    'tabindex': '0'
                });
            });
        },

        /**
         * Show specific tab
         */
        showTab: function($container, index) {
            const $tabButtons = $container.find('.rp-tab-button');
            const $tabPanels = $container.find('.rp-tab-panel');
            
            // Update buttons
            $tabButtons.removeClass('active').attr({
                'aria-selected': 'false',
                'tabindex': '-1'
            });
            
            const $activeButton = $tabButtons.eq(index);
            $activeButton.addClass('active').attr({
                'aria-selected': 'true',
                'tabindex': '0'
            });
            
            // Update panels
            $tabPanels.removeClass('active').attr('hidden', true);
            const $activePanel = $tabPanels.eq(index);
            $activePanel.addClass('active').removeAttr('hidden');
            
            // Trigger custom event
            $container.trigger('tabChanged', [index, $activeButton, $activePanel]);
            
            // Update URL hash if data attribute is set
            const tabHash = $activeButton.data('tab');
            if (tabHash && $container.data('update-url')) {
                history.replaceState(null, null, `#${tabHash}`);
            }
            
            // Focus management
            $activeButton.focus();
        },

        /**
         * Initialize accordion functionality
         */
        initAccordions: function() {
            $('.rp-accordion').each(function() {
                const $accordion = $(this);
                const $items = $accordion.find('.rp-accordion-item');
                
                // Setup ARIA attributes
                RecruitProUIComponents.setupAccordionAria($accordion, $items);
                
                // Handle initial state
                const allowMultiple = $accordion.data('allow-multiple') !== false;
                const openFirst = $accordion.data('open-first') !== false;
                
                if (openFirst && !$items.filter('.active').length) {
                    $items.first().addClass('active');
                    RecruitProUIComponents.openAccordionItem($items.first());
                }
                
                // Open items marked as active
                $items.filter('.active').each(function() {
                    RecruitProUIComponents.openAccordionItem($(this));
                });
            });
        },

        /**
         * Setup ARIA attributes for accordion
         */
        setupAccordionAria: function($accordion, $items) {
            const accordionId = $accordion.attr('id') || `rp-accordion-${Date.now()}`;
            $accordion.attr('id', accordionId);
            
            $items.each(function(index) {
                const $item = $(this);
                const $header = $item.find('.rp-accordion-header');
                const $button = $header.find('.rp-accordion-button');
                const $panel = $item.find('.rp-accordion-panel');
                
                const headerId = `${accordionId}-header-${index}`;
                const panelId = `${accordionId}-panel-${index}`;
                
                // Header setup
                $header.attr('id', headerId);
                
                // Button setup
                $button.attr({
                    'aria-expanded': 'false',
                    'aria-controls': panelId,
                    'id': `${headerId}-button`
                });
                
                // Panel setup
                $panel.attr({
                    'role': 'region',
                    'id': panelId,
                    'aria-labelledby': headerId
                });
            });
        },

        /**
         * Open accordion item
         */
        openAccordionItem: function($item) {
            const $button = $item.find('.rp-accordion-button');
            const $panel = $item.find('.rp-accordion-panel');
            
            $item.addClass('active');
            $button.attr('aria-expanded', 'true');
            $panel.slideDown({
                duration: 300,
                easing: 'easeInOutCubic',
                complete: function() {
                    // Trigger custom event
                    $item.trigger('accordionOpened', [$item, $panel]);
                }
            });
        },

        /**
         * Close accordion item
         */
        closeAccordionItem: function($item) {
            const $button = $item.find('.rp-accordion-button');
            const $panel = $item.find('.rp-accordion-panel');
            
            $item.removeClass('active');
            $button.attr('aria-expanded', 'false');
            $panel.slideUp({
                duration: 300,
                easing: 'easeInOutCubic',
                complete: function() {
                    // Trigger custom event
                    $item.trigger('accordionClosed', [$item, $panel]);
                }
            });
        },

        /**
         * Toggle accordion item
         */
        toggleAccordionItem: function($item) {
            const isOpen = $item.hasClass('active');
            const $accordion = $item.closest('.rp-accordion');
            const allowMultiple = $accordion.data('allow-multiple') !== false;
            
            if (isOpen) {
                this.closeAccordionItem($item);
            } else {
                // Close other items if multiple not allowed
                if (!allowMultiple) {
                    $accordion.find('.rp-accordion-item.active').each(function() {
                        RecruitProUIComponents.closeAccordionItem($(this));
                    });
                }
                
                this.openAccordionItem($item);
            }
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Tab click events
            $(document).on('click', '.rp-tab-button', function(e) {
                e.preventDefault();
                const $button = $(this);
                const $container = $button.closest('.rp-tabs');
                const index = $button.index();
                
                RecruitProUIComponents.showTab($container, index);
            });

            // Tab keyboard navigation
            $(document).on('keydown', '.rp-tab-button', function(e) {
                RecruitProUIComponents.handleTabKeydown(e);
            });

            // Accordion click events
            $(document).on('click', '.rp-accordion-button', function(e) {
                e.preventDefault();
                const $item = $(this).closest('.rp-accordion-item');
                RecruitProUIComponents.toggleAccordionItem($item);
            });

            // Accordion keyboard navigation
            $(document).on('keydown', '.rp-accordion-button', function(e) {
                RecruitProUIComponents.handleAccordionKeydown(e);
            });

            // Window resize handling
            $(window).on('resize', function() {
                RecruitProUIComponents.handleResponsive();
            });

            // Handle initial page load with hash
            $(window).on('load', function() {
                const hash = window.location.hash;
                if (hash) {
                    const $targetTab = $(`.rp-tab-button[data-tab="${hash.substring(1)}"]`);
                    if ($targetTab.length) {
                        const $container = $targetTab.closest('.rp-tabs');
                        const index = $targetTab.index();
                        RecruitProUIComponents.showTab($container, index);
                    }
                }
            });
        },

        /**
         * Handle tab keyboard navigation
         */
        handleTabKeydown: function(e) {
            const $current = $(e.target);
            const $container = $current.closest('.rp-tabs');
            const $buttons = $container.find('.rp-tab-button');
            const currentIndex = $buttons.index($current);
            let newIndex = currentIndex;

            switch (e.key) {
                case 'ArrowLeft':
                case 'ArrowUp':
                    e.preventDefault();
                    newIndex = currentIndex > 0 ? currentIndex - 1 : $buttons.length - 1;
                    break;
                
                case 'ArrowRight':
                case 'ArrowDown':
                    e.preventDefault();
                    newIndex = currentIndex < $buttons.length - 1 ? currentIndex + 1 : 0;
                    break;
                
                case 'Home':
                    e.preventDefault();
                    newIndex = 0;
                    break;
                
                case 'End':
                    e.preventDefault();
                    newIndex = $buttons.length - 1;
                    break;
                
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    this.showTab($container, currentIndex);
                    return;
            }

            if (newIndex !== currentIndex) {
                $buttons.eq(newIndex).focus();
                
                // Auto-switch on arrow keys (optional behavior)
                if ($container.data('auto-switch')) {
                    this.showTab($container, newIndex);
                }
            }
        },

        /**
         * Handle accordion keyboard navigation
         */
        handleAccordionKeydown: function(e) {
            const $current = $(e.target);
            const $accordion = $current.closest('.rp-accordion');
            const $buttons = $accordion.find('.rp-accordion-button');
            const currentIndex = $buttons.index($current);

            switch (e.key) {
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : $buttons.length - 1;
                    $buttons.eq(prevIndex).focus();
                    break;
                
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = currentIndex < $buttons.length - 1 ? currentIndex + 1 : 0;
                    $buttons.eq(nextIndex).focus();
                    break;
                
                case 'Home':
                    e.preventDefault();
                    $buttons.first().focus();
                    break;
                
                case 'End':
                    e.preventDefault();
                    $buttons.last().focus();
                    break;
                
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    const $item = $current.closest('.rp-accordion-item');
                    RecruitProUIComponents.toggleAccordionItem($item);
                    break;
            }
        },

        /**
         * Handle responsive behavior
         */
        handleResponsive: function() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            
            $('.rp-tabs').each(function() {
                const $container = $(this);
                const mobileMode = $container.data('mobile-mode') || 'accordion';
                
                if (isMobile && mobileMode === 'accordion') {
                    // Convert tabs to accordion on mobile
                    RecruitProUIComponents.convertTabsToAccordion($container);
                } else {
                    // Use tab layout
                    RecruitProUIComponents.convertAccordionToTabs($container);
                }
            });

            // Handle accordion responsive behavior
            $('.rp-accordion').each(function() {
                const $accordion = $(this);
                const stackOnMobile = $accordion.data('stack-mobile') !== false;
                
                if (isMobile && stackOnMobile) {
                    $accordion.addClass('mobile-stacked');
                } else {
                    $accordion.removeClass('mobile-stacked');
                }
            });
        },

        /**
         * Convert tabs to accordion for mobile
         */
        convertTabsToAccordion: function($container) {
            $container.addClass('mobile-accordion');
            
            const $buttons = $container.find('.rp-tab-button');
            const $panels = $container.find('.rp-tab-panel');
            
            $buttons.each(function(index) {
                const $button = $(this);
                const $panel = $panels.eq(index);
                const isActive = $button.hasClass('active');
                
                // Update ARIA for accordion behavior
                $button.attr('aria-expanded', isActive ? 'true' : 'false');
                
                if (!isActive) {
                    $panel.hide();
                }
            });
        },

        /**
         * Convert accordion back to tabs for desktop
         */
        convertAccordionToTabs: function($container) {
            $container.removeClass('mobile-accordion');
            
            const $buttons = $container.find('.rp-tab-button');
            const $panels = $container.find('.rp-tab-panel');
            
            // Reset to tab behavior
            $panels.show();
            
            $buttons.each(function() {
                const $button = $(this);
                const isActive = $button.hasClass('active');
                
                $button.attr('aria-expanded', null);
            });
        },

        /**
         * Public API methods
         */
        openTab: function(containerId, index) {
            const $container = $(`#${containerId}`);
            if ($container.length) {
                this.showTab($container, index);
            }
        },

        openAccordion: function(itemId) {
            const $item = $(`#${itemId}`);
            if ($item.length) {
                this.openAccordionItem($item);
            }
        },

        closeAccordion: function(itemId) {
            const $item = $(`#${itemId}`);
            if ($item.length) {
                this.closeAccordionItem($item);
            }
        },

        toggleAccordion: function(itemId) {
            const $item = $(`#${itemId}`);
            if ($item.length) {
                this.toggleAccordionItem($item);
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RecruitProUIComponents.init();
    });

    // Expose to global scope for external use
    window.RecruitProUIComponents = RecruitProUIComponents;

    // jQuery plugin interface
    $.fn.recruitProTabs = function(options) {
        const settings = $.extend({
            activeTab: 0,
            updateUrl: false,
            autoSwitch: false,
            mobileMode: 'accordion'
        }, options);

        return this.each(function() {
            const $container = $(this);
            
            // Apply settings as data attributes
            Object.keys(settings).forEach(key => {
                $container.data(key.replace(/([A-Z])/g, '-$1').toLowerCase(), settings[key]);
            });
            
            // Initialize if not already done
            if (!$container.hasClass('rp-tabs-initialized')) {
                $container.addClass('rp-tabs-initialized');
                RecruitProUIComponents.initTabs();
            }
        });
    };

    $.fn.recruitProAccordion = function(options) {
        const settings = $.extend({
            allowMultiple: true,
            openFirst: false,
            stackMobile: true
        }, options);

        return this.each(function() {
            const $container = $(this);
            
            // Apply settings as data attributes
            Object.keys(settings).forEach(key => {
                $container.data(key.replace(/([A-Z])/g, '-$1').toLowerCase(), settings[key]);
            });
            
            // Initialize if not already done
            if (!$container.hasClass('rp-accordion-initialized')) {
                $container.addClass('rp-accordion-initialized');
                RecruitProUIComponents.initAccordions();
            }
        });
    };

})(jQuery);

/**
 * CSS custom easing function for smooth animations
 */
jQuery.easing.easeInOutCubic = function(x, t, b, c, d) {
    if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
    return c / 2 * ((t -= 2) * t * t + 2) + b;
};

/**
 * Utility functions for external use
 */
window.RecruitProTabsUtils = {
    
    /**
     * Create tabs HTML structure
     */
    generateTabsHTML: function(options) {
        const defaults = {
            id: `rp-tabs-${Date.now()}`,
            tabs: [],
            activeTab: 0,
            updateUrl: false,
            mobileMode: 'accordion'
        };
        
        const settings = Object.assign(defaults, options);
        
        let tabsHTML = `<div class="rp-tabs" id="${settings.id}" data-active-tab="${settings.activeTab}" data-update-url="${settings.updateUrl}" data-mobile-mode="${settings.mobileMode}">`;
        
        // Tab list
        tabsHTML += '<div class="rp-tabs-list" role="tablist">';
        settings.tabs.forEach((tab, index) => {
            const isActive = index === settings.activeTab;
            tabsHTML += `
                <button type="button" class="rp-tab-button${isActive ? ' active' : ''}" 
                        data-tab="${tab.id || tab.title.toLowerCase().replace(/\s+/g, '-')}">
                    ${tab.icon ? `<i class="${tab.icon}"></i>` : ''}
                    <span>${tab.title}</span>
                </button>
            `;
        });
        tabsHTML += '</div>';
        
        // Tab panels
        tabsHTML += '<div class="rp-tabs-content">';
        settings.tabs.forEach((tab, index) => {
            const isActive = index === settings.activeTab;
            tabsHTML += `
                <div class="rp-tab-panel${isActive ? ' active' : ''}" ${!isActive ? 'hidden' : ''}>
                    ${tab.content}
                </div>
            `;
        });
        tabsHTML += '</div>';
        
        tabsHTML += '</div>';
        
        return tabsHTML;
    },

    /**
     * Create accordion HTML structure
     */
    generateAccordionHTML: function(options) {
        const defaults = {
            id: `rp-accordion-${Date.now()}`,
            items: [],
            allowMultiple: true,
            openFirst: false
        };
        
        const settings = Object.assign(defaults, options);
        
        let accordionHTML = `<div class="rp-accordion" id="${settings.id}" data-allow-multiple="${settings.allowMultiple}" data-open-first="${settings.openFirst}">`;
        
        settings.items.forEach((item, index) => {
            const isOpen = (index === 0 && settings.openFirst) || item.open;
            accordionHTML += `
                <div class="rp-accordion-item${isOpen ? ' active' : ''}">
                    <div class="rp-accordion-header">
                        <button type="button" class="rp-accordion-button">
                            ${item.icon ? `<i class="${item.icon}"></i>` : ''}
                            <span>${item.title}</span>
                            <i class="rp-accordion-arrow fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="rp-accordion-panel"${isOpen ? '' : ' style="display: none;"'}>
                        ${item.content}
                    </div>
                </div>
            `;
        });
        
        accordionHTML += '</div>';
        
        return accordionHTML;
    }
};