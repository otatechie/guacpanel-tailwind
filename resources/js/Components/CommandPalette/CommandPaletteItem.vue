<script setup>
import { computed } from 'vue'
import {
    HomeIcon,
    ChartBarIcon,
    BellIcon,
    Cog6ToothIcon,
    ChartBarSquareIcon,
    SwatchIcon,
    UsersIcon,
    CircleStackIcon,
    ShieldCheckIcon,
    ClockIcon,
    LockClosedIcon,
    ComputerDesktopIcon,
    HeartIcon,
    MoonIcon,
    UserIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    selected: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['select'])

/**
 * Map icon names to Heroicon components
 */
const iconComponents = {
    home: HomeIcon,
    chart: ChartBarIcon,
    bell: BellIcon,
    cog: Cog6ToothIcon,
    activity: ChartBarSquareIcon,
    palette: SwatchIcon,
    users: UsersIcon,
    database: CircleStackIcon,
    shield: ShieldCheckIcon,
    history: ClockIcon,
    lock: LockClosedIcon,
    monitor: ComputerDesktopIcon,
    heart: HeartIcon,
    moon: MoonIcon,
    user: UserIcon,
    logout: ArrowRightOnRectangleIcon,
    search: MagnifyingGlassIcon,
}

const IconComponent = computed(() => iconComponents[props.item.icon] || MagnifyingGlassIcon)

const handleClick = () => {
    emit('select', props.item)
}
</script>

<template>
    <div class="command-item" :class="{ selected }" role="option" :aria-selected="selected" @click="handleClick">
        <component :is="IconComponent" class="command-item-icon" aria-hidden="true" />

        <div class="command-item-content">
            <div class="command-item-label">{{ item.name }}</div>
            <div v-if="item.subtitle" class="command-item-subtitle">{{ item.subtitle }}</div>
        </div>

        <span v-if="item.type === 'action'" class="command-item-badge">Action</span>
        <span v-else-if="item.type === 'page'" class="command-item-badge">Page</span>
    </div>
</template>
