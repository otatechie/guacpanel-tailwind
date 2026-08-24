<script setup>
import Button from '@/Components/Button.vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, onBeforeUnmount } from 'vue'
import Default from '@js/Layouts/Default.vue'
import { usePermissions } from '@js/composables/usePermissions'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Badge from '@js/Components/Badge.vue'
import { formatPermissionName } from '@js/utils/permissions'
import Alert from '@js/Components/Notifications/Alert.vue'
import { XIcon } from '@lucide/vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    user: Object,
    roles: Object,
    rolePermissionCount: { type: Number, default: 0 },
})

const page = usePage()
const emailVerificationEnabled = computed(() => page.props.settings?.emailVerificationEnabled)
const currentUser = computed(() => page.props.auth?.user)
const isCurrentUser = computed(() => currentUser.value?.id == props.user?.id)

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.roles?.[0]?.id || '',
    force_password_change: Boolean(props.user.force_password_change) || false,
    disable_account: Boolean(props.user.disable_account) || false,
    permissions: props.user.permissions?.map(p => p.id) || [],
    auto_destroy: Boolean(props.user.auto_destroy) || false,
})

// Reaching this page needs edit-users; deleting needs delete-users. Without
// this the button is offered to anyone who can edit, and 403s on click.
const { hasPermission } = usePermissions()
const canDelete = hasPermission(['delete-users', 'manage-users'])

const showDeleteModal = ref(false)
const showToggleVerifyModal = ref(false)
const showSendVerificationModal = ref(false)
const verificationEmailSent = ref(false)
const deleting = ref(false)

/* Save sits below the form, so it is easy to wander off with edits pending.
   Guards both the in-app links and the browser's own navigation. */
const warnIfDirty = event => {
    if (form.isDirty) event.preventDefault()
}

const stopDirtyGuard = router.on('before', event => {
    if (!form.isDirty || deleting.value) return
    if (!window.confirm('You have unsaved changes. Leave without saving?')) {
        event.preventDefault()
    }
})

window.addEventListener('beforeunload', warnIfDirty)

onBeforeUnmount(() => {
    stopDirtyGuard()
    window.removeEventListener('beforeunload', warnIfDirty)
})

/* The 30-checkbox tree is gone: capability belongs to roles, and per-user grants
   are how an RBAC model rots — a year on, nobody can say why someone can do
   something. What stays is a read-only record of grants made directly in the
   past, because enforcement still honours them and hiding them would be worse
   than showing them. Assigning happens on the roles screen. */
const directPermissions = computed(() =>
    (props.user.permissions || []).filter(p => form.permissions.includes(p.id))
)

const removeDirectPermission = id => {
    const i = form.permissions.indexOf(id)
    if (i > -1) form.permissions.splice(i, 1)
}

const closeModal = () => {
    showDeleteModal.value = false
    showSendVerificationModal.value = false
    showToggleVerifyModal.value = false
}

const submit = () => {
    form.put(route('admin.user.update', props.user.id), { preserveScroll: true })
}

/* Its own request, not the edit form's: form.delete() shared `processing`, so
   the Save button read "Saving..." during a deletion and the DELETE carried the
   whole edit payload as its body. */
const deleteUser = () => {
    deleting.value = true
    router.delete(route('admin.user.destroy', props.user.id), {
        onFinish: () => {
            deleting.value = false
            showDeleteModal.value = false
        },
    })
}

const toggleVerified = () => {
    form.post(route('admin.user.verification.toggle', { user: props.user.id }), {
        onSuccess: () => {
            showToggleVerifyModal.value = false
            verificationEmailSent.value = false
        },
    })
}

const sendVerificationEmail = () => {
    form.post(route('admin.user.verification.send', { user: props.user.id }), {
        onSuccess: () => {
            showSendVerificationModal.value = false
            verificationEmailSent.value = true
        },
    })
}
</script>

