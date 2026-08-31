<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Alert from './Alert.vue'

defineProps({
    modelValue: {
        type: Number,
        default: 0,
    },
})

const emit = defineEmits(['update:modelValue'])

const page = usePage()
const bannerRef = ref(null)
const systemNotifications = computed(() => page.props.systemNotifications || [])

const STORAGE_KEY = 'dismissedSystemNotifications'

/* Capped, and read defensively. The stored list only ever grew -- one id per
   dismissal, with nothing that could ever drop one -- and a hand-edited or
   truncated value threw during setup, taking the whole layout down with it. */
const DISMISS_LIMIT = 100

const readDismissed = () => {
    try {
        const stored = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
        return Array.isArray(stored) ? stored : []
    } catch {
        return []
    }
}

const dismissedOrder = ref(readDismissed())
const dismissedIds = computed(() => new Set(dismissedOrder.value))

/* Loudest first. `danger` is Alert's alias for `error`, and an unknown type
   sorts with `info`, matching the icon Alert falls back to. */
const SEVERITY = { error: 0, danger: 0, warning: 1, info: 2, success: 3 }
const severityOf = type => SEVERITY[type] ?? SEVERITY.info

/* One at a time. Every notice used to render its own full-width band, and each
   band pushed the header, sidebar and page content further down -- two notices
   already cost 172px, and nothing capped the stack. Dismissing the top one
   reveals the next, and all of them stay in the notifications feed regardless. */
const pending = computed(() =>
    systemNotifications.value
        .filter(notification => !dismissedIds.value.has(notification.id))
        .map((notification, order) => ({ notification, order }))
        .sort(
            (a, b) =>
                severityOf(a.notification.type) - severityOf(b.notification.type) ||
                a.order - b.order
        )
        .map(entry => entry.notification)
)

const current = computed(() => pending.value[0] ?? null)
const queued = computed(() => Math.max(pending.value.length - 1, 0))

const dismiss = id => {
    dismissedOrder.value = [...dismissedOrder.value, id].slice(-DISMISS_LIMIT)

    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dismissedOrder.value))
    } catch {
        /* Private mode or a full quota: the banner still goes for this page view. */
    }
}

const measure = () => emit('update:modelValue', bannerRef.value?.offsetHeight || 0)

const updateHeight = async () => {
    await nextTick()
    measure()
}

/* Measured only when the list changed, the height went stale on every rewrap:
   narrowing the window grew the banner without moving the header, and the
   banner (z-45) then covered it -- search box and all. */
let observer = null

watch(current, updateHeight, { immediate: true })

watch(bannerRef, element => {
    observer?.disconnect()
    if (element) observer?.observe(element)
})

onMounted(() => {
    if (typeof ResizeObserver !== 'undefined') {
        observer = new ResizeObserver(measure)
        if (bannerRef.value) observer.observe(bannerRef.value)
    }

    updateHeight()
})

onBeforeUnmount(() => observer?.disconnect())
</script>

<template>
    <div
        v-if="current"
        ref="bannerRef"
        class="fixed top-0 right-0 left-0 z-45 w-full"
        role="region"
        aria-label="System notices">
        <!-- Keyed so dismissing one gets a fresh Alert for the next: Alert keeps
             its own `isVisible`, and a reused instance would stay hidden. -->
        <Alert
            :key="current.id"
            :type="current.type"
            :dismissible="true"
            class="rounded-none border-x-0 border-t-0 shadow-sm"
            @dismiss="dismiss(current.id)">
            <span class="font-medium">{{ current.title }}</span>
            <!-- Inline, not stacked: a title over a message spent two lines and
                 86px on two words, with the rest of the bar empty. -->
            <span v-if="current.message">— {{ current.message }}</span>
            <span v-if="queued" class="opacity-70">· {{ queued }} more</span>
        </Alert>
    </div>
</template>
