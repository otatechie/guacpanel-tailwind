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

const tableSortKey = ref(null)
const tableSortDir = ref('asc')

const isTableSortedBy = key => tableSortKey.value === key

const toggleTableSort = key => {
  if (tableSortKey.value !== key) {
    tableSortKey.value = key
    tableSortDir.value = 'asc'
    return
  }

  tableSortDir.value = tableSortDir.value === 'asc' ? 'desc' : 'asc'
}

const getSortValue = (row, key) => {
  if (!row) return ''

  if (key === 'title') return String(row.title ?? '')
  if (key === 'scope') return String(row.scope ?? '')
  if (key === 'type') return String(row.type ?? '')
  if (key === 'read') return row.is_read ? 1 : 0
  if (key === 'dismissed') return row.is_dismissed ? 1 : 0

  return ''
}

const displayedRows = computed(() => {
  const base = Array.isArray(rows.value) ? rows.value : []
  if (!tableSortKey.value) return base

  const key = tableSortKey.value
  const dir = tableSortDir.value === 'desc' ? -1 : 1

  return base
    .map((r, i) => ({ r, i }))
    .sort((a, b) => {
      const av = getSortValue(a.r, key)
      const bv = getSortValue(b.r, key)

      if (typeof av === 'number' && typeof bv === 'number') {
        if (av === bv) return a.i - b.i
        return (av - bv) * dir
      }

      const as = String(av).toLowerCase()
      const bs = String(bv).toLowerCase()

      if (as === bs) return a.i - b.i
      return as.localeCompare(bs) * dir
    })
    .map(x => x.r)
})

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
  if (type === 'error') return 'notification-badge-error'
  if (type === 'warning') return 'notification-badge-warning'
  if (type === 'success') return 'notification-badge-success'
  return 'notification-badge-default'
}

const scopeTooltip = scope => {
  if (scope === 'system') return 'System notification'
  if (scope === 'user') return 'User notification'
  if (scope === 'release') return 'Release notification'
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

const inlineActionTooltipClass =
  'absolute -bottom-6 left-1/2 -translate-x-1/2 rounded px-2 py-1 text-xs whitespace-nowrap opacity-0 transition-opacity group-hover:opacity-100'

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

let userChannel = null
let systemChannel = null
let releaseChannel = null
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
  scheduleRefresh()
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
  scheduleRefresh()
}

const getPayloadId = payload =>
  payload?.id ?? payload?.notification_id ?? payload?.app_notification_id

const upsertIncoming = payload => {
  if (!getPayloadId(payload)) return
  scheduleRefresh()
}

const handleStateChanged = payload => {
  if (!getPayloadId(payload)) return
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

  releaseChannel = window.Echo.private('release')
    .listen('.app-notification.created', upsertIncoming)
    .listen('.app-notification.state', handleStateChanged)
    .listen('.app-notification.bulk', handleBulkChanged)
}

