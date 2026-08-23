<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Tabs from '@js/Components/Common/Tabs.vue'
import RolesTab from '@js/Pages/Admin/PermissionRole/RolesTab.vue'
import PermissionsTab from '@js/Pages/Admin/PermissionRole/PermissionsTab.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    roles: { type: Array, required: true, default: () => [] },
    permissions: { type: Object, required: true },
    permissionsList: { type: Array, required: true, default: () => [] },
    protectedRoles: { type: Array, default: () => [] },
    protectedPermissions: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const tabs = ['Roles', 'Permissions']
const activeTab = ref(0)
</script>

<template>
    <Head title="Access Control" />

    <main class="mx-auto max-w-7xl" aria-labelledby="permissions-roles-title">
        <PageHeader
            title="Access control"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Access control' },
            ]" />

        <div class="card overflow-hidden">
            <div class="border-b border-border bg-muted px-4 sm:px-6">
                <Tabs v-model="activeTab" :tabs="tabs" />
            </div>
            <div class="px-4 py-5 sm:px-6">
                <RolesTab
                    v-if="activeTab === 0"
                    :roles="roles"
                    :permissions="permissionsList" />
                <PermissionsTab
                    v-else
                    :permissions="permissions"
                    :protectedPermissions="protectedPermissions"
                    :filters="filters" />
            </div>
        </div>
    </main>
</template>
