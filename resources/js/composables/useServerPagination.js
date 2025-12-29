import { ref, watch, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'

export function useServerPagination(options) {
    const columnFilters = ref([])
    const sorting = ref([])
    const globalFilter = ref('')

    // Store watchers and timers for cleanup
    let globalSearchTimer = null
    const watchers = []

    const watchColumnFilters = () => {
        const stopWatch = watch(
            columnFilters,
            newFilters => {
                const filterParams = {}
                newFilters.forEach(filter => {
                    if (
                        filter.value !== undefined &&
                        filter.value !== null &&
                        filter.value !== ''
                    ) {
                        filterParams[filter.id] = filter.value
                    }
                })

                const queryParams = {
                    ...options.filters,
                    ...filterParams,
                    page: 1,
                    per_page: options.filters?.per_page || options.pagination.per_page,
                }

                router.get(options.routeUrl, queryParams, {
                    preserveState: true,
                    preserveScroll: true,
                })
            },
            { deep: true }
        )
        watchers.push(stopWatch)
    }

    const watchSorting = () => {
        const stopWatch = watch(
            sorting,
            newSorting => {
                if (newSorting.length === 0) return

                const sort = newSorting[0]
                const queryParams = {
                    ...options.filters,
                    sort_by: sort.id,
                    sort_dir: sort.desc ? 'desc' : 'asc',
                    page: 1,
                    per_page: options.filters?.per_page || options.pagination.per_page,
                }

                router.get(options.routeUrl, queryParams, {
                    preserveState: true,
                    preserveScroll: true,
                })
            },
            { deep: true }
        )
        watchers.push(stopWatch)
    }

    const watchGlobalSearch = () => {
        const stopWatch = watch(
            globalFilter,
            newSearch => {
                if (newSearch === undefined || newSearch === null) return

                // Clear existing timer
                if (globalSearchTimer) {
                    clearTimeout(globalSearchTimer)
                }

                globalSearchTimer = setTimeout(() => {
                    const queryParams = {
                        ...options.filters,
                        search: newSearch,
                        page: 1,
                        per_page: options.filters?.per_page || options.pagination.per_page,
                    }

                    router.get(options.routeUrl, queryParams, {
                        preserveState: true,
                        preserveScroll: true,
                    })
                }, 300)
            },
            { deep: true }
        )
        watchers.push(stopWatch)
    }

    const cleanup = () => {
        // Stop all watchers
        watchers.forEach(stopWatch => stopWatch())
        watchers.length = 0

        // Clear any pending timer
        if (globalSearchTimer) {
            clearTimeout(globalSearchTimer)
            globalSearchTimer = null
        }
    }

    const init = () => {
        watchColumnFilters()
        watchSorting()
        watchGlobalSearch()
    }

    // Cleanup on component unmount
    onBeforeUnmount(() => {
        cleanup()
    })

    return {
        columnFilters,
        sorting,
        globalFilter,
        init,
        cleanup, // Export cleanup in case manual cleanup is needed
    }
}
