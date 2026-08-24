<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { MonitorIcon, XIcon } from '@lucide/vue'

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
            class="fixed top-0 right-0 left-0 z-30 bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg"
            role="banner"
            aria-live="polite">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center space-x-3">
                    <!-- Desktop Icon -->
                    <div class="flex-shrink-0 rounded-lg bg-blue-500/20 p-1.5">
                        <MonitorIcon class="h-5 w-5" aria-hidden="true" />
                    </div>

                    <!-- Message -->
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">
                            For the best experience, open on a desktop using Chrome, Safari, or
                            Firefox.
                        </p>
                    </div>
                </div>

                <!-- Close Button -->
                <button
                    type="button"
                    class="ml-3 flex-shrink-0 rounded-lg p-1.5 transition-colors hover:bg-blue-500/20 focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:ring-offset-blue-600 focus:outline-none"
                    aria-label="Dismiss notification"
                    @click="dismissNotification">
                    <XIcon class="h-4 w-4" aria-hidden="true" />
                </button>
            </div>
        </div>
    </Transition>
</template>
