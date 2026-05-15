<script lang="ts">
    import { cn } from '@/lib/utils';

    let {
        class: className = '',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<span class={cn('du-loading du-loading-spinner', className)} {...rest}></span>
