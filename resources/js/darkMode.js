function initializeTheme() {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    }
}

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

initializeTheme();
setupThemeListener();

function resetToSystemTheme() {
    localStorage.removeItem('theme');
    initializeTheme();
}

let currentPreference = localStorage.theme || 'system';

function getNextTheme(current) {
    switch (current) {
        case 'light':
            return 'dark';
        case 'dark':
            return 'system';
        case 'system':
            return 'light';
    }
}

function getNextThemeText(current) {
    switch (current) {
        case 'light':
            return 'Dark mode';
        case 'dark':
            return 'System mode';
        case 'system':
            return 'Light mode';
    }
}

function getNextThemeIcon(current) {
    switch (current) {
        case 'light':
            return 'moon';
        case 'dark':
            return 'system';
        case 'system':
            return 'sun';
    }
}

function cycleTheme() {
    const nextTheme = getNextTheme(currentPreference);
    currentPreference = nextTheme;

    switch (nextTheme) {
        case 'light':
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
            break;
        case 'dark':
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
            break;
        case 'system':
            localStorage.removeItem('theme');
            initializeTheme();
            break;
    }

    return {
        currentPreference: nextTheme,
        nextThemeText: getNextThemeText(nextTheme),
        nextThemeIcon: getNextThemeIcon(nextTheme)
    };
}

function getCurrentThemeState() {
    return {
        currentPreference,
        nextThemeText: getNextThemeText(currentPreference),
        nextThemeIcon: getNextThemeIcon(currentPreference)
    };
}

export {
    toggleTheme,
    resetToSystemTheme,
    cycleTheme,
    getCurrentThemeState
};
