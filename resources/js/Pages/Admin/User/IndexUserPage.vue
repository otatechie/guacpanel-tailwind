<script setup>
import { Head, router } from '@inertiajs/vue3'
import DataTable from '@/Components/Datatable.vue'
import Default from '@/Layouts/Default.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref } from 'vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    users: {
        type: Array,
        required: true,
        default: () => []
    }
})

const columnHelper = createColumnHelper()

const showEditModal = ref(false)
const editingUser = ref(null)

const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
    }),
    columnHelper.accessor('email', {
        header: 'Email',
    }),
    columnHelper.accessor('created_at', {
        header: 'Created At',
        cell: info => new Date(info.getValue()).toLocaleString()
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => {
            const status = info.getValue()
            const badgeClass = {
                active: 'bg-green-50 text-green-700',
                inactive: 'bg-red-50 text-red-700',
                pending: 'bg-yellow-50 text-yellow-700'
            }[status?.toLowerCase()] || 'bg-gray-50 text-gray-700'

            return h('span', {
                class: `px-2 py-1 rounded-full text-xs ${badgeClass}`
            }, status?.charAt(0).toUpperCase() + status?.slice(1) || 'N/A')
        }
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        cell: (info) => h('div', { class: 'flex items-center gap-2' }, [
            h('button', {
                class: 'p-1 text-gray-600 hover:text-blue-600 cursor-pointer',
                onClick: () => handleEdit(info.row.original),
                title: 'Edit'
            }, [
                h('svg', {
                    class: 'w-5 h-5',
                    fill: 'none',
                    stroke: 'currentColor',
                    viewBox: '0 0 24 24'
                }, [
                    h('path', {
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round',
                        'stroke-width': '2',
                        d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
                    })
                ])
            ]),
            h('button', {
                class: 'p-1 text-gray-600 hover:text-red-600 cursor-pointer',
                onClick: () => handleDelete(info.row.original),
                title: 'Delete'
            }, [
                h('svg', {
                    class: 'w-5 h-5',
                    fill: 'none',
                    stroke: 'currentColor',
                    viewBox: '0 0 24 24'
                }, [
                    h('path', {
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round',
                        'stroke-width': '2',
                        d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
                    })
                ])
            ])
        ])
    })
]

const handleEdit = (user) => {
    router.visit(route('admin.user.edit', { id: user.id }))
}

const handleDelete = (user) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.user.destroy', { id: user.id }))
    }
}
</script>

<template>
    <Head title="Users" />
    <DataTable :data="users" :columns="columns" title="Users" :search-fields="['name', 'email', 'created_at']"
        export-file-name="users" />
</template>
