<script lang="ts">
    import * as Drawer from '$components/ui/drawer/index.js';
    import type { Snippet } from 'svelte';

    let { children, trigger }: { children?: Snippet; trigger?: Snippet } = $props();
</script>

<Drawer.Root>
    <Drawer.Trigger>
        {@render trigger?.()}
    </Drawer.Trigger>
    <Drawer.Content>
        <Drawer.Header>
            <Drawer.Title>Are you sure absolutely sure?</Drawer.Title>
            <Drawer.Description>This action cannot be undone.</Drawer.Description>
        </Drawer.Header>
        <Drawer.Footer>
            {@render children?.()}
            <Drawer.Close>Cancel</Drawer.Close>
        </Drawer.Footer>
    </Drawer.Content>
</Drawer.Root>
