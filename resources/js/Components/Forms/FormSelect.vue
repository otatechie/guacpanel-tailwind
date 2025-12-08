<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },

  label: {
    type: String,
    required: true,
  },

  placeholder: {
    type: String,
    default: 'Select option',
  },

  id: {
    type: String,
    default: null,
  },

  required: {
    type: Boolean,
    default: false,
  },

  error: {
    type: String,
    default: '',
  },

  disabled: {
    type: Boolean,
    default: false,
  },

  options: {
    type: Array,
    default: () => [],
  },

  optionLabel: {
    type: String,
    default: 'label',
  },

  optionValue: {
    type: String,
    default: 'value',
  },

  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const searchQuery = ref('')
const dropdownPosition = ref('bottom')
const selectRef = ref(null)
const highlightedIndex = ref(-1)
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

const filteredOptions = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return props.options.filter(option => {
    const label = option[props.optionLabel]
    if (typeof label !== 'string') return false
    return label.toLowerCase().includes(query)
  })
})

const displayValue = computed(() => {
  const selected = props.options.find(
    option => String(option[props.optionValue]) === String(props.modelValue)
  )
  return selected ? selected[props.optionLabel] : props.placeholder
})

const selectClass = computed(() => {
  const baseClasses =
    'w-full peer border rounded-md px-3 py-2 text-sm appearance-none ' +
    'transition-all duration-200 ease-in-out focus:outline-none ' +
    'hover:border-[var(--color-border-strong)] ' +
    (isOpen.value ? 'ring-2 ring-[var(--selection-color-light)] ' : '')

  const borderClasses = props.error ? 'border-red-500' : 'border-[var(--color-border-strong)]'

  const disabledClasses = props.disabled
    ? 'cursor-not-allowed text-[var(--color-text-muted)] bg-[var(--color-surface)]'
    : 'cursor-pointer bg-[var(--color-surface)] text-[var(--color-text)]'

  return `${baseClasses} ${borderClasses} ${disabledClasses}`
})

function toggleDropdown() {
  if (props.disabled) return

  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => {
      const input = document.querySelector(`#${inputId.value}`)
      if (input) {
        input.focus()
        updateDropdownPosition(input)
      }
    })
  }
}

function updateDropdownPosition(inputElement) {
  const rect = inputElement.getBoundingClientRect()
  const spaceBelow = window.innerHeight - rect.bottom
  const spaceAbove = rect.top
  const dropdownHeight = 250 // max-height of dropdown

  dropdownPosition.value = spaceBelow < dropdownHeight && spaceAbove > spaceBelow ? 'top' : 'bottom'
}

function selectOption(option) {
  emit('update:modelValue', option[props.optionValue])
  searchQuery.value = ''
  isOpen.value = false
  highlightedIndex.value = -1
}

function isOptionSelected(option) {
  return String(option[props.optionValue]) === String(props.modelValue)
}

function handleClickOutside(e) {
  if (selectRef.value && !selectRef.value.contains(e.target)) {
    isOpen.value = false
    searchQuery.value = ''
    highlightedIndex.value = -1
  }
}

function clearSelection() {
  emit('update:modelValue', '')
  searchQuery.value = ''
}

function handleKeydown(e) {
  if (!isOpen.value) {
    if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
      e.preventDefault()
      isOpen.value = true
    }
    return
  }

  switch (e.key) {
    case 'Escape':
      e.preventDefault()
      isOpen.value = false
      searchQuery.value = ''
      highlightedIndex.value = -1
      break
    case 'ArrowDown':
      e.preventDefault()
      if (highlightedIndex.value < filteredOptions.value.length - 1) {
        highlightedIndex.value++
        scrollToHighlighted()
      }
      break
    case 'ArrowUp':
      e.preventDefault()
      if (highlightedIndex.value > 0) {
        highlightedIndex.value--
        scrollToHighlighted()
      }
      break
    case 'Enter':
      e.preventDefault()
      if (highlightedIndex.value >= 0 && highlightedIndex.value < filteredOptions.value.length) {
        selectOption(filteredOptions.value[highlightedIndex.value])
      }
      break
    case 'Home':
      e.preventDefault()
      highlightedIndex.value = 0
      scrollToHighlighted()
      break
    case 'End':
      e.preventDefault()
      highlightedIndex.value = filteredOptions.value.length - 1
      scrollToHighlighted()
      break
  }
}

function scrollToHighlighted() {
  nextTick(() => {
    const highlightedElement = document.querySelector(
      `#${inputId.value}-option-${highlightedIndex.value}`
    )
    if (highlightedElement) {
      highlightedElement.scrollIntoView({
        block: 'nearest',
        behavior: 'smooth',
      })
    }
  })
}

function isOptionHighlighted(index) {
  return highlightedIndex.value === index
}

const scrollHandler = () => {
  if (isOpen.value) {
    const input = document.querySelector(`#${inputId.value}`)
    if (input) updateDropdownPosition(input)
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside, true)
  window.addEventListener('scroll', scrollHandler, true)
  window.addEventListener('resize', scrollHandler)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside, true)
  window.removeEventListener('scroll', scrollHandler, true)
  window.removeEventListener('resize', scrollHandler)
})
</script>

