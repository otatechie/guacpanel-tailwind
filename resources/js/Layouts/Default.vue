<template>
    <div class="min-h-full flex">
        <!-- Sidebar - Overlay on mobile, push on desktop -->
        <NavSidebarDesktop
            data-sidebar
            :class="[
                'h-screen fixed left-0 top-16 w-64 transform transition-all duration-300 ease-in-out z-50',
                // Desktop: Push content
                'md:z-30',
                // Mobile: Overlay with transform
                isSidebarOpen ? 'translate-x-0' : '-translate-x-64'
            ]"
            @close="closeSidebar"
        ></NavSidebarDesktop>

        <!-- Main content area -->
        <div class="flex-1 min-h-screen">
            <!-- Main header -->
            <div class="bg-gray-50 border-b border-gray-200 fixed w-full top-0 z-40 shadow-xs">
                <div class="flex items-center h-16 px-4 sm:px-6">
                    <!-- Logo and hamburger section -->
                    <div class="flex items-center gap-4">
                        <!-- Hamburger menu -->

                        <!-- Logo -->
                        <Link href="/" class="text-gray-800 text-xl font-bold">
                        Your Logo
                        </Link>
                        <button type="button" class="text-white hover:text-gray-200 border border-blue-900 rounded-full p-1" @click="toggleSidebar"
                            data-menu-button>
                            <span class="sr-only">Open sidebar</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-blue-900">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Search bar -->
                    <div class="flex-1 px-4">
                    </div>

                    <!-- Right side icons -->
                    <div class="flex items-center gap-4">
                        <button type="button" class="text-white hover:text-gray-200" title="Help">
                            <span class="sr-only">Help</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="#000" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                        </button>

                        <!-- Profile/Login section -->
                        <NavProfile v-if="user" :user="user"></NavProfile>
                        <Link v-else href="/login" class="text-white hover:text-gray-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                            <path fill-rule="evenodd"
                                d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                        </svg>
                        Login
                        </Link>

                    </div>
                </div>
            </div>

            <!-- Main content wrapper -->
            <div :class="[
                'transition-all duration-300 ease-in-out',
                // Push content on desktop when sidebar is open
                { 'md:ml-64': isSidebarOpen },
                { 'md:ml-0': !isSidebarOpen },
                // Don't push content on mobile
                'ml-0'
            ]">
                <!-- Main content -->
                <div class="bg-gray-50 pb-8 mt-16 w-full">
                    <main class="min-h-screen pt-8">
                        <FlashMessage></FlashMessage>
                        <slot></slot>
                    </main>
                </div>

                <!-- Footer -->
                <footer class="relative bg-gradient-to-b from-indigo-900 to-indigo-950 text-gray-300 py-6">
                    <div class="absolute inset-0 opacity-5">
                        <div class="absolute inset-0"
                            style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                        </div>
                    </div>

                    <div class="container mx-auto px-4 max-w-7xl relative">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-center space-x-6">
                                <a href="#" class="text-gray-400 hover:text-indigo-400">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="text-gray-400 hover:text-indigo-400 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 underline">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="text-gray-400 hover:text-indigo-400 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 underline">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="text-gray-400 hover:text-indigo-400 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 underline">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="text-gray-400 hover:text-indigo-400 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 underline">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                                    </svg>
                                </a>
                            </div>
                            <div class="text-xs font-medium text-gray-500 hover:text-gray-400 transition-colors duration-200">
                                Copyright © 2010-2025 Swappa, LLC
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Mobile overlay backdrop -->
        <div
            v-if="isSidebarOpen && isMobile()"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity z-40"
            @click="closeSidebar"
        ></div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavSidebarDesktop from '../Shared/NavSidebarDesktop.vue';
import NavProfile from '../Shared/NavProfile.vue';
import NavSearch from '../Shared/NavSearch.vue';
import FlashMessage from '../Shared/FlashMessage.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

// Set sidebar to be open by default
const isSidebarOpen = ref(true);

// Initialize on component mount
onMounted(() => {
    // Force sidebar to be open by default
    isSidebarOpen.value = true;
    localStorage.setItem('sidebarOpen', 'true');
});

const isMobile = () => window.innerWidth < 768;

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    localStorage.setItem('sidebarOpen', isSidebarOpen.value.toString());
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
    localStorage.setItem('sidebarOpen', 'false');
};

const handleClickAway = (event) => {
    const sidebar = document.querySelector('[data-sidebar]');
    const menuButton = document.querySelector('[data-menu-button]');

    if (!sidebar || !menuButton) return;

    if (!sidebar.contains(event.target) && !menuButton.contains(event.target)) {
        closeSidebar();
    }
};

onUnmounted(() => {
    document.removeEventListener('click', handleClickAway);
});
</script>
