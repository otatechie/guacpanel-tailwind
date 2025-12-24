<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch, h } from 'vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import { createColumnHelper } from '@tanstack/vue-table'

defineOptions({
  layout: Default,
})

const props = defineProps({
  sessions: {
    type: Object,
    required: true,
  },
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
  current_page: props.sessions.current_page,
  per_page: Number(props.sessions.per_page),
  total: props.sessions.total,
})

const showTerminateModal = ref(false)
const showTerminateAllModal = ref(false)
const selectedSession = ref(null)
const selectedUser = ref(null)
const form = useForm({})

const closeModal = () => {
  showTerminateModal.value = false
  showTerminateAllModal.value = false
  selectedSession.value = null
  selectedUser.value = null
  form.reset()
}

const confirmTerminate = session => {
  if (session.is_current) return
  selectedSession.value = session
  showTerminateModal.value = true
}

/* const terminateSession = () => {
    if (!selectedSession.value?.id) return

    form.delete(route('admin.sessions.destroy', { sessionId: selectedSession.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            showTerminateModal.value = false
            selectedSession.value = null
        },
        onError: () => {
            showTerminateModal.value = false
            selectedSession.value = null
        }
    })
}
 */

const columns = [
  columnHelper.accessor('user', {
    header: 'User',
    cell: info =>
      h('div', {}, [
        h('div', { class: 'font-medium' }, info.row.original.user.name),
        h('div', { class: 'text-sm text-gray-500' }, info.row.original.user.email),
      ]),
  }),
  columnHelper.accessor('device_info', {
    header: 'Browser & Device',
    cell: info => {
      const device = info.row.original.device_info
      return h('span', {}, `${device.browser} on ${device.platform} (${device.device})`)
    },
  }),
  columnHelper.accessor('last_active_diff', {
    header: 'Last Active',
    cell: info => h('span', info.getValue() || '-'),
  }),
  columnHelper.accessor('is_current', {
    header: 'Status',
    cell: info => {
      return h(
        'span',
        {
          class: info.row.original.is_current
            ? 'px-2 py-1 text-sm rounded-md bg-green-50 text-green-700 dark:bg-green-900/50 dark:text-green-400'
            : 'px-2 py-1 text-sm rounded-md bg-gray-50 text-gray-700 dark:bg-gray-900/50 dark:text-gray-400',
        },
        info.row.original.is_current ? 'Current' : 'Active'
      )
    },
  }),
  columnHelper.display({
    id: 'actions',
    header: 'Actions',
    cell: info => {
      const session = info.row.original
      if (!session?.id || session.is_current) return null

      const terminateButton = h(
        'button',
        {
          class:
            'p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
          onClick: () => confirmTerminate(session),
          type: 'button',
          title: 'Terminate session',
        },
        [
          h('span', { class: 'sr-only' }, 'Terminate session'),
          h(
            'svg',
            {
              class: 'h-4 w-4',
              fill: 'none',
              viewBox: '0 0 24 24',
              stroke: 'currentColor',
              'aria-hidden': 'true',
            },
            [
              h('path', {
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'stroke-width': '2',
                d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
              }),
            ]
          ),
        ]
      )

      return h(
        'div',
        {
          class: 'flex items-center gap-2 justify-end',
        },
        [terminateButton]
      )
    },
  }),
]

watch(
  pagination,
  newPagination => {
    loading.value = true
    router.get(
      route('admin.sessions.index'),
      {
        page: newPagination.current_page,
        per_page: Number(newPagination.per_page),
      },
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

  <Head title="Active Sessions" />

  <main class="main-container mx-auto max-w-7xl" aria-labelledby="active-sessions">
    <div class="container-border">
      <PageHeader title="Active Sessions" description="Manage system user sessions" :breadcrumbs="[
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'System Settings', href: route('admin.setting.index') },
        { label: 'Session Management' },
      ]" />

      <section class="bg-[var(--color-bg)] p-6">
        <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
          <Datatable :data="sessions.data" :columns="columns" :loading="loading" :pagination="pagination"
            empty-message="No active sessions found" empty-description="Active sessions will appear here"
            export-file-name="sessions" @update:pagination="pagination = $event" />
        </div>
      </section>
    </div>
  </main>

  <!-- Terminate Session Modal -->
  <Modal :show="showTerminateModal" size="md" @close="closeModal">
    <template #title>
      <div class="text-red-600 dark:text-red-400">Terminate Session</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-[var(--color-text-muted)]">
          Are you sure you want to terminate this session? The user will be logged out immediately.
        </p>
        <div v-if="selectedSession"
          class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-muted)] p-4">
          <h4 class="mb-2 text-sm font-medium text-[var(--color-text)]">Session details:</h4>
          <dl class="space-y-1">
            <div class="flex gap-2">
              <dt class="text-sm text-[var(--color-text-muted)]">User:</dt>
              <dd class="text-sm text-[var(--color-text)]">
                {{ selectedSession.user.name }}
              </dd>
            </div>
            <div class="flex gap-2">
              <dt class="text-sm text-[var(--color-text-muted)]">Device:</dt>
              <dd class="text-sm text-[var(--color-text)]">
                {{ selectedSession.device_info.browser }} on
                {{ selectedSession.device_info.platform }}
              </dd>
            </div>
          </dl>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
          @click="closeModal">
          Cancel
        </button>
        <button type="button" class="btn btn-danger btn-sm" :disabled="form.processing" @click="terminateSession">
          Yes, terminate session
        </button>
      </div>
    </template>
  </Modal>
</template>
