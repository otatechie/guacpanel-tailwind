<script setup>
import { useForm } from '@inertiajs/vue3'
import Button from '@/Components/Button.vue'
import FormCheckbox from '@js/Components/Forms/FormCheckbox.vue'

const props = defineProps({
    preferences: { type: Object, default: () => ({ muted_scopes: [], muted_types: [] }) },
})

/* Muting, not subscribing: an empty list means everything reaches you, so a
   user who never opens this page keeps the behaviour they had. */
const form = useForm({
    muted_scopes: [...(props.preferences.muted_scopes ?? [])],
    muted_types: [...(props.preferences.muted_types ?? [])],
})

const SCOPES = [
    { value: 'system', label: 'System announcements', help: 'Maintenance windows and outages.' },
    { value: 'release', label: 'Release notes', help: "What changed in a version you're using." },
]

const TYPES = [
    { value: 'error', label: 'Errors' },
    { value: 'warning', label: 'Warnings' },
    { value: 'success', label: 'Confirmations' },
    { value: 'info', label: 'Information' },
]

/* The checkbox reads "deliver this", so it is the inverse of the stored value.
   Storing mutes rather than subscriptions means a notification type added later
   is on by default instead of silently missing for everyone. */
const isOn = (list, value) => !form[list].includes(value)

const toggle = (list, value, on) => {
    form[list] = on ? form[list].filter(item => item !== value) : [...form[list], value]
}

const submit = () => form.post(route('user.notification.preferences'), { preserveScroll: true })
</script>

<template>
    <section class="max-w-2xl">
        <p class="text-foreground text-base font-medium">Notifications</p>
        <p class="text-muted-foreground mt-1 text-sm">
            Choose what reaches you in the app. Notifications addressed to you personally are always
            delivered.
        </p>

        <form class="mt-5 space-y-6" @submit.prevent="submit">
            <fieldset>
                <legend class="text-foreground mb-2 text-xs font-medium">Announcements</legend>
                <div class="space-y-2">
                    <FormCheckbox
                        v-for="scope in SCOPES"
                        :key="scope.value"
                        :id="`scope-${scope.value}`"
                        :label="scope.label"
                        :help="scope.help"
                        :model-value="isOn('muted_scopes', scope.value)"
                        @update:model-value="toggle('muted_scopes', scope.value, $event)" />
                </div>
            </fieldset>

            <fieldset>
                <legend class="text-foreground mb-2 text-xs font-medium">By severity</legend>
                <div class="grid gap-2 sm:grid-cols-2">
                    <FormCheckbox
                        v-for="type in TYPES"
                        :key="type.value"
                        :id="`type-${type.value}`"
                        :label="type.label"
                        :model-value="isOn('muted_types', type.value)"
                        @update:model-value="toggle('muted_types', type.value, $event)" />
                </div>
            </fieldset>

            <Button type="submit" variant="primary" size="sm" :disabled="form.processing">
                {{ form.processing ? 'Saving...' : 'Save preferences' }}
            </Button>
        </form>
    </section>
</template>
