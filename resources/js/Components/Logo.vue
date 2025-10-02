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

const props = defineProps({
    size: {
        type: String,
        default: null
    },
    maxSize: {
        type: String,
        default: '8rem'
    }
})

const hasError = ref(false)
const hasDarkError = ref(false)
const logoStyles = ref({
    maxWidth: props.maxSize,
    maxHeight: props.maxSize,
    width: 'auto',
    height: 'auto'
})

function handleError() {
    hasError.value = true
}

onMounted(() => {
    if (props.size) {
        logoStyles.value = {
            width: props.size,
            height: 'auto',
            maxWidth: props.maxSize,
            maxHeight: props.maxSize
        }
    }
})
</script>

<template>
    <figure class="flex justify-center items-center m-0 p-0">
        <picture v-if="(logoUrl && !hasError) || (darkLogoUrl && !hasDarkError)">
            <!-- Dark mode logo -->
            <source
                v-if="darkLogoUrl && !hasDarkError"
                :srcset="darkLogoUrl"
                media="(prefers-color-scheme: dark)" />

            <!-- Light mode logo -->
            <img
                v-if="logoUrl && !hasError"
                :src="logoUrl"
                alt="Application Logo"
                :style="logoStyles"
                class="object-contain"
                @error="handleError" />
        </picture>

        <!-- Fallback -->
        <h1 v-else class="text-3xl font-extrabold text-gray-800 dark:text-white text-center">
            {{ page.props.personalisation?.app_name || 'GuacPanel' }}
        </h1>
    </figure>
</template>

<style scoped>
figure {
    margin: 0;
    padding: 0;
}

img {
    display: block;
}
</style>
