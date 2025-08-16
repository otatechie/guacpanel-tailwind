<script setup>
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Datatable from '@/Components/Datatable.vue'

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

const eventBadgeClasses = {
    created: 'bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-400 border border-green-100 dark:border-green-900',
    updated: 'bg-yellow-50 dark:bg-yellow-950 text-yellow-700 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-900',
    deleted: 'bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900'
}

const columns = [
    columnHelper.accessor('created_at', {
        header: 'Date',
        cell: info => {
            const date = new Date(info.getValue())
            const formattedDate = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            })
            const formattedTime = date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            })
            const fullDateTime = `${formattedDate} @ ${formattedTime}`
            return h('span', {
                'aria-label': `Activity date: ${fullDateTime}`
            }, fullDateTime)
        },
        meta: {
            ariaLabel: 'Activity timestamp'
        }
    }),
    columnHelper.accessor(row => row.user?.name, {
        id: 'user.name',
        header: 'User',
        cell: info => h('span', {
            'aria-label': `Action performed by: ${info.getValue() || 'System'}`
        }, info.getValue() || 'System'),
        meta: {
            ariaLabel: 'User who performed the action'
        }
    }),
    columnHelper.accessor('event', {
        header: 'Action',
        cell: info => {
            const event = info.getValue()
            const formattedEvent = event.charAt(0).toUpperCase() + event.slice(1)
            return h('span', {
                class: `px-2 py-1 rounded-full text-xs font-medium ${eventBadgeClasses[event.toLowerCase()] || 'bg-gray-50 text-gray-700 border border-gray-100'}`,
                role: 'status',
                'aria-label': `Action type: ${formattedEvent}`
            }, formattedEvent)
        },
        meta: {
            ariaLabel: 'Type of action performed'
        }
    }),
    columnHelper.accessor('auditable_type', {
        header: 'Model',
        cell: info => h('span', {
            'aria-label': `Resource type: ${info.getValue().split('\\').pop()}`
        }, info.getValue().split('\\').pop()),
        meta: {
            ariaLabel: 'Resource type affected'
        }
    })
]

watch(pagination, newPagination => {
    loading.value = true
    router.get(
        route('admin.audit.index'),
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

    <main class="max-w-7xl mx-auto" aria-labelledby="audit-log">
        <div class="container-border overflow-hidden">
            <PageHeader title="Audit Log" description="View and monitor system activities" :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Settings', href: route('admin.setting.index') },
                { label: 'Audit Log' }
            ]" />

            <section class="p-6 dark:bg-gray-900">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <Datatable :data="audits.data" :columns="columns" :loading="loading" :pagination="pagination"
                        :search-fields="['user.name', 'event', 'auditable_type', 'created_at']"
                        empty-message="No audit records found" empty-description="System activities will appear here"
                        export-file-name="activity_log" @update:pagination="pagination = $event">
                        <template #loading>
                            <p class="text-center p-4 text-gray-500 dark:text-gray-400" role="status" aria-live="polite">
                                Loading audit history...
                            </p>
                        </template>
                    </Datatable>
                </div>
            </section>
        </div>
    </main>
</template>
