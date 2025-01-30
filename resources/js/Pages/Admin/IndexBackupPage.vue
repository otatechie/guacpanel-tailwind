<template>

    <Head title="Backups" />

    <h2 class="text-xl font-heading font-semibold leading-7 text-gray-800 ml-5 mb-5">Backups</h2>

    <div class="container-border mx-5">
        <div v-for="info in backupInfo" :key="info.name" class="m-4">
            <div class="flex justify-end items-center mb-4">
                <Button @click="runBackup" :disabled="isBackupRunning"
                    :label="isBackupRunning ? 'Hang tight, creating backup...' : 'Create backup'" severity="primary"
                    size="small" />
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-100 p-4 rounded">
                    <div class="text-sm text-gray-600">Disk</div>
                    <div class="text-gray-600 font-medium">{{ info.disk }}</div>
                </div>
                <div class="bg-gray-100 p-4 rounded">
                    <div class="text-sm text-gray-600">Storage type</div>
                    <div class="text-gray-600 font-medium">{{ info.storageType }}</div>
                </div>
                <div class="bg-gray-100 p-4 rounded">
                    <div class="text-sm text-gray-600">Used space</div>
                    <div class="text-gray-600 font-medium">{{ info.storageSpace }}</div>
                </div>
                <div class="bg-gray-100 p-4 rounded">
                    <div class="text-sm text-gray-600">Status</div>
                    <div :class="info.healthy ? 'text-green-500 font-medium' : 'text-red-500'">
                        {{ info.healthy ? 'Healthy' : 'Unhealthy' }}
                    </div>
                </div>
            </div>

            <div v-if="info.backups.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Size</th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                                File name</th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-800 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        <tr v-for="backup in info.backups" :key="backup.path">
                            <td class="px-6 py-4 whitespace-nowrap">{{ backup.date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ backup.size }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ backup.path }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <Button @click="downloadBackup(backup.path)" icon="pi pi-arrow-down"
                                    severity="secondary" rounded aria-label="Cancel" class="mr-2" size="small" />
                                <Button @click="deleteBackup(backup.path)" icon="pi pi-times" severity="danger" rounded
                                    aria-label="Cancel" size="small" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-center text-gray-500 pt-5 text-md">No backups available</div>
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
    if (!confirm('Are you sure you want to delete this backup?')) return;

    form.delete(route('backup.delete', { path }), {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh the list without a full page reload
            form.get(route('backups.index'), { preserveScroll: true });
        },
    });
};
</script>
