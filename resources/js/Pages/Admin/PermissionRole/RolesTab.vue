<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'
import Alert from '@/Components/Alert.vue'

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

const isProtectedRole = roleName => {
    return props.protectedRoles.includes(roleName.toLowerCase())
}

const editRole = role => {
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

const confirmDeleteRole = role => {
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

const toggleAllPermissions = checked => {
    form.permissions = checked ? props.permissions.map(p => p.id) : []
}

const togglePermission = (permissionId, checked) => {
    if (checked) {
        if (!form.permissions.includes(permissionId)) {
            form.permissions.push(permissionId)
        }
    } else {
        form.permissions = form.permissions.filter(id => id !== permissionId)
    }
}

const toggleExpandRole = roleId => {
    if (expandedRoles.value.has(roleId)) {
        expandedRoles.value.delete(roleId)
    } else {
        expandedRoles.value.add(roleId)
    }
}

const isRoleExpanded = roleId => {
    return expandedRoles.value.has(roleId)
}
</script>

<template>
    <section class="p-6 space-y-4 sm:space-y-6">
        <header
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
            <hgroup>
                <h2 class="text-lg font-semibold text-[var(--color-text)]">Roles</h2>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">
                    Define roles and their permissions
                </p>
            </hgroup>
            <button
                type="button"
                class="btn-primary btn-sm w-full sm:w-auto"
                @click="showAddModal = true">
                Add Role
            </button>
        </header>

        <div class="overflow-x-auto">
            <div
                class="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden min-w-[600px]">
                <table class="w-full" role="table">
                    <thead class="bg-[var(--color-surface-muted)]">
                        <tr>
                            <th
                                scope="col"
                                class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider">
                                Role Name
                            </th>
                            <th
                                scope="col"
                                class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider">
                                Permissions
                            </th>
                            <th
                                scope="col"
                                class="px-3 sm:px-6 py-3 text-right text-xs font-medium text-[var(--color-text-muted)] uppercase tracking-wider w-[100px]">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="!roles?.length" class="text-center">
                            <td
                                colspan="3"
                                class="px-3 sm:px-6 py-8 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <p>No roles found</p>
                                    <button
                                        type="button"
                                        class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium cursor-pointer"
                                        @click="showAddModal = true">
                                        Add > Add Your First Role
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr
                            v-for="(role, index) in roles"
                            v-else
                            :key="role.id"
                            class="hover:bg-[var(--color-surface-muted)] transition-colors"
                            :class="
                                index % 2 === 0
                                    ? 'bg-[var(--color-surface)]'
                                    : 'bg-[var(--color-surface-muted)]'
                            ">
                            <td class="px-3 sm:px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <p
                                        class="text-sm font-medium text-[var(--color-text)] capitalize">
                                        {{ role.name }}
                                    </p>
                                    <span
                                        v-if="role.is_protected"
                                        class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                        <svg
                                            class="w-3 h-3 mr-1"
                                            fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        System Role
                                    </span>
                                </div>
                                <p
                                    v-if="role.description"
                                    class="text-xs text-gray-500 dark:text-gray-400 normal-case mt-1">
                                    {{ role.description }}
                                </p>
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <ul
                                    v-if="role.permissions?.length"
                                    class="flex flex-wrap gap-2"
                                    role="list">
                                    <template v-if="isRoleExpanded(role.id)">
                                        <li
                                            v-for="permission in role.permissions"
                                            :key="permission.id"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                            {{ permission.name }}
                                        </li>
                                        <li>
                                            <button
                                                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 px-2 py-0.5 cursor-pointer"
                                                @click="toggleExpandRole(role.id)">
                                                Show > Show Less
                                            </button>
                                        </li>
                                    </template>
                                    <template v-else>
                                        <li
                                            v-for="permission in role.permissions.slice(0, 3)"
                                            :key="permission.id"
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                            {{ permission.name }}
                                        </li>
                                        <li v-if="role.permissions.length > 3">
                                            <button
                                                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 px-2 py-0.5 cursor-pointer"
                                                @click="toggleExpandRole(role.id)">
                                                +{{ role.permissions.length - 3 }}
                                                more
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                                <p v-else class="text-xs text-[var(--color-text-muted)]">
                                    No permissions assigned
                                </p>
                            </td>
                            <td class="px-3 sm:px-6 py-4">
                                <menu class="flex justify-end gap-2">
                                    <li v-if="!role.is_protected">
                                        <button
                                            type="button"
                                            class="p-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200"
                                            title="Edit Role"
                                            @click="editRole(role)">
                                            <span class="sr-only">Edit Role</span>
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </li>
                                    <li v-if="!role.is_protected">
                                        <button
                                            type="button"
                                            class="p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200"
                                            title="Delete Role"
                                            @click="confirmDeleteRole(role)">
                                            <span class="sr-only">Delete Role</span>
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </li>
                                    <li v-if="role.is_protected">
                                        <span class="text-xs text-[var(--color-text-muted)]">
                                            Protected
                                        </span>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal :show="showAddModal" @close="closeModal">
            <template #title>{{ editingRole ? 'Edit role' : 'Add new role' }}</template>

            <template #default>
                <form class="space-y-6" @submit.prevent="submitRole">
                    <FormInput
                        v-model="form.name"
                        label="Role name"
                        type="text"
                        :error="form.errors.name"
                        required
                        placeholder="Enter role name" />
                    <FormTextarea
                        v-model="form.description"
                        label="Description"
                        :error="form.errors.description"
                        placeholder="Enter role description"
                        :rows="3" />

                    <fieldset>
                        <legend
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Permissions
                        </legend>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                <FormCheckbox
                                    :model-value="
                                        permissions?.length &&
                                        form.permissions.length === permissions.length
                                    "
                                    label="Select All Permissions"
                                    @update:model-value="
                                        checked => toggleAllPermissions(checked)
                                    " />
                            </div>

                            <div class="p-3 max-h-[240px] overflow-y-auto">
                                <div v-if="permissions?.length" class="space-y-2">
                                    <div
                                        v-for="permission in permissions"
                                        :key="permission.id"
                                        class="p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <FormCheckbox
                                            :model-value="form.permissions.includes(permission.id)"
                                            :label="permission.name"
                                            :help="permission.description"
                                            @update:model-value="
                                                checked => togglePermission(permission.id, checked)
                                            " />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p
                            v-if="form.errors.permissions"
                            class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.permissions }}
                        </p>
                    </fieldset>
                </form>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button
                        type="button"
                        class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        @click="closeModal">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn-primary btn-sm"
                        :disabled="form.processing"
                        :aria-busy="form.processing"
                        @click="submitRole">
                        <svg
                            v-if="form.processing"
                            class="animate-spin h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24">
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{
                            form.processing
                                ? 'Saving...'
                                : editingRole
                                  ? 'Save Changes'
                                  : 'Add Role'
                        }}
                    </button>
                </div>
            </template>
        </Modal>

        <Modal :show="showDeleteModal" size="md" @close="closeModal">
            <template #title>
                <div class="flex items-center text-red-600 dark:text-red-400">Delete Role</div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Are you sure you want to delete this role? This action cannot be undone.
                    </p>
                    <div
                        v-if="roleToDelete?.permissions?.length"
                        class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            This role has the following permissions:
                        </h4>
                        <ul class="flex flex-wrap gap-2">
                            <li
                                v-for="permission in roleToDelete.permissions"
                                :key="permission.id"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                {{ permission.name }}
                            </li>
                        </ul>
                    </div>
                    <Alert type="warning">
                        Deleting this role will remove it from all users who currently have it
                        assigned.
                    </Alert>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button
                        type="button"
                        class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                        :disabled="form.processing"
                        @click="closeModal">
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="btn-danger btn-sm"
                        :disabled="form.processing"
                        @click="deleteRole">
                        {{ form.processing ? 'Deleting...' : 'Yes, Delete Role' }}
                    </button>
                </div>
            </template>
        </Modal>
    </section>
</template>
