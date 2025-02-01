<template>
    <div class="min-h-full flex">
        <!-- Sidebar -->
        <NavSidebarDesktop data-sidebar :class="[
            'fixed left-0 top-16 w-64 h-screen transition-all duration-300',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-64'
        ]" @close="closeSidebar" />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="fixed w-full top-0 shadow-sm border-b border-gray-100 z-50">
                <div class="flex items-center h-16 px-4">
                    <!-- Logo & Menu -->
                    <div class="flex items-center gap-4">
                        <Link href="/" class="text-xl font-bold">Your Logo</Link>
                        <button type="button" class="rounded-full p-1" @click="toggleSidebar" data-menu-button>
                            <span class="sr-only">Toggle Menu</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1"></div>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <NavProfile v-if="user" :user="user" />
                        <Link v-else href="/login" class="flex items-center gap-2">Login</Link>
                    </div>
                </div>
            </header>

            <div class="bg-gray-50" :class="[
                'transition-all duration-300',
                isSidebarOpen ? 'md:ml-64' : 'md:ml-0'
            ]">
                <main class="mt-16 min-h-screen p-8">
                    <FlashMessage />
                    <slot></slot>
                </main>

                <footer class="bg-gradient-to-b from-gray-500 to-gray-600 py-6 mt-12">
                    <div class="container mx-auto px-4">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <!-- Social Links -->
                            <div class="flex items-center space-x-6">
                                <a href="#" class="text-white hover:text-indigo-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z" />
                                    </svg>
                                </a>
                                <a href="#" class="text-white hover:text-indigo-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                                    </svg>
                                </a>
                                <a href="#" class="text-white hover:text-indigo-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                </a>
                                <a href="#" class="text-white hover:text-indigo-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                    </svg>
                                </a>
                            </div>
                            <div class="text-xs text-white">© 2024 Ghana Maritime Authority</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Mobile Overlay -->
        <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="closeSidebar" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NavSidebarDesktop from '../Shared/NavSidebarDesktop.vue'
import NavProfile from '../Shared/NavProfile.vue'
import FlashMessage from '../Shared/FlashMessage.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(true)

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
    isSidebarOpen.value = true
    localStorage.setItem('sidebarOpen', 'true')
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
})

const handleClickAway = (event) => {
    const sidebar = document.querySelector('[data-sidebar]')
    const menuButton = document.querySelector('[data-menu-button]')

    if (sidebar?.contains(event.target) || menuButton?.contains(event.target)) return
    closeSidebar()
}
</script>
