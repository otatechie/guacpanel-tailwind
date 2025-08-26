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

    disabled: {
        type: Boolean,
        default: false
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
    const baseClasses = 'w-full peer border rounded-md px-3 py-2 text-sm appearance-none ' +
        'transition-all duration-200 ease-in-out focus:outline-none ' +
        'hover:border-gray-400 dark:hover:border-gray-400 ' +
        (isOpen.value ? 'ring-2 ring-gray-100 dark:ring-gray-700 ' : '')

    const borderClasses = props.error
        ? 'border-red-500 dark:border-red-400'
        : 'border-gray-300 dark:border-gray-600'

    const disabledClasses = props.disabled
        ? 'cursor-not-allowed text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700'
        : 'cursor-pointer bg-white dark:bg-gray-800 dark:text-white'

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
                :class="[selectClass]" :disabled="disabled"
                @keydown.arrow-down="isOpen = true" @keydown.enter.prevent="isOpen = !isOpen">

            <!-- Clear Button -->
            <button v-if="modelValue && !disabled" type="button" @click.stop="clearSelection" 
                class="absolute right-8 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 bg-gray-100 dark:bg-gray-700 
                       hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600
                       transition-all duration-200 flex items-center justify-center rounded-full group"
                :aria-label="'Clear ' + label + ' selection'">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 group-hover:scale-110 transition-transform duration-200">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Dropdown Arrow -->
            <svg class="w-4 h-4 transition-all duration-300 ease-out absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                :class="{ 
                    'rotate-180': isOpen,
                    'text-gray-400 dark:text-gray-500': !disabled,
                    'text-gray-300 dark:text-gray-600': disabled
                }" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>

            <section v-show="isOpen" :id="`${inputId}-listbox`" role="listbox" class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 
                       rounded-xl shadow-xl overflow-hidden flex flex-col transition-all duration-300 ease-out transform backdrop-blur-sm"
                :class="{
                    'bottom-full mb-2 opacity-100 translate-y-0 scale-100': dropdownPosition === 'top',
                    'top-full mt-2 opacity-100 translate-y-0 scale-100': dropdownPosition === 'bottom',
                    'opacity-0 translate-y-2 scale-95': !isOpen
                }" style="max-height: 280px">
                <header
                    class="p-3 border-b border-gray-200 dark:border-gray-600 flex-shrink-0 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="search" :aria-label="'Search ' + label" v-model="searchQuery"
                            placeholder="Search options..." class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-200 
                                   dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 
                                   focus:border-gray-400 dark:focus:ring-gray-700 dark:focus:border-gray-500
                                   bg-white dark:bg-gray-800 dark:text-white transition-all duration-200" @click.stop>
                    </div>
                </header>

                <ul class="overflow-y-auto flex-1">
                    <li v-for="option in filteredOptions" :key="option[optionValue]"
                        :id="`${inputId}-option-${option[optionValue]}`" role="option"
                        :aria-selected="isOptionSelected(option)" @click="selectOption(option)" 
                        class="px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700
                               transition-all duration-150 text-gray-700 dark:text-white"
                        :class="{ 
                            'bg-gray-100 dark:bg-gray-700': isOptionSelected(option)
                        }">
                        <div class="flex items-center justify-between">
                            <span>{{ option[optionLabel] }}</span>
                            <svg v-if="isOptionSelected(option)" class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </li>
                    <li v-if="filteredOptions.length === 0" role="status"
                        class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400 text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 9.5h.01" />
                        </svg>
                        No matches found
                    </li>
                </ul>
            </section>

            <span
                class="pointer-events-none absolute start-3 top-0 -translate-y-1/2 bg-white dark:bg-gray-800 
                         px-2 text-xs font-medium text-gray-700 dark:text-gray-400 transition-all duration-200
                         peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>
        </label>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1 text-red-600 dark:text-red-400 text-xs">
            {{ error }}
        </p>
    </fieldset>
</template>
