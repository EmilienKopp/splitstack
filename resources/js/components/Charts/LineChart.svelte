<script lang="ts">
    import * as Chart from '$components/ui/chart/index.js';
    import { scaleUtc } from 'd3-scale';
    import { curveNatural, curveStep, curveBasis } from 'd3-shape';
    import { AreaChart, LineChart, BarChart, Highlight } from 'layerchart';
    import { cubicInOut } from 'svelte/easing';

    interface DataShape {
        [key: string]: string | number | Date;
    }
    type Data = DataShape[];
    interface SeriesDefinition {
        key: string;
        label: string;
        color?: string;
    }

    interface Props {
        dataset: Data;
        area?: boolean;
        bars?: boolean;
        utcScale?: boolean;
        xKey?: string;
        definitions?: SeriesDefinition[];
        config?: Chart.ChartConfig;
        xAxisFormatter?: (value: any) => string;
        yAxisFormatter?: (value: any) => string;
        labelFormatter?: (value: any) => string;
        tooltipValueFormatter?: (value: any) => string;
        domain?: {
            x?: [Date | string | number, Date | string | number];
            y?: [number, number];
        };
        ticks?: {
            x?: (Date | string | number)[];
            y?: number[];
        };
        class?: string;
    }

    let {
        dataset = [],
        area = false,
        bars = false,
        utcScale = false,
        xKey = 'date',
        definitions = [],
        config,
        xAxisFormatter,
        yAxisFormatter,
        labelFormatter,
        tooltipValueFormatter,
        domain,
        ticks,
        class: className = 'max-h-72 max-w-100',
    }: Props = $props();

    const series = $derived(
        definitions?.map((def) => ({
            key: def.key,
            label: def.label,
            color: def.color ?? config?.[def.key]?.color ?? 'var(--chart-3)',
        })),
    );

    const ChartComponent = $derived.by(() => {
        if (area) return AreaChart;
        if (bars) return BarChart;
        return LineChart;
    });
    const curve = $derived.by(() => {
        if (area) return curveBasis;
        if (bars) return curveStep;
        return curveStep;
    });

    const motion = { type: 'tween' as const, duration: 500, easing: cubicInOut };

    const lineAreaProps = $derived({
        curve,
        line: { class: 'stroke-1' },
        fillOpacity: 0.3,
        motion,
    });

    const barProps = { stroke: 'none', rounded: 'none' as const, motion };

    function getDefaultYDomain(data: Data, yKeys: string[]) {
        const yValues = data
            .flatMap((d) => yKeys.map((key) => Number(d[key])))
            .filter((v) => isFinite(v));
        return yValues.length ? [0, Math.max(...yValues) * 1.1] : [0, 1];
    }

    function getYDomain() {
        return (
            domain?.y ??
            getDefaultYDomain(
                dataset,
                definitions.map((def) => def.key),
            )
        );
    }
</script>

<Chart.Container {config} class={className}>
    <ChartComponent
        data={dataset}
        {series}
        x={xKey}
        {...utcScale && !bars ? { xScale: scaleUtc() } : {}}
        {...!bars ? { seriesLayout: 'stack' } : {}}
        yDomain={getYDomain()}
        highlight={{ area: { fill: 'none' } }}
        props={{
            area: lineAreaProps,
            spline: lineAreaProps,
            bars: barProps,
            xAxis: {
                ticks: ticks?.x,
                format: xAxisFormatter,
            },
            yAxis: { ticks: ticks?.y ?? [0, 50, 100], format: yAxisFormatter },
        }}>
        {#snippet tooltip()}
            <Chart.Tooltip {labelFormatter} indicator="dot">
                {#snippet formatter({ value }: { value: any })}
                    <span>
                        Duration: <strong
                            >{tooltipValueFormatter ? tooltipValueFormatter(value) : value}</strong>
                    </span>
                {/snippet}
            </Chart.Tooltip>
        {/snippet}
    </ChartComponent>
</Chart.Container>
