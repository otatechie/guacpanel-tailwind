<template>

    <Head title="Reset password" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center">
            Reset your password
        </h2>

        <div class="mt-6 p-5 container-border">
            <div class="bg-gray-50 p-4 rounded-md text-xs">
                <p class="text-gray-700 mb-2">Password must include:</p>
                <ul class="text-gray-700 space-y-1 list-disc pl-5">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
            </div>

            <form @submit.prevent="submit" class="space-y-6 mt-4">
                <input type="hidden" name="token" :value="form.token">
                <input type="hidden" name="email" :value="form.email">

                <FormInput v-model="form.password" label="Password" id="password" type="password" required
                    :error="form.errors.password" />

                <FormInput v-model="form.password_confirmation" label="Confirm password" id="password_confirmation"
                    type="password" required :error="form.errors.password_confirmation" />

                <button @click="submit" :disabled="form.processing"
                    class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ form.processing ? 'Please wait...' : 'Reset password' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

const props = defineProps({
    token: String,
    email: String,
})

defineOptions({
    layout: Auth
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.update'))
}
</script>
