<script lang="ts">
    import ShowContainer from '$components/ShowContainer.svelte';
    import { date, smartDuration, autoticks } from '$lib/core/support/formatting';
    import LineChart from '@/components/Charts/LineChart.svelte';
    import projectsPerspective from '@/perspectives/projects';
    import dayjs from 'dayjs';

    interface Props {
        project: any;
    }

    let { project }: Props = $props();

    const { actions } = projectsPerspective.defaultVariant();

    const now = dayjs();

    const logsThisMonth = $derived(
        project.daily_logs.filter((log: any) => {
            const d = dayjs(log.date);
            return d.month() === now.month() && d.year() === now.year();
        }),
    );

    const totalSecondsThisMonth = $derived(
        logsThisMonth.reduce((sum: number, log: any) => sum + (log.total_seconds ?? 0), 0),
    );

    const totalSecondsAllTime = $derived(
        project.daily_logs.reduce((sum: number, log: any) => sum + (log.total_seconds ?? 0), 0),
    );

    const activeDaysThisMonth = $derived(logsThisMonth.length);

    const avgDailyThisMonth = $derived(
        activeDaysThisMonth > 0 ? Math.round(totalSecondsThisMonth / activeDaysThisMonth) : 0,
    );

    const lastLog = $derived(
        project.daily_logs.length > 0 ? project.daily_logs[project.daily_logs.length - 1] : null,
    );

    const lastActivityDate = $derived(lastLog ? dayjs(lastLog.date).format('MMM D') : null);
    const lastActivityDay = $derived(lastLog ? dayjs(lastLog.date).format('dddd') : null);

    const summaryItems = $derived(
        project.summary?.filter((item: any) => item.duration_seconds && item.activity_type?.name) ??
            [],
    );

    const recentLogs = $derived(
        [...project.daily_logs]
            .sort((a: any, b: any) => dayjs(b.date).valueOf() - dayjs(a.date).valueOf())
            .slice(0, 10),
    );
</script>

<ShowContainer title={project.name} {actions} record={project}>
    <!-- Stats Row -->
    <div
        class="grid grid-cols-2 md:grid-cols-4 gap-px bg-black/5 border border-black/5 rounded-xl overflow-hidden mb-10">
        {#each [{ label: 'This Month', value: smartDuration(totalSecondsThisMonth) || '—', sub: `${activeDaysThisMonth} ${activeDaysThisMonth === 1 ? 'day' : 'days'} logged` }, { label: 'All Time', value: smartDuration(totalSecondsAllTime) || '—', sub: `${project.daily_logs.length} ${project.daily_logs.length === 1 ? 'session' : 'sessions'} total` }, { label: 'Daily Average', value: smartDuration(avgDailyThisMonth) || '—', sub: 'this month' }, { label: 'Last Activity', value: lastActivityDate ?? '—', sub: lastActivityDay ?? `${project.type} · ${project.status}` }] as stat}
            <div class="bg-white px-6 py-6">
                <p class="text-xs tracking-widest uppercase text-black/30 mb-3">{stat.label}</p>
                <p class="text-2xl font-light font-mono text-black mb-1">{stat.value}</p>
                <p class="text-xs text-black/40 capitalize">{stat.sub}</p>
            </div>
        {/each}
    </div>

    <!-- Chart -->
    <div class="mb-12">
        <p class="text-xs tracking-widest uppercase text-black/30 mb-6">Time per Day</p>
        <LineChart
            utcScale
            bars
            dataset={project.daily_logs.map((log: any) => ({
                date: new Date(log.date),
                total_seconds: log.total_seconds,
            }))}
            ticks={{
                y: autoticks(
                    project.daily_logs.map((log: any) => log.total_seconds),
                    5000,
                    5,
                ),
            }}
            config={{
                total_seconds: { label: 'Duration (seconds)', color: 'var(--chart-1)' },
            }}
            labelFormatter={(value) => date(value)}
            tooltipValueFormatter={(value) => smartDuration(value)}
            xAxisFormatter={(value) => date(value)}
            definitions={[{ key: 'total_seconds', label: 'Duration (seconds)' }]}
            class="h-56 w-full max-h-56" />
    </div>

    <!-- By Activity -->
    {#if summaryItems.length > 0}
        <div class="mb-12 pt-10 border-t border-black/5">
            <p class="text-xs tracking-widest uppercase text-black/30 mb-6">By Activity</p>
            <div
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-px bg-black/5 border border-black/5 rounded-xl overflow-hidden">
                {#each summaryItems as item}
                    <div class="bg-white px-5 py-4">
                        <p class="text-xs text-black/30 mb-1">{item.activity_type?.name}</p>
                        <p class="text-base font-mono font-medium text-black">
                            {smartDuration(item.duration_seconds)}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    {/if}

    <!-- Recent Sessions -->
    {#if recentLogs.length > 0}
        <div class="pt-10 border-t border-black/5">
            <p class="text-xs tracking-widest uppercase text-black/30 mb-6">Recent Sessions</p>
            <div class="space-y-2">
                {#each recentLogs as log}
                    <div
                        class="flex items-center justify-between py-3 px-4 border border-black/5 rounded-lg hover:border-black/10 transition-colors">
                        <div>
                            <p class="text-sm text-black/70">
                                {dayjs(log.date).format('dddd, MMMM D')}
                            </p>
                            <p class="text-xs text-black/30 mt-0.5 capitalize">
                                {project.type} · {project.status}
                            </p>
                        </div>
                        <p class="text-sm font-mono font-medium text-black">
                            {smartDuration(log.total_seconds) || '—'}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    {/if}

    {#if project.cost}
        <div class="mt-8 pt-8 border-t border-black/5">
            <p class="text-xs tracking-widest uppercase text-black/30 mb-2">Cost</p>
            <p class="text-sm font-mono text-black/70">{project.cost}</p>
        </div>
    {/if}
</ShowContainer>
