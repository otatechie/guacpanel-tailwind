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
const sizeClasses = { 'sm': 'max-w-sm', 'md': 'max-w-md', 'lg': 'max-w-lg', 'xl': 'max-w-xl' }

const handleKeyDown = (e) => {
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

const handleClickOutside = (e) => {
    if (props.closeOnClickOutside && !modalPanel.value?.contains(e.target)) emit('close')
}

watch(() => props.show, (newValue) => {
    if (newValue) {
        previouslyFocused.value = document.activeElement
        nextTick(() => closeButton.value?.focus())
    } else if (previouslyFocused.value) {
        previouslyFocused.value.focus()
    }
}, { immediate: true })

onMounted(() => document.addEventListener('keydown', handleKeyDown))
onUnmounted(() => document.removeEventListener('keydown', handleKeyDown))
</script>


<template>
    <Transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[999]" role="region" aria-labelledby="modalId">
            <div class="fixed inset-0 grid h-screen w-screen place-items-center bg-black/30 backdrop-blur-[1px] dark:bg-black/50"
                @click="handleClickOutside" aria-hidden="true"></div>

            <main class="fixed inset-0 z-10 grid h-screen w-screen place-items-center p-4" role="dialog"
                aria-modal="true" :aria-labelledby="modalId">
                <article ref="modalPanel" tabindex="-1"
                    class="relative w-full rounded-xl bg-white dark:bg-gray-800 shadow-2xl"
                    :class="sizeClasses[size] || sizeClasses['md']">
                    <header
                        class="flex items-center justify-between p-6 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <h2 :id="modalId" class="text-xl font-semibold text-gray-800 dark:text-white">
                            <slot name="title"></slot>
                        </h2>
                        <button ref="closeButton" @click="emit('close')"
                            class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:text-gray-500 dark:hover:text-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 cursor-pointer"
                            aria-label="Close modal">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </header>

                    <section class="px-6 py-6 dark:text-gray-200">
                        <slot></slot>
                    </section>

                    <footer v-if="$slots.footer"
                        class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-6 py-4 rounded-b-xl">
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
    transition: opacity 0.15s linear;
}

.modal-enter-active .fixed,
.modal-leave-active .fixed {
    transition: opacity 0.15s linear;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease-out;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: translate(0, -50px);
}

.modal-enter-to,
.modal-leave-from {
    opacity: 1;
}

.modal-enter-to .relative,
.modal-leave-from .relative {
    transform: translate(0, 0);
}
</style>
