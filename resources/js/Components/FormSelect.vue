<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({

    modelValue: {
        type: [String, Number],
        default: ''
    },

    label: {
        type: String,
        required: true
    },

    placeholder: {
        type: String,
        default: 'Select option'
    },

    id: {
        type: String,
        default: null
    },

    required: {
        type: Boolean,
        default: false
    },

    error: {
        type: String,
        default: ''
    },

    options: {
        type: Array,
        default: () => []
    },

    optionLabel: {
        type: String,
        default: 'label'
    },

    optionValue: {
        type: String,
        default: 'value'
    }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const searchQuery = ref('')
const dropdownPosition = ref('bottom')
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

const filteredOptions = computed(() => {
    const query = searchQuery.value.toLowerCase()
    return props.options.filter(option =>
        option[props.optionLabel]?.toLowerCase().includes(query) ?? false
    )
})

const displayValue = computed(() => {
    const selected = props.options.find(
        option => option[props.optionValue] === props.modelValue
    )
    return selected ? selected[props.optionLabel] : props.placeholder
})

const selectClass = computed(() => {
    const baseClasses = 'w-full peer border rounded-md bg-white px-3 py-2 appearance-none ' +
        'transition-all duration-200 ease-in-out focus:outline-none cursor-pointer ' +
        'dark:bg-gray-800 dark:text-white ' +
        (isOpen.value ? 'ring-2 ring-gray-100 dark:ring-gray-700 ' : '')

    const borderClasses = props.error
        ? 'border-red-500 dark:border-red-500'
        : 'border-gray-300 dark:border-gray-600'

    return `${baseClasses} ${borderClasses}`
})

function toggleDropdown() {
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
    const dropdownHeight = 280 // Our dropdown's max height

    dropdownPosition.value = spaceBelow >= dropdownHeight || spaceBelow >= spaceAbove ? 'bottom' : 'top'
}

function selectOption(option) {
    emit('update:modelValue', option[props.optionValue])
    searchQuery.value = ''
    isOpen.value = false
}

function isOptionSelected(option) {
    return option[props.optionValue] === props.modelValue
}

function handleClickOutside(e) {
    const fieldset = document.getElementById(inputId.value)?.closest('fieldset')
    if (fieldset && !fieldset.contains(e.target)) {
        isOpen.value = false
    }
}

function clearSelection() {
    emit('update:modelValue', '')
    searchQuery.value = ''
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    window.addEventListener('scroll', () => {
        if (isOpen.value) {
            const input = document.querySelector(`#${inputId.value}`)
            if (input) updateDropdownPosition(input)
        }
    }, true)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
    window.removeEventListener('scroll', () => { })
})
</script>


<template>
    <fieldset class="space-y-1">
        <label :for="inputId" class="relative block" @click.stop="toggleDropdown">
            <input :id="inputId" type="text" readonly :value="displayValue" role="combobox" :aria-expanded="isOpen"
                :aria-controls="`${inputId}-listbox`"
                :aria-activedescendant="modelValue ? `${inputId}-option-${modelValue}` : undefined"
                :class="[selectClass]" @keydown.arrow-down="isOpen = true" @keydown.enter.prevent="isOpen = !isOpen">

            <!-- Clear Button -->
            <button v-if="modelValue" type="button" @click.stop="clearSelection" class="absolute right-7 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 bg-gray-100 dark:bg-gray-700 p-0.5 rounded-full
                       hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300
                       transition-colors duration-200 flex items-center justify-center"
                :aria-label="'Clear ' + label + ' selection'">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-full h-full">
                    <path d="M10 10l5.09-5.09L10 10l5.09 5.09L10 10zm0 0L4.91 4.91 10 10l-5.09 5.09L10 10z"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </button>

            <!-- Dropdown Arrow -->
            <svg class="w-4 h-4 transition-transform duration-200 absolute right-1.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 dark:text-gray-500"
                :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>

            <section v-show="isOpen" :id="`${inputId}-listbox`" role="listbox" class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 
                       rounded-md shadow-lg overflow-hidden flex flex-col transition-all duration-200 transform"
                :class="{
                    'bottom-full mb-1 opacity-100 translate-y-0': dropdownPosition === 'top',
                    'top-full mt-1 opacity-100 translate-y-0': dropdownPosition === 'bottom',
                    'opacity-0 translate-y-2': !isOpen
                }" style="max-height: 280px">
                <header
                    class="p-2 border-b border-gray-300 dark:border-gray-600 flex-shrink-0 bg-gray-50 dark:bg-gray-700">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="search" :aria-label="'Search ' + label" v-model="searchQuery"
                            placeholder="Search..." class="w-full pl-9 pr-3 py-2 text-sm rounded-md border border-gray-200 
                                   dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 
                                   focus:border-gray-400 dark:focus:ring-gray-700 dark:focus:border-gray-500
                                   bg-white dark:bg-gray-800 dark:text-white transition-all duration-200" @click.stop>
                    </div>
                </header>

                <ul class="overflow-y-auto flex-1">
                    <li v-for="option in filteredOptions" :key="option[optionValue]"
                        :id="`${inputId}-option-${option[optionValue]}`" role="option"
                        :aria-selected="isOptionSelected(option)" @click="selectOption(option)" class="px-3 py-2 text-sm cursor-pointer capitalize hover:bg-gray-100 dark:hover:bg-gray-700
                               transition-colors duration-150 text-gray-700 dark:text-white"
                        :class="{ 'bg-gray-100 dark:bg-gray-700': isOptionSelected(option) }">
                        {{ option[optionLabel] }}
                    </li>
                    <li v-if="filteredOptions.length === 0" role="status"
                        class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                        No matches found
                    </li>
                </ul>
            </section>

            <span
                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white dark:bg-gray-800 
                         px-1 text-xs text-gray-700 dark:text-gray-400 transition-all
                         peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>
        </label>

        <p v-if="error" role="alert" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </fieldset>
</template>