const unsubscribeRealtime = () => {
  const userId = page.props?.auth?.user?.id
  if (!window.Echo || !userId) return

  window.Echo.leave(`private-users.${userId}`)
  window.Echo.leave('private-system')
  window.Echo.leave('private-release')

  userChannel = null
  systemChannel = null
  releaseChannel = null
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
              <div v-if="selectedCount > 0" class="flex items-center gap-6">
                <span
                  role="status"
                  class="flex items-center gap-1.5 text-xs font-medium text-[var(--color-text)]">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="h-4 w-4 text-green-600 dark:text-green-500">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                  </svg>
                  {{ selectedCount }} selected
                </span>
              </div>
            </div>
          </div>

          <div class="w-full sm:w-auto">
            <div
              class="xs:gap-0 grid w-full grid-cols-2 gap-1 overflow-visible rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] sm:inline-grid sm:grid-cols-5">
              <button
                type="button"
                class="btn btn-xs text-xxs btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none rounded-l-lg border-0 sm:text-xs"
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
                class="btn btn-xs text-xxs btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 max-sm:rounded-r-lg sm:text-xs"
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
                class="btn btn-xs text-xxs btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 sm:text-xs"
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
                class="btn btn-xs text-xxs btn-secondary group relative inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 sm:text-xs"
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
                class="btn btn-xs text-xxs btn-secondary group relative col-span-2 inline-flex items-center justify-center gap-2 overflow-visible rounded-none border-0 max-sm:rounded-b sm:col-span-1 sm:rounded-r-lg sm:text-xs"
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
                <div
                  v-for="row in displayedRows"
                  :key="row.id"
                  class="p-4"
                  :class="{
                    'border-l-4': true,
                    'bg-blue-50/50 dark:bg-blue-900/20': !row.is_read && !row.is_dismissed,
                    'bg-gray-50/50 font-light opacity-70 dark:bg-gray-900/90': row.is_dismissed,
                    'bg-red-500/10 dark:bg-red-500/30':
                      (row.priority === 'critical' || row.type === 'danger') &&
                      !row.is_read &&
                      !row.is_dismissed,
                    'border-red-500': row.priority === 'critical',
                    'border-red-500': row.type === 'danger',
                    'border-yellow-500': row.priority === 'high',
                    'border-blue-500': row.priority === 'normal',
                    'border-blue-500': row.type === 'info',
                    'border-gray-500': row.priority === 'low',
                    'border-green-600': row.scope === 'release',
                  }">
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
                          <div
                            class="flex items-center justify-start gap-1 font-medium text-[var(--color-text)]">
                            <div
                              v-if="!row.is_read && !row.is_dismissed"
                              class="mt-0 -mr-0.5 -ml-1 flex h-6 w-6 items-center justify-center rounded not-hover:animate-pulse">
                              <div
                                class="h-2.5 w-2.5 rounded-full bg-blue-500"
                                aria-hidden="true"></div>
                            </div>
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
                            viewBox="0 0 288 288"
                            fill="none"
                            aria-hidden="true"
                            class="size-3.5 text-[var(--color-text-muted)]">
                            <path
                              d="M232.213 29.661a6.75 6.75 0 0 1 8.659 4.019 293.104 293.104 0 0 1 4.671 13.82 293.554 293.554 0 0 1 12.249 63.562c6.142 6.107 9.958 14.579 9.958 23.938 0 9.359-3.816 17.831-9.958 23.938a293.551 293.551 0 0 1-12.249 63.562 293.143 293.143 0 0 1-4.671 13.82 6.75 6.75 0 0 1-12.678-4.64c.937-2.56 1.838-5.137 2.702-7.731a279.258 279.258 0 0 0-88.553-26.124 207.662 207.662 0 0 0 8.709 22.888c4.285 9.53 1.151 21.268-8.338 26.747l-7.875 4.547c-9.831 5.675-22.847 2.225-27.825-8.542a256.906 256.906 0 0 1-16.74-48.337C60.857 190.897 38.25 165.588 38.25 135c0-33.551 27.199-60.75 60.75-60.75h9c8.258 0 16.431-.356 24.505-1.052 35.031-3.023 68.22-12.466 98.391-27.147a278.666 278.666 0 0 0-2.702-7.73 6.75 6.75 0 0 1 4.019-8.66Zm2.681 29.45a292.862 292.862 0 0 1-96.423 27.083c-3.74 15.652-5.721 31.994-5.721 48.806 0 16.812 1.981 33.154 5.721 48.806a292.884 292.884 0 0 1 96.423 27.083 280.39 280.39 0 0 0 9.636-55.608c.477-6.697.72-13.46.72-20.281 0-6.821-.243-13.584-.72-20.281a280.396 280.396 0 0 0-9.636-55.608ZM124.37 182.697A223.556 223.556 0 0 1 119.25 135c0-16.365 1.766-32.325 5.12-47.697a299.37 299.37 0 0 1-16.37.447h-9c-26.096 0-47.25 21.155-47.25 47.25S72.904 182.25 99 182.25h9c5.492 0 10.95.15 16.37.447Zm-20.039 13.053a243.387 243.387 0 0 0 14.937 42.049c1.434 3.103 5.418 4.481 8.821 2.516l7.875-4.547c3.054-1.763 4.429-5.84 2.775-9.519a221.156 221.156 0 0 1-10.907-29.811A285.523 285.523 0 0 0 108 195.75h-3.669Z"
                              fill="currentColor"
                              stroke="currentColor"
                              stroke-width="2"></path>
                            <g mask="url(#cc)">
                              <path
                                opacity=".1"
                                d="M-69 287.5h445"
                                stroke="url(#e)"
                                stroke-width="1.5"></path>
                              <path
                                opacity=".1"
                                d="M-69 0h445"
                                stroke="url(#f)"
                                stroke-width="1.5"></path>
                              <path
                                opacity=".1"
                                d="M.25 355V-90"
                                stroke="url(#g)"
                                stroke-width="1.5"></path>
                              <path
                                opacity=".1"
                                d="M287.75 355V-90"
                                stroke="url(#h)"
                                stroke-width="1.5"></path>
                            </g>
                            <defs>
                              <linearGradient
                                id="e"
                                x1="375.75"
                                y1="296.754"
                                x2="-69.25"
                                y2="296.754"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0F172A" stop-opacity="0"></stop>
                                <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset=".521" stop-color="#0F172A"></stop>
                                <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                              </linearGradient>
                              <linearGradient
                                id="f"
                                x1="375.75"
                                y1="9.254"
                                x2="-69.25"
                                y2="9.254"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0F172A" stop-opacity="0"></stop>
                                <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset=".521" stop-color="#0F172A"></stop>
                                <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                              </linearGradient>
                              <linearGradient
                                id="g"
                                x1="9.504"
                                y1="-89.75"
                                x2="9.504"
                                y2="355.25"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0F172A" stop-opacity="0"></stop>
                                <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset=".521" stop-color="#0F172A"></stop>
                                <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                              </linearGradient>
                              <linearGradient
                                id="h"
                                x1="297.004"
                                y1="-89.75"
                                x2="297.004"
                                y2="355.25"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#0F172A" stop-opacity="0"></stop>
                                <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset=".521" stop-color="#0F172A"></stop>
                                <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                                <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                              </linearGradient>
                              <mask
                                id="cc"
                                style="mask-type: alpha"
                                maskUnits="userSpaceOnUse"
                                x="-333"
                                y="-242"
                                width="956"
                                height="927">
                                <path fill="url(#b)" d="M-333-242h956v927h-956z"></path>
                              </mask>
                            </defs>
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
                          <span class="text-xxs font-medium uppercase">
                            {{ row.scope || 'notification' }}
                          </span>
                        </span>

                        <span
                          class="text-xxs inline-flex items-center gap-1 rounded-full border border-[var(--color-border)] bg-[var(--color-bg)] px-2 py-1 uppercase">
                          <span class="font-medium">Read:</span>
                          <span>{{ row.is_read ? 'Yes' : 'No' }}</span>
                        </span>

                        <span
                          class="text-xxs inline-flex items-center gap-1 rounded-full border border-[var(--color-border)] bg-[var(--color-bg)] px-2 py-1 uppercase">
                          <span class="font-medium">Dismissed:</span>
                          <span>{{ row.is_dismissed ? 'Yes' : 'No' }}</span>
                        </span>

                        <span
                          class="flex w-full items-center gap-1.5 text-[var(--color-text-muted)]">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="mt-0.25 size-3">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                          </svg>
                          {{ createdDisplay(row.created_at) }}
                        </span>
                      </div>

                      <div class="mt-3">
                        <div
                          class="grid w-full grid-cols-3 overflow-hidden rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)]">
                          <button
                            type="button"
                            class="btn btn-xs btn-secondary inline-flex items-center justify-center gap-2 rounded-none border-0"
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
                            class="btn btn-xs btn-secondary inline-flex items-center justify-center gap-2 rounded-none border-0"
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
                            class="btn btn-xs btn-secondary inline-flex items-center justify-center gap-2 rounded-none border-0"
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

            <div class="hidden w-full overflow-x-auto sm:flex">
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

                    <th class="p-3">
                      <button
                        type="button"
                        class="inline-flex items-center gap-1 hover:text-[var(--color-text)]"
                        @click="toggleTableSort('title')">
                        <span>Notification</span>

                        <span
                          v-if="isTableSortedBy('title')"
                          class="text-[var(--color-text-muted)]">
                          <svg
                            v-if="tableSortDir === 'asc'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                          </svg>
                        </span>
                      </button>
                    </th>

                    <th class="w-20 p-3 text-center">
                      <button
                        type="button"
                        class="inline-flex items-center justify-center gap-1 hover:text-[var(--color-text)]"
                        @click="toggleTableSort('scope')">
                        <span>Scope</span>

                        <span
                          v-if="isTableSortedBy('scope')"
                          class="text-[var(--color-text-muted)]">
                          <svg
                            v-if="tableSortDir === 'asc'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                          </svg>
                        </span>
                      </button>
                    </th>

                    <th class="w-24 p-3 text-center">
                      <button
                        type="button"
                        class="inline-flex items-center justify-center gap-1 hover:text-[var(--color-text)]"
                        @click="toggleTableSort('type')">
                        <span>Type</span>

                        <span v-if="isTableSortedBy('type')" class="text-[var(--color-text-muted)]">
                          <svg
                            v-if="tableSortDir === 'asc'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                          </svg>
                        </span>
                      </button>
                    </th>

                    <th class="w-20 p-3 text-center">
                      <button
                        type="button"
                        class="inline-flex items-center justify-center gap-1 hover:text-[var(--color-text)]"
                        @click="toggleTableSort('read')">
                        <span>Read</span>

                        <span v-if="isTableSortedBy('read')" class="text-[var(--color-text-muted)]">
                          <svg
                            v-if="tableSortDir === 'asc'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                          </svg>
                        </span>
                      </button>
                    </th>

                    <th class="w-28 p-3 text-center">
                      <button
                        type="button"
                        class="inline-flex items-center justify-center gap-1 hover:text-[var(--color-text)]"
                        @click="toggleTableSort('dismissed')">
                        <span>Dismissed</span>

                        <span
                          v-if="isTableSortedBy('dismissed')"
                          class="text-[var(--color-text-muted)]">
                          <svg
                            v-if="tableSortDir === 'asc'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                          </svg>
                        </span>
                      </button>
                    </th>

                    <th class="w-40 p-3 text-right">Actions</th>
                  </tr>
                </thead>

                <tbody class="divide-y divide-[var(--color-border)]">
                  <tr
                    v-for="row in displayedRows"
                    :key="row.id"
                    class="transition-colors hover:bg-[var(--color-surface-muted)]"
                    :class="{
                      'border-l-4': true,
                      'bg-blue-50/50 dark:bg-blue-900/20': !row.is_read && !row.is_dismissed,
                      'bg-gray-50/50 font-light opacity-70 dark:bg-gray-900/90': row.is_dismissed,
                      'bg-red-500/10 dark:bg-red-500/30':
                        (row.priority === 'critical' || row.type === 'danger') &&
                        !row.is_read &&
                        !row.is_dismissed,
                      'border-red-500': row.priority === 'critical',
                      'border-red-500': row.type === 'danger',
                      'border-yellow-500': row.priority === 'high',
                      'border-blue-500': row.priority === 'normal',
                      'border-blue-500': row.type === 'info',
                      'border-gray-500': row.priority === 'low',
                      'border-green-600': row.scope === 'release',
                    }">
                    <td class="p-3 align-middle">
                      <input
                        type="checkbox"
                        :checked="selected.has(row.id)"
                        @change="toggleRow(row.id)" />
                    </td>

                    <td class="p-3 align-middle">
                      <div class="flex items-start gap-3">
                        <div class="min-w-0">
                          <div
                            class="flex items-center justify-start gap-1 font-medium text-[var(--color-text)]">
                            <div
                              v-if="!row.is_read && !row.is_dismissed"
                              class="mt-0 -mr-0.5 -ml-1 flex h-6 w-6 items-center justify-center rounded not-hover:animate-pulse">
                              <div
                                class="h-2.5 w-2.5 rounded-full bg-blue-500"
                                aria-hidden="true"></div>
                            </div>
                            {{ row.title || 'Notification' }}
                          </div>
                          <div class="mt-1 truncate text-[var(--color-text-muted)]">
                            {{ row.message }}
                          </div>
                          <div
                            class="mt-1 flex items-center gap-1 text-xs text-[var(--color-text-muted)]">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke-width="1.5"
                              stroke="currentColor"
                              class="mt-0.25 size-3">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ createdDisplay(row.created_at) }}
                          </div>
                        </div>
                      </div>
                    </td>

                    <td class="p-3 text-center align-middle">
                      <span class="group relative flex flex-col items-center gap-1">
                        <svg
                          v-if="scopeIconName(row.scope) === 'user'"
                          class="size-5 text-[var(--color-text-muted)]"
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
                          class="size-5 text-[var(--color-text-muted)]"
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
                          viewBox="0 0 288 288"
                          fill="none"
                          aria-hidden="true"
                          class="size-5.5 text-[var(--color-text-muted)]">
                          <path
                            d="M232.213 29.661a6.75 6.75 0 0 1 8.659 4.019 293.104 293.104 0 0 1 4.671 13.82 293.554 293.554 0 0 1 12.249 63.562c6.142 6.107 9.958 14.579 9.958 23.938 0 9.359-3.816 17.831-9.958 23.938a293.551 293.551 0 0 1-12.249 63.562 293.143 293.143 0 0 1-4.671 13.82 6.75 6.75 0 0 1-12.678-4.64c.937-2.56 1.838-5.137 2.702-7.731a279.258 279.258 0 0 0-88.553-26.124 207.662 207.662 0 0 0 8.709 22.888c4.285 9.53 1.151 21.268-8.338 26.747l-7.875 4.547c-9.831 5.675-22.847 2.225-27.825-8.542a256.906 256.906 0 0 1-16.74-48.337C60.857 190.897 38.25 165.588 38.25 135c0-33.551 27.199-60.75 60.75-60.75h9c8.258 0 16.431-.356 24.505-1.052 35.031-3.023 68.22-12.466 98.391-27.147a278.666 278.666 0 0 0-2.702-7.73 6.75 6.75 0 0 1 4.019-8.66Zm2.681 29.45a292.862 292.862 0 0 1-96.423 27.083c-3.74 15.652-5.721 31.994-5.721 48.806 0 16.812 1.981 33.154 5.721 48.806a292.884 292.884 0 0 1 96.423 27.083 280.39 280.39 0 0 0 9.636-55.608c.477-6.697.72-13.46.72-20.281 0-6.821-.243-13.584-.72-20.281a280.396 280.396 0 0 0-9.636-55.608ZM124.37 182.697A223.556 223.556 0 0 1 119.25 135c0-16.365 1.766-32.325 5.12-47.697a299.37 299.37 0 0 1-16.37.447h-9c-26.096 0-47.25 21.155-47.25 47.25S72.904 182.25 99 182.25h9c5.492 0 10.95.15 16.37.447Zm-20.039 13.053a243.387 243.387 0 0 0 14.937 42.049c1.434 3.103 5.418 4.481 8.821 2.516l7.875-4.547c3.054-1.763 4.429-5.84 2.775-9.519a221.156 221.156 0 0 1-10.907-29.811A285.523 285.523 0 0 0 108 195.75h-3.669Z"
                            fill="currentColor"
                            stroke="currentColor"
                            stroke-width="2"></path>
                          <g mask="url(#cc)">
                            <path
                              opacity=".1"
                              d="M-69 287.5h445"
                              stroke="url(#e)"
                              stroke-width="1.5"></path>
                            <path
                              opacity=".1"
                              d="M-69 0h445"
                              stroke="url(#f)"
                              stroke-width="1.5"></path>
                            <path
                              opacity=".1"
                              d="M.25 355V-90"
                              stroke="url(#g)"
                              stroke-width="1.5"></path>
                            <path
                              opacity=".1"
                              d="M287.75 355V-90"
                              stroke="url(#h)"
                              stroke-width="1.5"></path>
                          </g>
                          <defs>
                            <linearGradient
                              id="e"
                              x1="375.75"
                              y1="296.754"
                              x2="-69.25"
                              y2="296.754"
                              gradientUnits="userSpaceOnUse">
                              <stop stop-color="#0F172A" stop-opacity="0"></stop>
                              <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset=".521" stop-color="#0F172A"></stop>
                              <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                            </linearGradient>
                            <linearGradient
                              id="f"
                              x1="375.75"
                              y1="9.254"
                              x2="-69.25"
                              y2="9.254"
                              gradientUnits="userSpaceOnUse">
                              <stop stop-color="#0F172A" stop-opacity="0"></stop>
                              <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset=".521" stop-color="#0F172A"></stop>
                              <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                            </linearGradient>
                            <linearGradient
                              id="g"
                              x1="9.504"
                              y1="-89.75"
                              x2="9.504"
                              y2="355.25"
                              gradientUnits="userSpaceOnUse">
                              <stop stop-color="#0F172A" stop-opacity="0"></stop>
                              <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset=".521" stop-color="#0F172A"></stop>
                              <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                            </linearGradient>
                            <linearGradient
                              id="h"
                              x1="297.004"
                              y1="-89.75"
                              x2="297.004"
                              y2="355.25"
                              gradientUnits="userSpaceOnUse">
                              <stop stop-color="#0F172A" stop-opacity="0"></stop>
                              <stop offset=".258" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset=".521" stop-color="#0F172A"></stop>
                              <stop offset=".784" stop-color="#0F172A" stop-opacity=".6"></stop>
                              <stop offset="1" stop-color="#0F172A" stop-opacity="0"></stop>
                            </linearGradient>
                            <mask
                              id="cc"
                              style="mask-type: alpha"
                              maskUnits="userSpaceOnUse"
                              x="-333"
                              y="-242"
                              width="956"
                              height="927">
                              <path fill="url(#b)" d="M-333-242h956v927h-956z"></path>
                            </mask>
                          </defs>
                        </svg>

                        <svg
                          v-else
                          class="size-4.5 text-[var(--color-text-muted)]"
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

                        <span class="text-xxxs block font-bold uppercase">
                          {{ row.scope }}
                        </span>

                        <span :class="inlineActionTooltipClass" :style="tooltipStyle">
                          {{ scopeTooltip(row.scope) }}
                        </span>
                      </span>
                    </td>

                    <td class="p-3 text-center align-middle">
                      <span
                        class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium"
                        :class="typeBadgeClass(row.type)">
                        {{ typeLabel(row.type) }}
                      </span>
                    </td>

                    <td class="p-3 text-center align-middle">
                      <span class="group relative inline-flex items-center justify-center">
                        <button
                          type="button"
                          class="flex cursor-pointer flex-col items-center gap-0.75 rounded-lg px-2 py-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                          :disabled="isWorking"
                          @click="row.is_read ? markUnread(row) : markRead(row)">
                          <svg
                            v-if="readIconName(row.is_read) === 'check'"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4.5 text-[var(--color-text-muted)]">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z" />
                          </svg>
                          <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4.5 text-[var(--color-text-muted)]">
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                          </svg>
                          <span class="text-xxxs block font-bold uppercase">
                            {{ readTooltip(row.is_read) }}
                          </span>
                          <span :class="inlineActionTooltipClass" :style="tooltipStyle">
                            {{ row.is_read ? 'Mark unread' : 'Mark read' }}
                          </span>
                        </button>
                      </span>
                    </td>

                    <td class="p-3 text-center align-middle">
                      <span class="group relative inline-flex items-center justify-center">
                        <button
                          type="button"
                          class="flex cursor-pointer flex-col items-center gap-0.75 rounded-lg px-2 py-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
                          :disabled="isWorking"
                          @click="row.is_dismissed ? undismiss(row) : dismiss(row)">
                          <svg
                            v-if="row.is_dismissed"
                            xmlns="http://www.w3.org/2000/svg"
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
                            xmlns="http://www.w3.org/2000/svg"
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
                          <span class="text-xxxs block font-bold uppercase">
                            {{ dismissedTooltip(row.is_dismissed) }}
                          </span>
                          <span :class="inlineActionTooltipClass" :style="tooltipStyle">
                            {{ row.is_dismissed ? 'Undismiss' : 'Dismiss' }}
                          </span>
                        </button>
                      </span>
                    </td>

                    <td class="overflow-visible p-3 align-middle">
                      <div class="flex justify-end overflow-visible">
                        <div
                          class="inline-flex overflow-visible rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)]">
                          <button
                            type="button"
                            class="group relative cursor-pointer rounded-l-lg px-2 py-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
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
