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
import { SquarePenIcon, Trash2Icon } from '@lucide/vue'

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

const btnClass =
    'cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground'
const iconClass = 'h-3.5 w-3.5'

const columns = [
    columnHelper.accessor('title', {
        header: 'Notification',
        cell: info => {
            const row = info.row.original
            return h('div', { class: 'min-w-0' }, [
                h('p', { class: 'truncate text-sm font-medium text-foreground' }, dash(row.title)),
                h('p', { class: 'mt-0.5 text-xs text-muted-foreground' }, [
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
        meta: { narrow: true },
        cell: info => h('span', { class: 'text-sm text-foreground' }, info.getValue()),
    }),
    columnHelper.accessor(row => dash(row.created_at_diff), {
        id: 'created_at',
        header: 'Created',
        meta: { narrow: true },
        cell: info => h('span', { class: 'text-xs text-muted-foreground' }, info.getValue()),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        cell: info => {
            const row = info.row.original
            if (!row?.id) return null

            // "Edit" / "Delete" alone do not say what. Screen-reader users get a
            // column of identical buttons otherwise.
            const label = dash(row.title)

            const editBtn = h(
                Link,
                {
                    href: route('admin.notifications.edit', row.id),
                    class: btnClass,
                    'aria-label': `Edit ${label}`,
                    title: `Edit ${label}`,
                },
                { default: () => [h(SquarePenIcon, { class: iconClass, 'aria-hidden': 'true' })] }
            )

            const deleteBtn = h(
                'button',
                {
                    type: 'button',
                    class: btnClass + ' hover:text-red-600! dark:hover:text-red-400!',
                    'aria-label': `Delete ${label}`,
                    title: `Delete ${label}`,
                    onClick: () => openDeleteModal(row),
                },
                [h(Trash2Icon, { class: iconClass, 'aria-hidden': 'true' })]
            )

            return h('div', { class: 'flex items-center justify-end gap-2' }, [editBtn, deleteBtn])
        },
    }),
]

// Matches how the settings index and every other subpage crumb this section.
const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: route('dashboard') },
    { label: 'System settings', href: route('admin.setting.index') },
    { label: 'Notifications' },
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
    <Head title="Notifications" />

    <main class="mx-auto max-w-4xl" aria-labelledby="admin-notifications">
        <!-- Named "Notifications" in the nav, so the page says the same. The
             description only restated the title and the button beside it. -->
        <PageHeader title="Notifications" :breadcrumbs="breadcrumbs">
            <template #actions>
                <Button
                    :as="Link"
                    variant="primary"
                    size="sm"
                    :href="route('admin.notifications.create')">
                    Create notification
                </Button>
            </template>
        </PageHeader>

        <!-- No card. The table already has its own border; wrapping it in a
             second bordered box is decoration, not structure. -->
        <div>
            <Datatable
                :data="notifications.data"
                :columns="columns"
                :loading="loading"
                :pagination="pagination"
                :page-size-options="pageSizeOptions"
                :default-page-size="Number(pagination.per_page) || 25"
                empty-message="No notifications yet"
                empty-description="Notifications you create appear here and go out to your users."
                export-file-name="admin_notifications"
                route-name="admin.notifications.index"
                :bulk-delete-route="route('admin.notifications.bulk-destroy')"
                :format-export-data="formatExportData"
                @bulk-delete="handleBulkDelete"
                @navigate="onNavigate"
                @update:pagination="pagination = $event">
                <!-- The next step, where the reader is looking, rather than only
                     in the header 700px away. -->
                <template #empty-action>
                    <Button
                        :as="Link"
                        variant="secondary"
                        size="sm"
                        :href="route('admin.notifications.create')">
                        Create notification
                    </Button>
                </template>
            </Datatable>
        </div>
    </main>

    <Modal
        :show="showDeleteModal"
        size="sm"
        description="This cannot be undone."
        @close="closeDeleteModal">
        <template #title>Delete notification</template>
        <template #default>
            <!-- Lead with the record, not a sentence wrapped around it. Prettier
                 wraps an inline <span> and Vue then renders the trailing "?"
                 with a leading space. -->
            <p class="text-foreground text-sm font-medium">
                {{ deleteTarget?.title || 'This notification' }}
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" :disabled="loading" @click="closeDeleteModal">
                    Cancel
                </Button>
                <Button variant="danger" size="sm" :disabled="loading" @click="destroyRow">
                    {{ loading ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal
        :show="showBulkDeleteModal"
        size="sm"
        description="This cannot be undone."
        @close="closeBulkDeleteModal">
        <template #title>Delete notifications</template>
        <template #default>
            <p class="text-foreground text-sm font-medium">
                {{ selectedCount }} selected
                {{ selectedCount === 1 ? 'notification' : 'notifications' }}
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button
                    variant="secondary"
                    size="sm"
                    :disabled="loading"
                    @click="closeBulkDeleteModal">
                    Cancel
                </Button>
                <Button variant="danger" size="sm" :disabled="loading" @click="runBulkDelete">
                    {{ loading ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
