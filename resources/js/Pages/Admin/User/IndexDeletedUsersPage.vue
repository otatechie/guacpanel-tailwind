<script setup>
import Button from '@/Components/Button.vue'
import { Head, useForm, usePage, router, Link } from '@inertiajs/vue3'
import DataTable from '@js/Components/Common/Datatable.vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { computed, h, ref, watch } from 'vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import RolesBadges from '@js/Components/Common/RolesBadges.vue'
import { usePermissions } from '@js/composables/usePermissions'

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
})

const page = usePage()

const columnHelper = createColumnHelper()
const loading = ref(false)
const pagination = ref({
    current_page: props.users.current_page,
    per_page: Number(props.users.per_page),
    total: props.users.total,
})

// Mirrors AdminDeletedUsersController: restore() checks edit-users, destroy()
// and destroyAll() check delete-users.
const { hasPermission } = usePermissions()
const canRestore = hasPermission(['edit-users', 'manage-users'])
const canPurge = hasPermission(['delete-users', 'manage-users'])

const showDeleteModal = ref(false)
const userToDelete = ref(null)
const showDestroyAllUsersModal = ref(false)

const form = useForm({
    confirm_destroy_all: false,
})

const closeModal = () => {
    showDeleteModal.value = false
    userToDelete.value = null
    showDestroyAllUsersModal.value = false
    form.errors = {}
    form.reset()
}

const isSuperUser = user => {
    if (!user?.roles?.length) return false
    const role = user.roles[0]
    return role?.name?.toLowerCase() === 'superuser' || role?.slug?.toLowerCase() === 'superuser'
}

const canDeleteUser = user => {
    if (!canPurge) return false
    if (!user) return false
    if (isSuperUser(user)) return false
    return true
}

const handleRestore = user => {
    if (!canRestore) return
    if (!user?.id) return
    router.post(route('admin.user.deleted.restore', { id: user.id }))
}

const confirmDeleteUser = user => {
    if (!canDeleteUser(user)) return
    userToDelete.value = user
    showDeleteModal.value = true
}

const checkAutoDeleteStatus = user => {
    const autoDestroy = user.auto_destroy
    const autoDestroyDate = user.auto_destroy_date_full
    let val = '-'
    if (!autoDestroy) {
        val = 'Disabled'
    } else if (autoDestroy && !autoDestroyDate) {
        val = 'Date Not Set'
    } else if (autoDestroy && autoDestroyDate) {
        val = autoDestroyDate
    }
    return val
}

// Flat rows the modal renders directly. Was a hand-built <dl> of hardcoded
// gray-* pairs, which is why it drifted from every other dialog.
const userDetails = computed(() => {
    const u = userToDelete.value
    if (!u) return []
    return [
        { label: 'Role', roles: u.roles },
        { label: 'Verified', value: u.email_verified_at ? 'Yes' : 'No' },
        { label: 'Disabled', value: u.disable_account ? 'Yes' : 'No' },
        { label: 'Created', value: u.created_at_full },
        { label: 'Deleted', value: u.deleted_at_full },
        { label: 'Auto-delete', value: checkAutoDeleteStatus(u) },
    ]
})

