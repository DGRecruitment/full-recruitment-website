/**
 * RecruitPro Theme - Video Player Module
 * 
 * Comprehensive video player system for recruitment websites supporting
 * team videos, testimonials, company culture content, and training materials.
 * Supports multiple video sources with accessibility and performance optimization.
 * 
 * @package RecruitPro
 * @subpackage Assets/JS
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Video Player Module
    const RecruitProVideoPlayer = {
        
        // Configuration
        config: {
            selectors: {
                player: '.rp-video-player',
                trigger: '[data-video]',
                placeholder: '.video-placeholder',
                overlay: '.video-overlay',
                controls: '.video-controls',
                playButton: '.video-play-btn',
                pauseButton: '.video-pause-btn',
                progressBar: '.video-progress',
                timeDisplay: '.video-time',
                volumeControl: '.video-volume',
                fullscreenBtn: '.video-fullscreen',
                muteBtn: '.video-mute',
                speedControl: '.video-speed',
                qualityControl: '.video-quality',
                captionsBtn: '.video-captions',
                playlist: '.video-playlist',
                thumbnails: '.video-thumbnails'
            },
            classes: {
                active: 'video-active',
                playing: 'video-playing',
                paused: 'video-paused',
                loading: 'video-loading',
                error: 'video-error',
                fullscreen: 'video-fullscreen',
                muted: 'video-muted',
                hasControls: 'has-controls',
                customControls: 'custom-controls',
                autoplay: 'video-autoplay',
                loop: 'video-loop',
                responsive: 'video-responsive'
            },
            attributes: {
                videoSrc: 'data-video-src',
                videoType: 'data-video-type',
                videoPoster: 'data-video-poster',
                videoTitle: 'data-video-title',
                videoDescription: 'data-video-description',
                videoId: 'data-video-id',
                videoProvider: 'data-video-provider',
                videoAutoplay: 'data-video-autoplay',
                videoLoop: 'data-video-loop',
                videoMuted: 'data-video-muted',
                videoControls: 'data-video-controls',
                videoCaptions: 'data-video-captions',
                videoQuality: 'data-video-quality',
                videoSpeed: 'data-video-speed'
            },
            providers: {
                youtube: {
                    embedUrl: 'https://www.youtube.com/embed/',
                    thumbnailUrl: 'https://img.youtube.com/vi/',
                    apiUrl: 'https://www.googleapis.com/youtube/v3/',
                    playerParams: {
                        autoplay: 0,
                        controls: 1,
                        modestbranding: 1,
                        rel: 0,
                        showinfo: 0,
                        fs: 1,
                        cc_load_policy: 0,
                        iv_load_policy: 3,
                        autohide: 0
                    }
                },
                vimeo: {
                    embedUrl: 'https://player.vimeo.com/video/',
                    apiUrl: 'https://vimeo.com/api/v2/video/',
                    playerParams: {
                        autoplay: 0,
                        loop: 0,
                        color: '2563eb',
                        title: 0,
                        byline: 0,
                        portrait: 0
                    }
                }
            },
            analytics: {
                trackViews: true,
                trackProgress: true,
                trackCompletion: true,
                progressIntervals: [25, 50, 75, 90, 100]
            }
        },

        // State management
        state: {
            players: new Map(),
            currentPlayer: null,
            fullscreenPlayer: null,
            modalOpen: false,
            touchStartY: 0,
            isTouch: false
        },

        /**
         * Initialize video player system
         */
        init: function() {
            this.detectCapabilities();
            this.bindEvents();
            this.initializePlayers();
            this.setupModalIntegration();
            this.setupAccessibility();
            this.setupAnalytics();
        },

        /**
         * Detect device capabilities
         */
        detectCapabilities: function() {
            this.state.isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            this.state.supportsFullscreen = !!(document.fullscreenEnabled || 
                document.webkitFullscreenEnabled || 
                document.mozFullScreenEnabled || 
                document.msFullscreenEnabled);
            this.state.supportsHLS = this.checkHLSSupport();
            this.state.preferredFormats = this.getPreferredFormats();
        },

        /**
         * Check HLS support
         */
        checkHLSSupport: function() {
            const video = document.createElement('video');
            return video.canPlayType('application/vnd.apple.mpegurl') !== '';
        },

        /**
         * Get preferred video formats
         */
        getPreferredFormats: function() {
            const video = document.createElement('video');
            const formats = [];
            
            if (video.canPlayType('video/mp4; codecs="avc1.42E01E"') !== '') {
                formats.push('mp4');
            }
            if (video.canPlayType('video/webm; codecs="vp8, vorbis"') !== '') {
                formats.push('webm');
            }
            if (video.canPlayType('video/ogg; codecs="theora"') !== '') {
                formats.push('ogg');
            }
            
            return formats;
        },

        /**
         * Initialize all video players on page
         */
        initializePlayers: function() {
            const self = this;
            
            $(this.config.selectors.player).each(function() {
                const $player = $(this);
                const playerId = self.generatePlayerId($player);
                
                self.createPlayer($player, playerId);
            });

            // Initialize video triggers
            $(this.config.selectors.trigger).each(function() {
                self.setupVideoTrigger($(this));
            });
        },

        /**
         * Generate unique player ID
         */
        generatePlayerId: function($player) {
            let id = $player.attr('id');
            if (!id) {
                id = 'rp-video-player-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                $player.attr('id', id);
            }
            return id;
        },

        /**
         * Create video player instance
         */
        createPlayer: function($container, playerId) {
            const videoData = this.extractVideoData($container);
            const playerConfig = this.buildPlayerConfig(videoData);
            
            let player;
            
            switch (videoData.provider) {
                case 'youtube':
                    player = this.createYouTubePlayer($container, playerConfig);
                    break;
                case 'vimeo':
                    player = this.createVimeoPlayer($container, playerConfig);
                    break;
                case 'self-hosted':
                default:
                    player = this.createNativePlayer($container, playerConfig);
                    break;
            }
            
            if (player) {
                this.state.players.set(playerId, player);
                this.setupPlayerEvents(player, $container);
                
                // Trigger player created event
                $container.trigger('playerCreated', [player, playerConfig]);
            }
        },

        /**
         * Extract video data from container
         */
        extractVideoData: function($container) {
            const attrs = this.config.attributes;
            
            return {
                src: $container.attr(attrs.videoSrc) || $container.find('video').attr('src'),
                type: $container.attr(attrs.videoType) || 'video/mp4',
                poster: $container.attr(attrs.videoPoster) || $container.find('video').attr('poster'),
                title: $container.attr(attrs.videoTitle) || '',
                description: $container.attr(attrs.videoDescription) || '',
                id: $container.attr(attrs.videoId) || '',
                provider: $container.attr(attrs.videoProvider) || this.detectProvider($container.attr(attrs.videoSrc)),
                autoplay: $container.attr(attrs.videoAutoplay) === 'true',
                loop: $container.attr(attrs.videoLoop) === 'true',
                muted: $container.attr(attrs.videoMuted) === 'true',
                controls: $container.attr(attrs.videoControls) !== 'false',
                captions: $container.attr(attrs.videoCaptions) || '',
                quality: $container.attr(attrs.videoQuality) || 'auto',
                speed: parseFloat($container.attr(attrs.videoSpeed)) || 1
            };
        },

        /**
         * Detect video provider from URL
         */
        detectProvider: function(url) {
            if (!url) return 'self-hosted';
            
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                return 'youtube';
            } else if (url.includes('vimeo.com')) {
                return 'vimeo';
            } else {
                return 'self-hosted';
            }
        },

        /**
         * Build player configuration
         */
        buildPlayerConfig: function(videoData) {
            return {
                ...videoData,
                width: '100%',
                height: 'auto',
                responsive: true,
                preload: 'metadata',
                playsinline: true,
                disablePictureInPicture: false,
                crossorigin: 'anonymous'
            };
        },

        /**
         * Create native HTML5 video player
         */
        createNativePlayer: function($container, config) {
            const videoHTML = this.generateNativeVideoHTML(config);
            $container.html(videoHTML);
            
            const $video = $container.find('video');
            const videoElement = $video[0];
            
            // Setup custom controls if enabled
            if (config.controls && $container.data('custom-controls')) {
                this.createCustomControls($container, videoElement);
            }
            
            return {
                type: 'native',
                element: videoElement,
                $element: $video,
                $container: $container,
                config: config,
                // Native API methods
                play: () => videoElement.play(),
                pause: () => videoElement.pause(),
                stop: () => {
                    videoElement.pause();
                    videoElement.currentTime = 0;
                },
                mute: () => videoElement.muted = true,
                unmute: () => videoElement.muted = false,
                setVolume: (volume) => videoElement.volume = volume,
                setCurrentTime: (time) => videoElement.currentTime = time,
                getDuration: () => videoElement.duration,
                getCurrentTime: () => videoElement.currentTime,
                isPaused: () => videoElement.paused,
                isMuted: () => videoElement.muted,
                getVolume: () => videoElement.volume
            };
        },

        /**
         * Create YouTube player
         */
        createYouTubePlayer: function($container, config) {
            const videoId = this.extractYouTubeId(config.src || config.id);
            if (!videoId) return null;
            
            const embedUrl = this.buildYouTubeEmbedUrl(videoId, config);
            const iframeHTML = this.generateYouTubeIframeHTML(embedUrl, config);
            
            $container.html(iframeHTML);
            
            const $iframe = $container.find('iframe');
            
            return {
                type: 'youtube',
                element: $iframe[0],
                $element: $iframe,
                $container: $container,
                config: config,
                videoId: videoId,
                // YouTube API methods (would need YouTube API integration)
                play: () => this.postMessageToYouTube($iframe[0], 'playVideo'),
                pause: () => this.postMessageToYouTube($iframe[0], 'pauseVideo'),
                stop: () => this.postMessageToYouTube($iframe[0], 'stopVideo'),
                mute: () => this.postMessageToYouTube($iframe[0], 'mute'),
                unmute: () => this.postMessageToYouTube($iframe[0], 'unMute')
            };
        },

        /**
         * Create Vimeo player
         */
        createVimeoPlayer: function($container, config) {
            const videoId = this.extractVimeoId(config.src || config.id);
            if (!videoId) return null;
            
            const embedUrl = this.buildVimeoEmbedUrl(videoId, config);
            const iframeHTML = this.generateVimeoIframeHTML(embedUrl, config);
            
            $container.html(iframeHTML);
            
            const $iframe = $container.find('iframe');
            
            return {
                type: 'vimeo',
                element: $iframe[0],
                $element: $iframe,
                $container: $container,
                config: config,
                videoId: videoId,
                // Vimeo API methods (would need Vimeo API integration)
                play: () => this.postMessageToVimeo($iframe[0], 'play'),
                pause: () => this.postMessageToVimeo($iframe[0], 'pause'),
                stop: () => this.postMessageToVimeo($iframe[0], 'pause'),
                mute: () => this.postMessageToVimeo($iframe[0], 'setVolume', 0),
                unmute: () => this.postMessageToVimeo($iframe[0], 'setVolume', 1)
            };
        },

        /**
         * Generate native video HTML
         */
        generateNativeVideoHTML: function(config) {
            const attributes = [
                config.controls ? 'controls' : '',
                config.autoplay ? 'autoplay' : '',
                config.loop ? 'loop' : '',
                config.muted ? 'muted' : '',
                'playsinline',
                config.preload ? `preload="${config.preload}"` : '',
                config.poster ? `poster="${config.poster}"` : '',
                config.crossorigin ? `crossorigin="${config.crossorigin}"` : ''
            ].filter(Boolean).join(' ');
            
            let sourceTags = '';
            if (Array.isArray(config.src)) {
                config.src.forEach(source => {
                    sourceTags += `<source src="${source.src}" type="${source.type}">`;
                });
            } else if (config.src) {
                sourceTags = `<source src="${config.src}" type="${config.type}">`;
            }
            
            let trackTags = '';
            if (config.captions) {
                trackTags = `<track kind="captions" src="${config.captions}" srclang="en" label="English" default>`;
            }
            
            return `
                <video ${attributes} class="rp-video-element">
                    ${sourceTags}
                    ${trackTags}
                    <p>Your browser doesn't support HTML5 video. <a href="${config.src}">Download the video</a> instead.</p>
                </video>
            `;
        },

        /**
         * Generate YouTube iframe HTML
         */
        generateYouTubeIframeHTML: function(embedUrl, config) {
            return `
                <iframe 
                    src="${embedUrl}" 
                    width="${config.width}" 
                    height="${config.height}"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    title="${config.title || 'YouTube video'}"
                    class="rp-video-iframe youtube-iframe">
                </iframe>
            `;
        },

        /**
         * Generate Vimeo iframe HTML
         */
        generateVimeoIframeHTML: function(embedUrl, config) {
            return `
                <iframe 
                    src="${embedUrl}" 
                    width="${config.width}" 
                    height="${config.height}"
                    frameborder="0" 
                    allow="autoplay; fullscreen; picture-in-picture" 
                    allowfullscreen
                    title="${config.title || 'Vimeo video'}"
                    class="rp-video-iframe vimeo-iframe">
                </iframe>
            `;
        },

        /**
         * Create custom video controls
         */
        createCustomControls: function($container, videoElement) {
            const controlsHTML = `
                <div class="rp-video-controls">
                    <div class="controls-row controls-main">
                        <button type="button" class="control-btn play-pause-btn" aria-label="Play/Pause">
                            <span class="play-icon">▶</span>
                            <span class="pause-icon">⏸</span>
                        </button>
                        
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div class="progress-filled"></div>
                                <div class="progress-handle"></div>
                            </div>
                        </div>
                        
                        <div class="time-display">
                            <span class="current-time">0:00</span>
                            <span class="duration">0:00</span>
                        </div>
                        
                        <div class="volume-container">
                            <button type="button" class="control-btn volume-btn" aria-label="Mute/Unmute">
                                <span class="volume-icon">🔊</span>
                                <span class="mute-icon">🔇</span>
                            </button>
                            <div class="volume-slider">
                                <div class="volume-bar">
                                    <div class="volume-filled"></div>
                                    <div class="volume-handle"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="control-group-right">
                            <button type="button" class="control-btn captions-btn" aria-label="Toggle Captions">CC</button>
                            <button type="button" class="control-btn speed-btn" aria-label="Playback Speed">1x</button>
                            <button type="button" class="control-btn quality-btn" aria-label="Video Quality">HD</button>
                            <button type="button" class="control-btn fullscreen-btn" aria-label="Fullscreen">⛶</button>
                        </div>
                    </div>
                </div>
            `;
            
            $container.append(controlsHTML);
            $container.addClass(this.config.classes.customControls);
            
            // Hide native controls
            $(videoElement).removeAttr('controls');
            
            this.bindCustomControlEvents($container, videoElement);
        },

        /**
         * Bind custom control events
         */
        bindCustomControlEvents: function($container, videoElement) {
            const $video = $(videoElement);
            const $controls = $container.find('.rp-video-controls');
            
            // Play/Pause button
            $controls.find('.play-pause-btn').on('click', () => {
                if (videoElement.paused) {
                    videoElement.play();
                } else {
                    videoElement.pause();
                }
            });
            
            // Progress bar
            this.bindProgressBarEvents($controls, videoElement);
            
            // Volume controls
            this.bindVolumeControlEvents($controls, videoElement);
            
            // Fullscreen button
            $controls.find('.fullscreen-btn').on('click', () => {
                this.toggleFullscreen($container[0]);
            });
            
            // Speed control
            this.bindSpeedControlEvents($controls, videoElement);
            
            // Video events
            $video.on('play', () => $container.addClass(this.config.classes.playing));
            $video.on('pause', () => $container.removeClass(this.config.classes.playing));
            $video.on('timeupdate', () => this.updateProgress($controls, videoElement));
            $video.on('loadedmetadata', () => this.updateDuration($controls, videoElement));
            $video.on('volumechange', () => this.updateVolumeDisplay($controls, videoElement));
        },

        /**
         * Bind progress bar events
         */
        bindProgressBarEvents: function($controls, videoElement) {
            const $progressBar = $controls.find('.progress-bar');
            const $progressFilled = $controls.find('.progress-filled');
            
            $progressBar.on('click', (e) => {
                const rect = $progressBar[0].getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                videoElement.currentTime = percent * videoElement.duration;
            });
            
            // Drag functionality
            let isDragging = false;
            
            $progressBar.on('mousedown', () => isDragging = true);
            $(document).on('mouseup', () => isDragging = false);
            
            $progressBar.on('mousemove', (e) => {
                if (!isDragging) return;
                
                const rect = $progressBar[0].getBoundingClientRect();
                const percent = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                videoElement.currentTime = percent * videoElement.duration;
            });
        },

        /**
         * Bind volume control events
         */
        bindVolumeControlEvents: function($controls, videoElement) {
            const $volumeBtn = $controls.find('.volume-btn');
            const $volumeBar = $controls.find('.volume-bar');
            
            $volumeBtn.on('click', () => {
                videoElement.muted = !videoElement.muted;
            });
            
            $volumeBar.on('click', (e) => {
                const rect = $volumeBar[0].getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                videoElement.volume = Math.max(0, Math.min(1, percent));
                videoElement.muted = false;
            });
        },

        /**
         * Bind speed control events
         */
        bindSpeedControlEvents: function($controls, videoElement) {
            const $speedBtn = $controls.find('.speed-btn');
            const speeds = [0.5, 0.75, 1, 1.25, 1.5, 2];
            let currentSpeedIndex = 2; // 1x
            
            $speedBtn.on('click', () => {
                currentSpeedIndex = (currentSpeedIndex + 1) % speeds.length;
                const newSpeed = speeds[currentSpeedIndex];
                videoElement.playbackRate = newSpeed;
                $speedBtn.text(`${newSpeed}x`);
            });
        },

        /**
         * Update progress display
         */
        updateProgress: function($controls, videoElement) {
            const percent = (videoElement.currentTime / videoElement.duration) * 100;
            $controls.find('.progress-filled').css('width', `${percent}%`);
            $controls.find('.current-time').text(this.formatTime(videoElement.currentTime));
            
            // Track progress for analytics
            this.trackVideoProgress(videoElement);
        },

        /**
         * Update duration display
         */
        updateDuration: function($controls, videoElement) {
            $controls.find('.duration').text(this.formatTime(videoElement.duration));
        },

        /**
         * Update volume display
         */
        updateVolumeDisplay: function($controls, videoElement) {
            const $container = $controls.closest('.rp-video-player');
            const percent = videoElement.muted ? 0 : videoElement.volume * 100;
            
            $controls.find('.volume-filled').css('width', `${percent}%`);
            
            if (videoElement.muted || videoElement.volume === 0) {
                $container.addClass(this.config.classes.muted);
            } else {
                $container.removeClass(this.config.classes.muted);
            }
        },

        /**
         * Format time in MM:SS format
         */
        formatTime: function(seconds) {
            if (isNaN(seconds)) return '0:00';
            
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        /**
         * Setup video trigger elements
         */
        setupVideoTrigger: function($trigger) {
            const self = this;
            
            $trigger.on('click', function(e) {
                e.preventDefault();
                
                const videoData = {
                    src: $trigger.attr(self.config.attributes.videoSrc),
                    type: $trigger.attr(self.config.attributes.videoType),
                    poster: $trigger.attr(self.config.attributes.videoPoster),
                    title: $trigger.attr(self.config.attributes.videoTitle),
                    provider: $trigger.attr(self.config.attributes.videoProvider),
                    autoplay: true
                };
                
                self.openVideoModal(videoData);
            });
        },

        /**
         * Open video in modal
         */
        openVideoModal: function(videoData) {
            const modalHTML = `
                <div class="rp-video-modal" id="video-modal">
                    <div class="modal-backdrop"></div>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">${videoData.title || 'Video'}</h3>
                            <button type="button" class="modal-close" aria-label="Close">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="rp-video-player modal-video-player" data-video-autoplay="true"></div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHTML);
            const $modal = $('#video-modal');
            const $videoContainer = $modal.find('.rp-video-player');
            
            // Set video data
            Object.keys(videoData).forEach(key => {
                if (videoData[key]) {
                    $videoContainer.attr(`data-video-${key}`, videoData[key]);
                }
            });
            
            // Create player
            const playerId = this.generatePlayerId($videoContainer);
            this.createPlayer($videoContainer, playerId);
            
            // Setup modal events
            this.setupVideoModalEvents($modal);
            
            // Show modal
            setTimeout(() => $modal.addClass('active'), 50);
            this.state.modalOpen = true;
            
            // Prevent body scroll
            $('body').addClass('modal-open');
        },

        /**
         * Setup video modal events
         */
        setupVideoModalEvents: function($modal) {
            const self = this;
            
            // Close button
            $modal.find('.modal-close').on('click', () => {
                self.closeVideoModal($modal);
            });
            
            // Backdrop click
            $modal.find('.modal-backdrop').on('click', () => {
                self.closeVideoModal($modal);
            });
            
            // Escape key
            $(document).on('keydown.videoModal', (e) => {
                if (e.key === 'Escape') {
                    self.closeVideoModal($modal);
                }
            });
        },

        /**
         * Close video modal
         */
        closeVideoModal: function($modal) {
            $modal.removeClass('active');
            $('body').removeClass('modal-open');
            this.state.modalOpen = false;
            
            setTimeout(() => {
                $modal.remove();
                $(document).off('keydown.videoModal');
            }, 300);
        },

        /**
         * Extract YouTube video ID
         */
        extractYouTubeId: function(url) {
            if (!url) return null;
            
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
            const match = url.match(regExp);
            
            return (match && match[2].length === 11) ? match[2] : null;
        },

        /**
         * Extract Vimeo video ID
         */
        extractVimeoId: function(url) {
            if (!url) return null;
            
            const regExp = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
            const match = url.match(regExp);
            
            return match ? match[1] : null;
        },

        /**
         * Build YouTube embed URL
         */
        buildYouTubeEmbedUrl: function(videoId, config) {
            const params = { ...this.config.providers.youtube.playerParams };
            
            if (config.autoplay) params.autoplay = 1;
            if (config.loop) params.loop = 1;
            if (config.muted) params.mute = 1;
            if (!config.controls) params.controls = 0;
            
            const queryString = Object.keys(params)
                .map(key => `${key}=${params[key]}`)
                .join('&');
            
            return `${this.config.providers.youtube.embedUrl}${videoId}?${queryString}`;
        },

        /**
         * Build Vimeo embed URL
         */
        buildVimeoEmbedUrl: function(videoId, config) {
            const params = { ...this.config.providers.vimeo.playerParams };
            
            if (config.autoplay) params.autoplay = 1;
            if (config.loop) params.loop = 1;
            if (config.muted) params.muted = 1;
            
            const queryString = Object.keys(params)
                .map(key => `${key}=${params[key]}`)
                .join('&');
            
            return `${this.config.providers.vimeo.embedUrl}${videoId}?${queryString}`;
        },

        /**
         * Toggle fullscreen
         */
        toggleFullscreen: function(element) {
            if (!this.state.supportsFullscreen) return;
            
            if (this.isFullscreen()) {
                this.exitFullscreen();
            } else {
                this.requestFullscreen(element);
            }
        },

        /**
         * Request fullscreen
         */
        requestFullscreen: function(element) {
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) {
                element.webkitRequestFullscreen();
            } else if (element.mozRequestFullScreen) {
                element.mozRequestFullScreen();
            } else if (element.msRequestFullscreen) {
                element.msRequestFullscreen();
            }
        },

        /**
         * Exit fullscreen
         */
        exitFullscreen: function() {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        },

        /**
         * Check if in fullscreen
         */
        isFullscreen: function() {
            return !!(document.fullscreenElement ||
                     document.webkitFullscreenElement ||
                     document.mozFullScreenElement ||
                     document.msFullscreenElement);
        },

        /**
         * Setup modal integration
         */
        setupModalIntegration: function() {
            // Integrate with existing modal system if available
            if (window.RecruitPro && window.RecruitPro.ModalSystem) {
                // Integration code would go here
            }
        },

        /**
         * Setup accessibility features
         */
        setupAccessibility: function() {
            // Add ARIA labels and roles
            $(this.config.selectors.player).each(function() {
                const $player = $(this);
                $player.attr('role', 'region');
                $player.attr('aria-label', 'Video player');
            });
        },

        /**
         * Setup analytics tracking
         */
        setupAnalytics: function() {
            if (!this.config.analytics.trackViews) return;
            
            // Would integrate with Google Analytics or other tracking
        },

        /**
         * Track video progress
         */
        trackVideoProgress: function(videoElement) {
            if (!this.config.analytics.trackProgress) return;
            
            const percent = Math.round((videoElement.currentTime / videoElement.duration) * 100);
            
            this.config.analytics.progressIntervals.forEach(interval => {
                if (percent >= interval && !videoElement.dataset[`tracked_${interval}`]) {
                    videoElement.dataset[`tracked_${interval}`] = 'true';
                    
                    // Send tracking event
                    this.sendAnalyticsEvent('video_progress', {
                        percent: interval,
                        video_src: videoElement.src,
                        video_duration: videoElement.duration
                    });
                }
            });
        },

        /**
         * Send analytics event
         */
        sendAnalyticsEvent: function(event, data) {
            // Google Analytics integration
            if (typeof gtag !== 'undefined') {
                gtag('event', event, data);
            }
            
            // WordPress AJAX integration
            if (typeof recruitpro_theme !== 'undefined' && recruitpro_theme.ajax_url) {
                $.post(recruitpro_theme.ajax_url, {
                    action: 'recruitpro_track_video',
                    event: event,
                    data: data,
                    nonce: recruitpro_theme.nonce
                });
            }
        },

        /**
         * Bind global events
         */
        bindEvents: function() {
            // Window resize
            $(window).on('resize', this.handleResize.bind(this));
            
            // Fullscreen change
            $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange', 
                this.handleFullscreenChange.bind(this));
            
            // Page visibility change
            $(document).on('visibilitychange', this.handleVisibilityChange.bind(this));
        },

        /**
         * Handle window resize
         */
        handleResize: function() {
            // Update video player dimensions
            this.state.players.forEach((player) => {
                if (player.type === 'native') {
                    // Update responsive video dimensions
                    this.updateVideoDimensions(player.$container);
                }
            });
        },

        /**
         * Handle fullscreen change
         */
        handleFullscreenChange: function() {
            const isFullscreen = this.isFullscreen();
            
            this.state.players.forEach((player) => {
                if (isFullscreen) {
                    player.$container.addClass(this.config.classes.fullscreen);
                } else {
                    player.$container.removeClass(this.config.classes.fullscreen);
                }
            });
        },

        /**
         * Handle visibility change
         */
        handleVisibilityChange: function() {
            if (document.hidden) {
                // Pause all playing videos when tab becomes hidden
                this.state.players.forEach((player) => {
                    if (player.type === 'native' && !player.element.paused) {
                        player.pause();
                        player.wasPlayingBeforeHidden = true;
                    }
                });
            } else {
                // Resume videos that were playing before hiding
                this.state.players.forEach((player) => {
                    if (player.wasPlayingBeforeHidden) {
                        player.play();
                        player.wasPlayingBeforeHidden = false;
                    }
                });
            }
        },

        /**
         * Update video dimensions for responsive behavior
         */
        updateVideoDimensions: function($container) {
            const $video = $container.find('video, iframe');
            if (!$video.length) return;
            
            const containerWidth = $container.width();
            const aspectRatio = 16 / 9; // Default aspect ratio
            const height = containerWidth / aspectRatio;
            
            $video.css({
                width: '100%',
                height: height + 'px'
            });
        },

        /**
         * Public API methods
         */
        getPlayer: function(playerId) {
            return this.state.players.get(playerId);
        },

        playAll: function() {
            this.state.players.forEach(player => player.play());
        },

        pauseAll: function() {
            this.state.players.forEach(player => player.pause());
        },

        stopAll: function() {
            this.state.players.forEach(player => player.stop());
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RecruitProVideoPlayer.init();
    });

    // Expose to global scope
    window.RecruitProVideoPlayer = RecruitProVideoPlayer;

    // jQuery plugin interface
    $.fn.recruitProVideoPlayer = function(options) {
        return this.each(function() {
            const $container = $(this);
            const playerId = RecruitProVideoPlayer.generatePlayerId($container);
            
            // Apply options as data attributes
            if (options) {
                Object.keys(options).forEach(key => {
                    $container.attr(`data-video-${key}`, options[key]);
                });
            }
            
            RecruitProVideoPlayer.createPlayer($container, playerId);
        });
    };

})(jQuery);

/**
 * Video player utility functions
 */
window.RecruitProVideoUtils = {
    
    /**
     * Create video playlist
     */
    createPlaylist: function(videos, containerId) {
        const $container = $(`#${containerId}`);
        if (!$container.length) return;
        
        let playlistHTML = '<div class="rp-video-playlist">';
        
        videos.forEach((video, index) => {
            playlistHTML += `
                <div class="playlist-item" data-video-index="${index}">
                    <div class="playlist-thumbnail">
                        <img src="${video.thumbnail}" alt="${video.title}">
                        <div class="play-overlay">▶</div>
                    </div>
                    <div class="playlist-info">
                        <h4 class="playlist-title">${video.title}</h4>
                        <p class="playlist-duration">${video.duration}</p>
                    </div>
                </div>
            `;
        });
        
        playlistHTML += '</div>';
        $container.html(playlistHTML);
        
        // Bind playlist events
        $container.find('.playlist-item').on('click', function() {
            const index = $(this).data('video-index');
            const video = videos[index];
            RecruitProVideoPlayer.openVideoModal(video);
        });
    },
    
    /**
     * Generate video thumbnail
     */
    generateThumbnail: function(videoUrl, provider) {
        switch (provider) {
            case 'youtube':
                const youtubeId = RecruitProVideoPlayer.extractYouTubeId(videoUrl);
                return `https://img.youtube.com/vi/${youtubeId}/maxresdefault.jpg`;
            
            case 'vimeo':
                // Would need API call to get Vimeo thumbnail
                return '';
            
            default:
                return '';
        }
    }
};