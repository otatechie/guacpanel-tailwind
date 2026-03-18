<script setup>
import { ref } from 'vue'
import {
    InformationCircleIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    XCircleIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'

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

const resolvedType = props.type === 'danger' ? 'error' : props.type

const config = {
    info: {
        icon: InformationCircleIcon,
        iconColor: 'text-blue-500',
        bg: 'bg-blue-50 dark:bg-blue-950/30',
        text: 'text-blue-800 dark:text-blue-300',
    },
    warning: {
        icon: ExclamationTriangleIcon,
        iconColor: 'text-amber-500',
        bg: 'bg-amber-50 dark:bg-amber-950/30',
        text: 'text-amber-800 dark:text-amber-300',
    },
    success: {
        icon: CheckCircleIcon,
        iconColor: 'text-green-500',
        bg: 'bg-green-50 dark:bg-green-950/30',
        text: 'text-green-800 dark:text-green-300',
    },
    error: {
        icon: XCircleIcon,
        iconColor: 'text-red-500',
        bg: 'bg-red-50 dark:bg-red-950/30',
        text: 'text-red-800 dark:text-red-300',
    },
}

const c = config[resolvedType]

const dismiss = () => {
    isVisible.value = false
    emit('dismiss')
}
</script>

<template>
    <div
        v-if="isVisible"
        :class="['flex items-start gap-2.5 rounded-md px-3.5 py-3 text-sm', c.bg]"
        role="alert">
        <component :is="c.icon" :class="['mt-0.5 h-4 w-4 shrink-0', c.iconColor]" aria-hidden="true" />
        <div :class="['min-w-0 flex-1', c.text]">
            <p v-if="title" class="mb-0.5 font-medium">{{ title }}</p>
            <p><slot /></p>
        </div>
        <button
            v-if="dismissible"
            type="button"
            :class="['shrink-0 cursor-pointer rounded p-0.5 transition-colors hover:bg-black/5 dark:hover:bg-white/5', c.text]"
            aria-label="Dismiss"
            @click="dismiss">
            <XMarkIcon class="h-3.5 w-3.5" />
        </button>
    </div>
</template>
