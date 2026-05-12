<script module lang="ts">
    import { index } from '@/routes/projects';

    export const layout = {
        breadcrumbs: [{ title: 'Projects', href: index() }],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import OrganizationController from '@/actions/App/Http/Controllers/Organization/OrganizationController';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { DataTable } from '@/components/Display/DataTable';
    import perspective from '@/perspectives/organizations';
    import { getUserRoleName } from '@/lib/inertia';
    import type { OrganizationEntity as Organization } from '@/types';

    interface Props {
        organizations: Organization[];
    }

    let { organizations }: Props = $props();
    let role = $state(getUserRoleName());
    const config = perspective.for(role);
    let headers = $state(config.headers);
    let actions = $state(config.actions);
    let table = $state<DataTable | null>(null);

    function getTableContext() {
        return table?.getContext();
    }
</script>

<AppHead title="Organizations" />

<div class="app-main">
    <div class="flex items-center justify-between">
        <Heading title="Organizations" description="Manage your organizations" />
        <Button onclick={() => router.visit(OrganizationController.create.url())}
            >New organization</Button>
    </div>

    {#if organizations.length === 0}
        <p class="text-sm text-muted-foreground">No organizations yet.</p>
    {:else}
        <DataTable
            selectable
            bind:this={table}
            data={organizations}
            {headers}
            {actions}
            onRowClick={(organization) =>
                router.visit(OrganizationController.show.url(organization.id))} />
    {/if}
</div>
