document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;


    /*
    |--------------------------------------------------------------------------
    | Hero entrance
    |--------------------------------------------------------------------------
    */

    const hero = document.querySelector('.hero-card');

    if (hero) {
        if (reduceMotion) {
            hero.classList.add('is-loaded');
        } else {
            requestAnimationFrame(() => {
                hero.classList.add('is-loaded');
            });
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Elements to reveal on scroll
    |--------------------------------------------------------------------------
    */

    const selectors = [
        '.platform-strip__inner',
        '.section-head',
        '.featured-card',
        '.live-release',
        '.about-biography',
        '.gallery__item',
        '.booking-shell',
    ];

    const elements = document.querySelectorAll(
        selectors.join(',')
    );


    elements.forEach((element, index) => {
        element.classList.add('js-reveal');

        /*
         * Small stagger.
         * Resets every 4 elements so large galleries don't
         * take forever to appear.
         */
        const delay = (index % 4) * 80;

        element.style.setProperty(
            '--reveal-delay',
            `${delay}ms`
        );
    });


    /*
    |--------------------------------------------------------------------------
    | Reduced motion
    |--------------------------------------------------------------------------
    */

    if (reduceMotion) {
        elements.forEach((element) => {
            element.classList.add('is-visible');
        });

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Intersection Observer
    |--------------------------------------------------------------------------
    */

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');

                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.12,
            rootMargin: '0px 0px -8% 0px',
        }
    );


    elements.forEach((element) => {
        observer.observe(element);
    });
});

