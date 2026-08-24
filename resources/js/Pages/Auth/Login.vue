<script setup>
import Button from '@/Components/Button.vue'
import { ref, onMounted, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '@js/Layouts/Auth.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'
import Modal from '@js/Components/Notifications/Modal.vue'
import Socialite from '@js/Components/Auth/Socialite.vue'
import Alert from '@js/Components/Notifications/Alert.vue'
import { MailIcon } from '@lucide/vue'
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
            <h1 class="text-foreground text-xl font-semibold">Sign in</h1>
        </header>

        <Alert v-if="status" type="info" class="mt-4">
            {{ status }}
        </Alert>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
            <FormInput
                id="email"
                v-model="form.email"
                placeholder=""
                label="Email address"
                name="email"
                type="email"
                :show-required-marker="false"
                required
                autocomplete="email"
                :error="form.errors.email" />

            <FormInput
                id="password"
                v-model="form.password"
                placeholder=""
                label="Password"
                name="password"
                type="password"
                :show-required-marker="false"
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
                    class="text-primary text-sm font-medium hover:underline"
                    aria-label="Reset forgotten password">
                    Forgot password?
                </Link>
            </div>

            <Button
                variant="primary"
                class="w-full"
                type="submit"
                :disabled="form.processing"
                :aria-busy="form.processing">
                {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </Button>
        </form>

        <template v-if="smLogin || passwordlessLogin">
            <div role="separator" class="relative my-6">
                <hr class="border-border border-t" />
                <span
                    class="bg-card text-muted-foreground absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 px-3 text-xs">
                    or continue with
                </span>
            </div>

            <div class="space-y-3">
                <Socialite v-if="smLogin" :providers-config="providersConfig" icons-only />

                <Button
                    v-if="passwordlessLogin"
                    variant="secondary"
                    class="w-full"
                    @click="showMagicLinkModal = true">
                    <MailIcon class="h-4 w-4" aria-hidden="true" />
                    Sign in with magic link
                </Button>
            </div>
        </template>

        <p v-if="canRegister" class="text-muted-foreground mt-8 text-center text-sm">
            Don't have an account?
            <Link :href="route('register')" class="text-primary font-medium hover:underline">
                Create one
            </Link>
        </p>
    </div>

    <Modal
        v-if="passwordlessLogin"
        :show="showMagicLinkModal"
        size="sm"
        description="We will email you a link that signs you in without a password."
        aria-labelledby="modal-title"
        @close="showMagicLinkModal = false">
        <template #title>
            <h2 id="modal-title">Sign in with magic link</h2>
        </template>

        <template #default>
            <form class="space-y-4" @submit.prevent="sendMagicLink">
                <p class="text-muted-foreground text-sm">
                    Enter your email and we'll send a secure sign-in link.
                </p>
                <FormInput
                    id="magic-link-email"
                    v-model="magicLinkForm.email"
                    placeholder=""
                    label="Email address"
                    name="magic-link-email"
                    type="email"
                    :show-required-marker="false"
                    required
                    :error="magicLinkForm.errors.email"
                    autocomplete="email" />
            </form>
        </template>

        <template #footer>
            <div class="flex justify-end gap-4">
                <Button variant="secondary" size="sm" @click="showMagicLinkModal = false">
                    Cancel
                </Button>
                <Button
                    variant="primary"
                    size="sm"
                    :disabled="magicLinkForm.processing"
                    :aria-busy="magicLinkForm.processing"
                    @click="sendMagicLink">
                    {{ magicLinkForm.processing ? 'Sending...' : 'Send link' }}
                </Button>
            </div>
        </template>
    </Modal>
</template>
