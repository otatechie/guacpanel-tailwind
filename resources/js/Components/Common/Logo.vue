<script setup>
import { computed, ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const logoUrl = computed(() => {
    const path = page.props.personalisation?.app_logo
    return path ? (path.startsWith('/storage/') ? path : `/storage/${path}`) : null
})

const darkLogoUrl = computed(() => {
    const path = page.props.personalisation?.app_logo_dark
    return path ? (path.startsWith('/storage/') ? path : `/storage/${path}`) : null
})

const appName = computed(
    () => page.props.personalisation?.app_name ?? page.props.settings?.appName ?? 'GuacPanel'
)

const props = defineProps({
    size: {
        type: String,
        default: null,
    },
    maxSize: {
        type: String,
        default: '8rem',
    },
})

const hasError = ref(false)
const hasDarkError = ref(false)
const isDark = ref(false)

const logoStyles = ref({
    maxWidth: props.maxSize,
    maxHeight: props.maxSize,
    width: 'auto',
    height: 'auto',
})

const activeLogo = computed(() => {
    if (isDark.value && darkLogoUrl.value && !hasDarkError.value) return darkLogoUrl.value
    if (logoUrl.value && !hasError.value) return logoUrl.value
    // Fallback to static logo files
    return isDark.value ? '/images/logo-dark.png' : '/images/logo.png'
})

function handleError() {
    if (isDark.value && darkLogoUrl.value) hasDarkError.value = true
    else hasError.value = true
}

function checkDarkMode() {
    isDark.value = document.documentElement.classList.contains('dark')
}

onMounted(() => {
    if (props.size) {
        logoStyles.value = {
            width: props.size,
            height: 'auto',
            maxWidth: props.maxSize,
            maxHeight: props.maxSize,
        }
    }

    checkDarkMode()

    // Watch for class changes on <html> to detect dark mode toggle
    const observer = new MutationObserver(checkDarkMode)
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
})
</script>

<template>
    <figure class="m-0 flex items-center justify-center p-0">
        <img
            v-if="activeLogo"
            :src="activeLogo"
            :alt="appName"
            :style="logoStyles"
            class="object-contain"
            @error="handleError" />

        <span v-else class="text-foreground text-xl font-bold">
            {{ appName }}
        </span>
    </figure>
</template>
