// Check for saved theme preference or system preference
function initializeTheme() {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

// Toggle theme function
function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
}

// Listen for system theme changes
function setupThemeListener() {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ({ matches }) => {
        if (!('theme' in localStorage)) {
            if (matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
}

// Initialize when the file loads
initializeTheme();
setupThemeListener();

// Export for use in other files
export { toggleTheme };
