<template>

    <Head title="Reset password" />

    <div>
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="main-heading">
                Reset your password
            </h2>
        </div>

        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-[384px] px-8">
            <div class="p-5 container-border">
                <p class="text-gray-800 text-sm mb-2">Password must include:</p>
                <ul class="text-gray-800 text-sm space-y-1 list-disc pl-5">
                    <li>8+ characters</li>
                    <li>One uppercase letter</li>
                    <li>One number</li>
                    <li>One special character</li>
                </ul>
                <form @submit.prevent="submit" class="space-y-6">
                    <input type="hidden" name="token" :value="form.token">
                    <input type="hidden" name="email" :value="form.email">
                    <FloatLabel variant="on">
                        <div>
                            <Password id="password" v-model="form.password" required fluid
                                :invalid="!!form.errors.password" toggleMask>
                            </Password>
                        </div>
                        <label for="password">Password</label>
                    </FloatLabel>
                    <div>
                        <FloatLabel variant="on">
                            <div>
                                <Password id="password_confirmation" v-model="form.password_confirmation" required fluid
                                    :invalid="!!form.errors.password_confirmation" toggleMask>
                                </Password>
                            </div>
                            <label for="password_confirmation">Confirm password</label>
                        </FloatLabel>
                        <p class="text-xs mt-2 text-red-600" v-if="form.errors.password_confirmation">{{
                            form.errors.password_confirmation }}
                        </p>
                    </div>
                    <div class="flex w-full justify-center ">
                        <Button @click="submit" :disabled="form.processing" label="Reset password"
                            size="small" class="w-full btn-primary" />
                    </div>
                </form>
            </div>
        </div>
    </div>

</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import Auth from '../../Layouts/Auth.vue';

// Props definition
const props = defineProps({
    token: String,
    email: String,
});

// Layout definition
defineOptions({
    layout: Auth,
});

// Form setup
const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'));
};
</script>
