<script setup>
import { h, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Badge from '@js/Components/Badge.vue'
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

// One map instead of two that had to be kept in step by hand.
const EVENT_VARIANT = { created: 'success', updated: 'warning', deleted: 'danger' }
const eventVariant = e => EVENT_VARIANT[(e || '').toLowerCase()] ?? 'neutral'

const columns = [
    columnHelper.accessor(row => row.user?.name, {
        id: 'user',
        header: 'User',
        cell: info =>
            h(
                'span',
                { class: 'text-sm font-medium text-foreground' },
                info.getValue() || 'System'
            ),
    }),
    columnHelper.accessor('event', {
        header: 'Action',
        meta: { narrow: true },
        cell: info => {
            const e = info.getValue() || ''
            const label = e.charAt(0).toUpperCase() + e.slice(1)
            return h(Badge, { dot: true, variant: eventVariant(e) }, { default: () => label })
        },
    }),
    // "User" alone does not say which user. The id is what makes an audit entry
    // traceable back to the record it describes.
    columnHelper.accessor('auditable_type', {
        header: 'Resource',
        meta: { narrow: true },
        cell: info => {
            const row = info.row.original
            const type = (info.getValue() || '').split('\\').pop() || 'Unknown'
            return h('span', { class: 'text-xs text-muted-foreground' }, [
                type,
                row.auditable_id
                    ? h('span', { class: 'ml-1 font-mono opacity-70' }, `#${row.auditable_id}`)
                    : null,
            ])
        },
    }),
    // What actually moved. Field names only — the values can hold anything,
    // including secrets, so they never leave the server.
    columnHelper.accessor('changed', {
        header: 'Changed',
        cell: info => {
            const fields = info.getValue() || []
            if (!fields.length) return h('span', { class: 'text-xs text-muted-foreground' }, '-')
            return h(
                'span',
                { class: 'font-mono text-xs text-muted-foreground' },
                fields.join(', ')
            )
        },
    }),
    columnHelper.accessor('ip_address', {
        header: 'IP address',
        meta: { narrow: true },
        cell: info =>
            h('span', { class: 'font-mono text-xs text-muted-foreground' }, info.getValue() || '-'),
    }),
    columnHelper.accessor('created_at', {
        header: 'When',
        meta: { narrow: true },
        cell: info => {
            const raw = info.getValue()
            const d = raw ? new Date(raw) : null
            if (!d || isNaN(d.getTime())) return '-'
            return h(
                'span',
                {
                    class: 'text-xs tabular-nums text-muted-foreground',
                    title: d.toLocaleString(),
                },
                d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) +
                    ' ' +
                    d.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true,
                    })
            )
        },
    }),
]

watch(
    pagination,
    p => {
        loading.value = true
        router.get(
            route('admin.audit.index'),
            { page: p.current_page, per_page: Number(p.per_page) },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => (loading.value = false),
            }
        )
    },
    { deep: true }
)
</script>

<template>
    <Head title="Activity log" />

    <main class="mx-auto max-w-4xl" aria-labelledby="audit-log">
        <PageHeader
            title="Activity log"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Activity log' },
            ]" />

        <!-- No card. The table already has its own border; wrapping it in a
             second bordered box is decoration, not structure. -->
        <Datatable
            :data="audits.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            empty-message="No activity yet"
            empty-description="Admin actions appear here as they happen."
            export-file-name="activity_log"
            @update:pagination="pagination = $event" />
    </main>
</template>
