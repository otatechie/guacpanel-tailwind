<template>

    <Head title="Permissions & Roles" />

    <div class="max-w-5xl mx-auto px-6">
        <div class="bg-white rounded-xl shadow-sm">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">User Access Management</h2>
                <p class="mt-1 text-sm text-gray-500">Manage user roles and permissions</p>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="[
                        'px-6 py-3 text-sm font-medium whitespace-nowrap',
                        activeTab === tab.key
                            ? 'border-b-2 border-blue-500 text-blue-600'
                            : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
                    ]">
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <!-- Content -->
            <div class="p-6">
                <RolesTab v-if="activeTab === 'roles'" :roles="roles" :permissions="permissions" />

                <PermissionsTab v-if="activeTab === 'permissions'" :permissions="permissions" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import RolesTab from './RolesTab.vue'
import PermissionsTab from './PermissionsTab.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    roles: Object,
    permissions: Object
})

const tabs = [
    { key: 'roles', label: 'Roles' },
    { key: 'permissions', label: 'Permissions' }
]

const activeTab = ref('roles')
</script>
