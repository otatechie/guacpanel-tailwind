import { describe, expect, it, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'

const success = vi.fn()
const error = vi.fn()
const warning = vi.fn()
const info = vi.fn()
const dismiss = vi.fn()

vi.mock('vue-sonner', () => ({
    toast: { success, error, warning, info, dismiss },
    Toaster: { name: 'Toaster', template: '<div data-sonner />' },
}))

const { useToast } = await import('@js/composables/useToast')

// Contract tests — assert the app-facing toast API from docs/ui-contract.md,
// not vue-sonner internals. These must keep passing if the toast lib is swapped.
describe('useToast contract', () => {
    beforeEach(() => vi.clearAllMocks())

    it('routes each type to its own channel', () => {
        const toast = useToast()

        toast.success('saved')
        toast.error('boom')
        toast.warning('careful')
        toast.info('fyi')

        expect(success).toHaveBeenCalledWith('saved', {})
        expect(error).toHaveBeenCalledWith('boom', {})
        expect(warning).toHaveBeenCalledWith('careful', {})
        expect(info).toHaveBeenCalledWith('fyi', {})
    })

    it('treats danger as error, since Laravel flash keys use both', () => {
        useToast().show('nope', 'danger')

        expect(error).toHaveBeenCalledWith('nope', {})
    })

    it('falls back to success for an unknown type', () => {
        useToast().show('hello', 'nonsense')

        expect(success).toHaveBeenCalledWith('hello', {})
    })

    it('ignores an empty message rather than showing a blank toast', () => {
        const toast = useToast()

        toast.show('')
        toast.show(null)
        toast.success(undefined)

        expect(success).not.toHaveBeenCalled()
        expect(error).not.toHaveBeenCalled()
    })

    it('forwards dismiss', () => {
        useToast().dismiss('abc')

        expect(dismiss).toHaveBeenCalledWith('abc')
    })
})

describe('Toaster wrapper contract', () => {
    it('renders and clears the fixed header by default', async () => {
        const Toaster = (await import('@js/Components/Toaster.vue')).default
        const wrapper = mount(Toaster)

        expect(wrapper.findComponent({ name: 'Toaster' }).props('offset')).toBe('72px')
    })

    it('accepts a custom offset for layouts with no header', async () => {
        const Toaster = (await import('@js/Components/Toaster.vue')).default
        const wrapper = mount(Toaster, { props: { offset: '16px' } })

        expect(wrapper.findComponent({ name: 'Toaster' }).props('offset')).toBe('16px')
    })
})
