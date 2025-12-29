<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormTextarea from '@js/Components/Forms/FormTextarea.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const props = defineProps({
    roles: {
        type: Array,
        required: true,
        default: () => [],
    },
    permissions: {
        type: Array,
        required: true,
        default: () => [],
    },
    protectedRoles: {
        type: Array,
        default: () => [],
    },
})

const showAddModal = ref(false)
const editingRole = ref(null)
const showDeleteModal = ref(false)
const roleToDelete = ref(null)
const expandedRoles = ref(new Set())
const permissionSearch = ref('')

const form = useForm({
    name: '',
    description: '',
    permissions: [],
})

const filteredPermissions = computed(() => {
    if (!permissionSearch.value) {
        return props.permissions
    }

    const keyword = permissionSearch.value.toLowerCase()

    return props.permissions.filter(permission => {
        const name = permission.name?.toLowerCase() ?? ''
        const description = permission.description?.toLowerCase() ?? ''

        return name.includes(keyword) || description.includes(keyword)
    })
})

const allPermissionsSelected = computed(() => {
    return props.permissions?.length && form.permissions.length === props.permissions.length
})

const selectedCountLabel = computed(() => {
    return `${form.permissions.length} selected of ${props.permissions.length}`
})

const closeModal = () => {
    showAddModal.value = false
    showDeleteModal.value = false
    editingRole.value = null
    roleToDelete.value = null
    permissionSearch.value = ''
    form.reset()
}

const isProtectedRole = roleName => {
    return props.protectedRoles.includes(roleName.toLowerCase())
}

const editRole = role => {
    if (role.is_protected) {
        return
    }
    permissionSearch.value = ''
    editingRole.value = role
    form.name = role.name
    form.description = role.description || ''
    form.permissions = role.permissions ? role.permissions.map(p => p.id) : []
    showAddModal.value = true
}