document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | Audio debugging
    |--------------------------------------------------------------------------
    | While true, a failed audio load prints a full diagnosis to the Console.
    | Set to false once playback works everywhere; the code can stay in place.
    */

    const AUDIO_DEBUG = true;

    const MEDIA_ERROR_NAMES = {
        1: 'MEDIA_ERR_ABORTED',
        2: 'MEDIA_ERR_NETWORK',
        3: 'MEDIA_ERR_DECODE',
        4: 'MEDIA_ERR_SRC_NOT_SUPPORTED',
    };

    const players = document.querySelectorAll(
        '[data-audio-player]'
    );

    const formatTime = (seconds) => {
        if (!Number.isFinite(seconds)) {
            return '0:00';
        }

        const minutes = Math.floor(seconds / 60);

        const remainingSeconds = Math.floor(seconds % 60)
            .toString()
            .padStart(2, '0');

        return `${minutes}:${remainingSeconds}`;
    };


    /*
    |--------------------------------------------------------------------------
    | Diagnose a failed audio source
    |--------------------------------------------------------------------------
    */

    const probeAudioSource = async (audio) => {

        const url = audio.currentSrc || audio.src;

        console.group('Audio diagnostics');

        console.log('Page origin:', location.origin);
        console.log('Audio URL:', url);

        try {
            console.log(
                'Same origin as page:',
                new URL(url, location.href).origin === location.origin
            );
        } catch (error) {
            console.warn('Could not parse the audio URL:', error);
        }

        console.log(
            'MediaError:',
            audio.error
                ? `${audio.error.code} ${MEDIA_ERROR_NAMES[audio.error.code] || ''}`
                : 'none',
            audio.error ? audio.error.message : ''
        );

        console.log(
            'networkState:',
            audio.networkState,
            '| readyState:',
            audio.readyState
        );

        [
            'audio/mpeg',
            'audio/mp4',
            'audio/aac',
            'audio/wav',
            'audio/ogg',
        ].forEach((type) => {
            console.log(
                `canPlayType(${type}):`,
                audio.canPlayType(type) || '(no)'
            );
        });

        try {
            /*
             * Same kind of request the <audio> element makes.
             * A healthy server answers 206 with a Content-Range header.
             */
            const response = await fetch(url, {
                headers: { Range: 'bytes=0-1' },
                cache: 'no-store',
            });

            console.log(
                'Range probe status:',
                response.status,
                response.statusText
            );

            [
                'content-type',
                'content-length',
                'content-range',
                'accept-ranges',
                'content-encoding',
            ].forEach((name) => {
                console.log(`${name}:`, response.headers.get(name));
            });

            /*
             * If the server ignored the Range header it is sending the
             * whole file. Stop that download straight away.
             */
            if (response.body) {
                response.body.cancel();
            }

        } catch (error) {
            console.warn(
                'Range probe failed. This usually means the request was ' +
                'blocked (mixed content, CORS) or the server is unreachable:',
                error
            );
        }

        console.groupEnd();
    };


    players.forEach((player) => {

        const audio = player.querySelector('[data-audio]');
        const playButton = player.querySelector('[data-audio-play]');
        const progress = player.querySelector('[data-audio-progress]');

        const currentTimeElement = player.querySelector(
            '[data-current-time]'
        );

        const durationElement = player.querySelector(
            '[data-duration]'
        );

        const playIcon = player.querySelector('[data-play-icon]');
        const pauseIcon = player.querySelector('[data-pause-icon]');

        const muteButton = player.querySelector('[data-audio-mute]');
        const volumeIcon = player.querySelector('[data-volume-icon]');
        const mutedIcon = player.querySelector('[data-muted-icon]');


        if (!audio || !playButton) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Debug source
        |--------------------------------------------------------------------------
        */

        if (AUDIO_DEBUG) {
            console.log('Audio source:', audio.src);
        }


        /*
        |--------------------------------------------------------------------------
        | Error state
        |--------------------------------------------------------------------------
        */

        let statusElement = null;

        const showError = () => {

            player.classList.add('is-error');

            playButton.setAttribute(
                'aria-label',
                'Audio unavailable'
            );

            if (!statusElement) {
                statusElement = document.createElement('p');
                statusElement.className = 'small text-danger mt-2 mb-0';
                statusElement.setAttribute('role', 'alert');
                statusElement.textContent =
                    'This audio could not be loaded. Please try again later.';

                player.appendChild(statusElement);
            }

        };

        const clearError = () => {

            player.classList.remove('is-error');

            if (statusElement) {
                statusElement.remove();
                statusElement = null;
            }

            playButton.setAttribute(
                'aria-label',
                audio.paused ? 'Play audio' : 'Pause audio'
            );

        };

        const handleError = () => {

            if (!audio.error) {
                return;
            }

            console.error(
                'HTML audio error:',
                audio.error
            );

            console.error(
                'Failed URL:',
                audio.currentSrc || audio.src
            );

            showError();

            if (AUDIO_DEBUG) {
                probeAudioSource(audio);
            }

        };


        /*
        |--------------------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------------------
        */

        const updateDuration = () => {
            if (!durationElement) {
                return;
            }

            durationElement.textContent = formatTime(
                audio.duration
            );
        };


        if (audio.readyState >= 1) {
            updateDuration();
        }

        audio.addEventListener('loadedmetadata', () => {
            clearError();
            updateDuration();
        });

        audio.addEventListener(
            'durationchange',
            updateDuration
        );


        /*
        |--------------------------------------------------------------------------
        | Play / Pause
        |--------------------------------------------------------------------------
        */

        playButton.addEventListener('click', async () => {

            if (!audio.paused) {
                audio.pause();
                return;
            }


            /*
             * Stop other players.
             */
            players.forEach((otherPlayer) => {

                const otherAudio = otherPlayer.querySelector(
                    '[data-audio]'
                );

                if (
                    otherAudio &&
                    otherAudio !== audio &&
                    !otherAudio.paused
                ) {
                    otherAudio.pause();
                }

            });


            try {

                /*
                 * Retry after a failed load, or start loading if nothing
                 * has been requested yet. An in-flight request is left alone.
                 */
                if (
                    audio.error ||
                    audio.networkState === audio.NETWORK_EMPTY
                ) {
                    audio.load();
                }

                await audio.play();

            } catch (error) {

                console.error(
                    'Audio playback failed:',
                    error
                );

                console.error(
                    'Audio URL:',
                    audio.currentSrc || audio.src
                );

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Playing state
        |--------------------------------------------------------------------------
        */

        audio.addEventListener('play', () => {

            player.classList.add('is-playing');

            if (playIcon) {
                playIcon.hidden = true;
            }

            if (pauseIcon) {
                pauseIcon.hidden = false;
            }

            playButton.setAttribute(
                'aria-label',
                'Pause audio'
            );

        });


        audio.addEventListener('pause', () => {

            player.classList.remove('is-playing');

            if (playIcon) {
                playIcon.hidden = false;
            }

            if (pauseIcon) {
                pauseIcon.hidden = true;
            }

            playButton.setAttribute(
                'aria-label',
                'Play audio'
            );

        });


        /*
        |--------------------------------------------------------------------------
        | Current position
        |--------------------------------------------------------------------------
        */

        audio.addEventListener('timeupdate', () => {

            if (currentTimeElement) {
                currentTimeElement.textContent = formatTime(
                    audio.currentTime
                );
            }

            if (!progress || !audio.duration) {
                return;
            }

            const percentage =
                (audio.currentTime / audio.duration) * 100;

            progress.value = percentage;

            progress.setAttribute(
                'aria-valuetext',
                `${formatTime(audio.currentTime)} of ${formatTime(audio.duration)}`
            );

            progress.style.setProperty(
                '--progress',
                `${percentage}%`
            );

        });


        /*
        |--------------------------------------------------------------------------
        | Seeking
        |--------------------------------------------------------------------------
        */

        if (progress) {

            progress.addEventListener('input', () => {

                if (!Number.isFinite(audio.duration)) {
                    return;
                }

                const percentage = Number(
                    progress.value
                );

                audio.currentTime =
                    audio.duration * (percentage / 100);

                progress.style.setProperty(
                    '--progress',
                    `${percentage}%`
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Mute
        |--------------------------------------------------------------------------
        */

        if (muteButton) {

            muteButton.addEventListener('click', () => {

                audio.muted = !audio.muted;

            });


            audio.addEventListener('volumechange', () => {

                const muted =
                    audio.muted ||
                    audio.volume === 0;

                if (volumeIcon) {
                    volumeIcon.hidden = muted;
                }

                if (mutedIcon) {
                    mutedIcon.hidden = !muted;
                }

                muteButton.setAttribute(
                    'aria-label',
                    muted
                        ? 'Unmute audio'
                        : 'Mute audio'
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | Ended
        |--------------------------------------------------------------------------
        */

        audio.addEventListener('ended', () => {

            audio.currentTime = 0;

            if (progress) {

                progress.value = 0;

                progress.style.setProperty(
                    '--progress',
                    '0%'
                );

            }

            if (currentTimeElement) {
                currentTimeElement.textContent = '0:00';
            }

        });


        /*
        |--------------------------------------------------------------------------
        | Media errors
        |--------------------------------------------------------------------------
        */

        audio.addEventListener('error', handleError);

        /*
         * With preload="metadata" the load can fail before this script runs,
         * so the error event has already been and gone. Check for it now.
         */
        if (audio.error) {
            handleError();
        }

    });

});

document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    /*
     * ------------------------------------------------------
     * HERO LOAD
     * ------------------------------------------------------
     */
    const hero = document.querySelector('.hero-card');

    if (hero) {
        requestAnimationFrame(() => {
            hero.classList.add('is-loaded');
        });
    }


    /*
     * ------------------------------------------------------
     * AUTOMATIC SCROLL REVEALS
     * ------------------------------------------------------
     */

    const revealSelectors = [
        '.section-head',
        '.featured-card',
        '.live-release',
        '.gallery__item',
        '.about-intro-copy',
        '.about-biography',
        '.booking-shell',
        '.platform-strip__inner',
        '.press-section',
        '.press-facts__item',
        '.press-music__item',
        '.press-photo',
        '.press-download'
    ];

    const revealItems = document.querySelectorAll(
        revealSelectors.join(',')
    );

    revealItems.forEach((element, index) => {
        element.classList.add('js-reveal');

        /*
         * Keep stagger short so long pages don't feel slow.
         */
        element.style.setProperty(
            '--reveal-delay',
            `${Math.min((index % 5) * 70, 280)}ms`
        );
    });


    /*
     * ------------------------------------------------------
     * INTERSECTION OBSERVER
     * ------------------------------------------------------
     */

    if (!reduceMotion && 'IntersectionObserver' in window) {

        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');

                    observer.unobserve(entry.target);
                });
            },
            {
                threshold: 0.12,
                rootMargin: '0px 0px -8% 0px'
            }
        );

        document
            .querySelectorAll('.js-reveal, .js-heading-reveal')
            .forEach(element => observer.observe(element));

    } else {

        document
            .querySelectorAll('.js-reveal, .js-heading-reveal')
            .forEach(element => {
                element.classList.add('is-visible');
            });
    }


    /*
     * ------------------------------------------------------
     * HERO PARALLAX
     * ------------------------------------------------------
     */

    if (hero && !reduceMotion) {

        let ticking = false;

        const updateHero = () => {
            const rect = hero.getBoundingClientRect();

            if (rect.bottom > 0) {
                const shift = Math.max(
                    -25,
                    Math.min(25, window.scrollY * 0.045)
                );

                hero.style.setProperty(
                    '--hero-parallax',
                    `${shift}px`
                );
            }

            ticking = false;
        };

        window.addEventListener(
            'scroll',
            () => {
                if (!ticking) {
                    requestAnimationFrame(updateHero);
                    ticking = true;
                }
            },
            {
                passive: true
            }
        );
    }


    /*
     * ------------------------------------------------------
     * HEADER SCROLL STATE
     * ------------------------------------------------------
     */

    const header = document.querySelector('.site-header');

    const updateHeader = () => {
        if (!header) return;

        header.classList.toggle(
            'is-scrolled',
            window.scrollY > 30
        );
    };

    updateHeader();

    window.addEventListener(
        'scroll',
        updateHeader,
        {
            passive: true
        }
    );


    /*
     * ------------------------------------------------------
     * AUDIO PLAYER PLAYING STATE
     * ------------------------------------------------------
     */

    document
        .querySelectorAll('.js-audio-player')
        .forEach(player => {

            const audio = player.querySelector('audio');

            if (!audio) {
                return;
            }

            audio.addEventListener('play', () => {
                player.classList.add('is-playing');
            });

            audio.addEventListener('pause', () => {
                player.classList.remove('is-playing');
            });

            audio.addEventListener('ended', () => {
                player.classList.remove('is-playing');
            });
        });
});