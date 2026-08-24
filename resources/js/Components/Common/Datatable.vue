<script setup>
import Button from '@/Components/Button.vue'
import { ref, computed, watch, onMounted, useSlots } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getSortedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
} from '@tanstack/vue-table'
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronsLeftIcon,
    ChevronsRightIcon,
    CircleCheckIcon,
    DownloadIcon,
    Trash2Icon,
    XIcon,
} from '@lucide/vue'
import Modal from '@/Components/Notifications/Modal.vue'
// shadcn's Table is markup-only: it contributes semantics and data-slot hooks,
// not styling. Its p-2 / whitespace-nowrap / border-b defaults are overridden
// below via cn(), which is why the rendered result is unchanged.
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table'
import { useServerPagination } from '@/composables/useServerPagination'

const props = defineProps({
    data: {
        type: Array,
        required: true,
        default: () => [],
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
    /* Whole-row activation. The row becomes a real keyboard target rather than
       a div with a click handler, so Enter/Space reach it too. */
    rowClickable: {
        type: Boolean,
        default: false,
    },
    /** row original -> accessible name for the row, e.g. `Edit ${user.name}` */
    rowLabel: {
        type: Function,
        default: null,
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

const emit = defineEmits(['update:pagination', 'bulk-delete', 'row-click'])
const slots = useSlots()
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
    // Getter, not a snapshot — props.filters is a new object after every visit.
    filters: () => props.filters,
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

const activateRow = (event, row) => {
    if (!props.rowClickable) return
    // Anything interactive inside the row owns its own click.
    if (event.target.closest('button, a, input, label, [role="switch"]')) return
    // Dragging across an email to copy it is a selection, not a click.
    if (window.getSelection()?.toString()) return

    emit('row-click', row.original)
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

const hasRows = computed(() => table.getRowModel().rows.length > 0)

// An empty table is empty for one of two reasons, and the advice differs:
// "nothing here yet" wants the next step, "nothing matched" wants the filter
// cleared. Showing the first message to someone mid-search is just wrong.
const isFiltered = computed(() => Boolean(globalFilter.value))

// An auto-layout table hands every column an equal share of the leftover width,
// so "2 days ago" ends up centred in a 169px column, 100px from the label it
// belongs to. Columns marked narrow shrink to their content instead and the
// descriptive column absorbs the slack. Opt in per column with
// `meta: { narrow: true }`; the actions column is always narrow.
const isNarrow = column => column.columnDef.meta?.narrow === true || column.id === 'actions'

// Selection only earns its column when something can act on a selection.
// Without a bulk action the checkboxes tick and nothing can ever happen, which
// is a control that lies about what it does.
const selectable = computed(() => Boolean(props.bulkDeleteRoute || slots['bulk-actions']))

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
    getPaginationRowModel: isServerPagination.value ? undefined : getPaginationRowModel(),
    /* In server mode the rows already are the filtered, sorted result. Without
       these, tanstack filtered them a second time against the visible cell text,
       which dropped rows the server matched on a related column (a user found by
       role name has that name nowhere in the row). */
    manualFiltering: isServerPagination.value,
    manualSorting: isServerPagination.value,
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
            class="bg-card/50 absolute inset-0 z-10 flex items-center justify-center">
            <span
                class="h-8 w-8 animate-spin rounded-full border-b-2"
                :style="{ borderColor: 'var(--primary)' }"></span>
        </div>

        <header
            class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div
                class="flex w-full flex-col items-start gap-3 sm:w-auto sm:flex-row sm:items-center">
                <div class="flex items-center gap-2">
                    <label class="text-muted-foreground text-sm whitespace-nowrap">Show</label>
                    <select
                        class="form-input w-auto pr-7 text-sm"
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
                        class="text-foreground flex items-center gap-1.5 text-xs font-medium">
                        <CircleCheckIcon class="h-4 w-4 text-green-600 dark:text-green-500" />
                        {{ selectionCount }} selected
                    </span>
                    <Button
                        variant="danger"
                        size="xs"
                        v-if="bulkDeleteRoute"
                        @click="showDeleteModal = true">
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
                        class="text-muted-foreground hover:text-foreground absolute top-1/2 right-2 -translate-y-1/2"
                        aria-label="Clear search">
                        <XIcon class="h-4 w-4" />
                    </button>
                </div>

                <Button
                    variant="secondary"
                    size="sm"
                    v-if="enableExport"
                    :disabled="!hasRows"
                    @click="exportToCSV">
                    Export CSV
                </Button>
            </nav>
        </header>

        <!-- No frame. The header rule and row dividers already bound the table;
             a box around them is the last piece of decoration left. -->
        <div>
            <div class="block space-y-3 md:hidden">
                <div
                    v-if="hasRows && selectable"
                    class="border-border bg-muted flex items-center justify-between rounded-lg border p-2">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            class="h-4 w-4 cursor-pointer rounded-sm"
                            style="accent-color: var(--primary)"
                            :checked="table.getIsAllRowsSelected()"
                            :indeterminate="table.getIsSomeRowsSelected()"
                            @change="handleSelectAll" />
                        <span class="text-foreground ml-2 text-xs font-medium">
                            {{ table.getIsAllRowsSelected() ? 'Deselect all' : 'Select all' }}
                        </span>
                    </label>
                    <div class="text-muted-foreground text-xs font-medium">
                        {{ table.getFilteredSelectedRowModel().rows.length }} of
                        {{ table.getFilteredRowModel().rows.length }} selected
                    </div>
                </div>

                <div
                    v-for="(row, index) in table.getRowModel().rows"
                    :key="row.id"
                    class="border-border rounded-lg border">
                    <div class="p-2">
                        <div class="mb-1.5 flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <label v-if="selectable" class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 cursor-pointer rounded-sm"
                                        style="accent-color: var(--primary)"
                                        :checked="row.getIsSelected()"
                                        @change="row.toggleSelected()" />
                                    <span class="text-foreground ml-1.5 text-xs font-medium">
                                        Select
                                    </span>
                                </label>
                                <Button
                                    variant="ghost"
                                    size="xs"
                                    class="text-muted-foreground text-xs font-medium"
                                    @click="toggleRow(index)">
                                    {{ expandedRows.includes(index) ? 'Less' : 'More' }}
                                </Button>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <slot name="mobile-actions" :row="row.original" />
                            </div>
                        </div>

                        <div class="border-border mb-1.5 border-b"></div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <div
                                v-for="(cell, cellIndex) in row.getVisibleCells().slice(0, 2)"
                                :key="cell.id"
                                class="flex flex-col space-y-0">
                                <dt
                                    class="text-muted-foreground text-xs font-medium tracking-wide uppercase">
                                    {{ getColumnHeader(cell.column.columnDef) }}
                                </dt>
                                <dd class="text-foreground text-xs font-medium">
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()" />
                                </dd>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="expandedRows.includes(index)"
                        class="border-border bg-muted border-t">
                        <div class="space-y-2 p-2">
                            <div class="grid grid-cols-1 gap-2">
                                <div
                                    v-for="(cell, cellIndex) in row.getVisibleCells().slice(2)"
                                    :key="cell.id"
                                    class="flex flex-col space-y-0">
                                    <dt
                                        class="text-muted-foreground text-xs font-medium tracking-wide uppercase">
                                        {{ getColumnHeader(cell.column.columnDef) }}
                                    </dt>
                                    <dd class="text-foreground text-xs font-medium">
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

            <!-- The edge cells used to drop their padding so the table sat flush
                 with the page. That put the row-hover fill flush against the
                 first glyph, which reads as a clipped background once the whole
                 row is a target. Keeping the padding and pulling the table out by
                 the same 8px leaves the text where it was and lets the fill
                 bleed past it. -->
            <div class="-mx-2 hidden md:block">
                <Table class="min-w-full" role="grid">
                    <TableHeader>
                        <TableRow>
                            <TableHead v-if="selectable" class="w-10">
                                <!-- Nothing to select when there are no rows. -->
                                <div v-if="hasRows" class="flex items-center">
                                    <label class="inline-flex items-center">
                                        <span class="sr-only">Select all rows</span>
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 cursor-pointer rounded-sm"
                                            style="accent-color: var(--primary)"
                                            :checked="table.getIsAllRowsSelected()"
                                            :indeterminate="table.getIsSomeRowsSelected()"
                                            @change="handleSelectAll" />
                                    </label>
                                </div>
                            </TableHead>

                            <TableHead
                                v-for="header in table.getHeaderGroups()[0].headers"
                                :key="header.id"
                                :class="[
                                    header.column.getCanSort()
                                        ? 'hover:bg-muted cursor-pointer'
                                        : '',
                                    isNarrow(header.column)
                                        ? 'w-px whitespace-nowrap'
                                        : 'whitespace-normal',
                                ]"
                                @click="header.column.getToggleSortingHandler()?.($event)">
                                <div class="flex items-center gap-2">
                                    <span>
                                        {{ header.column.columnDef.header }}
                                    </span>
                                    <span
                                        v-if="header.column.getIsSorted()"
                                        :style="{ color: 'var(--primary)' }"
                                        class="text-foreground">
                                        {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                    </span>
                                </div>
                            </TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <!-- No hover state: this row is not a record, and highlighting
                         it suggests there is something here to click. -->
                        <TableRow v-if="!hasRows" class="hover:bg-transparent">
                            <TableCell
                                :colspan="columns.length + (selectable ? 1 : 0)"
                                class="px-6 py-8 text-center whitespace-normal">
                                <template v-if="isFiltered">
                                    <p class="text-foreground text-sm">
                                        No results for "{{ globalFilter }}"
                                    </p>
                                    <button
                                        type="button"
                                        class="text-muted-foreground hover:text-foreground mt-1 cursor-pointer text-sm underline underline-offset-2"
                                        @click="globalFilter = ''">
                                        Clear search
                                    </button>
                                </template>
                                <template v-else>
                                    <p class="text-muted-foreground text-sm">{{ emptyMessage }}</p>
                                    <p class="text-muted-foreground mt-1 text-sm">
                                        {{ emptyDescription }}
                                    </p>
                                    <div v-if="$slots['empty-action']" class="mt-3">
                                        <slot name="empty-action" />
                                    </div>
                                </template>
                            </TableCell>
                        </TableRow>

                        <!-- No zebra striping: the dividers already separate rows, and
                         a third cue competes with the selected-row highlight, which
                         is the one that has to stand out. -->
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            :class="[
                                row.getIsSelected()
                                    ? 'bg-(--selection-color-light) dark:bg-(--selection-color-dark)'
                                    : '',
                                rowClickable ? 'cursor-pointer' : '',
                            ]"
                            :role="rowClickable ? 'button' : undefined"
                            :tabindex="rowClickable ? 0 : undefined"
                            :aria-label="
                                rowClickable && rowLabel ? rowLabel(row.original) : undefined
                            "
                            @click="activateRow($event, row)"
                            @keydown.enter.prevent="rowClickable && emit('row-click', row.original)"
                            @keydown.space.prevent="
                                rowClickable && emit('row-click', row.original)
                            ">
                            <TableCell v-if="selectable">
                                <div class="flex items-center">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 cursor-pointer rounded-sm"
                                            style="accent-color: var(--primary)"
                                            :checked="row.getIsSelected()"
                                            @change="row.toggleSelected()" />
                                    </label>
                                </div>
                            </TableCell>

                            <TableCell
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                :class="[
                                    isNarrow(cell.column)
                                        ? 'w-px whitespace-nowrap'
                                        : 'whitespace-normal',
                                ]">
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>

        <!-- Zero rows produced "1–0 of 0" and "1 / 0" next to five dead buttons.
             A pager for no pages is not information. Keyed on visible rows, not
             total: server pagination otherwise reports "1–10 of 10" underneath
             a table that is showing "No results". The empty state carries the
             way back out. -->
        <footer
            v-if="hasRows"
            class="mt-4 flex flex-col items-center justify-between gap-3 sm:flex-row">
            <p class="text-muted-foreground text-sm">
                {{ paginationInfo.start }}–{{ paginationInfo.end }} of {{ paginationInfo.total }}
            </p>

            <nav class="flex items-center gap-2" aria-label="Pagination">
                <template v-if="isServerPagination">
                    <Button variant="ghost" size="xs" :disabled="isFirstPage" @click="goToPage(1)">
                        <ChevronsLeftIcon class="h-4 w-4" />
                    </Button>

                    <Button
                        variant="ghost"
                        size="xs"
                        :disabled="isFirstPage"
                        @click="goToPage(paginationInfo.currentPage - 1)">
                        <ChevronLeftIcon class="h-4 w-4" />
                    </Button>

                    <span class="text-muted-foreground text-sm">
                        Page {{ paginationInfo.currentPage }} of {{ paginationInfo.pageCount }}
                    </span>

                    <Button
                        variant="ghost"
                        size="xs"
                        :disabled="isLastPage"
                        @click="goToPage(paginationInfo.currentPage + 1)">
                        <ChevronRightIcon class="h-4 w-4" />
                    </Button>

                    <Button
                        variant="ghost"
                        size="xs"
                        :disabled="isLastPage"
                        @click="goToPage(paginationInfo.pageCount)">
                        <ChevronsRightIcon class="h-4 w-4" />
                    </Button>
                </template>

                <template v-else>
                    <Button
                        variant="ghost"
                        size="xs"
                        class="disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!table.getCanPreviousPage()"
                        @click="table.previousPage()">
                        <ChevronLeftIcon class="h-4 w-4" />
                    </Button>

                    <span class="text-foreground text-sm">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of
                        {{ table.getPageCount() }}
                    </span>

                    <Button
                        variant="ghost"
                        size="xs"
                        class="disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!table.getCanNextPage()"
                        @click="table.nextPage()">
                        <ChevronRightIcon class="h-4 w-4" />
                    </Button>
                </template>
            </nav>
        </footer>

        <Modal
            :show="showDeleteModal"
            size="sm"
            :description="`${selectionCount} selected ${selectionCount === 1 ? 'row' : 'rows'} will be deleted. This cannot be undone.`"
            @close="showDeleteModal = false">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    Confirm Deletion
                </div>
            </template>

            <div class="sm:flex sm:items-start">
                <div class="text-center sm:text-left">
                    <p class="text-muted-foreground text-sm">
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
                    <Button
                        variant="danger"
                        size="sm"
                        :disabled="loading"
                        @click="handleBulkDelete">
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
