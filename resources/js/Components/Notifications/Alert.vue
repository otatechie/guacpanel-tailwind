<script setup>
import { computed, ref } from 'vue'
import { CircleCheckIcon, CircleXIcon, InfoIcon, TriangleAlertIcon, XIcon } from '@lucide/vue'
const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: v => ['info', 'warning', 'success', 'error', 'danger'].includes(v),
    },
    title: {
        type: String,
        default: '',
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['dismiss'])
const isVisible = ref(true)

const config = {
    info: {
        icon: InfoIcon,
        iconColor: 'text-blue-500',
        bg: 'bg-blue-50 dark:bg-blue-950/30',
        text: 'text-blue-800 dark:text-blue-300',
    },
    warning: {
        icon: TriangleAlertIcon,
        iconColor: 'text-amber-500',
        bg: 'bg-amber-50 dark:bg-amber-950/30',
        text: 'text-amber-800 dark:text-amber-300',
    },
    success: {
        icon: CircleCheckIcon,
        iconColor: 'text-green-500',
        bg: 'bg-green-50 dark:bg-green-950/30',
        text: 'text-green-800 dark:text-green-300',
    },
    error: {
        icon: CircleXIcon,
        iconColor: 'text-red-500',
        bg: 'bg-red-50 dark:bg-red-950/30',
        text: 'text-red-800 dark:text-red-300',
    },
}

/* Computed, not read once at setup: a bound `:type` used to leave the alert
   showing the icon and colours of whatever it was first rendered with. */
const resolvedType = computed(() => (props.type === 'danger' ? 'error' : props.type))

/* The validator only warns, so an unknown type still reaches here — without a
   fallback the template dereferences undefined and the page dies. */
const c = computed(() => config[resolvedType.value] ?? config.info)

/* `role="alert"` interrupts a screen reader, which is right for an error and
   wrong for the standing info and warning notes these mostly are. */
const role = computed(() => (resolvedType.value === 'error' ? 'alert' : 'status'))

const dismiss = () => {
    isVisible.value = false
    emit('dismiss')
}
</script>

<template>
    <div
        v-if="isVisible"
        :class="['flex items-start gap-2.5 rounded-md px-3.5 py-3 text-sm', c.bg]"
        :role="role">
        <component
            :is="c.icon"
            :class="['mt-0.5 h-4 w-4 shrink-0', c.iconColor]"
            aria-hidden="true" />
        <div :class="['min-w-0 flex-1', c.text]">
            <p v-if="title" class="mb-0.5 font-medium">{{ title }}</p>
            <p><slot /></p>
        </div>
        <button
            v-if="dismissible"
            type="button"
            :class="[
                'shrink-0 cursor-pointer rounded p-0.5 transition-colors hover:bg-black/5 dark:hover:bg-white/5',
                c.text,
            ]"
            aria-label="Dismiss"
            @click="dismiss">
            <XIcon class="h-3.5 w-3.5" />
        </button>
    </div>
</template>
