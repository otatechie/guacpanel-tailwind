<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getSortedRowModel,
    getFilteredRowModel,
    getPaginationRowModel
} from '@tanstack/vue-table'
import Modal from '@/Components/Modal.vue'

const selectionColor = 'var(--selection-color)'

const styles = {
    input: 'border border-[var(--color-border-strong)] rounded-md text-sm bg-[var(--color-surface)] text-[var(--color-text)] focus:ring-2 focus:ring-opacity-50 focus:border-transparent',
    button: 'btn-primary !p-2 focus:outline-none focus:ring-2 focus:ring-opacity-50',
    tableCell: 'px-6 py-4 text-xs text-[var(--color-text)]',
    tableHeader: 'table-header',
    sortableHeader: 'cursor-pointer hover:bg-[var(--color-surface-muted)]',
    rowEven: 'bg-[var(--color-surface)]',
    rowOdd: 'bg-[var(--color-surface-muted)]',
    rowHover: 'hover:bg-[var(--color-surface-muted)] transition-colors',
    rowSelected: 'bg-[var(--selection-color-light)] dark:bg-[var(--selection-color-dark)]',
    focusRing: 'focus:outline-none focus:ring-2 focus:ring-opacity-50',
    dropdown:
        'absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-[var(--color-surface)] shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none'
}

