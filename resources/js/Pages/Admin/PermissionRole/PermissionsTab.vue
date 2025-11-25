<script setup>
import { ref, h, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'
import FormInput from '@/Components/FormInput.vue'
import Datatable from '@/Components/Datatable.vue'
import Alert from '@/Components/Alert.vue'

const props = defineProps({
    permissions: {
        type: Object,
        required: true
    },
    protectedPermissions: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const showAddModal = ref(false)
const editingPermission = ref(null)
const showDeleteModal = ref(false)
const permissionToDelete = ref(null)
const loading = ref(false)

const pagination = ref({
    current_page: props.permissions.current_page,
    per_page: Number(props.permissions.per_page),
    total: props.permissions.total
})

const form = useForm({
    name: '',
    description: ''
})

const columns = [
    {
        accessorKey: 'name',
        header: 'Permission Name',
        cell: ({ row }) => {
            const permission = row.original
            return h('div', { class: 'flex-1' }, [
                h('div', { class: 'flex items-center gap-2' }, [
                    h('p', { class: 'text-sm font-medium text-gray-800 dark:text-gray-200' }, permission.name),
                    permission.is_protected ? h('span', {
                        class: 'inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400'
                    }, 'System Permission') : null
                ]),
                permission.description ? h('p', { class: 'text-xs text-gray-500 dark:text-gray-400' }, permission.description) : null
            ])
        }
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => {
            const permission = row.original
            return h('menu', { class: 'flex items-center gap-2' },
                !permission.is_protected ? [
                    h('li', [
                        h('button', {
                            type: 'button',
                            onClick: () => editPermission(permission),
                            class: 'p-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
                            title: 'Edit permission'
                        }, [
                            h('span', { class: 'sr-only' }, 'Edit permission'),
                            h('svg', { class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'aria-hidden': 'true' }, [
                                h('path', {
                                    'stroke-linecap': 'round',
                                    'stroke-linejoin': 'round',
                                    'stroke-width': '2',
                                    d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
                                })
                            ])
                        ])
                    ]),
                    h('li', [
                        h('button', {
                            type: 'button',
                            onClick: () => confirmDeletePermission(permission),
                            class: 'p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
                            title: 'Delete permission'
                        }, [
                            h('span', { class: 'sr-only' }, 'Delete permission'),
                            h('svg', { class: 'h-4 w-4', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor', 'aria-hidden': 'true' }, [
                                h('path', {
                                    'stroke-linecap': 'round',
                                    'stroke-linejoin': 'round',
                                    'stroke-width': '2',
                                    d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
                                })
                            ])
                        ])
                    ])
                ] : [
                    h('li', [
                        h('span', { class: 'text-xs text-gray-400 dark:text-gray-500' }, 'Protected')
                    ])
                ]
            )
        }
    }
]

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

watch(pagination, newPagination => {
    loading.value = true
    router.get(
        route('admin.permission.role.index'),
        {
            page: newPagination.current_page,
            per_page: Number(newPagination.per_page)
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => loading.value = false
        }
    )
}, { deep: true })

const handlePaginationUpdate = (paginationData) => {
    pagination.value = paginationData
}
</script>


<template>

    <section class="p-6 space-y-4 sm:space-y-6">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
            <hgroup>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Permissions</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage individual permissions</p>
            </hgroup>
            <button type="button" @click="showAddModal = true" class="btn-primary btn-sm w-full sm:w-auto">Add Permission</button>
        </header>

        <Datatable :data="permissions.data" :columns="columns" :pagination="pagination" :loading="loading" title="Permissions" :enable-search="true" :enable-export="true" :search-fields="['name', 'description']" empty-message="No permissions found" empty-description="Add your first permission to get started" export-file-name="permissions" :page-size-options="[10, 25, 50, 'All']" :default-page-size="10" @update:pagination="handlePaginationUpdate">
            <template #mobile-actions="{ row }">
                <menu v-if="!row.is_protected" class="flex items-center justify-end gap-2">
                    <li>
                        <button type="button" @click="editPermission(row)" class="p-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200" title="Edit Permission">
                            <span class="sr-only">Edit Permission</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                    </li>
                    <li>
                        <button type="button" @click="confirmDeletePermission(row)" class="p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200" title="Delete Permission">
                            <span class="sr-only">Delete Permission</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </li>
                </menu>
                <span v-else class="text-xs text-gray-400 dark:text-gray-500">Protected</span>
            </template>
        </Datatable>

        <Modal :show="showAddModal" @close="closeModal" size="sm">
            <template #title>
                {{ editingPermission ? 'Edit permission' : 'Add new permission' }}
            </template>

            <template #default>
                <form @submit.prevent="submitPermission" class="space-y-6">
                    <FormInput label="Permission name" v-model="form.name" type="text" :error="form.errors.name" required placeholder="Enter permission name" />
                    <FormInput label="Description" v-model="form.description" type="text" :error="form.errors.description" placeholder="Enter permission description (optional)" />
                </form>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button type="button" class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer" @click="closeModal">Cancel</button>
                    <button @click="submitPermission" type="button" class="btn-primary btn-sm" :disabled="form.processing" :aria-busy="form.processing">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Saving...' : (editingPermission ? 'Save Changes' : 'Add Permission') }}
                    </button>
                </div>
            </template>
        </Modal>

        <Modal :show="showDeleteModal" @close="closeModal" size="md">
            <template #title>
                <div class="flex items-center text-red-600 dark:text-red-400">Delete Permission</div>
            </template>

            <template #default>
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this permission? This action cannot be undone.</p>
                    <Alert type="warning">Deleting this permission will remove it from all roles that currently use it.</Alert>
                </div>
            </template>

            <template #footer>
                <div class="flex justify-end gap-8">
                    <button @click="closeModal" type="button" class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer" :disabled="form.processing">Cancel</button>
                    <button @click="deletePermission" type="button" class="btn-danger btn-sm" :disabled="form.processing">{{ form.processing ? 'Deleting...' : 'Yes, Delete Permission' }}</button>
                </div>
            </template>
        </Modal>
    </section>
</template>
