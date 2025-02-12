<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NavSidebarDesktop from '../Shared/NavSidebarDesktop.vue'
import NavProfile from '../Shared/NavProfile.vue'
import Notification from '../Shared/Notification.vue'
import FlashMessage from '../Shared/FlashMessage.vue'
import Footer from '../Shared/Public/Footer.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(false)

const handleClickAway = (event) => {
    const sidebar = document.querySelector('[data-sidebar]')
    const menuButton = document.querySelector('[data-menu-button]')
    const sidebarContent = document.querySelector('[data-sidebar-content]')

    if (sidebar?.contains(event.target) ||
        menuButton?.contains(event.target) ||
        sidebarContent?.contains(event.target)) {
        return
    }

    if (isMobile()) {
        closeSidebar()
    }
}

const isMobile = () => window.innerWidth < 768

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    const savedState = localStorage.getItem('sidebarOpen')
    isSidebarOpen.value = savedState ? savedState === 'true' : !isMobile()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 bg-black/30 z-30" @click.stop="closeSidebar"
            aria-hidden="true"></div>

        <NavSidebarDesktop data-sidebar
            class="fixed left-0 top-16 w-64 h-[calc(100vh-4rem)] transition-transform duration-200 z-40" :class="[
                isSidebarOpen ? 'translate-x-0' : '-translate-x-64'
            ]" @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <header class="fixed w-full top-0 bg-white dark:bg-gray-800 border-b border-gray-100 z-40">
                <div class="flex h-16 items-center px-4 gap-4">
                    <div class="flex items-center gap-4">
                        <Link href="/" class="text-xl font-semibold text-gray-900">
                        Your Logo
                        </Link>
                        <button type="button"
                            class="rounded-lg p-2 text-gray-500 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            @click="toggleSidebar" data-menu-button aria-label="Toggle Menu">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-1 items-center justify-end gap-4">
                        <Notification v-if="user" :user="user" />
                        <NavProfile v-if="user" :user="user" />
                        <Link v-else href="/login" class="text-sm font-medium text-gray-700 hover:text-gray-900">
                        Login
                        </Link>
                    </div>
                </div>
            </header>

            <main class="flex-1" :class="[
                'pt-16 px-4 sm:px-6 lg:px-8',
                isSidebarOpen ? 'md:ml-64' : 'md:ml-0'
            ]">
                <FlashMessage />
                <div class="py-8">
                    <slot />
                </div>
            </main>

            <Footer />
        </div>
    </div>
</template>
