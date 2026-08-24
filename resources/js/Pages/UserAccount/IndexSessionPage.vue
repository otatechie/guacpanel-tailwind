<script setup>
import Button from '@/Components/Button.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import Default from '@js/Layouts/Default.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Badge from '@js/Components/Badge.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import { LaptopIcon, MonitorIcon, SmartphoneIcon, TriangleAlertIcon } from '@lucide/vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    user: Object,
    sessions: {
        type: Object,
    },
})

const formattedSessions = computed(() => {
    if (!Array.isArray(props.sessions)) return []

    return props.sessions.map(session => ({
        id: session.id,
        device: session.agent?.device || 'Unknown device',
        browser: session.agent?.browser || 'Unknown browser',
        platform: session.agent?.platform || '',
        lastActive: session.lastActive || '',
        isCurrent: session.isCurrent || false,
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
        onError: () => (logoutModal.value = false),
    })
}

const logoutAllSessions = () => {
    passwordForm.post(route('user.session.logout'), {
        preserveScroll: true,
        onSuccess: () => (logoutAllModal.value = false),
    })
}

const deviceIcons = {
    default: MonitorIcon,
    mobile: SmartphoneIcon,
    desktop: LaptopIcon,
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
    <Head title="Devices" />

    <main class="mx-auto max-w-7xl" aria-labelledby="sessions-management">
        <PageHeader
            title="Devices"
            description="View and manage your active sessions"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Account settings', href: route('user.index') },
                { label: 'Devices' },
            ]" />

        <div class="card p-3 sm:p-6">
            <div class="space-y-4 sm:space-y-6">
                <h2
                    id="active-sessions"
                    class="text-base font-medium text-gray-800 sm:text-lg dark:text-gray-200">
                    Active Sessions
                </h2>

                <div
                    class="rounded-lg border border-amber-200 bg-amber-50 p-3 sm:p-4 dark:border-amber-700 dark:bg-amber-900/20">
                    <p
                        class="flex items-start gap-2 text-xs font-medium text-amber-700 sm:text-sm dark:text-amber-400">
                        <TriangleAlertIcon
                            class="mt-0.5 h-4 w-4 flex-shrink-0 sm:h-5 sm:w-5"
                            aria-hidden="true" />
                        <span>
                            If you notice any suspicious activity, immediately sign out of all other
                            browser sessions and update your password.
                        </span>
                    </p>
                </div>

                <div v-if="formattedSessions.length > 0" class="space-y-3 sm:space-y-0">
                    <div
                        class="hidden overflow-hidden rounded-lg border border-gray-200 sm:block dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                        Device
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                        Last Active
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                                <tr v-for="session in formattedSessions" :key="session.id">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                                <component
                                                    :is="getDeviceIcon(session.device)"
                                                    class="h-4 w-4 text-gray-600 dark:text-gray-400"
                                                    aria-hidden="true" />
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="flex flex-wrap items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ session.device }}
                                                    <Badge
                                                        v-if="session.isCurrent"
                                                        dot
                                                        variant="success">
                                                        Current
                                                    </Badge>
                                                </div>
                                                <div
                                                    class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ session.browser }}
                                                    <span v-if="session.platform" class="ml-1">
                                                        ({{ session.platform }})
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        {{ session.lastActive }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                        <button
                                            v-if="!session.isCurrent"
                                            @click="confirmLogout(session)"
                                            class="cursor-pointer rounded-md px-2 py-1 text-red-600 transition-colors hover:bg-red-50 hover:text-red-900 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                                            aria-label="Sign out from this device">
                                            Sign out
                                        </button>
                                        <span v-else class="text-gray-400 dark:text-gray-500">
                                            Current
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3 sm:hidden">
                        <div
                            v-for="session in formattedSessions"
                            :key="session.id"
                            class="card p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                    <component
                                        :is="getDeviceIcon(session.device)"
                                        class="h-5 w-5 text-gray-600 dark:text-gray-400"
                                        aria-hidden="true" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="flex flex-wrap items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                <span class="truncate">
                                                    {{ session.device }}
                                                </span>
                                                <Badge
                                                    v-if="session.isCurrent"
                                                    dot
                                                    variant="success">
                                                    Current
                                                </Badge>
                                            </div>
                                            <div
                                                class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ session.browser }}
                                                <span v-if="session.platform" class="ml-1">
                                                    ({{ session.platform }})
                                                </span>
                                            </div>
                                            <div
                                                class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Last active: {{ session.lastActive }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                                        <button
                                            v-if="!session.isCurrent"
                                            @click="confirmLogout(session)"
                                            class="flex min-h-[44px] w-full items-center justify-center rounded-md border border-red-200 px-4 py-3 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                                            aria-label="Sign out from this device">
                                            Sign out
                                        </button>
                                        <span
                                            v-else
                                            class="block py-2 text-sm text-gray-400 dark:text-gray-500">
                                            Current session
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="card p-4 text-center sm:p-6">
                    <div
                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                        <MonitorIcon
                            class="mb-2 h-10 w-10 text-gray-400 sm:mb-3 sm:h-12 sm:w-12 dark:text-gray-600"
                            aria-hidden="true" />
                        <p class="text-sm sm:text-base">No active sessions found</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-gray-200 pt-4 sm:mt-8 sm:pt-6 dark:border-gray-700">
                <div class="rounded-lg border border-red-200 p-3 sm:p-4 lg:p-6 dark:border-red-800">
                    <h3
                        class="mb-3 text-sm font-semibold text-red-600 sm:mb-4 sm:text-base lg:mb-6 lg:text-lg dark:text-red-400">
                        Danger Zone
                    </h3>

                    <div
                        class="rounded-lg border border-red-200 bg-red-50 p-3 sm:p-4 dark:border-red-700 dark:bg-red-900/20">
                        <h4
                            class="mb-1.5 text-sm font-medium text-gray-900 sm:mb-2 sm:text-base dark:text-gray-100">
                            Sign out all other sessions
                        </h4>
                        <p
                            class="mb-3 text-xs leading-relaxed text-gray-600 sm:mb-4 sm:text-sm dark:text-gray-400">
                            This will terminate access from any other devices where you're currently
                            logged in. Your current session will remain active.
                        </p>
                        <Button
                            variant="danger"
                            size="sm"
                            class="flex min-h-[44px] w-full items-center justify-center sm:min-h-0 sm:w-auto"
                            @click="confirmLogoutAll">
                            <span class="hidden sm:inline">Sign out of all other sessions</span>
                            <span class="sm:hidden">Sign out all</span>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <Modal
        :show="logoutModal"
        size="sm"
        description="That device will be signed out immediately."
        @close="logoutModal = false">
        <template #title>
            <div
                class="flex items-center gap-2 text-sm text-red-600 sm:text-base dark:text-red-400">
                Sign out session
            </div>
        </template>

        <template #default>
            <div class="space-y-3 sm:space-y-4">
                <p class="text-xs text-gray-500 sm:text-sm dark:text-gray-400">
                    Are you sure you want to sign out of this session?
                </p>
                <div
                    v-if="selectedSession"
                    class="rounded-lg border border-gray-200 bg-gray-50 p-3 sm:p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-100 sm:h-12 sm:w-12 dark:bg-gray-700">
                            <component
                                :is="getDeviceIcon(selectedSession.device)"
                                class="h-5 w-5 text-gray-600 sm:h-6 sm:w-6 dark:text-gray-400"
                                aria-hidden="true" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div
                                class="truncate text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ selectedSession.device }}
                            </div>
                            <div
                                class="truncate text-xs text-gray-500 sm:text-sm dark:text-gray-400">
                                {{ selectedSession.browser }}
                            </div>
                            <div class="text-xs text-gray-500 sm:text-sm dark:text-gray-400">
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
                    @click="logoutModal = false"
                    type="button"
                    class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
                    :disabled="logoutForm.processing">
                    Cancel
                </button>
                <Button
                    variant="danger"
                    size="sm"
                    @click="logoutSession"
                    :disabled="logoutForm.processing">
                    {{ logoutForm.processing ? 'Signing out...' : 'Yes, sign out' }}
                </Button>
            </div>
        </template>
    </Modal>

    <Modal
        :show="logoutAllModal"
        size="sm"
        description="Every device except this one will be signed out immediately."
        @close="logoutAllModal = false">
        <template #title>
            <div
                class="flex items-center gap-2 text-sm text-red-600 sm:text-base dark:text-red-400">
                Sign out all sessions
            </div>
        </template>

        <template #default>
            <div class="space-y-3 sm:space-y-4">
                <p class="text-xs text-gray-500 sm:text-sm dark:text-gray-400">
                    Are you sure you want to sign out of all other browser sessions? Your current
                    session will remain active.
                </p>

                <div class="mt-3 sm:mt-4">
                    <FormInput
                        id="password"
                        type="password"
                        label="Password"
                        v-model="passwordForm.password"
                        :error="passwordForm.errors.password"
                        placeholder="Enter your password"
                        required />
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-8">
                <button
                    @click="logoutAllModal = false"
                    type="button"
                    class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-500 dark:text-gray-200 dark:hover:text-gray-400"
                    :disabled="passwordForm.processing">
                    Cancel
                </button>
                <Button
                    variant="danger"
                    size="sm"
                    @click="logoutAllSessions"
                    :disabled="passwordForm.processing">
                    {{ passwordForm.processing ? 'Signing out...' : 'Yes, sign out all' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
