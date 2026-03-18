<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    user: Object,
    roles: Object,
    permissions: Object,
    categoryMap: Object,
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

const showDeleteModal = ref(false)
const showToggleVerifyModal = ref(false)
const showSendVerificationModal = ref(false)
const verificationEmailSent = ref(false)
const searchQuery = ref('')

// Permissions
const allPermissions = computed(() => props.permissions?.data || [])
const selectedCount = computed(() => form.permissions.length)
const totalCount = computed(() => allPermissions.value.length)
const expandedGroups = ref(new Set())

// Auto-group by suffix: view-users, manage-users, delete-users → "Users"
const groupedPermissions = computed(() => {
    const groups = {}
    const perms = allPermissions.value.filter(p => {
        if (!searchQuery.value) return true
        return p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    })
    perms.forEach(p => {
        const parts = p.name.split('-')
        const group = parts.length > 1 ? parts.slice(1).join(' ') : 'general'
        const label = group.charAt(0).toUpperCase() + group.slice(1)
        if (!groups[label]) groups[label] = []
        groups[label].push(p)
    })
    // Sort groups alphabetically
    return Object.fromEntries(Object.entries(groups).sort(([a], [b]) => a.localeCompare(b)))
})

const toggleGroup = name => {
    if (expandedGroups.value.has(name)) expandedGroups.value.delete(name)
    else expandedGroups.value.add(name)
}

const groupSelectedCount = perms => perms.filter(p => form.permissions.includes(p.id)).length

const isSelected = id => form.permissions.includes(id)
const togglePermission = id => {
    const i = form.permissions.indexOf(id)
    if (i > -1) form.permissions.splice(i, 1)
    else form.permissions.push(id)
}

// "manage-security-settings" → "Manage"
const formatAction = name => {
    const action = name.split('-')[0]
    return action.charAt(0).toUpperCase() + action.slice(1)
}

const closeModal = () => {
    showDeleteModal.value = false
    showSendVerificationModal.value = false
    showToggleVerifyModal.value = false
}

const submit = () => {
    form.put(route('admin.user.update', props.user.id), { preserveScroll: true })
}

const deleteUser = () => {
    form.delete(route('admin.user.destroy', props.user.id), {
        onSuccess: () => { showDeleteModal.value = false },
    })
}

const toggleVerified = () => {
    form.post(route('admin.user.verification.toggle', { user: props.user.id }), {
        onSuccess: () => { showToggleVerifyModal.value = false; verificationEmailSent.value = true },
    })
}

const sendVerificationEmail = () => {
    form.post(route('admin.user.verification.send', { user: props.user.id }), {
        onSuccess: () => { showSendVerificationModal.value = false; verificationEmailSent.value = true },
    })
}
</script>

