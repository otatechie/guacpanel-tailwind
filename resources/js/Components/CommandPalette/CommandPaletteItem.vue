<script setup>
import { computed } from 'vue'
import {
    BellIcon,
    ChartColumnBigIcon,
    ChartColumnIcon,
    ClockIcon,
    DatabaseIcon,
    HeartIcon,
    HouseIcon,
    LockIcon,
    LogOutIcon,
    MonitorIcon,
    MoonIcon,
    SearchIcon,
    SettingsIcon,
    ShieldCheckIcon,
    SwatchBookIcon,
    UserIcon,
    UsersIcon,
} from '@lucide/vue'
const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    selected: {
        type: Boolean,
        default: false,
    },
    /** Referenced by the input's aria-activedescendant. */
    id: {
        type: String,
        required: true,
    },
})

const emit = defineEmits(['select', 'activate'])

/**
 * Map icon names to Lucide components
 */
const iconComponents = {
    home: HouseIcon,
    chart: ChartColumnIcon,
    bell: BellIcon,
    cog: SettingsIcon,
    activity: ChartColumnBigIcon,
    palette: SwatchBookIcon,
    users: UsersIcon,
    database: DatabaseIcon,
    shield: ShieldCheckIcon,
    history: ClockIcon,
    lock: LockIcon,
    monitor: MonitorIcon,
    heart: HeartIcon,
    moon: MoonIcon,
    user: UserIcon,
    logout: LogOutIcon,
    search: SearchIcon,
}

const IconComponent = computed(() => iconComponents[props.item.icon] || SearchIcon)

const handleClick = () => {
    emit('select', props.item)
}
</script>

<template>
    <!-- Stays a non-tabbable `option`: in a combobox the input keeps focus and
         the arrow keys drive the list, so making these buttons would put two
         competing keyboard models in one widget. Hover drives the same
         selection the arrows do, rather than painting a second highlight. -->
    <div
        :id="id"
        class="command-item"
        :class="{ selected }"
        role="option"
        :aria-selected="selected"
        @mouseenter="emit('activate')"
        @click="handleClick">
        <component :is="IconComponent" class="command-item-icon" aria-hidden="true" />

        <div class="command-item-content">
            <div class="command-item-label">{{ item.name }}</div>
            <div v-if="item.subtitle" class="command-item-subtitle">{{ item.subtitle }}</div>
        </div>
    </div>
</template>
