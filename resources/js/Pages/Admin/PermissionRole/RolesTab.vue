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

const toggleAllPermissions = (e) => {
    form.permissions = e.target.checked
        ? props.permissions.data.map(p => p.id)
        : []
}
</script>

<template>
    <section class="p-6 space-y-6">
        <header class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Roles</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define roles and their permissions</p>
            </div>
            <button
                type="button"
                @click="showAddModal = true"
                class="btn-primary inline-flex items-center gap-2"
            >   Add role
            </button>
        </header>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" role="table">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Role Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Permissions
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[100px]">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="!roles.data.length" class="text-center">
                            <td colspan="3" class="px-6 py-8 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p>No roles found</p>
                                    <button
                                        type="button"
                                        @click="showAddModal = true"
                                        class="text-sm text-blue-600 hover:text-blue-700 font-medium cursor-pointer"
                                    > Add your first role
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-else v-for="role in roles.data" :key="role.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ role.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div v-if="role.permissions?.length" class="flex flex-wrap gap-2">
                                    <span v-for="permission in role.permissions.slice(0, 3)" :key="permission.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                        {{ permission.name }}
                                    </span>
                                    <span v-if="role.permissions.length > 3" class="text-xs text-gray-500 dark:text-gray-400 px-2 py-0.5">
                                        +{{ role.permissions.length - 3 }} more
                                    </span>
                                </div>
                                <span v-else class="text-xs text-gray-500 dark:text-gray-400">
                                    No permissions assigned
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="editRole(role)"
                                        class="text-gray-400 hover:text-blue-600 transition-colors" title="Edit role">
                                        <span class="sr-only">Edit role</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="deleteRole(role.id)"
                                        class="text-gray-400 hover:text-red-600 transition-colors" title="Delete role">
                                        <span class="sr-only">Delete role</span>
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
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                        {{ editingRole ? 'Edit role' : 'Add new role' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ editingRole ? 'Modify role details and permissions' : 'Create a new role and assign permissions' }}
                    </p>
                </header>

                <form @submit.prevent="submitRole" class="space-y-6">
                    <div>
                        <FormInput
                            label="Role name"
                            v-model="form.name"
                            type="text"
                            :error="form.errors.name"
                            required
                            placeholder="Enter role name"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-400">
                            Permissions
                        </label>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        :checked="form.permissions.length === permissions.data.length"
                                        @change="toggleAllPermissions"
                                    >
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Select all permissions</span>
                                </div>
                            </div>

                            <div class="p-3 max-h-[240px] overflow-y-auto">
                                <div v-if="permissions.data?.length" class="space-y-1">
                                    <div v-for="permission in permissions.data" :key="permission.id"
                                        class="flex items-center gap-2 p-1 rounded-lg">
                                        <input type="checkbox"
                                            :value="permission.id"
                                            v-model="form.permissions"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                        >
                                        <div>
                                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ permission.name }}
                                            </div>
                                            <div v-if="permission.description" class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ permission.description }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 text-center py-4">
                                    No permissions available
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.permissions" class="mt-1 text-sm text-red-600">
                            {{ form.errors.permissions }}
                        </div>
                    </div>

                    <footer class="flex justify-end gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="button"
                            class="cursor-pointer font-medium dark:text-white"
                            @click="closeModal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn-primary inline-flex items-center gap-2"
                            :disabled="form.processing"
                        >
                            <svg
                                v-if="form.processing"
                                class="animate-spin h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Saving...' : (editingRole ? 'Save changes' : 'Add role') }}
                        </button>
                    </footer>
                </form>
            </div>
        </Modal>
    </section>
</template>
