<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import Switch from '@js/Components/Forms/Switch.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    systemSettings: { type: Object, required: true, default: () => ({}) },
    twoFactorEnabled: { type: Boolean, default: false },
})

const form = useForm({
    password_expiry: Boolean(props.systemSettings?.password_expiry ?? false),
    passwordless_login: Boolean(props.systemSettings?.passwordless_login ?? true),
    two_factor_authentication: Boolean(props.systemSettings?.two_factor_authentication ?? false),
})

const save = () => {
    form.post(route('admin.setting.update'), { preserveScroll: true })
}

const toggle = field => {
    form[field] = !form[field]
    save()
}
</script>

<template>
    <Head title="Security settings" />

    <main class="mx-auto max-w-7xl" aria-labelledby="security-settings">
        <PageHeader
            title="Security settings"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System Settings', href: route('admin.setting.index') },
                { label: 'Security' },
            ]" />

        <div class="max-w-2xl space-y-4">
            <div class="card px-5 py-4" :class="{ 'opacity-50 pointer-events-none': form.processing }">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-base font-medium text-(--color-text)">Password expiration</p>
                        <p class="mt-0.5 text-sm text-(--color-text-muted)">Require password change every 90 days</p>
                    </div>
                    <Switch :model-value="form.password_expiry" @update:model-value="toggle('password_expiry')" aria-label="Toggle password expiration" />
                </div>
            </div>

            <div class="card px-5 py-4" :class="{ 'opacity-50 pointer-events-none': form.processing }">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-base font-medium text-(--color-text)">Two-factor authentication</p>
                        <p class="mt-0.5 text-sm text-(--color-text-muted)">
                            {{ twoFactorEnabled ? 'Require 2FA for all users' : 'Enable in Fortify config first' }}
                        </p>
                    </div>
                    <Switch :model-value="form.two_factor_authentication" :disabled="!twoFactorEnabled" @update:model-value="toggle('two_factor_authentication')" aria-label="Toggle two-factor authentication" />
                </div>
            </div>

            <div class="card px-5 py-4" :class="{ 'opacity-50 pointer-events-none': form.processing }">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-base font-medium text-(--color-text)">Passwordless login</p>
                        <p class="mt-0.5 text-sm text-(--color-text-muted)">Allow magic link sign-in without password</p>
                    </div>
                    <Switch :model-value="form.passwordless_login" @update:model-value="toggle('passwordless_login')" aria-label="Toggle passwordless login" />
                </div>
            </div>
        </div>
    </main>
</template>
