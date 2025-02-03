<template>

    <Head title="Create account" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center">
            Create account
        </h2>

        <div class="mt-6 container-border p-5">
            <form @submit.prevent="submit" class="space-y-6 mt-2">
                <FormInput v-model="form.name" label="Legal name" id="name" required :error="form.errors.name" />

                <FormInput v-model="form.email" label="Email" id="email" type="email" required
                    :error="form.errors.email" />

                <FormInput v-model="form.password" label="Password" id="password" type="password" required
                    :error="form.errors.password" />

                <FormInput v-model="form.password_confirmation" label="Confirm password" id="password_confirmation"
                    type="password" required :error="form.errors.password_confirmation" />

                <p class="text-sm text-gray-500">
                    By creating an account, you agree to our <span class="underline font-medium">Terms</span> and have
                    read and acknowledge the <span class="underline font-medium">Global Privacy</span> Statement.
                </p>

                <button @click="submit" :disabled="form.processing" class="w-full btn-primary">
                    Create account
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-800">
            Already a member?
            <Link :href="route('login')" class="text-sm link">
            Login
            </Link>
        </p>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/register')
}
</script>
