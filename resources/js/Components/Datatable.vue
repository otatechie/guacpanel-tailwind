<script setup>
import { ref, computed, watch } from 'vue'
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getSortedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
} from '@tanstack/vue-table'

const selectionColor = 'var(--selection-color)'

const styles = {
    input: "border border-gray-300 dark:border-gray-700 rounded-md text-sm dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-opacity-50 focus:border-transparent",
    button: "btn-primary-outline !p-2 focus:outline-none focus:ring-2 focus:ring-opacity-50",
    tableCell: "px-6 py-4 text-sm text-gray-900 dark:text-gray-200",
    tableHeader: "table-header",
    sortableHeader: "cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700",
    rowEven: "bg-white dark:bg-gray-900",
    rowOdd: "bg-gray-50 dark:bg-gray-800",
    rowHover: "hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors",
    rowSelected: "bg-[var(--selection-color-light)] dark:bg-[var(--selection-color-dark)]",
    focusRing: "focus:outline-none focus:ring-2 focus:ring-opacity-50"
}

const icons = {
    clearSearch: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`,
    export: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />`,
    firstPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />`,
    prevPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />`,
    nextPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />`,
    lastPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />`
}

const props = defineProps({
    data: { type: Array, required: true, default: () => [], },
    columns: { type: Array, required: true, },
    title: { type: String, default: 'Data Table' },
    enableSearch: { type: Boolean, default: true },
    enableExport: { type: Boolean, default: true },
    searchFields: { type: Array, default: () => [], },
    emptyMessage: { type: String, default: 'No data found' },
    emptyDescription: { type: String, default: 'Data will appear here' },
    exportFileName: { type: String, default: 'export' },
    pageSizeOptions: { type: Array, default: () => [10, 20, 30, 40, 50] },
    defaultPageSize: { type: Number, default: 10 },
    loading: { type: Boolean, default: false },
    error: { type: String, default: '' },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            per_page: 10,
            total: 0
        })
    }
})

const emit = defineEmits(['update:pagination'])
const sorting = ref([])
const selectedRows = ref({})
const searchQuery = ref('')
const pageSize = ref(props.defaultPageSize)
const pagination = ref({
    pageIndex: 0,
    pageSize: props.defaultPageSize,
})

const filteredData = computed(() => {
    if (!searchQuery.value || !props.searchFields.length) return props.data

    const query = searchQuery.value.toLowerCase()
    return props.data.filter(item =>
        props.searchFields.some(field =>
            String(item[field] || '').toLowerCase().includes(query)
        )
    )
})

const isServerPagination = computed(() => Boolean(props.pagination?.total))

const paginationInfo = computed(() => {
    const currentPage = isServerPagination.value
        ? props.pagination.current_page
        : table.getState().pagination.pageIndex + 1

    const pageSize = isServerPagination.value
        ? props.pagination.per_page
        : pagination.value.pageSize

    const total = isServerPagination.value
        ? props.pagination.total
        : table.getFilteredRowModel().rows.length

    const start = (currentPage - 1) * pageSize + 1
    const end = Math.min(currentPage * pageSize, total)
    const pageCount = Math.ceil(total / pageSize)

    return { currentPage, pageSize, total, start, end, pageCount }
})

const currentPage = computed(() => paginationInfo.value.currentPage)
const paginationStart = computed(() => paginationInfo.value.start)
const paginationEnd = computed(() => paginationInfo.value.end)
const totalRows = computed(() => paginationInfo.value.total)
const pageCount = computed(() => paginationInfo.value.pageCount)
const isFirstPage = computed(() => currentPage.value <= 1)
const isLastPage = computed(() => currentPage.value >= pageCount.value)
const hasSelection = computed(() => Object.keys(selectedRows.value).length > 0)
const selectionCount = computed(() => Object.keys(selectedRows.value).length)

const goToPage = (pageNumber) => {
    if (!isServerPagination.value) return
    if (pageNumber < 1 || pageNumber > pageCount.value) return
    updateServerPagination({ current_page: pageNumber })
}

const updateServerPagination = (updates) => {
    if (!isServerPagination.value) return

    emit('update:pagination', {
        ...props.pagination,
        ...updates
    })
}

const handlePageChange = (e) => {
    goToPage(Number(e.target.value))
}

const clearSearch = () => {
    searchQuery.value = ''
}

const formatValueForCSV = (value) => {
    if (value === null || value === undefined) return ''
    return String(value).replace(/,/g, ';')
}

