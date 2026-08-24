<script setup>
import Button from '@/Components/Button.vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import DataTable from '@js/Components/Common/Datatable.vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Sheet from '@js/Components/Notifications/Sheet.vue'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import RolesBadges from '@js/Components/Common/RolesBadges.vue'
import Badge from '@js/Components/Badge.vue'
import RowActions from '@js/Components/Common/RowActions.vue'
import { SquarePenIcon, Trash2Icon, VenetianMaskIcon } from '@lucide/vue'
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
    deletedUsers: {
        type: Number,
        default: 0,
    },
    /* The server's current search/sort/status. Every request the table makes is
       rebuilt from this — without it, sorting wiped the search and paging wiped
       both. */
    filters: {
        type: Object,
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

const showEditSheet = ref(false)
const userBeingEdited = ref(null)
const showDeleteModal = ref(false)
const userToDelete = ref(null)
const showCreateUserSheet = ref(false)

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    force_password_change: false,
})

/* Its own form, so an open create sheet and an open edit sheet never share
   errors or a processing flag. Identity only — account flags, permissions and
   deletion stay on the full page, which this sheet links to. */
const editForm = useForm({ name: '', email: '', role: '' })

const closeModal = () => {
    showDeleteModal.value = false
    userToDelete.value = null
}

const closeSheet = () => {
    showCreateUserSheet.value = false
    form.reset()
}

/* Mirrors the abort_unless checks in AdminUserController. The server is still
   the authority; this stops the page offering controls that would bounce off a
   403, and stops a view-only admin opening an edit sheet they cannot save. */
const { hasPermission } = usePermissions()
const canEdit = hasPermission(['edit-users', 'manage-users'])
const canDelete = hasPermission(['delete-users', 'manage-users'])
const canImpersonate = hasPermission('impersonate-users')
const canCreate = hasPermission(['create-users', 'manage-users'])

const isSuperUser = user => {
    if (!user?.roles?.length) return false
    const role = user.roles[0]
    return role?.name?.toLowerCase() === 'superuser' || role?.slug?.toLowerCase() === 'superuser'
}

const canDeleteUser = user => {
    if (!user) return false
    if (!canDelete) return false
    if (isSuperUser(user)) return false
    return true
}

const openEditSheet = user => {
    if (!user?.id || !canEdit) return
    userBeingEdited.value = user
    editForm.clearErrors()
    editForm.name = user.name || ''
    editForm.email = user.email || ''
    editForm.role = user.roles?.[0]?.id || ''
    showEditSheet.value = true
}

const closeEditSheet = () => {
    showEditSheet.value = false
    userBeingEdited.value = null
    editForm.reset()
    editForm.clearErrors()
}

/* A plain <Link> here unmounted the sheet and took the typing with it. The
   admin clicking this is going *to* the account controls, not away from their
   edit, so losing it silently is the wrong default. A button rather than a Link
   because Inertia's Link runs its own click handler regardless of
   preventDefault, so it cannot be guarded. */
const openFullSettings = () => {
    if (editForm.isDirty && !window.confirm('You have unsaved changes. Leave without saving?')) {
        return
    }
    router.visit(route('admin.user.edit', { id: userBeingEdited.value.id }))
}

const saveEdit = () => {
    editForm.put(route('admin.user.update', { id: userBeingEdited.value.id }), {
        preserveScroll: true,
        onSuccess: () => closeEditSheet(),
    })
}

/* No confirmation step: ImpersonationBanner pins an "Exit" button to the bottom
   of every page for the whole session, so this is reversible in one click. A
   dialog in front of an action with a permanent undo is ceremony. */
const impersonate = user => {
    if (!user?.id || isSuperUser(user)) return
    router.post(route('admin.user.impersonate.start', { user: user.id }))
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

const openCreateSheet = () => {
    if (!canCreate) return
    showCreateUserSheet.value = true
}

const createUser = () => {
    form.post(route('admin.user.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateUserSheet.value = false
            form.reset()
        },
    })
}

/* `locked` was already branched on here but the controller never sent
   `account_locked`, so a locked-out account rendered as a green "Active". */
const STATUS_META = {
    disabled: { variant: 'danger', label: 'Disabled' },
    locked: { variant: 'warning', label: 'Locked' },
    unverified: { variant: 'warning', label: 'Unverified' },
    active: { variant: 'success', label: 'Active' },
}

const statusOf = user => {
    if (user.disable_account) return STATUS_META.disabled
    if (user.account_locked) return STATUS_META.locked
    if (!user.email_verified_at) return STATUS_META.unverified
    return STATUS_META.active
}

const STATUS_OPTIONS = [
    { id: '', name: 'All statuses' },
    { id: 'active', name: 'Active' },
    { id: 'unverified', name: 'Unverified' },
    { id: 'locked', name: 'Locked' },
    { id: 'disabled', name: 'Disabled' },
]

const status = ref(props.filters?.status || '')

/* Every request the table makes now starts from the server's own filters, so
   changing one does not silently discard the others. */
