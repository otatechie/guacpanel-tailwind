<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '../../Layouts/Default.vue'
import Modal from '@/Components/Modal.vue'
import PageHeader from '@/Components/PageHeader.vue'
import FormInput from '@/Components/FormInput.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: Object,
    sessions: {
        type: Object
    }
})

const formattedSessions = computed(() => {
    if (!Array.isArray(props.sessions)) return []

    return props.sessions.map(session => ({
        id: session.id,
        device: session.agent?.device || 'Unknown device',
        browser: session.agent?.browser || 'Unknown browser',
        platform: session.agent?.platform || '',
        lastActive: session.lastActive || '',
        isCurrent: session.isCurrent || false
    }))
})

const logoutModal = ref(false)
const logoutAllModal = ref(false)
const selectedSession = ref(null)
const passwordForm = useForm({ password: '' })
const logoutForm = useForm({})

const confirmLogout = session => {
    selectedSession.value = session
    logoutModal.value = true
}

const confirmLogoutAll = () => {
    passwordForm.reset()
    logoutAllModal.value = true
}

const logoutSession = () => {
    logoutForm.delete(route('user.session.destroy', { sessionId: selectedSession.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            logoutModal.value = false
            selectedSession.value = null
        },
        onError: () => (logoutModal.value = false)
    })
}

const logoutAllSessions = () => {
    passwordForm.post(route('user.session.logout'), {
        preserveScroll: true,
        onSuccess: () => (logoutAllModal.value = false)
    })
}

const deviceIcons = {
    default:
        'M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z',
    mobile: 'M10 16.667c.92 0 1.667-.747 1.667-1.667H8.333c0 .92.746 1.667 1.667 1.667zm5-5V7.917c0-2.559-1.364-4.7-3.75-5.267v-.567c0-.691-.56-1.25-1.25-1.25s-1.25.559-1.25 1.25v.567c-2.386.567-3.75 2.708-3.75 5.267V11.667l-1.667 1.666v.834h13.334v-.834L15 11.667z',
    desktop:
        'M17 2H3c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 12H3V5c0-.55.45-1 1-1h12c.55 0 1 .45 1 1v9z'
}

const getDeviceIcon = device => {
    if (!device) return deviceIcons.default

    const deviceLower = device.toLowerCase()
    if (
        deviceLower.includes('iphone') ||
        deviceLower.includes('ipad') ||
        deviceLower.includes('mobile')
    ) {
        return deviceIcons.mobile
    } else if (deviceLower.includes('mac') || deviceLower.includes('apple')) {
        return deviceIcons.desktop
    }
    return deviceIcons.default
}
</script>

