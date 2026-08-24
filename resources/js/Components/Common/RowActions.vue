<script setup>
import { EllipsisIcon } from '@lucide/vue'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

defineProps({
    /** [{ label, icon, variant: 'default' | 'destructive', onSelect }] */
    actions: {
        type: Array,
        default: () => [],
    },
    /** Names the trigger for screen readers, e.g. `Actions for Ada Lovelace` */
    label: {
        type: String,
        default: 'Actions',
    },
})
</script>

<template>
    <!-- Nothing renders when a row has no permitted actions, so the column stays
         a single position on every row instead of shifting with the count. -->
    <div v-if="actions.length" class="flex justify-end">
        <DropdownMenu>
            <DropdownMenuTrigger
                :aria-label="label"
                class="text-muted-foreground hover:bg-muted hover:text-foreground focus-visible:outline-ring cursor-pointer rounded-md p-1.5 transition-colors focus-visible:outline-2">
                <EllipsisIcon class="h-3.5 w-3.5" aria-hidden="true" />
            </DropdownMenuTrigger>

            <!-- shadcn sizes the panel to the trigger, which here is a 26px button. -->
            <DropdownMenuContent align="end" class="w-44">
                <DropdownMenuItem
                    v-for="action in actions"
                    :key="action.label"
                    :variant="action.variant || 'default'"
                    class="cursor-pointer"
                    @select="action.onSelect">
                    <component :is="action.icon" v-if="action.icon" aria-hidden="true" />
                    {{ action.label }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
