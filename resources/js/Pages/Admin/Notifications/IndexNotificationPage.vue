<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { computed, h, ref, watch } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import NotificationTypeBadge from '@js/Components/Common/NotificationTypeBadge.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Modal from '@js/Components/Notifications/Modal.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    notifications: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const EMPTY = '-'

const loading = ref(false)

const showDeleteModal = ref(false)
const deleteTarget = ref(null)

const showBulkDeleteModal = ref(false)
const bulkDeleteIds = ref([])

const selectedCount = computed(() => bulkDeleteIds.value.length)

const pagination = ref({
    current_page: props.notifications.current_page,
    per_page: Number(props.notifications.per_page),
    total: props.notifications.total,
})

watch(
    () => props.notifications,
    next => {
        if (!next) return
        pagination.value = {
            current_page: next.current_page,
            per_page: Number(next.per_page),
            total: next.total,
        }
    },
    { deep: true }
)

const pageSizeOptions = [10, 25, 50, 100, 1000, 'All']

const openDeleteModal = row => {
    deleteTarget.value = row
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    deleteTarget.value = null
}

const destroyRow = () => {
    const row = deleteTarget.value
    if (!row?.id) return

    loading.value = true
    router.delete(route('admin.notifications.destroy', row.id), {
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            loading.value = false
            closeDeleteModal()
        },
    })
}

const closeBulkDeleteModal = () => {
    showBulkDeleteModal.value = false
    bulkDeleteIds.value = []
}

const runBulkDelete = () => {
    if (!bulkDeleteIds.value.length) return

    loading.value = true
    router.post(
        route('admin.notifications.bulk-destroy'),
        { ids: bulkDeleteIds.value },
        {
            preserveScroll: true,
            preserveState: false,
            onFinish: () => {
                loading.value = false
                closeBulkDeleteModal()
            },
        }
    )
}

const handleBulkDelete = payload => {
    const selected = payload?.selectedRows ?? []
    const ids = selected.map(r => r?.id).filter(Boolean)
    if (!ids.length) return

    bulkDeleteIds.value = ids
    showBulkDeleteModal.value = true
}

const dash = v => {
    if (v === null || v === undefined) return EMPTY
    const s = String(v).trim()
    return s ? s : EMPTY
}

const columnHelper = createColumnHelper()

const btnClass = 'cursor-pointer rounded-md p-1.5 text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]'
const iconClass = 'h-3.5 w-3.5'
const svgAttrs = { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.5', 'aria-hidden': 'true' }

const columns = [
    columnHelper.accessor('title', {
        header: 'Notification',
        cell: info => {
            const row = info.row.original
            return h('div', { class: 'min-w-0' }, [
                h('p', { class: 'truncate text-sm font-medium text-[var(--color-text)]' }, dash(row.title)),
                h('p', { class: 'mt-0.5 text-xs text-[var(--color-text-muted)]' }, [
                    h('span', { class: 'capitalize' }, dash(row.scope)),
                    h('span', { class: 'mx-1' }, '·'),
                    h(NotificationTypeBadge, { type: row.type }),
                ]),
            ])
        },
    }),
    columnHelper.accessor(row => dash(row.created_by_name), {
        id: 'created_by',
        header: 'Author',
        cell: info => h('span', { class: 'text-sm text-[var(--color-text)]' }, info.getValue()),
    }),
    columnHelper.accessor(row => dash(row.created_at_diff), {
        id: 'created_at',
        header: 'Created',
        cell: info => h('span', { class: 'text-xs text-[var(--color-text-muted)]' }, info.getValue()),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        cell: info => {
            const row = info.row.original
            if (!row?.id) return null

            const editBtn = h(Link, {
                href: route('admin.notifications.edit', row.id),
                class: btnClass,
                title: 'Edit',
            }, {
                default: () => [
                    h('svg', { class: iconClass, ...svgAttrs }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10' }),
                    ]),
                ],
            })

            const deleteBtn = h('button', {
                type: 'button',
                class: btnClass + ' hover:text-red-600! dark:hover:text-red-400!',
                title: 'Delete',
                onClick: () => openDeleteModal(row),
            }, [
                h('svg', { class: iconClass, ...svgAttrs }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' }),
                ]),
            ])

            return h('div', { class: 'flex items-center justify-end gap-0.5' }, [editBtn, deleteBtn])
        },
    }),
]

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: route('dashboard') },
    { label: 'Admin Notifications' },
])

const onNavigate = payload => {
    loading.value = Boolean(payload?.loading)
}

const formatExportData = row => ({
    Created: row.created_at_diff ?? '',
    Scope: row.scope ?? '',
    Type: row.type ?? '',
    Title: row.title ?? '',
    Message: row.message ?? '',
    User: row.username ?? '',
    Email: row.user_email ?? '',
    Read: row.read_count ?? 0,
    Dismissed: row.dismissed_count ?? 0,
    Deleted: row.deleted_count ?? 0,
    Scheduled: row.scheduled_on_diff ?? '',
    'Auto Expire':
        row?.auto_expires_on_diff ??
        row?.auto_expire_on_diff ??
        row?.expires_on_diff ??
        row?.expires_at_diff ??
        row?.expires_at_human ??
        row?.expires_at ??
        '',
})
</script>

<template>
    <Head title="Admin Notifications" />

    <main class="mx-auto max-w-7xl" aria-labelledby="admin-notifications">
        <PageHeader
            title="Admin Notifications"
            description="Create and manage app notifications"
            :breadcrumbs="breadcrumbs">
            <template #actions>
                <Button :as="Link" variant="primary" size="sm" :href="route('admin.notifications.create')">
                    Create notification
                </Button>
            </template>
        </PageHeader>

        <div class="notifications-data-table card p-6">
            <Datatable
                        class="datatable-admin-notifications"
                        :data="notifications.data"
                        :columns="columns"
                        :loading="loading"
                        :pagination="pagination"
                        :filters-enabled="false"
                        :page-size-options="pageSizeOptions"
                        :default-page-size="Number(pagination.per_page) || 25"
                        empty-message="No notifications found"
                        empty-description="Notifications you create will appear here"
                        export-file-name="admin_notifications"
                        route-name="admin.notifications.index"
                        :bulk-delete-route="route('admin.notifications.bulk-destroy')"
                        :format-export-data="formatExportData"
                        @bulk-delete="handleBulkDelete"
                        @navigate="onNavigate"
                        @update:pagination="pagination = $event" />
        </div>
    </main>

    <Modal :show="showDeleteModal" size="sm" @close="closeDeleteModal">
        <template #title>Delete notification</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                Delete <span class="font-medium text-(--color-text)">{{ deleteTarget?.title || 'this notification' }}</span>? This cannot be undone.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" :disabled="loading" @click="closeDeleteModal">Cancel</Button>
                <Button variant="danger" size="sm" :disabled="loading" @click="destroyRow">
                    {{ loading ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal :show="showBulkDeleteModal" size="sm" @close="closeBulkDeleteModal">
        <template #title>Delete notifications</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                Delete <span class="font-medium text-(--color-text)">{{ selectedCount }}</span> selected notifications? This cannot be undone.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" :disabled="loading" @click="closeBulkDeleteModal">Cancel</Button>
                <Button variant="danger" size="sm" :disabled="loading" @click="runBulkDelete">
                    {{ loading ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
