<script lang="ts">
  import { Link } from '@inertiajs/svelte';
  import clsx from 'clsx';
  import { twMerge } from 'tailwind-merge';

  interface Props {
    variant?: string;
    children?: import('svelte').Snippet;
    href?: string;
    onclick?: (e: MouseEvent) => void;
    loading?: boolean;
    prefetch?: boolean;
    [key: string]: any;
  }

  let {
    variant = 'primary',
    children,
    onclick,
    href,
    loading,
    type = 'button',
    prefetch,
    ...rest
  }: Props = $props();
</script>

{#if href}
  <Link
    {...rest}
    {href}
    {prefetch}
    class={twMerge(
      'btn',
      clsx({
        'btn-error': variant === 'danger',
        'btn-primary': variant === 'primary',
        'btn-secondary': variant === 'secondary',
        'btn-accent': variant === 'accent',
        'btn-outline': variant === 'outline-solid',
        'btn-link': variant === 'link',
      }),
      rest.class
    )}
  >
    {#if loading}
      <span class="loading loading-spinner"></span>
    {/if}
    {@render children?.()}
  </Link>
{:else}
  <button
    disabled={loading}
    {...rest}
    {type}
    {onclick}
    class={twMerge(
      'btn',
      clsx({
        'btn-error': variant === 'danger',
        'btn-primary': variant === 'primary',
        'btn-secondary': variant === 'secondary',
        'btn-accent': variant === 'accent',
        'btn-outline': variant === 'outline-solid',
        'btn-link': variant === 'link',
      }),
      rest.class
    )}
  >
    {#if loading}
      <span class="loading loading-spinner"></span>
    {/if}
    {@render children?.()}
  </button>
{/if}
