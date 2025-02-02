<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div class="space-y-1">
                <h3 class="text-lg font-medium text-gray-900">Roles</h3>
                <p class="text-sm text-gray-500">Define roles and their permissions</p>
            </div>
            <button @click="showAddModal = true" class="btn-primary">Add new role</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                            Name</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permissions</th>
                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="!roles.data.length">
                        <td colspan="3" class="px-2 py-4 text-sm text-gray-500 text-center">
                            No records found
                        </td>
                    </tr>
                    <tr v-else v-for="role in roles.data" :key="role.id">
                        <td class="px-2 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ role.name }}</div>
                        </td>
                        <td class="px-2 py-2">
                            <div v-if="role.permissions?.length" class="flex flex-wrap gap-2">
                                <span v-for="permission in role.permissions.slice(0, 3)" :key="permission.id"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ permission.name }}
                                </span>
                                <span v-if="role.permissions.length > 3" class="text-xs text-gray-500">
                                    +{{ role.permissions.length - 3 }} more
                                </span>
                            </div>
                            <span v-else class="text-xs text-gray-500">No permissions assigned</span>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-right ">
                            <button @click="editRole(role)"
                                class="p-2 text-gray-600 hover:text-blue-600 rounded-md hover:bg-gray-100 transition-colors cursor-pointer"
                                title="Edit role">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="deleteRole(role.id)"
                                class="p-2 text-gray-600 hover:text-red-600 rounded-md hover:bg-gray-100 transition-colors cursor-pointer"
                                title="Delete role">
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

        <Modal :show="showAddModal" @close="closeModal">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingRole ? 'Edit Role' : 'Add New Role' }}
                </h3>
                <form @submit.prevent="submitRole" class="space-y-4">
                    <div>
                        <FormInput label="Role Name" v-model="form.name" type="text" :error="form.errors.name"
                            required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Permissions</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3">
                            <div v-if="permissions.data?.length" class="space-y-2">
                                <div v-for="permission in permissions.data" :key="permission.id"
                                    class="flex items-center space-x-2">
                                    <input type="checkbox" :value="permission.id" v-model="form.permissions"
                                        class="rounded border-gray-300 text-blue-600">
                                    <span class="text-sm text-gray-700">{{ permission.name }}</span>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 text-center py-2">
                                No permissions available
                            </div>
                        </div>
                        <div v-if="form.errors.permissions" class="mt-1 text-sm text-red-600">
                            {{ form.errors.permissions }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg cursor-pointer"
                            @click="closeModal">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : (editingRole ? 'Save Changes' : 'Add Role') }}
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
    roles: Object,
    permissions: Object
})

const showAddModal = ref(false)
const editingRole = ref(null)

const form = useForm({
    name: '',
    permissions: []
})

const closeModal = () => {
    showAddModal.value = false
    editingRole.value = null
    form.reset()
}

const editRole = (role) => {
    editingRole.value = role;
    form.name = role.name;
    form.permissions = role.permissions.map(p => p.id);
    showAddModal.value = true;
}

const submitRole = () => {
    if (editingRole.value) {
        form.put(route('admin.role.update', editingRole.value.id), {
            onSuccess: () => closeModal()
        })
    } else {
        form.post(route('admin.role.store'), {
            onSuccess: () => closeModal()
        })
    }
}

const deleteRole = (id) => {
    if (confirm('Are you sure you want to delete this role?')) {
        form.delete(route('admin.role.destroy', id))
    }
}
</script>
