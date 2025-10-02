<!--
  RetroStatWidget - Terminal-themed stat card with retro CRT effects

  Features:
  - Retro terminal aesthetic with green phosphor theme
  - CRT corner brackets for authentic look
  - Scanline and grid overlay effects
  - Hover glow effects
  - Monospace Courier New font
  - Clipped icon container for retro shape

  Usage:
  <RetroStatWidget
    title="Expert Rating"
    value="4.9"
    :svg="ratingIconSVG"
    viewBox="0 0 50 50"
    color="text-blue-500 dark:text-blue-400" />
-->
<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number],
        required: true
    },
    svg: {
        type: String,
        required: true
    },
    viewBox: {
        type: String,
        default: '0 0 16 16'
    },
    color: {
        type: String,
        default: 'text-green-500 dark:text-green-400'
    }
})
</script>

<template>
    <div
        class="group relative bg-black dark:bg-gray-950 rounded border-2 border-green-500/40 dark:border-green-400/40 p-4 sm:p-6 transition-all duration-300 hover:border-green-500 dark:hover:border-green-400 hover:shadow-[0_0_15px_rgba(34,197,94,0.3)]"
        style="font-family: 'Courier New', monospace;">
        <!-- Scanline effect -->
        <div class="absolute inset-0 pointer-events-none opacity-10 bg-gradient-to-b from-transparent via-green-500/20 to-transparent"></div>

        <!-- CRT corner brackets -->
        <div class="absolute top-1 left-1 w-2 h-2 sm:w-3 sm:h-3 border-t-2 border-l-2 border-green-500/60"></div>
        <div class="absolute top-1 right-1 w-2 h-2 sm:w-3 sm:h-3 border-t-2 border-r-2 border-green-500/60"></div>
        <div class="absolute bottom-1 left-1 w-2 h-2 sm:w-3 sm:h-3 border-b-2 border-l-2 border-green-500/60"></div>
        <div class="absolute bottom-1 right-1 w-2 h-2 sm:w-3 sm:h-3 border-b-2 border-r-2 border-green-500/60"></div>

        <!-- Icon container with retro style -->
        <div class="relative z-10 flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 mb-3 sm:mb-4 border-2 border-green-500/40 group-hover:border-green-500 transition-colors duration-300"
            style="clip-path: polygon(0 0, 100% 0, 100% 75%, 85% 100%, 0 100%);">
            <svg
                :viewBox="viewBox"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-300 group-hover:drop-shadow-[0_0_8px_rgba(34,197,94,0.8)]"
                :class="color"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                v-html="svg"></svg>
        </div>

        <!-- Content with terminal styling -->
        <div class="relative z-10">
            <p class="text-xs sm:text-sm font-semibold text-green-500 dark:text-green-400 mb-2 sm:mb-3 uppercase tracking-widest opacity-80">
                {{ title }}
            </p>
            <div class="flex items-baseline gap-1">
                <p class="text-2xl sm:text-3xl font-semibold text-green-500 dark:text-green-400 tracking-wider group-hover:drop-shadow-[0_0_10px_rgba(34,197,94,0.6)] transition-all duration-300">
                    {{ value }}
                </p>
            </div>
        </div>

        <!-- Grid overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-5"
            style="background-image: linear-gradient(0deg, transparent 24%, rgba(34, 197, 94, .3) 25%, rgba(34, 197, 94, .3) 26%, transparent 27%, transparent 74%, rgba(34, 197, 94, .3) 75%, rgba(34, 197, 94, .3) 76%, transparent 77%, transparent), linear-gradient(90deg, transparent 24%, rgba(34, 197, 94, .3) 25%, rgba(34, 197, 94, .3) 26%, transparent 27%, transparent 74%, rgba(34, 197, 94, .3) 75%, rgba(34, 197, 94, .3) 76%, transparent 77%, transparent); background-size: 50px 50px;"></div>
    </div>
</template>
