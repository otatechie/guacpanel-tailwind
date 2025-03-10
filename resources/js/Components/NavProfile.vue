<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { cycleTheme, getCurrentThemeState } from '@/darkMode';

const page = usePage();
const user = computed(() => page.props.auth.user);
const menuOpen = ref(false);
const menuWrapper = ref(null);
const avatarUrl = computed(() => user.value?.avatar);

const safeUserName = computed(() => {
    return user.value?.name ? String(user.value.name).replace(/[<>]/g, '') : '';
});

const isDark = ref(document.documentElement.classList.contains('dark'));
const themeState = ref(getCurrentThemeState());

const handleThemeChange = () => {
    themeState.value = cycleTheme();
    closeMenu();
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscapeKey);

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

    onBeforeUnmount(() => {
        observer.disconnect();
        document.removeEventListener('click', handleClickOutside);
        document.removeEventListener('keydown', handleEscapeKey);
    });
});

const handleClickOutside = (event) => {
    if (menuWrapper.value &&
        !menuWrapper.value.contains(event.target) &&
        event.target.id !== 'user-menu-button') {
        menuOpen.value = false;
    }
};

const handleEscapeKey = (event) => {
    if (event.key === 'Escape') {
        menuOpen.value = false;
    }
};

const toggleMenu = () => {
    menuOpen.value = !menuOpen.value;
};

const closeMenu = () => {
    menuOpen.value = false;
};
</script>

<template>
    <nav class="relative" ref="menuWrapper">
        <button type="button"
            class="relative flex items-center rounded-full text-sm focus:outline-none lg:rounded-md lg:p-2 lg:hover:bg-gray-50 dark:lg:hover:bg-gray-700 cursor-pointer"
            id="user-menu-button" :aria-expanded="menuOpen.toString()" aria-haspopup="true" @click="toggleMenu">
            <img :src="avatarUrl" :alt="`${safeUserName}'s avatar`"
                class="h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800" />
            <svg class="ml-1 hidden h-4 w-4 text-gray-400 dark:text-gray-500 lg:block" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <menu v-show="menuOpen"
            class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-xl bg-white dark:bg-gray-800 py-2 shadow-md ring-1 ring-gray-300 dark:ring-gray-700 ring-opacity-5">
            <li>
                <button @click="handleThemeChange"
                    class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                    role="menuitem">
                    <svg v-if="themeState.nextThemeIcon === 'sun'"
                        class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg v-else-if="themeState.nextThemeIcon === 'moon'"
                        class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <svg v-else class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                    </svg>
                    {{ themeState.nextThemeText }}
                </button>
            </li>

            <li role="separator" class="my-1 border-t border-gray-200 dark:border-gray-700"></li>

            <li>
                <Link :href="route('user.index')" @click="closeMenu"
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Your account
                </Link>
            </li>

            <li>
                <Link :href="route('user.index') + '#password-section'" @click="closeMenu"
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
                Change password
                </Link>
            </li>

            <li role="separator" class="my-1 border-t border-gray-200 dark:border-gray-700"></li>

            <li>
                <Link :href="route('user.two.factor')" @click="closeMenu"
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                Two-Factor Authentication
                </Link>
            </li>

            <li role="separator" class="my-1 border-t border-gray-200 dark:border-gray-700"></li>

            <li>
                <Link :href="route('admin.setting.index')" @click="closeMenu"
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
                Help
                </Link>
            </li>

            <li>
                <Link :href="route('documentation.index')" target="_blank" @click="closeMenu"
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                Documentation
                </Link>
            </li>

            <li role="separator" class="my-1 border-t border-gray-200 dark:border-gray-700"></li>

            <li>
                <Link :href="route('admin.setting.index')" @click="closeMenu"
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-blue-500" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Settings
                </Link>
            </li>

            <li role="separator" class="my-1 border-t border-gray-200 dark:border-gray-700"></li>

            <li>
                <Link :href="route('logout')" method="post" as="button" @click="closeMenu"
                    class="group flex w-full items-center px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer">
                <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3h15" />
                </svg>
                Sign out
                </Link>
            </li>
        </menu>
    </nav>
</template>
