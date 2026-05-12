<script lang="ts">
    import { themeState } from '@/lib/theme.svelte';
    import { Toaster as SonnerPrimitive, toast, type ToasterProps } from 'svelte-sonner';
    import { router } from '@inertiajs/svelte';
    import { onMount } from 'svelte';

    let { ...restProps }: ToasterProps = $props();

    const { appearance } = themeState();
    
    onMount(() => {
        const cleanup = router.on('navigate', ({detail: {page: {props}}}) => {
            if(Object.keys(props.errors || {}).length > 0) {
                Object.values(props.errors).forEach((error) => {
                    toast.error(error as string, { duration: 5000 });
                });
            }
        });

        return () => {
            cleanup();
        };
    })
</script>

<SonnerPrimitive
    theme={appearance.value}
    class="toaster group"
    position="bottom-right"
    style="--normal-bg: var(--popover); --normal-text: var(--popover-foreground); --normal-border: var(--border);"
    {...restProps}
/>
