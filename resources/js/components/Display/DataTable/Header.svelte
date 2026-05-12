<script lang="ts">
    import { Row, Head, Header } from '$components/ui/table';
    import Checkbox from '@/components/ui/checkbox/Checkbox.svelte';
    import type { DataHeader } from '@/types/core/dataDisplay';
    import { getContext } from 'svelte';

    interface Props {
        headers: DataHeader<any>[];
        hasActions?: boolean;
        selectable?: boolean;
        selectionHandler?: (selected: boolean) => void;
    }

    let {
        headers,
        hasActions = false,
        selectable = false,
        selectionHandler = () => {},
    }: Props = $props();
</script>

<Header>
    <Row>
        {#if selectable}
            <th class="w-8">
                <span class="sr-only">Select</span>
                <Checkbox
                    onchange={(e) => selectionHandler((e.target as HTMLInputElement).checked)} />
            </th>
        {/if}
        {#each headers as header}
            <Head class="uppercase font-bold">
                {header.label}
            </Head>
        {/each}
        {#if hasActions}
            <Head class="uppercase font-bold text-center">Actions</Head>
        {/if}
    </Row>
</Header>