<template>
    <Head title="Device Management" />

    <main class="max-w-7xl mx-auto" aria-labelledby="sessions-management">
        <div class="container-border">
            <PageHeader
                title="Device Management"
                description="View and manage your active sessions"
                :breadcrumbs="[
                    { label: 'Dashboard', href: route('dashboard') },
                    { label: 'Account', href: route('user.index') },
                    { label: 'Device Management' }
                ]" />

            <div class="p-6 dark:bg-gray-900">
                <div
                    class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6">
                    <!-- Active Sessions Section -->
                    <div class="space-y-6">
                        <h2
                            id="active-sessions"
                            class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            Active Sessions
                        </h2>

                        <!-- Security Warning -->
                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                            <p
                                class="text-sm text-amber-700 dark:text-amber-400 font-medium flex items-center gap-2">
                                <svg
                                    class="w-5 h-5 flex-shrink-0"
                                    fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                If you notice any suspicious activity, immediately sign out of all
                                other browser sessions and update your password.
                            </p>
                        </div>

                        <!-- Sessions Table -->
                        <div
                            v-if="formattedSessions.length > 0"
                            class="overflow-hidden border border-gray-200 rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            Device
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 hidden sm:table-cell">
                                            Last Active
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                    <tr v-for="session in formattedSessions" :key="session.id">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center dark:bg-gray-800">
                                                    <svg
                                                        class="h-4 w-4 text-gray-600 dark:text-gray-400"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path :d="getDeviceIcon(session.device)" />
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div
                                                        class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center flex-wrap">
                                                        {{ session.device }}
                                                        <span
                                                            v-if="session.isCurrent"
                                                            class="ml-2 inline-flex items-center px-2 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            Current
                                                        </span>
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ session.browser }}
                                                        <span v-if="session.platform" class="ml-1">
                                                            ({{ session.platform }})
                                                        </span>
                                                    </div>
                                                    <div class="sm:hidden mt-1 space-y-1">
                                                        <div
                                                            class="text-xs text-gray-500 dark:text-gray-400">
                                                            Last active:
                                                            {{ session.lastActive }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                            {{ session.lastActive }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                v-if="!session.isCurrent"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                                aria-label="Sign out from this device"
                                                @click="confirmLogout(session)">
                                                Sign Out
                                            </button>
                                            <span v-else class="text-gray-400 dark:text-gray-500">
                                                Current
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-else
                            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 text-center">
                            <div
                                class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <svg
                                    class="w-12 h-12 mb-3 text-gray-400 dark:text-gray-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.5">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                                </svg>
                                <p>No active sessions found</p>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div
                            class="rounded-lg border border-red-200 dark:border-red-800 p-4 sm:p-6">
                            <h3
                                class="text-base sm:text-lg font-semibold text-red-600 dark:text-red-400 mb-4 sm:mb-6">
                                Danger Zone
                            </h3>

                            <!-- Sign Out All Sessions -->
                            <div
                                class="p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                                <h4
                                    class="text-sm sm:text-base font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    Sign Out All Other Sessions
                                </h4>
                                <p
                                    class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4 leading-relaxed">
                                    This will terminate access from any other devices where you're
                                    currently logged in. Your current session will remain active.
                                </p>
                                <button
                                    class="w-full sm:w-auto btn-danger btn-sm"
                                    @click="confirmLogoutAll">
                                    <span class="hidden sm:inline">
                                        Sign Out Of All Other Sessions
                                    </span>
                                    <span class="sm:hidden">Sign out all</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <Modal :show="logoutModal" size="sm" @close="logoutModal = false">
        <template #title>
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                <svg
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Sign Out Session
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to sign out of this session?
                </p>
                <div
                    v-if="selectedSession"
                    class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div
                            class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center dark:bg-gray-700">
                            <svg
                                class="h-6 w-6 text-gray-600 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 24 24">
                                <path :d="getDeviceIcon(selectedSession.device)" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ selectedSession.device }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ selectedSession.browser }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Last active: {{ selectedSession.lastActive }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button
                    type="button"
                    class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    :disabled="logoutForm.processing"
                    @click="logoutModal = false">
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn-danger"
                    :disabled="logoutForm.processing"
                    @click="logoutSession">
                    {{ logoutForm.processing ? 'Signing out...' : 'Yes, Sign Out' }}
                </button>
            </div>
        </template>
    </Modal>

    <Modal :show="logoutAllModal" size="sm" @close="logoutAllModal = false">
        <template #title>
            <div class="flex items-center gap-2 text-red-600 dark:text-red-400">
                Sign Out All Sessions
            </div>
        </template>

        <template #default>
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to sign out of all other browser sessions? Your current
                    session will remain active.
                </p>

                <div class="mt-4">
                    <FormInput
                        id="password"
                        v-model="passwordForm.password"
                        type="password"
                        label="Password"
                        :error="passwordForm.errors.password"
                        placeholder="Enter your password"
                        required />
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button
                    type="button"
                    class="text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-gray-500 dark:hover:text-gray-400 cursor-pointer"
                    :disabled="passwordForm.processing"
                    @click="logoutAllModal = false">
                    Cancel
                </button>
                <button
                    type="button"
                    class="btn btn-sm btn-danger"
                    :disabled="passwordForm.processing"
                    @click="logoutAllSessions">
                    {{ passwordForm.processing ? 'Signing out...' : 'Yes, Sign Out All' }}
                </button>
            </div>
        </template>
    </Modal>
</template>
