<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Tabs from '@js/Components/Common/Tabs.vue'
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
const isCurrentUser = computed(() => {
  if (currentUser.value?.id == props.user?.id) {
    return true
  }
  return false
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  role: props.user.roles?.[0]?.id || '',
  force_password_change: Boolean(props.user.force_password_change) || false,
  disable_account: Boolean(props.user.disable_account) || false,
  permissions: props.user.permissions?.map(permission => permission.id) || [],
  auto_destroy: Boolean(props.user.auto_destroy) || false,
  email_verified_at: props.user.email_verified_at,
})

const tabs = [
  {
    name: 'Account',
    key: 'account',
  },
  {
    name: 'Permissions',
    key: 'permissions',
  },
  {
    name: 'Administration',
    key: 'administration',
  },
]

const activeTab = ref(0)
const showDeleteModal = ref(false)
const searchQuery = ref('')
const expandedCategories = ref(new Set())

const groupedPermissions = computed(() => {
  const groups = {}
  const allPermissions = props.permissions?.data || []

  Object.keys(props.categoryMap || {}).forEach(category => {
    groups[category] = []
  })

  groups['Other'] = []

  allPermissions.forEach(permission => {
    let categorized = false
    for (const [category, patterns] of Object.entries(props.categoryMap || {})) {
      if (patterns.some(pattern => permission.name.includes(pattern))) {
        groups[category].push(permission)
        categorized = true
        break
      }
    }
    if (!categorized) {
      groups['Other'].push(permission)
    }
  })

  const filtered = {}
  Object.keys(groups).forEach(category => {
    const filteredPerms = groups[category].filter(perm => {
      if (!searchQuery.value) return true
      const query = searchQuery.value.toLowerCase()
      return perm.name.toLowerCase().includes(query)
    })
    if (filteredPerms.length > 0) {
      filtered[category] = filteredPerms
    }
  })

  return filtered
})

const selectedCount = computed(() => form.permissions.length)
const totalCount = computed(() => props.permissions?.data?.length || 0)

const toggleCategory = category => {
  if (expandedCategories.value.has(category)) {
    expandedCategories.value.delete(category)
  } else {
    expandedCategories.value.add(category)
  }
}

const expandAll = () => {
  Object.keys(groupedPermissions.value).forEach(cat => {
    expandedCategories.value.add(cat)
  })
}

const collapseAll = () => {
  expandedCategories.value.clear()
}

const isSelected = permissionId => {
  return form.permissions.includes(permissionId)
}

const togglePermission = permissionId => {
  const index = form.permissions.indexOf(permissionId)
  if (index > -1) {
    form.permissions.splice(index, 1)
  } else {
    form.permissions.push(permissionId)
  }
}

