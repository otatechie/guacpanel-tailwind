<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { CheckIcon, ChevronDownIcon, SearchIcon, XIcon } from '@lucide/vue'
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

function toggleDropdown() {
    if (props.disabled) return
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        nextTick(() => {
            const input = document.querySelector(`#${inputId.value}`)
            if (input) updateDropdownPosition(input)
        })
    }
}

function updateDropdownPosition(el) {
    const rect = el.getBoundingClientRect()
    const spaceBelow = window.innerHeight - rect.bottom
    dropdownPosition.value = spaceBelow < 250 && rect.top > spaceBelow ? 'top' : 'bottom'
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
            if (
                highlightedIndex.value >= 0 &&
                highlightedIndex.value < filteredOptions.value.length
            ) {
                selectOption(filteredOptions.value[highlightedIndex.value])
            }
            break
    }
}

function scrollToHighlighted() {
    nextTick(() => {
        const el = document.querySelector(`#${inputId.value}-option-${highlightedIndex.value}`)
        if (el) el.scrollIntoView({ block: 'nearest', behavior: 'smooth' })
    })
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
    <fieldset ref="selectRef" class="relative">
        <label :for="inputId" class="form-label" @click.stop="toggleDropdown">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="relative">
            <input
                :id="inputId"
                type="text"
                readonly
                :value="displayValue"
                role="combobox"
                :aria-expanded="isOpen"
                :aria-controls="`${inputId}-listbox`"
                class="form-input cursor-pointer pr-8 capitalize"
                :class="{
                    'form-input-error': error,
                    'form-input-disabled': disabled,
                }"
                :disabled="disabled"
                @click.stop="toggleDropdown"
                @keydown="handleKeydown" />

            <!-- Clear -->
            <button
                v-if="modelValue && !disabled"
                type="button"
                class="bg-muted text-muted-foreground hover:text-foreground absolute top-1/2 right-7 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full transition-colors"
                :aria-label="'Clear ' + label"
                @click.stop="clearSelection">
                <XIcon class="h-2.5 w-2.5" />
            </button>

            <!-- Chevron -->
            <ChevronDownIcon
                class="text-muted-foreground pointer-events-none absolute top-1/2 right-2.5 h-4 w-4 -translate-y-1/2 transition-transform duration-150"
                :class="{ 'rotate-180': isOpen }"
                aria-hidden="true" />

            <!-- Dropdown -->
            <section
                v-show="isOpen"
                :id="`${inputId}-listbox`"
                role="listbox"
                class="border-border bg-card absolute z-50 flex max-h-[180px] w-full flex-col overflow-hidden rounded-lg border shadow-lg sm:max-h-[250px]"
                :class="dropdownPosition === 'top' ? 'bottom-full mb-1.5' : 'top-full mt-1.5'">
                <div class="border-border relative shrink-0 border-b">
                    <SearchIcon
                        class="text-muted-foreground pointer-events-none absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2" />
                    <input
                        v-model="searchQuery"
                        type="search"
                        :aria-label="'Search ' + label"
                        placeholder="Search..."
                        class="text-foreground placeholder:text-muted-foreground w-full border-0 bg-transparent py-2.5 pr-3 pl-8 text-sm shadow-none focus:border-0 focus:shadow-none focus:ring-0 focus:outline-none"
                        @click.stop />
                </div>

                <ul class="flex-1 overflow-y-auto">
                    <li
                        v-if="loading"
                        role="status"
                        class="text-muted-foreground px-4 py-6 text-center text-sm">
                        Loading...
                    </li>
                    <li
                        v-for="(option, index) in filteredOptions"
                        v-else
                        :id="`${inputId}-option-${index}`"
                        :key="option[optionValue]"
                        role="option"
                        :aria-selected="isOptionSelected(option)"
                        class="text-foreground hover:bg-muted flex cursor-pointer items-center justify-between px-3 py-2 text-sm transition-colors"
                        :class="{
                            'bg-muted': isOptionSelected(option) || highlightedIndex === index,
                        }"
                        @mouseenter="highlightedIndex = index"
                        @click="selectOption(option)">
                        <span class="capitalize">{{ option[optionLabel] }}</span>
                        <CheckIcon
                            v-if="isOptionSelected(option)"
                            class="text-primary h-4 w-4 shrink-0" />
                    </li>
                    <li
                        v-if="!loading && filteredOptions.length === 0"
                        class="text-muted-foreground px-4 py-6 text-center text-sm">
                        No matches found
                    </li>
                </ul>
            </section>
        </div>

        <p v-if="error" :id="`${inputId}-error`" role="alert" class="mt-1.5 text-xs text-red-600">
            {{ error }}
        </p>
    </fieldset>
</template>
