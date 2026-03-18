<script setup>
import { ref, watch, onMounted } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'
import SearchResults from './SearchResults.vue'
import FederatedSearch from './FederatedSearch.vue'

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    isMobile: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: 'Search...',
    },
})

const emit = defineEmits(['close'])

const typesenseApiKey = ref(null)
const hasValidApiKey = ref(false)
const isLoading = ref(true)
const searchQuery = ref('')
const showResults = ref(false)
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
    searchQuery.value = ''
    emit('close')
}

const handleKeyDown = event => {
    if (event.key === 'Escape') closeOverlay()
}

const handleSearch = e => { searchQuery.value = e.target.value }
const handleSearching = searching => { isFederatedSearching.value = searching }
const handleSearchResults = results => { federatedResults.value = results }

const handleFocus = () => { showResults.value = true }
const handleBlur = () => {
    if (!props.isMobile) {
        setTimeout(() => { showResults.value = false }, 200)
    }
}

onMounted(async () => {
    await fetchTypesenseApiKey()
    if (props.isOpen) document.addEventListener('keydown', handleKeyDown)
})

watch(() => props.isOpen, isOpen => {
    if (isOpen) document.addEventListener('keydown', handleKeyDown)
    else document.removeEventListener('keydown', handleKeyDown)
})
</script>

<template>
    <div class="relative w-full">
        <!-- Mobile Search Overlay -->
        <div
            v-if="isOpen && isMobile"
            role="dialog"
            aria-modal="true"
            aria-label="Search site"
            class="fixed inset-0 z-[60] bg-black/40">
            <div
                class="fixed inset-x-0 top-0 z-[60] border-b border-(--card-border) bg-(--color-surface) p-4 shadow-lg"
                @click.stop>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-medium text-(--color-text)">Search</h2>
                    <button
                        aria-label="Close search"
                        class="nav-bar-btn"
                        @click="closeOverlay">
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>

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

        <FederatedSearch
            v-if="hasValidApiKey && typesenseApiKey"
            :search-query="searchQuery"
            :typesense-api-key="typesenseApiKey"
            @update:results="handleSearchResults"
            @searching="handleSearching" />
    </div>
</template>
