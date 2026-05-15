<script lang="ts">
  import { twMerge } from 'tailwind-merge';
  import clsx from 'clsx';

  interface Props {
    variant?: 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning';
    class?: string;
    children?: import('svelte').Snippet;
    [key: string]: any;
  }

  let { variant = 'default', class: className = '', children, ...rest }: Props = $props();

  const variantClass = clsx({
    'du-badge-primary': variant === 'default',
    'du-badge-neutral': variant === 'secondary',
    'du-badge-error': variant === 'destructive',
    'du-badge-outline': variant === 'outline',
    'du-badge-success': variant === 'success',
    'du-badge-warning': variant === 'warning',
  });
</script>

<span class={twMerge('du-badge', variantClass, className)} {...rest}>
  {@render children?.()}
</span>
