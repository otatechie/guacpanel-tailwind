const presets = {
    modern: {
        purple: {
            name: 'Modern Purple',
            value: 'purple',
            gradientFrom: '#a855f7',
            gradientTo: '#c084fc',
            primary: '#a855f7',
            hover: '#9333ea',
            rgb: '168, 85, 247'
        },
        blue: {
            name: 'Modern Blue',
            value: 'blue',
            gradientFrom: '#3b82f6',
            gradientTo: '#60a5fa',
            primary: '#3b82f6',
            hover: '#2563eb',
            rgb: '59, 130, 246'
        }
    },
    vibrant: {
        purple: {
            name: 'Vibrant Purple',
            value: 'purple',
            gradientFrom: '#c026d3',
            gradientTo: '#e879f9',
            primary: '#c026d3',
            hover: '#a21caf',
            rgb: '192, 38, 211'
        },
        blue: {
            name: 'Vibrant Blue',
            value: 'blue',
            gradientFrom: '#2563eb',
            gradientTo: '#3b82f6',
            primary: '#2563eb',
            hover: '#1d4ed8',
            rgb: '37, 99, 235'
        }
    }
}

const colors = [
    {
        name: 'Purple',
        value: 'purple',
        gradientFrom: '#a855f7',
        gradientTo: '#c084fc',
        primary: '#a855f7',
        hover: '#9333ea',
        rgb: '168, 85, 247'
    },
    {
        name: 'Blue',
        value: 'blue',
        gradientFrom: '#3b82f6',
        gradientTo: '#60a5fa',
        primary: '#3b82f6',
        hover: '#2563eb',
        rgb: '59, 130, 246'
    },
    {
        name: 'Green',
        value: 'green',
        gradientFrom: '#22c55e',
        gradientTo: '#4ade80',
        primary: '#22c55e',
        hover: '#16a34a',
        rgb: '34, 197, 94'
    },
    {
        name: 'Orange',
        value: 'orange',
        gradientFrom: '#f97316',
        gradientTo: '#fb923c',
        primary: '#f97316',
        hover: '#ea580c',
        rgb: '249, 115, 22'
    },
    {
        name: 'Yellow',
        value: 'yellow',
        gradientFrom: '#eab308',
        gradientTo: '#facc15',
        primary: '#eab308',
        hover: '#ca8a04',
        rgb: '234, 179, 8'
    },
    {
        name: 'Cyan',
        value: 'cyan',
        gradientFrom: '#06b6d4',
        gradientTo: '#22d3ee',
        primary: '#06b6d4',
        hover: '#0891b2',
        rgb: '6, 182, 212'
    }
]

function addThemeTransitions() {
    const root = document.documentElement
    root.style.setProperty('--theme-transition', 'all 0.3s ease-in-out')

    const transitionStyle = document.createElement('style')
    transitionStyle.textContent = `
        * {
            transition: var(--theme-transition);
        }
        [class*="text-primary"],
        [class*="bg-primary"],
        [class*="border-primary"],
        [class*="ring-primary"] {
            transition: var(--theme-transition);
        }
    `
    document.head.appendChild(transitionStyle)
}

function adjustThemeIntensity(intensity = 1) {
    const root = document.documentElement
    const currentColor = localStorage.getItem('theme-color') || 'cyan'
    const selectedColorObj = colors.find(c => c.value === currentColor)

    if (selectedColorObj) {
        const adjustColor = color => {
            const rgb = color.match(/\d+/g).map(Number)
            return rgb.map(v => Math.min(255, Math.round(v * intensity))).join(', ')
        }

        root.style.setProperty('--primary-color-rgb', adjustColor(selectedColorObj.rgb))
        localStorage.setItem('theme-intensity', intensity.toString())
    }
}

function applyThemePreset(presetName = 'modern') {
    const currentColor = localStorage.getItem('theme-color') || 'purple'
    const preset = presets[presetName]?.[currentColor]

    if (preset) {
        const root = document.documentElement
        root.style.setProperty('--primary-gradient-from', preset.gradientFrom)
        root.style.setProperty('--primary-gradient-to', preset.gradientTo)
        root.style.setProperty('--primary-color', preset.primary)
        root.style.setProperty('--primary-hover', preset.hover)
        root.style.setProperty('--primary-color-rgb', preset.rgb)

        localStorage.setItem('theme-preset', presetName)
    }
}

function applyThemeColor(color) {
    const selectedColorObj = colors.find(c => c.value === color)
    if (selectedColorObj) {
        const root = document.documentElement
        root.style.setProperty('--primary-gradient-from', selectedColorObj.gradientFrom)
        root.style.setProperty('--primary-gradient-to', selectedColorObj.gradientTo)
        root.style.setProperty('--primary-color', selectedColorObj.primary)
        root.style.setProperty('--primary-hover', selectedColorObj.hover)
        root.style.setProperty('--primary-color-rgb', selectedColorObj.rgb)
    }
}

export function initializeTheme() {
    const savedColor = localStorage.getItem('theme-color') || 'cyan'
    const savedPreset = localStorage.getItem('theme-preset')
    const savedIntensity = parseFloat(localStorage.getItem('theme-intensity') || '1')

    addThemeTransitions()

    if (savedPreset && presets[savedPreset]) {
        applyThemePreset(savedPreset)
    } else {
        applyThemeColor(savedColor)
    }

    if (savedIntensity !== 1) {
        adjustThemeIntensity(savedIntensity)
    }
}

export { colors, presets, applyThemePreset, adjustThemeIntensity, applyThemeColor }
