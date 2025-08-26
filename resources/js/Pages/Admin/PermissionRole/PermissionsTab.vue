<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'

const props = defineProps({
    permissions: {
        type: Array,
        required: true,
        default: () => []
    },
    protectedPermissions: {
        type: Array,
        default: () => []
    }
})

const showAddModal = ref(false)
const editingPermission = ref(null)
const showDeleteModal = ref(false)
const permissionToDelete = ref(null)

const form = useForm({
    name: '',
    description: ''
})

const closeModal = () => {
    showAddModal.value = false
    showDeleteModal.value = false
    editingPermission.value = null
    permissionToDelete.value = null
    form.reset()
}

const editPermission = (permission) => {
    if (permission.is_protected) {
        return 
    }
    editingPermission.value = permission
    form.name = permission.name
    form.description = permission.description
    showAddModal.value = true
}

const submitPermission = () => {
    if (editingPermission.value) {
        form.put(route('admin.permission.update', editingPermission.value.id), {
            onSuccess: () => closeModal()
        })
    } else {
        form.post(route('admin.permission.store'), {
            onSuccess: () => closeModal()
        })
    }
}

const confirmDeletePermission = (permission) => {
    if (permission.is_protected) {
        return 
    }
    permissionToDelete.value = permission
    showDeleteModal.value = true
}

const deletePermission = () => {
    form.delete(route('admin.permission.destroy', permissionToDelete.value.id), {
        onSuccess: () => closeModal()
    })
}
</script>


<template>

    <section class="p-3 sm:p-6 space-y-4 sm:space-y-6">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
            <hgroup>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Permissions</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage individual permissions</p>
            </hgroup>
            <button type="button" @click="showAddModal = true" class="btn btn-sm btn-primary w-full sm:w-auto">
                Add permission
            </button>
        </header>

        <!-- Mobile responsive table wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 min-w-[500px]"
                role="table">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Permission name
                        </th>
                        <th scope="col"
                            class="px-3 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[100px]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-if="!permissions?.length" class="text-center">
                        <td colspan="2" class="px-3 sm:px-6 py-8 text-sm text-gray-500 dark:text-gray-400">
                            <figure class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                                <figcaption>No permissions found</figcaption>
                                <button type="button" @click="showAddModal = true"
                                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium cursor-pointer">
                                    Add your first permission
                                </button>
                            </figure>
                        </td>
                    </tr>
                    <tr v-else v-for="permission in permissions" :key="permission.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-3 sm:px-6 py-4">
                            <figure class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                                <figcaption class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ permission.name }}
                                        </p>
                                        <span v-if="permission.is_protected"
                                            class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            System Permission
                                        </span>
                                    </div>
                                    <p v-if="permission.description" class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ permission.description }}
                                    </p>
                                </figcaption>
                            </figure>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <menu class="flex justify-end gap-1">
                                <li v-if="!permission.is_protected">
                                    <button type="button" @click="editPermission(permission)"
                                        class="p-1 text-blue-400 hover:text-blue-600 transition-colors rounded-lg hover:bg-blue-50 cursor-pointer"
                                        title="Edit permission">
                                        <span class="sr-only">Edit permission</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </li>
                                <li v-if="!permission.is_protected">
                                    <button type="button" @click="confirmDeletePermission(permission)"
                                        class="p-1 text-red-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50 cursor-pointer"
                                        title="Delete permission">
                                        <span class="sr-only">Delete permission</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </li>
                                <li v-if="permission.is_protected">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Protected</span>
                                </li>
                            </menu>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Modal :show="showAddModal" @close="closeModal" size="sm">
            <template #title>
                {{ editingPermission ? 'Edit Permission' : 'Add New Permission' }}
            </template>

            <template #default>
                <form @submit.prevent="submitPermission" class="space-y-6">
                    <FormInput label="Permission name" v-model="form.name" type="text" :error="form.errors.name"
                        required placeholder="Enter permission name" />

                    <FormInput label="Description" v-model="form.description" type="text"
                        :error="form.errors.description" placeholder="Enter permission description (optional)" />
                </form>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        @click="closeModal">
                        Cancel
                    </button>
                    <button @click="submitPermission" type="button" class="btn btn-sm btn-primary gap-8"
                        :disabled="form.processing" :aria-busy="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ form.processing ? 'Saving...' : (editingPermission ? 'Save changes' : 'Add permission') }}
                    </button>
                </div>
            </template>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeModal" size="md">
            <template #title>
                <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Delete Permission
                </div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this permission? This action cannot be undone.
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
                                Deleting this permission will remove it from all roles that currently use it.
                            </p>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button @click="closeModal" type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        :disabled="form.processing">
                        Cancel
                    </button>
                    <button @click="deletePermission" type="button" class="btn-danger btn-sm" :disabled="form.processing">
                        {{ form.processing ? 'Deleting...' : 'Yes, delete permission' }}
                    </button>
                </div>
            </template>
        </Modal>
    </section>
</template>
