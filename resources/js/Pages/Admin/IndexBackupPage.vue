<script setup>
import Button from '@/Components/Button.vue'
import { computed, ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import { DownloadIcon, Trash2Icon } from '@lucide/vue'
defineOptions({
    layout: Default,
})

const props = defineProps({
    backupInfo: {
        type: Array,
        required: true,
    },
})

const isOnlyBackup = computed(
    () => props.backupInfo.reduce((total, info) => total + (info.count ?? 0), 0) === 1
)

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

// base64url — plain base64 emits "/", which splits the route's path segment.
const encodePath = path => window.btoa(path.trim()).replace(/\+/g, '-').replace(/\//g, '_')

const downloadBackup = path => {
    if (!path || !path.match(/\.(zip|gz|sql)$/i)) return
    window.location.href = route('admin.backup.download', encodePath(path))
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
    form.delete(route('admin.backup.destroy', { path: encodePath(selectedBackup.value.path) }), {
        preserveScroll: true,
        onFinish: () => closeDeleteModal(),
    })
}

const actionBtnClass =
    'cursor-pointer rounded-md p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground'
</script>

<template>
    <Head title="Data backup" />

    <main class="mx-auto max-w-4xl" aria-labelledby="system-backups">
        <PageHeader
            title="Data backup"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Data backup' },
            ]">
            <template #actions>
                <Button
                    variant="primary"
                    size="sm"
                    :disabled="isBackupRunning"
                    :aria-busy="isBackupRunning"
                    @click="runBackup">
                    {{ isBackupRunning ? 'Creating...' : 'Create backup' }}
                </Button>
            </template>
        </PageHeader>

        <div class="space-y-8">
            <!-- Only surfaced when a disk actually needs attention; a permanent
                 banner for a failure that has not happened is just noise. -->
            <Alert v-if="backupInfo.some(i => !i.reachable)" type="warning">
                The backup disk could not be read, so existing backups cannot be listed. Check the
                disk configuration before creating another backup.
            </Alert>

            <section v-for="info in backupInfo" :key="info.name">
                <!-- Summary. Size and count are dropped while the list is empty —
                     "0 bytes" and "0 backups" only restate the empty state below. -->
                <div
                    class="text-muted-foreground mb-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-xs">
                    <span>
                        Disk:
                        <span class="text-foreground font-medium">{{ info.disk }}</span>
                    </span>
                    <template v-if="info.count > 0">
                        <span>{{ info.count }} {{ info.count === 1 ? 'backup' : 'backups' }}</span>
                        <span>
                            Using
                            <span class="text-foreground font-medium">{{ info.storageSpace }}</span>
                        </span>
                    </template>
                </div>

                <!-- Backups list -->
                <div v-if="info.backups?.length > 0" class="divide-border divide-y">
                    <div
                        v-for="backup in info.backups"
                        :key="backup.path"
                        class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline gap-3">
                                <span class="text-foreground text-sm font-medium">
                                    {{ backup.date }}
                                </span>
                                <span class="text-muted-foreground text-xs tabular-nums">
                                    {{ backup.size }}
                                </span>
                            </div>
                            <!-- The filename is the timestamp in another format, so it
                                 restates the line above. How old the backup is, is not. -->
                            <p class="text-muted-foreground mt-0.5 text-xs">{{ backup.age }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <button
                                type="button"
                                :class="actionBtnClass"
                                :aria-label="`Download backup from ${backup.date}`"
                                :disabled="!backup.path"
                                @click="downloadBackup(backup.path)">
                                <DownloadIcon class="h-3.5 w-3.5" aria-hidden="true" />
                            </button>
                            <button
                                type="button"
                                :class="
                                    actionBtnClass + ' hover:text-red-600! dark:hover:text-red-400!'
                                "
                                :aria-label="`Delete backup from ${backup.date}`"
                                :disabled="!backup.path"
                                @click="confirmDelete(backup)">
                                <Trash2Icon class="h-3.5 w-3.5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>

                <p v-else class="text-muted-foreground py-10 text-sm">
                    No backups yet. Each backup archives your database and the paths set in
                    <code class="text-foreground font-mono">config/backup.php</code>
                    as a single zip file on this disk.
                </p>
            </section>
        </div>
    </main>

    <Modal :show="showDeleteModal" size="sm" @close="closeDeleteModal">
        <template #title>Delete backup</template>

        <template #default>
            <!-- Lead with the record, then what happens to it. The filename is
                 the timestamp in another format, so it only repeats the line
                 above — it is not what the reader recognises. -->
            <p class="text-foreground text-sm font-medium">
                {{ selectedBackup?.date }}
                <span class="text-muted-foreground font-normal">· {{ selectedBackup?.size }}</span>
            </p>
            <p class="text-muted-foreground mt-2 text-sm">
                {{
                    isOnlyBackup
                        ? 'This is your only backup. Deleting it leaves nothing to restore from.'
                        : 'The file is removed from the disk immediately. This cannot be undone.'
                }}
            </p>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <Button
                    variant="secondary"
                    size="sm"
                    :disabled="form.processing"
                    @click="closeDeleteModal">
                    Cancel
                </Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="form.processing"
                    @click="deleteBackup">
                    {{ form.processing ? 'Deleting...' : 'Delete backup' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
