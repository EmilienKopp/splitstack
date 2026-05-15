<script lang="ts">
  import { twMerge } from 'tailwind-merge';

  interface Props {
    for?: string;
    htmlFor?: string;
    required?: boolean;
    children?: import('svelte').Snippet;
    class?: string;
    [key: string]: any;
  }

  let { for: for_, htmlFor, required = false, children, class: className = '', ...rest }: Props = $props();
</script>

<label for={htmlFor ?? for_} class={twMerge('du-label', className)} {...rest}>
  {@render children?.()}
  {#if required}
    <span class="text-error ml-0.5">*</span>
  {/if}
</label>
