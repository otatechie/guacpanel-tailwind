<template>

    <div class="relative" ref="menuWrapper">
        <div>
            <button type="button"
                class="relative flex max-w-xs items-center rounded-full text-sm focus:outline-none lg:rounded-md lg:p-2 cursor-pointer"
                id="user-menu-button" :aria-expanded="menuOpen.toString()" aria-haspopup="true" @click="toggleMenu"
                aria-label="User Menu">
                <span class="absolute -inset-1.5 lg:hidden"></span>
                <img :src="avatarUrl" :alt="`${user.name}'s avatar`" class="h-8 w-8 rounded-full" />
                <span class="ml-3 hidden text-sm font-medium text-gray-700 lg:block">
                    <span class="sr-only">Open user menu for </span>
                </span>
                <svg class="ml-1 hidden h-5 w-5 flex-shrink-0 text-gray-400 lg:block" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div v-show="menuOpen"
            class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-gray-300 ring-opacity-5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <Link :href="route('user.index')" @click="closeMenu"
                class="group px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-0">
            <div class="flex items-center w-full">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
                Switch theme
            </div>
            </Link>
            <div class="border-t border-gray-200 w-10/12 mx-auto"></div>
            <Link :href="route('user.index')" @click="closeMenu"
                class="group px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-0">
            <div class="flex items-center w-full">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Your account
            </div>
            </Link>
            <Link :href="route('user.index')" @click="closeMenu"
                class="group px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-0">
            <div class="flex items-center w-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
                Change password
            </div>
            </Link>
            <div class="border-t border-gray-200 w-10/12 mx-auto"></div>
            <Link :href="route('user.two.factor')" @click="closeMenu"
                class="px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-0">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Two-Factor Authentication
            </Link>
            <div class="border-t border-gray-200 w-10/12 mx-auto"></div>
            <Link :href="route('admin.setting.index')" @click="closeMenu"
                class="px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
            </svg>
            Help
            </Link>
            <div class="border-t border-gray-200 w-10/12 mx-auto"></div>
            <Link :href="route('admin.setting.index')" @click="closeMenu"
                class="px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-5 h-5 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
            Docs
            </Link>
            <Link :href="route('admin.setting.index')" @click="closeMenu"
                class="px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-1">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
            </Link>
            <div class="border-t border-gray-200 w-10/12 mx-auto"></div>
            <Link :href="route('logout')" method="post" as="button" @click="closeMenu"
                class="w-full text-left font-medium px-4 py-1.5 text-sm text-red-400 flex items-center hover:text-red-600"
                role="menuitem" tabindex="-1" id="user-menu-item-2">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
            </Link>
        </div>
    </div>

</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const page = usePage();

const user = computed(() => page.props.auth.user);

const menuOpen = ref(false);

const menuWrapper = ref(null);

const avatarUrl = computed(() => user.value?.avatar);

const toggleMenu = () => {
    menuOpen.value = !menuOpen.value;
};

const handleClickOutside = (event) => {
    if (menuWrapper.value &&
        !menuWrapper.value.contains(event.target) &&
        event.target.id !== 'user-menu-button') {
        menuOpen.value = false;
    }
};

const closeMenu = () => {
    menuOpen.value = false;
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
