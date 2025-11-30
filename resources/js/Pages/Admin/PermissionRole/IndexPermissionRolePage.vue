<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Tabs from '@/Components/Tabs.vue'
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
        type: Object,
        required: true
    },
    permissionsList: {
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
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const tabs = ['Roles', 'Permissions']

const activeTab = ref(0)
</script>

<template>
    <Head title="Permissions & Roles" />

    <main class="max-w-7xl mx-auto" aria-labelledby="permissions-roles-title">
        <div class="container-border">
            <PageHeader
                title="Access Control Management"
                description="Manage user roles and permissions"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('dashboard') },
                    { label: 'System Settings', href: route('admin.setting.index') },
                    { label: 'Access Control' }
                ]" />

            <section class="px-6 pb-6 pt-4 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-3 sm:px-6 bg-gray-50 dark:bg-gray-800">
                        <Tabs v-model="activeTab" :tabs="tabs" />
                    </div>

                    <div class="relative">
                        <Transition name="tab-fade" mode="out-in" appear>
                            <RolesTab
                                v-if="activeTab === 0"
                                :roles="roles"
                                :permissions="permissionsList"
                                role="tabpanel"
                                id="roles-panel"
                                aria-labelledby="roles-tab"
                                class="p-3 sm:p-6" />

                            <PermissionsTab
                                v-else-if="activeTab === 1"
                                :permissions="permissions"
                                :protectedPermissions="protectedPermissions"
                                :filters="filters"
                                role="tabpanel"
                                id="permissions-panel"
                                aria-labelledby="permissions-tab"
                                class="p-3 sm:p-6" />
                        </Transition>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>
