<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Number,
    default: 0,
  },
  tabs: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['update:modelValue'])

const activeTab = ref(props.modelValue)

watch(
  () => props.modelValue,
  newValue => {
    activeTab.value = newValue
  }
)

const switchTab = index => {
  activeTab.value = index
  emit('update:modelValue', index)
}
</script>

<template>
  <div>
    <div class="border-b border-gray-200 dark:border-gray-700">
      <!-- Desktop Tabs -->
      <nav class="-mb-px hidden space-x-8 sm:flex" aria-label="Tabs">
        <button
          v-for="(tab, index) in tabs"
          :key="index"
          type="button"
          class="flex cursor-pointer items-center gap-2 border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap transition-colors duration-75"
          :class="[
            activeTab === index
              ? 'border-primary-500 text-primary-600 dark:border-primary-400 dark:text-primary-400'
              : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-200',
          ]"
          :aria-current="activeTab === index ? 'page' : undefined"
          @click="switchTab(index)">
          <!-- Chart Bar Icon -->
          <svg
            v-if="tab.icon === 'chart-bar'"
            :class="['h-4 w-4', tab.color || 'text-gray-500']"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
          </svg>
          <!-- Document Text Icon -->
          <svg
            v-else-if="tab.icon === 'document-text'"
            :class="['h-4 w-4', tab.color || 'text-gray-500']"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <!-- Document Duplicate Icon -->
          <svg
            v-else-if="tab.icon === 'document-duplicate'"
            :class="['h-4 w-4', tab.color || 'text-gray-500']"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
          </svg>
          {{ typeof tab === 'string' ? tab : tab.name }}
        </button>
      </nav>

      <!-- Mobile Dropdown -->
      <div class="sm:hidden">
        <label for="tab-selector" class="sr-only">Select a tab</label>
        <select
          id="tab-selector"
          v-model="activeTab"
          class="focus:border-primary-500 focus:ring-primary-500 block w-full rounded-md border-gray-300 bg-white py-2 pr-10 pl-3 text-base text-gray-900 focus:outline-none sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
          @change="switchTab(activeTab)">
          <option v-for="(tab, index) in tabs" :key="index" :value="index">
            {{ typeof tab === 'string' ? tab : tab.name }}
          </option>
        </select>
      </div>
    </div>
    <div class="mt-6">
      <slot></slot>
    </div>
  </div>
</template>
