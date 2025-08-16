<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Default from '@/Layouts/Default.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';

defineOptions({
    layout: Default,
});

const props = defineProps({
    backupInfo: {
        type: Array,
        required: true,
        validator: (value) => {
            return value.every(info =>
                'disk' in info &&
                'storageType' in info &&
                'storageSpace' in info &&
                'healthy' in info
            );
        },
        default: () => [],
    },
});

const isBackupRunning = ref(false);
const showDeleteModal = ref(false);
const selectedBackup = ref(null);
const form = useForm({});

const runBackup = () => {
    if (isBackupRunning.value) return;

    isBackupRunning.value = true;
    form.post(route('admin.backup.create'), {
        preserveScroll: true,
        onError: () => {
            isBackupRunning.value = false;
        },
        onFinish: () => {
            isBackupRunning.value = false;
        },
    });
};

const downloadBackup = (fileName) => {
    if (!fileName || typeof fileName !== 'string') return;

    const encodedPath = btoa(fileName.trim());

    if (!fileName.match(/\.(zip|gz|sql)$/i)) return;

    window.location.href = `/admin/backup/download/${encodedPath}`;
};

const confirmDelete = (backup) => {
    selectedBackup.value = backup;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedBackup.value = null;
};

const deleteBackup = () => {
    if (!selectedBackup.value?.path) return;

    const encodedPath = btoa(selectedBackup.value.path.trim());
    form.delete(route('admin.backup.destroy', { path: encodedPath }), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
        },
        onError: (errors) => {
            closeDeleteModal();
        }
    });
};

const getStats = (info) => {
    if (!info) return [];

    return [
        {
            type: 'disk',
            title: 'Disk',
            value: info.disk || 'Unknown',
            icon: 'database',
            iconColor: 'text-purple-500 dark:text-purple-400'
        },
        {
            type: 'storage',
            title: 'Storage Type',
            value: info.storageType || 'Unknown',
            icon: 'server',
            iconColor: 'text-blue-500 dark:text-blue-400'
        },
        {
            type: 'space',
            title: 'Used Space',
            value: info.storageSpace || 'Unknown',
            icon: 'hard-drive',
            iconColor: 'text-indigo-500 dark:text-indigo-400'
        },
        {
            type: 'status',
            title: 'Status',
            value: info.healthy ? 'Normal' : 'Attention Required',
            icon: info.healthy ? 'check-circle' : 'alert-circle',
            iconColor: info.healthy ? 'text-emerald-500 dark:text-emerald-400' : 'text-red-500 dark:text-red-400',
            status: info.healthy ? 'success' : 'error'
        }
    ];
};
</script>


<template>

    <Head title="System Backups" />

    <main class="max-w-7xl mx-auto" aria-labelledby="system-backups">
        <div class="container-border">
            <PageHeader title="System Backups" description="Manage system backups and restore points" :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Settings', href: route('admin.setting.index') },
                { label: 'Backups' }
            ]">
                <template #actions>
                    <button @click="runBackup" class="btn btn-sm btn-primary gap-2" :disabled="isBackupRunning"
                        :aria-busy="isBackupRunning">
                        <svg v-if="isBackupRunning" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ isBackupRunning ? 'Creating backup...' : 'Create backup' }}
                    </button>
                </template>
            </PageHeader>

            <section class="p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                    <p
                        class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 text-sm text-amber-700 dark:text-amber-400 font-medium flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        If backup fails through the interface, try running
                        <span class="font-mono">php artisan backup:run</span>
                        in console
                    </p>

                    <section v-for="info in backupInfo" :key="info.name" class="space-y-6 mt-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <article v-for="(stat, index) in getStats(info)" :key="index"
                                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex items-center gap-3 mb-1">
                                    <svg v-if="stat.icon === 'database'" :class="[stat.iconColor, 'w-5 h-5']"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                    </svg>
                                    <svg v-if="stat.icon === 'server'" :class="[stat.iconColor, 'w-5 h-5']"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                    </svg>
                                    <svg v-if="stat.icon === 'hard-drive'" :class="[stat.iconColor, 'w-5 h-5']"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <svg v-if="stat.icon === 'check-circle'" :class="[stat.iconColor, 'w-5 h-5']"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-if="stat.icon === 'alert-circle'" :class="[stat.iconColor, 'w-5 h-5']"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ stat.title }}
                                    </h2>
                                </div>
                                <p :class="[
                                    stat.type === 'status' ?
                                        (info.healthy ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400') :
                                        'text-gray-700 dark:text-gray-200',
                                    'text-xl font-medium capitalize'
                                ]">{{ stat.value }}
                                </p>
                            </article>
                        </div>

                        <table v-if="info.backups?.length > 0"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden divide-y divide-gray-200 dark:divide-gray-700 md:table hidden">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Size</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        File Name</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="backup in info.backups" :key="backup.path"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        {{
                                            backup.date }}</td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ backup.size }}</td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono truncate max-w-[200px]">
                                        {{ backup.path }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right space-x-1">
                                        <button type="button" @click="downloadBackup(backup.path)"
                                            class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/50 transition-colors cursor-pointer"
                                            title="Download backup" :disabled="!backup.path">
                                            <span class="sr-only">Download backup</span>
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="confirmDelete(backup)"
                                            class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-md hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors cursor-pointer"
                                            title="Delete backup" :disabled="!backup.path">
                                            <span class="sr-only">Delete backup</span>
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="info.backups?.length > 0"
                            class="md:hidden divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div v-for="backup in info.backups" :key="backup.path"
                                class="p-4 space-y-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ backup.date
                                            }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ backup.size }}</p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button @click="downloadBackup(backup.path)"
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/50 transition-colors"
                                            title="Download backup">
                                            <i class="pi pi-download text-lg"></i>
                                        </button>
                                        <button @click="confirmDelete(backup)"
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-md hover:bg-red-50 dark:hover:bg-red-900/50 transition-colors"
                                            title="Delete backup">
                                            <i class="pi pi-trash text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate">{{ backup.path }}
                                </p>
                            </div>
                        </div>

                        <p v-else
                            class="text-center p-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                            No backups found in this storage location
                        </p>
                    </section>
                </div>
            </section>
        </div>
    </main>

    <Modal :show="showDeleteModal" @close="closeDeleteModal" size="md">
        <template #title>
            <div class="flex items-center gap-2 text-red-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Delete Backup
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this backup? This action cannot be undone and the backup file will
                    be permanently removed.
                </p>
                <div
                    class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            Backup file: <span class="font-mono font-medium">{{ selectedBackup?.path }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <button @click="closeDeleteModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer order-2 sm:order-1"
                    :disabled="form.processing">
                    Cancel
                </button>
                <button @click="deleteBackup" type="button" class="btn-danger order-1 sm:order-2"
                    :disabled="form.processing">
                    {{ form.processing ? 'Deleting...' : 'Yes, delete backup' }}
                </button>
            </div>
        </template>
    </Modal>
</template>
