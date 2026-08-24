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

defineProps({
    roles: { type: Array, required: true, default: () => [] },
    permissions: { type: Object, required: true },
    permissionsList: { type: Array, required: true, default: () => [] },
    protectedRoles: { type: Array, default: () => [] },
    protectedPermissions: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const tabs = ['Roles', 'Permissions']

/* `?role=<id>` deep-links here from anywhere that names a role — the user edit
   screen sends admins here to answer "which permissions does this role grant?"
   and used to drop them on the index with no idea which row to look at. */
const focusRoleId = new URLSearchParams(window.location.search).get('role') || ''
const activeTab = ref(0)
</script>

<template>
    <Head title="Access control" />

    <main class="mx-auto max-w-4xl" aria-labelledby="permissions-roles-title">
        <PageHeader
            title="Access control"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Access control' },
            ]" />

        <!-- No card. The tab rule already separates the strip from the panel;
             a box around both is decoration, not structure. -->
        <div class="border-border border-b">
            <Tabs v-model="activeTab" :tabs="tabs" />
        </div>
        <div class="pt-6">
            <RolesTab
                v-if="activeTab === 0"
                :roles="roles"
                :permissions="permissionsList"
                :focus-role-id="focusRoleId" />
            <PermissionsTab
                v-else
                :permissions="permissions"
                :protectedPermissions="protectedPermissions"
                :filters="filters" />
        </div>
    </main>
</template>
