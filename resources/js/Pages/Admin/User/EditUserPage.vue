<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '@/Layouts/Default.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormCheckbox from '@/Components/FormCheckbox.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Tabs from '@/Components/Tabs.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: Object,
    roles: Object,
    permissions: Object,
    categoryMap: Object,
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.roles?.[0]?.id || '',
    force_password_change: Boolean(props.user.force_password_change) || false,
    disable_account: Boolean(props.user.disable_account) || false,
    permissions: props.user.permissions?.map(permission => permission.id) || [],
})

const tabs = [
    {
        name: 'Account',
        key: 'account'
    },
    {
        name: 'Permissions',
        key: 'permissions'
    }
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

const toggleCategory = (category) => {
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

const isSelected = (permissionId) => {
    return form.permissions.includes(permissionId)
}

const togglePermission = (permissionId) => {
    const index = form.permissions.indexOf(permissionId)
    if (index > -1) {
        form.permissions.splice(index, 1)
    } else {
        form.permissions.push(permissionId)
    }
}

const formatPermissionName = (name) => {
    return name
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
}

const closeModal = () => {
    showDeleteModal.value = false
}

const submit = () => {
    const submitData = {
        name: form.name,
        email: form.email,
        role: form.role,
        force_password_change: form.force_password_change,
        disable_account: form.disable_account,
    }
    
    if (activeTab.value === 1) {
        submitData.permissions = form.permissions
    }
    
    form.transform((data) => submitData).put(route('admin.user.update', props.user.id), {
        preserveScroll: true,
    })
}

const deleteUser = () => {
    form.delete(route('admin.user.destroy', props.user.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>

<template>
    <Head :title="`Edit User - ${props.user.name}`" />
    <main class="max-w-7xl mx-auto" aria-labelledby="edit-user">
        <div class="container-border">
            <PageHeader
                :title="`Edit User - ${props.user.name}`"
                description="Manage user information, roles, and permissions"
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Users', href: '/admin/users' },
                    { label: props.user.name }
                ]"
            />

            <section class="p-3 sm:p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

                    <div class="px-3 sm:px-6 bg-gray-50 dark:bg-gray-800">
                        <Tabs v-model="activeTab" :tabs="tabs" />
                    </div>

                    <section class="relative">
                        <div class="relative">
                            <form @submit.prevent="submit">
                                <Transition name="tab-fade" mode="out-in" appear>
                                    <div v-if="activeTab === 0" class="p-3 sm:p-6 space-y-6">
                                    <div class="w-full lg:w-2/3 space-y-6">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-6">
                                            <FormInput v-model="form.name" label="Legal name" :error="form.errors.name"
                                                name="name" />
                                            <FormInput v-model="form.email" label="Email address" type="email"
                                                :error="form.errors.email" name="email" />
                                        </div>

                                        <div class="space-y-4">
                                            <div v-if="props.user.is_superuser" class="space-y-2">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Assigned role
                                                </label>
                                                <div
                                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-gray-100 capitalize">
                                                    {{ props.user.roles?.[0]?.name || 'No role assigned' }}
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Superuser role is protected and cannot be changed
                                                </p>
                                            </div>

                                            <FormSelect v-else v-model="form.role" :options="roles.data"
                                                option-label="name" option-value="id" name="role" label="Assigned role"
                                                :error="form.errors.role" />
                                        </div>

                                        <div class="space-y-4">
                                            <FormCheckbox 
                                                v-model="form.disable_account"
                                                :disabled="props.user.is_superuser"
                                                label="Disable Account"
                                                :help="props.user.is_superuser ? 'Superuser account cannot be disabled' : 'Enable or disable user access'"
                                                :error="form.errors.disable_account"
                                            />

                                            <FormCheckbox 
                                                v-model="form.force_password_change"
                                                :disabled="props.user.is_superuser"
                                                label="Force Password Reset"
                                                :help="props.user.is_superuser ? 'Superuser cannot be forced to reset password' : 'Require new password on next login'"
                                                :error="form.errors.force_password_change"
                                            />
                                        </div>
                                    </div>

                                    <div class="space-y-4" v-if="!props.user.is_superuser">
                                        <div
                                            class="rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
                                            <h3
                                                class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">
                                                Danger Zone</h3>

                                            <div
                                                class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                                                <h4
                                                    class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                                                    Delete Account Permanently</h4>
                                                <p
                                                    class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                                                    This action is permanent and cannot be undone. All user data will be
                                                    permanently deleted.
                                                </p>
                                                <button type="button" @click="showDeleteModal = true"
                                                    class="w-full sm:w-auto btn-danger btn-sm inline-flex items-center gap-2">
                                                    <span class="hidden sm:inline">Permanently Delete Account</span>
                                                    <span class="sm:hidden">Delete Account</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <div v-else-if="activeTab === 1" class="p-3 sm:p-6 space-y-6">
                                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                                <span class="font-medium">Direct Permissions:</span> Direct permissions override role-based permissions. Only assign direct permissions when necessary.
                                            </p>
                                        </div>

                                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800 p-4">
                                            <div class="flex items-center justify-between flex-wrap gap-4">
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Selected Permissions</h3>
                                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                                                        {{ selectedCount }} <span class="text-xs font-normal text-gray-600 dark:text-gray-400">of {{ totalCount }}</span>
                                                    </p>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button 
                                                        type="button"
                                                        @click="expandAll"
                                                        class="px-3 py-1.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/40 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-900/60 transition-colors"
                                                    >
                                                        Expand All
                                                    </button>
                                                    <button 
                                                        type="button"
                                                        @click="collapseAll"
                                                        class="px-3 py-1.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/40 rounded-md hover:bg-indigo-200 dark:hover:bg-indigo-900/60 transition-colors"
                                                    >
                                                        Collapse All
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <input
                                                v-model="searchQuery"
                                                type="text"
                                                placeholder="Search permissions..."
                                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            />
                                        </div>

                                        <div class="space-y-3">
                                            <div 
                                                v-for="(permissions, category) in groupedPermissions" 
                                                :key="category"
                                                class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800"
                                            >
                                                <button
                                                    type="button"
                                                    @click="toggleCategory(category)"
                                                    class="w-full px-4 py-3 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-900 transition-colors"
                                                >
                                                    <div class="flex items-center gap-3">
                                                        <svg 
                                                            :class="[
                                                                'w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-200',
                                                                expandedCategories.has(category) ? 'rotate-90' : ''
                                                            ]"
                                                            fill="none" 
                                                            stroke="currentColor" 
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                            {{ category }}
                                                        </h3>
                                                        <span class="px-2 py-0.5 text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                                            {{ permissions.length }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ permissions.filter(p => isSelected(p.id)).length }} selected
                                                    </span>
                                                </button>

                                                <Transition name="slide-down">
                                                    <div v-if="expandedCategories.has(category)" class="p-4 border-t border-gray-200 dark:border-gray-700">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <label
                                                                v-for="permission in permissions"
                                                                :key="permission.id"
                                                                :for="`permission-${permission.id}`"
                                                                class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-900/50 cursor-pointer transition-colors"
                                                            >
                                                                <div class="flex items-center h-5 mt-0.5">
                                                                    <input
                                                                        :id="`permission-${permission.id}`"
                                                                        type="checkbox"
                                                                        :checked="isSelected(permission.id)"
                                                                        @change="togglePermission(permission.id)"
                                                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:checked:bg-indigo-600"
                                                                    />
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                        {{ formatPermissionName(permission.name) }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                                                                        {{ permission.name }}
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </Transition>
                                            </div>

                                            <div v-if="Object.keys(groupedPermissions).length === 0" class="text-center py-12">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                                    No permissions found matching "{{ searchQuery }}"
                                                </p>
                                            </div>
                                        </div>

                                        <p v-if="form.errors.permissions" class="text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.permissions }}
                                        </p>
                                    </div>
                                </Transition>

                                <div
                                    class="px-3 sm:px-6 py-4 bg-gray-50 dark:bg-gray-900 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700">
                                    <button type="submit" class="btn-primary btn-sm w-full sm:w-auto"
                                        :disabled="form.processing">
                                        <svg v-if="form.processing" class="animate-spin h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
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
            <div class="flex items-center gap-2 text-red-600">
                Delete User Account
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete this user account? This action cannot be undone and all associated
                    data will be permanently removed.
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
                            User: <span class="font-medium">{{ props.user.name }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button @click="closeModal" type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer">
                    Cancel
                </button>
                <button @click="deleteUser" :disabled="form.processing" type="button" class="btn-danger btn-sm">
                    {{ form.processing ? 'Deleting...' : 'Yes, Delete Account' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

<style scoped>
.tab-fade-enter-active,
.tab-fade-leave-active {
    transition: all 0.3s ease;
}

.tab-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.tab-fade-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}

.slide-down-enter-active {
    transition: all 0.3s ease-out;
}

.slide-down-leave-active {
    transition: all 0.2s ease-in;
}

.slide-down-enter-from {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}

.slide-down-enter-to {
    opacity: 1;
    max-height: 1000px;
    transform: translateY(0);
}

.slide-down-leave-from {
    opacity: 1;
    max-height: 1000px;
    transform: translateY(0);
}

.slide-down-leave-to {
    opacity: 0;
    max-height: 0;
    transform: translateY(-10px);
}
</style>
