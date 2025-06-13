<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const version = ref('v1.0.0')
const personalisation = page.props.personalisation || {}

onMounted(async () => {
    try {
        const response = await fetch('https://api.github.com/repos/otatechie/guacpanel-tailwind/releases/latest')
        const data = await response.json()
        if (data.tag_name) {
            version.value = data.tag_name
        }
    } catch (error) {
        version.value = 'v1.0.0'
    }
})
</script>

<template>
    <footer
        class="bg-gray-100 border-t border-gray-200 text-gray-500 dark:border-t dark:border-gray-700 dark:bg-gray-800">
        <div class="mx-auto px-2 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2 text-xs">
                    <p>GuacPanel</p>
                    <span class="text-gray-400">{{ version }}</span>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                    <p class="text-gray-400">{{ personalisation.copyright_text || '© ' + new Date().getFullYear() + ' All rights reserved.' }}</p>
                    <a href="https://github.com/otatechie/guacpanel-tailwind" target="_blank" rel="noopener noreferrer"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                        aria-label="GitHub">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 0C5.374 0 0 5.373 0 12 0 17.302 3.438 21.8 8.207 23.387c.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
