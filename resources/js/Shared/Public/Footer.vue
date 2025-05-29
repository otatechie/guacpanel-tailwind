<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const footerText = computed(() => {
    return page.props.personalisation?.footer_text || ''
})

const copyrightText = computed(() => {
    return page.props.personalisation?.copyright_text || `© ${new Date().getFullYear()} All rights reserved `
})

const version = computed(() => {
    return page.props.app?.version || 'v1.0.0'
})
</script>

<template>
    <footer
        class="bg-gray-100 border-t border-gray-200 text-gray-500 dark:border-t dark:border-gray-700 dark:bg-gray-800">
        <div class="mx-auto px-2 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-6 text-xs">
                    <p>GuacPanel</p>
                    <span class="text-gray-400">{{ version }}</span>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                    <span class="text-gray-400">Made with ❤️ in Accra</span>
                </div>
            </div>
        </div>
    </footer>
</template>