const formatPermissionName = name => {
  return name
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const closeModal = () => {
  showDeleteModal.value = false
  showSendVerificationModal.value = false
  showToggleVerifyModal.value = false
}

const submit = () => {
  const submitData = {
    name: form.name,
    email: form.email,
    role: form.role,
    force_password_change: form.force_password_change,
    disable_account: form.disable_account,
    auto_destroy: form.auto_destroy,
  }

  if (activeTab.value === 1) {
    submitData.permissions = form.permissions
  }

  form
    .transform(data => submitData)
    .put(route('admin.user.update', props.user.id), {
      preserveScroll: true,
    })
}

const deleteUser = () => {
  form.delete(route('admin.user.destroy', props.user.id), {
    onSuccess: () => {
      showDeleteModal.value = false
    },
  })
}

const showToggleVerifyModal = ref(false)
const showSendVerificationModal = ref(false)
const verificationEmailSent = ref(false)

const triggerToggleVerified = () => {
  showToggleVerifyModal.value = true
}

const triggerSendVerificationEmail = () => {
  showSendVerificationModal.value = true
}

const sendVerificationEmail = () => {
  form.post(route('admin.user.verification.send', { user: props.user.id }), {
    onSuccess: () => {
      showSendVerificationModal.value = false
      verificationEmailSent.value = true
    },
  })
}

const toggleVerified = () => {
  form.post(route('admin.user.verification.toggle', { user: props.user.id }), {
    onSuccess: () => {
      showToggleVerifyModal.value = false
      verificationEmailSent.value = true
    },
  })
}
</script>

<template>
  <Head :title="`Edit User - ${props.user.name}`" />
  <main class="main-container mx-auto max-w-7xl" aria-labelledby="edit-user">
    <div class="container-border">
      <PageHeader
        :title="`Edit User - ${props.user.name}`"
        description="Manage user information, roles, and permissions"
        :breadcrumbs="[
          { label: 'Dashboard', href: '/' },
          { label: 'Users', href: '/admin/users' },
          { label: props.user.name },
        ]" />

      <section class="p-3 sm:p-6 dark:bg-gray-900">
        <div
          class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
          <div class="bg-gray-50 px-3 sm:px-6 dark:bg-gray-800">
            <Tabs v-model="activeTab" :tabs="tabs" />
          </div>

          <section class="relative">
            <div class="relative">
              <form @submit.prevent="submit">
                <Transition name="tab-fade" mode="out-in" appear>
                  <div v-if="activeTab === 0" class="space-y-6 px-3 sm:px-6">
                    <div class="w-full space-y-6 lg:w-2/3">
                      <div class="grid grid-cols-1 gap-6 sm:gap-6 lg:grid-cols-2">
                        <FormInput
                          v-model="form.name"
                          label="Legal name"
                          :error="form.errors.name"
                          name="name" />
                        <FormInput
                          v-model="form.email"
                          label="Email address"
                          type="email"
                          :error="form.errors.email"
                          name="email" />
                      </div>

                      <div class="space-y-4">
                        <div v-if="props.user.is_superuser" class="space-y-2">
                          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Assigned role
                          </label>
                          <div
                            class="rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-900 capitalize dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            {{ props.user.roles?.[0]?.name || 'No role assigned' }}
                          </div>
                          <p class="text-sm text-gray-500 dark:text-gray-400">
                            Superuser role is protected and cannot be changed
                          </p>
                        </div>

                        <FormSelect
                          v-else
                          v-model="form.role"
                          :options="roles.data"
                          option-label="name"
                          option-value="id"
                          name="role"
                          label="Assigned role"
                          :error="form.errors.role" />
                      </div>

                      <div v-if="emailVerificationEnabled" class="space-y-4">
                        <div
                          class="rounded-lg border border-[var(--color-border-strong)] p-3 sm:p-4">
                          <span v-if="props.user.email_verified_at_full">
                            <div class="grid grid-cols-1 items-center gap-4 md:grid-cols-3">
                              <h6 class="text-success col-span-2 w-full text-xs capitalize">
                                Email Verified at:
                                <br />
                                <strong class="font-bold">
                                  {{ props.user.email_verified_at_full }}
                                </strong>
                              </h6>
                              <button
                                class="btn btn-warning btn-xs w-full"
                                @click="triggerToggleVerified()"
                                type="button"
                                :disabled="isCurrentUser">
                                Toggle Verified
                              </button>
                            </div>
                            <p v-if="isCurrentUser" class="mt-3 flex justify-start text-xs">
                              Unable to toggle yourself as verified
                            </p>
                          </span>
                          <span v-else>
                            <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-3">
                              <h6 class="text-warning w-full text-xs font-bold capitalize">
                                Email Not Verified
                                <span v-if="verificationEmailSent" class="text-info">
                                  <br />
                                  Email Verification Sent
                                </span>
                              </h6>

                              <div class="flex justify-end gap-4 max-sm:flex-col sm:col-span-2">
                                <button
                                  class="btn btn-warning btn-xs w-full"
                                  @click="triggerToggleVerified()"
                                  type="button"
                                  :disabled="isCurrentUser">
                                  Toggle Verified
                                </button>

                                <button
                                  class="btn btn-primary btn-xs w-full"
                                  @click="triggerSendVerificationEmail()"
                                  type="button"
                                  :disabled="verificationEmailSent">
                                  {{
                                    verificationEmailSent
                                      ? 'Email Verification Sent'
                                      : 'Send Verification Email'
                                  }}
                                </button>
                              </div>
                            </div>
                            <p v-if="isCurrentUser" class="mt-3 flex justify-start text-xs">
                              Unable to toggle yourself as verified
                            </p>
                          </span>
                        </div>
                      </div>

                      <div class="mb-4 space-y-4">
                        <FormCheckbox
                          v-model="form.disable_account"
                          :disabled="props.user.is_superuser"
                          label="Disable Account"
                          :help="
                            props.user.is_superuser
                              ? 'Superuser account cannot be disabled'
                              : 'Enable or disable user access'
                          "
                          :error="form.errors.disable_account" />

                        <FormCheckbox
                          v-model="form.force_password_change"
                          :disabled="props.user.is_superuser"
                          label="Force Password Reset"
                          :help="
                            props.user.is_superuser
                              ? 'Superuser cannot be forced to reset password'
                              : 'Require new password on next login'
                          "
                          :error="form.errors.force_password_change" />

                        <FormCheckbox
                          v-model="form.auto_destroy"
                          label="Auto destroy on timer after deleted?"
                          help="After an account is deleted, this is the countdown timer until the record will be completely destroyed from the app automatically"
                          :error="form.errors.auto_destroy" />
                      </div>
                    </div>
                  </div>

                  <div v-else-if="activeTab === 1" class="space-y-6 px-3 pb-4 sm:px-6">
                    <div
                      class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-700 dark:bg-amber-900/20">
                      <p class="text-sm text-amber-700 dark:text-amber-400">
                        <span class="font-medium">Direct Permissions:</span>
                        Direct permissions override role-based permissions. Only assign direct
                        permissions when necessary.
                      </p>
                    </div>

                    <div
                      class="rounded-lg border border-indigo-200 bg-gradient-to-r from-indigo-50 to-blue-50 p-4 dark:border-indigo-800 dark:from-indigo-900/20 dark:to-blue-900/20">
                      <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                          <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Selected Permissions
                          </h3>
                          <p class="mt-1 text-lg font-bold text-indigo-600 dark:text-indigo-400">
                            {{ selectedCount }}
                            <span class="text-xs font-normal text-gray-600 dark:text-gray-400">
                              of {{ totalCount }}
                            </span>
                          </p>
                        </div>
                        <div class="flex gap-2">
                          <button
                            type="button"
                            @click="expandAll"
                            class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 transition-colors hover:bg-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-300 dark:hover:bg-indigo-900/60">
                            Expand All
                          </button>
                          <button
                            type="button"
                            @click="collapseAll"
                            class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 transition-colors hover:bg-indigo-200 dark:bg-indigo-900/40 dark:text-indigo-300 dark:hover:bg-indigo-900/60">
                            Collapse All
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="relative">
                      <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg
                          class="h-5 w-5 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      </div>
                      <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search permissions..."
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pr-3 pl-10 text-gray-900 placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400" />
                    </div>

                    <div class="space-y-3">
                      <div
                        v-for="(permissions, category) in groupedPermissions"
                        :key="category"
                        class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <button
                          type="button"
                          @click="toggleCategory(category)"
                          class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 transition-colors hover:bg-gray-100 dark:bg-gray-900/50 dark:hover:bg-gray-900">
                          <div class="flex items-center gap-3">
                            <svg
                              :class="[
                                'h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-400',
                                expandedCategories.has(category) ? 'rotate-90' : '',
                              ]"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24">
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7" />
                            </svg>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                              {{ category }}
                            </h3>
                            <span
                              class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                              {{ permissions.length }}
                            </span>
                          </div>
                          <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ permissions.filter(p => isSelected(p.id)).length }}
                            selected
                          </span>
                        </button>

                        <Transition name="slide-down">
                          <div
                            v-if="expandedCategories.has(category)"
                            class="border-t border-gray-200 p-4 dark:border-gray-700">
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                              <label
                                v-for="permission in permissions"
                                :key="permission.id"
                                :for="`permission-${permission.id}`"
                                class="flex cursor-pointer items-start gap-3 rounded-lg p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <div class="mt-0.5 flex h-5 items-center">
                                  <input
                                    :id="`permission-${permission.id}`"
                                    type="checkbox"
                                    :checked="isSelected(permission.id)"
                                    @change="togglePermission(permission.id)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-indigo-600" />
                                </div>
                                <div class="min-w-0 flex-1">
                                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ formatPermissionName(permission.name) }}
                                  </div>
                                  <div
                                    class="mt-0.5 font-mono text-xs text-gray-500 dark:text-gray-400">
                                    {{ permission.name }}
                                  </div>
                                </div>
                              </label>
                            </div>
                          </div>
                        </Transition>
                      </div>

                      <div
                        v-if="Object.keys(groupedPermissions).length === 0"
                        class="py-12 text-center">
                        <svg
                          class="mx-auto h-12 w-12 text-gray-400"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                          No permissions found matching "{{ searchQuery }}"
                        </p>
                      </div>
                    </div>

                    <p
                      v-if="form.errors.permissions"
                      class="text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.permissions }}
                    </p>
                  </div>

                  <div v-else-if="activeTab === 2" class="space-y-6 px-3 sm:px-6">
                    <div class="space-y-4">
                      <div class="well">
                        <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                          User account has been previously restored
                        </h4>
                        <dl class="space-y-1">
                          <div class="flex gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Restored On:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">
                              {{ props.user.restore_date_full }}
                            </dd>
                          </div>
                        </dl>
                      </div>

                      <div class="danger-card">
                        <h3
                          class="mb-4 text-base font-semibold text-red-600 sm:mb-6 sm:text-lg dark:text-red-400">
                          Danger Zone
                        </h3>

                        <div class="danger-well">
                          <h4
                            class="mb-2 text-sm font-medium text-gray-900 sm:text-base dark:text-gray-100">
                            Delete Account
                          </h4>
                          <p
                            class="mb-3 text-xs leading-relaxed text-gray-600 sm:mb-4 sm:text-sm dark:text-gray-400">
                            This action is permanent and cannot be undone. All user data will be
                            permanently deleted.
                          </p>

                          <Alert v-if="props.user.is_superuser" type="warning">
                            You cannot delete a super user account.
                          </Alert>

                          <button
                            v-if="!props.user.is_superuser"
                            type="button"
                            @click="showDeleteModal = true"
                            :disabled="props.user.is_superuser"
                            class="btn btn-danger btn-sm mb-4 inline-flex w-full items-center gap-2 sm:w-auto">
                            <span class="hidden sm:inline">Delete Account</span>
                            <span class="sm:hidden">Delete Account</span>
                          </button>

                          <div
                            v-if="!props.user.is_superuser"
                            class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
                            <div class="flex gap-2">
                              <svg
                                class="h-5 w-5 flex-shrink-0 text-amber-600 dark:text-amber-400"
                                fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                  fill-rule="evenodd"
                                  d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd" />
                              </svg>
                              <p class="text-sm text-amber-700 dark:text-amber-300">
                                This will delete the user's account and all associated data. The
                                account is recoverable up to the auto-delete date if it is set.
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </Transition>

                <div
                  v-if="activeTab != 2"
                  class="flex flex-col items-center justify-end gap-3 border-t border-gray-200 bg-gray-50 px-3 py-4 sm:flex-row sm:px-6 dark:border-gray-700 dark:bg-gray-900">
                  <button
                    type="submit"
                    class="btn btn-primary btn-sm w-full sm:w-auto"
                    :disabled="form.processing">
                    <svg
                      v-if="form.processing"
                      class="h-4 w-4 animate-spin"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24">
                      <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4" />
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                  </button>
                </div>
              </form>
            </div>
          </section>
        </div>
      </section>
    </div>
  </main>

  <Modal :show="showDeleteModal" @close="closeModal" size="sm">
    <template #title>
      <div class="text-danger flex items-center gap-2">Delete User Account</div>
    </template>

    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Are you sure you want to delete this user account? This action cannot be undone and all
          associated data will be permanently removed.
        </p>
        <div
          class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
          <div class="flex gap-2">
            <svg
              class="h-5 w-5 flex-shrink-0 text-amber-600 dark:text-amber-400"
              fill="currentColor"
              viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-amber-700 dark:text-amber-300">
              User:
              <span class="font-medium">{{ props.user.name }}</span>
            </p>
          </div>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-end gap-8">
        <button
          @click="closeModal"
          type="button"
          class="cursor-pointer px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400">
          Cancel
        </button>
        <button
          @click="deleteUser"
          :disabled="form.processing"
          type="button"
          class="btn btn-danger btn-sm">
          {{ form.processing ? 'Deleting...' : 'Yes, Delete Account' }}
        </button>
      </div>
    </template>
  </Modal>

  <Modal :show="showToggleVerifyModal" @close="closeModal" size="sm">
    <template #title>
      <div class="text-warning flex items-center gap-2">
        <span v-if="props.user.email_verified_at">Confirm User Un-Verification</span>
        <span v-else>Confirm User Verification</span>
      </div>
    </template>
    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          <span v-if="props.user.email_verified_at">
            Are you sure you want to
            <strong class="uppercase">un-verify</strong>
            {{ props.user.name }}'s email?
          </span>
          <span v-else>
            Are you sure you want to
            <strong class="uppercase">verify</strong>
            {{ props.user.name }}'s email?
          </span>
        </p>
        <div
          class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
          <div class="flex gap-2">
            <span class="text-sm text-amber-700 dark:text-amber-300">
              <dl class="space-y-1">
                <div class="flex gap-2">
                  <dt class="text-sm text-gray-500 dark:text-gray-400">
                    <span v-if="props.user.email_verified_at">Un-verify:</span>
                    <span v-else>Verify:</span>
                  </dt>
                  <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ props.user.email }}
                  </dd>
                </div>
              </dl>
            </span>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end gap-8">
        <button
          @click="closeModal"
          type="button"
          class="cursor-pointer px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400">
          Cancel
        </button>
        <button
          @click="toggleVerified"
          :disabled="form.processing"
          type="button"
          class="btn btn-warning btn-sm">
          {{ form.processing ? 'Confirming...' : 'Confirm' }}
        </button>
      </div>
    </template>
  </Modal>

  <Modal :show="showSendVerificationModal" @close="closeModal" size="sm">
    <template #title>
      <div class="text-warning flex items-center gap-2">Send User Verification</div>
    </template>
    <template #default>
      <div class="space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Are you sure you want to send an email to {{ user.name }} to verify their account?
        </p>
        <div
          class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20">
          <div class="flex gap-2">
            <span class="text-sm text-amber-700 dark:text-amber-300">
              <dl class="space-y-1">
                <div class="flex gap-2">
                  <dt class="text-sm text-gray-500 dark:text-gray-400">Email to verify:</dt>
                  <dd class="text-sm text-gray-900 dark:text-gray-100">
                    {{ props.user.email }}
                  </dd>
                </div>
              </dl>
            </span>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end gap-8">
        <button
          @click="closeModal"
          type="button"
          class="cursor-pointer px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400">
          Cancel
        </button>
        <button
          @click="sendVerificationEmail"
          :disabled="form.processing"
          type="button"
          class="btn btn-warning btn-sm">
          {{ form.processing ? 'Sending...' : 'Confirm' }}
        </button>
      </div>
    </template>
  </Modal>
</template>
