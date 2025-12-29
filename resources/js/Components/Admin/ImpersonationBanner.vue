<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const isImpersonating = computed(() => !!page.props.impersonation?.active)
const impersonatorName = computed(() => page.props.impersonation?.impersonator_name)
const currentUserName = computed(() => page.props.auth?.user?.name)

const stopImpersonation = () => {
    router.post(route('admin.user.impersonate.stop'))
}
</script>

<template>
    <div v-if="isImpersonating" class="impersonation-banner">
        <div class="impersonation-content">
            <svg
                class="impersonation-icon"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            <span class="impersonation-text">
                <strong>{{ impersonatorName }}</strong>
                is viewing as
                <strong>{{ currentUserName }}</strong>
            </span>
            <button class="impersonation-button" @click="stopImpersonation">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Exit
            </button>
        </div>
    </div>
</template>

<style scoped>
.impersonation-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 0.75rem 1rem;
    box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
}

.impersonation-content {
    max-width: 80rem;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.impersonation-icon {
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
}

.impersonation-text {
    font-size: 0.875rem;
}

.impersonation-text strong {
    font-weight: 600;
}

.impersonation-button {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #d97706;
    background: white;
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.15s ease;
}

.impersonation-button:hover {
    background: #fef3c7;
    transform: translateY(-1px);
}

.impersonation-button svg {
    width: 1rem;
    height: 1rem;
}
</style>
