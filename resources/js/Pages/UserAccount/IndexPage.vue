<template>
    <Head title="Profile" />

    <div class="px-5">
        <div class="divide-y divide-gray-900/10">
            <!-- Profile Section -->
            <div class="grid grid-cols-1 gap-x-8 md:grid-cols-3 pb-10">
                <div class="px-4 sm:px-0 pb-6">
                    <h2 class="sub-heading">Account</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-700">
                        Your name and other information is visible to others. Email address stays private.
                    </p>
                </div>

                <form @submit.prevent="submitProfileForm" class="container-border md:col-span-2">
                    <div class="px-4 py-6 sm:p-8">
                        <div class="md:max-w-md w-full space-y-8">
                            <FormInput
                                v-model="profileForm.name"
                                label="Legal name"
                                id="name"
                                type="text"
                                required
                                :error="profileForm.errors.name"
                            />

                            <FormInput
                                v-model="profileForm.email"
                                label="Email"
                                id="email"
                                type="email"
                                required
                                :error="profileForm.errors.email"
                            />

                            <div class="grid grid-cols-2 gap-4">
                                <FormInput
                                    v-model="profileForm.location"
                                    label="Location"
                                    id="location"
                                    type="text"
                                    required
                                    :error="profileForm.errors.location"
                                />
                                <FormSelect
                                    v-model="profileForm.region"
                                    :options="regions"
                                    label="Region"
                                    id="region"
                                    required
                                    :error="profileForm.errors.region"
                                    option-label="name"
                                    option-value="value"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-x-6 border-t border-gray-200 px-4 py-4 sm:px-8">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="btn-primary inline-flex items-center"
                        >
                            {{ profileForm.processing ? 'Updating...' : 'Update profile' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Section -->
            <div class="grid grid-cols-1 gap-x-8 pt-10 md:grid-cols-3">
                <div class="px-4 sm:px-0 pb-6">
                    <h2 class="sub-heading">Change Password</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-700">
                        Choose a strong password that's hard to guess and avoid common phrases.
                    </p>
                </div>

                <form @submit.prevent="submitPasswordForm" class="container-border md:col-span-2">
                    <div class="px-4 py-6 sm:p-8">
                        <div class="md:max-w-md w-full space-y-8">
                            <FormInput
                                v-model="passwordForm.current_password"
                                label="Current password"
                                id="current_password"
                                type="password"
                                required
                                :error="passwordForm.errors.current_password"
                            />

                            <FormInput
                                v-model="passwordForm.password"
                                label="Password"
                                id="password"
                                type="password"
                                required
                                :error="passwordForm.errors.password"
                            />

                            <FormInput
                                v-model="passwordForm.password_confirmation"
                                label="Confirm password"
                                id="password_confirmation"
                                type="password"
                                required
                                :error="passwordForm.errors.password_confirmation"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-x-6 border-t border-gray-200 px-4 py-4 sm:px-8">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="btn-primary inline-flex items-center"
                        >
                            {{ passwordForm.processing ? 'Updating...' : 'Update password' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Default from '../../Layouts/Default.vue'
import FormInput from '../../Components/FormInput.vue'
import FormSelect from '../../Components/FormSelect.vue'

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    countries: {
        type: Object,
        required: true
    },
})

defineOptions({ layout: Default })

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    location: props.user.location,
    region: props.user.region,
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const regions = ref([
    { name: 'Greater Accra', value: 'Greater Accra' },
    { name: 'Ashanti', value: 'Ashanti' },
    { name: 'Eastern', value: 'Eastern' },
    { name: 'Western', value: 'Western' },
    { name: 'Central', value: 'Central' },
    { name: 'Volta', value: 'Volta' },
    { name: 'Northern', value: 'Northern' },
    { name: 'Upper East', value: 'Upper East' },
    { name: 'Upper West', value: 'Upper West' },
    { name: 'Bono', value: 'Bono' },
    { name: 'Bono East', value: 'Bono East' },
    { name: 'Ahafo', value: 'Ahafo' },
    { name: 'Western North', value: 'Western North' },
    { name: 'Oti', value: 'Oti' },
    { name: 'Savannah', value: 'Savannah' },
    { name: 'North East', value: 'North East' }
].sort((a, b) => a.name.localeCompare(b.name)))

const submitProfileForm = () => {
    profileForm.put('/user/profile-information', {
        preserveScroll: true,
    })
}

const submitPasswordForm = () => {
    passwordForm.put('/user/password', {
        preserveScroll: true,
    })
}
</script>
