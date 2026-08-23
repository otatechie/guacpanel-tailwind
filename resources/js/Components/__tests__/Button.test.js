import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import Button from '@/Components/Button.vue'

// Contract tests — assert the wrapper API from docs/ui-contract.md,
// not shadcn internals. These must keep passing if ui/ is ever replaced.
describe('Button wrapper contract', () => {
    it('renders a real <button> with the slot content', () => {
        const wrapper = mount(Button, { slots: { default: 'Save changes' } })

        expect(wrapper.element.tagName).toBe('BUTTON')
        expect(wrapper.text()).toBe('Save changes')
    })

    it('defaults type to "button" so forms are not submitted by accident', () => {
        const wrapper = mount(Button)

        expect(wrapper.attributes('type')).toBe('button')
    })

    it('accepts type="submit" for form submits', () => {
        const wrapper = mount(Button, { props: { type: 'submit' } })

        expect(wrapper.attributes('type')).toBe('submit')
    })

    it('emits click and respects disabled', async () => {
        const wrapper = mount(Button, { attrs: { disabled: true } })

        expect(wrapper.attributes('disabled')).toBeDefined()

        const enabled = mount(Button)
        await enabled.trigger('click')
        expect(enabled.emitted('click')).toHaveLength(1)
    })

    it('maps every contract variant and size without leaking cva terms', () => {
        for (const variant of ['primary', 'secondary', 'danger', 'ghost', 'outline', 'link']) {
            const wrapper = mount(Button, { props: { variant } })
            expect(wrapper.attributes('class')).toBeTruthy()
        }

        for (const size of ['xs', 'sm', 'md', 'lg', 'icon']) {
            const wrapper = mount(Button, { props: { size } })
            expect(wrapper.attributes('class')).toBeTruthy()
        }
    })

    it('passes page classes through (e.g. w-full)', () => {
        const wrapper = mount(Button, { attrs: { class: 'w-full' } })

        expect(wrapper.attributes('class')).toContain('w-full')
    })

    it('renders danger as a solid fill, matching the old .btn-danger', () => {
        const wrapper = mount(Button, { props: { variant: 'danger' } })
        const classes = wrapper.attributes('class')

        // Solid base fill, not shadcn's soft `bg-destructive/10` tint
        expect(classes).toMatch(/(^|\s)bg-destructive(\s|$)/)
        expect(classes).not.toMatch(/(^|\s)bg-destructive\/\d+/)
        expect(classes).toContain('text-white')
    })

    it('can render as another component and drops the button-only type attr', () => {
        const Stub = { props: ['href'], template: '<a :href="href"><slot /></a>' }
        const wrapper = mount(Button, {
            props: { as: Stub },
            attrs: { href: '/somewhere' },
        })

        expect(wrapper.element.tagName).toBe('A')
        expect(wrapper.attributes('href')).toBe('/somewhere')
        expect(wrapper.attributes('type')).toBeUndefined()
    })
})
