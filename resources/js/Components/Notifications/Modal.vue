<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    show: Boolean,
    size: { type: String, default: 'md' },
    closeOnClickOutside: { type: Boolean, default: true },
})

const emit = defineEmits(['close'])
const modalPanel = ref(null)
const closeButton = ref(null)
const previouslyFocused = ref(null)
const modalId = `modal-${Math.random().toString(36).substr(2, 9)}`

const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
}

const handleKeyDown = e => {
    if (e.key === 'Escape' && props.show) emit('close')
    if (e.key === 'Tab' && props.show && modalPanel.value) {
        const focusable = modalPanel.value.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        )
        if (!focusable.length) return
        const first = focusable[0]
        const last = focusable[focusable.length - 1]
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus() }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus() }
    }
}

const handleClickOutside = e => {
    if (props.closeOnClickOutside && !modalPanel.value?.contains(e.target)) emit('close')
}

watch(() => props.show, v => {
    if (v) { previouslyFocused.value = document.activeElement; nextTick(() => closeButton.value?.focus()) }
    else if (previouslyFocused.value) previouslyFocused.value.focus()
}, { immediate: true })

onMounted(() => document.addEventListener('keydown', handleKeyDown))
onUnmounted(() => document.removeEventListener('keydown', handleKeyDown))
</script>

<template>
    <Transition name="modal" :duration="150">
        <div v-if="show" class="fixed inset-0 z-[999]">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/25 dark:bg-black/50" @click="handleClickOutside"></div>

            <!-- Dialog -->
            <div class="fixed inset-0 z-10 grid place-items-center p-4" role="dialog" aria-modal="true" :aria-labelledby="modalId">
                <div
                    ref="modalPanel"
                    tabindex="-1"
                    class="relative flex w-full flex-col rounded-lg border border-(--card-border) bg-(--color-surface) shadow-lg"
                    :class="[sizeClasses[size] || sizeClasses.md, 'max-h-[calc(100vh-2rem)]']">

                    <!-- Header -->
                    <div class="flex shrink-0 items-center justify-between border-b border-(--card-border) px-5 py-3.5">
                        <h2 :id="modalId" class="text-sm font-semibold text-(--color-text)">
                            <slot name="title" />
                        </h2>
                        <button
                            ref="closeButton"
                            class="nav-bar-btn"
                            aria-label="Close"
                            @click="emit('close')">
                            <XMarkIcon class="nav-bar-icon" />
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="min-h-0 flex-1 overflow-y-visible px-5 py-4">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div v-if="$slots.footer" class="shrink-0 border-t border-(--card-border) px-5 py-3.5">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 150ms ease-out;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
