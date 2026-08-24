<script setup>
import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Toaster from '@js/Components/Toaster.vue'
import { useToast } from '@js/composables/useToast'

defineProps({
    /** Distance from the top; auth pages have no header to clear */
    offset: { type: String, default: '72px' },
})

const flashMessageTypes = [
    {
        check: f => f.status === 'two-factor-authentication-enabled',
        message: 'Two-factor authentication enabled',
        type: 'success',
    },
    {
        check: f => f.status === 'two-factor-authentication-disabled',
        message: 'Two-factor authentication disabled',
        type: 'warning',
    },
    {
        check: f => f.status === 'recovery-codes-generated',
        message: 'Recovery codes generated',
        type: 'info',
    },
    {
        check: f => f.status === 'verification-link-sent',
        message: 'Verification link sent to your email',
        type: 'success',
    },
    {
        check: f => f.status === 'profile-information-updated',
        message: 'Profile updated',
        type: 'success',
    },
    {
        check: f =>
            f.success ||
            f.message ||
            (f.status &&
                ![
                    'two-factor-authentication-enabled',
                    'two-factor-authentication-disabled',
                    'recovery-codes-generated',
                    'verification-link-sent',
                    'profile-information-updated',
                ].includes(f.status)),
        message: f => f.success || f.message || f.status,
        type: 'success',
    },
    { check: f => f.warning, message: f => f.warning, type: 'warning' },
    { check: f => f.info, message: f => f.info, type: 'info' },
    { check: f => f.error || f.danger, message: f => f.error || f.danger, type: 'danger' },
]

const page = usePage()
const toast = useToast()

/* Was a `window.$showAlert` global registered by a component mounted in two
   layouts, with no unmount cleanup and last-mount-wins. Callers import the
   composable instead. */
watch(
    () => page.props.flash,
    flash => {
        if (!flash) return

        const errors = page.props.errors || {}
        if (Object.keys(errors).length > 0) {
            toast.warning('Please review the highlighted fields')
            return
        }

        for (const ft of flashMessageTypes) {
            if (ft.check(flash)) {
                const msg = typeof ft.message === 'function' ? ft.message(flash) : ft.message
                toast.show(msg, ft.type)
                return
            }
        }
    },
    { deep: true, immediate: true }
)
</script>

<template>
    <Toaster :offset="offset" />
</template>
