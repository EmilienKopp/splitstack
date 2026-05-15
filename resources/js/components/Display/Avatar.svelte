<script lang="ts">
  import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
  import { twMerge } from 'tailwind-merge';

  interface Props {
    src?: string;
    fallback?: string;
    alt?: string;
    class?: string;
    fallbackClass?: string;
  }

  let { src, fallback = '?', alt = '', class: className = '', fallbackClass = '' }: Props = $props();
</script>

<Avatar class={twMerge('size-8', className)}>
  {#if src}
    <AvatarImage {src} {alt} />
  {/if}
  <AvatarFallback class={fallbackClass || undefined}>{fallback}</AvatarFallback>
</Avatar>
