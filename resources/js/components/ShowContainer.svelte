<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';
    import type { DataAction } from '@/types/core/dataDisplay';
    import { Link } from '@inertiajs/svelte';
    import ArrowLeft from 'lucide-svelte/icons/arrow-left';
    import Tip from '@/components/Tip.svelte';

    let {
        children,
        class: className = '',
        title,
        actions = [],
        record,
        noActions = false,
    }: {
        children?: Snippet;
        class?: string;
        title: string;
        actions?: DataAction<any>[];
        record?: any;
        noActions?: boolean;
    } = $props();

    const visibleActions = $derived(
        actions.filter((a) => !a.hidden?.(record) && !a.listViewOnly && !noActions),
    );
    $inspect(visibleActions, 'visibleActions');
</script>

<div class={cn('py-10 px-1', className)}>
    <div class="mb-10">
        <button
            onclick={() => window.history.back()}
            class="flex items-center gap-2 text-sm text-black/40 hover:text-black transition-colors mb-6">
            <ArrowLeft class="w-4 h-4" strokeWidth={1.5} />
            Back
        </button>

        <div class="flex items-end justify-between gap-4">
            <h1 class="text-3xl font-light tracking-tight text-black">{title}</h1>

            {#if visibleActions.length > 0}
                <div class="flex items-center gap-2">
                    {#each visibleActions as action}
                        {#if action.href}
                            <Link
                                href={action.href(record) ?? '#'}
                                class="du-btn du-btn-sm {action.css?.(record) ?? 'du-btn-outline'}">
                                {#if action.icon}
                                    {@const Icon = action.icon(record)}
                                    <Tip text={action.label}>
                                        <Icon class="w-4 h-4" />
                                    </Tip>
                                {/if}
                                {#if !action.iconOnly}
                                    {action.label}
                                {/if}
                            </Link>
                        {:else if action.callback}
                            <button
                                onclick={() => action.callback!(record)}
                                disabled={action.disabled?.(record)}
                                class="du-btn du-btn-sm {action.css?.(record) ?? 'du-btn-outline'}">
                                {#if action.icon}
                                    {@const Icon = action.icon(record)}
                                    <Tip text={action.label}>
                                        <Icon class="w-4 h-4" />
                                    </Tip>
                                {/if}
                                {#if !action.iconOnly}
                                    {action.label}
                                {/if}
                            </button>
                        {/if}
                    {/each}
                </div>
            {/if}
        </div>
    </div>

    {@render children?.()}
</div>