<template>
    <Head :title="`${props.user.name}`" />

    <main class="mx-auto max-w-4xl" aria-labelledby="edit-user">
        <PageHeader
            id="edit-user"
            :title="props.user.name"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Users', href: route('admin.user.index') },
                { label: props.user.name },
            ]" />

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Sections, not cards: every other admin page (users list, settings,
                 backups) groups with a muted heading and a rule. This page was the
                 only one boxing its groups, so arriving from the users list
                 changed visual language mid-flow. -->
            <section>
                <h2 class="text-muted-foreground mb-3 text-xs font-medium">Profile</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <!-- Both are `required` in the update() rules. -->
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
                    <!-- Was a <p> with a border and input padding: non-interactive
                         content dressed as a form control invites clicks that do
                         nothing. The users list already shows roles as badges. -->
                    <div v-if="props.user.is_superuser">
                        <p class="text-muted-foreground mb-1.5 text-xs font-medium">Role</p>
                        <div class="flex h-8 items-center gap-2">
                            <!-- Neutral rather than RoleBadge's danger variant:
                                 red in a form-field slot reads as an error. -->
                            <Badge
                                v-if="props.user.roles?.[0]"
                                variant="neutral"
                                class="capitalize">
                                {{ props.user.roles[0].name }}
                            </Badge>
                            <span v-else class="text-muted-foreground text-sm">No role</span>
                            <span class="text-muted-foreground text-xs">Cannot be changed</span>
                        </div>
                    </div>
                    <FormSelect
                        v-else
                        v-model="form.role"
                        :options="roles.data"
                        option-label="name"
                        option-value="id"
                        name="role"
                        label="Role"
                        :error="form.errors.role" />
                </div>

                <!-- This was a card of its own holding one sentence. It is a
                     footnote on the Role field, so it lives under it. -->
                <p class="text-muted-foreground mt-2 text-sm">
                    Access comes from the role.
                    <template v-if="props.user.roles?.[0]">
                        <span class="text-foreground capitalize">
                            {{ props.user.roles[0].name }}
                        </span>
                        grants {{ rolePermissionCount }}
                        {{ rolePermissionCount === 1 ? 'permission' : 'permissions' }}.
                    </template>
                    <template v-else>This account has no role, so it grants nothing.</template>
                    <Link
                        :href="
                            route('admin.permission.role.index', {
                                role: props.user.roles?.[0]?.id,
                            })
                        "
                        class="text-primary underline-offset-2 hover:underline">
                        Manage role permissions
                    </Link>
                </p>

                <!-- Email verification -->
                <!-- Spacing, not a rule: the section separators are the only rules
                     on the page, so a sub-divider at the same weight flattened the
                     hierarchy. -->
                <div
                    v-if="emailVerificationEnabled"
                    class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2">
                    <Badge v-if="props.user.email_verified_at_full" dot variant="success">
                        Verified {{ props.user.email_verified_at_formatted }}
                    </Badge>
                    <Badge v-else dot variant="warning">Not verified</Badge>
                    <span
                        v-if="verificationEmailSent"
                        class="text-xs text-green-600 dark:text-green-400">
                        Verification sent
                    </span>
                    <!-- These were styled exactly like the status text beside them
                         and only separated on hover, though one of them sends a
                         real email. -->
                    <div class="flex gap-2">
                        <Button
                            variant="ghost"
                            size="xs"
                            :disabled="isCurrentUser"
                            @click="showToggleVerifyModal = true">
                            {{ props.user.email_verified_at ? 'Unverify' : 'Mark verified' }}
                        </Button>
                        <Button
                            v-if="!props.user.email_verified_at"
                            variant="ghost"
                            size="xs"
                            :disabled="verificationEmailSent"
                            @click="showSendVerificationModal = true">
                            Send verification
                        </Button>
                    </div>
                    <p v-if="isCurrentUser" class="text-muted-foreground w-full text-xs">
                        Cannot modify your own verification
                    </p>
                </div>
            </section>

            <!-- Account controls -->
            <section class="border-border border-t pt-5">
                <h2 class="text-muted-foreground mb-3 text-xs font-medium">Account</h2>
                <div class="space-y-3">
                    <FormCheckbox
                        v-model="form.disable_account"
                        :disabled="props.user.is_superuser"
                        label="Disable account"
                        :help="props.user.is_superuser ? 'Protected' : 'Blocks all access'"
                        :error="form.errors.disable_account" />
                    <FormCheckbox
                        v-model="form.force_password_change"
                        :disabled="props.user.is_superuser"
                        label="Force password reset"
                        :help="props.user.is_superuser ? 'Protected' : 'Required on next login'"
                        :error="form.errors.force_password_change" />
                    <FormCheckbox
                        v-model="form.auto_destroy"
                        label="Auto-delete after soft delete"
                        help="Permanently removes after retention period"
                        :error="form.errors.auto_destroy" />
                </div>
                <p v-if="props.user.restore_date_full" class="text-muted-foreground mt-4 text-xs">
                    Previously restored on {{ props.user.restore_date_full }}
                </p>
            </section>

            <!-- Only when the account actually has direct grants. The ordinary
                 case is covered by the line under Role, so an empty section here
                 would be a heading explaining a thing nobody has done. -->
            <section v-if="directPermissions.length" class="border-border border-t pt-5">
                <h2 class="text-muted-foreground mb-3 text-xs font-medium">Direct permissions</h2>

                <Alert type="warning">
                    {{ directPermissions.length }}
                    {{ directPermissions.length === 1 ? 'permission was' : 'permissions were' }}
                    granted directly to this account, outside its role. They still take effect.
                    Prefer changing the role.
                </Alert>

                <ul class="divide-border mt-3 divide-y">
                    <li
                        v-for="perm in directPermissions"
                        :key="perm.id"
                        class="flex items-center justify-between gap-4 py-2.5">
                        <div class="min-w-0">
                            <p class="text-foreground text-sm font-medium">
                                {{ formatPermissionName(perm.name) }}
                            </p>
                        </div>
                        <button
                            type="button"
                            class="text-muted-foreground hover:bg-muted hover:text-foreground focus-visible:outline-ring shrink-0 cursor-pointer rounded-md p-1.5 transition-colors focus-visible:outline-2"
                            :aria-label="`Remove ${formatPermissionName(perm.name)}`"
                            :title="`Remove ${formatPermissionName(perm.name)}`"
                            @click="removeDirectPermission(perm.id)">
                            <XIcon class="h-3.5 w-3.5" aria-hidden="true" />
                        </button>
                    </li>
                </ul>
                <p class="text-muted-foreground mt-2 text-xs">Removals apply when you save.</p>

                <p
                    v-if="form.errors.permissions"
                    class="mt-2 text-xs text-red-600 dark:text-red-400">
                    {{ form.errors.permissions }}
                </p>
            </section>

            <!-- Sticks only while there is something to save, so the button and
                 the "unsaved" marker stay reachable from wherever the edit was
                 made instead of sitting off-screen at the bottom. -->
            <div
                class="sticky bottom-0 flex items-center justify-between gap-3 py-3"
                :class="
                    form.isDirty
                        ? 'bg-background/95 border-border -mx-3 border-t px-3 backdrop-blur'
                        : ''
                ">
                <Button
                    variant="primary"
                    size="sm"
                    type="submit"
                    :disabled="form.processing"
                    :aria-busy="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save' }}
                </Button>
                <span v-if="form.isDirty" class="text-muted-foreground text-xs">
                    Unsaved changes
                </span>
            </div>
        </form>

        <!-- Out of the form and away from Save: a destructive action does not
             belong on the same row as the primary one. -->
        <section
            v-if="canDelete && !props.user.is_superuser"
            class="border-border mt-8 border-t pt-5">
            <h2 class="text-muted-foreground mb-3 text-xs font-medium">Delete account</h2>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-muted-foreground max-w-lg text-sm">
                    Removes {{ props.user.name }}'s access immediately. Recoverable from the deleted
                    users list until its auto-delete date.
                </p>
                <Button variant="danger" size="sm" @click="showDeleteModal = true">
                    Delete account
                </Button>
            </div>
        </section>
    </main>

    <!-- Delete modal -->
    <Modal :show="showDeleteModal" @close="closeModal" size="sm">
        <template #title>Delete account</template>
        <template #default>
            <p class="text-foreground text-sm font-medium">
                {{ props.user.name }}
                <span class="text-muted-foreground font-normal">· {{ props.user.email }}</span>
            </p>
            <p class="text-muted-foreground mt-2 text-sm">
                They lose access immediately. Recoverable from the deleted users list until its
                auto-delete date.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button variant="danger" size="sm" :disabled="deleting" @click="deleteUser">
                    {{ deleting ? 'Deleting...' : 'Delete' }}
                </Button>
            </div>
        </template>
    </Modal>

    <!-- Toggle verify modal -->
    <Modal :show="showToggleVerifyModal" @close="closeModal" size="sm">
        <template #title>{{ props.user.email_verified_at ? 'Unverify' : 'Verify' }} email</template>
        <template #default>
            <p class="text-muted-foreground text-sm">
                {{
                    props.user.email_verified_at ? 'Remove verification from' : 'Mark as verified:'
                }}
                <span class="text-foreground font-medium">{{ props.user.email }}</span>
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button
                    variant="primary"
                    size="sm"
                    :disabled="form.processing"
                    @click="toggleVerified">
                    {{ form.processing ? 'Confirming...' : 'Confirm' }}
                </Button>
            </div>
        </template>
    </Modal>

    <!-- Send verification modal -->
    <Modal :show="showSendVerificationModal" @close="closeModal" size="sm">
        <template #title>Send verification email</template>
        <template #default>
            <p class="text-muted-foreground text-sm">
                Send verification email to
                <span class="text-foreground font-medium">{{ props.user.email }}</span>
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <Button variant="secondary" size="sm" @click="closeModal">Cancel</Button>
                <Button
                    variant="primary"
                    size="sm"
                    :disabled="form.processing"
                    @click="sendVerificationEmail">
                    {{ form.processing ? 'Sending...' : 'Send' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
