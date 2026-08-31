<script setup>
import { computed, ref, watch } from 'vue'
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
    restoreEnabled: { type: Boolean, default: false },
    daysToRestore: { type: Number, default: 0 },
    deletePasswordRequired: { type: Boolean, default: true },
    notificationsEnabled: { type: Boolean, default: false },
    notificationPreferences: { type: Object, default: () => ({}) },
})

/* The Notifications tab only exists where the feature does, so the tab indices
   below shift with it rather than being hard-coded. */
const tabs = computed(() =>
    props.notificationsEnabled
        ? ['Profile', 'Security', 'Notifications', 'Data']
        : ['Profile', 'Security', 'Data']
)

const tabIndex = name => tabs.value.indexOf(name)

/* ?tab= keeps the open tab through a reload and makes a section linkable.
   replaceState rather than push, so Back leaves the page instead of walking
   the tabs, and Inertia's own history state survives. */
const slug = name => name.toLowerCase()

const requestedTab = new URLSearchParams(window.location.search).get('tab')
const activeTab = ref(
    Math.max(
        0,
        tabs.value.findIndex(tab => slug(tab) === requestedTab)
    )
)

watch(activeTab, index => {
    const url = new URL(window.location.href)
    url.searchParams.set('tab', slug(tabs.value[index]))
    window.history.replaceState(window.history.state, '', url)
})
</script>

<template>
    <Head title="Account" />

    <main class="mx-auto max-w-4xl">
        <PageHeader
            title="Account"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Account' },
            ]" />

        <div class="border-border border-b">
            <Tabs v-model="activeTab" :tabs="tabs" panelId="account-panel" />
        </div>

        <div
            id="account-panel"
            class="py-6"
            role="tabpanel"
            aria-labelledby="account-panel-active-tab">
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

            <NotificationsTab
                v-else-if="activeTab === tabIndex('Notifications')"
                :preferences="notificationPreferences" />

            <AccountTab
                v-else-if="activeTab === tabIndex('Data')"
                :deactivateEnabled="deactivateEnabled"
                :deleteEnabled="deleteEnabled"
                :restoreEnabled="restoreEnabled"
                :daysToRestore="daysToRestore"
                :deletePasswordRequired="deletePasswordRequired" />
        </div>
    </main>
</template>
