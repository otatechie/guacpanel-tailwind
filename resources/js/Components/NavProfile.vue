<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { cycleTheme, getCurrentThemeState } from '@/darkMode'

const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const safeUserName = computed(() => user.value?.name ? String(user.value.name).replace(/[<>]/g, '') : '')
const primaryRole = computed(() => user.value?.roles?.[0] || '')

const menuOpen = ref(false)
const menuWrapper = ref(null)
const isDark = ref(document.documentElement.classList.contains('dark'))
const themeState = ref(getCurrentThemeState())
const hoverColor = ref('group-hover:text-[var(--primary-color)]')

const menuItems = [
    {
        type: 'theme',
        label: computed(() => themeState.value.nextThemeText),
        icon: computed(() => themeState.value.nextThemeIcon),
        action: () => {
            themeState.value = cycleTheme()
            closeMenu()
        }
    },
    {
        type: 'link',
        label: 'Your account',
        icon: 'user',
        href: route('user.index')
    },
    {
        type: 'link',
        label: 'Change password',
        icon: 'lock',
        href: route('user.index') + '#password-section'
    },
    {
        type: 'link',
        label: 'Browser sessions',
        icon: 'browser',
        href: route('user.session.index')
    },
    { type: 'separator' },
    {
        type: 'link',
        label: 'Two-Factor Authentication',
        icon: 'shield',
        href: route('user.two.factor')
    },
    { type: 'separator' },
    {
        type: 'link',
        label: 'Help',
        icon: 'help',
        href: route('admin.setting.index')
    },
    {
        type: 'link',
        label: 'Documentation',
        icon: 'book',
        href: route('documentation.index'),
        target: '_blank'
    },
    { type: 'separator' },
    {
        type: 'link',
        label: 'Sign out',
        icon: 'logout',
        href: route('logout'),
        method: 'post',
        as: 'button',
        class: 'text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer w-full',
        iconClass: 'text-red-400 group-hover:text-red-500'
    }
]

const handleClickOutside = (event) => {
    if (menuWrapper.value && !menuWrapper.value.contains(event.target) && event.target.id !== 'user-menu-button') {
        menuOpen.value = false
    }
}

const handleEscapeKey = (event) => {
    if (event.key === 'Escape') menuOpen.value = false
}

const toggleMenu = () => menuOpen.value = !menuOpen.value
const closeMenu = () => menuOpen.value = false

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleEscapeKey)

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class')
                isDark.value = document.documentElement.classList.contains('dark')
        })
    })

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    })

    onBeforeUnmount(() => {
        observer.disconnect()
        document.removeEventListener('click', handleClickOutside)
        document.removeEventListener('keydown', handleEscapeKey)
    })
})

const icons = {
    user: '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
    lock: '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />',
    device: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />',
    shield: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />',
    help: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />',
    book: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />',
    cog: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    logout: '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3h15" />',
    sun: '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />',
    moon: '<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />',
    system: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />',
    check: '<path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />',
    browser: '<path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672Zm-7.518-.267A8.25 8.25 0 1 1 20.25 10.5M8.288 14.212A5.25 5.25 0 1 1 17.25 10.5" />'
}
</script>


<template>
    <nav class="relative" ref="menuWrapper">
        <button type="button"
            class="relative flex items-center rounded-full text-sm focus:outline-none lg:rounded-md uppercase lg:p-1.5 lg:hover:bg-gray-50 dark:lg:hover:bg-gray-700 cursor-pointer group"
            id="user-menu-button" :aria-expanded="menuOpen.toString()" @click="toggleMenu">
            <img :src="avatarUrl" :alt="`${safeUserName}'s avatar`"
                class="size-5 rounded-full ring-2 ring-white dark:ring-gray-800" />
            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap capitalize">
                Profile
            </span>
            <svg class="ml-1 hidden h-3.5 w-3.5 text-gray-400 dark:text-gray-400 lg:block"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <menu v-show="menuOpen"
            class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-xl bg-white dark:bg-gray-800 py-2 shadow-md ring-1 ring-gray-300 dark:ring-gray-700 ring-opacity-5 transition-all duration-200 ease-in-out transform"
            :class="menuOpen ? 'opacity-100 scale-100' : 'opacity-0 scale-95'">
            <li class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ safeUserName }}</div>
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-700/50 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10 dark:ring-gray-400/20 font-mono uppercase">
                            <svg class="mr-1 h-3 w-3 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24"
                                fill="currentColor" v-html="icons.check" />
                            {{ primaryRole }}
                        </span>
                    </div>
                </div>
            </li>

            <template v-for="(item, index) in menuItems" :key="index">
                <li v-if="item.type === 'separator'" role="separator"
                    class="my-1 border-t border-gray-200 dark:border-gray-700" />

                <li v-else-if="item.type === 'theme'">
                    <button @click="item.action"
                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                        role="menuitem">
                        <svg class="mr-3 h-5 w-5 text-gray-600 dark:text-gray-500" :class="hoverColor"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="icons[item.icon.value]" />
                        <span :class="hoverColor">{{ item.label }}</span>
                    </button>
                </li>

                <li v-else-if="item.type === 'link'">
                    <Link :href="item.href" :method="item.method" :as="item.as" :target="item.target" @click="closeMenu"
                        class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        :class="item.class">
                        <svg class="mr-3 h-5 w-5 text-gray-600 dark:text-gray-500" 
                            :class="[item.label === 'Sign out' ? '' : hoverColor, item.iconClass]"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="icons[item.icon]" />
                        <span :class="item.label === 'Sign out' ? '' : hoverColor">{{ item.label }}</span>
                    </Link>
                </li>
            </template>
        </menu>
    </nav>
</template>
