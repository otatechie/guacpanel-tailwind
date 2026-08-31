<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Button from '@/Components/Button.vue'
import Sheet from '@js/Components/Notifications/Sheet.vue'
import FormInput from '@js/Components/Forms/FormInput.vue'
import FormSelect from '@js/Components/Forms/FormSelect.vue'
import FormTextarea from '@js/Components/Forms/FormTextarea.vue'

/* One form for both jobs. Create and edit differed only in where they posted,
   so two pages meant two copies of the same fields drifting apart. */
const props = defineProps({
    show: Boolean,
    /** null creates, a row edits */
    notification: { type: Object, default: null },
    users: { type: Array, default: () => [] },
})

const emit = defineEmits(['close'])

const isEdit = computed(() => Boolean(props.notification?.id))

const SCOPES = [
    { label: 'User', value: 'user' },
    { label: 'System', value: 'system' },
    { label: 'Release', value: 'release' },
]

const TYPES = [
    { label: 'Success', value: 'success' },
    { label: 'Info', value: 'info' },
    { label: 'Warning', value: 'warning' },
    { label: 'Danger', value: 'danger' },
]

const userOptions = computed(() =>
    (props.users || []).map(u => ({
        label: u.email ? `${u.name} (${u.email})` : u.name,
        value: u.id,
    }))
)

/* <input type="datetime-local"> only accepts local wall time, so an ISO string
   straight from the API silently leaves the field blank. */
const toDatetimeLocal = value => {
    if (!value) return ''
    const d = new Date(value)
    if (Number.isNaN(d.getTime())) return ''
    const pad = n => String(n).padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
}

const blank = () => ({
    scope: 'user',
    user_id: '',
    type: 'success',
    title: '',
    message: '',
    scheduled_on: '',
    auto_expire_on: '',
})

const seed = row =>
    row
        ? {
              scope: row.scope ?? 'user',
              user_id: row.user_id ?? '',
              type: row.type ?? 'success',
              title: row.title ?? '',
              message: row.message ?? '',
              scheduled_on: toDatetimeLocal(row.scheduled_on),
              auto_expire_on: toDatetimeLocal(row.auto_expire_on),
          }
        : blank()

const form = useForm(seed(props.notification))

/* The panel outlives any one row, so reopening it on a different notification
   has to re-seed. Errors go with it: last attempt's complaints are not this
   record's. */
watch(
    () => props.show,
    open => {
        if (!open) return
        form.clearErrors()
        form.defaults(seed(props.notification))
        form.reset()
    }
)

watch(
    () => form.scope,
    next => {
        if (next !== 'user') form.user_id = ''
    }
)

const submit = () => {
    const done = { preserveScroll: true, onSuccess: () => emit('close') }

    if (isEdit.value) {
        form.put(route('admin.notifications.update', props.notification.id), done)
    } else {
        form.post(route('admin.notifications.store'), done)
    }
}
</script>

<template>
    <Sheet :show="show" size="lg" @close="emit('close')">
        <template #title>{{ isEdit ? 'Edit notification' : 'Create notification' }}</template>

        <form id="notification-form" class="space-y-5" @submit.prevent="submit">
            <FormInput v-model="form.title" label="Title" :error="form.errors.title" required />
            <FormTextarea
                v-model="form.message"
                label="Message"
                :error="form.errors.message"
                :rows="3"
                required />

            <div class="border-border grid grid-cols-1 gap-4 border-t pt-5 sm:grid-cols-2">
                <FormSelect
                    v-model="form.scope"
                    label="Scope"
                    :options="SCOPES"
                    :error="form.errors.scope" />
                <FormSelect
                    v-model="form.type"
                    label="Type"
                    :options="TYPES"
                    :error="form.errors.type" />
                <FormSelect
                    v-if="form.scope === 'user'"
                    v-model="form.user_id"
                    label="User"
                    placeholder="Select user"
                    :options="userOptions"
                    :error="form.errors.user_id"
                    class="sm:col-span-2" />
            </div>

            <div class="border-border grid grid-cols-1 gap-4 border-t pt-5 sm:grid-cols-2">
                <FormInput
                    v-model="form.scheduled_on"
                    label="Schedule"
                    type="datetime-local"
                    :error="form.errors.scheduled_on"
                    help="Leave blank to send immediately" />
                <FormInput
                    v-model="form.auto_expire_on"
                    label="Auto expire"
                    type="datetime-local"
                    :error="form.errors.auto_expire_on"
                    help="Optional" />
            </div>
        </form>

        <template #footer>
            <div class="flex w-full justify-end gap-3">
                <Button variant="secondary" size="sm" @click="emit('close')">Cancel</Button>
                <Button
                    variant="primary"
                    size="sm"
                    type="submit"
                    form="notification-form"
                    :disabled="form.processing">
                    {{
                        form.processing
                            ? isEdit
                                ? 'Saving...'
                                : 'Creating...'
                            : isEdit
                              ? 'Save changes'
                              : 'Create notification'
                    }}
                </Button>
            </div>
        </template>
    </Sheet>
</template>
