<template>

    <Head title="Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-50/20 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Enhanced header section with date -->
            <div class="mb-10 pb-6 border-b border-gray-200">
                <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">Welcome back, {{ userName }}! 👋</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">{{ formattedDate }}</p>
            </div>

            <!-- Stats Grid with sparkline graphs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
                <StatCard v-for="stat in stats" :key="stat.title" v-bind="stat"
                    class="hover:shadow-md transition-shadow duration-200">
                    <!-- Add sparkline graph to StatCard -->
                    <svg class="w-20 h-8 mt-2" v-if="stat.sparkline">
                        <path :d="stat.sparkline" stroke="currentColor" fill="none" stroke-width="2"
                            class="text-blue-500" stroke-linecap="round" />
                    </svg>
                </StatCard>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activity with empty state -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                    <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                        <button class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                            View all →
                        </button>
                    </div>
                    <div class="p-4 space-y-4">
                        <template v-if="recentActivity.length">
                            <div v-for="activity in recentActivity" :key="activity.id"
                                class="flex items-start gap-4 p-3 hover:bg-gray-50 rounded-lg transition-all duration-200">
                                <!-- Enhanced icon container -->
                                <div :class="[
                                    'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center',
                                    activity.iconBg
                                ]">
                                    <component :is="activity.icon" class="w-6 h-6" :class="activity.iconColor" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ activity.title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ activity.time }}</p>
                                </div>
                            </div>
                        </template>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-400 text-sm">No recent activity</p>
                        </div>
                    </div>
                </div>

                <!-- Team Members with status indicators -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow border border-gray-100">
                    <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Team Members</h2>
                        <button class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                            View all →
                        </button>
                    </div>

                    <div class="p-4 space-y-4">
                        <div v-for="member in teamMembers" :key="member.id"
                            class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg transition-all duration-200">
                            <div class="relative">
                                <img :src="member.avatar" :alt="member.name"
                                    class="w-10 h-10 rounded-full ring-2 ring-white shadow-sm">
                                <div v-if="member.online"
                                    class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white">
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ member.name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ member.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions with hover states -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
                <button v-for="action in quickActions" :key="action.label" class="flex items-center gap-3 p-4 bg-white rounded-xl shadow-md hover:shadow-lg transition-all
                   hover:scale-[1.02] border border-gray-100 hover:bg-gray-50">
                    <component :is="action.icon" class="w-6 h-6 text-blue-600" />
                    <span class="text-sm font-medium text-gray-700">{{ action.label }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import Default from '../Layouts/Default.vue'
import { ChevronRightIcon } from '@heroicons/vue/24/outline'

defineOptions({
    layout: Default
})

const page = usePage()
const userName = computed(() => page.props.auth.user?.name || 'User')

const formattedDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        month: 'long',
        day: 'numeric'
    })
})

const stats = ref([
    {
        title: 'Total Users',
        value: '1,234',
        change: '+12%',
        trending: 'up',
        icon: 'UserIcon',
        color: 'blue',
        sparkline: 'M0 16 L4 10 L8 14 L12 8 L16 12 L20 6 L24 10'
    },
    {
        title: 'Active Projects',
        value: '23',
        change: '+3',
        trending: 'up',
        icon: 'FolderIcon',
        color: 'green',
        sparkline: 'M0 16 L4 10 L8 14 L12 8 L16 12 L20 6 L24 10'
    },
    {
        title: 'Completion Rate',
        value: '92%',
        change: '+5%',
        trending: 'up',
        icon: 'ChartIcon',
        color: 'purple',
        sparkline: 'M0 16 L4 10 L8 14 L12 8 L16 12 L20 6 L24 10'
    },
    {
        title: 'Total Revenue',
        value: '$12,345',
        change: '+8%',
        trending: 'up',
        icon: 'CurrencyDollarIcon',
        color: 'yellow',
        sparkline: 'M0 16 L4 10 L8 14 L12 8 L16 12 L20 6 L24 10'
    }
])

const recentActivity = ref([
    {
        id: 1,
        title: 'Project "Sunset" completed',
        time: '2 hours ago',
        icon: 'CheckCircleIcon',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600'
    },
    {
        id: 2,
        title: 'New client onboarded',
        time: '4 hours ago',
        icon: 'UserPlusIcon',
        iconBg: 'bg-blue-100',
        iconColor: 'text-blue-600'
    }
])

const teamMembers = ref([
    {
        id: 1,
        name: 'Sarah Johnson',
        role: 'Frontend Developer',
        avatar: '/images/avatar-1.jpg',
        online: true
    },
    {
        id: 2,
        name: 'Michael Chen',
        role: 'Backend Developer',
        avatar: '/images/avatar-2.jpg',
        online: false
    }
])

const quickActions = [
    {
        label: 'New Project',
        icon: 'PlusIcon'
    },
    {
        label: 'Upload Files',
        icon: 'UploadIcon'
    },
    {
        label: 'Add Member',
        icon: 'UserPlusIcon'
    },
    {
        label: 'Generate Report',
        icon: 'DocumentIcon'
    }
]
</script>
