<script module lang="ts">
    import { index } from '@/routes/teams';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Teams',
                href: index(),
            },
        ],
    };
</script>

<script lang="ts">
    import Eye from 'lucide-svelte/icons/eye';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import AppHead from '@/components/AppHead.svelte';
    import CreateTeamModal from '@/components/CreateTeamModal.svelte';
    import Heading from '@/components/Heading.svelte';
    import Tip from '@/components/Tip.svelte';
    import Button from '@/components/Actions/Button.svelte';
    import Badge from '@/components/Display/Badge.svelte';
    import { edit } from '@/routes/teams';
    import type { Team } from '@/types';

    let {
        teams,
    }: {
        teams: Team[];
    } = $props();
</script>

<AppHead title="Teams" />

<h1 class="sr-only">Teams</h1>

<div class="flex flex-col space-y-6">
    <div class="flex items-center justify-between">
        <Heading
            variant="small"
            title="Teams"
            description="Manage your teams and team memberships"
        />

        <CreateTeamModal>
            {#snippet children(props)}
                <Button
                    onclick={props.onClick}
                    data-test="teams-new-team-button"
                >
                    <Plus class="h-4 w-4" /> New team
                </Button>
            {/snippet}
        </CreateTeamModal>
    </div>

    <div class="space-y-3">
        {#each teams as team (team.id)}
            <div
                class="flex items-center justify-between rounded-lg border p-4"
                data-test="team-row"
            >
                <div class="flex items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{team.name}</span>

                            {#if team.isPersonal}
                                <Badge variant="secondary">Personal</Badge>
                            {/if}
                        </div>

                        <span class="text-sm text-muted-foreground"
                            >{team.roleLabel}</span
                        >
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {#if team.role === 'member'}
                        <Tip text="View team">
                            <Button
                                variant="ghost"
                                size="sm"
                                href={edit(team.slug)}
                                data-test="team-view-button"
                            >
                                <Eye class="h-4 w-4" />
                            </Button>
                        </Tip>
                    {:else}
                        <Tip text="Edit team">
                            <Button
                                variant="ghost"
                                size="sm"
                                href={edit(team.slug)}
                                data-test="team-edit-button"
                            >
                                <Pencil class="h-4 w-4" />
                            </Button>
                        </Tip>
                    {/if}
                </div>
            </div>
        {/each}

        {#if teams.length === 0}
            <p class="py-8 text-center text-muted-foreground">
                You don't belong to any teams yet.
            </p>
        {/if}
    </div>
</div>
