<script setup>
import Button from '@/Components/Button.vue'
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
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    XMarkIcon,
    ArrowDownTrayIcon,
    TrashIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline'
import Modal from '@/Components/Notifications/Modal.vue'
import Filter from '@/Components/Filter.vue'
import { useServerPagination } from '@/composables/useServerPagination'

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
            class="mb-4 rounded-md bg-red-50 p-4 text-red-600 dark:bg-red-950 dark:text-red-400">
            {{ error }}
        </div>

        <div
            v-if="loading"
            role="status"
            class="absolute inset-0 z-10 flex items-center justify-center bg-(--color-surface)/50">
            <span
                class="h-8 w-8 animate-spin rounded-full border-b-2"
                :style="{ borderColor: 'var(--primary-color)' }"></span>
        </div>

        <header
            class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div
                class="flex w-full flex-col items-start gap-3 sm:w-auto sm:flex-row sm:items-center">
                <div class="flex items-center gap-2">
                    <label class="whitespace-nowrap text-sm text-(--color-text-muted)">Show</label>
                    <select
                        class="form-input w-auto py-1.5 pr-7 text-sm"
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
                        class="flex items-center gap-1.5 text-xs font-medium text-(--color-text)">
                        <CheckCircleIcon class="h-4 w-4 text-green-600 dark:text-green-500" />
                        {{ selectionCount }} selected
                    </span>
                    <Button variant="danger" size="xs" v-if="bulkDeleteRoute" @click="showDeleteModal = true">
                        Bulk delete
                    </Button>
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
                        class="form-input w-full pr-8 text-sm"
                        placeholder="Search all columns..." />
                    <button
                        v-if="globalFilter"
                        @click="globalFilter = ''"
                        class="absolute top-1/2 right-2 -translate-y-1/2 text-(--color-text-muted) hover:text-(--color-text)"
                        aria-label="Clear search">
                        <XMarkIcon class="h-4 w-4" />
                    </button>
                </div>

                <Button variant="secondary" size="sm" class="cursor-pointer" v-if="enableExport" @click="exportToCSV">
                    Export CSV
                </Button>
            </nav>
        </header>

        <div class="overflow-x-auto rounded-lg border border-(--card-border)">
            <div class="block space-y-3 p-3 md:hidden">
                <div
                    class="flex items-center justify-between rounded-lg border border-(--card-border) bg-(--color-surface-muted) p-2">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            class="h-4 w-4 cursor-pointer rounded-sm" style="accent-color: var(--primary-color)"
                            :checked="table.getIsAllRowsSelected()"
                            :indeterminate="table.getIsSomeRowsSelected()"
                            @change="handleSelectAll" />
                        <span class="ml-2 text-xs font-medium text-(--color-text)">
                            {{ table.getIsAllRowsSelected() ? 'Deselect all' : 'Select all' }}
                        </span>
                    </label>
                    <div class="text-xs font-medium text-(--color-text-muted)">
                        {{ table.getFilteredSelectedRowModel().rows.length }} of
                        {{ table.getFilteredRowModel().rows.length }} selected
                    </div>
                </div>

                <div
                    v-for="(row, index) in table.getRowModel().rows"
                    :key="row.id"
                    class="card shadow-sm transition-all duration-200 hover:shadow-md">
                    <div class="p-2">
                        <div class="mb-1.5 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded-sm" style="accent-color: var(--primary-color)"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()" />
                                    <span
                                        class="ml-1.5 text-xs font-medium text-(--color-text)">
                                        Select
                                    </span>
                                </label>
                                <Button variant="ghost" size="xs" class="text-xs font-medium text-(--color-text-muted)" @click="toggleRow(index)">
                                    {{ expandedRows.includes(index) ? 'Less' : 'More' }}
                                </Button>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <slot name="mobile-actions" :row="row.original" />
                            </div>
                        </div>

                        <div class="mb-1.5 border-b border-(--card-border)"></div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <div
                                v-for="(cell, cellIndex) in row.getVisibleCells().slice(0, 2)"
                                :key="cell.id"
                                class="flex flex-col space-y-0">
                                <dt
                                    class="text-xs font-medium tracking-wide text-(--color-text-muted) uppercase">
                                    {{ getColumnHeader(cell.column.columnDef) }}
                                </dt>
                                <dd class="text-xs font-medium text-(--color-text)">
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()" />
                                </dd>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="expandedRows.includes(index)"
                        class="border-t border-(--card-border) bg-(--color-surface-muted)">
                        <div class="space-y-2 p-2">
                            <div class="grid grid-cols-1 gap-2">
                                <div
                                    v-for="(cell, cellIndex) in row.getVisibleCells().slice(2)"
                                    :key="cell.id"
                                    class="flex flex-col space-y-0">
                                    <dt
                                        class="text-xs font-medium tracking-wide text-(--color-text-muted) uppercase">
                                        {{ getColumnHeader(cell.column.columnDef) }}
                                    </dt>
                                    <dd
                                        class="text-xs font-medium text-(--color-text)">
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
                class="hidden min-w-full divide-y divide-(--card-border) md:table"
                role="grid">
                <thead class="bg-(--color-surface-muted)">
                    <tr>
                        <th class="w-10 px-6 py-3">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded-sm" style="accent-color: var(--primary-color)"
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
                                'px-3 py-3 sm:px-6 text-xs font-medium uppercase tracking-wide text-(--color-text-muted) text-left',
                                header.column.getCanSort() ? 'cursor-pointer hover:bg-(--color-surface-muted)' : '',
                            ]"
                            @click="header.column.getToggleSortingHandler()?.($event)">
                            <div class="flex items-center gap-2">
                                <span class="text-(--color-text-muted)">
                                    {{ header.column.columnDef.header }}
                                </span>
                                <span
                                    v-if="header.column.getIsSorted()"
                                    :style="{ color: 'var(--primary-color)' }"
                                    class="text-(--color-text)">
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
                    class="divide-y divide-(--card-border) bg-(--color-surface)">
                    <tr v-if="!table.getRowModel().rows.length" class="hover:bg-(--color-surface-muted) transition-colors">
                        <td :colspan="columns.length + 1" class="px-6 py-8 text-center">
                            <p class="text-sm text-(--color-text-muted)">
                                {{ emptyMessage }}
                            </p>
                            <p class="mt-1 text-sm text-(--color-text-muted)">
                                {{ emptyDescription }}
                            </p>
                        </td>
                    </tr>

                    <tr
                        v-for="(row, index) in table.getRowModel().rows"
                        :key="row.id"
                        :class="[
                            'hover:bg-(--color-surface-muted) transition-colors',
                            row.getIsSelected()
                                ? 'bg-(--selection-color-light) dark:bg-(--selection-color-dark)'
                                : index % 2 === 1
                                  ? 'bg-(--color-surface-muted)'
                                  : '',
                        ]">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded-sm" style="accent-color: var(--primary-color)"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()" />
                                </label>
                            </div>
                        </td>

                        <td
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                            class="px-3 py-3 sm:px-6 text-sm text-(--color-text) text-left">
                            <FlexRender
                                :render="cell.column.columnDef.cell"
                                :props="cell.getContext()" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer
            class="mt-4 flex flex-col items-center justify-between gap-3 sm:flex-row">
            <p class="text-sm text-(--color-text-muted)">
                {{ paginationInfo.start }}–{{ paginationInfo.end }} of {{ paginationInfo.total }}
            </p>

            <nav class="flex items-center gap-2" aria-label="Pagination">
                <template v-if="isServerPagination">
                    <Button variant="ghost" size="xs" :disabled="isFirstPage" @click="goToPage(1)">
                        <ChevronDoubleLeftIcon class="h-4 w-4" />
                    </Button>

                    <Button variant="ghost" size="xs" :disabled="isFirstPage" @click="goToPage(paginationInfo.currentPage - 1)">
                        <ChevronLeftIcon class="h-4 w-4" />
                    </Button>

                    <span class="text-sm text-(--color-text-muted)">
                        {{ paginationInfo.currentPage }} / {{ paginationInfo.pageCount }}
                    </span>

                    <Button variant="ghost" size="xs" :disabled="isLastPage" @click="goToPage(paginationInfo.currentPage + 1)">
                        <ChevronRightIcon class="h-4 w-4" />
                    </Button>

                    <Button variant="ghost" size="xs" :disabled="isLastPage" @click="goToPage(paginationInfo.pageCount)">
                        <ChevronDoubleRightIcon class="h-4 w-4" />
                    </Button>
                </template>

                <template v-else>
                    <Button variant="ghost" size="xs" class="disabled:cursor-not-allowed disabled:opacity-50" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
                        <ChevronLeftIcon class="h-4 w-4" />
                    </Button>

                    <span class="text-sm text-(--color-text)">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of
                        {{ table.getPageCount() }}
                    </span>

                    <Button variant="ghost" size="xs" class="disabled:cursor-not-allowed disabled:opacity-50" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
                        <ChevronRightIcon class="h-4 w-4" />
                    </Button>
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
                    <p class="text-sm text-(--color-text-muted)">
                        Are you sure you want to delete {{ selectionCount }} selected records? This
                        action cannot be undone.
                    </p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="secondary" size="sm" @click="showDeleteModal = false">
                        Cancel
                    </Button>
                    <Button variant="danger" size="sm" :disabled="loading" @click="handleBulkDelete">
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
                    </Button>
                </div>
            </template>
        </Modal>
    </section>
</template>
