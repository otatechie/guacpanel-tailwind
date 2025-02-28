<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NavSidebarDesktop from '@/Shared/Public/NavSidebarDesktop.vue'
import ArticleNavigation from '@/Shared/Public/ArticleNavigation.vue'
import Footer from '@/Shared/Public/Footer.vue'
import { cycleTheme, getCurrentThemeState } from '@/darkMode'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(false)
const isDark = ref(document.documentElement.classList.contains('dark'))
const themeState = ref(getCurrentThemeState())

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

const toggleDarkMode = () => {
    themeState.value = cycleTheme();
};

const articleLinks = ref([
    { text: 'Overview', href: '#overview', active: true },
    { text: 'Basic test', href: '#basic-test' },
    { text: 'Card numbers', href: '#card-numbers' },
    { text: 'Internation cards', href: '#internation-cards' },
    { text: 'Hot Exit', href: '#hot-exit' },
    { text: 'Common questions', href: '#common-questions' }
]);

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    const savedState = localStorage.getItem('sidebarOpen')
    isSidebarOpen.value = savedState ? savedState === 'true' : !isMobile()

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                isDark.value = document.documentElement.classList.contains('dark');
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });

    onUnmounted(() => {
        observer.disconnect();
        document.removeEventListener('click', handleClickAway)
    });
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 bg-black/30 z-30" @click.stop="closeSidebar"
            aria-hidden="true">
        </div>

        <NavSidebarDesktop data-sidebar
            class="fixed left-0 top-16 w-64 h-[calc(100vh-4rem)] transition-transform duration-200 z-40 bg-white dark:bg-gray-800 shadow-lg"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']" @close="closeSidebar" />

        <div class="flex flex-col min-h-screen">
            <header
                class="fixed w-full top-0 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 z-40">
                <nav class="flex h-16 items-center px-4 gap-4">
                    <section class="flex items-center gap-4">
                        <Link href="/" class="text-xl font-semibold text-gray-900">
                        Your Logo
                        </Link>
                        <button type="button"
                            class="rounded-lg p-2 text-gray-500 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 cursor-pointer"
                            @click="toggleSidebar" data-menu-button aria-label="Toggle Menu"
                            :aria-expanded="isSidebarOpen">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </section>

                    <section class="flex flex-1 items-center justify-end gap-4">
                        <!-- Dark Mode Toggle -->
                        <button
                            type="button"
                            class="rounded-lg p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 transition-all"
                            @click="toggleDarkMode"
                            aria-label="Toggle Dark Mode">
                            <svg v-if="themeState.nextThemeIcon === 'sun'"
                                class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <svg v-else-if="themeState.nextThemeIcon === 'moon'"
                                class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                            </svg>
                            <svg v-else
                                class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                        </button>

                        <!-- Demo Button -->
                        <Link
                            href="/demo"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                             Demo
                        </Link>
                    </section>
                </nav>
            </header>

            <main class="flex-1 transition-all duration-200" :class="[
                'pt-16',
                isSidebarOpen ? 'md:ml-64 xl:mr-64' : 'md:ml-0 xl:mr-64'
            ]">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <article class="py-8 prose prose-gray dark:prose-invert prose-headings:scroll-mt-20 max-w-none">
                        <slot />
                    </article>
                </div>
            </main>

            <ArticleNavigation :links="articleLinks" />

            <Footer />
        </div>
    </div>
</template>

<style scoped>
/* Add smooth scrolling for the right sidebar */
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
