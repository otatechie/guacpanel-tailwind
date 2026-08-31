<script setup>
import Button from '@/Components/Button.vue'
import Badge from '@/Components/Badge.vue'
import { computed, h, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import apiFetch from '@js/utils/apiFetch'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import RowActions from '@js/Components/Common/RowActions.vue'
import { useToast } from '@js/composables/useToast'
import { usePermissions } from '@js/composables/usePermissions'
/* The circled family, matching the health page and Alert: a filled circle is a
   state that has been reached, an empty one a state that has not. */
import { CircleCheckIcon, CircleIcon, CircleXIcon, RotateCcwIcon, Trash2Icon } from '@lucide/vue'

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

const toast = useToast()
const loading = ref(false)

/* The row menu only offers what this reader may actually do.
   Every action was listed for everyone, and a viewer without edit rights got a
   403 for each one -- invisibly, because the old page patched the row on screen
   optimistically and quietly put it back when the request came home refused. */
const { hasPermission } = usePermissions()
const canEdit = hasPermission(['edit-notifications', 'manage-notifications'])
const canDelete = hasPermission(['delete-notifications', 'manage-notifications'])

const rows = computed(() => props.notifications?.data ?? [])
const meta = computed(() => props.notifications?.meta ?? {})

const pagination = ref({
    current_page: meta.value.current_page ?? 1,
    per_page: Number(meta.value.per_page ?? 25),
    total: meta.value.total ?? 0,
})

watch(
    () => props.notifications,
    () => {
        pagination.value = {
            current_page: meta.value.current_page ?? 1,
            per_page: Number(meta.value.per_page ?? 25),
            total: meta.value.total ?? 0,
        }
    },
    { deep: true }
)

/* 1000 and "All" are gone from the options: both rendered every matching row
   into the DOM at once. */
const pageSizeOptions = [10, 25, 50, 100]

/* One control, three states.
   A Read select and a Dismissed select together offered nine combinations, most
   of them meaningless. These are the three a reader actually navigates by. */
const STATES = [
    { value: 'unread', label: 'Unread' },
    { value: 'all', label: 'All' },
    { value: 'dismissed', label: 'Dismissed' },
]

const activeState = computed(() => props.filters.state ?? 'all')

const visit = params => {
    router.get(
        route('notifications.index'),
        { ...props.filters, ...params, page: 1 },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

const setState = value => visit({ state: value })

const notifyTopnavRefresh = () => {
    if (typeof window === 'undefined') return
    window.dispatchEvent(new CustomEvent('app-notifications:refresh'))
}

/* Inertia reloads rather than patching rows in place. The page used to mutate
   its own copy optimistically and reconcile over three Reverb channels -- ~500
   lines of it -- for an archive you visit occasionally. The bell is the live
   surface and keeps its own subscription. */
const refresh = () => {
    notifyTopnavRefresh()
    router.reload({ only: ['notifications'] })
}

/* POSTs carry an empty JSON object rather than no body at all.
   apiFetch stamps `Content-Type: application/json` on every non-GET, and a
   request that declares JSON and then sends nothing is not something every
   layer in front of Laravel agrees how to read. The tests never caught it:
   VerifyCsrfToken returns early under `runningUnitTests()`, so the whole
   header-and-body path a browser takes is the one path they do not exercise. */
const send = async (url, method = 'POST') => {
    loading.value = true

    try {
        const res = await apiFetch(url, {
            method,
            ...(method === 'POST' ? { body: JSON.stringify({}) } : {}),
        })

        if (!res.ok) {
            // The status, because "it did not work" is not a report anyone can
            // act on -- 403 is a refusal, 419 an expired session, 5xx a bug.
            if (res.status === 403) {
                toast.error('You do not have permission to do that.')
            } else if (res.status === 419) {
                toast.error('Your session expired. Reload the page and try again.')
            } else {
                toast.error(`That did not go through (${res.status}). Try again.`)
            }

            return false
        }

        return true
    } finally {
        loading.value = false
    }
}

const setRead = async (row, isRead) => {
    if (await send(`/notifications/${row.id}/${isRead ? 'read' : 'unread'}`)) refresh()
}

const setDismissed = async (row, isDismissed) => {
    if (await send(`/notifications/${row.id}/${isDismissed ? 'dismiss' : 'undismiss'}`)) refresh()
}

const showDeleteModal = ref(false)
const deleteTarget = ref(null)

const openDeleteModal = row => {
    deleteTarget.value = row
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    deleteTarget.value = null
}

const destroyRow = async () => {
    const row = deleteTarget.value
    if (!row?.id) return

    if (await send(`/notifications/${row.id}`, 'DELETE')) {
        closeDeleteModal()
        refresh()
    }
}

/* Row labels only. The list used to double as the source filter's options,
   which needed an "All sources" entry no row ever has. The server still honours
   ?scope=, so a deep link into one source keeps working. */
const SCOPE_LABELS = { user: 'Just me', system: 'System', release: 'Releases' }
const scopeLabel = scope => SCOPE_LABELS[scope] ?? 'Notification'

const TYPE_VARIANTS = { error: 'danger', warning: 'warning', success: 'success', info: 'info' }
const typeVariant = type => TYPE_VARIANTS[type] ?? 'info'
const typeLabel = type => (type ? type[0].toUpperCase() + type.slice(1) : 'Info')

/* "Delete" alone does not say what. Without this every row hands a screen
   reader the same three buttons. */
const rowLabel = row => `${row.title || 'Notification'}, ${scopeLabel(row.scope)}`

const columnHelper = createColumnHelper()

const muted = 'text-xs text-muted-foreground'

/* One fact per column, matching the sessions table.
   These were briefly folded into the title cell to close the empty gap that
   sat before them -- but the gap was density, not structure: two columns in a
   full-width table leave a void whichever way the facts are arranged. Source,
   type and date are what a reader scans to decide which row to act on, so they
   keep their own columns and their own headers. */
const columns = [
    columnHelper.accessor('title', {
        header: 'Notification',
        cell: info => {
            const row = info.row.original

            return h('div', { class: 'min-w-0' }, [
                h('div', { class: 'flex items-center gap-2' }, [
                    h(
                        'span',
                        { class: 'truncate text-sm font-medium text-foreground' },
                        row.title || 'Notification'
                    ),
                    // The dot form of Badge: a marker with a word beside it,
                    // rather than a bare coloured circle nothing can read out.
                    row.is_read || row.is_dismissed
                        ? null
                        : h(Badge, { dot: true, variant: 'info' }, { default: () => 'Unread' }),
                    // Without this, dismissing a row in the All view changed
                    // nothing on screen and read as a dead control.
                    row.is_dismissed
                        ? h(
                              Badge,
                              { dot: true, variant: 'neutral' },
                              { default: () => 'Dismissed' }
                          )
                        : null,
                ]),
                row.message ? h('p', { class: `mt-0.5 ${muted}` }, row.message) : null,
            ])
        },
    }),
    // Plain text, like the sessions table's Device column. No other admin table
    // puts a glyph in a data cell -- icons live in the actions column.
    columnHelper.accessor('scope', {
        header: 'Source',
        meta: { narrow: true },
        cell: info => h('span', { class: muted }, scopeLabel(info.getValue())),
    }),
    columnHelper.accessor('type', {
        header: 'Type',
        meta: { narrow: true },
        cell: info =>
            h(Badge, { variant: typeVariant(info.getValue()) }, () => typeLabel(info.getValue())),
    }),
    columnHelper.accessor('created_at', {
        header: 'Received',
        meta: { narrow: true },
        cell: info => {
            const row = info.row.original

            return h(
                'span',
                {
                    class: `tabular-nums whitespace-nowrap ${muted}`,
                    title: row.created_at_exact,
                },
                row.created_at_diff || '-'
            )
        },
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        /* One trigger, real words behind it -- the pattern the users and failed
           jobs tables set. Three glyphs side by side put Delete a few pixels
           from Dismiss, told apart only by a hover tooltip, which is not a
           label for something irreversible. */
        cell: info => {
            const row = info.row.original
            if (!row?.id) return null

            const actions = []

            if (canEdit) {
                actions.push(
                    {
                        label: row.is_read ? 'Mark unread' : 'Mark read',
                        icon: row.is_read ? CircleIcon : CircleCheckIcon,
                        onSelect: () => setRead(row, !row.is_read),
                    },
                    {
                        label: row.is_dismissed ? 'Undismiss' : 'Dismiss',
                        icon: row.is_dismissed ? RotateCcwIcon : CircleXIcon,
                        onSelect: () => setDismissed(row, !row.is_dismissed),
                    }
                )
            }

            if (canDelete) {
                actions.push({
                    label: 'Delete',
                    icon: Trash2Icon,
                    variant: 'destructive',
                    onSelect: () => openDeleteModal(row),
                })
            }

            // RowActions renders nothing for an empty list, so a read-only
            // reader gets no trigger rather than a menu that only fails.
            return h(RowActions, {
                label: `Actions for ${rowLabel(row)}`,
                actions,
            })
        },
    }),
]

const breadcrumbs = computed(() => [
    { label: 'Dashboard', href: route('dashboard') },
    { label: 'All notifications' },
])

const onNavigate = payload => {
    loading.value = Boolean(payload?.loading)
}

const formatExportData = row => ({
    Received: row.created_at_exact ?? '',
    Source: scopeLabel(row.scope),
    Type: typeLabel(row.type),
    Title: row.title ?? '',
    Message: row.message ?? '',
    Read: row.is_read ? 'Yes' : 'No',
    Dismissed: row.is_dismissed ? 'Yes' : 'No',
})
</script>

<template>
    <Head title="All notifications" />

    <main class="mx-auto max-w-4xl" aria-labelledby="notifications">
        <PageHeader title="All notifications" :breadcrumbs="breadcrumbs" />

        <!-- One control. This was a six-field grid sized for a dataset that does
             not exist yet; what is left is the axis a reader actually navigates
             an inbox by. Source is still a column and still reachable by the
             table's own search. -->
        <div class="mb-3 flex flex-wrap items-center gap-3">
            <div
                class="bg-muted inline-flex h-8 items-center rounded-lg p-0.5"
                role="group"
                aria-label="Filter by state">
                <Button
                    v-for="option in STATES"
                    :key="option.value"
                    variant="ghost"
                    size="xs"
                    class="h-7"
                    :class="
                        activeState === option.value
                            ? 'bg-card text-foreground shadow-sm'
                            : 'text-muted-foreground'
                    "
                    :aria-pressed="activeState === option.value"
                    @click="setState(option.value)">
                    {{ option.label }}
                </Button>
            </div>
        </div>

        <Datatable
            :data="rows"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            :filters="filters"
            :page-size-options="pageSizeOptions"
            :default-page-size="Number(pagination.per_page) || 25"
            :row-label="rowLabel"
            empty-message="No notifications"
            :empty-description="
                activeState === 'unread'
                    ? 'You have read everything.'
                    : 'Notifications appear here as they are sent to you.'
            "
            export-file-name="notifications"
            route-name="notifications.index"
            :format-export-data="formatExportData"
            @navigate="onNavigate"
            @update:pagination="pagination = $event" />
    </main>

    <Modal :show="showDeleteModal" size="sm" @close="closeDeleteModal">
        <template #title>Delete notification</template>
        <template #default>
            <p class="text-foreground text-sm font-medium">
                {{ deleteTarget?.title || 'This notification' }}
                <span v-if="deleteTarget" class="text-muted-foreground font-normal">
                    · {{ scopeLabel(deleteTarget.scope) }}
                </span>
            </p>
            <!-- Per-user, not global: the endpoint stamps this reader's row and
                 leaves the notification in place for everyone else it went to.
                 Nothing in the UI puts it back, so the copy says permanently. -->
            <p class="text-muted-foreground mt-2 text-sm">
                Removes it from your list for good. Everyone else it was sent to keeps theirs.
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
