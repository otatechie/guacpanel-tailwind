<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Switch from '@js/Components/Forms/Switch.vue'

const props = defineProps({
    preferences: { type: Object, default: () => ({ muted_scopes: [] }) },
})

/* Muting, not subscribing: an empty list means everything reaches you, so a
   user who never opens this page keeps the behaviour they had. */
const form = useForm({
    muted_scopes: [...(props.preferences.muted_scopes ?? [])],
})

/* Scopes only. Severity used to be mutable here and is not any more: it cut
   across every topic at once, and a per-item dismiss in the feed and the banner
   already handles the one-off case. A scope is the recurring case dismissing
   cannot answer -- release notes arrive again every version. */
const SCOPES = [
    { value: 'system', label: 'System announcements', desc: 'Maintenance windows and outages.' },
    { value: 'release', label: 'Release notes', desc: "What changed in a version you're using." },
]

const pending = ref(null)

/* The switch reads "deliver this", so it is the inverse of the stored value.
   Storing mutes rather than subscriptions means a scope added later is on by
   default instead of silently missing for everyone. */
const isOn = value => !form.muted_scopes.includes(value)

/* Applied on the flick, like the admin settings switches: a switch that waits
   for a Save button promises an immediacy it does not have. */
const toggle = scope => {
    const previous = [...form.muted_scopes]

    form.muted_scopes = isOn(scope.value)
        ? [...form.muted_scopes, scope.value]
        : form.muted_scopes.filter(item => item !== scope.value)
    pending.value = scope.value

    form.post(route('user.notification.preferences'), {
        preserveScroll: true,
        // Inertia reuses this component on a same-page redirect, so useForm never
        // re-seeds from props: without this the switch keeps showing a state the
        // server rejected.
        onError: () => {
            form.muted_scopes = previous
        },
        onFinish: () => {
            pending.value = null
        },
    })
}

const onRowClick = (event, scope) => {
    if (event.target.closest('[role="switch"]')) return
    toggle(scope)
}
</script>

<template>
    <section class="max-w-2xl">
        <p class="text-muted-foreground text-sm">
            Notifications sent directly to you always arrive.
        </p>

        <div class="mt-4">
            <div
                v-for="scope in SCOPES"
                :key="scope.value"
                role="group"
                :aria-labelledby="`${scope.value}-label`"
                :aria-describedby="`${scope.value}-desc`"
                class="hover:bg-muted/40 -mx-3 flex cursor-pointer items-center justify-between gap-6 rounded-md px-3 py-3.5 transition-colors"
                @click="onRowClick($event, scope)">
                <div class="min-w-0">
                    <p :id="`${scope.value}-label`" class="text-foreground text-sm font-medium">
                        {{ scope.label }}
                    </p>
                    <p :id="`${scope.value}-desc`" class="text-muted-foreground mt-0.5 text-xs">
                        {{ scope.desc }}
                    </p>
                </div>
                <Switch
                    :model-value="isOn(scope.value)"
                    :disabled="pending === scope.value"
                    :label="scope.label"
                    :described-by="`${scope.value}-desc`"
                    @update:model-value="toggle(scope)" />
            </div>
        </div>
    </section>
</template>
