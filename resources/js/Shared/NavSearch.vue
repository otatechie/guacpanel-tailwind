<template>
    <div class="relative">
        <!-- Mobile Search Toggle -->
        <button @click="toggleSearch" class="md:hidden text-gray-400 hover:text-gray-600 transition"
            aria-label="Toggle search">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" />
            </svg>
        </button>

        <!-- Search Form -->
        <Transition enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0">
            <form :class="{ 'hidden md:block': !isOpen }" @submit.prevent="handleSearch"
                class="absolute md:relative right-0 top-full md:top-0 mt-2 md:mt-0 z-50" v-click-outside="closeSearch">
                <div class="relative w-60 md:w-80 lg:w-96">
                    <!-- Search Icon -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" />
                        </svg>
                    </span>

                    <!-- Search Input -->
                    <input v-model="query" type="search" placeholder="Search phones, laptops..."
                        class="w-full rounded-md border-gray-200 pl-10 text-sm focus:border-primary-500 focus:ring-primary-500 bg-white shadow-sm"
                        @keydown.esc="closeSearch" ref="searchInput">

                    <!-- Loading Spinner -->
                    <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div v-if="query.trim() && (searchResults.length > 0 || !isLoading)"
                        class="absolute mt-2 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-96 overflow-y-auto">
                        <!-- No Results Message with Image -->
                        <div v-if="!isLoading && searchResults.length === 0" class="p-6 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 flex items-center justify-center text-gray-400">
                                <i class="pi pi-search text-4xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium">No results found for "{{ query }}"</p>
                            <p class="text-sm text-gray-500 mt-1">Try different keywords or check spelling</p>
                        </div>

                        <!-- Results List with Better Image Handling -->
                        <div v-else v-for="result in searchResults" :key="result.id"
                            class="p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                            @click="goToResult(result)">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                    <img v-if="result.images?.[0]" :src="result.images[0]"
                                        :alt="result.phone_brand?.name" class="w-full h-full object-cover"
                                        @error="handleImageError">
                                    <div v-else
                                        class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="pi pi-mobile text-xl"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 truncate">
                                        {{ result.phone_brand?.name }} {{ result.phone_variant?.name }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-sm font-medium text-indigo-600">${{ result.price }}</span>
                                        <span class="text-gray-300">•</span>
                                        <span class="text-sm text-gray-600 capitalize">{{ result.condition }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 truncate">
                                        {{ result.storage_capacity }}GB • {{ result.color }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </Transition>
    </div>
 </template>

 <script setup>
 import { ref, onMounted, nextTick, watch } from 'vue'
 import { router } from '@inertiajs/vue3'
 import debounce from 'lodash/debounce'
 import axios from 'axios'

 const query = ref('')
 const isOpen = ref(false)
 const searchInput = ref(null)
 const searchResults = ref([])
 const isLoading = ref(false)

 const toggleSearch = () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        nextTick(() => searchInput.value?.focus())
    }
 }

 const performSearch = debounce(async (value) => {
    if (!value.trim()) {
        searchResults.value = []
        return
    }

    isLoading.value = true
    try {
        const response = await axios.get('/search', {
            params: { search: value },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                // Include CSRF token if you're using Laravel's CSRF protection
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        searchResults.value = response.data.data
    } catch (error) {
        console.error('Search failed:', error)
        searchResults.value = []
        // Handle specific error cases
        if (error.response?.status === 401) {
            // Optionally redirect to login or show authentication error
            console.log('User not authenticated')
        }
    } finally {
        isLoading.value = false
    }
 }, 300)

 watch(query, (newValue) => {
    performSearch(newValue)
 })

 const handleSearch = () => {
    if (!query.value.trim()) return

    router.get('/search', {
        search: query.value
    })
 }

 const goToResult = (result) => {
    router.visit(`/mobile-device/${result.id}`)
    closeSearch()
 }

 const closeSearch = () => {
    isOpen.value = false
    query.value = ''
    searchResults.value = []
 }

 const handleImageError = (e) => {
    e.target.classList.add('hidden')
    e.target.parentElement.querySelector('.pi').classList.remove('hidden')
 }

 const vClickOutside = {
    mounted(el, binding) {
        el._clickOutside = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value(event)
            }
        }
        document.addEventListener('click', el._clickOutside)
    },
    unmounted(el) {
        document.removeEventListener('click', el._clickOutside)
    }
 }
 </script>
