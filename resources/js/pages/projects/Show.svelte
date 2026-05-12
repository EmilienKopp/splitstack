<script lang="ts">
    import Button from '$components/Actions/Button.svelte';
    import DataList from '$components/Display/DataList.svelte';
    import FieldsetWrapper from '$components/FieldsetWrapper.svelte';
    import PageTitle from '$components/PageTitle.svelte';
    import { superUseForm } from '$lib/inertia';
    import { useDetached } from '$lib/stores/global/drawerState.svelte';
    import { date, smartDuration } from '$lib/core/support/formatting';
    import projects from '@/routes/projects';
    // import type { ProjectBase } from '$lib/models/Project';

    interface Props {
        project: any; // ProjectBase;
    }

    let { project }: Props = $props();
    const detached = useDetached();

    const headers = [
        { key: 'name', label: 'Name' },
        { key: 'description', label: 'Description' },
        { key: 'created_at', label: 'Created At', formatter: date },
        { key: 'start_date', label: 'Start Date', formatter: date },
        { key: 'end_date', label: 'End Date', formatter: date },
        { key: 'type', label: 'Type' },
    ];

    $inspect(project);
</script>

<PageTitle>
    <h2 class="font-semibold text-xl leading-tight">
        {project.name}

        {#if !detached}
            <Button type="button" href={projects.show(project.id).url} class="ml-4">Edit</Button>
        {/if}
    </h2>
</PageTitle>

<FieldsetWrapper>
    <DataList {headers} data={project} />
    {#if project.summary}
        <h3 class="mt-8 mb-4 text-lg font-semibold">Summary</h3>
        {#each project.summary.filter((item) => item.duration_seconds && item.activity_type?.name) as item}
            <div class="mb-2">
                <strong>{item.activity_type?.name}:</strong>
                {smartDuration(item.duration_seconds)}
            </div>
        {/each}
    {/if}

    {#if project.cost}
        <h3 class="mt-8 mb-4 text-lg font-semibold">Cost</h3>
        {project.cost}
    {/if}
</FieldsetWrapper>
