<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        required: true,
        default: () => []
    }
});

const activeSection = ref('');

const updateActiveSection = () => {
    const sections = props.links.map(link => document.getElementById(link.id));
    const scrollPosition = window.scrollY + 100; // Add offset for header

    for (let i = sections.length - 1; i >= 0; i--) {
        const section = sections[i];
        if (section && section.offsetTop <= scrollPosition) {
            activeSection.value = props.links[i].id;
            break;
        }
    }
};

const scrollToSection = (sectionId) => {
    const section = document.getElementById(sectionId);
    if (section) {
        window.scrollTo({
            top: section.offsetTop - 80, // Adjust for header height
            behavior: 'smooth'
        });
    }
};

onMounted(() => {
    updateActiveSection();
    window.addEventListener('scroll', updateActiveSection);
});

onUnmounted(() => {
    window.removeEventListener('scroll', updateActiveSection);
});
</script>

<template>
    <nav class="space-y-1" aria-label="Article navigation">
        <button v-for="link in links" :key="link.id" @click="scrollToSection(link.id)"
            class="block w-full px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200" :class="[
                activeSection === link.id
                    ? 'bg-teal-50 text-teal-600 dark:bg-teal-900/20 dark:text-teal-400'
                    : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800'
            ]">
            {{ link.title }}
        </button>
    </nav>
</template>