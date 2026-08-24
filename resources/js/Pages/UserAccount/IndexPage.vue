<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Tabs from '@js/Components/Common/Tabs.vue'
import ProfileTab from '@js/Pages/UserAccount/Tabs/ProfileTab.vue'
import PasswordTab from '@js/Pages/UserAccount/Tabs/PasswordTab.vue'
import TwoFactorTab from '@js/Pages/UserAccount/Tabs/TwoFactorTab.vue'
import DevicesTab from '@js/Pages/UserAccount/Tabs/DevicesTab.vue'
import AccountTab from '@js/Pages/UserAccount/Tabs/AccountTab.vue'
import NotificationsTab from '@js/Pages/UserAccount/Tabs/NotificationsTab.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    user: { type: Object, required: true },
    qrCodeSvg: { type: String, default: null },
    recoveryCodes: { type: Array, default: () => [] },
    profileEnabled: { type: Boolean, default: false },
    twoFactorEnabled: { type: Boolean, default: false },
    passwordEnabled: { type: Boolean, default: false },
    sessions: { type: Object },
    deactivateEnabled: { type: Boolean, default: false },
    deleteEnabled: { type: Boolean, default: false },
    notificationsEnabled: { type: Boolean, default: false },
    notificationPreferences: { type: Object, default: () => ({}) },
})

const activeTab = ref(0)

/* The Notifications tab only exists where the feature does, so the tab indices
   below shift with it rather than being hard-coded. */
const tabs = computed(() =>
    props.notificationsEnabled
        ? ['Profile', 'Security', 'Notifications', 'Account']
        : ['Profile', 'Security', 'Account']
)

const tabIndex = name => tabs.value.indexOf(name)
</script>

<template>
    <Head title="Account" />

    <main class="mx-auto max-w-7xl">
        <PageHeader
            title="Account"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Account' },
            ]" />

        <div class="card overflow-hidden">
            <div class="border-border bg-muted border-b px-4 sm:px-6">
                <Tabs v-model="activeTab" :tabs="tabs" />
            </div>
            <div class="px-4 py-5 sm:px-6">
                <!-- Profile -->
                <ProfileTab v-if="activeTab === 0" :user="user" :profileEnabled="profileEnabled" />

                <!-- Security: password + 2FA + devices -->
                <div v-else-if="activeTab === 1" class="space-y-8">
                    <PasswordTab :passwordEnabled="passwordEnabled" />

                    <div class="border-border border-t pt-8">
                        <TwoFactorTab
                            :user="user"
                            :qrCodeSvg="qrCodeSvg"
                            :recoveryCodes="recoveryCodes"
                            :twoFactorEnabled="twoFactorEnabled" />
                    </div>

                    <div class="border-border border-t pt-8">
                        <DevicesTab :user="user" :sessions="sessions" />
                    </div>
                </div>

                <!-- Account: deactivate + delete -->
                <NotificationsTab
                    v-else-if="activeTab === tabIndex('Notifications')"
                    :preferences="notificationPreferences" />

                <AccountTab
                    v-else-if="activeTab === tabIndex('Account')"
                    :deactivateEnabled="deactivateEnabled"
                    :deleteEnabled="deleteEnabled" />
            </div>
        </div>
    </main>
</template>
