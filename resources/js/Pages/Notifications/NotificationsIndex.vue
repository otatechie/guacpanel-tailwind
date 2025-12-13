<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import apiFetch from '@js/utils/apiFetch'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'

const page = usePage()

defineOptions({
  layout: Default,
})

const notifications = computed(() => page.props.notifications)
const serverFilters = page.props.filters ?? {}

const filters = reactive({
  scope: serverFilters.scope ?? 'all',
  read: serverFilters.read ?? 'all',
  dismissed: serverFilters.dismissed ?? 'undismissed',
  type: serverFilters.type ?? 'all',
  search: serverFilters.search ?? '',
  sort: serverFilters.sort ?? 'newest',
  per_page: serverFilters.per_page ?? 25,
})

const selected = ref(new Set())
const isWorking = ref(false)

const rows = computed(() => notifications.value?.data ?? [])
const hasRows = computed(() => rows.value.length > 0)
const selectedCount = computed(() => selected.value.size)

const toggleSelectAll = () => {
  if (!hasRows.value) return

  if (selected.value.size === rows.value.length) {
    selected.value = new Set()
    return
  }

  selected.value = new Set(rows.value.map(r => r.id))
}

const toggleRow = id => {
  const next = new Set(selected.value)
  if (next.has(id)) next.delete(id)
  else next.add(id)
  selected.value = next
}

const applyFilters = () => {
  router.get('/notifications/all', { ...filters }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    onSuccess: () => {
      selected.value = new Set()
    },
  })
}

const clearSearch = () => {
  filters.search = ''
  applyFilters()
}

const post = async (url, body = {}) => {
  isWorking.value = true
  try {
    const res = await apiFetch(url, {
      method: 'POST',
      body: JSON.stringify(body),
    })

    return res.ok
  } finally {
    isWorking.value = false
  }
}

const del = async url => {
  isWorking.value = true
  try {
    const res = await apiFetch(url, {
      method: 'DELETE',
    })

    return res.ok
  } finally {
    isWorking.value = false
  }
}

const refresh = () => {
  router.reload({ preserveScroll: true, preserveState: true })
}

const markRead = async row => {
  const ok = await post(`/notifications/${row.id}/read`)
  if (ok) refresh()
}

const markUnread = async row => {
  const ok = await post(`/notifications/${row.id}/unread`)
  if (ok) refresh()
}

const dismiss = async row => {
  const ok = await post(`/notifications/${row.id}/dismiss`)
  if (ok) refresh()
}

const undismiss = async row => {
  const ok = await post(`/notifications/${row.id}/undismiss`)
  if (ok) refresh()
}

const destroyRow = async row => {
  const ok = await del(`/notifications/${row.id}`)
  if (ok) refresh()
}

const bulk = async action => {
  if (selectedCount.value === 0) return

  const ok = await post('/notifications/bulk', {
    action,
    ids: Array.from(selected.value),
  })

  if (ok) {
    selected.value = new Set()
    refresh()
  }
}
</script>

