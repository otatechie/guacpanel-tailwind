<template>
    <div class="relative">
        <!-- Error Alert -->
        <div v-if="error" class="mb-4 p-4 bg-red-50 text-red-700 rounded-md">
            {{ error }}
        </div>

        <!-- Loading Overlay -->
        <div v-if="loading" class="absolute inset-0 bg-white/50 flex items-center justify-center z-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Header Controls -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">{{ title }}</h2>
            <div class="flex items-center gap-3">
                <!-- Search -->
                <div v-if="enableSearch" class="relative">
                    <input type="text" v-model="searchQuery" placeholder="Search"
                        class="w-48 px-4 py-2 border border-gray-300 rounded-md transition-shadow duration-150 ease-in-out focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 text-sm" />
                    <button v-if="searchQuery" @click="searchQuery = ''"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Selected Count -->
                <span v-if="Object.keys(selectedRows).length"
                    class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium">
                    {{ Object.keys(selectedRows).length }} selected
                </span>

                <!-- Export -->
                <button v-if="enableExport" @click="exportToCSV" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>

        <!-- Page Size -->
        <div class="mb-4">
            <select v-model="pageSize" class="select-input transition-shadow duration-150 ease-in-out ">
                <option v-for="size in pageSizeOptions" :key="size" :value="size">
                    {{ size }} rows per page
                </option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-10 px-6 py-3">
                            <input type="checkbox" :checked="table.getIsAllRowsSelected()"
                                :indeterminate="table.getIsSomeRowsSelected()" @change="table.toggleAllRowsSelected()"
                                class="checkbox-input" />
                        </th>
                        <th v-for="header in table.getHeaderGroups()[0].headers" :key="header.id" :class="[
                            'table-header',
                            header.column.getCanSort() ? 'cursor-pointer hover:bg-gray-100' : ''
                        ]" @click="header.column.getToggleSortingHandler()?.($event)">
                            <div class="flex items-center gap-2">
                                {{ header.column.columnDef.header }}
                                <span v-if="header.column.getIsSorted()" class="text-gray-900">
                                    {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="!table.getRowModel().rows.length" class="hover:bg-gray-50">
                        <td :colspan="columns.length + 1" class="px-6 py-8 text-center">
                            <p class="text-gray-500 text-sm">{{ emptyMessage }}</p>
                            <p class="mt-1 text-gray-400 text-sm">{{ emptyDescription }}</p>
                        </td>
                    </tr>
                    <tr v-for="(row, index) in table.getRowModel().rows" :key="row.id" :class="[
                        'hover:bg-gray-50 transition-colors',
                        index % 2 === 0 ? 'bg-white' : 'bg-gray-50'
                    ]">
                        <td class="px-6 py-4">
                            <input type="checkbox" :checked="row.getIsSelected()" @change="row.toggleSelected()"
                                class="checkbox-input" />
                        </td>
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id"
                            class="px-6 py-4 text-sm text-gray-900">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-6 px-1">
            <div class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ paginationStart }}</span>
                to
                <span class="font-medium">{{ paginationEnd }}</span>
                of
                <span class="font-medium">{{ totalRows }}</span>
                results
            </div>
            <div class="flex items-center gap-2">
                <button class="pagination-btn" :disabled="!table.getCanPreviousPage()" @click="table.setPageIndex(0)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button class="pagination-btn" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-700">Page</span>
                    <input type="number" :value="currentPage" @change="handlePageChange"
                        class="w-16 px-3 py-2 text-center border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500"
                        min="1" :max="table.getPageCount()" />
                    <span class="text-sm text-gray-700">of {{ table.getPageCount() }}</span>
                </div>
                <button class="pagination-btn" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button class="pagination-btn" :disabled="!table.getCanNextPage()"
                    @click="table.setPageIndex(table.getPageCount() - 1)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

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

const props = defineProps({
    data: { type: Array, required: true, default: () => [] },
    columns: { type: Array, required: true },
    title: { type: String, default: 'Data Table' },
    enableSearch: { type: Boolean, default: true },
    enableExport: { type: Boolean, default: true },
    searchFields: { type: Array, default: () => [] },
    emptyMessage: { type: String, default: 'No data found' },
    emptyDescription: { type: String, default: 'Data will appear here' },
    exportFileName: { type: String, default: 'export' },
    pageSizeOptions: { type: Array, default: () => [10, 20, 30, 40, 50] },
    defaultPageSize: { type: Number, default: 10 },
    loading: { type: Boolean, default: false },
    error: { type: String, default: '' }
})

// State
const sorting = ref([])
const selectedRows = ref({})
const searchQuery = ref('')
const pageSize = ref(props.defaultPageSize)
const pagination = ref({
    pageIndex: 0,
    pageSize: props.defaultPageSize,
})

// Computed
const filteredData = computed(() => {
    if (!searchQuery.value || !props.searchFields.length) return props.data
    const query = searchQuery.value.toLowerCase()
    return props.data.filter(item =>
        props.searchFields.some(field =>
            String(item[field] || '').toLowerCase().includes(query)
        )
    )
})

const currentPage = computed(() => table.getState().pagination.pageIndex + 1)
const totalRows = computed(() => table.getFilteredRowModel().rows.length)
const paginationStart = computed(() =>
    table.getState().pagination.pageIndex * table.getState().pagination.pageSize + 1
)
const paginationEnd = computed(() =>
    Math.min(
        (table.getState().pagination.pageIndex + 1) * table.getState().pagination.pageSize,
        totalRows.value
    )
)

// Table Configuration
const table = useVueTable({
    get data() { return filteredData.value },
    columns: props.columns,
    state: {
        get sorting() { return sorting.value },
        get rowSelection() { return selectedRows.value },
        get pagination() { return pagination.value }
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
    getPaginationRowModel: getPaginationRowModel(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
    enableSubRowSelection: false,
    getRowId: (row) => row.id || row.ID || JSON.stringify(row),
})

// Methods
const handlePageChange = (e) => {
    const page = Number(e.target.value)
    table.setPageIndex(page - 1)
}

const exportToCSV = () => {
    const rowsToExport = Object.keys(selectedRows.value).length > 0
        ? table.getSelectedRowModel().rows
        : table.getRowModel().rows

    const dataToExport = rowsToExport.map(row => {
        const rowData = {}
        props.columns.forEach(column => {
            if (column.accessorKey) {
                rowData[column.header] = row.original[column.accessorKey]
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

watch(pageSize, (newSize) => {
    pagination.value = {
        ...pagination.value,
        pageSize: newSize,
    }
})

watch(() => props.data, () => {
    pagination.value.pageIndex = 0
}, { deep: true })
</script>
