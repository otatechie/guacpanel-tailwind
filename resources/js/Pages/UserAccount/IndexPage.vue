<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@/Layouts/Default.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Tabs from '@/Components/Tabs.vue'
import ProfileTab from '@/Pages/UserAccount/Tabs/ProfileTab.vue'
import PasswordTab from '@/Pages/UserAccount/Tabs/PasswordTab.vue'
import TwoFactorTab from '@/Pages/UserAccount/Tabs/TwoFactorTab.vue'
import DevicesTab from '@/Pages/UserAccount/Tabs/DevicesTab.vue'
import AccountTab from '@/Pages/UserAccount/Tabs/AccountTab.vue'

defineOptions({
    layout: Default
})

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    qrCodeSvg: {
        type: String,
        required: false,
        default: null
    },
    recoveryCodes: {
        type: Array,
        required: false,
        default: () => []
    },
    profileEnabled: {
        type: Boolean,
        default: false
    },
    twoFactorEnabled: {
        type: Boolean,
        default: false
    },
    passwordEnabled: {
        type: Boolean,
        default: false
    },
    sessions: {
        type: Object,
    }
})

const activeTab = ref(0)

const tabs = [
    { name: 'Profile',                      key: 'profile' },
    { name: 'Password',                     key: 'password' },
    { name: 'MFA',    key: '2fa' },
    { name: 'Devices',                      key: 'devices' },
    { name: 'Account',                      key: 'account' },
]

const breadcrumbs = computed(() => {
    const currentTab = tabs[activeTab.value] ?? tabs[0]

    return [
        { label: 'Dashboard', href: route('dashboard') },
        { label: 'Account Settings', href: route('user.index') },
        { label: currentTab.name }
    ]
})

const pageHeaderContent = computed(() => {
    const currentTab = tabs[activeTab.value] ?? tabs[0]
    let title = 'Account Settings'
    let description = 'Manage your profile information, password, and account settings'

    if (currentTab.key == '2fa') {
        title = 'Multi-Factor Authentication'
        description = 'Add an extra layer of security to your account'
    }

    if (currentTab.key == 'devices') {
        title = 'Device Management'
        description = 'View and manage your active sessions'
    }

    return {
        title: title,
        description: description
    }
})

</script>

<template>
    <Head title="Account Settings" />
    <main class="max-w-7xl mx-auto" aria-labelledby="profile-settings">
        <div class="container-border">
            <PageHeader
                :title="pageHeaderContent.title"
                :description="pageHeaderContent.description"
                :breadcrumbs="breadcrumbs"
            />

            <div class="px-3 sm:px-6">
                <Tabs v-model="activeTab" :tabs="tabs" />
            </div>

            <!-- Basic Information Section -->
            <ProfileTab v-if="activeTab === 0" :user="user" :profileEnabled="profileEnabled" />

            <!-- Password Section -->
            <PasswordTab v-if="activeTab === 1" :passwordEnabled="passwordEnabled" />

            <!-- Two-Factor -->
            <TwoFactorTab v-if="activeTab === 2" :user="user" :qrCodeSvg="qrCodeSvg" :recoveryCodes="recoveryCodes" :twoFactorEnabled="twoFactorEnabled"/>

            <!-- Devices -->
            <DevicesTab v-if="activeTab === 3" :user="user" :sessions="sessions" />

            <!-- Danger Zone Section -->
            <AccountTab v-if="activeTab === 4" />

        </div>
    </main>
</template>
