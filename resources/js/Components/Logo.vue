<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => page.props.personalisation?.app_logo || null)
const darkLogoUrl = computed(() => page.props.personalisation?.app_logo_dark || null)

const props = defineProps({
    // Can be set to any valid CSS size value (e.g., '2.5rem', '40px', '3rem')
    size: {
        type: String,
        default: '4rem'// Default size for the logo
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
    <header class="flex justify-center items-center py-2">
        <figure v-if="(logoUrl && !hasError) || (darkLogoUrl && !hasDarkError)" class="flex justify-center">
            <!-- Light mode logo -->
            <img v-if="logoUrl && !hasError" :src="logoUrl" alt="Application Logo"
                class="w-auto object-contain dark:hidden" :style="{ maxHeight: size }" @error="handleError" />
            <!-- Dark mode logo -->
            <img v-if="darkLogoUrl && !hasDarkError" :src="darkLogoUrl" alt="Application Logo"
                class="w-auto object-contain hidden dark:block" :style="{ maxHeight: size }" @error="handleDarkError" />
        </figure>

        <h1 v-else class="text-3xl font-extrabold text-gray-800 dark:text-white">
            {{ page.props.personalisation?.app_name || 'GuacPanel' }}
        </h1>
    </header>
</template>
