<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'
import FormInput from '../../Components/FormInput.vue'

defineOptions({
    layout: Auth
})

const props = defineProps({
    user: Object,
})

const form = useForm({
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('user.force.password.change.update'), {
    })
}
</script>

<template>

    <Head title="Reset password" />

    <div class="max-w-[384px] mx-auto px-8">
        <h2 class="main-heading text-center">
            Change your password
        </h2>

        <div class="mt-6 p-5 container-border">
            <p class="text-gray-700 text-sm mb-2">
                Your password needs to be updated. Enter a new one to proceed.
            </p>
            <div class="bg-gray-50 p-4 rounded-md text-xs">
                <p class="text-gray-500 mb-2">Password must include:</p>
                <ul class="text-gray-500 space-y-1 list-disc pl-5">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
            </div>

            <form @submit.prevent="submit" class="space-y-6 mt-4">
                <FormInput v-model="form.password" label="Password" id="password" type="password" required
                    :error="form.errors.password" />

                <FormInput v-model="form.password_confirmation" label="Confirm password" id="password_confirmation"
                    type="password" required :error="form.errors.password_confirmation" autocomplete="new-password" />

                <button @click="submit" :disabled="form.processing"
                    class="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ form.processing ? 'Please wait...' : 'Change password' }}
                </button>
            </form>
        </div>
    </div>
</template>
