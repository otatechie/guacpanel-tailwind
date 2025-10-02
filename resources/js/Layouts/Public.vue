<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Shared/Public/NavSidebarDesktop.vue'
import Footer from '@/Shared/Public/Footer.vue'
import { cycleTheme, getCurrentThemeState } from '@/darkMode'

const sidebarStorageKey = 'sidebarOpen'
const isMobile = () => window.innerWidth < 768
const isSidebarOpen = ref(false)
const isDark = ref(document.documentElement.classList.contains('dark'))
const themeState = ref(getCurrentThemeState())

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem(sidebarStorageKey, isSidebarOpen.value)
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem(sidebarStorageKey, false)
}

const handleClickAway = event => {
    const elements = {
        sidebar: document.querySelector('[data-sidebar]'),
        menuButton: document.querySelector('[data-menu-button]'),
        sidebarContent: document.querySelector('[data-sidebar-content]')
    }

    const isClickInside = Object.values(elements).some(el => el?.contains(event.target))

    if (isClickInside) return

    if (isMobile()) {
        closeSidebar()
    }
}

const handleKeyDown = event => {
    if (event.key === 'Escape' && isSidebarOpen.value && isMobile()) {
        closeSidebar()
    }
}

const toggleDarkMode = () => {
    themeState.value = cycleTheme()
}

const setupThemeObserver = () => {
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'class') {
                isDark.value = document.documentElement.classList.contains('dark')
            }
        })
    })

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    })

    return observer
}

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)

    const savedSidebarState = localStorage.getItem(sidebarStorageKey)
    isSidebarOpen.value = savedSidebarState ? savedSidebarState === 'true' : !isMobile()

    const observer = setupThemeObserver()

    onUnmounted(() => {
        observer.disconnect()
        document.removeEventListener('click', handleClickAway)
        document.removeEventListener('keydown', handleKeyDown)
    })
})
</script>

<template>
    <div class="min-h-screen bg-[var(--color-bg)]" role="document">
        <!-- Mobile Sidebar Overlay -->
        <div
            v-if="isSidebarOpen && isMobile()"
            class="fixed inset-0 bg-black/30 z-30"
            role="dialog"
            aria-modal="true"
            aria-label="Mobile navigation menu"
            aria-hidden="true"
            @click.stop="closeSidebar"></div>

        <!-- Main Sidebar Navigation -->
        <NavSidebarDesktop
            data-sidebar
            role="navigation"
            aria-label="Main sidebar"
            :aria-expanded="isSidebarOpen"
            :aria-hidden="!isSidebarOpen"
            class="fixed left-0 top-16 w-64 h:[calc(100vh-4rem)] transition-transform duration-200 z-40 bg-[var(--color-surface)] shadow-lg"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']"
            @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <header
                role="banner"
                class="fixed w-full top-0 bg-[var(--color-surface)] border-b border-[var(--color-border)] z-40">
                <nav
                    class="flex h-16 items-center px-4 gap-4"
                    role="navigation"
                    aria-label="Primary navigation">
                    <section
                        class="flex items-center gap-4"
                        aria-label="Site logo and menu controls">
                        <!-- Logo -->
                        <Link
                            href="/"
                            class="text-xl font-semibold text-[var(--color-text)] flex items-center"
                            aria-label="Go to homepage">
                            <img
                                src="/images/logo.png"
                                class="h-10 w-auto block dark:hidden"
                                alt="Logo" />
                            <img
                                src="/images/logo-dark.png"
                                class="h-10 w-auto hidden dark:block"
                                alt="Logo Dark" />
                        </Link>

                        <!-- Mobile Menu Toggle -->
                        <button
                            type="button"
                            data-menu-button
                            class="rounded-lg p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            aria-label="Toggle navigation menu"
                            :aria-expanded="isSidebarOpen"
                            @click="toggleSidebar">
                            <svg
                                class="w-6 h-6"
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

                    <section
                        class="flex flex-1 items-center justify-end gap-4"
                        aria-label="Site controls">
                        <!-- Theme Toggle Button -->
                        <button
                            type="button"
                            class="rounded-lg p-2 text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all cursor-pointer"
                            aria-label="Toggle color theme"
                            @click="toggleDarkMode">
                            <svg
                                v-if="themeState.nextThemeIcon === 'sun'"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <svg
                                v-else-if="themeState.nextThemeIcon === 'moon'"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                            </svg>
                            <svg
                                v-else
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                        </button>

                        <Link
                            href="/login"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-500 hover:bg-teal-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                            aria-label="View demo">
                            Demo
                        </Link>
                    </section>
                </nav>
            </header>

            <!-- Main Content -->
            <main
                role="main"
                class="flex-1 transition-all duration-200"
                :class="['pt-16', isSidebarOpen ? 'md:ml-64 xl:mr-64' : 'md:ml-0 xl:mr-64']">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <article
                        class="py-8 prose prose-gray dark:prose-invert prose-headings:scroll-mt-20 max-w-none">
                        <slot />
                    </article>
                </div>
            </main>

            <Footer />
        </div>
    </div>
</template>

<style scoped>
aside div {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

aside div::-webkit-scrollbar {
    width: 4px;
}

aside div::-webkit-scrollbar-track {
    background: transparent;
}

aside div::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 2px;
}

html {
    scroll-behavior: smooth;
}
</style>
