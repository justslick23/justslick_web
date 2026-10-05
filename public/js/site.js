(function () {
    'use strict';

    var STORAGE_KEY = 'js-theme';
    var root = document.documentElement;

    /* Theme toggle ------------------------------------------------------ */
    function currentTheme() {
        return root.getAttribute('data-bs-theme') === 'light' ? 'light' : 'dark';
    }

    function syncToggles() {
        var next = currentTheme() === 'dark' ? 'light' : 'dark';
        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.setAttribute('aria-label', 'Switch to ' + next + ' theme');
        });
    }

    function setTheme(theme) {
        root.setAttribute('data-bs-theme', theme);
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            // Storage unavailable (private mode); the theme still switches for this visit.
        }
        syncToggles();
    }

    document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
        });
    });

    syncToggles();

    /* Mobile menu ------------------------------------------------------- */
    var navToggle = document.querySelector('[data-nav-toggle]');
    var nav = document.getElementById('primary-nav');

    if (navToggle && nav) {
        function setMenu(open) {
            nav.classList.toggle('show', open);
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        navToggle.addEventListener('click', function () {
            setMenu(!nav.classList.contains('show'));
        });

        // Close the menu after choosing a link.
        nav.addEventListener('click', function (event) {
            if (event.target.closest('a')) {
                setMenu(false);
            }
        });

        // Escape closes the menu and returns focus to the button.
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && nav.classList.contains('show')) {
                setMenu(false);
                navToggle.focus();
            }
        });
    }
})();