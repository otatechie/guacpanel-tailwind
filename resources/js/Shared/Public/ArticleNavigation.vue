<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    links: {
        type: Array,
        default: () => []
    }
})

const activeSection = ref('')

const updateActiveSection = () => {
    const scrollPosition = window.scrollY + 150
    let currentActive = ''

    for (const link of props.links) {
        const sectionId = link.href.replace('#', '')
        const section = document.getElementById(sectionId)

        if (section) {
            const sectionTop = section.offsetTop

            // If this section is at or above the scroll position, mark it as active
            if (scrollPosition >= sectionTop) {
                currentActive = link.href
            }
        }
    }

    // If no section is found, default to the first one
    if (!currentActive && props.links.length > 0) {
        currentActive = props.links[0].href
    }

    activeSection.value = currentActive
}

onMounted(() => {
    window.addEventListener('scroll', updateActiveSection)
    updateActiveSection()
})

onUnmounted(() => {
    window.removeEventListener('scroll', updateActiveSection)
})
</script>

<template>
    <aside class="hidden xl:block w-64 fixed right-6 top-20">
        <div class="border border-green-500/30 dark:border-green-500/20 bg-white dark:bg-gray-900 p-4 font-mono">
            <!-- Header -->
            <div class="pb-3 border-b border-green-200 dark:border-green-800 mb-3">
                <div class="flex items-center">
                    <h2 class="text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wider">
                        Contents
                    </h2>
                </div>
            </div>

            <!-- Navigation -->
            <nav>
                <ul class="space-y-1">
                    <li v-for="link in links" :key="link.href">
                        <a
                            :href="link.href"
                            class="group relative block py-2 px-2 text-xs font-bold uppercase tracking-wide transition-all duration-150 border-l-2"
                            :class="[
                                link.href === activeSection
                                    ? 'text-green-600 dark:text-green-400 border-green-500'
                                    : 'text-gray-600 dark:text-gray-400 hover:text-green-700 dark:hover:text-green-400 hover:bg-green-50/10 dark:hover:bg-green-900/5 border-transparent hover:border-green-200 dark:hover:border-green-800'
                            ]">
                            <!-- Active indicator -->
                            <span v-if="link.href === activeSection" class="inline-block text-green-500"></span>
                            {{ link.text }}
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>
