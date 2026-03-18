<script setup>
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import DataTable from '@js/Components/Common/Datatable.vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import RolesBadges from '@js/Components/Common/RolesBadges.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Object,
        required: true,
    },
    deletedUsers: {
        type: [Object, Number],
        // required: true,
        default: () => ({}),
    },
})

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.users.current_page,
    per_page: Number(props.users.per_page),
    total: props.users.total,
})

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const showCreateUserModal = ref(false)
const showImpersonateModal = ref(false)
const userToImpersonate = ref(null)

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
    showImpersonateModal.value = false
    userToImpersonate.value = null
    form.reset()
}

const isSuperUser = user => {
    if (!user?.roles?.length) return false
    const role = user.roles[0]
    return role?.name?.toLowerCase() === 'superuser' || role?.slug?.toLowerCase() === 'superuser'
}

const canDeleteUser = user => {
    if (!user) return false
    if (isSuperUser(user)) return false
    return true
}

const handleEdit = user => {
    if (!user?.id) return
    router.visit(route('admin.user.edit', { id: user.id }))
}

const confirmImpersonate = user => {
    if (!user?.id) return
    if (isSuperUser(user)) return
    userToImpersonate.value = user
    showImpersonateModal.value = true
}

const handleImpersonate = () => {
    if (!userToImpersonate.value?.id) return
    router.post(route('admin.user.impersonate.start', { user: userToImpersonate.value.id }))
}

const confirmDeleteUser = user => {
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
        },
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
        },
    })
}

const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => {
            const user = info.row.original
            return h('div', { class: 'min-w-0' }, [
                h('span', { class: 'text-sm font-medium text-[var(--color-text)]' }, user.name || '-'),
                h('span', { class: 'ml-2 text-xs text-[var(--color-text-muted)]' }, user.email),
            ])
        },
    }),
    columnHelper.accessor('role', {
        header: 'Role',
        cell: info => {
            const roleName = info.row.original.roles?.[0]?.name || 'No role'
            return h('span', { class: 'text-xs font-mono capitalize text-[var(--color-text-muted)]' }, roleName)
        },
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        cell: info => {
            const user = info.row.original
            const verified = !!user.email_verified_at
            const disabled = !!user.disable_account
            const locked = !!user.account_locked

            if (disabled) return h('span', { class: 'flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400' }, [
                h('span', { class: 'h-1.5 w-1.5 rounded-full bg-red-500' }), 'Disabled'
            ])
            if (locked) return h('span', { class: 'flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400' }, [
                h('span', { class: 'h-1.5 w-1.5 rounded-full bg-amber-500' }), 'Locked'
            ])
            if (!verified) return h('span', { class: 'flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400' }, [
                h('span', { class: 'h-1.5 w-1.5 rounded-full bg-amber-500' }), 'Unverified'
            ])
            return h('span', { class: 'flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400' }, [
                h('span', { class: 'h-1.5 w-1.5 rounded-full bg-green-500' }), 'Active'
            ])
        },
    }),
    columnHelper.accessor('created_at_formatted', {
        header: 'Created',
        cell: info => h('span', { class: 'text-xs text-[var(--color-text-muted)]' }, info.getValue() || '-'),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        cell: info => {
            const user = info.row.original
            if (!user?.id) return null

            const btnClass = 'cursor-pointer rounded-md p-1.5 text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]'
            const iconClass = 'h-3.5 w-3.5'
            const svgAttrs = { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '1.5', 'aria-hidden': 'true' }

            const editBtn = h('button', { class: btnClass, onClick: () => handleEdit(user), title: 'Edit' }, [
                h('svg', { class: iconClass, ...svgAttrs }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10' }),
                ]),
            ])

            const impersonateBtn = !isSuperUser(user)
                ? h('button', { class: btnClass, onClick: () => confirmImpersonate(user), title: 'Impersonate' }, [
                    h('svg', { class: iconClass, ...svgAttrs }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z' }),
                    ]),
                ])
                : null

            const deleteBtn = canDeleteUser(user)
                ? h('button', { class: btnClass + ' hover:text-red-600! dark:hover:text-red-400!', onClick: () => confirmDeleteUser(user), title: 'Delete' }, [
                    h('svg', { class: iconClass, ...svgAttrs }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0' }),
                    ]),
                ])
                : null

            return h('div', { class: 'flex items-center justify-end gap-0.5' },
                [editBtn, impersonateBtn, deleteBtn].filter(Boolean)
            )
        },
    }),
]