const destroyUser = () => {
    if (!userToDelete.value?.id) return
    if (!canDeleteUser(userToDelete.value)) return

    router.delete(route('admin.user.deleted.destroy', { id: userToDelete.value.id }), {
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

const openDestroyAllUsersModal = () => {
    if (!canPurge) return
    form.errors = {}
    form.reset()
    showDestroyAllUsersModal.value = true
}

const destroyAllUsers = () => {
    if (!canPurge) return
    if (!form.confirm_destroy_all) {
        form.errors.confirm_destroy_all = 'The confirm destroy all field must be accepted.'
        return
    }

    form.post(route('admin.user.deleted.destroy-all'), {
        preserveScroll: true,
        onSuccess: () => {
            showDestroyAllUsersModal.value = false
            form.reset()
        },
        onError: errors => {
            //
        },
    })
}

const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => h('span', info.getValue() || '-'),
    }),
    columnHelper.accessor('email', {
        header: 'Email',
        cell: info => h('span', info.getValue() || '-'),
    }),
    columnHelper.accessor('role', {
        header: 'Role',
        cell: info => {
            const roleName = info.row.original.roles?.[0]?.name || 'No Role'
            return h(
                'span',
                {
                    class: 'px-1 py-1 text-xs capitalize rounded-md inline-flex items-center justify-center bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                },
                roleName
            )
        },
    }),
    columnHelper.accessor('email_verified_at', {
        header: 'Verified',
        cell: info => h('span', info.getValue() ? 'Yes' : 'No'),
    }),
    columnHelper.accessor('disable_account', {
        header: 'Disabled',
        cell: info => h('span', info.getValue() ? 'Yes' : 'No'),
    }),
    columnHelper.accessor('created_at_formatted', {
        header: 'Created At',
        cell: info => h('span', info.getValue() || '-'),
    }),
    columnHelper.accessor('deleted_at_formatted', {
        header: 'Deleted At',
        cell: info => h('span', info.getValue() || '-'),
    }),
    columnHelper.accessor('auto_destroy', {
        header: 'Auto Destroy On',
        cell: info => {
            const val = checkAutoDeleteStatus(info.row.original)
            return h(
                'span',
                {
                    class: 'text-xs',
                },
                val
            )
        },
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        cell: info => {
            const user = info.row.original
            if (!user?.id) return null

            const editButton = h(
                'button',
                {
                    class: 'p-2 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
                    onClick: () => handleRestore(user),
                    type: 'button',
                    title: 'Restore user',
                },
                [
                    h('span', { class: 'sr-only' }, 'Restore user'),
                    h(
                        'svg',
                        {
                            class: 'h-4 w-4',
                            xmlns: 'http://www.w3.org/2000/svg',
                            fill: 'none',
                            viewBox: '0 0 24 24',
                            stroke: 'currentColor',
                            'stroke-width': '1.5',
                            'aria-hidden': 'true',
                        },
                        [
                            h('path', {
                                'stroke-linecap': 'round',
                                'stroke-linejoin': 'round',
                                d: 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
                            }),
                        ]
                    ),
                ]
            )

            const deleteButton = h(
                'button',
                {
                    class: 'p-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg cursor-pointer hover:scale-105 transition-all duration-200',
                    onClick: () => confirmDeleteUser(user),
                    type: 'button',
                    title: 'Destroy user',
                },
                [
                    h('span', { class: 'sr-only' }, 'Destroy user'),
                    h(
                        'svg',
                        {
                            class: 'h-4 w-4',
                            fill: 'none',
                            stroke: 'currentColor',
                            viewBox: '0 0 24 24',
                            'aria-hidden': 'true',
                        },
                        [
                            h('path', {
                                'stroke-linecap': 'round',
                                'stroke-linejoin': 'round',
                                'stroke-width': '2',
                                d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
                            }),
                        ]
                    ),
                ]
            )

            return h(
                'div',
                {
                    class: 'flex items-center gap-2 justify-end',
                },
                [canRestore && editButton, canDeleteUser(user) && deleteButton].filter(Boolean)
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
    <Head title="Deleted users" />
    <main class="mx-auto max-w-4xl" aria-labelledby="deleted-users">
        <PageHeader
            title="Deleted users"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'User management', href: route('admin.user.index') },
                { label: 'Deleted users' },
            ]">
            <template #actions>
                <Button
                    v-if="canPurge"
                    variant="danger"
                    size="sm"
                    @click="openDestroyAllUsersModal">
                    Destroy all
                </Button>
            </template>
        </PageHeader>

        <!-- No card. The table already has its own border; wrapping it in a
             second bordered box is decoration, not structure. -->
        <DataTable
            :data="users.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            empty-message="No deleted users"
            empty-description="Users appear here after they are deleted, until they are erased."
            export-file-name="deleted_users"
            @update:pagination="pagination = $event" />
    </main>

    <Modal
        :show="showDeleteModal"
        size="md"
        description="This erases the account and everything attached to it. It cannot be undone."
        @close="closeModal">
        <template #title>Destroy user</template>

        <template #default>
            <p v-if="userToDelete" class="text-foreground text-sm font-medium">
                {{ userToDelete.name }}
                <span class="text-muted-foreground font-normal">· {{ userToDelete.email }}</span>
            </p>

            <dl v-if="userToDelete" class="divide-border border-border mt-4 divide-y border-t">
                <div
                    v-for="detail in userDetails"
                    :key="detail.label"
                    class="flex justify-between gap-4 py-2">
                    <dt class="text-muted-foreground text-xs">{{ detail.label }}</dt>
                    <dd class="text-foreground text-right text-xs">
                        <RolesBadges v-if="detail.roles" :roles="detail.roles" />
                        <template v-else>{{ detail.value }}</template>
                    </dd>
                </div>
            </dl>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button variant="danger" size="sm" @click="destroyUser">Destroy user</Button>
            </div>
        </template>
    </Modal>

    <Modal
        :show="showDestroyAllUsersModal"
        size="sm"
        description="Every deleted user is erased permanently. This cannot be undone."
        @close="closeModal">
        <template #title>Destroy all deleted users</template>

        <template #default>
            <!-- The count is the fact that decides this, and it was never shown.
                 One statement of the consequence, in the description, is enough. -->
            <p class="text-foreground text-sm font-medium">
                {{ pagination.total }}
                {{ pagination.total === 1 ? 'user' : 'users' }} will be erased
            </p>
            <div class="mt-4">
                <FormCheckbox
                    v-model="form.confirm_destroy_all"
                    label="I understand this cannot be undone"
                    :error="form.errors.confirm_destroy_all" />
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button
                    variant="danger"
                    size="sm"
                    :disabled="form.processing || !form.confirm_destroy_all"
                    @click="destroyAllUsers">
                    {{ form.processing ? 'Destroying...' : 'Destroy all' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
