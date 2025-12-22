<script setup>
import { ref, computed, watchEffect, shallowRef, onBeforeUnmount } from 'vue'
const props = defineProps({
    column: {
        type: Object,
        required: true,
    },
    table: {
        type: Object,
        required: true,
    },
})
const memoizedValues = shallowRef(null)
const memoizedUniqueCount = shallowRef(0)
const columnFilterValue = computed(() => {
    return props.column.getFilterValue()
})
const firstValue = computed(() => {
    const values = props.table.getPreFilteredRowModel().flatRows
    const firstValue = values[0]?.getValue(props.column.id)
    return firstValue
})
const selectedRange = computed(() => {
    const defaultValue = firstValue.value ?? 0
    return columnFilterValue.value || [defaultValue, defaultValue]
})
const getDisplayValue = value => {
    if (value === null || value === undefined) return ''
    if (typeof value === 'string' || typeof value === 'number') return String(value)
    if (typeof value === 'object') {
        if (value.name) return value.name
        if (value.title) return value.title
        if (value.label) return value.label
        if (value.id) return value.id
        return JSON.stringify(value)
    }
    return String(value)
}
const sortedUniqueValues = computed(() => {
    const rows = props.table.getPreFilteredRowModel().flatRows
    if (memoizedValues.value && memoizedValues.value.length === rows.length) {
        return memoizedValues.value
    }
    const values = rows
        .map(row => row.getValue(props.column.id))
        .map(value => ({
            original: value,
            display: getDisplayValue(value),
        }))
        .filter((item, index, self) => self.findIndex(i => i.display === item.display) === index)
        .sort((a, b) => a.display.localeCompare(b.display))
    memoizedValues.value = values
    memoizedUniqueCount.value = values.length
    return values
})
const shouldUseSelect = computed(() => {
    const uniqueCount = memoizedUniqueCount.value || sortedUniqueValues.value.length
    if (uniqueCount <= 1 || uniqueCount > 20) return false
    return sortedUniqueValues.value.every(
        item =>
            typeof item.original === 'string' ||
            typeof item.original === 'number' ||
            (typeof item.original === 'object' && item.original !== null)
    )
})
const shouldUseRange = computed(() => {
    if (props.column.columnDef.filterFn === 'inNumberRange') return true
    const uniqueCount = memoizedUniqueCount.value || sortedUniqueValues.value.length
    if (uniqueCount <= 20) return false
    return sortedUniqueValues.value.every(
        item => typeof item.original === 'number' || !isNaN(Number(item.display))
    )
})
let filterTimeout = null
const handleFilterChange = value => {
    if (filterTimeout) {
        clearTimeout(filterTimeout)
    }
    filterTimeout = setTimeout(() => {
        props.column.setFilterValue(value)
    }, 150)
}
const handleRangeChange = (index, value) => {
    const newRange = [...selectedRange.value]
    // Convert empty string to undefined for proper clearing
    newRange[index] = value === '' ? undefined : value
    handleFilterChange(newRange)
}
const clearFilter = () => {
    if (filterTimeout) {
        clearTimeout(filterTimeout)
    }
    props.column.setFilterValue(undefined)
}
watchEffect(() => {
    props.column.id
    memoizedValues.value = null
    memoizedUniqueCount.value = 0
})

// Cleanup timeout on component unmount
onBeforeUnmount(() => {
    if (filterTimeout) {
        clearTimeout(filterTimeout)
        filterTimeout = null
    }
})
</script>
<template>
    <div class="space-y-2">
        <div v-if="column.getCanFilter()">
            <div v-if="!shouldUseSelect && !shouldUseRange" class="relative">
                <input :value="columnFilterValue ?? ''" @input="handleFilterChange($event.target.value)"
                    placeholder="Filter..."
                    class="w-full truncate rounded border border-gray-300 bg-white px-2 py-1 pr-6 text-sm font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-400" />
                <button v-if="columnFilterValue" @click="clearFilter"
                    class="focus:ring-opacity-50 absolute top-1/2 right-1 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 focus:ring-2 focus:outline-none dark:hover:text-gray-200"
                    :style="{ '--tw-ring-color': 'var(--primary-color)' }" aria-label="Clear filter">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div v-else-if="shouldUseSelect" class="relative">
                <select :value="columnFilterValue ?? ''" @change="handleFilterChange($event.target.value)"
                    class="w-full appearance-none truncate rounded border border-gray-300 bg-white px-2 py-1 pr-8 text-sm font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-400">
                    <option value="" class="bg-white font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-200">
                        All
                    </option>
                    <option v-for="item in sortedUniqueValues" :key="item.display" :value="item.display"
                        class="bg-white font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-200">
                        {{ item.display }}
                    </option>
                </select>
                <div class="pointer-events-none absolute top-1/2 right-2 -translate-y-1/2">
                    <svg class="h-3 w-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <button v-if="columnFilterValue" @click="clearFilter"
                    class="focus:ring-opacity-50 absolute top-1/2 right-6 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 focus:ring-2 focus:outline-none dark:hover:text-gray-200"
                    :style="{ '--tw-ring-color': 'var(--primary-color)' }" aria-label="Clear filter">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div v-else-if="shouldUseRange" class="space-y-1">
                <div class="relative">
                    <input type="number" :value="selectedRange[0]"
                        @input="handleRangeChange(0, Number($event.target.value))" placeholder="Min"
                        class="w-full truncate rounded border border-gray-300 bg-white px-2 py-1 pr-6 text-sm font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-400" />
                    <button v-if="selectedRange[0]" @click="handleRangeChange(0, '')"
                        class="focus:ring-opacity-50 absolute top-1/2 right-1 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 focus:ring-2 focus:outline-none dark:hover:text-gray-200"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }" aria-label="Clear min value">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="relative">
                    <input type="number" :value="selectedRange[1]"
                        @input="handleRangeChange(1, Number($event.target.value))" placeholder="Max"
                        class="w-full truncate rounded border border-gray-300 bg-white px-2 py-1 pr-6 text-sm font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-400" />
                    <button v-if="selectedRange[1]" @click="handleRangeChange(1, '')"
                        class="focus:ring-opacity-50 absolute top-1/2 right-1 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 focus:ring-2 focus:outline-none dark:hover:text-gray-200"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }" aria-label="Clear max value">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
