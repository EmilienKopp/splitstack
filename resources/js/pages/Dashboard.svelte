<script module lang="ts">
    import { dashboard } from '@/routes';
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: props.currentTeam
                    ? dashboard(props.currentTeam.slug)
                    : '/dashboard',
            },
        ],
    });
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import { clock } from '@/lib/clock.svelte';
    import { Form, page } from '@inertiajs/svelte';
    import dayjs from 'dayjs';
    import { Play, Square } from 'lucide-svelte';
    import { onDestroy } from 'svelte';
    import { fade, slide } from 'svelte/transition';
    import { clockIn, clockOut } from '@/routes/daily-log';

    interface Project {
        id: number;
        name: string;
    }

    interface ClockEntry {
        id: number;
        in: string | null;
        out: string | null;
        daily_log?: {
            project?: Project;
        };
    }

    interface Props {
        projects: Project[];
        todayEntries: ClockEntry[];
    }

    let { projects, todayEntries }: Props = $props();

    const latestEntry = $derived(todayEntries?.[0]);
    const isClockedIn = $derived(!!latestEntry?.in && !latestEntry?.out);
    const activeProjectId = $derived(
        latestEntry?.daily_log?.project?.id ?? null,
    );
    const activeProjectName = $derived(
        latestEntry?.daily_log?.project?.name ?? null,
    );

    let selectedProjectId = $state<number | null>(activeProjectId);

    const elapsedTime = $derived.by(() => {
        if (isClockedIn && latestEntry?.in) {
            return clock.since(latestEntry.in);
        }
        return '00:00:00';
    });

    const today = $derived(dayjs().format('dddd'));
    const todayDate = $derived(dayjs().format('MMMM D, YYYY'));

    const interval = setInterval(() => clock.refresh(), 1000);
    onDestroy(() => clearInterval(interval));

    function formatTime(dateStr: string | null): string {
        if (!dateStr) return '--:--';
        return dayjs(dateStr).format('HH:mm');
    }

    function formatDuration(entry: ClockEntry): string {
        if (!entry.in) return '';
        const end = entry.out ? dayjs(entry.out) : dayjs();
        const secs = end.diff(dayjs(entry.in), 'second');
        const h = Math.floor(secs / 3600);
        const m = Math.floor((secs % 3600) / 60);
        return h > 0 ? `${h}h ${m}m` : `${m}m`;
    }
</script>

<AppHead title="Dashboard" />

<div
    class="flex h-full flex-1 flex-col items-center justify-center px-6 py-4 overflow-hidden"
>
    <div class="w-full max-w-lg flex flex-col items-center min-h-0">
        <!-- Date Header -->
        <div class="text-center mb-4">
            <p
                class="text-sm tracking-widest uppercase text-muted-foreground/60 mb-1"
            >
                {today}
            </p>
            <p class="text-lg font-light text-muted-foreground">{todayDate}</p>
        </div>

        <!-- Timer Display -->
        <div class="my-4">
            <div class="flex items-baseline gap-1">
                {#each elapsedTime.split(':') as segment, i}
                    {#if i > 0}
                        <span
                            class="font-mono text-2xl md:text-3xl font-light text-muted-foreground/40"
                            >:</span
                        >
                    {/if}
                    <span
                        class="font-mono text-3xl md:text-4xl font-light tracking-tighter {i <
                        2
                            ? ''
                            : 'text-muted-foreground/60'}"
                    >
                        {segment}
                    </span>
                {/each}
            </div>

            <div class="mt-3 flex items-center justify-center gap-3">
                <div
                    class="w-2 h-2 rounded-full transition-colors duration-300 {isClockedIn
                        ? 'bg-red-500 animate-pulse'
                        : 'bg-muted-foreground/20'}"
                ></div>
                <span
                    class="text-xs font-medium tracking-widest uppercase text-muted-foreground/60"
                >
                    {isClockedIn ? 'Recording' : 'Ready'}
                </span>
            </div>
        </div>

        {#if isClockedIn && activeProjectName}
            <div class="mb-4 text-center" transition:fade={{ duration: 200 }}>
                <p class="text-xs font-medium text-muted-foreground">
                    {activeProjectName}
                </p>
            </div>
        {/if}

        <!-- Clock Form -->
        <Form action={isClockedIn ? clockOut() : clockIn()}>
            {#if isClockedIn}
                <input
                    type="hidden"
                    name="project_id"
                    value={activeProjectId}
                />
            {:else}
                <div
                    class="w-full max-w-sm mb-4"
                    transition:slide={{ duration: 250 }}
                >
                    <select
                        name="project_id"
                        bind:value={selectedProjectId}
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <option value={null} disabled>Select Project</option>
                        {#each projects as project}
                            <option value={project.id}>{project.name}</option>
                        {/each}
                    </select>
                </div>
            {/if}

            <div class="flex justify-center">
                <button
                    type="submit"
                    disabled={!selectedProjectId && !isClockedIn}
                    class="w-20 h-20 md:w-24 md:h-24 rounded-full flex items-center justify-center transition-all duration-300 cursor-pointer
                        disabled:opacity-30 disabled:cursor-not-allowed
                        {isClockedIn
                        ? 'bg-foreground text-background'
                        : 'bg-background text-foreground border-2 border-foreground hover:bg-foreground hover:text-background'}"
                >
                    {#if isClockedIn}
                        <Square class="w-6 h-6 md:w-8 md:h-8 fill-current" />
                    {:else}
                        <Play class="w-6 h-6 md:w-8 md:h-8 fill-current ml-1" />
                    {/if}
                </button>
            </div>
        </Form>

        <p
            class="mt-4 text-sm tracking-widest uppercase text-muted-foreground/60"
        >
            {isClockedIn ? 'Clock Out' : 'Clock In'}
        </p>

        <!-- Today's Entries -->
        {#if todayEntries?.length}
            <div
                class="w-full mt-6 pt-4 border-t border-border min-h-0 flex flex-col max-h-[33vh]"
            >
                <p
                    class="text-xs tracking-widest uppercase text-muted-foreground/60 text-center mb-3"
                >
                    Today's Sessions
                </p>
                <div class="overflow-y-auto min-h-0 flex flex-col gap-2">
                    {#each todayEntries as entry}
                        <div
                            class="flex items-center justify-between py-2 px-3 rounded-lg border border-border/60 text-sm"
                        >
                            <span class="text-muted-foreground"
                                >{entry.daily_log?.project?.name ??
                                    'No project'}</span
                            >
                            <span
                                class="font-mono text-xs text-muted-foreground/70"
                            >
                                {formatTime(entry.in)} – {entry.out
                                    ? formatTime(entry.out)
                                    : '…'}
                                {#if entry.in}
                                    <span class="ml-1 text-muted-foreground/50"
                                        >({formatDuration(entry)})</span
                                    >
                                {/if}
                            </span>
                        </div>
                    {/each}
                </div>
            </div>
        {/if}
    </div>
</div>
