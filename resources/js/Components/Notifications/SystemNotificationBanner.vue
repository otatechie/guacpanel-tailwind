<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Alert from './Alert.vue'

const props = defineProps({
    modelValue: {
        type: Number,
        default: 0,
    },
})

const emit = defineEmits(['update:modelValue'])

const page = usePage()
const bannerRef = ref(null)
const systemNotifications = computed(() => page.props.systemNotifications || [])
const dismissedIds = ref(
    new Set(JSON.parse(localStorage.getItem('dismissedSystemNotifications') || '[]'))
)

const visibleNotifications = computed(() =>
    systemNotifications.value.filter(n => !dismissedIds.value.has(n.id))
)

const dismiss = id => {
    dismissedIds.value.add(id)
    localStorage.setItem('dismissedSystemNotifications', JSON.stringify([...dismissedIds.value]))
    updateHeight()
}

const updateHeight = async () => {
    await nextTick()
    const height = bannerRef.value?.offsetHeight || 0
    emit('update:modelValue', height)
}

watch(visibleNotifications, updateHeight, { immediate: true })

onMounted(() => {
    updateHeight()
})
</script>

<template>
    <div
        ref="bannerRef"
        v-if="visibleNotifications.length > 0"
        class="fixed top-0 right-0 left-0 z-70 w-full">
        <Alert
            v-for="notification in visibleNotifications"
            :key="notification.id"
            :type="notification.type"
            :title="notification.title"
            :dismissible="true"
            class="rounded-none border-x-0 border-t-0 shadow-sm"
            @dismiss="dismiss(notification.id)">
            {{ notification.message }}
        </Alert>
    </div>
</template>
