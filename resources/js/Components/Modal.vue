<template>
    <Transition name="modal">
        <div v-if="show" class="fixed inset-0 z-[999]">
            <div class="fixed inset-0 grid h-screen w-screen place-items-center bg-black bg-opacity-30 backdrop-blur-[1px]"
                @click="$emit('close')" aria-hidden="true"></div>
            <div class="fixed inset-0 z-10 grid h-screen w-screen place-items-center p-4" @keydown.esc="$emit('close')"
                role="dialog" aria-modal="true" :aria-labelledby="titleId">
                <div class="relative w-full rounded-xl bg-white shadow-2xl" :class="{
                    'max-w-sm': size === 'sm',
                    'max-w-md': size === 'md',
                    'max-w-lg': size === 'lg',
                    'max-w-xl': size === 'xl',
                    'max-w-2xl': size === '2xl',
                }" ref="modalPanel" tabindex="-1">
                    <div class="flex items-center justify-between p-6 pb-2 border-b border-gray-200">
                        <h3 :id="titleId" class="text-xl font-semibold text-gray-800">
                            <slot name="title"></slot>
                        </h3>
                        <button @click="$emit('close')"
                            class="rounded-lg p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            aria-label="Close modal">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6">
                        <slot></slot>
                    </div>

                    <div v-if="$slots.footer"
                        class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 rounded-b-xl">
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    show: Boolean,
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl'].includes(value)
    }
})

const emit = defineEmits(['close'])
const modalPanel = ref(null)
const titleId = `modal-${Math.random().toString(36).substr(2, 9)}`

const handleKeyDown = (e) => {
    if (e.key === 'Escape' && props.show) {
        emit('close')
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeyDown)
    if (props.show) {
        modalPanel.value?.focus()
    }
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyDown)
})
</script>

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
