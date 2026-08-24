import { toast as sonner } from 'vue-sonner'

/**
 * App-facing toast API. Behaviour lives here rather than in the Toaster wrapper
 * so callers get a plain function instead of having to reach for a component
 * instance; see docs/ui-contract.md.
 *
 * `danger` is accepted alongside `error` because Laravel flash keys and the
 * Alert component both use it.
 */
const TYPES = {
    success: sonner.success,
    error: sonner.error,
    danger: sonner.error,
    warning: sonner.warning,
    info: sonner.info,
}

export function useToast() {
    const show = (message, type = 'success', options = {}) => {
        if (!message) return
        const fn = TYPES[type] ?? sonner.success
        return fn(message, options)
    }

    return {
        show,
        success: (message, options) => show(message, 'success', options),
        error: (message, options) => show(message, 'error', options),
        warning: (message, options) => show(message, 'warning', options),
        info: (message, options) => show(message, 'info', options),
        dismiss: id => sonner.dismiss(id),
    }
}
