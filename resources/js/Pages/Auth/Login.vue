<template>

    <Head title="Login" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center mt-4">Login</h2>

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
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-800">
            Don't have an account?
            <Link :href="route('register')" class="link hover:underline ml-1">
            Sign up
            </Link>
        </p>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'
import FormCheckbox from '../../Components/FormCheckbox.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'))
}
</script>