<template>
  <Head title="Notifications" />

  <main class="main-container mx-auto max-w-7xl" aria-labelledby="audit-log">
    <div class="container-border">
      <PageHeader
        title="Notifications"
        description="Filter, mark read/unread, dismiss/undismiss, or delete notifications"
        :breadcrumbs="[
          { label: 'Dashboard', href: route('dashboard') },
          { label: 'Notifications' },
        ]" />

      <section class="bg-[var(--color-bg)] p-6">

        <div class="mb-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-4">
          <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
            <div class="md:col-span-1">
              <label class="text-xs text-[var(--color-text-muted)]">Scope</label>
              <select v-model="filters.scope" class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                <option value="all">All</option>
                <option value="user">User</option>
                <option value="system">System</option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="text-xs text-[var(--color-text-muted)]">Read</label>
              <select v-model="filters.read" class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                <option value="all">All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="text-xs text-[var(--color-text-muted)]">Dismissed</label>
              <select v-model="filters.dismissed" class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                <option value="all">All</option>
                <option value="undismissed">Undismissed</option>
                <option value="dismissed">Dismissed</option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="text-xs text-[var(--color-text-muted)]">Type</label>
              <select v-model="filters.type" class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                <option value="all">All</option>
                <option value="info">Info</option>
                <option value="success">Success</option>
                <option value="warning">Warning</option>
                <option value="error">Error</option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="text-xs text-[var(--color-text-muted)]">Sort</label>
              <select v-model="filters.sort" class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
              </select>
            </div>

            <div class="md:col-span-1">
              <label class="text-xs text-[var(--color-text-muted)]">Per page</label>
              <select v-model="filters.per_page" class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
            </div>

            <div class="md:col-span-6">
              <label class="text-xs text-[var(--color-text-muted)]">Search</label>
              <div class="mt-1 flex gap-2">
                <input
                  v-model="filters.search"
                  type="text"
                  class="w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm"
                  placeholder="Search title or message..." />

                <button
                  type="button"
                  class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
                  @click="applyFilters">
                  Apply
                </button>

                <button
                  type="button"
                  class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
                  @click="clearSearch">
                  Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <button
              type="button"
              class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
              :disabled="!hasRows || isWorking"
              @click="toggleSelectAll">
              Select all
            </button>

            <span class="text-sm text-[var(--color-text-muted)]">
              Selected: {{ selectedCount }}
            </span>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <button
              type="button"
              class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
              :disabled="selectedCount === 0 || isWorking"
              @click="bulk('read')">
              Mark read
            </button>

            <button
              type="button"
              class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
              :disabled="selectedCount === 0 || isWorking"
              @click="bulk('unread')">
              Mark unread
            </button>

            <button
              type="button"
              class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
              :disabled="selectedCount === 0 || isWorking"
              @click="bulk('dismiss')">
              Dismiss
            </button>

            <button
              type="button"
              class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
              :disabled="selectedCount === 0 || isWorking"
              @click="bulk('undismiss')">
              Undismiss
            </button>

            <button
              type="button"
              class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm text-red-500 hover:text-red-400"
              :disabled="selectedCount === 0 || isWorking"
              @click="bulk('delete')">
              Delete
            </button>
          </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)]">
          <div v-if="!hasRows" class="p-6 text-sm text-[var(--color-text-muted)]">
            No notifications match your filters.
          </div>

          <table v-else class="w-full text-left text-sm">
            <thead class="border-b border-[var(--color-border)] text-[var(--color-text-muted)]">
              <tr>
                <th class="w-10 p-3">
                  <input
                    type="checkbox"
                    :checked="selectedCount === rows.length && rows.length > 0"
                    @change="toggleSelectAll" />
                </th>
                <th class="p-3">Title</th>
                <th class="p-3">Scope</th>
                <th class="p-3">Type</th>
                <th class="p-3">Read</th>
                <th class="p-3">Dismissed</th>
                <th class="p-3">Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="row in rows"
                :key="row.id"
                class="border-b border-[var(--color-border)] last:border-b-0">
                <td class="p-3 align-top">
                  <input
                    type="checkbox"
                    :checked="selected.has(row.id)"
                    @change="toggleRow(row.id)" />
                </td>

                <td class="p-3 align-top">
                  <div class="font-medium text-[var(--color-text)]">{{ row.title || 'Notification' }}</div>
                  <div class="mt-1 text-[var(--color-text-muted)]">{{ row.message }}</div>
                  <div class="mt-1 text-xs text-[var(--color-text-muted)]">{{ row.created_at }}</div>
                </td>

                <td class="p-3 align-top text-[var(--color-text)]">{{ row.scope }}</td>
                <td class="p-3 align-top text-[var(--color-text)]">{{ row.type }}</td>
                <td class="p-3 align-top">
                  <span class="text-[var(--color-text)]">{{ row.is_read ? 'read' : 'unread' }}</span>
                </td>
                <td class="p-3 align-top">
                  <span class="text-[var(--color-text)]">{{ row.is_dismissed ? 'dismissed' : 'undismissed' }}</span>
                </td>

                <td class="p-3 align-top">
                  <div class="flex flex-wrap gap-2">
                    <button
                      type="button"
                      class="rounded-md border border-[var(--color-border)] px-2 py-1 text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
                      :disabled="isWorking"
                      @click="row.is_read ? markUnread(row) : markRead(row)">
                      {{ row.is_read ? 'Mark unread' : 'Mark read' }}
                    </button>

                    <button
                      type="button"
                      class="rounded-md border border-[var(--color-border)] px-2 py-1 text-xs text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
                      :disabled="isWorking"
                      @click="row.is_dismissed ? undismiss(row) : dismiss(row)">
                      {{ row.is_dismissed ? 'Undismiss' : 'Dismiss' }}
                    </button>

                    <button
                      type="button"
                      class="rounded-md border border-[var(--color-border)] px-2 py-1 text-xs text-red-500 hover:text-red-400"
                      :disabled="isWorking"
                      @click="destroyRow(row)">
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="notifications?.links?.length" class="mt-4 flex flex-wrap gap-2">
          <button
            v-for="l in notifications.links"
            :key="l.label"
            type="button"
            class="rounded-md border border-[var(--color-border)] px-3 py-2 text-sm"
            :class="l.active ? 'text-[var(--color-text)]' : 'text-[var(--color-text-muted)] hover:text-[var(--color-text)]'"
            :disabled="!l.url || isWorking"
            v-html="l.label"
            @click="l.url && router.visit(l.url, { preserveScroll: true, preserveState: true })" />
        </div>

      </section>
    </div>
  </main>

</template>