const visitWith = params => {
    const query = Object.fromEntries(
        Object.entries({ ...props.filters, ...params }).filter(
            ([, value]) => value !== '' && value !== null && value !== undefined
        )
    )

    loading.value = true
    router.get(route('admin.user.index'), query, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => (loading.value = false),
    })
}

watch(status, value => visitWith({ status: value, page: 1 }))

const columns = [
    columnHelper.accessor('name', {
        header: 'Name',
        cell: info => {
            const user = info.row.original
            return h('div', { class: 'min-w-0' }, [
                h('span', { class: 'text-sm font-medium text-foreground' }, user.name || '-'),
                h('span', { class: 'ml-2 text-xs text-muted-foreground' }, user.email),
            ])
        },
    }),
    columnHelper.accessor('role', {
        header: 'Role',
        meta: { narrow: true },
        cell: info => {
            // Was roles[0] as mono text: a second role vanished silently, and the
            // rest of the app shows roles as badges.
            const roles = info.row.original.roles || []
            if (!roles.length) {
                return h('span', { class: 'text-muted-foreground text-xs' }, 'No role')
            }
            return h('div', { class: 'flex flex-wrap gap-1' }, [h(RolesBadges, { roles })])
        },
    }),
    columnHelper.accessor('status', {
        header: 'Status',
        meta: { narrow: true },
        // Was four hand-rolled colour pairs; Badge already owns the semantic set.
        cell: info => {
            const { variant, label } = statusOf(info.row.original)
            return h(Badge, { variant }, () => label)
        },
    }),
    columnHelper.accessor('created_at_formatted', {
        header: 'Created',
        meta: { narrow: true },
        cell: info => h('span', { class: 'text-xs text-muted-foreground' }, info.getValue() || '-'),
    }),
    columnHelper.display({
        id: 'actions',
        header: '',
        /* Two icons in a row that is itself clickable were easy to mis-hit, and a
           glyph plus a hover tooltip is not a label for something as consequential
           as deleting an account. One trigger, real words behind it. */
        cell: info => {
            const user = info.row.original
            if (!user?.id) return null

            const actions = []

            if (canEdit) {
                actions.push({
                    label: 'Edit',
                    icon: SquarePenIcon,
                    onSelect: () => openEditSheet(user),
                })
            }

            if (canImpersonate && !isSuperUser(user)) {
                actions.push({
                    // A person glyph says nothing in a table of people; a mask is
                    // the impersonation convention.
                    label: 'Impersonate',
                    icon: VenetianMaskIcon,
                    onSelect: () => impersonate(user),
                })
            }

            if (canDeleteUser(user)) {
                actions.push({
                    label: 'Delete user',
                    icon: Trash2Icon,
                    variant: 'destructive',
                    onSelect: () => confirmDeleteUser(user),
                })
            }

            return h(RowActions, {
                actions,
                label: `Actions for ${user.name || user.email || 'this user'}`,
            })
        },
    }),
]

watch(
    pagination,
    newPagination =>
        visitWith({
            page: newPagination.current_page,
            per_page: Number(newPagination.per_page),
        }),
    { deep: true }
)
</script>

