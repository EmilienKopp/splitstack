<script lang="ts">
    import { getContext } from 'svelte';
    import { twMerge } from 'tailwind-merge';
    interface Props {
        title?: string;
        description?: string;
        children?: import('svelte').Snippet;
        [key: string]: any;
    }

    let { title = '', description = '', children, ...rest }: Props = $props();

    const detached = getContext<boolean>('detached') || false;
</script>

<fieldset
    {...rest}
    class={twMerge('p-6 bg-base-100 border border-base-600 rounded-lg', rest.class)}
    class:border-none={detached}
    class:p-1={detached}>
    <legend class="md:text-lg font-semibold text-primary">
        {title}
    </legend>
    {#if description}
        <p class="mt-1 text-xs sm:text-sm">{description}</p>
    {/if}
    <div class="mt-4">
        {@render children?.()}
    </div>
</fieldset>
