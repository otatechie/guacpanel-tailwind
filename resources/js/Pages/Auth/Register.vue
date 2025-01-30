<template>

    <Head title="Create account" />

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="main-heading">
            Create account
        </h2>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-[384px] px-8">
        <div class="container-border p-5">
            <form @submit.prevent="submit" class="space-y-4 mt-2">
                <div>
                    <FloatLabel variant="on">
                        <InputText id="name" v-model="form.name" required :invalid="!!form.errors.name"
                            class="w-full" />
                        <label for="name">Legal name</label>
                    </FloatLabel>
                    <p v-if="form.errors.name" class="text-xs mt-2 text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="pt-2">
                    <FloatLabel variant="on">
                        <InputText id="email" v-model="form.email" required type="email" :invalid="!!form.errors.email"
                            class="w-full" />
                        <label for="email">Email</label>
                    </FloatLabel>
                    <p v-if="form.errors.email" class="text-xs mt-2 text-red-600">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div class="pt-2">
                    <FloatLabel variant="on">
                        <Password id="password" v-model="form.password" required fluid :invalid="!!form.errors.password"
                            :feedback="false" toggleMask />
                        <label for="password">Password</label>
                    </FloatLabel>
                    <p v-if="form.errors.password" class="text-xs mt-2 text-red-600">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="pt-2">
                    <FloatLabel variant="on">
                        <Password id="password_confirmation" v-model="form.password_confirmation" required fluid
                            :invalid="!!form.errors.password_confirmation" :feedback="false" toggleMask />
                        <label for="password_confirmation">Confirm password</label>
                    </FloatLabel>
                    <p v-if="form.errors.password_confirmation" class="text-xs mt-2 text-red-600">
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>

                <p class="text-sm text-gray-700">
                    By creating an account, you agree to our <span class="underline font-medium">Terms</span> and have
                    read and
                    acknowledge the <span class="underline font-medium">Global Privacy</span> Statement.
                </p>

                <Button @click="submit" :disabled="form.processing" label="Create account" size="small"
                    class="w-full btn-primary" />
            </form>
        </div>

        <p class="py-10 text-center text-sm text-gray-800">
            Already a member?
            <Link :href="route('login')"
                class="text-sm underline text-gray-800 font-medium link-hover">
            Login
            </Link>
        </p>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Auth from '../../Layouts/Auth.vue'

defineOptions({
    layout: Auth
})

const form = useForm({
    name: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post('/register')
}
</script>
