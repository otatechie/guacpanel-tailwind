<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Components/NavSidebarDesktop.vue'
import NavProfile from '../Components/NavProfile.vue'
import Notification from '../Components/Notification.vue'
import FlashMessage from '../Components/FlashMessage.vue'
import Footer from '../Shared/Public/Footer.vue'
import ColorThemeSwitcher from '../Components/ColorThemeSwitcher.vue'
import Logo from '../Components/Logo.vue'
import Search from '../Components/Typesense/Search.vue'
import SystemNotice from '../Components/SystemNotice.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(false)
const isMobileSearchOpen = ref(false)
const noticeHeight = ref(0)

const headerHeight = computed(() => {
    return `${70 + (noticeHeight.value || 0)}px`
})

const handleNoticeHeightChange = (height) => {
    noticeHeight.value = height || 0
}

const isMobile = () => window.innerWidth < 768
const searchPlaceholder = "Search users and financial..."

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

const toggleMobileSearch = () => {
    isMobileSearchOpen.value = !isMobileSearchOpen.value
}

const closeMobileSearch = () => {
    isMobileSearchOpen.value = false
}

const handlers = {
    sidebar: (event) => {
        const elements = {
            sidebar: document.querySelector('[data-sidebar]'),
            menuButton: document.querySelector('[data-menu-button]'),
            sidebarContent: document.querySelector('[data-sidebar-content]')
        };

        if (Object.values(elements).some(el => el?.contains(event.target))) {
            return;
        }

        if (isMobile()) {
            closeSidebar();
        }
    },

    search: (event) => {
        const elements = {
            overlay: document.querySelector('[data-search-overlay]'),
            panel: document.querySelector('[data-search-panel]'),
            button: document.querySelector('[data-search-button]')
        };

        if (elements.overlay?.contains(event.target) &&
            !elements.panel?.contains(event.target) &&
            !elements.button?.contains(event.target)) {
            closeMobileSearch();
        }
    }
};

const handleClickAway = (event) => {
    handlers.sidebar(event);
    handlers.search(event);
}

const handleKeyDown = (event) => {
    if (event.key === 'Escape') {
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
</script>

<template>

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" role="document">
        <!-- Sidebar Overlay -->
        <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 bg-black/30 z-30" @click.stop="closeSidebar"
            role="dialog" aria-modal="true" aria-label="Mobile navigation menu" aria-hidden="true">
        </div>

        <!-- Main Sidebar Navigation -->
        <NavSidebarDesktop data-sidebar role="navigation" aria-label="Main sidebar" :aria-expanded="isSidebarOpen"
            :aria-hidden="!isSidebarOpen"
            class="fixed left-0 w-64 h-[calc(100vh-var(--header-height))] transition-transform duration-200 z-40"
            :style="{ top: headerHeight, '--header-height': headerHeight }"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']" @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">

            <!-- Header with System Notices -->
            <header role="banner" class="fixed w-full bg-white dark:bg-gray-800 z-40" :style="{ height: headerHeight }">

                <!-- System Notices -->
                <SystemNotice :notices="page.props.systemNotices" @height-change="handleNoticeHeightChange" />

                <!-- Navigation -->
                <nav class="flex h-17 items-center px-4 gap-4 shadow-sm" role="navigation"
                    aria-label="Primary navigation">
                    <!-- Logo Section -->
                    <section class="flex items-center gap-4" aria-label="GuacPanel logo and menu controls">
                        <Link href="/" class="flex items-center text-xl font-semibold text-gray-800 dark:text-white"
                            aria-label="Go to homepage">
                        <Logo size="5rem"
                            :class="page.props.personalisation?.app_logo ? 'transform -translate-x-8' : ''" />
                        </Link>

                        <button type="button" data-menu-button
                            class="rounded-lg p-2 text-gray-500 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            @click="toggleSidebar" aria-label="Toggle navigation menu" :aria-expanded="isSidebarOpen">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="roundL" stroke-linejoin="round" stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </section>

                    <!-- Mobile Search Controls -->
                    <section class="flex items-center gap-4 md:hidden" aria-label="Mobile search">
                        <button type="button" data-search-button
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                            @click="toggleMobileSearch" aria-label="Open search" :aria-expanded="isMobileSearchOpen">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </section>

                    <!-- Desktop Search -->
                    <section class="hidden lg:block flex-shrink-0 ml-4" aria-label="Site search">
                        <div class="w-80">
                            <Search :isMobile="false" :placeholder="searchPlaceholder" />
                        </div>
                    </section>

                    <!-- User Controls -->
                    <section class="flex flex-1 items-center justify-end gap-2" aria-label="User controls">
                        <ColorThemeSwitcher />
                        <Notification v-if="user" :user="user" />
                        <Link :href="route('admin.setting.index')"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 group relative hover:bg-gray-100 p-1.5 rounded-lg dark:hover:bg-gray-700 cursor-pointer dark:text-gray-300 dark:hover:text-gray-200">
                        <span
                            class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                            Settings
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        </Link>
                        <NavProfile v-if="user" :user="user" />
                        <Link v-else href="/login" class="text-sm font-medium text-gray-700 hover:text-gray-900"
                            aria-label="Login to your account">
                        Login
                        </Link>
                    </section>
                </nav>
            </header>

            <!-- Mobile Search Overlay -->
            <Search :isOpen="isMobileSearchOpen" :isMobile="true" :placeholder="searchPlaceholder"
                @close="isMobileSearchOpen = false" data-search-overlay />

            <!-- Main Content Area -->
            <main class="flex-1" role="main" :class="['px-4 sm:px-6 lg:px-8', isSidebarOpen ? 'md:ml-64' : 'md:ml-0']"
                :style="{ paddingTop: headerHeight }">
                <FlashMessage />
                <article class="py-8">
                    <slot />
                </article>
            </main>

            <Footer />
        </div>
    </div>
</template>
