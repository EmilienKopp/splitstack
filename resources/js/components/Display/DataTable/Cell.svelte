<script lang="ts">
    import { self } from 'svelte/legacy';

    import { Highlighter } from '$lib/core/support/highlight';
    import { dot } from '$lib/core/support/objects';
    import type { DataHeader } from '$types/common/dataDisplay';
    import { Cell } from '$components/ui/table';
    import type { Snippet } from 'svelte';

    interface Props {
        row?: any;
        header?: DataHeader<any>;
        searchStrings?: string[];
        onRowClick?: ((row: any) => void) | undefined;
        children?: Snippet;
    }

    let {
        row,
        header,
        searchStrings = [],
        onRowClick = undefined,
        children = undefined,
    }: Props = $props();

    let value = $derived(header ? dot(row, header?.key) : undefined);

    function handleClick() {
        if (onRowClick) {
            onRowClick(row);
        }
    }
</script>

<Cell class="whitespace-nowrap max-w-72 truncate" onclick={self(handleClick)}>
    <div class="flex gap-1 items-center">
        {#if header?.icon}
            {@const SvelteComponent = header.icon(row)}
            <span title={value} class={header.iconClass?.(row)}>
                <SvelteComponent class="w-5 h-5" />
            </span>
        {/if}
        {#if !header?.iconOnly}
            {#if header?.combined}
                {header.combined(row)}
            {:else if value === null || value === undefined}
                {#if children}
                    {@render children()}
                {:else}
                    <span class="text-sm text-muted-foreground">-</span>
                {/if}
            {:else}
                {@const formatted = header?.formatter ? header.formatter(value) : value}
                {#if searchStrings?.length && typeof formatted === 'string' && header?.searchable}
                    <p>
                        {@html Highlighter.highlightMany(
                            formatted,
                            searchStrings,
                            ['bg-yellow-100', 'bg-blue-100'],
                            'exact',
                        )}
                    </p>
                {:else}
                    {formatted}
                {/if}
            {/if}
        {/if}
    </div>
</Cell>
