<script module lang="ts">
    import { index } from '@/routes/project';

    export const layout = {
        breadcrumbs: [{ title: 'Projects', href: index() }],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import ProjectController from '@/actions/App/Http/Controllers/ProjectController';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';

    interface Project {
        id: number;
        name: string;
        status: string;
        type: string;
    }

    interface Props {
        projects: Project[];
    }

    let { projects }: Props = $props();
</script>

<AppHead title="Projects" />

<div class="flex flex-col space-y-6">
    <div class="flex items-center justify-between">
        <Heading title="Projects" description="Manage your projects" />
        <Button onclick={() => router.visit(ProjectController.create.url())}>New project</Button>
    </div>

    {#if projects.length === 0}
        <p class="text-sm text-muted-foreground">No projects yet.</p>
    {:else}
        <div class="space-y-3">
            {#each projects as project (project.id)}
                <div class="flex items-center justify-between rounded-lg border p-4">
                    <div>
                        <p class="font-medium">{project.name}</p>
                        <p class="text-sm text-muted-foreground capitalize">{project.status}</p>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</div>
