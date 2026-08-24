<script setup>
import Button from '@/Components/Button.vue'
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { LogOutIcon } from '@lucide/vue'
const page = usePage()

const isImpersonating = computed(() => !!page.props.impersonation?.active)
const impersonatorName = computed(() => page.props.impersonation?.impersonator_name)
const currentUserName = computed(() => page.props.auth?.user?.name)

const stopImpersonation = () => {
    router.post(route('admin.user.impersonate.stop'))
}
</script>

<template>
    <div
        v-if="isImpersonating"
        class="fixed inset-x-0 bottom-0 z-45 border-t border-amber-300 bg-amber-50 px-4 py-2 dark:border-amber-800 dark:bg-amber-950/80">
        <div
            class="mx-auto flex max-w-7xl items-center justify-center gap-3 text-sm text-amber-800 dark:text-amber-300">
            <span>
                <span class="font-medium">{{ impersonatorName }}</span>
                viewing as
                <span class="font-medium">{{ currentUserName }}</span>
            </span>
            <Button
                size="xs"
                class="inline-flex items-center gap-1.5 rounded-md border border-amber-300 bg-white px-2.5 py-1 text-xs font-medium text-amber-700 transition-colors hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900/50 dark:text-amber-300 dark:hover:bg-amber-900"
                @click="stopImpersonation">
                <LogOutIcon class="h-3.5 w-3.5" />
                Exit
            </Button>
        </div>
    </div>
</template>
