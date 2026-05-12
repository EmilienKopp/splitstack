<script lang="ts">
    import {
        Tooltip,
        TooltipContent,
        TooltipProvider,
        TooltipTrigger,
    } from '@/components/ui/tooltip';
    import type { Snippet } from 'svelte';

    interface Props {
        text?: string;
        children?: Snippet;
        content?: Snippet;
    }

    let { text, content, children }: Props = $props();
</script>

<TooltipProvider delayDuration={0}>
    <Tooltip>
        <TooltipTrigger>
            {@render children?.()}
        </TooltipTrigger>
        <TooltipContent>
            {#if content}
                {@render content()}
            {:else if text}
                {text}
            {/if}
        </TooltipContent>
    </Tooltip>
</TooltipProvider>