<template>
  <fieldset ref="selectRef" class="relative space-y-1">
    <label :for="inputId" class="relative block" @click.stop="toggleDropdown">
      <input
        :id="inputId"
        type="text"
        readonly
        :value="displayValue"
        role="combobox"
        :aria-expanded="isOpen"
        :aria-controls="`${inputId}-listbox`"
        :aria-activedescendant="modelValue ? `${inputId}-option-${modelValue}` : undefined"
        :class="[selectClass, 'capitalize']"
        :disabled="disabled"
        @keydown="handleKeydown" />

      <!-- Clear Button -->
      <button
        v-if="modelValue && !disabled"
        type="button"
        class="group absolute top-1/2 right-7 flex h-4 w-4 -translate-y-1/2 items-center justify-center rounded-full bg-[var(--color-surface-muted)] text-[var(--color-text-muted)] transition-all duration-200 hover:bg-[var(--color-surface)] hover:text-[var(--color-text)]"
        :aria-label="'Clear ' + label + ' selection'"
        @click.stop="clearSelection">
        <svg
          viewBox="0 0 20 20"
          fill="currentColor"
          class="h-2.5 w-2.5 transition-transform duration-200 group-hover:scale-110">
          <path
            fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd" />
        </svg>
      </button>

      <!-- Dropdown Arrow -->
      <svg
        class="pointer-events-none absolute top-1/2 right-2 h-4 w-4 -translate-y-1/2 transition-all duration-300 ease-out"
        :class="{
          'rotate-180': isOpen,
          'text-[var(--color-text-muted)]': !disabled,
          'text-[var(--color-text-muted)] opacity-70': disabled,
        }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2.5"
          d="M19 9l-7 7-7-7" />
      </svg>

      <section
        v-show="isOpen"
        :id="`${inputId}-listbox`"
        role="listbox"
        class="absolute z-50 flex max-h-[250px] w-full transform flex-col overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-lg transition-all duration-300 ease-out"
        :class="{
          'bottom-full mb-2 translate-y-0 scale-100 opacity-100': dropdownPosition === 'top',
          'top-full mt-2 translate-y-0 scale-100 opacity-100': dropdownPosition === 'bottom',
          'translate-y-2 scale-95 opacity-0': !isOpen,
        }">
        <header
          class="flex-shrink-0 border-b border-[var(--color-border)] bg-[var(--color-surface-muted)] p-2">
          <div class="relative">
            <svg
              class="absolute top-1/2 left-2 h-3 w-3 -translate-y-1/2 text-[var(--color-text-muted)]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="searchQuery"
              type="search"
              :aria-label="'Search ' + label"
              placeholder="Search..."
              class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] py-1.5 pr-3 pl-7 text-xs text-[var(--color-text)] transition-all duration-200 focus:border-[var(--color-border-strong)] focus:ring-1 focus:ring-[var(--selection-color-light)] focus:outline-none"
              @click.stop />
          </div>
        </header>

        <ul class="flex-1 overflow-y-auto">
          <!-- Loading State -->
          <li
            v-if="loading"
            role="status"
            class="px-4 py-6 text-center text-sm text-[var(--color-text-muted)]">
            <div class="flex items-center justify-center gap-2">
              <svg
                class="h-5 w-5 animate-spin text-[var(--color-text-muted)]"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24">
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Loading...</span>
            </div>
          </li>
          <!-- Options -->
          <li
            v-for="(option, index) in filteredOptions"
            v-else
            :id="`${inputId}-option-${index}`"
            :key="option[optionValue]"
            role="option"
            :aria-selected="isOptionSelected(option)"
            class="cursor-pointer px-3 py-2 text-sm text-[var(--color-text)] transition-all duration-150 hover:bg-[var(--color-surface-muted)]"
            :class="{
              'bg-[var(--color-surface-muted)]': isOptionSelected(option),
              'bg-blue-50 dark:bg-blue-900/20':
                isOptionHighlighted(index) && !isOptionSelected(option),
            }"
            @mouseenter="highlightedIndex = index"
            @click="selectOption(option)">
            <div class="flex items-center justify-between">
              <span class="capitalize">{{ option[optionLabel] }}</span>
              <svg
                v-if="isOptionSelected(option)"
                class="h-4 w-4 text-[var(--color-text-muted)]"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd" />
              </svg>
            </div>
          </li>
          <!-- Empty State -->
          <li
            v-if="!loading && filteredOptions.length === 0"
            role="status"
            class="px-4 py-6 text-center text-sm text-[var(--color-text-muted)]">
            <svg
              class="mx-auto mb-2 h-8 w-8 text-[var(--color-border)]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9.5 9.5h.01" />
            </svg>
            No matches found
          </li>
        </ul>
      </section>

      <span
        class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white px-1 text-xs font-medium text-gray-700 transition-all duration-200 peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs dark:bg-gray-800 dark:text-gray-300">
        {{ label }}{{ required ? ' *' : '' }}
      </span>
    </label>

    <p
      v-if="error"
      :id="`${inputId}-error`"
      role="alert"
      class="mt-1 text-xs text-red-600 dark:text-red-400">
      {{ error }}
    </p>
  </fieldset>
</template>
