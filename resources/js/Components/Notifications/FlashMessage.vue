<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import {
    CheckCircleIcon,
    XCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'

const typeConfig = {
    success: { icon: CheckCircleIcon, accent: 'text-green-600 dark:text-green-400', bg: 'bg-green-50 dark:bg-green-950/40', border: 'border-green-200 dark:border-green-900/50' },
    danger: { icon: XCircleIcon, accent: 'text-red-600 dark:text-red-400', bg: 'bg-red-50 dark:bg-red-950/40', border: 'border-red-200 dark:border-red-900/50' },
    warning: { icon: ExclamationTriangleIcon, accent: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-50 dark:bg-amber-950/40', border: 'border-amber-200 dark:border-amber-900/50' },
    info: { icon: InformationCircleIcon, accent: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-50 dark:bg-blue-950/40', border: 'border-blue-200 dark:border-blue-900/50' },
}

const flashMessageTypes = [
    { check: f => f.status === 'two-factor-authentication-enabled', message: 'Two-factor authentication enabled', type: 'success' },
    { check: f => f.status === 'two-factor-authentication-disabled', message: 'Two-factor authentication disabled', type: 'warning' },
    { check: f => f.status === 'recovery-codes-generated', message: 'Recovery codes generated', type: 'info' },
    { check: f => f.status === 'verification-link-sent', message: 'Verification link sent to your email', type: 'success' },
    { check: f => f.status === 'profile-information-updated', message: 'Profile updated', type: 'success' },
    { check: f => f.success || f.message || (f.status && !['two-factor-authentication-enabled', 'two-factor-authentication-disabled', 'recovery-codes-generated', 'verification-link-sent', 'profile-information-updated'].includes(f.status)), message: f => f.success || f.message || f.status, type: 'success' },
    { check: f => f.warning, message: f => f.warning, type: 'warning' },
    { check: f => f.info, message: f => f.info, type: 'info' },
    { check: f => f.error || f.danger, message: f => f.error || f.danger, type: 'danger' },
]

const toast = ref({ visible: false, type: 'success', message: '' })
const page = usePage()

const config = () => typeConfig[toast.value.type] || typeConfig.success

const show = (message, type = 'success', timeout = 5000) => {
    toast.value = { visible: true, type, message }
    setTimeout(() => { toast.value.visible = false }, timeout)
}

const close = () => { toast.value.visible = false }

if (typeof window !== 'undefined') {
    window.$showAlert = (title, message, type) => show(message || title, type)
    window.$closeAlert = close
}

watch(
    () => page.props.flash,
    flash => {
        if (!flash) return

        const errors = page.props.errors || {}
        if (Object.keys(errors).length > 0) {
            show('Please review the highlighted fields', 'warning')
            return
        }

        for (const ft of flashMessageTypes) {
            if (ft.check(flash)) {
                const msg = typeof ft.message === 'function' ? ft.message(flash) : ft.message
                show(msg, ft.type)
                return
            }
        }
    },
    { deep: true, immediate: true }
)
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-2 opacity-0">
        <div
            v-if="toast.visible"
            role="alert"
            aria-live="polite"
            class="fixed top-20 right-4 z-80 flex max-w-sm items-start gap-2.5 rounded-lg border px-4 py-3 shadow-lg"
            :class="[config().bg, config().border]">
            <component :is="config().icon" class="mt-0.5 h-5 w-5 shrink-0" :class="config().accent" aria-hidden="true" />
            <p class="flex-1 text-sm text-foreground">{{ toast.message }}</p>
            <button
                type="button"
                class="shrink-0 rounded p-0.5 text-muted-foreground transition-colors hover:text-foreground"
                aria-label="Dismiss"
                @click="close">
                <XMarkIcon class="h-4 w-4" />
            </button>
        </div>
    </Transition>
</template>
