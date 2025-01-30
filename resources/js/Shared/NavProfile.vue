<template>

    <div class="relative ml-3" ref="menuWrapper">
        <div>
            <button type="button"
                class="relative flex max-w-xs items-center rounded-full  text-sm focus:outline-none  lg:rounded-md lg:p-2"
                id="user-menu-button" :aria-expanded="menuOpen.toString()" aria-haspopup="true" @click="toggleMenu"
                aria-label="User Menu">
                <span class="absolute -inset-1.5 lg:hidden"></span>
                <img class="h-6 w-6 rounded-full" :src="avatarUrl" alt="User Avatar" />
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
            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-gray-400 ring-opacity-5 focus:outline-none"
            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <Link :href="route('user.index')" @click="closeMenu"
                class="group px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-0">
            <div class="flex items-center w-full">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Your Account
            </div>
            </Link>
            <Link :href="route('user.index')" @click="closeMenu"
                class="group px-4 py-1.5 text-sm text-gray-700 flex items-center hover:text-blue-600" role="menuitem"
                tabindex="-1" id="user-menu-item-0">
            <div class="flex items-center w-full">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Change Password
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
            2FA
            </Link>
            <div class="border-t border-gray-200 w-10/12 mx-auto"></div>
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

const avatarUrl = computed(() => {
    return `https://api.dicebear.com/9.x/initials/svg?seed=${encodeURIComponent(user.value.name)}&backgroundType=gradientLinear&scale=100&fontSize=50`;
});


const menuOpen = ref(false);

const menuWrapper = ref(null);

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
