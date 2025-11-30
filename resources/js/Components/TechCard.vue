<script setup>
defineProps({
  title: String,
  version: String,
  description: String,
  link: String,
})
</script>

<template>
  <div
    class="relative border-2 border-cyan-500/40 bg-white p-5 transition-all duration-300 hover:border-cyan-500 hover:shadow-[0_0_8px_rgba(6,182,212,0.2)] dark:border-cyan-400/40 dark:bg-gray-950 dark:hover:border-cyan-400"
    style="font-family: 'Courier New', monospace">
    <!-- Retro corner brackets -->
    <div
      class="absolute top-0 left-0 h-3 w-3 border-t-2 border-l-2 border-current opacity-40"></div>
    <div
      class="absolute top-0 right-0 h-3 w-3 border-t-2 border-r-2 border-current opacity-40"></div>
    <div
      class="absolute bottom-0 left-0 h-3 w-3 border-b-2 border-l-2 border-current opacity-40"></div>
    <div
      class="absolute right-0 bottom-0 h-3 w-3 border-r-2 border-b-2 border-current opacity-40"></div>

    <div class="flex items-start gap-4">
      <div class="flex-shrink-0 border-2 border-cyan-500/40 bg-cyan-500/10 p-2">
        <slot name="icon" />
      </div>
      <div class="min-w-0 flex-1">
        <h3 class="text-sm font-bold tracking-wider text-gray-900 uppercase dark:text-cyan-400">
          {{ title }}
          <span v-if="version" class="ml-2 text-xs text-gray-500 dark:text-gray-400">
            [{{ version }}]
          </span>
        </h3>
        <p class="mt-1 text-sm leading-relaxed text-gray-700 dark:text-gray-300">
          {{ description }}
        </p>
        <a
          v-if="link"
          :href="link"
          target="_blank"
          class="mt-2 inline-flex items-center text-xs font-bold tracking-wider text-cyan-600 uppercase hover:text-cyan-700 dark:text-cyan-400 dark:hover:text-cyan-300">
          [View_Docs]
        </a>
      </div>
    </div>
  </div>
</template>
