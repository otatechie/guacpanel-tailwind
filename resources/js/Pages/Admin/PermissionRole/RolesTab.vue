<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'

const props = defineProps({
    roles: {
        type: Array,
        required: true,
        default: () => []
    },
    permissions: {
        type: Array,
        required: true,
        default: () => []
    },
    protectedRoles: {
        type: Array,
        default: () => []
    }
})

const showAddModal = ref(false)
const editingRole = ref(null)
const showDeleteModal = ref(false)
const roleToDelete = ref(null)
const expandedRoles = ref(new Set())

const form = useForm({
    name: '',
    description: '',
    permissions: []
})

const closeModal = () => {
    showAddModal.value = false
    showDeleteModal.value = false
    editingRole.value = null
    roleToDelete.value = null
    form.reset()
}

const isProtectedRole = (roleName) => {
    return props.protectedRoles.includes(roleName.toLowerCase())
}

const editRole = (role) => {
    if (role.is_protected) {
        return
    }
    editingRole.value = role
    form.name = role.name
    form.description = role.description || ''
    form.permissions = role.permissions ? role.permissions.map(p => p.id) : []
    showAddModal.value = true
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

const confirmDeleteRole = (role) => {
    if (role.is_protected) {
        return
    }
    roleToDelete.value = role
    showDeleteModal.value = true
}

const deleteRole = () => {
    form.delete(route('admin.role.destroy', roleToDelete.value.id), {
        onSuccess: () => closeModal()
    })
}

const toggleAllPermissions = (e) => {
    form.permissions = e.target.checked
        ? props.permissions.map(p => p.id)
        : []
}

const toggleExpandRole = (roleId) => {
    if (expandedRoles.value.has(roleId)) {
        expandedRoles.value.delete(roleId)
    } else {
        expandedRoles.value.add(roleId)
    }
}

const isRoleExpanded = (roleId) => {
    return expandedRoles.value.has(roleId)
}
</script>

<template>
    <section class="p-3 sm:p-6 space-y-4 sm:space-y-6">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
            <hgroup>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Roles</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define roles and their permissions</p>
            </hgroup>
            <button type="button" @click="showAddModal = true" class="btn btn-sm btn-primary">
                Add role
            </button>
        </header>

        <!-- Mobile responsive table wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 min-w-[600px]"
                role="table">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Role Name
                        </th>
                        <th scope="col"
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th scope="col"
                            class="px-3 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-[100px]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-if="!roles?.length" class="text-center">
                        <td colspan="3" class="px-3 sm:px-6 py-8 text-sm text-gray-500 dark:text-gray-400">
                            <figure class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <figcaption>No roles found</figcaption>
                                <button type="button" @click="showAddModal = true"
                                    class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium cursor-pointer">
                                    Add your first role
                                </button>
                            </figure>
                        </td>
                    </tr>
                    <tr v-else v-for="role in roles" :key="role.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-3 sm:px-6 py-4">
                            <figure class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <figcaption class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 capitalize">
                                            {{ role.name }}
                                        </p>
                                        <span v-if="role.is_protected"
                                            class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            System Role
                                        </span>
                                    </div>
                                    <p v-if="role.description" class="text-xs text-gray-500 dark:text-gray-400 normal-case">
                                        {{ role.description }}
                                    </p>
                                </figcaption>
                            </figure>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <ul v-if="role.permissions?.length" class="flex flex-wrap gap-2" role="list">
                                <template v-if="isRoleExpanded(role.id)">
                                    <li v-for="permission in role.permissions" :key="permission.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                        {{ permission.name }}
                                    </li>
                                    <li>
                                        <button @click="toggleExpandRole(role.id)"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 px-2 py-0.5 cursor-pointer">
                                            Show less
                                        </button>
                                    </li>
                                </template>
                                <template v-else>
                                    <li v-for="permission in role.permissions.slice(0, 3)" :key="permission.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                        {{ permission.name }}
                                    </li>
                                    <li v-if="role.permissions.length > 3">
                                        <button @click="toggleExpandRole(role.id)"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 px-2 py-0.5 cursor-pointer">
                                            +{{ role.permissions.length - 3 }} more
                                        </button>
                                    </li>
                                </template>
                            </ul>
                            <p v-else class="text-xs text-gray-500 dark:text-gray-400">
                                No permissions assigned
                            </p>
                        </td>
                        <td class="px-3 sm:px-6 py-4">
                            <menu class="flex justify-end gap-1">
                                <li v-if="!role.is_protected">
                                    <button type="button" @click="editRole(role)"
                                        class="p-1 text-blue-400 hover:text-blue-600 transition-colors rounded-lg hover:bg-blue-50 cursor-pointer"
                                        title="Edit role">
                                        <span class="sr-only">Edit role</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </li>
                                <li v-if="!role.is_protected">
                                    <button type="button" @click="confirmDeleteRole(role)"
                                        class="p-1 text-red-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50 cursor-pointer"
                                        title="Delete role">
                                        <span class="sr-only">Delete role</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </li>
                                <li v-if="role.is_protected">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Protected</span>
                                </li>
                            </menu>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Modal :show="showAddModal" @close="closeModal">
            <template #title>
                {{ editingRole ? 'Edit Role' : 'Add New Role' }}
            </template>

            <template #default>
                <form @submit.prevent="submitRole" class="space-y-6">
                    <FormInput label="Role name" v-model="form.name" type="text" :error="form.errors.name" required
                        placeholder="Enter role name" />

                    <FormInput label="Description" v-model="form.description" type="text"
                        :error="form.errors.description" placeholder="Enter role description" />

                    <fieldset>
                        <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Permissions
                        </legend>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                            <label class="block p-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="flex items-center gap-2">
                                    <input type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600"
                                        :checked="permissions?.length && form.permissions.length === permissions.length"
                                        @change="toggleAllPermissions">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select all
                                        permissions</span>
                                </span>
                            </label>

                            <div class="p-3 max-h-[240px] overflow-y-auto">
                                <div v-if="permissions?.length" class="space-y-1">
                                    <label v-for="permission in permissions" :key="permission.id"
                                        class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                                        <input type="checkbox" :value="permission.id" v-model="form.permissions"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-blue-600">
                                        <span>
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ permission.name }}
                                            </span>
                                            <span v-if="permission.description"
                                                class="block text-xs text-gray-500 dark:text-gray-400">
                                                {{ permission.description }}
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.permissions" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.permissions }}
                        </p>
                    </fieldset>
                </form>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button type="button"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        @click="closeModal">
                        Cancel
                    </button>
                    <button @click="submitRole" type="button" class="btn btn-sm btn-primary" :disabled="form.processing"
                        :aria-busy="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ form.processing ? 'Saving...' : (editingRole ? 'Save changes' : 'Add role') }}
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
                    Delete Role
                </div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this role? This action cannot be undone.
                    </p>
                    <div v-if="roleToDelete?.permissions?.length"
                        class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">This role has the
                            following permissions:</h4>
                        <ul class="flex flex-wrap gap-2">
                            <li v-for="permission in roleToDelete.permissions" :key="permission.id"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                {{ permission.name }}
                            </li>
                        </ul>
                    </div>
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
                                Deleting this role will remove it from all users who currently have it assigned.
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
                    <button @click="deleteRole" type="button" class="btn btn-danger btn-sm" :disabled="form.processing">
                        {{ form.processing ? 'Deleting...' : 'Yes, delete role' }}
                    </button>
                </div>
            </template>
        </Modal>
    </section>
</template>
