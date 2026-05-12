<script lang="ts">
    import { twMerge } from 'tailwind-merge';
    import TableActions from './Action.svelte';
    import TableCell from './Cell.svelte';
    import TableHeader from './Header.svelte';
    import Pagination from './Pagination.svelte';
    import { query } from '$lib/stores/global/query.svelte';
    // import type { Paginated } from '$types/pagination';
    import { Root, Body, Row } from '$components/ui/table';
    import { Checkbox } from '$components/ui/checkbox';
    import type { SelectableDataItem } from '$types/core/dataDisplay';
    import { setContext } from 'svelte';

    let {
        data = undefined,
        paginated = false,
        paginatedData = undefined,
        headers = [],
        onRowClick = undefined,
        onDelete = undefined,
        model = 'user',
        className = '',
        actions = undefined,
        searchStrings = $bindable([]),
        detached = false,
        selectable = false,
    }: Props = $props();

    let rows: SelectableDataItem<any>[] = $state([]);
    const context = $state({
        selection: [] as any[],
    });
    setContext('context', context);

    $effect(() => {
        rows = data?.map((item) => ({ ...item, $selected: item.$selected ?? false })) || [];
    });

    if (!searchStrings?.length && query.param('search')) {
        searchStrings = [query.param('search')?.toString() || ''];
    }

    let pageIndex = $derived(query.param('page') || 1);

    let hasActions = $derived(Boolean(onDelete || actions?.length));

    export function getContext() {
        return context;
    }

    export function selectAll(selected: boolean) {
        if (rows) {
            rows.forEach((row: SelectableDataItem<any>) => (row.$selected = selected));
            context.selection = selected ? rows.map((row: SelectableDataItem<any>) => row.id) : [];
        }
    }
</script>

{#if rows?.length === 0}
    <div class="text-center p-4">No data available</div>
{:else}
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <Root class="table table-zebra table-sm w-full {className}">
            <TableHeader {headers} {hasActions} {selectable} selectionHandler={selectAll} />
            <Body>
                {#each rows as row, index (row?.id)}
                    <Row
                        class={twMerge(
                            'hover:bg-base-300 transition-colors duration-200',
                            onRowClick && 'cursor-pointer',
                        )}>
                        {#if selectable}
                            <TableCell>
                                <Checkbox bind:checked={rows[index].$selected} />
                            </TableCell>
                        {/if}

                        {#each headers as header}
                            <TableCell {row} {header} {searchStrings} {onRowClick} />
                        {/each}

                        {#if actions?.length}
                            <TableActions {actions} {row} />
                        {/if}
                    </Row>
                {/each}
            </Body>
        </Root>
    </div>

    <!-- {#if paginated && data}
        <Pagination paginatedData={data as Paginated<any>} pageIndex={Number(pageIndex)} />
    {/if} -->
{/if}
