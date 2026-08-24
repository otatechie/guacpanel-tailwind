<script setup>
import Button from '@/Components/Button.vue'
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormRadioGroup from '@js/Components/Forms/FormRadioGroup.vue'
import Alert from '@js/Components/Notifications/Alert.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const gravatarUrl = computed(() => user.value?.gravatar)

const props = defineProps({
    user: { type: Object, required: true },
    profileEnabled: { type: Boolean, default: false },
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    location: props.user.location,
    image_type: props.user.profile_image_type,
})

const submit = () => form.put('/user/profile-information', { preserveScroll: true })

const avatarTypes = {
    avatar: { label: 'Avatar', value: 'avatar' },
    gravatar: { label: 'Gravatar', value: 'gravatar' },
}

const currentAvatarUrl = computed(() =>
    form.image_type === 'gravatar' ? gravatarUrl.value : avatarUrl.value
)
</script>

<template>
    <div class="max-w-2xl">
        <Alert v-if="!profileEnabled" type="info" class="mb-5">
            Profile updates are disabled by your administrator.
        </Alert>

        <form class="space-y-4" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <FormInput v-model="form.name" label="Name" :error="form.errors.name" required />
                <FormInput
                    v-model="form.email"
                    label="Email"
                    type="email"
                    :error="form.errors.email"
                    disabled />
            </div>

            <FormInput v-model="form.location" label="Location" :error="form.errors.location" />

            <div class="flex items-center gap-4">
                <img :src="currentAvatarUrl" :alt="user?.name" class="h-10 w-10 rounded-full" />
                <FormRadioGroup v-model="form.image_type" label="Avatar" :options="avatarTypes" />
            </div>

            <div class="pt-2">
                <Button
                    variant="primary"
                    size="sm"
                    type="submit"
                    :disabled="form.processing || !profileEnabled">
                    {{ form.processing ? 'Saving...' : 'Save' }}
                </Button>
            </div>
        </form>
    </div>
</template>
