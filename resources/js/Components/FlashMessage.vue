<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const alertConfigs = {
    success: {
        iconColor: 'text-green-600 bg-green-100 dark:text-green-300 dark:bg-green-900/20',
        bgColor: 'bg-green-100 dark:bg-green-900',
        textColor: 'text-green-700 dark:text-green-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z'
    },
    danger: {
        iconColor: 'text-red-600 bg-red-100 dark:text-red-300 dark:bg-red-900/20',
        bgColor: 'bg-red-100 dark:bg-red-900',
        textColor: 'text-red-700 dark:text-red-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z'
    },
    warning: {
        iconColor: 'text-orange-600 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/20',
        bgColor: 'bg-orange-100 dark:bg-orange-900',
        textColor: 'text-orange-700 dark:text-orange-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z'
    },
    info: {
        iconColor: 'text-blue-600 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/20',
        bgColor: 'bg-blue-100 dark:bg-blue-900',
        textColor: 'text-blue-700 dark:text-blue-300',
        iconPath:
            'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z'
    }
}

const flashMessageTypes = [
    {
        check: flash => flash.status === 'two-factor-authentication-enabled',
        title: 'Two-Factor Authentication',
        getMessage: () => 'Two-factor authentication has been enabled.',
        type: 'success'
    },
    {
        check: flash => flash.status === 'two-factor-authentication-disabled',
        title: 'Two-Factor Authentication',
        getMessage: () => 'Two-factor authentication has been disabled.',
        type: 'warning'
    },
    {
        check: flash => flash.status === 'recovery-codes-generated',
        title: 'Recovery Codes',
        getMessage: () => 'Recovery codes have been successfully generated.',
        type: 'info'
    },
    {
        check: flash => flash.status === 'verification-link-sent',
        title: 'Verification Link',
        getMessage: () => 'A new email verification link has been emailed to you!',
        type: 'success'
    },
    {
        check: flash => flash.status === 'profile-information-updated',
        title: 'Profile Updated',
        getMessage: () => 'Your profile information has been updated.',
        type: 'success'
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
                    'profile-information-updated'
                ].includes(flash.status)),
        title: 'Success',
        getMessage: flash => flash.success || flash.message || flash.status,
        type: 'success'
    },
    {
        check: flash => flash.warning,
        title: 'Warning',
        getMessage: flash => flash.warning,
        type: 'warning'
    },
    {
        check: flash => flash.info,
        title: 'Info',
        getMessage: flash => flash.info,
        type: 'info'
    },
    {
        check: flash => flash.error || flash.danger,
        title: 'Error',
        getMessage: flash => flash.error || flash.danger,
        type: 'danger'
    }
]

const alert = ref({
    visible: false,
    type: 'success',
    title: '',
    message: '',
    ...alertConfigs.success
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
        ...config
    }

    setTimeout(() => {
        alert.value.visible = false
    }, timeout)
}

const closeAlert = () => {
    alert.value.visible = false
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
        immediate: true
    }
)
</script>

<template>
    <div
        v-if="alert.visible"
        :id="`toast-${alert.type}`"
        role="alert"
        aria-live="polite"
        class="fixed z-[60] flex items-center justify-center px-5 py-3 rounded-lg top-4 right-4 shadow-md"
        :class="[alert.bgColor, alert.textColor]">
        <div
            :class="`inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${alert.iconColor} rounded-lg`">
            <svg
                class="w-5 h-5"
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
            <p class="text-[var(--color-text)] mt-1" v-html="alert.message"></p>
        </div>

        <button
            type="button"
            class="relative -top-4 -right-2 ml-auto -mx-1.5 -my-1.5 bg-[var(--color-surface)] text-[var(--color-text-muted)] hover:text-[var(--color-text)] rounded-lg focus:ring-2 focus:ring-[var(--color-border)] p-1 hover:bg-[var(--color-surface-muted)] hover:cursor-pointer inline-flex items-center justify-center h-6 w-6"
            :aria-label="`Close ${alert.type} message`"
            @click="closeAlert">
            <svg
                class="w-3 h-3"
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
