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

const currentPage = computed(() => table.getState().pagination.pageIndex + 1)
const totalRows = computed(() => props.pagination?.total || table.getFilteredRowModel().rows.length)
const paginationStart = computed(() =>
    ((props.pagination?.current_page || 1) - 1) * (props.pagination?.per_page || pageSize.value) + 1
)
const paginationEnd = computed(() =>
    Math.min(
        (props.pagination?.current_page || 1) * (props.pagination?.per_page || pageSize.value),
        totalRows.value
    )
)

const table = useVueTable({
    get data() { return filteredData.value },
    columns: props.columns,
    state: {
        get sorting() { return sorting.value },
        get rowSelection() { return selectedRows.value },
        get pagination() {
            if (props.pagination?.total) {
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
    getPaginationRowModel: props.pagination?.total ? undefined : getPaginationRowModel(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
    enableSubRowSelection: false,
    getRowId: (row) => row.id || row.ID || JSON.stringify(row),
})

const handlePageChange = (e) => {
    if (!props.pagination?.total) return
    const page = Number(e.target.value)
    if (page < 1 || page > Math.ceil(props.pagination.total / props.pagination.per_page)) return
    emit('update:pagination', {
        ...props.pagination,
        current_page: page
    })
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

const emit = defineEmits(['update:pagination'])

watch(pageSize, (newSize) => {
    const newPagination = {
        ...props.pagination,
        per_page: newSize,
        current_page: 1
    }
    emit('update:pagination', newPagination)
})

watch(() => props.data, () => {
    pagination.value.pageIndex = 0
}, { deep: true })
</script>

<template>
    <section class="relative">
        <div v-if="error" role="alert"
            class="mb-4 p-4 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 rounded-md">
            {{ error }}
        </div>

        <div v-if="loading" role="status"
            class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 flex items-center justify-center z-10">
            <span class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 dark:border-purple-400"></span>
        </div>

        <header class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <label class="sr-only" :for="'page-size-select'">Rows per page</label>
                <select :id="'page-size-select'" v-model="pageSize"
                    class="select-input transition-shadow duration-150 ease-in-out bg-transparent dark:text-gray-200">
                    <option v-for="size in pageSizeOptions" :key="size" :value="size" class="dark:bg-gray-900">
                        {{ size }} rows per page
                    </option>
                </select>

                <span v-if="Object.keys(selectedRows).length" role="status"
                    class="px-3 py-1.5 rounded-full text-purple-700 dark:text-purple-400 text-xs font-medium">
                    {{ Object.keys(selectedRows).length }} selected
                </span>
            </div>

            <nav class="flex items-center gap-3">
                <div v-if="enableSearch" class="relative">
                    <label class="sr-only" :for="'table-search'">Search table</label>
                    <input type="search" :id="'table-search'" v-model="searchQuery" placeholder="Search"
                        class="w-48 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md transition-shadow duration-150 ease-in-out focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 text-sm dark:bg-gray-800 dark:text-gray-200" />
                    <button v-if="searchQuery" @click="searchQuery = ''"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600" aria-label="Clear search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <button v-if="enableExport" @click="exportToCSV"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:bg-purple-500 dark:hover:bg-purple-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV
                </button>
            </nav>
        </header>

        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="grid">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="w-10 px-6 py-3">
                            <input type="checkbox" :checked="table.getIsAllRowsSelected()"
                                :indeterminate="table.getIsSomeRowsSelected()" @change="table.toggleAllRowsSelected()"
                                class="checkbox-input" />
                        </th>
                        <th v-for="header in table.getHeaderGroups()[0].headers" :key="header.id" :class="[
                            'table-header',
                            header.column.getCanSort() ? 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700' : ''
                        ]" @click="header.column.getToggleSortingHandler()?.($event)">
                            <div class="flex items-center gap-2">
                                {{ header.column.columnDef.header }}
                                <span v-if="header.column.getIsSorted()" class="text-gray-900 dark:text-gray-200">
                                    {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-if="!table.getRowModel().rows.length" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td :colspan="columns.length + 1" class="px-6 py-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ emptyMessage }}</p>
                            <p class="mt-1 text-gray-400 dark:text-gray-500 text-sm">{{ emptyDescription }}</p>
                        </td>
                    </tr>
                    <tr v-for="(row, index) in table.getRowModel().rows" :key="row.id" :class="[
                        'hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
                        index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800'
                    ]">
                        <td class="px-6 py-4">
                            <input type="checkbox" :checked="row.getIsSelected()" @change="row.toggleSelected()"
                                class="checkbox-input" />
                        </td>
                        <td v-for="cell in row.getVisibleCells()" :key="cell.id"
                            class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer class="flex items-center justify-between mt-6 px-1">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing
                <span class="font-medium">{{ paginationStart }}</span>
                to
                <span class="font-medium">{{ paginationEnd }}</span>
                of
                <span class="font-medium">{{ totalRows }}</span>
                results
            </p>
            <nav class="flex items-center gap-2" aria-label="Pagination">
                <button class="pagination-btn" :disabled="props.pagination?.current_page <= 1"
                    @click="emit('update:pagination', { ...props.pagination, current_page: 1 })">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button class="pagination-btn" :disabled="props.pagination?.current_page <= 1"
                    @click="emit('update:pagination', { ...props.pagination, current_page: props.pagination.current_page - 1 })">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Page</span>
                    <input type="number" :value="currentPage" @change="handlePageChange"
                        class="w-16 px-3 py-2 text-center border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-gray-200"
                        min="1" :max="table.getPageCount()" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">of {{ table.getPageCount() }}</span>
                </div>
                <button class="pagination-btn"
                    :disabled="props.pagination?.current_page >= Math.ceil(props.pagination.total / props.pagination.per_page)"
                    @click="emit('update:pagination', { ...props.pagination, current_page: props.pagination.current_page + 1 })">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button class="pagination-btn"
                    :disabled="props.pagination?.current_page >= Math.ceil(props.pagination.total / props.pagination.per_page)"
                    @click="emit('update:pagination', { ...props.pagination, current_page: Math.ceil(props.pagination.total / props.pagination.per_page) })">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>
            </nav>
        </footer>
    </section>
</template>
