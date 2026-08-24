<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import Default from '@js/Layouts/Default.vue'
import Button from '@js/Components/Button.vue'
import Switch from '@js/Components/Forms/Switch.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import PageHeader from '@js/Components/Common/PageHeader.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    systemSettings: { type: Object, required: true, default: () => ({}) },
    twoFactorEnabled: { type: Boolean, default: false },
    lastChanged: { type: Object, default: null },
})

const form = useForm({
    password_expiry: Boolean(props.systemSettings?.password_expiry ?? false),
    passwordless_login: Boolean(props.systemSettings?.passwordless_login ?? true),
    two_factor_authentication: Boolean(props.systemSettings?.two_factor_authentication ?? false),
})

/** field currently in flight, so one row's save does not freeze the others */
const pending = ref(null)

const save = field => {
    const previous = form[field]
    form[field] = !previous
    pending.value = field

    form.post(route('admin.setting.update'), {
        preserveScroll: true,
        /* Inertia reuses this component on a same-page redirect, so useForm never
           re-seeds from props. Without this the switch keeps showing a state the
           server rejected. */
        onError: () => {
            form[field] = previous
        },
        onFinish: () => {
            pending.value = null
        },
    })
}

/* Both of these weaken authentication for every user at once, and apply the
   instant they are clicked. Confirm the weakening direction only — turning
   protection back on needs no ceremony. */
const confirmations = {
    passwordless_login: {
        on: true,
        title: 'Allow sign-in without a password?',
        body: 'Anyone who can read a user’s email inbox will be able to sign in as them.',
        action: 'Allow passwordless login',
    },
    two_factor_authentication: {
        on: false,
        title: 'Stop requiring two-factor authentication?',
        body: 'Every account falls back to a password alone. Users who already set up an authenticator app keep it, but it is no longer enforced.',
        action: 'Stop requiring 2FA',
    },
}

const confirming = ref(null)

const requestToggle = option => {
    if (option.disabled || pending.value === option.field) return

    const next = !form[option.field]
    const rule = confirmations[option.field]

    if (rule && rule.on === next) {
        confirming.value = { field: option.field, ...rule }
        return
    }

    save(option.field)
}

const applyConfirmed = () => {
    const field = confirming.value.field
    confirming.value = null
    save(field)
}

/* The switch is a 32px target at the far end of a wide row; the label is the
   thing people aim at. Clicks that started on the switch are already handled. */
const onRowClick = (event, option) => {
    if (event.target.closest('[role="switch"]')) return
    requestToggle(option)
}

const options = computed(() => [
    {
        field: 'password_expiry',
        label: 'Password expiration',
        desc: 'Require a password change every 3 months. Users past that point are sent to a change-password screen on their next request.',
    },
    {
        field: 'two_factor_authentication',
        label: 'Two-factor authentication',
        desc: props.twoFactorEnabled
            ? 'Require every user to confirm sign-in with an authenticator app.'
            : 'Turned off for this installation — set APP_MFA_ENABLED=true in your .env file to make it available.',
        disabled: !props.twoFactorEnabled,
    },
    {
        field: 'passwordless_login',
        label: 'Passwordless login',
        desc: 'Let users sign in with a link emailed to them instead of a password.',
    },
])
</script>

<template>
    <Head title="Security settings" />

    <main class="mx-auto max-w-4xl" aria-labelledby="security-settings">
        <PageHeader
            id="security-settings"
            title="Security settings"
            :breadcrumbs="[
                { label: 'Dashboard', href: route('dashboard') },
                { label: 'System settings', href: route('admin.setting.index') },
                { label: 'Security settings' },
            ]" />

        <section class="max-w-2xl">
            <h2 class="text-muted-foreground mb-2 text-xs font-medium">Authentication</h2>
            <p class="text-muted-foreground text-sm">
                Changes apply to every account the moment you make them — there is no save button.
            </p>

            <div class="divide-border mt-4 divide-y">
                <div
                    v-for="option in options"
                    :key="option.field"
                    role="group"
                    :aria-labelledby="`${option.field}-label`"
                    :aria-describedby="`${option.field}-desc`"
                    class="-mx-3 flex items-center justify-between gap-6 px-3 py-3.5 transition-colors"
                    :class="
                        option.disabled
                            ? 'cursor-not-allowed'
                            : 'hover:bg-muted/40 cursor-pointer rounded-md'
                    "
                    @click="onRowClick($event, option)">
                    <div class="min-w-0">
                        <p
                            :id="`${option.field}-label`"
                            class="text-foreground text-sm font-medium">
                            {{ option.label }}
                        </p>
                        <p
                            :id="`${option.field}-desc`"
                            class="text-muted-foreground mt-0.5 text-xs">
                            {{ option.desc }}
                        </p>
                    </div>
                    <Switch
                        :model-value="form[option.field]"
                        :disabled="option.disabled || pending === option.field"
                        :label="option.label"
                        :described-by="`${option.field}-desc`"
                        @update:model-value="requestToggle(option)" />
                </div>
            </div>

            <p v-if="lastChanged" class="text-muted-foreground mt-4 text-xs">
                Last changed {{ lastChanged.at }}
                <template v-if="lastChanged.by">by {{ lastChanged.by }}</template>
                ·
                <Link
                    :href="route('admin.audit.index')"
                    class="hover:text-foreground underline underline-offset-2">
                    Activity log
                </Link>
            </p>
        </section>
    </main>

    <Modal
        :show="!!confirming"
        size="md"
        description="Applies to every account, immediately."
        @close="confirming = null">
        <template #title>{{ confirming?.title }}</template>
        <template #default>
            <p class="text-foreground text-sm">{{ confirming?.body }}</p>
        </template>
        <template #footer>
            <div class="flex items-center justify-end gap-3">
                <Button variant="secondary" size="sm" @click="confirming = null">Cancel</Button>
                <Button variant="danger" size="sm" @click="applyConfirmed">
                    {{ confirming?.action }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
