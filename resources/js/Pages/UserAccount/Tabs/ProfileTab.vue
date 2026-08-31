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

/* "avatar" is the initials image the User model generates from the name, so
   naming the option after the mechanism told nobody what they were picking. */
const avatarTypes = {
    avatar: { label: 'Initials', value: 'avatar' },
    gravatar: { label: 'Gravatar', value: 'gravatar' },
}

const currentAvatarUrl = computed(() =>
    form.image_type === 'gravatar' ? gravatarUrl.value : avatarUrl.value
)
</script>

<template>
    <section>
        <p class="text-muted-foreground text-sm">
            Your name, location and the picture shown next to your activity.
        </p>

        <Alert v-if="!profileEnabled" type="info" class="mt-4">
            Profile updates are disabled by your administrator.
        </Alert>

        <form class="mt-5" @submit.prevent="submit">
            <!-- Disabling the fieldset rather than the button alone: an admin lock
                 should stop you typing, not wait until you try to save. -->
            <fieldset :disabled="!profileEnabled" class="space-y-4">
                <!-- Location joins the grid: a full-width box for a city name
                     promises an entry the field will never get. -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <FormInput
                        v-model="form.name"
                        label="Name"
                        :error="form.errors.name"
                        required />
                    <FormInput
                        v-model="form.email"
                        label="Email"
                        type="email"
                        :error="form.errors.email"
                        help="Contact an administrator to change your email."
                        disabled />
                    <FormInput
                        v-model="form.location"
                        label="Location"
                        :error="form.errors.location" />
                </div>

                <!-- Preview after the choices, so this label starts on the same
                     line as Name and Location rather than indented past an image. -->
                <div class="flex items-end gap-4">
                    <FormRadioGroup
                        v-model="form.image_type"
                        label="Profile picture"
                        :options="avatarTypes" />
                    <img :src="currentAvatarUrl" :alt="user?.name" class="h-10 w-10 rounded-full" />
                </div>

                <div class="pt-2">
                    <Button variant="primary" size="sm" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </Button>
                </div>
            </fieldset>
        </form>
    </section>
</template>
