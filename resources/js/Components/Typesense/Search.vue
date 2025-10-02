<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import SearchResults from './SearchResults.vue'
import FederatedSearch from './FederatedSearch.vue'

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    isMobile: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: 'Search...'
    }
})

const emit = defineEmits(['close'])

const typesenseApiKey = ref(null)
const hasValidApiKey = ref(false)
const isLoading = ref(true)
const searchQuery = ref('')
const showResults = ref(false)
const isMobileSearchActive = ref(false)
const federatedResults = ref([])
const isFederatedSearching = ref(false)

const fetchTypesenseApiKey = async () => {
    try {
        const response = await axios.get('/typesense/scoped-key')
        if (response?.data?.apiKey) {
            typesenseApiKey.value = response.data.apiKey
            hasValidApiKey.value = true
        }
    } catch {
        hasValidApiKey.value = false
    } finally {
        isLoading.value = false
    }
}

const closeOverlay = () => {
    showResults.value = false
    isMobileSearchActive.value = false
    searchQuery.value = ''
    emit('close')
}

const handleKeyDown = event => {
    if (event.key === 'Escape') {
        closeOverlay()
    }
}

const handleSearch = e => {
    searchQuery.value = e.target.value
}

const handleSearching = searching => {
    isFederatedSearching.value = searching
}

const handleSearchResults = results => {
    federatedResults.value = results
}

const handleFocus = () => {
    showResults.value = true
    if (props.isMobile) {
        isMobileSearchActive.value = true
    }
}

const handleBlur = () => {
    if (!props.isMobile) {
        setTimeout(() => {
            showResults.value = false
        }, 200)
    }
}

onMounted(async () => {
    await fetchTypesenseApiKey()
    if (props.isOpen) {
        document.addEventListener('keydown', handleKeyDown)
    }
})

watch(
    () => props.isOpen,
    isOpen => {
        if (isOpen) {
            document.addEventListener('keydown', handleKeyDown)
        } else {
            document.removeEventListener('keydown', handleKeyDown)
        }
    }
)
</script>

<template>
    <div class="search-component">
        <!-- Mobile Search Overlay -->
        <div
            v-if="isOpen && isMobile"
            role="dialog"
            aria-modal="true"
            aria-label="Search site"
            class="fixed inset-0 z-[60] bg-gray-900/50 dark:bg-gray-900/80">
            <div
                class="fixed inset-x-0 top-0 bg-white dark:bg-gray-800 p-4 shadow-lg z-[60]"
                @click.stop>
                <!-- Mobile Header -->
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Search</h2>
                    <button
                        aria-label="Close search"
                        class="p-1 rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300"
                        @click="closeOverlay">
                        <svg
                            class="w-5 h-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Search Results Component -->
                <SearchResults
                    :is-loading="isLoading"
                    :has-valid-api-key="hasValidApiKey"
                    :search-query="searchQuery"
                    :show-results="showResults"
                    :federated-results="federatedResults"
                    :is-federated-searching="isFederatedSearching"
                    :placeholder="placeholder"
                    @search="handleSearch"
                    @focus="handleFocus"
                    @blur="handleBlur" />
            </div>
        </div>

        <!-- Desktop Search -->
        <SearchResults
            v-else-if="!isMobile"
            :is-loading="isLoading"
            :has-valid-api-key="hasValidApiKey"
            :search-query="searchQuery"
            :show-results="showResults"
            :federated-results="federatedResults"
            :is-federated-searching="isFederatedSearching"
            :placeholder="placeholder"
            @search="handleSearch"
            @focus="handleFocus"
            @blur="handleBlur" />

        <!-- Federated Search Handler -->
        <FederatedSearch
            v-if="hasValidApiKey && typesenseApiKey"
            :search-query="searchQuery"
            :typesense-api-key="typesenseApiKey"
            @update:results="handleSearchResults"
            @searching="handleSearching" />
    </div>
</template>

<style>
.search-component {
    position: relative;
    width: 100%;
}
</style>
