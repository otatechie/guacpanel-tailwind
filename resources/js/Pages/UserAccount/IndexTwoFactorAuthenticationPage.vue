<script setup>
import { Head } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'
import Badge from '@js/Components/Badge.vue'
import TwoFactorTab from '@js/Pages/UserAccount/Tabs/TwoFactorTab.vue'

/* Same component as the account page's Security tab: one implementation, so
   enabling 2FA cannot look like two different features depending on the door. */
defineOptions({
    layout: Default,
})

defineProps({
    user: { type: Object, required: true },
    qrCodeSvg: { type: String, default: null },
    recoveryCodes: { type: Array, default: () => [] },
    twoFactorEnabled: { type: Boolean, default: false },
})
</script>

<template>
    <Head title="Two-factor authentication" />

    <main class="mx-auto max-w-4xl">
        <PageHeader
            title="Two-factor authentication"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'Account', href: route('user.index') },
                { label: 'Two-factor authentication' },
            ]">
            <template #actions>
                <Badge dot :variant="user.two_factor_secret ? 'success' : 'neutral'">
                    {{ user.two_factor_secret ? 'On' : 'Off' }}
                </Badge>
            </template>
        </PageHeader>

        <TwoFactorTab
            :user="user"
            :qrCodeSvg="qrCodeSvg"
            :recoveryCodes="recoveryCodes"
            :twoFactorEnabled="twoFactorEnabled"
            :showHeading="false" />
    </main>
</template>
