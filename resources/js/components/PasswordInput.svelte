<script lang="ts">
  import Eye from 'lucide-svelte/icons/eye';
  import EyeOff from 'lucide-svelte/icons/eye-off';
  import { twMerge } from 'tailwind-merge';

  let { class: className = '', ...rest } = $props();

  let showPassword = $state(false);
</script>

<div class="relative">
  <input
    type={showPassword ? 'text' : 'password'}
    class={twMerge('du-input du-input-bordered w-full pr-10', className)}
    {...rest}
  />
  <button
    type="button"
    onclick={() => (showPassword = !showPassword)}
    class="text-muted-foreground hover:text-foreground absolute inset-y-0 right-0 flex items-center rounded-r-md px-3 focus-visible:outline-none"
    aria-label={showPassword ? 'Hide password' : 'Show password'}
    tabindex={-1}
  >
    {#if showPassword}
      <EyeOff class="size-4" />
    {:else}
      <Eye class="size-4" />
    {/if}
  </button>
</div>
