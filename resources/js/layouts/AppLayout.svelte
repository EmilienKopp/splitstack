<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppLayout from '@/layouts/app/AppSidebarLayout.svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
        ...restProps
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
        [key: string]: any;
    } = $props();
    $inspect(restProps);
</script>

<AppLayout {breadcrumbs}>
    {@render children?.()}
</AppLayout>
