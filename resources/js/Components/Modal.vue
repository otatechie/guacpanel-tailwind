<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'

const props = defineProps({
    show: Boolean,
    size: { type: String, default: 'md' },
    closeOnClickOutside: { type: Boolean, default: true }
})

const emit = defineEmits(['close'])
const modalPanel = ref(null)
const closeButton = ref(null)
const previouslyFocused = ref(null)
const modalId = `modal-${Math.random().toString(36).substr(2, 9)}`
const sizeClasses = {
    sm: 'w-full max-w-sm mx-2 sm:mx-4',
    md: 'w-full max-w-md mx-2 sm:mx-4',
    lg: 'w-full max-w-lg mx-2 sm:mx-4',
    xl: 'w-full max-w-xl mx-2 sm:mx-4',
    '2xl': 'w-full max-w-2xl mx-2 sm:mx-4',
    '3xl': 'w-full max-w-3xl mx-2 sm:mx-4',
    '4xl': 'w-full max-w-4xl mx-2 sm:mx-4',
    '5xl': 'w-full max-w-5xl mx-2 sm:mx-4',
    full: 'w-full max-w-full mx-1 sm:mx-2'
}

const handleKeyDown = e => {
    if (e.key === 'Escape' && props.show) emit('close')

    if (e.key === 'Tab' && props.show && modalPanel.value) {
        const focusableElements = modalPanel.value.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        )
        if (focusableElements.length === 0) return

        const firstElement = focusableElements[0]
        const lastElement = focusableElements[focusableElements.length - 1]

        if (e.shiftKey && document.activeElement === firstElement) {
            e.preventDefault()
            lastElement.focus()
        } else if (!e.shiftKey && document.activeElement === lastElement) {
            e.preventDefault()
            firstElement.focus()
        }
    }
}

const handleClickOutside = e => {
    if (props.closeOnClickOutside && !modalPanel.value?.contains(e.target)) emit('close')
}

watch(
    () => props.show,
    newValue => {
        if (newValue) {
            previouslyFocused.value = document.activeElement
            nextTick(() => closeButton.value?.focus())
        } else if (previouslyFocused.value) {
            previouslyFocused.value.focus()
        }
    },
    { immediate: true }
)

onMounted(() => document.addEventListener('keydown', handleKeyDown))
onUnmounted(() => document.removeEventListener('keydown', handleKeyDown))
</script>

<template>
    <Transition name="modal" :duration="150">
        <div v-if="show" class="fixed inset-0 z-[999]" role="region" aria-labelledby="modalId">
            <div
                class="fixed inset-0 grid h-screen w-screen place-items-center bg-black/30 dark:bg-black/50"
                aria-hidden="true"
                @click="handleClickOutside"></div>

            <main
                class="fixed inset-0 z-10 grid h-screen w-screen place-items-center p-2 sm:p-4 md:p-6"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="modalId">
                <article
                    ref="modalPanel"
                    tabindex="-1"
                    class="relative w-full rounded-xl bg-white dark:bg-gray-800 shadow-2xl flex flex-col max-h-[calc(100vh-1rem)] sm:max-h-[calc(100vh-2rem)] md:max-h-[calc(100vh-3rem)] ring-1 ring-black/5 dark:ring-white/5 overflow-visible"
                    :class="sizeClasses[size] || sizeClasses['md']">
                    <header
                        class="flex items-center justify-between px-3 sm:px-4 md:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-gray-700/50 flex-shrink-0">
                        <h2
                            :id="modalId"
                            class="text-sm sm:text-base md:text-lg font-semibold text-gray-900 dark:text-white pr-2">
                            <slot name="title"></slot>
                        </h2>
                        <button
                            ref="closeButton"
                            class="rounded-lg p-1.5 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:text-gray-500 dark:hover:text-gray-400 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 cursor-pointer flex-shrink-0"
                            aria-label="Close modal"
                            @click="emit('close')">
                            <svg
                                class="h-4 w-4 sm:h-5 sm:w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </header>

                    <section
                        class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 dark:text-gray-200 relative flex-1 min-h-0">
                        <slot></slot>
                    </section>

                    <footer
                        v-if="$slots.footer"
                        class="flex justify-end gap-8 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/50 px-3 sm:px-4 md:px-6 py-3 sm:py-4 rounded-b-xl flex-shrink-0">
                        <slot name="footer"></slot>
                    </footer>
                </article>
            </main>
        </div>
    </Transition>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: all 0.15s ease-out;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
