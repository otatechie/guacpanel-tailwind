<template>

    <Head title="Backups" />

    <div class="w-full mx-auto max-w-7xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <h2 class="sub-heading">Backup Management</h2>
            <button @click="runBackup" :disabled="isBackupRunning"
                class="w-full sm:w-auto inline-flex items-center btn-primary">
                {{ isBackupRunning ? 'Creating backup...' : 'Create new backup' }}
            </button>
        </div>

        <div class="container-border p-3 sm:p-4 md:p-6">
            <div v-for="info in backupInfo" :key="info.name" class="space-y-4 sm:space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3">
                    <div class="bg-gray-100 p-3 sm:p-4 rounded">
                        <div class="text-xs sm:text-sm text-gray-600">Disk</div>
                        <div class="text-sm sm:text-base text-gray-600 font-medium capitalize font-mono">{{ info.disk }}
                        </div>
                    </div>
                    <div class="bg-gray-100 p-3 sm:p-4 rounded">
                        <div class="text-xs sm:text-sm text-gray-600">Storage type</div>
                        <div class="text-sm sm:text-base text-gray-600 font-medium capitalize font-mono">{{
                            info.storageType }}</div>
                    </div>
                    <div class="bg-gray-100 p-3 sm:p-4 rounded">
                        <div class="text-xs sm:text-sm text-gray-600">Used space</div>
                        <div class="text-sm sm:text-base text-gray-600 font-medium font-mono">{{ info.storageSpace }}
                        </div>
                    </div>
                    <div class="md:col-span-2 lg:col-span-1 bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-100">
                        <div class="text-xs font-medium text-gray-500 mb-1">Status</div>
                        <div class="flex items-center space-x-2">
                            <i :class="info.healthy ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'"
                                class="text-lg"></i>
                            <span :class="info.healthy ? 'text-green-600 font-mono' : 'text-red-600 font-mono'"
                                class="font-medium">
                                {{ info.healthy ? 'Normal' : 'Attention required' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="info.backups.length > 0" class="border border-gray-300 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 md:table hidden">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Size
                                    </th>
                                    <th
                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Name
                                    </th>
                                    <th
                                        class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="backup in info.backups" :key="backup.path"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2 whitespace-nowrap text-xs sm:text-sm">
                                        {{ backup.date }}
                                    </td>
                                    <td
                                        class="px-3 py-2 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                        {{ backup.size }}
                                    </td>
                                    <td
                                        class="px-3 py-2 whitespace-nowrap text-xs sm:text-sm text-gray-500 font-mono truncate max-w-[120px] sm:max-w-[160px]">
                                        {{ backup.path }}
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-right">
                                        <button @click="downloadBackup(backup.path)"
                                            class="p-2 text-gray-600 hover:text-blue-600 rounded-md hover:bg-gray-100 transition-colors cursor-pointer"
                                            title="Download backup">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </button>
                                        <button @click="deleteBackup(backup.path)"
                                            class="p-2 text-gray-600 hover:text-red-600 rounded-md hover:bg-gray-100 transition-colors cursor-pointer"
                                            title="Delete backup">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden space-y-3 p-3">
                        <div v-for="backup in info.backups" :key="backup.path"
                            class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm font-medium text-gray-900"><strong>Date:</strong> {{ backup.date }}</p>
                            <p class="text-sm text-gray-700"><strong>Size:</strong> {{ backup.size }}</p>
                            <p class="text-xs text-gray-500 font-mono truncate max-w-full"><strong>File:</strong> {{
                                backup.path }}</p>
                            <div class="flex justify-end mt-2 space-x-2">
                                <button @click="downloadBackup(backup.path)"
                                    class="p-2 text-gray-600 hover:text-blue-600 rounded-md hover:bg-gray-100 transition-colors"
                                    title="Download backup">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </button>
                                <button @click="deleteBackup(backup.path)"
                                    class="p-2 text-gray-600 hover:text-red-600 rounded-md hover:bg-gray-100 transition-colors"
                                    title="Delete backup">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center p-8 bg-gray-50 rounded-lg">
                    <p class="text-gray-400 text-sm">No backups found in this storage location</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Default from '../../Layouts/Default.vue';


defineOptions({
    layout: Default,
});

const props = defineProps({
    backupInfo: {
        type: Array,
        required: true,
    },
});

const isBackupRunning = ref(false);

const form = useForm({});

const runBackup = () => {
    if (isBackupRunning.value) return;

    isBackupRunning.value = true;

    form.post(route('backup.run'), {
        preserveScroll: true,
        onFinish: () => {
            isBackupRunning.value = false;
        },
    });
};

const downloadBackup = (fileName) => {
    const encodedPath = encodeURIComponent(fileName);
    window.open(route('backup.download', { path: encodedPath }));
};

const deleteBackup = (path) => {
    if (confirm('This backup will be permanently deleted. Continue?')) {
        form.delete(route('backup.delete', { path }), {
            preserveScroll: true,
            onSuccess: () => {
                // Add your own success notification implementation here
            }
        });
    }
};
</script>