<template>
    <Head title="User management" />
    <main class="mx-auto max-w-4xl" aria-labelledby="users-management">
        <!-- The description only restated the title. -->
        <PageHeader
            id="users-management"
            title="User management"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'User management' },
            ]">
            <template #actions>
                <Button v-if="canCreate" variant="primary" size="sm" @click="openCreateSheet">
                    Add user
                </Button>
            </template>

            <template #bottom>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                    <!-- The Status column was readable but not actionable: no way
                         to answer "who is still unverified?". -->
                    <FormSelect
                        v-model="status"
                        :options="STATUS_OPTIONS"
                        option-label="name"
                        option-value="id"
                        name="status"
                        label="Status"
                        class="w-44" />
                    <div v-if="deletedUsers" class="flex items-center gap-3">
                        <span class="text-muted-foreground text-xs">
                            {{ deletedUsers }} deleted
                            {{ deletedUsers === 1 ? 'user' : 'users' }}
                        </span>
                        <Button
                            :as="Link"
                            variant="secondary"
                            size="xs"
                            :href="route('admin.user.deleted.index')">
                            View deleted {{ deletedUsers === 1 ? 'user' : 'users' }}
                        </Button>
                    </div>
                </div>
            </template>
        </PageHeader>

        <!-- No card. The table already has its own border; wrapping it in a
             second bordered box is decoration, not structure. -->
        <DataTable
            :data="users.data"
            :columns="columns"
            :loading="loading"
            :pagination="pagination"
            :filters="filters"
            route-name="admin.user.index"
            :row-clickable="canEdit"
            :row-label="user => `Edit ${user.name || user.email}`"
            @row-click="openEditSheet"
            empty-message="No users yet"
            empty-description="Users appear here once created."
            export-file-name="users"
            @update:pagination="pagination = $event">
            <template #empty-action>
                <Button v-if="canCreate" variant="secondary" size="sm" @click="openCreateSheet">
                    Add user
                </Button>
            </template>
        </DataTable>
    </main>

    <Modal :show="showDeleteModal" size="sm" @close="closeModal">
        <template #title>Delete user</template>

        <template #default>
            <!-- Lead with the record, then what happens to it. Both used to be
                 split across the header and the body at two different sizes. -->
            <p class="text-foreground text-sm font-medium">
                {{ userToDelete?.name }}
                <span class="text-muted-foreground font-normal">· {{ userToDelete?.email }}</span>
            </p>
            <p class="text-muted-foreground mt-2 text-sm">
                They lose access immediately. Recoverable from the deleted users list until its
                auto-delete date.
            </p>
        </template>

        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button variant="danger" size="sm" @click="deleteUser">Delete user</Button>
            </div>
        </template>
    </Modal>

    <!-- Quick edit: the fields people change most, without leaving the list.
         Account flags, direct permissions and deletion stay on the full page,
         linked from the footer. -->
    <!-- No subtitle: it showed the email, which the Email field below already
         shows, and it was bound to the record rather than the form — so it kept
         displaying the old address while you typed a new one. -->
    <Sheet :show="showEditSheet" size="md" :close-on-click-outside="false" @close="closeEditSheet">
        <!-- Not "Edit {name}": that binds to the record, so renaming leaves the
             header showing the old name while the field shows the new one. The
             populated fields identify the account, accurately. -->
        <template #title>Edit user</template>

        <template #default>
            <form id="edit-user-form" class="space-y-4" @submit.prevent="saveEdit">
                <FormInput
                    v-model="editForm.name"
                    label="Name"
                    name="edit-name"
                    id="edit-name"
                    required
                    :error="editForm.errors.name" />
                <FormInput
                    v-model="editForm.email"
                    label="Email"
                    type="email"
                    name="edit-email"
                    id="edit-email"
                    required
                    :error="editForm.errors.email" />

                <!-- A superuser's role is refused by the server, so it is shown
                     rather than offered. Neutral, not RoleBadge: superuser maps to
                     the danger variant, and dark red in a form-field slot beside
                     red required markers reads as a validation error. -->
                <div v-if="userBeingEdited && isSuperUser(userBeingEdited)">
                    <p class="text-muted-foreground mb-1.5 text-xs font-medium">Role</p>
                    <div class="flex h-8 items-center gap-2">
                        <Badge variant="neutral" class="capitalize">
                            {{ userBeingEdited.roles?.[0]?.name }}
                        </Badge>
                        <span class="text-muted-foreground text-xs">Cannot be changed</span>
                    </div>
                </div>
                <FormSelect
                    v-else
                    v-model="editForm.role"
                    :options="props.roles?.data || []"
                    option-label="name"
                    option-value="id"
                    name="edit-role"
                    label="Role"
                    :error="editForm.errors.role" />
            </form>

            <!-- Link on its own line, not inline: Prettier wraps an inline
                 <Link> and Vue then renders the trailing full stop with a space
                 in front of it. -->
            <div class="mt-5">
                <p class="text-muted-foreground text-sm">
                    Account controls, direct permissions and deletion live on the full settings
                    page.
                </p>
                <button
                    v-if="userBeingEdited"
                    type="button"
                    class="text-primary mt-1 cursor-pointer text-sm underline-offset-2 hover:underline"
                    @click="openFullSettings">
                    Open full settings
                </button>
            </div>
        </template>

        <template #footer>
            <div class="flex w-full justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeEditSheet">Cancel</Button>
                <Button
                    type="submit"
                    form="edit-user-form"
                    variant="primary"
                    size="sm"
                    :disabled="editForm.processing">
                    {{ editForm.processing ? 'Saving...' : 'Save' }}
                </Button>
            </div>
        </template>
    </Sheet>

    <!-- A six-field form is panel work, not dialog work: the sheet gives it
         vertical room and leaves the list it came from on screen. closeSheet
         calls form.reset(), so click-outside stays off. -->
    <Sheet
        :show="showCreateUserSheet"
        size="md"
        :close-on-click-outside="false"
        description="The user can sign in as soon as you create the account."
        @close="closeSheet">
        <template #title>Create new user</template>

        <template #default>
            <!-- Single column: the panel is ~28rem, and a two-up grid put two
                 password fields side by side in half of that. -->
            <form id="create-user-form" class="space-y-4" @submit.prevent="createUser">
                <!-- Both are `required` in the store() rules; only the password
                     pair was marked, so half the required fields looked optional. -->
                <FormInput
                    v-model="form.name"
                    label="Name"
                    name="name"
                    required
                    :error="form.errors.name" />
                <FormInput
                    v-model="form.email"
                    label="Email"
                    type="email"
                    name="email"
                    required
                    :error="form.errors.email" />
                <FormInput
                    v-model="form.password"
                    label="Password"
                    name="password"
                    id="password"
                    type="password"
                    required
                    help="At least 8 characters."
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
            </form>
        </template>

        <template #footer>
            <div class="flex w-full justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeSheet">Cancel</Button>
                <!-- `form` reaches across the slot boundary, so Enter submits. -->
                <Button
                    type="submit"
                    form="create-user-form"
                    variant="primary"
                    size="sm"
                    :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create user' }}
                </Button>
            </div>
        </template>
    </Sheet>
</template>
