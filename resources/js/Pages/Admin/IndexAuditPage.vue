<script setup>
import { Head, router, Link } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Default from '../../Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
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
    created: 'bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-400 border border-green-100 dark:border-green-900',
    updated: 'bg-yellow-50 dark:bg-yellow-950 text-yellow-700 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-900',
    deleted: 'bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900'
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
            class: `px-2 py-1 rounded-full text-xs font-medium ${eventBadgeClasses[info.getValue().toLowerCase()] || 'bg-gray-50 text-gray-700 border border-gray-100'}`
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
        route('admin.audit'),
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
    <Head title="Audit Log" />

    <main class="max-w-5xl mx-auto">
        <section class="container-border overflow-hidden">
            <PageHeader
                title="Audit Log"
                description="View and monitor system activities"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Audit Log' }
                ]"
            />

            <div class="p-6 dark:bg-gray-900">
                <Datatable
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
            </div>
        </section>
    </main>
</template>
