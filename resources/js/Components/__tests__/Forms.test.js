import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import FormInput from '@/Components/Forms/FormInput.vue'
import FormTextarea from '@/Components/Forms/FormTextarea.vue'
import FormCheckbox from '@/Components/Forms/FormCheckbox.vue'
import Switch from '@/Components/Forms/Switch.vue'

// These assert the PUBLIC API from docs/ui-contract.md. The internals moved to
// shadcn primitives; if any of these break, pages break.

describe('FormInput contract', () => {
    it('renders a labelled input and round-trips v-model', async () => {
        const wrapper = mount(FormInput, { props: { label: 'Email', modelValue: 'a@b.c' } })
        const input = wrapper.find('input')

        expect(input.element.value).toBe('a@b.c')
        expect(wrapper.find('label').text()).toContain('Email')
        expect(input.attributes('id')).toBe('email')

        await input.setValue('x@y.z')
        expect(wrapper.emitted('update:modelValue').at(-1)).toEqual(['x@y.z'])
    })

    it('marks required and wires aria-invalid + error text', () => {
        const wrapper = mount(FormInput, {
            props: { label: 'Name', required: true, error: 'Name is required' },
        })

        expect(wrapper.find('input').attributes('aria-invalid')).toBe('true')
        expect(wrapper.find('[role="alert"]').text()).toBe('Name is required')
        expect(wrapper.find('input').attributes('aria-describedby')).toBe('name-error')
    })

    it('shows help text only when there is no error', async () => {
        const wrapper = mount(FormInput, { props: { label: 'Name', help: 'Your full name' } })
        expect(wrapper.text()).toContain('Your full name')

        await wrapper.setProps({ error: 'Bad' })
        expect(wrapper.text()).not.toContain('Your full name')
    })

    it('toggles password visibility', async () => {
        const wrapper = mount(FormInput, { props: { label: 'Password', type: 'password' } })
        expect(wrapper.find('input').attributes('type')).toBe('password')

        await wrapper.find('button[aria-label="Show password"]').trigger('click')
        expect(wrapper.find('input').attributes('type')).toBe('text')
    })

    it('honours a custom id and disabled', () => {
        const wrapper = mount(FormInput, {
            props: { label: 'Email', id: 'custom-id', disabled: true },
        })
        expect(wrapper.find('input').attributes('id')).toBe('custom-id')
        expect(wrapper.find('input').attributes('disabled')).toBeDefined()
    })
})

describe('FormTextarea contract', () => {
    it('round-trips v-model and honours rows', async () => {
        const wrapper = mount(FormTextarea, { props: { label: 'Bio', modelValue: 'hi', rows: 5 } })
        const textarea = wrapper.find('textarea')

        expect(textarea.element.value).toBe('hi')
        expect(textarea.attributes('rows')).toBe('5')

        await textarea.setValue('there')
        expect(wrapper.emitted('update:modelValue').at(-1)).toEqual(['there'])
    })
})

describe('FormCheckbox contract', () => {
    it('emits a strict boolean, never indeterminate', async () => {
        const wrapper = mount(FormCheckbox, { props: { label: 'Accept terms', modelValue: false } })

        await wrapper.find('button[role="checkbox"]').trigger('click')

        const emitted = wrapper.emitted('update:modelValue').at(-1)[0]
        expect(typeof emitted).toBe('boolean')
        expect(emitted).toBe(true)
    })

    it('reflects checked state and shows errors', () => {
        const checked = mount(FormCheckbox, { props: { label: 'Accept', modelValue: true } })
        expect(checked.find('[role="checkbox"]').attributes('aria-checked')).toBe('true')

        const errored = mount(FormCheckbox, {
            props: { label: 'Accept', modelValue: false, error: 'Required' },
        })
        expect(errored.find('[role="alert"]').text()).toBe('Required')
    })

    it('renders the mixed state via `indeterminate` but still emits a boolean', async () => {
        const wrapper = mount(FormCheckbox, {
            props: { label: 'Select all', modelValue: false, indeterminate: true },
        })

        expect(wrapper.find('[role="checkbox"]').attributes('aria-checked')).toBe('mixed')

        await wrapper.find('button[role="checkbox"]').trigger('click')
        expect(wrapper.emitted('update:modelValue').at(-1)).toEqual([true])
    })
})

describe('Switch contract', () => {
    it('exposes switch semantics and emits a boolean', async () => {
        const wrapper = mount(Switch, { props: { modelValue: false, label: 'Enable 2FA' } })
        const el = wrapper.find('[role="switch"]')

        expect(el.attributes('aria-checked')).toBe('false')
        expect(el.attributes('aria-label')).toBe('Enable 2FA')

        await el.trigger('click')
        expect(wrapper.emitted('update:modelValue').at(-1)).toEqual([true])
    })

    it('does not emit when disabled', async () => {
        const wrapper = mount(Switch, { props: { modelValue: false, disabled: true } })
        await wrapper.find('[role="switch"]').trigger('click')

        expect(wrapper.emitted('update:modelValue')).toBeUndefined()
    })
})
