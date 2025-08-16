<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Modal from '@/Components/Modal.vue'

defineOptions({
    layout: Default
})

defineProps({
    systemNotices: {
        type: Array,
        default: () => []
    }
})

const showDeleteModal = ref(false);
const selectedNotice = ref(null);

const deleteNotice = (notice) => {
    selectedNotice.value = notice;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (selectedNotice.value) {
        router.delete(route('admin.system-notice.destroy', selectedNotice.value.id), {
            onSuccess: () => {
                closeDeleteModal();
            }
        });
    }
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    selectedNotice.value = null;
};

const getTypeColor = (type) => {
    const colors = {
        info: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        success: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        error: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    }
    return colors[type] || colors.info
}

const getTypeIcon = (type) => {
    const icons = {
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    }
    return icons[type] || icons.info
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<template>

    <Head title="System Notices" />
    <main class="max-w-7xl mx-auto" aria-labelledby="system-notices">
        <div class="container-border">
            <PageHeader title="System Notices" description="Manage system-wide notices displayed to all users"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Settings', href: route('admin.setting.index') },
                    { label: 'System Notices' }
                ]">
                <template #actions>
                    <Link :href="route('admin.system-notice.create')" class="btn btn-sm btn-primary">
                    Create notice
                    </Link>
                </template>
            </PageHeader>


            <section class="p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div v-if="systemNotices.length === 0"
                        class="col-span-full flex flex-col items-center justify-center py-12 px-6 text-center">
                        <div
                            class="w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">
                            No System Notices Available
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-md">
                            Get started by creating your first system notice to communicate with users.
                        </p>
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="notice in systemNotices" :key="notice.id"
                            class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-4 sm:p-6 hover:shadow-lg transition-all duration-200">
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span :class="[
                                    'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getTypeColor(notice.type)
                                ]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="getTypeIcon(notice.type)" />
                                    </svg>
                                    {{ notice.type }}
                                </span>
                                <span v-if="notice.is_active"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Active
                                </span>
                                <span v-else
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Inactive
                                </span>
                                <span v-if="notice.is_dismissible"
                                    class="text-xs text-gray-500 dark:text-gray-400">Dismissible</span>
                            </div>

                            <div class="mb-4">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2"
                                    :title="notice.title">{{
                                    notice.title }}</h2>
                                <p class="text-gray-600 dark:text-gray-300 text-sm" :title="notice.content">{{
                                    notice.content }}</p>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Visibility Period</h3>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                    <div v-if="notice.visible_from"
                                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <div class="mb-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Start Date
                                            </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">When the notice becomes
                                                visible</p>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ notice.visible_from_formatted || formatDate(notice.visible_from) }}
                                        </div>
                                    </div>
                                    <div v-if="notice.expires_at"
                                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <div class="mb-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">End Date
                                            </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">When the notice expires
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ notice.expires_at_formatted || formatDate(notice.expires_at) }}
                                        </div>
                                    </div>
                                    <div v-if="!notice.visible_from && !notice.expires_at"
                                        class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <div class="mb-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Always
                                                Visible</h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">No time restrictions</p>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                            </svg>
                                            Always visible
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Created: {{ notice.created_at_human }}
                                </span>
                                <div class="flex items-center gap-2 self-end sm:self-auto">
                                    <Link :href="route('admin.system-notice.edit', notice.id)"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 hover:shadow-sm"
                                        :title="`Edit notice: ${notice.title}`">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    </Link>
                                    <button @click="deleteNotice(notice)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 hover:shadow-sm cursor-pointer"
                                        :title="`Delete notice: ${notice.title}`">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <Modal :show="showDeleteModal" @close="closeDeleteModal" size="sm">
        <template #title>
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Delete Notice
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this notice? This action cannot be undone and the notice will
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
                            Notice: <span class="font-medium">{{ selectedNotice?.title }}</span> will be
                            deleted.
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <button @click="closeDeleteModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer order-2 sm:order-1">
                    Cancel
                </button>
                <button @click="confirmDelete" type="button" class="btn btn-sm btn-danger order-1 sm:order-2">
                    Yes, delete notice
                </button>
            </div>
        </template>
    </Modal>
</template>