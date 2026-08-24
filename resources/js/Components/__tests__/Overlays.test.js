import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import Badge from '@/Components/Badge.vue'
import RoleBadge from '@/Components/Common/RoleBadge.vue'
import NotificationTypeBadge from '@/Components/Common/NotificationTypeBadge.vue'
import Modal from '@/Components/Notifications/Modal.vue'
import Alert from '@/Components/Notifications/Alert.vue'

describe('Badge contract', () => {
    it('renders slot content', () => {
        const wrapper = mount(Badge, { slots: { default: 'Active' } })
        expect(wrapper.text()).toBe('Active')
    })

    it('gives each semantic variant its own styling', () => {
        const seen = new Set()
        for (const variant of ['neutral', 'primary', 'info', 'success', 'warning', 'danger']) {
            const classes = mount(Badge, { props: { variant } }).attributes('class')
            expect(classes).toBeTruthy()
            seen.add(classes)
        }
        expect(seen.size).toBe(6)
    })

    it('falls back to neutral for an unknown variant', () => {
        const unknown = mount(Badge, { props: { variant: 'nonsense' } }).attributes('class')
        const neutral = mount(Badge, { props: { variant: 'neutral' } }).attributes('class')
        expect(unknown).toBe(neutral)
    })
})

describe('RoleBadge contract', () => {
    it('capitalises the role name', () => {
        const wrapper = mount(RoleBadge, { props: { role: { name: 'superuser' } } })
        expect(wrapper.text()).toBe('Superuser')
    })

    it('survives an empty role without throwing', () => {
        expect(() => mount(RoleBadge, { props: { role: {} } })).not.toThrow()
    })
})

describe('NotificationTypeBadge contract', () => {
    it('labels known types and defaults empty to Info', () => {
        expect(mount(NotificationTypeBadge, { props: { type: 'warning' } }).text()).toBe('Warning')
        expect(mount(NotificationTypeBadge, { props: { type: null } }).text()).toBe('Info')
        expect(mount(NotificationTypeBadge, { props: { type: '  SUCCESS ' } }).text()).toBe(
            'Success'
        )
    })
})

describe('Alert contract', () => {
    it('renders slot content and an optional title', () => {
        const wrapper = mount(Alert, {
            props: { title: 'Heads up' },
            slots: { default: 'Something happened' },
        })

        expect(wrapper.text()).toContain('Heads up')
        expect(wrapper.text()).toContain('Something happened')
    })

    it('treats danger as error', () => {
        const danger = mount(Alert, { props: { type: 'danger' } }).attributes('class')
        const error = mount(Alert, { props: { type: 'error' } }).attributes('class')

        expect(danger).toBe(error)
    })

    it('restyles when the type changes', async () => {
        // Regression: type was resolved once at setup, so a bound :type left the
        // alert wearing whatever it first rendered with.
        const wrapper = mount(Alert, { props: { type: 'info' } })
        const before = wrapper.attributes('class')

        await wrapper.setProps({ type: 'error' })

        expect(wrapper.attributes('class')).not.toBe(before)
    })

    it('survives an unknown type instead of dereferencing undefined', () => {
        expect(() => mount(Alert, { props: { type: 'nonsense' } })).not.toThrow()
    })

    it('only interrupts a screen reader for errors', () => {
        // role="alert" is assertive; standing info/warning notes must not be.
        expect(mount(Alert, { props: { type: 'error' } }).attributes('role')).toBe('alert')
        expect(mount(Alert, { props: { type: 'danger' } }).attributes('role')).toBe('alert')
        expect(mount(Alert, { props: { type: 'info' } }).attributes('role')).toBe('status')
        expect(mount(Alert, { props: { type: 'warning' } }).attributes('role')).toBe('status')
        expect(mount(Alert, { props: { type: 'success' } }).attributes('role')).toBe('status')
    })

    it('emits dismiss and hides when dismissed', async () => {
        const wrapper = mount(Alert, { props: { dismissible: true } })

        await wrapper.find('button[aria-label="Dismiss"]').trigger('click')

        expect(wrapper.emitted('dismiss')).toHaveLength(1)
        expect(wrapper.find('[role]').exists()).toBe(false)
    })
})

describe('Modal contract', () => {
    // The dialog renders through a portal, so assertions run against document.body
    // after the portal has flushed.
    const mountModal = async (props = {}) => {
        const wrapper = mount(Modal, {
            props: { show: true, ...props },
            slots: { title: 'Delete user', default: 'Are you sure?', footer: 'FooterHere' },
            attachTo: document.body,
        })
        await nextTick()
        await nextTick()
        return wrapper
    }

    it('renders title, body and footer slots when shown', async () => {
        await mountModal()
        expect(document.body.textContent).toContain('Delete user')
        expect(document.body.textContent).toContain('Are you sure?')
        expect(document.body.textContent).toContain('FooterHere')
    })

    it('renders nothing while show is false', async () => {
        mount(Modal, { props: { show: false }, slots: { title: 'Hidden title' } })
        await nextTick()
        expect(document.body.textContent).not.toContain('Hidden title')
    })

    it('exposes a real dialog with an accessible name', async () => {
        await mountModal()
        const dialog = document.querySelector('[role="dialog"]')

        expect(dialog).not.toBeNull()
        expect(dialog.getAttribute('aria-labelledby')).toBeTruthy()
    })

    it('emits close when escape is pressed', async () => {
        const wrapper = await mountModal()

        const dialog = document.querySelector('[role="dialog"]')
        dialog.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }))
        await nextTick()

        expect(wrapper.emitted('close')).toBeTruthy()
    })
})
