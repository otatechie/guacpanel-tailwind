<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const appName = computed(() => {
    return page.props.personalisation?.app_name || 'Your App Name'
})

const footerText = computed(() => {
    return page.props.personalisation?.footer_text || ''
})

const copyrightText = computed(() => {
    return page.props.personalisation?.copyright_text || `© ${new Date().getFullYear()} All rights reserved `
})
</script>

<template>
    <footer
        class="bg-gray-100 border-t border-gray-200 text-gray-500 dark:border-t dark:border-gray-700 dark:bg-gray-800">
        <div class="mx-auto px-2 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-6 text-xs">
                    <p>{{ appName }}</p>
                </div>
                <div class="text-xs">
                    {{ copyrightText }}
                </div>
            </div>
        </div>
    </footer>
</template>
