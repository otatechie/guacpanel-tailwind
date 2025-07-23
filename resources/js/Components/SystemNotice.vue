<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
    notices: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['heightChange'])

const { systemNotices } = usePage().props
const allNotices = computed(() => props.notices.length > 0 ? props.notices : systemNotices || [])
const dismissedNotices = ref(new Set())
const noticeContainer = ref(null)

const isNoticeVisible = (notice) => {
    const now = new Date()

    // Check if notice is active
    if (!notice.is_active) return false

    // Check if notice has been dismissed
    if (dismissedNotices.value.has(notice.id)) return false

    // Check visible_from date
    if (notice.visible_from) {
        const visibleFrom = new Date(notice.visible_from)
        if (now < visibleFrom) return false
    }

    // Check expires_at date
    if (notice.expires_at) {
        const expiresAt = new Date(notice.expires_at)
        if (now > expiresAt) return false
    }

    return true
}

const visibleNotices = computed(() => {
    return allNotices.value.filter(isNoticeVisible)
})

const dismissNotice = (noticeId) => {
    dismissedNotices.value.add(noticeId)
    nextTick(() => {
        emit('heightChange', noticeContainer.value?.offsetHeight || 0)
    })
}

watch(() => visibleNotices.value.length, (newCount) => {
    nextTick(() => {
        emit('heightChange', noticeContainer.value?.offsetHeight || 0)
    })
}, { immediate: true })

const handleKeyDown = (event, noticeId) => {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault()
        dismissNotice(noticeId)
    }
}

const getTypeStyles = (type) => {
    const styles = {
        info: {
            bg: 'bg-blue-50 border border-blue-200 dark:bg-blue-900/10 dark:border-blue-800/20',
            text: 'text-blue-800 dark:text-blue-100',
            icon: 'text-blue-500 dark:text-blue-400'
        },
        success: {
            bg: 'bg-green-50 border border-green-200 dark:bg-green-900/10 dark:border-green-800/20',
            text: 'text-green-800 dark:text-green-100',
            icon: 'text-green-500 dark:text-green-400'
        },
        warning: {
            bg: 'bg-yellow-50 border border-yellow-200 dark:bg-yellow-900/10 dark:border-yellow-800/20',
            text: 'text-yellow-800 dark:text-yellow-100',
            icon: 'text-yellow-500 dark:text-yellow-400'
        },
        error: {
            bg: 'bg-red-50 border border-red-200 dark:bg-red-900/10 dark:border-red-800/20',
            text: 'text-red-800 dark:text-red-100',
            icon: 'text-red-500 dark:text-red-400'
        }
    }
    return styles[type] || styles.info
}

const getTypeIcon = (type) => {
    const icons = {
        info: 'M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm.25,5a1.5,1.5,0,1,1-1.5,1.5A1.5,1.5,0,0,1,12.25,5ZM14.5,18.5h-4a1,1,0,0,1,0-2h.75a.25.25,0,0,0,.25-.25v-4.5a.25.25,0,0,0-.25-.25H10.5a1,1,0,0,1,0-2h1a2,2,0,0,1,2,2v4.75a.25.25,0,0,0,.25.25h.75a1,1,0,1,1,0,2Z',
        success: 'M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z',
        warning: 'M23.119,20,13.772,2.15h0a2,2,0,0,0-3.543,0L.881,20a2,2,0,0,0,1.772,2.928H21.347A2,2,0,0,0,23.119,20ZM11,8.423a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Zm1.05,11.51h-.028a1.528,1.528,0,0,1-1.522-1.47,1.476,1.476,0,0,1,1.448-1.53h.028A1.527,1.527,0,0,1,13.5,18.4,1.475,1.475,0,0,1,12.05,19.933Z',
        error: 'M11.983,0a12.206,12.206,0,0,0-8.51,3.653A11.8,11.8,0,0,0,0,12.207,11.779,11.779,0,0,0,11.8,24h.214A12.111,12.111,0,0,0,24,11.791h0A11.766,11.766,0,0,0,11.983,0ZM10.5,16.542a1.476,1.476,0,0,1,1.449-1.53h.027a1.527,1.527,0,0,1,1.523,1.47,1.475,1.475,0,0,1-1.449,1.53h-.027A1.529,1.529,0,0,1,10.5,16.542ZM11,12.5v-6a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Z'
    }
    return icons[type] || icons.info
}

const getTypeLabel = (type) => {
    const labels = {
        info: 'Information',
        success: 'Success',
        warning: 'Warning',
        error: 'Error'
    }
    return labels[type] || 'Information'
}
</script>

<template>
    <div v-if="visibleNotices.length > 0" ref="noticeContainer" class="w-full space-y-3 mb-0.5"
        aria-label="System Notices">
        <div v-for="notice in visibleNotices" :key="notice.id" :class="[
            'px-4 py-3 flex items-start w-full relative shadow-sm',
            'transition duration-200 ease-in-out hover:shadow-md',
            getTypeStyles(notice.type).bg
        ]" role="alert" :aria-label="`${getTypeLabel(notice.type)} notice: ${notice.title}`">
            <!-- Icon with background -->
            <div :class="[
                'flex-shrink-0 rounded-full p-1.5 bg-white/80 dark:bg-gray-900/50',
                'border border-current/10'
            ]">
                <svg viewBox="0 0 24 24" :class="['w-4 h-4', getTypeStyles(notice.type).icon]"
                    :aria-label="`${getTypeLabel(notice.type)} icon`" role="img">
                    <path fill="currentColor" :d="getTypeIcon(notice.type)" />
                </svg>
            </div>

            <!-- Content with better spacing -->
            <div class="flex-1 min-w-0 ml-3">
                <h3 class="font-medium leading-5" :class="getTypeStyles(notice.type).text">
                    {{ notice.title }}
                </h3>
                <p class="text-xs mt-1 leading-relaxed opacity-90" :class="getTypeStyles(notice.type).text">
                    {{ notice.content }}
                </p>
            </div>

            <!-- Dismiss button with better hover effect -->
            <button v-if="notice.is_dismissible" @click="dismissNotice(notice.id)"
                @keydown="handleKeyDown($event, notice.id)"
                class="ml-4 p-1.5 rounded-full transition-colors duration-200 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-offset-1 cursor-pointer"
                :class="[getTypeStyles(notice.type).icon]"
                :aria-label="`Dismiss ${getTypeLabel(notice.type).toLowerCase()} notice: ${notice.title}`" tabindex="0"
                type="button">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>