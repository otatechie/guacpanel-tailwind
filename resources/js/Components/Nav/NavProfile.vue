<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { setTheme, getCurrentThemePreference } from '@js/utils/darkMode'
import { colors, applyThemeColor, DEFAULT_THEME_COLOR } from '@js/utils/themeInit'
import Popover from '@js/Components/Popover.vue'
import {
    CheckIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    CircleUserIcon,
    LogOutIcon,
    MonitorIcon,
    MoonIcon,
    SunIcon,
} from '@lucide/vue'
const page = usePage()
const user = computed(() => page.props.auth.user)
const avatarUrl = computed(() => user.value?.avatar)
const userName = computed(() => user.value?.name || '')
const userEmail = computed(() => user.value?.email || '')

/* All three options are shown rather than a button cycling to the next one.
   A cycling control names only where you are going, so the mode you are in now
   has to be recalled instead of read. */
const THEME_OPTIONS = [
    { value: 'light', label: 'Light', icon: SunIcon },
    { value: 'dark', label: 'Dark', icon: MoonIcon },
    { value: 'system', label: 'System', icon: MonitorIcon },
]

const menuOpen = ref(false)
const signOut = () => {
    menuOpen.value = false
    router.post(route('logout'))
}

const themePreference = ref(getCurrentThemePreference())
const selectTheme = preference => {
    setTheme(preference)
    themePreference.value = preference
}

/* Read only — `initializeTheme()` already applied the saved accent at boot, so
   this just reflects it. The default has to match that one or the check marks a
   swatch the app is not actually using. */
const selectedColor = ref(localStorage.getItem('theme-color') || DEFAULT_THEME_COLOR)
const updateThemeColor = color => {
    selectedColor.value = color
    localStorage.setItem('theme-color', color)
    applyThemeColor(color)
}

/* The OS can flip the effective theme under 'system' without us touching it. */
const syncThemePreference = () => {
    themePreference.value = getCurrentThemePreference()
}

onMounted(() => window.addEventListener('themeChanged', syncThemePreference))
onBeforeUnmount(() => window.removeEventListener('themeChanged', syncThemePreference))
</script>

<template>
    <Popover v-model:open="menuOpen" align="end" width="w-60">
        <template #trigger>
            <button
                type="button"
                class="hover:bg-muted focus-visible:ring-ring flex h-9 cursor-pointer items-center gap-1.5 rounded-md px-1.5 transition-colors focus-visible:ring-2 focus-visible:outline-none"
                aria-label="Account menu">
                <img :src="avatarUrl" :alt="userName" class="size-6 shrink-0 rounded-full" />
                <ChevronDownIcon
                    class="text-muted-foreground hidden size-3.5 opacity-60 lg:block"
                    aria-hidden="true" />
            </button>
        </template>

        <!-- Identity. The chevron marks this as somewhere you go. -->
        <div class="p-1">
            <Link
                :href="route('user.index')"
                class="hover:bg-muted focus-visible:ring-ring flex w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-1.5 text-sm transition-colors focus-visible:ring-2 focus-visible:outline-none"
                @click="menuOpen = false">
                <CircleUserIcon class="text-muted-foreground size-4 shrink-0" />
                <span class="min-w-0 flex-1 text-left">
                    <span class="text-foreground block truncate font-medium capitalize">
                        {{ userName }}
                    </span>
                    <span v-if="userEmail" class="text-muted-foreground block truncate text-xs">
                        {{ userEmail }}
                    </span>
                </span>
                <ChevronRightIcon
                    class="text-muted-foreground size-4 shrink-0"
                    aria-hidden="true" />
            </Link>
        </div>

        <div class="border-border border-t"></div>

        <div class="space-y-2 px-3 py-2.5">
            <div
                class="bg-muted flex gap-0.5 rounded-md p-0.5"
                role="radiogroup"
                aria-label="Theme">
                <button
                    v-for="option in THEME_OPTIONS"
                    :key="option.value"
                    type="button"
                    role="radio"
                    :aria-checked="themePreference === option.value"
                    class="focus-visible:ring-ring flex flex-1 cursor-pointer items-center justify-center gap-1 rounded px-1 py-0.5 text-[11px] transition-colors focus-visible:ring-2 focus-visible:outline-none"
                    :class="
                        themePreference === option.value
                            ? 'bg-card text-foreground font-medium shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    @click="selectTheme(option.value)">
                    <component :is="option.icon" class="size-3" aria-hidden="true" />
                    {{ option.label }}
                </button>
            </div>

            <div class="flex items-center justify-between gap-2">
                <span id="accent-heading" class="text-muted-foreground text-xs">Accent</span>
                <span
                    class="flex items-center gap-1.5"
                    role="radiogroup"
                    aria-labelledby="accent-heading">
                    <button
                        v-for="color in colors"
                        :key="color.value"
                        type="button"
                        role="radio"
                        :aria-checked="selectedColor === color.value"
                        :aria-label="color.name"
                        class="focus-visible:ring-ring flex size-4 cursor-pointer items-center justify-center rounded-full transition-transform hover:scale-110 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                        :style="{
                            background: `linear-gradient(135deg, ${color.gradientFrom}, ${color.gradientTo})`,
                        }"
                        @click="updateThemeColor(color.value)">
                        <!-- The only selection cue, so it also has to be the
                             non-colour one. A ring as well read as a bullseye and
                             made this swatch sit larger than the rest of the row. -->
                        <CheckIcon
                            v-if="selectedColor === color.value"
                            class="size-2.5 text-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.5)]"
                            :stroke-width="3"
                            aria-hidden="true" />
                    </button>
                </span>
            </div>
        </div>

        <div class="border-border border-t"></div>

        <!-- Sign out. Not red: it is routine and reversible, and red kept for
             genuinely destructive actions keeps its meaning. -->
        <div class="p-1">
            <button
                type="button"
                class="text-foreground hover:bg-muted focus-visible:ring-ring flex w-full cursor-pointer items-center gap-2.5 rounded-md px-2.5 py-1.5 text-left text-sm transition-colors focus-visible:ring-2 focus-visible:outline-none"
                @click="signOut">
                <LogOutIcon class="text-muted-foreground size-4" />
                Sign out
            </button>
        </div>
    </Popover>
</template>
