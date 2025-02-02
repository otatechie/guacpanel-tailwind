<template>

    <Head title="Password Update Required" />

    <div class="max-w-[384px] mx-auto px-8">
        <Logo />

        <h2 class="main-heading text-center">
            Password update required
        </h2>

        <div class="mt-6 p-5 container-border">
            <p class="text-gray-700 text-sm mb-4">
                Your password has expired. Please create a new password to continue
            </p>
            <form @submit.prevent="submit" class="space-y-4">
                <FormInput v-model="form.current_password" label="Current Password" id="current_password"
                    type="password" required :disabled="form.processing" :error="form.errors.current_password" />
                <FormInput v-model="form.password" label="Password" id="password" type="password" required
                    :disabled="form.processing" :error="form.errors.password" />
                <FormInput v-model="form.password_confirmation" label="Confirm Password" id="password_confirmation"
                    type="password" required :disabled="form.processing" :error="form.errors.password_confirmation" />
                <div class="bg-gray-50 p-4 rounded-md text-xs">
                    <p class="text-gray-700 mb-2">Password must include:</p>
                    <ul class="text-gray-700 space-y-1 list-disc pl-5">
                        <li>8+ characters</li>
                        <li>One uppercase letter</li>
                        <li>One number</li>
                        <li>One special character</li>
                    </ul>
                </div>
                <button @click="submit" :disabled="form.processing"
                    class="w-full btn-primary h-11 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ form.processing ? 'Confirming...' : 'Confirm password' }}
                </button>
            </form>
        </div>

        <p class="mt-8 text-center text-sm text-gray-700">
            Having trouble?
            <Link :href="route('home')" class="text-sm link">
            Contact support
            </Link>
        </p>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Logo from '../../Shared/Logo.vue'
import FormInput from '../../Components/FormInput.vue'

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const submit = () => {
    form.post(route('user.password.expired.update'))
}
</script>
