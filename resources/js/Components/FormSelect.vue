<template>
    <div class="space-y-1">
        <label :for="id" class="relative block">
            <div class="relative" @click.stop="toggleDropdown">
                <!-- Updated input with dark mode classes -->
                <input :id="id" type="text" readonly
                    :value="displayValue"
                    class="w-full peer border rounded-md bg-white px-3 py-2 appearance-none
                    transition-shadow duration-150 ease-in-out focus:outline-none cursor-pointer dark:bg-gray-800 dark:text-white"
                    :class="[
                        error
                            ? 'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-900'
                            : 'border-gray-300 dark:border-gray-600 focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-500'
                    ]"
                    @keydown.arrow-down="isOpen = true"
                    @keydown.enter.prevent="isOpen = !isOpen"
                >

                <!-- Updated dropdown with dark mode classes -->
                <div v-show="isOpen" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-hidden">
                    <div class="p-2 border-b border-gray-300 dark:border-gray-600">
                        <input type="text"
                            v-model="searchQuery"
                            placeholder="Search..."
                            class="w-full p-2 text-sm rounded-md focus:outline-none focus:ring-1 focus:ring-gray-200 dark:bg-gray-800 dark:text-white dark:focus:ring-gray-600"
                            @click.stop
                        >
                    </div>

                    <div class="overflow-y-auto max-h-52">
                        <div v-for="option in filteredOptions"
                            :key="option[optionValue]"
                            @click="selectOption(option)"
                            class="px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 dark:text-white"
                            :class="{ 'bg-blue-50 dark:bg-gray-700': isOptionSelected(option) }"
                        >
                            {{ option[optionLabel] }}
                        </div>
                        <div v-if="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                            No matches found
                        </div>
                    </div>
                </div>

                <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': isOpen }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </div>

            <span class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-white dark:bg-gray-800 px-1 text-xs text-gray-700 dark:text-gray-400 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:text-sm peer-focus:top-0 peer-focus:text-xs">
                {{ label }}{{ required ? ' *' : '' }}
            </span>
        </label>
        <p v-if="error" class="text-red-500 dark:text-red-400 text-xs">{{ error }}</p>
    </div>
</template>

<script>
export default {
    name: 'SelectInput',
    props: {
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
            default: 'Select an option'
        },
        id: {
            type: String,
            required: true
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
    },
    emits: ['update:modelValue'],
    data() {
        return {
            isOpen: false,
            searchQuery: '',
        }
    },
    computed: {
        filteredOptions() {
            const query = this.searchQuery.toLowerCase()
            return this.options.filter(option =>
                option[this.optionLabel].toLowerCase().includes(query)
            )
        },
        displayValue() {
            const selected = this.options.find(
                option => option[this.optionValue] === this.modelValue
            )
            return selected ? selected[this.optionLabel] : this.placeholder
        }
    },
    methods: {
        toggleDropdown() {
            this.isOpen = !this.isOpen
            if (this.isOpen) this.$nextTick(() => this.$el.querySelector('input[type="text"]')?.focus())
        },
        selectOption(option) {
            this.$emit('update:modelValue', option[this.optionValue])
            this.searchQuery = ''
            this.isOpen = false
        },
        isOptionSelected(option) {
            return option[this.optionValue] === this.modelValue
        },
        handleClickOutside(e) {
            if (!this.$el.contains(e.target)) {
                this.isOpen = false
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside)
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside)
    }
}
</script>
