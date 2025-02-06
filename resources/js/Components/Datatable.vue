<template>
    <div class="">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="sub-heading">{{ title }}</h2>
            </div>

            <div class="flex items-center gap-2">
                <div v-if="enableSearch" class="relative">
                    <input type="text" v-model="searchQuery" placeholder="Search"
                        class="w-50 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm" />
                    <button v-if="searchQuery" @click="searchQuery = ''"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <span v-if="Object.keys(selectedRows).length"
                    class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs">
                    {{ Object.keys(selectedRows).length }} selected
                </span>

                <button v-if="enableExport" @click="exportToCSV"
                    class="flex items-center gap-1 px-3 py-2 rounded-md border border-blue-600 bg-blue-600 hover:bg-blue-700 hover:border-blue-700 transition-colors duration-200 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="text-xs text-white">Export CSV</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto container-border">
            <div class="min-w-full">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <th v-for="header in headerGroup.headers" :key="header.id" :colSpan="header.colSpan" :class="[
                                'px-3 sm:px-6 py-2 sm:py-4 text-left text-xs sm:text-sm font-semibold text-gray-900',
                                header.column.getCanSort() ? 'cursor-pointer hover:bg-gray-100 transition-colors' : ''
                            ]" @click="header.column.getToggleSortingHandler()?.($event)">
                                <div class="flex items-center gap-1 sm:gap-2">
                                    <template v-if="!header.isPlaceholder">
                                        <FlexRender :render="header.column.columnDef.header"
                                            :props="header.getContext()" />
                                        <span class="text-gray-400 transition-transform"
                                            :class="{ 'scale-125': header.column.getIsSorted() }">
                                            {{ { asc: '↑', desc: '↓' }[header.column.getIsSorted()] }}
                                        </span>
                                    </template>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-if="!table.getRowModel().rows.length">
                            <td :colspan="tableColumns.length" class="px-3 sm:px-6 py-4 sm:py-8 text-center">
                                <div class="text-gray-500 text-xs sm:text-sm">{{ emptyMessage }}</div>
                                <p class="mt-2 text-xs text-gray-400">{{ emptyDescription }}</p>
                            </td>
                        </tr>
                        <tr v-for="row in table.getRowModel().rows" :key="row.id"
                            class="hover:bg-gray-50 transition-colors even:bg-gray-50">
                            <td v-for="cell in row.getVisibleCells()" :key="cell.id"
                                class="px-3 sm:px-6 py-2 sm:py-3 text-xs sm:text-sm text-gray-700">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getSortedRowModel,
    getFilteredRowModel,
    createColumnHelper,
} from '@tanstack/vue-table'
import { h } from 'vue'

const props = defineProps({
    data: {
        type: Array,
        required: true,
        default: () => []
    },
    columns: {
        type: Array,
        required: true
    },
    title: {
        type: String,
        default: 'Data Table'
    },
    enableSearch: {
        type: Boolean,
        default: true
    },
    enableExport: {
        type: Boolean,
        default: true
    },
    enableSelection: {
        type: Boolean,
        default: true
    },
    searchFields: {
        type: Array,
        default: () => []
    },
    emptyMessage: {
        type: String,
        default: 'No data found'
    },
    emptyDescription: {
        type: String,
        default: 'Data will appear here'
    },
    exportFileName: {
        type: String,
        default: 'export'
    }
})

const sorting = ref([])
const selectedRows = ref({})
const searchQuery = ref('')
const columnHelper = createColumnHelper()

const selectionColumn = columnHelper.display({
    id: 'select',
    header: ({ table }) => h('div', { class: 'px-1' }, [
        h('input', {
            type: 'checkbox',
            checked: table.getIsAllRowsSelected(),
            indeterminate: table.getIsSomeRowsSelected() ? true : undefined,
            onChange: table.getToggleAllRowsSelectedHandler(),
            class: 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500'
        })
    ]),
    cell: ({ row }) => h('div', { class: 'px-1' }, [
        h('input', {
            type: 'checkbox',
            checked: row.getIsSelected(),
            disabled: !row.getCanSelect(),
            onChange: row.getToggleSelectedHandler(),
            class: 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500'
        })
    ]),
})

const tableColumns = computed(() => {
    if (props.enableSelection) {
        return [selectionColumn, ...props.columns]
    }
    return props.columns
})

const filteredData = computed(() => {
    if (!searchQuery.value || !props.searchFields.length) return props.data

    const query = searchQuery.value.toLowerCase()
    return props.data.filter(item => {
        return props.searchFields.some(field => {
            const value = item[field]
            if (!value) return false
            return String(value).toLowerCase().includes(query)
        })
    })
})

const table = useVueTable({
    get data() {
        return filteredData.value
    },
    columns: tableColumns.value,
    state: {
        get sorting() {
            return sorting.value
        },
        get rowSelection() {
            return selectedRows.value
        }
    },
    enableRowSelection: props.enableSelection,
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
    getCoreRowModel: getCoreRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
})

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
</script>
