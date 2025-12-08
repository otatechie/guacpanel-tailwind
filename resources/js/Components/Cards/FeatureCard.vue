<script setup>
defineProps({
  title: String,
  description: String,
  icon: String,
  link: String,
  color: {
    type: String,
    default: 'teal',
  },
})

const colorClasses = {
  teal: 'border-teal-500/40 dark:border-teal-400/40 hover:border-teal-500 dark:hover:border-teal-400 hover:shadow-[0_0_8px_rgba(20,184,166,0.2)]',
  indigo:
    'border-indigo-500/40 dark:border-indigo-400/40 hover:border-indigo-500 dark:hover:border-indigo-400 hover:shadow-[0_0_8px_rgba(99,102,241,0.2)]',
  emerald:
    'border-emerald-500/40 dark:border-emerald-400/40 hover:border-emerald-500 dark:hover:border-emerald-400 hover:shadow-[0_0_8px_rgba(16,185,129,0.2)]',
}

const iconColorClasses = {
  teal: 'text-teal-500 dark:text-teal-400 bg-teal-500/10 border-teal-500/40',
  indigo: 'text-indigo-500 dark:text-indigo-400 bg-indigo-500/10 border-indigo-500/40',
  emerald: 'text-emerald-500 dark:text-emerald-400 bg-emerald-500/10 border-emerald-500/40',
}
</script>

<template>
  <div
    :class="[
      'relative border-2 bg-white p-5 transition-all duration-300 dark:bg-gray-950',
      colorClasses[color],
    ]">
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
      <div
        :class="[
          'flex h-10 w-10 flex-shrink-0 items-center justify-center border-2',
          iconColorClasses[color],
        ]">
        <slot name="icon">
          <svg
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" :d="icon" />
          </svg>
        </slot>
      </div>
      <div class="min-w-0 flex-1">
        <h3
          class="mb-1 text-sm font-bold tracking-wider text-gray-900 uppercase dark:text-green-400">
          &gt; {{ title }}
        </h3>
        <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
          {{ description }}
        </p>
        <a
          v-if="link"
          :href="link"
          target="_blank"
          class="mt-2 inline-flex items-center text-xs font-bold tracking-wider text-teal-600 uppercase hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
          [Read_More]
        </a>
      </div>
    </div>
  </div>
</template>
