<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Default from '../../Layouts/Default.vue'
import Datatable from '../../Components/Datatable.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    audits: {
        type: Object,
        required: true
    }
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.audits.current_page,
    per_page: Number(props.audits.per_page),
    total: props.audits.total
})

// Event badge styles
const eventBadgeClasses = {
    created: 'bg-green-50 text-green-700',
    updated: 'bg-yellow-50 text-yellow-700',
    deleted: 'bg-red-50 text-red-700'
}

const columns = [
    columnHelper.accessor('created_at', {
        header: 'Date',
        cell: info => new Date(info.getValue()).toLocaleString()
    }),
    columnHelper.accessor('user.name', {
        header: 'User',
        cell: info => info.getValue() || 'System'
    }),
    columnHelper.accessor('event', {
        header: 'Action',
        cell: info => h('span', {
            class: `px-2 py-1 rounded-full text-xs ${eventBadgeClasses[info.getValue().toLowerCase()] || 'bg-gray-50 text-gray-700'}`
        }, info.getValue().charAt(0).toUpperCase() + info.getValue().slice(1))
    }),
    columnHelper.accessor('auditable_type', {
        header: 'Type',
        cell: info => info.getValue().split('\\').pop()
    })
]

// Handle pagination changes
watch(pagination, newPagination => {
    loading.value = true
    router.get(
        route('admin.audits.index'),
        {
            page: newPagination.current_page,
            per_page: Number(newPagination.per_page)
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => loading.value = false
        }
    )
}, { deep: true })
</script>

<template>
    <Head title="Audits" />
    <Datatable
        title="Audit Log"
        :data="audits.data"
        :columns="columns"
        :loading="loading"
        :pagination="pagination"
        :search-fields="['user.name', 'event', 'auditable_type', 'created_at']"
        empty-message="No audit records found"
        empty-description="System activities will appear here"
        export-file-name="activity_log"
        @update:pagination="pagination = $event"
    />
</template>
