<script module lang="ts">
    import { index } from '@/routes/projects';

    export const layout = {
        breadcrumbs: [{ title: 'Projects', href: index() }],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import ProjectController from '@/actions/App/Http/Controllers/TimeTracking/ProjectController';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { DataTable } from '@/components/Display/DataTable';
    import perspective from '@/perspectives/projects';
    import { getUserRoleName } from '@/lib/inertia';
    import type { ProjectEntity as Project } from '@/types';

    interface Props {
        projects: Project[];
    }

    let { projects }: Props = $props();
    let role = $state(getUserRoleName());
    const config = perspective.for(role);
    let headers = $state(config.headers);
    let actions = $state(config.actions);
    let table = $state<DataTable | null>(null);

    function getTableContext() {
        return table?.getContext();
    }
</script>

<AppHead title="Projects" />

<div class="app-main">
    <div class="flex items-center justify-between">
        <Heading title="Projects" description="Manage your projects" />
        <Button onclick={() => router.visit(ProjectController.create.url())}>New project</Button>
    </div>

    {#if projects.length === 0}
        <p class="text-sm text-muted-foreground">No projects yet.</p>
    {:else}
        <DataTable
            selectable
            bind:this={table}
            data={projects}
            {headers}
            {actions}
            onRowClick={(project) => router.visit(ProjectController.show.url(project.id))} />
    {/if}
</div>
