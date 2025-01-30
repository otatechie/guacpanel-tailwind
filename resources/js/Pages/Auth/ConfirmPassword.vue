<template>

    <Head title="Confirm password" />

    <div class="bg-stone-100 h-screen">
        <div>
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <Logo />
                <h2 class="main-heading">
                    Confirm access
                </h2>
            </div>

            <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-[384px] px-8">
                <div class="p-5 container-border">
                    <p class="text-gray-800 text-sm">Enter password to confirm access.</p>
                    <form @submit.prevent="submit" class="space-y-4 mt-4">
                        <div>
                            <FloatLabel variant="on">
                                <Password id="password" v-model="form.password" required fluid
                                    :invalid="!!form.errors.password" :feedback="false" toggleMask />
                                <label for="password">Password</label>
                            </FloatLabel>
                            <p class="text-xs mt-2 text-red-600" v-if="form.errors.password">{{ form.errors.password }}
                            </p>
                        </div>
                        <div>
                            <Button @click="submit" :disabled="form.processing" label="Confirm password"
                                size="small" class="w-full btn-primary" />
                        </div>
                    </form>
                </div>
                <p class="mt-10 text-center text-sm text-gray-800">
                    Back to
                    <Link :href="route('home')" class="font-medium underline text-gray-800 link-hover">
                    dashboard
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import Logo from '../../Shared/Logo.vue'

const form = useForm({
    password: '',
})

const submit = () => {
    form.post(route('password.confirm'));
}
</script>
