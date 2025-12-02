<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    default: () => [],
  },
})

const activeSection = ref('')

const updateActiveSection = () => {
  const scrollPosition = window.scrollY + 150
  let currentActive = ''

  for (const link of props.links) {
    const sectionId = link.href.replace('#', '')
    const section = document.getElementById(sectionId)

    if (section) {
      const sectionTop = section.offsetTop

      // If this section is at or above the scroll position, mark it as active
      if (scrollPosition >= sectionTop) {
        currentActive = link.href
      }
    }
  }

  // If no section is found, default to the first one
  if (!currentActive && props.links.length > 0) {
    currentActive = props.links[0].href
  }

  activeSection.value = currentActive
}

onMounted(() => {
  window.addEventListener('scroll', updateActiveSection)
  updateActiveSection()
})

onUnmounted(() => {
  window.removeEventListener('scroll', updateActiveSection)
})
</script>

<template>
  <aside class="fixed top-20 right-6 hidden w-64 xl:block">
    <div
      class="border border-green-500/30 bg-white p-4 font-mono dark:border-green-500/20 dark:bg-gray-900">
      <!-- Header -->
      <div class="mb-3 border-b border-green-200 pb-3 dark:border-green-800">
        <div class="flex items-center">
          <h2 class="text-xs font-bold tracking-wider text-green-700 uppercase dark:text-green-400">
            Contents
          </h2>
        </div>
      </div>

      <!-- Navigation -->
      <nav>
        <ul class="space-y-1">
          <li v-for="link in links" :key="link.href">
            <a
              :href="link.href"
              class="group relative block border-l-2 px-2 py-2 text-xs font-bold tracking-wide uppercase transition-all duration-150"
              :class="[
                link.href === activeSection
                  ? 'border-green-500 text-green-600 dark:text-green-400'
                  : 'border-transparent text-gray-600 hover:border-green-200 hover:bg-green-50/10 hover:text-green-700 dark:text-gray-400 dark:hover:border-green-800 dark:hover:bg-green-900/5 dark:hover:text-green-400',
              ]">
              <!-- Active indicator -->
              <span v-if="link.href === activeSection" class="inline-block text-green-500"></span>
              {{ link.text }}
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>
