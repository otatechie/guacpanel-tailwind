<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import NavSidebarDesktop from '@js/Components/Nav/NavSidebarDesktop.vue'
import NavProfile from '@js/Components/Nav/NavProfile.vue'
import Notification from '@js/Components/Notifications/Notification.vue'
import DemoNotifications from '@js/Components/Notifications/DemoNotifications.vue'
import FlashMessage from '@js/Components/Notifications/FlashMessage.vue'
import SystemNotificationBanner from '@js/Components/Notifications/SystemNotificationBanner.vue'
import Logo from '@js/Components/Common/Logo.vue'
import MobileNotification from '@js/Components/Notifications/MobileNotification.vue'
import CommandPalette from '@js/Components/CommandPalette/CommandPalette.vue'
import CommandPaletteTrigger from '@js/Components/CommandPalette/CommandPaletteTrigger.vue'
import { useCommandPalette } from '@js/composables/useCommandPalette'
import { usePermissions } from '@js/composables/usePermissions'
import ImpersonationBanner from '@js/Components/Admin/ImpersonationBanner.vue'
import { PanelLeftCloseIcon, PanelLeftOpenIcon, SearchIcon, Settings2Icon } from '@lucide/vue'
const page = usePage()
// The mobile icon opens the same palette the desktop trigger and Cmd+K do.
const { open: openCommandPalette } = useCommandPalette()
const { user, hasPermission } = usePermissions()
const isSidebarOpen = ref(false)
const isLayoutReady = ref(false)
const notificationEnabled = computed(() => page.props.settings?.notificationEnabled)
const notificationInDemoMode = computed(() => page.props.settings?.notificationInDemoMode)
const bannerHeight = ref(0)

/* 36px controls in a 70px bar left 17px of dead space above and below them. */
const HEADER_HEIGHT = 56

const headerTop = computed(() => `${bannerHeight.value}px`)
const headerHeight = `${HEADER_HEIGHT}px`
const sidebarTop = computed(() => `${bannerHeight.value + HEADER_HEIGHT}px`)
const sidebarHeight = computed(() => `calc(100vh - ${bannerHeight.value + HEADER_HEIGHT}px)`)
const mainPadding = computed(() => `${bannerHeight.value + HEADER_HEIGHT}px`)

/* Reactive, not a bare `window.innerWidth` read: the divider between sidebar
   and content is only drawn on desktop, so a resize has to repaint it. */
const isMobile = ref(false)
const updateIsMobile = () => {
    isMobile.value = window.innerWidth < 768
    if (isMobile.value) isSidebarOpen.value = false
}

/* The sidebar and content share one divider on desktop: the header paints over
   the content's border-left for its own height, so it draws the same hairline. */
const showSidebarDivider = computed(() => isSidebarOpen.value && !isMobile.value)

/* The pages the gear leads to. Listed rather than matched on `admin.*` so that
   adding an admin route does not silently light the gear up on a page the
   settings index does not actually link to. */
const ADMIN_ROUTES = [
    'admin.setting.*',
    'admin.user.*',
    'admin.audit.*',
    'admin.personalization.*',
    'admin.backup.*',
    'admin.permission.*',
    'admin.login.history.*',
    'admin.sessions.*',
    'admin.health.*',
    'admin.notifications.*',
]

const isAdminActive = computed(() =>
    ADMIN_ROUTES.some(name => {
        try {
            return route().current(name)
        } catch {
            return false
        }
    })
)

/* PanelLeft rather than a hamburger: this control collapses a side panel, it
   doesn't open a menu. The open/close pair states what the click will do
   instead of leaving it to be recalled. */
const toggleLabel = computed(() => {
    if (isMobile.value) return isSidebarOpen.value ? 'Close menu' : 'Open menu'
    return isSidebarOpen.value ? 'Collapse sidebar' : 'Expand sidebar'
})

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString())
}

const closeSidebar = () => {
    isSidebarOpen.value = false
    localStorage.setItem('sidebarOpen', 'false')
}

const handlers = {
    sidebar: event => {
        const elements = {
            sidebar: document.querySelector('[data-sidebar]'),
            menuButton: document.querySelector('[data-menu-button]'),
            sidebarContent: document.querySelector('[data-sidebar-content]'),
        }

        if (Object.values(elements).some(el => el?.contains(event.target))) {
            return
        }

        if (isMobile.value) {
            closeSidebar()
        }
    },
}

const handleClickAway = event => {
    handlers.sidebar(event)
}

const handleKeyDown = event => {
    if (event.key === 'Escape') {
        if (isSidebarOpen.value && isMobile.value) {
            closeSidebar()
        }
    }
}

const removeNavigateListener = router.on('navigate', () => {
    if (isMobile.value) closeSidebar()
})

onMounted(() => {
    document.addEventListener('click', handleClickAway)
    document.addEventListener('keydown', handleKeyDown)
    window.addEventListener('resize', updateIsMobile)

    isMobile.value = window.innerWidth < 768
    const savedState = localStorage.getItem('sidebarOpen')
    /* The saved preference is a desktop one. Restoring it on a phone opens the
       overlay over the page on first paint, so mobile always starts closed. */
    isSidebarOpen.value = isMobile.value ? false : savedState !== 'false'

    setTimeout(() => {
        isLayoutReady.value = true
    }, 50)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway)
    document.removeEventListener('keydown', handleKeyDown)
    window.removeEventListener('resize', updateIsMobile)
    removeNavigateListener()
})
</script>

