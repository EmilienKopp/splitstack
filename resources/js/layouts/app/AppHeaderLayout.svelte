<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppContent from '@/components/AppContent.svelte';
    import AppHeader from '@/components/AppHeader.svelte';
    import AppShell from '@/components/AppShell.svelte';
    import Toaster from '@/components/Toaster.svelte';
    import type { BreadcrumbItem } from '@/types';
    import { translucid } from 'translucid-svelte';
    import { page } from '@inertiajs/svelte';

    let {
        breadcrumbs = [],
        children,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
    } = $props();

    translucid.configure(() => page.props.space as string);
</script>

<AppShell variant="header">
    <AppHeader {breadcrumbs} />
    <AppContent variant="header">
        {@render children?.()}
    </AppContent>
    <Toaster />
</AppShell>
