<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'
import FormCheckbox from '../../Components/FormCheckbox.vue'
import Modal from '../../Components/Modal.vue'
import { ref } from 'vue'

defineOptions({
    layout: Auth
})

const { settings: { passwordlessLogin = true } = {} } = usePage().props

const form = useForm({
    email: 'ota@example.com',
    password: 'password',
    remember: false,
})

const showMagicLinkModal = ref(false)
const magicLinkForm = useForm({
    email: ''
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
        }
    })
}
</script>


<template>
    
    <Head title="Login" />

    <main class="max-w-[384px] mx-auto px-8" role="main">
        <h1 class="main-heading text-center dark:text-white">Login</h1>

        <form @submit.prevent="submit" class="mt-6 container-border p-5 space-y-6" aria-labelledby="login-form">
            <FormInput v-model="form.email" label="Email address" name="email" id="email" type="email" required
                autocomplete="email" :error="form.errors.email" />
            <section class="space-y-4">
                <FormInput v-model="form.password" label="Password" name="password" id="password" type="password"
                    required autocomplete="current-password" :error="form.errors.password" />
                <div class="flex items-center justify-between">
                    <FormCheckbox v-model="form.remember" label="Remember me" name="remember" id="remember-me" />
                    <Link :href="route('password.request')" class="text-sm link hover:underline"
                        aria-label="Reset forgotten password">
                    Forgot password?
                    </Link>
                </div>
            </section>

            <button type="submit" :disabled="form.processing" class="w-full btn-primary" aria-busy="form.processing">
                {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </button>

            <template v-if="passwordlessLogin">
                <div role="separator" aria-label="or separator" class="relative">
                    <hr class="border-t border-gray-300 dark:border-gray-600">
                    <span
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 px-2 bg-white dark:bg-gray-800 text-gray-500">OR</span>
                </div>

                <button type="button" @click="showMagicLinkModal = true"
                    class="w-full flex items-center justify-center gap-2 btn-secondary text-sm dark:hover:bg-gray-800 dark:text-gray-200 dark:hover:text-purple-400 transition-colors cursor-pointer"
                    aria-label="Open magic link login modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Login with magic link</span>
                </button>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400" role="note">
                    We'll send a secure login link to your email
                </p>
            </template>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-200">
            Don't have an account?
            <Link :href="route('register')" class="link hover:underline ml-1" aria-label="Go to registration page">
            Sign up
            </Link>
        </p>
    </main>

    <Modal v-if="passwordlessLogin" :show="showMagicLinkModal" @close="showMagicLinkModal = false" size="sm"
        aria-labelledby="modal-title">
        <template #title>
            <h2 id="modal-title">Login with Magic Link</h2>
        </template>

        <template #default>
            <form @submit.prevent="sendMagicLink" class="space-y-4" aria-labelledby="magic-link-form">
                <FormInput v-model="magicLinkForm.email" id="magic-link-email" label="Email address"
                    name="magic-link-email" type="email" required :error="magicLinkForm.errors.email"
                    autocomplete="email" />
                <p class="text-sm text-gray-500">
                    We'll send a secure login link to your email address.
                </p>
            </form>
        </template>

        <template #footer>
            <button @click="showMagicLinkModal = false" type="button" class="cursor-pointer mr-2 dark:text-gray-200"
                aria-label="Close modal">
                Cancel
            </button>
            <button @click="sendMagicLink" :disabled="magicLinkForm.processing" type="button" class="btn-primary"
                aria-busy="magicLinkForm.processing">
                {{ magicLinkForm.processing ? 'Sending...' : 'Send magic link' }}
            </button>
        </template>
    </Modal>
</template>
