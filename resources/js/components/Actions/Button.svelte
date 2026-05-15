<script lang="ts">
  import { Link } from '@inertiajs/svelte';
  import clsx from 'clsx';
  import { twMerge } from 'tailwind-merge';

  interface Props {
    variant?: string;
    size?: 'default' | 'sm' | 'lg' | 'icon' | 'xs';
    children?: import('svelte').Snippet;
    href?: string;
    onclick?: (e: MouseEvent) => void;
    loading?: boolean;
    prefetch?: boolean;
    [key: string]: any;
  }

  let {
    variant = 'primary',
    size = 'default',
    children,
    onclick,
    href,
    loading,
    type = 'button',
    prefetch,
    ...rest
  }: Props = $props();

  const variantClass = $derived(clsx({
    'du-btn-primary': variant === 'primary' || variant === 'default',
    'du-btn-secondary': variant === 'secondary',
    'du-btn-accent': variant === 'accent',
    'du-btn-error': variant === 'danger' || variant === 'destructive',
    'du-btn-ghost': variant === 'ghost',
    'du-btn-outline': variant === 'outline' || variant === 'outline-solid',
    'du-btn-link': variant === 'link',
  }));

  const sizeClass = $derived(clsx({
    'du-btn-sm': size === 'sm',
    'du-btn-lg': size === 'lg',
    'du-btn-xs': size === 'xs',
    'du-btn-square': size === 'icon',
  }));
</script>

{#if href}
  <Link
    {...rest}
    {href}
    {prefetch}
    class={twMerge('du-btn', variantClass, sizeClass, rest.class)}
  >
    {#if loading}
      <span class="du-loading du-loading-spinner"></span>
    {/if}
    {@render children?.()}
  </Link>
{:else}
  <button
    disabled={loading}
    {...rest}
    {type}
    {onclick}
    class={twMerge('du-btn', variantClass, sizeClass, rest.class)}
  >
    {#if loading}
      <span class="du-loading du-loading-spinner"></span>
    {/if}
    {@render children?.()}
  </button>
{/if}
