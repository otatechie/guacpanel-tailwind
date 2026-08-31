<script setup>
import Button from '@/Components/Button.vue'
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { computed, h, ref, watch } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import NotificationTypeBadge from '@js/Components/Common/NotificationTypeBadge.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import RowActions from '@js/Components/Common/RowActions.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import NotificationSheet from '@js/Pages/Admin/Notifications/NotificationSheet.vue'
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
    users: {
        type: Array,
        default: () => [],
    },
})

const EMPTY = '-'

const loading = ref(false)

const showSheet = ref(false)
const editTarget = ref(null)

const openCreate = () => {
    editTarget.value = null
    showSheet.value = true
}

const openEdit = row => {
    editTarget.value = row
    showSheet.value = true
}

const showDeleteModal = ref(false)
const deleteTarget = ref(null)

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

const dash = v => {
    if (v === null || v === undefined) return EMPTY
    const s = String(v).trim()
    return s ? s : EMPTY
}

const columnHelper = createColumnHelper()

const muted = 'text-xs text-muted-foreground'

/* "Delete" alone does not say what. Without this every row hands a screen reader
   the same trigger. */
const rowLabel = row => `${dash(row.title)}, ${dash(row.scope)}`

const columns = [
    /* One fact per column, as the reader's list does. Scope and type were stacked
       under the title to fill the gap before them, but the gap is density, not
       structure, and these are what an admin scans to pick a row. */
    columnHelper.accessor('title', {
        header: 'Notification',
        cell: info =>
            h(
                'p',
                { class: 'truncate text-sm font-medium text-foreground' },
                dash(info.getValue())
            ),
    }),
    columnHelper.accessor('scope', {
        header: 'Scope',
        meta: { narrow: true },
        cell: info => h('span', { class: `${muted} capitalize` }, dash(info.getValue())),
    }),
    columnHelper.accessor('type', {
        header: 'Type',
        meta: { narrow: true },
        cell: info => h(NotificationTypeBadge, { type: info.row.original.type }),
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
        cell: info =>
            h(
                'span',
                {
                    class: `tabular-nums whitespace-nowrap ${muted}`,
                    title: info.row.original.created_at_exact,
                },
                info.getValue()
            ),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        /* One trigger with words behind it. Two glyphs side by side put an
           irreversible Delete a few pixels from Edit, told apart only by a hover
           tooltip, which is not a label for something that cannot be undone. */
        cell: info => {
            const row = info.row.original
            if (!row?.id) return null

            return h(RowActions, {
                label: `Actions for ${rowLabel(row)}`,
                actions: [
                    {
                        label: 'Edit',
                        icon: SquarePenIcon,
                        onSelect: () => openEdit(row),
                    },
                    {
                        label: 'Delete',
                        icon: Trash2Icon,
                        variant: 'destructive',
                        onSelect: () => openDeleteModal(row),
                    },
                ],
            })
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
                <Button variant="primary" size="sm" @click="openCreate">Create notification</Button>
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
                :row-label="rowLabel"
                empty-message="No notifications yet"
                empty-description="Notifications you create appear here and go out to your users."
                export-file-name="admin_notifications"
                route-name="admin.notifications.index"
                :format-export-data="formatExportData"
                @navigate="onNavigate"
                @update:pagination="pagination = $event">
                <!-- The next step, where the reader is looking, rather than only
                     in the header 700px away. -->
                <template #empty-action>
                    <Button variant="secondary" size="sm" @click="openCreate">
                        Create notification
                    </Button>
                </template>
            </Datatable>
        </div>
    </main>

    <NotificationSheet
        :show="showSheet"
        :notification="editTarget"
        :users="users"
        @close="showSheet = false" />

    <Modal :show="showDeleteModal" size="sm" @close="closeDeleteModal">
        <template #title>Delete notification</template>
        <template #default>
            <!-- Lead with the record, not a sentence wrapped around it. Prettier
                 wraps an inline <span> and Vue then renders the trailing "?"
                 with a leading space. -->
            <p class="text-foreground text-sm font-medium">
                {{ deleteTarget?.title || 'This notification' }}
            </p>
            <!-- The consequence moved out of the header subtitle and into the
                 body, where every other delete modal in the app puts it -- and
                 said specifically, since an admin delete takes it from everyone. -->
            <p class="text-muted-foreground mt-2 text-sm">
                Withdraws it from everyone it was sent to. This cannot be undone.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" :disabled="loading" @click="closeDeleteModal">
                    Cancel
                </Button>
                <Button variant="danger" size="sm" :disabled="loading" @click="destroyRow">
                    {{ loading ? 'Deleting...' : 'Delete notification' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
