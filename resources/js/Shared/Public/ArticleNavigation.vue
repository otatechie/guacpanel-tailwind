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
    const scrollPosition = window.scrollY + 150;
    let currentActive = '';
    
    for (const link of props.links) {
        const sectionId = link.href.replace('#', '');
        const section = document.getElementById(sectionId);
        
        if (section) {
            const sectionTop = section.offsetTop;
            
            // If this section is at or above the scroll position, mark it as active
            if (scrollPosition >= sectionTop) {
                currentActive = link.href;
            }
        }
    }
    
    // If no section is found, default to the first one
    if (!currentActive && props.links.length > 0) {
        currentActive = props.links[0].href;
    }
    
    activeSection.value = currentActive;
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
    <aside class="hidden xl:block w-56 fixed right-6 top-20">
        <div class="space-y-2">
            <!-- Header -->
            <div class="pb-2 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-medium text-gray-900 dark:text-white">Contents</h2>
            </div>

            <!-- Navigation -->
            <nav>
                <ul class="space-y-1">
                    <li v-for="link in links" :key="link.href">
                        <a :href="link.href"
                           class="group relative block py-1.5 text-sm transition-colors duration-150"
                           :class="[
                               link.href === activeSection
                                   ? 'text-gray-900 dark:text-white font-medium'
                                   : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                           ]">
                            
                            <!-- Tiny active indicator -->
                            <div v-if="link.href === activeSection" 
                                 class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-1 bg-gray-400 dark:bg-gray-500 rounded-full"></div>
                            
                            <span class="block"
                                  :class="{ 'pl-3': link.href === activeSection }">
                                {{ link.text }}
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>
