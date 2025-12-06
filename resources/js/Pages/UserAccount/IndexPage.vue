<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Tabs from '@js/Components/Common/Tabs.vue'
import ProfileTab from '@js/Pages/UserAccount/Tabs/ProfileTab.vue'
import PasswordTab from '@js/Pages/UserAccount/Tabs/PasswordTab.vue'
import TwoFactorTab from '@js/Pages/UserAccount/Tabs/TwoFactorTab.vue'
import DevicesTab from '@js/Pages/UserAccount/Tabs/DevicesTab.vue'
import AccountTab from '@js/Pages/UserAccount/Tabs/AccountTab.vue'

defineOptions({
  layout: Default,
})

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  qrCodeSvg: {
    type: String,
    required: false,
    default: null,
  },
  recoveryCodes: {
    type: Array,
    required: false,
    default: () => [],
  },
  profileEnabled: {
    type: Boolean,
    default: false,
  },
  twoFactorEnabled: {
    type: Boolean,
    default: false,
  },
  passwordEnabled: {
    type: Boolean,
    default: false,
  },
  sessions: {
    type: Object,
  },
  deactivateEnabled: {
    type: Boolean,
    default: false,
  },
  deleteEnabled: {
    type: Boolean,
    default: false,
  },
})

const activeTab = ref(0)

const tabs = [
  { name: 'Profile', key: 'profile' },
  { name: 'Password', key: 'password' },
  { name: 'MFA', key: '2fa' },
  { name: 'Devices', key: 'devices' },
  { name: 'Account', key: 'account' },
]

const breadcrumbs = computed(() => {
  const currentTab = tabs[activeTab.value] ?? tabs[0]

  return [
    { label: 'Dashboard', href: route('dashboard') },
    { label: 'Account Settings', href: route('user.index') },
    { label: currentTab.name },
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
    description: description,
  }
})
</script>

<template>
  <Head title="Account Settings" />
  <main class="mx-auto max-w-7xl" aria-labelledby="profile-settings">
    <div class="container-border">
      <PageHeader
        :title="pageHeaderContent.title"
        :description="pageHeaderContent.description"
        :breadcrumbs="breadcrumbs" />

      <div class="overflow-hidden">
        <div class="px-3 sm:px-6">
          <Tabs v-model="activeTab" :tabs="tabs" />
        </div>

        <section class="relative">
          <div class="relative">
            <Transition name="tab-fade" mode="out-in" appear>
              <div v-if="activeTab === 0">
                <ProfileTab :user="user" :profileEnabled="profileEnabled" />
              </div>

              <div v-else-if="activeTab === 1">
                <PasswordTab :passwordEnabled="passwordEnabled" />
              </div>
              <div v-else-if="activeTab === 2">
                <TwoFactorTab
                  :user="user"
                  :qrCodeSvg="qrCodeSvg"
                  :recoveryCodes="recoveryCodes"
                  :twoFactorEnabled="twoFactorEnabled" />
              </div>
              <div v-else-if="activeTab === 3">
                <DevicesTab :user="user" :sessions="sessions" />
              </div>
              <div v-else-if="activeTab === 4">
                <AccountTab :deactivateEnabled="deactivateEnabled" :deleteEnabled="deleteEnabled" />
              </div>
            </Transition>
          </div>
        </section>
      </div>
    </div>
  </main>
</template>
