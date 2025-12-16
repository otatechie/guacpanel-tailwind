<script setup>
import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import apiFetch from '@js/utils/apiFetch'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineOptions({
  layout: Default,
})

const page = usePage()

const notificationsProp = computed(() => page.props.notifications)
const serverFilters = computed(() => page.props.filters ?? {})

const normalizePerPage = v => {
  if (v === 'all') return 'all'
  const n = Number.parseInt(v, 10)
  return Number.isFinite(n) ? n : 25
}

const defaultFilters = () => ({
  scope: 'all',
  read: 'all',
  dismissed: 'all',
  type: 'all',
  search: '',
  sort: 'newest',
  per_page: 25,
})

const filters = reactive({
  ...defaultFilters(),
  scope: serverFilters.value.scope ?? 'all',
  read: serverFilters.value.read ?? 'all',
  dismissed: serverFilters.value.dismissed ?? 'all',
  type: serverFilters.value.type ?? 'all',
  search: serverFilters.value.search ?? '',
  sort: serverFilters.value.sort ?? 'newest',
  per_page: normalizePerPage(serverFilters.value.per_page ?? 25),
})

const selected = ref(new Set())
const isWorking = ref(false)
const isFiltering = ref(false)

const rows = ref([])
const links = ref([])
const meta = ref(null)

const notifyTopnavRefresh = () => {
  if (typeof window === 'undefined') return
  window.dispatchEvent(new CustomEvent('app-notifications:refresh'))
}

const hydrateFromPageProps = () => {
  const p = notificationsProp.value
  rows.value = Array.isArray(p?.data) ? p.data : []
  links.value = Array.isArray(p?.links) ? p.links : []
  meta.value = p?.meta ?? null
}

const reconcileSelected = prevSelected => {
  const liveIds = new Set(rows.value.map(r => r.id))
  const next = new Set()

  for (const id of prevSelected) {
    if (liveIds.has(id)) next.add(id)
  }

  selected.value = next
}

watch(
  () => notificationsProp.value,
  () => {
    const prev = selected.value
    hydrateFromPageProps()
    reconcileSelected(prev)
  },
  { deep: true }
)

const hasOwn = (obj, key) => Object.prototype.hasOwnProperty.call(obj || {}, key)

const syncFilterIfPresent = (next, key, setter) => {
  if (!hasOwn(next, key)) return
  const v = next[key]
  if (v === undefined || v === null) return
  setter(v)
}

watch(
  () => serverFilters.value,
  next => {
    syncFilterIfPresent(next, 'scope', v => (filters.scope = v))
    syncFilterIfPresent(next, 'read', v => (filters.read = v))
    syncFilterIfPresent(next, 'dismissed', v => (filters.dismissed = v))
    syncFilterIfPresent(next, 'type', v => (filters.type = v))
    syncFilterIfPresent(next, 'search', v => (filters.search = v ?? ''))
    syncFilterIfPresent(next, 'sort', v => (filters.sort = v))
    syncFilterIfPresent(next, 'per_page', v => (filters.per_page = normalizePerPage(v)))
  },
  { deep: true }
)

const hasRows = computed(() => rows.value.length > 0)
const selectedCount = computed(() => selected.value.size)

const showingCount = computed(() => rows.value.length)

const totalAllCount = computed(() => {
  const total = meta.value?.total_all
  return typeof total === 'number' ? total : null
})

const pageHeaderTitle = computed(() => {
  const total = totalAllCount.value
  if (typeof total === 'number')
    return `All Notifications <span class="text-success text-xs float-right mt-2">(${total} Total)</span>`
  return 'All Notifications'
})

const totalCount = computed(() => {
  const total = meta.value?.total
  return typeof total === 'number' ? total : null
})

const showingLabel = computed(() => {
  const shown = showingCount.value
  const total = totalAllCount.value
  if (typeof total === 'number') return `Showing ${shown} of ${total}`
  return `${shown}`
})

const pageHeaderTitleTotalLabel = computed(() => {
  const total = totalAllCount.value
  const label = showingLabel.value
  if (typeof total === 'number')
    return `All Notifications <span class="text-success text-xs max-xs:block max-xs:mb-0.5 xs:float-right mt-0.5 xs:mt-2">${label}</span>`
  return 'All Notifications'
})

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

const buildQuery = () => {
  return {
    scope: filters.scope,
    read: filters.read,
    dismissed: filters.dismissed,
    type: filters.type,
    search: filters.search,
    sort: filters.sort,
    per_page: filters.per_page,
  }
}

