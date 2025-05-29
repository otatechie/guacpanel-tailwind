<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NavSidebarDesktop from '../Components/NavSidebarDesktop.vue'
import NavProfile from '../Components/NavProfile.vue'
import Notification from '../Components/Notification.vue'
import FlashMessage from '../Components/FlashMessage.vue'
import Footer from '../Shared/Public/Footer.vue'
import ColorThemeSwitcher from '../Components/ColorThemeSwitcher.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(false)
const isMobileSearchOpen = ref(false)
const isMobile = () => window.innerWidth < 768

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

const handleSidebarClickAway = (event) => {
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

const handleSearchClickAway = (event) => {
    const searchOverlay = document.querySelector('[data-search-overlay]')
    const searchPanel = document.querySelector('[data-search-panel]')
    const searchButton = document.querySelector('[data-search-button]')

    if (searchOverlay?.contains(event.target) &&
        !searchPanel?.contains(event.target) &&
        !searchButton?.contains(event.target)) {
        isMobileSearchOpen.value = false
    }
}

const handleClickAway = (event) => {
    handleSidebarClickAway(event)
    handleSearchClickAway(event)
}

const handleKeyDown = (event) => {
    if (event.key === 'Escape') {
        if (isMobileSearchOpen.value) {
            isMobileSearchOpen.value = false
        }
        if (isSidebarOpen.value && isMobile()) {
            closeSidebar()
        }
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    const savedState = localStorage.getItem('sidebarOpen')
    isSidebarOpen.value = savedState ? savedState === 'true' : !isMobile()
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
})

const logoUrl = computed(() => {
    return page.props.personalisation?.app_logo
        ? `/storage/${page.props.personalisation.app_logo}`
        : null
})

const darkLogoUrl = computed(() => {
    return page.props.personalisation?.app_logo_dark
        ? `/storage/${page.props.personalisation.app_logo_dark}`
        : null
})
</script>


<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" role="document">
        <!-- Mobile Sidebar Overlay - Manages focus trap and keyboard interactions -->
        <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 bg-black/30 z-30" @click.stop="closeSidebar"
            role="dialog" aria-modal="true" aria-label="Mobile navigation menu" aria-hidden="true">
        </div>

        <!-- Main Sidebar Navigation -->
        <NavSidebarDesktop data-sidebar role="navigation" aria-label="Main sidebar" :aria-expanded="isSidebarOpen"
            :aria-hidden="!isSidebarOpen"
            class="fixed left-0 top-16 w-64 h-[calc(100vh-4rem)] transition-transform duration-200 z-40"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']" @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <header role="banner"
                class="fixed w-full top-0 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 z-40">
                <nav class="flex h-16 items-center px-4 gap-4" role="navigation" aria-label="Primary navigation">
                    <section class="flex items-center gap-4" aria-label="GuacPanel logo and menu controls">
                        <!-- Logo -->
                        <Link href="/" class="text-xl font-semibold text-gray-800 dark:text-white"
                            aria-label="Go to homepage">
                        <img v-if="logoUrl && darkLogoUrl" :src="logoUrl" alt="GuacPanel logo" class="h-10 w-auto object-contain dark:hidden" />
                        <img v-if="darkLogoUrl" :src="darkLogoUrl" alt="GuacPanel logo" class="h-10 w-auto object-contain hidden dark:block" />
                        <img v-if="logoUrl && !darkLogoUrl" :src="logoUrl" alt="GuacPanel logo" class="h-10 w-auto object-contain" />
                        <span v-if="!logoUrl && !darkLogoUrl">GuacPanel</span>
                        </Link>

                        <!-- Mobile Menu Toggle Button -->
                        <button type="button" data-menu-button
                            class="rounded-lg p-2 text-gray-500 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            @click="toggleSidebar" aria-label="Toggle navigation menu" :aria-expanded="isSidebarOpen">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </section>

                    <!-- Mobile Search Controls -->
                    <section class="flex items-center gap-4 md:hidden" aria-label="Mobile search">
                        <button type="button" data-search-button
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                            @click="isMobileSearchOpen = true" aria-label="Open search"
                            :aria-expanded="isMobileSearchOpen">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </section>

                    <!-- Desktop Search -->
                    <section class="hidden md:flex flex-1 justify-center pl-10" aria-label="Site search">
                        <div class="relative w-full max-w-lg">
                            <label for="desktop-search" class="sr-only">Search site</label>
                            <input id="desktop-search" type="search" placeholder="Search..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </section>

                    <!-- User Controls Section -->
                    <section class="flex flex-1 items-center justify-end gap-4" aria-label="User controls">
                        <ColorThemeSwitcher />
                        <Notification v-if="user" :user="user" />
                        <NavProfile v-if="user" :user="user" />
                        <Link v-else href="/login" class="text-sm font-medium text-gray-700 hover:text-gray-900"
                            aria-label="Login to your account">
                        Login
                        </Link>
                    </section>
                </nav>
            </header>

            <!-- Mobile Search Overlay -->
            <div v-if="isMobileSearchOpen" data-search-overlay role="dialog" aria-modal="true" aria-label="Search site"
                class="fixed inset-0 z-50 bg-gray-900/50 dark:bg-gray-900/80">
                <div data-search-panel class="fixed inset-x-0 top-0 bg-white dark:bg-gray-800 p-4 shadow-lg">
                    <div class="relative">
                        <label for="mobile-search" class="sr-only">Search site</label>
                        <input id="mobile-search" type="search" placeholder="Search..." autofocus
                            class="w-full pl-10 pr-12 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 text-base text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" />
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-gray-500"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button @click="isMobileSearchOpen = false" aria-label="Close search"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1" role="main"
                :class="['pt-16 px-4 sm:px-6 lg:px-8', isSidebarOpen ? 'md:ml-64' : 'md:ml-0']">
                <FlashMessage />
                <article class="py-8">
                    <slot />
                </article>
            </main>

            <Footer />
        </div>
    </div>
</template>