const exportToCSV = () => {
    const rowsToExport = hasSelection.value
        ? table.getSelectedRowModel().rows
        : table.getRowModel().rows

    const dataToExport = rowsToExport.map(row => {
        const rowData = {}
        props.columns.forEach(column => {
            if (column.accessorKey) {
                rowData[column.header] = formatValueForCSV(row.original[column.accessorKey])
            }
        })
        return rowData
    })

    if (!dataToExport.length) return

    const csvContent = [
        Object.keys(dataToExport[0]).join(','),
        ...dataToExport.map(row => Object.values(row).join(','))
    ].join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)

    link.setAttribute('href', url)
    link.setAttribute('download', `${props.exportFileName}_${new Date().toISOString().split('T')[0]}.csv`)
    link.style.visibility = 'hidden'

    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const table = useVueTable({
    get data() { return filteredData.value },
    columns: props.columns,
    state: {
        get sorting() { return sorting.value },
        get rowSelection() { return selectedRows.value },
        get pagination() {
            if (isServerPagination.value) {
                return {
                    pageSize: props.pagination.per_page,
                    pageIndex: props.pagination.current_page - 1
                }
            }
            return pagination.value
        }
    },
    onRowSelectionChange: updaterOrValue => {
        selectedRows.value = typeof updaterOrValue === 'function'
            ? updaterOrValue(selectedRows.value)
            : updaterOrValue
    },
    onSortingChange: updaterOrValue => {
        sorting.value = typeof updaterOrValue === 'function'
            ? updaterOrValue(sorting.value)
            : updaterOrValue
    },
    onPaginationChange: updaterOrValue => {
        pagination.value = typeof updaterOrValue === 'function'
            ? updaterOrValue(pagination.value)
            : updaterOrValue
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: isServerPagination.value ? undefined : getPaginationRowModel(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
    enableSubRowSelection: false,
    getRowId: (row) => row.id || row.ID || JSON.stringify(row),
})

watch(pageSize, (newSize) => {
    if (!isServerPagination.value) return
    updateServerPagination({
        per_page: newSize,
        current_page: 1
    })
})

watch(() => props.data, () => {
    pagination.value.pageIndex = 0
}, { deep: true })
</script>


<template>
    <section class="relative">
        <!-- Error Alert -->
        <div v-if="error" role="alert"
            class="mb-4 p-4 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 rounded-md">
            {{ error }}
        </div>

        <!-- Loading Overlay -->
        <div v-if="loading" role="status"
            class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 flex items-center justify-center z-10">
            <span class="animate-spin rounded-full h-8 w-8 border-b-2"
                :style="{ borderColor: 'var(--primary-color)' }"></span>
        </div>

        <!-- Table Controls -->
        <header class="flex justify-between items-center mb-4">
            <!-- Left Controls: Page Size and Selection Count -->
            <div class="flex items-center gap-3">
                <div class="flex space-x-2 items-center">
                    <label class="text-sm text-gray-700 dark:text-gray-300">
                        {{ table.options.meta?.showRowsSelectLabel || 'Rows per page:' }}
                    </label>
                    <select
                        class="border border-gray-300 dark:border-gray-700 rounded-md text-sm dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :value="table.getState().pagination.pageSize"
                        @change="table.setPageSize(Number($event.target.value))"
                    >
                        <option v-for="pageSize in table.options.meta?.pageSizeOptions || [5, 10, 20, 30, 40, 50]" :key="pageSize" :value="pageSize">
                            {{ pageSize }}
                        </option>
                    </select>
                </div>

                <!-- Selection Count Badge -->
                <span 
                    v-if="hasSelection" 
                    role="status" 
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium"
                    :style="{ 
                        backgroundColor: 'var(--selection-color-light)', 
                        color: 'var(--selection-color)'
                    }"
                >
                    <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke-width="1.5" 
                        stroke="currentColor" 
                        class="w-4 h-4"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" 
                        />
                    </svg>
                    {{ selectionCount }} selected
                </span>
            </div>

            <!-- Right Controls: Search and Export -->
            <nav class="flex items-center gap-3">
                <!-- Search Input -->
                <div v-if="enableSearch" class="relative">
                    <label class="sr-only" :for="'table-search'">Search table</label>
                    <input type="search" :id="'table-search'" v-model="searchQuery" placeholder="Search"
                        :class="[styles.input, styles.focusRing, 'w-48 px-4 py-2']" 
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }" />
                    <button v-if="searchQuery" @click="clearSearch"
                        :class="[styles.focusRing, 'absolute right-3 top-2.5 text-gray-400 hover:text-gray-600']" 
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        aria-label="Clear search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                            v-html="icons.clearSearch"></svg>
                    </button>
                </div>

                <!-- Export Button -->
                <button v-if="enableExport" @click="exportToCSV"
                    :class="['btn-primary btn-sm inline-flex items-center gap-2', styles.focusRing]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"
                        v-html="icons.export"></svg>
                    Export CSV
                </button>
            </nav>
        </header>

        <!-- Main Table -->
        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="grid">
                <!-- Table Header -->
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <!-- Select All Checkbox -->
                        <th class="w-10 px-6 py-3">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-2 focus:ring-opacity-50 dark:bg-gray-800"
                                        :style="{ '--tw-ring-color': 'var(--primary-color)', 'color': selectionColor }"
                                        :checked="table.getIsAllRowsSelected()"
                                        :indeterminate="table.getIsSomeRowsSelected()"
                                        @change="table.toggleAllRowsSelected()"
                                    />
                                </label>
                            </div>
                        </th>

                        <!-- Column Headers -->
                        <th v-for="header in table.getHeaderGroups()[0].headers" :key="header.id" :class="[
                            styles.tableHeader,
                            header.column.getCanSort() ? styles.sortableHeader : ''
                        ]" @click="header.column.getToggleSortingHandler()?.($event)">
                            <div class="flex items-center gap-2">
                                {{ header.column.columnDef.header }}
                                <!-- Sort Indicator -->
                                <span v-if="header.column.getIsSorted()" 
                                      :style="{ color: selectionColor }" 
                                      class="text-gray-900 dark:text-gray-200">
                                    {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Empty State -->
                    <tr v-if="!table.getRowModel().rows.length" :class="styles.rowHover">
                        <td :colspan="columns.length + 1" class="px-6 py-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ emptyMessage }}</p>
                            <p class="mt-1 text-gray-400 dark:text-gray-500 text-sm">{{ emptyDescription }}</p>
                        </td>
                    </tr>

                    <!-- Data Rows -->
                    <tr v-for="(row, index) in table.getRowModel().rows" :key="row.id" 
                        :class="[
                            styles.rowHover,
                            row.getIsSelected() ? styles.rowSelected : (index % 2 === 0 ? styles.rowEven : styles.rowOdd)
                        ]">
                        <!-- Row Checkbox -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox rounded border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-opacity-50 dark:bg-gray-800"
                                        :style="{ '--tw-ring-color': 'var(--primary-color)', 'color': selectionColor }"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()"
                                    />
                                </label>
                            </div>
                        </td>

                        <!-- Cell Data -->
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id" :class="styles.tableCell">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <footer class="flex items-center justify-between mt-6 px-1">
            <!-- Results Count -->
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing
                <span class="font-medium">{{ paginationStart }}</span>
                to
                <span class="font-medium">{{ paginationEnd }}</span>
                of
                <span class="font-medium">{{ totalRows }}</span>
                results
            </p>

            <!-- Pagination Controls -->
            <nav class="flex items-center gap-2" aria-label="Pagination">
                <!-- Pagination Buttons -->
                <template v-if="isServerPagination">
                    <!-- First Page Button -->
                    <button 
                        :class="[styles.button, styles.focusRing]" 
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isFirstPage" 
                        @click="goToPage(1)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="icons.firstPage"></svg>
                    </button>

                    <!-- Previous Page Button -->
                    <button 
                        :class="[styles.button, styles.focusRing]" 
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isFirstPage" 
                        @click="goToPage(currentPage - 1)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="icons.prevPage"></svg>
                    </button>

                    <!-- Page Number Input -->
                    <div class="flex items-center gap-1">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Page</span>
                        <input 
                            type="number" 
                            :value="currentPage" 
                            @change="handlePageChange"
                            :class="[styles.input, styles.focusRing, 'w-16 px-3 py-2 text-center']" 
                            :style="{ '--tw-ring-color': 'var(--primary-color)' }" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">of {{ pageCount }}</span>
                    </div>

                    <!-- Next Page Button -->
                    <button 
                        :class="[styles.button, styles.focusRing]" 
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isLastPage" 
                        @click="goToPage(currentPage + 1)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="icons.nextPage"></svg>
                    </button>

                    <!-- Last Page Button -->
                    <button 
                        :class="[styles.button, styles.focusRing]" 
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isLastPage" 
                        @click="goToPage(pageCount)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="icons.lastPage"></svg>
                    </button>
                </template>

                <!-- Client-side pagination controls (when not using server pagination) -->
                <template v-else>
                    <button
                        class="px-2 py-1 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="[
                            table.getCanPreviousPage() ? 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' : 'text-gray-400 dark:text-gray-600',
                            styles.focusRing
                        ]"
                        :style="table.getCanPreviousPage() ? { '--tw-ring-color': 'var(--primary-color)' } : {}"
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.previousPage()"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="icons.prevPage"></svg>
                    </button>

                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of {{ table.getPageCount() }}
                    </span>

                    <button
                        class="px-2 py-1 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="[
                            table.getCanNextPage() ? 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' : 'text-gray-400 dark:text-gray-600',
                            styles.focusRing
                        ]"
                        :style="table.getCanNextPage() ? { '--tw-ring-color': 'var(--primary-color)' } : {}"
                        :disabled="!table.getCanNextPage()"
                        @click="table.nextPage()"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            v-html="icons.nextPage"></svg>
                    </button>
                </template>
            </nav>
        </footer>
    </section>
</template>
