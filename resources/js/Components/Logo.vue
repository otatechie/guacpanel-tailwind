<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const logoUrl = computed(() => page.props.personalisation?.app_logo || null)

const hasError = ref(false)
const imageHeight = ref('2.5rem') 

function handleError() {
    hasError.value = true
}
</script>


<template>
    <header class="flex justify-center items-center py-2">
        <figure v-if="logoUrl && !hasError" class="flex justify-center">
            <img :src="logoUrl" alt="Application Logo" class="h-10 w-auto object-contain"
                :style="{ maxHeight: imageHeight }" @error="handleError" />
        </figure>

        <h1 v-else class="text-xl font-semibold text-gray-800 dark:text-white">
            {{ page.props.personalisation?.app_name || 'Application' }}
        </h1>
    </header>
</template>