let filterTimer = null

const applyFilters = ({ immediate = false } = {}) => {
  if (filterTimer) clearTimeout(filterTimer)
  filterTimer = null

  const run = () => {
    isFiltering.value = true

    router.get('/notifications/all', buildQuery(), {
      replace: true,
      preserveScroll: true,
      preserveState: true,
      only: ['notifications', 'filters'],
      onFinish: () => {
        isFiltering.value = false
      },
    })
  }

  if (immediate) {
    run()
    return
  }

  filterTimer = setTimeout(run, 250)
}

const clearSearch = () => {
  filters.search = ''
  applyFilters({ immediate: true })
}

const resetFilters = () => {
  const d = defaultFilters()
  filters.scope = d.scope
  filters.read = d.read
  filters.dismissed = d.dismissed
  filters.type = d.type
  filters.search = d.search
  filters.sort = d.sort
  filters.per_page = d.per_page

  applyFilters({ immediate: true })
}

const showToast = (title, message, type = 'success') => {
  if (typeof window !== 'undefined' && typeof window.$showAlert === 'function') {
    window.$showAlert(title, message, type)
  }
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

const updateRow = (id, patch) => {
  const idx = rows.value.findIndex(r => r.id === id)
  if (idx === -1) return
  rows.value.splice(idx, 1, { ...rows.value[idx], ...patch })
}

const removeRow = id => {
  rows.value = rows.value.filter(r => r.id !== id)
  selected.value.delete(id)
}

const relativeTime = iso => {
  if (!iso) return ''
  const then = new Date(iso).getTime()
  if (Number.isNaN(then)) return ''
  const now = Date.now()
  const diff = Math.max(0, Math.floor((now - then) / 1000))

  if (diff < 10) return 'just now'
  if (diff < 60) return `${diff}s ago`
  const mins = Math.floor(diff / 60)
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  const days = Math.floor(hrs / 24)
  return `${days}d ago`
}

const formatDateTime = iso => {
  if (!iso) return ''
  const d = new Date(iso)
  if (Number.isNaN(d.getTime())) return String(iso)

  const md = new Intl.DateTimeFormat(undefined, {
    month: 'numeric',
    day: 'numeric',
    year: 'numeric',
  }).format(d)

  const tm = new Intl.DateTimeFormat(undefined, {
    hour: 'numeric',
    minute: '2-digit',
  }).format(d)

  return `${md} @ ${tm}`
}

const createdDisplay = iso => {
  const full = formatDateTime(iso)
  const rel = relativeTime(iso)
  if (!full) return ''
  if (!rel) return full
  return `${full} · ${rel}`
}

const markRead = async row => {
  if (!row?.id || row.is_read) return

  const original = row.is_read
  updateRow(row.id, { is_read: true, read_at: new Date().toISOString() })

  const ok = await post(`/notifications/${row.id}/read`)
  if (!ok) {
    updateRow(row.id, { is_read: original })
    showToast('Error', 'Failed to mark notification as read.', 'danger')
    return
  }

  notifyTopnavRefresh()
}

const markUnread = async row => {
  if (!row?.id || !row.is_read) return

  const original = row.is_read
  updateRow(row.id, { is_read: false, read_at: null })

  const ok = await post(`/notifications/${row.id}/unread`)
  if (!ok) {
    updateRow(row.id, { is_read: original })
    showToast('Error', 'Failed to mark notification as unread.', 'danger')
    return
  }

  notifyTopnavRefresh()
}

const dismiss = async row => {
  if (!row?.id || row.is_dismissed) return

  const original = row.is_dismissed
  updateRow(row.id, { is_dismissed: true, dismissed_at: new Date().toISOString() })

  const ok = await post(`/notifications/${row.id}/dismiss`)
  if (!ok) {
    updateRow(row.id, { is_dismissed: original })
    showToast('Error', 'Failed to dismiss notification.', 'danger')
    return
  }

  notifyTopnavRefresh()
}

const undismiss = async row => {
  if (!row?.id || !row.is_dismissed) return

  const original = row.is_dismissed
  updateRow(row.id, { is_dismissed: false, dismissed_at: null })

  const ok = await post(`/notifications/${row.id}/undismiss`)
  if (!ok) {
    updateRow(row.id, { is_dismissed: original })
    showToast('Error', 'Failed to undismiss notification.', 'danger')
    return
  }

  notifyTopnavRefresh()
}

const scopeIconName = scope => {
  if (scope === 'system') return 'cpu'
  if (scope === 'user') return 'user'
  if (scope === 'release') return 'release'
  return 'fallback'
}

const typeLabel = type => {
  if (type === 'error') return 'Error'
  if (type === 'warning') return 'Warning'
  if (type === 'success') return 'Success'
  return 'Info'
}

const typeBadgeClass = type => {
  if (type === 'error')
    return 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300'
  if (type === 'warning')
    return 'border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-900/50 dark:bg-yellow-900/20 dark:text-yellow-300'
  if (type === 'success')
    return 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300'
  return 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300'
}

const scopeTooltip = scope => {
  if (scope === 'system') return 'System notification'
  if (scope === 'user') return 'User notification'
  return 'Notification'
}

const dismissedTooltip = isDismissed => (isDismissed ? 'Dismissed' : 'Undismissed')
const readTooltip = isRead => (isRead ? 'Read' : 'Unread')

const dismissedIconName = isDismissed => (isDismissed ? 'x' : 'check')
const readIconName = isRead => (isRead ? 'check' : 'dot')

const tooltipStyle = {
  backgroundColor: 'var(--color-text)',
  color: 'var(--color-bg)',
}

const tooltipClass =
  'absolute -bottom-8 -left-2 -translate-x-1/2 rounded px-2 py-1 text-xs whitespace-nowrap opacity-0 transition-opacity group-hover:opacity-100'

const actionTooltipClass =
  'absolute -bottom-8 left-1/2 -translate-x-1/2 rounded px-2 py-1 text-xs whitespace-nowrap opacity-0 transition-opacity group-hover:opacity-100'

const bulkButtonClasses = 'sm:hidden lg:inline'

const showDeleteModal = ref(false)
const showBulkDeleteModal = ref(false)
const deleteTarget = ref(null)

const confirmDelete = row => {
  deleteTarget.value = row
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteTarget.value = null
}

const confirmBulkDelete = () => {
  if (selectedCount.value === 0) return
  showBulkDeleteModal.value = true
}

const closeBulkDeleteModal = () => {
  showBulkDeleteModal.value = false
}

const destroyRow = async () => {
  if (!deleteTarget.value?.id) return

  const id = deleteTarget.value.id
  const snapshot = rows.value
  removeRow(id)

  const ok = await del(`/notifications/${id}`)
  if (!ok) {
    rows.value = snapshot
    showToast('Error', 'Failed to delete notification.', 'danger')
    return
  }

  closeDeleteModal()
  showToast('Success', 'Notification deleted.', 'success')
  notifyTopnavRefresh()
}

const bulk = async action => {
  if (selectedCount.value === 0) return

  if (action === 'delete') {
    confirmBulkDelete()
    return
  }

  const ids = Array.from(selected.value)
  const snapshot = rows.value

  if (action === 'read') {
    ids.forEach(id => updateRow(id, { is_read: true, read_at: new Date().toISOString() }))
  }

  if (action === 'unread') {
    ids.forEach(id => updateRow(id, { is_read: false, read_at: null }))
  }

  if (action === 'dismiss') {
    ids.forEach(id => updateRow(id, { is_dismissed: true, dismissed_at: new Date().toISOString() }))
  }

  if (action === 'undismiss') {
    ids.forEach(id => updateRow(id, { is_dismissed: false, dismissed_at: null }))
  }

  const ok = await post('/notifications/bulk', { action, ids })

  if (!ok) {
    rows.value = snapshot
    showToast('Error', 'Bulk action failed.', 'danger')
    return
  }

  if (action === 'read') showToast('Success', 'Selected notifications marked as read.', 'success')
  if (action === 'unread')
    showToast('Success', 'Selected notifications marked as unread.', 'success')
  if (action === 'dismiss') showToast('Success', 'Selected notifications dismissed.', 'success')
  if (action === 'undismiss') showToast('Success', 'Selected notifications undismissed.', 'success')

  notifyTopnavRefresh()
}

const runBulkDelete = async () => {
  if (selectedCount.value === 0) return

  const ids = Array.from(selected.value)
  const snapshot = rows.value

  rows.value = rows.value.filter(r => !selected.value.has(r.id))
  selected.value = new Set()

  const ok = await post('/notifications/bulk', { action: 'delete', ids })

  if (!ok) {
    rows.value = snapshot
    showToast('Error', 'Bulk delete failed.', 'danger')
    return
  }

  closeBulkDeleteModal()
  showToast('Success', 'Selected notifications deleted.', 'success')
  notifyTopnavRefresh()
}

let userChannel = null
let systemChannel = null
let refreshTimer = null

const scheduleRefresh = () => {
  if (refreshTimer) return

  refreshTimer = setTimeout(() => {
    refreshTimer = null

    router.reload({
      only: ['notifications', 'filters'],
      preserveScroll: true,
      preserveState: true,
    })
  }, 250)
}

const upsertIncoming = payload => {
  if (!payload?.id) return
  scheduleRefresh()
}

const handleStateChanged = payload => {
  if (!payload?.id) return
  scheduleRefresh()
}

const handleBulkChanged = payload => {
  if (!payload?.action) return
  scheduleRefresh()
}

const subscribeRealtime = () => {
  const userId = page.props?.auth?.user?.id
  if (!window.Echo || !userId) return

  userChannel = window.Echo.private(`users.${userId}`)
    .listen('.app-notification.created', upsertIncoming)
    .listen('.app-notification.state', handleStateChanged)
    .listen('.app-notification.bulk', handleBulkChanged)

  systemChannel = window.Echo.private('system')
    .listen('.app-notification.created', upsertIncoming)
    .listen('.app-notification.state', handleStateChanged)
    .listen('.app-notification.bulk', handleBulkChanged)
}

const unsubscribeRealtime = () => {
  const userId = page.props?.auth?.user?.id
  if (!window.Echo || !userId) return

  window.Echo.leave(`private-users.${userId}`)
  window.Echo.leave('private-system')

  userChannel = null
  systemChannel = null
}

watch(
  () => [
    filters.scope,
    filters.read,
    filters.dismissed,
    filters.type,
    filters.sort,
    filters.per_page,
  ],
  () => applyFilters(),
  { deep: false }
)

watch(
  () => filters.search,
  () => applyFilters(),
  { deep: false }
)

onMounted(() => {
  hydrateFromPageProps()
  reconcileSelected(selected.value)
  subscribeRealtime()
})

onUnmounted(() => {
  if (filterTimer) clearTimeout(filterTimer)
  filterTimer = null

  if (refreshTimer) clearTimeout(refreshTimer)
  refreshTimer = null

  unsubscribeRealtime()
})
</script>

<template>
  <Head title="Notifications" />

  <main class="main-container mx-auto max-w-7xl" aria-labelledby="notifications">
    <div class="container-border">
      <PageHeader
        :title="pageHeaderTitleTotalLabel"
        description="Filter, mark read/unread, dismiss/undismiss, or delete notifications"
        :breadcrumbs="[
          { label: 'Dashboard', href: route('dashboard') },
          { label: 'All Notifications' },
        ]" />

      <section class="bg-[var(--color-bg)] p-6">
        <div
          class="mb-4 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-4 shadow-sm">
          <div class="flex flex-col gap-4">
            <div
              class="xs:grid-cols-2 grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-3 lg:grid-cols-6">
              <div>
                <label class="text-xs text-[var(--color-text-muted)]">Scope</label>
                <select
                  v-model="filters.scope"
                  class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                  <option value="all">All</option>
                  <option value="user">User</option>
                  <option value="system">System</option>
                  <option value="release">Release</option>
                </select>
              </div>

              <div>
                <label class="text-xs text-[var(--color-text-muted)]">Read</label>
                <select
                  v-model="filters.read"
                  class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                  <option value="all">All</option>
                  <option value="unread">Unread</option>
                  <option value="read">Read</option>
                </select>
              </div>

              <div>
                <label class="text-xs text-[var(--color-text-muted)]">Dismissed</label>
                <select
                  v-model="filters.dismissed"
                  class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                  <option value="all">All</option>
                  <option value="dismissed">Dismissed</option>
                  <option value="undismissed">Undismissed</option>
                </select>
              </div>

              <div>
                <label class="text-xs text-[var(--color-text-muted)]">Type</label>
                <select
                  v-model="filters.type"
                  class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                  <option value="all">All</option>
                  <option value="info">Info</option>
                  <option value="success">Success</option>
                  <option value="warning">Warning</option>
                  <option value="error">Error</option>
                </select>
              </div>

              <div>
                <label class="text-xs text-[var(--color-text-muted)]">Sort</label>
                <select
                  v-model="filters.sort"
                  class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                  <option value="newest">Newest</option>
                  <option value="oldest">Oldest</option>
                </select>
              </div>

              <div>
                <label class="text-xs text-[var(--color-text-muted)]">Per page</label>
                <select
                  v-model="filters.per_page"
                  class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 text-sm">
                  <option :value="10">10</option>
                  <option :value="25">25</option>
                  <option :value="50">50</option>
                  <option :value="100">100</option>
                  <option :value="1000">1000</option>
                  <option value="all">All</option>
                </select>
              </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
              <div class="relative w-full">
                <input
                  v-model="filters.search"
                  type="text"
                  class="w-full rounded-md border border-[var(--color-border)] bg-transparent p-2 pr-10 text-sm"
                  placeholder="Search title or message..." />
                <button
                  v-if="filters.search"
                  type="button"
                  class="absolute top-1/2 right-2 -translate-y-1/2 cursor-pointer rounded-md p-1 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                  @click="clearSearch">
                  <svg
                    class="size-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M18 6 6 18" />
                    <path d="M6 6 18 18" />
                  </svg>
                </button>
              </div>
              <button
                type="button"
                class="btn btn-lg btn-secondary mt-2 gap-2 sm:mt-0"
                :disabled="isFiltering || isWorking"
                @click="resetFilters">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="mt-0.25 size-4 sm:size-8 md:size-7.5 lg:size-7 xl:size-6.5"
                  :class="isFiltering || isWorking ? 'animate-spin' : ''">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Reset
              </button>
            </div>
          </div>
        </div>

        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <button
                type="button"
                class="btn btn-sm btn-secondary"
                :disabled="!hasRows || isWorking"
                @click="toggleSelectAll">
                Select all
              </button>

              <span class="text-xs text-[var(--color-text-muted)]">
                Selected: {{ selectedCount }}
              </span>
            </div>
          </div>

          <div class="w-full sm:w-auto">
            <div
              class="xs:gap-0 grid w-full grid-cols-2 gap-1 overflow-visible rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] sm:inline-grid sm:grid-cols-5">
              <button
                type="button"
                class="btn btn-sm btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none rounded-l-lg border-0 sm:text-xs"
                :disabled="selectedCount === 0 || isWorking"
                @click="bulk('read')">
                <svg
                  class="size-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M20 6 9 17l-5-5" />
                </svg>
                <span :class="bulkButtonClasses">Mark read</span>

                <span :class="tooltipClass" :style="tooltipStyle" class="max-sm:hidden">
                  Mark selected notifications as read
                </span>
              </button>

              <button
                type="button"
                class="btn btn-sm btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 max-sm:rounded-r-lg sm:text-xs"
                :disabled="selectedCount === 0 || isWorking"
                @click="bulk('unread')">
                <svg
                  class="size-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M9 15 3 9m0 0 6-6M3 9h9a6 6 0 1 1 0 12h-3" />
                </svg>
                <span :class="bulkButtonClasses">Mark unread</span>

                <span :class="tooltipClass" :style="tooltipStyle" class="max-sm:hidden">
                  Mark selected notifications as unread
                </span>
              </button>

              <button
                type="button"
                class="btn btn-sm btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 sm:text-xs"
                :disabled="selectedCount === 0 || isWorking"
                @click="bulk('dismiss')">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

                <span :class="bulkButtonClasses">Dismiss</span>

                <span :class="tooltipClass" :style="tooltipStyle" class="max-sm:hidden">
                  Dismiss selected notifications
                </span>
              </button>

              <button
                type="button"
                class="btn btn-sm btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 sm:text-xs"
                :disabled="selectedCount === 0 || isWorking"
                @click="bulk('undismiss')">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-4.5">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 16.811c0 .864-.933 1.406-1.683.977l-7.108-4.061a1.125 1.125 0 0 1 0-1.954l7.108-4.061A1.125 1.125 0 0 1 21 8.689v8.122ZM11.25 16.811c0 .864-.933 1.406-1.683.977l-7.108-4.061a1.125 1.125 0 0 1 0-1.954l7.108-4.061A1.125 1.125 0 0 1 11.25 8.689v8.122Z" />
                </svg>

                <span :class="bulkButtonClasses">Undismiss</span>
                <span :class="tooltipClass" :style="tooltipStyle" class="max-sm:hidden">
                  Undo dismissal for selected notifications
                </span>
              </button>

              <button
                type="button"
                class="btn btn-sm btn-danger group relative col-span-2 inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 max-sm:rounded-b sm:col-span-1 sm:rounded-r-lg sm:text-xs"
                :disabled="selectedCount === 0 || isWorking"
                @click="bulk('delete')">
                <svg
                  class="size-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path
                    d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7" />
                  <path d="M10 11v6" />
                  <path d="M14 11v6" />
                  <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                  <path d="M4 7h16" />
                </svg>
                <span :class="bulkButtonClasses">Delete</span>

                <span :class="tooltipClass" :style="tooltipStyle" class="max-sm:hidden">
                  Delete selected notifications
                </span>
              </button>
            </div>
          </div>
        </div>

        <div
          class="overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)]">
          <div v-if="!hasRows" class="p-6 text-sm text-[var(--color-text-muted)]">
            No notifications match your filters.
          </div>

          <template v-else>
            <div class="sm:hidden">
              <div class="divide-y divide-[var(--color-border)]">
                <div v-for="row in rows" :key="row.id" class="p-4">
                  <div class="flex items-start gap-3">
                    <div class="pt-0.5">
                      <input
                        type="checkbox"
                        :checked="selected.has(row.id)"
                        @change="toggleRow(row.id)" />
                    </div>

                    <div class="min-w-0 flex-1">
                      <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                          <div class="font-medium text-[var(--color-text)]">
                            {{ row.title || 'Notification' }}
                          </div>
                          <div class="mt-1 text-sm break-words text-[var(--color-text-muted)]">
                            {{ row.message }}
                          </div>
                        </div>

                        <div class="shrink-0">
                          <span
                            class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium"
                            :class="typeBadgeClass(row.type)">
                            {{ typeLabel(row.type) }}
                          </span>
                        </div>
                      </div>

                      <div
                        class="mt-2 flex flex-wrap items-center gap-2 text-xs text-[var(--color-text-muted)]">
                        <span
                          class="inline-flex items-center gap-1 rounded-full border border-[var(--color-border)] bg-[var(--color-bg)] px-2 py-1">
                          <svg
                            v-if="scopeIconName(row.scope) === 'user'"
                            class="size-3.5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            <path d="M4.5 20.25a7.5 7.5 0 0115 0" />
                          </svg>
                          <svg
                            v-else-if="scopeIconName(row.scope) === 'cpu'"
                            class="size-3.5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                          </svg>
                          <svg
                            v-else-if="scopeIconName(row.scope) === 'release'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-3.5">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12M12.265 3.11a.375.375 0 1 1-.53 0L12 2.845l.265.265Zm-3 0a.375.375 0 1 1-.53 0L9 2.845l.265.265Zm6 0a.375.375 0 1 1-.53 0L15 2.845l.265.265Z" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-3.5">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                          </svg>
                          <span class="font-medium">{{ row.scope || 'notification' }}</span>
                        </span>

                        <span
                          class="inline-flex items-center gap-1 rounded-full border border-[var(--color-border)] bg-[var(--color-bg)] px-2 py-1">
                          <span class="font-medium">Read:</span>
                          <span>{{ row.is_read ? 'Yes' : 'No' }}</span>
                        </span>

                        <span
                          class="inline-flex items-center gap-1 rounded-full border border-[var(--color-border)] bg-[var(--color-bg)] px-2 py-1">
                          <span class="font-medium">Dismissed:</span>
                          <span>{{ row.is_dismissed ? 'Yes' : 'No' }}</span>
                        </span>

                        <span class="w-full text-[var(--color-text-muted)]">
                          {{ createdDisplay(row.created_at) }}
                        </span>
                      </div>

                      <div class="mt-3">
                        <div
                          class="grid w-full grid-cols-3 overflow-hidden rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)]">
                          <button
                            type="button"
                            class="btn btn-sm btn-secondary inline-flex items-center justify-center gap-2 rounded-none border-0"
                            :disabled="isWorking"
                            @click="row.is_read ? markUnread(row) : markRead(row)">
                            <svg
                              v-if="row.is_read"
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M9 15 3 9m0 0 6-6M3 9h9a6 6 0 1 1 0 12h-3" />
                            </svg>
                            <svg
                              v-else
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M20 6 9 17l-5-5" />
                            </svg>
                            <span class="xs:inline hidden">
                              {{ row.is_read ? 'Unread' : 'Read' }}
                            </span>
                            <span class="xs:hidden">{{ row.is_read ? 'Undo' : 'Read' }}</span>
                          </button>

                          <button
                            type="button"
                            class="btn btn-sm btn-secondary inline-flex items-center justify-center gap-2 rounded-none border-0"
                            :disabled="isWorking"
                            @click="row.is_dismissed ? undismiss(row) : dismiss(row)">
                            <svg
                              v-if="row.is_dismissed"
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M9 12l2 2 4-4" />
                              <path d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10Z" />
                            </svg>
                            <svg
                              v-else
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M18 6 6 18" />
                              <path d="M6 6 18 18" />
                            </svg>
                            <span class="xs:inline hidden">
                              {{ row.is_dismissed ? 'Undismiss' : 'Dismiss' }}
                            </span>
                            <span class="xs:hidden">
                              {{ row.is_dismissed ? 'Undo' : 'Dismiss' }}
                            </span>
                          </button>

                          <button
                            type="button"
                            class="btn btn-sm btn-danger inline-flex items-center justify-center gap-2 rounded-none border-0"
                            :disabled="isWorking"
                            @click="confirmDelete(row)">
                            <svg
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path
                                d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7" />
                              <path d="M10 11v6" />
                              <path d="M14 11v6" />
                              <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                              <path d="M4 7h16" />
                            </svg>
                            <span>Delete</span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="w-full overflow-x-auto">
              <table
                class="hidden w-full min-w-max table-auto border-collapse text-left text-sm sm:table">
                <thead class="border-b border-[var(--color-border)] text-[var(--color-text-muted)]">
                  <tr>
                    <th class="w-10 p-3">
                      <input
                        type="checkbox"
                        :checked="selectedCount === rows.length && rows.length > 0"
                        @change="toggleSelectAll" />
                    </th>
                    <th class="p-3">Notification</th>
                    <th class="w-20 p-3">Scope</th>
                    <th class="w-24 p-3">Type</th>
                    <th class="w-20 p-3 text-center">Read</th>
                    <th class="w-28 p-3 text-center">Dismissed</th>
                    <th class="w-40 p-3 text-right">Actions</th>
                  </tr>
                </thead>

                <tbody class="divide-y divide-[var(--color-border)]">
                  <tr
                    v-for="row in rows"
                    :key="row.id"
                    class="transition-colors hover:bg-[var(--color-surface-muted)]">
                    <td class="p-3 align-middle">
                      <input
                        type="checkbox"
                        :checked="selected.has(row.id)"
                        @change="toggleRow(row.id)" />
                    </td>

                    <td class="p-3 align-middle">
                      <div class="flex items-start gap-3">
                        <div class="min-w-0">
                          <div class="font-medium text-[var(--color-text)]">
                            {{ row.title || 'Notification' }}
                          </div>
                          <div class="mt-1 truncate text-[var(--color-text-muted)]">
                            {{ row.message }}
                          </div>
                          <div class="mt-1 text-xs text-[var(--color-text-muted)]">
                            {{ createdDisplay(row.created_at) }}
                          </div>
                        </div>
                      </div>
                    </td>

                    <td class="p-3 align-middle">
                      <span class="group relative inline-flex items-center">
                        <svg
                          v-if="scopeIconName(row.scope) === 'user'"
                          class="size-4 text-[var(--color-text-muted)]"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                          <path d="M4.5 20.25a7.5 7.5 0 0115 0" />
                        </svg>

                        <svg
                          v-else-if="scopeIconName(row.scope) === 'cpu'"
                          class="size-4 text-[var(--color-text-muted)]"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                        </svg>

                        <svg
                          v-else-if="scopeIconName(row.scope) === 'release'"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="size-4 text-[var(--color-text-muted)]">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12M12.265 3.11a.375.375 0 1 1-.53 0L12 2.845l.265.265Zm-3 0a.375.375 0 1 1-.53 0L9 2.845l.265.265Zm6 0a.375.375 0 1 1-.53 0L15 2.845l.265.265Z" />
                        </svg>

                        <svg
                          v-else
                          class="size-4 text-[var(--color-text-muted)]"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                        </svg>

                        <span :class="tooltipClass" :style="tooltipStyle">
                          {{ scopeTooltip(row.scope) }}
                        </span>
                      </span>
                    </td>

                    <td class="p-3 align-middle">
                      <span
                        class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium"
                        :class="typeBadgeClass(row.type)">
                        {{ typeLabel(row.type) }}
                      </span>
                    </td>

                    <td class="p-3 text-center align-middle">
                      <span class="group relative inline-flex items-center justify-center">
                        <svg
                          v-if="readIconName(row.is_read) === 'check'"
                          class="size-4 text-[var(--color-text-muted)]"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                          <path d="M9 12.75l2.25 2.25L15 10.5" />
                        </svg>

                        <svg
                          v-else
                          class="size-4 text-[var(--color-text-muted)]"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                          <path d="M12 12h.01" />
                        </svg>

                        <span :class="tooltipClass" :style="tooltipStyle">
                          {{ readTooltip(row.is_read) }}
                        </span>
                      </span>
                    </td>

                    <td class="p-3 text-center align-middle">
                      <span class="group relative inline-flex items-center justify-center">
                        <svg
                          v-if="dismissedIconName(row.is_dismissed) === 'x'"
                          class="size-4 text-[var(--color-text-muted)]"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                          <path d="M9.75 9.75l4.5 4.5M14.25 9.75l-4.5 4.5" />
                        </svg>

                        <svg
                          v-else
                          class="size-4 text-[var(--color-text-muted)]"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <path d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                          <path d="M9 12.75l2.25 2.25L15 10.5" />
                        </svg>

                        <span :class="tooltipClass" :style="tooltipStyle">
                          {{ dismissedTooltip(row.is_dismissed) }}
                        </span>
                      </span>
                    </td>

                    <td class="overflow-visible p-3 align-middle">
                      <div class="flex justify-end overflow-visible">
                        <div
                          class="inline-flex overflow-visible rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)]">
                          <button
                            type="button"
                            class="group relative cursor-pointer px-2 py-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                            :disabled="isWorking"
                            @click="row.is_read ? markUnread(row) : markRead(row)">
                            <svg
                              v-if="row.is_read"
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M9 15 3 9m0 0 6-6M3 9h9a6 6 0 1 1 0 12h-3" />
                            </svg>
                            <svg
                              v-else
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M20 6 9 17l-5-5" />
                            </svg>

                            <span :class="actionTooltipClass" :style="tooltipStyle">
                              {{ row.is_read ? 'Mark unread' : 'Mark read' }}
                            </span>
                          </button>

                          <div class="w-px bg-[var(--color-border)]"></div>

                          <button
                            type="button"
                            class="group relative cursor-pointer px-2 py-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                            :disabled="isWorking"
                            @click="row.is_dismissed ? undismiss(row) : dismiss(row)">
                            <svg
                              v-if="row.is_dismissed"
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M9 12l2 2 4-4" />
                              <path d="M12 22a10 10 0 1 0-10-10 10 10 0 0 0 10 10Z" />
                            </svg>
                            <svg
                              v-else
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path d="M18 6 6 18" />
                              <path d="M6 6 18 18" />
                            </svg>

                            <span :class="actionTooltipClass" :style="tooltipStyle">
                              {{ row.is_dismissed ? 'Undismiss' : 'Dismiss' }}
                            </span>
                          </button>

                          <div class="w-px bg-[var(--color-border)]"></div>

                          <button
                            type="button"
                            class="group relative cursor-pointer px-2 py-2 text-red-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                            :disabled="isWorking"
                            @click="confirmDelete(row)">
                            <svg
                              class="size-4"
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round">
                              <path
                                d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7" />
                              <path d="M10 11v6" />
                              <path d="M14 11v6" />
                              <path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                              <path d="M4 7h16" />
                            </svg>

                            <span :class="actionTooltipClass" :style="tooltipStyle">Delete</span>
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>
        </div>

        <div v-if="links?.length" class="mt-4 flex flex-wrap gap-2">
          <button
            v-for="l in links"
            :key="l.label"
            type="button"
            class="btn btn-sm btn-secondary"
            :class="l.active ? 'btn-outline' : ''"
            :disabled="!l.url || isWorking"
            v-html="l.label"
            @click="
              l.url &&
              router.visit(l.url, {
                preserveScroll: true,
                preserveState: true,
                only: ['notifications', 'filters'],
              })
            " />
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
          :disabled="isWorking"
          @click="closeDeleteModal">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary btn-sm"
          :disabled="isWorking"
          @click="destroyRow">
          {{ isWorking ? 'Deleting...' : 'Yes, Delete' }}
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
          :disabled="isWorking"
          @click="closeBulkDeleteModal">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary btn-sm"
          :disabled="isWorking"
          @click="runBulkDelete">
          {{ isWorking ? 'Deleting...' : 'Yes, Delete' }}
        </button>
      </div>
    </template>
  </Modal>
</template>
