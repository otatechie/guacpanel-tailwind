<script setup>
defineProps({
    title: String,
    version: String,
    description: String,
    link: String
})
</script>

<template>
    <div
        class="relative bg-white dark:bg-gray-950 border-2 border-cyan-500/40 dark:border-cyan-400/40 hover:border-cyan-500 dark:hover:border-cyan-400 p-5 transition-all duration-300 hover:shadow-[0_0_8px_rgba(6,182,212,0.2)]"
        style="font-family: 'Courier New', monospace;">
        <!-- Retro corner brackets -->
        <div class="absolute top-0 left-0 w-3 h-3 border-t-2 border-l-2 border-current opacity-40"></div>
        <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 border-current opacity-40"></div>
        <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 border-current opacity-40"></div>
        <div class="absolute bottom-0 right-0 w-3 h-3 border-b-2 border-r-2 border-current opacity-40"></div>

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 p-2 border-2 border-cyan-500/40 bg-cyan-500/10">
                <slot name="icon" />
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-gray-900 dark:text-cyan-400 tracking-wider uppercase">
                    {{ title }}
                    <span v-if="version" class="text-xs text-gray-500 dark:text-gray-400 ml-2">[{{ version }}]</span>
                </h3>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">
                    {{ description }}
                </p>
                <a
                    v-if="link"
                    :href="link"
                    target="_blank"
                    class="inline-flex items-center text-xs text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300 mt-2 font-bold uppercase tracking-wider">
                    [View_Docs]
                </a>
            </div>
        </div>
    </div>
</template>
