<template>

    <Head title="Login" />

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="main-heading">
            Login
        </h2>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-[384px] px-8">
        <div class="container-border p-5">
            <form @submit.prevent="submit" class="space-y-4 mt-2">
                <FloatLabel variant="on">
                    <InputText id="email" v-model="form.email" required type="email" :invalid="!!form.errors.email"
                        class="w-full" />
                    <label for="email">Email</label>
                </FloatLabel>
                <p v-if="form.errors.email" class="text-xs mt-2 text-red-600">
                    {{ form.errors.email }}
                </p>

                <div class="pt-2">
                    <FloatLabel variant="on">
                        <Password id="password" v-model="form.password" required fluid :invalid="!!form.errors.password"
                            :feedback="false" toggleMask />
                        <label for="password">Password</label>
                    </FloatLabel>
                </div>
                <p v-if="form.errors.password" class="text-xs mt-2 text-red-600">
                    {{ form.errors.password }}
                </p>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" v-model="form.remember"
                            class="h-4 w-4 rounded border-gray-300 text-gray-800 focus:ring-gray-800">
                        <label for="remember-me" class="ml-1.5 block text-sm leading-6 text-gray-800">
                            Remember me
                        </label>
                    </div>

                    <Link :href="route('password.request')"
                        class="text-sm underline text-gray-800 font-medium link-hover">
                    Forgot password?
                    </Link>
                </div>

                <Button @click="submit" :disabled="form.processing" label="Sign in" size="small"
                    class="w-full btn-primary" />
            </form>
        </div>

        <p class="mt-10 text-center text-sm text-gray-800">
            Not a member?
            <Link :href="route('register')"
                class="text-sm underline text-gray-800 font-medium link-hover">
            Create an account
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
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login')
}
</script>
