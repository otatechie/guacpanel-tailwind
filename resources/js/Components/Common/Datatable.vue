<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getSortedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getFacetedRowModel,
    getFacetedUniqueValues,
    getFacetedMinMaxValues,
} from '@tanstack/vue-table'
import Modal from '@/Components/Notifications/Modal.vue'
import Filter from '@/Components/Filter.vue'
import { useServerPagination } from '@/Composables/useServerPagination'

const selectionColor = 'var(--selection-color)'

const styles = {
    input: 'border border-gray-300 dark:border-gray-700 rounded-md text-sm dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-opacity-50 focus:border-transparent',
    button: 'btn btn-3d-secondary btn-xs !p-2 focus:outline-none focus:ring-2 focus:ring-opacity-50',
    tableCell: 'px-6 py-4 text-xs text-gray-900 dark:text-gray-100 text-left',
    tableHeader: 'table-header',
    sortableHeader: 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700',
    rowEven: 'bg-white dark:bg-gray-800',
    rowOdd: 'bg-gray-50 dark:bg-gray-900',
    rowHover: 'hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors',
    rowSelected: 'bg-[var(--selection-color-light)] dark:bg-[var(--selection-color-dark)]',
    focusRing: 'focus:outline-none focus:ring-2 focus:ring-opacity-50',
    dropdown:
        'absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none',
}

