import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/* One implementation. There were three, and they disagreed: the sidebar's took
   arrays, the layout's returned false for a missing name, the palette's returned
   true. Union of the useful behaviour — a falsy name means "no gate", an array
   means "any of these". */
export function usePermissions() {
    const page = usePage()
    const user = computed(() => page.props.auth?.user)
    const held = computed(() => user.value?.permissions ?? [])

    const hasPermission = permission => {
        if (!permission) return true
        const wanted = Array.isArray(permission) ? permission : [permission]
        return wanted.some(p => held.value.includes(p))
    }

    return { user, hasPermission }
}
