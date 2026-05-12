<script lang="ts">
  import { twMerge } from 'tailwind-merge';

  interface Props {
    htmlFor?: string;
    required?: boolean;
    [key: string]: any;
  }

  let { children, required, ...rest }: Props = $props();
</script>

<legend class={twMerge('fieldset-legend', rest.class)} {...rest}>
  {@render children?.()}
  {#if required}
    <span class="text-error">*</span>
  {/if}
</legend>
