import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'

vi.mock('@inertiajs/vue3', () => ({
    router: { visit: vi.fn() },
    usePage: () => ({ props: { auth: { user: { permissions: [] } } } }),
}))
vi.mock('axios', () => ({ default: { get: vi.fn(() => Promise.reject(new Error('no key'))) } }))
vi.mock('@js/Components/Typesense/FederatedSearch.vue', () => ({
    default: { name: 'FederatedSearch', render: () => null },
}))

import CommandPalette from '@/Components/CommandPalette/CommandPalette.vue'
import CommandPaletteTrigger from '@/Components/CommandPalette/CommandPaletteTrigger.vue'
import { useCommandPalette } from '@/composables/useCommandPalette'

const cmdK = () => document.dispatchEvent(new KeyboardEvent('keydown', { key: 'k', metaKey: true }))

describe('command palette', () => {
    beforeEach(() => {
        useCommandPalette().close()
    })

    it('opens on Cmd+K and stays open when pressed again', async () => {
        const wrapper = mount(CommandPalette, { attachTo: document.body })
        const { isOpen } = useCommandPalette()

        cmdK()
        await nextTick()
        expect(isOpen.value).toBe(true)

        // It used to toggle, so the second press of a reflex shortcut closed it.
        cmdK()
        await nextTick()
        expect(isOpen.value).toBe(true)

        wrapper.unmount()
    })

    it('wires the input to the listbox as a combobox', async () => {
        const wrapper = mount(CommandPalette, { attachTo: document.body })

        useCommandPalette().open()
        await nextTick()

        // The palette teleports to body, so it is not inside the wrapper tree.
        const input = document.body.querySelector('input')
        const listbox = document.body.querySelector('[role="listbox"]')

        expect(input.getAttribute('role')).toBe('combobox')
        expect(input.getAttribute('aria-controls')).toBe(listbox.id)

        // The active option must be addressable, or arrowing is silent to AT.
        const active = input.getAttribute('aria-activedescendant')
        expect(active).toBeTruthy()
        expect(document.getElementById(active).getAttribute('role')).toBe('option')

        wrapper.unmount()
    })

    it('puts a page under the default selection, not Logout', async () => {
        const wrapper = mount(CommandPalette, { attachTo: document.body })

        useCommandPalette().open()
        await nextTick()

        const options = [...document.body.querySelectorAll('[role="option"]')]
        const input = document.body.querySelector('input')

        // Actions used to lead, which left Logout in the blind arrow-down path.
        expect(options[0].id).toBe(input.getAttribute('aria-activedescendant'))
        expect(options[0].textContent).not.toContain('Logout')
        expect(options.at(-1).textContent).toContain('Logout')

        wrapper.unmount()
    })

    it('names the theme destination rather than saying "toggle"', async () => {
        document.documentElement.classList.remove('dark')
        const light = mount(CommandPalette, { attachTo: document.body })
        useCommandPalette().open()
        await nextTick()
        expect(document.body.textContent).toContain('Switch to dark mode')
        light.unmount()

        useCommandPalette().close()
        document.documentElement.classList.add('dark')
        const dark = mount(CommandPalette, { attachTo: document.body })
        useCommandPalette().open()
        await nextTick()
        expect(document.body.textContent).toContain('Switch to light mode')
        dark.unmount()

        document.documentElement.classList.remove('dark')
    })

    it('does not badge every row with what its group heading already says', async () => {
        const wrapper = mount(CommandPalette, { attachTo: document.body })

        useCommandPalette().open()
        await nextTick()

        expect(document.body.querySelector('.command-item-badge')).toBeNull()

        wrapper.unmount()
    })

    it('takes the app root out of the a11y tree while open', async () => {
        const root = document.createElement('div')
        root.id = 'app'
        document.body.appendChild(root)

        const wrapper = mount(CommandPalette, { attachTo: document.body })
        const { open, close } = useCommandPalette()

        open()
        await nextTick()
        expect(root.hasAttribute('inert')).toBe(true)

        close()
        await nextTick()
        expect(root.hasAttribute('inert')).toBe(false)

        wrapper.unmount()
        root.remove()
    })
})

describe('CommandPaletteTrigger', () => {
    it('opens the shared palette rather than searching on its own', async () => {
        const wrapper = mount(CommandPaletteTrigger)
        const { isOpen } = useCommandPalette()

        expect(isOpen.value).toBe(false)
        await wrapper.find('button').trigger('click')
        expect(isOpen.value).toBe(true)

        useCommandPalette().close()
    })

    it('holds the shortcut badge back until the platform is known', async () => {
        const wrapper = mount(CommandPaletteTrigger)

        // Nothing on the first paint, rather than the wrong key.
        expect(wrapper.find('kbd').exists()).toBe(false)

        await nextTick()
        expect(wrapper.find('kbd').text()).toMatch(/Cmd\+K|Ctrl\+K/)
    })
})
