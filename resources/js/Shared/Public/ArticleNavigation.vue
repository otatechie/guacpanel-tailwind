<script setup>
defineProps({
    links: {
        type: Array,
        default: () => []
    }
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
                            class="block px-4 py-2 text-sm rounded-md transition-colors duration-200 ease-in-out"
                            :class="[
                                link.active
                                    ? 'text-purple-600 dark:text-purple-400 font-medium'
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
