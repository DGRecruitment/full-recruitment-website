/**
 * Search Interface Component - RecruitPro Theme
 * 
 * Comprehensive search system for recruitment agency websites
 * Handles job search, candidate search, and general content search
 * 
 * Features:
 * - Advanced job search with filters
 * - Real-time autocomplete and suggestions
 * - Multi-faceted search results
 * - Search analytics and tracking
 * - AJAX-powered search experience
 * - Mobile-optimized interface
 * - Integration with CRM system
 * - SEO-friendly search URLs
 * - Search history and saved searches
 * - Voice search support
 * 
 * @package RecruitPro
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Search interface namespace
    window.RecruitPro = window.RecruitPro || {};
    window.RecruitPro.SearchInterface = window.RecruitPro.SearchInterface || {};

    /**
     * Search Interface Component
     */
    const SearchInterface = {
        
        // Configuration
        config: {
            selectors: {
                searchForm: '.search-form',
                searchInput: '.search-input',
                searchButton: '.search-button',
                searchToggle: '.search-toggle',
                searchDropdown: '.search-dropdown',
                searchResults: '.search-results',
                searchFilters: '.search-filters',
                advancedToggle: '.advanced-search-toggle',
                advancedFilters: '.advanced-search-filters',
                clearFilters: '.clear-filters',
                filterGroup: '.filter-group',
                searchSuggestions: '.search-suggestions',
                searchHistory: '.search-history',
                searchTabs: '.search-tabs',
                voiceSearch: '.voice-search-button',
                searchLoader: '.search-loader',
                noResults: '.no-results',
                resultItem: '.search-result-item',
                paginationContainer: '.search-pagination'
            },
            classes: {
                active: 'search-active',
                loading: 'search-loading',
                hasResults: 'has-results',
                noResults: 'no-results',
                filterActive: 'filter-active',
                suggestionsOpen: 'suggestions-open',
                advancedOpen: 'advanced-open',
                searching: 'is-searching',
                focused: 'search-focused',
                voiceActive: 'voice-active'
            },
            searchTypes: {
                jobs: 'jobs',
                candidates: 'candidates',
                content: 'content',
                companies: 'companies',
                all: 'all'
            },
            endpoints: {
                search: '/wp-json/recruitpro/v1/search',
                suggestions: '/wp-json/recruitpro/v1/search/suggestions',
                filters: '/wp-json/recruitpro/v1/search/filters'
            },
            settings: {
                minQueryLength: 2,
                debounceDelay: 300,
                maxSuggestions: 8,
                resultsPerPage: 12,
                maxHistoryItems: 10,
                enableVoiceSearch: true,
                enableGeolocation: true,
                enableAnalytics: true
            }
        },

        // State management
        state: {
            currentQuery: '',
            currentFilters: {},
            searchType: 'all',
            currentPage: 1,
            totalResults: 0,
            totalPages: 0,
            isSearching: false,
            suggestions: [],
            searchHistory: [],
            savedSearches: [],
            currentLocation: null,
            voiceRecognition: null,
            activeFilters: {},
            sortBy: 'relevance',
            sortOrder: 'desc',
            lastSearchTime: 0,
            searchAnalytics: {
                totalSearches: 0,
                popularQueries: {},
                searchTypes: {},
                clickThroughs: {}
            }
        },

        /**
         * Initialize the search interface
         */
        init: function() {
            if (this.initialized) return;
            
            this.cacheElements();
            this.detectCapabilities();
            this.loadSearchHistory();
            this.bindEvents();
            this.setupSearchForms();
            this.setupFilters();
            this.setupVoiceSearch();
            this.setupGeolocation();
            this.initializeSearchTabs();
            
            this.initialized = true;
            console.log('RecruitPro Search Interface initialized');
        },

        /**
         * Cache DOM elements
         */
        cacheElements: function() {
            this.cache = {
                $window: $(window),
                $document: $(document),
                $body: $('body'),
                $searchForms: $(this.config.selectors.searchForm),
                $searchInputs: $(this.config.selectors.searchInput),
                $searchButtons: $(this.config.selectors.searchButton),
                $searchToggles: $(this.config.selectors.searchToggle),
                $searchDropdowns: $(this.config.selectors.searchDropdown),
                $searchResults: $(this.config.selectors.searchResults),
                $searchFilters: $(this.config.selectors.searchFilters),
                $advancedToggle: $(this.config.selectors.advancedToggle),
                $advancedFilters: $(this.config.selectors.advancedFilters)
            };
        },

        /**
         * Detect browser capabilities
         */
        detectCapabilities: function() {
            this.capabilities = {
                speechRecognition: 'webkitSpeechRecognition' in window || 'SpeechRecognition' in window,
                geolocation: 'geolocation' in navigator,
                localStorage: this.supportsLocalStorage(),
                urlHistory: 'pushState' in history,
                intersectionObserver: 'IntersectionObserver' in window
            };
        },

        /**
         * Check localStorage support
         */
        supportsLocalStorage: function() {
            try {
                const test = 'test';
                localStorage.setItem(test, test);
                localStorage.removeItem(test);
                return true;
            } catch (e) {
                return false;
            }
        },

        /**
         * Load search history from localStorage
         */
        loadSearchHistory: function() {
            if (!this.capabilities.localStorage) return;

            try {
                const history = localStorage.getItem('recruitpro_search_history');
                const savedSearches = localStorage.getItem('recruitpro_saved_searches');
                
                if (history) {
                    this.state.searchHistory = JSON.parse(history);
                }
                
                if (savedSearches) {
                    this.state.savedSearches = JSON.parse(savedSearches);
                }
            } catch (error) {
                console.warn('Could not load search history:', error);
            }
        },

        /**
         * Save search history to localStorage
         */
        saveSearchHistory: function() {
            if (!this.capabilities.localStorage) return;

            try {
                localStorage.setItem('recruitpro_search_history', JSON.stringify(this.state.searchHistory));
                localStorage.setItem('recruitpro_saved_searches', JSON.stringify(this.state.savedSearches));
            } catch (error) {
                console.warn('Could not save search history:', error);
            }
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const self = this;

            // Search form submissions
            this.cache.$document.on('submit', this.config.selectors.searchForm, (e) => {
                this.handleSearchSubmit(e);
            });

            // Search input events
            this.cache.$document.on('input', this.config.selectors.searchInput, 
                this.utils.debounce((e) => this.handleSearchInput(e), this.config.settings.debounceDelay)
            );

            this.cache.$document.on('focus', this.config.selectors.searchInput, (e) => {
                this.handleSearchFocus(e);
            });

            this.cache.$document.on('blur', this.config.selectors.searchInput, (e) => {
                this.handleSearchBlur(e);
            });

            // Search toggle
            this.cache.$document.on('click', this.config.selectors.searchToggle, (e) => {
                this.toggleSearchDropdown(e);
            });

            // Advanced search toggle
            this.cache.$document.on('click', this.config.selectors.advancedToggle, (e) => {
                this.toggleAdvancedFilters(e);
            });

            // Filter changes
            this.cache.$document.on('change', this.config.selectors.searchFilters + ' select, ' + 
                                              this.config.selectors.searchFilters + ' input', (e) => {
                this.handleFilterChange(e);
            });

            // Clear filters
            this.cache.$document.on('click', this.config.selectors.clearFilters, (e) => {
                this.clearAllFilters(e);
            });

            // Search result clicks
            this.cache.$document.on('click', this.config.selectors.resultItem, (e) => {
                this.trackResultClick(e);
            });

            // Search tab switching
            this.cache.$document.on('click', this.config.selectors.searchTabs + ' button', (e) => {
                this.handleTabSwitch(e);
            });

            // Voice search
            this.cache.$document.on('click', this.config.selectors.voiceSearch, (e) => {
                this.handleVoiceSearch(e);
            });

            // Keyboard shortcuts
            this.cache.$document.on('keydown', (e) => this.handleKeyboardShortcuts(e));

            // Click outside to close dropdowns
            this.cache.$document.on('click', (e) => {
                if (!$(e.target).closest('.search-wrapper').length) {
                    this.closeSearchDropdowns();
                }
            });

            // Pagination
            this.cache.$document.on('click', '.search-pagination a', (e) => {
                this.handlePagination(e);
            });

            // Window events
            this.cache.$window.on('popstate', (e) => this.handleHistoryChange(e));
            this.cache.$window.on('resize', this.utils.debounce(() => this.handleResize(), 250));
        },

        /**
         * Setup search forms
         */
        setupSearchForms: function() {
            this.cache.$searchForms.each((index, form) => {
                const $form = $(form);
                
                // Add search enhancements
                this.enhanceSearchForm($form);
                
                // Initialize search input
                this.initializeSearchInput($form.find(this.config.selectors.searchInput));
            });
        },

        /**
         * Enhance search form
         */
        enhanceSearchForm: function($form) {
            const $input = $form.find(this.config.selectors.searchInput);
            const $button = $form.find(this.config.selectors.searchButton);

            // Add search wrapper if not exists
            if (!$form.parent().hasClass('search-wrapper')) {
                $form.wrap('<div class="search-wrapper"></div>');
            }

            // Add suggestions container
            if (!$form.siblings('.search-suggestions').length) {
                $form.after('<div class="search-suggestions"></div>');
            }

            // Add voice search button if supported
            if (this.capabilities.speechRecognition && this.config.settings.enableVoiceSearch) {
                this.addVoiceSearchButton($form);
            }

            // Add clear button
            this.addClearButton($form);

            // Enhance with ARIA attributes
            this.enhanceFormAccessibility($form);
        },

        /**
         * Initialize search input
         */
        initializeSearchInput: function($input) {
            if (!$input.length) return;

            // Set up autocomplete attributes
            $input.attr({
                'autocomplete': 'off',
                'spellcheck': 'false',
                'autocapitalize': 'off',
                'autocorrect': 'off'
            });

            // Add placeholder rotation if multiple placeholders exist
            this.setupPlaceholderRotation($input);

            // Initialize with URL query if present
            this.initializeFromURL($input);
        },

        /**
         * Setup search filters
         */
        setupFilters: function() {
            // Initialize filter groups
            this.cache.$searchFilters.each((index, filterGroup) => {
                const $group = $(filterGroup);
                this.initializeFilterGroup($group);
            });

            // Load saved filter preferences
            this.loadFilterPreferences();
        },

        /**
         * Initialize filter group
         */
        initializeFilterGroup: function($group) {
            // Add filter group enhancements
            $group.find('select').each(function() {
                const $select = $(this);
                // Enhanced select styling would go here
            });

            // Range sliders
            $group.find('input[type="range"]').each(function() {
                const $range = $(this);
                this.setupRangeSlider($range);
            }.bind(this));

            // Checkbox groups
            $group.find('.checkbox-group').each(function() {
                const $checkboxGroup = $(this);
                this.setupCheckboxGroup($checkboxGroup);
            }.bind(this));
        },

        /**
         * Setup voice search
         */
        setupVoiceSearch: function() {
            if (!this.capabilities.speechRecognition || !this.config.settings.enableVoiceSearch) {
                return;
            }

            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.state.voiceRecognition = new SpeechRecognition();
            
            this.state.voiceRecognition.continuous = false;
            this.state.voiceRecognition.interimResults = false;
            this.state.voiceRecognition.lang = 'en-US';

            this.state.voiceRecognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                this.handleVoiceSearchResult(transcript);
            };

            this.state.voiceRecognition.onerror = (event) => {
                this.handleVoiceSearchError(event);
            };

            this.state.voiceRecognition.onend = () => {
                this.cache.$body.removeClass(this.config.classes.voiceActive);
            };
        },

        /**
         * Setup geolocation
         */
        setupGeolocation: function() {
            if (!this.capabilities.geolocation || !this.config.settings.enableGeolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.state.currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    };
                    
                    // Update location-based filters
                    this.updateLocationFilters();
                },
                (error) => {
                    console.warn('Geolocation error:', error);
                },
                {
                    enableHighAccuracy: false,
                    timeout: 10000,
                    maximumAge: 300000 // 5 minutes
                }
            );
        },

        /**
         * Initialize search tabs
         */
        initializeSearchTabs: function() {
            const $tabs = $(this.config.selectors.searchTabs);
            if (!$tabs.length) return;

            // Set default active tab
            const $firstTab = $tabs.find('button').first();
            if ($firstTab.length && !$tabs.find('.active').length) {
                $firstTab.addClass('active');
                this.state.searchType = $firstTab.data('search-type') || 'all';
            }
        },

        /**
         * Handle search form submission
         */
        handleSearchSubmit: function(e) {
            e.preventDefault();
            
            const $form = $(e.currentTarget);
            const $input = $form.find(this.config.selectors.searchInput);
            const query = $input.val().trim();

            if (query.length < this.config.settings.minQueryLength) {
                this.showValidationError($input, 'Please enter at least ' + this.config.settings.minQueryLength + ' characters');
                return;
            }

            this.performSearch(query, this.getCurrentFilters(), 1);
        },

        /**
         * Handle search input changes
         */
        handleSearchInput: function(e) {
            const $input = $(e.currentTarget);
            const query = $input.val().trim();

            this.state.currentQuery = query;

            if (query.length >= this.config.settings.minQueryLength) {
                this.fetchSuggestions(query);
                this.showSuggestions($input);
            } else {
                this.hideSuggestions($input);
            }

            // Update clear button visibility
            this.updateClearButton($input);
        },

        /**
         * Handle search input focus
         */
        handleSearchFocus: function(e) {
            const $input = $(e.currentTarget);
            const $wrapper = $input.closest('.search-wrapper');
            
            $wrapper.addClass(this.config.classes.focused);
            
            // Show recent searches or suggestions
            if (this.state.currentQuery.length >= this.config.settings.minQueryLength) {
                this.showSuggestions($input);
            } else {
                this.showRecentSearches($input);
            }
        },

        /**
         * Handle search input blur
         */
        handleSearchBlur: function(e) {
            const $input = $(e.currentTarget);
            const $wrapper = $input.closest('.search-wrapper');
            
            // Delay to allow for suggestion clicks
            setTimeout(() => {
                $wrapper.removeClass(this.config.classes.focused);
                this.hideSuggestions($input);
            }, 150);
        },

        /**
         * Perform search
         */
        performSearch: function(query, filters = {}, page = 1, replaceHistory = false) {
            if (this.state.isSearching) return;

            this.state.isSearching = true;
            this.state.currentQuery = query;
            this.state.currentFilters = filters;
            this.state.currentPage = page;

            // Show loading state
            this.showSearchLoading();

            // Build search parameters
            const searchParams = this.buildSearchParams(query, filters, page);

            // Update URL
            this.updateURL(searchParams, replaceHistory);

            // Add to search history
            this.addToSearchHistory(query, filters);

            // Track search
            this.trackSearch(query, filters);

            // Perform AJAX search
            this.executeSearch(searchParams).then((results) => {
                this.handleSearchResults(results);
            }).catch((error) => {
                this.handleSearchError(error);
            }).finally(() => {
                this.hideSearchLoading();
                this.state.isSearching = false;
            });
        },

        /**
         * Build search parameters
         */
        buildSearchParams: function(query, filters, page) {
            const params = {
                q: query,
                type: this.state.searchType,
                page: page,
                per_page: this.config.settings.resultsPerPage,
                sort_by: this.state.sortBy,
                sort_order: this.state.sortOrder
            };

            // Add filters
            Object.keys(filters).forEach(key => {
                if (filters[key] !== '' && filters[key] !== null && filters[key] !== undefined) {
                    params[key] = filters[key];
                }
            });

            // Add location if available
            if (this.state.currentLocation) {
                params.lat = this.state.currentLocation.lat;
                params.lng = this.state.currentLocation.lng;
            }

            return params;
        },

        /**
         * Execute search via AJAX
         */
        executeSearch: function(params) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: this.config.endpoints.search,
                    method: 'GET',
                    data: params,
                    timeout: 10000,
                    success: (response) => {
                        if (response.success) {
                            resolve(response.data);
                        } else {
                            reject(new Error(response.message || 'Search failed'));
                        }
                    },
                    error: (xhr, status, error) => {
                        reject(new Error(`Search request failed: ${error}`));
                    }
                });
            });
        },

        /**
         * Handle search results
         */
        handleSearchResults: function(results) {
            this.state.totalResults = results.total || 0;
            this.state.totalPages = results.pages || 1;

            // Display results
            this.displaySearchResults(results);

            // Update pagination
            this.updatePagination(results);

            // Update result count
            this.updateResultCount(results);

            // Track successful search
            this.trackSearchSuccess(results);
        },

        /**
         * Display search results
         */
        displaySearchResults: function(results) {
            const $container = this.cache.$searchResults;
            if (!$container.length) return;

            if (!results.items || results.items.length === 0) {
                this.showNoResults();
                return;
            }

            // Generate results HTML
            const resultsHTML = this.generateResultsHTML(results.items);
            
            // Update container
            $container.html(resultsHTML).addClass(this.config.classes.hasResults);

            // Animate results
            this.animateResults($container);

            // Setup lazy loading if needed
            this.setupResultsLazyLoading($container);
        },

        /**
         * Generate results HTML
         */
        generateResultsHTML: function(items) {
            return items.map(item => this.generateResultItemHTML(item)).join('');
        },

        /**
         * Generate single result item HTML
         */
        generateResultItemHTML: function(item) {
            const typeClass = `result-type-${item.type}`;
            const imageHTML = item.image ? `<img src="${item.image}" alt="${item.title}" loading="lazy">` : '';
            const metaHTML = this.generateResultMetaHTML(item);

            return `
                <div class="search-result-item ${typeClass}" data-result-id="${item.id}" data-result-type="${item.type}">
                    <div class="result-content">
                        ${imageHTML ? `<div class="result-image">${imageHTML}</div>` : ''}
                        <div class="result-details">
                            <h3 class="result-title">
                                <a href="${item.url}" title="${item.title}">${item.title}</a>
                            </h3>
                            <div class="result-excerpt">${item.excerpt}</div>
                            <div class="result-meta">${metaHTML}</div>
                        </div>
                    </div>
                </div>
            `;
        },

        /**
         * Generate result meta HTML
         */
        generateResultMetaHTML: function(item) {
            const meta = [];
            
            if (item.type === 'job') {
                if (item.company) meta.push(`<span class="meta-company">${item.company}</span>`);
                if (item.location) meta.push(`<span class="meta-location">${item.location}</span>`);
                if (item.salary) meta.push(`<span class="meta-salary">${item.salary}</span>`);
            } else if (item.type === 'candidate') {
                if (item.experience) meta.push(`<span class="meta-experience">${item.experience}</span>`);
                if (item.skills) meta.push(`<span class="meta-skills">${item.skills}</span>`);
            } else {
                if (item.date) meta.push(`<span class="meta-date">${item.date}</span>`);
                if (item.author) meta.push(`<span class="meta-author">${item.author}</span>`);
            }

            return meta.join(' • ');
        },

        /**
         * Fetch suggestions
         */
        fetchSuggestions: function(query) {
            if (query.length < this.config.settings.minQueryLength) return;

            $.ajax({
                url: this.config.endpoints.suggestions,
                method: 'GET',
                data: {
                    q: query,
                    type: this.state.searchType,
                    limit: this.config.settings.maxSuggestions
                },
                timeout: 5000,
                success: (response) => {
                    if (response.success) {
                        this.state.suggestions = response.data || [];
                        this.updateSuggestions();
                    }
                },
                error: (xhr, status, error) => {
                    console.warn('Suggestions request failed:', error);
                }
            });
        },

        /**
         * Update suggestions display
         */
        updateSuggestions: function() {
            const suggestions = this.state.suggestions;
            if (!suggestions.length) return;

            this.cache.$searchInputs.each((index, input) => {
                const $input = $(input);
                const $suggestions = $input.siblings('.search-suggestions');
                
                if ($suggestions.length) {
                    const suggestionsHTML = this.generateSuggestionsHTML(suggestions);
                    $suggestions.html(suggestionsHTML);
                }
            });
        },

        /**
         * Generate suggestions HTML
         */
        generateSuggestionsHTML: function(suggestions) {
            const html = suggestions.map(suggestion => `
                <div class="suggestion-item" data-suggestion="${suggestion.text}">
                    <span class="suggestion-text">${suggestion.text}</span>
                    <span class="suggestion-type">${suggestion.type}</span>
                </div>
            `).join('');

            return `<div class="suggestions-list">${html}</div>`;
        },

        /**
         * Handle filter changes
         */
        handleFilterChange: function(e) {
            const $filter = $(e.currentTarget);
            const filterName = $filter.attr('name');
            const filterValue = $filter.val();

            // Update active filters
            if (filterValue && filterValue !== '') {
                this.state.activeFilters[filterName] = filterValue;
            } else {
                delete this.state.activeFilters[filterName];
            }

            // Update filter display
            this.updateFilterDisplay();

            // Perform search with new filters
            if (this.state.currentQuery) {
                this.performSearch(this.state.currentQuery, this.state.activeFilters, 1, true);
            }
        },

        /**
         * Handle voice search
         */
        handleVoiceSearch: function(e) {
            e.preventDefault();
            
            if (!this.state.voiceRecognition) return;

            this.cache.$body.addClass(this.config.classes.voiceActive);
            
            try {
                this.state.voiceRecognition.start();
            } catch (error) {
                console.error('Voice search error:', error);
                this.cache.$body.removeClass(this.config.classes.voiceActive);
            }
        },

        /**
         * Handle voice search result
         */
        handleVoiceSearchResult: function(transcript) {
            const $activeInput = this.cache.$searchInputs.filter(':focus').first();
            const $targetInput = $activeInput.length ? $activeInput : this.cache.$searchInputs.first();

            $targetInput.val(transcript).trigger('input');
            this.performSearch(transcript, this.getCurrentFilters(), 1);
            
            this.cache.$body.removeClass(this.config.classes.voiceActive);
        },

        /**
         * Handle keyboard shortcuts
         */
        handleKeyboardShortcuts: function(e) {
            // Focus search on "/" key
            if (e.key === '/' && !$(e.target).is('input, textarea')) {
                e.preventDefault();
                const $firstInput = this.cache.$searchInputs.first();
                if ($firstInput.length) {
                    $firstInput.focus();
                }
            }

            // Escape key to close suggestions
            if (e.key === 'Escape') {
                this.closeSearchDropdowns();
            }

            // Arrow keys for suggestion navigation
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                this.handleSuggestionNavigation(e);
            }
        },

        /**
         * Track search action
         */
        trackSearch: function(query, filters) {
            this.state.searchAnalytics.totalSearches++;
            
            // Track popular queries
            if (!this.state.searchAnalytics.popularQueries[query]) {
                this.state.searchAnalytics.popularQueries[query] = 0;
            }
            this.state.searchAnalytics.popularQueries[query]++;

            // Track search types
            if (!this.state.searchAnalytics.searchTypes[this.state.searchType]) {
                this.state.searchAnalytics.searchTypes[this.state.searchType] = 0;
            }
            this.state.searchAnalytics.searchTypes[this.state.searchType]++;

            // Google Analytics tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'search', {
                    search_term: query,
                    search_type: this.state.searchType,
                    filters_applied: Object.keys(filters).length
                });
            }

            // Custom event
            this.cache.$document.trigger('recruitpro:search-performed', {
                query: query,
                filters: filters,
                type: this.state.searchType
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
            },

            escapeHtml: function(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        },

        // Helper methods that would be implemented
        getCurrentFilters: function() {
            return this.state.activeFilters;
        },

        showSearchLoading: function() {
            this.cache.$searchResults.addClass(this.config.classes.loading);
        },

        hideSearchLoading: function() {
            this.cache.$searchResults.removeClass(this.config.classes.loading);
        },

        showNoResults: function() {
            this.cache.$searchResults.addClass(this.config.classes.noResults);
        },

        updateURL: function(params, replaceHistory) {
            if (!this.capabilities.urlHistory) return;
            
            const url = new URL(window.location);
            Object.keys(params).forEach(key => {
                url.searchParams.set(key, params[key]);
            });
            
            if (replaceHistory) {
                history.replaceState({}, '', url);
            } else {
                history.pushState({}, '', url);
            }
        },

        addToSearchHistory: function(query, filters) {
            const historyItem = {
                query: query,
                filters: filters,
                timestamp: Date.now(),
                type: this.state.searchType
            };

            this.state.searchHistory.unshift(historyItem);
            
            // Limit history size
            if (this.state.searchHistory.length > this.config.settings.maxHistoryItems) {
                this.state.searchHistory = this.state.searchHistory.slice(0, this.config.settings.maxHistoryItems);
            }

            this.saveSearchHistory();
        },

        // Additional helper methods would be implemented here...
        toggleSearchDropdown: function(e) { /* Implementation */ },
        toggleAdvancedFilters: function(e) { /* Implementation */ },
        clearAllFilters: function(e) { /* Implementation */ },
        handleTabSwitch: function(e) { /* Implementation */ },
        trackResultClick: function(e) { /* Implementation */ },
        handlePagination: function(e) { /* Implementation */ },
        handleHistoryChange: function(e) { /* Implementation */ },
        handleResize: function() { /* Implementation */ },
        enhanceFormAccessibility: function($form) { /* Implementation */ },
        addVoiceSearchButton: function($form) { /* Implementation */ },
        addClearButton: function($form) { /* Implementation */ },
        setupPlaceholderRotation: function($input) { /* Implementation */ },
        initializeFromURL: function($input) { /* Implementation */ },
        loadFilterPreferences: function() { /* Implementation */ },
        setupRangeSlider: function($range) { /* Implementation */ },
        setupCheckboxGroup: function($group) { /* Implementation */ },
        updateLocationFilters: function() { /* Implementation */ },
        showValidationError: function($input, message) { /* Implementation */ },
        showSuggestions: function($input) { /* Implementation */ },
        hideSuggestions: function($input) { /* Implementation */ },
        showRecentSearches: function($input) { /* Implementation */ },
        updateClearButton: function($input) { /* Implementation */ },
        handleSearchError: function(error) { /* Implementation */ },
        updatePagination: function(results) { /* Implementation */ },
        updateResultCount: function(results) { /* Implementation */ },
        trackSearchSuccess: function(results) { /* Implementation */ },
        animateResults: function($container) { /* Implementation */ },
        setupResultsLazyLoading: function($container) { /* Implementation */ },
        updateFilterDisplay: function() { /* Implementation */ },
        handleVoiceSearchError: function(event) { /* Implementation */ },
        closeSearchDropdowns: function() { /* Implementation */ },
        handleSuggestionNavigation: function(e) { /* Implementation */ },

        /**
         * Public API methods
         */
        api: {
            search: function(query, filters, page) {
                SearchInterface.performSearch(query, filters || {}, page || 1);
            },

            setSearchType: function(type) {
                SearchInterface.state.searchType = type;
            },

            getSearchHistory: function() {
                return SearchInterface.state.searchHistory;
            },

            clearSearchHistory: function() {
                SearchInterface.state.searchHistory = [];
                SearchInterface.saveSearchHistory();
            },

            addFilter: function(name, value) {
                SearchInterface.state.activeFilters[name] = value;
                SearchInterface.updateFilterDisplay();
            },

            removeFilter: function(name) {
                delete SearchInterface.state.activeFilters[name];
                SearchInterface.updateFilterDisplay();
            },

            getAnalytics: function() {
                return SearchInterface.state.searchAnalytics;
            },

            refresh: function() {
                SearchInterface.cacheElements();
                SearchInterface.setupSearchForms();
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        SearchInterface.init();
    });

    // Expose public API
    window.RecruitPro.SearchInterface = SearchInterface.api;

    // Support for dynamic content
    $(document).on('recruitpro:content-loaded', function() {
        SearchInterface.cache.$searchForms = $(SearchInterface.config.selectors.searchForm);
        SearchInterface.setupSearchForms();
    });

})(jQuery);