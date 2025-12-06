<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineOptions({
  layout: Default,
})

defineProps({
  backupInfo: {
    type: Array,
    required: true,
    validator: value =>
      value.every(
        info =>
          'disk' in info && 'storageType' in info && 'storageSpace' in info && 'healthy' in info
      ),
  },
})

const isBackupRunning = ref(false)
const showDeleteModal = ref(false)
const selectedBackup = ref(null)
const form = useForm({})

const runBackup = () => {
  if (isBackupRunning.value) return

  isBackupRunning.value = true

  form.post(route('admin.backup.create'), {
    preserveScroll: true,
    onFinish: () => {
      isBackupRunning.value = false
    },
  })
}

const downloadBackup = fileName => {
  if (!fileName || typeof fileName !== 'string') return
  if (!fileName.match(/\.(zip|gz|sql)$/i)) return

  const encodedPath = window.btoa(fileName.trim())
  window.location.href = `/admin/backup/download/${encodedPath}`
}

const confirmDelete = backup => {
  selectedBackup.value = backup
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedBackup.value = null
}

const deleteBackup = () => {
  if (!selectedBackup.value?.path) return

  const encodedPath = window.btoa(selectedBackup.value.path.trim())

  form.delete(route('admin.backup.destroy', { path: encodedPath }), {
    preserveScroll: true,
    onFinish: () => {
      closeDeleteModal()
    },
  })
}

const getStats = info => {
  if (!info) return []

  return [
    {
      type: 'disk',
      title: 'Disk',
      value: info.disk || 'Unknown',
      icon: 'database',
      iconColor: 'text-purple-500 dark:text-purple-400',
    },
    {
      type: 'storage',
      title: 'Storage Type',
      value: info.storageType || 'Unknown',
      icon: 'server',
      iconColor: 'text-blue-500 dark:text-blue-400',
    },
    {
      type: 'space',
      title: 'Used Space',
      value: info.storageSpace || 'Unknown',
      icon: 'hard-drive',
      iconColor: 'text-indigo-500 dark:text-indigo-400',
    },
    {
      type: 'status',
      title: 'Status',
      value: info.healthy ? 'Normal' : 'Attention Required',
      icon: info.healthy ? 'check-circle' : 'alert-circle',
      iconColor: info.healthy
        ? 'text-emerald-500 dark:text-emerald-400'
        : 'text-red-500 dark:text-red-400',
      status: info.healthy ? 'success' : 'error',
    },
  ]
}
</script>

