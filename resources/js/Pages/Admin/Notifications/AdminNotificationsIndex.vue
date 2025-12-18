<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { computed, h, ref, watch } from 'vue'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import NotificationTypeBadge from '@js/Components/Common/NotificationTypeBadge.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

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

const EMPTY = '—'

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

const scopeLabel = scope => dash(scope || 'notification')

const scopeIconName = scope => {
  const s = String(scope ?? '')
    .trim()
    .toLowerCase()

  if (!s) return 'tag'
  if (s === 'user' || s === 'users') return 'user'
  if (s === 'system' || s === 'cpu') return 'cpu'
  if (s === 'release' || s === 'releases') return 'release'
  return 'tag'
}

const autoExpireValue = row => {
  return (
    row?.auto_expires_on_diff ??
    row?.auto_expire_on_diff ??
    row?.expires_on_diff ??
    row?.expires_at_diff ??
    row?.expires_at_human ??
    row?.expires_at ??
    null
  )
}

const countValue = v => {
  const n = Number(v ?? 0)
  return Number.isFinite(n) ? n : 0
}

const usersLabel = n => {
  const num = countValue(n)
  return `${num} ${num === 1 ? 'USER' : 'USERS'}`
}

const StatusCountCell = {
  name: 'StatusCountCell',
  props: {
    count: { type: [Number, String], required: true },
  },
  setup(p) {
    return () =>
      h('div', { class: 'flex items-center justify-center' }, [
        h(
          'span',
          { class: 'text-xxs font-bold uppercase text-[var(--color-text-muted)] text-nowrap' },
          usersLabel(p.count)
        ),
      ])
  },
}

const ScopeCell = {
  name: 'ScopeCell',
  props: {
    row: { type: Object, required: true },
  },
  setup(p) {
    return () => {
      const icon = scopeIconName(p.row.scope)

      return h(
        'span',
        {
          class:
            'inline-flex items-center gap-1 rounded-full border border-[var(--color-border)] bg-[var(--color-bg)] px-2 py-1',
        },
        [
          icon === 'user'
            ? h(
                'svg',
                {
                  class: 'size-3.5',
                  viewBox: '0 0 24 24',
                  fill: 'none',
                  stroke: 'currentColor',
                  'stroke-width': '1.5',
                  'stroke-linecap': 'round',
                  'stroke-linejoin': 'round',
                  'aria-hidden': 'true',
                },
                [
                  h('path', {
                    d: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z',
                  }),
                  h('path', {
                    d: 'M4.5 20.25a7.5 7.5 0 0115 0',
                  }),
                ]
              )
            : icon === 'cpu'
              ? h(
                  'svg',
                  {
                    class: 'size-3.5',
                    xmlns: 'http://www.w3.org/2000/svg',
                    fill: 'none',
                    viewBox: '0 0 24 24',
                    'stroke-width': '1.5',
                    stroke: 'currentColor',
                    'aria-hidden': 'true',
                  },
                  [
                    h('path', {
                      'stroke-linecap': 'round',
                      'stroke-linejoin': 'round',
                      d: 'M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z',
                    }),
                  ]
                )
              : icon === 'release'
                ? h(
                    'svg',
                    {
                      xmlns: 'http://www.w3.org/2000/svg',
                      viewBox: '0 0 288 288',
                      fill: 'none',
                      'aria-hidden': 'true',
                      class: 'size-3.5 text-[var(--color-text-muted)]',
                    },
                    [
                      h('path', {
                        d: 'M232.213 29.661a6.75 6.75 0 0 1 8.659 4.019 293.104 293.104 0 0 1 4.671 13.82 293.554 293.554 0 0 1 12.249 63.562c6.142 6.107 9.958 14.579 9.958 23.938 0 9.359-3.816 17.831-9.958 23.938a293.551 293.551 0 0 1-12.249 63.562 293.143 293.143 0 0 1-4.671 13.82 6.75 6.75 0 0 1-12.678-4.64c.937-2.56 1.838-5.137 2.702-7.731a279.258 279.258 0 0 0-88.553-26.124 207.662 207.662 0 0 0 8.709 22.888c4.285 9.53 1.151 21.268-8.338 26.747l-7.875 4.547c-9.831 5.675-22.847 2.225-27.825-8.542a256.906 256.906 0 0 1-16.74-48.337C60.857 190.897 38.25 165.588 38.25 135c0-33.551 27.199-60.75 60.75-60.75h9c8.258 0 16.431-.356 24.505-1.052 35.031-3.023 68.22-12.466 98.391-27.147a278.666 278.666 0 0 0-2.702-7.73 6.75 6.75 0 0 1 4.019-8.66Zm2.681 29.45a292.862 292.862 0 0 1-96.423 27.083c-3.74 15.652-5.721 31.994-5.721 48.806 0 16.812 1.981 33.154 5.721 48.806a292.884 292.884 0 0 1 96.423 27.083 280.39 280.39 0 0 0 9.636-55.608c.477-6.697.72-13.46.72-20.281 0-6.821-.243-13.584-.72-20.281a280.396 280.396 0 0 0-9.636-55.608ZM124.37 182.697A223.556 223.556 0 0 1 119.25 135c0-16.365 1.766-32.325 5.12-47.697a299.37 299.37 0 0 1-16.37.447h-9c-26.096 0-47.25 21.155-47.25 47.25S72.904 182.25 99 182.25h9c5.492 0 10.95.15 16.37.447Zm-20.039 13.053a243.387 243.387 0 0 0 14.937 42.049c1.434 3.103 5.418 4.481 8.821 2.516l7.875-4.547c3.054-1.763 4.429-5.84 2.775-9.519a221.156 221.156 0 0 1-10.907-29.811A285.523 285.523 0 0 0 108 195.75h-3.669Z',
                        fill: 'currentColor',
                        stroke: 'currentColor',
                        'stroke-width': '2',
                      }),
                    ]
                  )
                : h(
                    'svg',
                    {
                      xmlns: 'http://www.w3.org/2000/svg',
                      fill: 'none',
                      viewBox: '0 0 24 24',
                      'stroke-width': '1.5',
                      stroke: 'currentColor',
                      class: 'size-3.5',
                      'aria-hidden': 'true',
                    },
                    [
                      h('path', {
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round',
                        d: 'M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z',
                      }),
                    ]
                  ),
          h('span', { class: 'text-xxs font-medium uppercase' }, scopeLabel(p.row.scope)),
        ]
      )
    }
  },
}