<template>
    <Head :title="`${props.user.name}`" />

    <main class="mx-auto max-w-7xl" aria-labelledby="edit-user">
        <PageHeader
            :title="props.user.name"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Users', href: route('admin.user.index') },
                { label: props.user.name },
            ]" />

        <form @submit.prevent="submit" class="max-w-3xl space-y-5">

            <!-- Profile -->
            <div class="card px-5 py-4">
                <h2 class="text-base font-medium text-(--color-text)">Profile</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <FormInput v-model="form.name" label="Name" :error="form.errors.name" name="name" />
                    <FormInput v-model="form.email" label="Email" type="email" :error="form.errors.email" name="email" />
                    <div v-if="props.user.is_superuser">
                        <p class="mb-1.5 text-xs font-medium text-(--color-text-muted)">Role</p>
                        <p class="rounded-md border border-(--color-border) bg-(--color-surface-muted) px-3 py-2 text-sm capitalize text-(--color-text)">
                            {{ props.user.roles?.[0]?.name || 'No role' }}
                        </p>
                        <p class="mt-1 text-xs text-(--color-text-muted)">Protected</p>
                    </div>
                    <FormSelect v-else v-model="form.role" :options="roles.data" option-label="name" option-value="id" name="role" label="Role" :error="form.errors.role" />
                </div>

                <!-- Email verification -->
                <div v-if="emailVerificationEnabled" class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-(--card-border) pt-4">
                    <span v-if="props.user.email_verified_at_full" class="flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Verified {{ props.user.email_verified_at_formatted }}
                    </span>
                    <span v-else class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Not verified
                    </span>
                    <span v-if="verificationEmailSent" class="text-xs text-green-600 dark:text-green-400">Verification sent</span>
                    <div class="flex gap-2">
                        <button type="button" class="text-xs text-(--color-text-muted) hover:text-(--color-text)" :disabled="isCurrentUser" @click="showToggleVerifyModal = true">
                            {{ props.user.email_verified_at ? 'Unverify' : 'Mark verified' }}
                        </button>
                        <button v-if="!props.user.email_verified_at" type="button" class="text-xs text-(--color-text-muted) hover:text-(--color-text)" :disabled="verificationEmailSent" @click="showSendVerificationModal = true">
                            Send verification
                        </button>
                    </div>
                    <p v-if="isCurrentUser" class="w-full text-xs text-(--color-text-muted)">Cannot modify your own verification</p>
                </div>
            </div>

            <!-- Account controls -->
            <div class="card px-5 py-4">
                <h2 class="text-base font-medium text-(--color-text)">Account</h2>
                <div class="mt-4 space-y-3">
                    <FormCheckbox v-model="form.disable_account" :disabled="props.user.is_superuser" label="Disable account" :help="props.user.is_superuser ? 'Protected' : 'Blocks all access'" :error="form.errors.disable_account" />
                    <FormCheckbox v-model="form.force_password_change" :disabled="props.user.is_superuser" label="Force password reset" :help="props.user.is_superuser ? 'Protected' : 'Required on next login'" :error="form.errors.force_password_change" />
                    <FormCheckbox v-model="form.auto_destroy" label="Auto-delete after soft delete" help="Permanently removes after retention period" :error="form.errors.auto_destroy" />
                </div>
                <div v-if="props.user.restore_date_full" class="mt-4 border-t border-(--card-border) pt-3">
                    <p class="text-xs text-(--color-text-muted)">Previously restored on {{ props.user.restore_date_full }}</p>
                </div>
            </div>

            <!-- Permissions -->
            <div class="card px-5 py-4">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-base font-medium text-(--color-text)">Permissions</h2>
                    <span class="text-xs tabular-nums text-(--color-text-muted)">{{ selectedCount }}/{{ totalCount }}</span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Filter..."
                        class="ml-auto w-36 rounded-md border border-(--color-border-strong) bg-(--color-surface) px-2.5 py-1 text-xs text-(--color-text) placeholder-(--color-text-muted) focus:border-(--primary-color) focus:ring-1 focus:ring-(--primary-color)/20 focus:outline-none" />
                </div>

                <Alert v-if="!props.user.is_superuser" type="info" class="mt-3">
                    Direct permissions override role-based permissions.
                </Alert>

                <div class="mt-3 divide-y divide-(--card-border) rounded-lg border border-(--card-border)">
                    <div v-for="(perms, group) in groupedPermissions" :key="group">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between px-3 py-2 text-left transition-colors hover:bg-(--color-surface-muted)"
                            @click="toggleGroup(group)">
                            <div class="flex items-center gap-2">
                                <svg
                                    :class="['h-3 w-3 text-(--color-text-muted) transition-transform duration-150', expandedGroups.has(group) ? 'rotate-90' : '']"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                                <span class="text-sm font-medium text-(--color-text)">{{ group }}</span>
                            </div>
                            <span class="text-xs tabular-nums text-(--color-text-muted)">
                                {{ groupSelectedCount(perms) }}/{{ perms.length }}
                            </span>
                        </button>
                        <div v-if="expandedGroups.has(group)" class="grid grid-cols-2 gap-x-2 border-t border-(--card-border) px-3 py-2 sm:grid-cols-3">
                            <label
                                v-for="perm in perms"
                                :key="perm.id"
                                :for="`perm-${perm.id}`"
                                class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 transition-colors hover:bg-(--color-surface-muted)"
                                :class="isSelected(perm.id) ? 'text-(--color-text)' : 'text-(--color-text-muted)'">
                                <input
                                    :id="`perm-${perm.id}`"
                                    type="checkbox"
                                    :checked="isSelected(perm.id)"
                                    @change="togglePermission(perm.id)"
                                    class="h-3.5 w-3.5 shrink-0 rounded border-(--color-border-strong) text-(--primary-color) focus:ring-(--primary-color)/20" />
                                <span class="text-sm" :class="isSelected(perm.id) ? 'font-medium' : ''">{{ formatAction(perm.name) }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <p v-if="Object.keys(groupedPermissions).length === 0 && searchQuery" class="py-4 text-center text-xs text-(--color-text-muted)">No match</p>
                <p v-if="form.errors.permissions" class="mt-2 text-xs text-red-600 dark:text-red-400">{{ form.errors.permissions }}</p>
            </div>

            <!-- Save -->
            <div class="flex items-center justify-between">
                <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing" :aria-busy="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save' }}
                </button>
                <button v-if="!props.user.is_superuser" type="button" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" @click="showDeleteModal = true">
                    Delete account
                </button>
            </div>
        </form>
    </main>

    <!-- Delete modal -->
    <Modal :show="showDeleteModal" @close="closeModal" size="sm">
        <template #title>Delete account</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                Permanently delete <span class="font-medium text-(--color-text)">{{ props.user.name }}</span> and all associated data. Recoverable until auto-delete date if set.
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="closeModal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" :disabled="form.processing" @click="deleteUser">
                    {{ form.processing ? 'Deleting...' : 'Delete' }}
                </button>
            </div>
        </template>
    </Modal>

    <!-- Toggle verify modal -->
    <Modal :show="showToggleVerifyModal" @close="closeModal" size="sm">
        <template #title>{{ props.user.email_verified_at ? 'Unverify' : 'Verify' }} email</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                {{ props.user.email_verified_at ? 'Remove verification from' : 'Mark as verified:' }}
                <span class="font-medium text-(--color-text)">{{ props.user.email }}</span>
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="closeModal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" :disabled="form.processing" @click="toggleVerified">
                    {{ form.processing ? 'Confirming...' : 'Confirm' }}
                </button>
            </div>
        </template>
    </Modal>

    <!-- Send verification modal -->
    <Modal :show="showSendVerificationModal" @close="closeModal" size="sm">
        <template #title>Send verification email</template>
        <template #default>
            <p class="text-sm text-(--color-text-muted)">
                Send verification email to <span class="font-medium text-(--color-text)">{{ props.user.email }}</span>
            </p>
        </template>
        <template #footer>
            <div class="flex justify-end gap-3">
                <button type="button" class="btn btn-sm btn-secondary" @click="closeModal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" :disabled="form.processing" @click="sendVerificationEmail">
                    {{ form.processing ? 'Sending...' : 'Send' }}
                </button>
            </div>
        </template>
    </Modal>
</template>
