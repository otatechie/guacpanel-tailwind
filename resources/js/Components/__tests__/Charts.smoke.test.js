import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import AreaChart from '../Charts/AreaChart.vue'
import BarChart from '../Charts/BarChart.vue'
import DonutChart from '../Charts/DonutChart.vue'
import LineChart from '../Charts/LineChart.vue'

const chartData = {
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [
        { label: 'Income', data: [10, 20, 30] },
        { label: 'Expenses', data: [5, 15, 25] },
    ],
}

describe('charts', () => {
    it.each([
        ['LineChart', LineChart],
        ['BarChart', BarChart],
        ['AreaChart', AreaChart],
        ['DonutChart', DonutChart],
    ])('%s mounts and builds its Unovis container', (name, component) => {
        const wrapper = mount(component, { props: { chartData, height: '320px' } })
        // happy-dom has no layout engine, so d3 never paints an <svg> here; the
        // container is as far as a unit test can check.
        expect(wrapper.find('.unovis-xy-container, .unovis-single-container').exists()).toBe(true)
        wrapper.unmount()
    })

    it('labels each series in the legend', () => {
        const wrapper = mount(LineChart, { props: { chartData, height: '320px' } })
        expect(wrapper.text()).toContain('Income')
        expect(wrapper.text()).toContain('Expenses')
        wrapper.unmount()
    })
})
