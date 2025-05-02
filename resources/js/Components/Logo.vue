<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => page.props.personalisation?.app_logo || null)

const props = defineProps({
    // Controls the maximum height of the logo
    // Can be set to any valid CSS size value (e.g., '2.5rem', '40px', '3rem')
    size: {
        type: String,
        default: '2.5rem'// Default size for the logo
    }
})

const hasError = ref(false)

function handleError() {
    hasError.value = true
}
</script>


<template>
    <header class="flex justify-center items-center py-2">
        <figure v-if="logoUrl && !hasError" class="flex justify-center">
            <img :src="logoUrl" alt="Application Logo" class="w-auto object-contain" :style="{ maxHeight: size }"
                @error="handleError" />
        </figure>

        <h1 v-else class="text-3xl font-extrabold text-gray-800 dark:text-white">
            {{ page.props.personalisation?.app_name || 'GuacPanel' }}
        </h1>
    </header>
</template>