const TypeCell = {
  name: 'TypeCell',
  props: {
    row: { type: Object, required: true },
  },
  setup(p) {
    return () => h(NotificationTypeBadge, { type: p.row.type })
  },
}

const UserCell = {
  name: 'UserCell',
  props: {
    row: { type: Object, required: true },
  },
  setup(p) {
    return () => {
      const username = p.row?.username
      const email = p.row?.user_email

      if (!username && !email) return h('span', EMPTY)

      const parts = []

      if (username) {
        parts.push(
          h('div', { class: 'flex items-center gap-1.5 min-w-0' }, [
            h(
              'svg',
              {
                class: 'size-3.5 text-[var(--color-text-muted)] shrink-0',
                viewBox: '0 0 24 24',
                fill: 'none',
                stroke: 'currentColor',
                'stroke-width': '1.5',
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'aria-hidden': 'true',
              },
              [
                h('path', { d: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z' }),
                h('path', { d: 'M4.5 20.25a7.5 7.5 0 0115 0' }),
              ]
            ),
            h('span', { class: 'truncate' }, dash(username)),
          ])
        )
      }

      if (email) {
        parts.push(
          h('div', { class: 'flex items-center gap-1.5 min-w-0' }, [
            h(
              'svg',
              {
                class: 'size-3.5 text-[var(--color-text-muted)] shrink-0',
                viewBox: '0 0 24 24',
                fill: 'none',
                stroke: 'currentColor',
                'stroke-width': '1.5',
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'aria-hidden': 'true',
              },
              [
                h('path', {
                  d: 'M21.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15A2.25 2.25 0 012.25 17.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75m19.5 0l-8.31 5.815a2.25 2.25 0 01-2.58 0L2.25 6.75',
                }),
              ]
            ),
            h(
              'a',
              {
                href: `mailto:${email}`,
                class:
                  'truncate text-[var(--primary-color)] hover:opacity-80 underline underline-offset-2',
                onClick: e => e.stopPropagation(),
              },
              dash(email)
            ),
          ])
        )
      }

      return h('div', { class: 'min-w-0 space-y-1' }, parts)
    }
  },
}

const ActionButtonsCell = {
  name: 'ActionButtonsCell',
  props: {
    row: { type: Object, required: true },
  },
  setup(p) {
    return () =>
      h('div', { class: 'flex items-center justify-end gap-2' }, [
        h(
          Link,
          {
            href: route('admin.notifications.edit', p.row.id),
            class: 'btn btn-secondary btn-xs inline-flex items-center justify-center gap-2',
          },
          {
            default: () => [
              h(
                'svg',
                {
                  class: 'size-4',
                  viewBox: '0 0 24 24',
                  fill: 'none',
                  stroke: 'currentColor',
                  'stroke-width': '1.5',
                  'stroke-linecap': 'round',
                  'stroke-linejoin': 'round',
                  'aria-hidden': 'true',
                },
                [
                  h('path', {
                    d: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l9.932-8.931Z',
                  }),
                  h('path', {
                    d: 'M19.5 7.125L16.862 4.487',
                  }),
                ]
              ),
              h('span', { class: 'xs:inline hidden' }, 'Edit'),
            ],
          }
        ),
        h(
          'button',
          {
            type: 'button',
            class: 'btn btn-danger btn-xs inline-flex items-center justify-center gap-2',
            onClick: () => openDeleteModal(p.row),
          },
          [
            h(
              'svg',
              {
                class: 'size-4',
                viewBox: '0 0 24 24',
                fill: 'none',
                stroke: 'currentColor',
                'stroke-width': '1.5',
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'aria-hidden': 'true',
              },
              [
                h('path', {
                  d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7',
                }),
                h('path', { d: 'M10 11v6' }),
                h('path', { d: 'M14 11v6' }),
                h('path', { d: 'M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3' }),
                h('path', { d: 'M4 7h16' }),
              ]
            ),
            h('span', { class: 'xs:inline hidden' }, 'Delete'),
          ]
        ),
      ])
  },
}

const columnHelper = createColumnHelper()

const columns = [
  columnHelper.accessor(row => dash(row.created_at_diff), {
    id: 'created_at',
    header: 'Created',
    cell: info => info.getValue(),
  }),
  columnHelper.accessor('scope', {
    header: 'Scope',
    cell: info => h(ScopeCell, { row: info.row.original }),
  }),
  columnHelper.accessor('type', {
    header: 'Type',
    cell: info => h(TypeCell, { row: info.row.original }),
  }),
  columnHelper.accessor('title', {
    header: 'Title',
    cell: info => h('div', { class: 'truncate' }, dash(info.row.original.title)),
  }),
  columnHelper.accessor('message', {
    header: 'Message',
    cell: info =>
      h(
        'div',
        { class: 'whitespace-normal break-words leading-snug' },
        dash(info.row.original.message)
      ),
  }),
  columnHelper.accessor(row => row.username ?? null, {
    id: 'user',
    header: 'User',
    cell: info => h(UserCell, { row: info.row.original }),
  }),

  columnHelper.accessor(row => countValue(row.read_count), {
    id: 'read_count',
    header: 'Read',
    cell: info => h(StatusCountCell, { count: info.row.original.read_count }),
  }),
  columnHelper.accessor(row => countValue(row.dismissed_count), {
    id: 'dismissed_count',
    header: 'Dismissed',
    cell: info => h(StatusCountCell, { count: info.row.original.dismissed_count }),
  }),
  columnHelper.accessor(row => countValue(row.deleted_count), {
    id: 'deleted_count',
    header: 'Deleted',
    cell: info => h(StatusCountCell, { count: info.row.original.deleted_count }),
  }),

  columnHelper.accessor(row => dash(row.scheduled_on_diff), {
    id: 'scheduled_on',
    header: 'Scheduled',
    cell: info => info.getValue(),
  }),
  columnHelper.accessor(row => dash(autoExpireValue(row)), {
    id: 'auto_expire',
    header: 'Auto Expire',
    cell: info => info.getValue(),
  }),
  columnHelper.display({
    id: 'actions',
    header: 'Actions',
    cell: info => h(ActionButtonsCell, { row: info.row.original }),
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

  <main class="main-container mx-auto max-w-7xl" aria-labelledby="admin-notifications">
    <div class="container-border">
      <PageHeader
        title="Admin Notifications"
        description="Create and manage app notifications"
        :breadcrumbs="breadcrumbs">
        <template #actions>
          <Link :href="route('admin.notifications.create')" class="btn btn-primary btn-sm">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="mr-1 size-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Notification
          </Link>
        </template>
      </PageHeader>

      <section class="bg-[var(--color-bg)] p-6">
        <div
          class="notifications-data-table rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
          <Datatable
            class="datatable-admin-notifications"
            :data="notifications.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            :filters="filters"
            :page-size-options="pageSizeOptions"
            :default-page-size="Number(pagination.per_page) || 25"
            :search-fields="[
              'title',
              'message',
              'scope',
              'type',
              'username',
              'user_email',
              'read_count',
              'dismissed_count',
              'deleted_count',
            ]"
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
      </section>
    </div>
  </main>

  <Modal :show="showDeleteModal" size="md" @close="closeDeleteModal">
    <template #title>
      <div class="flex items-center text-red-600">Delete Notification</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-[var(--color-text-muted)]">
          Are you sure you want to delete this notification? This action cannot be undone.
        </p>
        <Alert type="warning" title="Notification">
          <span class="font-medium">{{ deleteTarget?.title || 'Notification' }}</span>
        </Alert>
      </div>
    </template>

    <template #footer>
      <div class="flex items-center justify-end gap-8">
        <button
          type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
          :disabled="loading"
          @click="closeDeleteModal">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary btn-sm"
          :disabled="loading"
          @click="destroyRow">
          {{ loading ? 'Deleting...' : 'Yes, Delete' }}
        </button>
      </div>
    </template>
  </Modal>

  <Modal :show="showBulkDeleteModal" size="md" @close="closeBulkDeleteModal">
    <template #title>
      <div class="flex items-center text-red-600">Delete Notifications</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-[var(--color-text-muted)]">
          Delete
          <span class="font-medium">{{ selectedCount }}</span>
          selected notifications? This action cannot be undone.
        </p>
      </div>
    </template>

    <template #footer>
      <div class="flex items-center justify-end gap-8">
        <button
          type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
          :disabled="loading"
          @click="closeBulkDeleteModal">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary btn-sm"
          :disabled="loading"
          @click="runBulkDelete">
          {{ loading ? 'Deleting...' : 'Yes, Delete' }}
        </button>
      </div>
    </template>
  </Modal>
</template>