watch(
    pagination,
    newPagination => {
        loading.value = true
        router.get(
            route('admin.user.index'),
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
</script>

<template>
    <Head title="Users Management" />
    <main class="mx-auto max-w-7xl" aria-labelledby="users-management">
        <PageHeader
            title="Users Management"
            description="Manage system users and their access"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Users Management' },
            ]">
            <template #actions>
                <button @click="openCreateModal" class="btn btn-primary btn-sm">
                    Add user
                </button>
            </template>

            <template #bottom v-if="deletedUsers">
                <div class="mt-3 flex items-center justify-between">
                    <span v-if="deletedUsers" class="text-xs">
                        {{ deletedUsers }} Deleted
                        {{ deletedUsers == 1 ? 'User' : 'Users' }}
                    </span>
                    <Link
                        v-if="deletedUsers"
                        :href="route('admin.user.deleted.index')"
                        class="btn btn-secondary btn-xs">
                        View deleted {{ deletedUsers == 1 ? 'user' : 'users' }}
                    </Link>
                </div>
            </template>
        </PageHeader>

        <div class="card p-6">
            <DataTable
                :data="users.data"
                :columns="columns"
                :loading="loading"
                :pagination="pagination"
                :filters-enabled="false"
                empty-message="No users found"
                empty-description="Users will appear here once created"
                export-file-name="users"
                @update:pagination="pagination = $event" />
        </div>
    </main>

    <Modal :show="showDeleteModal" @close="closeModal" size="sm">
        <template #title>Delete user</template>

        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                Delete <span class="font-medium text-(--color-text)">{{ userToDelete?.name }}</span> ({{ userToDelete?.email }})? This action is recoverable until the auto-delete date.
            </p>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button @click="closeModal" type="button" class="btn btn-secondary btn-sm">Cancel</button>
                <button @click="deleteUser" type="button" class="btn btn-danger btn-sm">Delete user</button>
            </div>
        </template>
    </Modal>

    <Modal :show="showCreateUserModal" @close="closeModal" size="md">
        <template #title>Create new user</template>

        <template #default>
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <FormInput
                        v-model="form.name"
                        label="Name"
                        :error="form.errors.name"
                        name="name" />
                    <FormInput
                        v-model="form.email"
                        label="Email"
                        type="email"
                        :error="form.errors.email"
                        name="email" />
                    <FormInput
                        v-model="form.password"
                        label="Password"
                        name="password"
                        id="password"
                        type="password"
                        required
                        :error="form.errors.password"
                        autocomplete="new-password" />
                    <FormInput
                        v-model="form.password_confirmation"
                        label="Confirm password"
                        name="password_confirmation"
                        id="password_confirmation"
                        type="password"
                        required
                        :error="form.errors.password_confirmation"
                        autocomplete="new-password" />
                </div>
                <FormSelect
                    v-model="form.role"
                    :options="props.roles?.data || []"
                    option-label="name"
                    option-value="id"
                    name="role"
                    label="Role"
                    :error="form.errors.role" />
                <FormCheckbox
                    v-model="form.force_password_change"
                    label="Force password reset on next login"
                    :error="form.errors.force_password_change" />
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button @click="closeModal" type="button" class="btn btn-secondary btn-sm">
                    Cancel
                </button>
                <button
                    @click="createUser"
                    type="button"
                    class="btn btn-primary btn-sm"
                    :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create user' }}
                </button>
            </div>
        </template>
    </Modal>

    <Modal :show="showImpersonateModal" @close="closeModal" size="sm">
        <template #title>Impersonate user</template>

        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                You will be logged in as <span class="font-medium text-(--color-text)">{{ userToImpersonate?.name }}</span> ({{ userToImpersonate?.email }}) and see what they see.
            </p>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button @click="closeModal" type="button" class="btn btn-secondary btn-sm">Cancel</button>
                <button @click="handleImpersonate" type="button" class="btn btn-danger btn-sm">Impersonate</button>
            </div>
        </template>
    </Modal>
</template>
