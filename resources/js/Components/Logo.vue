<script setup>
import { computed, ref } from 'vue'
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

const props = defineProps({
    size: {
        type: String,
        default: '7rem'// Default size for the logo
    }
})

const hasError = ref(false)
const hasDarkError = ref(false)

function handleError() {
    hasError.value = true
}

function handleDarkError() {
    hasDarkError.value = true
}
</script>


<template>
    <figure v-if="(logoUrl && !hasError) || (darkLogoUrl && !hasDarkError)" class="flex justify-center items-center">
        <!-- Light mode logo -->
        <img v-if="logoUrl && !hasError" :src="logoUrl" alt="Application Logo"
            class="w-auto object-contain dark:hidden" :style="{ maxHeight: size }" @error="handleError" />
        <!-- Dark mode logo -->
        <img v-if="darkLogoUrl && !hasDarkError" :src="darkLogoUrl" alt="Application Dark Logo"
            class="w-auto object-contain hidden dark:block" :style="{ maxHeight: size }" @error="handleDarkError" />
    </figure>

    <h1 v-else class="text-3xl font-extrabold text-gray-800 dark:text-white text-center">
        {{ page.props.personalisation?.app_name || 'GuacPanel' }}
    </h1>
</template>
