<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { cycleTheme, getCurrentThemeState } from '@/darkMode'

const props = defineProps({
  showTooltip: { type: Boolean, default: true },
  showLabelText: { type: Boolean, default: false }, // extra inline label option
  wrapperClass: {
    type: String,
    default: 'theme-dropdown relative hidden lg:block',
  },
  buttonClass: {
    type: String,
    default:
      'group flex cursor-pointer items-center rounded-lg p-1.5 text-sm text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]',
  },
  iconClass: {
    type: String,
    default: 'h-5 w-5 text-[var(--color-text-muted)] group-hover:text-[var(--primary-color)]',
  },
})

const themeState = ref(getCurrentThemeState())

const label = computed(() => themeState.value.nextThemeText)
const icon = computed(() => themeState.value.nextThemeIcon)
const currentLabel = computed(() => themeState.value.currentThemeText)
const currentIcon = computed(() => themeState.value.currentThemeIcon)

const icons = {
  sun: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />',
  moon: '<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />',
  system:
    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />',
}

let observer = null

const updateThemeState = () => {
  themeState.value = getCurrentThemeState()
}

const switchMode = () => {
  cycleTheme()
  updateThemeState()
}

onMounted(() => {
  observer = new MutationObserver(mutations => {
    if (mutations.some(m => m.attributeName === 'class')) updateThemeState()
  })

  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
  })

  window.addEventListener('themeChanged', updateThemeState)
})

onBeforeUnmount(() => {
  if (observer) observer.disconnect()
  window.removeEventListener('themeChanged', updateThemeState)
})
</script>

<template>
  <div :class="wrapperClass">
    <button :class="buttonClass" @click="switchMode">
      <svg
        :class="iconClass"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.5"
        v-html="icons[currentIcon]" />
      <span v-if="showLabelText" class="ml-2">
        {{ currentLabel }}
      </span>

      <span
        v-if="showTooltip"
        class="absolute -bottom-8 left-1/2 -translate-x-1/2 rounded px-2 py-1 text-xs whitespace-nowrap opacity-0 transition-opacity group-hover:opacity-100"
        :style="{ backgroundColor: 'var(--color-text)', color: 'var(--color-bg)' }">
        Switch to {{ label }}
      </span>
    </button>
  </div>
</template>
