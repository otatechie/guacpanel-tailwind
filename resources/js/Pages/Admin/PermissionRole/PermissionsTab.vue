<script setup>
import { ref, h, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Modal from '@js/Components/Notifications/Modal.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import Datatable from '@js/Components/Common/Datatable.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const props = defineProps({
    permissions: {
        type: Object,
        required: true,
    },
    protectedPermissions: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const showAddModal = ref(false)
const editingPermission = ref(null)
const showDeleteModal = ref(false)
const permissionToDelete = ref(null)
const loading = ref(false)

const pagination = ref({
    current_page: props.permissions.current_page || 1,
    per_page: Number(props.permissions.per_page) || 25,
    total: props.permissions.total || (Array.isArray(props.permissions) ? props.permissions.length : props.permissions?.data?.length || 0),
})

const form = useForm({
    name: '',
    description: '',
})

const formatName = name => {
    const words = (name || '').split('-').join(' ')
    return words.charAt(0).toUpperCase() + words.slice(1)
}

const btnClass = 'cursor-pointer rounded-md p-1.5 text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]'
const iconClass = 'h-3.5 w-3.5'
const svgAttrs = { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.5', 'aria-hidden': 'true' }

const columns = [
    {
        accessorKey: 'name',
        header: 'Permission',
        cell: ({ row }) => {
            const p = row.original
            return h('div', {}, [
                h('span', { class: 'text-sm font-medium text-[var(--color-text)]' }, formatName(p.name)),
                p.is_protected
                    ? h('span', { class: 'ml-2 text-[10px] text-[var(--color-text-muted)]' }, 'Protected')
                    : null,
                p.description
                    ? h('p', { class: 'mt-0.5 text-xs text-[var(--color-text-muted)]' }, p.description)
                    : null,
            ])
        },
    },
    {
        id: 'actions',
        header: '',
        cell: ({ row }) => {
            const p = row.original
            if (p.is_protected) return null
            return h('div', { class: 'flex items-center justify-end gap-0.5' }, [
                h('button', { class: btnClass, onClick: () => editPermission(p), title: 'Edit' }, [
                    h('svg', { class: iconClass, ...svgAttrs }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10' }),
                    ]),
                ]),
                h('button', { class: btnClass + ' hover:text-red-600! dark:hover:text-red-400!', onClick: () => confirmDeletePermission(p), title: 'Delete' }, [
                    h('svg', { class: iconClass, ...svgAttrs }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' }),
                    ]),
                ]),
            ])
        },
    },
]

const closeModal = () => {
    showAddModal.value = false
    showDeleteModal.value = false
    editingPermission.value = null
    permissionToDelete.value = null
    form.reset()
}

const editPermission = permission => {
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
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('admin.permission.store'), {
            onSuccess: () => closeModal(),
        })
    }
}

const confirmDeletePermission = permission => {
    if (permission.is_protected) {
        return
    }
    permissionToDelete.value = permission
    showDeleteModal.value = true
}

const deletePermission = () => {
    form.delete(route('admin.permission.destroy', permissionToDelete.value.id), {
        onSuccess: () => closeModal(),
    })
}

watch(
    pagination,
    newPagination => {
        loading.value = true
        router.get(
            route('admin.permission.role.index'),
            {
                page: newPagination.current_page,
                per_page: Number(newPagination.per_page),
            },
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => (loading.value = false),
            }
        )
    },
    { deep: true }
)

const handlePaginationUpdate = paginationData => {
    pagination.value = paginationData
}
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-xs text-(--color-text-muted)">{{ permissions.total || (Array.isArray(permissions) ? permissions.length : permissions?.data?.length) || 0 }} permissions</p>
            <button type="button" @click="showAddModal = true" class="btn btn-primary btn-sm">
                Add permission
            </button>
        </div>

        <Datatable
            :data="Array.isArray(permissions) ? permissions : permissions.data"
            :columns="columns"
            :pagination="pagination"
            :loading="loading"
            title="Permissions"
            :enable-search="true"
            :enable-export="true"
            empty-message="No permissions found"
            empty-description="Add your first permission to get started"
            export-file-name="permissions"
            :page-size-options="[10, 25, 50, 'All']"
            :default-page-size="10"
            @update:pagination="handlePaginationUpdate">
            <template #mobile-actions="{ row }">
                <menu v-if="!row.is_protected" class="flex items-center justify-end gap-2">
                    <li>
                        <button
                            type="button"
                            @click="editPermission(row)"
                            class="cursor-pointer rounded-lg bg-blue-50 p-2 text-blue-500 transition-all duration-200 hover:scale-105 hover:bg-blue-100 hover:text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                            title="Edit permission">
                            <span class="sr-only">Edit permission</span>
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            @click="confirmDeletePermission(row)"
                            class="cursor-pointer rounded-lg bg-red-50 p-2 text-red-500 transition-all duration-200 hover:scale-105 hover:bg-red-100 hover:text-red-600 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 dark:hover:text-red-300"
                            title="Delete permission">
                            <span class="sr-only">Delete permission</span>
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </li>
                </menu>
                <span v-else class="text-xs text-gray-400 dark:text-gray-500">Protected</span>
            </template>
        </Datatable>

        <Modal :show="showAddModal" @close="closeModal" size="sm">
            <template #title>{{ editingPermission ? 'Edit permission' : 'Add permission' }}</template>
            <template #default>
                <form @submit.prevent="submitPermission" class="space-y-4">
                    <FormInput label="Name" v-model="form.name" :error="form.errors.name" required />
                    <FormInput label="Description" v-model="form.description" :error="form.errors.description" />
                </form>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-sm btn-secondary" @click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" :disabled="form.processing" @click="submitPermission">
                        {{ form.processing ? 'Saving...' : editingPermission ? 'Save' : 'Add' }}
                    </button>
                </div>
            </template>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeModal" size="sm">
            <template #title>Delete permission</template>
            <template #default>
                <p class="text-sm text-(--color-text-muted)">
                    Delete <span class="font-medium text-(--color-text)">{{ permissionToDelete?.name }}</span>? This removes it from all roles that use it.
                </p>
            </template>
            <template #footer>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-sm btn-secondary" @click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" :disabled="form.processing" @click="deletePermission">
                        {{ form.processing ? 'Deleting...' : 'Delete' }}
                    </button>
                </div>
            </template>
        </Modal>
    </section>
</template>
