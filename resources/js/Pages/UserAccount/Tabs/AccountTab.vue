<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import FormInput from '@/Components/FormInput.vue'
import Modal from '@/Components/Modal.vue'

const deactivateModal = ref(false)
const deleteModal = ref(false)
const deactivateForm = useForm({})
const deleteForm = useForm({})

const confirmDeactivateAccount = () => {
    deactivateModal.value = true
}

const confirmDeleteAccount = () => {
    deleteModal.value = true
}

const deactivateAccount = () => {
    deactivateForm.post(route('user.deactivate'), {
        preserveScroll: true,
        onSuccess: () => {
            deactivateModal.value = false
            window.location.href = route('home')
        }
    })
}

const deleteAccount = () => {
    deleteForm.post(route('user.delete'), {
        preserveScroll: true,
        onSuccess: () => {
            deleteModal.value = false
            window.location.href = route('home')
        }
    })
}

</script>

<template>
    <Head title="Danger Zone - Account Settings" />
    <div class="p-4 sm:p-6 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
            <h3
                class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">
                Danger Zone
            </h3>

            <!-- Deactivate Account -->
            <div
                class="mb-4 sm:mb-6 p-3 sm:p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-700">
                <h4
                    class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                    Deactivate Account
                </h4>
                <p
                    class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                    Hide your profile and data temporarily. You can reactivate your account
                    anytime.
                </p>
                <button
                    class="w-full sm:w-auto btn-secondary btn-sm inline-flex items-center gap-2"
                    @click="confirmDeactivateAccount">
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <span class="hidden sm:inline">Deactivate account</span>
                    <span class="sm:hidden">Deactivate</span>
                </button>
            </div>

            <!-- Delete Account -->
            <div
                class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                <h4
                    class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                    Delete Account Permanently
                </h4>
                <p
                    class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                    This action is permanent and cannot be undone. All your data will be
                    permanently deleted.
                </p>
                <button
                    class="w-full sm:w-auto btn-danger btn-sm inline-flex items-center gap-2"
                    @click="confirmDeleteAccount">
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="hidden sm:inline">Permanently delete account</span>
                    <span class="sm:hidden">Delete Account</span>
                </button>
            </div>
        </div>
    </div>

    <Modal :show="deactivateModal" size="sm" @close="deactivateModal = false">
        <template #title>
            <div class="text-red-600 dark:text-red-400">
                Deactivate Account
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to deactivate your account? This action cannot be
                    undone.
                </p>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button
                    type="button"
                    class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    :disabled="deactivateForm.processing"
                    @click="deactivateModal = false">
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn-danger btn-sm"
                    :disabled="deactivateForm.processing"
                    @click="deactivateAccount">
                    {{ deactivateForm.processing ? 'Deactivating...' : 'Yes, deactivate' }}
                </button>
            </div>
        </template>
    </Modal>

    <Modal :show="deleteModal" size="sm" @close="deleteModal = false">
        <template #title>
            <div class="text-red-600 dark:text-red-400">
                Delete Account
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete your account? This action is permanent and
                    cannot be undone.
                </p>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button
                    type="button"
                    class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    :disabled="deleteForm.processing"
                    @click="deleteModal = false">
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn-danger btn-sm"
                    :disabled="deleteForm.processing"
                    @click="deleteAccount">
                    {{ deleteForm.processing ? 'Deleting...' : 'Yes, delete' }}
                </button>
            </div>
        </template>
    </Modal>
</template>
