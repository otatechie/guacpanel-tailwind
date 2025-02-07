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
        default: () => [],
    },
});

const isBackupRunning = ref(false);
const form = useForm({});

const runBackup = () => {
    if (isBackupRunning.value) return;
    isBackupRunning.value = true;
    form.post(route('backup.run'), {
        preserveScroll: true,
        onError: () => isBackupRunning.value = false,
        onFinish: () => isBackupRunning.value = false,
    });
};

const downloadBackup = (fileName) => {
    if (!fileName) return;
    const encodedPath = encodeURIComponent(fileName.trim());
    window.open(route('backup.download', { path: encodedPath }));
};

const deleteBackup = (path) => {
    if (!path) return;
    if (confirm('This backup will be permanently deleted. Are you sure you want to continue?')) {
        form.delete(route('backup.delete', { path: path.trim() }), {
            preserveScroll: true,
        });
    }
};
</script>


<template>

    <Head title="Backups" />

    <main class="container-border max-w-5xl mx-auto p-6">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Backup Management</h1>
            </div>
            <button type="button" @click="runBackup" :disabled="isBackupRunning" :aria-busy="isBackupRunning"
                class="btn-primary">
                <svg v-if="isBackupRunning" class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                {{ isBackupRunning ? 'Creating backup...' : 'Create new backup' }}
            </button>
        </header>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-amber-700 font-medium flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                If backup fails through the interface, try running <code class="bg-amber-100 px-1.5 py-0.5 rounded">php artisan backup:run</code> in console
            </p>
        </div>

        <section v-for="info in backupInfo" :key="info.name" class="space-y-4 sm:space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <article class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center gap-3 mb-1">
                        <svg class="w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <h2 class="text-sm font-medium text-gray-600">Disk</h2>
                    </div>
                    <p class="text-xl font-semibold text-gray-900 capitalize">{{ info.disk }}</p>
                </article>

                <article class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center gap-3 mb-1">
                        <svg class="w-4 h-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                        <h2 class="text-sm font-medium text-gray-600">Storage Type</h2>
                    </div>
                    <p class="text-xl font-semibold text-gray-900 capitalize">{{ info.storageType }}</p>
                </article>

                <article class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center gap-3 mb-1">
                        <svg class="w-4 h-4 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h2 class="text-sm font-medium text-gray-600">Used Space</h2>
                    </div>
                    <p class="text-xl font-semibold text-gray-900">{{ info.storageSpace }}</p>
                </article>

                <article class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center gap-3 mb-1">
                        <svg class="w-4 h-4" :class="info.healthy ? 'text-emerald-600' : 'text-red-600'"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h2 class="text-sm font-medium text-gray-600">Status</h2>
                    </div>
                    <p :class="[
                        info.healthy ? 'text-emerald-600' : 'text-red-600',
                        'text-xl font-semibold'
                    ]">
                        {{ info.healthy ? 'Normal' : 'Attention Required' }}
                    </p>
                </article>
            </div>

            <section v-if="info.backups?.length > 0" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 md:table hidden" role="table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Size
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    File Name
                                </th>
                                <th scope="col"
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="backup in info.backups" :key="backup.path"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ backup.date }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ backup.size }}
                                </td>
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-mono truncate max-w-[200px]">
                                    {{ backup.path }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right space-x-1">
                                    <button type="button" @click="downloadBackup(backup.path)"
                                        class="inline-flex items-center p-2 text-gray-600 hover:text-blue-600 rounded-md hover:bg-blue-50 transition-colors cursor-pointer"
                                        title="Download backup" :disabled="!backup.path">
                                        <span class="sr-only">Download backup</span>
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="deleteBackup(backup.path)"
                                        class="inline-flex items-center p-2 text-gray-600 hover:text-red-600 rounded-md hover:bg-red-50 transition-colors cursor-pointer"
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
                </div>

                <div class="md:hidden divide-y divide-gray-200">
                    <div v-for="backup in info.backups" :key="backup.path"
                        class="p-4 space-y-2 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ backup.date }}</p>
                                <p class="text-sm text-gray-500">{{ backup.size }}</p>
                            </div>
                            <div class="flex space-x-1">
                                <button @click="downloadBackup(backup.path)"
                                    class="p-2 text-gray-600 hover:text-blue-600 rounded-md hover:bg-blue-50 transition-colors"
                                    title="Download backup">
                                    <i class="pi pi-download text-lg"></i>
                                </button>
                                <button @click="deleteBackup(backup.path)"
                                    class="p-2 text-gray-600 hover:text-red-600 rounded-md hover:bg-red-50 transition-colors"
                                    title="Delete backup">
                                    <i class="pi pi-trash text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 font-mono truncate">{{ backup.path }}</p>
                    </div>
                </div>
            </section>

            <section v-else class="text-center p-8 bg-white rounded-xl border border-gray-200 shadow-sm">
                <p class="text-gray-600">No backups found in this storage location</p>
            </section>
        </section>
    </main>
</template>
