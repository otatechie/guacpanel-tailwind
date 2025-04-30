<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        default: () => []
    }
});

const activeSection = ref('');

const updateActiveSection = () => {
    const scrollPosition = window.scrollY + 100;
    for (const link of props.links) {
        const sectionId = link.href.replace('#', '');
        const section = document.getElementById(sectionId);
        if (section && section.offsetTop <= scrollPosition) {
            activeSection.value = link.href;
        }
    }
    if (!activeSection.value && props.links.length > 0) {
        activeSection.value = props.links[0].href;
    }
};

onMounted(() => {
    window.addEventListener('scroll', updateActiveSection);
    updateActiveSection();
});

onUnmounted(() => {
    window.removeEventListener('scroll', updateActiveSection);
});
</script>

<template>
    <aside
        class="hidden xl:block w-64 fixed right-0 top-16 bg-gray-50 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700">
        <div class="max-h-[600px] overflow-y-auto">
            <div
                class="sticky top-0 px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 z-10 backdrop-blur-sm">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">In This Article
                </h2>
            </div>
            <nav class="py-3 px-3">
                <ul class="space-y-1">
                    <li v-for="link in links" :key="link.href">
                        <a :href="link.href"
                            class="block px-2 py-1 text-sm rounded-md transition-colors duration-200 ease-in-out"
                            :class="[
                                link.href === activeSection
                                    ? 'text-teal-600 dark:text-teal-400 font-medium bg-teal-50 dark:bg-teal-900/20'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white'
                            ]">
                            {{ link.text }}
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<style scoped>
aside div {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

aside div::-webkit-scrollbar {
    width: 4px;
}

aside div::-webkit-scrollbar-track {
    background: transparent;
}

aside div::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 2px;
}
</style>
