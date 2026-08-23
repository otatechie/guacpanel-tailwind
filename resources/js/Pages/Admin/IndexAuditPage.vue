<script setup>
import { h, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    audits: { type: Object, required: true },
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.audits.current_page,
    per_page: Number(props.audits.per_page),
    total: props.audits.total,
})

const eventColor = e => {
    const k = (e || '').toLowerCase()
    if (k === 'created') return 'text-green-600 dark:text-green-400'
    if (k === 'updated') return 'text-amber-600 dark:text-amber-400'
    if (k === 'deleted') return 'text-red-600 dark:text-red-400'
    return 'text-muted-foreground'
}

const eventDot = e => {
    const k = (e || '').toLowerCase()
    if (k === 'created') return 'bg-green-500'
    if (k === 'updated') return 'bg-amber-500'
    if (k === 'deleted') return 'bg-red-500'
    return 'bg-border'
}

const columns = [
    columnHelper.accessor(row => row.user?.name, {
        id: 'user',
        header: 'User',
        cell: info => h('span', { class: 'text-sm font-medium text-foreground' }, info.getValue() || 'System'),
    }),
    columnHelper.accessor('event', {
        header: 'Action',
        cell: info => {
            const e = info.getValue() || ''
            const label = e.charAt(0).toUpperCase() + e.slice(1)
            return h('span', { class: `flex items-center gap-1.5 text-xs ${eventColor(e)}` }, [
                h('span', { class: `h-1.5 w-1.5 rounded-full ${eventDot(e)}` }),
                label,
            ])
        },
    }),
    columnHelper.accessor('auditable_type', {
        header: 'Resource',
        cell: info => {
            const full = info.getValue() || ''
            return h('span', { class: 'text-xs text-muted-foreground' }, full.split('\\').pop() || 'Unknown')
        },
    }),
    columnHelper.accessor('created_at', {
        header: 'When',
        cell: info => {
            const raw = info.getValue()
            const d = raw ? new Date(raw) : null
            if (!d || isNaN(d.getTime())) return '-'
            return h('span', { class: 'text-xs tabular-nums text-muted-foreground' },
                d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' ' +
                d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })
            )
        },
    }),
]

watch(pagination, p => {
    loading.value = true
    router.get(route('admin.audit.index'), { page: p.current_page, per_page: Number(p.per_page) }, {
        preserveState: true, preserveScroll: true, onFinish: () => (loading.value = false),
    })
}, { deep: true })
</script>

<template>
    <Head title="Activity log" />

    <main class="mx-auto max-w-7xl" aria-labelledby="audit-log">
        <PageHeader
            title="Activity log"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Activity log' },
            ]" />

        <div class="card p-6">
            <Datatable
                :data="audits.data"
                :columns="columns"
                :loading="loading"
                :pagination="pagination"
                empty-message="No activity"
                empty-description="Actions appear as users interact with the system"
                export-file-name="activity_log"
                @update:pagination="pagination = $event" />
        </div>
    </main>
</template>
