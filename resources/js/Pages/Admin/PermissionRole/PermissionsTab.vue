<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'

const props = defineProps({
    permissions: {
        type: Object,
        required: true
    }
})

const showAddModal = ref(false)
const editingPermission = ref(null)

const form = useForm({
    name: '',
    description: ''
})

const closeModal = () => {
    showAddModal.value = false
    editingPermission.value = null
    form.reset()
}

const editPermission = (permission) => {
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

const deletePermission = (id) => {
    if (confirm('Are you sure you want to delete this permission?')) {
        form.delete(route('admin.permission.destroy', id))
    }
}
</script>

<template>
    <section class="p-6 space-y-6">
        <header class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Permissions</h2>
                <p class="mt-1 text-sm text-gray-500">Manage individual permissions</p>
            </div>
            <button type="button" @click="showAddModal = true" class="btn-primary inline-flex items-center gap-2">
                Add permission
            </button>
        </header>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" role="table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Permission name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-[100px]">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-if="!permissions.data.length" class="text-center">
                            <td colspan="2" class="px-6 py-8 text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                    <p>No permissions found</p>
                                    <button type="button" @click="showAddModal = true"
                                        class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        Add your first permission
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="permission in permissions.data" :key="permission.id"
                            class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-800">{{ permission.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="editPermission(permission)"
                                        class="text-gray-400 hover:text-blue-600 transition-colors"
                                        title="Edit permission">
                                        <span class="sr-only">Edit permission</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="deletePermission(permission.id)"
                                        class="text-gray-400 hover:text-red-600 transition-colors"
                                        title="Delete permission">
                                        <span class="sr-only">Delete permission</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal :show="showAddModal" @close="closeModal">
            <div class="p-6">
                <header class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ editingPermission ? 'Edit Permission' : 'Add New Permission' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ editingPermission ? 'Modify permission details' : 'Create a new permission' }}
                    </p>
                </header>

                <form @submit.prevent="submitPermission" class="space-y-6">
                    <div>
                        <FormInput label="Permission Name" :error="form.errors.name" v-model="form.name" type="text"
                            required placeholder="Enter permission name" />
                    </div>

                    <footer class="flex justify-end gap-6 pt-4 border-t border-gray-200">
                        <button type="button" class="btn-primary-outline" @click="closeModal">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ form.processing ? 'Saving...' : (editingPermission ? 'Save changes' : 'Add permission')
                            }}
                        </button>
                    </footer>
                </form>
            </div>
        </Modal>
    </section>
</template>
