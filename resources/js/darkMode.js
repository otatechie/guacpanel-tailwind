/**
 * Theme utilities.
 *
 * Theme preference:
 *   - 'light'  → Always use light mode
 *   - 'dark'   → Always use dark mode
 *   - 'system' → Follow the OS-level preference
 *
 * Effective theme:
 *   - 'light' | 'dark' (actual theme applied to the document)
 */

const THEME_LIGHT = "light";
const THEME_DARK = "dark";
const THEME_SYSTEM = "system";
const THEME_STORAGE_KEY = "theme";
const DARK_MEDIA_QUERY = "(prefers-color-scheme: dark)";

const NEXT_THEME = {
  [THEME_LIGHT]: THEME_DARK,
  [THEME_DARK]: THEME_SYSTEM,
  [THEME_SYSTEM]: THEME_LIGHT,
};

const NEXT_THEME_TEXT = {
  [THEME_LIGHT]: "Dark Mode",
  [THEME_DARK]: "System Mode",
  [THEME_SYSTEM]: "Light Mode",
};

const NEXT_THEME_ICON = {
  [THEME_LIGHT]: "moon",
  [THEME_DARK]: "system",
  [THEME_SYSTEM]: "sun",
};

const root = document.documentElement;

const systemPrefersDark = () => window.matchMedia(DARK_MEDIA_QUERY).matches;

const applyEffectiveTheme = (theme) =>
  root.classList.toggle("dark", theme === THEME_DARK);

/**
 * Reads the user's saved theme preference from localStorage.
 *
 * @returns {'light' | 'dark' | 'system'}
 */
function getCurrentThemePreference() {
  const pref = localStorage.getItem(THEME_STORAGE_KEY);
  return pref === THEME_DARK || pref === THEME_LIGHT ? pref : THEME_SYSTEM;
}

/**
 * Resolves the effective theme (what is actually applied).
 *
 * @returns {'light' | 'dark'}
 */
function getEffectiveTheme() {
  const pref = getCurrentThemePreference();
  return pref === THEME_SYSTEM
    ? systemPrefersDark()
      ? THEME_DARK
      : THEME_LIGHT
    : pref;
}

/**
 * Next theme in cycle: light → dark → system → light
 *
 * @param {'light' | 'dark' | 'system'} current
 * @returns {'light' | 'dark' | 'system'}
 */
function getNextTheme(current) {
  return NEXT_THEME[current] || THEME_LIGHT;
}

/**
 * Label for the next theme in the cycle.
 *
 * @param {'light' | 'dark' | 'system'} current
 * @returns {string}
 */
function getNextThemeText(current) {
  return NEXT_THEME_TEXT[current] || NEXT_THEME_TEXT[THEME_SYSTEM];
}

/**
 * Icon key for the next theme in the cycle.
 *
 * @param {'light' | 'dark' | 'system'} current
 * @returns {string}
 */
function getNextThemeIcon(current) {
  return NEXT_THEME_ICON[current] || NEXT_THEME_ICON[THEME_SYSTEM];
}

/**
 * Current theme state.
 *
 * @returns {{
 *   currentPreference: 'light' | 'dark' | 'system',
 *   effectiveTheme: 'light' | 'dark',
 *   nextThemeText: string,
 *   nextThemeIcon: string,
 *   isSystem: boolean
 * }}
 */
function getCurrentThemeState() {
  const currentPreference = getCurrentThemePreference();
  const effectiveTheme = getEffectiveTheme();

  return {
    currentPreference,
    effectiveTheme,
    nextThemeText: getNextThemeText(currentPreference),
    nextThemeIcon: getNextThemeIcon(currentPreference),
    isSystem: currentPreference === THEME_SYSTEM,
  };
}

/**
 * Applies the given theme preference and persists it.
 *
 * @param {'light' | 'dark' | 'system'} preference
 */
function setTheme(preference) {
  if (preference === THEME_DARK) {
    localStorage.setItem(THEME_STORAGE_KEY, THEME_DARK);
  } else if (preference === THEME_LIGHT) {
    localStorage.setItem(THEME_STORAGE_KEY, THEME_LIGHT);
  } else if (preference === THEME_SYSTEM) {
    localStorage.removeItem(THEME_STORAGE_KEY);
  } else {
    preference = THEME_LIGHT;
    localStorage.setItem(THEME_STORAGE_KEY, THEME_LIGHT);
  }

  applyEffectiveTheme(
    preference === THEME_SYSTEM ? getEffectiveTheme() : preference,
  );
}

/**
 * Initializes the theme on first load.
 *
 * @returns {'light' | 'dark' | 'system'}
 */
function initializeTheme() {
  const preference = getCurrentThemePreference();
  setTheme(preference);
  return preference;
}

/**
 * Subscribes to system (OS-level) theme changes when using 'system'.
 */
function setupThemeListener() {
  window
    .matchMedia(DARK_MEDIA_QUERY)
    .addEventListener("change", ({ matches }) => {
      if (getCurrentThemePreference() !== THEME_SYSTEM) return;

      const effectiveTheme = matches ? THEME_DARK : THEME_LIGHT;
      applyEffectiveTheme(effectiveTheme);

      window.dispatchEvent(
        new CustomEvent("themeChanged", {
          detail: {
            preference: THEME_SYSTEM,
            effectiveTheme,
          },
        }),
      );
    });
}

/**
 * Cycle preference: light → dark → system → light
 *
 * @returns {{
 *   currentPreference: 'light' | 'dark' | 'system',
 *   effectiveTheme: 'light' | 'dark',
 *   nextThemeText: string,
 *   nextThemeIcon: string
 * }}
 */
function cycleTheme() {
  const nextTheme = getNextTheme(getCurrentThemePreference());
  setTheme(nextTheme);

  const effectiveTheme = getEffectiveTheme();

  window.dispatchEvent(
    new CustomEvent("themeChanged", {
      detail: {
        preference: nextTheme,
        effectiveTheme,
      },
    }),
  );

  return {
    currentPreference: nextTheme,
    effectiveTheme,
    nextThemeText: getNextThemeText(nextTheme),
    nextThemeIcon: getNextThemeIcon(nextTheme),
  };
}

/**
 * Toggle between light and dark (ignores 'system').
 *
 * @returns {{
 *   currentPreference: 'light' | 'dark',
 *   effectiveTheme: 'light' | 'dark'
 * }}
 */
function toggleTheme() {
  const current = getCurrentThemePreference();
  const nextTheme = current === THEME_DARK ? THEME_LIGHT : THEME_DARK;

  setTheme(nextTheme);

  return {
    currentPreference: nextTheme,
    effectiveTheme: nextTheme,
  };
}

/**
 * Reset preference back to 'system'.
 *
 * @returns {{
 *   currentPreference: 'system',
 *   effectiveTheme: 'light' | 'dark'
 * }}
 */
function resetToSystemTheme() {
  setTheme(THEME_SYSTEM);
  const effectiveTheme = getEffectiveTheme();

  return {
    currentPreference: THEME_SYSTEM,
    effectiveTheme,
  };
}

// Initialize immediately on load and attach system listener.
initializeTheme();
setupThemeListener();

export {
  initializeTheme,
  getCurrentThemePreference,
  getEffectiveTheme,
  setTheme,
  toggleTheme,
  resetToSystemTheme,
  cycleTheme,
  getCurrentThemeState,
};
