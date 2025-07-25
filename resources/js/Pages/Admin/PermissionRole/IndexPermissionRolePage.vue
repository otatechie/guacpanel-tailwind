<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import RolesTab from './RolesTab.vue'
import PermissionsTab from './PermissionsTab.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    roles: {
        type: Array,
        required: true,
        default: () => []
    },
    permissions: {
        type: Array,
        required: true,
        default: () => []
    },
    protectedRoles: {
        type: Array,
        default: () => []
    },
    protectedPermissions: {
        type: Array,
        default: () => []
    }
})

const tabs = [
    { key: 'roles', label: 'Roles' },
    { key: 'permissions', label: 'Permissions' }
]

const activeTab = ref('roles')

</script>

<template>

    <Head title="Permissions & Roles" />

    <main class="max-w-6xl mx-auto" aria-labelledby="permissions-roles-title">
        <div class="container-border overflow-hidden">
            <PageHeader title="User Access Management" description="Manage user roles and permissions" :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Settings', href: route('admin.setting.index') },
                { label: 'User Access Management' }
            ]" />

            <nav class="px-6 bg-gray-50 border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                aria-label="User access tabs">
                <ul class="flex -mb-px" role="tablist">
                    <li v-for="tab in tabs" :key="tab.key" class="mr-2" role="presentation">
                        <button type="button" @click="activeTab = tab.key" :class="[
                            'px-4 py-3 inline-flex items-center gap-2 font-medium text-sm whitespace-nowrap cursor-pointer',
                            activeTab === tab.key
                                ? 'border-b-2 border-blue-500 text-blue-600 bg-white dark:bg-gray-700 dark:text-blue-400'
                                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700'
                        ]" :aria-selected="activeTab === tab.key" :aria-controls="`${tab.key}-panel`" role="tab">
                            <svg v-if="tab.key === 'roles'" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <svg v-else class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            {{ tab.label }}
                        </button>
                    </li>
                </ul>
            </nav>

            <section class="bg-white dark:bg-gray-700">
                <RolesTab v-if="activeTab === 'roles'" :roles="roles" :permissions="permissions" role="tabpanel"
                    id="roles-panel" aria-labelledby="roles-tab" />

                <PermissionsTab v-if="activeTab === 'permissions'" :permissions="permissions"
                    :protectedPermissions="protectedPermissions" role="tabpanel" id="permissions-panel"
                    aria-labelledby="permissions-tab" />
            </section>
        </div>
    </main>
</template>
