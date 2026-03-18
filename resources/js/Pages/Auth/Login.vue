<script setup>
import { ref, onMounted, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Socialite from '@js/Components/Auth/Socialite.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

defineOptions({
    layout: Auth,
})

const props = defineProps({
    status: {
        type: String,
        default: null,
    },
    canResetPassword: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
    providersConfig: {
        type: Object,
        required: false,
    },
    demo: {
        type: Object,
        default: () => ({
            enabled: false,
            username: '',
            password: '',
        }),
    },
})

const page = usePage()

const { settings: { passwordlessLogin = true } = {} } = page.props

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

onMounted(() => {
    if (props.demo.enabled) {
        form.email = props.demo.username
        form.password = props.demo.password
    }
})

watch(
    () => props.demo,
    newDemo => {
        if (newDemo && newDemo.enabled) {
            form.email = newDemo.username
            form.password = newDemo.password
        }
    },
    { deep: true }
)

const smLogin = (() => {
    const providersConfig = props.providersConfig
    if (providersConfig.providers.length === 0) {
        return false
    }
    return true
})()

const showMagicLinkModal = ref(false)
const magicLinkForm = useForm({
    email: '',
})

const submit = () => {
    form.post(route('login'))
}

const sendMagicLink = () => {
    magicLinkForm.post(route('magic.login'), {
        onFinish: () => {
            if (!magicLinkForm.hasErrors) {
                showMagicLinkModal.value = false
            }
        },
    })
}
</script>

<template>
    <Head title="Sign in" />

    <div class="w-full" role="main">
        <header>
            <h1 class="text-2xl font-bold text-(--color-text)">Sign in</h1>
            <p class="mt-1 text-sm text-(--color-text-muted)">
                Enter your credentials to access your account
            </p>
        </header>

        <Alert v-if="status" type="info" class="mt-4">
            {{ status }}
        </Alert>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="email"
                v-model="form.email"
                label="Email address"
                name="email"
                type="email"
                required
                autocomplete="email"
                :error="form.errors.email" />

            <FormInput
                id="password"
                v-model="form.password"
                label="Password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                :error="form.errors.password" />

            <div class="flex items-center justify-between">
                <FormCheckbox
                    id="remember-me"
                    v-model="form.remember"
                    label="Remember me"
                    name="remember" />
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-(--primary-color) hover:underline"
                    aria-label="Reset forgotten password">
                    Forgot password?
                </Link>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="btn btn-primary w-full"
                :aria-busy="form.processing">
                {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </button>
        </form>

        <template v-if="smLogin || passwordlessLogin">
            <div role="separator" class="relative my-6">
                <hr class="border-t border-(--color-border)" />
                <span
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-(--color-bg) px-3 text-xs text-(--color-text-muted)">
                    or continue with
                </span>
            </div>

            <div class="space-y-3">
                <Socialite v-if="smLogin" :providers-config="providersConfig" />

                <button
                    v-if="passwordlessLogin"
                    type="button"
                    class="btn btn-secondary flex w-full items-center justify-center gap-2 text-sm"
                    @click="showMagicLinkModal = true">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Sign in with magic link
                </button>
            </div>
        </template>

        <p v-if="canRegister" class="mt-8 text-center text-sm text-(--color-text-muted)">
            Don't have an account?
            <Link
                :href="route('register')"
                class="font-medium text-(--primary-color) hover:underline">
                Create one
            </Link>
        </p>
    </div>

    <Modal
        v-if="passwordlessLogin"
        :show="showMagicLinkModal"
        size="sm"
        aria-labelledby="modal-title"
        @close="showMagicLinkModal = false">
        <template #title>
            <h2 id="modal-title">Sign in with magic link</h2>
        </template>

        <template #default>
            <form class="space-y-4" @submit.prevent="sendMagicLink">
                <p class="text-sm text-(--color-text-muted)">
                    Enter your email and we'll send a secure sign-in link.
                </p>
                <FormInput
                    id="magic-link-email"
                    v-model="magicLinkForm.email"
                    label="Email address"
                    name="magic-link-email"
                    type="email"
                    required
                    :error="magicLinkForm.errors.email"
                    autocomplete="email" />
            </form>
        </template>

        <template #footer>
            <div class="flex justify-end gap-4">
                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    @click="showMagicLinkModal = false">
                    Cancel
                </button>
                <button
                    :disabled="magicLinkForm.processing"
                    type="button"
                    class="btn btn-primary btn-sm"
                    :aria-busy="magicLinkForm.processing"
                    @click="sendMagicLink">
                    {{ magicLinkForm.processing ? 'Sending...' : 'Send link' }}
                </button>
            </div>
        </template>
    </Modal>
</template>
