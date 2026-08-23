<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import {
    UserCircleIcon,
    Cog6ToothIcon,
    ArrowLeftStartOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const userName = computed(() => user.value?.name || '')
const primaryRole = computed(() => user.value?.roles?.[0] || '')

const menuOpen = ref(false)
const menuWrapper = ref(null)

const hasPermission = perm => user.value?.permissions?.includes(perm) ?? false

const toggleMenu = () => (menuOpen.value = !menuOpen.value)
const closeMenu = () => (menuOpen.value = false)

const handleClickOutside = e => {
    if (menuWrapper.value && !menuWrapper.value.contains(e.target)) closeMenu()
}
const handleEscape = e => {
    if (e.key === 'Escape') closeMenu()
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleEscape)
})
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleEscape)
})
</script>

<template>
    <nav ref="menuWrapper" class="relative">
        <button
            type="button"
            class="flex cursor-pointer items-center gap-1 rounded-lg p-1 transition-colors hover:bg-muted"
            :aria-expanded="menuOpen"
            @click="toggleMenu">
            <img
                :src="avatarUrl"
                :alt="userName"
                class="size-6 rounded-full" />
            <svg class="hidden h-3 w-3 text-muted-foreground lg:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95">
            <div
                v-if="menuOpen"
                class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg border border-border bg-card py-1 shadow-lg"
                role="menu">

                <!-- User info -->
                <div class="border-b border-border px-3 py-2.5">
                    <p class="text-sm font-medium capitalize text-foreground">{{ userName }}</p>
                    <p v-if="primaryRole" class="mt-0.5 font-mono text-[11px] capitalize text-muted-foreground">{{ primaryRole }}</p>
                </div>

                <!-- Links -->
                <div class="py-1">
                    <Link
                        :href="route('user.index')"
                        class="flex items-center gap-2.5 px-3 py-2 text-sm text-foreground transition-colors hover:bg-muted"
                        role="menuitem"
                        @click="closeMenu">
                        <UserCircleIcon class="h-4 w-4 text-muted-foreground" />
                        Account
                    </Link>

                    <Link
                        v-if="hasPermission('manage-settings')"
                        :href="route('admin.setting.index')"
                        class="flex items-center gap-2.5 px-3 py-2 text-sm text-foreground transition-colors hover:bg-muted"
                        role="menuitem"
                        @click="closeMenu">
                        <Cog6ToothIcon class="h-4 w-4 text-muted-foreground" />
                        Settings
                    </Link>
                </div>

                <!-- Sign out -->
                <div class="border-t border-border py-1">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex w-full items-center gap-2.5 px-3 py-2 text-sm text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                        role="menuitem"
                        @click="closeMenu">
                        <ArrowLeftStartOnRectangleIcon class="h-4 w-4" />
                        Sign out
                    </Link>
                </div>
            </div>
        </Transition>
    </nav>
</template>
