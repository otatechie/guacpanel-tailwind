<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isVisible = ref(false)
const isDismissed = ref(false)

const isMobile = () => {
    return (
        window.innerWidth < 768 ||
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    )
}

const isInAppBrowser = () => {
    return /FBAN|FBAV|Instagram|Twitter|LinkedInApp|WhatsApp|Telegram|Line|WeChat|Snapchat/i.test(
        navigator.userAgent
    )
}

const dismissNotification = () => {
    isVisible.value = false
    isDismissed.value = true
    localStorage.setItem('mobile-notification-dismissed', 'true')
}

const checkShouldShow = () => {
    if (localStorage.getItem('mobile-notification-dismissed') === 'true') {
        isDismissed.value = true
        return
    }

    if (isMobile() && !isInAppBrowser()) {
        isVisible.value = true
    }
}

const handleResize = () => {
    if (window.innerWidth >= 768) {
        isVisible.value = false
    } else if (!isDismissed.value && isMobile() && !isInAppBrowser()) {
        isVisible.value = true
    }
}

onMounted(() => {
    checkShouldShow()
    window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="transform -translate-y-full opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-full opacity-0">
        <div
            v-if="isVisible"
            class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg"
            role="banner"
            aria-live="polite">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center space-x-3">
                    <!-- Desktop Icon -->
                    <div class="flex-shrink-0 p-1.5 bg-blue-500/20 rounded-lg">
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                        </svg>
                    </div>

                    <!-- Message -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold">
                            For the best experience, open on a desktop using Chrome, Safari, or
                            Firefox.
                        </p>
                    </div>
                </div>

                <!-- Close Button -->
                <button
                    type="button"
                    class="flex-shrink-0 ml-3 p-1.5 rounded-lg hover:bg-blue-500/20 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:ring-offset-blue-600 transition-colors"
                    aria-label="Dismiss notification"
                    @click="dismissNotification">
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Ensure the notification appears above other content */
.fixed {
    z-index: 9999;
}
</style>
