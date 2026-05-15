<script lang="ts">
    import { cn } from '@/lib/utils';

    let {
        class: className = '',
    }: {
        class?: string;
    } = $props();
</script>

<div data-slot="skeleton" class={cn('du-skeleton', className)}></div>
