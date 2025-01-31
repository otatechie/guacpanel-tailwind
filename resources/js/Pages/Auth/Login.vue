<template>

    <Head title="Login" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center">Login</h2>

        <div class="mt-6 container-border p-5">
            <form @submit.prevent="submit" class="space-y-6 mt-2">
                <FormInput v-model="form.email" label="Email" id="email" type="email" required
                    :error="form.errors.email" />

                <FormInput v-model="form.password" label="Password" id="password" type="password" required
                    :error="form.errors.password" />

                <div class="flex items-center justify-between">
                    <FormCheckbox v-model="form.remember" label="Remember me" id="remember-me" />
                    <Link :href="route('password.request')" class="text-sm link">
                    Forgot password?
                    </Link>
                </div>

                <button @click="submit" :disabled="form.processing" class="w-full btn-primary">
                    Sign in
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-800">
            Not a member?
            <Link :href="route('register')" class="text-sm link">
            Create an account
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
    form.post('/login')
}
</script>
