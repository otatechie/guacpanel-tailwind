<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const alertConfigs = {
    success: {
        iconColor: 'text-green-600 bg-green-100 dark:text-green-300 dark:bg-green-900/20',
        bgColor: 'bg-green-100 dark:bg-green-900',
        textColor: 'text-green-700 dark:text-green-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z',
    },
    danger: {
        iconColor: 'text-red-600 bg-red-100 dark:text-red-300 dark:bg-red-900/20',
        bgColor: 'bg-red-100 dark:bg-red-900',
        textColor: 'text-red-700 dark:text-red-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z',
    },
    warning: {
        iconColor: 'text-orange-600 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/20',
        bgColor: 'bg-orange-100 dark:bg-orange-900',
        textColor: 'text-orange-700 dark:text-orange-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z',
    },
    info: {
        iconColor: 'text-blue-600 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/20',
        bgColor: 'bg-blue-100 dark:bg-blue-900',
        textColor: 'text-blue-700 dark:text-blue-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z',
    },
}

const flashMessageTypes = [
    {
        check: flash => flash.status === 'two-factor-authentication-enabled',
        title: 'Two-Factor Authentication',
        getMessage: () => 'Two-factor authentication has been enabled.',
        type: 'success',
    },
    {
        check: flash => flash.status === 'two-factor-authentication-disabled',
        title: 'Two-Factor Authentication',
        getMessage: () => 'Two-factor authentication has been disabled.',
        type: 'warning',
    },
    {
        check: flash => flash.status === 'recovery-codes-generated',
        title: 'Recovery Codes',
        getMessage: () => 'Recovery codes have been successfully generated.',
        type: 'info',
    },
    {
        check: flash => flash.status === 'verification-link-sent',
        title: 'Verification Link',
        getMessage: () => 'A new email verification link has been emailed to you!',
        type: 'success',
    },
    {
        check: flash => flash.status === 'profile-information-updated',
        title: 'Profile Updated',
        getMessage: () => 'Your profile information has been updated.',
        type: 'success',
    },
    {
        check: flash =>
            flash.success ||
            flash.message ||
            (flash.status &&
                ![
                    'two-factor-authentication-enabled',
                    'two-factor-authentication-disabled',
                    'recovery-codes-generated',
                    'verification-link-sent',
                    'profile-information-updated',
                ].includes(flash.status)),
        title: 'Success',
        getMessage: flash => flash.success || flash.message || flash.status,
        type: 'success',
    },
    {
        check: flash => flash.warning,
        title: 'Warning',
        getMessage: flash => flash.warning,
        type: 'warning',
    },
    {
        check: flash => flash.info,
        title: 'Info',
        getMessage: flash => flash.info,
        type: 'info',
    },
    {
        check: flash => flash.error || flash.danger,
        title: 'Error',
        getMessage: flash => flash.error || flash.danger,
        type: 'danger',
    },
]

const defaultAlertState = {
    visible: false,
    type: 'success',
    title: '',
    message: '',
    ...alertConfigs.success,
}

const alert = ref({
    ...defaultAlertState,
})

const page = usePage()

const getAlertConfig = type => {
    return alertConfigs[type] || alertConfigs.success
}

const showAlert = (title, message, type = 'success', timeout = 10000) => {
    const config = getAlertConfig(type)

    alert.value = {
        visible: true,
        type,
        title,
        message,
        ...config,
    }

    setTimeout(() => {
        alert.value.visible = false
    }, timeout)
}

const closeAlert = () => {
    alert.value = { ...defaultAlertState }
}

if (typeof window !== 'undefined') {
    window.$showAlert = showAlert
    window.$closeAlert = closeAlert
}

watch(
    () => page.props.flash,
    newFlash => {
        if (!newFlash) return

        const errors = page.props.errors || {}
        if (Object.keys(errors).length > 0) {
            showAlert('Form Error', 'Please review the highlighted fields.', 'warning')
            return
        }

        for (const flashType of flashMessageTypes) {
            if (flashType.check(newFlash)) {
                const message =
                    typeof flashType.getMessage === 'function'
                        ? flashType.getMessage(newFlash)
                        : flashType.getMessage

                showAlert(flashType.title, message, flashType.type)
                return
            }
        }
    },
    {
        deep: true,
        immediate: true,
    }
)
</script>

<template>
    <div
        v-if="alert.visible"
        :id="`toast-${alert.type}`"
        role="alert"
        aria-live="polite"
        class="fixed top-4 right-4 z-80 flex items-center justify-center rounded-lg px-5 py-3 shadow-md"
        :class="[alert.bgColor, alert.textColor]">
        <div
            :class="`inline-flex h-8 w-8 flex-shrink-0 items-center justify-center ${alert.iconColor} rounded-lg`">
            <svg
                class="h-5 w-5"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20">
                <path :d="alert.iconPath" />
            </svg>
            <span class="sr-only">{{ alert.type }} icon</span>
        </div>

        <div class="ml-3 text-sm font-normal">
            <h2 class="block font-medium uppercase">{{ alert.title }}</h2>
            <p class="mt-1 text-[var(--color-text)]" v-html="alert.message"></p>
        </div>

        <button
            type="button"
            class="relative -top-4 -right-2 -mx-1.5 -my-1.5 ml-auto inline-flex h-6 w-6 items-center justify-center rounded-lg bg-[var(--color-surface)] p-1 text-[var(--color-text-muted)] hover:cursor-pointer hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)] focus:ring-2 focus:ring-[var(--color-border)]"
            :aria-label="`Close ${alert.type} message`"
            @click="closeAlert">
            <svg
                class="h-3 w-3"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 14 14">
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
</template>
