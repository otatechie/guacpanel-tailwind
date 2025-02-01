// Components/Modal.vue
<template>
    <Transition name="modal">
        <div v-if="show" class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity duration-300"></div>

            <!-- Modal -->
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative w-full max-w-sm overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-gray-900/5">
                        <!-- Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <slot name="title"></slot>
                                </h3>
                                <button @click="$emit('close')"
                                    class="p-1 -m-1 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 transition-colors"
                                    aria-label="Close">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="">
                            <slot></slot>
                        </div>

                        <!-- Footer -->
                        <div v-if="$slots.footer" class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                            <slot name="footer"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
defineProps({
    show: Boolean
})

defineEmits(['close'])
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: translateY(-4px) scale(0.95);
}
</style>
