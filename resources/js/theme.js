const STORAGE_KEY = 'js-theme';
const root = document.documentElement;

function currentTheme() {
    return root.getAttribute('data-bs-theme') === 'light' ? 'light' : 'dark';
}

function syncToggles() {
    const next = currentTheme() === 'dark' ? 'light' : 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', `Switch to ${next} theme`);
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

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        setTheme(currentTheme() === 'dark' ? 'light' : 'dark');
    });
});

syncToggles();