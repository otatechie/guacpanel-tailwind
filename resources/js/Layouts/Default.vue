<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavSidebarDesktop from '@js/Components/Nav/NavSidebarDesktop.vue'
import NavProfile from '@js/Components/Nav/NavProfile.vue'
import Notification from '@js/Components/Notifications/Notification.vue'
import DemoNotifications from '@js/Components/Notifications/DemoNotifications.vue'
import FlashMessage from '@js/Components/Notifications/FlashMessage.vue'
import SystemNotificationBanner from '@js/Components/Notifications/SystemNotificationBanner.vue'
import Logo from '@js/Components/Common/Logo.vue'
import Search from '@js/Components/Typesense/Search.vue'
import ColorThemeSwitcher from '@js/Components/Nav/ColorThemeSwitcher.vue'
import MobileNotification from '@js/Components/Notifications/MobileNotification.vue'
import NavDarkModeToggle from '@js/Components/Nav/NavDarkModeToggle.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isSidebarOpen = ref(false)
const isMobileSearchOpen = ref(false)
const isLayoutReady = ref(false)
const notificationEnabled = computed(() => page.props.settings?.notificationEnabled)
const notificationInDemoMode = computed(() => page.props.settings?.notificationInDemoMode)
const bannerHeight = ref(0)

const headerTop = computed(() => `${bannerHeight.value}px`)
const sidebarTop = computed(() => `${bannerHeight.value + 70}px`)
const sidebarHeight = computed(() => `calc(100vh - ${bannerHeight.value + 70}px)`)
const mainPadding = computed(() => {
  const base = isMobile() ? 112 : 70
  return `${bannerHeight.value + base}px`
})

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
      sidebarContent: document.querySelector('[data-sidebar-content]'),
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
      button: document.querySelector('[data-search-button]'),
    }

    if (
      elements.overlay?.contains(event.target) &&
      !elements.panel?.contains(event.target) &&
      !elements.button?.contains(event.target)
    ) {
      closeMobileSearch()
    }
  },
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
  <div class="min-h-screen bg-[var(--color-bg)]" role="document" :class="{ 'opacity-0': !isLayoutReady }">
    <!-- System Notification Banner - Fixed at very top -->
    <SystemNotificationBanner v-model="bannerHeight" />

    <!-- Mobile Notification -->
    <MobileNotification />

    <div v-if="isSidebarOpen && isMobile()" class="fixed inset-0 z-30 bg-black/30" role="dialog" aria-modal="true"
      aria-label="Mobile navigation menu" aria-hidden="true" @click.stop="closeSidebar"></div>

    <NavSidebarDesktop data-sidebar role="navigation" aria-label="Main sidebar" :aria-expanded="isSidebarOpen"
      :aria-hidden="!isSidebarOpen" class="fixed left-0 z-60 w-64 transition-transform duration-200"
      :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-64']" :style="{ top: sidebarTop, height: sidebarHeight }"
      @close="closeSidebar" />

    <div class="flex min-h-screen flex-col">
      <header role="banner"
        class="fixed right-0 left-0 z-55 h-[70px] w-full border-b border-(--color-border) bg-(--color-surface) shadow-xs sm:h-[70px]"
        :style="{ top: headerTop }">
        <nav class="flex h-full items-center gap-2 px-3 sm:gap-4 sm:px-4" role="navigation"
          aria-label="Primary navigation">
          <section class="flex flex-shrink-0 items-center gap-2 transition sm:gap-4"
            :class="[isSidebarOpen ? 'md:w-58' : 'md:w-auto']" aria-label="Application logo and menu controls">
            <Link href="/dashboard" class="flex items-center text-lg font-semibold text-[var(--color-text)] sm:text-xl"
              aria-label="Go to dashboard">
              <Logo :size="isMobile() ? '5rem' : '5rem'" />
            </Link>
          </section>

          <section class="flex-shrink-0 transition" aria-label="Sidebar Toggle Area">
            <button type="button" data-menu-button
              class="flex min-h-[44px] min-w-[44px] cursor-pointer items-center justify-center rounded-lg text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] focus:ring-2 focus:ring-gray-200 focus:outline-none"
              aria-label="Toggle navigation menu" :aria-expanded="isSidebarOpen" @click="toggleSidebar">
              <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
            </button>
          </section>

          <section class="hidden flex-shrink-0 lg:block" aria-label="Site search">
            <div class="w-80">
              <Search :is-mobile="false" :placeholder="searchPlaceholder" />
            </div>
          </section>

          <section class="flex flex-shrink-0 items-center gap-2 sm:gap-4 lg:hidden" aria-label="Mobile search">
            <button type="button" data-search-button
              class="min-h-[44px] min-w-[44px] rounded-lg p-2.5 text-[var(--color-text-muted)] hover:text-[var(--color-text)] sm:p-2"
              aria-label="Open search" :aria-expanded="isMobileSearchOpen" @click="toggleMobileSearch">
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </section>

          <div class="flex-1"></div>

          <section class="flex flex-shrink-0 items-center gap-1 sm:gap-2" aria-label="User controls">
            <ColorThemeSwitcher />
            <Notification v-if="user && notificationEnabled && !notificationInDemoMode" :user="user"
              class="z-[100] scale-90 sm:scale-100" />
            <DemoNotifications v-else-if="user && notificationEnabled && notificationInDemoMode" :user="user"
              class="z-[100] scale-90 sm:scale-100" />
            <NavDarkModeToggle />
            <NavProfile v-if="user" :user="user" />
            <Link v-else href="/login"
              class="rounded-md px-2 py-1 text-sm font-medium text-[var(--color-text-muted)] hover:bg-[var(--color-surface-muted)] hover:text-[var(--color-text)]"
              aria-label="Login to your account">
              Login
            </Link>
          </section>
        </nav>
      </header>

      <Search :is-open="isMobileSearchOpen" :is-mobile="true" :placeholder="searchPlaceholder" data-search-overlay
        @close="isMobileSearchOpen = false" />

      <main class="flex-1" role="main" :class="[
        'transition-all duration-200',
        'px-3 sm:px-4 lg:px-8',
        isSidebarOpen ? 'md:ml-64' : 'md:ml-0',
      ]" :style="{ paddingTop: mainPadding }">
        <FlashMessage />
        <article class="py-4 sm:py-6 lg:py-8">
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
