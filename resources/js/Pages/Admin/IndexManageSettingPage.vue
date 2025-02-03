<template>
    <div class="max-w-7xl mx-auto">
        <div class="container-border p-6">
            <div class="mb-8">
                <h2 class="sub-heading">Account Security Settings</h2>
                <p class="mt-1 text-gray-500 font-medium"> Configure authentication settings and security requirements
                    for your users
                </p>
            </div>

            <div class="w-full md:w-4xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-700">Initial Password Change</h3>
                                <p class="mt-1 text-sm text-gray-500"> Require users to set a new password when they
                                    first
                                    access their account</p>
                            </div>
                            <label for="forcePasswordChange"
                                class="relative inline-block h-8 w-14 cursor-pointer rounded-full bg-gray-300 transition [-webkit-tap-highlight-color:_transparent] has-[:checked]:bg-green-500">
                                <input type="checkbox" id="forcePasswordChange" v-model="form.force_password_change"
                                    class="peer sr-only [&:checked_+_span_svg[data-checked-icon]]:block [&:checked_+_span_svg[data-unchecked-icon]]:hidden" />
                                <span
                                    class="absolute inset-y-0 start-0 z-10 m-1 inline-flex size-6 items-center justify-center rounded-full bg-white text-gray-400 transition-all peer-checked:start-6 peer-checked:text-green-600">
                                    <svg data-unchecked-icon xmlns="http://www.w3.org/2000/svg" class="size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg data-checked-icon xmlns="http://www.w3.org/2000/svg" class="hidden size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-700">Password Expiration Policy</h3>
                                <p class="my-2 text-sm text-gray-500">Require regular password updates to maintain
                                    security
                                    standards</p>
                                <div v-if="form.password_expiry"
                                    class="mt-2 max-w-md flex items-start gap-2 p-2 rounded-md bg-amber-50 border border-amber-200 rounded-lg backdrop-blur-sm">
                                    <p class="text-sm text-amber-600 flex items-center gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="leading-tight">Passwords must be changed every 90 days</span>
                                    </p>
                                </div>
                            </div>
                            <label for="passwordExpiry"
                                class="relative inline-block h-8 w-14 cursor-pointer rounded-full bg-gray-300 transition [-webkit-tap-highlight-color:_transparent] has-[:checked]:bg-green-500">
                                <input type="checkbox" id="passwordExpiry" v-model="form.password_expiry"
                                    class="peer sr-only [&:checked_+_span_svg[data-checked-icon]]:block [&:checked_+_span_svg[data-unchecked-icon]]:hidden" />
                                <span
                                    class="absolute inset-y-0 start-0 z-10 m-1 inline-flex size-6 items-center justify-center rounded-full bg-white text-gray-400 transition-all peer-checked:start-6 peer-checked:text-green-600">
                                    <svg data-unchecked-icon xmlns="http://www.w3.org/2000/svg" class="size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg data-checked-icon xmlns="http://www.w3.org/2000/svg" class="hidden size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-700">Enable 2FA Requirement</h3>
                                <p class="my-2 text-sm text-gray-500"> Add an extra layer of security by making
                                    two-factor
                                    authentication mandatory</p>
                            </div>
                            <label for="twoFactorAuthentication"
                                class="relative inline-block h-8 w-14 cursor-pointer rounded-full bg-gray-300 transition [-webkit-tap-highlight-color:_transparent] has-[:checked]:bg-green-500">
                                <input type="checkbox" id="twoFactorAuthentication"
                                    v-model="form.two_factor_authentication"
                                    class="peer sr-only [&:checked_+_span_svg[data-checked-icon]]:block [&:checked_+_span_svg[data-unchecked-icon]]:hidden" />
                                <span
                                    class="absolute inset-y-0 start-0 z-10 m-1 inline-flex size-6 items-center justify-center rounded-full bg-white text-gray-400 transition-all peer-checked:start-6 peer-checked:text-green-600">
                                    <svg data-unchecked-icon xmlns="http://www.w3.org/2000/svg" class="size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg data-checked-icon xmlns="http://www.w3.org/2000/svg" class="hidden size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-gray-700">Passwordless Login</h3>
                                <p class="my-2 text-sm text-gray-500"> Allow users to log in without entering a password
                                </p>
                            </div>
                            <label for="passwordlessLogin"
                                class="relative inline-block h-8 w-14 cursor-pointer rounded-full bg-gray-300 transition [-webkit-tap-highlight-color:_transparent] has-[:checked]:bg-green-500">
                                <input type="checkbox" id="passwordlessLogin" v-model="form.passwordless_login"
                                    class="peer sr-only [&:checked_+_span_svg[data-checked-icon]]:block [&:checked_+_span_svg[data-unchecked-icon]]:hidden" />
                                <span
                                    class="absolute inset-y-0 start-0 z-10 m-1 inline-flex size-6 items-center justify-center rounded-full bg-white text-gray-400 transition-all peer-checked:start-6 peer-checked:text-green-600">
                                    <svg data-unchecked-icon xmlns="http://www.w3.org/2000/svg" class="size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg data-checked-icon xmlns="http://www.w3.org/2000/svg" class="hidden size-4"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Default from '../../Layouts/Default.vue'
import { useForm } from '@inertiajs/vue3'
import Switch from '@/Components/Switch.vue'

defineOptions({
    layout: Default,
})

const props = defineProps({
    settings: Object
})

const form = useForm({
    force_password_change: Boolean(props.settings?.force_password_change ?? false),
    password_expiry: Boolean(props.settings?.password_expiry ?? false),
    passwordless_login: Boolean(props.settings?.passwordless_login ?? false),
    two_factor_authentication: Boolean(props.settings?.two_factor_authentication ?? false),
})

const submit = () => {
    form.post(route('admin.setting.update'))
}
</script>
