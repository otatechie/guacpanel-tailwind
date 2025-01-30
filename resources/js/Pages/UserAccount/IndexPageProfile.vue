<template>

    <Head title="Profile" />

    <div class="max-w-5xl mx-auto">
        <div class="container-border p-6">
            <!-- Profile Card -->
            <div
                class="max-w-4xl mx-auto bg-white rounded-md shadow-md hover:shadow-md transition-shadow duration-200 p-8 mt-6 border border-indigo-100">
                <div class="flex items-center gap-8">
                    <!-- Profile Image -->
                    <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-indigo-100 shadow-sm">
                        <img :src="`https://api.dicebear.com/9.x/initials/svg?seed=${encodeURIComponent(user.name)}&backgroundType=gradientLinear&scale=100&fontSize=50`" :alt="`${user.name}'s profile`" class="w-full h-full object-cover">
                    </div>

                    <!-- Profile Info -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-semibold text-gray-800">{{ user.name }}</h1>
                        <p class="text-gray-500 mt-1 text-sm">Member since <span class="font-medium text-gray-700">{{ user.created_at_date }} ({{ user.created_at_human }})</span></p>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>MI, US</span>
                                <img src="us-flag.png" class="w-5 h-4" alt="US flag">
                            </span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-600">Profile code: U1Z163</span>
                        </div>
                        <div class="mt-3">
                            <span
                                class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-sm font-medium flex items-center gap-2 w-fit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Trusted Seller
                            </span>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="text-right">
                        <div class="flex items-center justify-end gap-2 mb-3">
                            <span class="text-lg font-medium">65 Reviews</span>
                            <span class="text-gray-300">•</span>
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <!-- Add 4 more stars here -->
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div
                                class="px-4 py-3 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors duration-200">
                                <div class="text-2xl font-semibold text-indigo-600">82</div>
                                <div class="text-sm text-indigo-600">Sold</div>
                            </div>
                            <div
                                class="px-4 py-3 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors duration-200">
                                <div class="text-2xl font-semibold text-indigo-600">38</div>
                                <div class="text-sm text-indigo-600">Bought</div>
                            </div>
                            <div
                                class="px-4 py-3 bg-indigo-50 rounded-md hover:bg-indigo-100 transition-colors duration-200">
                                <div class="text-2xl font-semibold text-indigo-600">2</div>
                                <div class="text-sm text-indigo-600">Active</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Listings Section -->
            <div class="mt-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Active Listings</h2>
                    <a href="#" class="link-hover underline text-gray-700">See all ({{
                        userListings.length }}) listings →</a>
                </div>

                <!-- Listing Items -->
                <div v-if="userListings && userListings.length > 0">
                    <div v-for="listing in userListings" :key="listing.id"
                        class="py-4 flex items-center relative">
                        <img :src="listing.media?.images?.[0]?.original_url || 'default-phone.jpg'"
                            :alt="listing?.phone_model?.name"
                            class="w-16 h-20 object-cover rounded-sm">
                        <div class="flex-1 ml-4">
                            <h3 class="font-medium text-gray-700">
                                {{ listing?.phone_brand?.name }}
                                {{ listing?.phone_variant?.name }}
                                {{ listing?.phone_model?.name }}
                            </h3>
                            <p class="text-gray-500 text-sm">
                                {{ listing.condition.charAt(0).toUpperCase() + listing.condition.slice(1) }} •
                                {{ listing.color.charAt(0).toUpperCase() + listing.color.slice(1) }} •
                                {{ listing.storage_capacity >= 1024
                                    ? (listing.storage_capacity / 1024) + ' TB'
                                    : listing.storage_capacity + ' GB'
                                }}
                                {{ listing.phone_brand.name.toLowerCase() === 'apple'
                                    ? ` • ${listing.battery_health}% Battery`
                                    : ''
                                }}
                                {{ listing.ram ? ` • ${listing.ram}GB RAM` : '' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-semibold text-indigo-600">${{ listing.price }}</p>
                        </div>
                        <div class="border-b border-gray-200 w-full absolute bottom-0 left-0"></div>
                    </div>
                </div>
                <div v-else class="text-center py-4 text-gray-500">
                    No active listings found
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Default from '../../Layouts/Default.vue';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: Default,
})

const props = defineProps({
    user: {
        type: Object,
        required: true,
        default: () => ({})
    },
    userListings: {
        type: Array,
        required: true,
        default: () => []
    }
});

console.log('User:', props.user);
console.log('UserListings:', props.userListings);
</script>
