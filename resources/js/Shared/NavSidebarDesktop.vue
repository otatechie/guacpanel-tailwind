<template>
    <div data-sidebar-content class="h-full flex flex-col bg-white shadow-lg transition-all duration-300 ease-in-out"
        @click.stop>
        <div class="px-3 py-3 border-b flex justify-between items-center bg-white sticky top-0 z-10">
            <h2 class="text-base font-semibold text-gray-700">Menu</h2>
            <button @click.stop="$emit('close')"
                class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors hidden md:block cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>
            <button @click.stop="$emit('close')"
                class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors md:hidden cursor-pointer">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto py-3 px-2">
            <div class="space-y-1">
                <template v-for="(section, sectionIndex) in navigationSections" :key="sectionIndex">
                    <div v-if="sectionIndex > 0" class="my-1.5 px-2">
                        <div class="h-px w-full bg-gray-100"></div>
                    </div>
                    <template v-for="(item, itemIndex) in section.items" :key="itemIndex">
                        <div v-if="item.type === 'divider'" class="my-1.5 px-2">
                            <div class="h-px w-full bg-gray-100"></div>
                        </div>
                        <div v-else-if="item.children" class="space-y-1">
                            <button @click="toggleDropdown(item)"
                                class="w-full flex items-center justify-between px-2.5 py-2 rounded-lg transition-all duration-200 ease-in-out text-gray-700 hover:text-gray-900 cursor-pointer">
                                <div class="flex items-center">
                                    <svg class="w-[18px] h-[18px] mr-2.5 text-gray-400"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" v-html="item.icon"></svg>
                                    <span class="text-sm font-medium">{{ item.name }}</span>
                                </div>
                                <svg :class="['w-4 h-4 transition-transform duration-200 text-gray-400', item.isOpen ? 'rotate-180' : '']"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-show="item.isOpen" class="pl-9 space-y-1">
                                <Link v-for="child in item.children" :key="child.name" :href="child.href"
                                    :class="['flex items-center px-1.5 py-1.5 rounded-lg transition-all duration-200 ease-in-out', child.active ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-900']">
                                <span class="text-sm">{{ child.name }}</span>
                                <span v-if="child.badge"
                                    class="ml-auto inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="child.badge.class">{{ child.badge.text }}</span>
                                </Link>
                            </div>
                        </div>
                        <Link v-else :href="item.href"
                            :class="['flex items-center px-2.5 py-2 rounded-lg transition-all duration-200 ease-in-out', item.active ? ' text-blue-600' : 'text-gray-700 hover:text-gray-900']">
                        <svg class="w-[18px] h-[18px] mr-2.5 transition-colors duration-200"
                            :class="item.active ? 'text-blue-600' : 'text-gray-400'" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            v-html="item.icon"></svg>
                        <span class="text-sm font-medium">{{ item.name }}</span>
                        <span v-if="item.badge"
                            class="ml-auto inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                            :class="item.badge.class">{{ item.badge.text }}</span>
                        </Link>
                    </template>
                </template>
            </div>
        </nav>

        <div class="px-3 py-3 border-t bg-white">
            <div class="flex items-center gap-2.5">
                <img src="/path-to-logo.png" alt="Logo" class="w-7 h-7 rounded-lg">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">Admin Panel</p>
                    <p class="text-xs text-gray-500">v2.0.0</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const navigationSections = ref([
    {
        items: [
            {
                name: 'Dashboard',
                href: route('home'),
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
                active: true
            },
            {
                type: 'divider'
            },
            {
                name: 'Analytics',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
                isOpen: false,
                children: [
                    { name: 'Overview', href: route('home'), badge: { text: 'Live', class: 'bg-blue-100 text-blue-600' } },
                    { name: 'Reports', href: route('home') },
                    { name: 'Statistics', href: route('home') }
                ]
            }
        ]
    },
    {
        items: [
            {
                name: 'User Management',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
                isOpen: false,
                children: [
                    { name: 'Users', href: route('home'), badge: { text: '50+', class: 'bg-blue-100 text-blue-600' } },
                    { name: 'Roles', href: route('home') },
                    { name: 'Permissions', href: route('home') }
                ]
            },
        ]
    },
    {
        items: [
            {
                name: 'Settings',
                icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                isOpen: false,
                children: [
                    { name: 'General', href: route('home') },
                    { name: 'Security', href: route('home'), badge: { text: 'Updated', class: 'bg-blue-100 text-blue-600' } },
                    { name: 'Backups', href: route('home') }
                ]
            },
        ]
    }
]);

const toggleDropdown = (item) => {
    item.isOpen = !item.isOpen;
};
</script>

<style scoped>
nav {
    scrollbar-width: thin;
    scrollbar-color: rgba(59, 130, 246, 0.5) transparent;
    height: calc(100vh - 8rem);
}

nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: transparent;
}

nav::-webkit-scrollbar-thumb {
    background-color: rgba(59, 130, 246, 0.5);
    border-radius: 2px;
}

.h-full {
    height: calc(100vh - 4rem);
}
</style>
