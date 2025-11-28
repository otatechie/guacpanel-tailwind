<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Components/Nav/NavSidebarDesktop.vue'
import NavProfile from '@js/Components/Nav/NavProfile.vue'
import Notification from '@js/Components/Notification.vue'
import FlashMessage from '@js/Components/FlashMessage.vue'
import Footer from '@js/Shared/Public/Footer.vue'
import Logo from '@js/Components/Logo.vue'
import Search from '@js/Components/Typesense/Search.vue'
import ColorThemeSwitcher from '@js/Components/ColorThemeSwitcher.vue'
import MobileNotification from '@js/Components/MobileNotification.vue'
import NavDarkModeToggle from '@js/Components/Nav/NavDarkModeToggle.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(false)
const isMobileSearchOpen = ref(false)
const isLayoutReady = ref(false)

const isMobile = () => window.innerWidth < 768
const searchPlaceholder = 'Search...'

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
    sidebar: event => {
        const elements = {
            sidebar: document.querySelector('[data-sidebar]'),
            menuButton: document.querySelector('[data-menu-button]'),
            sidebarContent: document.querySelector('[data-sidebar-content]')
        }

        if (Object.values(elements).some(el => el?.contains(event.target))) {
            return
        }

        if (isMobile()) {
            closeSidebar()
        }
    },

    search: event => {
        const elements = {
            overlay: document.querySelector('[data-search-overlay]'),
            panel: document.querySelector('[data-search-panel]'),
            button: document.querySelector('[data-search-button]')
        }

        if (
            elements.overlay?.contains(event.target) &&
            !elements.panel?.contains(event.target) &&
            !elements.button?.contains(event.target)
        ) {
            closeMobileSearch()
        }
    }
}

const handleClickAway = event => {
    handlers.sidebar(event)
    handlers.search(event)
}

const handleKeyDown = event => {
    if (event.key === 'Escape') {
        if (isSidebarOpen.value && isMobile()) {
            closeSidebar()
        }
        if (isMobileSearchOpen.value) {
            closeMobileSearch()
        }
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    const savedState = localStorage.getItem('sidebarOpen')
    isSidebarOpen.value = savedState ? savedState === 'true' : !isMobile()

    setTimeout(() => {
        isLayoutReady.value = true
    }, 50)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
})
</script>

<template>
    <div
        class="min-h-screen bg-[var(--color-bg)]"
        role="document"
        :class="{ 'opacity-0': !isLayoutReady }">
        <!-- Mobile Notification -->
        <MobileNotification />

        <div
            v-if="isSidebarOpen && isMobile()"
            class="fixed inset-0 bg-black/30 z-30"
            role="dialog"
            aria-modal="true"
            aria-label="Mobile navigation menu"
            aria-hidden="true"
            @click.stop="closeSidebar"></div>

        <NavSidebarDesktop
            data-sidebar
            role="navigation"
            aria-label="Main sidebar"
            :aria-expanded="isSidebarOpen"
            :aria-hidden="!isSidebarOpen"
            class="fixed left-0 top-[70px] w-64 h-[calc(100vh-70px)] transition-transform duration-200 z-[60]"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']"
            @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <header
                role="banner"
                class="fixed top-0 left-0 right-0 w-full bg-[var(--color-surface)] z-[55] h-[70px] sm:h-[70px] h-16 shadow-xs border-b border-[var(--color-border)]">
                <nav
                    class="flex h-full items-center px-3 sm:px-4 gap-2 sm:gap-4"
                    role="navigation"
                    aria-label="Primary navigation">
                    <section
                        class="flex items-center gap-2 sm:gap-4 flex-shrink-0"
                        aria-label="Application logo and menu controls">
                        <Link
                            href="/dashboard"
                            class="flex items-center text-lg sm:text-xl font-semibold text-[var(--color-text)]"
                            aria-label="Go to dashboard">
                            <Logo :size="isMobile() ? '5rem' : '5rem'" />
                        </Link>

                        <button
                            type="button"
                            data-menu-button
                            class="rounded-lg p-2.5 sm:p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer min-h-[44px] min-w-[44px]"
                            aria-label="Toggle navigation menu"
                            :aria-expanded="isSidebarOpen"
                            @click="toggleSidebar">
                            <svg
                                class="w-5 h-5 sm:w-6 sm:h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </section>

                    <section class="hidden lg:block flex-shrink-0 ml-4" aria-label="Site search">
                        <div class="w-80">
                            <Search :is-mobile="false" :placeholder="searchPlaceholder" />
                        </div>
                    </section>

                    <section
                        class="flex items-center gap-2 sm:gap-4 lg:hidden flex-shrink-0"
                        aria-label="Mobile search">
                        <button
                            type="button"
                            data-search-button
                            class="p-2.5 sm:p-2 text-[var(--color-text-muted)] hover:text-[var(--color-text)] min-h-[44px] min-w-[44px] rounded-lg"
                            aria-label="Open search"
                            :aria-expanded="isMobileSearchOpen"
                            @click="toggleMobileSearch">
                            <svg
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </section>

                    <div class="flex-1"></div>

                    <section
                        class="flex items-center gap-1 sm:gap-2 flex-shrink-0"
                        aria-label="User controls"
                    >
                        <ColorThemeSwitcher />
                        <Notification
                            v-if="user"
                            :user="user"
                            class="scale-90 sm:scale-100 z-[100]"
                        />
                        <NavDarkModeToggle />
                        <NavProfile v-if="user" :user="user" />
                        <Link
                            v-else
                            href="/login"
                            class="text-sm font-medium text-[var(--color-text-muted)] hover:text-[var(--color-text)] px-2 py-1 rounded-md hover:bg-[var(--color-surface-muted)]"
                            aria-label="Login to your account">
                            Login
                        </Link>
                    </section>
                </nav>
            </header>

            <Search
                :is-open="isMobileSearchOpen"
                :is-mobile="true"
                :placeholder="searchPlaceholder"
                data-search-overlay
                @close="isMobileSearchOpen = false" />

            <main
                class="flex-1"
                role="main"
                :class="[
                    'transition-all duration-200',
                    'px-3 sm:px-4 lg:px-8',
                    isMobile() ? 'pt-28' : 'pt-[70px]',
                    isSidebarOpen ? 'md:ml-64' : 'md:ml-0'
                ]">
                <FlashMessage />
                <article class="py-4 sm:py-6 lg:py-8">
                    <slot />
                </article>
            </main>

            <Footer />
        </div>
    </div>
</template>

<style scoped>
.min-h-screen {
    transition: opacity 0.1s ease-in-out;
}

.flex-shrink-0 {
    transition: width 0.2s ease-in-out;
}

@media (max-width: 640px) {
    button {
        min-height: 44px;
        min-width: 44px;
    }

    .gap-1 {
        gap: 0.25rem;
    }
}
</style>
