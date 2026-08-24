import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import DropdownMenu from '@/Components/DropdownMenu.vue'

// Contract tests — assert the wrapper API from docs/ui-contract.md,
// not shadcn/reka internals. These must keep passing if ui/ is ever replaced.

// The panel renders through a portal, so assertions run against document.body
// once it has opened and flushed.
const panel = () => document.body.querySelector('[data-slot="dropdown-menu-content"]')

const open = async (options = {}) => {
    const wrapper = mount(DropdownMenu, {
        attachTo: document.body,
        slots: { trigger: '<button>Open</button>', default: '<p>Panel body</p>' },
        ...options,
    })
    await wrapper.find('[data-slot="dropdown-menu-trigger"]').trigger('click')
    await nextTick()
    await nextTick()
    return wrapper
}

describe('DropdownMenu wrapper contract', () => {
    it('renders the trigger slot and stays closed until it is used', () => {
        const wrapper = mount(DropdownMenu, {
            slots: { trigger: '<button>Open</button>', default: '<p>Panel body</p>' },
        })

        expect(wrapper.text()).toContain('Open')
        expect(panel()).toBeNull()
    })

    it('opens on trigger click and shows the default slot', async () => {
        const wrapper = await open()

        expect(panel()).not.toBeNull()
        expect(panel().textContent).toContain('Panel body')
        wrapper.unmount()
    })

    it('strips the shadcn padding so sections own their spacing', async () => {
        const wrapper = await open()

        expect(panel().className).toContain('p-0')
        expect(panel().className).not.toContain('p-1')
        wrapper.unmount()
    })

    it('accepts a width utility and drops the trigger-width default', async () => {
        const wrapper = await open({ props: { width: 'w-80' } })

        expect(panel().className).toContain('w-80')
        expect(panel().className).not.toContain('--reka-dropdown-menu-trigger-width')
        wrapper.unmount()
    })

    it('supports v-model:open so content can close the panel', async () => {
        const wrapper = mount(DropdownMenu, {
            attachTo: document.body,
            props: { open: true, 'onUpdate:open': e => wrapper.setProps({ open: e }) },
            slots: { trigger: '<button>Open</button>', default: '<p>Panel body</p>' },
        })
        await nextTick()
        await nextTick()

        expect(panel()).not.toBeNull()

        await wrapper.setProps({ open: false })
        await nextTick()
        await nextTick()

        expect(panel()).toBeNull()
        wrapper.unmount()
    })

    it('is non-modal by default, so panel controls stay in tab order', async () => {
        const wrapper = await open({
            slots: {
                trigger: '<button>Open</button>',
                default: '<button id="row-action">Dismiss</button>',
            },
        })

        const event = new KeyboardEvent('keydown', { key: 'Tab', bubbles: true, cancelable: true })
        panel().dispatchEvent(event)

        expect(event.defaultPrevented).toBe(false)
        wrapper.unmount()
    })
})