const submitRole = () => {
    if (editingRole.value) {
        form.put(route('admin.role.update', editingRole.value.id), {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('admin.role.store'), {
            onSuccess: () => closeModal(),
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
        onSuccess: () => closeModal(),
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
    <section class="space-y-4 p-6 sm:space-y-6">
        <header
            class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center sm:gap-0">
            <hgroup>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Roles</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Define roles and their permissions
                </p>
            </hgroup>
            <button
                type="button"
                @click="showAddModal = true"
                class="btn btn-primary btn-sm w-full sm:w-auto">
                Add Role
            </button>
        </header>

        <div class="overflow-x-auto">
            <div
                class="min-w-[600px] overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full" role="table">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                scope="col"
                                class="px-3 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase sm:px-6 dark:text-gray-400">
                                Role Name
                            </th>
                            <th
                                scope="col"
                                class="px-3 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase sm:px-6 dark:text-gray-400">
                                Permissions
                            </th>
                            <th
                                scope="col"
                                class="w-[100px] px-3 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase sm:px-6 dark:text-gray-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-if="!roles?.length" class="text-center">
                            <td
                                colspan="3"
                                class="px-3 py-8 text-sm text-gray-500 sm:px-6 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <p>No roles found</p>
                                    <button
                                        type="button"
                                        @click="showAddModal = true"
                                        class="cursor-pointer text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        Add Your First Role
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr
                            v-else
                            v-for="(role, index) in roles"
                            :key="role.id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            :class="
                                index % 2 === 0
                                    ? 'bg-white dark:bg-gray-800'
                                    : 'bg-gray-50 dark:bg-gray-700/30'
                            ">
                            <td class="px-3 py-4 sm:px-6">
                                <div class="flex items-center gap-2">
                                    <p
                                        class="text-sm font-medium text-gray-800 capitalize dark:text-gray-200">
                                        {{ role.name }}
                                    </p>
                                    <span
                                        v-if="role.is_protected"
                                        class="inline-flex items-center rounded bg-amber-100 px-1 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                        <svg
                                            class="mr-1 h-3 w-3"
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
                                    class="mt-1 text-xs text-gray-500 normal-case dark:text-gray-400">
                                    {{ role.description }}
                                </p>
                            </td>
                            <td class="px-3 py-4 sm:px-6">
                                <ul
                                    v-if="role.permissions?.length"
                                    class="flex flex-wrap gap-2"
                                    role="list">
                                    <template v-if="isRoleExpanded(role.id)">
                                        <li
                                            v-for="permission in role.permissions"
                                            :key="permission.id"
                                            class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/50 dark:text-blue-400">
                                            {{ permission.name }}
                                        </li>
                                        <li>
                                            <button
                                                @click="toggleExpandRole(role.id)"
                                                class="cursor-pointer px-2 py-0.5 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                Show Less
                                            </button>
                                        </li>
                                    </template>
                                    <template v-else>
                                        <li
                                            v-for="permission in role.permissions.slice(0, 3)"
                                            :key="permission.id"
                                            class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/50 dark:text-blue-400">
                                            {{ permission.name }}
                                        </li>
                                        <li v-if="role.permissions.length > 3">
                                            <button
                                                @click="toggleExpandRole(role.id)"
                                                class="cursor-pointer px-2 py-0.5 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                +{{ role.permissions.length - 3 }} more
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                                <p v-else class="text-xs text-gray-500 dark:text-gray-400">
                                    No permissions assigned
                                </p>
                            </td>
                            <td class="px-3 py-4 sm:px-6">
                                <menu class="flex justify-end gap-2">
                                    <li v-if="!role.is_protected">
                                        <button
                                            type="button"
                                            @click="editRole(role)"
                                            class="cursor-pointer rounded-lg bg-blue-50 p-2 text-blue-500 transition-all duration-200 hover:scale-105 hover:bg-blue-100 hover:text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                                            title="Edit Role">
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
                                            @click="confirmDeleteRole(role)"
                                            class="cursor-pointer rounded-lg bg-red-50 p-2 text-red-500 transition-all duration-200 hover:scale-105 hover:bg-red-100 hover:text-red-600 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                                            title="Delete Role">
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
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
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
            <template #title>{{ editingRole ? 'Edit Role' : 'Add New Role' }}</template>

            <template #default>
                <form @submit.prevent="submitRole" class="space-y-6">
                    <FormInput
                        label="Role name"
                        v-model="form.name"
                        type="text"
                        :error="form.errors.name"
                        required
                        placeholder="Enter role name" />
                    <FormTextarea
                        label="Description"
                        v-model="form.description"
                        :error="form.errors.description"
                        placeholder="Enter role description"
                        :rows="3" />

                    <fieldset>
                        <legend
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Permissions
                        </legend>
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                            <div
                                class="space-y-3 border-b border-gray-200 p-3 dark:border-gray-700">
                                <div
                                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <FormCheckbox
                                        :model-value="Boolean(allPermissionsSelected)"
                                        @update:model-value="
                                            checked => toggleAllPermissions(checked)
                                        "
                                        label="Select all permissions" />
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                        {{ selectedCountLabel }}
                                    </p>
                                </div>
                                <div class="relative">
                                    <input
                                        v-model="permissionSearch"
                                        type="search"
                                        placeholder="Search permissions…"
                                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500/40 focus:outline-none dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-200" />
                                    <span
                                        v-if="permissionSearch"
                                        class="absolute inset-y-0 right-3 flex cursor-pointer items-center text-xs text-gray-400"
                                        @click="permissionSearch = ''"></span>
                                </div>
                            </div>

                            <div class="max-h-[220px] overflow-y-auto p-3">
                                <div
                                    v-if="filteredPermissions?.length"
                                    class="grid gap-1.5 text-xs leading-tight sm:grid-cols-2 lg:grid-cols-2">
                                    <div
                                        v-for="permission in filteredPermissions"
                                        :key="permission.id"
                                        class="rounded-lg p-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <div class="text-xs">
                                            <FormCheckbox
                                                :model-value="
                                                    form.permissions.includes(permission.id)
                                                "
                                                @update:model-value="
                                                    checked =>
                                                        togglePermission(permission.id, checked)
                                                "
                                                :label="permission.name"
                                                :help="permission.description" />
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No permissions match that search
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
                        class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
                        @click="closeModal">
                        Cancel
                    </button>
                    <button
                        @click="submitRole"
                        type="button"
                        class="btn btn-primary btn-sm"
                        :disabled="form.processing"
                        :aria-busy="form.processing">
                        <svg
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
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

        <Modal :show="showDeleteModal" @close="closeModal" size="md">
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
                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            This role has the following permissions:
                        </h4>
                        <ul class="flex flex-wrap gap-2">
                            <li
                                v-for="permission in roleToDelete.permissions"
                                :key="permission.id"
                                class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/50 dark:text-blue-400">
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
                        @click="closeModal"
                        type="button"
                        class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
                        :disabled="form.processing">
                        Cancel
                    </button>
                    <button
                        @click="deleteRole"
                        type="button"
                        class="btn btn-danger btn-sm"
                        :disabled="form.processing">
                        {{ form.processing ? 'Deleting...' : 'Yes, Delete Role' }}
                    </button>
                </div>
            </template>
        </Modal>
    </section>
</template>
