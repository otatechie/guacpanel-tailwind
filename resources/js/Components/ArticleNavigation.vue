<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    links: {
        type: Array,
        required: true
    }
})

const activeSection = ref('')

const updateActiveSection = () => {
    const sections = props.links.map(link => document.getElementById(link.id))
    const scrollPosition = window.scrollY + 100 // Add offset for header

    for (let i = sections.length - 1; i >= 0; i--) {
        const section = sections[i]
        if (section && section.offsetTop <= scrollPosition) {
            activeSection.value = props.links[i].id
            break
        }
    }
}

const scrollToSection = sectionId => {
    const section = document.getElementById(sectionId)
    if (section) {
        window.scrollTo({
            top: section.offsetTop - 80, // Adjust for header height
            behavior: 'smooth'
        })
    }
}

onMounted(() => {
    updateActiveSection()
    window.addEventListener('scroll', updateActiveSection)
})

onUnmounted(() => {
    window.removeEventListener('scroll', updateActiveSection)
})
</script>

<template>
    <nav class="space-y-2" aria-label="Article navigation" style="font-family: 'Courier New', monospace;">
        <button
            v-for="link in links"
            :key="link.id"
            class="relative block w-full px-4 py-2 text-xs font-bold uppercase tracking-wider transition-all duration-200 border-l-4 text-left"
            :class="[
                activeSection === link.id
                    ? 'bg-teal-500/10 text-teal-600 dark:bg-teal-500/20 dark:text-teal-400 border-teal-500 shadow-[0_0_10px_rgba(20,184,166,0.3)]'
                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800/50 border-transparent hover:border-gray-400 dark:hover:border-gray-600'
            ]"
            @click="scrollToSection(link.id)">
            <span v-if="activeSection === link.id" class="inline-block mr-2 text-teal-500">&gt;</span>
            {{ link.title }}
        </button>
    </nav>
</template>