<template>
  <Head title="Data Backups" />

  <main class="mx-auto max-w-7xl" aria-labelledby="system-backups">
    <div class="container-border">
      <PageHeader
        title="Data Backups"
        description="Manage system backups and restore points"
        :breadcrumbs="[
          { label: 'Dashboard', href: route('dashboard') },
          { label: 'System Settings', href: route('admin.setting.index') },
          { label: 'Data Backups' },
        ]">
        <template #actions>
          <button
            class="btn btn-sm btn-primary"
            :disabled="isBackupRunning"
            :aria-busy="isBackupRunning"
            @click="runBackup">
            {{ isBackupRunning ? 'Creating backup...' : 'Create Backup' }}
          </button>
        </template>
      </PageHeader>

      <section class="bg-[var(--color-bg)] p-6">
        <div
          class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-4 shadow-sm sm:p-6">
          <Alert type="warning">
            If the backup process fails, you can run the command manually:
            <span class="font-mono">php artisan backup:run</span>
          </Alert>

          <section v-for="info in backupInfo" :key="info.name" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <article
                v-for="(stat, index) in getStats(info)"
                :key="index"
                class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-4">
                <div class="mb-1 flex items-center gap-3">
                  <svg
                    v-if="stat.icon === 'database'"
                    :class="[stat.iconColor, 'h-5 w-5']"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                  </svg>
                  <svg
                    v-if="stat.icon === 'server'"
                    :class="[stat.iconColor, 'h-5 w-5']"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                  </svg>
                  <svg
                    v-if="stat.icon === 'hard-drive'"
                    :class="[stat.iconColor, 'h-5 w-5']"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                  </svg>
                  <svg
                    v-if="stat.icon === 'check-circle'"
                    :class="[stat.iconColor, 'h-5 w-5']"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg
                    v-if="stat.icon === 'alert-circle'"
                    :class="[stat.iconColor, 'h-5 w-5']"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    {{ stat.title }}
                  </h2>
                </div>
                <p
                  :class="[
                    stat.type === 'status'
                      ? info.healthy
                        ? 'text-emerald-600 dark:text-emerald-400'
                        : 'text-red-600 dark:text-red-400'
                      : 'text-gray-700 dark:text-gray-200',
                    'text-xl font-medium capitalize',
                  ]">
                  {{ stat.value }}
                </p>
              </article>
            </div>

            <!-- Desktop table -->
            <table
              v-if="info.backups?.length > 0"
              class="hidden w-full divide-y divide-[var(--color-border)] overflow-hidden rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] md:table">
              <thead class="bg-[var(--color-surface-muted)]">
                <tr>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                    Date
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                    Size
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                    File Name
                  </th>
                  <th
                    scope="col"
                    class="px-4 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[var(--color-border)]">
                <tr
                  v-for="backup in info.backups"
                  :key="backup.path"
                  class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                  <td class="px-4 py-3 text-sm whitespace-nowrap text-[var(--color-text)]">
                    {{ backup.date }}
                  </td>
                  <td
                    class="px-4 py-3 text-sm font-medium whitespace-nowrap text-[var(--color-text)]">
                    {{ backup.size }}
                  </td>
                  <td
                    class="max-w-[200px] truncate px-4 py-3 font-mono text-sm whitespace-nowrap text-[var(--color-text-muted)]">
                    {{ backup.path }}
                  </td>
                  <td class="space-x-1 px-4 py-3 text-right whitespace-nowrap">
                    <button
                      type="button"
                      class="cursor-pointer rounded-lg bg-blue-50 p-2 text-blue-500 transition-all duration-200 hover:scale-105 hover:bg-blue-100 hover:text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                      title="Download backup"
                      :disabled="!backup.path"
                      @click="downloadBackup(backup.path)">
                      <span class="sr-only">Download backup</span>
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                    </button>
                    <button
                      type="button"
                      class="cursor-pointer rounded-lg bg-red-50 p-2 text-red-500 transition-all duration-200 hover:scale-105 hover:bg-red-100 hover:text-red-600 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                      title="Delete backup"
                      :disabled="!backup.path"
                      @click="confirmDelete(backup)">
                      <span class="sr-only">Delete backup</span>
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Mobile cards -->
            <div
              v-if="info.backups?.length > 0"
              class="divide-y divide-[var(--color-border)] rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] md:hidden">
              <div
                v-for="backup in info.backups"
                :key="backup.path"
                class="space-y-2 p-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="flex items-start justify-between">
                  <div>
                    <p class="text-sm font-medium text-[var(--color-text)]">
                      {{ backup.date }}
                    </p>
                    <p class="text-sm text-[var(--color-text-muted)]">
                      {{ backup.size }}
                    </p>
                  </div>
                  <div class="flex space-x-1">
                    <button
                      class="cursor-pointer rounded-lg bg-blue-50 p-2 text-blue-500 transition-all duration-200 hover:scale-105 hover:bg-blue-100 hover:text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                      title="Download backup"
                      @click="downloadBackup(backup.path)">
                      <span class="sr-only">Download backup</span>
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                    </button>
                    <button
                      class="cursor-pointer rounded-lg bg-red-50 p-2 text-red-500 transition-all duration-200 hover:scale-105 hover:bg-red-100 hover:text-red-600 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                      title="Delete backup"
                      @click="confirmDelete(backup)">
                      <span class="sr-only">Delete backup</span>
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>
                <p class="truncate font-mono text-xs text-gray-500 dark:text-gray-400">
                  {{ backup.path }}
                </p>
              </div>
            </div>

            <p
              v-else
              class="rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-8 text-center text-[var(--color-text-muted)]">
              No backups found in this storage location
            </p>
          </section>
        </div>
      </section>
    </div>
  </main>

  <Modal :show="showDeleteModal" size="md" @close="closeDeleteModal">
    <template #title>
      <div class="flex items-center text-red-600">Delete Backup</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-[var(--color-text-muted)]">
          Are you sure you want to delete this backup? This action cannot be undone.
        </p>
        <Alert type="warning" title="File Details">
          <span class="font-mono font-medium">{{ selectedBackup?.path }}</span>
        </Alert>
      </div>
    </template>

    <template #footer>
      <div class="flex items-center justify-end gap-8">
        <button
          type="button"
          class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
          :disabled="form.processing"
          @click="closeDeleteModal">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary btn-sm"
          :disabled="form.processing"
          @click="deleteBackup">
          {{ form.processing ? 'Deleting...' : 'Yes, Delete Backup' }}
        </button>
      </div>
    </template>
  </Modal>
</template>
