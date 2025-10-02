<script setup>
import { computed, ref, nextTick } from 'vue'

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: value => ['info', 'warning', 'success', 'error', 'danger'].includes(value)
    },
    title: {
        type: String,
        default: ''
    },
    dismissible: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'md',
        validator: value => ['sm', 'md', 'lg'].includes(value)
    }
})

const emit = defineEmits(['dismiss'])

const isVisible = ref(true)
const isDismissing = ref(false)

const alertConfig = computed(() => {
    const configs = {
        info: {
            container: 'bg-blue-50/70 dark:bg-blue-900/20 border-blue-300/80 dark:border-blue-700',
            text: 'text-blue-700 dark:text-blue-300',
            icon: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
            iconColor: 'text-blue-600 dark:text-blue-300'
        },
        warning: {
            container: 'bg-amber-50/70 dark:bg-amber-900/20 border-amber-300/80 dark:border-amber-700',
            text: 'text-amber-700 dark:text-amber-300',
            icon: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
            iconColor: 'text-amber-600 dark:text-amber-300'
        },
        success: {
            container: 'bg-green-50/70 dark:bg-green-900/20 border-green-300/80 dark:border-green-700',
            text: 'text-green-700 dark:text-green-300',
            icon: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
            iconColor: 'text-green-600 dark:text-green-300'
        },
        error: {
            container: 'bg-red-50/70 dark:bg-red-900/20 border-red-300/80 dark:border-red-700',
            text: 'text-red-700 dark:text-red-300',
            icon: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
            iconColor: 'text-red-600 dark:text-red-300'
        },
        danger: {
            container: 'bg-red-50/70 dark:bg-red-900/20 border-red-300/80 dark:border-red-700',
            text: 'text-red-700 dark:text-red-300',
            icon: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
            iconColor: 'text-red-600 dark:text-red-300'
        }
    }

    return configs[props.type] || configs.info
})

const sizeConfig = computed(() => {
    const configs = {
        sm: {
            container: 'p-3 text-xs',
            icon: 'w-4 h-4',
            title: 'text-xs',
            content: 'text-xs'
        },
        md: {
            container: 'p-4 text-sm',
            icon: 'w-5 h-5',
            title: 'text-sm',
            content: 'text-sm'
        },
        lg: {
            container: 'p-5 text-base',
            icon: 'w-6 h-6',
            title: 'text-base',
            content: 'text-base'
        }
    }

    return configs[props.size] || configs.md
})

const handleDismiss = async () => {
    isDismissing.value = true
    emit('dismiss')

    await nextTick()
    setTimeout(() => {
        isVisible.value = false
    }, 150)
}
</script>

<template>
    <div
        v-if="isVisible"
        :class="[
            'border rounded-lg transition-all duration-150 ease-in-out',
            isDismissing ? 'opacity-0 scale-95' : 'opacity-100 scale-100',
            sizeConfig.container,
            alertConfig.container
        ]">
        <div class="flex items-start gap-3">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <svg
                    :class="[sizeConfig.icon, alertConfig.iconColor]"
                    fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd" :d="alertConfig.icon" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <h3 v-if="title" :class="[sizeConfig.title, 'font-medium mb-1', alertConfig.text]">
                    {{ title }}
                </h3>
                <p :class="[sizeConfig.content, alertConfig.text]">
                    <slot></slot>
                </p>
            </div>

            <!-- Dismiss button -->
            <button
                v-if="dismissible"
                class="flex-shrink-0 ml-2 p-1 rounded-md hover:bg-black/5 dark:hover:bg-white/5 transition-colors cursor-pointer"
                :class="alertConfig.text"
                type="button"
                aria-label="Dismiss alert"
                @click="handleDismiss">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>