const icons = {
    clearSearch: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`,
    export: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />`,
    firstPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />`,
    prevPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />`,
    nextPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />`,
    lastPage: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />`,
    chevronDown: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />`
}

const props = defineProps({
    data: { type: Array, required: true },
    columns: { type: Array, required: true },
    title: { type: String, default: 'Data Table' },
    enableSearch: { type: Boolean, default: true },
    enableExport: { type: Boolean, default: true },
    searchFields: { type: Array, default: () => [] },
    emptyMessage: { type: String, default: 'No data found' },
    emptyDescription: { type: String, default: 'Data will appear here' },
    exportFileName: { type: String, default: 'export' },
    pageSizeOptions: { type: Array, default: () => [10, 25, 50, 'All'] },
    defaultPageSize: { type: Number, default: 10 },
    loading: { type: Boolean, default: false },
    error: { type: String, default: '' },
    bulkDeleteRoute: {
        type: String,
        default: ''
    },
    pagination: {
        type: Object,
        default: () => ({
            current_page: 1,
            per_page: 10,
            total: 0
        })
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    formatExportData: {
        type: Function,
        default: null
    },
    routeName: {
        type: String,
        default: ''
    },
    routeParams: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:pagination', 'bulk-delete', 'navigate'])
const sorting = ref([])
const rowSelection = ref({})
const searchQuery = ref('')
const expandedRows = ref([])
const pagination = ref({
    pageIndex: 0,
    pageSize: props.defaultPageSize
})
const showDeleteModal = ref(false)

const getNavigationUrl = () => {
    if (props.routeName) {
        return route(props.routeName, props.routeParams)
    }
    return window.location.pathname
}

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

const filteredData = computed(() => {
    if (!searchQuery.value || !props.searchFields.length) return props.data

    const query = searchQuery.value.toLowerCase()
    return props.data.filter(item =>
        props.searchFields.some(field =>
            String(item[field] || '')
                .toLowerCase()
                .includes(query)
        )
    )
})

const isServerPagination = computed(() => Boolean(props.pagination?.total))

const isAllSelected = computed(() => {
    if (!isServerPagination.value) return false
    const originalPerPage = props.filters?.per_page
    return originalPerPage === 'all' || originalPerPage === 'All'
})

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

    if (pageSize === 'all' || pageSize === 'All') {
        return {
            currentPage: 1,
            pageSize: 'all',
            total,
            start: 1,
            end: total,
            pageCount: 1
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
        selectedRows: selectedRows.value.map(row => row.original)
    })
}

const currentPage = computed(() => paginationInfo.value.currentPage)
const paginationStart = computed(() => paginationInfo.value.start)
const paginationEnd = computed(() => paginationInfo.value.end)
const totalRows = computed(() => paginationInfo.value.total)
const pageCount = computed(() => paginationInfo.value.pageCount)
const isFirstPage = computed(() => currentPage.value <= 1)
const isLastPage = computed(() => currentPage.value >= pageCount.value)

const goToPage = pageNumber => {
    if (!isServerPagination.value) return
    if (pageNumber < 1 || pageNumber > pageCount.value) return
    updateServerPagination({ current_page: pageNumber })
}

const updateServerPagination = updates => {
    if (!isServerPagination.value) return

    emit('update:pagination', {
        ...props.pagination,
        ...updates
    })
}

const handlePageChange = e => {
    goToPage(Number(e.target.value))
}

const clearSearch = () => {
    searchQuery.value = ''
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

    const csvContent = [
        Object.keys(dataToExport[0]).join(','),
        ...dataToExport.map(row => Object.values(row).join(','))
    ].join('\n')

    const blob = new window.Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
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
        get pagination() {
            if (isServerPagination.value) {
                return {
                    pageSize: props.pagination.per_page,
                    pageIndex: props.pagination.current_page - 1
                }
            }
            return {
                pageIndex: pagination.value.pageIndex,
                pageSize: pagination.value.pageSize
            }
        }
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
    onPaginationChange: updaterOrValue => {
        const newPagination =
            typeof updaterOrValue === 'function' ? updaterOrValue(pagination.value) : updaterOrValue
        pagination.value = newPagination
    },
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: isServerPagination.value ? undefined : getPaginationRowModel(),
    enableRowSelection: true,
    enableMultiRowSelection: true,
    getRowId: row => row.id || row.ID || JSON.stringify(row)
})

watch(
    () => pagination.value.pageSize,
    newSize => {
        if (!isServerPagination.value) return
        updateServerPagination({
            per_page: newSize,
            current_page: 1
        })
    }
)

watch(
    () => props.data,
    () => {
        pagination.value.pageIndex = 0
    },
    { deep: true }
)

watch(
    () => props.pagination,
    (newPagination, oldPagination) => {
        if (!isServerPagination.value) return

        if (
            oldPagination &&
            (newPagination.current_page !== oldPagination.current_page ||
                newPagination.per_page !== oldPagination.per_page)
        ) {
            router.get(
                getNavigationUrl(),
                {
                    page: newPagination.current_page,
                    per_page:
                        newPagination.per_page === 'all' ? 'all' : Number(newPagination.per_page)
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onStart: () => emit('navigate', { loading: true }),
                    onFinish: () => emit('navigate', { loading: false })
                }
            )
        }
    },
    { deep: true }
)
</script>

<template>
    <section class="relative">
        <!-- Error Alert -->
        <div
            v-if="error"
            role="alert"
            class="mb-4 p-4 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 rounded-md">
            {{ error }}
        </div>

        <!-- Loading Overlay -->
        <div
            v-if="loading"
            role="status"
            class="absolute inset-0 bg-[color:rgb(255_255_255_/_.5)] dark:bg-[color:rgb(17_24_39_/_.5)] flex items-center justify-center z-10">
            <span
                class="animate-spin rounded-full h-8 w-8 border-b-2"
                :style="{ borderColor: 'var(--primary-color)' }"></span>
        </div>

        <!-- Table Controls -->
        <header
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                <div class="flex space-x-2 items-center">
                    <label class="text-sm text-[var(--color-text)]">
                        {{ table.options.meta?.showRowsSelectLabel || 'Rows per page:' }}
                    </label>
                    <select
                        class="border border-[var(--color-border-strong)] rounded-md text-sm bg-[var(--color-surface)] text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-opacity-50"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :value="
                            isServerPagination
                                ? isAllSelected
                                    ? 'all'
                                    : String(props.pagination.per_page)
                                : String(pagination.pageSize)
                        "
                        @change="
                            e => {
                                const newSize =
                                    e.target.value === 'all' ? 'all' : Number(e.target.value)
                                if (isServerPagination) {
                                    updateServerPagination({
                                        per_page: newSize,
                                        current_page: 1
                                    })
                                } else {
                                    pagination.pageSize =
                                        newSize === 'all'
                                            ? table.getFilteredRowModel().rows.length
                                            : newSize
                                    pagination.pageIndex = 0
                                }
                            }
                        ">
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
                        class="flex items-center gap-1.5 text-xs font-medium text-[var(--color-text)]">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4 text-green-600 dark:text-green-500">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ selectionCount }} selected
                    </span>
                    <button
                        v-if="bulkDeleteRoute"
                        class="inline-flex text-red-500 dark:text-red-500 items-center gap-2 cursor-pointer"
                        @click="showDeleteModal = true">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-4 h-4">
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
                class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                <div v-if="enableSearch" class="relative w-full sm:w-48">
                    <label class="sr-only" :for="'table-search'">Search table</label>
                    <input
                        :id="'table-search'"
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search"
                        :class="[styles.input, styles.focusRing, 'w-full px-4 py-2 pr-8']"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }" />
                    <button
                        v-if="searchQuery"
                        :class="[
                            styles.focusRing,
                            'absolute right-2 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] hover:text-[var(--color-text)]'
                        ]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        aria-label="Clear search"
                        @click="clearSearch">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            v-html="icons.clearSearch"></svg>
                    </button>
                </div>

                <button
                    v-if="enableExport"
                    :class="['btn-primary btn-sm inline-flex items-center gap-2', styles.focusRing]"
                    @click="exportToCSV">
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                        v-html="icons.export"></svg>
                    Export CSV
                </button>
            </nav>
        </header>

        <div class="overflow-x-auto border border-[var(--color-border)] rounded-lg">
            <div class="block md:hidden space-y-3 p-3">
                <div
                    class="flex items-center justify-between p-2 bg-[var(--color-surface-muted)] rounded-lg border border-[var(--color-border)]">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            class="form-checkbox rounded border-[var(--color-border-strong)] focus:ring-2 focus:ring-opacity-50 bg-[var(--color-surface)]"
                            :style="{
                                '--tw-ring-color': 'var(--primary-color)',
                                color: selectionColor
                            }"
                            :checked="table.getIsAllRowsSelected()"
                            :indeterminate="table.getIsSomeRowsSelected()"
                            @change="handleSelectAll" />
                        <span class="ml-2 text-xs font-medium text-[var(--color-text)]">
                            {{ table.getIsAllRowsSelected() ? 'Deselect All' : 'Select All' }}
                        </span>
                    </label>

                    <div class="text-xs font-medium text-[var(--color-text-muted)]">
                        {{ table.getFilteredSelectedRowModel().rows.length }} of
                        {{ table.getFilteredRowModel().rows.length }} selected
                    </div>
                </div>

                <div
                    v-for="(row, index) in table.getRowModel().rows"
                    :key="row.id"
                    class="bg-[var(--color-surface)] rounded-lg border border-[var(--color-border)] shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="p-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-1.5">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox rounded border-[var(--color-border-strong)] focus:ring-2 focus:ring-opacity-50 bg-[var(--color-surface)]"
                                        :style="{
                                            '--tw-ring-color': 'var(--primary-color)',
                                            color: selectionColor
                                        }"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()" />
                                    <span
                                        class="ml-1.5 text-xs font-medium text-[var(--color-text)]">
                                        Select
                                    </span>
                                </label>

                                <button
                                    class="group flex items-center gap-1 px-1.5 py-0.5 rounded bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all duration-200"
                                    @click="toggleRow(index)">
                                    <svg
                                        class="w-3 h-3 text-[var(--color-text-muted)] transition-transform duration-200 group-hover:text-[var(--color-text)]"
                                        :class="{
                                            'rotate-90': expandedRows.includes(index)
                                        }"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm4.28 10.28a.75.75 0 0 0 0-1.06l-3-3a.75.75 0 1 0-1.06 1.06l1.72 1.72H8.25a.75.75 0 0 0 0 1.5h5.69l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span
                                        class="text-xs font-medium text-[var(--color-text-muted)]">
                                        {{ expandedRows.includes(index) ? 'Less' : 'More' }}
                                    </span>
                                </button>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <slot name="mobile-actions" :row="row.original" />
                            </div>
                        </div>

                        <div class="border-b border-[var(--color-border)] mb-1.5"></div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <div
                                v-for="cell in row.getVisibleCells().slice(0, 2)"
                                :key="cell.id"
                                class="flex flex-col space-y-0">
                                <dt
                                    class="text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wide">
                                    {{ getColumnHeader(cell.column.columnDef) }}
                                </dt>
                                <dd class="text-xs font-medium text-[var(--color-text)]">
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()" />
                                </dd>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="expandedRows.includes(index)"
                        class="border-t border-[var(--color-border)] bg-[var(--color-surface-muted)]">
                        <div class="p-2 space-y-2">
                            <div class="grid grid-cols-1 gap-2">
                                <div
                                    v-for="cell in row.getVisibleCells().slice(2)"
                                    :key="cell.id"
                                    class="flex flex-col space-y-0">
                                    <dt
                                        class="text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wide">
                                        {{ getColumnHeader(cell.column.columnDef) }}
                                    </dt>
                                    <dd
                                        class="text-xs font-medium text-[var(--color-text)]">
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
                class="hidden md:table min-w-full divide-y divide-[var(--color-border)]"
                role="grid">
                <thead class="bg-[var(--color-surface-muted)]">
                    <tr>
                        <th class="w-10 px-6 py-3">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox rounded border-[var(--color-border-strong)] text-blue-600 focus:ring-2 focus:ring-opacity-50 bg-[var(--color-surface)]"
                                        :style="{
                                            '--tw-ring-color': 'var(--primary-color)',
                                            color: selectionColor
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
                                header.column.getCanSort() ? styles.sortableHeader : ''
                            ]"
                            @click="header.column.getToggleSortingHandler()?.($event)">
                            <div class="flex items-center gap-2">
                                {{ header.column.columnDef.header }}
                                <span
                                    v-if="header.column.getIsSorted()"
                                    :style="{ color: selectionColor }"
                                    class="text-[var(--color-text)]">
                                    {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody
                    class="bg-[var(--color-surface)] divide-y divide-[var(--color-border)]">
                    <tr v-if="!table.getRowModel().rows.length" :class="styles.rowHover">
                        <td :colspan="columns.length + 1" class="px-6 py-8 text-center">
                            <p class="text-[var(--color-text-muted)] text-sm">
                                {{ emptyMessage }}
                            </p>
                            <p class="mt-1 text-[var(--color-text-muted)] text-sm">
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
                                  : styles.rowOdd
                        ]">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox rounded border-[var(--color-border-strong)] focus:ring-2 focus:ring-opacity-50 bg-[var(--color-surface)]"
                                        :style="{
                                            '--tw-ring-color': 'var(--primary-color)',
                                            color: selectionColor
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
            class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 px-3 sm:px-1">
            <p class="text-xs text-[var(--color-text-muted)] text-center sm:text-left">
                Showing
                <span class="font-medium">{{ paginationStart }}</span>
                to
                <span class="font-medium">{{ paginationEnd }}</span>
                of
                <span class="font-medium">{{ totalRows }}</span>
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
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.firstPage"></svg>
                    </button>

                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isFirstPage"
                        @click="goToPage(currentPage - 1)">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.prevPage"></svg>
                    </button>

                    <div class="flex items-center gap-1">
                        <span class="hidden sm:inline text-xs text-[var(--color-text-muted)]">
                            Page
                        </span>
                        <input
                            type="number"
                            :value="currentPage"
                            :class="[styles.input, styles.focusRing, 'w-16 px-3 py-2 text-center']"
                            :style="{
                                '--tw-ring-color': 'var(--primary-color)'
                            }"
                            @change="handlePageChange" />
                        <span class="hidden sm:inline text-xs text-[var(--color-text-muted)]">
                            of {{ pageCount }}
                        </span>
                    </div>

                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isLastPage"
                        @click="goToPage(currentPage + 1)">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.nextPage"></svg>
                    </button>

                    <button
                        :class="[styles.button, styles.focusRing]"
                        :style="{ '--tw-ring-color': 'var(--primary-color)' }"
                        :disabled="isLastPage"
                        @click="goToPage(pageCount)">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.lastPage"></svg>
                    </button>
                </template>

                <template v-else>
                    <button
                        class="px-2 py-1 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="[
                            table.getCanPreviousPage()
                                ? 'text-[var(--color-text)] hover:bg-[var(--color-surface-muted)]'
                                : 'text-[var(--color-text-muted)]',
                            styles.focusRing
                        ]"
                        :style="
                            table.getCanPreviousPage()
                                ? { '--tw-ring-color': 'var(--primary-color)' }
                                : {}
                        "
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.previousPage()">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.prevPage"></svg>
                    </button>

                    <span class="text-sm text-[var(--color-text-muted)]">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of
                        {{ table.getPageCount() }}
                    </span>

                    <button
                        class="px-2 py-1 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="[
                            table.getCanNextPage()
                                ? 'text-[var(--color-text)] hover:bg-[var(--color-surface-muted)]'
                                : 'text-[var(--color-text-muted)]',
                            styles.focusRing
                        ]"
                        :style="
                            table.getCanNextPage()
                                ? { '--tw-ring-color': 'var(--primary-color)' }
                                : {}
                        "
                        :disabled="!table.getCanNextPage()"
                        @click="table.nextPage()">
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            v-html="icons.nextPage"></svg>
                    </button>
                </template>
            </nav>
        </footer>

        <Modal :show="showDeleteModal" size="sm" @close="showDeleteModal = false">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    Confirm Deletion
                </div>
            </template>

            <div class="sm:flex sm:items-start">
                <div class="text-center sm:text-left">
                    <p class="text-sm text-[var(--color-text-muted)]">
                        Are you sure you want to delete
                        {{ selectionCount }} selected records? This action cannot be undone.
                    </p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button
                        type="button"
                        class="text-sm font-medium text-[var(--color-text)] hover:text-[var(--color-text-muted)] cursor-pointer"
                        @click="showDeleteModal = false">
                        Cancel
                    </button>
                    <button
                        type="button"
                        :disabled="loading"
                        class="btn-danger btn-sm"
                        @click="handleBulkDelete">
                        <template v-if="loading">
                            <svg
                                class="animate-spin -ml-1 mr-2 h-4 w-4"
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
