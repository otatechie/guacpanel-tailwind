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
    }
]

export function initializeTheme() {
    const savedColor = localStorage.getItem('theme-color') || 'purple'
    const selectedColorObj = colors.find(c => c.value === savedColor)
    
    if (selectedColorObj) {
        const root = document.documentElement
        root.style.setProperty('--primary-gradient-from', selectedColorObj.gradientFrom)
        root.style.setProperty('--primary-gradient-to', selectedColorObj.gradientTo)
        root.style.setProperty('--primary-color', selectedColorObj.primary)
        root.style.setProperty('--primary-hover', selectedColorObj.hover)
        root.style.setProperty('--primary-color-rgb', selectedColorObj.rgb)
    }
}

export { colors } 