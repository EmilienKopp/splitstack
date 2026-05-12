<script lang="ts">
    import type { DataAction } from '$types/common/dataDisplay';
    import { Link } from '@inertiajs/svelte';
    import { twMerge } from 'tailwind-merge';
    import { Cell } from '$components/ui/table';

    interface Props {
        actions: DataAction<any>[];
        row: any;
    }

    let { actions, row }: Props = $props();
</script>

{#snippet buttonContent(action: DataAction<any>, row: any)}
    {#if action.icon}
        {@const SvelteComponent = action.icon(row)}
        <SvelteComponent class="w-4 h-4" />
    {/if}
    {action.label}
{/snippet}

<Cell class="text-center flex justify-center gap-2">
    {#each actions.toSorted((a, b) => (a.position ?? 0) - (b.position ?? 0)) as action}
        {#if !action.hidden?.(row)}
            {#if action?.callback}
                <button
                    type="button"
                    onclick={() => action.callback?.(row)}
                    class={twMerge('btn btn-action btn-xs', action.css?.(row))}>
                    {@render buttonContent(action, row)}
                </button>
            {:else if action?.href}
                <Link
                    href={action.href(row)}
                    class={twMerge(
                        'mx-1 px-1 hover:underline flex items-center gap-1',
                        action.css?.(row),
                    )}>
                    {@render buttonContent(action, row)}
                </Link>
            {/if}
        {/if}
    {/each}
</Cell>
