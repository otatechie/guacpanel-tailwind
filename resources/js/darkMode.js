function initializeTheme() {
    const userPreference = localStorage.getItem('theme')

    if (userPreference === 'dark') {
        document.documentElement.classList.add('dark')
        return 'dark'
    } else if (userPreference === 'light') {
        document.documentElement.classList.remove('dark')
        return 'light'
    } else {
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        if (systemPrefersDark) {
            document.documentElement.classList.add('dark')
            return 'system'
        } else {
            document.documentElement.classList.remove('dark')
            return 'system'
        }
    }
}

function getCurrentThemePreference() {
    const userPreference = localStorage.getItem('theme')
    if (userPreference === 'dark' || userPreference === 'light') {
        return userPreference
    }
    return 'system'
}

function getEffectiveTheme() {
    const preference = getCurrentThemePreference()
    if (preference === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }
    return preference
}

function setTheme(preference) {
    switch (preference) {
        case 'dark':
            document.documentElement.classList.add('dark')
            localStorage.setItem('theme', 'dark')
            break
        case 'light':
            document.documentElement.classList.remove('dark')
            localStorage.setItem('theme', 'light')
            break
        case 'system': {
            localStorage.removeItem('theme')
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
            if (systemPrefersDark) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
            break
        }
    }
}

function setupThemeListener() {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ({ matches }) => {
        const currentPreference = getCurrentThemePreference()

        if (currentPreference === 'system') {
            if (matches) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }

            window.dispatchEvent(
                new CustomEvent('themeChanged', {
                    detail: {
                        preference: 'system',
                        effectiveTheme: matches ? 'dark' : 'light'
                    }
                })
            )
        }
    })
}

function getNextTheme(current) {
    switch (current) {
        case 'light':
            return 'dark'
        case 'dark':
            return 'system'
        case 'system':
            return 'light'
        default:
            return 'light'
    }
}

function getNextThemeText(current) {
    switch (current) {
        case 'light':
            return 'Dark Mode'
        case 'dark':
            return 'System Mode'
        case 'system':
            return 'Light Mode'
        default:
            return 'Light Mode'
    }
}

function getNextThemeIcon(current) {
    switch (current) {
        case 'light':
            return 'moon'
        case 'dark':
            return 'system'
        case 'system':
            return 'sun'
        default:
            return 'sun'
    }
}

function cycleTheme() {
    const currentPreference = getCurrentThemePreference()
    const nextTheme = getNextTheme(currentPreference)

    setTheme(nextTheme)

    window.dispatchEvent(
        new CustomEvent('themeChanged', {
            detail: {
                preference: nextTheme,
                effectiveTheme: getEffectiveTheme()
            }
        })
    )

    return {
        currentPreference: nextTheme,
        effectiveTheme: getEffectiveTheme(),
        nextThemeText: getNextThemeText(nextTheme),
        nextThemeIcon: getNextThemeIcon(nextTheme)
    }
}

function getCurrentThemeState() {
    const currentPreference = getCurrentThemePreference()
    const effectiveTheme = getEffectiveTheme()

    return {
        currentPreference,
        effectiveTheme,
        nextThemeText: getNextThemeText(currentPreference),
        nextThemeIcon: getNextThemeIcon(currentPreference),
        isSystem: currentPreference === 'system'
    }
}

function toggleTheme() {
    const currentPreference = getCurrentThemePreference()
    const nextTheme = currentPreference === 'dark' ? 'light' : 'dark'
    setTheme(nextTheme)

    return {
        currentPreference: nextTheme,
        effectiveTheme: nextTheme
    }
}

function resetToSystemTheme() {
    setTheme('system')

    return {
        currentPreference: 'system',
        effectiveTheme: getEffectiveTheme()
    }
}

initializeTheme()
setupThemeListener()

export {
    initializeTheme,
    getCurrentThemePreference,
    getEffectiveTheme,
    setTheme,
    toggleTheme,
    resetToSystemTheme,
    cycleTheme,
    getCurrentThemeState
}
