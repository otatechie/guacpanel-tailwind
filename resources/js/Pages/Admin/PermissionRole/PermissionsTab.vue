<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div class="space-y-1">
                <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
                <p class="text-sm text-gray-500">Manage individual permissions</p>
            </div>
            <button @click="showAddModal = true" class="btn-primary">Add permission</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="permission in permissions.data" :key="permission.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ permission.name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ permission.description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button @click="editPermission(permission)"
                                class="p-2 text-gray-600 hover:text-blue-600 rounded-md hover:bg-gray-100 transition-colors cursor-pointer"
                                title="Edit permission">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deletePermission(permission.id)"
                                class="p-2 text-gray-600 hover:text-red-600 rounded-md hover:bg-gray-100 transition-colors cursor-pointer"
                                title="Delete permission">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Permission Modal -->
        <Modal :show="showAddModal" @close="closeModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingPermission ? 'Edit Permission' : 'Add New Permission' }}
                </h3>
                <form @submit.prevent="submitPermission" class="space-y-4">
                    <div>
                        <FormInput label="Permission Name" :error="form.errors.name" v-model="form.name" type="text"
                            required />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg cursor-pointer"
                            @click="closeModal">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : (editingPermission ? 'Save changes' : 'Add permission')
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'

const props = defineProps({
    permissions: Object
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
