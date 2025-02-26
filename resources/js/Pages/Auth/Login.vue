<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'
import FormCheckbox from '../../Components/FormCheckbox.vue'
import Modal from '../../Components/Modal.vue'
import { ref } from 'vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    email: '',
    password: '',
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
    magicLinkForm.post(route('magic.login.request'), {
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

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center dark:text-white">Login</h2>
        <div class="mt-6 container-border p-5">
            <form @submit.prevent="submit" class="space-y-6 mt-2">
                <FormInput v-model="form.email" label="Email address" name="email" id="email" type="email" required
                    autocomplete="email" :error="form.errors.email" />

                <div>
                    <FormInput v-model="form.password" label="Password" name="password" id="password" type="password"
                        required autocomplete="current-password" :error="form.errors.password" />
                    <div class="mt-4 flex items-center justify-between">
                        <FormCheckbox v-model="form.remember" label="Remember me" name="remember" id="remember-me" />
                        <Link :href="route('password.request')" class="text-sm link hover:underline">
                        Forgot password?
                        </Link>
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="w-full btn-primary">
                    {{ form.processing ? 'Signing in...' : 'Sign in' }}
                </button>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-900 text-gray-500">OR</span>
                    </div>
                </div>

                <button type="button" @click="showMagicLinkModal = true"
                    class="w-full flex items-center justify-center gap-2 btn-secondary dark:hover:bg-gray-800 transition-colors cursor-pointer hover:text-purple-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Login with magic link</span>
                </button>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                    We'll send a secure login link to your email
                </p>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-gray-800 dark:text-gray-200">
            Don't have an account?
            <Link :href="route('register')" class="link hover:underline ml-1">
            Sign up
            </Link>
        </p>
    </div>

    <Modal :show="showMagicLinkModal" @close="showMagicLinkModal = false" size="sm">
        <template #title>
            Login with Magic Link
        </template>

        <template #default>
            <form @submit.prevent="sendMagicLink" class="space-y-4">
                <FormInput v-model="magicLinkForm.email" id="magic-link-email" label="Email address"
                    name="magic-link-email" type="email" required :error="magicLinkForm.errors.email" />
                <p class="text-sm text-gray-500">
                    We'll send a secure login link to your email address.
                </p>
            </form>
        </template>

        <template #footer>
            <button @click="showMagicLinkModal = false" type="button" class="cursor-pointer mr-2">
                Cancel
            </button>
            <button @click="sendMagicLink" :disabled="magicLinkForm.processing" type="button" class="btn-primary">
                {{ magicLinkForm.processing ? 'Sending...' : 'Send magic link' }}
            </button>
        </template>
    </Modal>
</template>
