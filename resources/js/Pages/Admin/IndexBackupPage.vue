<script setup>
import Button from '@/Components/Button.vue'
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import { ArrowDownTrayIcon, TrashIcon } from '@heroicons/vue/24/outline'

defineOptions({
    layout: Default,
})

defineProps({
    backupInfo: {
        type: Array,
        required: true,
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
        onFinish: () => { isBackupRunning.value = false },
    })
}

const downloadBackup = path => {
    if (!path || !path.match(/\.(zip|gz|sql)$/i)) return
    window.location.href = `/admin/backup/download/${window.btoa(path.trim())}`
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
    form.delete(route('admin.backup.destroy', { path: window.btoa(selectedBackup.value.path.trim()) }), {
        preserveScroll: true,
        onFinish: () => closeDeleteModal(),
    })
}

const actionBtnClass = 'cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground'
</script>

<template>
    <Head title="Data Backups" />

    <main class="mx-auto max-w-7xl" aria-labelledby="system-backups">
        <PageHeader
            title="Data Backups"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Data Backups' },
            ]">
            <template #actions>
                <Button variant="primary" size="sm" :disabled="isBackupRunning" :aria-busy="isBackupRunning" @click="runBackup">
                    {{ isBackupRunning ? 'Creating...' : 'Create backup' }}
                </Button>
            </template>
        </PageHeader>

        <div class="space-y-4">
            <Alert type="info">
                If backup fails, run manually:
                <code class="ml-1 rounded bg-muted px-1.5 py-0.5 font-mono text-xs">php artisan backup:run</code>
            </Alert>

            <section v-for="info in backupInfo" :key="info.name">
                <!-- Summary -->
                <div class="mb-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-xs text-muted-foreground">
                    <span>Disk: <span class="font-medium text-foreground">{{ info.disk }}</span></span>
                    <span>Storage: <span class="font-medium text-foreground">{{ info.storageType }}</span></span>
                    <span>Used: <span class="font-medium text-foreground">{{ info.storageSpace }}</span></span>
                    <span class="flex items-center gap-1.5">
                        <span :class="['h-1.5 w-1.5 rounded-full', info.healthy ? 'bg-green-500' : 'bg-red-500']"></span>
                        {{ info.healthy ? 'Healthy' : 'Needs attention' }}
                    </span>
                    <span>{{ info.count }} {{ info.count === 1 ? 'backup' : 'backups' }}</span>
                </div>

                <!-- Backups list -->
                <div v-if="info.backups?.length > 0" class="card divide-y divide-border overflow-hidden">
                    <div
                        v-for="backup in info.backups"
                        :key="backup.path"
                        class="flex items-center justify-between gap-4 px-4 py-3 transition-colors hover:bg-muted">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline gap-3">
                                <span class="text-sm font-medium text-foreground">{{ backup.date }}</span>
                                <span class="text-xs tabular-nums text-muted-foreground">{{ backup.size }}</span>
                            </div>
                            <p class="mt-0.5 truncate font-mono text-[11px] text-muted-foreground">{{ backup.path }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-0.5">
                            <button
                                type="button"
                                :class="actionBtnClass"
                                title="Download"
                                :disabled="!backup.path"
                                @click="downloadBackup(backup.path)">
                                <ArrowDownTrayIcon class="h-3.5 w-3.5" />
                            </button>
                            <button
                                type="button"
                                :class="actionBtnClass + ' hover:text-red-600! dark:hover:text-red-400!'"
                                title="Delete"
                                :disabled="!backup.path"
                                @click="confirmDelete(backup)">
                                <TrashIcon class="h-3.5 w-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <p v-else class="card px-5 py-8 text-center text-sm text-muted-foreground">
                    No backups found
                </p>
            </section>
        </div>
    </main>

    <Modal :show="showDeleteModal" size="md" @close="closeDeleteModal">
        <template #title>Delete backup</template>
        <template #default>
            <p class="text-sm text-muted-foreground">
                This backup will be permanently deleted. This cannot be undone.
            </p>
            <p v-if="selectedBackup" class="mt-2 truncate rounded-md bg-muted px-3 py-2 font-mono text-xs text-muted-foreground">
                {{ selectedBackup.path }}
            </p>
        </template>
        <template #footer>
            <div class="flex items-center justify-end gap-3">
                <Button variant="secondary" size="sm" :disabled="form.processing" @click="closeDeleteModal">
                    Cancel
                </Button>
                <Button variant="danger" size="sm" :disabled="form.processing" @click="deleteBackup">
                    {{ form.processing ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
