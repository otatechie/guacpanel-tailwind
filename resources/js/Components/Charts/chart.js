/*
  Shared helpers for the Unovis chart wrappers.

  Charts are given the Chart.js-style shape used across the app:
    { labels: ['Jan', ...], datasets: [{ label: 'Income', data: [1, ...] }] }
  Unovis wants one record per x position, so we pivot it.
*/

// Series colours are the design tokens from app.css. SVG resolves var() at paint
// time, so charts follow the light/dark switch without any JS.
const seriesColors = [
    'var(--chart-1)',
    'var(--chart-2)',
    'var(--chart-3)',
    'var(--chart-4)',
    'var(--chart-5)',
]

export const colorAt = index => seriesColors[index % seriesColors.length]

export const formatNumber = value => Math.round(Number(value ?? 0)).toLocaleString()

// Small-count charts (user sign-ups, say) otherwise get half-step ticks that
// round to duplicate labels — 2, 2, 1, 1, 0. numTicks is only a hint to d3, so
// name the values outright. Returns undefined for larger ranges: d3 picks well.
export const integerTickValues = records => {
    const max = Math.max(0, ...records.flatMap(record => record.values))
    return max <= 5 ? Array.from({ length: Math.ceil(max) + 1 }, (_, i) => i) : undefined
}

export const toRecords = chartData =>
    (chartData.labels || []).map((label, i) => ({
        label,
        values: (chartData.datasets || []).map(dataset => Number(dataset.data?.[i] ?? 0)),
    }))

export const tooltipTemplate = (record, datasets) => `
    <div class="min-w-36 text-xs">
        <div class="mb-1.5 font-medium">${record.label}</div>
        ${datasets
            .map(
                (dataset, i) => `
                    <div class="flex items-center gap-2">
                        <span class="size-2 shrink-0 rounded-full" style="background:${colorAt(i)}"></span>
                        <span>${dataset.label}</span>
                        <span class="ml-auto font-medium tabular-nums">${formatNumber(record.values[i])}</span>
                    </div>`
            )
            .join('')}
    </div>`
