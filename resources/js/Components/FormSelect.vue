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
const inputId = computed(() => props.id || props.label.toLowerCase().replace(/\s+/g, '-'))

const filteredOptions = computed(() => {
    const query = searchQuery.value.toLowerCase()
    return props.options.filter(option =>
        option[props.optionLabel].toLowerCase().includes(query)
    )
})

const displayValue = computed(() => {
    const selected = props.options.find(
        option => option[props.optionValue] === props.modelValue
    )
    return selected ? selected[props.optionLabel] : props.placeholder
})

const selectClass = computed(() => {
    const baseClasses = 'w-full peer border rounded-md bg-white px-3 py-2 appearance-none' +
                      'transition-shadow duration-150 ease-in-out focus:outline-none cursor-pointer ' +
                      'dark:bg-gray-800 dark:text-white'
    
    const borderClasses = props.error 
        ? 'error' 
        : 'border-gray-300 dark:border-gray-600'
        
    return `${baseClasses} ${borderClasses}`
})

function toggleDropdown() {
    isOpen.value = !isOpen.value
    if (isOpen.value) nextTick(() => document.querySelector(`#${inputId.value}`)?.focus())
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

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>


<template>
    <fieldset class="space-y-1">
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
                @keydown.arrow-down="isOpen = true" 
                @keydown.enter.prevent="isOpen = !isOpen"
            >

            <section v-show="isOpen" :id="`${inputId}-listbox`" role="listbox"
                class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-hidden">
                <header class="p-2 border-b border-gray-300 dark:border-gray-600">
                    <input type="search" :aria-label="'Search ' + label" v-model="searchQuery" placeholder="Search..."
                        class="w-full p-2 text-sm rounded-md focus:outline-none focus:ring-1 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:focus:ring-gray-600"
                        @click.stop>
                </header>

                <ul class="overflow-y-auto max-h-52">
                    <li v-for="option in filteredOptions" :key="option[optionValue]"
                        :id="`${inputId}-option-${option[optionValue]}`" role="option"
                        :aria-selected="isOptionSelected(option)" @click="selectOption(option)"
                        class="px-3 py-2 text-sm cursor-pointer capitalize hover:bg-blue-50 dark:hover:bg-gray-700 dark:text-white"
                        :class="{ 'bg-blue-50 dark:bg-gray-700': isOptionSelected(option) }">
                        {{ option[optionLabel] }}
                    </li>
                    <li v-if="filteredOptions.length === 0" role="status"
                        class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                        No matches found
                    </li>
                </ul>
            </section>

            <svg class="w-4 h-4 transition-transform absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>

            <span
                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white dark:bg-gray-800 px-1 text-xs text-gray-700 dark:text-gray-400 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>
        </label>
        
        <p v-if="error" role="alert" class="mt-1 text-red-600 text-sm">
            {{ error }}
        </p>
    </fieldset>
</template>