<template>
    <div
        class="bg-background min-h-screen"
        role="document"
        :class="{ 'opacity-0': !isLayoutReady }">
        <!-- System Notification Banner - Fixed at very top -->
        <SystemNotificationBanner v-model="bannerHeight" />

        <!-- Command Palette -->
        <CommandPalette />

        <!-- Mobile Notification -->
        <MobileNotification />

        <!-- Impersonation Banner -->
        <ImpersonationBanner />

        <div
            v-if="isSidebarOpen && isMobile"
            class="fixed inset-0 z-20 bg-black/30"
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
            class="fixed left-0 z-30 w-64 transition-transform duration-200"
            :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']"
            :style="{ top: sidebarTop, height: sidebarHeight }"
            @close="closeSidebar" />

        <div class="flex min-h-screen flex-col">
            <header
                role="banner"
                class="bg-card fixed right-0 left-0 z-40 w-full"
                :style="{ top: headerTop, height: headerHeight }">
                <!-- Continues the sidebar divider across the header, which paints
                     over the content's border-left for its own height. -->
                <span
                    v-if="showSidebarDivider"
                    aria-hidden="true"
                    class="bg-border absolute top-0 bottom-0 left-64 w-px"></span>

                <nav
                    class="flex h-full items-center"
                    role="navigation"
                    aria-label="Primary navigation">
                    <!-- Brand zone. Held to the sidebar's width while it is open so
                         the divider lands on its edge instead of through the search. -->
                    <section
                        class="flex shrink-0 items-center gap-2 px-3 sm:px-4"
                        :class="[isSidebarOpen ? 'md:w-64' : 'md:w-auto']">
                        <button
                            type="button"
                            data-menu-button
                            class="nav-bar-btn"
                            :aria-label="toggleLabel"
                            :aria-expanded="isSidebarOpen"
                            @click="toggleSidebar">
                            <PanelLeftCloseIcon v-if="isSidebarOpen" class="nav-bar-icon" />
                            <PanelLeftOpenIcon v-else class="nav-bar-icon" />
                            <span class="nav-bar-tooltip nav-bar-tooltip-start">
                                {{ toggleLabel }}
                            </span>
                        </button>

                        <span class="bg-border h-6 w-px shrink-0" aria-hidden="true"></span>

                        <Link
                            href="/dashboard"
                            class="flex items-center"
                            aria-label="Go to dashboard">
                            <Logo size="2.5rem" />
                        </Link>
                    </section>

                    <div class="flex h-full flex-1 items-center gap-2 px-3 sm:px-4">
                        <section class="hidden shrink-0 lg:block">
                            <div class="w-72">
                                <CommandPaletteTrigger />
                            </div>
                        </section>

                        <section class="shrink-0 lg:hidden">
                            <button
                                type="button"
                                class="nav-bar-btn"
                                aria-label="Open search"
                                @click="openCommandPalette">
                                <SearchIcon class="nav-bar-icon" />
                                <span class="nav-bar-tooltip">Search</span>
                            </button>
                        </section>

                        <div class="flex-1"></div>

                        <section class="flex shrink-0 items-center gap-2">
                            <!-- Replaces the System settings sidebar item and its submenu.
                             Same gate the nav item carried, so who can see it is unchanged.
                             Being current shows in the ink alone — a fill would read as
                             hover rather than as location. -->
                            <Link
                                v-if="hasPermission('manage-settings')"
                                :href="route('admin.setting.index')"
                                class="nav-bar-btn"
                                :class="{ 'text-primary': isAdminActive }"
                                aria-label="Administration"
                                :aria-current="isAdminActive ? 'page' : undefined">
                                <Settings2Icon class="nav-bar-icon" />
                                <span class="nav-bar-tooltip">Administration</span>
                            </Link>
                            <Notification
                                v-if="user && notificationEnabled && !notificationInDemoMode"
                                :user="user" />
                            <DemoNotifications
                                v-else-if="user && notificationEnabled && notificationInDemoMode"
                                :user="user" />
                            <NavProfile v-if="user" :user="user" />
                            <Link
                                v-else
                                href="/login"
                                class="text-muted-foreground hover:text-foreground text-sm font-medium">
                                Sign in
                            </Link>
                        </section>
                    </div>
                </nav>
            </header>

            <main
                class="flex-1"
                role="main"
                :class="[
                    'transition-[margin] duration-200',
                    'px-3 sm:px-4 lg:px-8',
                    isSidebarOpen ? 'md:ml-64' : 'md:ml-0',
                    /* Divider lives on the scrolling content, not the fixed sidebar,
                       so it spans the full document height rather than one viewport. */
                    showSidebarDivider ? 'border-border border-l' : '',
                ]"
                :style="{ paddingTop: mainPadding }">
                <FlashMessage />
                <article class="mx-auto max-w-6xl py-4 sm:py-6 lg:py-8">
                    <slot />
                </article>
            </main>
        </div>
    </div>
</template>

<style scoped>
.min-h-screen {
    transition: opacity 0.1s ease-in-out;
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
