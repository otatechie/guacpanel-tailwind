<script setup>
import { Head, router, Link } from '@inertiajs/vue3'
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
    created: 'bg-green-50 text-green-700 border border-green-100',
    updated: 'bg-yellow-50 text-yellow-700 border border-yellow-100',
    deleted: 'bg-red-50 text-red-700 border border-red-100'
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
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center space-x-2 text-sm">
                    <Link href="/admin" class="text-gray-500 hover:text-gray-700">Dashboard</Link>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900">Audit Log</span>
                </div>
            </div>

            <header class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Audit Log</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            View and monitor system activities
                        </p>
                    </div>
                </div>
            </header>

            <div class="p-6">
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