const icons = {
    clearSearch: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`,
    export: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />`,
    firstPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />`,
    prevPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />`,
    nextPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />`,
    lastPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />`,
    chevronDown: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />`,
}

const props = defineProps({
    data: {
        type: Array,
        required: true,
        default: () => [],
    },
    filtersEnabled: {
        type: Boolean,
        default: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    title: {
        type: String,
        default: 'Data Table',
    },
    enableSearch: {
        type: Boolean,
        default: true,
    },
    enableExport: {
        type: Boolean,
        default: true,
    },
    enableFiltering: {
        type: Boolean,
        default: true,
    },
    emptyMessage: {
        type: String,
        default: 'No data found',
    },
    emptyDescription: {
        type: String,
        default: 'Data will appear here',
    },
    exportFileName: {
        type: String,
        default: 'export',
    },
    pageSizeOptions: {
        type: Array,
        default: () => [10, 25, 50, 'All'],
    },
    defaultPageSize: {
        type: Number,
        default: 10,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
    bulkDeleteRoute: {
        type: String,
        default: '',
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            per_page: 10,
            total: 0,
        }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    formatExportData: {
        type: Function,
        default: null,
    },
    routeName: {
        type: String,
        default: '',
    },
    routeParams: {
        type: Object,
        default: () => ({}),
    },
    exportRoute: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:pagination', 'bulk-delete'])
const rowSelection = ref({})
const expandedRows = ref([])
const pagination = ref({
    pageIndex: 0,
    pageSize: props.defaultPageSize,
})
const showDeleteModal = ref(false)

const getNavigationUrl = () => {
    if (props.routeName) {
        return route(props.routeName, props.routeParams)
    }
    return window.location.pathname
}

// Initialize server-side pagination composable
const serverPagination = useServerPagination({
    routeUrl: getNavigationUrl(),
    pagination: props.pagination,
    filters: props.filters,
})

const { columnFilters, sorting, globalFilter } = serverPagination

onMounted(() => {
    if (isServerPagination.value) {
        serverPagination.init()
    }
})

const toggleRow = index => {
    const currentIndex = expandedRows.value.indexOf(index)
    if (currentIndex > -1) {
        expandedRows.value.splice(currentIndex, 1)
    } else {
        expandedRows.value.push(index)
    }
}

const handleSelectAll = () => {
    table.toggleAllRowsSelected()
}

const filteredData = computed(() => props.data)

const isServerPagination = computed(() => {
    if (!props.pagination) return false
    if (
        typeof props.pagination.current_page !== 'undefined' &&
        typeof props.pagination.per_page !== 'undefined'
    ) {
        return true
    }
    if (typeof props.pagination.last_page !== 'undefined') {
        return true
    }
    return Boolean(props.pagination.total)
})

const isAllSelected = computed(() => {
    if (!isServerPagination.value) return false
    const originalPerPage = props.filters?.per_page
    return originalPerPage === 'all' || originalPerPage === 'All'
})

const paginationInfo = computed(() => {
    const isServer = isServerPagination.value
    const currentPage = isServer
        ? props.pagination.current_page
        : table.getState().pagination.pageIndex + 1
    const pageSize = isServer ? props.pagination.per_page : pagination.value.pageSize
    const total = isServer ? props.pagination.total : table.getFilteredRowModel().rows.length

    if (pageSize === 'all' || pageSize === 'All') {
        return {
            currentPage: 1,
            pageSize: 'all',
            total,
            start: 1,
            end: total,
            pageCount: 1,
        }
    }

    const start = (currentPage - 1) * pageSize + 1
    const end = Math.min(currentPage * pageSize, total)
    const pageCount = Math.ceil(total / pageSize)

    return { currentPage, pageSize, total, start, end, pageCount }
})

const selectedRows = computed(() => table.getSelectedRowModel().rows)
const hasSelection = computed(() => selectedRows.value.length > 0)
const selectionCount = computed(() => selectedRows.value.length)

const handleBulkDelete = () => {
    if (!props.bulkDeleteRoute) return
    showDeleteModal.value = false
    emit('bulk-delete', {
        route: props.bulkDeleteRoute,
        selectedRows: selectedRows.value.map(row => row.original),
    })
}

const isFirstPage = computed(() => paginationInfo.value.currentPage <= 1)
const isLastPage = computed(
    () => paginationInfo.value.currentPage >= paginationInfo.value.pageCount
)

const goToPage = pageNumber => {
    if (!isServerPagination.value) return
    if (pageNumber < 1 || pageNumber > paginationInfo.value.pageCount) return
    updatePagination({ current_page: pageNumber })
}

const updatePagination = updates => {
    if (!isServerPagination.value) return

    const newPagination = {
        ...props.pagination,
        ...updates,
    }

    const queryParams = {
        ...props.filters,
        page: newPagination.current_page,
        per_page: newPagination.per_page === 'all' ? 'all' : Number(newPagination.per_page),
    }

    router.get(getNavigationUrl(), queryParams, {
        preserveState: true,
        preserveScroll: true,
    })
}

const handlePageChange = e => {
    goToPage(Number(e.target.value))
}

const handlePageSizeChange = e => {
    const newSize = e.target.value === 'all' ? 'all' : Number(e.target.value)
    if (isServerPagination.value) {
        updatePagination({ per_page: newSize, current_page: 1 })
    } else {
        pagination.value.pageSize =
            newSize === 'all' ? table.getFilteredRowModel().rows.length : newSize
        pagination.value.pageIndex = 0
    }
}

const formatValueForCSV = value => {
    if (value === null || value === undefined) return ''
    if (typeof value === 'object') {
        if (value instanceof Date) return value.toISOString()
        return Object.values(value).join(' - ')
    }
    return String(value).replace(/,/g, ';')
}

const getColumnHeader = column => {
    if (typeof column.header === 'string') return column.header
    if (column.accessorKey) {
        return column.accessorKey
            .replace(/([a-z])([A-Z])/g, '$1 $2')
            .replace(/_/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase())
    }
    return ''
}

const exportToCSV = () => {
    if (props.exportRoute) {
        const params = new URLSearchParams(props.routeParams)
        window.open(props.exportRoute + '?' + params.toString(), '_blank')
        return
    }

    const rowsToExport = hasSelection.value
        ? table.getSelectedRowModel().rows
        : table.getRowModel().rows

    const dataToExport = rowsToExport.map(row => {
        if (props.formatExportData) {
            return props.formatExportData(row.original)
        }

        const rowData = {}
        props.columns.forEach(column => {
            if (column.accessorKey) {
                const header = getColumnHeader(column)
                const value = column.accessorFn
                    ? column.accessorFn(row.original)
                    : row.original[column.accessorKey]
                rowData[header] = formatValueForCSV(value)
            } else if (column.id && !column.id.startsWith('_')) {
                const header = getColumnHeader(column)
                const cell = row.getVisibleCells().find(c => c.column.id === column.id)
                if (cell?.getValue) {
                    rowData[header] = formatValueForCSV(cell.getValue())
                }
            }
        })
        return rowData
    })

    if (!dataToExport.length) return

    const escapeCSV = value => {
        if (value === null || value === undefined) return ''
        const str = String(value)
        if (str.includes(',') || str.includes('\n') || str.includes('"')) {
            return `"${str.replace(/"/g, '""')}"`
        }
        return str
    }

    const csvContent = [
        Object.keys(dataToExport[0]).map(escapeCSV).join(','),
        ...dataToExport.map(row => Object.values(row).map(escapeCSV).join(',')),
    ].join('\n')

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)
    link.setAttribute('href', url)
    link.setAttribute(
        'download',
        `${props.exportFileName}_${new Date().toISOString().split('T')[0]}.csv`
    )
    link.style.visibility = 'hidden'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

