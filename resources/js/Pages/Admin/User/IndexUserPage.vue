<script setup>
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import DataTable from '@/Components/Datatable.vue'
import Default from '@/Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import PageHeader from '@/Components/PageHeader.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    roles: {
        type: Object,
        required: true
    }
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.users.current_page,
    per_page: Number(props.users.per_page),
    total: props.users.total
})

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const showCreateUserModal = ref(false)

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    force_password_change: false,
})

const closeModal = () => {
    showDeleteModal.value = false
    userToDelete.value = null
    showCreateUserModal.value = false
    form.reset()
}

const isSuperUser = (user) => {
    if (!user?.roles?.length) return false
    const role = user.roles[0]
    return role?.name?.toLowerCase() === 'superuser' || role?.slug?.toLowerCase() === 'superuser'
}

const canDeleteUser = (user) => {
    if (!user) return false
    if (isSuperUser(user)) return false
    return true
}

const handleEdit = (user) => {
    if (!user?.id) return
    router.visit(route('admin.user.edit', { id: user.id }))
}

const confirmDeleteUser = (user) => {
    if (!canDeleteUser(user)) return
    userToDelete.value = user
    showDeleteModal.value = true
}

const deleteUser = () => {
    if (!userToDelete.value?.id) return
    if (!canDeleteUser(userToDelete.value)) return

    router.delete(route('admin.user.destroy', { id: userToDelete.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            userToDelete.value = null
        },
        onError: () => {
            showDeleteModal.value = false
            userToDelete.value = null
        }
    })
}

const openCreateModal = () => {
    showCreateUserModal.value = true
}

const createUser = () => {
    form.post(route('admin.user.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateUserModal.value = false
            form.reset()
        }
    })
}

const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => h('span', info.getValue() || '-')
    }),
    columnHelper.accessor('email', {
        header: 'Email',
        cell: info => h('span', info.getValue() || '-')
    }),
    columnHelper.accessor('role', {
        header: 'Role',
        cell: info => {
            const roleName = info.row.original.roles?.[0]?.name || 'No Role'
            return h('span', {
                class: 'px-1 py-1 text-xs capitalize rounded-md inline-flex items-center justify-center bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300'
            }, roleName)
        }
    }),
    columnHelper.accessor('created_at_formatted', {
        header: 'Created At',
        cell: info => h('span', info.getValue() || '-')
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        cell: info => {
            const user = info.row.original
            if (!user?.id) return null

            const editButton = h('button', {
                class: 'p-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
                onClick: () => handleEdit(user),
                type: 'button',
                title: 'Edit User'
            }, [
                h('span', { class: 'sr-only' }, 'Edit User'),
                h('svg', {
                    class: 'h-4 w-4',
                    fill: 'none',
                    stroke: 'currentColor',
                    viewBox: '0 0 24 24',
                    'aria-hidden': 'true'
                }, [
                    h('path', {
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round',
                        'stroke-width': '2',
                        d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
                    })
                ])
            ])

            const deleteButton = h('button', {
                class: 'p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
                onClick: () => confirmDeleteUser(user),
                type: 'button',
                title: 'Delete User'
            }, [
                h('span', { class: 'sr-only' }, 'Delete User'),
                h('svg', {
                    class: 'h-4 w-4',
                    fill: 'none',
                    stroke: 'currentColor',
                    viewBox: '0 0 24 24',
                    'aria-hidden': 'true'
                }, [
                    h('path', {
                        'stroke-linecap': 'round',
                        'stroke-linejoin': 'round',
                        'stroke-width': '2',
                        d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
                    })
                ])
            ])

            return h('div', {
                class: 'flex items-center gap-2 justify-end'
            }, [
                editButton,
                canDeleteUser(user) && deleteButton
            ].filter(Boolean))
        }
    })
]

watch(pagination, newPagination => {
    loading.value = true
    router.get(
        route('admin.user.index'),
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
</script>

<template>

    <Head title="Users Management" />
    <main class="max-w-7xl mx-auto" aria-labelledby="users-management">
        <div class="container-border">
            <PageHeader title="Users Management" description="Manage system users and their access" :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Users Management' }
            ]">
                <template #actions>
                    <button @click="openCreateModal" class="btn-primary btn-sm">
                        Add User
                    </button>
                </template>
            </PageHeader>

            <section class="p-6 dark:bg-gray-900">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <DataTable :data="users.data" :columns="columns" :loading="loading" :pagination="pagination"
                        :search-fields="['name', 'email', 'created_at_formatted']" empty-message="No users found"
                        empty-description="Users will appear here once created" export-file-name="users"
                        @update:pagination="pagination = $event" />
                </div>
            </section>
        </div>
    </main>

    <Modal :show="showDeleteModal" @close="closeModal" size="md">
        <template #title>
            <div class="text-red-600 dark:text-red-400">
                Delete User
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this user? This action cannot be undone.
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
                            This will permanently delete the user's account and all associated data.
                        </p>
                    </div>
                </div>
                <div v-if="userToDelete"
                    class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User details:</h4>
                    <dl class="space-y-1">
                        <div class="flex gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Name:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ userToDelete.name }}</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ userToDelete.email }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button @click="closeModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer">
                    Cancel
                </button>
                <button @click="deleteUser" type="button" class="btn-danger btn-sm" :disabled="false">
                    Yes, Delete User
                </button>
            </div>
        </template>
    </Modal>

    <Modal :show="showCreateUserModal" @close="closeModal" size="lg">
        <template #title>
            Create New User
        </template>

        <template #default>
            <div class="w-full space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <FormInput v-model="form.name" label="Legal name" :error="form.errors.name" name="name" />
                    <FormInput v-model="form.email" label="Email address" type="email" :error="form.errors.email"
                        name="email" />
                    <FormInput v-model="form.password" label="Password" name="password" id="password" type="password"
                        required :error="form.errors.password" autocomplete="new-password" />
                    <FormInput v-model="form.password_confirmation" label="Confirm password"
                        name="password_confirmation" id="password_confirmation" type="password" required
                        :error="form.errors.password_confirmation" autocomplete="new-password" />
                </div>
                <div>
                    <FormSelect v-model="form.role" :options="props.roles?.data || []" option-label="name"
                        option-value="id" name="role" label="Assigned role" :error="form.errors.role" />
                </div>
                <div class="space-y-6">
                    <FormCheckbox 
                        v-model="form.force_password_change" 
                        label="Force Password Reset"
                        description="Require new password on next login"
                        :error="form.errors.force_password_change"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button @click="closeModal" type="button"
                    class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer">
                    Cancel
                </button>
                <button @click="createUser" type="button" class="btn-primary btn-sm" :disabled="form.processing">
                    Create User
                </button>
            </div>
        </template>
    </Modal>
</template>
