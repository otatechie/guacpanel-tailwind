import { ref } from 'vue'

/* Module-level, not per-caller: the palette mounts once in the layout and the
   header trigger has to reach the same instance. Wrappers stay dumb — the
   behaviour lives here, per docs/ui-contract.md. */
const isOpen = ref(false)

export function useCommandPalette() {
    return {
        isOpen,
        // Cmd+K is a reflex, and it gets struck twice as often as not. It always
        // lands open; Escape and the ESC badge are how it closes.
        open: () => {
            isOpen.value = true
        },
        close: () => {
            isOpen.value = false
        },
    }
}