const table = useVueTable({
    get data() {
        return filteredData.value
    },
    columns: props.columns,
    state: {
        get sorting() {
            return sorting.value
        },
        get rowSelection() {
            return rowSelection.value
        },
        get columnFilters() {
            return columnFilters.value
        },
        get globalFilter() {
            return globalFilter.value
        },
        get pagination() {
            if (isServerPagination.value) {
                return {
                    pageSize: props.pagination.per_page,
                    pageIndex: props.pagination.current_page - 1,
                }
            }
            return {
                pageIndex: pagination.value.pageIndex,
                pageSize: pagination.value.pageSize,
            }
        },
    },
    onRowSelectionChange: updaterOrValue => {
        rowSelection.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(rowSelection.value)
                : updaterOrValue
    },
    onSortingChange: updaterOrValue => {
        sorting.value =
            typeof updaterOrValue === 'function' ? updaterOrValue(sorting.value) : updaterOrValue
    },
    onColumnFiltersChange: updaterOrValue => {
        columnFilters.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(columnFilters.value)
                : updaterOrValue
    },
    onGlobalFilterChange: updaterOrValue => {
        globalFilter.value =
            typeof updaterOrValue === 'function'
                ? updaterOrValue(globalFilter.value)
                : updaterOrValue
    },
    onPaginationChange: updaterOrValue => {
        pagination.value =
            typeof updaterOrValue === 'function' ? updaterOrValue(pagination.value) : updaterOrValue
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedUniqueValues: getFacetedUniqueValues(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
    getPaginationRowModel: isServerPagination.value ? undefined : getPaginationRowModel(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
    getRowId: row => row.id || row.ID || JSON.stringify(row),
})

watch(
    () => props.data,
    () => {
        pagination.value.pageIndex = 0
    },
    { deep: true }
)
</script>

<template>
    <section class="relative">
        <div
            v-if="error"
            role="alert"
            class="mb-4 rounded-md bg-red-50 p-4 text-red-700 dark:bg-red-950 dark:text-red-400">
            {{ error }}
        </div>

        <div
            v-if="loading"
            role="status"
            class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-gray-900/50">
            <span
                class="h-8 w-8 animate-spin rounded-full border-b-2"
                :style="{ borderColor: 'var(--primary-color)' }"></span>
        </div>

        <header
            class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div
                class="flex w-full flex-col items-start gap-3 sm:w-auto sm:flex-row sm:items-center">
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-700 dark:text-gray-300">
                        {{ table.options.meta?.showRowsSelectLabel || 'Rows per page:' }}
                    </label>
                    <select
                        class="focus:ring-opacity-50 rounded-md border border-gray-300 text-sm focus:ring-2 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :value="
                            isServerPagination
                                ? isAllSelected
                                    ? 'all'
                                    : String(props.pagination.per_page)
                                : String(pagination.pageSize)
                        "
                        @change="handlePageSizeChange">
                        <option
                            v-for="size in pageSizeOptions"
                            :key="size"
                            :value="size === 'All' ? 'all' : size">
                            {{ size === 'All' ? 'All' : size }}
                        </option>
                    </select>
                </div>

                <div v-if="hasSelection" class="flex items-center gap-6">
                    <span
                        role="status"
                        class="flex items-center gap-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-4 w-4 text-green-600 dark:text-green-500">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ selectionCount }} selected
                    </span>
                    <button
                        v-if="bulkDeleteRoute"
                        @click="showDeleteModal = true"
                        class="btn-3d-danger btn-xs inline-flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-4 w-4">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Bulk Delete
                    </button>
                    <slot name="bulk-actions" :selected-rows="selectedRows" />
                </div>
            </div>

            <nav
                class="flex w-full flex-col items-start gap-3 sm:w-auto sm:flex-row sm:items-center">
                <div v-if="enableSearch" class="relative w-full sm:w-48">
                    <label class="sr-only" :for="'table-search'">Search table</label>
                    <input
                        type="text"
                        :value="globalFilter ?? ''"
                        @input="e => (globalFilter = String(e.target.value))"
                        :class="[styles.input, styles.focusRing, 'w-full px-4 py-2 pr-8'].join(' ')"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        placeholder="Search all columns..." />
                    <button
                        v-if="globalFilter"
                        @click="globalFilter = ''"
                        :class="[
                            styles.focusRing,
                            'absolute top-1/2 right-2 -translate-y-1/2 text-gray-400 hover:text-gray-600',
                        ]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        aria-label="Clear search">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            v-html="icons.clearSearch"></svg>
                    </button>
                </div>

                <button
                    v-if="enableExport"
                    @click="exportToCSV"
                    class="btn-secondary btn-sm inline-flex cursor-pointer items-center gap-2">
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                        v-html="icons.export"></svg>
                    Export CSV
                </button>
            </nav>
        </header>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="block space-y-3 p-3 md:hidden">
                <div
                    class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            class="form-checkbox focus:ring-opacity-50 rounded border-gray-300 focus:ring-2 dark:border-gray-700 dark:bg-gray-800"
                            :style="{
                                '--tw-ring-color': 'var(--primary-color)',
                                color: selectionColor,
                            }"
                            :checked="table.getIsAllRowsSelected()"
                            :indeterminate="table.getIsSomeRowsSelected()"
                            @change="handleSelectAll" />
                        <span class="ml-2 text-xs font-medium text-gray-700 dark:text-gray-300">
                            {{ table.getIsAllRowsSelected() ? 'Deselect All' : 'Select All' }}
                        </span>
                    </label>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ table.getFilteredSelectedRowModel().rows.length }} of
                        {{ table.getFilteredRowModel().rows.length }} selected
                    </div>
                </div>

                <div
                    v-for="(row, index) in table.getRowModel().rows"
                    :key="row.id"
                    class="rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                    <div class="p-2">
                        <div class="mb-1.5 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox focus:ring-opacity-50 rounded border-gray-300 focus:ring-2 dark:border-gray-700 dark:bg-gray-800"
                                        :style="{
                                            '--tw-ring-color': 'var(--primary-color)',
                                            color: selectionColor,
                                        }"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()" />
                                    <span
                                        class="ml-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Select
                                    </span>
                                </label>
                                <button
                                    @click="toggleRow(index)"
                                    class="group flex items-center gap-1 rounded bg-gray-50 px-1.5 py-0.5 transition-all duration-200 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <svg
                                        class="h-3 w-3 text-gray-500 transition-transform duration-200 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300"
                                        :class="{ 'rotate-90': expandedRows.includes(index) }"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span
                                        class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                        {{ expandedRows.includes(index) ? 'Less' : 'More' }}
                                    </span>
                                </button>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <slot name="mobile-actions" :row="row.original" />
                            </div>
                        </div>

                        <div class="mb-1.5 border-b border-gray-100 dark:border-gray-700"></div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <div
                                v-for="(cell, cellIndex) in row.getVisibleCells().slice(0, 2)"
                                :key="cell.id"
                                class="flex flex-col space-y-0">
                                <dt
                                    class="text-xs font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                    {{ getColumnHeader(cell.column.columnDef) }}
                                </dt>
                                <dd class="text-xs font-medium text-gray-900 dark:text-gray-100">
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()" />
                                </dd>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="expandedRows.includes(index)"
                        class="border-t border-gray-100 bg-gray-50 dark:border-gray-700 dark:bg-gray-700/30">
                        <div class="space-y-2 p-2">
                            <div class="grid grid-cols-1 gap-2">
                                <div
                                    v-for="(cell, cellIndex) in row.getVisibleCells().slice(2)"
                                    :key="cell.id"
                                    class="flex flex-col space-y-0">
                                    <dt
                                        class="text-xs font-medium tracking-wide text-gray-500 uppercase dark:text-gray-400">
                                        {{ getColumnHeader(cell.column.columnDef) }}
                                    </dt>
                                    <dd
                                        class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                        <FlexRender
                                            :render="cell.column.columnDef.cell"
                                            :props="cell.getContext()" />
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <table
                class="hidden min-w-full divide-y divide-gray-200 md:table dark:divide-gray-700"
                role="grid">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="w-10 px-6 py-3">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox focus:ring-opacity-50 rounded border-gray-300 text-blue-600 focus:ring-2 dark:border-gray-700 dark:bg-gray-800"
                                        :style="{
                                            '--tw-ring-color': 'var(--primary-color)',
                                            color: selectionColor,
                                        }"
                                        :checked="table.getIsAllRowsSelected()"
                                        :indeterminate="table.getIsSomeRowsSelected()"
                                        @change="handleSelectAll" />
                                </label>
                            </div>
                        </th>

                        <th
                            v-for="header in table.getHeaderGroups()[0].headers"
                            :key="header.id"
                            :class="[
                                styles.tableHeader,
                                header.column.getCanSort() ? styles.sortableHeader : '',
                            ]"
                            @click="header.column.getToggleSortingHandler()?.($event)">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-900 dark:text-gray-400">
                                    {{ header.column.columnDef.header }}
                                </span>
                                <span
                                    v-if="header.column.getIsSorted()"
                                    :style="{ color: selectionColor }"
                                    class="text-gray-900 dark:text-gray-200">
                                    {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                </span>
                            </div>
                        </th>
                    </tr>

                    <tr
                        v-if="
                            filtersEnabled &&
                            enableFiltering &&
                            table.getHeaderGroups()[0].headers.some(h => h.column.getCanFilter())
                        ">
                        <th class="px-6 py-2"></th>
                        <th
                            v-for="header in table.getHeaderGroups()[0].headers"
                            :key="`filter-${header.id}`"
                            class="px-6 py-2">
                            <Filter
                                v-if="header.column.getCanFilter()"
                                :column="header.column"
                                :table="table" />
                        </th>
                    </tr>
                </thead>

                <tbody
                    class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                    <tr v-if="!table.getRowModel().rows.length" :class="styles.rowHover">
                        <td :colspan="columns.length + 1" class="px-6 py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ emptyMessage }}
                            </p>
                            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                                {{ emptyDescription }}
                            </p>
                        </td>
                    </tr>

                    <tr
                        v-for="(row, index) in table.getRowModel().rows"
                        :key="row.id"
                        :class="[
                            styles.rowHover,
                            row.getIsSelected()
                                ? styles.rowSelected
                                : index % 2 === 0
                                  ? styles.rowEven
                                  : styles.rowOdd,
                        ]">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox focus:ring-opacity-50 rounded border-gray-300 focus:ring-2 dark:border-gray-700 dark:bg-gray-800"
                                        :style="{
                                            '--tw-ring-color': 'var(--primary-color)',
                                            color: selectionColor,
                                        }"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()" />
                                </label>
                            </div>
                        </td>

                        <td
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                            :class="styles.tableCell">
                            <FlexRender
                                :render="cell.column.columnDef.cell"
                                :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer
            class="mt-6 flex flex-col items-center justify-between gap-4 px-3 sm:flex-row sm:px-1">
            <p class="text-center text-xs text-gray-700 sm:text-left dark:text-gray-300">
                Showing
                <span class="font-medium">{{ paginationInfo.start }}</span>
                to
                <span class="font-medium">{{ paginationInfo.end }}</span>
                of
                <span class="font-medium">{{ paginationInfo.total }}</span>
                results
            </p>

            <nav class="flex items-center gap-2" aria-label="Pagination">
                <template v-if="isServerPagination">
                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isFirstPage"
                        @click="goToPage(1)">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.firstPage"></svg>
                    </button>

                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isFirstPage"
                        @click="goToPage(paginationInfo.currentPage - 1)">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.prevPage"></svg>
                    </button>

                    <div class="flex items-center gap-1">
                        <span class="hidden text-xs text-gray-700 sm:inline dark:text-gray-300">
                            Page
                        </span>
                        <input
                            type="number"
                            :value="paginationInfo.currentPage"
                            @change="handlePageChange"
                            :class="[styles.input, styles.focusRing, 'w-16 px-3 py-2 text-center']"
                            :style="{ '--tw-ring-color': 'var(--primary-color)' }" />
                        <span class="hidden text-xs text-gray-700 sm:inline dark:text-gray-300">
                            of {{ paginationInfo.pageCount }}
                        </span>
                    </div>

                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isLastPage"
                        @click="goToPage(paginationInfo.currentPage + 1)">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.nextPage"></svg>
                    </button>

                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isLastPage"
                        @click="goToPage(paginationInfo.pageCount)">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.lastPage"></svg>
                    </button>
                </template>

                <template v-else>
                    <button
                        class="rounded-md px-2 py-1 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="[
                            table.getCanPreviousPage()
                                ? 'text-gray-700 hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-700'
                                : 'text-gray-400 dark:text-gray-600',
                            styles.focusRing,
                        ]"
                        :style="
                            table.getCanPreviousPage()
                                ? { '--tw-ring-color': 'var(--primary-color)' }
                                : {}
                        "
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.previousPage()">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.prevPage"></svg>
                    </button>

                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of
                        {{ table.getPageCount() }}
                    </span>

                    <button
                        class="rounded-md px-2 py-1 disabled:cursor-not-allowed disabled:opacity-50"
                        :class="[
                            table.getCanNextPage()
                                ? 'text-gray-700 hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-700'
                                : 'text-gray-400 dark:text-gray-600',
                            styles.focusRing,
                        ]"
                        :style="
                            table.getCanNextPage()
                                ? { '--tw-ring-color': 'var(--primary-color)' }
                                : {}
                        "
                        :disabled="!table.getCanNextPage()"
                        @click="table.nextPage()">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.nextPage"></svg>
                    </button>
                </template>
            </nav>
        </footer>

        <Modal :show="showDeleteModal" @close="showDeleteModal = false" size="sm">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    Confirm Deletion
                </div>
            </template>

            <div class="sm:flex sm:items-start">
                <div class="text-center sm:text-left">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete {{ selectionCount }} selected records? This
                        action cannot be undone.
                    </p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button
                        type="button"
                        @click="showDeleteModal = false"
                        class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400">
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="loading"
                        @click="handleBulkDelete"
                        class="btn-3d-danger btn-sm">
                        <template v-if="loading">
                            <svg
                                class="mr-2 -ml-1 h-4 w-4 animate-spin"
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
                            Deleting...
                        </template>
                        <template v-else>Delete</template>
                    </button>
                </div>
            </template>
        </Modal>
    </section>
</template>
